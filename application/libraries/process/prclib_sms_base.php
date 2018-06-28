<?php
/**
 * Created by PhpStorm.
 * User: huxiaoming
 * Date: 2017/10/18
 * Time: 9:48
 */
include_once dirname(__FILE__) . '/prclib_base.php';
class prclib_sms_base extends prclib_base
{
    private $DB = NULL;
    private $_mode = array(
        1 => 'register',
        2 => 'others'
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
        $this->url = $this->CI->config->item('sms_url');
        $this->CI->load->library('process/http_client', $config);
    }

    public function run()
    {
        swoole_set_process_name(sprintf('php-ps:%s', 'sms_base'));
        $params = explode(',', $this->PARAMS);
        foreach ($params as $mode) {
            $this->do_work($mode);
        }
        $this->processWait();
    }

    private function do_work($mode = 1){
        $workers = array('send');
        $process = new swoole_process(function(swoole_process $worker)use($mode, $workers){
            $this->processLock($worker, "sms_base_{$this->_mode[$mode]}");
            swoole_set_process_name(sprintf('php-ps:%s', "sms_base:{$this->_mode[$mode]}"));
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
    
    private function send($mode = 1) 
    {
        $this->CI->load->model('sms_model');
        $data = $this->CI->sms_model->getData($mode);
        foreach ($data as $val) 
        {
            $sms = array(
                'post_data' => [
                    'phone' => $val['phone'], 
                    'msg' => iconv('UTF-8', 'GBK', $val['content']), 
                    'smsType' => ($val['ctype'] == '0') ? '5' : $val['ctype'],
                    'pid'   => '122',
                    'positionId' => $val['position'],
                    'clientIp' => $val['uip']
                ],
                'back_data' => ['id' => $val['id'], 'phone' => $val['phone']],
            );
            $this->CI->http_client->request($this->url, array($this, 'smsCallback'), $sms);
        }
    }
    
    public function smsCallback($params) {
        if($params['status']['statusCode'] == 200){
            $result = json_decode($params['response'], true);
            if ($result['status'] == 1) {
                $this->finishSend($params['back_data']['id']);
            }else {
                log_message('LOG', $params['back_data']['id']."-".$params['back_data']['phone']."-".$params['response'], 'sms/error');
                $this->errorSend($params['back_data']['id']);
            }
        }elseif($params['status']['statusCode'] < 0){
            log_message('LOG', $params['back_data']['id']."-".$params['back_data']['phone']."-".$params['status']['statusCode'], 'sms/error');
            $this->errorSend($params['back_data']['id']);
        }
        else{
            log_message('LOG', print_r($params, true), 'sms');
        }
    }
    
    private function finishSend($id) {
        $upparams = array(
            'db' => 'DB',
            'sql' => "update cp_sms_logs set status = '1' where id = ?",
            'data' => array('id' => $id)
        );
        $this->back_update($upparams);
    }
    
    private function errorSend($id) {
        $upparams = array(
            'db' => 'DB',
            'sql' => "update cp_sms_logs set status = '2' where id = ?",
            'data' => array('id' => $id)
        );
        $this->back_update($upparams);
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