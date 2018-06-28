<?php

/*
 * APP 提现
 * @date:2015-05-15
 */

 class Withdraw extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->library('tools');
		$this->load->library('comm');
		$this->load->model('user_model');
		$this->load->model('newwallet_model');
		$this->checkUserAgent();
	}

	/*
	 * APP 提现 - 选择银行卡
	 * @date:2015-05-15
	 */
	public function index($token, $id)
	{	
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);
		
		if( empty($data['uid']) || !isset($id) )
		{
			echo "参数错误！";
			die;
		}
		

		// 查询银行卡绑定信息
		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

		if(empty($bankInfo))
		{	
			echo "无绑定银行卡";
			die; 
		}
		// 获取账户金额
		$withDrawMoney = $this->newwallet_model->getWithDraw($data['uid']);

                $money = $this->user_model->getUserInfo($data['uid']);
		$bank_id = '';
		if(empty($id))
		{
			// 获取默认卡信息
			foreach ($bankInfo as $banks) 
			{
				if($banks['is_default'] == '1')
	            {
	            	$id = $banks['id'];
	                $bank_id = $banks['bank_id'];
	                $bank_type = $banks['bank_type'];
	                $is_default = $banks['is_default'];
	            }
			}
		}
		else
		{
			foreach ($bankInfo as $banks) 
			{
				if($banks['id'] == $id)
	            {
	            	$id = $banks['id'];
	                $bank_id = $banks['bank_id'];
	                $bank_type = $banks['bank_type'];
	                $is_default = $banks['is_default'];
	            }
			}
		}

		if(empty($bank_id))
		{
			echo "此银行卡未绑定或已删除";
			die;
		}

		// 组装参数
		$info = array(
			'id' =>  $id,
			'bank_type' => $bank_type,
			'bank_id' => $bank_id,
			'is_default' => $is_default,
			'token' => $token,
			'withDrawMoney' => $withDrawMoney,
                        'money' => $money
		);
		
		// 提现页面
		$this->load->view('withdraw/index', $info);

	}

	/*
	 * APP 提现 - 申请提交
	 * @date:2015-05-15
	 */
	public function apply($token)
	{	
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);

		if(empty($data['uid']))
		{
			echo "参数错误！";
			die;
		}

		// 查询银行卡绑定信息
		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

		if(empty($bankInfo))
		{
			echo "未绑定银行卡";
			die;
		}

		$this->load->view('withdraw/apply', array('token'=>$token, 'bankInfo'=>$bankInfo));
	}

	/*
	 * APP 提现 - 申请提交 step1
	 * @date:2015-05-15
	 */
	public function applyWithdraw()
	{
		$data['moneyNum'] = $this->input->post('moneyNum', true);
		$data['uname'] = $this->input->post('uname', true);
		$data['token'] = $this->input->post('token');
		$data['bankId'] = $this->input->post('bankId', true);
		$data['action'] = $this->input->post('action', true);
		$data['captcha'] = $this->input->post('captcha', true);

		if( empty($data['moneyNum']) || empty($data['token']) || empty($data['bankId']) || empty($data['action']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要参数',
				'data' => ''
			);
			die(json_encode($result));
		}

		$uinfo = $this->strCode(urldecode($data['token']));
		$uinfo = json_decode($uinfo, true);

		if(empty($uinfo['uid']))
		{
			$result = array(
				'status' => '0',
				'msg' => '参数校验失败',
				'data' => ''
			);
			die(json_encode($result));
		}
		
		$user = $this->user_model->getUserInfo($uinfo['uid']);
		if($user['userStatus'] == '2')
		{
			$result = array(
					'status' => '0',
					'msg' => '您的账户已被冻结，如需解冻请联系客服。',
					'data' => ''
			);
			die(json_encode($result));
		}

		//账户可提现金额
		$withDrawMoney = $this->newwallet_model->getWithDraw($uinfo['uid']); 
		// 今日申请提现
        $isTodayWithdraw = $this->isTodayWithdraw($uinfo['uid']); 
        //绑定银行卡信息
        $bankInfo = $this->user_model->freshBankInfo($uinfo['uid']); 

        // 检查账户提现条件
        // $moneyNum = ParseUnit($data['moneyNum']);
        $data['moneyNum'] = doubleval($data['moneyNum']);
        if(intval(ParseUnit($data['moneyNum'])) < 1)
        {
        	$result = array(
				'status' => '0',
				'msg' => '提现金额格式错误',
				'data' => ''
			);
			die(json_encode($result));
        }

        if(intval(ParseUnit($data['moneyNum'])) < 1000)
        {
        	$result = array(
				'status' => '0',
				'msg' => '单笔提现金额至少10元',
				'data' => ''
			);
			die(json_encode($result));
        }
        
        if(intval(ParseUnit($data['moneyNum'])) > $withDrawMoney)
        {
        	$result = array(
				'status' => '0',
				'msg' => '可提现余额不足',
				'data' => ''
			);
			die(json_encode($result));
        }

        if ($isTodayWithdraw) 
        {
            $result = array(
				'status' => '0',
				'msg' => '已达每日提现上限，请明日再来哦',
				'data' => ''
			);
			die(json_encode($result));
        }

        // 提现银行卡检查
        if(empty($bankInfo))
        {
        	$result = array(
				'status' => '0',
				'msg' => '尚未绑定银行卡',
				'data' => ''
			);
			die(json_encode($result));
        }

        $data['card_id'] = '';
        foreach ($bankInfo as $banks) 
        {
            if($banks['id'] == $data['bankId'])
            {
                $data['card_id'] = $banks['bank_id'];
                $data['bank_type'] = $banks['bank_type'];
            }
        }

        if(empty($data['card_id']))
        {
        	$result = array(
				'status' => '0',
				'msg' => '银行卡不存在或已删除',
				'data' => ''
			);
			die(json_encode($result));
        }

        // 获取版本信息
        $versionInfo = $this->version;
        $data['platform'] = $this->config->item('platform');
        $data['app_version'] = !empty($versionInfo['appVersionName']) ? $versionInfo['appVersionName'] : '1.0';
		$data['channel'] = $this->recordChannel($versionInfo['channel']);

        switch ($data['action']) 
        {
        	// 验证开户名
        	case '1': 
        		$returnData = $this->validateRealName($uinfo['uid'], $data);
        		break;
    		// 验证支付密码 // 验证手机号
        	//case '2':
        		// $returnData = $this->validatePayPwd($uinfo, $data);
        		//$returnData = $this->validatePhone($uinfo, $data);
        		//break;
        	default:
        		$returnData = array(
					'status' => FALSE,
					'msg' => '未识别的请求',
					'data' => ''
				);
        		break;
        }

        if($returnData['status'])
        {
        	/*if($data['action'] == 1)
        	{
        		$tokenStr = $this->strCode(json_encode(array(
					'uid' => $uinfo['uid'],
					'moneyNum' => $data['moneyNum']
				)), 'ENCODE');
	        	$result = array(
					'status' => '1',
					'msg' => '实名信息验证通过',
					'data' => $this->config->item('pages_url') . 'app/withdraw/phone/' . urlencode($tokenStr) . '/' . $data['bankId']
					// 'data' => $this->config->item('pages_url') . 'app/withdraw/doWithdraw/' . urlencode($tokenStr) . '/' . $data['bankId']
				);
        	}
        	else
        	{
        		$result = array(
					'status' => '2',
					'msg' => $returnData['msg'],
					'data' => $returnData['data']
				);
        	}*/ 
        	$result = array(
        		'status' => '1',
        		'msg' => $returnData['msg'],
        		'data' => $returnData['data']
        	);
        	die(json_encode($result));
        }
        else
        {
        	$result = array(
				'status' => '0',
				'msg' => $returnData['msg'],
				'data' => ''
			);
			die(json_encode($result));
        }
	}

	/*
	 * APP 提现 - 验证实名信息
	 * @date:2015-05-15
	 */
	public function validateRealName($uid, $postData)
	{
		$info = $this->user_model->getUserInfo($uid);
        if( $postData['uname'] != $info['real_name'] )
        {
        	$result = array(
				'status' => FALSE,
				'msg' => '真实姓名错误',
				'data' => ''
			);
        }
        else
        {
        	$additions = '0@'.$postData['bank_type'].'@'.$postData['card_id'];
			$postData['moneyNum'] = intval(ParseUnit($postData['moneyNum']));
			$extData = array('app_version' => $postData['app_version'], 'channel' => $postData['channel']);
            $return = $this->newwallet_model->setWithDraw($postData['moneyNum'], $uid, $postData['platform'], $additions, $extData);

            if ($return['status']) 
            {
            	// 组装提现记录页数据
            	$tokenStr = $this->strCode(json_encode(array(
					'uid' => $uid,
					'tradeNo' => $return['data']
				)), 'ENCODE');

                $result = array(
					'status' => TRUE,
					'msg' => '提现申请成功',
					'data' => $this->config->item('pages_url') . 'app/withdraw/withdrawComplete/' . urlencode($tokenStr)
				);
            } 
            else 
            {
                $result = array(
					'status' => FALSE,
					'msg' => $return['msg'],
					'data' => ''
				);
            }
        }
        return $result;
	}

	/*
	 * APP 提现 - 验证手机号码并申请提现
	 * @date:2015-05-15
	 */
	public function validatePhone($uinfo, $data)
	{
		// 金额校验
		if($uinfo['moneyNum'] != $data['moneyNum'])
		{
			$result = array(
				'status' => FALSE,
				'msg' => '金额校验错误',
				'data' => ''
			);
			return $result;
		}

		if(empty($data['captcha']))
		{
			$result = array(
				'status' => FALSE,
				'msg' => '请输入验证码',
				'data' => ''
			);
			return $result;
		}

		// 验证码校验
		$this->primarysession->startSession();
        $codestr = explode(':', $this->primarysession->getArg('app_captcha'));

        if ( (empty($data['captcha']) || $codestr[1] < time() || strtolower($data['captcha']) != $codestr[0])) 
        {
        	// 短信验证码过期机制
			$this->primarysession->setArg('withdrawError', $this->primarysession->getArg('withdrawError') + 1);

			if($this->primarysession->getArg('withdrawError') >= 5)
			{
				// 清除有效验证码
				$this->primarysession->setArg('app_captcha', '');
				// 清除错误次数
				$this->primarysession->setArg('withdrawError', 0); 

				$result = array(
					'status' => FALSE,
					'msg' => '验证码已失效，请重新获取验证码',
					'data' => ''
				);
			}
			else
			{
				$result = array(
					'status' => FALSE,
					'msg' => '验证码不正确',
					'data' => ''
				);
			}
        }
        else
		{
			$additions = '0@'.$data['bank_type'].'@'.$data['card_id'];
			$data['moneyNum'] = intval(ParseUnit($data['moneyNum']));
			$extData = array('app_version' => $data['app_version'], 'channel' => $data['channel']);
            $return = $this->newwallet_model->setWithDraw($data['moneyNum'], $uinfo['uid'], $data['platform'], $additions, $extData);

            if ($return['status']) 
            {
            	// 组装提现记录页数据
            	$tokenStr = $this->strCode(json_encode(array(
					'uid' => $uinfo['uid'],
					'tradeNo' => $return['data']
				)), 'ENCODE');

                $result = array(
					'status' => TRUE,
					'msg' => '提现申请成功',
					'data' => $this->config->item('pages_url') . 'app/withdraw/withdrawComplete/' . urlencode($tokenStr)
				);
            } 
            else 
            {
                $result = array(
					'status' => FALSE,
					'msg' => $return['msg'],
					'data' => ''
				);
            }
		}
		return $result;
	}

	/*
	 * APP 提现 - 验证支付密码
	 * @date:2015-05-15
	 */
	public function validatePayPwd($uinfo, $data)
	{
		// 金额校验
		if($uinfo['moneyNum'] != $data['moneyNum'])
		{
			$result = array(
				'status' => FALSE,
				'msg' => '金额校验错误',
				'data' => ''
			);
			return $result;
		}

		if(empty($data['payPsw']))
		{
			$result = array(
				'status' => FALSE,
				'msg' => '请输入支付密码',
				'data' => ''
			);
			return $result;
		}

		// 支付密码验证
		$info = $this->user_model->getUserInfo($uinfo['uid']);
		
		if(md5($data['payPsw']) != $info['pay_pwd'])
		{
			$result = array(
				'status' => FALSE,
				'msg' => '支付密码不正确',
				'data' => ''
			);
		}
		else
		{
			$additions = '0@'.$data['bank_type'].'@'.$data['card_id'];
			$data['moneyNum'] = intval(ParseUnit($data['moneyNum']));
			$extData = array('app_version' => $data['app_version'], 'channel' => $data['channel']);
            $return = $this->newwallet_model->setWithDraw($data['moneyNum'], $uinfo['uid'], $data['platform'], $additions, $extData);

            if ($return['status']) 
            {
            	// 组装提现记录页数据
            	$tokenStr = $this->strCode(json_encode(array(
					'uid' => $uinfo['uid'],
					'tradeNo' => $return['data']
				)), 'ENCODE');

                $result = array(
					'status' => TRUE,
					'msg' => '提现申请成功',
					'data' => $this->config->item('pages_url') . 'app/withdraw/withdrawComplete/' . urlencode($tokenStr)
				);
            } 
            else 
            {
                $result = array(
					'status' => FALSE,
					'msg' => $return['msg'],
					'data' => ''
				);
            }
		}
		return $result;
	}

	/*
	 * APP 提现 - 申请提交 - step2
	 * @date:2015-05-15
	 */
	public function doWithdraw($token, $bankId)
	{
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);

		if( empty($data['uid']) || empty($bankId) || empty($data['moneyNum']) )
		{
			echo "参数错误！";
			die;
		}	

		// 查询银行卡绑定信息
		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

		if(empty($bankInfo))
		{	
			echo "无绑定银行卡";
			die; 
		}

		$data['bankId'] = $bankId;
		$data['token'] = $this->strCode(json_encode(array(
			'uid' => $data['uid'],
			'moneyNum' => $data['moneyNum']
		)), 'ENCODE');

		$this->load->view('withdraw/doWithdraw', $data);
	}

	/*
	 * APP 提现 - 申请提交 - step2
	 * @date:2015-05-15
	 */
	public function phone($token, $bankId)
	{
		// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);

		if( empty($data['uid']) || empty($bankId) || empty($data['moneyNum']) )
		{
			echo "参数错误！";
			die;
		}	

		// 查询银行卡绑定信息
		$bankInfo = $this->user_model->freshBankInfo($data['uid']);

		if(empty($bankInfo))
		{	
			echo "无绑定银行卡";
			die; 
		}

		// 获取手机号码
		$uinfo = $this->user_model->getUserInfo($data['uid']);
		$data['phone'] = $uinfo['phone'];

		$data['bankId'] = $bankId;
		$data['token'] = $this->strCode(json_encode(array(
			'uid' => $data['uid'],
			'moneyNum' => $data['moneyNum']
		)), 'ENCODE');

		$this->load->view('withdraw/phone', $data);
	}
	
	/*
	 * APP 发送手机验证码
	 * @date:2015-05-15
	 */
	public function sendPhone()
	{
		if(empty($this->uid))
		{
			$result = array(
				'status' => '0',
				'msg' => '用户信息失效',
				'data' => ''
			);
			die(json_encode($result));
		}

		// 获取用户信息
		$uinfo = $this->user_model->getUserInfo($this->uid);

		if(empty($uinfo['phone']))
		{
			$result = array(
				'status' => '0',
				'msg' => '用户信息失效',
				'data' => ''
			);
			die(json_encode($result));
		}

		// 防刷限制
		$this->load->library('limit');
		$userLimit = $this->limit->checkUser('userSms', $this->uid);

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

		// 发送短信验证码
		$sendResult = $this->getSmsCode($uinfo['phone'], 'withdraw', 'app_captcha');
        // $sendResult = true;
        if($sendResult)
        {
            $result = array(
                'status' => '1',
                'msg' => '手机短信已发送',
                'data' => ''
            );
        }
        else
        {
            $result = array(
                'status' => '0',
                'msg' => '手机短信发送失败',
                'data' => ''
            );
        }
        echo json_encode($result); 
	}

	/*
	 * APP 提现 - 是否当日已提交提现申请
	 * @date:2015-05-15
	 */
    private function isTodayWithdraw() 
    {
        $withdrawLog = $this->newwallet_model->getWithdrawLog($this->uid);
        if (!empty($withdrawLog)) {
            return true;
        } else {
            return false;
        }
    }

    /*
 	 * APP 提现成功页
 	 * @date:2015-06-15
 	 */
    public function withdrawComplete($token)
    {
    	// 检查参数
		$data = $this->strCode(urldecode($token));
		$data = json_decode($data, true);
        $this->load->model('wallet_model');
		$order = $this->wallet_model->getWithdrawDetail($data['uid'], $data['tradeNo']);
		$detail = array();

		if(!empty($order))
		{
			$tmpAry = explode('@', $order['additions']);
			$detail = array(
				'money' => ParseUnit($order['money'],1),
				'bank_id' => $tmpAry[2],
				'applyTime' => date("Y年m月d日H:i",(strtotime($order['created']) + 3600*24))
			);
		}
		// var_dump($detail);die;
		$this->load->view('withdraw/result', $detail);

    }
	
}