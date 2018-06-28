<?php

/**
 * 合买红人统计脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Planner_Statistic extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
    }

    public function index()
    {
        // 处理30天未发单的近一月中奖统计
        $this->united_order_model->clearStatisticOrder();
        $orders = $this->united_order_model->getStatisticOrder();
        if(!empty($orders))
        {
            foreach ($orders as $key => $order) 
            {
                $this->united_order_model->getStatisticDetail($order['uid']);
            }
        }
    }
}