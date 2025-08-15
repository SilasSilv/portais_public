<?php 
namespace api\v1\resource\category\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;

Class CategoryDAO extends Crud {

	public function __construct() 
	{
		parent::__construct();
		$this->table = 'tb_categoria';
		$this->fields = 'c.cd_categoria, tc.ds_tipo_categoria, c.cd_tipo_categoria, c.ds_categoria';
	}

	public function tableFieldsPrefix($fields) 
	{
		$tempFields = explode(",", $fields);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = trim($tempFields[$i]);

			switch($field) {				
				case 'ds_tipo_categoria':
					$fields .= "tc.ds_tipo_categoria";
					break;
				default:
					$fields .= "c.$field";
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
				case 'ds_tipo_categoria':
					$fields .= "tc.ds_tipo_categoria";
					break;
				default:
					$fields .= "c.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	public function findCategory($cd_categoria, $fields)
	{
		if (!empty($fields))
			$this->fields = $this->tableFieldsPrefix($fields);

		$query = "SELECT  {$this->fields}
				   FROM  tb_categoria c
				  INNER JOIN tb_tipo_categoria tc
				   ON c.cd_tipo_categoria = tc.cd_tipo_categoria  
				  WHERE c.cd_categoria = :cd_categoria";

		try {		   

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindParam(":cd_categoria", $cd_categoria);

			$stmt->execute();

			$category = $stmt->fetch(\PDO::FETCH_ASSOC);

			$category = is_array($category) ? $category : [];

			return $category;

		} catch (\PDOException $e) {
			
			Error::generateErrorDb($e);

		}
	}

	public function findCategoryAll($params, $fields, $sortField, $sortingType, $limit, $offset)
	{
		if (!empty($fields)) {
			$this->fields = $this->tableFieldsPrefix($fields);
			$sortField = empty($sortField) ? 1 : $sortField;
		}

		$query = "SELECT  {$this->fields}
				   FROM  tb_categoria c
				  INNER JOIN tb_tipo_categoria tc
				   ON c.cd_tipo_categoria = tc.cd_tipo_categoria ";
		
		$i = 0;
		$conditionPrefix = "WHERE ";
		foreach($params as $key => $value) {
			if($i > 0)
				$conditionPrefix = "AND ";
		
			switch($key) {
				case 'cd_tipo_categoria':
					$query .= "$conditionPrefix c.cd_tipo_categoria LIKE :cd_tipo_categoria ";
					break;
			} 

			$i++;
		}

		$order = empty($sortField) ? "c.cd_tipo_categoria, c.ds_categoria" : ($sortField == 1 ? $sortField : $this->sortFieldsPrefix($sortField));
		$order .= empty($sortingType) ? " " : " $sortingType";
		$queryLimitOffset = $limit != "ALL" ? "LIMIT :limit OFFSET :offset" : "";
		
		$query .= "ORDER BY $order 
					$queryLimitOffset";

		try {
		  
			$stmt = $this->conn->getConn()->prepare($query);
			
			foreach($params as $key => $value) {	
				switch($key) {
					case 'cd_tipo_categoria':
						$stmt->bindValue(':cd_tipo_categoria', '%' . $value . '%');
						break;		
				} 
			}

			if ($limit != "ALL") {
				$limit = intval($limit);
				$offset = intval($offset);
				$stmt->bindParam(":limit", $limit, \PDO::PARAM_INT);
				$stmt->bindParam(":offset", $offset, \PDO::PARAM_INT);
			}

			$stmt->execute();

			$categorys = $stmt->fetchALL(\PDO::FETCH_ASSOC);

			$categorys = is_array($categorys) ? $categorys : [];

			return $categorys;

		} catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public function register($category) 
	{			
		return parent::create($category, [
											"showId" => true 
									  	 ]);
	}

	public function updateCategory($cd_categoria, $category) 
	{
		$where = [
					[
						"field" => "cd_categoria",
						"operator" => "=",
						"value" => $cd_categoria
					]
				];

		return parent::update($category, $where);
	}

	public function deleteCategory($cd_categoria) 
	{
		$where = [
					[
						"field" => "cd_categoria",
						"operator" => "=",
						"value" => $cd_categoria
					]
				];
		
		return parent::delete($where);
	}

}