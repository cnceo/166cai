<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 数据中心缓存 模型层
 * @description: 数据中心 - 比赛对阵、期次、开奖信息缓存
 * @date:2015-08-05
 */

class Cache_Model extends MY_Model 
{
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
        '55' => array('cache' => 'CQSSC_ISSUE_TZ'),
        '56' => array('cache' => 'JLKS_ISSUE_TZ'),
        '57' => array('cache' => 'JXKS_ISSUE_TZ'),
        '21421' => array('cache' => 'GDSYXW_ISSUE_TZ'),
    );

    // 遗漏缓存
    private $lidMissMap = array(
        '51' => array('cache' => 'SSQ_MISS'),
        '52' => array('cache' => 'FC3D_MISS'),
        '33' => array('cache' => 'PL3_MISS'),
        '35' => array('cache' => 'PL5_MISS'),
        '10022' => array('cache' => 'QXC_MISS'),
        '23528' => array('cache' => 'QLC_MISS'),
        '23529' => array('cache' => 'DLT_MISS'),
        '21406' => array('cache' => 'SYXW_MISS'),
        '21407' => array('cache' => 'JXSYXW_MISS'),
        '21408' => array('cache' => 'HBSYXW_MISS'),
    	'53' => array('cache' => 'KS_MISS'),
        '54' => array('cache' => 'KLPK_MISS'),
        '55' => array('cache' => 'CQSSC_MISS'),
        '56' => array('cache' => 'JLKS_MISS'),
        '57' => array('cache' => 'JXKS_MISS'),
        '21421' => array('cache' => 'GDSYXW_MISS'),
    );

    // 投注信息缓存
    private $betCache = array(
        '42' => 'JCZQ_BET_COUNT',
        '43' => 'JCLQ_BET_COUNT',
        '11' => 'SFC_BET_COUNT',
        '19' => 'SFC_BET_COUNT',
    );
    
	public function __construct() 
	{
		parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
	}

	/*
     * 数据中心 - 获取竞足、竞篮在售期次对阵
     * @version:V1.2
     * @date:2015-08-05
     */
	public function getJjcInfo($lotteryId)
    {
    	$info = array();

        // 彩票数据中心
        switch ($lotteryId) 
        {
            // 竞彩足球
            case '42':
                $info = $this->getJczqMatch();
                break;
            // 竞彩篮球
            case '43':
                $info = $this->getJclqMatch();
                break;
            default:
                # code...
                break;
        }
        return $info;
    }

	/*
     * 数据中心 - 获取竞彩足球对阵缓存信息
     * @version:V1.2
     * @date:2015-08-05
     */
    public function getJczqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    /*
     * 数据中心 - 获取竞彩篮球对阵缓存信息
     * @version:V1.2
     * @date:2015-08-05
     */
    public function getJclqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
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

    /*
     * 数据中心 - 获取所有彩种最新期次的【开奖】信息
     * @version:V1.2
     * @date:2015-08-11
     */
    public function getLastAward()
    {
        $awards = array();
        $lotteryInfo = $this->lidMap;
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
     * 数据中心 - 获取指定彩种当前【在售】期信息
     * @version:V1.2
     * @date:2015-08-06
     */
    public function getCurrentByLid($lotteryId) 
    {
        $info = array();
        $lotteryInfo = $this->lidMap;
        if(!empty($lotteryInfo[$lotteryId]['cache']))
        {
            $REDIS = $this->config->item('REDIS');
            $caches = $this->cache->get($REDIS[$lotteryInfo[$lotteryId]['cache']]);
            $caches = json_decode($caches, true);
            if(!empty($caches['cIssue']))
            {
                array_push($info, $caches['cIssue']);         
            }
        }
        
        return $info;
    }

    /*
     * 数据中心 - 获取所有彩种当前【在售】期信息
     * @version:V1.2
     * @date:2015-08-06
     */
    public function getCurrentLottery()
    {
        $info = array();
        $lotteryInfo = $this->lidMap;
        $REDIS = $this->config->item('REDIS');
        foreach ($lotteryInfo as $lid => $items) 
        {
            $caches = $this->cache->get($REDIS[$items['cache']]);
            $caches = json_decode($caches, true);
            if(!empty($caches['cIssue']))
            {
                // 奖池信息获取
                $caches['cIssue']['awardPool'] = $caches['lIssue']['awardPool'];
                array_push($info, $caches['cIssue']);         
            }
        }
      
        return $info;
    }

    /*
     * 查询老足彩当前【在售】期的【对阵】信息
     * @version:V1.2
     * @date:2015-08-21
     */
    public function getTczqInfo($lotteryId)
    {
        $matches = array();
        if(in_array($lotteryId, array('11', '19')))
        {
            $REDIS = $this->config->item('REDIS');
            $caches = $this->cache->get($REDIS['SFC_MATCH']);
            $caches = json_decode($caches, true);
            if(!empty($caches))
            {
                $matches = $caches;
            }       
        }

        return $matches;
    }

    /*
     * 数据中心 - 获取最近【十期】遗漏数据缓存
     * @version:V1.3
     * @date:2015-10-14
     */
    public function getMissInfo($lotteryId)
    {
        $info = array();
        $lotteryInfo = $this->lidMissMap;
        if(!empty($lotteryInfo[$lotteryId]['cache']))
        {
            $REDIS = $this->config->item('REDIS');
            $info = $this->cache->get($REDIS[$lotteryInfo[$lotteryId]['cache']]);
            $info = in_array($lotteryId, array('53', '54', '56', '57')) ? json_decode($info, true) : unserialize($info);
        }
        
        return $info;
    }

    /*
     * 数据中心 - 获取数字彩期次汇总信息【最新开奖、在售、预售、开奖中】
     * @version:V1.3
     * @date:2015-11-10
     */
    public function getIssueInfo($lotteryId)
    {
        $info = array();
        $lotteryInfo = $this->lidMap;
        if(!empty($lotteryInfo[$lotteryId]['cache']))
        {
            $REDIS = $this->config->item('REDIS');
            $info = $this->cache->get($REDIS[$lotteryInfo[$lotteryId]['cache']]);
            $info = json_decode($info, true);
        }
        
        return $info;
    }

    /*
     * 获取投注页赛事历史缓存
     */
    public function getJcMatchHistory()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }

    /**
     * 获取篮球投注页赛事历史缓存
     * @return mixed
     */
    public function getLqMatchHistory()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }
    
    /*
     * 获取彩种配置信息
     */
    public function getlotteryConfig()
    {
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $lotteryConfig = json_decode($lotteryConfig, true);
        return $lotteryConfig;
    }

    /*
     * 获取版本配置信息、彩种停开售
     */
    public function getAppConfig($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        if(empty($info))
        {
            $info = $this->freshVersionInfo($platform, $appVersionCode);
        }
        return $info;
    }

    /*
     * 刷新版本信息
     * @date:2015-04-17
     */
    public function freshVersionInfo($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT id, versionName, versionCode, showAlert, showRedpack, isCheck, upgradeVersion, mark, lotteryConfig, platform FROM cp_app_version_config WHERE platform = '{$platform}' ORDER BY versionCode DESC;";
        $info = $this->slave->query($sql)->getALL();
        $versionData = array();
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                $versionData[$items['versionCode']] = $items;
            }
            $this->cache->redis->save($ukey, serialize($versionData), 0);

        }
        return $versionData;
    }

    public function getOrderWin()
    {
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['AWARD_NOTICE']));
        return $info;
    }

    /*
     * 获取启动页缓存
     * @date:2016-09-27
     */
    public function getPreloadInfo($platform, $ctype = 'preload')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        return $info;
    }

    public function getSfcMatchHistory()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['SFC_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }

    public function getJcBetInfo($lotteryId)
    {
        $REDIS = $this->config->item('REDIS');
        $betInfo = array();
        $lotteryInfo = $this->betCache;
        if(!empty($lotteryInfo[$lotteryId]))
        {
            $ukey = "{$REDIS[$lotteryInfo[$lotteryId]]}";
            $betInfo = $this->cache->redis->get($ukey);
            $betInfo = json_decode($betInfo, true);
        }
        return $betInfo;
    }

    public function getIndex($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_INDEX_NEW']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        return unserialize($this->cache->redis->get($ukey));
    }

    public function getLimitChannel()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['LIMIT_CHANNEL']}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $channelArr = json_decode($this->cache->redis->get($ukey), true);
        $channelArr = $channelArr ? $channelArr : array();
        return $channelArr;
    }

    // 老版缓存兼容
    public function getBannerInfo($ctype, $platformId = 1, $platformName = 'ios')
    {
        $ctypeArr = array(
            '1' =>  'appGiftRemind',    // 实名礼包提醒页配置
            '2' =>  'appPreload',       // 启动页
            '3' =>  'appIndexPop',      // 首页弹层
            '4' =>  'appWechatLogin',   // 微信登录
            '5' =>  'appBetBanner',     // 投注页加奖素材
        );
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctypeArr[$ctype]}_{$platformName}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        return $info;
    }

    public function getEventInfo($platform, $ctype = 'eventStatus')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}{$ctype}_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        return $info;
    }
}