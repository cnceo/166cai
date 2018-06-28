<?php

/*
 * 胜负彩 过关
 * @date:2015-05-05
 */
class Sfc
{
	private $lids = array(
		'sfc' => '11',
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
		$ainfos = $this->CI->bonus_model->sfcAwardInfo(0);
		
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['mid']);
				//查询此期次的订单
				$orders = $this->CI->bonus_model->bonusOrders($issue, 11, $this->order_status['draw']);
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
					$orders = $this->CI->bonus_model->bonusOrders($issue, 11, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$orders = $this->CI->bonus_model->setSfcStatus($ainfo['mid'], array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				}
				$this->CI->bonus_model->trans_complete();
			}
		}
	}

	/**
	 * 胜负彩过关
	 */
	private function cal_sd($order, $ainfo)
	{
		$bonus = 0;
		// 投注串
		$betStr = explode('*', $order['codes']);
		// 赛果
		$result = explode(',', $ainfo['result']);
		// 奖池详情
		$award_detail = json_decode($ainfo['award_detail'], true);

		// 统计中奖球数、额外球数
		$awardCount = 0;
		$otherCount = 0;
		$failCount = 0;
		$multi     = 1;
		foreach ($result as $in => $res) 
		{
			$bet = array();
			$bet = explode(',', $betStr[$in]);
			if(in_array($res, $bet))
			{
				$awardCount++;
				$c = count($bet) - 1;
				$otherCount += $c;
			}
			else if($res == '*' && !in_array(4, $bet))
			{
				$awardCount++;
				$multi *= count($bet);
				//$otherCount += $c;
			}
			else
			{
				$c = count($bet);
				$failCount += $c;
			}
		}

		$bonus_detail = array(
			'1' => 0,
			'2' => 0
		);

		if( $awardCount == 14 )
		{
			// 统计中奖注数
			$bonus_detail = array(
				'1' => 1 * $multi,
				'2' => $otherCount * $multi 
			);
		}
		elseif( $awardCount == 13 )
		{
			// 统计中奖注数
			$bonus_detail = array(
				'1' => 0,
				'2' => $failCount * $multi
			);
		}
		else
		{
			$bonus_detail = array(
				'1' => 0,
				'2' => 0
			);
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
		$ainfos = $this->CI->bonus_model->sfcAwardInfo(1);

		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$fields = array('sub_order_id', 'result', 'status');
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['mid']);
				//查询此期次的已过关中奖的订单
				$orders = $this->CI->bonus_model->bonusOrders($issue, 11, $this->order_status['split_ggwin']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bonus = $this->cal_bonus_sfc($order, $ainfo);
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
					$orders = $this->CI->bonus_model->bonusOrders($issue, 11, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$this->CI->bonus_model->setSfcStatus($ainfo['mid'], array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
				}
				$this->CI->bonus_model->trans_complete();
			}
		}
	}

	/**
	 * 胜负彩算奖
	 */
	public function cal_bonus_sfc($order, $ainfo)
	{
		// 中奖详情
		$bonus_detail = json_decode($order['bonus_detail'], true);

		// 奖池详情
		$award_detail = json_decode($ainfo['award_detail'], true);

		$bonusAll = array(
			'bonus' => 0,
			'margin' => 0
		);

		//一等奖
		$bonus1 = $bonus_detail[1] * $award_detail['1dj']['dzjj'] * $order['multi'];
		$bonus1 = ParseUnit($bonus1);
		if( $award_detail['1dj']['dzjj'] >= 10000 )
		{
			$margin1 = $bonus1 * 0.8;
		}
		else
		{
			$margin1 = $bonus1;
		}
		//二等奖
		$bonus2 = $bonus_detail[2] * $award_detail['2dj']['dzjj'] * $order['multi'];
		$bonus2 = ParseUnit($bonus2);
		if( $award_detail['2dj']['dzjj'] >= 10000 )
		{
			$margin2 = $bonus2 * 0.8;
		}
		else
		{
			$margin2 = $bonus2;
		}

		$bonusAll['bonus'] = $bonus1 + $bonus2;
		$bonusAll['margin'] = $margin1 + $margin2;

		return $bonusAll;
	}

}
