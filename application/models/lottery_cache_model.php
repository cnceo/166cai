<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/8
 * 修改时间: 17:44
 */
class Lottery_Cache_model extends MY_Model
{
	private $lidMap = array(
			'51' => array('table' => 'cp_ssq_paiqi', 'cache' => 'SSQ'),
            '23529' => array('table' => 'cp_dlt_paiqi', 'cache' => 'DLT'),
            '10022' => array('table' => 'cp_qxc_paiqi', 'cache' => 'QXC'),
            '23528' => array('table' => 'cp_qlc_paiqi', 'cache' => 'QLC'),
            '52' => array('table' => 'cp_fc3d_paiqi', 'cache' => 'FCSD'),
            '33' => array('table' => 'cp_pl3_paiqi', 'cache' => 'PLS'),
            '35' => array('table' => 'cp_pl5_paiqi', 'cache' => 'PLW'),
            '21406' => array('table' => 'cp_syxw_paiqi', 'cache' => 'SYXW'),
			'21407' => array('table' => 'cp_jxsyxw_paiqi', 'cache' => 'JXSYXW'),
			'53' => array('table' => 'cp_ks_paiqi', 'cache' => 'KS'),
            '56' => array('table' => 'cp_jlks_paiqi', 'cache' => 'JLKS'),
	        '57' => array('table' => 'cp_jxks_paiqi', 'cache' => 'JXKS'),
			'21408' => array('table' => 'cp_hbsyxw_paiqi', 'cache' => 'HBSYXW'),
            '54' => array('table' => 'cp_klpk_paiqi', 'cache' => 'KLPK'),
            '55' => array('table' => 'cp_cqssc_paiqi', 'cache' => 'CQSSC'),
	        '21421' => array('table' => 'cp_gdsyxw_paiqi', 'cache' => 'GDSYXW'),
	);
	
    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
    }
    
    public function run($lid = 0)
    {
    	$lids = array_keys($this->lidMap);
    	if(!empty($lid) && in_array($lid, $lids))
    	{
    		$this->getCombineIssue($lid);
    	}
    	else 
    	{
	    	foreach ($lids as $lid)
	    	{
	    		$this->getCombineIssue($lid);
	    	}
    	}
    }
    
    public function getCombineIssue($lid)
    {	
    	$sql = "SELECT issue, award_time, show_end_time FROM `{$this->lidMap[$lid]['table']}`
				WHERE 1 AND show_end_time > now( )
				AND award_time > now( ) and delect_flag = 0 and is_open = 1
				ORDER BY issue limit 200";
    	$data = $this->cfgDB->query($sql)->getAll();
        $this->cache->hSet($this->REDIS['ISSUE_COMING'], $this->lidMap[$lid]['cache'], json_encode($data));
    }
}