<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );
/*
 * 拉取票商中奖明细
 * @date:2017-11-20
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Ticketbonus extends Task_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('ticket_model');
        $this->load->model('task_model');
        $this->load->config('order');
        $this->sellers = $this->ticket_model->getSeller();
        if(!empty($this->sellers))
        {
            foreach ($this->sellers as $seller)
            {
                $this->load->library("ticket_{$seller['name']}");
            }
        }
    }

    public function index($taskId, $lid)
    {
        $sellerCount = 0;
        foreach ($this->sellers as $seller)
        {
            $lseller = "ticket_{$seller['name']}";
            $sellerRes = $this->$lseller->med_getTicketBonus($lid);
            if($sellerRes)
            {
                $sellerCount ++;
            }
        }

        // 胜负彩任九暂不走触发逻辑 常驻查询
        if(!in_array($lid, array('11', '19', '44', '45')))
        {
            // 未空跑拉取即触发奖金比对任务
            if($sellerCount > 0)
            {
                $this->task_model->updateStop(5, $lid, 0); 
            }
            
            // 检查是否处理完毕
            $count = $this->ticket_model->countTicketBonusByLid($lid);
            if($count == 0)
            {
                // 回收当前任务
                $this->stopCurrentTask($taskId);
                $message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
                $this->tlog->infoHandler($this->startTime, $message, 'ticketbonus');
            }
        } 
    }
}