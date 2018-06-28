<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Statistics extends MY_Controller
{
    public function __construct()
    {
    	parent::__construct();
    	$this->load->model('statistics_model');
    }
    
    public function index($date = '')
    {
    	$date = $date ? $date : date('Y-m-d', strtotime("-1 day"));
    	/*$this->statistics_model->orderStatistics($date);
		$this->statistics_model->orderStatistics1($date);
    	$this->statistics_model->rechargeStatistics($date);
    	$this->statistics_model->withdrawStatistics($date);
    	$this->statistics_model->costStatistics($date);*/
    	$this->walletStatistics();
    	$this->partnerStatistics();
    }
    
    /**
     * 账户余额对账
     * @param unknown_type $startDate
     */
    public function walletStatistics($startDate = '')
    {
    	$date  = date('Y-m-d', strtotime("-1 day"));
    	if(!$startDate)
    	{
    		$this->statistics_model->walletStatistics($date);
    	}
    	else
    	{
    		$dates = $this->getAllDates($startDate, $date);
    		foreach ($dates as $vDate)
    		{
    			$this->statistics_model->walletStatistics($vDate);
    		}
    	}
    }
    
    /**
     * 供应商对账统计
     * @param unknown_type $date
     */
    public function partnerStatistics($startDate = '')
    {
    	$date  = date('Y-m-d', strtotime("-1 day"));
    	if(!$startDate)
    	{
    		$this->statistics_model->partnerStatistics($date);
    	}
    	else
    	{
    		$dates = $this->getAllDates($startDate, $date);
    		foreach ($dates as $vDate)
    		{
    			$this->statistics_model->partnerStatistics($vDate);
    		}
    	}
    	//报警
    	//$this->statistics_model->statisticsWarning();
    }
    
    public function test()
    {
    	$s = date('Y-m-d', strtotime('-61 day'));
    	$e = date('Y-m-d', strtotime('-1 day'));;
    	$allDates = $this->getAllDates($s, $e);
    	foreach ($allDates as $date)
    	{
    		$this->statistics_model->orderStatistics($date);
    		$this->statistics_model->orderStatistics1($date);
    		$this->statistics_model->rechargeStatistics($date);
    		$this->statistics_model->withdrawStatistics($date);
    		$this->statistics_model->costStatistics($date);
    	}
    }
    
    /**
     * 处理日期数组
     * @param string $s	开始日期
     * @param string $e	结束日期
     * @return array()
     */
    private function getAllDates($s, $e)
    {
    	if (empty($s) || empty($e) || (strtotime($s) > strtotime($e)))
    	{
    		return array();
    	}
    	$res = array();
    	$datetime1 = new DateTime($s);
    	$datetime2 = new DateTime($e);
    	$interval  = $datetime1->diff($datetime2);
    	$days = $interval->format('%a');
    	for ($j = 0; $j <= $days; $j++)
    	{
	    	$time = strtotime("+$j days", strtotime($s));
	    	$val = date("Y-m-d", $time);
	    	array_push($res, $val);
    	}
    	 
    		return $res;
    	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */