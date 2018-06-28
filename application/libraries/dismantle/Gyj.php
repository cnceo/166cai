<?php

class Gyj
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
	public function dismantle($order)
	{
		$check = false;//格式检查已废弃
		$this->CI->load->library('dismantle/gj');
    	$this->CI->gj->dismantle($order);
    }
}
