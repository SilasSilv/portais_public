<?php 

use \api\v1\resource\accidentOccurrence\rules\AccidentOccurrenceDAO;
use \api\v1\resource\accidentOccurrence\rules\AccidentOccurrence;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\people\rules\PeopleDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$getAccidentOccurrence = function ($req, $res, $args, $session = []) {	

	$accidentOccurrenceDAO = new AccidentOccurrenceDAO();	

	$queryParams = $req->getQueryParams();
	$params = [];

	$fields = $queryParams["fields"] ?? '';
	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? 'DESC';
	$numberLine = $queryParams['number_line'] ?? 20;
	$skipLine = $queryParams['skip_line'] ?? 0;
	
	if (isset($args['nr_sequencia'])) {
		$params["nr_sequencia"] = $args["nr_sequencia"];
	} 
		
	if (isset($queryParams["nr_cracha"])) {
		$params["nr_cracha"] = $queryParams["nr_cracha"];
	}

	if (isset($queryParams["nm_pessoa_fisica"])) {
		$params["nm_pessoa_fisica"] = $queryParams["nm_pessoa_fisica"];
	}

	if (count($session) > 0) {

		$params["cd_setor"] =  $session["cd_setor"];

	} else if (isset($queryParams["cd_setor"])) {

		$params["cd_setor"] = $queryParams["cd_setor"];

	} 

	if (count($session) > 0) {

		$params["cd_grupo"] =  $session["cd_grupo"];

	} else if (isset($queryParams["cd_grupo"])) {

		$params["cd_grupo"] = $queryParams["cd_grupo"];
		
	} 

	if (isset($queryParams["ie_lido_cipa"])) {
		$params["ie_lido_cipa"] = $queryParams["ie_lido_cipa"];
	}

	if (isset($queryParams['consist_true']) && $queryParams['consist_true'] == "ONE") {
		$logicalOp = "OR";
	} else {
		$logicalOp = "AND";
	}

	if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
		$params["dates"] = array(
				"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
				"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
			);
	}

	$params["type_search"] = count($session) == 0 ? "free" : "default";

	$occurrences = $accidentOccurrenceDAO->findOccurrenceAll($params, 
								   						  	 ClearData::stringSQL($fields),
														  	 ClearData::stringSQL($sortField),
														  	 ClearData::stringSQL($sortType),
														  	 $numberLine,
														  	 $skipLine,
														  	 $logicalOp);

	return $res->withJson($occurrences);
};

$app->get("/v1/accidentOccurrence[/{nr_sequencia}]", function($req, $res, $args) use ($getAccidentOccurrence) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(4, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	return $getAccidentOccurrence($req, $res, $args, $session);
});

$app->get("/v1/accidentOccurrence/free/[{nr_sequencia}]", function($req, $res, $args) use ($getAccidentOccurrence) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(5, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(19, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	return $getAccidentOccurrence($req, $res, $args);
});


$app->post("/v1/accidentOccurrence", function($req, $res) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(4, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$accidentOccurrence = new AccidentOccurrence();
	$accidentOccurrenceDAO = new AccidentOccurrenceDAO();

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_inclusao'] = $session['nr_cracha'];
	$accidentOccurrence->setAccidentOccurrenceValidate($occurrenceData);

	if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
		Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
	} 

	$nr_sequencia = $accidentOccurrenceDAO->register($accidentOccurrence->getParsedArray());

	return $res->withJson(["nr_sequencia" => $nr_sequencia]);
});

$app->put("/v1/accidentOccurrence/{nr_sequencia}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(4, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$accidentOccurrence = new AccidentOccurrence();
	$accidentOccurrenceDAO = new AccidentOccurrenceDAO();

	if ($accidentOccurrenceDAO->checkSeemCipa($args['nr_sequencia'])) {
		Error::generateErrorCustom('Occurrence of accident can not be updated, has opinion of SESMT', 406);
	}

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_inclusao'] = $session['nr_cracha'];

	$accidentOccurrence->setAccidentOccurrenceValidate($occurrenceData);

	if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
		Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
	}

	$wasChanged = $accidentOccurrenceDAO->updateOccurrence(
			$args['nr_sequencia'], 
			$accidentOccurrence->getParsedArray()
		);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/accidentOccurrence/{nr_sequencia}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(4, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$accidentOccurrenceDAO = new AccidentOccurrenceDAO();

	$occurrenceData = $accidentOccurrenceDAO->findOccurrence($args['nr_sequencia'], 'nr_cracha');

	if (count($occurrenceData) > 0) {
		$occurrenceData = $occurrenceData[0];

		if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
			Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
		}
	}	

	if ($accidentOccurrenceDAO->checkSeemCipa($args['nr_sequencia'])) {
		Error::generateErrorCustom('Occurrence of accident can not be updated, has opinion of SESMT', 406);
	}

	$wasExcluded = $accidentOccurrenceDAO->deleteOccurrence($args['nr_sequencia']);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);

});

$app->put("/v1/accidentOccurrence/SeemSESMT/{nr_sequencia}", function($req, $res, $args) {	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(5, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$accidentOccurrence = new AccidentOccurrence();
	$accidentOccurrenceDAO = new AccidentOccurrenceDAO();

	if ($accidentOccurrenceDAO->checkSeemCipa($args['nr_sequencia'], 'parecerSESMT')) {	
		Error::generateErrorCustom('Occurrence of accident can not be updated, has opinion of SESMT', 406);
	}

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_cipa'] = $session['nr_cracha'];

	$accidentOccurrence->setAccidentOccurrenceSeemCipaValidate($occurrenceData);

	$wasChanged = $accidentOccurrenceDAO->updateOccurrence(
			$args['nr_sequencia'], 
			$accidentOccurrence->getParsedArray()
		);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});