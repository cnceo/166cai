<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property Syncr_Model $syncr_model
 * @property             $multi_process
 * @property             $db_config
 * @property             $cfg_orders
 */
class Cli_Cfg_Syncr_Data extends MY_Controller
{

    private $methods = array(
        //'bjdc_paiqi',
        //'bjdc_score',
        //'sfgg_paiqi',
        //'sfgg_score',
        //'jczq_paiqi',
        //'jczq_score',
        //'jclq_paiqi',
        //'jclq_score',
        //'tczq_paiqi',
        //'tczq_score',
        //'rsfc_paiqi',
        //'rsfc_score',
        //'rbqc_paiqi',
        //'rbqc_score',
        //'rjqc_paiqi',
        //'rjqc_score',
        //'ssq_paiqi',
        //'ssq_score',
        //'dlt_paiqi',
        //'dlt_score',
        //'qxc_paiqi',
        //'qxc_score',
        //'qlc_paiqi',
        //'qlc_score',
        //'fc3d_paiqi',
        //'fc3d_score',
        //'pl3_paiqi',
        //'pl3_score',
        //'pl5_paiqi',
        //'pl5_score',
        //'syxw_paiqi',
        //'syxw_score',
    	//'jxsyxw_paiqi',
    	//'jxsyxw_score',
    	//'ks_paiqi',
    	//'ks_score',
    	//'hbsyxw_paiqi',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('syncr_model');
        $this->cfg_orders = $this->syncr_model->orderConfig('orders');
        $this->multi_process = $this->config->item('multi_process');
    }

    public function index()
    {
        $cName = strtolower(__CLASS__);
        $multi = $this->multi_process[$cName];
        $pLimit = $this->multi_process['process_num_limit'];
        $stop = $this->syncr_model->ctrlRun($cName);
        $threads = array();
        $pNum = 0;
        while ( ! $stop)
        {
            foreach ($this->methods as $method)
            {
                $method = "cfg_$method";
                if ($multi)
                {
                    $pNum ++;
                    $pid = pcntl_fork();
                    $this->syncr_model->cfgDB = $this->load->database('cfg', TRUE);
                    if($pid == -1)
					{
						//进程创建失败 跳出循环
						$pNum --;
						continue;
					}
					else if($pid)
					{
                        $threads[$pid] = $method;
                        if ($pNum >= $pLimit)
                        {
                            $wPid = pcntl_wait($status);
                            if ( ! empty($wPid))
                            {
                                unset($threads[$wPid]);
                                $pNum --;
                            }
                        }
                    }
                    else
                    {
                        if (method_exists($this, $method) && ! in_array($method, $threads))
                        {
                            $this->$method();
                        }
                        die(0);
                    }
                }
                else
                {
                    if (method_exists($this, $method))
                    {
                        $this->$method();
                    }
                }
            }
            if ($multi)
            {
                $this->syncr_model->threadWait($threads, 1);
            }
            $stop = $this->syncr_model->ctrlRun($cName);
            //break;
        }
    }

    private function webInstance()
    {
        $webInstance = $this->config->item('web_instance');

        return empty($webInstance) ? 1 : $webInstance;
    }

