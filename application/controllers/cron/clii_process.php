<?php
class Clii_Process extends CI_Controller
{
    private $mpid = 0;
    private $works = [];
    /*
     * 提票进程配置说明：
     * 'ticket_base:127[,1]' => '1[,0]:1[,1];1[,1]:1[,1];'
     * */
    private $processes = array();
    private $master;
    public function __construct(){
        parent::__construct();
        try {
            /* 创建管理进程， 获得命令信号后做进程的重启工作
            * */
            $process = new swoole_process(function(swoole_process $worker){
                swoole_set_process_name(sprintf('php-ps:%s', 'manager'));
                /*
                 * 停止所有任务进程的信号处理函数
                 * */
                swoole_process::signal(SIGTERM, function() use($worker){
                    swoole_process::kill($this->mpid, SIGTERM);
                    swoole_process::wait(true);
                    $worker->exit();
                });
                /*  通过给管理进程（manager）发送USR1信号热重启master和其
                 *  所有子进程，从而快速实现所有任务进程的重启。
                 * */
                swoole_process::signal(SIGUSR1, function(){
                    //$this->master->write('给子进程发消息');
                    swoole_process::kill($this->mpid, SIGTERM);
                });
                /*
                 *  解决直接kill掉master进程，master进程会进入僵死状态的问题。一但
                 *  出现如上问题子master的所有子进程的父进程变为1号进程并且无法通过
                 *  发送消息获得master进程是否存在
                 * */
                swoole_process::signal(SIGCHLD, function(){
                    swoole_process::wait(true);
                    $this->create_master(true);
                });
                $this->processLock($worker, 'manager');
                $this->create_master(false);
                $this->is_reload();
            }, false, false);
            $process->start();
        }catch (\Exception $e){
            die('ALL ERROR: '.$e->getMessage());
        }
    }

    private function is_reload(){
        //监控的文件路径
        $filename = dirname(__FILE__, 3) . "/logs/plock/restart/" . basename(__FILE__) . '.reload';
        //创建一个inotify句柄
        $fd = inotify_init();
        //监听文件，仅监听修改操作，如果想要监听所有事件可以使用IN_ALL_EVENTS
        $wd = inotify_add_watch($fd, $filename, IN_ATTRIB);
        //加入到swoole的事件循环中
        swoole_event_add($fd, function ($fd) use ($filename, $wd) {
            $events = inotify_read($fd);
            inotify_rm_watch($fd, $wd);
            if ($events) {
                foreach ($events as $event) {
                    if($event['mask'] == 4){
                        swoole_process::kill($this->mpid, SIGTERM);
                    }
                }
            }
            swoole_event_del($fd);
            fclose($fd);
            $this->is_reload();
        });
    }
    public function index(){
    }

    private function init_process(){
        $process = new swoole_process(function(swoole_process $worker){
            swoole_set_process_name(sprintf('php-ps:%s', 'initdata'));
            $CFDB = $this->load->database('cfg', true);
            $processes = $CFDB->query('select cname, params, server from cp_crontab_config where ctype = 3 and delflag = 0')->getAll();
            $worker->write(json_encode($processes));
            $worker->exit();
        }, false, true);
        $this->works['init'] = $process->start();
        return $process;
    }
    /* 创建监控进程， 实现挂掉任务进程的回收和重启
     * */
    private function create_master($init = false)
    {
        try {
            $process = new swoole_process(function(swoole_process $worker)use($init){
                swoole_set_process_name(sprintf('php-ps:%s', 'master'));
                if($init){
                    $server = $this->getServerIp();
                    $initProcess = $this->init_process();
                    $recv = $initProcess->read();
                    $processes = json_decode($recv, true);
                    $this->processes = array();
                    foreach ($processes as $processe){
                        if(empty($processe['server']) || $server == $processe['server']){
                            $this->processes[$processe['cname']] = $processe['params'];
                        }
                    }
                }else{
                    $this->processes = $this->getProcesses();
                }
                $this->onMaster();
            }, false, true);
            $this->mpid = $process->start();
            $this->master = $process;
        }catch (\Exception $e){
            die('ALL ERROR: '.$e->getMessage());
        }
    }

    private function onMaster(){
        $this->mpid = posix_getpid();
        foreach($this->processes as $precess => $params){
            $this->CreateProcess($precess);
        }
        $this->processWait();
    }

    private function on_newprocess(&$worker, $prcname)
    {
        $params = explode(':', $prcname);
        $this->processLock($worker, $params[0]);
        $this->load->library("process/prclib_{$params[0]}",
            array(
                'WORKER' => $worker, 'MPID' => $this->mpid, 'ACNAME' => $params[1],
                'PARAMS' => $this->processes[$prcname]
            )
        );
        $this->{"prclib_{$params[0]}"}->run();
    }
    /*创建任务进程，去完成具体的任务
     * */
    public function CreateProcess($prcname=null){
        $process = new swoole_process(function(swoole_process $worker)use($prcname){
            swoole_set_process_name(sprintf('php-ps:%s', $prcname));
            $this->on_newprocess($worker, $prcname);
        }, false, false);
        $pid = $process->start();
        $this->works[$prcname] = $pid;
        return $pid;
    }
    /*任务进程的重启工作
     * */
    public function rebootProcess($ret){
        $prcname = array_search($ret['pid'], $this->works);
        if($prcname !== false){
            //初始化进程结束，无需重启
            if($prcname != 'init'){
                $new_pid = $this->CreateProcess($prcname);
            }
            return;
        }else{
            unset($this->works[$prcname]);
            throw new \Exception('rebootProcess Error: no pid');
        }
    }
    /* 回收任务进程，如果有任务进程结束或挂掉master进程负责回收，
     * 不能使用manager进程直接回收，因为manager要处理信号问题。
     * */
    public function processWait(){
        while(true) {
            if(count($this->works)){
                $ret = swoole_process::wait(false);
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            }
            sleep(1);
        }
    }

    private function processLock(&$worker, $pname){
        $pname = __CLASS__ . "-$pname";
        $this->load->library('processlock');
        if (!$this->processlock->getLock($pname)) {
            $worker->exit();
        }
    }
    /*
     * 提票进程配置说明：
     * 'ticket_base:127[,1]' => '1[,0]:1[,1];1[,1]:1[,1];'
     * */
    private function getProcesses(){
        switch ($this->getServerIp()){
            case '120.132.33.197':
                return array('task_center' => '2', 'sms_base' => '1,2');
                break;
            case '120.132.33.198':
                return array('push_base' => '1,2', 'ticket_base:255,3' => '1:31;2:27;4:10;8:26;16:3;32:6;64:24;128:20;1,1:1;1,2:18');
                break;
            case '123.59.105.39':
                return array('push_base' => '1,2', 'ticket_base:255,3' => '1:31;2:27;4:10;8:26;16:3;32:6;64:24;128:20;1,1:1;1,2:18', 'task_center' => '2', 'sms_base' => '1,2');
                break;
            default :
                return array('push_base' => '1,2');
                break;
        }
    }

    private function getServerIp()
    {
        @exec("ifconfig", $adress);
        preg_match('/.+?addr:(\d+\.\d+\.\d+\.\d+).+/', $adress[1], $match);
        return $match[1];
    }
}
?>