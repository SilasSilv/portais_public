<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\office\rules\Office;
use \api\v1\resource\office\rules\OfficeDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/office[/{cd_cargo}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$officeDAO = new OfficeDAO();

	$params = array_merge($args, $req->getQueryParams());
	$offices = $officeDAO->find($params);

	return $res->withJson($offices);
});

$app->post("/v1/office", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$office = new Office();
	$officeDAO = new OfficeDAO();

	$officeData = $req->getParsedBody();
	$office->setOfficeValidate($officeData, 'POST');

	$cd_cargo = $officeDAO->register($office->getParsedArray());

	return $res->withJson(["cd_cargo" => $cd_cargo]);
});

$app->put("/v1/office/{cd_cargo}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$office = new Office();
	$officeDAO = new OfficeDAO();

	$officeData = $req->getParsedBody();
	$office->setOfficeValidate($officeData, 'PUT');

	$wasChanged = $officeDAO->updateOffice($args['cd_cargo'], $office->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/office/{cd_cargo}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$officeDAO = new OfficeDAO();

	$wasExcluded = $officeDAO->deleteOffice($args['cd_cargo']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});