    /**
     * 北京单场
     * @uses    cfg_bjdc_paiqi()
     * @date    :2015-04-29
     */
    private function cfg_bjdc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_bjdc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_bjdc_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'm_date',
                'game_type',
                'league',
                'home',
                'away',
                'begin_time',
                'created'
            ),
            'ups' => array('m_date', 'game_type', 'league', 'home', 'away', 'begin_time', 'created'),
            'cdt' => "m.modified > date_sub(now(), interval 1 day) and m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_bjdc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_bjdc_score()
     */
    private function cfg_bjdc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_bjdc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_bjdc_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'half_score',
                'full_score',
                'status',
                'rq',
                'spf_odds',
                'jqs_odds',
                'bqc_odds',
                'dss_odds',
                'dcbf_odds',
                'xbcbf_odds',
                'state'
            ),
            'ups' => array(
                'half_score',
                'full_score',
                'status',
                'rq',
                'spf_odds',
                'jqs_odds',
                'bqc_odds',
                'dss_odds',
                'dcbf_odds',
                'xbcbf_odds',
                'state'
            ),
            'cdt' => "m.modified > date_sub(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_bjdc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);

    }

    /**
     * 胜负过关
     * @date    :2015-04-29
     * @uses    cfg_sfgg_paiqi()
     */
    private function cfg_sfgg_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_sfgg_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_sfgg_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'm_date',
                'game_type',
                'league',
                'home',
                'away',
                'begin_time',
                'created'
            ),
            'ups' => array('m_date', 'game_type', 'league', 'home', 'away', 'begin_time', 'created'),
            'cdt' => "m.modified > date_sub(now(), interval 1 day) and m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_sfgg_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_sfgg_score()
     */
    private function cfg_sfgg_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_sfgg_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_sfgg_paiqi",
            'fld' => array('id', 'mid', 'mname', 'rq', 'sfgg_odds', 'half_score', 'full_score', 'status', 'state'),
            'ups' => array('rq', 'sfgg_odds', 'half_score', 'full_score', 'status', 'state'),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_sfgg_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 竞彩足球
     * @date    :2015-04-30
     * @uses    cfg_jczq_paiqi()
     */
    private function cfg_jczq_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_jczq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_jczq_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'm_date',
                'league',
                'home',
                'away',
                'end_sale_time',
                'm_status',
                'created'
            ),
            'ups' => array('m_date', 'league', 'home', 'away', 'end_sale_time', 'm_status', 'created'),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_jczq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_jczq_score()
     */
    private function cfg_jczq_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_jczq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_jczq_paiqi",
            'fld' => array('id', 'mid', 'mname', 'rq', 'half_score', 'full_score', 'm_status', 'status'), //需要拉取的数据
            'ups' => array('rq', 'half_score', 'full_score', 'm_status', 'status'),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_jczq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 竞彩篮球
     * @date    :2015-04-30
     * @uses    cfg_jclq_paiqi()
     */
    private function cfg_jclq_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_jclq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_jclq_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'm_date',
                'league',
                'home',
                'away',
                'begin_time',
                'm_status',
                'preScore',
                'created'
            ),
            'ups' => array('m_date', 'league', 'home', 'away', 'begin_time', 'm_status', 'preScore', 'created'),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_jclq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_jclq_score()
     */
    private function cfg_jclq_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_jclq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_jclq_paiqi",
            'fld' => array('id', 'mid', 'mname', 'rq', 'preScore', 'full_score', 'm_status', 'status'),
            'ups' => array('rq', 'preScore', 'full_score', 'm_status', 'status'), //需要拉取的数据
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_jclq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 老足彩对阵
     * @date    :2015-04-30
     * @uses    cfg_tczq_paiqi()
     */
    private function cfg_tczq_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_tczq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_tczq_paiqi",
            'fld' => array(
                'id',
                'mid',
                'mname',
                'ctype',
                'league',
                'home',
                'away',
                'start_sale_time',
                'end_sale_time',
                'begin_date',
                'eur_odd_win',
                'eur_odd_deuce',
                'eur_odd_loss',
                'created'
            ),
            'ups' => array(
                'ctype',
                'league',
                'home',
                'away',
                'start_sale_time',
                'end_sale_time',
                'begin_date',
                'eur_odd_win',
                'eur_odd_deuce',
                'eur_odd_loss',
                'created'
            ),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_tczq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 胜负彩赛果同步
     * @date    :2015-04-30
     * @uses    cfg_tczq_score()
     */
    private function cfg_tczq_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_tczq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_tczq_paiqi",
            'fld' => array('id', 'mid', 'mname', 'ctype', 'half_score', 'full_score', 'result1', 'result2', 'status'),
            'ups' => array('half_score', 'full_score', 'result1', 'result2', 'status'),
            'cdt' => "m.modified > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_tczq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 胜负彩
     * @date    :2015-04-30
     * @uses    cfg_rsfc_paiqi()
     */
    private function cfg_rsfc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rsfc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rsfc_paiqi",
            'fld' => array('id', 'mid', 'created'),
            'ups' => array('created'),
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rsfc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    //老足彩重算奖金较慢的原因是设置了同步时间间隔为1分钟
    /**
     * @uses    cfg_rsfc_score()
     */
    private function cfg_rsfc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rsfc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rsfc_paiqi",
            'fld' => array(
                'id',
                'mid',
                'result',
                'status',
                'rjstatus',
                'rj_sale',
                'sfc_sale',
                'award',
                'award_detail',
                'rstatus',
                'rjrstatus'
            ),
            'ups' => array(
                'result',
                'status',
                'rjstatus',
                'rj_sale',
                'sfc_sale',
                'award',
                'award_detail',
                'rstatus',
                'rjrstatus'
            ),
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rsfc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 半全场
     * @date    :2015-04-30
     * @uses    cfg_rbqc_paiqi()
     */
    private function cfg_rbqc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rbqc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rbqc_paiqi",
            'fld' => array('id', 'mid', 'created'),
            'ups' => array('created'),
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rbqc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_rbqc_score()
     */
    private function cfg_rbqc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rbqc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rbqc_paiqi",
            'fld' => array('id', 'mid', 'result', 'status', 'sale', 'award', 'award_detail', 'rstatus'), //需要拉取的数据
            'ups' => array('result', 'status', 'sale', 'award', 'award_detail', 'rstatus'),
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rbqc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 进球彩
     * @date    :2015-04-30
     * @uses    cfg_rjqc_paiqi()
     */
    private function cfg_rjqc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rjqc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rjqc_paiqi",
            'fld' => array('id', 'mid', 'created'),
            'ups' => array('created'),
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rjqc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_rjqc_score()
     */
    private function cfg_rjqc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_rjqc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_rjqc_paiqi",
            'fld' => array('id', 'mid', 'result', 'status', 'sale', 'award', 'award_detail', 'rstatus'), //需要拉取的数据
            'ups' => array('result', 'status', 'sale', 'award', 'award_detail', 'rstatus'), //需要拉取的数据
            'cdt' => "m.created > DATE_SUB(NOW(), INTERVAL 1 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_rjqc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 双色球
     * @date    :2015-04-30
     * @uses    cfg_ssq_paiqi()
     */
    private function cfg_ssq_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_ssq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_ssq_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_ssq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_ssq_score()
     */
    private function cfg_ssq_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_ssq_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_ssq_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_ssq_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 大乐透
     * @date    :2015-04-29
     * @uses    cfg_dlt_paiqi()
     */
    private function cfg_dlt_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_dlt_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_dlt_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > date_sub(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_dlt_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_dlt_score()
     */
    private function cfg_dlt_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_dlt_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_dlt_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_dlt_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 七星彩
     * @date    :2015-04-29
     * @uses    cfg_qxc_paiqi()
     */
    private function cfg_qxc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_qxc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_qxc_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_qxc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_qxc_score()
     */
    private function cfg_qxc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_qxc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_qxc_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_qxc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 七乐彩
     * @date    :2015-04-29
     * @uses    cfg_qlc_paiqi()
     */
    private function cfg_qlc_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_qlc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_qlc_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_qlc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_qlc_score()
     */
    private function cfg_qlc_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_qlc_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_qlc_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_qlc_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 福彩3D
     * @date    :2015-04-29
     * @uses    cfg_fc3d_paiqi()
     */
    private function cfg_fc3d_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_fc3d_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_fc3d_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_fc3d_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_fc3d_score()
     */
    private function cfg_fc3d_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_fc3d_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_fc3d_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_fc3d_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 排列三
     * @date    :2015-04-29
     * @uses    cfg_pl3_paiqi()
     */
    private function cfg_pl3_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_pl3_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_pl3_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_pl3_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_pl3_score()
     */
    private function cfg_pl3_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_pl3_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_pl3_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_pl3_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 排列五
     * @date    :2015-04-29
     * @uses    cfg_pl5_paiqi()
     */
    private function cfg_pl5_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_pl5_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_pl5_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_pl5_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_pl5_score()
     */
    private function cfg_pl5_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_pl5_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_pl5_paiqi",
            'fld' => array('id', 'issue', 'awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'rstatus'), //需要拉取的数据
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_pl5_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * 十一选五
     * @date    :2015-04-29
     * @uses    cfg_syxw_paiqi()
     */
    private function cfg_syxw_paiqi()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_syxw_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_syxw_paiqi",
            'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
            'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'synflag',
            'ftb' => "{$this->db_config['dc']}.cp_syxw_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }

    /**
     * @uses    cfg_syxw_score()
     */
    public function cfg_syxw_score()
    {
        $webInstance = $this->webInstance();
        $cfg = array(
            'sdh' => $this->load->database('dc', TRUE),
            'ddh' => $this->load->database('cfg', TRUE),
            'stb' => "{$this->db_config['dc']}.cp_syxw_paiqi m",
            'dtb' => "{$this->db_config['cfg']}.cp_syxw_paiqi",
            'fld' => array(
                'id',
                'issue',
                'awardNum',
                'status',
                'sale',
                'pool',
                'bonusDetail',
                'delect_flag',
                'rstatus'
            ),
            'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'delect_flag', 'rstatus'), //需要拉取的数据
            'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
            'odr' => 'id',
            'sfg' => 'd_synflag',
            'ftb' => "{$this->db_config['dc']}.cp_syxw_paiqi",
            'bit' => TRUE,
        );
        $this->syncr_model->syncr_start($cfg);
    }
    
    /**
     * 江西十一选五
     * @date    :2015-04-29
     * @uses    cfg_jxsyxw_paiqi()
     */
    private function cfg_jxsyxw_paiqi()
    {
    	$webInstance = $this->webInstance();
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.cp_jxsyxw_paiqi m",
    		'dtb' => "{$this->db_config['cfg']}.cp_jxsyxw_paiqi",
    		'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
    		'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'synflag',
    		'ftb' => "{$this->db_config['dc']}.cp_jxsyxw_paiqi",
    		'bit' => TRUE,
    	);
    	$this->syncr_model->syncr_start($cfg);
    }
    
    /**
     * @uses    cfg_jxsyxw_score()
     */
    public function cfg_jxsyxw_score()
    {
    	$webInstance = $this->webInstance();
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.cp_jxsyxw_paiqi m",
    		'dtb' => "{$this->db_config['cfg']}.cp_jxsyxw_paiqi",
    		'fld' => array(
    			'id',
    			'issue',
    			'awardNum',
    			'status',
    			'sale',
    			'pool',
    			'bonusDetail',
    			'delect_flag',
    			'rstatus'
    		),
    		'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'delect_flag', 'rstatus'), //需要拉取的数据
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'd_synflag',
    		'ftb' => "{$this->db_config['dc']}.cp_jxsyxw_paiqi",
    		'bit' => TRUE,
    	);
    	$this->syncr_model->syncr_start($cfg);
    }
    
    /**
     * 上海快三
     * @date    :2015-04-29
     * @uses    cfg_ks_paiqi()
     */
    private function cfg_ks_paiqi()
    {
    	$webInstance = $this->webInstance();
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.cp_ks_paiqi m",
    		'dtb' => "{$this->db_config['cfg']}.cp_ks_paiqi",
    		'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
    		'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'synflag',
    		'ftb' => "{$this->db_config['dc']}.cp_ks_paiqi",
    		'bit' => TRUE,
    	);
    	$this->syncr_model->syncr_start($cfg);
    }
    
    /**
     * @uses    cfg_ks_score()
     */
    public function cfg_ks_score()
    {
    	$webInstance = $this->webInstance();
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.cp_ks_paiqi m",
    		'dtb' => "{$this->db_config['cfg']}.cp_ks_paiqi",
    		'fld' => array(
    			'id',
    			'issue',
    			'awardNum',
    			'status',
    			'sale',
    			'pool',
    			'bonusDetail',
    			'delect_flag',
    			'rstatus'
    		),
    		'ups' => array('awardNum', 'status', 'sale', 'pool', 'bonusDetail', 'delect_flag', 'rstatus'), //需要拉取的数据
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.d_synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'd_synflag',
    		'ftb' => "{$this->db_config['dc']}.cp_ks_paiqi",
    		'bit' => TRUE,
    	);
    	$this->syncr_model->syncr_start($cfg);
    }
    
    /**
     * 上海快三
     * @date    :2015-04-29
     * @uses    cfg_ks_paiqi()
     */
    private function cfg_hbsyxw_paiqi()
    {
    	$webInstance = $this->webInstance();
    	$cfg = array(
    		'sdh' => $this->load->database('dc', TRUE),
    		'ddh' => $this->load->database('cfg', TRUE),
    		'stb' => "{$this->db_config['dc']}.cp_hbsyxw_paiqi m",
    		'dtb' => "{$this->db_config['cfg']}.cp_hbsyxw_paiqi",
    		'fld' => array('id', 'issue', 'sale_time', 'end_time', 'award_time', 'created'),
    		'ups' => array('sale_time', 'end_time', 'award_time', 'created'),
    		'cdt' => "m.award_time > DATE_SUB(NOW(), INTERVAL 7 DAY) AND m.synflag & $webInstance = 0",
    		'odr' => 'id',
    		'sfg' => 'synflag',
    		'ftb' => "{$this->db_config['dc']}.cp_hbsyxw_paiqi",
    		'bit' => TRUE,
    	);
    	$this->syncr_model->syncr_start($cfg);
    }

}
