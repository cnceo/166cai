<?php

class Klpk
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
        $result = $this->CI->disorder->dismantle_klpk($codestr, $check);
        $saleTime = $this->CI->dismantle_model->getNumSaleTime($order['lid'], $order['issue']);

        if(!empty($result['betcbt']))
        {
            // 合票处理
            $dis_results = $this->klpkBets($result['betcbt']);

            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;
            $this->CI->dismantle_model->trans_start();
            foreach ($dis_results as $betcbt)
            {
                // 计算倍数拆分
                $multis = $this->CI->libcomm->disMulti($betcbt['betnum'], $_multi);

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
                            $re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, 54);
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
                $re = $this->CI->dismantle_model->saveDisOrders($bdata, 0, 54);
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
     * 快乐扑克合票处理
     * @param unknown_type $betcbts
     * @return multitype:
     */
    private function klpkBets($betcbts)
    {
        $betArrs = array();
        // 需要合票的数据
        $onebets = array();
        foreach ($betcbts as $betcbt)
        {
            // 任选二及以上单式合票
            if(in_array($betcbt['playtype'], array('2', '3', '4', '5', '6')))
            {
                $onebets[$betcbt['playtype']][] = $betcbt;
            }
            else
            {
                array_push($betArrs, $betcbt);
            }
        }

        //相同类型的单式票每5注合并到一张票
        if(!empty($onebets))
        {
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
                        $tmp['codes'] = substr($tmp['codes'], 0, -1);
                        array_push($betArrs, $tmp);
                        $tmp = array();
                        $counts = 0;
                    }
                }
            
                if($counts > 0)
                {
                    $tmp['codes'] = substr($tmp['codes'], 0, -1);
                    array_push($betArrs, $tmp);
                    $onebets = array();
                    $counts = 0;
                }
            }
        }
     
        return $betArrs;
    }
}
