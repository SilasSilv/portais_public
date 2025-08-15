<?php

namespace api\v1\vendor\authorization;

use \api\v1\vendor\db\Conn;
use \api\v1\vendor\error\Error;

Class Authorization {

	private static $queryBase = "SELECT COALESCE(vl_pf, vl_setor, vl_padrao) AS vl_permissao
									FROM (SELECT  p.cd_permissao,
									              p.vl_padrao,
												  (SELECT pf.vl_pf
												    FROM tb_permissao_pf pf
												   WHERE pf.cd_permissao = p.cd_permissao
													AND pf.nr_cracha = :nr_cracha) AS vl_pf,
												  (SELECT s.vl_setor 
												    FROM tb_permissao_setor s
												   WHERE s.cd_permissao = p.cd_permissao
													AND s.cd_setor = :cd_setor) AS vl_setor 
											FROM tb_permissao p ";

	public static function checkPermission($cd_permissao, $nr_cracha=0, $cd_setor=0) 
	{
		$valuePermission = self::findPermission($cd_permissao, $nr_cracha, $cd_setor);

		if ($valuePermission == 'S') {
			return true;
		}

		return false;
	}

	public static function checkSystemAccess($cd_sistema, $cd_permissao, $nr_cracha=0, $cd_setor=0) 
	{
		$valuePermission = self::findSystemAccess($cd_sistema, $cd_permissao, $nr_cracha, $cd_setor);

		if ($valuePermission == 'S') {
			return true;
		}

		return false;
	}

	private static function findPermission($cd_permissao, $nr_cracha, $cd_setor)
	{
		try {

		    $query = self::$queryBase . "WHERE p.cd_permissao = :cd_permissao
		    								AND p.ie_situacao = 'A') x";   
			
			$conn = new Conn();

		    $stmt = $conn->getConn()->prepare($query);
		    $stmt->bindParam(":nr_cracha", $nr_cracha);
		    $stmt->bindParam(":cd_setor", $cd_setor);
		    $stmt->bindParam(":cd_permissao", $cd_permissao);
		
		    $stmt->execute();

		    $permission = $stmt->fetch(\PDO::FETCH_ASSOC);
		    
		    return is_array($permission) ? $permission['vl_permissao'] : false;

		} catch(\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	private static function findSystemAccess($cd_sistema, $cd_permissao, $nr_cracha, $cd_setor)
	{
		try {


			if ($cd_permissao == null) { 

		    	$query = self::$queryBase . "WHERE p.cd_sistema = :cd_sistema
		    								  AND p.cd_tipo_permissao = 68
		    								  AND p.ie_situacao = 'A') x 
		    								 LIMIT 1"; 

			} else {

				$query = self::$queryBase . "WHERE p.cd_sistema = :cd_sistema 
											  AND p.cd_tipo_permissao = 68
											  AND p.cd_permissao = :cd_permissao
											  AND p.ie_situacao = 'A') x";

			}

			$conn = new Conn();

		    $stmt = $conn->getConn()->prepare($query);

		    if ($cd_permissao == 0) {

		    	$stmt->bindParam(":nr_cracha", $nr_cracha);
		    	$stmt->bindParam(":cd_setor", $cd_setor);
		    	$stmt->bindParam(":cd_sistema", $cd_sistema);

		    } else {

		    	$stmt->bindParam(":nr_cracha", $nr_cracha);
		    	$stmt->bindParam(":cd_setor", $cd_setor);
		    	$stmt->bindParam(":cd_sistema", $cd_sistema);
		    	$stmt->bindParam(":cd_permissao", $cd_permissao);

		    }
		    
		
		    $stmt->execute();

		    $permission = $stmt->fetch(\PDO::FETCH_ASSOC);
		    
		    return is_array($permission) ? $permission['vl_permissao'] : false;

		} catch(\PDOException $e) {

			Error::generateErrorDb($e);

		}
	}

	public static function findSystemAll($cd_sistema, $nr_cracha, $cd_setor, $type='All')
	{
		try {

			$query = "SELECT cd_permissao, ds_permissao, COALESCE(vl_pf, vl_setor, vl_padrao) AS vl_permissao
									FROM (SELECT  p.cd_permissao,
												  COALESCE(p.ds_descricao_cliente, p.ds_titulo) AS ds_permissao,
									              p.vl_padrao,
												  (SELECT pf.vl_pf
												    FROM tb_permissao_pf pf
												   WHERE pf.cd_permissao = p.cd_permissao
													AND pf.nr_cracha = :nr_cracha) AS vl_pf,
												  (SELECT s.vl_setor 
												    FROM tb_permissao_setor s
												   WHERE s.cd_permissao = p.cd_permissao
													AND s.cd_setor = :cd_setor) AS vl_setor 
											FROM tb_permissao p ";


			if ($type == 'All') { 

		    	$query .= "WHERE p.cd_sistema = :cd_sistema
		    			     AND p.cd_tipo_permissao IN (69, 71)
		    			     AND p.ie_situacao = 'A') x"; 

			} elseif ($type == 'Client') {

				$query .=  "WHERE p.cd_sistema = :cd_sistema 
							  AND p.cd_tipo_permissao IN (69, 71)
							  AND p.ie_mostrar_cliente = 'S'										  
							  AND p.ie_situacao = 'A') x";

			} else {

				Error::generateErrorApi(Error::INTERNAL_ERROR);

			}

			$conn = new Conn();

		    $stmt = $conn->getConn()->prepare($query);

	    	$stmt->bindParam(":nr_cracha", $nr_cracha);
	    	$stmt->bindParam(":cd_setor", $cd_setor);
	    	$stmt->bindParam(":cd_sistema", $cd_sistema);		    
		
		    $stmt->execute();

		    $permissions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		    $result = [];
		    foreach($permissions as $perm) {

		    	$result[$perm['cd_permissao']] = [
		    				'ds_permissao' => $perm['ds_permissao'],
		    				'vl_permissao' => $perm['vl_permissao']
		    			];

		    }
		    
		    return  $result;

		} catch(\PDOException $e) {

			Error::generateErrorDb($e);

		}
	} 

}