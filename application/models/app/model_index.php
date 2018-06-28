<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 移动端 首页缓存化 模型层
 */
class Model_Index extends MY_Model 
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('lottery_model');
        $this->lidMap = $this->lottery_model->getCache();
    }

    // 获取活动模块
    public function getActivityInfo($platId)
    {
        $sql = "SELECT id, type, title, content, cstatus, weight, extra, platform, channels FROM cp_app_activity_config WHERE platform = ? ORDER BY weight DESC;";
        return $this->slave->query($sql, array($platId))->getALL();
    }
    
    private function getBannerList($platform) {
        $res = $this->slave->query("SELECT id, imgTitle, imgUrl, hrefUrl, lid, weight, extra, platform, channels 
            FROM cp_add_info 
            WHERE platform = ? AND delect_flag = 0 and start_time <= NOW() and end_time > NOW()
            ORDER BY weight DESC;", array($platform))->getALL();
        if (empty($res)) {
            $res = $this->slave->query("SELECT id, imgTitle, imgUrl, hrefUrl, lid, weight, extra, platform, channels
                FROM cp_add_info
                WHERE platform = ? AND delect_flag = 0 AND end_time < NOW()
                ORDER BY end_time DESC, start_time DESC, weight DESC
                limit 1", array($platform))->getALL();
        }
        return $res;
    }
    
    public function getAddInfo($platform = 'android') {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}add_info_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = $this->getBannerList($platform);
        $this->cache->redis->save($ukey, serialize($info), 0);
        return $info;
    }
    
    public function freshQdyInfo($platform = 'android', $pid) {
        $info = $this->slave->query("SELECT id, ctype, cid, title, imgUrl, url, lid, weight, extra, status, platform, channels 
            FROM cp_app_banner 
            WHERE ctype = 2 AND platform = ? AND delete_flag = 0 AND start_time < now() and end_time > now()
            ORDER BY id ASC;", array($pid))->getAll();
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_CONFIG']}appPreload_{$platform}";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->save($ukey, serialize($info), 0);
    }

    // 获取中奖公告
    public function getWins()
    {
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['AWARD_NOTICE']));
        return $info;
    }

    // 获取首页彩种信息
    public function getLotterys($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['LOTTERY_INFO']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $info = unserialize($this->cache->redis->get($ukey));
        if(empty($info))
        {
            $info = $this->freshLotterys($platform);
        }
        return $info;
    }

    // 刷新首页彩种信息
    public function freshLotterys($platform = 'android')
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['LOTTERY_INFO']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "SELECT lid, ltype, lname, logUrl, memo, isHot, ctype, attachFlag, channels FROM cp_lottery_info WHERE platform = '{$platform}' AND delect_flag = 0 ORDER BY weight DESC;";
        $info = $this->slave->query($sql)->getALL();
        if(!empty($info))
        {
            $this->cache->redis->save($ukey, serialize($info), 0);
        }
        return $info;
    }

    public function getAllLotterys($platform = 'android')
    {
        $sql = "SELECT plid, lid, ltype, lname, logUrl, weight, memo, isHot, ctype, delect_flag, attachFlag, channels FROM cp_lottery_info WHERE platform = '{$platform}' AND delect_flag = 0 ORDER BY weight DESC;";
        return $this->slave->query($sql)->getALL();
    }

    // 获取所有彩种最新期次的开奖信息
    public function getLotteryAwards()
    {
        $awards = array();

        $lotteryInfo = $this->lidMap;
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        foreach ($lotteryInfo as $lid => $cacheName) 
        {
            $caches = $this->cache->get($REDIS[$cacheName]);
            $caches = json_decode($caches, true);
            if(!empty($caches['lIssue']))
            {
                array_push($awards, $caches['lIssue']);         
            }
        }
      
        return $awards;
    }

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

    public function getJczqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function getJclqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function saveIndex($platform, $info)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_INDEX_NEW']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->redis->save($ukey, serialize($info), 0);
    }

    public function getIndex($platform)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['APP_INDEX']}$platform";
        $this->load->driver('cache', array('adapter' => 'redis'));
        return unserialize($this->cache->redis->get($ukey));
    }

    // 获取当前销售期次信息
    public function getLotterySale($lid)
    {
        $info = array();
        $lotteryInfo = $this->lidMap;
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $caches = $this->cache->get($REDIS[$lotteryInfo[$lid]]);
        $caches = json_decode($caches, true);
        if(!empty($caches['cIssue']))
        {
            $info = $caches['cIssue'];
        }
        return $info;
    }

    public function getJjcHistory($lotteryId)
    {
        $info = array();

        // 彩票数据中心
        switch ($lotteryId) 
        {
            // 竞彩足球
            case '42':
                $info = $this->getJcMatchHistory();
                break;
            // 竞彩篮球
            case '43':
                $info = $this->getLqMatchHistory();
                break;
            default:
                # code...
                break;
        }
        return $info;
    }

    public function getJcMatchHistory()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }
    
    public function getLqMatchHistory()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }

    // 获取致胜比赛信息
    public function getMatchDetail($lid, $mid)
    {
        $table = ($lid == '42') ? 'cp_data_zq_matchs' : 'cp_data_lq_matchs';
        $sql = "SELECT homelogo, awaylogo, htid, atid, lid, sid FROM {$table} WHERE mid = ?;";
        return $this->slaveDc->query($sql, array($mid))->getRow();
    }

    // 彩种停开售
    public function getlotteryConfig()
    {
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $lotteryConfig = json_decode($lotteryConfig, true);
        return $lotteryConfig;
    }
}
