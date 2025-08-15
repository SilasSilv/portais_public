<?php

use \api\v1\vendor\utils\PasswordUtils;
use PHPUnit\Framework\TestCase;

final class PasswordUtilsTest extends TestCase { 

	public function testPassworWeak()
	{	
		//Números

		$pass = PasswordUtils::checkStrength('123456');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('12345678');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('123456789');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('1234567890');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('123');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('12345');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('321654');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('9876543210');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('2222222');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('1111111111111111111111111');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('11122233355566');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('888899996666332122');
		$this->assertLessThan(30, $pass);


		//Letras minúsculas	

		$pass = PasswordUtils::checkStrength('aaa');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('ashdgakq');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('qwerty');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('qwertyuiop');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('asdfghjklç');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('zxcvbnm');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('aaaaaaaaaaaaaaaaaaaaaaa');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('aaaabbbbbssssswwwww');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('aassddffgghhjj');
		$this->assertLessThan(30, $pass);


		//Letras maiscúlas

		$pass = PasswordUtils::checkStrength('AAA');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('ASDGASFQUWEY');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('QWERTY');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('QWERTYUIOP');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('ASDFGHJKLÇ');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('ZXCVBNM');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('AAAAAAAAAAAAAAAAAAA');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('AAAABBBBSSSSWWWW');
		$this->assertLessThan(30, $pass);


		//Letras (minúsculas/maiscúlas), números e menor que 8

		$pass = PasswordUtils::checkStrength('gabwc45');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('AGBET45');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('12345AV');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('12244ab');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('Ab2');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('Aaaba2');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('AQbt2');
		$this->assertLessThan(30, $pass);


		//1 caraceter especial, letras (minúsculas/maiscúlas), números e menor que 8

		$pass = PasswordUtils::checkStrength('gabcaw$');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('AGBTAW!');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('123456!');
		$this->assertLessThan(30, $pass);


		//N caracter especial e variações de letras, números

		$pass = PasswordUtils::checkStrength('@$!');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('@$123');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('@$avb');
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('@$ABC');
		$this->assertLessThan(30, $pass);
	}

	public function testPasswordMedium()
	{
		//Letras e números menor que 8 caracteres

		$pass = PasswordUtils::checkStrength('123ASC');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('123asv');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('123AVeb');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('abd1235');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('ABD235');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('123765aqwetbqsv');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('123765AQWETBQSV');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);


		//Caracter especial e (letras, números) menor que 8 caracteres

		$pass = PasswordUtils::checkStrength('gabc45$');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('AGBT45!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('aGBT45!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%abr3c!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABR3C!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABr3c!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);


		//Letras e números maior ou igual que 8 caracteres

		$pass = PasswordUtils::checkStrength('123ASCWADS');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('123asasdwv');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('abdfeq1235');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('ABAD1235');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('abad1235');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);


		//Caracter especial e (letras, números) maior ou igual que 8 caracteres

		$pass = PasswordUtils::checkStrength('%abrwq3c!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABRWQ3C!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%123546789!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%123546789');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABat43!');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABat432');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABaat32');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABTaat32');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('%ABTat32');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);

		$pass = PasswordUtils::checkStrength('78912daASdas');
		$this->assertGreaterThan(29, $pass);
		$this->assertLessThan(60, $pass);		
	}

	public function testPasswordStrong()
	{
		//Letras e números menor que 8 caracteres

		$pass = PasswordUtils::checkStrength('12446278ewqASC');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('128543AVRGWebhf');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('128AVRebf');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('78912daASres');
		$this->assertGreaterThan(59, $pass);


		//Caracter especial e (letras, números)

		$pass = PasswordUtils::checkStrength('gabcEas45$');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('AGBTaas45!');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('%ABrr323c!');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('aGBTwa45!');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('%abr623c!!');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('%ABart432');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('%ABTeat32');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('AGBTaas45!');
		$this->assertGreaterThan(59, $pass);

		$pass = PasswordUtils::checkStrength('%ABat432905617');
		$this->assertGreaterThan(59, $pass);
	}

	public function testePasswordSuperStrong()
	{
		$pass = PasswordUtils::checkStrength('&Y@BTiol459*63&*');
		$this->assertGreaterThan(99, $pass);		
	}

	public function testePasswordAsLoginOrName()
	{
		$pass = PasswordUtils::checkStrength('%ABat432905617teste', ['teste']);
		$this->assertLessThan(30, $pass);

		$pass = PasswordUtils::checkStrength('%ABat432906175555', ['5555']);
		$this->assertLessThan(30, $pass);
	}

}