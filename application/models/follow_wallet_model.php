<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 定制跟单 - 流水 - 模型层
 */
class Follow_Wallet_Model extends MY_Model
{
	public $status = array(
        'recharge'                   	=>	0,	//充值-添加预付款
        'pay'                        	=> 	1, 	//付款-购买彩票
        'reward'                     	=> 	2, 	//奖金-奖金派送
        'drawback'                   	=> 	3, 	//订单退款-订单失败返款
        'apply_for_withdraw'         	=> 	4, 	//提款
        'apply_for_withdraw_succ'    	=> 	5, 	//提款成功解除冻结预付款(已废弃)
        'withdraw_succ'              	=> 	6, 	//申请提款成功-扣除预付款成功(已废弃)
        'apply_for_withdraw_conceal'	=>	7, 	//申请提款撤销(已废弃)
        'apply_for_withdraw_fail'    	=> 	8, 	//提款失败还款
        'dispatch'                   	=> 	9, 	//系统奖金派送
        'addition'                   	=> 	10, //其他应收款项
        'transfer'                   	=> 	11, //转帐
    	'rebate'                     	=> 	14, //联盟返点
    	'united_refund'				 	=> 	15, //合买返还预付款
    	'united_follow_freez'		 	=> 	16, //冻结合买跟单预付款
    	'united_follow_refund'			=> 	17,	//合买跟单退款
    );

