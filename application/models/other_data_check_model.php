<?php
/**
 * 对账操作数据库服务类
 * @author Administrator
 *
 */
class Other_Data_Check_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 返回所有需要拉取对账的配置信息
     */
    public function getCheckConfig()
    {
    	$sql = "select id, type, c_type, do_type, name, lib_name, exc_time, exc_date, fail_times, extend from cp_data_check_config where status = 1";
    	return $this->db->query($sql)->getAll();
    }
    
    /**
     * 更新配置表信息
     * @param unknown_type $id
     * @param unknown_type $data
     */
    public function updateCheckConfig($id, $data)
    {
    	$this->db->where('id', $id);
    	$this->db->update('cp_data_check_config', $data);
    	return $this->db->affected_rows();
    }
    
    /**
     * 返回票商需要重新对账记录
     */
    public function getTotalSplitRflag()
    {
    	return $this->db->query("select config_id, date, r_flag from cp_data_check_total_split where modified > date_sub(now(), interval 1 day) and r_flag = 1")->getAll();
    }
    
    /**
     * 返回充值需要重新对账记录
     */
    public function getTotalRechargeRflag()
    {
    	return $this->db->query("select config_id, date, r_flag from cp_data_check_total_recharge where modified > date_sub(now(), interval 1 day) and r_flag = 1")->getAll();
    }
    
    /**
     * 返回提现需要重新对账记录
     */
    public function getTotalWithdrawRflag()
    {
    	return $this->db->query("select config_id, date, r_flag from cp_data_check_total_withdraw where modified > date_sub(now(), interval 1 day) and r_flag = 1")->getAll();
    }
    
    /**
     * 返回充值配置信息
     * @param unknown_type $configId
     */
    public function getPayConfig($configId)
    {
    	return $this->db->query("select extra from cp_pay_config where id = ?", $configId)->getRow();
    }
    
    /**
     * 添加内容到报警表
     * @param int $ctype	报警类型
     * @param string $content	报警内容
     */
    public function insertAlert($ufield, $title, $content)
    {
    	$sql = "INSERT ignore INTO cp_alert_log
    	(ctype,ufiled,title,content,created) VALUES (24, ?, ?, ?, NOW())";
    	$this->db->query($sql, array($ufield,$title,$content));
    }
    
    /**
     * 数据入出票比对表
     * @param unknown_type $datas
     * @param unknown_type $fields
     */
    public function insertSplitCheck($datas, $fields)
    {
    	if(empty($datas))
    	{
    		return false;
    	}
    	
    	$s_data = array();
    	$sql = "insert {$this->db_config['tmp']}.cp_data_check_split(" . implode(',', $fields) . ") values";
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
    	$sql .= implode(',', $s_data) . $this->onduplicate($fields, $fields);
    	return $this->db->query($sql);
    }
    
    /**
     * 数据入充值比对表
     * @param unknown_type $datas
     * @param unknown_type $fields
     */
    public function insertRechargeCheck($datas, $fields)
    {
    	if(empty($datas))
    	{
    		return false;
    	}
    	 
    	$s_data = array();
    	$sql = "insert {$this->db_config['tmp']}.cp_data_check_recharge(" . implode(',', $fields) . ") values";
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
    	$sql .= implode(',', $s_data) . $this->onduplicate($fields, $fields);
    	return $this->db->query($sql);
    }
    
    /**
     * 数据入提现比对表
     * @param unknown_type $datas
     * @param unknown_type $fields
     */
    public function insertWithdrawCheck($datas, $fields)
    {
    	if(empty($datas))
    	{
    		return false;
    	}
    
    	$s_data = array();
    	$sql = "insert {$this->db_config['tmp']}.cp_data_check_withdraw(" . implode(',', $fields) . ") values";
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
    	$sql .= implode(',', $s_data) . $this->onduplicate($fields, $fields);
    	return $this->db->query($sql);
    }
    
    /**
     * 清空出票比对表
     */
    public function truncateSplitCheck()
    {
    	return $this->db->query("truncate {$this->db_config['tmp']}.cp_data_check_split");
    }
    
    /**
     * 清空充值比对表
     */
    public function truncateRechargeCheck()
    {
    	return $this->db->query("truncate {$this->db_config['tmp']}.cp_data_check_recharge");
    }
    
    /**
     * 清空提现比对表
     */
    public function truncateWithdrawCheck()
    {
    	return $this->db->query("truncate {$this->db_config['tmp']}.cp_data_check_withdraw");
    }
    
    /**
     * 执行出票比对操作
     * @param string $checkDate 比对日期
     * @param array $config 对账配置信息
     * @param int $r_flag   是否为重新比对   1 是 0 否
     * @return boolean
     */
    public function checkSplit($checkDate, $config, $r_flag)
    {
    	$tableName = "{$this->db_config['tmp']}.cp_data_check_split";
    	$lidConfig = $this->orderConfig('lidmap');
    	$lids = array_keys($lidConfig);
    	//各彩种比对信息比对表
    	foreach ($lids as $lid)
    	{
    		$tables = $this->getSplitTable($lid);
    		$sql = "select sub_order_id, '{$checkDate}' as date, if(status = '600', 0, 1) as s_status, money as s_money 
    		from {$tables['split_table']} where modified > date_sub(now(), interval 10 day) and lid='{$lid}' 
    		and (ticket_submit_time >= '{$checkDate}' and ticket_submit_time <='{$checkDate} 23:59:59') 
    		and ticket_seller = '{$config['name']}' and status >= '500'";
    		$splitOrder = $this->slaveCfg->query($sql)->getAll();
    		if($splitOrder)
    		{
    			$this->insertSplitCheck($splitOrder, array('sub_order_id', 'date', 's_status', 's_money'));
    		}
    	}
    	
    	//缓存池数据入比对表
    	$beforeDay = date('Y-m-d', strtotime($checkDate) - 86400);
    	$sql1 = "select sub_order_id, from_unixtime(unix_timestamp(date) + 86400, '%Y-%m-%d') as date, date as s_date, s_status, s_money from {$this->db_config['tmp']}.cp_data_check_split_cache where 
    	date = '$beforeDay' and config_id='{$config['id']}'";
    	$cacheOrder = $this->db->query($sql1)->getAll();
    	$this->insertSplitCheck($cacheOrder, array('sub_order_id', 'date', 's_date', 's_status', 's_money'));
    	if($r_flag)
    	{
    		//删除差错池中数据 主要适应重跑逻辑
    		$sql2 = "delete from cp_data_check_error_split where config_id='{$config['id']}' and date = '{$checkDate}'";
    		$this->db->query($sql2);
    	}
		//数据入缓存池
		$sql3 = "insert ignore {$this->db_config['tmp']}.cp_data_check_split_cache(config_id, sub_order_id, date, s_status, s_money) 
		select '{$config['id']}', sub_order_id, date, s_status, s_money from $tableName 
		where s_status=1 and o_status = 0 and s_date is null";
		$this->db->query($sql3);
		$e_flag = 0;
		//数据入差错池  第一步 网站和票商出票都成功  金额不一致
		$sql4 = "insert ignore cp_data_check_error_split(config_id, sub_order_id, date, s_date, s_status, o_status, s_money, o_money,created) 
		select '{$config['id']}', sub_order_id, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName 
		where s_status = 1 and o_status = 1 and s_money != o_money";
		$this->db->query($sql4);
		$affectedRows = $this->db->affected_rows();
		if($affectedRows)
		{
			$e_flag = 1;
		}
		
		//数据入差错池  第二步 前一天缓存池数据比对不一致
		$sql5 = "insert ignore cp_data_check_error_split(config_id, sub_order_id, date, s_date, s_status, o_status, s_money, o_money,created)
		select '{$config['id']}', sub_order_id, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName
		where s_status = 1 and o_status = 0 and s_date is not null";
		$this->db->query($sql5);
		$affectedRows = $this->db->affected_rows();
		if($affectedRows)
		{
			$e_flag = 1;
		}
		
		//数据入差错池  第三步 网站出票失败，票商成功
		$sql6 = "insert ignore cp_data_check_error_split(config_id, sub_order_id, date, s_date, s_status, o_status, s_money, o_money, created)
		select '{$config['id']}', sub_order_id, date, s_date, s_status, o_status, s_money, o_money,now() from $tableName
		where s_status = 0 and o_status = 1";
		$this->db->query($sql6);
		$affectedRows = $this->db->affected_rows();
		if($affectedRows)
		{
			$e_flag = 1;
		}
		
    	//统计数据
    	$sql7 = "SELECT SUM(IF(s_status=1, s_money, 0)) as s_money, SUM(IF(s_status=1 and o_status = 0 and s_date is null, s_money, 0)) c_money, SUM(IF(o_status=1, o_money, 0)) o_money FROM $tableName WHERE 1 GROUP BY date";
    	$total = $this->db->query($sql7)->getRow();
    	$s_money = $total['s_money'] - $total['c_money'];
    	$sql8 = "insert into cp_data_check_total_split(config_id, date, s_money, o_money, status, e_flag, r_flag, created) values 
    	('{$config['id']}', '{$checkDate}', '{$s_money}', '{$total['o_money']}', 1, '{$e_flag}', 0, now()) 
    	on duplicate key update s_money = values(s_money), o_money = values(o_money), status = values(status), e_flag = values(e_flag), r_flag = values(r_flag)";
    	$this->db->query($sql8);
    	//清除缓存表10天之前的记录
    	$this->db->query("delete from {$this->db_config['tmp']}.cp_data_check_split_cache where date < date_sub(from_unixtime(unix_timestamp('{$checkDate}')), interval 10 day) and config_id = '{$config['id']}'");
    	if(!$r_flag)
    	{
    		//更新配置表统计日期
    		$this->db->query("update cp_data_check_config set exc_date = '{$checkDate}', fail_times = 0 where id = {$config['id']}");
    	}
    	
    	return true;
    }
    
    /**
     * 执行充值比对操作
     * @param string $checkDate 比对日期
     * @param array $config 对账配置信息
     * @param int $r_flag   是否为重新比对   1 是 0 否
     * @return boolean
     */
    public function checkRecharge($checkDate, $config, $r_flag)
    {
    	$tableName = "{$this->db_config['tmp']}.cp_data_check_recharge";
    	if(isset($config['startTime']) && isset($config['endTime']))
    	{
    		$sql = "SELECT p.trade_no, '{$checkDate}' as date, p.`status` as s_status, p.money as s_money FROM cp_pay_logs p INNER JOIN cp_wallet_logs w ON w.trade_no=p.trade_no
    		WHERE w.recharge_over_time >= '{$config['startTime']}' AND w.recharge_over_time <='{$config['endTime']}' AND p.`status`='1' AND p.rcg_serial IN({$config['c_type']})";
    	}
    	else 
    	{
    		$sql = "SELECT p.trade_no, '{$checkDate}' as date, p.`status` as s_status, p.money as s_money FROM cp_pay_logs p INNER JOIN cp_wallet_logs w ON w.trade_no=p.trade_no
    		WHERE w.recharge_over_time >= '{$checkDate}' AND w.recharge_over_time <='{$checkDate} 23:59:59' AND p.`status`='1' AND p.rcg_serial IN({$config['c_type']})";
    	}
    	$rechargeOrder = $this->slave->query($sql)->getAll();
    	if($rechargeOrder)
    	{
    		$this->insertRechargeCheck($rechargeOrder, array('trade_no', 'date', 's_status', 's_money'));
    	}
    	
    	//缓存池数据入比对表
    	$beforeDay = date('Y-m-d', strtotime($checkDate) - 86400);
    	$sql1 = "select trade_no, from_unixtime(unix_timestamp(date) + 86400, '%Y-%m-%d') as date, date as s_date, o_status, o_money from {$this->db_config['tmp']}.cp_data_check_recharge_cache where
    	date = '$beforeDay' and config_id='{$config['id']}'";
    	$cacheOrder = $this->db->query($sql1)->getAll();
    	$this->insertRechargeCheck($cacheOrder, array('trade_no', 'date', 's_date', 'o_status', 'o_money'));
    	if($r_flag)
    	{
    		//删除差错池中数据 主要适应重跑逻辑
    		$sql2 = "delete from cp_data_check_error_recharge where config_id='{$config['id']}' and date = '{$checkDate}'";
    		$this->db->query($sql2);
    	}
    	
    	//数据入缓存池
    	$sql3 = "insert ignore {$this->db_config['tmp']}.cp_data_check_recharge_cache(config_id, trade_no, date, o_status, o_money)
    	select '{$config['id']}', trade_no, date, o_status, o_money from $tableName
    	where o_status=1 and s_status = 0 and s_date is null";
    	$this->db->query($sql3);
    	//修补比对表中缓存池来的数据网站金额 (产品确认不需要了)
    	//$sql4 = "update $tableName a INNER JOIN cp_pay_logs b on a.trade_no = b.trade_no SET a.s_money = b.money WHERE a.s_date is not null and a.s_status = 0";
    	//$this->db->query($sql4);
    	$e_flag = 0;
    	//数据入差错池  第一步 网站和第三方都成功  金额不一致
    	$sql5 = "insert ignore cp_data_check_error_recharge(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money,created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName
    	where s_status = 1 and o_status = 1 and s_money != o_money";
    	$this->db->query($sql5);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
    	
    	//数据入差错池  第二步 前一天缓存池数据比对不一致
    	$sql5 = "insert ignore cp_data_check_error_recharge(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money,created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName
    	where o_status = 1 and s_status = 0 and s_date is not null";
    	$this->db->query($sql5);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
    	
    	//数据入差错池  第三步 网站成功，第三方失败
    	$sql6 = "insert ignore cp_data_check_error_recharge(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money, created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money,now() from $tableName
    	where s_status = 1 and o_status = 0";
    	$this->db->query($sql6);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
    	
    	//统计数据
    	$sql7 = "SELECT SUM(IF(s_status=1, s_money, 0)) as s_money, SUM(IF(o_status=1 and s_status = 0 and s_date is null, o_money, 0)) c_money, SUM(IF(o_status=1, o_money, 0)) o_money FROM $tableName WHERE 1 GROUP BY date";
    	$total = $this->db->query($sql7)->getRow();
    	$s_money = $total['s_money'] + $total['c_money'];
    	$sql8 = "insert into cp_data_check_total_recharge(config_id, date, s_money, o_money, status, e_flag, r_flag, created) values
    	('{$config['id']}', '{$checkDate}', '{$s_money}', '{$total['o_money']}', 1, '{$e_flag}', 0, now())
    	on duplicate key update s_money = values(s_money), o_money = values(o_money), status = values(status), e_flag = values(e_flag), r_flag = values(r_flag)";
    	$this->db->query($sql8);
    	//清除缓存表10天之前的记录
    	$this->db->query("delete from {$this->db_config['tmp']}.cp_data_check_recharge_cache where date < date_sub(from_unixtime(unix_timestamp('{$checkDate}')), interval 10 day) and config_id = '{$config['id']}'");
    	if(!$r_flag)
    	{
    		//更新配置表统计日期
    		$this->db->query("update cp_data_check_config set exc_date = '{$checkDate}', fail_times = 0 where id = {$config['id']}");
    	}
    	
    	return true;
    }
    
    /**
     * 执行提现比对操作
     * @param string $checkDate 比对日期
     * @param array $config 对账配置信息
     * @param int $r_flag   是否为重新比对   1 是 0 否
     * @return boolean
     */
    public function checkWithdraw($checkDate, $config, $r_flag)
    {
    	$tableName = "{$this->db_config['tmp']}.cp_data_check_withdraw";
    	if(isset($config['startTime']) && isset($config['endTime']))
    	{
    		$sql = "SELECT trade_no, '{$checkDate}' as date, 1 as s_status, money as s_money FROM cp_withdraw 
    		WHERE succ_time >= '{$config['startTime']}' AND succ_time <='{$config['endTime']}' AND `status`='2'";
    	}
    	else
    	{
    		$sql = "SELECT trade_no, '{$checkDate}' as date, 1 as s_status, money as s_money FROM cp_withdraw
    		WHERE succ_time >= '{$checkDate}' AND succ_time <='{$checkDate} 23:59:59' AND `status`='2'";
    	}
    	$withdrawOrder = $this->slave->query($sql)->getAll();
    	if($withdrawOrder)
    	{
    		$this->insertWithdrawCheck($withdrawOrder, array('trade_no', 'date', 's_status', 's_money'));
    	}
    	 
    	//缓存池数据入比对表
    	$beforeDay = date('Y-m-d', strtotime($checkDate) - 86400);
    	$sql1 = "select trade_no, from_unixtime(unix_timestamp(date) + 86400, '%Y-%m-%d') as date, date as s_date, o_status, o_money from {$this->db_config['tmp']}.cp_data_check_withdraw_cache where
    	date = '$beforeDay' and config_id='{$config['id']}'";
    	$cacheOrder = $this->db->query($sql1)->getAll();
    	$this->insertWithdrawCheck($cacheOrder, array('trade_no', 'date', 's_date', 'o_status', 'o_money'));
    	if($r_flag)
    	{
    		//删除差错池中数据 主要适应重跑逻辑
    		$sql2 = "delete from cp_data_check_error_withdraw where config_id='{$config['id']}' and date = '{$checkDate}'";
    		$this->db->query($sql2);
    	}
    	 
    	//数据入缓存池
    	$sql3 = "insert ignore {$this->db_config['tmp']}.cp_data_check_withdraw_cache(config_id, trade_no, date, o_status, o_money)
    	select '{$config['id']}', trade_no, date, o_status, o_money from $tableName
    	where o_status=1 and s_status = 0 and s_date is null";
    	$this->db->query($sql3);
    	//修补比对表中缓存池来的数据网站金额(产品确认不需要了)
    	//$sql4 = "update $tableName a INNER JOIN cp_withdraw b on a.trade_no = b.trade_no SET a.s_money = b.money WHERE a.s_date is not null and a.s_status = 0";
    	//$this->db->query($sql4);
    	$e_flag = 0;
    	//数据入差错池  第一步 网站和第三方都成功  金额不一致
    	$sql5 = "insert ignore cp_data_check_error_withdraw(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money,created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName
    	where s_status = 1 and o_status = 1 and s_money != o_money";
    	$this->db->query($sql5);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
     
    	//数据入差错池  第二步 前一天缓存池数据比对不一致
    	$sql5 = "insert ignore cp_data_check_error_withdraw(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money,created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money, now() from $tableName
   		where o_status = 1 and s_status = 0 and s_date is not null";
    	$this->db->query($sql5);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
     
    	//数据入差错池  第三步 网站成功，第三方失败
   		$sql6 = "insert ignore cp_data_check_error_withdraw(config_id, trade_no, date, s_date, s_status, o_status, s_money, o_money, created)
    	select '{$config['id']}', trade_no, date, s_date, s_status, o_status, s_money, o_money,now() from $tableName
    	where s_status = 1 and o_status = 0";
    	$this->db->query($sql6);
    	$affectedRows = $this->db->affected_rows();
    	if($affectedRows)
    	{
    		$e_flag = 1;
    	}
     
    	//统计数据
    	$sql7 = "SELECT SUM(IF(s_status=1, s_money, 0)) as s_money, SUM(IF(o_status=1 and s_status = 0 and s_date is null, o_money, 0)) c_money, SUM(IF(o_status=1, o_money, 0)) o_money FROM $tableName WHERE 1 GROUP BY date";
    	$total = $this->db->query($sql7)->getRow();
    	$s_money = $total['s_money'] + $total['c_money'];
    	$sql8 = "insert into cp_data_check_total_withdraw(config_id, date, s_money, o_money, status, e_flag, r_flag, created) values
    	('{$config['id']}', '{$checkDate}', '{$s_money}', '{$total['o_money']}', 1, '{$e_flag}', 0, now())
    	on duplicate key update s_money = values(s_money), o_money = values(o_money), status = values(status), e_flag = values(e_flag), r_flag = values(r_flag)";
    	$this->db->query($sql8);
    	//清除缓存表10天之前的记录
    	$this->db->query("delete from {$this->db_config['tmp']}.cp_data_check_withdraw_cache where date < date_sub(from_unixtime(unix_timestamp('{$checkDate}')), interval 10 day) and config_id = '{$config['id']}'");
    	if(!$r_flag)
    	{
    		//更新配置表统计日期
    		$this->db->query("update cp_data_check_config set exc_date = '{$checkDate}', fail_times = 0 where id = {$config['id']}");
    	}
     
    	return true;
    }
}
