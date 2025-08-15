<?php

namespace api\v1\vendor\utils;

class ClearData {

	public static function stringSQL($string) 
	{
		return str_replace([';', '#', '\\', '/', ' ', '@', '(', ')'], '', $string);
	}

}