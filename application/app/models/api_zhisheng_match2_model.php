<?php

/**
 * 智胜竞彩比赛数据接口
 * @date:2016-04-11
 */
class Api_Zhisheng_Match2_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->config->load('jcMatch');
        $this->redis = $this->config->item('redisList');
        $this->apiUrl = $this->config->item('apiUrl');
        $this->shortName = $this->config->item('shortName');
    }

    /*
     * 获取缓存数据
     */
    public function getRedisData($redisKey)
    {
    	$this->load->driver('cache', array('adapter' => 'redis', 'dbname' => 'slave'), 'cacheSlave');
        $resData = $this->cacheSlave->get($redisKey);
        return json_decode($resData, true);
    }
    
    /**
     * 获取联赛信息
     * @param int $type	类型  1 足球  2 篮球
     * @return mixed
     */
    public function getLeague($type)
    {
    	$redisKey = "{$this->redis['LEAGUES']}{$type}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$sql = "select lid, cnshort, logo, sid from cp_data_league where type = ? order by sort asc";
    	$result = $this->slaveDc->query($sql, array($type))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 86400);
    	
    	return $result;
    }
    
    /**
     * 返回联赛赛季比赛类型数据
     * @param int $lid	联赛编号
     * @param int $sid  赛季编号
     * @return mixed|unknown
     */
    public function getMatchOnames($lid, $sid)
    {
    	$redisKey = "{$this->redis['JCLQ_ONAMES']}{$sid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$sql = "select DISTINCT oname from cp_data_lq_matchs where lid = ? and sid = ?";
    	$result = $this->slaveDc->query($sql, array($lid, $sid))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	
    	return $result;
    }
    
    /**
     * 返回联赛赛季比赛类型数据
     * @param int $lid	联赛编号
     * @param int $sid  赛季编号
     * @return mixed|unknown
     */
    public function getZqMatchOnames($lid, $sid)
    {
    	$redisKey = "{$this->redis['JCZQ_ONAMES']}{$sid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$sql = "select DISTINCT oname from cp_data_zq_matchs where lid = ? and sid = ? ORDER BY mtime DESC";
    	$result = $this->slaveDc->query($sql, array($lid, $sid))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	 
    	return $result;
    }
    
    /**
     * 根据日期查询开始记录
     * @param unknown_type $date
     */
    public function getLqMatchOname($lid, $sid, $date)
    {
    	$redisKey = "{$this->redis['JCLQ_MATCHONAME']}{$sid}_{$date}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$sql = "select oname from cp_data_lq_matchs where lid = ? and sid = ? and mtime > ? and state not in('9') order by mtime asc limit 1";
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $date))->getRow();
    	if(empty($result))
    	{
    		$sql = "select oname from cp_data_lq_matchs FORCE INDEX(lid_s) where lid = ? and sid = ? and mtime < ? order by mid desc limit 1";
    		$result = $this->slaveDc->query($sql, array($lid, $sid, $date))->getRow();
    	}
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	 
    	return $result;
    }
    
    /**
     * 根据日期查询开始记录
     * @param unknown_type $date
     */
    public function getZqMatchOname($lid, $sid, $date)
    {
    	$redisKey = "{$this->redis['JCZQ_MATCHONAME']}{$sid}_{$date}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$sql = "select oname from cp_data_zq_matchs where lid = ? and sid = ? and mtime > ? and state not in('4') order by mtime asc limit 1";
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $date))->getRow();
    	if(empty($result))
    	{
    		$sql = "select oname from cp_data_zq_matchs FORCE INDEX(lid_s) where lid = ? and sid = ? and mtime < ? order by mid desc limit 1";
    		$result = $this->slaveDc->query($sql, array($lid, $sid, $date))->getRow();
    	}
    	$this->cache->save($redisKey, json_encode($result), 3600);
    
    	return $result;
    }
    
    /**
     * 篮球赛事赛程第一页列表
     * @param unknown_type $lid
     * @param unknown_type $sid
     * @param unknown_type $oname
     * @param unknown_type $date
     */
    public function getLqMatchStartSchedule($lid, $sid, $oname, $date)
    {
    	$redisKey = "{$this->redis['JCLQ_LEAGUE_SCHEDULE']}{$sid}_{$oname}_{$date}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo
    	from cp_data_lq_matchs where lid = ? and sid = ? and oname = ? and mtime > ? order by mtime asc, mid asc limit 50";
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $oname, $date))->getAll();
    	if(empty($result))
    	{
    		$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo
    		from cp_data_lq_matchs  FORCE INDEX(lid_s) where lid = ? and sid = ? and oname = ? and mtime < ? order by  mtime desc, mid desc limit 50";
    		$result = $this->slaveDc->query($sql, array($lid, $sid, $oname, $date))->getAll();
    	}
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 600);
    	 
    	return $result;
    }
    
    /**
     * 篮球赛事赛程第一页列表
     * @param unknown_type $lid
     * @param unknown_type $sid
     * @param unknown_type $oname
     */
    public function getZqMatchStartSchedule($lid, $sid, $oname)
    {
    	$redisKey = "{$this->redis['JCZQ_LEAGUE_SCHEDULE']}{$sid}_{$oname}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo,scoreState, (if(htid = 0 or atid = 0, 1, 0)) as canJump
    	from cp_data_zq_matchs where lid = ? and sid = ? and oname = ?  ORDER BY coname ASC,mtime ASC,mid ASC";
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $oname))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 600);
    
    	return $result;
    }
    
    /**
     * 返回赛程列表信息
     * @param int $lid
     * @param int $sid
     * @param string $oname
     * @param int $mid
     * @param int $pageFlag
     * @return unknown
     */
    public function getLqMatchSchedule($lid, $sid, $oname, $mtime, $pageFlag)
    {
    	$redisKey = "{$this->redis['JCLQ_LEAGUE_SCHEDULE']}{$sid}_{$oname}_{$mtime}_{$pageFlag}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	if($pageFlag)
    	{
    		$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo
    		from cp_data_lq_matchs where lid = ? and sid = ? and oname = ? and mtime > ? order by mtime asc, mid asc limit 50";
    	}
    	else
    	{
    		$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo
    		from cp_data_lq_matchs  FORCE INDEX(lid_s) where lid = ? and sid = ? and oname = ? and mtime < ? order by mtime desc, mid desc limit 50";
    	}
    	
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $oname, $mtime))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 600);
    	
    	return $result;
    }
    
    /**
     * 返回赛程列表信息
     * @param int $lid
     * @param int $sid
     * @param string $oname
     * @return unknown
     */
    public function getZqMatchSchedule($lid, $sid, $oname)
    {
    	$redisKey = "{$this->redis['JCZQ_LEAGUE_SCHEDULE']}{$sid}_{$oname}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	$sql = "select mid,home,away,htid,atid,mtime,hqt, aqt,state,coname,homelogo,awaylogo,scoreState, (if(htid = 0 or atid = 0, 1, 0)) as canJump
    	from cp_data_zq_matchs where lid = ? and sid = ? and oname = ? ORDER BY coname ASC,mtime ASC,mid ASC";
    	
    	$result = $this->slaveDc->query($sql, array($lid, $sid, $oname))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 600);
    	 
    	return $result;
    }
    
    /** 篮球联赛积分榜信息
    * @param unknown_type $sid
    * @return mixed|unknown
    */
    public function getLqScoreRank($sid)
    {
    	$redisKey = "{$this->redis['JCLQ_SCORERANK']}{$sid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select tid,loc,name,win,lose,wrate,cc,jwin,lx,rank from cp_data_lq_ranking where sid=? ORDER BY sort ASC";
    	$result = $this->slaveDc->query($sql, array($sid))->getAll();
        if(!empty($result))
        {
            foreach ($result as $key => $items) 
            {
                $result[$key]['name'] = $this->getShortName($items['name']);
            }
        }
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	return $result;
    }
    
    /**
     * 足球联赛积分榜信息
     * @param unknown_type $sid
     * @return mixed|unknown
     */
    public function getZqScoreRank($sid)
    {
    	$redisKey = "{$this->redis['JCZQ_SCORERANK']}{$sid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select tid,name,num,w,d,l,goal,loss,diff,score, grouping, type, oname from cp_data_zq_ranking where sid=? ORDER BY sort ASC";
    	$result = $this->slaveDc->query($sql, array($sid))->getAll();
    	$data = array();
    	foreach ($result as $value)
    	{
            $value['name'] = $this->getShortName($value['name']);
    		$data[$value['oname']][] = $value;
    	}
    	if($data)
    	{
    		$data = array_pop($data);
    	}
    	$this->cache->save($redisKey, json_encode($data), 3600);
    	return $data;
    }
    
    /**
     * 篮球联赛积分榜信息
     * @param unknown_type $sid
     * @return mixed|unknown
     */
    public function getZqShotRank($sid)
    {
    	$redisKey = "{$this->redis['JCZQ_SHOTRANK']}{$sid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    
    	$sql = "select name,pname,jq,dq from cp_data_zq_shotrank where sid=? ORDER BY jq DESC, dq ASC";
    	$result = $this->slaveDc->query($sql, array($sid))->getAll();
        if(!empty($result))
        {
            foreach ($result as $key => $items) 
            {
                $result[$key]['name'] = $this->getShortName($items['name']);
            }
        }
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	return $result;
    }
    
    /**
     * 篮球比赛详情
     * @param int $mid
     * @return unknown
     */
    public function getLqMatchDetail($mid)
    {
    	$redisKey = "{$this->redis['JCLQ_MATCHDETAIL']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	$sql = "select * from cp_data_lq_matchs where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getRow();
        if(!empty($result))
        {
            $result['home'] = $this->getShortName($result['home']);
            $result['away'] = $this->getShortName($result['away']);
        }
    	$this->cache->save($redisKey, json_encode($result), 10);
    	return $result;
    }
    
    /**
     * 足球比赛详情
     * @param int $mid
     * @return unknown
     */
    public function getZqMatchDetail($mid)
    {
    	$redisKey = "{$this->redis['JCZQ_MATCHDETAIL']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select * from cp_data_zq_matchs where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getRow();
        if(!empty($result))
        {
            $result['home'] = $this->getShortName($result['home']);
            $result['away'] = $this->getShortName($result['away']);
        }
    	$this->cache->save($redisKey, json_encode($result), 10);
    	return $result;
    }
    
    /**
     * 足球比赛详情
     * @param int $mid
     * @return unknown
     */
    public function getZqPrediction($mid)
    {
        $redisKey = "{$this->redis['JCZQ_PREDICTION']}{$mid}";
        $redisData = $this->getRedisData($redisKey);
        if($redisData)
        {
            return $redisData;
        }
        
        $sql = "select * from cp_data_zq_calculate where mid=?";
        $result = $this->slaveDc->query($sql, array($mid))->getRow();
        
        $this->cache->save($redisKey, json_encode($result), 60);
        
        return $result;
    }
    
    /**
     * 返回篮球统计信息
     * @param unknown_type $mid
     */
    public function getLqStatistics($mid)
    {
    	$redisKey = "{$this->redis['JCLQ_MATCHSTATISTICS']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	$sql = "select mid,type,s2num,s2hit,s3num,s3hit,sbnum,sbhit,ords,drds,assists,steals,bshots,fouls,turnovers,fbnum,fbhit,mlead from cp_data_lq_statistics where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 600);
    	return $result;
    }
    
    /**
     * 返回篮球统计信息
     * @param unknown_type $mid
     */
    public function getZqStatistics($mid)
    {
    	$redisKey = "{$this->redis['JCZQ_MATCHSTATISTICS']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select mid,event, total from cp_data_zq_statistics where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getRow();
    	$this->cache->save($redisKey, json_encode($result), 600);
    	return $result;
    }
    
    /**
     * 返回篮球比赛预计阵容数据
     * @param unknown_type $mid
     */
    public function getLqPlayer($mid)
    {
    	$redisKey = "{$this->redis['JCLQ_MATCHPLAYER']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	$sql = "select mid,type,pid,name,number,point,status,info from cp_data_lq_expected where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 600);
    	return $result;
    }
    
    /**
     * 返回足球比赛预计阵容数据
     * @param unknown_type $mid
     */
    public function getZqPlayer($mid)
    {
    	$redisKey = "{$this->redis['JCZQ_MATCHPLAYER']}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    
    	$sql = "select mid,type,pid,name,number,point,status from cp_data_zq_expected where mid=?";
    	$result = $this->slaveDc->query($sql, array($mid))->getAll();
    	$this->cache->save($redisKey, json_encode($result), 600);
    	return $result;
    }
    
    /**
     * 返回竞彩篮球历史交锋数据
     * @param unknown_type $matchInfo
     */
    public function getLqHistoryMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCLQ_MATCHHISTORY']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	//主客相同十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,bet as binfo from cp_data_lq_hfmatchs where htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$smatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['atid'], $matchInfo['mtime']))->getAll();
    	//主客相反十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,bet as binfo from cp_data_lq_hfmatchs where htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$omatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['htid'], $matchInfo['mtime']))->getAll();
    	$result = array(
    		'smatch' => $this->dataFormat($smatchs),
    		'omatch' => $this->dataFormat($omatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	
    	return $result;
    }
    
    /**
     * 返回竞彩足球历史交锋数据
     * @param unknown_type $matchInfo
     */
    public function getZqHistoryMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCZQ_MATCHHISTORY']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	//主客相同十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$smatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['atid'], $matchInfo['mtime']))->getAll();
    	//主客相反十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$omatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['htid'], $matchInfo['mtime']))->getAll();
    	$binfo = array('赢' => '输', '输' => '赢', '走' => '走', '-' => '-');
    	foreach ($omatchs as $key => $value)
    	{
    		$omatchs[$key]['binfo'] = $binfo[$value['binfo']];
    	}
    	//相同赛事主客相同十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where lid = ? and htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$slmatchs = $this->slaveDc->query($sql, array($matchInfo['lid'], $matchInfo['htid'], $matchInfo['atid'], $matchInfo['mtime']))->getAll();
    	//主客相反十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where lid = ? and htid = ? and atid = ? and mtime < ? and state='1' ORDER BY mtime DESC LIMIT 10";
    	$olmatchs = $this->slaveDc->query($sql, array($matchInfo['lid'], $matchInfo['atid'], $matchInfo['htid'], $matchInfo['mtime']))->getAll();
    	foreach ($olmatchs as $key => $value)
    	{
    		$olmatchs[$key]['binfo'] = $binfo[$value['binfo']];
    	}
    	$result = array(
    		'smatch' => $this->dataFormat($smatchs),
    		'omatch' => $this->dataFormat($omatchs),
    		'slmatch' => $this->dataFormat($slmatchs),
    		'olmatch' => $this->dataFormat($olmatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	 
    	return $result;
    }
    
    /**
     * 返回篮球近期战绩
     * @param unknown_type $matchInfo
     * @return unknown
     */
    public function getLqLastMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCLQ_LASTMATCH']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	//主队五十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,bet as binfo from cp_data_lq_hfmatchs where (htid = ? or atid = ?) and mtime < ? and state='1' ORDER BY int_mtime DESC LIMIT 50";
    	$hmatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['htid'], $matchInfo['mtime']))->getAll();
    	//客队五十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,bet as binfo from cp_data_lq_hfmatchs where (htid = ? or atid = ?) and mtime < ? and state='1' ORDER BY int_mtime DESC LIMIT 50";
    	$amatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['atid'], $matchInfo['mtime']))->getAll();
    	$result = array(
    		'hmatch' => $this->dataFormat($hmatchs),
    		'amatch' => $this->dataFormat($amatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	 
    	return $result;
    }
    
    /**
     * 返回足球近期战绩
     * @param unknown_type $matchInfo
     * @return unknown
     */
    public function getZqLastMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCZQ_LASTMATCH']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	//主队五十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where (htid = ? or atid = ?) and mtime < ? and state='1' ORDER BY int_mtime DESC LIMIT 50";
    	$hmatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['htid'], $matchInfo['mtime']))->getAll();
    	$binfo = array('赢' => '输', '输' => '赢', '走' => '走', '-' => '-');
    	foreach ($hmatchs as $key => $value)
    	{
    		if($matchInfo['htid'] != $value['htid'])
    		{
    			$hmatchs[$key]['binfo'] = $binfo[$value['binfo']];
    		}
    	}
    	//客队五十条数据
    	$sql = "select mid,sid,ln,home,away,mtime,hqt,aqt,htid,atid,binfo from cp_data_zq_hfmatchs where (htid = ? or atid = ?) and mtime < ? and state='1' ORDER BY int_mtime DESC LIMIT 50";
    	$amatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['atid'], $matchInfo['mtime']))->getAll();
    	foreach ($amatchs as $key => $value)
    	{
    		if($matchInfo['atid'] != $value['htid'])
    		{
    			$amatchs[$key]['binfo'] = $binfo[$value['binfo']];
    		}
    	}
    	$result = array(
    			'hmatch' => $this->dataFormat($hmatchs),
    			'amatch' => $this->dataFormat($amatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    
    	return $result;
    }
    
    /**
     * 返回篮球未来赛事
     * @param unknown_type $matchInfo
     * @return unknown
     */
    public function getLqFutureMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCLQ_FUTUREMATCH']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	//主队三条数据
    	$sql = "select mid,sid,ln,home,away,mtime,htid,atid from cp_data_lq_matchs where (htid = ? or atid = ?) and mtime > ? and mid <> ? order by mtime asc LIMIT 3";
    	$hmatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['htid'], $matchInfo['mtime'], $matchInfo['mid']))->getAll();
    	//客队三条数据
    	$sql = "select mid,sid,ln,home,away,mtime,htid,atid from cp_data_lq_matchs where (htid = ? or atid = ?) and mtime > ? and mid <> ? order by mtime asc LIMIT 3";
    	$amatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['atid'], $matchInfo['mtime'], $matchInfo['mid']))->getAll();
    	$result = array(
    		'hmatch' => $this->dataFormat($hmatchs),
    		'amatch' => $this->dataFormat($amatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	
    	return $result;
    }
    
    /**
     * 返回足球未来赛事
     * @param unknown_type $matchInfo
     * @return unknown
     */
    public function getZqFutureMatch($matchInfo)
    {
    	$redisKey = "{$this->redis['JCZQ_FUTUREMATCH']}{$matchInfo['mid']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	//主队三条数据
    	$sql = "select mid,sid,ln,home,away,mtime,htid,atid from cp_data_zq_hfmatchs FORCE INDEX(int_mtime) where (htid = ? or atid = ?) and mtime > ? and mid <> ? order by int_mtime asc LIMIT 3";
    	$hmatchs = $this->slaveDc->query($sql, array($matchInfo['htid'], $matchInfo['htid'], $matchInfo['mtime'], $matchInfo['mid']))->getAll();
    	//客队三条数据
    	$sql = "select mid,sid,ln,home,away,mtime,htid,atid from cp_data_zq_hfmatchs FORCE INDEX(int_mtime) where (htid = ? or atid = ?) and mtime > ? and mid <> ? order by int_mtime asc LIMIT 3";
    	$amatchs = $this->slaveDc->query($sql, array($matchInfo['atid'], $matchInfo['atid'], $matchInfo['mtime'], $matchInfo['mid']))->getAll();
    	$result = array(
    		'hmatch' => $this->dataFormat($hmatchs),
    		'amatch' => $this->dataFormat($amatchs),
    	);
    	$this->cache->save($redisKey, json_encode($result), 3600);
    	 
    	return $result;
    }
    
    /*
     * API 获取赛事欧赔亚赔列表
    */
    public function getOddList($type, $mid, $lid)
    {
    	$lidMap = array(
    		'42' => array('o' => '2002', 'y' => '2003'),
    		'43' => array('o' => '5002', 'y' => '5006'),
    	);
    	if(empty($lidMap[$lid][$type]))
    	{
    		return array();
    	}
    
    	/*$redisKey = "{$this->redis['ODD_LIST']}{$lidMap[$lid][$type]}{$mid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}*/
    	$url = $this->apiUrl . 'home/base/?oid=' . $lidMap[$lid][$type] . '&mid=' . $mid;
    	$content = $this->tools->request($url);
    	$content = json_decode($content, true);
    	if($content['desc'] != 'succ' || $content['code'] != '0000' || empty($content['row']))
    	{
    		return array();
    	}
    	
    	$data = array();
    	if(!empty($content['row']))
    	{
    		$data = $content['row'];
    	}
    	//$this->cache->save($redisKey, json_encode($data), 10);
    	
    	return $data;
    }
    
    /*
     * API 获取赛事欧赔亚赔主流公司列表
    */
    public function getArteryList($lid)
    {
    	$lidMap = array(
    		'42' => '2001',
    		'43' => '5001',
    	);
    	if(empty($lidMap[$lid]))
    	{
    		return array();
    	}
    
    	$redisKey = "{$this->redis['JC_ARTERY_COMPANIES']}{$lid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	$url = $this->apiUrl . 'home/base/?oid=' . $lidMap[$lid] . '&type=1';
    	$content = $this->tools->request($url);
    	$content = json_decode($content, true);
    	if($content['desc'] != 'succ' || $content['code'] != '0000' || empty($content['row']))
    	{
    		return array();
    	}
    	 
    	$data = array();
    	if(!empty($content['row']))
    	{
    		$data = $content['row'];
    	}
    	$this->cache->save($redisKey, json_encode($data), 86400);
    	 
    	return $data;
    }
    
    /*
     * API 获取赛事欧赔亚赔详情
    */
    public function getOddDetail($type, $cid, $mid, $lid)
    {
    	$lidMap = array(
    		'42' => array('o' => '2013', 'y' => '2014'),
    		'43' => array('o' => '5003', 'y' => '5007'),
    	);

    	if(empty($lidMap[$lid][$type]))
    	{
    		return array();
    	}
    	
    	/*$redisKey = "{$this->redis['ODD_DETAIL']}{$lidMap[$lid][$type]}_{$mid}_{$cid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}*/
    
    	$url = $this->apiUrl . 'home/base/?oid=' . $lidMap[$lid][$type] . '&mid=' . $mid . '&comid=' . $cid;
    	$content = $this->tools->request($url);
    	$content = json_decode($content, true);
    	if($content['desc'] != 'succ' || $content['code'] != '0000' || empty($content['row']))
    	{
    		return array();
    	}
    	
    	$data = array();
    	if(!empty($content['row']))
    	{
    		$data = $content['row'];
    	}
    	//$this->cache->save($redisKey, json_encode($data), 10);
    	
    	return $data;
    }
    
    /**
     * 返回篮球即时列表记录
     */
    public function getLqMatchList()
    {
    	$redisKey = "{$this->redis['JCLQ_LIVELIST']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	//查询一天前到未来所有未完结的对阵信息
    	$date = date('Y-m-d', strtotime("-1 day"));
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,hqt,aqt,state from cp_data_lq_matchs FORCE INDEX(mtime_s) where mtime >= ? and state not in('9', '11', '13', '14', '15') and xid > 0 order by mtime asc, xid asc";
    	$result = $this->slaveDc->query($sql, array($date))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 10);
    	
    	return $result;
    }
    
    /**
    * 返回篮球即时列表记录
    */
    public function getZqMatchList()
    {
	    $redisKey = "{$this->redis['JCZQ_LIVELIST']}";
	    $redisData = $this->getRedisData($redisKey);
	    if($redisData)
	    {
	    	return $redisData;
	    }
    	//查询一天前到未来所有未完结的对阵信息
    	$date = date('Y-m-d', strtotime("-1 day"));
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,hqt,aqt,state,scoreState from cp_data_zq_matchs FORCE INDEX(xid) where mtime >= ? and state not in('4', '6', '8', '10', '11', '13') and xid > 0 order by mtime asc, xid asc";
    	$result = $this->slaveDc->query($sql, array($date))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 10);
    	 
    	return $result;
    }
    
    /**
     * 获取竞彩篮球最新消息编号
     * @return mixed
     */
    public function getLqNewMessageId()
    {
    	return $this->getRedisData($this->redis['JCLQ_MESSGEID']);
    }
    
    /**
     * 获取竞彩足球最新消息编号
     * @return mixed
     */
    public function getZqNewMessageId()
    {
    	return $this->getRedisData($this->redis['JCZQ_MESSGEID']);
    }
    
    /**
     * 
     * @param unknown_type $newMsgId
     * @param unknown_type $msgId
     * @return multitype:unknown
     */
    public function getLqLiveScore($newMsgId, $msgId)
    {
    	$datas = array();
    	for($i = $msgId + 1; $i <= $newMsgId; $i++)
    	{
    		$redisKey = $this->redis['JCLQ_MESSGEID'] . $i;
    		$redisData = $this->getRedisData($redisKey);
    		if($redisData)
    		{
    			foreach ($redisData as $value)
    			{
    				$datas[$value['mid']] = $value;
    			}
    		}
    		
    	}
    	
    	return $datas;
    }
    
    /**
     *
     * @param unknown_type $newMsgId
     * @param unknown_type $msgId
     * @return multitype:unknown
     */
    public function getZqLiveScore($newMsgId, $msgId)
    {
    	$datas = array();
    	for($i = $msgId + 1; $i <= $newMsgId; $i++)
    	{
	    	$redisKey = $this->redis['JCZQ_MESSGEID'] . $i;
	    	$redisData = $this->getRedisData($redisKey);
	    	if($redisData)
	    	{
	    		foreach ($redisData as $value)
	    		{
	    			$datas[$value['mid']] = $value;
	    		}
	    	}
    	}
    		 
    	return $datas;
    }
    
    /**
     * 竞彩篮球完结列表查询
     * @param unknown_type $page
     * @param unknown_type $pSize
     */
    public function getLqEndList()
    {
    	$redisKey = "{$this->redis['JCLQ_ENDLIST']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	//查询时间范围
    	$sdate = date('Y-m-d', strtotime("-6 day"));
    	$edate = date('Y-m-d H:i:s');
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,hqt,aqt,state from cp_data_lq_matchs FORCE INDEX(mtime_s) where (mtime BETWEEN ? AND ?) and state in('9', '11', '13', '14', '15') and xid > 0 order by mtime desc, xid desc";
    	$result = $this->slaveDc->query($sql, array($sdate, $edate))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 30);
    	
    	return $result;
    }
    
    /**
     * 竞彩篮球完结列表查询
     */
    public function getZqEndList()
    {
    	$redisKey = "{$this->redis['JCZQ_ENDLIST']}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	//查询时间范围
    	$sdate = date('Y-m-d', strtotime("-6 day"));
    	$edate = date('Y-m-d H:i:s');
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,bc,hqt,aqt,state,scoreState from cp_data_zq_matchs FORCE INDEX(mtime_s) where (mtime BETWEEN ? AND ?) and state in('4', '6', '8', '10', '11', '13') and xid > 0 order by mtime desc, xid desc";
    	$result = $this->slaveDc->query($sql, array($sdate, $edate))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 30);
    	 
    	return $result;
    }
    
    /**
     * 竞彩篮球关注信息
     * @param unknown_type $mids
     * @return unknown
     */
    public function getLqFollow($mids)
    {
    	$midStr = implode(',', $mids);
    	$redisKey = "{$this->redis['JCLQ_FOLLOW']}{$midStr}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	
    	//查询时间范围
    	$sdate = date('Y-m-d', strtotime("-6 day"));
    	$edate = date('Y-m-d H:i:s', strtotime("+5 day"));
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,hqt,aqt,state from cp_data_lq_matchs where (mtime BETWEEN ? AND ?) and mid in ? and xid > 0 order by mtime asc, xid asc";
    	$result = $this->slaveDc->query($sql, array($sdate, $edate, $mids))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 30);
    	 
    	return $result;
    }
    
    /**
     * 竞彩篮球关注信息
     * @param unknown_type $mids
     * @return unknown
     */
    public function getZqFollow($mids)
    {
    	$midStr = implode(',', $mids);
    	$redisKey = "{$this->redis['JCZQ_FOLLOW']}{$midStr}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		return $redisData;
    	}
    	 
    	//查询时间范围
    	$sdate = date('Y-m-d', strtotime("-6 day"));
    	$edate = date('Y-m-d H:i:s', strtotime("+5 day"));
    	$sql = "select xid,mid,home,away,homelogo,awaylogo,htid,atid,mtime,stime,lid,ln,bc,hqt,aqt,state,scoreState from cp_data_zq_matchs where (mtime BETWEEN ? AND ?) and mid in ? and xid > 0 order by mtime asc, xid asc";
    	$result = $this->slaveDc->query($sql, array($sdate, $edate, $mids))->getAll();
        $result = $this->dataFormat($result);
    	$this->cache->save($redisKey, json_encode($result), 30);
    
    	return $result;
    }

    // 字段过滤
    public function dataFormat($match)
    {
        $data = array();
        if(!empty($match))
        {
            foreach ($match as $items) 
            {
                $items['home'] = $this->getShortName($items['home']);
                $items['away'] = $this->getShortName($items['away']);
                $data[] = $items;
            }
        }
        return $data;
    }

    public function getShortName($name)
    {
        return (!empty($this->shortName[$name])) ? $this->shortName[$name] : $name;
    }
}