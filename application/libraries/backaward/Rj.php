<?php

class Rj
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
		$lname = 'rsfc';
		$issues = $this->CI->backaward_model->getMids($lname, $this->order_status['paiqi_jjsucc'], 'rjrstatus');
		foreach ($issues as $pIssue)
		{
			$oIssue = $this->CI->libcomm->format_issue($pIssue);
			$this->CI->backaward_model->award_sfc($pIssue, $oIssue, 19, 'rjrstatus');
		}
		$issues = $this->CI->backaward_model->getMids($lname, $this->order_status['paiqi_awarding'], 'rjrstatus');
		foreach ($issues as $pIssue)
		{
			$oIssue = $this->CI->libcomm->format_issue($pIssue);
			$this->CI->backaward_model->period_sfc($pIssue, $oIssue, 19, 'rjrstatus');
		}
	}
}
