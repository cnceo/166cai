<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * IOS 注册/登录接口
 * @date:2016-01-18
 */
class Uinfo extends MY_Controller 
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('uinfo_model');
		$this->load->library('tools');
	}

	/*
 	 * IOS 手机号注册接口
 	 * @version:O2O安卓客户端-V1.0
 	 * @prams:phone,password,captcha
 	 * @date:2016-1-19
 	 * @mark:文案修改需谨慎
 	 */
	public function register()
	{
	    $result = array(
	        'status' => '0',
	        'msg' => '系统升级维护中',
	        'data' => array()
	    );
	    echo json_encode($result);
	    exit();
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		
		$this->load->model('forbid_ip_model');
		if ($this->forbid_ip_model->countNum(UCIP)) {
			$result['code'] = 5;
			$result['msg'] = '系统繁忙，请稍后再试';
			echo json_encode($result);
			exit();
		}

		if(empty($data['phone']) || empty($data['password']) || empty($data['captcha']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '账号或密码输入错误',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

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

		// 验证码验证
		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('yycaptcha'));
		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]) || $data['phone'] != $codestr[3]) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('registerError', $this->primarysession->getArg('registerError') + 1);

			if($this->primarysession->getArg('registerError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('yycaptcha', '');
				// 清除错误次数
				$this->primarysession->setArg('registerError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => array()
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码错误',
					'data' => array()
				);
			}
			echo json_encode($result);
			exit();
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 组装用户信息
		$userData = array(
			'phone' => $data['phone'],
			'password' => $data['password'],
			'ip' => UCIP,
			'platform' => $this->config->item('platform'),
			'version' => isset($headerInfo['appVersionName']) ? $headerInfo['appVersionName'] : '1.0',
			'channel' => $this->recordChannel($headerInfo['channel'])
		);

		// 调用注册模块创建用户信息
		$registerStatus = $this->uinfo_model->userRegister($userData);

		if($registerStatus['status'] == '200')
		{
			// 调用活动关联组件
			$activityData = array(
				'uid' => $registerStatus['data']['uid'],
				'phone' => $data['phone'],
				'platformId' => $this->config->item('platform'),
				'channelId' => $this->recordChannel($headerInfo['channel']),
			);
			$this->load->library('activity');
			// 188红包
			$this->activity->regHookBy188($activityData);
			// 拉新活动
			$this->activity->regHookByLx($activityData);

	        //登录记录
        	$refe = REFE ? REFE : '';
        	$loginRecord = array(
        		'login_time'	=>	date('Y-m-d H:i:s'), 
        		'uid' 			=> 	$registerStatus['data']['uid'], 
        		'ip' 			=> 	UCIP, 
        		'area' 			=> 	$this->tools->convertip(UCIP), 
        		'reffer' 		=> 	$refe, 
        		'platform' 		=> 	$this->config->item('platform'),
        		'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
				'version' 		=> 	$headerInfo['appVersionName'],
				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
                        'channel'               =>      $this->recordChannel($headerInfo['channel']),
        	);
        	$this->user_model->loginRecord($loginRecord);

        	// 单设备登录
        	$this->recordIdfaLogin($registerStatus['data']['uid']);

			$uinfo = $this->user_model->getUserInfo($registerStatus['data']['uid']);
			$uinfo['uid'] = $registerStatus['data']['uid'];
			// 汇总用户信息
			$uinfo = $this->callbackUinfo($uinfo);
			$uinfo['auth'] = $this->getUserAuth($uinfo);
			
			// 清除有效验证码
			$this->primarysession->setArg('yycaptcha', '');
			
			$result = array(
				'status' => '1',
				'msg' => '注册成功',
				'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $registerStatus['msg'],
				'data' => array()
			);
		}
		echo json_encode($result);
	}


	/*
 	 * IOS 手机号登录接口
 	 * @version:O2O客户端-V1.0
 	 * @prams:phone,password,captcha
 	 * @date:2016-1-19
 	 * @mark:文案修改需谨慎
 	 */
	public function login()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		// log_message('LOG', "请求参数: " . json_encode($data), 'login');

		// 手机格式检查
		$rule = '/1\d{10}$/';
		if (!preg_match($rule, $data['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '手机号码格式不正确',
				'needCaptcha' => '0',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		$phone = isset($data['phone']) ? $data['phone'] : '';
		$pword = isset($data['password']) ? $data['password'] : '';
		$captcha = isset($data['captcha']) ? $data['captcha'] : '';
		$sessionid = isset($data['sessionid']) ? $data['sessionid'] : '';

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 增加平台、版本、渠道标识
		$data['platform'] = $this->config->item('platform');
		$data['version'] = isset($headerInfo['appVersionName']) ? $headerInfo['appVersionName'] : '1.0';
		$data['channel'] = $this->recordChannel($headerInfo['channel']);

		if($sessionid) $this->primarysession->setSessionId($sessionid);
		$this->primarysession->startSession();

		if(!empty($phone) && !empty($pword))
		{
			if($this->loginCaptchaPass($captcha))
			{
				$loginData = array(
		            'phone' 	=>	$phone,
		            'password'	=>	$pword,
		            'last_login_channel' => $this->recordChannel($headerInfo['channel']),
		        );
				// 调用登录模块验证用户信息
				$loginStatus = $this->uinfo_model->userLogin($loginData);

				if($loginStatus['status'] == '200')
				{
					//登录成功清除error次数
					$this->primarysession->setArg('loginError', 0); 

					//登录记录
	            	$refe = REFE ? REFE : '';
	            	$loginRecord = array(
	            		'login_time'	=>	date('Y-m-d H:i:s'), 
	            		'uid' 			=> 	$loginStatus['data']['uid'], 
	            		'ip' 			=> 	UCIP, 
	            		'area' 			=> 	$this->tools->convertip(UCIP), 
	            		'reffer' 		=> 	$refe, 
	            		'platform' 		=> 	$this->config->item('platform'),
	            		'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
        				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
        				'version' 		=> 	$headerInfo['appVersionName'],
        				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
                                'channel'               =>      $this->recordChannel($headerInfo['channel']),
	            	);
	            	$this->user_model->loginRecord($loginRecord);

	            	// 单设备登录
        			$this->recordIdfaLogin($loginStatus['data']['uid']);

					$uinfo = $this->user_model->getUserInfo($loginStatus['data']['uid']);
					$uinfo['uid'] = $loginStatus['data']['uid'];
					// 汇总用户信息
					$uinfo = $this->callbackUinfo($uinfo);
					$uinfo['auth'] = $this->getUserAuth($uinfo);

					$result = array(
						'status' => '1',
						'msg' => '登录成功',
						'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
					);
				}
				else
				{
					$this->primarysession->setArg('loginError', $this->primarysession->getArg('loginError') + 1);

					$result = array(
						'status' => '-1',
						'msg' => $loginStatus['msg'],
						'needCaptcha' => $this->primarysession->getArg('loginError') >= 3 ? '1' : '0',
						'data' => array()
					);
				}
			}
			else
			{
				$this->primarysession->setArg('loginError', $this->primarysession->getArg('loginError') + 1);

				$result = array(
					'status' => '-1',
					'msg' => '验证码输入错误',
					'needCaptcha' => $this->primarysession->getArg('loginError') >= 3 ? '1' : '0',
					'data' => array()
				);
			}
		}
		else
		{
			$this->primarysession->setArg('loginError', $this->primarysession->getArg('loginError') + 1);

			$result = array(
				'status' => '-1',
				'msg' => '账号或密码输入错误',
				'needCaptcha' => $this->primarysession->getArg('loginError') >= 3 ? '1' : '0',
				'data' => array()
			);
		}
		echo json_encode($result);
	}

	/*
 	 * IOS 短信登录接口
 	 * @version:O2O客户端-V1.0
 	 * @prams:phone,password,captcha
 	 * @date:2016-1-19
 	 * @mark:文案修改需谨慎
 	 */
	public function smsLogin()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		// log_message('LOG', "请求参数: " . json_encode($data), 'smsLogin');

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

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 增加平台、版本、渠道标识
		$data['platform'] = $this->config->item('platform');
		$data['version'] = isset($headerInfo['appVersionName']) ? $headerInfo['appVersionName'] : '1.0';
		$data['channel'] = $this->recordChannel($headerInfo['channel']);

		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('yycaptcha'));

		// log_message('LOG', "请求参数: " . json_encode($codestr), 'smsLogin');

		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]) || $data['phone'] != $codestr[3] )
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('smsLoginError', $this->primarysession->getArg('smsLoginError') + 1);

			if($this->primarysession->getArg('smsLoginError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('yycaptcha', '');
				// 清除错误次数
				$this->primarysession->setArg('smsLoginError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => array()
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码输入错误',
					'data' => array()
				);
			}
		}
		else
		{

			$registerInfo = $this->uinfo_model->getRegisterByPhone($data['phone']);

			if(!empty($registerInfo))
			{
				//登录记录
            	$refe = REFE ? REFE : '';
            	$loginRecord = array(
            		'login_time'	=>	date('Y-m-d H:i:s'), 
            		'uid' 			=> 	$registerInfo['id'], 
            		'ip' 			=> 	UCIP, 
            		'area' 			=> 	$this->tools->convertip(UCIP), 
            		'reffer' 		=> 	$refe,
            		'login_type' 	=> 	2,
            		'platform' 		=> 	$this->config->item('platform'),
            		'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
    				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
    				'version' 		=> 	$headerInfo['appVersionName'],
    				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
                        'channel'               =>      $this->recordChannel($headerInfo['channel']),
            	);
            	$this->user_model->loginRecord($loginRecord);
            	
            	$uinfo = $this->user_model->getUserInfo($registerInfo['id']);

            	// 单设备登录
        		$this->recordIdfaLogin($registerInfo['id']);

            	//访问次数统计
            	$this->user_model->saveUser(
	                array(
	                    'uid' => $registerInfo['id'], 
	                    'last_login_time' => date('Y-m-d H:i:s'), 
	                    'last_login_channel' => $this->recordChannel($headerInfo['channel']),
	                    'visit_times' => 1
	                )
	            );

				$uinfo['uid'] = $registerInfo['id'];
				// 汇总用户信息
				$uinfo = $this->callbackUinfo($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);
				
				$result = array(
					'status' => '1',
					'msg' => '登录成功',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
				
				//消息入队
				apiRequest('common_stomp_send', 'login', array('uid' => $uinfo['uid'], 'last_login_time' => $uinfo['last_login_time']));
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '登录失败',
					'data' => array()
				);
			}
		}
		echo json_encode($result);
	}

	//登陆验证码验证
	private function loginCaptchaPass($captcha) 
	{
		if($this->primarysession->getArg('loginError') >= 3)
		{
			if ($this->primarysession->getArg('captcha') == strtolower($captcha))
			{
				// 清除图形验证码
				$this->primarysession->setArg('captcha', '');
				return true;
			}
			else
			{
				// 清除图形验证码
				$this->primarysession->setArg('captcha', '');
				return false;
			}
		}
		else
		{
			return true;
		}
		
	}

	/*
 	 * IOS 手机号注册 step1 发送【语音验证码】
 	 * @version:O2O客户端-V1.0
 	 * @date:2016-1-19
 	 */
	public function sendRegCode()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(empty($data['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '手机号格式错误',
				'data' => array()
			);

			echo json_encode($result);
			exit();
		}

		// 手机格式检查
		$rule = '/1\d{10}$/';
		if (!preg_match($rule, $data['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '手机号格式错误',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		// 检查手机号码是否可用
		$checkStatus = $this->uinfo_model->checkRegister($data['phone']);

		if(!$checkStatus)
		{
			$result = array(
				'status' => '0',
				'msg' => '该手机号已被注册',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		$sendStatus = $this->getPhoneCode($data['phone'], 'yycaptcha');

		if($sendStatus)
		{
			$result = array(
				'status' => '1',
				'msg' => '语音验证码发送成功',
				'data' => array()
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '语音验证码发送失败',
				'data' => array()
			);
		}	
		echo json_encode($result);
	}

	/*
 	 * IOS 手机号短信注册、登陆 step2 发送【短信验证码】
 	 * @version:O2O客户端-V1.0
 	 * @date:2016-1-19
 	 */
	public function sendRegSms()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		// 参数 ctype register：短信注册；login：短信登录

		if(empty($data['phone']))
		{
			$result = array(
				'status' => '0',
				'needCaptcha' => '0',
				'msg' => '手机号格式错误',
				'data' => array()
			);

			echo json_encode($result);
			exit();
		}

		// 手机格式检查
		$rule = '/1\d{10}$/';
		if (!preg_match($rule, $data['phone']))
		{
			$result = array(
				'status' => '0',
				'needCaptcha' => '0',
				'msg' => '手机号格式错误',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		// 检查手机号码是否可用
		$checkStatus = $this->uinfo_model->checkRegister($data['phone']);

		// 根据使用场景
		if($data['ctype'] == 'register')
		{
			if(!$checkStatus)
			{
				$result = array(
					'status' => '-1',
					'needCaptcha' => '0',
					'msg' => '该手机已被注册',
					'data' => array()
				);
				echo json_encode($result);
				exit();
			}
		}
		elseif($data['ctype'] == 'login') 
		{
			// 短信登录
			if($checkStatus)
			{
				$result = array(
					'status' => '-101',
					'needCaptcha' => '0',
					'msg' => '该手机号不存在',
					'data' => array()
				);
				echo json_encode($result);
				exit();
			}
			// 检查是否注销
			$lockStatus = $this->uinfo_model->checkIsLocked($data['phone']);
			if($lockStatus)
			{
				$result = array(
					'status' => '-101',
					'needCaptcha' => '0',
					'msg' => '该手机号已注销',
					'data' => array()
				);
				echo json_encode($result);
				exit();
			}
		}
		else
		{
			$result = array(
				'status' => '0',
				'needCaptcha' => '0',
				'msg' => '请求参数错误',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		// 防刷限制 start
		$headerInfo = $this->getRequestHeaders();

		$this->load->library('limit');

		// 限制后图形验证码
		if($headerInfo['appVersionCode'] >= '11' && !empty($data['captcha']))
		{
			$captcha = isset($data['captcha']) ? $data['captcha'] : '';
			$sessionid = isset($data['sessionid']) ? $data['sessionid'] : '';

			if($sessionid) $this->primarysession->setSessionId($sessionid);
			$this->primarysession->startSession();

			if ($this->primarysession->getArg('captcha') == strtolower($captcha))
			{
				// 清除图形验证码
				$this->primarysession->setArg('captcha', '');

				// 清除限制 IP 设备号
				$this->limit->deleteCheck('ipRegister', '', UCIP);
				$this->limit->deleteCheck('deviceReg', $headerInfo['deviceId'], UCIP);
			}
			else
			{
				// 清除图形验证码
				$this->primarysession->setArg('captcha', '');
				
				$result = array(
					'status' => '-3',
					'needCaptcha' => '0',
					'msg' => '验证码错误',
					'data' => array()
				);
				echo json_encode($result);
				exit();
			}
		}

		// IP限制
		$ipResult = $this->limit->checkIp('ipRegister', UCIP);

		if($ipResult)
		{
			$result = array(
				'status' => ($headerInfo['appVersionCode'] >= '11')?'-2':'0',
				'needCaptcha' => ($headerInfo['appVersionCode'] >= '11')?'1':'0',
				'msg' => '操作频繁，请稍后再试',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		// V1.6及以上版本设备号限制
		if($headerInfo['appVersionCode'] >= '15' && !empty($headerInfo['deviceId']))
		{
			$deviceResult = $this->limit->checkDevice('deviceReg', $headerInfo['deviceId'], UCIP);
		
			if($deviceResult)
			{
				$result = array(
					'status' => '-2',
					'needCaptcha' => '1',
					'msg' => '操作频繁，请稍后再试',
					'data' => array()
				);
				echo json_encode($result);
				exit();
			}
		}

		// 防刷限制 end

		$sendStatus = $this->getSmsCode($data['phone'], $data['ctype'], 'yycaptcha');

		if($sendStatus)
		{
			$result = array(
				'status' => '1',
				'msg' => '短信验证码发送成功',
				'data' => array()
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '短信验证码发送失败',
				'data' => array()
			);
		}	
		echo json_encode($result);
	}


	/*
 	 * IOS 手机号注册 step1 确认手机验证码
 	 * @version:O2O客户端-V1.0
 	 * @prams:phone,password,captcha
 	 * @date:2016-1-19
 	 * @mark:文案修改需谨慎
 	 */
	public function isCaptchaPass()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('yycaptcha'));
		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]))
		{
			$result = array(
				'status' => '0',
				'msg' => '验证码不正确',
				'data' => array()
			);
		}
		else
		{
			$result = array(
				'status' => '1',
				'msg' => '验证成功',
				'data' => array()
			);
		}
		echo json_encode($result);
	}

	public function logout()
	{
		//清除session
		$this->primarysession->startSession();
		$this->primarysession->close();
	}

	// 微信登录 - 检查手机号码
	public function checkPhoneFormat($phone)
	{
		$rule = '/1\d{10}$/';
		if(!preg_match($rule, $phone))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// 微信登录 - SDK获取授权信息
	public function wxLogin()
	{
		$data = json_decode($this->strCode($this->input->post('data')), true);

		if(empty($data['unionid']))
		{
			$result = array(
				'status'	=>	'0',
				'msg' 		=> 	'unionid参数不能为空',
				'data' 		=> 	''
			);
			exit(json_encode($result));
		}
		
		// 检查是否已关联
		$this->load->model('app/model_uinfo', 'app_model_uinfo');
		$userInfo = $this->app_model_uinfo->checkWxUnionid($data['unionid']);

                $headerInfo = $this->getRequestHeaders();
		if(!empty($userInfo))
		{
			//登录记录
			$loginRecord = array(
				'login_time'	=>	date('Y-m-d H:i:s'), 
				'uid' 			=> 	$userInfo['uid'], 
				'ip' 			=> 	UCIP, 
				'area' 			=> 	$this->tools->convertip(UCIP), 
				'reffer' 		=> 	REFE ? REFE : '', 
				'platform' 		=> 	$this->config->item('platform'),
                'channel'       =>  $this->recordChannel($headerInfo['channel']),
				'login_type'	=>	1,
				'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
				'version' 		=> 	$headerInfo['appVersionName'],
				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
			);
	        $this->user_model->loginRecord($loginRecord);

	        // 单设备登录
        	$this->recordIdfaLogin($userInfo['uid']);

			$uinfo = $this->user_model->getUserInfo($userInfo['uid']);
			// 更新用户登录信息
			$this->user_model->saveUser(
			    array(
			        'uid' => $userInfo['uid'],
			        'last_login_time' => date('Y-m-d H:i:s'),
			        'last_login_channel' => $this->recordChannel($headerInfo['channel']),
			        'visit_times' => 1
			    )
			);
			$uinfo['uid'] = $userInfo['uid'];

			$uinfo = $this->callbackUinfo($uinfo);

			$uinfo['auth'] = $this->getUserAuth($uinfo);

			$result = array(
				'status'	=>	'1',
				'msg' 		=> 	'登陆成功',
				'data' 		=> 	$this->strCode(json_encode($uinfo), 'ENCODE')
			);
			
			//消息入队
			apiRequest('common_stomp_send', 'login', array('uid' => $uinfo['uid'], 'last_login_time' => $uinfo['last_login_time']));
		}
		else
		{
			$result = array(
				'status'	=>	'2',
				'msg' 		=> 	'该微信未绑定过账户',
				'data' 		=> 	$this->strCode(json_encode($data), 'ENCODE')
			);
		}
		exit(json_encode($result));
	}

	// 微信登录 - 根据手机号码发送验证码
	public function sendSmsByWxlogin()
	{
		$data = json_decode($this->strCode($this->input->post('data')), true);

		/*
		$data = array(
			'phone'		=>	'',
			'unionid'	=>	'',
			'captcha'	=>	'',
			'sessionid'	=>	'',
		);
		*/

		if(empty($data['phone']) || empty($data['unionid']))
		{
			$result = array(
				'status'		=>	'0',
				'needCaptcha'	=> 	'0',
				'msg' 			=>	'缺少必要参数',
				'data' 			=> 	''
			);
			exit(json_encode($result));
		}

		$phoneRes = $this->checkPhoneFormat($data['phone']);
		if(!$phoneRes)
		{
			$result = array(
				'status'		=>	'0',
				'needCaptcha'	=> 	'0',
				'msg' 			=>	'手机号码格式错误',
				'data' 			=> 	''
			);
			exit(json_encode($result));
		}

		// 检查手机号是否已注册
		$userInfo = $this->uinfo_model->getUserInfoByPhone($data['phone']);

                // 检查注销
                if(isset($userInfo['userStatus']) && $userInfo['userStatus'] == '1')
                {
                        $result = array(
                                'status'	=>	'0',
                                'msg' 		=> 	'该用户已注销',
                                'data' 		=> 	''
                        );
                        exit(json_encode($result));
                }
		// 号码已注册 已占用
		if(!empty($userInfo['wx_unionid']) && $userInfo['wx_unionid'] != $data['unionid'])
		{
			$result = array(
				'status'		=>	'0',
				'needCaptcha'	=> 	'0',
				'msg' 			=>	'该手机号已绑定了其他微信账号，请更换手机号码',
				'data' 			=> 	''
			);
			exit(json_encode($result)); 
		}
		
		// 号码已注册 且微信号相同
		if(!empty($userInfo) && !empty($userInfo['unionid']) && $userInfo['unionid'] == $data['unionid'])
		{
			$uinfo = $this->user_model->getUserInfo($userInfo['id']);

			// 检查注销
			if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
			{
				$result = array(
					'status'	=>	'0',
					'msg' 		=> 	'该用户已注销',
					'data' 		=> 	''
				);
				exit(json_encode($result));
			}
			
			// 更新用户登录信息
			$this->user_model->saveUser(
			    array(
			        'uid' => $userInfo['id'],
			        'last_login_time' => date('Y-m-d H:i:s'),
			        'last_login_channel' => $this->recordChannel($headerInfo['channel']),
			        'visit_times' => 1
			    )
			);

                        $headerInfo = $this->getRequestHeaders();
			//登录记录
			$loginRecord = array(
				'login_time'	=>	date('Y-m-d H:i:s'), 
				'uid' 			=> 	$userInfo['id'], 
				'ip' 			=> 	UCIP, 
				'area' 			=> 	$this->tools->convertip(UCIP), 
				'reffer' 		=> 	REFE ? REFE : '', 
				'platform' 		=> 	$this->config->item('platform'),
                'channel'       =>  $this->recordChannel($headerInfo['channel']),
				'login_type'	=>	1,
				'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
				'version' 		=> 	$headerInfo['appVersionName'],
				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
			);
	        $this->user_model->loginRecord($loginRecord);

	        // 单设备登录
        	$this->recordIdfaLogin($userInfo['id']);

			$uinfo = $this->user_model->getUserInfo($userInfo['id']);
			$uinfo['uid'] = $userInfo['id'];

			$uinfo = $this->callbackUinfo($uinfo);

			$uinfo['auth'] = $this->getUserAuth($uinfo);

			$result = array(
				'status'		=>	'1',
				'needCaptcha'	=> 	'0',
				'msg' 			=> 	'登陆成功',
				'data' 			=> 	$this->strCode(json_encode($uinfo), 'ENCODE')
			);
			
			//消息入队
			apiRequest('common_stomp_send', 'login', array('uid' => $uinfo['uid'], 'last_login_time' => $uinfo['last_login_time']));
			
			exit(json_encode($result)); 
		}

		// 防刷限制 start
		$headerInfo = $this->getRequestHeaders();

		$this->load->library('limit');

		// 验证图形验证码
		if(!empty($data['captcha']))
		{
			if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
			$this->primarysession->startSession();

			if ($this->primarysession->getArg('captcha') == strtolower($data['captcha']))
			{
				$this->primarysession->setArg('captcha', '');
				// 清除限制 IP 设备号
				$this->limit->deleteCheck('ipRegister', $headerInfo['deviceId'], UCIP);
				$this->limit->deleteCheck('deviceReg', $headerInfo['deviceId'], UCIP);
			}
			else
			{
				$this->primarysession->setArg('captcha', '');
				$result = array(
					'status' 		=>	'0',
					'needCaptcha'	=> 	'0',
					'msg' 			=> 	'图形验证码错误',
					'data' 			=> 	''
				);
				exit(json_encode($result));
			}
		}

		// IP限制
		$ipResult = $this->limit->checkIp('ipRegister', UCIP);

		if($ipResult)
		{
			$result = array(
				'status' 		=>	'-2',
				'needCaptcha'	=>	'1',
				'msg' 			=> 	'操作频繁，请稍后再试',
				'data' 			=> 	''
			);
			exit(json_encode($result));
		}

		// 设备号限制
		if(!empty($headerInfo['deviceId']))
		{
			$deviceResult = $this->limit->checkDevice('deviceReg', $headerInfo['deviceId'], UCIP);

			if($deviceResult)
			{
				$result = array(
					'status' 		=>	'-2',
					'needCaptcha'	=>	'1',
					'msg' 			=> 	'操作频繁，请稍后再试',
					'data' 			=> 	''
				);
				exit(json_encode($result));
			}
		}

		$res = $this->getSmsCode($data['phone'], 'register', 'app_captcha');

		if($res)
		{
			$result = array(
				'status' 		=>	'2',
				'needCaptcha'	=>	'0',
				'msg' 			=> 	'短信发送成功',
				'data' 			=> 	''
			);
		}
		else
		{
			$result = array(
				'status' 		=>	'0',
				'needCaptcha'	=>	'0',
				'msg' 			=> 	'短信发送失败',
				'data' 			=> 	''
			);
		}
		exit(json_encode($result));
	}

	// 微信登录 - 根据验证码绑定或注册账号
	public function wxRegister()
	{
	    $result = array(
	        'status' => '0',
	        'msg' => '系统升级维护中',
	        'data' => array()
	    );
	    echo json_encode($result);
	    exit();
		$data = json_decode($this->strCode($this->input->post('data')), true);

		/*
		$data = array(
			'phone'		=>	'',
			'unionid'	=>	'',
			'captcha'	=>	'',
			'sessionid'	=>	'',
		);
		*/

		// 手机格式检查
		if(empty($data['phone']) || empty($data['unionid']) || empty($data['captcha']))
		{
			$result = array(
				'status'		=>	'0',
				'msg' 			=>	'缺少必要参数',
				'data' 			=> 	''
			);
			exit(json_encode($result));
		}

		$phoneRes = $this->checkPhoneFormat($data['phone']);
		if(!$phoneRes)
		{
			$result = array(
				'status'		=>	'0',
				'msg' 			=>	'手机号码格式错误',
				'data' 			=> 	''
			);
			exit(json_encode($result));
		}

		// 检查是否已关联
		$this->load->model('app/model_uinfo', 'app_model_uinfo');
		$userInfo = $this->app_model_uinfo->checkWxUnionid($data['unionid']);

		if(!empty($userInfo))
		{
			$result = array(
				'status'	=>	'0',
				'msg' 		=>	'微信号不能重复绑定',
				'data' 		=>	''
			);
			exit(json_encode($result));
		}

		// 验证码验证
		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);

		$this->primarysession->startSession();

		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));

		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]) || $data['phone'] != $codestr[3]) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('wxRegisterError', $this->primarysession->getArg('wxRegisterError') + 1);

			if($this->primarysession->getArg('wxRegisterError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('wxRegisterError', 0); 

				$result = array(
					'status'	=>	'0',
					'msg' 		=>	'验证码已失效，请重新获取验证码',
					'data' 		=>	''
				);
			}
			else
			{
				$result = array(
					'status'	=>	'0',
					'msg' 		=>	'验证码错误',
					'data' 		=>	''
				);
			}
			exit(json_encode($result));
		}

		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		// 组装用户信息
		$userData = array(
			'unionid'	=>	$data['unionid'],
			'phone'		=>	$data['phone'],
			'password'	=> 	$this->createNonceStr(rand(6, 16)),
			'ip' 		=> 	UCIP,
			'platform'	=> 	$this->config->item('platform'),
			'version' 	=> 	isset($headerInfo['appVersionName']) ? $headerInfo['appVersionName'] : '1.0',
			'channel' 	=> 	$this->recordChannel($headerInfo['channel'])
		);

		// 调用注册模块创建用户信息
		$registerStatus = $this->uinfo_model->wxRegister($userData);

		if($registerStatus['status'])
		{
			if($registerStatus['data']['regType'] == '1')
			{
				// 调用活动关联组件
				$activityData = array(
					'uid' 			=>	$registerStatus['data']['uid'],
					'phone' 		=> 	$data['phone'],
					'platformId'	=> 	$this->config->item('platform'),
					'channelId' 	=> 	$this->recordChannel($headerInfo['channel']),
				);
				$this->load->library('activity');
				// 188红包
				$this->activity->regHookBy188($activityData);
				// 拉新活动
				$this->activity->regHookByLx($activityData);
			}

	        //登录记录
        	$loginRecord = array(
        		'login_time'	=>	date('Y-m-d H:i:s'), 
        		'uid' 			=> 	$registerStatus['data']['uid'], 
        		'ip' 			=> 	UCIP, 
        		'area' 			=> 	$this->tools->convertip(UCIP), 
        		'reffer' 		=> 	REFE ? REFE : '', 
        		'platform' 		=> 	$this->config->item('platform'),
				'channel'       =>  $this->recordChannel($headerInfo['channel']),
        		'login_type'	=>	1,
        		'model' 		=> 	$headerInfo['model'] ? $headerInfo['model'] : '',
				'system' 		=> 	$headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
				'version' 		=> 	$headerInfo['appVersionName'],
				'idfa' 			=>  strtoupper($headerInfo['deviceId']),
        	);
        	$this->user_model->loginRecord($loginRecord);

        	// 访问次数统计
        	$this->user_model->saveUser(
                array(
                    'uid' => $registerStatus['data']['uid'], 
                    'last_login_time' => date('Y-m-d H:i:s'), 
                    'last_login_channel' => $this->recordChannel($headerInfo['channel']),
                    'visit_times' => 1
                )
            );

        	// 单设备登录
        	$this->recordIdfaLogin($registerStatus['data']['uid']);

			$uinfo = $this->user_model->getUserInfo($registerStatus['data']['uid']);
			$uinfo['uid'] = $registerStatus['data']['uid'];

			$uinfo = $this->callbackUinfo($uinfo);

			$uinfo['auth'] = $this->getUserAuth($uinfo);

			// 清除有效验证码
			$this->primarysession->setArg('yycaptcha', '');
			
			$result = array(
				'status' 	=> 	'1',
				'msg' 		=> 	'绑定成功',
				'data' 		=> 	$this->strCode(json_encode($uinfo), 'ENCODE')
			);
			
			//如果客户端返回微信头像URL则本地存储
			if ($registerStatus['data']['regType'] == '1' && isset($data['headimgurl']) && !empty($data['headimgurl'])) {
			    $file_path = dirname(BASEPATH) . '/cpiaoimg/headimg/';
			    $file_name = md5(time() . rand(1, 1000)) . '.jpeg';
			    $image = $this->tools->request($data['headimgurl']);
			    if($this->tools->recode == 200) {
			        file_put_contents($file_path . $file_name, $image);
			        $static_url = $this->config->item('img_url');
			        shuffle($static_url);
			        if (ENVIRONMENT === 'production') {
			            $img_url = 'https:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
			        } else {
			            $img_url = 'http:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
			        }
			        $this->user_model->uploadImg($img_url, $registerStatus['data']['uid']);
			    }
			}
		}
		else
		{
			$result = array(
				'status' 	=> 	'0',
				'msg' 		=> 	'该手机号已绑定了其他微信账号，请更换手机号码',
				'data' 		=> 	''
			);
		}
		exit(json_encode($result));
	}
}
