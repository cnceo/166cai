<?php
class Model_Order_Check extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 90秒未出票报警
     * @param unknown_type $lids
     */
    public function check_ticket_gaopin($lids)
    {
    	if(empty($lids)) return ;
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$this->load->library('BetCnName');
    	$otherCondition = $this->db->query("select otherCondition from cp_alert_config where id = 9")->getCol();
    	$otherCondition = json_decode($otherCondition[0], true);
    	foreach ($lids as $lid)
    	{
    		if (isset($otherCondition['stoptime']) && array_key_exists($lid, $otherCondition['stoptime'])) {
    			$t0 = date('Y-m-d')." ".$otherCondition['stoptime'][$lid][0];
				$t1 = date('Y-m-d')." ".$otherCondition['stoptime'][$lid][1];
				if (($t0 < $t1 && date('Y-m-d H:i') > $t0 && date('Y-m-d H:i') < $t1) ||($t0 > $t1 && ((date('Y-m-d H:i') > $t0 || date('Y-m-d H:i') < $t1)))) continue;
    		}
    		$notickettime = array('180', '180', '180', '120', '120', '120', '90', '90'); //容错
    		if (isset($otherCondition['notickettime']) && count($otherCondition['notickettime']) == 8) $notickettime = $otherCondition['notickettime'];
    		$tables = $this->getSplitTable($lid);
    		$sql = "select * FROM
				(select lid, sub_order_id, 
				CASE 
        			WHEN created <= saleTime THEN $notickettime[0]
        			WHEN created > saleTime AND DATE_SUB(created,INTERVAL 1 MINUTE) <= saleTime THEN $notickettime[1]
        			WHEN DATE_SUB(created,INTERVAL 1 MINUTE) > saleTime AND DATE_SUB(created,INTERVAL 2 MINUTE) <= saleTime THEN $notickettime[2]
        			WHEN DATE_SUB(created,INTERVAL 2 MINUTE) > saleTime AND DATE_SUB(created,INTERVAL 3 MINUTE) <= saleTime THEN $notickettime[3]
        			WHEN DATE_SUB(created,INTERVAL 3 MINUTE) > saleTime AND DATE_SUB(created,INTERVAL 4 MINUTE) <= saleTime THEN $notickettime[4]
        			WHEN DATE_SUB(created,INTERVAL 4 MINUTE) > saleTime AND DATE_SUB(created,INTERVAL 5 MINUTE) <= saleTime THEN $notickettime[5]
        			WHEN DATE_SUB(created,INTERVAL 5 MINUTE) > saleTime AND DATE_SUB(created,INTERVAL 6 MINUTE) <= saleTime THEN $notickettime[6]
        			ELSE $notickettime[7]
        		END as settime, 
        		modified, created, saleTime, status
    				from {$tables['split_table']} where 1 and modified > date_sub(now(), interval 10 minute) and lid = ? and status < 500) as tmp 
    			where 1 and TIMESTAMPDIFF(second, if(created < saleTime, saleTime, created), now()) > settime";
    		$result = $this->slaveCfg->query($sql, array($lid))->getAll();
    		foreach ($result as $order)
    		{
    			$betName = BetCnName::getCnName($order['lid']);
    			array_push($bdata['s_data'], "('9', ?, ?, ?, now())");
    			array_push($bdata['d_data'], $order['sub_order_id']);
    			array_push($bdata['d_data'], "{$betName},{$order['settime']}秒内未出票报警");
    			array_push($bdata['d_data'], $betName . ",{$order['sub_order_id']},{$order['settime']}秒内未出票【166彩票】");
    		}
    	}
    	
    	if(!empty($bdata['s_data']))
    	{
    		$fields = array('ctype', 'ufiled', 'title', 'content', 'created');
    		$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
    		$this->db->query($sql, $bdata['d_data']);
    	}
    }
    
    public function check_ticket() {
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$this->load->library('BetCnName');
    	$lidmps = array(SSQ, DLT, QLC, QXC);
    	$lidjjcs = array(JCZQ, JCLQ);
    	$sql = "SELECT lid, sub_order_id, if (lid in (".implode(',', $lidmps)."), 10, 7) as minute
    			FROM cp_orders_split
    			WHERE ((lid IN (".implode(',', $lidmps).") AND DATE_SUB(endTime , INTERVAL 10 MINUTE) < NOW()) AND endTime > NOW()
    			    OR (lid IN (".implode(',', $lidjjcs).") AND DATE_SUB(endTime , INTERVAL 7 MINUTE) < NOW()))
    			     AND endTime > now() AND `status` < 500";
    	$result = $this->slaveCfg->query($sql)->getAll();
    	foreach ($result as $order)
    	{
    		$betName = BetCnName::getCnName($order['lid']);
    		array_push($bdata['s_data'], "('9', ?, ?, ?, now())");
    		array_push($bdata['d_data'], $order['sub_order_id']);
    		array_push($bdata['d_data'], "{$betName},截止前{$order['minute']}分钟未出票报警");
    		array_push($bdata['d_data'], $betName . ",{$order['sub_order_id']},截止前{$order['minute']}分钟未出票【166彩票】");
    	}
    	if(!empty($bdata['s_data']))
    	{
    		$fields = array('ctype', 'ufiled', 'title', 'content', 'created');
    		$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
    		$this->db->query($sql, $bdata['d_data']);
    	}
    }
    
    /**
     * 大额充值报警
     */
    public function checkPay() 
    {
    	$res = $this->slave->query("select u.uname, pl.trade_no, pl.money, pl.id from cp_pay_logs as pl INNER JOIN cp_wallet_logs as wl ON pl.trade_no=wl.trade_no
		INNER JOIN cp_user as u ON wl.uid=u.uid where pl.modified > date_sub(now() ,interval 10 MINUTE) and pl.status='1' and pl.money >= 500000 and (pl.cstate & 1)=0")->getAll();
    	if ($res) 
    	{
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$orders = array();
    		foreach ($res as $val)
    		{
    			array_push($bdata['s_data'], "(13, ?, ?, now())");
    			array_push($bdata['d_data'], '大额充值订单报警');
    			array_push($bdata['d_data'], $val['uname']."，".$val['trade_no']."，".($val['money']/100)."元，请关注线上购彩");
    			$orders[] = $val['id'];
    		}
    		
    		if(!empty($bdata['s_data']))
    		{
    			$fields = array('ctype', 'title', 'content', 'created');
    			$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
    			$res = $this->db->query($sql, $bdata['d_data']);
    			if($res)
    			{
    				$this->db->query("update cp_pay_logs set cstate = cstate | 1 where id in ('".implode("','", $orders)."')");
    			}
    		}
    	}
    }
    
    /**
     * 大额购彩报警
     */
    public function checkOrders()
    {
        $checkArr = array(
        	'200000' => '11, 19, 53, 21406, 21407, 21408, 54, 55, 56, 57, 21421',
        	'500000' => '42, 43',
        );
    	$cnArr = array(SFC => '胜负彩', RJ => '任九', JCZQ => '竞足', JCLQ => '竞篮', KS => '快三', JLKS => '吉林快三', JXKS => '江西快三', SYXW => '山东11选5', JXSYXW => '新11选5', HBSYXW => '惊喜11选5', KLPK => '快乐扑克', CQSSC => '老时时彩', GDSYXW => '乐11选5');
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$orders = array();
    	foreach ($checkArr as $key => $val) 
    	{
    		$res = $this->slave->query("SELECT o.lid, o.orderId, u.uname, o.money, o.id FROM cp_orders as o inner join cp_user as u on o.uid=u.uid WHERE o.lid in (". $val .") AND o.status >=40 AND o.money>=".$key." AND (cstate & 16)=0 AND o.pay_time > date_sub(now() ,interval 10 MINUTE)")->getAll();
    		if ($res) 
    		{
    			foreach ($res as $val) 
    			{
    				array_push($bdata['s_data'], "(14, ?, ?, now())");
    				array_push($bdata['d_data'], $cnArr[$val['lid']] . '大额购彩订单报警');
    				array_push($bdata['d_data'], $cnArr[$val['lid']]."，".$val['orderId']."，".$val['uname']."，".($val['money']/100)."元，请关注出票");
    				$orders[] = $val['id'];
    			}
    		}
    	}
    	
    	if(!empty($bdata['s_data']))
    	{
    		$fields = array('ctype', 'title', 'content', 'created');
    		$sql = "insert ignore cp_alert_log(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
    		$res = $this->db->query($sql, $bdata['d_data']);
    		if($res)
    		{
    			$this->db->query("update cp_orders set cstate = cstate | 16 where id in ('".implode("','", $orders)."')");
    		}
    	}
    }
}
?>