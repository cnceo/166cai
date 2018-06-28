<?php

class Qxc
{
	private $lids = array(
		'qxc' => '10022',
	);
	//定义中奖规则
	private $bonusMap = 	array(
		'1' => array('1111111'),
		'2' => array('1111110','0111111'),
		'3' => array('1111101','1111100','0111110','0011111','1011111'),
		'4' => array('1111000','1111001','1111010','1111011','0111101','0111100','0011110','1011110','0001111','0101111','1001111','1101111'),
		'5' => array('1110000','1110001','1110010','1110011','1110100','1110101','1110110','1110111','0111000','0111001','0111011','0011100','0011101','1011100','1011101','0001110','1001110','0101110','0000111','0010111','0100111','0110111','1000111','1010111','1100111', '0111010', '1101110'),
		'6' => array('1100000','1100001','1100010','1100011','1100100','1100101','1100110','1101000','1101001','1101010','1101011','1101100','1101101','0110000','0110001','0110010','0110011','0110100','0110101','0110110','1011000','1011001','1011011','0011000','0011001','0011011','0001100','0001101','1001100','1001101','0101100','0101101','0000110','0010110','0100110','1000110','1010110','0000011','0001011','0010011','0100011','0101011','1000011','1001011','1010011','0011010','1011010'),
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 10022);
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
				$orders = $this->CI->bonus_model->bonusOrders($issue, 10022, $this->order_status['draw']);
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
					$orders = $this->CI->bonus_model->bonusOrders($issue, 10022, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 10022, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				    if($affectedRows)
				    {
				        //兼容多次过关计奖状态问题
				        $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 10022, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 10022);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
			    $orderStatus = strpos($ainfo['bonusDetail'], '--') == true ? $this->order_status['split_ggwin'] : $this->order_status['split_bigwin'];
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['issue']);
				$orders = $this->CI->bonus_model->bonusOrders($issue, 10022, $orderStatus);
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
					$orders = $this->CI->bonus_model->bonusOrders($issue, 10022, $orderStatus);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 10022, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
		$bonusall = array();
		$mbonus['bonus'] = 0;
		$mbonus['margin'] = 0;
		foreach ($obonus as $lev => $bonus)
		{
			foreach ($bonus as $in => $bnum)
			{
			    $jj = $abonus["{$lev}dj"]['dzjj'];
			    if(in_array($lev, array(1, 2)) && ($bnum > 0) && ($jj == '--'))
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
		$codestrs = explode('^', $order['codes']);
		$levs = array_keys($this->bonusMap);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			$codes = explode('*', $code);
			$awards = explode(',', $ainfo['awardNum']);
			$res = array();
			foreach ($codes as $key => $code)
			{
				$balls = explode(',', $code);
				foreach ($balls as $ball)
				{
					if($ball == $awards[$key])
					{
						$res[$key][] = 1;
					}
					else
					{
						$res[$key][] = 0;
					}
				}
			}
			$param = $this->calBonusParams($res);
			foreach ( $levs as $lev)
			{
				$params[$lev][] = $param[$lev];
			}
		}
		return $params;
	}
	
	//各等奖中奖注数
	private function calBonusParams($arr)
	{
		$return = array();
		$res = array();
		foreach ($arr[0] as $p1)
		{
			foreach ($arr[1] as $p2)
			{
				foreach ($arr[2] as $p3)
				{
					foreach ($arr[3] as $p4)
					{
						foreach ($arr[4] as $p5)
						{
							foreach ($arr[5] as $p6)
							{
								foreach ($arr[6] as $p7)
								{
									$str = $p1.$p2.$p3.$p4.$p5.$p6.$p7;
									array_push($res, $str);
								}
							}
						}
					}
				}
			}
		}
		$res = array_count_values($res);
		foreach ($this->bonusMap as $lev => $value)
		{
			$num = 0;
			foreach ($value as $val)
			{
				$zj = isset($res[$val]) ? $res[$val] : 0;
				$num += $zj;
			}
			$return[$lev] = $num;
		}
		return $return;
	}
}