	public function __construct() 
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->load->model('follow_order_model');
		$this->load->model('united_order_model');
		$this->orderStatus = $this->follow_order_model->getStatus();
	}

	// 预支付跟单扣款
	public function payForAdvance($params = array(), $trans = true)
    {
    	/*
    	$params = array(
    		'followId'		=>	'',
    		'uid'			=>	'',
    		'puid'			=>	'',
    		'lid'			=>	'',
    		'totalMoney'	=>	'',
    	);
    	*/

    	// 事务开始
    	if($trans) $this->db->trans_start();

    	// 用户信息锁表
    	$uinfo = $this->getUserMoney($params['uid']);

    	if(empty($uinfo))
    	{
    		if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	400,
				'msg' 	=> 	'用户信息获取失败',
				'data'	=> 	$params
    		);
    		return $result;
    	}

    	// 检查进行中的方案
    	$following = $this->follow_order_model->checkFollowingOrder($params['puid'], $params['uid'], $params['lid']);
    	if($following > 0)
		{
			if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	401,
				'msg' 	=> 	'您已定制发起人的方案，换个彩种试试吧',
				'data'	=> 	$params
    		);
    		return $result;
		}

    	// 红人跟单上限统计
    	$plannerInfo = array(
    		'uid'	=>	$params['puid'],
    		'lid'	=>	$params['lid'],
    	);
    	$recoredRes = $this->follow_order_model->recoredPlannerInfo($plannerInfo, 1, false);
    	if(!$recoredRes)
    	{
    		if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	402,
				'msg' 	=> 	'定制人数已达上限，换个彩种试试吧',
				'data'	=> 	$params
    		);
    		return $result;
    	}

    	// 扣款
    	$payData = $this->payFollowMoney($params, $uinfo);

    	if(!$payData['status'])
    	{
    		if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	403,
				'msg' 	=> 	$payData['msg'],
				'data'	=> 	$params
    		);
    		return $result;
    	}

    	// 更新订单信息
    	$followInfo = $this->follow_order_model->getFollowOrderDetail($params['followId']);

    	if(empty($followInfo) || $followInfo['uid'] != $params['uid'] || $followInfo['totalMoney'] != $params['totalMoney'])
    	{
    		if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	404,
				'msg' 	=> 	'跟单信息错误',
				'data'	=> 	$params
    		);
    		return $result;
    	}

    	$currentTime = date('Y-m-d H:i:s');
    	$endTime = date('Y-m-d H:i:s', strtotime('+60 days'));
    	$this->db->query("UPDATE cp_united_follow_orders SET trade_no = ?, status = {$this->orderStatus['following']}, effectTime = ?, endTime = ? WHERE followId = ? AND status = {$this->orderStatus['create']}", array($payData['trade_no'], $currentTime, $endTime, $params['followId']));
        $followRes = $this->db->affected_rows();

        if($followRes)
        {
        	if($trans) $this->db->trans_complete();
        	$result = array(
    			'code' 	=>	200,
				'msg' 	=> 	'跟单支付成功',
				'data'	=> 	$params
    		);
    		// 刷新钱包
    		$this->freshWallet($followInfo['uid']);
        }
        else
        {
        	if($trans) $this->db->trans_rollback();
        	$result = array(
    			'code' 	=>	405,
				'msg' 	=> 	'跟单支付失败，跟单状态不满足',
				'data'	=> 	$params
    		);
        }
        return $result;
    }

    // 预支付订单扣款
    public function payFollowMoney($followData, $uinfo = array())
    {
    	$payData = array(
			'status'	=>	FALSE,
			'msg' 		=> 	'支付失败',
			'trade_no'	=>	'',
		);

		$cost = intval($followData['totalMoney']);
		if(!empty($followData['followId']) && $uinfo['money'] >= $cost)
		{
			// 流水号生成
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			
			// 流水日志组装数据
			$wallet_log = array(
	            'uid'       =>	$followData['uid'],
	            'money'     =>	$cost,
	            'ctype'     =>	$this->status['united_follow_freez'],
	            'trade_no'  =>	$trade_no,
	            'mark' 		=> 	'0',
	            'umoney'    =>	($uinfo['money'] - $cost),
	            'must_cost' =>	0,
	            'dispatch'  =>	0,
	            'status'    =>  '4',	// 定制跟单
	            'additions' =>	(empty($followData['lid']) ? 0 : $followData['lid']),
	            'orderId'   =>	$followData['followId'],
	            'content'   =>  '跟单预付扣款',
	        );

			// 记录扣款流水
			$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			// 扣款 不计入总账流水
			$this->db->query("update cp_user set money = money - $cost, blocked = blocked + $cost,
			chaseMoney = chaseMoney + $cost
			where money >= $cost and uid = ?", array($followData['uid']));
			$userRes = $this->db->affected_rows();

			if($userRes)
			{
				$payData = array(
					'status'	=>	TRUE,
					'msg' 		=> 	'订单扣款成功！',
					'trade_no'	=>	$trade_no,
				);
			}
			else
			{
				$payData = array(
					'status'	=>	FALSE,
					'msg' 		=> 	'订单扣款失败！',
					'trade_no'	=>	'',
				);
			}
		}
		else
		{
			$payData = array(
				'status'	=>	FALSE,
				'msg' 		=> 	'订单支付失败,余额不足！',
				'trade_no'	=>	'',
			);
		}
		return $payData;
    }

    // 预支付跟单充值回调扣款
    public function autoPay($trade_no, $trans)
	{
		if($trade_no)
		{
			$orders = $this->db->query("SELECT f.followId, f.uid, f.puid, f.lid, f.totalMoney FROM cp_wallet_logs AS w JOIN cp_united_follow_orders AS f ON w.orderId = f.followId WHERE w.trade_no = ? AND f.status = 0 AND f.payType = 0 AND w.ctype = {$this->status['recharge']} AND w.status = 4", array($trade_no))->getRow();
			if(!empty($orders))
			{ 
				$followData = array(
					'followId'		=>	$orders['followId'],
    				'uid'			=>	$orders['uid'],
		    		'puid'			=>	$orders['puid'],
		    		'lid'			=>	$orders['lid'],
		    		'totalMoney'	=>	$orders['totalMoney'],
				);
				$this->payForAdvance($followData, $trans);
			}
		}
	}

    // 获得余额
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch, chaseMoney FROM cp_user WHERE uid = ? for update', array($uid))->getRow();
	}

	// 跟单任务待处理量
	public function getFollowTask()
	{
		$REDIS = $this->config->item('REDIS');
		$counts = $this->cache->Llen($REDIS['UNITED_FOLLOW']);
		$counts = $counts ? $counts : 0;
		return $counts;
	}

	// 跟单任务左入列
	public function pushFollowTask($followData)
	{
		if(!empty($followData))
		{
			$REDIS = $this->config->item('REDIS');
			$counts = $this->cache->Lpush($REDIS['UNITED_FOLLOW'], json_encode($followData));
			if($counts > 500)
			{
				// 报警
				$this->follow_order_model->insertAlert('跟单处理队列超过500单未处理，请尽快处理');
			}
		}	
	}

	// 跟单任务右出列
	public function popFollowTask()
	{
		$REDIS = $this->config->item('REDIS');
		$followData = json_decode($this->cache->Rpop($REDIS['UNITED_FOLLOW']), true);
		return $followData;
	}

	// 定制跟单
	public function handleFollowBet($unitedData, $followData, $trans = true)
	{
		/*
		$unitedData 	合买订单
		$followData 	跟单方案
		*/

		// 事务开始
    	if($trans) $this->db->trans_start();

    	// 行锁跟单方案
    	$followInfo = $this->follow_order_model->getFollowOrderDetail($followData['followId']);

    	// 检查跟单订单状态
    	if(empty($followInfo) || $followInfo['status'] != $this->orderStatus['following'] || $followInfo['followTimes'] == $followInfo['followTotalTimes'])
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
    			'code'			=>	400,
				'msg' 			=> 	'跟单方案状态不满足',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    		return $result;
    	}

    	// 检查是否已跟
    	$joinInfo = $this->follow_order_model->getJoinOrderDetail($followData['followId'], $unitedData['orderId']);
    	if(!empty($joinInfo))
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
    			'code'			=>	400,
				'msg' 			=> 	'当前合买订单已跟',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    		return $result;
    	}

    	// 行锁合买订单
    	$unitedInfo = $this->united_order_model->getUnitedOrderDetail($unitedData['orderId']);

    	// 合买订单状态
    	$this->unitedStatus = $this->united_order_model->getStatus();

    	// 检查合买订单状态 不满足即通知退出
    	if(empty($unitedInfo) || in_array($unitedInfo['status'], array($this->unitedStatus['revoke_by_user'], $this->unitedStatus['revoke_by_system'])) || $unitedInfo['endTime'] < date('Y-m-d H:i:s') || $unitedInfo['status'] < $this->unitedStatus['pay'] || $unitedInfo['status'] > $this->unitedStatus['draw'] || $unitedInfo['money'] == $unitedInfo['buyTotalMoney'])
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
    			'code'			=>	500,
				'msg' 			=> 	'合买订单状态不满足',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    		return $result;
    	}

    	// 根据方案给出实际跟单金额	
    	$actualMoney = $this->getActualMoney($followInfo, $unitedInfo);

    	// 根据方案走不同扣款
    	if($actualMoney['buyMoney'] > 0)
    	{
    		$payRes = $this->payBuyMoney($actualMoney, $unitedInfo, $followInfo);

    		// 实时扣款的实际认购金额
    		$actualMoney['buyMoney'] = $payRes['data']['buyMoney'];
    		
    		if(!$payRes['status'])
	    	{
	    		if($trans) $this->db->trans_rollback();
	    		$result = array(
	    			'code'			=>	400,
					'msg' 			=> 	$payRes['msg'],
					'unitedData'	=> 	$unitedData,
					'followData'	=>	$followData
	    		);
	    		return $result;
	    	}
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
    			'code'			=>	400,
				'msg' 			=> 	'跟单方案金额不满足',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    		return $result;
    	}

    	$subscribeId = $payRes['data']['subscribeId'];
    	$trade_no = $payRes['data']['trade_no'];

    	// 新增cp_united_join认购记录
    	$ujoinInfo = array(
    		'orderId'			=>	$unitedData['orderId'],
    		'trade_no'			=>	$trade_no,
			'subscribeId'		=>	$subscribeId,
			'uid'				=>	$followData['uid'],
    		'puid'				=>  $followData['puid'],
			'lid'				=>	$followData['lid'],
			'issue'				=>	$unitedInfo['issue'],
			'money'				=>	$unitedInfo['money'],
			'status'			=>	$unitedInfo['status'],
			'buyMoney'			=>	$actualMoney['buyMoney'],	// 实际认购金额
			'buyPlatform'		=>	$followData['buyPlatform'],
			'orderType'			=>	'2',
			'subOrderType'		=>	'1',	// 跟单
    	);
    	$ujoinRes = $this->united_order_model->saveJoinOrder($ujoinInfo);

    	// 新增cp_united_follow_join跟单记录
    	$fjoinInfo = array(
    		'followId'		=>	$followData['followId'],
    		'trade_no'		=>	$trade_no,
    		'hmOrderId'		=>	$unitedData['orderId'],
    		'subscribeId'	=>	$subscribeId,
    		'uid'			=>	$followData['uid'],
    		'puid'			=>	$followData['puid'],
    		'buyMoney'		=>	$actualMoney['buyMoney'],
    		'refundMoney'	=>	$actualMoney['refundMoney'],
    	);
    	$fjoinRes = $this->follow_order_model->saveJoinOrder($fjoinInfo);

    	// 更新合买订单状态
    	$this->db->query("UPDATE cp_united_orders SET buyTotalMoney = buyTotalMoney + ?, popularity = popularity + 1 WHERE orderId = ? AND {$actualMoney['buyMoney']} + buyTotalMoney <= money AND endTime > now()", array($actualMoney['buyMoney'], $unitedData['orderId']));
        $unitedRes = $this->db->affected_rows();

    	// 更新跟单方案信息 检查跟单次数已满则更新至跟单结束
    	$endTime = date('Y-m-d H:i:s', strtotime('+60 days'));
    	if($followInfo['payType'])
    	{
    		// 实时扣款 不修改blockMoney
    		$this->db->query("UPDATE cp_united_follow_orders SET followTimes = followTimes + 1, status = (if(followTimes = followTotalTimes, {$this->orderStatus['followed']}, {$this->orderStatus['following']})), lastFollowTime = NOW(), totalBuyMoney = totalBuyMoney + ?, endTime = ? WHERE followId = ? AND uid = ? AND followTimes < followTotalTimes AND status = {$this->orderStatus['following']}", array($actualMoney['buyMoney'], $endTime, $followData['followId'], $followData['uid']));
    		$followRes = $this->db->affected_rows();
    	}
    	else
    	{
    		$this->db->query("UPDATE cp_united_follow_orders SET blockMoney = blockMoney - {$actualMoney['money']}, followTimes = followTimes + 1, status = (if(followTimes = followTotalTimes, {$this->orderStatus['followed']}, {$this->orderStatus['following']})), lastFollowTime = NOW(), totalBuyMoney = totalBuyMoney + ?, endTime = ? WHERE followId = ? AND uid = ? AND blockMoney >= {$actualMoney['money']} AND followTimes < followTotalTimes AND status = {$this->orderStatus['following']}", array($actualMoney['buyMoney'], $endTime, $followData['followId'], $followData['uid']));
    		$followRes = $this->db->affected_rows();
    	}

    	// 跟单结束则更新红人统计
    	if($followInfo['followTimes'] + 1 == $followInfo['followTotalTimes'])
    	{
    		$plannerInfo = array(
    			'uid'	=>	$followInfo['puid'],
    			'lid'	=>	$followInfo['lid'],
    		);
    		$checkFollowed = $this->follow_order_model->recoredPlannerInfo($plannerInfo, 0, false);
    	}
    	else
    	{
    		$checkFollowed = TRUE;
    	}

    	if($ujoinRes && $fjoinRes && $unitedRes && $followRes && $checkFollowed)
    	{
    		if($trans) $this->db->trans_complete();
    		$result = array(
    			'code'			=>	200,
				'msg' 			=> 	'跟单成功',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    		// 刷新钱包
    		$this->freshWallet($followData['uid']);
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
    			'code'			=>	400,
				'msg' 			=> 	'跟单失败',
				'unitedData'	=> 	$unitedData,
				'followData'	=>	$followData
    		);
    	}
    	return $result;
	}

	// 根据方案给出实际跟单金额
	public function getActualMoney($followInfo, $unitedInfo)
	{
		$actualMoney = array(
			'money'			=>	0,	// 单笔金额
			'buyMoney'		=>	0,	// 实际支付
			'refundMoney'	=>	0,	// 实际退款
		);

		$leftMoney = $unitedInfo['money'] - $unitedInfo['buyTotalMoney'];		
		if($leftMoney > 0)
		{
			switch ($followInfo['followType']) 
			{
				case '0':
					// 按固定金额
					$actualMoney = $this->getMoneyByFixed($leftMoney, $followInfo);
					break;
				case '1':
					// 按百分比
					$actualMoney = $this->getMoneyByPercent($leftMoney, $followInfo, $unitedInfo);
					break;
				default:
					$actualMoney = array(
						'money'			=>	0,
						'buyMoney'		=>	0,
						'refundMoney'	=>	0,
					);
					break;
			}
		}
		if($followInfo['payType'])
		{
			// 实时扣款的实际退款金额始终是 0 
			$actualMoney['refundMoney'] = 0;
		}
		return $actualMoney;
	}

	// 按固定金额计算
	public function getMoneyByFixed($leftMoney, $followInfo)
	{
		if($leftMoney <= $followInfo['buyMoney'])
		{
			$actualMoney = array(
				'money'			=>	$followInfo['buyMoney'],
				'buyMoney'		=>	$leftMoney,
				'refundMoney'	=>	$followInfo['buyMoney'] - $leftMoney,
			);
		}
		else
		{
			$actualMoney = array(
				'money'			=>	$followInfo['buyMoney'],
				'buyMoney'		=>	$followInfo['buyMoney'],
				'refundMoney'	=>	0,
			);
		}
		return $actualMoney;
	}

	// 按百分比计算
	public function getMoneyByPercent($leftMoney, $followInfo, $unitedInfo)
	{
		// 单次平均金额
		$avgMoney = $followInfo['buyMaxMoney'];
		// 进位取整
		$buyMoney = $unitedInfo['money'] * ($followInfo['buyMoneyRate']/100);
		$buyMoney = ParseUnit(ceil(ParseUnit($buyMoney, 1)));
		// 是否高于最大认购金额
		if($buyMoney >= $followInfo['buyMaxMoney'])
		{
			$buyMoney = $followInfo['buyMaxMoney'];
		}

		if($leftMoney <= $buyMoney)
		{
			$actualMoney = array(
				'money'			=>	$followInfo['buyMaxMoney'],
				'buyMoney'		=>	$leftMoney,
				'refundMoney'	=>	($avgMoney > $leftMoney) ? ($avgMoney - $leftMoney) : 0,
			);
		}
		else
		{
			$actualMoney = array(
				'money'			=>	$followInfo['buyMaxMoney'],
				'buyMoney'		=>	$buyMoney,
				'refundMoney'	=>	($avgMoney > $buyMoney) ? ($avgMoney - $buyMoney) : 0,
			);
		}
		return $actualMoney;
	}

	// 根据方案走不同扣款及处理退款
	public function payBuyMoney($actualMoney, $unitedInfo, $followInfo)
	{
		switch ($followInfo['payType']) 
		{
			case '0':
				// 预付款 走chaseMoney字段扣款及处理退款
				$payRes = $this->payByAdvance($actualMoney, $unitedInfo, $followInfo);
				break;
			
			case '1':
				// 实时付款 走money字段扣款
				$payRes = $this->payByNow($actualMoney, $unitedInfo, $followInfo);
				break;

			default:
				$payRes = array(
					'status'	=>	FALSE,
					'msg'		=>	'付款类型错误',
					'data'		=>	''
				);
				break;
		}
		return $payRes;
	}

	// 预付款认购及处理退款
	public function payByAdvance($actualMoney, $unitedInfo, $followInfo)
	{
		$cost = intval($actualMoney['buyMoney']);

		// 行锁用户金额表
		$uinfo = $this->getUserMoney($followInfo['uid']);

		if(!empty($uinfo) && $uinfo['chaseMoney'] >= $cost)
		{
			// 生成认购订单号
			$subscribeId = $this->tools->getIncNum('UNIQUE_KEY');

			// 流水号生成
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

			//计算其他余额花费
	        $bmoney = ($uinfo['must_cost'] + $uinfo['dispatch']) - $cost;
	        $must_cost = 0;
	        if ($bmoney >= $uinfo['must_cost'])
	        {
	            $dispatch = $cost;
	        }
	        elseif ($bmoney >= 0)
	        {
	            $dispatch = $uinfo['dispatch'];
	            $must_cost = abs($dispatch - $cost);
	        }
	        else
	        {
	            $dispatch = $uinfo['dispatch'];
	            $must_cost = $uinfo['must_cost'];
	        }

			// 流水日志组装数据
			$wallet_log = array(
	            'uid'       =>	$followInfo['uid'],
	            'money'     =>	$cost,
	            'ctype'     =>	$this->status['pay'],
	            'trade_no'  =>	$trade_no,
	            'mark' 		=> 	'0',
	            'umoney'    =>	$uinfo['money'],
	            'must_cost' =>	$must_cost,
	            'dispatch'  =>	$dispatch,
	            'status'    =>  '5',	// 合买跟单，预付款跟单不展示
	            'additions' =>	(empty($followInfo['lid']) ? 0 : $followInfo['lid']),
	            'orderId'   =>	$unitedInfo['orderId'],
	            'subscribeId'   =>	$subscribeId,	// 流水关联认购订单号
	            'content'	=>	'',
	        );

	        // 记录扣款流水
			$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			// 预支付扣款
			$this->db->query("update cp_user set blocked = blocked - $cost, chaseMoney = chaseMoney - $cost,
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where chaseMoney >= $cost and uid = ?", array($followInfo['uid']));
			$userRes = $this->db->affected_rows();

			// 入总账流水
			$this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false);

			// 处理退款
			if($actualMoney['refundMoney'] > 0)
			{
				$refundRes = $this->handleRefund($actualMoney, $uinfo, $followInfo);
			}
			else
			{
				$refundRes = TRUE;
			}

			if($walletRes && $userRes && $capitalRes && $refundRes) 
			{
				$payRes = array(
					'status'	=>	TRUE,
					'msg'		=>	'付款及退款处理成功',
					'data'		=>	array(
						'subscribeId'	=>	$subscribeId,
						'trade_no'		=>	$trade_no,
						'buyMoney'		=>	$cost,
					)
				);
			}
			else
			{
				$payRes = array(
					'status'	=>	FALSE,
					'msg'		=>	'付款及退款处理失败',
					'data'		=>	''
				);
			}
		}
		else
		{
			$payRes = array(
				'status'	=>	FALSE,
				'msg'		=>	'付款金额错误',
				'data'		=>	''
			);
		}
		return $payRes;
	}

	// 处理退款
	public function handleRefund($actualMoney, $uinfo, $followInfo)
	{
		$cost = intval($actualMoney['refundMoney']);

		// 流水日志组装数据
		$wallet_log = array(
            'uid'       =>	$followInfo['uid'],
            'money'     =>	$cost,
            'ctype'     =>	$this->status['united_follow_refund'],
            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
            'mark' 		=> 	'1',
            'umoney'    =>	($uinfo['money'] + $cost),
            'must_cost' =>	0,
            'dispatch'  =>	0,
            'status'    =>  '4',	// 合买跟单
            'additions' =>	(empty($followInfo['lid']) ? 0 : $followInfo['lid']),
            'orderId'   =>	$followInfo['followId'],
            'content'	=>	'实际认购' . ParseUnit($actualMoney['buyMoney'], 1) . '元，退款' . ParseUnit($actualMoney['refundMoney'], 1) . '元',
        );

		// 记录退款流水
		$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
		values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

		// 预支付退款
		$this->db->query("update cp_user set money = money + $cost, blocked = blocked - $cost, chaseMoney = chaseMoney - $cost where chaseMoney >= $cost and uid = ?", array($followInfo['uid']));
		$userRes = $this->db->affected_rows();

		if($walletRes && $userRes)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// 实时付款认购
	public function payByNow($actualMoney, $unitedInfo, $followInfo)
	{
		$cost = intval($actualMoney['buyMoney']);

		// 行锁用户金额表
		$uinfo = $this->getUserMoney($followInfo['uid']);

		// 若账户余额小于实际认购金额 以账户余额扣款，但至少要1元
		if(!empty($uinfo) && $uinfo['money'] >= 100)
		{
			if($uinfo['money'] < $cost)
			{
				// 余额退位取整
				$cost = ParseUnit(intval(floor($uinfo['money']/100)));
				$cost = intval($cost);
			}
			// 生成认购订单号
			$subscribeId = $this->tools->getIncNum('UNIQUE_KEY');

			// 流水号生成
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

			//计算其他余额花费
	        $bmoney = ($uinfo['must_cost'] + $uinfo['dispatch']) - $cost;
	        $must_cost = 0;
	        if ($bmoney >= $uinfo['must_cost'])
	        {
	            $dispatch = $cost;
	        }
	        elseif ($bmoney >= 0)
	        {
	            $dispatch = $uinfo['dispatch'];
	            $must_cost = abs($dispatch - $cost);
	        }
	        else
	        {
	            $dispatch = $uinfo['dispatch'];
	            $must_cost = $uinfo['must_cost'];
	        }

			// 流水日志组装数据
			$wallet_log = array(
	            'uid'       =>	$followInfo['uid'],
	            'money'     =>	$cost,
	            'ctype'     =>	$this->status['pay'],
	            'trade_no'  =>	$trade_no,
	            'mark' 		=> 	'0',
	            'umoney'    =>	($uinfo['money'] - $cost),
	            'must_cost' =>	$must_cost,
	            'dispatch'  =>	$dispatch,
	            'status'    =>  '4',	// 合买跟单
	            'additions' =>	(empty($followInfo['lid']) ? 0 : $followInfo['lid']),
	            'orderId'   =>	$unitedInfo['orderId'],
	            'subscribeId'   =>	$subscribeId,	// 流水关联认购订单号
	            'content'	=>	'跟单实时扣款，参与合买：合买认购' . ParseUnit($cost, 1) . '元',
	        );

	        // 记录扣款流水
			$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			// 预支付扣款
			$this->db->query("update cp_user set money = money - $cost,
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where money >= $cost and uid = ?", array($followInfo['uid']));
			$userRes = $this->db->affected_rows();

			// 入总账流水
			$this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false);

			if($walletRes && $userRes && $capitalRes)
			{
				$payRes = array(
					'status'	=>	TRUE,
					'msg'		=>	'付款处理成功',
					'data'		=>	array(
						'subscribeId'	=>	$subscribeId,
						'trade_no'		=>	$trade_no,
						'buyMoney'		=>	$cost,
					)
				);
			}
			else
			{
				$payRes = array(
					'status'	=>	FALSE,
					'msg'		=>	'付款及退款处理失败',
					'data'		=>	''
				);
			}
		}
		else
		{
			$payRes = array(
				'status'	=>	FALSE,
				'msg'		=>	'付款金额错误',
				'data'		=>	''
			);
		}
		return $payRes;
	}

	// 撤单退款
	public function refundFollowOrder($followInfo, $trans = true)
	{
		$cost = intval($followInfo['blockMoney']);

		if($trans) $this->db->trans_start();

		// 行锁用户信息
		$uinfo = $this->getUserMoney($followInfo['uid']);

		if(!empty($uinfo))
		{
			// 流水日志组装数据
			$wallet_log = array(
	            'uid'       =>	$followInfo['uid'],
	            'money'     =>	$cost,
	            'ctype'     =>	$this->status['united_follow_refund'],
	            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
	            'mark' 		=> 	'1',
	            'umoney'    =>	($uinfo['money'] + $cost),
	            'must_cost' =>	0,
	            'dispatch'  =>	0,
	            'status'    =>  '4',	// 合买跟单
	            'additions' =>	(empty($followInfo['lid']) ? 0 : $followInfo['lid']),
	            'orderId'   =>	$followInfo['followId'],
	            'content'	=>	'跟单预付撤单退款',
	        );

	        // 记录退款流水
			$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			// 预支付退款
			$this->db->query("update cp_user set money = money + $cost, blocked = blocked - $cost, chaseMoney = chaseMoney - $cost where chaseMoney >= $cost and uid = ?", array($followInfo['uid']));
			$userRes = $this->db->affected_rows();

			if($walletRes && $userRes)
			{
				if($trans) $this->db->trans_complete();
				$result = array(
	    			'status'		=>	TRUE,
					'msg' 			=> 	'退款成功',
					'data'			=>	''
	    		);
	    		// 刷新钱包
    			$this->freshWallet($followInfo['uid']);
			}
			else
			{
				if($trans) $this->db->trans_rollback();
				$result = array(
	    			'status'		=>	FALSE,
					'msg' 			=> 	'退款失败',
					'data'			=>	''
	    		);
			}
		}
		else
		{
			if($trans) $this->db->trans_rollback();
			$result = array(
    			'status'		=>	FALSE,
				'msg' 			=> 	'用户信息获取失败',
				'data'			=>	''
    		);
		}
		return $result;
	}

	// 刷新钱包
    public function freshWallet($uid)
    {
    	$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['USER_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$uinfo = $this->cache->redis->hGet($ukey, "uname");
		if(empty($uinfo))
		{
			$this->load->model('user_model');
			$this->user_model->freshUserInfo($uid);
			return true;
		}
		else
		{
			$wallet = $this->db->query('select m.money, m.blocked, m.dispatch, m.united_points from cp_user m left join cp_user_info n on m.uid = n.uid where m.uid = ?', array($uid))->getRow();
			return $this->cache->redis->hMSet($ukey, $wallet);
		}
    }
}
