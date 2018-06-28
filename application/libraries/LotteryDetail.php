<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 数字彩订单详情解析类
 * @author Administrator
 *
 */
class LotteryDetail
{
	
	private $_jjcPlayType = array(
    		'1' => '单关',
    		'2' => '2*1',
    		'3' => '3*1',
    		'4' => '4*1',
    		'5' => '5*1',
    		'6' => '6*1',
    		'7' => '7*1',
    		'8' => '8*1',
    		'9' => '9*1',
    		'10' => '10*1',
    		'11' => '11*1',
    		'12' => '12*1',
    		'13' => '13*1',
    		'14' => '14*1',
    		'15' => '15*1',
    		'16' => '2*3',
    		'17' => '3*3',
    		'18' => '3*4',
    		'19' => '3*7',
    		'20' => '4*4',
    		'21' => '4*5',
    		'22' => '4*6',
    		'23' => '4*11',
    		'24' => '4*15',
    		'25' => '5*5',
    		'26' => '5*6',
    		'27' => '5*10',
    		'28' => '5*16',
    		'29' => '5*20',
    		'30' => '5*26',
    		'31' => '5*31',
    		'32' => '6*6',
    		'33' => '6*7',
    		'34' => '6*15',
    		'35' => '6*20',
    		'36' => '6*22',
    		'37' => '6*35',
    		'38' => '6*42',
    		'39' => '6*50',
    		'40' => '6*57',
    		'41' => '6*63',
    		'42' => '7*7',
    		'43' => '7*8',
    		'44' => '7*21',
    		'45' => '7*35',
    		'46' => '7*120',
    		'47' => '8*8',
    		'48' => '8*9',
    		'49' => '8*28',
    		'50' => '8*56',
    		'51' => '8*70',
    		'52' => '8*247',
			'53' => '自由过关',
    );
	
	public function __construct() {
	    $this->CI = &get_instance();
	}
	
	/**
	 * 投注串解析
	 * @param unknown_type $lid
	 * @param unknown_type $code
	 */
	private function parseCast($lid, $code, &$playType = null, $type = 'order')
	{
		$code = str_replace('#', '$', $code);
		$parts = explode(':', $code);
		$numbers = $parts[0];
		$playType = $playType ? $playType : $parts[1];
		$hasDan = strpos($code, '$');
		$result = array(
			'playTypeCode' => $playType,
			'modeCode' => $parts[2],
			'preDan' => array(),
			'preTuo' => array(),
			'postDan' => array(),
			'postTuo' => array(),
			'hasDan' => $hasDan
		);
		//双色球、大乐透
		if(in_array($lid, array(SSQ, DLT)))
		{
			$numTmp = explode('|', $numbers);
			$pre = $numTmp[0];
			$post = $numTmp[1];
			if($hasDan)
			{
				$preSplit = explode('$', $pre);
				if(count($preSplit) === 2)
				{
					$result['preDan'] = explode(',', $preSplit[0]);
					$result['preTuo'] = explode(',', $preSplit[1]);
				}
				else
				{
					$result['preTuo'] = explode(',', $preSplit[0]);
				}
				
				$postSplit = explode('$', $post);
				if(count($postSplit) === 2)
				{
					$result['postDan'] = explode(',', $postSplit[0]);
					$result['postTuo'] = explode(',', $postSplit[1]);
				}
				else
				{
					$result['postTuo'] = explode(',', $postSplit[0]);
				}
			}
			else
			{
				$result['preTuo'] = explode(',', $pre);
				$result['postTuo'] = explode(',', $post);
			}
		}
		elseif(in_array($lid, array(SYXW, JXSYXW, HBSYXW, GDSYXW)))
		{
			if(in_array((int)$playType, array(9, 10, 13)))
			{
				$result['preTuo'] = preg_split('/\||\*/', $numbers);
			}
			else
			{
				if($hasDan)
				{
					$preSplit = explode('$', $numbers);
					$result['preDan'] = explode(',', $preSplit[0]);
					$result['preTuo'] = explode(',', $preSplit[1]);
				}
				else
				{
					$result['preTuo'] = explode(',', $numbers);
				}
			}
		}
		elseif (in_array($lid, array(KS, JLKS, JXKS)))
		{
			if($playType == 2)
			{
				$result['preTuo'][] = '三同号通选';
			}elseif ($playType == 5)
			{
				$result['preTuo'][] = '三连号通选';
			}elseif($playType == 1 || $type == 'chase')
			{
				$result['preTuo'] = explode(',', $numbers);
			}elseif($playType == 8)
			{
				$result['preTuo'][] = str_replace(array(',', '*'), '', $numbers);
			}
			else
			{
				$result['preTuo'][] = str_replace(',', '', $numbers);
			}
		}elseif ($lid == KLPK) {
    		$preSplit = explode('$', $numbers);
    		$arr = array(
    			0  => array('01'=>'A', '02'=>'2', '03'=>'3', '04'=>'4', '05'=>'5', '06'=>'6', '07'=>'7', '08'=>'8', '09'=>'9', '10'=>'10', '11'=>'J', '12'=>'Q', '13'=>'K'),
    			7  => array('00'=>'包选', '01'=>'黑桃', '02'=>'红桃', '03'=>'梅花', '04'=>'方块'),
    			8  => array('00'=>'包选', '01'=>'黑桃', '02'=>'红桃', '03'=>'梅花', '04'=>'方块'),
    			9  => array('00'=>'包选', '01'=>'A23', '02'=>'234', '03'=>'345', '04'=>'456', '05'=>'567', '06'=>'678', '07'=>'789', '08'=>'8910', '09'=>'910J', '10'=>'10JQ', '11'=>'JQK', '12'=>'QKA'),
    			10 => array('00'=>'包选', '01'=>'AAA', '02'=>'222', '03'=>'333', '04'=>'444', '05'=>'555', '06'=>'666', '07'=>'777', '08' =>'888', '09'=>'999', '10'=>'101010', '11'=>'JJJ', '12'=>'QQQ', '13'=>'KKK'),
    			11 => array('00'=>'包选', '01'=>'AA', '02'=>'22', '03'=>'33', '04'=>'44', '05'=>'55', '06'=>'66', '07'=>'77', '08'=>'88', '09'=>'99', '10'=>'1010', '11'=>'JJ', '12'=>'QQ', '13'=>'KK'),
    		);
    		$nArr = in_array($playType, array(7, 8, 9, 10, 11)) ? $arr[$playType] : $arr[0];
    		if (count($preSplit) == 2) {
    			foreach (explode(',', $preSplit[0]) as $dan) {
    				$result['preDan'][] = $nArr[$dan];
    			}
    			foreach (explode(',', $preSplit[1]) as $tuo) {
    				$result['preTuo'][] = $nArr[$tuo];
    			}
    		}else {
    			foreach (explode(',', $preSplit[0]) as $tuo) {
    				$result['preTuo'][] = $nArr[$tuo];
    			}
    		}
    	}
    	elseif ($lid == CQSSC && $playType == 1)
    	{
    		$arr = array(1 => '大', 2 => '小', 4 => '单', 5 => '双');
    		$numbers = explode(',', $numbers);
    		foreach ($numbers as &$num)
    		{
    			$num = $arr[$num];
    		}
    		$result['preTuo'] = $numbers;
    	}
    	elseif (in_array($lid, array(FCSD, PLS, PLW, QXC))) {
    		if(strpos($code, '*'))
    		{
    			$result['preTuo'] = explode('*', $numbers);
    		}
    		else
    		{
    			$result['preTuo'] = explode(',', $numbers);
    		}
    		if ($playType > 1) sort($result['preTuo']);
    	}
		else
		{
			$result['preTuo'] = explode(',', $numbers);
		}
		return $result;
	}
	
