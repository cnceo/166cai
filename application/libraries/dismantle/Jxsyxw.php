<?php

class Jxsyxw
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
	public function dismantle($order)
	{
		$this->CI->load->library('dismantle/syxw');
		$this->CI->syxw->dismantle($order);
    }
}
