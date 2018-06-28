<?php
/**
 * 大乐透订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class DltCheck extends BaseCheck
{
	//后缀  1:1 正常 2:1 追加  135:1 胆拖追加 135:5 胆拖
	private $mode = array('1:1', '2:1', '135:1', '135:5');
	private $rballs = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35');
	private $bballs = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
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
			$betcodes = explode('|', $betcode);
			$endstrs = explode(':', $betcodes[1]);
			$mode = $endstrs['1'] . ':' . $endstrs['2'];
			if(empty($betcodes[0]) || (empty($endstrs[0])) || (!in_array($mode, $this->mode)))
			{
				$check = false;
				break;
			}
			//追加判断
			if(($this->params['isChase'] && (!in_array($mode, array('2:1', '135:1')))) || (!$this->params['isChase'] && (!in_array($mode, array('1:1', '135:5')))))
			{
				$check = false;
				break;
			}
			
			$isSalts = ($endstrs['1'] == '135') ? true : false;
			$result = $this->checkBalls($betcodes[0], $endstrs[0], $isSalts);
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
				$price = ($this->params['isChase'] > 0) ? 3 : 2;
				if($this->params['money'] != ($rebetnum * $this->params['multi'] * $price))
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
	 * @param string $bball
	 * @param boolean $isSalts
	 */
	private function checkBalls($rball, $bball, $isSalts)
	{
		if(!$isSalts)
		{
			//无胆码
			$rballs = explode(',', $rball);
			$bballs = explode(',', $bball);
			$rCount = count($rballs);
			$bCount = count($bballs);
			if(($rCount < 5) || ($bCount < 2) || ($this->isValueRepeat($rballs)) || ($this->isValueRepeat($bballs)) || (!$this->checkRballs($rballs)) || (!$this->checkBballs($bballs)))
			{
				return array('status' => false);
			}
			
			$pre_cbt =  $this->CI->libcomm->combine($rCount, 5);
			$pos_cbt =  $this->CI->libcomm->combine($bCount, 2);
			$result = array(
				'status' => true,
				'betNum' => $pre_cbt * $pos_cbt
			);
			
			return $result;
		}
		else
		{
			//有胆码
			$rTmp = explode('$', $rball);
			$preSalt = isset($rTmp[1]) ? explode(',', $rTmp[0]) : array();
			$pre_balls = isset($rTmp[1]) ? explode(',', $rTmp[1]) : explode(',', $rTmp[0]);
			$bTmp = explode('$', $bball);
    		$posSalt = isset($bTmp[1]) ? explode(',', $bTmp[0]) : array();
    		$pos_balls = isset($bTmp[1]) ? explode(',', $bTmp[1]) : explode(',', $bTmp[0]);
			$preSaltCount = count($preSalt);
			$posSaltCount = count($posSalt);
			$rCount = count($pre_balls);
			$bCount = count($pos_balls);
			if(($preSaltCount > 4) || ($posSaltCount > 1) || ($preSaltCount == 0 && $posSaltCount == 0)
				|| ($preSaltCount + $rCount < 5) || ($posSaltCount + $bCount < 2) || ($preSaltCount + $rCount + $posSaltCount + $bCount < 8))
			{
				return array('status' => false);
			}
			
			if(($this->isValueRepeat($preSalt)) || ($this->isValueRepeat($pre_balls)) || ($this->isValueRepeat($posSalt))
				|| ($this->isValueRepeat($pos_balls)) || (!$this->checkRballs($preSalt)) || (!$this->checkRballs($pre_balls))
				|| (!$this->checkBballs($posSalt)) || (!$this->checkBballs($pos_balls)))
			{
				return array('status' => false);
			}
			$pre_cbt =  $this->CI->libcomm->combine($rCount, 5 - $preSaltCount);
			$pos_cbt =  $this->CI->libcomm->combine($bCount, 2 - $posSaltCount);
			$result = array(
					'status' => true,
					'betNum' => $pre_cbt * $pos_cbt
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
			if(!in_array($ball, $this->rballs, true))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 检查选择的蓝球球号码   true 球正确  false 选球错误
	 * @param unknown_type $balls
	 * @return boolean
	 */
	private function checkBballs($balls = array())
	{
		foreach ($balls as $ball)
		{
			if(!in_array($ball, $this->bballs, true))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 追加字段限制
	 */
	public function checkIsChase()
	{
		if(!in_array($this->params['isChase'], array('0', '1'), true))
		{
			$result = array(
				'status' => false,
				'msg' => '请求参数错误',
			);
		}
		else
		{
			$result = array(
				'status' => true,
			);
		}
		
		return $result;
	}

}
