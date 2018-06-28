<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/10/18
 * Time: 9:48
 */
include_once dirname(__FILE__) . '/prclib_base.php';
class prclib_push_base extends prclib_base
{
    private $DB = NULL;
    private $_mode = array(
        1 => 'default',
        2 => 'redpack'
    );
    
    public function __construct(array $config = array())
    {
        parent::__construct($config);
        $this->CI->load->config('server');
        $config = $this->CI->config->item("server");
        $this->CI->load->library('process/send_base', array('host' => $config['ip'], 'port' => $config['port']));
        $config = [
            'timeout' => '10',
            'keep_alive' => '3',
            'method' => 'POST'
        ];
        $this->CI->load->library('process/http_client', $config);
    }

    public function run()
    {
        swoole_set_process_name(sprintf('php-ps:%s', 'push_base'));
        $params = explode(',', $this->PARAMS);
        foreach ($params as $mode) {
            $this->do_work($mode);
        }
        $this->processWait();
    }

    private function do_work($mode = 1){
        $workers = array('mipush');
        $process = new swoole_process(function(swoole_process $worker)use($mode, $workers){
            $this->processLock($worker, "push_base_{$this->_mode[$mode]}");
            swoole_set_process_name(sprintf('php-ps:%s', "push_base:{$this->_mode[$mode]}"));
            foreach ($workers as $work) {
                if (method_exists($this, $work)) {
                    try{
                        $this->{$work}($mode);
                    }catch (Exception $e){
                        log_message('LOG', "Fatal Error:($work)" . $e->getMessage(), 'client/error.log');
                        $worker->exit();
                    }
                }
            }
        }, false, false);
        $this->workers[$this->_mode[$mode]] = $process->start();
    }

    private function mipush($mode = 1)
    {
        $this->CI->load->model('mipush_model');
        $pushInfo = $this->CI->mipush_model->getPushLog($mode, false);
        if (!empty($pushInfo)) {
            foreach ($pushInfo as $info) {
                $this->doMiPush($info);
            }
        }
    }

    private function doMiPush($info)
    {
        $appSecret = array(
            // 安卓配置
            0 => 'pnYQnu3N82RIS95iC1hxug==',
            // IOS配置
            1 => 'dEDI0cjQeFF8LVbZTsl+/A==',
            // IOS马甲版
            2 => 'sn+A1M/KwrffNBUjSjTGdw==',
            // 竞彩166
            3 => '42mp93f1E5ddGXpq9FF0EA==',
            // 超级大乐透
            4 => 'e6t632H/SrweNBlt1tCXYw==',
        );
        if (!empty($info)) {
            $datas = [
                'post_data' => [],
                'post_ip'   => '114.54.23.61',
                'back_data' => ['id' => $info['id']],
                'set_headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => "key={$appSecret[$info['platform']]}"
                ]
            ];
            $this->CI->http_client->request($info['url'], array($this, 'pushCallback'), $datas);
        }
    }

    // 回调函数
    public function pushCallback($params)
    {
        if($params['status']['statusCode'] == 200){
            $response = json_decode($params['response'], true);
            $messageId = $response['data']['id'] ? $response['data']['id'] : '';
            if ($response['result'] == 'ok' && !empty($messageId)) {
                $upparams = array(
                    'db' => 'DB',
                    'sql' => 'update cp_push_log set status = 1, messageId = ? where id = ?',
                    'data' => array('messageId' => $messageId, 'id' => $params['back_data']['id'])
                );
                $this->back_update($upparams);
            }
        }elseif($params['status']['statusCode'] < 0){
            $upparams = array(
                'db' => 'DB',
                'sql' => 'update cp_push_log set messageId = messageId + ? where id = ?',
                'data' => array('messageId' => 1, 'id' => $params['back_data']['id'])
            );
            $this->back_update($upparams);
        }
        else{
            log_message('LOG', print_r($params, true), 'mipush');
        }
    }

    private function back_update($params){
        $data = array(
            'model' => 'worker_model',
            'method' => 'execute',
            'errnum' => 0,
            'params' => $params
        );
        $this->CI->send_base->send($data);
    }
    
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
    
    public function rebootProcess($ret)
    {
        $prcname = array_search($ret['pid'], $this->workers);
        if($prcname !== false)
        {
            $modes = array_flip($this->_mode);
            $this->do_work($modes[$prcname]);
        }
        else
        {
            throw new \Exception("rebootProcess Error: no {$ret['pid']}");
        }
    }
}