<?php

/**
 * 老时时彩订单创建检查类
 */
require_once APPPATH . '/libraries/createcheck/BaseCheck.php';
class CqsscCheck extends BaseCheck
{
	// 玩法定义
	private $playTypes = array(
		'1'  =>	array(
			'split' => 	2,		// 分隔位数
			'dfs'	=>	0,		// 是否单复式
			'cname' => '大小单双',
		), 
		'10' => array(
			'split' => 	1,		
			'dfs'	=>	1,
			'cname' => '一星直选',
		), 
		'20' => array(
			'split' => 	2,		
			'dfs'	=>	0,
			'cname' => '二星直选',
		), 
		'21' => array(
			'split' => 	2,		
			'dfs'	=>	1,
			'cname' => '二星直选',
		), 
		'23' => array(
			'split' => 	2,		
			'dfs'	=>	0,
			'cname' => '二星组选',
		), 
		'27' => array(
			'split' => 	2,		
			'dfs'	=>	0,
			'cname' => '二星组选',
		), 
		'30' => array(
			'split' => 	3,		
			'dfs'	=>	0,
			'cname' => '三星直选',
		), 
		'31' => array(
			'split' => 	3,		
			'dfs'	=>	1,
			'cname' => '三星直选',
		), 
		'33' => array(
			'split' => 	3,		
			'dfs'	=>	0,
			'cname' => '三星组三',
		), 
		'34' => array(
			'split' => 	3,		
			'dfs'	=>	0,
			'cname' => '三星组六',
		), 
		'37' => array(
			'split' => 	1,		
			'dfs'	=>	1,
			'cname' => '三星组三',
		), 
		'38' => array(
			'split' => 	3,		
			'dfs'	=>	0,
			'cname' => '三星组六',
		), 
		'40' => array(
			'split' => 	5,		
			'dfs'	=>	0,
			'cname' => '五星直选',
		), 
		'41' => array(
			'split' => 	5,		
			'dfs'	=>	1,
			'cname' => '五星直选',
		), 
		'43' => array(
			'split' => 	5,		
			'dfs'	=>	1,
			'cname' => '五星通选',
		)
	);

	// 大小单双号码值定义
	private $dxds = array('1', '2', '4', '5');
	
	//和值数值定义
	private $chase = 0;
	
	public function __construct()
	{
		parent::__construct();
		$this->CI->load->library(array('libcomm'));
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
			$checkMethods = array('checkParams', 'checkIsChase', 'checkSale', 'checkFobbidChaseIssue', 'checkChaseIssue', 'checkCodes', 'checkOrderLimit');
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
	
	public function checkFobbidChaseIssue() {
		$chaseDetail = json_decode($this->params['chaseDetail'], TRUE);
		foreach ($chaseDetail as $chase) {
			if ($chase['issue'] >= 180201120 && $chase['issue'] <= 180202023) return array('status' => false, 'code' => 405, 'msg' => '0201120至0202023期系统维护，请减少追号期次');
		}
		return array('status' => true, 'msg' => '');
	}
	
	// 投注串检查
	public function checkCodes()
	{
		$codes_arr = explode(';', $this->params['codes']);
		$rebetnum = 0;
		$check = true;
		$checklimit = true;
		$limitmsg = array();
		foreach ($codes_arr as $code)
		{
			// 玩法检查
			$codes = explode(':', $code);
			if($codes[0] === '' || (!in_array($codes['1'], array_keys($this->playTypes))) || $codes['2'] != 1)
			{
				$check = false;
				break;
			}

			// 串格式检查
			$result = $this->checkCodesFormat($codes[0], $codes[1]);
			if($result['status'] == false)
			{
				$check = false;
				break;
			}	
			
			$limitres = $this->checkLimitCode($codes[0], $codes[1]);
			if ($limitres['status'])
			{
				$checklimit = false;
				foreach ($limitres['limitcode'] as $lcode)
				{
					if ($codes[1] == 1) $lcode = str_replace(array(1, 2, 4, 5), array('大', '小', '单', '双'), $lcode);
					if (!in_array($this->playTypes[$codes[1]]['cname'].$lcode, $limitmsg)) array_push($limitmsg, $this->playTypes[$codes[1]]['cname'].$lcode);
				}
			}
			
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
				if($this->params['money'] != ($rebetnum * $this->params['multi'] * 2))
				{
					return array('status' => false, 'code' => 403, 'msg' => '订单校验错误');
				}
			}
			
			//计算票张数
			$this->ticketCount = count($codes_arr);

			return array('status' => true, 'msg' => '');
		}
	}

