<?php
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\people\rules\People;
use \api\v1\resource\people\rules\PeopleDAO;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\utils\Image;
use \api\v1\vendor\error\Error;

$getPeople = function ($req, $res, $args, $session = []) {

	$peopleDAO = new PeopleDAO();

	$queryParams = $req->getQueryParams();
	$params = [];

	$fields = $queryParams["fields"] ?? '';
	$sortField = $queryParams["sort_field"] ?? '';
	$sortType = $queryParams["sort_type"] ?? 'ASC';
	$numberLine = $queryParams['number_line'] ?? 20;
	$skipLine = $queryParams['skip_line'] ?? 0;

	if (isset($args["nr_cracha"])) {
		$params["nr_cracha"] = $args["nr_cracha"];
	}

	if (isset($queryParams['nm_pessoa_fisica'])) {
		$params["nm_pessoa_fisica"] = $queryParams["nm_pessoa_fisica"];
	}
	
	if (isset($queryParams['cd_cargo'])) {
		$params["cd_cargo"] = $queryParams["cd_cargo"];
	}

	if (count($session) > 0) {
		$params["cd_setor"] =  $session["cd_setor"];
		$params["cd_grupo"] =  $session["cd_grupo"];
	} else {
		if (isset($queryParams["cd_setor"])) {
			$params["cd_setor"] = $queryParams["cd_setor"];
		} 

		if (isset($queryParams["cd_grupo"])) {
			$params["cd_grupo"] = $queryParams["cd_grupo"];
		}		
	} 

	if (isset($queryParams["ie_situacao"])) {
		$params["ie_situacao"] = $queryParams["ie_situacao"];
	}

	if (isset($queryParams["dt_inicio"]) && isset($queryParams["dt_fim"])) {
		$params["dates"] = array(
				"dt_inicio" => DateUtils::convert($queryParams["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']),
				"dt_fim" => DateUtils::convert($queryParams["dt_fim"], "BR-DATE", ['format' => 'Y-m-d'])
			);
	}

	$params["type_search"] = count($session) == 0 ? "free" : "default";

	$peoples = $peopleDAO->findPeopleAll($params, 
									     ClearData::stringSQL($fields),
									     ClearData::stringSQL($sortField),
									     ClearData::stringSQL($sortType),
										 $numberLine,
										 $skipLine);

	return $res->withJson($peoples);
};

$app->get('/v1/people[/{nr_cracha}]', function ($req, $res, $args) use ($getPeople) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	return $getPeople($req, $res, $args, $session);
});

$app->get('/v1/people/free/[{nr_cracha}]', function ($req, $res, $args) use ($getPeople) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	return $getPeople($req, $res, $args);
});

$app->post('/v1/people', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$people = new People();

	$people->setPeopleValidate($req->getParsedBody(), "POST");
	$dt_inclusao = new \DateTime();
	$people->setDtInclusao($dt_inclusao);
	
	$nr_cracha = $peopleDAO->register($people->getParsedArray());

	return $res->withJson(["nr_cracha" => $nr_cracha]);
});

$app->put('/v1/people/{nr_cracha}', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}
	
	$peopleDAO = new PeopleDAO();	
	$people = new People();
	$body = $req->getParsedBody() ?? [];

	$people->setPeopleValidate($body, "PUT");

	$wasChanged = $peopleDAO->updatePeople($args['nr_cracha'], $people->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->put('/v1/people/password/', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'), true);

	$peopleDAO = new PeopleDAO();	
	$people = new People();
	$body = $req->getParsedBody() ?? [];

	$people->setPasswordValidate($body);
	
	$wasChanged = $peopleDAO->updatePassword($session["nr_cracha"], $people->getParsedArray());

	return $res->withJson(["foi_alterado" => $wasChanged]);
});

$app->delete('/v1/people/{nr_cracha}', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$wasExcluded = $peopleDAO->deletePeople($args['nr_cracha']);

	if ($wasExcluded) {
		if (file_exists('./img/people/'.$args['nr_cracha'].'.jpg')) {
			unlink('./img/people/'.$args['nr_cracha'].'.jpg');
		} 
		
		if (file_exists('./img/people/'.$args['nr_cracha'].'.png')) {
			unlink('./img/people/'.$args['nr_cracha'].'.png');
		}
	}

	return $res->withJson(["foi_deletado" => $wasExcluded]);
});

$app->post('/v1/people/photo/{nr_cracha}', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();
	$people = new People();

	$files = $req->getUploadedFiles();
	$personExists = $peopleDAO->findPeople($args['nr_cracha'], 'nr_cracha');

	if (count($files) == 0 || count($personExists) == 0) {
		return $res->withJson(["foi_alterado" => false]);
	}

	$mediaType = $files['photo']->getClientMediaType();

	if (!array_key_exists("photo", $files)) {
		return $error->generateErrorCustom("PEO-01", "Valor photo é obrigatorio", 422);
	}

	switch ($mediaType) {
        case 'image/jpeg':
          	$type = 'jpg';
          	break;
        case 'image/png':
          	$type = 'png';
          	break;
        default: 
        	Error::generateErrorCustom("Media type $mediaType not acceptable", 422);     
    }

	$targetPath = "img/people/{$args['nr_cracha']}.$type";
	$people->setUrlFotoPerfil($targetPath);

	$wasChanged = $peopleDAO->updatePeople($args['nr_cracha'], ["url_foto_perfil" => $people->getUrlFotoPerfil()]);

	$files['photo']->moveTo($targetPath);
	$image = new Image($targetPath, "img/people/{$args['nr_cracha']}");
	$image->treatImage();

	if ($type == 'png' && file_exists('./img/people/'.$args['nr_cracha'].'.jpg')) {
		unlink('./img/people/'.$args['nr_cracha'].'.jpg');
	} 
	
	if ($type == 'jpg' && file_exists('./img/people/'.$args['nr_cracha'].'.png')) {
		unlink('./img/people/'.$args['nr_cracha'].'.png');
	}
	
	return $res->withJson(["foi_alterado" => true]);
});


$app->get('/v1/people/statistic/amount', function ($req, $res, $args) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));
	
	if (!Auth::checkPermission(11, $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$peopleDAO = new PeopleDAO();

	$amount = $peopleDAO->statisticAmount();

	return $res->withJson($amount);
});