<?php
/**
 * 十一选五订单创建检查类
 * @author shigx
 *
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class KlpkCheck extends BaseCheck
{
	//玩法定义
	private $playTypes = array('1', '2', '21', '22', '3', '31', '32', '4', '41', '42', '5', '51', '52', '6', '61', '62', '7', '8', '9', '10', '11');
	//号码值定义
	private $balls = array(
		0 => array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13'),
		7 => array('00', '01', '02', '03', '04'),
		8 => array('00', '01', '02', '03', '04'),
		9 => array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
		10 => array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13'),
		11 => array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13')
	);
	private $showstr = array(
    	0  => array('01'=>'A', '02'=>'2', '03'=>'3', '04'=>'4', '05'=>'5', '06'=>'6', '07'=>'7', '08'=>'8', '09'=>'9', '10'=>'10', '11'=>'J', '12'=>'Q', '13'=>'K'),
    	7  => array('00'=>'包选', '01'=>'黑桃', '02'=>'红桃', '03'=>'梅花', '04'=>'方块'),
    	8  => array('00'=>'包选', '01'=>'黑桃', '02'=>'红桃', '03'=>'梅花', '04'=>'方块'),
    	9  => array('00'=>'包选', '01'=>'A23', '02'=>'234', '03'=>'345', '04'=>'456', '05'=>'567', '06'=>'678', '07'=>'789', '08'=>'8910', '09'=>'910J', '10'=>'10JQ', '11'=>'JQK', '12'=>'QKA'),
    	10 => array('00'=>'包选', '01'=>'AAA', '02'=>'222', '03'=>'333', '04'=>'444', '05'=>'555', '06'=>'666', '07'=>'777', '08' =>'888', '09'=>'999', '10'=>'101010', '11'=>'JJJ', '12'=>'QQQ', '13'=>'KKK'),
    	11 => array('00'=>'包选', '01'=>'AA', '02'=>'22', '03'=>'33', '04'=>'44', '05'=>'55', '06'=>'66', '07'=>'77', '08'=>'88', '09'=>'99', '10'=>'1010', '11'=>'JJ', '12'=>'QQ', '13'=>'KK'),
    );
	//和值数值定义
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
		$rebetnum = 0;
		$check = true;
		$checklimit = true;
		$limitmsg = array();
		$ticketCount = 0;
		foreach ($codes_arr as $code)
		{
			$codes = explode(':', $code);
			if(empty($codes[0]) || (!in_array($codes['1'], $this->playTypes, true)) || $codes['2'] != 1)
			{
				$check = false;
				break;
			}
			
			if (in_array($codes['1'], array(7, 8, 9, 10, 11))) {
				$result = $this->checkPlay($codes[0], $codes['1']);
			}else {
				$result = $this->checkPlayRx($codes[0], $codes['1']);
			}
			
			if($result['status'] == false)
			{
				$check = false;
				break;
			}
			
			$singlePlayType = $this->getSinglePlayType($codes['1']);
			$limitres = $this->checkLimitCode($codes[0], $singlePlayType);
			if ($limitres['status'])
			{
				$checklimit = false;
				foreach ($limitres['limitcode'] as $lcode)
				{
					if (!in_array(str_replace('单式', '', BetCnName::getCnPlaytype(54, $singlePlayType)).$this->getLimitStr($singlePlayType, $lcode), $limitmsg))
						array_push($limitmsg, str_replace('单式', '', BetCnName::getCnPlaytype(54, $singlePlayType)).$this->getLimitStr($singlePlayType, $lcode));
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
	 * 对子校验
	 * @param unknown_type $code
	 */
	private function checkPlay($code0, $code1)
	{
		$codes = explode(',', $code0);
		foreach ($codes as $code)
		{
			if(!in_array($code, $this->balls[$code1], true))
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
	 * 任二校验
	 * @param unknown_type $code
	 * @param unknown_type $isSalts
	 */
	private function checkPlayRx($code0, $code1)
	{
		if ((strlen($code1) == 1 && $code1 != 1) || strpos($code0, ',') === false) {
			$codes = explode(',', $code0);
			$countBalls = count($codes);
			if($countBalls != $code1 || $this->isValueRepeat($codes) || !$this->checkBalls($codes, 0)) {
				return array('status' => false);
			}
				
			$result = array(
				'status' => true,
				'betNum' => $this->CI->libcomm->combine($countBalls, $code1)
			);
			return $result;
		}else {
			$type = substr($code1, -1);
			if ($type == 1) {
				$codes = explode(',', $code0);
				$countBalls = count($codes);
				if($countBalls <= substr($code1, 0, 1) || $countBalls > 13 || $this->isValueRepeat($codes) || !$this->checkBalls($codes, 0)) {
					return array('status' => false);
				}
					
				$result = array('status' => true, 'betNum' => $this->CI->libcomm->combine($countBalls, substr($code1, 0, 1)));
				return $result;
			}else {
				//有胆码
				$ball = explode('$', $code0);
				$salts = explode(',', $ball[0]);
				$balls = explode(',', $ball[1]);
				$countSalts = count($salts);
				$countBalls = count($balls);
				//判断胆码选择是否正确
				if(($countSalts > substr($code1, 0, 1) - 1) || $countSalts == 0 || (!$this->checkBalls($salts, 0)) || ($this->isValueRepeat($salts)))
				{
					return array('status' => false);
				}
				if(array_intersect($salts, $balls))
				{
					return array('status' => false);
				}
				
				if((($countSalts + $countBalls) < substr($code1, 0, 1)+1) || (!$this->checkBalls($balls, 0)) || ($this->isValueRepeat($balls)))
				{
					return array('status' => false);
				}
				
				$result = array(
						'status' => true,
						'betNum' => $this->CI->libcomm->combine($countBalls, substr($code1, 0, 1) - $countSalts)
				);
				return $result;
			}
		}
		return array('status' => false);
	}
	
	/**
	 * 检查选择的球号码   true 球正确  false 选球错误
	 * @param unknown_type $balls
	 * @return boolean
	 */
	private function checkBalls($balls = array(), $i)
	{
		foreach ($balls as $ball)
		{
			if(!in_array($ball, $this->balls[$i], true))
			{
				return false;
			}
		}
	
		return true;
	}
	
	private function getLimitStr($playType, $code)
	{
		$codeArr = explode(',', $code);
		
		foreach ($codeArr as &$c)
		{
			if ($playType <= 6)
			{
				$c = $this->showstr[0][$c];
			}
			else
			{
				$c = $this->showstr[$playType][$c];
			}
		}
		return implode(',', $codeArr);
	}
	
	private function getSinglePlayType($playType)
	{
		if (in_array($playType, array(21, 22, 31, 32, 41, 42, 51, 52, 61, 62))) return substr($playType, 0, 1);
		return $playType;
	}
	
}
