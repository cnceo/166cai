<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 用户中心数据接口
 * @date:2016-01-18
 */
class User extends MY_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('comm');
		$this->load->library('tools');
		$this->load->library('BankCard');
		$this->load->model('user_model');
		$this->load->model('newuser_model');
		$this->versionInfo = $this->getRequestHeaders();
		$this->redpackmethod = 'hongbao166';
	}

	public $activityId = array(
        '188hb' => '1', //188红包
		'166hb'	=>	'8',
    );

	public function index()
    {
        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $this->getRequestHeaders()
        );
        echo json_encode($result);  
    }

	/*
 	 * 获取用户基本信息
 	 * @date:2016-01-18
 	 */
	public function userBaseInfo()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => ''
		);

		// log_message('LOG', "请求参数: " . json_encode($data), 'userBaseInfo');
		if(!empty($data['uid']))
		{
			$uinfo = $this->user_model->getUserInfo($data['uid']);

			if(!empty($uinfo))
			{			
				$uinfo['uid'] = $data['uid'];

				// 检查用户登录状态
				if(!$this->checkUserAuth($uinfo, $data['auth']))
				{
					$result = array(
						'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
						'msg' 		=> 	'您的登录密码已修改，请重新登录',
						'data' 		=> 	''
					);
					echo json_encode($result);
					exit();
				}

				// 单设备登录检查
		        $checkData = $this->checkUserLogin($uinfo['uid']);
		        if(!$checkData['status'])
		        {
		            $result = array(
		                'status'    =>  $checkData['code'],
		                'msg'       =>  $checkData['msg'],
		                'data'      =>  '',
		            );
		            echo json_encode($result);
		            exit();
		        }

				// 用户是否注销
				if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
				{
					$result = array(
						'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
						'msg' 		=> 	'您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
						'data' 		=> 	''
					);
					echo json_encode($result);
					exit();
				}
				
				// 汇总用户信息
				$uinfo = $this->callbackUinfo($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);

				$result = array(
					'status' => '1',
					'msg' => 'Success',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
			}
		}

		echo json_encode($result);
		exit();
	}

	/*
 	 * 修改手机号码
 	 * @date:2016-01-18
 	 */
	public function updatePhone()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		// 调试
		// $data = array(
		// 	'uid' => '35',
		// 	'old_phone' => '18668211325',
		// 	'new_phone' => '13148461630'
		// );

		if( empty($data['uid']) || empty($data['old_phone']) || empty($data['new_phone']) || empty($data['sessionid']) || empty($data['captcha']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 手机格式检查
		$rule = '/1\d{10}$/';
		if (!preg_match($rule, $data['new_phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '手机号码格式不正确',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}

		if($data['new_phone'] == $data['old_phone'])
		{
			$result = array(
				'status' => '0',
				'msg' => '不能绑定原手机号码',
				'data' => array()
			);
			echo json_encode($result);
			exit();
		}
		
		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();

		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));

		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]) || $data['new_phone'] != $codestr[3]) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('updatePhoneError', $this->primarysession->getArg('updatePhoneError') + 1);

			if($this->primarysession->getArg('updatePhoneError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('updatePhoneError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码不正确',
					'data' => ''
				);
			}
		}
		else
		{
			$uinfo = $this->user_model->getUserInfo($data['uid']);
			$uinfo['uid'] = $data['uid'];

			// 检查用户登录状态
			if(!$this->checkUserAuth($uinfo, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($uinfo['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			// 组装数据
			$userData = array(
				'uid' => $data['uid'],
				'old_phone' => $data['old_phone'],
				'new_phone' => $data['new_phone']
			);
	
			$this->load->model('uinfo_model');
			$updateStatus = $this->uinfo_model->updateRegisterPhone($userData);
	
			if($updateStatus['status'] == '200')
			{				
				// 汇总用户信息
				$uinfo = $this->callbackUinfo($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);

				$result = array(
					'status' => '1',
					'msg' => '手机号码更新成功',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => $updateStatus['msg'],
					'data' => ''
				);
			}
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 获取用户绑定银行卡信息
 	 * @date:2016-01-18
 	 */
	public function userBankInfo()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => ''
		);

		if(!empty($data['uid']))
		{
			$uinfo = $this->user_model->getUserInfo($data['uid']);
			$uinfo['uid'] = $data['uid'];

			// 检查用户登录状态
			if(!$this->checkUserAuth($uinfo, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($uinfo['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			$info = $this->user_model->freshBankInfo($data['uid']);
			if(!empty($info))
			{	
				$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
				//增加银行卡logo信息
				foreach ($info as $key => $items) 
				{
					$bankInfo = $this->comm->BankInfo();
					$info[$key]['bank_name'] = $bankInfo[$items['bank_type']]['name'];
					$info[$key]['logo'] = $protocol . $this->config->item('pages_url') . 'caipiaoimg/static/images/bank/' . $bankInfo[$items['bank_type']]['img'];
				}

				$result = array(
					'status' => '1',
					'msg' => 'Success',
					'data' => $this->strCode(json_encode(array('info' => $info)), 'ENCODE')
				);
			}
			else
			{
				$info = array();
				$result = array(
					'status' => '1',
					'msg' => 'Success',
					'data' => $this->strCode(json_encode(array('info' => $info)), 'ENCODE')
				);
			}
		}
		echo json_encode($result);
	}

	/*
 	 * 银行卡信息
 	 * @date:2016-01-18
 	 */
	public function bankInfo()
	{
		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => ''
		);

		$bankInfo = $this->comm->BankInfo();

		if(!empty($bankInfo))
		{

			$list = array();
			$count = 0;			
			foreach ($bankInfo as $key => $items) 
			{
				$list[$count]['bank_type'] =  $key;
				$list[$count]['name'] =  $items['name'];
				$list[$count]['dname'] =  $items['dname'];
				$count ++;
			}

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => $list
			);
		}
		
		echo json_encode($result);
	}

	/*
 	 * 绑定银行卡接口
 	 * @date:2016-01-18
 	 */
	public function setBankInfo()
	{
		// 加密
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

        if( empty($data['uid']) || empty($data['action']) )
        {
        	$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
        }

		$params = array(
			'uid' => '',				//用户ID
			'bank_id' => '',			//银行卡号
			'bank_type' => '',			//银行类型
			'bank_province' => '',		//开户省
			'bank_city' => '',			//开户市
			'action' => ''				//请求类型
		);

		$uinfo = $this->user_model->getUserInfo($data['uid']);
		$uinfo['uid'] = $data['uid'];
		// 检查用户登录状态
		if(!$this->checkUserAuth($uinfo, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }
		
		/*
		$action = array(
			0 => 'add',			//添加银行卡
			1 => 'modify',		//修改
			2 => 'delect',		//删除
			3 => 'default'		//修改默认卡
		);
		*/

		$resData = array(
			'status' => FALSE,
			'msg' => '银行卡处理失败，未知请求'
		);

		switch ($data['action']) {
			case 'add':
				$resData = $this->addBank($data);
				break;

			case 'modify':
				$resData = $this->updateBank($data);
				break;

			case 'delete':
				$resData = $this->delectBank($data);
				break;

			case 'default':
				$resData = $this->defaultBank($data);
				break;

			default:
				# code...
				break;
		}

		if($resData['status'])
		{	
			$result = array(
				'status' => '1',
				'msg' => $resData['msg'],
				'data' => ''
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $resData['msg'],
				'data' => ''
			);
		}

		echo json_encode($result);
	}

	/*
 	 * 添加银行卡
 	 * @date:2016-01-18
 	 */
	public function addBank($data)
	{
		$resData = array(
			'status' => FALSE,
			'msg' => '添加银行卡失败'
		);

		if( empty($data['bank_type']) || empty($data['bank_province']) || empty($data['bank_city']) )
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '缺少必要参数'
			);
			return $resData;
		}

		$data['bank_id'] = str_replace(' ', '', $data['bank_id']);

		//验证提交参数
		if(!BankCard::checkBankCard($data['bank_id']))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡号格式错误'
			);
			return $resData;
		}

		$bank = $this->comm->BankInfo($data['bank_type'], 'img');
		if(empty($bank))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡类型错误'
			);
			return $resData;
		}

		//检查是否存在
		if($this->checkAlredyCard($data['uid'], trim($data['bank_id'])))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡号已存在'
			);
			return $resData;
		}
		
		// 组装参数
		$rdata = array(
			'uid' => $data['uid'],
			'bank_province' => $data['bank_province'],
			'bank_city' => $data['bank_city'],
			'bank_type' => $data['bank_type'],
			'bank_id' => trim($data['bank_id'])
		);

		$dbRes = $this->newuser_model->insertBank($rdata);

		if($dbRes['status'])
		{
			$resData = array(
				'status' => TRUE,
				'msg' => '添加成功'
			);
		}
		else
		{
			$resData = array(
				'status' => FALSE,
				'msg' => $dbRes['data']['msg']
			);
		}

		return $resData;
		
	}

	/*
 	 * 修改银行卡
 	 * @date:2016-01-18
 	 */
	public function updateBank($data)
	{
		if(empty($data['id']))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '缺少必要参数'
			);
			return $resData;
		}

		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

		if(empty($bankInfo))
		{	
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡信息失效'
			);
			return $resData;
		}
		else
		{
			$check = FALSE;
			foreach ($bankInfo as $key => $items) 
			{
				if($items['id'] == $data['id'])
				{
					$check = TRUE;
				}
			}

			if(!$check)
			{
				$resData = array(
					'status' => FALSE,
					'msg' => '银行卡信息失效'
				);
				return $resData;
			}
		}

		//检查是否存在
		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

        if(!empty($bankInfo))
        {
            $banks = array();
            foreach ($bankInfo as $bank) 
            {
            	if($bank['id'] != $data['id'])
            	{
            		array_push($banks, $bank['bank_id']);
            	}
            }

            if(in_array($data['bank_id'], $banks))
            {
                $resData = array(
					'status' => FALSE,
					'msg' => '银行卡号已存在'
				);
				return $resData;
            }
        }
        else
        {
        	$resData = array(
				'status' => FALSE,
				'msg' => '银行卡号已失效'
			);
			return $resData;
		}

		//验证提交参数
		if(!BankCard::checkBankCard($data['bank_id']))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡号格式错误'
			);
			return $resData;
		}

		$bank = $this->comm->BankInfo($data['bank_type'], 'img');
		if(empty($bank))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '银行卡类型错误'
			);
			return $resData;
		}

		$dbRes = $this->newuser_model->updateBank($data);

		if($dbRes['status'])
		{
			$resData = array(
				'status' => TRUE,
				'msg' => '修改成功'
			);
		}
		else
		{
			$resData = array(
				'status' => FALSE,
				'msg' => $dbRes['data']['msg']
			);
		}

		return $resData;
	}

	/*
 	 * 修改银行卡
 	 * @date:2016-01-18
 	 */
	public function delectBank($data)
	{
		if(empty($data['id']))
		{
			$resData = array(
				'status' => FALSE,
				'msg' => '缺少必要参数'
			);
			return $resData;
		}

		$dbRes = $this->newuser_model->delBank($data);

		if($dbRes['status'])
		{
			$resData = array(
				'status' => TRUE,
				'msg' => '删除成功'
			);
		}
		else
		{
			$resData = array(
				'status' => FALSE,
				'msg' => $dbRes['data']['msg']
			);
		}

		return $resData;
	}
	
	/*
 	 * 设置默认银行卡
 	 * @date:2016-01-18
 	 */
	public function defaultBank($data)
	{
		$dbRes = $this->newuser_model->setDefaultBank($data);

		if($dbRes['status'])
		{
			$resData = array(
				'status' => TRUE,
				'msg' => '设置默认卡成功'
			);
		}
		else
		{
			$resData = array(
				'status' => FALSE,
				'msg' => $dbRes['data']['msg']
			);
		}

		return $resData;
	}

	
	/*
 	 * 检查是否存在
 	 * @date:2016-01-18
 	 */
	public function checkAlredyCard($uid, $bank_id) 
    {       
        $res = FALSE;
        $bankInfo = $this->user_model->freshBankInfo($uid);
        if(!empty($bankInfo))
        {
            $banks = array();
            foreach ($bankInfo as $bank) 
            {
                array_push($banks, $bank['bank_id']);
            }
            if(in_array($bank_id, $banks, true))
            {
                $res = TRUE;
            }
        }
        return $res;
    }

    /*
 	 * 绑定身份证
 	 * @date:2016-01-18
 	 */
    public function setIdCardInfo()
    {
    	$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

        if( empty($data['uid']) || empty($data['id_card']) || empty($data['real_name']) )
        {
        	$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
        }

        // 获取用户信息
		$uinfo = $this->user_model->getUserInfo($data['uid']);
		$uinfo['uid'] = $data['uid'];
		// 检查用户登录状态
		if(!$this->checkUserAuth($uinfo, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($uinfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		if(empty($uinfo))
		{
			$result = array(
				'status' => '0',
				'msg' => '获取用户信息失败',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		if(!empty($uinfo['id_card']))
		{
			$result = array(
				'status' => '0',
				'msg' => '您已完成实名认证',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 用户名检查
		$rule = '/^[_\x{4e00}-\x{9fa5}\.\·\d]{2,10}$/iu';
		if( !preg_match($rule, $data['real_name']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '请输入正确的中文名',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        $this->load->library('BindIdCard');
		$result = $this->bindidcard->appsetIdCardInfo($data);
		echo json_encode($result);
		exit();
    }

    /*
 	 * 发送语音验证码
 	 * @date:2016-01-18
 	 */
	public function sendPhoneCode()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		// LOG
		// log_message('LOG', "发送手机验证 - 手机号码: " . json_encode($data), 'user_api');

		if(empty($data['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		// $ctype = 'captcha_' . $data['phone'];
		$res = $this->getPhoneCode($data['phone'], 'app_captcha');
		if($res)
		{
			$result = array(
				'status' => '1',
				'msg' => '发送成功',
				'data' => ''
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '发送失败',
				'data' => ''
			);
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 发送短信验证码
 	 * @date:2016-01-18
 	 */
	public function sendSmsCode()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => '手机号码不能为空',
			'data' => ''
		);

		// 手机格式检查
		$rule = '/1\d{10}$/';
		if (!preg_match($rule, $data['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '手机号码格式不正确',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		$headerInfo = $this->getRequestHeaders();

		if($headerInfo['appVersionCode'] > '10')
		{
			if(empty($data['uid']))
			{
				$result = array(
					'status' => '0',
					'msg' => '发送失败，用户信息获取失败',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			// 防刷限制
			$this->load->library('limit');
			$userLimit = $this->limit->checkUser('userSms', $data['uid']);

			if($userLimit)
			{
				$result = array(
					'status' => '0',
					'msg' => '请求太频繁，请24小时后再试',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
		}	
		
		if(!empty($data['phone']))
		{
			$res = $this->getSmsCode($data['phone'], $data['ctype'], 'app_captcha');
			if($res)
			{
				$result = array(
					'status' => '1',
					'msg' => '发送成功',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '发送失败',
					'data' => ''
				);
			}
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 按用户发送短信验证码
 	 * @date:2016-09-07
 	 */
	public function sendUserSms()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => '参数错误，请稍后再试',
			'data' => ''
		);

		if(!empty($data['uid']) && !empty($data['ctype']))
		{
			$uinfo = $this->user_model->getUserInfo($data['uid']);

			if(!empty($uinfo))
			{
				// 防刷限制
				$this->load->library('limit');
				$userLimit = $this->limit->checkUser('userSms', $data['uid']);

				if($userLimit)
				{
					$result = array(
						'status' => '0',
						'msg' => '请求太频繁，请24小时后再试',
						'data' => ''
					);
					echo json_encode($result);
					exit();
				}

				$res = $this->getSmsCode($uinfo['phone'], $data['ctype'], 'app_captcha');

				if($res)
				{
					$result = array(
						'status' => '1',
						'msg' => '短信发送成功',
						'data' => ''
					);
				}
				else
				{
					$result = array(
						'status' => '0',
						'msg' => '短信发送失败',
						'data' => ''
					);
				}
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '用户信息获取失败',
					'data' => ''
				);
			}
		}

		echo json_encode($result);
	}

	/*
 	 * 按手机号发送短信验证码
 	 * @date:2016-09-07
 	 */
	public function sendPhoneSms()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'needCaptcha' => '0',
			'msg' => '参数错误，请稍后再试',
			'data' => ''
		);

		if(!empty($data['phone']) && !empty($data['ctype']))
		{
			$check = array(
				'status' => TRUE,
				'msg' => '',
				'data' => ''
			);

			switch ($data['ctype']) 
			{
				case 'forgetPword':
					// 检查手机号是否已注册
					$this->load->model('uinfo_model');
					$returnData = $this->uinfo_model->checkRegister($data['phone']);
					if($returnData)
					{
						$check = array(
							'status' => FALSE,
							'msg' => '该手机号不存在',
							'data' => ''
						);
					}
					break;
				
				default:
					$check = array(
						'status' => FALSE,
						'msg' => '请求参数错误',
						'data' => ''
					);
					break;
			}

			// 手机号逻辑验证
			if($check['status'])
			{
				// 防刷限制 start
				$headerInfo = $this->getRequestHeaders();

				$this->load->library('limit');

				// 验证图形验证码
				if(!empty($data['captcha']))
				{
					if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
					$this->primarysession->startSession();

					if ($this->primarysession->getArg('send_captcha') == strtolower($data['captcha']))
					{
						$this->primarysession->setArg('send_captcha', '');
						// 清除限制 IP 设备号
						$this->limit->deleteCheck('ipRegister', $headerInfo['deviceId'], UCIP);
						$this->limit->deleteCheck('deviceReg', $headerInfo['deviceId'], UCIP);
					}
					else
					{
						$this->primarysession->setArg('send_captcha', '');
						$result = array(
							'status' => '-3',
							'needCaptcha' => '0',
							'msg' => '图形验证码错误',
							'data' => ''
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
						'status' => '-2',
						'needCaptcha' => '1',
						'msg' => '操作频繁，请稍后再试',
						'data' => ''
					);
					echo json_encode($result);
					exit();
				}

				// 设备号限制
				if(!empty($headerInfo['deviceId']))
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

				$res = $this->getSmsCode($data['phone'], $data['ctype'], 'app_captcha');

				if($res)
				{
					$result = array(
						'status' => '1',
						'needCaptcha' => '0',
						'msg' => '短信发送成功',
						'data' => ''
					);
				}
				else
				{
					$result = array(
						'status' => '0',
						'needCaptcha' => '0',
						'msg' => '短信发送失败',
						'data' => ''
					);
				}
			}
			else
			{
				$result = array(
					'status' => '0',
					'needCaptcha' => '0',
					'msg' => $check['msg'],
					'data' => ''
				);
			}
		}

		echo json_encode($result);
	}

	/*
 	 * 获取验证码操作
 	 * @date:2016-01-18
 	 */
	public function checkCaptcha()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		
		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();

		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));

		if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0]) || $data['phone'] != $codestr[3]) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('checkError', $this->primarysession->getArg('checkError') + 1);

			if($this->primarysession->getArg('checkError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('checkError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码不正确',
					'data' => ''
				);
			}
		}
		else
		{
			$result = array(
				'status' => '1',
				'msg' => '验证成功',
				'data' => ''
			);
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 验证并一键绑定
 	 * @date:2016-01-18
 	 */
	public function one()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		// LOG
		// log_message('LOG', "一键绑定 - 请求参数: " . $this->input->post('data'), 'user_api');

		// 姓名、身份证、支付密码、手机号码
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(!empty($data))
		{
			// 参数检查
			if( empty($data['uid']) || empty($data['sessionid']) || empty($data['captcha']) )
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少必要参数',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			// 获取用户信息
			$uinfo = $this->user_model->getUserInfo($data['uid']);
			if(empty($uinfo))
			{
				$result = array(
					'status' => '0',
					'msg' => '获取用户信息失败',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
	
			// 检查用户登录状态
			$uinfo['uid'] = $data['uid'];
			if(!$this->checkUserAuth($uinfo, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($uinfo['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			// 组装数据
			$userData = array();
			$userData['uid'] = $data['uid'];

			if(isset($data['real_name']))
			{
				$rule = '/^[_\x{4e00}-\x{9fa5}\.\d]{2,10}$/iu';
				if( !preg_match($rule, $data['real_name']) )
				{
					$result = array(
						'status' => '0',
						'msg' => '请输入正确的中文名',
						'data' => ''
					);
					echo json_encode($result);
					exit();
				}

				$userData['real_name'] = $data['real_name'];
			}

			// 参数规则校验
			if(isset($data['id_card']))
			{
				// 检查唯一性
		        if ($this->user_model->isIdCardRepeat($data['id_card'])) 
		        {
					$result = array(
						'status' => '0',
						'msg' => '单个身份证最多绑定5个帐号。如您不知悉这些帐号，请联系客服查询。',
						'data' => ''
					);
					echo json_encode($result);
					exit();
				}

				// 判断身份证合法性, 判断希望年龄
				$this->load->library('IdCard');
		        $idInfo = IdCard::checkIdCard($data['id_card']);
		        if($idInfo == false)
		        {
		        	$result = array(
						'status' => '0',
						'msg' => '身份证格式错误',
						'data' => ''
					);
					echo json_encode($result);
					exit();
		        }

		        if (!IdCard::isEnoughAgeByIdCard($data['id_card'], 18)) 
	            {
	                $result = array(
						'status' => '0',
						'msg' => '身份证未满18周岁',
						'data' => ''
					);
					echo json_encode($result);
					exit();
	            } 

		        // 添加身份证号码
		        $userData['id_card'] = $data['id_card'];

			}

			// 绑定支付密码
			if(isset($data['new_pay_pwd']))
			{
				// 6至16位字母，数字，符号组合
				$rule = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,16}$/';
				if( !preg_match($rule, $data['new_pay_pwd']) )
				{
					$result = array(
						'status' => '0',
						'msg' => '支付密码格式不正确',
						'data' => ''
					);
					echo json_encode($result);
					exit();
				}

				// 支付密码不能与登录密码一致
		        if ( md5($data['new_pay_pwd']) == $uinfo['pword'] ) 
		        {
		        	$result = array(
						'status' => '0',
						'msg' => '支付密码不能与登录密码一致',
						'data' => ''
					);
					echo json_encode($result);
					exit();
		        }

				// 添加支付密码
				$userData['pay_pwd'] = md5($data['new_pay_pwd']);
			}

			// 至少需要一个参数
			if( empty($userData['real_name']) && empty($userData['id_card']) && empty($userData['pay_pwd']) )
			{
				$result = array(
					'status' => '0',
					'msg' => '缺少有效参数',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			// 验证检查
			if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
			$this->primarysession->startSession();
			$codestr = explode(':', $this->primarysession->getArg('app_captcha'));
			if ( (empty($data['sessionid']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0])) 
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码不正确',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			unset($data['sessionid']);
			unset($data['captcha']);

			// 红包功能 完善信息红包
			$this->load->library('libredpack');
            if ($this->libredpack->{$this->redpackmethod}('hasAttend', array('phone' => $uinfo['phone']))) {
	            // 红包功能 完善信息红包
				if(!empty($data['id_card'])) {
					if ($this->libredpack->{$this->redpackmethod}('checkBound', array('userId' => $data['uid'], 'idCard' => $data['id_card']))) {
						$redpackResult = $this->libredpack->{$this->redpackmethod}('activate', array('userId' => $data['uid']));
	            	} else {
	            		if(isset($data['ignoreRedpack']) &&  $data['ignoreRedpack'] == '1') {
	            			$redpackResult = $this->libredpack->{$this->redpackmethod}('deleteOwn', array('userId' => $data['uid']));
	            		} else {// 返回提示
							exit(json_encode(array('status' => '-102', 'msg' => '该身份证已参加红包活动', 'data' => '')));
	            		}
	            	}
				}
	        }

			//绑定信息
			$dbRes = $this->newuser_model->saveUserBase($userData);

			if($dbRes)
			{
				$result = array(
					'status' => '1',
					'msg' => '完善个人信息成功',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '完善个人信息失败',
					'data' => ''
				);
			}

		}

		echo json_encode($result);
		exit();
	}

	/*
 	 * 修改登录密码
 	 * @date:2016-01-18
 	 */
	public function setLoginPwd()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(empty($data['uid']) || empty($data['new_pword']) || empty($data['old_pword']))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取用户基本信息
		$info = $this->user_model->getUserInfo($data['uid']);

		// 检查用户登录状态
		$info['uid'] = $data['uid'];
		if(!$this->checkUserAuth($info, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($info['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

                $oldpword = md5($data['old_pword']);
                if ($info['salt'])
                {
                    $oldpword = $oldpword . $info['salt'];
                    $oldpword = strCode($oldpword, 'ENCODE');
                }
		// 旧密码校验
		if($oldpword != $info['pword'])
		{
			$result = array(
				'status' => '0',
				'msg' => '原登录密码错误',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
		$newpword = md5($data['new_pword']);
                if ($info['salt'])
                {
                    $newpword = $newpword . $info['salt'];
                    $newpword = strCode($newpword, 'ENCODE');
                }
		// 检查新密码是否与旧密码一致
                if($newpword == $info['pword'])
                {
                    $result = array(
                    'status' => '0', 
                    'msg' => '不能与原登录密码一致',
                    'data' => '', 
                    );
                    echo json_encode($result);
                            exit();
                }

        // 6至16位字母，数字，符号组合
		$rule = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,16}$/';
		if( !preg_match($rule, $data['new_pword']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '登录密码格式不正确',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}
                $uniqid = substr(uniqid(), 0, 9);
                $pwd = md5($data['new_pword']) . $uniqid;        
		// 组装数据
		$saveInfo = array(
			'uid' 	=>	$data['uid'],
			'pword'	=>	strCode($pwd, 'ENCODE'),
            'salt' 	=> 	$uniqid,
		);

		// 更新登录密码
		if( $this->user_model->saveUser($saveInfo) ) 
        {
            $this->user_model->freshUserInfo($data['uid']);

            // 重新获取用户信息
            $uinfo = $this->user_model->getUserInfo($data['uid']);
            $uinfo['uid'] = $data['uid'];
            // 汇总用户信息
			$uinfo = $this->callbackUinfo($uinfo);
			$uinfo['auth'] = $this->getUserAuth($uinfo);

            $result = array(
				'status' => '1',
				'msg' => '修改登录密码成功',
				'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
			);
        }
        else
        {
        	$result = array(
				'status' => '0',
				'msg' => '修改登录密码失败',
				'data' => ''
			);
        }

        echo json_encode($result);
	}

	/*
 	 * 设置支付密码
 	 * @date:2016-01-18
 	 */
	public function setPayPwd()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if( empty($data['uid']) || empty($data['new_pay_pwd']) || empty($data['sessionid']) || empty($data['captcha']) || empty($data['phone']) )
		{
			echo json_encode($result);
			exit();
		}

		// 支付密码不能与登录密码一致
		$info = $this->user_model->getUserInfo($data['uid']);

		// 检查用户登录状态
		$info['uid'] = $data['uid'];
		if(!$this->checkUserAuth($info, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($info['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		if(md5($data['new_pay_pwd']) == $info['pword'])
		{
			$result = array(
				'status' => '0',
				'msg' => '不能与登录密码一致',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 验证检查
		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));
		if ( (empty($data['sessionid']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0])) 
		{
			$result = array(
				'status' => '0',
				'msg' => '验证码不正确',
				'data' => ''
			);
		}
		else
		{
			$saveInfo = array(
				'uid' => $data['uid'],
				'pay_pwd' => md5($data['new_pay_pwd'])
			);

	        if ($this->user_model->saveUserBase($saveInfo)) 
	        {
	            $result = array(
					'status' => '1',
					'msg' => '设置支付密码成功',
					'data' => ''
				);
	        } 
	        else 
	        {
	            $result = array(
					'status' => '0',
					'msg' => '设置支付密码失败',
					'data' => ''
				);
	        }
		}	
        echo json_encode($result);
		exit();
	}

	/*
 	 * 设置支付密码
 	 * @date:2016-01-18
 	 */
	public function updatePayPwd()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if( empty($data['uid']) || empty($data['new_pay_pwd']) || empty($data['old_pay_pwd']) )
		{
			echo json_encode($result);
			exit();
		}

		$info = $this->user_model->getUserInfo($data['uid']);

		if(empty($info))
		{
			$result = array(
				'status' => '0',
				'msg' => '无效的用户信息',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 检查用户登录状态
		$info['uid'] = $data['uid'];
		if(!$this->checkUserAuth($info, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($info['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

		if( md5($data['old_pay_pwd']) != $info['pay_pwd'] )
		{
			$result = array(
				'status' => '0',
				'msg' => '原支付密码不正确',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 支付密码不能与登录密码一致
		if( md5($data['new_pay_pwd']) == $info['pword'] )
		{
			$result = array(
				'status' => '0',
				'msg' => '支付密码不能与登录密码一致',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		$saveInfo = array(
			'uid' => $data['uid'],
			'pay_pwd' => md5($data['new_pay_pwd'])
		);

        if ($this->user_model->saveUserBase($saveInfo)) 
        {
            $result = array(
				'status' => '1',
				'msg' => '修改支付密码成功',
				'data' => ''
			);
        } 
        else 
        {
            $result = array(
				'status' => '0',
				'msg' => '修改支付密码失败',
				'data' => ''
			);
        }
        echo json_encode($result);
		exit();
	}

	/*
 	 * 通过手机号修改登录密码
 	 * @date:2016-09-07
 	 */
	public function setPwordByUser()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(empty($data['uid']) || empty($data['new_pword']) || empty($data['sessionid']) || empty($data['captcha']) || empty($data['auth']))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 获取用户基本信息
		$userInfo = $this->user_model->getUserInfo($data['uid']);

		if(empty($userInfo))
		{
			$result = array(
				'status' => '0',
				'msg' => '用户信息获取失败',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		// 检查用户登录状态
		$userInfo['uid'] = $data['uid'];
		if(!$this->checkUserAuth($userInfo, $data['auth']))
		{
			$result = array(
				'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
				'msg' 		=> 	'您的登录密码已修改，请重新登录',
				'data' 		=> 	''
			);
			echo json_encode($result);
			exit();
		}

		// 单设备登录检查
        $checkData = $this->checkUserLogin($userInfo['uid']);
        if(!$checkData['status'])
        {
            $result = array(
                'status'    =>  $checkData['code'],
                'msg'       =>  $checkData['msg'],
                'data'      =>  '',
            );
            echo json_encode($result);
            exit();
        }

        // 6至16位字母，数字，符号组合
		$rule = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,16}$/';
		if( !preg_match($rule, $data['new_pword']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '登录密码格式不正确',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 验证码校验
        if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));

		if ( empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0] || $userInfo['phone'] != $codestr[3] ) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('checkPwordError', $this->primarysession->getArg('checkPwordError') + 1);

			if($this->primarysession->getArg('checkPwordError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('checkPwordError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码不正确',
					'data' => ''
				);
			}
		}
		else
		{
			// 清除有效验证码
			$this->primarysession->setArg('app_captcha', '');

            $uniqid = substr(uniqid(), 0, 9);
            $pwd = md5($data['new_pword']) . $uniqid;  
			// 组装数据
			$saveInfo = array(
				'uid' 	=>	$data['uid'],
				'pword'	=>	strCode($pwd, 'ENCODE'),
                'salt' 	=> 	$uniqid,
			);

			// 更新登录密码
			if( $this->user_model->saveUser($saveInfo) ) 
	        {
	            $this->user_model->freshUserInfo($data['uid']);

	            // 重新获取用户信息
	            $uinfo = $this->user_model->getUserInfo($data['uid']);
	            $uinfo['uid'] = $data['uid'];
	            $uinfo['money'] = number_format(ParseUnit($uinfo['money'], 1), 2);
				$uinfo['nick_name'] = (!empty($uinfo['nick_name']))?$uinfo['nick_name']:$this->config->item('defaultName');
				// 短信通知开关
				$uinfo['msg_send'] = isset($uinfo['msg_send'])?$uinfo['msg_send']:'0';
				// 中奖推送开关
				$uinfo['push_status'] = isset($uinfo['push_status'])?$uinfo['push_status']:'1';
				// 红包个数
				$this->load->model('redpack_model');
				$redpack = $this->redpack_model->getUserRedpacks($uinfo['uid'], 1, 1);
				$uinfo['redpack'] = $redpack['totals']?$redpack['totals']:'0';

				$uinfo['token'] = $this->getUserToken($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);
				
	            $result = array(
					'status' => '1',
					'msg' => '修改登录密码成功',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
	        }
	        else
	        {
	        	$result = array(
					'status' => '0',
					'msg' => '修改登录密码失败',
					'data' => ''
				);
	        }
		}
		
		echo json_encode($result);
	}

	/*
 	 * 通过手机号修改登录密码
 	 * @date:2016-09-07
 	 */
	public function setPwordByPhone()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(empty($data['phone']) || empty($data['new_pword']) || empty($data['sessionid']) || empty($data['captcha']))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

		$userInfo = $this->user_model->getUinfoByType($data['phone'], 'phone');

		if(empty($userInfo))
		{
			$result = array(
				'status' => '0',
				'msg' => '获取用户信息失败',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 6至16位字母，数字，符号组合
		$rule = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{6,16}$/';
		if( !preg_match($rule, $data['new_pword']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '登录密码格式不正确',
				'data' => ''
			);
			echo json_encode($result);
			exit();
		}

        // 验证码校验
        if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();
		$codestr = explode(':', $this->primarysession->getArg('app_captcha'));

		if ( empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0] || $data['phone'] != $codestr[3] ) 
		{
			// 短信验证码过期机制
			$this->primarysession->setArg('checkPwordError', $this->primarysession->getArg('checkPwordError') + 1);

			if($this->primarysession->getArg('checkPwordError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('checkPwordError', 0); 

				$result = array(
					'status' => '0',
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '验证码不正确',
					'data' => ''
				);
			}
		}
		else
		{	        
			// 清除有效验证码
			$this->primarysession->setArg('app_captcha', '');

			// 组装数据
            $uniqid = substr(uniqid(), 0, 9);
            $pwd = md5($data['new_pword']) . $uniqid;
			$saveInfo = array(
				'uid'	=> 	$userInfo['uid'],
				'pword' =>	strCode($pwd, 'ENCODE'),
                'salt' 	=> 	$uniqid,
                'last_login_time' => date('Y-m-d H:i:s'),
            	'last_login_channel' => $this->recordChannel($this->versionInfo['channel']),
            	'visit_times' => 1,
			);

			// 更新登录密码
			if( $this->user_model->saveUser($saveInfo) ) 
	        {
	            $this->user_model->freshUserInfo($userInfo['uid']);

	            //登录记录
            	$refe = REFE ? REFE : '';
            	$loginRecord = array(
            		'login_time'	=>	date('Y-m-d H:i:s'), 
            		'uid' 			=> 	$userInfo['uid'], 
            		'ip' 			=> 	UCIP, 
            		'area' 			=> 	$this->tools->convertip(UCIP), 
            		'reffer' 		=> 	$refe, 
            		'platform' 		=> 	$this->config->item('platform'),
            		'model' 		=> 	$this->versionInfo['model'] ? $this->versionInfo['model'] : '',
    				'system' 		=> 	$this->versionInfo['OSVersion'] ? $this->versionInfo['OSVersion'] : '',
    				'version' 		=> 	$this->versionInfo['appVersionName'],
                        'channel'               =>      $this->recordChannel($this->versionInfo['channel']),
            	);
            	$this->user_model->loginRecord($loginRecord);

            	// 单设备登录
        		$this->recordIdfaLogin($userInfo['uid']);

	            // 重新获取用户信息
	            $uinfo = $this->user_model->getUserInfo($userInfo['uid']);
	            $uinfo['uid'] = $userInfo['uid'];
	            // 汇总用户信息
				$uinfo = $this->callbackUinfo($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);
				
				//消息入队
				apiRequest('common_stomp_send', 'login',array('uid' => $uinfo['uid'], 'last_login_time' => 0));
				
	            $result = array(
					'status' => '1',
					'msg' => '修改登录密码成功',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
	        }
	        else
	        {
	        	$result = array(
					'status' => '0',
					'msg' => '修改登录密码失败',
					'data' => ''
				);
	        }
		}
		
		echo json_encode($result);
	}

	/*
 	 * 设置用户昵称
 	 * @date:2016-01-18
 	 */
	public function setNickName()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		// 调试
		// $data = array(
		// 	'uid' => '4',
		// 	'nick_name' => 'test123'
		// );

		if(!empty($data['uid']) && $data['nick_name'] !== '')
		{
			$info = $this->user_model->getUserInfo($data['uid']);

			if(empty($info))
			{
				$result = array(
					'status' => '0',
					'msg' => '用户信息获取失败',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			// 检查用户登录状态
			$info['uid'] = $data['uid'];
			if(!$this->checkUserAuth($info, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($info['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			// 昵称长度限制
			if($this->comm->abslength($data['nick_name']) > 15 || $this->comm->abslength($data['nick_name']) < 2)
			{
				$result = array(
					'status' => '0',
					'msg' => '请输入2-15位字符',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
			if (!preg_match('/^[\d\w_\p{Han}]+$|^\w+[\w]*@[\w]+\.[\w]+$/u', $data['nick_name']) || (preg_match('/^[0-9]*$/u', $data['nick_name']))) 
			{
				$result = array(
					'status' => '0',
					'msg' => '抱歉，昵称格式不支持',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			if(($info['nick_name_modify_time'] > 0))
			{
				$result = array(
					'status' => '0',
					'msg' => '抱歉，您的昵称暂不支持修改',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}
			if($this->user_model->checkUname($data['nick_name']))
			{
				$result = array(
					'status' => '0',
					'msg' => '该昵称已被使用，请换一个试试',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			// 修改昵称
			$userData = array(
				'uid' => $data['uid'],
				'nick_name' => $data['nick_name'],
				'nick_name_modify_time' => date('Y-m-d H:i:s')
			);

			if($this->user_model->updateUname($userData))
			{
				$uinfo = $this->user_model->getUserInfo($data['uid']);
				$uinfo['uid'] = $data['uid'];
				// 汇总用户信息
				$uinfo = $this->callbackUinfo($uinfo);
				$uinfo['auth'] = $this->getUserAuth($uinfo);

				$result = array(
					'status' => '1',
					'msg' => '设置昵称成功',
					'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '设置昵称失败',
					'data' => ''
				);
			}
		}
		echo json_encode($result);
		exit();
	}

	/*
 	 * 修改短信通知开关
 	 * @date:2016-01-18
 	 */
	public function setMsgSend()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(!empty($data['uid']) && $data['msg_send'] !== '')
		{
			$info = $this->user_model->getUserInfo($data['uid']);

			if(empty($info))
			{
				$result = array(
					'status' => '0',
					'msg' => '用户信息获取失败',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			$info['uid'] = $data['uid'];
			if(!$this->checkUserAuth($info, $data['auth']) && $this->versionInfo['appVersionCode'] >= 2)
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($info['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			// 组装参数
			$postData = array(
				'uid' => $data['uid'],
				'msg_send' => $data['msg_send']
			);

			// 初始化订单信息
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

			$postStatus = $this->tools->request($postUrl . 'api/user/updateMsgsend', $postData);
			$postStatus = json_decode($postStatus, true);

			if($postStatus['codes'] == '200')
			{
				$result = array(
					'status' => '1',
					'msg' => '修改成功',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '修改失败',
					'data' => ''
				);
			}
		}

		echo json_encode($result);
		exit();
	}

	/*
 	 * 中奖推送开关
 	 * @date:2016-06-21
 	 */
	public function setPushSend()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(!empty($data['uid']) && $data['push_status'] !== '')
		{
			$info = $this->user_model->getUserInfo($data['uid']);

			if(empty($info))
			{
				$result = array(
					'status' => '0',
					'msg' => '用户信息获取失败',
					'data' => ''
				);
				echo json_encode($result);
				exit();
			}

			$info['uid'] = $data['uid'];
			if(!$this->checkUserAuth($info, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($info['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			// 组装参数
			$postData = array(
				'uid' => $data['uid'],
				'push_status' => $data['push_status']
			);

			// 初始化订单信息
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

			$postStatus = $this->tools->request($postUrl . 'api/user/updatePushsend', $postData);
			$postStatus = json_decode($postStatus, true);

			if($postStatus['codes'] == '200')
			{
				$result = array(
					'status' => '1',
					'msg' => '修改成功',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '修改失败',
					'data' => ''
				);
			}
		}

		echo json_encode($result);
		exit();
	}

	/*
 	 * 绑定邮箱
 	 * @date:2016-09-22
 	 */
	public function setEmail()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$data['sessionid'] = $data['sessionid']?$data['sessionid']:'';

		if($data['sessionid']) $this->primarysession->setSessionId($data['sessionid']);
		$this->primarysession->startSession();

		if(!empty($data['uid']) && !empty($data['email']) && !empty($data['auth']))
		{
			
			$uinfo = $this->user_model->getUserInfo($data['uid']);
			$uinfo['uid'] = $data['uid'];

			if(!$this->checkUserAuth($uinfo, $data['auth']))
			{
				$result = array(
					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
					'msg' 		=> 	'您的登录密码已修改，请重新登录',
					'data' 		=> 	''
				);
				echo json_encode($result);
				exit();
			}

			// 单设备登录检查
	        $checkData = $this->checkUserLogin($uinfo['uid']);
	        if(!$checkData['status'])
	        {
	            $result = array(
	                'status'    =>  $checkData['code'],
	                'msg'       =>  $checkData['msg'],
	                'data'      =>  '',
	            );
	            echo json_encode($result);
	            exit();
	        }

			$this->load->library('BindEmail');
			$result = $this->bindemail->appbindEmail($data, $uinfo);
		}
		echo json_encode($result);
		exit();
	}
	
    // 更新推送信息
    public function updatePushStatus()
    {
    	$result = array(
    		'status' => '0',
    		'msg' => '缺少必要参数',
    		'data' => ''
    	);

    	$data = $this->strCode($this->input->post('data'));
    	$data = json_decode($data, true);

    	// 推送标识位
    	$pushTag = array(
    		'2' => 4,		// 红包推送
    	);

    	if(!empty($data['uid']) && !empty($pushTag[$data['ctype']]))
    	{
    		$uinfo = $this->user_model->getUserInfo($data['uid']);
    		if(!empty($uinfo))
    		{
    			$uinfo['uid'] = $data['uid'];

    			// 检查用户登录状态
    			if(!$this->checkUserAuth($uinfo, $data['auth']))
    			{
    				$result = array(
    					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
    					'msg' 		=> 	'您的登录密码已修改，请重新登录',
    					'data' 		=> 	''
    				);
    				echo json_encode($result);
    				exit();
    			}

    			// 单设备登录检查
		        $checkData = $this->checkUserLogin($uinfo['uid']);
		        if(!$checkData['status'])
		        {
		            $result = array(
		                'status'    =>  $checkData['code'],
		                'msg'       =>  $checkData['msg'],
		                'data'      =>  '',
		            );
		            echo json_encode($result);
		            exit();
		        }

    			// 用户是否注销
    			if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
    			{
    				$result = array(
    					'status'	=>	($this->versionInfo['appVersionCode'] >= '3020001') ? '700' : '300',
    					'msg' 		=> 	'您的账号已注销，被注销的账号不能使用原手机号再注册，请注册新账号登录',
    					'data' 		=> 	''
    				);
    				echo json_encode($result);
    				exit();
    			}

    			$status = $data['status'] ? '1' : '0';		// 1 开 0 关

    			// 组装参数
    			$postData = array(
    				'uid' 			=> 	$data['uid'],
    				'push_status' 	=> 	$status,
    				'position' 		=> 	$pushTag[$data['ctype']]
    			);

	    		// 初始化订单信息
	    		if(ENVIRONMENT === 'checkout')
	    		{
	    			$postUrl = $this->config->item('cp_host');
	    			$postData['HOST'] = $this->config->item('domain');
				}
				else
				{
					$postUrl = $this->config->item('protocol') . $this->config->item('pages_url');
				}

				$postStatus = $this->tools->request($postUrl . 'api/user/updatePushStatus', $postData);
				$postStatus = json_decode($postStatus, true);

				if($postStatus['codes'] == '200')
				{
					// 汇总返回数据
					$uinfo = $this->callbackUinfo($uinfo);
					$uinfo['auth'] = $this->getUserAuth($uinfo);

					$result = array(
						'status' => '1',
						'msg' => '修改成功',
						'data' => $this->strCode(json_encode($uinfo), 'ENCODE')
					);
				}
				else
				{
					$result = array(
						'status' => '0',
						'msg' => '修改失败',
						'data' => ''
					);
				}
			}
		}
		echo json_encode($result);	
    }

    // 推送设置通知
    public function pushNotice()
    {
    	$result = array(
    		'status' => '0',
    		'msg' => '缺少必要参数',
    		'data' => ''
    	);

    	$data = $this->strCode($this->input->post('data'));
    	$data = json_decode($data, true);

    	// switch 格式 id|0,id|1
    	if($data['uid'] && $data['push_time'] && $data['switchData'])
    	{
    		$this->load->model('mipush_model');
	    	$uinfo = $this->mipush_model->getUserPushConfig($data['uid']);

	    	$bet_push = 0;
	    	if($uinfo)
	    	{
	    		$bet_push = $uinfo['bet_push'];
	    	}

	    	// 标志位
	    	$pushTag = array(
	    		'51'	=>	1,
	    		'23529'	=>	2,
	    	);

	    	// 解析提交参数
	    	foreach (explode(',', $data['switchData']) as $key => $items) 
	    	{
	    		$pushStr = explode('|', $items);
	    		$status = $pushStr[1] ? 1 : 0;

	    		$cstate = $pushTag[$pushStr[0]];
	    		if(empty($cstate))
	    		{
	    			continue;
	    		}

	    		if($status)
	    		{
	    			// 开启推送
	    			if( ($bet_push & $cstate) != 0 )
	    			{
	    				$bet_push = ($bet_push ^ $cstate);
	    			}
	    		}
	    		else
	    		{
	    			// 关闭推送
	    			if( ($bet_push & $cstate) == 0 )
	    			{
	    				$bet_push = ($bet_push ^ $cstate);
	    			}		
	    		}
	    	}
	    	
	    	$userData = array(
	    		'uid'		=>	$data['uid'],
	    		'push_time'	=>	trim($data['push_time'] . ':00'),
	    		'bet_push'	=>	$bet_push
	    	);

	    	$res = $this->mipush_model->saveUserPushConfig($userData);

	    	if($res)
	    	{
	    		$result = array(
					'status' => '1',
					'msg' => '保存成功',
					'data' => ''
				);
	    	}
    	}

    	echo json_encode($result);
    }
    
    public function memberInfo()
    {
        $redata = json_decode($this->strCode($this->input->post('data')), true);
        //        $redata = array(
        //            'uid' => '1024', //当前登陆用户uid
        //        );
        if (empty($redata['uid'])) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model', 'User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1') {
            $result = array(
                'status' => '300',
                'msg' => '用户登录信息过期',
                'data' => $redata
            );
            die(json_encode($result));
        }
        if ($uinfo['userStatus'] == '2') {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('member_model');
        $level = $this->member_model->getGrowthLevel();
        foreach ($level as $k => $v) {
            $level[$k]['privilege'] = json_decode($v['privilege'], true);
            $next = $uinfo['grade'] + 1;
            $next = $next > 6 ? 6 : $next;
            if ($v['grade'] == $next) {
                $uinfo['next_grade_name'] = $v['grade_name'];
                $uinfo['next_grade_value'] = $v['value_start'];
            }
            if ($v['grade'] == $uinfo['grade']) {
                $privilege = json_decode($v['privilege'], true);
            }
        }
        $user = array();
        $user['uid'] = $uinfo['uid'];
        $user['avatar'] = $uinfo['headimgurl'];
        $user['levelpic'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/prompt/level_v' . $uinfo['grade'] . '_medal.png';
        $user['uname'] = $uinfo['uname'];
        $user['grade'] = $uinfo['grade'];
        $user['cycle_end'] = date('Y-m-d 24:00',strtotime($uinfo['cycle_end']));
        $user['grade_value'] = $uinfo['grade_value'];
        if ($uinfo['grade'] <= 5) {
            $user['difference'] = $uinfo['next_grade_value'] - $uinfo['grade_value'];
            $user['percentage'] = round(($uinfo['grade_value'] / $uinfo['next_grade_value']) * 100, 0);
        } else {
            $user['difference'] = 0;
            $user['percentage'] = 100;
        }
        $privileges = array();
        $imgs = array(
            'caipiaoimg/static/images/privilege/privilege_show_one_yes.png',
            'caipiaoimg/static/images/privilege/privilege_show_two_yes.png',
            'caipiaoimg/static/images/privilege/privilege_show_three_yes.png',
            'caipiaoimg/static/images/privilege/privilege_show_four_yes.png',
            'caipiaoimg/static/images/privilege/privilege_show_five_yes.png',
            'caipiaoimg/static/images/privilege/privilege_show_six_yes.png',
        );
        $noimgs = array(
            'caipiaoimg/static/images/privilege/privilege_show_one_no.png',
            'caipiaoimg/static/images/privilege/privilege_show_two_no.png',
            'caipiaoimg/static/images/privilege/privilege_show_three_no.png',
            'caipiaoimg/static/images/privilege/privilege_show_four_no.png',
            'caipiaoimg/static/images/privilege/privilege_show_five_no.png',
            'caipiaoimg/static/images/privilege/privilege_show_six_no.png',
        );
        $upgrades = array(16, 16, 16, 66, 266, 1666);
        $tixian = array(3, 3, 3, 4, 5, 8);
        foreach ($level as $j => $v) {
            $k = 0;
            foreach ($privilege as $v) {
                $privileges[$j][$k]['id'] = $k + 1;
                $privileges[$j][$k]['imgurl'] = $this->config->item('protocol') . $this->config->item('pages_url') . $imgs[$k];
                $privileges[$j][$k]['jumpurl'] = $this->config->item('protocol') . $this->config->item('pages_url') . 'ios/points/privilege/' . $privileges[$j][$k]['id'];
                switch ($k) {
                    case 0:
                        $privileges[$j][$k]['name'] = "身份勋章";
                        $privileges[$j][$k]['des'] = "等级身份象征";
                        $privileges[$j][$k]['light'] = 1;
                        break;
                    case 1:
                        $privileges[$j][$k]['name'] = "提现特权";
                        $privileges[$j][$k]['des'] = "每日{$tixian[$j]}次机会";
                        $privileges[$j][$k]['light'] = 1;
                        break;
                    case 2:
                        $privileges[$j][$k]['name'] = "积分兑换";
                        $privileges[$j][$k]['des'] = "积分商城兑好礼";
                        $privileges[$j][$k]['light'] = $j>= 1 ? 1 : 0;
                        $privileges[$j][$k]['imgurl'] = $privileges[$j][$k]['light'] == 0 ? $this->config->item('protocol') . $this->config->item('pages_url') . $noimgs[$k] : $privileges[$j][$k]['imgurl'];
                        break;
                    case 3:
                        $privileges[$j][$k]['name'] = "升级礼包";
                        $privileges[$j][$k]['des'] = "{$upgrades[$j]}元升级礼包";
                        $privileges[$j][$k]['light'] = $j>= 2 ? 1 : 0;
                        $privileges[$j][$k]['imgurl'] = $privileges[$j][$k]['light'] == 0 ? $this->config->item('protocol') . $this->config->item('pages_url') . $noimgs[$k] : $privileges[$j][$k]['imgurl'];
                        break;
                    case 4:
                        $privileges[$j][$k]['name'] = "生日礼包";
                        $privileges[$j][$k]['des'] = "超值满减红包";
                        $privileges[$j][$k]['light'] = $j>= 3 ? 1 : 0;
                        $privileges[$j][$k]['imgurl'] = $privileges[$j][$k]['light'] == 0 ? $this->config->item('protocol') . $this->config->item('pages_url') . $noimgs[$k] : $privileges[$j][$k]['imgurl'];
                        break;
                    case 5:
                        $privileges[$j][$k]['name'] = "积分双倍";
                        $privileges[$j][$k]['des'] = "购彩双倍积分";
                        $privileges[$j][$k]['light'] = $j>= 5 ? 1 : 0;
                        $privileges[$j][$k]['imgurl'] = $privileges[$j][$k]['light'] == 0 ? $this->config->item('protocol') . $this->config->item('pages_url') . $noimgs[$k] : $privileges[$j][$k]['imgurl'];
                        break;
                }
                $k++;
            }
        }
        $user['privileges'] = $privileges;
        $status = $this->User->getPopStatus($redata['uid']);
        if (!empty($status)) {
            $gradeName = array(
                1 => '新手',
                2 => '青铜',
                3 => '白银',
                4 => '黄金',
                5 => '铂金',
                6 => '钻石',
            );
            $pop = array(
                'type' => 1,
                'congratulations' => '恭喜您',
                'des' => '晋升为' . $gradeName[$status['grade']] . '彩民',
                'picurl' => $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/prompt/level_v' . $status['grade'] . '_medal.png'
            );
            $this->User->setPopStatus($redata['uid']);
        } else {
            $pop = "";
        }
        $user['prompt'] = $pop;
        $result = array(
            'status' => '1',
            'msg' => '成功',
            'data' => $user
        );
        echo json_encode($result);
    }
    
    public function uploadImg()
    {
        $redata = json_decode($this->strCode($this->input->post('data')), true);
        $avatar = $_FILES['avatar'];
        //        $redata = array(
        //            'uid' => '1024', //当前登陆用户uid
        //        );
        if (empty($redata['uid'])) {
            $result = array(
                'status' => '300',
                'msg' => '未登录',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $this->load->model('user_model', 'User');
        $uinfo = $this->User->getUserInfo($redata['uid']);
        if (isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1') {
            $result = array(
                'status' => '300',
                'msg' => '用户登录信息过期',
                'data' => $redata
            );
            die(json_encode($result));
        }
        if ($uinfo['userStatus'] == '2') {
            $result = array(
                'status' => '0',
                'msg' => '您的账户已被冻结，如需解冻请联系客服。',
                'data' => $redata
            );
            die(json_encode($result));
        }
        if ($uinfo['headimg_status'] == 2) {
            $result = array(
                'status' => '0',
                'msg' => '您因不当操作或敏感信息被禁止上传头像',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $uploadConfig = $this->User->uploadImgConfig(2);
        if ($uploadConfig['headimg_config'] == 1) {
            $result = array(
                'status' => '0',
                'msg' => '系统维护中，暂不支持上传头像',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $count = $this->User->countUploadImg($redata['uid'], date("Y-m-d"));
        if ($count['count'] >= 3) {
            $result = array(
                'status' => '0',
                'msg' => '您好，每天上传头像最多3次',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $fileExt = strtoupper(end(explode('.', $avatar['name'])));
        if ($fileExt != 'JPEG') {
            $result = array(
                'status' => '0',
                'msg' => '上传格式不支持',
                'data' => $redata
            );
            die(json_encode($result));
        }
        $file_path = dirname(BASEPATH) . '/cpiaoimg/headimg/';
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $file_name = md5(time() . rand(1, 1000)) . '.jpeg';
        move_uploaded_file($avatar["tmp_name"], $file_path . $file_name);
        $static_url = $this->config->item('img_url');
        shuffle($static_url);
        if (ENVIRONMENT === 'production') {
            $img_url = 'https:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
        } else {
            $img_url = 'http:' . $static_url[0] . 'cpiaoimg/headimg/' . $file_name;
        }
        $this->User->uploadImg($img_url, $redata['uid']);
        $result = array(
            'status' => '1',
            'msg' => '头像上传成功',
            'data' => array(
                'avatar' => $img_url
            )
        );
        echo json_encode($result);
    }
}