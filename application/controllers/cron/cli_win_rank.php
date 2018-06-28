<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 中奖排行榜
 * @date:2018-04-16
 */

class Cli_Win_Rank extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('rank_model');
    }

    // 统计用户总量
    private $limitType = array(
        '1' =>  2000,
        '2' =>  1000,
        '3' =>  3000,
    );

    // 统计用户
    public function getRankUser()
    {
        $config = $this->rank_model->getConfigData();
        if(!empty($config))
        {
            // 当前注册用户量
            $counts = $this->rank_model->getAllUser();
            foreach ($config as $items) 
            {
                $this->rank_model->calConfigUser($items, $this->limitType, $counts);
            }
        }
    }

    // 派奖
    public function getPrizeUser()
    {
        $config = $this->rank_model->getConfigPrize();
        if(!empty($config))
        {
            foreach ($config as $items) 
            {
                $this->rank_model->sendPrizeUser($items);
            }
        }
    }

    // 短信、推送
    public function getSmsUser()
    {
        $config = $this->rank_model->getPrizedConfig();
        if(!empty($config))
        {
            foreach ($config as $items) 
            {
                $this->rank_model->sendSmsUser($items);
            }
        }
    }
}