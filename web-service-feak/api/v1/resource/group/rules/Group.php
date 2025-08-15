<?php 
namespace api\v1\resource\group\rules;

use \api\v1\vendor\error\Error;

class Group {

	private $cd_grupo;
	private $nm_grupo;

	public function getCdGrupo()
	{
		return $this->cd_grupo;
	}

	public function setCdGrupo($cd_grupo)
	{
		return $this->cd_grupo = $cd_grupo;
	}

	public function getNmGrupo()
	{
		return $this->nm_grupo;
	}

	public function setNmGrupo($nm_grupo)
	{
		return $this->nm_grupo = $nm_grupo;
	}

	public function getParsedArray()
	{
		$group = [];

		if ($this->getCdGrupo() != NULL) {
			$group['cd_grupo'] = $this->getCdGrupo();
		}

		if ($this->getNmGrupo() != NULL) {
			$group['nm_grupo'] = $this->getNmGrupo();
		}

		return $group;
	}

	public function setGroupValidate($group, $method='')
	{
		$fieldsRequired = '';

		if (array_key_exists('cd_grupo', $group) && $method != 'POST') {
			$this->setCdGrupo($group['cd_grupo']);
		}

		if (array_key_exists('nm_grupo', $group)) {
			$this->setNmGrupo($group['nm_grupo']);
		} else {
			$fieldsRequired .= 'Required: nm_grupo';
		}
			
		if (strlen($fieldsRequired) > 0) {
			Error::generateErrorCustom($fieldsRequired, 422);
		}
	}
}