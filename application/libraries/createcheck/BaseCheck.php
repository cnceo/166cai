<?php
/**
 * 订单创建检查基类
 * @author shigx
 *
 */
class BaseCheck
{
	//必要参数
	private $mustParams = array('uid', 'ctype', 'userName', 'buyPlatform', 'codes', 'lid', 'money', 'multi', 'issue', 'playType', 'isChase', 'betTnum', 'orderType', 'endTime');
	//追号必要参数
	private $chaseMustPsrams = array('uid', 'codes', 'lid', 'money', 'playType', 'betTnum', 'isChase', 'totalIssue', 'setStatus', 'endTime');
	//正整数参数
	private $hemaiMustPsrams = array('uid', 'codes', 'lid', 'money', 'multi', 'playType', 'betTnum', 'endTime', 'issue', 'buyMoney', 'commissionRate', 'guaranteeAmount', 'openStatus', 'openEndtime');
	private $hemaiBuyPsrams = array('uid', 'buyMoney');
	private $integerParams = array('uid', 'money', 'multi', 'issue', 'betTnum', 'totalIssue', 'buyMoney');
	private $cParams;
	protected $CI;
	protected $_lotteryConfig;
	protected $limitcodes;
	//订单票张数
	protected $ticketCount = 0;
	public $params = array();
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->driver('cache', array('adapter' => 'redis'));
		$this->REDIS = $this->CI->config->item('REDIS');
		$this->_lotteryConfig = json_decode($this->CI->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
	}
	
	/**
	 * 检查创建订单参数是否正确
	 * @param array $params 
	 */
	public function checkParams()
	{
		$check = true;
		foreach ($this->cParams as $val)
		{
			if(!isset($this->params[$val]) || $this->params[$val] === '')
			{
				//检查必要参数是否设置
				$check = false;
				break;
			}
			
			if(in_array($val, $this->integerParams) && !(preg_match("/^[1-9]\d*$/", $this->params[$val])))
			{
				//检查必须为正整数的参数
				$check = false;
				break;
			}
		}
		
		if(!empty($this->params['setMoney']))
		{	
			if((!(preg_match("/^[1-9]\d*$/", $this->params['setMoney']))) || ($this->params['setMoney'] > 100000))
			{
				$check = false;
			}
		}
		if ($this->params['orderType'] == 4 && empty($this->params['type'])) {
			if ($this->params['buyMoney'] * 100 < $this->params['money'] * ($this->params['commissionRate'] <= 5 ? 5 : $this->params['commissionRate'])) {
				return array('status' => false, 'msg' => '认购金额错误');
			}
			if(preg_match("/\D/", $this->params['guaranteeAmount']))
			{
				return array('status' => false, 'msg' => '请求参数错误');
			}
			if ($this->params['buyMoney'] + $this->params['guaranteeAmount'] > $this->params['money']) {
				return array('status' => false, 'msg' => '认购金额加保底金额不能大于订单总额');
			}
			if (!in_array($this->params['commissionRate'], array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10))) {
				return array('status' => false, 'msg' => '盈利佣金出错！');
			}
			if (!in_array($this->params['openStatus'], array(0, 1, 2))) {
				return array('status' => false, 'msg' => '保密设置出错！');
			}
		}
		
		if($check == true)
		{
			$result = array(
				'status' => true,
				'msg' => '参数正确',
			);
		}
		else
		{
			$result = array(
				'status' => false,
				'msg' => '请求参数错误',
			);
		}
		
		return $result;
	}
	
	/**
	 * 检查金额最大值 竞技彩需要重载该方法
	 */
	public function checkMaxMoney()
	{
		if($this->params['money'] > 20000)
		{
			$result = array(
				'status' => false,
				'msg' => "订单金额需小于2万，请修改订单后重新投注",
			);
		}
		else
		{
			$result = array(
				'status' => true,
				'msg' => '',
			);
		}
		
		return $result;
	}
	
	/**
	 * 检查彩种销售状态
	 * @return array()
	 */
	public function checkSale()
	{
		$result = array(
			'status' => true,
			'msg' => '当前彩种正常销售',
		);
		if(empty($this->_lotteryConfig[$this->params['lid']]['status']))
		{
			$result = array(
				'status' => false,
				'msg' => '当前彩种停止销售',
			);
		}
		
		return $result;
	}
	
	/**
	 * 检查期次是否正确
	 */
	public function checkIssue()
	{
		$this->CI->load->model('lottery_model');
		$cacheName = $this->CI->lottery_model->getCache($this->params['lid']);
		$cache = json_decode($this->CI->cache->get($this->REDIS[$cacheName]), true);
		if(empty($cache['cIssue']) 
				|| ($this->params['issue'] != $cache['cIssue']['seExpect']) 
				|| ($cache['cIssue']['seFsendtime']/1000 != strtotime($this->params['endTime']) && in_array($this->params['orderType'], array(0, 1)))
				|| ($this->params['orderType'] == 4 
						&& ($cache['cIssue']['seFsendtime']/1000-$this->_lotteryConfig[$this->params['lid']]['united_ahead']*60 != strtotime($this->params['endTime'])
							|| $cache['cIssue']['seEndtime']/1000 != strtotime($this->params['openEndtime'])))
			)
		{
			$result = array(
				'status' => false,
				'msg' => '投注期次错误', 
			);
			return $result;
		}
		if(($cache['cIssue']['seFsendtime']/1000 < time() && in_array($this->params['orderType'], array(0, 1)))
		|| ($cache['cIssue']['seFsendtime']/1000-$this->_lotteryConfig[$this->params['lid']]['united_ahead']*60 < time() && $this->params['orderType'] == 4))
		{
			$result = array(
				'status' => false,
				'msg' => '期次已过投注时间',
			);
			return $result;
		}
		
		if ($this->params['singleFlag']) return $this->sigleEndtime($this->params['lid'], $this->params['endTime'], $this->params['betTnum']);
		
		$result = array(
			'status' => true,
			'msg' => '期次正确',
		);
		return $result;
	}
	
	/**
	 * 追号期次信息检查
	 * @return multitype:boolean string
	 */
	public function checkChaseIssue()
	{
		$lidMap = array(
			'21406' => array('name' => 'SYXW', 'maxIssue' => 174, 'format' => ''),
			'21407' => array('name' => 'JXSYXW', 'maxIssue' => 168, 'format' => ''), 
			'53' => array('name' => 'KS', 'maxIssue' => 164, 'format' => ''),
			'54' => array('name' => 'KLPK', 'maxIssue' => 176, 'format' => ''),
			'51' => array('name' => 'SSQ', 'maxIssue' => 50, 'format' => ''),
			'23529' => array('name' => 'DLT', 'maxIssue' => 50, 'format' => '20'),
			'23528' => array('name' => 'QLC', 'maxIssue' => 50, 'format' => ''),
			'35' => array('name' => 'PLW', 'maxIssue' => 50, 'format' => '20'),
			'10022' => array('name' => 'QXC', 'maxIssue' => 50, 'format' => '20'),
			'33' => array('name' => 'PLS', 'maxIssue' => 50, 'format' => '20'),
			'52' => array('name' => 'FCSD', 'maxIssue' => 50, 'format' => ''),
			'21408' => array('name' => 'HBSYXW', 'maxIssue' => 168, 'format' => ''),
			'55' => array('name' => 'CQSSC', 'maxIssue' => 240, 'format' => ''),
			'56' => array('name' => 'JLKS', 'maxIssue' => 87, 'format' => ''),
		    '57' => array('name' => 'JXKS', 'maxIssue' => 88, 'format' => ''),
		    '21421' => array('name' => 'GDSYXW', 'maxIssue' => 168, 'format' => ''),
		);
		$followIssues = json_decode ($this->CI->cache->hGet($this->REDIS['ISSUE_COMING'], $lidMap[$this->params['lid']]['name']), true);
		$chaseDetail = json_decode($this->params['chaseDetail'], TRUE);
		if(empty($chaseDetail) || (count($chaseDetail) != $this->params['totalIssue']) || ($this->params['totalIssue'] > $lidMap[$this->params['lid']]['maxIssue']))
		{
			$result = array(
				'status' => false,
				'msg' => '追号期次校验错误',
			);
			return $result;
		}
		
		$fData = array();
		foreach ($followIssues as $val)
		{
			$fData[$lidMap[$this->params['lid']]['format'].$val['issue']] = $val;
		}
		$preMoney = 0;
		$price = ($this->params['isChase'] > 0) ? 3 : 2;
		$endTimes = array();
		foreach ($chaseDetail as $key => $issue)
		{
			$endTimes[] = $issue['endTime'];
			//对老11选5单独处理
			if(isset($this->params['totalMoney']) && isset($this->params['way']))
			{
				$money = $issue['multi']*$this->params['totalMoney'];
			}else{
				$money =  $this->params['betTnum']* $issue['multi'] * $price;
			}
			if((strtotime($issue['endTime']) <= time())
				|| (empty($fData[$issue['issue']])) 
				|| ($fData[$issue['issue']]['show_end_time'] != $issue['endTime']) 
				|| ($fData[$issue['issue']]['award_time'] != $issue['award_time'])
				|| ($money != $issue['money']) || ($money == 0) 
				|| (!preg_match("/^[1-9]\d*$/", $issue['multi']))
				|| (!preg_match("/^[1-9]\d*$/", $issue['money'])))
			{
				$result = array(
						'status' => false,
						'msg' => '追号期次校验错误',
				);
				return $result;
			}
			if($money > 20000)
			{
				$result = array(
					'status' => FALSE,
					'msg' => "单期订单金额需小于2万，请修改订单后重新投注",
				);
				return $result;
			}
			
			$preMoney += $issue['money'];
		}
		$minEndTime = min($endTimes);
		if($minEndTime != $this->params['endTime'])
		{
			$result = array(
					'status' => FALSE,
					'msg' => '追号期次校验错误',
			);
			return $result;
		}
		if($preMoney != $this->params['money'])
		{
			$result = array(
					'status' => FALSE,
					'msg' => '追号方案金额错误',
			);
			return $result;
		}
		$result = array(
				'status' => true,
				'msg' => '期次正确',
		);
		return $result;
	}
	
	//开奖日检查 
	public function checkAissue()
	{
		$this->CI->load->model('lottery_model');
		$cacheName = $this->CI->lottery_model->getCache($this->params['lid']);
		$cache = json_decode($this->CI->cache->get($this->REDIS[$cacheName]), true);
		if (!empty($cache['aIssue']) && (time() > (floor($cache['aIssue']['seEndtime']/1000)-$this->_lotteryConfig[$this->params['lid']]['ahead']*60)) && (time() < floor($cache['aIssue']['seEndtime']/1000)))
		{
			$result = array(
				'status' => FALSE,
				'msg' => "对不起，您购买的彩种已过当期投注截止时间，下一期开售时间为".date('H:i', (floor($cache['aIssue']['seEndtime']/1000)))."！",
			);
			return $result;
		}
		
		$result = array(
			'status' => true,
			'msg' => '期次正确',
		);
		return $result;
	}
	
	/**
	 * 追加字段限制   针对大乐透彩种要重构该方法
	 */
	public function checkIsChase()
	{
		if($this->params['isChase'])
		{
			$result = array(
				'status' => false,
				'msg' => '请求参数错误',
			);
			return $result;
		}
		
		$result = array(
			'status' => true,
		);
		return $result;
	}
	
	/**
	 * 判断数组中是否有重复值   true 有   false 无
	 * @param unknown_type $params
	 * @return boolean
	 */
	public function isValueRepeat($params = array())
	{
		$count = count($params);
		$unique = count(array_unique($params));
		if($count == $unique)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 设置参数
	 * @param unknown_type $params
	 * @param int $chaseFlag  追号标识
	 */
	public function setParams($params = array(), $flag = 0)
	{
		$this->params = $params;
		//如果是追号 验证追号字段
		switch ($flag) {
			case 1:
				$this->cParams = $this->chaseMustPsrams;
				break;
			case 4:
				$this->cParams = $this->hemaiMustPsrams;
				break;
			case 41:
				$this->cParams = $this->hemaiBuyPsrams;
				break;
			default:
				$this->cParams = $this->mustParams;
				break;
		}
	}
	
	/**
	 * 校验投注截止时间
	 */
	public function checkEndTime()
	{
		if(strtotime($this->params['endTime']) < time())
		{
			return array('status' => false, 'code' => '600', 'msg' => '此彩种已过投注结束时间！');
		}		
	
		return array('status' => true, 'msg' => '');
	}
	
	public function sigleEndtime($lid, $endTime, $betNum) {
		$lidArray = array(FCSD, SFC, RJ);
		$configItem = json_decode($this->_lotteryConfig[$lid]['order_limit'], true);
		if (strtotime($endTime) - 5 * 60 < time() && $betNum > $y = in_array($lid,$lidArray) ? $configItem[0]['value'] : $configItem[0]['value'] * 5) {
			return array('status' => false, 'code' => '998', 'msg' => '离截止不到5分钟，为及时出票请确保方案不超过'.$y.'注！');
		}elseif (strtotime($endTime) - 15 * 60 < time() && $betNum > $y = in_array($lid,$lidArray) ? $configItem[1]['value'] : $configItem[1]['value'] * 5) {
			return array('status' => false, 'code' => '998', 'msg' => '离截止不到15分钟，为及时出票请确保方案不超过'.$y.'注！');
		}elseif (strtotime($endTime) - 45 * 60 < time() && $betNum > $y = in_array($lid,$lidArray) ? $configItem[2]['value'] : $configItem[2]['value'] *5) {
			return array('status' => false, 'code' => '998', 'msg' => '离截止不到45分钟，为及时出票请确保方案不超过'.$y.'注！');
		}
		return array('status' => true, 'msg' => '');
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
				if (strpos($limitcode, '|') !== false)
				{
					$codesArr = explode('|', $codes);
					foreach (explode('|', $limitcode) as $k => $lc)
					{
						if (strpos($codesArr[$k], $lc) === false) 
						{
							$flag = false;
							break;
						}
					}
				}
				elseif (strpos($codes, '$') !== false)
				{
					$limitArr = explode(',', $limitcode);
					$codesArr = explode('$', $codes);
					$danArr = explode(',', $codesArr[0]);
					$tuoArr = explode(',', $codesArr[1]);
					$lastArr = array_diff($limitArr, $danArr);
					if (array_values(array_intersect($limitArr, $danArr)) != $danArr) $flag = false;
					if (array_values(array_intersect($tuoArr, $lastArr)) != array_values($lastArr)) $flag = false;
				}
				else 
				{
					$codeArr = preg_split('/\$|\,/', $codes);
					$limitArr = explode(',', $limitcode);
					if (array_values(array_intersect($codeArr, $limitArr)) != $limitArr) $flag = false;
				}
				if ($flag) array_push($limitcodeArr, $limitcode);
			}
		}
		if ($limitcodeArr) return array('status' => true, 'limitcode' => $limitcodeArr);
		return array('status' => false, 'limitcode' => '');
	}
	
	/**
	 * 校验彩种票张数限制
	 */
	protected function checkOrderLimit()
	{
		if(isset($this->params['singleFlag']) && $this->params['singleFlag']==1)
		{
			$result = array(
				'status' => true,
				'msg' => '校验通过',
			);
			return $result;
		}
		$orderLimit = json_decode($this->_lotteryConfig[$this->params['lid']]['order_limit'], true);
		$checkTime = strtotime($this->params['endTime']) - time();
		//第一档校验
		if(($checkTime < $orderLimit['0']['time']) && ($this->ticketCount > $orderLimit['0']['value']))
		{
			return array('status' => false, 'msg' => "离截止不到". ($orderLimit['0']['time']/60) . "分钟，为及时出票请确保方案不超过{$orderLimit['0']['value']}注！");
		}
		
		//第二档校验
		if(($checkTime > $orderLimit['0']['time']) && ($checkTime < $orderLimit['1']['time']) && ($this->ticketCount > $orderLimit['1']['value']))
		{
			return array('status' => false, 'msg' => "离截止不到". ($orderLimit['1']['time']/60) . "分钟，为及时出票请确保方案不超过{$orderLimit['1']['value']}注！");
		}
		
		//第三档校验
		if(($checkTime > $orderLimit['1']['time']) && ($checkTime < $orderLimit['2']['time']) && ($this->ticketCount > $orderLimit['2']['value']))
		{
			return array('status' => false, 'msg' => "离截止不到". ($orderLimit['2']['time']/60) . "分钟，为及时出票请确保方案不超过{$orderLimit['2']['value']}注！");
		}
		
		$result = array(
			'status' => true,
			'msg' => '校验通过',
		);
		return $result;
	}
}
