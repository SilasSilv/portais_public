<?php
namespace api\v1\resource\category\rules;

use \api\v1\vendor\error\Error;

Class CategoryType
{
    private $cd_tipo_categoria;
    private $ds_tipo_categoria;

    public function getCdTipoCategoria()
    {
        return $this->cd_tipo_categoria;
    }

    public function setCdTipoCategoria($cd_tipo_categoria)
    {
        return $this->cd_tipo_categoria = $cd_tipo_categoria;
    }

    public function getDsTipoCategoria()
    {
        return $this->ds_tipo_categoria;
    }

    public function setDsTipoCategoria($ds_tipo_categoria)
    {
        return $this->ds_tipo_categoria = $ds_tipo_categoria;
    }

    public function getParsedArray() 
	{
		$category_type = [];

		if ($this->getCdTipoCategoria() != NULL) {
            $category_type['cd_tipo_categoria'] = $this->getCdTipoCategoria();
        }

		if ($this->getDsTipoCategoria() != NULL) {
            $category_type['ds_tipo_categoria'] = $this->getDsTipoCategoria();
        }

		return $category_type;
	}


	public function setCategoryTypeValidate($category_type)
	{	
        $fieldsRequired = '';

		if (array_key_exists('ds_tipo_categoria', $category_type)) {
			$this->setDsTipoCategoria($category_type['ds_tipo_categoria']);
		} else {
			$fieldsRequired .= strlen($fieldsRequired) > 0 ? ',ds_tipo_categoria' : 'Required: ds_tipo_categoria';
        }
        
        if (strlen($fieldsRequired) > 0) {
			$error = "$fieldsRequired";
			Error::generateErrorCustom($error, 422);
		}	
	}
}