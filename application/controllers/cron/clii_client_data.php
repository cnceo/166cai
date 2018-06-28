<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/9/22
 * Time: 14:33
 */
class Clii_Client_Data extends CI_Controller {
    private $client;
    private $i = 0;
    private $timer;
    private $errnum = 2;
    public function __construct() {
        parent::__construct();
        /*$this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');//TASK_LIST1
        $this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $this->client->on('Close',   array($this, 'onClose'));
        $this->client->on('Error',   array($this, 'onError'));
        $this->client->on('Connect', array($this, 'onConnect'));
        $this->client->on('Receive', array($this, 'onReceive'));*/
    }

    public function run()
    {
        $this->load->library('process/send_base', array('host'=>'192.168.75.128', 'port'=>10245));
        $tasks = json_encode(array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0,
            'params'=>array('db'=>'DB', 'sql'=>'select * from cp_user where 1 limit 10', 'data'=>array())));
       /* while($count++<5)
        {
            $this->send_base->send($tasks);
        }*/
        $this->send_base->send($tasks);
        echo 'asdsad';

       /* while($this->cache->Llen($this->REDIS['TASK_LIST1'])) {*/
        //$conn = $this->client->connect("192.168.75.128", 10245, 1, 1, 1);
    }

    private function send($task){
        $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $client->on("connect", function(swoole_client $cli) use($task) {
            $cli->send($task);
        });
        $client->on("receive", function(swoole_client $cli, $data) use ($task){
            echo "Receive:";
            $cli->close();
            //$cli->send($task);
            //sleep(1);
        });
        $client->on("error", function(swoole_client $cli){
            echo "error\n";
        });
        $client->on("close", function(swoole_client $cli){
            //echo "Connection close\n";
        });
        $client->connect('192.168.75.128', 10245);
    }
    /**
     * Created by huxiaoming.
     * User: huxiaoming
     * Date: 2017/9/26
     * Time: 14:21
     * $tasks:array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0, 'params'=>array('db'=>'DB/CF', 'sql'=>'', 'data'=>array()))
     */
    public function onConnect(swoole_client $client)
    {
        $tasks = json_encode(array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0,
            'params'=>array('db'=>'DB', 'sql'=>'select * from cp_user where 1 limit 10', 'data'=>array())));
        $this->client->send($tasks);//$this->cache->Rpop($this->REDIS['TASK_LIST1']));
    }

    public function onReceive(swoole_client $client, string $data)
    {
        echo 'eeee';
        $result = json_decode($data);
        log_message('LOG', print_r($result, true), 'process/client');
        $client->close();
        /*if (!$result['result'] && $result['task']['errnum']++ < $this->errnum) {
            $this->cache->Lpush($this->REDIS['TASK_LIST1'], json_encode($result['task']));
        }
        if ($client->isConnected() && $this->cache->Llen($this->REDIS['TASK_LIST1'])) {
            //$this->client->close();
            sleep(1);
            $tasks = json_encode(array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0,
                'params'=>array('db'=>'DB', 'sql'=>'select * from cp_user where 1 limit 10', 'data'=>array())));
            $this->client->send($tasks);//$this->cache->Rpop($this->REDIS['TASK_LIST1']));
        }*/


    }

    /**
     * Created by huxiaoming.
     * User: huxiaoming
     * Date: 2017/9/26
     * Time: 14:21
     * $tasks:array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0, 'params'=>array('db'=>'DB/CF', 'sql'=>'', 'data'=>array()))
     */
    public function onClose( swoole_client $client ) {
        log_message('LOG', "Client close connection\n", 'process/client');
    }
    public function onError( swoole_client $client ) {
        log_message('LOG', "Error: {$conn->errMsg}[{$conn->errCode}]\n", 'process/client-error') ;
    }
}