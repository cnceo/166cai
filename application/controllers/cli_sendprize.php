<?php
class Cli_SendPrize extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('order_model');
    	$this->load->model('activity_model');
    }

    public function index($stime=0) {
    	$tspan = 60;
    	$stime = $this->startTime($stime);
    	$count = array();
    	$contine = true;
    	while($contine)
    	{
    		$sdate = date('Y-m-d H:i:s', $stime);
    		$etime = strtotime("$tspan min", $stime);
    		//便于测试环境的调试
        	if(ENVIRONMENT==='development')
        	{
        		$re = $this->runCron($stime, $etime);
        	}else{
        		$rarray = null;
        		$croname = "cli_sendprize runCron/$stime/$etime";
	    		exec("{$this->php_path} {$this->cmd_path} $croname", $rarray, $status);
	    		$re = $rarray[0];
        	}
    		if(($re && $etime < time()) || (!$re && ($count["{$stime}-$tspan"]++ < 3)))
    		{
    			if($re)
    			{
                    if(ENVIRONMENT==='development')
                    {
                        $this->runCron($stime, $etime);
                    }else{
                        exec("{$this->php_path} {$this->cmd_path} $croname", $rarray, $status);
                    }
    				$stime = $this->startTime($etime);
    			}
    			$contine = true;
    		}else 
    		{
    			sleep(5);
    			//$contine = false;
    		}
    		//重试三次失败
    		if(!$re && $count["{$stime}-$tspan"] >= 3)
    		{
    			log_message('LOG', "Fail: {$sdate}->$tspan min");
    		}
    		break;
    	}
    }
    
	public function runCron($stime, $etime)
    {
    	$sdate = date('Y-m-d H:i:s', $stime);
    	$edate = date('Y-m-d H:i:s', $etime);
    	//派奖功能
    	$re = $this->order_model->CheckStatus($sdate, $edate);
    	//过期订单更新
    	$this->order_model->updateFailOrder($sdate);
    	//联盟返点活动
    	$this->activity_model->activity_deal($sdate, $edate);
    	if($re)
    	{
	    	if(ENVIRONMENT==='development')
	        {
	        	return $re;
	        }else{
	        	echo 1;
	        }
    	}
    }
    
    private function startTime($stime=null)
    {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$ini_time = strtotime('-1 day', time());
    	if(empty($stime))
    	{
    		$stime = $this->cache->redis->get($REDIS['ORDERS_CHECK_START_TIME']);
    	}
    	if($stime < $ini_time)
    	{
    		$stime = $ini_time;
    	}
    	$this->cache->redis->save($REDIS['ORDERS_CHECK_START_TIME'], $stime, 0);
    	return $this->cache->redis->get($REDIS['ORDERS_CHECK_START_TIME']);
    }
}
