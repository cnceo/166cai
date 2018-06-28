<?php

class Qlc
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
    	$rballs = 7;
    	$codes = $order['codes'];
    	$ordermulti = $order['multi'];
    	$results = $this->CI->disorder->dismantle_qlc($codes, $rballs, $check);
    	$saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);
    	$betnum = $results['betnum'];
    	$betcbts = $this->qlcBets($results['betcbt']);
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$count = 0;
    	$this->CI->dismantle_model->trans_start();
    	foreach ($betcbts as $betcbt)
    	{
    		$multis = $this->CI->libcomm->disMulti($betcbt['betnum'], $ordermulti, $order['lid']);
    		if(!empty($multis))
    		{
    			foreach ($multis as $multi)
    			{
    				$obetnum = $betcbt['betnum'] * $multi;
    				$money = $obetnum * ParseUnit(2);
    				$codes = $betcbt['codes'];
    				array_push($bdata['s_data'], "(?, ?, ?, ?, '$codes', '{$order['orderId']}', '$money', '$multi', '$betcbt[betnum]', '{$order['isChase']}', '{$order['endTime']}', '{$saleTime}', '{$order['ticket_seller']}',
    				'{$order['real_name']}', '{$order['id_card']}', now())");
    				array_push($bdata['d_data'], $this->CI->libcomm->createOrderId($order['lid']));
    				array_push($bdata['d_data'], $order['issue']);
    				array_push($bdata['d_data'], $betcbt['playtype']);
    				array_push($bdata['d_data'], $order['lid']);
    				if(++$count >= 500)
    				{
	    				$re = $this->CI->dismantle_model->saveDisOrders($bdata);
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
    		$re = $this->CI->dismantle_model->saveDisOrders($bdata);
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
	
	/**
     * 七乐彩投注转化成字符串
     * @param unknown_type $betcbts
     */
    private function qlcBets($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	$counts = 0;
    	foreach ($betcbts as $betcbt)
    	{
    		$onebet = array();
    		$saltball = implode(',', $betcbt['saltball']);
    		$codes = (empty($saltball) ? '' : "$saltball#") . implode(',', $betcbt['otherball']);
    		if($betcbt['betnum'] > 1)
    		{
    			$onebet['codes'] = $codes;
    			$onebet['betnum'] = $betcbt['betnum'];
    			$onebet['playtype'] = $betcbt['playtype'];
    			array_push($betArrs, $onebet);
    		}
    		else
    		{
    			$onebets['codes'] .= $codes . '^';
    			$onebets['betnum'] += $betcbt['betnum'];
    			$onebets['playtype'] = $betcbt['playtype'];
    			if(++$counts >= 5)
    			{
    				array_push($betArrs, $onebets);
    				$onebets = array();
    				$counts = 0;
    			}
    		}
    	}
    	if($counts > 0)
    	{
    		array_push($betArrs, $onebets);
    		$onebets = array();
    		$counts = 0;
    	}
    	return $betArrs;
    }
}
