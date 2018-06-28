<?php

class Statistics_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
    }
    
    /**
     * 订单统计数据
     * @param string $date 	日期  yyyy-mm-dd
     */
    public function orderStatistics($date)
    {
    	$sql = "INSERT INTO cp_order_statistics ( date, platform, version, lid, channel, betting_users, total, order_nums, created ) 
    	SELECT ?, IF (buyPlatform = 1, 2, 1) platform, app_version version, lid, channel, COUNT(DISTINCT uid) betting_users, SUM(money) total, COUNT(id) order_nums, NOW() FROM cp_orders 
    	WHERE modified >= date_sub(?, INTERVAL 2 DAY) AND ( pay_time BETWEEN ? AND ? ) AND `status` IN ('{$this->order_status['drawing']}', '{$this->order_status['draw']}','{$this->order_status['notwin']}', '{$this->order_status['win']}') 
    	GROUP BY channel, lid, buyPlatform, app_version 
    	ON DUPLICATE KEY UPDATE betting_users = VALUES (betting_users), total = VALUES (total), order_nums = VALUES (order_nums)";
    	$this->db->query($sql, array($date, $date, $date, $date . ' 23:59:59'));
    	
    	$sql1 = "INSERT INTO cp_order_statistics ( date, platform, version, lid, channel, award_users, award_total, created ) 
    	SELECT ?, IF (buyPlatform = 1, 2, 1) platform, app_version version, lid, channel, COUNT(DISTINCT uid) award_users, SUM(bonus) award_total, NOW() FROM cp_orders 
    	WHERE modified >= date_sub(?, INTERVAL 2 DAY) AND ( win_time BETWEEN ? AND ? ) AND `status` IN ('{$this->order_status['win']}') 
    	GROUP BY channel, lid, buyPlatform, app_version 
    	ON DUPLICATE KEY UPDATE award_users = VALUES (award_users), award_total = VALUES (award_total)";
    	$this->db->query($sql1, array($date, $date, $date, $date . ' 23:59:59'));
    }
    
    /**
     * 订单不区分彩种统计数据
     * @param string $date 	日期  yyyy-mm-dd
     */
    public function orderStatistics1($date)
    {
    	$sql = "INSERT INTO cp_order_statistics_all ( date, platform, version, channel, betting_users, total, order_nums, created )
    	SELECT ?, IF (buyPlatform = 1, 2, 1) platform, app_version version, channel, COUNT(DISTINCT uid) betting_users, SUM(money) total, COUNT(id) order_nums, NOW() FROM cp_orders 
    	WHERE modified >= date_sub(?, INTERVAL 2 DAY) AND ( pay_time BETWEEN ? AND ? ) AND `status` IN ('{$this->order_status['drawing']}', '{$this->order_status['draw']}','{$this->order_status['notwin']}', '{$this->order_status['win']}') 
    	GROUP BY channel, buyPlatform, app_version
    	ON DUPLICATE KEY UPDATE betting_users = VALUES (betting_users), total = VALUES (total), order_nums = VALUES (order_nums)";
    	$this->db->query($sql, array($date, $date, $date, $date . ' 23:59:59'));
    	 
    	$sql1 = "INSERT INTO cp_order_statistics_all ( date, platform, version, channel, award_users, award_total, created )
    	SELECT ?, IF (buyPlatform = 1, 2, 1) platform, app_version version, channel, COUNT(DISTINCT uid) award_users, SUM(bonus) award_total, NOW() FROM cp_orders
    	WHERE modified >= date_sub(?, INTERVAL 2 DAY) AND ( win_time BETWEEN ? AND ? ) AND `status` IN ('{$this->order_status['win']}')
    	GROUP BY channel, buyPlatform, app_version
    	ON DUPLICATE KEY UPDATE award_users = VALUES (award_users), award_total = VALUES (award_total)";
    	$this->db->query($sql1, array($date, $date, $date, $date . ' 23:59:59'));
    }
    
    /**
     * 充值统计数据
     * @param string $date 	日期  yyyy-mm-dd
     */
    public function rechargeStatistics($date)
    {
    	$sql = "INSERT INTO cp_recharge_statistics ( date, platform, channel, version, users, total, recharge_nums, created ) 
    	SELECT ?, IF (platform = 1, 2, 1) platform, channel, app_version, COUNT(DISTINCT uid) users, SUM(money) total, COUNT(id) recharge_nums, NOW() FROM cp_wallet_logs 
    	WHERE modified >= date_sub(?, INTERVAL 2 DAY) AND ctype = '0' AND mark = '1' AND ( created BETWEEN ? AND ? ) 
    	GROUP BY platform, channel, app_version 
    	ON DUPLICATE KEY UPDATE users = VALUES (users), total = VALUES (total), recharge_nums = VALUES (recharge_nums)";
    	$this->db->query($sql, array($date, $date, $date, $date . ' 23:59:59'));
    }
    
    /**
     * 提款统计数据
     * @param string $date 	日期  yyyy-mm-dd
     */
    public function withdrawStatistics($date)
    {
    	$sql = "INSERT INTO cp_withdraw_statistics ( date, platform, channel, version, users, total, withdraw_nums, created ) 
    	SELECT ?, IF (platform = 1, 2, 1) platform, channel, app_version, COUNT(DISTINCT uid) users, SUM(money) total, COUNT(id) withdraw_nums, NOW() FROM cp_withdraw 
    	WHERE STATUS = '2' AND ( modified BETWEEN ? AND ? ) 
    	GROUP BY platform, channel, app_version 
    	ON DUPLICATE KEY UPDATE users = VALUES (users), total = VALUES (total), withdraw_nums = VALUES (withdraw_nums)";
    	$this->db->query($sql, array($date, $date, $date . ' 23:59:59'));
    }
    
    //渠道成本统计
    public function costStatistics($date)
    {
    	//CPA统计
    	$sql = "INSERT INTO cp_cost_statistics ( date, platform, channel, total, created ) 
    	SELECT ?, r.platform, r.channel_id, c.unit_price * SUM(r.register_num) * c.subtract_coefficient, NOW() FROM cp_register_stat r INNER JOIN cp_channel c ON c.id = r.channel_id AND c.settle_mode = 1 AND r.platform = c.platform 
    	WHERE r.cdate = ? GROUP BY r.platform, r.channel_id 
    	ON DUPLICATE KEY UPDATE total = VALUES (total)";
    	$this->db->query($sql, array($date, $date));
    	
    	//CPS统计
    	$sql1 = "INSERT INTO cp_cost_statistics ( date, platform, channel, total, created ) 
    	SELECT ?, o.platform, o.channel, SUM(o.total) * c.share_ratio / 100, NOW() FROM cp_order_statistics o INNER JOIN cp_channel c ON c.id = o.channel AND c.settle_mode = 2 AND o.platform = c.platform 
    	WHERE o.date = ? GROUP BY o.platform, o.channel 
    	ON DUPLICATE KEY UPDATE total = VALUES (total)";
    	$this->db->query($sql1, array($date, $date));
    	
    	//CPT统计
    	$sql2 = "INSERT INTO cp_cost_statistics ( date, platform, channel, total, created ) 
    	SELECT ?, platform, id, month_fee / 30, NOW() FROM cp_channel WHERE settle_mode = 3 
    	ON DUPLICATE KEY UPDATE total = VALUES (total)";
    	$this->db->query($sql2, array($date));
    }
    
    /**
     * 账户余额对账
     * @param string $date	日期
     */
    public function walletStatistics($date)
    {
    	$lastDay = date('Y-m-d', strtotime($date) - 86400);
    	$this->load->library('tools');
    	$suffix = $this->tools->getTableSuffixByDate($date);
    	$suffix = $suffix ? '_' . $suffix : '';
    	$this->db->query("truncate {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp");
    	//用户对账统计
    	$sqlCheck = "";
    	$sqlCheck = "SELECT COUNT(*) FROM cp_wallet_logs{$suffix} WHERE 1 AND created BETWEEN ? AND ? AND ctype IN('0','1','2','3','4','8','9','10','11','14') AND mark <> '2'";
    	$count = $this->db->query($sqlCheck, array($date, $date . ' 23:59:59'))->getOne();
    	if($count > 0)
    	{
    		$sql1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp (uid,date,recharge,bonus,cost,refund,withdraw,withdraw_fail,activity,oplus,ominus,rebate) 
	    			SELECT
						uid,
						left(created, 10) date,
						SUM(IF(ctype = '0' AND mark = '1', money, 0)) recharge,
						SUM(IF(ctype = '2' AND mark = '1', money, 0)) bonus,
						SUM(IF(ctype = '1' AND mark = '0', money, 0)) cost,
						SUM(IF(ctype = '3' AND mark = '1', money, 0)) refund,
						SUM(IF(ctype = '4' AND mark = '0', money, 0)) withdraw,
						SUM(IF(ctype = '8' AND mark = '1', money, 0)) withdraw_fail,
						SUM(IF(ctype = '9' AND mark = '1', money, 0)) activity,
						SUM(IF(ctype IN('10', '11') AND mark = '1', money, 0)) oplus,
						SUM(IF(ctype IN('11') AND mark = '0', money, 0)) ominus,
						SUM(IF(ctype = '14' AND mark = '1', money, 0)) rebate
					FROM cp_wallet_logs{$suffix} WHERE 1 AND created BETWEEN ? AND ? AND ctype IN('0','1','2','3','4','8','9','10','11','14') AND mark <> '2' GROUP BY uid";
    		$this->db->query($sql1, array($date, $date . ' 23:59:59'));
    		$sql2 = "UPDATE {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp SET money = (recharge + bonus + refund + withdraw_fail + activity + oplus + rebate - cost - withdraw - ominus), tmoney = money WHERE 1";
    		$this->db->query($sql2);
    		$sql3 = "INSERT INTO cp_wallet_user_money(uid,money) SELECT uid,money FROM {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp WHERE 1 ON DUPLICATE KEY UPDATE money = cp_wallet_user_money.money + VALUES(money)";
    		$this->db->query($sql3);
    		 
    		$this->db->query("UPDATE {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp a JOIN cp_wallet_user_money b ON a.uid=b.uid SET a.money = b.money");
    		 
    		$sql4 = "INSERT INTO cp_wallet_statistics_user (uid,date,recharge,bonus,cost,refund,withdraw,withdraw_fail,activity,oplus,ominus,rebate,money,created)
    		SELECT uid,date,recharge,bonus,cost,refund,withdraw,withdraw_fail,activity,oplus,ominus,rebate,money,NOW()
    		FROM {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp where 1
    		ON DUPLICATE KEY UPDATE recharge = values(recharge), bonus = values(bonus), cost = values(cost), refund = values(refund), withdraw = values(withdraw), withdraw_fail = values(withdraw_fail), activity = values(activity), oplus = values(oplus), ominus = values(ominus), rebate = values(rebate), money = values(money)";
    		$this->db->query($sql4);
    	}
    	else 
    	{
    		$sql1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp set date=?";
    		$this->db->query($sql1, array($date));
    	}
    	//对账统计表
    	$lastMoney = $this->db->query("SELECT money FROM cp_wallet_statistics WHERE date = ? LIMIT 1", array($lastDay))->getOne();
    	$lastMoney = $lastMoney > 0 ? $lastMoney : 0;
    	$sql5 = "INSERT INTO cp_wallet_statistics (date,recharge,bonus,cost,refund,withdraw,withdraw_fail,activity,oplus,ominus,rebate,money,created) 
    			SELECT date,SUM(recharge) recharge,SUM(bonus) bonus,SUM(cost) cost,SUM(refund) refund,SUM(withdraw) withdraw,SUM(withdraw_fail) withdraw_fail,SUM(activity) activity,SUM(oplus) oplus,SUM(ominus) ominus,SUM(rebate) rebate,(SUM(tmoney) + {$lastMoney}) money,NOW()
				FROM {$this->db_config['tmp']}.cp_wallet_statistics_user_tmp
    			ON DUPLICATE KEY UPDATE recharge = values(recharge), bonus = values(bonus), cost = values(cost), refund = values(refund), withdraw = values(withdraw), withdraw_fail = values(withdraw_fail), activity = values(activity), oplus = values(oplus), ominus = values(ominus), rebate = values(rebate), money = values(money)";
    	$this->db->query($sql5, array($lastDay));
    }
    
    /**
     * 供应商对账统计
     * @param unknown_type $date
     */
    public function partnerStatistics($date)
    {
    	$tmpTable = "{$this->db_config['tmp']}.cp_wallet_statistics_partner_temp";
    	$this->db->query("truncate {$tmpTable}");
    	$lidConfig = $this->orderConfig('lidmap');
    	$lids = array_keys($lidConfig);
    	//付款
    	foreach ($lids as $lid)
    	{
    		$tables = $this->getSplitTable($lid);
    		$sql = "select ticket_seller seller, '{$date}' as date, sum(money) cost
    		from {$tables['split_table']} where modified > date_sub(now(), interval 5 day) and lid='{$lid}'
    		and (ticket_submit_time >= '{$date}' and ticket_submit_time <='{$date} 23:59:59') 
    		and status in ('500', '1000', '1010', '1020', '2000') GROUP BY ticket_seller";
    		$splitOrder = $this->slaveCfg->query($sql)->getAll();
    		if($splitOrder)
    		{
    			$this->insertDatas($tmpTable, $splitOrder, array('seller', 'date', 'cost'), array('cost'));
    		}
    	}
    	//奖金
    	foreach ($lids as $lid)
    	{
    		$tables = $this->getSplitTable($lid);
    		$sql = "select ticket_seller seller, '{$date}' as date, sum(margin) bonus
    		from {$tables['split_table']} where modified > date_sub(now(), interval 3 day) and lid='{$lid}'
    		and (win_time >= '{$date}' and win_time <='{$date} 23:59:59')
    		and status in ('1010', '1020', '2000') GROUP BY ticket_seller";
    		$splitOrder = $this->slaveCfg->query($sql)->getAll();
    		if($splitOrder)
    		{
    			$this->insertDatas($tmpTable, $splitOrder, array('seller', 'date', 'bonus'), array('bonus'));
    		}
    	}
    	//入统计表
    	$sql4 = "INSERT INTO cp_wallet_statistics_partner (seller,date,deposit,bonus,cost,refund,money,created)
    	SELECT seller,date,deposit,bonus,cost,refund,money,NOW()
    	FROM {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp where 1
    	ON DUPLICATE KEY UPDATE deposit = values(deposit), bonus = values(bonus), cost = values(cost), refund = values(refund), money = values(money)";
    	$this->db->query($sql4);
    }
    
    /**
     * 插入数据
     * @param unknown_type $datas
     * @param unknown_type $fields
     */
    public function insertDatas($table, $datas, $fields, $apd=array())
    {
    	if(empty($datas))
    	{
    		return false;
    	}
    	 
    	$s_data = array();
    	$sql = "insert {$table}(" . implode(',', $fields) . ") values";
    	foreach ($datas as $data)
    	{
    		$s_str = '';
    		foreach ($fields as $field)
    		{
    			$s_str .= "'{$data[$field]}',";
    		}
    		$s_str = preg_replace('/,$/', '', $s_str);
    		array_push($s_data, "($s_str)");
    	}
    	$sql .= implode(',', $s_data) . $this->onduplicate($fields, $fields, $apd);
    	return $this->db->query($sql);
    }
    
    /**
     * 供应商对账统计（跑历史数据）
     * @param unknown_type $date
     */
    public function partnerStatistics1($date)
    {
    	$this->load->library('tools');
    	$suffix = $this->tools->getTableSuffixByDate($date);
    	$suffix = $suffix ? '_' . $suffix : '';
    	$this->db->query("truncate {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp");
    	$sql = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller, date, money)
    	SELECT name, ?, money FROM cp_wallet_statistics_warning WHERE 1";
    	$this->db->query($sql, array($date));
    	//付款
    	$sql1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,cost)
    	SELECT ticket_seller, ?, SUM( money) cost
    	FROM cp_orders{$suffix} WHERE pay_time BETWEEN ? AND ? AND `status` IN('{$this->order_status['out_of_date_pay']}','{$this->order_status['pay']}','{$this->order_status['drawing']}','{$this->order_status['draw']}','{$this->order_status['draw_part']}', '{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}') GROUP BY ticket_seller
    	ON DUPLICATE KEY UPDATE cost = VALUES(cost)";
    	$this->db->query($sql1, array($date, $date, $date . ' 23:59:59'));
    	if($suffix == '_2015')
    	{
    		//付款
    		$sql1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,cost)
    		SELECT ticket_seller, ?, SUM( money) cost
    		FROM cp_orders WHERE pay_time BETWEEN ? AND ? AND `status` IN('{$this->order_status['out_of_date_pay']}','{$this->order_status['pay']}','{$this->order_status['drawing']}','{$this->order_status['draw']}', '{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}') GROUP BY ticket_seller
    		ON DUPLICATE KEY UPDATE cost = cost + VALUES(cost)";
    		$this->db->query($sql1, array($date, $date, $date . ' 23:59:59'));
    	}
    	//退款
    	$sql_1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,refund)
    	SELECT ticket_seller, ?, SUM(failMoney) refund
    	FROM cp_orders{$suffix} WHERE refund_time BETWEEN ? AND ? AND `failMoney` > '0' GROUP BY ticket_seller
    	ON DUPLICATE KEY UPDATE refund = VALUES(refund)";
    	$this->db->query($sql_1, array($date, $date, $date . ' 23:59:59'));
    	if($suffix == '_2015')
    	{
    		$sql_1 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,refund)
    		SELECT ticket_seller, ?, SUM(failMoney) refund
    		FROM cp_orders WHERE refund_time BETWEEN ? AND ?  GROUP BY ticket_seller
    		ON DUPLICATE KEY UPDATE refund = refund + VALUES(refund)";
    		$this->db->query($sql_1, array($date, $date, $date . ' 23:59:59'));
    	}
    	//奖金
    	$sql2 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,bonus)
    	SELECT ticket_seller, ?, SUM(margin) bonus
    	FROM cp_orders{$suffix} WHERE win_time BETWEEN ? AND ? AND `status` = '{$this->order_status['win']}' GROUP BY ticket_seller
    	ON DUPLICATE KEY UPDATE bonus = VALUES(bonus)";
    	$this->db->query($sql2, array($date, $date, $date . ' 23:59:59'));
    	if($suffix == '_2015')
    	{
    		$sql2 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,bonus)
    		SELECT ticket_seller, ?, SUM(margin) bonus
    		FROM cp_orders WHERE win_time BETWEEN ? AND ? AND `status` = '{$this->order_status['win']}' GROUP BY ticket_seller
    		ON DUPLICATE KEY UPDATE bonus = bonus + VALUES(bonus)";
    		$this->db->query($sql2, array($date, $date, $date . ' 23:59:59'));
    	}
    	//预存金额
    	$sql3 = "INSERT INTO {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp(seller,date,deposit)
    	SELECT name, date, SUM(money) FROM cp_wallet_log_partner WHERE date =? GROUP BY name
    	ON DUPLICATE KEY UPDATE deposit = VALUES(deposit)";
    	$this->db->query($sql3, array($date));
    	//计算余额
    	$this->db->query("UPDATE {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp SET money = money + (deposit + bonus + refund - cost) WHERE 1");
    	//更新预警表余额
    	$this->db->query("UPDATE cp_wallet_statistics_warning a JOIN {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp b ON a.name=b.seller SET a.money = b.money");
    	//入统计表
    	$sql4 = "INSERT INTO cp_wallet_statistics_partner (seller,date,deposit,bonus,cost,refund,money,created)
    	SELECT seller,date,deposit,bonus,cost,refund,money,NOW()
    	FROM {$this->db_config['tmp']}.cp_wallet_statistics_partner_temp where 1
    	ON DUPLICATE KEY UPDATE deposit = values(deposit), bonus = values(bonus), cost = values(cost), refund = values(refund), money = values(money)";
    	$this->db->query($sql4);
    }
    
    /**
     * 商户余额预警
     */
    public function statisticsWarning()
    {
    	$result = $this->db->query("select * from cp_wallet_statistics_warning where 1")->getAll();
    	$insertFlag = false;
    	$content = '';
    	foreach ($result as $value)
    	{
    		if($value['name'] && ($value['money'] <= $value['warning_money']))
    		{
    			$insertFlag = true;
    			$content .= "{$value['name']} ";
    		}
    	}
    	
    	if($insertFlag)
    	{
    		$sql = "INSERT INTO cp_alert_log
    		(ctype,title,content,status,created) VALUES ('3', '合作商预存款报警', '合作商 {$content}预存款余额低于预警金额，请及时处理。', '0', NOW())";
    		$this->db->query($sql);
    	}
    }
}
