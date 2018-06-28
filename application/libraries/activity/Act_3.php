<?php
class Act_3
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('jcmatch_model');
	}
	
	public function index($order)
	{
		return $this->CI->jcmatch_model->dealJcOrder($order);
	}
	
}