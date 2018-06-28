<?php

class Jczq
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
		$ainfos =$this->CI->bonus_model->jczqAwardInfo();
		if(!empty($ainfos))
		{
			foreach ($ainfos as $ainfo)
			{
				$this->CI->bonus_model->trans_start();
				$orders = $this->CI->bonus_model->jczqOrders($ainfo['mid'], $ainfo['aduitflag']);
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
							$order['odds'] = $this->cal_jczq($order, $ainfo);
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
					
					$orders = $this->CI->bonus_model->jczqOrders($ainfo['mid'], $ainfo['aduitflag']);
					if($orders['flag'])
					{
						$flag = $orders['flag'];
					}
				}
				if(empty($flag))
				{
					$affectedRows = $this->CI->bonus_model->setJczqStatus($ainfo['mid'], array('key' => 'status', 'val' => $this->order_status['paiqi_ggsucc']));
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
		$this->CI->bonus_model->calBonusOrders(42);
		$this->CI->bonus_model->calRcBounsOrders(42);
		$returnData = array(
			'currentFlag' => true,
			'triggerFlag' => true,
		);
		
		return $returnData;
	}
	
	
	private function cal_jczq($order, $ainfo)
	{
		$res = 0;
		$fall_score = explode(':', $ainfo['full_score']);
		$pscores = $order['pscores'];
		$pscores = explode('/', $pscores);
		$details = json_decode($order['pdetail'], true);
		switch ($order['ptype'])
		{
			case 'SPF':
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+)\(.*?\)$/is', $pscore, $matches);
					if($matches[1] == $this->cal_mresult($fall_score))
					{
						$res = $details["vs"]["v{$matches[1]}"][0];
						break;
					}
				}
				break;
			case 'RQSPF':
				$rqs = $details['letVs']['letPoint'];
				$fall_score[0] += $rqs[0];
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $pscore, $matches);
					if($matches[1] == $this->cal_mresult($fall_score))
					{
						$res = $details["letVs"]["v{$matches[1]}"][0];
						break;
					}
				}
				break;
			case 'CBF':
				$result = $this->cal_mresult($fall_score);
				$dinfos = array('0' => '09', '1' => '99', '3' => '90');
				$cscores= array('1:0', '2:0', '2:1', '3:0', '3:1', '3:2', '4:0', '4:1', '4:2', '5:0', '5:1', '5:2',
								'0:0', '1:1', '2:2', '3:3',
								'0:1', '0:2', '1:2', '0:3', '1:3', '2:3', '0:4', '1:4', '2:4', '0:5', '1:5', '2:5');
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+:\d+)\(.*?\)$/is', $pscore, $matches);
					$index = preg_replace('/[^\d]/is', '', $matches[1]);
					if(($matches[1] == $ainfo['full_score'] && in_array($ainfo['full_score'], $cscores))  
					|| (!in_array($ainfo['full_score'], $cscores) && $index == $dinfos[$result]))
					{
						$res = $details["score"]["v$index"][0];
						break;
					}
					
				}
				break;
			case 'JQS':
				$result = $fall_score[0] + $fall_score[1];
				if($result >= 7)
				{
					$res = 7;
					$res = $details["goal"]["v$res"][0];
				}
				else 
				{
					foreach ($pscores as $pscore)
					{
						preg_match('/^(\d+)\(.*?\)$/is', $pscore, $matches);
						if($matches[1] == $result)
						{
							$res = $matches[1];
							$res = $details["goal"]["v$res"][0];
							break;
						}
					}
				}
				break;
			case 'BQC':
				$half_score = explode(':', $ainfo['half_score']);
				$hresult = $this->cal_mresult($half_score);
				$fresult = $this->cal_mresult($fall_score);
				$result = "$hresult-$fresult";
				foreach ($pscores as $pscore)
				{
					preg_match('/^(\d+-\d+)\(.*?\)$/is', $pscore, $matches);
					if($matches[1] == $result)
					{
						$res = $details["half"]["v$hresult$fresult"][0];
						break;
					}
				}
				break;
			default:
				break;
		}
		return $res;
	}
	
	private function cal_mresult($score)
	{
		$mresult = '0';
		if($score[0] > $score[1])
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
