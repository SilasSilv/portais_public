<?php 
namespace api\v1\resource\category\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class CategoryTypeDAO extends Crud 
{
    public function __construct()
	{	
        parent::__construct();
		$this->table = 'tb_tipo_categoria';
        $this->sortFields = [["field" => "c.ds_tipo_categoria", "type" => "ASC"]];
	}

	public function find($params) {
        $params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
        $params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
		$params["table"] = ["name" => $this->table, "aliases" => "c"];
        $params["where"] = $this->mountWhere($params);

        $params["sort_fields_filter"]["fields"] = array_key_exists('sort_fields', $params) ? $params["sort_fields"] : "";
        $params["sort_fields_filter"]['from_to'] = $this->sortFieldFromTo;
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
                case 'cd_tipo_categoria': $where['cd_tipo_categoria'] = $this->mountPartWhere($logicalOp, "c", $key, "IN", "$value", $i); 
                    break;
                case 'ds_tipo_categoria': $where['ds_tipo_sistema'] = $this->mountPartWhere($logicalOp, "c", $key, "LIKE", "%$value%", $i); 
                    break;
                default: $i--;
            }

            $i++;
        }

		return $where;
	}
    
    public function register($category_type)
	{
		return parent::create($category_type, ["showId" => true]); 
	}

	public function updateCategoryType($cd_tipo_categoria, $category_type)
	{
		$where = [["field" => "cd_tipo_categoria", "operator" => "=", "value" => $cd_tipo_categoria]];

		return parent::update($category_type, $where);
	}

	public function deleteCategoryType($cd_tipo_categoria)
	{
		$where = [["field" => "cd_tipo_categoria", "operator" => "=", "value" => $cd_tipo_categoria]];
		
		return parent::delete($where);
	}
}