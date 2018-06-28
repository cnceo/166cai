<?php

class Hbsyxw
{
	private $playType = array(
		'1' => 'qy',
		'2' => 'r2',
		'3'	=> 'r3',
		'4'	=> 'r4',
		'5' => 'r5',
		'6' => 'r6',
		'7' => 'r7',
		'8'	=> 'r8',
		'9' => 'q2zhix',
		'10' => 'q3zhix',
		'11' => 'q2zux',
		'12' => 'q3zux',
	);
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('bonus_model');
		$this->CI->load->library('libcomm');
		$this->CI->load->helper('string');
		$this->order_status = $this->CI->bonus_model->orderConfig('orders');
        
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 21408);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 21408, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$fun = "cal_{$this->playType[$order['playType']]}";
						$bouns_detail = $this->$fun($order, $ainfo);
						$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data'], 21408);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 21408, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 21408, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 21408);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 21408, $this->order_status['split_ggwin']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns = $this->cal_bonus($order, $ainfo);
						$orders['data'][$in]['status'] = $this->order_status['win'];
						$orders['data'][$in]['bonus'] = $bouns['bonus'];
						$orders['data'][$in]['margin'] = $bouns['margin'];
					}
					$re = $this->CI->bonus_model->setBonus($orders['data'], 21408);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 21408, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 21408, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
	
	private function cal_bonus($order, $ainfo)
	{
		$abonus = json_decode($ainfo['bonusDetail'], true);
		$obonus = json_decode($order['bonus_detail'], true);
		$mbonus['bonus'] = 0;
		foreach ($obonus as $bnum)
		{
			$dzjj = isset($abonus[$this->playType[$order['playType']]]['dzjj']) ? $abonus[$this->playType[$order['playType']]]['dzjj'] : 0;
			$mbonus['bonus'] += $bnum * $dzjj;
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = $mbonus['bonus'];
		return $mbonus;
	}
	
	//前一过关
	private function cal_qy($order, $ainfo)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			$codes = explode(',', $code);
			if(in_array($awards[0], $codes))
			{
				array_push($params, 1);
			}
			else
			{
				array_push($params, 0);
			}
		}
		return $params;
	}
	
	//任2过关
	private function cal_r2($order, $ainfo)
	{
		return $this->comm_r2_r5($order, $ainfo, 2);
	}
	
	//任3过关
	private function cal_r3($order, $ainfo)
	{
		return $this->comm_r2_r5($order, $ainfo, 3);
	}
	
	//任4过关
	private function cal_r4($order, $ainfo)
	{
		return $this->comm_r2_r5($order, $ainfo, 4);
	}
	
	//任5过关
	private function cal_r5($order, $ainfo)
	{
		return $this->comm_r2_r5($order, $ainfo, 5);
	}
	
	//任6过关
	private function cal_r6($order, $ainfo)
	{
		return $this->comm_r6_r8($order, $ainfo, 1);
	}
	
	//任7过关
	private function cal_r7($order, $ainfo)
	{
		return $this->comm_r6_r8($order, $ainfo, 2);
	}
	
	//任8过关
	private function cal_r8($order, $ainfo)
	{
		return $this->comm_r6_r8($order, $ainfo, 3);
	}
	
	//前二直选
	private function cal_q2zhix($order, $ainfo)
	{
		return $this->comm_z9_z10($order, $ainfo, '11');
	}
	
	//前三直选
	private function cal_q3zhix($order, $ainfo)
	{
		return $this->comm_z9_z10($order, $ainfo, '111');
	}
	
	//前二组选
	private function cal_q2zux($order, $ainfo)
	{
		return $this->comm_z11_z12($order, $ainfo, 2);
	}
	
	//前三组选
	private function cal_q3zux($order, $ainfo)
	{
		return $this->comm_z11_z12($order, $ainfo, 3);
	}
	
	//前二、前三组选
	private function comm_z11_z12($order, $ainfo, $flag)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$aw = array();
		for($i = 0; $i < $flag; ++$i)
		{
			$aw[] = $awards[$i];
		}
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			if(strpos($code, '#'))
			{
				$codes = explode('#', $code);
				$pre_codes = explode(',', $codes[0]);
				$tail_codes = explode(',', $codes[1]);
				$pre_hits = array_intersect($pre_codes, $aw);
				$tail_hits = array_intersect($tail_codes, $aw);
				if(count($pre_hits) == count($pre_codes) && (count($pre_hits) + count($tail_hits)) == $flag)
				{
					array_push($params, 1);
				}
				else 
				{
					array_push($params, 0);
				}
			}
			else 
			{
				$codes = explode(',', $code);
				$hits = array_intersect($aw, $codes);
				if(count($hits) == $flag)
				{
					array_push($params, 1);
				}
				else
				{
					array_push($params, 0);
				}
			}
		}
		return $params;
	}
	
	//前二、前三直选
	private function comm_z9_z10($order, $ainfo, $flag)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			$codes = explode('*', $code);
			$hits = array();
			foreach ($codes as $key => $ball)
			{
				$balls = explode(',', $ball);
				if(in_array($awards[$key], $balls))
				{
					array_push($hits, 1);
				}
				else
				{
					array_push($hits, 0);
				}
			}
			$hits_str = implode('', $hits);
			//如果前两位都为1即中奖
			if($hits_str === $flag)
			{
				array_push($params, 1);
			}
			else
			{
				array_push($params, 0);
			}
		}
		return $params;
	}
	
	private function comm_r2_r5($order, $ainfo, $rm)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			if(strpos($code, '#'))
			{
				$codes = explode('#', $code);
				$pre_codes = explode(',', $codes[0]);
				$tail_codes = explode(',', $codes[1]);
				$pre_hits = array_intersect($pre_codes, $awards);
				$tail_hits = array_intersect($tail_codes, $awards);
				if(count($pre_hits) == count($pre_codes))
				{
					$rm = $rm - count($pre_hits);
					$hits = $tail_hits;
					$num = $this->CI->libcomm->combine(count($hits), $rm);
				}
				else 
				{
					$num = 0;
				}
			}
			else 
			{
				$codes = explode(',', $code);
				$hits = array_intersect($awards, $codes);
				$num = $this->CI->libcomm->combine(count($hits), $rm);
			}
			array_push($params, $num);
		}
		return $params;
	}
	
	private function comm_r6_r8($order, $ainfo, $rm)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			if(strpos($code, '#'))
			{
				$codes = explode('#', $code);
				$pre_codes = explode(',', $codes[0]);
				$pre_codes_n = count($pre_codes);
				$tail_codes = explode(',', $codes[1]);
				$tail_codes_n = count($tail_codes);
				$pre_hits = array_intersect($pre_codes, $awards);
				$pre_hits_n = count($pre_hits);
				$tail_hits = array_intersect($tail_codes, $awards);
				$tail_hits_n = count($tail_hits);
				if($pre_hits_n + $tail_hits_n == 5)
				{
					$optLeft = (5 + $rm) - $pre_codes_n; //拖中还需选择的个数
					$optMiss = $tail_codes_n - $tail_hits_n; //拖中未中球数
					
					$optNeed = 5 - $pre_hits_n; //还要选几个中的球
					$optNhit = $optLeft - $optNeed;
					if($optNeed > $tail_hits_n || $optNhit > $optMiss)
					{
						$num = 0;
					}
					else 
					{
						$num = $this->CI->libcomm->combine($tail_hits_n, $optNeed) *
									$this->CI->libcomm->combine($optMiss, $optNhit);
					}
				}
				else 
				{
					$num = 0;
				}
			}
			else 
			{
				$codes = explode(',', $code);
				$hits = array_intersect($awards, $codes);
				$hitNum = count($hits);
				if($hitNum == 5)
				{
					$noHits = count($codes) - $hitNum;
					$num = $this->CI->libcomm->combine($noHits, $rm);
				}
				else
				{
					$num = 0;
				}
			}
			array_push($params, $num);
		}
		return $params;
	}
	
	public function caculatelimit($playType, $code, $award)
	{
		$fun = "cal_{$playType}";
		$code = str_replace(array('|', '$'), array('*', '#'), $code);
		$res = $this->$fun(array('codes' => $code), array('awardNum' => $award));	
		if ($res[0] == 1) return true;
		return false;
	}
}
