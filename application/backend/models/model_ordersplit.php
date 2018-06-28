<?php

class Model_OrderSplit extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：orderId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function getSplitTableByOrder($orderId, $lid = '')
    {
    	$tables = $this->getSplitTable($lid);
        $date = substr($orderId, 0, 4) . '-' . substr($orderId, 4, 2) . '-' . substr($orderId, 6, 2);
        $table = $this->decideMonthTable($tables['split_table'], $date);

        return $table;
    }

    /**
     * 参    数：orderId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getSplitDetailByOrder($orderId, $lid)
    {
        $selectMap = array(
            'sub_order_id' => 'subId',
            'codes'        => 'content',
            'ticket_money' => 'ticketMoney',
            'ticket_time'  => 'ticketTime',
        	'ticket_seller'=> 'ticketSeller',
            'lid'          => 'lid',
            'money'        => 'money',
            'multi'        => 'multi',
            'betTnum'      => 'betNum',
            'status'       => 'status',
        	'cancelFlag'   => 'cancelFlag',
            'bonus_detail' => 'bonusDetail',
            'bonus'        => 'bonus',
            'cpstate'        => 'cpstate',
            'margin'       => 'margin',
            'bonus_t'      => 'ticketBonus',
            'margin_t'     => 'ticketMargin',
            'error_num'    => 'error_num',
        );
        $selectStr = $this->composeSelectStr($selectMap);
        $this->slaveCfg1->select($selectStr);
        $this->slaveCfg1->where('orderId', $orderId);
        $table = $this->getSplitTableByOrder($orderId, $lid);
        $result = $this->slaveCfg1->get($table)->result_array();
        $subOrders = $result[0] ? $result[0] : array();

        return $subOrders;
    }

    /**
     * 参    数：orderId
     * 作    者：刁寿钧
     * 功    能：由订单号得到出票订单编号
     * 修改日期：2015-07-29
     */
    public function getMessageId($orderId, $lid)
    {
        $table = $this->getSplitTableByOrder($orderId, $lid);
        $sql = "SELECT message_id FROM $table WHERE orderId = ?";
        $messageId = $this->slaveCfg1->query($sql, $orderId)->getOne();

        return $messageId;
    }

    /**
     * 参    数：orderId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function consistencyInfo($id, $orderId)
    {
        $splitTb = $this->getSplitTableByOrder($orderId, $id);
        $conTb = 'cp_orders_inconsistent';
        $this->slaveCfg1->select("{$splitTb}.orderId splitId, {$conTb}.orderId conId");
        $this->slaveCfg1->from($splitTb);
        $this->slaveCfg1->join($conTb, "{$splitTb}.orderId = {$conTb}.orderId AND {$conTb}.distributed = 1", 'left');
        $this->slaveCfg1->where("{$splitTb}.orderId", $orderId);
		if(in_array($id, array('21406', '21407', '53', '56', '57', '21408', '54', '42', '43', '55', '21421')))
		{
		    $this->slaveCfg1->where("{$splitTb}.cpstate", 4);
		}
		else
		{
		    $this->slaveCfg1->where("{$splitTb}.cpstate", 2);
		}
		$resultAry = $this->slaveCfg1->get()->row_array();
        if (empty($resultAry[0]))
        {
            $consistencyInfo = '未比对';
        }
        elseif (empty($resultAry[0]['conId']))
        {
            $consistencyInfo = '一致';
        }
        else
        {
            $consistencyInfo = '不一致';
        }

        return $consistencyInfo;
    }
    
    /**
     * 获得票商
     */
    public function getSeller()
    {
    	$sql = "select id, name, cname from cp_ticket_sellers";
    	return $this->slaveCfg1->query($sql)->getAll();
    }
    
    public function getAllRates()
    {
    	$sql = "SELECT lid, GROUP_CONCAT(CONCAT_WS(':',ticketSeller,CAST(ticketRate AS char)) ORDER BY ticketSeller SEPARATOR '|') as ticketRate, mark
    			 FROM cp_seller_rate GROUP BY lid";
    	return $this->slaveCfg1->query($sql)->getAll();
    }
    
    public function modifyRates($lid, $rate)
    {
    	foreach ($rate as $key => $rt)
    	{
    		$sql = "update cp_seller_rate set ticketRate='{$rt}' where ticketSeller = ? and lid = ?";
    		$this->cfgDB->query($sql, array($key, $lid));
    	}
    }
    
    public function modifytickselrMark($marks)
    {
        foreach ($marks as $lid => $mark)
        {
            $sql = "update cp_seller_rate set `mark` = ? where lid = ?";
            $this->cfgDB->query($sql, array($mark, $lid));
        }
    }
    
    public function gettictselrData($start, $end)
    {
    	$split_lid = $this->config->item('split_lid');
    	$split_lid[] = '';
    	$res = array();
    	foreach ($split_lid as $lid) {
            $startTable = $this->getSplitTableByOrder($start, $lid);
            $endTable = $this->getSplitTableByOrder($end, $lid);
            $startTable = $this->judgeTableExit($startTable, $start, $lid);
            $endTable = $this->judgeTableExit($endTable, $end, $lid);
            if ($startTable == $endTable)
    		{
    			$sql = "SELECT SUM(money) as money, lid, ticket_seller
    			FROM {$startTable}
    			WHERE orderId > '{$start}' and orderId < '{$end}'
    			GROUP BY lid, ticket_seller";
    		}else {
	    		$sql = "SELECT SUM(money) as money, lid, ticket_seller
	    		FROM {$startTable}
	    		WHERE orderId > '{$start}' and orderId < '{$end}'
	    		UNION
	    		SELECT SUM(money) as money, lid, ticket_seller
	    		FROM {$endTable}
	    		WHERE orderId > '{$start}' and orderId < '{$end}'
	    		GROUP BY lid, ticket_seller";
    		}
    		$res = array_merge($res, $this->slaveCfg1->query($sql)->getAll());
    	}
    	return $res;
    }
    
    public function getOrders($lid, $start, $end)
    {
    	$table = $this->getSplitTable($lid);
    	$sql = "select ticket_seller, issue, orderId, real_name, ticket_time, bonus, margin, ticket_money, bonus_t, margin_t 
    	from {$table} where lid='{$lid}' and created > '{$start}' and created < '{$end}' and status in ('500', '1000', '2000')";
    	return $this->slaveCfg1->query($sql)->getAll();
    }
    
    /**
     * 出票监控查询
     * @param unknown_type $searchData
     */
    public function fetchTicketingOrders($searchData, $page, $pageCount)
    {
    	$where = 'where 1';
    	$leftJoin = '';
    	$springTime = $this->dc->query('select delay_start_time start, DATE_ADD(delay_end_time,INTERVAL 1 DAY) end from cp_issue_rearrange where lid = "ssq"')->getRow();
    	$daynum = ($springTime['start'] < date('Y-m-d H:i:s') && $springTime['end'] > date('Y-m-d H:i:s')) ? 7 : 3;
    	$date = date('Y-m-d H:i:s', strtotime("$daynum days ago midnight"));
    	$where .= " and s.modified >= '{$date}'";
    	$where .= $this->condition("s.lid", $searchData['lid']);
        // 大订单或者子订单
        if($this->emp($searchData['orderId'])) $where .= " and (s.orderId = '{$searchData[orderId]}' or s.sub_order_id = '{$searchData[orderId]}')";
        
        if($this->emp($searchData['ticket_seller'])) $where .= $this->condition("s.ticket_seller", $searchData['ticket_seller']);
        
    	if ($this->emp($searchData['status'])) $where .= $this->condition("s.status", $searchData['status']);
    	else $where .= " and s.status in ('0', '240')";
    	
    	if ($this->emp($searchData['errNum'])) $where .= " and s.error_num > '0' and s.error_num not in('0_0','1_0','200021','0000','6_0')";
    	
        if(!empty($searchData['issue'])) $where .= " and s.issue = '{$searchData['issue']}'";
        
        if ($this->emp($searchData['havnotendTime'])) $where .= " and s.endTime = '{$searchData['havnotendTime']}'";
        
        if ($this->emp($searchData['ticketed'])) $where .= " and (LOG2(s.ticket_flag) % 1 > 0 or s.ticket_seller = '')";
        
    	if($this->emp($searchData['playType']) || $this->emp($searchData['mid'])) {
    		$leftJoin = " left join cp_orders_relation r on r.sub_order_id = s.sub_order_id";
    		$where .= $this->condition("r.ptype", $searchData['playType']);
    		$where .= $this->condition("r.mid", $searchData['mid']);
    	}

        // 19:30 - 09:00 按saleTime升序查询
        $n = date('Y-m-d') . ' 19:30:00';
        $t = date('Y-m-d', strtotime("+1 day")) . ' 09:00:00';
        if(date('Y-m-d H:i:s') >= $n && date('Y-m-d H:i:s') <= $t) $orderBy = 'saleTime';
        else $orderBy = 'endTime';
        
        $table = $this->getSplitTable($searchData['lid']);
        
    	$sql = "select DISTINCT s.orderId, s.lid, s.sub_order_id, s.created, s.endTime, s.money, s.status, s.ticket_seller, s.error_num, s.saleTime, s.ticket_flag
    	from {$table['split_table']} s
    		$leftJoin $where order by lid, $orderBy asc limit " . ($page - 1) * $pageCount . "," . $pageCount;
    		$orders = $this->slaveCfg1->query($sql)->getAll();
    	$countSql = "select count(DISTINCT s.sub_order_id) num from {$table['split_table']} s $leftJoin $where";
    	$count = $this->slaveCfg1->query($countSql)->getOne();
    
    	return array($orders, $count);
    }
    
    /**
     * 手工撤单操作
     * @param unknown_type $orderIds
     */
    public function orderCancel($orderIds)
    {
    	$nlid = array_flip($this->orderConfig('nlid'));
    	$sql = "update #TABLE# s left join cp_orders_relation r on s.sub_order_id=r.sub_order_id
    		set s.status='600', s.cancelFlag='1', r.status='600'
    		where s.sub_order_id in ? and s.status in('0', '240')";
    	if($orderIds)
    	{
    		$orderIds = explode(',', $orderIds);
    		foreach ($orderIds as $orderId) {
    			$lid = $nlid[substr($orderId, -2)];
    			$stable = $this->getSplitTable($lid);
    			$data[$stable['split_table']][] = $orderId;
    		}
    	}
    	$this->cfgDB->trans_start();
    	foreach ($data as $k => $val) {
    		if (!$this->cfgDB->query(str_replace('#TABLE#', $k, $sql), array($val))) {
    			$this->cfgDB->trans_rollback();
    			return false;
    		}
    	}
    	$this->cfgDB->trans_complete();
    	return true;
    }
    
    /**
     * 手工切换票商操作
     * @param unknown_type $orderIds
     */
    public function orderTicket($orderIds, $ticketSeller)
    {
    	$nlid = array_flip($this->orderConfig('nlid'));
    	$sql = "update #TABLE# set message_id='', status='0', error_num='', ticket_flag = '0', ticket_seller = ?
    	where sub_order_id in ? and status in('0', '240')";
    	if($orderIds)
    	{
    		$orderIds = explode(',', $orderIds);
    		foreach ($orderIds as $orderId) 
    		{
    			$lid = $nlid[substr($orderId, -2)];
    			$stable = $this->getSplitTable($lid);
    			$data[$stable['split_table']][] = $orderId;
    		}
    	}
    	$this->cfgDB->trans_start();
    	foreach ($data as $k => $val) 
    	{
    	    $this->cfgDB->query(str_replace('#TABLE#', $k, $sql), array($ticketSeller, $val));
    		if (!$this->cfgDB->affected_rows()) 
    		{
    			$this->cfgDB->trans_rollback();
    			return false;
    		}
    	}
    	$this->cfgDB->trans_complete();
    	return true;
    }
    
    /**
     * 出票详情
     */
    public function getJjcOrderDetail($orderId)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$sql = "SELECT s.lid,s.sub_order_id,s.ticket_money,s.ticket_time,s.ticket_seller,s.codes,s.money,s.multi,s.betTnum,s.status,s.bonus_detail,s.bonus,s.margin,s.cpstate,s.bonus_t,s.margin_t,s.error_num,r.mid,r.pdetail
    	FROM cp_orders_split{$suffix} s JOIN cp_orders_relation{$suffix} r ON s.sub_order_id=r.sub_order_id WHERE s.orderId=?";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	foreach ($res as $val)
    	{
	    	$data[$val['sub_order_id']]['subId'] = $val['sub_order_id'];
	    	$data[$val['sub_order_id']]['content'] = $val['codes'];
	    	$data[$val['sub_order_id']]['ticketMoney'] = $val['ticket_money'];
	    	$data[$val['sub_order_id']]['ticketTime'] = $val['ticket_time'];
	    	$data[$val['sub_order_id']]['ticketSeller'] = $val['ticket_seller'];
	    	$data[$val['sub_order_id']]['lid'] = $val['lid'];
	    	$data[$val['sub_order_id']]['money'] = $val['money'];
	    	$data[$val['sub_order_id']]['multi'] = $val['multi'];
	    	$data[$val['sub_order_id']]['betNum'] = $val['betTnum'];
	    	$data[$val['sub_order_id']]['status'] = $val['status'];
	    	$data[$val['sub_order_id']]['bonusDetail'] = $val['bonus_detail'];
	    	$data[$val['sub_order_id']]['bonus'] = $val['bonus'];
	    	$data[$val['sub_order_id']]['cpstate'] = $val['cpstate'];
	    	$data[$val['sub_order_id']]['margin'] = $val['margin'];
	    	$data[$val['sub_order_id']]['ticketBonus'] = $val['bonus_t'];
	    	$data[$val['sub_order_id']]['ticketMargin'] = $val['margin_t'];
            $data[$val['sub_order_id']]['error_num'] = $val['error_num'];
	    	$data[$val['sub_order_id']]['info'][$val['mid']] = $val['pdetail'];
    	}
    
    	return $data;
    }
    
    public function getStatics() {
        $res = $this->slaveCfg1->query("select lid, wait, draw, problem, havenot, summoney, endTime, cwait, uwait from cp_ticket_statistics")->getAll();
    	foreach ($res as $val) {
    		$result[$val['lid']] = $val;
    	}
    	return $result;
    }
    
    /**
     * 判断表是否存在，不存在使用下个月
     * @param string $table
     * @param string $data
     * @param string $lid
     * @return string 表名
     */
    private function judgeTableExit($table, $data, $lid)
    {
        $i = 1;
        while (!$this->slaveCfg1->query("SHOW TABLES LIKE '" . $table . "'")->getOne())
        {
            $date = substr($data, 0, 4) . '-' . substr($data, 4, 2) . '-' . substr($data, 6, 2);
            $s = date("YmdHis", strtotime("$date +" . ($i++) . " month")) . '000000';
            $table = $this->getSplitTableByOrder($s, $lid);
        }
        return $table;
    }
    
    public function getEndTimeByLid($lid) {
        return $this->slaveCfg1->query("select endTime from cp_ticket_statistics where lid = ?", array($lid))->getOne();
    }

}