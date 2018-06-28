<?php

/*
 * APP 活动聚合页
 * @date:2016-02-02
 */

class Activity extends MY_Controller {

	public function __construct() 
    {
        parent::__construct();
        $this->versionInfo = $this->getUserAgentInfo();
        $this->load->model('user_model');
        $this->load->model('activity_model');
    }

    public function index($activity)
    {
    	$this->load->view('activity/' . $activity);
    }

    // O2O开售送彩金 iOS内页
	public function yfqmcp()
    {
    	$hasAttend = 0;
    	$title = '注册即送166元红包';
    	$data = $this->activity_model->getTimeById(8);
    	$startTime = $data['startTime'];
    	$endTime = $data['endTime'];
    	// 检查用户是否已参与
    	if(!empty($this->uid))
    	{
    		$uinfo = $this->user_model->getUserInfo($this->uid);
    		$this->load->model('new_activity_model');
    		$hasAttend = $this->new_activity_model->hasAttend(8, $uinfo['phone']);
    	}
    	$this->load->view('activity/yfqmcp', compact('hasAttend', 'title', 'startTime', 'endTime'));
    }
    // O2O开售送彩金 iOS内页
    public function appScj()
    {
        $data = array(
            'hasAttend' => '0'
        );
        // 检查用户是否已参与
        if(!empty($this->uid))
        {
            // 获取用户信息
            $uinfo = $this->user_model->getUserInfo($this->uid);
            // 组装参数
            $postData = array(
                'phone' => $uinfo['phone']
            );
            // 请求红包活动接口
	        if(ENVIRONMENT === 'checkout')
			{
				$postUrl = $this->config->item('cp_host');
				$checkData['HOST'] = $this->config->item('domain');
			}
			else
			{
				// $postUrl = $this->config->item('pages_url');
				$postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
			}
            $attendResult = $this->tools->request($postUrl . '/api/activity/hasAttend', $postData);
            $attendResult = json_decode($attendResult, true);
            // 已参与
            if($attendResult['status'] == '200')
            {
                $data = array(
                    'hasAttend' => '1'
                );
            }
        }
        $time = $this->activity_model->getTimeById(1);
        $data = array_merge($time, $data);
    	$this->load->view('activity/appScj', $data);
    }

