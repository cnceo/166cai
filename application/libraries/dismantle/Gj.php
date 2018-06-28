<?php

class Gj
{
	private $CI;
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
		$check = false;//格式检查已废弃
		$this->dismantle_gyj_comm($order, $check);
    }
    
	/**
	 * 冠亚军拆票逻辑
	 * @param unknown_type $order
	 * @param unknown_type $check
	 */
	private function dismantle_gyj_comm($order, $check)
	{
		$betStr = $order['codes'];
		$multi = $order['multi'];
		$dis_results = $this->CI->disorder->_dismantle_gyj($betStr, $multi, $check = false);
		$betcbts = $dis_results['betcbt'];
		$bdata['s_data'] = array();
		$bdata['d_data'] = array();
		$bdata['ss_data'] = array();
		$bdata['sd_data'] = array();
		$count = 0;
		$this->CI->dismantle_model->trans_start();
		foreach ($betcbts as $sorder)
		{
			preg_match('/\|ZS=(\d+),BS=(\d+),JE=(\d+)/is', $sorder, $matches);
			$obetnum = $matches[1];
			$money = ParseUnit($matches[3]);
			$multi = $matches[2];
			$sub_orderId = $this->CI->libcomm->createOrderId($order['lid']);
			$orders = explode('|', $sorder);
			$values = explode('=', $orders[0]);
			array_push($bdata['ss_data'], "(?, ?, ?, ?, ?, now())");
			array_push($bdata['sd_data'], $sub_orderId);
			array_push($bdata['sd_data'], $values[0]);
			array_push($bdata['sd_data'], $order['lid']);
			array_push($bdata['sd_data'], $dis_results['playType']);
			array_push($bdata['sd_data'], $values[1]);
			array_push($bdata['s_data'], "(?, ?, '{$order['playType']}', ?, 0, '$sorder', '{$order['orderId']}', '$money', '$multi', '$obetnum', '{$order['isChase']}', '{$order['endTime']}', 0, '{$order['ticket_seller']}',
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
}
