<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 收银台 模型层
 * @date:2015-04-17
 */

class Wallet_Model extends MY_Model 
{
	public $tbname;
	public $ctype = array(
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
        'transfer'                   => 11 //转帐
	);

	public $status = array(
        'withdraw_ini'     => 0, //提款申请状态
        'withdraw_lock'    => 1, //提款锁定后台处理中(已废弃)
        'withdraw_over'    => 2, //处理结束 
        'withdraw_concel'  => 3, //申请取消(已废弃)
        'withdraw_fail'    => 4, //财务打款失败
        'withdraw_operate' => 5, //已操作打款
    );

	public function __construct() 
	{
		parent::__construct();
		$this->tbname = 'cp_wallet_logs';
		$this->load->library('tools');
	}

	/*
	 * APP 订单支付
	 * @date:2015-04-17
	 */
    public function payOrder($uid, $PostData, $orderData, $money=0)
    {
    	$url = $this->config->item('order_api') . 'order/pay';
    	// LOG
    	log_message('LOG', "订单支付 - money: " . $money, 'pay');
    	if($money > 0)
    	{
    		$trade_no = $this->payMoney($uid, $money, $orderData['orderId']);
    	}
    	else 
    	{
    		$trade_no = $orderData['trade_no'];
    		$orderData['money'] = ParseUnit($orderData['money'], 1);
    	}
    	// LOG
    	log_message('LOG', "订单支付 - trade_no返回: " . $trade_no, 'pay');

        if($trade_no)
        {
        	$orderData['trade_no'] = $trade_no;
        	
        	$response = $this->tools->request($url, $PostData);
        	$response = json_decode($response, true);
        	// LOG
    		log_message('LOG', "订单支付 - 请求出票返回数据: " . json_encode($response), 'pay');

        	$response = $this->dealResult($response, $orderData);
        	if(!$response || $response['code'] != 0)
        	{
        		if(!$response)
        		{
	        		$response = array(
		                'code' => 11,
		                'msg' => '订单支付，失败！',
		                'data' => array(
	        				'orderId' => $orderData['orderId'],
	        				'money' => $orderData['money']
	        			),
		        	);
        		}
        	}
        	
        }
        else 
        {
        	//判断订单状态
        	$this->load->model('order_model');
			$order = $this->order_model->getById($orderData['orderId']);
			if($order['status'] == 10)
			{
				$response = array(
	                'code' => 12,
	                'msg' => '订单支付失败，余额不足！',
	                'data' => array(
	        			'orderId' => $orderData['orderId'],
	        			'money' => $orderData['money']
	        		),
	        	);
			}else{
				$response = array(
	                'code' => 16,
	                'msg' => '订单支付失败，状态不满足！',
	                'data' => array(),
	            );
			}
        	
        }
        //log_message('LOG', serialize($response), "ORDER");
    	return $response;
    }

