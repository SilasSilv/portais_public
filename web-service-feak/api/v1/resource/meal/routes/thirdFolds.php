<?php

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\thirdFolds\ThirdFolds;
use \api\v1\resource\meal\rules\thirdFolds\ThirdFoldsDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$getThirdFolds = function ($req, $res, $args, $queryType = '', $session = []) {	
	$thirdFoldsDAO = new ThirdFoldsDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';

	if (isset($args['nr_sequencia'])) {

		$thirdFolds = $thirdFoldsDAO->findThirdFolds($args['nr_sequencia'],  ClearData::stringSQL($fields));
		return $res->withJson($thirdFolds);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? '';
		$skipLine = $queryParams['skip_line'] ?? 0;
		
		if ($queryType == 'default') {
			$numberLine = $queryParams['number_line'] ?? 'ALL';
		} else {
			$numberLine = $queryParams['number_line'] ?? 20;
		}

		$params = [];

		if (count($session) > 0) {
			$params['nr_cracha_resp'] = $session['nr_cracha'];
		}

		if (isset($queryParams["ie_terceiro_dobra"])) {
			$params["ie_terceiro_dobra"] = $queryParams["ie_terceiro_dobra"];
		}

		if (isset($queryParams["nr_cracha"])) {
			$params["nr_cracha"] = $queryParams["nr_cracha"];
		}

		if (isset($queryParams["nr_cartao"])) {
			$params["nr_cartao"] = $queryParams["nr_cartao"];
		}

		if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
			$params["dates"] = array(
					"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
					"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
				);
		}

		$thirdFolds = $thirdFoldsDAO->finThirdFoldsAll($params, 
													   ClearData::stringSQL($fields),
													   ClearData::stringSQL($sortField),
													   ClearData::stringSQL($sortType),
													   $numberLine,
													   $skipLine,
													   $queryType);

		return $res->withJson($thirdFolds);
	}
};

$app->get("/v1/meal/thirdFolds[/{nr_sequencia}]", function($req, $res, $args) use ($getThirdFolds) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(8, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	return $getThirdFolds($req, $res, $args, 'default', $session);
});

$app->get("/v1/meal/thirdFolds/free/[{nr_sequencia}]", function($req, $res, $args) use ($getThirdFolds) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	return $getThirdFolds($req, $res, $args);
});

$app->post("/v1/meal/thirdFolds", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(8, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$thirdFoldsDAO = new ThirdFoldsDAO();
	$thirdFolds = new ThirdFolds();

	$reqData = $req->getParsedBody();
	$reqData['nr_cracha_resp'] = $session['nr_cracha'];
	$thirdFolds->setThirdFoldsValidate($reqData);

	$nr_sequencia = $thirdFoldsDAO->register(
								$thirdFolds->getParsedArray()
							);

	return $res->withJson([
							"nr_sequencia" => $nr_sequencia
						  ]);
	
});

$app->put("/v1/meal/thirdFolds/{nr_sequencia}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(8, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$thirdFoldsDAO = new ThirdFoldsDAO();
	$thirdFolds = new ThirdFolds();

	$reqData = $req->getParsedBody();
	$reqData['nr_cracha_resp'] = $session['nr_cracha'];
	$thirdFolds->setThirdFoldsValidate($reqData);

	$wasChanged = $thirdFoldsDAO->updateThirdFolds(
								$args['nr_sequencia'],
								$thirdFolds->getParsedArray()
							);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/meal/thirdFolds/{nr_sequencia}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(8, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$thirdFoldsDAO = new ThirdFoldsDAO();

	$wasExcluded = $thirdFoldsDAO->deleteThirdFolds(
								$args['nr_sequencia']								
							);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);

});