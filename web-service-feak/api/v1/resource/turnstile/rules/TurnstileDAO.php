<?php
namespace api\v1\resource\turnstile\rules;

use \api\v1\vendor\db\Crud;
use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\ClearData;
use \api\v1\resource\meal\rules\menu\MenuDAO;
use \api\v1\resource\turnstile\rules\Turnstile;

class TurnstileDAO extends Crud
{
    public function __construct()
    {
        parent::__construct();
		$this->table = 'henry7x.HE02';
        $this->fields = 'h.HE02_AT_COD, h.HE02_ST_MATRICULA, h.HE02_ST_NOME, h.HE02_BL_DENTRO, h.HE02_NR_HORARIO, h.HE02_FL_ACESSO';
        $this->sortFields = [['field' => 'h.HE02_ST_MATRICULA', 'type' => 'DESC']];
    }

    public function find($params) 
	{
		$params["fields_filter"]["fields"] = array_key_exists('fields', $params) ? $params["fields"] : "";
        $params["fields"] = $this->fields;
        
        $params["table"] = ["name" => $this->table, "aliases" => "h"];;
        $params["where"] = $this->mountWhere($params);

        $params["sort_fields_filter"]["fields"] = array_key_exists('sort_fields', $params) ? $params["sort_fields"] : "";
        $params["sort_fields"] = $this->sortFields;
        
        $params["pagination"] = array_key_exists('pagination', $params) ? $params["pagination"] : 20;

		return parent::readNew($params);
    }
    
    private function mountWhere($params)
	{
		$where = [];
		$logicalOp = "AND";
        $i = 0;

		foreach ($params as $key => $value) {
			switch($key) {
                case 'HE02_ST_MATRICULA': $where['HE02_ST_MATRICULA'] = $this->mountPartWhere($logicalOp, '', 'CAST(h.HE02_ST_MATRICULA AS UNSIGNED)', "IN", $value, $i); 
					break;
				case 'HE02_ST_NOME': $where['HE02_ST_NOME'] = $this->mountPartWhere($logicalOp, "h", $key, "LIKE", "%{$value}%", $i); 
					break;
				case 'HE02_BL_DENTRO': $where['HE02_BL_DENTRO'] = $this->mountPartWhere($logicalOp, "h", $key, "IN", $value, $i); 
                    break;
                case 'HE02_NR_HORARIO': $where['HE02_NR_HORARIO'] = $this->mountPartWhere($logicalOp, "h", $key, "IN", $value, $i); 
                    break;
                case 'HE02_FL_ACESSO': $where['HE02_FL_ACESSO'] = $this->mountPartWhere($logicalOp, "h", $key, "IN", $value, $i); 
					break;
				default: $i--;
			}

			$i++;
        }	
    
		return $where;
    }

