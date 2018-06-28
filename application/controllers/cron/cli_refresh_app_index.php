<?php

/**
 * 移动端首页缓存
 * @date:2017-10-25
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Refresh_App_Index extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->library('BetCnName');
        $this->load->model('lottery_model');
        $this->load->model('app/model_index', 'app_model_index');
        $url_prefix = $this->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';
    }

    // 平台
    private $platform = array(
        'android'   =>  '1', 
        'ios'       =>  '2',
        'm'         =>  '3',
    );

    // 首页展示顺序
    private $indexArr = array(
        'banner'    =>  '',
        'notice'    =>  '',
        'top'       =>  '',     // 顶部活动模块
        'lottery'   =>  '',
        'buttom'    =>  '',     // 底部活动模块
    );

    private $topArr = array(
        '1' =>  'zhbzbp',
        '2' =>  'jc',
        '3' =>  'szc',
        '4' =>  'zdy',
        '7' =>  'zdy',
        '8' =>  'zdy',
    );

    private $buttomArr = array(
        '5' =>  'zdy',
        '6' =>  'zdy',
        '9' =>  'zdy',
    );

    // 竞彩足球玩法
    private $jczqPlayArr = array(
        '1' =>  '胜平负',
        '2' =>  '让球胜平负',
        '3' =>  '单关',
        '4' =>  '总进球',
        '5' =>  '比分',
        '6' =>  '半全场',
    );

    public function index()
    {
        foreach ($this->platform as $plat => $platId) 
        {
            if ($platId == 3) $this->banner('m');
            else $this->refreshIndex($plat, $platId);
        }
    }

    public function refreshIndex($platform, $platId)
    {
        // 获取活动模块信息
        $activitys = $this->app_model_index->getActivityInfo($platId);

        $index = array();
        foreach ($this->indexArr as $fun => $value) 
        {
            if(in_array($fun, array('top', 'buttom')) && !empty($activitys))
            {
                $fun = 'get' . $fun;
                $results = $this->$fun($activitys);
                if(!empty($results))
                {
                    foreach ($results as $items) 
                    {
                        $index[] = array(
                            'type'      =>  $items['typeName'],
                            'content'   =>  array(
                                $items['typeName'] => $items['data']
                            )
                        );
                    }    
                }
            }
            else
            {
                if(method_exists($this, $fun))
                {
                    $result = $this->$fun($platform);
                    if(!empty($result))
                    {
                        $index[] = array(
                            'type'      =>  $fun,
                            'content'   =>  array(
                                $fun => $result
                            )
                        );
                    }
                }   
            }  
        }

        if(!empty($index))
        {
            $this->app_model_index->saveIndex($platform, $index);
        }
    }

    // url地址处理
    public function urlFormat($url)
    {
        return (strpos($url, 'http') !== FALSE) ? $url : 'https:' . $url;
    }

    // 首页轮播图
    public function banner($platform)
    {
        $info = $this->app_model_index->getAddInfo($platform, $this->platform[$platform]);
        $this->app_model_index->freshQdyInfo($platform, $this->platform[$platform]);
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                // 轮播图竞彩足球区分玩法
                $info[$key]['playTypeName'] = '';   // 安卓处理
                $info[$key]['playTypeId'] = '0';    // IOS处理
                if($items['lid'] == '42' && !empty($items['extra']))
                {
                    $extra = json_decode($items['extra'], true);
                    if($this->jczqPlayArr[$extra['playType']])
                    {
                        $info[$key]['playTypeName'] = $this->jczqPlayArr[$extra['playType']];
                        $info[$key]['playTypeId'] = $extra['playType'];
                    }
                }
                $info[$key]['imgUrl'] = $this->urlFormat($items['imgUrl']);
            }
        }
        return $info;
    }
    
    // 首页中奖信息
    public function notice($platform)
    {
        // 获取中奖缓存
        $info = $this->app_model_index->getWins();

        $awardInfo = array();
        if(!empty($info['orderInfo']))
        {
            $info['orderInfo'] = array_slice($info['orderInfo'], 0, 200);

            foreach ($info['orderInfo'] as $key => $orders) 
            {
                $awardInfo[$key]['detail'] = "恭喜 " . mb_substr($orders['nick_name'], 0, 4) . "*** 投注" . BetCnName::getCnName($orders['lid']) . "中奖" . number_format(ParseUnit($orders['margin'], 1), 2) . "元";
            }
        }

        $data = array(
            'statistics' => $info['count']['margin'] ? number_format(ParseUnit($info['count']['margin'], 1), 2) : '0',
            'info' => $awardInfo
        );

        return $data;
    }

    // 首页彩种信息
    public function lottery($platform)
    {
        $lotterys = $this->app_model_index->getAllLotterys($platform);

        $lotteryConfig = $this->app_model_index->getlotteryConfig();
        
        if(!empty($lotterys))
        {
            // 获取彩种奖池信息
            $awardInfo = $this->getLotteryAwards();

            // 奖池信息
            foreach ($lotterys as $key => $items)
            {
                if(in_array($items['lid'], array('42', '43')))
                {
                    $lotterys[$key]['awardPool'] = $this->getCountJc($items['lid']);      
                }
                else
                {
                    $lotterys[$key]['awardPool'] = $this->moneyFormat($awardInfo[$items['lid']]['awardPool']);
                }

                // 今日开奖
                $lotterys[$key]['kaijiang'] = $this->getKjinfo($items['lid']);

                // 加奖标识
                $lotterys[$key]['attachFlag'] = (($items['attachFlag'] & 1) == 1) ? '1' : '0';
                // 副标题标红
                $lotterys[$key]['memoFlag'] = (($items['attachFlag'] & 2) == 2) ? '1' : '0';
                // 停开售 竞足单关同竞足
                $lotteryId = ($items['lid'] == '4201') ? '42' : $items['lid'];
                $lotterys[$key]['isSale'] = ($lotteryConfig[$lotteryId]['status'] || in_array($lotteryId, array(1, 2))) ? '1' : '0';
            }

            $lotterys = $this->handleLottery($lotterys);
        }
        return $lotterys;
    }

    // 彩种配置处理
    public function handleLottery($lotteryInfo = array())
    {
        $baseInfo = array();
        $subInfo = array();
        $info = array();
        if(!empty($lotteryInfo))
        {
            foreach ($lotteryInfo as $lottery) 
            {
                $plid = $lottery['plid'];
                if($plid > 0)
                {
                    $subInfo[$plid ][] = $lottery;
                }
                else
                {
                    $lottery['subs'] = array();
                    $baseInfo[$lottery['lid']] = $lottery;
                }
            }
            if(!empty($subInfo))
            {
                foreach ($subInfo as $pid => $items) 
                {
                    if(!empty($baseInfo[$pid]))
                    {
                        $baseInfo[$pid]['subs'] = $items;
                    }
                }
            }
            if(!empty($baseInfo))
            {
                foreach ($baseInfo as $lid => $items) 
                {
                    $info[] = $items;
                }
            }
        }
        return $info;
    }

    // 近期开奖
    public function getLotteryAwards()
    {
        $this->load->model('award_model', 'Award');
        $awardData = $this->Award->getCurrentAward();

        $awardInfo = array();
        if(!empty($awardData))
        {
            foreach ($awardData as $items) 
            {
                $awardInfo[$items['seLotid']] = $items;
            }
        }
        return $awardInfo;
    }

    // 统计竞彩在售场次
    public function getCountJc($lid)
    {
        $count = 0;
        // 数据中心
        $info = $this->app_model_index->getJjcInfo($lid);
        if(!empty($info))
        {
            // 全部玩法均被停售
            if($lid == 42)
            {
                foreach ($info as $key => $items) 
                {
                    if( empty($items['spfGd']) && empty($items['rqspfGd']) && empty($items['bqcGd']) && empty($items['jqsGd']) && empty($items['bfGd']) && empty($items['spfFu']) && empty($items['rqspfFu']) && empty($items['bqcFu']) && empty($items['jqsFu']) && empty($items['bfFu']) )
                    {
                        // 过滤该场比赛
                    }
                    else
                    {
                        $count ++;
                    }
                }
            }
            else
            {
                foreach ($info as $key => $items) 
                {
                    if( empty($items['rfsfGd']) && empty($items['sfGd']) && empty($items['sfcGd']) && empty($items['dxfGd']) && empty($items['sfFu']) && empty($items['rfsfFu']) && empty($items['sfcFu']) && empty($items['dxfFu']) )
                    {
                        // 过滤该场比赛
                    }
                    else
                    {
                        $count ++;
                    }
                }
            }
        }
        return $count . '场比赛在售';
    }

    // 金额格式处理
    public function moneyFormat($pool)
    {
        if(empty($pool))
        {
            return '更新中...';
        }

        // 取整
        $poolArry = explode('.', $pool);
        $pool = $poolArry[0];

        $unit = array('', '万', '亿');
        $tpl = "";
        if(is_numeric($pool) && !empty($pool))
        {
            $temp = str_split(strrev(floatval($pool)), 4);
            // 升序
            krsort($temp);
            if(isset($temp[2]))
            {
                $temp[0] = '0000';
            }
            foreach ($temp as $key => $items) 
            {
                if(!isset($unit[$key]))
                {
                    $tpl .= intval(strrev($items));
                }
                else
                {
                    $num = intval(strrev($items));
                    if(!empty($num) || $key == 2)
                    {
                        $str = $num . $unit[$key];
                        $tpl .= $str;
                    }             
                }
            }
        }
        else
        {
            $tpl .= 0;
        }
        if(count($temp) <= 2)
        {
            $tpl .= "元";
        }       
        return $tpl;
    }

    // 正在开奖
    public function getKjinfo($lid)
    {
        $result = '0';
        if(in_array($lid, array('51', '23529')))
        {
            $info = $this->lottery_model->getKjinfo($lid);

            if(!empty($info['next']['seEndtime']) && ( date('Y-m-d') == date('Y-m-d',substr($info['next']['seEndtime'], 0, 10)) ) && ( date('Y-m-d H:i:s') <= date('Y-m-d H:i:s',substr($info['next']['seEndtime'], 0, 10)) ))
            {
                $result = '1';
            }
        }
        return $result;
    }

    // 顶部活动
    public function gettop($info)
    {
        $data = array();
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                if(in_array($items['type'], array_keys($this->topArr)))
                {
                    $fun = $this->topArr[$items['type']];
                    $result = $this->$fun($items);
                    if(!empty($result))
                    {
                        $data[] = array(
                            'typeName'  =>  $fun,
                            'data'      =>  $result,
                        );
                    }
                }
            } 
        }
        return $data;
    }

    // 活动 - 追号不中包赔
    public function zhbzbp($info)
    {
        $extra = json_decode($info['extra'], true);

        // 获取当前期次投注信息
        $issueInfo = $this->getIssueInfo($extra['lid']);

        $urlMap = ($info['platform'] == '1') ? 'app' : 'ios';

        $lnameArr = array(
            '51'        =>  '双色球',
            '23529'     =>  '大乐透',
        );

        $data = array();
        if(!empty($issueInfo))
        {
            $lname = $lnameArr[$issueInfo['lid']] ? $lnameArr[$issueInfo['lid']] : '';
            $data = array(
                'lid'           =>  $issueInfo['lid'],
                'chaseType'     =>  (string)($extra['issue']/10),
                'content'       =>  $lname . $extra['content'],
                'url'           =>  'https://www.ka5188.com/' . $urlMap . '/event/zhbzbp',
                'totalIssue'    =>  $extra['issue'],
                'issue'         =>  $issueInfo['issue'],
                'endTime'       =>  $issueInfo['endTime'],
                'channels'      =>  $info['channels'],
            );
        }
        return $data;
    }

    // 获取数字彩期次信息
    public function getIssueInfo($lid = 0)
    {
        $issueInfo = array();
        if($lid)
        {
            $info = $this->app_model_index->getLotterySale($lid);
        }
        else
        {
            $info = $this->getDefaultByLid();
        }

        if(!empty($info))
        {
            $issueInfo = array(
                'issue'     =>  $info['seExpect'],
                'lid'       =>  $info['seLotid'],
                'endTime'   =>  date('Y-m-d H:i:s',substr($info['seFsendtime'], 0, 10)),
            );
        }
        return $issueInfo;
    }

    // 获取默认投注彩种
    public function getDefaultByLid()
    {
        $ssq = $this->app_model_index->getLotterySale(51);
        $dlt = $this->app_model_index->getLotterySale(23529);

        if($dlt['seFsendtime'] > 0 && $dlt['seFsendtime'] < $ssq['seFsendtime'])
        {
            $info = $dlt;
        }
        elseif($ssq['seFsendtime'] > 0 && $ssq['seFsendtime'] < $dlt['seFsendtime'])
        {
            $info = $ssq;
        }
        else
        {
            $info = array();
        }
        return $info;
    }

    // 活动 - 竞彩投注
    public function jc($info)
    {
        $extra = json_decode($info['extra'], true);
        $matches = $this->app_model_index->getJjcInfo($extra['lid']);
        // 检查场次信息
        $matchInfo = array();
        $count = 0;
        if(!empty($extra['mid']) && !empty($matches) && in_array($extra['lid'], array(42, 43)))
        {
            $midArr = explode(',', $extra['mid']);
            $matchData = array();
            foreach ($midArr as $mid) 
            {
                if(!empty($matches[$mid]))
                {
                    $matchData[] = $this->getJcDetail($extra['lid'], $matches[$mid]);
                    // 竞足胜平负玩法是否开售
                    if($extra['lid'] == 42)
                    {
                        if((count($midArr) == 1 && $matches[$mid]['spfFu']) || (count($midArr) == 2 && $matches[$mid]['spfGd']))
                        {
                            $count ++; 
                        } 
                    }
                    // 竞篮胜负玩法是否开售
                    if($extra['lid'] == 43)
                    {
                        if((count($midArr) == 1 && $matches[$mid]['sfFu']) || (count($midArr) == 2 && $matches[$mid]['sfGd']))
                        {
                            $count ++; 
                        } 
                    }    
                }
            }

            if(count($midArr) != $count || $count > 2)
            {
                $matchData = array();
            }

            if(!empty($matchData))
            {
                // 截止时间
                $endTime = '';
                foreach ($matchData as $key => $items) 
                {
                    if(empty($endTime))
                    {
                        $endTime = $items['endTime'];
                    }
                    elseif($endTime > $items['endTime'])
                    {
                        $endTime = $items['endTime'];
                    }
                }
                $matchInfo = array(
                    'lid'       =>  $extra['lid'],
                    'playType'  =>  'c' . count($midArr),
                    'endTime'   =>  $endTime,
                    'content'   =>  $this->getDayInfo($endTime),
                    'match'     =>  $matchData,
                    'channels'  =>  $info['channels'],
                );
            }            
        }
        return $matchInfo;        
    }

    public function getJcDetail($lid, $match)
    {
        // 获取球队Logo
        $history = $this->app_model_index->getJjcHistory($lid);

        $jcMid = $history[$match['mid']]['mid'];
        if($jcMid)
        {
            $detail = $this->app_model_index->getMatchDetail($lid, $jcMid);
        }

        // 2018世界杯赛程 主客队logo替换
        if($detail['lid'] == '149' && $detail['sid'] == '7574')
        {
            $staticImgUrl = $this->config->item('img_url');
            shuffle($staticImgUrl);
            if($detail['htid'] > 0) 
            {
                $detail['homelogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $staticImgUrl[0] . 'cpiaoimg/zqlogo/appsjb/' . $detail['htid'] . '.png';
            }
            if($detail['atid'] > 0) 
            {
                $detail['awaylogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $staticImgUrl[0] . 'cpiaoimg/zqlogo/appsjb/' . $detail['atid'] . '.png';
            }
        }

        if($lid == 42)
        {
            $info = array(
                'issue'     =>  $match['issue'],
                'mid'       =>  $match['mid'],
                'name'      =>  $match['nameSname'],
                'home'      =>  $match['homeSname'],
                'homeLogo'  =>  $detail['homelogo'],
                'awary'     =>  $match['awarySname'],
                'awaryLogo' =>  $detail['awaylogo'],
                'spfSp3'    =>  $match['spfSp3'] ? $match['spfSp3'] : 0,
                'spfSp1'    =>  $match['spfSp1'] ? $match['spfSp1'] : 0,
                'spfSp0'    =>  $match['spfSp0'] ? $match['spfSp0'] : 0,
                'endTime'   =>  date('Y-m-d H:i:s',substr($match['jzdt'], 0, 10)),
            );
        }
        else
        {
            $info = array(
                'issue'     =>  $match['issue'],
                'mid'       =>  $match['mid'],
                'name'      =>  $match['nameSname'],
                'home'      =>  $match['homeSname'],
                'homeLogo'  =>  $detail['homelogo'],
                'awary'     =>  $match['awarySname'],
                'awaryLogo' =>  $detail['awaylogo'],
                'spfSp3'    =>  $match['sfHs'] ? $match['sfHs'] : 0,
                'spfSp0'    =>  $match['sfHf'] ? $match['sfHf'] : 0,
                'endTime'   =>  date('Y-m-d H:i:s',substr($match['jzdt'], 0, 10)),
            );
        }
        return $info;
    }

    // 活动 - 数字彩
    public function szc($info)
    {
        $extra = json_decode($info['extra'], true);

        // 获取当前期次投注信息
        $issueInfo = $this->getIssueInfo($extra['lid']);

        // 文案说明
        $mark = array(
            '51'    =>  array(
                'tag'       =>  '幸运号码，来一注',
                'content'   =>  '每周二、四、日开奖',
            ),
            '23529' => array(
                'tag'       =>  '幸运号码，来一注',
                'content'   =>  '每周一、三、六开奖',
            ),
        );

        $data = array();
        if(!empty($issueInfo))
        {
            $data = array(
                'lid'           =>  $issueInfo['lid'],
                'tag'           =>  $mark[$issueInfo['lid']]['tag'],
                'content'       =>  $mark[$issueInfo['lid']]['content'],
                'issue'         =>  $issueInfo['issue'],
                'endTime'       =>  $issueInfo['endTime'],
                'channels'      =>  $info['channels'],
            );
        }
        return $data;
    }

    // 活动 - 自定义底部banner
    public function zdy($info)
    {
        $extra = json_decode($info['extra'], true);

        $platform = array_flip($this->platform);

        $data = array(
            'imgUrl'    =>  $this->config->item('base_url') . '/uploads/appconfig/' . $platform[$info['platform']] . '/banner/' . $extra['imgUrl'],
            'url'       =>  (strpos($extra['url'], 'http') !== FALSE) ? $extra['url'] : ($this->url_prefix . ':' . $extra['url']),
            'lid'       =>  $extra['lid'],
            'playTypeName'  =>  ($extra['lid'] == '42' && $extra['playType'] && $this->jczqPlayArr[$extra['playType']]) ? $this->jczqPlayArr[$extra['playType']] : '',
            'playTypeId'    =>  ($extra['lid'] == '42' && $this->jczqPlayArr[$extra['playType']]) ? $extra['playType'] : '0',
            'channels'  =>  $info['channels'],
        );
        return $data;
    }

    // 底部活动
    public function getbuttom($info)
    {
        $data = array();
        if(!empty($info))
        {
           foreach ($info as $items) 
            {
                if(in_array($items['type'], array_keys($this->buttomArr)))
                {
                    $fun = $this->buttomArr[$items['type']];
                    $result = $this->$fun($items);
                    if(!empty($result))
                    {
                        $data[] = array(
                            'typeName'  =>  $fun,
                            'data'      =>  $result,
                        );
                    }
                }
            } 
        }
        return $data;
    }

    public function getDayInfo($date)
    {
        $nowTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d', strtotime($date)));
        $dif = intval(($endTime-$nowTime)/3600/24);
        $dayInfo = array('今天', '明天');

        $info = '';
        if(isset($dayInfo[$dif]))
        {
            $info .= $dayInfo[$dif] . ' ' . date('H:i', strtotime($date));
        }
        else
        {
            $info .= date('m-d H:i', strtotime($date));
        }
        $info .= ' 截止';
        return $info;
    }
}