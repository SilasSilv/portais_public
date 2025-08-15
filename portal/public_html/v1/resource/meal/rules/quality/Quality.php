<?php 

namespace api\v1\resource\meal\rules\quality;

use \api\v1\vendor\error\Error;

Class Quality {

	private $nr_sequencia;
	private $nr_cracha;
	private $cd_apresentacao;
	private $cd_temperatura;
	private $cd_sabor;
	private $cd_simpatia;
	private $cd_higiene_loc;
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

	public function getCdApresentacao()
	{
		return $this->cd_apresentacao;
	} 

	public function setCdApresentacao($cd_apresentacao)
	{
		$this->cd_apresentacao = $cd_apresentacao;
		return $this;
	}

	public function getCdTemperatura()
	{
		return $this->cd_temperatura;
	} 

	public function setCdTemperatura($cd_temperatura)
	{
		$this->cd_temperatura = $cd_temperatura;
		return $this;
	}

	public function getCdSabor()
	{
		return $this->cd_sabor;
	} 

	public function setCdSabor($cd_sabor)
	{
		$this->cd_sabor = $cd_sabor;
		return $this;
	}

	public function getCdSimpatia()
	{
		return $this->cd_simpatia;
	} 

	public function setCdSimpatia($cd_simpatia)
	{
		$this->cd_simpatia = $cd_simpatia;
		return $this;
	}

	public function getCdHigieneLoc()
	{
		return $this->cd_higiene_loc;
	} 

	public function setCdHigieneLoc($cd_higiene_loc)
	{
		$this->cd_higiene_loc = $cd_higiene_loc;
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
		$quality = [];

		if ($this->getNrSequencia() != NULL)
			$quality['nr_sequencia'] = $this->getNrSequencia();

		if ($this->getNrCracha() != NULL)
			$quality['nr_cracha'] = $this->getNrCracha();

		if ($this->getCdApresentacao() != NULL)
			$quality['cd_apresentacao'] = $this->getCdApresentacao();

		if ($this->getCdTemperatura() != NULL)
			$quality['cd_temperatura'] = $this->getCdTemperatura();

		if ($this->getCdSabor() != NULL)
			$quality['cd_sabor'] = $this->getCdSabor();

		if ($this->getCdSimpatia() != NULL)
			$quality['cd_simpatia'] = $this->getCdSimpatia();

		if ($this->getCdHigieneLoc() != NULL)
			$quality['cd_higiene_loc'] = $this->getCdHigieneLoc();

		if ($this->getDtInclusao() != NULL)
			$quality['dt_inclusao'] = $this->getDtInclusao();

		return $quality;
	}

	public function setQualityValidate($quality, $operation) 
	{

		if (array_key_exists('nr_sequencia', $quality)) {
			$this->setNrSequencia($quality['nr_sequencia']);
		} 

		if (array_key_exists('nr_cracha', $quality) && $operation == "POST") {
			$this->setNrCracha($quality['nr_cracha']);
		} elseif ($operation == "POST") {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
		}

		if (array_key_exists('cd_apresentacao', $quality)) {
			$this->setCdApresentacao($quality['cd_apresentacao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_apresentacao']);
		}

		if (array_key_exists('cd_temperatura', $quality)) {
			$this->setCdTemperatura($quality['cd_temperatura']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_temperatura']);
		}

		if (array_key_exists('cd_sabor', $quality)) {
			$this->setCdSabor($quality['cd_sabor']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_sabor']);
		}

		if (array_key_exists('cd_simpatia', $quality)) {
			$this->setCdSimpatia($quality['cd_simpatia']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_simpatia']);
		}

		if (array_key_exists('cd_higiene_loc', $quality)) {
			$this->setCdHigieneLoc($quality['cd_higiene_loc']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_higiene_loc']);
		}

		$dt_inclusao = new \DateTime();
		$this->setDtInclusao($dt_inclusao);
	}

}