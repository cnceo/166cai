<?php

/**
 * 智胜竞彩比赛数据接口
 * @date:2016-04-11
 */
class Api_Zhisheng_Match_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->config->load('jcMatch');
        $this->redis = $this->config->item('redisList');
        $this->apiUrl = $this->config->item('apiUrl');
    }

    /*
     * 获取缓存数据
     */
    public function getRedisData($redisKey, $url, $time = 30)
    {
        $redisData = $this->cache->redis->get($redisKey);
        if(empty($redisData))
        {
            $redisData = $this->refreshApi($redisKey, $url, $time);
        }
        return $redisData;
    }

    /*
     * 刷新接口数据
     */
    public function refreshApi($redisKey, $url, $time)
    {
        $apiUrl = $this->apiUrl . $url;

        $content = $this->tools->request($apiUrl, array());
        $content = json_decode($content, true);

        // 存入缓存
        $content = json_encode($content);
        $this->cache->redis->save($redisKey, $content, $time);
        return $content;
    }

    /*
     * API 获取联赛最新一期的赛程
     */
    public function getLastSid($lid)
    {
        $url = 'home/base/?oid=1003&lid=' . $lid;
        $redisKey = "{$this->redis['LEAGUE_SEASON']}$lid";

        $redisData = $this->getRedisData($redisKey, $url, 600);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ' && !empty($redisData['row']))
        {
            $data = $redisData['row'];
        }
        return $data;
    }

    /*
     * API 获取联赛赛程赛果
     */
    public function getMatchSchedule($lid, $sid)
    {
        $url = 'home/base/?oid=1004&lid=' . $lid . '&sid=' . $sid;
        $redisKey = "{$this->redis['LEAGUE_SCHEDULE']}{$lid}{$sid}";

        $redisData = $this->getRedisData($redisKey, $url, 600);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ')
        {
            $data = $redisData;
        }
        return $data;
    }

    /*
     * API 获取联赛积分榜
     */
    public function getScoreRank($lid, $sid)
    {
        $url = 'home/base/?oid=1007&lid=' . $lid . '&sid=' . $sid;
        $redisKey = "{$this->redis['LEAGUE_SCORERANK']}{$lid}{$sid}";

        $redisData = $this->getRedisData($redisKey, $url, 600);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ')
        {
            $data = $redisData;
        }
        return $data;
    }

    /*
     * API 获取联赛射手榜
     */
    public function getShotRank($lid, $sid)
    {
        $url = 'home/base/?oid=1012&lid=' . $lid . '&sid=' . $sid;
        $redisKey = "{$this->redis['LEAGUE_SHOTRANK']}{$lid}{$sid}";

        $redisData = $this->getRedisData($redisKey, $url, 600);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ')
        {
            $data = $redisData;
        }
        return $data;
    }

    /*
     * API 比赛详情
     */
    public function getMatchDetail($mid)
    {
        $url = 'home/base/?oid=1022&mid=' . $mid;
        $redisKey = "{$this->redis['JC_MATCHDETAIL']}{$mid}";

        $redisData = $this->getRedisData($redisKey, $url, 10);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ' && !empty($redisData['row']))
        {
            $data = $redisData['row'];
        }
        return $data;
    }


    /*
     * API 足彩比分对阵
     */
    public function getMatchScore($expect, $lottid = 6)
    {
        $url = 'home/base/?oid=3001&lottid=' . $lottid . '&expect=' . $expect;
        $redisKey = "{$this->redis['JC_MATCHSCORE']}{$lottid}{$expect}";

        $redisData = $this->getRedisData($redisKey, $url, 10);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ' && !empty($redisData['row']))
        {
            $data = $redisData;
        }
        return $data;
    }

    /*
     * API 预计比赛阵容
     */
    public function getMatchPlayer($mid)
    {
        $url = 'home/base/?oid=1016&mid=' . $mid;
        $redisKey = "{$this->redis['JC_MATCHPLAYER']}{$mid}";
        
        $redisData = $this->getRedisData($redisKey, $url, 10);
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ')
        {
            $data['hteam'] = $redisData['hteam']['r']?$redisData['hteam']['r']:array();
            $data['ateam'] = $redisData['ateam']['r']?$redisData['ateam']['r']:array();
        }
        return $data;
    }

    /*
     * API 比分直播
     */
    public function getMatchLive($mid)
    {
        $url = 'home/base/?oid=3010&mid=' . $mid;
        $apiUrl = $this->apiUrl . $url;

        $redisData = $this->tools->request($apiUrl, array());
        $redisData = json_decode($redisData, true);

        $data = array();
        if($redisData['desc'] == 'succ')
        {
            $data['match'] = $redisData['match'];
            $data['event'] = $redisData['event'];
            $data['total'] = $redisData['total'];
        }
        return $data;
    }

    /*
     * 历史交锋数据
     */
    public function getHistoryMatch($matchInfo)
    {
        // 检查缓存
        $redisKey = "{$this->redis['JC_MATCHHISTORY']}{$matchInfo['mid']}";
        $match = $this->cache->redis->get($redisKey);

        if(empty($match))
        {
            $match = $this->refreshHistoryMatch($matchInfo);
        }

        return $match;

    }

    /*
     * 处理并刷新历史交锋数据
     */
    public function refreshHistoryMatch($matchInfo)
    {
        $redisKey = "{$this->redis['JC_MATCHHISTORY']}{$matchInfo['mid']}";

        // 获取相同主客场 10场交锋
        $smatchInfo = array(
            'htid' => $matchInfo['htid'],
            'atid' => $matchInfo['atid'],
            'mtime' => date('Y-m-d H:i:s', $matchInfo['mtime'])
        );
        $smatch = $this->getMatchHistoryInfo($smatchInfo);

        // 获取相反主客场 10场交锋
        $omatchInfo = array(
            'htid' => $matchInfo['atid'],
            'atid' => $matchInfo['htid'],
            'mtime' => date('Y-m-d H:i:s', $matchInfo['mtime'])
        );
        $omatch = $this->getMatchHistoryInfo($omatchInfo);

        // 获取相同赛事 相同主客场 10场交锋
        $slmatchInfo = array(
            'htid' => $matchInfo['htid'],
            'atid' => $matchInfo['atid'],
            'lid' => $matchInfo['lid'],
            'mtime' => date('Y-m-d H:i:s', $matchInfo['mtime'])
        );        
        $slmatch = $this->getMatchHistoryInfo($slmatchInfo);

        // 获取相同赛事 相反主客场 10场交锋
        $olmatchInfo = array(
            'htid' => $matchInfo['atid'],
            'atid' => $matchInfo['htid'],
            'lid' => $matchInfo['lid'],
            'mtime' => date('Y-m-d H:i:s', $matchInfo['mtime'])
        );        
        $olmatch = $this->getMatchHistoryInfo($olmatchInfo);

        $match = array(
            'smatch' => $smatch ? $smatch : array(), 
            'omatch' => $omatch ? $omatch : array(),
            'slmatch' => $slmatch ? $slmatch : array(),
            'olmatch' => $olmatch ? $olmatch : array(),
        );
 
        // 存入缓存
        $match = json_encode($match);
        $this->cache->redis->save($redisKey, $match, 3600);

        return $match;

        
    }

    /*
     * 查询指定赛事的历史交锋数据
     */
    public function getMatchHistoryInfo($matchInfo)
    {
        $con = '';
        if($matchInfo['lid'])
        {
            $con .= 'AND lid = ? ';
        }
        $sql = "SELECT mid, sid, rid, ln, hteam, ateam, mtime, hscore, ascore, htid, atid, binfo FROM cp_jcMatch_history WHERE htid = ? AND atid = ? " . $con . "AND mtime < ? ORDER BY mtime DESC LIMIT 10";
        $match = $this->slaveCfg->query($sql, $matchInfo)->getAll();
        return $match;
    }

    /*
     * API 获取球队近期数据
     */
    public function getTeamState($tid, $type = 0)
    {
        $url = 'home/base/?oid=1015&tid=' . $tid . '&type=' . $type;
        $apiUrl = $this->apiUrl . $url;

        $matchData = $this->tools->request($apiUrl, array());
        $matchData = json_decode($matchData, true);

        $data = array();
        if(!empty($matchData['row']))
        {
            foreach ($matchData['row'] as $key => $items) 
            {
                $items['index'] = $key;
                $data[$items['mid']] = $items;
            }
        }
        return $data;
    }

    /*
     * API 获取球队未来赛事
     */
    public function getFutureMatch($tid, $nums = 3)
    {
        $url = 'home/base/?oid=1021&tid=' . $tid . '&nums=' . $nums;
        $apiUrl = $this->apiUrl . $url;

        $matchData = $this->tools->request($apiUrl, array());
        $matchData = json_decode($matchData, true);

        $data = array();
        if(!empty($matchData['row'][0]))
        {
            $data = $matchData['row'];
        }
        return $data;
    }

    /*
     * API 获取赛事欧赔亚赔列表
     */
    public function getOddList($type, $mid)
    {
        switch ($type)
        {
            case 'o':
                $oid = '2002';
                break;
            case 'y':
                $oid = '2003';
                break;
        }

        $url = 'home/base/?oid=' . $oid . '&mid=' . $mid;

        $redisKey = "{$this->redis['ODD_LIST']}{$oid}{$mid}";
        $redisData = $this->getRedisData($redisKey, $url, 10);

        $redisData = json_decode($redisData, true);

        $data = array();
        if(!empty($redisData['row'][0]))
        {
            $data = $redisData['row'];
        }
        return $data;
    }

    /*
     * API 获取赛事欧赔亚赔详情
     */
    public function getOddDetail($type, $cid, $mid)
    {
        switch ($type)
        {
            case 'o':
                $oid = '2013';
                break;
            case 'y':
                $oid = '2014';
                break;
        }

        $url = 'home/base/?oid=' . $oid . '&mid=' . $mid . '&comid=' . $cid;

        $redisKey = "{$this->redis['ODD_DETAIL']}{$oid}{$mid}{$cid}";
        $redisData = $this->getRedisData($redisKey, $url, 10);

        $redisData = json_decode($redisData, true);

        $data = array();
        if(!empty($redisData['row'][0]))
        {
            $data = $redisData['row'];
        }
        elseif(!empty($redisData['row']['ab']) || !empty($redisData['row']['oh']))
        {
            $data[0] = $redisData['row'];
        }
        return $data;
    }

    /*
     * API 获取赛事最新消息编号
     */
    public function getCurrentMsgId()
    {
        $url = 'home/base/?oid=3003';

        $redisKey = "{$this->redis['JC_MATCHMSGID']}";
        $redisData = $this->getRedisData($redisKey, $url, 10);

        $redisData = json_decode($redisData, true);

        $msgId = $redisData['c']['fnum'];

        return $msgId;
    }

    /*
     * API 获取赛事即时比分最新信息 3007
     */
    public function getLiveNew()
    {
        $url = 'home/base/?oid=3007';

        $redisKey = "{$this->redis['JC_LIVENEW']}";
        $redisData = $this->getRedisData($redisKey, $url, 10);

        $redisData = json_decode($redisData, true);

        $data = array(
            'msg' => array(
                'msgId' => $redisData['c']['fn'],
                'reset' => $redisData['c']['reset']
            ),
            'detail' => array()
        );

        if(!empty($redisData['row'][0]))
        {
            $data['detail'] = $redisData['row'];
        }
        elseif(!empty($redisData['row']['bh']))
        {
            $data['detail'][0] = $redisData['row'];
        }

        return $data;
    }

    /*
     * API 获取指定消息编号的即时比分最新信息 3008
     */
    public function getMsgLiveDetail($msgId)
    {
        $url = 'home/base/?oid=3008&fn=' . $msgId;

        $redisKey = "{$this->redis['JC_LIVEDETAIL']}$msgId";
        $redisData = $this->getRedisData($redisKey, $url, 600);

        $redisData = json_decode($redisData, true);

        $data = array();
        if(!empty($redisData['row'][0]))
        {
            $data = $redisData['row'];
        }
        elseif(!empty($redisData['row']['bh']))
        {
            $data[0] = $redisData['row'];
        }

        return $data;
    }

}