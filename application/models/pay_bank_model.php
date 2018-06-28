<?php
class Pay_Bank_Model extends MY_Model {

	// 协议支付标识位
	public $cstateType = array(
		'umpay'		=>	1,
	);
	
	public function getBankList($uid) {
		$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$info = unserialize($this->cache->hGet ( $REDIS ['PAY_BANK_INFO'], $uid ));
		if(empty($info))
		{
			$info = $this->freshPayBankInfo($uid);
		}
		return $info;
	}
	
	public function freshPayBankInfo($uid) {
		$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$result = $this->db->query("select id, bank_id, bank_type, is_default, pay_agreement from cp_pay_bank where uid = ? and delect_flag = 0", array($uid))->getAll();
		$this->cache->hSet($REDIS['PAY_BANK_INFO'], $uid, serialize($result));
		return $result; 
	}
	
	public function setDefault($bankid, $uid) {
		//事务开始
		
		$this->db->trans_start();
		$this->getUserLock($uid);
		$this->db->query("update cp_pay_bank set is_default = 0 where uid = ?", array($uid));
		$this->db->query("update cp_pay_bank set is_default = 1 where uid = ? and id = ? and delect_flag = 0", array($uid, $bankid));
		if(!$this->db->affected_rows())
		{
			$this->db->trans_rollback();
			return false;
		}
		//刷新缓存
		$this->freshPayBankInfo($uid);

		//事务结束
		$this->db->trans_complete();
		return true;
	}
		
	public function getCardById($id) 
	{
		return $this->slave->query("select bank_id, bank_type from cp_pay_bank where id = ? and delect_flag = 0", array($id))->getRow();
	}
	
	public function getDefaultByUid($uid)
	{
		return $this->slave->query("select bank_id, pay_agreement from cp_pay_bank where uid = ? and delect_flag = 0 and is_default = 1", array($uid))->getRow();
	}

	// 行锁
	public function getUserLock($uid)
	{
		$sql = "SELECT real_name, phone FROM cp_user_info WHERE uid = ? for update";
		$this->db->query($sql, array($uid))->getRow();
	}

	// 查询用户银行卡
	public function getUserPayBanks($uid)
	{
		$sql = "SELECT uid, bank_id, bank_type, pay_agreement, is_default, cstate, delect_flag, created, modified FROM cp_pay_bank WHERE uid = ?";
		return $this->db->query($sql, array($uid))->getAll();
	}

	// 查询用户指定银行卡
	public function getUserBankInfo($uid, $bank_id)
	{
		$sql = "SELECT uid, bank_id, bank_type, pay_agreement, is_default, created, modified FROM cp_pay_bank WHERE uid = ? AND bank_id = ?";
		return $this->db->query($sql, array($uid, $bank_id))->getRow();
	}

