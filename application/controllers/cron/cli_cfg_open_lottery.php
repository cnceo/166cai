<?php
/**
 * Copyright (c) 2012,上海瑞创网络科技股份有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/9
 * 修改时间: 9:56
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Cfg_Open_Lottery extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('lottery_config_model', 'lotteryConfig');
    }

    public function index()
    {
        $configItems = $this->lotteryConfig->fetchConfigItems();
        $this->lotteryConfig->deliveryConfigItems($configItems);
    }
}