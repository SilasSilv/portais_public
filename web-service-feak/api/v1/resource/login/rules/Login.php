<?php

namespace api\v1\resource\login\rules;

use \api\v1\vendor\error\Error;
use \api\v1\resource\meal\rules\quality\QualityDAO;

class Login {
	/*
		Este método tem o objetivo de recuperar qualquer valor importante pra um determinado sistema no momento do login
	*/
	public static function CheckDataSystem($cd_sistema, $params=[]) {
		if ($cd_sistema == 4) {
			$qualityDAO = new QualityDAO();

			if (array_key_exists('nr_cracha', $params)) {
				$registerEvaluation = $qualityDAO->checkRegisterEvaluation($params["nr_cracha"]);

				if ($registerEvaluation["avaliacao_refeicao"] >= 1) {
					$registerEvaluation["avaliacao_refeicao"] = 'N';
				} else {
					$registerEvaluation["avaliacao_refeicao"] = 'S';
				}

				return $registerEvaluation;
			} else {
				Error::generateErrorApi(Error::INTERNAL_ERROR);
			}			
		}
	}
}
