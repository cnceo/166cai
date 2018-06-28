<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . '/core/Task_Controller.php';
class Cli_Jjcpaiqi_Sync extends Task_Controller
{
	private $startTime;
    public function __construct()
    {
    	$this->startTime = microtime(true);
        parent::__construct();
        register_shutdown_function(array($this->tlog, "fatalErrorHandler"), "jjcpaiqiSync");
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
    		'fld' => $linfo['fld'],
    		'ups' => $linfo['ups'],
    		'cdt' => "m.{$linfo['cdt']} > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'synflag',
    		'ftb' => "{$this->db_config['dc']}.{$linfo['table']}",
    		'bit' => 1,
    	);
    	$this->syncr_model->syncr_start($cfg);
        //添加设置show_end_time操作
        if(in_array($lid, array(10, 41, 42, 43)))
        {
        	//触发过关任务
        	$this->load->model('task_model');
    		$this->task_model->updateStop(7, $lid, 0);
        }
    	//回收当前任务
    	$this->stopCurrentTask($taskId);
    	$message = "任务ID：{$taskId} | 彩种Lid：{$lid}执行完成。";
    	$this->tlog->infoHandler($this->startTime, $message, 'jjcpaiqiSync');
    }
    
    private function getLidInfo($lid)
    {
    	$data = array(
    		'42' => array(
    			'table' => 'cp_jczq_paiqi',
    			'fld' => array('id','mid','mname','m_date','league','home','away','end_sale_time','m_status', 'cstate', 'created'),
    			'ups' => array('m_date', 'league', 'home', 'away', 'end_sale_time', 'm_status', 'cstate', 'created'),
    			'cdt' => 'modified',
    		),
    		'43' => array(
    			'table' => 'cp_jclq_paiqi',
    			'fld' => array('id','mid','mname','m_date','league','home','away','begin_time','m_status','preScore','cstate','created'),
    			'ups' => array('m_date', 'league', 'home', 'away', 'begin_time', 'm_status', 'preScore', 'cstate', 'created'),
    			'cdt' => 'modified',
    		),
    		//老足彩排期表信息同步配置  lid为虚构
    		'10' => array(
    			'table' => 'cp_tczq_paiqi',
    			'fld' => array('id','mid','mname','ctype','league','home','away','start_sale_time','end_sale_time','begin_date','eur_odd_win','eur_odd_deuce','eur_odd_loss','created'),
    			'ups' => array('ctype','league','home','away','start_sale_time','end_sale_time','begin_date','eur_odd_win','eur_odd_deuce','eur_odd_loss','created'),
    			'cdt' => 'modified',
    		),
    		'11' => array(
    			'table' => 'cp_rsfc_paiqi',
    			'fld' => array('id', 'mid', 'created'),
    			'ups' => array('created'),
    			'cdt' => 'created',
    		),
    		'16' => array(
    			'table' => 'cp_rbqc_paiqi',
    			'fld' => array('id', 'mid', 'created'),
    			'ups' => array('created'),
    			'cdt' => 'created',
    		),
    		'18' => array(
    			'table' => 'cp_rjqc_paiqi',
    			'fld' => array('id', 'mid', 'created'),
    			'ups' => array('created'),
    			'cdt' => 'created',
    		),
    		'41' => array(
    			'table' => 'cp_bjdc_paiqi',
    			'fld' => array('id','mid','mname','m_date','game_type','league','home','away','begin_time','created'),
    			'ups' => array('m_date', 'game_type', 'league', 'home', 'away', 'begin_time', 'created'),
    			'cdt' => 'modified',
    		),
    		'40' => array(
    			'table' => 'cp_sfgg_paiqi',
    			'fld' => array('id','mid','mname','m_date','game_type','league','home','away','begin_time','created'),
    			'ups' => array('m_date', 'game_type', 'league', 'home', 'away', 'begin_time', 'created'),
    			'cdt' => 'modified',
    		),
    	);
    	
    	return $data[$lid];
    }
}