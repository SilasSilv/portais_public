<?php 

use \api\v1\resource\pointOccurrence\rules\PointOccurrenceDAO;
use \api\v1\resource\pointOccurrence\rules\PointOccurrence;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\people\rules\PeopleDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$getPointOccurrence = function ($req, $res, $args, $session = []) {	
	$pointOccurrenceDAO = new PointOccurrenceDAO();	

	$queryParams = $req->getQueryParams();
	$params = [];	

	$fields = $queryParams["fields"] ?? '';
	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? 'DESC';
	$numberLine = $queryParams['number_line'] ?? 20;
	$skipLine = $queryParams['skip_line'] ?? 0;

	if (isset($args["nr_sequencia"])) {
		$params["nr_sequencia"] = $args["nr_sequencia"];
	}

	if (isset($queryParams["nr_cracha"])) {
		$params["nr_cracha"] = $queryParams["nr_cracha"];
	}

	if (isset($queryParams["nm_pessoa_fisica"])) {
		$params["nm_pessoa_fisica"] = $queryParams["nm_pessoa_fisica"];
	}

	if (isset($queryParams["cd_tipo_ocorrencia"])) {
		$params["cd_tipo_ocorrencia"] = $queryParams["cd_tipo_ocorrencia"];
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

	if (isset($queryParams["ie_lido_rh"])) {
		$params["ie_lido_rh"] = $queryParams["ie_lido_rh"];
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

	$occurrences = $pointOccurrenceDAO->findOccurrenceAll($params, 
							   						  	  ClearData::stringSQL($fields),
													  	  ClearData::stringSQL($sortField),
													  	  ClearData::stringSQL($sortType),
													  	  $numberLine,
													  	  $skipLine,
													  	  $logicalOp);

	return $res->withJson($occurrences);
};

$app->get("/v1/pointOccurrence[/{nr_sequencia}]", function($req, $res, $args) use ($getPointOccurrence){

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(2, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}	

	return $getPointOccurrence($req, $res, $args, $session);

});

$app->get("/v1/pointOccurrence/free/[{nr_sequencia}]", function($req, $res, $args) use ($getPointOccurrence) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(3, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(18, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}	
	
	return $getPointOccurrence($req, $res, $args);

});

$app->post("/v1/pointOccurrence", function($req, $res) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(2, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$pointOccurrence = new PointOccurrence();
	$pointOccurrenceDAO = new PointOccurrenceDAO();

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_inclusao'] = $session['nr_cracha'];
	
	$pointOccurrence->setPointOccurrenceValidate($occurrenceData);
	$dt_criacao = new \DateTime();
	$pointOccurrence->setDtCriacao($dt_criacao);

	if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
		Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
	} 

	$nr_sequencia = $pointOccurrenceDAO->register($pointOccurrence->getParsedArray());

	return $res->withJson(["nr_sequencia" => $nr_sequencia]);
});

$app->put("/v1/pointOccurrence/{nr_sequencia}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(2, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$pointOccurrence = new PointOccurrence();
	$pointOccurrenceDAO = new PointOccurrenceDAO();

	if ($pointOccurrenceDAO->checkSeemHr($args['nr_sequencia'])) {	
		return Error::generateErrorCustom('Occurrence of point can not be updated, has opinion of HR', 406);
	}

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_inclusao'] = $session['nr_cracha'];

	$pointOccurrence->setPointOccurrenceValidate($occurrenceData);

	if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
		Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
	} 

	$wasChanged = $pointOccurrenceDAO->updateOccurrence(
			$args['nr_sequencia'], 
			$pointOccurrence->getParsedArray()
		);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);
	
});

$app->delete("/v1/pointOccurrence/{nr_sequencia}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(2, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$pointOccurrenceDAO = new PointOccurrenceDAO();	

	$occurrenceData = $pointOccurrenceDAO->findOccurrence($args['nr_sequencia'], 'nr_cracha');

	if (count($occurrenceData) > 0) {
		$occurrenceData = $occurrenceData[0];

		if ($peopleDAO->checkSectorOrGroup($occurrenceData['nr_cracha'], $session['cd_setor'], $session['cd_grupo'])) {
			Error::generateErrorCustom('Person of the event is not part of the sector and group that you belong to, it may also be that it is inactive.', 406);
		}
	}	

	if ($pointOccurrenceDAO->checkSeemHr($args['nr_sequencia'])) {
		return Error::generateErrorCustom('Occurrence of point can not be deleted, has opinion of HR', 406);
	}

	$wasExcluded = $pointOccurrenceDAO->deleteOccurrence($args['nr_sequencia']);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);

});

$app->put("/v1/pointOccurrence/SeemHR/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(3, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$pointOccurrence = new PointOccurrence();
	$pointOccurrenceDAO = new PointOccurrenceDAO();	

	if ($pointOccurrenceDAO->checkSeemHr($args['nr_sequencia'], 'parecerRh')) {	
		return Error::generateErrorCustom('Occurrence of point can not be updated, has opinion of HR', 406);
	}

	$occurrenceData = $req->getParsedBody();
	$occurrenceData['nr_cracha_rh'] = $session['nr_cracha'];

	$pointOccurrence->setPointOccurrenceSeemHRValidate($occurrenceData);

	$wasChanged = $pointOccurrenceDAO->updateOccurrence(
			$args['nr_sequencia'], 
			$pointOccurrence->getParsedArray()
		);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});