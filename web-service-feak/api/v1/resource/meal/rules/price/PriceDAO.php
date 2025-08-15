<?php

namespace api\v1\resource\meal\rules\price;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

class PriceDAO extends Crud {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'valor_refeicao';
		$this->fields = "nr_sequencia,
						 DATE_FORMAT(dt_vigencia_inicial, '%d/%m/%Y') AS dt_vigencia_inicial,
					     DATE_FORMAT(dt_vigencia_final, '%d/%m/%Y') AS dt_vigencia_final,
					     vl_refeicao,
					     ie_situacao";
	}

	private function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {

			switch($tempFields[$i]) {
				case 'dt_vigencia_inicial':
				case 'dt_vigencia_final':
					$fields .= "DATE_FORMAT({$tempFields[$i]}, '%d/%m/%Y') AS {$tempFields[$i]}";
					break;
				default:
					$fields .= $tempFields[$i];
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function parserSortField($fields)
	{
		$tempFields = explode(',', $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			switch (trim($tempFields[$i])) {
				case 'dt_vigencia_inicial':
				case 'dt_vigencia_final':
					$fields .= "DATE_FORMAT(". trim($tempFields[$i]) .", '%Y-%m-%d')";
					break;					
				default:
					$fields .= $tempFields[$i];
			}

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findPrice($params, $fields, $sortField, $sortType, $limit) 
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);
		}

		$query = "SELECT {$this->fields}
				   FROM {$this->table} ";

		$i = 0;
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if($i > 0)
				$conditionPrefix = "AND ";
		
			switch($key) {	
				case 'nr_sequencia':
					$query .= "$conditionPrefix nr_sequencia = :nr_sequencia ";
					break;					
				case 'dt_vigencia_inicial':
					$query .= "$conditionPrefix dt_vigencia_inicial BETWEEN :dt_inicio AND :dt_fim ";
					break;
				case 'dt_vigencia_final':
					$query .= "$conditionPrefix dt_vigencia_final BETWEEN :dt_inicio AND :dt_fim ";
					break;
				case 'vl_refeicao':
					$query .= "$conditionPrefix vl_refeicao = :vl_refeicao ";
					break;
				case 'ie_situacao':
					$query .= "$conditionPrefix ie_situacao IN (:ie_situacao) ";		
			} 

			$i++;
		}

		$order = empty($sortField) ? " DATE_FORMAT(dt_vigencia_inicial, '%Y-%m-%d')  DESC, DATE_FORMAT(dt_vigencia_final, '%Y-%m-%d') DESC, vl_refeicao DESC " : $this->parserSortField($sortField);
		$order .= empty($sortType) ? " " : " $sortType";
		$queryLimit = $limit != "ALL" ? "LIMIT :limit" : "";
		
		$query .= "ORDER BY $order 
					$queryLimit";
		
		try {
					  
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
					case "nr_sequencia":
						$stmt->bindValue(":nr_sequencia", $value["nr_sequencia"]);
						break;
					case "dt_vigencia_inicial":
			 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
			 			$stmt->bindValue(":dt_fim", $value['dt_fim']);
			 			break;						
					case "dt_vigencia_final":
			 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
			 			$stmt->bindValue(":dt_fim", $value['dt_fim']);
			 			break;
			 		case 'vl_refeicao': 
						$stmt->bindValue(':vl_refeicao', $value);
						break;
			 		case 'ie_situacao':
						$stmt->bindValue(':ie_situacao', $value);
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

	public function register($price) {
		return parent::create($price, ["showId" => true]);
	}

	public function updatePrice($nr_sequencia, $price)
	{
		$where = [[
					"field" => "nr_sequencia",
					"operator" => "=",
					"value" => $nr_sequencia
				]];

		return parent::update($price, $where);
	}

	public function deletePrice($nr_sequencia)
	{
		$where = [[
					"field" => "nr_sequencia",
					"operator" => "=",
					"value" => $nr_sequencia
				]];

		return parent::delete($where);		
	}
}