	private function checkCodesFormat($codes, $playType)
	{
		// 玩法检查配置信息
		$config = $this->playTypes[$playType];
		if(empty($config))
		{
			return array('status' => false);
		}

		$codesArr = explode(',', $codes);
		if(in_array($playType, array(23, 27, 37, 34, 38)))
		{
			if(count($codesArr) <= 0 || count($codesArr) != count(array_unique($codesArr)))
			{
				return array('status' => false);
			}
		}
		elseif(in_array($playType, array(33)) && count(array_unique($codesArr)) != 2)
		{
			return array('status' => false);
		}

		// 分隔位数检查
		if(in_array($playType, array(27, 37, 38)))
		{
			// 组选玩法判断必须的最小位数
			if(count($codesArr) <= $config['split'])
			{
				return array('status' => false);
			}
		}
		else
		{
			if(count($codesArr) != $config['split'])
			{
				return array('status' => false);
			}
		}

		// 二星组选复式、三星组三复式、三星组六复式顺序检查
		if(in_array($playType, array(23, 27, 37, 34, 38)))
		{
			$tplArr = $codesArr;
			$c1 = implode('', $tplArr);
			asort($tplArr);
			$c2 = implode('', $tplArr);
			if($c1 != $c2)
			{
				return array('status' => false);
			}
		}
		
		foreach ($codesArr as $nums) 
		{
			if(!$this->checkIsNum($nums, $config['dfs']))
			{
				return array('status' => false);
			}
			if($playType == 1 && !in_array($nums, $this->dxds))
			{
				return array('status' => false);
			}
			// 一星直选、二星直选、三星直选、五星直选、五星通选 顺序检查
			if(in_array($playType, array(10, 21, 31, 41, 43)))
			{
				$numsTpl = str_split($nums);
				if(count($numsTpl) > 1)
				{
					asort($numsTpl);
					$cnums = implode('', $numsTpl);
					if($cnums != $nums)
					{
						return array('status' => false);
					}
				}
			}
		}

		// 一星直选 五星通选 仅支持单式
		if(in_array($playType, array('10', '43')))
		{
			// 拆单式方法
			$codeArr = $this->dismantle_sigle_codes($codes);
			foreach ($codeArr as $num) 
			{
				$ballArr = array(
    				'codes' => implode(',', $num),
    				'playtype' => $playType
    			);

    			// 根据号码玩法计算注数
				$betNums = $this->getCqsscBetNum($ballArr);				
				$betnum += $betNums;
			}
		}
		elseif(in_array($playType, array('40', '41')))
		{
			// 五星直选 注释可能超过一万注
			$results = $this->_dismantle_number($codes);
			$betnum += $results['betnum'];
		}
		else
		{
			// 根据号码玩法计算注数
			$ballArr = array(
				'codes' => $codes,
				'playtype' => $playType
			);
			$betNums = $this->getCqsscBetNum($ballArr); 				
			$betnum += $betNums;
		}
		if($config['dfs'] && $betnum <= 1 && !in_array($playType, array('10', '43')))
		{
			return array('status' => false);
		}
		return array('status' => true, 'betNum' => $betnum);	
	}

	// 数字检查
	private function checkIsNum($num, $isDfs)
	{
		// 0 - 9
		if($isDfs)
		{
			if(!preg_match('/[0-9]{1,10}$/', $num))
			{
				return false;
			}
		}
		else
		{
			if(!preg_match('/[0-9]{1}$/', $num))
			{
				return false;
			}
		}
		
		// 不重复
		$numArr = str_split($num);
		if(count($numArr) <= 0 || count($numArr) != count(array_unique($numArr)))
		{
			return false;
		}
		return true;
	}

