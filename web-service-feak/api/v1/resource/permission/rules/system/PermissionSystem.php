<?php
namespace api\v1\resource\permission\rules\system;

use \api\v1\vendor\utils\Image;
use \api\v1\vendor\error\Error;

class PermissionSystem 
{
    private $cd_permissao;
    private $cd_sistema;
    private $cd_tipo_permissao;
    private $ds_titulo;
    private $ds_descricao;
    private $vl_padrao;
    private $ie_mostrar_cliente;
    private $ds_descricao_cliente;
    private $ie_mostrar_parametro;
    private $url_acesso;
    private $nm_apelido_acesso;
    private $ie_situacao;

    /**
     * Get the value of cd_permissao
     */ 
    public function getCdPermissao()
    {
        return $this->cd_permissao;
    }

    /**
     * Set the value of cd_permissao
     *
     * @return  self
     */ 
    public function setCdPermissao($cd_permissao)
    {
        $this->cd_permissao = $cd_permissao;

        return $this;
    }

    /**
     * Get the value of cd_sistema
     */ 
    public function getCdSistema()
    {
        return $this->cd_sistema;
    }

    /**
     * Set the value of cd_sistema
     *
     * @return  self
     */ 
    public function setCdSistema($cd_sistema)
    {
        $this->cd_sistema = $cd_sistema;

        return $this;
    }

    /**
     * Get the value of cd_tipo_permissao
     */ 
    public function getCdTipoPermissao()
    {
        return $this->cd_tipo_permissao;
    }

    /**
     * Set the value of cd_tipo_permissao
     *
     * @return  self
     */ 
    public function setCdTipoPermissao($cd_tipo_permissao)
    {
        $this->cd_tipo_permissao = $cd_tipo_permissao;

        return $this;
    }

    /**
     * Get the value of ds_titulo
     */ 
    public function getDsTitulo()
    {
        return $this->ds_titulo;
    }

    /**
     * Set the value of ds_titulo
     *
     * @return  self
     */ 
    public function setDsTitulo($ds_titulo)
    {
        $this->ds_titulo = $ds_titulo;

        return $this;
    }

    /**
     * Get the value of ds_descricao
     */ 
    public function getDsDescricao()
    {
        return $this->ds_descricao;
    }

    /**
     * Set the value of ds_descricao
     *
     * @return  self
     */ 
    public function setDsDescricao($ds_descricao)
    {
        $this->ds_descricao = $ds_descricao;

        return $this;
    }

    /**
     * Get the value of vl_padrao
     */ 
    public function getVlPadrao()
    {
        return $this->vl_padrao;
    }

    /**
     * Set the value of vl_padrao
     *
     * @return  self
     */ 
    public function setVlPadrao($vl_padrao)
    {
        $this->vl_padrao = $vl_padrao;

        return $this;
    }

    /**
     * Get the value of ie_mostrar_cliente
     */ 
    public function getIeMostrarCliente()
    {
        return $this->ie_mostrar_cliente;
    }

    /**
     * Set the value of ie_mostrar_cliente
     *
     * @return  self
     */ 
    public function setIeMostrarCliente($ie_mostrar_cliente)
    {
        $this->ie_mostrar_cliente = $ie_mostrar_cliente;

        return $this;
    }

    /**
     * Get the value of ds_descricao_cliente
     */ 
    public function getDsDescricaoCliente()
    {
        return $this->ds_descricao_cliente;
    }

    /**
     * Set the value of ds_descricao_cliente
     *
     * @return  self
     */ 
    public function setDsDescricaoCliente($ds_descricao_cliente)
    {
        if ($ds_descricao_cliente == NULL && $this->ie_mostrar_cliente == 'S') 
        {
            Error::generateErrorCustom('Field cannot be null ds_descricao_cliente when the ie_show_client is yes', 422);
        }


        $this->ds_descricao_cliente = $ds_descricao_cliente;

        return $this;
    }

    /**
     * Get the value of ie_mostrar_parametro
     */ 
    public function getIeMostrarParametro()
    {
        return $this->ie_mostrar_parametro;
    }

    /**
     * Set the value of ie_mostrar_parametro
     *
     * @return  self
     */ 
    public function setIeMostrarParametro($ie_mostrar_parametro)
    {
        $this->ie_mostrar_parametro = $ie_mostrar_parametro;

        return $this;
    }

    /**
     * Get the value of url_acesso
     */ 
    public function getUrlAcesso()
    {
        return $this->url_acesso;
    }

    /**
     * Set the value of url_acesso
     *
     * @return  self
     */ 
    public function setUrlAcesso($url_acesso)
    {
        $this->url_acesso = $url_acesso;

        return $this;
    }

    /**
     * Get the value of url_acesso
     */ 
    public function getNmApelidoAcesso()
    {
        return $this->nm_apelido_acesso;
    }

    /**
     * Set the value of url_acesso
     *
     * @return  self
     */ 
    public function setNmApelidoAcesso($nm_apelido_acesso)
    {
        $this->nm_apelido_acesso = $nm_apelido_acesso;

        return $this;
    }

    /**
     * Get the value of ie_situacao
     */ 
    public function getIeSituacao()
    {
        return $this->ie_situacao;
    }

    /**
     * Set the value of ie_situacao
     *
     * @return  self
     */ 
    public function setIeSituacao($ie_situacao)
    {
        $this->ie_situacao = $ie_situacao;

        return $this;
    }

