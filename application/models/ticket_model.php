<?php

class Ticket_Model extends MY_Model 
{

    private $gblids = array(53, 21406, 21407, 21408, 54, 42, 43, 55, 56, 57, 21421);

    // 出票商配置
    private $sellerMap = array(
        'qihui' =>  array(
            'cstate'    =>  1,
            'time'      =>  'qihui_bonus_time',
        ),
        'caidou' => array(
            'cstate'    =>  2,                      // 拉取标识位
            'time'      =>  'caidou_bonus_time',    // 拉取时间
        ),
    	'shancai' => array(
    		'cstate'	=>	4,
    		'time'		=> 'shancai_bonus_time',
    	),
        'huayang' => array(
            'cstate'    =>  8,
            'time'      => 'huayang_bonus_time',
        ),
        'hengju' => array(
            'cstate'    =>  16,
            'time'      => 'hengju_bonus_time',
        ),
    );

    public function __construct() 
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
    }
	
    public function getSeller()
    {
    	$sql = "select name, weight from cp_ticket_sellers where status = 1 order by weight";
    	return $this->cfgDB->query($sql)->getAll();
    }
    //专门处理疑似提交失败的订单重复提交
    public function getOrderIds($seller, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "select message_id from (select distinct(message_id) message_id, lid, endTime from {$tables['split_table']} 
    	where modified > date_sub(now(), interval 3 day) and status = ?	and (message_id is not null or message_id <> '')
    	and ticket_seller = ? and ticket_submit_time < now() and endTime > now() group by message_id) m 
    	order by m.endTime limit 5";
    	/*$sql = "select distinct(orderId), lid, endTime from cp_orders_split 
    	where created > date_sub(now(), interval 3 day) and orderId='20150528170358583500' ";*/
    	
    	return $this->cfgDB->query($sql, array($this->order_status['split_ini'], $seller))->getCol();
    }
    
    public function getTicketOrdersByMsgId($msgid, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	return $this->cfgDB->query("select orderId, message_id, sub_order_id, real_name, id_card, 
    	codes, lid, playType, money, multi, issue, betTnum, isChase from {$tables['split_table']} 
    	where message_id = ? and status = ?", array($msgid, $this->order_status['split_ini']))->getAll();
    }
    
    //专门处理需要首次提交的订单
    public function getTicketOrders($business, $lid = 0)
    {
    	$sql = $this->createTpiaoSql($lid);
    	return $this->cfgDB->query($sql, array($this->order_status['split_ini'], $business))->getAll();
    }
    
    private function createTpiaoSql($lid)
    {
    	$tables = $this->getSplitTable($lid);
    	$datetime = date('H:i:s');
    	if($datetime >= '07:50:00' and $datetime < '09:00:00')
    	{
	    	$sql = "select orderId, message_id, sub_order_id, real_name, id_card, 
	    	codes, lid, playType, money, multi, issue, betTnum, isChase from {$tables['split_table']} 
	    	where status = ? and ticket_seller = ?
	    	and (message_id is null or message_id = '') and endTime > now() and 
	    	(
		    	saleTime < now()
		    	or 
		    	(
		    		saleTime >= now() and 
		    		(
		    			(
			    			DATE_FORMAT(now(), '%H:%i') >= '07:50' and 
			    			(
			    				(lid = 53 and issue = date_format(now(), '%Y%m%d001') and DATE_FORMAT(now(), '%H:%i') <= '08:48')
			    				or
                                (lid = 56 and issue = date_format(now(), '%Y%m%d001') and DATE_FORMAT(now(), '%H:%i') <= '08:20')
                                or 
			    				(lid = 21407 and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i') <= '09:00')
			    				or
			    				(lid = 21408 and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i') <= '08:25')
			    				or 
			    				(lid = 21406 and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i:%s') <= '08:26:20')
		    					or
		    					(lid = 54 and issue = date_format(now(), '%y%m%d01') and DATE_FORMAT(now(), '%H:%i:%s') <= '08:20:50')
			    			)
		    			)
		    			or
		    			(
		    				lid not in(53, 21406, 21407, 21408, 54,56) and
		    				(
		    					DATE_FORMAT(now(), '%H:%i') >= '07:50' and DATE_FORMAT(now(), '%H:%i') <= '09:00'
		    				)
		    			)
		    		)
		    	)
	    	)	
	    	order by endTime limit 100";
    	}
    	else
    	{
    		$sql = "select orderId, message_id, sub_order_id, real_name, id_card, 
	    	codes, lid, playType, money, multi, issue, betTnum, isChase from {$tables['split_table']} 
	    	where status = ? and ticket_seller = ?
	    	and (message_id is null or message_id = '') and endTime > now() and saleTime < now()
	    	order by endTime limit 100";
    	}
    	return $sql;
    }
    
    public function saveMessageId($orders, $messageid, $lid = 0, $sellerFlag = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	return $this->cfgDB->query("update {$tables['split_table']} set message_id = ?, ticket_submit_time = now(), ticket_flag = ticket_flag ^ {$sellerFlag}  
    	where sub_order_id in('" . implode("','", $orders) . "')", array($messageid));
    }
    
    public function saveBonustime($messageid, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$this->cfgDB->query("update {$tables['split_table']} set pull_bonus_time = date_add(now(), interval 10 second)
    	where message_id = ?", array($messageid));
    }
	//票机预约成功回调函数
    public function ticket_succ($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "update {$tables['split_table']} 
    	set status = if(status < {$this->order_status['drawing']}, {$this->order_status['drawing']}, status), 
    	error_num='', 
    	ticket_time = if(status <= '{$this->order_status['drawing']}', 
    	if(date_add(now(), interval 1 minute) > endTime, date_sub(endTime, interval 10 second), date_add(now(), interval 1 minute)), 
    	ticket_time) 
    	where sub_order_id in ('" . implode("','", $datas['sids']) . "')";
    	return $this->cfgDB->query($sql, array());
    }
    //更新彩豆数据
    public function ticket_succ_cd($fields, $datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$upfields = array('sub_order_id', 'error_num', 'status');
    	$sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($datas, ',')
    		 . $this->onduplicate($fields, $upfields)
    		 . ", ticket_time = if(status <= '{$this->order_status['drawing']}', 
    		 if(values(ticket_time) > endTime, date_sub(endTime, interval 10 second), values(ticket_time)), 
    		 ticket_time)";
    	return $this->cfgDB->query($sql);
    } 

    // 更新善彩数据
    public function ticket_succ_sc($fields, $datas, $lid = 0)
    {
        $tables = $this->getSplitTable($lid);
        $upfields = array('sub_order_id', 'error_num', 'status', 'seller_order_id');
        $sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($datas, ',')
             . $this->onduplicate($fields, $upfields)
             . ", ticket_time = if(status <= '{$this->order_status['drawing']}', 
             if(values(ticket_time) > endTime, date_sub(endTime, interval 10 second), values(ticket_time)), 
             ticket_time)";
        return $this->cfgDB->query($sql);
    } 
    
    public function ticket_fail_md($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$upfiles = array('error_num');
    	$sql = "insert {$tables['split_table']}(sub_order_id, error_num, ticket_submit_time)
    	values" . implode(',', $datas['s_data']) . $this->onduplicate($upfiles, $upfiles) .
    	", ticket_submit_time = if(date_add(now(), interval 30 second) > endTime, date_sub(endTime, interval 20 second), date_add(now(), interval 30 second))";
    	return $this->cfgDB->query($sql, $datas['d_data']);
    }
    
    public function ticket_fail($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "update {$tables['split_table']} set error_num = '{$datas['error']}',
    	ticket_submit_time = if(date_add(now(), interval 30 second) > endTime, date_sub(endTime, interval 20 second), date_add(now(), interval 30 second))
    	where sub_order_id in('" . implode("','", $datas['sids']) . "')";
    	return $this->cfgDB->query($sql);
    }
    /*
     * 作者：huxm
     * 功能：彩豆预约出票失败回调函数
     * 日期：2016-03-03
     * */
    public function ticket_fail_cd($datas)
    {
    	if($datas['error'])
    	{
    		$upsql = "update cp_orders_split m join cp_orders_relation n 
    		on m.sub_order_id = n.sub_order_id set n.status = ? where m.sub_order_id in('" . implode("','", $datas['sids']) . "') 
    		and m.lid in(42, 43)";
    		$this->cfgDB->query($upsql, array($this->order_status['concel']));
    		$sql = "update cp_orders_split set error_num = '{$datas['error']}', status = '{$this->order_status['concel']}'
    		where sub_order_id in ('" . implode("','", $datas['sids']) . "')";
    		return $this->cfgDB->query($sql);
    	}
    }
    
	public function getTicketResult($seller, $concel = FALSE, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	if($concel)
    	{
    		$sql = "select * from (select message_id, lid, endTime, seller_order_id from {$tables['split_table']} where modified > date_sub(now(), interval 1 day)
				and lid not in(44, 45) and endTime < now() and status = ? and ticket_seller = ?
				order by endTime) m group by m.message_id order by m.endTime limit 5";
    	}
    	else 
    	{
    		$sql = "select * from (select message_id, lid, endTime, seller_order_id from {$tables['split_table']} where modified > date_sub(now(), interval 1 day)
				and lid not in(44, 45) and ticket_time < now() and endTime > now() and status = ? and ticket_seller = ?
				order by endTime) m group by m.message_id order by m.endTime limit 5";
    	}
    	/*$sql = "select message_id, lid from cp_orders_split where orderId = '20150601171821965513'
    	group by message_id ";*/
    	return $this->cfgDB->query($sql, array($this->order_status['drawing'], $seller))->getAll();
    }
    
    public function getTicketBonus($seller, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$lidstr = implode(', ', $this->gblids);
    	if($seller == 'qihui')
    	{
	    	//十一选五特殊处理调用    十一选五接口未通 暂时不执行
	    	$sql = "select * from (select message_id, lid, pull_bonus_time from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 2 hour)
	    	and endTime < now() and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
	    	and ((lid not in($lidstr) and cpstate = 0) or (lid in($lidstr) and cpstate=2)) and ticket_seller = ?
	    	and pull_bonus_time < now()
	    	and lid in(42, 43, 44, 45) group by message_id) mm 
	    	order by pull_bonus_time limit 100";
    	}
        elseif($seller == 'huayang')
        {
            // 增加了大乐透
            $sql = "select * from (select message_id, lid, pull_bonus_time from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 24 hour)
            and endTime < now() and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
            and ((lid not in($lidstr) and cpstate = 2) or (lid in($lidstr) and cpstate=2)) and ticket_seller = ?
            and pull_bonus_time < now()
            and lid in(42, 43, 44, 45, 23529, 21407, 56, 21408) group by message_id) mm 
            order by pull_bonus_time limit 200";
        }
    	else 
    	{
	    	$sql = "select * from (select message_id, lid, pull_bonus_time from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 2 hour)
	    	and endTime < now() and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
	    	and ((lid not in($lidstr) and cpstate = 0) or (lid in($lidstr) and cpstate=2)) and ticket_seller = ?
	    	and pull_bonus_time < now()
	    	and lid in(42, 43, 44, 45) group by message_id) mm
	    	order by pull_bonus_time limit 100";
    	}
    	return $this->cfgDB->query($sql, array($seller))->getAll();
    }
    
    public function setTicketBonus($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
    	$sql = "insert {$tables['split_table']}(" . implode(',', $fields) . ")values " . implode(',', $datas['s_datas'])
    	. $this->onduplicate($fields, array('bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate'));
    	return $this->cfgDB->query($sql, $datas['d_datas']);
    }
    
    public function getIssuesForCpBonus($ctype, $seller)
    {
        $sellerMap = $this->sellerMap;
    	$rstatus = 70;
    	if(in_array($ctype, array('sfc', 'rj')))
    	{
    		$rstatus = 60;
    	}
    	$remap = $this->bonusMap($ctype);
        // 时间字段
        $timeColumn = ($ctype == 'rj') ? $seller . '_rjbonus_time' : $sellerMap[$seller]['time'];
    	$sql = "select {$remap['issue']} from cp_{$remap['ctype']}_paiqi where 
    		   {$remap['modified']} > date_sub(now(), interval 7 day) and ({$remap['cd_bonus']} & {$sellerMap[$seller]['cstate']}) = 0 and {$remap['rstatus']} >= ? and {$timeColumn} <= date_sub(now(), interval 10 second)";
    	return $this->cfgDB->query($sql, $rstatus)->getCol();
    }
    
    //获取已开奖的排期号
    public function getSyxwIssue($type = 'syxw')
    {
    	$sql = "SELECT issue FROM cp_{$type}_paiqi where award_time <= now() and award_time > date_sub(now(), interval 30 day)
    	 and state = 0 and delect_flag=0 and try_num < 1000 and modified < date_sub(now(), interval 5 second) limit 50";
    	return $this->dc->query($sql)->getCol();
    }
    
    //修改抓取次数
    public function updateTryNum($issue, $type = 'syxw')
    {
    	$sql = "update cp_{$type}_paiqi set try_num=try_num+1 where issue=?";
    	return $this->dc->query($sql, $issue);
    }
    
    //修改排期信息
    public function updateByIssue($data, $type = 'syxw')
    {
    	$sql = "update cp_{$type}_paiqi set awardNum=?, bonusDetail=?, state=1, status=50, rstatus=50, d_synflag=0 where issue=?";
    	$re = $this->dc->query($sql, $data);
    	return $re;
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param unknown_type $type
     * @param unknown_type $lid
     * @param unknown_type $stop
     */
    public function updateStop($type, $lid, $stop)
    {
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
    
    public function setIssueStatus($ctype, $issue, $seller)
    {
        $sellerMap = $this->sellerMap;
    	$remap = $this->bonusMap($ctype);
    	$sql = "update cp_{$remap['ctype']}_paiqi set {$remap['cd_bonus']} = ({$remap['cd_bonus']} | {$sellerMap[$seller]['cstate']}) where {$remap['issue']}=?";
    	return $this->cfgDB->query($sql, array($issue));
    }

    // 更新拉取奖金时间
    public function setCdBonusTime($ctype, $issue, $seller)
    {
        $sellerMap = $this->sellerMap;
        $remap = $this->bonusMap($ctype);
        // 时间字段
        $timeColumn = ($ctype == 'rj') ? $seller . '_rjbonus_time' : $sellerMap[$seller]['time'];
        $sql = "update cp_{$remap['ctype']}_paiqi set {$timeColumn} = now() where {$remap['issue']}=?";
        return $this->cfgDB->query($sql, array($issue));
    }
    
    public function setCpstate($ctype, $issue, $seller)
    {
    	$ctype_map = array('fc3d' => 'fcsd');
    	if(array_key_exists($ctype, $ctype_map))
    		$ctype = $ctype_map[$ctype];
    	$lids = array_flip($this->orderConfig('lidmap'));
    	$cpstate_map = array('1' => '0', '3' => '2');
    	$cpstate = 3;
    	$tables = $this->getSplitTable($lids[$ctype]);
    	if(in_array($lids[$ctype], array('11', '19')))
    	{
    		$cpstate = 1;
    	}
    	$sql = "update {$tables['split_table']} set cpstate = ?, pull_bonus_time = now() where issue = ? and lid = ? and ticket_seller = ? and cpstate = ?
    	and status in({$this->order_status['win']}, {$this->order_status['notwin']}) 
    	and modified > date_sub(now(), interval 7 day)";
    	return $this->cfgDB->query($sql, array($cpstate, $issue, $lids[$ctype], $seller, $cpstate_map[$cpstate]));
    }
    
    public function setCdBonus($fields, $s_data, $d_data, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($s_data, ',');
    	$tail = array();
    	foreach ($fields as $field)
    	{
    		if(in_array($field, array('bonus_t', 'margin_t')))
    		{
    			array_push($tail, "$field = if(status in ('1000', '2000'), values($field), $field)");
    		}
    		elseif(in_array($field, array('cpstate')))
    		{
    			array_push($tail, "$field = if(cpstate < values($field), values($field), $field)");
    		}
    		else 
    		{
    			array_push($tail, "$field = values($field)");
    		}
    	}
    	if (!empty($tail))
    	{
    		$sql .= " on duplicate key update " . implode(', ', $tail);
    	}
    	return $this->cfgDB->query($sql, $d_data);	 
    }

    /*
     * 出票订单随机分配投注站
     * @date:2016-02-01
     */
    public function getBetStation($seller, $lid)
    {
        $search = array(
            'partner_name' => $seller,
            'lottery_type' => $this->getLotteryType($lid),
            'status' => '30',
            'delete_flag' => '0'
        );

        $sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone, 
        qq, webchat, other_contact, address, fail_reason, off_reason, 
        delete_flag, status, created 
        FROM cp_partner_shop
        WHERE partner_name = ? AND lottery_type = ? AND status = ? AND delete_flag = ?";

        $stationInfo = $this->db->query($sql, array($search['partner_name'], $search['lottery_type'], $search['status'], $search['delete_flag']))->getAll();
    
        $shopId = '0';
        if(!empty($stationInfo))
        {
            $stationNum = count($stationInfo) - 1;
            $stationIndex = rand(0, $stationNum);
            $shopId = $stationInfo[$stationIndex]['id'];
        }
        return $shopId;
    }

    /*
     * 获取彩种所属类型 福彩 体彩
     * @date:2016-01-27
     */
    public function getLotteryType($lid)
    {
        // 福彩：双色球，福彩3D，七乐彩
        if(in_array($lid, array('51', '52', '23528')))
        {
            $lotteryType = 1;
        }
        else
        {
            $lotteryType = 0;
        }
        return $lotteryType;
    }

    public function getSubOrdersByMsg($messageid, $concel = false, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	if($concel)
    	{
    		$sql = "select sub_order_id from {$tables['split_table']} where message_id = ? and status = ?
    				and endTime < now()";
    	}
    	else 
    	{
    		$sql = "select sub_order_id from {$tables['split_table']} where message_id = ? and status = ? 
    				and endTime > now()";
    	}
    	return $this->cfgDB->query($sql, array($messageid, $this->order_status['drawing']))->getCol();
    }
    
	public function getSubOrdersByMsg_bonus($messageid, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$lidstr = implode(', ', $this->gblids);
    	$sql = "select sub_order_id from {$tables['split_table']} where message_id = ? 
    	and ((cpstate = 0 and lid in('11', '19', '44', '45')) or (lid not in('11', '19', '44', '45') and cpstate=2))
    	and status in ? limit 100";
    	return $this->cfgDB->query($sql, array($messageid, 
    	array($this->order_status['win'], $this->order_status['notwin'])))->getCol();
    }
    
    /**
     * 过期订单设置成失败操作
     */
    public function ticketConcel($lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "select id from {$tables['split_table']} 
    	WHERE modified > date_sub(now(), interval 7 day) AND endTime<NOW() 
    	AND `status`='{$this->order_status['split_ini']}'";
    	$result = $this->cfgDB->query($sql)->getCol();
    	if(!empty($result))
    	{
    		$sql = "UPDATE {$tables['split_table']} a LEFT JOIN cp_orders_relation b ON a.sub_order_id=b.sub_order_id
    		SET a.`status`='{$this->order_status['concel']}', b.`status`='{$this->order_status['concel']}' 
    		WHERE a.id in ?";
    		return $this->cfgDB->query($sql, array($result));
    	}
    }
    /**
     * 过期订单设置成失败操作----查询失败
     */
	public function ticketConcel_searchFail($msgid, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "UPDATE {$tables['split_table']} a LEFT JOIN cp_orders_relation b ON a.sub_order_id=b.sub_order_id
    	SET a.`status`='{$this->order_status['concel']}', b.`status`='{$this->order_status['concel']}' 
    	WHERE a.message_id = ? and status = 240 and endTime < now()";
    	return $this->cfgDB->query($sql, array($msgid));
    }
    
    /**
     * 添加内容到报警表
     * @param int $ctype	报警类型
     * @param string $content	报警内容
     */
    public function insertAlert($ctype, $messageid, $content, $title = '')
    {
    	$sql = "INSERT INTO cp_alert_log
    	(ctype,title,content,status,created) VALUES (?, ?, ?, '0', NOW())";
    	$this->db->query($sql, array($ctype,$title,$content));
    }
    /**
     * 功能：彩豆提票失败订单回调
     * @param array $suborders	失败的字订单
     * 作者：huxm
     * 日期：2016-03-14
     */
    public function order_split_concel($suborders, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql2 = "update {$tables['split_table']} set 
    	ticket_submit_time = if(date_add(now(), interval 30 second) > endTime, date_sub(endTime, interval 20 second), date_add(now(), interval 30 second)) 
		where sub_order_id in('" . implode("','", $suborders) . "')";
    	return $this->cfgDB->query($sql2);
    }
    
    public function getlidbysuborderid($suborderid)
    {
    	$sql2 = "select lid, ticket_seller from cp_orders_split where sub_order_id = ?";
    	return $this->cfgDB->query($sql2, array($suborderid))->getRow();
    }
    
    public function bonusMap($ctype)
    {
    	$re = array();
    	$ctype_map = array('pls' => 'pl3', 'plw' => 'pl5', 'sfc' => 'rsfc', 'rj' => 'rsfc');
    	$re['cd_bonus'] = 'cd_bonus';
    	$re['rstatus']  = 'rstatus';
    	$re['ctype'] = $ctype;
    	$re['issue'] = 'issue';
    	$re['modified'] = 'modified';
    	if(array_key_exists($ctype, $ctype_map))
    	{
    		if($ctype == 'rj')
    		{
    			$re['cd_bonus'] = 'cd_rjbonus';
    			$re['rstatus']  = 'rjrstatus';
    		}
    		if(in_array($ctype, array('sfc', 'rj')))
    		{
    			$re['issue']  = 'mid';
    			$re['modified'] = 'created';
    		}
    		$re['ctype'] = $ctype_map[$ctype];
    	}
    	return $re;
    }
    
    public function getTicketId()
    {
    	$sql2 = "select sub_order_id, ticket_seller, lid 
			from bn_cpiao_cfg_tmp.cp_orders_split_ticketid 
			where status <> 500 and trytimes < 5 limit 100";
    	return $this->cfgDB->query($sql2)->getAll();
    }
    
    public function upTicketId($orderids)
    {
    	$sql = "update bn_cpiao_cfg_tmp.cp_orders_split_ticketid 
    	set trytimes = trytimes + 1 where sub_order_id in ?";
    	$this->cfgDB->query($sql, array($orderids));
    }
    public function getIssue($issue, $lid)
    {
        $tableArr = array(56 => 'cp_jlks_paiqi', 57 => 'cp_jxks_paiqi');
        $sql = "select issue from {$tableArr[$lid]}  where issue<$issue";
        $res = $this->cfgDB->query($sql)->getCol();
        return  $res;
    }

    // 汇总查询该彩种未拉取奖金的订单 - 六小时内
    public function countTicketBonusByLid($lid)
    {
        $tables = $this->getSplitTable($lid);
        $sql = "select count(*) from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 6 hour)
            and endTime < now() and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
            and lid = ? and cpstate < 3";
        return $this->cfgDB->query($sql, array($lid))->getOne();
    }

    // 汇总查询该票商该彩种未拉取的订单 - 六小时内 
    public function getTicketBonusByLid($seller, $lid)
    {
        $tables = $this->getSplitTable($lid);
        $sql = "select * from (select message_id, lid, pull_bonus_time from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 6 hour)
            and lid = ? 
            and endTime < now() and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
            and cpstate < 3 and ticket_seller = ?
            and pull_bonus_time < now()
            group by message_id) mm 
            order by pull_bonus_time limit 100";
        return $this->cfgDB->query($sql, array($lid, $seller))->getAll();
    }

    // 查询指定票商指定期次订单数 - 六小时内 
    public function countTicketDetailByLid($lid, $issue, $seller)
    {
        $tables = $this->getSplitTable($lid);
        $sql = "select count(*) from {$tables['split_table']} FORCE INDEX(modified) where modified > date_sub(now(), interval 6 hour)
            and status in('{$this->order_status['win']}', '{$this->order_status['notwin']}')
            and lid = ? and issue = ? and ticket_seller = ? and cpstate < 3";
        return $this->cfgDB->query($sql, array($lid, $issue, $seller))->getOne();
    }

    // 按期拉取cd_bonus未处理的期次 - 七天内
    public function getIssuesByCdBonus($ctype, $seller)
    {
        $sellerMap = $this->sellerMap;
        $rstatus = 70;
        if(in_array($ctype, array('sfc', 'rj')))
        {
            $rstatus = 60;
        }
        $remap = $this->bonusMap($ctype);
        $sql = "select {$remap['issue']} from cp_{$remap['ctype']}_paiqi where 
               {$remap['modified']} > date_sub(now(), interval 7 day) and ({$remap['cd_bonus']} & {$sellerMap[$seller]['cstate']}) = 0 and {$remap['rstatus']} >= ?";
        return $this->cfgDB->query($sql, $rstatus)->getCol();
    }

    // 排期表至split表格式转换
    public function paiqiToSplit($issue, $lid)
    {
        // 0：一致 -2：截取前两位 2：添加前两位
        $issue_format = array(
            '23529' => 2, 
            '51' => 0, 
            '21406' => 0, 
            '33' => 2, 
            '52' => 0,
            '53' => 0, 
            '35' => 2, 
            '10022' => 2, 
            '23528' => 0, 
            '11' => 2, 
            '19' => 2, 
            '21407' => 0, 
            '21408' => 0,
            '54' => 0,
            '55' => 0,
            '56' => 0,
            '21421' => 0,
        );

        if(empty($issue_format[$lid]))
        {
            return $issue;
        }
        elseif($issue_format[$lid] < 0)
        {
            // 截取前位
            return substr($issue, abs($issue_format[$lid]));
        }
        else
        {
            // 添加前位
            return substr(date('Ymd'), 0, $issue_format[$lid]) . $issue;
        } 
    }
}