<?php

class testDb extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkPermit();
    }

    private function checkPermit()
    {
        $uid = $this->input->get('u', TRUE);
        $this->load->model('user_model');
        $uInfo = $this->user_model->getUserInfo($uid);
        if (empty($uInfo))
        {
            die('permition limited!!');
        }
    }

    public function index()
    {
        $this->monitorDbSync();
        $this->monitorRedis();
    }

    private function monitorDb($dbName)
    {
        $DDB = $this->load->database($dbName, TRUE);
        $address = $this->filterIP($DDB->hostname);
        $row = $DDB->query("show slave status")->getRow();
        //判断主服务器同步到从服务器的同步时间
        if ($row['Seconds_Behind_Master'] > 300)
        {
            $this->send404("$address($dbName):从服务器同步数据时间大于60秒" . $row['Seconds_Behind_Master']);
        }
        elseif ($row["Slave_IO_Running"] != "Yes")
        {
            $this->send404("$address($dbName):Slave_IO_Running值不为Yes, 为：" . $row["Slave_IO_Running"] . "__");
        }
        elseif ($row["Slave_SQL_Running"] != "Yes")
        {
            $this->send404("$address($dbName):Slave_SQL_Running值不为Yes, 为：" . $row["Slave_SQL_Running"] . "__");
        }
    }

    private function filterIP($hostName)
    {
        $address = '';
        if (preg_match('/(\d+\.\d+\.\d+\.\d+).*/', $hostName, $matches))
        {
            $address = $matches[1];
        }

        return $address;
    }

    //监控主从服务同步情况
    private function monitorDbSync()
    {
        $this->monitorDb('tdb');
        $this->monitorDb('tcdb');
    }

    private function monitorRedis()
    {
        $this->config->load('redis');
        $redisCfg = $this->config->item('slave');
        $redis = new Redis();
        $success = $redis->connect($redisCfg['host'], $redisCfg['port'], 3);
        if ( ! $success)
        {
            $this->send404($redisCfg['host'] . ":Redis链接不成功！");
        }
    }

    private function send404($msg)
    {
        header("HTTP/1.1 404 Not Found");
        die(iconv('utf-8', 'gbk', $msg));
    }
}
