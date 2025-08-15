<?php 
namespace api\v1\resource\office\rules;

use \api\v1\vendor\error\Error;

class Office {
	private $cd_cargo;
	private $ds_cargo;

	public function getCdCargo()
	{
		return $this->cd_cargo;
	}

	public function setCdCargo($cd_cargo)
	{
		return $this->cd_cargo = $cd_cargo;
	}

	public function getDsCargo()
	{
		return $this->ds_cargo;
	}

	public function setDsCargo($ds_cargo)
	{
		return $this->ds_cargo = $ds_cargo;
	}

	public function getParsedArray()
	{
		$office = [];

		if ($this->getCdCargo() != NULL) {
			$office['cd_cargo'] = $this->getCdCargo();
		}

		if ($this->getDsCargo() != NULL) {
			$office['ds_cargo'] = $this->getDsCargo();
		}

		return $office;
	}

	public function setOfficeValidate($office, $method='')
	{
		$fieldsRequired = '';

		if (array_key_exists('cd_cargo', $office) && $method != 'POST') {
			$this->setCdCargo($office['cd_cargo']);
		}

		if (array_key_exists('ds_cargo', $office)) {
			$this->setDsCargo($office['ds_cargo']);
		} else {
			$fieldsRequired .= 'Required: ds_cargo';
		}
			
		if (strlen($fieldsRequired) > 0) {
			Error::generateErrorCustom($fieldsRequired, 422);
		}
	}
}