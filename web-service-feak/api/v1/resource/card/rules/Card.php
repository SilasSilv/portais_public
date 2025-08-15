<?php 
namespace api\v1\resource\card\rules;

use \api\v1\vendor\error\Error;

class Card {
	private $nr_cartao;
	private $nr_cracha;
	private $cd_setor;
	private $ie_passe_livre_catraca;
	private $ie_situacao;

	public function getNrCartao()
	{
		return $this->nr_cartao;
	}

	public function setNrCartao($nr_cartao)
	{
		$this->nr_cartao = $nr_cartao;
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

	public function getCdSetor()
	{
		return $this->cd_setor;
	}

	public function setCdSetor($cd_setor)
	{	
		$this->cd_setor = $cd_setor;
		return $this;
	}

	public function getIePasseLivreCatraca()
	{
		return $this->ie_passe_livre_catraca;
	}

	public function setIePasseLivreCatraca($ie_passe_livre_catraca)
	{
		if ($ie_passe_livre_catraca != 'S' && $ie_passe_livre_catraca != 'N') {
			 Error::generateErrorCustom('Field ie_passe_livre_catraca: Value invalid', 422);
		}

		$this->ie_passe_livre_catraca = $ie_passe_livre_catraca;
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
		$card = [];

		if ($this->getNrCartao() != NULL) {
			$card['nr_cartao'] = $this->getNrCartao();
		}
	
		if ($this->getNrCracha() !== NULL) {
			if ($this->getNrCracha() === '') {
				$card['nr_cracha'] = NULL;
			} else {
				$card['nr_cracha'] = $this->getNrCracha();
			}
		}

		if ($this->getCdSetor() !== NULL) {
			if ($this->getCdSetor() === '') {
				$card['cd_setor'] = NULL;
			} else {
				$card['cd_setor'] = $this->getCdSetor();
			}
		}

		if ($this->getIePasseLivreCatraca() != NULL) {
			$card['ie_passe_livre_catraca'] = $this->getIePasseLivreCatraca();
		}

		if ($this->getIeSituacao() != NULL) {
			$card['ie_situacao'] = $this->getIeSituacao();
		}

		return $card;
	}

	public function setCardValidate($card, $method='')
	{
		$fieldsRequired = '';
		$registeredNrCrachaAndCdSetor = '';

		if (array_key_exists('nr_cartao', $card)) {
			$this->setNrCartao($card['nr_cartao']);
		} else {
			if ($method == 'POST') {
				$fieldsRequired .= 'Required: nr_cartao';
			}
		}

		if (
			array_key_exists('nr_cracha', $card) && array_key_exists('cd_setor', $card)
			&& $card['nr_cracha'] !== '' && $card['cd_setor'] !== '' 
		) { 
			$registeredNrCrachaAndCdSetor .= "You can not register both fields nr_cracha and cd_setor";
		} else {
			if (array_key_exists('nr_cracha', $card)) {
				$this->setNrCracha($card['nr_cracha']);
			} 
			
			if (array_key_exists('cd_setor', $card)) {
				$this->setCdSetor($card['cd_setor']);
			}
		}

		if (array_key_exists('ie_passe_livre_catraca', $card)) {
			$this->setIePasseLivreCatraca($card['ie_passe_livre_catraca']);
		}

		if (array_key_exists('ie_passe_livre_catraca', $card)) {
			$this->setIePasseLivreCatraca($card['ie_passe_livre_catraca']);
		} else {
			$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'Required: ie_passe_livre_catraca' : ',ie_passe_livre_catraca';
		}

		if (array_key_exists('ie_situacao', $card)) {
			$this->setIeSituacao($card['ie_situacao']);
		} else {
			$fieldsRequired .= (strlen($fieldsRequired) == 0) ? 'Required: ie_situacao' : ',ie_situacao';
		}

		if (strlen($fieldsRequired) > 0 || strlen($registeredNrCrachaAndCdSetor) > 0) {
			$error = $fieldsRequired . (strlen($registeredNrCrachaAndCdSetor) > 0 ? ". $registeredNrCrachaAndCdSetor": $registeredNrCrachaAndCdSetor);
			Error::generateErrorCustom($error, 422);
		}
	}
}