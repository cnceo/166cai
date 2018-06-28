<?php

class Cli_Score_Compare extends MY_Controller {
	
	/*
	 * 1 表名确认
	 * 2 比较字段选取
	 * 3 唯一键的选取
	 * 4 state字段的添加
	 * 5 $tables_map配置
	 * 6 检查数据
	 * */
    public function __construct() 
    {
        parent::__construct();
        $this->load->model('compare_model');
    }
	
    public function index()
    {
    	$compare_list = $this->compare_model->get_compare_list();
    	if(!empty($compare_list))
    	{
    		foreach ($compare_list as $list)
    		{
    			if(method_exists($this, "compare_{$list['ctype']}"))
    			{
    				call_user_func_array(array($this, "compare_{$list['ctype']}"),array($list['ctype'], $list['sources']));
    			}
    		}
    	}
    }
    
    //北京单场
    private function compare_bjdc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('half_score', 'full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid', 'mname'));
    }
    
    //北京单场胜负过关
    private function compare_sfgg($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid', 'mname'));
    }
    
    //竞彩足球
	private function compare_jczq($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('half_score', 'full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
    //竞彩篮球
    private function compare_jclq($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
    //进球彩比分
    private function compare_jqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid', 'mname'));
    }
    
    //半全场比分
    private function compare_bqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('half_score', 'full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid', 'mname'));
    }
    
    //胜负彩比分
    private function compare_sfc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('half_score', 'full_score');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid', 'mname'));
    }
    
    //胜负彩赛果
    private function compare_rsfc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('result');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
	private function compare_dsfc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('rj_sale', 'sfc_sale', 'award', 'award_detail');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
    //半全场赛果
    private function compare_rbqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('result');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
	private function compare_dbqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('sale', 'award', 'award_detail');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
    //进球彩赛果
    private function compare_rjqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('result');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }
    
	private function compare_djqc($ctype, $source)
    {
    	$sources = explode(',', $source);
    	$cfields = array('sale', 'award', 'award_detail');
    	$this->compare_model->compare_data($ctype, $sources, $cfields, array('mid'));
    }

    //双色球
    private function compare_ssq($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//双色球详情
    private function compare_rssq($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //大乐透
    private function compare_dlt($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//大乐透详情
    private function compare_rdlt($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //七乐彩
    private function compare_qlc($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
 	//七乐彩详情
    private function compare_rqlc($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //七星彩
    private function compare_qxc($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//七星彩详情
    private function compare_rqxc($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //福彩3D
    private function compare_fc3d($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//福彩3D详情
    private function compare_rfc3d($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //排列三
    private function compare_pl3($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//排列三详情
    private function compare_rpl3($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }

    //排列五
    private function compare_pl5($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('awardNum');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
    
	//排列五详情
    private function compare_rpl5($ctype, $source)
    {
        $sources = explode(',', $source);
        $cfields = array('sale', 'pool', 'bonusDetail');
        $this->compare_model->compare_data($ctype, $sources, $cfields, array('lid', 'issue'));
    }
 }
