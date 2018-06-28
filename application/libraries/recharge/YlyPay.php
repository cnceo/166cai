<?php
/**
 * 银联云支付充值服务类
 * @author yindefu
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class YlyPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;
	
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
		$pparams = array(
                    'version' => '1.0', //版本号
                    'signMethod' => '01', //签名方法
                    'txnType' => '02', //接口类型
                    'txnSubType' => '05', //交易类型
                    "productCode"=>'0205', //产品编码
                    'merId' => $this->merId, //开通商户后 分配的商户号
                    'merchantNum' => $this->config['mchNo'], //开开通商户后 分配的商户编码
                    'backUrl' => $this->config['notify_url'],//交易成功后的通知地址
                    'orderId' => $params['trade_no'], //订单编号
                    'txnAmt' => $params['money'], //交易金额 单位分
                    'txnTime' => date('YmdHis'), //订单发送时间
                    'reqReserved' => "166cai",   //商户自保留字段
		);
                $sign= $this->createSign($pparams);
                $pparams['signature'] = $sign['signature'];
                $pparams['certId'] = $sign['certId'];      
		$postStr = $this->createString($pparams, false, true);
                $respData = $this->http_post_data($this->payGateway, $postStr);
                if(empty($respData))
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求银联云商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode($respData);
                    $this->alertEmail($message);
                    return $returnData;
		}
                if($respData['respCode'] == '66'){
                    $resParams['orderId'] = $params['trade_no'];
                    $resParams['orderTime'] = date("Y-m-d H:i:s");
                    $resParams['txnAmt'] = $params['money'];
                    $resParams['code_url'] = $respData['payInfo'];
                    $returnData = array(
                            'code' => true,
                            'msg' => '请求成功',
                            'data' => $resParams,
                    );
                } else {
                    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求银联云商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode($respData);
                    $this->alertEmail($message);
                }
                return $returnData;
	}
	
        private function createString($para, $sort, $encode)
        {
            if ($para == null || !is_array($para)) {
                return "";
            }

            $linkString = "";
            if ($sort) {
                ksort($para);
                reset($para);
            }
            while (list($key, $value) = each($para)) {
                if ($encode) {
                    $value = urlencode($value);
                }
                $linkString .= $key . "=" . $value . "&";
            }
            // 去掉最后一个&字符
            $linkString = substr($linkString, 0, count($linkString) - 2);

            return $linkString;
        }

        
        private function createSign($params)
        {
            $data = array();
            $cert_path = dirname(__FILE__) . '/ylypaykey/' . $this->merId . '/' . $this->merId . '_cert.pfx';
            $pkcs12certdata = file_get_contents($cert_path);
            openssl_pkcs12_read($pkcs12certdata, $certs, $this->key);
            $x509data = $certs['cert'];
            openssl_x509_read($x509data);
            $certdata = openssl_x509_parse($x509data);
            $data['certId'] = $certdata['serialNumber'];
            $params['certId'] = $certdata['serialNumber'];
            $params_str = $this->createString($params, true, false);
            $params_sha1x16 = sha1($params_str, false);
            $private_key = $certs['pkey'];
            $sign_falg = openssl_sign($params_sha1x16, $signature, $private_key, OPENSSL_ALGO_SHA1);
            if ($sign_falg) {
                $signature_base64 = base64_encode($signature);
                $data['signature'] = $signature_base64;
            }
            return $data; 
    }

        /**
	 * 充值异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => 'error',
			'succMsg' => 'success',
			'data' => array(),
		);
		$params = array();
                foreach ($_POST as $key => $val) {
                    if (!isset($params[$key])) {
                        $params[$key] = $val;
                    }
                }
                $falg = $this->veryfy($params);
		if($falg)
		{
			if($params['respCode'] == '00')
			{
				$payData = array(
					'trade_no' => $params['orderId'],
					'pay_trade_no' => $params['queryId'],
					'status' => '1',	// 成功
				);
				
				$returnData['code'] = true;
				$returnData['data'] = $payData;
				
				return $returnData;
			}
			else
			{
                            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>银联云商户号：'. $this->merId . '异步通知接口支付失败，请及时留意。<br/>请求返回数据：' . json_encode($params);
                            $this->alertEmail($message);
		            $this->log($params, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
		else
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>银联云商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . json_encode($params);
                    $this->alertEmail($message);
                    $this->log($params, __CLASS__, $this->merId, __FUNCTION__);
		}
		
		return $returnData;
	}
        
        public function veryfy($params)
        {
            $public_key = $this->getPulbicKeyByCertId($params['certId']);
            $signature_str = $params['signature'];
            unset($params['signature']);
            $params_str = $this->createString($params, true, false);
            $signature = base64_decode($signature_str);
            $params_sha1x16 = sha1($params_str, false);
            try {
                $isSuccess = openssl_verify($params_sha1x16, $signature, $public_key, OPENSSL_ALGO_SHA1);
            } catch (Exception $exc) {
                
            }
            return $isSuccess;
        }

        public function getPulbicKeyByCertId($certId)
        {
            $cert_dir = dirname(__FILE__) . '/ylypaykey/' . $this->merId . '/';
            $handle = opendir($cert_dir);
            if ($handle) {
                while ($file = readdir($handle)) {
                    clearstatcache();
                    $filePath = $cert_dir . '/' . $file;
                    if (is_file($filePath)) {
                        if (pathinfo($file, PATHINFO_EXTENSION) == 'cer') {
                            $x509data = file_get_contents($filePath);
                            openssl_x509_read($x509data);
                            $certdata = openssl_x509_parse($x509data);
                            $cert_id = $certdata['serialNumber'];
                            if ($cert_id == $certId) {
                                closedir($handle);
                                return file_get_contents($filePath);
                            }
                        }
                    }
                }
            }
            closedir($handle);
            return null;
    }

        /**
	 * 充值同步通知
	 * @see RechargeAbstract::syncCallback()
	 */
	public function syncCallback()
	{
              
	}
	
	public function formSubmit($params)
	{
		//$params['money'] = 1 ;//* 100;
		$res = $this->requestHttp($params);
		if($res['code'] != true) return array();
		$defaultParams = $res['data'];
		$defaultParams['submit_url'] = $params['submit_url'];
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('charset' => 'utf-8','html' => $this->createForm($defaultParams),'params'=>$defaultParams),
		);
		return $returnData;
	}
	
	public function queryOrder($params)
	{
            $returnData = array(
                'code' => false,
                'msg' => '操作失败',
                'data' => array('code' => 1, 'msg' => '操作失败'),
            );
            $pparams = array(
                'version' => '1.0', //版本号
                'signMethod' => '01', //签名方法
                'txnType' => '01', //接口类型
                'txnSubType' => '00', //交易类型
                'merId' => $this->merId, //开通商户后 分配的商户号
                'orderId' => $params['trade_no'], //订单编号
                'txnTime' => date('YmdHis'), //订单发送时间
                'reqReserved' => "166cai",   //商户自保留字段
            );
            $sign= $this->createSign($pparams);
            $pparams['signature'] = $sign['signature'];
            $pparams['certId'] = $sign['certId'];      
            $postStr = $this->createString($pparams, false, true);
            $respData = $this->http_post_data($this->payGateway, $postStr);
            if($respData['origRespCode'] == '00')
            {
                    $payData = array(
                            'code' => '0',
                            'ptype' => '银联云支付',
                            'pstatus' => '成功',
                            'ptime' => date('Y-m-d H:i:s', strtotime($respData['txnTime'])),
                            'pmoney' => '',
                            'pbank' => '银联云支付',
                            'ispay' =>  true,
                            'pay_trade_no' => $params['trade_no'],
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
                    $returnData['data']['msg'] = $respData['origRespMsg'];
            }

            return $returnData;
    }
	
	public function refundSubmit($params)
	{
		
	}
	
	public function queryRefund($params)
	{
		
	}
	
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
	
	
	private function signString($data, $unSignKeyList) 
	{
		$linkStr="";
		$isFirst=true;
		ksort($data);
		foreach($data as $key=>$value)
		{
			if($value==null || $value=="")
			{
				continue;
			}
			$bool=false;
			foreach ($unSignKeyList as $str) 
			{
				if($key."" == $str."")
				{
					$bool=true;
					break;
				}
			}
			if($bool)
			{
				continue;
			}
			if(!$isFirst)
			{
				$linkStr.="&";
			}
			$linkStr.=$key."=".$value;
			if($isFirst)
			{
				$isFirst=false;
			}
		}
		
		return $linkStr;
	}
	
	/**
	 * 查询curl post请求
	 * @param unknown_type $url
	 * @param unknown_type $data_string
	 */
	private function http_post_data($url, $data_string ) 
	{
	
		$TIMEOUT = 30;	//超时时间(秒)
		$ch = curl_init ();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt ( $ch, CURLOPT_TIMEOUT, $TIMEOUT);
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT-2);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证HOST
                curl_setopt($ch, CURLOPT_SSLVERSION, 1); // http://php.net/manual/en/function.curl-setopt.php页面搜CURL_SSLVERSION_TLSv1
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-type:application/x-www-form-urlencoded;charset=UTF-8',
                ));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $html = curl_exec($ch);
                $curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		if ($curl_errno || (!empty($curl_error)))
		{
			// 记录错误日志
			log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . $data_string . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
		}
                curl_close($ch);
                $result_arr = $this->parseQString($html);
                return $result_arr;
	}
        
    private function parseQString($str, $needUrlDecode = false)
    {
        $result = array();
        $len = strlen($str);
        $temp = "";
        $curChar = "";
        $key = "";
        $isKey = true;
        $isOpen = false;
        $openName = "\0";

        for ($i = 0; $i < $len; $i++) {
            $curChar = $str[$i];
            if ($isOpen) {
                if ($curChar == $openName) {
                    $isOpen = false;
                }
                $temp = $temp . $curChar;
            } elseif ($curChar == "{") {
                $isOpen = true;
                $openName = "}";
                $temp = $temp . $curChar;
            } elseif ($curChar == "[") {
                $isOpen = true;
                $openName = "]";
                $temp = $temp . $curChar;
            } elseif ($isKey && $curChar == "=") {
                $key = $temp;
                $temp = "";
                $isKey = false;
            } elseif ($curChar == "&" && !$isOpen) {
                $this->putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
                $temp = "";
                $isKey = true;
            } else {
                $temp = $temp . $curChar;
            }
        }
        $this->putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
        return $result;
    }

    private function putKeyValueToDictionary($temp, $isKey, $key, &$result, $needUrlDecode)
    {
        if ($isKey) {
            $key = $temp;
            if (strlen($key) == 0) {
                return false;
            }
            $result[$key] = "";
        } else {
            if (strlen($key) == 0) {
                return false;
            }
            if ($needUrlDecode) {
                $result[$key] = urldecode($temp);
            } else {
                $result[$key] = $temp;
            }
        }
    }

}