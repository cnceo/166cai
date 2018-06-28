<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:遗漏数据服务类
 */
class Missed_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 查询最大的一期遗漏数据
     * @param unknown_type $lid
     */
    public function fetchLastIssue($lid)
    {
        $sql = "SELECT MAX(issue) FROM cp_missed_counter WHERE lid = ? and play_type =0";
        return $this->cfgDB->query($sql, array($lid))->getOne();
    }
    
    /**
     * 查询期次的遗漏数据
     * @param unknown_type $lid
     * @param unknown_type $issue
     */
    public function fetchDetail($lid, $issue)
    {
    	$sql = "select lid, issue, play_type, detail from cp_missed_counter where lid=? and issue=? and play_type =0";
    	return $this->cfgDB->query($sql, array($lid, $issue))->getRow();
    }

    /**
     * 查询期次的遗漏数据
     * @param unknown_type $lid
     * @param unknown_type $issue
     */
    public function fetchMissDetail($lid, $issue)
    {
        $sql = "SELECT detail FROM cp_missed_counter WHERE lid = $lid AND issue = $issue";
        return $this->cfgDB->query($sql)->getCol();
    }
    
    /**
     * 查询期次开奖号码
     * @param unknown_type $type
     * @param unknown_type $startIssue
     */
	public function fetchIssueRecords($type, $startIssue = 0)
    {
        $sql = "SELECT issue, awardNum FROM cp_{$type}_paiqi
        WHERE issue > ? AND awardNum IS NOT NULL AND status >= 50
        ORDER BY issue ASC LIMIT 500";
        return $this->cfgDB->query($sql, array($startIssue))->getAll();
    }
    
    /**
     * 根据期次查询期次开奖号码
     * @param unknown_type $type
     * @param unknown_type $issue
     */
    public function fetchAward($type, $issue)
    {
    	$sql = "select issue, awardNum from cp_{$type}_paiqi where issue > ? and awardNum IS NOT NULL AND status >= 50 ORDER BY issue ASC LIMIT 1";
    	return $this->cfgDB->query($sql, array($issue))->getRow();
    }
    
    /**
     * 遗漏入库操作
     * @param unknown_type $data
     */
    public function insertMissed($data)
    {
    	$val = array_values($data);
    	$sql = "INSERT IGNORE cp_missed_counter (lid, issue, play_type, detail, created) VALUES (?, ?, ?, ?, now())";
    	return $this->cfgDB->query($sql, $val);
    }

    /**
     * 快乐扑克遗漏入库操作
     * @param unknown_type $data
     */
    public function insertMissedData($data)
    {
        $valueStr = implode(',', $data);
        $sql = "INSERT IGNORE cp_missed_counter (lid, issue, play_type, detail, created) VALUES $valueStr";
        return $this->cfgDB->query($sql);
    }
    
    /**
     * 查询历史遗漏数据
     * @param unknown_type $lid
     * @param unknown_type $limit
     */
    public function fetchMisseds($lid, $limit)
    {
    	$sql = "SELECT * FROM cp_missed_counter WHERE lid = ? and play_type =0 ORDER BY issue DESC LIMIT ?";
    	return $this->cfgDB->query($sql, array($lid, $limit))->getAll();
    }
    
    /**
     * 遗漏数据入缓存
     * @param unknown_type $type
     * @param unknown_type $lid
     * @param unknown_type $limit
     */
    public function writeRedis($type, $lid, $limit)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$redis = $this->config->item('REDIS');
    	$sql = "SELECT * FROM cp_missed_counter WHERE lid = ? ORDER BY issue DESC LIMIT ?";
    	$records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
    	$missCounter = array();
    	foreach ($records as $rc)
    	{
    		if (empty($missCounter[$rc['issue']]))
    		{
    			$missCounter[$rc['issue']] = array();
    		}
    		$missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
    	}
    	$this->cache->save($redis[strtoupper($type) . '_MISS'], json_encode($missCounter), 0);
    }

    /**
     * 快三遗漏数据入缓存
     * @param unknown_type $type
     * @param unknown_type $lid
     * @param unknown_type $limit
     */
    public function writeRedisMore($type, $lid, $limit)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$redis = $this->config->item('REDIS');
    	$sql = "SELECT * FROM cp_missed_counter WHERE lid = ? and play_type=0 ORDER BY issue DESC LIMIT ?";
    	$records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
    	$missCounter = array();
    	foreach ($records as $rc)
    	{
    		if (empty($missCounter[$rc['issue']]))
    		{
    			$missCounter[$rc['issue']] = array();
    		}
    		$missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
    		//unset($missCounter[$rc['issue']][1]); //将冷热数据清除
    	}
    	$this->cache->save($redis[strtoupper($type) . '_MISS_MORE'], json_encode($missCounter), 0);
    }
    /**
     * [writeRedisSerialize Serialize格式缓存]
     * @author LiKangJian 2017-05-24
     * @param  [type] $type  [description]
     * @param  [type] $lid   [description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function writeRedisSerialize($type, $lid, $limit)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = ? ORDER BY issue DESC LIMIT ?";
        $records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
        $missCounter = array();
        foreach ($records as $rc)
        {
            if (empty($missCounter[$rc['issue']]))
            {
                $missCounter[$rc['issue']] = array();
            }
            if(in_array($type,array('qlc','ssq','dlt')))
            {
             $missCounter[$rc['issue']] = $rc['detail'];
            }else{
             $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];   
            }
            
        }
        $this->cache->save($redis[strtoupper($type) . '_MISS'], serialize($missCounter), 0);
    }
    /**
     * 遗漏数据入缓存
     * @param unknown_type $type
     * @param unknown_type $lid
     * @param unknown_type $limit
     */
    public function writeMissMore($type, $lid, $limit)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = ? ORDER BY issue DESC LIMIT ?";
        $records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
        $missCounter = array();
        foreach ($records as $rc)
        {
            if (empty($missCounter[$rc['issue']]))
            {
                $missCounter[$rc['issue']] = array();
            }
            $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
        }
        $this->cache->save($redis[strtoupper($type) . '_MISS_MORE'], json_encode($missCounter), 0);
    }
    /**
     * [writeRedisMoreSerialize 写入多条Serialize格式缓存]
     * @author LiKangJian 2017-05-24
     * @param  [type] $type  [description]
     * @param  [type] $lid   [description]
     * @param  [type] $limit [description]
     * @return [type]        [description]
     */
    public function writeRedisMoreSerialize($type, $lid, $limit)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = ?  ORDER BY issue DESC LIMIT ?";
        $records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
        $missCounter = array();
        foreach ($records as $rc)
        {
            if (empty($missCounter[$rc['issue']]))
            {
                $missCounter[$rc['issue']] = array();
            }
            if(in_array($type,array('qlc','ssq','dlt')))
            {
             $missCounter[$rc['issue']] = $rc['detail'];
            }else{
             $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];   
            }

        }
        $this->cache->save($redis[strtoupper($type) . '_MISS_MORE'], serialize($missCounter), 0);
    }
    /**
     * 页面静态化从数据库读取遗漏
     * @param unknown $lid
     * @param unknown $limit
     * @return Ambigous <multitype:multitype: , unknown>
     */
    public function getData($lid, $limit)
    {
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = ? ORDER BY issue DESC LIMIT ?";
        $records = $this->cfgDB->query($sql, array($lid, $limit))->getAll();
        $missCounter = array();
        foreach ($records as $rc)
        {
            if (empty($missCounter[$rc['issue']]))
            {
                $missCounter[$rc['issue']] = array();
            }
            $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
        }
        return $missCounter;
    }
    
    /**
     * 查询上次统计遗漏的期次信息
     * @param unknown_type $lid
     */
    public function getLastIssue($lid)
    {
    	$sql = "SELECT issue FROM cp_miss_data WHERE lid = ?";
    	return $this->dc->query($sql, array($lid))->getOne();
    }
    
    /**
     * 返回指定彩种下的遗漏统计数据
     * @param unknown_type $lid
     */
    public function getMissDataByLid($lid)
    {
    	$sql = "select id, issue, playType, modType, codes, curMiss, lastMiss, maxMiss, showTotal, missTotal, lastTenMissingTimes,
    	curHit, maxHit from cp_miss_data where lid = ?";
    	return $this->dc->query($sql, array($lid))->getAll();
    }
    
    /**
     * 组合遗漏数据入库操作
     * @param unknown_type $data
     * @param unknown_type $issue
     */
    public function insertZhMissedData($data)
    {
    	if(empty($data))
    	{
    		return;
    	}
    	//处理on duplication
    	$tail = array();
    	$upd = array('curMiss', 'lastMiss', 'maxMiss', 'showTotal', 'missTotal', 'lastTenMissingTimes', 'curHit', 'maxHit', 'issue');
    	foreach ($upd as $field)
    	{
    		array_push($tail, "$field = values($field)");
    	}
    	$duplication = " ON DUPLICATE KEY UPDATE " . implode(', ', $tail);
    	$this->dc->trans_start();
    	$insertVal = "";
    	$issue = '';
    	$fields = array();
    	$count = 0;
    	foreach($data as $value)
    	{
    		if(empty($fields))
    		{
    			$issue = $value['issue'];
    			$fields = array_keys($value);
    		}
    		$flag = $insertVal == null ? "" : ",";
    		$v = array_values($value);
    		$insertVal .= $flag."('".implode("','", $v)."')";
    		$count ++;
    		if($count >= 1000)
    		{
    			$sql = "INSERT INTO `cp_miss_data` (".implode(',', $fields).")
    			VALUES ".$insertVal . $duplication;
    			if(substr($issue, -2) == '01')
    			{
    				//每天的第一期数据记录日志，防止需要重跑
    				log_message('log', $sql, 'ZhMissedData');
    			}
    			$result = $this->dc->query($sql);
    			if(!$result)
    			{
    				$this->dc->trans_rollback();
    				return false;
    			}
    			 
    			$count = 0;
    			$insertVal = "";
    		}
    	}
    	 
    	if($count > 0)
    	{
    		$sql = "INSERT INTO `cp_miss_data` (".implode(',', $fields).")
    		VALUES ".$insertVal . $duplication;
    		if(substr($issue, -2) == '01')
    		{
    			//每天的第一期数据记录日志，防止需要重跑
    			log_message('log', $sql, 'ZhMissedData');
    		}
    		$result = $this->dc->query($sql);
    		if(!$result)
    		{
    			$this->dc->trans_rollback();
    			return false;
    		}
    	}
    	 
    	$this->dc->trans_complete();
    	return true;
    }
}