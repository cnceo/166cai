<?php

/*
 * IOS 账户安全中心 @date:2015-05-14
 */
class Safe extends MY_Controller
{

	public function __construct()
	{
		parent::__construct ();
		$this->load->library ( 'tools' );
		$this->load->library ( 'comm' );
		$this->load->model ( 'User_model', 'User' );
		$this->checkUserAgent ();
	}
	
	/*
	 * IOS 账户安全设置首页 @date:2015-05-14
	 */
	public function index()
	{
		$this->load->view ( 'safe/index', $info );
	}
	
	/*
	 * IOS 找回账户 @date:2015-05-14
	 */
	public function findAccount()
	{
		if ($this->input->is_ajax_request ())
		{
			$data = $this->input->post ( null, true );
			
			$res = $this->validateAccountInfo ( $data );
			
			if ($res ['status'])
			{
				$result = array (
						'status' => '1',
						'msg' => '验证通过',
						'data' => $this->load->view ( 'safe/findAccount_result', $res ['data'], true ) 
				);
			} else
			{
				$result = array (
						'status' => '0',
						'msg' => $res ['msg'],
						'data' => '' 
				);
			}
			die ( json_encode ( $result ) );
		} else
		{
			$data = $this->input->get ( null, true );
			$this->load->view ( 'safe/findAccount_' . $data ['type'] );
		}
	}
	
	/*
	 * IOS 找回账户验证 @date:2015-05-14
	 */
	public function validateAccountInfo($data)
	{
		switch ($data ['action'])
		{
			case 'phone' :
				// 必要参数验证
				if (! empty ( $data ['phoneNum'] ) && ! empty ( $data ['vCode'] ))
				{
					// 验证码校验
					$this->primarysession->startSession ();
					$codestr = explode ( ':', $this->primarysession->getArg ( 'captcha_' . $data ['phoneNum'] ) );
					if (empty ( $data ['vCode'] ) || $codestr [1] < time () || strtolower ( $data ['vCode'] ) != $codestr [0])
					{
						$result = array (
								'status' => FALSE,
								'msg' => '验证码不正确',
								'data' => '' 
						);
					} else
					{
						$userinfo = $this->User->getUinfoByType ( $data ['phoneNum'], $type = 'phone' );
						if ($userinfo)
						{
							$result = array (
									'status' => TRUE,
									'msg' => '验证码正确',
									'data' => $userinfo 
							);
						} else
						{
							$result = array (
									'status' => FALSE,
									'msg' => '用户信息不存在',
									'data' => '' 
							);
						}
					}
				} else
				{
					$result = array (
							'status' => FALSE,
							'msg' => '缺少必要参数',
							'data' => '' 
					);
				}
				break;
			case 'email' :
				// 验证邮箱格式
				$userinfo = $this->User->getUinfoByType ( $data ['email'], $type = 'email' );
				if ($userinfo)
				{
					$message ['to'] = trim ( $data ['email'] );
					$message ['subject'] = '找回2345账号';
					// 添加view
					$message ['message'] = $this->load->view ( 'safe/findAccount_email_tpl', $userinfo, TRUE );
					$res_sendMail = $this->tools->sendMail ( $message );
					$result = array (
							'status' => FALSE,
							'msg' => '已发送至邮箱',
							'data' => '' 
					);
				} else
				{
					$result = array (
							'status' => FALSE,
							'msg' => '邮箱不存在',
							'data' => '' 
					);
				}
				break;
			case 'setPwd' :
				if ($data ['token'])
				{
					$reData = $this->strCode ( $data ['token'] );
					$reData = json_decode ( $reData, true );
					
					if ($reData ['uid'])
					{
						// 查询用户信息
						$info = $this->User->getUserInfo ( $reData ['uid'] );
						
						if ($info)
						{
							// 密码规则
							if (! preg_match ( '/^[a-zA-Z\d_\W]{6,16}$/', $data ['newPsw'] ))
							{
								$result = array (
										'status' => FALSE,
										'msg' => '密码格式错误',
										'data' => '' 
								);
							} elseif (md5 ( $data ['newPsw'] ) == $info ['pay_pwd'])
							{
								$result = array (
										'status' => FALSE,
										'msg' => '不能与支付密码一致',
										'data' => '' 
								);
							} else
							{
								// 修改登录密码
								$PostData = array ();
								$edit_url = $this->config->item ( 'update_password' );
								$PostData ['uid'] = $reData ['uid'];
								$PostData ['passid'] = $info ['passid'];
								$PostData ['password'] = $data ['newPsw'];
								// 用户中心接口切换
								if (ENVIRONMENT != 'production')
								{
									$PostData ['HOST'] = 'login.2345.com';
								}
								$redata = $this->tools->request ( $edit_url, $PostData );
								// 返回状态处理
								if ($redata == 'success')
								{
									$sdata ['uid'] = $reData ['uid'];
									$sdata ['pword'] = $data ['newPsw'];
									if ($this->User->saveUser ( $sdata ))
									{
										$this->User->freshUserInfo ( $reData ['uid'] );
									}
									$result = array (
											'status' => TRUE,
											'msg' => '登录密码修改成功',
											'data' => '' 
									);
								} else
								{
									$result = array (
											'status' => FALSE,
											'msg' => '登录密码修改失败',
											'data' => '' 
									);
								}
							}
						} else
						{
							$result = array (
									'status' => FALSE,
									'msg' => '用户信息不存在',
									'data' => '' 
							);
						}
					} else
					{
						$result = array (
								'status' => FALSE,
								'msg' => '参数校验失败',
								'data' => '' 
						);
					}
				} else
				{
					$result = array (
							'status' => FALSE,
							'msg' => '缺少必要参数，无效的请求',
							'data' => '' 
					);
				}
				break;
			case 'setPwdByEmail' :
				$userinfo = $this->User->getUinfoByType ( $data ['email'], $type = 'email' );
				if ($userinfo)
				{
					// 2.1 组装用户信息
					$arr = array ();
					$arr ['passid'] = $userinfo ['passid']; // 用户标识
					$arr ['timestamp'] = time (); // 时间戳
					$arr ['tracker'] = 'emailSever'; // 渠道来源
					$arr ['loginType'] = 0;
					
					// 2.2 信息加密
					$sign_data = array ();
					$sign_data ['passid'] = $arr ['passid'];
					$sign_data ['timestamp'] = $arr ['timestamp'];
					$sign_data ['nonce'] = rand ( 10000000, 99999999 );
					$sign_data ['tracker'] = $arr ['tracker'];
					$sign_data ['loginType'] = $arr ['loginType'];
					$sha1_data = $this->comm->auth_data ( $sign_data );
					$sign = $sha1_data ['sign'];
					// 2.3 组装URL地址
					$validate_url = $this->config->item ( 'pages_url' ) . 'safe/find_password?';
					$validate_url .= 'passid=' . $arr ['passid'];
					$validate_url .= '&timestamp=' . $arr ['timestamp'];
					$validate_url .= '&nonce=' . $sign_data ['nonce'];
					$validate_url .= '&tracker=' . $arr ['tracker'];
					$validate_url .= '&loginType=' . $arr ['loginType'];
					$validate_url .= '&sign=' . $sign;
					// 2.4 验证邮箱地址
					$message ['to'] = $data ['email'];
					$message ['subject'] = '修改2345登录密码';
					// 添加view
					$e_data = array ();
					$e_data ['validate_url'] = $validate_url;
					$message ['message'] = $this->load->view ( 'safe/findPwd_email_tpl', $e_data, TRUE );
					$res_email = $this->tools->sendMail ( $message );
					$result = array (
							'status' => FALSE,
							'msg' => '已发送至邮箱',
							'data' => '' 
					);
				} else
				{
					$result = array (
							'status' => FALSE,
							'msg' => '邮箱不存在',
							'data' => '' 
					);
				}
				break;
			default :
				$result = array (
						'status' => FALSE,
						'msg' => '无效的请求',
						'data' => '' 
				);
				break;
		}
		return $result;
	}
	
