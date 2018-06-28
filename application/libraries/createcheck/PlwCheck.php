<?php
/**
 * 排列五订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class PlwCheck extends BaseCheck
{
	//后缀  1:1 正常 
	private $mode = array('1:1');
	private $balls = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	//彩种列  排列5 5列   七星彩  7列
	private $column = array('35' => 5, '10022' => 7);
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
		$singleCount = 0;
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
			$result = $this->dismantle($codes[0]);
			if($result['status'] == false)
			{
				$check = false;
				break;
			}
			
			$limitres = $this->checkLimitCode($codes[0], 1);
			if ($limitres['status'])
			{
				$checklimit = false;
				foreach ($limitres['limitcode'] as $lcode)
				{
					if (!in_array("排列五".$lcode, $limitmsg))
						array_push($limitmsg, "排列五".$lcode);
				}
			}
			$rebetnum += $result['betNum'];
			//计算票张数
			if($result['betNum'] > 1)
			{
				$ticketCount += 1;
			}
			else
			{
				$singleCount += 1;
			}
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
			$this->ticketCount = $ticketCount + (ceil($singleCount / 5));
				
			return array('status' => true, 'msg' => '');
		}
	}
	
	private function dismantle($ball)
	{
		$balls = explode(',', $ball);
		if(count($balls) != $this->column[$this->params['lid']])
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
				$codesArr = explode(',', $codes);
				foreach (explode(',', $limitcode) as $k => $lc)
				{
					if (strpos($codesArr[$k], $lc) === false)
					{
						$flag = false;
						break;
					}
				}
				if ($flag) array_push($limitcodeArr, $limitcode);
			}
		}
		if ($limitcodeArr) return array('status' => true, 'limitcode' => $limitcodeArr);
		return array('status' => false, 'limitcode' => '');
	}

}
