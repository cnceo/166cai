<?php

class Jclq
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('DisOrder');
		$this->CI->load->library('libcomm');
	}
	
	public function dismantle($order)
	{
		$this->CI->load->library('dismantle/jczq');
    	if ($order['playType'] == '7')
    	{
    		//奖金优化
    		$betstr = $order['codes'];
    		$dis_results = $this->CI->disorder->_dismantle_optimization($betstr);
    	}
    	else
    	{
    		$betstr = $order['codes'];
    		$multi = $order['multi'];
    		$dis_results = $this->CI->disorder->_dismantle_match($betstr, $multi, $order['betTnum']);
    	}
    	
    	$this->CI->jczq->dismantle_JJC($order, $dis_results);
    }
}