	/**
	 * 返回单式玩法
	 * @param unknown_type $lid
	 * @param unknown_type $playType
	 * @param unknown_type $playType1
	 */
	private function getPlayTypeName($lid, $playType, $hasDan, $multi)
	{
		$cnName = '';
		$dfs = $multi ? '复式' : '单式';
		switch ($lid) {
			case SYXW:
			case JXSYXW:
			case HBSYXW:
			case GDSYXW:
				$end = $hasDan ? '胆拖' : $dfs;
				$playCnNames = array(
					1  => "前一".$end,
					2  => "任二".$end,
					3  => "任三".$end,
					4  => "任四".$end,
					5  => "任五".$end,
					6  => "任六".$end,
					7  => "任七".$end,
					8  => "任八".$end,
					9  => "前二直选",
					10 => "前三直选",
					11 => "前二".($hasDan ? '胆拖' : '组选'),
					12 => "前三".($hasDan ? '胆拖' : '组选'),
					13 => "乐三".$end,
					14 => "乐四".$end,
					15 => "乐五".$end,
				);
				$cnName = $playCnNames[(int)$playType];
				break;
			case PLS:
			case FCSD:
				$playCnNames = array(1 => "直选".$dfs, 2 => "组三".$dfs, 3 => "组六".$dfs);
				$cnName = $playCnNames[(int)$playType];
				break;
			case SSQ:
			case DLT:
				$cnName = $hasDan ? '胆拖' : $dfs;
				break;
			case KS:
			case JLKS:
			case JXKS:
				$playCnNames = array(1 => '和值', 2 => '三同号通选', 3 => '三同号单选', 4 => '三不同号', 5 => '三连号通选', 6 => '二同号复选', 7 => '二同号单选', 8 => '二不同号');
				$cnName = $playCnNames[(int)$playType];
				break;
			case KLPK:
				$playCnNames = array(
					1 => '任选一',
					2  => '任二单式',
					21 => '任二复式',
					22 => '任二胆拖',
					3  => '任三单式',
					31 => '任三复式',
					32 => '任三胆拖',
					4  => '任四单式',
					41 => '任四复式',
					42 => '任四胆拖',
					5  => '任五单式',
					51 => '任五复式',
					52 => '任五胆拖',
					6  => '任六单式',
					61 => '任六复式',
					62 => '任六胆拖',
					7  => '同花',
					8  => '同花顺',
					9  => '顺子',
					10 => '豹子',
					11 => '对子',
				);
				$cnName = $playCnNames[(int)$playType];
				break;
			case CQSSC:
				$playCnNames = array(
					1 => '大小单双', 
					10 => '一星直选', 
					20 => '二星直选', 
					21 => '二星直选', 
					23 => '二星组选',
					27 => '二星组选',
					30 => '三星直选', 
					31 => '三星直选', 
					33 => '三星组三单', 
					34 => '三星组六',
					37 => '三星组三复',
                    38 => '三星组六', 
                    40 => '五星直选', 
                    41 => '五星直选', 
                    43 => '五星通选'
				);
				$cnName = $playCnNames[(int)$playType];
				break;
			default:
				$cnName = $dfs;
				break;
		}
	
		return $cnName;
	}
	
