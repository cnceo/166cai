<?php
/**
 * 竞彩篮球订单创建检查类
 * @author shigx
 *
 */
require_once dirname(__FILE__) . '/BaseCheck.php';
class JclqCheck extends BaseCheck
{
	private $playTypes = array('SF', 'RFSF', 'DXF', 'SFC');
	private $playOptions = array(
		'SF' => array('3', '0'),
		'RFSF' => array('3', '0'),
		'DXF' => array('3', '0'),
		'SFC' => array('01', '02', '03', '04', '05', '06', '11', '12', '13', '14', '15', '16'),
	);
	private $ggTypes = array('1*1', '2*1', '3*1', '4*1', '5*1', '6*1', '7*1', '8*1');
	//混合玩法串关定义
	private $hhGgTypes = array('1*1', '2*1', '3*1', '4*1', '5*1', '6*1', '7*1', '8*1', '3*3', '3*4', '4*4', '4*5', '4*6', '4*11', '5*5', '5*6', '5*10', '5*16', '5*20', '5*26', '6*6', '6*7', '6*15', '6*20', '6*22', '6*35', '6*42', '6*50', '6*57', '7*7', '7*8', '7*21', '7*35', '7*120', '8*8', '8*9', '8*28', '8*56', '8*70', '8*247');
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
		
		if ($params['orderType'] == 4 && $params['type'] == 1)
		{
			$this->setParams($params, 41);
			$checkMethods = array('checkParams');
		}else {
			$this->setParams($params, $params['orderType']);
			//需要检查的方法
			$checkMethods = array('checkParams', 'checkEndTime', 'checkCodecc', 'checkIsChase', 'checkMaxMoney', 'checkSale', 'checkCodes', 'checkOrderLimit');
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
		if ($this->params['playType'] == '7')
		{
			$result = $this->checkPlayYh($this->params['codes']);
		}
		else
		{
			$result = $this->checkPlayHh($this->params['codes']);
		}
			
		if($result['status'] == true)
		{
			if($result['betNum'] != $this->params['betTnum'])
			{
				return array('status' => false, 'msg' => '投注串校验错误');
			}
			if($this->params['money'] != ($result['money'] * $this->params['multi']))
			{
				return array('status' => false, 'msg' => '订单校验错误');
			}
				
			return array('status' => true, 'msg' => '');
		}
		
		return array('status' => false, 'msg' => isset($result['msg']) ? $result['msg'] : '投注串校验错误');
	}
	
	/**
	 * 优化串校验
	 * @param unknown_type $betstr
	 * @return multitype:boolean |multitype:boolean number
	 */
	private function checkPlayYh($betstr)
	{
		$betstrs = explode(';', $betstr);
		$codecc = explode(' ', $this->params['codecc']);
		$rebetnum = 0;
		$money = 0;
		$matches = array();
		$ggtypes = array();
		$isgg4 = false;
		$rule = '/([\d\-\:]*)(?:\{([\+\-]?\d+\.?\d*)\})?\(?(\d+\.?\d*)?\)?(?:\{([\+\-]?\d+\.?\d*)\})?/is';
		foreach ($betstrs as $key => $code)
		{
			$codes = explode('|', $code);
			if(($codes[0] != 'HH') || (empty($codes[1])) || (!preg_match("/^[1-9]\d*$/", $codes[2])) || (!in_array($codes[3], $this->ggTypes)))
			{
				return array('status' => false);
			}
			//优化单如果有一单是单关玩法其它单应该都是单关
			$isSingle = $codes[3] == '1*1' ? true : false;
			$matcharr = explode(',', $codes['1']);
			foreach ($matcharr as $match)
			{
				preg_match('/(.*?)>(.*?)=(.*)/i', $match, $map);
				if(empty($map) || (!in_array($map[1], $this->playTypes)) || (!in_array($map[2], $codecc)) || empty($map[3]))
				{
					return array('status' => false);
				}
				
				$isgg4 = $map[1] == 'SFC' ? true : $isgg4;
				preg_match($rule, $map[3], $oMap);
				if(empty($oMap) || (!in_array($oMap[1], $this->playOptions[$map[1]], true)) || ($map[1] == 'RFSF' && empty($oMap[2])) || empty($oMap[3]))
				{
					return array('status' => false);
				}
				$matches[$map[2]][$map[1]] = !isset($matches[$map[2]][$map[1]]) ? $map[1] : $matches[$map[2]][$map[1]];
				$playTypes[] = $map[1];
			}
			$money += $codes['2'] * 2;
			$rebetnum ++;
			$ggtypes[] = $codes[3];
			//计算票张数
			$this->ticketCount += 1;
		}
		$ggtypes = array_unique($ggtypes);
		if((!$this->checkSubset($ggtypes, $this->ggTypes)) || (in_array('1*1', $ggtypes) && count($ggtypes) != 1))
		{
			return array('status' => false);
		}
		
		foreach ($ggtypes as $ggtype)
		{
			$ggnum = substr($ggtype, 0, 1);
			if(($isgg4 && $ggtype > 4))
			{
				return array('status' => false);
			}
		}
		
		$result = $this->checkMatchs($matches, $isSingle);
		if($result['status'] == false)
		{
			return $result;
		}
		
		return array('status' => true, 'betNum' => $rebetnum, 'money' => $money);
	}
	
