<?php 
use \api\v1\vendor\error\Error;
use \api\v1\resource\category\rules\CategoryType;
use \api\v1\resource\category\rules\CategoryTypeDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/category/type/[{cd_tipo_categoria}]", function($req, $res, $args) {
    $session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
    
    if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$categoryTypeDAO = new CategoryTypeDAO();

	$params = array_merge($args, $req->getQueryParams());
	$categorysTypes = $categoryTypeDAO->find($params);

	return $res->withJson($categorysTypes, 200, JSON_NUMERIC_CHECK);
});

$app->post("/v1/category/type/", function($req, $res) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$categoryType = new CategoryType();
	$categoryTypeDAO = new CategoryTypeDAO();

	$categoryTypeData = $req->getParsedBody();
	$categoryType->setCategoryTypeValidate($categoryTypeData);

	$cd_tipo_categoria = $categoryTypeDAO->register($categoryType->getParsedArray());

	return $res->withJson(["cd_tipo_categoria" => $cd_tipo_categoria]);
});

$app->put("/v1/category/type/{cd_tipo_categoria}", function($req, $res, $args) {
    $session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$categoryType = new CategoryType();
	$categoryTypeDAO = new CategoryTypeDAO();

	$categoryTypeData = $req->getParsedBody();
	$categoryType->setCategoryTypeValidate($categoryTypeData);

	$wasChanged = $categoryTypeDAO->updateCategoryType($args['cd_tipo_categoria'], $categoryType->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);	
});

$app->delete("/v1/category/type/{cd_tipo_categoria}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$categoryTypeDAO = new CategoryTypeDAO();

	$wasExcluded = $categoryTypeDAO->deleteCategoryType($args['cd_tipo_categoria']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});