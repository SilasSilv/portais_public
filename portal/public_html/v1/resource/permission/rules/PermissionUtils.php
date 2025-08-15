<?php 
namespace api\v1\resource\permission\rules;

use api\v1\resource\turnstile\rules\TurnstileDAO;

class PermissionUtils 
{
	public function resolvePermissions($permissions)
	{
		$pReturn = [];
		$indexSystem = -1;
		$indexTypePermission = -1;
		$codeSystemOld = -1;
		$codeTypePermissionOld = -1;



		foreach ($permissions as $p) {
			$system_not_exists = true;
	
			if ($p['cd_sistema'] == $codeSystemOld && $codeSystemOld !== -1) {
				$system_not_exists = false;
			} else {
				$codeSystemOld = $p['cd_sistema'];
			}

			if ($system_not_exists) {
				$pReturn[] = [
					'cd_sistema' => $p['cd_sistema'], 'nm_sistema' => $p['nm_sistema'], 'ds_sistema' => $p['ds_sistema'],
					'tipos_permissoes' => [[
							'cd_tipo_permissao' => $p['cd_tipo_permissao'], 'ds_tipo_permissao' => $p['ds_tipo_permissao'],
							'permissoes' => [[
								'cd_permissao' => $p['cd_permissao'], 'ds_titulo' => $p['ds_titulo'], 'ds_descricao' => $p['ds_descricao'], 
								'vl_padrao' => $p['vl_padrao'], 'vl_setor' => $p['vl_setor'], 'vl_permissao' => $p['vl_permissao']
							]]
					]]
				];

				$indexSystem++; 
				$indexTypePermission = 0;
				$codeTypePermissionOld = $p['cd_tipo_permissao'];
			} else {
				$type_permission_not_exists = true;

				if ($p['cd_tipo_permissao'] == $codeTypePermissionOld && $codeTypePermissionOld !== -1) {
					$type_permission_not_exists = false;
				} else {
					$codeTypePermissionOld = $p['cd_tipo_permissao'];
				}

				if ($type_permission_not_exists) {
					$pReturn[$indexSystem]['tipos_permissoes'][] = [
						'cd_tipo_permissao' => $p['cd_tipo_permissao'], 'ds_tipo_permissao' => $p['ds_tipo_permissao'],
						'permissoes' => [[
							'cd_permissao' => $p['cd_permissao'], 'ds_titulo' => $p['ds_titulo'], 'ds_descricao' => $p['ds_descricao'], 
							'vl_padrao' => $p['vl_padrao'], 'vl_setor' => $p['vl_setor'], 'vl_permissao' => $p['vl_permissao']
						]]
					];

					$indexTypePermission++;
				} else {
					$pReturn[$indexSystem]['tipos_permissoes'][$indexTypePermission]['permissoes'][] = [
						'cd_permissao' => $p['cd_permissao'], 'ds_titulo' => $p['ds_titulo'], 'ds_descricao' => $p['ds_descricao'], 
						'vl_padrao' => $p['vl_padrao'], 'vl_setor' => $p['vl_setor'], 'vl_permissao' => $p['vl_permissao']
					];
				}		
			}			
		}

		return $pReturn;
	}

	public function toExtractCodePermissions($permissions)
	{
		$codesPermission = "";

		for ($i=0; $i<count($permissions); $i++) {
			$codesPermission .=  $permissions[$i]['cd_permissao'];

			if ($i < (count($permissions) -1)) {
				$codesPermission .= ',';
			}			
		}

		return $codesPermission;
	}


	public function resolvePermissionsGroup($permissions) 
	{
		$permissionsNew = [];

		foreach ($permissions as $permission) {
			if (!array_key_exists($permission['ds_tipo_categoria'] , $permissionsNew)) {
				$permissionsNew[$permission['ds_tipo_categoria']] = [];
				$permissionsNew['tipos_permissao'][] = $permission['ds_tipo_categoria']; 
			}
	
			$permissionsNew[$permission['ds_tipo_categoria']][] = $permission;
		}

		return $permissionsNew;
	}

	public static function actionPermission($id, $options=[]) 
	{
		switch($id) 
		{
			case 10: 
				if (array_key_exists('action', $options) &&
					array_key_exists('value', $options)) {
					$turnstileDAO = new TurnstileDAO();
					
					if ($options['action'] == 'mass') {
						$turnstileDAO->massAccessUpdate($options['value']);
					} elseif ($options['action'] == 'sector' && 
							  array_key_exists('cd_setor', $options)) {
						$turnstileDAO->sectorAccessUpdate($options['value'], $options['cd_setor']);
					} elseif ($options['action'] == 'person' && 
							  array_key_exists('nr_cracha', $options)) {
						$turnstileDAO->personalAccessUpdate($options['value'], $options['nr_cracha']);
					}

				}
		}
	}
}