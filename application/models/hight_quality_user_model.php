<?php

class Hight_Quality_User_Model extends MY_Model 
{
    public function __construct() 
    {
        parent::__construct();
        $this->cfg_lidmap = $this->orderConfig('lidmap');
    }
    
    public function quality_user_statistic($stime)
    {
    	$cfields = array('uid', 'uname', 'total_betmoney', 'total_winmoney', 'order_num', 'account');
    	$ufields = array('total_betmoney', 'total_winmoney', 'order_num', 'account');
    	$afields = array('total_betmoney', 'total_winmoney', 'order_num');
    	$lids = array_keys($this->cfg_lidmap);
    	$sdate = date('Y-m-d', $stime);
    	$edate = date('Y-m-d', strtotime('1 day', $stime));
    	//还原累积数据
    	$sql = "update cp_hight_quality_user m join cp_hight_quality_user_index n on m.uid = n.uid 
    	set " . implode(', ', array_map(array($this, 'get_init_fvalus'), $afields)) . ",
    	n.total_recharge = n.total_recharge - m.total_recharge,
    	n.total_withdraw = n.total_withdraw - m.total_withdraw
    	where m.cdate = '$sdate'";
    	$this->db->query($sql);
    	//初始化数据
    	$sql = "delete from cp_hight_quality_user where cdate = '$sdate'";
    	$this->db->query($sql);
    	//产生原始数据
        $table = 'cp_hight_quality_user';
        $fields = array('cfields' => $cfields, 'ufields' => $ufields, 'afields' => $afields);
        $fields['cfields'] = array_merge($fields['cfields'], array('cdate', 'created'));
        $select = "select m.id, m.uid, n.uname, (m.money - m.failMoney) total_betmoney, m.bonus total_winmoney, 
        1 order_num, n.money account, '$sdate' cdate, now() created
        from cp_orders m join cp_user n
    	on m.uid = n.uid where m.pay_time >= '$sdate' and m.pay_time < '$edate' and m.id > ?
    	and m.status > 40 and m.status != 600 and m.orderType != 4 and m.modified >= '$sdate' order by m.id limit 500";
    	$this->insert_data($table, $fields, $select);
    	//------------------------------------------------------------------------------
    	//获得中奖总额
    	$sql = "update cp_hight_quality_user set total_winmoney = 0 where cdate = '$sdate'";
    	$this->db->query($sql);
        $fields = array('cfields' => array('uid', 'uname', 'total_winmoney', 'cdate', 'created'),
            'ufields' => array('total_winmoney'), 'afields' => array('total_winmoney'));
        $select = "select m.id, m.uid, n.uname, m.bonus total_winmoney,
    	'$sdate' cdate, now() created from cp_orders m join cp_user n
    	on m.uid = n.uid where m.win_time >= '$sdate' and m.win_time < '$edate' 
    	and m.status = 2000 and m.orderType != 4 and m.modified >= '$sdate' and m.id > ? order by m.id limit 500";
        $this->insert_data($table, $fields, $select);
    	//---------------------------------------------------------------------------------------
    	//计算累计数据
    	$sql = "insert cp_hight_quality_user_index(" . implode(', ', $cfields) . ", login_time, created) 
    	select " . implode(', ', $cfields) . ", login_time, now() from cp_hight_quality_user 
    	where cdate = '$sdate' "; 
    	array_push($cfields, 'login_time');
    	array_push($ufields, 'login_time');
    	$sql .= $this->onduplicate($cfields, $ufields, $afields, 'cp_hight_quality_user_index.');
    	$this->db->query($sql);
    	//计算所有用户最近30天的登录次数
		//$this->cal_login_times($sdate, $edate);
    }
    
