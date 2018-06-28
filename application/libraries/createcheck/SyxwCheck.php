<?php
/**
 * 十一选五订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class SyxwCheck extends BaseCheck
{
	//玩法定义
	private $playTypes = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12','13','14','15');
	//后缀  01 正常  02 胆拖
	private $mode = array('01', '05');
	private $balls = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11');
	private $chase = 0;
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library(array('libcomm', 'BetCnName'));
	}
	
	/**
	 * 投注检查主方法
	 * @param unknown_type $params
	 * @return unknown|multitype:boolean string
	 */
	public function check($params = array())
	{
		//追号
		if($params['orderType'] == 1)
		{
			$this->setParams($params, 1);
			$this->chase = 1;
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkSale', 'checkCodes','checkChaseIssue', 'checkOrderLimit');
		}
		else
		{
			$this->setParams($params);
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkMaxMoney', 'checkSale', 'checkIssue', 'checkCodes', 'checkOrderLimit');
		}
		
		$this->limitcodes = $this->CI->cache->hGet($this->REDIS['LIMIT_CODE'], $this->params['lid']);
		if (!empty($this->limitcodes)) $this->limitcodes = json_decode($this->limitcodes, true);
		
		foreach ($checkMethods as $method)
		{
			$result = $this->$method();
			if($result['status'] == false)
			{
				return $result;
			}
		}
		
		$result = array(
			'status' => true,
			'msg' => '',
		);
		
		return $result;
	}
	
	public function checkCodes()
	{
		$codes_arr = explode(';', $this->params['codes']);
		$rebetnum = 0;
		$totalMoney = 0;
		$check = true;
		$checklimit = true;
		$limitmsg = array();
		$is_have = 1;//是否支持乐选3,4,5
		$ticketCount = 0;
		foreach ($codes_arr as $code)
		{
			$codes = explode(':', $code);
			if(empty($codes[0]) || (!in_array($codes['1'], $this->playTypes, true)) || (!in_array($codes['2'], $this->mode, true)))
			{
				$check = false;
				break;
			}
			
			$isSalts = intval($codes['2']) == '5' ? true : false;
			$playType = intval($codes['1']);
			//对十一选5严格控制  老11选5 才支持乐选3,4,5	
			if($this->params['lid'] != '21406' && in_array($playType,array(13,14,15)))
			{
				$check = false;
				$is_have = 0;
				break;
			}
			$method = 'checkPlay' . $playType;
			$result = $this->$method($codes[0], $isSalts);
			if($result['status'] == false)
			{
				$check = false;
				break;
			}
			
			$limitres = $this->checkLimitCode($codes[0], $codes['1']);
			if ($limitres['status'])
			{
				$checklimit = false;
				foreach ($limitres['limitcode'] as $lcode)
				{
					if (!in_array(BetCnName::getCnPlaytype($this->params['lid'], (int)$codes['1']).$lcode, $limitmsg))
						array_push($limitmsg, BetCnName::getCnPlaytype($this->params['lid'], (int)$codes['1']).$lcode);
				}
			}
			$rebetnum += $result['betNum'];
			$totalMoney += $result['betNum']*$this->getPrice($playType);
			$ticketCount += 1;
		}
		
		if($check == false)
		{
			if($is_have === 0 ) { return array('status' => false, 'msg' => '该彩种不支持乐选玩法');}
			return array('status' => false, 'code' => 401, 'msg' => '投注串校验错误');
		}
		elseif (!$checklimit)
		{
			return array('status' => false, 'code' => 402, 'msg' => implode('，', $limitmsg)."官方限号，暂不支持预约投注");
		}
		else 
		{
			if($rebetnum != $this->params['betTnum'])
			{
				return array('status' => false, 'code' => 401, 'msg' => '投注串校验错误');
			}
			if(empty($this->chase))
			{
				//if($this->params['money'] != ($rebetnum * $this->params['multi'] * 2))
				if($this->params['money'] != $this->params['multi']*$totalMoney)
				{
					return array('status' => false, 'code' => 403, 'msg' => '订单校验错误');
				}
			}
			
			//计算票张数
			$this->ticketCount = $ticketCount;
			//针对11选5
			$this->params['totalMoney'] = $totalMoney;
			$this->params['way'] = 'syxw';
			return array('status' => true, 'msg' => '','totalMoney'=>$totalMoney);
		}
	}
	
	/**
	 * 前一校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay1($code, $isSalts)
	{
		$codes = explode(',', $code);
		$countBalls = count($codes);
		if(($countBalls < 1) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || $isSalts || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		$result = array(
			'status' => true,
			'betNum' => $countBalls
		);
		
		return $result;
	}
	
	/**
	 * 任二校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay2($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 2) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
			
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 2)
			);
			return $result;
		}
		else 
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts != 1) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
			
			if((($countSalts + $countBalls) < 3) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
			
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 2 - $countSalts)
			);
			return $result;
		}
		
		return array('status' => false);
	}
	
	/**
	 * 任三校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay3($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 3) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
				
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 3)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 2) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
				
			if((($countSalts + $countBalls) < 4) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
				
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 3 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 任四校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay4($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 4) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 4)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 3) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 5) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 4 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 任五校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay5($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 5) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 5)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 4) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 6) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 5 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 任六校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay6($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 6) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 6)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 5) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 7) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 6 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 任七校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay7($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 7) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 7)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 6) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 8) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
					'status' => true,
					'betNum' => $this->CI->libcomm->combine($countBalls, 7 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 任八校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay8($code, $isSalts)
	{
		$codes = explode(',', $code);
		$countBalls = count($codes);
		if(($countBalls < 8) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || $isSalts || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		$result = array(
			'status' => true,
			'betNum' => $this->CI->libcomm->combine($countBalls, 8),
		);
		
		return $result;
	}
	
	/**
	 * 前二直选校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay9($code, $isSalts)
	{
		$balls = explode('|', $code);
		$codes[0] = explode(',', $balls[0]);
		$codes[1] = explode(',', $balls[1]);
		$count0 = count($codes[0]);
		$count1 = count($codes[1]);
		if(($count0 < 1) || ($count0 > 11) || ($this->isValueRepeat($codes[0])) || $isSalts || (!$this->checkBalls($codes[0])))
		{
			return array('status' => false);
		}
		
		if(($count1 < 1) || ($count1 > 11) || ($this->isValueRepeat($codes[1])) || (!$this->checkBalls($codes[1])))
		{
			return array('status' => false);
		}
		//第一位和第二位是否有重复值
		if(array_intersect($codes[0], $codes[1]))
		{
			return array('status' => false);
		}
	
		$result = array(
				'status' => true,
				'betNum' => $count0 * $count1,
		);
	
		return $result;
	}
	
	/**
	 * 前三直选校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay10($code, $isSalts)
	{
		$balls = explode('|', $code);
		$codes[0] = explode(',', $balls[0]);
		$codes[1] = explode(',', $balls[1]);
		$codes[2] = explode(',', $balls[2]);
		$count0 = count($codes[0]);
		$count1 = count($codes[1]);
		$count2 = count($codes[2]);
		if(($count0 < 1) || ($count0 > 11) || ($this->isValueRepeat($codes[0])) || $isSalts || (!$this->checkBalls($codes[0])))
		{
			return array('status' => false);
		}
	
		if(($count1 < 1) || ($count1 > 11) || ($this->isValueRepeat($codes[1])) || (!$this->checkBalls($codes[1])))
		{
			return array('status' => false);
		}
		if(($count2 < 1) || ($count2 > 11) || ($this->isValueRepeat($codes[2])) || (!$this->checkBalls($codes[2])))
		{
			return array('status' => false);
		}
		//第一位和第二位是否有重复值
		if(array_intersect($codes[0], $codes[1]) || array_intersect($codes[0], $codes[2]) || array_intersect($codes[1], $codes[2]))
		{
			return array('status' => false);
		}
	
		$result = array(
			'status' => true,
			'betNum' => $count0 * $count1 * $count2,
		);
	
		return $result;
	}
	
	/**
	 * 前二组选校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay11($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 2) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 2)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts != 1) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 3) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 2 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * 前三组选校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlay12($code, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$codes = explode(',', $code);
			$countBalls = count($codes);
			if(($countBalls < 3) || ($countBalls > 11) || ($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
	
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 3)
			);
			return $result;
		}
		else
		{
			//有胆码
			$ball = explode('$', $code);
			$salts = explode(',', $ball[0]);
			$balls = explode(',', $ball[1]);
			$countSalts = count($salts);
			$countBalls = count($balls);
			//判断胆码选择是否正确
			if(($countSalts < 1) || ($countSalts > 2) || (!$this->checkBalls($salts)) || ($this->isValueRepeat($salts)))
			{
				return array('status' => false);
			}
			if(array_intersect($salts, $balls))
			{
				return array('status' => false);
			}
	
			if((($countSalts + $countBalls) < 4) || (!$this->checkBalls($balls)) || ($this->isValueRepeat($balls)))
			{
				return array('status' => false);
			}
	
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, 3 - $countSalts)
			);
			return $result;
		}
	
		return array('status' => false);
	}
	
	/**
	 * [checkPlay13 乐3玩法]
	 * @author JackLee 2017-04-07
	 * @param  [type] $code    [description]
	 * @param  [type] $isSalts [description]
	 * @return [type]          [description]
	 */
	private function checkPlay13($code, $isSalts)
	{
		$codes = explode('|', $code);
		$countBalls = count($codes);
		if(($countBalls != 3)  || ($this->isValueRepeat($codes)) || $isSalts || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		$result = array(
			'status' => true,
			'betNum' => 1,
		);
		return $result;
	}
	/**
	 * [checkPlay14 乐4玩法]
	 * @author JackLee 2017-04-07
	 * @param  [type] $code    [description]
	 * @param  [type] $isSalts [description]
	 * @return [type]          [description]
	 */
	private function checkPlay14($code, $isSalts)
	{
		$codes = explode(',', $code);
		$countBalls = count($codes);
		if(($countBalls != 4)  || $isSalts || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		$result = array(
			'status' => true,
			'betNum' => 1,
		);

		return $result;
	}
	/**
	 * [checkPlay15 乐5玩法]
	 * @author JackLee 2017-04-07
	 * @param  [type] $code    [description]
	 * @param  [type] $isSalts [description]
	 * @return [type]          [description]
	 */
	private function checkPlay15($code, $isSalts)
	{
		$codes = explode(',', $code);
		$countBalls = count($codes);
		if(($countBalls != 5)  || $isSalts || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		$result = array(
			'status' => true,
			'betNum' => 1,
		);

		return $result;
	}
	/**
	 * 检查选择的球号码   true 球正确  false 选球错误
	 * @param unknown_type $balls
	 * @return boolean
	 */
	private function checkBalls($balls = array())
	{
		foreach ($balls as $ball)
		{
			if(!in_array($ball, $this->balls, true))
			{
				return false;
			}
		}
		
		return true;
	}

	/**
	 * [getPrice 获取每注金额]
	 * @author LiKangJian 2017-04-17
	 * @param  [type] $playType [description]
	 * @return [type]           [description]
	 */
	private function getPrice($playType)
	{
		$eachPrice = 2;
		if($playType == 13){$eachPrice = 6;}
		if($playType == 14){$eachPrice = 10;}
		if($playType == 15){$eachPrice = 14;}
		return $eachPrice;
	}
}
