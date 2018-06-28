<?php

class Jczq extends MY_Controller
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
            $match['od'] = '0.00';
            $match['oa'] = '0.00';
            $zhisheng = json_decode($match['zhisheng'], true);
            $match['hTid'] = $zhisheng['htid'];
            $match['hDetail'] = $detailUrl . 'teams/' . $match['hTid'];
            $match['aTid'] = $zhisheng['atid'];
            $match['aDetail'] = $detailUrl . 'teams/' . $match['aTid'];
            $match['hRank'] = $zhisheng['hpm'];
            $match['aRank'] = $zhisheng['apm'];
            $match['oddsUrl'] = $oddsUrl;
            $match['queryMId'] = $zhisheng['mid'];
            if(empty($zhisheng['odds'][0]['oh']))
                    $zhisheng['odds'][0]['oh'] = 0;
            if(empty($zhisheng['odds'][0]['od']))
                    $zhisheng['odds'][0]['od'] = 0;
            if(empty($zhisheng['odds'][0]['oa']))
                    $zhisheng['odds'][0]['oa'] = 0;
            $match['oh'] = number_format($zhisheng['odds'][0]['oh'], 2);
            $match['od'] = number_format($zhisheng['odds'][0]['od'], 2);
            $match['oa'] = number_format($zhisheng['odds'][0]['oa'], 2);
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
            return $match['bqcGd'] OR $match['bfGd'] OR $match['spfGd'] OR $match['rqspfGd'] OR $match['jqsGd'];
        }
        elseif ($type == 'dg')
        {
            return ($match['spfGd'] && $match['spfFu']) OR ($match['rqspfGd'] && $match['rqspfFu']);
        }
        elseif ($type == 'cbf')
        {
            return $match['bfGd'];
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

    private function deliveryType($jczqType)
    {
        $date=$this->input->get('date', TRUE);
        $mid = $this->input->get('mid', TRUE);
        $multiple = $this->input->get('multiple', TRUE);
        $spf = $this->input->get('spf', TRUE);
        $rqspf = $this->input->get('rqspf', TRUE);
        is_numeric($multiple) && $multiple >= 1 ? ($multiple = (int)$multiple) : ($multiple = 1);
        $stakeStr = $this->input->post('stakeStr', TRUE);
        $this->load->config('wenan');
        $wenan = $this->config->item('wenan');
        $bqcOptions = array(
            '33' => '胜-胜',
            '31' => '胜-平',
            '30' => '胜-负',
            '13' => '平-胜',
            '11' => '平-平',
            '10' => '平-负',
            '03' => '负-胜',
            '01' => '负-平',
            '00' => '负-负',
        );
        $cbfWinOptions = array(
            '10' => '1:0',
            '20' => '2:0',
            '21' => '2:1',
            '30' => '3:0',
            '31' => '3:1',
            '32' => '3:2',
            '40' => '4:0',
            '41' => '4:1',
            '42' => '4:2',
            '50' => '5:0',
            '51' => '5:1',
            '52' => '5:2',
            '93' => '胜其他',
        );
        $cbfDrawOptions = array(
            '00' => '0:0',
            '11' => '1:1',
            '22' => '2:2',
            '33' => '3:3',
            '91' => '平其他',
        );
        $cbfLoseOptions = array(
            '01' => '0:1',
            '02' => '0:2',
            '12' => '1:2',
            '03' => '0:3',
            '13' => '1:3',
            '23' => '2:3',
            '04' => '0:4',
            '14' => '1:4',
            '24' => '2:4',
            '05' => '0:5',
            '15' => '1:5',
            '25' => '2:5',
            '90' => '负其他',
        );
        $jqsOptions = array(
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7+',
        );
        $typeMAP = array(
            'hh'    => array(
                'cnName' => '混合过关',
            ),
            'dg'    => array(
                'cnName' => '单关',
            ),
            'spf'   => array(
                'cnName' => '胜平负',
            ),
            'rqspf' => array(
                'cnName' => '让球胜平负',
            ),
            'cbf'   => array(
                'cnName' => '比分',
            ),
//            'jqs'   => array(
//                'cnName' => '总进球',
//            ),
//            'bqc'   => array(
//                'cnName' => '半全场',
//            ),
        );
        list($cnName, $enName, $lotteryId) = array('竞彩足球', 'jczq', JCZQ);

        // 加奖hover
        $hoverInfo = $this->getHoverInfo();
        $this->load->model('jcmatch_model');
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
        if($date == $firstDate)
        {
            list($matches, $leagues, $issueToContent) = $this->roundRobin($responseAry, $jczqType);
            $issueToContent = $issueToContent ? $issueToContent : array();
            $issueStr = implode(',', $issueToContent);
            list($leagues, $leaguesNew) = $this->withMatchCount($leagues);
            if ($jczqType != 'dg')
            {
                $count = $this->jcmatch_model->countOldMatch($date);                
            }
            else
            {
                $oldmatches = $this->jcmatch_model->getOldMatch($date);
                $count = $this->calDgCount($oldmatches);
            }
            $wcpopurl = '';
            if ($jczqType === 'hh') {
                $cpk = $this->input->get('cpk');
                if (in_array($cpk, array('10060', '10349', '10350', '10351', '10357', '10359', '10363', '10364', '10365',
            '10366', '10367', '10368', '10369', '10370'))) $wcpopurl =  "/activity/sjbhb?sc{$cpk}";
            }
        
            $this->display('jczq/' . $jczqType, compact(
                'matches',
                'leagues',
                'leaguesNew',
                'bqcOptions',
                'cbfWinOptions',
                'cbfDrawOptions',
                'cbfLoseOptions',
                'jqsOptions',
                'jczqType',
                'typeMAP',
                'cnName',
                'enName',
                'lotteryId',
                'mid',
                'multiple',
                'spf',
                'rqspf',
                'stakeStr',
                'issueStr',
                'hoverInfo',
                'dates',
                'date',
                'count',
                'wenan',
                'wcpopurl'
            ), 'v1.1');
        }
        else
        {
            $oldmatches = $this->jcmatch_model->getOldMatch($date);
            $datas = $this->getOldMatchInfo($oldmatches, $jczqType);
            $matches = $datas[0];
            $leagues = $datas[1];
            list($leagues, $leaguesNew) = $this->withMatchCount($leagues);
            $issueStr = $date;
            if ($jczqType != 'dg')
            {
                $count = $this->jcmatch_model->countOldMatch($date);                
            }
            else
            {
                $count = $this->calDgCount($oldmatches);
            }
            $this->display('jczq/old' . $jczqType, compact(
                'matches',
                'leagues',
                'leaguesNew',
                'bqcOptions',
                'cbfWinOptions',
                'cbfDrawOptions',
                'cbfLoseOptions',
                'jqsOptions',
                'jczqType',
                'typeMAP',
                'cnName',
                'enName',
                'lotteryId',
                'mid',
                'multiple',
                'spf',
                'rqspf',
                'stakeStr',
                'issueStr',
                'hoverInfo',
                'dates',
                'date',
                'count',
                'wenan'
            ), 'v1.1');
        }
    }

    public function spf()
    {
        $this->deliveryType('spf');
    }

    public function rqspf()
    {
        $this->deliveryType('rqspf');
    }

    public function cbf()
    {
        $this->deliveryType('cbf');
    }

    public function dg()
    {
        $this->deliveryType('dg');
    }

    private function getMatchData()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));

        return json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
    }

    public function jqs()
    {
        $this->deliveryType('jqs');
    }

    public function bqc()
    {
        $this->deliveryType('bqc');
    }

    public function index()
    {
        $this->hh();
    }

    public function getHoverInfo($lname = 'JCZQ')
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
        $oldmatches = $this->jcmatch_model->getOldMatch($firstDate);
        $res = $this->getOldMatchInfo($oldmatches, 'hh');
        $matches = $res[0];
        echo $this->load->view('v1.1/jczq/old' . $type . 'option', compact('matches'));
    }
    
    private function getOldMatchInfo($oldmatches,$type)
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
            if ($match['m_status'] == 1)
            {
                $match['full_score'] = '取消';
            }
            $match['weekId'] = '周' . $weekDays[$dayIndex] . substr($mid, 8);
            $match['name'] = $match['league'];
            $match['awary'] = $match['away'];
            $match['dt'] = strtotime($match['end_sale_time']) * 1000;
            $match['jzdt'] = strtotime($match['show_end_time']) * 1000;
            $match['result'] = json_decode($match['result'], true);
            $match['issue'] = $match['m_date'];
            $odds = json_decode($match['odds'], true);
            $codes = @unserialize($odds['spf']);
            $match['spfSp3'] = $match['m_status']==1?'--':$codes['h'];
            $match['spfSp1'] = $match['m_status']==1?'--':$codes['d'];
            $match['spfSp0'] = $match['m_status']==1?'--':$codes['a'];
            $match['spfGd'] = $codes ? 1 : 0;
            $match['spfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['rqspf']);
            $match['let'] = $codes['fixedodds'];
            $match['rqspfSp3'] = $match['m_status']==1?'--':$codes['h'];
            $match['rqspfSp1'] = $match['m_status']==1?'--':$codes['d'];
            $match['rqspfSp0'] = $match['m_status']==1?'--':$codes['a'];
            $match['rqspfGd'] = $codes ? 1 : 0;
            $match['rqspfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['bqc']);
            $match['bqcSp00'] = $match['m_status']==1?'--':$codes['aa'];
            $match['bqcSp01'] = $match['m_status']==1?'--':$codes['ad'];
            $match['bqcSp03'] = $match['m_status']==1?'--':$codes['ah'];
            $match['bqcSp10'] = $match['m_status']==1?'--':$codes['da'];
            $match['bqcSp11'] = $match['m_status']==1?'--':$codes['dd'];
            $match['bqcSp13'] = $match['m_status']==1?'--':$codes['dh'];
            $match['bqcSp30'] = $match['m_status']==1?'--':$codes['ha'];
            $match['bqcSp31'] = $match['m_status']==1?'--':$codes['hd'];
            $match['bqcSp33'] = $match['m_status']==1?'--':$codes['hh'];
            $match['bqcGd'] = $codes ? 1 : 0;
            $match['bqcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['jqs']);
            $match['jqsSp0'] = $match['m_status']==1?'--':$codes['s0'];
            $match['jqsSp1'] = $match['m_status']==1?'--':$codes['s1'];
            $match['jqsSp2'] = $match['m_status']==1?'--':$codes['s2'];
            $match['jqsSp3'] = $match['m_status']==1?'--':$codes['s3'];
            $match['jqsSp4'] = $match['m_status']==1?'--':$codes['s4'];
            $match['jqsSp5'] = $match['m_status']==1?'--':$codes['s5'];
            $match['jqsSp6'] = $match['m_status']==1?'--':$codes['s6'];
            $match['jqsSp7'] = $match['m_status']==1?'--':$codes['s7'];
            $match['jqsGd'] = $codes ? 1 : 0;
            $match['jqsFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['cbf']);
            $match['bfSp00'] = $match['m_status']==1?'--':$codes['0:0'];
            $match['bfSp01'] = $match['m_status']==1?'--':$codes['0:1'];
            $match['bfSp02'] = $match['m_status']==1?'--':$codes['0:2'];
            $match['bfSp03'] = $match['m_status']==1?'--':$codes['0:3'];
            $match['bfSp04'] = $match['m_status']==1?'--':$codes['0:4'];
            $match['bfSp05'] = $match['m_status']==1?'--':$codes['0:5'];
            $match['bfSp10'] = $match['m_status']==1?'--':$codes['1:0'];
            $match['bfSp11'] = $match['m_status']==1?'--':$codes['1:1'];
            $match['bfSp12'] = $match['m_status']==1?'--':$codes['1:2'];
            $match['bfSp13'] = $match['m_status']==1?'--':$codes['1:3'];
            $match['bfSp14'] = $match['m_status']==1?'--':$codes['1:4'];
            $match['bfSp15'] = $match['m_status']==1?'--':$codes['1:5'];
            $match['bfSp20'] = $match['m_status']==1?'--':$codes['2:0'];
            $match['bfSp21'] = $match['m_status']==1?'--':$codes['2:1'];
            $match['bfSp22'] = $match['m_status']==1?'--':$codes['2:2'];
            $match['bfSp23'] = $match['m_status']==1?'--':$codes['2:3'];
            $match['bfSp24'] = $match['m_status']==1?'--':$codes['2:4'];
            $match['bfSp25'] = $match['m_status']==1?'--':$codes['2:5'];
            $match['bfSp30'] = $match['m_status']==1?'--':$codes['3:0'];
            $match['bfSp31'] = $match['m_status']==1?'--':$codes['3:1'];
            $match['bfSp32'] = $match['m_status']==1?'--':$codes['3:2'];
            $match['bfSp33'] = $match['m_status']==1?'--':$codes['3:3'];
            $match['bfSp40'] = $match['m_status']==1?'--':$codes['4:0'];
            $match['bfSp41'] = $match['m_status']==1?'--':$codes['4:1'];
            $match['bfSp42'] = $match['m_status']==1?'--':$codes['4:2'];
            $match['bfSp50'] = $match['m_status']==1?'--':$codes['5:0'];
            $match['bfSp51'] = $match['m_status']==1?'--':$codes['5:1'];
            $match['bfSp52'] = $match['m_status']==1?'--':$codes['5:2'];
            $match['bfSp90'] = $match['m_status']==1?'--':$codes['a_o'];
            $match['bfSp91'] = $match['m_status']==1?'--':$codes['d_o'];
            $match['bfSp93'] = $match['m_status']==1?'--':$codes['h_o'];
            $match['bfGd'] =  $codes ? 1 : 0;
            $match['bfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $match['issue'] = $match['m_date'];
            $zhisheng = json_decode($match['zhisheng'], true);
            $match['hTid'] = $zhisheng['htid'];
            $match['hDetail'] = $detailUrl . 'teams/' . $match['hTid'];
            $match['aTid'] = $zhisheng['atid'];
            $match['aDetail'] = $detailUrl . 'teams/' . $match['aTid'];
            $match['hRank'] = $zhisheng['hpm'];
            $match['aRank'] = $zhisheng['apm'];
            $match['oddsUrl'] = $oddsUrl;
            $match['queryMId'] = $zhisheng['mid'];
            if(empty($zhisheng['odds'][0]['oh']))
                    $zhisheng['odds'][0]['oh'] = 0;
            if(empty($zhisheng['odds'][0]['od']))
                    $zhisheng['odds'][0]['od'] = 0;
            if(empty($zhisheng['odds'][0]['oa']))
                    $zhisheng['odds'][0]['oa'] = 0;
            $match['oh'] = number_format($zhisheng['odds'][0]['oh'], 2);
            $match['od'] = number_format($zhisheng['odds'][0]['od'], 2);
            $match['oa'] = number_format($zhisheng['odds'][0]['oa'], 2);
            if ($this->isValidMatch($match, $type))
            {
                $leagues[] = $match['name'];
            }
            $dateWithWeek = $date . ' 周' . $weekDays[$dayIndex];
            $matches[$dateWithWeek][] = $match;
        }
        return array($matches, $leagues);
    }
    
    private function calDgCount($oldmatches)
    {
        $count = 0;
        foreach ($oldmatches as $match)
        {
            $odds = json_decode($match['odds'], true);
            $codes = @unserialize($odds['spf']);
            $match['spfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            $codes = @unserialize($odds['rqspf']);
            $match['rqspfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
            if ($match['spfFu'] OR $match['rqspfFu'])
            {
                $count += 1;
            }
        }
        return array('count' => $count);
    }
}
