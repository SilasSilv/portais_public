<?php 

namespace api\v1\resource\pointOccurrence\rules;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\DateUtils;

Class PointOccurrence {

	private $nr_sequencia;
	private $nr_cracha;
	private $cd_tipo_ocorrencia;
	private $dt_ocorrencia;
	private $ds_qt_horas_dias;
	private $cd_tipo_horas_dias;
	private $ds_justificativa;
	private $cd_tipo_parecer;
	private $ds_observacao;
	private $dt_atualizacao;
	private $nr_cracha_inclusao;
	private $ie_lido_rh;
	private $ds_parecer_rh;
	private $dt_parecer_rh;
	private $nr_cracha_rh;
	private $dt_criacao;

	public function getNrSequencia() 
	{
		return $this->nr_sequencia;
	}

	public function setNrSquencia($nr_sequencia)
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

	public function getCdTipoOcorrencia() 
	{
		return $this->cd_tipo_ocorrencia;
	}

	public function setCdTipoOcorrencia($cd_tipo_ocorrencia)
	{
		if($cd_tipo_ocorrencia >= 1 && $cd_tipo_ocorrencia <= 7) {
			$this->cd_tipo_ocorrencia = $cd_tipo_ocorrencia;
			return $this;
		} else {
			Error::generateErrorCustom(
        			'Field cd_tipo_ocorrencia: Invalid code', 422
        		);
		}
	}

	public function getDtOcorrencia($format = 'Y-m-d') 
	{
		if ($this->dt_ocorrencia != null) {
			return $this->dt_ocorrencia->format($format);
		} else {
			return $this->dt_ocorrencia;
		}
	}

	public function setDtOcorrencia($dt_ocorrencia)
	{
		$this->dt_ocorrencia = DateUtils::convert($dt_ocorrencia, "BR-DATE", ['name_field' => 'dt_ocorrencia']);
		$dt_min = new \DateTime();
        $dt_max = new \DateTime();

        $dt_min->modify('-2 month');
		$dt_min->setTime(0, 0, 0);
		$dt_max->modify('+2 month');   
       	$dt_max->setTime(0, 0, 0);
       	$this->dt_ocorrencia->setTime(0, 0, 0);

        if ($this->dt_ocorrencia < $dt_min) {
        	Error::generateErrorCustom(
        			'Field dt_ocorrencia: Date less than the period of two months', 422
        		);
        } elseif ($this->dt_ocorrencia > $dt_max) {
        	Error::generateErrorCustom(
        			'Field dt_ocorrencia: Date higher than today', 422
        		);
        }

		return $this;
	}

	public function getDsQtHorasDias() 
	{
		return $this->ds_qt_horas_dias;
	}

	public function setDsQtHorasDias($ds_qt_horas_dias)
	{
		if (($this->getCdTipoHorasDias() == 11 && !preg_match("/^\d{1,2}$/", $ds_qt_horas_dias)) ||
			($this->getCdTipoHorasDias() == 12 && !preg_match("/^\d{2,2}:[0-5][0-9]$/", $ds_qt_horas_dias))) {

			Error::generateErrorCustom(
        			'Field ds_qt_horas_dias: Invalid value', 422
        		);

		} 	

		$this->ds_qt_horas_dias = $ds_qt_horas_dias;
		return $this;
	}

	public function getCdTipoHorasDias() 
	{
		return $this->cd_tipo_horas_dias;
	}

	public function setCdTipoHorasDias($cd_tipo_horas_dias)
	{
		if ($cd_tipo_horas_dias == 11 || $cd_tipo_horas_dias == 12 || $cd_tipo_horas_dias == 76) {
			$this->cd_tipo_horas_dias = $cd_tipo_horas_dias;
			return $this;
		} else {
			Error::generateErrorCustom('Field cd_tipo_horas_dias: Invalid code', 422);		
		}		
	}

	public function getDsJustificativa() 
	{
		return $this->ds_justificativa;
	}

	public function setDsJustificativa($ds_justificativa)
	{
		$this->ds_justificativa = $ds_justificativa;
		return $this;
	}

	public function getCdTipoParecer() 
	{
		return $this->cd_tipo_parecer;
	}

	public function setCdTipoParecer($cd_tipo_parecer)
	{	
		if($cd_tipo_parecer >= 8 && $cd_tipo_parecer <= 10) {
			$this->cd_tipo_parecer = $cd_tipo_parecer;
			return $this;
		} else {
			Error::generateErrorCustom(
        			'Field cd_tipo_parecer: Invalid code', 422
        		);
		}
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

	public function getDtAtualizacao($format = 'Y-m-d H:i:s') 
	{
		if ($this->dt_atualizacao != null) {
			return $this->dt_atualizacao->format($format);
		} else {
			return $this->dt_atualizacao;
		}	
	}

	public function setDtAtualizacao($dt_atualizacao)
	{
		$this->dt_atualizacao = $dt_atualizacao;
		return $this;
	}

	public function getNrCrachaInclusao() 
	{
		return $this->nr_cracha_inclusao;
	}

	public function setNrCrachaInclusao($nr_cracha_inclusao)
	{
		$this->nr_cracha_inclusao = $nr_cracha_inclusao;
		return $this;
	}

	public function getIeLidoRh() 
	{
		return $this->ie_lido_rh;
	}

	public function setIeLidoRh($ie_lido_rh)
	{
		$this->ie_lido_rh = $ie_lido_rh;
		return $this;
	}

	public function getDsParecerRh() 
	{
		return $this->ds_parecer_rh;
	}

	public function setDsParecerRh($ds_parecer_rh)
	{
		$this->ds_parecer_rh = $ds_parecer_rh;
		return $this;
	}

	public function getDtParecerRh($format = 'Y-m-d') 
	{	
		if ($this->dt_parecer_rh != null) {
			return $this->dt_parecer_rh->format($format);
		} else {
			return $this->dt_parecer_rh;
		}		
	}

	public function setDtParecerRh($dt_parecer_rh)
	{
		$this->dt_parecer_rh = $dt_parecer_rh;
		return $this;
	}

	public function getNrCrachaRh() 
	{
		return $this->nr_cracha_rh;
	}

	public function setNrCrachaRh($nr_cracha_rh)
	{
		$this->nr_cracha_rh = $nr_cracha_rh;
		return $this;
	}

	public function getDtCriacao($format = 'Y-m-d H:i:s') 
	{	
		if ($this->dt_criacao != null) {
			return $this->dt_criacao->format($format);
		} else {
			return $this->dt_criacao;
		}		
	}

	public function setDtCriacao($dt_criacao)
	{
		$this->dt_criacao = $dt_criacao;
		return $this;
	}

	public function getParsedArray() 
	{
		$pointOccurrence = [];

		if ($this->getNrSequencia() != NULL)
			$pointOccurrence['nr_sequencia'] = $this->getNrSequencia();

		if ($this->getNrCracha() != NULL)
			$pointOccurrence['nr_cracha'] = $this->getNrCracha();

		if ($this->getCdTipoOcorrencia() != NULL)
			$pointOccurrence['cd_tipo_ocorrencia'] = $this->getCdTipoOcorrencia();

		if ($this->getDtOcorrencia() != NULL)
			$pointOccurrence['dt_ocorrencia'] = $this->getDtOcorrencia();

		if ($this->getDsQtHorasDias() != NULL)
			$pointOccurrence['ds_qt_horas_dias'] = $this->getDsQtHorasDias();

		if ($this->getCdTipoHorasDias() != NULL)
			$pointOccurrence['cd_tipo_horas_dias'] = $this->getCdTipoHorasDias();

		if ($this->getDsJustificativa() != NULL)
			$pointOccurrence['ds_justificativa'] = $this->getDsJustificativa();

		if ($this->getCdTipoParecer() != NULL)
			$pointOccurrence['cd_tipo_parecer'] = $this->getCdTipoParecer();

		if ($this->getDsObservacao() != NULL)
			$pointOccurrence['ds_observacao'] = $this->getDsObservacao();

		if ($this->getDtAtualizacao() != NULL)
			$pointOccurrence['dt_atualizacao'] = $this->getDtAtualizacao();

		if ($this->getNrCrachaInclusao() != NULL)
			$pointOccurrence['nr_cracha_inclusao'] = $this->getNrCrachaInclusao();

		if ($this->getIeLidoRh() != NULL)
			$pointOccurrence['ie_lido_rh'] = $this->getIeLidoRh();

		if ($this->getDsParecerRh() != NULL)
			$pointOccurrence['ds_parecer_rh'] = $this->getDsParecerRh();

		if ($this->getDtParecerRh() != NULL)
			$pointOccurrence['dt_parecer_rh'] = $this->getDtParecerRh();

		if ($this->getNrCrachaRh() != NULL)
			$pointOccurrence['nr_cracha_rh'] = $this->getNrCrachaRh();

		if ($this->getDtCriacao() != NULL)
			$pointOccurrence['dt_criacao'] = $this->getDtCriacao();

		return $pointOccurrence;
	}

	public function setPointOccurrenceValidate($pointOccurrence)
	{	
		if (array_key_exists('nr_cracha', $pointOccurrence)) {
			$this->setNrCracha($pointOccurrence['nr_cracha']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
		}

		if (array_key_exists('cd_tipo_ocorrencia', $pointOccurrence)) {
			$this->setCdTipoOcorrencia($pointOccurrence['cd_tipo_ocorrencia']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_ocorrencia']);
		}

		if (array_key_exists('dt_ocorrencia', $pointOccurrence)) {
			$this->setDtOcorrencia($pointOccurrence['dt_ocorrencia']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_ocorrencia']);
		}

		if (array_key_exists('cd_tipo_horas_dias', $pointOccurrence)) {
			$this->setCdTipoHorasDias($pointOccurrence['cd_tipo_horas_dias']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_horas_dias']);
		}

		if (array_key_exists('ds_qt_horas_dias', $pointOccurrence)) {
			$this->setDsQtHorasDias($pointOccurrence['ds_qt_horas_dias']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_qt_horas_dias']);
		}	

		if (array_key_exists('ds_justificativa', $pointOccurrence)) {
			$this->setDsJustificativa($pointOccurrence['ds_justificativa']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_justificativa']);
		}

		if (array_key_exists('cd_tipo_parecer', $pointOccurrence)) {
			$this->setCdTipoParecer($pointOccurrence['cd_tipo_parecer']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_parecer']);
		}

		if (array_key_exists('ds_observacao', $pointOccurrence)) {
			$this->setDsObservacao($pointOccurrence['ds_observacao']);
		}

		if (array_key_exists('nr_cracha_inclusao', $pointOccurrence)) {
			$this->setNrCrachaInclusao($pointOccurrence['nr_cracha_inclusao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha_inclusao']);
		}			

		$dt_atualizacao = new \DateTime();

		$this->setDtAtualizacao($dt_atualizacao);
		$this->setIeLidoRh('N');
	}

	public function setPointOccurrenceSeemHRValidate($pointOccurrence)
	{	
		if (array_key_exists('nr_cracha_rh', $pointOccurrence)) {
			$this->setNrCrachaRh($pointOccurrence['nr_cracha_rh']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha_rh']);
		}

		if (array_key_exists('ds_parecer_rh', $pointOccurrence) && 
			$pointOccurrence['ds_parecer_rh'] != '') {
			$this->setDsParecerRh($pointOccurrence['ds_parecer_rh']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_parecer_rh']);
		}

		$dt_atualizacao = new \DateTime();
		$dt_parecer_rh = new \DateTime();
	
		$this->setDtAtualizacao($dt_atualizacao);
		$this->setDtParecerRh($dt_parecer_rh);
		$this->setIeLidoRh('S');
	}

}