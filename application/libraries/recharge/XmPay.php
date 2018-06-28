<?php
/**
 * 厦门银行 支付宝扫码 充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class XmPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;
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
	
	public function __construct($config = array())
	{
		$this->merId = $config['mch_id'];
		$this->key = $config['key'];
		$this->payGateway = $config['url'];
		$this->sign_type = $config['sign_type'];
		unset($config['key']);
		unset($config['url']);
		unset($config['sign_type']);
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
		$defaultParams = $this->getParams($params);
		$reqParams = $this->setReqParams($defaultParams);
		// 创建签名
		$sign = $this->createSign($reqParams);
                $reqParams['sign'] = $sign;
		$postStr = json_encode($reqParams);
		// 远程获取数据
		$respData = $this->curlPostJson($this->payGateway, $postStr);
		if(empty($respData))
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求厦门国际银行商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode($respData);
			$this->alertEmail($message);
			
			return $returnData;
		}
                $resParams = json_decode($respData, true);
                if(isset($resParams['respCode']) && $resParams['respCode'] == '00000')
                {
                    $resParams['orderId'] = $params['trade_no'];
                    $resParams['orderTime'] = date("Y-m-d H:i:s");
                    $resParams['txnAmt'] = $params['money'];
                    $resParams['code_url'] = $resParams['payUrl'];
                    $resParams['token'] = $resParams['token'];
                    $returnData = array(
                            'code' => true,
                            'msg' => '请求成功',
                            'data' => $resParams,
                    );
                }
                else
                {
                    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求厦门国际银行商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . $respData;
                    $this->alertEmail($message);
                    $this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
                }
		return $returnData;
	}
	
	/**
	 * 充值异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => 'failure',
			'succMsg' => 'success',
			'data' => array(),
		);
		
		$res = file_get_contents('php://input');
		$resParams = json_decode($res, true);
                
		$checkData = $this->verifyNotify($resParams);
		if(strtoupper($checkData['sign']) == $resParams['sign'])
		{
			if($resParams['txnStatus'] == '2')
			{
				$payData = array(
					'trade_no' => $resParams['merchOrderNum'],
					'pay_trade_no' => $resParams['txnSeqId'],
					'status' => '1',	// 成功
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
				// err_code err_msg
				// TODO 交易异常 这里记录错误日志或邮件报警
			    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>厦门国际银行商户号：'. $this->merId . '异步通知接口交易异常，请及时留意。';
				// $this->alertEmail($message);
				$this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
		else
		{
			// TODO 验签失败 这里记录错误日志或邮件报警
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>厦门国际银行商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $res;
			$this->alertEmail($message);
			$this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
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
        return $returnData;
	}
	/**
	 * [formSubmit 表单提交]
	 * @author LiKangJian 2017-06-16
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function formSubmit($params)
	{
		//$params['money'] = 1 ;//* 100;
		$res = $this->requestHttp($params);
		if($res['code'] != 1) return array();
		$defaultParams = $res['data'];
		$defaultParams['submit_url'] = $params['submit_url'];
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('charset' => 'utf-8','html' => $this->createForm($defaultParams),'params'=>$defaultParams),
		);
		return $returnData;
	}
	/**
	 * [createForm 构建表单]
	 * @author LiKangJian 2017-06-16
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	private function createForm($params)
	{
		$html =  '<body onLoad="document.autoForm.submit();">';
		$html .= '<form name="autoForm" action="'.$params['submit_url'].'" method="post">';
		$formParams = array();
		$formParams['orderId'] = $params['orderId'];
		$formParams['orderTime'] = $params['orderTime'];
		$formParams['txnAmt'] = $params['txnAmt'];
		$formParams['codeUrl'] = $params['code_url'];
		foreach ($formParams as $key => $value)
		{

			$html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/><br/>';
			
		}
		$html .= '</form></body>';

		return $html;
	}
	public function queryOrder($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '操作失败'),
		);
                $reqParams = array();
                $reqParams['txnType'] = 'OrderCodeQuery';
                $reqParams['merchCd'] = $this->merId;
                $reqParams['msgId'] = $params['trade_no'];
                $reqParams['token'] = $params['token'];
                $sign = $this->createSign($reqParams);
                $reqParams['sign'] = $sign;
                $postStr = json_encode($reqParams);
		// 远程获取数据
		$respData = $this->curlPostJson($this->payGateway, $postStr);
		// 解析参数
		$resParams = json_decode($respData, true);
		if($resParams['respCode'] == '00000')
    		{
    			$pstatus = array('01' => '处理中','02' => '成功','03' => '失败','99' => '状态未知');
    			$payData = array(
    				'code' => '0',
    				'ptype' => '厦门银行支付宝',
    				'pstatus' => $pstatus[$resParams['txnStatus']],
    				'ptime' => $resParams['txnStatus'] == '02' ? date('Y-m-d H:i:s', strtotime($resParams['txnTime'])) : '',
    				'pmoney' => $resParams['txnAmt'],
    				'pbank' => '厦门银行',
    				'ispay' => $resParams['txnStatus'] == '02' ? true : false,
    				'pay_trade_no' => $resParams['txnSeqId'],
    			);
    			
    			$returnData = array(
    				'code' => true,
    				'msg' => '操作成功',
    				'data' => $payData,
    			);
    			
    			return $returnData;
    		}
    		else
    		{
    			$returnData['data']['msg'] = $respData;
    		}
		
		return $returnData;
	}
	
	public function refundSubmit($params)
	{
		
	}
	
	public function queryRefund($params)
	{
		
	}
	
	/**
	 * 对账接口
	 * @param unknown_type $params
	 */
	public function queryBill($params)
	{
            $rparams = array(
                    'chkFileType' => '002',
                    'chkDate' => $params['bill_date'],
                    'merchCd' => $this->merId,
                    'txnType'=> 'ChkFileQuery',
                    'msgId' => time()
            );
            $sign = $this->createSign($rparams);//创建签名
            $rparams['sign'] = $sign;
            $postStr = json_encode($rparams);
            // 远程获取数据
            $respData = $this->curlPostJson($this->payGateway, $postStr);
            $resParams = json_decode($respData, true);
            if($resParams['respCode'] != '00000')
            {
                return array('code' => false, 'msg' => '接口请求失败');
            }
            return array('code' => true, 'msg' => '操作成功', 'data' => $resParams['downloadUrl']);
	}
	
	/**
	 * 返回请求参数数组
	 * @return multitype:string unknown_type
	 */
	private function getParams($data)
	{
		$params = array();
		foreach ($this->config as $key => $value)
		{
			$params[$key] = $value;
		}
                $params['txnType'] = $params['channel'];
                $params['merchCd'] = $params['mch_id'];
                $params['orderInfo'] = $params['subject'];
                $params['orderType'] = '01';
                $params['currencyCode'] = 'CNY';
                $params['notifyUrl'] = $params['notify_url'];
                $params['msgId'] = $data['trade_no'];
                $params['txnAmt'] = number_format($data['money'] / 100, 2);
                unset($params['mch_id']);
                unset($params['channel']);
                unset($params['subject']);
                unset($params['notify_url']);
		return $params;
	}

	// 过滤参数
	public function setReqParams($params, $filterField = null)
	{
		if($filterField !== null)
		{
            forEach($filterField as $k => $v)
            {
                unset($params[$v]);
            }
        }
        
        //判断是否存在空值，空值不提交
        forEach($params as $k => $v)
        {
            if(empty($v))
            {
                unset($params[$k]);
            }
        }
        return $params;
	}

	// 添加参数
	public function setParameter($params = array(), $parameter, $parameterValue) 
	{
		$params[$parameter] = $parameterValue;
		return $params;
	}

	// 检查参数
	public function getParameter($params = array(), $parameter) 
	{
		return isset($params[$parameter]) ? $params[$parameter] : '';
	}

	// 参数加签
	public function createSign($params) 
	{
            $resParams = $this->argSort($params);
            $sign = $this->createLinkstring($resParams);
            $sign.='&key=' . $this->key;
            $sign = md5($sign);
            return $sign;
	}	


        public function curlPostJson($url, $para, $cacert_url = '') 
	{
		$curl = curl_init($url);
		if($cacert_url)
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
			curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
		}
		else
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
                curl_setopt ($curl, CURLOPT_HTTPHEADER, array (
				'Content-Type:application/json;charset=utf-8',
				'Content-Length:' . strlen($para)
		) );
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl,CURLOPT_POST,true); // post传输数据
		curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
		$responseText = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_error = curl_error($curl);
		if ($curl_errno || (!empty($curl_error)))
		{
			// 记录错误日志
			log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($para) . "\tcurlInfo:" . json_encode(curl_getinfo($curl)), 'recharge/curl_error');
		}
		$this->recode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
                
		return $responseText;
	}
        
    public function verifyNotify($resParams)
    {
        unset($resParams['sign']);
        $resParams = $this->argSort($resParams);
        $sign = $this->createLinkstring($resParams);
        $sign.='&key=' . $this->key;
        $sign = md5($sign);
        return array('sign' => $sign);
    }
}