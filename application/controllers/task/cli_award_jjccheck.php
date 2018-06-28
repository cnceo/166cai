<?php
/**
 * 竞技彩奖金比对任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Jjccheck extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardJjccheck");
        $this->load->model('task_model');
        $this->load->model('backaward_model');
    }

    public function index($taskId, $lid)
    {
    	$stopFlag = $this->backaward_model->jjcBonusCheck($lid);
    	//当前任务操作
    	$this->stopCurrentTask($taskId, $stopFlag);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	if(!$stopFlag)
    	{
    		$message .= "有条件不满足，需要下次继续执行。";
    	}
    	$this->tlog->infoHandler($this->startTime, $message, 'awardJjccheck');
    }
}