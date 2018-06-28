<?php

/**
 * 定制跟单过期撤单脚本
 * @date:2017-08-18
 */

class Cli_United_Follow_Endtime extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('follow_order_model');
    }

    public function index()
    {
        // 跟单10秒未处理报警
        $this->follow_order_model->handleFollowWarning();
        
        $orders = $this->follow_order_model->getExpiredOrders();
        while(!empty($orders))
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                $this->follow_order_model->cancelFollowOrder($order['uid'], $order['followId'], 1);
            }
            $orders = $this->follow_order_model->getExpiredOrders();
        }
    }
}