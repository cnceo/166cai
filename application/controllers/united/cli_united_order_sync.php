<?php

/**
 * 合买订单状态同步脚本
 * @date:2016-12-08
 */

class Cli_United_Order_Sync extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('united_order_model');
        $this->load->model('follow_order_model');
    }

    public function index()
    {
        $contine = true;
        while($contine)
        {
            $this->controlRestart($this->con);
        	//便于测试环境的调试
        	if(ENVIRONMENT==='development')
        	{
        		$this->runCron();
        	}else{
        		$croname = "united/cli_united_order_sync runCron";
	    		system("{$this->php_path} {$this->cmd_path} $croname",  $status);
        	}
            sleep(5);
        }
    }
    
    public function runCron()
    {
        // 同步cp_orders至cp_united_orders
    	$this->united_order_model->syncOrder();

        // 同步cp_united_join至cp_united_follow_orders 一小时内未处理的跟单小订单
        $orders = $this->follow_order_model->getFollowJoinInfo();
        if(!empty($orders))
        {
            foreach ($orders as $order) 
            {
                $this->follow_order_model->handleCompleteOrder($order);
            }
        }
    }
}
