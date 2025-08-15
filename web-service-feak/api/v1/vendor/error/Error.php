<?php 

namespace api\v1\vendor\error;

use \api\v1\vendor\error\LogError;

Class Error {

	//Erros gerais da API

	const JSON_SYNTAX_ERROR = ["message" => "JSON syntax error",
				 			   "status" => 400];

	const NOT_AUTHORIZED = ["message" => "Not authorized",
				 			"status" => 403];

	const SESSION_EXPIRED = ["message" => "Session expired",
				 			 "status" => 401];	

	const REQUIRED_FIELD = ["message" => "Required",
				 			"status" => 422];

	const INTERNAL_ERROR = ["message" => "Internal API error, sorry for the inconvenience",
						 	"status" => 500];

	//Erros gerais do banco de dados

	const DB_NOT_EXISTS_FIELD = ["message" => "Some field passed in parameter does not exist",
								 "status" => 404];

	const DB_NO_REFERENCES_PK = ["message" => "Some past value there are no references in the primary key",
								 "status" => 404];

	const DB_DUPLICATE_VALUES = ["message" => "Duplicate values",
								 "status" => 422];

	const DB_REQUIRED_FIELD = ["message" => "Some required field not informed",
							   "status" => 422];

	const DB_DEPENDENT_ROWS = ["message" => "Cannot delete parent row, has dependent child rows",
							   "status" => 422];

	private static function informHeaderOfError($statusHttp) 
	{

		$statusHttpNotExist = false;

		header('Content-Type: application/json;charset=utf-8');

		switch($statusHttp) {
			case 400:
				header('HTTP/1.1 400 Bad Request');
				break;
			case 401:
				header('HTTP/1.1 401 Unauthorized');
				break;
			case 403:
				header('HTTP/1.1 403 Forbidden');
				break;
			case 404: 
				header('HTTP/1.1 404 Not Found');
				break;
			case 406:
				header('HTTP/1.1 406 Not Acceptable');
				break;
			case 422:
				header('HTTP/1.1 422 Unprocessable Entity');
				break;
			case 500:
				header('HTTP/1.1 500 Internal Server Error');
				break;
			default: 
				$statusHttpNotExist = true;
		}	

		return $statusHttpNotExist;	

	} 

	public static function generateErrorApi($error, $params=[]) 
	{
		if (!array_key_exists('message', $error) && !array_key_exists('status', $error)) {

			$error = self::INTERNAL_ERROR;

		}

		if (array_key_exists('observation', $params)) {

			$error['message'] .= $params['observation'];

		}

		self::informHeaderOfError($error["status"]);	 
	    		
		echo json_encode(["message_error" => $error["message"]]);

		die();

	}

	public static function generateErrorDb($exception) 
	{

		switch ($exception->getCode()) {
			case '42S22':

				if (stripos($exception->getMessage(), '1054')) {

					$error = self::DB_NOT_EXISTS_FIELD;

				} else {

					$error = self::INTERNAL_ERROR;

				}
				
				break;	

			case '23000':

				if (stripos($exception->getMessage(), '1452')) {

					$error = self::DB_NO_REFERENCES_PK;

				} elseif (stripos($exception->getMessage(), '1062')) {

					$error = self::DB_DUPLICATE_VALUES;

				} elseif (stripos($exception->getMessage(), '1048')) {

					$error = self::DB_REQUIRED_FIELD;

				} elseif (stripos($exception->getMessage(), '1451')) {

					$error = self::DB_DEPENDENT_ROWS;

				} else {

					$error = self::INTERNAL_ERROR;

				}

				break;

			case 'HY000':

				if (stripos($exception->getMessage(), '1364')) {

					$error = self::DB_NO_REFERENCES_PK;

				} else {

					$error = self::INTERNAL_ERROR;

				}
				
				break;

			case '42000':
			case '42S02':
			default:

				$error = self::INTERNAL_ERROR;

				break;
		}

		$errorData = ["message" => $exception->getMessage(),
					  "file" => $exception->getFile(),
					  "line" => $exception->getLine()];

		if ($error["status"] == 500) {

			LogError::generateLogErrorCritical('DataBase', $errorData);

		} else {

			LogError::generateLogErrorWarning('DataBase', $errorData);

		}

		self::informHeaderOfError($error["status"]);	 
	    		
		echo json_encode(["message_error" => $error["message"]]);

		die();

	}

	public static function generateErrorCustom($msgError, $statusHttp) 
	{

		$statusHttpNotExist = self::informHeaderOfError($statusHttp);

		if ($statusHttpNotExist) {

			self::generateErrorApi(self::INTERNAL_ERROR);	

		}

		echo json_encode(["message_error" => $msgError]);

		die();
		
	}

}