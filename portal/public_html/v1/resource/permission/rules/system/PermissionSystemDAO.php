<?php 
namespace api\v1\resource\permission\rules\system;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\utils\UrlUtils;
use \api\v1\resource\permission\rules\PermissionUtils;

class PermissionSystemDAO extends Crud 
{
    public function __construct()
	{
		parent::__construct();
		$this->table = 'tb_permissao';
		$this->fields = "p.cd_permissao, p.cd_sistema, p.cd_tipo_permissao, c.ds_categoria AS ds_tipo_categoria, p.ds_titulo, p.ds_descricao, p.vl_padrao,
			p.ie_mostrar_cliente, COALESCE(p.ds_descricao_cliente, '') AS ds_descricao_cliente, p.ie_mostrar_parametro, ie_situacao,
			COALESCE(p.url_acesso, '') AS url_acesso, COALESCE(p.nm_apelido_acesso, '') AS nm_apelido_acesso,
			CASE WHEN logo_alternativo_acesso IS NOT NULL THEN CONCAT('" . UrlUtils::getUrl() . "', logo_alternativo_acesso) ELSE '' END AS logo_alternativo_acesso";
		$this->fieldFromTo = ["ds_tipo_categoria" => "c.ds_categoria AS ds_tipo_categoria", 
			"ds_descricao_cliente" => "COALESCE(p.ds_descricao_cliente, '') AS ds_descricao_cliente",
			"url_acesso" => "COALESCE(p.url_acesso, '') AS url_acesso",
			"nm_apelido_acesso" => "COALESCE(p.nm_apelido_acesso, '') AS nm_apelido_acesso",
			"logo_alternativo_acesso" => "CASE WHEN logo_alternativo_acesso IS NOT NULL THEN CONCAT('" . UrlUtils::getUrl() . "', logo_alternativo_acesso) ELSE '' END AS logo_alternativo_acesso"];
		$this->join = "INNER JOIN tb_categoria c ON p.cd_tipo_permissao = c.cd_categoria";
        $this->sortFields = [["field" => "c.ds_categoria", "type" => "ASC"], ["field" => "p.ds_descricao", "type" => "ASC"]];
        $this->sortFieldFromTo = ["ds_tipo_categoria" => "c.ds_categoria"];
    }

    public function find($params) {
        $params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
        $params["fields"] = $this->fields;
        
        $params["table"] = ["name" => $this->table, "aliases" => "p"];
        $params["join"] = $this->join;
        $params["where"] = $this->mountWhere($params);

        $params["sort_fields_filter"]["fields"] = array_key_exists('sort_fields', $params) ? $params["sort_fields"] : "";
        $params["sort_fields_filter"]['from_to'] = $this->sortFieldFromTo;
        $params["sort_fields"] = $this->sortFields;
        
        $params["pagination"] = array_key_exists('pagination', $params) ? $params["pagination"] : "ALL";

		return parent::readNew($params);
	}

    private function mountWhere($params)
	{
		$where = [];
		$logicalOp = "AND";
		$i = 0;

		foreach ($params as $key => $value) {
			switch($key) {
                case 'cd_sistema': $where['cd_sistema'] = $this->mountPartWhere($logicalOp, "p", $key, "IN", $value, $i); 
					break;
				case 'cd_tipo_permissao': $where['cd_tipo_permissao'] = $this->mountPartWhere($logicalOp, "p", $key, "IN", $value, $i); 
                    break;
                case 'cd_permissao': $where['cd_permissao'] = $this->mountPartWhere($logicalOp, "p", $key, "IN", $value, $i); 
					break;
				case 'ds_titulo': $where['ds_titulo'] = $this->mountPartWhere($logicalOp, "p", $key, "LIKE", "%$value%", $i); 
                    break;
                case 'ie_situacao': $where['ie_situacao'] = $this->mountPartWhere($logicalOp, "p", $key, "IN", "$value", $i); 
					break;
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}

	public function register($permission)
	{
		$cd_permissao = parent::create($permission, ["showId" => true]);

		if ($cd_permissao) {
			PermissionUtils::actionPermission($cd_permissao, ['action' => 'mass', 'value' => $permission['vl_padrao']]);
		}

		return $cd_permissao;
	}

	public function registerImage($cd_permissao, $image) 
	{
		$permssion = $this->find(["cd_permissao" => $cd_permissao, "fields" => "logo_alternativo_acesso"]);
		
		if (! empty($permssion)) {
			$path_image = str_replace(UrlUtils::getUrl(), '', $permssion[0]["logo_alternativo_acesso"]);
			if (file_exists($path_image)) {
				unlink($path_image);
			}
		}

		$where = [
			["field" => "cd_permissao", "operator" => "=", "value" => $cd_permissao],
			["logical_op" => "AND", "field" => "cd_tipo_permissao", "operator" => "=", "value" => "68"]
		];
		return parent::update($image, $where);
	}

	public function updatePermission($cd_permissao, $permission)
	{
		$where = [["field" => "cd_permissao", "operator" => "=", "value" => $cd_permissao]];
		$result = parent::update($permission, $where);

		if ($result) {
			PermissionUtils::actionPermission($cd_permissao, ['action' => 'mass', 'value' => $permission['vl_padrao']]);
		}

		return $result;
	}

	public function deletePermission($cd_permissao)
	{
		$permssion = $this->find(["cd_permissao" => $cd_permissao, "fields" => "logo_alternativo_acesso"]);
		
		if (! empty($permssion)) {
			$path_image = str_replace(UrlUtils::getUrl(), '', $permssion[0]["logo_alternativo_acesso"]);
			if (file_exists($path_image)) {
				unlink($path_image);
			}
		}

		$where = [["field" => "cd_permissao", "operator" => "=", "value" => $cd_permissao]];
		$result = parent::delete($where);

		if ($result) {
			PermissionUtils::actionPermission($cd_permissao, ['action' => 'mass', 'value' => 'N']);
		}

		return $result;
	}

	public function deleteImage($cd_permissao) {
		$permssion = $this->find(["cd_permissao" => $cd_permissao, "fields" => "logo_alternativo_acesso"]);
		
		if (! empty($permssion)) {
			$path_image = str_replace(UrlUtils::getUrl(), '', $permssion[0]["logo_alternativo_acesso"]);
			if (file_exists($path_image)) {
				unlink($path_image);
			}
		}

		$where = [["field" => "cd_permissao", "operator" => "=", "value" => $cd_permissao]];
		return parent::update(["logo_alternativo_acesso" => null], $where);
	}
}