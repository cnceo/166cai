<?php

class Ssq
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('backaward_model');
        $this->order_status = $this->CI->backaward_model->orderConfig('orders');
	}
	
	public function backaward()
	{
		$lname = 'ssq';
		//cfg库订单对账
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_jjsucc']);
		foreach ($issues as $issue)
		{
			$this->CI->backaward_model->award_number($lname, '51', $issue, $issue);
		}
		//前台库订单对账
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_awarding']);
		foreach ($issues as $issue)
		{
			$this->CI->backaward_model->period_number($lname, '51', $issue, $issue);
		}
	}
}