	//共用拼接投注串方法
	public function renderCode($code, $lid, $playType = null, $award = null, $isChase = null, $type = 'order')
	{
		if ($type === 'chase') {
			$render = array(
				'red' => 'renderRed',
				'blue' => 'renderChaseBlue',
				'grey' => 'renderGray',
			);
		}else {
			$render = array(
				'red' => 'renderRedDetail',
				'blue' => 'renderBlueDetail',
				'grey' => 'renderGrayDetail',
			);
		}
		$tpl = '';
		$parsedCast = $this->parseCast($lid, $code, $playType, $type);
		$betNum = $this->getBetNum($lid, $parsedCast);
		$parsedAward = array('preCode' => array(), 'postCode' => array());
		if ($award) $parsedAward = $this->parseAward($award, $lid);
		$preDan = $preTpl = $postTpl = $postDan = '';
		if(count($parsedCast['preDan']) > 0)
		{
			$preDan .= $this->$render['grey']('(');
			foreach ($parsedCast['preDan'] as $dan)
			{
				if(in_array($dan, $parsedAward['preCode']))
				{
					$preDan .= $this->$render['red']($dan);
				}
				else
				{
					$preDan .= $this->$render['grey']($dan);
				}
			}
			$preDan .= $this->$render['grey'](')');
		}
		if(count($parsedCast['postDan']) > 0)
		{
			$postDan .= ($type === 'chase') ? $this->$render['blue']('(') : $this->$render['grey']('(');
			foreach ($parsedCast['postDan'] as $dan)
			{
				if(in_array($dan, $parsedAward['postCode']))
				{
					$postDan .= $this->$render['red']($dan);
				}
				elseif ($type === 'chase')
				{
					$postDan .= $this->$render['blue']($dan);
				}
				else
				{
					$postDan .= $this->$render['grey']($dan);
				}
			}
			$postDan .= ($type === 'chase') ? $this->$render['blue'](')') : $this->$render['grey'](')');
		}
		foreach ($parsedCast['preTuo'] as $key => $number)
		{
			if (in_array($lid, array(PLS, PLW, FCSD, QXC))) {
				switch ($parsedCast['playTypeCode']) {
					case 2:
						$awardcode = $parsedAward['preCode'];
						sort($awardcode);
						if (count(array_unique($parsedCast['preTuo'])) < count($parsedCast['preTuo'])) {
							if (count(array_unique($awardcode)) == 2 && $number == $awardcode[$key]) {
								$preTpl .= $this->$render['red']($number);
							}else {
								$preTpl .= $this->$render['grey']($number);
							}
						} else {
							if (count(array_unique($awardcode)) == 2 && in_array($number, $awardcode)) {
								$preTpl .= $this->$render['red']($number);
							} else {
								$preTpl .= $this->$render['grey']($number);
							}
						}
						break;
					case 3:
						if(count(array_unique($parsedAward['preCode'])) == 3 && in_array($number, $parsedAward['preCode'])) {
							$preTpl .= $this->$render['red']($number);
						} else {
							$preTpl .= $this->$render['grey']($number);
						}
						break;
					case 1:
					default:
						if($preTpl != '') $preTpl .= $this->$render['grey']('|');
						$num = explode(',', $number);
						$num = strlen($num[0]) > 1 ? str_split($number) : $num;
						foreach ($num as $val) {
							if($val == $parsedAward['preCode'][$key]) {
								$preTpl .= $this->$render['red']($val);
							} else {
								$preTpl .= $this->$render['grey']($val);
							}
						}
						break;
				}
			}elseif(in_array($lid, array(SYXW, JXSYXW, HBSYXW, GDSYXW)))
    		{
    			if($playType == 1)
    			{
    				$qy = isset($parsedAward['preCode'][0]) ? array($parsedAward['preCode'][0]) : array();
    				if(in_array($number, $qy))
    				{
    					$preTpl .= $this->$render['red']($number);
    				}
    				else
    				{
    					$preTpl .= $this->$render['grey']($number);
    				}
    			}
    			elseif($playType == '9')
    			{
    				if($preTpl != '')
    				{
    					$preTpl .= $this->$render['grey']('|');
    				}
    				$num = explode(',', $number);
    				foreach ($num as $val)
    				{
    					if (($val == $parsedAward['preCode'][$key])) 
    					{
    						$preTpl .= $this->$render['red']($val);
    					} 
    					else 
    					{
    						$preTpl .= $this->$render['grey']($val);
    					}
    				}
    			}
    			elseif($playType == '10')
    			{
    				if($preTpl != '')
    				{
    					$preTpl .= $this->$render['grey']('|');
    				}
    				$num = explode(',', $number);
    				foreach ($num as $val)
    				{
    					if (($val == $parsedAward['preCode'][$key])) 
    					{
    						$preTpl .= $this->$render['red']($val);
    					} 
    					else 
    					{
    						$preTpl .= $this->$render['grey']($val);
    					}
    				}
    			}
    			elseif($playType == 11)
    			{
    				$qezx = isset($parsedAward['preCode'][0]) ? array($parsedAward['preCode'][0], $parsedAward['preCode'][1]) : array();
    				if(in_array($number, $qezx))
    				{
    					$preTpl .= $this->$render['red']($number);
    				}
    				else
    				{
    					$preTpl .= $this->$render['grey']($number);
    				}
    			}
    			elseif($playType == 12)
    			{
    				$qszx = isset($parsedAward['preCode'][0]) ? array($parsedAward['preCode'][0], $parsedAward['preCode'][1], $parsedAward['preCode'][2]) : array();
    				if(in_array($number, $qszx))
    				{
    					$preTpl .= $this->$render['red']($number);
    				}
    				else
    				{
    					$preTpl .= $this->$render['grey']($number);
    				}
    			}
    			else if ( $playType == 13 )
				{
					//前三直选
					// 每位各选1或多个号码，选号与开奖号码前三位号码相同（且顺序一致）
					if($preTpl != '')
    				{
    					$preTpl .= $this->$render['grey']('|');
    				}
					$numTmp = explode(',', $number);
					foreach($numTmp as $n1)
					{
						if (in_array($n1, $parsedAward['preCode']))
						{
							$preTpl .= $this->$render['red']($n1);
						}
						else
						{
							$preTpl .= $this->$render['grey']($n1);
						}
					}
				}
    			else
    			{
    				if(in_array($number, $parsedAward['preCode']))
    				{
    					$preTpl .= $this->$render['red']($number);
    				}
    				else
    				{
    					$preTpl .= $this->$render['grey']($number);
    				}
    			}
    		}
    		elseif(in_array($lid, array(KS, JLKS, JXKS)))
    		{
    			$check = $parsedAward['hasAward'] ? $this->ksAwardCheck($number, $parsedAward['preCode'], $playType) : false; 
    			if($check)
    			{
    				$preTpl .= $this->$render['red']($number);
    			}
    			else
    			{
    				$preTpl .= $this->$render['grey']($number);
    			}
    			
    		}
    		elseif ($lid == KLPK) {
    			$check = $parsedAward['hasAward'] ? $this->klpkAwardCheck($number, $parsedAward, $playType) : false;
    			if($check)
    			{
    				$preTpl .= $this->$render['red']($number);
    			}
    			else
    			{
    				$preTpl .= $this->$render['grey']($number);
    			}
    		}
    		elseif ($lid == CQSSC) {
    			switch ($playType) {
    				case '1':
    					if($preTpl != '') $preTpl .= $this->$render['grey']('|');
    					$num = explode(',', $number);
    					$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					foreach ($num as $val) {
    						if($award && (($val == '大' && $awardcode[$key] > 4) || ($val == '小' && $awardcode[$key] <= 4) || ($val == '单' && $awardcode[$key]%2 == 1) || ($val == '双' && $awardcode[$key]%2 === 0))) {
    							$preTpl .= $this->$render['red']($val);
    						} else {
    							$preTpl .= $this->$render['grey']($val);
    						}
    					}
    					break;
    				case '10':
    					$num = strlen($number) > 1 ? str_split($number) : array($number);
    					foreach ($num as $val) {
    						if($val == $parsedAward['preCode'][4]) {
    							$preTpl .= $this->$render['red']($val);
    						} else {
    							$preTpl .= $this->$render['grey']($val);
    						}
    					}
    					break;
    				case '20':
    				case '21':
    					$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if($preTpl != '') $preTpl .= $this->$render['grey']('|');
    					$num = explode(',', $number);
    					$num = strlen($num[0]) > 1 ? str_split($number) : $num;
    					foreach ($num as $val) {
    						if($val == $awardcode[$key]) {
    							$preTpl .= $this->$render['red']($val);
    						} else {
    							$preTpl .= $this->$render['grey']($val);
    						}
    					}
    					break;
    				case '23':
    				case '27':
    					$awardcode = array($parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if(in_array($number, $awardcode)) {
    						$preTpl .= $this->$render['red']($number);
    					} else {
    						$preTpl .= $this->$render['grey']($number);
    					}
    					break;
    				case '30':
    				case '31':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if($preTpl != '') $preTpl .= $this->$render['grey']('|');
    					$num = explode(',', $number);
    					$num = strlen($num[0]) > 1 ? str_split($number) : $num;
    					foreach ($num as $val) {
    						if($val == $awardcode[$key]) {
    							$preTpl .= $this->$render['red']($val);
    						} else {
    							$preTpl .= $this->$render['grey']($val);
    						}
    					}
    					break;
    				case '33':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					sort($awardcode);
    					if (count(array_unique($awardcode)) == 2 && $number == $awardcode[$key]) {
    						$preTpl .= $this->$render['red']($number);
    					}else {
    						$preTpl .= $this->$render['grey']($number);
    					}
    					break;
    				case '37':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if (count(array_unique($awardcode)) == 2 && in_array($number, $awardcode)) {
    						$preTpl .= $this->$render['red']($number);
    					} else {
    						$preTpl .= $this->$render['grey']($number);
    					}
    					break;
    				case '34':
    				case '38':
    					$awardcode = array($parsedAward['preCode'][2], $parsedAward['preCode'][3], $parsedAward['preCode'][4]);
    					if(count(array_unique($awardcode)) == 3 && in_array($number, $awardcode)) {
    						$preTpl .= $this->$render['red']($number);
    					} else {
    						$preTpl .= $this->$render['grey']($number);
    					}
    					break;
    				case '40':
    				case '41':
    				case '43':
    					if($preTpl != '') $preTpl .= $this->$render['grey']('|');
    					$num = explode(',', $number);
    					$num = strlen($num[0]) > 1 ? str_split($number) : $num;
    					foreach ($num as $val) {
    						if($val == $parsedAward['preCode'][$key]) {
    							$preTpl .= $this->$render['red']($val);
    						} else {
    							$preTpl .= $this->$render['grey']($val);
    						}
    					}
    					break;
    			}
    		}
			else
			{
				if(in_array($number, $parsedAward['preCode']))
				{
					$preTpl .= $this->$render['red']($number);
				}
				else
				{
					$preTpl .= $this->$render['grey']($number);
				}
			}
		}
		foreach ($parsedCast['postTuo'] as $number)
		{
			if(in_array($number, $parsedAward['postCode']) || $type === 'chase')
			{
				$postTpl .= $this->$render['blue']($number);
			}
			else
			{
				$postTpl .= $this->$render['grey']($number);
			}
		}
		$playTypeCast = $this->getPlayTypeName($lid, $parsedCast['playTypeCode'], $parsedCast['hasDan'], ($betNum > 1));
		$playtpl = '[' . $playTypeCast . ($isChase > 0 ? '追加' : '') . ']';
		$tpl = $playtpl . $preDan . $preTpl;
		$postTpl = $postDan . $postTpl;
		if($postTpl)
		{
			if ($type === 'order') $tpl .= $this->$render['grey']('+');
			$tpl .= $postTpl;
		}
		return array('code' => $tpl, 'betNum' => $betNum);
		
	}
	
	private function getBetNum($lid, $code) {
		$minArr = array(
			SSQ => array(6, 1),
			DLT => array(5, 2),
			QLC => 7,
			SYXW => array(9 => array(1, 1), 10 => array(1, 1, 1), 11 => 2, 12 => 3),
			JXSYXW => array(9 => array(1, 1), 10 => array(1, 1, 1), 11 => 2, 12 => 3),
			HBSYXW => array(9 => array(1, 1), 10 => array(1, 1, 1), 11 => 2, 12 => 3),
		    GDSYXW => array(9 => array(1, 1), 10 => array(1, 1, 1), 11 => 2, 12 => 3),
		);
		switch ($lid) {
			case SSQ:
			case DLT:
				return $this->combine(count($code['preTuo']), $minArr[$lid][0] - count($code['preDan'])) * $this->combine(count($code['postTuo']), $minArr[$lid][1] - count($code['postDan']));
				break;
			case FCSD:
			case PLS:
			case PLW:
			case QXC:
				switch ($code['playTypeCode']) {
					case 1:
						$sum = 1;
						foreach ($code['preTuo'] as $k => $preTuo) {
							$sum *= $this->combine(strlen(preg_replace('/\D/', '', $code['preTuo'][$k])), 1);
						}
						return $sum;
						break;
					case 2:
						if (count($code['preTuo']) == 3 && count(array_unique($code['preTuo'])) == 2) {
							return 1;
						}else {
							return $this->combine(count($code['preTuo']), 2) * 2;
						}
						break;
					case 3:
					default:
						return $this->combine(count($code['preTuo']), 3);
						break;
				}
				break;
			case QLC:
				return $this->combine(count($code['preTuo']), $minArr[$lid]);
				break;
			case SYXW:
			case JXSYXW:
			case HBSYXW:
			case GDSYXW:
				if (in_array($code['playTypeCode'], array(11, 12))) {
					return $this->combine(count($code['preTuo']), $minArr[$lid][$code['playTypeCode']] - count($code['preDan']));
				}elseif (in_array($code['playTypeCode'], array(9, 10))) {
					$sum = 1;
					if (count($code['preTuo']) == 1) $code['preTuo'] = explode('*', $code['preTuo'][0]);
					foreach ($code['preTuo'] as $k => $preTuo) {
						$sum *= $this->combine(count(explode(',', $preTuo)), 1);
					}
					return $sum;
				}elseif (in_array($code['playTypeCode'], array(13, 14, 15))) {
					return 1;
				}else {
					return $this->combine(count($code['preTuo']), $code['playTypeCode'] - count($code['preDan']));
				}
				break;
			case KS:
			case JLKS:
			case JXKS:
				return count($code['preTuo']);
				break;
			case KLPK:
				if (in_array($code['playTypeCode'], array(1, 2, 21, 22, 3, 31, 32, 4, 41, 42, 5, 51, 52, 6, 61, 62))) {
					return $this->combine(count($code['preTuo']), substr($code['playTypeCode'], 0, 1) - count($code['preDan']));
				}else {
					return count($code['preTuo']);
				}
				break;
			case CQSSC:
				if ($code['playTypeCode'] == 1)
				{
					return 1;
				}
				elseif ($code['playTypeCode'] == 37)
				{
					return $this->combine(count($code['preTuo']), 2) * 2;
				}
				elseif (in_array($code['playTypeCode'], array('10', '20', '21', '30', '31', '40', '41', '43')))
				{
					$sum = 1;
					foreach ($code['preTuo'] as $k => $preTuo) {
						$sum *= strlen($preTuo);
					}
					return $sum;
				}
				else 
				{
					return $this->combine(count($code['preTuo']), substr($code['playTypeCode'], 0, 1));
				}
				break;
		};
	}
	
	/**
	 * 组装中奖信息
	 * @param unknown_type $code
	 */
	public function renderAward($code, $lid = null)
	{
		$tpl = '';
		$codes = $this->parseAward($code, $lid);
		if($codes['hasAward'])
		{
			if ($lid == KLPK) {
				$tpl = $this->renderKlpk($codes['preCode'], $codes['postCode']);
			}else {
				foreach ($codes['preCode'] as $num)
				{
					$tpl .= $this->renderRed($num);
				}
				foreach ($codes['postCode'] as $num)
				{
					$tpl .= $this->renderBlue($num);
				}
			}
		}
		else
		{
			$tpl = '未开奖';
		}
		return $tpl;
	}
	
	/**
	 * 解析中奖号码
	 * @param unknown_type $code
	 * @return 
	 */
	private function parseAward($code, $lid = null)
	{
		$result = array(
			'hasAward' => false,
			'preCode'  => array(),
			'postCode' => array(),
		);
		if($code)
		{
			$result['hasAward'] = true;
			if(strpos($code, ':'))
			{
				$parts = explode(':', $code);
				$result['preCode'] = explode(',', $parts[0]);
				$result['postCode'] = explode(',', $parts[1]);
			}
			else
			{
				$result['preCode'] = explode(',', $code);
			}
		}
		
		if ($lid == KLPK) {
			$arr = array('01'=>'A', '02'=>'2', '03'=>'3', '04'=>'4', '05'=>'5', '06'=>'6', '07'=>'7', '08'=>'8', '09'=>'9', '10'=>'10', '11'=>'J', '12'=>'Q', '13'=>'K');
			foreach ($result['preCode'] as &$pcode) {
				$pcode = $arr[$pcode];
			}
		}
		
		return $result;
	}
	
	private function renderKlpk($preCode, $postCode)
	{
		$tpl = "<div class='klpk-num'>";
		for ($i = 0; $i < 3; $i++) {
			$tpl .= "<span class='klpk-num-".strtolower($postCode[$i])."'>".$preCode[$i]."</span>";
		}
		$tpl .= '</div>';
		return $tpl;
	}
	
	/**
	 * 红球样式
	 * @param unknown_type $num
	 */
	private function renderRed($num)
	{
		return '<span class="ball ball-red">' . $num . '</span>';
	}
	
	/**
	 * 蓝球样式
	 * @param unknown_type $num
	 */
	private function renderBlue($num)
	{
		return '<span class="ball ball-blue">' . $num . '</span>';
	}
	
	private function renderChaseBlue($num)
	{
		return '<span class="num-blue">' . $num . '</span>';
	}
	
	private function renderGray($num)
	{
		return '<span>' . $num . '</span>';
	}
	
	/**
	 * 分隔符
	 * @param unknown_type $num
	 */
	public function renderGrayDetail($num) 
	{
		return '<em>' . $num . '</em>';
	}
	
	/**
	 * 蓝球样式
	 * @param unknown_type $num
	 */
	private function renderBlueDetail($num)
	{
		return '<em class="spec">' . $num . '</em>';
	}
	
	/**
	 * 红球样式
	 * @param unknown_type $num
	 * @return string
	 */
	private function renderRedDetail($num) 
	{
		return '<em class="spec">' . $num . '</em>';
	}

	private function combine($n, $m) {
		$dividend = $this->factorial($n, $n - $m + 1);
		$divisor = $this->factorial($m);
	
		return $dividend / $divisor;
	}
	
	private function factorial($n, $s = 0) {
		if ($n == 0) {
			return 1;
		}
		$product = 1;
		($s > 0) || ($s = 1);
		for ($i = $s; $i <= $n; ++$i) {
			$product *= $i;
		}
		return $product;
	}
	
	
	/**
	 * 返回订单状态
	 * @param unknown_type $status
	 */
	public function getTicketStatus($status, $hm = false)
	{
		switch ((int)$status) {
			case 0:
			case 40:
			case 240:
				return '等待出票';
				break;
			case 500:
			case 1000:
			case 1010:
            case 1020:
			case 2000:
				return '出票成功';
				break;
			case 600:
				return $hm ? '方案撤单' : '出票失败';
				break;
			case 610:
				return '发起人撤单';
				break;
			case 620:
				return '未满员撤单';
				break;
			default:
				return '---';
				break;
		}
	}
	
	/**
	 * 返回奖金
	 * @param unknown_type $status
	 * @param unknown_type $bouns
	 */
	public function getTicketBonus($status, $bouns)
	{
		if(in_array($status, array('500')))
		{
			$status = '<span class="ddkj">等待开奖</a>';
		}
		elseif(in_array($status, array('2000')))
		{
			if($bouns > 0)
			{
				$status = '<span class="bingo">' . number_format(ParseUnit($bouns, 1), 2) . '</span>';
			}
			else
			{
				$status = '<span class="wzj">未中奖</span>';
			}
		}
		elseif(in_array($status, array('1000')))
		{
			$status = '<span class="wzj">未中奖</span>';
		}
		elseif(in_array($status, array('1010','1020')))
		{
			$status = '<span class="ddkj">等待开奖</a>';
		}
		else
		{
			$status = '---';
		}
		return $status;
	}
	
	/**
	 * 计算奖金
	 * @param unknown_type $key
	 * @param unknown_type $order
	 * @return number
	 */
	public function getBonus($key, $order, $award)
	{
		$bonus = 0;
		$bonusDetail = json_decode($order['bonus_detail'], true);
		switch ($order['lid'])
		{
			case SSQ:
			case QXC:
			case QLC:
				foreach ($bonusDetail as $dk => $detail)
				{
					if ($detail[$key] > 0)
					{
						$bonus = $award['bonusDetail'][$dk."dj"]['dzjj'] * 100 * $detail[$key] * $order['multi'];
						break;
					}
				}
				break;
			case DLT:
				foreach ($bonusDetail as $dk => $detail)
				{
					if ($detail[$key] > 0)
					{
						if ($order['isChase'] > 0)
						{
							$bonus = $award['bonusDetail'][$dk."dj"]['zj']['dzjj'] * 100 * $detail[$key] * $order['multi'];
							$bonus += $award['bonusDetail'][$dk."dj"]['jb']['dzjj'] * 100 * $detail[$key] * $order['multi'];
						}
						else
						{
							$bonus = $award['bonusDetail'][$dk."dj"]['jb']['dzjj'] * 100 * $detail[$key] * $order['multi'];
						}
					}
				}
				break;
			case PLS:
			case PLW:
			case FCSD:
			case SYXW:
			case JXSYXW:
			case HBSYXW:
			case GDSYXW:
				if($bonusDetail[$key] > 0)
				{
					if (in_array($order['lid'], array(21406, 21407, 21408)))
					{
						$typeArr = array(1 => 'qy', 2 => 'r2', 3 => 'r3', 4 => 'r4', 5 => 'r5', 6 => 'r6', 7 => 'r7', 8 => 'r8', 9 => 'q2zhix', 
								10 => 'q3zhix', 11 => 'q2zux', 12 => 'q3zux', 13 => 'lexuan3', 14 => 'lexuan4', 15 => 'lexuan5');
					}
					else
					{
						$typeArr = array(1 => 'zx', 2 => 'z3', 3 => 'z6');
					}
					$bonus = $award['bonusDetail'][$typeArr[$order['playType']]]['dzjj'] * 100 * $bonusDetail[$key] * $order['multi'];
				}
				break;
			case KLPK:
				$typeArr = array(1 => 'r1', 2 => 'r2', 3 => 'r3', 4 => 'r4', 5 => 'r5', 6 => 'r6');
				$bonus = $award['bonusDetail'][$typeArr[$order['playType']]]['dzjj'] * 100 * $bonusDetail[$key] * $order['multi'];
				break;
		}
		 
		return $bonus;
	}
	
	
	/**
	 * 判断号码是否中奖
	 * @param unknown_type $number
	 * @param unknown_type $award
	 * @param unknown_type $playType
	 */
	private function ksAwardCheck($number, $award, $playType)
	{
		sort($award);
		switch ($playType)
		{
			case 1:
				$isAward = (array_sum($award) == $number) ? true : false;
				break;
			case 2:
				$aUnique = array_unique($award);
				$isAward = count($aUnique) == 1 ? true : false;
				break;
			case 3:
				$award = implode('', $award);
				$isAward = ($award == $number) ? true : false;
				break;
			case 4:
				$number = str_split($number);
				$intersect = array_intersect($number, $award);
				$isAward = (count($intersect) == 3) ? true : false;
				break;
			case 5:
				$isAward = in_array(implode('', $award), array('123', '234', '345', '456')) ? true : false;
				break;
			case 6:
				$number = substr($number, 0, 2);
				$award = implode('', $award);
				$isAward = (strpos($award, $number) !== false) ? true : false;
				break;
			case 7:
				$number = str_split($number);
				sort($number);
				$number = implode('', $number);
				$award = implode('', $award);
				$isAward = ($award == $number) ? true : false;
				break;
			case 8:
				$number = str_split($number);
				$intersect = array_intersect($number, $award);
				$isAward = (count($intersect) == 2) ? true : false;
				break;
			default:
				$isAward = false;
				break;
		}
		 
		return $isAward;
	}
	
	private function klpkAwardCheck($number, $award, $playType) {
		$pArr = array('S' => '黑桃', 'H' => '红桃', 'C' => '梅花', 'D' => '方块');
		$th = false;
		$sz = false;
		$bz = false;
		$dz = false;
		if ($award['postCode'][0] == $award['postCode'][1] && $award['postCode'][1] == $award['postCode'][2]) $th = true;
		if ($award['preCode'][0] + 1 == $award['preCode'][1] && $award['preCode'][1] + 1 == $award['preCode'][2]) $sz = true;
		if ($award['preCode'][0] == $award['preCode'][1] && $award['preCode'][1] == $award['preCode'][2]) $bz = true;
		if (count(array_count_values($award['preCode']) == 2)) $dz = true;
		switch ($playType) {
			case 7:
				$isAward = false;
				if (($number == $pArr[$award['postCode'][0]] || strpos($number, '包选') !== false) && $th) $isAward = true;
			case 8:
				if (($number == $pArr[$award['postCode'][0]] || strpos($number, '包选') !== false) && $th && $sz) $isAward = true;
				break;
			case 9:
				if (($award['preCode'][0].$award['preCode'][1].$award['preCode'][2] === $number || strpos($number, '包选') !== false) && $sz) $isAward = true;
				break;
			case 10:
				$isAward = (($number == $pArr[$award['postCode'][0]] || strpos($number, '包选') !== false) && $bz) ? true : false;
				break;
			case 11:
				$aArr = array_flip(array_count_values($award['preCode']));
				$isAward = (((string)$number === (string)$aArr[2].$aArr[2] || strpos($number, '包选') !== false) && $dz) ? true:false;
				break;
			default:
				$isAward = (in_array($number, $award['preCode'])) ? true : false;
				break;
		}
		return $isAward;
	}
	
	public function ticketDetail($mid, $playType, $tickets, $info, $matchData = array())
	{
	    
	    $this->CI->load->config('wenan');
	    $wenan = $this->CI->config->item('wenan');
	
		$matchDetail = '';
		//胜平负
        $spf = array(
            '0' => $wenan['jzspf']['0'],
            '1' => $wenan['jzspf']['1'],
            '3' => $wenan['jzspf']['3']
        );
        
        $rqspf = array(
            '0' => $wenan['jzspf']['r0'],
            '1' => $wenan['jzspf']['r1'],
            '3' => $wenan['jzspf']['r3']
        );
        $sf = array(
    	    '0' => $wenan['jlsf']['0'],
    		'3' => $wenan['jlsf']['3']
    	);
    	$rfsf = array(
    	    '0' => $wenan['jlsf']['r0'],
    		'3' => $wenan['jlsf']['r3']
    	);
        //大小分
        $dxf = array(
            '0' => '小分',
            '3' => '大分'
        );
		//胜分差
		$sfc = array(
			'01' => $wenan['jlsf']['3']."1-5分",
    		'02' => $wenan['jlsf']['3']."6-10分",
    		'03' => $wenan['jlsf']['3']."11-15分",
    		'04' => $wenan['jlsf']['3']."16-20分",
    		'05' => $wenan['jlsf']['3']."21-25分",
    		'06' => $wenan['jlsf']['3']."26+分",
    		'11' => $wenan['jlsf']['0']."1-5分",
    		'12' => $wenan['jlsf']['0']."6-10分",
    		'13' => $wenan['jlsf']['0']."11-15分",
    		'14' => $wenan['jlsf']['0']."16-20分",
    		'15' => $wenan['jlsf']['0']."21-25分",
    		'16' => $wenan['jlsf']['0']."26+分",
		);
		//获取赛事日期
		$date = substr($mid, 0,4).'-'.substr($mid, 4,2).'-'.substr($mid, 6,2);
		//拆分获取 日期 + 赛事编号
		$weekarray = array("日","一","二","三","四","五","六");
		$matchDetail = "<b";
		// 新增对阵信息
		if(!empty($matchData[$mid]))
        {
        	$matchDetail .= " class='bubble-tip' tiptext='";
        	if(in_array($playType, array('RFSF', 'SF', 'DXF', 'SFC')))
            {
                $matchDetail .= $matchData[$mid]['awary'] . " VS " . $matchData[$mid]['home'];
            }
            else
            {
                $matchDetail .= $matchData[$mid]['home'] . " VS " . $matchData[$mid]['awary'];
            }
        	$matchDetail .= "'";
        }
		$matchDetail .= ">周".$weekarray[date("w",strtotime($date))];
		$matchDetail .= substr($mid, 8).'</b>';
		$matchDetail .= '(';
	
		//拆分获取 赛果选择 + 赔率  存在单场复试 分隔符 /
		$resBet =  explode('/', $tickets);
		$info = json_decode($info, true);
		$count = count($resBet);
		switch ($playType)
		{
			//胜平负
			case 'SPF':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
					$matchDetail .= $spf[$matches[1]].':'.$info["vs"]["v{$matches[1]}"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//让球胜平负
			case 'RQSPF':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
					$matchDetail .= $rqspf[$matches[1]]. '[' . ($info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0]) . ']' .':' . $info["letVs"]["v{$matches[1]}"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//猜比分
			case 'CBF':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
					$index = preg_replace('/[^\d]/is', '', $matches[1]);
					$bf = $this->getCbf($matches[1]);
					$matchDetail .= $bf.':' . $info["score"]["v$index"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//总进球
			case 'JQS':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
					if($matches[1] >= 7)
					{
						$res = $info["goal"]["v7"][0];
						$matches[1] = '7+';
					}
					else
					{
						$res = $info["goal"]["v".$matches[1]][0];
					}
					$matchDetail .= $matches[1] . ':' . $res . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//半全场
			case 'BQC':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
					$spfInfo = explode('-', $matches[1]);
					$matchDetail .= $spf[$spfInfo[0]] . '-'. $spf[$spfInfo[1]] . ':' . $info["half"]["v$spfInfo[0]$spfInfo[1]"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//竞彩篮球 让分胜负
			case 'RFSF':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
					$matchDetail .= $rfsf[$matches[1]] . '[' . ($info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0]) . ']' . ':' . $info['letVs']["v$matches[1]"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//竞彩篮球 胜负
			case 'SF':
				foreach ($resBet as $kBet => $vBet)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
					$matchDetail .= $sf[$matches[1]].':'.$info["vs"]["v{$matches[1]}"][0] . ' ';
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//竞彩篮球 大小分
			case 'DXF':
				foreach ($resBet as $kBet => $vBet)
				{
					$in_map = array('0' => 'l', '3' => 'g');
					preg_match('/^(\d+)\(.*?\).*?$/is', $vBet, $matches);
					$matchDetail .= $dxf[$matches[1]] . '[' . $info['bs']['basePoint'][0] . ']' . ':' . $info['bs'][$in_map[$matches[1]]][0];
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
				//竞彩篮球 胜分差
			case 'SFC':
				foreach ($resBet as $kBet => $vBet) {
					preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
					$matchDetail .= $sfc[$matches[1]] . ':' . $info['diff']["v$matches[1]"][0];
					if($kBet < $count - 1)
					{
						$matchDetail .= ', ';
					}
				}
				break;
			default:
				# code...
				break;
		}
	
		$matchDetail .= ')';

		return $matchDetail;
	}
	
	private function getCbf($bf)
	{
		$bfStr = explode(':', $bf);
		if($bfStr[0] == $bfStr[1])
		{
			if($bfStr[0] >3)
			{
				$bfRes = '平其他';
			}
			else
			{
				$bfRes = $bf;
			}
		}
		elseif($bfStr[0]>5)
		{
			$bfRes = '胜其他';
		}
		elseif($bfStr[1]>5)
		{
			$bfRes = '负其他';
		}
		else
		{
			$bfRes = $bf;
		}
		return $bfRes;
	}
	
	public function getjjcPlayType($playType)
	{
		return $this->_jjcPlayType[$playType] ? $this->_jjcPlayType[$playType] : $playType."*1";
	}
	
}