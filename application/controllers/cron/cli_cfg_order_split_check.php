<?php
/**
 * Copyright (c) 2016,上海瑞创网络科技股份有限公司.
 * 摘    要:
 * 作    者: 胡小明
 * 修改日期: 2016/07/26
 * 修改时间: 8:08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Cfg_Order_Split_Check extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_order_check', 'orderCheck');
    }

    public function index()
    {
    	$stop = false;
    	while(!$stop)
    	{
    		$this->controlRestart($this->con);
    		$this->orderCheck->check_ticket_gaopin($this->config->item('split_lid'));
    		$this->orderCheck->check_ticket();
    		sleep(5);
    	}
    }

}