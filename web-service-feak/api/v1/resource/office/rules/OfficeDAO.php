<?php
namespace api\v1\resource\office\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class OfficeDAO extends Crud 
{
	public function __construct()
	{	
		parent::__construct();
		$this->table = 'cargo';
		$this->sortFields = [["field" => "c.ds_cargo", "type" => "ASC"]];
	}

	public function find($params = []) 
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields"] = $this->fields;
		
		$params["table"] = ["name" => $this->table, "aliases" => "c"];
		$params["where"] = $this->mountWhere($params);

		$params["sort_fields_filter"]["fields"] = array_key_exists('sort_fields', $params) ? $params["sort_fields"] : "";
		$params["sort_fields"] = $this->sortFields;

		return parent::readNew($params);
	}

	private function mountWhere($params)
	{
		$where = [];
		$logicalOp = "AND";
		$i = 0;

		foreach ($params as $key => $value) {
			switch($key) {
				case 'cd_cargo': $where['cd_cargo'] = $this->mountPartWhere($logicalOp, "c", $key, "IN", $value, $i); 
					break;
				case 'ds_cargo': $where['ds_cargo'] = $this->mountPartWhere($logicalOp, "c", $key, "LIKE", "$value%", $i); 
					break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}

	public function register($office)
	{
		$office["cd_cargo"] = $this->generateSequence();

		if (parent::create($office)) {
			return $office["cd_cargo"];
		} else {
			return -1;
		}		
	}

	public function generateSequence()
	{	
		$params = ["fields" => " MAX(c.cd_cargo) sequencia", "table" => ["name" => $this->table, "aliases" => "c"], "pagination" => "ALL"];
		$sequence	= parent::readNew($params)[0]['sequencia'];

		return ++$sequence;
	}

	public function updateOffice($cd_cargo, $office)
	{
		$where = [ [ "field" => "cd_cargo", "operator" => "=", "value" => $cd_cargo ] ];

		return parent::update($office, $where);
	}

	public function deleteOffice($cd_cargo)
	{
		$where = [ [ "field" => "cd_cargo", "operator" => "=", "value" => $cd_cargo ] ];
		
		return parent::delete($where);
	}
}