<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity extends MY_Controller {

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

    // 开售送红包活动
    public $activityId = array(
        '188hb' => '1', //188红包
    );

    /**
     * 彩票后台入口控制器
     */
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->redirect('/');
    }

    public function caijin( $activity = '') 
    {
        $this->load->model('activity_model');

		$dispatchs = $this->activity_model->getDispatch( $activity );
		if(empty($dispatchs)) $this->redirect('/');

        $this->load->model('safe_model');
        $vdata['provinceList'] = $this->safe_model->getProvince();
        $vdata['bankTypeList'] = $this->bankTypeList;
        $vdata['activityId'] = $activity;
        $vdata['isTakeMoney'] = false;
        
        if( !$this->activity_model->getIsdispatch( $this->uid, $activity ) ) $vdata['isTakeMoney'] = true;

        $this->displayLess('v1.1/activity/takeMoney', $vdata);
        /*
        if( method_exists( $this, 'm' . $activity ) )
        {
            return call_user_func_array( array($this, 'm' . $activity ), array() );
        }
        */
    }
    
    public function jczq()
    {
    	$this->load->view('v1.1/activity/jczq');
    }

    public function springstopselling()
    {
        $this->load->view('v1.1/activity/springstopselling');
    }

    public function o2o()
    {
    	$this->load->view('v1.1/activity/o2o', 
    			array('version' => 'v1.1', 
    				'pagesUrl' => $this->config->item('pages_url'),
    				'position' => $this->config->item('POSITION'),
    			)
    	);
    }

    public function newmode()
    {
        $this->load->view('v1.1/activity/newmode');
    }
    
	public function jclq()
    {
    	$this->load->view('v1.1/activity/jclq');
    }
    
	public function dggp()
    {
    	$this->load->view('v1.1/activity/dggp');
    }
    public function dantuo()
    {
        $this->load->view('v1.1/activity/dantuo');
    }

    public function dingdanshahao()
    {
        $this->load->view('v1.1/activity/dingdanshahao');
    }

    public function chart()
    {
        $this->load->view('v1.1/activity/chart');
    }
    
    public function ypjx()
    {
        $this->load->view('v1.1/activity/ypjx');
    }
    
    public function note()
    {
        $this->load->view('v1.1/activity/note');
    }
    
    public function jzjq()
    {
        $this->load->view('v1.1/activity/jzjq');
    }
    
    public function fucai3d()
    {
        $this->load->view('v1.1/activity/fucai3d');
    }
    public function nba()
    {
        $this->load->view('v1.1/activity/nba');
    }
    
    public function kxzhuihao()
    {
        $this->load->view('v1.1/activity/kxzhuihao');
    }
    
    public function kxzhuihao3()
    {
        $this->load->view('v1.1/activity/kxzhuihao3');
    }
    
    public function league()
    {
        $this->load->view('v1.1/activity/league');
    }
    
    public function fwcn()
    {
    	$url_prefix = $this->config->item('url_prefix');
    	$urlprefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';
    	$this->load->view('v1.1/activity/fwcn', compact('urlprefix'));
    }
    
    public function dispatchMoney() 
    {
        if ($this->is_ajax) 
        {
            if( false && (empty( $this->uid) ||
                empty( $this->uinfo['bank_id'] ) ||
                empty( $this->uinfo['phone'] ) || 
                empty( $this->uinfo['id_card'] ) ) ) 
            {
                echo 2;
                return ;
            }

            $acid = $this->input->post('acid', true);
            $this->load->model('activity_model');
            
            if( !$this->activity_model->getIsdispatch( $this->uid, $acid ) ) 
            {
                echo 3;
                return;
            }
            
            if( $this->activity_model->dispatch( $this->uid, $acid ) == 0 )
            {
                echo 1;
                return ;
            }
            else
            {
                echo 0;
            }
        }
    }

    public function sendSms()
    {
        $this->load->model('user_model');
        $ok = true;
        $msg = "";
        $uid = $this->input->post('uid', TRUE);
        $vdata = array(
        );
        $type = '166_huodong';
        $tel_num = $this->input->post('tel_num', TRUE);
        $uip = UCIP;
        if(!preg_match('/^[1][3-8]+\d{9}/', $tel_num) || strlen($tel_num) != 11)
        {
            $ok = false;
            $msg = "请输入正确手机号码！" ;
        }
        elseif($this->user_model->isOldIp($uip))
        {
            $ok = false;
            $msg = "发送太频繁，请稍后再试！$uip" ;
        }
        elseif($this->user_model->isThreeTimes($tel_num,$type))
        {
            $ok = false;
            $msg = "每手机号码单日仅可发送三次！请使用其他方式！" ;
        }
        elseif ($this->user_model->isInFiveMinute($tel_num))
        {
            $ok = false;
            $msg = "发送太频繁，请稍后再试！" ;
        }
        else
        {
			$position = $this->config->item('POSITION');
            $this->user_model->sendSms($uid, $vdata, $type, $tel_num, $uip, $position['166_huodong']);
        }
        echo json_encode(compact('ok', 'msg'));
    }

    // 检查用户是否已参与活动
    public function check188hb()
    {
        $result = FALSE;    // 未参与
        // 检查用户是否已参与
        if(!empty($this->uid))
        {
            // 获取用户信息
            $uinfo = $this->user_model->getUserInfo($this->uid);

            // 组装参数
            $postData = array(
                'activityId' => $this->activityId['188hb'],
                'phone' => $uinfo['phone']
            );

            // 请求红包活动接口
            if(ENVIRONMENT === 'checkout')
            {
                $postUrl = $this->config->item('cp_host');
                $postData['HOST'] = $this->config->item('domain');
            }
            else
            {
                $postUrl = $this->config->item('pages_url');
            }

            $attendResult = $this->tools->request($postUrl . 'api/activity/hasAttend', $postData);
            $attendResult = json_decode($attendResult, true);

            // 已参与
            if($attendResult['status'] == '200')
            {
                $result = TRUE;
            }
        }

        return $result;
    }

    // 通过手机号码领取红包
    public function get188hb()
    {
        $data = $this->input->post(null, true);

        $result = array(
            'status' => '0',
            'msg' => '通讯异常',
            'data' => ''
        );

        // 手机格式检查
        $rule = '/1\d{10}$/';
        if (!preg_match($rule, $data['phone']))
        {
            $result = array(
                'status' => '0',
                'msg' => '手机号码格式不正确',
                'data' => array()
            );
            echo json_encode($result);
            exit();
        }

        // 验证码检查
		$codestr = $this->primarysession->getArg('newphoneyzm');
        $codestr = explode(':', $codestr);
        if((empty($data['phoneCaptcha']) || $codestr[1] < time() || $data['phoneCaptcha'] != $codestr[0]))
        {
            $result = array(
                'status' => '0',
                'msg' => '请输入正确的验证码!',
                'data' => array()
            );
            echo json_encode($result);
            exit();
        }
        elseif ($data['phone'] != $codestr[3])
        {
        	$result = array(
        			'status' => '0',
        			'msg' => '手机号码错误!',
        			'data' => array()
        	);
        	echo json_encode($result);
        	exit();
        }
        else
        {
            // 检查是否领过
            $checkData = array(
                'activityId' => $this->activityId['188hb'],
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
                $postUrl = $this->config->item('pages_url');
            }

            $checkResult = $this->tools->request($postUrl . 'api/activity/hasAttend', $checkData);
            $checkResult = json_decode($checkResult, true);

            // 已参与
            if($checkResult['status'] == '200')
            {
                $result = array(
                    'status' => '0',
                    'msg' => '该手机已参加过活动',
                    'data' => 'attented'
                );
                echo json_encode($result);
                exit();
            }

            // 组装参数
            $postData = array(
                'activityId' => $this->activityId['188hb'],
                'phone' => $data['phone'],
                'platformId' => '0',
                'channelId' => $this->getChannelId()
            );

            // 请求红包活动接口
            if(ENVIRONMENT === 'checkout')
            {
                $postUrl = $this->config->item('cp_host');
                $postData['HOST'] = $this->config->item('domain');
            }
            else
            {
                $postUrl = $this->config->item('pages_url');
            }

            $attendResult = $this->tools->request($postUrl . 'api/activity/attend', $postData);
            $attendResult = json_decode($attendResult, true);

            if($attendResult['status'] == '200')
            {
            	$this->primarysession->setArg('newphoneyzm', '');
                $result = array(
                    'status' => '1',
                    'msg' => '参加成功',
                    'data' => ''
                );
            }
            else
            {
                $result = array(
                    'status' => '0',
                    'msg' => $attendResult['msg'],
                    'data' => ''
                );
            }
            echo json_encode($result);
        }

    }
    
    public function jcwzbp($id = null)
    {
    	$this->load->model('jcmatch_model');
    	$matchInfo = $this->jcmatch_model->getActivityDetail($activity_id = '3', $id);
    	if (empty($matchInfo)) $this->redirect('/activity/jcwzbp');
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$matches =  json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);
    	$matchDetail = $matches[$matchInfo[0]['mid']];
    	
    	$bonus = 0;
    	foreach ($matchInfo as $val) {
    		$bonus += $val['bonus'];
    	}
    	if ($matchInfo[0]['status'] > 0 || empty($matchDetail)) {
    		$matchDetail = $this->jcmatch_model->getMatchInfo($matchInfo[0]['mid']);
    		$matchInfo[0]['full_score'] = $matchDetail['full_score'];
    	}
    	$data = array('info' => $matchInfo, 'detail' => $matchDetail, 'bonus' => $bonus);
    	$data['showBind'] = false;
    	if (!empty($this->uinfo) && !$this->isBindForRecharge()) $data['showBind'] = true;
    	if ($matchInfo[0]['activity_issue'] > 2) {
    		$this->load->view('v1.1/activity/jcwzbpcn', $data);
    	}else {
    		$this->load->view('v1.1/activity/jcwzbp', $data);
    	}
    }
    
    public function jcbuy()
    {
    	$post = $this->input->post(null, true);
    	
    	$this->load->model('jcmatch_model');
    	$orderData = array(
    			'uid' => $this->uid,
    			'userName' => $this->uname,
    			'ctype' => $post['ctype'],   // create pay
    			'buyPlatform' => '0',
    			'codes' => $post['codes'],
    			'lid' => '42',
    			'money' => $post['money'],
    			'multi' => $post['multi'],
    			'issue' => $post['issue'],
    			'playType' => $post['playType'],
    			'isChase' => '0',
    			'betTnum' => '1',
    			'codecc' => $post['codecc'],
    			'endTime' => $post['endTime'],
    			'activity_id' => $post['activity_id'],
    			'activity_issue' => $post['activity_issue'],
    			'channel' => $this->getChannelId()
    	);
    	$result = $this->jcmatch_model->doPay($orderData);
    	exit(json_encode($result));
    }
    
    public function euroxl()
    {
    	$this->load->model('gjc_model');
    	$teams = $this->gjc_model->getTeams();
    	$data = array();
    	foreach ($teams as $team) {
    		$data['team'][$team['logo']] = array(
    			'odds' => $team['odds']
    		);
    	}
    	$this->load->view('v1.1/activity/euroxl', $data);
    }
    
    public function eurointro()
    {
    	$this->load->view('v1.1/activity/eurointro');
    }

    /*
     * 加奖活动
     */
    public function jcjj($id = null)
    {
    	$this->load->model('jjactivity_model');
    	$matchInfo = $this->jjactivity_model->getActivityDetail($id);
    	$aStatus = '0';
    	if(!empty($matchInfo['startTime']) && !empty($matchInfo['endTime'])) {
    		$now = date('Y-m-d H:i:s');
    		if($now >= $matchInfo['startTime'] && $now <= $matchInfo['endTime'] && $matchInfo['status'] == '0') {
    			$aStatus = '1';
    		} elseif($now > $matchInfo['endTime']) {
    			$aStatus = '2';
    		}
    	}
    	$js = 'zmjj';
        $this->load->view('v1.1/activity/jcjj', compact('aStatus', 'js'));
    }  
    
    /*
     * 加奖活动
    */
    public function ecyjj($id = null)
    {
    	$this->load->model('jjactivity_model');
    	$matchInfo = $this->jjactivity_model->getActivityDetail($id);
    	$aStatus = '0';
    	if(!empty($matchInfo['startTime']) && !empty($matchInfo['endTime'])) {
    		$now = date('Y-m-d H:i:s');
    		if($now >= $matchInfo['startTime'] && $now <= $matchInfo['endTime'] && $matchInfo['status'] == '0') {
    			$aStatus = '1';
    		} elseif($now > $matchInfo['endTime']) {
    			$aStatus = '2';
    		}
    	}
    	$js = 'ecyjj';
    	$this->load->view('v1.1/activity/jcjj', compact('aStatus', 'js'));
    }

    // 获取竞彩足球热门赛事
    public function getJczqMatch($num = 3)
    {
        $matchInfo = array();
        // 竞足投注缓存
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $matches =  json_decode($this->cache->redis->get($REDIS['JCZQ_MATCH']), TRUE);

        if(!empty($matches))
        {
            $count = 0;
            $hotMatch = array();
            $hotIdArry = array();
            foreach ($matches as $match) 
            {
                if( empty($match['spfGd']) && empty($match['rqspfGd']) && empty($match['bqcGd']) && empty($match['jqsGd']) && empty($match['bfGd']) && empty($match['spfFu']) && empty($match['rqspfFu']) && empty($match['bqcFu']) && empty($match['jqsFu']) && empty($match['bfFu']) )
                {
                    // 过滤该场比赛
                }
                else
                {
                    if($match['hot'] == '1')
                    {
                        if($match['hotid'] == 0)
                        {
                            $match['hotid'] = 10;
                        }
                        $hotIdArry[] = $match['hotid'];
                        array_push($hotMatch, $match);
                    }
                }
            }

            if(!empty($hotMatch))
            {
                // 排序
                array_multisort($hotIdArry, SORT_ASC, $hotMatch);

                foreach ($hotMatch as $items) 
                {
                    if($count < $num)
                    {
                        array_push($matchInfo, $items);
                        $count ++;
                    }
                }
            }

            if($count < $num)
            {
                foreach ($matches as $match) 
                {
                    if( empty($match['spfGd']) && empty($match['rqspfGd']) && empty($match['bqcGd']) && empty($match['jqsGd']) && empty($match['bfGd']) && empty($match['spfFu']) && empty($match['rqspfFu']) && empty($match['bqcFu']) && empty($match['jqsFu']) && empty($match['bfFu']) )
                    {
                        // 过滤该场比赛
                    }
                    else
                    {
                        if($match['hot'] == '0' && $count < $num)
                        {
                            array_push($matchInfo, $match);
                            $count ++;
                        }
                    }
                }
            }
        }
        return $matchInfo;
    }
    public function caxl() 
    {
    	$this->load->view('v1.1/activity/caxl');
    }
    
    public function euroschedue()
    {
    	$this->load->view('v1.1/activity/euroschedue');
    }
    
    public function joinus($from_channel_id = 1)
    {
        // 发起渠道
        $from_channel = array(
            1 => 'PC活动页',
            2 => '红包记录页',
            3 => '投注记录页',
            4 => '浮层弹窗',
            // 5 => 'Android',
            6 => '投注页banner',
        );

        $from_channel_id = ($from_channel[$from_channel_id])?$from_channel_id:1;

    	$this->load->model('activity_lx_model');
    	
    	$showBind = 0;
    	if (!empty($this->uinfo) && !$this->isBindForRecharge()) $showBind = 1;
    	$data = array(
    		'list'  => $this->activity_lx_model->getLxRecord(),
    		'users' => (empty($this->uid) && !$showBind) ? array() : $this->activity_lx_model->getJoined($this->uid),
    		'chj' =>  (empty($this->uid) && !$showBind) ? array() : $this->activity_lx_model->getchoujiang($this->uid),
    		'showBind' => $showBind,
    		'info' => $this->activity_lx_model->getInfo(),
    		'url' => $this->uid."/".$from_channel_id.'?cpk=10077'
    	);
    	$this->load->view('v1.1/activity/joinus', $data);
    }
    
    public function chj()
    {
    	$this->load->model('activity_lx_model');
    	$res = $this->activity_lx_model->draw($this->uid, 0, $this->getChannelId());
    	echo json_encode($res);exit();
    }
    
    public function join($uid = null, $channel = null) {
    	
    	$this->load->model('user_model');
    	$this->load->model('activity_lx_model');
    	$uinfo = $this->user_model->getUserInfo($uid);
    	
    	if (empty($uinfo) || $uinfo['userStatus']) $this->redirect('/error/');
    	
    	if (!in_array($channel, array(1, 2, 3, 4, 5, 6))) $channel = 1;
    	
    	$data['uid'] = $uid;
    	$data['channel'] = $channel;
    	$data['position'] = $this->config->item('POSITION');
    	$data['info'] = $this->activity_lx_model->getInfo();
    	
    	$useragent = $_SERVER['HTTP_USER_AGENT'];
    	if( (strpos($useragent, 'Android') !== FALSE && strpos($useragent, 'Mobile') !== FALSE) || strpos($useragent, 'iPhone') !== FALSE){
    		if (strpos($useragent, 'iPhone') !== FALSE) {
    			$data['platform'] = 'ios';
    		}else {
    			$data['platform'] = 'app';
    		}
            $this->load->library('weixin');
            $data['signPackage'] = $this->weixin->getSignPackage();
    		$this->load->view('v1.1/activity/joinapp', $data);
    	}else {
    		$this->load->view('v1.1/activity/join', $data);
    	}
    }
    
    public function joinattend() {
    	$data = $this->input->post(NULL, true);
    	
    	$this->load->model('user_model');
    	$uinfo = $this->user_model->getUserInfo($data['uid']);
    	if (empty($uinfo) || $uinfo['userStatus']) exit(json_encode(array('status' => 2)));
    	
    	if (!in_array($channel, array(1, 2, 3, 4, 5, 6))) $channel = 1;
    	
    	$code = $this->primarysession->getArg('joinCapche');
    	$codestr = explode(':', $code);
    	
    	$res = $this->checkCaptcha($data['joinCapche'], $data['lxcheck'], 'joinCapche');
    	if ($res === 2) {
    		exit(json_encode(array('status' => 2, 'msg' => '请重新获取验证码')));
    	}else if ($res) {
    		exit(json_encode(array('status' => 1, 'msg' => '请输入正确的手机验证码')));
    	}
    		
    	$uid = $data['uid'];
    	$channel = $data['channel'];
    	$this->load->model('activity_lx_model');
    	$res = $this->activity_lx_model->attend($uid, $data['lxcheck'], $channel, $data['tchl']);
    	exit(json_encode($res));
    }

    public function cshLxRecored()
    {
        $this->load->model('activity_lx_model');
        $this->activity_lx_model->cshLxRecored();
    }
    
    public function checkJoinPhone()
    {
    	$result = 0;
    	$this->load->model('user_model');
    	if($this->user_model->isPhoneRepeat($PostData[$PostData['type']]))
    	{
    		// 账户是否注销
    		if(!$this->user_model->isPhoneLocked($PostData[$PostData['type']]))
    		{
    			$result = 2;
    		}else
    		{
    			$result = 1;
    		}
    	}
    	echo $result;
    }
    
    public function fiveLeague($type = '') {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	
    	$lidArr = array(92, 85, 39, 34, 93);
    	foreach ($lidArr as $lid) {
    		$teamTmp = array();
    		$scoreTmp = json_decode($this->cache->get($REDIS['EUROPE_SCORE'].$lid), true);
    		$schedule[] = json_decode($this->cache->get($REDIS['EUROPE_SCHEDULE'].$lid), true);
    		foreach ($scoreTmp as $val) {
    			$teamTmp[$val['tid']] = $val['name'];
    		}
    		$teams [] = $teamTmp;
    		$score[] = $scoreTmp;
    	}
    	$shouye = unserialize($this->cache->redis->get($REDIS['SHOUYE']));
    	for ($i = 0; $i < 5; $i++) {
    		for ($j = 1; $j <= 3; $j++) {
    			$info[$i][$j] = $shouye['wdls'.$i.$j];
    		}
    	}
    	$infotype = array('前瞻', '推荐', '分析');
        if(in_array($type, array('yingchao','xijia','dejia','yijia','fajia')))
        {
            $this->load->view('v1.1/activity/fiveLeague'.$type, compact('score', 'schedule', 'info', 'infotype', 'teams'));
        }
        else
        {
           $this->load->view('v1.1/activity/fiveLeague', compact('score', 'schedule', 'info', 'infotype', 'teams'));
        }
    }
    
    public function zhbzbp() {
    	$this->load->model('activity_model');
    	$activityInfo = $this->activity_model->getActivityInfo(array(6));
    	$this->load->view('v1.1/activity/zhbzbp', array('activityInfo' => $activityInfo[0]));
    }
    
    public function welcometo166() {
        $isLogin = false;
        if ($this->uid) $isLogin = true;
        $this->load->view('v1.1/activity/promoteLand', compact('isLogin'));
    }
    
    public function hmzt() {
    	$this->load->view('v1.1/activity/hemai');
    }
    
    public function xczf()
    {
        $this->load->view('v1.1/activity/xczf');
    }
    
    public function yfqmcp()
    {
        $this->redirect('/weihu', 'location', 301);
    	$this->load->model('activity_model');
    	$time = $this->activity_model->getTimeById(8);
    	$this->load->view('v1.1/activity/yfqmcp', 
    			array('version' => 'v1.1', 
    				'pagesUrl' => $this->config->item('pages_url'),
    				'position' => $this->config->item('POSITION'),
    				'startTime'	=> $time['startTime'],
    				'endTime'	=> $time['endTime'],
    			));
    }
    
    public function sjbshb()
    {
        $this->redirect('/weihu', 'location', 301);
        $this->load->model('activity_model');
        $time = $this->activity_model->getTimeById(8);
        $this->load->view('v1.1/activity/yfqmcp1',
            array('version' => 'v1.1',
                'pagesUrl' => $this->config->item('pages_url'),
                'position' => $this->config->item('POSITION'),
                'startTime'	=> $time['startTime'],
                'endTime'	=> $time['endTime'],
            ));
    }

    public function sjbhb()
    {
        $this->redirect('/weihu', 'location', 301);
        $this->load->view('v1.1/activity/yfqmcp2',
            array('version' => 'v1.1',
                'pagesUrl'  => $this->config->item('pages_url'),
                'position'  => $this->config->item('POSITION'),
            ));
    }

    // 一分钱买彩票
    public function xzcshb()
    {
        $this->redirect('/weihu', 'location', 301);
        $this->load->model('activity_model');
        $time = $this->activity_model->getTimeById(8);
        $this->load->library('WeixinLogin');
        $this->load->view('v1.1/activity/xzcshb', 
            array('version' => 'v1.1', 
                'pagesUrl'  =>  $this->config->item('pages_url'),
                'position'  =>  $this->config->item('POSITION'),
                'baseUrl' => $this->config->item('pages_url'),
                'startTime' =>  $time['startTime'],
                'endTime'   =>  $time['endTime'],
                'qrbLogin'  =>  $this->weixinlogin->qrbLogin(),
            )
        );
    }
    
    public function mobile() {
        $this->load->view('v1.1/activity/mobile');
    }

    public function worldcup2018()
    {
        $this->load->model('activity_model');
        $lists = $this->activity_model->getJzRankLists();
        $data = array();
        $cpk = $this->input->get('cpk');
        if (in_array($cpk, array('10060', '10349', '10350', '10351', '10357', '10359', '10363', '10364', '10365',
            '10366', '10367', '10368', '10369', '10370'))) $wcpopurl =  "/activity/sjbhb?sc{$cpk}";
        foreach ($lists as $k => $list) {
            $data[$k]['ranking'] = $list['rankId'];
            $data[$k]['username'] = uname_cut($list['userName'], 2, 2);
            $data[$k]['prize'] = round($list['margin']/100, 2) . '元';
            $data[$k]['award'] = '彩金' . ($list['addMoney'] / 100) . '元';
        }
        $this->load->view('v1.1/activity/sjb', compact('data'));
        $this->load->view('v1.1/elements/pop-spring', compact('wcpopurl'));
    }

    // 京东支付活动页
    public function jdpay($type = 'newuser')
    {
        // 登录回调地址
        $this->primarysession->setArg('reffer', '/wallet/recharge/jd');
        $this->load->view('v1.1/activity/jdpay', array());
    }
    
    /**
     * 世界杯加奖
     */
    public function sjbjj()
    {
        $this->load->view('v1.1/activity/sjbjj', array('js' => 'sjbjj'));
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */