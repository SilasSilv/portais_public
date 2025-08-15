<?php 
use \api\v1\resource\category\rules\Category;
use \api\v1\resource\category\rules\CategoryDAO;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\error\Error;

$app->get("/v1/category[/{cd_categoria}]", function($req, $res, $args) {

	$this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	$categoryDAO = new CategoryDAO();	

	$queryParams = $req->getQueryParams();
	$fields = $queryParams["fields"] ?? '';

	if (isset($args['cd_categoria'])) {

		$category = $categoryDAO->findCategory($args['cd_categoria'],
											   ClearData::stringSQL($fields));

		return $res->withJson($category);

	} else {

		$sortField = $queryParams["sort_field"] ?? '';
		$sortType = $queryParams["sort_type"] ?? 'ASC';
		$numberLine = $queryParams['number_line'] ?? 20;
		$skipLine = $queryParams['skip_line'] ?? 0;

		$params = [];
		
		if (isset($queryParams["cd_tipo_categoria"])) {
			$params["cd_tipo_categoria"] = $queryParams["cd_tipo_categoria"];
		}
		
		$categorys = $categoryDAO->findCategoryAll($params, 
				 				   			  	   ClearData::stringSQL($fields),
											 	   ClearData::stringSQL($sortField),
											 	   ClearData::stringSQL($sortType),
											 	   $numberLine,
											  	   $skipLine);

		return $res->withJson($categorys, 200, JSON_NUMERIC_CHECK);

	}

});

$app->post("/v1/category", function($req, $res) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$category = new Category();
	$categoryDAO = new CategoryDAO();

	$categoryData = $req->getParsedBody();
	$category->setCategoryValidate($categoryData);
	
	$cd_categoria = $categoryDAO->register(
			$category->getParsedArray()
		);

	return $res->withJson([
							"cd_categoria" => $cd_categoria
						  ]);
	
});

$app->put("/v1/category/{cd_categoria}", function($req, $res, $args) {

	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$category = new Category();
	$categoryDAO = new CategoryDAO();

	$categoryData = $req->getParsedBody();
	$category->setCategoryValidate($categoryData);

	$wasChanged = $categoryDAO->updateCategory(
			$args['cd_categoria'], 
			$category->getParsedArray()
		);

	return $res->withJson([
							"foi_alterado" => $wasChanged
						  ]);

});

$app->delete("/v1/category/{cd_categoria}", function($req, $res, $args) {
	
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$categoryDAO = new CategoryDAO();

	$wasExcluded = $categoryDAO->deleteCategory($args['cd_categoria']);

	return $res->withJson([
							"foi_deletado" => $wasExcluded
						  ]);
});