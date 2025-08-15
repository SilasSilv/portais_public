<?php
namespace api\v1\resource\session\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class SessionDAO extends Crud 
{
	public function __construct()
	{	
		parent::__construct();
		$this->table = 'tb_sessao';
		$this->fields = "s.nr_sequencia, s.cd_sistema, si.nm_sistema, s.nr_cracha, p.nm_pessoa_fisica, s.ip, DATE_FORMAT(s.dt_inicio, '%d/%m/%Y %H:%i') AS dt_inicio, DATE_FORMAT(s.expire, '%d/%m/%Y %H:%i') AS expire";
        $this->fieldFromTo = [
            "dt_inicio" => "DATE_FORMAT(s.dt_inicio, '%d/%m/%Y %H:%i') AS dt_inicio", 
            "expire" => "DATE_FORMAT(s.expire, '%d/%m/%Y %H:%i') AS expire"
         ];
        $this->join = "INNER JOIN pessoa_fisica p ON s.nr_cracha = p.nr_cracha INNER JOIN tb_sistema si ON s.cd_sistema = si.cd_sistema";
        $this->sortFields = [["field" => "s.nr_sequencia", "type" => "DESC"]];
        $this->sortFieldFromTo = [
            "dt_inicio" => "s.dt_inicio", 
            "expire" => "s.expire"
        ];
	}

	public function find($params) {
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
		$params["fields"] = $this->fields;
		
		$params["table"] = ["name" => $this->table, "aliases" => "s"];
		$params["join"] = $this->join;
		$params["where"] = $this->mountWhere($params, "sector");

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
                case 'cd_setor': $where['nr_sequencia'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", $value, $i); 
                    break;
                case 'cd_sistema': $where['cd_sistema'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", "$value", $i); 
                    break;
                case 'nm_sistema': $where['nm_sistema'] = $this->mountPartWhere($logicalOp, "si", $key, "LIKE", "%$value%", $i); 
                    break;
                case 'nr_cracha': $where['nr_cracha'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", "$value", $i); 
                    break;
                case 'nm_pessoa_fisica': $where['nm_pessoa_fisica'] = $this->mountPartWhere($logicalOp, "p", $key, "LIKE", "%$value%", $i); 
                    break;
                case 'logado': 
                    if ($value == 'S') {
                        $where['dt_fim'] = $this->mountPartWhere($logicalOp, "s", "dt_fim", "IS NULL", "", $i);
                    } elseif ($value == 'N') {
                        $where['dt_fim'] = $this->mountPartWhere($logicalOp, "s", "dt_fim", "IS NOT NULL", "", $i);
                    }
                    break;		
                default: $i--;
            }

            $i++;
        }

		return $where;
	}
    
    public function amount()
    {
        $parmas = [];
        $return = [];

        $params["fields"] = "COUNT(1)";
        $params["table"] = ["name" => "tb_sessao", "aliases" => "s"];
        $params["where"] = $this->mountWhere(["logado" => "S"]);
        $params["return_type"] = \PDO::FETCH_BOTH;        
        $return["logados"] = parent::readNew($params)[0][0];

        unset($params["where"]);
        $return["acessos"] = parent::readNew($params)[0][0];

        return $return;
    }

    public function close($nr_sequencia)
    {
        $dt_fim = new \DateTime();

        $where = [[
            "field"	=> "nr_sequencia",
            "operator" => "=",
            "value" => $nr_sequencia
        ],[
            "logical_op" => "AND",
            "field"	=> "dt_fim",
            "operator" => "IS NULL"
        ]];

        return parent::update(["dt_fim" => $dt_fim->format('Y-m-d H:i:s')], $where);
    }
}