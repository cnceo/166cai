<?php

class Award_Model extends MY_Model {

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
    
    const KS = 53;

    const KLPK = 54;

    const CQSSC = 55;

    const JLKS = 56;
    
    const JXKS = 57;
    
    const GDSYXW = 21421;

    private $lidMap = array(
        '51' => array('cache' => 'SSQ_ISSUE', 'missNum' => 'SSQ_MISS'),
        '52' => array('cache' => 'FC3D_ISSUE', 'missNum' => 'DLT_MISS'),
        '33' => array('cache' => 'PLS_ISSUE'),
        '35' => array('cache' => 'PLW_ISSUE'),
        '10022' => array('cache' => 'QXC_ISSUE', 'missNum' => 'QXC_MISS'),
        '23528' => array('cache' => 'QLC_ISSUE', 'missNum' => 'QLC_MISS'),
        '23529' => array('cache' => 'DLT_ISSUE'),
        '11' => array('cache' => 'SFC_ISSUE'),
        '19' => array('cache' => 'RJ_ISSUE'),
        '21406' => array('cache' => 'SYXW_ISSUE_TZ', 'missNum' => 'SYXW_MISS'),
        '21407' => array('cache' => 'JXSYXW_ISSUE_TZ', 'missNum' => 'JXSYXW_MISS_MORE'),
        '21408' => array('cache' => 'HBSYXW_ISSUE_TZ', 'missNum' => 'HBSYXW_MISS_MORE'),
    	'53' => array('cache' => 'KS_ISSUE_TZ', 'missNum' => 'KS_MISS_MORE'),
        '54' => array('cache' => 'KLPK_ISSUE_TZ', 'missNum' => 'KLPK_MISS_MORE'),
        '55' => array('cache' => 'CQSSC_ISSUE_TZ', 'missNum' => 'CQSSC_MISS_MORE'),
        '56' => array('cache' => 'JLKS_ISSUE_TZ', 'missNum' => 'JLKS_MISS_MORE'),
        '57' => array('cache' => 'JXKS_ISSUE_TZ', 'missNum' => 'JXKS_MISS_MORE'),
        '21421' => array('cache' => 'GDSYXW_ISSUE_TZ', 'missNum' => 'GDSYXW_MISS_MORE'),
    );

    // 数据库对应表名
    private static $TB_NAMES = array(
        self::SFC => 'rsfc',
        self::RJ => 'rsfc',
        self::PLS => 'pl3',
        self::PLW => 'pl5',
        self::BJDC => 'bjdc',
        self::JCZQ => 'jczq',
        self::JCLQ => 'jclq',
        self::SSQ => 'ssq',
        self::FCSD => 'fc3d',
        self::QXC => 'qxc',
        self::SYYDJ => 'syxw',
        self::JXSYXW => 'jxsyxw',
        self::HBSYXW => 'hbsyxw',
        self::QLC => 'qlc',
        self::DLT => 'dlt',
    	self::KS => 'ks',
        self::JLKS => 'jlks',
        self::KLPK => 'klpk',
        self::CQSSC => 'cqssc',
        self::JXKS => 'jxks',
        self::GDSYXW => 'gdsyxw',
    );

    public function __construct() {
        parent::__construct();
        $this->load->helper('string');
        $this->load->library('BetCnName');
        $this->load->library('libcomm');
        $this->load->model('lottery_model', 'Lottery');
    }

    public function getLastByQihui()
    {
        $awards = array();
        $REDIS = $this->config->item('REDIS');
        $awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/pl', 
        array('cache_2345caipiao' => $REDIS['CACHE_TYPE']['ticket_data_pl0']));
        if ($awardResponse['code'] == 0) 
        {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }
    
    /*
     * 获取所有彩种最新期次的开奖信息
     * @date:2015-08-11
     */
    public function getLastByDcenter()
    {
    	$awards = array();
        $lotteryInfo = $this->lidMap;
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        foreach ($lotteryInfo as $lid => $items) 
        {
            $caches = $this->cache->get($REDIS[$items['cache']]);
            $caches = json_decode($caches, true);
            if(!empty($caches['lIssue']))
            {
                array_push($awards, $caches['lIssue']);         
            }
        }
      
        return $awards;
    }

