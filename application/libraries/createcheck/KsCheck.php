<?php
/**
 * 十一选五订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class KsCheck extends BaseCheck
{
	//玩法定义
	private $playTypes = array('1', '2', '3', '4', '5', '6', '7', '8');
	//号码值定义
	private $balls = array('1', '2', '3', '4', '5', '6');
	//和值数值定义
	private $hBalls = array('3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18');
	private $chase = 0;

	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library('BetCnName');
	}
	
	/**
	 * 投注检查主方法
	 * @param unknown_type $params
	 * @return unknown|multitype:boolean string
	 */
	public function check($params = array())
	{
		if($params['orderType'] == 1)
		{
			$this->setParams($params, 1);
			$this->chase = 1;
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkSale', 'checkChaseIssue', 'checkCodes', 'checkOrderLimit');
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
		$money = 0;
		$rebetnum = 0;
		$check = true;
		$checklimit = true;
		$limitmsg = array();
		foreach ($codes_arr as $code)
		{
			$codes = explode(':', $code);
			if(empty($codes[0]) || (!in_array($codes['1'], $this->playTypes, true)) || (!(preg_match("/^[1-9]\d*$/", $codes['2']))))
			{
				$check = false;
				break;
			}
			
			$method = 'checkPlay' . $codes['1'];
			$result = $this->$method($codes[0]);
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
					if (!in_array(BetCnName::getCnPlaytype(53, $codes['1']).$this->getLimitStr($codes['1'], $lcode), $limitmsg))
						array_push($limitmsg, BetCnName::getCnPlaytype(53, $codes['1']).$this->getLimitStr($codes['1'], $lcode));
				}
			}
			$money += $codes['2'] * 2 * $result['betNum'];
			$rebetnum += $result['betNum'];
		}
		
		if($check == false)
		{
			return array('status' => false, 'code' => 401, 'msg' => '投注串校验错误');
		}
		elseif (!$checklimit)
		{
			return array('status' => false, 'code' => 402, 'msg' => implode('、', $limitmsg)."官方限号，暂不支持预约投注");
		}
		else 
		{
			if($rebetnum != $this->params['betTnum'])
			{
				return array('status' => false, 'code' => 401, 'msg' => '投注串校验错误');
			}
			if(empty($this->chase))
			{
				if($this->params['money'] != ($money * $this->params['multi']))
				{
					return array('status' => false, 'code' => 403, 'msg' => '订单校验错误');
				}
			}

			//计算票张数
			$this->ticketCount = $rebetnum;
			
			return array('status' => true, 'msg' => '');
		}
	}
	
	/**
	 * 和值校验
	 * @param unknown_type $code
	 */
	private function checkPlay1($code)
	{
		$codes = explode(',', $code);
		foreach ($codes as $code)
		{
			if(!in_array($code, $this->hBalls, true))
			{
				return array('status' => false);
			}
		}
		if(($this->isValueRepeat($codes)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => count($codes));
	}
	
	/**
	 * 三同号通选
	 * @param unknown_type $code
	 */
	private function checkPlay2($code)
	{
		if($code != '0,0,0')
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 三同号单选
	 * @param unknown_type $code
	 */
	private function checkPlay3($code)
	{
		$codes = explode(',', $code);
		$uCodes = array_unique($codes);
		if(count($codes) != 3 || count($uCodes) != 1 || (!$this->checkBalls($uCodes)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 三不同号
	 * @param unknown_type $code
	 */
	private function checkPlay4($code)
	{
		$codes = explode(',', $code);
		$uCodes = array_unique($codes);
		if(count($codes) != 3 || count($uCodes) != 3 || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 三连号通选
	 * @param unknown_type $code
	 */
	private function checkPlay5($code)
	{
		if($code != '0,0,0')
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 两同号复选
	 * @param unknown_type $code
	 */
	private function checkPlay6($code)
	{
		$codes = explode(',', $code);
		if(($codes[0] != $codes[1]) || ($codes[2] != '*') || (!in_array($codes[0], $this->balls, true)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 两同号单选
	 * @param unknown_type $code
	 */
	private function checkPlay7($code)
	{
		$codes = explode(',', $code);
		$uCodes = array_unique($codes);
		if(count($codes) != 3 || count($uCodes) != 2 || (!$this->checkBalls($codes)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
	}
	
	/**
	 * 两不同号
	 * @param unknown_type $code
	 */
	private function checkPlay8($code)
	{
		$codes = explode(',', $code);
		if(($codes[0] == $codes[1]) || ($codes[2] != '*') || (!in_array($codes[0], $this->balls, true)) || (!in_array($codes[1], $this->balls, true)))
		{
			return array('status' => false);
		}
		
		return array('status' => true, 'betNum' => 1);
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
	
	private function getLimitStr($playType, $code)
	{
		switch ($playType)
		{
			case 1:
				return $code;
				break;
			case 2:
			case 5:
				return '';
				break;
			case 8:
				return str_replace(array('*', ','), '', $code);
				break;
			default:
				return str_replace(',', '', $code);
				break;
		}
	}
	
	protected function checkLimitCode($codes, $playType)
	{
		$limitcodes = $this->limitcodes[(int)$playType];
		$limitcodeArr = array();
		if (!empty($limitcodes))
		{
			foreach ($limitcodes as $limitcode)
			{
				$flag = true;
				if ($playType != 1)
				{
					$codesArr = explode(',', $codes);
					foreach (explode(',', $limitcode) as $k => $lc)
					{
						if (strpos($codesArr[$k], $lc) === false)
						{
							$flag = false;
							break;
						}
					}
				}
				else
				{
					$codeArr = explode(',', $codes);
					$limitArr = explode(',', $limitcode);
					if (array_values(array_intersect($codeArr, $limitArr)) != $limitArr) $flag = false;
				}
				if ($flag) array_push($limitcodeArr, $limitcode);
			}
		}
		if ($limitcodeArr) return array('status' => true, 'limitcode' => $limitcodeArr);
		return array('status' => false, 'limitcode' => '');
	}

}
