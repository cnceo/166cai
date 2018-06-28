<?php

/**
 * 走势图 model
 */
class Trend_Model extends MY_Model 
{
    // 数据库对应表名
    private static $lotteryCache = array(
        33 => 'PL3_MISS_MORE',
        35 => 'PL5_MISS_MORE',
        51 => 'SSQ_MISS_MORE',
        52 => 'FC3D_MISS_MORE',
        10022 => 'QXC_MISS_MORE',
        21406 => 'SYXW_MISS_MORE',
        21407 => 'JXSYXW_MISS_MORE',
        21408 => 'HBSYXW_MISS_MORE',
        23528 => 'QLC_MISS_MORE',
        23529 => 'DLT_MISS_MORE',
    	53 => 'KS_MISS_MORE',
    	54 => 'KLPK_MISS_MORE',
        56 => 'JLKS_MISS_MORE',
        57 => 'JXKS_MISS_MORE',
        21421 => 'GDSYXW_MISS_MORE',
    );

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
    	'54' => array('cache' => 'KLPK_ISSUE_TZ'),
        '56' => array('cache' => 'JLKS_ISSUE_TZ'),
        '57' => array('cache' => 'JXKS_ISSUE_TZ'),
        '21421' => array('cache' => 'GDSYXW_ISSUE_TZ'),
    );

    public function __construct() 
    {
        parent::__construct();
    }

    public function getTrendData($lotteryId, $limit = 200)
    {
        $sql2 = "SELECT lid, issue, play_type, detail FROM cp_missed_counter WHERE lid = $lotteryId ORDER BY issue DESC LIMIT $limit";
        $data = $this->slaveCfg->query($sql2)->getAll();

        return $data;
    }

    public function getTrendCache($lotteryId, $limit = 200)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $cacheName = self::$lotteryCache[$lotteryId];
        $data = $this->cache->get($REDIS[$cacheName]);
        $data = in_array($lotteryId, array('53', '54', '56', '57')) ? json_decode($data, true) : unserialize($data);
        return $data;
    }

    /*
     * 数据中心 - 获取指定彩种最新期次的【开奖】信息
     * @version:V1.2
     * @date:2015-08-18
     */
    public function getLastByLid($lotteryId)
    {
        $awards = array();
        $lotteryInfo = $this->lidMap;
        if(!empty($lotteryInfo[$lotteryId]['cache']))
        {
            $REDIS = $this->config->item('REDIS');
            $caches = $this->cache->get($REDIS[$lotteryInfo[$lotteryId]['cache']]);
            $caches = json_decode($caches, true);
            if(!empty($caches['lIssue']))
            {
                array_push($awards, $caches['lIssue']);         
            }
        }
      
        return $awards;
    }


}
