<?php

class Dlt
{
	private $lids = array(
		'dlt' => '23529',
	);
	private $lbinfo = 	array(
		'23529' => array(
			'fnum' => 5, 
			'bnum' => 2,
			'bonus' => array(
				'1' => array(array(5, 2)),
				'2' => array(array(5, 1)),
				'3' => array(array(5, 0), array(4, 2)),
				'4' => array(array(4, 1), array(3, 2)),
				'5' => array(array(4, 0), array(3, 1), array(2, 2)),
				'6' => array(array(3, 0), array(1, 2), array(2, 1), array(0, 2)),
			)
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 23529);
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
				$issue = $this->CI->libcomm->format_issue($ainfo['issue']);
				$orders = $this->CI->bonus_model->bonusOrders($issue, 23529, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
					    $bouns_detail = $this->cal_sd($order, $ainfo, $ctype);
					    $status = $this->check_is_win($bouns_detail);
					    // 乐善过关计奖
					    $lsStatus = $this->handle_ls($order, $issue);
					    $orders['data'][$in]['status'] = $lsStatus ? $this->order_status['split_ggwin'] : $status;
					    $orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 23529, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23529, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				    if($affectedRows)
				    {
				        //兼容多次过关计奖状态问题
				        $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23529, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 23529);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
			    $orderStatus = strpos($ainfo['bonusDetail'], '--') == true ? $this->order_status['split_ggwin'] : $this->order_status['split_bigwin'];
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['issue']);
				$orders = $this->CI->bonus_model->bonusOrders($issue, 23529, $orderStatus);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns = $this->cal_dlt_bonus($order, $ainfo);
						if($bouns['flag'])
						{
							// 浮动奖级已出，乐善奖奖金检查 原奖金（可能是0） + 乐善奖金
							$bouns = $this->add_ls_bonus($order, $bouns);
						}
						$orders['data'][$in]['status'] = $bouns['bonus'] > 0 ? $this->order_status['win'] : $this->order_status['split_bigwin'];
						$orders['data'][$in]['bonus'] = $bouns['bonus'];
						$orders['data'][$in]['margin'] = $bouns['margin'];
					}
					$re = $this->CI->bonus_model->setBonus($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 23529, $orderStatus);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23529, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
	
	private function cal_dlt_bonus($order, $ainfo)
	{
		$abonus = json_decode($ainfo['bonusDetail'], true);
		$obonus = json_decode($order['bonus_detail'], true);
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
		$mbonus['flag'] = TRUE;
		foreach ($obonus as $lev => $bonus)
		{
			foreach ($bonus as $in => $bnum)
			{
			    $jj = $abonus["{$lev}dj"]['jb']['dzjj'];
			    if(in_array($lev, array(1, 2, 3)) && ($bnum > 0) && ($jj == '--'))
			    {
			        //如果是浮动奖级，并且奖级未出来，并且又中奖的情况下直接返回
			        $mbonus = array(
			            'bonus' => 0,
			            'margin' => 0,
			            'flag' => FALSE,	// 存在大奖
			        );
			        
			        return $mbonus;
			    }
			    
				$mbonus['bonus'] += $bnum * $abonus["{$lev}dj"]['jb']['dzjj'];
				$dzjj = $abonus["{$lev}dj"]['jb']['dzjj'] >=10000 ? $abonus["{$lev}dj"]['jb']['dzjj'] * 0.8 : $abonus["{$lev}dj"]['jb']['dzjj'];
				$mbonus['margin'] += $dzjj * $bnum;
				//追加奖金
				if($order['isChase'])
				{
					$mbonus['bonus'] += $bnum * $abonus["{$lev}dj"]['zj']['dzjj'];
					$zjdzjj = $abonus["{$lev}dj"]['zj']['dzjj'] >=10000 ? $abonus["{$lev}dj"]['zj']['dzjj'] * 0.8 : $abonus["{$lev}dj"]['zj']['dzjj'];
					$mbonus['margin'] += $zjdzjj * $bnum;
				}
			}
		}
		
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = ParseUnit($mbonus['margin']) * $order['multi'];
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
				$oricode['rsalts'] = explode(',', $rmatches[1]); //前驱胆
			}
			$oricode['bsalts'] = array();
			if(!empty($bmatches[1]))
			{
				$oricode['bsalts'] = explode(',', $bmatches[1]); //后驱胆
			}
			$oricode['rballs'] = explode(',', $rmatches[2]); //前驱拖
			$oricode['bballs'] = explode(',', $bmatches[2]); //后驱拖
			$oricode['award_rballs'] = explode(',', $awards[0]); //前驱开奖号
			$oricode['award_bballs'] = explode(',', $awards[1]); //后驱开奖号
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

	// 乐善奖
	public function handle_ls($order, $issue)
	{
		$result = FALSE;
		// split表期次判断
		$info = $this->CI->bonus_model->lsBonusOrders($order);
		if(!empty($info) && !empty($info['awardNum']))
		{
			// 过关
			$bouns_detail = $this->cal_sd($order, $info, 23529);
			// 计奖
			$margin = $this->cal_ls_bonus($order, $bouns_detail);
			if($margin > 0)
			{
				$result = TRUE;
			}
			// 更新乐善奖信息
			$data = array(
				'sub_order_id'	=>	$order['sub_order_id'],
				'bonus_detail'	=>	json_encode($bouns_detail),
				'margin'		=>	$margin,
			);
			$this->CI->bonus_model->recordLsDetail($data);
		}
		return $result;
	}

	public function cal_ls_bonus($order, $bounsDetail)
	{
		$margin = 0;
		// 乐善固定奖级（元）
		$abonus = array(
			'1'	=>	'5000',
			'2'	=>	'1000',
			'3'	=>	'500',
			'4'	=>	'50',
			'5'	=>	'5',
			'6'	=>	'3',
		);
		foreach ($bounsDetail as $lev => $bonus)
		{
			foreach ($bonus as $bnum)
			{
				$margin += $bnum * $abonus[$lev];
			}
		}
		$margin = ParseUnit($margin) * $order['multi'];
		return $margin;
	}

	// 总奖金内汇总乐善奖奖金
	public function add_ls_bonus($order, $bouns)
	{
		$info = $this->CI->bonus_model->lsBonusOrders($order);
		$lsMargin = $info['margin'] ? $info['margin'] : 0;
		$bouns['bonus'] += $lsMargin;
		$bouns['margin'] += $lsMargin;
		return $bouns;
	}
}
