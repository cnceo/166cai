<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 支付
 * @date:2016-03-29
 */
class Pay_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 易宝支付 移动端
	 * @date:2016-03-29
	 */
	public function yeepayMPay($params = array())
	{
		// 创建充值流水
		$payLog = array(
			'trade_no' => $params['trade_no'],
			'money' => $params['money'],
			'pay_time' => $params['pay_time'],
			'pay_type' => '1'
		);

		$this->recordPayLog($payLog);

		// 获取加密钥
        $this->config->load('pay');
        $yeepay = $this->config->item('yeepay'); 

        $initDatas['merchantaccount']    = $yeepay['merchantaccount'];
        $initDatas['merchantPrivateKey'] = $yeepay['merchantPrivateKey'];
        $initDatas['merchantPublicKey']  = $yeepay['merchantPublicKey'];
        $initDatas['yeepayPublicKey']    = $yeepay['yeepayPublicKey'];

        // 加载公共类
        $this->load->library("yeepay/YeepayMPay");
        $this->yeepaympay->init($initDatas);

        $datas['order_id'] = $params['trade_no'];
        $datas['transtime'] = (int)strtotime($params['pay_time']);
        $datas['product_catalog'] = '1';
        $datas['identity_id'] = $params['uid'];
        $datas['identity_type'] = 0;	//支付身份标识类型码
        $datas['user_ip'] = $params['ip'];
        $datas['user_ua'] = '';
        $datas['callbackurl'] =	$yeepay['callbackurl'];	//后台回调地址
        $datas['fcallbackurl'] = $yeepay['fcallbackurl'];	//前台回调地址
        $datas['product_name'] = $params['product_name'];
        $datas['product_desc'] = $params['product_desc'];
        $datas['terminaltype'] = 3;
        $datas['terminalid'] = '05-16-DC-59-C2-34';//其他支付身份信息
        $datas['amount'] = (int)$params['money'];
        $datas['cardno'] = '';
        $datas['idcardtype'] = '01';	// 证件类型 - 身份证
        $datas['idcard'] = $params['id_card'];
        $datas['owner'] = $params['real_name'];
        $datas['currency'] = 156;	// 币种 - 人民币
        // $datas['orderexp_date'] = '';	// 默认 24h
        // log_message('LOG', "请求参数2: " . json_encode($datas), 'yeepayMPay');
        $url = $this->yeepaympay->webPay($datas);
        return $url;
	}

	/**
	 * 记录充值流水
	 * @date:2016-03-29
	 */
	public function recordPayLog($payLog)
	{
		$upd = array('pay_trade_no', 'status');
		$fields = array_keys($payLog);
		$sql = "insert cp_pay_logs(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
		return $this->db->query($sql, $payLog);
	}

	/**
	 * 记录充值流水
	 * @date:2016-03-29
	 */
	public function getPayDetail($trade_no)
	{
		return $this->db->query('SELECT trade_no, money, pay_trade_no, pay_time, status, pay_type from cp_pay_logs where trade_no = ? for update', array($trade_no))->getRow();
	}

	/*
     * 加密解密公共函数
     * @date:2016-01-18
     */
    public function strCode($str , $action = 'DECODE')
    {
    	$action == 'DECODE' && $str = base64_decode ($str);
    	$code = '';
    	$hash = $this->config->item('encrypt_hash');
    	$key = md5 ( $hash );
    	$keylen = strlen ( $key );
    	$strlen = strlen ( $str );
    	for($i = 0; $i < $strlen; $i ++)
    	{
    	   $k = $i % $keylen; //余数  将字符全部位移
    	   $code .= $str[$i] ^ $key[$k]; //位移
    	}
    	return ($action == 'DECODE' ? $code : base64_encode ( $code ));
    }

	/**
	 * 易宝支付 异步通知
	 * @date:2016-03-29
	 */
	public function yeepayMPayCallback($params)
	{
		// 获取加密钥
        $this->config->load('pay');
        $yeepay = $this->config->item('yeepay'); 

        $initDatas['merchantaccount']    = $yeepay['merchantaccount'];
        $initDatas['merchantPrivateKey'] = $yeepay['merchantPrivateKey'];
        $initDatas['merchantPublicKey']  = $yeepay['merchantPublicKey'];
        $initDatas['yeepayPublicKey']    = $yeepay['yeepayPublicKey'];

		$status = FALSE;
		$this->load->library("yeepay/YeepayMPay");

		$this->yeepaympay->init($initDatas);

		$payResult = $this->yeepaympay->callback($params['data'], $params['encryptkey']);

		if($payResult['status'] == 1)
		{
			// 组装参数
			$payData = array(
				'trade_no' => $payResult['orderid'],
				'pay_trade_no' => $payResult['yborderid'],
				'status' => '1',	// 成功
			);
			$status = $this->handleRechargeSucc($payData);
		}
		return $status;
	}

	/**
	 * 易宝支付 同步通知
	 * @date:2016-03-29
	 */
	public function yeepayMPayReturnUrl($params)
	{
		// 获取加密钥
        $this->config->load('pay');
        $yeepay = $this->config->item('yeepay'); 

        $initDatas['merchantaccount']    = $yeepay['merchantaccount'];
        $initDatas['merchantPrivateKey'] = $yeepay['merchantPrivateKey'];
        $initDatas['merchantPublicKey']  = $yeepay['merchantPublicKey'];
        $initDatas['yeepayPublicKey']    = $yeepay['yeepayPublicKey'];

        $this->load->library("yeepay/YeepayMPay");

		$this->yeepaympay->init($initDatas);

		// 解析数据
		$payResult = $this->yeepaympay->callback($params['data'], $params['encryptkey']);

		if($payResult['status'] == 1)
		{
			// 查询流水类型返回平台
			$this->load->model('wallet_model');
			$walletInfo = $this->wallet_model->getWalletLog($payResult['orderid']);

			if(empty($walletInfo))
			{
				var_dump("订单信息错误");die;
			}

			// 更新同步通知标识
			$data['sync_flag'] = 1;
        	$this->updatePayLog($walletInfo['trade_no'], $data); 

			// 用户信息加密
			$rechargeData = array(
				'uid' => $walletInfo['uid'],    			// 用户ID
		        'tradeNo' => $walletInfo['trade_no'],    	// wallet_log流水号
		        'redirectPage' => (!empty($walletInfo['orderId']))?'order':'recharge',   // 跳转类型 充值详情、订单支付页
		        'cp_orderId' => $walletInfo['orderId']  	// 订单号
			);

			$token = $this->strCode(json_encode($rechargeData), 'ENCODE');

			switch ($walletInfo['platform']) 
			{
				case '1':
					$url = 'https:' . $this->config->item('pages_url') . 'app/wallet/rechargeComplete/' . urlencode($token);
					break;
				
				case '2':
					$url = 'https:' . $this->config->item('pages_url') . 'ios/wallet/rechargeComplete/' . urlencode($token);
					break;

				case '3':
					// 暂不支持
					$url = $this->config->item('m_pages_url') . 'wallet/rechargeComplete/' . $walletInfo['trade_no'];
					break;

				default:
					var_dump("订单信息错误");die;
					break;
			}
			header('Location: ' . $url);
		}
	}

	/**
     * 支付 异步成功处理
     * @date:2016-03-29
     */
	public function handleRechargeSucc($payResult)
	{
		$status = FALSE;
		// 事务开始
		$this->trans_start('db');

		$payData = $this->getPayDetail($payResult['trade_no']);

		if($payData['status'] == '0')
		{
			$sql = "UPDATE cp_pay_logs SET status = ?, pay_trade_no = ? WHERE trade_no = ? AND status = 0";
			$res = $this->db->query($sql, array('status' => $payResult['status'], 'pay_trade_no' => $payResult['pay_trade_no'], 'trade_no' =>
			 $payResult['trade_no']));

			if($res)
			{
				// 通知打款
				$real_amount = ParseUnit($payData['money'], 1);
				$this->load->model('wallet_model');
				$doRecharge = $this->wallet_model->recharge($payResult['trade_no'], $real_amount, false);
				if($doRecharge)
				{
					$status = TRUE;
					$this->trans_complete('db');
				}
				else 
				{
					$this->trans_rollback('db');
					// 打款失败 记录日志
					log_message('LOG', "打款失败: " . json_encode($payResult), 'payMPay');
				}
			}
			else
			{
				$this->trans_rollback('db');
			}
		}
		else
		{
			$this->trans_rollback('db');
		}
		return $status;
	}

	/**
     * 易宝支付 【网银】 同步 异步通知
     * @date:2016-03-29
     */
	public function yeepayWebCallback($params)
	{
		$this->load->library('yeepay/YeepayComm');
		$checkResult = $this->yeepaycomm->CheckHmac($params);
		// var_dump($checkResult);die;
		// 校验通过 支付状态成功
		if($checkResult && $params['r1_Code'] == '1')
		{
			// r9_BType 1 页面端 2 服务端
			switch ($params['r9_BType']) 
			{
				case '1':
					$data['sync_flag'] = 1;
					$this->updatePayLog($params['r6_Order'], $data); //更新同步通知标识
					$url = $this->config->item('pages_url') . 'mylottery/rchagscess/' . $params['r6_Order'];
					header('Location: ' . $url);
					break;
				
				case '2':
					// 组装参数
					$payData = array(
						'trade_no' => $params['r6_Order'],
						'pay_trade_no' => $params['r2_TrxId'],
						'status' => '1',	// 成功
					);
					$status = $this->handleRechargeSucc($payData);
					if($status)
					{
						echo "SUCCESS";
						die;
					}
					break;

				default:
					# code...
					break;
			}
		}
	}

	public function yeepayMPayCallback2($params)
	{
		// 获取加密钥
        $this->config->load('pay');
        $yeepay = $this->config->item('yeepay'); 

        $initDatas['merchantaccount']    = $yeepay['merchantaccount'];
        $initDatas['merchantPrivateKey'] = $yeepay['merchantPrivateKey'];
        $initDatas['merchantPublicKey']  = $yeepay['merchantPublicKey'];
        $initDatas['yeepayPublicKey']    = $yeepay['yeepayPublicKey'];

        $this->load->library("yeepay/YeepayMPay");

		$this->yeepaympay->init($initDatas);

		// 解析数据
		$payResult = $this->yeepaympay->callback($params['data'], $params['encryptkey']);
		var_dump($payResult);die;
	}
	
	/**
	 * 连连支付 同步 异步通知
	 * @date:2016-03-29
	 */
	public function llpayWebCallback($params)
	{
		
		$payData = array(
				'trade_no' => $params['no_order'],
				'pay_trade_no' => $params['oid_paybill'],
				'status' => '1',	// 成功
		);
		$status = $this->handleRechargeSucc($payData);
		if($status)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 中信支付 同步 异步通知
	 * @date:2016-03-29
	 */
	public function zxpayWebCallback($params)
	{
		if($params['respCode'] == '0000')
		{
			$payData = array(
				'trade_no' => $params['trade_no'],
				'pay_trade_no' => $params['pay_trade_no'],
				'status' => '1',	// 成功
			);
			$status = $this->handleRechargeSucc($payData);
			if($status)
			{
				return true;
			}
		}
		else
		{
			$status = $this->updatePayLog($params['trade_no'], array('status' => '2'));
			if($status)
			{
				log_message('log', print_r($params, true), 'zxPayCallback');
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 威富通支付宝支付 同步 异步通知
	 * @date:2016-03-29
	 */
	public function wftZfbCallback($params)
	{
		if($params['respCode'] == '0')
		{
			$payData = array(
				'trade_no' => $params['trade_no'],
				'pay_trade_no' => $params['pay_trade_no'],
				'status' => '1',	// 成功
			);
			$status = $this->handleRechargeSucc($payData);
			if($status)
			{
				return true;
			}
		}
		else
		{
			$status = $this->updatePayLog($params['trade_no'], array('status' => '2'));
			if($status)
			{
				log_message('log', print_r($params, true), 'wftPayCallback');
				return true;
			}
		}
	
		return false;
	}

	/**
	 * 统统付  移动端异步通知
	 * @date:2016-06-27
	 */
	public function sumpayWapCallback($params)
	{
		if($params['status'] == '1')
		{
			$payData = array(
				'trade_no' => $params['order_no'],
				'pay_trade_no' => $params['serial_no'],
				'status' => '1',	// 成功
			);
			$status = $this->handleRechargeSucc($payData);
		}
		return TRUE;	
	}

	/**
	 * 现在支付  支付宝h5异步通知
	 * @date:2017-04-11
	 */
	public function xzZfbWapCallback($params)
	{
		if($params['tradeStatus'] != "" && $params['tradeStatus'] == "A001")
		{
			$payData = array(
				'trade_no' => $params['mhtOrderNo'],
				'pay_trade_no' => $params['nowPayOrderNo'],
				'status' => '1',	// 成功
			);
			$status = $this->handleRechargeSucc($payData);
		}
	}

	/**
	 * 移动支付  同步回调处理函数
	 * @date:2016-06-28
	 */
	public function appSyncCallBack($trade_no)
	{
		// 查询流水类型返回平台
		$this->load->model('wallet_model');
		$walletInfo = $this->wallet_model->getWalletLog($trade_no);

		if(empty($walletInfo))
		{
			var_dump("订单信息错误");die;
		}

		// 更新同步通知标识
		$data['sync_flag'] = 1;
        $this->updatePayLog($walletInfo['trade_no'], $data); 

		// 用户信息加密
		$rechargeData = array(
			'uid' => $walletInfo['uid'],    			// 用户ID
	        'tradeNo' => $walletInfo['trade_no'],    	// wallet_log流水号
	        'redirectPage' => (!empty($walletInfo['orderId']))?'order':'recharge',   // 跳转类型 充值详情、订单支付页
	        'cp_orderId' => $walletInfo['orderId']  	// 订单号
		);

		$token = $this->strCode(json_encode($rechargeData), 'ENCODE');

		switch ($walletInfo['platform']) 
		{
			case '1':
				$url = $this->config->item('pages_url') . 'app/wallet/rechargeComplete/' . urlencode($token);
				break;
			
			case '2':
				$url = $this->config->item('pages_url') . 'ios/wallet/rechargeComplete/' . urlencode($token);
				break;

			case '3':
					$url = $this->config->item('m_pages_url') . 'wallet/rechargeComplete/' . $walletInfo['trade_no'];
					break;

			default:
				var_dump("订单信息错误");die;
				break;
		}
		header('Location: ' . $url);
	}
	
	/**
	 * 更新pay_log表记录
	 * @param unknown_type $trade_no
	 * @param unknown_type $data
	 */
	public function updatePayLog($trade_no, $data = array())
	{
		$this->db->where('trade_no', $trade_no);
		$this->db->update('cp_pay_logs', $data);
		return $this->db->affected_rows();
	}
	
	/**
	 * 查询pay_log表记录
	 * @param unknown_type $trade_no
	 * @param unknown_type $sync_flag
	 */
	public function getPayLog($trade_no, $sync_flag)
	{
		$sql = "select trade_no, status, select_num, pay_type from cp_pay_logs where trade_no = ? and sync_flag = ?";
		return $this->db->query($sql, array($trade_no, $sync_flag))->getRow();
	}
	
	/**
	 * 记录退款流水
	 * @date:2016-11-22
	 */
	public function recordRefundLog($datas)
	{
		$upd = array('status');
		$fields = array_keys($datas);
		$sql = "insert cp_refund_logs(" . implode(',', $fields) . ")values(" .
				implode(',', array_map(array($this, 'maps'), $fields)) .  ")" . $this->onduplicate($fields, $upd);
		return $this->db->query($sql, $datas);
	}

	// 查询指定商户
	public function getPayConfig($configId)
	{
		$sql = "select platform, ctype, pay_type, mer_id, extra from cp_pay_config where id = ?";
		return $this->db->query($sql, array($configId))->getRow();
	}
	
	/**
	 * 添加内容到报警表
	 * @param string $content	报警内容
	 */
	public function insertAlert($content)
	{
		$sql = "INSERT INTO cp_alert_log
		(ctype, title,content,created) VALUES ('19', '充值或存在异常报警', ?, NOW())";
		$this->db->query($sql, array($content));
	}
        
        public function getTradenoByToken($token)
        {
            $time = date("Y-m-d H:i:s", strtotime("-10 minute"));
            $sql = "select trade_no from cp_wallet_logs where content=? and modified>=?";
            return $this->db->query($sql, array($token, $time))->getRow();
        }
        
        public function getFreshPayConfig()
        {
             $sql = "select fresh_payconfig from cp_fresh_payconfig where 1 limit 1";
             return $this->db->query($sql)->getRow();           
        }

    public function getRateDetail($params)
    {
    	$info = array();
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	if($params['isDev'])
    	{
    		$redisKey = 'DEV_' . $params['platform'] . '_' . $params['ctype'];
    	}
    	else
    	{
    		$redisKey = $params['platform'] . '_' . $params['ctype'];
    	}
        
        $dispatch = json_decode($this->cache->hGet($REDIS['PAY_RATE_CONFIG'], $redisKey), true);
        if($dispatch)
        {   
            $list = array();
            $rateArr = array();
            foreach ($dispatch as $items) 
            {
                $list[$items['id']] = $items;
                $rateArr[$items['id']] = $items['rate'];
            }

            $id = $this->getRate($rateArr);
            $info = $list[$id];
        }
        return $info;
    }

    public function getRate($rateArr) 
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($rateArr);
        //概率数组循环
        foreach ($rateArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($rateArr);
        
        return $result;
    }
    
    public function queryMarkByMerid($merid)
    {
        $sql = "select mark from cp_pay_config where mer_id=? and rate>0 limit 1";
        return $this->slave->query($sql, array($merid))->getRow();
    }
}
