<?php

class Jclq
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('bonus_model');
		$this->order_status = $this->CI->bonus_model->orderConfig('orders');
	}
	
	public function calculate($ctype = '')
	{
		$returnData = array(
			'currentFlag' => true,
			'triggerFlag' => false,
		);
		//过关运算
		$ainfos =$this->CI->bonus_model->jclqAwardInfo();
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->jclqOrders($ainfo['mid'], $ainfo['aduitflag']);
				$bdata = array();
				$flag = $orders['flag'];
				while(!empty($orders['data']))
				{
					foreach ($orders['data'] as $in => $order)
					{
						if($ainfo['m_status'] == 1)
						{
							$pscores = explode('/', $order['pscores']);
							$order['odds'] = 1;
							$order['hitnum'] = count($pscores);
						}
						else 
						{
							$order['odds'] = $this->cal_jclq($order, $ainfo);
							$order['hitnum'] = $order['odds'] > 0 ? 1 : 0;
						}
						$order['status'] = $this->order_status['relation_ggsucc'];
						$order['aduitflag'] = $ainfo['aduitflag'] == '1' ? 1 : 0;
						$bdata[] = $order;
					}
					$re = $this->CI->bonus_model->setJJcResult($bdata);
					if(!$re)
					{
						$this->CI->bonus_model->trans_rollback();
						return false;
					}
					$bdata = array();
					
					$orders = $this->CI->bonus_model->jclqOrders($ainfo['mid'], $ainfo['aduitflag']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setJclqStatus($ainfo['mid'], array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
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
	
	public function bonus($ctype = '')
	{
		$this->CI->bonus_model->calBonusOrders(43);
		$this->CI->bonus_model->calRcBounsOrders(43);
		$returnData = array(
			'currentFlag' => true,
			'triggerFlag' => true,
		);
		
		return $returnData;
	}
	
	private function cal_jclq($order, $ainfo)
	{
		$res = 0;
		$fall_score = explode(':', $ainfo['full_score']);
		$pscores = $order['pscores'];
		$pscores = explode('/', $pscores);
		$details = json_decode($order['pdetail'], true);
		switch ($order['ptype'])
		{
			case 'SF':
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $pscore, $matches);
					$result = $this->cal_mresult($fall_score);
					if($matches[1] == $result)
					{
						$res = $details['vs']["v$result"][0];
						break;
					}
				}
				break;
			case 'RFSF':
				$fall_score[1] += $details['letVs']['letPoint'][0];
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $pscore, $matches);
					$result = $this->cal_mresult($fall_score);
					if($matches[1] == $result)
					{
						$res = $details['letVs']["v$result"][0];
						break;
					}
				}
				break;
			case 'SFC':
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $pscore, $matches);
					$result = $this->cal_diff($fall_score);
					if($matches[1] == $result)
					{
						$res = $details['diff']["v$result"][0];
						break;
					}
				}
				break;
			case 'DXF':
				foreach ($pscores as $pscore)
				{
					$in_map = array('0' => 'l', '3' => 'g');
					preg_match('/^(\d+)\(.*?\)(?:{.*?})?$/is', $pscore, $matches);
					$prescore = $details['bs']['basePoint'][0];
					$score =  $fall_score[0] + $fall_score[1];
					$kval = '0';
					if($score > $prescore)
					{
						$kval = '3';
					}
					if($matches[1] == $kval)
					{
						$res = $details['bs'][$in_map[$kval]][0];
						break;
					}
				}
				break;
			default:
				break;
		}
		return $res;
	}
	
	private function cal_diff($score)
	{
		$diff = $score[1] - $score[0];
		if($diff > 0)
		{
			$pre = '0';
		}
		else 
		{
			$pre = '1';
		}
		$diff = abs($diff);
		if($diff >= 1 && $diff <= 5)
		{
			$re = "{$pre}1";
		}
		elseif($diff >= 6 && $diff <= 10)
		{
			$re = "{$pre}2";
		}
		elseif($diff >= 11 && $diff <= 15)
		{
			$re = "{$pre}3";
		}
		elseif($diff >= 16 && $diff <= 20)
		{
			$re = "{$pre}4";
		}
		elseif($diff >= 21 && $diff <= 25)
		{
			$re = "{$pre}5";
		}
		elseif($diff >= 26) 
		{
			$re = "{$pre}6";
		}
		return $re;
	}
	
	private function cal_mresult($score)
	{
		$mresult = '0';
		if($score[0] < $score[1])
		{	
			$mresult = '3';
		}
		elseif($score[0] == $score[1])
		{
			$mresult = '1';
		}
		return $mresult;
	}
}
