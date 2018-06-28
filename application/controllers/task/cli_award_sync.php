<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Award_Sync extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "awardSync");
        $this->load->model('syncr_model');
        $this->load->model('task_model');
        $this->load->model('lottery_model', 'Lottery');
        $this->load->model('cache_model');
    }

    public function index($taskId, $lid)
    {
    	$webInstance = $this->webInstance();
    	$linfo = $this->getLidInfo($lid);
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.{$linfo['table']} m",
    		'dtb' => "{$this->db_config['cfg']}.{$linfo['table']}",
    		'fld' => array('id', 'issue','awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'delect_flag', 'rstatus'),
    		'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'delect_flag', 'rstatus'), //需要拉取的数据
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'd_synflag',
    		'ftb' => "{$this->db_config['dc']}.{$linfo['table']}",
    		'bit' => 1, //位运算 -- 按位运算
    	);
    	$this->syncr_model->syncr_start($cfg);
    	//刷新开奖号码
    	$this->Lottery->$linfo['awardName']();
        //快频
        if( in_array( $lid, array('21406','21407','21408','53','54', '55','56', '57', '21421') ) )
        {
            //触发刷新追号期次
            $this->task_model->updateStop(7, $lid, 0);
            //触发遗漏计算 
            $this->task_model->updateStop(8, $lid, 0);
        }
     //    $missConfig = array(
     //        '53' => 'Ks',
     //        '54' => 'Klpk'
     //    );
    	// if(!empty($missConfig[$lid]))
    	// {
     //        $type = strtolower($missConfig[$lid]);
    	// 	//针对快三特殊处理  刷新遗漏数据
    	// 	$this->load->library("missed/{$missConfig[$lid]}");
    	// 	$this->$type->exec();
    	// }
    	//触发过关任务
    	$this->task_model->updateStop(2, $lid, 0);
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
    	$this->cache_model->refreshFastLottery($lid); //刷新投注缓存
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'awardSync');
    }
    
    private function getLidInfo($lid)
    {
    	$data = array(
    		'53' => array('table' => 'cp_ks_paiqi', 'awardName' => 'refreshKsAwards'),
    		'21406' => array('table' => 'cp_syxw_paiqi', 'awardName' => 'refreshSyxwAwards'),
    		'21407' => array('table' => 'cp_jxsyxw_paiqi', 'awardName' => 'refreshJxSyxwAwards'),
    		'21408' => array('table' => 'cp_hbsyxw_paiqi', 'awardName' => 'refreshHbSyxwAwards'),
            '54' => array('table' => 'cp_klpk_paiqi', 'awardName' => 'refreshKlpkAwards'),
            '55' => array('table' => 'cp_cqssc_paiqi', 'awardName' => 'refreshCqsscAwards'),
            '56' => array('table' => 'cp_jlks_paiqi', 'awardName' => 'refreshJlksAwards'),
    	    '57' => array('table' => 'cp_jxks_paiqi', 'awardName' => 'refreshJxksAwards'),
    	    '21421' => array('table' => 'cp_gdsyxw_paiqi', 'awardName' => 'refreshGdSyxwAwards'),
    	);
    	
    	return $data[$lid];
    }
}