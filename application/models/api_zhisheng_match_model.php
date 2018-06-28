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
    }

    /*
     * 刷新接口数据
     */
    public function refreshApi($url)
    {
        $this->config->load('jcMatch');
        $apiUrl = $this->config->item('apiUrl') . $url;

        $content = $this->tools->request($apiUrl, array());
        $content = json_decode($content, true);

        return $content;
    }

    /*
     * 获取竞彩足球在售期次信息
     */
    public function getJczqMatch()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }

    /*
     * 处理竞彩足球对阵数据
     */
    public function setJczqMatch($expect, $lottid = 6)
    {
        // 获取【足彩 lottid 6】对阵数据 
        $matchData = $this->getZcMatch($expect);

        $match = array();
        // 由于接口数据一条和多条格式不一致 做区分处理
        if(!empty($matchData['r']) && !empty($matchData['c']['expect']))
        {
            if($matchData['c']['num'] == 1 && empty($matchData['r'][0]))
            {
                $matchArry[0] = $matchData['r'];
            }
            else
            {
                $matchArry = $matchData['r'];
            }

            $year = substr(date('Y'), 0, 2);
            foreach ($matchArry as $key => $items) 
            {
                $index = $year . $items['xid'];
                // 联赛排名
                $rankData = $this->analyTeamRank($items);

                $match[$index] = array(
                    'mid' => $items['mid'],
                    'expect' => $matchData['c']['expect'],
                    'ln' => $items['ln'],
                    'hid' => $items['hid'],
                    'aid' => $items['aid'],
                    'hn' => $items['hn'],
                    'an' => $items['an'],
                    'mt' => $items['mt'],
                    'lid' => $items['lid'],
                    // 历史交锋数据
                    'his' => $this->analyMatchHistory($items['mid'], $items['hid'], $items['aid']),
                    // 主客队近期状态
                    'hstate' => $this->analyTeamState($items['mid'], $items['hid']),
                    'astate' => $this->analyTeamState($items['mid'], $items['aid']),
                    // 联赛排名
                    'hrank' => $rankData['hrank'],
                    'arank' => $rankData['arank'],
                );
            }
        }
        return $match;
    }

    /*
     * API 获取足彩对阵数据
     */
    public function getZcMatch($expect, $lottid = 6)
    {
        $url = 'home/base/?oid=2018&lottid=' . $lottid . '&expect=' . $expect;
 
        $matchData = $this->refreshApi($url);

        return $matchData;
    }

    /*
     * 分析历史交锋数据
     */
    public function analyMatchHistory($mid, $hid, $aid)
    {
        // 获取历史交锋数据
        $data = $this->getMatchHistory($mid);

        // 初始化交锋数据
        $alalyData = array(
            'w' => 0,
            'd' => 0,
            'l' => 0,
            'goal' => 0,
            'loss' => 0
        );

        if(!empty($data))
        {
            // 获取所选场次之前的历史对阵
            if(!empty($data[$mid]))
            {
                $historyData = array_slice($data, $data[$mid]['index'] + 1, 10);
            }
            else
            {
                $historyData = array_slice($data, 0, 10);
            }

            // 分析主队 胜、平、负、进、失球
            $alalyArr = array();
            // 拆分对阵格式 主队id - 主队score ,客队id - 客队score
            foreach ($historyData as $key => $items) 
            {
                $alalyArr[$key][$items['htid']] = $items['hscore'];
                $alalyArr[$key][$items['atid']] = $items['ascore'];
            }

            foreach ($alalyArr as $k => $detail) 
            {
                if($detail[$hid] == $detail[$aid])
                {
                    $alalyData['d'] = $alalyData['d'] + 1;
                }
                else
                {
                    if($detail[$hid] > $detail[$aid])
                    {
                        $alalyData['w'] = $alalyData['w'] + 1;
                    }
                    else
                    {
                        $alalyData['l'] = $alalyData['l'] + 1;
                    }
                }
                
                $alalyData['goal'] = $alalyData['goal'] + $detail[$hid];
                $alalyData['loss'] = $alalyData['loss'] + $detail[$aid];
            }
        }

        $alalyData = array_values($alalyData);
        return implode(',', $alalyData);
    }

    /*
     * API 获取历史交锋数据
     */
    public function getMatchHistory($mid)
    {
        $url = 'home/base/?oid=1017&mid=' . $mid;

        $matchData = $this->refreshApi($url);

        $data = array();
        if(!empty($matchData['row']))
        {
            // 历史对阵数据入库
            $fields = array('mid', 'sid', 'rid', 'ln', 'lid', 'hteam', 'ateam', 'mtime', 'hscore', 'ascore', 'bc', 'bet', 'binfo', 'htid', 'atid', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

            // 由于接口数据一条和多条格式不一致 做区分处理
            if(empty($matchData['row'][0]))
            {
                $matchArry[0] = $matchData['row'];
            }
            else
            {
                $matchArry = $matchData['row'];
            }

            foreach ($matchArry as $key => $items) 
            {
                $items['index'] = $key;
                $items['mtime'] = date('Y-m-d H:i:s', $items['mtime']);
                $data[$items['mid']] = $items;

                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $items['mid']);
                array_push($bdata['d_data'], $items['sid']);
                array_push($bdata['d_data'], $items['rid']);
                array_push($bdata['d_data'], $items['ln']);
                array_push($bdata['d_data'], $items['lid']);
                array_push($bdata['d_data'], $items['hteam']);
                array_push($bdata['d_data'], $items['ateam']);
                array_push($bdata['d_data'], $items['mtime']);
                array_push($bdata['d_data'], $items['hscore']);
                array_push($bdata['d_data'], $items['ascore']);
                array_push($bdata['d_data'], $items['bc']);
                array_push($bdata['d_data'], $items['bet']);
                array_push($bdata['d_data'], $items['binfo']);
                array_push($bdata['d_data'], $items['htid']);
                array_push($bdata['d_data'], $items['atid']);
            }
            // 历史对阵入库
            $this->insertMatchHistory($fields, $bdata);
        }
        return $data;        
    }

    /*
     * 历史交锋数据入库
     */
    public function insertMatchHistory($fields, $bdata)
    {
        if(!empty($bdata['s_data']))
        {
            $upd = array('hteam', 'ateam', 'mtime', 'lid', 'hscore', 'ascore', 'bc', 'bet');
            $sql = "insert ignore cp_jcMatch_history(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
            $this->cfgDB->query($sql, $bdata['d_data']);
        }
    }

    /*
     * 分析球队近期状态
     */
    public function analyTeamState($mid, $tid)
    {
        // 获取球队近期数据
        $data = $this->getTeamState($tid);

        // 初始化交锋数据
        $alalyData = array(
            'w' => 0,
            'd' => 0,
            'l' => 0
        );

        if(!empty($data))
        {
            // 获取所选场次之前的近期状态
            if(!empty($data[$mid]))
            {
                $stateData = array_slice($data, $data[$mid]['index'] + 1, 10);
            }
            else
            {
                $stateData = array_slice($data, 0, 10);
            }

            // 分析球队 胜、平、负
            $alalyArr = array();

            if(!empty($stateData))
            {
                foreach ($stateData as $key => $items) 
                {
                    if($items['htid'] == $tid)
                    {
                        if($items['hscore'] > $items['ascore'])
                        {
                            $alalyData['w'] = $alalyData['w'] + 1;
                        }
                        elseif($items['hscore'] == $items['ascore'])
                        {
                            $alalyData['d'] = $alalyData['d'] + 1;
                        }
                        else
                        {
                            $alalyData['l'] = $alalyData['l'] + 1;
                        }
                    }
                    else
                    {
                        if($items['hscore'] > $items['ascore'])
                        {
                            $alalyData['l'] = $alalyData['l'] + 1;
                        }
                        elseif($items['hscore'] == $items['ascore'])
                        {
                            $alalyData['d'] = $alalyData['d'] + 1;
                        }
                        else
                        {
                            $alalyData['w'] = $alalyData['w'] + 1;
                        }
                    }
                }
            }
        }

        $alalyData = array_values($alalyData);
        return implode(',', $alalyData);
    }

    /*
     * API 获取球队近期数据
     */
    public function getTeamState($tid, $type = 0)
    {
        $url = 'home/base/?oid=1015&tid=' . $tid . '&type=' . $type;

        $matchData = $this->refreshApi($url);

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
     * 分析球队排名
     */
    public function analyTeamRank($matchInfo)
    {
        $rankRes = array(
            'hrank' => '',
            'arank' => ''
        );

        $randResult = '';
        // 获取联赛最新一期的赛程
        $sidResult = $this->getLastSid($matchInfo['lid']);

        if(empty($sidResult))
        {
            return $randResult;
        }

        $sidData = current($sidResult);

        // 获取积分榜
        $rankData = $this->getScoreRank($matchInfo['lid'], $sidData['sid']);

        $rankArr = array();
        if(!empty($rankData['row']) && !empty($rankData['c']['type']))
        {
            // 联赛展示联赛排名 杯赛暂不处理
            if($rankData['c']['type'] == 'league')
            {
                foreach ($rankData['row'] as $key => $items) 
                {
                    $items['rank'] = (string)($key + 1);
                    $rankArr[$items['tid']] = $items;
                }

                $rankRes = array(
                    'hrank' => $rankArr[$matchInfo['hid']]['rank'],
                    'arank' => $rankArr[$matchInfo['aid']]['rank'],
                );
            }

            if($rankData['c']['type'] == 'cup')
            {
                $rankArray = array();
                $rankGroup = array();
                foreach ($rankData['row'] as $key => $items) 
                {
                    $rankArray[$items['group']][] = $items;
                    $rankGroup[$items['tid']] = $items;
                }

                if($rankGroup[$matchInfo['hid']]['group'] == $rankGroup[$matchInfo['aid']]['group'])
                {
                    $groupArr = $rankArray[$rankGroup[$matchInfo['hid']]['group']];

                    foreach ($groupArr as $key => $items) 
                    {
                        $items['rank'] = (string)($key + 1);
                        $rankArr[$items['tid']] = $items;
                    }

                    $rankRes = array(
                        'hrank' => $rankArr[$matchInfo['hid']]['rank'],
                        'arank' => $rankArr[$matchInfo['aid']]['rank'],
                    );
                }
            }
        }

        return $rankRes;
    }

    /*
     * API 获取联赛最新一期的赛程
     */
    public function getLastSid($lid)
    {
        $url = 'home/base/?oid=1003&lid=' . $lid;

        $matchData = $this->refreshApi($url);

        $data = array();
        if(!empty($matchData['row']))
        {
            $data = $matchData['row'];
        }
        return $data;
    }

    /*
     * API 获取联赛积分榜
     */
    public function getScoreRank($lid, $sid)
    {
        $url = 'home/base/?oid=1007&lid=' . $lid . '&sid=' . $sid;
        $data = $this->refreshApi($url);
        return $data;
    }

    /*
     * 缓存数据
     */
    public function saveJczqMatch($matchData)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->cache->save($REDIS['JCZQ_HISTORY'], json_encode($matchData), 0);
    }

    /*
     * 获取投注页赛事历史缓存
     */
    public function getJczqHistory()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_HISTORY']}";
        $match = $this->cache->redis->get($ukey);
        $match = json_decode($match, true);
        return $match;
    }

    /*
     * API 获取联赛积分榜
     */
    public function getMatchSchedule($lid, $sid)
    {
        $url = 'home/base/?oid=1004&lid=' . $lid . '&sid=' . $sid;
        $data = $this->refreshApi($url);
        return $data;
    }

    /*
     * 五大联赛积分榜缓存
     */
    public function saveEuropeScoreRank($lid, $data)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['EUROPE_SCORE']}$lid";
        $this->cache->save($ukey, json_encode($data), 0);
    }

    /*
     * 五大联赛赛程缓存
     */
    public function saveEuropeSchedule($lid, $data)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['EUROPE_SCHEDULE']}$lid";
        $this->cache->save($ukey, json_encode($data), 0);
    }
    
}