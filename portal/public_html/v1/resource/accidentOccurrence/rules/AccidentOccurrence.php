<?php

namespace api\v1\resource\accidentOccurrence\rules;

use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

Class AccidentOccurrence {

	private $nr_sequencia;
	private $nr_cracha;
	private $dt_ocorrencia;
	private $nr_horas;
	private $ds_acidente;
	private $cd_parte_corpo;
	private $ie_situacao_epi;
	private $ds_epi;
	private $ds_acidente_evitado;
	private $ie_situacao_comunicado;
	private $cd_motivo_acidente;
	private $ds_outros;
	private $ie_situacao_testemunha;
	private $ds_testemunha;
	private $ie_lido_cipa;
	private $ds_parecer_cipa;
	private $dt_parecer_cipa;
	private $nr_cracha_cipa;
	private $dt_atualizacao;
	private $nr_cracha_inclusao;
	private $ds_local;

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

       	$dt_min->modify('-3 month');
       	$dt_min->setTime(0, 0, 0);
       	$dt_max->setTime(0, 0, 0);
       	$this->dt_ocorrencia->setTime(0, 0, 0);

        if ($this->dt_ocorrencia < $dt_min) {
        	Error::generateErrorCustom(
        			'Field dt_ocorrencia: Date lower than the two-month period', 422
        		);
        } elseif ($this->dt_ocorrencia > $dt_max) {
            Error::generateErrorCustom(
            		'Field dt_ocorrencia: Date larger than today', 422
            	);
        }

		return $this;
	}

	public function getNrHoras()
	{
		return $this->nr_horas;
	}

	public function setNrHoras($nr_horas)
	{
		if (!preg_match("/^\d{2,2}:[0-5][0-9]$/", $nr_horas)) {
			Error::generateErrorCustom(
        			'Field nr_horas: Invalid time', 422
        		);
		}		

		$this->nr_horas = $nr_horas;
		return $this;
	}

	public function getDsAcidente()
	{
		return $this->ds_acidente;
	}

	public function setDsAcidente($ds_acidente)
	{
		$this->ds_acidente = $ds_acidente;
		return $this;
	}

	public function getCdParteCorpo()
	{
		return $this->cd_parte_corpo;
	}

	public function setCdParteCorpo($cd_parte_corpo)
	{
		if($cd_parte_corpo >= 13 && $cd_parte_corpo <= 50) {
			$this->cd_parte_corpo = $cd_parte_corpo;
			return $this;
		} else {
			Error::generateErrorCustom(
        			'Field cd_parte_corpo: Invalid code', 422
        		);
		}
	}

	public function getIeSituacaoEpi()
	{
		return $this->ie_situacao_epi;
	}

	public function setIeSituacaoEpi($ie_situacao_epi)
	{
		$this->ie_situacao_epi = $ie_situacao_epi;
		return $this;
	}

	public function getDsEpi()
	{
		return $this->ds_epi;
	}

	public function setDsEpi($ds_epi)
	{
		$this->ds_epi = $ds_epi;
		return $this;
	}

	public function getDsAcidenteEvitado()
	{
		return $this->ds_acidente_evitado;
	}

	public function setDsAcidenteEvitado($ds_acidente_evitado)
	{
		$this->ds_acidente_evitado = $ds_acidente_evitado;
		return $this;
	}

	public function getIeSituacaoComunicado()
	{
		return $this->ie_situacao_comunicado;
	}

	public function setIeSituacaoComunicado($ie_situacao_comunicado)
	{
		$this->ie_situacao_comunicado = $ie_situacao_comunicado;
		return $this;
	}

	public function getCdMotivoAcidente()
	{
		return $this->cd_motivo_acidente;
	}

	public function setCdMotivoAcidente($cd_motivo_acidente)
	{
		if($cd_motivo_acidente >= 51 && $cd_motivo_acidente <= 59) {
			$this->cd_motivo_acidente = $cd_motivo_acidente;
			return $this;
		} else {
			Error::generateErrorCustom(
        			'Field cd_motivo_acidente: Invalid code', 422
        		);
		}		
	}

	public function getDsOutros()
	{
		return $this->ds_outros;
	}

	public function setDsOutros($ds_outros)
	{
		$this->ds_outros = $ds_outros;
		return $this;
	}

	public function getIeSituacaoTestemunha()
	{
		return $this->ie_situacao_testemunha;
	}

	public function setIeSituacaoTestemunha($ie_situacao_testemunha)
	{
		$this->ie_situacao_testemunha = $ie_situacao_testemunha;
		return $this;
	}

	public function getDsTestemunha()
	{
		return $this->ds_testemunha;
	}

	public function setDsTestemunha($ds_testemunha)
	{
		$this->ds_testemunha = $ds_testemunha;
		return $this;
	}

	public function getIeLidoCipa()
	{
		return $this->ie_lido_cipa;
	}

	public function setIeLidoCipa($ie_lido_cipa)
	{
		$this->ie_lido_cipa = $ie_lido_cipa;
		return $this;
	}

	public function getDsParecerCipa()
	{
		return $this->ds_parecer_cipa;
	}

	public function setDsParecerCipa($ds_parecer_cipa)
	{
		$this->ds_parecer_cipa = $ds_parecer_cipa;
		return $this;
	}

	public function getDtParecerCipa($format = 'Y-m-d')
	{
		if ($this->dt_parecer_cipa != null) {
			return $this->dt_parecer_cipa->format($format);
		} else {
			return $this->dt_parecer_cipa;
		}		
	}

	public function setDtParecerCipa($dt_parecer_cipa)
	{
		$this->dt_parecer_cipa = $dt_parecer_cipa;
		return $this;
	}

	public function getNrCrachaCipa()
	{
		return $this->nr_cracha_cipa;
	}

	public function setNrCrachaCipa($nr_cracha_cipa)
	{
		$this->nr_cracha_cipa = $nr_cracha_cipa;
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

	public function getDsLocal()
	{
		return $this->ds_local;
	}

	public function setDsLocal($ds_local)
	{
		$this->ds_local = $ds_local;
		return $this;
	}

	public function getParsedArray() 
	{
		$accidentOccurrence = [];

		if ($this->getNrSequencia() != NULL)
			$accidentOccurrence['nr_sequencia'] = $this->getNrSequencia();

		if ($this->getNrCracha() != NULL)
			$accidentOccurrence['nr_cracha'] = $this->getNrCracha();

		if ($this->getDtOcorrencia() != NULL)
			$accidentOccurrence['dt_ocorrencia'] = $this->getDtOcorrencia();

		if ($this->getNrHoras() != NULL)
			$accidentOccurrence['nr_horas'] = $this->getNrHoras();

		if ($this->getDsAcidente() != NULL)
			$accidentOccurrence['ds_acidente'] = $this->getDsAcidente();

		if ($this->getCdParteCorpo() != NULL)
			 $accidentOccurrence['cd_parte_corpo'] = $this->getCdParteCorpo();

		if ($this->getIeSituacaoEpi() != NULL){
			$accidentOccurrence['ie_situacao_epi'] = $this->getIeSituacaoEpi();
			$accidentOccurrence['ds_epi'] = $this->getDsEpi();
		}	

		if ($this->getDsAcidenteEvitado() != NULL)
			$accidentOccurrence['ds_acidente_evitado'] = $this->getDsAcidenteEvitado();

		if ($this->getIeSituacaoComunicado() != NULL)
			$accidentOccurrence['ie_situacao_comunicado'] = $this->getIeSituacaoComunicado();

		if ($this->getCdMotivoAcidente() != NULL) {
			$accidentOccurrence['cd_motivo_acidente'] = $this->getCdMotivoAcidente();
			$accidentOccurrence['ds_outros'] = $this->getDsOutros();
		}

		if ($this->getIeSituacaoTestemunha() != NULL) {
			$accidentOccurrence['ie_situacao_testemunha'] = $this->getIeSituacaoTestemunha();
			$accidentOccurrence['ds_testemunha'] = $this->getDsTestemunha();
		}

		if ($this->getIeLidoCipa() != NULL)
			$accidentOccurrence['ie_lido_cipa'] = $this->getIeLidoCipa();

		if ($this->getDsParecerCipa() != NULL)
			$accidentOccurrence['ds_parecer_cipa'] = $this->getDsParecerCipa();

		if ($this->getDtParecerCipa() != NULL)
			$accidentOccurrence['dt_parecer_cipa'] = $this->getDtParecerCipa();

		if ($this->getNrCrachaCipa() != NULL)
			$accidentOccurrence['nr_cracha_cipa'] = $this->getNrCrachaCipa();

		if ($this->getDtAtualizacao() != NULL)
			$accidentOccurrence['dt_atualizacao'] = $this->getDtAtualizacao();

		if ($this->getNrCrachaInclusao() != NULL)
			$accidentOccurrence['nr_cracha_inclusao'] = $this->getNrCrachaInclusao();

		if ($this->getDsLocal() != NULL)
			$accidentOccurrence['ds_local'] = $this->getDsLocal();

		return $accidentOccurrence;
	}

	public function setAccidentOccurrenceValidate($accidentOccurrence)
	{	
		if (array_key_exists('nr_cracha', $accidentOccurrence)) {
			$this->setNrCracha($accidentOccurrence['nr_cracha']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
		}

		if (array_key_exists('dt_ocorrencia', $accidentOccurrence)) {
			$this->setDtOcorrencia($accidentOccurrence['dt_ocorrencia']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_ocorrencia']);
		}

		if (array_key_exists('nr_horas', $accidentOccurrence)) {
			$this->setNrHoras($accidentOccurrence['nr_horas']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_horas']);
		}

		if (array_key_exists('ds_acidente', $accidentOccurrence)) {
			$this->setDsAcidente($accidentOccurrence['ds_acidente']);			
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_acidente']);
		}

		if (array_key_exists('cd_parte_corpo', $accidentOccurrence)) {
			$this->setCdParteCorpo($accidentOccurrence['cd_parte_corpo']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_parte_corpo']);
		}

		if (array_key_exists('ie_situacao_epi', $accidentOccurrence)) {
			$this->setIeSituacaoEpi($accidentOccurrence['ie_situacao_epi']);
			
			if($accidentOccurrence['ie_situacao_epi'] == 'S'){
				if(array_key_exists("ds_epi", $accidentOccurrence)){
					$this->setDsEpi($accidentOccurrence['ds_epi']);
				} else {
					Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_epi']);
				}
			}

		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['msgField' => 'ie_situacao_epi']);
		}

		if (array_key_exists('ds_acidente_evitado', $accidentOccurrence)) {
			$this->setDsAcidenteEvitado($accidentOccurrence['ds_acidente_evitado']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['msgField' => 'ds_acidente_evitado']);
		}

		if (array_key_exists('ie_situacao_comunicado', $accidentOccurrence)) {
			$this->setIeSituacaoComunicado($accidentOccurrence['ie_situacao_comunicado']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ie_situacao_comunicado']);
		}

		if (array_key_exists('cd_motivo_acidente', $accidentOccurrence)) {
			$this->setCdMotivoAcidente($accidentOccurrence['cd_motivo_acidente']);
			
			if($accidentOccurrence['cd_motivo_acidente'] == 59){
				if(array_key_exists("ds_outros", $accidentOccurrence)){
					$this->setDsOutros($accidentOccurrence['ds_outros']);
				} else {
					Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_outros']);
				}
			}

		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_motivo_acidente']);
		}

		if (array_key_exists('ie_situacao_testemunha', $accidentOccurrence)) {
			$this->setIeSituacaoTestemunha($accidentOccurrence['ie_situacao_testemunha']);

			if($accidentOccurrence['ie_situacao_testemunha'] == 'S'){
				if(array_key_exists("ds_testemunha", $accidentOccurrence)){
					$this->setDsTestemunha($accidentOccurrence['ds_testemunha']);
				} else {
					Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_testemunha']);
				}
			}

		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ie_situacao_testemunha']);
		}

		if (array_key_exists('nr_cracha_inclusao', $accidentOccurrence)) {
			$this->setNrCrachaInclusao($accidentOccurrence['nr_cracha_inclusao']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha_inclusao']);
		}

		if (array_key_exists('ds_local', $accidentOccurrence)) {
			$this->setDsLocal($accidentOccurrence['ds_local']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_local']);
		}

		$dt_atualizacao = new \DateTime();

		$this->setDtAtualizacao($dt_atualizacao);
		$this->setIeLidoCipa('N');
	}

	public function setAccidentOccurrenceSeemCipaValidate($accidentOccurrence)
	{	
		if (array_key_exists('nr_cracha_cipa', $accidentOccurrence)) {
			$this->setNrCrachaCipa($accidentOccurrence['nr_cracha_cipa']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha_cipa']);
		}

		if (array_key_exists('ds_parecer_cipa', $accidentOccurrence)) {
			$this->setDsParecerCipa($accidentOccurrence['ds_parecer_cipa']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_parecer_cipa']);
		}

		$dt_atualizacao = new \DateTime();
		$dt_parecer_cipa = new \DateTime();

		$this->setDtAtualizacao($dt_atualizacao);
		$this->setDtParecerCipa($dt_parecer_cipa);
		$this->setIeLidoCipa('S');
	}
	
}