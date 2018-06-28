<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 支付中心
 * @date:2016-01-18
 */
class Wallet extends MY_Controller 
{
	// SDK 验证 salt
	private $salt = '2345androidcp';

	public function __construct() 
	{
		parent::__construct();
		$this->load->model('order_model','Order');
		$this->load->model('user_model','User');
		$this->load->model('wallet_model','Wallet');
	}

	/*
	 * APP 充值调用 支付宝、微信、盛付通
	 * @date:2015-05-11
	 */
	public function doRecharge()
	{
		$redata = $this->input->post(null, true);
		$result = array(
		    'status' => '0',
		    'msg' => '系统升级维护中',
		    'data' => array()
		);
		die(json_encode($result));

		if( empty($redata['token']) || empty($redata['pay_type']) || empty($redata['money']) || empty($redata['redirectPage']) )
		{
			$result = array(
				'status' => '0',
				'msg' => '必要参数缺失',
				'data' => $redata
			);
			die(json_encode($result));
		}

		// 验证提交信息
		$data = $this->strCode(urldecode($redata['token']));
		$data = json_decode($data, true);

		if(empty($data['uid']))
		{
			$result = array(
				'status' => '0',
				'msg' => '参数校验失败',
				'data' => $redata
			);
			die(json_encode($result));
		}

		// 充值白名单
		// $this->config->load('whitelist');
		// $userList = $this->config->item('user');
		// if(!in_array($data['uid'], $userList))
		// {
		// 	$result = array(
		// 		'status' => '0',
		// 		'msg' => '因彩票停售，充值通道暂时关闭',
		// 		'data' => $redata
		// 	);
		// 	die(json_encode($result));
		// }

		$redata['orderId'] = $data['orderId']?$data['orderId']:'';
		$redata['orderType'] = $data['orderType']?'1':'0';
		$redata['lid'] = $data['lid']?$data['lid']:0;
		// 平台、渠道及版本信息
		$redata['platform'] = $this->config->item('platform');
		$redata['channel'] = $this->recordChannel($redata['channel']);
		$redata['app_version'] = (isset($redata['app_version']) && !empty($redata['app_version']))?$redata['app_version']:'1.0';

		// 金额校验
		if( !is_numeric($redata['money']) || $redata['money'] <= 0 )
		{
			$result = array(
				'status' => '0',
				'msg' => '充值金额错误',
				'data' => $redata
			);
			die(json_encode($result));
		}

		// 按充值渠道判断金额限制
		if($redata['redirectPage'] == 'order')
		{
			if(!preg_match('/^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/', $redata['money']))
			{
				$result = array(
					'status' => '0',
					'msg' => '充值金额格式错误',
					'data' => $redata
				);
				die(json_encode($result));
			}
		}
		else
		{
			if(preg_match('/^\d+$/', $redata['money']))
			{
				if($redata['money'] < 10)
				{
					$result = array(
						'status' => '0',
						'msg' => '请至少充值10元',
						'data' => $redata
					);
					die(json_encode($result));
				}
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '请输入整数金额',
					'data' => $redata
				);
				die(json_encode($result));
			}
		}

		// 红包使用条件检查
		if(!empty($redata['redpackId']))
		{
			$redpacks = array();
			$redpackArry = explode(',', $redata['redpackId']);
			// 查询红包信息
	        $this->load->model('redpack_model');
	        $this->eventType = $this->redpack_model->getEventType();
	        $redpackData = $this->redpack_model->getRedpackInfo($data['uid'], $this->eventType['recharge']);
	        
	        if(!empty($redpackData))
	        {
	        	$redpackInfo = array();
	        	foreach ($redpackData as $redpack) 
		        {
		        	$redpackInfo[$redpack['id']] = $redpack;
		        }

		        // 遍历用户所选红包 
		        $checkMoney = 0;
		        foreach ($redpackArry as $packItems) 
		        {
		        	$redPackStr = explode('#', $packItems);
		        	if(!empty($redpackInfo[$redPackStr[0]]))
			        {
			        	array_push($redpacks, $redPackStr[0]);
			        	$redpackParams = json_decode($redpackInfo[$redPackStr[0]]['use_params'], true);
			        	
			        	$checkMoney += ParseUnit($redpackParams['money_bar'], 1);
			        }
			        else
			        {
			        	$result = array(
							'status' => '2',
							'msg' => '当前红包不可用',
							'data' => ''
						);
						die(json_encode($result));
			        }
		        }	

		        // 总金额检查
		        if($checkMoney > $redata['money'])
	        	{
	        		$result = array(
						'status' => '2',
						'msg' => '当前红包不可用',
						'data' => ''
					);
					die(json_encode($result));
	        	}	        
	        }
	        else
	        {
	        	$result = array(
					'status' => '2',
					'msg' => '当前红包不可用',
					'data' => ''
				);
				die(json_encode($result));
	        }

	        // 判断是否存在重复红包
	        if(count($redpacks) != count(array_unique($redpacks)))
	        {
	        	$result = array(
					'status' => '2',
					'msg' => '当前红包不可用',
					'data' => ''
				);
				die(json_encode($result));
	        }

	        $redata['redpackId'] = implode(',', $redpacks);
		}