    public function total_recharge_withdraw($stime)
    {
    	$sdate = date('Y-m-d', $stime);
    	$edate = date('Y-m-d', strtotime('1 day', $stime));
    	$inisql = "update cp_hight_quality_user m join cp_hight_quality_user_index n
    	on m.uid = n.uid 
    	set n.total_recharge = n.total_recharge - m.total_recharge,
    	n.total_withdraw = n.total_withdraw - m.total_withdraw
    	where m.cdate = '$sdate'";
    	//$this->db->query($inisql);
    	//$this->db->query("update cp_hight_quality_user set total_recharge = 0, total_withdraw = 0 where cdate = '$sdate'");

        $table = 'cp_hight_quality_user';
        $fields = array('cfields' => array('uid', 'uname', 'cdate', 'total_recharge'),
            'ufields' => array('total_recharge'), 'afields' => array('total_recharge'));
        $select = "select m.id, m.uid, n.uname, '$sdate' cdate, m.money total_recharge from cp_wallet_logs m
    	join cp_user n on m.uid = n.uid where ctype = 0
    	and mark = '1' and m.modified >= '$sdate' and m.modified < '$edate' and m.id > ? order by m.id limit 500";
        $this->insert_data($table, $fields, $select);
    	//------------------------------------------------------------
        $fields = array('cfields' => array('uid', 'uname', 'cdate', 'total_withdraw'),
            'ufields' => array('total_withdraw'), 'afields' => array('total_withdraw'));
        $select = "select m.id, m.uid, n.uname, '$sdate' cdate, m.money total_withdraw from cp_withdraw m
    	join cp_user n on m.uid = n.uid 
    	where m.status = 2 and m.succ_time >= '$sdate' and m.succ_time < '$edate' and m.id > ? order by m.id limit 500";
        $this->insert_data($table, $fields, $select);
    	//-----------------------------------------------------------------
    	$isql = "insert cp_hight_quality_user_index(uid, uname, total_recharge, total_withdraw)
    	select uid, uname, total_recharge, total_withdraw from cp_hight_quality_user where 
    	cdate = '$sdate' " . $this->onduplicate(array('total_recharge', 'total_withdraw', 'uname'), array('total_recharge', 'total_withdraw', 'uname'),
    	array('total_recharge', 'total_withdraw'), 'cp_hight_quality_user_index.');
    	$this->db->query($isql);
    }
    
    private function cal_login_times($sdate, $edate)
    {
    	$tmptable = "{$this->db_config['tmp']}.cp_hight_quality_user_tmp";
    	$csql = "create table if not exists $tmptable(
    		id int unsigned not null auto_increment primary key,
    		uid bigint unsigned default null,
    		login_times_30day int unsigned not null default 0,
    		account bigint unsigned not null default 0,
    		unique key(uid)
    	)engine = innodb;";
    	$this->db->query($csql);
    	$maxuid = 0;
    	$this->db->query("truncate $tmptable;");
    	$isql = "insert ignore $tmptable(uid) 
    	select uid from cp_hight_quality_user_index where uid > ? order by uid limit 10000;";
    	$this->db->query($isql, array($maxuid));
    	$count = $this->db->query("select count(*) from $tmptable")->getOne();
    	while(!empty($count))
    	{
    		$sql = "insert $tmptable(uid, login_times_30day) select m.uid, count(*) 
	    	from cp_login_info m join $tmptable n 
	    	on m.uid = n.uid where m.created >= date_sub('$sdate', interval 30 day) 
	    	and m.created < '$edate' group by uid " . 
	    	$this->onduplicate(array('login_times_30day'), array('login_times_30day'));
	    	$this->db->query($sql);
	    	//获取当前余额信息
	    	$this->db->query("update $tmptable m join cp_user n on m.uid = n.uid set m.account = n.money");
	    	$usql = "update cp_hight_quality_user_index m join $tmptable n on m.uid = n.uid
	    	set m.login_times_30day = n.login_times_30day, m.account = n.account";
	    	$this->db->query($usql);
	    	$maxuid = $this->db->query("select max(uid) from $tmptable")->getOne();
	    	$this->db->query("truncate $tmptable;");
	    	$isql = "insert ignore $tmptable(uid) 
	    	select uid from cp_hight_quality_user_index where uid > ? order by uid limit 10000;";
	    	$this->db->query($isql, array($maxuid));
	    	$count = $this->db->query("select count(*) from $tmptable")->getOne();
    	}
    	
    }
    
    private function get_init_fvalus($field)
    {
    	return "n.$field = n.$field - m.$field";
    }
    
    private function get_betmoney_fields($lids)
    {
    	$fields = array();
    	foreach ($lids as $lid)
    	{
    		array_push($fields, "betmoney$lid");
    	}
    	return $fields;
    }
    
	private function get_betmoney_values($lids)
    {
    	$fields = array();
    	foreach ($lids as $lid)
    	{
    		array_push($fields, "if(m.lid = $lid, m.money, 0) as betmoney$lid");
    	}
    	return $fields;
    }

    private function insert_data($table, $fields, $select){
        $start = 0;
        $datas = $this->db->query($select, array($start))->getAll();
        while(!empty($datas)){
            $data_sql = array();
            $data_bid = array();
            foreach ($datas as $row){
                $start = $row['id'];
                array_push($data_sql, '(' . implode(',', array_map(array($this, 'sql_map'), $fields['cfields'])) . ')');
                foreach ($fields['cfields'] as $field){
                    array_push($data_bid, $row[$field]);
                }
            }
            if(!empty($data_sql)){
                $sql = "insert $table(" . implode(', ', $fields['cfields']) . ")values" . implode(', ', $data_sql) .
                $this->onduplicate($fields['ufields'], $fields['ufields'], $fields['afields']);
                $this->db->query($sql, $data_bid);
            }
            $datas = $this->db->query($select, array($start))->getAll();
        }
    }

    private function sql_map($val){
        return '?';
    }
}