<?php
/**
 * 过关子任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Calculate extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardCalculate");
        $this->load->model('task_model');
        $this->lids = $this->task_model->orderConfig('lidmap');
    }

    public function index($taskId, $lid)
    {
    	$lEname = $this->lids[$lid];
    	$library = "award/{$lEname}";
    	$this->load->library($library);
    	$result = $this->$lEname->calculate($lid); //过关
    	if($result['triggerFlag'])
    	{
    		$this->task_model->updateStop(3, $lid, 0);
    		if(in_array($lid, array('42', '43')))
    		{
    		    //计算比赛结果
    		    $this->task_model->updateStop(8, $lid, 0);
    		}
    	}
    	//回收当前任务
    	$this->stopCurrentTask($taskId, $result['currentFlag']);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'awardCalculate');
    }
}