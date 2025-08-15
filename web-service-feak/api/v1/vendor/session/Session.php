<?php

namespace api\v1\vendor\session;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\UniqueID;

Class Session extends Crud {

	public function __construct()
	{
		parent::__construct();
		$this->table = 'tb_sessao';	
	}

	public function dataDevice()
	{
		 $browser = $_SERVER['HTTP_USER_AGENT'];

	    // Para não armazenar a string inteira do http_user_agent foi feito uma conversão
	    if (strpos($browser, 'Chrome') && !strpos($browser, 'Edge'))
	        $browser = 'Chrome';
	    elseif (strpos($browser, 'Firefox'))
	        $browser = 'Firefox';
	    elseif (strpos($browser, 'MSIE') || strpos($browser, 'Trident'))
	        $browser = 'IE';
	    elseif (strpos($browser, 'Edge'))
	        $browser = 'Edge';

	    return array(
	    		"ip" => $_SERVER['REMOTE_ADDR'],
	        	"hostname" => gethostbyaddr($_SERVER["REMOTE_ADDR"]),
	        	"browser" => $browser
	        );
	}

	public function getSession($token)
	{
		try {

			$query = "SELECT s.token, s.refresh_token, s.expire, s.cd_sistema,
							 s.expire_unix, s.nr_cracha, p.cd_setor, p.cd_grupo,
							 p.ie_alterar_senha
					   FROM tb_sessao s
					  	INNER JOIN pessoa_fisica p 
					  	 ON s.nr_cracha = p.nr_cracha
					  WHERE s.token = :token
					  	AND s.dt_fim IS NULL";

			$stmt = $this->conn->getConn()->prepare($query);
			$stmt->bindParam(':token', $token);

			$stmt->execute();

			$session = $stmt->fetch(\PDO::FETCH_ASSOC);
			
			return $session;

		} catch (\PDOException $e) {

			Error::generateErrorApi($e->getCode());

		}
	}

	public function createSession($cd_sistema, $nr_cracha, $expire = 3600)
	{	
		/*
			Caso o cliente não tenha realizado o logout antes de criar 
			uma nova sessão é preenchido a data fim da sessão anterior 
		*/
		$data = ["dt_fim" => date('Y-m-d H:i:s')];

		$where = [
					[
						"field" => "nr_cracha",
						"operator" => "=",
						"value" => $nr_cracha
 					],
					[
						"logical_op" => "AND",
						"field" => "dt_fim",
						"operator" => "IS NULL"
					]
				];

		parent::update($data, $where);

		//Criar Sessão

		$dataDevice = $this->dataDevice();	
		$expireUnix	= time() + $expire;
		$dateExpire = date('Y-m-d H:i:s', $expireUnix);

		$session["token"] = UniqueID::uuid();
		$session["refresh_token"] = UniqueID::uuid();
		$session["expire"] = $dateExpire;

		$dataSession = [
							"token" => $session["token"],
							"refresh_token" => $session["refresh_token"],
							"cd_sistema" => $cd_sistema,					
							"nr_cracha" => $nr_cracha,
							"ip" => $dataDevice["ip"],
							"hostname" => $dataDevice["hostname"],
							"navegador" => $dataDevice["browser"],
							"expire" => $dateExpire,	
							"expire_unix" => $expireUnix,	
							"dt_inicio" => date('Y-m-d H:i:s')
						];

		parent::create($dataSession);

		return $session;
	}

	public function updateSession($token, $refresh_token, $expire = 3600)
	{	
		$expire_unix = time() + $expire;
		$expireDB = date('Y-m-d H:i:s', $expire_unix);
		$data = ["expire" => $expireDB,
				 "expire_unix" => $expire_unix];

		$where = [
					[
						"field" => "refresh_token",
						"operator" => "=",
						"value" => $refresh_token
					],
					[	
						"logical_op" => "AND",
						"field" => "token",
						"operator" => "=",
						"value" => $token
					],
					[
						"logical_op" => "AND",
						"field" => "dt_fim",
						"operator" => "IS NULL"
					]
				];
		
		parent::update($data, $where);

		return $this->getSession($token);	
	}

	public function destroySession($token)
	{
		$data = ["dt_fim" => date('Y-m-d H:i:s')];

		$where = [
					[
						"field" => "token",
						"operator" => "=",
						"value" => $token
					]
				];

		parent::update($data, $where);
	}

} 