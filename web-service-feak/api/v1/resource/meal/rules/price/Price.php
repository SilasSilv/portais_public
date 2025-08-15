<?php 

namespace api\v1\resource\meal\rules\price;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\DateUtils;

class Price {
	private $nr_sequencia;
	private $dt_vigencia_inicial;
	private $dt_vigencia_final;
	private $vl_refeicao;
	private $ie_situacao;

	public function getNrSequencia() 
	{
		return $this->nr_sequencia;
	}

	public function setNrSequencia($nr_sequencia)
	{
		$this->nr_sequencia = $nr_sequencia;
		return $this;
	}

	public function getDtVigenciaInicial($format = 'Y-m-d') 
	{
		return $this->dt_vigencia_inicial->format($format);;
	}

	public function setDtVigenciaInicial($dt_vigencia_inicial)
	{
		$this->dt_vigencia_inicial = DateUtils::convert($dt_vigencia_inicial, 'BR-DATE', ['name_field' => 'dt_vigencia_inicial']);
		$this->dt_vigencia_inicial->setTime(0, 0, 0);
		return $this;
	}

	public function getDtVigenciaFinal($format = 'Y-m-d') 
	{
		return $this->dt_vigencia_final->format($format);
	}

	public function setDtVigenciaFinal($dt_vigencia_final)
	{
		$this->dt_vigencia_final = DateUtils::convert($dt_vigencia_final, 'BR-DATE', ['name_field'  => 'dt_vigencia_final']);
		$this->dt_vigencia_final->setTime(0, 0, 0);

		if ($this->dt_vigencia_inicial > $this->dt_vigencia_final) {
			Error::generateErrorCustom('Field dt_vigencia_final: Can not be less than dt_vigencia_inicial', 422);
		}

		return $this;
	}

	public function getVlRefeicao() 
	{
		return $this->vl_refeicao;
	}

	public function setVlRefeicao($vl_refeicao)
	{
		if ($vl_refeicao < 0) {
			Error::generateErrorCustom('Field vl_refeicao: Can not be less than 0', 422);
		}

		$this->vl_refeicao = $vl_refeicao;
		return $this;
	}

	public function getIeSituacao() 
	{
		return $this->ie_situacao;
	}

	public function setIeSituacao($ie_situacao)
	{	
		if ($ie_situacao != 'A' && $ie_situacao != 'I') {
			 Error::generateErrorCustom('Field ie_situacao: Value invalid', 422);
		}

		$this->ie_situacao = $ie_situacao;
		return $this;
	}

	public function getParsedArray()
	{
		$price = [];

		if ($this->getNrSequencia() != NULL) {
			$price['nr_sequencia'] = $this->getNrSequencia();
		}

		if ($this->getDtVigenciaInicial() != NULL) {
			$price['dt_vigencia_inicial'] = $this->getDtVigenciaInicial();
		}

		if ($this->getDtVigenciaFinal() != NULL) {
			$price['dt_vigencia_final'] = $this->getDtVigenciaFinal();
		}

		if ($this->getVlRefeicao() !== NULL) {
			$price['vl_refeicao'] = $this->getVlRefeicao();
		} 

		if ($this->getIeSituacao() != NULL) {
			$price['ie_situacao'] = $this->getIeSituacao();
		}

		return $price;
	}

	public function setPriceValidate($price)
	{
		$fieldsRequired = '';

		if (array_key_exists('nr_sequencia', $price)) {
			$this->setNrSequencia($price['nr_sequencia']);
		}

		if (array_key_exists('dt_vigencia_inicial', $price)) {
			$this->setDtVigenciaInicial($price['dt_vigencia_inicial']);
		} else {
			$fieldsRequired = (strlen($fieldsRequired) == 0) ? 'dt_vigencia_inicial' : ',dt_vigencia_inicial';	
		}

		if (array_key_exists('dt_vigencia_final', $price)) {
			$this->setDtVigenciaFinal($price['dt_vigencia_final']);
		} else {
			$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'dt_vigencia_final' : ',dt_vigencia_final';
		}

		if (array_key_exists('vl_refeicao', $price)) {
			$this->setVlRefeicao($price['vl_refeicao']);
		} else {
			$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'vl_refeicao' : ',vl_refeicao';
		}

		if (array_key_exists('ie_situacao', $price)) {
			$this->setIeSituacao($price['ie_situacao']);
		} else {
			$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'ie_situacao' : ',ie_situacao';
		}

		if (strlen($fieldsRequired) > 0) {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => " $fieldsRequired"]);
		}
	}
}