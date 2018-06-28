<?php

class Syxw
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('DisOrder');
        $this->CI->load->library('createcheck/SyxwCheck');
        $this->CI->load->library('libcomm');
        $this->CI->load->model('dismantle_model');
        $this->order_status = $this->CI->dismantle_model->orderConfig('orders');
	}
	
	public function dismantle($order)
	{
		$check = false;//格式检查已废弃
    	$codestr = $order['codes'];
    	$_multi = $order['multi'];
    	$result = $this->CI->disorder->dismantle_syxw($codestr, $check, $order['lid']);
    	$saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);
    	$dis_results = $this->syxwBets($result['betcbt']);
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$count = 0;
    	$this->CI->dismantle_model->trans_start();
    	//针对恒炬十一选五乐选玩法不接票切其他票商
    	$allSeller = $this->CI->dismantle_model->getRetByLid('21406');
    	foreach ($dis_results as $betcbt)
    	{
            $seller = '';
    		$multis = $this->CI->libcomm->disMulti($betcbt['betnum'], $_multi);
    		if(!empty($multis))
    		{
    			foreach ($multis as $multi)
    			{
    				$obetnum = $betcbt['betnum'] * $multi;
    				$money = $obetnum * ParseUnit($this->getMoney($betcbt['playtype']));
    				$codes = $betcbt['codes'];
                    $seller = $order['ticket_seller'];
                    if ($order['ticket_seller'] == 'hengju' && in_array($betcbt['playtype'], array(13, 14, 15))) {
                        foreach ($allSeller as $seller) {
                            if($seller !== 'hengju') break;
                        }
                    }
    				array_push($bdata['s_data'], "(?, ?, ?, ?, '$codes', '{$order['orderId']}', '$money', '$multi', '$betcbt[betnum]', '{$order['isChase']}', '{$order['endTime']}', '{$saleTime}', '{$seller}',
    				'{$order['real_name']}', '{$order['id_card']}', now())");
    				array_push($bdata['d_data'], $this->CI->libcomm->createOrderId($order['lid']));
    				array_push($bdata['d_data'], $order['issue']);
    				array_push($bdata['d_data'], $betcbt['playtype']);
    				array_push($bdata['d_data'], $order['lid']);
    				if(++$count >= 500)
    				{
	    				$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, $order['lid']);
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
    		$re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, $order['lid']);
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
     * [getMoney 对乐3乐4乐5特殊处理]
     * @author JackLee 2017-04-12
     * @param  [type] $playtype [description]
     * @return [type]           [description]
     */
    private function getMoney($playtype)
    {
        $money = 0;
        switch ($playtype)
        {
            case 13:
              $money = 6;
              break;
            case 14:
              $money = 10;
              break;
            case 15:
              $money = 14;
              break;
            default:
              $money = 2;
              break;
        }
        return $money;
    }
	/**
     * 十一选五合票处理
     * @param unknown_type $betcbts
     * @return multitype:
     */
	private function syxwBets($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	foreach ($betcbts as $betcbt)
    	{
    		$onebet = array();
    		$onebet['betnum'] = $betcbt['betnum'];
    		$onebet['playtype'] = $betcbt['playtype'];
    		switch ($betcbt['playtype'])
    		{
    			case 1:
                case 14:
                case 15:
    				$onebet['codes'] = implode(',', $betcbt['balls']); 
    				break;
                case 13:
                    $onebet['codes'] = implode('*', $betcbt['balls']); 
                    break;
    			case 9:
    			case 10:
    				$codes_arr = array();
    				foreach ($betcbt['balls'] as $bet)
    				{
    					$codes_arr[] = implode(',', $bet);
    				}
    				$onebet['codes'] = implode('*', $codes_arr);
    				break;
    			default:
    				if(!$betcbt['isSalts'])
    				{
    					$onebet['codes'] = implode(',', $betcbt['balls']);
    				}
    				else
    				{
    					$salts = implode(',', $betcbt['salts']);
    					$onebet['codes'] = (empty($salts) ? '' : "$salts#") . implode(',', $betcbt['balls']);
    				}
    				break;
    		}
            //不合票
    		if($onebet['betnum'] > 1 || in_array($onebet['playtype'],array(1,13,14,15)))
    		{
    			array_push($betArrs, $onebet);
    		}
    		else
    		{
    			$onebets[$onebet['playtype']][] = $onebet;
    		}
    	}
    	
    	//相同类型的单式票每5注合并到一张票
    	foreach ($onebets as $onebet)
    	{
    		$counts = 0;
    		$tmp = array();
    		foreach ($onebet as $v)
    		{
    			$tmp['codes'] .= $v['codes'] . '^';
    			$tmp['betnum'] += $v['betnum'];
    			$tmp['playtype'] = $v['playtype'];
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
    	return $betArrs;
    }
}
