<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 购彩推送
 * @date:2017-06-25
 */

class Cli_Lottery_Push extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('mipush_model');
        $this->load->library('mipush');
    }

    // 主函数
    public function index()
    {
        // 扫描明天需要执行的数据
        $week = date('w', strtotime("+1 day"));
        if(in_array($week, array(0, 2, 4)))
        {
            $lid = '51';
        }
        else
        {
            $lid = '23529';
        }
        $info = $this->mipush_model->getPushConfig($lid, $week);
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                $this->handlePushData($items);
                // 更新时间
                $this->mipush_model->updateSendTime($items['id']);
            }
        }

        // push_log表定期删除七天前数据
        $this->deletePushLog();
    }

    public function handlePushData($data)
    {
        $day = date('Y-m-d', strtotime("+1 day"));
        $startTime = date('Y-m-d H:i:s',strtotime($day));

        // 00:00 - 19:30
        for ($i=0; $i < 40; $i++) 
        { 
            $next = strtotime($startTime) + $i * 30 * 60;
            $nextTime = date('Y-m-d H:i:s',$next);
            // 主题标识
            $hitTime = date('Hi',$next);
            // 推送消息
            $pushData = array(
                'type'         =>  'lid_bet', 
                'topic'        =>  $data['lid'] . '_bet_' . $hitTime,
                'lid'          =>  $data['lid'],
                'title'        =>  $data['title'],
                'description'  =>  $data['content'],
                'time_to_send' =>  $nextTime,
            );
            $this->mipush->index('topic', $pushData);
        }
    }

    public function deletePushLog()
    {
        $this->mipush_model->deletePushLog();
    }
}