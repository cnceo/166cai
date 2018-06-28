<?php

class Dlt
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
    	$parm['rballs'] = 5;	//红球数量
    	$parm['bballs'] = 2;	//蓝球数量
    	if($order['isChase'])
    	{
    		$parm['money'] = 3;	//追加单注金额
    	}
    	else
    	{
    		$parm['money'] = 2;	//普通单注金额
    	}
    	$this->commSsqAndDlt($order, $parm, $check);
    }
    
	/**
     * 双色球和大乐透公用拆票方法
     * @param array $order
     * @param array $parm
     * @param boolean $check
     */
    public function commSsqAndDlt($order, $parm, $check = false, $lid = '')
    {
    	$rballs = $parm['rballs'];
    	$bballs = $parm['bballs'];
    	$codes = $order['codes'];
    	$ordermulti = $order['multi'];
    	$results = $this->CI->disorder->dismantle_ball($codes, $rballs, $bballs, $check);
    	if($lid == '51')
    	{
    		//$results = $this->dismantle_bball($results);
    		$results = $this->dismantle_tballs($results);
    	}
    	$saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);
    	$betnum = $results['betnum'];
    	$betcbts = $this->cbeBets($results['betcbt']);
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$count = 0;
    	$this->CI->dismantle_model->trans_start();
    	foreach ($betcbts as $betcbt)
    	{
    		//双色球胆拖倍数拆成1倍  （临时修改 待票机恢复后删除）
    		$ticket_seller = $order['ticket_seller'];
    		if($order['lid'] == '51' && $betcbt['playtype'] == '135' && $this->check_codes($betcbt['codes']))
    		{
    			$ticket_seller = 'caidou';
    		}
    		if($order['lid'] == '51' && $betcbt['playtype'] == '135' && $ordermulti > 1)
    		{
    			$multis = $this->CI->libcomm->calBets($ordermulti, 1);
    		}
    		else
    		{
    			$multis = $this->CI->libcomm->disMulti($betcbt['betnum'], $ordermulti, $order['lid']);
    		}
    		if(!empty($multis))
    		{
    			foreach ($multis as $multi)
    			{
    				$obetnum = $betcbt['betnum'] * $multi;
    				$money = $obetnum * ParseUnit($parm['money']);
	    			$codes = $betcbt['codes'];
	    			array_push($bdata['s_data'], "(?, ?, ?, ?, '$codes', '{$order['orderId']}', '$money', '$multi', '$betcbt[betnum]', '{$order['isChase']}', '{$order['endTime']}', '{$saleTime}', '{$ticket_seller}', 
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
     * 双色球和大乐透投注数组转换成字符串
     * @param unknown_type $betcbts
     */
    private function cbeBets($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	$counts = 0;
    	foreach ($betcbts as $betcbt)
    	{
    		$onebet = array();
    		$saltball = implode(',', $betcbt['saltball']);
    		$bsaltball = implode(',', $betcbt['bsaltball']);
    		$codes = (empty($saltball) ? '' : "$saltball#") . implode(',', $betcbt['otherball'])
    				 . '|' . (empty($bsaltball) ? '' : "$bsaltball#") . implode(',', $betcbt['bluball']);
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
    
	//双色球将篮球拆成单式
    private function dismantle_bball($results)
    {
    	foreach ($results['betcbt'] as $in => $betcbt)
    	{
    		$bnum = count($betcbt['bluball']);
    		$presaltnum = count($betcbt['saltball']);
    		if($presaltnum > 0 && $bnum > 1)
    		{
    			$bluballs = $betcbt['bluball'];
    			$betnum = $betcbt['betnum'];
    			foreach ($bluballs as $bluball)
    			{
    				$betcbt['bluball'] = array($bluball);
    				$betcbt['betnum']  = $betnum / $bnum;
    				array_push($results['betcbt'], $betcbt);
    			}
    			unset($results['betcbt'][$in]);
    		}
    	}
    	return $results;
    }
    
    //双色球拖超20的拆分
	private function dismantle_tballs($results)
    {
    	foreach ($results['betcbt'] as $in => $betcbt)
    	{
    		$snum = count($betcbt['saltball']);
    		$tnum = count($betcbt['otherball']);
    		if($snum == 5 && $tnum > 19)
    		{
    			$tballs = array_chunk($betcbt['otherball'], 14);
    			foreach ($tballs as $tball)
    			{
    				$betcbt['otherball'] = $tball;
    				$betcbt['betnum']  = count($tball) * count($betcbt['bluball']);
    				array_push($results['betcbt'], $betcbt);
    			}
    			unset($results['betcbt'][$in]);
    		}
    	}
    	return $results;
    }
    
	private function check_codes($code)
    {
    	$codestr = explode('|', $code);
    	$codes = explode('#', $codestr[0]);
    	$cnums = explode(',', $codes[1]);
    	return count($cnums) > 20 ? true : false;
    }
}
