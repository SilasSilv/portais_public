<?php 
namespace api\v1\resource\category\rules;

use \api\v1\vendor\error\Error;

Class Category {

	private $cd_categoria;
	private $cd_tipo_categoria;
	private $ds_categoria;

	public function getCdCategoria()
	{
		return $this->cd_categoria;	
	}

	public function setCdCategoria($cd_categoria)
	{
		$this->cd_categoria = $cd_categoria;
		return $this;
	}

	public function getCdTipoCategoria()
	{
		return $this->cd_tipo_categoria;
	}

	public function setCdTipoCategoria($cd_tipo_categoria)
	{
		$this->cd_tipo_categoria = $cd_tipo_categoria;
		return $this;
	}

	public function getDsCategoria()
	{
		return $this->ds_categoria;
	}

	public function setDsCategoria($ds_categoria)
	{
		$this->ds_categoria = $ds_categoria;
		return $this;
	}

	public function getParsedArray() 
	{
		$category = [];

		if ($this->getCdCategoria() != NULL)
			$category['cd_categoria'] = $this->getCdCategoria();

		if ($this->getCdTipoCategoria() != NULL)
			$category['cd_tipo_categoria'] = $this->getCdTipoCategoria();

		if ($this->getDsCategoria() != NULL)
			$category['ds_categoria'] = $this->getDsCategoria();

		return $category;
	}


	public function setCategoryValidate($category)
	{	

		if (array_key_exists('cd_tipo_categoria', $category)) {
			$this->setCdTipoCategoria($category['cd_tipo_categoria']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' cd_tipo_categoria']);
		}

		if (array_key_exists('ds_categoria', $category)) {
			$this->setDsCategoria($category['ds_categoria']);
		} else {
			Error::generateErrorApi(Error::REQUIRED_FIELD, ['observation' => ' ds_categoria']);
		}
	
	}
		
}