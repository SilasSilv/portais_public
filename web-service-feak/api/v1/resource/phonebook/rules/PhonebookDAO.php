<?php 

namespace api\v1\resource\phonebook\rules;

use \api\v1\vendor\db\Crud;

Class PhonebookDAO extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = 'tb_agenda_telefonica';
		$this->fields = "a.nr_sequencia, a.ds_contato, a.nr_telefone, COALESCE(a.ds_observacao, '') AS ds_observacao, a.cd_tipo_contato, c.ds_categoria AS ds_tipo_contato";
		$this->fieldFromTo = ["ds_tipo_contato" => "c.ds_categoria AS ds_tipo_contato", "ds_observacao"  => "COALESCE(a.ds_observacao, '') AS ds_observacao"];
        $this->join = "INNER JOIN tb_categoria c ON a.cd_tipo_contato = c.cd_categoria";
        $this->sortFields = [["field" => "a.ds_contato", "type" => "ASC"], ["field" => "a.nr_telefone", "type" => "ASC"]];
	}

	public function findContact($params) 
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
        $params["table"] = ["name" => $this->table, "aliases" => "a"];
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
		$i = 0;

		if (array_key_exists('cd_tipo_contato',  $params)) {
			$where['cd_tipo_contato'] = $this->mountPartWhere("", "a", "cd_tipo_contato", "IN", $params['cd_tipo_contato'], $i);
			$i++;
		}

		if (array_key_exists('name_or_fone',  $params)) {
			$where['ds_contato'] = $this->mountPartWhere("AND", "a", "ds_contato", "LIKE", "%" . $params['name_or_fone'] . "%", $i, 'left');
			$where['nr_telefone'] = $this->mountPartWhere("OR", "a", "nr_telefone", "LIKE", "%" . $params['name_or_fone'] . "%", ++$i, 'right');
			$i++;
		}

		return $where;
	}

	public function registerContact($contact)
	{			
		return parent::create($contact, ["showId" => true]);
	}

	public function updateContact($nr_sequencia, $contact)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia
					]
				];

		return parent::update($contact, $where);
	}

	public function deleteContact($nr_sequencia)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia
					]
				];

		return parent::delete($where);
	}
}