<?php

class Award_Model extends MY_Model {

    private $lidMap = array(
        '51' => array('cache' => 'SSQ_ISSUE'),
        '52' => array('cache' => 'FC3D_ISSUE'),
        '33' => array('cache' => 'PLS_ISSUE'),
        '35' => array('cache' => 'PLW_ISSUE'),
        '10022' => array('cache' => 'QXC_ISSUE'),
        '23528' => array('cache' => 'QLC_ISSUE'),
        '23529' => array('cache' => 'DLT_ISSUE'),
        '11' => array('cache' => 'SFC_ISSUE'),
        '19' => array('cache' => 'RJ_ISSUE'),
        '21406' => array('cache' => 'SYXW_ISSUE_TZ'),
    	'21407' => array('cache' => 'JXSYXW_ISSUE_TZ'),
    	'21408' => array('cache' => 'HBSYXW_ISSUE_TZ'),
    	'53' => array('cache' => 'KS_ISSUE_TZ'),
    	'21408' => array('cache' => 'HBSYXW_ISSUE_TZ'),
    	'54' => array('cache' => 'KLPK_ISSUE_TZ'),
        '55' => array('cache' => 'CQSSC_ISSUE_TZ'),
        '56' => array('cache' => 'JLKS_ISSUE_TZ'),
        '57' => array('cache' => 'JXKS_ISSUE_TZ'),
        '21421' => array('cache' => 'GDSYXW_ISSUE_TZ'),
    );

    public function __construct() {
        parent::__construct();
        $this->load->model('lottery_model', 'Lotery');
        $this->load->model('state_model', 'State');
    }

    public function getLast() {
        $awards = array();
        $REDIS = $this->config->item('REDIS');
        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/pl', 
        array('cache_2345caipiao' => $REDIS['CACHE_TYPE']['ticket_data_pl0']));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    public function getCurrent() {
        $awards = array();

        $lottery = array(
            'DLT' => 23529,
            'RJ' => 19,
            'SFC' => 11,
            'QLC' => 23528,
            'QXC' =>  10022,
            'SYXW' =>  21406,
            'SSQ' =>  51,
            'FCSD' => 52,
            'PLS' => 33,
            'PLW' => 35
        );

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/v1/il', array(
                'lid' => implode( ',', array_values( $lottery ) ), // 逗号分隔
                'ci' => '1'
        ));

        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
     * 分页查询指定彩种的开奖列表
     * @param $lotteryId 彩种id
     * @param $state 期号状态 100：可售 201：已开奖
     * @return array
     */
    public function getNumber($lotteryId, $state, $pn, $ps = 10) {
        $awards = array();

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/il', array(
            'lid' => $lotteryId,
            'state' => $state,
            'pn' => $pn,
            'ps' => $ps,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    public function getJC($lotteryId, $state, $issue) {
        $awards = array();
        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/jil', array(
            'lid' => $lotteryId,
            'state' => $state,
            'issue' => $issue,
            'pre_issue' => 1,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    public function getJCNew($lotteryId, $date)
    {
        $awards = array();
        if ($lotteryId == Lottery_Model::JCZQ)
        {
            $awards = $this->getJczqAwardCache($date);
        }
        elseif ($lotteryId == Lottery_Model::JCLQ)
        {
            $awards = $this->getJclqAwardCache($date);
        }

        return $awards;
    }

    private function getJczqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $awards = $this->cache->redis->get($ukey);
        $awards = json_decode($awards, true);
$awards = array();
        if(empty($awards))
        {
            $awards = $this->refreshJczqAwardCache($date);
        }

        return $awards;
    }

    private function refreshJczqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT p.mid, p.m_date, p.mname, p.league, p.home, p.away, p.end_sale_time, p.rq, p.half_score, p.full_score, p.m_status,
            p.status, p.is_open, p.sale_status, p.show_end_time, IF(d.mid > 0, 1, 0) as showDetail 
            FROM cp_jczq_paiqi AS p LEFT JOIN cp_jczq_detail AS d ON p.mid = d.mid
            WHERE p.m_date = $date AND p.status >= 50 GROUP BY p.mid 
            ORDER BY p.mid ASC;";

        $awards = $this->cfgDB->query($sql)->getAll();

        if(!empty($awards))
        {
            $this->cache->save($ukey, json_encode($awards), 600);
        }
        return $awards;
    }

    private function getJclqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $awards = $this->cache->redis->get($ukey);
        $awards = json_decode($awards, true);
