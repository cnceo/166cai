<?php
/**
 * 吉林快三获取期次比对更新
 */
require_once APPPATH . '/core/Task_Controller.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Get_New_Issue extends Task_Controller
{
    private $startTime;
    public function __construct()
    {
        $this->startTime = microtime(true);
        parent::__construct();
    }
    
    private $_ctypeArr = array(
        '56' => 'jlks_issue',
        '57' => 'jxks_issue',
    );

    public function index($taskId, $lid)
    {
        $this->load->model('task_model');
        $issueTime = $this->task_model->getJLKSTaskStatus($lid);
        if (!$issueTime[0]) {
            $config = $this->task_model->getJLKSIssueStatus($this->_ctypeArr[$lid]);
            $this->load->library('ticket_'.$config['lname']);
            $seller='ticket_'.$config['lname'];
            $this->$seller->met_getIssue($lid);
            //回收当前任务
            $this->stopCurrentTask($taskId);
            $message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
            $this->tlog->infoHandler($this->startTime, $message, 'getnewissue');
        }
    }
}