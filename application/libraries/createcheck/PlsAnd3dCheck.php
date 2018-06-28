<?php
/**
 * 大乐透订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class PlsAnd3dCheck extends BaseCheck
{
	//后缀  1:1 直选 2:1 组三单式  2:3 组三复式 3:3 组六
	private $mode = array('1:1', '2:1', '2:3', '3:3');
	private $balls = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
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
		if($params['orderType'] == 1)
		{
			$this->setParams($params, 1);
			$this->chase = 1;
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkSale', 'checkChaseIssue', 'checkAissue', 'checkCodes', 'checkOrderLimit');
		}
		elseif ($params['orderType'] == 4 && $params['type'] == 1)
		{
			$this->setParams($params, 41);
			$checkMethods = array('checkParams');
		}
		else
		{
			$this->setParams($params, $params['orderType']);
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkIsChase', 'checkMaxMoney', 'checkSale', 'checkIssue', 'checkAissue', 'checkCodes', 'checkOrderLimit');
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
		$check = true;
		$checklimit = true;
		$limitmsg = array();
		$ticketCount = 0;
		foreach ($codes_arr as $betcode)
		{
			$codes = explode(':', $betcode);
			$mode = $codes['1'] . ':' . $codes['2'];
			if(empty($codes[0]) || (!in_array($mode, $this->mode)))
			{
				$check = false;
				break;
			}
			
			$method = 'checkPlay' . $codes['1'];
			$result = $this->$method($codes[0], $codes['2']);
			
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
					if (!in_array(BetCnName::getCnPlaytype($this->params['lid'], $codes['1']).$lcode, $limitmsg))
						array_push($limitmsg, BetCnName::getCnPlaytype($this->params['lid'], $codes['1']).$lcode);
				}
			}
			$rebetnum += $result['betNum'];
			$ticketCount += 1;
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
				if($this->params['money'] != ($rebetnum * $this->params['multi'] * 2))
				{
					return array('status' => false, 'code' => 403, 'msg' => '订单校验错误');
				}
			}
			
			//计算票张数
			$this->ticketCount = $ticketCount;
			
			return array('status' => true, 'msg' => '');
		}
	}
	
	/**
	 * 直选校验
	 * @param string $code
	 */
	private function checkPlay1($code, $mode = '')
	{
		$balls = explode(',', $code);
		if(count($balls) != 3)
		{
			return array('status' => false);
		}
		$betTnum = 1;
		foreach ($balls as $key => $code)
		{
			$codes = str_split($code);
			if(($this->isValueRepeat($codes)) || (!$this->checkBalls($codes)))
			{
				return array('status' => false);
			}
			$betTnum *= count($codes);
		}
		
		$result = array(
				'status' => true,
				'betNum' => $betTnum
		);
		
		return $result;
	}
	
	/**
	 * 组三校验
	 * @param string $code
	 * @param int $mode
	 * @return multitype:boolean |multitype:boolean number
	 */
	private function checkPlay2($code, $mode = '')
	{
		$balls = explode(',', $code);
		if($mode == 1)
		{
			//单式
			if((count($balls) != 3) || (count(array_unique($balls)) != 2) || (!$this->checkBalls($balls)))
			{
				return array('status' => false);
			}
			
			$result = array(
				'status' => true,
				'betNum' => 1
			);
		}
		else
		{
			//复式
			if((count($balls) < 2) || ($this->isValueRepeat($balls)) || (!$this->checkBalls($balls)))
			{
				return array('status' => false);
			}
			
			$betnum =  $this->CI->libcomm->combine(count($balls), 2);
			$result = array(
				'status' => true,
				'betNum' => $betnum * 2
			);
		}
		
		return $result;
	}
	
	/**
	 * 组六校验
	 * @param string $code
	 * @param int $mode
	 * @return multitype:boolean |multitype:boolean NULL
	 */
	private function checkPlay3($code, $mode = '')
	{
		$balls = explode(',', $code);
		$ballCount = count($balls);
		if(($ballCount < 3) || ($this->isValueRepeat($balls)) || (!$this->checkBalls($balls)))
		{
			return array('status' => false);
		}
		
		$result = array(
			'status' => true,
			'betNum' => $this->CI->libcomm->combine($ballCount, 3)
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
	
	protected function checkLimitCode($codes, $playType)
	{
		
		$limitcodes = $this->limitcodes[(int)$playType];
		$limitcodeArr = array();
		if (!empty($limitcodes))
		{
			foreach ($limitcodes as $limitcode)
			{
				$flag = true;
				if ($playType == 2 && count(array_unique(explode(',', $codes))) == count(explode(',', $codes)))
				{
					foreach (array_unique(explode(',', $limitcode)) as $lc)
					{
						if (strpos($codes, $lc) === false)
						{
							$flag = false;
							break;
						}
					}
				}
				elseif ($playType == 1 || ($playType == 2 && count(array_unique(explode(',', $codes))) < count(explode(',', $codes))))
				{
					$codesArr = explode(',', $codes);
					if ($playType == 2) sort($codesArr);
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
					foreach (explode(',', $limitcode) as $lc)
					{
						if (strpos($codes, $lc) === false)
						{
							$flag = false;
							break;
						}
					}
				}
				if ($flag) array_push($limitcodeArr, $limitcode);
			}
		}
		if ($limitcodeArr) return array('status' => true, 'limitcode' => $limitcodeArr);
		return array('status' => false, 'limitcode' => '');
	}

}
