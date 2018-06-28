<?php
class Act_4
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('jjactivity_model');
	}
	
	public function index($order)
	{
		return $this->CI->jjactivity_model->dealJjOrder($order);
	}
	
}