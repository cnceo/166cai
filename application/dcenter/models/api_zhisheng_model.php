<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 智胜接口数据入库操作类
 * @author shigx
 *
 */
class Api_Zhisheng_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * 返回赛事mid
     * @param unknown_type $mids
     */
    public function getMatchMids($mids)
    {
    	$sql = "select mid from cp_data_lq_matchs where mid in ?";
    	return $this->dc->query($sql, array($mids))->getCol();
    }
    
    /**
     * 返回赛事mid
     * @param unknown_type $mids
     */
    public function getZqMatchMids($mids)
    {
    	$sql = "select mid from cp_data_zq_matchs where mid in ?";
    	return $this->dc->query($sql, array($mids))->getCol();
    }
    
    /**
     * 返回需要不全信息的场次信息
     */
    public function getZqSidMatchs()
    {
    	$sql = "select mid, lid, sid from cp_data_zq_matchs where modified > date_sub(now(), interval 1 hour) and state='0' and (sid = 0 or homelogo = '')";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 返回需要不全信息的场次信息
     */
    public function getLqSidMatchs()
    {
    	$sql = "select mid, lid, sid from cp_data_lq_matchs where modified > date_sub(now(), interval 1 hour) and state='0' and (sid = 0 or homelogo = '')";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 返回需要更新历史交锋的对阵
     */
    public function getZqMatchs()
    {
    	$sql = "select mid, htid, atid, state, hpm, apm, xid, sid from cp_data_zq_matchs where mtime between ? and ?";
    	$data1 = $this->dc->query($sql, array(date('Y-m-d', strtotime("-1 day")), date('Y-m-d', strtotime("+5 day"))))->getAll();
    	//世界杯临时修改 世界杯结束删除
    	$sql = "select mid, htid, atid, state, hpm, apm, xid, sid from cp_data_zq_matchs where mtime between ? and ? and lid =149 and xid > 0";
    	$data2 = $this->dc->query($sql, array(date('Y-m-d', strtotime("-1 day")), date('Y-m-d', strtotime("+20 day"))))->getAll();
    	return array_merge($data1, $data2);
    }
    
    /**
     * 返回需要更新数据的场次信息
     */
    public function getLqMatchs()
    {
    	$sql = "select mid, htid, atid, state, hpm, apm, xid from cp_data_lq_matchs where mtime between ? and ?";
    	return $this->dc->query($sql, array(date('Y-m-d', strtotime("-1 day")), date('Y-m-d', strtotime("+5 day"))))->getAll();
    }
    
    /**
     * 返回最近非竞彩比赛对阵
     */
    public function getZqNoXidMatchs()
    {
    	$sql = "select mid, state from cp_data_zq_matchs where (mtime between ? and ?) and xid IS NULL";
    	return $this->dc->query($sql, array(date('Y-m-d'), date('Y-m-d', strtotime("+1 day"))))->getAll();
    }
    
    /**
     * 返回篮球最近非竞彩比赛对阵
     */
    public function getLqNoXidMatchs()
    {
    	$sql = "select mid, state from cp_data_lq_matchs where (mtime between ? and ?) and xid IS NULL";
    	return $this->dc->query($sql, array(date('Y-m-d'), date('Y-m-d', strtotime("+1 day"))))->getAll();
    }
    
    /**
     * 获取篮球联赛信息
     */
    public function getLqLids()
    {
    	$sql = "select mid, lid, sid from cp_data_lq_matchs where modified > date_sub(now(), interval 7 day) group by lid";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 获取足球联赛信息
     */
    public function getZqLids()
    {
    	$sql = "select mid, lid, sid from cp_data_zq_matchs where modified > date_sub(now(), interval 7 day) group by lid";
    	return $this->dc->query($sql)->getAll();
    }
    
    /**
     * 根据类型返回所有联赛信息
     * @param unknown_type $type
     */
    public function getLeague($type)
    {
    	$sql = "select * from cp_data_league where type=?";
    	return $this->dc->query($sql, array($type))->getAll();
    }
    
    public function updateMatch($mid, $data)
    {
    	$this->dc->where('mid', $mid);
    	$this->dc->update('cp_data_lq_matchs', $data);
    	return $this->dc->affected_rows();
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将篮球联赛数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveLqMatchs($data)
    {
    	return $this->saveData('cp_data_lq_matchs', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将足球联赛数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveZqMatchs($data)
    {
    	return $this->saveData('cp_data_zq_matchs', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将足球联赛交锋数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveZqHfmatchs($data)
    {
    	return $this->saveData('cp_data_zq_hfmatchs', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将篮球联赛交锋数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveLqHfmatchs($data)
    {
    	return $this->saveData('cp_data_lq_hfmatchs', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将篮球联赛数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveLqExpected($data)
    {
    	return $this->saveData('cp_data_lq_expected', $data);
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将足球联赛数据写入数据库
     * 修改日期：2015-03-16
     */
    public function saveZqExpected($data)
    {
    	return $this->saveData('cp_data_zq_expected', $data);
    }
    
    /**
     * 篮球技术统计数据入库
     */
    public function saveLqStatistics($data)
    {
    	return $this->saveData('cp_data_lq_statistics', $data);
    }
    
    /**
     * 足球技术统计数据入库
     */
    public function saveZqStatistics($data)
    {
    	return $this->saveData('cp_data_zq_statistics', $data);
    }
    
    /**
     * 篮球积分排名数据入库
     */
    public function saveLqRanking($data)
    {
    	return $this->saveData('cp_data_lq_ranking', $data);
    }
    
    /**
     * 足球积分排名数据入库
     */
    public function saveZqRanking($data)
    {
    	return $this->saveData('cp_data_zq_ranking', $data);
    }
    
    /**
     * 足球射手榜数据入库
     */
    public function saveZqShotRank($data)
    {
    	return $this->saveData('cp_data_zq_shotrank', $data);
    }
    
    /**
     * 联赛信息入库
     */
    public function saveLeague($data)
    {
    	return $this->saveData('cp_data_league', $data);
    }
    
    /**
     * 最新赛季信息如库
     * @param unknown_type $data
     */
    public function updateLeague($id, $data)
    {
    	$this->dc->where('id', $id);
    	$this->dc->update('cp_data_league', $data);
    	return $this->dc->affected_rows();
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
    		$vData[] = implode('|||', $v);
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
    
    	$vData = implode('|||', $vData);
    	return $this->dc->query($sql, explode('|||', $vData));
    }
    
    /**
     * 查询历史交锋近十场战绩
     * @param unknown_type $match
     */
    public function getLqHistoryMatch($match)
    {
    	$sql = "select htid,atid, hqt,aqt from cp_data_lq_hfmatchs where (htid = ? and atid = ?) or (htid = ? and atid = ?) and state='1' ORDER BY mtime DESC LIMIT 10";
    	return $this->dc->query($sql, array($match['htid'], $match['atid'], $match['atid'], $match['htid']))->getAll();
    }
    
    /**
     * 查询最近十场战绩
     * @param unknown_type $tid
     */
    public function getLqRecentMatch($tid)
    {
    	$sql = "select htid,atid, hqt,aqt from cp_data_lq_hfmatchs where htid = ? or atid = ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	return $this->dc->query($sql, array($tid, $tid))->getAll();
    }
    
    /**
     * 将非竞彩赛事状态更新为完结
     */
    public function updateZqMatchState()
    {
    	$sql = "update cp_data_zq_matchs set state='4' where modified > date_sub(now(), interval 3 day) and (xid is null) and state='0' and (date_add(`mtime`, interval 180 minute) < now()) and bc !=''";
    	return $this->dc->query($sql);
    }
    
    /**
     * 根据竞彩id查询mid信息
     * @param unknown_type $xids
     */
    public function getLqMidByXids($xids)
    {
    	$sql = "select xid, mid from cp_data_lq_matchs where xid in ?";
    	return $this->dc->query($sql, array($xids))->getAll();
    }
    
    /**
     * 根据竞彩id查询mid信息
     * @param unknown_type $xids
     */
    public function getZqMidByXids($xids)
    {
    	$sql = "select xid, mid from cp_data_zq_matchs where xid in ?";
    	return $this->dc->query($sql, array($xids))->getAll();
    }
    
    /**
     * 删除xid对应关系
     * @param unknown_type $mids
     */
    public function deleteLqXid($mids)
    {
    	return $this->dc->query("update cp_data_lq_matchs set xid = null where mid in ?", array($mids));
    }
    
    /**
     * 删除xid对应关系
     * @param unknown_type $mids
     */
    public function deleteZqXid($mids)
    {
    	return $this->dc->query("update cp_data_zq_matchs set xid = null where mid in ?", array($mids));
    }

    /**
     * 查询历史交锋近十场战绩
     * @param unknown_type $match
     */
    public function getZqHistoryMatch($match)
    {
        $sql = "select htid,atid, hqt,aqt from cp_data_zq_hfmatchs where ((htid = ? and atid = ?) or (htid = ? and atid = ?)) and state='1' ORDER BY mtime DESC LIMIT 10";
        return $this->dc->query($sql, array($match['htid'], $match['atid'], $match['atid'], $match['htid']))->getAll();
    }

    /**
     * 查询最近十场战绩
     * @param unknown_type $tid
     */
    public function getZqRecentMatch($tid)
    {
        $sql = "select htid,atid, hqt,aqt from cp_data_zq_hfmatchs where (htid = ? or atid = ?) and state='1' ORDER BY mtime DESC LIMIT 10";
        return $this->dc->query($sql, array($tid, $tid))->getAll();
    }

    public function getJczqLiveMatch()
    {
        $sql = "select xid, mid, home, away, mtime, bc, hqt, aqt, state from cp_data_zq_matchs where mtime >= date_sub(now(), interval 1 day) and mtime <= date_add(now(), interval 1 day) and xid > 0 order by mtime";
        return $this->dc->query($sql)->getAll();
    }

    public function getJclqLiveMatch()
    {
        $sql = "select xid, mid, home, away, mtime, hs1, as1, hs2, as2, hqt, aqt, state from cp_data_lq_matchs where mtime >= date_sub(now(), interval 1 day) and mtime <= date_add(now(), interval 1 day) and xid > 0 order by mtime";
        return $this->dc->query($sql)->getAll();
    }

    public function getZqStatistics($mid)
    {
        $sql = "select event, total from cp_data_zq_statistics where mid = ?";
        return $this->dc->query($sql, array($mid))->getRow();
    }
    
    /**
     * 参    数：$data,数组,赛事信息数组
     * 作    者：shigx
     * 功    能：将足球分析数据写入数据库
     * 修改日期：2018-03-06
     */
    public function saveZqCalculate($data)
    {
        return $this->saveData('cp_data_zq_calculate', $data);
    }
    
    /**
     * 返回足球智能推荐数据
     * @param unknown $mids
     * @return unknown
     */
    public function getZqCalculate($mids)
    {
        $sql = "select mid, hrank, arank, hrecent, arecent, hhistorical, ahistorical, odds, bet, transaction, prediction from 
        cp_data_zq_calculate where mid IN ?";
        return $this->dc->query($sql, array($mids))->getAll();
    }
}