	// 关联银行卡及支付协议
	public function savePayBankInfo($info, $trans = true)
	{
		if($trans)
		{
			// 事务开始
			$this->db->trans_start();

			$this->getUserLock($info['uid']);
		}

		// 查询
		$bankInfo = $this->getUserPayBanks($info['uid']);

		$bank = array();
		$default = array();
		if(!empty($bankInfo) && !empty($info['bank_id']))
		{
			// 检查是否已绑定
			foreach ($bankInfo as $key => $items) 
			{
				// 是否存在可用的默认银行卡
				if($items['is_default'] == 1 && $items['delect_flag'] == 0)
				{
					$default = $items;
				}
				if($items['bank_id'] != $info['bank_id'])
				{
					continue;
				}
				$bank = $items;
			}

			// 是否设置默认卡
			$cons = '';
			if(empty($default))
			{
				$cons .= ', is_default = 1';
			}

			if(!empty($bank))
			{
				if(!empty($info['bank_type']) && !empty($info['pay_agreement_id']))
				{
					// 更新
					if(!empty($bank['pay_agreement']))
					{
						$pay_agreement = json_decode($bank['pay_agreement'], true);
					}
					else
					{
						$pay_agreement = array();
					}
					$pay_agreement[$info['library']] = $info['pay_agreement_id'];

					$bankData = array(
						'bank_type'			=>	$info['bank_type'],
						'pay_agreement'		=>	json_encode($pay_agreement),
					);

					$res = $this->db->query("UPDATE cp_pay_bank SET bank_type = ?, pay_agreement = ?, delect_flag = 0{$cons} WHERE uid = ? AND bank_id = ?", array($bankData['bank_type'], $bankData['pay_agreement'], $info['uid'], $info['bank_id']));

					// 更新签约信息
					if($this->cstateType[$info['library']])
					{
						$cstate = $this->cstateType[$info['library']];
						$this->db->query("UPDATE cp_pay_bank SET cstate = cstate | ? WHERE uid = ? AND bank_id = ?", array($cstate, $info['uid'], $info['bank_id']));
					}	
				}
				else
				{
					$res = $this->db->query("UPDATE cp_pay_bank SET delect_flag = 0{$cons} WHERE uid = ? AND bank_id = ?", array($info['uid'], $info['bank_id']));
				}
			}
			else
			{
				if($info['pay_agreement_id'])
				{
					$pay_agreement = array();
					$pay_agreement[$info['library']] = $info['pay_agreement_id'];
				}
				
				// 添加
				$bankData = array(
					'uid'			=>	$info['uid'],
					'bank_id'		=>	$info['bank_id'],
					'is_default'	=>	empty($default) ? 1 : 0,
					'bank_type'		=>	$info['bank_type'] ? $info['bank_type'] : '',
					'pay_agreement'	=>	$info['pay_agreement_id'] ? json_encode($pay_agreement) : '',
				);
				$res = $this->recodeBankInfo($bankData);

				if($this->cstateType[$info['library']])
				{
					$this->db->query("UPDATE cp_pay_bank SET cstate = cstate | ? WHERE uid = ? AND bank_id = ?", array($this->cstateType[$info['library']], $info['uid'], $info['bank_id']));
				}
			}
		}
		else
		{
			if($info['pay_agreement_id'])
			{
				$pay_agreement = array();
				$pay_agreement[$info['library']] = $info['pay_agreement_id'];
			}

			// 添加，设置为默认
			$bankData = array(
				'uid'			=>	$info['uid'],
				'bank_id'		=>	$info['bank_id'],
				'bank_type'		=>	$info['bank_type'] ? $info['bank_type'] : '',
				'pay_agreement'	=>	$info['pay_agreement_id'] ? json_encode($pay_agreement) : '',
				'is_default'	=>	1,
			);
			$res = $this->recodeBankInfo($bankData);

			if($this->cstateType[$info['library']] && $info['pay_agreement_id'])
			{
				$this->db->query("UPDATE cp_pay_bank SET cstate = cstate | ? WHERE uid = ? AND bank_id = ?", array($this->cstateType[$info['library']], $info['uid'], $info['bank_id']));
			}
		}

		if($res)
		{
			if($trans) $this->db->trans_complete();
			$this->freshPayBankInfo($info['uid']);
		}
		else
		{
			if($trans) $this->db->trans_rollback();
		}

	}

	// 记录前置银行卡信息
	public function recodeBankInfo($info)
	{
		$fields = array_keys($info);
		$sql = "insert cp_pay_bank(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
		return $this->db->query($sql, $info);
	}

	// 解绑银行卡 前台解绑 delect_flag 不解绑 cstate
	public function deleteBankInfo($info, $trans = true)
	{

		$result = array(
			'status'	=> 	false,
			'msg'		=>	'此银行卡信息错误'
		);

		if($trans)
		{
			// 事务开始
			$this->db->trans_start();

			$this->getUserLock($info['uid']);
		}

		// 查询
		$bankInfo = $this->getUserPayBanks($info['uid']);

		if(empty($bankInfo))
		{
			if($trans) $this->db->trans_rollback();
			return $result;
		}

		$bank = array();
		$lastBank = array();
		// 检查是否已绑定
		foreach ($bankInfo as $key => $items) 
		{
			if($items['bank_id'] != $info['bank_id'])
			{
				// 未删除
				if(empty($lastBank) && $items['delect_flag'] == 0)
				{
					$lastBank = $items;
				}
				else
				{
					if($lastBank['modified'] < $items['modified'] && $items['delect_flag'] == 0)
					{
						$lastBank = $items;
					}
				}		
				continue;
			}
			$bank = $items;
		}

		if(!empty($bank))
		{
			// 当前为默认
			if($bank['is_default'] == '1' && !empty($lastBank))
			{
				$this->db->query("UPDATE cp_pay_bank SET is_default = 1 WHERE uid = ? AND bank_id = ? AND delect_flag = 0", array($info['uid'], $lastBank['bank_id']));
				$res1 = $this->db->affected_rows();
			}
			else
			{
				$res1 = true;
			}

			// 删除当前银行卡
			$this->db->query("UPDATE cp_pay_bank SET is_default = 0, delect_flag = 1 WHERE uid = ? AND bank_id = ? AND delect_flag = 0", array($info['uid'], $bank['bank_id']));
			$res2 = $this->db->affected_rows();
			
			if($res1 && $res2)
			{
				if($trans) $this->db->trans_complete();
				$this->freshPayBankInfo($info['uid']);

				$result = array(
					'status'	=> 	true,
					'msg'		=>	'解约成功'
				);
			}
			else
			{
				if($trans) $this->db->trans_rollback();
				$result = array(
					'status'	=> 	false,
					'msg'		=>	'此银行卡解约失败'
				);
			}
		}
		else
		{
			if($trans) $this->db->trans_rollback();	
		}
		return $result;
	}

