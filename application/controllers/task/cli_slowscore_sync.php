<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Slowscore_Sync extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "slowscoreSync");
        $this->load->model('syncr_model');
        $this->load->model('task_model');
        $this->load->model('cache_model');
        $this->load->model('lottery_model', 'Lottery');
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
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus', 'aduitflag'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus', 'aduitflag'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.{$linfo['table']}",
            'bit' => 1,
        );
        $this->syncr_model->syncr_start($cfg);
        
        //开奖内存刷新
        $this->Lottery->frushKjHistory($lid);
        //慢 array('ssq', 'dlt', 'qlc', 'qxc', 'pl3', 'pl5', 'fc3d');
        if( in_array( $lid, array('51','23529','23528','10022','33','35','52') ) )
        {
            $this->task_model->updateStop(8, $lid, 0);
        }
        //触发过关任务
        $this->task_model->updateStop(2, $lid, 0);
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
    	$this->cache_model->refreshByLid($lid);  //刷新投注缓存
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'slowscoreSync');
    }
    
    private function getLidInfo($lid)
    {
    	$data = array(
    		'51' => array('table' => 'cp_ssq_paiqi'),
    		'23529' => array('table' => 'cp_dlt_paiqi'),
    		'10022' => array('table' => 'cp_qxc_paiqi'),
    		'23528' => array('table' => 'cp_qlc_paiqi'),
    		'52' => array('table' => 'cp_fc3d_paiqi'),
    		'33' => array('table' => 'cp_pl3_paiqi'),
    		'35' => array('table' => 'cp_pl5_paiqi'),
    	);
    	
    	return $data[$lid];
    }
}