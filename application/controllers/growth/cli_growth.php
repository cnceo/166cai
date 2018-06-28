<?php

/**
 * 每天执行一次
 * @date:2017-12-29
 */

class Cli_Growth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('growth_cli_model');
    }

    /**
     * 保级降级操作(一天执行一次)
     */
    public function downgrade()
    {
        $date = date('Y-m-d');
        $this->growth_cli_model->downgrade($date);
    }
    
    /**
     * 生日礼包派发  (同一天不能多次执行，多次执行派发多次)
     */
    public function sendBirth($date = '')
    {
        $date = empty($date) ? date('m-d') : $date;
        $this->growth_cli_model->sendBirth($date);
    }
    
    /**
     * 重置红包今日兑换数量(每天0点整执行)
     */
    public function resetRedpackStock()
    {
        $this->growth_cli_model->resetRedpackStock();
    }
    
    /**
     * 上一年度积分转存(每年1月1日0时执行)
     */
    public function pointTransfer()
    {
        $this->growth_cli_model->pointTransfer();
    }
    
    /**
     * 清除上一年度积分(每年3月1日0时执行)
     */
    public function pointEmpty()
    {
        $this->growth_cli_model->pointEmpty();
    }
    
    /**
     * 任务执行失败的记录重新入队操作(每分钟执行1次)
     */
    public function taskRetry()
    {
        $this->load->library('stomp');
        $config = $this->config->item('stomp');
        $connect = $this->stomp->connect();
        $datas = $this->growth_cli_model->getTaskRetry();
        while ($datas && $connect)
        {
            $ids = array();
            foreach ($datas as $data)
            {
                $res = $this->stomp->push($config['queueName'], json_decode($data['data'], true), array('persistent'=>'true'));
                if($res)
                {
                    $ids[] = $data['id'];
                }
            }
            
            if($ids)
            {
                $result = $this->growth_cli_model->delTaskRetry($ids);
                if(!$result)
                {
                    $this->stomp->disconnect();
                    return ;
                }
            }
            
            $datas = $this->growth_cli_model->getTaskRetry();
        }
        
        $this->stomp->disconnect();
    }
}