		// 充值开启状态
		$is_recharge = $this->config->item('is_recharge');
		if(!$is_recharge)
		{
			$result = array(
				'status' => '0',
				'msg' => '因彩票停售，充值通道暂时关闭',
				'data' => $redata
			);
			die(json_encode($result));
		}

		// 充值渠道判断
		switch ($redata['pay_type']) 
		{
			case 'alipaysdk':
				$returnData = $this->doAlipayRecharge($data['uid'], $redata);				
				break;

			case 'shengpaysdk':
				$returnData = $this->doShengpayRecharge($data['uid'], $redata);	
				break;

			case 'shengpaywap':
				$returnData = $this->doShengpaywapRecharge($data['uid'], $redata);	
				break;
			default:
				$returnData = array(
					'status' => '0',
					'msg' => '支付类型异常',
					'data' => ''
				);
				break;
		}

		// 处理结果
		if( $returnData['status'] == '1' )
		{
			$result = array(
				'status' => '1',
				'msg' => '创建订单成功',
				'data' => $returnData['data'],
				'token' => $returnData['token']
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => $returnData['msg'],
				'data' => $redata,
				'token' => ''
			);
		}

		die(json_encode($result));
	}

	/*
 	 * APP 支付宝SDK充值
 	 * @date:2015-05-07
 	 */
	public function doAlipayRecharge($uid, $redata)
	{
		// 组装支付宝SDK调用参数
		$parmas = array(
			'trade_no' => $this->tools->getIncNum('UNIQUE_KEY'),
			'mid' => 'CP',
			'pay_type' => $redata['pay_type'],
			'total_fee' => $redata['money'],
			'bank' => 'directPay',
			'subject' => '购买彩票',
			'alibody' => '支付宝充值彩金',
			'buyer' => $uid,
			'dateline' => time(),
			'token' => '',
			'salt' => ''
		);
		//获取 salt
		$parmas['salt'] = $this->Wallet->GetSalt();
		$parmas['token'] = md5("{$parmas[trade_no]}{$parmas[mid]}{$parmas[pay_type]}{$parmas[total_fee]}{$parmas[bank]}{$parmas[dateline]}{$parmas[salt]}");

		// LOG
    	// log_message('LOG', "充值 - 支付中心请求参数: " . json_encode($parmas), 'recharge');

		// 支付中心生成流水记录
		if(ENVIRONMENT === 'production')
		{
			$res = $this->Wallet->doPay($parmas, $redata['pay_type']);
		}
		else
		{
			$res = true;
		}

		// 支付中心创建成功，充值流水入库
		if($res)
		{
			$walletData = array(
				'uid' => $uid,
				'trade_no' => $parmas['trade_no'],
				'orderId' => $redata['orderId'],
				'status' => $redata['orderType'],
				'additions' => "{$parmas[pay_type]}@{$parmas[bank]}",
				'money' => ParseUnit($parmas['total_fee']),
				'mark' => '2',
				'platform' => $redata['platform'],			
				'app_version' => $redata['app_version'],
				'channel' => $redata['channel']
			);

			// 入库 cp_wallet_log
			$resLog = $this->Wallet->recordWalletLog($walletData);

			if($resLog)
			{
				// 调用支付宝SDK参数
				$this->config->load('pay');
				$alipay = $this->config->item('alipaysdk');

				// 组装数据
				$payData = array(
					'partner' => $alipay['partner'],
					'seller' => $alipay['seller'],
					'notify_url' => $alipay['notify_url'],
					'rsa_private' => $alipay['rsa_private'],
					'orderid' => $parmas['mid'] . '_' . $parmas['trade_no'],
					'price' => $parmas['total_fee'],
					'subject' => $parmas['subject'],
					'body' => $parmas['alibody'],
					'redirectPage' => $redata['redirectPage'],	// 返回商户跳转类型
					'cp_orderId' => $redata['orderId'],			// 彩票购彩订单号
					'lid' => $redata['lid']
				);

				// LOG
    			// log_message('LOG', "充值 - 支付宝请求参数: " . json_encode($payData), 'recharge');
				
				// 加密
				$sdkJs = $this->strCode(json_encode($payData), 'ENCODE');

				// token验证
				$token = md5("{$payData[orderid]}{$payData[price]}{$parmas['pay_type']}{$this->salt}");

				$result = array(
					'status' => '1',
					'msg' => '创建订单成功',
					'data' => $sdkJs,
					'token' => $token
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '流水记录错误,创建订单失败',
					'data' => ''
				);
			}
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '支付中心异常,创建订单失败',
				'data' => ''
			);
		}
		return $result;
	}
	
	/*
 	 * APP 盛付通SDK充值
 	 * @date:2015-05-07
 	 */
	public function doShengpayRecharge($uid, $redata)
	{
		$parmas = array(
			'trade_no' => $this->tools->getIncNum('UNIQUE_KEY'),
			'mid' => 'CP',
			'pay_type' => $redata['pay_type'],
			'total_fee' => $redata['money'],
			'bank' => 'directPay',
			'subject' => '购买彩票',
			'alibody' => '盛付通充值彩金',
			'buyer' => $uid,
			'dateline' => time(),
			'token' => '',
			'salt' => ''
		);
		//获取 salt
		$parmas['salt'] = $this->Wallet->GetSalt();
		$parmas['token'] = md5("{$parmas[trade_no]}{$parmas[mid]}{$parmas[pay_type]}{$parmas[total_fee]}{$parmas[bank]}{$parmas[dateline]}{$parmas[salt]}");
		
		// LOG
    	// log_message('LOG', "充值 - 支付中心请求参数: " . json_encode($parmas), 'recharge');

		// 支付中心生成流水记录
		if(ENVIRONMENT === 'production')
		{
			$res = $this->Wallet->doPay($parmas, $redata['pay_type']);
		}
		else
		{
			$res = true;
		}	

		// 支付中心创建成功，充值流水入库
		if($res)
		{
			$walletData = array(
				'uid' => $uid,
				'trade_no' => $parmas['trade_no'],
				'orderId' => $redata['orderId'],
				'status' => $redata['orderType'],
				'additions' => "{$parmas[pay_type]}@{$parmas[bank]}",
				'money' => ParseUnit($parmas['total_fee']),
				'mark' => '2',
				'red_pack' => $redata['redpackId'],
				'platform' => $redata['platform'],
				'app_version' => $redata['app_version'],
				'channel' => $redata['channel']
			);

			// 入库 cp_wallet_log
			$resLog = $this->Wallet->recordWalletLog($walletData);

			if($resLog)
			{
				// 调用支付宝SDK参数
				$this->config->load('pay');
				$shengpay = $this->config->item('shengpaysdk');

				// 组装数据
				$payData = array(
					'partner' => $shengpay['partner'],
					'seller' => $shengpay['seller'],
					'notify_url' => $shengpay['notify_url'],
					'rsa_private' => $shengpay['rsa_private'],
					'orderid' => $parmas['mid'] . '_' . $parmas['trade_no'],
					'price' => $parmas['total_fee'],
					'subject' => $parmas['subject'],
					'body' => $parmas['alibody'],
					'redirectPage' => $redata['redirectPage'],
					'cp_orderId' => $redata['orderId'],
					'lid' => $redata['lid']
				);

				// LOG
    			// log_message('LOG', "充值 - 支付宝请求参数: " . json_encode($payData), 'recharge');
				
				// 加密
				$sdkJs = $this->strCode(json_encode($payData), 'ENCODE');

				// token验证
				$token = md5("{$payData[orderid]}{$payData[price]}{$parmas['pay_type']}{$this->salt}");

				$result = array(
					'status' => '1',
					'msg' => '创建订单成功',
					'data' => $sdkJs,
					'token' => $token
				);
			}
			else
			{
				$result = array(
					'status' => '0',
					'msg' => '流水记录错误,创建订单失败',
					'data' => ''
				);
			}

		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '支付中心异常,创建订单失败',
				'data' => ''
			);
		}
		return $result;
	}


	/*
 	 * APP 盛付通WAP充值
 	 * @date:2015-05-07
 	 */
	public function doShengpaywapRecharge($uid, $redata)
	{
		// 支付中心跳转参数组装
		$parmas = array(
			'mid' => 'CP',
			'OrderNo' => $this->tools->getIncNum('UNIQUE_KEY'),
			'OrderAmount' => $redata['money'],
			'OrderTime' => date('YmdHis', time()),
			'BuyerIp' => UCIP,
			'ProductName' => '2345CP',
			'PayType' => $redata['pay_type'],
			'token' => '',
			'salt' => ''
		);

		//获取 salt
		$parmas['salt'] = $this->Wallet->GetSalt();
		$parmas['token'] = md5("{$parmas[OrderNo]}{$parmas[mid]}{$parmas[OrderAmount]}{$parmas[OrderTime]}{$parmas[BuyerIp]}{$parmas[ProductName]}{$parmas[salt]}");

		// 创建充值流水
		$walletData = array(
			'uid' => $uid,
			'trade_no' => $parmas['OrderNo'],
			'orderId' => $redata['orderId'],
			'status' => $redata['orderType'],
			'additions' => "{$parmas[PayType]}",
			'money' => ParseUnit($parmas['OrderAmount']),
			'mark' => '2',
			'red_pack' => $redata['redpackId'],
			'platform' => $redata['platform'],
			'app_version' => $redata['app_version'],
			'channel' => $redata['channel']
		);

		// 入库 cp_wallet_log
		$resLog = $this->Wallet->recordWalletLog($walletData);

		if($resLog)
		{
			$result = array(
				'status' => '1',
				'msg' => '创建订单成功',
				'data' => $parmas,
				'token' => ''
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => '流水记录错误,创建订单失败',
				'data' => '',
				'token' => ''
			);
		}
		return $result;
	}
}