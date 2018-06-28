<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 武林榜渠道统计数据
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/16
 * 修改时间: 10:22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Register_Statistics extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('register_statistics_model', 'registerStatistics');
    }

    public function index()
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->registerStatistics->statistics($yesterday);
    }

    public function prepareData()
    {
        for ($i = 61; $i >= 1; $i --)
        {
            $date = date('Y-m-d', strtotime("-$i day"));
            $this->registerStatistics->statistics($date);
        }
    }
}