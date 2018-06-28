<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：对阵管理
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Match extends MY_Controller
{
	private $tczq_ctype = array(
		'1' => '胜负14场/任选九',
		'2' => '六场半全场',
		'3' => '四场进球彩'
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_match');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->load->helper(array("fn_common"));
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：北京单场对阵列表
     * 修改日期：2015.03.25
     */
    public function index()
    {
        $this->check_capacity('7_3_1');
    	$mids = $this->Model_match->get_bjdc_mids();
    	$mid = $this->input->get("mid", true);
    	$mid = empty($mid) ? $mids[0] : $mid;
    	$searchData = array(
    		"mid" => $mid,
    	);
    	
    	$result = $this->Model_match->list_bjdc($searchData);
    	
    	$infos = array(
    		"mids"		=> $mids,
    		"search"	=> $searchData,
    		"result"	=> $result
    	);
    	
        $this->load->view("bjdc", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：北单胜负过关对阵列表
     * 修改日期：2015.03.25
     */
    public function bdsfgg()
    {
        $this->check_capacity('7_3_1');
    	$mids = $this->Model_match->get_bjdc_mids();
    	$mid = $this->input->get("mid", true);
    	$mid = empty($mid) ? $mids[0] : $mid;
    	$searchData = array(
    			"mid" => $mid,
    	);
    	 
    	$result = $this->Model_match->list_bdsfgg($searchData);
    	 
    	$infos = array(
    			"mids"		=> $mids,
    			"search"	=> $searchData,
    			"result"	=> $result
    	);
    	 
    	$this->load->view("bdsfgg", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：体彩足球对阵列表
     * 修改日期：2015.03.25
     */
    public function tczq()
    {
        $this->check_capacity('7_3_1');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 1 : $ctype;
    	$mids = $this->Model_match->get_tczq_mids($ctype);
    	$mid = $this->input->get("mid", true);
    	$mid = empty($mid) ? $mids[0] : ($mid > $mids[0] ? $mids[0] : $mid);
    	$searchData = array(
    			"ctype"	=> $ctype,
    			"mid"	=> $mid,
    	);
    
    	$result = $this->Model_match->list_tczq($searchData);

    	$infos = array(
    			"ctypes"		=> $this->tczq_ctype,
    			"mids"		=> $mids,
    			"search"	=> $searchData,
    			"result"	=> $result,
    	);
    
    	$this->load->view("tczq", $infos);
    }

    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：竞彩足球对阵列表
     * 修改日期：2015.03.25
     */
    public function jczq()
    {
        $this->check_capacity('7_3_1');
    	$searchData = array(
    		"start_time" => $this->input->get("start_time", true),
    		"end_time" => $this->input->get("end_time", true),
            "is_aduitflag" => $this->input->get("is_aduitflag", true),  // 是否审核
            "is_capture" => $this->input->get("is_capture", true),    // 抓取状态
    	);
    	$this->filteTime($searchData['start_time'], $searchData['end_time']);
    	$result = $this->Model_match->list_jczq($searchData);
        // 审核状态
        $aduitflagArr = array('0' => '全部', '1' => '待审核', '2' => '已审核', '3' => '人工审核', '4' => '系统审核');
        // 抓取状态
        $captureArr = array('0' => '全部', '1' => '已抓取', '2' => '未抓取');
    	$infos = array(
    			"search"	=> $searchData,
    			"result"	=> $result,
                "aduitflagArr" => $aduitflagArr,
                "captureArr" => $captureArr,
    	);
    
    	$this->load->view("jczq", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：竞彩篮球对阵列表
     * 修改日期：2015.03.25
     */
    public function jclq()
    {
        $this->check_capacity('7_3_1');
    	$searchData = array(
			"start_time" => $this->input->get("start_time", true),
			"end_time" => $this->input->get("end_time", true),
            "is_aduitflag" => $this->input->get("is_aduitflag", true),  // 是否审核
            "is_capture" => $this->input->get("is_capture", true),    // 抓取状态
    	);
    	$this->filteTime($searchData['start_time'], $searchData['end_time']);
    	$result = $this->Model_match->list_jclq($searchData);
        // 审核状态
        $aduitflagArr = array('0' => '全部', '1' => '待审核', '2' => '已审核', '3' => '人工审核', '4' => '系统审核');
        // 抓取状态
        $captureArr = array('0' => '全部', '1' => '已抓取', '2' => '未抓取');
    	$infos = array(
    			"search"	=> $searchData,
    			"result"	=> $result,
                "aduitflagArr" => $aduitflagArr,
                "captureArr" => $captureArr,
    	);
    
    	$this->load->view("jclq", $infos);
    }
    
    /**
     * 冠军彩
     */
    public function gj()
    {
        $this->check_capacity('7_3_1');
    	$issues = $this->Model_match->get_champion_issues(1);
    	$issue = $this->input->get("issue", true);
    	$issue = empty($issue) ? $issues[0] : $issue;
    	$result = $this->Model_match->list_champion($issue, 1);
    	$infos = array(
    		'issues' => $issues,
    		'issue' => $issue,
    		'result' => $result
    	);
    	$this->load->view("gj", $infos);
    }
    
    /**
     * 冠亚军彩
     */
    public function gyj()
    {
        $this->check_capacity('7_3_1');
    	$issues = $this->Model_match->get_champion_issues(2);
    	$issue = $this->input->get("issue", true);
    	$issue = empty($issue) ? $issues[0] : $issue;
    	$result = $this->Model_match->list_champion($issue, 2);
    	$infos = array(
    			'issues' => $issues,
    			'issue' => $issue,
    			'result' => $result
    	);
    	$this->load->view("gyj", $infos);
    }
    
    /*
* 修改表中字段的值
* 作者 ：刘祯
* 时间：2015-08-17
* */
    public function alter()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
        $data = $this->input->post('data', true);
        $datas = json_decode($data, true);
        foreach($datas as $value)
        {
            $value['synflag'] = 0;
            if(((preg_match('/([0-9]{3}[1-9]|[0-9]{2}[1-9][0-9]{1}|[0-9]{1}[1-9][0-9]{2}|[1-9][0-9]{3})-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8])))\s+[\d]{2}\:[\d]{2}/',$value['begin_date'] )) == FALSE && $value['begin_date'] != null)||
                (!is_numeric($value['eur_odd_win']) && $value['eur_odd_win'] != null)||(!is_numeric($value['eur_odd_deuce']) && $value['eur_odd_deuce'] != null)||(!is_numeric($value['eur_odd_loss']) && $value['eur_odd_loss'] != null)
                ||$value['eur_odd_win'] < 0  ||  $value['eur_odd_deuce'] < 0 || $value['eur_odd_loss'] < 0)
            {

                return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
            }
            else
            {
                $row = $this->Model_match->updateRow($value);
                //触发比分同步任务
                $this->Model_match->updateTicketStop(6, 10, 0);
                if(isset($value['begin_date']))
                {
                    $this->syslog(18, "老足彩 第（".$value['id']."）期修改(比赛时间)为".$value['begin_date']);
                }
                if(isset($value['eur_odd_win']))
                {
                    $this->syslog(18, "老足彩 第（".$value['id']."）期修改(欧赔胜)为".$value['eur_odd_win']);
                }
                if(isset($value['eur_odd_deuce']))
                {
                    $this->syslog(18, "老足彩 第（".$value['id']."）期修改(欧赔平)为".$value['eur_odd_deuce']);
                }
                if(isset($value['eur_odd_loss']))
                {
                    $this->syslog(18, "老足彩 第（".$value['id']."）期修改(欧赔负)为".$value['eur_odd_loss']);
                }

            }

        }

        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：北京单场取消操作
     * 修改日期：2015.03.25
     */
    public function bjdc_cancel()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_7', true);
    	$id = intval($this->input->post("cancelId", true));
        $res = $this->Model_match->get_bjdc($id);
    	$data['m_status'] = 1;
    	$data['spf_odds'] = 1;
    	$data['dcbf_odds'] = 1;
    	$data['bqc_odds'] = 1;
    	$data['dss_odds'] = 1;
    	$data['jqs_odds'] = 1;
    	$data['status'] = 50;
    	$data['state'] = 1;
        $data['d_synflag'] = 0;
    	$row = $this->Model_match->bjdc_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 41, 0);
    	$this->Model_match->updateScoreState('cp_bjdc_score', $res['mid'], $res['mname']); //更新比对状态
        $this->syslog(18, "北京单场 第（".$res['mid']."）期（".$res['mname']."）场取消比赛");
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：北京单场取消操作
     * 修改日期：2015.03.25
     */
    public function sfgg_cancel()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_7', true);
    	$id = intval($this->input->post("cancelId", true));
        $res = $this->Model_match->get_sfgg($id);
    	$data['m_status'] = 1;
    	$data['sfgg_odds'] = 1;
    	$data['status'] = 50;
    	$data['state'] = 1;
        $data['d_synflag'] = 0;
    	$row = $this->Model_match->sfgg_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 40, 0);
    	$this->Model_match->updateScoreState('cp_sfgg_score', $res['mid'], $res['mname']); //更新比对状态
        $this->syslog(18, "北京单场胜负过关 第（".$res['mid']."）期（".$res['mname']."）场取消比赛");
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：竞彩足球取消操作
     * 修改日期：2015.03.25
     */
    public function jczq_cancel()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_7', true);
    	$id = intval($this->input->post("cancelId", true));
        $res = $this->Model_match->get_jczq($id);
    	$data['m_status'] = 1;
    	$data['status'] = 50;
        $data['d_synflag'] = 0;
    	$row = $this->Model_match->jczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
        //更新人工审核状态
        $captureData = array(
            'aduitflag' => 1,
        );
        $this->Model_match->saveJczqCapture($res['mid'], $captureData);
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 42, 0);
    	$this->Model_match->updateScoreState('cp_jczq_score', $res['mid']); //更新比对状态
        
        $this->syslog(18, "竞彩足球 第（".$res['mid']."）期（".$res['mname']."）场取消比赛");
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：竞彩篮球取消操作
     * 修改日期：2015.03.25
     */
    public function jclq_cancel()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_7', true);
    	$id = intval($this->input->post("cancelId", true));
        $res = $this->Model_match->get_jclq($id);
    	$data['m_status'] = 1;
    	$data['status'] = 50;
        $data['d_synflag'] = 0;
    	$row = $this->Model_match->jclq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
        //更新人工审核状态
        $captureData = array(
            'aduitflag' => 1,
        );
        $this->Model_match->saveJclqCapture($res['mid'], $captureData);
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 43, 0);
    	$this->Model_match->updateScoreState('cp_jclq_score', $res['mid']); //更新比对状态
        
        $this->syslog(18, "竞彩篮球 第（".$res['mid']."）期（".$res['mname']."）场取消比赛");
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：北京单场修改比赛时间操作
     * 修改日期：2015.03.25
     */
    public function bjdc_update_time()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
        $id = intval($this->input->post("timeId", true));
        $res = $this->Model_match->get_bjdc($id);
    	$data['begin_time'] = $this->input->post("time", true);
    	if($data['begin_time'] > date('Y-m-d H:i:s'))
    	{
    		$data['status'] = 1;
    	}
    	$data['synflag'] = '0';
        $time = $this->Model_match->selcetTime('bjdc','begin_time',$id);
    	$row = $this->Model_match->bjdc_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(6, 41, 0);
    	$this->Model_match->updateTicketStop(1, 41, 0);
        foreach($time as $times)
        {
            if($times['begin_time'] != $data['begin_time'])
            {
                $this->syslog(18, "北京单场 第（".$res['mid']."）期（".$res['mname']."）场修改比赛时间为".$data['begin_time']);
            }
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：胜负过关修改比赛时间操作
     * 修改日期：2015.03.25
     */
    public function sfgg_update_time()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
    	$id = intval($this->input->post("timeId", true));
        $res = $this->Model_match->get_sfgg($id);
    	$data['begin_time'] = $this->input->post("time", true);
    	if($data['begin_time'] > date('Y-m-d H:i:s'))
    	{
    		$data['status'] = 1;
    	}
    	$data['synflag'] = '0';
        $time = $this->Model_match->selcetTime('sfgg','begin_time',$id);
    	$row = $this->Model_match->sfgg_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(6, 40, 0);
    	$this->Model_match->updateTicketStop(1, 40, 0);
        foreach($time as $times)
        {
            if($times['begin_time'] != $data['begin_time'])
            {
                $this->syslog(18, "北京单场胜负过关 第（".$res['mid']."）期（".$res['mname']."）场修改比赛时间为".$data['begin_time']);//同上
            }
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：体彩足球修改比赛时间操作
     * 修改日期：2015.03.25
     */
    public function jczq_update_time()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
    	$id = intval($this->input->post("timeId", true));
        $res = $this->Model_match->get_jczq($id);
    	$data['end_sale_time'] = $this->input->post("time", true);
    	if($data['end_sale_time'] > date('Y-m-d H:i:s'))
    	{
    		$data['status'] = 1;
    	}
    	$data['synflag'] = '0';
    	$data['sale_time_set'] = 1;
        $time = $this->Model_match->selcetTime('jczq','end_sale_time',$id);
    	$row = $this->Model_match->jczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(6, 42, 0);
    	// $this->Model_match->updateTicketStop(1, 42, 0);
        foreach($time as $times)
        {
            if($times['end_sale_time'] != $data['end_sale_time'])
            {
                 $this->syslog(18, "竞彩足球 第（".$res['mid']."）期（".$res['mname']."）场修改比赛时间为".$data['end_sale_time']);
            }
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：体彩足球修改比赛时间操作
     * 修改日期：2015.03.25
     */
    public function jclq_update_time()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
    	$id = intval($this->input->post("timeId", true));
        $res = $this->Model_match->get_jclq($id);
    	$data['begin_time'] = $this->input->post("time", true);
    	if($data['begin_time'] > date('Y-m-d H:i:s'))
    	{
    		$data['status'] = 1;
    	}
    	$data['synflag'] = '0';
    	$data['sale_time_set'] = 1;
        $time = $this->Model_match->selcetTime('jclq','begin_time',$id);
    	$row = $this->Model_match->jclq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(6, 43, 0);
    	// $this->Model_match->updateTicketStop(1, 43, 0);
        foreach($time as $times)
        {
            if($times['begin_time'] != $data['begin_time'])
            {
                $this->syslog(18, "竞彩篮球 第（".$res['mid']."）期（".$res['mname']."）场修改(比赛时间为".$data['begin_time'] );
            }
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：体彩足球延期操作
     * 修改日期：2015.03.25
     */
    public function tczq_delay()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_4', true);
    	$id = intval($this->input->post("delayId", true));
        $res = $this->Model_match->get_tczq($id);
    	$data['status'] = 51;
    	$row = $this->Model_match->tczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 10, 0);
    	$scoreTable = 'cp_sfc_score';
    	if($res['ctype'] == '2')
    	{
    		$scoreTable = 'cp_bqc_score';
    	}
    	elseif ($res['ctype'] == '3')
    	{
    		$scoreTable = 'cp_jqc_score';
    	}
    	$this->Model_match->updateScoreState($scoreTable, $res['mid'], $res['mname']); //更新比对状态
        $this->syslog(18, "老足彩 第（".$res['mid']."）期".str_pad($res['mname'], 3,"0", STR_PAD_LEFT)."场进行延期操作");
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：体彩足球修改比赛时间操作
     * 修改日期：2015.03.25
     */
    public function tczq_update_time()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_2', true);
    	$id = intval($this->input->post("timeId", true));
        $res = $this->Model_match->get_tczq($id);
    	$data['begin_date'] = $this->input->post("time", true);
    	if($data['begin_date'] > date('Y-m-d H:i:s'))
    	{
    		$data['status'] = 1;
    	}
    	$data['synflag'] = '0';
        $time = $this->Model_match->selcetTime('tczq','begin_date',$id);
    	$row = $this->Model_match->tczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(6, 10, 0);
    	$this->Model_match->updateTicketStop(1, 10, 0);
        foreach($time as $times)
        {
            if($times['begin_date'] != $data['begin_date'])
            {
                $this->syslog(18, "老足彩 第（".$res['mid']."）期（".$res['mname']."）场修改比赛时间为".$data['begin_date'] );
            }
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：北京单场修改比赛分数操作
     * 修改日期：2015.03.25
     */
    public function bjdc_update_score()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_3', true);
    	$id = intval($this->input->post("scoreId", true));
        $matchStatus = $this->input->post('matchStatus', true);
    	$res = $this->Model_match->get_bjdc($id);
    	if(isset($res['m_status']) && $res['m_status'] ==1)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$half_h = $this->input->post("half_h", true);
    	$half_a = $this->input->post("half_a", true);
    	$full_h = $this->input->post("full_h", true);
    	$full_a = $this->input->post("full_a", true);
    	if(!isset($half_h) || !isset($half_a) || $half_h === '' || $half_a === '')
    	{
    		$data['half_score'] = '';
    	}
    	else
    	{
    		$data['half_score'] = $half_h . ':' . $half_a;

    	}
    	if(!isset($full_h) || !isset($full_a)  || $full_h === '' || $full_a === '')
    	{
    		$data['full_score'] = '';
    	}
    	else
    	{
    		$data['full_score'] = $full_h . ':' . $full_a;
    	}
    	$data['status'] = 50;
        if($matchStatus == 0)
        {
            $data['d_synflag'] = '0';
        }
    	$row = $this->Model_match->bjdc_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 41, 0);
    	$this->Model_match->updateScoreState('cp_bjdc_score', $res['mid'], $res['mname']); //更新比对状态
        if($res['half_score'] != $data['half_score'])
        {
            $this->syslog(18, "北京单场 第（".$res['mid']."）期（".$res['mname']."）场修改(半场)比分为".$data['half_score'] );
        }
        if($res['full_score'] != $data['full_score'])
        {
            $this->syslog(18, "北京单场 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(全场)比分为" . $data['full_score']);
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：北京单场修改比赛分数操作
     * 修改日期：2015.03.25
     */
    public function sfgg_update_score()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_3', true);
    	$id = intval($this->input->post("scoreId", true));
        $matchStatus = $this->input->post('matchStatus', true);
    	$res = $this->Model_match->get_sfgg($id);
    	if(isset($res['m_status']) && $res['m_status'] ==1)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$full_h = $this->input->post("full_h", true);
    	$full_a = $this->input->post("full_a", true);   	 
    	if(!isset($full_h) || !isset($full_a)  || $full_h === '' || $full_a === '')
    	{
    		$data['full_score'] = '';
    	}
    	else
    	{
    		$data['full_score'] = $full_h . ':' . $full_a;
    	}
    	$data['status'] = 50;
        if($matchStatus == 0)
        {
            $data['d_synflag'] = '0';
        }
    	$row = $this->Model_match->sfgg_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 40, 0);
    	$this->Model_match->updateScoreState('cp_sfgg_score', $res['mid'], $res['mname']); //更新比对状态
        if($res['full_score'] != $data['full_score'])
        {
            $this->syslog(18, "北京单场胜负过关 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(全场)比分为" . $data['full_score']);
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：竞彩足球修改比赛分数操作
     * 修改日期：2015.03.25
     */
    public function jczq_update_score()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_3', true);
    	$id = intval($this->input->post("scoreId", true));
        $matchStatus = $this->input->post('matchStatus', true);
    	$res = $this->Model_match->get_jczq($id);
    	if(isset($res['m_status']) && $res['m_status'] ==1)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$half_h = $this->input->post("half_h", true);
    	$half_a = $this->input->post("half_a", true);
    	$full_h = $this->input->post("full_h", true);
    	$full_a = $this->input->post("full_a", true);
    	if(!isset($half_h) || !isset($half_a) || $half_h === '' || $half_a === '')
    	{
    		$data['half_score'] = '';
    	}
    	else
    	{
    		$data['half_score'] = $half_h . ':' . $half_a;
    	}
    	 
    	if(!isset($full_h) || !isset($full_a)  || $full_h === '' || $full_a === '')
    	{
    		$data['full_score'] = '';
    	}
    	else
    	{
    		$data['full_score'] = $full_h . ':' . $full_a;
    	}
    	// $data['status'] = 50;
        if($matchStatus == 0)
        {
            // $data['d_synflag'] = '0';
        }
    	$row = $this->Model_match->jczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	// $this->Model_match->updateTicketStop(1, 42, 0);
    	$this->Model_match->updateScoreState('cp_jczq_score', $res['mid']); //更新比对状态
        if($res['half_score'] != $data['half_score'])
        {
            $this->syslog(18, "竞彩足球 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(半场)比分为" . $data['half_score']);
        }
        if($res['full_score'] != $data['full_score'])
        {
            $this->syslog(18, "竞彩足球 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(全场)比分为" . $data['full_score']);
        }
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：竞彩篮球修改比赛分数操作
     * 修改日期：2015.03.25
     */
    public function jclq_update_score()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_3', true);
    	$id = intval($this->input->post("scoreId", true));
        $matchStatus = $this->input->post('matchStatus', true);
    	$res = $this->Model_match->get_jclq($id);
    	if(isset($res['m_status']) && $res['m_status'] ==1)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$full_h = $this->input->post("full_h", true);
    	$full_a = $this->input->post("full_a", true);   
    	if(!isset($full_h) || !isset($full_a)  || $full_h === '' || $full_a === '')
    	{
    		$data['full_score'] = '';
    	}
    	else
    	{
    		$data['full_score'] = $full_a . ':' . $full_h;

    	}
    	// $data['status'] = 50;
        if($matchStatus == 0)
        {
            // $data['d_synflag'] = '0';
        }
    	$row = $this->Model_match->jclq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	// $this->Model_match->updateTicketStop(1, 43, 0);
    	$this->Model_match->updateScoreState('cp_jclq_score', $res['mid']); //更新比对状态
        if($res['full_score'] != $data['full_score'])
        {
            $this->syslog(18, "竞彩篮球 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(全场)比分为" . $data['full_score']);
        }
    
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：体彩足球修改比赛分数操作
     * 修改日期：2015.03.25
     */
    public function tczq_update_score()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity('7_3_3', true);
    	$id = intval($this->input->post("scoreId", true));
        $matchStatus = $this->input->post('matchStatus', true);
        $res = $this->Model_match->get_tczq($id);
    	$half_h = $this->input->post("half_h", true);
    	$half_a = $this->input->post("half_a", true);
    	$full_h = $this->input->post("full_h", true);
    	$full_a = $this->input->post("full_a", true);
    	if(!isset($half_h) || !isset($half_a) || $half_h === '' || $half_a === '')
    	{
    		$data['half_score'] = '';
    	}
    	else
    	{
    		$data['half_score'] = $half_h . ':' . $half_a;

    	}
    	 
    	if(!isset($full_h) || !isset($full_a)  || $full_h === '' || $full_a === '')
    	{
    		$data['full_score'] = '';
    	}
    	else
    	{
    		$data['full_score'] = $full_h . ':' . $full_a;
    	}
    	$data['status'] = 50;
        if($matchStatus == 0)
        {
            $data['d_synflag'] = '0';
        }
    	$row = $this->Model_match->tczq_update($id, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	//触发比分同步任务
    	$this->Model_match->updateTicketStop(1, 10, 0);
    	$scoreTable = 'cp_sfc_score';
    	if($res['ctype'] == '2')
    	{
    		$scoreTable = 'cp_bqc_score';
    	}
    	elseif ($res['ctype'] == '3')
    	{
    		$scoreTable = 'cp_jqc_score';
    	}
    	$this->Model_match->updateScoreState($scoreTable, $res['mid'], $res['mname']); //更新比对状态
        if($res['half_score'] != $data['half_score'])
        {
            $this->syslog(18, "老足彩 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(半场)比分为" . $data['half_score']);
        }
        if($res['full_score'] != $data['full_score'])
        {
            $this->syslog(18, "老足彩 第（" . $res['mid'] . "）期（" . $res['mname'] . "）场修改(全场)比分为" . $data['full_score']);
        }
    
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
    
    /**
     * 参    数：$time1 时间1
     *       $time2 时间2
     * 作    者：shigx
     * 功    能：过滤
     * 修改日期：2015.03.25
     */
    public function filteTime(&$time1, &$time2)
    {
    	if (!empty($time1) || !empty($time2))
    	{
    		if (empty($time1))
    		{
    			$time1 = date("Y-m-d 00:00:00", strtotime('-1 week', strtotime($time2)));
    		}
    		elseif (empty($time2))
    		{
    			$time2 = date("Y-m-d 23:59:59", strtotime('+1 week', strtotime($time1)));
    		}
    		else
    		{
    			if (strtotime($time1) > strtotime($time2))
    			{
    				echo "时间非法";
    				exit;
    			}
    			 
    			if (strtotime("-1 week", strtotime($time2)) > strtotime($time1))
    			{
    				$time2 = date("Y-m-d 23:59:59", strtotime("+1 week", strtotime($time1)));
    			}
    		}
    	}
    	else
    	{
    		$time2 = date("Y-m-d 23:59:59");
    		$time1 = date("Y-m-d 00:00:00");
    	}
    }

    // 查询竞彩足球抓取详情
    public function getJczqCapture()
    {
        $searchData = array(
            'mid'    =>  $this->input->post("mid", true),
        );

        $info = $this->Model_match->getJczqCapture($searchData);

        if(!empty($info))
        {
            $tpl = '';
            $tpl .= '<tr><th width="60">' . $info[0]['mname'] . '</th><td>' . $info[0]['home'] . ' VS ' . $info[0]['away'] . '</td></tr>';
            $tpl .= '<tr class="selectTab0">';
            $tpl .= '<th style="vertical-align: top;">抓取网站</th>';
            $tpl .= '<td><ul>';
            foreach ($info as $key => $items) 
            {
                $checked = ($key == 0) ? 'checked' : '';
                $score = (!empty($items['full_score']) && !empty($items['half_score'])) ? trim($items['full_score']) . '#' . trim($items['half_score']) : '';
                $tpl .= '<li>';
                $tpl .= '<label for="' . $items['lname'] . '">';
                $tpl .= '<input type="radio" class="radio" id="' . $items['lname'] . '" name="captured" value="' . $score . '"' . $checked . '>';
                $tpl .= $items['cname'];
                if($score)
                {
                    $tpl .= ' 半全场比分 ' . $items['full_score'] . ' （' . $items['half_score'] . '）';
                }
                else
                {
                    $tpl .= ' 暂无比分';
                }
                $tpl .= '</label></li>';
            }
            $tpl .= '</ul></td>';
            $tpl .= '</tr>';
            $tpl .= '<input type="hidden" name="captureMid" value="' . $searchData['mid'] . '" />';

            $result = array(
                'status' => '1',
                'msg' => '请求成功',
                'data' => array(
                    'html' => $tpl
                )
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '请求成功',
                'data' => array()
            );
        }
        
        die(json_encode($result));
    }

    // 查询竞彩篮球抓取详情
    public function getJclqCapture()
    {
        $searchData = array(
            'mid'    =>  $this->input->post("mid", true),
        );

        $info = $this->Model_match->getJclqCapture($searchData);

        if(!empty($info))
        {
            $tpl = '';
            $tpl .= '<tr><th width="60">' . $info[0]['mname'] . '</th><td>' . $info[0]['away'] . ' VS ' . $info[0]['home'] . '</td></tr>';
            $tpl .= '<tr class="selectTab0">';
            $tpl .= '<th style="vertical-align: top;">抓取网站</th>';
            $tpl .= '<td><ul>';
            foreach ($info as $key => $items) 
            {
                $checked = ($key == 0) ? 'checked' : '';
                $score = (!empty($items['full_score'])) ? trim($items['full_score']) : '';
                $tpl .= '<li>';
                $tpl .= '<label for="' . $items['lname'] . '">';
                $tpl .= '<input type="radio" class="radio" id="' . $items['lname'] . '" name="captured" value="' . $score . '"' . $checked . '>';
                $tpl .= $items['cname'];
                if($score)
                {
                    $tpl .= ' 全场比分 ' . $score;
                }
                else
                {
                    $tpl .= ' 暂无比分';
                }
                $tpl .= '</label></li>';
            }
            $tpl .= '</ul></td>';
            $tpl .= '</tr>';
            $tpl .= '<input type="hidden" name="captureMid" value="' . $searchData['mid'] . '" />';

            $result = array(
                'status' => '1',
                'msg' => '请求成功',
                'data' => array(
                    'html' => $tpl
                )
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '请求成功',
                'data' => array()
            );
        }
        
        die(json_encode($result));
    }

    // 比分录入
    public function saveJczqCapture()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("7_3_6", true);
        $postData = array(
            'scoreVal'  =>  $this->input->post("scoreVal", true),
            'mid'       =>  $this->input->post("mid", true),
        );

        $matchInfo = $this->Model_match->getJczqPaiqi($postData['mid']);

        if(!empty($postData['scoreVal']) && !empty($matchInfo))
        {
            $scoreArr = explode('#', $postData['scoreVal']);
            if(empty($scoreArr[0]) || empty($scoreArr[1]))
            {
                return $this->ajaxReturn('n', '比分录入信息不全，录入失败');
            }
            $scoreData = array(
                'full_score' => $scoreArr[0] ? $scoreArr[0] : '',
                'half_score' => $scoreArr[1] ? $scoreArr[1] : '',           
                // 'status'     => 50,
            );
            // 更新paiqi比分
            $row = $this->Model_match->saveJczqCapture($postData['mid'], $scoreData);
            if($row === false || $row == 0)
            {
                return $this->ajaxReturn('n', '该场次比分已审核，无需录入比分');
            }
            $stopData = array(
                'state' => 1,
            );
            // 停止抓取对比
            $this->Model_match->updateJczqscore($postData['mid'], $stopData);
            // 记录日志
            $this->syslog(18, "比分录入记录：竞彩足球 第（". $matchInfo['m_date'] ."）期（" . $matchInfo['mname'] . "）比分录入");

            return $this->ajaxReturn('y', '比分录入成功');
        }
        else
        {
            return $this->ajaxReturn('n', '比分录入信息不全，录入失败');
        }
    }

    // 比分录入
    public function saveJclqCapture()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("7_3_6", true);
        $postData = array(
            'scoreVal'  =>  $this->input->post("scoreVal", true),
            'mid'       =>  $this->input->post("mid", true),
        );

        $matchInfo = $this->Model_match->getJclqPaiqi($postData['mid']);

        if(!empty($postData['scoreVal']) && !empty($matchInfo))
        {
            $scoreArr = explode(':', $postData['scoreVal']);
            if(empty($scoreArr[0]) || empty($scoreArr[1]))
            {
                return $this->ajaxReturn('n', '比分录入信息不全，录入失败');
            }
            $scoreData = array(
                'full_score' => $postData['scoreVal'] ? $postData['scoreVal'] : '',         
                // 'status'     => 50,
            );
            // 更新paiqi比分
            $row = $this->Model_match->saveJclqCapture($postData['mid'], $scoreData);
            if($row === false || $row == 0)
            {
                return $this->ajaxReturn('n', '该场次比分已审核，无需录入比分');
            }
            $stopData = array(
                'state' => 1,
            );
            // 停止抓取对比
            $this->Model_match->updateJclqscore($postData['mid'], $stopData);
            // 记录日志
            $this->syslog(18, "比分录入记录：竞彩篮球 第（". $matchInfo['m_date'] ."）期（" . $matchInfo['mname'] . "）比分录入");

            return $this->ajaxReturn('y', '比分录入成功');
        }
        else
        {
            return $this->ajaxReturn('n', '比分录入信息不全，录入失败');
        }
    }

    public function verifyJczqScore()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("7_3_5", true);
        $score = $this->input->post('score', true);
        $idArr = array();
        $errId = array();  // 比对错误场次
        $succId = array();
        $succMatch = array();
        if(!empty($score))
        {
            foreach ($score as $id => $val)
            {
                if($val['f_h'] === '' ||  $val['f_a'] === '' || $val['h_h'] === '' || $val['h_a'] === '')
                {
                    array_push($errId, $id);
                }
                else
                {
                    array_push($idArr, $id);
                }      
            }

            if(!empty($errId))
            {
                return $this->ajaxReturn('n', '比分录入信息不全，审核失败');
            }
            $errId = array();
            $idArr = array_unique($idArr);
            if(!empty($idArr))
            {
                // 查询比分信息
                $info = $this->Model_match->getjczqInfo($idArr);
                if(!empty($info))
                {
                    foreach ($info as $key => $items) 
                    {
                        $full_score = intval($score[$items['id']]['f_h']) . ':' . intval($score[$items['id']]['f_a']);
                        $half_score = intval($score[$items['id']]['h_h']) . ':' . intval($score[$items['id']]['h_a']);

                        if($full_score == $items['full_score'] && $half_score == $items['half_score'])
                        {
                            array_push($succId, $items['mid']);
                            $succMatch[$items['m_date']][] = $items['mname'];
                        }
                        else
                        {
                            // 记录对比错误场次
                            array_push($errId, $items['mname']);
                        }
                    }
                }
            }
        }

        if(!empty($succId) && !empty($succMatch))
        {
            // 更新cfg status = 50
            $this->Model_match->updatejczqStatus($succId);
            // 更新审核状态 人工审核
            $this->Model_match->updateJczqAduitflag($succId, 1);
            // 触发过关
            $this->Model_match->updateTicketStop(1, 42, 0);
            // 记录日志
            $logs = "比分审核记录：竞彩足球 ";
            foreach ($succMatch as $m_date => $mnames) 
            {
                $logs .= "第（". $m_date ."）期（" . implode(',', array_values($mnames)) . "） ";
            }
            $logs .= "比分审核通过";
            $this->syslog(18, $logs);

            if(!empty($errId))
            {
                return $this->ajaxReturn('y', implode(',', $errId) . '比分审核失败，请重新审核，其他场次均已录入成功');
            }
            else
            {
                return $this->ajaxReturn('y', '比分审核成功');
            }
        }
        else
        {
            return $this->ajaxReturn('n', '比分录入信息不全，审核失败');
        }  
    }

    public function verifyJclqScore()
    {
    	$env = $this->input->post('env');
    	$this->checkenv($env, true);
        $this->check_capacity("7_3_5", true);
        $score = $this->input->post('score', true);
        $idArr = array();
        $errId = array();  // 比对错误场次
        $succId = array();
        $succMatch = array();
        if(!empty($score))
        {
            foreach ($score as $id => $val)
            {
                if($val['ascore'] === '' ||  $val['hscore'] === '')
                {
                    array_push($errId, $id);
                }
                else
                {
                    array_push($idArr, $id);
                }      
            }

            if(!empty($errId))
            {
                return $this->ajaxReturn('n', '比分录入信息不全，审核失败');
            }
            $errId = array();
            $idArr = array_unique($idArr);
            if(!empty($idArr))
            {
                // 查询比分信息
                $info = $this->Model_match->getjclqInfo($idArr);
                if(!empty($info))
                {
                    foreach ($info as $key => $items) 
                    {
                        $full_score = intval($score[$items['id']]['ascore']) . ':' . intval($score[$items['id']]['hscore']);

                        if($full_score == $items['full_score'])
                        {
                            array_push($succId, $items['mid']);
                            $succMatch[$items['m_date']][] = $items['mname'];
                        }
                        else
                        {
                            // 记录对比错误场次
                            array_push($errId, $items['mname']);
                        }
                    }
                }
            }
        }

        if(!empty($succId) && !empty($succMatch))
        {
            // 更新cfg status = 50
            $this->Model_match->updatejclqStatus($succId);
            // 更新审核状态 人工审核
            $this->Model_match->updateJclqAduitflag($succId, 1);
            // 触发过关
            $this->Model_match->updateTicketStop(1, 43, 0);
            // 记录日志
            $logs = "比分审核记录：竞彩篮球 ";
            foreach ($succMatch as $m_date => $mnames) 
            {
                $logs .= "第（". $m_date ."）期（" . implode(',', array_values($mnames)) . "） ";
            }
            $logs .= "比分审核通过";
            $this->syslog(18, $logs);

            if(!empty($errId))
            {
                return $this->ajaxReturn('y', implode(',', $errId) . '比分审核失败，请重新审核，其他场次均已录入成功');
            }
            else
            {
                return $this->ajaxReturn('y', '比分审核成功');
            }
        }
        else
        {
            return $this->ajaxReturn('n', '比分录入信息不全，审核失败');
        } 
    }

    // 场次延期 - 竞彩足球
    public function jczq_delay()
    {
        $env = $this->input->post('env');
        $this->checkenv($env, true);
        $this->check_capacity('7_3_8', true);
        $id = intval($this->input->post("delayId", true));
        $res = $this->Model_match->get_jczq($id);
        $synflag = 0;
        $row = $this->Model_match->updatePaiqiCstate('cp_jczq_paiqi', $id, 1, $synflag);
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        //触发比分同步任务
        $this->Model_match->updateTicketStop(6, 42, 0);
   
        $this->syslog(18, "竞彩足球 第（".$res['mid']."）期（".$res['mname']."）场延期比赛");
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    // 场次延期 - 竞彩篮球
    public function jclq_delay()
    {
        $env = $this->input->post('env');
        $this->checkenv($env, true);
        $this->check_capacity('7_3_8', true);
        $id = intval($this->input->post("delayId", true));
        $res = $this->Model_match->get_jclq($id);
        $synflag = 0;
        $row = $this->Model_match->updatePaiqiCstate('cp_jclq_paiqi', $id, 1, $synflag);
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        //触发比分同步任务
        $this->Model_match->updateTicketStop(6, 43, 0);
   
        $this->syslog(18, "竞彩篮球 第（".$res['mid']."）期（".$res['mname']."）场延期比赛");
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    // 取消延期 - 竞彩足球
    public function jczq_cancel_delay()
    {
        $env = $this->input->post('env');
        $this->checkenv($env, true);
        $this->check_capacity('7_3_8', true);
        $id = intval($this->input->post("cancelDelayId", true));
        $res = $this->Model_match->get_jczq($id);
        $synflag = 0;
        $row = $this->Model_match->cancelDelay('cp_jczq_paiqi', $id, 1, $synflag);
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        //触发比分同步任务
        $this->Model_match->updateTicketStop(6, 42, 0);
   
        $this->syslog(18, "竞彩足球 第（".$res['mid']."）期（".$res['mname']."）场取消延期比赛");
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    // 取消延期 - 竞彩篮球
    public function jclq_cancel_delay()
    {
        $env = $this->input->post('env');
        $this->checkenv($env, true);
        $this->check_capacity('7_3_8', true);
        $id = intval($this->input->post("cancelDelayId", true));
        $res = $this->Model_match->get_jczq($id);
        $synflag = 0;
        $row = $this->Model_match->cancelDelay('cp_jclq_paiqi', $id, 1, $synflag);
        if ($row === false)
        {
            return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
        }
        //触发比分同步任务
        $this->Model_match->updateTicketStop(6, 43, 0);
   
        $this->syslog(18, "竞彩篮球 第（".$res['mid']."）期（".$res['mname']."）场取消延期比赛");
        return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
}
