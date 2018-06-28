<?php
class BindIdCard {
	
	private $CI;
	
	public function __construct() {
		$this->redpackmethod = 'hongbao166';
		$this->CI = &get_instance();
		$this->CI->load->library('IdCard');
		$this->CI->load->library('libredpack');
		$this->CI->load->model('newuser_model');
		$this->CI->load->model('user_model');
	}
	
	/**
	 * 客户端、M版实名认证逻辑
	 * @param array $data
	 * @return array $result
	 */
	public function appsetIdCardInfo($data) {
		// 检查唯一性
		if ($this->CI->user_model->isIdCardRepeat($data['id_card'])) {
			return array(
					'status' => '0',
					'msg' => '单个身份证最多绑定5个帐号。如您不知悉这些帐号，请联系客服查询。',
					'data' => ''
			);
		}
		
		// 判断身份证合法性, 判断希望年龄
		$idInfo = IdCard::checkIdCard($data['id_card']);
		if ($idInfo !== false) {
			if (!IdCard::isEnoughAgeByIdCard($idInfo['idcard'], 18)) {
				return array(
						'status' => '0',
						'msg' => '身份证未满18周岁',
						'data' => ''
				);
			} else {
				$uinfo = $this->CI->user_model->getUserInfo($data['uid']);
				if ($activityId = $this->CI->libredpack->{$this->redpackmethod}('hasAttend', array('phone' => $uinfo['phone']))) {
					// 红包功能 完善信息红包
					if(!empty($data['id_card'])) {
						$checkBoundRes = $this->CI->libredpack->{$this->redpackmethod}('checkBound', array('uid' => $data['uid'], 'id_card' => $data['id_card']));
						if ($checkBoundRes[0]) {
						    if ($activityId == 1) $this->CI->libredpack->hongbao188('activatePack', array('uid' => $data['uid'], 'activityId' => $activityId));
							else $this->CI->libredpack->{$this->redpackmethod}('activatePack', array('uid' => $data['uid'], 'activityId' => $activityId));
						} else {
							if(isset($data['ignoreRedpack']) &&  $data['ignoreRedpack'] == '1') {
								$this->CI->libredpack->{$this->redpackmethod}('deleteOwnPack', array('uid' => $data['uid'], 'activityId' => $activityId));
							} else {// 返回提示
								return array(
										'status' => '-102', 
										'msg' => '该身份证已参加红包活动', 
										'data' => ''
								);
							}
						}
					}
				}
				$this->CI->libredpack->hongbaoworldcup('bindcard', array('phone' => $uinfo['phone'], 'uid' => $data['uid'], 'id_card' => $data['id_card']));
				// 拉新活动
				$this->CI->load->model('activity_lx_model');
				$this->CI->activity_lx_model->idcardAdd($data['uid'], $data['id_card']);
		
				unset($data['ignoreRedpack']);
				unset($data['auth']);
		
				$userData = array(
						'uid'		=>	$data['uid'],
						'id_card'	=>	$data['id_card'],
						'real_name'	=>	$data['real_name'],
				);
		
				$dbRes = $this->CI->newuser_model->saveUserBase($userData);
		
				if($dbRes['status']) {
					return array(
							'status' => '1',
							'msg' => '身份证绑定成功',
							'data' => ''
					);
				} else {
					return array(
							'status' => '0',
							'msg' => '身份证绑定失败',
							'data' => ''
					);
				}
			}
		} else {
			return array(
					'status' => '0',
					'msg' => '身份证格式错误',
					'data' => ''
			);
		}
	}
	
	/**
	 * PC实名认证逻辑
	 * @param array $data
	 */
	public function pcsetIdCardInfo($data, $newData = array()) {
		if ($this->CI->user_model->isIdCardRepeat($data['id_card'])) {
			die('2'); // 身份证最多绑定5个账号
		} else {
			// 判断身份证合法性, 判断希望年龄
			$idInfo = IdCard::checkIdCard($data['id_card']);
			if ($idInfo === false) die('3'); // 身份证格式错误
			if(!IdCard::isEnoughAgeByIdCard($data['id_card'], 18)) die('4'); //身份证未满18周岁
			$newData['real_name'] = $data['real_name'];
			$newData['id_card'] = $data['id_card'];
			$result = $this->CI->newuser_model->saveUserBase($newData);
			if(!$result['status']) die('2');
			//红包操作
			$this->CI->libredpack->{$this->redpackmethod}('bindcard', array('phone' => $newData['phone'], 'uid' => $newData['uid'], 'id_card' => $data['id_card']));
			$this->CI->libredpack->hongbaoworldcup('bindcard', array('phone' => $newData['phone'], 'uid' => $newData['uid'], 'id_card' => $data['id_card']));
			//拉新活动
			$this->CI->load->model('activity_lx_model');
			$this->CI->activity_lx_model->idcardAdd($newData['uid'], $data['id_card']);
		}
	}
}