<?php
/**
 * 过关子任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Quick_Awardnumber extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardnumber");
        $this->load->model('task_model');
        $this->lids = $this->task_model->orderConfig('lidmap');
    }

    public function index($taskId, $lid)
    {
        $ctype = $this->lids[$lid];
        $stopFlag = true;
        $source = $this->task_model->getCronScore($ctype);
        if($source)
        {
            $this->load->library("awardnumber/{$source['lname']}");
            $stopFlag = $this->$source['lname']->capture($lid);
        }
        
        //当前任务操作
        $this->stopCurrentTask($taskId, $stopFlag);
        $message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
        if(!$stopFlag)
        {
            $message .= "有条件不满足，需要下次继续执行。";
        }
        $this->tlog->infoHandler($this->startTime, $message, 'awardnumber');
    }
}