<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/9/22
 * Time: 14:33
 */
class send_base{
    private $client;
    private $host = '0.0.0.0';
    private $port = 10245;
    private $errnum = 2;
    private $package_eof = "\r\n\r\n";
    public function __construct($config) {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }
    /**
     * Created by huxiaoming.
     * User: huxiaoming
     * Date: 2017/9/26
     * Time: 14:21
     * $params:array('model'=>'worker_model', 'method'=>'execute', 'errnum'=>0, 'params'=>array('db'=>'DB/CF', 'sql'=>'', 'data'=>array()))
     * $async 是否阻塞
     */
    public function send(array $params, $async = true){
        $task = swoole_serialize::pack($params);
        $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $client->set(
            array(
                'package_eof' => $this->package_eof,
                'open_eof_check' => 1
            )
        );
        $client->on("connect", function(swoole_client $cli) use($task) {
            $cli->send($task . $this->package_eof);
        });
        $client->on("receive", function(swoole_client $cli, $data) {
            $result = swoole_serialize::unpack($this->trimTail($data));
            if (empty($result['result']) && $result['task']['errnum']++ < $this->errnum) {
                $cli->send(swoole_serialize::pack($result['task']) . $this->package_eof);
            }else{
                $cli->close();
            }
        });
        $client->on("error", function(swoole_client $cli) {
            //可做报警处理
            log_message('LOG', "Error: {$cli->errMsg}[{$cli->errCode}]", 'process/client-error');
        });
        $client->on("close", function(swoole_client $cli){
         });
        $client->connect($this->host, $this->port, 10, $async);
    }

    private function trimTail($params){
        return preg_replace("/({$this->package_eof})$/is", '', $params);
    }
}