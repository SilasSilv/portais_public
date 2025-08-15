<?php 

namespace api\v1\resource\meal\rules\thirdFolds;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\resource\turnstile\rules\Turnstile;
use \api\v1\resource\turnstile\rules\TurnstileDAO;

Class ThirdFoldsDAO extends Crud {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'refeicao_terceiros';
		$this->fields = "rt.nr_sequencia, rt.ie_terceiro_dobra, 
						 DATE_FORMAT(rt.dt_refeicao, '%d/%m/%Y') AS dt_refeicao,
       					 rt.ie_tipo_refeicao, rt.nr_cracha_resp, COALESCE(rt.nm_pessoa_cartao, '') AS nm_pessoa_cartao,
       					 COALESCE(rt.nr_cartao, '') AS nr_cartao, COALESCE(rt.nr_cracha, '') AS nr_cracha, 
       					 COALESCE(p.nm_pessoa_fisica, '') AS nm_pessoa_fisica";
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			switch($tempFields[$i]) {
				case 'nm_pessoa_fisica':
					$fields .= "COALESCE(p.nm_pessoa_fisica, '') AS nm_pessoa_fisica";
					break;
				case 'nm_pessoa_cartao':
				case 'nr_cartao':
				case 'nr_cracha':
					$fields .= "COALESCE(rt.{$tempFields[$i]}, ) AS {$tempFields[$i]}";
					break;
				case '':
					$fields .= "DATE_FORMAT(rt.dt_refeicao, '%d/%m/%Y') AS dt_refeicao";
					break;
				default:
					$fields .= "rt.{$tempFields[$i]}";
			}					

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findThirdFolds($nr_sequencia, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);	

		$query = "SELECT {$this->fields}
			       FROM refeicao_terceiros rt
			      LEFT JOIN pessoa_fisica p
			   	   ON rt.nr_cracha = p.nr_cracha 
			   	  WHERE nr_sequencia = :nr_sequencia";		

		try {

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindParam(":nr_sequencia", $nr_sequencia, \PDO::PARAM_INT);

			$stmt->execute();

			return $thirdFolds = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public function finThirdFoldsAll($params, $fields, $sortField, $sortingType, $limit, $offset, $queryType)
	{	
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields}
			       FROM refeicao_terceiros rt
			      LEFT JOIN pessoa_fisica p
			   	   ON rt.nr_cracha = p.nr_cracha ";

		if ($queryType == 'default') {
			$query .= "WHERE rt.dt_refeicao >= DATE_FORMAT(NOW(), '%Y-%m-%d') ";
			$i = 1;
		} else {
			$i = 0;
		}
		
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if($i > 0)
				$conditionPrefix = "AND ";

			switch($key) {
				case 'nr_cracha_resp':
					$query .= "$conditionPrefix rt.nr_cracha_resp = :nr_cracha_resp ";
					break;
				case 'ie_terceiro_dobra':
					$query .= "$conditionPrefix rt.ie_terceiro_dobra = :ie_terceiro_dobra ";
					break;
				case 'nr_cracha': 
					$query .= "$conditionPrefix rt.nr_cracha = :nr_cracha ";
					break;
				case 'nr_cartao':
					$query .= "$conditionPrefix rt.nr_cartao = :nr_cartao ";
					break;
				case 'dates':
					$query .= "$conditionPrefix rt.dt_refeicao BETWEEN :dt_inicio AND :dt_fim ";
			} 

			$i++;
		}

		$order = empty($sortField) ? " rt.dt_refeicao " : " $sortField ";
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";

		$query .= "ORDER BY $order
					$queryLimitOffset"; 

		try {			
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
					case 'nr_cracha_resp':
						$stmt->bindValue(':nr_cracha_resp', $value);
						break;
					case 'ie_terceiro_dobra':
						$stmt->bindValue(':ie_terceiro_dobra', $value);
						break;
					case 'nr_cracha': 
						$stmt->bindValue(':nr_cracha', $value);
						break;
					case 'nr_cartao':
						$stmt->bindValue(':nr_cartao', $value);					
						break;
					case "dates":
			 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
			 			$stmt->bindValue(":dt_fim", $value['dt_fim']);
				} 
			}

			if ($limit != "ALL") {
				$limit = intval($limit);
				$offset = intval($offset);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
				$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
			}

			$stmt->execute();

			return $thirdFolds = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
				
			Error::generateErrorDb($e);

		}			
	}

	public function register($thirdFolds)
	{
		$return = parent::create($thirdFolds, ["showId" => true]);

		$turnstile = new Turnstile();
		$turnstileDAO = new TurnstileDAO();		

		if ($turnstile->lunchTime($thirdFolds['dt_refeicao'])) {
			if (array_key_exists('nr_cracha', $thirdFolds) && $thirdFolds['nr_cracha'] != '') {
				$turnstileDAO->accessUpdate($thirdFolds['nr_cracha'], 'ALMOÇO');
			} else {
				$turnstileDAO->accessUpdate($thirdFolds['nr_cartao'], 'ALMOÇO');
			}	
		}

		return $return;
	}

	public function updateThirdFolds($nr_sequencia, $thirdFolds)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia 
					]
				];

		return parent::update($thirdFolds, $where);
	}

	public function deleteThirdFolds($nr_sequencia)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia 
					]
				];

		return parent::delete($where);
	}
}