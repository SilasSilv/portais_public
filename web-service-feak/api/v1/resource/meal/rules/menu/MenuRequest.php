<?php

namespace  api\v1\resource\meal\rules\menu;

use \api\v1\vendor\error\Error;

Class MenuRequest {

	private $nr_refeicao;
	private $nr_cracha;
	private $dt_atualizacao;
	private $ds_ref_alt;

	public function getNrRefeicao()
	{
		return $this->nr_refeicao;
	}

	public function setNrRefeicao($nr_refeicao)
	{
		$this->nr_refeicao = $nr_refeicao;
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

	public function getDtAtualizacao()
	{
		return $this->dt_atualizacao;
	}

	public function setDtAtualizacao($dt_atualizacao)
	{
		$this->dt_atualizacao = $dt_atualizacao;
		return $this;
	}

	public function getDsRefAlt()
	{
		return $this->ds_ref_alt;		
	}	

	public function setDsRefAlt($ds_ref_alt)
	{
		$this->ds_ref_alt = $ds_ref_alt;
		return $this;
	}

	public function getParsedArray() 
	{
		$menuRequest = [];

		if($this->getNrRefeicao() != NULL)
			$menuRequest['nr_refeicao'] = $this->getNrRefeicao();

		if($this->getNrCracha() != NULL)
			$menuRequest['nr_cracha'] = $this->getNrCracha();

		if($this->getDtAtualizacao() != NULL)
			$menuRequest['dt_atualizacao'] = $this->getDtAtualizacao();

		$menuRequest['ds_ref_alt'] = $this->getDsRefAlt() == "" ? NULL : $this->getDsRefAlt();

		return $menuRequest;
	}

	public function setRequestValidate($request, $operation)
	{
		if ($operation == 'POST') {

			if (array_key_exists('nr_refeicao', $request)) {
				$this->setNrRefeicao($request['nr_refeicao']);
			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_refeicao']);
			}

			if (array_key_exists('nr_cracha', $request)) {
				$this->setNrCracha($request['nr_cracha']);
			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
			}

	
		} elseif ($operation == 'PUT') {

			if (array_key_exists('ds_ref_alt', $request)) {
				$this->setDsRefAlt($request['ds_ref_alt']);
			} else {
				Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_ref_alt']);
			}
	
		}

		$this->setDtAtualizacao(date('Y-m-d H:i:s'));
	}	
}