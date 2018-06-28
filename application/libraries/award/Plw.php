<?php

class Plw
{
	private $lids = array(
		'fcsd' => '35',
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 35);
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
				$orders = $this->CI->bonus_model->bonusOrders($issue, 35, $this->order_status['draw']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns_detail = $this->cal_gg($order, $ainfo);
						$orders['data'][$in]['status'] = $this->check_is_win($bouns_detail);
						$orders['data'][$in]['bonus_detail'] = json_encode($bouns_detail);
					}
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 35, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 35, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				    if($affectedRows)
				    {
				        //兼容多次过关计奖状态问题
				        $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 35, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 35);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$issue = $this->CI->libcomm->format_issue($ainfo['issue']);
				$orders = $this->CI->bonus_model->bonusOrders($issue, 35, $this->order_status['split_ggwin']);
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
					$re = $this->CI->bonus_model->setBonus($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($issue, 35, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 35, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
		$mbonus['margin'] = 0;
		foreach ($obonus as $bnum)
		{
			$dzjj = isset($abonus['zx']['dzjj']) ? $abonus['zx']['dzjj'] : 0;
			$mbonus['bonus'] += $bnum * $dzjj;
			$mbonus['margin'] += $bnum * $dzjj * 0.8;
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = ParseUnit($mbonus['margin']) * $order['multi'];
		return $mbonus;
	}
	
	//过关
	private function cal_gg($order, $ainfo)
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
			$hits_str = implode('#', $hits);
			//所有位都相同时为中奖
			if($hits_str === '1#1#1#1#1')
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
	
	//限号投注串过关
	public function caculatelimit($fun, $code, $award)
	{
		$code = preg_replace(array('/,/', '/(\d{1})(\d{1})/'), array('*', '$1,$2'), $code);
		$res = $this->cal_gg(array('codes' => $code), array('awardNum' => $award));	
		if ($res[0] == 1) return true;
		return false;
	}
}
