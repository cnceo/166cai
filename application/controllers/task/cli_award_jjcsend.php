<?php
/**
 * 竞技彩派奖操作
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Jjcsend extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardJjcsend");
        $this->load->model('task_model');
        $this->load->model('backaward_model');
    }

    public function index($taskId, $lid)
    {
    	$stopFlag = true;
    	$res = $this->backaward_model->jjcCompareBonus($lid);
    	if(!$res)
    	{
    		$stopFlag = false;
    	}
    	
    	//触发奖金拉取任务
        $this->task_model->updateStop(10, $lid, 0);
    	//当前任务操作
    	$this->stopCurrentTask($taskId, $stopFlag);
    	
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	if(!$stopFlag)
    	{
    		$message .= "有条件不满足，需要下次继续执行。";
    	}
    	$this->tlog->infoHandler($this->startTime, $message, 'awardJjcsend');
    }
}