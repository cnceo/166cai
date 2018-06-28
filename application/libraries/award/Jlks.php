<?php

class Jlks
{
	private $CI;
	private $playType = array(
		'1' => 'hz',
		'2' => 'sthtx',
		'3'	=> 'sthdx',
		'4'	=> 'sbth',
		'5' => 'slhtx',
		'6' => 'ethfx',
		'7' => 'ethdx',
		'8'	=> 'ebth'
	);
	private $lid;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('bonus_model');
		$this->CI->load->helper('string');
		$this->order_status = $this->CI->bonus_model->orderConfig('orders');
		$this->lid = 56;
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, $this->lid);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], $this->lid, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$fun = "cal_{$this->playType[$order['playType']]}";
						if(method_exists($this, $fun))
						{
							$bouns_detail = $this->$fun($order, $ainfo);
							$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
							$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
						}
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data'], $this->lid);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], $this->lid, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], $this->lid, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, $this->lid);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], $this->lid, $this->order_status['split_ggwin']);
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
					$re = $this->CI->bonus_model->setBonus($orders['data'], $this->lid);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], $this->lid, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], $this->lid, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
		$jjDetail = $this->jjDetail();
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
		$mbonus['otherBonus'] = 0;//加奖
		if($this->playType[$order['playType']] == 'hz')
		{
			foreach ($obonus as $bnum)
			{
				if(in_array($bnum, array(3, 18)))
				{
					$dzjj = isset($abonus[$this->playType[3]]) ? $abonus[$this->playType[3]] : 0;
					//$otherBonus = isset($jjDetail[$this->playType[3]]) ? $jjDetail[$this->playType[3]] : 0;
				}
				else 
				{
					$dzjj = isset($abonus['hz']["z{$bnum}"]) ? $abonus['hz']["z{$bnum}"] : 0;
					//$otherBonus = isset($jjDetail['hz']["z{$bnum}"]) ? $jjDetail['hz']["z{$bnum}"] :0;

				}
				$otherBonus = (time() > strtotime('2018-03-30 23:30:00')) ? 0 : (isset($jjDetail['hz']["z{$bnum}"]) ? $jjDetail['hz']["z{$bnum}"] :0);
				$mbonus['bonus'] += $dzjj;
// 				加奖
				$mbonus['otherBonus'] += $otherBonus*100;
				$mbonus['otherBonus'] = $mbonus['otherBonus']*$order['multi'];
				$mbonus['bonus'] += $otherBonus;
			}
		}
		else 
		{
			foreach ($obonus as $bnum)
			{
				$dzjj = isset($abonus[$this->playType[$order['playType']]]) ? $abonus[$this->playType[$order['playType']]] : 0;
				$mbonus['bonus'] += $dzjj;
				//加奖
				$otherBonus = (time() > strtotime('2018-03-30 23:30:00')) ? 0 : (isset($jjDetail[$this->playType[$order['playType']]]) ? $jjDetail[$this->playType[$order['playType']]] :0);
				$mbonus['otherBonus'] += $otherBonus*100;
				$mbonus['otherBonus'] = $mbonus['otherBonus']*$order['multi'];
				$mbonus['bonus'] += $otherBonus;
			}
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = $mbonus['bonus'];
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
	//和值玩法
	private function cal_hz($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = trim($order['codes']);
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			if($betnums == array_sum($awardnums))
			{
				$betnums = intval($betnums);
				$bonus_detail = array($betnums);
			}
		}
		
		return $bonus_detail;
	}
	//三同号通选
	private function cal_sthtx($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums) && count($betnums) == 3)
		{
			if($awardnums[0] == $awardnums[1] && $awardnums[0] == $awardnums[2])
			{
				if(array_sum($betnums) == 0)
				{
					$bonus_detail = array(1);
				}
			}
		}
		return $bonus_detail;
	}
	//三同号单选
	private function cal_sthdx($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			if($awardnums[0] == $awardnums[1] && $awardnums[0] == $awardnums[2])
			{
				if($betnums[0] == $betnums[1] && $betnums[0] == $betnums[2])
				{
					if($awardnums[0] == $betnums[1])
						$bonus_detail = array(1);
				}
			}
		}
		return $bonus_detail;
	}
	//三不同号
	private function cal_sbth($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			$onums = array('1', '2', '3', '4', '5', '6');
			$crsanum = count($this->arrInterSectUniq($awardnums, $onums));
			$crsbnum = count($this->arrInterSectUniq($awardnums, $betnums));
			if($crsanum == 3 && $crsanum == $crsbnum)
			{
				$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}
	//三连号通选
	private function cal_slhtx($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums) && count($betnums) == 3)
		{
			sort($awardnums);
			if(($awardnums[0] + 1) == $awardnums[1] && ($awardnums[1] + 1) == $awardnums[2])
			{
				if(array_sum($betnums) == 0)
					$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}
	//二同号复选
	private function cal_ethfx($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			$acount = array_count_values($awardnums);
			$bcount = array_count_values($betnums);
			$onums = array('1', '2', '3', '4', '5', '6');
			foreach ($onums as $onum)
			{
				if(!empty($acount[$onum]) && $acount[$onum] > 1 && $bcount[$onum] == 2)
				{
					$bonus_detail = array(1);
					break;
				}
			}
		}
		return $bonus_detail;
	}
	//二同号单选
	private function cal_ethdx($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			$acount = array_count_values($awardnums);
			$bcount = array_count_values($betnums);
			$onums = array('1', '2', '3', '4', '5', '6');
			$flag = 0;
			foreach ($onums as $onum)
			{
				if($acount[$onum] == 2 && $bcount[$onum] == 2)
				{
					$flag ++;
				}
				elseif($acount[$onum] == 1 && $bcount[$onum] == 1)
				{
					$flag ++;
				}
				else 
				{
					continue;
				}
			}
			if($flag == 2)
				$bonus_detail = array(1);
		}
		return $bonus_detail;
	}
	//二不同号
	private function cal_ebth($order, $ainfo)
	{
		$awardnums = array_map('trim', explode(',', $ainfo['awardNum']));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($awardnums))
		{
			$onums = array('1', '2', '3', '4', '5', '6');
			$crsanum = count($this->arrInterSectUniq($awardnums, $onums));
			$crsbnum = count($this->arrInterSectUniq($awardnums, $betnums));
			if($crsanum >= 2 && $crsbnum == 2)
			{
				$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}
	
	private function arrInterSectUniq($arrone, $arrtow)
	{
		$intersect = array_intersect($arrone, $arrtow);
		return array_unique($intersect);
	}
	
	//限号投注串过关
	public function caculatelimit($playType, $code, $award)
	{
		$fun = "cal_{$playType}";
		$res = $this->$fun(array('codes' => $code), array('awardNum' => $award));	
		if ($res[0]) return true;
		return false;
	}
   	/**
   	 * [jjDetail 加奖算法]
   	 * @author LiKangJian 2017-11-16
   	 * @return [type] [description]
   	 */
	private function jjDetail()
	{
		$detail = array();
		$detail['hz']['z3'] = '70';
		$detail['hz']['z4'] = '20';
		$detail['hz']['z5'] = '10';
		$detail['hz']['z6'] = '5';
		$detail['hz']['z7'] = '4';
		$detail['hz']['z8'] = '3';
		$detail['hz']['z9'] = '3';
		$detail['hz']['z10'] = '2';
		$detail['hz']['z11'] = '2';
		$detail['hz']['z12'] = '3';
		$detail['hz']['z13'] = '3';
		$detail['hz']['z14'] = '4';
		$detail['hz']['z15'] = '5';
		$detail['hz']['z16'] = '10';
		$detail['hz']['z17'] = '20';
		$detail['hz']['z18'] = '70';
		$detail['sthtx'] = '10';
		$detail['sthdx'] = '70';
		$detail['sbth'] = '10';
		$detail['slhtx'] = '3';
		$detail['ethfx'] = '3';
		$detail['ethdx'] = '20';
		$detail['ebth'] = '2';

		return $detail;
	}

}
