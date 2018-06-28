<?php
/**
 * Copyright (c) 2015,上海快猫文化传媒有限公司.
 * 摘    要: 新年活动脚本
 * 作    者: 
 * 修改日期: 2017/03/27
 * 修改时间: 14:14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Activity_Xn extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_xn_model', 'activity_xn_model');
    }

    public function index()
    {
        $this->activity_xn_model->luckSend();
    }
}