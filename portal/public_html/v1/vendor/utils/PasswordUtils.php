<?php
namespace api\v1\vendor\utils;

class PasswordUtils {

	public static function checkStrength($password, $params=[])
	{
		$pass = new PasswordUtils();
		$power = 0;		

		$power += $pass->addPoint($password);
		$power -= $pass->subtractPoint($password, $params);

		return $power;
	}

	private function analyzeRepeatedChar($password)
	{
		$amountTotal = 0;
		$character = [];

		for($i=0; $i<strlen($password); $i++) {

			if(!array_search($password{$i}, $character)){
				array_push($character, $password{$i});
			} else {
				$amountTotal++;
			}		

		}

		return $amountTotal;
	}

	private function addPoint($password)
	{
		preg_match_all('/[a-z]{1,}/', $password, $letterLowercase);
		preg_match_all('/[A-Z]{1,}/', $password, $letterUppercase);
		preg_match_all('/\d{1,}/', $password, $number);
		preg_match_all('/\W{1,}/', $password, $specialChar);	 
		$amoutLetterLowercase = 0;
		$amoutLetterUppercase = 0;
		$amoutNumber = 0;
		$amoutSpecialChar = 0;
		$power = 0;

		foreach ($letterLowercase[0] as  $value) {
			if (isset($value)) {
				$amoutLetterLowercase += mb_strlen($value);
			}
		}

		foreach ($letterUppercase[0] as $value) {
			if (isset($value)) {
				$amoutLetterUppercase += mb_strlen($value);
			}		
		}

		foreach ($number[0] as $value) {
			if (isset($value)) {
				$amoutNumber += strlen($value);
			}
		}

		foreach ($specialChar[0] as $value) {
			if (isset($value)) {
				$amoutSpecialChar += mb_strlen($value);
			}
		}

		if (strlen($password) >= 8 && strlen($password) < 10)  {
			$power += 10;
		} else if (strlen($password) >= 10 && strlen($password) <= 12) {
			$power += 15;
		} else if (strlen($password) >= 13 && strlen($password) <= 15) {
			$power += 20;
		} else if (strlen($password) > 15) {
			$power += 25;
		}

		if ($amoutLetterLowercase >= 3) {
			$power += 17;
		} else if ($amoutLetterLowercase >= 1) {
			$power += 10;
		}

		if ($amoutLetterUppercase >= 3) {
			$power += 18;
		} else if ($amoutLetterUppercase >= 1) {
			$power += 10;
		}

		if ($amoutNumber >= 3) {
			$power += 18;
		} else if ($amoutNumber >= 1) {
			$power += 10;
		}

		if ($amoutSpecialChar >= 6) {
			$power += 40;
		} else if ($amoutSpecialChar >= 5) {
			$power += 30;
		} else if ($amoutSpecialChar >= 4) {
			$power += 25;
		} else if ($amoutSpecialChar >= 3) {
			$power += 20;
		} else if ($amoutSpecialChar >= 2) {
			$power += 15;
		} else if ($amoutSpecialChar == 1) {
			$power += 10;
		}

		return $power;
	}

	private function subtractPoint($password, $params)
	{
		$poweNegativer = 0;
		$sequentialPasswords = '/^(ASDFGHJKLÇ|asdfghjklç)$/';
		
		if (preg_match($sequentialPasswords, $password)) {
			$poweNegativer += 35;
		}

		if (strlen($password) < 7) {
			$poweNegativer += 5;
		}

		if (strlen($password) >= 6 && preg_match('/^[0-9]+$/', $password)) {
			$poweNegativer += 35;
		}

		if (strlen($password) >= 6 && preg_match('/^[a-z]+$/', $password)) {
			$poweNegativer += 35;
		}

		if (strlen($password) >= 6 && preg_match('/^[A-Z]+$/', $password)) {
			$poweNegativer += 30;
		}

		foreach ($params as $value) {
			
			if (preg_match('/'.$value.'/i', $password)) {
				$poweNegativer += 50;
			}

		}

		preg_match_all('/([a-zA-z0-9\s\W])\1{1,1}/', $password, $consecutiveCharacters);		
		$amountConsecutiveChar = count($consecutiveCharacters[0]);
		$repeatedCharTotal = $this->analyzeRepeatedChar($password);

		$poweNegativer += count($consecutiveCharacters[0]) * 5;		
		$poweNegativer += $repeatedCharTotal * 2;

		return $poweNegativer;
	}
}
