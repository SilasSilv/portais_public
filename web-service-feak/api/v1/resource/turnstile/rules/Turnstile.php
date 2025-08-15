<?php
namespace api\v1\resource\turnstile\rules;

use \api\v1\vendor\error\Error;
use \api\v1\vendor\utils\DateUtils;

class Turnstile
{
    public function lunchTime($dt_refeicao)
    {
        $now = new \DateTime();
		$now->setTime(0, 0);
		$dt_third_folds = new \DateTime($dt_refeicao);
		
		if ($now == $dt_third_folds) {
			$now = new \DateTime();
			$dt_begin = new \DateTime();
            $dt_end = new \DateTime();
            
			$dt_begin->setTime(11,00);
			$dt_end->setTime(14,00);

			if (($now >= $dt_begin) && ($now < $dt_end)) {
				return true;
			}
		} 

		return false;
	}
	
	public function checkHour() 
	{
		$now = new \DateTime();

		if ($now->format('H') < 11) {
			return 'Café da Manha';
		} elseif ($now->format('H') >= 11 && $now->format('H') < 14) {
			return 'Almoço';
		} else {
			return 'Café da Tarde';
		}
	}

	public function treatParams($params)
	{
		$params['dt_entrada'] = true;
		$params['dt_inicio'] = DateUtils::convert($params["dt_inicio"], "BR-DATE", ['format' => 'Y-m-d']) . ' 00:00:00';
		$params['dt_fim'] = DateUtils::convert($params["dt_fim"], "BR-DATE", ['format' => 'Y-m-d']) . ' 23:59:59';
	
		if (array_key_exists('op_tempo', $params)) {
			switch ($params['op_tempo']) {
				case 'maior': $params['operator'] = '>';
					break;
				case 'menor': $params['operator'] = '<';
					break;
				case 'igual': $params['operator'] = '=';
					break;
				default: unset($params['tm_dentro']);
			}
		}

		if (array_key_exists('pagination', $params)) {
			if (! preg_match('/^\d+\,\d+$/',$params['pagination'])) {
				Error::generateErrorCustom('Invalid parameter: pagination', 422);
			}
		} else {
			$params['pagination'] = '0,40';
		}

		return $params;
	}
}