<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：用户管理
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.10
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_user');
        $this->config->load('msg_text');
        $this->msg_text_cfg = $this->config->item('msg_text_cfg');
    }
    
    /**
     * 参    数：
     * 作    者：wangl
     * 功    能：用户列表
     * 修改日期：2014.11.11
     */
    public function index()
    {
        $this->check_capacity('1_1_1');
        $page = intval($this->input->get("p"));
        $searchData = array(
            "reg_reffer" => $this->input->get("regreffer", true),
            "start_v_t" => $this->input->get("start_v_t", true),
            "end_v_t" => $this->input->get("end_v_t", true),
            "is_id_bind" => $this->input->get("is_id_bind", true),
            "is_bankcard_bind" => $this->input->get("is_bankcard_bind", true),
            "is_phone_bind" => $this->input->get("is_phone_bind", true),
        	"is_email_bind" => $this->input->get("is_email_bind", true),
            "name" => $this->input->get("name", true),
            "platform" => $this->input->get("platform", true),
            "start_l_t" => $this->input->get("start_l_t", true),
            "end_l_t" => $this->input->get("end_l_t", true),
            "start_r_t" => $this->input->get("start_r_t", true),
            "end_r_t" => $this->input->get("end_r_t", true),
            "islike" => $this->input->get("islike", true),
        	"registerChannel" => $this->input->get("registerChannel", true),
            "userLockStatus" => $this->input->get("userLockStatus", true),
            "reg_type" => $this->input->get("reg_type", true),
        );
        $searchData['userLockStatus'] = $searchData['userLockStatus']?$searchData['userLockStatus']:'0';
        $searchData["platform"] = $searchData["platform"] - 1;
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_user->list_user($searchData, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $result[1]
        );
        $pages = get_pagination($pageConfig);
        $this->load->model('model_channel');
        $channelRes = $this->model_channel->getChannels();
        foreach ($channelRes as $val){
        	$channels[$val['id']] = $val;
        }
        $pageInfo = array(
            "users" => $result[0],
            "pages" => $pages,
            "search" => $searchData,
        	'channels' => $channels,
        );
        $this->load->view("user", $pageInfo);
    }
    
    /**
     * 参    数：
     * 作    者：wangl
     * 功    能：用户基本信息
     * 修改日期：2014.11.11
     */
    public function user_manage()
    {
        $this->check_capacity('1_1_2');
        $id = intval($this->input->get("uid"));
        $tab = $this->input->get("tab");
        $user = $this->Model_user->find_user_by_id($id);
        //银行卡信息
        $bankInfo = $this->Model_user->find_bank_by_id($id);
        $this->load->model('Model_order');
        $this->load->model('Model_transactions');
        $orderInfo = $this->Model_order->find_order_by_uid($id);
        $transInfo = $this->Model_transactions->find_trans_by_uid($id);
        //可提现
        $withdraw = $this->Model_user->getWithDraw($id);
        //成长值
        $growth = $this->Model_user->get_growth_by_uid($id);
        $this->config->load('pay');
        foreach ($this->config->item('pay_all_cfg') as $key => $value)
        {
            $this->$key = $value;
        }
        //支付银行卡
        $this->load->model('pay_bank_model');
        $payBankInfo = $this->pay_bank_model->getUserPayBanks($id);
        $pageInfo = array(
            "user" => $user,
            "bankInfo" => $bankInfo,
            "orderInfo" => $orderInfo,
            "transInfo" => $transInfo,
            "withdraw" => $withdraw,
            "payBankInfo" => $payBankInfo,
        	"tab" => $tab,
            'growth'=>$growth,
        );
        $pageInfo['adjust'] = false;
        $pageInfo['growths'] = false;
        if (in_array('1_1_8', $this->capacity)) {
        	$pageInfo['adjust'] = true;
        }
        if (in_array('1_1_12', $this->capacity)) {
            $pageInfo['growths'] = true;
        }
        $this->load->view("user_manage", $pageInfo);
    }
    
    public function uinfo_log($uid) {
        $this->check_capacity('1_1_15');
        $typeArr = array(1 => '用户名', 2 => '姓名', 3 => '身份证', 4 => '手机号', 5 => '邮箱地址');
        $placeArr = array(1 => '用户', 2 => '后台');
        $data = $this->Model_user->list_uinfo_log($uid);
        $this->load->view("uinfo_log", compact('data', 'typeArr', 'placeArr'));
    }
    
    public function resetuname() {
        $this->check_capacity('1_1_16', true);
        $uid = $this->input->post('uid');
        $this->Model_user->resetuname($uid);
        $this->ajaxReturn('y');
        $this->syslog(1, "重置用户名修改次数，uid：{$uid}");
    }
    
    /**
     * 参    数：
     * 作    者：wangl
     * 功    能：用户登录信息
     * 修改日期：2014.11.11
     */
    public function login_info()
    {
        $this->check_capacity('1_1_3');
        $page = intval($this->input->get("p"));
        $id = intval($this->input->get("uid"));
        $page = $page <= 1 ? 1 : $page;
        $login_infos = $this->Model_user->get_login_info($id, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page" => $page,
            "npp" => self::NUM_PER_PAGE,
            "allCount" => $login_infos[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "login_infos" => $login_infos[0],
            "pages" => $pages
        );
        echo $this->load->view("login_info", $pageInfo, true);
    }

    // 注销账户
    public function lockUser()
    {
        $uid = $this->input->post("lock_uid", true);
        $uname = $this->input->post("lock_uname", true);
        $userStatus = $this->input->post("userStatus", true);
        if($userStatus == '1')
        {
            $this->check_capacity('1_1_7', true);
        }
        else
        {
            $this->check_capacity('1_1_6', true);
        }
        if(!empty($uid))
        {
            if($this->Model_user->lockUser($uid, $userStatus))
            {
            	$msg = array('0' => '解冻用户' ,'1' => '注销用户', '2' => '冻结用户');
                // 记录日志
                $this->syslog(1, "{$msg[$userStatus]}：" . $uname . "操作");
                $this->ajaxReturn('y', '修改成功');
            }
        }
        $this->ajaxReturn('n', '修改失败');
    }

    /**
     * 修改身份证/手机号/真实姓名/邮箱 权限检查操作
     */
    public function checkModifyCapacity()
    {
        $this->check_capacity('1_1_4', true);

        $result = array(
            'status' => 'y',
        );
        echo json_encode($result);
    }
    
    /**
     * 出票、中奖短信、邮件操作 权限检查操作
     */
    public function updateMsgsendCapacity()
    {
        $this->check_capacity('1_1_5', true);
        
        $result = array(
            'status' => 'y',
        );
        echo json_encode($result);
    }

    public function recordLog()
    {
        $msg = $this->input->post("msg", true);
        $uname = $this->input->post("uname", true);
        // 扩展字段
        $extra = $this->input->post("extra", true);
        $msg = "{$msg}：" . $uname . "操作";
        $msg .= (!empty($extra)) ? ',' . $extra : '';
        $this->syslog(1, $msg);
    }
    
    public function ajustumoney()
    {
    	$this->check_capacity('1_1_8', true);
    	$info = $this->input->post('ajust');
    	if ($info['money'] > 0) {
    		$this->load->model('model_ajust_umoney', 'model');
    		if ($info['orderId'] && !$this->model->check_order($info['uid'], $info['orderId'])) {
    			exit(json_encode(array('status' => '0')));
    		}
    		if ($info['type'] === '1') {
    			$info['ismustcost'] = '1';
    		}
    		if (in_array($info['ctype'], array('2'))) {
    			$info['iscapital'] = '0';
    		}
    		$info['money'] *= 100;
    		$info['created'] = date('Y-m-d H:i:s');
    		$uname = $info['uname'];
    		unset($info['uname']);
    		$res = $this->model->insertData(array($info));
    		if ($res) {
    			$this->syslog(40, "手动调款对{$uname}操作");
    			exit(json_encode(array('status' => '1')));
    		}
    	}
    	exit(json_encode(array('status' => '0')));
    }
    
    public function ip() {
    	$this->check_capacity('1_1_9');
    	$page = intval($this->input->get("p"));
    	$page = $page <= 1 ? 1 : $page;
    	$this->load->model('model_forbid_ip', 'model');
    	$result = $this->model->list_data(array(), $page, self::NUM_PER_PAGE);
    	$pages = get_pagination(array("page" => $page, "npp" => self::NUM_PER_PAGE, "allCount" => $result['count'])); 
    	$this->load->view('/userip', array('data' => $result['data'], "pages" => $pages));
    }
    
    public function addip() {
    	$this->check_capacity('1_1_10', true);
    	$ip = $this->input->post('ip');
    	if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip)) {
    		$data = array('ip' => $ip);
    		try {
    			$this->load->library('tools');
    			$ipJson = $this->tools->request("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
    			$ipInfo = json_decode($ipJson, true);
    			if ($ipInfo['code'] == 0) {
    				$data['address'] = $ipInfo['data']['region'];
    				$data['operator'] = $ipInfo['data']['isp'];
    			}
    		} catch (Exception $e) {
    		}
    		$this->load->model('model_forbid_ip', 'model');
    		if ($this->model->add($data)) {
    			$this->syslog(1, "新增冻结IP：".$ip.(isset($data['address']) ? "，所在地区：".$data['address']." ".$data['operator'] : ''));
    			return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    		}
    		return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    	}
    	return $this->ajaxReturn('n', $this->msg_text_cfg['falied']);
    }
    
    public function delip() {
    	$this->check_capacity('1_1_11', true);
    	$id = $this->input->post('id');
    	$this->load->model('model_forbid_ip', 'model');
    	$res = $this->model->del($id);
    	$this->syslog(1, "删除冻结IP：".$res['ip'].(isset($res['address']) ? "，所在地区：".$res['address']." ".$res['operator'] : ''));
    	return $this->ajaxReturn('y', $this->msg_text_cfg['success']);
    }

    // 合买关注
    public function unitedFollow()
    {
        $this->check_capacity('1_1_13');
        $page = intval($this->input->get("p"));
        $uid = intval($this->input->get("uid"));
        $uinfo = $this->Model_user->find_user_by_id($uid);
        $page = $page <= 1 ? 1 : $page;
        $result = $this->Model_user->getUnitedFollowed($uid, $page, self::NUM_PER_PAGE);
        $pageConfig = array(
            "page"      =>  $page,
            "npp"       =>  self::NUM_PER_PAGE,
            "allCount"  =>  $result[1]
        );
        $pages = get_pagination($pageConfig);
        $pageInfo = array(
            "list"  =>  $result[0],
            "pages" =>  $pages,
            "uid"   =>  $uid,
            "uname" =>  $uinfo['uname'],
        );
        echo $this->load->view("management/unitedFollow", $pageInfo, true);
    }

    public function cancelFollow()
    {
        $this->check_capacity('1_1_14', true);
        $id = intval($this->input->post("id"));
        $uname = $this->input->post("uname");
        $puname = $this->input->post("puname");
        if($this->Model_user->cancelFollow($id))
        {
            // 记录日志
            $this->syslog(1, "取消" . $uname . "关注发起人" . $puname . '操作');
            $this->ajaxReturn('SUCCESSS', "恭喜您，操作成功。");
        }
        else
        {
            $this->ajaxReturn('ERROR', "操作失败。");
        }
    }
}
