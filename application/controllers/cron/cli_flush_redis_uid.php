<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 清空uid缓存
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/30
 * 修改时间: 18:10
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Flush_Redis_Uid extends MY_Controller
{
    private $flushStep = 5000;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model', 'user');
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：清空uid缓存
     * 修改日期：2015-06-30
     */
    public function index()
    {
        $this->user->reFreshByStep($this->flushStep);
    }
}