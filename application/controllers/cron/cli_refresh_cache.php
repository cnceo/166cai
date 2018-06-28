<?php
/**
 * 竞技彩投注缓存刷新
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Refresh_Cache extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('cache_model');
    }

    public function index()
    {
    	//刷新竞彩足球缓存
        $this->cache_model->refreshJczqMatch();
        //刷新竞彩篮球缓存
        $this->cache_model->refreshJclqMatch();
        
        $this->cache_model->refreshSfcMatch();
        $this->cache_model->refreshSfcMatchInfo();

        $this->cache_model->refreshSfc(11);
        $this->cache_model->refreshSfc(19);

        $this->cache_model->refreshSfcNew(11);
        $this->cache_model->refreshSfcNew(19);
        //彩种配置信息缓存(已修改成同步刷新)
        //$this->cache_model->refreshLotteryConfig();
        //刷新脚本缓存
        $this->cache_model->refreshCrontabConfig();
        $this->refreshOdds();
    }
    
    public function refreshOdds()
    {
        $this->load->model('jcmatch_model');
        $count = $this->jcmatch_model->getHasEndMatch();
        $this->load->model('task_model');
        if ($count[0] > 0) {
            $this->task_model->updateStop(8, 42, 0);
        }
        if ($count[1] > 0) {
            $this->task_model->updateStop(8, 43, 0);
        }
    }
}