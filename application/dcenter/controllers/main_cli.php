<?php
if ( ! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/**
 * Class Main_Cli
 * @property cron_model  $cron_model
 * @property processlock $processlock
 */
class Main_Cli extends MY_Controller
{

	/*
	 * 功能：数据中心脚本启动控制总入口
	 * 作者：huxm
	 * 日期：2016-03-10
	 * */
    public function index()
    {
        $mprocnum = 10;
        $running = TRUE;
        $mruntime = 1;
        $procnum = 0;
        $this->controlRun($this->con);
        $this->load->library('processlock');
        if ( ! $this->processlock->getLock('main_cli'))
        {
            die();
        }
        $this->load->model('cron_model');

        while ($running)
        {
            $running = FALSE;
            $proclist = $this->cron_model->get_crons();
            foreach ($proclist as $proc)
            {
                if ( ! $this->checkpro($proc) || ! empty($proc['pid']))
                {
                    continue;
                }

                $procnum ++;
                $pid = pcntl_fork();
                if($pid == -1)
				{
					//进程创建失败 跳出循环
					$procnum --;
					continue;
				}
				else if($pid)
				{
                    $this->cron_model->set_values($proc['id'], array('state' => 1, 'pid' => $pid));
                    if ($procnum >= $mprocnum)
                    {
                        $pid = pcntl_wait($status);
                        if ($pid > 0 || $pid == -1)
                        {
                        	$this->cron_model->set_values($proc['id'], array('state' => 0, 'pid' => 0, 
                        	'end_time' => date('Y-m-d H:i')));
                            $procnum --;
                            if($pid == -1)
                            	log_message('LOG', "$pid:" . pcntl_wexitstatus($status), 'procerr');
                        }
                    }
                }
                else
                {
                    //child
                    $croname = "cron/{$proc['con']} {$proc['act']}";
                    system("{$this->php_path} {$this->cmd_path} $croname", $status);
                    if ($status)
                    {
                        log_message('LOG', "$croname:$status", 'procerr');
                    }
                    die(0);
                }
            }
            //等待的截止时间
            if ( ! empty($mruntime))
            {
                $dead_line = time() + 60 * $mruntime;
            }

            $runlist = $this->cron_model->get_run_crons();
            while (count($runlist) > 0)
            {
                foreach ($runlist as $id => $pid)
                {
                    $rpid = pcntl_waitpid($pid, $status, WNOHANG);
                    if ($rpid > 0 || $rpid == -1)
                    {
                        unset($runlist[$id]);
                        $this->cron_model->set_values($id, array('state' => 0, 'pid' => 0, 
                        	'end_time' => date('Y-m-d H:i')));
                        $procnum --;
                        if($rpid == -1)
                        	log_message('LOG', "$pid:" . pcntl_wexitstatus($status), 'procerr');
                    }

                }
                sleep(1);
                if ( ! empty($dead_line) && $dead_line < time())
                {
                    //log_message('LOG', "time out", 'procerr');
                    $running = TRUE;
                    break;
                }
                $runlist = $this->cron_model->get_run_crons();
            }
        }
    }

    /*
     * 功能：检查进程启动条件
     * 作者：huxm
     * 日期：2016-03-10
     * */
    private function checkpro($pro)
    {
        $result = TRUE;
        $time = time();
        if ($pro['stop_time'] > '00:00:00' && date('Y-m-d H:i:s', $time) > $pro['stop_time'])
        {
            return FALSE;
        }
        $ndate = date('Y-m-d', $time);
        $rdate = date('Y-m-d H:i:s', strtotime($pro['end_time']) + $pro['span']);
        if ($pro['start_time'] > '00:00:00')
        {
            if (date('H:i:s', $time) < $pro['start_time'])
            {
                $result = FALSE;
            }
            elseif ($pro['end_time'] >= $ndate)
            {
                if ($pro['span'] == 0)
                {
                    $result = FALSE;
                }
                elseif ($pro['span'] > 0 && $rdate >= date('Y-m-d H:i:s', $time))
                {
                    $result = FALSE;
                }
            }
        }
        else
        {
            if ($pro['span'] == 0)
            {
                $result = FALSE;
            }
            elseif ($pro['span'] > 0 && $rdate >= date('Y-m-d H:i:s', $time))
            {
                $result = FALSE;
            }
        }

        return $result;
    }
}
