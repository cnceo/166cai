<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 新版支付
 * @date:2017-04-13
 */
require_once APPPATH . '/core/CommonController.php';
class Recharge extends CommonController 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('pay_model');
        $this->load->model('wallet_model', 'Wallet');
    }

    /**
     * 充值网关请求通用接口
     * @param unknown_type $libery
     */
    public function request($libery = '')
    {
        $params = array(
            'trade_no'  =>  $this->input->post('trade_no', true),
            'money'     =>  $this->input->post('money', true),
            'configId'  =>  $this->input->post('configId', true),
            'uid'       =>  $this->input->post('uid', true),
            'real_name' =>  $this->input->post('real_name', true),
            'id_card'   =>  $this->input->post('id_card', true),
            'ip'        =>  $this->input->post('ip', true),
            'bank_id'   =>  $this->input->post('bank_id', true),
            'pay_agreement_id'  =>  $this->input->post('pay_agreement_id', true),
        );
        $respone = array(
            'code' => false,
            'msg'  => '请求充值类型错误',
            'data' => $params,
        );

        if($libery && !empty($params) && !empty($params['configId']))
        {
            require_once APPPATH . '/libraries/recharge/' . $libery . '.php';
            if(class_exists($libery))
            {
                $configData = $this->pay_model->getPayConfig($params['configId']);
                $config = array();
                if(!empty($configData['extra']))
                {
                    $config = json_decode($configData['extra'], true);
                }
                $paySubmit = new $libery($config);
                $rparams = array(
                    'trade_no'  =>  $params['trade_no'],
                    'money'     =>  $params['money'],
                    'uid'       =>  $params['uid'],
                    'real_name' =>  $params['real_name'],
                    'id_card'   =>  $params['id_card'],
                    'ip'        =>  $params['ip'],
                    'bank_id'   =>  $params['bank_id'],
                    'pay_agreement_id'  =>  $params['pay_agreement_id'],
                );
                $respone = $paySubmit->requestHttp($rparams);
            }
        }
        exit(json_encode($respone));
    }
    
    /**
     * 充值异步通知通用接口
     * @param string $libery	充值类名
     * @param int $configId		配置id
     */
    public function notice($libery, $configId)
    {
        //番茄关闭报警通知
        if($libery == 'TomatoPay' && $configId < 258){
            die('failure');
        }
    	if($libery && $configId)
    	{
    		require_once APPPATH . 'libraries/recharge/'. $libery . '.php';
    		if (class_exists($libery)) 
    		{
    			$configData = $this->pay_model->getPayConfig($configId);
    			if($configData)
    			{
    				$config = json_decode($configData['extra'], true);
    				$payNotify = new $libery($config);
    				$result = $payNotify->notify();
                    if($libery == 'XmPay')
                    {
                        $trade = $this->pay_model->getTradenoByToken($result['data']['trade_no']);
                        $result['data']['trade_no'] = $trade['trade_no'];
                    }
    				if($result['code'])
    				{
    					$res = $this->pay_model->handleRechargeSucc($result['data']);
    					if($res)
    					{
                            $this->updatePayBank($result['data']);
    						die($result['succMsg']);
    					}
    				}
    				die($result['errMsg']);
    			}
    		}
    	}
    	
    	die('failure');
    }
    
    
	/**
	 * 充值同步回调接口
	 * @param string $libery	充值类名
	 * @param int $configId		配置id
	 */
    public function syncCallback($libery, $configId)
    {
        //对同步地址修正
        $this->repair();
        if($libery && $configId)
    	{
            require_once APPPATH . 'libraries/recharge/'. $libery . '.php';
    		if (class_exists($libery))
    		{
    			$configData = $this->pay_model->getPayConfig($configId);
    			if($configData)
    			{
    				$config = json_decode($configData['extra'], true);
    				$payNotify = new $libery($config);
                    $result = $payNotify->syncCallback();
                    $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';
                    $orderType = isset($_GET['orderType']) ? $_GET['orderType'] : '';
                    $buyMoney = isset($_GET['buyMoney']) ? $_GET['buyMoney'] : '';
                    if($result['code'])
                    {
                        $urlData = $this->getSuccUrl($result['data']['trade_no'],$orderId,$orderType,$buyMoney);
                        if($urlData['code'] && !empty($urlData['url']))
                        {
                            header('Location: ' . $urlData['url']);
                            die();
                        }
                    }else{
                        if(isset($_GET['trade_no']) && in_array($libery, array('HjPay','WftPay', 'BbnPay', 'TomatoPay', 'UlinePay', 'YzPay')))
                        {

                            $urlData =  $this->getSuccUrl($_GET['trade_no'],$orderId,$orderType);
                            if($urlData['code'] && !empty($urlData['url']))
                            {
                                header('Location: ' . $urlData['url']);
                                die();
                            }
                        }
                    }
    			}
    		}
    	}else{
          die('failure');  
        }
    	
    	
    }
    
    /**
     * 同步和异步地址相同时调用
     * @param string $libery	充值类名
	 * @param int $configId		配置id
     */
    public function syncAnotice($libery, $configId)
    {
        //同步地址时候修正
        $this->repair();
        if($libery && $configId)
    	{
    		require_once APPPATH . 'libraries/recharge/'. $libery . '.php';
    		if (class_exists($libery))
    		{
    			$configData = $this->pay_model->getPayConfig($configId);
    			if($configData)
    			{
    				$config = json_decode($configData['extra'], true);
    				$payNotify = new $libery($config);
    				$result = $payNotify->notify();
    				if($result['code'])
    				{
    					if($result['isSync'])
    					{
    						//同步回调操作
                            $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';
                            $orderType = isset($_GET['orderType']) ? $_GET['orderType'] : '';
                            $buyMoney = isset($_GET['buyMoney']) ? $_GET['buyMoney'] : '';
    						$urlData = $this->getSuccUrl($result['data']['trade_no'],$orderId,$orderType,$buyMoney);
    						if($urlData['code'] && !empty($urlData['url']))
    						{
    							header('Location: ' . $urlData['url']);
    							die();
    						}
    					}
    					else
    					{
    						//异步回调操作
    						$res = $this->pay_model->handleRechargeSucc($result['data']);
    						if($res)
    						{
    							die($result['succMsg']);
    						}
    					}
    				}
    	
    				die($result['errMsg']);
    			}
    		}
    	}
    	 
    	die('failure');
    }
    
    /**
     * 同步通知获取跳转链接
     * @param unknown_type $trade_no
     */
    private function getSuccUrl($trade_no,$orderId='',$orderType='',$buyMoney='')
    {
    	$returnData = array(
    		'code' => false,
    		'url'  => ''
    	);
    	$this->load->model('wallet_model');
    	$walletInfo = $this->wallet_model->getWalletLog($trade_no);
    	if(empty($walletInfo))
    	{
    		return $returnData;
    	}
    	
    	// 更新同步通知标识
    	$data['sync_flag'] = 1;
    	$res = $this->pay_model->updatePayLog($walletInfo['trade_no'], $data);
    	//if($res)
    	{
    		if($walletInfo['platform'])
    		{
    			// 用户信息加密
    			$rechargeData = array(
    				'uid' => $walletInfo['uid'],    			// 用户ID
    				'tradeNo' => $walletInfo['trade_no'],    	// wallet_log流水号
    				'redirectPage' => (!empty($walletInfo['orderId'])) ? 'order' : 'recharge',   // 跳转类型 充值详情、订单支付页
    				'cp_orderId' => $walletInfo['orderId']  	// 订单号
    			);
    				
    			$token = $this->pay_model->strCode(json_encode($rechargeData), 'ENCODE');
    		}
    	
    		switch ($walletInfo['platform'])
    		{
    			case '0':
    				$url = $this->config->item('pages_url') . 'mylottery/rchagscess/' . $walletInfo['trade_no'];
    				break;
    			case '1':
    				$url = $this->config->item('app_pages_url') . 'app/wallet/rechargeComplete/' . urlencode($token);
    				break;
    	
    			case '2':
    				$url = $this->config->item('app_pages_url') . 'ios/wallet/rechargeComplete/' . urlencode($token);
    				break;
    	
    			case '3':
    				// 暂不支持
    				$url = $this->config->item('m_pages_url') . 'wallet/rechargeComplete/' . $walletInfo['trade_no'].'/'.$orderId.'/'.$orderType.'/'.$buyMoney;
    				break;
    			default:
    				break;
    		}
    	}
    	if(!empty($url))
    	{
    		$returnData = array(
    			'code' => true,
    			'url' => $url,
    		);
    	}
    	
    	return $returnData;
    }

    /**
     * 支付补单查询接口
     * @param string $trade_no  交易流水号
     */
    public function orderSelect($trade_no,$succFlag='')
    {
        if(!in_array($this->get_client_ip(), $this->config->item('own_ip')) && empty($succFlag) )
        {
            die('{"code":1,"msg":"操作失败"}');
        }
        $walletlog = $this->Wallet->getPayDetail($trade_no);
        if(!$walletlog || $walletlog['ctype'] != 0)
        {
            die('{"code":1,"msg":"操作失败"}');
        }
        $additionData = array(
        	'yeepayWangy' => 'YeepayWPay',
        	'yeepayCredit' => 'YeepayWPay',
        	'yeepayKuaij' => 'YeepayWPay',
        	'yeepayWeix' => 'YeepayWPay',
        	'yeepayMPay' => 'YeepayMPay',
        	'zxwxSdk' => 'ZxWeixinPay',
        	'payWeix' => 'ZxWeixinPay',
            'sumpayWap' => 'SumPay', 
            'sumpayWeb' => 'SumPay', 
            'xzZfbWap'=> 'XzPay',
        	'payZfb'  => 'WftPay',
        	'wftWxSdk' => 'WftPay',
        	'wftWx' => 'WftPay',
        	'jdPay' => 'JdPay',
            'umPay' => 'UmPay',
            'hjZfbPay' => 'HjPay',
            'wftZfbWap'=> 'WftPay',
            'xzpay'=> 'XzPay',
            'hjpay' => 'HjPay',
            'wftpay'=> 'WftPay',
            'wzPay'=> 'WzPay',
            'hjWxWap' => 'HjPay',
            'hjZfbWap' => 'HjPay',
            'wftWxWap' => 'WftPay',
            'payPaZfb' => 'PaPay',
            'payXmZfb' => 'XmPay',
            'pfWxWap'  => 'BbnPay',
            'payYlyZf' => 'YlyPay',
            'yzpayh'   => 'YzPay',
            'llpayWeb' => 'LlPay',
            'tomatoZfbWap' => 'TomatoPay',
            'ulineWxWap' => 'UlinePay',
            'hjZfbSh' => 'HjPay',
            'yzWxWap' => 'YzPay',
            'jdSdk'    => 'JdSdk',
            'wftwxzx'  => 'WftPay',
            'wftzfbzx'  => 'WftPay',
            'tomatoWxWap' => 'TomatoPay',
        );
        
        if(!empty($walletlog['rcg_serial']) && isset($additionData[$walletlog['additions']]))
        {
        	require_once APPPATH . 'libraries/recharge/'. $additionData[$walletlog['additions']] . '.php';
        	$configData = $this->pay_model->getPayConfig($walletlog['rcg_serial']);
        	if($configData)
        	{
        		$config = json_decode($configData['extra'], true);
        		$library = new $additionData[$walletlog['additions']]($config);
        		$params = array(
        			'trade_no' => $walletlog['trade_no'],
        			'additions' => $walletlog['additions'],
                                'created' => $walletlog['created'],
        		);
                        if($additionData[$walletlog['additions']] == 'XmPay')
                        {
                            $params['token'] = $walletlog['content'];
                        }
                        if($additionData[$walletlog['additions']] == 'TomatoPay')
                        {
                            $params['pay_trade_no'] = $walletlog['pay_trade_no'];
                        }
        		$result = $library->queryOrder($params);
        		$return = $result['data'];
        		if($result['code'])
        		{
        			$return['isDone'] = 0;
                                $return['mer_id'] = $configData['mer_id'];
        			if(($walletlog['mark'] == '2') && ($result['data']['ispay'] == true))
        			{
        				$payData = array(
        					'trade_no' => $walletlog['trade_no'],
        					'pay_trade_no' => $result['data']['pay_trade_no'],
        					'status' => '1',    // 成功
        				);
        				//补单操作
        				$hres = $this->pay_model->handleRechargeSucc($payData);
        				if($hres)
        				{
        					$return['isDone'] = 1;
        				}
        			}
        		}
        		die(json_encode($return));
        	}
        	
        	die('{"code":1,"msg":"操作失败"}');
        }
        else
        {
        	$this->oldOrderSelect($walletlog);
        }
    }
    
    /**
     * 退款操作
     */
    public function orderRefund()
    {
    	if(!in_array($this->get_client_ip(), $this->config->item('own_ip')))
    	{
    		die('{"code":1,"msg":"操作失败"}');
    	}
    	$trade_no = $this->input->post('trade_no', true);
    	$money = trim($this->input->post('money', true));
    	$walletlog = $this->Wallet->getPayDetail($trade_no);
    	if(!$walletlog || $walletlog['ctype'] != 0 || $walletlog['mark'] != 1)
    	{
    		die('{"code":1,"msg":"操作失败"}');
    	}
    	$money = ParseUnit($money);
    	if($money > $walletlog['money'])
    	{
    		die('{"code":1,"msg":"退款金额不能大于充值金额"}');
    	}
    	 
    	$additionData = array(
    			'zxwxSdk' => 'ZxWeixinPay',
    			'payWeix' => 'ZxWeixinPay',
    	);
    	 
    	if(isset($additionData[$walletlog['additions']]))
    	{
    		require_once APPPATH . 'libraries/recharge/'. $additionData[$walletlog['additions']] . '.php';
    		$configData = $this->pay_model->getPayConfig($walletlog['rcg_serial']);
    		if($configData)
    		{
    			$this->load->library('tools');
    			$config = json_decode($configData['extra'], true);
    			$library = new $walletlog['additions']($config);
    			$params = array(
    					'trade_no' => $walletlog['trade_no'],
    					'additions' => $walletlog['additions'],
    					'created' => $walletlog['created'],
    					'refundId' => $this->tools->getIncNum('UNIQUE_KEY'),
    					'money' => $money,
    					'uid' => $walletlog['uid'],
    					'pay_type' => $walletlog['pay_type']
    			);
    			$result = $library->queryOrder($params);
    			$return = $result['data'];
    			if($result['code'])
    			{
    				$res = $this->pay_model->recordRefundLog($result['data']);
    				if($res)
    				{
    					die('{"code":0,"msg":"操作成功"}');
    				}
    				else
    				{
    					die('{"code":1,"msg":"操作失败"}');
    				}
    			}
    			 
    			die(json_encode($return));
    		}
    	}
    	 
    	die('{"code":1,"msg":"操作失败"}');
    }
    
    /**
     * 支付补单查询接口(老接口 以后废弃)
     */
    private function oldOrderSelect($walletlog)
    {
    	$this->config->load('pay');
    	$this->load->library('tools');
    	switch ($walletlog['additions'])
    	{
    		case 'sumpayWeb':
    		case 'sumpayWap':
    			$this->load->library('sumpay/Sumpayfuns');
    			$config = $this->config->item($walletlog['additions']);
    			$params = array(
    					'version'   => $config['version'],
    					'service' => 'sumpay.trade.order.search',
    					'format' => 'JSON',
    					'app_id' => $config['app_id'],
    					'timestamp' => date('YmdHis', time()),
    					'terminal_type' => $config['terminal_type'],
    					'sign_type' => $config['sign_type'],
    					'mer_id' => $config['mer_id'],
    					'order_no' => $walletlog['trade_no']
    			);
    			$params['sign'] = $this->sumpayfuns->init($params);
    			$respone = $this->tools->request($config['apiUrl'], $params);
    			$respone = json_decode($respone, true);
    			if($respone['resp_code'] == '000000')
    			{
    				$ptype = array('sumpayWeb' => '统统付快捷', 'sumpayWap' => '统统付Wap');
    				$pstatus = array('0' => '失败', '1' => '已付款', '2' => '处理中', '00' => '未支付');
    				$return['code'] = 0;
    				$return['ptype'] = $ptype[$walletlog['additions']];
    				$return['pstatus'] = $pstatus[$respone['status']];
    				$return['pmoney'] = number_format($respone['succ_amt'], 2, ".", ",");
    				$return['ptime'] = $respone['succ_time'] > 0 ? date('Y-m-d H:i:s', strtotime($respone['succ_time'])) : '';
    				$return['pbank'] = '';
    				$return['isDone'] = 0; //是否补单
    				if($walletlog['mark'] == '2' && $respone['status'] == '1')
    				{
    					$payData = array(
    							'trade_no' => $walletlog['trade_no'],
    							'pay_trade_no' => $respone['serial_no'],
    							'status' => '1',	// 成功
    					);
    					//补单操作
    					$hres = $this->pay_model->handleRechargeSucc($payData);
    					if($hres)
    					{
    						$return['isDone'] = 1;
    					}
    				}
    				die(json_encode($return));
    			}
    			else
    			{
    				die('{"code":1,"msg":"' . $respone['resp_msg'] . '"}');
    			}
    			break;
    		case 'zxwxSdk':
    		case 'payWeix':
    			require_once APPPATH . '/libraries/weixinzxpay/pay_submit.class.php';
    			$config = $walletlog['additions'] == 'zxwxSdk' ? $this->config->item('zxwxSdk') : $this->config->item('zxpayWeb');
    			$params = array(
    					'encoding' => $config['encoding'],			//编码方式
    					'signMethod' => $config['signMethod'], 		//充值金额 单位:元，精确到分.
    					'txnType' => '38',
    					'txnSubType' => '383000', 						//交易子类型 010130：二维码支付 010131：公众号支付 010132：APP支付（主扫）
    					'channelType' => '6002', 						//接入渠道  6002：商户互联网渠道;
    					'payAccessType' => '02', 						//接入支付类型 02：接口支付
    					'merId' => $config['merId'], 					//普通商户或一级商户的商户号
    					'origOrderId' => $walletlog['trade_no'],		//商户订单号
    					'fetchOrderNo' => 'Y',
    					'origOrderTime' => date('YmdHis', strtotime($walletlog['created'])),
    					'orderTime' => date('YmdHis', time()),
    			);
    			//建立请求
    			$paySubmit = new paySubmit($config);
    			$respone = $paySubmit->buildOrderSelectHttp($params);
    			if($respone['respCode'] == '0000')
    			{
    				$ptype = array('payWeix' => '中信微信', 'zxwxSdk' => '中信微信SDK');
    				$pstatus = array('SUCCESS' => '已付款', 'NOTPAY' => '未支付');
    				$return['code'] = 0;
    				$return['ptype'] = $ptype[$walletlog['additions']];
    				$return['pstatus'] = $pstatus[$respone['origRespCode']];
    				$return['ptime'] = ''; //中信未返回支付时间
    				$return['pmoney'] = number_format($respone['txnAmt'] / 100, 2, ".", ",");
    				$return['pbank'] = '';
    				$return['isDone'] = 0; //是否补单
    				if($walletlog['mark'] == '2' && $respone['origRespCode'] == 'SUCCESS')
    				{
    					$payData = array(
    							'trade_no' => $walletlog['trade_no'],
    							'pay_trade_no' => $respone['origSeqId'],
    							'status' => '1',	// 成功
    					);
    					//补单操作
    					$hres = $this->pay_model->handleRechargeSucc($payData);
    					if($hres)
    					{
    						$return['isDone'] = 1;
    					}
    				}
    				die(json_encode($return));
    			}
    			else
    			{
    				die('{"code":1,"msg":"' . $respone['respCode'] . $respone['respMsg'] . '"}');
    			}
    			break;
    		case 'yeepayWangy':
    		case 'yeepayCredit':
    		case 'yeepayKuaij':
    		case 'yeepayWeix':
    			$this->load->library('yeepay/YeepayComm');
    			$config = $this->config->item('yeepayWeb');
    			$confArr = array('yeepayWeix' => 'weixin', 'yeepayKuaij' => 'weixin', 'yeepayWangy' => 'other', 'yeepayCredit' => 'other');
    			$config = $config[$confArr[$walletlog['additions']]];
    			$params = array(
    					'p0_Cmd' => 'QueryOrdDetail',
    					'p1_MerId' => $config['p1_MerId'],
    					'p2_Order' => $walletlog['trade_no'],
    					'pv_Ver' => '3.0',
    					'p3_ServiceType' => '2',
    			);
    			$encryParams = $params;
    			$encryParams['merchantKey'] = $config['merchantKey'];
    			$params['hmac'] = $this->yeepaycomm->getCommandHmacString($encryParams);
    			$url = 'https://cha.yeepay.com/app-merchant-proxy/command';
    			$respone = $this->tools->request($url, $params);
    			$respone = explode("\n", $respone);
    			$res = array();
    			foreach ($respone as $val)
    			{
    				$tmp = explode('=', $val);
    				if(isset($tmp[0]) && isset($tmp[1]))
    				{
    					$res[$tmp[0]] = $tmp[1];
    				}
    			}
    			if(isset($res['r1_Code']) && $res['r1_Code'] == '1')
    			{
    				$ptype = array('yeepayWangy' => '易宝网银', 'yeepayCredit' => '易宝信用卡', 'yeepayWeix' => '易宝微信', 'yeepayKuaij' => '易宝快捷');
    				$pstatus = array('INIT' => '未支付', 'CANCELED' => '已取消', 'SUCCESS' => '已支付');
    				$return['code'] = 0;
    				$return['ptype'] = $ptype[$walletlog['additions']];
    				$return['pstatus'] = $pstatus[$res['rb_PayStatus']];
    				$return['ptime'] = $res['rb_PayStatus'] == 'SUCCESS' ? date('Y-m-d H:i:s', strtotime($res['ry_FinshTime'])) : '';
    				$return['pmoney'] = number_format($res['r3_Amt'], 2, ".", ",");
    				$return['pbank'] = '';
    				$return['isDone'] = 0; //是否补单
    				if($walletlog['mark'] == '2' && $res['rb_PayStatus'] == 'SUCCESS')
    				{
    					$payData = array(
    							'trade_no' => $walletlog['trade_no'],
    							'pay_trade_no' => $res['r2_TrxId'],
    							'status' => '1',	// 成功
    					);
    					//补单操作
    					$hres = $this->pay_model->handleRechargeSucc($payData);
    					if($hres)
    					{
    						$return['isDone'] = 1;
    					}
    				}
    				die(json_encode($return));
    			}
    			else
    			{
    				die('{"code":1,"msg":"查询失败' . $res['r1_Code'] . '"}');
    			}
    			break;
    		case 'yeepayMPay':
    			$yeepay = $this->config->item('yeepay');
    			$initDatas['merchantaccount']    = $yeepay['merchantaccount'];
    			$initDatas['merchantPrivateKey'] = $yeepay['merchantPrivateKey'];
    			$initDatas['merchantPublicKey']  = $yeepay['merchantPublicKey'];
    			$initDatas['yeepayPublicKey']    = $yeepay['yeepayPublicKey'];
    
    			// 加载公共类
    			$this->load->library("yeepay/YeepayMPay");
    			$this->yeepaympay->init($initDatas);
    			$respone = $this->yeepaympay->getOrder($walletlog['trade_no']);
    			if($respone)
    			{
    				$pstatus = array('0' => '未支付', '1' => '已支付', '2' => '已撤销', '3' => '阻断交易', '4' => '失败', '5' => '处理中');
    				$return['code'] = 0;
    				$return['ptype'] = '易宝Wap';
    				$return['pstatus'] = $pstatus[$respone['status']];
    				$return['ptime'] = $respone['status'] == '1' ? date('Y-m-d H:i:s', $respone['closetime']) : '';
    				$return['pmoney'] = number_format($respone['sourceamount'] / 100, 2, ".", ",");
    				$return['pbank'] = $respone['bank'];
    				$return['isDone'] = 0; //是否补单
    				if($walletlog['mark'] == '2' && $respone['status'] == '1')
    				{
    					$payData = array(
    							'trade_no' => $walletlog['trade_no'],
    							'pay_trade_no' => $respone['yborderid'],
    							'status' => '1',	// 成功
    					);
    					//补单操作
    					$hres = $this->pay_model->handleRechargeSucc($payData);
    					if($hres)
    					{
    						$return['isDone'] = 1;
    					}
    				}
    				die(json_encode($return));
    			}
    			break;
    		case 'payZfb':
    		case 'wftWxSdk':
    		case 'wftWx':
    			require_once APPPATH . '/libraries/wftZfb/request.php';
    			$confArr = array('payZfb' => 'wftZfb', 'wftWxSdk' => 'wftWxSdk', 'wftWx' => 'wftWx');
    			$config = $this->config->item($confArr[$walletlog['additions']]);
    			$params = array(
    					'service' => 'unified.trade.query',
    					'version' => $config['version'],
    					'charset' => $config['charset'],
    					'mch_id' => $config['mch_id'],
    					'out_trade_no' => $walletlog['trade_no'],
    			);
    			//建立请求
    			$paySubmit = new Request($config);
    			$respone = $paySubmit->queryOrder($params);
    			if($respone['code'] == true)
    			{
    				if($respone['data']['result_code'] == 0 && $respone['data']['status'] == 0)
    				{
    					$ptype = array('payZfb' => '全付通支付宝', 'wftWxSdk' => '全付通微信SDK', 'wftWx' => '全付通微信PC');
    					$pstatus = array('SUCCESS' => '已付款', 'REFUND' => '转入退款', 'NOTPAY' => '未支付', 'CLOSED' => '已关闭', 'REVERSE' => '已冲正', 'REVOK' => '已撤销', 'REVOKED' => '已冲正', 'USERPAYING' => '用户支付中', 'PAYERROR' => '支付失败');
    					$return['code'] = 0;
    					$return['ptype'] = $ptype[$walletlog['additions']];
    					$return['pstatus'] = $pstatus[$respone['data']['trade_state']];
    					$return['ptime'] = $respone['data']['trade_state'] == 'SUCCESS' ? date('Y-m-d H:i:s', strtotime($respone['data']['time_end'])) : '';
    					$return['pmoney'] = number_format($respone['data']['total_fee'] / 100, 2, ".", ",");
    					$return['pbank'] = '';
    					$return['isDone'] = 0; //是否补单
    					if($walletlog['mark'] == '2' && $respone['data']['trade_state'] == 'SUCCESS')
    					{
    						$payData = array(
    								'trade_no' => $walletlog['trade_no'],
    								'pay_trade_no' => $respone['data']['transaction_id'],
    								'status' => '1',	// 成功
    						);
    						//补单操作
    						$hres = $this->pay_model->handleRechargeSucc($payData);
    						if($hres)
    						{
    							$return['isDone'] = 1;
    						}
    					}
    					die(json_encode($return));
    				}
    				 
    				die('{"code":1,"msg":"' . $respone['data']['err_msg'] . '"}');
    			}
    			else
    			{
    				die('{"code":1,"msg":"' . $respone['data']['message'] . '"}');
    			}
    			break;
    		case 'xzZfbWap':
    			$this->config->load('pay');
    			$payConfig = $this->config->item('xzZfbWap');
    
    			$params = array(
    					'appId' => $payConfig['appId'],
    					'trade_no' => $walletlog['trade_no'],
    					'secureKey' => $payConfig['secure_key']
    			);
    			$this->load->library('xianzaipay/XianZaiPay');
    			$respone = $this->xianzaipay->queryOrder($params);
    
    			if(!empty($respone))
    			{
    				$pstatus = array(
    						'A00I' => '订单未处理',
    						'A004' => '订单受理成功',
    						'A005' => '订单受理失败',
    						'A001' => '订单支付成功',
    						'A002' => '订单支付失败',
    						'A003' => '支付结果未知',
    						'A006' => '交易关闭'
    				);
    				$return['code'] = 0;
    				$return['ptype'] = '现在支付宝H5';
    				$return['pstatus'] = $pstatus[$respone['responseCode']];
    				$return['ptime'] = $respone['responseCode'] == 'A001' ? date('Y-m-d H:i:s', $respone['mhtOrderStartTime']) : '';
    				$return['pmoney'] = number_format($respone['mhtOrderAmt'] / 100, 2, ".", ",");
    				$return['pbank'] = '';
    				$return['isDone'] = 0; //是否补单
    				if($walletlog['mark'] == '2' && $respone['status'] == 'A001')
    				{
    					$payData = array(
    							'trade_no' => $walletlog['trade_no'],
    							'pay_trade_no' => $respone['nowPayOrderNo'],
    							'status' => '1',    // 成功
    					);
    					//补单操作
    					$hres = $this->pay_model->handleRechargeSucc($payData);
    					if($hres)
    					{
    						$return['isDone'] = 1;
    					}
    				}
    				die(json_encode($return));
    			}
    			else
    			{
    				die('{"code":1,"msg":"查询失败"}');
    			}
    			break;
    		default:
    			die('{"code":1,"msg":"操作失败"}');
    	}
    }

    // 更新卡前置信息
    public function updatePayBank($payData)
    {
        // 检查是否存在卡前置
        $walletlog = $this->Wallet->getPayDetail($payData['trade_no']);
        if(!empty($walletlog['bank_id']))
        {
            // 后四位检查
            $last_four_cardid = substr($walletlog['bank_id'], -4);
            if( (empty($payData['last_four_cardid'])) || (!empty($payData['last_four_cardid']) && $last_four_cardid == $payData['last_four_cardid']) )
            {
                $info = array(
                    'uid'       =>  $walletlog['uid'],
                    'bank_id'   =>  $walletlog['bank_id'],
                    'bank_type' =>  $payData['bank_type'] ? $payData['bank_type'] : '',
                    'pay_agreement_id'  =>   $payData['pay_agreement_id'] ? $payData['pay_agreement_id'] : '',
                    'library'   =>  strtolower($walletlog['additions']),
                );
                $this->load->model('pay_bank_model');
                $this->pay_bank_model->savePayBankInfo($info);
            }    
        }
    }

    /**
     * 解约支付协议
     */
    public function breakPayRequest()
    {
        $params = array(
            'uid'       =>  $this->input->post('uid', true),
            'bank_id'   =>  $this->input->post('bank_id', true),
        );

        // $params = array(
        //     'uid'       =>  '151',
        //     'bank_id'   =>  '62148502147137751',
        // );

        $respone = array(
            'code' => false,
            'msg'  => '解约支付错误',
            'data' => $params,
        );

        $this->load->model('pay_bank_model');

        if(!empty($params['uid']) && !empty($params['bank_id']))
        {
            $bankInfo = $this->pay_bank_model->getUserBankInfo($params['uid'], $params['bank_id']);

            if(!empty($bankInfo))
            {
                if(!empty($bankInfo['pay_agreement']))
                {
                    $agreement = json_decode($bankInfo['pay_agreement'], true);

                    // 当前支持卡前置的支付类型
                    $typeArr = array(
                        'umpay' =>  array(
                            'library'   =>  'UmPay',
                            'configId'  =>  '72',
                        )
                    );

                    foreach ($typeArr as $name => $items) 
                    {
                        if(!empty($agreement[$name]))
                        {
                            require_once APPPATH . '/libraries/recharge/' . $items['library'] . '.php';
                            if(class_exists($items['library']))
                            {
                                $configData = $this->pay_model->getPayConfig($items['configId']);
                                $config = array();
                                if(!empty($configData['extra']))
                                {
                                    $config = json_decode($configData['extra'], true);
                                }
                                $paySubmit = new $items['library']($config);
                                $rparams = array(
                                    'configId'  =>  $items['configId'],
                                    'uid'       =>  $params['uid'],
                                    'bank_id'   =>  $params['bank_id'],
                                    'pay_agreement_id' => $agreement[$name],
                                );
                                $paySubmit->breakPayRequest($rparams);
                            }
                        }
                    }
                }
                
                // 前端解绑
                $this->pay_bank_model->deleteBankInfo($params);

                $respone = array(
                    'code' => true,
                    'msg'  => '解约支付成功',
                    'data' => $params,
                );
            }
        }
        exit(json_encode($respone));
    }

    /**
     * 解约支付异步通知
     */
    public function breakPayNotify($libery, $configId = 72)
    {
        if($libery && $configId)
        {
            require_once APPPATH . 'libraries/recharge/'. $libery . '.php';
            if (class_exists($libery)) 
            {
                $configData = $this->pay_model->getPayConfig($configId);
                if($configData)
                {
                    $config = json_decode($configData['extra'], true);
                    $payNotify = new $libery($config);
                    $result = $payNotify->breakNotify();
                    if($result['code'])
                    {
                        $this->load->model('pay_bank_model');
                        $this->pay_bank_model->breakBankByNotify($result['data']);
                        die($result['succMsg']);
                    }
                    die($result['errMsg']);
                }
            }
        }
        
        die('failure');
    }

    /**
     * [repair 修复返回参数中问号问题]
     * @author LiKangJian 2017-10-30
     * @return [type] [description]
     */
    public function repair()
    {
        if(substr_count($_SERVER["REQUEST_URI"],'?' )==2)
        {
           
           $url = $_SERVER["REQUEST_URI"];
           $indexQ = intval(strrpos( $url,'?') );
           if (ENVIRONMENT === 'production')
           {
                $base_url = rtrim($this->config->item('pages_url'),'/' );
           }else{
                $base_url = '//123.59.105.39';
           }
           $url= $base_url.substr_replace($url,'&',$indexQ,1); 
           header('Location: ' . $url);
        }
    }
    /**
     * [success 自定义成功页面]
     * @author LiKangJian 2017-12-01
     * @return [type] [description]
     */
    public function success($trade_no='')
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
        if (ENVIRONMENT === 'production')
        {
            $base_url = rtrim($this->config->item('pages_url'),'/' );
        }else{
            $protocol = 'http:';
             $base_url = '//123.59.105.39';
        }
        $this->load->view('wallet/success',array('url'=>$protocol.$base_url."/api/recharge/orderSelect/$trade_no/1"));
    }
}
