<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Hight_Quality_User_Statistic extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('hight_quality_user_model', 'hqu_model');
	}
	
	public function index()
	{
		$stime = strtotime('-1 day');
		$sdate = date('Y-m-d', $stime);
		while ($sdate < date('Y-m-d'))
		{
			$this->hqu_model->quality_user_statistic($stime);
			$this->hqu_model->total_recharge_withdraw($stime);
			$stime = strtotime('1 day', $stime);
			$sdate = date('Y-m-d', $stime);
		}
	}
}  
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */