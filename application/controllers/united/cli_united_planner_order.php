<?php

/**
 * 合买红人进行中统计脚本
 * @date:2016-12-08
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_United_Planner_Order extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
    }

    public function index()
    {
        // 取消置顶
        $this->united_order_model->checkIsTop();
        
        $orders = $this->united_order_model->getIsOrdering();

        if(!empty($orders))
        {
            foreach ($orders as $key => $order) 
            {
                $this->united_order_model->checkIsOrdering($order['uid']);
            }
        }
    }
    
    public function setTop() 
    {
    	$this->united_order_model->getIstop();
    }
    
    public function setHot()
    {
    	$this->united_order_model->setHot();
    }
}