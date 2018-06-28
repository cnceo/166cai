<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 追号 - 钱包流水 - 模型层
 * @date:2015-12-01
 */
class Chase_Wallet_Model extends MY_Model
{
	// 追号订单状态
	public $ctype = array(
		'recharge' => 0, //充值-添加预付款
		'pay' => 1, //付款-购买彩票
		'reward' => 2, //奖金-奖金派送
		'drawback' => 3, //订单退款-订单失败返款
		'apply_for_withdraw' => 4, //申请提款-冻结预付款
		'apply_for_withdraw_succ' => 5, //提款成功解除冻结预付款
		'withdraw_succ' => 6, //申请提款成功-扣除预付款成功
		'apply_for_withdraw_conceal' => 7, //申请提款撤销
		'apply_for_withdraw_fail' => 8, //打款失败解除冻结预付款
		'dispatch' => 9, //系统奖金派送
		'addition' => 10, //其他应收款项
		'transfer' => 11, //转帐
		'chase_freez' => 12, //追号预付款冻结
		'chase_conceal' => 13, //追号方案取消-预付款解冻
	);

	public function __construct() 
	{
		parent::__construct();
		$this->load->model('chase_order_model');
		$this->lcfgs = $this->getLotteryConfig();
	}
	
	private function getLotteryConfig()
	{
		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        return json_decode($lotteryConfig, true);
	}

	// 获取流水状态标示
	public function getStatus()
	{
		return $this->ctype;
	}
	
