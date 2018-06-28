<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 数字彩投注方案解析
 * @author Administrator
 *
 */
class Lottery
{
	private $me = array(
		'DLT' => '23529',
		'QLC' => '23528',
		'QXC' => '10022',
		'SYXW'=> '21406',
		'SSQ' => '51',
		'PL3' => '33',
		'PLS' => '33',
		'PL5' => '35',
		'PLW' => '35',
		'FCSD'=> '52',
		'SFC' => '11',
		'RJ' => '19',
		'JXSYXW' => '21407',
		'HBSYXW' => '21408',
		'KS' => '53',
		'KLPK' => '54',
		'CQSSC' => '55',
		'JLKS' => '56',
	    'JXKS' => '57',
	    'GDSYXW' => '21421',
	);
	public function index($cast, $awards)
	{
		$datas = array();
		//$cast = '09,10,15,20,23,27|04:1:1;03,07,10,11,13,15,19,20,21,25,28,29|04,06,10:1:1';
		//$awards = json_decode('{"seExpect":"2015054","awardNumber":"01,02,07,10,22,26:07","seLotid":51,"seEndtime":1431432000000,"seAllowbuy":0,"seFsendtime":1431431100000,"seIsactive":0,"awardTime":1431437400000,"seAllowcp":1,"seId":127156,"seDsendtime":1431430200000,"seAddtime":1426262702000,"seIsqs":1,"seIsover":0,"awardFlag":null,"seIsIssue":0,"modifyflag":0,"status":400}', true);
		$award = $this->parseAward($awards['awardNumber']);
		$datas['atpl'] = $this->renderAward($awards['seLotid'], $award);
		$datas['tpl'] = $this->renderCast($cast, $awards['seLotid'], $award);
		return $datas;
	}
	
