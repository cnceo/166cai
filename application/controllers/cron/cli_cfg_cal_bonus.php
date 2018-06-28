<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Cfg_Cal_Bonus extends MY_Controller 
{
	private $lids;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('bonus_model');
		$this->load->library('libcomm');
		$this->multi_process = $this->config->item('multi_process');
	}
	
	private function getLids()
	{
		$datetime = date('H:i:s');
		if($datetime > '20:30:00' || $datetime < '08:30:00')
		{
			$lids = $this->bonus_model->getLidMap();
		}
		else 
		{
			$lids = array(
		    //'21406' => 'syxw',
			//'21407' => 'jxsyxw',
			//'53'    => 'ks',
			//'42'    => 'jczq',
    		//'43'    => 'jclq',
			'11'    => 'sfc',
    		'19'    => 'rj'
			);
		}
		return $lids;
	}
	
	public function index()
	{
		$cname = strtolower(__CLASS__);
		$multi = $this->multi_process[$cname];
		$plimit = 1;
		$stop = $this->bonus_model->ctrlRun(null, null, true, array('model' => 'bonus_model', 'func' => 'ctrlRun', 'params' => $cname));
		$threads = array();
		$pnum = 0;
		while(!$stop)
		{
		    $this->lids = $this->getLids();
			foreach ($this->lids as $lid => $lname)
			{
				if(!in_array($lid, array(11,19))) 
				{
					continue;
				}
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
						$threads[$pid] = $lname;
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
						if(!in_array($lname, $threads))
		    			{
		    				$croname = "cron/{$this->con} cal_bonus/$lname/$lid";
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
	    			$library = "award/{$lname}";
	    			$this->load->library($library);
	    			$this->$lname->calculate($lname); //过关
	    			$this->$lname->bonus($lname);	//算奖
				}
			}
			if($multi) $this->bonus_model->threadWait($threads, 1);
			$stop = $this->bonus_model->ctrlRun(null, null, true, array('model' => 'bonus_model', 'func' => 'ctrlRun', 'params' => $cname));
			//break;
		}
	}
	
	public function cal_bonus( $lname, $lid)
	{
		if(!in_array($lid, array(11,19))) exit; 
		$library = "award/{$lname}";
    	$this->load->library($library);
    	$this->$lname->calculate($lname); //过关
    	$this->$lname->bonus($lname);	//算奖
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */