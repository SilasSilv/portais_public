<?php

namespace api\v1\resource\meal\rules\opinion;

use \api\v1\vendor\error\Error;

Class Opinion {

	private $nr_sequencia;
	private $nr_cracha;
	private $cd_tipo_opiniao;
	private $ds_opinicao;
	private $dt_inclusao;

	public function getNrSequencia()
	{
		return $this->nr_sequencia;
	}

	public function setNrSequencia($nr_sequencia)
	{
		$this->nr_sequencia = $nr_sequencia;
		return $this;
	}

	public function getNrCracha()
	{
		return $this->nr_cracha;
	}

	public function setNrCracha($nr_cracha)
	{
		$this->nr_cracha = $nr_cracha;
		return $this;
	}

	public function getCdTipoOpiniao()
	{
		return $this->cd_tipo_opiniao;
	}

	public function setCdTipoOpiniao($cd_tipo_opiniao)
	{
		$this->cd_tipo_opiniao = $cd_tipo_opiniao;
		return $this;
	}

	public function getDsOpiniao()
	{
		return $this->ds_opinicao;
	}

	public function setDsOpiniao($ds_opinicao)
	{
		$this->ds_opinicao = $ds_opinicao;
		return $this;
	}

	public function getDtInclusao($format = 'Y-m-d H:i:s')
	{	
		if ($this->dt_inclusao != null) {
			return $this->dt_inclusao->format($format);
		} else {
			return $this->dt_inclusao;
		}
	}

	public function setDtInclusao($dt_inclusao)
	{
		$this->dt_inclusao = $dt_inclusao;
		return $this;
	}

	public function getParsedArray()
	{
		$opinion = [];

		if($this->getNrSequencia() != NULL)
			$opinion['nr_sequencia'] = $this->getNrSequencia();

		if($this->getNrCracha() != NULL)
			$opinion['nr_cracha'] = $this->getNrCracha();

		if($this->getCdTipoOpiniao() != NULL)
			$opinion['cd_tipo_opiniao'] = $this->getCdTipoOpiniao();

		if($this->getDsOpiniao() != NULL)
			$opinion['ds_opiniao'] = $this->getDsOpiniao();

		if($this->getDtInclusao() != NULL)
			$opinion['dt_inclusao'] = $this->getDtInclusao();

		return $opinion;
	}

	public function setOpinionValidate($opinion, $operation)
	{
		if (array_key_exists('nr_sequencia', $opinion)) {
			$this->setNrSequencia($opinion['nr_sequencia']);
		} 

		if (array_key_exists('nr_cracha', $opinion) && $operation == "POST") {
			$this->setNrCracha($opinion['nr_cracha']);
		} elseif ($operation == "POST") {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
		}

		if (array_key_exists('cd_tipo_opiniao', $opinion)) {
			$this->setCdTipoOpiniao($opinion['cd_tipo_opiniao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_opiniao']);
		}

		if (array_key_exists('ds_opiniao', $opinion)) {
			$this->setDsOpiniao($opinion['ds_opiniao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_opiniao']);
		}
	}	
	
}