<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Jjcscore_Sync extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "jjcscoreSync");
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
            'fld' => $linfo['fld'], //需要拉取的数据
            'ups' => $linfo['ups'],
            'cdt' => "m.{$linfo['cdt']} > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.{$linfo['table']}",
            'bit' => 1,
        );
        $this->syncr_model->syncr_start($cfg);
        if(in_array($lid, array('42', '43')))
        {
        	//触发过关任务
        	$this->task_model->updateStop(2, $lid, 0);
        }
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'jjcscoreSync');
    }
    
    private function getLidInfo($lid)
    {
    	$data = array(
    		'42' => array(
    			'table' => 'cp_jczq_paiqi',
    			'fld' => array('id', 'mid', 'mname', 'rq', 'half_score', 'full_score', 'm_status', 'status', 'aduitflag'),
    			'ups' => array('rq', 'half_score', 'full_score', 'm_status', 'status', 'aduitflag'),
    			'cdt' => 'modified',
    		),
    		'43' => array(
    			'table' => 'cp_jclq_paiqi',
    			'fld' => array('id', 'mid', 'mname', 'rq', 'preScore', 'full_score', 'm_status', 'status', 'aduitflag'),
    			'ups' => array('rq', 'preScore', 'full_score', 'm_status', 'status', 'aduitflag'),
    			'cdt' => 'modified',
    		),
    		//老足彩排期表信息同步配置  lid为虚构
    		'10' => array(
    			'table' => 'cp_tczq_paiqi',
    			'fld' => array('id', 'mid', 'mname', 'ctype', 'half_score', 'full_score', 'result1', 'result2', 'status'),
    			'ups' => array('half_score', 'full_score', 'result1', 'result2', 'status'),
    			'cdt' => 'modified',
    		),
    		'11' => array(
    			'table' => 'cp_rsfc_paiqi',
    			'fld' => array('id','mid','result','status','rjstatus','rj_sale','sfc_sale','award','award_detail','rstatus','rjrstatus'),
    			'ups' => array('result','status','rjstatus','rj_sale','sfc_sale','award','award_detail','rstatus','rjrstatus'),
    			'cdt' => 'created',
    		),
    		'16' => array(
    			'table' => 'cp_rbqc_paiqi',
    			'fld' => array('id', 'mid', 'result', 'status', 'sale', 'award', 'award_detail', 'rstatus'),
    			'ups' => array('result', 'status', 'sale', 'award', 'award_detail', 'rstatus'),
    			'cdt' => 'created',
    		),
    		'18' => array(
    			'table' => 'cp_rjqc_paiqi',
    			'fld' => array('id', 'mid', 'result', 'status', 'sale', 'award', 'award_detail', 'rstatus'),
    			'ups' => array('result', 'status', 'sale', 'award', 'award_detail', 'rstatus'),
    			'cdt' => 'created',
    		),
    		'41' => array(
    			'table' => 'cp_bjdc_paiqi',
    			'fld' => array('id','mid','mname','half_score','full_score','status','rq','spf_odds','jqs_odds','bqc_odds','dss_odds','dcbf_odds','xbcbf_odds','state'),
    			'ups' => array('half_score','full_score','status','rq','spf_odds','jqs_odds','bqc_odds','dss_odds','dcbf_odds','xbcbf_odds','state'),
    			'cdt' => 'modified',
    		),
    		'40' => array(
    			'table' => 'cp_sfgg_paiqi',
    			'fld' => array('id', 'mid', 'mname', 'rq', 'sfgg_odds', 'half_score', 'full_score', 'status', 'state'),
    			'ups' => array('rq', 'sfgg_odds', 'half_score', 'full_score', 'status', 'state'),
    			'cdt' => 'modified',
    		),
    	);
    	
    	return $data[$lid];
    }
}