<?php

class Qlc
{
	private $lids = array(
		'qlc' => '23528',
	);
	private $lbinfo = 	array(
		//QLC
		'23528' => array(
			'fnum' => 7,
			'bnum' => 0,
			'bonus' => array(
				'1' => array('hit1' => 7, 'hit2' => 0),
				'2' => array('hit1' => 6, 'hit2' => 1),
				'3' => array('hit1' => 6, 'hit2' => 0),
				'4' => array('hit1' => 5, 'hit2' => 1),
				'5' => array('hit1' => 5, 'hit2' => 0),
				'6' => array('hit1' => 4, 'hit2' => 1),
				'7' => array('hit1' => 4, 'hit2' => 0),
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 23528);
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
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 23528, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns_detail = $this->cal_sd($order, $ainfo);
						$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 23528, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23528, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				    if($affectedRows)
				    {
				        //兼容多次过关计奖状态问题
				        $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23528, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 23528);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
			    $orderStatus = strpos($ainfo['bonusDetail'], '--') == true ? $this->order_status['split_ggwin'] : $this->order_status['split_bigwin'];
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 23528, $orderStatus);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns = $this->cal_bonus($order, $ainfo);
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
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 23528, $orderStatus);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 23528, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
	
	private function cal_bonus($order, $ainfo)
	{
		$abonus = json_decode($ainfo['bonusDetail'], true);
		$obonus = json_decode($order['bonus_detail'], true);
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
		foreach ($obonus as $lev => $bonus)
		{
			foreach ($bonus as $in => $bnum)
			{
			    $jj = $abonus["{$lev}dj"]['dzjj'];
			    if(in_array($lev, array(1, 2, 3)) && ($bnum > 0) && ($jj == '--'))
			    {
			        //如果是浮动奖级，并且奖级未出来，并且又中奖的情况下直接返回
			        $mbonus = array(
			            'bonus' => 0,
			            'margin' => 0,
			        );
			        
			        return $mbonus;
			    }
				$mbonus['bonus'] += $bnum * $abonus["{$lev}dj"]['dzjj'];
				$dzjj = $abonus["{$lev}dj"]['dzjj'] >=10000 ? $abonus["{$lev}dj"]['dzjj'] * 0.8 : $abonus["{$lev}dj"]['dzjj'];
				$mbonus['margin'] += $dzjj * $bnum;
			}
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = ParseUnit($mbonus['margin']) * $order['multi'];
		return $mbonus;
	}
	
	private function cal_sd($order, $ainfo)
	{
		$params = array();
		$awardnum = $ainfo['awardNum'];
		$codestrs = explode('^', $order['codes']);
		$levs = array_keys($this->lbinfo[$this->lids['qlc']]['bonus']);
		$preg = '/\((\d+)\)/is';
		preg_match($preg, $awardnum, $matches);
		//特殊号
		$tnum = $matches[1];
		//基础号
		$bnums = explode(',', preg_replace($preg, '', $awardnum));
		$sballs = array();
		$bballs = array();
		foreach ($codestrs as $codestr)
		{
			if(empty($codestr)) continue;
			//投注串
			$codes = explode('#', $codestr);
			if(count($codes) > 1)
			{
				$sballs = explode(',', $codes[0]);
				$bballs = explode(',', $codes[1]);
			}
			else 
			{
				$bballs = explode(',', $codes[0]);
			}
			$dan = count($sballs);
			$max = $dan + count($bballs);
			//胆拖和基本开奖交集
			$hd = count($this->checkWinNum($sballs, $bnums));
			$hit = count($this->checkWinNum($bballs, $bnums));
			//和特殊号交集
			$tshits = $this->checkWinNum($sballs, array($tnum));
			$tbhits = $this->checkWinNum($bballs, array($tnum));
			$sd = count($tshits);
			$sp = $sd + count($tbhits);
			$param = $this->med_qlc($max, $hit, $dan, $hd, $sp, $sd);
			foreach ( $levs as $lev)
			{
				$params[$lev][] = $param[$lev]['num'];
			}
		}
		
		return $params;
	}
	
	private function checkWinNum($balls, $aballs)
	{
		return array_intersect($balls, $aballs);
	}
	
	/**
	 * 七乐彩-奖级奖金计算
	 * max:总共选的个数,	hit:基本码命中个数(不包含特别码),
	 * dan:胆的个数,	hd:胆码命中个数,	
	   sd:特别号是否为胆	sp:是否中特别号(1:命中,0:未命中),
	 * return:	各奖级注数和奖金
	 */
	private function med_qlc($max, $hit, $dan, $hd, $sp, $sd)
	{
		$hn = array();//存储结果
		$nh = $max - $dan - $hit; //可用于选择未中的球
		$prizes = $this->lbinfo[$this->lids['qlc']]['bonus'];
		$bn = $this->lbinfo[$this->lids['qlc']]['fnum'];
		foreach( $prizes as $i => $arr)
		{
			//应该命中
			$must = $arr['hit1'];
			//须命中
			$h = $this->CI->libcomm->combine($hit, $must-$hd);
			//须未命中
			$nhx= $bn-($must-$hd)-$dan;
			if($sp==1)
			{
				if($sd==1)
				{
					$snum1 = $this->CI->libcomm->combine($nh, $nhx);
					$snum2=0;
				}else
				{
					$snum1 = $this->CI->libcomm->combine($nh-1, $nhx-1);
					$snum2 = $this->CI->libcomm->combine($nh-1, $nhx);
				}
			}
			else
			{
				$snum1 = 0;
				$snum2 = $this->CI->libcomm->combine($nh,$nhx);
			}
			
			if($arr['hit2'] == 1)
			{
				$hn[$i]['num'] = $h * $snum1;
			}
			else
			{
				$hn[$i]['num'] = $h * $snum2;
			}
		}
		return $hn;
	}
	
}
