<?php

class Lottery_Model extends MY_Model
{

    const SFC = 11;
    const RJ = 19;
    const PLS = 33;
    const PLW = 35;
    const BJDC = 41;
    const JCZQ = 42;
    const JCLQ = 43;
    const SSQ = 51;
    const FCSD = 52;
    const QXC = 10022;
    const SYYDJ = 21406;
    const JXSYXW = 21407;
    const HBSYXW = 21408;
    const QLC = 23528;
    const DLT = 23529;
    const GJ = 44;
    const GYJ = 45;
    const KS = 53;
    const KLPK = 54;
    const CQSSC = 55;
    const JLKS = 56;
    const JXKS = 57;
    const GDSYXW = 21421;

    private static $CN_NAMES = array(
        self::SFC => '胜负彩',
        self::RJ => '任九',
        self::PLS => '排列三',
        self::PLW => '排列五',
        self::BJDC => '北京单场',
        self::JCZQ => '竞彩足球',
        self::JCLQ => '竞彩篮球',
        self::SSQ => '双色球',
        self::FCSD => '福彩3D',
        self::QXC => '七星彩',
        self::SYYDJ => '老11选5',
        self::QLC => '七乐彩',
        self::DLT => '大乐透',
    	self::GJ => '冠军彩',
    	self::GYJ => '冠亚军彩',
    	self::JXSYXW => '新11选5',
    	self::KS => '经典快3',
    	self::JLKS => '易快3',
    	self::HBSYXW => '惊喜11选5',
        self::KLPK => '快乐扑克',
        self::CQSSC => '老时时彩',
        self::JLKS => '易快3',
        self::JXKS => '红快3',
        self::GDSYXW => '乐11选5',
    );

    private static $EN_NAMES = array(
        self::SFC => 'sfc',
        self::RJ => 'rj',
        self::PLS => 'pls',
        self::PLW => 'plw',
        self::BJDC => 'bjdc',
        self::JCZQ => 'jczq',
        self::JCLQ => 'jclq',
        self::SSQ => 'ssq',
        self::FCSD => 'fcsd',
        self::QXC => 'qxc',
        self::SYYDJ => 'syxw',
        self::QLC => 'qlc',
        self::DLT => 'dlt',
    	self::GJ => 'gj',
    	self::GYJ => 'gyj',
    	self::JXSYXW => 'jxsyxw',
    	self::KS => 'ks',
    	self::JLKS => 'jlks',
    	self::HBSYXW => 'hbsyxw',
        self::KLPK => 'klpk',
        self::CQSSC => 'cqssc',
        self::JLKS => 'jlks',
        self::JXKS => 'jxks',
        self::GDSYXW => 'gdsyxw',
    );
    
    private static $TB_NAMES = array(
    	self::SFC => 'cp_rsfc_paiqi',
    	self::RJ => 'cp_rsfc_paiqi',
    	self::PLS => 'cp_pl3_paiqi',
    	self::PLW => 'cp_pl5_paiqi',
    	self::BJDC => 'cp_bjdc_paiqi',
    	self::JCZQ => 'cp_jczq_paiqi',
    	self::JCLQ => 'cp_jclq_paiqi',
    	self::SSQ => 'cp_ssq_paiqi',
    	self::FCSD => 'cp_fc3d_paiqi',
    	self::QXC => 'cp_qxc_paiqi',
    	self::SYYDJ => 'cp_syxw_paiqi',
    	self::QLC => 'cp_qlc_paiqi',
    	self::DLT => 'cp_dlt_paiqi',
    	self::JXSYXW => 'cp_jxsyxw_paiqi',
    	self::KS =>'cp_ks_paiqi',
    	self::JLKS =>'cp_jlks_paiqi',
    	self::HBSYXW => 'cp_hbsyxw_paiqi',
        self::KLPK => 'cp_klpk_paiqi',
        self::CQSSC => 'cp_cqssc_paiqi',
        self::JLKS => 'cp_jlks_paiqi',
        self::JXKS => 'cp_jxks_paiqi',
        self::GDSYXW => 'cp_gdsyxw_paiqi',
    );
    