	/**
	 * 混合玩法投注串检查
	 * @param string $betstr
	 */
	private function checkPlayHh($betstr)
	{
		$betarr = explode('|', $betstr);
		if(($betarr[0] != 'HH') || empty($betarr[1]) || empty($betarr[2]))
		{
			return array('status' => false);
		}
		//过关方式校验
		$ggtypes = explode(',', $betarr[2]);
		if((!$this->checkSubset($ggtypes, $this->hhGgTypes)) || ($this->isValueRepeat($ggtypes)) ||(in_array('1*1', $ggtypes) && count($ggtypes) != 1))
		{
			return array('status' => false);
		}
		$matcharr = explode(',', $betarr[1]);
		$codecc = explode(' ', $this->params['codecc']);
		$matches = array();
		$matchCount = array();
		$playTypes = array();
		$rule = '/([\d\-\:]*)(?:\{([\+\-]?\d+\.?\d*)\})?\(?(\d+\.?\d*)?\)?(?:\{([\+\-]?\d+\.?\d*)\})?/is';
		foreach ($matcharr as $match)
		{
			preg_match('/(.*?)>(.*?)=(.*)/i', $match, $map);
			if(empty($map) || (!in_array($map[1], $this->playTypes)) || (!in_array($map[2], $codecc)) || empty($map[3]))
			{
				return array('status' => false);
			}
			$vTmp = explode('/', $map[3]);
			$pTmp = array();
			foreach ($vTmp as $val)
			{
				preg_match($rule, $val, $oMap);
				if(empty($oMap) || (!in_array($oMap[1], $this->playOptions[$map[1]], true)) || ($map[1] == 'RFSF' && empty($oMap[2])) || empty($oMap[3]))
				{
					return array('status' => false);
				}
				$pTmp[] = $oMap[1];
			}
			if($this->isValueRepeat($pTmp))
			{
				return array('status' => false);
			}
			$matches[$map[2]][$map[1]] = !isset($matches[$map[2]][$map[1]]) ? $map[1] : $matches[$map[2]][$map[1]];
			$matchCount[$map[2]][$map[1]] = count($vTmp);
			$playTypes[] = $map[1];
		}
		$isSingle = $betarr[2] == '1*1' ? true : false;
		$result = $this->checkMatchs($matches, $isSingle);
		if($result['status'] == false)
		{
			return $result;
		}
		$matchnums = array_keys($matches);
		$isgg4 = in_array('SFC', $playTypes) ? true : false;
		$rebetnum = 0;
		foreach ($ggtypes as $ggtype)
		{
			$ggnum = substr($ggtype, 0, 1);
			if(($isgg4 && $ggtype > 4))
			{
				return array('status' => false);
			}
			$matchcom = $this->CI->libcomm->combineList($matchnums, intval($ggnum)); //先场次组合
			foreach ($matchcom as $matchone)
			{
				$at = array_values($matchone);
				$ah = array_shift($at);
				$mcoms = $this->CI->libcomm->jjcRecursive($matchCount, $ah, $at); //玩法组合
				foreach ($mcoms as $selected)
				{
					$selecteds = explode(',', $selected);
					$betnum = $this->CI->libcomm->calBetNum($ggtype, $selecteds);
					//容错过关方式单张票超10000注校验
					if((!in_array($ggtype, $this->ggTypes)) && ($betnum > 10000))
					{
						return array('status' => false, 'msg' => '订单中单张彩票超过10000注，请修改订单后重新投注');
					}
					$rebetnum += $betnum;
					//计算票张数
					$this->ticketCount += 1;
				}
			}
		}
		
		return array('status' => true, 'betNum' => $rebetnum, 'money' => $rebetnum * 2);
	}
	
