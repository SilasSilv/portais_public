<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\group\rules\Group;
use \api\v1\resource\group\rules\GroupDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/group[/{cd_grupo}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$groupDAO = new GroupDAO();

	$params = array_merge($args, $req->getQueryParams());
	$groups = $groupDAO->find($params);

	return $res->withJson($groups);
});

$app->post("/v1/group", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$group = new Group();
	$groupDAO = new GroupDAO();

	$groupData = $req->getParsedBody();
	$group->setGroupValidate($groupData, 'POST');

	$cd_grupo = $groupDAO->register($group->getParsedArray());

	return $res->withJson(["cd_grupo" => $cd_grupo]);
});

$app->put("/v1/group/{cd_grupo}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$group = new Group();
	$groupDAO = new GroupDAO();

	$groupData = $req->getParsedBody();
	$group->setGroupValidate($groupData, 'PUT');

	$wasChanged = $groupDAO->updateGroup($args['cd_grupo'], $group->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/group/{cd_grupo}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$groupDAO = new GroupDAO();

	$wasExcluded = $groupDAO->deleteGroup($args['cd_grupo']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});