<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：帐号及权限管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.07
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Warning extends MY_Controller
{
    private $_lidMap = array(
           '21406' => '山东11选5',
           '21407' => '江西11选5',
           '21408' => '湖北11选5',
           '53' => '上海快三',
           '56' => '吉林快三',
	       '57' => '江西快三',
           '54' => '快乐扑克',
           '55' => '重庆时时彩',
	       '21421' => '广东11选5',
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_warning');
        $this->config->load('msg_text');
        $this->config->load('caipiao');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
        $this->caipiao = $this->config->item('caipiao_all_cfg');
        $this->payTypeName = array(
        		'llpayWeb' => '连连快捷',
        		//'llpaySdk' => '连连SDK',
        		'payWeix'     =>  '中信微信',
        		'sumpayWap'   =>  '统统付Wap',
        		'sumpayWeb'   =>  '统统付快捷',
        		//'yeepay'      =>  '易宝',
        		'yeepayCredit'=>  '易宝信用卡',
        		'yeepayKuaij' =>  '易宝快捷',
        		'yeepayMPay'  =>  '易宝Wap',
        		'yeepayWangy' =>  '易宝网银',
        		'yeepayWeix'  =>  '易宝微信',
        		'zxwxSdk'     =>  '中信微信SDK',
        		'payZfb'      =>  '全付通支付宝',
        		'wftWxSdk'    =>  '全付通微信SDK',
        		'wftWx'       =>  '全付通微信PC',
        		'jdPay'       =>  '京东支付',
        		'umPay'       =>  '联动快捷',
        		'xzZfbWap'    =>  '现在支付宝H5',
        		'hjZfbPay'    =>  '汇聚无限支付宝',
        		'wftZfbWap'   =>  '兴业支付宝H5',
                        'wftWxWap'    =>  '鸿粤兴业银行微信H5',
        		'xzpay'       =>  '现在支付宝H5',
        		'wftpay'      =>  '兴业支付宝H5',
        		'hjpay'       =>  '汇聚无限支付宝',
        		'wzPay'       =>  '微众银行支付宝',
        		'hjWxWap'     =>  '微信H5-兴业银行',
        		'hjZfbWap'    =>  '支付宝H5-鸿粤浦发银行',
                'pfWxWap'     =>  '浦发白名单',
                'payXmZfb'    =>  '厦门银行支付宝',
                'payPaZfb'    =>  '平安银行支付宝',
                'payYlyZf'    =>  '银联云支付',
                'yzpayh'      =>  '盈中平安银行',
                'tomatoZfbWap'=> '番茄支付宝h5',
                'ulineWxWap'=> '上海银行微信h5',
                'yzWxWap'     =>  '微信H5-盈中平安银行渠道',
                'hjZfbSh'     => '支付宝H5-上海银行',
                'jdSdk'       =>  '京东支付SDK',
                'wftwxzx'     => '微信扫码-长沙中信银行渠道',
                'wftzfbzx'    => '支付宝扫码-长沙中信银行渠道',
                'tomatoWxWap' => '番茄微信h5',
        );
    }
    
    public function index()
    {
    	$this->check_capacity('8_3_1');
        $result = $this->Model_warning->getWarningConfig();
        
        $this->load->view('warningConfig', array(
            'configs' => $result,
        	'caipiao' => $this->caipiao['caipiao_cfg'],
        	'payTypeName' => $this->payTypeName,
            'lidMap'    => $this->_lidMap,
        ));
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：修改报警配置信息
     * 修改日期：2016.03.14
     */
    public function update()
    {
        $this->check_capacity('8_3_2', true);
    	$postData = $this->input->post(null, true);
    	$ctype = intval($postData['updateId']);
    	$data['phone'] = $postData['phone'];
    	$data['email'] = $postData['email'];
    	$data['sendType'] = intval($postData['sendType']);
    	$data['stop'] = intval($postData['stop']);
    	$data['otherCondition'] = '';
    	switch ($postData['updateId']) {
    	    case 10:
    	       if ($postData['lid'] && is_array($postData['lid'])) $data['otherCondition'] = json_encode(array('lid' => $postData['lid']));
    	       break;
    	    case 9:
    	        $otherCondition = array();
    	        foreach ($this->_lidMap as $lid => $cname) {
    	            if (array_key_exists($lid, $postData)) {
        				if (empty($postData[$lid]['stime']) || empty($postData[$lid]['etime'])) return $this->ajaxReturn('n', '请输入时间');
        				$otherCondition['stoptime'][$lid] = array($postData[$lid]['stime'], $postData[$lid]['etime']);
    	            }
    	        }
    	        for ($i = 0; $i <= 7; $i++) {
    	            if (empty($postData['notickettime'][$i])) return $this->ajaxReturn('n', '请输入未出票报警时间');
    	            $otherCondition['notickettime'][$i] = $postData['notickettime'][$i];
    	        }
    	        $data['otherCondition'] = '';
    	        if (!empty($otherCondition)) $data['otherCondition'] = json_encode($otherCondition);
    	        break;
    	    case 19:
    	        if (!empty($postData['payType'])) $data['otherCondition'] = json_encode(array('payType' => $postData['payType']));
    	        break;
    	}
        $name = $this->Model_warning->getAlertName($ctype);
    	$row = $this->Model_warning->update($ctype, $data);
    	if ($row === false)
    	{
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	$stop = $data['stop'] ? '停止' : '开启';
    	$syslogstr = "修改{$name} ：配置信息为 phone:".$data['phone'].",email:".$data['email'].",状态为：".$stop;
    	if ($postData['updateId'] == 10 && $postData['lid']) {
    		$tmpArr = array();
    		foreach ($postData['lid'] as $lid) {
    			array_push($tmpArr, $this->caipiao['caipiao_cfg'][$lid]['name']);
    		}
    		$syslogstr .= "，彩种为：".implode(',', $tmpArr);
    	} 
    	$this->syslog(30, $syslogstr);
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }
}
