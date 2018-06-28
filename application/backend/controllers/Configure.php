<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：对阵管理
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configure extends MY_Controller
{
	private $kjhmType = array(
			'ssq' => '双色球',
			'dlt' => '大乐透',
			'qlc' => '七乐彩',
			'qxc' => '七星彩',
			'fc3d' => '福彩3D',
			'pl3' => '排列三',
			'pl5' => '排列五',
			'syxw' => '十一选五',
			'ks' => '上海快三',
            'jlks' => '吉林快三',
	        'jxks' => '江西快三',
			'jxsyxw' => '江西十一选五',
			'klpk' => '快乐扑克',
			'hbsyxw' => '湖北十一选五',
            'cqssc' => '老时时彩',
	        'gdsyxw' => '广东十一选五',
	);
	private $kjxqType = array(
			'rssq' => '双色球',
			'rdlt' => '大乐透',
			'rqlc' => '七乐彩',
			'rqxc' => '七星彩',
			'rfc3d' => '福彩3D',
			'rpl3' => '排列三',
			'rpl5' => '排列五',
			'dsfc' => '胜负彩/任选九',
			'dbqc' => '半全场',
			'djqc' => '进球彩',
	);
	private $bfType = array(
			'jczq' => '竞彩足球',
			'jclq' => '竞彩篮球',
			'bjdc' => '北京单场',
			'sfc'  => '胜负彩/任选九',
			'bqc'  => '半全场',
			'jqc'  => '进球彩',
			'sfgg' => '北单胜负过关'
	);
	private $lzcType = array(
			'rsfc'  => '胜负彩/任选九',
			'rbqc'  => '半全场',
			'rjqc'  => '进球彩',
	);
	private $dzzqType = array(
			'bjdc_sfgg'  => '北京单场-胜负过关',
			'bjdc_spf'  => '北京单场-胜平负',
			'bjdc_jqs'  => '北京单场-进球数',
			'bjdc_bqc'  => '北京单场-半全场',
			'bjdc_dss'  => '北京单场-单双数',
			'bjdc_dcbf'  => '北京单场-单场比分',
			'jclq_sf'  => '竞彩篮球-胜负',
			'jclq_rfsf'  => '竞彩篮球-让分胜负',
			'jclq_sfc'  => '竞彩篮球-胜分差',
			'jclq_dxf'  => '竞彩篮球-大小分',
			'jczq_spf'  => '竞彩足球-胜平负',
			'jczq_rqspf'  =>'竞彩足球-让球胜平负',
			'jczq_cbf'  => '竞彩足球-猜比分',
			'jczq_jqs'  => '竞彩足球-进球数',
			'jczq_bqc'  => '竞彩足球-半全场',
			'tczq_sfc'  => '胜负彩/任选九',
			'tczq_bqc'  => '半全场',
			'tczq_jqc'  => '进球彩',
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_configure');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：开奖号码配置列表
     * 修改日期：2015.04.09
     */
    public function index()
    {
    	$this->check_capacity('7_4_1');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 'ssq' : $ctype;
    	$searchData = array(
    		'ctype' => $ctype
    	);
    	$result = $this->Model_configure->getByCtype($ctype);
    	$infos = array(
    			"ctypes"	=> $this->kjhmType,
    			"search"	=> $searchData,
    			"result"	=> $result
    	);
        $this->load->view("configure/kjhm", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：开奖详情配置列表
     * 修改日期：2015.04.09
     */
    public function kjxq()
    {
        $this->check_capacity('7_4_2');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 'rssq' : $ctype;
    	$searchData = array(
    			'ctype' => $ctype
    	);
    	$result = $this->Model_configure->getByCtype($ctype);
    	$infos = array(
    			"ctypes"	=> $this->kjxqType,
    			"search"	=> $searchData,
    			"result"	=> $result,
    	);
    	$this->load->view("configure/kjxq", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：对阵抓取配置列表
     * 修改日期：2015.04.09
     */
    public function dzzq()
    {
        $this->check_capacity('7_4_3');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 'bjdc_sfgg' : $ctype;
    	$searchData = array(
    			'ctype' => $ctype
    	);
    	$result = $this->Model_configure->getByCtype($ctype);
    	$infos = array(
    			"ctypes"	=> $this->dzzqType,
    			"search"	=> $searchData,
    			"result"	=> $result,
    	);
    	$this->load->view("configure/dzzq", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：比分抓取配置列表
     * 修改日期：2015.04.09
     */
    public function bfzq()
    {
        $this->check_capacity('7_4_4');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 'jczq' : $ctype;
    	$searchData = array(
    			'ctype' => $ctype
    	);
    	$result = $this->Model_configure->getByCtype($ctype);
    	$infos = array(
    			"ctypes"	=> $this->bfType,
    			"search"	=> $searchData,
    			"result"	=> $result,
    	);
    	$this->load->view("configure/bfzq", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：老足彩赛果抓取配置列表
     * 修改日期：2015.04.09
     */
    public function lzcsg()
    {
        $this->check_capacity('7_4_5');
    	$ctype = $this->input->get("ctype", true);
    	$ctype = empty($ctype) ? 'rsfc' : $ctype;
    	$searchData = array(
    			'ctype' => $ctype
    	);
    	$result = $this->Model_configure->getByCtype($ctype);
    	$infos = array(
    			"ctypes"	=> $this->lzcType,
    			"search"	=> $searchData,
    			"result"	=> $result,
    	);
    	$this->load->view("configure/lzcsg", $infos);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：开奖号码配置修改
     * 修改日期：2015.04.09
     */
    public function update()
    {
        $this->check_capacity('7_4_6', true);
    	$postData = $this->input->post(null, true);
    	$id = intval($postData['updateId']);
    	$data['start'] = intval($postData['start']);
    	$data['compare'] = intval($postData['start']);
    	$data['cname'] = $postData['cname'];
    	$data['url'] = $postData['url'];//抓取配置的都在这里
        // 高频彩 网站和票商抓取二选一
        $params = explode("_", $postData['lname']);

        if(in_array($params[0], array('syxw', 'jxsyxw', 'hbsyxw', 'ks', 'klpk', 'cqssc', 'jlks', 'jxks', 'gdsyxw')))
        {
            // 设置cp_crontab_config
            //$row = $this->handleCrontab($params[0], $params[1], $data['start']);
            // 重置start = 0
            $this->Model_configure->removeConfigure($params[0]);  
            $this->Model_configure->update($id, $data); 
        }
        else
        {
            if($postData['names_'] =='开奖号码')
            {
                $startNumFlag = $this->Model_configure->getStartNum($postData['type_']);
                if($startNumFlag<=2 && $data['start'] ==0)
                {
                    return $this->ajaxReturn('n', $this->msg_text_cfg['startNumFlag']);
                }else{
                    $row = $this->Model_configure->update($id, $data);
                }
            }else{
                $row = $this->Model_configure->update($id, $data);
            }
            
        }
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
        switch(trim($postData['type_']))
        {
            case 'ssq':
                $type = "双色球";
                break;
            case 'dlt':
                $type = "大乐透";
                break;
            case 'qlc':
                $type = "七乐彩";
                break;
            case 'qxc':
                $type = "七星彩";
                break;
            case 'fc3d':
                $type = "福彩3D";
                break;
            case 'pl3':
                $type = "排列三";
                break;
            case 'pl5':
                $type = "排列五";
                break;
            case 'syxw':
                $type = "十一选五";
                break;
            case 'rssq':
                $type = "双色球";
                break;
            case 'rdlt':
                $type = "大乐透";
                break;
            case 'rqlc':
                $type = "七乐彩";
                break;
            case 'rqxc':
                $type = "七星彩";
                break;
            case 'rfc3d':
                $type = "福彩3D";
                break;
            case 'rpl3':
                $type = "排列三";
                break;
            case 'rpl5':
                $type = "排列五";
                break;
            case 'rsyxw':
                $type = "十一选五";
                break;
            case 'bjdc_sfgg':
                $type = "北京单场-胜负过关";
                break;
            case 'bjdc_spf':
                $type = "北京单场-胜平负";
                break;
            case 'bjdc_jqs':
                $type = "北京单场-进球数";
                break;
            case 'bjdc_bqc':
                $type = "北京单场-半全场";
                break;
            case 'bjdc_dss':
                $type = "北京单场-单双数";
                break;
            case 'bjdc_dcbf':
                $type = "北京单场-单场比分";
                break;
            case 'jclq_sf':
                $type = "北京单场-胜负";
                break;
            case 'jclq_rfsf':
                $type = "竞彩篮球-让分胜负";
                break;
            case 'jclq_sfc':
                $type = "竞彩篮球-胜分差";
                break;
            case 'jclq_dxf':
                $type = "竞彩篮球-大小分";
                break;
            case 'jczq_spf':
                $type = "竞彩足球-胜平负";
                break;
            case 'jczq_rqspf':
                $type = "竞彩足球-让球胜平负";
                break;
            case 'jczq_cbf':
                $type = "竞彩足球-猜比分";
                break;
            case 'jczq_jqs':
                $type = "竞彩足球-进球数";
                break;
            case 'jczq_bqc':
                $type = "竞彩足球-半全场";
                break;
            case 'tczq_sfc':
                $type = "胜负彩/任选九";
                break;
            case 'tczq_bqc':
                $type = "半全场";
                break;
            case 'tczq_jqc':
                $type = "进球彩";
                break;
            case 'jczq':
                $type = "竞彩足球";
                break;
            case 'jclq':
                $type = "竞彩篮球";
                break;
            case 'bjdc':
                $type = "北京单场";
                break;
            case 'sfc':
                $type = "胜负彩/任选九";
                break;
            case 'bqc':
                $type = "半全场";
                break;
            case 'jqc':
                $type = "进球彩";
                break;
            case 'sfgg':
                $type = "北单胜负过关";
                break;
            case 'rsfc':
                $type = "胜负彩/任选九";
                break;
            case 'rbqc':
                $type = "半全场";
                break;
            case 'rjqc':
                $type = "进球彩";
                break;
            case 'jxsyxw':
            	$type = "新11选5";
            	break;
            case 'hbsyxw':
            	$type = "惊喜11选5";
            	break;
            case 'gdsyxw':
                $type = "乐11选5";
                break;
            case 'ks':
            	$type = "上海快三";
            	break;
            case 'jlks':
                $type = "吉林快三";
                break;
            case 'jxks':
                $type = "江西快三";
                break;
            case 'klpk':
            	$type = "快乐扑克";
            	break;
            case 'cqssc':
                $type = "老时时彩";
                break;
        }
        $data['start'] = ($data['start'] == 1 ? "开启" : "关闭");
        $this->syslog(19, "（".$postData['names_']."）".$type."抓取来源:".$data['cname'].",抓取配置:".$data['url']."，状态为:".$data['start'] );
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    public function handleCrontab($ctype, $seller, $start)
    {
        // seller 可能是抓取网站
        $row = false;
        $info = $this->Model_configure->getCrontabs();

        $this->load->library('BetCnName');
        $BetCnName = new BetCnName ();
        $lidMaps = $BetCnName->getLotteryEgName();
        $lidMaps = array_flip($lidMaps);
        $lid = $lidMaps[$ctype];

        $config = array();
        if(!empty($info))
        {
            foreach ($info as $key => $items) 
            {
                $cnames = explode('-', $items['cname']);
                $cnameArr = explode('@', $items['cname']);
                $myparams = explode('-', $cnameArr[1]);
                $lids = explode('_', $myparams[1]);
                $nolids = array();
                // 排除指定lid的 myparams 配置
                if(!empty($lids))
                {
                    foreach ($lids as $k => $lotteryId) 
                    {
                        if($lotteryId != $lid)
                        {
                            array_push($nolids, $lotteryId);
                        }
                    }
                }
                $data = array(
                    'id' => $items['id'],
                    'lids' => $lids,
                    'cname' => $cnames[0] . '-' . implode('_', $nolids),
                );
                $config[$myparams[0]] = $data;
            }
        }

        if(!empty($config))
        {
            if(in_array($seller, array('qihui', 'caidou', 'huayang', 'hengju')) && in_array($lid, $config[$seller]['lids']) && $start)
            {
                // 已存在
                $row = true;
            }
            else
            {
                if(in_array($seller, array('qihui', 'caidou', 'huayang', 'hengju')) && $start)
                {
                    $checkArr = explode('-', $config[$seller]['cname']);
                    $split = $checkArr[1] ? '_' : '';
                    $config[$seller]['cname'] .= $split . $lid;
                }
                // 更新 cfg库 crontab_config
                foreach ($config as $seller => $items) 
                {
                    $data = array(
                        'cname' => $items['cname'],
                    );
                    $this->Model_configure->updateCrontab($items['id'], $data);
                }
                $row = true;
            }
        }
        return $row;     
    }
    
    public function jlks($lid = 56)
    {
        $this->check_capacity('7_4_7');
        $lidArr = array(
            '56' => array('ename' => 'jlks', 'cname' => '吉林快三'), 
            '57' => array('ename' => 'jxks', 'cname' => '江西快三')
        );
        $result = $this->Model_configure->getJlksIssue($lidArr[$lid]['ename']."_issue");
        $this->load->view("configure/jlks", compact('result', 'lid', 'lidArr'));
    }
    
    public function updateJlks($lid = 56)
    {
        $this->check_capacity('7_4_8', true);
        $postData = $this->input->post(null, true);
        $lidArr = array(
            '56' => array('ename' => 'jlks', 'cname' => '吉林快三'),
            '57' => array('ename' => 'jxks', 'cname' => '江西快三')
        );
        $res = $this->Model_configure->updateJlksIssue($postData, $lidArr[$lid]['ename']."_issue");
        if ($res['status'] != 200) {
            return $this->ajaxReturn('n', $res['msg']);
        } else {
            if ($postData['cname'] == '恒巨') {
                $this->syslog(63, "（快3期次更新）".$lidArr[$lid]['cname']."抓取来源:恒巨，状态为:".($postData['start']==1?"开启":"关闭"));
                $this->syslog(63, "（快3期次更新）".$lidArr[$lid]['cname']."抓取来源:华阳，状态为:".($postData['start']==0?"开启":"关闭"));
            }else{
                $this->syslog(63, "（快3期次更新）".$lidArr[$lid]['cname']."抓取来源:恒巨，状态为:".($postData['start']==0?"开启":"关闭"));
                $this->syslog(63, "（快3期次更新）".$lidArr[$lid]['cname']."抓取来源:华阳，状态为:".($postData['start']==1?"开启":"关闭"));
            }
            return $this->ajaxReturn('y', $res['msg']);
        }
    }
}
