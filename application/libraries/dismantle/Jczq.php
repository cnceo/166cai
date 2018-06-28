<?php

class Jczq
{
	private $CI;
	//玩法定义
	private $playTypeMap = array(
		'1*1' => '1',
		'2*1' => '2',
		'3*1' => '3',
		'4*1' => '4',
		'5*1' => '5',
		'6*1' => '6',
		'7*1' => '7',
		'8*1' => '8',
		'9*1' => '9',
		'10*1' => '10',
		'11*1' => '11',
		'12*1' => '12',
		'13*1' => '13',
		'14*1' => '14',
		'15*1' => '15',
		'2*3' => '16',
		'3*3' => '17',
		'3*4' => '18',
		'3*7' => '19',
		'4*4' => '20',
		'4*5' => '21',
		'4*6' => '22',
		'4*11' => '23',
		'4*15' => '24',
		'5*5' => '25',
		'5*6' => '26',
		'5*10' => '27',
		'5*16' => '28',
		'5*20' => '29',
		'5*26' => '30',
		'5*31' => '31',
		'6*6' => '32',
		'6*7' => '33',
		'6*15' => '34',
		'6*20' => '35',
		'6*22' => '36',
		'6*35' => '37',
		'6*42' => '38',
		'6*50' => '39',
		'6*57' => '40',
		'6*63' => '41',
		'7*7' => '42',
		'7*8' => '43',
		'7*21' => '44',
		'7*35' => '45',
		'7*120' => '46',
		'8*8' => '47',
		'8*9' => '48',
		'8*28' => '49',
		'8*56' => '50',
		'8*70' => '51',
		'8*247' => '52',
        '9*9'  => '53'
	);
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('DisOrder');
        $this->CI->load->library('libcomm');
        $this->CI->load->model('dismantle_model');
        $this->order_status = $this->CI->dismantle_model->orderConfig('orders');
	}
	
	public function dismantle($order)
	{
    	if($order['playType'] == '6')
    	{
    		//单关拆票
    		$betStr = $order['codes'];
    		$dis_results = $this->CI->disorder->_dismantle_single_match($betStr);
    	}
    	elseif ($order['playType'] == '7')
    	{
    		//奖金优化
    		$betstr = $order['codes'];
    		$dis_results = $this->CI->disorder->_dismantle_optimization($betstr);
    	}
    	else
    	{
    		$betstr = $order['codes'];
    		$multi = $order['multi'];
    		$dis_results = $this->CI->disorder->_dismantle_match($betstr, $multi, $order['betTnum']);
    	}
    	$this->dismantle_JJC($order, $dis_results);
    }
    
	/**
     * 竞彩足球、竞彩篮球公共拆票方法
     * @param unknown_type $order
     * @param unknown_type $check
     */
    public function dismantle_JJC($order, $dis_results)
    {
		$endTimes = $this->CI->dismantle_model->getJjcEndtime($order['lid'], $dis_results['matchnums']);
		$betcbts = $dis_results['betcbt'];
		$sellerRates = $this->CI->dismantle_model->getTicketRate($order['lid']);
		 
		$bdata['s_data'] = array();
		$bdata['d_data'] = array();
		$bdata['ss_data'] = array();
		$bdata['sd_data'] = array();
		$count = 0;
		$this->CI->dismantle_model->trans_start();
		foreach ($betcbts as $sorder)
    	{
    		$orders = explode('|', $sorder);
    		if(empty($dis_results['ggtype'])){
                $ggtype = $orders[2];
            }else{
                $ggtype = $dis_results['ggtype'];
                $orders['1'] .= ",GG={$orders[2]}";
                $order['isChase'] = 1; //大乐透追加和足球、篮球自由过关标识
            }
    		$playType = $this->playTypeMap[$ggtype];
    		$subCodeId = isset($orders['3']) ? $orders['3'] : 0;
    		$sorder = $orders['0'] . '|' . $orders['1'];
			preg_match('/ZS=(\d+),BS=(\d+),JE=(\d+)/is', $orders['1'], $matches);
			$obetnum = $matches[1];
			$money = ParseUnit($matches[3]);
			$multi = $matches[2];
			$sub_orderId = $this->CI->libcomm->createOrderId($order['lid']);
			$mdetails = explode('*', $orders[0]);
			$needle = array();
			foreach ($mdetails as $mdetail)
			{
				$values = explode(',', $mdetail);
				array_push($bdata['ss_data'], "(?, ?, ?, ?, ?, now())");
				array_push($bdata['sd_data'], $sub_orderId);
				array_push($bdata['sd_data'], $values[0]);
				array_push($bdata['sd_data'], $order['lid']);
				array_push($bdata['sd_data'], $values[1]);
				array_push($bdata['sd_data'], $values[2]);
				$needle[] = $values[0];
			}
			$endTime = $this->getMinTime($needle, $endTimes);
			$endTime = ($endTime < $order['endTime']) ? $order['endTime'] : $endTime;
			//小单重新分配票商 2018.05.07
			$ticketSeller = $order['ticket_seller'];
			if($sellerRates) {
			    $seller = $this->CI->libcomm->getTicketSeller($sellerRates);
			    if ($seller) {
			        $ticketSeller = $seller;
			    }
			}
			array_push($bdata['s_data'], "(?, ?, '{$playType}', ?, '$subCodeId', '$sorder', '{$order['orderId']}', '$money', '$multi', '$obetnum', '{$order['isChase']}', '{$endTime}', 0, '{$ticketSeller}', 
			'{$order['real_name']}', '{$order['id_card']}', now())");
			array_push($bdata['d_data'], $sub_orderId);
			array_push($bdata['d_data'], $order['issue']);
			array_push($bdata['d_data'], $order['lid']);

			if(++$count >= 500)
			{
				$re = $this->CI->dismantle_model->saveDisOrders($bdata, 1);
				if(!$re)
				{
					$this->CI->dismantle_model->trans_rollback();
					return false;
				}
				$bdata['s_data'] = array();
				$bdata['d_data'] = array();
				$bdata['ss_data'] = array();
				$bdata['sd_data'] = array();
				$count = 0;
			}
    	}

		if($count > 0)
    	{
    		$re = $this->CI->dismantle_model->saveDisOrders($bdata, 1);
    		if(!$re)
    		{
    			$this->CI->dismantle_model->trans_rollback();
    			return false;
    		}
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$bdata['ss_data'] = array();
    		$bdata['sd_data'] = array();
	    	$count = 0;
    	}
    	//更新订单状态
    	$uporder = $this->CI->dismantle_model->updateOrdersOriStatus(array($order['orderId']), $this->order_status['drawing']);
    	if(!$uporder)
    	{
    		$this->CI->dismantle_model->trans_rollback();
    		return false;
    	}
    	$this->CI->dismantle_model->trans_complete();
    }
    
    /**
     * 返回最小的endtime
     * @param unknown_type $needle
     * @param unknown_type $times
     * @return mixed
     */
    private function getMinTime($needle, $times)
    {
    	$data = array();
    	foreach ($needle as $val)
    	{
    		$data[] = $times[$val];
    	}
    	return min($data);
    }
}
