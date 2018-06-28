<?php
class Act_5
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('activity_jcbzbp_model');
	}
	
	public function index($order)
	{
		return $this->CI->activity_jcbzbp_model->dealOrder($order);
	}
	
}