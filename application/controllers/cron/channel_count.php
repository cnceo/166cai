<?php
/**
 * Copyright (c) 2015,上海快猫文化传媒有限公司.
 * 摘    要: 红包派发验证派发
 * 作    者: 李康建
 * 修改日期: 2017/03/27
 * 修改时间: 14:14
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Pull_Redpack extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_model', 'activity');
    }

    /**
     * [index 红包派发]
     * @author JackLee 2017-03-27
     * @return [type] [description]
     */
    public function index()
    {
        $this->activity->doPullRedPack();
    }
}