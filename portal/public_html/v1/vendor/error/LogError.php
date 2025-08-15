<?php 

namespace api\v1\vendor\error;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogError {

	private static $nameLogger = 'api-feak';
	private static $fileLogger = './logs/api-feak.log';

	public static function generateLogError($name, $error=[]) {		
		$logger = new Logger(self::$nameLogger);	

		$logger->pushHandler(new StreamHandler(self::$fileLogger, Logger::ERROR));

		$logger->error($name, $error);	
	}


	public static function generateLogErrorWarning($name, $error=[]) {	

		$logger = new Logger(self::$nameLogger);	

		$logger->pushHandler(new StreamHandler(self::$fileLogger, Logger::WARNING));

		$logger->warning($name, $error);	
	}

	
	public static function generateLogErrorCritical($name, $error=[]) {		
		$logger = new Logger(self::$nameLogger);	

		$logger->pushHandler(new StreamHandler(self::$fileLogger, Logger::CRITICAL));

		$logger->critical($name, $error);	
	}
}	