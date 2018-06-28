<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
class Model_match extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
    	$this->dbDc = $this->load->database('dc', true);
    }
     
    /**
     * 参    数：$limit 整型  查询数量
     * 作    者：shigx
     * 功    能：返还北京单场排期编号
     * 修改日期：2015.03.25
     */
    public function get_bjdc_mids($limit = 10)
    {
       $sql = "SELECT mid FROM cp_bjdc_paiqi GROUP BY mid ORDER BY mid DESC LIMIT ?";
       return $this->slaveDc->query($sql, $limit)->getCol();
    }
    
    /**
     * 参    数：$limit 整型  查询数量
     * 作    者：shigx
     * 功    能：返还体彩足球排期编号
     * 修改日期：2015.03.25
     */
    public function get_tczq_mids($ctype = 1, $limit = 10)
    {
    	$sql = "SELECT mid FROM cp_tczq_paiqi WHERE ctype=? GROUP BY mid ORDER BY mid DESC LIMIT ?";
    	return $this->slaveDc->query($sql, array($ctype, $limit))->getCol();
    }
    
    /**
     * 参    数：$searchData 数组  查询条件
     * 作    者：shigx
     * 功    能：返回北京单场排期信息
     * 修改日期：2015.03.25
     */
    public function list_bjdc($searchData)
    {
    	//条件块
    	$where = " WHERE 1";
    	$where .= $this->condition("mid", $searchData['mid']);
    	$sql = "SELECT * FROM cp_bjdc_paiqi {$where} ORDER BY mname ASC";
    	return $this->slaveDc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$searchData 数组  查询条件
     * 作    者：shigx
     * 功    能：返回北单胜负过关排期信息
     * 修改日期：2015.03.25
     */
    public function list_bdsfgg($searchData)
    {
    	//条件块
    	$where = " WHERE 1";
    	$where .= $this->condition("mid", $searchData['mid']);
    	$sql = "SELECT * FROM cp_sfgg_paiqi {$where} ORDER BY mname ASC";
    	return $this->slaveDc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$searchData 数组  查询条件
     * 作    者：shigx
     * 功    能：返回体彩足球排期信息
     * 修改日期：2015.03.25
     */
    public function list_tczq($searchData)
    {
    	//条件块
    	$where = " WHERE 1";
    	$where .= $this->condition("ctype", $searchData['ctype']);
    	$where .= $this->condition("mid", $searchData['mid']);
    	$sql = "SELECT * FROM cp_tczq_paiqi {$where} ORDER BY mid,mname ASC";
    	return $this->slaveDc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$searchData 数组  查询条件
     * 作    者：shigx
     * 功    能：返回竞彩足球排期信息
     * 修改日期：2015.03.25
     */
    public function list_jczq($searchData)
    {
    	//条件块
    	$where = " WHERE 1";
    	$where .= $this->condition(" p.m_date", array(
    			$searchData['start_time'],
    			$searchData['end_time']
    	), "time");
        // 审核类型
        if($searchData['is_aduitflag'] > 0)
        {
            if($searchData['is_aduitflag'] == 2)
            {
                $where .= " AND p.aduitflag > 0"; 
            }
            else
            {
                $aduitflag = ($searchData['is_aduitflag'] > 3) ? 2 : (($searchData['is_aduitflag'] > 1) ? 1 : 0);
                $where .= " AND p.aduitflag = {$aduitflag}"; 
            }
        }
        // 抓取状态
        if($searchData['is_capture'] > 0)
        {
            $captureflag = ($searchData['is_capture'] > 1) ? 'is null' : 'is not null';
            $where .= " AND s.mid {$captureflag}"; 
        }
    	$sql = "SELECT p.*, IF(s.mid > 0, 1, 0) AS isCapture FROM cp_jczq_paiqi AS p LEFT JOIN cp_jczq_score AS s ON p.mid = s.mid {$where} GROUP BY p.mid ORDER BY p.mid, p.mname ASC";
    	return $this->slaveDc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$searchData 数组  查询条件
     * 作    者：shigx
     * 功    能：返回竞彩篮球排期信息
     * 修改日期：2015.03.25
     */
    public function list_jclq($searchData)
    {
    	//条件块
    	$where = " WHERE 1";
    	$where .= $this->condition(" m_date", array(
    			$searchData['start_time'],
    			$searchData['end_time']
    	), "time");
        // 审核类型
        if($searchData['is_aduitflag'] > 0)
        {
            if($searchData['is_aduitflag'] == 2)
            {
                $where .= " AND p.aduitflag > 0"; 
            }
            else
            {
                $aduitflag = ($searchData['is_aduitflag'] > 3) ? 2 : (($searchData['is_aduitflag'] > 1) ? 1 : 0);
                $where .= " AND p.aduitflag = {$aduitflag}"; 
            }
        }
        // 抓取状态
        if($searchData['is_capture'] > 0)
        {
            $captureflag = ($searchData['is_capture'] > 1) ? 'is null' : 'is not null';
            $where .= " AND s.mid {$captureflag}"; 
        }
    	$sql = "SELECT p.*, IF(s.mid > 0, 1, 0) AS isCapture FROM cp_jclq_paiqi AS p LEFT JOIN cp_jclq_score AS s ON p.mid = s.mid {$where} GROUP BY p.mid ORDER BY p.mid, p.mname ASC";
    	return $this->slaveDc->query($sql)->getAll();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新北京单场记录
     * 修改日期：2015.03.25
     */
    public function bjdc_update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_bjdc_paiqi', $data);
    	return $this->dbDc->affected_rows();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新胜负过关记录
     * 修改日期：2015.03.25
     */
    public function sfgg_update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_sfgg_paiqi', $data);
    	return $this->dbDc->affected_rows();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新体彩足球记录
     * 修改日期：2015.03.25
     */
    public function tczq_update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_tczq_paiqi', $data);
    	return $this->dbDc->affected_rows();
    }
    public function updateCol($searchData)
    {
        $where = " 1";
        $altName = key($searchData);
        $querywhere = $where.$this->condition("id", intval($searchData['id']));
        $query = $this->dbDc->update_string('cp_tczq_paiqi', $searchData , $querywhere );
        $this->dbDc->query( $query);
        return $this->dbDc->affected_rows();
    }
    /**
     * 参    数：$searchData  需要更新的数据
     * 作    者：liuz
     * 功    能：更新体彩足球记录
     * 修改日期：2015.11.20
     */

    public function updateRow($searchData)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("id", intval($searchData['id']));
        $query = $this->dbDc->update_string('cp_tczq_paiqi', $searchData , $querywhere );
        $this->dbDc->query( $query);
        return $this->dbDc->affected_rows();
    }
    /**
     * 参    数：$searchData  需要查找的数据
     * 作    者：liuz
     * 功    能：查找体彩足球记录
     * 修改日期：2015.11.20
     */
    public function selcetTime($type, $searchData, $id)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("id", intval($id));
        $sql = "select ".$searchData."  from cp_".$type."_paiqi where ".$querywhere;
        $res = $this->slaveDc->query($sql)->row_array();
        return $res;
    }
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新竞彩足球记录
     * 修改日期：2015.03.25
     */
    public function jczq_update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_jczq_paiqi', $data);
    	return $this->dbDc->affected_rows();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新竞彩篮球记录
     * 修改日期：2015.03.25
     */
    public function jclq_update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_jclq_paiqi', $data);
    	return $this->dbDc->affected_rows();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 作    者：shigx
     * 功    能：获取北京单场信息
     * 修改日期：2015.03.25
     */
    public function get_bjdc($id)
    {
        return $this->slaveDc->query("select * from cp_bjdc_paiqi where id=?", array($id))->getRow();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 作    者：shigx
     * 功    能：获取胜负过关信息
     * 修改日期：2015.03.25
     */
    public function get_sfgg($id)
    {
        return $this->slaveDc->query("select * from cp_sfgg_paiqi where id=?", array($id))->getRow();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 作    者：shigx
     * 功    能：获取竞彩足球信息
     * 修改日期：2015.03.25
     */
    public function get_jczq($id)
    {
        return $this->slaveDc->query("select * from cp_jczq_paiqi where id=?", array($id))->getRow();
    }

    /**
     * 参    数：$id 整形 主键id
     * 作    者：liuz
     * 功    能：获取体彩足球信息
     * 修改日期：2015.11.16
     */
    public function get_tczq($id)
    {
        return $this->slaveDc->query("select * from cp_tczq_paiqi where id=?", array($id))->getRow();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 作    者：shigx
     * 功    能：获取竞彩足球信息
     * 修改日期：2015.03.25
     */
    public function get_jclq($id)
    {
        return $this->slaveDc->query("select * from cp_jclq_paiqi where id=?", array($id))->getRow();
    }
    
    /**
     * 参    数：$type 类型
     * 作    者：shigx
     * 功    能：查询冠军彩期次信息
     * 修改日期：2015.03.25
     */
    public function get_champion_issues($type = 1)
    {
    	$sql = "SELECT DISTINCT issue FROM cp_champion_paiqi WHERE type=? ORDER BY issue DESC";
    	return $this->slaveDc->query($sql, array($type))->getCol();
    }
    
    /**
     * 查询冠亚军场次信息
     * @param unknown_type $issue
     * @param unknown_type $type
     */
    public function list_champion($issue, $type)
    {
        return $this->slaveDc->query("select * from cp_champion_paiqi where issue=? and type=?", array($issue, $type))->getAll();
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param int $type
     * @param int $lid
     * @param int $stop
     */
    public function updateTicketStop($type, $lid, $stop)
    {
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
    
    /**
     * 更新比分表state状态
     * @param string $table 表名称
     * @param int $mid	场次id
     * @param string $mname	场次名
     */
    public function updateScoreState($table, $mid, $mname = '')
    {
    	$sql = "update {$table} set state=1 where mid=?";
    	if($mname)
    	{
    		$sql .= " and mname = '{$mname}'";
    	}
    	
    	return $this->dbDc->query($sql, array($mid));
    }

    // 竞彩足球 - 查询抓取详情
    public function getJczqCapture($searchData)
    {
        $sql = "SELECT p.mid, s.mname, p.home, p.away, s.half_score, s.full_score, s.source, c.lname, c.cname FROM cp_jczq_score AS s LEFT JOIN cp_jczq_paiqi AS p ON s.mid = p.mid LEFT JOIN cp_cron_score AS c ON s.source = c.source WHERE s.mid = ? AND c.ctype = 'jczq'";
        return $this->slaveDc->query($sql, array($searchData['mid']))->getAll();
    }

    // 竞彩篮球 - 查询抓取详情
    public function getJclqCapture($searchData)
    {
        $sql = "SELECT p.mid, s.mname, p.home, p.away, s.full_score, s.source, c.lname, c.cname FROM cp_jclq_score AS s LEFT JOIN cp_jclq_paiqi AS p ON s.mid = p.mid LEFT JOIN cp_cron_score AS c ON s.source = c.source WHERE s.mid = ? AND c.ctype = 'jclq'";
        return $this->slaveDc->query($sql, array($searchData['mid']))->getAll();
    }

    // 竞彩足球 - 比分录入
    public function saveJczqCapture($mid, $data)
    {
        $this->dbDc->where('mid', $mid);
        $this->dbDc->where('aduitflag', 0);
        $this->dbDc->update('cp_jczq_paiqi', $data);
        return $this->dbDc->affected_rows();
    }

    // 竞彩篮球 - 比分录入
    public function saveJclqCapture($mid, $data)
    {
        $this->dbDc->where('mid', $mid);
        $this->dbDc->where('aduitflag', 0);
        $this->dbDc->update('cp_jclq_paiqi', $data);
        return $this->dbDc->affected_rows();
    }

    // 竞彩足球 - 更新抓取表信息
    public function updateJczqscore($mid, $data)
    {
        $this->dbDc->where('mid', $mid);
        $this->dbDc->update('cp_jczq_score', $data);
        return $this->dbDc->affected_rows();
    }

    // 竞彩篮球 - 更新抓取表信息
    public function updateJclqscore($mid, $data)
    {
        $this->dbDc->where('mid', $mid);
        $this->dbDc->update('cp_jclq_score', $data);
        return $this->dbDc->affected_rows();
    }

    // 竞彩足球 - 查询比分信息
    public function getjczqInfo($idArr)
    {
        return $this->slaveDc->query("select id, mid, m_date, mname, half_score, full_score, aduitflag from cp_jczq_paiqi where id in (".implode(',', $idArr).")")->getAll();
    }

    // 竞彩篮球 - 查询比分信息
    public function getjclqInfo($idArr)
    {
        return $this->slaveDc->query("select id, mid, m_date, mname, full_score, aduitflag from cp_jclq_paiqi where id in (".implode(',', $idArr).")")->getAll();
    }

    // 竞彩足球 - 更新审核状态
    public function updateJczqAduitflag($idArr, $aduitflag)
    {
        $sql = "UPDATE cp_jczq_paiqi SET aduitflag = ?, d_synflag = 0, status = 50 WHERE mid IN (".implode(',', $idArr).") and aduitflag <> 1";
        $this->dbDc->query($sql, array($aduitflag));
    }

    // 竞彩篮球 - 更新审核状态
    public function updateJclqAduitflag($idArr, $aduitflag)
    {
        $sql = "UPDATE cp_jclq_paiqi SET aduitflag = ?, d_synflag = 0, status = 50 WHERE mid IN (".implode(',', $idArr).") and aduitflag <> 1";
        $this->dbDc->query($sql, array($aduitflag));
    }

    // 竞彩足球 - 场次查询
    public function getJczqPaiqi($mid)
    {
        return $this->slaveDc->query("select id, mid, m_date, mname, home, away, half_score, full_score, aduitflag from cp_jczq_paiqi where mid = ?", array($mid))->getRow();
    }

    // 竞彩篮球 - 场次查询
    public function getJclqPaiqi($mid)
    {
        return $this->slaveDc->query("select id, mid, m_date, mname, home, away, full_score, aduitflag from cp_jclq_paiqi where mid = ?", array($mid))->getRow();
    }

    // 更新cfg 竞技彩paiqi status=50
    public function updatejczqStatus($idArr)
    {
        $sql = "UPDATE cp_jczq_paiqi SET status = 0 WHERE mid IN (".implode(',', $idArr).") and aduitflag = 2";
        $this->cfgDB->query($sql);
    }

    // 更新cfg 竞技彩paiqi status=50
    public function updatejclqStatus($idArr)
    {
        $sql = "UPDATE cp_jclq_paiqi SET status = 0 WHERE mid IN (".implode(',', $idArr).") and aduitflag = 2";
        $this->cfgDB->query($sql);
    }

    // 延期操作
    public function updatePaiqiCstate($table, $id, $cstate, $synflag = 0)
    {
        $sql = "UPDATE {$table} SET cstate = (cstate | ?), synflag = ? WHERE id = ?";
        $this->dbDc->query($sql, array($cstate, $synflag, $id));
        return $this->dbDc->affected_rows();
    }

    // 取消延期
    public function cancelDelay($table, $id, $cstate, $synflag = 0)
    {
        $sql = "UPDATE {$table} SET cstate = (cstate ^ ?), synflag = ? WHERE id = ? AND (cstate & 1) = 1";
        $this->dbDc->query($sql, array($cstate, $synflag, $id));
        return $this->dbDc->affected_rows();
    }
}
