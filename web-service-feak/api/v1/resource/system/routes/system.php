<?php
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\UniqueID;
use \api\v1\resource\system\rules\System;
use \api\v1\resource\system\rules\SystemDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/system[/{cd_sistema}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
    }

    $systemDAO = new SystemDAO();

	$params = array_merge($args, $req->getQueryParams());
	$systems = $systemDAO->find($params);

	return $res->withJson($systems);
});

$app->get("/v1/system/id/", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$uniqueID = new UniqueID();
	
	return $res->withJson(["id_sistema" => $uniqueID->uuid()]);
});

$app->post('/v1/system', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$system = new System();
	$systemDAO = new SystemDAO();

	$systemData = $req->getParsedBody();
	
	if (array_key_exists("img_logo", $req->getUploadedFiles())) {
		$systemData["img_logo"] = $req->getUploadedFiles()["img_logo"];
	}

	$system->setSystemValidate($systemData, "CREATE");
	
	$cd_sistema = $systemDAO->register($system->getParsedArray());

	return $res->withJson(["cd_sistema" => $cd_sistema]);
});

$app->post('/v1/system/{cd_sistema}', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$system = new System();
	$systemDAO = new SystemDAO();

	$systemData = $req->getParsedBody();
	$system->setSystemValidate($systemData);
	$system->setImgLogoUpdate($req, $args['cd_sistema']);

	$cd_sistema = $systemDAO->updateSystem($args['cd_sistema'], $system->getParsedArray("UPDATE"));

	return $res->withJson(["cd_sistema" => $cd_sistema]);
});


$app->delete('/v1/system/{cd_sistema}', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$systemDAO = new SystemDAO();
	
	$wasExcluded = $systemDAO->deleteSystem($args['cd_sistema']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});