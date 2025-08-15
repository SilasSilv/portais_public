<?php
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\utils\ClearData;
use \api\v1\resource\meal\rules\price\Price;
use \api\v1\resource\meal\rules\price\PriceDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/meal/price[/{nr_sequencia}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor']) || 
		!Auth::checkPermission(13, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$priceDAO = new PriceDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';

	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? '';
	$numberLine = $queryParams['number_line'] ?? 'ALL';
	$params = [];

	if (isset($args['nr_sequencia'])) {
		$params["nr_sequencia"] = $args["nr_sequencia"];
	} 

	if (isset($queryParams["dt_vigencia_inicial_i"]) && isset($queryParams["dt_vigencia_inicial_f"])) {
		$params["dt_vigencia_inicial"] = array(
				"dt_inicio" => DateUtils::convert($queryParams["dt_vigencia_inicial_i"], "BR-DATE", ['format' => 'Y-m-d']),
				"dt_fim" => DateUtils::convert($queryParams["dt_vigencia_inicial_f"], "BR-DATE", ['format' => 'Y-m-d'])
			);
	}

	if (isset($queryParams["dt_vigencia_final_i"]) && isset($queryParams["dt_vigencia_final_f"])) {
		$params["dt_vigencia_final"] = array(
				"dt_inicio" => DateUtils::convert($queryParams["dt_vigencia_final_i"], "BR-DATE", ['format' => 'Y-m-d']),
				"dt_fim" => DateUtils::convert($queryParams["dt_vigencia_final_f"], "BR-DATE", ['format' => 'Y-m-d'])
			);
	}

	if (isset($queryParams["vl_refeicao"])) {
		$params["vl_refeicao"] = $queryParams["vl_refeicao"];
	}
	
	if (isset($queryParams["ie_situacao"])) {
		$params["ie_situacao"] = $queryParams["ie_situacao"];
	}

	$price = $priceDAO->findPrice($params, ClearData::stringSQL($fields), ClearData::stringSQL($sortField),
										ClearData::stringSQL($sortType), $numberLine);

	return $res->withJson($price);
});

$app->post("/v1/meal/price", function($req, $res) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor']) || 
		!Auth::checkPermission(13, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$price = new Price();
	$priceDAO = new PriceDAO();

	$priceData = $req->getParsedBody();
	$price->setPriceValidate($priceData);

	$nr_sequencia = $priceDAO->register($price->getParsedArray());

	return $res->withJson(["nr_sequencia" => $nr_sequencia]);
});

$app->put("/v1/meal/price/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor']) || 
		!Auth::checkPermission(13, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$price = new Price();
	$priceDAO = new PriceDAO();

	$priceData = $req->getParsedBody();
	$price->setPriceValidate($priceData);

	$wasChanged = $priceDAO->updatePrice($args['nr_sequencia'], $price->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/meal/price/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor']) || 
		!Auth::checkPermission(13, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$priceDAO = new PriceDAO();

	$wasExcluded = $priceDAO->deletePrice($args['nr_sequencia']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});