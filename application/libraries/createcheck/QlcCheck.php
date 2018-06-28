<?php
/**
 * 七乐彩订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class QlcCheck extends BaseCheck
{
	//后缀  1:1 正常  135:5 胆拖(暂时前端不支持投注故删除)
	private $mode = array('1:1');
	private $balls = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30');
	private $chase = 0;
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library('libcomm');
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
			
			$isSalts = ($mode == '135:5') ? true : false;
			$result = $this->checkBalls($codes[0], $isSalts);
			if($result['status'] == false)
			{
				$check = false;
				break;
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
	
	/**
	 * 选球检查
	 * @param string $rball
	 * @param boolean $isSalts
	 */
	private function checkBalls($rball, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$rballs = explode(',', $rball);
			$rCount = count($rballs);
			if(($rCount < 7) || ($this->isValueRepeat($rballs)) || (!$this->checkRballs($rballs)))
			{
				return array('status' => false);
			}
			
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($rCount, 7),
			);
			
			return $result;
		}
		else
		{
			//有胆码
			$rTmp = explode('$', $rball);
			$preSalt = isset($rTmp[1]) ? explode(',', $rTmp[0]) : array();
			$pre_balls = isset($rTmp[1]) ? explode(',', $rTmp[1]) : array();
			$saltCount = count($preSalt);
			$rCount = count($pre_balls);
			if(($saltCount < 1) || ($saltCount > 6) || (($saltCount + $rCount) < 8) 
				|| (array_intersect($preSalt, $pre_balls)) || (!$this->checkRballs($pre_balls)) || ($this->isValueRepeat($pre_balls))
				|| (!$this->checkRballs($preSalt)) || ($this->isValueRepeat($preSalt)))
			{
				return array('status' => false);
			}
			
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($rCount, 7 - $saltCount)
			);
				
			return $result;
		}
	}
	
	/**
	 * 检查选择的红球球号码   true 球正确  false 选球错误
	 * @param unknown_type $balls
	 * @return boolean
	 */
	private function checkRballs($balls = array())
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
}
