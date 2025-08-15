<?php
namespace api\v1\resource\system\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\UrlUtils;

class SystemDAO extends Crud 
{
	public function __construct()
	{	
        parent::__construct();
        $this->fields = "cd_sistema, nm_sistema, ds_sistema, cd_token, ie_situacao, CONCAT('" . UrlUtils::getUrl() . "', img_logo) AS img_logo";
        $this->fieldFromTo = ["img_logo" => "CONCAT('" . UrlUtils::getUrl() . "', img_logo) AS img_logo"];
		$this->table = 'tb_sistema';
        $this->sortFields = [["field" => "s.nm_sistema", "type" => "ASC"]];
	}

	public function find($params) {
        $params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
		$params["table"] = ["name" => $this->table, "aliases" => "s"];
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
                case 'cd_sistema': $where['cd_sistema'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", "$value", $i); 
                    break;
                case 'nm_sistema': $where['nm_sistema'] = $this->mountPartWhere($logicalOp, "s", $key, "LIKE", "%$value%", $i); 
                    break;
                case 'ie_situacao': $where['ie_situacao'] = $this->mountPartWhere($logicalOp, "s", $key, "IN", "$value", $i); 
                    break;	
                default: $i--;
            }

            $i++;
        }

		return $where;
	}
	
	public function register($system) {
		return parent::create($system, ["showId" => true]);
	}

	public function updateSystem($cd_sistema, $system)
	{
		$where = [[
					"field" => "cd_sistema",
					"operator" => "=",
					"value" => $cd_sistema
				]];

		if (array_key_exists("img_logo_delete", $system)) {
			$img_logo_delete = str_replace(UrlUtils::getUrl(), '', $system["img_logo_delete"]);
			unset($system["img_logo_delete"]);
		} else {
			$img_logo_delete = NULL;
		}

		$result = parent::update($system, $where);

		if (file_exists($img_logo_delete)) {
			unlink($img_logo_delete);
		}

		return $result;
	}

	public function deleteSystem($cd_sistema)
	{
		$where = [[
					"field" => "cd_sistema",
					"operator" => "=",
					"value" => $cd_sistema
				]];

		$img_logo_delete = $this->find(["cd_sistema" => $cd_sistema, "fields" => "img_logo"]);
		$img_logo_delete = count($img_logo_delete) == 1 ? $img_logo_delete[0]["img_logo"] : NULL;
		$img_logo_delete = str_replace(UrlUtils::getUrl(), '', $img_logo_delete);

		$result = parent::delete($where);

		if (file_exists($img_logo_delete)) {
			unlink($img_logo_delete);
		}
		
		return $result;
	}
}