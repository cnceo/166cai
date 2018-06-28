<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Syncr_Model $syncr_model
 * @property             $multi_process
 * @property             $db_config
 * @property             $cfg_orders
 */
class Clii_Cfg_Collect_Status extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bonus_model');
        $this->multi_process = $this->config->item('multi_process');
    }

    public function index()
    {
    	$this->lock();
        $cname = strtolower(__CLASS__);
        $multi = $this->multi_process[$cname];
        $pLimit = 1;
        $stop = $this->bonus_model->ctrlRun(null, null, true, array('model' => 'bonus_model', 'func' => 'ctrlRun', 'params' => $cname));
        $threads = array();
        $pNum = 0;
        while ( ! $stop)
        {
        	$this->methods = $this->getCrontabList(1);
            foreach ($this->methods as $method)
            {
                $method = "collect_$method";
                if ($multi)
                {
                	$pNum ++;
                    $pid = pcntl_fork();
                    if($pid == -1)
					{
						//进程创建失败 跳出循环
						$pNum --;
						continue;
					}
					else if($pid)
					{
                    	if(!in_array($method, $threads))
                    	{
                        	$threads[$pid] = $method;
                    	}
                        if ($pNum >= $pLimit)
                        {
                            $wPid = pcntl_wait($status);
                            if (!empty($wPid) || $wPid == -1)
                            {
                                unset($threads[$wPid]);
                                $pNum --;
                            }
                        }
                    }
                    else
                    {
                       if (!in_array($method, $threads))
                       {
							$croname = "cron/{$this->con} run/$method";
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
                	$this->run($method, false);
                }
            }
            if ($multi)
            {
                $this->bonus_model->threadWait($threads, 1);
            }
            $stop = $this->bonus_model->ctrlRun(null, null, true, array('model' => 'bonus_model', 'func' => 'ctrlRun', 'params' => $cname));
            $pNum = count($threads);
            //break;
        }
    }
    
    public function run($method, $lock = true)
    {
    	if($lock)
    	{
    		$this->lock("run_$method");
    	}
    	$params = explode('_', $method);
    	if(empty($params[2]))
    	{
    		$this->bonus_model->calOrderStatus(); 
    	}
    	else
    	{
    		$this->bonus_model->calOrderStatus($params[2]);
    	}
    }
	    
    private function getCrontabList($ctype)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$lists = json_decode($this->cache->get($REDIS['CRONTAB_CONFIG']), true);
    	return $lists[$ctype];
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
