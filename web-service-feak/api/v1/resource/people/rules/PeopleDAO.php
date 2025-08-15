<?php

namespace api\v1\resource\people\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\UrlUtils;
use \api\v1\resource\meal\rules\menu\MenuDAO;

class PeopleDAO extends Crud {

	public function __construct()
	{	
		parent::__construct();
		$this->table = 'pessoa_fisica';	
		$this->fields = "p.nr_cracha, p.nm_pessoa_fisica, COALESCE(p.ds_login_alternativo, '') AS ds_login_alternativo,
						 CONCAT('" . UrlUtils::getUrl() . "', p.url_foto_perfil) AS url_foto_perfil, COALESCE(p.ds_mail, '') AS ds_mail,
				         p.cd_cargo, c.ds_cargo, p.cd_setor, s.ds_setor,
						 COALESCE(p.cd_grupo, '') AS cd_grupo, COALESCE(g.nm_grupo, '') AS nm_grupo,
				         COALESCE(ca.nr_cartao, '') AS nr_cartao,
						 COALESCE(DATE_FORMAT(p.dt_demissao, '%d/%m/%Y'), '') AS dt_demissao,
		                 p.ie_situacao, COALESCE(p.ie_alterar_senha, 'N') AS ie_alterar_senha";
		$this->join = "INNER JOIN cargo c   ON p.cd_cargo = c.cd_cargo
 					   INNER JOIN setores s ON p.cd_setor = s.cd_setor
 					   LEFT JOIN tb_grupo g ON p.cd_grupo = g.cd_grupo
 					   LEFT JOIN tb_cartao ca ON p.nr_cracha = ca.nr_cracha";
	}
	
	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = trim($tempFields[$i]);

