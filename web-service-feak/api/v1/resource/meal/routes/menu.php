<?php

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\menu\MenuRequest;
use \api\v1\resource\meal\rules\menu\MenuDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$app->get("/v1/meal/menu[/{nr_refeicao}]", function($req, $res, $args) {
	
	$token = $req->getHeader('Authorization')[0] ?? '';

	if ($token != '4b348ba0-ccb1-4ae1-89a4-6b49effa8249') {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$menuDAO = new MenuDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';

	if (isset($args['nr_refeicao'])) {

		$menu = $menuDAO->findMenu($args['nr_refeicao'],
								   ClearData::stringSQL($fields));

		return $res->withJson($menu);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? '';
		$numberLine = $queryParams['number_line'] ?? 'ALL';

		$params = [];
		
		if (isset($queryParams["dobras_terceiros"])) {
			if ($queryParams["dobras_terceiros"] == 'S') {
				$params["dobras_terceiros"] = $queryParams["dobras_terceiros"];
			}
		}

		if (isset($queryParams["ie_tipo_refeicao"])) {
			$params["ie_tipo_refeicao"] = $queryParams["ie_tipo_refeicao"];
		}

		if (isset($queryParams['ds_refeicao'])) {
			$params["ds_refeicao"] = $queryParams["ds_refeicao"];
		}

		if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
			$params["dates"] = array(
					"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "EUA-DATE"),
					"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "EUA-DATE")
				);
		}
		
		$menu = $menuDAO->findMenuAll($params, 
									  ClearData::stringSQL($fields),
									  ClearData::stringSQL($sortField),
									  ClearData::stringSQL($sortType),
									  $numberLine);

		return $res->withJson($menu);

	}

});

$app->get("/v1/meal/menu/request/", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$menuDAO = new MenuDAO();

	$fields = isset($req->getQueryParams()["fields"]) ? $req->getQueryParams()["fields"] : '';
	$sortField = isset($req->getQueryParams()["sort_field"]) ? $req->getQueryParams()["sort_field"] : '';
	$sortType = isset($req->getQueryParams()["sort_type"]) ? $req->getQueryParams()["sort_type"] : '';
	$numberLine = isset($req->getQueryParams()['number_line']) ? $req->getQueryParams()['number_line'] : 'ALL'; 
	$params = [];
		
	$params["nr_cracha"] = $session["nr_cracha"];
	
	if (isset($req->getQueryParams()["ie_tipo_refeicao"])) {
		$params["ie_tipo_refeicao"] = $req->getQueryParams()["ie_tipo_refeicao"];
	}

	if (isset($req->getQueryParams()['ds_refeicao'])) {
		$params["ds_refeicao"] = $req->getQueryParams()["ds_refeicao"];
	}

	if(isset($req->getQueryParams()["dt_inicio"]) && isset($req->getQueryParams()["dt_fim"])) {
		$params["dates"] = array(
				"dt_inicio" => DateUtils::convert($req->getQueryParams()["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
				"dt_fim" => DateUtils::convert($req->getQueryParams()["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
			);
	}
	
	$menu = $menuDAO->findMealRequest($params, 
									  ClearData::stringSQL($fields),
									  ClearData::stringSQL($sortField),
									  ClearData::stringSQL($sortType),
									  $numberLine);

	return $res->withJson($menu);	

});

$app->post("/v1/meal/menu/request/{nr_refeicao}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$menuRequest = new MenuRequest();
	$menuDAO = new MenuDAO();

	$menuMeal = $menuDAO->findMenu($args['nr_refeicao'], 'ds_dia,dt_final,ie_feriado');

	if (count($menuMeal) == 0) {
		Error::generateErrorCustom('Meal not found', 404);
	} else { 
		$dt_atual = new \DateTime();
		$dt_final = DateUtils::convert($menuMeal[0]['dt_final'], 'BR-DATETIME', ['name_field' => 'dt_final']);

		if ($dt_final < $dt_atual) {
			Error::generateErrorCustom('Meal is not available', 406);
		}

		if ($menuMeal[0]['ds_dia'] == 'Sab' || $menuMeal[0]['ds_dia'] == 'Dom' || $menuMeal[0]['ie_feriado'] == 'S') {
			if (!Auth::checkPermission(14, $session['nr_cracha'], $session['cd_setor'])) {
				Error::generateErrorCustom('You are not allowed to request on holiday and weekend', 422);
			}
		} 
    }

	$menuRequest->setRequestValidate([
			"nr_cracha" => $session['nr_cracha'],
			"nr_refeicao" => $args['nr_refeicao']
		], 
		'POST'
	);

	$menuDAO->mealRequest(
		$menuRequest->getParsedArray()
	);

	return $res->withJson([
		"foi_cadastrado" => true
	]);
});

$app->put("/v1/meal/menu/request/{nr_refeicao}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$menuRequest = new MenuRequest();
	$menuDAO = new MenuDAO();

	$menuMeal = $menuDAO->findMenu($args['nr_refeicao'], 'ds_dia,dt_final,ie_feriado');

	if (count($menuMeal) == 0) {
		Error::generateErrorCustom('Meal not found', 404);
	} else { 
		$dt_atual = new \DateTime();
		$dt_final = DateUtils::convert($menuMeal[0]['dt_final'], 'BR-DATETIME', ['name_field' => 'dt_final']);

		if ($dt_final < $dt_atual) {
			Error::generateErrorCustom('Meal is not available', 406);
		}

		if ($menuMeal[0]['ds_dia'] == 'Sab' || $menuMeal[0]['ds_dia'] == 'Dom' || $menuMeal[0]['ie_feriado'] == 'S') {
			if (!Auth::checkPermission(14, $session['nr_cracha'], $session['cd_setor'])) {
				Error::generateErrorCustom('You are not allowed to request on holiday and weekend', 422);
			}
		}
    }

	$menuRequest->setRequestValidate($req->getParsedBody(), 'PUT');

	$wasChanged = $menuDAO->updateMealRequest(
				$session['nr_cracha'],
				$args['nr_refeicao'],
				$menuRequest->getParsedArray()
			);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/meal/menu/request/{nr_refeicao}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(6, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$menuDAO = new MenuDAO();

	$menuMeal = $menuDAO->findMenu($args['nr_refeicao'], 'ds_dia,dt_final,ie_feriado');

	if (count($menuMeal) == 0) {
		Error::generateErrorCustom('Meal not found', 404);
	} else { 
		$dt_atual = new \DateTime();
		$dt_final = DateUtils::convert($menuMeal[0]['dt_final'], 'BR-DATETIME', ['name_field' => 'dt_final']);

		if ($dt_final < $dt_atual) {
			Error::generateErrorCustom('Meal is not available', 406);
		}

		if ($menuMeal[0]['ds_dia'] == 'Sab' || $menuMeal[0]['ds_dia'] == 'Dom' || $menuMeal[0]['ie_feriado'] == 'S') {
			if (!Auth::checkPermission(14, $session['nr_cracha'], $session['cd_setor'])) {
				Error::generateErrorCustom('You are not allowed to request on holiday and weekend', 422);
			}
		}
    }

	$wasExcluded = $menuDAO->deleteMealRequest(
		$session['nr_cracha'],
		$args['nr_refeicao']
	);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);
	
});