<?php
namespace api\v1\resource\permission\rules\sector;

use \api\v1\vendor\error\Error;

class PermissionSector {

	private $permissions = [];

	private function setPermissions($permission, $cd_setor)
	{	
		$permission["cd_setor"] = $cd_setor;
		
		if ($permission["vl_permissao"] != 'S' && $permission["vl_permissao"] != 'N' && $permission["vl_permissao"] != 'D') {
			Error::generateErrorCustom('Invalid value vl_permissao', 422);
		} else {
			$permission["vl_setor"] = $permission["vl_permissao"];
			unset($permission["vl_permissao"]);
		}		

		return $this->permissions[] = $permission;
	}

	public function getPermissions()
	{	
		return $this->permissions;
	}

	public function setValidate($permissions)
	{
		if (array_key_exists('permissoes', $permissions)) {
			if (is_array($permissions['permissoes'])) {

				foreach($permissions['permissoes'] as $value) {
					$fieldsRequired = '';

					if (!array_key_exists('cd_permissao', $value)) {
						$fieldsRequired .= 'Elemet array required: cd_permissao';
					}
					if (!array_key_exists('vl_permissao', $value)) {
						$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'Elemet array required: vl_permissao' : ',vl_permissao';
					}
					if (strlen($fieldsRequired) > 0) {
						Error::generateErrorCustom($fieldsRequired, 422);
					} else {
						$this->setPermissions($value, $permissions["cd_setor"]);
					}
				}

			} else {
				Error::generateErrorCustom('Permissions must be an array', 422);
			}
		} else {
			Error::generateErrorCustom('Required: permissoes', 422);
		}
	}
}