<?php
/**
 * 统统付 充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class SumPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;

	private $appId;

	private $terminalType;
	
	/**
	 * 秘钥
	 * @var unknown_type
	 */
	private $key;
	/**
	 * 秘钥类型
	 * @var unknown_type
	 */
	private $sign_type;
	/**
	 * 配置信息
	 * @var unknown_type
	 */
	private $config;
	
	/**
	 * 请求第三方网关地址
	 * @var unknown_type
	 */
	private $payGateway;
	
	private $baseUrl;
	public function __construct($config = array())
	{
		$this->merId = $config['mer_id'];
		$this->appId = $config['app_id'];
		$this->terminalType = $config['terminal_type'];
		$this->payGateway = $config['payUrl'];
		$this->apiUrl = $config['apiUrl'];
		unset($config['payUrl']);
		unset($config['apiUrl']);
		$this->config = $config;
	}
	
	/**
	 * curl请求支付信息
	 * @see RechargeAbstract::requestHttp()
	 */
	public function requestHttp($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '请求错误',
			'data' => $params,
		);

		$resParams = $this->getParams();
		$resParams['order_no'] = $params['trade_no'];
		$resParams['order_amt'] = number_format(ParseUnit($params['money'], 1), 2, '.', '');		// 单位：元保留两位
		$resParams['cstno'] = $params['uid'];
		$resParams['cre_no'] = $params['id_card'];
		$resParams['card_holder_name'] = $params['real_name'];
		// RSA 加密初始化
		$resParams['sign'] = $this->init($resParams);
		$resParams['payUrl'] = $this->payGateway;

		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => $resParams,
		);
			
		return $returnData;
	}
	
	/**
	 * 充值异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		$res = array(
            'resp_code' => '000000',
            'resp_msg'  => '接收成功'
        );

		$returnData = array(
			'code' => false,
			'errMsg' => json_encode($res),
			'succMsg' => json_encode($res),
			'data' => array(),
		);
		
		$postData = $GLOBALS['HTTP_RAW_POST_DATA'];
		if(!empty($postData))
        {
            $postData = json_decode($postData, true);
            $source = $this->sortArrayData($postData);
            $verifyRes = $this->verify($source, $postData['sign'], $this->merId);

            if($verifyRes)
            {
            	if($postData['status'] == '1')
            	{
            		$payData = array(
						'trade_no' => $postData['order_no'],
						'pay_trade_no' => $postData['serial_no'],
						'status' => '1',	// 成功
					);

					$returnData = array(
						'code' => true,
						'errMsg' => json_encode($res),
						'succMsg' => json_encode($res),
						'data' => $payData,
					);
            	}
            	elseif($postData['status'] == '2') 
            	{
            		// 处理中 不作处理
            	}
            	else
            	{
            		// err_code err_msg
					// TODO 交易异常 这里记录错误日志或邮件报警
            		$message = '统统付商户号：'. $this->merId . '异步通知接口交易异常，请及时留意。';
            		// $this->alertEmail($message);
            		$this->log($postData, __CLASS__, $this->merId, __FUNCTION__);
            	}
            }
            else
            {
            	// TODO 验签失败 这里记录错误日志或邮件报警
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>统统付商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($postData);
            	$this->alertEmail($message);
            	$this->log($postData, __CLASS__, $this->merId, __FUNCTION__);
            }
        }
		return $returnData;
	}

	/**
	 * 充值同步通知
	 * @see RechargeAbstract::syncCallback()
	 */
	public function syncCallback()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => 'failure',
			'succMsg' => 'success',
			'data' => array(),
		);

		$postData = $_POST['res'];
		$postData = urldecode($postData);
        $postData = base64_decode($postData);
        $postData = json_decode($postData, true);
        
        if($postData)
        {
            $source = $this->sortArrayData($postData);
            $verifyRes = $this->verify($source, $postData['sign'], $this->merId);
            
            if($verifyRes)
            {
            	$payData = array(
					'trade_no' => $postData['order_no'],
				);

            	$returnData = array(
					'code' => true,
					'errMsg' => 'failure',
					'succMsg' => 'success',
					'data' => $payData,
				);
            }
            else
            {
                // TODO 验签失败 这里记录错误日志或邮件报警
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>统统付商户号：'. $this->merId . '同步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($postData);
            	$this->alertEmail($message);
            	$this->log($postData, __CLASS__, $this->merId, __FUNCTION__);
            }
        }
        else
        {
			//对返回数据处理
			$input = file_get_contents('php://input');
			$gPost = $GLOBALS['HTTP_RAW_POST_DATA'];
			$get = $_GET;
			$msg = '';
			if($input)
			{
				$msg .= $this->printJson($input);
			}
			if($gPost)
			{
				$msg .= $this->printJson($gPost);
			}
			if($get)
			{
				$msg .= $this->printJson($get);
			}
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>统统付商户号：'. $this->merId . '同步通知接口接收数据异常，请及时留意。<br/>请求返回数据：' . $msg;
        	$this->alertEmail($message);
        	$this->log($postData, __CLASS__, $this->merId, __FUNCTION__);
        }
        return $returnData;
	}
	/**
	 * [formSubmit 统统付]
	 * @author LiKangJian 2017-06-19
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function formSubmit($params)
	{
		$res = $this->requestHttp($params);
		if($res['code']!=1) return array();
		$params = $res['data'];
		$html =  '<body onLoad="document.autoForm.submit();">';
		$html .= '<form name="autoForm" action="'.$params['payUrl'].'" method="post">';
		unset($params['payUrl']);
		foreach ($params as $k => $v) 
		{
			$html .= '<input type="hidden" name="'.$k.'" value="'.$v.'"/><br/>';
		}
		$html .= '</form></body>';
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('charset' => 'utf-8','html' => $html,'params'=>$params),
		);
		return $returnData;
	}
	public function queryOrder($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array(),
		);

		$resParams = array(
			'version'   => '1.0',
			'service' => 'sumpay.trade.order.search',
			'format' => 'JSON',
			'app_id' => $this->appId,
			'timestamp' => date('YmdHis', time()),
			'terminal_type' => $this->terminalType,
			'sign_type' => 'RSA',
			'mer_id' => $this->merId,
			'order_no' => $params['trade_no']
		);
		// RSA 加密初始化
		$resParams['sign'] = $this->init($resParams);
		//拼接待签名数据
        $resParams = $this->createLinkstring($resParams);
		$respone = $this->curlPost($this->apiUrl, $resParams);
		$respone = json_decode($respone, true);
		if($respone['resp_code'] == '000000')
    	{
    		$ptype = array('sumpayWeb' => '统统付快捷', 'sumpayWap' => '统统付Wap');
    		$pstatus = array('0' => '失败', '1' => '已付款', '2' => '处理中', '00' => '未支付');
    		$payData = array(
				'code' => '0',
				'ptype' => $ptype[$params['additions']],
				'pstatus' => $pstatus[$respone['status']],
				'ptime' => $respone['succ_time'] > 0 ? date('Y-m-d H:i:s', strtotime($respone['succ_time'])) : '',
				'pmoney' => number_format($respone['succ_amt'], 2, ".", ","),
				'pbank' => '',
				'ispay' => $respone['status'] == '1' ? true : false,
				'pay_trade_no' => $respone['serial_no'],
			);

			$returnData = array(
				'code' => true,
				'msg' => '操作成功',
				'data' => $payData,
			);
    	}
    	return $returnData;
	}
	
	public function queryBill($params)
	{
		$resParams = array(
			'version'   => '1.0',
			'service' => 'sumpay.trade.order.checkbills',
			'format' => 'JSON',
			'app_id' => $this->appId,
			'timestamp' => date('YmdHis', time()),
			'terminal_type' => $this->terminalType,
			'sign_type' => 'RSA',
			'mer_id' => $this->merId,
			'bill_time' => $params['bill_date'],
			'part_num' => '1',
		);
		// RSA 加密初始化
		$resParams['sign'] = $this->init($resParams);
		//拼接待签名数据
		$resParams = $this->createLinkstring($resParams);
		$respone = $this->curlPost($this->apiUrl, $resParams);
		$respone = json_decode($respone, true);
		if($respone['resp_code'] != '000000')
		{
			return array('code' => false, 'msg' => '接口请求失败');
		}
		
		return array('code' => true, 'msg' => '操作成功', 'data' => base64_decode($respone['file_content']));
	}
	
	public function refundSubmit($params)
	{
		
	}
	
	public function queryRefund($params)
	{
		
	}
	
	/**
	 * 返回请求参数数组
	 * @return multitype:string unknown_type
	 */
	private function getParams()
	{
		$params = array();
		foreach ($this->config as $key => $value)
		{
			$params[$key] = $value;
		}
		$params['sign_type'] = 'RSA';
		$params['sign'] = '';
		$params['goods_num'] = '1';		// 商品数量
		$params['goods_type'] = '2';	
		$params['logistics'] = '0';	
		$params['order_time'] = date('YmdHis', time());	
		$params['trade_code'] = 'T0002';	
		$params['version'] = '1.0';
		$params['cur_type'] = 'CNY';
				
		return $params;
	}

	// RSA 加密初始化
	public function init($initDatas)
	{
		return $this->signArrayData($initDatas, $this->merId);
	}

	// 过滤数据
	public function signArrayData($dataArray, $mer_id)
    {
        $source = $this->sortArrayData($dataArray);
        return $this->sign($source, $mer_id);
    }

    // 过滤数据 排序
    public function sortArrayData($dataArray)
    {
        //去除 sign 和  sign_type 其余参数均要参与签名
        $targetArray = array();
        foreach ($dataArray as $key => $val) 
        {
            //跳过 sign sign_type和空值参数
            if ($key == 'sign' || $key == 'sign_type' || strlen($val) == 0) 
            {
                continue;
            }
            $kvItem = $key . '=' . $val;
            $targetArray[] = $kvItem;
        }

        //数据排序
        asort($targetArray);

        //拼接待签名数据
        $source = implode('&', $targetArray);
        return $source;
    }

    // RSA签名
    public function sign($data, $mer_id)
    {
        $priKeyFile = dirname(__FILE__) . '/sumpaykey/' . $mer_id . '/rsa_private_key.pem';

        //读取私钥文件
        $priKey = file_get_contents($priKeyFile);

        //转换为openssl密钥，必须是没有经过pkcs8转换的私钥
        $priKeyId = openssl_get_privatekey($priKey);

        //调用openssl内置签名方法，生成签名$sign
        openssl_sign($data, $sign, $priKeyId);

        //释放资源
        openssl_free_key($priKeyId);

        //base64编码
        $sign = base64_encode($sign);

        return $sign;
    }
	
	// RSA 验签
    public function verify($data, $sign, $mer_id)
    {
        $pubKeyFile = dirname(__FILE__) . '/sumpaykey/' . $mer_id . '/rsa_public_key.pem';

        //读取公钥文件
        $pubKey = file_get_contents($pubKeyFile);

        //转换为openssl格式密钥
        $pubKeyId = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $pubKeyId);

        //释放资源
        openssl_free_key($pubKeyId);

        return $result;
    }
}