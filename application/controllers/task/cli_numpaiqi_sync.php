<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Numpaiqi_Sync extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "numpaiqiSync");
        $this->load->model('syncr_model');
        $this->load->model('task_model');
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
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'), //需要拉取的数据
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
    		'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.{$linfo['table']}",
            'bit' => 1,
        );
        $this->syncr_model->syncr_start($cfg);
     	//添加设置show_end_time操作
        $this->load->model('task_model');
    	$this->task_model->updateStop(7, $lid, 0);
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'numpaiqiSync');
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
    		'53' => array('table' => 'cp_ks_paiqi'),
            '56' => array('table' => 'cp_jlks_paiqi'),
    	    '57' => array('table' => 'cp_jxks_paiqi'),
    		'21406' => array('table' => 'cp_syxw_paiqi'),
    		'21407' => array('table' => 'cp_jxsyxw_paiqi'),
    		'21408' => array('table' => 'cp_hbsyxw_paiqi'),
            '54' => array('table' => 'cp_klpk_paiqi'),
            '55' => array('table' => 'cp_cqssc_paiqi'),
    	    '21421' => array('table' => 'cp_gdsyxw_paiqi'),
    	);
    	
    	return $data[$lid];
    }
}