    // 内页领取红包 AJAX
	public function innerAttend()
    {
    	$data = $this->input->post(null, true);

    	$result = array(
    		'status' => '000',
    		'msg' => '通讯异常',
    		'data' => ''
    	);

    	if(empty($this->uid))
        {
            $result = array(
                'status' => '100',
                'msg' => '用户未登录',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }

        // 获取用户信息
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if(empty($uinfo))
        {
            $result = array(
                'status' => '100',
                'msg' => '用户信息获取失败',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        
    	$postData = array(
            'phone' => $uinfo['phone'],
            'platformId' => $this->config->item('platform'),
            'channelId' => $this->recordChannel($data['channel'])
       );
       // 请求红包活动接口
    	if(ENVIRONMENT === 'checkout')
		{
			$postUrl = $this->config->item('cp_host');
			$checkData['HOST'] = $this->config->item('domain');
		}
		else
		{
			// $postUrl = $this->config->item('pages_url');
			$postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
		}
       $attendResult = $this->tools->request($postUrl . 'api/activity/attend', $postData);
       $attendResult = json_decode($attendResult, true);
       if($attendResult['status'] == '200')
       {
           $result = array(
               'status' => '200',
               'msg' => '参加成功',
               'data' => ''
           );
       }
       else
       {
           $result = array(
               'status' => '300',
               'msg' => $attendResult['msg'],
               'data' => ''
           );
       }
        

        echo json_encode($result);
    }

	// O2O开售送彩金 APP外页
    public function appOutScj()
    {
    	$data = $this->activity_model->getTimeById(1);
        $this->load->view('activity/appOutScj', $data);
    }
    
    public function appOutScjeastnews1()
    {
    	$data = array(
    		'css' => 'dkw.min.css',
    		'version' => 10149,
    		'smsid' => 1,
    		'rpbox' => false
    	);
    	$time = $this->activity_model->getTimeById(1);
    	$data = array_merge($time, $data);
    	$this->load->view('activity/appOutScjeastnews', $data);
    }
    
    public function appOutScjeastnews2()
    {
    	$data = array(
    		'css' => 'df-newuser.min.css',
    		'version' => 10150,
    		'smsid' => 2,
    		'rpbox' => true
    	);
    	$time = $this->activity_model->getTimeById(1);
    	$data = array_merge($time, $data);
    	$this->load->view('activity/appOutScjeastnews', $data);
    }

    // 发送手机语音验证码
    public function sendCaptcha()
    {
        $phone = $this->input->post('phone', true);
        $imgCaptcha = $this->input->post('imgCaptcha', true);

        $this->primarysession->startSession();

        if ($this->primarysession->getArg('captcha') != strtolower($imgCaptcha))
        {
            $result = array(
                'status' => '0',
                'msg' => '图形验证码错误',
                'data' => ''
            );
            echo json_encode($result); 
            exit();
        }

        $sendResult = $this->getSmsCode($phone, '188hb', 'app_captcha');
        // $sendResult = true;
        if($sendResult)
        {
            $result = array(
                'status' => '1',
                'msg' => '手机号码发送成功',
                'data' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '手机号码发送失败',
                'data' => ''
            );
        }
        echo json_encode($result); 
    }

    // 外页领取红包 AJAX
    public function outerAttend()
    {
        $data = $this->input->post(null, true);

        $result = array(
            'status' => '000',
            'msg' => '通讯异常',
            'data' => ''
        );

        // 手机格式检查
        $rule = '/1\d{10}$/';
        if (!preg_match($rule, $data['phone']))
        {
            $result = array(
                'status' => '100',
                'msg' => '手机号码格式不正确',
                'data' => array()
            );
            echo json_encode($result);
            exit();
        }

        // 验证码检查
        $this->primarysession->startSession();

        if ($this->primarysession->getArg('captcha') != strtolower($data['imgCaptcha']))
        {
            $result = array(
                'status' => '300',
                'msg' => '验证码不正确',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        else
        {
            // 检查是否领过
            $checkData = array(
                'phone' => $data['phone']
            );

            // 请求红包活动接口
            if(ENVIRONMENT === 'checkout')
            {
                $postUrl = $this->config->item('cp_host');
                $checkData['HOST'] = $this->config->item('domain');
            }
            else
            {
                // $postUrl = $this->config->item('pages_url');
                $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
            }

            $checkResult = $this->tools->request($postUrl . 'api/activity/hasAttend', $checkData);
            $checkResult = json_decode($checkResult, true);

            // 已参与
            if($checkResult['status'] == '200')
            {
                $result = array(
                    'status' => '400',
                    'msg' => '该手机已参加过活动',
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }

            // 组装参数
            $postData = array(
                'phone' => $data['phone'],
                'platformId' => $this->config->item('platform'),
                'channelId' => $this->recordChannel($data['channel']),
            	'smsid' => $data['smsid'],
            );
            
            // 请求红包活动接口
            if(ENVIRONMENT === 'checkout')
            {
                $postUrl = $this->config->item('cp_host');
                $postData['HOST'] = $this->config->item('domain');
            }
            else
            {
                // $postUrl = $this->config->item('pages_url');
                $postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
            }

            $attendResult = $this->tools->request($postUrl . 'api/activity/attend', $postData);
            $attendResult = json_decode($attendResult, true);

            if($attendResult['status'] == '200')
            {
                $result = array(
                    'status' => '200',
                    'msg' => '参加成功',
                    'data' => ''
                );
            }
            else
            {
                $result = array(
                    'status' => '300',
                    'msg' => $attendResult['msg'],
                    'data' => ''
                );
            }
            echo json_encode($result);
        }

    }

	// 竞彩加奖活动
    public function jcjj($id = null) {
        $this->load->view('mobileview/activity/jcjj');
    }
    
    // 竞彩加奖活动
    public function ecyjj($id = null) {
    	$this->load->view('activity/ecyjj');
    }
    /**
     * 世界杯加奖
     */
    public function sjbjj()
    {
        $this->load->view('activity/sjbjj');
    }
    
    public function hemai()
    {
    	$this->load->view('/activity/hemai');
    }
    
    public function invitation()
    {
        $cpk = intval($this->input->get("cpk"));
        $datas = $this->activity_model->getInvitation(9, $this->uid);
        $users = $datas[0];
        $count = $datas[1]['count'];
        $showBind = 1;
        if ($this->uid)
        {
            $uinfo = $this->user_model->getUserInfo($this->uid);
            // 是否实名认证
            if ($uinfo['real_name'] && $uinfo['id_card'])
            {
                $showBind = 0;
            }
        }
        foreach ($users as $k => $user)
        {
            $userinfo = $this->user_model->getUserInfo($user['uid']);
            $users[$k]['uname'] = $userinfo['uname'];
            $users[$k]['created'] = date("Y.m.d",strtotime($userinfo['created']));
        }
        $time = $this->activity_model->getTimeById(9);
        $self = $this->user_model->getId($this->uid);
        $uid = $this->uid;
        $url = $this->config->item('protocol') . "//www.166cai.cn/ios/activity/invitationRegister?userId=" . $self['id'] . "&cpk=" . $cpk;
        $imgurl = $this->config->item('protocol') . "//www.166cai.cn/caipiaoimg/static/images/app-icon.png";
        $from = $this->user_model->getId ($this->uid, 2 );
        $this->load->model('activity_lx_model');
        $recode =$this->activity_lx_model->countHasLx($from['uid']);
        $this->load->view('/activity/invitation', compact('users', 'self', 'showBind', 'uid', 'url', 'imgurl', 'count', 'recode', 'time'));
    }
    
    public function invitationRegister()
    {
        $cpk = intval($this->input->get("cpk"));
        $this->load->model('channel_model', 'Channel');
        $channel = $this->Channel->getById($cpk);
        // $downHref = $this->config->item('protocol') . $this->config->item('pages_url') . 'app/download/?c=10170';
        if (isset($channel['app_path']) && $channel['app_path'])
        {
            $downHref = $channel['app_path'];
        }
        $downHref = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.caipiao166&ckey=CK1393591343473';
        $id = $this->input->get('userId', true);
        $from = $this->user_model->getId($id, 2);
        $this->load->model('activity_lx_model');
        $recode =$this->activity_lx_model->countHasLx($from['uid']);
        $this->load->view('/activity/invitationRegister', compact('from', 'id', 'downHref', 'recode'));
    }
    
    public function invitationDoRegister()
    {
        $data = $this->input->post(null, true);

        $result = array(
            'status' => '100',
            'msg' => '通讯异常',
            'data' => ''
        );

        // 手机格式检查
        $rule = '/1\d{10}$/';
        if (!preg_match($rule, $data['phone']))
        {
            $result = array(
                'status' => '100',
                'msg' => '手机号码格式不正确',
                'data' => array()
            );
            echo json_encode($result);
            exit();
        }

        $from = $this->user_model->getId ( $data ['id'], 2 );
        
        $this->load->model('activity_lx_model');
        $recode =$this->activity_lx_model->countHasLx($from['uid']);
        if ($recode >= 15) {
        	// 验证码检查
        	$this->primarysession->startSession();
        	$captcha = $this->primarysession->getArg('captcha');
        	$this->primarysession->setArg('captcha', '');
        	if ($captcha != strtolower($data['imgCode']))
        	{
        		$result = array(
        				'status' => '100',
        				'msg' => '验证码不正确',
        				'data' => ''
        		);
        		echo json_encode($result);
        		exit();
        	}
        }
        
		// 检查是否领过
		$checkData = array(
			'phone' => $data['phone']
		);
			
			// 请求红包活动接口
		if (ENVIRONMENT === 'checkout')
		{
			$postUrl = $this->config->item ( 'cp_host' );
			$checkData ['HOST'] = $this->config->item ( 'domain' );
		} else
		{
			// $postUrl = $this->config->item('pages_url');
			$postUrl = $this->config->item ( 'protocol' ) . $this->config->item ( 'pages_url' );
		}
		
		$checkResult = $this->tools->request ( $postUrl . 'api/activity/hasAttend', $checkData );
		$checkResult = json_decode ( $checkResult, true );
		
		// 已参与
		if ($checkResult ['status'] == '200')
		{
			$result = array (
					'status' => '300',
					'msg' => '您已领取过红包',
					'data' => '' 
			);
			echo json_encode ( $result );
			exit ();
		}
		
		
		// 组装参数
		$postData = array (
				'phone' => $data ['phone'],
				'platformId' => $this->config->item ( 'platform' ),
				'channelId' => $this->recordChannel ( $data ['channel'] ),
				'smsid' => $data ['smsid'],
				'fromUid' => $from ['uid'] 
		);
		
		// 请求红包活动接口
		if (ENVIRONMENT === 'checkout')
		{
			$postUrl = $this->config->item ( 'cp_host' );
			$postData ['HOST'] = $this->config->item ( 'domain' );
		} else
		{
			// $postUrl = $this->config->item('pages_url');
			$postUrl = $this->config->item ( 'protocol' ) . $this->config->item ( 'pages_url' );
		}
		
		$attendResult = $this->tools->request ( $postUrl . 'api/activity/attend', $postData );
		$attendResult = json_decode ( $attendResult, true );
		
		if ($attendResult ['status'] == '200')
		{
			$result = array (
					'status' => '200',
					'msg' => '恭喜，领取成功',
					'data' => '' 
			);
		} else
		{
			$result = array (
					'status' => '300',
					'msg' => $attendResult ['msg'],
					'data' => '' 
			);
		}
		echo json_encode ( $result );

        
    }
    
    public function join818()
    {
    	$cpk = intval($this->input->get("cpk"));
    	$this->load->model('channel_model', 'Channel');
    	$channel = $this->Channel->getById($cpk);
    	$downHref = '//888.166cai.cn/app/download/?c=10047';
    	if (isset($channel['app_path']) && $channel['app_path']) $downHref = $channel['app_path'];
    	$this->load->view('/mobileview/activity/join818', compact('downHref'));
    }
    
    /**
     * 新年活动
     */
    public function xnhk()
    {
        if ($this->uid)
        {
            $uinfo = $this->user_model->getUserInfo($this->uid);
        }
        $activity = $this->activity_model->getTimeById(13);
        $remark = $activity['delete_flag'] == '0' ? 2 : $activity['delete_flag'];
        $data = array(
            'awards' => empty($uinfo) ? array() : $this->activity_model->getChjLogs($uinfo['uid'], 13),
            'uid' => $this->uid,
            'chj' => empty($uinfo) ? array('total_num' => 0, 'left_num' => 0) : $this->activity_model->getChjUser($uinfo['uid'], 13),
            'url' => $this->config->item('protocol') . $this->config->item('base_url') . 'activity/xnhkRegister/' . $uinfo['uid'],
            'remark' => $remark,
            'prizeList' => $this->activity_model->getPrizeList(13),
            'token' => urlencode($this->strCode(json_encode(array('uid' => $uinfo['uid'])), 'ENCODE')),
            'imgurl' => $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/active/heka/wxfx.png',
            'endTime' => $activity['endTime'],
        );
        
        $this->load->view('/activity/xnhk', $data);
    }
    
    /**
     * 新年抽奖
     */
    public function xnchj()
    {
        $this->load->model('activity_xn_model');
        $res = $this->activity_xn_model->draw($this->uid, 2, 13);
        
        die(json_encode($res));
    }
    
    /**
     * 新年活动邀请页
     */
    public function xnhkRegister($uid = null)
    {
        $uinfo = $this->user_model->getUserInfo($uid);
        if (empty($uinfo) || $uinfo['userStatus'])
        {
            $this->redirect('/error/');
        }
        $this->load->model('activity_xn_model');
        $recode =$this->activity_xn_model->countHasLx($uinfo['uid']);
        $this->load->view('/activity/xnhkRegister', compact('uid', 'recode'));
    }
    
    /**
     * 新年活动邀请页
     */
    public function xnhkDoRegister()
    {
        $data = $this->input->post(null, true);
        
        $result = array(
            'status' => '100',
            'msg' => '通讯异常',
            'data' => ''
        );
        
        // 手机格式检查
        $rule = '/1\d{10}$/';
        if (!preg_match($rule, $data['phone']))
        {
            $result = array(
                'status' => '100',
                'msg' => '手机号码格式不正确',
                'data' => array()
            );
            echo json_encode($result);
            exit();
        }
        
        $this->load->model('activity_xn_model');
        $recode =$this->activity_xn_model->countHasLx($data['uid']);
        if ($recode >= 10)
        {
            // 验证码检查
            $this->primarysession->startSession();
            $captcha = $this->primarysession->getArg('captcha');
            $this->primarysession->setArg('captcha', '');
            if ($captcha != strtolower($data['imgCode']))
            {
                $result = array(
                    'status' => '100',
                    'msg' => '验证码不正确',
                    'data' => ''
                );
                echo json_encode($result);
                exit();
            }
        }
        
        $checkResult = $this->activity_xn_model->has166Attend($data['phone']);
        if($checkResult)
        {
            if($checkResult['uid'] > 0)
            {
                $result = array (
                    'status' => '300',
                    'msg' => '仅限新用户领取',
                    'data' => ''
                );
                die(json_encode ($result));
            }
            else
            {
                $result = array (
                    'status' => '500',
                    'msg' => '您已参加过166红包活动',
                    'data' => ''
                );
                die(json_encode ($result));
            }
        }
        
        
        // 组装参数
        $postData = array (
            'phone' => $data ['phone'],
            'platformId' => $this->config->item ( 'platform' ),
            'channelId' => 10267,
            'smsid' => 0,
            'fromUid' => $data ['uid'],
            'activityType' => 'xnhk',
        );
        
        // 请求红包活动接口
        if (ENVIRONMENT === 'checkout')
        {
            $postUrl = $this->config->item ( 'cp_host' );
            $postData ['HOST'] = $this->config->item ( 'domain' );
        }
        else
        {
            // $postUrl = $this->config->item('pages_url');
            $postUrl = $this->config->item ( 'protocol' ) . $this->config->item ( 'pages_url' );
        }
        
        $attendResult = $this->tools->request ( $postUrl . 'api/activity/attend', $postData );
        $attendResult = json_decode ( $attendResult, true );
        
        if ($attendResult ['status'] == '200')
        {
            $result = array (
                'status' => '200',
                'msg' => '恭喜，领取成功',
                'data' => ''
            );
        }
        else
        {
            $result = array (
                'status' => '100',
                'msg' => $attendResult ['msg'],
                'data' => ''
            );
        }
        echo json_encode ( $result );
        
        
    }
    
    /**
     * 彩票学院
     */
    public function college($ename) {
        $this->load->view("/activity/cpxy".$ename);
    }
    
    public function worldcup2018($ename = '') {
        switch ($ename) {
            case 'hb':
                $has = 0;
                $token = '';
                $token = urlencode($this->strCode(json_encode(array('uid' => $this->uid)), 'ENCODE'));
                $data = $this->activity_model->getTimeById(14);
                $isend = $data['endTime'] < date('Y-m-d H:i:s');
                $isbefore = $data['startTime'] > date('Y-m-d H:i:s');
                $agent = 'ios';
                if(!empty($this->uid)) {
                    $uinfo = $this->user_model->getUserInfo($this->uid);
                    $this->load->model('new_activity_model');
                    $has = $this->new_activity_model->hasAttend(14, $uinfo['phone']);
                    $token = urlencode($this->strCode(json_encode(array('uid' => $this->uid)), 'ENCODE'));
                }
                $this->load->view("/mobileview/activity/sjbhb", compact('agent', 'token', 'isend', 'isbefore', 'has'));
                break;
            default:
                $max_pissue = $this->activity_model->getNewPissue(3);
                $money = 0;
                if (time() > strtotime("2018-06-11")) {
                    $jc = $this->activity_model->getJcMoney();
                    if (!empty($jc)) {
                        $money = $jc['money'] / 10;
                    }
                }
                $userName = '';
                if (time() > strtotime($max_pissue['start_time']) && time() < strtotime($max_pissue['end_time'])) {
                    $user = $this->activity_model->getTopUser(3, $max_pissue['max_pissue']);
                    $userName = uname_cut($user['userName'], 2, 2);
                }
                $this->load->driver('cache', array('adapter' => 'redis'));
                $REDIS = $this->config->item('REDIS');
                $hongbao = $this->cache->get($REDIS ['WC_RP_MONEY']);
                if ($hongbao > 0) {
                    if (($hongbao / 100) >= 10000) {
                        $hongbao = round($hongbao / 1000000) . '万';
                    } else {
                        $hongbao = round($hongbao / 100);
                    }
                }
                $hbstatus = 'false';
                $dtstatus = 'false';
                $jcstatus = 'false';
                $time = $_GET['time'] ? strtotime($_GET['time']) : time();
                if ($time >= strtotime("2018-05-14") && $this->uid) {
                    $hbstatus = $this->activity_model->getHbGameStatus($this->uid);
                }
                if ($time >= strtotime("2018-06-11") && $this->uid) {
                    $dtstatus = $this->activity_model->getDtGameStatus($this->uid);
                }
                if ($time >= strtotime("2018-07-16") && $this->uid) {
                    $jcstatus = $this->activity_model->getJcGameStatus($this->uid);
                }
                $this->load->view("/activity/sjb", compact('max_pissue', 'money', 'userName', 'hongbao', 'hbstatus', 'dtstatus', 'jcstatus'));
                break;
        }
    }
    
    public function getwcredpack() {
        if (empty($this->uid)) exit(json_encode(array('status' => '100', 'msg' => '用户未登录', 'data' => '')));
    
        $uinfo = $this->user_model->getUserInfo($this->uid);
        if(empty($uinfo)) exit(json_encode(array('status' => '100', 'msg' => '用户信息获取失败', 'data' => '')));
        $channel = $this->input->post('channel');
        $this->load->library('libredpack');
        $res = $this->libredpack->hongbaoworldcup('attend', array('uid' => $this->uid, 'phone' => $uinfo['phone'], 'platformId' => 2, 'channel' => $this->recordChannel($channel)));
        if ($res[0]) exit(json_encode(array('status' => '200', 'msg' => $res[1], 'data' => $res[2])));
        exit(json_encode(array('status' => isset($res[2]['code']) ? $res[2]['code'] : '400', 'msg' => $res[1], 'data' => $res[2])));
    }
    
    public function worldcup($type, $country = 'deguo') {
        $agent = 'ios';
        if ($type === 'qdxl')
            $this->load->view("mobileview/activity/worldcup/".$country, compact('agent'));
        else
            $this->load->view("mobileview/activity/worldcup".$type, compact('agent'));
    }

    public function dthd()
    {
        //$this->uid = 3;
        $agentInfo = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agentInfo, '2345caipiao/android') == FALSE && strpos($agentInfo, '166cai/iOS') == FALSE) {
            $url = '//8.166cai.cn/activity/dthd?' . $_SERVER["QUERY_STRING"];
            header('Location: ' . $url);
            return;
        }
        $totalvalue = $this->input->get('totalvalue', null);
        $userid = $this->input->get('userid', null);
        $token = urlencode($this->strCode(json_encode(array('uid' => "{$this->uid}")), 'ENCODE'));
        if ($token != urlencode($userid)) {
            $totalvalue = 0;
        }
        $res = $this->activity_model->getQuestionConfig();
        $count = $res[1]['count'];
        $config = $res[0];
        $rid = '-';
        if ($userid) {
            $extras = json_decode($config['extra'], true);
            foreach ($extras as $extra) {
                if ($extra['min'] <= $totalvalue && $totalvalue <= $extra['max']) {
                    $rid = $extra['rid'];
                }
            }
        }
        $has = 0;
        $redpack = array();
        if ($rid != '-') {
            $redpack = $this->activity_model->queryRedpack($rid);
            if (!empty($redpack)) {
                $this->load->library('libredpack');
                $header = $this->getUserAgentInfo();
                $uinfo = $this->user_model->getUserInfo($this->uid);
                $result = $this->libredpack->hongbaoworldcup('sendHongbao', array('uid' => $this->uid, 'phone' => $uinfo['phone'], 'question' => $config['id'], 'rid' => $rid, 'platformId' => 1, 'channel' => $this->recordChannel($header['channel'])));
                if ($result[0] == 300) {
                    $has = 1;
                }
            }
        }
        if ($rid == '-' && $userid && $config['id'] && $this->uid) {
            $rid = $this->activity_model->insertQuestionUser(array('uid' => $this->uid, 'question' => $config['id'], 'rid' => 0));
            if ($rid > 0) {
                $has = 1;
            }
        }
        $this->load->view("/activity/sjbdt", compact('count', 'config', 'token', 'redpack', 'totalvalue', 'userid', 'has'));
    }
    /**
     * 竞猜活动
     */
    public function jchd() {
        $this->load->view("/activity/jchd");
    }
    
    public function jchdShare() {
        $this->load->view("/activity/jchdShare");
    }
    
    public function sharepage(){
        $this->load->view("/activity/sharepage");
    }
    
    public function jdpay($type) {
        switch ($type) {
            case 'newuser':
            default:
                $url = 'javascript:login();';
                if ($this->uid) {
                    $url = 'javascript:bindcard();';
                    if ($this->uinfo['id_card']) $url = '/ios/wallet/recharge/'.urlencode($this->strCode(json_encode(array('uid' => $this->uid, 'checked_idName' => 'payJd')), 'ENCODE'));
                }
                $this->load->view('mobileview/activity/jdpaynewuser', compact('url'));
                break;
        }
    }
}
