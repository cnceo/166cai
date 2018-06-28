<?php
/**
 * Created by PhpStorm.
 * User: HuXiaoMing
 * Date: 2017/12/28
 * Time: 11:00
 */
include_once dirname(__FILE__) . '/prclib_base.php';
class prclib_task_center extends prclib_base
{
    protected $workers = array();
    public function __construct(array $config = array())
    {
        parent::__construct($config);
    }

    public function run()
    {
        swoole_set_process_name(sprintf('php-ps:%s', 'task_center'));
        $task = 0;
        while ($task ++ < $this->PARAMS){
            $this->CreateProcess($task);
        }
        $this->processWait();
    }

    /*创建任务进程，去完成具体的任务
     * */
    public function CreateProcess($task)
    {
        $process = new swoole_process(function(swoole_process $worker)use($task)
        {
            $this->processLock($worker, "task_center-{$task}");
            swoole_set_process_name(sprintf('php-ps:%s', "task_center:(work{$task})"));
            //添加任务逻辑；
            $this->doWork();
            sleep(1);
        }, false, false);
        $this->workers["task_center:{$task}"] = $process->start();
    }
    
    private function doWork()
    {
        $this->CI->load->model('prcworker/server_task_model');
        $this->CI->load->library('stomp');
        $this->CI->config->load('stomps');
        $config = $this->CI->config->item('stomp');
        $connect = $this->CI->stomp->connect();
        $subscribe = $this->CI->stomp->subscribe($config['queueName']);
        if($connect && $subscribe)
        {
            $this->CI->load->library('process/lib_task');
            $i = 0;
            while ($this->CI->stomp->hasPop())
            {
                if($i > 200)
                {
                    break;
                }
                $frame = $this->CI->stomp->pop();
                if($frame)
                {
                    try
                    {
                        $result = $this->CI->lib_task->run($frame->body);
                        if($result['code'] == $this->CI->lib_task::CODE_RETRY)
                        {
                            //错误处理
                            $this->CI->server_task_model->errorRecord(json_encode($frame->body));
                        }
                        elseif ($result['code'] == $this->CI->lib_task::CODE_ERROR)
                        {
                            //写日志
                            log_message('log', json_encode($frame), 'task_center_error');
                        }
                        
                        //从队列中清除消息
                        $this->CI->stomp->ack($frame);
                    }
                    catch (Exception $e)
                    {
                        //输出错误信息
                        log_message('LOG', "Fatal Error:" . $e->getMessage(), 'task_center_error');
                        $this->CI->stomp->unsubscribe($config['queueName']);
                        $this->CI->stomp->disconnect();
                        return ;
                    }
                }
                $i++;
            }
            
            $this->CI->stomp->unsubscribe($config['queueName']);
            $this->CI->stomp->disconnect();
        }
    }

    /* 回收任务进程，如果有任务进程结束或挂掉master进程负责回收，
       * 不能使用manager进程直接回收，因为manager要处理信号问题。
       * */
    public function processWait()
    {
        while(true)
        {
            if(count($this->workers))
            {
                $ret = swoole_process::wait(false);
                if ($ret)
                {
                    $this->rebootProcess($ret);
                }
            }
            $this->checkMpid();
            usleep(500000);
        }
    }

    /*任务进程的重启工作
      * */
    public function rebootProcess($ret)
    {
        $prcname = array_search($ret['pid'], $this->workers);
        if($prcname !== false)
        {
            $prcnames = explode(':', $prcname);
            $this->CreateProcess($prcnames[1]);
        }
        else
        {
            throw new \Exception("rebootProcess Error: no {$ret['pid']}");
        }
    }
}