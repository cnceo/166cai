<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 红包推送
 * 作    者: 李康建
 * 修改日期: 2017/05/26
 * 修改时间: 11:50
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Redpack_Push extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('redpack_push_model');
    }

    /**
     * [index 红包生效推送入口 10:00]
     * @author LiKangJian 2017-05-26
     * @return [type] [description]
     */
    public function index()
    {
        $this->redpack_push_model->validStartPush();
    }
    /**
     * [validEndThreeDayPush 失效前3天推送 19:00]
     * @author LiKangJian 2017-05-31
     * @return [type] [description]
     */
    public function validEndThreeDayPush()
    {
        $this->redpack_push_model->validEndPush(3);
    }
    /**
     * [validEndThreeDayPush 失效前1天推送 10:00]
     * @author LiKangJian 2017-05-31
     * @return [type] [description]
     */
    public function validEndOneDayPush()
    {
        $this->redpack_push_model->validEndPush(1);
    }
}

