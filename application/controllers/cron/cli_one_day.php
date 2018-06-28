<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 每隔一分钟需要执行的脚本
 * 作    者: 刘兆东
 * 修改日期: 2015/7/28
 * 修改时间: 10:00
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_One_Day extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //渠道统计
        //system("{$this->php_path} {$this->cmd_path} cron/cli_channel_statistics index", $status);
        //渠道注册数据统计
        //system("{$this->php_path} {$this->cmd_path} cron/cli_register_statistics index", $status);
		//优质用户统计
        system("{$this->php_path} {$this->cmd_path} cron/cli_hight_quality_user_statistic index", $status);
        //检查期次预排是否充足
        system("{$this->php_path} {$this->cmd_path} cron/cli_cfg_check_period index", $status);
    }
    
}