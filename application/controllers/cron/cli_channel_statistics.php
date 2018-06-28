<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 武林榜渠道统计数据
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/16
 * 修改时间: 10:22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Channel_Statistics extends MY_Controller
{

    private $webTypeToPrefix = array(
        'caipiao2345' => 'browse',
        'ajax112'     => 'click',
    );

    private $appKey = '982a73ce106b68b4312cb763cb3cbcb3';
    private $appType = 'channel_version_baseinfo';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rank_statistics_model', 'channelStatistics');
    }

    public function index()
    {
        $date = date('Y-m-d', strtotime("-1 day"));
        $this->statisticsWeb($date);
        $this->statisticsApp($date);
        $this->statisticsWebAll($date);
        $this->fillVersions();
    }

    public function prepareData()
    {
        $this->channelStatistics->cleanTable();
        for ($i = 61; $i >= 1; $i --)
        {
            $date = date('Y-m-d', strtotime("-$i day"));
            $this->statisticsWeb($date);
            $this->statisticsApp($date);
            $this->statisticsWebAll($date);
        }
        $this->fillVersions();
    }

    private function statisticsWeb($date)
    {
        $yesterday = $date;
        $typeToStat = array();
        foreach ($this->webTypeToPrefix as $tp => $pre)
        {
            $url = 'http://www.50bang.org/plus/sql_interface.php?d=' . $yesterday . '&site=' . $tp . '&type=all';
            $content = $this->tools->request($url, array(), $tout = 60);
            $typeToStat[$pre] = $content;
        }

        $this->channelStatistics->deliveryWebContent($typeToStat, $date);
    }

    private function statisticsWebAll($date)
    {
        $typeToStat = array();
        foreach ($this->webTypeToPrefix as $tp => $pre)
        {
            $url = 'http://www.50bang.org/plus/sql_interface.php?sdate=' . $date . '&site=' . $tp . '&type=total'
                . '&edate=' . $date;
            $content = $this->tools->request($url, array(), $tout = 60);
            $typeToStat[$pre] = $content;
        }

        $this->channelStatistics->deliveryWebAllContent($typeToStat, $date);
    }

    private function statisticsApp($date)
    {
        $url = '42.62.4.24/mbang/api/getData.php?date=' . $date
            . '&key=' . $this->appKey . '&type=' . $this->appType;
        $params = array('HOST' => 'rto.m.50bang.org');
        $content = $this->tools->request($url, $params, $tout = 60);

        $this->channelStatistics->deliveryAppContent($content);
    }

    private function fillVersions()
    {
        $this->load->model('app_version_model', 'appVersion');
        $this->appVersion->fillVersions();
    }
}