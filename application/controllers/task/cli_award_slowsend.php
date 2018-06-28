<?php
/**
 * 慢频彩不比对直接派奖操作
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Slowsend extends Task_Controller
{
	private $startTime;
	private $lnames = array(
	    '51' => array('name'=> 'ssq', 'issueFormt' => 0),
	    '23529' => array('name'=> 'dlt', 'issueFormt' => 2),
	    '10022' => array('name'=> 'qxc', 'issueFormt' => 2),
	    '23528' => array('name'=> 'qlc', 'issueFormt' => 0),
	    '52' => array('name'=> 'fc3d', 'issueFormt' => 0),
	    '33' => array('name'=> 'pl3', 'issueFormt' => 2),
	    '35' => array('name'=> 'pl5', 'issueFormt' => 2),
	);
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardSlowsend");
        $this->load->model('task_model');
        $this->load->model('backaward_model');
        $this->order_status = $this->task_model->orderConfig('orders');
        $this->load->library('libcomm');
    }

    public function index($taskId, $lid)
    {
    	$stopFlag = true;
    	$lname = $this->lnames[$lid]['name'];
    	$issues = $this->backaward_model->getIssues($lname, $this->order_status['paiqi_jjsucc']);
    	$checkTaskFlag = false;
    	foreach ($issues as $issue)
    	{
    	    $oIssue = $this->libcomm->format_issue($issue, 1, $this->lnames[$lid]['issueFormt']);
    	    $res = $this->backaward_model->slowCompareBonus($lid, $oIssue);
    		if(!$res)
    		{
    			$stopFlag = false;
    		}
    		$aRes = $this->backaward_model->award_slow_number($lname, $lid, $issue, $oIssue);
    		if($aRes)
    		{
    		    $checkTaskFlag = true;
    		}
    	}
    	
    	$issues = $this->backaward_model->getIssues($lname, $this->order_status['paiqi_awarding']);
    	foreach ($issues as $issue)
    	{
    	    $oIssue = $this->libcomm->format_issue($issue, 1, $this->lnames[$lid]['issueFormt']);
    	    $res = $this->backaward_model->period_slow_number($lname, $lid, $issue, $oIssue);
    		if(!$res)
    		{
    			$stopFlag = false;
    		}
    	}
    	
    	if($checkTaskFlag)
    	{
    	    //触发奖金拉取任务
            $this->task_model->updateStop(10, $lid, 0);
    	}
    	
    	//当前任务操作
    	$this->stopCurrentTask($taskId, $stopFlag);
    	
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	if(!$stopFlag)
    	{
    		$message .= "有条件不满足，需要下次继续执行。";
    	}
    	$this->tlog->infoHandler($this->startTime, $message, 'awardSlowsend');
    }
}