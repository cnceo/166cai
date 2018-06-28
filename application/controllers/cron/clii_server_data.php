<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/9/22
 * Time: 11:42
 */
class Clii_Server_Data extends CI_Controller
{
    private $server;
    private $monitor;
    private $errnum = 3;
    private static $db = null;
    private static $cf = null;
    private $package_eof = "\r\n\r\n";
    public function __construct() {
        parent::__construct();
        $this->processLock('server');
        $this->create_server();
    }

    public function create_server( ){
        $this->load->config('server');
        $config = $this->config->item("server");
        $this->server = new swoole_server($config['ip'], $config['port']);
        $this->server->set(array(
            'worker_num' => 6,
            'daemonize' => true,
            'backlog' => 1000,
            'max_conn' => 1000,
            'max_request' => 10000,
            'task_max_request' => 5000,
            'dispatch_mode' => 3,
            'debug_mode'=> false ,
            'task_worker_num' => 18,
            'package_eof' => $this->package_eof,
            'open_eof_check' => 1,
            'log_file' => dirname(__FILE__, 3) . '/logs/server/swoole.log',
        ));

        $this->server->on('ManagerStart', array($this, 'onManagerStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('WorkerError', array($this, 'onWorkerError'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));
        $this->server->on('Start', array($this, 'onStart'));
        $this->server->start();
    }

    public function index(){
    }

    private function get_db($name, $recon = false)
    {
        $dbname = array('db' => 'default', 'cf' => 'cfg');
        if(empty(self::$$name) || $recon){
            self::$$name = $this->load->database($dbname[$name], true);
        }
        return self::$$name;
    }

    private function is_reload($server){
        //监控的文件路径
        $filename = dirname(__FILE__, 3) . "/logs/plock/restart/" . basename(__FILE__) . '.reload';
        //创建一个inotify句柄
        $fd = inotify_init();
        //监听文件，仅监听修改操作，如果想要监听所有事件可以使用IN_ALL_EVENTS
        $wd = inotify_add_watch($fd, $filename, IN_ATTRIB);
        //加入到swoole的事件循环中
        swoole_event_add($fd, function ($fd) use ($filename, $wd, $server) {
            $events = inotify_read($fd);
            inotify_rm_watch($fd, $wd);
            if ($events) {
                foreach ($events as $event) {
                    if($event['mask'] == 4){
                        $server->reload();
                    }
                }
            }
            swoole_event_del($fd);
            fclose($fd);
            $this->is_reload($server);
        });
    }

    public function onStart(swoole_server $server){
        swoole_set_process_name(sprintf('php-ps:%s', "server_monitor"));
        $this->monitor = $server->master_pid;
        $this->is_reload($server);
    }

    public function onManagerStart(swoole_server $server){
        swoole_set_process_name(sprintf('php-ps:%s', "server_manager"));
    }

    public function onWorkerError(swoole_server $server, int $worker_id, int $worker_pid, int $exit_code, int $signal){
        //可做报警处理
        log_message('LOG', "ERROR:$worker_id, $worker_pid, $exit_code, $signal", 'server/server-error');
    }
    public function onWorkerStart(swoole_server $server, int $worker_id) {
        chdir(dirname(__FILE__, 4));
        if( $server->taskworker ) {
            swoole_set_process_name(sprintf('php-ps:%s', "server[task-$worker_id]"));
        }else{
            swoole_set_process_name(sprintf('php-ps:%s', "server[work-$worker_id]"));
        }
    }

    public function onReceive( swoole_server $server, int $fd, int $reactor_id, string $data ) {
        $work = array(
            //客户端socket句柄
            'conn' => $fd,
            //array('model'=>'', 'method'=>'', 'params'=>array('db'=>'DB/CF', 'sql'=>'', 'data'=>array()));
            'task' => $data
        );
        $server->task( $work );
    }

    public function onTask(swoole_server $server, int $task_id, int $src_worker_id, $data) {
        try{
            self::$db = $this->get_db('db');
            self::$cf = $this->get_db('cf');
            $this->load->library('process/lib_worker', array('DB' => self::$db, 'CF' => self::$cf));
            $task = swoole_serialize::unpack($this->trimTail($data['task']));
            if(method_exists($this->lib_worker->{$task['model']}, $task['method'])){
                $result = $this->doWork($task);
                $redata = array(
                    'conn'   => $data['conn'],
                    'result' => swoole_serialize::pack(array('task' => ($result ? '' : $task), 'result' => $result))
                );
                return $redata;
            }
            else{
                throw new Exception("Fatal error: {$task['model']}[{$task['method']}] function is not exist!", 500);
            }
        }catch( Exception $e ) {
            log_message('LOG', $this->errMessage($e), 'process/server-error');
            if($e->getCode() == 500){
                $task['errnum'] = $this->errnum;
                $result = false;
            }
            if($e->getCode() == '2006'){
                $result = $this->doWork($task, true);
            }
            $redata = array(
                'conn'   => $data['conn'],
                'result' => swoole_serialize::pack(array('task' => ($result ? '' : $task), 'result' => $result))
            );
            return $redata;
        }
    }

    private function doWork(&$task, $retry = false){
        try{
            if($retry){
                self::$db = $this->get_db('db', true);
                self::$cf = $this->get_db('cf', true);
                $this->lib_worker->{$task['model']}->init(array('DB' => self::$db, 'CF' => self::$cf));
            }
            return $this->lib_worker->{$task['model']}->{$task['method']}($task['params']);
        }catch (Exception $e){
            if($retry){
                log_message('LOG', $this->errMessage($e), 'process/server-error');
                return flase;
            }else{
                throw new Exception($e->getMessage(), $e->getCode());
            }
        }
    }

    public function onFinish(swoole_server $server, int $task_id, $data) {
        $server->send($data['conn'], $data['result'] . $this->package_eof);
    }

    private function processLock($pname){
        $pname = __CLASS__ . "-$pname";
        $this->load->library('processlock');
        if (!$this->processlock->getLock($pname)) {
            exit("$pname 已启动！");
        }
    }

    private function trimTail($params){
        return preg_replace("/({$this->package_eof})$/is", '', $params);
    }

    private function errMessage($e){
        return $e->getMessage() . ' (errPath:' . $e->getFile() . '[errLine:' . $e->getLine() . '])';
    }
}
