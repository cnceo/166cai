<?php

class Tenday_Model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
    }
    
    
        
    public function calResult($lid)
    {
        if ($lid == 42) {
            $day = date("Y-m-d 00:00:00", strtotime("-10 day"));
            $sql = "select id,mid,m_date,rq,half_score,full_score from cp_jczq_paiqi where show_end_time < now() and end_sale_time >= ?";
            $matches = $this->cfgDB->query($sql, array($day))->getAll();
            $ctypeMap = array(
                '0' => 'sg',
                '1' => 'spf',
                '2' => 'rqspf',
                '3' => 'bqc',
                '4' => 'jqs',
                '5' => 'cbf',
            );
            $this->cfgDB->trans_start();
            $spf = array('负', '平', '胜', '胜');
            foreach ($matches as $match) {
                $result = array();
                if ($match['full_score']) {
                    $fall_score = explode(':', $match['full_score']);
                    $result['spf'] = $this->cal_mresult($fall_score);
                    $result['rqspf'] = $this->cal_mresult(array($fall_score[0] + $match['rq'], $fall_score[1]));
                    $result['bf'] = $match['full_score'];
                    $result['jqs'] = $fall_score[0] + $fall_score[1];
                    $half_score = explode(':', $match['half_score']);
                    $half = $this->cal_mresult($half_score);
                    $result['bqc'] = $spf[$half] . '-' . $spf[$result['spf']];
                }
                $sql1 = "select ctype,codes from cp_jczq_match where mid =?";
                $detail = $this->dc->query($sql1, array($match['mid']))->getAll();
                $awards = array();
                foreach ($detail as $key => $items) {
                    $awards[$ctypeMap[$items['ctype']]] = $items['codes'];
                }
                $awards['info']['spf'] = $awards['spf'];
                $awards['info']['rqspf'] = $awards['rqspf'];
                $awards['info']['jqs'] = $awards['jqs'];
                $awards['info']['bqc'] = $awards['bqc'];
                $awards['info']['cbf'] = $awards['cbf'];
                $this->cfgDB->query("update cp_jczq_paiqi set result=?,odds=? where id=?", array(json_encode($result), json_encode($awards['info']), $match['id']));
            }
            $this->cfgDB->trans_complete();
        }
        if ($lid == 43) {
            $day = date("Y-m-d 00:00:00", strtotime("-10 day"));
            $sql = "select id,mid,m_date,rq,preScore,full_score from cp_jclq_paiqi where show_end_time < now() and show_end_time >= ?";
            $matches = $this->cfgDB->query($sql, array($day))->getAll();
            $this->cfgDB->trans_start();
            $ctypeMap = array(
                '1' => 'sf',
                '2' => 'rfsf',
                '3' => 'sfc',
                '4' => 'dxf',
            );
            foreach ($matches as $match) {
                $result = array();
                if ($match['full_score']) {
                    $fall_score = explode(':', $match['full_score']);
                    $result['sf'] = $this->cal_mresult($fall_score);
                    $result['rfsf'] = $this->cal_mresult(array($fall_score[0] + $match['rq'], $fall_score[1]));
                    $preScore = str_replace('+', '', $match['preScore']);
                    $result['dxf'] = 0;
                    $score = $fall_score[0] + $fall_score[1];
                    if ($score > $preScore) {
                        $result['dxf'] = 3;
                    }
                    $result['sfc'] = $this->cal_diff($fall_score);
                }
                $sql1 = "select ctype,codes from cp_jclq_match where mid =?";
                $detail = $this->dc->query($sql1, array($match['mid']))->getAll();
                $awards = array();
                foreach ($detail as $key => $items) {
                    $awards[$ctypeMap[$items['ctype']]] = $items['codes'];
                }
                $awards['info']['sf'] = $awards['sf'];
                $awards['info']['rfsf'] = $awards['rfsf'];
                $awards['info']['sfc'] = $awards['sfc'];
                $awards['info']['dxf'] = $awards['dxf'];
                $this->cfgDB->query("update cp_jclq_paiqi set result=?,odds=? where id=?", array(json_encode($result), json_encode($awards['info']), $match['id']));
            }
            $this->cfgDB->trans_complete();
        }
    }
    
    public function writeEuropeOdds()
    {
        $apiUrl = $this->config->item('api_bf');
        $REDIS = $this->config->item('REDIS');
        $cidAry = range(0, 8);

        //jczq
        $issues = array(20170726,20170727,20170728,20170729,20170730,20170731,20170801,20170802,20170803,20170804);
        if ( ! empty($issues))
        {
            foreach ($issues as $issue)
            {
                $info = array();
                $matchinfo = array();
                foreach ($cidAry as $key=>$cid)
                {
                    $url = $apiUrl . "apps/?lotyid=6&cid=$cid";
                    $content = $this->tools->request($url . "&expect=$issue", array(), $tout = 60);
                    if ($this->tools->recode == 200) {
                    	  $zhishengs = json_decode($content, true);
                        foreach ($zhishengs as $k=>$zhisheng)
                        {
                            $matchinfo[$k]['htid'] = $zhisheng['htid'];
                            $matchinfo[$k]['atid'] = $zhisheng['atid'];
                            $matchinfo[$k]['hpm'] = preg_replace('/[^\d]/', '', $zhisheng['hpm']);
                            $matchinfo[$k]['apm'] = preg_replace('/[^\d]/', '', $zhisheng['apm']);
                            $matchinfo[$k]['mid'] = $zhisheng['mid'];
                            $matchinfo[$k]['odds'][$key] = $zhisheng['odds'];
                        }
                    }
                }
                foreach ($matchinfo as $k => $match) {
                    $info[] = "('{$issue}','20{$k}','" . json_encode($match) . "',now())";
                }
                if(!empty($info))
                {
                    $values = implode(',', $info);
                    $this->cfgDB->query("INSERT INTO cp_jczq_zhisheng (issue,mid,zhisheng,created) VALUES ".$values." on duplicate key update zhisheng = values(zhisheng)");
                }
            }
        }
        
        //jclq
        $issues = array(20170726,20170727,20170728,20170729,20170730,20170731,20170801,20170802,20170803,20170804);
        if ( ! empty($issues))
        {
            foreach ($issues as $issue)
            {
                $info = array();
                $matchinfo = array();
                $content = $this->tools->request($apiUrl . "apps/jclq?expect=$issue", array(), $tout = 60);
                if ($this->tools->recode == 200) {
                    $zhishengs = json_decode($content, true);
                    foreach ($zhishengs as $k=>$zhisheng)
                    {
                        $matchinfo[$k]['htid'] = $zhisheng['htid'];
                        $matchinfo[$k]['atid'] = $zhisheng['atid'];
                        $matchinfo[$k]['hpm'] = preg_replace('/[^\d]/', '', $zhisheng['hpm']);
                        $matchinfo[$k]['apm'] = preg_replace('/[^\d]/', '', $zhisheng['apm']);
                        $matchinfo[$k]['mid'] = $zhisheng['mid'];
                        $matchinfo[$k]['sid'] = $zhisheng['sid'];
                        $matchinfo[$k]['odds'] = $zhisheng['odds'];
                    }   
                }
                foreach ($matchinfo as $k => $match) {
                    $info[] = "('{$issue}','20{$k}','" . json_encode($match) . "',now())";
                }
                if(!empty($info))
                {
                    $values = implode(',', $info);
                    $this->cfgDB->query("INSERT INTO cp_jclq_zhisheng (issue,mid,zhisheng,created) VALUES ".$values." on duplicate key update zhisheng = values(zhisheng)");
                }
            }
        }
    }
    
    private function cal_mresult($score)
    {
        $mresult = '0';
        if ($score[0] > $score[1]) {
            $mresult = '3';
        } elseif ($score[0] == $score[1]) {
            $mresult = '1';
        }
        return $mresult;
    }

    private function cal_diff($score)
    {
            $diff = $score[1] - $score[0];
            $diff = abs($diff);
            if($diff >= 1 && $diff <= 5)
            {
                    $re = "{$pre}1";
            }
            elseif($diff >= 6 && $diff <= 10)
            {
                    $re = "{$pre}2";
            }
            elseif($diff >= 11 && $diff <= 15)
            {
                    $re = "{$pre}3";
            }
            elseif($diff >= 16 && $diff <= 20)
            {
                    $re = "{$pre}4";
            }
            elseif($diff >= 21 && $diff <= 25)
            {
                    $re = "{$pre}5";
            }
            elseif($diff >= 26) 
            {
                    $re = "{$pre}6";
            }
            return $re;
    }    
}
