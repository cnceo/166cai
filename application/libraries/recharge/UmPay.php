<?php
/**
 * 联动支付充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class UmPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $mer_id;
	
	/**
	 * 配置信息
	 * @var unknown_type
	 */
	private $config;

	private $plat_pay_product_name;

	/**
	 * 默认配置
	 * @var unknown_type
	 */
    private static $default = array(
    	//订单查询数据字段
        'fields_querytrans' => 'merId,goodsId,orderId,merDate,payDate,amount,amtType,bankType,mobileId,gateId,transType,transState,settleDate,bankCheck,merPriv,retCode,version,sign',
        'fields_cancel' => 'merId,amount,retCode,retMsg,version,sign',
        'fields_refund'	=> 'merId,refundNo,amount,retCode,retMsg,version,sign',
        'method_get'	=> 'get',
        'method_post'   => 'post',
        'umpay_url'     => '/pay/payservice.do',
    );
	private $baseUrl;
	public function __construct($config = array())
	{
		$this->mer_id = $config['mer_id'];
		$this->plat_url = $config['plat_url'];
		$this->plat_pay_product_name = $config['plat_pay_product_name'];
		unset($config['plat_url']);
		unset($config['plat_pay_product_name']);
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

		$defaultParams = array();
		foreach ($this->config as $key => $value)
		{
			$defaultParams[$key] = $value;
		}
		$defaultParams['order_id'] = $params['trade_no'];
		$defaultParams['mer_date'] = date('Ymd', time());
		$defaultParams['amount'] = $params['money'];			// 单位：分
		$defaultParams['mer_cust_id'] = $params['uid'];
		if(isset($params['pay_agreement_id']) && !empty($params['pay_agreement_id']))
		{
			$defaultParams['usr_pay_agreement_id'] = $params['pay_agreement_id'];
			unset($defaultParams['identity_type']);
		}
		else
		{
			$defaultParams['card_id'] = $params['bank_id'];			// 银行卡号
			$defaultParams['identity_code'] = $params['id_card'];	// 证件号
			$defaultParams['card_holder'] = $params['real_name'];	// 实名
		}
		$defaultParams['user_ip'] = $params['ip'];

		$recParams = array();
		foreach ($defaultParams as $key => $value) 
		{
			$recParams = $this->put($key, $value, $recParams);
		}

		$reqDataGet = $this->makeRequestDataByGet($recParams);

		if(!empty($reqDataGet))
		{
			$returnData = array(
				'code' => true,
				'msg'  => '请求成功',
				'data' => $reqDataGet,
			);
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
			'errMsg' => '',
			'succMsg' => '',
			'data' => array(),
		);

		$request = $_GET;

		if($request)
		{
			$reqData = $this->getNotifyRequestData($request);
			if($reqData['status'])
			{
				if($request['error_code'] == '0000' && $request['trade_state'] == 'TRADE_SUCCESS')
				{
					$payData = array(
						'trade_no' => $request['order_id'],
						'pay_trade_no' => $request['trade_no'],
						'status' => '1',	// 成功
						'pay_agreement_id' => $request['usr_pay_agreement_id'],
						'bank_type' => $request['gate_id'],		// 银行信息
						'last_four_cardid' => $request['last_four_cardid'],
					);

					$returnData = array(
						'code' => true,
						'errMsg' => '',
						'succMsg' => '',
						'data' => $payData,
					);
				}
				else
				{
					$this->log($request, __CLASS__, $this->mer_id, __FUNCTION__);
				}
			}
			else
			{
			    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>联动支付商户号：'. $this->mer_id . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($request);
				$this->alertEmail($message);
				$this->log($request, __CLASS__, $this->mer_id, __FUNCTION__);
			}

			// 返回通知
			$returnMsg = $this->getRequestMsg($request);
			$returnData['errMsg'] = $returnMsg;
			$returnData['succMsg'] = $returnMsg;
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
			'errMsg' => '',
			'succMsg' => '',
			'data' => array(),
		);

		$request = $_POST;
		if($request)
		{
			$reqData = $this->getNotifyRequestData($request);
			// 验签成功
			if($reqData['status'])
			{
				$payData = array(
					'trade_no' => $request['order_id'],
				);

				$returnData = array(
					'code' => true,
					'errMsg' => '',
					'succMsg' => '',
					'data' => $payData,
				);
			}
			else
			{
			    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>联动支付商户号：'. $this->mer_id . '同步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($request);
				$this->alertEmail($message);
				$this->log($request, __CLASS__, $this->mer_id, __FUNCTION__);
			}
		}
		return $returnData;
	}
	
	public function formSubmit($params)
	{
		
	}
	
	public function queryOrder($params)
	{
		$returnData = array(
			'code'	=>	false,
			'msg' 	=>	'操作失败',
			'data' 	=> 	$params,
		);

		$defaultParams = array(
			'service'		=>	'mer_order_info_query',
			'sign_type'		=>	'RSA',
			'charset'		=>	'UTF-8',
			'res_format'	=>	'HTML',
			'mer_id'		=>	$this->mer_id,
			'version'		=>	'4.0',
			'order_id'		=>	$params['trade_no'],
			'mer_date'		=>	date("Ymd", strtotime($params['created'])),
		);

		$recParams = array();
		foreach ($defaultParams as $key => $value) 
		{
			$recParams = $this->put($key, $value, $recParams);
		}

		$reqDataGet = $this->makeRequestDataByGet($recParams);

		$resParams = array();
		if(!empty($reqDataGet))
		{
			$resdata = get_meta_tags($reqDataGet['payUrl']);
			parse_str($resdata['mobilepayplatform'], $resParams);
		}

		if($resParams['ret_code'] == '0000' || $resParams['ret_msg'])
		{
			$pstatus = array(
				'WAIT_BUYER_PAY'	=>	'交易创建，等待买家付款',
				'TRADE_SUCCESS'		=>	'交易成功',
				'TRADE_CLOSED'		=>	'指定时间段内未支付，交易关闭',
				'TRADE_CANCEL'		=>	'交易撤销',
				'TRADE_FAIL'		=>	'交易失败',
			);

			$payData = array(
				'code' => '0',
				'ptype' => '联动支付',
				'pstatus' => $pstatus[$resParams['trade_state']] ? $pstatus[$resParams['trade_state']] : $resParams['ret_msg'] . ' 状态码:' . $resParams['ret_code'],
				'ptime' => $resParams['trade_state'] == 'TRADE_SUCCESS' ? $params['created'] : '',
				'pmoney' => $resParams['amount'] ? number_format($resParams['amount'] / 100, 2, ".", ",") : '',
				'pbank' => '',
				'ispay' => ($resParams['ret_code'] == '0000' && $resParams['trade_state'] == 'TRADE_SUCCESS') ? true : false,
				'pay_trade_no' => $resParams['trade_no'],
			);
			
			$returnData = array(
				'code' => true,
				'msg' => '操作成功',
				'data' => $payData,
			);
			
			return $returnData;
		}

		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '查询操作失败'),
		);
		
		return $returnData;
	}
	
	/**
	 * 对账文件拉取
	 * @param unknown_type $params
	 */
	public function queryBill($params)
	{
		$defaultParams = array(
			'service'		=>	'download_settle_file',
			'sign_type'		=>	'RSA',
			'mer_id'		=>	$this->mer_id,
			'version'		=>	'4.0',
			'settle_date'	=>	$params['bill_date'],
		);
		$recParams = array();
		foreach ($defaultParams as $key => $value)
		{
			$recParams = $this->put($key, $value, $recParams);
		}
		
		$reqDataGet = $this->makeRequestDataByGet($recParams);
		if(!empty($reqDataGet))
		{
			$reqDataGet['payUrl'] = str_replace('https://', 'http://', $reqDataGet['payUrl']);
			$resdata = file_get_contents($reqDataGet['payUrl']);
			if($resdata)
			{
				return array('code' => true, 'msg' => '操作成功', 'data' => $resdata);
			}
		}
		
		return array('code' => false, 'msg' => '接口请求失败');
	}
	
	public function refundSubmit($params)
	{
		
	}
	
	public function queryRefund($params)
	{
		
	}

	/**
	 * 解约请求
	 */
	public function breakPayRequest($params)
	{
		$returnData = array(
			'code'	=>	false,
			'msg' 	=>	'操作失败',
			'data' 	=> 	$params,
		);

		$defaultParams = array(
			'service'		=>	'unbind_mercust_protocol_shortcut',
			'sign_type'		=>	'RSA',
			'charset'		=>	'UTF-8',
			'res_format'	=>	'HTML',
			'mer_id'		=>	$this->mer_id,
			'version'		=>	'4.0',
			'mer_cust_id'	=>	$params['uid'],
			'usr_pay_agreement_id'	=>	$params['pay_agreement_id'],
		);

		$recParams = array();
		foreach ($defaultParams as $key => $value) 
		{
			$recParams = $this->put($key, $value, $recParams);
		}

		$reqDataGet = $this->makeRequestDataByGet($recParams);
		$resParams = array();

		if(!empty($reqDataGet))
		{
			$resdata = get_meta_tags($reqDataGet['payUrl']);
			parse_str($resdata['mobilepayplatform'], $resParams);
		}

		if($resParams['ret_code'] == '0000')
		{
			$returnData = array(
				'code'	=>	true,
				'msg' 	=>	'操作成功',
				'data' 	=> 	$params,
			);
		}

		return $returnData;
	}


	/**
	 * 解约异步通知
	 */
	public function breakNotify()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => '',
			'succMsg' => '',
			'data' => array(),
		);

		$request = $_GET;

		if($request)
		{
			$reqData = $this->getNotifyRequestData($request);
			if($reqData['status'])
			{
				if($request['error_code'] == '0000' && $request['mer_cust_id'] && $request['usr_pay_agreement_id'])
				{
					$payData = array(
						'uid' => $request['mer_cust_id'],
						'pay_agreement_id' => $request['usr_pay_agreement_id'],
						'last_four_cardid' => $request['last_four_cardid'],
						'library'	=>	__CLASS__,
					);

					$returnData = array(
						'code' => true,
						'errMsg' => '',
						'succMsg' => '',
						'data' => $payData,
					);
				}
				else
				{
					$this->log($resdata, __CLASS__, $this->mer_id, __FUNCTION__);
				}
			}
			else
			{
				$message = '联动支付商户号：'. $this->mer_id . '用户银行卡解约异步通知接口验签失败，请及时留意。';
				// $this->alertEmail($message);
				$this->log($resdata, __CLASS__, $this->mer_id, __FUNCTION__);
			}

			// 返回通知
			$returnMsg = $this->getRequestMsg($request);
			$returnData['errMsg'] = $returnMsg;
			$returnData['succMsg'] = $returnMsg;
		}
		
		return $returnData;
	}

	// 插入元素
	private function put($key, $value, $data = array()) 
	{
		if (!array_key_exists($key, $data)) 
		{
		   	$data[$key] = $value;	   
		} 
		else 
		{
			// 覆盖
			$tempValue = $data[$key];
		   	$data[$key] = $value;
		}
		return $data;
	}

	// 获取元素
	private function get($key, $data = array()) 
	{
		if (array_key_exists($key, $data))
		{
			return $data[$key];
		}
		else
		{
			return null;
		}	 	
	 }

	private function makeRequestDataByGet($recParams)
	{
		$reqData = $this->makeRequestData($this->plat_pay_product_name, $recParams, self::$default['method_get']);
		return $reqData;
	}

	// 组装请求参数
	private function makeRequestData($appname, $recParams, $method)
	{
		$reqData = array();

		$funcode = $this->strTrim($recParams['service']);
		// 敏感字段加密
		$recParams = $this->doEncrypt($recParams);
		// 获取请求数据签名明文串
		$plain = $this->getSortPlain($recParams);
		// 获取请求数据签名密文串
		$sign = $this->getSignData($recParams);

		if(empty($sign))
		{
			return $reqData;
		}
	
		if($method == self::$default['method_get']) 
		{
			// 获取GET方式请求数据对象
			$url = $this->getUrlForV4($appname);
			// 获取请求参数
			$param = $this->getSortParameter($recParams);

			$reqData['payUrl'] = $url . "?" . $param . '&sign=' . urlencode($sign);	
		} 
		else if($method == self::$default['method_post']) 
		{
			// 获取POST方式请求数据对象
			$reqData['payUrl'] = $this->getUrlForV4($appname);
			$reqData['sign'] = $sign;
			$reqData = array_merge($reqData, $recParams);

		} 

		$reqData['plain'] = $plain;
		return $reqData;
	}

	// 去除字符串前后空格
	private function strTrim($str)
	{
		if($str == null)
		{
			return "";
		}
		else
		{
			return trim($str);
		}
	}

	// 敏感字段加密
	private function doEncrypt($recParams) 
	{
		// 对每个key进行正则表达式校验
		$keys = "card_id,valid_date,cvv2,pass_wd,identity_code,card_holder,recv_account,recv_user_name,identity_holder,identityCode,cardHolder,mer_cust_name,account_name";
		$chkKeys = array();
		$chkKeys = explode(",", $keys);
		if(count($chkKeys) > 0)
		{
			foreach($chkKeys as $key)
			{
				if(empty($recParams[$key]))
				{
					continue;
				}
				$recParams[$key] = iconv("UTF-8", "GBK", $recParams[$key]);
				$recParams[$key] = $this->encrypt($recParams[$key]);
			}
		}
		return $recParams;
	}

	// 对明文进行加密
	private function encrypt($data)
	{
		// 读取配置文件
		$cert_file = dirname(__FILE__) . '/umpaykey/' . $this->mer_id . '/cert_2d59.cert.pem';
		if(!File_exists($cert_file)) 
		{
			return '';
		}
		$fp = fopen($cert_file, "r");
		$public_key = fread($fp, 8192);
		fclose($fp);
		$public_key = openssl_get_publickey($public_key);
		//private encrypt
		openssl_public_encrypt($data, $crypttext, $public_key);
		//加密後產生出參數$crypttext
		//public decrypt
		//openssl_public_encrypt ( $crypttext, $newsource, $public_key );
		$encryptDate = base64_encode($crypttext);

		return $encryptDate;
	}


	// 获取签名明文串
	private function getSortPlain($recParams) 
	{
		$plain = $this->getPlainSortAndByAnd($recParams);
		return $plain;
	}

	// 组织签名明文串排序
	private function getPlainSortAndByAnd($recParams) 
	{
		$plain = "";
		$arg = "";
		$paramter = array ();
		if(!empty($recParams) && count($recParams) > 0) 
		{
			$keys = array_keys($recParams);
			foreach ( $keys as $key ) 
			{
				if($key !== "sign_type") 
				{
					$plain = $plain . $key . "=" . $recParams[$key] . "|";
					$paramter[$key . "=" . $recParams[$key]] = $key . "=" . $recParams[$key];
				}
			}
			$plain = substr($plain, 0, strlen($plain) - 1);
			// 得到从字母a到z排序后的加密参数数组
			$sort_array = $this->arg_sort($paramter); 
			
			while(list($key, $val) = each($sort_array)) 
			{
				$arg .= $val . "&";
			}
			// 去掉最后一个&字符
			$arg = substr($arg, 0, count($arg) - 2); 
		} 
		return $arg;
	}

	// 排序
	private function arg_sort($array) 
	{
		asort($array);
		return $array;
	}

	private function getSignData($recParams) 
	{
		$plain = $this->getSortPlain($recParams);
		$merId = $this->mer_id;
		$sign = $this->sign($plain, $merId);
		return $sign;
	}

	// 签名
	public static function sign($plain, $merId)
	{
		// 获取商户私钥地址配置信息
	    $priv_key_file = dirname(__FILE__) . '/umpaykey/' . $merId . '/rsa_private_key.pem';

	    if(!File_exists($priv_key_file))
	    {
	        return FALSE;
	    }
	    $fp = fopen($priv_key_file, "rb");
	
	    $priv_key = fread($fp, 8192);
	    @fclose($fp);
	    $pkeyid = openssl_get_privatekey($priv_key);
	
	    if(!is_resource($pkeyid))
	    {
	    	return FALSE;
	    }
	    // compute signature
	    @openssl_sign($plain, $signature, $pkeyid);
	    // free the key from memory
	    @openssl_free_key($pkeyid);
	    return base64_encode($signature);
    }

    // 根据功能码获取平台地址
    private function getUrlForV4($appname)
    {
		return $this->plat_url ."/" . $appname . self::$default['umpay_url'];
	}

	private function getSortParameter($recParams) 
	{
		$param = "";
		$arg = "";
		$paramter = array();
		if (!empty($recParams) && count($recParams) > 0) 
		{
			$keys = array_keys($recParams);
			foreach($keys as $key) 
			{
				$param = $param . $key . "=" . urlencode($recParams[$key]) . "&";
				$paramter[$key . "=" . urlencode($recParams[$key])] = $key . "=" . urlencode ($recParams[$key]);
			}
			$param = substr($param, 0, strlen($param) - 1);

			// 得到从字母a到z排序后的加密参数数组
			$sort_array = $this->arg_sort($paramter);

			foreach (array_values($sort_array) as $key => $value)
			{
				$arg .= $value . "&";
			}
			// 去掉最后一个&字符
			$arg = substr($arg, 0, count($arg) - 2); 
		}
		return $arg;
	}

	// 通知验证
	private function getNotifyRequestData($reqParams)
	{
		$funcode = $this->strTrim($reqParams['service']);
		switch ($funcode) 
		{
			// 支付通知
			case 'pay_result_notify':
			case 'mer_order_info_query':
			case 'unbind_mercust_protocol_shortcut':
			case 'unbind_agreement_notify_shortcut':
				$result = $this->verifyRequestData($reqParams);
				break;
			
			default:
				$result = array(
					'status'	=>	false,
					'data'		=>	$reqParams
				);
				break;
		}
		return $result;
	}

	// 验签
	private function verifyRequestData($reqParams)
	{
		$result = array(
			'status'	=>	FALSE,
			'data'		=>	$reqParams
		);

		if(!empty($reqParams))
		{
			$sign = $reqParams['sign'];
			unset($reqParams['sign']);
			$plain = $this->getPlainSortAndByAnd($reqParams);
			$plain = iconv("UTF-8", "GBK", $plain);
			if($this->verify($plain, $sign))
			{
				$result = array(
					'status'	=>	TRUE,
					'data'		=>	$reqParams
				);
			}
		}
		return $result;
	}

	// 签名数据验签
	private function verify($plain, $signature)
	{
		$cert_file = dirname(__FILE__) . '/umpaykey/' . $this->mer_id . '/cert_2d59.cert.pem';

		if(!File_exists($cert_file)) 
		{
			return FALSE;
		}

		$signature = base64_decode($signature);
		$fp = fopen($cert_file, "r");
	    $cert = fread($fp, 8192);
	    fclose($fp);

	    $pubkeyid = openssl_get_publickey($cert);
	    if(!is_resource($pubkeyid))
	    {
	        return FALSE;
	    }

	    $result = openssl_verify($plain, $signature, $pubkeyid);
	    @openssl_free_key($pubkeyid);
	    if($result == 1) 
	    {
	    	return TRUE;
	    }
	    return FALSE;
	}

	// 返回通知信息
	private function getRequestMsg($reqParams)
	{
		$param = array(
			'ret_code'	=>	'0000',
			'ret_msg'	=>	'success',
			'order_id'  => 	$reqParams['order_id'],
			'mer_id'	=>	$this->mer_id,
			'sign_type'	=>	'RSA',
			'version'	=>	'4.0',
		);

		$content = $this->notifyResponseData($param);
		$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		$html .= '<html>';
		$html .= '<head>';
		$html .= '<META NAME="MobilePayPlatform" CONTENT="' . $content . '" />';
		$html .= '<title>result</title>';
		$html .= '</head> ';
		$html .= '</html>';
		return $html;
	}

	private function notifyResponseData($reqParams)
	{
		$plain = $this->getPlainSortAndByAnd($reqParams);
		$merId = $this->mer_id;
		$sign = $this->sign($plain, $merId);
		return $plain . "&sign=" . $sign;
	}
}