<?php
namespace api\v1\resource\sector\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class SectorDAO extends Crud 
{
	public function __construct()
	{	
		parent::__construct();
		$this->table = 'setores';
		$this->fields = "s.cd_setor, s.ds_setor, s.dt_inclusao";
		$this->sortFields = [["field" => "s.ds_setor", "type" => "ASC"]];
	}

	public function find($params = []) 
	{
		$sector = $this->findSector($params);

		if (array_key_exists('mostrar_cartoes', $params) && $params['mostrar_cartoes'] == 'N') {
			return $sector;
		}

		for ($i=0; $i<count($sector); $i++) {
			if (array_key_exists('cd_setor', $sector[$i])) {
				$sector[$i]["cartoes"] = $this->findCard(["cd_setor" => $sector[$i]["cd_setor"]]);
			}
		}

		return $sector;
	}

	private function findSector($params) {
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

	private function findCard($params) {
		$params["fields"] = "c.nr_cartao, COALESCE(c.ie_passe_livre_catraca, 'N') AS ie_passe_livre_catraca, COALESCE(c.ie_situacao, 'I') AS ie_situacao";
		
		$params["table"] = ["name" => "tb_cartao", "aliases" => "c"];
		$params["where"] = $this->mountWhere($params, "card");

		return parent::readNew($params);
	}

	private function mountWhere($params, $fontData)
	{
		$where = [];
		$logicalOp = "AND";
		$i = 0;

		if ($fontData == "sector") {
			foreach ($params as $key => $value) {
				switch($key) {
					case 'cd_setor': $where['cd_setor'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", $value, $i); 
						break;
					case 'ds_setor': $where['ds_setor'] = $this->mountPartWhere($logicalOp, "s", $key, "LIKE", "%$value%", $i); 
						break;		
					default: $i--;
				}

				$i++;
			}
		} elseif ($fontData == "card") {
			foreach ($params as $key => $value) {
				switch($key) {
					case 'cd_setor': $where['cd_setor'] = $this->mountPartWhere($logicalOp, "c", $key, "=", $value, $i); 
						break;
					default: $i--;
				}

				$i++;
			}
		}		

		return $where;
	}

	public function register($sector)
	{
		$cards = [];

		if (array_key_exists('cartoes', $sector)) {
			$cards = $sector['cartoes'];
			unset($sector['cartoes']);
		}

		$cd_setor = parent::create($sector, ["showId" => true]);
		$this->manipulateCard($cards, $cd_setor);
		
		return $cd_setor;
	}

	public function updateSector($cd_setor, $sector)
	{
		$where = [ ["field"	=> "cd_setor", "operator" => "=", "value" => $cd_setor] ];
		$cards = [];

		if (array_key_exists('cartoes', $sector)) {
			$cards = $sector['cartoes'];
			unset($sector['cartoes']);
		}
		
		$updateSector = parent::update($sector, $where);

		$cd_setor = array_key_exists('cd_setor', $sector) ? $sector['cd_setor'] : $cd_setor;
		$updateCards = $this->manipulateCard($cards, $cd_setor);

		return $updateSector || $updateCards;
	}

	private function manipulateCard($cards, $cd_setor) 
	{
		$this->table = "tb_cartao";
		$update = false;

		foreach ($cards as $value) {
			if (array_key_exists('operacao', $value)) {
				$card = [];
				$where = [ ["field" => "nr_cartao", "operator" => "=", "value" => $value["nr_cartao"]],
						["logical_op" => "AND", "field" => "nr_cracha", "operator" => "IS NULL"] ];

				if ($value["operacao"] == "C") {
					$card = ["cd_setor" => $cd_setor];
					$where[] = ["logical_op" => "AND", "field" => "cd_setor", "operator" => "IS NULL"];
				} elseif ($value["operacao"] == "D") {
					$card = ["cd_setor" => null];
					$where[] = ["logical_op" => "AND", "field" => "cd_setor", "operator" => "=", "value" => $cd_setor];
				}		    

				if (parent::update($card, $where)) {
					$update = true;
				}
			}
		}

		return $update;
	}

	public function deleteSector($cd_setor)
	{
		$where = [ ["field" => "cd_setor", "operator" => "=", "value" => $cd_setor] ];

		return parent::delete($where);
	}
}