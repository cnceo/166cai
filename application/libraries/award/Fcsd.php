<?php

class Fcsd
{
	private $lids = array(
		'fcsd' => '52',
	);
	
	private $playType = array(
		'1' => 'zx',
		'2' => 'z3',
		'3'	=> 'z6',
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
		$ainfos = $this->CI->bonus_model->awardInfo(0, 52);
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
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 52, $this->order_status['draw']);
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
					$re = $this->CI->bonus_model->setBonusDetail($orders['data']);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 52, $this->order_status['draw']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 52, array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
				    //兼容多次过关计奖状态问题
				    $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 52, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_complete']));
				    $returnData['triggerFlag'] = true;
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
		$ainfos = $this->CI->bonus_model->awardInfo(1, 52);
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 52, $this->order_status['split_ggwin']);
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						$bouns = $this->cal_bonus($order, $ainfo);
						$orders['data'][$in]['status'] = $this->order_status['win'];
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
					$orders = $this->CI->bonus_model->bonusOrders($ainfo['issue'], 52, $this->order_status['split_ggwin']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
				    $affectedRows = $this->CI->bonus_model->setPaiqiStatus($ainfo['issue'], 52, array('key' => 'rstatus', 'val' => $this->order_status['paiqi_jjsucc']));
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
                $mbonus['otherBonus'] = 0;
		foreach ($obonus as $bnum)
		{
			$dzjj = isset($abonus[$this->playType[$order['playType']]]['dzjj']) ? $abonus[$this->playType[$order['playType']]]['dzjj'] : 0;
			$mbonus['bonus'] += $bnum * $dzjj;
		}
		$mbonus['bonus'] = ParseUnit($mbonus['bonus']) * $order['multi'];
		$mbonus['margin'] = $mbonus['bonus'];
		return $mbonus;
	}
	
	//直选过关
	private function cal_zx($order, $ainfo)
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
			//如果百十个位都相同时为中奖
			if($hits_str === '1#1#1')
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
	
	//组三过关
	private function cal_z3($order, $ainfo)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$aw = array_unique($awards);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			if(count($aw) == 2)
			{
				if(strpos($code, '*') == true)
				{
					$codes = explode('*', $code);
					sort($codes);
					sort($awards);
					if(implode('|', $codes) == implode('|', $awards))
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
					$result = array_intersect($aw, $codes);
					if(count($result) == 2)
					{
						array_push($params, 1);
					}
					else
					{
						array_push($params, 0);
					}
				}
			}
			else
			{
				array_push($params, 0);
			}
		}
		return $params;
	}
	
	//组六过关
	private function cal_z6($order, $ainfo)
	{
		$codestrs = explode('^', $order['codes']);
		$awards = explode(',', $ainfo['awardNum']);
		$aw = array_unique($awards);
		$params = array();
		foreach ($codestrs as $code)
		{
			if(empty($code)) continue;
			if(strpos($code, '*') == true)
			{
				$codes = explode('*', $code);
			}
			else
			{
				$codes = explode(',', $code);
			}
			if(count($aw) == 3)
			{
				$result = array_intersect($aw, $codes);
				if(count($result) == 3)
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
				array_push($params, 0);
			}
		}
		return $params;
	}
	
	//限号投注串过关
	public function caculatelimit($playType, $code, $award)
	{
		$fun = "cal_{$playType}";
		$code = preg_replace(array('/,/', '/(\d{1})(\d{1})/'), array('*', '$1,$2'), $code);
		$res = $this->$fun(array('codes' => $code), array('awardNum' => $award));
		if ($res[0] == 1) return true;
		return false;
	}
}
