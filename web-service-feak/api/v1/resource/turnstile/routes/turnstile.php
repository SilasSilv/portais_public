<?php

use \api\v1\vendor\error\Error;
use \api\v1\resource\turnstile\rules\Turnstile;
use \api\v1\resource\turnstile\rules\TurnstileDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/turnstile[/{HE02_ST_MATRICULA:[0-9]+}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(17, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$turnstileDAO = new TurnstileDAO();

	$params = array_merge($args, $req->getQueryParams());
	$people = $turnstileDAO->find($params);

	return $res->withJson($people);
});


$app->get("/v1/turnstile/access/{dt_inicio:\d{2}\/\d{2}\/\d{4}}/{dt_fim:\d{2}\/\d{2}\/\d{4}}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(17, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$turnstile = new Turnstile();
	$turnstileDAO = new TurnstileDAO();
	
	$params = array_merge($args, $req->getQueryParams());
	$params = $turnstile->treatParams($params);

	$access = $turnstileDAO->findAccess($params);

	return $res->withJson($access);
});