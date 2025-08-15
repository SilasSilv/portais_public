<?php

use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\phonebook\rules\Phonebook;
use \api\v1\resource\phonebook\rules\PhonebookDAO;
use \api\v1\vendor\error\Error;

$app->get("/v1/phonebook[/{name_or_fone}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(1, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$phonebookDAO = new PhonebookDAO();

	$params = array_merge($args, $req->getQueryParams());
	$contacts = $phonebookDAO->findContact($params);

	return $res->withJson($contacts, 200, JSON_NUMERIC_CHECK);
});

$app->post("/v1/phonebook", function($req, $res) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(1, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$phonebook = new Phonebook();
	$phonebookDAO = new PhonebookDAO();

	$phonebook->setPhonebookValidate($req->getParsedBody());
	$nr_sequencia = $phonebookDAO->registerContact($phonebook->getParsedArray());

	return $res->withJson(["nr_sequencia" => $nr_sequencia]);
});

$app->put("/v1/phonebook/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(1, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$phonebook = new Phonebook();
	$phonebookDAO = new PhonebookDAO();

	$phonebook->setPhonebookValidate($req->getParsedBody());	
	$wasChanged = $phonebookDAO->updateContact($args['nr_sequencia'], $phonebook->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/phonebook/{nr_sequencia}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(1, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$phonebookDAO = new PhonebookDAO();

	$wasExcluded = $phonebookDAO->deleteContact($args['nr_sequencia']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});