	//追号充值直接支付订单功能
	public function autoPay($trade_no, $trans)
	{
		if($trade_no)
		{
			$orders = $this->db->query("select n.uid, n.money, n.chaseId, n.lid, n.endTime 
			from cp_wallet_logs m 
			join cp_chase_manage n on m.orderId = n.chaseId 
			where m.trade_no = ? and n.status = 0 
			and m.ctype={$this->ctype['recharge']} and m.status = 1", array($trade_no))->getRow();
			if(!empty($orders))
			{ 
				$orderData = array('chaseId' => $orders['chaseId'], 'lid' => $orders['lid']);
				$this->payChaseOrder($orders['uid'], $orderData, $orders['money'], $trans);
			}
		}
	}

	//追号订单支付
    public function payChaseOrder($uid, $orderData, $money=0, $trans = true)
    {
    	// 事务开始
    	if($trans) $this->db->trans_start();
    	
    	if($money > 0)
    	{
    		$payStatus = $this->payChaseMoney($uid, $money, $orderData['chaseId']);
    		$trade_no = $payStatus['trade_no'];
    	}
    	else 
    	{
    		$status = array(
    			'code' => 400,
				'msg' => '支付金额错误',
				'data' => $orderData
    		);
    	}

        if($trade_no)
        {
        	// 扣款成功处理
        	$orderData['trade_no'] = $trade_no;

        	// 处理追号订单状态
        	$orderStatus = $this->dealChaseOrder($orderData);

        	if($orderStatus)
        	{
        		// 支付成功
        		if($trans) $this->db->trans_complete();
        			
        		$this->freshWallet($uid);
        		// 启动追号脚本
        		// $orderData['betNum'] = $this->startAutoBet($orderData['lid']);

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
    			$this->chase_order_model->upOutOfDay($orderData['chaseId']);
    		}
    		$status = array(
    			'code' => $payStatus['code'],
				'msg' => $payStatus['msg'],
				'data' => $orderData
    		);
        }
    	return $status;
    }

    //追号扣款
	public function payChaseMoney($uid, $cost, $chaseId=0)
	{
		$payStatus = array(
			'code' => 400,
			'trade_no' => false,
			'msg' => '支付失败'
		);

		$cost = intval($cost);
		// 用户信息锁表
		$uinfo = $this->getUserMoney($uid);

		if($uinfo['money'] >= $cost)
		{
			if(!empty($chaseId))
			{
				// 追号表锁表
				$order = $this->getChaseInfo($chaseId);
				$this->lids = array_flip($this->orderConfig('lidmap'));
				$sublids = array($this->lids['syxw'], $this->lids['jxsyxw'], $this->lids['ks'], $this->lids['jlks'], $this->lids['jxks'], $this->lids['hbsyxw'], $this->lids['klpk'], $this->lids['cqssc'], $this->lids['gdsyxw']);
            	$ahead = !in_array($order['lid'], $sublids) ? 
            	($this->lcfgs[$order['lid']]['ahead'] - 5) . " minute" : ($this->lcfgs[$order['lid']]['ahead'] * 60 - 0) . ' second';
				// 获取订单状态
	        	$this->orderStatus = $this->chase_order_model->getStatus();
				// 判断追号订单状态是否异常
				$flag = true;
				if($order['money'] != $cost || $order['status'] != $this->orderStatus['create'] || $order['endTime'] < date('Y-m-d H:i:s'))
				{
					$payStatus = array(
						'code' => 300,
						'trade_no' => false,
						'msg' => '订单支付失败，状态不满足！'
					);
					$flag = false;
				}
				elseif(strtotime($ahead, time()) >= strtotime($order['endTime']))
				{
					$flag = false;
					$payStatus = array(
						'code' => 401,
						'trade_no' => false,
						'msg' => '订单支付失败，已过支付时间！'
					);
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
					'msg' => '订单支付失败，订单信息错误！'
				);
				return $payStatus;
			}

			// 流水号生成
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			
			// 流水日志组装数据
			$wallet_log = array(
				'uid' => $uid, 
				'money' => $cost, 
				'ctype' => $this->ctype['chase_freez'],
				'trade_no' => $trade_no, 
				'umoney' => ($uinfo['money']-$cost),
				'must_cost' => 0,
				'mark' => '0',
				'dispatch' => 0,
				'additions' => (empty($order['lid'])? 0 : $order['lid']), 
				'orderId' => $chaseId
			);
			// 记录扣款流水
			$sql1 = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
			// 扣款
			$sql2 = $this->db->query("update cp_user set money = money - $cost, blocked = blocked + $cost,
			chaseMoney = chaseMoney + $cost
			where money >= $cost and uid = ?", array($uid));
			
			$re = $sql1 && $sql2;
			if($re) 
			{
				$payStatus = array(
					'code' => 200,
					'trade_no' => $trade_no,
					'msg' => '订单扣款成功！'
				);
				return $payStatus;
			}
			else 
			{
				$payStatus = array(
					'code' => 400,
					'trade_no' => false,
					'msg' => '订单扣款失败！'
				);
				return $payStatus;
			}
		}
		else 
		{
			$payStatus = array(
				'code' => 400,
				'trade_no' => false,
				'msg' => '订单支付失败,余额不足！'
			);
			return $payStatus;
		}	
	}

	// 获得余额
	public function getUserMoney($uid)
	{
		return $this->db->query('select money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}

	// 查询追号订单信息
	public function getChaseInfo($chaseId)
	{
		$sql = 'SELECT uid, chaseId, buyPlatform, codes, lid, money, playType, betTnum, 
		isChase, bonus, chaseIssue, totalIssue, status, setStatus, endTime, created 
		FROM cp_chase_manage WHERE chaseId = ? for update';
		$chaseInfo = $this->db->query($sql, array($chaseId))->getRow();
		return $chaseInfo;
	}

	// 处理追号订单状态
	public function dealChaseOrder($orderData)
	{
		$orderStatus = FALSE;
		$this->orderStatus = $this->chase_order_model->getStatus();
		// 组装追号信息数据
		$chaseInfo = array(
			'uid' => $orderData['uid'],
			'chaseId' => $orderData['chaseId'],
			'trade_no' => $orderData['trade_no'],
			'status' => $this->orderStatus['is_chase'],
			'pay_time' => date('Y-m-d H:i:s')
		);
		if($this->chase_order_model->saveChaseInfo($chaseInfo))
		{
			$orderStatus = TRUE;
		}
		return $orderStatus;
	}

	// 刷新钱包
    public function freshWallet($uid)
    {
    	$this->load->model('wallet_model');
		return $this->wallet_model->freshWallet($uid);
    }

    // 开启投注池缓存信息
    public function startAutoBet($lid)
    {
    	$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));

    	$this->load->model('chase_bet_model');
    	$lotteryConfig = $this->chase_bet_model->getLotteryConfig();
    	if(!empty($lotteryConfig[$lid]['lname']))
    	{
    		$lname = $lotteryConfig[$lid]['lname'];
    		$ukey = "{$REDIS['CHASE_BET_NUM']}$lname";
    		// 投注池缓存 +1
    		$betNum = $this->cache->redis->increment($ukey, 1, 0);
    	}
    }
}
