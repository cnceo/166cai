<?php

class Jlks
{
	private $CI;
	private $lottlyId = 56;
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
    	//$dis_results = $this->jlksBets($result['betcbt']);
    	$dis_results = $result['betcbt'];
    	$saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);
    	if(!empty($dis_results))
    	{
	    	$bdata['s_data'] = array();
	    	$bdata['d_data'] = array();
	    	$count = 0;
	    	$this->CI->dismantle_model->trans_start();
	    	foreach ($dis_results as $betcbt)
	    	{
	    		$nmulti = 0;
	    		if(in_array($betcbt['playtype'], array('1', '3'))) $nmulti = 99;
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
	    					$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, $this->lottlyId);
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
	    		$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, $this->lottlyId);
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

	/**
	 * [jlksBets 易快3和票处理]
	 * @author LiKangJian 2017-11-14
	 * @param  [type] $betcbts [description]
	 * @return [type]          [description]
	 */
	private function jlksBets($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	foreach ($betcbts as $betcbt) 
    	{
    		$onebet = array();
    		$onebet['betnum'] = $betcbt['betnum'];
    		$onebet['playtype'] = $betcbt['playtype'];
    		$onebet['codes'] = str_replace(',', '', $betcbt['codes']);
    		$onebet['multi'] = $betcbt['multi'];
            //不合票
    		if($onebet['betnum'] > 1 || in_array($onebet['playtype'],array(1,2,5,6,8)))
    		{
    			array_push($betArrs, $onebet);
    		}
    		else
    		{
    			$onebets[$onebet['playtype']][] = $onebet;
    		}
    	}
    	//相同类型的单式票每5注合并到一张票
    	foreach ($onebets as  $onebet)
    	{
    		$counts = 0;
    		$tmp = array();
    		foreach ($onebet as $v)
    		{
    			$tmp['codes'] .= $v['codes'] . '**';
    			$tmp['betnum'] += $v['betnum'];
    			$tmp['playtype'] = $v['playtype'];
    			$tmp['multi'] = $v['multi'];
    			if(++$counts >= 5)
    			{
    				array_push($betArrs, $tmp);
    				$tmp = array();
    				$counts = 0;
    			}
    		}
    		
    		if($counts > 0)
    		{
    			array_push($betArrs, $tmp);
    			$onebets = array();
    			$counts = 0;
    		}
    	}
    	//组合出最终格式
    	foreach ($betArrs as $k=>$v) 
    	{
    		switch ($v['playtype'])
    		{
    			case 3:
    			case 4:
    			case 7:
    				$betArrs[$k]['codes'] = trim(trim($v['codes'],'*'));
    				break;				
    			default:
    				break;
    		}    	
    	}

    	return $betArrs;
    }


}
