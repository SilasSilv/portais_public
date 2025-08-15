<?php

namespace api\v1\resource\meal\rules\meal;

use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;


Class Meal {

	private $nr_refeicao;
	private $ie_tipo_refeicao;
    private $dt_refeicao;
    private $ds_refeicao;
    private $dt_inicio;
    private $dt_final;
    private $nr_cracha;
    private $dt_atualizacao;
    private $ie_situacao;
    private $ie_feriado;

    public function getNrRefeicao()
    {
    	return $this->nr_refeicao;
    }

    public function setNrRefeicao($nr_refeicao)
    {
    	$this->nr_refeicao = $nr_refeicao;
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
        $dt_min = new \DateTime();
        $dt_max = new \DateTime();

        $dt_max->modify('+2 month');
        $dt_min->setTime(0, 0, 0);
        $dt_max->setTime(0, 0, 0);
        $this->dt_refeicao->setTime(0, 0, 0);

        if ($this->dt_refeicao < $dt_min) {
            Error::generateErrorCustom(
                    'Field dt_refeicao: Date lower than today', 422
                );
        } elseif ($this->dt_refeicao > $dt_max) {
            Error::generateErrorCustom(
                    'Field dt_refeicao: Date exceeding two weeks', 422
                );
        }

    	return $this;
    }

    public function getDsRefeicao()
    {
    	return $this->ds_refeicao;
    }

    public function setDsRefeicao($ds_refeicao)
    {
    	$this->ds_refeicao = $ds_refeicao;
    	return $this;
    }

    public function getDtInicio($format = 'Y-m-d H:i:s')
    {
        if ($this->dt_inicio != null) {
            return $this->dt_inicio->format($format);
        } else {
            return $this->dt_inicio;             
        }
    }

    public function setDtInicio($dt_inicio)
    {  
        $dt_inicio = DateUtils::convert($dt_inicio, "BR-DATETIME", ["name_field" => "dt_inicio"]);
    	$dt_min = new \DateTime();
        $dt_refeicao = \DateTime::createFromFormat('Y-m-d', $this->getDtRefeicao());

        $dt_min->modify('-2 month');
        $dt_min->setTime(0, 0, 0);
        $dt_refeicao->setTime(23, 59, 59);

    	if ($dt_inicio < $dt_min) {
            Error::generateErrorCustom(
                    'Field dt_inicio: Date less than two months', 422
                );
    	} elseif ($dt_inicio > $dt_refeicao) {
            Error::generateErrorCustom(
                    'Field dt_inicio: Date greater than dt_refeicao', 422
                );
    	}
    
    	$this->dt_inicio = $dt_inicio;
    	return $this;  
    }

    public function getDtFinal($format = 'Y-m-d H:i:s')
    {
        if ($this->dt_final != null) {
            return $this->dt_final->format($format);
        } else {
            return $this->dt_final;             
        }
    }

    public function setDtFinal($dt_final)
    {
        $dt_final = DateUtils::convert($dt_final, "BR-DATETIME", ["name_field" => "dt_final"]);
        $dt_min = new \DateTime();
        $dt_inicio = \DateTime::createFromFormat('Y-m-d H:i:s', $this->getDtInicio());
        $dt_refeicao = \DateTime::createFromFormat('Y-m-d', $this->getDtRefeicao());

        $dt_min->setTime(0, 0, 0);
        $dt_refeicao->setTime(23, 59, 59);

    	if ($dt_final < $dt_min) {
            Error::generateErrorCustom(
                    'Field dt_final: Date lower than today', 422
                );
        } elseif ($dt_final < $dt_inicio) {
            Error::generateErrorCustom(
                    'Field dt_final: End date lower than start date', 422
                );
    	} elseif ($dt_final > $dt_refeicao) {
            Error::generateErrorCustom(
                    'Field dt_final: End date greater than meal date', 422
                );
    	}

    	$this->dt_final = $dt_final;
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

    public function getIeSituacao()
    {
    	return $this->ie_situacao;
    }

    public function setIeSituacao($ie_situacao)
    {
    	$this->ie_situacao = $ie_situacao;
    	return $this;
    }

    public function getIeFeriado()
    {
        return $this->ie_feriado;
    }

    public function setIeFeriado($ie_feriado)
    {   
        if (preg_match('/S|N/', $ie_feriado)) {
            $this->ie_feriado = $ie_feriado;
        } else {
            Error::generateErrorCustom('Field ie_feriado: Value invalid!', 422);
        }

        return $this;
    }

    public function getParsedArray()
    {
    	$meal = [];

    	if($this->getNrRefeicao() != NULL)
    		$meal['nr_refeicao'] = $this->getNrRefeicao();

    	if($this->getIeTipoRefeicao() != NULL)
    		$meal['ie_tipo_refeicao'] = $this->getIeTipoRefeicao();

    	if($this->getDtRefeicao() != NULL)
    		$meal['dt_refeicao'] = $this->getDtRefeicao();

    	if($this->getDsRefeicao() != NULL)
    		$meal['ds_refeicao'] = $this->getDsRefeicao();

    	if($this->getDtInicio() != NULL)
    		$meal['dt_inicio'] = $this->getDtInicio();

    	if($this->getDtFinal() != NULL)
    		$meal['dt_final'] = $this->getDtFinal();

    	if($this->getNrCracha() != NULL)
    		$meal['nr_cracha'] = $this->getNrCracha();

    	if($this->getDtAtualizacao() != NULL)
    		$meal['dt_atualizacao'] = $this->getDtAtualizacao();

    	if($this->getIeSituacao() != NULL)
    		$meal['ie_situacao'] = $this->getIeSituacao();

        if($this->getIeFeriado() != NULL)
            $meal['ie_feriado'] = $this->getIeFeriado();

    	return $meal;
    }

    public function setMealValidate($meal)
    {
    	
    	if(array_key_exists('nr_refeicao', $meal)) {
    		$this->setNrRefeicao($meal['nr_refeicao']);    	
        } 

        if (array_key_exists('ie_tipo_refeicao', $meal)) {
            $this->setIeTipoRefeicao($meal['ie_tipo_refeicao']);
        } else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ie_tipo_refeicao']);
        }
    
    	if(array_key_exists('dt_refeicao', $meal)) {
    		$this->setDtRefeicao($meal['dt_refeicao']);
       	} else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_refeicao']);
        }

    	if(array_key_exists('ds_refeicao', $meal)) {
    		$this->setDsRefeicao($meal['ds_refeicao']);
    	} else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_refeicao']);
        }

    	if(array_key_exists('dt_inicio', $meal)) {
    		$this->setDtInicio($meal['dt_inicio']);
    	} else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_inicio']);
        }

    	if(array_key_exists('dt_final', $meal)) {
    		$this->setDtFinal($meal['dt_final']);
    	} else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' dt_final']);
        }

    	if(array_key_exists('nr_cracha', $meal)) {
    		$this->setNrCracha($meal['nr_cracha']);
    	} else {
            Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
        }

    	if(array_key_exists('ie_situacao', $meal)) {
    		$this->setIeSituacao($meal['ie_situacao']);
    	} else {
            $this->setIeSituacao('A');
        }

        if(array_key_exists('ie_feriado', $meal)) {
            $this->setIeFeriado($meal['ie_feriado']);
        } else {
            $this->setIeFeriado('N');
        }

        $dt_atualizacao = new \DateTime();        
        $this->setDtAtualizacao($dt_atualizacao);
    }
}