	private function getCqsscBetNum($ballArr, $check = false)
	{
		$betNum = 0;
		switch ($ballArr['playtype'])
		{
			case 10:
			case 43:
			case 31:
			case 41:
				$betNum = 1;
				$codesArr = explode(',', $ballArr['codes']);
				if(!empty($codesArr))
				{
					foreach ($codesArr as $code) 
					{
						// 拆分最小单位
						$betNum = $betNum * strlen($code);
					}
				}
				break;
			case 27:
				// 二星组选复式
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 2);
				break;
			case 37:
				// 三星组三复式
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 2);
				$betNum *= 2;
				break;
			case 38:
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 3);
				break;
			default:
				$results = $this->_dismantle_number($ballArr['codes'], $check);
				$betNum = $results['betnum'];
				break;
		}
		return $betNum;
	}

	private function dismantle_sigle_codes($codes)
    {
    	$codesData = array();
    	$codesArr = explode(',', $codes);
    	for ($i = 0; $i < count($codesArr); $i++) 
    	{ 
    		$splitArr = str_split($codesArr[$i], 1);
    		for ($j = 0; $j < count($splitArr); $j++) 
    		{ 
    			$codesData[$i][$j] = $splitArr[$j];
    		}
    	}
    	return $this->CI->libcomm->dismantleSigleCodes($codesData);
    }

    public function _dismantle_number($codestr, $check = false)
	{
		$codestrmulti = explode(';', $codestr);
		$allCodes = array();
		foreach ($codestrmulti as $codearrone)
		{
			$codearr = explode(':', $codearrone);
			$codestrs = explode(',', $codearr[0]);
			$codearrs = array();
			$sortarr = array();
			foreach ($codestrs as $in => $codestr)
			{
				$codearrs[$in] = str_split($codestr);
			}
			$this->dismantle_recursive_number($codearrs, $allCodes, $check);
		}
		$betnums = 0;
		foreach ($allCodes as $allCode)
		{
			$num = 1;
			foreach ($allCode as $bets)
			{
				$num *= count($bets);
			}
			$betnums += $num;
		}
		return array('playtype' => $codearr[1],'betnum' => $betnums, 'betcbt' => ($check ? array() : $allCodes));
	}

	private function dismantle_recursive_number($codearrs, &$allCodes, $check)
	{
		$betnums = 1;
		foreach ($codearrs as $in => $codearr)
		{
			$nums = count($codearr);
			$sortarr[$nums] = $in;
			$betnums *= $nums;
		}
		if(!$check)
		{
			if($betnums > 10000)
			{
				ksort($sortarr);
				$in = array_pop($sortarr);
				$splinum = $betnums / 10000;
				$betnums = $betnums / count($codearrs[$in]);
				$arrsize = floor(count($codearrs[$in]) / $splinum);
				$arrsize = $arrsize > 0 ? $arrsize : 1;
				$splitarrs = array_chunk($codearrs[$in], $arrsize);
				foreach ($splitarrs as $splitarr)
				{
					$codearrs[$in] = $splitarr;
					$betnums *= count($splitarr);
					if($betnums <= 10000)
					{
						array_push($allCodes, $codearrs);
					}
					else
					{
						$this->dismantle_recursive_number($codearrs, $allCodes, $check);
					}
				}
			}
			else
			{
				array_push($allCodes, $codearrs);
			}
		}
		else
		{
			array_push($allCodes, $codearrs);
		}
	}
	
	private function swithPlayType($playType) {
		$switchArr = array('21' => '20', '27' => '23', '31' => '30', '37' => '33', '38' => '34', '41' => '40');
		return array_key_exists($playType, $switchArr) ? $switchArr[$playType] : $playType;
	}
	
	protected function checkLimitCode($codes, $playType)
	{
		$codeArr = explode(',', $codes);
		$switchPlayType = $this->swithPlayType($playType);
		$limitcodes = $this->limitcodes[(int)$switchPlayType];
		$limitcodeArr = array();
		if (!empty($limitcodes))
		{
			foreach ($limitcodes as $limitcode)
			{
				$flag = true;
				$limitArr = explode(',', $limitcode);
				if (in_array($switchPlayType, array('1', '10', '20', '30', '40', '43'))) {
					foreach ($limitArr as $k => $limit) {
						if (strpos($codeArr[$k], $limit) === false) $flag = false;
					}
				} elseif ($playType == '33') {
					sort($codeArr);
					$flag = true;
					foreach ($codeArr as $k => $c) {
						if ($limitArr[$k] != $c) $flag = false;
					}
				} elseif (array_values(array_intersect($codeArr, array_unique($limitArr))) != array_values(array_unique($limitArr))) { 
					$flag = false;
				}
				if ($flag) array_push($limitcodeArr, $limitcode);
			}
		}
		if ($limitcodeArr) return array('status' => true, 'limitcode' => $limitcodeArr);
		return array('status' => false, 'limitcode' => '');
	}
}
