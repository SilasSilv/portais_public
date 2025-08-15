<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\people\rules\PeopleDAO;
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\permission\rules\people\PermissionPeople;
use \api\v1\resource\permission\rules\people\PermissionPeopleDAO;

$app->get("/v1/permission/{nr_cracha:[0-9]+}/people", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$permissionPeopleDAO = new PermissionPeopleDAO();
	
	$people = $peopleDAO->findPeople($args["nr_cracha"], "nr_cracha, cd_setor");
	$params = $req->getQueryParams();

	if (! empty($people)) {
		$params = array_merge($people[0], $params);
	} else {
		return $res->withJson([]);
	}
	
	$permissions = $permissionPeopleDAO->find($params);
	return $res->withJson($permissions);
});

$app->post("/v1/permission/{nr_cracha:[0-9]+}/people", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionPeople = new PermissionPeople();
	$permissionPeopleDAO = new PermissionPeopleDAO();

	$permission = array_merge($args, ($req->getParsedBody() ?? []));
	$permissionPeople->setValidate($permission);

	$wasPersist =  $permissionPeopleDAO->persist($permissionPeople->getPermissions(), $args['nr_cracha']);

	return $res->withJson(["foi_persistido" => $wasPersist]);
});

$app->post("/v1/permission/{nr_cracha:[0-9]+}/people/{cd_permissao:[0-9]+}/{vl_pf:S|N|D}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionPeopleDAO = new PermissionPeopleDAO();

	$cd_permissao = $permissionPeopleDAO->register($args);

	return $res->withJson(["cd_permissao" => $cd_permissao]);
});

$app->put("/v1/permission/{nr_cracha:[0-9]+}/people/{cd_permissao:[0-9]+}/{vl_pf:S|N|D}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionPeopleDAO = new PermissionPeopleDAO();

	$wasChanged = $permissionPeopleDAO->updatePermission(['vl_pf' => $args['vl_pf']], $args);

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/permission/{nr_cracha:[0-9]+}/people/{cd_permissao:[0-9]+}", function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor']) &&
		!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$permissionPeopleDAO = new PermissionPeopleDAO();

	$wasExcluded = $permissionPeopleDAO->deletePermission($args);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});