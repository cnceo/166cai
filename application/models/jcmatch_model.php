<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jcmatch_Model extends MY_Model
{
	//订单最大金额
	private $moneyMax = array(
		'11' => array('name' => '20万', 'money' => 200000),
		'19' => array('name' => '20万', 'money' => 200000),
		'33' => array('name' => '20万', 'money' => 200000),
		'35' => array('name' => '2万', 'money' => 20000),
		'42' => array('name' => '20万', 'money' => 200000),
		'43' => array('name' => '20万', 'money' => 200000),
		'51' => array('name' => '2万', 'money' => 20000),
		'52' => array('name' => '20万', 'money' => 200000),
		'10022' => array('name' => '2万', 'money' => 20000),
		'21406' => array('name' => '20万', 'money' => 200000),
		'23528' => array('name' => '2万', 'money' => 20000),
		'23529' => array('name' => '2万', 'money' => 20000),
	);

	public function __construct()
	{
		$this->order_status = $this->orderConfig('orders');
		parent::__construct();
	}

	/*
 	 * 获取最新的活动信息
 	 */
	public function getActivityDetail($activity_id = '3', $activity_issue = null, $id_card = null)
	{
		if (empty($activity_issue)) {
			$sql = "select activity_issue from cp_activity_jc_config where startTime <= NOW() order by id desc limit 1";
			$activity_issue = $this->db->query($sql)->getCol();
			$activity_issue = $activity_issue[0];
		}
		$arr = array($activity_id, $activity_issue);
		$sql = "SELECT c.activity_id, c.activity_issue, c.mid, c.plan, c.lid, c.playType, c.startTime, c.pay_money, 
		c.left_money, c.join_num, c.status, c.pay_status, c.delete_flag, c.created, j.bonus, j.userName, j.money
		FROM
		cp_activity_jc_config as c
		LEFT JOIN cp_activity_jc_join as j ON c.activity_id=j.activity_id AND c.activity_issue=j.activity_issue
		WHERE c.activity_id = ?  AND c.activity_issue = ? and c.startTime <= NOW()";
		if ($id_card)
		{
			$sql .= " AND j.id_card = ?";
			$arr[] = $id_card;
		}
		$sql .= " AND c.delete_flag = 0
				order by j.id desc";
		$info = $this->db->query($sql, $arr)->getAll();

		return $info;
	}

	/*
 	 * 获取详情
 	 */
	public function getJoinDetail($activity_id = '3', $id_card)
	{
		$sql = "SELECT uid, activity_id, id_card FROM cp_activity_jc_join WHERE $activity_id = ? AND id_card = ?";
		$info = $this->db->query($sql, array($activity_id, $id_card))->getAll();
		return $info;
	}

	/*
 	 * 竞彩活动 - 购买
 	 */
	public function doPay($params = array())
	{
		$orderFormat = array(
			'uid' => '',
			'userName' => '',
            'ctype' => '',
            'buyPlatform' => '',
            'codes' => '',
            'lid' => '',
            'money' => '',
            'multi' => '',
            'issue' => '',
            'playType' => '',
            'isChase' => '',
            'betTnum' => '',
            'endTime' => '',
            'activity_id' => '',
            'activity_issue' => ''
        );

        // 必要参数检查
        foreach ($orderFormat as $key => $items) 
        {
            if($params[$key] === '' || !isset($params[$key]))
            {
                $result = array(
                    'status' => '100',
                    'msg' => '缺少必要参数',
                    'data' => ''
                );
                return $result;
            }
        }

        // 彩种销售状态判断
		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        $lotteryConfig = json_decode($lotteryConfig, true);
        
        $lotteryId = $params['lid'];
        
        $this->load->model('lottery_model');
        $cacheName = $this->lottery_model->getCache($lotteryId);
        if (!empty($cacheName)) 
        {
        	$cache = json_decode($this->cache->get($REDIS[$cacheName]), true);
        	
        	if (in_array($params['lid'], array(SSQ, DLT, PLW, QXC, QLC))
        			&& !empty($cache['aIssue'])
        			&& time() > (floor($cache['aIssue']['seEndtime']/1000)-$lotteryConfig[$params['lid']]['ahead']*60)
        			&& time() < floor($cache['aIssue']['seEndtime']/1000))
        	{
        		$result = array(
    				'status' => '101',
    				'msg' => "对不起，您购买的彩种已过当期投注截止时间，下一期开售时间为".date('H:i', (floor($cache['aIssue']['seEndtime']/1000)))."！",
    				'data' => ''
        		);
        		return $result;
        	}
        }

        if(empty($params['uid']))
        {
        	$result = array(
				'status' => '300',
				'msg' => '登录超时，请重新登录',
				'data' => ''
			);
			return $result;
        }

        // 用户余额 实名检查
        $this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($params['uid']);

		if(empty($uinfo['id_card']))
		{
			$result = array(
				'status' => '600',
				'msg' => '用户尚未实名认证',
				'data' => ''
			);
			return $result;
		}

		$params['id_card'] = $uinfo['id_card'];

        // 提交投注串及金额验证
        $checkResult = $this->checkOrders($params);

        if(!$checkResult['status'])
		{
			$result = array(
					'status' => '102',
					'msg' => $checkResult['msg'],
					'data' => ''
			);
			return $result;
		}

		// 订单金额范围检查
		$maxMoney = $this->getMaxMoney($params);
		if($params['money'] > $maxMoney['money'])
		{
			$result = array(
				'status' => '102',
				'msg' => "订单金额需小于{$maxMoney['name']}，请修改订单后重新投注",
				'data' => ''
			);
			return $result;
		}
		
		if (time() > ($lotteryConfig[$params['lid']]['endTime']-$lotteryConfig[$params['lid']]['ahead']) && time() < $lotteryConfig[$params['lid']]['endTime'])
		{
			$result = array(
				'status' => '101',
				'msg' => '对不起，您购买的彩钟已过当期投注截止时间！',
				'data' => ''
			);
			return $result;
		}

		// 金额检查
		if($params['money']%10 != 0)
		{
			$result = array(
                'status' => '102',
                'msg' => '投注金额不正确',
                'data' => ''
            );
            return $result;
		}

		if($params['money'] < 0)
		{
			$result = array(
                'status' => '102',
                'msg' => '投注金额不正确',
                'data' => ''
            );
            return $result;
		}

		if($params['money'] > 100)
		{
			$result = array(
                'status' => '102',
                'msg' => '投注金额不正确',
                'data' => ''
            );
            return $result;
		}

        // 金额转换为分
        $params['money'] = ParseUnit($params['money']);
        if($uinfo['money'] < $params['money'])
        {
        	$result = array(
                'status' => '400',
                'msg' => '扣款失败，余额不足',
                'data' => number_format(ParseUnit($uinfo['money'], 1), 2)
            );
            return $result;
        }

        switch ($params['ctype']) 
        {
        	case 'create':
        		$result = $this->simpleCheck($params, $uinfo);
        		break;

        	case 'pay':
        		$result = $this->payMoney($params);
        		break;
        	
        	default:
        		$result = array(
                    'status' => '100',
                    'msg' => '缺少必要参数',
                    'data' => ''
                );
        		break;
        }

        return $result;
	}

	/*
	 * 订单投注串及金额验证
	* @date:2015-05-22
	*/
	public function checkOrders($params)
	{
		$this->load->library('DisOrder');
		// 倍数校验
		if( preg_match('/^\d+$/', $params['multi']) )
		{
			if($params['multi'] <= 0)
			{
				$result = array(
						'status' => FALSE,
						'msg' => '倍数校验错误',
						'data' => ''
				);
				return $result;
			}
		}
		else
		{
			$result = array(
					'status' => FALSE,
					'msg' => '倍数校验错误',
					'data' => ''
			);
			return $result;
		}

		$check = true;
		switch ($params['lid'])
		{
			// 胜负彩
			case '11':
				$results = $this->disorder->_dismantle_number($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 任九
			case '19':
				$results = $this->disorder->_dismantle_rj($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 排列三
			case '33':
				$results = $this->disorder->dismantle_3dAndpls($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 排列五
			case '35':
				$results = $this->disorder->dismantle_plcomm($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 竞彩足球
			case '42':
				if($params['playType'] == '6')
				{
					$results = $this->disorder->_dismantle_single_match($params['codes'], $check);
					$money = $results['money'];
				}
				else if($params['playType'] == '7')
				{
					$results = $this->disorder->_dismantle_optimization($params['codes'], $check);
					if(!$results)
					{
						$results['betnum'] = 0;
					}
					else 
					{
						$money = $results['money'];
					}
				}
				else
				{
					$results = $this->disorder->_dismantle_match($params['codes'], $params['multi'], $check);
					$money = $results['betnum'] * $params['multi'] * 2;
				}
				break;
				// 竞彩篮球
			case '43':
				if($params['playType'] == '7')
				{
					$results = $this->disorder->_dismantle_optimization($params['codes'], $check);
					if(!$results)
					{
						$results['betnum'] = 0;
					}
					else 
					{
						$money = $results['money'];
					}
				}
				else
				{
					$results = $this->disorder->_dismantle_match($params['codes'], $params['multi'], $check);
					$money = $results['betnum'] * $params['multi'] * 2;
				}
				break;
				// 双色球
			case '51':
				$results = $this->disorder->dismantle_ball($params['codes'], 6, 1, $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 福彩3D
			case '52':
				$results = $this->disorder->dismantle_3dAndpls($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 七星彩
			case '10022':
				$results = $this->disorder->dismantle_plcomm($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 十一选五
			case '21406':
				$results = $this->disorder->dismantle_syxw($params['codes'], $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 七乐彩
			case '23528':
				$results = $this->disorder->dismantle_qlc($params['codes'], 7, $check);
				$money = $results['betnum'] * $params['multi'] * 2;
				break;
				// 大乐透
			case '23529':
				$results = $this->disorder->dismantle_ball($params['codes'], 5, 2, $check);
				if($params['isChase'])
				{
					$money = $results['betnum'] * $params['multi'] * 3;
				}
				else
				{
					$money = $results['betnum'] * $params['multi'] * 2;
				}
				break;
			default:
				$results['betnum'] = 0;
				$money = 0;
				break;
		}
		
		// 注数校验
		if( !isset($results['betnum']) || $results['betnum'] == 0 )
		{
			$result = array(
					'status' => FALSE,
					'msg' => '投注串校验失败',
					'data' => ''
			);
			return $result;
		}
	
		// 订单注数校验
		if( $results['betnum'] != $params['betTnum'] )
		{
			$result = array(
					'status' => FALSE,
					'msg' => '注数校验出错',
					'data' => ''
			);
			return $result;
		}
	
		// 投注串解析 金额校验
		if($money == $params['money'])
		{
			$result = array(
					'status' => TRUE,
					'msg' => '订单校验正确',
					'data' => ''
			);
		}
		else
		{
			$result = array(
					'status' => FALSE,
					'msg' => '订单校验错误',
					'data' => ''
			);
		}
		return $result;
	}

	/*
	 * 返还指定彩种的订单最大金额
	 */
	private function getMaxMoney($params)
	{
		if(isset($this->moneyMax[$params['lid']]))
		{
			return $this->moneyMax[$params['lid']];
		}
		else
		{
			return array('name' => '2万', 'money' => '20000');
		}
	}

	/*
 	 * 竞彩活动 - 检查活动库存及用户余额
 	 */
	public function simpleCheck($data, $uinfo)
	{
		$activityInfo = $this->getActivityDetail($data['activity_id'], $data['activity_issue']);
		$activityInfo = $activityInfo[0];

		// 投注串检查
		$checkRes = $this->checkActivityCodes($data, $activityInfo);

		if(!$checkRes)
		{
			$result = array(
				'status' => '102',
				'msg' => '投注串校验错误',
				'data' => ''
			);
			return $result;
		}

		// 检查用户是否已参与
		$joinInfo = $this->getJoinDetail($data['activity_id'], $data['id_card']);
		$joinInfo = $joinInfo[0];
		
		if(!empty($joinInfo))
		{
			$result = array(
                'status' => '505',
                'msg' => '您已参与过不中包赔活动，若想继续购买您可以前往投注页自购订单（该订单不计入包赔活动）',
                'data' => ''
            );
            return $result;
		}

		if(empty($activityInfo))
		{
			$result = array(
                'status' => '500',
                'msg' => '当前活动信息不存在',
                'data' => ''
            );
            return $result;
		}

		if( date('Y-m-d H:i:s') < $activityInfo['startTime'] )
		{
			$result = array(
                'status' => '503',
                'msg' => '当前活动尚未开始',
                'data' => ''
            );
            return $result;
		}

		if( date('Y-m-d H:i:s') > $data['endTime'] )
		{
			$result = array(
                'status' => '504',
                'msg' => '当前活动已过截止时间',
                'data' => ''
            );
            return $result;
		}

		if($data['lid'] == '42')
		{
			if($data['codecc'] != $activityInfo['mid'])
			{
				$result = array(
	                'status' => '500',
	                'msg' => '当前活动信息不存在',
	                'data' => ''
	            );
	            return $result;
			}
		}

		// 余额不足
		if($uinfo['money'] < $data['money'])
        {
        	$result = array(
                'status' => '400',
                'msg' => '扣款失败，余额不足',
                'data' => number_format(ParseUnit($uinfo['money'], 1), 2)
            );
            return $result;
        }

        // 赔付份额不足
        if( $data['money'] > $activityInfo['left_money'] && $activityInfo['left_money'] > 0)
		{
			$result = array(
                'status' => '502',
                'msg' => '您好，当前总担保池仅剩' . ParseUnit($activityInfo['left_money'], 1) . '元，不足以完成您的订单，请修改参与金额后重新提交订单！',
                'data' => ''
            );
            return $result;
		}

		// 份额已满
		if($activityInfo['left_money'] == 0)
		{
			$result = array(
                'status' => '501',
                'msg' => '对不起，本期活动担保额已满，您可以去投注页自购订单(该订单不计入包赔活动），下次记得尽早参与活动！',
                'data' => ''
            );
            return $result;
		}

		$result = array(
            'status' => '200',
            'msg' => '购买条件满足',
            'data' => number_format(ParseUnit($uinfo['money'], 1), 2)
        );
        return $result;
	}

	/*
 	 * 竞彩活动 - 支付
 	 */
	public function payMoney($orderData)
	{
		// 事务开始
    	$this->db->trans_start();

    	// 生成订单号
    	$orderData['orderId'] = $this->tools->getIncNum('UNIQUE_KEY');

		// 用户余额扣款
		$payStatus = $this->payOrderMoney($orderData);

		if($payStatus['status'] && !empty($payStatus['trade_no']))
		{
			// 活动库存检查、更新
			$activityResult = $this->updateActivity($orderData);

			if($activityResult['status'])
			{
				// 创建订单
				$orderData['trade_no'] = $payStatus['trade_no'];
				$orderStatus = $this->dealOrder($orderData);

				if($orderStatus['status'])
				{
					$this->db->trans_complete();
					// 刷新钱包
					$this->freshWallet($orderData['uid']);
		        	$result = array(
		    			'status' => '200',
						'msg' => '预约活动成功',
						'data' => $orderData['orderId']
		    		);
				}
				else
				{
					$this->db->trans_rollback();

		        	$result = array(
		    			'status' => '600',
						'msg' => $orderStatus['msg'],
						'data' => ''
		    		);
				}
			}
			else
			{
				$this->db->trans_rollback();

	        	$result = array(
	    			'status' => $activityResult['code'],
					'msg' => $activityResult['msg'],
					'data' => ''
	    		);
			}
		}
		else
		{
			$this->db->trans_rollback();

        	$result = array(
    			'status' => '400',
				'msg' => $payStatus['msg'],
				'data' => $payStatus['data']
    		);
		}
		
		return $result;
	}

	/*
 	 * 竞彩活动 - 活动库存检查、更新
 	 */
	public function updateActivity($orderData)
	{
		// 活动库存锁
		$activityInfo = $this->getActivityInfo($orderData['activity_issue'], $orderData['activity_id']);

		// 投注串检查
		$checkRes = $this->checkActivityCodes($orderData, $activityInfo);

		if(!$checkRes)
		{
			$activityStatus = array(
				'status' => FALSE,
                'code' => '102',
				'msg' => '投注串校验错误',
				'data' => ''
			);
			return $activityStatus;
		}

		// 截止时间
		if( date('Y-m-d H:i:s') < $activityInfo['startTime'] )
		{
			$activityStatus = array(
				'status' => FALSE,
                'code' => '503',
                'msg' => '当前活动尚未开始',
                'data' => ''
            );
            return $activityStatus;
		}

		if( date('Y-m-d H:i:s') > $orderData['endTime'] )
		{
			$activityStatus = array(
				'status' => FALSE,
                'code' => '504',
                'msg' => '当前活动已过截止时间',
                'data' => ''
            );
            return $activityStatus;
		}

		// 赔付份额
		if($activityInfo['left_money'] < $orderData['money'] && $activityInfo['left_money'] > 0)
		{
			$activityStatus = array(
				'status' => FALSE,
				'code' => '502',
                'msg' => '您好，当前总担保池仅剩' . ParseUnit($activityInfo['left_money'], 1) . '元，不足以完成您的订单，请修改参与金额后重新提交订单！',
                'data' => ''
			);
			return $activityStatus;
		}

		// 赔付份额
		if($activityInfo['left_money'] == 0)
		{
			$activityStatus = array(
				'status' => FALSE,
				'code' => '501',
	            'msg' => '对不起，本期活动担保额已满，您可以去投注页自购订单(该订单不计入包赔活动），下次记得尽早参与活动！',
	            'data' => ''
			);
			return $activityStatus;
		}

		// 记录用户参与信息
		$joinData = array(
			'uid' => $orderData['uid'],
			'activity_id' => $orderData['activity_id'],
			'id_card' => $orderData['id_card'],
			'userName' => $orderData['userName'],
			'activity_issue' => $orderData['activity_issue'],
			'orderId' => $orderData['orderId'],
			'money' => $orderData['money'],
			'buyPlatform' => $orderData['buyPlatform']
		);

		$res1 = $this->db->query("insert ignore cp_activity_jc_join(". implode(',', array_keys($joinData)) .', created)
		values('. implode(',', array_map(array($this, 'maps'), $joinData)) .', now())', $joinData);

		$joinId = $this->db->affected_rows();

		// 更新活动库存
		$res2 = $this->db->query("update cp_activity_jc_config set left_money = left_money - {$orderData['money']}, join_num = join_num + 1 where activity_id = ? and activity_issue = ? and left_money >= {$orderData['money']} and startTime <= now() and status = 0 and pay_status = 0 and delete_flag = 0", array($orderData['activity_id'], $orderData['activity_issue']));
		
		if(!$res1 || empty($joinId))
		{
			$activityStatus = array(
				'status' => FALSE,
				'code' => '505',
                'msg' => '您已参与过不中包赔活动，若想继续购买您可以前往投注页自购订单（该订单不计入包赔活动）',
                'data' => ''
			);
			
		}
		elseif(!$res2)
		{
			$activityStatus = array(
				'status' => FALSE,
				'code' => '502',
                'msg' => '您好，当前总担保池仅剩' . ParseUnit($activityInfo['left_money'], 1) . '元，不足以完成您的订单，请修改参与金额后重新提交订单！',
                'data' => ''
			);
		}
		else
		{
			$activityStatus = array(
				'status' => TRUE,
				'code' => '',
	            'msg' => '活动库存正常',
	            'data' => ''
			);
		}	
		return $activityStatus;
	}

	/*
 	 * 竞彩活动 - 用户余额扣款
 	 */
	public function payOrderMoney($orderData)
	{
		// 活动库存锁
		$userInfo = $this->getUserMoney($orderData['uid']);

		if($userInfo['money'] >= $orderData['money'])
		{
			// 生成流水记录号
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

			// 计算其他余额花费
            $bmoney = ($userInfo['must_cost'] + $userInfo['dispatch']) - $orderData['money'];
            $must_cost = 0;
            if ($bmoney >= $userInfo['must_cost'])
            {
                $dispatch = $orderData['money'];
            }
            elseif ($bmoney >= 0)
            {
                $dispatch = $userInfo['dispatch'];
                $must_cost = abs($dispatch - $orderData['money']);
            }
            else
            {
                $dispatch = $userInfo['dispatch'];
                $must_cost = $userInfo['must_cost'];
            }

            $wallet_log = array(
                'uid'       => $orderData['uid'],
                'money'     => $orderData['money'],
                'ctype'     => 1,
                'trade_no'  => $trade_no,
                'umoney'    => ($userInfo['money'] - $orderData['money']),
                'must_cost' => $must_cost,
                'dispatch'  => $dispatch,
                'additions' => (empty($orderData['lid']) ? 0 : $orderData['lid']),
                'orderId'   => $orderData['orderId']
            );

            // 用户流水记录
            $res1 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
			values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

			// 用户余额扣款
            $res2 = $this->db->query("update cp_user set money = money - {$orderData['money']},
			must_cost = if((must_cost + dispatch) > {$orderData['money']}, 
			if(dispatch - {$orderData['money']} > 0, must_cost, must_cost + (dispatch - {$orderData['money']})), 0),
			dispatch = if(dispatch > {$orderData['money']}, dispatch - {$orderData['money']}, 0)
			where money >= {$orderData['money']} and uid = ?", array($orderData['uid']));

            // 总账购彩支出
            $this->load->model('capital_model');
        	$res3 = $this->capital_model->recordCapitalLog('1', $trade_no, 'pay', $orderData['money'], '1', $tranc = FALSE);

			if($res1 && $res2 && $res3)
			{
				$payStatus = array(
					'status' => TRUE,
					'trade_no' => $trade_no,
		            'msg' => '扣款完成',
		            'data' => ''
				);
			}
			else
			{
				$payStatus = array(
					'status' => FALSE,
					'trade_no' => '',
		            'msg' => '扣款失败，余额不足',
		            'data' => number_format(ParseUnit($userInfo['money'], 1), 2)
				);
			}
		}
		else
		{
			$payStatus = array(
				'status' => FALSE,
				'trade_no' => '',
                'msg' => '扣款失败，余额不足',
                'data' => number_format(ParseUnit($userInfo['money'], 1), 2)
			);
		}
		return $payStatus;
	}

	/*
 	 * 竞彩活动 - 生成订单
 	 */
	public function dealOrder($orderData)
	{
		$orderStatus = array(
			'status' => FALSE,
			'msg' => '订单创建失败',
			'data' => ''
		);

		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ticketSeller = unserialize($this->cache->get($REDIS['TICKET_SELLER'])); //分配票商

        $this->load->model('activity_model');
        // 组装追号信息数据
		$orderInfo = array(
			'uid' => $orderData['uid'],
			'userName' => $orderData['userName'],
			'orderId' => $orderData['orderId'],
			'trade_no' => $orderData['trade_no'],
			'buyPlatform' => $orderData['buyPlatform'],
			'codes' => $orderData['codes'],
			'lid' => $orderData['lid'],
			'money' => $orderData['money'],
			'multi' => $orderData['multi'],
			'issue' => $orderData['issue'],
			'playType' => $orderData['playType'],
			'isChase' => $orderData['isChase'],
			'orderType' => 3, // 赔付类型
			'betTnum' => $orderData['betTnum'],
			'status' => $this->order_status['pay'],
			'codecc' => $orderData['codecc'],
			'channel' => $orderData['channel']?$orderData['channel']:'0',
			'app_version' => $orderData['app_version']?$orderData['app_version']:'0',
			'mark' => 0,
			'pay_time' => date('Y-m-d H:i:s'),
			'endTime' => $orderData['endTime'],
			'ticket_seller' => $ticketSeller[$orderData['lid']],
			'shopId' => $this->getBetStation($ticketSeller[$orderData['lid']], $orderData['lid']),
			'activity_ids' => $this->activity_model->checkRebateByUid($orderData['uid']) | 2
		);

		// 创建已支付订单
		if($this->doBetOrder($orderInfo))
		{
			$orderStatus = array(
				'status' => TRUE,
				'msg' => '订单创建成功',
				'data' => $orderInfo
			);
		}
		else
		{
			$orderStatus = array(
				'status' => FALSE,
				'msg' => '订单创建失败',
				'data' => $orderInfo
			);
		}
		
		return $orderStatus;
	}

	/*
 	 * 竞彩活动 - 投注串检查
 	 */
	public function checkActivityCodes($orderData, $activityInfo)
	{
		// 解析投注串
        $casts = explode(';', $orderData['codes']);
        if(count($casts) > 1)
        {
            return false;
        }
        $casts = explode('|', $orderData['codes']);
        $codeArr = explode('>', $casts[1]);
        if(strtoupper($codeArr[0]) != strtoupper($activityInfo['playType']))
        {
            return false;
        }
        $planArr = explode('=', $codeArr[1]);
        if($planArr[0] != $activityInfo['mid'])
        {
            return false;
        }
        $rule = '/(\d+)\(.*?\)/is';
        preg_match($rule, $planArr[1], $matchs);
        if($matchs[1] != $activityInfo['plan'])
        {
            return false;
        }
        return true;
	}

	/*
 	 * 竞彩活动 - 投注订单
 	 */
	public function doBetOrder($orderInfo)
	{
		$upfields = array('status', 'bonus', 'margin', 'eachAmount', 'channel', 'codecc', 'qsFlag', 'ticket_time', 'win_time', 'trade_no', 'mark', 'pay_time');
        $fields = array_keys($orderInfo);
        $sql = "insert cp_orders(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())" . $this->onduplicate($fields, $upfields);
		return $this->db->query($sql, $orderInfo);
	}

	/*
 	 * 竞彩活动 - 查询用户信息
 	 */
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}

	/*
 	 * 竞彩活动 - 查询用户信息
 	 */
	public function getActivityInfo($activity_issue, $activity_id = '3')
	{
		$sql = "SELECT activity_id, activity_issue, mid, plan, lid, playType, startTime, pay_money, left_money, join_num, status, pay_status, delete_flag, created 
		FROM cp_activity_jc_config
		WHERE activity_id = ? AND activity_issue = ? AND delete_flag = 0 for update";

		return $this->db->query($sql, array($activity_id, $activity_issue))->getRow();
	}
	
	/*
 	 * 竞彩活动 - 刷新钱包
 	 */
    public function freshWallet($uid)
    {
        $this->load->model('wallet_model');
		return $this->wallet_model->freshWallet($uid);
    }

    
    public function getMatchInfo($mid)
    {
    	$this->dcDB = $this->load->database('dc', true);
    	$this->cfgDB = $this->load->database('cfg', true);
    	$sql1 = "select league_abbr, home_abbr, away_abbr, codes, ctype from cp_jczq_match where mid = ?";
    	$sql2 = "select full_score, end_sale_time from cp_jczq_paiqi where mid = ?";
    	$result = $this->dcDB->query($sql1, array($mid))->getAll();
    	$res2 = $this->cfgDB->query($sql2, array($mid))->getRow();
    	foreach ($result as $val) {
    		if ($val['ctype'] == 1) {
    			$codes1 = @unserialize($val['codes']);
    		}elseif ($val['ctype'] == 2) {
    			$codes2 = @unserialize($val['codes']);
    		}
    	}
    	$res = array(
    		'nameSname' => $result[0]['league_abbr'],
    		'homeSname' => $result[0]['home_abbr'],
    		'awarySname' => $result[0]['away_abbr'],
    		'spfSp3' => $codes1['h'],
    		'spfSp1' => $codes1['d'],
    		'spfSp0' => $codes1['a'],
    		'rqspfSp3' => $codes2['h'],
    		'rqspfSp1' => $codes2['d'],
    		'rqspfSp0' => $codes2['a'],
    		'full_score' => $res2['full_score'],
    		'dt' => strtotime($res2['end_sale_time']) * 1000
    	);
    	return $res;
    }


    /*
     * 出票订单随机分配投注站
     * @date:2016-02-01
     */
    public function getBetStation($seller, $lid)
    {
    	$partnerArr = $this->config->item('cfg_partner_lid');
    	$search = array(
    			'partner_name' => $seller,
    			'lottery_type' => $this->getLotteryType($lid),
    			'status' => '30',
    			'delete_flag' => '0',
    			'lid' => array_key_exists($lid, $partnerArr) ? $lid : '0'
    	);
    
    	$sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone,
    	qq, webchat, other_contact, address, fail_reason, off_reason,
    	delete_flag, status, created
    	FROM cp_partner_shop
    	WHERE partner_name = ? AND lottery_type = ? AND status = ? AND delete_flag = ? AND lid = ?";
    
    	$stationInfo = $this->db->query($sql, array($search['partner_name'], $search['lottery_type'], $search['status'], $search['delete_flag'], $search['lid']))->getAll();
    
    	$shopId = '0';
    	if(!empty($stationInfo))
    	{
    		$stationNum = count($stationInfo) - 1;
    		$stationIndex = rand(0, $stationNum);
    		$shopId = $stationInfo[$stationIndex]['id'];
    	}
    	return $shopId;
    }

    /*
     * 获取彩种所属类型 福彩 体彩
     * @date:2016-01-27
     */
    public function getLotteryType($lid)
    {
        // 福彩：双色球，福彩3D，七乐彩
        if(in_array($lid, array('51', '52', '23528')))
        {
            $lotteryType = 1;
        }
        else
        {
            $lotteryType = 0;
        }
        return $lotteryType;
    }

    /*
 	 * 竞彩活动 - 脚本 - 同步订单状态 处理赔付
 	 */
    public function dealJcOrder($orderInfo)
    {
    	$result = FALSE;

        if(!empty($orderInfo))
        {
        	if(in_array($orderInfo['status'], array($this->order_status['concel'], $this->order_status['notwin'], $this->order_status['win'])))
        	{
        		// 未中奖赔款处理
        		$pay_status = 0;
        		$returnFlag = TRUE;
        		if($orderInfo['status'] == $this->order_status['notwin'])
        		{
        			// 赔付
        			$pay_status = $this->returnPayMoney($orderInfo);

        			if(!$pay_status)
        			{
        				$returnFlag = FALSE;
        			}
        		}

        		// 同步至活动订单
        		$orderArry = array(
        			'orderId' => $orderInfo['orderId'],
        			'uid' => $orderInfo['uid'],
        			'status' => $orderInfo['status'],
        			'bonus' => $orderInfo['bonus'],
        			'pay_status' => $pay_status
        		);

        		$updateRes = $this->updateJoinInfo($orderArry);

        		if($updateRes && $returnFlag)
        		{
        			$result = TRUE;
        		}
        	}
        }
        return $result;
    }

    /*
 	 * 竞彩活动 - 脚本 - 赔付
 	 */
    public function returnPayMoney($orderInfo, $tranc = FALSE)
    {
    	if($tranc)
    	{
    		// 事务开始
    		$this->db->trans_start();
    	}

    	$userInfo = $this->getUserMoney($orderInfo['uid']);

    	$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

    	// 彩金派送流水
    	$wallet_log = array(
            'uid'       => $orderInfo['uid'],
            'money'     => $orderInfo['money'],
            'ctype'     => 9,
            'trade_no'  => $trade_no,
            'umoney'    => ($userInfo['money'] + $orderInfo['money']),
            'must_cost' => 0,
            'dispatch'  => 0,
            'mark'      => '1',
            'orderId'   => $orderInfo['orderId'],
            'content'   => '竞彩活动赔付'
        );

        $res1 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

		// 用户余额扣款
        $res2 = $this->db->query("update cp_user set money = money + {$orderInfo['money']}, dispatch = dispatch + {$orderInfo['money']} where uid = ?", array($orderInfo['uid']));

        // 总账记录流水
        $this->load->model('capital_model');
        $res3 = $this->capital_model->recordCapitalLog('2', $trade_no, 'repaid', $orderInfo['money'], '2', $tranc = FALSE);

        if($res1 && $res2 && $res3)
        {
        	if($tranc)
        	{
        		$this->db->trans_complete();
        	}
        	// 刷新钱包缓存
        	$this->load->model('wallet_model');
        	$this->wallet_model->freshWallet($orderInfo['uid']);
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

    public function updateJoinInfo($orderInfo)
    {
    	return $this->db->query("update cp_activity_jc_join set status = ?, bonus = ?, pay_status = ? where uid = ? and orderId = ? and pay_status = 0", array($orderInfo['status'], $orderInfo['bonus'], $orderInfo['pay_status'], $orderInfo['uid'], $orderInfo['orderId']));
    }

    public function getActivityConfig()
    {
    	$sql = "SELECT activity_id, activity_issue, mid FROM cp_activity_jc_config WHERE status = '0' ORDER BY created ASC LIMIT 20";
    	return $this->db->query($sql)->getAll();
    }

    public function getJoinConfig($activityInfo)
    {
    	$sql = "SELECT uid, orderId, status, pay_status FROM cp_activity_jc_join WHERE activity_id = ? AND activity_issue = ? AND delete_flag = 0";
    	return $this->db->query($sql, array($activityInfo['activity_id'], $activityInfo['activity_issue']))->getAll();
    }

    public function synActivityConfig($activityInfo)
    {
    	$sql = "UPDATE cp_activity_jc_config SET status = ?, pay_status = ? WHERE activity_id = ? AND activity_issue = ?";
    	return $this->db->query($sql, array($activityInfo['status'], $activityInfo['pay_status'], $activityInfo['activity_id'], $activityInfo['activity_issue']));
    }
    
    public function calResult($lid)
    {
        if($lid == 42){
            $day = date("Y-m-d 00:00:00", strtotime("-1 day"));
            $sql = "select id,mid,m_date,rq,half_score,full_score from cp_jczq_paiqi where show_end_time < now() and end_sale_time >= ?";
            $matches = $this->cfgDB->query($sql, array($day))->getAll();
            $ctypeMap = array(
                '0' => 'sg',
                '1' => 'spf',
                '2' => 'rqspf',
                '3' => 'bqc',
                '4' => 'jqs',
                '5' => 'cbf',
            );
            $this->cfgDB->trans_start();
            $spf = array('负', '平', '胜', '胜');
            foreach ($matches as $match)
            {
                $result = array();
                if ($match['full_score']) {
                    $fall_score = explode(':', $match['full_score']);
                    $result['spf'] = $this->cal_mresult($fall_score);
                    $result['rqspf'] = $this->cal_mresult(array($fall_score[0] + $match['rq'], $fall_score[1]));
                    $result['bf'] = $match['full_score'];
                    $result['jqs'] = $fall_score[0] + $fall_score[1];
                    $half_score = explode(':', $match['half_score']);
                    $half = $this->cal_mresult($half_score);
                    $result['bqc'] = $spf[$half] . '-' . $spf[$result['spf']];
                }
                $sql1 = "select ctype,codes from cp_jczq_match where mid =?";
                $detail = $this->dc->query($sql1, array($match['mid']))->getAll();
                $awards = array();
                foreach ($detail as $key => $items) 
                {
                    $awards[$ctypeMap[$items['ctype']]] = $items['codes'];
                }
                $awards['info']['spf'] = $awards['spf'];
                $awards['info']['rqspf'] = $awards['rqspf'];
                $awards['info']['jqs'] = $awards['jqs'];
                $awards['info']['bqc'] = $awards['bqc'];
                $awards['info']['cbf'] = $awards['cbf'];
                $this->cfgDB->query("update cp_jczq_paiqi set result=?,odds=? where id=?", array(json_encode($result), json_encode($awards['info']), $match['id']));
            }
            $this->cfgDB->trans_complete();
        }
        if ($lid == 43)
        {
            $day = date("Y-m-d 00:00:00", strtotime("-1 day"));
            $sql = "select id,mid,m_date,rq,preScore,full_score from cp_jclq_paiqi where show_end_time < now() and show_end_time >= ?";
            $matches = $this->cfgDB->query($sql, array($day))->getAll();
            $this->cfgDB->trans_start();
            $ctypeMap = array(
                '1' => 'sf',
                '2' => 'rfsf',
                '3' => 'sfc',
                '4' => 'dxf',
            );
            foreach ($matches as $match)
            {
                $result = array();
                if ($match['full_score']) {
                    $fall_score = explode(':', $match['full_score']);
                    $result['sf'] = $this->cal_mresult($fall_score);
                    $result['rfsf'] = $this->cal_mresult(array($fall_score[0], $fall_score[1] + $match['rq']));
                    $preScore = str_replace('+', '', $match['preScore']);
                    $result['dxf'] = 0;
                    $score = $fall_score[0] + $fall_score[1];
                    if ($score > $preScore) {
                        $result['dxf'] = 3;
                    }
                    $result['sfc'] = $this->cal_diff($fall_score);
                }
                $sql1 = "select ctype,codes from cp_jclq_match where mid =?";
                $detail = $this->dc->query($sql1, array($match['mid']))->getAll();
                $awards = array();
                foreach ($detail as $key => $items) 
                {
                    $awards[$ctypeMap[$items['ctype']]] = $items['codes'];
                }
                $awards['info']['sf'] = $awards['sf'];
                $awards['info']['rfsf'] = $awards['rfsf'];
                $awards['info']['sfc'] = $awards['sfc'];
                $awards['info']['dxf'] = $awards['dxf'];
                $this->cfgDB->query("update cp_jclq_paiqi set result=?,odds=? where id=?", array(json_encode($result), json_encode($awards['info']), $match['id']));
            }
            $this->cfgDB->trans_complete();
        }
    }
    
    private function cal_mresult($score)
    {
        $mresult = '0';
        if ($score[0] > $score[1]) {
            $mresult = '3';
        } elseif ($score[0] == $score[1]) {
            $mresult = '1';
        }
        return $mresult;
    }

    private function cal_diff($score)
    {
            $diff = $score[1] - $score[0];
            $diff = abs($diff);
            if($diff >= 1 && $diff <= 5)
            {
                    $re = "{$pre}1";
            }
            elseif($diff >= 6 && $diff <= 10)
            {
                    $re = "{$pre}2";
            }
            elseif($diff >= 11 && $diff <= 15)
            {
                    $re = "{$pre}3";
            }
            elseif($diff >= 16 && $diff <= 20)
            {
                    $re = "{$pre}4";
            }
            elseif($diff >= 21 && $diff <= 25)
            {
                    $re = "{$pre}5";
            }
            elseif($diff >= 26) 
            {
                    $re = "{$pre}6";
            }
            return $re;
    }
    
    public function getOldMatch($date)
    {
        $start = date("Y-m-d", strtotime($date));
        $sql="select p.mid,p.m_date,p.hot,p.odds,p.hotid,p.mname,p.league,p.home,p.away,p.rq,p.half_score,p.full_score,p.result,p.end_sale_time,p.show_end_time,z.zhisheng,p.m_status from cp_jczq_paiqi p left join cp_jczq_zhisheng z on p.mid=z.mid where p.show_end_time < now() and p.m_date= ?  order by p.mid";
        return $this->cfgDB->query($sql, array($start))->getAll();
    }
    
    private function getCbf($score)
    {
        $cbf = '';
        $tag = array(
            '1:0'   =>  's1',
            '2:0'   =>  's2',
            '2:1'   =>  's3',
            '3:0'   =>  's4',
            '3:1'   =>  's5',
            '3:2'   =>  's6',
            '4:0'   =>  's7',
            '4:1'   =>  's8',
            '4:2'   =>  's9',
            '5:0'   =>  's10',
            '5:1'   =>  's11',
            '5:2'   =>  's12',
            '0:0'   =>  's14',
            '1:1'   =>  's15',
            '2:2'   =>  's16',
            '3:3'   =>  's17',
            '0:1'   =>  's19',
            '0:2'   =>  's20',
            '1:2'   =>  's21',
            '0:3'   =>  's22',
            '1:3'   =>  's23',
            '2:3'   =>  's24',
            '0:4'   =>  's25',
            '1:4'   =>  's26',
            '2:4'   =>  's27',
            '0:5'   =>  's28',
            '1:5'   =>  's29',
            '2:5'   =>  's30',
        );
        if(!empty($score))
        {
            $scoreArr = explode(':', $score);
            if($scoreArr[0] > $scoreArr[1])
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's13';
            }
            elseif($scoreArr[0] == $scoreArr[1])
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's18';
            }
            else
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's31';
            }
        }
        return $cbf;
    }
    
    public function getMatch($mid, $field = '*')
    {
        $sql = "select {$field} from cp_jczq_paiqi where mid=?";
        return $this->cfgDB->query($sql, array($mid))->getRow();
    }
    

    public function countOldMatch($date)
    {
        $start = date("Y-m-d", strtotime($date));
        $sql = "select count(*) as count from cp_jczq_paiqi where show_end_time < now() and m_date = ?";
        return $this->cfgDB->query($sql, array($start))->getRow();
    }
    
    public function getOldLqMatch($date)
    {
        $start = date("Y-m-d", strtotime($date));
        $sql="select p.mid,p.m_date,p.hot,p.odds,p.hotid,p.mname,p.league,p.home,p.away,p.rq,p.preScore,p.full_score,p.result,p.show_end_time,p.begin_time,z.zhisheng,p.m_status from cp_jclq_paiqi p left join cp_jclq_zhisheng z on p.mid=z.mid where p.show_end_time < now() and p.m_date= ?  order by p.mid";
        return $this->cfgDB->query($sql, array($start))->getAll();
    }
    
  
    public function countOldLqMatch($date)
    {
        $start = date("Y-m-d", strtotime($date));
        $sql = "select count(*) as count from cp_jclq_paiqi where show_end_time < now() and m_date= ?";
        return $this->cfgDB->query($sql, array($start))->getRow();
    }
    
    public function getLqMatch($mid, $field = '*')
    {
        $sql = "select {$field} from cp_jclq_paiqi where mid=?";
        return $this->cfgDB->query($sql, array($mid))->getRow();
    }
    
    public function getZhisheng($issue,$lid)
    {
        if($lid == 42)
        {
            $sql = "select mid,zhisheng from cp_jczq_zhisheng where issue=?";
            return $this->cfgDB->query($sql, array($issue))->getAll();
        }
    }
    
    public function getHasEndMatch()
    {
        $day = date("Y-m-d 00:00:00", strtotime("-1 day"));
        $sql = "select count(*) as count from cp_jczq_paiqi where show_end_time < now() and odds='' and show_end_time >= ?";
        $jczq = $this->cfgDB->query($sql, array($day))->getRow();
        $sql = "select count(*) as count from cp_jclq_paiqi where show_end_time < now() and odds='' and show_end_time >= ?";
        $jclq = $this->cfgDB->query($sql, array($day))->getRow();
        return array($jczq['count'], $jclq['count']);
    }
}
