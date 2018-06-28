<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 高频彩投注串解析类
 */

class GpBetCodes
{
	private $me = array(
		'SYXW'=> '21406',
		'JXSYXW' => '21407',
		'HBSYXW' => '21408',
		'KS' => '53',
		'KLPK' => '54',
		'CQSSC' => '55',
		'JLKS' => '56',
	    'JXKS' => '57',
	    'GDSYXW' => '21421',
	);

	public function index($lotteryId, $codes)
	{
		
		$codesArr = explode(';', $codes);
		$tpl = array();
		foreach ($codesArr as $code) 
		{
			$ctpl = '';
			$castHtml = $this->castToTpl($lotteryId, $code);
			if($castHtml['dfsType'])
			{
				if(BetCnName::$playTypeDfsCnName[$lotteryId][$castHtml['playTypeCode']])
				{
					$ctpl .= BetCnName::$playTypeDfsCnName[$lotteryId][$castHtml['playTypeCode']] . ' ';
				}
			}
			else
			{
				if(BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']])
				{
					$ctpl .= BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']] . ' ';
				}
			}
			$castTpl = $castHtml['preTpl'];
			if ($castHtml['postTpl'])
			{
                $castTpl .= ': ' . $castHtml['postTpl'];
            }
            $ctpl .= $castTpl;
            array_push($tpl, $ctpl);
		}
		return implode(' ; ', $tpl);
	}

	public function castToTpl($lotteryId, $cast)
	{
		$parsedCast = $this->parseCast($lotteryId, $cast);
		// 单复式判断
		$dfsType = $this->checkDfsType($lotteryId, $parsedCast);
		$parsedAward = $award;
		$preTpl = '';
        $postTpl = '';
        $preDan = '';
        $postDan = '';
		$castPre = array_merge_recursive($parsedCast['preDan'], $parsedCast['preTuo']);
		$castPost = array_merge_recursive($parsedCast['postDan'], $parsedCast['postTuo']);
		$playTypeCode = $parsedCast['playTypeCode'];
		// 十一选五 modeCode 01 单复式投注、05 胆拖投注
		$modeCode = $parsedCast['modeCode'];

		// 胆拖
		if(!empty($parsedCast['preDan']))
		{
			$preDanArry = $this->danFormat($parsedCast['preDan']);
			foreach($preDanArry as $key => $number)
			{
				$preDan .=  $number . ' ';
			}
			$castPre = $parsedCast['preTuo'];
		}

		if(!empty($parsedCast['postDan']))
		{
			$postDanArry = $this->danFormat($parsedCast['postDan']);
			foreach($postDanArry as $key => $number)
			{
				$postDan .= $number . ' ';
			}
			$castPost = $parsedCast['postTuo'];
		}

		foreach($castPre as $key => $number)
		{
			if($lotteryId == $this->me['SYXW'] || $lotteryId == $this->me['JXSYXW'] || $lotteryId == $this->me['HBSYXW'] || $lotteryId == $this->me['GDSYXW'])
			{
				if(in_array($playTypeCode, array('09', '10', '13')))
				{
					// 前二直选 前三直选
					// 每位各选1或多个号码，选号与开奖号码前两位号码相同（且顺序一致）
					if($preTpl != '') 
					{
						$preTpl .= '| ';
					}
					$numTmp = explode(',', $number);
					foreach($numTmp as $n1)
					{
						$preTpl .= $n1 . ' ';
					}
				}
				else 
				{
					$preTpl .= $number . ' ';
				}
			}
			elseif($lotteryId == $this->me['CQSSC'])
			{
				if(in_array($playTypeCode, array('1')))
				{
					if($preTpl != '') 
					{
						$preTpl .= '| ';
					}
					$preTpl .= $number . ' ';
				}
				elseif(in_array($playTypeCode, array('10')))
				{
					$num = strlen($number) > 1 ? str_split($number) : array($number);
					foreach ($num as $val)
    				{
    					$preTpl .= $val . ' ';
    				}
				}
				elseif(in_array($playTypeCode, array('20', '21', '30', '31', '40', '41', '43')))
				{
					if($preTpl != '') 
					{
						$preTpl .= '| ';
					}
					$num = strlen($number) > 1 ? str_split($number) : array($number);
					foreach ($num as $val)
    				{
    					$preTpl .= $val . ' ';
    				}
				}
				else
				{
					$preTpl .= $number . ' ';
				}
			}
			else if($lotteryId == $this->me['KLPK'])
			{
				if($playTypeCode == '7')
				{
					// 同花处理
					$th = array('S' => '黑桃', 'H' => '红桃', 'C' => '梅花', 'D' => '方块', 'B' => '包选');
					$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
					$preTpl .= $th[$code[$number]] . ' ';
				}
				elseif($playTypeCode == '8')
				{
					$th = array('S' => '黑桃顺子', 'H' => '红桃顺子', 'C' => '梅花顺子', 'D' => '方块顺子', 'B' => '同花顺包选');
					$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
					$preTpl .= $th[$code[$number]] . ' ';
				}
				elseif($playTypeCode == '9')
				{
					//顺子处理
					$sz = array('A' =>'A23', '2' => '234', '3' => '345', '4' => '456', '5' => '567', '6' => '678', '7' => '789', '8' => '8910', '9' => '910J', '10' => '10JQ', 'J' => 'JQK', 'Q' => 'AQK', '00' => '包选');
					$number = $sz[$number] == 'AQK' ? 'QKA' : $sz[$number];
					$preTpl .= $number . ' ';
				}
				elseif($playTypeCode == '10')
				{
					$number = $number == '00' ? '包选' : str_repeat($number, 3);
					$preTpl .= $number . ' ';
				}
				elseif($playTypeCode == '11')
				{
					$number = $number == '00' ? '包选' : str_repeat($number, 2);
					$preTpl .= $number . ' ';
				}
				elseif($playTypeCode == '12')
				{
					//包选处理
					$bx = array(
						'7' => array('name' => '同花包选', 'value' => 'isTh'),
						'8' => array('name' => '同花顺包选', 'value' => 'isThs'),
						'9' => array('name' => '顺子包选', 'value' => 'isSz'),
						'10' => array('name' => '豹子包选', 'value' => 'isBz'),
						'J' => array('name' => '对子包选', 'value' => 'isDz')
					);
					$preTpl .= $bx[$number]['name'] . ' ';
				}
				else
				{
					$preTpl .= $number . ' ';
				}
			}
			else if (strpos($number, ',') !== FALSE )
			{
				if( $preTpl != '' )
				{
					$preTpl .= '| ';
				}
				$numTmp = explode(',', $number);
				foreach($numTmp as $k1 => $n1)
				{
					$preTpl .= $n1 . ' ';
				}
			}
			else 
			{
				if(is_numeric($number) && $number)
				{
					$preTpl .= $number . ' ';
				}	
			}
		}

		foreach($castPost as $key => $number)
		{
			$postTpl .= $number . ' ';
		}

		$preTpl = $preDan . $preTpl;
		$postTpl = $postDan .  $postTpl;

		return array(
			'preTpl' => $preTpl,
			'postTpl' => $postTpl,
			'playTypeCode' => $playTypeCode,
			'modeCode' => $modeCode,
			'dfsType' => $dfsType
		);
	}

	// 单复式判断
	private function checkDfsType($lotteryId, $parsedCast)
	{
		$dfsType = 0;
		if(isset(BetCnName::$getMinLength[$lotteryId][$parsedCast['playTypeCode']]))
		{
			$minLength = BetCnName::$getMinLength[$lotteryId][$parsedCast['playTypeCode']];
			switch ($lotteryId) 
			{
				case '21406':
				case '21407':
				case '21408':
				case '21421':
					// 十一选五胆拖判断
					if(intval($parsedCast['modeCode'] == '5'))
					{
						$dfsType = 1;
					}
				default:
					# code...
					break;
			}
		}
		return $dfsType;
	}

	private function parseCast($lotteryId, $code)
	{
		$parts = explode(':', $code);
		$numbers = $parts[0];
		$playType = $parts[1];
		$hasDan = strpos($code, '$') > 0;
		$hasPost = strpos($code, '|') > 0;
		$preDan = array();
		$preTuo = array();
		$postDan = array();
		$postTuo = array();

		if($lotteryId == $this->me['SYXW'] || $lotteryId == $this->me['JXSYXW'] || $lotteryId == $this->me['HBSYXW'] || $lotteryId == $this->me['GDSYXW'])
		{
			if(in_array($playType, array('09', '10', '13')))
			{
				$preTuo = explode('|', $numbers);
			}
			else
			{
				// 胆拖
				if($hasDan)
				{
					$pres = explode('$', $numbers);
					$preDan = explode(',', $pres[0]);
					$preTuo = explode(',', $pres[1]);
				}
				else
				{
					$preTuo = explode(',', $numbers);
				}	
			}
		}
		elseif ($lotteryId == $this->me['CQSSC'] && $playType == 1)
		{
			$arr = array(1 => '大', 2 => '小', 4 => '单', 5 => '双');
			$numbers = explode(',', $numbers);
			foreach ($numbers as &$num)
			{
				$num = $arr[$num];
			}
			$preTuo = $numbers;
		}
		elseif ($lotteryId == $this->me['KLPK'])
		{
			// 胆拖
			if($hasDan)
			{
				$pres = explode('$', $numbers);
				$preDan = explode(',', $pres[0]);
				$preTuo = explode(',', $pres[1]);
			}
			else
			{
				$preTuo = explode(',', $numbers);
			}
			$preDan = array_map(array($this, 'getKlpkAlias'), $preDan);
			$preTuo = array_map(array($this, 'getKlpkAlias'), $preTuo);
		}
		else
		{
			$preTuo = explode(',', $numbers);
		}

		return array(
			'playTypeCode'	=>	$parts[1],
			'modeCode' 		=> 	$parts[2],
			'preDan' 		=> 	$preDan,
			'preTuo' 		=> 	$preTuo,
			'postDan' 		=> 	$postDan,
			'postTuo' 		=> 	$postTuo
		);
	}

	// 胆拖格式 数字前后增加括号()
	private function danFormat($danArry)
	{
		// 开头插入左括号
		array_unshift($danArry, '(');
		// 结尾插入右括号
		array_push($danArry, ')');
		return $danArry;
	}

	//快乐扑克别名返回
	private function getKlpkAlias($key)
	{
		$klpkAlias = array(
			'01' => 'A', 
			'02' => '2',
			'03' => '3',
			'04' => '4',
			'05' => '5',
			'06' => '6',
			'07' => '7',
			'08' => '8',
			'09' => '9',
			'10' => '10',
			'11' => 'J', 
			'12' => 'Q', 
			'13' => 'K', 
			'S' => 's', 
			'H' => 'h', 
			'C' => 'c', 
			'D' => 'd'
		);	
		return isset($klpkAlias[$key]) ? $klpkAlias[$key] : $key;
	}
}