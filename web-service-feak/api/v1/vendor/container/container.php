<?php

use \api\v1\vendor\error\Error;
use \api\v1\vendor\error\LogError;
use \api\v1\vendor\session\Session;

$c = $app->getContainer();

//Verificar autenticação do usuário
$c['checkAuthenticated'] = function($c) {	
	return function ($token, $update_password=false) {
		$token = $token[0] ?? '';		
		$session = new Session();
		$sessionData = $session->getSession($token);
		
		if (!is_array($sessionData) || ($sessionData['ie_alterar_senha'] == 'S' && !$update_password)) {
			Error::generateErrorApi(Error::NOT_AUTHORIZED);
		} elseif (time() > $sessionData['expire_unix']) {
			$session->destroySession($token);
			Error::generateErrorApi(Error::SESSION_EXPIRED);
		} 

		return $sessionData;
	};	
};

//Tratamento de Erros Geral da API 
$c['notFoundHandler'] = function ($c) {
	return function ($req, $res) use ($c) {

		LogError::generateLogError(
				'NotFoundHandler',
				[													
					"path" => $req->getUri()->getPath()									
				]
			);

		return $c['response']			
			->withJson([
					"message_error" => "Route does not exist"							
				])
			->withStatus(404);
	};
};

$c['notAllowedHandler'] = function ($c) {
	return function ($req, $res, $method) use ($c) {

		LogError::generateLogError(
				'NotAllowedHandler',
				[													
					"path" => $req->getUri()->getPath(),
					"method" => $method									
				]
			);

		return $c['response']			
			->withJson([ 
					"message_error" => "Route method does not exist"
				])
			->withStatus(405);
	};
};

$c['errorHandler'] = function ($c) {
	return function ($req, $res, $exception) use ($c) {

		LogError::generateLogErrorCritical(
				'ErrorHandler',
				[
					"message" => $exception->getMessage(),
					"file" => $exception->getFile(),
					"line" => $exception->getLine()											
				]
			);

		return $c['response']			
			->withJson([
					"message_error" => "Internal API error, sorry for the inconvenience"
				])
			->withStatus(500);
	};
};

$c['phpErrorHandler'] = function ($c) {
    return function ($req, $res, $error) use ($c) {

    	LogError::generateLogErrorCritical(
				'PhpErrorHandler',
				[
					"message" => $error->getMessage(),
					"file" => $error->getFile(),
					"line" => $error->getLine()											
				]
			);

        return $c['response']
            ->withJson([
					"message_error" => "Internal API error, sorry for the inconvenience"
				])
			->withStatus(500);
    };
};