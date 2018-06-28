<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_Cli extends MY_Controller 
{

	public function index()
	{
		$mprocnum = 20;
		$runlist = array();
		$running = true;
		$mruntime = 1;
		$procnum = 0;
		$this->controlRun($this->con);
	    $this->load->library('processlock');
	    if (!$this->processlock->getLock('main_cli')) die();
	    $this->load->model('cron_model');
	    
		while($running)
		{
			$running = false;
			$proclist = $this->cron_model->get_crons();
			foreach ($proclist as $proc)
			{
				if(!$this->checkpro($proc) || !empty($proc['pid']))
				{
					continue;
				}
				$procnum++;
				$pid = pcntl_fork();
				if($pid == -1)
				{
					//进程创建失败 跳出循环
					$procnum --;
					continue;
				}
				else if($pid)
				{
					$this->cron_model->set_values($proc['id'], $pid);
					if($procnum > $mprocnum)
					{
						$pid = pcntl_wait($status);
						if($pid > 0 || $pid == -1)
						{
							$this->cron_model->set_values($proc['id']);
                            $procnum --;
                            if($pid == -1)
                            	log_message('LOG', "$pid:" . pcntl_wexitstatus($status), 'procerr');
						}
					}
				}
				else 
				{
					$croname = (empty($proc['path']) ? '' : "{$proc['path']}/") . "{$proc['con']} {$proc['act']}";
					system("{$this->php_path} {$this->cmd_path} $croname", $status);
					if($status)
					{
						log_message('LOG', "$croname:$status", 'procerr');
					}
					die(0);
				}
			}
			//等待的截止时间
            if(!empty($mruntime))
            {
                $dead_line = time() + 1 * $mruntime;
            }
            $runlist = $this->cron_model->get_run_list();
            while(count($runlist) > 0)
            {
                foreach($runlist as $id => $pid)
                {
                    $rpid = pcntl_waitpid($pid, $status, WNOHANG);
                    if($rpid > 0 || $rpid == -1)
                    {
                        $this->cron_model->set_values($id);
                        $procnum --;
                        if($rpid == -1)
                        	log_message('LOG', "$pid:" . pcntl_wexitstatus($status), 'procerr');
                    }
                }
                sleep(1);
                if(!empty($dead_line) && $dead_line < time())
                {
                	//log_message('LOG', "time out $procnum", 'procerr');
                    $running = true;
                    break;
                }
                $runlist = $this->cron_model->get_run_list();
            }
		}
	}
	
	private function checkpro($pro)
	{
		$result = true;
		$time = time();
		if($pro['stop_time'] > '00:00:00' && date('Y-m-d H:i:s', $time) > $pro['stop_time'])
		{
			return false;
		}
		$ndate = date('Y-m-d', $time);
		$rdate = date('Y-m-d H:i:s', strtotime($pro['end_time']) + $pro['span']);
		if($pro['start_time'] > '00:00:00' )
		{
			if(date('H:i:s', $time) < $pro['start_time'])
			{
				$result = false;
			}
			elseif($pro['end_time'] >= $ndate)
			{
				if($pro['span'] == 0)
				{
					$result = false;
				}
				elseif($pro['span'] > 0 && $rdate >= date('Y-m-d H:i:s', $time))
				{
					$result = false;
				}
			}
		}
		else 
		{
			if($pro['span'] == 0)
			{
				$result = false;
			}
			elseif($pro['span'] > 0 && $rdate >= date('Y-m-d H:i:s', $time))
			{
				$result = false;
			}
		}
		return $result;
	}
}
