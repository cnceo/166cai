<?php
class Cli_SemailProcess extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
    }

    public function index($stime=0) 
    {
    	$tspan = 60;
    	$stime = $this->startTime($stime);
    	$this->load->model('order_model');
    	$contine = true;
    	while($contine)
    	{
    		$sdate = date('Y-m-d H:i:s', $stime);
    		$etime = strtotime("$tspan min", $stime);
    		$edate = date('Y-m-d H:i:s', $etime);
    		if($etime < time())
    		{
    			$stime = $this->startTime($etime);
    			$contine = true;
    		}
    		else 
    		{
    			$contine = false;
    		}
    		//出票成功发邮件功能
    		$this->order_model->ticketEmail($sdate, $edate);
    	}
    }
    
    private function startTime($stime=null)
    {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$ini_time = strtotime('-1 day', time());
    	if(empty($stime))
    	{
    		$stime = $this->cache->redis->get($REDIS['ORDERS_EMAIL_START_TIME']);
    	}
    	if($stime < $ini_time)
    	{
    		$stime = $ini_time;
    	}
    	$this->cache->redis->save($REDIS['ORDERS_EMAIL_START_TIME'], $stime, 0);
    	return $this->cache->redis->get($REDIS['ORDERS_EMAIL_START_TIME']);
    }
}