	/*
	 * IOS 重设密码 @date:2015-05-15
	 */
	public function findPwd()
	{
		if ($this->input->is_ajax_request ())
		{
			$data = $this->input->post ( null, true );
			
			$res = $this->validateAccountInfo ( $data );
			
			if ($res ['status'])
			{
				if ($data ['action'] == 'setPwd')
				{
					$result = array (
							'status' => '1',
							'msg' => $res ['msg'],
							'data' => '' 
					);
				} else
				{
					// 加密用户信息
					$res ['token'] = $this->strCode ( json_encode ( array (
							'uid' => $res ['data'] ['uid'] 
					) ), 'ENCODE' );
					
					$setView = $this->config->item ( 'pages_url' ) . "ios/safe/setPwd/" . urlencode ( $res ['token'] );
					$result = array (
							'status' => '1',
							'msg' => $res ['msg'],
							'data' => $setView 
					);
				}
			} else
			{
				$result = array (
						'status' => '0',
						'msg' => $res ['msg'],
						'data' => '' 
				);
			}
			die ( json_encode ( $result ) );
		} else
		{
			$data = $this->input->get ( null, true );
			$this->load->view ( 'safe/findPwd_' . $data ['type'] );
		}
	}
	
	// 重设密码
	public function setPwd($token)
	{
		$data = $this->strCode ( urldecode ( $token ) );
		$data = json_decode ( $data, true );
		
		if (empty ( $data ['uid'] ))
		{
			echo "参数错误！";
			die ();
		}
		
		$this->load->view ( 'safe/findPwd_phone_1', array (
				'token' => $token 
		) );
	}
}