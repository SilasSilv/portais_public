<?php

namespace api\v1\resource\people\rules;

use \api\v1\vendor\utils\PasswordUtils;
use \api\v1\vendor\utils\DateUtils;
use \api\v1\vendor\error\Error;

Class People {

	private $nr_cracha;
	private $nm_pessoa_fisica;
	private $ds_login_alternativo;
	private $url_foto_perfil;
	private $ds_mail;
	private $ds_senha;
	private $dt_inclusao;
	private $cd_setor;
	private $cd_cargo;
	private $cd_grupo;
	private $nr_cartao;
	private $dt_demissao;
	private $ie_alterar_senha;
	private $ie_situacao;

	
	public function getNrCracha() 
	{
		return $this->nr_cracha;		
	}

	public function setNrCracha($nr_cracha) 
	{
		$this->nr_cracha = $nr_cracha;
		return $this;		
	}

	public function getNmPessoaFisica() 
	{
		return $this->nm_pessoa_fisica;		
	}

	public function setNmPessoaFisica($nm_pessoa_fisica) 
	{
		$this->nm_pessoa_fisica = $nm_pessoa_fisica;
		return $this;		
	}

	public function getDsLoginAlternativo() 
	{
		return $this->ds_login_alternativo;		
	}

	public function setDsLoginAlternativo($ds_login_alternativo) 
	{
		$this->ds_login_alternativo = $ds_login_alternativo;

		return $this;		
	}

	public function getUrlFotoPerfil() 
	{		
		return $this->url_foto_perfil;	
	}

	public function setUrlFotoPerfil($url_foto_perfil) 
	{
		$this->url_foto_perfil = $url_foto_perfil;
		return $this;		
	}

	public function getDsMail() 
	{
		return $this->ds_mail;		
	}

	public function setDsMail($ds_mail) 
	{
		$this->ds_mail = $ds_mail;

		return $this;		
	}

	public function getDsSenha() 
	{
		return $this->ds_senha;		
	}

	public function setDsSenha($ds_senha, $ds_senha_confirma) 
	{	
		if ($ds_senha != $ds_senha_confirma) {
			Error::generateErrorCustom('Password different', 422);
		} elseif (PasswordUtils::checkStrength($ds_senha) < 30) {
			Error::generateErrorCustom('Weak password', 422);
		} else {
			$this->ds_senha = $ds_senha;
			return $this;
		}		
	}

	public function getDtInclusao($format = 'Y-m-d') 
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

	public function getCdSetor() 
	{
		return $this->cd_setor;		
	}

	public function setCdSetor($cd_setor) 
	{
		$this->cd_setor = $cd_setor;
		return $this;		
	}

	public function getCdCargo() 
	{
		return $this->cd_cargo;		
	}

	public function setCdCargo($cd_cargo) 
	{
		$this->cd_cargo = $cd_cargo;
		return $this;		
	}

	public function getCdGrupo() 
	{
		return $this->cd_grupo;		
	}

	public function setCdGrupo($cd_grupo) 
	{
		$this->cd_grupo = $cd_grupo;		
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

	public function getDtDemissao($format = 'Y-m-d') 
	{	
		if ($this->dt_demissao === "" || $this->dt_demissao == null) {
			return $this->dt_demissao;
		} else {
			return $this->dt_demissao->format($format);
		}
	}

	public function setDtDemissao($dt_demissao) 
	{	
		if ($dt_demissao === "") {
			$this->dt_demissao = $dt_demissao;
		} else {
			$this->dt_demissao = DateUtils::convert($dt_demissao, "BR-DATE", ["name_field" => "dt_demissao"]);		
			return $this;	
		}	
	}

	public function getIeAlterarSenha() 
	{
		return $this->ie_alterar_senha;
	}

	public function setIeAlterarSenha($ie_alterar_senha)
	{
		$this->ie_alterar_senha = $ie_alterar_senha;
		return $this;
	}

	public function getIeSituacao()
	{
		return $this->ie_situacao;		
	}

	public function setIeSituacao($ie_situacao) 
	{	
		if ($this->getDtDemissao() == "" ||  $this->getDtDemissao() == NULL) {
			$this->ie_situacao = $ie_situacao;
		} else {
			$this->ie_situacao = "I";
		}

		return $this;		
	}

	 public function getParsedArray()
    {
    	$people = [];  	  	

    	if ($this->getNrCracha() != NULL) {
    		$people['nr_cracha'] = $this->getNrCracha();
    	}

    	if ($this->getNmPessoaFisica() != NULL) {
    		$people['nm_pessoa_fisica'] = $this->getNmPessoaFisica();
		}
		
		if ($this->getDsLoginAlternativo() === "") {
			$people['ds_login_alternativo'] = NULL;
		} elseif ($this->getDsLoginAlternativo() != NULL) {
			$people['ds_login_alternativo'] = $this->getDsLoginAlternativo();
		}
		
		if ($this->getUrlFotoPerfil() != NULL) {
    		$people['url_foto_perfil'] = $this->getUrlFotoPerfil();
    	}
 
		if ($this->getDsMail() === "") {
			$people['ds_mail'] = NULL;
		} elseif ($this->getDsMail() != NULL) {
			$people['ds_mail'] = $this->getDsMail();
		}

    	if ($this->getDsSenha() != NULL) {
    		$people['ds_senha'] = $this->getDsSenha();
    	}

    	if ($this->getDtInclusao() != NULL) {
    		$people['dt_inclusao'] = $this->getDtInclusao();
    	}

    	if ($this->getCdSetor() != NULL) {
    		$people['cd_setor'] = $this->getCdSetor();
    	}

    	if ($this->getCdCargo() != NULL) {
    		$people['cd_cargo'] = $this->getCdCargo();
		}
		
		if ($this->getCdGrupo() === "") {				
			$people['cd_grupo'] = NULL;
		} elseif ($this->getCdGrupo() != NULL) {
			$people['cd_grupo'] = $this->getCdGrupo(); 
		} 

    	if ($this->getNrCartao() != NULL || $this->getNrCartao() === "") {
    		$people['nr_cartao'] = $this->getNrCartao();  
		}
		
		if ($this->getDtDemissao() === "") {				
			$people['dt_demissao'] = NULL;
		} elseif ($this->getDtDemissao() != NULL) {
			$people['dt_demissao'] = $this->getDtDemissao(); 
		}

    	if ($this->getIeAlterarSenha() != NULL) {
    		$people['ie_alterar_senha'] = $this->getIeAlterarSenha();
    	}

    	if ($this->getIeSituacao() != NULL) {
    		$people['ie_situacao'] = $this->getIeSituacao();
    	}

    	return $people;
    }

    public function setPeopleValidate($people, $method)
    {
    	$requiredField = 0;

    	if (array_key_exists('nr_cracha', $people)) {
    		$this->setNrCracha($people['nr_cracha']);   	
        } elseif ($method == "POST") {
        	Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nr_cracha']);
		}
    
    	if (array_key_exists('nm_pessoa_fisica', $people)) {
    		$this->setNmPessoaFisica($people['nm_pessoa_fisica']);
       	} else {
       		Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' nm_pessoa_fisica']);
		}

    	if (array_key_exists('ds_login_alternativo', $people)) {
    		$this->setDsLoginAlternativo($people['ds_login_alternativo']);
		}

		if (array_key_exists('url_foto_perfil', $people)) {
			if ($people['url_foto_perfil']	== 'img/people/default.jpg') {
				$this->setUrlFotoPerfil($people['url_foto_perfil']);
			}		
    	}

    	if (array_key_exists('ds_mail', $people)) {
    		$this->setDsMail($people['ds_mail']);
    	}

    	if (array_key_exists('cd_setor', $people)) {
    		$this->setCdSetor($people['cd_setor']);
    	} else {
    		Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_setor']);
		}

    	if (array_key_exists('cd_cargo', $people)) {
    		$this->setCdCargo($people['cd_cargo']);
    	} else {
    		Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_cargo']);
		}

    	if (array_key_exists('cd_grupo', $people)) {
    		$this->setCdGrupo($people['cd_grupo']);
    	}

    	if (array_key_exists('nr_cartao', $people)) {
    		$this->setNrCartao($people['nr_cartao']);
    	}

    	if (array_key_exists('dt_demissao', $people)) {
    		$this->setDtDemissao($people['dt_demissao']);
    	}

    	if (array_key_exists('ie_situacao', $people)) {
    		$this->setIeSituacao($people['ie_situacao']);
    	} else {
    		Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ie_situacao']);
		}

		if ($method == "POST") {
			$this->ds_senha = $this->getNrCracha() . 123;
			$this->setIeAlterarSenha('S');
			$this->setUrlFotoPerfil('img/people/default.jpg');
		} elseif ($method == "PUT" && array_key_exists('ie_alterar_senha', $people)) {
			if ($people['ie_alterar_senha'] == 'S') {
				$this->ds_senha = $this->getNrCracha() . 123;
				$this->setIeAlterarSenha('S');
			}
		}
    }

    public function setPasswordValidate($people) {
    	if (array_key_exists('ds_senha', $people)) {
    		if (array_key_exists('ds_senha_confirma', $people)) {
    			$this->setDsSenha($people['ds_senha'], $people['ds_senha_confirma']);
    		} else {
    			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_senha_confirma']);
    		}
    	} else {
    		Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_senha']);
		}
		
		$this->setIeAlterarSenha('N');
    }    
}