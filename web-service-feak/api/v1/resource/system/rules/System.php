<?php
namespace api\v1\resource\system\rules;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\Image;
use \api\v1\resource\system\rules\SystemDAO;

class System 
{
    private $cd_token;
    private $nm_sistema;
    private $ds_sistema;
    private $img_logo;
    private $img_logo_delete;
    private $ie_situacao;

    public function getCdToken()
    {
        return $this->cd_token;
    }

    public function setCdToken($cd_token)
    {
        $this->cd_token = $cd_token;
        return $this;
    }

    public function getNmSistema()
    {
        return $this->nm_sistema;
    }

    public function setNmSistema($nm_sistema)
    {
        $this->nm_sistema = $nm_sistema;
        return $this;
    }

    public function getDsSistema()
    {
        return $this->ds_sistema;
    }

    public function setDsSistema($ds_sistema)
    {
        $this->ds_sistema = $ds_sistema;
        return $this;
    }

    public function getImgLogo()
    {
        return $this->img_logo;
    }

    public function setImgLogo($img_logo)
    {   
        if ($img_logo !== '') {
            $extension = pathinfo($img_logo->getClientFilename(), PATHINFO_EXTENSION);        
            $hash = md5(rand() . (new \DateTime())->format('Y-m-d H:i:s'));
            $targetPath = "img/system/{$hash}.$extension";

            $img_logo->moveTo($targetPath);

            $image = new Image($targetPath);
	        $image->treatImage(['width' => 100, 'height' => 100]);

            $this->img_logo = $targetPath;
        } elseif ($img_logo === '') {
            $this->img_logo = $img_logo;
        }        
    
        return $this;
    }

    public function getImgLogoDelete()
    {
        return $this->img_logo_delete;
    }

    public function setImgLogoUpdate($req, $cd_sistema)
    {
        $systemDAO = new SystemDAO();

        $img_logo = $systemDAO->find(["cd_sistema" => $cd_sistema, "fields" => "img_logo"]);
        $this->img_logo_delete = count($img_logo) == 1 ? $img_logo[0]["img_logo"] : NULL;

        if (array_key_exists("img_logo", $req->getUploadedFiles())) {            
            $this->setImgLogo($req->getUploadedFiles()["img_logo"]);	
        } elseif (array_key_exists("img_logo", $req->getParsedBody())) {
            if ($req->getParsedBody()["img_logo"] == "") {
                $this->setImgLogo("");
            } else {
                $this->img_logo_delete = NULL;
            }
        } else {
            $this->img_logo_delete = NULL;
        }      

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

    public function getParsedArray($action='')
	{
		$system = [];

		if ($this->getCdToken() != NULL) {
			$system['cd_token'] = $this->getCdToken();
		}

		if ($this->getNmSistema() != NULL) {
			$system['nm_sistema'] = $this->getNmSistema();
		}

		if (count($this->getDsSistema()) > 0) {
			$system['ds_sistema'] = $this->getDsSistema();
		}

		if ($this->getImgLogo() === '') {
			$system['img_logo'] = NULL;
        } elseif ($this->getImgLogo() !== NULL) {
            $system['img_logo'] = $this->getImgLogo(); 
        }

        if ($this->getImgLogoDelete() != NULL && $action === "UPDATE") {
			$system['img_logo_delete'] = $this->getImgLogoDelete();
		}
        
        if ($this->getIeSituacao() != NULL) {
			$system['ie_situacao'] = $this->getIeSituacao();
        }
        
		return $system;
	}

	public function setSystemValidate($system, $action='')
	{
		$fieldsRequired = '';
        
        if (array_key_exists('cd_token', $system)) {
			$this->setCdToken($system['cd_token']);
		} else {
			$fieldsRequired .= 'Required: cd_token';
		}

		if (array_key_exists('nm_sistema', $system)) {
			$this->setNmSistema($system['nm_sistema']);
		} else {
            $fieldsRequired .= strlen($fieldsRequired) > 0 ? ',nm_sistema' : 'Required: nm_sistema';
		}

		if (array_key_exists('ds_sistema', $system)) {
			$this->setDsSistema($system['ds_sistema']);
		} else {
            $fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ds_sistema' : 'Required: ds_sistema';
        }
        
        if (array_key_exists('ie_situacao', $system)) {
			$this->setIeSituacao($system['ie_situacao']);
		} else {
            $fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ie_situacao' : 'Required: ie_situacao';
        }

        if (array_key_exists('img_logo', $system) && $action === "CREATE") {
            if (strlen($fieldsRequired) === 0) {
                $this->setImgLogo($system['img_logo']);
            }            
		} 
        
        if (strlen($fieldsRequired) > 0) {
			$error = "$fieldsRequired";
			Error::generateErrorCustom($error, 422);
		}
	}
}