	/**
	 * 验证选择场次是否支持投注
	 * @param unknown_type $matchs
	 * @param unknown_type $isSingle
	 * @return boolean
	 */
	private function checkMatchs($matchs = array(), $isSingle = false)
	{
		$REDIS = $this->CI->config->item('REDIS');
		$matchCache = json_decode($this->CI->cache->redis->get($REDIS['JCLQ_MATCH']), TRUE);
		if(empty($matchs))
		{
			return array('status' => false, 'msg' => '投注串校验错误！');
		}
		
		$endTimes = array();
		foreach ($matchs as $mid => $match)
		{
			if(!isset($matchCache[$mid]))
			{
				return false;
			}
			foreach ($match as $playType)
			{
				$playType = strtolower($playType);
				$play = $playType . 'Gd';
				$single = $playType . 'Fu';
				if(empty($matchCache[$mid][$play]) || ($isSingle && empty($matchCache[$mid][$single])))
				{
					return array('status' => false, 'msg' => '选择的场次已停售，刷新页面后重新选择！');
				}
			}
			$endTimes[] = date('Y-m-d H:i:s', $matchCache[$mid]['jzdt'] / 1000);
		}
		
                $minEndTime = min($endTimes);
                if(($this->params['orderType'] == 4 && strtotime($minEndTime) - $this->_lotteryConfig[JCLQ]['united_ahead'] * 60 != strtotime($this->params['endTime'])) 
                                || (in_array($this->params['orderType'], array(0, 1)) && $minEndTime != $this->params['endTime'])) 
                                        return array('status' => false, 'msg' => '投注串校验错误！');

                if ($this->params['orderType'] == 4) {
                        $maxEndTime = max($endTimes);
                        if (strtotime($maxEndTime) + $this->_lotteryConfig[JCLQ]['ahead'] * 60 != strtotime($this->params['openEndtime'])) return array('status' => false, 'msg' => '投注串校验错误！');
                }
		
		return array('status' => true, 'msg' => '');
	}
	
	/**
	 *  判断数组1中的值是否都存在于数组2
	 * @param array() $arr1
	 * @param array() $arr2
	 * @return boolean
	 */
	private function checkSubset ($arr1 = array(), $arr2 = array())
	{
		foreach ($arr1 as $val)
		{
			if(!in_array($val, $arr2, true))
			{
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 订单总额校验
	 * @see BaseCheck::checkMaxMoney()
	 */
	public function checkMaxMoney()
	{
		if($this->params['money'] > 200000)
		{
			$result = array(
				'status' => false,
				'msg' => "订单金额需小于20万，请修改订单后重新投注",
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
	 * 校验codecc字段是否有值
	 * @return multitype:boolean string
	 */
	public function checkCodecc()
	{
		$codecc = $this->params['codecc'] ? explode(' ', $this->params['codecc']) : array();
		if(empty($codecc) || $this->isValueRepeat($codecc))
		{
			return array('status' => false, 'msg' => "投注串校验错误");
		}
		
		return array('status' => true, 'msg' => '');
	}
	
	/**
	 * 校验投注截止时间
	 */
	public function checkEndTime()
	{
		if(strtotime($this->params['endTime']) < time())
		{
			return array('status' => false, 'code' => '600', 'msg' => '投注场次已过截止时间！');
		}
	
		return array('status' => true, 'msg' => '');
	}
}