    /*
     * 分页查询指定彩种的开奖列表（已废弃）
     * @param $lotteryId 彩种id
     * @param $state 期号状态 100：可售 201：已开奖
     * @return array
     */
    public function getNumberByQihui($lotteryId, $pn, $ps = 10, $state ='') {
        $awards = array();
        $awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/il', array(
            'lid' => $lotteryId,
            'state' => $state,
            'pn' => $pn,
            'ps' => $ps,
        ));
        if ($awardResponse['code'] == 0) 
        {
            $res = $awardResponse['data'];
            if (!empty($res['items'])) 
            {
            	$awards = $res['items'];
            }
        }

        return $awards;
    }
    
    public function getNumberByDcenter($lotteryId, $pn, $ps = 10, $state ='')
    {
        // $state 期次遗漏
    	$awards = $this->getAwardListByDcenter($lotteryId, $missNum = 0, $pn, $ps);
    	return $awards;
    }

    //已废弃
    public function getJczqByQihui($lotteryId, $issue, $state) {
        $matches = array();
        $awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/jil', array(
            'lid' => $lotteryId,
            'state' => $state,
            'issue' => $issue,
            'pre_issue' => 1,
        ));
        if ($awardResponse['code'] == 0) 
        {
            $awards = $awardResponse['data'];
            $tmp = array();
            foreach ($awards as  $val)
            {
            	array_unshift($tmp, $val); //重新排序
            }
            foreach ($tmp as $match)
            {
            	if (count(explode(':', $match['score'])) != 2)
            	{
            		continue;
            	}
            	list($homeScore, $awayScore) = explode(':', $match['score']);
            	list($homeHalfScore, $awayHalfScore) = explode(':', $match['scoreHalf']);
            	if ($homeScore > $awayScore)
            	{
            		$match['spf'] = '胜';
            	} 
            	else if ($homeScore == $awayScore) 
            	{
            		$match['spf'] = '平';
            	} 
            	else 
            	{
            		$match['spf'] = '负';
            	}
            	if ($homeScore + $match['let'] > $awayScore) 
            	{
            		$match['rqspf'] = '胜';
            	} 
            	else if ($homeScore + $match['let'] == $awayScore) 
            	{
            		$match['rqspf'] = '平';
            	} 
            	else 
            	{
            		$match['rqspf'] = '负';
            	}
            	if ($homeHalfScore > $awayHalfScore) 
            	{
            		$match['bqc'] = '胜-' . $match['spf'];
            	} 
            	else if ($homeHalfScore == $awayHalfScore) 
            	{
            		$match['bqc'] = '平-' . $match['spf'];
            	} 
            	else 
            	{
            		$match['bqc'] = '负-' . $match['spf'];
            	}
            	$match['jqs'] = $homeScore + $awayScore;
            
            	//拆分获取 日期 + 赛事编号
            	$matchDate = getWeekByTime(strtotime($match['issue']));
            	$match['matchId'] = $matchDate.substr($match['mid'], 8);
            	$matches[$match['issue']][] = $match;
            }
        }

        return $matches;
    }

    // 获取最近一期有数据的日期
    public function getLastJczqDate()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $date = $this->cache->redis->get($ukey);

        if(empty($date))
        {
            $date = $this->refreshLastJczqDate();
        }

        return $date;
    }

    // 刷新最近一期有数据的日期
    public function refreshLastJczqDate()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT date_format(max(m_date), '%Y%m%d') FROM cp_jczq_paiqi WHERE `status` >= 50;";

        $date  = $this->slaveCfg->query($sql)->getOne();

        if(!empty($date ))
        {       
            $this->cache->save($ukey, $date , 600);
        }
        return $date;
    }
    
    /*
    * 竞彩足球历史开奖 【每期十分钟缓存】
    * @param $lotteryId 彩种id
    * @param $issue 期号
    * @return array
    */
    public function getJczqByDcenter($lotteryId, $date, $state)
    {
    	$matches = array();

        // 获取缓存数据
        $awards = $this->getJczqAwardCache($date);

        if(!empty($awards))
        {
            foreach ($awards as $in => $items) 
            {
                $match = array();
                $match['issue'] = $items['m_date'];
                $match['score'] = $items['full_score'];
                $match['mid'] = $items['mid'];
                $match['homeSname'] = $items['home'];
                $match['awary'] = $items['away'];
                $match['awarySname'] = $items['away'];
                $match['scoreHalf'] = $items['half_score'];
                $match['home'] = $items['home'];
                $match['name'] = $items['league'];
                $match['let'] = $items['rq'];
                $match['showDetail'] = $items['showDetail'];
                list($homeScore, $awayScore) = explode(':', $items['full_score']);
                list($homeHalfScore, $awayHalfScore) = explode(':', $items['half_score']);
                // 胜平负
                if ($homeScore > $awayScore)
                {
                    $match['spf'] = '胜';
                } 
                else if ($homeScore == $awayScore) 
                {
                    $match['spf'] = '平';
                } 
                else 
                {
                    $match['spf'] = '负';
                }
                // 让球胜平负
                if ($homeScore + $items['rq'] > $awayScore) 
                {
                    $match['rqspf'] = '胜';
                } 
                else if ($homeScore + $items['rq'] == $awayScore) 
                {
                    $match['rqspf'] = '平';
                } 
                else 
                {
                    $match['rqspf'] = '负';
                }
                // 半全场
                if ($homeHalfScore > $awayHalfScore) 
                {
                    $match['bqc'] = '胜-' . $match['spf'];
                } 
                else if ($homeHalfScore == $awayHalfScore) 
                {
                    $match['bqc'] = '平-' . $match['spf'];
                } 
                else 
                {
                    $match['bqc'] = '负-' . $match['spf'];
                }
                // 总进球
                $match['jqs'] = $homeScore + $awayScore;
                // 拆分获取 日期 + 赛事编号
                $matchDate = getWeekByTime(strtotime($match['issue']));
                $match['matchId'] = $matchDate.substr($match['mid'], 8);

                $matches[$match['issue']][] = $match;
            }
        }

    	return $matches;
    }

    /*
     * 获取竞彩足球开奖 期次 缓存
     * @version:V1.2
     * @date:2015-08-25
     */
    public function getJczqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $awards = $this->cache->redis->get($ukey);
        $awards = json_decode($awards, true);

        if(empty($awards))
        {
            $awards = $this->refreshJczqAwardCache($date);
        }

        return $awards;
    }

    /*
     * 刷新竞彩足球开奖 期次 缓存
     * @version:V1.2
     * @date:2015-08-25
     */
    public function refreshJczqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT p.mid, p.m_date, p.mname, p.league, p.home, p.away, p.end_sale_time, p.rq, p.half_score, p.full_score, p.m_status,
            p.status, p.is_open, p.sale_status, p.show_end_time, IF(d.mid > 0, 1, 0) as showDetail 
            FROM cp_jczq_paiqi AS p LEFT JOIN cp_jczq_detail AS d ON p.mid = d.mid
            WHERE p.m_date = $date AND p.status >= 50 GROUP BY p.mid 
            ORDER BY p.mid ASC;";

        $awards = $this->slaveCfg->query($sql)->getAll();

        if(!empty($awards))
        {       
            $this->cache->save($ukey, json_encode($awards), 600);
        }
        return $awards;
    }
    
    //已废弃
    public function getJclqByQihui($lotteryId, $issue, $state) 
    {
    	$matches = array();
    	$awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/jil', array(
    			'lid' => $lotteryId,
    			'state' => $state,
    			'issue' => $issue,
    			'pre_issue' => 1,
    	));
    	if ($awardResponse['code'] == 0)
    	{
    		$awards = $awardResponse['data'];
    		$tmp = array();
    		foreach ($awards as  $val)
    		{
    			array_unshift($tmp, $val); //重新排序
    		}
    		foreach ($tmp as $key => $match) 
    		{
    			$mid = $match['mid'];
    			if (count(explode(':', $match['score'])) != 2) 
    			{
    				continue;
    			}
    			list($awayScore, $homeScore) = explode(':', $match['score']);
    			$preScore = $match['preScore'];
    			if ($homeScore > $awayScore) 
    			{
    				$match['sf'] = '主胜';
    			} 
    			else if ($homeScore == $awayScore) 
    			{
    				$match['sf'] = '平';
    			} 
    			else 
    			{
    				$match['sf'] = '主负';
    			}
    			if ($homeScore + $match['let'] > $awayScore) 
    			{
    				$match['rfsf'] = '主胜';
    			} 
    			else if ($homeScore == $awayScore) 
    			{
    				$match['rfsf'] = '平';
    			} 
    			else 
    			{
    				$match['rfsf'] = '主负';
    			}
    			$sfc = '';
    			$gap = 0;
    			if ($homeScore >= $awayScore) 
    			{
    				$sfc = '主胜';
    				$gap = $homeScore - $awayScore;
    			} 
    			else 
    			{
    				$sfc = '客胜';
    				$gap = $awayScore - $homeScore;
    			}
    			if ($gap >= 1 && $gap <= 5) 
    			{
    				$sfc .= '1-5';
    			} 
    			else if ($gap >= 6 && $gap <= 10) 
    			{
    				$sfc .= '6-10';
    			} 
    			else if ($gap >= 11 && $gap <= 15) 
    			{
    				$sfc .= '11-15';
    			} 
    			else if ($gap >= 16 && $gap <= 20) 
    			{
    				$sfc .= '16-20';
    			} 
    			else if ($gap >= 21 && $gap <= 25) 
    			{
    				$sfc .= '21-25';
    			} 
    			else if ($gap >= 26) 
    			{
    				$sfc .= '26+';
    			}
    			$match['sfc'] = $sfc;
    			$match['dxf'] = ($homeScore + $awayScore > $preScore) ? '大分' : '小分';
    		
    			//拆分获取 日期 + 赛事编号
    			$matchDate = getWeekByTime(strtotime($match['issue']));
    			$match['matchId'] = $matchDate.substr($match['mid'], 8);
    			$matches[$match['issue']][] = $match;
    		}
    	}
    	
    	return $matches;
    }

    // 获取最近一期有数据的日期
    public function getLastJclqDate()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $date = $this->cache->redis->get($ukey);

        if(empty($date))
        {
            $date = $this->refreshLastJclqDate();
        }

        return $date;
    }

    // 刷新最近一期有数据的日期
    public function refreshLastJclqDate()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT date_format(max(m_date), '%Y%m%d') FROM cp_jclq_paiqi WHERE `status` >= 50;";

        $date  = $this->slaveCfg->query($sql)->getOne();

        if(!empty($date ))
        {       
            $this->cache->save($ukey, $date , 600);
        }
        return $date;
    }
    
    public function getJclqByDcenter($lotteryId, $date, $state)
    {
    	$matches = array();

        // 获取缓存数据
        $awards = $this->getJclqAwardCache($date);

        if(!empty($awards))
        {
            foreach ($awards as $in => $items) 
            {
                $match = array();
                $match['issue'] = $items['m_date'];
                $match['score'] = $items['full_score'];
                $match['mid'] = $items['mid'];
                $match['home'] = $items['home'];
                $match['awary'] = $items['away'];
                $match['name'] = $items['league'];
                $match['let'] = $items['rq'];
                $match['showDetail'] = $items['showDetail'];
                list($awayScore, $homeScore) = explode(':', $match['score']);
                $preScore = $items['preScore'];
                if ($homeScore > $awayScore) 
                {
                    $match['sf'] = '主胜';
                } 
                else if ($homeScore == $awayScore) 
                {
                    $match['sf'] = '平';
                } 
                else 
                {
                    $match['sf'] = '主负';
                }
                if ($homeScore + $match['let'] > $awayScore) 
                {
                    $match['rfsf'] = '主胜';
                } 
                else if ($homeScore == $awayScore) 
                {
                    $match['rfsf'] = '平';
                } 
                else 
                {
                    $match['rfsf'] = '主负';
                }
                $sfc = '';
                $gap = 0;
                if ($homeScore >= $awayScore) 
                {
                    $sfc = '主胜';
                    $gap = $homeScore - $awayScore;
                } 
                else 
                {
                    $sfc = '客胜';
                    $gap = $awayScore - $homeScore;
                }
                if ($gap >= 1 && $gap <= 5) 
                {
                    $sfc .= '1-5';
                } 
                else if ($gap >= 6 && $gap <= 10) 
                {
                    $sfc .= '6-10';
                } 
                else if ($gap >= 11 && $gap <= 15) 
                {
                    $sfc .= '11-15';
                } 
                else if ($gap >= 16 && $gap <= 20) 
                {
                    $sfc .= '16-20';
                } 
                else if ($gap >= 21 && $gap <= 25) 
                {
                    $sfc .= '21-25';
                } 
                else if ($gap >= 26) 
                {
                    $sfc .= '26+';
                }
                $match['sfc'] = $sfc;
                $match['dxf'] = ($homeScore + $awayScore > $preScore) ? '大分' : '小分';
            
                //拆分获取 日期 + 赛事编号
                $matchDate = getWeekByTime(strtotime($match['issue']));
                $match['matchId'] = $matchDate.substr($match['mid'], 8);
                $matches[$match['issue']][] = $match;
            }
        }

    	return $matches;
    }

    /*
     * 获取竞彩篮球开奖 期次 缓存
     * @version:V1.2
     * @date:2015-08-25
     */
    public function getJclqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $awards = $this->cache->redis->get($ukey);
        $awards = json_decode($awards, true);

        if(empty($awards))
        {
            $awards = $this->refreshJclqAwardCache($date);
        }

        return $awards;
    }

    /*
     * 刷新竞彩篮球开奖 期次 缓存
     * @version:V1.2
     * @date:2015-08-25
     */
    public function refreshJclqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT p.mid, p.m_date, p.mname, p.league, p.home, p.away, p.begin_time, p.rq, p.preScore, p.full_score, p.m_status, p.status,
            p.is_open, p.sale_status, p.show_end_time, IF(d.mid > 0, 1, 0) as showDetail
            FROM cp_jclq_paiqi AS p LEFT JOIN cp_jclq_detail AS d ON p.mid = d.mid 
            WHERE p.m_date = $date AND p.status >= 50 GROUP BY p.mid 
            ORDER BY p.mid ASC;";

        $awards = $this->slaveCfg->query($sql)->getAll();

        if(!empty($awards))
        {       
            $this->cache->save($ukey, json_encode($awards), 600);
        }
        return $awards;
    }
    
    /*
    * 普通彩果指定期号【投注】内容（已废弃）
    * @param $lotteryId 彩种id
    * @param $issue 期号
    * @return array
    */
    public function getIssueInfoByQihui($lotteryId, $issue)
    {
    	$awards = array();
    	$awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/il', array(
    			'lid' => $lotteryId,
    			'issue' => $issue,
    	));
    	if ($awardResponse['code'] == 0) {
    		$awards = $awardResponse['data'];
    	}
    
    	return $awards;
    }
    
    /*
    * 普通彩果指定期号【开奖】详情（已废弃）
    * @param $lotteryId 彩种id
    * @param $issue 期号
    * @return array
    */
    public function getAwardDetailByQihui($lotteryId, $issue)
    {
    	$awards = array();
    	if(empty($lotteryId) || empty($issue))
    	{
    		return $awards;
    	}
    	$awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/awardDetail', array(
    			'lid' => $lotteryId,
    			'issue' => $issue,
    	));
    	if ($awardResponse['code'] == 0) {
    		$awards['base'] = $this->getIssueInfoByQihui($lotteryId, $issue);
    		$awards['detail'] = $awardResponse['data'];
    		if(in_array($lotteryId, array(Lottery_Model::SFC, Lottery_Model::RJ)))
    		{
    			$awards['matchs'] = $this->getMatchByByQihui($lotteryId, $issue);
    		}
    		
    	}
    
    	return $awards;
    }

    /*
     * 分页查询指定【数字彩】彩种【开奖】信息
     * @version:V1.2
     * @date:2015-08-18
     */
    public function getAwardListByDcenter($lotteryId, $missNum, $page, $number = 10)
    {   
        // 对应表名   
        $enName = $this->getTBName($lotteryId);

        $table = "cp_" . $enName . "_paiqi";

        if(in_array($lotteryId, array('11', '19')))
        {
            $sql = "SELECT s.mid, s.result, s.rj_sale, s.sfc_sale, s.award, s.award_detail, t.start_sale_time, t.end_sale_time, MAX(t.begin_date) as begin_date, t.show_end_time FROM {$table} as s LEFT JOIN cp_tczq_paiqi as t ON s.mid = t.mid WHERE 1 AND s.mid >= 15051 AND s.status >= 50 AND t.ctype = 1 GROUP BY s.mid order by s.mid desc limit " . ($page - 1) * $number . ", $number";
        }
        else
        {
            $sql = "select * from " . $table . " where 1 and status >= 50 ";
            // 双色球、大乐透、福彩3D、排列三、排列五 只展示 15051期 之后的数据
            if(in_array($lotteryId, array('51','52')))
            {
                $sql .= "and issue >= '2015001' ";
            }
            elseif(in_array($lotteryId, array('23529','33','35')))
            {
                $sql .= "and issue >= '15001' ";
            }
            elseif(in_array($lotteryId, array('21406', '21407', '21408', '21421')))
            {
                $sql .= "and issue >= '150630' ";
            }
            elseif (in_array($lotteryId, array('53', '56', '57')))
            {
            	$sql .= "and issue >= '20160624021'";
            }
            
            $sql .= "order by issue desc limit " . ($page - 1) * $number . ", $number";
        }       
        $awards = $this->slaveCfg->query($sql)->getAll();

        // 格式化数据
        $awards = $this->getAwardListInfo($lotteryId, $awards, $missNum);

        return $awards;
    }

    /*
     * 分页查询指定【开奖】信息 格式化
     * @version:V1.2
     * @date:2015-08-19
     */
    public function getAwardListInfo($lotteryId, $awards, $missNum)
    {
        $awardList = array();
        if(!empty($awards))
        {
            if(in_array($lotteryId, array('11', '19')))
            {
                foreach ($awards as $key => $items) 
                {
                    $awardList[$key]['seExpect'] = $items['mid'];
                    $awardList[$key]['awardNumber'] = str_replace(array('|','(', ')'), array(':', ':', ''), $items['result']);
                    $awardList[$key]['seLotid'] = $lotteryId;
                    $awardList[$key]['seEndtime'] = strtotime($items['end_sale_time'])*1000;
                    $awardList[$key]['seFsendtime'] = strtotime($items['show_end_time'])*1000;
                    $awardList[$key]['awardTime'] = strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($items['begin_date'])))) * 1000;
                    $awardList[$key]['seDsendtime'] = strtotime($items['end_sale_time'])*1000;
                    $awardList[$key]['seAddtime'] = strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($items['begin_date'])))) * 1000;
                }
            }
            else
            {
                foreach ($awards as $key => $items) 
                {
                    $awardList[$key]['seExpect'] = $items['issue'];
                    $awardList[$key]['awardNumber'] = str_replace(array('|','(', ')'), array(':', ':', ''), $items['awardNum']);
                    $awardList[$key]['seLotid'] = $lotteryId;
                    $awardList[$key]['seEndtime'] = strtotime($items['end_time'])*1000;
                    $awardList[$key]['seFsendtime'] = strtotime($items['show_end_time'])*1000;
                    $awardList[$key]['awardTime'] = strtotime($items['award_time'])*1000;
                    $awardList[$key]['seDsendtime'] = strtotime($items['end_time'])*1000;
                    $awardList[$key]['seAddtime'] = strtotime($items['sale_time'])*1000;
                }
            }
        }

        return $awardList;
    }

    /*
     * 数字彩最近【十期】遗漏号码
     * @version:V1.2
     * @date:2015-08-24
     */
    public function getMissNumber($lotteryId, $issue, $awardInfo, $key)
    {
        $missNum = array();
        $lotteryInfo = $this->lidMap;
        if(isset($lotteryInfo[$lotteryId]['missNum']))
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $missInfo = $this->cache->get($REDIS[$lotteryInfo[$lotteryId]['missNum']]);
            $missInfo = in_array($lotteryId, array('53', '54', '56', '57')) ? json_decode($missInfo, true) : unserialize($missInfo);
            // unset($missInfo[15082433]);
            if(!empty($missInfo[$issue]))
            {
                $missNum = $this->missFormat($lotteryId, $missInfo[$issue]);
            }
            else
            {
                // 计算遗漏
                $missNum = $this->calMissNum($lotteryId, $missInfo, $issue, $awardInfo, $key);
                $missNum = $this->missFormat($lotteryId, $missNum);
            }
        }
        return $missNum;
    }

    /*
     * 数字彩最近【十期】遗漏号码 格式处理
     * @version:V1.2
     * @date:2015-08-24
     */
    public function missFormat($lotteryId, $missNum)
    {
        $missData = array();

        if(empty($missNum))
        {
            return $missData;
        }

        // 十一选五
        if($lotteryId == '21406' || $lotteryId == '21407' || $lotteryId == '21408' || $lotteryId == '21421')
        {
            $keyName = array('renxuan','qian1_zhixuan','qian2_zhixuan','qian3_zhixuan','qian2_zuxuan','qian3_zuxuan');

            foreach ($missNum as $key => $items) 
            {
                $missData[$keyName[$key]] = $items;
            }
        }
        elseif(in_array($lotteryId, array('53','56', '57')))
        {
        	$keyName = array('jiben', 'hz', 'sthtx', 'sthdx', 'sbth', 'slhtx', 'ethfx', 'ethdx', 'ebth', 'hz_xingtai', 'hm_xingtai', 'kuadu');
        	$missNum = explode('|', $missNum[0]);
        	foreach ($missNum as $key => $items)
        	{
        		$missData[$keyName[$key]] = $items;
        	}
        	
        }
        elseif($lotteryId == '23528')
        {
            $missData[0] = $missNum;
        }
        elseif (in_array($lotteryId, array('54')))
        {
            $keyName = array('renxuan', 'duizi', 'tonghua', 'shunzi', 'tonghuashun', 'baozi', 'baoxuan');
            foreach ($missNum as $key => $items)
            {
                $missData[$keyName[$key]] = $items;
            }
        }
        elseif (in_array($lotteryId, array('55')))
        {
            $zhixArr = explode('|', $missNum[0]);
            $dxdsArr = explode('|', $missNum[6]);
            $missData = array(
                'gewei'       =>  $zhixArr[4] ? $zhixArr[4] : '',  
                'shiwei'      =>  $zhixArr[3] ? $zhixArr[3] : '',  
                'baiwei'      =>  $zhixArr[2] ? $zhixArr[2] : '',  
                'qianwei'     =>  $zhixArr[1] ? $zhixArr[1] : '',  
                'wanwei'      =>  $zhixArr[0] ? $zhixArr[0] : '',  
                'exzhux'        =>  $missNum[4] ? $missNum[4] : '',
                'sxzhux'        =>  $missNum[2] ? $missNum[2] : '',
                'dxds_ge'       =>  $dxdsArr[4] ? $dxdsArr[4] : '',
                'dxds_shi'      =>  $dxdsArr[3] ? $dxdsArr[3] : '',
                'sx_xingtai'    =>  $missNum[7] ? $missNum[7] : '',
            );
        }
        else
        {
            if(in_array($lotteryId, array('51', '23529')))
            {
                $missNum = explode('|', $missNum);
            }
            foreach ($missNum as $key => $items) 
            {
                $keyName = $this->libcomm->getTypeName($lotteryId, $key);
                $missData[$keyName] = $items;
            }
        }
        return $missData;
    }

    /*
     * 根据上一期开奖 计算本期遗漏数据
     * @version:V1.2
     * @date:2015-08-24
     */
    public function calMissNum($lotteryId, $missInfo, $issue, $awardInfo, $key)
    {
        $missNum = array();

        // 本期开奖号码
        $awardNumber = $awardInfo[$key]['awardNumber'];

        $key ++;
        // 获取上一期的期号
        $lastIssue = $awardInfo[$key]['seExpect'];

        if(!empty($lastIssue))
        {
            if($lotteryId == '21406' || $lotteryId == '21407' || $lotteryId == '21408' || $lotteryId == '21421')
            {
                // 上期遗漏数据是否存在缓存
                if(!empty($missInfo[$lastIssue]))
                {   
                    $ballAmount = $this->getBallAmount($lotteryId);
                    $count = count($ballAmount);

                    foreach ($missInfo[$lastIssue] as $playType => $countStr)
                    {
                        $tmpAry = explode(',', $countStr);
                        $c = count($tmpAry);
                        for ($i = 0; $i < $c; $i ++)
                        {
                            $missedCounterAry[$playType][$i + 1] = intval($tmpAry[$i]);
                        }
                    }

                    // 初始化数据源格式
                    $matches[1] = $awardNumber;
                    $numberAry = explode(',', $awardNumber);
                    $matches[2] = $numberAry[0];
                    $matches[3] = $numberAry[1];
                    $matches[4] = $numberAry[2];
                    $matches[5] = implode(',', array($numberAry[0], $numberAry[1]));
                    $matches[6] = implode(',', array($numberAry[0], $numberAry[1], $numberAry[2]));

                    for ($i = 0; $i < $count; $i ++)
                    {
                        for ($j = 1; $j <= $ballAmount[$i]; $j ++)
                        {
                            if ($j < 10)
                            {
                                $needle = '0' . $j;
                            }
                            else
                            {
                                $needle = '' . $j;
                            }
                            if (strstr($matches[$i + 1], $needle))
                            {
                                $missedCounterAry[$i][$j] = 0;
                            }
                            else
                            {
                                $missedCounterAry[$i][$j] += 1;
                            }
                        }
                    }

                    foreach ($missedCounterAry as $playType => $countStr) 
                    {
                        $missNum[$playType] = implode(',', $countStr);
                    }
                }
            }
        }

        return $missNum;
    }

    // 遗漏种类统计
    private function getBallAmount($lotteryId)
    {
        $ballAmountConfig = array(
            '21406' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
            '21407' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
            '21408' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
            '21421' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
        );

        return $ballAmountConfig[$lotteryId];
    }
    
    /*
     * 查询指定彩种|期号【开奖】详情
     * @version:V1.2
     * @date:2015-08-14
     */
    public function getAwardDetailByDcenter($lotteryId, $issue = null)
    {   
        // 对应表名   
        $enName = $this->getTBName($lotteryId);
        // 期次规则处理
        $issue = $this->libcomm->getIssueFormat($lotteryId, $issue);

        $table = "cp_" . $enName . "_paiqi";

        // 胜负彩 任九处理
        if(in_array($lotteryId, array('11', '19')))
        {
            $sql = "SELECT s.mid, s.result, s.rj_sale, s.sfc_sale, s.award, s.award_detail, t.start_sale_time, t.end_sale_time, MAX(t.begin_date) as begin_date FROM {$table} as s LEFT JOIN cp_tczq_paiqi as t ON s.mid = t.mid WHERE 1 ";

            if(!empty($issue))
            {
                $sql .= " and s.mid= ? and s.status >= 50";
            }
            else
            {
                $sql .= " and s.status >= 50 AND t.ctype = 1 GROUP BY s.mid order by s.id desc limit 1";
            }
        }
        else
        {
            $sql = "select * from " . $table . " where 1 ";

            if (!empty($issue)) 
            {
                $sql .= " and issue= ? and status >= 50";
            } 
            else 
            {
                $sql .= " and status >= 50 order by id desc limit 1";
            }
        }

        $awards = $this->slaveCfg->query($sql,array($issue))->getRow();

        // 格式化数据
        $awards = $this->getAwardDetailInfo($lotteryId, $awards);

        return $awards;
    }

    /*
     * 获取【老足彩】胜负彩|任选九指定期次场次信息
     * @version:V1.2
     * @date:2015-08-19
     */
    public function getTczqMatchInfo($lotteryId, $mid)
    {
        $matches = array();

        // 期次规则处理
        $mid = $this->libcomm->getIssueFormat($lotteryId, $mid);

        if(!empty($mid))
        {
            $sql = "SELECT mname, ctype, league, home, away, start_sale_time, end_sale_time, begin_date, eur_odd_win, eur_odd_deuce, eur_odd_loss FROM `cp_tczq_paiqi` where mid = ? and ctype = 1 ORDER BY mname ASC";
            $matches = $this->slaveCfg->query($sql,array($mid))->getAll();
        }
        
        return $matches;
    }

    /*
     * 指定彩种|期号【开奖】详情 格式化
     * @version:V1.2
     * @date:2015-08-19
     */
    public function getAwardDetailInfo($lotteryId, $awards)
    {
        $awardDetail = array();
        if(!empty($awards))
        {
            if(in_array($lotteryId, array('11', '19')))
            {
                $awardDetail['base'] = array(
                    'seExpect' => $awards['mid'],
                    'awardNumber' => str_replace(array('|','(', ')'), array(':', ':', ''), $awards['result']),
                    'seLotid' => $lotteryId,
                    'seEndtime' => strtotime($awards['end_sale_time'])*1000,
                    'seFsendtime' => strtotime($awards['show_end_time'])*1000,
                    'awardTime' => strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($awards['begin_date'])))) * 1000,
                    'seDsendtime' => strtotime($items['end_sale_time'])*1000,
                    'seAddtime' => strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($awards['begin_date'])))) * 1000
                );

                // 奖级信息
                $levInfo = $this->getAwardLevInfo($lotteryId, $awards['award_detail']);

                $awardDetail['detail'] = array(
                    'awardMoney' => ($lotteryId == 11)?$awards['sfc_sale']:$awards['rj_sale'],
                    'awardPool' => $awards['award'],
                    'awardLevelList' => $levInfo
                );   

                // 获取场次对阵信息
                $matches = $this->getTczqMatchInfo($lotteryId, $awards['mid']);

                $awardDetail['matchs'] = $this->getMatchList($lotteryId, $matches);
            }
            else
            {
                $awardDetail['base'] = array(
                    'seExpect' => $awards['issue'],
                    'awardNumber' => str_replace(array('|','(', ')'), array(':', ':', ''), $awards['awardNum']),
                    'seLotid' => $lotteryId,
                    'seEndtime' => strtotime($awards['end_time'])*1000,
                    'seFsendtime' => strtotime($awards['show_end_time'])*1000,
                    'awardTime' => strtotime($awards['award_time'])*1000,
                    'seDsendtime' => strtotime($awards['end_time'])*1000,
                    'seAddtime' => strtotime($awards['sale_time'])*1000
                );

                // 奖级信息
                $levInfo = $this->getAwardLevInfo($lotteryId, $awards['bonusDetail']);

                $awardDetail['detail'] = array(
                    'awardMoney' => $awards['sale'],
                    'awardPool' => $awards['pool'],
                    'awardLevelList' => $levInfo,
                    'bonusDetail' => $awards['bonusDetail']
                );              
            }         
        }

        return $awardDetail;
    }


    // 各彩种查询对应表名
    public function getTBName($lotteryId)
    {
        $enName = '';
        if (isset(self::$TB_NAMES[$lotteryId])) 
        {
            $enName = self::$TB_NAMES[$lotteryId];
        }
        
        return $enName;
    }

     /*
     * 老足彩场次 格式化
     * @version:V1.2
     * @date:2015-08-20
     */
    public function getMatchList($lotteryId, $matches)
    {
        $matchList = array();
        if(!empty($matches))
        {
            foreach ($matches as $key => $items) 
            {
                $matchList[$key]['orderId'] = $items['mname'];
                $matchList[$key]['typeId'] = $lotteryId;
                $matchList[$key]['gameName'] = $items['league'];
                $matchList[$key]['teamName1'] = $items['home'];
                $matchList[$key]['teamName2'] = $items['away'];
            }
        }
        return $matchList;
    }

    /*
     * 彩种奖级信息 格式化
     * @version:V1.2
     * @date:2015-08-19
     */
    public function getAwardLevInfo($lotteryId, $bonusDetail)
    {
        $levInfo = array();
        $bonusDetail = json_decode($bonusDetail, true);

        if(!empty($bonusDetail))
        {
            if($lotteryId == '51')
            {
                // 双色球
                $levArray = array(
                    '1dj' => '一等奖',
                    '2dj' => '二等奖',
                    '3dj' => '三等奖',
                    '4dj' => '四等奖',
                    '5dj' => '五等奖',
                    '6dj' => '六等奖'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    $levDetail = array(
                        'awardLevel' => $awardLevel,
                        'prize' => $items['dzjj'],
                        'prizeNumber' => $items['zs'],
                        'awardName' => $levArray[$lev]
                    );
                    $awardLevel++;
                    array_push($levInfo, $levDetail);
                }
            }
            elseif($lotteryId == '23529')
            {
                // 大乐透
                $levArray = array(
                    '1dj' => array(
                        'jb' => '一等奖',
                        'zj' => '一等奖(追加)'
                    ),
                    '2dj' => array(
                        'jb' => '二等奖',
                        'zj' => '二等奖(追加)'
                    ),
                    '3dj' => array(
                        'jb' => '三等奖',
                        'zj' => '三等奖(追加)'
                    ),
                    '4dj' => array(
                        'jb' => '四等奖',
                        'zj' => '四等奖(追加)'
                    ),
                    '5dj' => array(
                        'jb' => '五等奖',
                        'zj' => '五等奖(追加)'
                    ),
                    '6dj' => array(
                        'jb' => '六等奖',
                        'zj' => '六等奖(追加)'
                    )
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    foreach ($items as $key => $value) 
                    {
                        $levDetail = array(
                            'awardLevel' => $awardLevel,
                            'prize' => $value['dzjj'],
                            'prizeNumber' => $value['zs'],
                            'awardName' => $levArray[$lev][$key]
                        );
                        $awardLevel++;
                        array_push($levInfo, $levDetail);
                    }                 
                }
            }
            elseif($lotteryId == '21406' || $lotteryId == '21407' || $lotteryId == '21408' || $lotteryId == '21421')
            {
                // 十一选五
                $levArray = array(
                    'qy' => '任选一',
                    'r2' => '任选二',
                    'r3' => '任选三',
                    'r4' => '任选四',
                    'r5' => '任选五',
                    'r6' => '任选六',
                    'r7' => '任选七',
                    'r8' => '任选八',
                    'q2zhix' => '前二直选',
                    'q2zux' => '前二组选',
                    'q3zhix' => '前三直选',
                    'q3zux' => '前三组选'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    $levDetail = array(
                        'awardLevel' => $awardLevel,
                        'prize' => $items['dzjj'],
                        'prizeNumber' => 0,
                        'awardName' => $levArray[$lev]
                    );
                    $awardLevel++;
                    array_push($levInfo, $levDetail);
                }
            }
            elseif($lotteryId == '52' || $lotteryId == '33' || $lotteryId == '35')
            {
                // 福彩3D、排列三
                $levArray = array(
                    'zx' => '直选',
                    'z3' => '组选3',
                    'z6' => '组选6'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    $levDetail = array(
                        'awardLevel' => $awardLevel,
                        'prize' => $items['dzjj'],
                        'prizeNumber' => $items['zs'],
                        'awardName' => $levArray[$lev]
                    );
                    $awardLevel++;
                    array_push($levInfo, $levDetail);
                }
            }
            elseif($lotteryId == '11')
            {
                // 胜负彩
                $levArray = array(
                    '1dj' => '一等奖',
                    '2dj' => '二等奖'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    if(isset($levArray[$lev]))
                    {
                        $levDetail = array(
                            'awardLevel' => $awardLevel,
                            'prize' => $items['dzjj'],
                            'prizeNumber' => $items['zs'],
                            'awardName' => $levArray[$lev]
                        );
                        $awardLevel++;
                        array_push($levInfo, $levDetail);
                    }
                }
            }
            elseif($lotteryId == '19') 
            {
                // 任选九
                $levArray = array(
                    'rj' => '一等奖'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    if(isset($levArray[$lev]))
                    {
                        $levDetail = array(
                            'awardLevel' => $awardLevel,
                            'prize' => $items['dzjj'],
                            'prizeNumber' => $items['zs'],
                            'awardName' => $levArray[$lev]
                        );
                        $awardLevel++;
                        array_push($levInfo, $levDetail);
                    }
                }
            }
            elseif($lotteryId == '10022')
            {
                // 七星彩
                $levArray = array(
                    '1dj' => '一等奖',
                    '2dj' => '二等奖',
                    '3dj' => '三等奖',
                    '4dj' => '四等奖',
                    '5dj' => '五等奖',
                    '6dj' => '六等奖'
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    $levDetail = array(
                        'awardLevel' => $awardLevel,
                        'prize' => $items['dzjj'],
                        'prizeNumber' => $items['zs'],
                        'awardName' => $levArray[$lev]
                    );
                    $awardLevel++;
                    array_push($levInfo, $levDetail);
                }
            }
            elseif($lotteryId == '23528')
            {
                // 七乐彩
                $levArray = array(
                    '1dj' => '一等奖',
                    '2dj' => '二等奖',
                    '3dj' => '三等奖',
                    '4dj' => '四等奖',
                    '5dj' => '五等奖',
                    '6dj' => '六等奖',
                    '7dj' => '七等奖',
                );

                $awardLevel = 1;         
                foreach ($bonusDetail as $lev => $items) 
                {
                    $levDetail = array(
                        'awardLevel' => $awardLevel,
                        'prize' => $items['dzjj'],
                        'prizeNumber' => $items['zs'],
                        'awardName' => $levArray[$lev]
                    );
                    $awardLevel++;
                    array_push($levInfo, $levDetail);
                }
            }
        }

        return $levInfo;                
    }
    
    /*
    * 查询老足彩比赛对阵信息（已废弃）
    *
    * @param $lotteryId 彩种id
    * @param $issue 期号
    * @return array
    */
    public function getMatchByByQihui($lotteryId, $issue)
    {
    	$awards = array();
    	$awardResponse = $this->tools->get($this->config->item('busi_api') . 'ticket/data/ozc_games', array(
    			'lid' => $lotteryId,
    			'issue' => $issue,
    	));
    
    	if ($awardResponse['code'] == 0) {
    		$awards = $awardResponse['data'];
    	}
    
    	return $awards;
    }

    public function getJczqAwardDetail($mid)
    {
        $sql = "SELECT d.ctype, d.detail, p.m_date, p.mname, p.league, p.home, p.away, p.full_score FROM cp_jczq_detail AS d LEFT JOIN cp_jczq_paiqi AS p ON d.mid = p.mid WHERE d.mid = $mid;";
        $detail = $this->slaveCfg->query($sql)->getAll();
        return $detail;
    }

    public function getJclqAwardDetail($mid)
    {
        $sql = "SELECT d.ctype, d.detail, p.m_date, p.mname, p.league, p.home, p.away, p.full_score FROM cp_jclq_detail AS d LEFT JOIN cp_jclq_paiqi AS p ON d.mid = p.mid WHERE d.mid = $mid;";
        $detail = $this->slaveCfg->query($sql)->getAll();
        return $detail;
    }

    // 近四天中奖信息
    public function getUserAwards($uid)
    {
        $sql = "SELECT uid, money, additions, orderId, `status` FROM cp_wallet_logs WHERE uid = ? AND ctype = 2 AND created >= DATE_SUB(NOW(),INTERVAL 4 DAY) AND (cstate & 1) = 0 AND (cstate & 2) = 0 AND orderId > 0 AND money > 0 ORDER BY money DESC LIMIT 1";
        $info = $this->db->query($sql, array($uid))->getRow();

        if(!empty($info))
        {
            // 更新
            $sql = "UPDATE cp_wallet_logs SET cstate = (cstate | 2) WHERE uid = ? AND ctype = 2 AND created >= DATE_SUB(NOW(),INTERVAL 4 DAY) AND (cstate & 1) = 0 AND (cstate & 2) = 0 AND orderId > 0";
            $this->db->query($sql, array($info['uid']));
        }
        return $info;
    }

    // 中奖墙
    public function getIndexWin()
    {
        $sql = "SELECT title, newsId, url, content, is_top, lname FROM cp_shouye_win WHERE status = 1 AND delete_flag = 0 ORDER BY is_top DESC, created DESC";
        return $this->slave->query($sql)->getAll();exit();
    }

    public function getWins()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['AWARD_NOTICE']));
        return $info;
    }
}
