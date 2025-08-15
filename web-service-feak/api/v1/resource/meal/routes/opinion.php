<?php

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\opinion\Opinion;
use \api\v1\resource\meal\rules\opinion\OpinionDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$app->get("/v1/meal/opinion[/{nr_sequencia}]", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$opinionDAO = new OpinionDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';
	
	if (isset($args['nr_sequencia'])) {

		$opinion = $opinionDAO->findOpinion($args['nr_sequencia'], 
											$session["nr_cracha"],
										    ClearData::stringSQL($fields));

		return $res->withJson($opinion);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? '';
		$numberLine = $queryParams['number_line'] ?? 'ALL';

		$params = [];

		$params["nr_cracha_default"] = $session["nr_cracha"];
		$params["ie_lido"] = $queryParams['ie_lido'] ?? 'N';

		if (isset($queryParams["nr_cracha"])) {
			$params["nr_cracha"] = $queryParams["nr_cracha"];
		}

		if (isset($queryParams["cd_tipo_opiniao"])) {
			$params["cd_tipo_opiniao"] = $queryParams["cd_tipo_opiniao"];		
		}

		if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
			$params["dates"] = array(
					"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
					"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
				);
		}
		
		$opinion = $opinionDAO->findOpinionAll($params, 
											   ClearData::stringSQL($fields),
											   ClearData::stringSQL($sortField),
											   ClearData::stringSQL($sortType),
											   $numberLine);

		return $res->withJson($opinion);

	}

});

$app->post("/v1/meal/opinion", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$opinionDAO = new OpinionDAO();
	$opinion = new Opinion();

	$opinionData = $req->getParsedBody();
	$opinionData["nr_cracha"] = $session["nr_cracha"];

	$opinion->setOpinionValidate($opinionData, 'POST');
	$dt_inclusao = new \DateTime();
	$opinion->setDtInclusao($dt_inclusao);

	$nr_sequencia = $opinionDAO->register(
								$opinion->getParsedArray()
							);

	return $res->withJson([
							"nr_sequencia" => $nr_sequencia
						  ]);
});

$app->put("/v1/meal/opinion/{nr_sequencia}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$opinionDAO = new OpinionDAO();
	$opinion = new Opinion();

	$opinion->setOpinionValidate($req->getParsedBody(), 'PUT');

	$wasChanged = $opinionDAO->updateOpinion(
								$args['nr_sequencia'],
								$opinion->getParsedArray()
							);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);
});

$app->delete("/v1/meal/opinion/{nr_sequencia}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$opinionDAO = new OpinionDAO();

	$wasExcluded = $opinionDAO->deleteOpinion(
								$args['nr_sequencia']								
							);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);
});

$app->put("/v1/meal/opinion/read/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$opinionDAO = new OpinionDAO();

	$wasRead = $opinionDAO->readOpinion($session['nr_cracha'],
						  			 	$args['nr_sequencia']);

	return $res->withJson(["foi_lido" => $wasRead]);
});