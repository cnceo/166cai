<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Safe extends MY_Controller {

    private $bankTypeList = array(
        '1025' => '中国工商银行',
        '3080' => '招商银行',
        '105' => '中国建设银行',
        '103' => '中国农业银行',
        '104' => '中国银行',
        '301' => '交通银行',
        '307' => '平安银行',
        '309' => '兴业银行',
        '311' => '华夏银行',
        '305' => '中国民生银行',
        '306' => '广东发展银行',
        '314' => '上海浦东发展银行',
        '313' => '中信银行',
        '312' => '光大银行',
        '316' => '南京银行',
        '326' => '上海银行',
        '3230' => '中国邮政储蓄银行',
    );

    public function __construct() {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('safe_model');
        $this->load->model('user_model');
        $this->load->model('newuser_model');
        $this->load->model('Notice_Model');
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $vdata['bankTypeList'] = $this->bankTypeList;
        $vdata['rfshbind'] = 1;
        $vdata['htype'] = 1;
        $this->display('safe/index', $vdata, 'v1.1');
    }

    // 基本信息
    public function baseinfo() {
        $vdata['uname'] = $this->uname;

        $vdata['gender'] = $this->uinfo['gender'];
        $vdata['province'] = $this->uinfo['province'];
        $vdata['city'] = $this->uinfo['city'];
        $vdata['qq'] = $this->uinfo['qq'];

        $vdata['provinceList'] = $this->safe_model->getProvince();
        if (!empty($vdata['province'])) {
            $vdata['cityList'] = $this->getCity($vdata['province']);
        }

        if ($this->is_ajax) {
            $newData = array('uid' => $this->uid);
            $newData['province'] = $this->input->post('province', true);
            $newData['city'] = $this->input->post('city', true);
            $newData['gender'] = intval($this->input->post('gender', true));
            $newData['qq'] = $this->input->post('qq', true);
            $return = $this->newuser_model->saveUserBase($newData);
            echo $return['status'];
            exit;
        }
        $vdata['rfshbind'] = 1;
        $vdata['htype'] = 1;
        // 记录字段 
        $this->display('safe/baseinfo', $vdata, 'v1.1');
    }

    /*
     * 绑定银行卡 -- 用户中心
     * @author:liuli
     * @date:2015-03-03
     */
    public function bankcard() {
        
        if (empty($this->uinfo))
        {
            $this->redirect('/safe');
        }
        //Ajax处理
        if ($this->is_ajax) {
            $vdata = $this->input->post(null, true);
            $res = 0;
            switch ($vdata['action']) {
                case '_add':
                    if( $this->uinfo['phone'] && $this->uinfo['id_card'])
                    {
                        //添加银行卡 验证
                        if(!empty($this->bankInfo) && count($this->bankInfo)>=5)
                        {
                            $res = 2;
                        }else{
                            $res = 1;
                            $uid = $this->uid;
                            $isbind = $this->primarysession->getArg($uid.'isbind');
                            if(!empty($isbind)){
                                $vdata['action'] = '_sadd';
                            }else{
                                $vdata['action'] = '_add';
                            }                       
                            $vdata['provinceList'] = $this->safe_model->getProvince();
                            $vdata['bankTypeList'] = $this->bankTypeList;
                            $vdata['isNeedShowBindId'] = true;
                            $vdata['isNeedShowBindIdNoPhone'] = false;            
                        }
                    }else{
                        $res = 3;
                    }
                    
                    break;
                case '_2':
                    //身份证入库
                    $this->load->library('BankCard');
                    //$data['uid'] = $this->uid;
                    $vdata['bank_province'] = $this->input->post('province', true);
                    $vdata['bank_city'] = $this->input->post('city', true);
                    $vdata['bank_type'] = $this->input->post('bank_type', true);
                    $vdata['bank_id'] = trim($this->input->post('bank_id', true));
                    //银行卡列表
                    $bankList = $this->bankTypeList;
                    //验证提交信息
                    if (empty($vdata['bank_province']) || empty($vdata['bank_city'])) {
                        $res = 2; // 开户地区为空
                    } elseif (empty($vdata['bank_type'])) {
                        $res = 3; // 开户行为空
                    } elseif (!BankCard::checkBankCard($vdata['bank_id'])) {
                        $res = 4; // 卡号不符合规则
                    } elseif (empty($bankList[$vdata['bank_type']])){
                        $res = 5; // 卡类型错误
                    } elseif ($this->checkAlredyCard(trim($vdata['bank_id']))){
                        $res = 6; // 已存在
                    } else {
                    	$data = array();
                    	$data['uid'] = $this->uid;
                    	$data['bank_province'] = $vdata['bank_province'];
                    	$data['bank_city'] = $vdata['bank_city'];
                    	$data['bank_type'] = $vdata['bank_type'];
                    	$data['bank_id'] = trim($vdata['bank_id']);
                    	$result = $this->newuser_model->insertBank($data);
                    	if($result['status']){
                    		$res = 1;
                    		$vdata['action'] = '_add_2';
                    	}                      
                    }
                    break;
                case '_3':
                    //设置默认提现卡
                    $data = array();
                    $data['uid'] = $this->uid;
                    $data['id'] = $vdata['id']?$vdata['id']:0;
                    $result = $this->newuser_model->setDefaultBank($data);
                    if($result['status'])
                    {
                        $res = 2;
                    }else{
                        $res = 3;
                    }
                    break;
                case '_4':
                    //删除提现卡
                    $data = array();
                    $data['uid'] = $this->uid;
                    $data['id'] = $vdata['id']?$vdata['id']:0;
                    $result = $this->newuser_model->delBank($data);
                    if($result['status'])
                    {
                        $res = 2;
                    }else{
                        $res = 3;
                    }
                    break;
                case '_modify':
                    //修改提现卡展示
                    $info = array();
                    $info['id'] = $vdata['id']?$vdata['id']:0;
                    $info['uid'] = $this->uid;
                    //获取银行卡详情
                    $binfo = $this->user_model->getBankDetail($info);
                    if(empty($binfo))
                    {
                        $res = 2;  //获取失败，刷新页面
                    }else{
                        $vdata['binfo'] = $binfo;
                        $vdata['provinceList'] = $this->safe_model->getProvince();
                        $vdata['bankTypeList'] = $this->bankTypeList;
                        $res = 1;
                    }
                    break;
                case '_5':
                    //修改提现卡信息 入库
                    $this->load->library('BankCard');
                    //$vdata['uid'] = $this->uid;
                    $vdata['id'] = $this->input->post('id', true);
                    $vdata['bank_province'] = $this->input->post('province', true);
                    $vdata['bank_city'] = $this->input->post('city', true);
                    $vdata['bank_type'] = $this->input->post('bank_type', true);
                    $vdata['bank_id'] = $this->input->post('bank_id', true);
                    //银行卡列表
                    $bankList = $this->bankTypeList;
                    //验证提交信息
                    if (empty($vdata['bank_province']) || empty($vdata['bank_city'])) {
                        $res = 2; // 开户地区为空
                    } elseif (empty($vdata['bank_type'])) {
                        $res = 3; // 开户行为空
                    } elseif (!BankCard::checkBankCard($vdata['bank_id'])) {
                        $res = 4; // 卡号不符合规则
                    } elseif (empty($bankList[$vdata['bank_type']])){
                        $res = 5; // 卡类型错误
                    } 
                    else 
                    {
                    	$data = array();
                    	$data['uid'] = $this->uid;
                    	$data['id'] = trim($vdata['id']);
                    	$data['bank_province'] = $vdata['bank_province'];
                    	$data['bank_city'] = $vdata['bank_city'];
                    	$data['bank_type'] = $vdata['bank_type'];
                    	$data['bank_id'] = $vdata['bank_id'];
                    	$result = $this->newuser_model->updateBank($data);
                    	if($result['status']){
                    		$res = 1;
                    		$vdata['action'] = '_modify_2';
                    	}
                    	else
                    	{
                    		$res = 6;
                    	}                    
                    }
                    break;
                case '_6': //验证手机号操作已废弃
                    //添加银行卡入库
                    //银行卡信息
                    $this->load->library('BankCard');
                    $bankList = $this->bankTypeList;
                    //验证提交信息
                    if (empty($vdata['bank_province']) || empty($vdata['bank_city'])) {
                        $res = 2; // 开户地区为空
                    } elseif (empty($vdata['bank_type'])) {
                        $res = 3; // 开户行为空
                    } elseif (!BankCard::checkBankCard($vdata['bank_id'])) {
                        $res = 4; // 卡号不符合规则
                    } elseif (empty($bankList[$vdata['bank_type']])){
                        $res = 5; // 卡类型错误
                    } elseif ($this->checkAlredyCard(trim($vdata['bank_id']))){
                        $res = 6; // 已存在
                    } elseif ($res = $this->checkCaptcha($vdata['captcha'], $this->uinfo['phone'], 'phoneCaptcha'))
                    {
                    	if ($res === 2) {
                    		$res = 71;
                    	}else {
                    		$res = 7;
                    	}
                    } else {
                        $data = array();
                        $data['uid'] = $this->uid;
                        $data['bank_province'] = $vdata['bank_province'];
                        $data['bank_city'] = $vdata['bank_city'];
                        $data['bank_type'] = $vdata['bank_type'];
                        $data['bank_id'] = trim($vdata['bank_id']);
                        $result = $this->newuser_model->insertBank($data);
                        if($result['status']){
                            $res = 1;
                            $vdata['action'] = '_add_2';
                        }  
                    }                 
                    break;
                case '_7': //验证手机号操作已废弃
                    //修改提现卡信息 入库
                    $this->load->library('BankCard');
                    //验证码信息
                    //$vdata['uid'] = $this->uid;
                    $vdata['id'] = $this->input->post('id', true);
                    $vdata['bank_province'] = $this->input->post('bank_province', true);
                    $vdata['bank_city'] = $this->input->post('bank_city', true);
                    $vdata['bank_type'] = $this->input->post('bank_type', true);
                    $vdata['bank_id'] = $this->input->post('bank_id', true);
                    //银行卡列表
                    $bankList = $this->bankTypeList;
                    //验证提交信息
                    if (empty($vdata['bank_province']) || empty($vdata['bank_city'])) {
                        $res = 2; // 开户地区为空
                    } elseif (empty($vdata['bank_type'])) {
                        $res = 3; // 开户行为空
                    } elseif (!BankCard::checkBankCard($vdata['bank_id'])) {
                        $res = 4; // 卡号不符合规则
                    } elseif (empty($bankList[$vdata['bank_type']])){
                        $res = 5; // 卡类型错误
                    } elseif ($res = $this->checkCaptcha($vdata['captcha'], $this->uinfo['phone'], 'phoneCaptcha')){
                    	if ($res === 2) {
                    		$res = 71;
                    	}else {
                    		$res = 7;
                    	}
                    } else {
                        $data = array();
                        $data['uid'] = $this->uid;
                        $data['id'] = trim($vdata['id']);
                        $data['bank_province'] = $vdata['bank_province'];
                        $data['bank_city'] = $vdata['bank_city'];
                        $data['bank_type'] = $vdata['bank_type'];
                        $data['bank_id'] = $vdata['bank_id'];
                        $result = $this->newuser_model->updateBank($data);
                        if($result['status']){
                            $res = 1;
                            $vdata['action'] = '_modify_2';
                        }                        
                    }                    
                    break;
                case '_8':
                    //身份证入库
                    $this->load->library('BankCard');
                    //$data['uid'] = $this->uid;
                    $vdata['bank_province'] = $this->input->post('province', true);
                    $vdata['bank_city'] = $this->input->post('city', true);
                    $vdata['bank_type'] = $this->input->post('bank_type', true);
                    $vdata['bank_id'] = trim($this->input->post('bank_id', true));
                    //银行卡列表
                    $bankList = $this->bankTypeList;
                    //验证提交信息
                    if (empty($vdata['bank_province']) || empty($vdata['bank_city'])) {
                        $res = 2; // 开户地区为空
                    } elseif (empty($vdata['bank_type'])) {
                        $res = 3; // 开户行为空
                    } elseif (!BankCard::checkBankCard($vdata['bank_id'])) {
                        $res = 4; // 卡号不符合规则
                    } elseif (empty($bankList[$vdata['bank_type']])){
                        $res = 5; // 卡类型错误
                    } elseif ($this->checkAlredyCard(trim($vdata['bank_id']))){
                        $res = 6; // 已存在
                    } else {
                        //提交内容带入下一步页面
                        $data = array();
                        $data['uid'] = $this->uid;
                        $data['bank_province'] = $vdata['bank_province'];
                        $data['bank_city'] = $vdata['bank_city'];
                        $data['bank_type'] = $vdata['bank_type'];
                        $data['bank_id'] = trim($vdata['bank_id']);
                        $result = $this->newuser_model->insertBank($data);
                        if($result['status']){
                            $res = 1;
                            $vdata['action'] = '_sadd_1';
                        }                      
                    }
                    break;                       
                default:
                    # code...
                    break;
            }

            if($res == 1){
                echo $this->load->view("v1.1/safe/bankcard".$vdata['action'], $vdata, true); 
            }else{
                echo $res;
            }
            exit;
        }
        //获取银行卡绑定信息

        $vdata['bankInfo'] = $this->bankInfo;
        $vdata['rfshbind'] = 1;
        $vdata['htype'] = 1;
        $this->display('safe/bankcard', $vdata, 'v1.1');
    }

    public function checkAlredyCard($bank_id) 
    {       
        $res = FALSE;
        $bankInfo = $this->bankInfo;
        if(!empty($bankInfo))
        {
            $banks = array();
            foreach ($bankInfo as $bank) 
            {
                array_push($banks, $bank['bank_id']);
            }
            if(in_array($bank_id, $banks,true)){
                $res = TRUE;
            }
        }
        return $res;
    }

    public function getCityList(){
        $province = $this->input->post('province', true);
        echo $this->getCity( $province );
    }

    // 获得的城市pages
    private function getCity($province) {
        $vdata['cityList'] = $this->safe_model->getCityListByProvince($province);
        return $this->load->view('elements/common/fragment', $vdata, true);
    }

    //找回密码
    public function findPword()
    {
    	if($this->is_ajax)
    	{
    		$res = array(
    			'status' => '001',
    			'msg' => '系统异常',
    		);
    		$vdata = $this->input->post(null, true);

    		//根据不同请求展开
    		if($vdata['actiontype'])
    		{
    			switch ($vdata['actiontype']) 
    			{
    				case '_1':
    					//验证第一步
    					$res = $this->checkPawOne($vdata);
    					break;
    				case '_2':
    					//绑定信息验证
    					$res = $this->checkPawTwo($vdata);
    					break;
    				case '_3':
    					//确认新密码
    					$res = $this->checkPawThree($vdata);
    				default:
    					break;
    			}
    		}
    		//验证通过
    		if($res['status'] == '100')
    		{
    		    $this->primarysession->setArg('findphone', $vdata['findpwd']);
    			$data['phone'] = $vdata['findpwd'];
    			$res['status'] = '000';
    			$this->displayBanner('v1.1', $data, 'ycfc');
    			$res['data'] = $this->displayAjaxLess("v1.1/safe/findPword{$vdata['actiontype']}", $data);
    			$this->ajaxResult($res);
    		}
    		elseif($res['status'] == '200')
    		{
    			$res['status'] = '000';
                $vdata['phone'] = $res['phone'];
                $this->displayBanner('v1.1', $vdata, 'ycfc');
    			$res['data'] = $this->displayAjaxLess("v1.1/safe/findPword{$vdata['actiontype']}", $vdata);
    			$this->ajaxResult($res);
    		}
    		elseif($res['status'] == '300')
    		{
    			$res['status'] = '000';
    			$res['msg'] = '操作成功';
    			$vdata['uname'] = $res['data']['uname'];
    			$this->displayBanner('v1.1', $vdata, 'ycfc');
    			$res['data'] = $this->displayAjaxLess("v1.1/safe/findPword{$vdata['actiontype']}", $vdata);
    			$this->ajaxResult($res);
    		}
    		else
    		{
    			$this->ajaxResult($res);
    		}
    	}
    	else
    	{
    		if($this->uid)
    		{
    			$this->redirect('/');
    		}
    		$vdata = $this->input->post(null, true);
    		$data['current'] = $vdata['current'] ? $vdata['current'] : 1;
    		$data['headTitle'] = '找回密码';
    		$this->displayShort('safe/findPword', $data, 'v1.1');
    	}
    }
    
    /**
     * 重置密码第一步
     * @param unknown_type $vdata
     */
    private function checkPawOne($vdata)
    {
    	//获取验证码信息
    	$this->load->model('user_model');
    	$user = $this->user_model->isPhoneRepeat($vdata['findpwd']);
    	if(empty($vdata['findpwd']) || !$user){
    		$res = array(
    				'status' => '003',
    				'msg' => '手机号不存在',
    		);
    		return $res;
    	}
    	$res = array(
    			'status' => '100',
    			'msg' => '验证手机号通过',
    	);
    	return $res;
    }
    
    /**
     * 重置密码第二步
     * @param unknown_type $vdata
     */
    private function checkPawTwo($vdata)
    {
        $phone = $this->primarysession->getArg('findphone');

    	$codestr = $this->primarysession->getArg('findPwdCaptcha');
        $codestr = explode(':', $codestr);
        
        $res = $this->checkCaptcha($vdata['phoneCaptcha'], $phone, 'findPwdCaptcha', false);
        if ($res === 2) {
        	$res = array(
        			'status' => '004',
        			'msg' => '验证码不正确',
        	);
        }elseif ($res) {
        	$res = array(
        			'status' => '002',
        			'msg' => '验证码不正确',
        	);
        }else {
        	$res = array(
        			'status' => '200',
        			'msg' => '手机验证码通过',
        			'phone' => $phone
        	);
        }
        return $res;
    }
    
    /**
     * 重置密码第三步
     * @param unknown_type $vdata
     * @return multitype:string
     */
    private function checkPawThree($vdata)
    {
    	if( empty($vdata['pword']) || empty($vdata['con_pword']) || $vdata['pword'] != $vdata['con_pword'] ){
    		$res = array(
    			'status' => '004',
    			'msg' => '两次密码不匹配',
    			'data' => array(),
    		);
    		return $res;
    	}

        $phone = $this->primarysession->getArg('findphone');

        // 检查第二步
        $checkData = array('phoneCaptcha' => $vdata['phoneCaptcha']);
        $checkRes = $this->checkPawTwo($checkData);

        if($checkRes['status'] != '200')
        {   
            $res = array(
                'status' => '004',
                'msg' => '验证已失效',
                'data' => array()
            );
            return $res;
        }

    	$this->load->model('user_model');
    	$uid = $this->user_model->getUid($phone);
    	if($uid)
    	{
    		$userInfo = $this->user_model->getUserInfo($uid);
    		$user['uid'] = $uid;
                $user['salt'] = substr(uniqid(), 0, 9);
                $newpword = md5($vdata['pword']) . $user['salt'];
                $user['pword'] = strCode($newpword, 'ENCODE');
                $user['last_login_time'] = date('Y-m-d H:i:s');
    		$user['visit_times'] = 1;
    		$result = $this->user_model->SaveUser($user);
    		if($result)
    		{
    			$this->primarysession->setArg('findPwdCaptcha', '');
    			$res['status'] = '300';
    			$res['msg'] = '操作成功';
    			$res['data'] = $userInfo;
    			return $res;
    		}
    	}
    	
    	$res = array(
    		'status' => '001',
    		'msg' => '修改失败',
    		'data' => array(),
    	);
    	return $res;
    }
    
    /**
     * 打印json数据，并终止程序
     * @param array $result
     */
    private function ajaxResult($result)
    {
    	header('Content-type: application/json');
    	die(json_encode($result));
    }

    /*
     * 修改密码 -- 用户中心
     * @author:liuli
     * @date:2015-01-07
     */
    public function update_password(){
    	$this->redirect('/');
        if($this->is_ajax){
            $res = array(
                'status' => '001', 
                'msg' => '系统异常',
                'data' => '', 
            );
            $vdata = $this->input->post(null, true);
            //根据不同请求展开
            if($vdata['actiontype']){
                switch ($vdata['actiontype']) {
                    case '_1':
                        //验证用户身份
                        $res = $this->validateUserInfo($vdata);
                        break;
                    case '_2':
                        //绑定信息验证
                        $res = $this->updatePassWord($vdata);
                        break;
                    default:
                        break;
                }
            }
            //验证通过
            if($res['status'] == '000'){
                echo $this->displayAjaxLess("safe/update_pwd{$vdata['actiontype']}", $res['data']);
            }else{
                echo $res['status'];
            }      
            exit;
        }
        if(empty($this->uid)){
            $this->redirect('/');
            exit;
        }
        //保存用户信息
        $this->primarysession->setArg('baseInfo',$this->uinfo);  
        if($this->uinfo['email']){
            $data['hide_email'] = $this->hideEmail($this->uinfo['email'],1);
        }
        $this->display('safe/update_pwd',$data);
    }

    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：完善个人信息
     * 修改日期：2015-11-25
     */
    public function userInfo()
    {
    	if(empty($this->uid))
    	{
    		$this->redirect('/main/login');
    	}
    	$vdata = $this->input->post(null, true);
    	$view = $this->uinfo['id_card'] ? 'userInfo1' : 'userInfo';
    	if ($this->is_ajax)
    	{
    		if($this->uinfo['real_name'] || $this->uinfo['id_card']) die('5');
    		if(empty($vdata['real_name'])) die('1');
    		//绑定身份信息
    		if(isset($vdata['id_card'])) {
    			$this->load->library('BindIdCard');
    			$this->bindidcard->pcsetIdCardInfo($vdata, array('uid' => $this->uid, 'phone' => $this->uinfo['phone']));
    		}
    		die('0');
    	}
    	else
    	{
    		$this->load->model('red_pack_model');
    		$vdata['hasRedpack'] = $this->red_pack_model->hasRedpack($this->uid, 1);
    	}
    	$this->displayShortHeader('/safe/' . $view, $vdata, 'v1.1');
    }
    
    /**
     * 参    数：ctype,字符型,验证码session键名
     * 作    者：shigx
     * 功    能：发送短信验证码操作
     * 修改日期：2015-11-25
     */
    public function sendSmsCode($ctype='captcha')
    {
    	$phone = $this->input->post('phone', true);
    	$pImgCode = $this->input->post('code', true);
    	$position = $this->input->post('position', true);
    	if(empty($pImgCode) || $this->primarysession->getArg('captcha') != strtolower($pImgCode))
    	{
    		echo json_encode(array('status' => false, 'msg' => '请输入正确的验证码'));
    		return ;
    	}
    	if (!empty($phone))
    	{
    		$code = $this->getSmsCode($phone, $position);
    		if (!empty($code))
    		{
    			$out_time = $this->config->item('OUTTIME');
    			$time = time();
    			$expire = $time + 60 * $out_time['captche'];
    			$second = $time + 60;
    			$codestr = "{$code}:$expire:$second";
    			$this->primarysession->setArg($ctype, $codestr);
    			echo json_encode(array('status' => true, 'msg' => ''));
    			return ;
    		}
    	}
    	echo json_encode(array('status' => false,'msg' => ''));
    }
    
    /**
     * 参    数：无
     * 作    者：shigx
     * 功    能：查看身份证信息
     * 修改日期：2015-11-25
     */
    public function seeCard()
    {
    	if(empty($this->uid))
    	{
    		die('1');
    	}
    	$vdata = $this->input->post(null, true);
        /*
        $newpword = md5($vdata['pay_pwd']);
        if ($this->uinfo['salt'])
        {
            $newpword = $newpword . $this->uinfo['salt'];
            $newpword = strCode($newpword, 'ENCODE');
        }
        if($newpword != $this->uinfo['pword'])
    	{
    		die('3');
    	}
        */
        // 校验短信验证码
        $res = $this->checkCaptcha($vdata['modifyCaptcha'], $this->uinfo['phone'], 'modifyCaptcha', false);
        if($res === 2) 
        {
            die('2');
        } 
        elseif($res)
        {
            die('3');
        }
        else
    	{
    		echo "<label class='form-item-label'>真实姓名：</label><div class='form-item-con'><span class='form-item-txt'>{$this->uinfo['real_name']}</span>
					</div><label class='form-item-label'>身份证号：</label><div class='form-item-con'><span class='form-item-txt'>{$this->uinfo['id_card']}</span></div>";
    	}
    }

    /*
     * 修改用户手机号码
     * @date:2016-06-13
     */
    public function modifyUserPhone()
    {
        if($this->is_ajax)
        {
            $res = array(
                'status' => '001',
                'msg' => '系统异常',
            );
            $vdata = $this->input->post(null, true);

            if(empty($this->uinfo['phone']))
            {
                $res = array(
                    'status' => '110',
                    'msg' => '登录超时',
                );
                $this->ajaxResult($res);
            }
            //根据不同请求展开
            if($vdata['actiontype'])
            {
                switch ($vdata['actiontype']) 
                {
                    case '_1':
                        // 验证第一步
                        $res = $this->checkCaptcha($vdata['modifyCaptcha'], $this->uinfo['phone'], 'modifyCaptcha', false);
                        if ($res === 2) {
                        	$res = array('status' => '003', 'msg' => '验证码不正确');
                        } elseif ($res) {
                        	$res = array('status' => '002', 'msg' => '验证码不正确');
                        } else {
                        	$res = array('status' => '100', 'msg' => '手机验证码通过');
                        }
                        break;
                    case '_2':
                    	$modifyCaptcha = explode(':', $this->primarysession->getArg('modifyCaptcha'));
                    	if (empty($vdata['captcha']) || $vdata['captcha'] !== $modifyCaptcha[0]) {
                    		$this->primarysession->setArg('modifyCaptcha', '');
                    		echo json_encode(array('status'=> false, 'msg'=>'原手机验证码校验失败'));
                    		return ;
                    	}
                        //绑定信息验证
                		$res = $this->checkCaptcha($vdata['phoneCaptcha'], $vdata['modifyPhone'], 'modifyYzm');
                        if ($res === 2) {
                        	$res = array('status' => '003', 'msg' => '验证码不正确');
                        } elseif ($res) {
                        	$res = array('status' => '002', 'msg' => '验证码不正确');
                        } else {
                        	// 修改手机号码
                        	$data = array('uid'   => $this->uid, 'phone' => $vdata['modifyPhone']);
                        	$response = $this->user_model->modifyUserPhone($data);
                        	if($response['status']) {
                        		$this->primarysession->setArg('modifyCaptcha', '');
                        		$res = array('status' => '200', 'msg' => '手机号码重置成功');
                        	} else {
                        		$res = array('status' => '002', 'msg' => '手机号码重置失败');
                        	}
                        }
                        
                        break;
                    default:
                        break;
                }
            }
            //验证通过
            if($res['status'] == '100')
            {
            	$this->displayBanner('v1.1', $vdata, 'ycfc');
                $res['data'] = $this->displayAjaxLess("v1.1/safe/modifyUserPhone{$vdata['actiontype']}", $vdata);
                $this->ajaxResult($res);
            }
            elseif($res['status'] == '200')
            {
            	$this->displayBanner('v1.1', $vdata, 'ycfc');
                $res['data'] = $this->displayAjaxLess("v1.1/safe/modifyUserPhone{$vdata['actiontype']}", $vdata);
                $this->ajaxResult($res);
            }
            else
            {
                $this->ajaxResult($res);
            }
        }
        else
        {
            $vdata = $this->input->post(null, true);
            $data['current'] = $vdata['current'] ? $vdata['current'] : 1;
            $this->displayShortHeader('safe/modifyUserPhone', $data, 'v1.1');
        }
    }

    /*
     * 修改用户登录密码
     * @date:2016-06-17
     */
    public function modifyUserPword()
    {
        if($this->is_ajax)
        {
            $res = array(
                'status' => '001',
                'msg' => '系统异常',
            );

            $vdata = $this->input->post(null, true);

            if(empty($this->uinfo['phone']))
            {
                $res = array(
                    'status' => '110',
                    'msg' => '登录超时',
                );
                $this->ajaxResult($res);
            }
            //根据不同请求展开
            if($vdata['actiontype'])
            {
                switch ($vdata['actiontype']) 
                {
                    case '_1':
                        //验证第一步
                        $res = $this->checkCaptcha($vdata['modifyCaptcha'], $this->uinfo['phone'], 'modifyCaptcha', false);
                        if ($res === 2) {
                        	$res = array('status' => '003', 'msg' => '验证码不正确');
                        } elseif ($res) {
                        	$res = array('status' => '002', 'msg' => '验证码不正确');
                        } else {
                        	$res = array('status' => '100', 'msg' => '手机验证码通过');
                        }
                        break;
                    case '_2':
                        //确认新密码
                        $res = $this->checkPword($vdata);
                        break;
                    default:
                        break;
                }
            }
            //验证通过
            if($res['status'] == '100')
            {
            	$this->displayBanner('v1.1', $vdata, 'ycfc');
                $res['data'] = $this->displayAjaxLess("v1.1/safe/modifyUserPword{$vdata['actiontype']}", $vdata);
                $this->ajaxResult($res);
            }
            elseif($res['status'] == '300')
            {
                $this->loginout();
                $this->displayBanner('v1.1', $vdata, 'ycfc');
                $res['data'] = $this->displayAjaxLess("v1.1/safe/modifyUserPword{$vdata['actiontype']}", $vdata);
                $this->ajaxResult($res);
            }
            else
            {
                $this->ajaxResult($res);
            }
        }
        else
        {
            $vdata = $this->input->post(null, true);
            $data['current'] = $vdata['current'] ? $vdata['current'] : 1;
            $this->displayShortHeader('safe/modifyUserPword', $data, 'v1.1');
        }
    }

    private function checkPword($vdata)
    {
    	$modifyCaptcha = explode(':', $this->primarysession->getArg('modifyCaptcha'));
    	$this->primarysession->setArg('modifyCaptcha', '');
    	if (empty($vdata['captcha']) || $vdata['captcha'] !== $modifyCaptcha[0]) {
    		echo json_encode(array('status'=> false, 'msg'=>'原手机验证码校验失败'));
    		return ;
    	}
        if( empty($vdata['pword']) || empty($vdata['con_pword']) || $vdata['pword'] != $vdata['con_pword'] ){
            $res = array(
                'status' => '004',
                'msg' => '两次密码不匹配',
                'data' => array(),
            );
            return $res;
        }
        $this->load->model('user_model');
        if($this->uid)
        {
            $userInfo = $this->user_model->getUserInfo($this->uid);
            $user['salt'] = substr(uniqid(), 0, 9);
            $newpword = md5($vdata['pword']) . $user['salt'];
            $user['pword'] = strCode($newpword, 'ENCODE');
            $user['uid'] = $this->uid;
            $user['last_login_time'] = date('Y-m-d H:i:s');
            $user['visit_times'] = 1;
            $result = $this->user_model->SaveUser($user);
            if($result)
            {
                $res['status'] = '300';
                $res['msg'] = '操作成功';
                $res['data'] = $userInfo;
                $this->user_model->freshUserInfo($this->uid);
                return $res;
            }
        }
        
        $res = array(
            'status' => '001',
            'msg' => '修改失败',
            'data' => array(),
        );
        return $res;
    }

    public function loginout() {
        $this->load->helper('cookie');
        $url = $this->input->post('barUrl', true);
        $domain = str_replace('www.', '', $this->config->item('domain'));
        delete_cookie('I', $domain, '/');
        delete_cookie('name_ie', $domain, '/');
        delete_cookie('need_modify_name', $domain, '/');
    }
    
    /**
     * 绑定邮箱操作
     */
    public function bindEmail()
    {
    	if($this->is_ajax)
    	{
    		if(empty($this->uid) || $this->uinfo['email'])
    		{
    			die(json_encode(array('status'=> '1', 'msg'=>'操作失败')));
    		}
    		$email = $this->input->post('email', true);
    		$emailCaptcha = $this->input->post('emailCaptcha', true);
    		if(empty($email))
    		{
    			die(json_encode(array('status'=> '2', 'msg'=>'请输入正确的邮箱地址')));
    		}
    		if(empty($emailCaptcha) || !$this->checkEmailCaptcha($emailCaptcha, $email))
    		{
    			die(json_encode(array('status'=> '3', 'msg'=>'请输入正确的验证码')));
    		}
    		$data = array(
    			'email' => $email,
    			'uid' => $this->uid,
    		);
    		$this->load->model('user_model');
    		$res = $this->newuser_model->bindEmail($data);
    		if($res)
    		{
    			$this->user_model->freshUserInfo($this->uid);
    			die(json_encode(array('status'=> '0', 'msg'=>'操作成功')));
    		}
    		die(json_encode(array('status'=> '1', 'msg'=>'操作失败')));
    	}
    	else
    	{
    		if(empty($this->uid) || $this->uinfo['email'])
    		{
    			$this->redirect('/');
    			exit;
    		}
    		$vdata['headTitle'] = '绑定邮箱';
    		$this->displayShortHeader('safe/bindEmail', $vdata, 'v1.1');
    	}
    }
    
    /**
     * 修改邮箱操作
     */
    public function modifyEmail()
    {
    	if($this->is_ajax)
    	{
    		if(empty($this->uid) || empty($this->uinfo['email']))
    		{
    			die(json_encode(array('status'=> '1', 'msg'=>'操作失败')));
    		}
    		$email = $this->input->post('email', true);
    		$emailCaptcha = $this->input->post('emailCaptcha', true);
    		if(empty($email))
    		{
    			die(json_encode(array('status'=> '2', 'msg'=>'请输入正确的邮箱地址')));
    		}
    		if($email == $this->uinfo['email'])
    		{
    			die(json_encode(array('status'=> '2', 'msg'=>'新邮箱不可与原邮箱相同')));
    		}
    		if(empty($emailCaptcha) || !$this->checkEmailCaptcha($emailCaptcha, $email))
    		{
    			die(json_encode(array('status'=> '3', 'msg'=>'请输入正确的验证码')));
    		}
    		$data = array(
    			'email' => $email,
    			'uid' => $this->uid,
    		);
    		$this->load->model('user_model');
    		$res = $this->newuser_model->bindEmail($data);
    		if($res)
    		{
    			$this->user_model->freshUserInfo($this->uid);
    			die(json_encode(array('status'=> '0', 'msg'=>'操作成功')));
    		}
    		die(json_encode(array('status'=> '1', 'msg'=>'操作失败')));
    	}
    	else
    	{
    		if(empty($this->uid) || empty($this->uinfo['email']))
    		{
    			$this->redirect('/');
    			exit;
    		}
    		$vdata['headTitle'] = '修改邮箱';
    		$this->displayShortHeader('safe/modifyEmail', $vdata, 'v1.1');
    	}
    }
    
    //绑定邮箱验证码校验
    private function checkEmailCaptcha($captcha, $email)
    {
    	$code = $this->primarysession->getArg('emailCaptcha');
    	$codestr = explode(':', $code);
    	if (($codestr[1] > time()) && (strtoupper($captcha) == $codestr[0]) && ($email === $codestr[3])) 
    	{
    		$this->primarysession->setArg('emailCaptcha', '');
    		return true;
    	}
    	
    	return false;
    }
    
    /**
     * 邮箱绑定发送邮件
     */
    public function getEmailCode()
    {
    	if(empty($this->uid))
    	{
    		die(json_encode(array('status'=> false, 'msg'=>'请先登录')));
    	}
    	$email = trim($this->input->post('email', true));
    	$type = $this->input->post('type', true);
    	if(!preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/u', $email))
    	{
    		die(json_encode(array('status'=> false, 'msg'=>'邮箱格式错误')));
    	}
    	$sessionCode = $this->primarysession->getArg('emailCaptcha');
    	if(!empty($sessionCode))
    	{
    		$codestr = explode(':', $sessionCode);
    		if(($email == $codestr[3]) && (time() < $codestr[2]))
    		{
    			die(json_encode(array('status'=> false, 'msg'=>'操作太频繁，请稍后重试')));
    		}
    	}
    	
    	$code = $this->getVerificationCode();
    	$url_prefix = $this->config->item('url_prefix');
    	$this->url_prefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';
    	if($type)
    	{
    		$message = '<style>body {background: #fbf7eb;}</style><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"><tr>
    			<td style="padding: 38px 10px; background: #fbf7eb;"><table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background: #eee;"><tr><td>
                <img src="'.$this->url_prefix . ':'.getStaticFile('/caipiaoimg/v1.1/img/logo-email.png').'" width="280" height="68" alt="166彩票为生活添彩" border="0"></td></tr>
                </table></td></tr><tr><td style="margin:0 auto; padding: 30px 30px 0; background: #fff; font-family:\'Microsoft YaHei\';">
                <table width="650" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td style="font-size:14px; color: #666;">
                <tr><td style="font-size:16px; color: #666;">尊敬的166彩票网用户'.$this->uinfo['uname'].'，您好！</td></tr><tr>
                <td style="padding-top: 10px; line-height: 1.6; font-size: 16px; font-family:\'Microsoft YaHei\'; color: #333;">
                <b style="padding-left: 32px; font-weight: bold;">感谢您使用166彩票，您的邮箱验证码为<span style="color: #e60000">'.$code.'</span>，</b>请及时输入验证码并完成邮箱修改，完成后即可使用新邮箱享受出票通知服务！
                </td></tr><tr><td style="padding: 30px 0 40px; font-size: 12px; color: #e50100;">温馨提示：如非您本人操作，请忽略邮件中内容！</td></tr><tr>
                <td style="border-top: 5px solid #f5f4ef;"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                <td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';color: #999;">此邮件为系统自动发送，请勿直接回复<br>
                	如对以上内容有所疑问，欢迎前往<a href="'.$this->url_prefix . ':'. $this->config->item('pages_url') .'" target="_blank" style="color: #3e8be7;">166彩票</a>联系在线客服<br>客服热线：400-690-6760<br>
                	查看《<a href="'.$this->url_prefix . ':'. $this->config->item('pages_url') .'activity/fwcn" target="_blank" style="color: #3e8be7;">166彩票用户服务承诺</a>》<br>更多优惠活动可扫描右侧二维码下载客户端查看</td>
                <td align="center" style="padding:20px 0;"><img src="'.$this->url_prefix . ':'.getStaticFile('/caipiaoimg/v1.1/img/qrcode.png').'" alt="" border="0"></td></tr></table>
                </td></tr></table></td></tr></table></td></tr></table>';
    	}
    	else
    	{
    		$message = '<style>body {background: #fbf7eb;}</style><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"><tr>
    			<td style="padding: 38px 10px; background: #fbf7eb;"><table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background: #eee;"><tr><td>
                <img src="'.$this->url_prefix . ':'.getStaticFile('/caipiaoimg/v1.1/img/logo-email.png').'" width="280" height="68" alt="166彩票为生活添彩" border="0"></td></tr>
                </table></td></tr><tr><td style="margin:0 auto; padding: 30px 30px 0; background: #fff; font-family:\'Microsoft YaHei\';">
                <table width="650" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td style="font-size:14px; color: #666;">
                <tr><td style="font-size:16px; color: #666;">尊敬的166彩票网用户'.$this->uinfo['uname'].'，您好！</td></tr><tr>
                <td style="padding-top: 10px; line-height: 1.6; font-size: 16px; font-family:\'Microsoft YaHei\'; color: #333;">
                <b style="padding-left: 32px; font-weight: bold;">感谢您使用166彩票，您的邮箱验证码为<span style="color: #e60000">'.$code.'</span>，</b>请及时输入验证码并完成邮箱绑定，完成后即可享受出票通知服务！
                </td></tr><tr><td style="padding: 30px 0 40px; font-size: 12px; color: #e50100;">温馨提示：如非您本人操作，请忽略邮件中内容！</td></tr><tr>
                <td style="border-top: 5px solid #f5f4ef;"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                <td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';color: #999;">此邮件为系统自动发送，请勿直接回复<br>
                	如对以上内容有所疑问，欢迎前往<a href="'.$this->url_prefix . ':'. $this->config->item('pages_url') .'" target="_blank" style="color: #3e8be7;">166彩票</a>联系在线客服<br>客服热线：400-690-6760<br>
                	查看《<a href="'.$this->url_prefix . ':'. $this->config->item('pages_url') .'activity/fwcn" target="_blank" style="color: #3e8be7;">166彩票用户服务承诺</a>》<br>更多优惠活动可扫描右侧二维码下载客户端查看</td>
                <td align="center" style="padding:20px 0;"><img src="'.$this->url_prefix . ':'.getStaticFile('/caipiaoimg/v1.1/img/qrcode.png').'" alt="" border="0"></td></tr></table>
                </td></tr></table></td></tr></table></td></tr></table>';
    	}
    	
    	$data = array(
    		'to'	  => $email,
    		'subject' => $type ? '修改邮箱通知' : '邮箱绑定通知',
    		'message' => $message,
    	);
        //修改成阿里云
    	//$result = $this->tools->sendMail($data);
        $result = $this->tools->sendMail($data,array(),1);
    	if($result)
    	{
    		$out_time = $this->config->item('OUTTIME');
    		$time = time();
    		$expire = $time + 60 * $out_time['captche'];
    		$second = $time + 60;
    		$codestr = "{$code}:$expire:$second:$email";
    		$this->primarysession->setArg('emailCaptcha', $codestr);
    		die(json_encode(array('status'=> true, 'msg'=>'操作成功')));
    	}
    	
    	die(json_encode(array('status'=> false, 'msg'=>'操作失败，请联系客服')));
    }
    
    /**
     * 邮箱验证码生成
     */
    public function getVerificationCode()
    {
    	$codes = array();
    	$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	for($i = 0; $i < 4; $i++)
    	{
    		$codes[] = $str[mt_rand(0, 25)];
    	}
    	$str = "0123456789";
    	for($i = 0; $i < 2; $i++)
    	{
    		$codes[] = $str[mt_rand(0, 9)];
    	}
    	
    	shuffle($codes);
    	return implode('', $codes);
    }
    
    /**
     * 邮箱绑定成功页
     */
    public function bindEmailSucc()
    {
    	if(empty($this->uid) || empty($this->uinfo['email']))
    	{
    		$this->redirect('/');
    		exit;
    	}
    	$this->displayShortHeader('safe/bindEmailSucc', array(), 'v1.1');
    }
    
    /**
     * 邮箱修改成功页
     */
    public function modifyEmailSucc()
    {
    	if(empty($this->uid) || empty($this->uinfo['email']))
    	{
    		$this->redirect('/');
    		exit;
    	}
    	$this->displayShortHeader('safe/modifyEmailSucc', array(), 'v1.1');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
