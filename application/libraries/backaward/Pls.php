<?php

class Pls
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->model('backaward_model');
        $this->CI->load->library('libcomm');
        $this->order_status = $this->CI->backaward_model->orderConfig('orders');
	}
	
	public function backaward()
	{
		$lname = 'pl3';
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_jjsucc']);
		foreach ($issues as $pIssue)
		{
			$oIssue = $this->CI->libcomm->format_issue($pIssue);
			$this->CI->backaward_model->award_number($lname, '33', $pIssue, $oIssue);
		}
		$issues = $this->CI->backaward_model->getIssues($lname, $this->order_status['paiqi_awarding']);
		foreach ($issues as $pIssue)
		{
			$oIssue = $this->CI->libcomm->format_issue($pIssue);
			$this->CI->backaward_model->period_number($lname, '33', $pIssue, $oIssue);
		}
	}
}
