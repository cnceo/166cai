<?php

/**
 * 合买订单发短信脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Order_Message extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
        $this->load->model('follow_order_model');
    }

    public function index()
    {
        // 合买短信 扫描一小时内修改的中奖及失败订单
        $this->handleUnitedMessage();

        // 跟单短信 半小时内
        $this->handleFollowMessage();
    }

    public function handleUnitedMessage()
    {
        $orders = $this->united_order_model->getMessageOrder();
        while(!empty($orders)) 
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                $this->united_order_model->handleMessage($order);
            }
            $orders = $this->united_order_model->getMessageOrder();
        }
    }

    public function handleFollowMessage()
    {
        $orders = $this->follow_order_model->getMessageOrder();
        while(!empty($orders))
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                $this->follow_order_model->handleMessage($order);
            }
            $orders = $this->follow_order_model->getMessageOrder();
        }
    }
}