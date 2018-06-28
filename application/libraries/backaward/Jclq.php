<?php

class Jclq
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('backaward_model');
	}
	
	public function backaward()
	{
		$this->CI->backaward_model->award_jjc(43);
	}
}
