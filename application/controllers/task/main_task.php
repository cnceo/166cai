<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Main_Task extends Task_Controller 
{

	public function index()
	{
		$runlist = array();
		$running = true;
		$mruntime = 5;
		$this->controlRun($this->con);
	    $this->load->library('processlock');
	    if (!$this->processlock->getLock('main_task')) die();
	    $this->load->model('task_model');
	    
		while($running)
		{
			$this->controlRun($this->con);
			$proclist = $this->task_model->get_task_crons();
			pcntl_signal(SIGCHLD, SIG_IGN);
			foreach ($proclist as $proc)
			{
				if(!$this->checkpro($proc) || !empty($proc['pid']))
				{
					continue;
				}
				$pid = pcntl_fork();
				if($pid == -1)
				{
					//进程创建失败 跳出循环
					continue;
				}
				else if($pid)
				{
					$this->task_model->set_task_values($proc['id'], $pid);
				}
				else 
				{
					$croname = "task/{$proc['cron']} {$proc['act']} {$proc['id']} {$proc['lid']}";
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
            $runlist = $this->task_model->get_task_run_list();
            while(count($runlist) > 0)
            {
                foreach($runlist as $id => $pid)
                {
                    $rpid = pcntl_waitpid($pid, $status, WNOHANG);
                    if($rpid > 0 || $rpid == -1)
                    {
                        $this->task_model->set_task_values($id);
                        if($rpid == -1)
                        	log_message('LOG', "$pid:" . pcntl_wexitstatus($status), 'procerr');
                    }
                }
                sleep(1);
                if(!empty($dead_line) && $dead_line < time())
                {
                    //$running = true;
                    break;
                }
                $runlist = $this->task_model->get_task_run_list();
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
