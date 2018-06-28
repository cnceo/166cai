<?php
class Cli_ScanProcess extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index($stime=0) {
    	$tspan = 60;
    	$stime = $this->startTime($stime);
    	$this->load->model('user_model');
    	$this->load->model('chase_model');
    	$count = array();
    	$contine = true;
    	while($contine)
    	{
    		$sdate = date('Y-m-d H:i:s', $stime);
    		$etime = strtotime("$tspan min", $stime);
    		$edate = date('Y-m-d H:i:s', $etime);
    		//出票短信功能
    		$this->user_model->ticketSms($sdate, $edate);
    		if($etime < time())
    		{
    			$stime = $this->startTime($etime);
    			$contine = true;
    		}else 
    		{
    			$contine = false;
    		}
    		//追号完成短信发送功能
    		$this->chase_model->chaseSendSms();
            // 不中包赔发短信
            $this->chase_model->chaseActivitySendSms();
    	}
    }
    
    public function win($stime=0) {
        $tspan = 60;
        $stime = $this->startTime($stime, 'win');
        $this->load->model('sms_model');
        $contine = true;
        while($contine)
        {
            $sdate = date('Y-m-d H:i:s', $stime);
            $etime = strtotime("$tspan min", $stime);
            $edate = date('Y-m-d H:i:s', $etime);
            //出票短信功能
            $this->sms_model->sendWin($sdate, $edate);
            if($etime < time()) {
                $stime = $this->startTime($etime, 'win');
                $contine = true;
            }else
                $contine = false;
        }
        
    }
    
    private function startTime($stime=null, $ctype = '')
    {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$ini_time = strtotime('-1 day', time());
    	if(empty($stime))
    	{
    		$stime = $this->cache->redis->get($REDIS['ORDERS_SCAN_START_TIME'].$ctype);
    	}
    	if($stime < $ini_time)
    	{
    		$stime = $ini_time;
    	}
    	$this->cache->redis->save($REDIS['ORDERS_SCAN_START_TIME'].$ctype, $stime, 0);
    	return $this->cache->redis->get($REDIS['ORDERS_SCAN_START_TIME'].$ctype);
    }
}
