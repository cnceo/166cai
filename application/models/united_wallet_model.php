<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 合买 - 流水 - 模型层
 */
class United_Wallet_Model extends MY_Model
{
	public $status = array(
        'recharge'                   => 0, //充值-添加预付款
        'pay'                        => 1, //付款-购买彩票
        'reward'                     => 2, //奖金-奖金派送
        'drawback'                   => 3, //订单退款-订单失败返款
        'apply_for_withdraw'         => 4, //提款
        'apply_for_withdraw_succ'    => 5, //提款成功解除冻结预付款(已废弃)
        'withdraw_succ'              => 6, //申请提款成功-扣除预付款成功(已废弃)
        'apply_for_withdraw_conceal' => 7, //申请提款撤销(已废弃)
        'apply_for_withdraw_fail'    => 8, //提款失败还款
        'dispatch'                   => 9, //系统奖金派送
        'addition'                   => 10, //其他应收款项
        'transfer'                   => 11, //转帐
    	'rebate'                     => 14, //联盟返点
    	'united_refund'				 => 15, //合买返还预付款
    );

    public $concealMsg = array(
    	1	=>	'合买发起人撤单',
    	2	=>	'合买未满员撤单',
    	3	=>	'合买方案撤单',
    );

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('BetCnName');
		$this->load->model('united_order_model');
		// 获取订单状态
	    $this->orderStatus = $this->united_order_model->getStatus();
	}

	public function getStatus()
	{
		return $this->status;
	}

	// 合买发起人订单支付
    public function payUnitedOrder($uid, $orderData, $money = 0, $trans = true)
    {
    	// 事务开始
    	if($trans) $this->db->trans_start();

    	if($money > 0)
    	{
    		$payStatus = $this->payUnitedMoney($uid, $money, $orderData['orderId']);
    		$trade_no = $payStatus['trade_no'];
    		$subscribeId = $payStatus['subscribeId'];
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();
    		$status = array(
    			'code' => 400,
				'msg' => '支付金额错误',
				'data' => $orderData
    		);
    		return $status;
    	}

    	if($trade_no && $subscribeId)
    	{
    		// 扣款成功处理
        	$orderData['trade_no'] = $trade_no;
        	$orderData['subscribeId'] = $subscribeId;

        	// 新增cp_united_join认购记录
        	$joinInfo = array(
        		'orderId'			=>	$orderData['orderId'],
        		'trade_no'			=>	$orderData['trade_no'],
				'subscribeId'		=>	$orderData['subscribeId'],
				'uid'				=>	$uid,
        		'puid'				=>  $payStatus['orderInfo']['uid'],
				'lid'				=>	$payStatus['orderInfo']['lid'],
				'issue'				=>	$payStatus['orderInfo']['issue'],
				'money'				=>	$payStatus['orderInfo']['money'],
				'status'			=>	$this->orderStatus['pay'],
				'buyMoney'			=>	$payStatus['orderInfo']['buyMoney'],
				'buyPlatform'		=>	$orderData['buyPlatform'] ? $orderData['buyPlatform'] : $payStatus['orderInfo']['buyPlatform'],
				'orderType'			=>	'1'
        	);

			$joinRes = $this->united_order_model->saveJoinOrder($joinInfo);

        	// 更新cp_united_orders认购及状态
        	$pay_time = date('Y-m-d H:i:s', time());
        	$this->db->query("UPDATE cp_united_orders SET trade_no = ?, status = {$this->orderStatus['pay']}, popularity = popularity + 1, pay_time = ? WHERE orderId = ? AND status = {$this->orderStatus['create']} AND endTime > now()", array($orderData['trade_no'], $pay_time, $orderData['orderId']));
        	$unitedRes = $this->db->affected_rows();

        	// 发单统计
    		$orders = array(
    			'uid'           =>  $uid,
	            'lid'           =>  $payStatus['orderInfo']['lid'],
	            'allTimes'  	=>  '1',
	            'lastPayTime'	=>	date('Y-m-d H:i:s'),
    		);
    		$recoredRes = $this->united_order_model->recoredPlannerOrder($orders);

    		// 更新进行中
    		$isOrderRes = $this->united_order_model->updateIsOrdering($orders);

        	if($joinRes && $unitedRes && $recoredRes && $isOrderRes)
        	{
        		// 支付成功
        		if($trans) $this->db->trans_complete();
        		$this->freshWallet($uid);

        		// 触发跟单
        		$followData = array(
        			'uid'		=>	$uid,
        			'lid'		=>	$payStatus['orderInfo']['lid'],
        			'orderId'	=>	$orderData['orderId'],
        		);
        		log_message('LOG', print_r($followData, true), 'followTrigger');
        		$this->followTrigger($followData);

        		$status = array(
	    			'code' => $payStatus['code'],
					'msg' => $payStatus['msg'],
					'data' => $orderData
	    		);
        	}
        	else
        	{
        		if($trans) $this->db->trans_rollback();
        		$status = array(
	    			'code' => 302,
					'msg' => '订单支付失败，状态不满足！',
					'data' => $orderData
	    		);
        	}
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();

    		if($payStatus['code'] == '401')
    		{
    			$payStatus['code'] = '300';
    			$payStatus['msg'] = '订单支付失败，已过支付时间！';
    			$this->chase_order_model->updateExpiredStatus($orderData);
    		}
    		$status = array(
    			'code' => $payStatus['code'],
				'msg' => $payStatus['msg'],
				'data' => $orderData
    		);
    	}
    	return $status;
    }

    // 合买认购人订单支付
    public function payBuyOrder($uid, $orderData, $money = 0, $trans = true)
    {
    	// 事务开始
    	if($trans) $this->db->trans_start();

    	if($money > 0)
    	{
    		$payStatus = $this->payBuyMoney($uid, $money, $orderData['orderId']);
    		$trade_no = $payStatus['trade_no'];
    		$subscribeId = $payStatus['subscribeId'];
    		$orderInfo = $payStatus['orderInfo'];
    	}
    	else
    	{
    		$status = array(
    			'code' => 400,
				'msg' => '支付金额错误',
				'data' => $orderData
    		);
    		return $status;
    	}

    	if($trade_no && $subscribeId)
    	{
    		// 扣款成功处理
        	$orderData['trade_no'] = $trade_no;
        	$orderData['subscribeId'] = $subscribeId;

        	$this->load->model('user_model');
        	$userInfo = $this->user_model->getUserInfo($uid);
        	
        	// 新增cp_united_join认购记录
        	$joinInfo = array(
        		'orderId'			=>	$orderData['orderId'],
        		'trade_no'			=>	$orderData['trade_no'],
				'subscribeId'		=>	$orderData['subscribeId'],
				'uid'				=>	$uid,
        		'puid'				=>  $payStatus['orderInfo']['uid'],
				'lid'				=>	$payStatus['orderInfo']['lid'],
				'issue'				=>	$payStatus['orderInfo']['issue'],
				'money'				=>	$payStatus['orderInfo']['money'],
				'status'			=>	$payStatus['orderInfo']['status'],
				'buyMoney'			=>	$money,
				'buyPlatform'		=>	$orderData['buyPlatform'],
				'orderType'			=>	'2'
        	);

			$joinRes = $this->united_order_model->saveJoinOrder($joinInfo);

        	// 更新cp_united_orders认购及状态
        	$this->db->query("UPDATE cp_united_orders SET buyTotalMoney = buyTotalMoney + ?, popularity = popularity + 1 WHERE orderId = ? AND {$money} + buyTotalMoney <= money AND endTime > now()", array($money, $orderData['orderId']));
        	$updateRes = $this->db->affected_rows();

        	if($joinRes && $updateRes)
        	{
        		// 支付成功
        		if($trans) $this->db->trans_complete();
        		$this->freshWallet($uid);

        		$status = array(
	    			'code' => $payStatus['code'],
					'msg' => $payStatus['msg'],
					'data' => $orderData
	    		);
        	}
        	else
        	{
        		if($trans) $this->db->trans_rollback();
        		$status = array(
	    			'code' => 302,
					'msg' => '订单支付失败，状态不满足！',
					'data' => $orderData
	    		);
        	}
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();

    		if($payStatus['code'] == '401')
    		{
    			$payStatus['code'] = '300';
    			$payStatus['msg'] = '订单支付失败，已过支付时间！';
    			// $this->chase_order_model->updateExpiredStatus($orderData);
    		}
    		$status = array(
    			'code' => $payStatus['code'],
				'msg' => $payStatus['msg'],
				'data' => $orderData
    		);
    	}
    	return $status;
    }

    // 发起人订单扣款
	public function payUnitedMoney($uid, $cost, $orderId = 0)
	{
		$payStatus = array(
			'code' => 400,
			'trade_no' => false,
			'subscribeId' => false,
			'orderInfo' => false,
			'msg' => '支付失败'
		);

		// 用户信息锁表
		$uinfo = $this->getUserMoney($uid);

		$cost = intval($cost);
		if(!empty($orderId) && $uinfo['money'] >= $cost)
		{
			// 发起人订单行锁
			$order = $this->united_order_model->getUnitedOrderDetail($orderId);

			// 判断订单状态是否异常
			$flag = true;
			if($order['uid'] != $uid || ($order['buyMoney'] + $order['guaranteeAmount'] != $cost) || $order['status'] != $this->orderStatus['create'] || $order['endTime'] <= date('Y-m-d H:i:s'))
			{
				$payStatus = array(
					'code' => 300,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '订单支付失败，状态不满足！'
				);
				$flag = false;
			}

			if(!$flag)
			{
				return $payStatus;
			}
		}
		else
		{
			$payStatus = array(
				'code' => 301,
				'trade_no' => false,
				'subscribeId' => false,
				'orderInfo' => $order,
				'msg' => '订单支付失败，余额不足'
			);
			return $payStatus;
		}

		$content = '发起合买：合买认购' . ParseUnit($order['buyMoney'], 1) . '元，保底' . ParseUnit($order['guaranteeAmount'], 1) . '元';
		// 扣款
		$payStatus = $this->recordWallet($uid, $uinfo, $cost, $order, $content);
		
		return $payStatus;
	}

	// 认购人订单扣款
	public function payBuyMoney($uid, $cost, $orderId = 0)
	{
		$payStatus = array(
			'code' => 400,
			'trade_no' => false,
			'subscribeId' => false,
			'msg' => '支付失败'
		);

		$this->load->library("createcheck/SsqCheck");//只校验buyMoney，所以直接调用双色球的校验
		$result = $this->ssqcheck->check(array('uid' => $uid, 'orderType' => 4, 'buyMoney' => $cost/100, 'type' => 1));
		if($result['status'] == false)
		{
			return $result;
		}

		// 用户信息锁表
		$uinfo = $this->getUserMoney($uid);

		$cost = intval($cost);
		if(!empty($orderId) && $uinfo['money'] >= $cost)
		{
			// 发起人订单行锁
			$order = $this->united_order_model->getUnitedOrderDetail($orderId);
			
			if (in_array($order['status'], array(610, 620))) 
			{
				$payStatus = array(
					'code' => 300,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已撤单'
				);
				return $payStatus;
			}
			
			if ($order['endTime'] < date('Y-m-d H:i:s')) 
			{
				$payStatus = array(
					'code' => 300,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已截止'
				);
				return $payStatus;
			}
			
			// 判断订单状态是否异常
			if($order['status'] < $this->orderStatus['pay'] || $order['status'] > $this->orderStatus['draw'] || $order['money'] == $order['buyTotalMoney'])
			{
				$payStatus = array(
					'code' => 300,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '<i class="icon-font">&#xe611;</i>该合买方案已满员'
				);
				return $payStatus;
			}

			if($cost > $order['money'] - $order['buyTotalMoney'])
			{
				$payStatus = array(
					'code' => 300,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '<i class="icon-font">&#xe611;</i>该合买方案剩余金额不足'
				);
				return $payStatus;
			}
		}
		else
		{
			$payStatus = array(
				'code' => 300,
				'trade_no' => false,
				'subscribeId' => false,
				'orderInfo' => $order,
				'msg' => '订单支付失败，余额不足'
			);
			return $payStatus;
		}

		$content = '参与合买：合买认购' . ParseUnit($cost, 1) . '元';
		// 扣款
		$payStatus = $this->recordWallet($uid, $uinfo, $cost, $order, $content);
		
		return $payStatus;
	}

	// 扣款逻辑
	private function recordWallet($uid, $uinfo, $cost, $order, $content = '')
	{
		if($uinfo['money'] >= $cost)
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
	            'uid'       =>	$uid,
	            'money'     =>	$cost,
	            'ctype'     =>	$this->status['pay'],
	            'trade_no'  =>	$trade_no,
	            'mark' 		=> 	'0',
	            'umoney'    =>	($uinfo['money'] - $cost),
	            'must_cost' =>	$must_cost,
	            'dispatch'  =>	$dispatch,
	            'status'    =>  '3',	// 合买流水
	            'additions' =>	(empty($order['lid']) ? 0 : $order['lid']),
	            'orderId'   =>	$order['orderId'],
	            'subscribeId'   =>	$subscribeId,	// 流水关联认购订单号
	            'content'   =>  $content,
	        );
			// 记录扣款流水
			$walletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
			// 扣款
			$this->db->query("update cp_user set money = money - $cost,
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where money >= $cost and uid = ?", array($uid));
			$userRes = $this->db->affected_rows();
			// 入总账流水
			$this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false);
			
			if($walletRes && $userRes && $capitalRes) 
			{
				$payStatus = array(
					'code' => 200,
					'trade_no' => $trade_no,
					'subscribeId' => $subscribeId,
					'orderInfo' => $order,
					'msg' => '订单扣款成功！'
				);
			}
			else 
			{
				$payStatus = array(
					'code' => 400,
					'trade_no' => false,
					'subscribeId' => false,
					'orderInfo' => $order,
					'msg' => '订单扣款失败！'
				);
			}
		}
		else 
		{
			$payStatus = array(
				'code' => 400,
				'trade_no' => false,
				'subscribeId' => false,
				'orderInfo' => $order,
				'msg' => '订单支付失败,余额不足！'
			);
		}
		return $payStatus;
	}

	// 单笔认购流水退款
	public function refunds($orderInfo, $failType, $tranc = true)
	{
		// 流水号
		$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

		if($tranc)
        {
        	// 开启事务
            $this->db->trans_start();
        }

		$this->db->query("update cp_user m
		join cp_wallet_logs w on m.uid = w.uid 
		set m.money = m.money + w.money, m.must_cost = m.must_cost + w.must_cost,
		m.dispatch = m.dispatch + w.dispatch where w.trade_no = ?", array($orderInfo['trade_no']));
		$re1 = $this->db->affected_rows();

		// 退款备注
		$content = ($this->concealMsg[$failType]) ? $this->concealMsg[$failType] : '';

		$walletData = $this->db->query('select u.uid, w.money as allMoney, w.must_cost, w.dispatch, u.lid, u.subscribeId, n.money from cp_united_join u join cp_user n on u.uid = n.uid join cp_wallet_logs w on u.trade_no = w.trade_no where u.subscribeId = ? AND w.ctype = 1', array($orderInfo['subscribeId']))->getRow();

		$wallet_log = array(
            'uid'       =>	$walletData['uid'],
            'mark'      =>	'1',
            'money'     =>	$walletData['allMoney'],
            'must_cost' =>	$walletData['must_cost'],
            'dispatch'  =>	$walletData['dispatch'], 
            'ctype'     =>	$this->status['drawback'],
            'additions'	=>	$walletData['lid'],
            'trade_no'  =>	$trade_no,
            'orderId'	=>	$orderInfo['orderId'],
            'subscribeId'	=>	$walletData['subscribeId'],
            'status'    =>  '3',	// 合买流水  
            'umoney'    =>	$walletData['money'],
            'content'	=>	$content,
        );

        // 记录流水
        $re2 = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

		$walletInfo = $this->getWalletLog($orderInfo['trade_no']);

        // 总账
		$this->load->model('capital_model');
		$capitalRes = $this->capital_model->recordCapitalLog(1, $trade_no, 'ticket_fail', $walletInfo['money'], '2', false);

		if($re1 && $re2 && $capitalRes)
		{
			if($tranc)
			{
				$this->db->trans_complete();
			}
			// 刷新钱包
            $this->freshWallet($orderInfo['uid']);	
			return true;
		}
		else
		{
			if($tranc)
			{
				$this->db->trans_rollback();
			}
			return false;
		}
	}

	// 保底退款
	public function handleGuaranteeRefund($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();
        // 行锁
        $order = $this->united_order_model->getUnitedOrderDetail($orderInfo['orderId']);

        // 认购大于等于100%，且未处理
        if(!empty($order) && $order['status'] <= $this->orderStatus['draw'] && $order['guaranteeAmount'] > 0 && $order['buyTotalMoney'] + $order['guaranteeAmount'] >= $order['money'] && ($order['cstate'] & 2) == 0 && ($order['cstate'] & 4) == 0)
        {
        	// 退款金额
        	$refundMoney = $order['buyTotalMoney'] + $order['guaranteeAmount'] - $order['money'];

        	// 保底转认购金额
        	$transMoney = $order['guaranteeAmount'] - $refundMoney;

        	// 保底退款及转认购的流水号
			$refund_trade_no = $this->tools->getIncNum('UNIQUE_KEY');

        	// 可能不存在退款，即全部转为了认购
        	if($refundMoney > 0)
        	{
        		$refundRes = $this->guaranteeRefund($order, $refund_trade_no, $refundMoney, $transMoney, false);

        		// 更新保底退款状态
        		$this->db->query("update cp_united_orders set cstate = cstate ^ 2 where orderId = ? and cstate & 2 = 0", array($order['orderId']));
        		$unitedRes = $this->db->affected_rows();

        		$refundMoneyRes = $refundRes && $unitedRes;
        	}
        	else
        	{
        		$refundMoneyRes = true;
        	}
        	
        	// 存在保底转认购金额
        	if($transMoney > 0)
        	{
        		$order['puid'] = $order['uid'];
        		$transRes = $this->transBuyMoney($order, $refund_trade_no, $transMoney, $orderType = 3, false);

        		// 更新cp_united_orders发起人实际保底金额
        		$this->db->query("update cp_united_orders set actualguranteeAmount = ? where orderId = ?", array($transMoney, $order['orderId']));
        		$transBuyStatusRes = $this->db->affected_rows();

        		// 保底转认购状态处理
        		$transStatusRes = $this->handleTransStatus($order, $cstate = 4);
        		$handleRes = $refundMoneyRes && $transRes && $transBuyStatusRes && $transStatusRes;
        	}
        	else
        	{
        		$handleRes = $refundMoneyRes;
        	}
        	
        	if($handleRes)
        	{
        		$this->db->trans_complete();
				// 刷新钱包
            	$this->freshWallet($order['uid']);
        	}
        	else
        	{
        		$this->db->trans_rollback();
        	}
        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 保底退款
	public function guaranteeRefund($orderInfo, $refund_trade_no, $money, $transMoney = 0, $tranc = true)
	{
		if($tranc)
        {
        	// 开启事务
            $this->db->trans_start();
        }

        $userInfo = $this->getUserMoney($orderInfo['uid']);

        $walletInfo = $this->getWalletLog($orderInfo['trade_no']);
 		
        $bmoney = $walletInfo['money'] - $walletInfo['must_cost'] - $walletInfo['dispatch'];
        if($money > $bmoney)
        {
        	// 先扣must_cost
        	if($money - $bmoney >= $walletInfo['must_cost'])
        	{
        		// 再扣dipatch
        		if($money - $bmoney - $walletInfo['must_cost'] < $walletInfo['dispatch'])
        		{
        			$dispatch = $money - $bmoney - $walletInfo['must_cost'];
        			$must_cost = $walletInfo['must_cost'];
        		}
        		else
        		{
        			$dispatch = $walletInfo['dispatch'];
        			$must_cost = $walletInfo['must_cost'];
        		}
        	}
        	else
        	{
        		$dispatch = 0;
        		$must_cost = $money - $bmoney;
        	}
        }
        else
        {
        	$dispatch = 0;
        	$must_cost = 0;
        }

        $wallet_log = array(
            'uid'       =>	$orderInfo['uid'],
            'money'     =>	$money,
            'ctype'     =>	$this->status['united_refund'],
            'trade_no'  =>	$refund_trade_no,
            'additions'	=>	$orderInfo['lid'],
            'umoney'    =>	$userInfo['money'] + $money,
            'must_cost' =>	$must_cost,
            'dispatch'  =>	$dispatch,
            'mark'      =>	'1',
            'status'    =>  '3',	// 合买流水
            'orderId'   =>	$orderInfo['orderId'],
            'subscribeId'	=>	$orderInfo['subscribeId'],	// 流水关联认购订单号
            'content'   =>	'保底转认购' . ParseUnit($transMoney, 1) . '元，退款' . ParseUnit($money, 1) . '元',
        );

        // 记录流水
        $walletRes = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
		values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

		// 用户余额扣款
        $this->db->query("update cp_user set money = money + {$money}, must_cost = must_cost + {$must_cost}, dispatch = dispatch + {$dispatch} where uid = ?", array($orderInfo['uid']));
        $userRes = $this->db->affected_rows();

        // 总账记录流水
        $this->load->model('capital_model');
        $capitalRes = $this->capital_model->recordCapitalLog('1', $refund_trade_no, 'united_refund', $money, '2', $tranc = FALSE);

        if($walletRes && $userRes && $capitalRes)
        {
        	if($tranc)
			{
				$this->db->trans_complete();
				// 刷新钱包
            	$this->freshWallet($orderInfo['uid']);
			}	
			return true;
        }
        else
        {
        	if($tranc)
			{
				$this->db->trans_rollback();
			}
			return false;
        }
	}

	// 保底转认购
	public function handleGuaranteeTrans($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();
        // 行锁
        $order = $this->united_order_model->getUnitedOrderDetail($orderInfo['orderId']);

        // 认购大于等于100%，且未处理
        if(!empty($order) && $order['buyTotalMoney'] + $order['guaranteeAmount'] < $order['money'] && ($order['cstate'] & 2) == 0 && ($order['cstate'] & 8) == 0)
        {
        	$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
        	// 发起人保底转认购
        	if($order['guaranteeAmount'] > 0 && ($order['cstate'] & 4) == 0)
        	{
        		$order['puid'] = $order['uid'];
        		$transBuyRes = $this->transBuyMoney($order, $trade_no, $order['guaranteeAmount'], $orderType = 3, false);

        		// 更新cp_united_orders发起人实际保底金额
        		$this->db->query("update cp_united_orders set actualguranteeAmount = ? where orderId = ?", array($order['guaranteeAmount'], $order['orderId']));
        		$transBuyStatusRes = $this->db->affected_rows();

        		// 用户保底转认购状态处理
        		$transStatusRes = $this->handleTransStatus($order, $cstate = 4);
        		$transRes = $transBuyRes && $transBuyStatusRes && $transStatusRes;
        	}
        	else
        	{
        		$transRes = true;
        	}

        	// 网站是否需要认购介入
        	$webMoney = $order['money'] - $order['buyTotalMoney'] - $order['guaranteeAmount'];
        	if($webMoney > 0)
        	{
        		$webOrder = array(
        			'orderId'	=>	$order['orderId'],
        			'uid'		=>	'0',	// 网站uid
        			'puid'		=>	$order['uid'],
        			'lid'		=>	$order['lid'],
        			'issue'		=>	$order['issue'],
        			'money'		=>	$order['money'],
        			'buyPlatform' => '0',
        			'status'	=>	$order['status'],
        		);

        		$web_trade_no = $this->tools->getIncNum('UNIQUE_KEY');
        		$webBuyRes = $this->transBuyMoney($webOrder, $web_trade_no, $webMoney, $orderType = 4, false);

        		// 更新cp_united_orders网站保底金额
        		$this->db->query("update cp_united_orders set webguranteeAmount = ? where orderId = ?", array($webMoney, $order['orderId']));
        		$unitedRes = $this->db->affected_rows();

        		// 入总账流水
				$this->load->model('capital_model');
				// 购彩
				$capitalRes1 = $this->capital_model->recordCapitalLog(1, $web_trade_no, 'pay', $webMoney, '1', false);
				// 成本
				$web_pay_trade_no = $this->tools->getIncNum('UNIQUE_KEY');
				$capitalRes2 = $this->capital_model->recordCapitalLog(2, $web_pay_trade_no, 'united_pay', $webMoney, '2', false);

				// 网站保底转认购状态处理
        		$transStatusRes = $this->handleTransStatus($order, $cstate = 8);

				$webTransRes = $webBuyRes && $capitalRes1 && $capitalRes2 && $transStatusRes;
        	}
        	else
        	{
        		$webTransRes = true;
        	}

        	if($transRes && $webTransRes)
        	{
        		$this->db->trans_complete();
        	}
        	else
        	{
        		$this->db->trans_rollback();
        	}
        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 保底转认购
	public function transBuyMoney($orderInfo, $trade_no, $money, $orderType = 3, $tranc = true)
	{
		if($tranc)
        {
        	// 开启事务
            $this->db->trans_start();
        }

        // 新增认购记录
    	$unitedInfo = array(
    		'orderId'			=>	$orderInfo['orderId'],
    		'trade_no'			=>	$trade_no,
			'subscribeId'		=>	$this->tools->getIncNum('UNIQUE_KEY'),
			'uid'				=>	$orderInfo['uid'],
			'puid'				=>	$orderInfo['puid'],
			'lid'				=>	$orderInfo['lid'],
			'issue'				=>	$orderInfo['issue'],
			'money'				=>	$orderInfo['money'],
			'status'			=>	$orderInfo['status'],
			'buyMoney'			=>	$money,
			'buyPlatform'		=>	$orderInfo['buyPlatform'],
			'orderType'			=>	$orderType		// 3 发起人保底转认购 4 网站保底转认购
    	);
    	
		$unitedRes = $this->united_order_model->saveJoinOrder($unitedInfo);

		// 更新cp_united_orders认购，状态不在此处理
    	$this->db->query("UPDATE cp_united_orders SET buyTotalMoney = buyTotalMoney + ?, popularity = popularity + 1 WHERE orderId = ?", array($money, $orderInfo['orderId']));
    	$updateRes = $this->db->affected_rows();

    	if($unitedRes && $updateRes)
    	{
    		if($tranc)
			{
				$this->db->trans_complete();
			}
			return true;
    	}
    	else
    	{
    		if($tranc)
			{
				$this->db->trans_rollback();
			}
			return false;
    	}
	}

	// 更新保底转认购状态
	public function handleTransStatus($orderInfo, $cstate = 4)
	{
		$this->db->query("UPDATE cp_united_orders SET cstate = (cstate ^ {$cstate}) WHERE orderId = ?", array($orderInfo['orderId']));
		$transStatusRes = $this->db->affected_rows();
		return $transStatusRes;
	}

	// 返点
	public function handleRebate($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();
        // 行锁
        $order = $this->united_order_model->getUnitedOrderDetail($orderInfo['orderId']);

        // 确保不是尚未处理网站保底转认购的订单
        if(!empty($order) && $order['status'] >= $this->orderStatus['draw'] && !in_array($order['status'], array($this->orderStatus['concel'], $this->orderStatus['revoke_by_user'], $this->orderStatus['revoke_by_system'])) && $order['buyTotalMoney'] + $order['guaranteeAmount'] >= $order['money'] && $order['webguranteeAmount'] == 0 && ($order['cstate'] & 8) == 0)
        {
        	// 返点逻辑处理
        	$this->load->model('activity_model');
        	$activity_ids = $this->activity_model->checkRebateByUid($orderInfo['uid']);

        	if($activity_ids)
        	{
        		$this->db->query("UPDATE cp_orders SET activity_ids = activity_ids | ? WHERE orderId = ? AND orderType = 4", array($activity_ids, $orderInfo['orderId']));
        		$updateRes = $this->db->affected_rows();
        	}
        	else
        	{
        		$updateRes = true;
        	}
        	
        	// 更新cp_united_orders
        	$this->db->query("UPDATE cp_united_orders SET cstate = cstate ^ 16 WHERE orderId = ? AND (cstate & 16 = 0) AND status >= {$this->orderStatus['draw']}", array($orderInfo['orderId']));
        	$unitedRes = $this->db->affected_rows();

        	if($updateRes && $unitedRes)
        	{
        		$this->db->trans_complete();
        	}
        	else
        	{
        		$this->db->trans_rollback();
        	}

        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 系统派奖
	public function sendPrize($sdate, $edate)
	{
		$sql = "SELECT orderId, trade_no, uid, lid, issue, money, buyMoney, buyTotalMoney, commissionRate, status, guaranteeAmount, webguranteeAmount, openStatus, buyPlatform, commission, orderBonus, orderMargin, margin, popularity, isTop, openEndtime, ForecastBonusv, endTime, refund_time, sendprize_time, my_status, cstate, bet_flag, created 
        FROM cp_united_orders 
        WHERE modified >= '{$sdate}' AND modified <= '{$edate}' 
        AND ((status = {$this->orderStatus['win']} AND my_status = 0 AND sendprize_time = '0000-00-00 00:00:00' and orderBonus = orderMargin and orderBonus < 5000000) 
        OR (status = {$this->orderStatus['win']} AND my_status = 3 AND sendprize_time = '0000-00-00 00:00:00') 
        OR (status = {$this->orderStatus['concel']} AND my_status = 0 AND refund_time = '0000-00-00 00:00:00')) ORDER BY orderId ASC";
        $info = $this->db->query($sql)->getAll();

		while(!empty($info))
		{
			foreach ($info as $order) 
			{
				if($order['status'] == $this->orderStatus['win'])
				{
					$this->handleSendPrize($order);
				}
				elseif($order['status'] == $this->orderStatus['concel'])
				{
					$this->handleFailed($order);
				}
			}
			$info = $this->db->query($sql)->getAll();
		}
		return true;
	}

	// 派奖
	public function handleSendPrize($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();

        // 行锁
        $order = $this->united_order_model->getUnitedOrderDetail($orderInfo['orderId']);
        if(!empty($order) && $order['status'] == $this->orderStatus['win'] && (($order['my_status'] == 0 && $order['orderBonus'] == $order['orderMargin']) || $order['my_status'] == 3))
        {
        	// 用户列表
        	$userArr = array();
        	$parentMargin = 0;
        	// 发起人保底转认购奖金
        	$guarantee = 0;

        	// 发起人用户信息锁表
			$uinfo = $this->getUserMoney($order['uid']);
        	// 佣金结算
        	if($order['commission'] > 0)
        	{
        		$cUserRes = $this->db->query("UPDATE cp_user SET money = money + ?, must_cost = must_cost +  ?, dispatch = dispatch + ? WHERE uid = ?", array($order['commission'], 0, 0, $order['uid']));

        		$wallet_log = array(
		            'uid'       =>	$orderInfo['uid'],
		            'money'     =>	$order['commission'],
		            'ctype'     =>	$this->status['reward'],
		            'additions'	=>	$orderInfo['lid'],
		            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
		            'umoney'    =>	($uinfo['money'] + $order['commission']),
		            'must_cost' =>	0,
		            'dispatch'  =>	0,
		            'mark'      =>	'1',
		            'status'    =>  '3',	// 合买流水
		            'orderId'   =>	$orderInfo['orderId'],
		            'subscribeId'   =>	$orderInfo['subscribeId'],	// 流水关联认购订单号
		            'content'	=>	BetCnName::getCnName($order['lid']) . '合买盈利佣金',
		        );

		        $cWalletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created) values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

		        $commissionRes = $cUserRes && $cWalletRes;
        	}
        	else
        	{
        		$commissionRes = true;
        	}

        	// 认购人结算（包含保底转认购）
			$sql = "SELECT orderId, trade_no, subscribeId, lid, issue, money, uid, puid, buyMoney, status, buyPlatform, orderType, margin, my_status, cstate, created FROM cp_united_join WHERE orderId = ? ORDER BY created ASC";
			$buyOrders = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

			if(!empty($buyOrders))
			{
				$orderCounts = count($buyOrders);
				$count = 0;
				foreach ($buyOrders as $buyOrder) 
				{
					$addInfo = array(
						'uid' 		=>	$buyOrder['uid'],
						'money' 	=>	$buyOrder['margin'],
						'lid'		=>	$buyOrder['lid'],
						'must_cost'	=>	0,
						'dispatch'	=>	0,
						'orderId'	=>	$buyOrder['orderId'],
						'subscribeId'	=>	$buyOrder['subscribeId'],
						'trade_no'  =>  $buyOrder['trade_no'],
						'content'	=>	BetCnName::getCnName($order['lid']) . '合买奖金',
					);

					// 按类别派奖
					switch ($buyOrder['orderType']) 
					{
						case '1':
							$parentMargin = $buyOrder['margin'];
							$addRes = true;
							break;
						case '2':
							$addRes = $this->addMoney($addInfo, false);
							break;
						case '3':
							$guarantee += $buyOrder['margin'];
							$addRes = true;
							break;
						case '4':
							// 网站认购派奖
							$addRes = $this->addWebMoney($addInfo);
							break;
						default:
							$addRes = false;
							break;
					}
					
					if($addRes)
					{
						if(!empty($buyOrder['uid']))
						{
							array_push($userArr, $buyOrder['uid']);
						}
						$count ++;
					}
					else
					{
						// 退款失败日志
						log_message('LOG', "认购派奖失败: " . json_encode($buyOrder), 'united_order');
					}
				}

				if($orderCounts == $count)
				{
					$buyRes = true;
				}
				else
				{
					$buyRes = false;
				}
			}
			else
			{
				$buyRes = true;
			}

        	// 发起人结算（1和3类型）
        	$buyMargin = $parentMargin + $guarantee;
        	$wallet_log = array(
	            'uid'       =>	$orderInfo['uid'],
	            'money'     =>	$buyMargin,
	            'ctype'     =>	$this->status['reward'],
	            'additions'	=>	$orderInfo['lid'],
	            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
	            'umoney'    =>	($uinfo['money'] + $buyMargin + $order['commission']),
	            'must_cost' =>	0,
	            'dispatch'  =>	0,
	            'mark'      =>	'1',
	            'status'    =>  '3',	// 合买流水
	            'orderId'   =>	$orderInfo['orderId'],
	            'subscribeId'   =>	$orderInfo['subscribeId'],	// 流水关联认购订单号
	            'content'	=>	BetCnName::getCnName($order['lid']) . '合买奖金',
	        );

	        $buyWalletRes = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created) values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

	        // 方案盈利积分
	        $winMoney = $order['orderMargin'] - $order['money'];
	        $handlePointsRes = $this->calWinLevel($orderInfo['uid'], $winMoney);

        	// 更新钱包信息
        	$this->db->query("UPDATE cp_user SET money = money + ?, must_cost = must_cost +  ?, dispatch = dispatch + ? WHERE uid = ?", array($buyMargin, 0, 0, $orderInfo['uid']));
        	$buyUserRes = $this->db->affected_rows();

        	// 刷新发起人钱包
        	if(!empty($orderInfo['uid']))
			{
				array_push($userArr, $orderInfo['uid']);
			}
    		$this->freshWallet($orderInfo['uid']);

        	// 更新派奖状态
        	$curTime = date('Y-m-d H:i:s');
        	$my_status = $orderInfo['my_status'] ? $orderInfo['my_status'] : 1;

        	// cp_united_orders
        	$this->db->query("UPDATE cp_united_orders SET my_status = ?, sendprize_time = ?, united_points = ? WHERE orderId = ? AND status = {$this->orderStatus['win']}", array($my_status, $curTime, $handlePointsRes['points'], $orderInfo['orderId']));
        	$unitedRes = $this->db->affected_rows();

        	// cp_united_join
        	$this->db->query("UPDATE cp_united_join SET my_status = ? WHERE orderId = ? AND status = {$this->orderStatus['win']}", array($my_status, $orderInfo['orderId']));

        	// cp_orders
        	$this->db->query("UPDATE cp_orders SET my_status = ?, sendprize_time = ? WHERE orderId = ? AND status = {$this->orderStatus['win']} AND margin > 0 AND sendprize_time = '0000-00-00 00:00:00'", array($my_status, $curTime, $orderInfo['orderId']));
        	$orderRes = $this->db->affected_rows();

        	// 记录中奖
	        $winRes = $this->db->query("insert cp_orders_win(uid, userName, orderId, trade_no, money, status, bonus, margin, my_status, orderType, created) select uid, userName, orderId, trade_no, money, status, bonus, margin, '0', orderType, now() from cp_orders WHERE orderId = ?", array($orderInfo['orderId']));

	        // 统计汇总
    		$orderData = array(
    			'uid'           =>  $orderInfo['uid'],
	            'lid'           =>  $orderInfo['lid'],
	            'winningTimes'  =>  '1',
	            'bonus'      	=>  $orderInfo['orderBonus'],	// 大订单税前总额
	            'united_points'	=>	$handlePointsRes['points'],	// 战绩
    		);
    		$recoredRes = $this->united_order_model->recoredPlannerOrder($orderData);

        	if($commissionRes && $buyRes && $buyWalletRes && $buyUserRes && $unitedRes && $orderRes && $recoredRes && $handlePointsRes['status'])
        	{
        		$this->db->trans_complete();
        	}
        	else
        	{
        		$this->db->trans_rollback();
        		// 回滚钱包
        		$userArr = array_unique($userArr);
				if(!empty($userArr))
				{
					foreach ($userArr as $uid) 
					{
						// 刷新钱包
        				$this->freshWallet($uid);
					}
				}
        	}
        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 派送奖金
	public function addMoney($orderInfo, $tranc = true)
	{
        if($orderInfo['money'] > 0)
        {
        	if($tranc)
	        {
	        	// 开启事务
	            $this->db->trans_start();
	        }

	        // 认购人用户信息锁表
			$uinfo = $this->getUserMoney($orderInfo['uid']);

			$this->db->query("UPDATE cp_user SET money = money + ?, must_cost = must_cost +  ?, dispatch = dispatch + ? WHERE uid = ?", array($orderInfo['money'], $orderInfo['must_cost'], $orderInfo['dispatch'], $orderInfo['uid']));
			$res1 = $this->db->affected_rows();

			$wallet_log = array(
	            'uid'       =>	$orderInfo['uid'],
	            'money'     =>	$orderInfo['money'],
	            'ctype'     =>	$this->status['reward'],
	            'additions'	=>	$orderInfo['lid'],
	            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
	            'umoney'    =>	($uinfo['money'] + $orderInfo['money']),
	            'must_cost' =>	$orderInfo['must_cost'],
	            'dispatch'  =>	$orderInfo['dispatch'],
	            'mark'      =>	'1',
	            'status'    =>  '3',	// 合买流水
	            'orderId'   =>	$orderInfo['orderId'],
	            'subscribeId'   =>	$orderInfo['subscribeId'],	// 流水关联认购订单号
	            'content'   =>	$orderInfo['content'] ? $orderInfo['content'] : '',
	        );
			$res2 = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created) values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			if($res1 && $res2)
			{
				if($tranc)
		        {
		            $this->db->trans_complete();
		        }
		        // 刷新钱包
		        $this->freshWallet($orderInfo['uid']);
		        return true;
			}
			else
			{
				if($tranc)
		        {
		            $this->db->trans_rollback();
		        }
		        return false;
			}
        }
        else
        {
        	return true;
        }
	}

	// 网站认购派奖
	public function addWebMoney($orderInfo)
	{
		if($orderInfo['money'] > 0)
		{
			// 入总账流水
			$this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(2, $orderInfo['trade_no'], 'united_awards', $orderInfo['money'], '1', false);
			return true;
		}
		else
		{
			return true;
		}
	}

	// 出票失败退款
	public function handleFailed($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();

        // 行锁
        $order = $this->united_order_model->getUnitedOrderDetail($orderInfo['orderId']);

        if(!empty($order) && $order['status'] == $this->orderStatus['concel'] && $order['refund_time'] == '0000-00-00 00:00:00')
        {
        	// 用户列表
        	$userArr = array();
        	// 发起人保底转认购奖金
        	$buyMoney = 0;

        	// 认购人退款（可能包含网站保底）
        	$sql = "SELECT orderId, trade_no, subscribeId, lid, issue, money, uid, puid, buyMoney, status, buyPlatform, orderType, margin, my_status, cstate, created FROM cp_united_join WHERE orderId = ? AND orderType != 1 ORDER BY created ASC";
			$buyOrders = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

			if(!empty($buyOrders))
			{
				$orderCounts = count($buyOrders);
				$count = 0;
				foreach ($buyOrders as $buyOrder) 
				{
					// 按类别退款
					switch ($buyOrder['orderType']) 
					{
						case '2':
							$addRes = $this->refunds($buyOrder, 3, false);
							break;
						case '3':
							// 保底转认购退款流水
							$buyMoney += $buyOrder['buyMoney'];
							$addRes = true;
							break;
						case '4':
							// 网站认购退款
							$addRes = $this->webRefunds($buyOrder);
							break;
						default:
							$addRes = false;
							break;
					}

					if($addRes)
					{
						// 网站uid=0不计入
						if(!empty($buyOrder['uid']))
						{
							array_push($userArr, $buyOrder['uid']);
						}
						$count ++;
					}
					else
					{
						// 退款失败日志
						log_message('LOG', "出票失败退款失败: " . json_encode($buyOrder), 'united_order');
					}
				}

				if($orderCounts == $count)
				{
					$refundsRes = true;
				}
				else
				{
					$refundsRes = false;
				}
			}
			else
			{
				$refundsRes = true;
			}

			// 发起人实际退款（可能包含保底转认购）
			// 可能在endtime之前未执行保底退款，即全额退
			if($order['guaranteeAmount'] > 0 && ($order['cstate'] & 2) == 0)
			{
				$refundMoney = $orderInfo['buyMoney'] + $order['guaranteeAmount'];
			}
			else
			{
				$refundMoney = $orderInfo['buyMoney'] + $buyMoney;
			}

			$parentRes = $this->parentRefunds($orderInfo, $refundMoney);

			if(!empty($orderInfo['uid']))
			{
				array_push($userArr, $orderInfo['uid']);
			}	

			// 更新退款状态
			$curTime = date('Y-m-d H:i:s');
        	// cp_united_orders
        	$this->db->query("UPDATE cp_united_orders SET refund_time = ? WHERE orderId = ? AND status = {$this->orderStatus['concel']}", array($curTime, $orderInfo['orderId']));
        	$unitedRes = $this->db->affected_rows();

        	// cp_orders
        	$this->db->query("UPDATE cp_orders SET sendprize_time = ? WHERE orderId = ?", array($curTime, $orderInfo['orderId']));
        	$orderRes = $this->db->affected_rows();

			if($refundsRes && $parentRes && $unitedRes && $orderRes)
			{
				$this->db->trans_complete();
			}
			else
			{
				$this->db->trans_rollback();
				// 刷新钱包
        		$userArr = array_unique($userArr);
				if(!empty($userArr))
				{
					foreach ($userArr as $uid) 
					{
						// 刷新钱包
        				$this->freshWallet($uid);
					}
				}
			}

        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 网站保底退款
	public function webRefunds($orderInfo)
	{
		$capitalRes = true;
		if($orderInfo['buyMoney'] > 0)
		{
			// 总账处理
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			$this->load->model('capital_model');
			// 购彩
			$capitalRes1 = $this->capital_model->recordCapitalLog(1, $trade_no, 'ticket_fail', $orderInfo['buyMoney'], '2', false);
			// 成本
			$buy_trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			$capitalRes2 = $this->capital_model->recordCapitalLog(2, $buy_trade_no, 'united_web_refund', $orderInfo['buyMoney'], '1', false);
			$capitalRes = $capitalRes1 && $capitalRes2;
		}
		return $capitalRes;
	}

	// 发起人出票失败退款
	public function parentRefunds($orderInfo, $refundMoney = 0)
	{
		$refundData = array(
			'money'		=>	0,
			'must_cost'	=>	0,
			'dispatch'	=>	0
		);

		if(($orderInfo['cstate'] & 2) != 0)
		{
			// 查询保底退款流水
			$refundInfo = $this->getRefundsWalletLog($orderInfo['orderId']);
			if(!empty($refundInfo))
			{
				$refundData = array(
					'money'		=>	$refundInfo['money'],
					'must_cost'	=>	$refundInfo['must_cost'],
					'dispatch'	=>	$refundInfo['dispatch']
				);
			}
		}

		$userInfo = $this->getUserMoney($orderInfo['uid']);

		// 发起认购流水
		$parentInfo = $this->getWalletLog($orderInfo['trade_no']);

		$money = $parentInfo['money'] - $refundData['money'];
		$must_cost = $parentInfo['must_cost'] - $refundData['must_cost'];
		$dispatch = $parentInfo['dispatch'] - $refundData['dispatch'];

		if($refundMoney == $money && $money > 0 && $must_cost >= 0 && $dispatch >= 0)
		{
			$wallet_log = array(
	            'uid'       =>	$orderInfo['uid'],
	            'money'     =>	$money,
	            'ctype'     =>	$this->status['drawback'],
	            'additions'	=>	$orderInfo['lid'],
	            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
	            'umoney'    =>	($userInfo['money'] + $money),
	            'must_cost' =>	$must_cost,
	            'dispatch'  =>	$dispatch,
	            'mark'      =>	'1',
	            'status'    =>  '3',	// 合买流水
	            'orderId'   =>	$orderInfo['orderId'],
	            'subscribeId'   =>	$orderInfo['subscribeId'],	// 流水关联认购订单号
	            'content'   =>	'合买方案撤单',
	        );

	        // 记录流水
	        $walletRes = $this->db->query("insert cp_wallet_logs(" . implode(',', array_keys($wallet_log)) . ', created)
			values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);

			// 用户余额扣款
	        $this->db->query("update cp_user set money = money + {$money}, must_cost = must_cost + {$must_cost}, dispatch = dispatch + {$dispatch} where uid = ?", array($orderInfo['uid']));
	        $userRes = $this->db->affected_rows();

	        // 总账
	        $this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(1, $wallet_log['trade_no'], 'ticket_fail', $money, '2', false);

			if($walletRes && $userRes && $capitalRes)
			{
				$this->freshWallet($orderInfo['uid']);
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	// 计算方案盈利等级
	public function calWinLevel($uid, $winMoney = 0)
	{
		$points = 0;
		if($winMoney >= 50000000)
		{
			// 皇冠
			$points = 1000;
		}
		elseif($winMoney >= 10000000)
		{
			// 太阳
			$points = 100;
		}
		elseif($winMoney >= 500000)
		{
			// 月亮
			$points = 10;
		}
		elseif($winMoney >= 50000)
		{
			// 星星
			$points = 1;
		}

		$handleRes = array(
			'status' => true,
			'points' => $points
		);

		return $handleRes;
	}

	// 充值自动支付
	public function autoPay($trade_no, $tranc = true)
	{
		if($trade_no)
		{
			$orders = $this->db->query("select n.uid, n.money, n.buyMoney, n.guaranteeAmount, n.orderId 
			from cp_wallet_logs m 
			join cp_united_orders n on m.orderId = n.orderId 
			where m.trade_no = ? and n.status = 0 
			and m.ctype = {$this->status['recharge']} and m.status = 3", array($trade_no))->getRow();
			if(!empty($orders))
			{ 
				$orderData = array(
					'orderId' => $orders['orderId'], 
				);
				$buyMoney = $orders['buyMoney'] + $orders['guaranteeAmount'];
				$this->payUnitedOrder($orders['uid'], $orderData, $buyMoney, $tranc);
			}
		}
	}

	// 获取流水记录
	public function getWalletLog($trade_no)
	{
		return $this->db->query("SELECT uid, money, must_cost, dispatch, mark, ctype, additions, trade_no, orderId, umoney, status 
            FROM cp_wallet_logs
            WHERE trade_no = ?", array($trade_no))->getRow();
	}

	// 查询保底退款流水
	public function getRefundsWalletLog($orderId)
	{
		return $this->db->query("SELECT uid, money, must_cost, dispatch, mark, ctype, additions, trade_no, orderId, umoney, status 
            FROM cp_wallet_logs
            WHERE orderId = ? AND ctype = {$this->status['united_refund']}", array($orderId))->getRow();
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
			$wallet = $this->db->query('select m.money, m.blocked, m.dispatch from cp_user m left join cp_user_info n on m.uid = n.uid where m.uid = ?', array($uid))->getRow();
			return $this->cache->redis->hMSet($ukey, $wallet);
		}
    }

	// 获得余额
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch FROM cp_user WHERE uid = ? for update', array($uid))->getRow();
	}

	// 触发跟单任务列表
	public function followTrigger($followData)
	{
		if(!empty($followData))
		{
			$this->load->model('follow_wallet_model');
			$this->follow_wallet_model->pushFollowTask($followData);
		}
	}
}