	// 异步通知解绑银行卡
	public function breakBankByNotify($info, $trans = true)
	{
		log_message('LOG', "请求参数: " . json_encode($info), 'breakBankByNotify');
		$params = array(
			'uid'				=>	$info['uid'],
			'pay_agreement_id'	=>	$info['pay_agreement_id'],
			'last_four_cardid'	=>	$info['last_four_cardid'],
			'library'			=>	strtolower($info['library'])
		);

		$result = array(
			'status'	=> 	false,
			'msg'		=>	'此银行卡信息错误'
		);

		if($trans)
		{
			// 事务开始
			$this->db->trans_start();

			$this->getUserLock($params['uid']);
		}

		// 查询
		$bankInfo = $this->getUserPayBanks($params['uid']);

		if(empty($bankInfo))
		{
			if($trans) $this->db->trans_rollback();
			return $result;
		}
	
		// 检查协议号是否已绑定
		$bank = array();
		$lastBank = array();
		foreach ($bankInfo as $key => $items) 
		{
			$match = false;
			$agreement = array();
			if(!empty($items['pay_agreement']))
			{
				$pay_agreement = json_decode($items['pay_agreement'], true);
				// 存在协议号信息
				if(isset($pay_agreement[$params['library']]) && $pay_agreement[$params['library']] == $params['pay_agreement_id'])
				{
					unset($pay_agreement[$params['library']]);

					$agreement = $pay_agreement;

					$items['pay_agreement'] = !empty($agreement) ? json_encode($agreement) : '';

					// 更新
					$bank = $items;
					$match = true;
				}
			}

			if(!$match && $items['delect_flag'] == 0)
			{
				if(empty($lastBank))
				{
					$lastBank = $items;
				}
				else
				{
					if($lastBank['modified'] < $items['modified'])
					{
						$lastBank = $items;
					}
				}
			}
		}

		if(!empty($bank))
		{
			// 当前为默认
			if($bank['is_default'] == '1' && !empty($lastBank))
			{
				$this->db->query("UPDATE cp_pay_bank SET is_default = 1 WHERE uid = ? AND bank_id = ? AND delect_flag = 0", array($info['uid'], $lastBank['bank_id']));
				$res1 = $this->db->affected_rows();
			}
			else
			{
				$res1 = true;
			}

			// 删除当前银行卡
			$res2 = $this->db->query("UPDATE cp_pay_bank SET is_default = 0, delect_flag = 1 WHERE uid = ? AND bank_id = ?", array($info['uid'], $bank['bank_id']));

			if($this->cstateType[$params['library']])
			{
				$cstate = $this->cstateType[$params['library']];
				// 设置cstate解绑
				$this->db->query("UPDATE cp_pay_bank SET cstate = cstate ^ {$cstate} WHERE uid = ? AND bank_id = ? AND (cstate & {$cstate}) =  {$cstate}", array($info['uid'], $bank['bank_id']));
			}

			if($res1 && $res2)
			{
				if($trans) $this->db->trans_complete();
				$this->freshPayBankInfo($info['uid']);

				$result = array(
					'status'	=> 	true,
					'msg'		=>	'解约成功'
				);
			}
			else
			{
				if($trans) $this->db->trans_rollback();
				$result = array(
					'status'	=> 	false,
					'msg'		=>	'此银行卡解约失败'
				);
			}
		}
		else
		{
			if($trans) $this->db->trans_rollback();	
		}
		return $result;
	}
}
