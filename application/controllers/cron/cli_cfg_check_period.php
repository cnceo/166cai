<?php
/**
 * Copyright (c) 2012,上海瑞创网络科技股份有限公司.
 * 摘    要: 检查期次是否需求重新预排
 * 作    者: shigx
 * 修改日期: 2015/9/22
 * 修改时间: 13:56
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Cfg_Check_Period extends MY_Controller
{
	private $lidMap = array(
		'51' => array('table' => 'cp_ssq_paiqi', 'alertCount' => '60', 'name' => '双色球'),
		'52' => array('table' => 'cp_fc3d_paiqi', 'alertCount' => '60', 'name' => '福彩3D'),
		'33' => array('table' => 'cp_pl3_paiqi', 'alertCount' => '60', 'name' => '排列三'),
		'35' => array('table' => 'cp_pl5_paiqi', 'alertCount' => '60', 'name' => '排列五'),
		'10022' => array('table' => 'cp_qxc_paiqi', 'alertCount' => '60', 'name' => '七星彩'),
		'23528' => array('table' => 'cp_qlc_paiqi', 'alertCount' => '60', 'name' => '七乐彩'),
		'23529' => array('table' => 'cp_dlt_paiqi', 'alertCount' => '60', 'name' => '大乐透'),
		'21406' => array('table' => 'cp_syxw_paiqi', 'alertCount' => '300', 'name' => '十一选五'),
		'21407' => array('table' => 'cp_jxsyxw_paiqi', 'alertCount' => '300', 'name' => '江西十一选五'),
		'11' => array('table' => 'cp_tczq_paiqi', 'alertCount' => '1', 'name' => '老足彩'),
		'19' => array('table' => 'cp_tczq_paiqi', 'alertCount' => '1', 'name' => '老足彩'),
		'53' => array('table' => 'cp_ks_paiqi', 'alertCount' => '300', 'name' => '上海快三'),
        '56' => array('table' => 'cp_jlks_paiqi', 'alertCount' => '300', 'name' => '吉林快三'),
	    '57' => array('table' => 'cp_jxks_paiqi', 'alertCount' => '300', 'name' => '江西快三'),
		'21408' => array('table' => 'cp_hbsyxw_paiqi', 'alertCount' => '300', 'name' => '湖北十一选五'),
        '54' => array('table' => 'cp_klpk_paiqi', 'alertCount' => '300', 'name' => '快乐扑克'),
        '55' => array('table' => 'cp_cqssc_paiqi', 'alertCount' => '300', 'name' => '老时时彩'),
	    '21421' => array('table' => 'cp_gdsyxw_paiqi', 'alertCount' => '300', 'name' => '广东十一选五'),
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('lottery_config_model', 'lotteryConfig');
    }

    public function index()
    {
        $warnList = array();
        $configItems = $this->lotteryConfig->fetchConfigItems();
        foreach ($configItems as $value)
        {
        	if(isset($this->lidMap[$value['lotteryId']]))
        	{
        		$lottery = $this->lidMap[$value['lotteryId']];
        		if(in_array($value['lotteryId'], array('11', '19')))
        		{
        			$count = $this->lotteryConfig->getCountTczqIssue($lottery['table']);
        		}
        		else
        		{
        			$count = $this->lotteryConfig->getCountNumberIssue($lottery['table']);
        		}
        		

        		if($count < $lottery['alertCount'])
        		{
                    array_push($warnList, $lottery['name']);
        		}
        	}
        }

        if(!empty($warnList))
        {
            $warnList = implode(',', $warnList);
            $message = "{$warnList}预排的期次即将用完，请及时到后台进行重新预排。";
            $this->lotteryConfig->saveAlert(2, $message);
        }
    }
}