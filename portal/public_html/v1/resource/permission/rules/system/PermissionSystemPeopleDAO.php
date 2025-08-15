<?php 
namespace api\v1\resource\permission\rules\system;

use \api\v1\vendor\db\Crud;

class PermissionSystemPeopleDAO extends Crud 
{
    public function __construct()
	{
		parent::__construct();
		$this->table = 'tb_permissao_pf';
		$this->fields = "ppf.cd_permissao, ppf.nr_cracha, pf.nm_pessoa_fisica, ppf.vl_pf";
		$this->fieldFromTo = ["nm_pessoa_fisica" => "pf.nm_pessoa_fisica"];
        $this->join = "INNER JOIN pessoa_fisica pf ON ppf.nr_cracha = pf.nr_cracha
                       INNER JOIN tb_permissao p ON ppf.cd_permissao = p.cd_permissao
                       INNER JOIN tb_sistema s ON s.cd_sistema = p.cd_sistema";
        $this->sortFields = [["field" => "ppf.cd_permissao", "type" => "ASC"], ["field" => "pf.nm_pessoa_fisica", "type" => "ASC"]];
        $this->sortFieldFromTo = ["nm_pessoa_fisica" => "pf.nm_pessoa_fisica"];
    }

    public function find($params) {
        $params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
        $params["table"] = ["name" => $this->table, "aliases" => "ppf"];
        $params["join"] = $this->join;  
        $params['ie_situacao'] = 'A';
        $params["where"] = $this->mountWhere($params);

        $params["sort_fields_filter"]["fields"] = array_key_exists('sort_fields', $params) ? $params["sort_fields"] : "";
        $params["sort_fields_filter"]['from_to'] = $this->sortFieldFromTo;
        $params["sort_fields"] = $this->sortFields;
        
        $params["pagination"] = array_key_exists('pagination', $params) ? $params["pagination"] : 20;

		return parent::readNew($params);
    }

    private function mountWhere($params)
	{
		$where = [];
		$logicalOp = "AND";
		$i = 0;

		foreach ($params as $key => $value) {
			switch($key) {
                case 'cd_sistema': $where['cd_sistema'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", $value, $i); 
					break;
                case 'cd_permissao': $where['cd_permissao'] = $this->mountPartWhere($logicalOp, "ppf", $key, "IN", $value, $i); 
                    break;
                case 'nr_cracha': $where['nr_cracha'] = $this->mountPartWhere($logicalOp, "ppf", $key, "IN", "$value", $i); 
					break;
				case 'nm_pessoa_fisica': $where['nm_pessoa_fisica'] = $this->mountPartWhere($logicalOp, "pf", $key, "LIKE", "%$value%", $i); 
                    break;
                case 'ie_situacao': $where['ie_situacao'] = $this->mountPartWhere($logicalOp, "pf", $key, "=", $value, $i); 
                    break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}
}