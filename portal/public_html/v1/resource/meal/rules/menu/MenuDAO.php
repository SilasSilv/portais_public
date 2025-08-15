<?php

namespace api\v1\resource\meal\rules\menu;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class MenuDAO extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = "refeicao r";
		$this->fields = "r.nr_refeicao,
						 CASE DATE_FORMAT(r.dt_refeicao,'%w') 
							WHEN 0 THEN 'Dom'
					        WHEN 1 THEN 'Seg'
					        WHEN 2 THEN 'Ter'
					        WHEN 3 THEN 'Qua'
					        WHEN 4 THEN 'Qui'
					        WHEN 5 THEN 'Sex'
					        WHEN 6 THEN 'Sab'
					        ELSE 'Desconhecido'
					     END AS ds_dia, 
						 r.ie_tipo_refeicao,
						 CASE r.ie_tipo_refeicao 
						 	WHEN 'A' THEN 'Almoço'
							WHEN 'J' THEN 'Jantar'
							ELSE 'Desconhecido'
						 END AS ds_tipo_refeicao,
						 DATE_FORMAT(r.dt_refeicao, '%d/%m/%Y') AS dt_refeicao, r.ds_refeicao,
						 DATE_FORMAT(r.dt_final, '%d/%m/%Y %H:%i:%s') AS dt_final,
						 r.dt_final AS dt_final_unix, r.ie_feriado";
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			switch($tempFields[$i]) {
				case 'ie_solicitado':
					$fields .= "CASE 
								  WHEN rp.nr_cracha IS NULL THEN 0
					              ELSE 1
							    END AS ie_solicitado";
					break;
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
				case 'ds_ref_alt':
					$fields .= "COALESCE(rp.ds_ref_alt, '') AS ds_ref_alt";
					break;
				case 'dt_refeicao':
					$fields .= "DATE_FORMAT(r.dt_refeicao, '%d/%m/%Y') AS dt_refeicao";
					break;
				case 'dt_final':
					$fields .= "DATE_FORMAT(r.dt_final, '%d/%m/%Y %H:%i:%s') AS dt_final";
					break;
				case 'dt_final_unix':
					$fields .= "r.dt_final AS dt_final_unix";
					break;
				default:
					$fields .= "r.{$tempFields[$i]}";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	private function insertMealWeekEnd($menu) 
	{
		$dt_week_end = new \DateTime();
		$dt_saturday = new \DateTime();
		$dt_sunday = new \DateTime();
		$meal_saturday = ["ds_dia" => "Sab", "ie_tipo_refeicao" => "A",	"ds_tipo_refeicao" => "Almoço",	"ds_refeicao" => "Almoço no sábado"];
		$meal_sunday = ["ds_dia" => "Dom", "ie_tipo_refeicao" => "A", "ds_tipo_refeicao" => "Almoço", "ds_refeicao" => "Almoço no domingo"];
		
		if ($dt_week_end->format('w') == 6) {
			$dt_sunday->modify('+1 day');

			$meal_saturday["dt_refeicao"] = $dt_saturday->format('d/m/Y');
			$meal_sunday["dt_refeicao"] = $dt_sunday->format('d/m/Y');

		} elseif ($dt_week_end->format('w') == 0) {
			$dt_saturday = null;

			$meal_sunday["dt_refeicao"] = $dt_sunday->format('d/m/Y');
		} else {
			$dt_saturday->modify('+' . (6 - $dt_week_end->format('w')) .' day');
			$dt_sunday->modify('+' . ((6 - $dt_week_end->format('w')) + 1) .' day');

			$meal_saturday["dt_refeicao"] = $dt_saturday->format('d/m/Y');
			$meal_sunday["dt_refeicao"] = $dt_sunday->format('d/m/Y');
		}
		
		if ($dt_saturday) {
			if ($this->notExistsMealDate($meal_saturday["dt_refeicao"], $menu)) {
				$menu[] = $meal_saturday;
			} 
		} 

		if ($this->notExistsMealDate($meal_sunday["dt_refeicao"], $menu)) {
			$menu[] = $meal_sunday;
		}
	
		return $menu;
	}

	private function notExistsMealDate($date, $menu) {
		foreach($menu as $meal) {
			if ($date === $meal['dt_refeicao']) {
				return false;
			}
		}
		return true;
	}

	private function sortMenu($meal_current, $meal_next) 
	{
		$dt_current = \DateTime::createFromFormat('d/m/Y', $meal_current['dt_refeicao']);
		$dt_next = \DateTime::createFromFormat('d/m/Y', $meal_next['dt_refeicao']);

		if ($dt_current == $dt_next) {
			return 0;
		} else {
			return ($dt_current < $dt_next) ? -1 : 1;
		}
	}

	public function findMenu($nr_refeicao, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);	

		$query = "SELECT {$this->fields}
				   FROM refeicao r
				  WHERE STR_TO_DATE(CONCAT(r.dt_refeicao, ' ', '23:59:59'), 
								'%Y-%m-%d %H:%i:%s') >= SYSDATE()
				   AND r.ie_situacao = 'A'
				   AND nr_refeicao = :nr_refeicao";

		try {
		  
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_refeicao', $nr_refeicao);

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public function findMenuAll($params, $fields, $sortField, $sortingType, $limit)
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);	
			$sortField = empty($sortField) ? " 1 " : $sortField;
		}

		$query = "SELECT {$this->fields}
				   FROM refeicao r 		
			      WHERE STR_TO_DATE(CONCAT(r.dt_refeicao, ' ', '23:59:59'), 
								'%Y-%m-%d %H:%i:%s') >= SYSDATE()
				   	AND ie_situacao = 'A' ";
		
		foreach($params as $key => $value) {

			switch($key) {
				case 'ie_tipo_refeicao':
					$query .= "AND r.ie_tipo_refeicao = :ie_tipo_refeicao ";
					break;
				case 'ds_refeicao':
					$query .= "AND r.ds_refeicao LIKE :ds_refeicao ";
					break;				
				case 'dates':
					$query .= "AND r.dt_refeicao BETWEEN :dt_inicio AND :dt_fim ";
			} 

		}

		$order = empty($sortField) ? " r.dt_refeicao " : " $sortField ";
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimit = $limit != "ALL" ? "LIMIT :limit" : "";
		
		$query .= "ORDER BY $order 
					$queryLimit";

		try {
 			  
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
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

			$menu = $stmt->fetchALL(\PDO::FETCH_ASSOC);

			if (array_key_exists('dobras_terceiros', $params)) {
				$menu = $this->insertMealWeekEnd($menu);
				usort($menu, array($this, "sortMenu"));
			}
			
			return $menu;

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findMealRequest($params, $fields, $sortField, $sortingType, $limit) 
	{	
		if (!array_key_exists('nr_cracha', $params)) {
			Error::generateErrorApi(Error::INTERNAL_ERROR);
		}

		$this->fields .= ", CASE 
							WHEN rp.nr_cracha IS NULL THEN 0
				            ELSE 1
						 END AS ie_solicitado,
						 COALESCE(rp.ds_ref_alt, '') AS ds_ref_alt ";

		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);	
			$sortField = empty($sortField) ? " 1 " : $sortField;
		}

		$query = "SELECT {$this->fields}				                
				FROM refeicao r
				 LEFT JOIN refeicao_pedidos rp
				  ON r.nr_refeicao = rp.nr_refeicao
				  AND rp.nr_cracha = :nr_cracha
				 LEFT JOIN pessoa_fisica p
				  ON p.nr_cracha = :nr_cracha 
				WHERE (p.dt_demissao IS NULL 
					    OR p.dt_demissao <= r.dt_refeicao) 
				 AND r.ie_situacao = 'A' ";	

		if (!isset($params['dates'])) {
			$query .= "AND STR_TO_DATE(CONCAT(r.dt_refeicao, ' ', '23:59:59'), 
								   '%Y-%m-%d %H:%i:%s') >= SYSDATE() ";
		}		 

		foreach($params as $key => $value) {
		
			switch($key) {
				case 'nr_refeicao':
					$query .= "AND r.nr_refeicao = :nr_refeicao ";
					break;
				case 'ie_tipo_refeicao':
					$query .= "AND r.ie_tipo_refeicao = :ie_tipo_refeicao ";
					break;
				case 'ds_refeicao':
					$query .= "AND r.ds_refeicao LIKE :ds_refeicao ";
					break;				
				case 'dates':
					$query .= "AND r.dt_refeicao BETWEEN :dt_inicio AND :dt_fim ";
			} 

		}

		$order = empty($sortField) ? " r.dt_refeicao " : " $sortField ";
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimit = $limit != "ALL" ? "LIMIT :limit" : "";
		
		$query .= "ORDER BY $order 
					$queryLimit";
		
		try {

			$stmt = $this->conn->getConn()->prepare($query);

			foreach($params as $key => $value) {	
				switch($key) {
					case 'nr_refeicao': 
						$stmt->bindValue(':nr_refeicao', $value);
						break;
					case 'nr_cracha':
						$stmt->bindValue(':nr_cracha', $value);
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

			$menuRequested = $stmt->fetchALL(\PDO::FETCH_ASSOC);

			for ($i=0; $i < count($menuRequested); $i++) {
				if (strlen($menuRequested[$i]['ds_ref_alt']) > 0) {
					$menuRequested[$i]['ie_ref_alt'] = 1;
				} else {
					$menuRequested[$i]['ie_ref_alt'] = 0;
				}
			}
			
			return $menuRequested;

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}		
	}

	public function mealRequest($request)
	{
		$this->table = "refeicao_pedidos";
		return parent::create($request, array(
									"showId" => true
							));
	}

	public function listRequest(\DateTime $dt_refeicao) 
	{
		$query = "SELECT rp.nr_cracha FROM refeicao r
			INNER JOIN refeicao_pedidos rp ON r.nr_refeicao = rp.nr_refeicao
			WHERE r.dt_refeicao = ?";

		return $this->select($query, $dt_refeicao->format('Y-m-d'));
	}

	public function updateMealRequest($nr_cracha, $nr_refeicao, $requestUpdate)
	{	
		$this->table = "refeicao_pedidos";
		$where = [
					[
						"field" => "nr_cracha",
						"operator" => "=",
						"value" => $nr_cracha
					],
					[
						"logical_op" => "AND",
						"field" => "nr_refeicao",
						"operator" => "=",
						"value" => $nr_refeicao
					]
				];

		return parent::update($requestUpdate, $where);
	}

	public function deleteMealRequest($nr_cracha, $nr_refeicao)
	{
		$this->table = "refeicao_pedidos";
		$where = [
					[
						"field" => "nr_cracha",
						"operator" => "=",
						"value" => $nr_cracha
					],
					[
						"logical_op" => "AND",
						"field" => "nr_refeicao",
						"operator" => "=",
						"value" => $nr_refeicao
					]
				];

		return parent::delete($where);		
	}
}