    public function setAlternativeImage($files)
    {
        if (array_key_exists('logo_alternativo_acesso', $files)) {
            $image = $files['logo_alternativo_acesso'];
            $mediaType = $image->getClientMediaType();

            switch ($mediaType) {
                case 'image/jpeg': break;
                case 'image/png': break;
                default: 
                    Error::generateErrorCustom("Media type $mediaType not acceptable", 422);     
            }

            $extension = pathinfo($image->getClientFilename(), PATHINFO_EXTENSION);        
            $hash = md5(rand() . (new \DateTime())->format('Y-m-d H:i:s'));
            $targetPath = "img/system/{$hash}.$extension";

            $image->moveTo($targetPath);

            $image = new Image($targetPath);
            $image->treatImage(['width' => 100, 'height' => 100]);

            return ['logo_alternativo_acesso' => $targetPath];
        } else {
            Error::generateErrorCustom('Required: logo_alternativo_acesso', 422);
        }
    }

    public function getParsedArray()
    {
        $permission = [];

        if ($this->getCdSistema() != NULL) {
            $permission['cd_sistema'] = $this->getCdSistema();
        }

        if ($this->getCdTipoPermissao() != NULL) {
            $permission['cd_tipo_permissao'] = $this->getCdTipoPermissao();
        }

        if ($this->getDsTitulo() != NULL) {
            $permission['ds_titulo'] = $this->getDsTitulo();
        }

        if ($this->getDsDescricao() != NULL) {
            $permission['ds_descricao'] = $this->getDsDescricao();
        }

        if ($this->getVlPadrao() != NULL) {
            $permission['vl_padrao'] = $this->getVlPadrao();
        }

        if ($this->getIeMostrarCliente() != NULL) {
            $permission['ie_mostrar_cliente'] = $this->getIeMostrarCliente();
        }

        if ($this->getDsDescricaoCliente() === '') {
            $permission['ds_descricao_cliente'] = null;
        } elseif ($this->getDsDescricaoCliente() != NULL) {
            $permission['ds_descricao_cliente'] = $this->getDsDescricaoCliente();
        }

        if ($this->getIeMostrarParametro() != NULL) {
            $permission['ie_mostrar_parametro'] = $this->getIeMostrarParametro();
        }

        if ($this->getUrlAcesso() === '') {
            $permission['url_acesso'] = null;
        } elseif ($this->getUrlAcesso() != NULL) {
            $permission['url_acesso'] = $this->getUrlAcesso();
        }

        if ($this->getNmApelidoAcesso() === '') {
            $permission['nm_apelido_acesso'] = null;
        } elseif ($this->getUrlAcesso() != NULL) {
            $permission['nm_apelido_acesso'] = $this->getNmApelidoAcesso();
        }

        if ($this->getIeSituacao() != NULL) {
            $permission['ie_situacao'] = $this->getIeSituacao();
        }

        return $permission;
    }

    public function setPermissionValidate($permission)
    {
        $fieldsRequired = '';
        
        if (array_key_exists('cd_sistema', $permission)) {
			$this->setCdSistema($permission['cd_sistema']);
		} else {
			$fieldsRequired .= 'Required: cd_sistema';
        }

        if (array_key_exists('cd_tipo_permissao', $permission)) {
			$this->setCdTipoPermissao($permission['cd_tipo_permissao']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',cd_tipo_permissao' : 'Required: cd_tipo_permissao';
        }

        if (array_key_exists('ds_titulo', $permission)) {
			$this->setDsTitulo($permission['ds_titulo']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ds_titulo' : 'Required: ds_titulo';
        }

        if (array_key_exists('ds_descricao', $permission)) {
			$this->setDsDescricao($permission['ds_descricao']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ds_descricao' : 'Required: ds_descricao';
        }

        if (array_key_exists('vl_padrao', $permission)) {
			$this->setVlPadrao($permission['vl_padrao']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',vl_padrao' : 'Required: vl_padrao';
        }

        if (array_key_exists('ie_mostrar_cliente', $permission)) {
			$this->setIeMostrarCliente($permission['ie_mostrar_cliente']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ie_mostrar_cliente' : 'Required: ie_mostrar_cliente';
        }

        if (array_key_exists('ds_descricao_cliente', $permission)) {
			$this->setDsDescricaoCliente($permission['ds_descricao_cliente']);
        } elseif ($this->getIeMostrarCliente() == 'S') {
            $fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ds_descricao_cliente' : 'Required: ds_descricao_cliente';
        }
        
        if (array_key_exists('ie_mostrar_parametro', $permission)) {
			$this->setIeMostrarParametro($permission['ie_mostrar_parametro']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ie_mostrar_parametro' : 'Required: ie_mostrar_parametro';
        }

        if (array_key_exists('url_acesso', $permission)) {
			$this->setUrlAcesso($permission['url_acesso']);
        } 
        
        if (array_key_exists('nm_apelido_acesso', $permission)) {
			$this->setNmApelidoAcesso($permission['nm_apelido_acesso']);
        } 

        if (array_key_exists('ie_situacao', $permission)) {
			$this->setIeSituacao($permission['ie_situacao']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ie_situacao' : 'Required: ie_situacao';
        }        

        if (strlen($fieldsRequired) > 0) {
			$error = "$fieldsRequired";
            Error::generateErrorCustom($error, 422);
		}
    }    
}