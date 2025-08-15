<?php

namespace api\v1\vendor\db;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\error\LogError;

class Conn {

	private $host; 
	private $dbname;
	private $user;
	private $password;
	private $conn;

	//Função recebe os paramêtros para a conexão
	public function __construct ($host = "localhost", $dbname = "refsoft", $user = "suporte", $password = "123") 
	{
		$this->host = $host;
		$this->dbname = $dbname;
		$this->user = $user;
		$this->password = $password;
	}

	//Função realiza a conexão com o banco
	private function Connection()
	{
		try {

			$this->conn = new \PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password,
            	    array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'));
        	$this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);	

    	} catch(\PDOException $e) {

    		LogError::generateLogErrorCritical('DataBase', ["message" => "Connection error in database"]);
    		Error::generateErrorApi(Error::INTERNAL_ERROR);
		
		}
	} 

	//Função retorna a Conexão para as operações SQL
	public function getConn() {
		if (!isset($this->conn)) {
			$this->Connection();	
		}

		return $this->conn;
	}

}