    /*
	 * APP 订单付款 
	 * *********************
 	 * 流程说明：
 	 * 1.检查订单状态
 	 * 2.生成支付流水
 	 * 3.彩票账户扣款
 	 * *********************
	 * @date:2015-05-08
	 */
	public function payMoney($uid, $cost, $orderId=0)
	{
		// 事务处理 开始
		$this->db->trans_start();
		$cost = intval($cost);
		// 检查账户余额
		$money = $this->Order->getMoney($uid);
		$re = false;
		if($money['money'] >= $cost)
		{
			if(!empty($orderId))
			{
				// 检查订单状态 代付款 status => 10
				$this->load->model('order_model');
				$order = $this->order_model->getById($orderId);

				// 金额检查
				if($order['money'] != $cost || $order['status'] != 10)
				{
					return $re; 
				}
			}
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');

			// LOG
    		log_message('LOG', "订单支付 - trade_no生成: " . json_encode($trade_no), 'pay');

			//计算其他余额花费
			$bmoney = ($money['must_cost'] + $money['dispatch']) - $cost;
			$dispatch = 0;
			$must_cost = 0;
			if($bmoney >= $money['must_cost'])
			{
				$dispatch = $cost;
			}
			elseif($bmoney >=0)
			{
				$dispatch = $money['dispatch'];
				$must_cost = abs($dispatch - $cost);
			}
			else 
			{
				$dispatch = $money['dispatch'];
				$must_cost = $money['must_cost'];
			}
			
			$wallet_log = array('uid' => $uid, 'money' => $cost, 'ctype' => $this->ctype['pay'],
			'trade_no' => $trade_no, 'umoney' => ($money['money']-$cost), 'must_cost' => $must_cost,
			'dispatch' => $dispatch, 'additions' => (empty($order['lid'])? 0 : $order['lid']), 'orderId' => $orderId);
			$re1 = $this->db->query("insert {$this->tbname}(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
			$re2 = $this->db->query("update cp_user set money = money - $cost, 
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where money >= $cost and uid = ?", array($uid));
			$re = $re1 && $re2;
			// LOG
    		log_message('LOG', "订单支付 - sql状态1: " . $re1, 'pay');
    		log_message('LOG', "订单支付 - sql状态2: " . $re2, 'pay');
			if($re)
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
		if($re) 
		{
			$this->freshWallet($uid);
			return $trade_no;
		}
		else 
		{
			return $re;
		}
	}

	/*
	 * APP 出票通知返回处理
	 * *********************
 	 * 流程说明：
 	 * 1.检查出票通知返回
 	 * 2.更新订单表状态
 	 * *********************
	 * @date:2015-05-08
	 */
	public function dealResult($response, $data)
	{
		if ($response == null) 
		{
            $response = array(
                'code' => -9999,
                'msg' => '网络出了问题，请稍后再试！',
                'data' => array(),
            );

            // 已扣款，出票通知无响应
            $ctype = 'pay_fail';
        } 
        else 
        {
        	if($response['code'] != 0)
        	{
        		$ctype = 'pay';
        	}
            if ($response['code'] == 6) {
                $response['msg'] = '请退出并重新登录';
            }
        }
        // LOG
    	log_message('LOG', "出票通知返回处理 - sql状态1: " . $re1, 'pay');

        //记录返回状态
        $data['mark'] = $response['code'];
        $response['data'] = array();
        $response['data']['orderId'] = $data['orderId'];
        $response['data']['money'] = $data['money'];
        $data['money'] = ParseUnit($data['money']);
        $this->load->model('order_model');
        if($this->order_model->SaveOrder($ctype, $data))
        {
        	return $response;
        }
        else
        { 
			return false;    
        }    
	}

	/*
	 * APP 余额查询
	 * @date:2015-05-08
	 */
	public function getMoney($uid)
	{
		return $this->db->query('select money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}

	/*
	 * APP 刷新钱包
	 * @date:2015-05-08
	 */
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
			$wallet = $this->db->query('select money, blocked, dispatch from cp_user where uid = ?', array($uid))->getRow();
			return $this->cache->redis->hMSet($ukey, $wallet);
		}
    	
    }




    /*
	 * APP 请求支付中心创建订单
	 * @date:2015-05-08
	 */
	public function doPay($parmas, $pay_type)
	{
		$res = FALSE;
		switch ($pay_type) 
		{
			case 'alipaysdk':
			case 'shengpaysdk':
				$orderResponse = $this->tools->request('http://pay.2345.com/doPay.php', $parmas);
				break;
			default:
				$orderResponse = 'unknown pay type';
				break;
		}
        
        // LOG
    	log_message('LOG', "充值 - 支付中心返回参数: " . $orderResponse, 'recharge');

        if($orderResponse == 'success')
        {
        	$res = TRUE;
        }

        return $res;
	}

	/*
	 * APP 获得钱包密钥
	 * @date:2015-05-08
	 */
	public function GetSalt()
	{
		$redata = $this->db->query('select content from cp_secret where cid = 1')->getOne();
		if(!empty($redata))
		{
			$redata = unserialize($redata);
			return $redata['new_token'];
		}
	}

	/*
	 * APP 记录充值流水表
	 * @date:2015-05-08
	 */
	public function recordWalletLog($data)
	{
		$trade_check = $this->db->query("select count(*) from {$this->tbname} where trade_no = ? ", array($data['trade_no']))->getOne();
		$re = false;
		if($trade_check < 1)
		{
			$this->db->trans_start();
			$wallet = $this->getMoney($data['uid']);
			$data['umoney'] = $wallet['money'];
			$fields = array_keys($data);
			$sql = "insert ignore {$this->tbname}(" . implode(',', $fields) . ", created)values(" . 
			implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
			$re = $this->db->query($sql, $data);
			if($re)
				$this->db->trans_complete();
			else 
				$this->db->trans_rollback();
		}
		return $re;
	}

	/*
	 * APP 查询用户账户明细
	 * @date:2015-05-08
	 */
    public function getTradeDetail($cons, $cpage, $psize)
    {
    	// 合买类型过滤
        $conStr = " AND (mark != '2' OR (mark = '2' AND additions IN (1, 2, 3))) AND uid = ? and (ctype <> 1 || status <> 1) and ctype <> 15 and status <> 3 ";
        $orderStr = "created";
        if (in_array($cons['ctype'], $this->ctype, TRUE))
        {
            $conStr .= ' AND ctype = ? ';
        }
        else
        {
            switch ($cons['ctype'])
            {
                case 'all':
                    unset($cons['ctype']);
                    break;
                case 'income':
                    $conStr .= " AND mark = '1'";
                    unset($cons['ctype']);
                    break;
                case 'expand':
                    $conStr .= " AND mark = '0'";
                    unset($cons['ctype']);
                    break;
                case 'withdraw':
                    $withdrawAry = array(
                        $this->ctype['apply_for_withdraw'],
                        // $this->ctype['apply_for_withdraw_succ'],
                        // $this->ctype['withdraw_succ'],
                        // $this->ctype['apply_for_withdraw_conceal'],
                        $this->ctype['apply_for_withdraw_fail'],
                    );
                    $conStr .= ' AND ctype IN (' . implode(',', $withdrawAry) . ') ';
                    unset($cons['ctype']);
                    break;
                case '0':
                	$conStr .= " AND ctype = ?";
                	// $orderStr = "modified";
                	break;
                default:
                    $conStr .= " AND ctype = ?";
                    break;
            }
        }

        $nsql = "SELECT COUNT(*) total, SUM(IF(mark = '1', money, 0)) income, SUM(IF(mark = '0', money, 0)) umoney
            FROM {$this->tbname} WHERE 1 $conStr";
        $vdata = $this->slave->query($nsql, $cons)->getRow();

        $sql = "SELECT $orderStr as created, trade_no, orderId, additions, status,content, IF(mark = '0', money, 0) expend,
            IF(mark = '1', money, 0) income, umoney, ctype, mark,
            IF(mark = '2' AND additions IN (1, 2, 3), money, 0) kmoney,
            IF(DATE_ADD(created, INTERVAL 12 HOUR ) < NOW(), 1, 0) outdate, uid
            FROM {$this->tbname} force index(uid)
            WHERE 1 $conStr
            ORDER BY $orderStr DESC LIMIT " . ($cpage - 1) * $psize . ", $psize";
        $vdata['datas'] = $this->slave->query($sql, $cons)->getAll();

        return $vdata;
    }

    /*
	 * APP 查询某一次流水详情
	 * @date:2015-05-14
	 */
    public function getDetail($uid, $tradeNo)
    {
        $sql = "SELECT created, trade_no, orderId, additions, status, IF(mark = '0', money, 0) expend,
            IF(mark = '1', money, 0) income, umoney, ctype, mark,
            IF(mark = '2' AND additions IN (1, 2, 3), money, 0) kmoney,
            IF(DATE_ADD(created, interval 12 hour) < NOW(), 1, 0) outdate,
            content
            FROM {$this->tbname}
            WHERE trade_no = ? AND uid = ? AND (mark != '2' OR (mark = '2' AND additions IN (1, 2, 3)))";
        $params = array(
            'trade_no' => $tradeNo,
            'uid'      => $uid
        );
        $result = $this->slave->query($sql, $params)->getRow();

        return $result;
    }

    /*
	 * APP 查询某一次流水详情 不区分订单状态
	 * @date:2015-05-14
	 */
    public function getWalletLog($uid, $tradeNo)
    {
        $sql = "SELECT created, trade_no, orderId, additions, status, money, channel
            FROM {$this->tbname}
            WHERE trade_no = ? AND uid = ?";
        $params = array(
            'trade_no' => $tradeNo,
            'uid'      => $uid
        );
        $result = $this->db->query($sql, $params)->getRow();

        return $result;
    }

    /*
	 * APP 获得可提资金
	 * @date:2015-05-14
	 */
	public function getWithDraw($uid)
	{
		$subtract = "if((must_cost + dispatch) > chaseMoney, (must_cost + dispatch - chaseMoney), 0)";
        return $this->db->query("SELECT if(money >= $subtract, money - $subtract, 0) FROM cp_user WHERE uid = ? FOR UPDATE",
            array($uid))->getOne();
	}

	/*
	 * APP 获得提现记录
	 * @date:2015-05-14
	 */
	public function getWithdrawLog($uid)
	{
	    $todayCount = $this->db->query("SELECT count(*) FROM cp_withdraw
		WHERE status != {$this->status['withdraw_fail']} AND uid = ? AND created >= date(now()) && created < date(date_add(now(), INTERVAL 1 DAY))",
            array($uid))->getOne();
        $privilege = $this->db->query("select l.privilege from cp_growth_level l inner join cp_user_growth g on l.grade= g.grade where g.uid = ?", array($uid))->getRow();
        $countLimit = 3;
        if($privilege)
        {
            $privilege = json_decode($privilege['privilege'], true);
            $countLimit = $privilege['withdraw'];
        }
        
        return array('count' => $todayCount, 'countLimit' =>$countLimit);
	}

	/*
	 * APP 申请提现资金
	 * @date:2015-05-14
	 */
	public function setWithDraw($money, $uid, $platform, $version, $channel, $additions='')
	{
		$this->db->trans_start();
		$money = intval($money);
		$withdraw = $this->getWithDraw($uid);
		$cmoney = $this->getMoney($uid);
		$cmoney = $cmoney['money'];
		$result = array(
			'status' => FALSE,
			'msg' => '系统异常',
			'data' => ''
		);
		$withdrawLog = $this->getWithdrawLog($uid);
		if($withdrawLog['count'] >= $withdrawLog['countLimit'])
		{
			$this->db->trans_rollback();
			$result = array(
				'status' => FALSE,
				'msg' => '获取提现记录失败',
				'data' => ''
			);
			return $result;
		}
		if($withdraw >= $money)
		{
			// 更新用户余额
			$sql = "update cp_user set money = money - $money where money >= $money and uid = ?";
			$re1 = $this->db->query($sql, $uid);
			$orderid = $this->tools->getIncNum('UNIQUE_KEY');
			$wallet_log = array(
				'uid' => $uid, 
				'money' => $money, 
				'ctype' => $this->ctype['apply_for_withdraw'],
				'trade_no' => $orderid, 
				'umoney' => ($cmoney - $money), 
				'channel' => $channel, 
				'app_version' => $version, 
				'platform' => $platform
			);
			// 记录钱包流水
			$re2 = $this->db->query("insert {$this->tbname}(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
			// 记录提款表
			$re3 = $this->db->query("insert cp_withdraw(uid, trade_no, money, umoney, additions, platform, app_version, channel, created) values (?, ?, ?, ?, ?, ?, ?, ?, now())", array($uid, $orderid, $money, ($cmoney - $money), $additions, $platform, $version, $channel));

			// 总账记录流水
        	$this->load->model('capital_model');
        	$re4 = $this->capital_model->recordCapitalLog('1', $orderid, 'withdraw', $money, '1', $tranc = FALSE);

			$re = $re1 && $re2 && $re3 && $re4;
			if($re)
			{
				$this->db->trans_complete();
				$result = array(
					'status' => TRUE,
					'msg' => '提现成功',
					'data' => $orderid
				);
			}
			else 
			{
				$this->db->trans_rollback();
				$result = array(
					'status' => FALSE,
					'msg' => '提现失败',
					'data' => ''
				);
			}
		}
		else
		{
			$this->db->trans_rollback();
			$result = array(
				'status' => FALSE,
				'msg' => '提现金额不足',
				'data' => ''
			);
		}
		if($result['status'])	$this->freshWallet($uid);
		return $result;
	}

	/*
	 * APP 获得提现记录详情
	 * @date:2015-05-14
	 */
	public function getWithdrawDetail($uid, $trade_no)
	{
		return $this->db->query("SELECT uid, trade_no, money, status, additions, created FROM cp_withdraw 
		WHERE uid = ? and trade_no = ?", 
		array( $uid, $trade_no ) )->getRow();
	}

	/*
	 * APP wap网关充值同步回调 根据流水查询充值信息
	 * @version:V1.6
	 * @date:2015-12-23
	 */
	public function getRechargeLog($tradeNo)
	{
		$sql = "SELECT uid, created, trade_no, orderId, additions, status, money
            FROM {$this->tbname}
            WHERE trade_no = ? AND ctype = ?";
        $params = array(
            'trade_no' => $tradeNo,
            'ctype'    => $this->ctype['recharge']
        );
        $result = $this->db->query($sql, $params)->getRow();

        return $result;
	}

	/*
	 * APP 根据提款状态查询提现记录进度详情
	 * @date:2016-03-11
	 */
	public function getWithdrawInfo($tradeNo)
	{
		$sql = "SELECT uid, trade_no, money, umoney, status, additions, content, platform, app_version, channel, start_check, fail_time, succ_time, created, modified  
        FROM cp_withdraw 
        WHERE 1 AND trade_no = '{$tradeNo}'";
        $info = $this->db->query($sql)->getRow();
        return $info;
	}

	/*
	 * APP 记录支付流水
	 * @date:2016-05-25
	 */
	public function recordPayLog($payLog)
	{
		$upd = array('pay_trade_no', 'status');
		$fields = array_keys($payLog);
		$sql = "insert cp_pay_logs(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
		return $this->db->query($sql, $payLog);
	}

	/*
	 * APP 查询支付流水
	 * @date:2016-08-01
	 */
	public function getPayLog($trade_no, $sync_flag)
	{
		$sql = "select trade_no, status, select_num, pay_type from cp_pay_logs where trade_no = ? and sync_flag = ?";
		return $this->db->query($sql, array($trade_no, $sync_flag))->getRow();
	}

	/*
	 * APP 查询支付流水
	 * @date:2016-08-01
	 */
	public function getUserPayLog($uid, $trade_no)
	{
		$sql = "SELECT w.uid, w.trade_no, p.status, p.select_num, p.pay_type FROM cp_wallet_logs AS w LEFT JOIN cp_pay_logs AS p ON w.trade_no = p.trade_no WHERE w.uid = ? AND w.trade_no = ?";
		return $this->db->query($sql, array($uid, $trade_no))->getRow();
	}

	/*
	 * APP 更新支付流水
	 * @date:2016-08-01
	 */
	public function updatePayLog($trade_no, $data = array())
	{
		$this->db->where('trade_no', $trade_no);
		$this->db->update('cp_pay_logs', $data);
		return $this->db->affected_rows();
	}
        
        /*
	 * APP 查询所有用户账户明细
	 * 包含合买
	 */
    public function getAllTradeDetail($cons, $cpage, $psize)
    {
    	// 合买类型过滤
        $conStr = " AND (mark != '2' OR (mark = '2' AND additions IN (1, 2, 3))) AND uid = ? and (ctype <> 1 || status <> 1) ";
        $orderStr = "created";
        if (in_array($cons['ctype'], $this->ctype, TRUE))
        {
            $conStr .= ' AND ctype = ? ';
        }
        else
        {
            switch ($cons['ctype'])
            {
                case 'all':
                    unset($cons['ctype']);
                    break;
                case 'income':
                    $conStr .= " AND mark = '1'";
                    unset($cons['ctype']);
                    break;
                case 'expand':
                    $conStr .= " AND mark = '0'";
                    unset($cons['ctype']);
                    break;
                case 'withdraw':
                    $withdrawAry = array(
                        $this->ctype['apply_for_withdraw'],
                        // $this->ctype['apply_for_withdraw_succ'],
                        // $this->ctype['withdraw_succ'],
                        // $this->ctype['apply_for_withdraw_conceal'],
                        $this->ctype['apply_for_withdraw_fail'],
                    );
                    $conStr .= ' AND ctype IN (' . implode(',', $withdrawAry) . ') ';
                    unset($cons['ctype']);
                    break;
                case '0':
                	$conStr .= " AND ctype = ?";
                	// $orderStr = "modified";
                	break;
                default:
                    $conStr .= " AND ctype = ?";
                    break;
            }
        }

        $nsql = "SELECT COUNT(*) total, SUM(IF(mark = '1', money, 0)) income, SUM(IF(mark = '0', money, 0)) umoney
            FROM {$this->tbname} WHERE 1 $conStr";
        $vdata = $this->slave->query($nsql, $cons)->getRow();

        $sql = "SELECT $orderStr as created, trade_no, orderId, additions, status,content, IF(mark = '0', money, 0) expend,
            IF(mark = '1', money, 0) income, umoney, ctype, mark,
            IF(mark = '2' AND additions IN (1, 2, 3), money, 0) kmoney,
            IF(DATE_ADD(created, INTERVAL 12 HOUR ) < NOW(), 1, 0) outdate, uid
            FROM {$this->tbname} force index(uid)
            WHERE 1 $conStr
            ORDER BY $orderStr DESC LIMIT " . ($cpage - 1) * $psize . ", $psize";
        $vdata['datas'] = $this->slave->query($sql, $cons)->getAll();

        return $vdata;
    }

	/*
	 * APP 更新支付流水
	 * @date:2017-04-17
	 */
	public function getPayConfigDetail($platform, $pay_type)
	{
		$sql = "SELECT id, platform, ctype, pay_type, mer_id, rate, weight, status FROM cp_pay_config WHERE platform = ? AND pay_type = ? AND status = 0";
		return $this->db->query($sql, array($platform, $pay_type))->getAll();
	}

	public function getPayConfigByCtype($platform, $ctype)
	{
		$sql = "SELECT id, platform, ctype, pay_type, mer_id, rate, weight, status FROM cp_pay_config WHERE platform = ? AND ctype = ? AND rate > 0 AND status = 0 ORDER BY rate DESC";
		return $this->db->query($sql, array($platform, $ctype))->getAll();
	}

}