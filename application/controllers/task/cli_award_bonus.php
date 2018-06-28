<?php
/**
 * 过关子任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Bonus extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardBonus");
        $this->load->model('task_model');
        $this->lids = $this->task_model->orderConfig('lidmap');
    }

    public function index($taskId, $lid)
    {
    	$lEname = $this->lids[$lid];
    	$library = "award/{$lEname}";
    	$this->load->library($library);
    	$result = $this->$lEname->bonus($lid); //过关
    	if($result['triggerFlag'])
    	{
    		$this->task_model->updateStop(4, $lid, 0);
    	}
    	//回收当前任务
    	$this->stopCurrentTask($taskId, $result['currentFlag']);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'awardBonus');
    }
}