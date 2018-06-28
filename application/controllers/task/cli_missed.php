<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 排列三  遗漏统计
 * 作    者: 李康建
 * 修改日期: 2017/05/18
 * 修改时间: 09:33
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Missed extends Task_Controller
{
    private $startTime;
    public function __construct()
    {
        $this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "missed");
        $this->load->model('task_model');
        $this->types = array(
            '51' => 'Ssq',
            '23529' => 'Dlt',
            '23528' => 'Qlc',
            '10022' => 'Qxc',
            '33' => 'Pl3',
            '35' => 'Pl5',
            '52' => 'Fc3d',
            '21406' => 'Syxw',
            '21407' => 'Jxsyxw',
            '21408' => 'Hbsyxw',
            '53' => 'Ks',
            '54' => 'Klpk',
            '55' => 'Cqssc',
            '56' => 'Jlks',
            '57' => 'Jxks',
            '21421' => 'Gdsyxw',
        );
    }

    public function index($taskId, $lid)
    {
        $lEname = $this->types[$lid];
        $library = "missed/{$lEname}";
        $this->load->library($library);
        $class = strtolower($lEname);
        //遗漏统计
        $result = $this->$class->exec(); 
        //回收当前任务
        $this->stopCurrentTask($taskId, 1);
        $message = "任务ID：{$taskId} | 彩种Lid遗漏：{$lid}执行完成。";
        $this->tlog->infoHandler($this->startTime, $message, 'missed');
    }


}