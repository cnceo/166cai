<?php

/**
 * 定制跟单自动跟单脚本
 * @date:2017-08-18
 */

class Cli_United_Follow_Bet extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('follow_order_model');
        $this->load->model('follow_wallet_model');
    }

    public function index()
    {
        $contine = true;
        while($contine)
        {
            $this->controlRestart($this->con);
            // 便于测试环境的调试
            if(ENVIRONMENT === 'development')
            {
                $this->runCron();
            }
            else
            {
                $croname = "united/cli_united_follow_bet runCron";
                system("{$this->php_path} {$this->cmd_path} $croname",  $status);
            }
            sleep(1);
        }
    }

    public function runCron()
    {
        $counts = $this->follow_wallet_model->getFollowTask();
        if($counts > 0)
        {
            // 上限 500
            $counts = ($counts >= 500) ? 500 : $counts;
            for ( $i = 0; $i <= $counts; $i++) 
            { 
                $followData = $this->follow_wallet_model->popFollowTask();
                if(!empty($followData))
                {
                    $this->handleFollowOrder($followData);
                }
            }
        }
    }

    // 单笔合买发起方案处理跟单
    public function handleFollowOrder($unitedData)
    {
        // 检查符合条件的方案 根据生效时间
        $orders = $this->follow_order_model->getFollowOrders($unitedData['uid'], $unitedData['lid']);

        $handleFlag = FALSE;
        if(!empty($orders))
        {
            foreach ($orders as $order) 
            {
                $handleRes = $this->follow_wallet_model->handleFollowBet($unitedData, $order);
                // 合买订单状态不满足退出
                if($handleRes['code'] == 500)
                {
                    break;
                }
            }
        }

        // 更新合买订单状态 已处理跟单
        $this->follow_order_model->updateUnitedFollowed($unitedData);
    }
}