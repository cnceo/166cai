<?php
include_once dirname(__FILE__) . '/prclib_base.php';
class prclib_usertag_center extends prclib_base
{
	private $tables  = NULL;
	private $methods = NULL;
	private $actions = NULL;
	private $tasknum = 2;
	protected $workers = array();

	public function __construct($config = array())
	{
        parent::__construct($config);
        $this->tasknum = $this->PARAMS;
        $this->CI->load->library('stomp');
        $this->CI->config->load('stomps');
        $this->config = $this->CI->config->item('stomp');

	}

    private function get_task(){
        $process = new swoole_process(function(swoole_process $worker){
            swoole_set_process_name(sprintf('php-ps:%s', 'usertag_getask'));
            $connect = $this->CI->stomp->connect();
            $subscribe = $this->CI->stomp->subscribe($this->config['queueNames']['usertag']);
            if($connect && $subscribe)
            {
                if ($this->CI->stomp->hasPop()) {
                    $frame = $this->CI->stomp->pop();
                    $body = $frame->body;
                    $this->CI->stomp->ack($frame);
                }
            }
            $this->CI->stomp->unsubscribe($this->config['queueNames']['usertag']);
            $this->CI->stomp->disconnect();
            $worker->write("usertag:{$body}");
            $worker->exit();
        }, false, true);
        $this->workers['getask'] = $process->start();
        return $process;
    }

    public function run()
    {
        swoole_set_process_name(sprintf('php-ps:%s', 'usertag_center'));
        do {
            $recv = explode(':', $this->get_task()->read());
            if(!empty($recv[1])){
                if(count($this->workers) <= $this->tasknum){
                    $this->CreateProcess($recv[1]);
                }else{
                    $this->rebootProcess($recv[1]);
                }
            }else{
                sleep(1);
            }
            $this->processWait();
        }while(true);
    }

    /*创建任务进程，去完成具体的任务
     * */
    public function CreateProcess($tagid)
    {
        $process = new swoole_process(function(swoole_process $worker)use($tagid)
        {
            $this->processLock($worker, "usertag_center_{$tagid}");
            swoole_set_process_name(sprintf('php-ps:%s', "usertag_worker:{$tagid}"));
            sleep(10);
        }, false, false);
        $this->workers["usertag_worker_{$tagid}"] = $process->start();
    }


    /*任务进程的重启工作
      * */
    public function rebootProcess($tagid)
    {
        do{
            $ret = swoole_process::wait(true);
            if ($ret)
            {
                $prcname = array_search($ret['pid'], $this->workers);
                unset($this->workers[$prcname]);
                if($prcname !== false && $prcname !== 'getask')
                {
                    $this->CreateProcess($tagid);
                    return true;
                }
            }
        }while(true);
    }

    /*
     * 遗留进程回收功能
     * */
    public function processWait()
    {
        if(count($this->workers))
        {
            $ret = swoole_process::wait(false);
            if ($ret)
            {
                $prcname = array_search($ret['pid'], $this->workers);
                if($prcname !== false) {
                    unset($this->workers[$prcname]);
                }
            }
        }
        $this->checkMpid();
    }
}
?>
