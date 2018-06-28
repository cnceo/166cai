<?php

/*
 * 胜负彩 过关
 * @date:2015-05-05
 */
class Rj
{
	private $lids = array(
		'rj' => '19',
	);
	private $lbinfo = array(
		//SFC
		'11' => array(
			'bonus' => array(
				'1' => '14',
				'2' => '13',
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
		$ainfos = $this->CI->bonus_model->rjAwardInfo(0);
		
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['mid']);
				//查询此期次的订单
				$orders = $this->CI->bonus_model->bonusOrders($issue, 19, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						// 逐一过关
						$bonus_detail = $this->cal_sd($order, $ainfo);
						$orders['data'][$in]['status'] = $this->check_is_win($bonus_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bonus_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 19, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$orders = $this->CI->bonus_model->setSfcStatus($ainfo['mid'], array('key' => 'rjstatus', 'val' => $this->order_status['paiqi_ggsucc']));
				}
				$this->CI->bonus_model->trans_complete();
			}
		}
	}

	/**
	 * 任九过关
	 */
	private function cal_sd($order, $ainfo)
	{
		$bonus_detail = array();

		$codesStr = explode('^', $order['codes']);

		foreach ($codesStr as $key => $codes) 
		{
			// 投注串
			$betStr = explode('*', $codes);
			// 赛果
			$result = explode(',', $ainfo['result']);
			// 奖池详情
			$award_detail = json_decode($ainfo['award_detail'], true);

			// 统计中奖球数、额外球数
			$awardCount = 0;
			$multi = 1;
			foreach ($result as $in => $res) 
			{
				$bet = array();
				$bet = explode(',', $betStr[$in]);
				if(in_array($res, $bet))
				{
					$awardCount++;
				}
				elseif($res == '*' && !in_array(4, $bet))
				{
					$awardCount++;
					$multi *= count($bet);
				}
			}

			if( $awardCount >= 9 )
			{
				$count = $this->CI->libcomm->combine($awardCount, 9);
				// 统计中奖注数
				array_push($bonus_detail, $count * $multi);
			}
			else
			{
				// 统计中奖注数
				array_push($bonus_detail, 0);
			}
		}
		
		return $bonus_detail;
	}

	/**
	 * 检查是否中奖
	 */
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
	
	/**
	 * 算奖
	 */
	public function bonus($ctype = '')
	{
		$ainfos = $this->CI->bonus_model->rjAwardInfo(1);

		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['mid']);
				//查询此期次的订单
				$orders = $this->CI->bonus_model->bonusOrders($issue, 19, $this->order_status['split_ggwin']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bonus = $this->cal_bonus_rj($order, $ainfo);
						$orders['data'][$in]['status'] = $this->order_status['win'];
						$orders['data'][$in]['bonus'] = $bonus['bonus'];
						$orders['data'][$in]['margin'] = $bonus['margin'];
					}
					$re = $this->CI->bonus_model->setBonus($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 19, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$this->CI->bonus_model->setSfcStatus($ainfo['mid'], array('key' => 'rjrstatus', 'val' => $this->order_status['paiqi_jjsucc']));
				}
				$this->CI->bonus_model->trans_complete();
			}
		}
	}

	/**
	 * 任选九算奖
	 */
	public function cal_bonus_rj($order, $ainfo)
	{
		// 中奖详情
		$bonus_detail = json_decode($order['bonus_detail'], true);

		// 奖池详情
		$award_detail = json_decode($ainfo['award_detail'], true);

		$bonusAll = array(
			'bonus' => 0,
			'margin' => 0
		);

		if(!empty($bonus_detail))
		{
			foreach ($bonus_detail as $count) 
			{
				$bonus1 = $count * $award_detail['rj']['dzjj'] * $order['multi'];
				$bonus1 = ParseUnit($bonus1);
				if( $award_detail['rj']['dzjj'] >= 10000 )
				{
					$margin1 = $bonus1 * 0.8;
				}
				else
				{
					$margin1 = $bonus1;
				}
				$bonusAll['bonus'] += $bonus1;
				$bonusAll['margin'] += $margin1;
			}
		}	
		return $bonusAll;
	}

}
