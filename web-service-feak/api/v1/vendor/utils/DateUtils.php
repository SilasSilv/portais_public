<?php

namespace api\v1\vendor\utils;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\error\LogError;

Class DateUtils {

	public static function convert($date, $type, $params=[])
	{
		if ($type == "BR-DATETIME") {

			if (preg_match('/\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}:\d{2}/', $date)) {
				$date = \DateTime::createFromFormat('d/m/Y H:i:s', $date);

				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}
			}

		} elseif ($type == "BR-DATE") {

			if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $date)) {
				$date = \DateTime::createFromFormat('d/m/Y', $date);		
			
				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}	
			}

		} elseif ($type == "EUA-DATETIME") {

			if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $date)) {
				$date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);		
			
				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}
			}

		} elseif ($type == "EUA-DATE") {
			
			if (preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
				$date = \DateTime::createFromFormat('Y-m-d', $date);	
			
				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}	
			}
			
		} elseif ($type == "BR-MONTH-YEAR") {				

			if (preg_match('/\d{2}\/\d{4}/', $date)) {
					
				$date = \DateTime::createFromFormat('m/Y', $date);
			
				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}
			}

		} elseif ($type == "EUA-MONTH-YEAR") {

			if (preg_match('/\d{4}-\d{2}/', $date)) {
				$date = \DateTime::createFromFormat('Y-m', $date);
			
				if ($date) {				
					return array_key_exists('format', $params) ? $date->format($params['format']) : $date;
				}	
			}
			
		} else {
			$errorData = ['message' => 'Type format date not exists', 'file' => 'DateUtils'];
			LogError::generateLogErrorCritical('Utils', $errorData);
			Error::generateErrorApi(Error::INTERNAL_ERROR);
		}	 
		
		if (array_key_exists('name_field', $params)) {
			Error::generateErrorCustom("Field {$params['name_field']}: Date invalid", 422);
		} else {
			Error::generateErrorCustom("Field ?: Date invalid", 422);
		}					
	}

}