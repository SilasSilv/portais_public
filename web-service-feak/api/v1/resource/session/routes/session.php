<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\session\rules\SessionDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/session[/{nr_sequencia}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
    }

    $sessionDAO = new SessionDAO();

	$params = array_merge($args, $req->getQueryParams());
	$sessions = $sessionDAO->find($params);
    
	return $res->withJson($sessions);
});

$app->get("/v1/session/amount/", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
    }

    $sessionDAO = new SessionDAO();

	$amount = $sessionDAO->amount();
    
	return $res->withJson($amount);
});


$app->put("/v1/session/{nr_sequencia}/close", function($req, $res, $args) {
    $session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(16, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
    }

    $sessionDAO = new SessionDAO();

	$close = $sessionDAO->close($args["nr_sequencia"]);
    
	return $res->withJson(["foi_fechado" => $close]);
});