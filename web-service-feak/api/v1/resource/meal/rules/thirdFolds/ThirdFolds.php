<?php

namespace api\v1\resource\meal\rules\thirdFolds;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\DateUtils;

Class ThirdFolds {

	private $nr_sequencia;
	private $ie_terceiro_dobra;
	private $dt_refeicao;
	private $ie_tipo_refeicao;
	private $nm_pessoa_cartao;
	private $nr_cracha_resp;
	private $nr_cartao;
	private $nr_cracha;

	public function getNrSequencia()
	{
		return $this->nr_sequencia;
	}

	public function setNrSequencia($nr_sequencia)
	{
		$this->nr_sequencia = $nr_sequencia;
		return $this;
	}

	public function getIeTerceiroDobra()
	{
		return $this->ie_terceiro_dobra;
	}

	public function setIeTerceiroDobra($ie_terceiro_dobra)
	{	
		if($ie_terceiro_dobra != 'D' && $ie_terceiro_dobra != 'T') {
			Error::generateErrorCustom(
        			'Field ie_terceiro_dobra: Invalid value', 422
        		);
		}

		$this->ie_terceiro_dobra = $ie_terceiro_dobra;
		return $this;
	}

	public function getDtRefeicao($format = 'Y-m-d')
	{	
		if ($this->dt_refeicao != null) {
			return $this->dt_refeicao->format($format);
		} else {
			return $this->dt_refeicao;
		}
	}

	public function setDtRefeicao($dt_refeicao)
	{
		$this->dt_refeicao = DateUtils::convert($dt_refeicao, "BR-DATE", ["name_field" => "dt_refeicao"]);
		return $this;
	}

	public function getIeTipoRefeicao()
	{
		return $this->ie_tipo_refeicao;
	}

	public function setIeTipoRefeicao($ie_tipo_refeicao)
	{
		$this->ie_tipo_refeicao = $ie_tipo_refeicao;
		return $this;
	}

	public function getNmPessoaCartao()
	{
		return $this->nm_pessoa_cartao;
	}

	public function setNmPessoaCartao($nm_pessoa_cartao)
	{
		$this->nm_pessoa_cartao = $nm_pessoa_cartao;
		return $this;
	}

	public function getNrCrachaResp()
	{
		return $this->nr_cracha_resp;
	}

	public function setNrCrachaResp($nr_cracha_resp)
	{
		$this->nr_cracha_resp = $nr_cracha_resp;
		return $this;
	}

	public function getNrCartao()
	{
		return $this->nr_cartao;
	}

	public function setNrCartao($nr_cartao)
	{
		$this->nr_cartao = $nr_cartao;
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

	public function getParsedArray()
	{
		$thirdFolds = [];

		if ($this->getNrSequencia() != NULL)
			$thirdFolds['nr_sequencia'] = $this->getNrSequencia();

		if ($this->getIeTerceiroDobra() != NULL)
			$thirdFolds['ie_terceiro_dobra'] = $this->getIeTerceiroDobra();

		if ($this->getDtRefeicao() != NULL)
			$thirdFolds['dt_refeicao'] = $this->getDtRefeicao();

		if ($this->getIeTipoRefeicao() != NULL)
			$thirdFolds['ie_tipo_refeicao'] = $this->getIeTipoRefeicao();			

		if ($this->getNrCrachaResp() != NULL)
			$thirdFolds['nr_cracha_resp'] = $this->getNrCrachaResp();

		if ($this->getNrCartao() != NULL && $this->getNrCartao() != "") {
			$thirdFolds['nr_cartao'] = $this->getNrCartao();
			$thirdFolds['nm_pessoa_cartao'] = $this->getNmPessoaCartao();
			$thirdFolds['nr_cracha'] = NULL;
		}

		if ($this->getNrCracha() != NULL && $this->getNrCracha() != "") {			
			$thirdFolds['nr_cracha'] = $this->getNrCracha();
			$thirdFolds['nr_cartao'] = NULL;
			$thirdFolds['nm_pessoa_cartao'] = NULL;
		}

		return $thirdFolds;
	}

	public function setThirdFoldsValidate($thirdFolds) 
	{
		if ((array_key_exists('nr_cracha', $thirdFolds) && !empty($thirdFolds['nr_cracha'])) && (array_key_exists('nr_cartao', $thirdFolds) && !empty($thirdFolds['nr_cartao']))) {
			Error::generateErrorCustom('fill in the field nr_cracha or nr_cartao', 422);
		}
		
		if (array_key_exists('nr_sequencia', $thirdFolds)) {
			$this->setNrSequencia($thirdFolds['nr_sequencia']);
		} 

		if (array_key_exists('ie_terceiro_dobra', $thirdFolds)) {
			$this->setIeTerceiroDobra($thirdFolds['ie_terceiro_dobra']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ie_terceiro_dobra']);
		}

		if (array_key_exists('dt_refeicao', $thirdFolds)) {
			$this->setDtRefeicao($thirdFolds['dt_refeicao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_refeicao']);
		}

		if (array_key_exists('nr_cracha_resp', $thirdFolds)) {
			$this->setNrCrachaResp($thirdFolds['nr_cracha_resp']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha_resp']);
		}

		if ($thirdFolds['ie_terceiro_dobra'] == "T") {
			if (array_key_exists('nr_cartao', $thirdFolds) && !empty($thirdFolds['nr_cartao'])) {
				$this->setNrCartao($thirdFolds['nr_cartao']);
			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cartao']);
			}
			
			if (array_key_exists('nm_pessoa_cartao', $thirdFolds) && !empty($thirdFolds['nm_pessoa_cartao'])) {
				$this->setNmPessoaCartao($thirdFolds['nm_pessoa_cartao']);
			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nm_pessoa_cartao']);
			}
		} elseif ($thirdFolds['ie_terceiro_dobra'] == "D") {

			if (array_key_exists('nr_cartao', $thirdFolds) && array_key_exists('nm_pessoa_cartao', $thirdFolds) && !empty($thirdFolds['nr_cartao']) && !empty($thirdFolds['nm_pessoa_cartao'])) {

				$this->setNrCartao($thirdFolds['nr_cartao']);
				$this->setNmPessoaCartao($thirdFolds['nm_pessoa_cartao']);

			} elseif (array_key_exists('nr_cracha', $thirdFolds) && !empty($thirdFolds['nr_cracha'])) {

				$this->setNrCracha($thirdFolds['nr_cracha']);

			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cartao and nm_pessoa_cartao or nr_cracha']);
			}

		}
			
		$this->setIeTipoRefeicao('A');
	}

}