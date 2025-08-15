<?php
use \api\v1\resource\permission\rules\sector\PermissionSector;
use \api\v1\resource\permission\rules\sector\PermissionSectorDAO;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\vendor\error\Error;


$app->get("/v1/permission/{cd_setor:[0-9]+}/sector", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSectorDAO = new PermissionSectorDAO();

	$params = array_merge($args, $req->getQueryParams());
	$permissions = $permissionSectorDAO->find($params);

	return $res->withJson($permissions);
});

$app->post("/v1/permission/{cd_setor:[0-9]+}/sector", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSector = new PermissionSector();
	$permissionSectorDAO = new PermissionSectorDAO();

	$permission = array_merge($args, ($req->getParsedBody() ?? []));
	$permissionSector->setValidate($permission);

	$wasPersist =  $permissionSectorDAO->persist($permissionSector->getPermissions(), $args['cd_setor']);

	return $res->withJson(["foi_persistido" => $wasPersist]);
});

$app->post("/v1/permission/{cd_setor:[0-9]+}/sector/{cd_permissao:[0-9]+}/{vl_setor:S|N|D}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSectorDAO = new PermissionSectorDAO();

	$cd_permissao = $permissionSectorDAO->register($args);

	return $res->withJson(["cd_permissao" => $cd_permissao]);
});

$app->put("/v1/permission/{cd_setor:[0-9]+}/sector/{cd_permissao:[0-9]+}/{vl_setor:S|N|D}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSectorDAO = new PermissionSectorDAO();

	$wasChanged = $permissionSectorDAO->updatePermission(['vl_setor' => $args['vl_setor']], $args);

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/permission/{cd_setor:[0-9]+}/sector/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionSectorDAO = new PermissionSectorDAO();

	$wasExcluded = $permissionSectorDAO->deletePermission($args);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});