<?php

class Ssq
{
	private $lids = array(
		'ssq' => '51',
	);
	private $lbinfo = 	array(
		//SSQ
		'51' => array(
			'fnum' => 6,
			'bnum' => 1,
			'bonus' => array(
				'1' => array(array(6, 1)),
				'2' => array(array(6, 0)),
				'3' => array(array(5, 1)),
				'4' => array(array(5, 0), array(4, 1)),
				'5' => array(array(4, 0), array(3, 1)),
				'6' => array(array(2, 1), array(1, 1), array(0, 1)),
			),
		),
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 51);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
			    if($ainfo['aduitflag'] == '0')
			    {
			        //未审核期次不操作
			        continue;
			    }
			    
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 51, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns_detail = $this->cal_sd($order, $ainfo, $ctype);
						$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 51, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 51, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
					if($affectedRows)
					{
					    //兼容多次过关计奖状态问题
					    $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 51, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 51);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
			    $orderStatus = strpos($ainfo['bonusDetail'], '--') == true ? $this->order_status['split_ggwin'] : $this->order_status['split_bigwin'];
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 51, $orderStatus);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
						$bouns = $this->cal_ssq_bonus($order, $ainfo);
						$orders['data'][$in]['status'] = $bouns['bonus'] > 0 ? $this->order_status['win'] : $this->order_status['split_bigwin'];
						$orders['data'][$in]['bonus'] = $bouns['bonus'];
						$orders['data'][$in]['margin'] = $bouns['margin'];
						$orders['data'][$in]['otherBonus'] = $bouns['otherBonus'];
					}

					$re = $this->CI->bonus_model->setBonus($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 51, $orderStatus);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 51, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
		foreach ($details as $detail)
		{
			foreach ($detail as $num)
			{
				if($num > 0)
				{
					$status = $this->order_status['split_ggwin'];
					break;
				}
			}
			if($status == $this->order_status['split_ggwin']) break;
		}
		return $status;
	}
	
	private function cal_ssq_bonus($order, $ainfo, $addbonus = array())
	{
		$abonus = json_decode($ainfo['bonusDetail'], true);
		$obonus = json_decode($order['bonus_detail'], true);
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
		$mbonus['otherBonus'] = 0;
		$margin_tmp = 0;
		foreach ($obonus as $lev => $bonus)
		{
			foreach ($bonus as $in => $bnum)
			{
				$dzjj = $abonus["{$lev}dj"]['dzjj'];
				if(in_array($lev, array(1, 2)) && ($bnum > 0) && ($dzjj == '--'))
				{
				    //如果是浮动奖级，并且奖级未出来，并且又中奖的情况下直接返回  
				    $mbonus = array(
				        'bonus' => 0,
				        'margin' => 0,
				        'otherBonus' => 0,
				    );
				    
				    return $mbonus;
				}
				if(!empty($addbonus[$lev]))
				{
					$dzjj = $abonus["{$lev}dj"]['dzjj'] + $addbonus[$lev];
				}
				$mbonus['bonus'] += $bnum * $dzjj;
				$dzjj = $dzjj >= 10000 ? $dzjj * 0.8 : $dzjj;
				$mbonus['margin'] += $dzjj * $bnum;
				
				$dzjj_tmp = $abonus["{$lev}dj"]['dzjj'] >= 10000 ? $abonus["{$lev}dj"]['dzjj'] * 0.8 : $abonus["{$lev}dj"]['dzjj'];
				$margin_tmp += $bnum * $dzjj_tmp;
			}
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = ParseUnit($mbonus['margin']) * $order['multi'];
		$mbonus['otherBonus'] = $mbonus['margin'] - ParseUnit($margin_tmp * $order['multi']);
		return $mbonus;
	}
	
	private function cal_sd($order, $ainfo, $lid)
	{
		$codestrs = explode('^', $order['codes']);
		$levs = array_keys($this->lbinfo[$lid]['bonus']);
		$award = $ainfo['awardNum'];
		$awards = explode('|', $award);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			$codes = explode('|', $code);
			preg_match('/(?:(.*)#)?(.*)/', $codes[0], $rmatches);
			preg_match('/(?:(.*)#)?(.*)/', $codes[1], $bmatches);
			$oricode['rsalts'] = array();
			if(!empty($rmatches[1]))
			{
				$oricode['rsalts'] = explode(',', $rmatches[1]);
			}
			$oricode['bsalts'] = array();
			if(!empty($bmatches[1]))
			{
				$oricode['bsalts'] = explode(',', $bmatches[1]);
			}
			$oricode['rballs'] = explode(',', $rmatches[2]);
			$oricode['bballs'] = explode(',', $bmatches[2]);
			$oricode['award_rballs'] = explode(',', $awards[0]);
			$oricode['award_bballs'] = explode(',', $awards[1]);
			$param = $this->calBonusParams($oricode, $lid);
			foreach ( $levs as $lev)
			{
				$params[$lev][] = $param[$lev];
			}
		}
		return $params;
	}
	
	private function checkWinNum($balls, $aballs)
	{
		return array_intersect($balls, $aballs);
	}
	
	private function calBonusParams($oricode, $lid)
	{
		$rsaltn = $this->checkWinNum($oricode['rsalts'], $oricode['award_rballs']);
		$rballn = $this->checkWinNum($oricode['rballs'], $oricode['award_rballs']);
	
		$bsaltn = $this->checkWinNum($oricode['bsalts'], $oricode['award_bballs']);
		$bballn = $this->checkWinNum($oricode['bballs'], $oricode['award_bballs']);
	
		$params = array(
				'RH' => count($rsaltn), //前区胆中个数
				'RT' => count($rballn), //前区拖中个数
				'RN' => count($oricode['rsalts']), //前区胆个数
				'PN' => count($oricode['rballs']), //前区拖个数
	
				'BH' => count($bsaltn), //后区胆中个数
				'BT' => count($bballn), //后区拖中个数
				'BN' => count($oricode['bsalts']), //后区胆个数
				'TN' => count($oricode['bballs'])  //后区拖个数
		);
		return $this->win_sd($params, $lid);
	}
	
	/* 计算前区或后区命中指定个数的方案注数
	 * $num @前区或者后区应选的个数 SSQ: 6 || 1; DLT: 5 || 2;
	* $req @胆个数
	* $opt @拖个数
	* $reqHit @胆的命中数
	* $optHit @拖的命中数
	* */
	
	private function solveHits($num, $req, $opt, $reqHit, $optHit)
	{
		$optLeft = $num - $req; //拖中还需选择的个数
		$optMiss = $opt - $optHit; //拖中未中球数
		$max = $reqHit + $optHit; //总中球数
		$hits = array();
		for ($i = 0; $i <= $num; ++ $i)
		{
			if ($i < $reqHit || $i > $max)
			{
				$hits[$i] = 0;
			}
			else
			{
				$optNeed = $i - $reqHit; //还要选几个中的球
				$optNhit = $optLeft - $optNeed;
				if($optNeed > $optHit || $optNhit > $optMiss)
				{
					$hits[$i] = 0;
				}
				else 
				{
					$hits[$i] = $this->CI->libcomm->combine($optHit, $optNeed) *
								$this->CI->libcomm->combine($optMiss, $optNhit);
				}
			}
		}
		return $hits;
	}
	
	// 计算各奖项命中的方案注数
	private function win_sd($params, $lid)
	{
		$lbinfo = $this->lbinfo[$lid];
		$fHits = $this->solveHits($lbinfo['fnum'], $params['RN'], $params['PN'], $params['RH'], $params['RT']);
		$bHits = $this->solveHits($lbinfo['bnum'], $params['BN'], $params['TN'], $params['BH'], $params['BT']);
		$result = array();
		$winners = $lbinfo['bonus'];
		$levels = count($winners);
		for ($i = 1; $i <= $levels; ++ $i)
		{
			$winner = $winners[$i];
			$count = 0;
			for ($j = 0; $j < count($winner); ++ $j)
			{
				$item = $winner[$j];
				$count += $fHits[$item[0]] * $bHits[$item[1]];
			}
			$result[$i] = $count;
		}
		return $result;
	}
}
