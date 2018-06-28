<?php

class BackAward_Model extends MY_Model 
{
    public function __construct() 
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
    }
    
	public function compareBonus($lids = 0)
	{
		$tables = $this->getSplitTable($lids);
		$lid = "'21406', '21407', '21408', '53', '54', '55','56','57'";
		$split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp";
		$check_sql_sub = "
		select orderId from {$tables['split_table']} force index(modified) 
		where modified > date_sub(now(), interval 1 hour) and 
		status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') 
		and cpstate = 1 and lid in(11,19,44,45) group by orderId limit 1000";
		$check_sql = "
		select m.orderId, m.lid, m.issue, sum(1) onum, 
		sum(if(m.lid in ({$lid}), if(m.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0), 
		if(m.cpstate in(1,2) or (m.status = '{$this->order_status['concel']}'), 1, 0))) cnum
		from {$tables['split_table']} m 
		join ($check_sql_sub) n 
		on m.orderId = n.orderId
		group by m.orderId
		having onum = cnum";
		$orderIds = $this->cfgDB->query($check_sql)->getAll();
		if(!empty($orderIds))
		{
			$this->trans_start();
			$this->cfgDB->query("truncate $split_cmp_table");
			//插入可能需要对比奖金的数据
			$this->insert_select($split_cmp_table, $check_sql);
			//补休其他数据
			$fields = array('orderId', 'bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney');
			$msql = $isql = "insert $split_cmp_table (". implode(',', $fields) .")
			select n.orderId, n.bonus, n.bonus_t, n.margin, n.margin_t, 1 as onum, if(n.lid in ({$lid}), 
			if(n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0), 
			if(((n.bonus = n.bonus_t) && (n.margin = n.margin_t) && 
			n.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') && n.cpstate in(1,2)) or (n.status = '{$this->order_status['concel']}'), 1, 0)) as num,
			if(n.cpstate in(1,2) or (n.status = '{$this->order_status['concel']}'), 1, 0) as cnum,
			if(n.status={$this->order_status['concel']}, n.money, 0) failMoney
			from $split_cmp_table m join {$tables['split_table']} n 
			on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney'), 
			array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney'), "{$split_cmp_table}.");
			$this->cfgDB->query($msql);
			//更新订单原表
			$usql = "update cp_orders_ori m 
			join $split_cmp_table n on m.orderId = n.orderId and m.status <> '{$this->order_status['concel']}'
			left join cp_orders_inconsistent o on m.orderId = o.orderId
			set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'), 
			m.bonus = n.bonus, m.margin = n.margin,	m.synflag = 0, m.win_time = if(n.bonus > 0, now(), 0),
			m.failMoney = n.failMoney
			where n.onum = n.num or o.dispatch = 1";
			$re1 = $this->cfgDB->query($usql);
			//更新split表
			$usql = "update {$tables['split_table']} m 
			join $split_cmp_table n on m.orderId = n.orderId
			left join cp_orders_inconsistent o on m.orderId = o.orderId
			set m.cpstate = if(n.lid in ({$lid}), if(n.onum = n.num, 2, m.cpstate), 
			if((n.onum = n.num || o.dispatch = 1 || n.onum = n.cnum), 2, m.cpstate))";
			$re2 = $this->cfgDB->query($usql);
			//对比不一致数据存储
			/*$this->cfgDB->query("update cp_check_distribution m join $split_cmp_table n on m.lottery_id = n.lid and m.issue = n.issue
			set m.unmatched=0 where 1");*/
			$sql3 = "insert into cp_check_distribution(lottery_id,issue,unmatched,created) select lid,issue,1,now() 
			from $split_cmp_table m where m.onum != m.num and m.lid not in ({$lid}) and m.onum = m.cnum
			on duplicate key update unmatched = VALUES(unmatched)";
			$re3 = $this->cfgDB->query($sql3);
			
			$this->cfgDB->query("update cp_orders_inconsistent m join $split_cmp_table n 
			on m.orderId = n.orderId set m.distributed=0, m.bonus = 0, m.margin = 0, m.bonus_t = 0,
			m.margin_t = 0 where 1");
			$sql4 = "insert into cp_orders_inconsistent(orderId, lid, issue, bonus,bonus_t,margin,margin_t, distributed, created) 
			select orderId, lid, issue, bonus,bonus_t,margin,margin_t,1,now() 
			from $split_cmp_table m where m.onum != m.num and m.lid not in ({$lid}) and m.onum = m.cnum" . 
			$this->onduplicate(array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'), array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'), 
			array('bonus', 'bonus_t', 'margin', 'margin_t'), 'cp_orders_inconsistent.');
			$re4 = $this->cfgDB->query($sql4);
			
			if($re1 && $re2 && $re3 && $re4)
			{
				$this->trans_complete();
			}
			else 
			{
				$this->trans_rollback();
			}
			//添加报警功能(新)
			$sql = "select  8 ctype, '奖金对比失败报警'  title, concat(orderId, ':有奖金对比失败的订单') content, 
    		0 status, now() created from $split_cmp_table m where m.onum != m.num and m.lid not in ({$lid}) 
    		and m.onum = m.cnum";
    		//奖金对比失败报警
			$alertRow = $this->cfgDB->query($sql)->getRow();
			if(!empty($alertRow))
    		{
    			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
                VALUES (?,?,?, ?, ?)";
    			$this->db->query($isql, $alertRow);
    		}
		}
	}
	
	/**
	 * 高频彩奖金推送到ori表操作
	 * @param int $lid		彩种id
	 * @param string $issue 期次
	 * @return boolean   true 已处理完   false 未处理完
	 */
	public function quickCompareBonus($lid, $issue)
	{
		$tables = $this->getSplitTable($lid);
		$split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}";
		$sql = "select m.orderId, m.lid, m.issue, sum(1) onum, 
		sum(if(m.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0)) cnum
		from {$tables['split_table']} m where m.lid = ? and issue=? and status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') 
		and cpstate < 2
		group by m.orderId having onum = cnum limit 2000";
		$data = $this->cfgDB->query($sql, array($lid, $issue))->getAll();
		while (!empty($data))
		{
			$this->trans_start();
			$this->cfgDB->query("truncate $split_cmp_table");
			//插入可能需要对比奖金的数据
			$res = $this->insertTmpTable($split_cmp_table, $data);
			if(!$res)
			{
				$this->trans_rollback();
				break;
			}
			//补休其他数据
			$fields = array('orderId', 'bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'failMoney');
			$msql = "insert $split_cmp_table (". implode(',', $fields) .")
			select n.orderId, n.bonus, n.bonus_t, n.margin, n.margin_t, 1 as onum,
			if(n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
			if(n.status={$this->order_status['concel']}, n.money, 0) failMoney
			from $split_cmp_table m join {$tables['split_table']} n
			on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'failMoney'),
			array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'failMoney'), "{$split_cmp_table}.");
			$this->cfgDB->query($msql);
			//更新订单原表
			$usql = "update cp_orders_ori m
			join $split_cmp_table n on m.orderId = n.orderId and m.status <> '{$this->order_status['concel']}'
			set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
			m.bonus = n.bonus, m.margin = n.margin,	m.synflag = 0, m.win_time = if(n.bonus > 0, now(), 0),
			m.failMoney = n.failMoney
			where n.onum = n.num";
			$re1 = $this->cfgDB->query($usql);
			//更新split表
			$usql = "update {$tables['split_table']} m
			join $split_cmp_table n on m.orderId = n.orderId
			set m.cpstate = if(n.onum = n.num, 2, m.cpstate)";
			$re2 = $this->cfgDB->query($usql);
			if($re1 && $re2)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
				break;
			}
			$data = $this->cfgDB->query($sql, array($lid, $issue))->getAll();
		}
		
		if(!empty($data))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * 竞技彩奖金推送到ori表操作
	 * @param unknown_type $lid
	 * @param unknown_type $issue
	 * @return boolean
	 */
	public function jjcCompareBonus($lid)
	{
		$tables = $this->getSplitTable($lid);
		$split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}";
		$sql = "select m.orderId, m.lid
		from {$tables['split_table']} m force index(modified) where m.modified > date_sub(now(), interval 1 hour) and m.lid = ? and m.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')
		and cpstate < 2
		group by m.orderId limit 2000 ";
		$data = $this->cfgDB->query($sql, array($lid))->getAll();
		while (!empty($data))
		{
			$this->trans_start();
			$this->cfgDB->query("truncate $split_cmp_table");
			//插入可能需要对比奖金的数据
			$res = $this->insertTmpTable($split_cmp_table, $data);
			if(!$res)
			{
				$this->trans_rollback();
				break;
			}
			//补休其他数据
			$fields = array('orderId', 'bonus', 'margin', 'onum', 'num', 'failMoney', 'aduit_num');
			$msql = "insert $split_cmp_table (". implode(',', $fields) .")
			select n.orderId, n.bonus, n.margin, 1 as onum,
			if(n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
			if(n.status={$this->order_status['concel']}, n.money, 0) failMoney,
			if(n.status in('{$this->order_status['concel']}'), 1, n.aduitflag) as aduit_num
			from $split_cmp_table m join {$tables['split_table']} n
			on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'margin', 'onum', 'num', 'failMoney', 'aduit_num'),
			array('bonus', 'margin', 'onum', 'num', 'failMoney', 'aduit_num'), "{$split_cmp_table}.");
			$this->cfgDB->query($msql);
			//更新订单原表
			$usql = "update cp_orders_ori m
			join $split_cmp_table n on m.orderId = n.orderId and m.status not in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}')
			set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
			m.bonus = n.bonus, m.margin = n.margin,	m.synflag = (m.synflag << 1), m.win_time = if(n.bonus > 0, now(), 0),
			m.failMoney = n.failMoney
			where n.onum = n.num and n.num = n.aduit_num";
			$re1 = $this->cfgDB->query($usql);
			//更新split表
			$usql = "update {$tables['split_table']} m
			join $split_cmp_table n on m.orderId = n.orderId
			set m.cpstate = if((m.status in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}') and m.cpstate < 2), 2, m.cpstate)";
			$re2 = $this->cfgDB->query($usql);
			if($re1 && $re2)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
				break;
			}
			$data = $this->cfgDB->query($sql, array($lid))->getAll();
		}
	
		if(!empty($data))
		{
			return false;
		}
	
		return true;
	}
	
	/**
	 * 慢频彩奖金推送到ori表操作
	 * @param unknown_type $lid
	 * @param unknown_type $issue
	 * @return boolean
	 */
	public function slowCompareBonus($lid, $issue)
	{
	    $tables = $this->getSplitTable($lid);
	    $split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}";
	    $sql = "select m.orderId, m.lid, m.issue
	    from {$tables['split_table']} m force index(modified) where m.modified > date_sub(now(), interval 1 hour) and m.lid = ? and issue=? and m.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')
	    and cpstate < 2
	    group by m.orderId limit 10000 ";
	    $data = $this->cfgDB->query($sql, array($lid, $issue))->getAll();
	    while (!empty($data))
	    {
	        $this->trans_start();
	        $this->cfgDB->query("truncate $split_cmp_table");
	        //插入可能需要派奖的数据
	        $res = $this->insertTmpTable($split_cmp_table, $data);
	        if(!$res)
	        {
	            $this->trans_rollback();
	            break;
	        }
	        //补休其他数据
	        $fields = array('orderId', 'bonus', 'margin', 'onum', 'num', 'failMoney');
	        $msql = "insert $split_cmp_table (". implode(',', $fields) .")
	        select n.orderId, n.bonus, n.margin, 1 as onum,
	        if(n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
	        if(n.status={$this->order_status['concel']}, n.money, 0) failMoney
	        from $split_cmp_table m join {$tables['split_table']} n
	        on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'margin', 'onum', 'num', 'failMoney'),
	            array('bonus', 'margin', 'onum', 'num', 'failMoney'), "{$split_cmp_table}.");
	        $this->cfgDB->query($msql);
	        //更新订单原表
	        $usql = "update cp_orders_ori m
	        join $split_cmp_table n on m.orderId = n.orderId and m.status not in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}')
	        set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
	        m.bonus = n.bonus, m.margin = n.margin,	m.synflag = (m.synflag << 1), m.win_time = if(n.bonus > 0, now(), 0),
	        m.failMoney = n.failMoney
	        where n.onum = n.num";
	        $re1 = $this->cfgDB->query($usql);
	        //更新split表
	        $usql = "update {$tables['split_table']} m
	        join $split_cmp_table n on m.orderId = n.orderId
	        set m.cpstate = if((m.status in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}') and m.cpstate < 2), 2, m.cpstate)";
	        $re2 = $this->cfgDB->query($usql);
	        if($re1 && $re2)
	        {
	            $this->trans_complete();
	        }
	        else
	        {
	            $this->trans_rollback();
	            break;
	        }
	        $data = $this->cfgDB->query($sql, array($lid, $issue))->getAll();
	    }
	    
	    if(!empty($data))
	    {
	        return false;
	    }
	    
	    return true;
	}
	
	/**
	 * 将对比数据插入临时表操作
	 * @param unknown_type $tname
	 * @param unknown_type $datas
	 */
	private function insertTmpTable($tname, $datas)
	{
		$s_data = array();
		$d_data = array();
		foreach ($datas as $data)
		{
			array_push($s_data, '(?, ?, ?)');
			array_push($d_data, $data['orderId']);
			array_push($d_data, $data['lid']);
			array_push($d_data, $data['issue']);
		}
		$inisql = "insert ignore $tname(orderId, lid, issue) values" . implode(',', $s_data);
		return $this->cfgDB->query($inisql, $d_data);
	}
	
	/**
	 * 快频彩奖金比对
	 * @param unknown_type $lid
	 */
	public function quickBonusCheck($lid)
	{
		$tables = $this->getSplitTable($lid);
		$split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}_check";
		$sql = "select orderId, lid, issue
		from {$tables['split_table']} where modified > date_sub(now(), interval 1 day) and lid = ? and cpstate = 3
		group by orderId limit 2000";
		$datas = $this->cfgDB->query($sql, array($lid))->getAll();
		while (!empty($datas))
		{
			$this->trans_start();
			$this->cfgDB->query("truncate $split_cmp_table");
			//插入可能需要对比奖金的数据
			$res = $this->insertTmpTable($split_cmp_table, $datas);
			if(!$res)
			{
				$this->trans_rollback();
				break;
			}
			//补休其他数据
			$fields = array('orderId', 'bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum');
			$msql = "insert $split_cmp_table (". implode(',', $fields) .")
			select n.orderId, n.bonus, n.bonus_t, n.margin, n.margin_t, 1 as onum,
			if((n.bonus = n.bonus_t) && (n.margin = n.margin_t) && n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
			if((n.cpstate = 3 && n.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')) or (n.status = '{$this->order_status['concel']}'), 1, 0) as cnum
			from $split_cmp_table m join {$tables['split_table']} n
			on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum'),
			array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum'), "{$split_cmp_table}.");
			$this->cfgDB->query($msql);
			//更新split表
			$usql = "update {$tables['split_table']} m
			join $split_cmp_table n on m.orderId = n.orderId
			left join cp_orders_inconsistent o on m.orderId = o.orderId
			set m.cpstate = if((n.onum = n.num || o.dispatch = 1 || n.onum = n.cnum), 4, m.cpstate)";
			$re1 = $this->cfgDB->query($usql);
			//对比不一致数据存储
			$sql2 = "insert into cp_check_distribution(lottery_id,issue,unmatched,created) select lid,issue,1,now()
			from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum
			on duplicate key update unmatched = VALUES(unmatched)";
			$re2 = $this->cfgDB->query($sql2);
				
			$re3 = $this->cfgDB->query("update cp_orders_inconsistent m join $split_cmp_table n
			on m.orderId = n.orderId set m.distributed=0, m.bonus = 0, m.margin = 0, m.bonus_t = 0,
			m.margin_t = 0 where 1");
				
			$sql4 = "insert into cp_orders_inconsistent(orderId, lid, issue, bonus,bonus_t,margin,margin_t, distributed, created)
			select orderId, lid, issue, bonus,bonus_t,margin,margin_t,1,now()
			from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum" .
			$this->onduplicate(array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'), array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'),
			array('bonus', 'bonus_t', 'margin', 'margin_t'), 'cp_orders_inconsistent.');
			$re4 = $this->cfgDB->query($sql4);
			if($re1 && $re2 && $re3 && $re4)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
				break;
			}
			//添加报警功能
			$waringSql = "select  8 ctype,'奖金对比失败报警'  title, concat(orderId, ':有奖金对比失败的订单') content,
			0 status, now() created from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum";
			$alertRow = $this->cfgDB->query($waringSql)->getRow();
			if(!empty($alertRow))
			{
    			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
                VALUES (?,?,?, ?, ?)";
    			$this->db->query($isql, $alertRow);
			}
			$datas = $this->cfgDB->query($sql, array($lid))->getAll();
		}
		$sql = "select count(1) from {$tables['split_table']} where modified > date_sub(now(), interval 1 day) and lid = ? and status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') and cpstate < 3";
		$count = $this->cfgDB->query($sql, array($lid))->getOne();
		if($count > 0)
		{
			//还有订单未对比
			return false;
		}
		
		return true;
	}
	
	/**
	 * 竞技彩奖金比对
	 * @param unknown_type $lid
	 */
	public function jjcBonusCheck($lid)
	{
		$tables = $this->getSplitTable($lid);
		$split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}_check";
		$sql = "select m.orderId, m.lid, m.issue, sum(1) onum, 
		sum(if(m.cpstate in(3,4) or (m.status = '{$this->order_status['concel']}'), 1, 0)) cnum
		from cp_orders_split m 
		join (
		select orderId from cp_orders_split force index(modified) 
		where modified > date_sub(now(), interval 1 hour) and 
		status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') 
		and (cpstate = 3) and lid = ? group by orderId limit 2000) n 
		on m.orderId = n.orderId
		group by m.orderId
		having onum = cnum";
		$datas = $this->cfgDB->query($sql, array($lid))->getAll();
		while (!empty($datas))
		{
			$this->trans_start();
			$this->cfgDB->query("truncate $split_cmp_table");
			//插入可能需要对比奖金的数据
			$res = $this->insertTmpTable($split_cmp_table, $datas);
			if(!$res)
			{
				$this->trans_rollback();
				break;
			}
			//补休其他数据
			$fields = array('orderId', 'bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney', 'aduit_num');
			$msql = "insert $split_cmp_table (". implode(',', $fields) .")
			select n.orderId, n.bonus, n.bonus_t, n.margin, n.margin_t, 1 as onum,
			if((n.bonus = n.bonus_t) && (n.margin = n.margin_t) && n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
			if((n.cpstate = 3 && n.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')) or (n.status = '{$this->order_status['concel']}'), 1, 0) as cnum,
			if(n.status={$this->order_status['concel']}, n.money, 0) failMoney,
			if(n.status in('{$this->order_status['concel']}'), 1, n.aduitflag) as aduit_num
			from $split_cmp_table m join {$tables['split_table']} n
			on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney', 'aduit_num'),
			array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney', 'aduit_num'), "{$split_cmp_table}.");
			$this->cfgDB->query($msql);
			//更新split表
			$usql = "update {$tables['split_table']} m
			join $split_cmp_table n on m.orderId = n.orderId
			left join cp_orders_inconsistent o on m.orderId = o.orderId
			set m.cpstate = if((n.onum = n.num || o.dispatch = 1 || n.onum = n.cnum), 4, m.cpstate)";
			$re1 = $this->cfgDB->query($usql);
			//对比不一致数据存储
			$sql2 = "insert into cp_check_distribution(lottery_id,issue,unmatched,created) select lid,issue,1,now()
			from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum
			on duplicate key update unmatched = VALUES(unmatched)";
			$re2 = $this->cfgDB->query($sql2);
			$re3 = $this->cfgDB->query("update cp_orders_inconsistent m join $split_cmp_table n
			on m.orderId = n.orderId set m.distributed=0, m.bonus = 0, m.margin = 0, m.bonus_t = 0,
			m.margin_t = 0 where 1");
			$sql4 = "insert into cp_orders_inconsistent(orderId, lid, issue, bonus,bonus_t,margin,margin_t, distributed, created)
			select orderId, lid, issue, bonus,bonus_t,margin,margin_t,1,now()
			from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum" .
			$this->onduplicate(array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'), array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'),
			array('bonus', 'bonus_t', 'margin', 'margin_t'), 'cp_orders_inconsistent.');
			$re4 = $this->cfgDB->query($sql4);
			//比对成功更新ori表
			$sql5 = "update cp_orders_ori m
			join $split_cmp_table n on m.orderId = n.orderId and m.status not in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}')
			left join cp_orders_inconsistent o on m.orderId = o.orderId
			set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
			m.bonus = n.bonus, m.margin = n.margin,	m.synflag = (m.synflag << 1), m.win_time = if(n.bonus > 0, now(), 0),
			m.failMoney = n.failMoney
			where n.onum = n.num or o.dispatch = 1";
			$re5 = $this->cfgDB->query($sql5);
			
			if($re1 && $re2 && $re3 && $re4 && $re5)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
				break;
			}
			//添加报警功能
			$waringSql = "select  8 ctype, '奖金对比失败报警'  title, concat(orderId, ':有奖金对比失败的订单') content,
			0 status, now() created from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum";
			$alertRow = $this->cfgDB->query($waringSql)->getRow();
			if(!empty($alertRow))
			{
    			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
                VALUES (?,?,?, ?, ?)";
    			$this->db->query($isql, $alertRow);
			}
			$datas = $this->cfgDB->query($sql, array($lid))->getAll();
		}
		
		$sql = "select count(1) from {$tables['split_table']} force index(modified) where modified > date_sub(now(), interval 6 hour) and lid = ? and status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') and cpstate < 3";
		$count = $this->cfgDB->query($sql, array($lid))->getOne();
		if($count > 0)
		{
			//还有订单未对比
			return false;
		}
	
		return true;
	}
	
	/**
	 * 慢频彩奖金比对
	 * @param unknown_type $lid
	 */
	public function slowBonusCheck($lid)
	{
	    $tables = $this->getSplitTable($lid);
	    $split_cmp_table = "{$this->db_config['cfgtmp']}.cp_orders_split_cmp_{$lid}_check";
	    $sql = "select m.orderId, m.lid, m.issue, sum(1) onum,
	    sum(if(m.cpstate in(3,4) or (m.status = '{$this->order_status['concel']}'), 1, 0)) cnum
	    from cp_orders_split m
	    join (
	    select orderId from cp_orders_split force index(modified)
	    where modified > date_sub(now(), interval 1 hour) and
	    status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')
	    and (cpstate = 3) and lid = ? group by orderId limit 2000) n
	    on m.orderId = n.orderId
	    group by m.orderId
	    having onum = cnum";
	    $datas = $this->cfgDB->query($sql, array($lid))->getAll();
	    while (!empty($datas))
	    {
	        $this->trans_start();
	        $this->cfgDB->query("truncate $split_cmp_table");
	        //插入可能需要对比奖金的数据
	        $res = $this->insertTmpTable($split_cmp_table, $datas);
	        if(!$res)
	        {
	            $this->trans_rollback();
	            break;
	        }
	        //补休其他数据
	        $fields = array('orderId', 'bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney');
	        $msql = "insert $split_cmp_table (". implode(',', $fields) .")
	        select n.orderId, n.bonus, n.bonus_t, n.margin, n.margin_t, 1 as onum,
	        if((n.bonus = n.bonus_t) && (n.margin = n.margin_t) && n.status in('{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0) as num,
	        if((n.cpstate = 3 && n.status in('{$this->order_status['notwin']}', '{$this->order_status['win']}')) or (n.status = '{$this->order_status['concel']}'), 1, 0) as cnum,
	        if(n.status={$this->order_status['concel']}, n.money, 0) failMoney
	        from $split_cmp_table m join {$tables['split_table']} n
	        on m.orderId = n.orderId " . $this->onduplicate($fields, array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney'),
	            array('bonus', 'bonus_t', 'margin', 'margin_t', 'onum', 'num', 'cnum', 'failMoney'), "{$split_cmp_table}.");
	        $this->cfgDB->query($msql);
	        //更新split表
	        $usql = "update {$tables['split_table']} m
	        join $split_cmp_table n on m.orderId = n.orderId
	        left join cp_orders_inconsistent o on m.orderId = o.orderId
	        set m.cpstate = if((n.onum = n.num || o.dispatch = 1 || n.onum = n.cnum), 4, m.cpstate)";
	        $re1 = $this->cfgDB->query($usql);
	        //对比不一致数据存储
	        $sql2 = "insert into cp_check_distribution(lottery_id,issue,unmatched,created) select lid,issue,1,now()
	        from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum
	        on duplicate key update unmatched = VALUES(unmatched)";
	        $re2 = $this->cfgDB->query($sql2);
	        $re3 = $this->cfgDB->query("update cp_orders_inconsistent m join $split_cmp_table n
	            on m.orderId = n.orderId set m.distributed=0, m.bonus = 0, m.margin = 0, m.bonus_t = 0,
	            m.margin_t = 0 where 1");
	        $sql4 = "insert into cp_orders_inconsistent(orderId, lid, issue, bonus,bonus_t,margin,margin_t, distributed, created)
	        select orderId, lid, issue, bonus,bonus_t,margin,margin_t,1,now()
	        from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum" .
	        $this->onduplicate(array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'), array('bonus', 'bonus_t', 'margin', 'margin_t', 'distributed'),
	            array('bonus', 'bonus_t', 'margin', 'margin_t'), 'cp_orders_inconsistent.');
	        $re4 = $this->cfgDB->query($sql4);
	        //未派奖订单更新ori表
	        $sql5 = "update cp_orders_ori m
	        join $split_cmp_table n on m.orderId = n.orderId and m.status not in('{$this->order_status['concel']}', '{$this->order_status['win']}', '{$this->order_status['notwin']}')
	        left join cp_orders_inconsistent o on m.orderId = o.orderId
	        set m.status = if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
	        m.bonus = n.bonus, m.margin = n.margin,	m.synflag = (m.synflag << 1), m.win_time = if(n.bonus > 0, now(), 0),
	        m.failMoney = n.failMoney
	        where n.onum = n.num or o.dispatch = 1";
	        $re5 = $this->cfgDB->query($sql5);
	        if($re1 && $re2 && $re3 && $re4 && $re5)
	        {
	            $this->trans_complete();
	        }
	        else
	        {
	            $this->trans_rollback();
	            break;
	        }
	        //添加报警功能
	        $waringSql = "select  8 ctype, '奖金对比失败报警'  title, concat(orderId, ':有奖金对比失败的订单') content,
	        0 status, now() created from $split_cmp_table m where m.onum != m.num and m.onum = m.cnum";
	        $alertRow = $this->cfgDB->query($waringSql)->getRow();
	        if(!empty($alertRow))
	        {
	            $isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
                VALUES (?,?,?, ?, ?)";
	            $this->db->query($isql, $alertRow);
	        }
	        $datas = $this->cfgDB->query($sql, array($lid))->getAll();
	    }
	    
	    $sql = "select count(1) from {$tables['split_table']} force index(modified) where modified > date_sub(now(), interval 6 hour) and lid = ? and status in('{$this->order_status['notwin']}', '{$this->order_status['win']}') and cpstate < 3";
	    $count = $this->cfgDB->query($sql, array($lid))->getOne();
	    if($count > 0)
	    {
	        //还有订单未对比
	        return false;
	    }
	    
	    return true;
	}
	
	/*
	 *功能：将insert select 进行分离解决死锁问题
	 *作者：huxm
	 * */
	private function insert_select($tname, $sql)
	{
		$retrun = true;
		$datas = $this->cfgDB->query($sql)->getAll();
		if(!empty($datas))
		{
			$s_data = array();
			$d_data = array();
			foreach ($datas as $data)
			{
				array_push($s_data, '(?, ?, ?)');
				array_push($d_data, $data['orderId']);
				array_push($d_data, $data['lid']);
				array_push($d_data, $data['issue']);
			}
			$inisql = "insert ignore $tname(orderId, lid, issue) values" . implode(',', $s_data);
			$retrun = $this->cfgDB->query($inisql, $d_data);
		}
		return $retrun;
	}
	
	public function award_jjc($lid)
	{
		$condition = "where status in ('{$this->order_status['notwin']}', '{$this->order_status['win']}')
		and modified > date_sub(now(), interval 30 minute) and synflag = 0 and lid = ?";
		$checksql = "select count(*) from cp_orders_ori $condition";
		$re = $this->cfgDB->query($checksql, array($lid))->getOne();
		if($re > 0)
		{
			$usql = "update cp_orders_ori set synflag = (1 << 1) $condition";
			$re2 = $this->cfgDB->query($usql, array($lid));
		}
	}
	
	public function getIssues($lname, $status)
	{
		$sql = "select issue from cp_{$lname}_paiqi where modified > date_sub(now(), interval 12 day) and rstatus = ?";
		return $this->cfgDB->query($sql, array($status))->getCol();
	}
	
	public function getMids($lname, $status, $fname = 'rstatus')
	{
		
		$sql = "select mid from cp_{$lname}_paiqi where created > date_sub(now(), interval 12 day) and $fname = ?";
		return $this->cfgDB->query($sql, array($status))->getCol();
	}
	
	/**
	 * 数字彩订单兑奖检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function award_number($lname, $lid, $pIssue, $oIssue)
	{
		$stime = $this->get_cissue_stime($lid, $oIssue);
		$sql = "SELECT count(*) FROM cp_orders_ori WHERE lid=? AND issue=? AND `status` NOT IN ('{$this->order_status['concel']}','{$this->order_status['notwin']}','{$this->order_status['win']}')
		and modified > ?";
		$result = $this->cfgDB->query($sql, array($lid, $oIssue, $stime))->getOne();
		if(!$result)
		{
			$this->trans_start();
			$sql1 = "UPDATE cp_orders_ori SET synflag=(1 << 1) WHERE lid=? AND issue=? and modified > ?";
			$re1 = $this->cfgDB->query($sql1, array($lid, $oIssue, $stime));
			$sql = "select count(*) from cp_orders_ori where lid=? AND issue=? and status = {$this->order_status['win']} 
			and modified > date_sub(now(), interval 30 minute)";
			$tcount = $this->cfgDB->query($sql, array($lid, $oIssue))->getOne();
			$rstatus = $tcount > 0 ? $this->order_status['paiqi_awarding'] : $this->order_status['paiqi_awarded'];
			$sql2 = "UPDATE cp_{$lname}_paiqi SET rstatus='$rstatus', tcount = $tcount WHERE issue=?";
			$re2 = $this->cfgDB->query($sql2, array($pIssue));
			if($re1 && $re2)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
			}
		}
	}
	
	/**
	 * 慢频数字彩订单兑奖检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function award_slow_number($lname, $lid, $pIssue, $oIssue)
	{
	    $stime = $this->get_cissue_stime($lid, $oIssue);
	    $sql = "SELECT count(*) FROM cp_orders_ori WHERE lid=? AND issue=? AND `status` NOT IN ('{$this->order_status['concel']}','{$this->order_status['notwin']}','{$this->order_status['win']}')
	    and modified > ?";
	    $result = $this->cfgDB->query($sql, array($lid, $oIssue, $stime))->getOne();
	    if(!$result)
	    {
	        $sql = "UPDATE cp_{$lname}_paiqi SET rstatus=? WHERE issue=?";
	        $res = $this->cfgDB->query($sql, array($this->order_status['paiqi_awarding'], $pIssue));
	        if($res)
	        {
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * 数字彩订单结期检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function period_number($lname, $lid, $pIssue, $oIssue)
	{
		$sql = "SELECT count(*) FROM cp_orders WHERE lid=? AND issue=? AND `status` = {$this->order_status['win']} and my_status not in(0, 2)
		and modified > date_sub(now(), interval 1 hour)";
		$tcounto = $this->db->query($sql, array($lid, $oIssue))->getOne();
		$sql2 = "select tcount from cp_{$lname}_paiqi where issue= ?";
		$tcount = $this->cfgDB->query($sql2, array($pIssue))->getOne();
		if($tcounto == $tcount)
		{
			$sql1 = "UPDATE cp_{$lname}_paiqi SET rstatus='{$this->order_status['paiqi_awarded']}' WHERE issue=?";
			return $this->cfgDB->query($sql1, array($pIssue));
		}
		
		return false;
	}
	
	/**
	 * 慢频数字彩订单结期检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function period_slow_number($lname, $lid, $pIssue, $oIssue)
	{
	    $sql = "SELECT count(*) FROM cp_orders WHERE lid=? AND issue=? AND (`status` in('{$this->order_status['pay']}', '{$this->order_status['drawing']}', '{$this->order_status['draw']}', '{$this->order_status['draw_part']}') or (status = '{$this->order_status['win']}' AND my_status in(0, 2)))";
	    $count = $this->db->query($sql, array($lid, $oIssue))->getOne();
	    if(!($count > 0))
	    {
	        $sql1 = "UPDATE cp_{$lname}_paiqi SET rstatus='{$this->order_status['paiqi_awarded']}' WHERE issue=?";
	        return $this->cfgDB->query($sql1, array($pIssue));
	    }
	    
	    return false;
	}
	
	/**
	 * 数字彩订单兑奖检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function award_sfc($pIssue, $oIssue, $lid, $fname = 'rstatus')
	{
		$stime = $this->get_cissue_stime($lid, $oIssue);
		$sql = "SELECT * FROM cp_orders_ori WHERE lid = ? AND issue=? AND `status` NOT IN ('{$this->order_status['concel']}','{$this->order_status['notwin']}','{$this->order_status['win']}')
		and modified > ?";
		$result = $this->cfgDB->query($sql, array($lid, $oIssue, $stime))->getRow();
		if(!$result)
		{
			$this->trans_start();
			$sql1 = "UPDATE cp_orders_ori SET synflag=(1 << 1) WHERE lid = ? AND issue=? and modified > ?";
			$re1 = $this->cfgDB->query($sql1, array($lid, $oIssue, $stime));
			$sql = "select count(*) from cp_orders_ori where lid=? AND issue=? and status = {$this->order_status['win']} 
			and modified > date_sub(now(), interval 30 minute)";
			$tcount = $this->cfgDB->query($sql, array($lid, $oIssue))->getOne();
			$rstatus = $tcount > 0 ? $this->order_status['paiqi_awarding'] : $this->order_status['paiqi_awarded'];
			$fcount = ($fname == 'rstatus') ? 'tcount' : 'rjtcount';
			$sql2 = "UPDATE cp_rsfc_paiqi SET $fname = $rstatus, $fcount = $tcount WHERE mid=?";
			$re2 = $this->cfgDB->query($sql2, array($pIssue));
			if($re1 && $re2)
			{
				$this->trans_complete();
			}
			else
			{
				$this->trans_rollback();
			}
		}
	}
	
	/**
	 * 数字彩订单兑奖检查
	 * @param string $lname		表名
	 * @param int $lid			彩种id
	 * @param string $pIssue	排期表期号
	 * @param string $oIssue	订单表期号
	 */
	public function period_sfc($pIssue, $oIssue, $lid, $fname = 'rstatus')
	{
		$sql = "SELECT count(*) FROM cp_orders WHERE lid=? AND issue=? AND `status` = {$this->order_status['win']} and my_status not in(0, 2)
		and modified > date_sub(now(), interval 1 hour)";
		$tcounto = $this->db->query($sql, array($lid, $oIssue))->getOne();
		$fcount = ($fname == 'rstatus') ? 'tcount' : 'rjtcount';
		$sql2 = "select $fcount from cp_rsfc_paiqi where mid= ?";
		$tcount = $this->cfgDB->query($sql2, array($pIssue))->getOne();
		if($tcounto == $tcount)
		{
			$sql1 = "UPDATE cp_rsfc_paiqi SET $fname='{$this->order_status['paiqi_awarded']}' WHERE mid=?";
			return $this->cfgDB->query($sql1, array($pIssue));
		}
	}
}
