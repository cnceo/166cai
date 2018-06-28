<?php
/**
 * 过关子任务
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Lottery_Config extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardBonus");
        $this->load->model('lottery_config_model', 'lotteryConfig');
		$this->load->model('lottery_cache_model', 'LotteryCache');
        $this->load->model('cache_model');
    }

    public function index($taskId, $lid)
    {
        $this->lotteryConfig->deliveryConfigItems(array(), $lid);
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
        if(in_array($lid, array('21406', '21407', '21408', '53', '54', '55', '56', '57', '21421')))
        {
            $this->cache_model->refreshFastLottery($lid); //刷新投注缓存
        }
        else
        {
            $this->cache_model->refreshByLid($lid); //刷新投注缓存
        }
        if (in_array($lid, array(SSQ, DLT, FCSD, PLS, PLW, QXC, QLC, SYXW, JXSYXW, HBSYXW, KS, JLKS, JXKS, KLPK, CQSSC, GDSYXW))) {
        	$this->LotteryCache->getCombineIssue($lid);
        }
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'lotteryConfig');
    }
}