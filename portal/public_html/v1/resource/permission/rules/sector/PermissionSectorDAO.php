<?php 
namespace api\v1\resource\permission\rules\sector;

use \api\v1\vendor\db\Crud;
use \api\v1\resource\permission\rules\PermissionUtils;

class PermissionSectorDAO extends Crud 
{
	public function __construct()
	{
		parent::__construct();
		$this->table = 'tb_permissao_setor';
		$this->fields = "p.cd_permissao, p.cd_sistema, s.ds_sistema, p.cd_tipo_permissao, c.ds_categoria AS ds_tipo_permissao, p.ds_titulo, COALESCE(ps.vl_setor, '') AS vl_permissao";
		$this->fieldFromTo = [
								"ds_sistema" => "s.ds_sistema",
								"vl_permissao" => "COALESCE(pf.vl_setor, '') AS vl_permissao",
								"ds_tipo_permissao" => "c.ds_categoria AS ds_tipo_permissao" 
							];
		$this->join = "INNER JOIN tb_sistema s ON p.cd_sistema = s.cd_sistema
    				   INNER JOIN tb_categoria c ON p.cd_tipo_permissao = c.cd_categoria
					   LEFT JOIN tb_permissao_setor ps ON p.cd_permissao = ps.cd_permissao";
		$this->sortFields = [ ["field" => "s.ds_sistema", "type" => ""], ["field" => "c.ds_categoria", "type" => ""],  ["field" => "p.ds_titulo", "type" => ""]];
	}

	public function find($params)
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
		$params["fields_filter"]['from_to'] = $this->fieldFromTo;
		$params["fields"] = $this->fields;
		
		$params["table"] = ["name" => "tb_permissao", "aliases" => "p"];
		$params["join"] = $this->join . " AND ps.cd_setor = {$params['cd_setor']} ";
		$params["where"] = $this->mountWhere($params);

		$params["sort_fields"] = $this->sortFields;
		$params["pagination"] = "ALL";

		$permissionUtils = new PermissionUtils();

		return $permissionUtils->resolvePermissions(parent::readNew($params));
	}

	public function findPermissionsExists($codesPermission, $cd_setor)
	{	
		$params["fields"] = "p.cd_permissao";
		$params["table"] = ["name" => $this->table, "aliases" => "p"];

		$params["where"] = $this->mountWhere(["cd_permissao" => $codesPermission]);
		$params["where"]["cd_setor"] = $this->mountPartWhere("AND", "p", "cd_setor", "IN", $cd_setor, 1);

		$params["pagination"] = "ALL";
		$params['return_type'] = \PDO::FETCH_NUM;

		$codesReturn = [];

		foreach (parent::readNew($params) as $value) {
			$codesReturn[] = $value[0];
		}

		return $codesReturn;
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
				default: $i--;
			}

			$i++;
		}	

		return $where;
	}

	public function persist($permissions, $nr_cracha)
	{
		$permissionUtils = new PermissionUtils();

		$codesPermission = $permissionUtils->toExtractCodePermissions($permissions);
		$permissionsExists = $this->findPermissionsExists($codesPermission, $nr_cracha);

		foreach ($permissions as $permission) {
		
			if (in_array($permission['cd_permissao'], $permissionsExists) && $permission['vl_setor'] == 'D') {

				$this->deletePermission($permission);

			} elseif (in_array($permission['cd_permissao'], $permissionsExists)) {

				$this->updatePermission(['vl_setor' => $permission['vl_setor']], $permission);

			} elseif ($permission['vl_setor'] != 'D') {
				
				$this->register($permission);

			}
		}

		return true;
	}

	public function register($permission)
	{
		if (parent::create($permission)) {
			PermissionUtils::actionPermission($permission["cd_permissao"],
			['action' => 'sector', 'cd_setor' => $permission['cd_setor'], 'value' => $permission['vl_setor']]);
	
			return $permission["cd_permissao"];
		} else {
			return -1;
		}
	}

	public function updatePermission($permission, $params)
	{
		$where = [ 
					[ "field" => "cd_setor", "operator" => "=", "value" => $params['cd_setor'] ],
					[ "logical_op" => "AND", "field" => "cd_permissao", "operator" => "=", "value" => $params['cd_permissao'] ]
				];

		$result =  parent::update($permission, $where);


		if ($result) {
			PermissionUtils::actionPermission($params["cd_permissao"],
			['action' => 'sector', 'cd_setor' => $params['cd_setor'], 'value' => $permission['vl_setor']]);
		}

		return $result;
	}

	public function deletePermission($params)
	{
		$where = [ 
					[ "field" => "cd_setor", "operator" => "=", "value" => $params['cd_setor'] ],
					[ "logical_op" => "AND", "field" => "cd_permissao", "operator" => "=", "value" => $params['cd_permissao'] ]
				];
		
		$result = parent::delete($where);

		if ($result) {
			PermissionUtils::actionPermission($params["cd_permissao"],
			['action' => 'sector', 'cd_setor' => $params['cd_setor'], 'value' => 'N']);
		}

		return $result;
	}
}