<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/14
 * Time: 10:06
 */

class prclib_base
{
    protected $CI     = NULL;
    protected $ACNAME = NULL;
    protected $WORKER = NULL; /*当前任务进程*/
    protected $MPID   = NULL; /*当前任务父进程ID*/
    protected $PARAMS = NULL;
    protected $WLIMIT = NULL;
    protected function __construct($config = array())
    {
        $this->CI = &get_instance();
        $this->CI->load->config('config');//重启重新加载config
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    protected function checkMpid(){
        if($this->MPID){
            if(!swoole_process::kill($this->MPID, 0)){
                $this->WORKER->exit();
            }
        }
        //对子进程的时间监控
        if($this->WLIMIT && !empty($this->workers)){
            foreach ($this->workers as $key => $worker){
                $params = explode('-', $key);
                $otime = array_pop($params);
                if(time() - $otime > $this->WLIMIT){
                    if(swoole_process::kill($worker, 0))
                        swoole_process::kill($worker, SIGTERM);
                }
            }
        }
    }

    protected function getWorkKey($key){
        if($this->WLIMIT){
            $key .= "-" . microtime(true);
        }
        return $key;
    }

    protected function processLock(&$worker, $pname){
        $pname = __CLASS__ . "-$pname";
        $this->CI->load->library('processlock');
        if (!$this->CI->processlock->getLock($pname)) {
            $worker->exit();
        }
    }
}