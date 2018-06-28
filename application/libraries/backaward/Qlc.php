<?php

class Qlc
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
		$lname = 'qlc';
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_jjsucc']);
		foreach ($issues as $issue)
		{
			$this->CI->backaward_model->award_number($lname, '23528', $issue, $issue);
		}
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_awarding']);
		foreach ($issues as $issue)
		{
			$this->CI->backaward_model->period_number($lname, '23528', $issue, $issue);
		}
	}
}
