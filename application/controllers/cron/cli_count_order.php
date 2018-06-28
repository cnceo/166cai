<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：统计
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_count_order extends MY_Controller{
	
    public function __construct(){
        parent::__construct();
        $this->filepath = FCPATH.'application/logs/plock';
    }
    
    public function getCount() {
    	$this->load->model('order_model');
    	while (true) {
    		if(file_exists("{$this->filepath}/cli_count_order.start"))
        	{
        		if(file_exists("{$this->filepath}/cli_count_order.stop"))
        			unlink("{$this->filepath}/cli_count_order.stop");
        		if(file_exists("{$this->filepath}/cli_count_order.start"))
        			unlink("{$this->filepath}/cli_count_order.start");
        	}
        	
			if(file_exists("{$this->filepath}/cli_count_order.stop"))
        	{
        		break;
        	}
        	$res = $this->order_model->countSplitOrders();
        	$this->order_model->freshStatics($res);
        	sleep(1);
    	}
    }

}
