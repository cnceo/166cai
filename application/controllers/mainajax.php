<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
require_once APPPATH . '/core/CommonController.php';
class mainajax extends CommonController 
{
	
	public function __construct()
	{
		parent::__construct();
		//加密功能
		$this->pre_decrypt();
		$this->cre_pubkey();
	}
	
	public function getLoginAjax()
	{
		$version = $this->input->post('version', true) ? $this->input->post('version', true) : $this->input->get('version', true);
		$response = array(
			'topBar' => $this->ajaxDisplay('elements/common/header_topbar_notlogin', '', $version),
			'bindPop' => '',
		);
		if($this->is_ajax)
		{
			if (!empty($this->uid))
			{
				$response['topBar'] = $this->ajaxDisplay('elements/common/header_topbar', '', $version);
			}
			
			if (!empty($this->uinfo) && !$this->isBindForRecharge())
			{
				$response['bindPop'] = 1; //未绑定
			}
		}
		echo json_encode($response);
	}
	
	public function captcha($img = true, $key = 'captcha')
	{
		$this->load->library('captcha');
		$this->captcha->doimg();
		$code = $this->captcha->getCode();
		$this->primarysession->setArg($key, $code);
		if ($img)
			$this->captcha->outPut();
		else
			return $code;
	}
	
	//二维码
	public function qrCode($content, $size = 7)
	{
		require_once APPPATH . '/libraries/phpqrcode.php';
		$content = base64_decode(urldecode($content));
		QRcode::png($content, false, QR_ECLEVEL_L, $size, 0);
	}

	//二维码
	public function qrCode2($size = 4.5)
	{
		$url = $this->input->get('url');
		require_once APPPATH . '/libraries/phpqrcode.php';
		$content = urldecode($url);
		QRcode::png($content, false, QR_ECLEVEL_L, $size, 0);
	}
	
	/**
	 * ajax 登录
	 */
	public function login()
	{
		$result = array(
			'code' => 1,
			'msg'  => '密码错误，你可以通过手机号码来<a href="/safe/findPword">找回密码</a>'
		);
		$username = $this->input->post('username', true);
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['CHECK_LIMIT']}login_{$username}";
		$loginCount = $this->cache->get($ukey);
		if ($loginCount > 20) {
			$result['code'] = 2;
			$result['msg'] = '登录尝试次数过多，请24小时后重新登录！';
			$this->ajaxResult($result);
		}
		
		$validate = $this->input->post('validate', true);
		$this->load->library('NECaptcha');
		if ($this->primarysession->getArg('loginError') >= 3 || $this->input->cookie('needCaptcha')) {
		    $this->FreshCookie('needCaptcha', true);
		    if (empty($validate) || !$this->necaptcha->verifier($validate)) {
		        $result['code'] = 2;
		        $result['msg'] = '请先滑动验证码完成校验';
		        $this->ajaxResult($result);
		    }
		}
		
		$pword = $this->input->post('pword', true);
		if(empty($username) || empty($pword))
		{
			$result['code'] = 2;
			$result['msg'] = '请输入用户名或密码';
			$this->ajaxResult($result);
		}
		$this->load->model('user_model');
		$user = $this->user_model->getLogin($username, md5($pword));
		$loginError = $this->primarysession->getArg('loginError');
		if(!$user)
		{
			if (empty($loginCount)) {
				$this->cache->save($ukey, 1, 86400);
			}else {
				$this->cache->increment($ukey);
			}
			$this->primarysession->setArg('loginError', $loginError + 1);
			$this->ajaxResult($result);
		}

		// 检查用户状态
		$userInfo = $this->user_model->getUserInfo($user['uid']);
		if(isset($userInfo['userStatus']) && $userInfo['userStatus'] == '1')
		{
			$result = array(
				'code' => 3,
				'msg'  => '该账户已注销，如有疑问请联系<a target="_blank" href="http://wpa.b.qq.com/cgi/wpa.php?ln=1&key=XzkzODE3OTU2M18zODkzMDBfNDAwNjkwNjc2MF8yXw">在线客服</a>'
			);
			$this->primarysession->setArg('loginError', $loginError + 1);
			$this->ajaxResult($result);
		}
		$this->cache->delete($ukey);
		//统计登录方式
		$ctype = ($username === $user['uname']) ? 'uname' : 'phone';
		$ccount = unserialize($this->cache->get($REDIS['CLICK_COUNT']));
		$ccount[$ctype]++;
		$this->cache->save($REDIS['CLICK_COUNT'], serialize($ccount), 0);
		