    private static $CACHE_NAMES = array(
    	self::PLS => 'PLS_ISSUE',
    	self::PLW => 'PLW_ISSUE',
    	self::SSQ => 'SSQ_ISSUE',
    	self::FCSD => 'FC3D_ISSUE',
    	self::QXC => 'QXC_ISSUE',
    	self::SYYDJ => 'SYXW_ISSUE_TZ',
    	self::QLC => 'QLC_ISSUE',
    	self::DLT => 'DLT_ISSUE',
    	self::JXSYXW => 'JXSYXW_ISSUE_TZ',
    	self::KS => 'KS_ISSUE_TZ',
    	self::JLKS => 'JLKS_ISSUE_TZ',
    	self::HBSYXW => 'HBSYXW_ISSUE_TZ',
        self::KLPK => 'KLPK_ISSUE_TZ',
        self::CQSSC => 'CQSSC_ISSUE_TZ',
        self::JLKS => 'JLKS_ISSUE_TZ',
        self::JXKS => 'JXKS_ISSUE_TZ',
        self::GDSYXW => 'GDSYXW_ISSUE_TZ',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function getCnName($lotteryId)
    {
        $cnName = '未知';
        if (isset(self::$CN_NAMES[$lotteryId])) {
            $cnName = self::$CN_NAMES[$lotteryId];
        }
        
        return $cnName;
    }

    public function getCnNames()
    {
        return self::$CN_NAMES;
    }
    
    public function getCache($lid = null)
    {
    	if ($lid)
    	{
    		return self::$CACHE_NAMES[$lid];
    	}
    	return self::$CACHE_NAMES;
    }

    public function getEnName($lotteryId)
    {
        $enName = 'unknown';
        if (isset(self::$EN_NAMES[$lotteryId])) 
        {
            $enName = self::$EN_NAMES[$lotteryId];
        }
        
        return $enName;
    }

    public function getEnNames()
    {
        return self::$EN_NAMES;
    }
    
    public function getTbName($lotteryId)
    {
    	$tbName = '未知';
    	if (isset(self::$TB_NAMES[$lotteryId])) {
    		$tbName = self::$TB_NAMES[$lotteryId];
    	}
    
    	return $tbName;
    }

    public function getDetail($lotteryId, $issue = null)
    {
        $enName = $this->getEnName($lotteryId);
        if (in_array($enName, array('rj','sfc')))
        {
        	$issuefield = 'mid';
        }else 
        {
        	$issuefield = 'issue';
        }
        if ($enName == 'pls') 
        {
            $enName = 'pl3';
        } 
        elseif ($enName == 'plw') 
        {
            $enName = 'pl5';
        } 
        elseif ($enName == 'fcsd') 
        {
            $enName = 'fc3d';
        }
        $table = "cp_" . $enName . "_paiqi";
        if (in_array($enName, array('rj','sfc'))) 
        {
        	$table = "cp_rsfc_paiqi";
        }
        
        $sql = "select * from " . $table . " where 1 ";
        if (in_array($lotteryId, array('21406', '21407', '53', '21408', '54', '55', '56', '57', '21421'))) 
        {
            $sql .= " and DATE(sale_time)= ? and delect_flag = 0";
            return $this->slaveCfg->query($sql, array($issue))->getAll();
        } 
        elseif ($issue) 
        {
        	$sql .= " and ".$issuefield."= ? and status >= 50";
        } 
        else 
        {
        	$sql .= " and status >= 50 order by ".$issuefield." desc limit 1";
        }
        return $this->slaveCfg->query($sql,array($issue))->getRow();
    }

    public function getAllIssue($lotteryId, $issue = null)
    {
        $enName = $this->getEnName($lotteryId);
        if ($enName == 'pls') 
        {
            $enName = 'pl3';
        } 
        elseif ($enName == 'plw') 
        {
            $enName = 'pl5';
        } 
        elseif ($enName == 'fcsd') 
        {
            $enName = 'fc3d';
        }
        $table = "cp_" . $enName . "_paiqi";
        $sql = "select issue from " . $table . " where status >=50 ";
        if (in_array($enName, array('rj','sfc')))
        {
            $sql = "select mid as issue from cp_rsfc_paiqi where status >=50";
        }

        if ($issue)
        {
        	$sql .= (in_array($enName, array('rj','sfc')) ? " AND mid >= " : " AND issue >=") . $issue;
        }
        $sql .= " order by issue desc";

        return $this->slaveCfg->query($sql)->getAll();
    }
    
    public function getAwardList($lottery, $num)
    {
    	$table = "cp_" . $lottery . "_paiqi";
    	return $this->slaveCfg->query("select issue, awardNum, bonusDetail from {$table} where status >=50 order by issue desc limit {$num}")->getAll();
    }

    public function getAllAwarddate($lotteryId)
    {
        $enName = $this->getEnName($lotteryId);
        $table = "cp_" . $enName . "_paiqi";
        if(in_array($lotteryId, array(self::SYYDJ, self::JXSYXW, self::KS, self::HBSYXW, self::KLPK, self::CQSSC,self::JLKS,self::JXKS, self::GDSYXW)))
        {
        	$sql = "SELECT DISTINCT DATE(award_time) as date FROM " . $table . " WHERE `status` >= 50 and delect_flag = 0 order by date desc";
        }
        else 
        {
        	$sql = "SELECT DISTINCT DATE(award_time) as date FROM " . $table . " WHERE `status` >= 50 order by date desc";
        }
        return $this->slaveCfg->query($sql)->getAll();
    }

    public function getCurrentIssue($lotteryId)
    {//已售
        $enName = $this->getEnName($lotteryId);
        if ($enName == 'pls')
        {
            $enName = 'pl3';
        }
        elseif ($enName == 'plw')
        {
            $enName = 'pl5';
        }
        elseif ($enName == 'fcsd')
        {
            $enName = 'fc3d';
        }
        $table = "cp_" . $enName . "_paiqi";
        
        if (in_array($enName, array('rj','sfc')))
        {
            $sql = "SELECT * FROM cp_tczq_paiqi WHERE end_sale_time < NOW()  AND `status` >=50 ORDER BY mid DESC LIMIT 1";
        }
        elseif (in_array($enName, array('syxw', 'jxsyxw', 'ks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            $sql = "select * from {$table} where award_time > date_sub(now(), interval 10 day)
            and award_time < now() and delect_flag = 0
            AND end_time<NOW() AND `status` >=50 order by issue desc LIMIT 1";
        }else 
        {
            $sql = "SELECT * FROM " . $table . " WHERE end_time < NOW() and status >= 50 order by issue desc limit 1";
        }
        $result = $this->cfgDB->query($sql)->getRow();
     	if(empty($result) && in_array($enName, array('syxw', 'jxsyxw', 'ks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
        	$sql = "select * from {$table} where DATE(award_time)=date(date_sub(now(), interval 1 day)) AND end_time<NOW() AND `status` >=50 order by issue desc LIMIT 1";
        	$result = $this->cfgDB->query($sql)->getRow();
        }
        return $result;
    }
    
    public function getTeamByIssue($mid)
    {
        $sql = "select home, away from cp_tczq_paiqi where mid= ? and ctype=1 order by mname";
        return $this->cfgDB->query($sql,array($mid))->getAll();
    }

    private function delSyxwCache($mkey, $difkeys)
    {
    	foreach ($difkeys as $key)
    	{
    		$this->cache->hDel($mkey, $key);
    	}
    }
    
    /**
     * 获取投注页信息
     * @param int $lotteryId
     */
	public function getKjinfo($lotteryId)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$enName = $this->getEnName($lotteryId);
    	if ($lotteryId == self::FCSD) 
    	{
    		$enName = 'fc3d';
    	}
    	$str = '';
    	if(in_array($lotteryId, array(self::SYYDJ, self::JXSYXW, self::KS, self::HBSYXW, self::KLPK, self::CQSSC ,self::JLKS, self::JXKS, self::GDSYXW)))
    	{
    		$str = '_TZ';
    	}
    	$current = json_decode($this->cache->get($REDIS[strtoupper($enName).'_ISSUE'.$str]), true);
    	$billion = floor($current['lIssue']['awardPool'] / 100000000);
    	$million = floor(($current['lIssue']['awardPool'] - $billion * 100000000) / 10000);
    	$yuan = $current['lIssue']['awardPool'] % 10000;
    	$res['current'] = array(
    			'pool' => $billion . "|" . $million . "|" . $yuan ."|". floor($current['lIssue']['awardPool'] / 5000000),
    			'issue' => $current['lIssue']['seExpect'],
    			'awardNum' => $current['lIssue']['awardNumber'],
    			'awardTime' => $current['lIssue']['awardTime'],
    			'rStatus' => $current['lIssue']['rStatus']
    	);
    	if (!empty($current['aIssue']))
    	{
    		$res['current']['endTime'] = $current['aIssue']['seEndtime'];
    		$res['current']['seFsendtime'] = $current['aIssue']['seFsendtime'];
    	}
    	if (in_array($lotteryId, array(self::PLS, self::FCSD, self::PLW)))
    	{
    		$bonusDetail = json_decode($current['lIssue']['bonusDetail'], true);
    		if (empty($bonusDetail))
    		{
    			if ($lotteryId !== self::PLW)
    			{
    				$bonusDetail = array(
    					'zx' => array(
	    					'zs' => '---',
	    					'dzjj' => '1040'
	    				),
    					'z3' => array(
    						'zs' => '---',
    						'dzjj' => '346'
    					),
    					'z6' => array(
    						'zs' => '---',
    						'dzjj' => '173'
    					)
    				);
    			}else {
    				$bonusDetail = array(
    					'zx' => array(
	    					'zs' => '---',
	    					'dzjj' => '100000'
	    				)
    				);
    			}
    		}
    		$res['current']['bonusDetail'] = $bonusDetail;
    	}
    	$res['next'] = array(
    			'seEndtime' => $current['cIssue']['seEndtime'],
    			'seFsendtime' => $current['cIssue']['seFsendtime'],
    			'issue' => $current['cIssue']['seExpect']
    	);
    	if(!in_array($lotteryId, array(self::SYYDJ, self::JXSYXW, self::KS, self::HBSYXW, self::KLPK, self::SSQ, self::CQSSC ,self::JLKS, self::JXKS, self::GDSYXW))) {
    		$res['kj'] = unserialize($this->cache->get($REDIS[strtoupper($enName).'_HISTORY']));
    	}
    	return $res;
    }
    
    public function frushKjHistory($lotteryId)
    {
    	$erlin = array(self::DLT, self::QXC, self::PLS, self::PLW);
    	$qian = '';
    	if (in_array($lotteryId, $erlin))
    	{
    		$qian = '20';
    	}
    	$enName = $this->getEnName($lotteryId);
    	if ($lotteryId == self::FCSD)
    	{
    		$enName = 'fc3d';
    	}
    	$table = $this->getTbName($lotteryId);
    	$sql = "select * from ".$table." WHERE end_time < NOW() and DATE_SUB(NOW(),INTERVAL 1 MONTH) and status >= 50 order by issue desc limit 1,5";
    	$result = $this->cfgDB->query($sql)->getAll();
    	foreach ($result as $value)
    	{
    		$res[] = array(
    				'issue' => $qian.$value['issue'],
    				'awardNum' => str_replace(array('(', ')'), array('|', ''), $value['awardNum'])
    		);
    	}
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS[strtoupper($enName).'_HISTORY'], serialize($res), 0);
    }
    
    public function freshGaopin($lid, $date = null)
    {
    	$dateList = $this->getAllAwarddate($lid);
    	$date = $date ? $date : $dateList[0]['date'];
    	$data = $this->getDetail($lid, $date);
    	$count = 0;
    	foreach ($data as $val)
    	{
    		if ($val['status'] >= 50)
    			$count++;
    	}
    	$current = $this->getCurrentIssue($lid);
    	//$next = $this->getNextIssue(21406);
    	$cdata = array('data' => $data, 'dateList' => $dateList, 'date' => $date,
    			'current' => $current, 'count' => $count, 'issue' => $date);
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS[strtoupper($this->getEnName($lid)).'_KJ'], serialize($cdata), 0);
    }
    
    public function getHistory($lotteryId) {
    	$erlin = array(DLT, QXC, PLS, PLW);
    	$qian = '';
    	if (in_array($lotteryId, $erlin)) {
    		$qian = '20';
    	}
    	$enName = $this->getEnName($lotteryId);
    	if ($lotteryId == FCSD) {
    		$enName = 'fc3d';
    	}
    	$table = $this->getTbName($lotteryId);
    	$sql = "select * from ".$table." WHERE end_time < NOW() and DATE_SUB(NOW(),INTERVAL 1 MONTH) and status >= 50 order by issue desc limit 1,5";
    	$result = $this->cfgDB->query($sql)->getAll();
    	foreach ($result as $value) {
    		$res[] = array(
    			'issue' => $qian.$value['issue'],
    			'awardNum' => str_replace(array('(', ')'), array('|', ''), $value['awardNum'])
    		);
    	}
    	return $res;
    }

    //刷新开奖信息缓存
    public function refreshSyxwAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_syxw_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['SYXW_AWARD'], serialize($awards), 0);
        } 
    }
    
    //刷新开奖信息缓存
    public function refreshJxSyxwAwards()
    {
    	$sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_jxsyxw_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
    	$awards = $this->cfgDB->query($sql)->getAll();
    	if(!empty($awards))
    	{
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		$REDIS = $this->config->item('REDIS');
    		$this->cache->save($REDIS['JXSYXW_AWARD'], serialize($awards), 0);
    	}
    }
    
    //刷新开奖信息缓存
    public function refreshHbSyxwAwards()
    {
    	$sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_hbsyxw_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
    	$awards = $this->cfgDB->query($sql)->getAll();
    	if(!empty($awards))
    	{
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		$REDIS = $this->config->item('REDIS');
    		$this->cache->save($REDIS['HBSYXW_AWARD'], serialize($awards), 0);
    	}
    }
    
    //刷新开奖信息缓存
    public function refreshGdSyxwAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_gdsyxw_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['GDSYXW_AWARD'], serialize($awards), 0);
        }
    }
    
    //刷新开奖信息缓存
    public function refreshKsAwards()
    {
    	$sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_ks_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
    	$awards = $this->cfgDB->query($sql)->getAll();
    	if(!empty($awards))
    	{
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		$REDIS = $this->config->item('REDIS');
    		$this->cache->save($REDIS['KS_AWARD'], serialize($awards), 0);
    	}
    }
    //刷新易快3开奖信息缓存
    public function refreshJlksAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_jlks_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['JLKS_AWARD'], serialize($awards), 0);
        }
    }
    
    //刷新红快3开奖信息缓存
    public function refreshJxksAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_jxks_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['JXKS_AWARD'], serialize($awards), 0);
        }
    }
    
    //刷新开奖信息缓存
    public function refreshKlpkAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_klpk_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 100";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['KLPK_AWARD'], serialize($awards), 0);
        }
    }

    //刷新开奖信息缓存
    public function refreshCqsscAwards()
    {
        $sql = "select issue,sale_time,end_time,award_time,awardNum,sale,pool from cp_cqssc_paiqi where 1 and status >= 50 and delect_flag = 0 order by issue desc limit 120";
        $awards = $this->cfgDB->query($sql)->getAll();
        if(!empty($awards))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['CQSSC_AWARD'], serialize($awards), 0);
        }
    }
    
    public function getAllAwards() 
    {
    	$lidMap = array(
    			SSQ => array('cache' => 'SSQ_ISSUE'),
    			FCSD => array('cache' => 'FC3D_ISSUE'),
    			PLS => array('cache' => 'PLS_ISSUE'),
    			PLW => array('cache' => 'PLW_ISSUE'),
    			QXC => array('cache' => 'QXC_ISSUE'),
    			QLC => array('cache' => 'QLC_ISSUE'),
    			DLT => array('cache' => 'DLT_ISSUE'),
    			SFC => array('cache' => 'SFC_ISSUE'),
    			RJ => array('cache' => 'RJ_ISSUE'),
    			SYXW => array('cache' => 'SYXW_ISSUE_TZ'),
    	);
    	$issueData = array();
    	$issues = array();
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	foreach ($lidMap as $lid)
    	{
    		$caches = $this->cache->get($REDIS[$lid['cache']]);
    		$caches = json_decode($caches, true);
    		array_push($issues, $caches);
    	}
    	foreach ($issues as $k => $issue) {
    		$issue['lIssue']['pool']['b'] = floor($issue['lIssue']['awardPool'] / 100000000);
    		$issue['lIssue']['pool']['m'] = floor(($issue['lIssue']['awardPool'] - $issue['lIssue']['pool']['b'] * 100000000) / 10000);
    		$d = date('d', $issue['cIssue']['seFsendtime']/1000);
    		$issue['jrkj'] = 0;
    		if ($d == date('d')){
    			$issue['jrkj'] = 1;
    		}
    		$issueData[$issue['cIssue']['seLotid']] = $issue;
    	}
    	return $issueData;
    }
    
    public function getDataByIssue($lid, $issue, $fields = null) {
    	if (empty($fields)) {
    		$fields = 'award_time, awardNum';
    	}
    	return $this->cfgDB->query("select ".$fields." from ".$this->getTbName($lid)." where issue = ?", array($issue))->getRow();
    }
    /**
     * 获取采种配置
     * @param int $lid
     * @param string $filed
     * @return array
     */
    public function getLotteryConfig($lid, $filed = '')
    {
        if (!$filed)
        {
            $filed = '*';
        }
        $sql = "select {$filed} from cp_lottery_config where lottery_id='{$lid}'";
        $date = $this->slaveCfg->query($sql)->getRow();
        return $date;
    }

}
