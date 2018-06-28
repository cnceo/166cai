<?php

class Cqssc
{
	private $CI;
	private $playType = array(
		'1'   =>  array(
			'playType'	=>	'dxds',		// 大小单双
			'bonusType'	=>	'dxds',
		),
        '10'  =>  array(
        	'playType'	=>	'1xzhix',	// 一星直选单式
        	'bonusType'	=>	'1xzhix',
        ),		
        '20'  =>  array(
        	'playType'	=>	'2xzhixds',	// 二星直选单式
        	'bonusType'	=>	'2xzhix',
        ),
        '21'  =>  array(
        	'playType'	=>	'2xzhixfs',	// 二星直选复式
        	'bonusType'	=>	'2xzhix',
        ),
        '23'  =>  array(
        	'playType'	=>	'2xzuxds',	// 二星组选单式
        	'bonusType'	=>	'2xzux',
        ),
        '27'  =>  array(
        	'playType'	=>	'2xzuxfs',	// 二星组选复式
        	'bonusType'	=>	'2xzux',
        ),
        '30'  =>  array(
        	'playType'	=>	'3xzhixds',	// 三星直选单式
        	'bonusType'	=>	'3xzhix',
        ), 
        '31'  =>  array(
        	'playType'	=>	'3xzhixfs',	// 三星直选复式
        	'bonusType'	=>	'3xzhix',
        ), 
        '33'  =>  array(
        	'playType'	=>	'3xzu3ds',	// 三星组三单式
        	'bonusType'	=>	'3xzu3',
        ), 
        '37'  =>  array(
        	'playType'	=>	'3xzu3fs',	// 三星组三复式
        	'bonusType'	=>	'3xzu3',
        ), 
        '34'  =>  array(
        	'playType'	=>	'3xzu6ds',	// 三星组六单式
        	'bonusType'	=>	'3xzu6',
        ), 
        '38'  =>  array(
        	'playType'	=>	'3xzu6fs',	// 三星组六复式
        	'bonusType'	=>	'3xzu6',
        ),
        '40'  =>  array(
        	'playType'	=>	'5xzhixds',	// 五星直选单式
        	'bonusType'	=>	'5xzhix',
        ),
        '41'  =>  array(
        	'playType'	=>	'5xzhixfs',	// 五星直选复式
        	'bonusType'	=>	'5xzhix',
        ),
        '43'  =>  array(
        	'playType'	=>	'5xtx',		// 五星通选
        	'bonusType'	=>	'5xtx',
        ),
        // '25'  =>  '2xhz',		// 二星和值
        // '26'  =>  '2xzuxhz',		// 二星组选和值
        // '35'  =>  '3xhz',		// 三星和值
        // '36'  =>  '3xzuxhz',		// 三星组选和值
	);

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('bonus_model');
		$this->CI->load->helper('string');
		$this->order_status = $this->CI->bonus_model->orderConfig('orders');
		$this->CI->load->library('libcomm');
	}
	
	/**
	 * 计算过关
	 */
	public function calculate($ctype = '')
	{
		$returnData = array(
			'currentFlag' => true,
			'triggerFlag' => false,
		);
		$ainfos = $this->CI->bonus_model->awardInfo(0, 55);

		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 55, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$fun = "cal_{$this->playType[$order['playType']]['playType']}";
						$bouns_detail = $this->$fun($order, $ainfo);
						$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data'], 55);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 55, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 55, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
					if($affectedRows)
					{
						$returnData['triggerFlag'] = true;
					}
				}
				$this->CI->bonus_model->trans_complete();
				if($returnData['currentFlag'] && $flag)
				{
					$returnData['currentFlag'] = false;
				}
			}
		}
		
		return $returnData;
	}
	
	/**
	 * 算奖
	 */
	public function bonus($ctype = '')
	{
		$returnData = array(
			'currentFlag' => true,
			'triggerFlag' => false,
		);
		$ainfos = $this->CI->bonus_model->awardInfo(1, 55);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 55, $this->order_status['split_ggwin']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns = $this->cal_bonus($order, $ainfo);
						$orders['data'][$in]['status'] = $this->order_status['win'];
						$orders['data'][$in]['bonus'] = $bouns['bonus'];
						$orders['data'][$in]['margin'] = $bouns['margin'];
                        $orders['data'][$in]['otherBonus'] = isset($bouns['otherBonus']) ? $bouns['otherBonus'] : 0;
					}
					$re = $this->CI->bonus_model->setBonus($orders['data'], 55);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 55, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 55, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
					if($affectedRows)
					{
						$returnData['triggerFlag'] = true;
					}
				}
				$this->CI->bonus_model->trans_complete();
				if($returnData['currentFlag'] && $flag)
				{
					$returnData['currentFlag'] = false;
				}
			}
		}
		
		return $returnData;
	}
	
	private function cal_bonus($order, $ainfo)
	{
		$abonus = json_decode($ainfo['bonusDetail'], true);
		$obonus = json_decode($order['bonus_detail'], true);
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
        $mbonus['otherBonus'] = 0;
		$playType = $this->playType[$order['playType']]['bonusType'];

		if(!empty($obonus))
		{
			foreach ($obonus as $bonusType => $betnum)
			{
				if($playType == '5xtx')
				{
					$dzjj = isset($abonus[$playType][$bonusType]) ? $abonus[$playType][$bonusType] : 0;
				}
				else
				{
					$dzjj = isset($abonus[$playType]) ? $abonus[$playType] : 0;
				}
				// 税前
				$mbonus['bonus'] += $betnum * $dzjj;
				// 税后
				$margin_dzjj = $dzjj >= 10000 ? $dzjj * 0.8 : $dzjj;
				$mbonus['margin'] += $betnum * $margin_dzjj;
			}
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = ParseUnit($mbonus['margin']) * $order['multi'];
        $mbonus['otherBonus'] = $mbonus['otherBonus'] * $order['multi'];
		return $mbonus;
	}
	
	private function check_is_win($details)
	{
		$status = $this->order_status['notwin'];
		foreach ($details as $num)
		{
			if($num > 0)
			{
				$status = $this->order_status['split_ggwin'];
				break;
			}
		}
		return $status;
	}

	// 大小单双
	public function cal_dxds($order, $ainfo)
	{
		// 开奖号码后两位 四种中奖形态
		$awardstrs = explode(',', $ainfo['awardNum']);
		$awardtypesArr = array(
			0	=>	$this->getDxdsType($awardstrs[3]),
			1 	=>	$this->getDxdsType($awardstrs[4]),
		);
		$matchArr = $this->CI->libcomm->dismantleSigleCodes($awardtypesArr);
		$awardtypes = array();
		foreach ($matchArr as $items) 
		{
			$awardtypes[] = implode(',', $items);
		}

		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			if(in_array($order['codes'], $awardtypes))
			{
				$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}

	// 大小单双形态
	private function getDxdsType($num)
	{
		$typeArr = array();
		// 大小
		if($num >= 5)
		{
			array_push($typeArr, 1);
		}
		else
		{
			array_push($typeArr, 2);
		}
		// 单双
		if($num % 2 == 0)
		{
			array_push($typeArr, 5);
		}
		else
		{
			array_push($typeArr, 4);
		}
		return $typeArr;
	}

	// 一星直选
	public function cal_1xzhix($order, $ainfo)
	{
		// 开奖号码个位
		$awardstrs = explode(',', $ainfo['awardNum']);

		$bonus_detail = array(0);
		if(isset($order['codes']))
		{
			if(trim($order['codes']) == trim($awardstrs[4]))
			{
				$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}

	// 二星直选单式 0,1
	public function cal_2xzhixds($order, $ainfo)
	{
		// 开奖号码后两位
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 二星直选复式 0,1,2
	public function cal_2xzhixfs($order, $ainfo)
	{
		// 开奖号码后两位
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 二星组选单式 0,1
	public function cal_2xzuxds($order, $ainfo)
	{
		// 开奖号码后两位 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_2xzux($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 二星组选复式 012,0189
	public function cal_2xzuxfs($order, $ainfo)
	{
		// 开奖号码后两位 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_2xzux($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 二星组选公共过关
	private function cal_com_2xzux($codes, $awardNum)
	{
		$awardstrs = array_map('trim', explode(',', $awardNum));
		$awardSum = count($awardstrs);

		$codestrs = array_map('trim', explode(',', $codes));
		$codeSum = count($codestrs);

		// 取后两位开奖号码
		$index = 3;
		$awardArr = array();
		for ($i = $index; $i < $awardSum; $i++) 
		{ 
			$awardArr[] = $awardstrs[$i];
		}

		$bonus_detail = array(0);
		if(!empty($awardArr))
		{
			if(count(array_unique($awardArr)) == 1)
			{
				// 对子必不中
				return $bonus_detail;
			}

			$c = 0;
			foreach ($awardArr as $num) 
			{
				// 不区分顺序
				if(in_array($num, $codestrs))
				{
					$c ++;
				}
			}

			if($c == 2)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}

	// 三星直选单式 1,0,1
	public function cal_3xzhixds($order, $ainfo)
	{
		// 开奖号码后三位
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 三星直选复式 01,0,123
	public function cal_3xzhixfs($order, $ainfo)
	{
		// 开奖号码后三位
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 直选公共过关
	private function cal_com_zhix($codes, $awardNum)
	{
		$awardstrs = array_map('trim', explode(',', $awardNum));
		$awardSum = count($awardstrs);

		$codestrs = array_map('trim', explode(',', $codes));
		$codeSum = count($codestrs);

		$index = $awardSum - $codeSum;
		$awardArr = array();
		for ($i = $index; $i < $awardSum; $i++) 
		{ 
			$awardArr[] = $awardstrs[$i];
		}

		$bonus_detail = array(0);
		if(!empty($awardArr))
		{
			$matchNums = 0;
			foreach ($awardArr as $key => $num) 
			{
				if(strpos($codestrs[$key], $num) !== FALSE)
				{
					$matchNums++;
				}
			}

			if($matchNums > 0 && $matchNums == $codeSum)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}

	// 三星组三单式 2,2,4
	public function cal_3xzu3ds($order, $ainfo)
	{
		// 开奖号码后三位任意两位号码相同 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zu3($order['codes'], $ainfo['awardNum'], 1);
		}
		return $bonus_detail;
	}

	// 三星组三复式 2,4,6,7
	public function cal_3xzu3fs($order, $ainfo)
	{
		// 开奖号码后三位任意两位号码相同 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zu3($order['codes'], $ainfo['awardNum'], 0);
		}
		return $bonus_detail;
	}

	// 组三公共过关
	private function cal_com_zu3($codes, $awardNum, $dfs = 1)
	{
		$awardstrs = array_map('trim', explode(',', $awardNum));
		$awardSum = count($awardstrs);

		$codestrs = array_map('trim', explode(',', $codes));

		$index = 2;
		$awardArr = array();
		for ($i = $index; $i < $awardSum; $i++) 
		{ 
			$awardArr[] = $awardstrs[$i];
		}

		// 开奖号码后三位任意两位号码相同 不区分顺序
		$aw = array_unique($awardArr);

		if(count($aw) == 2)
		{
			if($dfs)
			{
				// 单式判断
				sort($codestrs);
				sort($awardArr);
				if(implode('|', $codestrs) == implode('|', $awardArr))
				{
					$bonus_detail = array(1);
				}
				else
				{
					$bonus_detail = array(0);
				}
			}
			else
			{
				// 复式判断
				$result = array_intersect($aw, $codestrs);
				if(count($result) == 2)
				{
					$bonus_detail = array(1);
				}
				else
				{
					$bonus_detail = array(0);
				}
			}
		}
		else
		{
			$bonus_detail = array(0);
		}
		return $bonus_detail;
	}

	// 三星组六单式 0,1,2
	public function cal_3xzu6ds($order, $ainfo)
	{
		// 开奖号码后三位任意两位号码相同 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zu6($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 三星组六复式 0,1,2,3,4,5
	public function cal_3xzu6fs($order, $ainfo)
	{
		// 开奖号码后三位三个号码各不相同 不区分顺序
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zu6($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 组六公共过关
	private function cal_com_zu6($codes, $awardNum)
	{
		$awardstrs = array_map('trim', explode(',', $awardNum));
		$awardSum = count($awardstrs);

		$codestrs = array_map('trim', explode(',', $codes));

		$index = 2;
		$awardArr = array();
		for ($i = $index; $i < $awardSum; $i++) 
		{ 
			$awardArr[] = $awardstrs[$i];
		}

		// 开奖号码后三位三个号码各不相同 不区分顺序
		$aw = array_unique($awardArr);

		if(count($aw) == 3)
		{
			$result = array_intersect($aw, $codestrs);
			if(count($result) == 3)
			{
				$bonus_detail = array(1);
			}
			else
			{
				$bonus_detail = array(0);
			}
		}
		else
		{
			$bonus_detail = array(0);
		}
		return $bonus_detail;
	}

	// 五星直选单式 0,0,0,0,0
	public function cal_5xzhixds($order, $ainfo)
	{
		// 开奖号码完全按位全部相符
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 五星直选复式 123456789,123456789,123456789,123456789,123456789
	public function cal_5xzhixfs($order, $ainfo)
	{
		// 开奖号码完全按位全部相符
		$bonus_detail = array(0);
		if(!empty($order['codes']))
		{
			$bonus_detail = $this->cal_com_zhix($order['codes'], $ainfo['awardNum']);
		}
		return $bonus_detail;
	}

	// 五星通选
	public function cal_5xtx($order, $ainfo)
	{
		$bonus_detail = array('2w' => 0);

		$awardstrs = array_map('trim', explode(',', $ainfo['awardNum']));
		$codestrs = array_map('trim', explode(',', $order['codes']));
		
		// 全中
		$matchNums = 0;
		for ($i = 0; $i < 5; $i++) 
		{ 
			if($awardstrs[$i] == $codestrs[$i])
			{
				$matchNums++;
			}
		}
		
		if($matchNums == 5)
		{
			return $bonus_detail = array('qw' => 1);
		}

		// 前后三位中
		if( $awardstrs[0] == $codestrs[0] && $awardstrs[1] == $codestrs[1] && $awardstrs[2] == $codestrs[2] )
		{
			return $bonus_detail = array('3w' => 1);
		}

		if( $awardstrs[2] == $codestrs[2] && $awardstrs[3] == $codestrs[3] && $awardstrs[4] == $codestrs[4] )
		{
			return $bonus_detail = array('3w' => 1);
		}

		// 前后两位中
		if($awardstrs[0] == $codestrs[0] && $awardstrs[1] == $codestrs[1])
		{
			$bonus_detail['2w']++;
		}

		if($awardstrs[3] == $codestrs[3] && $awardstrs[4] == $codestrs[4])
		{
			$bonus_detail['2w']++;
		}
		return $bonus_detail;
	}
	
	//限号投注串过关
	public function caculatelimit($playType, $code, $award)
	{
		$fun = in_array($playType, array('1xzhix', 'dxds', '5xtx')) ? "cal_{$playType}" : "cal_{$playType}ds";
		$res = $this->$fun(array('codes' => $code), array('awardNum' => $award));
		if ((isset($res[0]) && $res[0]) || (isset($res['qw']) && $res['qw'])) return true;
		return false;
	}
}
