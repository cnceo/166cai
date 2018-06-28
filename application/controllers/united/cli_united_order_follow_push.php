<?php

/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 关注推送
 * 作    者: 李康建
 * 修改日期: 2017/05/26
 * 修改时间: 10:10
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Order_Follow_Push extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
    }

    // 扫描已支付未推送的订单 向关注发起人的用户分发推送信息至 cp_push_log
    public function index()
    {
        $orders = $this->united_order_model->getFollowPushOrder();
        while(!empty($orders)) 
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                $this->united_order_model->handleFollowPush($order);
            }
            $orders = $this->united_order_model->getFollowPushOrder();
        }
    }
}