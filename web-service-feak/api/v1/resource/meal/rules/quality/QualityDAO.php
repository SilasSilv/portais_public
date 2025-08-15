<?php 

namespace api\v1\resource\meal\rules\quality;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class QualityDAO extends Crud {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'qualidade';
		$this->fields =  "q.nr_sequencia, 
						   q.nr_cracha, p.nm_pessoa_fisica,
						   q.cd_apresentacao, c1.ds_categoria AS ds_apresentacao,
						   q.cd_temperatura, c2.ds_categoria AS ds_temperatura,
						   q.cd_sabor, c3.ds_categoria AS ds_sabor,      
						   q.cd_simpatia, c4.ds_categoria AS ds_simpatia,
						   q.cd_higiene_loc, c5.ds_categoria AS ds_higiene_loc,
						   DATE_FORMAT(q.dt_inclusao, '%d/%m/%Y %H:%i:%s') AS dt_inclusao";
	}

	private function updateEvaluationsPresentation($pEvaluations)
	{
		$evaluations = [];

		foreach($pEvaluations as $evaluation) {
			$avaliacao = strtolower(str_replace('Ótimo', 'Otimo', $evaluation["ds_categoria"]));

			$evaluations["apresentacao"][$avaliacao] = $evaluation["apresentacao"]; 
			$evaluations["temperatura"][$avaliacao] =  $evaluation["temperatura"];
			$evaluations["sabor"][$avaliacao] =  $evaluation["sabor"];
			$evaluations["simpatia"][$avaliacao] =  $evaluation["simpatia"];
			$evaluations["cd_higiene_loc"][$avaliacao] =  $evaluation["cd_higiene_loc"];
		}

		foreach($pEvaluations as $evaluation) {
			$evaluations["apresentacao"]["resumo"][] = $evaluation["apresentacao"];
			$evaluations["temperatura"]["resumo"][] = $evaluation["temperatura"];
			$evaluations["sabor"]["resumo"][] = $evaluation["sabor"];
			$evaluations["simpatia"]["resumo"][] = $evaluation["simpatia"];
			$evaluations["cd_higiene_loc"]["resumo"][] = $evaluation["cd_higiene_loc"];
		}

		return $evaluations;
	} 

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {

			switch ($tempFields[$i]) {
				case "ds_apresentacao":
					$fields .= "c1.ds_categoria AS ds_apresentacao";
					break;
				case "ds_temperatura":
					$fields .= "c2.ds_categoriaAS ds_temperatura";
					break;
				case "ds_sabor":
					$fields .= "c3.ds_categoria AS ds_sabor";
					break;
				case "ds_simpatia":
					$fields .= "c4.ds_categoria AS ds_simpatia";
					break;
				case "ds_higiene_loc":
					$fields .= "c5.ds_categoriaAS ds_higiene_loc";
					break;
				case "nm_pessoa_fisica":
					$fields .= "p.nm_pessoa_fisica";
					break;
				case "dt_inclusao":
					$fields .= "DATE_FORMAT(q.dt_inclusao, '%d/%m/%Y %H:%i:%s') AS dt_inclusao";
					break;
				default:
					$fields .= "q.{$tempFields[$i]}";
			}	

			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findQuality($nr_sequencia, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields}
				 	FROM qualidade q
				  INNER JOIN pessoa_fisica p
				   ON q.nr_cracha = p.nr_cracha
				  INNER JOIN tb_categoria c1
				   ON q.cd_apresentacao = c1.cd_categoria
				  INNER JOIN tb_categoria c2
				   ON q.cd_temperatura = c2.cd_categoria
				  INNER JOIN tb_categoria c3
				   ON q.cd_sabor = c3.cd_categoria
				  INNER JOIN tb_categoria c4
				   ON q.cd_simpatia = c4.cd_categoria
				  INNER JOIN tb_categoria c5
				   ON q.cd_higiene_loc = c5.cd_categoria 
				  WHERE q.nr_sequencia = :nr_sequencia";

		try {
 			  
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindValue(':nr_sequencia', $nr_sequencia);

			$stmt->execute();

			return $stmt->fetchALL(\PDO::FETCH_ASSOC);	

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findQualityAll($params, $fields, $sortField, $sortingType, $limit)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT {$this->fields}
				 	FROM qualidade q
				  INNER JOIN pessoa_fisica p
				   ON q.nr_cracha = p.nr_cracha
				  INNER JOIN tb_categoria c1
				   ON q.cd_apresentacao = c1.cd_categoria
				  INNER JOIN tb_categoria c2
				   ON q.cd_temperatura = c2.cd_categoria
				  INNER JOIN tb_categoria c3
				   ON q.cd_sabor = c3.cd_categoria
				  INNER JOIN tb_categoria c4
				   ON q.cd_simpatia = c4.cd_categoria
				  INNER JOIN tb_categoria c5
				   ON q.cd_higiene_loc = c5.cd_categoria  ";

		$i = 0;
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if($i > 0)
				$conditionPrefix = "AND ";

			switch($key) {
				case 'nr_cracha':
					$query .= "$conditionPrefix q.nr_cracha = :nr_cracha ";
					break;
				case 'dates':
					$query .= "$conditionPrefix q.dt_inclusao BETWEEN :dt_inicio AND :dt_fim ";
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
					case 'nr_cracha': 
						$stmt->bindValue(':nr_cracha', $value);
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

	public function checkRegisterEvaluation($nr_cracha) {
		$query = "SELECT COUNT(1) AS avaliacao_refeicao
					FROM qualidade
				  WHERE nr_cracha = :nr_cracha
					AND DATE_FORMAT(dt_inclusao, '%Y-%m') = '" . date('Y-m') . "'";

		try {
			$stmt = $this->conn->getConn()->prepare($query);

			$stmt->bindValue(':nr_cracha', $nr_cracha);

			$stmt->execute();

			return $stmt->fetch(\PDO::FETCH_ASSOC);

		} catch(\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	public function findStatisticsQuality($dt_mes_referencia) {
		$queryAll  = "SELECT COUNT(1)
						FROM qualidade
					  WHERE DATE_FORMAT(dt_inclusao, '%Y-%m') = :dt_mes_referencia";

		$queryDetail = "SELECT c.cd_categoria, ds_categoria, 
							(SELECT COUNT(1) 
								FROM qualidade q 
							WHERE DATE_FORMAT(q.dt_inclusao, '%Y-%m') = :dt_mes_referencia
								AND q.cd_apresentacao = c.cd_categoria) AS apresentacao,
							(SELECT COUNT(1) 
								FROM qualidade q 
							WHERE DATE_FORMAT(q.dt_inclusao, '%Y-%m') = :dt_mes_referencia
								AND q.cd_temperatura = c.cd_categoria) AS temperatura,
							(SELECT COUNT(1) 
								FROM qualidade q 
							WHERE DATE_FORMAT(q.dt_inclusao, '%Y-%m') = :dt_mes_referencia
								AND q.cd_sabor = c.cd_categoria) AS sabor,
							(SELECT COUNT(1) 
								FROM qualidade q 
							WHERE DATE_FORMAT(q.dt_inclusao, '%Y-%m') = :dt_mes_referencia
								AND q.cd_simpatia = c.cd_categoria) AS simpatia,
							(SELECT COUNT(1) 
								FROM qualidade q 
							WHERE DATE_FORMAT(q.dt_inclusao, '%Y-%m') = :dt_mes_referencia
								AND q.cd_higiene_loc = c.cd_categoria) AS cd_higiene_loc
						FROM tb_categoria c 
							WHERE c.cd_tipo_categoria = 7";

		try {
			$stmtAll = $this->conn->getConn()->prepare($queryAll);
			$stmtAll->bindValue(':dt_mes_referencia', $dt_mes_referencia);
			$stmtAll->execute();

			$stmtDetail = $this->conn->getConn()->prepare($queryDetail);
			$stmtDetail->bindValue(':dt_mes_referencia', $dt_mes_referencia);
			$stmtDetail->execute();

			$statisticsQuality = [];
			$statisticsQuality['total_avaliacoes'] = $stmtAll->fetch(\PDO::FETCH_BOTH)[0];
			$statisticsQuality['detalhe_avaliacoes'] = $stmtDetail->fetchAll(\PDO::FETCH_ASSOC);
			
			$statisticsQuality['detalhe_avaliacoes']  = $this->updateEvaluationsPresentation($statisticsQuality['detalhe_avaliacoes']);

			return $statisticsQuality;

		} catch(\PDOException $e) {
			Error::generateErrorDb($e);
		}


	}

	public function register($quality)
	{
		return parent::create($quality, [
											"showId" => true 
										]);
	}

	public function updateQuality($nr_sequencia, $quality)
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia 
					]
				];

		return parent::update($quality, $where);
	}

	public function deleteQuality($nr_sequencia)
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