<?php

class Plw
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
	}
	
	public function dismantle($order)
	{
		$this->CI->load->library('dismantle/qxc');
		$this->CI->qxc->dismantle($order);
    }
}
