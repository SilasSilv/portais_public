<?php

namespace api\v1\vendor\db;

use \api\v1\vendor\db\Conn;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\ClearData;
	
class Crud 
{
	protected $table;
	protected $fields;
	protected $fieldFromTo;
	protected $join;
	protected $sortFieldFromTo;
	protected $groupBy;
	protected $conn;

	public function __construct() 
	{	
		$this->fields = " * ";
		$this->fieldFromTo = [];
		$this->join = "";
		$this->sortFieldFromTo = [];
		$this->groupBy = "";
		$this->conn = new Conn();
	}

	protected function create($data, $params=[], $options=[])
	{
		$md5 = array_key_exists("md5", $params) ? $params["md5"] : '';
		$showId = array_key_exists("showId", $params) ? $params["showId"] : false;
		
		if(sizeof($data) > 0) {
			try {				

				$keys = array_keys($data);
				$md5 = explode(',',$md5);
				$query = "INSERT INTO {$this->table} (" . implode(',',$keys) . ") VALUES (:" .  implode(',:',$keys)  . ")";

				$stmt = $this->conn->getConn()->prepare($query);			
				foreach($data as $key => $value) {
					if(in_array($key, $md5)) {
						$value = md5($value);
					} 
					$stmt->bindValue(":{$key}", $value);
				}

				$stmt->execute();

				if ($showId) {
					return $this->conn->getConn()->lastInsertId();
				} else {
					$inserted = $stmt->rowCount() > 0 ? true : false;
					return $inserted;						
				}				
								
			} catch (\PDOException $e) {
				if (array_key_exists('exception_return', $options)) {
					if ($options['exception_return'] == 'S') {
						return $e;
					}
				}
				Error::generateErrorDb($e);
			}			
		}

		return ["Executado" => false,
				"Msg" => "Sem dados para inserir"];		
	}

	protected function readNew($params) 
	{
		try {
			$fields = $params['fields'];
			$where = array_key_exists('where', $params) ? $params['where'] : [];
			$orderBy = "";

			if (array_key_exists('fields_filter', $params)) {
				if (strlen($params['fields_filter']['fields']) > 0) {
					$fields = $this->tableFieldsPrefix($params['fields_filter'], $params['table']['aliases']);
				}				
			} 

			$query = "SELECT $fields FROM {$params['table']['name']} {$params['table']['aliases']} ";
			$query .= array_key_exists('join', $params) ? "{$params['join']} " : "";
			$query .= count($where) > 0 ? "WHERE {$this->resolveWhere($where)} " : "";
			$query .= array_key_exists('group_by', $params) ? "GROUP BY {$params['group_by']} " : "";
			
			if (array_key_exists('sort_fields', $params)) {
				$sortType = array_key_exists('sort_type', $params) ? $params['sort_type'] : "";
				$orderBy = "ORDER BY " . $this->mountSortFieldsDefault($params['sort_fields'], $sortType);
			}

			if (array_key_exists('sort_fields_filter', $params)) {				
				if (strlen($params['sort_fields_filter']['fields']) > 0 ) {					
					$orderBy = "ORDER BY " . $this->sortFieldsPrefix($params['sort_fields_filter'], $params['table']['aliases']) . $sortType;
				}
			}

			$query .= "$orderBy ";
			
			if (array_key_exists('pagination', $params)) {
				$query .= strtoupper($params['pagination']) != "ALL" ? "LIMIT " . ClearData::stringSQL($params['pagination']) . " " : "";
			} else {
				$query .=  "LIMIT 20 ";	
			}

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt = $this->prepareWhere($where, $stmt);

			$stmt->execute();

			$returnType = array_key_exists('return_type', $params) ? $params['return_type'] : \PDO::FETCH_ASSOC;
			return $stmt->fetchAll($returnType);

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}


	public function select($query, ...$params)
	{
		try {
			$stmt = $this->conn->getConn()->prepare($query);

			foreach($params as $key => $value) {
				$stmt->bindValue(($key+1), $value);
			}

			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);

		} catch (\PDOException $e) {
			Error::generateErrorDb($e);
		}
	}

