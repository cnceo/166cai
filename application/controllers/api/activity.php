<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/1/25
 * 修改时间: 18:57
 */

/**
 * @property New_Activity_Model $new_activity_model
 */
class Activity extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->method = 'hongbao166';
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->load->model('new_activity_model');
    }

    public function detail()
    {
        $activityId = $this->input->post('id', TRUE);

        if (empty($activityId) || ! is_numeric($activityId))
        {
            list($status, $success, $msg, $data) = array(400, 0, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }

        list($success, $msg, $data) = $this->new_activity_model->detail($activityId);
        $status = $success ? 200 : 400;

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }

    public function attend()
    {
        $phone = $this->input->post('phone', TRUE);
        $platformId = $this->input->post('platformId', TRUE);
        $fromUid = $this->input->post('fromUid', TRUE);
        $channelId = $this->input->post('channelId', TRUE);
        $sendSms = $this->input->post('sendSms', TRUE);
        $smsid = $this->input->post('smsid', TRUE);
        $activityType = $this->input->post('activityType', TRUE);
        // 是否发送短信
        $sendSms = $sendSms ? 0 : 1;
        

        if (empty($phone) || ! is_numeric($phone))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }

        //todo use redis
        $this->load->library('libredpack');
        
        list($success, $msg, $data) = $this->libredpack->{$this->method}('register', array('phone' => $phone, 'platformId' => $platformId, 'channel' => $channelId, 'fromUid'=>$fromUid, 'activityType' => $activityType));
        $status = $success ? 200 : 400;
        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }
    
    public function attendlx166() {
    	$phone = $this->input->post('phone', TRUE);
    	$platformId = $this->input->post('platformId', TRUE);
    	$channelId = $this->input->post('channelId', TRUE);
    	$sendSms = $this->input->post('sendSms', TRUE);
    	$smsid = $this->input->post('smsid', TRUE);
    	$reffer = $this->input->post('reffer', TRUE);
    	$ip = $this->input->post('ip', TRUE);
    	// 是否发送短信
    	$sendSms = $sendSms ? 0 : 1;
    	if (empty($phone) || ! is_numeric($phone)) {
    		list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());
    		echo json_encode(compact('status', 'success', 'msg', 'data'));
    		exit;
    	}
    	
    	$this->load->library('libredpack');
    	list($success, $msg, $data) = $this->libredpack->{$this->method}('lx', array('phone' => $phone, 'platformId' => $platformId, 'reffer' => $reffer, 'ip' => $ip, 'channel' => $channelId, 'fromUid'=>$fromUid));
    	$status = $data['code'];
    	unset($data['code']);
    	echo json_encode(compact('status', 'success', 'msg', 'data'));
    	exit;
    }
    
    public function active()
    {
    	$phone = $this->input->post('phone', TRUE);
    	$id_card = $this->input->post('id_card', TRUE);
    	$uid = $this->input->post('uid', TRUE);
    	$this->load->library('libredpack');
    	$res = $this->libredpack->{$this->method}('bindcard', array('phone' => $phone, 'uid' => $uid, 'id_card' => $id_card));
    	echo json_encode($res);exit();
    }

    public function hasAttend()
    {
        $phone = $this->input->post('phone', TRUE);

        if (empty($phone) || ! is_numeric($phone))
        {
            list($status, $success, $msg, $data) = array(400, FALSE, '参数不正确', array());

            echo json_encode(compact('status', 'success', 'msg', 'data'));
            exit;
        }
        
        $this->load->library('libredpack');
        $hasAttend = $this->libredpack->{$this->method}('hasAttend', array('phone' => $phone));
        $status = $hasAttend ? 200 : 400;
        $data = compact('hasAttend');

        echo json_encode(compact('status', 'success', 'msg', 'data'));
        exit;
    }
    
    public function initData()
    {
        $this->new_activity_model->initData();
    }

    // 拉新活动 - 注册
    public function regAdd()
    {
        $uid = $this->input->post('uid', TRUE);
        $phone = $this->input->post('phone', TRUE);

        $this->load->model('activity_lx_model');
        $res = $this->activity_lx_model->regAdd($uid, $phone);
        exit(json_encode($res));
    }

    // 拉新活动 - 实名
    public function idcardAdd()
    {
        $uid = $this->input->post('uid', TRUE);
        $idCard = $this->input->post('idCard', TRUE);

        $this->load->model('activity_lx_model');
        $res = $this->activity_lx_model->idcardAdd($uid, $idCard);
        exit(json_encode($res));
    }

    // 拉新活动 - 抽奖
    public function draw()
    {
        $uid = $this->input->post('uid', TRUE);
        $platformId = $this->input->post('platformId', TRUE);
        $channelId = $this->input->post('channelId', TRUE);

        $this->load->model('activity_lx_model');
        $res = $this->activity_lx_model->draw($uid, $platformId, $channelId, $activityId = 5);
        exit(json_encode($res));
    }
    
    public function getwcredpack() {
        $uid = $this->input->post('uid', TRUE);
        $platformId = $this->input->post('platformId', TRUE);
        $channelId = $this->input->post('channelId', TRUE);
        $phone = $this->input->post('phone', TRUE);
        $this->load->library('libredpack');
        $res = $this->libredpack->hongbaoworldcup('attend', array('uid' => $uid, 'phone' => $phone, 'platformId' => $platformId, 'channel' => $channelId));
        exit(json_encode($res));
    }

    public function sendDtHongbao()
    {
        $data = $this->input->post();
        $this->load->library('libredpack');
        $res = $this->libredpack->hongbaoworldcup('sendHongbao', $data);
        exit(json_encode($res));
    }
}