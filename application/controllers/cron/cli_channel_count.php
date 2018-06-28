<?php
/**
 * Copyright (c) 2015,上海快猫文化传媒有限公司.
 * 摘    要: 渠道数据统计
 * 作    者: sgx
 * 修改日期: 2017/05/03
 * 修改时间: 10:20
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Channel_Count extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('channel_count_model');
    }
    
    /**
     * 10分钟执行一次 统计当日数据
     */
    public function index()
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d H:i:s');
        $this->channel_count_model->channelCount($start, $end);
    }
    
    /**
     * 每天10点执行一次 按天统计 默认当天统计昨天数据
     * @param string $date  YYYY-MM-DD
     */
    public function dateCount($date = '')
    {
        if(empty($date)) {
            $start = date('Y-m-d', strtotime("-1 day"));
            $end = date('Y-m-d 23:59:59', strtotime("-1 day"));
        } else {
            $start = $date;
            $end = $date . ' 23:59:59';
        }
        
        $this->channel_count_model->channelCount($start, $end, 1);
    }
    
    /**
     * 每天6点执行一次 按天统计 默认当天统计前天数据
     * @param string $date
     */
    public function delayedCount($date = '')
    {
        if(empty($date)) {
            $date = date('Y-m-d', strtotime("-2 day"));
        } else {
            if(strtotime($date) > strtotime("-2 day")) {
                die("该脚步最晚可以执行到前天数据");
            }
        }
        
        $this->channel_count_model->delayedCount($date);
    }
    
    /**
     * 老数据修复
     */
    public function historyChannelCount()
    {
        $this->channel_count_model->historyChannelCount();
    }
}

