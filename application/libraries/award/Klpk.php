<?php

class Klpk
{
	private $CI;
	private $playType = array(
		'1'  => 'r1',
		'2'  => 'r2s',
		'21' => 'r2f',
		'22' => 'r2d',
		'3'  => 'r3s',
		'31' => 'r3f',
		'32' => 'r3d',
		'4'  => 'r4s',
		'41' => 'r4f',
		'42' => 'r4d',
		'5'  => 'r5s',
		'51' => 'r5f',
		'52' => 'r5d',
		'6'  => 'r6s',
		'61' => 'r6f',
		'62' => 'r6d',
		'7'  => 'th',
		'8'  => 'ths',
		'9'  => 'sz',
		'10'  => 'bz',
		'11'  => 'dz',
		// '12'  => 'bx',
	);

	// 同花格式
	private $th = array(
		'S' => '01',	// 黑
		'H' => '02',	// 红
		'C' => '03',	// 梅
		'D' => '04',	// 方
	);

	// 顺子格式
    private $sz = array('01,02,03', '02,03,04', '03,04,05', '04,05,06', '05,06,07', '06,07,08', '07,08,09', '08,09,10', '09,10,11', '10,11,12', '11,12,13', '01,12,13');

	// 同花顺类型
	private $ths = array(
		'S' => '01',	// 黑
		'H' => '02',	// 红
		'C' => '03',	// 梅
		'D' => '04',	// 方
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 54);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 54, $this->order_status['draw']);
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
					$re = $this->CI->bonus_model->setBonusDetail($orders['data'], 54);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 54, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 54, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 54);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 54, $this->order_status['split_ggwin']);
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
					$re = $this->CI->bonus_model->setBonus($orders['data'], 54);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 54, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 54, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
		$playType = preg_replace('/(r\d)[sfd]$/is', '$1', $this->playType[$order['playType']]);
		// 同花顺等玩法单选包选处理
		$playType = $this->checkTypesFormat($playType, $order);

		if(!empty($obonus))
		{
			foreach ($obonus as $betnum)
			{
				$dzjj = isset($abonus[$playType]['dzjj']) ? $abonus[$playType]['dzjj'] : 0;
                                if(($order['playType'] == '21' || $order['playType'] == '22') && $order['betTnum'] > 1)
                                {
                                    $dzjj += 7;
                                    $mbonus['otherBonus'] += 700 * $betnum;
                                }
                                if(($order['playType'] == '31' || $order['playType'] == '32') && $order['betTnum'] > 1)
                                {
                                    $dzjj += 24;
                                    $mbonus['otherBonus'] += 2400 * $betnum;
                                }
                                if($order['playType'] == '11' && $order['codes']!='00')
                                {
                                    $dzjj += 18;
                                    $mbonus['otherBonus'] += 1800 * $betnum;
                                }
                                if($order['playType'] == '11' && $order['codes']=='00')
                                {
                                    $dzjj += 1;
                                    $mbonus['otherBonus'] += 100 * $betnum;
                                }
				$mbonus['bonus'] += $betnum * $dzjj;
			}
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = $mbonus['bonus'];
                $mbonus['otherBonus'] = $mbonus['otherBonus'] * $order['multi'];
		return $mbonus;
	}

	private function checkTypesFormat($playType, $order)
	{
		$plays = '';
		if(!preg_match('/r(\d)/is', $playType, $match))
		{
			$plays .= $playType . 'dx';
			$codes = array_map('trim', explode(',', $order['codes']));
			foreach ($codes as $in => $code) 
			{
				if($code == '00' && count($codes) == 1)
				{
					$plays = $playType . 'bx';
				}
				elseif($code == '00' && count($codes) != 1)
				{
					$plays .= $playType . 'bx';
				}
			}
		}
		else
		{
			$plays = $playType;
		}
		return $plays;
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
	//任选一玩法
	private function cal_r1($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));
		$betnums = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		if(!empty($betnums))
		{
			foreach ($betnums as $in => $betnum)
			{
				$bonus_detail[$in] = 0;
				if(in_array($betnum, $awardnums))
				{
					$bonus_detail[$in] = 1;
				}
			}
		}
		return $bonus_detail;
	}
	
	public function __call($func, $params)
	{
		if(preg_match('/r(\d)([sfd])/is', $func, $match))
		{
			switch ($match[2])
			{
				case 's':
				case 'f':
					return $this->rx_com_sf($params[0], $params[1], $match[1]);
					break;
				case 'd':
					return $this->cal_com_rxd($params[0], $params[1], $match[1]);
					break;
				default:
					break;
			}
		}
	}
	private function cal_com_rxd($order, $ainfo, $num)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));
		$uawardnums=array_unique($awardnums);
		$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
		$codes = explode('#', $order['codes']);
		$pre_codes = array_map('trim', explode(',', $codes[0]));
		$tai_codes = array_map('trim', explode(',', $codes[1]));
		$bonus_detail = array(0);
		$pre_intersect = array_intersect($pre_codes, $awardnums);
		$pre_hnum = count($pre_intersect);
		$uawardnums_num = count($uawardnums);
		$preNum = count($pre_codes);
		if($pre_hnum + count($tai_codes) >= $uawardnums_num)
		{
			$tai_intersect = array_intersect($tai_codes, $awardnums);
			$tai_hnum = count($tai_intersect);
			if($num >= 3)
			{
				if($pre_hnum + $tai_hnum == $uawardnums_num)
				{
					$bonus_detail[0] = $this->CI->libcomm->combine(count($tai_codes) - $tai_hnum, $num - ($tai_hnum + $preNum));
				}
			}
			else 
			{
				if($pre_hnum + $tai_hnum >= $num && $pre_hnum > 0)
				{
					$bonus_detail[0] = $this->CI->libcomm->combine($tai_hnum, 2 - $pre_hnum);
				}
			}
			
		}
		return $bonus_detail;
	}
	
	private function rx_com_sf($order, $ainfo, $num)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));
		$uawardnums=array_unique($awardnums);
		$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
		$codes = explode('^', $order['codes']);
		$bonus_detail = array(0);
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				$betnums = array_map('trim', explode(',', $code));
				$intersect = array_intersect($betnums, $awardnums);
				$hnum = count($intersect);
				$bonus_detail[$in] = 0;
				if($num >= 3)
				{
					$uawardnums_num = count($uawardnums);
					if($hnum == $uawardnums_num)
					{
						$bonus_detail[$in] = $this->CI->libcomm->combine(count($betnums) - $uawardnums_num, $num - $uawardnums_num);
					}
				}
				else
				{
					if($hnum >= $num)
					{
						$bonus_detail[$in] = $this->CI->libcomm->combine($hnum, $num);
					}
				}
			}
		}
		return $bonus_detail;
	}

	// 同花
	private function cal_th($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardtypes = array_map('trim', explode(',', $awardstrs[1]));

		// 同花检查
		$match = '';
		if( $awardtypes[0] == $awardtypes[1] && $awardtypes[1] == $awardtypes[2] && $awardtypes[0] == $awardtypes[2] )
		{
			$match = $this->th[$awardtypes[0]];
		}

		$codes = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		$details = array();
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				if(!empty($match) && $code == '00')
				{
					$details[$in] = 1;
				}
				else
				{
					if(!empty($match) && $match == $code)
					{
						$details[$in] = 1;
					}
					else
					{
						$details[$in] = 0;
					}
				}
			}

			if(array_sum($details) > 0)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}

	// 同花顺
	private function cal_ths($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardtypes = array_map('trim', explode(',', $awardstrs[1]));

		$match = '';
		$thFlag = false;
		$szFlag = false;

		// 同花检查
		if( !empty($awardtypes) && $awardtypes[0] == $awardtypes[1] && $awardtypes[1] == $awardtypes[2] && $awardtypes[0] == $awardtypes[2] )
		{
			$thFlag = true;
			$match = $this->ths[$awardtypes[0]];
		}

		// 顺子检查
		$awardnums = explode(',', $awardstrs[0]);
		// 排序
		sort($awardnums);

		$preMatchNum = implode(',', $awardnums);
		
		// 判断是否是顺子
		if( in_array($preMatchNum, $this->sz) )
		{
			$szFlag = true;
		}

		$codes = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		$details = array();
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				if(!empty($match) && $thFlag && $szFlag && $code == '00')
				{
					$details[$in] = 1;
				}
				else
				{
					if(!empty($match) && $thFlag && $szFlag && $match == $code)
					{
						$details[$in] = 1;
					}
					else
					{
						$details[$in] = 0;
					}
				}
			}

			if(array_sum($details) > 0)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}

	// 顺子
	private function cal_sz($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));

		// 排序
		sort($awardnums);

		$preMatchNum = implode(',', $awardnums);

		$match = '';
		if( in_array($preMatchNum, $this->sz) )
		{
			$szArry = array_flip($this->sz);
			$match = $szArry[$preMatchNum] + 1;
			// 补齐两位
			$match = str_pad($match, 2, "0", STR_PAD_LEFT);
		}

		$codes = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		$details = array();
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				if(!empty($match) && $code == '00')
				{
					$details[$in] = 1;
				}
				else
				{
					if(!empty($match) && $match == $code)
					{
						$details[$in] = 1;
					}
					else
					{
						$details[$in] = 0;
					}
				}
			}

			if(array_sum($details) > 0)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}

	// 豹子
	private function cal_bz($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));

		$match = '';
		if( !empty($awardnums) && $awardnums[0] == $awardnums[1] && $awardnums[1] == $awardnums[2] && $awardnums[0] == $awardnums[2] )
		{
			$match = $awardnums[0];
			// 补齐两位
			$match = str_pad($match, 2, "0", STR_PAD_LEFT);
		}

		$codes = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		$details = array();
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				if(!empty($match) && $code == '00')
				{
					$details[$in] = 1;
				}
				else
				{
					if(!empty($match) && $match == $code)
					{
						$details[$in] = 1;
					}
					else
					{
						$details[$in] = 0;
					}
				}
			}

			if(array_sum($details) > 0)
			{
				$bonus_detail = array(1);
			}
		}
		return $bonus_detail;
	}

	// 对子
	private function cal_dz($order, $ainfo)
	{
		$awardstrs = explode('|', $ainfo['awardNum']);
		$awardnums = array_map('trim', explode(',', $awardstrs[0]));

		// 排序
		sort($awardnums);
		// 统计出现次数
		$countData = array_count_values($awardnums);

		$match = '';
		foreach ($countData as $num => $counts) 
		{
			if($counts == 2)
			{
				$match = $num;
				// 补齐两位
				$match = str_pad($match, 2, "0", STR_PAD_LEFT);
			}
		}
		
		$codes = array_map('trim', explode(',', $order['codes']));
		$bonus_detail = array(0);
		$details = array();
		if(!empty($codes))
		{
			foreach ($codes as $in => $code)
			{
				if(!empty($match) && $code == '00')
				{
					$details[$in] = 1;
				}
				else
				{
					if(!empty($match) && $match == $code)
					{
						$details[$in] = 1;
					}
					else
					{
						$details[$in] = 0;
					}
				}
			}

			if(array_sum($details) > 0)
			{
				$bonus_detail = array(1);
			}
		}

		return $bonus_detail;
	}
	
	public function caculatelimit($playType, $code, $award)
	{
		if (strpos($playType, 'rx') !== false) {
			$res = $this->cal_com_rxd(array('codes' => $code), array('awardNum' => $award), preg_replace('/\D/', '', $playType));
		}else {
			$fun = "cal_{$playType}";
			$res = $this->$fun(array('codes' => $code), array('awardNum' => $award));
		}
		if ($res[0] == 1) return true;
		return false;
	}
}
