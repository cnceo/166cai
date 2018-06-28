<?php

/**
 * 合买订单发邮件脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Order_Email extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
    }

    // 扫描一小时内修改的出票成功、未满员撤单、发起人撤单
    public function index()
    {
        $orders = $this->united_order_model->getEmailOrder();

        while(!empty($orders)) 
        {
            $this->controlRestart($this->con);
            foreach ($orders as $order) 
            {
                $this->united_order_model->handleEmail($order);
            }
            $orders = $this->united_order_model->getEmailOrder();
        }
    }
}