		$uData = array('uid' => $user['uid'], 'last_login_time' => date('Y-m-d H:i:s'), 'visit_times' => 1);
		$this->user_model->SaveUser($uData);
		//登录记录
		$loginRecord = array('login_time' => $uData['last_login_time'], 'uid' => $user['uid'], 'ip' => UCIP, 'area' => $this->tools->convertip(UCIP), 'reffer' => REFE);
		$this->user_model->loginRecord($loginRecord);
		$sid = $this->calcSessionId($user['passid'], $user['uid'], $user['uname'], $uData['last_login_time']);
		$this->SetCookie($user['uname'], $user['passid'], $user['uid'], 0, $uData['last_login_time'], $sid);
		//消息入队
		$this->load->library('common_stomp_send');
		$this->common_stomp_send->login(array('uid' => $user['uid'], 'last_login_time' => $userInfo['last_login_time']));
		$result['code'] = 0;
		$result['msg'] = '登录成功';
		$result['udata'] = array('iseml' => !empty($userInfo['email']), 'uname' => $userInfo['uname']);
		// 回调地址
		$result['url'] = '/';
		$reffer = $this->primarysession->getArg('reffer');
		if(!empty($reffer))
		{
			$result['url'] = $reffer;
			$this->primarysession->setArg('reffer', '');
		}
		$this->ajaxResult($result);
	}
	
	/**
	 * ajax注册
	 */
	public function register()
	{
		$result = array(
				'code' => 1,
				'msg'  => '系统升级维护中'
		);
		$this->ajaxResult($result);
		$captcha = $this->input->post('phoneCaptcha', true);
		$phone = $this->input->post('phone', true);
		$checkpwd = $this->input->post('checkpwd', true);
		
		$this->load->model('forbid_ip_model');
		if ($this->forbid_ip_model->countNum(UCIP)) {
			$result['code'] = 5;
			$result['msg'] = '系统繁忙，请稍后再试';
			$this->ajaxResult($result);
		}
		
		$capthaRes = $this->checkCaptcha($captcha, $phone, 'registerCaptcha');
		if($capthaRes)
		{
			$result['code'] = 2;
			$result['msg'] = '验证码错误';
			$result['needfrsh'] = 0;
			if ($capthaRes === 2) {
				$result['needfrsh'] = 1;
			}
			$this->ajaxResult($result);
		}
		$pword = $this->input->post('pword', true);
		$con_pword = $this->input->post('con_pword', true);
		if(empty($checkpwd) && (empty($con_pword) || ($pword != $con_pword)))
		{
			$result['code'] = 3;
			$result['msg'] = '密码与确认密码不同';
			$this->ajaxResult($result);
		}
		$this->load->model('user_model');
		if($this->user_model->getRegister($phone))
		{
			$result['code'] = 4;
			$result['msg'] = '手机号已被使用';
			$this->ajaxResult($result);
		}
		$activity_id = $this->input->post('activity_id', true);
		$activity_id = empty( $activity_id ) ? 0 : $activity_id;
        $uniqid = substr(uniqid(), 0, 9);
        $pwd = md5($pword) . $uniqid;
        $user = array(
        	'passid' => 0,
            'salt' => $uniqid,
        	'pword' => strCode($pwd, 'ENCODE'),
        	'reg_type' => 0,
       		'reg_reffer' => REFE,
         	'activity_id' => $activity_id,
         	'reg_ip' => UCIP,
         	'last_login_time' => date('Y-m-d H:i:s'),
         	'visit_times' => 1,
       		'channel' => $this->getChannelId()
    	);
		 
		$result = $this->user_model->doRegister($phone, $user);
		if($result['code'])
		{
			//登录记录
			$loginRecord = array('login_time' => $user['last_login_time'], 'uid' => $result['uid'], 'ip' => UCIP, 'area' => $this->tools->convertip(UCIP), 'reffer' => REFE);
			$this->user_model->loginRecord($loginRecord);
			$sid = $this->calcSessionId($user['passid'], $result['uid'], $result['uname'], $user['last_login_time']);
			$this->SetCookie($result['uname'], $user['passid'], $result['uid'], 0, $user['last_login_time'], $sid);
			
			if ($this->input->cookie('cpk')) {
				$this->user_model->saveCpkUser($this->input->cookie('cpk'), $result['uid']);
			}
			
			$result['code'] = 0;
			$result['msg'] = '注册成功';
			$this->load->library('libredpack');
			$this->libredpack->hongbao166('register', array('phone' => $phone, 'uid' => $result['uid'], 'platformId' => 0, 'channel' => $user['channel']));
			
			//联盟添加二级用户
			$rebateId = $this->input->cookie('rebateId');
			if($rebateId)
			{
				$this->load->model('rebates_model');
				$this->rebates_model->RegAddRebate($result['uid'], $rebateId);
			}
			//拉新活动
			$this->load->model('activity_lx_model');
			$this->activity_lx_model->regAdd($result['uid'], $phone);
			//消息入队
			$this->load->library('common_stomp_send');
			$this->common_stomp_send->login(array('uid' => $result['uid'], 'last_login_time' => 0));
		}
		
		$this->ajaxResult($result);
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
	
	/**
	 * 退出登录
	 */
	public function loginout()
	{
		$domain = str_replace('www.', '', $this->config->item('domain'));
		$this->load->helper('cookie');
		delete_cookie('I', $domain, '/');
		delete_cookie('name_ie', $domain, '/');
		delete_cookie('need_modify_name', $domain, '/');
	}
	
	/**
	 * 检查用户信息合法性
	 */
	public function uinfoCheck()
	{
		$checkData = $this->input->post(null, true);
		$PostData = array();
		if (!empty($checkData))
		{
			foreach ($checkData as $key => $val)
			{
				if(!in_array($key, array('encrypt')))
				{
					$PostData['type'] = $key;
					$PostData[$key] = $val;
				}
			}
		}
		switch ($PostData['type']) 
		{
			case 'username':
				$result = 0;
				if($this->ucheck($PostData[$PostData['type']]))
				{
					$result = 1;
				}
				$this->load->model('user_model');
				if($this->user_model->checkUserUnique('uname', $PostData[$PostData['type']]))
				{
					// 账户已存在，账户是否注销
					if($this->user_model->checkUserInfoLocked('uname', $PostData[$PostData['type']]))
					{
						$result = 3;
					}
					else
					{
						$result = 2;
					}
				}
				echo $result;
				break;
			case 'phone':
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
				break;
			case 'findpwd':
				$result = 1;
				$this->load->model('user_model');
				if($this->user_model->isPhoneRepeat($PostData[$PostData['type']]))
				{
					// 账户是否注销
					if($this->user_model->isPhoneLocked($PostData[$PostData['type']]))
					{
						$result = 0;
					}		
				}
				echo $result;
				break;
			case 'modifyPhone':
				$result = 0;
				$this->load->model('user_model');
				if($this->user_model->isPhoneRepeat($PostData[$PostData['type']]))
				{
					$result = 1;
				}
				echo $result;
				break;
			case 'id_card':
				$this->load->model('user_model');
				echo $this->user_model->isIdCardRepeat($PostData[$PostData['type']]);
				break;
			case 'pay_pwd':
				$result = 0;
				$pay_pwd = $PostData[$PostData['type']];
				if ($this->uinfo['pword'] == md5($pay_pwd)) {
					$result = 1;
				}
				echo $result;
				break;
			case 'email':
				$this->load->model('user_model');
				echo $this->user_model->checkEmailBind($PostData[$PostData['type']]);
				break;
			case 'validate_email':
				$result = 1;
				if( $PostData[$PostData['type']] == $this->uinfo['email'] ){
					$result = 0;
				}
				echo $result;
				break;
			case 'validate_idcard':
				$result = 1;
				if( $PostData[$PostData['type']] === $this->uinfo['id_card'] ){
					$result = 0;
				}
				echo $result;
				break;
			case 'real_name':
				$result = 1;
				if( $PostData[$PostData['type']] == $this->uinfo['real_name'] ){
					$result = 0;
				}
				echo $result;
				break;
			case 'imgCaptcha':
			case 'captcha':
				$result = 1;
				$code = $this->primarysession->getArg('captcha');
				if(strtolower($PostData[$PostData['type']]) == $code)
				{
					$result = 0;
				}else {
					$this->primarysession->setArg('captcha', '');
				}
				echo $result;
				break;
			case 'phoneCaptcha':
				$code = $this->primarysession->getArg('phoneCaptcha');
				$codestr = explode(':', $code);
				if ( (empty($PostData[$PostData['type']]) || $codestr[1] < time() || $PostData[$PostData['type']] != $codestr[0]))
				{
					$result = 1;
				}
				else
				{
					$result = 0;
				}
				echo $result;
				break;
			case 'lxcheck':
				$result = 0;
				$this->load->model('user_model');
				if($this->user_model->isPhoneRepeat($PostData[$PostData['type']]))
				{
					$result = 1;
				}
				else
				{
					// 是否参与拉新活动
					$this->load->model('activity_lx_model');
					if($this->activity_lx_model->isJoinLx($PostData[$PostData['type']]))
					{
						$result = 2;
					}
				}
				echo $result;
				break;
			case 'wechatPhone':
				$result = 0;
				$this->load->model('user_model');
				$wxInfo = $this->user_model->getUserInfoByPhone($PostData[$PostData['type']]);
				if(!empty($wxInfo['wx_unionid']))
				{
					$result = 1;
				}
				elseif($this->user_model->isPhoneRepeat($PostData[$PostData['type']]))
				{
					// 账户是否注销
					if(!$this->user_model->isPhoneLocked($PostData[$PostData['type']]))
					{
						$result = 2;
					}
				}	
				echo $result;
				break;
			default:
				break;
		}
	}
	
	/**
	 * 用户名验证
	 * @param unknown_type $uname
	 * @return boolean
	 */
	private function ucheck($uname)
	{
		// 小于2个字符, 或字符超出给定范围
		if (!preg_match('/^[\d\w_\p{Han}]+$|^\w+[\w]*@[\w]+\.[\w]+$/u', $uname) || (preg_match('/^[0-9]*$/u', $uname))) {
			return true;
		}
		if (strlen($uname) > 24 || strlen($uname) < 2) {
			return true;
		}
		return false;
	}
	
	/**
	 * 显示、隐藏money
	 */
	public function hideMoney()
	{
		$res = array(
			'code' => 1,
			'data' => ''
		);
		$data = $this->input->post(null, true);
		if($data['uid'] && $data['version'])
		{
			$this->load->model('user_model');
			$result = $this->user_model->updateMoneyHide($data['uid'], $data['hide']);
			if($result)
			{
				$this->uinfo = $this->user_model->getUserInfo($data['uid']);
				$res['code'] = 0;
				$res['data'] = $this->ajaxDisplay('elements/common/header_topbar', '', $data['version']);
			}
		}
		$this->ajaxResult($res);
	}
	
	/**
	 * 修改用户名操作
	 */
	public function modifyName()
	{
		$result = array(
			'status' => '0',
			'msg' => '操作失败',
		);
		$uname = $this->input->post('username', true);
		if((!$this->is_ajax) || (!$this->uid) || (empty($uname)))
		{
			$this->ajaxResult($result);
		}
		
		if($this->ucheck($uname))
		{
			$result['msg'] = '抱歉，用户名格式不支持';
			$this->ajaxResult($result);
		}
		
		if($this->uinfo['nick_name_modify_time'] > 0)
		{
			$result['msg'] = '抱歉，用户名只允许修改一次';
			$this->ajaxResult($result);
		}
		
		$this->load->model('user_model');
		if($this->user_model->checkUserUnique('uname', $uname))
		{
			$result['msg'] = '该用户名已被使用，请换一个试试';
			$this->ajaxResult($result);
		}
		
		// 修改用户名
		$userData = array(
			'uid' => $this->uid,
			'uname' => $uname,
			'nick_name_modify_time' => date('Y-m-d H:i:s')
		);
		
		$res = $this->user_model->updateUname($userData);
		if($res)
		{
			$result = array(
				'status' => '1',
				'msg' => '操作成功',
			);
			$user = $this->user_model->getUserInfo($this->uid);
			$sid = $this->calcSessionId($user['passid'], $this->uid, $user['uname'], $user['last_login_time']);
			$this->SetCookie($user['uname'], $user['passid'], $this->uid, 0, $user['last_login_time'], $sid);
		}
		
		$this->ajaxResult($result);
	}
}
