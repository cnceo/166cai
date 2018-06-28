<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clii_Cfg_Dismantle_Order extends MY_Controller 
{

	private $fun_map;
	
    public function __construct() 
    {
        parent::__construct();
        $this->load->library('DisOrder');
        $this->load->library('tools');
        $this->load->library('libcomm');
        $this->load->model('dismantle_model');
        $this->multi_process = $this->config->item('multi_process');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
    }
    
    public function index()
    {
    	$this->lock();
    	$cname = strtolower(__CLASS__);
    	$multi = $this->multi_process[$cname];
    	$plimit = 1;
		$stop = $this->dismantle_model->ctrlRun(null, null, true, array('model' => 'dismantle_model', 'func' => 'ctrlRun', 'params' => $cname));
		$threads = array();
		$pnum = 0;
		while(!$stop)
		{
			$this->fun_map = $this->dismantle_model->getLidMap();
	    	$orders = $this->dismantle_model->getDisOrders(true);
	    	if(!empty($orders))
	    	{
	    		foreach ($orders as $lid => $order)
	    		{
	    			$dis_fun = "dismantle_" . $this->fun_map[$lid];
	    			if($multi)
	    			{
	    				$pnum ++;
		    			$pid = pcntl_fork();
		    			if($pid == -1)
						{
							//进程创建失败 跳出循环
							$pnum --;
							continue;
						}
						else if($pid)
						{
							$threads[$pid] = $dis_fun;
							if($pnum >= $plimit)
							{
								$wpid = pcntl_wait($status);
								if(!empty($wpid))
								{
									unset($threads[$wpid]);
									$pnum --;
								}
							}
						}
						else
						{
			    			if(!in_array($dis_fun, $threads))
			    			{
								$croname = "cron/{$this->con} run/0/$dis_fun";
								$this->cache->hSet($this->REDIS['CLIRUNPARAMS'], "{$this->con}::$dis_fun", json_encode($order));
								system("{$this->php_path} {$this->cmd_path} $croname", $status);
								if($status)
								{
									log_message('LOG', "$croname:$status", 'procerr');
								}
			    			}
			    			die(0);
						}
	    			}
	    			else 
	    			{
						$this->run($order, '', $dis_fun);
	    			}
	    		}
	    	}
	    	if($multi) $this->dismantle_model->threadWait($threads, 1);
			$stop = $this->dismantle_model->ctrlRun(null, null, true, array('model' => 'dismantle_model', 'func' => 'ctrlRun', 'params' => $cname));
			//break;
		}
    }
    
    public function run($order, $rtype = '', $method = '')
    {
    	$flag = true;
    	if(empty($rtype))
    	{
    		$flag = false;
    		$rtype = $method;
    	}
    	else 
    	{
    		$this->lock("run_$rtype");
    	}
    	$this->exchange($order, $flag, $rtype);
    }
    
 	private function exchange($orders, $rtype = false, $method = '')
    {
    	if($rtype)
    	{
    		$orderstr = $this->cache->hGet($this->REDIS['CLIRUNPARAMS'], "{$this->con}::{$method}");
    		$orders = json_decode($orderstr, true);
    	} 
    	$lids = explode('_', $method);
    	$lid  = $lids[1];
    	$this->load->library("dismantle/{$lid}");
    	if(!empty($orders))
    	{
    		foreach ($orders as $order)
    		{
    			$this->$lid->dismantle($order); //拆票
    		}
    	}
    }
    
    private function lock($rtype='')
    {
    	$this->load->library('processlock');
        $param = $this->con . (empty($rtype) ? '' : "-$rtype");
        if (!$this->processlock->getLock($param)) 
        {
        	log_message('LOG', "This file({$param}) is running!", 'LOCK');
            die("This file({$param}) is running! \n");
        }
    }
    
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */