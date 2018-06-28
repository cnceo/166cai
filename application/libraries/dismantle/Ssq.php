<?php

class Ssq
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
	public function dismantle($order)
	{
		$check = false;//格式检查已废弃
		$this->CI->load->library('dismantle/dlt');
    	$parm['rballs'] = 6;	//红球数量
    	$parm['bballs'] = 1;	//蓝球数量
    	$parm['money'] = 2;		//单注金额
    	$this->CI->dlt->commSsqAndDlt($order, $parm, $check, $order['lid']);
	}
}
