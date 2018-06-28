<?php
class BindEmail {
	
	private $CI;
	
	public function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->model('newuser_model');
		$this->CI->load->model('user_model');
	}
	
	public function appbindEmail($data, $uinfo) {
		switch ($data['ctype']) {
			// 发送验证码
			case '1':
				$result = $this->sendEmail($data['email'], $uinfo);
				break;
				// 检查验证码
			case '2':
				$result = $this->checkEmailCaptcha($data['email'], $data['captcha']);
				break;
				// 绑定修改邮箱
			case '3':
				$result = $this->bindEmail($data);
				break;
			default:
				$result = array(
					'status' => '0',
					'msg' => '请求参数错误',
					'data' => ''
				);
				break;
		}
		return $result;
	}
	
	/*
	 * 发送邮箱验证码
	* @date:2016-09-05
	*/
	private function sendEmail($email, $uinfo)
	{
		// 格式检查
		if(!$this->checkEmailFormat($email)) {
			$result = array(
					'status' => '0',
					'msg' => '邮箱格式错误',
					'data' => ''
			);
			return $result;
		}
	
		// 检查邮箱是否被使用
		if(empty($uinfo['email']) || (!empty($uinfo['email']) && $uinfo['email'] != $email)) {
			if($this->CI->user_model->checkEmailBind($email)) {
				$result = array(
						'status' => '0',
						'msg' => '此邮箱已被使用',
						'data' => ''
				);
				return $result;
			}
		}
		$this->CI->load->library('primarySession');
		$sessionCode = $this->CI->primarysession->getArg('emailCaptcha');
	
		if(!empty($sessionCode)) {
			$codestr = explode(':', $sessionCode);
			if(($email == $codestr[3]) && (time() < $codestr[2])) {
				$result = array(
						'status' => '0',
						'msg' => '操作太频繁，请稍后重试',
						'data' => ''
				);
				return $result;
			}
		}
	
		$code = $this->getVerificationCode();
		if (empty($uinfo['email'])) {
			$message = '<style>body {background: #fbf7eb;}</style><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"><tr>
    			<td style="padding: 38px 10px; background: #fbf7eb;"><table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background: #eee;"><tr><td>
                <img src="'.getStaticFile('/caipiaoimg/v1.1/img/logo-email.png').'" width="280" height="68" alt="166彩票为生活添彩" border="0"></td></tr>
                </table></td></tr><tr><td style="margin:0 auto; padding: 30px 30px 0; background: #fff; font-family:\'Microsoft YaHei\';">
                <table width="650" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td style="font-size:14px; color: #666;">
                <tr><td style="font-size:16px; color: #666;">尊敬的166彩票网用户'.$this->uinfo['uname'].'，您好！</td></tr><tr>
                <td style="padding-top: 10px; line-height: 1.6; font-size: 16px; font-family:\'Microsoft YaHei\'; color: #333;">
                <b style="padding-left: 32px; font-weight: bold;">感谢您使用166彩票，您的邮箱验证码为<span style="color: #e60000">'.$code.'</span>，</b>请及时输入验证码并完成邮箱绑定，完成后即可享受出票通知服务！
                </td></tr><tr><td style="padding: 30px 0 40px; font-size: 12px; color: #e50100;">温馨提示：如非您本人操作，请忽略邮件中内容！</td></tr><tr>
                <td style="border-top: 5px solid #f5f4ef;"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                <td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';color: #999;">此邮件为系统自动发送，请勿直接回复<br>
                	如对以上内容有所疑问，欢迎前往<a href="' . $this->CI->config->item('protocol') . $this->CI->config->item('pages_url') . '" target="_blank" style="color: #3e8be7;">166彩票</a>联系在线客服<br>客服热线：400-690-6760<br>
                	查看《<a href="' . $this->CI->config->item('protocol') . $this->CI->config->item('pages_url') . 'activity/fwcn" target="_blank" style="color: #3e8be7;">166彩票用户服务承诺</a>》<br>更多优惠活动可扫描右侧二维码下载客户端查看</td>
                <td align="center" style="padding:20px 0;"><img src="'.getStaticFile('/caipiaoimg/v1.1/img/qrcode.png').'" alt="" border="0"></td></tr></table>
                </td></tr></table></td></tr></table></td></tr></table>';
		}else {
			$message = '<style>body {background: #fbf7eb;}</style><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center"><tr>
    			<td style="padding: 38px 10px; background: #fbf7eb;"><table width="710" border="0" cellpadding="0" cellspacing="0" align="center">
                <tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="background: #eee;"><tr><td>
                <img src="'.getStaticFile('/caipiaoimg/v1.1/img/logo-email.png').'" width="280" height="68" alt="166彩票为生活添彩" border="0"></td></tr>
                </table></td></tr><tr><td style="margin:0 auto; padding: 30px 30px 0; background: #fff; font-family:\'Microsoft YaHei\';">
                <table width="650" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td style="font-size:14px; color: #666;">
                <tr><td style="font-size:16px; color: #666;">尊敬的166彩票网用户'.$this->uinfo['uname'].'，您好！</td></tr><tr>
                <td style="padding-top: 10px; line-height: 1.6; font-size: 16px; font-family:\'Microsoft YaHei\'; color: #333;">
                <b style="padding-left: 32px; font-weight: bold;">感谢您使用166彩票，您的邮箱验证码为<span style="color: #e60000">'.$code.'</span>，</b>请及时输入验证码并完成邮箱修改，完成后即可使用新邮箱享受出票通知服务！
                </td></tr><tr><td style="padding: 30px 0 40px; font-size: 12px; color: #e50100;">温馨提示：如非您本人操作，请忽略邮件中内容！</td></tr><tr>
                <td style="border-top: 5px solid #f5f4ef;"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
                <td style="line-height:1.8;font-size:12px;font-family:\'Microsoft YaHei\';color: #999;">此邮件为系统自动发送，请勿直接回复<br>
                	如对以上内容有所疑问，欢迎前往<a href="' . $this->CI->config->item('protocol') . $this->CI->config->item('pages_url') . '" target="_blank" style="color: #3e8be7;">166彩票</a>联系在线客服<br>客服热线：400-690-6760<br>
                	查看《<a href="' . $this->CI->config->item('protocol') . $this->CI->config->item('pages_url') . 'activity/fwcn" target="_blank" style="color: #3e8be7;">166彩票用户服务承诺</a>》<br>更多优惠活动可扫描右侧二维码下载客户端查看</td>
                <td align="center" style="padding:20px 0;"><img src="'.getStaticFile('/caipiaoimg/v1.1/img/qrcode.png').'" alt="" border="0"></td></tr></table>
                </td></tr></table></td></tr></table></td></tr></table>';
		}
	
		$sendData = array(
				'to'	  => $email,
				'subject' => '邮箱绑定通知',
				'message' => $message,
		);
	
		//$result = $this->tools->sendMail($sendData);
		//修改成阿里云
		$this->CI->load->library('tools');
		$result = $this->CI->tools->sendMail($sendData,array(),1);
	
		if($result) {
			$out_time = $this->CI->config->item('OUTTIME');
			$time = time();
			$expire = $time + 60 * $out_time['captche'];
			$second = $time + 60;
			$codestr = "{$code}:$expire:$second:$email";
			$this->CI->primarysession->setArg('emailCaptcha', $codestr);
			$sessionCode = $this->CI->primarysession->getArg('emailCaptcha');
	
			$result = array(
					'status' => '1',
					'msg' => '发送成功',
					'data' => ''
			);
		} else {
			$result = array(
					'status' => '0',
					'msg' => '发送失败',
					'data' => ''
			);
		}
	
		return $result;
	}
	
	private function checkEmailCaptcha($email, $captcha) {
		$result = array(
				'status' => '0',
				'msg' => '验证码错误',
				'data' => ''
		);
	
		$code = $this->CI->primarysession->getArg('emailCaptcha');
	
		$codestr = explode(':', $code);
		if (($codestr[1] > time()) && (strtoupper($captcha) == $codestr[0]) && ($email === $codestr[3])) {
			$this->CI->primarysession->setArg('emailCaptcha', '');
			$result = array(
					'status' => '1',
					'msg' => '验证码正确',
					'data' => ''
			);
		}
		 
		return $result;
	}
	
	/*
	 * 绑定邮箱
	* @date:2016-09-05
	*/
	private function bindEmail($data) {
		// 格式检查
		if(!$this->checkEmailFormat($data['email'])) {
			$result = array(
					'status' => '0',
					'msg' => '邮箱格式错误',
					'data' => ''
			);
			return $result;
		}
	
		// 验证码检查
		$checkRes = $this->checkEmailCaptcha($data['email'], $data['captcha']);
	
		if(!$checkRes['status']) {
			$result = array(
					'status' => '0',
					'msg' => '验证码错误',
					'data' => ''
			);
			return $result;
		}
	
		if($this->CI->newuser_model->bindEmail( array('uid' => $data['uid'], 'email' => $data['email']))) {
			$result = array(
					'status' => '1',
					'msg' => '设置成功',
					'data' => ''
			);
		} else {
			$result = array(
					'status' => '0',
					'msg' => '设置失败',
					'data' => ''
			);
		}
		return $result;
	}
	
	/*
	 * 邮箱格式检查
	* @date:2016-09-05
	*/
	public function checkEmailFormat($email) {
		if(preg_match('/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/u', $email)) return TRUE;
		return FALSE;
	}
	
	/*
	 * 生成邮箱验证码
	* @date:2016-09-05
	*/
	public function getVerificationCode() {
		$codes = array();
		$str="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for($i = 0; $i < 4; $i++) {
			$codes[] = $str[mt_rand(0, 25)];
		}
		$str = "0123456789";
    	for($i = 0; $i < 2; $i++) {
			$codes[] = $str[mt_rand(0, 9)];
		}
		 
		shuffle($codes);
		return implode('', $codes);
	}
}