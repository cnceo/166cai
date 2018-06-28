<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jjactivity_Model extends MY_Model
{

	public function __construct()
	{
		$this->order_status = $this->orderConfig('orders');
		parent::__construct();
	}

	// 获取活动详情
	public function getActivityDetail($activityId)
	{
		$info = array();
		if(!empty($activityId))
		{
			$sql = "SELECT id, activity_id, lid, playType, startTime, endTime, mark, ctype, params, status FROM cp_activity_jj_config WHERE id = ?";
			$info = $this->db->query($sql, array($activityId))->getRow();
		}
		
		if(empty($info))
		{
			$info = $this->getLastActivityDetail();
		}

		return $info;
	}

	public function getLastActivityDetail()
	{
		$sql = "SELECT id, activity_id, lid, playType, startTime, endTime, mark, ctype, params, status FROM cp_activity_jj_config ORDER BY id DESC LIMIT 1";
			$info = $this->db->query($sql)->getRow();
			return $info;
	}

	// 更新活动状态
	public function closeJjActivity()
	{
		$sql = "UPDATE cp_activity_jj_config SET status = 1 WHERE endTime < DATE_SUB(NOW(), INTERVAL 30 MINUTE) AND status = 0";
		return $this->db->query($sql);
	}

	// 获取所有未结束的活动
	public function getJjActivityInfo()
	{
		$sql = "SELECT id, activity_id, lid, playType, startTime, endTime, mark, ctype, params, buyPlatform FROM cp_activity_jj_config WHERE status = 0";
		return $this->db->query($sql)->getAll();
	}

	// 检查订单是否满足活动
	public function checkOrderCodes($activityInfo, $orderInfo)
	{
		$result = FALSE;

		// 玩法类型
		$playType = array(
			'0' => '1*1',
			'1' => '2*1'
		);

		// 加奖平台
		$platformArr = explode(',', $activityInfo['buyPlatform']);

		// 彩种及加奖平台检查
		if($activityInfo['lid'] == $orderInfo['lid'] && in_array($orderInfo['buyPlatform'], $platformArr))
		{
			// 投注串检查
			if(!empty($orderInfo['codes']))
			{
				$strcodes = explode(';', $orderInfo['codes']);
				
				foreach ($strcodes as $strcode)
				{
					$codesArry = explode('|', $strcode);
					$inum = count($codesArry);
					if(trim($codesArry[$inum - 1] == $playType[$activityInfo['playType']]))
					{
						$result = TRUE;
					}
					else 
					{
						$result = FALSE;
						break;
					}
				}
			}
		}
		return $result;
	}

	// 记录活动
	public function recordActivity($recordData)
	{
		$res = $this->db->query("insert ignore cp_activity_jj_order(". implode(',', array_keys($recordData)) .', created)
		values('. implode(',', array_map(array($this, 'maps'), $recordData)) .', now())', $recordData); 

		$lastId = $this->db->affected_rows();
		return $lastId;
	}

	// 处理订单加奖
	public function dealJjOrder($orderInfo)
	{
		$result = FALSE;

		if(!empty($orderInfo))
		{
			if($orderInfo['status'] == $this->order_status['notwin'])
			{
				$result = TRUE;
			}
			elseif($orderInfo['status'] == $this->order_status['concel'])
			{
				$result = TRUE;
			}
			elseif($orderInfo['status'] == $this->order_status['win'] && in_array($orderInfo['my_status'], array('1', '3', '5')))
			{
				$addparams = array();

				// 查询活动配置
				$configData = $this->getActivityConfig($orderInfo['orderId']);
				
				if(!empty($configData))
				{
					// 活动时间
					if($orderInfo['created'] >= $configData['startTime'] && $orderInfo['created'] <= $configData['endTime'])
					{
						$configParams = unserialize($configData['params']);
						if(!empty($configParams))
						{
							foreach ($configParams as $params)
							{
								// 检查是否包含 *
								if($params['max'] == '*' && $orderInfo['margin'] > $params['min'])
								{
									$addparams = $params;
									break;
								}
								elseif($orderInfo['margin'] > $params['min'] && $orderInfo['margin'] <= $params['max'])
								{
									$addparams = $params;
									break;
								}
							}
						}

						// 符合加奖
						if(!empty($addparams))
						{
							$addData = array(
								'uid' 		=> $orderInfo['uid'],
								'orderId'	=> $orderInfo['orderId'],
								'money'		=> $addparams['val'],
								'mark'		=> $configData['mark']
							);
							$dipathRes = $this->doDispath($addData);

							// 同步至活动订单
			        		$orderArry = array(
			        			'orderId' 	=> $orderInfo['orderId'],
			        			'add_money' => $addparams['val']
			        		);

			        		$updateRes = $this->updateJoinInfo($orderArry);

			        		if($dipathRes && $updateRes)
			        		{
			        			$result = TRUE;
			        		}
						}
						else
						{
							// 不符合加奖条件，状态置为已处理
							$result = TRUE;
						}			
					}
				}			
			}
		}
		// log_message('LOG', "返回参数: " . json_encode($orderInfo), 'dealJjOrder');
		// log_message('LOG', "处理结果: " . $result, 'dealJjOrder');
		return $result;
	}

	// 获取活动配置
	public function getActivityConfig($orderId)
	{
		$sql = "SELECT o.orderId, c.lid, c.playType, c.startTime, c.endTime, c.mark, c.ctype, c.params FROM cp_activity_jj_order AS o LEFT JOIN cp_activity_jj_config AS c ON o.jj_id = c.id WHERE o.orderId = ? AND o.add_money = 0";
		return $this->db->query($sql, array($orderId))->getRow();
	}

	// 加奖操作
	public function doDispath($addData, $tranc = FALSE)
	{
		if($tranc)
    	{
    		// 事务开始
    		$this->db->trans_start();
    	}

    	$userInfo = $this->getUserMoney($addData['uid']);

    	$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

    	// 彩金派送流水
    	$wallet_log = array(
            'uid'       => $addData['uid'],
            'money'     => $addData['money'],
            'ctype'     => 9,
            'trade_no'  => $trade_no,
            'umoney'    => ($userInfo['money'] + $addData['money']),
            'must_cost' => 0,
            'dispatch'  => 0,
            'mark'      => '1',
            'orderId'   => $addData['orderId'],
            'content'   => $addData['mark'],
        );

        $res1 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

		// 用户余额扣款
        $res2 = $this->db->query("update cp_user set money = money + {$addData['money']}, dispatch = dispatch + {$addData['money']} where uid = ?", array($addData['uid']));

        // 总账记录流水
        $this->load->model('capital_model');
        $res3 = $this->capital_model->recordCapitalLog('2', $trade_no, 'plus_awards', $addData['money'], '2', $tranc = FALSE);

        if($res1 && $res2 && $res3)
        {
        	if($tranc)
        	{
        		$this->db->trans_complete();
        	}
        	// 刷新钱包缓存
        	$this->load->model('wallet_model');
        	$this->wallet_model->freshWallet($addData['uid']);
        	$pay_status = 1;
        }
        else
        {
        	if($tranc)
        	{
        		$this->db->trans_rollback();
        	}
        	$pay_status = 0;
        }

        return $pay_status;
	}

	// 加奖操作 - 查询用户信息
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}

	// 同步加奖
	public function updateJoinInfo($orderInfo)
	{
		return $this->db->query("update cp_activity_jj_order set add_money = ? where orderId = ?", array($orderInfo['add_money'], $orderInfo['orderId']));
	}

	public function getOrder()
	{
		$sql = "select uid, orderId, lid, status, activity_ids, activity_status, money, failMoney, 
    	(money-failMoney) as calMoney, issue, userName, bonus, margin, created from cp_orders where orderId = ?";
    	return $this->db->query($sql, array('20160531091839958064'))->getRow();
	}
}