	protected function read($where = '', $fields = '', $order = '', $limit = '', $join = '',  $groupBy = '')
	{
		try {	

			$query = "SELECT " . (!empty($fields) ? $fields : '*');
			$query .= " FROM {$this->table}";
			$query .= !empty($join) ? $this->resolveJoin($join) : '';
			$query .= !empty($where) ? " WHERE {$this->resolveWhere($where)}" : '';
			$query .= !empty($order) ? " ORDER BY $order" : '';
			$query .= !empty($groupBy) ? " GROUP BY $groupBy" : '';
			$query .= !empty($limit) ? "LIMIT $limit" : '';
			
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt = $this->prepareWhere($where,$stmt);

			$stmt->execute();

	        return $stmt->fetchAll(\PDO::FETCH_ASSOC); 

        } catch (\PDOException $e) {

			Error::generateErrorDb($e);

		}			
	}

	protected function update($data, $where='', $md5='', $options=[])
	{		
		if(count($data) == 0) {

			return ["Executado" => false,
					"Msg" => "Sem dados para inserir"];	

		} elseif (empty($where)) {

			return ["Executado" => false,
					"Msg" => "Não é permitido executar update sem where"];	

		} else {

			try {

				$keys = array_keys($data);
				$md5 = explode(',',$md5);
				$query = "UPDATE {$this->table} SET";
				foreach($data as $key => $value) {
					$query .= " {$key} = :{$key},";
				}
				$query = substr($query,0,strlen($query)-1); 
				$query .= " WHERE {$this->resolveWhere($where)}";
	
				$stmt = $this->conn->getConn()->prepare($query);	
				$stmt = $this->prepareWhere($where,$stmt);		
				foreach($data as $key => $value) {
					if(in_array($key, $md5)) {
						$value = md5($value);
					} 
					$stmt->bindValue(":{$key}", $value);
				}

				$stmt->execute();
				$updated = $stmt->rowCount() > 0 ? true : false;
				
				return $updated;

			} catch (\PDOException $e) {
				if (array_key_exists('exception_return', $options)) {
					if ($options['exception_return'] == 'S') {
						return $e;
					}
				}
				Error::generateErrorDb($e);
			}

		}
	}

	protected function delete($where = '')
	{

		if(!empty($where)) {

			try {

				$query = "DELETE FROM {$this->table}";
				$query .= " WHERE {$this->resolveWhere($where)}";
	
				$stmt = $this->conn->getConn()->prepare($query);			
				$stmt = $this->prepareWhere($where,$stmt);
				
				$stmt->execute();
				$deleted = $stmt->rowCount() > 0 ? true : false;

				return $deleted;
				
			} catch (\PDOException $e) {

				Error::generateErrorDb($e);
				
			}

		}

		return ["Executado" => false];
	}

