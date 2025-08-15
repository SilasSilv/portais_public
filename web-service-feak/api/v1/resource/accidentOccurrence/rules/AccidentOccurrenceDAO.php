<?php 

namespace api\v1\resource\accidentOccurrence\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\UrlUtils;

class AccidentOccurrenceDAO extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = 'ocorrencia_acidente';
		$this->fields = "o.nr_sequencia, 
						 o.nr_cracha, 
						 f.nm_pessoa_fisica AS nm_pessoa_fisica_ocorr,
						 CONCAT('" . UrlUtils::getUrl() . "', f.url_foto_perfil) AS url_foto_perfil_ocorr,
						 sf.ds_setor AS ds_setor_ocorr, 
						 cf.ds_cargo AS ds_cargo_ocorr,
						 DATE_FORMAT(o.dt_ocorrencia, '%d/%m/%Y') AS dt_ocorrencia,
						 o.nr_horas, 
						 o.ds_acidente, 
						 o.cd_parte_corpo, 
						 c1.ds_categoria AS ds_parte_corpo,
						 o.ie_situacao_epi,
						 o.ds_epi,
						 o.ds_acidente_evitado,
						 o.ie_situacao_comunicado,
						 o.cd_motivo_acidente,
						 c2.ds_categoria AS ds_motivo_acidente,
						 o.ds_outros,
						 o.ie_situacao_testemunha,
						 o.ds_testemunha,
						 o.ie_lido_cipa,
						 o.ds_parecer_cipa,
						 DATE_FORMAT(o.dt_parecer_cipa, '%d/%m/%Y') AS dt_parecer_cipa, 
						 o.nr_cracha_cipa,
						 o.nr_cracha_inclusao,
						 r.nm_pessoa_fisica AS nm_pessoa_fisica_resp,
						 CONCAT('" . UrlUtils::getUrl() . "', r.url_foto_perfil) AS url_foto_perfil_resp,
						 sr.ds_setor AS ds_setor_resp, 
						 cr.ds_cargo AS ds_cargo_resp,
						 o.ds_local";
		$this->join = "INNER JOIN tb_categoria c1 ON o.cd_parte_corpo = c1.cd_categoria
					   INNER JOIN tb_categoria c2 ON o.cd_motivo_acidente = c2.cd_categoria
					   INNER JOIN pessoa_fisica f ON o.nr_cracha = f.nr_cracha
					   INNER JOIN cargo cf        ON f.cd_cargo = cf.cd_cargo
					   INNER JOIN setores sf      ON f.cd_setor = sf.cd_setor
					   INNER JOIN pessoa_fisica r ON o.nr_cracha_inclusao = r.nr_cracha
					   INNER JOIN cargo cr        ON r.cd_cargo = cr.cd_cargo
					   INNER JOIN setores sr      ON r.cd_setor = sr.cd_setor";
	}

	private function resolveAccidentOccurrence($occurrences)
	{	
		$new_occurrences = [];

		foreach($occurrences as $occurrence) {
			if (array_key_exists('nm_pessoa_fisica_ocorr', $occurrence)) {
				$occurrence['pessoa_ocorrencia'] = array(
						"nm_pessoa_fisica" => $occurrence['nm_pessoa_fisica_ocorr'],
						"url_foto_perfil" => $occurrence['url_foto_perfil_ocorr'],
						"ds_setor" => $occurrence['ds_setor_ocorr'],
						"ds_cargo" => $occurrence['ds_cargo_ocorr']					
					);

				unset($occurrence['nm_pessoa_fisica_ocorr']);
				unset($occurrence['url_foto_perfil_ocorr']);
				unset($occurrence['ds_setor_ocorr']);
				unset($occurrence['ds_cargo_ocorr']);
			}

			if (array_key_exists('nm_pessoa_fisica_resp', $occurrence)) {
				$occurrence['pessoa_cadastrou'] = array(
						"nm_pessoa_fisica" => $occurrence['nm_pessoa_fisica_resp'],
						"url_foto_perfil" => $occurrence['url_foto_perfil_resp'],
						"ds_setor" => $occurrence['ds_setor_resp'],
						"ds_cargo" => $occurrence['ds_cargo_resp']					
					);
				
				unset($occurrence['nm_pessoa_fisica_resp']);
				unset($occurrence['url_foto_perfil_resp']);
				unset($occurrence['ds_setor_resp']);
				unset($occurrence['ds_cargo_resp']);
			}

			$new_occurrences[] = $occurrence;
		}	

		return $new_occurrences;
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = trim($tempFields[$i]);

			switch($field) {
				case 'pessoa_ocorrencia':
					$fields .= "f.nm_pessoa_fisica AS nm_pessoa_fisica_ocorr,
								CONCAT('" . UrlUtils::getUrl() . "', f.url_foto_perfil) AS url_foto_perfil_ocorr,
						 		sf.ds_setor AS ds_setor_ocorr, 
						 		cf.ds_cargo AS ds_cargo_ocorr";
					break;
				case 'pessoa_cadastrou':
					$fields .= "r.nm_pessoa_fisica AS nm_pessoa_fisica_resp,
								CONCAT('" . UrlUtils::getUrl() . "', r.url_foto_perfil) AS url_foto_perfil_resp,
						 		sr.ds_setor AS ds_setor_resp, 
						 		cr.ds_cargo AS ds_cargo_resp";
					break;			
				case 'ds_parte_corpo':
					$fields .= "c1.ds_categoria AS ds_parte_corpo";
					break;
				case 'ds_motivo_acidente':
					$fields .= "c2.ds_categoria AS ds_motivo_acidente";
					break;
				case 'dt_ocorrencia':
					$fields .= "DATE_FORMAT(o.dt_ocorrencia, '%d/%m/%Y') AS dt_ocorrencia";
					break;
				case 'dt_parecer_cipa':
					$fields .= "DATE_FORMAT(o.dt_parecer_cipa, '%d/%m/%Y') AS dt_parecer_cipa";
					break;
				default:
					$fields .= "o.$field";
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
				case 'nm_pessoa_fisica_ocorr':
					$fields .= "f.nm_pessoa_fisica";
					break;
				case 'ds_setor_ps_ocorr':
					$fields .= "sf.ds_setor";
					break;
				case 'ds_cargo_ps_ocorr':
					$fields .= "cf.ds_cargoa";
					break;
				case 'nm_pessoa_fisica_cad_ocorr':
					$fields .= "r.nm_pessoa_fisica";
					break;
				case 'ds_setor_ps_cad_ocorr':
					$fields .= "sr.ds_setor";
					break;
				case 'ds_cargo_ps_cad_ocorr':
					$fields .= "cr.ds_cargoa";
					break;		
				case 'ds_parte_corpo':
					$fields .= "c1.ds_categoria";
					break;
				case 'ds_motivo_acidente':
					$fields .= "c2.ds_categoria";
					break;
				default:
					$fields .= "o.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}


	public function mountWhereAsParams($params, $query, $logicalOp) 
	{
		$i = 0;

		foreach($params as $key => $value) {

			if ($i == 0 && $params["type_search"] == "free") {
				$prefix = "WHERE ";
				$i = 1;
			} else {
				$prefix = $logicalOp;
			}

		 	switch($key) {
		 		case "nr_sequencia":
		 			$query .= " $prefix o.nr_sequencia = :nr_sequencia ";
		 			break;
		 		case "nr_cracha":
		 			$query .= "  $prefix o.nr_cracha = :nr_cracha ";
		 			break;
		 		case "nm_pessoa_fisica":
		 			$query .= "  $prefix f.nm_pessoa_fisica LIKE :nm_pessoa_fisica ";
		 			break;
		 		case "cd_setor":
		 			$query .= $params["type_search"] == "free" ? " $prefix f.cd_setor = :cd_setor " : "";
		 			break;
		 		case "cd_grupo":
		 			$query .= $params["type_search"] == "free" ? " $prefix f.cd_grupo = :cd_grupo " : "";
		 			break;
		 		case "dates":
		 			$query .= " $prefix o.dt_ocorrencia BETWEEN :dt_inicio AND :dt_fim ";
		 			break;
		 		case "ie_lido_cipa":
		 			$query .= "  $prefix o.ie_lido_cipa = :ie_lido_cipa ";
		 	}

		}

		return $query;
	}

	public function prepareWhereAsParams($params, $stmt)
	{
		foreach($params as $key => $value) {

			switch($key) {
					case "nr_sequencia":
		 				$stmt->bindValue(":nr_sequencia", $value);
		 				break;
			 		case "nr_cracha":
			 			$stmt->bindValue(":nr_cracha", $value);
			 			break;
			 		case "nm_pessoa_fisica":
			 			$value = "%$value%";
			 			$stmt->bindValue(":nm_pessoa_fisica", $value);
			 			break;
			 		case "dates":
			 			$stmt->bindValue(":dt_inicio", $value['dt_inicio']);
			 			$stmt->bindValue(":dt_fim", $value['dt_fim']);
			 			break;
			 		case "cd_setor":	
			 			$stmt->bindValue(":cd_setor", $value);
			 			break;
			 		case "cd_grupo":
			 			if ($value != NULL) {
			 				$stmt->bindValue(":cd_grupo", $value);
			 			}		 			
			 			break;
			 		case "ie_lido_cipa":
			 			$stmt->bindValue(":ie_lido_cipa", $value);
			 	}

		}

		return $stmt;		
	}	

	public function findOccurrence($nr_sequencia, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT  {$this->fields}
				   FROM  ocorrencia_acidente o
				  	{$this->join}
				  WHERE o.nr_sequencia = :nr_sequencia";

		try {		   

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindParam(":nr_sequencia", $nr_sequencia);

			$stmt->execute();

			$occurrence = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			$occurrence = $this->resolveAccidentOccurrence($occurrence);

			return $occurrence;

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findOccurrenceAll($params, $fields, $sortField, $sortingType, $limit, $offset, $logicalOp)
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);
			$sortField = empty($sortField) ? 1 : $sortField;
		}

		$query = "SELECT  {$this->fields}
				   FROM  ocorrencia_acidente o
				  {$this->join}	";

		if ($params["type_search"] == "default") {

			if ($params["cd_grupo"] != NULL) {
				$query .= "WHERE (f.cd_setor = :cd_setor OR f.cd_grupo = :cd_grupo) ";
			} else {
				$query .= "WHERE f.cd_setor = :cd_setor ";
			}

		}
		
		$query = $this->mountWhereAsParams($params, $query, $logicalOp);

		$order = empty($sortField) ? "o.dt_ocorrencia" : ($sortField == 1 ? $sortField : $this->sortFieldsPrefix($sortField));
		$order .= empty($sortingType) ? " " : " $sortingType ";
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

			$occurrences = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			$occurrences = $this->resolveAccidentOccurrence($occurrences);

			return $occurrences;

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}
		
	public function checkSeemCipa($nr_sequencia, $type='')
	{
		$where = [
					[
						"field" => "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia
					],
					[
						"logical_op" => "AND",
						"field" => "ie_lido_cipa",
						"operator" => "=",
						"value" => "S"
					]
				];

		$parecer_cipa = parent::read($where,"dt_atualizacao");

		if (count($parecer_cipa) > 0) {
			$dt_atualizacao = strtotime($parecer_cipa[0]["dt_atualizacao"]) + 600;
			$dt_expira = time();
			
			if ($dt_atualizacao >= $dt_expira && $type == 'parecerSESMT') {
				return false; 
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function register($accident_occurrence)
	{
		return parent::create($accident_occurrence, array(
											"showId" => true
										));
	}

	public function updateOccurrence($nr_sequencia, $accident_occurrence)
	{
		$where = [
					[
						"field"	=> "nr_sequencia",
						"operator" => "=",
						"value" => $nr_sequencia
					]
				];

		return parent::update($accident_occurrence, $where);
	}

	public function deleteOccurrence($nr_sequencia)
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