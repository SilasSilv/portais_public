<?php
namespace api\v1\resource\group\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class GroupDAO extends Crud 
{
	public function __construct()
	{	
		parent::__construct();
		$this->table = 'tb_grupo';
		$this->sortFields = [["field" => "g.nm_grupo", "type" => "ASC"]];
	}

	public function find($params = []) 
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields"] = $this->fields;
		
		$params["table"] = ["name" => $this->table, "aliases" => "g"];
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
				case 'cd_grupo': $where['cd_grupo'] = $this->mountPartWhere($logicalOp, "g", $key, "IN", $value, $i); 
					break;
				case 'nm_grupo': $where['nm_grupo'] = $this->mountPartWhere($logicalOp, "g", $key, "LIKE", "$value%", $i); 
					break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}

	public function register($group)
	{
		return parent::create($group, ["showId" => true]); 
	}

	public function updateGroup($cd_grupo, $group)
	{
		$where = [ [ "field" => "cd_grupo", "operator" => "=", "value" => $cd_grupo ] ];

		return parent::update($group, $where);
	}

	public function deleteGroup($cd_grupo)
	{
		$where = [ [ "field" => "cd_grupo", "operator" => "=", "value" => $cd_grupo ] ];
		
		return parent::delete($where);
	}
}