$awards = array();
        if(empty($awards))
        {
            $awards = $this->refreshJclqAwardCache($date);
        }

        return $awards;
    }

    private function refreshJclqAwardCache($date)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD']}$date";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT p.mid, p.m_date, p.mname, p.league, p.home, p.away, p.begin_time, p.rq, p.preScore, p.full_score, p.m_status, p.status,
            p.is_open, p.sale_status, p.show_end_time, IF(d.mid > 0, 1, 0) as showDetail
            FROM cp_jclq_paiqi AS p LEFT JOIN cp_jclq_detail AS d ON p.mid = d.mid 
            WHERE p.m_date = $date AND p.status >= 50 GROUP BY p.mid 
            ORDER BY p.mid ASC;";

        $awards = $this->cfgDB->query($sql)->getAll();

        if(!empty($awards))
        {
            $this->cache->save($ukey, json_encode($awards), 600);
        }
        return $awards;
    }

    public function getDefaultDate($lotteryId)
    {
        if ($lotteryId == Lottery_Model::JCZQ)
        {
            $defaultDate = $this->getJczqDateCache();
        }
        elseif ($lotteryId == Lottery_Model::JCLQ)
        {
            $defaultDate = $this->getJclqDateCache();
        }

        return $defaultDate;
    }

    private function getJczqDateCache()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $date = $this->cache->redis->get($ukey);

        if(empty($date))
        {
            $date = $this->refreshJczqDateCache();
        }

        return $date;
    }

    private function refreshJczqDateCache()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT DATE_FORMAT(MAX(m_date), '%Y%m%d') FROM cp_jczq_paiqi WHERE status >= 50 ";
        $date = $this->slaveCfg->query($sql)->getOne();

        if ( ! empty($date))
        {
            $this->cache->save($ukey, $date, 600);
        }

        return $date;
    }

    private function getJclqDateCache()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $date = $this->cache->redis->get($ukey);

        if(empty($date))
        {
            $date = $this->refreshJclqDateCache();
        }

        return $date;
    }

    private function refreshJclqDateCache()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_AWARD_LAST']}";
        $this->load->driver('cache', array('adapter' => 'redis'));

        $sql = "SELECT DATE_FORMAT(MAX(m_date), '%Y%m%d') FROM cp_jclq_paiqi WHERE status >= 50 ";
        $date = $this->slaveCfg->query($sql)->getOne();

        if ( ! empty($date))
        {
            $this->cache->save($ukey, $date, 600);
        }

        return $date;
    }

    /*
     * 查询老足彩比赛对阵信息
     * @author:liuli
     * @date:2015-02-02
     *
     * @param $lotteryId 彩种id
     * @param $issue 期号
     * @return array
     */
    public function getMatchByIssue($lotteryId, $issue)
    {
        $awards = array();

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/ozc_games', array(
            'lid' => $lotteryId,
            'issue' => $issue,
        ));

        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
     * 竞彩指定场次列表详情
     * @author:liuli
     * @date:2015-02-02
     *
     * @param $lotteryId 彩种id
     * @param $gameList 比赛场次
     * @return array
     */
    public function getNumberByGamelist($lotteryId, $gameList)
    {
        $awards = array();

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/datail', array(
            'lid' => $lotteryId,
            'gamelist' => $gameList,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
     * 普通彩果指定期号【投注】内容
     * @author:liuli
     * @date:2015-02-02
     *
     * @param $lotteryId 彩种id
     * @param $issue 期号
     * @return array
     */
    public function getIssueInfo($lotteryId, $issue)
    {
        $awards = array();

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/il', array(
            'lid' => $lotteryId,
            'issue' => $issue,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
     * 普通彩果指定期号【开奖】详情
     * @author:liuli
     * @date:2015-02-02
     *
     * @param $lotteryId 彩种id
     * @param $issue 期号
     * @return array
     */
    public function getAwardDetail($lotteryId, $issue)
    {
        $awards = array();

        $awardResponse = $this->tools->get($this->busiApi . 'ticket/data/awardDetail', array(
            'lid' => $lotteryId,
            'issue' => $issue,
        ));
        if ($awardResponse['code'] == 0) {
            $awards = $awardResponse['data'];
        }

        return $awards;
    }

    /*
     * 获取所有彩种最新期次的开奖信息
     * @date:2015-08-11
     */
    public function getCurrentAward()
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

    // 获取竞彩开奖详情
    public function getJcDetail($lotteryId, $mid)
    {
        $detail = array();
        if ($lotteryId == Lottery_Model::JCZQ)
        {
            $detail = $this->getJczqAwardDetail($mid);
        }
        elseif ($lotteryId == Lottery_Model::JCLQ)
        {
            $detail = $this->getJclqAwardDetail($mid);
        }
        return $detail;
    }

    public function getJczqAwardDetail($mid)
    {
        $sql = "SELECT d.ctype, d.detail, p.m_date, p.mname, p.league, p.home, p.away, p.full_score FROM cp_jczq_detail AS d LEFT JOIN cp_jczq_paiqi AS p ON d.mid = p.mid WHERE d.mid = $mid;";
        $detail = $this->cfgDB->query($sql)->getAll();
        return $detail;
    }

    public function getJclqAwardDetail($mid)
    {
        $sql = "SELECT d.ctype, d.detail, p.m_date, p.mname, p.league, p.home, p.away, p.full_score FROM cp_jclq_detail AS d LEFT JOIN cp_jclq_paiqi AS p ON d.mid = p.mid WHERE d.mid = $mid;";
        $detail = $this->cfgDB->query($sql)->getAll();
        return $detail;
    }

}
