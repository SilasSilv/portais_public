<?php 

namespace api\v1\resource\meal\rules\opinion;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class OpinionDAO extends Crud 
{
	public function __construct()
	{
		parent::__construct();
		$this->table = 'opine';
		$this->fields = "o.nr_sequencia, o.nr_cracha, p.nm_pessoa_fisica,
					     o.cd_tipo_opiniao, c.ds_categoria AS ds_tipo_opiniao, o.ds_opiniao,
					     DATE_FORMAT(o.dt_inclusao, '%d/%m/%Y %H:%i:%s') AS dt_inclusao,
       					 COALESCE(lo.ie_lido, 'N') AS ie_lido";
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {

			switch($tempFields[$i]) {
				case 'ds_tipo_opiniao':
					$fields .= "c.ds_categoria AS ds_tipo_opiniao";
					break;
				case 'nm_pessoa_fisica':
					$fields .= "p.nm_pessoa_fisica";
					break;
				case 'dt_inclusao':
					$fields .= "DATE_FORMAT(o.dt_inclusao, '%d/%m/%Y %H:%i:%s') AS dt_inclusao";
					break;
				default:
					$fields .= "o.{$tempFields[$i]}";
			}			

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findOpinion($nr_sequencia, $nr_cracha_default, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query .= "SELECT {$this->fields} 
					FROM opine o
				   INNER JOIN pessoa_fisica p
				    ON o.nr_cracha = p.nr_cracha
				   INNER JOIN tb_categoria c
				   	ON o.cd_tipo_opiniao = c.cd_categoria
				   LEFT JOIN tb_lido_opine  lo
					ON p.nr_cracha  = :nr_cracha_default
					AND o.nr_sequencia = lo.nr_seq_opine 
				   WHERE o.nr_sequencia = :nr_sequencia";

		try {
		  
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_sequencia', $nr_sequencia);
			$stmt->bindValue(':nr_cracha_default', $nr_cracha_default);

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findOpinionAll($params, $fields, $sortField, $sortingType, $limit)
	{	
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields} 
					FROM opine o
				   INNER JOIN pessoa_fisica p
				    ON o.nr_cracha = p.nr_cracha	 
				   INNER JOIN tb_categoria c
				   	ON o.cd_tipo_opiniao = c.cd_categoria 
				   LEFT JOIN tb_lido_opine  lo
					ON p.nr_cracha  = :nr_cracha_default
					AND o.nr_sequencia = lo.nr_seq_opine ";

		$i = 0;
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if ($i > 0) {
				$conditionPrefix = "AND ";
			}

			switch($key) {
				case 'ie_lido':
					$query .= "$conditionPrefix COALESCE(lo.ie_lido, 'N') = :ie_lido ";
					break;
				case 'nr_cracha':
					$query .= "$conditionPrefix o.nr_cracha = :nr_cracha ";
					break;
				case 'cd_tipo_opiniao':
					$query .= "$conditionPrefix o.cd_tipo_opiniao = :cd_tipo_opiniao ";
					break;
				case 'dates':
					$query .= "$conditionPrefix o.dt_inclusao BETWEEN :dt_inicio AND :dt_fim ";
				default: 
					$i--;
			} 

			$i++;
		}

		$order = empty($sortField) ? " 1 " : " $sortField ";
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimit = $limit != "ALL" ? "LIMIT :limit" : "";
		
		$query .= "ORDER BY $order 
					$queryLimit";
		
		try {
		  
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
					case 'nr_cracha_default':
						$stmt->bindValue(':nr_cracha_default', $value);
						break;
					case 'ie_lido':
						$stmt->bindValue(':ie_lido', $value);
						break;
					case 'nr_cracha': 
						$stmt->bindValue(':nr_cracha', $value);
						break;
					case 'cd_tipo_opiniao':
						$stmt->bindValue(':cd_tipo_opiniao', $value);
						break;								
					case "dates":
			 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
			 			$stmt->bindValue(":dt_fim", $value['dt_fim'] . " 23:59:59");
				} 
			}

			if ($limit != "ALL") {
				$limit = intval($limit);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
			}

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public function register($opinion)
	{
		return parent::create($opinion, [
											"showId" => true 
										]);
	}

	public function updateOpinion($nr_sequencia, $opinion)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia 
					]
				];

		return parent::update($opinion, $where);
	}

	public function deleteOpinion($nr_sequencia)
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

	public function readOpinion($nr_cracha, $nr_sequencia)
	{	
		$this->table = 'tb_lido_opine';

		return parent::create(['nr_cracha' => $nr_cracha,
							   'nr_seq_opine' => $nr_sequencia,
							   'ie_lido' => 'S'], 
							  ["showId" => false]);
	}
}