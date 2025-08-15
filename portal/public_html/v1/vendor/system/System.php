<?php 

namespace api\v1\vendor\system;

use \api\v1\vendor\db\Crud;

Class System extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = "tb_sistema";
	}

	public function findSystem($params)
	{	
		if (array_key_exists('cd_sistema', $params)) {

			$where = [
				[
					"field" => "cd_sistema",
					"operator" => "=",
					"value" => $params['cd_sistema']
				]
			];


		} elseif (array_key_exists('cd_token', $params)) {

			$where = [
				[
					"field" => "cd_token",
					"operator" => "=",
					"value" => $params['cd_token']
				]
			];


		} else {

			return [];

		}

		return parent::read($where)[0] ?? [];
	}

	public function existsSystem($cd_sistema) 
	{
		$where = array(
				array(
					"field" => "cd_sistema",
					"operator" => "=",
					"value" => $cd_sistema
				)				
			);

		$result = parent::read($where,"COUNT(1) AS exists_system")[0]; 

		if ($result['exists_system'] == 1) return true;
		else return false;
	}
	
}