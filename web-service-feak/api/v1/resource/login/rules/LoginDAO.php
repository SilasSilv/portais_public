<?php

namespace api\v1\resource\login\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\utils\UrlUtils;
use \api\v1\vendor\utils\ClearData;
use \api\v1\vendor\session\Session;
use \api\v1\vendor\utils\StringUtils;
use \api\v1\resource\login\rules\Login;
use \api\v1\resource\people\rules\PeopleDAO;
use \api\v1\vendor\authorization\Authorization as Auth;

Class LoginDAO extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = 'pessoa_fisica';
	}

	public function checkLogin($usuario_nome, $senha) 
	{
		$ds_senha = md5($senha);	
		$params = [];	
		$params["fields"] = "p.nr_cracha, p.nm_pessoa_fisica, CONCAT('" . UrlUtils::getUrl() . "', p.url_foto_perfil) AS url_foto_perfil, cd_setor, ie_situacao, COALESCE(ie_alterar_senha, 'N') ie_alterar_senha";		
		$params["table"] = ["name" => "pessoa_fisica", "aliases" => "p"];

		$params["where"] = [ ["aliases" => "p", "field" => "nr_cracha", "operator" => "=", "value" => $usuario_nome],
					["logical_op" => "AND", "aliases" => "p", "field" => "ds_senha", "operator" => "=", "value" => $ds_senha],
					["logical_op" => "OR", "aliases" => "p", "field" => "ds_login_alternativo", "operator" => "=", "value" => $usuario_nome],
					["logical_op" => "AND", "aliases" => "p", "field" => "ds_senha", "operator" => "=", "value" => $ds_senha] ];

		$result = parent::readNew($params);
		$result = count($result) > 0 ? $result[0] : $result;

		return $result;
	}

	public function getME($token) 
	{
		$session = new Session();
		$peopleDAO = new PeopleDAO();

		$sessionData = $session->getSession($token);
		$peopleData = [];
		$permissionData = [];

		if (count($sessionData) > 0) {

			$fileds = "nr_cracha, nm_pessoa_fisica, url_foto_perfil, cd_cargo, ds_cargo, 
					   cd_setor, ds_setor, ie_alterar_senha";
		
			$peopleData = $peopleDAO->findPeople($sessionData['nr_cracha'], $fileds)[0];

			$permissionData = Auth::findSystemAll(
									$sessionData["cd_sistema"], 
								   	$sessionData['nr_cracha'], 
								   	$sessionData['cd_setor'], 
								   	'Client');
		
		}

		$dateExpire = \DateTime::createFromFormat('Y-m-d H:i:s', $sessionData['expire']);
		$sessionData['expire'] = $dateExpire->format('d/m/Y H:i:s');

		$paramsCheckDataSystem = ['nr_cracha' => $peopleData['nr_cracha']];
		$returnCheckDataSystem = Login::checkDataSystem($sessionData["cd_sistema"], $paramsCheckDataSystem);

		$me = [
			"user" => [
				"name" => StringUtils::pretty($peopleData['nm_pessoa_fisica']),
				"photo"	=> $peopleData['url_foto_perfil'],
				"update_password" =>  $peopleData['ie_alterar_senha'],
				"system_data" => $returnCheckDataSystem
			],
			"permissions" => $permissionData,
			"session" => [
				"token" => $sessionData["token"],
				"refresh_token" => $sessionData["refresh_token"],
				"expire" => $sessionData['expire'],
				"cd_sistema" => $sessionData["cd_sistema"]
			]
		];

		return $me;
	}

	public function findSystemsAccess($nr_cracha, $cd_setor) 
	{
		$nr_cracha = ClearData::stringSQL($nr_cracha);
		$cd_setor = ClearData::stringSQL($cd_setor);

		$params = [
			"fields" => "s.cd_sistema, COALESCE(p.nm_apelido_acesso, s.nm_sistema) AS nm_sistema, p.cd_permissao, 
				CASE 
					WHEN p.logo_alternativo_acesso IS NOT NULL THEN CONCAT('" . UrlUtils::getUrl() . "', p.logo_alternativo_acesso)
					ELSE COALESCE(CONCAT('" . UrlUtils::getUrl() . "', s.img_logo), '" . UrlUtils::getUrl() . "img/system/70b2aecb155937ca5abc0dfbd3d0eedb.png')
				END	AS img_logo, 
				COALESCE(p.url_acesso, '') AS url_acesso, COALESCE(u.vl_pf, se.vl_setor, p.vl_padrao) AS vl_permissao",
			"table" => ["name" => "tb_sistema", "aliases" => "s"],
			"join" => "INNER JOIN tb_permissao p ON p.cd_sistema = s.cd_sistema AND cd_tipo_permissao = 68
				LEFT JOIN tb_permissao_pf u ON u.cd_permissao = p.cd_permissao AND u.nr_cracha = $nr_cracha
				LEFT JOIN tb_permissao_setor se ON se.cd_permissao = p.cd_permissao AND se.cd_setor = $cd_setor" .
			" WHERE COALESCE(u.vl_pf, se.vl_setor, p.vl_padrao) = 'S' AND s.ie_situacao = 'A'",
			"sort_fields" => [["field" => "s.nm_sistema", "type" => "ASC"]],
			"pagination" => "ALL",
		];

		return parent::readNew($params);
	}

	public function changeOfSystem($token, $cd_sistema)
	{
		$this->table = "tb_sessao";
		$where = [ [ "field" => "token", "operator" => "=", "value" => $token ] ];

		return parent::update(["cd_sistema" => $cd_sistema], $where);
	}
}