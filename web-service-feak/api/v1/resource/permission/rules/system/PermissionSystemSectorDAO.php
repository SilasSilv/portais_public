<?php 
namespace api\v1\resource\permission\rules\system;

use \api\v1\vendor\db\Crud;

class PermissionSystemSectorDAO extends Crud 
{
    public function __construct()
	{
		parent::__construct();
		$this->table = 'tb_permissao_setor';
		$this->fields = "ps.cd_permissao, ps.cd_setor, s.ds_setor, ps.vl_setor";
		$this->fieldFromTo = ["ds_setor" => "s.ds_setor"];
        $this->join = "INNER JOIN setores s ON ps.cd_setor = s.cd_setor
                       INNER JOIN tb_permissao p ON ps.cd_permissao = p.cd_permissao
                       INNER JOIN tb_sistema si ON p.cd_sistema = si.cd_sistema";
        $this->sortFields = [["field" => "ps.cd_permissao", "type" => "ASC"], ["field" => "s.ds_setor", "type" => "ASC"]];
        $this->sortFieldFromTo = ["ds_setor" => "s.ds_setor"];
    }

    public function find($params) {
        $params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
        $params["table"] = ["name" => $this->table, "aliases" => "ps"];
        $params["join"] = $this->join;
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
                case 'cd_sistema': $where['cd_sistema'] = $this->mountPartWhere($logicalOp, "si", $key, "IN", $value, $i); 
					break;
                case 'cd_permissao': $where['cd_permissao'] = $this->mountPartWhere($logicalOp, "ps", $key, "IN", $value, $i); 
                    break;
                case 'cd_setor': $where['cd_setor'] = $this->mountPartWhere($logicalOp, "ps", $key, "IN", "$value", $i); 
					break;
				case 'ds_setor': $where['ds_setor'] = $this->mountPartWhere($logicalOp, "s", $key, "LIKE", "%$value%", $i); 
                    break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}
}