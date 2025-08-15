<?php

namespace api\v1\resource\meal\rules\meal;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class MealDAO extends Crud {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'refeicao';
		$this->fields = "nr_refeicao, 
						 CASE DATE_FORMAT(dt_refeicao,'%w') 
							WHEN 0 THEN 'Dom'
					        WHEN 1 THEN 'Seg'
					        WHEN 2 THEN 'Ter'
					        WHEN 3 THEN 'Qua'
					        WHEN 4 THEN 'Qui'
					        WHEN 5 THEN 'Sex'
					        WHEN 6 THEN 'Sab'
					        ELSE 'Desconhecido'
					     END AS ds_dia,
						 ie_tipo_refeicao,
						 CASE ie_tipo_refeicao 
						 	WHEN 'A' THEN 'Almoço'
							WHEN 'J' THEN 'Jantar'
							ELSE 'Desconhecido'
						 END AS ds_tipo_refeicao, 
						 DATE_FORMAT(dt_refeicao, '%d/%m/%Y') AS dt_refeicao, ds_refeicao,
       					 DATE_FORMAT(dt_inicio, '%d/%m/%Y %H:%i:%s') AS dt_inicio, 
       					 DATE_FORMAT(dt_final, '%d/%m/%Y %H:%i:%s') AS dt_final, nr_cracha,
       					 DATE_FORMAT(dt_atualizacao, '%d/%m/%Y %H:%i:%s') AS dt_atualizacao, ie_situacao, ie_feriado";
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {

			switch($tempFields[$i]) {
			    case 'ds_dia':
			    	$fields .= "CASE DATE_FORMAT(r.dt_refeicao,'%w') 
									WHEN 0 THEN 'Dom'
							        WHEN 1 THEN 'Seg'
							        WHEN 2 THEN 'Ter'
							        WHEN 3 THEN 'Qua'
							        WHEN 4 THEN 'Qui'
							        WHEN 5 THEN 'Sex'
							        WHEN 6 THEN 'Sab'
							    	ELSE 'Desconhecido'
							    END AS ds_dia";
					break;
				case 'ds_tipo_refeicao':
					$fields .= "CASE r.ie_tipo_refeicao 
								  WHEN 'A' THEN 'Almoço'
								  WHEN 'J' THEN 'Jantar'
								  ELSE 'Desconhecido'
								END AS ds_tipo_refeicao";
					break;
				case 'dt_refeicao':
					$fields .= "DATE_FORMAT({$tempFields[$i]}, '%d/%m/%Y') AS {$tempFields[$i]}";
					break;
				case 'dt_inicio':
				case 'dt_final':
				case 'dt_atualizacao':
					$fields .= "DATE_FORMAT({$tempFields[$i]}, '%d/%m/%Y %H:%i:%s') AS {$tempFields[$i]}";
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
				case 'dt_refeicao':
					$fields .= "DATE_FORMAT(". trim($tempFields[$i]) .", '%Y-%m-%d')";
					break;
				case 'dt_inicio':
				case 'dt_final':
				case 'dt_atualizacao':
					$fields .= "DATE_FORMAT(". trim($tempFields[$i]) .", '%Y-%m-%d %H:%i:%s')";
					break;					
				default:
					$fields .= $tempFields[$i];
			}

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findMeal($nr_refeicao, $fields)
	{
		if (!empty($fields)) 
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields}
				   FROM refeicao
				  WHERE nr_refeicao = :nr_refeicao";

		try {
 			  
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_refeicao', $nr_refeicao);

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findMealAll($params, $fields, $sortField, $sortingType, $limit)
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);
			$sortField = empty($sortField) ? " 1 " : $sortField;
		}

		$query = "SELECT {$this->fields}
				   FROM refeicao ";

		if (count($params) == 0) {
			$query .= "WHERE dt_refeicao >= DATE_FORMAT(SYSDATE(), '%Y-%m-%d')
				   		AND ie_situacao = 'A' ";
		}
		
		$i = 0;
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if($i > 0)
				$conditionPrefix = "AND ";
		
			switch($key) {
				case 'ie_situacao':
					$query .= "$conditionPrefix ie_situacao IN (:ie_situacao) ";
					break;
				case 'ie_tipo_refeicao':
					$query .= "$conditionPrefix ie_tipo_refeicao = :ie_tipo_refeicao ";
					break;
				case 'ds_refeicao':
					$query .= "$conditionPrefix ds_refeicao LIKE :ds_refeicao ";
					break;				
				case 'dates':
					$query .= "$conditionPrefix dt_refeicao BETWEEN :dt_inicio AND :dt_fim ";
			} 

			$i++;
		}

		$order = empty($sortField) ? " DATE_FORMAT(dt_refeicao, '%Y-%m-%d') " : $this->parserSortField($sortField);
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimit = $limit != "ALL" ? "LIMIT :limit" : "";
		
		$query .= "ORDER BY $order 
					$queryLimit";

		try {
					  
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
					case 'ie_situacao':
						$stmt->bindValue(':ie_situacao', $value);
						break;
					case 'ie_tipo_refeicao': 
						$stmt->bindValue(':ie_tipo_refeicao', $value);
						break;
					case 'ds_refeicao':
						$stmt->bindValue(':ds_refeicao', '%' . $value . '%');
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

	public function register($meal)
	{
		return parent::create($meal, [
										"showId" => true 
									]);
	}

	public function updateMeal($nr_refeicao, $meal)
	{
		$where = [
					[
						"field" => "nr_refeicao",
						"operator" => "=",
						"value" => $nr_refeicao 
					]
				];

		return parent::update($meal, $where);
	}

	public function deleteMeal($nr_refeicao)
	{
		$where = [
					[
						"field" => "nr_refeicao",
						"operator" => "=",
						"value" => $nr_refeicao 
					]
				];

		return parent::delete($where);		
	}
}