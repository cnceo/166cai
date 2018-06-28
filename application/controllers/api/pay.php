<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 支付
 * @date:2016-03-29
 */
require_once APPPATH . '/core/CommonController.php';
class Pay extends CommonController 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('pay_model');
        $this->load->model('wallet_model','Wallet');
    }

    /**
	 * 易宝支付 移动端
	 * @date:2016-03-29
	 */
    public function yeepayMPay()
    {
    	$params = $this->input->post();

    	if(empty($params))
    	{
    		die("缺少必要参数");
    	}

    	$payData = array(
			'uid' => '',
			'trade_no' => '',
			'product_name' => '',
			'product_desc' => '',
    		'money' => '',
			'pay_time' => '',
			'ip' => '',
			'token' => ''
		);

		// 必要参数检查
		foreach ($payData as $key => $items) 
		{
			if($params[$key] === '' || !isset($params[$key]))
			{
                log_message('LOG', "请求参数: " . $key, 'yeepayMPay');
				die("缺少必要参数：" . $key);
			}
		}

		// 获取 salt
		$params['salt'] = $this->Wallet->GetSalt();
		// 参数加密检查
		$token = md5("{$params[trade_no]}{$params[uid]}{$params[money]}{$params[pay_time]}{$params[ip]}{$params[product_name]}{$params[product_desc]}{$params[salt]}");

		if($token != $params['token'])
		{
			die("参数校验错误");
		}

        // 检查实名
        $this->load->model('user_model','User');
        $uinfo = $this->User->getUserInfo($params['uid']);

        if(empty($uinfo['real_name']) || empty($uinfo['id_card']))
        {
            die("用户信息错误");
        }
        $params['real_name'] = $uinfo['real_name'];
        $params['id_card'] = $uinfo['id_card'];

    	$url = $this->pay_model->yeepayMPay($params);

    	echo $url;
    }

    /**
	 * 易宝支付 异步通知
	 * @date:2016-03-29
	 */
    public function yeepayMPayCallback()
    {
    	$params = $this->input->post();

    	if(empty($params['data']) || empty($params['encryptkey']))
    	{
    		echo "参数错误";die;
    	}

    	$res = $this->pay_model->yeepayMPayCallback($params);

    	if($res)
    	{
    		echo "SUCCESS";
    	}	
    }

    /**
	 * 易宝支付 前台同步通知
	 * @date:2016-03-29
	 */
    public function yeepayMPayReturnUrl()
    {
    	$params = $this->input->get();

        if(empty($params['data']) || empty($params['encryptkey']))
        {
            echo "参数错误";die;
        }

    	$this->pay_model->yeepayMPayReturnUrl($params);

    }

    /**
     * 易宝支付 web端微信
     * @date:2016-03-29
     */
    public function yeepayWebCallback()
    {
        $params = array();
        $params['r0_Cmd']   = $this->input->get_post('r0_Cmd', true);
        $params['r1_Code']  = $this->input->get_post('r1_Code', true);
        $params['r2_TrxId'] = $this->input->get_post('r2_TrxId', true);
        $params['r3_Amt']   = $this->input->get_post('r3_Amt', true);
        $params['r4_Cur']   = $this->input->get_post('r4_Cur', true);
        $params['r5_Pid']   = $this->input->get_post('r5_Pid', true);
        $params['r6_Order'] = $this->input->get_post('r6_Order', true);
        $params['r7_Uid']   = $this->input->get_post('r7_Uid', true);
        $params['r8_MP']    = $this->input->get_post('r8_MP', true);
        $params['r9_BType'] = $this->input->get_post('r9_BType', true); 
        $params['hmac']     = $this->input->get_post('hmac', true);
        log_message('LOG', "请求参数: " . json_encode($params), 'yeepayWeb');
        if(empty($params))
        {
            var_dump("参数错误");die;
        }
        $this->config->load('pay');
        $yeepayWeb = $this->config->item('yeepayWeb');
		$params['p1_MerId'] = $yeepayWeb['weixin']['p1_MerId'];
		$params['merchantKey'] = $yeepayWeb['weixin']['merchantKey'];
        $res = $this->pay_model->yeepayWebCallback($params);
    }

	/**
     * 易宝支付 web端其它
     * @date:2016-03-29
     */
    public function yeepayWebCallback1()
    {
        $params = array();
        $params['r0_Cmd']   = $this->input->get_post('r0_Cmd', true);
        $params['r1_Code']  = $this->input->get_post('r1_Code', true);
        $params['r2_TrxId'] = $this->input->get_post('r2_TrxId', true);
        $params['r3_Amt']   = $this->input->get_post('r3_Amt', true);
        $params['r4_Cur']   = $this->input->get_post('r4_Cur', true);
        $params['r5_Pid']   = $this->input->get_post('r5_Pid', true);
        $params['r6_Order'] = $this->input->get_post('r6_Order', true);
        $params['r7_Uid']   = $this->input->get_post('r7_Uid', true);
        $params['r8_MP']    = $this->input->get_post('r8_MP', true);
        $params['r9_BType'] = $this->input->get_post('r9_BType', true); 
        $params['hmac']     = $this->input->get_post('hmac', true);
        log_message('LOG', "请求参数: " . json_encode($params), 'yeepayWeb');
        if(empty($params))
        {
            var_dump("参数错误");die;
        }
        $this->config->load('pay');
        $yeepayWeb = $this->config->item('yeepayWeb');
		$params['p1_MerId'] = $yeepayWeb['other']['p1_MerId'];
		$params['merchantKey'] = $yeepayWeb['other']['merchantKey'];
        $res = $this->pay_model->yeepayWebCallback($params);
    }
    
    public function yeepayMPayCallback2()
    {
    	$params = $this->input->get();
    
    	$this->pay_model->yeepayMPayCallback2($params);
    }
    
    /**
     * 连连支付  网页端同步通知
     */
    public function llpayWebSync()
    {
    	require_once APPPATH . '/libraries/webllpay/lib/llpay_notify.class.php';
    	require_once APPPATH . '/libraries/webllpay/lib/llpay_cls_json.php';
    	$this->config->load('pay');
    	$llpayWeb = $this->config->item('llpayWeb');
    	$llpayNotify = new LLpayNotify($llpayWeb);
    	$verify_result = $llpayNotify->verifyReturn();
    	$params = array();
    	//验证成功
    	if($verify_result) 
    	{
    		//商户编号
    		$params['oid_partner'] = $_POST['oid_partner' ];
    		//签名方式
    		$params['sign_type'] = $_POST['sign_type' ];
    		//签名
    		$params['sign'] = $_POST['sign' ];
    		//商户订单时间
    		$params['dt_order'] = $_POST['dt_order' ];
    		//商户订单号
    		$params['no_order'] = $_POST['no_order' ];
    		//支付单号
    		$params['oid_paybill'] = $_POST['oid_paybill' ];
    		//交易金额
    		$params['money_order'] = $_POST['money_order' ];
    		//支付结果
    		$params['result_pay'] =  $_POST['result_pay'];
    		//清算日期
    		$params['settle_date'] =  $_POST['settle_date'];
    		//订单描述
    		$params['info_order'] =  $_POST['info_order'];
    		//支付方式
    		$params['pay_type'] =  $_POST['pay_type'];
    		//银行编号
    		$params['bank_code'] =  $_POST['bank_code'];
    		log_message('LOG', "请求参数: " . json_encode($params), 'llpayWeb');
    		if($params['result_pay'] == 'SUCCESS') 
    		{
    			$data['sync_flag'] = 1;
    			$this->pay_model->updatePayLog($params['no_order'], $data); //更新同步通知标识
    			$url = $this->config->item('pages_url') . '/mylottery/rchagscess/' . $params['no_order'];
    			header('Location: ' . $url);
    			die();
    		}
    		else 
    		{
    			echo "result_pay=" . $params['result_pay'];
    			die();
    		}
    	}
    	else 
    	{
    		//验证失败
    		echo "验证失败";
    	}
    }
    
    /**
     * 连连支付  网页端异步通知
     */
    public function llpayWebAsync()
    {
    	require_once APPPATH . '/libraries/webllpay/lib/llpay_notify.class.php';
    	$this->config->load('pay');
    	$llpayWeb = $this->config->item('llpayWeb');
    	$llpayNotify = new LLpayNotify($llpayWeb);
    	$verify_result = $llpayNotify->verifyNotify();
    	$params = array();
	    if ($verify_result) 
	    { //验证成功
			$is_notify = true;
			require_once APPPATH . '/libraries/webllpay/lib/llpay_cls_json.php';
			$json = new JSON;
			$str = file_get_contents("php://input");
			$val = $json->decode($str);
			$params['oid_partner'] = trim($val->{'oid_partner'});
			$params['dt_order'] = trim($val->{'dt_order'});
			$params['no_order'] = trim($val->{'no_order'});
			$params['oid_paybill'] = trim($val->{'oid_paybill'});
			$params['money_order'] = trim($val->{'money_order'});
			$params['result_pay'] = trim($val->{'result_pay'});
			$params['settle_date'] = trim($val->{'settle_date'});
			$params['info_order'] = trim($val->{'info_order'});
			$params['pay_type'] = trim($val->{'pay_type'});
			$params['bank_code'] = trim($val->{'bank_code'});
			$params['sign_type'] = trim($val->{'sign_type'});
			$params['sign'] = trim($val->{'sign'});
			log_message('LOG', "请求参数: " . json_encode($params), 'llpayWeb');
			$res = $this->pay_model->llpayWebCallback($params);
			if($res)
			{
				die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
			}
			else
			{
				die("{'ret_code':'9999','ret_msg':'交易失败'}");
			}
		} 
		else 
		{
			log_message('LOG', '异步通知 验证失败', 'llpayWeb');
			//验证失败
			die("{'ret_code':'9999','ret_msg':'验签失败'}");
		}
    }
    
    /**
     * 中信微信支付  网页端异步通知
     */
    public function zxpayWebAsync()
    {
    	require_once APPPATH . '/libraries/weixinzxpay/pay_notify.class.php';
    	$this->config->load('pay');
    	$zxpayWeb = $this->config->item('zxpayWeb');
    	$payNotify = new payNotify($zxpayWeb);
    	$result = file_get_contents("php://input");
    	$result = str_replace('#', '+', $result);
    	if(strpos($result, 'sendData=') === false)
    	{
    		$response = '{"respCode":"9999","respMsg":"交易失败"}';
    		$response = 'sendData=' . base64_encode($response);
    		die($response);
    	}
    	$str = substr($result, 9);
    	$verify_result = $payNotify->verifyNotify($str);
    	$params = array();
    	if ($verify_result)
    	{ //验证成功
    		$val = base64_decode($str);
			$val = json_decode($val, true);
    		$params['trade_no'] = $val['orderId'];
    		$params['respCode'] = $val['respCode'];
    		$params['pay_trade_no'] = $val['txnSeqId'];
    		$params['respMsg'] = $val['respMsg'];
    		$res = $this->pay_model->zxpayWebCallback($params);
    		if($res)
    		{
    			$response = '{"respCode":"0000","respMsg":"OK"}';
    			$response = 'sendData=' . base64_encode($response);
    			die($response); //请不要修改或删除
    		}
    	}
    	
    	$response = '{"respCode":"9999","respMsg":"交易失败"}';
    	$response = 'sendData=' . base64_encode($response);
    	die($response);
    }

    /**
     * 中信微信支付  APP-SDK异步通知
     */
    public function zxpaySdkAsync()
    {
        require_once APPPATH . '/libraries/weixinzxpay/pay_notify.class.php';
        $this->config->load('pay');
        $zxwxSdk = $this->config->item('zxwxSdk');
        $payNotify = new payNotify($zxwxSdk);
        $result = file_get_contents("php://input");
        $result = str_replace('#', '+', $result);
        if(strpos($result, 'sendData=') === false)
        {
            $response = '{"respCode":"9999","respMsg":"交易失败"}';
            $response = 'sendData=' . base64_encode($response);
            die($response);
        }
        $str = substr($result, 9);
        $verify_result = $payNotify->verifyNotify($str);
        $params = array();
        if ($verify_result)
        { //验证成功
            $val = base64_decode($str);
            $val = json_decode($val, true);
            $params['trade_no'] = $val['orderId'];
            $params['respCode'] = $val['respCode'];
            $params['pay_trade_no'] = $val['txnSeqId'];
            $params['respMsg'] = $val['respMsg'];
            $res = $this->pay_model->zxpayWebCallback($params);
            if($res)
            {
                $response = '{"respCode":"0000","respMsg":"OK"}';
                $response = 'sendData=' . base64_encode($response);
                die($response); //请不要修改或删除
            }
        }
        
        $response = '{"respCode":"9999","respMsg":"交易失败"}';
        $response = 'sendData=' . base64_encode($response);
        die($response);
    }

    /**
     * 统统付  移动端统一支付调用接口
     */
    public function sumpayWapInfo()
    {
        $postData = $this->input->post(null, true);

        // 获取支付配置
        $this->config->load('pay');
        $sumpayWapConfig = $this->config->item('sumpayWap');

        // 组装支付参数
        $payData = array(
            'sign_type'     =>  $sumpayWapConfig['sign_type'],
            'sign'          =>  '',
            'mer_id'        =>  $sumpayWapConfig['mer_id'],
            'app_id'        =>  $sumpayWapConfig['app_id'],
            'goods_name'    =>  '彩咖充值',
            'goods_num'     =>  $sumpayWapConfig['goods_num'],
            'goods_type'    =>  $sumpayWapConfig['goods_type'],
            'logistics'     =>  $sumpayWapConfig['logistics'],
            'cstno'         =>  $postData['cstno'],
            'order_no'      =>  $postData['order_no'],
            'order_time'    =>  $postData['order_time'],
            'order_amt'     =>  $postData['order_amt'],
            'notify_url'    =>  $sumpayWapConfig['notify_url'],
            'return_url'    =>  $sumpayWapConfig['return_url'],
            'terminal_type' =>  $sumpayWapConfig['terminal_type'],
            'version'       =>  $sumpayWapConfig['version'],
            'service'       =>  $sumpayWapConfig['service'],
            'cre_no'        =>  $postData['cre_no'],
            'card_holder_name'  =>  $postData['card_holder_name'],
            'trade_code'    =>  $sumpayWapConfig['trade_code'],
        );

        // 加密
        $this->load->library("sumpay/Sumpayfuns");
        $payData['sign'] = $this->sumpayfuns->init($payData);
        // 请求地址
        $payData['payUrl'] = $sumpayWapConfig['payUrl'];

        exit(json_encode($payData));
    }

    /**
     * 统统付  移动端异步通知
     */
    public function sumpayWapAsync()
    {
        $postData = $GLOBALS['HTTP_RAW_POST_DATA'];

        if(!empty($postData))
        {
            $postData = json_decode($postData, true);
            // 获取支付配置
            $this->config->load('pay');
            $sumpayWapConfig = $this->config->item('sumpayWap');

            // 验签
            $this->load->library("sumpay/Sumpayfuns");
            $source = $this->sumpayfuns->sortArrayData($postData);
            $verifyRes = $this->sumpayfuns->verify($source, $postData['sign'], $sumpayWapConfig['mer_id']);

            if($verifyRes)
            {
                // 验签成功
                $res = $this->pay_model->sumpayWapCallback($postData);
                log_message('LOG', "验签成功 - 订单信息: " . json_encode($postData), 'sumpayWapAsync');
            }
            else
            {
                // 验签失败
                log_message('LOG', "验签失败 - 订单信息: " . json_encode($postData), 'sumpayWapAsync');
            }
        }
        else
        {
            log_message('LOG', "请求参数错误 - 请求参数: " . json_encode($postRes), 'sumpayWapAsync');
        }
        
        // 只要接收到请求都返回 000000
        $res = array(
            'resp_code' => '000000',
            'resp_msg'  => '接收成功'
        );
        die(json_encode($res));
    }

    /**
     * 统统付  移动端同步通知
     */
    public function sumpayWapSync()
    {
        $postData = $this->input->post('res');
        // 调试
        // $postData = 'eyJyZXNwX2NvZGUiOiIwMDAwMDAiLCJyZXNwX21zZyI6InN1Y2Nlc3MiLCJzaWduX3R5cGUiOiJSU0EiLCJzaWduIjoiU1pSM3FZL0pueVBNM3d5eVV5WU51THF3TmpFb043NnNhaEltSUU5TnVYWWsrYUFUM2cwQlF0VXoxeWRocGU0anZzTnJyOTVVOGNuVTkxemIyMlkxTDA2a3VCdmdEWmltTGVLRWcvZlpXVDhUQ1pVOVpteHpId0JjVlBrU0dEalZ5cWZ6dWFhRkFxOUoxMmJKSmFJN2RncWV2YkJVSlZlWjBxVXl0UmxScDcwPSIsIm1lcl9pZCI6InMxMDAwMDAwNDAiLCJvcmRlcl9ubyI6IjIwMTYwNjI3MTEzNTUzNTc5Mjc0Iiwib3JkZXJfdGltZSI6IjIwMTYwNjI3MTEzNTUzIiwic2VyaWFsX25vIjoiVDMxOTQ3MSIsInN0YXR1cyI6IjEiLCJzdWNjX3RpbWUiOiIyMDE2MDYyNzExNTI0MiIsInN1Y2NfYW10IjoiMC4wMSJ9';
        $postData = urldecode($postData);
        $postData = base64_decode($postData);
        $postData = json_decode($postData, true);

        if($postData)
        {
            // 获取支付配置
            $this->config->load('pay');
            $sumpayWapConfig = $this->config->item('sumpayWap');

            // 验签
            $this->load->library("sumpay/Sumpayfuns");
            $source = $this->sumpayfuns->sortArrayData($postData);
            $verifyRes = $this->sumpayfuns->verify($source, $postData['sign'], $sumpayWapConfig['mer_id']);

            if($verifyRes)
            {
                // 验签成功
                $this->pay_model->appSyncCallBack($postData['order_no']);
            }
            else
            {
                // 验签失败
                log_message('LOG', "请求参数验签失败 - 请求参数: " . json_encode($postData), 'sumpayWapSync');
                echo "请求参数错误，验签失败！";
                die;
            }
        }
        
    }

    /**
     * 统统付  网页网关同步通知
     */
    public function sumpayWebSync()
    {
        $postData = $this->input->post('res');
        $postData = urldecode($postData);
        $postData = base64_decode($postData);
        $postData = json_decode($postData, true);

        if($postData)
        {
            // 获取支付配置
            $this->config->load('pay');
            $sumpayWebConfig = $this->config->item('sumpayWeb');

            // 验签
            $this->load->library("sumpay/Sumpayfuns");
            $source = $this->sumpayfuns->sortArrayData($postData);
            $verifyRes = $this->sumpayfuns->verify($source, $postData['sign'], $sumpayWebConfig['mer_id']);

            if($verifyRes)
            {
                // 更新同步通知标识
                $data['sync_flag'] = 1;
                $this->pay_model->updatePayLog($postData['order_no'], $data); 
                $url = $this->config->item('pages_url') . 'mylottery/rchagscess/' . $postData['order_no'];
                header('Location: ' . $url);
                die();
            }
            else
            {
                // 验签失败
                log_message('LOG', "请求参数验签失败 - 请求参数: " . json_encode($postData), 'sumpayWebSync');
                echo "请求参数错误，验签失败！";
                die;
            }
        }
    }

    /**
     * 中信微信SDK  获取预支付订单信息
     */
    public function zxwxSdk()
    {
        $trade_no = $this->input->post('trade_no', true);
        $money = $this->input->post('money', true);

        require_once APPPATH . '/libraries/weixinzxpay/pay_submit.class.php';
        $this->config->load('pay');
        $zxwxSdk = $this->config->item('zxwxSdk');

        $rparams = array(
            'encoding' => $zxwxSdk['encoding'],             //编码方式
            'signMethod' => $zxwxSdk['signMethod'],         //充值金额 单位:元，精确到分.
            'txnType' => '01',                              //交易类型 01：消费；
            'txnSubType' => '010132',                       //交易子类型 010130：二维码支付 010131：公众号支付 010132：APP支付（主扫）
            'channelType' => '6002',                        //接入渠道  6002：商户互联网渠道;
            'payAccessType' => '02',                        //接入支付类型 02：接口支付
            'backEndUrl' => $zxwxSdk['backEndUrl'],         //后台通知地址
            'merId' => $zxwxSdk['merId'],                   //普通商户或一级商户的商户号
            'orderId' => $trade_no,                         //商户订单号
            'orderTime' => date('YmdHis', time()),          //交易起始时间  格式为[yyyyMMddHHmmss] ,如2009年12月25日9点10分10秒 表示为20091225091010
            'orderTimeExpire' => date('YmdHis', strtotime('+7 day')),   //交易结束时间
            'productId' => '1',                             //商品ID trade_type=010130，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
            'orderBody' => '彩咖充值',                      //商品描述
            'txnAmt' => (string)$money,                     //交易金额  订单总金额(交易单位为分，例:1.23元=123)，只能整数
            'currencyType' => $zxwxSdk['currencyType'],     //交易币种 默认是156：人民币
        );
        //建立请求
        $paySubmit = new paySubmit($zxwxSdk);
        $respone = $paySubmit->buildRequestHttp($rparams);

        exit(json_encode($respone));
    }
    
    /**
     * 支付补单查询接口
     * @param string $trade_no	交易流水号
     */
    public function orderSelect($trade_no)
    {
    	if(!in_array($this->get_client_ip(), $this->config->item('own_ip')))
    	{
    		die('{"code":1,"msg":"操作失败"}');
    	}
    	$walletlog = $this->Wallet->getWalletLog($trade_no);
    	if(!$walletlog || $walletlog['ctype'] != 0)
    	{
    		die('{"code":1,"msg":"操作失败"}');
    	}
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

    /**
     * 充值流程优化 易宝支付Wap
     */
    public function yeepayWap()
    {
        $params = $this->input->post();

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
        $datas['identity_type'] = 0;    //支付身份标识类型码
        $datas['user_ip'] = $params['ip'];
        $datas['user_ua'] = '';
        $datas['callbackurl'] = $yeepay['callbackurl']; //后台回调地址
        $datas['fcallbackurl'] = $yeepay['fcallbackurl'];   //前台回调地址
        $datas['product_name'] = $params['product_name'];
        $datas['product_desc'] = $params['product_desc'];
        $datas['terminaltype'] = 3;
        $datas['terminalid'] = '05-16-DC-59-C2-34';//其他支付身份信息
        $datas['amount'] = (int)$params['money'];
        $datas['cardno'] = '';
        $datas['idcardtype'] = '01';    // 证件类型 - 身份证
        $datas['idcard'] = $params['id_card'];
        $datas['owner'] = $params['real_name'];
        $datas['currency'] = 156;   // 币种 - 人民币
        $url = $this->yeepaympay->webPay($datas);
        echo $url;
    }

    /**
     * 中信微信SDK马甲版预订单生成
     */
    public function zxwxSdkByChannel()
    {
        $trade_no = $this->input->post('trade_no', true);
        $money = $this->input->post('money', true);
        $channel = $this->input->post('channel', true);

        require_once APPPATH . '/libraries/weixinzxpay/pay_submit.class.php';
        $this->config->load('appChannel');
        $config = $this->config->item('zxwxSdk');

        $respone = array();
        if(!empty($config[$channel]))
        {
            $zxwxSdk = $config[$channel];
            $rparams = array(
                'encoding' => $zxwxSdk['encoding'],             //编码方式
                'signMethod' => $zxwxSdk['signMethod'],         //充值金额 单位:元，精确到分.
                'txnType' => '01',                              //交易类型 01：消费；
                'txnSubType' => '010132',                       //交易子类型 010130：二维码支付 010131：公众号支付 010132：APP支付（主扫）
                'channelType' => '6002',                        //接入渠道  6002：商户互联网渠道;
                'payAccessType' => '02',                        //接入支付类型 02：接口支付
                'backEndUrl' => $zxwxSdk['backEndUrl'] . $channel,         //后台通知地址
                'merId' => $zxwxSdk['merId'],                   //普通商户或一级商户的商户号
                'orderId' => $trade_no,                         //商户订单号
                'orderTime' => date('YmdHis', time()),          //交易起始时间  格式为[yyyyMMddHHmmss] ,如2009年12月25日9点10分10秒 表示为20091225091010
                'orderTimeExpire' => date('YmdHis', strtotime('+7 day')),   //交易结束时间
                'productId' => '1',                             //商品ID trade_type=010130，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
                'orderBody' => '彩咖充值',                      //商品描述
                'txnAmt' => (string)$money,                     //交易金额  订单总金额(交易单位为分，例:1.23元=123)，只能整数
                'currencyType' => $zxwxSdk['currencyType'],     //交易币种 默认是156：人民币
            );
            //建立请求
            $paySubmit = new paySubmit($zxwxSdk);
            $respone = $paySubmit->buildRequestHttp($rparams);
        }
        exit(json_encode($respone));
    }

    public function zxpaySdkAsyncByChannel($channel = '')
    {
        require_once APPPATH . '/libraries/weixinzxpay/pay_notify.class.php';
        $this->config->load('appChannel');
        $config = $this->config->item('zxwxSdk');

        if(!empty($config[$channel]))
        {
            $zxwxSdk = $config[$channel];
            $payNotify = new payNotify($zxwxSdk);
            $result = file_get_contents("php://input");
            $result = str_replace('#', '+', $result);
            if(strpos($result, 'sendData=') === false)
            {
                $response = '{"respCode":"9999","respMsg":"交易失败"}';
                $response = 'sendData=' . base64_encode($response);
                die($response);
            }
            $str = substr($result, 9);
            $verify_result = $payNotify->verifyNotify($str);
            $params = array();
            if ($verify_result)
            { 
                //验证成功
                $val = base64_decode($str);
                $val = json_decode($val, true);
                $params['trade_no'] = $val['orderId'];
                $params['respCode'] = $val['respCode'];
                $params['pay_trade_no'] = $val['txnSeqId'];
                $params['respMsg'] = $val['respMsg'];
                $res = $this->pay_model->zxpayWebCallback($params);
                if($res)
                {
                    $response = '{"respCode":"0000","respMsg":"OK"}';
                    $response = 'sendData=' . base64_encode($response);
                    die($response); //请不要修改或删除
                }
            }
        }
        
        $response = '{"respCode":"9999","respMsg":"交易失败"}';
        $response = 'sendData=' . base64_encode($response);
        die($response);
    }
    
    /**
     * 威富通支付宝支付  网页端异步通知
     */
    public function wftZfbCallback()
    {
    	require_once APPPATH . '/libraries/wftZfb/request.php';
    	$this->config->load('pay');
    	$wftZfb = $this->config->item('wftZfb');
    	//建立请求
    	$paySubmit = new Request($wftZfb);
    	$respone = $paySubmit->callback();
    	if($respone['code'] == true)
    	{
    		$params['trade_no'] = $respone['data']['out_trade_no'];
    		$params['pay_trade_no'] = $respone['data']['transaction_id'];
    		$params['respCode'] = $respone['data']['pay_result'];
    		$res = $this->pay_model->wftZfbCallback($params);
    		if($res)
    		{
    			die('success');
    		}
    	}
    	
    	die('failure');
    }
    
    /**
     * 威富通微信SDK
     */
    public function wftWxSdk()
    {
    	$trade_no = $this->input->post('trade_no', true);
    	$money = $this->input->post('money', true);
    	require_once APPPATH . '/libraries/wftZfb/request.php';
    	$this->config->load('pay');
        $payConfig = $this->config->item('wftWxSdk');
        $rparams = array(
        	'service' => $payConfig['service'],
        	'mch_id' => $payConfig['mch_id'],
        	'appid' => $payConfig['appid'],
        	'version' => $payConfig['version'],
        	'charset' => $payConfig['charset'],
        	'out_trade_no' => $trade_no,
        	'body' => '彩咖充值',
        	'total_fee' => (string)$money,
        	'mch_create_ip' => UCIP,
        	'notify_url' => $payConfig['notify_url'],
        );
        
        //建立请求
        $paySubmit = new Request($payConfig);
        $respone = $paySubmit->sdkOrderInfo($rparams);
        exit(json_encode($respone));
    }
    
    /**
     * 威富通微信  异步通知
     */
    public function wftWxSdkCallback()
    {
    	require_once APPPATH . '/libraries/wftZfb/request.php';
    	$this->config->load('pay');
    	$payConfig = $this->config->item('wftWxSdk');
    	//建立请求
    	$paySubmit = new Request($payConfig);
    	$respone = $paySubmit->callback();
    	if($respone['code'] == true)
    	{
    		$params['trade_no'] = $respone['data']['out_trade_no'];
    		$params['pay_trade_no'] = $respone['data']['transaction_id'];
    		$params['respCode'] = $respone['data']['pay_result'];
    		$res = $this->pay_model->wftZfbCallback($params);
    		if($res)
    		{
    			die('success');
    		}
    	}
    	 
    	die('failure');
    }
    
    /**
     * 威富通微信PC  异步通知
     */
    public function wftWxCallback()
    {
    	require_once APPPATH . '/libraries/wftZfb/request.php';
    	$this->config->load('pay');
    	$payConfig = $this->config->item('wftWx');
    	//建立请求
    	$paySubmit = new Request($payConfig);
    	$respone = $paySubmit->callback();
    	if($respone['code'] == true)
    	{
    		$params['trade_no'] = $respone['data']['out_trade_no'];
    		$params['pay_trade_no'] = $respone['data']['transaction_id'];
    		$params['respCode'] = $respone['data']['pay_result'];
    		$res = $this->pay_model->wftZfbCallback($params);
    		if($res)
    		{
    			die('success');
    		}
    	}
    
    	die('failure');
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
    	$walletlog = $this->Wallet->getWalletLog($trade_no);
    	if(!$walletlog || $walletlog['ctype'] != 0 || $walletlog['mark'] != 1)
    	{
    		die('{"code":1,"msg":"操作失败"}');
    	}
    	$money = ParseUnit($money);
    	if($money > $walletlog['money'])
    	{
    		die('{"code":1,"msg":"退款金额不能大于充值金额"}');
    	}
    	$this->config->load('pay');
    	$this->load->library('tools');
    	switch ($walletlog['additions'])
    	{
    		case 'zxwxSdk':
    		case 'payWeix':
    			require_once APPPATH . '/libraries/weixinzxpay/pay_submit.class.php';
    			$config = $walletlog['additions'] == 'zxwxSdk' ? $this->config->item('zxwxSdk') : $this->config->item('zxpayWeb');
    			$refundId = $this->tools->getIncNum('UNIQUE_KEY');
    			$params = array(
    				'encoding' => $config['encoding'],			//编码方式
    				'signMethod' => $config['signMethod'], 		//充值金额 单位:元，精确到分.
    				'txnType' => '04',
    				'txnSubType' => '040441',
    				'channelType' => '6002', 						//接入渠道  6002：商户互联网渠道;
    				'payAccessType' => '02', 						//接入支付类型 02：接口支付
    				'merId' => $config['merId'], 					//普通商户或一级商户的商户号
    				'origTxnSeqId' => $walletlog['trade_no'],		//商户充值订单号
    				'origSettleDate' => date('Ymd', strtotime($walletlog['created'])),
    				'orderId' => $refundId,
    				'orderTime' => date('YmdHis', time()),
    				'txnAmt' => (string)$money,
    				'currencyType' => $config['currencyType']
    			);
    			//建立请求
    			$paySubmit = new paySubmit($config);
    			$respone = $paySubmit->buildOrderSelectHttp($params);
    			if(!is_array($respone))
    			{
    				die('{"code":1,"msg":"' . $respone . '"}'); //异常返回处理
    			}
    			if($respone['respCode'] == '0000')
    			{
    				$payType = array('payWeix' => 5, 'zxwxSdk' => 8);
    				$refundData = array(
    					'trade_no' => $refundId,
    					'recharge_trade_no' => $walletlog['trade_no'],
    					'partner_trade_no' => $respone['txnSeqId'],
    					'money' => $respone['txnAmt'],
    					'uid' => $walletlog['uid'],
    					'status' => '0',
    					'pay_type' => $payType[$walletlog['additions']],
    					'created' => date('Y-m-d H:i:s', strtotime($respone['txnTime'])),
    				);
    				$res = $this->pay_model->recordRefundLog($refundData);
    				die('{"code":0,"msg":"操作成功"}');
    			}
    			else
    			{
    				die('{"code":1,"msg":"' . $respone['respCode'] . $respone['respMsg'] . '"}');
    			}
    			break;
    		default:
    			die('{"code":1,"msg":"操作失败"}');
    	}
    }

    // 威富通微信APP扫码
    public function wftWx()
    {
        $params = $this->input->post(null, true);
        require_once APPPATH . '/libraries/wftZfb/request.php';
        $this->config->load('pay');
        $wftWx = $this->config->item('wftWx');
        $rparams = array(
            'service' => $wftWx['service'],
            'mch_id' => $wftWx['mch_id'],
            'version' => $wftWx['version'],
            'charset' => $wftWx['charset'],
            'out_trade_no' => $params['trade_no'],
            'body' => '彩咖充值',
            'total_fee' => (string)ParseUnit($params['money']),
            'mch_create_ip' => UCIP,
            'notify_url' => $wftWx['notify_url'],
            'time_start' => date('YmdHis', time()),
            'time_expire' => date('YmdHis', strtotime('+7 day')),
        );

        //建立请求
        $paySubmit = new Request($wftWx);
        $respone = $paySubmit->submitOrderInfo($rparams);
        $respone['orderId'] = $params['trade_no'];
        $respone['orderTime'] = $rparams['time_start'];
        $respone['txnAmt'] = $rparams['total_fee'];
        exit(json_encode($respone));
    }

    // 现在支付 支付宝h5
    public function xzZfbWap()
    {
        $params = $this->input->post(null, true);
        $this->config->load('pay');
        $payConfig = $this->config->item('xzZfbWap');
        $this->load->library('xianzaipay/XianZaiPay');
        $rparams = array(
            'mhtOrderAmt'       =>  $params['mhtOrderAmt'],   // 交易金额 分
            'mhtOrderNo'        =>  $params['mhtOrderNo'],    // 商户订单编号
            'notifyUrl'         =>  $payConfig['back_notify_url'],  // 后台回调
            'frontNotifyUrl'    =>  $payConfig['front_notify_url'], // 前台回调
            'mhtOrderName'      =>  '彩咖充值',
            'mhtOrderDetail'    =>  '彩咖充值',
            'appId'             =>  $payConfig['appId'],
            'mhtOrderStartTime' =>  date("YmdHis"),
            'mhtReserved'       =>  '166cai',    
        );
        $respone = $this->xianzaipay->pay($rparams, $payConfig['secure_key']);
        exit(json_encode($respone));
    }

    // 现在支付 支付宝h5 同步回调
    public function xzZfbWapFront()
    {
        $request = $this->input->get();
        $this->config->load('pay');
        $payConfig = $this->config->item('xzZfbWap');
        $this->load->library('xianzaipay/XianZaiPay');
        if($this->xianzaipay->verifySignature($request, $payConfig['secure_key']))
        {
            // 验签成功
            $this->pay_model->appSyncCallBack($request['mhtOrderNo']);
        }
        else
        {
            // 验签失败
            log_message('LOG', "请求参数 -- 同步回调验签失败: ", 'xzZfbWapFront');
            echo "请求参数错误，验签失败！";
            die;
        }
    }

    // 现在支付 支付宝h5 异步回调
    public function xzZfbWapBack()
    {
        $request = file_get_contents('php://input');
        $this->config->load('pay');
        $payConfig = $this->config->item('xzZfbWap');
        $this->load->library('xianzaipay/XianZaiPay');
        $result = $this->xianzaipay->notify($request, $payConfig['secure_key']);
        if($result['status'])
        {
            // 验证成功
            $requestData = $result['data'];
            $this->pay_model->xzZfbWapCallback($requestData);
            echo "success=Y";
        }
        else
        {
            log_message('LOG', "请求参数 -- 异步回调验签失败: ", 'xzZfbWapBack');
        }
    }

}