	private function renderCast($cast, $lotteryId, $award)
	{
		if($lotteryId == $this->me['KLPK'])
		{
			// 排序
			sort($award['preCode']);
			$award['preCode'] = array_map (array($this, 'getKlpkAlias'), $award['preCode']);
		}
		$ctpl = '';
		$casts = explode(';', $cast);
		foreach ($casts as $val)
		{		
			$castHtml = $this->castToTpl($lotteryId, $val, $award);
			$ctpl .= '<dd>';
			// 单复式判断,十一选五胆拖判断
			if($castHtml['dfsType'])
			{
				if(BetCnName::$playTypeDfsCnName[$lotteryId][$castHtml['playTypeCode']])
				{
					if($lotteryId == $this->me['DLT'] && $castHtml['playTypeCode'] == '135' && $castHtml['modeCode'] == '1')
					{
						$ctpl .= '<span class="bet-detail-tag"><small>胆拖 追加</small></span>';
					}
					else
					{
						$ctpl .= '<span class="bet-detail-tag"><small>' . BetCnName::$playTypeDfsCnName[$lotteryId][$castHtml['playTypeCode']] . '</small></span>';
					}
				}
			}
			else
			{
				if(BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']])
				{
					if($lotteryId == $this->me['DLT'] && $castHtml['playTypeCode'] == '135' && $castHtml['modeCode'] == '1')
					{
						$ctpl .= '<span class="bet-detail-tag"><small>胆拖 追加</small></span>';
					}
					else
					{
						$ctpl .= '<span class="bet-detail-tag"><small>' . BetCnName::$playTypeCnName[$lotteryId][$castHtml['playTypeCode']] . '</small></span>';
					}
				}
			}
			$ctpl .= '<div class="num-group">';
			$castTpl = $castHtml['preTpl'];
            if ($castHtml['postTpl'])
			{
                $castTpl .= $this->renderGrayDetail(':', 2) . $castHtml['postTpl'];
            }
			$ctpl .= $castTpl . '</div></dd>';
		}
		return $ctpl;
	}

	private function castToTpl($lotteryId, $cast, $award)
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
				// $numberTemp = str_replace(array('(',')'), array('', ''), $number);
				if(is_numeric($number) || ($lotteryId == $this->me['KLPK'] && !in_array($number, array('(', ')'))))
				{
					if (in_array($number, $parsedAward['preCode']))
					{
						$preDan .= $this->renderRedDetail($number);
					} 
					else 
					{
						$preDan .= $this->renderGrayDetail($number);
					}
				}
				else
				{
					$preDan .= $this->renderGrayDetail($number, 3);
				}			
			}
			// $preDan .= $this->renderGrayDetail(')', 3);
			$castPre = $parsedCast['preTuo'];
		}
		if(!empty($parsedCast['postDan']))
		{
			// $postDan .= $this->renderGrayDetail('(', 3);
			$postDanArry = $this->danFormat($parsedCast['postDan']);
			foreach($postDanArry as $key => $number)
			{
				// $numberTemp = str_replace(array('(',')'), array('', ''), $number);
				if(is_numeric($number))
				{
					if (in_array($number, $parsedAward['postCode']))
					{
						$postDan .= $this->renderRedDetail($number);
					} 
					else 
					{
						$postDan .= $this->renderGrayDetail($number);
					}
				}
				else
				{
					$postDan .= $this->renderGrayDetail($number, 3);
				}			
			}
			// $postDan .= $this->renderGrayDetail(')', 3);
			$castPost = $parsedCast['postTuo'];
		}

		foreach($castPre as $key => $number)
		{
		    if($lotteryId == $this->me['SYXW'] || $lotteryId == $this->me['JXSYXW'] || $lotteryId == $this->me['HBSYXW'] || $lotteryId == $this->me['GDSYXW'])
			{
				if($playTypeCode == '01' )
				{
					//前一
					if($number == $parsedAward['preCode'][0])
					{
						$preTpl .= $this->renderRedDetail($number);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number);
					}
				}
				else if ( $playTypeCode == '09' )
				{
					//前二直选
					// 每位各选1或多个号码，选号与开奖号码前两位号码相同（且顺序一致）
					if( $preTpl != '' ) 
					{
						$preTpl .= $this->renderGrayDetail('|', 1);
					}
					$numTmp = explode(',', $number);
					foreach($numTmp as $n1)
					{
						if ( ( $n1 == $parsedAward['preCode'][$key] && $key < 2 )) 
						{
							$preTpl .= $this->renderRedDetail($n1);
						} 
						else 
						{
							$preTpl .= $this->renderGrayDetail($n1);
						}
					}
				}
				else if ( $playTypeCode == '10' )
				{
					//前三直选
					// 每位各选1或多个号码，选号与开奖号码前三位号码相同（且顺序一致）
					if( $preTpl != '' ) 
					{
						$preTpl .= $this->renderGrayDetail('|', 1);
					}
					$numTmp = explode(',', $number);
					foreach($numTmp as $n1)
					{
						if ( ( $n1 == $parsedAward['preCode'][$key] && $key < 3 )) 
						{
							$preTpl .= $this->renderRedDetail($n1);
						} 
						else 
						{
							$preTpl .= $this->renderGrayDetail($n1);
						}
					}
				}
				else if($playTypeCode == '11' ){ // 前二组选
					$aTmp = array_slice($parsedAward['preCode'], 0, 2);
					if(in_array($number, $aTmp))
					{
						$preTpl .= $this->renderRedDetail($number);
					}
					else 
					{
						$preTpl .= $this->renderGrayDetail($number);
					}
				}
				else if($playTypeCode == '12' ){ // 前三组选
					$aTmp = array_slice($parsedAward['preCode'], 0, 3);
					if(in_array($number, $aTmp))
					{
						$preTpl .= $this->renderRedDetail($number);
					}
					else 
					{
						$preTpl .= $this->renderGrayDetail($number);
					}
				}
				else if ( $playTypeCode == '13' )
				{
					//前三直选
					// 每位各选1或多个号码，选号与开奖号码前三位号码相同（且顺序一致）
					if( $preTpl != '' )
					{
						$preTpl .= $this->renderGrayDetail('|', 1);
					}
					$numTmp = explode(',', $number);
					foreach($numTmp as $n1)
					{
						if (in_array($n1, $parsedAward['preCode']))
						{
							$preTpl .= $this->renderRedDetail($n1);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($n1);
						}
					}
				}
				else 
				{
					if(in_array($number, $parsedAward['preCode']))
					{
						$preTpl .= $this->renderRedDetail($number);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number);
					}
				}
			}
			else if ( in_array($lotteryId, array($this->me['PLS'], $this->me['FCSD'], $this->me['PLW'], $this->me['QXC']))) {
				switch ($parsedCast['playTypeCode']) {
					case 2:
						$awardcode = $parsedAward['preCode'];
						sort($awardcode);
						if (count(array_unique($parsedCast['preTuo'])) < count($parsedCast['preTuo'])) {
							if (count(array_unique($awardcode)) == 2 && $number == $awardcode[$key]) {
								$preTpl .= $this->renderRedDetail($number);
							}else {
								$preTpl .= $this->renderGrayDetail($number);
							}
						} else {
							if (count(array_unique($awardcode)) == 2 && in_array($number, $awardcode)) {
								$preTpl .= $this->renderRedDetail($number);
							} else {
								$preTpl .= $this->renderGrayDetail($number);
							}
						}
						break;
					case 3:
						if(count(array_unique($parsedAward['preCode'])) == 3 && in_array($number, $parsedAward['preCode'])) {
							$preTpl .= $this->renderRedDetail($number);
						} else {
							$preTpl .= $this->renderGrayDetail($number);
						}
						break;
					case 1:
					default:
						if($preTpl != '' ) $preTpl .= $this->renderGrayDetail('|', 1);
						$bTmp = str_split($number);
						foreach($bTmp as $k1 => $n1) {
							if($n1 == $parsedAward['preCode'][$key]) {
								$preTpl .= $this->renderRedDetail($n1);
							} else {
								$preTpl .= $this->renderGrayDetail($n1);
							}
						}
						break;
				}
			} elseif ($lotteryId == $this->me['CQSSC']) {
				switch ($playTypeCode) {
					case '1':
						if($preTpl != '') $preTpl .= $this->renderGrayDetail('|', 1);
						$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
						if(!is_null($awardcode[$key]) && (($number == '大' && $awardcode[$key] > 4) || ($number == '小' && $awardcode[$key] <= 4) || ($number == '单' && $awardcode[$key]%2 == 1) || ($number == '双' && $awardcode[$key]%2 === 0))) {
							$preTpl .= $this->renderRedDetail($number);
						} else {
							$preTpl .= $this->renderGrayDetail($number);
						}
						break;
					case '10':
						$num = strlen($number) > 1 ? str_split($number) : array($number);
    					foreach ($num as $val) {
    						if($val == $parsedAward['preCode'][4]) {
    							$preTpl .= $this->renderRedDetail($val);
    						} else {
    							$preTpl .= $this->renderGrayDetail($val);
    						}
    					}
						break;
					case '20':
					case '21':
						$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
						if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
						$num = strlen($number) > 1 ? str_split($number) : array($number);
						foreach ($num as $val) {
							if($val == $awardcode[$key]) {
								$preTpl .= $this->renderRedDetail($val);
							} else {
								$preTpl .= $this->renderGrayDetail($val);
							}
						}
						break;
					case '23':
					case '27':
						$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
						if(in_array($number, $awardcode)) {
							$preTpl .= $this->renderRedDetail($number);
						} else {
							$preTpl .= $this->renderGrayDetail($number);
						}
						break;
					case '30':
					case '31':
						$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
						if($preTpl != '') $preTpl .= $this->renderGrayDetail('|');
						$num = strlen($number) > 1 ? str_split($number) : array($number);
						foreach ($num as $val) {
							if($val == $awardcode[$key]) {
								$preTpl .= $this->renderRedDetail($val);
							} else {
								$preTpl .= $this->renderGrayDetail($val);
							}
						}
						break;
					case '33':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					sort($awardcode);
    					if (count(array_unique($awardcode)) == 2 && $number == $awardcode[$key]) {
    						$preTpl .= $this->renderRedDetail($number);
    					}else {
    						$preTpl .= $this->renderGrayDetail($number);
    					}
    					break;
    				case '37':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if (count(array_unique($awardcode)) == 2 && in_array($number, $awardcode)) {
    						$preTpl .= $this->renderRedDetail($number);
    					} else {
    						$preTpl .= $this->renderGrayDetail($number);
    					}
    					break;
    				case '34':
    				case '38':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if(count(array_unique($awardcode)) == 3 && in_array($number, $awardcode)) {
    						$preTpl .= $this->renderRedDetail($number);
    					} else {
    						$preTpl .= $this->renderGrayDetail($number);
    					}
    					break;
					case '40':
					case '41':
					case '43':
						if($preTpl != '') $preTpl .= $this->renderGrayDetail('|', 1);
						$num = strlen($number) > 1 ? str_split($number) : array($number);
						foreach ($num as $val) {
							if($val == $parsedAward['preCode'][$key]) {
								$preTpl .= $this->renderRedDetail($val);
							} else {
								$preTpl .= $this->renderGrayDetail($val);
							}
						}
						break;
				}
			}
			else if ($lotteryId == $this->me['SFC'] || $lotteryId == $this->me['RJ'])
			{
				$numTmp = explode(',', $number);
				foreach($numTmp as $k0 => $part)
				{
					//复试
					if(strlen($number) > 1)
					{
						if( $preTpl != '' )
						{
							$preTpl .= $this->renderGrayDetail('|', 1);
						}
						$bTmp = str_split($part);
						foreach($bTmp as $k1 => $n1)
						{
							if (( $n1 == $parsedAward['preCode'][$k0] ))
							{
								$preTpl .= $this->renderRedDetail($n1);
							} 
							else 
							{
								$preTpl .= $this->renderGrayDetail($n1);
							}
						}
					}
					else 
					{
						//单式
						if ($part == $parsedAward['preCode'][$k0]) 
						{
							$preTpl .= $this->renderRedDetail($n1);
						}
						else 
						{
							$preTpl .= $this->renderGrayDetail($n1);
						}
					}
				}
			}
			else if($lotteryId == $this->me['KLPK'])
			{
				if($playTypeCode == '7')
				{
					//同花处理
					$th = array('S' => '黑桃', 'H' => '红桃', 'C' => '梅花', 'D' => '方块', 'B' => '包选');
					$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
					$countData = array_count_values($parsedAward['postCode']); //开奖花色统计处理
					$countData = array_flip($countData);
					$isTh = isset($countData[3]) ? true : false;
					$thValue = isset($countData[3]) ? $countData[3] : '';
					if($isTh && ($code[$number] == $thValue || $number == '00'))
					{
						$preTpl .= $this->renderRedDetail($th[$code[$number]]);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($th[$code[$number]]);
					}
				}
				else if($playTypeCode == '8')
				{
					//同花顺处理
					$th = array('S' => '黑桃顺子', 'H' => '红桃顺子', 'C' => '梅花顺子', 'D' => '方块顺子', 'B' => '同花顺包选');
					$code = array('A' => 'S', '2' => 'H', '3' => 'C', '4' => 'D', '00' => 'B');
					$countData = array_count_values($parsedAward['postCode']); //开奖花色统计处理
					$countData = array_flip($countData);
					$isTh = isset($countData[3]) ? true : false;
					$thValue = isset($countData[3]) ? $countData[3] : '';
					$sz = array('A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'AQK');
					$awardStr = implode('', $parsedAward['preCode']);
					$isSz = in_array($awardStr, $sz) ? true : false;
					if($isSz && $isTh && ($code[$number] == $thValue || $number == '00'))
					{
						$preTpl .= $this->renderRedDetail($th[$code[$number]]);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($th[$code[$number]]);
					}
				}
				else if($playTypeCode == '9')
				{
					//顺子处理
					$sz = array('A' =>'A23', '2' => '234', '3' => '345', '4' => '456', '5' => '567', '6' => '678', '7' => '789', '8' => '8910', '9' => '910J', '10' => '10JQ', 'J' => 'JQK', 'Q' => 'AQK', '00' => '包选');
					$awardStr = implode('', $parsedAward['preCode']);
					$isSz = in_array($awardStr, array_values($sz)) ? true : false;
					$number1 = $sz[$number] == 'AQK' ? 'QKA' : $sz[$number];
					if($isSz && ($sz[$number] == $awardStr || $number == '00'))
					{
						$preTpl .= $this->renderRedDetail($number1);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number1);
					}
				}
				else if($playTypeCode == '10')
				{
					//豹子处理
					$countData = array_count_values($parsedAward['preCode']); //开奖号码统计处理
					$countData = array_flip($countData);
					$isBz = isset($countData[3]) ? true : false;
					$bz = isset($countData[3]) ? $countData[3] : '';
					$number1 = $number == '00' ? '包选' : str_repeat($number, 3);
					if($isBz && ($number == $bz || $number == '00'))
					{
						$preTpl .= $this->renderRedDetail($number1);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number1);
					}
				}
				else if($playTypeCode == '11')
				{
					//对子处理
					$countData = array_count_values($parsedAward['preCode']); //开奖号码统计处理
					$countData = array_flip($countData);
					$isDz = isset($countData[2]) ? true : false;
					$dz = isset($countData[2]) ? $countData[2] : '';
					$number1 = $number == '00' ? '包选' : str_repeat($number, 2);
					if($isDz && ($number == $dz || $number == '00'))
					{
						$preTpl .= $this->renderRedDetail($number1);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number1);
					}
				}
				else if($playTypeCode == '12')
				{
					//包选处理
					$bx = array(
						'7' => array('name' => '同花包选', 'value' => 'isTh'),
						'8' => array('name' => '同花顺包选', 'value' => 'isThs'),
						'9' => array('name' => '顺子包选', 'value' => 'isSz'),
						'10' => array('name' => '豹子包选', 'value' => 'isBz'),
						'J' => array('name' => '对子包选', 'value' => 'isDz')
					);
					$countData = array_count_values($parsedAward['postCode']); //开奖花色统计处理
					$countData = array_flip($countData);
					$isBx['isTh'] = isset($countData[3]) ? true : false;
					$sz = array('A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'AQK');
					$awardStr = implode('', $parsedAward['preCode']);
					$isSz = in_array($awardStr, $sz) ? true : false;
					$isBx['isThs'] = ($isBx['isTh'] && $isSz) ? true : false;
					$isBx['isSz'] = $isSz;
					$bzCount = array_count_values($parsedAward['preCode']); //开奖号码统计处理
					$bzCount = array_flip($bzCount);
					$isBx['isBz'] = isset($bzCount[3]) ? true : false;
					$isBx['isDz'] = isset($bzCount[2]) ? true : false;
					if($isBx[$bx[$number]['value']] == true)
					{
						$preTpl .= $this->renderRedDetail($bx[$number]['name']);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($bx[$number]['name']);
					}
				}
				else
				{
					if(in_array($number, $parsedAward['preCode']))
					{
						$preTpl .= $this->renderRedDetail($number);
					}
					else
					{
						$preTpl .= $this->renderGrayDetail($number);
					}
				}
			}
			else if (strpos($number, ',') !== FALSE )
			{
				if( $preTpl != '' )
				{
					$preTpl .= $this->renderGrayDetail('|', 1);
				}
				$numTmp = explode(',', $number);
				foreach($numTmp as $k1 => $n1)
				{
					if ( $parsedAward['hasAward'] == true ) 
					{
						if(in_array($n1, $parsedAward['preCode']))
						{
							$preTpl .= $this->renderRedDetail($n1);
						} 
						else if(in_array($n1, $parsedAward['postCode']))
						{
							$preTpl .= $this->renderRedDetail($n1);
						}
						else
						{
							$preTpl .= $this->renderGrayDetail($n1);
						}
					}
					else 
					{
						$preTpl .= $this->renderRedDetail($n1);
					}
				}
			}
			else 
			{
				if (in_array($number, $parsedAward['preCode'])) 
				{
					$preTpl .= $this->renderRedDetail($number);
				} 
				elseif ($lotteryId == $this->me['QLC'] && in_array($number, $parsedAward['postCode']))
				{
					$preTpl .= $this->renderBlueDetail($number);
				}
				else 
				{
					$preTpl .= $this->renderGrayDetail($number);
				}
			}
		}
		foreach($castPost as $key => $number)
		{
			if (in_array($number, $parsedAward['postCode']))
			{
				$postTpl .= $this->renderBlueDetail($number);
			} 
			else 
			{
				$postTpl .= $this->renderGrayDetail($number);
			}
		}

		$preTpl = $preDan . $preTpl;
		$postTpl = $postDan .  $postTpl;

		// $preTpl = '[' . $parsedCast['playType'] . ']' . $preTpl;
		return array(
			'preTpl' => $preTpl,
			'postTpl' => $postTpl,
			'playTypeCode' => $playTypeCode,
			'modeCode' => $modeCode,
			'dfsType' => $dfsType
		);
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
		if($lotteryId == $this->me['DLT'] || $lotteryId == $this->me['SSQ'])
		{
			$preBalls = explode('|', $numbers);
			$pre = $preBalls[0];
			$post = $preBalls[1];
			if($hasDan)
			{
				$pres = explode('$', $pre);
				$preDan = explode(',', $pres[0]);
				$preTuo = explode(',', $pres[1]);
				$postSplit = explode('$', $post);
				if(count($postSplit) == 2)
				{
					$postDan = explode(',', $postSplit[0]);
					$postTuo = explode(',', $postSplit[1]);
				}
				else
				{
					$postDan = array();
					$postTuo = explode(',', $postSplit[0]);
				}
			}
			else
			{
				$preTuo = explode(',', $pre);
				$postTuo = explode(',', $post);
			}
		}
		elseif (in_array($lotteryId, array($this->me['PLS'], $this->me['FCSD'])))
		{
			$preTuo = explode(',', $numbers);
			if ($playType > 1) sort($preTuo);
		}
		/*else if($lotteryId == $this->me['PLS'] || $lotteryId == $this->me['PLW'] || $lotteryId == $this->me['FCSD'] || $lotteryId == $this->me['QXC'] || $lotteryId == $this->me['QLC'])
		{
			$preTuo = explode(',', $numbers);
		}
		else if($lotteryId == $this->me['SFC'] || $lotteryId == $this->me['RJ'])
		{
			$preTuo = explode(',', $numbers);
		}*/
		else if($lotteryId == $this->me['SYXW'] || $lotteryId == $this->me['JXSYXW'] || $lotteryId == $this->me['HBSYXW'] || $lotteryId == $this->me['GDSYXW'])
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
		elseif ($lotteryId == $this->me['CQSSC'])
		{
			switch ($playType) {
				case 1:
					$arr = array(1 => '大', 2 => '小', 4 => '单', 5 => '双');
					$numbers = explode(',', $numbers);
					foreach ($numbers as &$num)
					{
						$num = $arr[$num];
					}
					$preTuo = $numbers;
					break;
				case 33:
					$preTuo = explode(',', $numbers);
					sort($preTuo);
					break;
				default:
					$preTuo = explode(',', $numbers);
					break;
			}
		}
		else if($lotteryId == $this->me['KLPK'])
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
			$preDan = array_map (array($this, 'getKlpkAlias'), $preDan);
			$preTuo = array_map (array($this, 'getKlpkAlias'), $preTuo);
		}
		else
		{
			$preTuo = explode(',', $numbers);
		}

		//$playType = $this->getPlayTypeName($lotteryId, $playType);

		return array(
				'playTypeCode' => $parts[1],
				'modeCode' => $parts[2],
				//'playType' => $playType,
				'preDan' => $preDan,
				'preTuo' => $preTuo,
				'postDan' => $postDan,
				'postTuo' => $postTuo
			);
	}
	
	private function renderAward($lotteryId, $award)
	{
		$atpl = '';
		if($award['hasAward'])
		{
			if($lotteryId == $this->me['SFC'] || $lotteryId == $this->me['RJ'])
			{
				foreach ($award['preCode'] as $number)
				{
					$atpl .= "{$number} ";
				}
			}
			elseif ($lotteryId == $this->me['KLPK'])
			{
				
				foreach ($award['preCode'] as $key => $number)
				{
					$atpl .= '<span class="' . $this->getKlpkAlias($award['postCode'][$key]) . '">' . $this->getKlpkAlias($number) . '</span>';
				}
				return $atpl;
			}
			else
			{
				foreach ($award['preCode'] as $number)
				{
					$atpl .= "<span>{$number}</span>";
				}
			}
			foreach ($award['postCode'] as $number)
			{
				$atpl .= '<span class="blue-ball">'.$number.'</span>';
			}
		}
		else
		{
			switch ($lotteryId)
			{
				case $this->me['SSQ']:
					$atpl = '<span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span class="blue-ball">?</span>';
					break;
				case $this->me['DLT']:
					$atpl = '<span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span class="blue-ball">?</span><span class="blue-ball">?</span>';
					break;
				case $this->me['FCSD']:
				case $this->me['PLS']:
					$atpl = '<span>?</span><span>?</span><span>?</span>';
					break;
				case $this->me['PLW']:
				case $this->me['SYXW']:
				case $this->me['JXSYXW']:
				case $this->me['HBSYXW']:
				case $this->me['GDSYXW']:
					$atpl = '<span>?</span><span>?</span><span>?</span><span>?</span><span>?</span>';
					break;
				case $this->me['QXC']:
					$atpl = '<span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span>?</span>';
					break;
				case $this->me['QLC']:
					$atpl = '<span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span>?</span><span class="blue-ball">?</span>';
					break;
			}
		}
		
		return $atpl;
	}
	
	private function parseAward($code)
	{
		$preCode = array();
		$postCode = array();
		$hasAward = false;
		if ($code)
		{
			$code .= '';
			$hasAward = true;
			$code = preg_replace('/\s+/is', ',', $code);
			$hasPost = strpos($code, ':') > 0 ? true : false;
			$parts = explode(':', $code);
			$preCode = explode(',', $parts[0]);
			if ($hasPost) {
				$postCode = explode(',', $parts[1]);
			}
		}
		
		$award = array(
			'hasAward' => $hasAward,
			'preCode' => $preCode,
			'postCode' => $postCode
		);
		
		return $award;
	}
	
	/*private function getPlayTypeName($lotteryId, $playType)
	{
		$cnName = '';
		$playCnNames = array();
		if($lotteryId == $this->me['SYXW'])
		{
			$playCnNames = array(
				'01' => '前1',
				'02' => '任选二',
				'03' => '任选三',
				'04' => '任选四',
				'05' => '任选五',
				'06' => '任选六',
				'07' => '任选七',
				'08' => '任选八',
				'09' => '前二直选',
				'10' => '前三直选',
				'11' => '前二组选',
				'12' => '前三组选'
			);
			$cnName = $playCnNames[$playType];
		}
		else if($lotteryId == $this->me['PLS'] || $lotteryId == $this->me['FCSD'])
		{
			$playCnNames = array(
				1 => '直选',
				2 => '组三',
				3 => '组六'
            );
            $cnName = $playCnNames[$playType];
		}
		else if($lotteryId == $this->me['DLT'])
		{
			$playCnNames = array(
				0 => '普通',
				1 => '普通',
				2 => '普通 追加'
			);
			$cnName = $playCnNames[$playType];
		}
		else
		{
			$cnName = '普通';
		}
		return $cnName;
	}*/

	private function renderRedDetail($num)
	{
		if((is_numeric($num) && mb_strlen($num) < 3) || in_array($num, array('A', 'J', 'Q', 'K')))
		{
			return '<span class="bingo">' . $num . '</span>';
		}
		else
		{
			$tpl = '<span class="num-txt">';
			if(strpos($num, '(') !== FALSE)
			{
				$tpl .= '(';
			}
			$number = str_replace(array('(',')'), array('', ''), $num);
			$tpl .= '<i class="bingo">' . $number . '</i>';
			if(strpos($num, ')') !== FALSE)
			{
				$tpl .= ')';
			}
			$tpl .= '</span>';
			return $tpl;
		}	
	}

	private function renderGrayDetail($num ,$class = '')
	{
		if(!empty($class))
		{
			if($class == 1)
			{
				return '<span class="symbol-line">' .$num. '</span>';
			}
			elseif($class == 3) 
			{
				return '<span class="symbol-bracket">' .$num. '</span>';
			}
			else
			{
				return '<span class="symbol-colon">' .$num. '</span>';
			}
		}
		else
		{
			if((is_numeric($num) && mb_strlen($num) < 3))
			{
				return '<span>' .$num. '</span>';
			}
			else
			{
				return '<span class="num-txt">' .$num. '</span>';
			}
		}		
	}

	private function renderBlueDetail($num)
	{
		// class="blue-ball"
		return '<span class="bingo special-color">' . $num . '</span>';
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
				case '51':
				case '23529':
					if(count($parsedCast['preTuo']) > $minLength['preTuo'] || count($parsedCast['postTuo']) > $minLength['postTuo'])
					{
						$dfsType = 1;
					}
					break;
				case '33':
				    if($parsedCast['playTypeCode'] == '3')
				    {
				    	if(count($parsedCast['preTuo']) > $minLength[0])
				    	{
				    		$dfsType = 1;
				    	}
				    }
				    elseif($parsedCast['playTypeCode'] == '2')
				    {
				    	if($parsedCast['modeCode'] == '3')
				    	{
				    		$dfsType = 1;
				    	}
				    }
				    else
				    {
				    	foreach ($parsedCast['preTuo'] as $playType => $items) 
						{
							if(strlen($items) > $minLength[$playType])
							{
								$dfsType = 1;
							}
						}
				    }
					break;
				case '35':
					if($parsedCast['playTypeCode'] == '3')
				    {
				    	if(count($parsedCast['preTuo']) > $minLength[0])
				    	{
				    		$dfsType = 1;
				    	}
				    }
				    else
				    {
				    	foreach ($parsedCast['preTuo'] as $playType => $items) 
						{
							if(strlen($items) > $minLength[$playType])
							{
								$dfsType = 1;
							}
						}
				    }
					break;
				case '52':
					if($parsedCast['playTypeCode'] == '3')
				    {
				    	if(count($parsedCast['preTuo']) > $minLength[0])
				    	{
				    		$dfsType = 1;
				    	}
				    }
				    elseif($parsedCast['playTypeCode'] == '2')
				    {
				    	if($parsedCast['modeCode'] == '3')
						{
							$dfsType = 1;
						}
				    }
				    else
				    {
				    	foreach ($parsedCast['preTuo'] as $playType => $items) 
						{
							if(strlen($items) > $minLength[$playType])
							{
								$dfsType = 1;
							}
						}
				    }
					break;
				case '10022':
					foreach ($parsedCast['preTuo'] as $playType => $items) 
					{
						if(strlen($items) > $minLength[$playType])
						{
							$dfsType = 1;
						}
					}
					break;
				case '23528':
					if(count($parsedCast['preTuo']) > $minLength[$parsedCast['playTypeCode']])
					{
						$dfsType = 1;
					}
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