			switch($field) {
				case 'ds_login_alternativo':
					$fields .= "COALESCE(p.ds_login_alternativo, '') AS ds_login_alternativo";
					break;
				case 'ds_mail':
					$fields .= "COALESCE(p.ds_mail, '') AS ds_mail";
					break;
				case 'ds_cargo':
					$fields .= "c.ds_cargo";
					break;
				case 'ds_setor':
					$fields .= "s.ds_setor";
					break;
				case 'cd_grupo':
					$fields .= "COALESCE(p.cd_grupo, '') AS cd_grupo";
					break;
				case 'nm_grupo':
					$fields .= "COALESCE(g.nm_grupo, '') AS nm_grupo";
					break;
				case 'nr_cartao':
					$fields .= "COALESCE(ca.nr_cartao, '') AS nr_cartao";
					break;
				case 'dt_demissao':
					$fields .= "COALESCE(DATE_FORMAT(p.dt_demissao, '%d/%m/%Y'), '')  AS dt_demissao";
					break;
				case 'ie_alterar_senha':
					$fields .= "COALESCE(p.ie_alterar_senha, 'N') AS ie_alterar_senha";
					break;
				case 'url_foto_perfil':
					$fields .= "CONCAT('" . UrlUtils::getUrl() . "', p.url_foto_perfil) AS url_foto_perfil";
					break;
				default:
					$fields .= "p.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}	

	public function sortFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = trim($tempFields[$i]);

			switch($field) {
				case 'ds_cargo':
					$fields .= "c.ds_cargo";
					break;
				case 'ds_setor':
					$fields .= "s.ds_setor";
					break;
				case 'nm_grupo':
					$fields .= "g.nm_grupo";
					break;
				default:
					$fields .= "p.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function mountWhereAsParams($params, $query) 
	{
		$i = 0;

		foreach($params as $key => $value) {

			if ($i == 0 && $params["type_search"] == "free") {
				$prefix = "WHERE ";
				$i = 1;
			} else {
				$prefix = "AND";
			}

		 	switch($key) {
		 		case 'nr_cracha':
		 			$query .= "$prefix p.nr_cracha = :nr_cracha ";
		 			break;
				case 'nm_pessoa_fisica':
					$query .= "$prefix p.nm_pessoa_fisica LIKE :nm_pessoa_fisica ";
					break;
				case 'cd_cargo':
					$query .= "$prefix c.cd_cargo = :cd_cargo ";
					break;
				case "cd_setor":
		 			$query .= $params["type_search"] == "free" ? " $prefix p.cd_setor = :cd_setor " : "";
		 			break;
		 		case "cd_grupo":
		 			$query .= $params["type_search"] == "free" ? " $prefix p.cd_grupo = :cd_grupo " : "";
		 			break;
				case 'ie_situacao':
					$query .= "$prefix p.ie_situacao IN (:ie_situacao) ";
					break;
				case "dates":
		 			$query .= "$prefix p.dt_demissao BETWEEN :dt_inicio AND :dt_fim ";
			} 
		 	
		}

		return $query;
	}

	public function prepareWhereAsParams($params, $stmt)
	{
		foreach($params as $key => $value) {

			switch($key) {
				case 'nr_cracha':
					$stmt->bindValue(':nr_cracha', $value);
					break;
				case 'nm_pessoa_fisica':
					$stmt->bindValue(':nm_pessoa_fisica', '%' . $value . '%');
					break;		
				case 'cd_cargo': 
					$stmt->bindValue(':cd_cargo', $value);
					break;	
				case 'cd_setor': 
					$stmt->bindValue(':cd_setor', $value);
					break;
				case "cd_grupo":
		 			if ($value != NULL) {
		 				$stmt->bindValue(":cd_grupo", $value);
		 			}		 			
		 			break;										
				case 'ie_situacao':
					$stmt->bindValue(':ie_situacao', $value);
					break;
				case "dates":
		 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
		 			$stmt->bindValue(":dt_fim", $value['dt_fim']);
			} 
			
		}

		return $stmt;		
	}

	private function handleExceptionDB($exception) {
		if ($exception instanceof \PDOException) {			
			if (stripos($exception->getMessage(), '1062')) {
				if (stripos($exception->getMessage(), 'PRIMARY')) {
					Error::generateErrorCustom(["code" => "PEOPLE-01", "message" => "Field nr_cracha duplicate values"], 422);
				} elseif (stripos($exception->getMessage(), 'ds_login_alternativo')) {
					Error::generateErrorCustom(["code" => "PEOPLE-02", "message" => "Field ds_login_alternative duplicate values"], 422);
				}
			} elseif (stripos($exception->getMessage(), '1451')) {
				if (stripos($exception->getMessage(), 'nr_cracha')) {
					Error::generateErrorCustom(["code" => "PEOPLE-03", "message" => "Field nr_cracha has dependent lines (FOREIGN KEY)"], 422);
				}
			} else {
				Error::generateErrorDb($exception);
			}
		}
	}

	public function findPeople($nr_cracha, $fields)
	{
		if (!empty($fields)) 
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields}
				   FROM pessoa_fisica p
				  {$this->join}
				  WHERE p.nr_cracha = :nr_cracha";

		try {
			  
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_cracha', $nr_cracha);

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findPeopleAll($params, $fields, $sortField, $sortingType, $limit, $offset)
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);
		}

		$query = "SELECT {$this->fields}
				   FROM pessoa_fisica p 
				  {$this->join} ";

		if ($params["type_search"] == "default") {

			if ($params["cd_grupo"] != NULL) {
				$query .= "WHERE (p.cd_setor = :cd_setor OR p.cd_grupo = :cd_grupo) ";
			} else {
				$query .= "WHERE p.cd_setor = :cd_setor ";
			}

		}

		$query = $this->mountWhereAsParams($params, $query);		
		
		$order = empty($sortField) ? "p.nm_pessoa_fisica" : ($sortField == 1 ? $sortField : $this->sortFieldsPrefix($sortField));
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";
		
		$query .= "ORDER BY $order 
					$queryLimitOffset";
					
		try {
		  
			$stmt = $this->conn->getConn()->prepare($query);
			
			$stmt = $this->prepareWhereAsParams($params, $stmt);

			if ($limit != "ALL") {
				$limit = intval($limit);
				$offset = intval($offset);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
				$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
			}

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public function statisticAmount()
	{
		$query = "SELECT (SELECT COUNT(1) FROM pessoa_fisica) AS pessoas_total,
					(SELECT COUNT(1) FROM pessoa_fisica WHERE ie_situacao = 'A') AS pessoas_ativo,
					(SELECT COUNT(1) FROM pessoa_fisica WHERE COALESCE(ie_situacao, 'I') = 'I') AS pessoas_inativo";

		try {				
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->execute();
			return $stmt->fetchALL(\PDO::FETCH_ASSOC);
		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	public function register($people) 
	{			
		$people['ds_senha'] = md5($people['ds_senha']);
		$nr_cartao = 0;

		if (array_key_exists('nr_cartao', $people)) {
			$nr_cartao = $people['nr_cartao'];
			unset($people['nr_cartao']);
		}	

		$insertPeople = parent::create($people, [], ["exception_return" => "S"]);

		$this->handleExceptionDB($insertPeople);

		if ($insertPeople) {
			if ($nr_cartao != 0) {
				$this->manipulateCard($nr_cartao, $people['nr_cracha']);
			}
			
			return $people['nr_cracha'];
		} else {
			return -1;
		}
	}

	public function updatePeople($nr_cracha, $people) 
	{
		$where = [ ["field" => "nr_cracha",	"operator" => "=", "value" => $nr_cracha] ];
		$nr_cartao = 0;

		if (array_key_exists('dt_demissao', $people)) {
			if ($people['dt_demissao'] !== NULL) {
				$menuDAO = new MenuDAO();	
				$dt_demissao = \DateTime::createFromFormat('Y-m-d', $people['dt_demissao']);
				
				$menu = $menuDAO->findMealRequest(["nr_cracha" => $nr_cracha], "", "", "", "ALL");

				foreach($menu as $meal) {
					$dt_refeicao = \DateTime::createFromFormat('d/m/Y', $meal['dt_refeicao']);	

					if ($dt_demissao < $dt_refeicao && $meal['ie_solicitado'] == 1) {
						$menuDAO->deleteMealRequest($nr_cracha, $meal['nr_refeicao']);
					} 
				}
			}
		}

		if (array_key_exists('nr_cartao', $people)) {
			$nr_cartao = $people['nr_cartao'];
			unset($people['nr_cartao']);
		}

		if (array_key_exists('url_foto_perfil', $people)) {
			if ($people['url_foto_perfil'] == 'img/people/default.jpg') {
				if (file_exists("./img/people/$nr_cracha.jpg")) {
					unlink("./img/people/$nr_cracha.jpg");
				} elseif (file_exists("./img/people/$nr_cracha.png")) {
					unlink("./img/people/$nr_cracha.png");
				}
			}			
		} else {
			if (array_key_exists('nr_cracha', $people)) {
				if (file_exists("./img/people/$nr_cracha.jpg")) {
					$people['url_foto_perfil'] = "img/people/{$people['nr_cracha']}.jpg";
				} elseif (file_exists("./img/people/$nr_cracha.png")) {
					$people['url_foto_perfil'] = "img/people/{$people['nr_cracha']}.png";
				}
			}
		}		

		$updatePeople = parent::update($people, $where, 'ds_senha', ["exception_return" => "S"]);

		$this->handleExceptionDB($updatePeople);

		if ($updatePeople && array_key_exists('nr_cracha', $people)) {
			if (file_exists("./img/people/$nr_cracha.jpg")) {
				rename("./img/people/$nr_cracha.jpg", "./img/people/{$people['nr_cracha']}.jpg");
			} elseif (file_exists("./img/people/$nr_cracha.png")) {
				rename("./img/people/$nr_cracha.png", "./img/people/{$people['nr_cracha']}.png");
			}
		}

		$nr_cracha = array_key_exists('nr_cracha', $people) ? $people['nr_cracha'] : $nr_cracha;
		$updateCards = $nr_cartao === 0 ? false : $this->manipulateCard($nr_cartao, $nr_cracha);
		
		return $updatePeople || $updateCards;
	}

	public function updatePassword($nr_cracha, $people) 
	{
		$ds_senha = $this->findPeople($nr_cracha, 'ds_senha')[0]["ds_senha"];

		if ($ds_senha === md5($people['ds_senha'])) {
			Error::generateErrorCustom('Same password', 422);
		}
		
		$where = [ ["field" => "nr_cracha",	"operator" => "=", "value" => $nr_cracha] ];

		return parent::update($people, $where, 'ds_senha');
	}

	private function manipulateCard($nr_cartao, $nr_cracha) 
	{	
		$this->table = "tb_cartao";
		$update = false;

		$cardUpdate = ["nr_cracha" => null];
		$where = [ ["field" => "cd_setor", "operator" => "IS NULL"] ];
		$where[] = ["logical_op" => "AND", "field" => "nr_cracha", "operator" => "=", "value" => $nr_cracha];

		if ($nr_cartao !== "") {
			parent::update($cardUpdate, $where);

			$cardUpdate = ["nr_cracha" => $nr_cracha];
			$where[1] = ["logical_op" => "AND", "field" => "nr_cracha", "operator" => "IS NULL"];
			$where[] = ["logical_op" => "AND", "field" => "nr_cartao", "operator" => "=", "value" => $nr_cartao];
		} 

		if (parent::update($cardUpdate, $where)) {
			$update = true;
		}
		
		return $update;
	}

	public function deletePeople($nr_cracha) 
	{
		$where = [ ["field" => "nr_cracha",	"operator" => "=", "value" => $nr_cracha] ];
		
		return parent::delete($where);
	}

	public function checkSectorOrGroup($nr_cracha, $cd_setor, $cd_grupo) 
	{
		$query = "SELECT COUNT(1) AS faz_parte
				   FROM pessoa_fisica 
				  WHERE nr_cracha = :nr_cracha
				   AND ie_situacao = 'A'
				   AND (cd_setor = :cd_setor OR 
				   		cd_grupo = :cd_grupo)";

		try {		

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_cracha', $nr_cracha);
			$stmt->bindValue(':cd_setor', $cd_setor);
			$stmt->bindValue(':cd_grupo', $cd_grupo);

			$stmt->execute();

			$result = $stmt->fetchALL(\PDO::FETCH_ASSOC)[0];	

			if ($result['faz_parte'] > 0) {
				return false;
			} else {
				return true;
			}

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}		
	}

}