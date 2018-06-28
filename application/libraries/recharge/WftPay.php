<?php
/**
 * 威富通 支付宝扫码 微信扫码 微信SDK 充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class WftPay extends RechargeAbstract
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
		
		$defaultParams = $this->getParams();
                if(isset($defaultParams['callback_url']))
                {
                    $defaultParams['callback_url'] = $defaultParams['callback_url'].'?trade_no='.$params['trade_no'];
                } 
		$defaultParams['out_trade_no'] = $params['trade_no'];
		$defaultParams['total_fee'] = (string)$params['money'];		// 单位：分
		$defaultParams['mch_create_ip'] = $params['ip'];
		$reqParams = $this->setReqParams($defaultParams);
		$reqParams = $this->setParameter($reqParams, 'nonce_str', mt_rand(time(), time() + rand()));
		// 创建签名
		$reqParams = $this->createSign($reqParams);
		// 转xml
		$postStr = $this->toXml($reqParams);
		// 远程获取数据
		$respData = $this->curlPost($this->payGateway, $postStr);
		if(empty($respData))
		{
                    $mark = $this->getMark($this->merId);
                    if($this->merId == '102515568378' && $defaultParams['service'] == 'pay.alipay.native'){
                        $mark['mark'] = '支付宝扫码-长沙中信银行渠道';
                    }
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求'.$mark['mark'].'商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode(simplexml_load_string($respData));
			$this->alertEmail($message);
			
			return $returnData;
		}
		// 解析参数
		$resParams = $this->setContent($respData);
		// 解密
		$checkData = $this->isTenpaySign($resParams);
		if($checkData['sign'] == $checkData['tenpaySign'])
		{
			if($resParams['status'] == 0 && $resParams['result_code'] == 0)
			{
				$resParams['orderId'] = $params['trade_no'];
        		$resParams['orderTime'] = $defaultParams['time_start'];
        		$resParams['txnAmt'] = $defaultParams['total_fee'];
        		//塞入一个回跳地址没有返回空 处理wft支付没办法同步跳会使用
        		if(isset($this->config['back_url']))
        		{
        			$resParams['backUrl'] = $this->config['back_url'];
        		}
				$returnData = array(
					'code' => true,
					'msg' => '请求成功',
					'data' => $resParams,
				);
			}
			else
			{
				// err_code err_msg
				// TODO 交易异常 这里记录错误日志或邮件报警
                            $mark = $this->getMark($this->merId);
                            if($this->merId == '102515568378' && $defaultParams['service'] == 'pay.alipay.native'){
                                $mark['mark'] = '支付宝扫码-长沙中信银行渠道';
                            }
			    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求'.$mark['mark'].'商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . $this->printJson($resParams);
				$this->alertEmail($message);
				$this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
		else
		{
			// TODO 验签失败 这里记录错误日志或邮件报警
                    $mark = $this->getMark($this->merId);
                    if($this->merId == '102515568378' && $defaultParams['service'] == 'pay.alipay.native'){
                        $mark['mark'] = '支付宝扫码-长沙中信银行渠道';
                    }
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求'.$mark['mark'].'商户号：'. $this->merId . '付款信息时验签失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . $this->printJson($resParams);
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
		
		$xml = file_get_contents('php://input');
		$resParams = $this->setContent($xml);
		$checkData = $this->isTenpaySign($resParams);
		if($checkData['sign'] == $checkData['tenpaySign'])
		{
			if($resParams['status'] == 0 && $resParams['result_code'] == 0)
			{
				$payData = array(
					'trade_no' => $resParams['out_trade_no'],
					'pay_trade_no' => $resParams['transaction_id'],
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
                            $mark = $this->getMark($this->merId);
			    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$mark['mark'].'商户号：'. $this->merId . '异步通知接口交易异常，请及时留意。';
				// $this->alertEmail($message);
				$this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
		else
		{
			// TODO 验签失败 这里记录错误日志或邮件报警
                    $mark = $this->getMark($this->merId);
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$mark['mark'].'商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($resParams);
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
		$params['money'] = $params['money'] ;//* 100;
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
		
		$rparams = array(
			'service' => 'unified.trade.query',
			'version' => $this->config['version'],
			'charset' => $this->config['charset'],
			'mch_id' => $this->merId,
			'out_trade_no' => $params['trade_no'],
		);
		$reqParams = $this->setReqParams($rparams);
		$reqParams = $this->setParameter($reqParams, 'nonce_str', mt_rand(time(), time() + rand()));
		$reqParams = $this->createSign($reqParams);//创建签名
		$postStr = $this->toXml($reqParams);
		// 远程获取数据
		$respData = $this->curlPost($this->payGateway, $postStr);
		// 解析参数
		$resParams = $this->setContent($respData);
		// 解密
		$checkData = $this->isTenpaySign($resParams);
		if($checkData['sign'] == $checkData['tenpaySign'])
		{
			if($resParams['result_code'] == 0 && $resParams['status'] == 0)
    		{
    			$ptype = array('payZfb' => '全付通支付宝', 'wftZfbWap'=>'兴业支付宝H5','wftWxSdk' => '全付通微信SDK', 'wftWx' => '全付通微信PC','wftpay'=>'兴业支付宝H5','wftWxWap'=>'鸿粤兴业银行微信H5','wftwxzx'=>'微信扫码-长沙中信银行渠道','wftzfbzx'=>'支付宝扫码-长沙中信银行渠道');
    			$pstatus = array('SUCCESS' => '已付款', 'REFUND' => '转入退款', 'NOTPAY' => '未支付', 'CLOSED' => '已关闭', 'REVERSE' => '已冲正', 'REVOK' => '已撤销', 'REVOKED' => '已冲正', 'USERPAYING' => '用户支付中', 'PAYERROR' => '支付失败');
    			$payData = array(
    				'code' => '0',
    				'ptype' => $ptype[$params['additions']],
    				'pstatus' => $pstatus[$resParams['trade_state']],
    				'ptime' => $resParams['trade_state'] == 'SUCCESS' ? date('Y-m-d H:i:s', strtotime($resParams['time_end'])) : '',
    				'pmoney' => number_format($resParams['total_fee'] / 100, 2, ".", ","),
    				'pbank' => '',
    				'ispay' => $resParams['trade_state'] == 'SUCCESS' ? true : false,
    				'pay_trade_no' => $resParams['transaction_id'],
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
    			$returnData['data']['msg'] = $resParams['err_msg'];
    		}
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
			'service' => 'pay.bill.merchant',
			'bill_date' => $params['bill_date'],
			'bill_type' => $params['bill_type'],
			'mch_id' => $this->merId,
		);
		$reqParams = $this->setReqParams($rparams);
		$reqParams = $this->setParameter($reqParams, 'nonce_str', mt_rand(time(), time() + rand()));
		$reqParams = $this->createSign($reqParams);//创建签名
		$postStr = $this->toXml($reqParams);
		// 远程获取数据
		$respData = $this->curlPost('https://download.swiftpass.cn/gateway', $postStr);
		return $respData;
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
		$params['time_start'] = date('YmdHis', time());
		$params['time_expire'] = date('YmdHis', strtotime('+7 day'));
		
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
            if($this->sign_type == 'RSA_1_256'){
                $signPars = "";
                $params['sign_type'] = $this->sign_type;
		ksort($params);
		foreach($params as $k => $v) {
			if("" != $v && "sign" != $k) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars = substr($signPars, 0, strlen($signPars) - 1);
                $keyFile = dirname(__FILE__).'/wftwxzxkey/'.$params['mch_id'].'/app_private_key.pem';
		$res = openssl_get_privatekey(file_get_contents($keyFile));
		openssl_sign($signPars, $sign, $res, SHA256);
		openssl_free_key($res);
		$sign = base64_encode($sign);
		$params = $this->setParameter($params, "sign", $sign);
		return $params;	
            }else{
		$signPars = "";
		ksort($params);
		foreach($params as $k => $v) 
		{
			if("" != $v && "sign" != $k) 
			{
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->key;
		$sign = strtoupper(md5($signPars));
		$params = $this->setParameter($params, "sign", $sign);
		return $params;	
            }
	}	

	// 数组转xml
	public function toXml($params)
	{
        $xml = '<xml>';
        forEach($params as $k => $v)
        {
            $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
        }
        $xml .= '</xml>';
        return $xml;
    }

    // 解析xml
    public function setContent($respData)
    {
    	$params = array();

    	$xml = simplexml_load_string($respData);
		$encode = $this->getXmlEncode($respData);
		
		if($xml && $xml->children()) 
		{
			foreach ($xml->children() as $node)
			{
				//有子节点
				if($node->children()) 
				{
					$k = $node->getName();
					$nodeXml = $node->asXML();
					$v = substr($nodeXml, strlen($k)+2, strlen($nodeXml)-2*strlen($k)-5);
					
				} 
				else 
				{
					$k = $node->getName();
					$v = (string)$node;
				}
				
				if($encode!="" && $encode != "UTF-8") 
				{
					$k = iconv("UTF-8", $encode, $k);
					$v = iconv("UTF-8", $encode, $v);
				}
				
				$params = $this->setParameter($params, $k, $v);			
			}
		}
		return $params;
    }

    // 获取xml编码
	public function getXmlEncode($xml) 
	{
		$ret = preg_match ("/<?xml[^>]* encoding=\"(.*)\"[^>]* ?>/i", $xml, $arr);
		if($ret) 
		{
			return strtoupper ( $arr[1] );
		} 
		else 
		{
			return "";
		}
	}

	// 解密
	public function isTenpaySign($params) 
	{
            if($params['sign_type'] == 'RSA_1_256'){
                $signPars = "";
		ksort($params);
		foreach($params as $k => $v) {
			if("sign" != $k && "" != $v) {
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars = substr($signPars, 0, strlen($signPars) - 1);
                $keyFile = dirname(__FILE__).'/wftwxzxkey/'.$params['mch_id'].'/app_public_key.pem';
		$res = openssl_get_publickey(file_get_contents($keyFile));
		$result = (bool)openssl_verify($signPars, base64_decode($this->getParameter($params, "sign")), $res, SHA256);
                openssl_free_key($res);
                if($result){
                    return array('sign' => $this->getParameter($params, "sign"), 'tenpaySign' => $this->getParameter($params, "sign"));
                }else{
                    return array('sign' => $result, 'tenpaySign' => $this->getParameter($params, "sign"));
                }
            }else{
		$signPars = "";
		ksort($params);
		foreach($params as $k => $v) 
		{
			if("sign" != $k && "" != $v) 
			{
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->key;
		
		$sign = strtolower(md5($signPars));
		
		$tenpaySign = strtolower($this->getParameter($params, "sign"));
				
		return array('sign' => $sign, 'tenpaySign' => $tenpaySign);
            }
	}

    
}