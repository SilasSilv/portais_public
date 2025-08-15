<?php 

namespace api\v1\resource\sector\rules;

use \api\v1\vendor\error\Error;

class Sector {

	private $cd_setor;
	private $ds_setor;
	private $cartoes;
	private $dt_inclusao;

	public function getCdSetor()
	{
		return $this->cd_setor;
	}

	public function setCdSetor($cd_setor)
	{
		$this->cd_setor = $cd_setor;
		return $this;
	}

	public function getDsSetor()
	{
		return $this->ds_setor;
	}

	public function setDsSetor($ds_setor)
	{
		$this->ds_setor = $ds_setor;
		return $this;
	}

	public function getCartoes()
	{
		if (is_array($this->cartoes)) {
			return $this->cartoes;
		} else {
			return [];
		}
	}

	public function setCartoes($cartoes)
	{
		$this->cartoes = $cartoes;
		return $this;
	}

	public function getDtInclusao($format = 'Y-m-d H:i:s')
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

	public function getParsedArray()
	{
		$sector = [];

		if ($this->getCdSetor() != NULL) {
			$sector['cd_setor'] = $this->getCdSetor();
		}

		if ($this->getDsSetor() != NULL) {
			$sector['ds_setor'] = $this->getDsSetor();
		}

		if (count($this->getCartoes()) > 0) {
			$sector['cartoes'] = $this->getCartoes();
		}

		if ($this->getDtInclusao() != NULL) {
			$sector['dt_inclusao'] = $this->getDtInclusao();
		}

		return $sector;
	}

	public function setSectorValidate($sector, $method = '')
	{
		$fieldsRequired = '';
		$cardError = '';

		if (array_key_exists('cd_setor', $sector) && $method != 'POST') {
			$this->setCdSetor($sector['cd_setor']);
		} 

		if (array_key_exists('ds_setor', $sector)) {
			$this->setDsSetor($sector['ds_setor']);
		} else {
			$fieldsRequired .= 'Required: ds_setor';
		}

		if (array_key_exists('cartoes', $sector)) {
			if (is_array($sector['cartoes'])) {
				$this->setCartoes($sector['cartoes']);
			} else {
				$cardError = 'Card type must be array';
			}
		}

		if ($method == 'POST') {
			$dt_inclusao = new \DateTime();
			$this->setDtInclusao($dt_inclusao);
		}

		if (strlen($fieldsRequired) > 0 || strlen($cardError) > 0) {
			$error = "$fieldsRequired. $cardError";
			Error::generateErrorCustom($error, 422);
		}
	}
}