<?php
namespace api\v1\vendor\utils;

class StringUtils 
{
	public static function pretty($str, $regex='') {
		if (!empty($regex)) {
			preg_match($regex, $str, $strTemp);
			$str = $strTemp[0] ?? '';
		}

		return ucwords(mb_strtolower($str));
	} 
}