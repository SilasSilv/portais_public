<?php 

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\quality\Quality;
use \api\v1\resource\meal\rules\quality\QualityDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$app->get("/v1/meal/quality[/{nr_sequencia}]", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$qualityDAO = new QualityDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';

	if (isset($args['nr_sequencia'])) {

		$quality = $qualityDAO->findQuality($args['nr_sequencia'], 
										    ClearData::stringSQL($fields));

		return $res->withJson($quality);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? 'DESC';
		$numberLine = $queryParams['number_line'] ?? 20;

		$params = [];

		if (isset($queryParams["nr_cracha"])) {
			$params["nr_cracha"] = $queryParams["nr_cracha"];
		}

		if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
			$params["dates"] = array(
					"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
					"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
				);
		}
		
		$quality = $qualityDAO->findQualityAll($params, 
											   ClearData::stringSQL($fields),
											   ClearData::stringSQL($sortField),
											   ClearData::stringSQL($sortType),
											   $numberLine);

		return $res->withJson($quality);

	}

});

$app->get("/v1/meal/quality/statistics/{dt_mes_referencia:\d{2}\/\d{4}}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$qualityDAO = new QualityDAO();

	$dt_mes_referencia = DateUtils::convert($args["dt_mes_referencia"], "BR-MONTH-YEAR", ['format' => 'Y-m']);

	$statisticsQuality = $qualityDAO->findStatisticsQuality($dt_mes_referencia);
	
	return $res->withJson($statisticsQuality);
});

$app->post("/v1/meal/quality", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$qualityDAO = new QualityDAO();
	$quality = new Quality();

	$dataQuality = $req->getParsedBody();
	$dataQuality["nr_cracha"] = $session["nr_cracha"];
	$quality->setQualityValidate($dataQuality, 'POST');

	$nr_sequencia = $qualityDAO->register(
								$quality->getParsedArray()
							);

	return $res->withJson([
							"nr_sequencia" => $nr_sequencia
						  ]);

});

$app->put("/v1/meal/quality/{nr_sequencia}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$qualityDAO = new QualityDAO();
	$quality = new Quality();

	$dataQuality = $req->getParsedBody();
	$dataQuality["nr_cracha"] = $session["nr_cracha"];
	$quality->setQualityValidate($dataQuality, 'PUT');

	$wasChanged = $qualityDAO->updateQuality(
								$args['nr_sequencia'],
								$quality->getParsedArray()
							);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/meal/quality/{nr_sequencia}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$qualityDAO = new QualityDAO();

	$wasExcluded = $qualityDAO->deleteQuality(
								$args['nr_sequencia']								
							);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);

});