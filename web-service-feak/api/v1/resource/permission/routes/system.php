<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\permission\rules\PermissionUtils;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\permission\rules\system\PermissionSystem;
use \api\v1\resource\permission\rules\system\PermissionSystemDAO;
use \api\v1\resource\permission\rules\system\PermissionSystemPeopleDAO;
use \api\v1\resource\permission\rules\system\PermissionSystemSectorDAO;

$app->get("/v1/permission/system[/{cd_sistema:[0-9]+}]", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$permissionSystemDAO = new PermissionSystemDAO();

	$params = array_merge($args, $req->getQueryParams());
	$permissions = $permissionSystemDAO->find($params);

    return $res->withJson($permissions);
});

$app->get("/v1/permission/system/group[/{cd_sistema:[0-9]+}]", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$permissionUtils = new PermissionUtils();
	$permissionSystemDAO = new PermissionSystemDAO();

	$params = array_merge($args, $req->getQueryParams());
	$params['fields'] = 'cd_permissao,ds_titulo,ds_descricao,vl_padrao,ds_tipo_categoria';

	$permissions = $permissionSystemDAO->find($params);
	$permissions = $permissionUtils->resolvePermissionsGroup($permissions);

	return $res->withJson($permissions);
});

$app->get("/v1/permission/system/{cd_sistema:[0-9]+}/people[/{cd_permissao:[0-9]+}]", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$permissionSystemPeopleDAO = new PermissionSystemPeopleDAO();

	$params = array_merge($args, $req->getQueryParams());
	$permissions = $permissionSystemPeopleDAO->find($params);

    return $res->withJson($permissions);
});

$app->get("/v1/permission/system/{cd_sistema:[0-9]+}/sector[/{cd_permissao:[0-9]+}]", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$permissionSystemSectorDAO = new PermissionSystemSectorDAO();

	$params = array_merge($args, $req->getQueryParams());
	$permissions = $permissionSystemSectorDAO->find($params);

    return $res->withJson($permissions);
});

$app->post("/v1/permission/system", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}   

	$permissionSystem = new PermissionSystem();
	$permissionSystemDAO = new PermissionSystemDAO();

	$permission = $req->getParsedBody() ?? [];
	$permissionSystem->setPermissionValidate($permission);

 	$cd_permissao =  $permissionSystemDAO->register($permissionSystem->getParsedArray());

    return $res->withJson(["cd_permissao" => $cd_permissao]);
});

$app->post("/v1/permission/system/image/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSystem = new PermissionSystem();
	$permissionSystemDAO = new PermissionSystemDAO();

	$files = $req->getUploadedFiles();

	$image = $permissionSystem->setAlternativeImage($files);
	$wasRegister = $permissionSystemDAO->registerImage($args["cd_permissao"], $image);

	return $res->withJson(["foi_alterado" => $wasRegister]);
});

$app->put("/v1/permission/system/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSystem = new PermissionSystem();
	$permissionSystemDAO = new PermissionSystemDAO();

	$permission = $req->getParsedBody() ?? [];
	$permissionSystem->setPermissionValidate($permission);

	$wasChanged = $permissionSystemDAO->updatePermission($args['cd_permissao'], $permissionSystem->getParsedArray());

    return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/permission/system/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSystemDAO = new PermissionSystemDAO();

	$wasExcluded = $permissionSystemDAO->deletePermission($args['cd_permissao']);

    return $res->withJson(["foi_deletado" => $wasExcluded]);
});

$app->delete("/v1/permission/system/image/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$permissionSystemDAO = new PermissionSystemDAO();

	$wasExcluded = $permissionSystemDAO->deleteImage($args['cd_permissao']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});