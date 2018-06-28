<?php

class Ks
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
    	$codestr = $order['codes'];
    	$_multi = $order['multi'];
    	$result = $this->CI->disorder->dismantle_ks($codestr, $check);
    	$saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);
    	if(!empty($result['betcbt']))
    	{
	    	$bdata['s_data'] = array();
	    	$bdata['d_data'] = array();
	    	$count = 0;
	    	$this->CI->dismantle_model->trans_start();
	    	foreach ($result['betcbt'] as $betcbt)
	    	{
	    		$nmulti = 0;
	    		if($betcbt['playtype'] == 3 || ($betcbt['playtype'] == 1 && in_array($betcbt['codes'], array('3','18')))){
                    $nmulti = 40; //上海快三和值3和18、三同号单选玩法按最高40倍拆票
                }
	    		$multis = $this->CI->libcomm->disMulti($betcbt['betnum'], $_multi * $betcbt['multi'], $order['lid'], $nmulti);
		    	if(!empty($multis))
	    		{
	    			foreach ($multis as $multi)
	    			{
	    				$obetnum = $betcbt['betnum'] * $multi;
	    				$money = $obetnum * ParseUnit(2);
	    				$codes = $betcbt['codes'];
	    				array_push($bdata['s_data'], "(?, ?, ?, ?, '$codes', '{$order['orderId']}', '$money', '$multi', '{$betcbt['betnum']}', '{$order['isChase']}', '{$order['endTime']}', '{$saleTime}', '{$order['ticket_seller']}',
	    				'{$order['real_name']}', '{$order['id_card']}', now())");
	    				array_push($bdata['d_data'], $this->CI->libcomm->createOrderId($order['lid']));
	    				array_push($bdata['d_data'], $order['issue']);
	    				array_push($bdata['d_data'], $betcbt['playtype']);
	    				array_push($bdata['d_data'], $order['lid']);
	    				if(++$count >= 500)
	    				{
	    					$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, 53);
	    					if(!$re)
	    					{
	    						$this->CI->dismantle_model->trans_rollback();
	    						return false;
	    					}
	    					$bdata['s_data'] = array();
	    					$bdata['d_data'] = array();
	    					$count = 0;
	    				}
	    			}
	    		}
	    	}
	    	if($count > 0)
	    	{
	    		$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, 53);
	    		if(!$re)
	    		{
	    			$this->CI->dismantle_model->trans_rollback();
	    			return false;
	    		}
	    		$bdata['s_data'] = array();
	    		$bdata['d_data'] = array();
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
}
