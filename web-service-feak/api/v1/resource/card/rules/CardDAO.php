<?php
namespace api\v1\resource\card\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class CardDAO extends Crud 
{
	public function __construct()
	{	
		parent::__construct();
		$this->table = 'tb_cartao';
		$this->fields = "c.nr_cartao, COALESCE(p.nr_cracha, '') AS nr_cracha, COALESCE(p.nm_pessoa_fisica, '') AS nm_pessoa_fisica, 
						 COALESCE(s.cd_setor, '') AS cd_setor, COALESCE(s.ds_setor, '') AS ds_setor, 
						 COALESCE(c.ie_passe_livre_catraca, 'N') AS ie_passe_livre_catraca, c.ie_situacao";
		$this->fieldFromTo = [
								"pessoa" => "COALESCE(p.nr_cracha, '') AS nr_cracha, COALESCE(p.nm_pessoa_fisica, '') AS nm_pessoa_fisica", 
							  	"setor" => "COALESCE(s.cd_setor, '') AS cd_setor, COALESCE(s.ds_setor, '') AS ds_setor", 
							  	"ie_passe_livre_catraca" => "COALESCE(c.ie_passe_livre_catraca, 'N') AS ie_passe_livre_catraca"
							 ];
		$this->join = "LEFT JOIN pessoa_fisica p ON c.nr_cracha = p.nr_cracha LEFT JOIN setores s ON c.cd_setor = s.cd_setor";
		$this->sortFields = [ ["field" => "c.nr_cartao", "type" => "DESC"] ];
		$this->sortFieldFromTo = [
									"pessoa.nr_cracha" => "p.nr_cracha", 
									"pessoa.nm_pessoa_fisica" => "p.nm_pessoa_fisica", 
									"setor.cd_setor" => "s.cd_setor", 
									"setor.ds_setor" => "s.ds_setor"
								];
	}

	public function find($params = []) 
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
		$params["fields"] = $this->fields;
		
		$params["table"] = ["name" => $this->table, "aliases" => "c"];
		$params["join"] = $this->join;
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
				case 'nr_cartao': $where['nr_cartao'] = $this->mountPartWhere($logicalOp, "c", $key, "IN", $value, $i); 
					break;
				case 'nm_pessoa_fisica': $where['nm_pessoa_fisica'] = $this->mountPartWhere($logicalOp, "p", $key, "LIKE", "$value%", $i); 
					break;
				case 'ds_setor': $where['ds_setor'] = $this->mountPartWhere($logicalOp, "s", $key, "LIKE", "$value%", $i); 
					break;
				case 'available':
					if ($value == 'S') {
						$where['nr_cracha'] = $this->mountPartWhere($logicalOp, "p", "nr_cracha", "IS NULL", "", $i);
						$where['cd_setor'] = $this->mountPartWhere($logicalOp, "s", "cd_setor", "IS NULL", "", ++$i); 
					} elseif ($value == 'N') {
						$where['nr_cracha'] = $this->mountPartWhere($logicalOp, "p", "nr_cracha", "IS NOT NULL", "", $i);
						$where['cd_setor'] = $this->mountPartWhere("OR", "s", "cd_setor", "IS NOT NULL", "", ++$i); 
					}
					break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}

	public function register($card)
	{
		if (parent::create($card)) {
			return $card["nr_cartao"];
		}	

		return null; 
	}

	public function updateCard($nr_cartao, $card)
	{

		$where = [[
					"field"	=> "nr_cartao",
					"operator" => "=",
					"value" => $nr_cartao
				 ]];

		return parent::update($card, $where);
	}

	public function deleteCard($nr_cartao)
	{
		$where = [[
					"field" => "nr_cartao",
					"operator" => "=",
					"value" => $nr_cartao
				 ]];

		return parent::delete($where);
	}
}