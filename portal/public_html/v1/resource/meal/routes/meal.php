<?php 
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\meal\rules\meal\Meal;
use \api\v1\resource\meal\rules\meal\MealDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

$app->get("/v1/meal", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$mealDAO = new MealDAO();

	$queryParams = $req->getQueryParams();

	$fields = $queryParams["fields"] ?? '';

	if(isset($queryParams["nr_refeicao"])) {

		$meal = $mealDAO->findMeal($queryParams["nr_refeicao"],
								   ClearData::stringSQL($fields));

		return $res->withJson($meal);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? 'ASC';
		$numberLine = $queryParams['number_line'] ?? "ALL";

		$params = [];
				
		if (isset($queryParams["ie_situacao"])) {
			$params["ie_situacao"] = $queryParams["ie_situacao"];
		}

		if (isset($queryParams["ie_tipo_refeicao"])) {
			$params["ie_tipo_refeicao"] = $queryParams["ie_tipo_refeicao"];
		}

		if (isset($queryParams['ds_refeicao'])) {
			$params["ds_refeicao"] = $queryParams["ds_refeicao"];
		}

		if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
			$params["dates"] = array(
					"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
					"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
				);
		}
	
		$meals = $mealDAO->findMealAll($params, 
									   ClearData::stringSQL($fields),
									   ClearData::stringSQL($sortField),
									   ClearData::stringSQL($sortType),
									   $numberLine);

		return $res->withJson($meals);
	}
});

$app->post("/v1/meal", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$mealDAO = new MealDAO();	
	$meal = new Meal();
	
	$mealData = $req->getParsedBody();
	$mealData['nr_cracha'] = $session['nr_cracha'];
	$meal->setMealValidate($mealData);

	$nr_sequencia = $mealDAO->register($meal->getParsedArray());

	return $res->withJson(["nr_sequencia" => $nr_sequencia]);
});

$app->put("/v1/meal/{nr_refeicao}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$queryParams = $req->getQueryParams();
	$inactivate = $queryParams['inactivate'] ?? 'N';
	
	$mealDAO = new MealDAO();
	$meal = new Meal();

	if ($inactivate == 'S') {
		$meal->setIeSituacao('I');
	} else {
		$mealData = $req->getParsedBody();
		$mealData['nr_cracha'] = $session['nr_cracha'];
		$meal->setMealValidate($mealData);
	}	
	
	$wasChanged = $mealDAO->updateMeal(
								$args['nr_refeicao'],
								$meal->getParsedArray()
							);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/meal/{nr_refeicao}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(7, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$mealDAO = new MealDAO();

	$wasExcluded = $mealDAO->deleteMeal(
		$args['nr_refeicao']								
	);

	return $res->withJson([
		"foi_deletado" => $wasExcluded
	]);
});