	private function tableFieldsPrefix($fieldsFilter, $aliases) 
	{
		$tempFields = explode(",", $fieldsFilter['fields']);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = ClearData::stringSQL($tempFields[$i]);

			if (array_key_exists($field, $fieldsFilter['from_to'])) {
				$fields .= $fieldsFilter['from_to'][$field];
			} else {
				$fields .= "$aliases.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}

	private function resolveJoin($join)
	{
		$result = ' x ';

		foreach ($join as $value) {						
			switch (strtoupper($value['operation'])) {
				case 'JOIN':
				case 'LEFT JOIN':
				case 'RIGHT JOIN':
					$result .= strtoupper($value['operation']) . ' ' . $value['table'] . ' ON  ' . $value['clause'] . ' ';
					break;
				
				default:
					break;
			}
		}

		return $result;
	}

	protected function mountPartWhere($logicalOp, $aliases, $fieldName, $operator, $fieldValue, $index, $paranthesis='') 
	{
		$partWhere = ["field" => $fieldName, "operator" => $operator, "value" => $fieldValue];
		
		if ($aliases != '') {
			$partWhere['aliases'] = $aliases;
		}

		if ($paranthesis == 'left' || $paranthesis == 'right') {
			$partWhere['paranthesis'] = $paranthesis;
		} else {
			$partWhere['paranthesis'] = '';
		}

		if ($index > 0) {
			$partWhere['logical_op'] = $logicalOp;
		}

		return $partWhere;
	}

	private function resolveWhere($where)
	{
		$result = '';

		foreach ($where as $key => $value) {
			$result .= isset($value['logical_op']) ? $value['logical_op'] . ' ' : '';

			if (array_key_exists('paranthesis', $value) && $value['paranthesis'] == 'left') {
				$result .= ' ( ';
			}

			$aliases = array_key_exists('aliases', $value) ? "{$value['aliases']}." : ""; 
			
			switch (strtoupper($value['operator'])) {
				case 'LIKE':
					$result .= "lower({$aliases}{$value['field']}) " . strtoupper($value['operator']) . " (:{$key}_w) ";
					break;
				case '=':
				case '>':
				case '<':
				case '!=':
				case '>=':
				case '<=':
					$result .= "{$aliases}{$value['field']} " . strtoupper($value['operator']) . " :{$key}_w ";
					break;

				case 'IN':
				case 'NOT IN':	
					$result .= "{$aliases}{$value['field']} " . strtoupper($value['operator']) . ' (';
					foreach (explode(',',$value['value']) as $key_in => $value_in) {
						$result .= ":{$key}_" . $key_in . ',';
					}	
					$result = substr($result,0,strlen($result)-1);			
					$result .= ') ';
					break;

				case 'IS NULL':
				case 'IS NOT NULL':
					$result .= "{$aliases}{$value['field']} " . strtoupper($value['operator']) . ' ';
					break;
					
				default:
					break;
			}

			if (array_key_exists('paranthesis', $value) && $value['paranthesis'] == 'right') {
				$result .= ' ) ';
			}
		}

		return $result;
	}	

	private function prepareWhere($where, $stmt) 
	{
		if(is_array($where)) {
			foreach ($where as  $key => $value) {							
				switch (strtoupper($value['operator'])) {
					case 'LIKE':
						$valueLike = mb_strtolower($value['value'], 'UTF-8');
						$stmt->bindValue(":{$key}_w", $valueLike);
						break;
					case '=':
					case '>':
					case '<':
					case '!=':
					case '>=':
					case '<=':
						$stmt->bindValue(":{$key}_w",$value['value']);
						break;

					case 'IN':
					case 'NOT IN':	
						foreach (explode(",",$value['value']) as $key_in => $value_in) {
							$stmt->bindValue(":{$key}_{$key_in}", $value_in);
						}	
						break;	

					default:
						break;
				}
			}
		}

	
		return $stmt;
	}

	private function mountSortFieldsDefault($pOrderBy, $sortType = "") {
		$orderBy = "";
		$i = 0;

		foreach ($pOrderBy as $value) {
			$orderBy .= $i == 0 ? "" : ",";

			if (strlen($sortType) == 0) {
				$orderBy .= "{$value['field']} {$value['type']}"; 				
			} else {
				$orderBy .= "{$value['field']}";
			}

			$i++;
		}

		if (strlen($orderBy) > 0 && strlen($sortType) > 0) {
			$orderBy .= " $sortType";
		}

		return $orderBy;
	}

	private function sortFieldsPrefix($sortFields, $aliases) 
	{
		$tempFields = explode(",", $sortFields['fields']);
		$fields = "";

		for($i=0; $i < count($tempFields); $i++) {
			$field = ClearData::stringSQL($tempFields[$i]);

			if (array_key_exists($field, $sortFields['from_to'])) {
				$fields .= ClearData::stringSQL($sortFields['from_to'][$field]);
			} else {
				$fields .= "$aliases.$field";
			}
				
			$fields .= ($i+1) < count($tempFields) ? "," : " ";
		}

		return $fields;
	}
}