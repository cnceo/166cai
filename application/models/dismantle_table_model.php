<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dismantle_Table_Model extends MY_Model
{
	private $status = array('0', '21', '31', '600', '2000');
	public function __construct()
	{
		parent::__construct();
	}
	
	public function dis_orders()
	{
		$tableSuffix = date('Y');
		$this->db->query("create table if not exists cp_orders_{$tableSuffix} like cp_orders");
		$fields = $this->getFields("cp_orders");
		$fieldstr = implode(',', $fields);
		$this->db->query("create table if not exists bn_cpiao_tmp.cp_orders_dtmp like bn_cpiao.cp_orders");
		$isql = "insert ignore bn_cpiao_tmp.cp_orders_dtmp( $fieldstr ) select $fieldstr 
		from bn_cpiao.cp_orders where created < date_sub(CURDATE(), interval 90 day)";
		$this->db->query($isql);
		$this->db->query("update bn_cpiao_tmp.cp_orders_dtmp m join bn_cpiao.cp_orders n on m.orderId = n.orderId set n.dtable = '1'");
		$tsql = "select distinct(left(created, 4)) from bn_cpiao_tmp.cp_orders_dtmp";
		$dtables = $this->db->query($tsql)->getCol();
		if(!empty($dtables))
		{
			foreach ($dtables as $dtable)
			{
				$tname = "cp_orders_" . $dtable;
				$this->db->query("create table if not exists $tname like cp_orders");
				$isql = "insert ignore $tname( $fieldstr ) select $fieldstr 
						from bn_cpiao_tmp.cp_orders_dtmp where created like ?";
				$this->db->query($isql, array("$dtable%"));
			}
			$this->db->query("delete from bn_cpiao.cp_orders where dtable = '1'");
		}
		$this->db->query("drop table if exists bn_cpiao_tmp.cp_orders_dtmp");
	}
	
	public function dis_wallet_logs()
	{
		$tableSuffix = date('Y');
		$this->db->query("create table if not exists cp_wallet_logs_{$tableSuffix} like cp_wallet_logs");
		$fields = $this->getFields("cp_wallet_logs");
		array_unshift($fields, 'id');
		$fieldstr = implode(',', $fields);
		$this->db->query("create table if not exists cp_wallet_logs_dtmp like cp_wallet_logs");
		$this->db->query("delete from cp_wallet_logs_dtmp");
		$this->db->query("ALTER TABLE cp_wallet_logs_dtmp auto_increment = 1");
		$this->db->query("ALTER TABLE `cp_wallet_logs_dtmp` MODIFY COLUMN `id`  bigint(20) UNSIGNED NOT NULL FIRST"); //去除自增
		$isql = "insert ignore cp_wallet_logs_dtmp( $fieldstr ) select $fieldstr
		from cp_wallet_logs where created < date_sub(CURDATE(), interval 90 day)";
		$this->db->query($isql);
		$this->db->query("update cp_wallet_logs_dtmp m join cp_wallet_logs n on m.id = n.id set n.dtable = '1'");
		$tsql = "select distinct(left(created, 4)) from cp_wallet_logs_dtmp";
		$dtables = $this->db->query($tsql)->getCol();
		if(!empty($dtables))
		{
			foreach ($dtables as $dtable)
			{
				$tname = "cp_wallet_logs_" . $dtable;
				$this->db->query("create table if not exists $tname like cp_wallet_logs");
				$isql = "insert ignore $tname( $fieldstr ) select $fieldstr
				from cp_wallet_logs_dtmp where created like ?";
				$this->db->query($isql, array("$dtable%"));
			}
			$this->db->query("delete from cp_wallet_logs where dtable = '1'");
		}
	}
	
	public function dis_order_split($lid = 0)
	{	
		$tables = $this->getSplitTable($lid);
		$tableSuffix = date('Ym');
		$this->cfgDB->query("create table if not exists {$tables['split_table']}_{$tableSuffix} like {$tables['split_table']}");
		$this->cfgDB->query("create table if not exists cp_orders_relation_{$tableSuffix} like cp_orders_relation");
		$orderpre = date('Ymd', strtotime('-30 day'));
		$sql = "create table if not exists cp_orders_split_dtmp (
			id bigint unsigned not null auto_increment primary key,
			orderId char(50) default null,
			sub_order_id char(50) default null,
			unique key(orderId, sub_order_id)
		)engine = innodb";
		$this->cfgDB->query($sql);
		$this->cfgDB->query("delete from cp_orders_split_dtmp");
		$this->cfgDB->query("ALTER TABLE cp_orders_split_dtmp auto_increment = 1");
		$isql = "insert cp_orders_split_dtmp(orderId, sub_order_id) 
		select orderId, sub_order_id
		from {$tables['split_table']} where orderId < ? and status in('600', '1000', '2000') on duplicate key update sub_order_id = values(sub_order_id)";
		$this->cfgDB->query($isql, array($orderpre));
		$disorders = $this->cfgDB->query("select distinct(left(orderId, 6)) from cp_orders_split_dtmp")->getCol();
		if(!empty($disorders))
		{
			foreach ($disorders as $disorder)
			{
				$this->cfgDB->query("create table if not exists {$tables['split_table']}_$disorder like {$tables['split_table']}");
				if(empty($lid))
				{
					$this->cfgDB->query("create table if not exists cp_orders_relation_$disorder like cp_orders_relation");
				}
				$this->cfgDB->query("update cp_orders_split_dtmp m join {$tables['split_table']} n 
				on m.sub_order_id = n.sub_order_id set n.dtable = 1");
				if(empty($lid))
				{
					$this->cfgDB->query("update cp_orders_split_dtmp m join cp_orders_relation n 
					on m.sub_order_id = n.sub_order_id set n.dtable = 1");
				}
				
				$splitFields = $this->getFields("{$tables['split_table']}_$disorder", $this->db_config['cfg'], $this->cfgDB);
				$issql = "insert {$tables['split_table']}_$disorder(" . implode(',', $splitFields) . ") select n." . implode(', n.', $splitFields)
				. " from cp_orders_split_dtmp m join {$tables['split_table']} n on m.sub_order_id = n.sub_order_id
				where m.orderId like '$disorder%' and n.dtable = 1" . $this->onduplicate($splitFields, $splitFields, array(), "{$tables['split_table']}_$disorder.");
				$this->cfgDB->query($issql);
				if(empty($lid))
				{
					$relationFields = $this->getFields("cp_orders_relation_$disorder", $this->db_config['cfg'], $this->cfgDB);
					$issql = "insert cp_orders_relation_$disorder(" . implode(',', $relationFields) . ") select n." . implode(', n.', $relationFields)
					. " from cp_orders_split_dtmp m join cp_orders_relation n on m.sub_order_id = n.sub_order_id
					where m.orderId like '$disorder%' and n.dtable = 1 " . $this->onduplicate($relationFields, $relationFields, array(), "cp_orders_relation_$disorder.");
					$this->cfgDB->query($issql);
				}
			}
			$this->cfgDB->query("delete from {$tables['split_table']} where dtable = 1 and modified > date_sub(now(), interval 1 day)");
			if(empty($lid))
			{
				$this->cfgDB->query('delete from cp_orders_relation where dtable = 1 and modified > date_sub(now(), interval 1 day)');
			}
		}
	}
	
	/**
	 * 订单原始表分表操作
	 */
	public function dis_orders_ori()
	{
		$tableSuffix = date('Ym');
		$this->cfgDB->query("create table if not exists cp_orders_ori_{$tableSuffix} like cp_orders_ori");
		$fields = $this->getFields("cp_orders_ori", $this->db_config['cfg'], $this->cfgDB);
		$fieldstr = implode(',', $fields);
		$this->cfgDB->query("create table if not exists cp_orders_ori_dtmp like cp_orders_ori");
		$this->cfgDB->query("delete from cp_orders_ori_dtmp");
		$this->cfgDB->query("ALTER TABLE cp_orders_ori_dtmp auto_increment = 1");
		$isql = "insert ignore cp_orders_ori_dtmp( $fieldstr ) select $fieldstr
		from cp_orders_ori where created < date(date_sub(CURDATE(), interval 30 day)) and status in('600', '1000', '2000')";
		$this->cfgDB->query($isql);
		$tsql = "select distinct(left(created, 7)) from cp_orders_ori_dtmp";
		$dtables = $this->cfgDB->query($tsql)->getCol();
		if(!empty($dtables))
		{
			foreach ($dtables as $dtable)
			{
				$tname = "cp_orders_ori_" . str_replace('-', '', $dtable);
				$this->cfgDB->query("create table if not exists $tname like cp_orders_ori");
				$isql = "insert ignore $tname( $fieldstr ) select $fieldstr 
						from cp_orders_ori_dtmp where created like ?";
				$this->cfgDB->query($isql, array("$dtable%"));
			}
			
			$this->cfgDB->query("update cp_orders_ori_dtmp m join cp_orders_ori n on m.orderId = n.orderId set n.dtable = '1'");
			$this->cfgDB->query("delete from cp_orders_ori where dtable = '1'");
		}
	}
	
	public function dis_tables_gp($flag = false)
	{
		$sql = "select table_name from information_schema.tables where table_name like 'cp_orders_split_2016%'
		order by table_name";
		$tables = $this->cfgDB->query($sql)->getCol();
		array_push($tables, 'cp_orders_split');
		foreach ($tables as $table)
		{
			if($flag)
			{
				$this->cfgDB->query("delete from $table where lid in('21406', '21407')");
			}
			else 
			{
				$maps = array('21406' => 'syxw', '21407' => 'jxsyxw');
				foreach ($maps as $lid => $lname)
				{
					$splitFields = $this->getFields($table, $this->db_config['cfg'], $this->cfgDB);
					if($table=='cp_orders_split')
					{
						$ntable = $table . "_{$maps[$lid]}";
					}
					else 
					{
						$ntable = str_replace('_2016', "_{$maps[$lid]}_2016", $table);
					}
					$this->cfgDB->query("create table if not exists $ntable like $table");
					$issql = "insert ignore {$ntable}(" . implode(',', $splitFields) . ") select n." . implode(', n.', $splitFields)
					. " from $table n where n.lid = {$lid}";
					$this->cfgDB->query($issql);
				}
			}
		}
	}
	/**
	 * [dis_redpack_log 用户红包分表操作]
	 * @author JackLee 2017-03-30
	 * @return [type] [description]
	 */
	public function dis_redpack_log()
	{
		$tableSuffix = date('Y');
		$oriTable = 'cp_redpack_log';
		$tableName = $oriTable."_{$tableSuffix}";
		$dtmpTable = $oriTable."_dtmp";
		$fields = $this->getFields($oriTable);
		array_unshift($fields, 'id');
		if(!in_array('dtable', $fields))
		{
			//对原始表加入分表标识符号
			$alertSql = "ALTER TABLE `".$oriTable."` ADD `dtable` TINYINT UNSIGNED NOT NULL DEFAULT '0' COMMENT '分表标识符号' AFTER `remark`";
			$this->db->query($alertSql);
			//写入字段
			array_push($fields, 'dtable');
		}
		$fieldstr = implode(',', $fields);
		$this->db->query("create table if not exists $tableName like {$oriTable}");
		$this->db->query("create table if not exists $dtmpTable like $oriTable");
		$this->db->query("delete from $dtmpTable");
		$this->db->query("ALTER TABLE $dtmpTable auto_increment = 1");
		$this->db->query("ALTER TABLE `".$dtmpTable."` MODIFY COLUMN `id`  bigint(20) UNSIGNED NOT NULL FIRST"); //去除自增
		$flag = true;
		while ($flag) {
		    //挪走过期大于3个月后的数据 =>过期90天
		    $isql = "insert into $dtmpTable( $fieldstr ) select $fieldstr from $oriTable where valid_end < date_sub(CURDATE(), interval 90 day) and status <> 2 and valid_end != 0
		    and (aid <> 1 or (aid = 1 and rid <> 1)) order by id asc limit 50000";
		    $this->db->query($isql);
		    //已用 保留一年
		    $isql  = "insert into $dtmpTable( $fieldstr ) select $fieldstr from $oriTable where use_time <= date_sub(CURDATE(), interval 1 YEAR) and status = 2
		    and (aid <> 1 or (aid = 1 and rid <> 1)) order by id asc limit 50000";
		    $this->db->query($isql);
		    $this->db->query("update $dtmpTable m join $oriTable n on m.id = n.id set n.dtable = '1'");
		    $tsql = "select distinct(left(created, 4)) from $dtmpTable";
		    $dtables = $this->db->query($tsql)->getCol();
		    
		    if(!empty($dtables))
		    {
		        foreach ($dtables as $dtable)
		        {
		            $tname = $oriTable.'_' . $dtable;
		            $this->db->query("create table if not exists $tname like $oriTable");
		            $isql = "insert ignore $tname( $fieldstr ) select $fieldstr
		            from $dtmpTable where created like ?";
		            $this->db->query($isql, array("$dtable%"));
		        }
		        $this->db->query("delete from $oriTable where dtable = '1'");
		    }
		    
		    $count1 = $this->db->query("select id from $oriTable where valid_end < date_sub(CURDATE(), interval 90 day) and status <> 2 and valid_end != 0
		    and (aid <> 1 or (aid = 1 and rid <> 1))")->getOne();
		    $count2 = $this->db->query("select id from $oriTable where use_time <= date_sub(CURDATE(), interval 1 YEAR) and status = 2
		    and (aid <> 1 or (aid = 1 and rid <> 1))")->getOne();
		    if($count1 == 0 && $count2 == 0) {
		        $flag = false;
		    }
		}
	}
	
	/**
	 * 删除出票邮件60天之前数据
	 */
	public function dis_order_email_log()
	{
	    $this->db->query("DELETE FROM cp_order_email_logs WHERE created < date(date_sub(CURDATE(), interval 60 day))");
	}
}
