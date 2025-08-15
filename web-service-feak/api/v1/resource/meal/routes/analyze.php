<?php

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\analyze\AnalyzeDAO;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$app->get("/v1/meal/analyze/private/{dt_inicio:\d{2}\/\d{2}\/\d{4}}/{dt_fim:\d{2}\/\d{2}\/\d{4}}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$analyzeDAO = new AnalyzeDAO();

	$queryParams = $req->getQueryParams();

	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? 'ASC';
	$numberLine = $queryParams['number_line'] ?? 20;
	$skipLine = $queryParams['skip_line'] ?? 0;

	$params["nr_cracha"] = $session["nr_cracha"];
	$params["dates"] = [
		"dt_inicio" => DateUtils::convert($args["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
		"dt_fim" => DateUtils::convert($args["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
	];

	if (isset($queryParams["ie_tipo_refeicao"])) {
		$params["ie_tipo_refeicao"] = $queryParams["ie_tipo_refeicao"];
	}

	$requests = $analyzeDAO->findPrivate($params, $sortField, $sortType, $numberLine, $skipLine);

	return $res->withJson($requests);
});

$app->get("/v1/meal/analyze/public/synthetic/{dt_inicio:\d{2}\/\d{2}\/\d{4}}/{dt_fim:\d{2}\/\d{2}\/\d{4}}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$analyzeDAO = new AnalyzeDAO();	

	$queryParams = $req->getQueryParams();

	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? 'ASC';
	$numberLine = $queryParams['number_line'] ?? 20;
	$skipLine = $queryParams['skip_line'] ?? 0;

	$params["dates"] = [
		"dt_inicio" => DateUtils::convert($args["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
		"dt_fim" => DateUtils::convert($args["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
	];

	if (isset($queryParams["ie_tipo_refeicao"])) {
		$params["ie_tipo_refeicao"] = $queryParams["ie_tipo_refeicao"];
	}	

	$meals = $analyzeDAO->findPublicSynthetic($params, $sortField, $sortType, $numberLine, $skipLine);

	return $res->withJson($meals);
});


$app->get("/v1/meal/analyze/public/analytical/{nr_refeicao}", function($req, $res, $args) {
	
		$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
		
		if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
			Error::generateErrorApi(Error::NOT_AUTHORIZED);
		}

		$analyzeDAO = new AnalyzeDAO();

		$mealData = $analyzeDAO->findPublicAnalytical($args["nr_refeicao"]);

		return $res->withJson($mealData);
});

$app->get("/v1/meal/analyze/public/analytical/{dt_inicio:\d{2}\/\d{2}\/\d{4}}/{dt_fim:\d{2}\/\d{2}\/\d{4}}", function($req, $res, $args) {

	if (count($req->getHeader('Authorization')) > 0) {
		if ($req->getHeader('Authorization')[0] != 'a9736142-6824-42bd-96b8-64e738d663f6') {
			$this->get('checkAuthenticated')($req->getHeader('Authorization'));
		}
	} else {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$dt_inicio = DateUtils::convert($args["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']);
	$dt_fim = DateUtils::convert($args["dt_fim"], "BR-DATE", ['format' => 'Y-m-d']);
	
	$analyzeDAO = new AnalyzeDAO();
	$mealData = $analyzeDAO->findPublicAnalyticalDate($dt_inicio, $dt_fim);

	return $res->withJson($mealData);
});

$app->get("/v1/meal/analyze/payroll/{dt_inicio:\d{2}\/\d{2}\/\d{4}}/{dt_fim:\d{2}\/\d{2}\/\d{4}}", function($req, $res, $args) {
	
		$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
		
		if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor']) ||
			!Auth::checkPermission(15, $session['nr_cracha'], $session['cd_setor'])) {
			Error::generateErrorApi(Error::NOT_AUTHORIZED);
		}

		$analyzeDAO = new AnalyzeDAO();
	
		$queryParams = $req->getQueryParams();

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? 'ASC';
		$numberLine = $queryParams['number_line'] ?? 'ALL';
		$skipLine = $queryParams['skip_line'] ?? 0;

		$params["dates"] = [
			"dt_inicio" => DateUtils::convert($args["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
			"dt_fim" => DateUtils::convert($args["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
		];

		if (isset($queryParams["cracha_ou_nome"])) {
			if (preg_match('/\d+/', $queryParams["cracha_ou_nome"])) {
				$params["cracha_ou_nome"] = ["nr_cracha" => $queryParams["cracha_ou_nome"], "nm_pessoa_fisica" => "-1"];
			} else {
				$params["cracha_ou_nome"] = ["nr_cracha" => 0, "nm_pessoa_fisica" => $queryParams["cracha_ou_nome"]];	
			}
		}	
	
		if (isset($queryParams["ie_tipo_refeicao"])) {
			$params["ie_tipo_refeicao"] = $queryParams["ie_tipo_refeicao"];
		}

		$payroll = $analyzeDAO->findPayroll($params, $sortField, $sortType, $numberLine, $skipLine);

		return $res->withJson($payroll);
	});