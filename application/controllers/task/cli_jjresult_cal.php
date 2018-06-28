<?php

/**
 * 计算比赛中奖信息
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';

class Cli_Jjresult_Cal extends Task_Controller {

    private $startTime;

    public function __construct() {
        $this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "jjresultsync");
        $this->load->model('task_model');
    }

    public function index($taskId, $lid) {
        $this->load->model('jcmatch_model');
        $this->jcmatch_model->calResult($lid);
        //回收当前任务
        $this->stopCurrentTask($taskId);
        $message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
        $this->tlog->infoHandler($this->startTime, $message, 'awardBonus');
    }

}
