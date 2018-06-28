<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：订单管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_order extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：获取订单列表
     * 修改日期：2014.11.11
     */
    function list_orders($searchData, $page, $pageCount)
    {
    	$where = 'where 1';
    	$data = array();
    	$startSuffix = $this->tools->getTableSuffixByDate($searchData['start_time']);
    	$endSuffix = $this->tools->getTableSuffixByDate($searchData['end_time']);
    	if($startSuffix) $startSuffix = '_' . $startSuffix;
    	if($endSuffix) $endSuffix = '_' . $endSuffix;
    	
    	$result = array('data' => array(), 'count'=> array('count' => 0, 'ucount' => 0, 'money' => 0, 'margin' => 0, 'bonus' => 0, 'failMoney' => 0, 'cpmoney' => 0));
    	
    	if ($this->emp($searchData['sub_order_id'])) {
    		if (preg_match('/(.*)(\d{22})(.*)/', $searchData['sub_order_id'], $matches) && $matches[2] && empty($matches[1]) && empty($matches[3])) {
    			$nlid = array_flip($this->orderConfig('nlid'));
    			$lid = $nlid[substr($searchData['sub_order_id'], -2)];
    			$stable = $this->getSplitTable($lid);
    			$stable = $stable['split_table'];
    			$stableSuffix = $this->tools->getTableSuffixByOrder($searchData['sub_order_id']);
    			if ($stableSuffix) $stable .= "_".$stableSuffix;
    			if ($this->slaveCfg1->query("select table_name from `INFORMATION_SCHEMA`.`TABLES` where table_name ='".$stable."' and TABLE_SCHEMA='bn_cpiao_cfg'")->num_rows) {
    			    $searchData['orderId'] = $this->slaveCfg1->query("select orderId from {$stable} where sub_order_id = ?", array($searchData['sub_order_id']))->row()->orderId;
    				unset($searchData['sub_order_id']);
    			}
    			if (empty($searchData['orderId'])) {
    				return $result;
    			}
    		}else {
    			return $result;
    		}
    	}
    	
    	if ($searchData['seller'] > 0) {
    	    $shopids = $this->BcdDb->query('select id from cp_partner_shop where partnerId = ?', array($searchData['seller']))->getCol();
    		$where .= " and shopId in (".implode(',', $shopids).")";
    	}
    	unset($searchData['seller']);
    	
    	$where .= $this->condition(" #TABLE#.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
    	unset($searchData['start_time'], $searchData['end_time']);
    	if ($this->emp($searchData['start_money'])) {
    		$where .= " and #TABLE#.money >= ?";
    		$data[] = $searchData['start_money']*100;
    		unset($searchData['start_money']);
    	}
    	if ($this->emp($searchData['end_money'])) {
    		$where .= " and #TABLE#.money <= ?";
    		$data[] = $searchData['end_money']*100;
    		unset($searchData['end_money']);
    	}
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where .= " and {$this->cp_user}.reg_type in ('0', '2')";
            }
            else
            {
                $where .= " and {$this->cp_user}.reg_type = ".$searchData['reg_type'];
            } 
        }
        unset($searchData['reg_type']);
    	
    	foreach ($searchData as $key => $val) {
    		if ($this->emp($val)) {
    			if ($key === 'name') {
    				$where .= " and ({$this->cp_user}.uname = ? or #TABLE#.orderId = ?)";
    				$data[] = $val;
    				$data[] = $val;
    			}elseif ($key === 'issue') {
    				$where .= " and (#TABLE#.issue = ? or #TABLE#.codecc like ?)";
    				$data[] = trim($val);
    				$data[] = "%".trim($val)."%";
    			}elseif ($key === 'status' && $val == 'success') {
    				$where .= " and #TABLE#.status in('500','1000','2000')";
    			}elseif (in_array($key, array('channel'))) {
    				$where .= " and {$this->cp_user}.`{$key}` = ?";
    				$data[] = $val;
    			}elseif ($key === 'orderType' && $searchData['orderType'] == 3) {
    				$where .= " and #TABLE#.orderType in('3','6')";
                }elseif ($key === 'playType' && $searchData['lid'] == 55) {
                    $playTypeArrs = explode(',', $searchData['playType']);
                    $where .= " and #TABLE#.playType in('" . implode(', ', $playTypeArrs) . "')";
    			}else {
    				$where .= " and #TABLE#.`{$key}` = ?";
    				$data[] = $val;
    			}
    		}
    	}
    	
    	if ($startSuffix == $endSuffix) {
    		$table = $this->cp_orders.$startSuffix;
    		$where = str_replace('#TABLE#', $table, $where);
    		$sql = "select {$table}.orderId, {$this->cp_user}.uname as userName, {$table}.lid, {$table}.playType, {$table}.issue, {$table}.created, {$table}.money,
    				{$table}.margin, {$table}.status, {$table}.my_status, {$table}.buyPlatform,{$table}.orderType, {$this->cp_user}.channel, {$this->cp_user}.reg_type, {$this->cp_user}.uid
		    		from {$table} inner join {$this->cp_user} on {$table}.uid = {$this->cp_user}.uid {$where}
		    		ORDER BY {$table}.created DESC
		         	LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    		$sqlc = "SELECT COUNT(*) as count, count(distinct({$table}.uid)) as ucount, sum({$table}.money) as money, sum({$table}.margin) as margin, sum({$table}.bonus) as bonus, 
    				sum({$table}.failMoney) as failMoney, sum(case when status in ('500','510','1000','2000') then ({$table}.money-{$table}.failMoney) else 0 end) as cpmoney
    		 		FROM {$table} inner join {$this->cp_user} on {$table}.uid={$this->cp_user}.uid {$where}";
    		$res = $this->BcdDb->query($sql, $data)->getAll();
    		$count = $this->BcdDb->query($sqlc, $data)->getRow();
    	}else {
    		
    		$table1 = $this->cp_orders.$startSuffix;
    		$where1 = str_replace('#TABLE#', $table1, $where);
    		$table2 = $this->cp_orders.$endSuffix;
    		$where2 = str_replace('#TABLE#', $table2, $where);
    		
    		$sql = "SELECT tmp.* FROM (select {$table1}.orderId, {$this->cp_user}.uname as userName, {$table1}.lid, {$table1}.playType, {$table1}.issue, {$this->cp_user}.uid, 
    				{$table1}.created, {$table1}.money, {$table1}.margin, {$table1}.status, {$table1}.my_status, {$table1}.buyPlatform,{$table1}.orderType,{$this->cp_user}.channel,{$this->cp_user}.reg_type
		    		from {$table1} inner join {$this->cp_user} on {$table1}.uid = {$this->cp_user}.uid {$where1}
		    		union 
		    		select  {$table2}.orderId, {$this->cp_user}.uname as userName, {$table2}.lid, {$table2}.playType, {$table2}.issue, {$this->cp_user}.uid, 
    				{$table2}.created, {$table2}.money, {$table2}.bonus, {$table2}.status, {$table2}.my_status, {$table2}.buyPlatform, {$table2}.orderType,{$this->cp_user}.channel,{$this->cp_user}.reg_type 
		    		from {$table2} inner join {$this->cp_user} on {$table2}.uid = {$this->cp_user}.uid {$where2}) tmp
		    		ORDER BY tmp.created DESC
		    		LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    		$sqlc = "select sum(count) as count, count(DISTINCT(uid)) as ucount, sum(money) as money, sum(margin) as margin, sum(bonus) as bonus, sum(money) as money, sum(cpmoney) as cpmoney 
    				from (SELECT COUNT(*) as count, {$table1}.uid, sum({$table1}.money) as money,
		    		sum({$table1}.margin) as margin, sum({$table1}.bonus) as bonus, sum({$table1}.failMoney) as failMoney,
		    		sum(case when status in ('500','510','1000','2000') then ({$table1}.money-{$table1}.failMoney) else 0 end) as cpmoney
		    		FROM {$table1} inner join {$this->cp_user} on {$table1}.uid={$this->cp_user}.uid {$where1}
		    		group by {$table1}.uid union 
		    		SELECT COUNT(*) as count, {$table2}.uid, sum({$table2}.money) as money,
		    		sum({$table2}.margin) as margin, sum({$table2}.bonus) as bonus, sum({$table2}.failMoney) as failMoney,
		    		sum(case when status in ('500','510','1000','2000') then ({$table2}.money-{$table2}.failMoney) else 0 end) as cpmoney
		    		FROM {$table2} inner join {$this->cp_user} on {$table2}.uid={$this->cp_user}.uid {$where2}
		    		group by {$table2}.uid) tmp";
    		$res = $this->BcdDb->query($sql, array_merge($data, $data))->getAll();
    		$count = $this->BcdDb->query($sqlc, array_merge($data, $data))->getRow();
    	}
    	
        return array(
	        'data' => $res,
	        'count' => $count,
        );
    }
    
    /**
     * 参    数：$id id
     * 作    者：wangl
     * 功    能：获取订单详细信息
     * 修改日期：2014.11.11
     */
    public function find_order_by_id($id)
    {
        $select = "SELECT {$this->cp_orders}.* FROM {$this->cp_orders}  WHERE {$this->cp_orders}.id = {$id}";
        $result = $this->BcdDb->query($select)->row_array();
        return $result[0];
    }

    public function findOrderByOrderId($orderId)
    {
    	$date = date('Y-m-d H:i:s', strtotime(substr($orderId, 0, 14)));
    	$tableSuffix = $this->tools->getTableSuffixByDate($date);
    	if($tableSuffix && $tableSuffix < '2014')
    	{
    		return array();
    	}
    	if($tableSuffix) $tableSuffix = '_' . $tableSuffix;
        $sql = "SELECT * FROM {$this->cp_orders}{$tableSuffix} WHERE orderId = ?";
        $result = $this->BcdDb->query($sql, $orderId)->row_array();
        if($result[0])
        {
            $uname = $this->BcdDb->query("select uname from cp_user where uid = ?", array($result[0]['uid']))->getOne();
        	$result[0]['userName'] = $uname ? $uname : $result[0]['userName'];
        }

        return $result[0];
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：获取审核大奖订单列表
     * 修改日期：2014.11.11
     */
    public function list_check($searchData, $page, $pageCount)
    {
        $where = " WHERE  {$this->cp_orders}.my_status = '2' and {$this->cp_orders}.status = '2000' ";
        $where .= $this->condition("{$this->cp_user}.uname", $searchData['name']);
        $where .= $this->condition("{$this->cp_orders}.orderId", $searchData['orderId']);
        $where .= $this->condition("{$this->cp_orders}.lid", $searchData['lid']);
        $where .= $this->condition("{$this->cp_orders}.playType", $searchData['playType']);
        $where .= $this->condition("{$this->cp_orders}.issue", $searchData['issue']);
        $where .= $this->condition(" {$this->cp_orders}.bonus", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");
        $where .= $this->condition(" {$this->cp_orders}.win_time", array(
            $searchData['start_w_time'],
            $searchData['end_w_time']
        ), "time");
        
        $left = " LEFT JOIN {$this->cp_user} ON {$this->cp_orders}.uid = {$this->cp_user}.uid
         LEFT JOIN {$this->cp_winning} ON {$this->cp_orders}.issue = {$this->cp_winning}.issue AND {$this->cp_orders}.lid = {$this->cp_winning}.lid AND {$this->cp_orders}.playType = {$this->cp_winning}.playType ";
        
        $select = "SELECT {$this->cp_orders}.*,{$this->cp_winning}.time, {$this->cp_user}.uname FROM {$this->cp_orders} force index(win_time)
                        {$left}
                        {$where} 
                        ORDER BY {$this->cp_orders}.created DESC
                        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        
                        $result = $this->BcdDb->query($select)->row_array();
                        $count = $this->BcdDb->query("SELECT count(*)  as count FROM   {$this->cp_orders} {$left} {$where}")->row();
        //订单和中奖总额
        $select1 = "SELECT sum({$this->cp_orders}.money) as mcount,
                            sum( {$this->cp_orders}.margin) as macount,
                            sum({$this->cp_orders}.bonus) as bcount
                            FROM {$this->cp_orders} {$left} {$where}";
        
        $result1 = $this->BcdDb->query($select1)->row();
        foreach ($result as $key => $value)
        {
            $userInfo = $this->get_user_info($value['uid'], array(
                "real_name"
            ));
            $result[$key]['real_name'] = $userInfo['real_name'];
        }
        return array(
            $result,
            $count->count,
            array(
                $result1->mcount,
                $result1->macount,
                $result1->bcount
            )
        );
    }
    
    /**
     * 参    数：$orderId 订单ID
     *                $my_status 派奖状态
     *                $order 新订单ID  
     *                $userKey 用户信息key
     * 作    者：wangl
     * 功    能：派奖审核
     * 修改日期：2014.11.11
     */
    public function check($orderId, $my_status, $caipiao_cfg = '')
    {
        $row = $this->master->query("SELECT * FROM {$this->cp_orders} WHERE orderId = '{$orderId}' and my_status = '2'")->row_array();
        
        if (!empty($row))
        {
            //2015.03.05 注销加载前端配置操作
        	//$redisfile = $this->config->load('../../config/config');
            $rediskeys = $this->config->item("REDIS");
            $this->load->library("tools");
            $userInfo = $this->get_user_info($row[0]['uid']);
            $time = date("Y-m-d H:i:s");
            $this->master->trans_start();
            // 合买订单不派奖
            if ($my_status == 3 && $row[0]['orderType'] != 4)
            {
                //更新用户余额
                $this->load->library('tools');
                $this->master->query("UPDATE {$this->cp_user} SET money = money + {$row[0]['margin']} WHERE uid = {$row[0]['uid']}");
                $momey = $this->master->query("SELECT money FROM {$this->cp_user} WHERE uid = {$row[0]['uid']}")->row();
                $insert = array(
                    "uid" => $row[0]['uid'],
                    "money" => $row[0]['margin'],
                    "mark" => '1',
                    "ctype" => '2',
                    "trade_no" => $this->tools->getIncNum('UNIQUE_KEY'),
                	"orderId" => $orderId,
                    "umoney" => $momey->money,
                    "additions" => $row[0]['lid'],
                    "created" => date("Y-m-d H:i:s")
                );
                
                //插入交易明细
                $this->master->insert($this->cp_w_l, $insert);
                //更新用户redis余额
            }
            if($row[0]['orderType'] == 4)
            {
                $time = '0000-00-00 00:00:00';
            }
            //更新订单状态
            $this->master->query("UPDATE {$this->cp_orders} SET my_status = '{$my_status}',sendprize_time = '{$time}',c_synflag = '0' WHERE orderId = '{$orderId}'");
            $this->master->trans_complete();
            if ($this->master->trans_status() === FALSE)
            {
                return false;
            }
            if ($my_status == 3 && $row[0]['orderType'] != 4)
            {
            	if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $row[0]['uid'], "uname"))
            	{
            		$this->cache->redis->hSet($rediskeys['USER_INFO'] . $row[0]['uid'], "money", $insert['umoney']);
            	}
            	else
            	{
            		$this->load->model('model_user');
            		$this->model_user->freshUserInfo($row[0]['uid']);
            	}
            }
            $this->updateModified($orderId);
            return true;
        }
        return false;
    }
    
    private function updateModified($orderId)
    {
    	$sql = "select lid, issue from {$this->cp_orders} where orderId = '$orderId'";
    	$order = $this->master->query($sql)->getRow();
    	$stime = $this->get_cissue_stime($order['lid'], $order['issue']);
    	$this->master->query("UPDATE {$this->cp_orders} SET modified = now() 
    	WHERE status = 2000 and lid = ? and issue = ? and modified > ?", array($order['lid'], $order['issue'], $stime));
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：异常列表
     * 修改日期：2014.11.11
     */
    public function abnormal_list($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        $where .= $this->condition("{$this->cp_user}.uname", $searchData['name']);
        $where .= $this->condition("{$this->cp_orders}.mark", $searchData['mark']);
        $where .= $this->condition("{$this->cp_orders}.orderId", $searchData['orderId']);
        $where .= $this->condition("{$this->cp_orders}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        if ($this->emp($searchData['lid'])) $where .= " and {$this->cp_orders}.lid = '{$searchData['lid']}'";
        if ($this->emp($searchData['issue'])) $where .= " and ({$this->cp_orders}.issue like '%{$searchData['issue']}%' or {$this->cp_orders}.codecc like '%{$searchData['issue']}%')";
        if ($this->emp($searchData['status']))
        {
            $where .= " and {$this->cp_orders}.status = '{$searchData['status']}'";
        }
        else
        {
            $where .= " and {$this->cp_orders}.status in('600', '510', '21')";
        }
        $select = "SELECT {$this->cp_orders}.id, {$this->cp_orders}.uid, {$this->cp_orders}.orderId, {$this->cp_orders}.lid, {$this->cp_orders}.money, {$this->cp_orders}.multi, {$this->cp_orders}.issue, {$this->cp_orders}.playType, {$this->cp_orders}.isChase, {$this->cp_orders}.orderType, {$this->cp_orders}.betTnum, {$this->cp_orders}.`status`, {$this->cp_orders}.created, {$this->cp_orders}.mark, {$this->cp_user}.uname FROM {$this->cp_orders} 
       					 left join {$this->cp_user} on {$this->cp_orders}.uid = {$this->cp_user}.uid
                         {$where}
                         ORDER BY {$this->cp_orders}.created DESC
                         LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
                         $count = $this->BcdDb->query("SELECT count(*)  as count FROM   {$this->cp_orders} left join {$this->cp_user} on {$this->cp_orders}.uid = {$this->cp_user}.uid  {$where}")->row();
                         $result = $this->BcdDb->query($select)->row_array();
        foreach ($result as $key => $value)
        {
            $userInfo = $this->get_user_info($value['uid'], array(
                "real_name"
            ));
            $result[$key]['real_name'] = $userInfo['real_name'];
        }
        return array(
            $result,
            $count->count
        );
    }
    
    /**
     * 参    数：$id 用户ID
     * 作    者：wangl
     * 功    能：更具用户ID查找订单
     * 修改日期：2014.11.11
     */
    public function find_order_by_uid($id)
    {
        //出票成功数 / l累计投注金额
        $date = date('Y-m-d');
        $select = "select sum(order_num) as ocount, sum(total_betmoney) as mon, sum(total_winmoney) as bonus
		from (
		(select count(o.id) as order_num, SUM(o.money - o.failMoney) as total_betmoney,
		SUM(IF(o.`status` = '2000', o.bonus, 0)) as total_winmoney from cp_orders o
		where (o.created >= '{$date}' OR o.win_time >= '{$date}') AND o.`status` > 40 AND o.`status` != 600 and o.uid = '{$id}' and o.orderType!=4)
		union all
		(SELECT order_num, total_betmoney, total_winmoney from cp_hight_quality_user_index where uid = '{$id}')
		) mm ";
        $result = $this->BcdDb->query($select)->row();
        return array(
            $result->ocount,
            $result->mon,
            $result->bonus
        );
    }
    /**
     * 参    数：$id ID
     * 作    者：wangl
     * 功    能：隐藏异常订单
     * 修改日期：2014.11.05
     */
    public function hide_ab($id)
    {
        $sql = "UPDATE {$this->cp_orders} SET is_hide = (is_hide | 2) WHERE id = ?";
        $this->master->query($sql, array($id));
        return $this->master->affected_rows();
    }
    
	//获得当前的期次的开售时间点
	private function get_cissue_stime($lid, $issue)
    {
    	$lidMap = array(
			'51' => array('table' => 'cp_ssq_paiqi', 'issuePrefix' => ''),
			'52' => array('table' => 'cp_fc3d_paiqi', 'issuePrefix' => ''),
			'33' => array('table' => 'cp_pl3_paiqi', 'issuePrefix' => '20'),
			'35' => array('table' => 'cp_pl5_paiqi', 'issuePrefix' => '20'),
			'10022' => array('table' => 'cp_qxc_paiqi', 'issuePrefix' => '20'),
			'23528' => array('table' => 'cp_qlc_paiqi', 'issuePrefix' => ''),
			'23529' => array('table' => 'cp_dlt_paiqi', 'issuePrefix' => '20'),
			'11' => array('table' => 'cp_tczq_paiqi', 'issuePrefix' => '20'),
			'19' => array('table' => 'cp_tczq_paiqi', 'issuePrefix' => '20'),
			'21406' => array('table' => 'cp_syxw_paiqi', 'issuePrefix' => ''),
			'21407' => array('table' => 'cp_jxsyxw_paiqi', 'issuePrefix' => ''),
			'21408' => array('table' => 'cp_hbsyxw_paiqi', 'issuePrefix' => ''),
			'53' => array('table' => 'cp_ks_paiqi', 'issuePrefix' => ''),
            '56' => array('table' => 'cp_jlks_paiqi', 'issuePrefix' => ''),
    	    '57' => array('table' => 'cp_jxks_paiqi', 'issuePrefix' => ''),
			'54' => array('table' => 'cp_klpk_paiqi', 'issuePrefix' => ''),
            '55' => array('table' => 'cp_cqssc_paiqi', 'issuePrefix' => ''),
    	    '21421' => array('table' => 'cp_gdsyxw_paiqi', 'issuePrefix' => ''),
		);
		$stime = date('Y-m-d H:i:s', time() - 86400 * 2);
		if($lidMap[$lid])
		{
			$issue = preg_replace("/^{$lidMap[$lid]['issuePrefix']}/is", '', $issue);
			$sql = "SELECT show_end_time FROM {$lidMap[$lid]['table']} WHERE 
			issue < '$issue' and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1";
			if(in_array($lid, array(11, 19)))
			{
				$sql = "select show_end_time from {$lidMap[$lid]['table']} where show_end_time > 0 and mid < '$issue' and ctype = 1 
						order by mid desc limit 1";
			}
			$stime = $this->slaveCfg1->query($sql)->getOne();
		}
		return $stime;
    }
    
    /**
     * 查询发送邮件信息
     * @param unknown_type $orderId
     * @param unknown_type $ctype
     */
    public function getOrderEmail($orderId, $ctype)
    {
        return $this->BcdDb->query("select * from cp_order_email_logs where orderId=? and ctype=?", array($orderId, $ctype))->getRow();
    }

    // 乐善奖奖金
    public function getLsDetail($orderId, $lid)
    {
        $tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
        $suffix = '';
        if($tableSuffix)
        {
            $suffix = '_' . $tableSuffix;
        }
        $tables = $this->getSplitTable($lid);
        $sql = "SELECT s.lid, s.sub_order_id, s.codes, s.betTnum, s.multi, s.status, s.bonus, s.otherBonus, s.bonus_detail, s.isChase, s.multi, s.playType, s.ticket_time, d.awardNum, d.bonus_detail, d.margin FROM {$tables['split_table']}{$suffix} AS s LEFT JOIN cp_orders_split_detail AS d ON s.sub_order_id = d.sub_order_id WHERE s.orderId = ?";
        return $this->slaveCfg1->query($sql, array($orderId))->getAll();
    }
    
    public function getOrderTypeByOrderids($orders) {
        return $this->BcdDb->query("select orderType, orderId from cp_orders where orderId in ?", array($orders))->getAll();
    }
}
