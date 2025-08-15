<?php
	
use \api\v1\vendor\authorization\Authorization as Auth;
use \api\v1\resource\login\rules\LoginDAO;
use \api\v1\resource\login\rules\Login;
use \api\v1\vendor\utils\StringUtils;
use \api\v1\vendor\session\Session;
use \api\v1\vendor\system\System;
use \api\v1\vendor\error\Error;

$app->post("/v1/login", function($req, $res, $args) {

	$session = new Session();
	$system = new System();
	$loginDAO = new LoginDAO();
	
	$token = $req->getHeader('Authorization')[0] ?? ''; 

	$systemData = $system->findSystem(["cd_token" => $token]);

	if (count($systemData) == 0 || $systemData['ie_situacao'] != 'A') {
		Error::generateErrorCustom('Invalid token', 403);	
	}	

	$cd_permissao = $req->getQueryParams()['cd_permissao'] ?? NULL;
	$expire	= $req->getQueryParams()['expire'] ?? 3600;

	$user_name = $req->getParsedBody()['user_name'] ?? '';
	$senha = $req->getParsedBody()['senha'] ?? '';		

	if ($user_name == '' || $senha == '') {
		Error::generateErrorCustom('Missing parameters', 401);
	} 

	$resultLogin = $loginDAO->checkLogin($user_name, $senha);	
		
	if (count($resultLogin) == 0) {
		Error::generateErrorCustom('Invalid credentials', 401);
	} elseif ($resultLogin['ie_situacao'] != 'A') { 
		Error::generateErrorCustom('Inactive user', 403);
	}

	$auth = Auth::checkSystemAccess($systemData['cd_sistema'], $cd_permissao, $resultLogin['nr_cracha'], $resultLogin['cd_setor']);

	if (!$auth) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$sessionData = $session->createSession($systemData['cd_sistema'], $resultLogin['nr_cracha'], $expire);

	$dateExpire = \DateTime::createFromFormat('Y-m-d H:i:s', $sessionData['expire']);
	$sessionData['expire'] = $dateExpire->format('d/m/Y H:i:s');

	$permission = Auth::findSystemAll($systemData['cd_sistema'], 
									  $resultLogin['nr_cracha'], 
								   	  $resultLogin['cd_setor'], 
								   	  'Client') ?? [];

	$name = StringUtils::pretty($resultLogin['nm_pessoa_fisica']);

	$paramsCheckDataSystem = ['nr_cracha' => $resultLogin['nr_cracha']];
	$returnCheckDataSystem = Login::checkDataSystem($systemData['cd_sistema'], $paramsCheckDataSystem);

	$user = [
		"user" => [
			"name" => $name,
			"photo"	=> $resultLogin['url_foto_perfil'],
			"update_password" =>  $resultLogin['ie_alterar_senha'],
			"system_data" => $returnCheckDataSystem
		],
		"session" => $sessionData,
		"permissions" => $permission
	];

	return $res->withJson($user);
});

$app->put("/v1/login/{refresh_token}[/{expire}]", function($req, $res, $args) {

	$token = $req->getHeader('Authorization') ?? '';
	$this->get('checkAuthenticated')($token);
			
	$session = new Session();

	$refresh_token = $args['refresh_token'];
	$expire = $args['expire'] ?? 3600;
	
	$sessionData = $session->updateSession($token[0], $refresh_token, $expire);

	$dateExpire = \DateTime::createFromFormat('Y-m-d H:i:s', $sessionData['expire']);
	$sessionData['expire'] = $dateExpire->format('d/m/Y H:i:s');

	return $res->withJson([
							"session" => [
								"token" => $sessionData['token'],
								"refresh_token" => $sessionData['refresh_token'],
								"expire" => $sessionData['expire']
							]
						]);

});


$app->put("/v1/logout", function($req, $res, $args) {

	$token = $req->getHeader('Authorization') ?? [];
	$this->get('checkAuthenticated')($token);

	$session = new Session();

	$session->destroySession($token[0]);

	return $res->withJson(['message' => 'ok']);

});


$app->get("/v1/login/ME", function($req, $res) {

	$token = $req->getHeader('Authorization') ?? [];
	$session = $this->get('checkAuthenticated')($token);

	$cd_permissao = $req->getQueryParams()['cd_permissao'] ?? NULL;

	if ($cd_permissao != NULL) {
		$permission = Auth::checkPermission($cd_permissao, $session['nr_cracha'], $session['cd_setor']);

		if (!$permission) {
			Error::generateErrorApi(Error::NOT_AUTHORIZED);
		}		
	}

	$loginDAO = new LoginDAO();
	$me = $loginDAO->getME($token[0]);

	return $res->withJson($me);

});

$app->get("/v1/login/system", function($req, $res) {
	$session = $this->get('checkAuthenticated')($req->getHeader('Authorization'));

	$loginDAO = new LoginDAO();
	$systems = $loginDAO->findSystemsAccess($session["nr_cracha"], $session['cd_setor']);

	return $res->withJson($systems);
});

$app->put("/v1/login/system/{cd_sistema}/{cd_permissao}", function($req, $res, $args) {
	$token = $req->getHeader('Authorization');
	$session = $this->get('checkAuthenticated')($token);

	if (!Auth::checkPermission($args['cd_permissao'], $session['nr_cracha'], $session['cd_setor'])) {
		Error::generateErrorApi(Error::NOT_AUTHORIZED);
	}

	$loginDAO = new LoginDAO();
	$token = empty($token) ? '' : $token[0];  
	$wasChanged = $loginDAO->changeOfSystem($token, $args['cd_sistema']);
	
	return $res->withJson(['foi_alterado' => $wasChanged]);
});

