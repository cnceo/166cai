<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sms_Model extends MY_Model
{
    public function getData($mode = 1) 
    {
        switch ($mode) {
            case 2:
                $where = ' AND ctype not in (9, 18)';
                break;
            case 1:
            default:
                $where = ' AND ctype in (9, 18)';
                break;
        }
        return $this->db->query("select id, phone, ctype, content, uip, position from cp_sms_logs 
            where status = 0 AND created > DATE_SUB(NOW(),INTERVAL 2 HOUR)$where 
            order by created 
            limit 500")->getAll();
    }
    
    public function sendWin($sdate, $edate) {
        $this->load->library('BetCnName');
    	// APP消息推送加载类
    	$this->load->library('mipush');
    	$position = $this->config->item('POSITION');
    	$this->load->model('user_model');
    	//竞技彩
    	$sql = "(select o.uid, o.orderId, o.lid, o.money, o.created, u.msg_send & 4 as msgSend, u.app_push, o.margin, u.push_status,
		if (((o.activity_ids & 4)=4) && ((o.activity_status&4)=4), (select add_money from cp_activity_jj_order where orderId = o.orderId limit 1), 0) as add_money
		 from cp_orders as o
		 INNER JOIN cp_user_info as u on o.uid = u.uid
		 where o.modified >= ? and o.modified < ? and o.orderType <> 4 and o.status in ('2000') and o.my_status in(1,3) and (o.cstate & 2) = 0 
    	    and (((o.activity_ids & 4)=0) || (((o.activity_ids & 4)=4) && ((o.activity_status&4)=4))) and o.lid in (42, 43)
    	    limit 200)
    	    union 
    	 (select o.uid, o.orderId, o.lid, o.money, o.created, u.msg_send & 4 as msgSend, u.app_push, o.margin, u.push_status, 0 as add_money
		 from cp_orders as o
		 INNER JOIN cp_user_info as u on o.uid = u.uid
		 where o.modified >= ? and o.modified < ? and o.orderType <> 4 and o.status in ('2000') and o.my_status in(1,3) and (o.cstate & 2) = 0 and lid not in (42, 43)
    	    limit 300)";
    	$orders = $this->db->query($sql, array($sdate, $edate, $sdate, $edate))->getAll();
    	while (!empty($orders)) {
    	    //加奖的彩种
    	    $lidgaopin = array('53', '54', '56', '57', '21421');
    	    $oidArr = array();
    	    $orderData = array();
    	    $smsData = array();
    	    $pushDatas = array();
    	    foreach ($orders as $order) {
    	        if (in_array($order['lid'], $lidgaopin)) {
    	            $oidArr[$order['lid']][] = $order['orderId'];
    	        } else {
    	            $time = strtotime($order['created']);
    	            $vdatas = array('#MM#' => date('m', $time), '#DD#' => date('d', $time),
    	                '#HH#' => date('H', $time), '#II#' => date('i', $time), '#LID#' => BetCnName::getCnName($order['lid']),
    	                '#MONEY#' => $order['margin']);
    	            $ctype = 'win_prize';
    	            if($order['add_money'] > 0) {
    	                $vdatas['#ADDS#'] = $order['add_money'];
    	                $ctype = 'win_prize_jj';
    	            }
    	            if(($order['msgSend'] & 4) == 0) {
    	                $sendData = $this->user_model->sendSms($order['uid'], $vdatas, $ctype, null, '127.0.0.1', '192', false);
    	                array_push($smsData, $sendData);
    	            }
    	             
    	            // APP消息推送 中奖
    	            if( $order['push_status'] ) {
    	                $pushData = $this->mipush->getSendData('user', array(
    	                    'type'      =>  $ctype,
    	                    'uid'       =>  $order['uid'],
    	                    'lid'       => 	$order['lid'],
    	                    'lname'		=> 	BetCnName::getCnName($order['lid']),
    	                    'orderId'   => 	$order['orderId'],
    	                    'money'     => 	number_format(ParseUnit($order['margin'], 1), 2),
    	                    'time'      => 	$order['created'],
    	                    'trade_no'  => 	'',
    	                    'add_money' => 	isset($order['add_money']) ? number_format(ParseUnit($order['add_money'], 1), 2) : '0',
    	                ));
    	                array_push($pushDatas, $pushData);
    	            }
    	        }
    	        $orderData[$order['orderId']] = $order;
    	    }
    	    foreach ($oidArr as $lid => $oids) {
    	        $table = $this->getSplitTable($lid);
    	        $gpaddmoney = $this->cfgDB->query("select sum(otherBonus) as add_money, orderId from {$table['split_table']} where orderId in ? group by orderId", array($oids))->getAll();
    	        if (!empty($gpaddmoney)) {
    	            foreach ($gpaddmoney as $val) {
    	                $time = strtotime($orderData[$val['orderId']]['created']);
    	                $vdatas = array('#MM#' => date('m', $time), '#DD#' => date('d', $time), '#ADDS#' => $val['add_money'],
    	                    '#HH#' => date('H', $time), '#II#' => date('i', $time), '#LID#' => BetCnName::getCnName($orderData[$val['orderId']]['lid']),
    	                    '#MONEY#' => $orderData[$val['orderId']]['margin']);
    	                $ctype = 'win_prize';
    	                if ($val['add_money'] > 0) $ctype = 'win_prize_jj';
    	                
    	                if(($orderData[$val['orderId']]['msgSend'] & 4) == 0) {
    	                    $sendData = $this->user_model->sendSms($orderData[$val['orderId']]['uid'], $vdatas, $ctype, null, '127.0.0.1', '192', false);
    	                    array_push($smsData, $sendData);
    	                }
    	            
    	                // APP消息推送 中奖
    	                if( $orderData[$val['orderId']]['push_status'] ) {
    	                    $pushData = $this->mipush->getSendData('user', array(
    	                        'type'      =>  $ctype,
    	                        'uid'       =>  $orderData[$val['orderId']]['uid'],
    	                        'lid'       => 	$orderData[$val['orderId']]['lid'],
    	                        'lname'		=> 	BetCnName::getCnName($orderData[$val['orderId']]['lid']),
    	                        'orderId'   => 	$orderData[$val['orderId']]['orderId'],
    	                        'money'     => 	number_format(ParseUnit($orderData[$val['orderId']]['margin'], 1), 2),
    	                        'time'      => 	$orderData[$val['orderId']]['created'],
    	                        'trade_no'  => 	'',
    	                        'add_money' => 	isset($val['add_money']) ? number_format(ParseUnit($val['add_money'], 1), 2) : '0',
    	                    ));
    	                    array_push($pushDatas, $pushData);
    	                }
    	            }
    	        }
    	    }
    	    if (!empty($smsData)) $this->tools->saveSms($smsData);
    	    if (!empty($pushDatas)) $this->mipush->recodeUserLogs($pushDatas);
    	    $this->db->query('update cp_orders set cstate = cstate | 2 where orderId in ?', array(array_keys($orderData)));
    	    $orders = $this->db->query($sql, array($sdate, $edate, $sdate, $edate))->getAll();
    	}
    }
}