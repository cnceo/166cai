<?php

class Match_Model extends MY_Model 
{

	private $compare_succ;
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('compare_model');
        $this->compare_succ = $this->compare_model->status_map['compare_succ'];
    }

    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将竞彩足球数据更新到数据库
     * 修改日期：2015-03-12
     */
    public function saveJczq($data)
    {
    	return $this->saveData('cp_jczq_match', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将体彩足球数据更新到数据库
     * 修改日期：2015-03-13
     */
    public function saveTczq($data)
    {
    	return $this->saveData('cp_tczq_match', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将竞彩篮球数据更新到数据库
     * 修改日期：2015-03-13
     */
    public function saveJclq($data)
    {
    	return $this->saveData('cp_jclq_match', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将北京单场数据更新到数据库
     * 修改日期：2015-03-16
     */
    public function saveBjdc($data)
    {
    	return $this->saveData('cp_bjdc_match', $data);
    }
    
    /**
     * 参    数：$tableName,字符型,表名称
     * 		 $data,二维数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将体彩足球数据更新到数据库
     * 修改日期：2015-03-13
     */
    protected function saveData($tableName, $data)
    {
    	$insertVal = "";
    	$vData = array();
    	foreach($data as $value)
    	{
    		$flag = $insertVal == null ? "" : ",";
    		$fields = array_keys($value);
    		$v = array_values($value);
    		$vData[] = implode(',', $v);
    		$insertVal .= $flag."(".implode(',', array_map(array($this, 'maps'), $fields)).", now())";
    	}
    	 
    	$sql = "INSERT INTO `{$tableName}` (".implode(',', $fields).",created)
    	VALUES ".$insertVal;
    	$tail = array();
    	foreach ($fields as $field)
    	{
    		array_push($tail, "$field = values($field)");
    	}
    	if(!empty($tail))
    		$sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $tail);
    	 
    	$vData = implode(',', $vData);
    	return $this->dc->query($sql, explode(',', $vData));
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：查询北京单场match数据
     * 修改日期：2015-03-27
     */
    public function getBjdcMatchs()
    {
    	$sql = "SELECT * FROM cp_bjdc_match WHERE state = 0 AND modified > DATE_SUB(NOW(), INTERVAL 3 DAY)";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：查询竞彩足球match数据
     * 修改日期：2015-03-27
     */
    public function getJczqMatchs()
    {
    	$sql = "SELECT * FROM cp_jczq_match WHERE state = 0 AND modified > DATE_SUB(NOW(), INTERVAL 3 DAY)";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：查询竞彩篮球match数据
     * 修改日期：2015-03-27
     */
    public function getJclqMatchs()
    {
    	$sql = "SELECT * FROM cp_jclq_match WHERE state = 0 AND modified > DATE_SUB(NOW(), INTERVAL 3 DAY)";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$fields,数组,表字段名
     * 		 $bdata,二维数组,值
     * 作    者：shigx
     * 功    能：将北京单场数据更新到排期表
     * 修改日期：2015-03-27
     */
    public function saveBjdcPaiqi($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
    		$upd = array('mid', 'rq', 'league', 'home', 'away');
    		$sql = "insert cp_bjdc_paiqi(" . implode(', ', $fields) . ") values" .
    				implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
    		$this->dc->query($sql, $bdata['d_data']);
    		$affectedRows = $this->dc->affected_rows();
    		if($affectedRows > 0)
    		{
    			$this->updateTicketStop(6, 41, 0);
    		}
    	}
    }
    
    /**
     * 参    数：$fields,数组,表字段名
     * 		 $bdata,二维数组,值
     * 作    者：shigx
     * 功    能：将北京单场胜负过关数据更新到排期表
     * 修改日期：2015-03-27
     */
    public function saveSfggPaiqi($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
    		$upd = array('rq', 'league', 'home', 'away');
    		$sql = "insert cp_sfgg_paiqi(" . implode(', ', $fields) . ") values" .
    				implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd);
    		$this->dc->query($sql, $bdata['d_data']);
    		$affectedRows = $this->dc->affected_rows();
    		if($affectedRows > 0)
    		{
    			$this->updateTicketStop(6, 40, 0);
    		}
    	}
    }
    
    /**
     * 参    数：$fields,数组,表字段名
     * 		 $bdata,二维数组,值
     * 作    者：shigx
     * 功    能：将竞彩足球数据更新到排期表
     * 修改日期：2015-03-27
     */
    public function saveJczqPaiqi($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
    		$upd = array('rq', 'league', 'home', 'away');
    		$sql = "insert cp_jczq_paiqi(" . implode(', ', $fields) . ") values" .
    				implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd)
    				. ', synflag = if(end_sale_time <> values(end_sale_time), 0, synflag)'
    				. ', end_sale_time = if(sale_time_set=1, end_sale_time, values(end_sale_time))';
    		$this->dc->query($sql, $bdata['d_data']);
    		$affectedRows = $this->dc->affected_rows();
    		if($affectedRows > 0)
    		{
    			$this->updateTicketStop(6, 42, 0);
    		}
    	}
    }
    
    /**
     * 参    数：$fields,数组,表字段名
     * 		 $bdata,二维数组,值
     * 作    者：shigx
     * 功    能：将竞彩篮球数据更新到排期表
     * 修改日期：2015-03-27
     */
    public function saveJclqPaiqi($fields, $bdata)
    {
    	if(!empty($bdata['s_data']))
    	{
    		$upd = array('rq', 'league', 'home', 'away', 'preScore');
    		$sql = "insert cp_jclq_paiqi(" . implode(', ', $fields) . ") values" .
    				implode(', ', $bdata['s_data']) . $this->onduplicate($fields, $upd)
    				. ', synflag = if(begin_time <> values(begin_time), 0, synflag)'
    				. ', begin_time = if(sale_time_set=1, begin_time, values(begin_time))';
    		$this->dc->query($sql, $bdata['d_data']);
    		$affectedRows = $this->dc->affected_rows();
    		if($affectedRows > 0)
    		{
    			$this->updateTicketStop(6, 43, 0);
    		}
    	}
    }
    
    /**
     * 参    数：$fields,数组,表字段名
     * 		 $bdata,二维数组,值
     * 作    者：shigx
     * 功    能：将北京单场数据更新到排期表
     * 修改日期：2015-03-27
     */
    public function saveTczqPaiqi()
    {
    	$sql = "INSERT INTO cp_tczq_paiqi
    			(mid,mname,ctype,league,home,away,start_sale_time,end_sale_time,status,created, synflag)
    			SELECT mid,mname,ctype,league,home,away,start_sale_time,end_sale_time,status,NOW(), 0
    			FROM cp_tczq_match WHERE state = 0 AND modified > DATE_SUB(NOW(), INTERVAL 3 DAY) 
    	ON DUPLICATE KEY UPDATE 
    	synflag = if((cp_tczq_paiqi.end_sale_time <> VALUES(end_sale_time)) 
    	or (cp_tczq_paiqi.start_sale_time <> VALUES(start_sale_time))
    	or (cp_tczq_paiqi.league <> VALUES(league))
    	or (cp_tczq_paiqi.home <> VALUES(home))
    	or (cp_tczq_paiqi.away <> VALUES(away))
    	, values(synflag), synflag),
    	mid = VALUES(mid),league=VALUES(league),home=VALUES(home),away=VALUES(away),
    	start_sale_time=VALUES(start_sale_time),end_sale_time=VALUES(end_sale_time)";
    	$this->dc->query($sql);
    	$affectedRows = $this->dc->affected_rows();
    	if($affectedRows > 0)
    	{
    		$this->updateTicketStop(6, 10, 0);
    	}
    	$sql1 = "SELECT id FROM cp_tczq_match WHERE state = 0 AND modified > DATE_SUB(NOW(), INTERVAL 3 DAY) and (home !='0')";
    	$ids = $this->dc->query($sql1)->getCol();
    	if($ids)
    	{
    		$ids = implode(',', $ids);
    		$this->dc->query("UPDATE cp_tczq_match SET state=1 WHERE id IN({$ids}) AND begin_date < NOW() and begin_date <> '0000-00-00 00:00'");
    	}
    }
    
    /**
     * 获取近几期的mid
     * @param 期数 int $count
     */
    public function getNewMid($count)
    {
    	$sql = "select distinct mid from cp_tczq_paiqi where ctype='1' order by mid desc limit 3";
    	return $this->dc->query($sql)->getCol();
    }
    
    /**
     * 根据mid、ctype获取近几期的mid,mname,begin_date
     * @param array $mids
     * @param int $ctype
     */
    public function getBegindateByMid($mids, $ctype)
    {
    	$sql = "select mid, mname, begin_date from cp_tczq_paiqi where mid in (".implode(',', $mids).") and ctype='{$ctype}'";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 更新begin_date
     * @param string $begindate
     * @param string $mname
     * @param string $mid
     */
    public function updateBegindate($begindate, $mname, $mid)
    {
    	$sql = "update cp_tczq_paiqi set begin_date='{$begindate}', synflag = 0 where mname='{$mname}' and mid='{$mid}'";
    	$this->dc->query($sql);
    	$affectedRows = $this->dc->affected_rows();
    	if($affectedRows > 0)
    	{
    		$this->updateTicketStop(6, 10, 0);
    	}
    }
    
    /**
     * 参    数：$fields,字符串,id字符串
     * 作    者：shigx
     * 功    能：更新北京单场match state字段
     * 修改日期：2015-03-27
     */
    public function updateBjdcMatch($ids)
    {
    	$sql = "UPDATE cp_bjdc_match SET state=1 WHERE id IN ({$ids}) AND begin_time < NOW()";
    	return $this->dc->query($sql);
    }
    
    /**
     * 参    数：$fields,字符串,id字符串
     * 作    者：shigx
     * 功    能：更新竞彩足球match state字段
     * 修改日期：2015-03-27
     */
    public function updatejczqMatch($ids)
    {
    	$sql = "UPDATE cp_jczq_match SET state=1 WHERE id IN ({$ids}) AND concat(end_sale_date, ' ', end_sale_time) < NOW()";
    	return $this->dc->query($sql);
    }
    
    /**
     * 参    数：$fields,字符串,id字符串
     * 作    者：shigx
     * 功    能：更新竞彩篮球match state字段
     * 修改日期：2015-03-27
     */
    public function updatejclqMatch($ids)
    {
    	$sql = "UPDATE cp_jclq_match SET state=1 WHERE id IN ({$ids}) AND begin_time < NOW()";
    	return $this->dc->query($sql);
    }
    
    /**
     * 参    数：$ctype,整形,类型id
     * 作    者：shigx
     * 功    能：查询北京单场信息
     * 修改日期：2015-03-27
     */
    public function keysOfBjdc()
    {
    	$sql = "SELECT mid, m_date, mname, if(date_add(`begin_time`, interval 120 minute) < now(), 1, 0) as status
    	FROM `cp_bjdc_paiqi`
    	WHERE 1 and status <= {$this->compare_succ} and `begin_time` < now()
    	and begin_time  > date_sub(now(), interval 7 day) and state='0'
    	order by mid desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
    		foreach ($datas as $data)
    		{
    			if($data['status'] == 1)
    			{
    				$rekeys[$data['mid']]['date'][] = $data['m_date'];
    				$rekeys[$data['mid']]['mname'][] = $data['mname'];
    			}
    		}
    	}
    	return $rekeys;
    }
    
    /**
     * 参    数：$ctype,整形,类型id
     * 作    者：shigx
     * 功    能：查询北京单场信息
     * 修改日期：2015-03-27
     */
    public function keysOfSfgg()
    {
    	$sql = "SELECT mid, m_date, mname, if(date_add(`begin_time`, interval 120 minute) < now(), 1, 0) as status
    	FROM `cp_sfgg_paiqi`
    	WHERE 1 and status <= {$this->compare_succ} and `begin_time` < now()
    	and begin_time  > date_sub(now(), interval 7 day) and state='0'
    	order by mid desc";
    	$datas = $this->dc->query($sql)->getAll();
    	$rekeys = array();
    	if(!empty($datas))
    	{
    		foreach ($datas as $data)
    		{
    			if($data['status'] == 1)
    			{
    				$rekeys[$data['mid']]['date'][] = $data['m_date'];
    				$rekeys[$data['mid']]['mname'][] = $data['mname'];
    			}
    		}
    	}
    	
    	return $rekeys;
    }
	/**
     * 查询冠亚军场次
     * @param unknown_type $issue
     * @param unknown_type $type
     */
    public function getChampion($issue, $type)
    {
    	return $this->dc->query("select mid,odds,status from cp_champion_paiqi where issue=? and type=?", array($issue, $type))->getAll();
    }
    
    /**
     * 更新冠亚军赔率
     * @param unknown_type $issue
     * @param unknown_type $type
     * @param unknown_type $mid
     * @param unknown_type $odds
     */
    public function updateChampionOdds($issue, $type, $mid, $odds, $status)
    {
    	return $this->dc->query("update cp_champion_paiqi set odds=?, status=? where issue=? and type=? and mid=?", array($odds, $status, $issue, $type, $mid));
    }
    
    public function getSchedule() {
    	$sql = "SELECT s.id, m.mid
			FROM cp_champion_schedule as s
			LEFT JOIN cp_jczq_match as m
			on s.home=m.home AND s.away=m.away AND s.begin_time=date_add(m.end_sale_date, interval m.end_sale_time hour_second)
			WHERE s.hid > 0 and s.begin_time >= date_sub( now( ) , INTERVAL 3 DAY )";
    	return $this->dc->query($sql)->getAll();
    }
    
    public function updateSchedule($mid, $id) {
    	$this->dc->where('id', $id);
    	return $this->dc->update('cp_champion_schedule', array('mid' => $mid));
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param int $type
     * @param int $lid
     * @param int $stop
     */
    public function updateTicketStop($type, $lid, $stop)
    {
    	$this->cfgDB = $this->load->database('cfg', TRUE);
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
}
