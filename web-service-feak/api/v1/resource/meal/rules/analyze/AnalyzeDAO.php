<?php

namespace api\v1\resource\meal\rules\analyze;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class AnalyzeDAO extends Crud {

	public function __construct()
	{	
		parent::__construct();
		$this->table = "refeicao";
	}

	public function parserSortField($fields)
	{
		$tempFields = explode(',', $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			switch (trim($tempFields[$i])) {
				case 'dt_refeicao':
					$fields .= "DATE_FORMAT(r.". trim($tempFields[$i]) .", '%Y-%m-%d')";
					break;					
				default:
					$fields .= $tempFields[$i];
			}

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findPublicSynthetic($params, $sortField, $sortingType, $limit, $offset)
	{		
		$query = "SELECT r.nr_refeicao,
										 r.ds_refeicao,
										 DATE_FORMAT(r.dt_refeicao, '%d/%m/%Y') AS dt_refeicao,
										 CASE r.ie_tipo_refeicao
										  WHEN 'A' THEN 'Almoço'
										  WHEN 'J' THEN 'Jantar'
											ELSE 'Outro'
										 END AS ie_tipo_refeicao,
										 SUM(CASE 
												WHEN rp.nr_refeicao IS NOT NULL THEN 1
                                                ELSE 0
											  END) AS qt_solicitacao,
										 (SELECT COUNT(1)
											 FROM refeicao_terceiros rt
											WHERE rt.dt_refeicao = r.dt_refeicao) AS qt_dobra,
										 SUM(CASE  
												  WHEN rp.ds_ref_alt IS NULL THEN 0
													ELSE 1
											   END) AS qt_troca,
										 SUM(CASE 
												WHEN rp.nr_refeicao IS NOT NULL THEN 1
                                                ELSE 0
											  END) +  
												(SELECT COUNT(1)
												  FROM refeicao_terceiros rt
											   WHERE rt.dt_refeicao = r.dt_refeicao) AS qt_total
							FROM refeicao r
							LEFT JOIN refeicao_pedidos rp
							ON r.nr_refeicao = rp.nr_refeicao
							WHERE r.dt_refeicao BETWEEN :dt_inicio AND :dt_fim 
								AND r.ie_situacao = 'A'";

		if (array_key_exists('ie_tipo_refeicao', $params)) {
			$query .= "AND r.ie_tipo_refeicao = :ie_tipo_refeicao ";
		}

		$order = empty($sortField) ? " DATE_FORMAT(r.dt_refeicao, '%Y-%m-%d') " : $this->parserSortField($sortField);
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";

		$query .= "GROUP BY r.nr_refeicao,
							r.ds_refeicao,
							r.dt_refeicao,
							r.ie_tipo_refeicao
					ORDER BY $order 
						$queryLimitOffset";
								 
		try {
			$stmt = $this->conn->getConn()->prepare($query);

			foreach($params as $key => $value) {				
				switch($key) {					
					case "dates":
						$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
						$stmt->bindValue(":dt_fim", $value['dt_fim']);
						break;
					case "ie_tipo_refeicao":	
						$stmt->bindValue(":ie_tipo_refeicao", $value);
				}
			}

			if ($limit != "ALL") {
				$limit = intval($limit);
				$offset = intval($offset);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
				$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
			}

			$stmt->execute();

			$meals = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return $meals;

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	public function findPublicAnalyticalDate($dt_inicio, $dt_fim) 
	{
		$query = "SELECT nr_refeicao, DATE_FORMAT(dt_refeicao, '%d/%m/%Y') AS dt_refeicao FROM refeicao WHERE dt_refeicao BETWEEN ? AND ?";
		$meals = $this->select($query, $dt_inicio, $dt_fim);

		foreach($meals as $key => $meal) {
			$meals[$key]['lista'] = $this->findPublicAnalytical($meal['nr_refeicao']);
		}

		return $meals;
	}

	public function findPublicAnalytical($nr_refeicao) 
	{
		$queryRequest = "SELECT p.nr_cracha, p.nm_pessoa_fisica, s.ds_setor
						FROM refeicao_pedidos rp
						INNER JOIN pessoa_fisica p
						  ON rp.nr_cracha = p.nr_cracha
						INNER JOIN setores s
						  ON p.cd_setor = s.cd_setor
						 WHERE rp.nr_refeicao = :nr_refeicao
						  ORDER BY 2";

		$queryThirdFold = "SELECT CASE rt.ie_terceiro_dobra
											 					WHEN 'D' THEN 'Dobra' 
        							 					WHEN 'T' THEN 'Terceiro'
	   									 				END AS ds_dobra,
       								 				CASE 
											 					WHEN rt.nr_cracha IS NULL THEN rt.nm_pessoa_cartao 
											 					ELSE pf.nm_pessoa_fisica
											 				END AS nm_pessoa,       
											 				pr.nm_pessoa_fisica AS nm_resp
											 FROM refeicao_terceiros rt
												INNER JOIN pessoa_fisica pr
													ON rt.nr_cracha_resp = pr.nr_cracha
												LEFT JOIN pessoa_fisica pf
													ON rt.nr_cracha = pf.nr_cracha
												WHERE rt.dt_refeicao = (SELECT MAX(r.dt_refeicao) 
																									FROM refeicao r
																								WHERE r.nr_refeicao = :nr_refeicao)
												ORDER BY 2";

		$queryUpdate = "SELECT p.nr_cracha, p.nm_pessoa_fisica, s.ds_setor, rp.ds_ref_alt
											FROM refeicao_pedidos rp
												INNER JOIN pessoa_fisica p
													ON rp.nr_cracha = p.nr_cracha
												INNER JOIN setores s
													ON p.cd_setor = s.cd_setor
											WHERE rp.nr_refeicao = :nr_refeicao
												AND rp.ds_ref_alt IS NOT NULL
											ORDER BY 2";

		try {
			$stmtRequest = $this->conn->getConn()->prepare($queryRequest);
			$stmtThirdFold = $this->conn->getConn()->prepare($queryThirdFold);
			$stmtUpdate = $this->conn->getConn()->prepare($queryUpdate);

			$stmtRequest->bindValue(':nr_refeicao', $nr_refeicao);
			$stmtThirdFold->bindValue(':nr_refeicao', $nr_refeicao);
			$stmtUpdate->bindValue(':nr_refeicao', $nr_refeicao);

			$stmtRequest->execute();
			$stmtThirdFold->execute();
			$stmtUpdate->execute();

			$requets = $stmtRequest->fetchAll(\PDO::FETCH_ASSOC);
			$thirdsFolds = $stmtThirdFold->fetchAll(\PDO::FETCH_ASSOC);
			$update = $stmtUpdate->fetchAll(\PDO::FETCH_ASSOC);

			return ["solicitacoes" => $requets, "dobras" => $thirdsFolds, "trocas" => $update];

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	public function findPrivate($params, $sortField, $sortingType, $limit, $offset)
	{
		$query = "SELECT 	r.nr_refeicao,
    								 	r.ds_refeicao,
											DATE_FORMAT(r.dt_refeicao, '%d/%m/%Y') AS dt_refeicao,
											CASE r.ie_tipo_refeicao
													WHEN 'A' THEN 'Almoço'
													ELSE 'Jantar'
											END AS ie_tipo_refeicao,
											COALESCE((SELECT rp.ds_ref_alt
														FROM refeicao_pedidos rp
													  WHERE rp.nr_refeicao = r.nr_refeicao
														AND rp.nr_cracha = :nr_cracha) , 'Nenhuma') AS ds_ref_alt,
										    COALESCE((SELECT 'SIM'
														FROM refeicao_pedidos rp
													  WHERE rp.nr_refeicao = r.nr_refeicao
														AND rp.nr_cracha = :nr_cracha), 'NÃO') AS ie_solicitacao
							FROM refeicao r
							WHERE r.dt_refeicao BETWEEN :dt_inicio AND :dt_fim 
								AND r.ie_situacao = 'A'";

		if (array_key_exists('ie_tipo_refeicao', $params)) {
			$query .= "AND r.ie_tipo_refeicao = :ie_tipo_refeicao ";
		}

		$order = empty($sortField) ? " DATE_FORMAT(r.dt_refeicao, '%Y-%m-%d') " : $this->parserSortField($sortField);
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";

		$query .= "ORDER BY $order 
								$queryLimitOffset";

		try {
			$stmt = $this->conn->getConn()->prepare($query);

			foreach($params as $key => $value) {				
				switch($key) {
					case "nr_cracha":
						$stmt->bindValue(":nr_cracha", $value);
						break;					
					case "dates":
						$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
						$stmt->bindValue(":dt_fim", $value['dt_fim']);
						break;
					case "ie_tipo_refeicao":	
						$stmt->bindValue(":ie_tipo_refeicao", $value);
				}
			}

			if ($limit != "ALL") {
				$limit = intval($limit);
				$offset = intval($offset);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
				$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
			}

			$stmt->execute();

			$requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

			return $requests;

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	public function findPayroll($params, $sortField, $sortingType, $limit, $offset)
	{
		$query = "SELECT	/*p.nr_cracha*/ p.ds_login_alternativo,
							p.nm_pessoa_fisica,
							s.ds_setor,
							COUNT(1) AS qt_solicitacao,
							CASE vr.ie_situacao 
								WHEN 'A' THEN concat('R$ ', format(SUM(COALESCE(vr.vl_refeicao, 0)), 2,'de_DE'))
					            ELSE concat('R$ ', format(0, 2,'de_DE'))
							END AS vl_refeicao
				FROM refeicao r
					INNER JOIN refeicao_pedidos rp
						ON r.nr_refeicao = rp.nr_refeicao
					INNER JOIN pessoa_fisica p
						ON rp.nr_cracha = p.nr_cracha
					INNER JOIN setores s
						ON p.cd_setor = s.cd_setor
					LEFT JOIN valor_refeicao vr
						ON r.dt_refeicao BETWEEN vr.dt_vigencia_inicial AND vr.dt_vigencia_final
				WHERE r.dt_refeicao BETWEEN :dt_inicio AND :dt_fim
					AND r.ie_situacao = 'A'";

		foreach ($params as $key => $value) {
			switch ($key) {
				case "cracha_ou_nome":
					$query .= "AND (p.nr_cracha = :nr_cracha OR p.nm_pessoa_fisica LIKE :nm_pessoa_fisica) ";
					break;
				case "ie_tipo_refeicao":
					$query .= "AND r.ie_tipo_refeicao = :ie_tipo_refeicao ";
			}
		}

		$order = empty($sortField) ? " p.nm_pessoa_fisica " : $this->parserSortField($sortField);
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";

		$query .= "GROUP BY p.nr_cracha,
												p.nm_pessoa_fisica,
												s.ds_setor
								ORDER BY $order 
									$queryLimitOffset";

		try {
			$stmt = $this->conn->getConn()->prepare($query);

			foreach($params as $key => $value) {				
				switch($key) {
					case "cracha_ou_nome":
						$stmt->bindValue(':nr_cracha', $value['nr_cracha']);
						$stmt->bindValue(':nm_pessoa_fisica', $value['nm_pessoa_fisica'].'%');
						break;					
					case "dates":
						$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
						$stmt->bindValue(":dt_fim", $value['dt_fim']);
						break;
					case "ie_tipo_refeicao":	
						$stmt->bindValue(":ie_tipo_refeicao", $value);
				}
			}

		if ($limit != "ALL") {
			$limit = intval($limit);
			$offset = intval($offset);
			$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
			$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
		}

		$stmt->execute();

		$requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		return $requests;

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}
} 