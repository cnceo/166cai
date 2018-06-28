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
            foreach ($issues as $issue)
            {
                foreach ($cidAry as $cid)
                {
                    $url = $apiUrl . "apps/?lotyid=6&cid=$cid";
                    $content = $this->tools->request($url . "&expect=$issue", array(), $tout = 60);
                    $this->cache->redis->hSet($REDIS['JCZQ_EUROPE_ODDS'], $issue . '_' . $cid, $content);
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
            foreach ($issues as $issue)
            {
                $content = $this->tools->request($apiUrl . "apps/jclq?expect=$issue", array(), $tout = 60);
                $this->cache->redis->hSet($REDIS['JCLQ_EUROPE_ODDS'], $issue, $content);
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
            foreach ($issues as $issue)
            {
                foreach ($cidAry as $cid)
                {
                    $url = $apiUrl . "apps/?lotyid=1&cid=$cid";
                    $content = $this->tools->request($url . "&expect=$issue", array(), $tout = 60);
                    $this->cache->redis->hSet($REDIS['SFC_EUROPE_ODDS'], $issue . '_' . $cid, $content);
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
            case '42':
                $key = 'JCZQ_EUROPE_ODDS';
                break;
            case '43':
                $key = 'JCLQ_EUROPE_ODDS';
                break;
            case '11':
                $key = 'SFC_EUROPE_ODDS';
                break;
            case '19':
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
        $issueKey = $lotteryId == '43' ? $issue : ($issue . '_' . $cid);

        return $this->cache->redis->hGet($REDIS[$key], $issueKey);
    }
}