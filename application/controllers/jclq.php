<?php

class Jclq extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('api_zhisheng_model', 'dataSource');
    }

    public function hh()
    {
        $this->deliveryType('hh');
    }

    private function roundRobin($matchAry, $type)
    {
        if (empty($matchAry))
        {
            return array(array(), array());
        }

        $matches = array();
        $leagues = array();
        $issues = array();
        $weekDays = array('日', '一', '二', '三', '四', '五', '六');
        $apiUrl = $this->config->item('api_bf');
        $detailUrl = $this->config->item('api_info');
        $oddsUrl = $this->config->item('api_odds');
        foreach ($matchAry as $key => $match)
        {
            $mid = $match['mid'];
            $time = strtotime(substr($mid, 0, 8));
            $date = date('Y-m-d', $time);
            $dayIndex = date('w', $time);
            $match['weekId'] = '周' . $weekDays[$dayIndex] . substr($mid, 8);
            if ( ! empty($match['homeSname']))
            {
                $match['home'] = $match['homeSname'];
            }
            if ( ! empty($match['awarySname']))
            {
                $match['awary'] = $match['awarySname'];
            }
            if ( ! empty($match['nameSname']))
            {
                $match['name'] = $match['nameSname'];
            }
            $dateWithWeek = $date . ' 周' . $weekDays[$dayIndex];
            if ( ! isset($matches[$dateWithWeek]))
            {
                $matches[$dateWithWeek] = array();
            }
            $match['hTid'] = 0;
            $match['aTid'] = 0;
            $match['hRank'] = 0;
            $match['aRank'] = 0;
            $match['oh'] = '0.00';
            $match['oa'] = '0.00';
            $zhisheng = json_decode($match['zhisheng'], true);
            $match['hTid'] = $zhisheng['htid'];
            $match['hDetail'] = $detailUrl . 'lanqiu/' . $zhisheng['sid'] . '/team/' . $match['hTid'];
            $match['aTid'] = $zhisheng['atid'];
            $match['aDetail'] = $detailUrl . 'lanqiu/' . $zhisheng['sid'] . '/team/' . $match['aTid'];
            $match['hRank'] = preg_replace('/[^\d]/', '', $zhisheng['hpm']);
            $match['aRank'] = preg_replace('/[^\d]/', '', $zhisheng['apm']);
            $match['oddsUrl'] = $oddsUrl;
            $match['queryMId'] = $zhisheng['mid'];
            $match['oh'] = ( ! empty($zhisheng['odds']['oh']) && is_numeric($zhisheng['odds']['oh']))
                ? $zhisheng['odds']['oh']
                : '0.00';
            $match['oa'] = ( ! empty($zhisheng['odds']['oa']) && is_numeric($zhisheng['odds']['oa']))
                ? $zhisheng['odds']['oa']
                : '0.00';
            $matches[$dateWithWeek][] = $match;
            if ($this->isValidMatch($match, $type))
            {
                $leagues[] = $match['name'];
            }
            if ( ! empty($match['issue']) && ! in_array($match['issue'], $issues))
            {
                $issues[] = $match['issue'];
            }
        }

        if (empty($issues))
        {
            return array($matches, $leagues);

        }
        if (empty($apiUrl))
        {
            return array($matches, $leagues);
        }

        $issueToContent = array();
        foreach ($issues as &$issue)
        {
            $issue = str_replace('-', '', $issue);
            $issueToContent[] = $issue;
        }

        if (empty($issueToContent))
        {
            return array($matches, $leagues);
        }
        
        return array($matches, $leagues, $issueToContent);
    }

    private function isValidMatch($match, $type)
    {
        if ($type == 'hh')
        {
            return $match['sfGd'] OR $match['dxfGd'] OR $match['sfcGd'] OR $match['rfsfGd'];
        }
        else
        {
            return $match[$type . 'Gd'];
        }
    }

    private function withMatchCount($leagues)
    {
        $leagueCount = array_count_values($leagues);
        $leagues = array_flip(array_unique($leagues));
        $leaguesNew = $leagues;
        foreach (array_keys($leagues) as $key)
        {
            $leaguesNew[$key] = $key . '(' . $leagueCount[$key] . '场)';
        }

        return array($leagues, $leaguesNew);
    }

    private function deliveryType($jclqType)
    {
        $date = $this->input->get('date', TRUE);
        $idp = $this->input->get('midp', TRUE);
        $multiple = $this->input->get('multiple', TRUE);
        $sf = $this->input->get('sf', TRUE);
        $rfsf = $this->input->get('rfsf', TRUE);
        $dxf = $this->input->get('dxf', TRUE);
        $idn = $this->input->get('midn', TRUE);
        $rfsfn = $this->input->get('rfsfn', TRUE);
        is_numeric($multiple) && $multiple >= 1 ? ($multiple = (int)$multiple) : ($multiple = 1);
        $stakeStr = $this->input->post('stakeStr', TRUE);
        $sfcOptions = array(
            '15'   => '1-5',
            '610'  => '6-10',
            '1115' => '11-15',
            '1620' => '16-20',
            '2125' => '21-25',
            '26'   => '26+',
        );

        $typeMAP = array(
            'hh'  => array(
                'cnName' => '混合过关',
            ),
            'sfc' => array(
                'cnName' => '胜分差',
            ),
        );

        // 加奖hover
        $hoverInfo = $this->getHoverInfo();

    	$REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $lotteryConfig = json_decode($this->cache->redis->get($REDIS['LOTTERY_CONFIG']), TRUE);
        $time = date('Y-m-d H:i:s', time()+$lotteryConfig[JCLQ]['ahead']*60);
        $w = date('w');
        if (($w == 1 && (($time > date('Y-m-d')." 01:00:00" && date('Y-m-d H:i:s') < date('Y-m-d')." 09:00:00") || $time > date('Y-m-d', strtotime('+1 day'))))
        || (in_array($w, array(2, 5)) && (date('Y-m-d H:i:s') < date('Y-m-d')." 09:00:00" || $time > date('Y-m-d', strtotime('+1 day'))))
        || (in_array($w, array(3, 4)) && (date('Y-m-d H:i:s') < date('Y-m-d')." 07:30:00" || $time > date('Y-m-d', strtotime('+1 day'))))
        || $w == 6 && (date('Y-m-d H:i:s') < date('Y-m-d')." 09:00:00")
        || $w == 0 && ($time > date('Y-m-d')." 01:00:00" && date('Y-m-d H:i:s') < date('Y-m-d')." 09:00:00")) {
        	$t1 = date('H:i', strtotime(date('Y-m-d')." 24:00:00")-$lotteryConfig[JCLQ]['ahead']*60);
        	$t2 = date('H:i', strtotime(date('Y-m-d')." 01:00:00")-$lotteryConfig[JCLQ]['ahead']*60);
        	$tips = "附：周一、周二、周五9：00-".$t1."出票，周三、周四7：30<br>-".$t1."出票，周六、周日9：00-次日".$t2."出票。";
        }
        $responseAry = $this->getMatchData();
        if($responseAry)
        {
            $first = current($responseAry);
            $firstDate = substr($first['mid'], 0, 8);
        }
        else
        {
            $firstDate = date("Ymd");
        }
        $dates = array(
            date('Y.m.d', strtotime($firstDate)) => $firstDate,
            date("Y.m.d", strtotime("-1 day {$firstDate}")) => date("Ymd", strtotime("-1 day {$firstDate}")),
            date("Y.m.d", strtotime("-2 day {$firstDate}")) => date("Ymd", strtotime("-2 day {$firstDate}")),
            date("Y.m.d", strtotime("-3 day {$firstDate}")) => date("Ymd", strtotime("-3 day {$firstDate}")),
            date("Y.m.d", strtotime("-4 day {$firstDate}")) => date("Ymd", strtotime("-4 day {$firstDate}")),
            date("Y.m.d", strtotime("-5 day {$firstDate}")) => date("Ymd", strtotime("-5 day {$firstDate}")),
            date("Y.m.d", strtotime("-6 day {$firstDate}")) => date("Ymd", strtotime("-6 day {$firstDate}")),
            date("Y.m.d", strtotime("-7 day {$firstDate}")) => date("Ymd", strtotime("-7 day {$firstDate}")),
            date("Y.m.d", strtotime("-8 day {$firstDate}")) => date("Ymd", strtotime("-8 day {$firstDate}")),
            date("Y.m.d", strtotime("-9 day {$firstDate}")) => date("Ymd", strtotime("-9 day {$firstDate}"))
        );
        if(!$date)
        {
            $date = $firstDate;
        }
        $this->load->model('jcmatch_model');
        $this->load->config('wenan');
        $wenan = $this->config->item('wenan');
        if($date == $firstDate)
        {
            list($matches, $leagues, $issueToContent) = $this->roundRobin($responseAry, $jclqType);
            list($leagues, $leaguesNew) = $this->withMatchCount($leagues);
            list($cnName, $enName, $lotteryId) = array('竞彩篮球', 'jclq', JCLQ);
            $count = $this->jcmatch_model->countOldLqMatch($date);
            $this->display('jclq/' . $jclqType, compact(
                'matches',
                'leagues',
                'leaguesNew',
                'sfcOptions',
                'jclqType',
                'typeMAP',
                'cnName',
                'enName',
                'lotteryId',
                'idp',
                'multiple',
                    'sf',
                'rfsf',
                    'dxf',
                'idn',
                'rfsfn',
                'stakeStr',
                    'tips',
                'hoverInfo',
                'dates',
                'date',
                'count',
                'wenan'
            ), 'v1.1');
        }
        else
        {
            $oldmatches = $this->jcmatch_model->getOldLqMatch($date);
            $datas = $this->getOldLqMatchInfo($oldmatches, $jclqType);
            $matches = $datas[0];
            $leagues = $datas[1];
            list($leagues, $leaguesNew) = $this->withMatchCount($leagues);
            $issueStr = $date;
            list($cnName, $enName, $lotteryId) = array('竞彩篮球', 'jclq', JCLQ);
            $count = $this->jcmatch_model->countOldLqMatch($date);
            $this->display('jclq/old' . $jclqType, compact(
                'matches',
                'leagues',
                'leaguesNew',
                'sfcOptions',
                'jclqType',
                'typeMAP',
                'cnName',
                'enName',
                'lotteryId',
                'idp',
                'multiple',
                    'sf',
                'rfsf',
                    'dxf',
                'idn',
                'rfsfn',
                'stakeStr',
                    'tips',
                'hoverInfo',
                'dates',
                'date',
                'count',
                'wenan'
            ), 'v1.1');
        }
    }

    private function getMatchData()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));

        return json_decode($this->cache->redis->get($REDIS['JCLQ_MATCH']), TRUE);
    }

    public function sfc()
    {
        $this->deliveryType('sfc');
    }

    public function index()
    {
        $this->hh();
    }

    public function getHoverInfo($lname = 'JCLQ')
    {
        $hoverInfo = array();

        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));

        $ukey = "{$REDIS['JCJJ_HOVER']}$lname";
        $info = $this->cache->redis->hGetAll($ukey);

        if(!empty($info) && ($info['platform'] & 1))
        {
            $tpl = '';
            $params = json_decode($info['params'], true);
            foreach ($params as $key => $items) 
            {
                if($items['max'] != '*')
                {
                    $tpl .= "<tr><td>" . $this->mformat($items['min']) . "<奖金≤" . $this->mformat($items['max']) . "</td><td>" . $items['dg'] . "</td><td>" . $items['2c1'] . "</td></tr>";
                }
                else
                {
                    $tpl .= "<tr><td>奖金>" . $this->mformat($items['min']) . "</td><td>" . $items['dg'] . "</td><td>" . $items['2c1'] . "</td></tr>";
                } 
            }

            $hoverInfo = array(
                'startTime' => $info['startTime'],
                'endTime' => $info['endTime'],
                'slogan' => $info['slogan'],
                'tpl' => $tpl
            );
        }

        return $hoverInfo;
    }

    public function mformat($money = 0)
    {
        if($money >= 10000)
        {
            if($money%10000 == 0)
            {
                $money = $money/10000 . '万';
            }
            else
            {
                $m = strrev($money);
                $money = strrev(substr($m, 4)) . '万' . strrev(substr($m, 0, 4));
            }
        }
        return $money;
    }

    public function getOldMatch()
    {
        $type = $this->input->get('type', TRUE);
        $this->load->model('jcmatch_model');
        $responseAry = $this->getMatchData();
        $first = current($responseAry);
        $firstDate = substr($first['mid'], 0, 8);
        if(!$firstDate)
        {
            $firstDate = date("Ymd");
        }
        $oldmatches = $this->jcmatch_model->getOldLqMatch($firstDate);
        $res = $this->getOldLqMatchInfo($oldmatches, 'hh');
        $matches = $res[0];
        echo $this->load->view('v1.1/jclq/old' . $type . 'option', compact('matches'));
    }
    
    private function getOldLqMatchInfo($oldmatches,$type)
    {
        $matches = array();
        $leagues = array();
        $weekDays = array('日', '一', '二', '三', '四', '五', '六');
        $detailUrl = $this->config->item('api_info');
        $oddsUrl = $this->config->item('api_odds');
        $m_date = '';
        foreach ($oldmatches as $match)
        {
            $mid = $match['mid'];
            $time = strtotime(substr($mid, 0, 8));
            $date = date('Y-m-d', $time);
            $dayIndex = date('w', $time);
            $match['weekId'] = '周' . $weekDays[$dayIndex] . substr($mid, 8);
            $match['name'] = $match['league'];
            $match['awary'] = $match['away'];
            if ($match['m_status'] == 1)
            {
                $match['full_score'] = '取:消';
            }
            $match['dt'] = strtotime($match['begin_time']) * 1000;
            $match['jzdt'] = strtotime($match['show_end_time']) * 1000;
            $match['result'] = json_decode($match['result'], true);
            $match['issue'] = $match['m_date'];
            $odds = json_decode($match['odds'], true);
            $codes = @unserialize($odds['sf']);
            $match['sfHs'] = $match['m_status']==1?'--':$codes['h'];
            $match['sfHf'] = $match['m_status']==1?'--':$codes['a'];
            $match['sfGd'] = $match['m_status']==1?'--':$codes ? 1 : 0;
            $match['sfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $match['let'] = $match['rq'];
            $codes = @unserialize($odds['rfsf']);
            $match['rfsfHs'] = $match['m_status']==1?'--':$codes['h'];
            $match['rfsfHf'] = $match['m_status']==1?'--':$codes['a'];
            $match['rfsfGd'] = $match['m_status']==1?'--':$codes ? 1 : 0;
            $match['rfsfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['sfc']);
            $match['sfcHs15'] = $match['m_status']==1?'--':$codes['h_1-5'];
            $match['sfcHs610'] = $match['m_status']==1?'--':$codes['h_6-10'];
            $match['sfcHs1115'] = $match['m_status']==1?'--':$codes['h_11-15'];
            $match['sfcHs1620'] = $match['m_status']==1?'--':$codes['h_16-20'];
            $match['sfcHs2125'] = $match['m_status']==1?'--':$codes['h_21-25'];
            $match['sfcHs26'] = $match['m_status']==1?'--':$codes['h_26+'];
            $match['sfcAs15'] = $match['m_status']==1?'--':$codes['a_1-5'];
            $match['sfcAs610'] = $match['m_status']==1?'--':$codes['a_6-10'];
            $match['sfcAs1115'] = $match['m_status']==1?'--':$codes['a_11-15'];
            $match['sfcAs1620'] = $match['m_status']==1?'--':$codes['a_16-20'];
            $match['sfcAs2125'] = $match['m_status']==1?'--':$codes['a_21-25'];
            $match['sfcAs26'] = $match['m_status']==1?'--':$codes['a_26+'];
            $match['sfcGd'] = $codes ? 1 : 0;
            $match['sfcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['dxf']);
            $match['preScore'] = $codes['score'];
            $match['dxfBig'] = $match['m_status']==1?'--':$codes['b_s'];
            $match['dxfSmall'] = $match['m_status']==1?'--':$codes['m_s'];
            $match['dxfGd'] = $codes ? 1 : 0;
            $match['dxfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $match['issue'] = $match['m_date'];
            $zhisheng = json_decode($match['zhisheng'], true);
            $match['hTid'] = $zhisheng['htid'];
            $match['hDetail'] = $detailUrl . 'lanqiu/' . $zhisheng['sid'] . '/team/' . $match['hTid'];
            $match['aTid'] = $zhisheng['atid'];
            $match['aDetail'] = $detailUrl . 'lanqiu/' . $zhisheng['sid'] . '/team/' . $match['aTid'];
            $match['hRank'] = preg_replace('/[^\d]/', '', $zhisheng['hpm']);
            $match['aRank'] = preg_replace('/[^\d]/', '', $zhisheng['apm']);
            $match['oddsUrl'] = $oddsUrl;
            $match['queryMId'] = $zhisheng['mid'];
            $match['oh'] = ( ! empty($zhisheng['odds']['oh']) && is_numeric($zhisheng['odds']['oh']))
                ? $zhisheng['odds']['oh']
                : '0.00';
            $match['oa'] = ( ! empty($zhisheng['odds']['oa']) && is_numeric($zhisheng['odds']['oa']))
                ? $zhisheng['odds']['oa']
                : '0.00';
            if ($this->isValidMatch($match, $type))
            {
                $leagues[] = $match['name'];
            }
            $dateWithWeek = $date . ' 周' . $weekDays[$dayIndex];
            $matches[$dateWithWeek][] = $match;
        }
        return array($matches, $leagues);
    }
}