    public function findAccess($params) 
	{
        $queryTable = " FROM henry7x.tb_log_catraca l
                            LEFT JOIN pessoa_fisica p ON l.nr_cracha = p.nr_cracha
                            LEFT JOIN tb_cartao c ON l.nr_cartao = c.nr_cartao
                            LEFT JOIN setores s ON l.cd_setor = s.cd_setor ";

		$query = "SELECT l.nr_cracha, l.nr_cartao, p.nm_pessoa_fisica, s.ds_setor, 
                        DATE_FORMAT(l.dt_entrada, '%d/%m/%Y %H:%i:%s') dt_entrada, 
                        DATE_FORMAT(l.dt_saida, '%d/%m/%Y %H:%i:%s') dt_saida, 
                        l.tm_dentro, l.cd_horario,
                        CASE l.cd_horario
                            WHEN 1 THEN 'Café da Manhã'
                            WHEN 2 THEN 'Almoço'
                            WHEN 3 THEN 'Café da Tarde'
                            WHEN 100 THEN 'Fora do Horário'
                            ELSE 'Desconhecido'
                        END ds_horario {$queryTable} ";   
        $query .= $this->mountWhereAccess($params);     
		$query .= " ORDER BY l.dt_entrada DESC
                        LIMIT {$params['pagination']}";

        $queryCount = "SELECT COUNT(1) AS qt_total {$queryTable} ";
        $queryCount .= $this->mountWhereAccess($params); 

		try { 
			$stmt = $this->conn->getConn()->prepare($query);
			$stmt = $this->prepareWhereAccess($params, $stmt);
            $stmt->execute();
            
            $stmtCount = $this->conn->getConn()->prepare($queryCount);
			$stmtCount = $this->prepareWhereAccess($params, $stmtCount);
			$stmtCount->execute();
            
            $access = $stmtCount->fetchAll(\PDO::FETCH_ASSOC)[0];
            $access['acessos'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

		    return $access;
		} catch (\PDOException $e) {			
			Error::generateErrorDb($e);
		}
    }
    
    private function mountWhereAccess($params)
	{
        $where = ' ';
        $y = 0;

		for ($i=0; $i<count($params); $i++) {
            $operator = (($i - $y) == 0) ? 'WHERE ' : 'AND ';

			switch (array_keys($params)[$i]) {
                case 'cd_horario': $where .= $operator . 'l.cd_horario = :cd_horario ';
                    break;
                case 'nr_cracha_cartao': $where .= $operator . '(l.nr_cracha = :nr_cracha_cartao OR l.nr_cartao = :nr_cracha_cartao) ';
                    break;
                case 'nome': $where .= $operator . 'COALESCE(p.nm_pessoa_fisica, s.ds_setor) LIKE :nome ';	
                    break;
                case 'cd_setor': $where .= $operator . 's.cd_setor = :cd_setor ';	
                    break;
				case 'dt_entrada': $where .= $operator . 'l.dt_entrada BETWEEN :dt_inicio AND :dt_fim ';	
                    break;
                case 'tm_dentro': $where .= $operator . "l.tm_dentro {$params['operator']} TIME(:tm_dentro) ";
                    break;
                default: $y++;                
			}
        }	

		return $where;
    }

    public function prepareWhereAccess($params, $stmt)
	{
		foreach($params as $key => $value) {
			switch ($key) {
                case "cd_horario": $stmt->bindValue(":cd_horario", $value);
                    break;
                case "nr_cracha_cartao": $stmt->bindValue(":nr_cracha_cartao", $value);
                    break;
                case "nome": $stmt->bindValue(":nome", ("%$value%"));
                    break;
                case "cd_setor": $stmt->bindValue(":cd_setor", $value);
                    break;
                case "dt_entrada": $stmt->bindValue(":dt_inicio", $params['dt_inicio']);
                                   $stmt->bindValue(":dt_fim", $params['dt_fim']);
                    break;
                case "tm_dentro": $stmt->bindValue(":tm_dentro", $value);
            }
		}

		return $stmt;		
	}
    
    public function accessUpdate($nr_matricula, $type) 
    {   
        $where = [[            
            "field" => "CAST(HE02_ST_MATRICULA AS UNSIGNED)",
            "operator" => "=",
            "value" => $nr_matricula
        ]];
        $nr_horario = 0;
        $fl_acesso = 2;
        
        if ($type == "Almoço") {
            $nr_horario = 3;
        } elseif ($type == "Café da Manha") { 
            $nr_horario = 1;
        } elseif ($type == "Café da Manha - Limp") { 
            $nr_horario = 2;
        } elseif ($type == "Café da Tarde") { 
            $nr_horario = 4;
        } elseif ($type == "Total") {
            $nr_horario = 0;
            $fl_acesso = 126;
        } elseif ($type == "Negar") {
            $nr_horario = 0;
            $fl_acesso = 127;
        } else {
            return false;
        }

        return parent::update([
            "HE02_NR_HORARIO" => $nr_horario,
            "HE02_FL_ACESSO"  => $fl_acesso
        ], $where);
    } 

    private function personalAndCardAccessUpdate($person, $type)
    {
        $this->accessUpdate($person['nr_cracha'], $type);

        if ($person['nr_cartao'] != null && $person['ie_passe_livre_catraca'] != 'S') {
            $this->accessUpdate($person['nr_cartao'], $type);
        }
    }

    private function accessUpdateForPeople($people)
    {
        $turnstile = new Turnstile();
        $menuDAO = new MenuDAO();
        $hour = $turnstile->checkHour();

        if ($hour == 'Almoço') {
           $listRequest = $menuDAO->listRequest((new \DateTime()));              
        }

        foreach($people as $person) {
            if ($hour == 'Café da Manha') {
                if ($person['cd_setor'] == 19) {
                    $this->personalAndCardAccessUpdate($person, 'Café da Manha - Limp');
                } else {
                    $this->personalAndCardAccessUpdate($person, 'Café da Manha');
                }
            } elseif ($hour == 'Almoço') {
                $request = false;

                foreach($listRequest as $request_person) {
                    if ($request_person['nr_cracha'] == $person['nr_cracha']) {
                        $request = true;
                    }
                }       
                
                if ($request) {
                    $this->personalAndCardAccessUpdate($person, 'Almoço');
                } else {
                    $this->personalAndCardAccessUpdate($person, 'Café da Tarde');
                }
            } else {
                $this->personalAndCardAccessUpdate($person, 'Café da Tarde');
            }    
        } 
    }
    
    public function massAccessUpdate($value)
    {       
        $query = "SELECT p.nr_cracha, p.cd_setor, c.nr_cartao, c.ie_passe_livre_catraca
            FROM pessoa_fisica p
                LEFT JOIN tb_cartao c ON p.nr_cracha = c.nr_cracha AND c.ie_situacao = 'A'  
                LEFT JOIN tb_permissao_pf pp ON p.nr_cracha = pp.nr_cracha AND pp.cd_permissao = 10 
                LEFT JOIN tb_permissao_setor ps ON p.cd_setor = ps.cd_setor AND ps.cd_permissao = 10
            WHERE p.ie_situacao = 'A'
                AND pp.vl_pf IS NULL
                AND ps.vl_setor IS NULL";

        $people = $this->select($query);

        if ($value == 'N') {
            $this->accessUpdateForPeople($people);         
        } elseif ($value == 'S') {
            foreach($people as $person) {
                $this->personalAndCardAccessUpdate($person, 'Total'); 
            }
        }
    }

    public function sectorAccessUpdate($value, $cd_setor)
    {       
        $query = "SELECT p.nr_cracha, p.cd_setor, c.nr_cartao, c.ie_passe_livre_catraca
            FROM pessoa_fisica p 
                LEFT JOIN tb_cartao c ON p.nr_cracha = c.nr_cracha AND c.ie_situacao = 'A'
                LEFT JOIN tb_permissao_pf pp ON p.nr_cracha = pp.nr_cracha AND pp.cd_permissao = 10 
            WHERE p.ie_situacao = 'A'
                AND pp.vl_pf IS NULL
                AND p.cd_setor = ?";

        $people = $this->select($query, $cd_setor);

        if ($value == 'N') {
            $this->accessUpdateForPeople($people);         
        } elseif ($value == 'S') {
            foreach($people as $person) {
                $this->personalAndCardAccessUpdate($person, 'Total'); 
            }
        }
    }

    public function personalAccessUpdate($value, $nr_cracha)
    {       
        $query = "SELECT p.nr_cracha, p.cd_setor, c.nr_cartao, c.ie_passe_livre_catraca
            FROM pessoa_fisica p  
                LEFT JOIN tb_cartao c ON p.nr_cracha = c.nr_cracha AND c.ie_situacao = 'A'
            WHERE p.ie_situacao = 'A'
                AND p.nr_cracha = ?";

        $people = $this->select($query, $nr_cracha);

        if ($value == 'N') {
            $this->accessUpdateForPeople($people);         
        } elseif ($value == 'S') {
            foreach($people as $person) {
                $this->personalAndCardAccessUpdate($person, 'Total'); 
            }
        }
    }
}