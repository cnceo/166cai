<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/18
 * 修改时间: 7:37
 */
class Api_Zhisheng_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
    }

    /*
	 * 功能：获取智胜赔率
	 * 作者：刁寿钧
	 * 日期：2016-03-10
	 * */
    public function writeEuropeOdds()
    {
        $apiUrl = $this->config->item('api_bf');
        $REDIS = $this->config->item('REDIS');
        $cidAry = range(0, 8);

        //jczq
        $issues = array();
        $issueDetailAry = json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
        if ( ! empty($issueDetailAry))
        {
            foreach ($issueDetailAry as $detail)
            {
                $issue = str_replace('-', '', $detail['issue']);
                if ( ! in_array($issue, $issues))
                {
                    $issues[] = $issue;
                }
            }
        }
        if ( ! empty($issues))
        {
            $this->cache->redis->delete($REDIS['JCZQ_EUROPE_ODDS']);
            foreach ($issues as $issue)
            {
                $info = array();
                $matchinfo = array();
                foreach ($cidAry as $key=>$cid)
                {
                    $url = $apiUrl . "apps/?lotyid=6&cid=$cid";
                    $content = $this->tools->request($url . "&expect=$issue", array(), $tout = 60);
                    if ($this->tools->recode == 200) {
                        $this->cache->redis->hSet($REDIS['JCZQ_EUROPE_ODDS'], $issue . '_' . $cid, $content);
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
        $issues = array();
        $issueDetailAry = json_decode($this->cache->redis->get($REDIS['JCLQ_MATCH']), TRUE);
        if ( ! empty($issueDetailAry))
        {
            foreach ($issueDetailAry as $detail)
            {
                $issue = str_replace('-', '', $detail['issue']);
                if ( ! in_array($issue, $issues))
                {
                    $issues[] = $issue;
                }
            }
        }
        if ( ! empty($issues))
        {
            $this->cache->redis->delete($REDIS['JCLQ_EUROPE_ODDS']);
            foreach ($issues as $issue)
            {
                $info = array();
                $matchinfo = array();
                $content = $this->tools->request($apiUrl . "apps/jclq?expect=$issue", array(), $tout = 60);
                if ($this->tools->recode == 200) {
                    $this->cache->redis->hSet($REDIS['JCLQ_EUROPE_ODDS'], $issue, $content);
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

        //sfc rj
        $issues = array();
        $issueDetailAry = json_decode($this->cache->redis->get($REDIS['SFC_ISSUE_NEW']), TRUE);
        if ( ! empty($issueDetailAry))
        {
            foreach ($issueDetailAry as $detail)
            {
                $issues[] = '20' . $detail['mid'];
            }
        }
        if ( ! empty($issues))
        {
        	$this->cache->redis->delete($REDIS['SFC_EUROPE_ODDS']);
            foreach ($issues as $issue)
            {
                foreach ($cidAry as $cid)
                {
                    $url = $apiUrl . "apps/?lotyid=1&cid=$cid";
                    $content = $this->tools->request($url . "&expect=$issue", array(), $tout = 60);
                    if ($this->tools->recode == 200) {
                    	$this->cache->redis->hSet($REDIS['SFC_EUROPE_ODDS'], $issue . '_' . $cid, $content);
                    }
                }
            }
        }
    }

    /*
	 * 功能：获取智胜赔率
	 * 作者：刁寿钧
	 * 日期：2016-03-10
	 * */
    public function readEuropeOdds($lotteryId, $issue, $cid)
    {
        $REDIS = $this->config->item('REDIS');
        switch ($lotteryId)
        {
            case JCZQ:
                $key = 'JCZQ_EUROPE_ODDS';
                break;
            case JCLQ:
                $key = 'JCLQ_EUROPE_ODDS';
                break;
            case SFC:
                $key = 'SFC_EUROPE_ODDS';
                break;
            case RJ:
                $key = 'SFC_EUROPE_ODDS';
                break;
            default:
                $key = '';
                break;
        }
        if (empty($key))
        {
            return '';
        }
        $issueKey = $lotteryId == JCLQ ? $issue : ($issue . '_' . $cid);

        $odds=$this->cache->redis->hGet($REDIS[$key], $issueKey);
        return $odds;
    }
}