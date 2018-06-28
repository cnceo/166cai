<?php
/**
 * 快频彩奖金比对任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Quickcheck extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardQuickcheck");
        $this->load->model('task_model');
        $this->load->model('backaward_model');
        $this->lids = $this->task_model->orderConfig('lidmap');
        $this->order_status = $this->task_model->orderConfig('orders');
    }

    public function index($taskId, $lid)
    {
    	$stopFlag = $this->backaward_model->quickBonusCheck($lid);
    	//当前任务操作
    	$this->stopCurrentTask($taskId, $stopFlag);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	if(!$stopFlag)
    	{
    		$message .= "有条件不满足，需要下次继续执行。";
    	}
    	$this->tlog->infoHandler($this->startTime, $message, 'awardQuickcheck');
    }
}