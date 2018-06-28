<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Chase_Bet_All extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('ticket_model');
        $this->multi_process = $this->config->item('multi_process');
    }

    public function index()
    {
        $methods = array('chase', 'statistics');
        $cname = strtolower(__CLASS__);
        $multi = $this->multi_process[$cname];
        $plimit = $this->multi_process['process_num_limit'];
        $stop = $this->ticket_model->ctrlRun($cname);
        $threads = array();
        $pnum = 0;
        while(!$stop)
        {
            foreach ($methods as $method)
            {
                if($multi)
                {//开启多进程
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
                        $threads[$pid] = $method;
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
                        if(method_exists($this, $method) && !in_array($method, $threads))
                        {
                            $this->ticket_model->cfgDB = $this->load->database('cfg', true);
                            $this->$method();
                        }
                        die(0);
                    }
                }
                else 
                {
                    if(method_exists($this, $method))
                    {
                        $this->$method();
                    }
                }
            }
            if($multi) $this->ticket_model->threadWait($threads, 1);
            $stop = $this->ticket_model->ctrlRun($cname);
            //break;
        }
    }

    public function statistics(){
        //追号统计脚本
        system("{$this->php_path} {$this->cmd_path} cron/cli_chase_statistics index", $status);
    }

    public function chase()
    {
        //投单脚本
        system("{$this->php_path} {$this->cmd_path} cron/cli_chase_bet index", $status);
    }
}