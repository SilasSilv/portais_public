<?php
use \api\v1\vendor\error\Error;
use \api\v1\resource\sector\rules\Sector;
use \api\v1\resource\sector\rules\SectorDAO;
use \api\v1\vendor\authorization\Authorization as Auth;


$app->get("/v1/sector[/{cd_setor}]", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	$sectorDAO = new SectorDAO();

	$params = array_merge($args, $req->getQueryParams());
	$sector = $sectorDAO->find($params);

	return $res->withJson($sector);
});

$app->post("/v1/sector", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$sector = new Sector();
	$sectorDAO = new SectorDAO();

	$sectorData = $req->getParsedBody();
	$sector->setSectorValidate($sectorData, 'POST');

	$cd_setor = $sectorDAO->register($sector->getParsedArray());

	return $res->withJson(["cd_setor" => $cd_setor]);
});

$app->put("/v1/sector/{cd_setor}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$sector = new Sector();
	$sectorDAO = new SectorDAO();

	$sectorData = $req->getParsedBody();
	$sector->setSectorValidate($sectorData);

	$wasChanged = $sectorDAO->updateSector($args['cd_setor'], $sector->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete("/v1/sector/{cd_setor}", function($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$sectorDAO = new SectorDAO();

	$wasExcluded = $sectorDAO->deleteSector($args['cd_setor']);

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});