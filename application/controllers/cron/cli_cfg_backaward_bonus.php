<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Cfg_Backaward_Bonus extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('backaward_model');
		$this->multi_process = $this->config->item('multi_process');
	}
	
	public function index()
	{
		$cname = strtolower(__CLASS__);
    	$multi = $this->multi_process[$cname];
    	$plimit = 1;
		$stop = $this->backaward_model->ctrlRun(null, null, true, array('model' => 'backaward_model', 'func' => 'ctrlRun', 'params' => $cname));
		$threads = array();
		$pnum = 0;
		while(!$stop)
		{
			$funcs = array("run_backaward", 'run_comparebonus');
			foreach ($funcs as $func)
			{
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
						$threads[$pid] = $func;
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
		    			if(method_exists($this, $func) && !in_array($func, $threads))
		    			{
							$croname = "cron/{$this->con} $func";
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
    				if(method_exists($this, $func))
	    			{
						$this->$func();
	    			}
    			}
	    	}
	    	if($multi) $this->backaward_model->threadWait($threads, 1);
			$stop = $this->backaward_model->ctrlRun(null, null, true, array('model' => 'backaward_model', 'func' => 'ctrlRun', 'params' => $cname));
			//break;
		}
	}
	
	public function run_backaward()
	{
		$lids = $this->backaward_model->getLidMap();
		foreach ($lids as $lid => $lname)
		{
			if(!in_array($lid, array(11, 19, 44, 45))) continue; 
			$library = "backaward/{$lname}";
	    	$this->load->library($library);
	    	$this->$lname->backaward(); //派奖
		}
	}
	
	public function run_comparebonus()
	{
		$this->backaward_model->compareBonus();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */