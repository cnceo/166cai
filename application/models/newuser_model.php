<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Newuser_Model extends MY_Model {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}
	
	//保存绑定信息
	public function saveUserBase($info) {
		$result = array(
			'status' => FALSE,
			'code' => 401,
			'data' => array(
				'msg' => '系统异常'
			)
		);
		$unique = array('id_card');
		foreach ($unique as $field) {
			if(!empty($info[$field])) {
				if($this->user_model->isIdCardRepeat($info['id_card'])) {
					$result = array(
						'status' => FALSE,
						'code' => 402,
						'data' => array(
							'msg' => '单个身份证最多绑定5个帐号。如您不知悉这些帐号，请联系客服查询。'
						)
					);
					return $result;
				}
				// 是否已绑定 id_card
				if($this->user_model->isIdCardBind($info['uid'])) {
					$result = array(
						'status' => FALSE,
						'code' => 403,
						'data' => array(
							'msg' => '您已完成实名认证'
						)
					);
					return $result;
				}
			}
		}
		$upd = array('real_name', 'nick_name', 'nick_name_modify_time', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
		    'bank_city', 'pay_pwd', 'bind_id_card_time', 'birthday');
		if(!empty($info['id_card'])) 
		{
		    $info['bind_id_card_time'] = date('Y-m-d H:i:s');
		    $info['birthday'] = $this->getIDBirthday($info['id_card']);
		}
		
		$fields = array_keys($info);
		$sql = "insert cp_user_info(" . implode(',', $fields) . " ) values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  " )" . $this->onduplicate($fields, $upd);
		$this->db->trans_start();
		$re = $this->db->query($sql, $info);
		if($re) {
			$this->db->trans_complete();
			$this->user_model->freshUserInfo($info['uid']);
			$result = array(
					'status' => true,
					'code' => 400,
					'data' => array(
						'msg' => '绑定成功'
					)
			);
		} else {
			$this->db->trans_rollback();
			$result = array(
					'status' => FALSE,
					'code' => 400,
					'data' => array(
						'msg' => '绑定失败'
					)
			);
		}
		return $result;
	}
	
	/**
	 * 根据身份证id返回生日
	 * @param string $id_card 身份证id
	 * @return string
	 */
	public function getIDBirthday($id_card)
	{
	    $strlen = strlen($id_card);
	    if($strlen == 18)
	    {
	        $month = substr($id_card,10,2);
	        $day = substr($id_card,12,2);
	    }
	    else
	    {
	        $month = substr($id_card,8,2);
	        $day = substr($id_card,10,2);
	    }
	    
	    $result = $month . '-' . $day;
	    if($result == '02-29')
	    {
	        $result = '03-01';
	    }
	    
	    return $result;
	}
	
	public function insertBank($info) {
		//事务开始
		$this->db->trans_start();
		$sqlStop = "select real_name,phone from cp_user_info where uid = ? for update";
		$this->db->query($sqlStop, array($info['uid']))->getRow(); //处理并发加锁操作
		$fields = array_keys($info);
		$sql = "insert cp_user_bank(" . implode(',', $fields) . " ,created) values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  " , now())";
		$res = $this->db->query($sql, $info);
		$id = $this->db->insert_id();
		if(!$res) {
			$result = array(
					'status' => FALSE,
					'code' => 400,
					'data' => array(
						'msg' => '绑定失败'
					)
			);
			$this->db->trans_rollback();
			return $result;
		}
	
		$sql1 = "select bank_id, is_default from cp_user_bank where uid = ? and delect_flag = 0";
		$banks = $this->db->query($sql1, array($info['uid'],$info['bank_id']))->getAll();
		if(count($banks) > 5) {
			$result = array(
					'status' => FALSE,
					'code' => 401,
					'data' => array(
						'msg' => '超过预设上限'
					)
			);
			//事务结束
			$this->db->trans_rollback();
			return $result;
		}
	
		$bCount = 0;
		$isDefault = false;
		foreach ($banks as $value) {
			if($value['bank_id'] == $info['bank_id']) $bCount += 1;
			if($value['is_default'] == '1') $isDefault = true;
		}
		//判断卡是否重复绑定
		if($bCount > 1) {
			$result = array(
					'status' => FALSE,
					'code' => 400,
					'data' => array(
						'msg' => '绑定失败'
					)
			);
			$this->db->trans_rollback();
			return $result;
		}
		//设置默认卡操作
		if(!$isDefault) {
			$sql2 = "update cp_user_bank set is_default=1 where id = ?";
			$res = $this->db->query($sql2, array($id));
			if(!$res) {
				$result = array(
						'status' => FALSE,
						'code' => 400,
						'data' => array(
							'msg' => '绑定失败'
						)
				);
				$this->db->trans_rollback();
				return $result;
			}
		}
		//绑定成功操作
		$this->db->trans_complete();
		$this->user_model->freshBankInfo($info['uid']);
		$result = array(
				'status' => TRUE,
				'code' => 200,
				'data' => array(
					'msg' => '绑定成功'
				)
		);
	
		return $result;
	}
	
	public function updateBank($info) {
		$result = array(
				'status' => FALSE,
				'code' => 401,
				'data' => array(
					'msg' => '系统异常'
				)
		);
		try {
			$this->db->trans_start();
			$sql = "update cp_user_bank set bank_id = ?, bank_type = ?, bank_province = ?, bank_city = ? where uid = ? and id = ?";
			$res = $this->db->query($sql, array($info['bank_id'],$info['bank_type'],$info['bank_province'],$info['bank_city'],$info['uid'],$info['id']));
			if($res) {
				$sql1 = "select count(1) from cp_user_bank where uid = ? and bank_id = ? and delect_flag = 0";
				$count = $this->db->query($sql1, array($info['uid'], $info['bank_id']))->getOne();
				if($count > 1) {
					$result = array(
							'status' => FALSE,
							'code' => 400,
							'data' => array(
								'msg' => '修改银行卡失败'
							)
					);
					$this->db->trans_rollback();
				} else {
					$this->db->trans_complete();
					//刷新缓存
					$this->user_model->freshBankInfo($info['uid']);
					$result = array(
							'status' => TRUE,
							'code' => 200,
							'data' => array(
								'msg' => '修改银行卡成功'
							)
					);
				}
			}else{
				$result = array(
						'status' => FALSE,
						'code' => 400,
						'data' => array(
							'msg' => '修改银行卡失败'
						)
				);
				$this->db->trans_rollback();
			}
		}
		catch (Exception $e) {
			log_message('LOG', "updateBank error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return $result;
		}
		return $result;
	}
	
	public function delBank($info) {
		$result = array(
				'status' => FALSE,
				'code' => 401,
				'data' => array(
					'msg' => '系统异常'
				)
		);
		try {
			$banks = $this->user_model->getBankInfo($info['uid']);
			$bIds = array();
			$dBank = array();
			foreach ($banks as $bank) {
				if($bank['id'] == $info['id']) {
					$dBank = $bank;
					continue;
				}
				$bIds[] = $bank['id'];
			}
			if(empty($dBank)) return $result;
			 
			$this->db->trans_start();
			$res = $this->db->query("update cp_user_bank set delect_flag = 1, is_default = 0 where id = ?", array($info['id']));
			if($res) {
				if ($dBank['is_default'] == 1) {
					if(count($bIds) > 0) {
						sort($bIds);
						$res = $this->db->query("update cp_user_bank set is_default = 1 where id = ?", array($bIds[0]));
						if(!$res) {
							$result = array(
									'status' => FALSE,
									'code' => 400,
									'data' => array(
										'msg' => '删除银行卡失败'
									)
							);
							//事务结束
							$this->db->trans_rollback();
							return $result;
						}
					}
				}
				//刷新缓存
				$this->db->trans_complete();
				$this->user_model->freshBankInfo($info['uid']);
				$result = array(
						'status' => TRUE,
						'code' => 200,
						'data' => array(
							'msg' => '删除银行卡成功'
						)
				);
			} else {
				$result = array(
						'status' => FALSE,
						'code' => 400,
						'data' => array(
							'msg' => '删除银行卡失败'
						)
				);
				//事务结束
				$this->db->trans_rollback();
				return $result;
			}
		} catch (Exception $e) {
			log_message('LOG', "delBank error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return $result;
		}
		return $result;
	}
	
	public function setDefaultBank($info) {
		$result = array(
				'status' => FALSE,
				'code' => 401,
				'data' => array(
					'msg' => '系统异常'
				)
		);
		try {
			//事务开始
			$this->db->trans_start();
			$sql1 = "update cp_user_bank set is_default = 0 where uid = ? and delect_flag = 0";
			$res1 = $this->db->query($sql1, array($info['uid']));
			if(!$res1) {
				$result = array(
						'status' => FALSE,
						'code' => 400,
						'data' => array(
							'msg' => '重置默认卡失败'
						)
				);
				//事务结束
				$this->db->trans_rollback();
				return $result;
			}
			$sql2 = "update cp_user_bank set is_default = 1 where uid = ? and id = ? and delect_flag = 0";
			$res2 = $this->db->query($sql2, array($info['uid'],$info['id']));
			$row = $this->db->affected_rows();
			if($row <= 0) {
				$result = array(
						'status' => FALSE,
						'code' => 400,
						'data' => array(
								'msg' => '重置默认卡失败'
						)
				);
				//事务结束
				$this->db->trans_rollback();
				return $result;
			}
			//刷新缓存
			$this->user_model->freshBankInfo($info['uid']);
			$result = array(
					'status' => TRUE,
					'code' => 401,
					'data' => array(
							'msg' => '更新默认卡成功'
					)
			);
	
			//事务结束
			$this->db->trans_complete();
		} catch (Exception $e) {
			log_message('LOG', "setDefaultBank error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			$this->db->trans_rollback();
			return $result;
		}
		return $result;
	}
	
	public function bindEmail($data) {
		// 事务开始
		$bemail = $this->db->query("select email from cp_user where uid = ?", array($data['uid']))->getOne();
		$this->db->trans_start();
		$sql = "update cp_user u join cp_user_info i on u.uid=i.uid set u.email=?, bind_email_time = NOW(), i.msg_send = (i.msg_send | 2) where u.uid =?";
		$res = $this->db->query($sql, array($data['email'], $data['uid']));
		if($res) {
			$count = $this->db->query("select count(1) from cp_user where email=?", array($data['email']))->getOne();
			if($count == 1) {
				$this->user_model->freshUserInfo($data['uid']);
				$this->db->trans_complete();
				$place = (isset($data['isbck']) && $data['isbck'] == 1) ? 2 : 1;
				if (!empty($bemail) || $place == 2) {
				    $bemail = !empty($bemail) ? $bemail : '';
				    $this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 5, '{$bemail}', ?, ?)", array($data['uid'], $data['email'], $place));
				}
				return true;
			}
		}
		$this->db->trans_rollback();
		return false;
	}
}