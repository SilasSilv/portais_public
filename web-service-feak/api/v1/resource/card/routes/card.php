<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\card\rules\Card;
use \api\v1\resource\card\rules\CardDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

$app->get("/v1/card[/{nr_cartao}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$cardDAO = new CardDAO();

	$params = array_merge($args, $req->getQueryParams());
	$cards = $cardDAO->find($params);

	return $res->withJson($cards);
});

$app->post("/v1/card", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$card = new Card();
	$cardDAO = new CardDAO();

	$cardData = $req->getParsedBody();
	$card->setCardValidate($cardData, 'POST');

	$nr_cartao = $cardDAO->register($card->getParsedArray());

	return $res->withJson(["nr_cartao" => $nr_cartao]);

});

$app->put("/v1/card/{nr_cartao}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$card = new Card();
	$cardDAO = new CardDAO();

	$cardData = $req->getParsedBody();
	$card->setCardValidate($cardData, 'PUT');

	$wasChanged = $cardDAO->updateCard($args['nr_cartao'], $card->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/card/{nr_cartao}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$cardDAO = new CardDAO();

	$wasExcluded = $cardDAO->deleteCard($args['nr_cartao']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});