<?php 
namespace api\v1\resource\phonebook\rules;

use \api\v1\vendor\error\Error;

Class Phonebook {

	private $nr_sequencia;
	private $ds_contato;
	private $nr_telefone;
	private $ds_observacao;
	private $cd_tipo_contato;

	public function getNrSequencia() 
	{
		return $this->nr_sequencia;
	}

	public function setNrSequencia($nr_sequencia)
	{
		$this->nr_sequencia = $nr_sequencia;
		return $this;
	}

	public function getDsContato() 
	{
		return $this->ds_contato;
	}

	public function setDsContato($ds_contato)
	{
		$this->ds_contato = $ds_contato;
		return $this;
	}

	public function getNrTelefone() 
	{
		return $this->nr_telefone;
	}

	public function setNrTelefone($nr_telefone)
	{
		$this->nr_telefone = $nr_telefone;
		return $this;
	}

	public function getDsObservacao() 
	{
		return $this->ds_observacao;
	}

	public function setDsObservacao($ds_observacao)
	{
		$this->ds_observacao = $ds_observacao;
		return $this;
	}

	public function getCdTipoContato() 
	{
		return $this->cd_tipo_contato;
	}

	public function setCdTipoContato($cd_tipo_contato)
	{
		$this->cd_tipo_contato = $cd_tipo_contato;
		return $this;
	}

	public function getParsedArray()
	{
		$phonebook = [];

		if ($this->getNrSequencia() != NULL) {
			$phonebook['nr_sequencia'] = $this->getNrSequencia();
		}

		if ($this->getDsContato() != NULL) {
			$phonebook['ds_contato'] = $this->getDsContato();
		}

		if ($this->getNrTelefone() != NULL) {
			$phonebook['nr_telefone'] = $this->getNrTelefone();
		}

		if ($this->getDsObservacao() === "") {
			$phonebook['ds_observacao'] = NULL;
		} elseif ($this->getDsObservacao() != NULL) {
			$phonebook['ds_observacao'] = $this->getDsObservacao();
		}
		
		if ($this->getCdTipoContato() != NULL) {
			$phonebook['cd_tipo_contato'] = $this->getCdTipoContato();
		}

		return $phonebook;
	}

	public function setPhonebookValidate($phonebook)
	{	
		if (array_key_exists('nr_sequencia', $phonebook)) {
			$this->setNrSequencia($phonebook['nr_sequencia']);
		} 
		
		if (array_key_exists('ds_contato', $phonebook)) {
			$this->setDsContato($phonebook['ds_contato']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_contato']);
		}

		if (array_key_exists('nr_telefone', $phonebook)) {
			if (preg_match('/^\(\d{2}\) \d{4,5}-\d{4}$|^\d{4,5}-\d{4}$|^\d{4}$/', $phonebook['nr_telefone'])) {
				$this->setNrTelefone($phonebook['nr_telefone']);
			} else {
				Error::generateErrorCustom('Field nr_telefone not pattern: (99) 99999-9999 ou 99999-9999 ou 9999-9999 ou 9999', 422);				
			}
		} else {+
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_telefone']);
		}

		if (array_key_exists('ds_observacao', $phonebook)) {
			$this->setDsObservacao($phonebook['ds_observacao']);
		}

		if (array_key_exists('cd_tipo_contato', $phonebook)) {
			$this->setCdTipoContato($phonebook['cd_tipo_contato']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_contato']);
		}
	}
	
}