<?php

class Bonus_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
        $this->order_status = $this->orderConfig('orders');
        $this->lidmap = $this->orderConfig('lidmap');
    }
    
    public function jclqAwardInfo()
    {
    	$sql = "select mid, full_score, m_status, aduitflag from cp_jclq_paiqi where status = ? 
    	and begin_time < now() and begin_time > date_sub(now(), interval 12 day) limit 500";
    	return $this->cfgDB->query($sql, array($this->order_status['paiqi_complete']))->getAll();
    }
    
	public function jclqOrders($mid, $aduitflag)
    {
    	$return = array(
    			'flag' => 0,
    			'data' => array()
    	);
    	$checknum = $this->cfgDB->query("select count(*) from cp_orders_relation where mid = ? 
    	and status < ? and lid = 43", 
    	array($mid, $this->order_status['draw']))->getOne();
    	if($checknum)
    	{
    		$return['flag'] = 1;
    	}
    	else
    	{
	    	$sql = "select sub_order_id, mid, ptype, pscores, pdetail from cp_orders_relation where mid = ? and 
	    	status = ? and lid = 43 limit 500";
	    	$return['data'] =  $this->cfgDB->query($sql, array($mid, $this->order_status['draw']))->getAll();
	    	//人工审核
	    	if($aduitflag == '1' && empty($return['data']))
	    	{
	    		$count = $this->cfgDB->query("select count(*) from cp_orders_relation where mid = ? and lid = '43' and aduitflag ='0'", array($mid))->getOne();
	    		if($count)
	    		{
	    			$this->cfgDB->query("update cp_orders_relation set aduitflag = 1, status=if(status != '{$this->order_status['concel']}', '{$this->order_status['relation_ggsucc']}', status) where mid = ? and lid= '43'", array($mid));
	    		}
	    	}
    	}
    	return $return;
    }
    
	public function setJclqStatus($mid, $sdata)
    {
    	$sql = "update cp_jclq_paiqi set {$sdata['key']} = ? where mid = ?";
    	$this->cfgDB->query($sql, array($sdata['val'], $mid));
    	return $this->cfgDB->affected_rows();
    }
    
    public function jczqAwardInfo()
    {
    	$sql = "select mid, half_score, full_score, m_status, aduitflag from cp_jczq_paiqi where status = ? and 
    	end_sale_time < now() and end_sale_time > date_sub(now(), interval 12 day) limit 500";
    	return $this->cfgDB->query($sql, array($this->order_status['paiqi_complete']))->getAll();
    	
    }
    
	public function setJczqStatus($mid, $sdata)
    {
    	$sql = "update cp_jczq_paiqi set {$sdata['key']} = ? where mid = ?";
    	$this->cfgDB->query($sql, array($sdata['val'], $mid));
    	return $this->cfgDB->affected_rows();
    }
    
    
    public function jczqOrders($mid, $aduitflag)
    {
    	$return = array(
    		'flag' => 0,
    		'data' => array()
    	);
    	
    	$checknum = $this->cfgDB->query("select count(*) from cp_orders_relation where mid = ? 
    	and status < ? and lid = 42", 
    	array($mid, $this->order_status['draw']))->getOne();
    	if($checknum)
    	{
    		$return['flag'] = 1;
    	}
    	else
    	{
	    	$sql = "select sub_order_id, mid, ptype, pscores, pdetail from cp_orders_relation where mid = ? and 
	    	status = ? and lid = 42 limit 1500";
	    	$return['data'] = $this->cfgDB->query($sql, array($mid, $this->order_status['draw']))->getAll();
    		//人工审核
    		if($aduitflag == '1' && empty($return['data']))
    		{
    			$count = $this->cfgDB->query("select count(*) from cp_orders_relation where mid = ? and lid = '42' and aduitflag ='0'", array($mid))->getOne();
    			if($count)
    			{
    				$this->cfgDB->query("update cp_orders_relation set aduitflag = 1, status=if(status != '{$this->order_status['concel']}', '{$this->order_status['relation_ggsucc']}', status) where mid = ? and lid= '42'", array($mid));
    			}
    		}
    	}
    	
    	return $return;
    }
    
    /**
     * 冠军彩订单查询
     * @param string $mid	期次
     * @param int $lid		彩种id
     */
    public function championOrders($mid, $lid)
    {
    	$return = array(
    		'flag' => 0,
    		'data' => array()
    	);
    	$checknum = $this->cfgDB->query("select count(*) from cp_orders_relation where mid = ? and status < ? and lid = ?",
    			array($mid, $this->order_status['draw'], $lid))->getOne();
    	if($checknum)
    	{
    		$return['flag'] = 1;
    	}
    	else
    	{
    		$sql = "select sub_order_id, mid, ptype, pscores, pdetail from cp_orders_relation where mid=? and
    		status=? and lid=? limit 500";
    		$return['data'] = $this->cfgDB->query($sql, array($mid, $this->order_status['draw'], $lid))->getAll();
    	}
    	
    	return $return;
    }
    
    public function awardInfo($ctype, $lid)
    {
    	$mylidmap = array('52' => 'fc3d', '33' => 'pl3', '35' => 'pl5');
    	$piqitable = empty($mylidmap[$lid]) ? $this->lidmap[$lid] : $mylidmap[$lid]; 
    	$con_map = array('0' => 'status', '1' => 'rstatus');
    	$sql = "select issue, awardNum, bonusDetail, aduitflag from cp_{$piqitable}_paiqi where 
    	award_time < now() and award_time > date_sub(now(), interval 12 day) 
    	and {$con_map[$ctype]} = ? limit 500";
    	return $this->cfgDB->query($sql, array($this->order_status['paiqi_complete']))->getAll();
    }
    
    public function setPaiqiStatus($issue, $lid, $sdata)
    {
    	$mylidmap = array('52' => 'fc3d', '33' => 'pl3', '35' => 'pl5');
    	$piqitable = empty($mylidmap[$lid]) ? $this->lidmap[$lid] : $mylidmap[$lid]; 
    	$sql = "update cp_{$piqitable}_paiqi set {$sdata['key']} = ? where issue = ?";
    	$this->cfgDB->query($sql, array($sdata['val'], $issue));
    	return $this->cfgDB->affected_rows();
    }
    
   	public function bonusOrders($issue, $lid, $status = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$stime = $this->get_cissue_stime($lid, $issue);
    	$return = array(
    			'flag' => 0,
    			'data' => array()
    	);
    	if($status == $this->order_status['split_bigwin'])
    	{
    	    //如果状态值是1020时  检查语句条件判断<1010
    	    $checknum = $this->cfgDB->query("select count(*) from {$tables['split_table']} where issue = ?
    	    and (status < ? && status not in('{$this->order_status['concel']}','{$this->order_status['notwin']}'))
    	    and modified > ? and lid = ?",
    	    array($issue, $this->order_status['split_ggwin'], $stime, $lid))->getOne();
    	}
    	else
    	{
    	    $checknum = $this->cfgDB->query("select count(*) from {$tables['split_table']} where issue = ?
    	    and (status < ? && status not in('{$this->order_status['concel']}','{$this->order_status['notwin']}'))
    	    and modified > ? and lid = ?",
    	    array($issue, $status, $stime, $lid))->getOne();
    	}
    	if($checknum)
    	{
    		$return['flag'] = 1;
    	}
    	else
    	{
    	    if($status == $this->order_status['split_bigwin'])
    	    {
    	        $sql = "select sub_order_id, codes, multi, betTnum, playType, bonus_detail, isChase
    	        from {$tables['split_table']}
    	        where issue = ? and lid = ? and status in ? and modified > ?
    	        limit 500";
    	        $return['data'] = $this->cfgDB->query($sql, array($issue, $lid, array($this->order_status['split_ggwin'], $this->order_status['split_bigwin']), $stime))->getAll();
    	    }
    	    else
    	    {
    	        $sql = "select sub_order_id, codes, multi, betTnum, playType, bonus_detail, isChase
    	        from {$tables['split_table']}
    	        where issue = ? and lid = ? and status = ? and modified > ?
    	        limit 500";
    	        $return['data'] = $this->cfgDB->query($sql, array($issue, $lid, $status, $stime))->getAll();
    	    }
    	}
    	return $return;
    }
    
    public function setBonusDetail($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$fields = array('sub_order_id', 'bonus_detail', 'status');
    	$s_datas = array();
    	$d_datas = array();
    	foreach ($datas as $data)
    	{
    		array_push($s_datas, '(?, ?, ?)');
    		foreach ($fields as $field)
    		{
    			array_push($d_datas, $data[$field]);
    		}
    	}
    	if(!empty($s_datas))
    	{
    		$sql  = "insert {$tables['split_table']}(" . implode(',', $fields) . ") values" . implode(',', $s_datas);
    		$sql .= $this->onduplicate($fields, array('bonus_detail', 'status'));
    		return $this->cfgDB->query($sql, $d_datas);
    	}
    }
    
	public function setJJcResult($datas)
    {
    	$fields = array('sub_order_id', 'mid', 'odds', 'hitnum', 'status', 'aduitflag');
    	$s_datas = array();
    	$d_datas = array();
    	foreach ($datas as $data)
    	{
    		array_push($s_datas, '(?, ?, ?, ?, ?, ?)');
    		foreach ($fields as $field)
    		{
    			array_push($d_datas, $data[$field]);
    		}
    	}
    	
    	if(!empty($s_datas))
    	{
    		$sql  = "insert cp_orders_relation(" . implode(',', $fields) . ") values" . implode(',', $s_datas);
    		$sql .= $this->onduplicate($fields, array('odds', 'status', 'hitnum', 'aduitflag'));
    		return $this->cfgDB->query($sql, $d_datas);
    	}
    }
    
	public function setBonus($datas, $lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$fields = array('sub_order_id', 'bonus', 'margin', 'otherBonus', 'status', 'win_time');
    	$s_datas = array();
    	$d_datas = array();
    	foreach ($datas as $data)
    	{
    		array_push($s_datas, '(?, ?, ?, ?, ?, ?)');
    		foreach ($fields as $field)
    		{
    		    if($field == 'win_time')
    		    {
    		        $data['win_time'] = ($data['status'] == $this->order_status['win']) ? date('Y-m-d H:i:s') : '0';
    		    }
    			if(empty($data[$field])) $data[$field] = 0;
    			array_push($d_datas, $data[$field]);
    		}
    	}
    	if(!empty($s_datas))
    	{
    		$sql  = "insert {$tables['split_table']}(" . implode(',', $fields) . ") values" . implode(',', $s_datas);
    		$sql .= $this->onduplicate($fields, array('bonus', 'margin', 'otherBonus', 'status', 'win_time'));
    		return $this->cfgDB->query($sql, $d_datas);
    	}
    }
    
    public function setBonusSfc($datas)
    {
        $fields = array('sub_order_id', 'bonus_detail', 'bonus', 'margin', 'status', 'win_time');
        $s_datas = array();
        $d_datas = array();
        foreach ($datas as $data)
        {
            array_push($s_datas, '(?, ?, ?, ?, ?, ?, now())');
            foreach ($fields as $field)
            {
            	if($field == 'win_time')
    			{
    				$data['win_time'] = ($data['status'] == $this->order_status['win'])? date('Y-m-d H:i:s') : '0';
    			}
                array_push($d_datas, $data[$field]);
            }
        }
        if(!empty($s_datas))
        {
            $sql  = "insert cp_orders_split(" . implode(',', $fields) . ", win_time) values" . implode(',', $s_datas);
            $sql .= $this->onduplicate($fields, array('bonus_detail', 'bonus', 'margin', 'status', 'win_time'));
            return $this->cfgDB->query($sql, $d_datas);
        }
    }

    //胜负彩
    // status = 50 表示已出开奖号码 rstatus = 50 表示已出开奖详情 
    public function sfcAwardInfo($ctype)
    {
        $con_map = array('0' => 'status', '1' => 'rstatus');
        $sql = "select mid, result, award_detail from cp_rsfc_paiqi 
        where {$con_map[$ctype]} = 50 and created > date_sub(now(), interval 12 day) limit 500";
        return $this->cfgDB->query($sql)->getAll();        
    }
    
	//任九
    public function rjAwardInfo($ctype)
    {
        $con_map = array('0' => 'rjstatus', '1' => 'rjrstatus');
        $sql = "select mid, result, award_detail from cp_rsfc_paiqi 
        where {$con_map[$ctype]} = 50 and created > date_sub(now(), interval 12 day) limit 500";
        return $this->cfgDB->query($sql)->getAll();        
    }

    public function setSfcStatus($mid, $sdata)
    {
        $sql = "update cp_rsfc_paiqi set {$sdata['key']} = ? where mid = ?";
        return $this->cfgDB->query($sql, array($sdata['val'], $mid));
    }

    public function sfcSubAward($subId, $status = 60)
    {
        $sql = "SELECT sum(if(r.result >= 0, 1, 0)) as count from cp_orders_split as s LEFT JOIN cp_orders_relation as r ON s.sub_order_id = r.sub_order_id WHERE s.sub_order_id = ? AND r.status = ?;";
        return $this->cfgDB->query($sql, array($subId, $status))->getAll();
    }
    
    public function calBonusOrders($lid)
    {
    	$table_map = array(
    		'42' => array('jczq'),
    		'43' => array('jclq')
    	);
    	$ttail = $table_map[$lid][0];
    	$table_name = "{$this->db_config['cfgtmp']}.cp_bonus_$ttail";
    	$check_sql_con = "SELECT r.sub_order_id FROM cp_orders_relation r inner join cp_orders_split s on r.sub_order_id=s.sub_order_id AND s.playType < 16
			WHERE 1 and r.modified > date_sub(now(), interval 30 minute) and r.lid = '$lid'	and 
			r.status in('{$this->order_status['relation_ggsucc']}') group by r.sub_order_id";
    	$check_sql = "SELECT m.sub_order_id, sum(1) total, 
    	sum(if(status in('{$this->order_status['relation_ggsucc']}', '{$this->order_status['relation_jjsucc']}'), 1, 0)) ggnum 
    	FROM cp_orders_relation m 
    	join ($check_sql_con) n on m.sub_order_id = n.sub_order_id
    	group by m.sub_order_id having total = ggnum";
    	$sub_order_id = $this->cfgDB->query($check_sql)->getAll();
    	if(!empty($sub_order_id))
    	{
			$this->cfgDB->query("truncate $table_name");
			$this->insert_select($table_name, $check_sql, array('sub_order_id'));
			$sql2 = "insert $table_name(sub_order_id, match_num, gg_num, odds, hitnum, aduit_num)
				SELECT n.sub_order_id, 1 as match_num, 
				if(n.status in('{$this->order_status['relation_ggsucc']}', '{$this->order_status['relation_jjsucc']}'),	1, 0) as gg_num, 
				if(n.status in('{$this->order_status['relation_ggsucc']}', '{$this->order_status['relation_jjsucc']}'), n.odds, 0) as odds, 
				if(n.status in('{$this->order_status['relation_ggsucc']}', '{$this->order_status['relation_jjsucc']}'), n.hitnum, 0) as hitnum,
				n.aduitflag as aduit_num
				FROM $table_name m JOIN cp_orders_relation n ON m.`sub_order_id` = n.`sub_order_id`
				WHERE 1	on duplicate key update match_num = $table_name.match_num + values(match_num),
				gg_num = $table_name.gg_num + values(gg_num), odds = $table_name.odds * values(odds),
				hitnum = $table_name.hitnum * values(hitnum), aduit_num = $table_name.aduit_num + values(aduit_num)";
			$this->cfgDB->query($sql2);
			
	    	$sql3 = "UPDATE $table_name SET bonus = CASE match_num 
	    	WHEN 1 THEN IF ((200 * odds) > 10000000, 10000000, (200 * odds)) 
	    	WHEN 2 THEN IF ((200 * odds) > 20000000, 20000000, (200 * odds)) 
	    	WHEN 3 THEN IF ((200 * odds) > 20000000, 20000000, (200 * odds)) 
	    	WHEN 4 THEN IF ((200 * odds) > 50000000, 50000000, (200 * odds)) 
	    	WHEN 5 THEN IF ((200 * odds) > 50000000, 50000000, (200 * odds)) 
	    	ELSE IF ((200 * odds) > 100000000, 100000000, (200 * odds)) 
	    	END WHERE gg_num = match_num";
	    	$this->cfgDB->query($sql3);
	    	//0.00001 是为了矫正误差
	    	$sql4 = "update $table_name set bonus = floor(bonus * 10 + 0.00001) / 10  where gg_num = match_num";
	    	$this->cfgDB->query($sql4);
	    	
	    	$sql5 = "update $table_name set bonus = if((bonus - 0.5) > floor(bonus), ceil(bonus), 
	    	if((bonus - 0.5) = floor(bonus), if(mod(floor(bonus), 2) = 0, floor(bonus), ceil(bonus)), 
	    	floor(bonus))) where gg_num = match_num";
	    	$this->cfgDB->query($sql5);
	    	
	    	$sql6 = "update $table_name set margin = if(bonus >= 1000000, bonus * 0.8, bonus)
	    			where gg_num = match_num";
	    	$this->cfgDB->query($sql6);
	    	
	    	$sql7 = "update $table_name m join cp_orders_split n on m.sub_order_id = n.sub_order_id 
	    	set n.bonus = m.hitnum * m.bonus * multi, n.margin = m.hitnum * m.margin * multi, n.status = 
	    	if(m.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
	    	n.win_time = if(m.bonus > 0, now(), 0), n.bonus_detail = m.hitnum,
	    	n.aduitflag = if(m.match_num = m.aduit_num, 1, 0),
	    	n.cpstate = if(m.match_num = m.aduit_num, 0, n.cpstate)
	    	where m.gg_num = m.match_num";
	    	$this->cfgDB->query($sql7);
	    	
	    	$sql8 = "update (SELECT m.sub_order_id, n.aduit_num, sum(m.aduitflag) aduitflag FROM cp_orders_relation m 
            INNER JOIN $table_name n ON m.sub_order_id=n.sub_order_id AND n.gg_num=n.match_num GROUP BY m.sub_order_id
            HAVING aduitflag = aduit_num) mm
            join cp_orders_relation nn on mm.sub_order_id = nn.sub_order_id
            set nn.status = ?";
	    	$this->cfgDB->query($sql8, array($this->order_status['relation_jjsucc']));
    	}
    }
    
    /**
     * 竞彩容错过关订单算奖
     * @param unknown_type $lid
     */
    public function calRcBounsOrders($lid)
    {
    	$table_map = array(
    		'42' => array('jczq'),
    		'43' => array('jclq')
    	);
    	$ttail = $table_map[$lid][0];
    	$table_name = "{$this->db_config['cfgtmp']}.cp_bonus_relation_$ttail";
    	$table_aduitflag_name = "{$this->db_config['cfgtmp']}.cp_bonus_aduitflag_$ttail";
    	$check_sql_con = "SELECT r.sub_order_id, s.playType, substring_index(s.codes,'JE=', -1) codes FROM cp_orders_relation r inner join cp_orders_split s on r.sub_order_id=s.sub_order_id AND s.playType > 15
    	WHERE 1 and r.modified > date_sub(now(), interval 30 minute) and r.lid = '$lid'	and
    	r.status in('{$this->order_status['relation_ggsucc']}') group by r.sub_order_id";
    	$check_sql = "SELECT m.sub_order_id, sum(1) total, n.playType, n.codes,
    	sum(if(status in('{$this->order_status['relation_ggsucc']}', '{$this->order_status['relation_jjsucc']}'), 1, 0)) ggnum
    	FROM cp_orders_relation m
    	join ($check_sql_con) n on m.sub_order_id = n.sub_order_id
    	group by m.sub_order_id having total = ggnum limit 200";
    	$datas = $this->cfgDB->query($check_sql)->getAll();
    	while(!empty($datas))
    	{
    		$this->load->library('libcomm');
    		$subOrders = array();
    		$playTypes = array();
    		foreach ($datas as $value)
    		{
                preg_match('/\d+,GG=(\d+)/is', $value['codes'], $matches);
                $playTypes[$value['sub_order_id']] = array('playType' => $value['playType'], 'ggtype' => $matches[1]);
    			$subOrders[] = $value['sub_order_id'];
    		}
    		$relOrders = $this->cfgDB->query("SELECT sub_order_id, mid, odds, hitnum, aduitflag FROM cp_orders_relation WHERE sub_order_id in ?", array($subOrders))->getAll();
    		$orders = array();
    		$smids = array();
    		$aduits = array();
    		foreach ($relOrders as $subOrder)
    		{
    			$orders[$subOrder['sub_order_id']][$subOrder['mid']] = $subOrder;
    			$smids[$subOrder['sub_order_id']][] = $subOrder['mid'];
    			$aduits[$subOrder['sub_order_id']] = isset($aduits[$subOrder['sub_order_id']]) ? ($aduits[$subOrder['sub_order_id']] + $subOrder['aduitflag']) : $subOrder['aduitflag'];
    		}
    		
    		$bdata['s_data'] = array();
			$bdata['d_data'] = array();
			$bdata['s1_data'] = array();
			$bdata['d1_data'] = array();
    		foreach ($smids as $sub_order_id => $mids)
    		{
    		    array_push($bdata['s1_data'], "(?, ?)");
    		    array_push($bdata['d1_data'], $sub_order_id);
    		    array_push($bdata['d1_data'], $aduits[$sub_order_id]);
    			$zuPlay = $this->getPlayTypeZuhe($playTypes[$sub_order_id]);
    			$ssubId = 1;
    			foreach ($zuPlay as $value)
    			{
    				$result = $this->libcomm->combineList($mids, $value);
    				foreach ($result as $val)
    				{
    					foreach ($val as $vall)
    					{
    						array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?)");
    						array_push($bdata['d_data'], $sub_order_id);
    						array_push($bdata['d_data'], $ssubId);
    						array_push($bdata['d_data'], $vall);
    						array_push($bdata['d_data'], $orders[$sub_order_id][$vall]['odds']);
    						array_push($bdata['d_data'], $orders[$sub_order_id][$vall]['hitnum']);
    						array_push($bdata['d_data'], $orders[$sub_order_id][$vall]['aduitflag']);
    					}
    					$ssubId++;
    				}
    			}
    		}
    		if(!empty($bdata['s_data']))
			{
				$this->cfgDB->query("truncate $table_name");
				$this->cfgDB->query("truncate $table_aduitflag_name");
				$fields = array('sub_order_id', 'ssub_order_id', 'mid', 'odds', 'hitnum', 'aduitflag');
				$sql = "insert $table_name(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']);
				$res = $this->cfgDB->query($sql, $bdata['d_data']);
				$fields1 = array('sub_order_id', 'aduit_num');
				$sql1 = "insert $table_aduitflag_name(" . implode(', ', $fields1) . ") values" . implode(', ', $bdata['s1_data']);
				$res1 = $this->cfgDB->query($sql1, $bdata['d1_data']);
				if((!$res) && (!$res1))
				{
					die();
				}
			}
			
			$this->trans_start();
			$bonus_table_name = "{$this->db_config['cfgtmp']}.cp_bonus_rongcuo_$ttail";
			$res1 = $this->cfgDB->query("truncate $bonus_table_name");
			$sql2 = "insert $bonus_table_name(sub_order_id, ssub_order_id, odds, match_num, hitnum, aduit_num)
			SELECT sub_order_id, ssub_order_id, odds, 1 as match_num, hitnum, aduitflag as aduit_num
			FROM $table_name 
			WHERE 1	on duplicate key update match_num = $bonus_table_name.match_num + values(match_num),
			odds = $bonus_table_name.odds * values(odds),
			hitnum = $bonus_table_name.hitnum * values(hitnum), aduit_num = $bonus_table_name.aduit_num + values(aduit_num)";
			$res2 = $this->cfgDB->query($sql2);
			$sql3 = "UPDATE $bonus_table_name SET bonus = CASE match_num
			WHEN 1 THEN IF ((200 * odds) > 10000000, 10000000, (200 * odds))
			WHEN 2 THEN IF ((200 * odds) > 20000000, 20000000, (200 * odds))
			WHEN 3 THEN IF ((200 * odds) > 20000000, 20000000, (200 * odds))
			WHEN 4 THEN IF ((200 * odds) > 50000000, 50000000, (200 * odds))
			WHEN 5 THEN IF ((200 * odds) > 50000000, 50000000, (200 * odds))
			ELSE IF ((200 * odds) > 100000000, 100000000, (200 * odds))
			END WHERE 1";
			$res3 = $this->cfgDB->query($sql3);
			//0.00001 是为了矫正误差
			$sql4 = "update $bonus_table_name set bonus = floor(bonus * 10 + 0.00001) / 10  where 1";
			$res4 = $this->cfgDB->query($sql4);
			$sql5 = "update $bonus_table_name set bonus = if((bonus - 0.5) > floor(bonus), ceil(bonus),
			if((bonus - 0.5) = floor(bonus), if(mod(floor(bonus), 2) = 0, floor(bonus), ceil(bonus)),
			floor(bonus))) where 1";
			$res5 = $this->cfgDB->query($sql5);
			
			$sql6 = "update $bonus_table_name set margin = if(bonus >= 1000000, bonus * 0.8 * hitnum, bonus * hitnum),bonus = (bonus * hitnum)
			where 1";
			$res6 = $this->cfgDB->query($sql6);
			
			$sql7 = "UPDATE cp_orders_split m INNER JOIN 
			(SELECT sub_order_id, SUM(bonus) bonus, SUM(margin) margin, SUM(match_num) match_num,SUM(aduit_num) aduit_num FROM $bonus_table_name WHERE 1 GROUP BY sub_order_id) n 
			ON n.sub_order_id = m.sub_order_id
			SET m.bonus = n.bonus * m.multi, m.margin = n.margin * m.multi,
			m.status =
			if(n.bonus > 0, '{$this->order_status['win']}', '{$this->order_status['notwin']}'),
			m.win_time = if(n.bonus > 0, now(), 0), m.bonus_detail = if(n.bonus > 0, 1, 0),
			m.aduitflag = if(n.match_num = n.aduit_num, 1, 0),
			m.cpstate = if(n.match_num = n.aduit_num, 0, m.cpstate)";
			$res7 = $this->cfgDB->query($sql7);
			
			$sql8 = "update (SELECT m.sub_order_id, n.aduit_num, sum(m.aduitflag) aduitflag FROM cp_orders_relation m
			INNER JOIN $table_aduitflag_name n ON m.sub_order_id=n.sub_order_id GROUP BY m.sub_order_id
			HAVING aduitflag = aduit_num) mm
			join cp_orders_relation nn on mm.sub_order_id = nn.sub_order_id
			set nn.status = ?";
			$res8 = $this->cfgDB->query($sql8, array($this->order_status['relation_jjsucc']));
			if(!($res1 && $res2 && $res3 && $res4 && $res5 && $res6 && $res7 && $res8))
			{
				$this->trans_rollback();
				die();
			}
			$this->trans_complete();
    		$datas = $this->cfgDB->query($check_sql)->getAll();
    	}
    }
    
    /**
     * 玩法组合定义
     * @param unknown_type $playType
     * @return Ambigous <multitype:, multitype:string >
     */
    private function getPlayTypeZuhe($playType)
    {
    	$data = array();
    	switch ($playType['playType'])
    	{
    		case '17':
    		case '22':
    		case '27':
    		case '34':
    			$data = array('2');
    			break;
    		case '18':
    		case '29':
    		case '37':
    			$data = array('2', '3');
    			break;
    		case '20':
    		case '35':
    			$data = array('3');
    			break;
    		case '21':
    			$data = array('3', '4');
    			break;
    		case '23':
    		case '39':
    			$data = array('2', '3', '4');
    			break;
    		case '25':
    		case '45':
    		case '51':
    			$data = array('4');
    			break;
    		case '26':
    			$data = array('4', '5');
    			break;
    		case '28':
    			$data = array('3', '4', '5');
    			break;
    		case '30':
    			$data = array('2', '3', '4', '5');
    			break;
    		case '32':
    		case '44':
    		case '50':
    			$data = array('5');
    			break;
    		case '33':
    			$data = array('5', '6');
    			break;
    		case '42':
    		case '49':
    			$data = array('6');
    			break;
    		case '43':
    			$data = array('6', '7');
    			break;
    		case '36':
    			$data = array('4', '5', '6');
    			break;
    		case '38':
    			$data = array('3', '4', '5', '6');
    			break;
    		case '40':
    			$data = array('2', '3', '4', '5', '6');
    			break;
    		case '46':
    			$data = array('2', '3', '4', '5', '6', '7');
    			break;
    		case '47':
    			$data = array('7');
    			break;
    		case '48':
    			$data = array('7', '8');
    			break;
    		case '52':
    			$data = array('2', '3', '4', '5', '6', '7', '8');
    			break;
            case '53':
                $ggmaps = array('2', '3', '4', '5', '6', '7', '8');
                foreach ($ggmaps as $ggmap){
                    if($playType['ggtype'] & (1 << ($ggmap - 2))){
                        array_push($data, $ggmap);
                    }
                }
                break;
    		default:
    			$data;
    			break;
    	}
    	
    	return $data;
    }
    
    /**
     * 更新大订单状态
     */
    public function calOrderStatus($lid = 0)
    {
    	$tables = $this->getSplitTable($lid);
    	$mSql = "{$tables['split_table']} a force index(modified)
    	INNER JOIN cp_orders_ori b ON a.orderId = b.orderId 
    	where a.modified > date_sub(now(), interval 20 minute) 
    	and a.status in('{$this->order_status['draw']}', '{$this->order_status['concel']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}') and b.status in ('{$this->order_status['drawing']}') 
    	group by a.orderId";
    	$orderId = $this->cfgDB->query("select a.orderId from {$mSql} limit 1")->getCol();
    	if($orderId)
    	{
    		$this->trans_start();
    		$tableName = "{$this->db_config['cfgtmp']}.cp_order_status_cmp";
    		if(!empty($lid)) $tableName .= "_$lid";
    		$this->cfgDB->query("truncate $tableName");
    		$this->insert_select($tableName, "select a.orderId from {$mSql}", array('orderId'));
    		
    		$sql1 = "select a.orderId as orderId,
    		count(1) as total,
    		sum(if(a.status in ('{$this->order_status['draw']}', '{$this->order_status['notwin']}', '{$this->order_status['win']}'), 1, 0)) as succNum,
    		sum(if(a.status = '{$this->order_status['concel']}', 1, 0)) as failNum,
    		sum(if(a.status = '{$this->order_status['concel']}', a.money, 0)) as failMoney 
    		from {$tables['split_table']} a inner join $tableName b on a.orderId=b.orderId where 1 group by a.orderId";
    		$this->insert_select($tableName, $sql1, array('orderId', 'total', 'succNum', 'failNum', 'failMoney'));
    		
    		$sql2 = "update $tableName SET status = if(succNum = total, {$this->order_status['draw']}, 
    		if(failNum = total, {$this->order_status['concel']}, if(succNum + failNum = total, 
    		{$this->order_status['draw_part']}, status)))";
    		$re2 = $this->cfgDB->query($sql2);
    		
    		$sql3 = "update cp_orders_ori a inner join $tableName b on a.orderId = b.orderId
    		set a.status = b.status, a.failMoney=b.failMoney, a.synflag=(a.synflag << 1) where b.status > 0 and a.status = '{$this->order_status['drawing']}'";
    		$re3 = $this->cfgDB->query($sql3);
    		if($re2 && $re3)
    		{
    			$this->trans_complete();
    		}
    		else
    		{
    			$this->trans_rollback();
    		}
    		//添加出票失败报警机制 
    		$sql = "select a.sub_order_id, a.lid from {$tables['split_table']} a inner join $tableName b on a.orderId=b.orderId where b.failNum > 0 and a.status = '{$this->order_status['concel']}'";
    		$alertRow = $this->cfgDB->query($sql)->getAll();
    		if(!empty($alertRow))
    		{
                $sql = "insert ignore cp_alert_log (ufiled,ctype,title,content,status,created)
                VALUES ";
                $s_data = array();
    			$d_data = array();
                foreach ($alertRow as $sub)
                {
                 array_push($s_data, "(?,'6','出票失败报警',?, '0', NOW())");
                 array_push($d_data, $sub['sub_order_id']);
                 array_push($d_data, $this->getLidName($sub['lid']).','.$sub['sub_order_id'].',子订单出票失败;【166彩票】');
                }
    			if(!empty($d_data))
    			{
    				$this->db->query($sql . implode(', ', $s_data), $d_data);
    			}
    		}
    	}
    }
    /**
     * [getLidName description]
     * @author LiKangJian 2017-06-09
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    private function getLidName($lid)
    {
        //彩票种类及玩法
        $caipiao_cfg = array(
            "23529" => "大乐透",
            "23528" => "七乐彩",
            "10022" => "七星彩",
            "21406"  => "老11选5",
            "21407" => "新11选5",
            "21408" =>"惊喜11选5",
            "51" => "双色球",
            "33" => "排列三",
            "35" => "排列五",
            "41" => "北京单场",
            "42" => "竞彩足球",
            "43" => "竞彩篮球",
            "52" => "福彩3D",
            "11" => "胜负彩",
            "19" => "任选九",
            "44" => "冠军彩",
            "45" => "冠亚军彩",
            "53" =>"上海快三",
            "54" => "快乐扑克",
            "55" => "老时时彩",
            "56" => "吉林快三",
            "57" => "江西快三",
            "21421" =>"乐11选5",
            );
        return $caipiao_cfg[$lid];
    }
    /*
     * 功能：将insert select分离处理
     * 作者：huxm
     * */
    private function insert_select($tname, $sql, $fields)
    {
    	$return = true;
    	$datas = $this->cfgDB->query($sql)->getAll();
    	if(!empty($datas))
    	{
    		$s_data = array();
    		$sql1 = "insert $tname(" . implode(',', $fields) . ") values";
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
    		$sql1 .= implode(',', $s_data) . $this->onduplicate($fields, $fields);
    		$return = $this->cfgDB->query($sql1);
    	}
    	return $return;
    }
    
    /**
     * 冠军彩算奖操作
     */
    public function calBonusChampionOrders()
    {
    	$sql = "select sub_order_id, mid, odds from cp_orders_relation where lid in ('44', '45') 
    	and modified > date_sub(now(), interval 1 day) and status in('{$this->order_status['relation_ggsucc']}') limit 200";
    	$result = $this->cfgDB->query($sql)->getAll();
    	while(!empty($result))
    	{
    		$this->cfgDB->trans_start();
    		foreach ($result as $order)
    		{
    			if($order['odds'] > 0)
    			{
    				$splitSql = "update cp_orders_split set bonus = {$order['odds']} * multi * 200, 
    				margin = {$order['odds']} * multi * 200, status = '{$this->order_status['win']}',
    				win_time = now(), bonus_detail = 1 where sub_order_id = '{$order['sub_order_id']}'";
    			}
    			else
    			{
    				$splitSql = "update cp_orders_split set status = '{$this->order_status['notwin']}',
    				bonus_detail = 0 where sub_order_id = '{$order['sub_order_id']}'";
    			}
    			$res1 = $this->cfgDB->query($splitSql);
    			$rSql = "update cp_orders_relation set status = '{$this->order_status['relation_jjsucc']}' 
    			where sub_order_id = '{$order['sub_order_id']}' and mid = '{$order['mid']}'";
    			$res2 = $this->cfgDB->query($rSql);
    			if(!$res1 || !$res2)
    			{
    				$this->cfgDB->trans_rollback();
    			}
    		}
    		$this->cfgDB->trans_complete();
    		$result = $this->cfgDB->query($sql)->getAll();
    	}
    }
    
    // 乐善奖
    public function lsBonusOrders($order)
    {
        $sql = "SELECT sub_order_id, lid, ticket_seller, awardNum, bonus_detail, margin FROM cp_orders_split_detail WHERE sub_order_id = ?";
        return $this->cfgDB->query($sql, array($order['sub_order_id']))->getRow();
    }

    // 更新乐善奖
    public function recordLsDetail($order)
    {
        $sql = "UPDATE cp_orders_split_detail SET bonus_detail = ?, margin = ? WHERE sub_order_id = ?";
        $this->cfgDB->query($sql, array($order['bonus_detail'], $order['margin'], $order['sub_order_id']));
    }
}
 
