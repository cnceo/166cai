<?php

class Test extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('DisOrder');
        $this->load->library('tools');
        $this->load->library('libcomm');
        $this->load->model('ticket_model');
    }

    public function restart($pwd = '', $type = 'pro'){
        if($pwd == 'CaiPiao_reload_process'){
            $types = array('pro' => 'clii_process', 'srv' => 'clii_server_data');
            touch("/opt/case/www.166cai.com/application/logs/plock/restart/{$types[$type]}.php.reload");
            echo 'ok';
        }
    }
    
    public function inTest()
    {
        require_once APPPATH . 'libraries/nusoap/lib/nusoap.php';
        $client = new nusoap_client('http://123.57.147.232:8088/service?wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = false;
        $err = $client->getError();
        if ($err) {
            echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
        }
        
        $msg = '2018062011502508412806#2*1^F20180620018,3-11//F20180620019,0_3-14#1#1#2';
        echo md5('800152' . '16' . '' . $msg . '166caitest');
        $sign = 'cac565b63baee429ac8513d525ca4024';
        $param = array('arg0' => '800152', 'arg1' => '16', 'arg2'=> '', 'arg3'=> $msg, 'arg4' => $sign);
        
        $result = $client->call('BetTicket', $param, '', '', false, true);
        //$result1 = $client->call('BetTicket', $param, '', '', false, true);
        print_r($result1);
        if ($client->fault) {
            echo '<h2>Fault</h2><pre>';
            print_r($result);
            echo '</pre>';
        } else {
            // Check for errors
            $err = $client->getError();
            if ($err) {
                // Display the error
                echo '<h2>Error</h2><pre>' . $err . '</pre>';
            } else {
                // Display the result
                echo '<h2>Result</h2><pre>';
                //echo $result['return'];
                $result = simplexml_load_string($result['return']);
                print_r($result);
                echo '</pre>';
            }
        }
    }

    public function index1()
    {
        $this->load->library('stomp');
        $this->config->load('stomps');
        $this->config = $this->config->item('stomp');
        $connect = $this->stomp->connect();
        if ($connect) {
            for ($i = 1; $i <= 10; $i++) {
                $this->stomp->push($this->config['queueNames']['usertag'], $i);
            }
            echo 'sadsad';
        }
        /*$this->load->library('process/prclib_usertag_center');
        $this->prclib_usertag_center->run();*/
    }
    
    public function dao()
    {
    	/*用户分类定义：
		1. 忠诚用户：最近30日内登录超过15天及以上；
		select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info force INDEX (created)
		where 1 and  created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		group by uid having lnum >= 15
		2. 普通活跃用户：最近30日内登录在2~14天的用户；
		select uid, count(DISTINCT(date(created))) lnum from  bn_cpiao.cp_login_info force INDEX (created)
		where 1 and  created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		group by uid having lnum >= 2 and lnum < 15
		3. 新增用户：注册时间在最近30日内的用户；
		select uid from bn_cpiao.cp_user where created >= DATE_SUB('2017-08-23',INTERVAL 30 day) and created < '2017-08-23'
		4. 不活跃用户：最近登录时间距今为31~60天；
		select uid, last_login_time from bn_cpiao.cp_user where last_login_time >= DATE_SUB('2017-08-23',INTERVAL 60 day)
		and last_login_time < DATE_SUB('2017-08-23',INTERVAL 31 day)
		5. 流失用户：最近登录时间距今超过60天；
		select uid, last_login_time from bn_cpiao.cp_user where last_login_time < DATE_SUB('2017-08-23',INTERVAL 60 day)
		and last_login_time > 0 */
    	set_time_limit(0);
		$this->load->model('tips_model');
		$this->tips_model->getDatas();
    }
    
     public function indexss()
    {
    	$configData = array();
        //执行拉取对账信息
        $this->load->model('other_data_check_model', 'dataModel');
        $configs = $this->dataModel->getCheckConfig();
        foreach ($configs as $config)
        {
        	$configData[$config['id']] = $config;
        	$this->load->library("dataCheck/{$config['lib_name']}");
        	$class = strtolower($config['lib_name']);
	        $this->$class->exec($config); 
        }
        
        //需要重新拉取
        $datas = array();
        //出票重拉查询
        $result = $this->dataModel->getTotalSplitRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        //充值重拉查询
        $result = $this->dataModel->getTotalRechargeRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        //提现重拉查询
        $result = $this->dataModel->getTotalWithdrawRflag();
        foreach ($result as $value)
        {
        	$datas[] = $value;
        }
        
        foreach ($datas as $data)
        {
        	$config = $configData[$data['config_id']];
        	if(empty($config))
        	{
        		continue;
        	}
        	
        	$config['exc_date'] = date('Y-m-d', strtotime($data['date']) - 86400);
        	$this->load->library("dataCheck/{$config['lib_name']}");
        	$class = strtolower($config['lib_name']);
        	$this->$class->exec($config, 1);
        }
    }
    
    
    public function ttt(){
        $REDIS = $this->config->item('REDIS');
        $oldchannels = $this->cache->hGetAll($REDIS['CS_RCG_DISPATCH']);
        var_dump($oldchannels);
        echo '<br>';
        $oldchannels = $this->cache->hGetAll($REDIS['RCG_DISPATCH']);
        var_dump($oldchannels);
        echo '<br>';
        $oldchannels = $this->cache->hGetAll($REDIS['CS_PAY_CONFIG']);
        var_dump($oldchannels);
        echo '<br>';
        $oldchannels = $this->cache->hGetAll($REDIS['PAY_CONFIG']);
        var_dump($oldchannels);
        echo '<br>';
    }
}
