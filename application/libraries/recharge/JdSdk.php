<?php
/**
 * 京东支付充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class JdSdk extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;
	
	/**
	 * 商户DES密钥
	 * @var unknown_type
	 */
	private $desKey;
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
		$this->merId = $config['merchant'];
		$this->desKey = $config['desKey'];
		$this->payGateway = $config['payGateway'];
		unset($config['desKey']);
		$this->config = $config;
	}
	
	/**
	 * curl请求支付信息
	 * @see RechargeAbstract::requestHttp()
	 */
	public function requestHttp($params)
	{
		$returnData = array(
			'code'	=>	false,
			'msg'  	=> 	'请求错误',
			'data' 	=> 	$params,
		);

		// 必要参数
		$reqParams = array(
			'version'	=>	$this->config['version'],
			'merchant'	=>	$this->config['merchant'],
			'tradeNum'	=>	$params['trade_no'],
			'tradeTime'	=>	date('YmdHis', time()),
			'amount'	=>	(string)$params['money'],
			'tradeName'	=>	$this->config['tradeName'],
			'tradeDesc'	=>	$this->config['tradeDesc'],
			'orderType'	=>	$this->config['orderType'],
			'notifyUrl'	=>	$this->config['notifyUrl'],
			'currency'	=>	$this->config['currency'],
			'ip'		=>	$params['ip'],	
			'specId'	=>	$params['id_card'],
			'specName'	=>	$params['real_name'],
			'userId'	=>	$params['uid'],
		);

		// 加密报文
		$reqXmlStr = $this->encryptReqXml($reqParams);
		
		// 统一下单接口获取订单号
		list($return_code, $return_content) = $this->http_post_data($this->payGateway, $reqXmlStr);
		if($return_code == '200' && !empty($return_content))
		{
			// 解密报文
			$resData = array();
			$flag = $this->decryptResXml($return_content, $resData);
			if($flag && $resData['result']['code'] == '000000' && !empty($resData['orderId']))
			{
				$resParams = array(
					'orderId'	=>	$resData['orderId'],
					'merchant'	=>	$resData['merchant'],
				);
				$resParams = $this->createSign($resParams);
				$resParams['appid'] = $this->config['appid'];
				$resParams['trade_no'] = $params['trade_no'];

				$returnData = array(
					'code'	=>	true,
					'msg' 	=> 	'请求成功',
					'data' 	=> 	$resParams,
				);
				return $returnData;
			}
			else
			{
				$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求京东SDK支付商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode(simplexml_load_string($return_content));
				$this->alertEmail($message);
			}
		}
		else
		{
			$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求京东SDK支付商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode(simplexml_load_string($return_content));
			$this->alertEmail($message);
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
			'errMsg' => '验签失败',
			'succMsg' => '验签成功',
			'data' => array(),
		);
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$resdata = array();
		$falg = $this->decryptResXml($xml, $resdata);
		if($falg)
		{
			if($resdata['result']['code'] == '000000' && $resdata['status'] == '2')
			{
				$payData = array(
					'trade_no' => $resdata['tradeNum'],
					'pay_trade_no' => 'jd_' . $resdata['tradeNum'],
					'status' => '1',	// 成功
				);
				
				$returnData['code'] = true;
				$returnData['data'] = $payData;
				
				return $returnData;
			}
			else
			{
				$this->log($resdata, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
		else
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>京东SDK支付商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($resdata);
            $this->alertEmail($message);
            $this->log($resdata, __CLASS__, $this->merId, __FUNCTION__);
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
		$desKey = $this->desKey;
		$keys = base64_decode($desKey);
		$postData = $_POST;
		$sign = $postData['sign'];
		unset($postData['sign']);
		$param = array();
		foreach ($postData as $key => $value)
		{
			if($value != null && $value != "")
			{
				$param[$key]=$this->decrypt4HexStr($keys, $value);
			}
		}
		$strSourceData = $this->signString($param, array());
		$decryptStr = $this->decryptByPublicKey($sign);
		$sha256SourceSignString = hash ( "sha256", $strSourceData);
		if($decryptStr == $sha256SourceSignString)
		{
			if($param['status'] == '0')
			{
				$payData = array(
					'trade_no' => $param['tradeNum'],
				);
				$returnData = array(
					'code' => true,
					'errMsg' => 'failure',
					'succMsg' => 'success',
					'data' => $payData,
				);
					
				return $returnData;
			}
			$this->log($param, __CLASS__, $this->merId, __FUNCTION__);
		}
		else
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>京东SDK支付商户号：'. $this->merId . '同步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($_POST);
            $this->alertEmail($message);
            $this->log($param, __CLASS__, $this->merId, __FUNCTION__);
		}
		
		return $returnData;
	}
	
	public function formSubmit($params)
	{
		
	}
	
	public function queryOrder($params)
	{
		$pparams = array(
			'version' => $this->config['version'],
			'merchant' => $this->merId,
			'tradeNum' => $params['trade_no'],
			'oTradeNum' => '', //原交易号
			'tradeType' => '0',
		);
		$queryUrl = 'https://paygate.jd.com/service/query';
		$reqXmlStr = $this->encryptReqXml($pparams);
		list ( $return_code, $return_content )  = $this->http_post_data($queryUrl, $reqXmlStr);
		$resData1 = array();
		$flag = $this->decryptResXml($return_content, $resData1);
		if($flag)
		{
			if($resData1['result']['code'] == '000000')
			{
				$pstatus = array('0' => '创建', '1' => '处理中', '2' => '成功', '3' => '失败', '4' => '关闭');
				$payData = array(
					'code' => '0',
					'ptype' => '京东支付',
					'pstatus' => $pstatus[$resData1['status']],
					'ptime' => $resData1['status'] == '2' ? date('Y-m-d H:i:s', strtotime($resData1['payList']["pay"]["tradeTime"])) : '',
					'pmoney' => number_format($resData1['amount'] / 100, 2, ".", ","),
					'pbank' => '',
					'ispay' => $resData1['status'] == '2' ? true : false,
					'pay_trade_no' => 'jd_'.$params['trade_no'],
				);
				
				$returnData = array(
					'code' => true,
					'msg' => '操作成功',
					'data' => $payData,
				);
				
				return $returnData;
			}
		}
		
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '验签失败'),
		);
		
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
		
	}
	
	private function signWithoutToHex($params,$unSignKeyList) 
	{
		ksort($params);
		$sourceSignString = $this->signString ( $params, $unSignKeyList );
		$sha256SourceSignString = hash ( "sha256", $sourceSignString);
		return $this->encryptByPrivateKey($sha256SourceSignString);
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
	
	private function encryptByPrivateKey($data) 
	{
		$pi_key =  openssl_pkey_get_private(file_get_contents(dirname(__FILE__) . '/jdpaykey/' . $this->merId . '/seller_rsa_private_key.pem'));
		//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id
		$encrypted="";
		openssl_private_encrypt($data,$encrypted,$pi_key,OPENSSL_PKCS1_PADDING);//私钥加密
		$encrypted = base64_encode($encrypted);//加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
		return $encrypted;
	}
	
	private function decryptByPublicKey($data) 
	{
		$pu_key =  openssl_pkey_get_public(file_get_contents(dirname(__FILE__) . '/jdpaykey/' . $this->merId . '/wy_rsa_public_key.pem'));//这个函数可用来判断公钥是否是可用的，可用返回资源id Resource id
		$decrypted = "";
		$data = base64_decode($data);
		openssl_public_decrypt($data,$decrypted,$pu_key);//公钥解密
		return $decrypted;
	}
	
	/**
	 * 将元数据进行补位后进行3DES加密
	 * <p/>
	 * 补位后 byte[] = 描述有效数据长度(int)的byte[]+原始数据byte[]+补位byte[]
	 *
	 * @param
	 *        	sourceData 元数据字符串
	 * @return 返回3DES加密后的16进制表示的字符串
	 */
	private function encrypt2HexStr($keys, $sourceData) 
	{
		$source = array ();
		// 元数据
		$source = $this->getBytes ( $sourceData );
		// 1.原数据byte长度
		$merchantData = count($source);
		// 2.计算补位
		$x = ($merchantData + 4) % 8;
		$y = ($x == 0) ? 0 : (8 - $x);
		// 3.将有效数据长度byte[]添加到原始byte数组的头部
		$sizeByte = $this->integerToBytes ( $merchantData );
		$resultByte = array ();
	
		for($i = 0; $i < 4; $i ++) 
		{
			$resultByte [$i] = $sizeByte [$i];
		}
		// 4.填充补位数据
		for($j = 0; $j < $merchantData; $j ++) 
		{
			$resultByte [4 + $j] = $source [$j];
		}
		for($k = 0; $k < $y; $k ++) 
		{
			$resultByte [$merchantData + 4 + $k] = 0x00;
		}
		$desdata = $this->encrypt ( $this->toStr ( $resultByte ), $keys );
		
		return $this->strToHex ( $desdata );
	}
	
	/**
	 * 3DES 解密 进行了补位的16进制表示的字符串数据
	 * @param unknown_type $keys
	 * @param unknown_type $data
	 */
	private function decrypt4HexStr($keys, $data) 
	{
		$hexSourceData = array ();
		$hexSourceData = $this->hexStrToBytes ($data);
		// 解密
		$unDesResult = $this->decrypt ($this->toStr($hexSourceData),$keys);
		$unDesResultByte = $this->getBytes($unDesResult);
		$dataSizeByte = array ();
		for($i = 0; $i < 4; $i ++) 
		{
			$dataSizeByte [$i] = $unDesResultByte [$i];
		}
		
		// 有效数据长度
		$dsb = $this->byteArrayToInt( $dataSizeByte, 0 );
		$tempData = array ();
		for($j = 0; $j < $dsb; $j++) 
		{
			$tempData [$j] = $unDesResultByte [4 + $j];
		}
			
		return $this->hexTobin ($this->bytesToHex ( $tempData ));
	}
	
	/**
	 * 加密算法
	 * @param unknown_type $input
	 * @param unknown_type $key
	 */
	private function encrypt($input, $key) 
	{
		$size = mcrypt_get_block_size ( 'des', 'ecb' );
		$td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' );
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		// 使用MCRYPT_3DES算法,cbc模式
		@mcrypt_generic_init ( $td, $key, $iv );
		// 初始处理
		$data = mcrypt_generic ( $td, $input );
		// 加密
		mcrypt_generic_deinit ( $td );
		// 结束
		mcrypt_module_close ( $td );
	
		return $data;
	}
	
	/**
	 * 解密算法
	 * @param unknown_type $encrypted
	 * @param unknown_type $key
	 */
	private function decrypt($encrypted, $key) 
	{
		//$encrypted = base64_decode($encrypted);
		$td = mcrypt_module_open ( MCRYPT_3DES, '', 'ecb', '' ); // 使用MCRYPT_DES算法,cbc模式
		$iv = @mcrypt_create_iv ( mcrypt_enc_get_iv_size ( $td ), MCRYPT_RAND );
		$ks = mcrypt_enc_get_key_size ( $td );
		@mcrypt_generic_init ( $td, $key, $iv ); // 初始处理
		$decrypted = mdecrypt_generic ( $td, $encrypted ); // 解密
		mcrypt_generic_deinit ( $td ); // 结束
		mcrypt_module_close ( $td );
		return $decrypted;
	}
	
	/**
	 * 将String字符串转换为byte数组
	 */
	private function getBytes($string) 
	{
		$bytes = array ();
		for($i = 0; $i < strlen ( $string ); $i ++) 
		{
			$bytes [] = ord ( $string [$i] );
		}
		
		return $bytes;
	}
	
	/**
	 * 将int数据转换为byte数组
	 * @param unknown_type $val
	 * @return multitype:boolean
	 */
	private function integerToBytes($val) 
	{
		$byt = array ();
		$byt [0] = ($val >> 24 & 0xff);
		$byt [1] = ($val >> 16 & 0xff);
		$byt [2] = ($val >> 8 & 0xff);
		$byt [3] = ($val & 0xff);
		
		return $byt;
	}
	
	/**
	 * 将十进制字符串转换为十六进制字符串
	 * @param unknown_type $string
	 */
	private function strToHex($string)
	{
		$hex = "";
		for($i = 0; $i < strlen ( $string ); $i ++) 
		{
			$tmp = dechex ( ord ( $string [$i] ) );
			if (strlen ( $tmp ) == 1) 
			{
				$hex .= "0";
			}
			$hex .= $tmp;
		}
		$hex = strtolower ( $hex );
		
		return $hex;
	}
	
	/**
	 * 将字节数组转化为String类型的数据
	 * @param unknown_type $bytes
	 */
	private function toStr($bytes) 
	{
		$str = '';
		foreach ( $bytes as $ch ) 
		{
			$str .= chr ( $ch );
		}
	
		return $str;
	}
	
	/**
	 * 转换一个16进制hexString字符串为十进制byte数组
	 * @param unknown_type $hexString 需要转换的十六进制字符串
	 */
	private function hexStrToBytes($hexString) 
	{
		$bytes = array ();
		for($i = 0; $i < strlen ( $hexString ) - 1; $i += 2) 
		{
			$bytes [$i / 2] = hexdec ( $hexString [$i] . $hexString [$i + 1] ) & 0xff;
		}
	
		return $bytes;
	}
	
	/**
	 * 将byte数组 转换为int
	 * @param unknown_type $b
	 * @param unknown_type $offset
	 */
	private function byteArrayToInt($b, $offset) 
	{
		$value = 0;
		for($i = 0; $i < 4; $i ++) 
		{
			$shift = (4 - 1 - $i) * 8;
			$value = $value + ($b [$i + $offset] & 0x000000FF) << $shift; // 往高位游
		}
		
		return $value;
	}
	
	private function hexTobin($hexstr)
	{
		$n = strlen($hexstr);
		$sbin = "";
		$i = 0;
		while($i<$n)
		{
			$a = substr($hexstr, $i, 2);
			$c = pack("H*", $a);
			if ($i == 0)
			{
				$sbin = $c;
			}
			else 
			{
				$sbin .= $c;
			}
			
			$i += 2;
		}
		
		return $sbin;
	}
	
	/**
	 * 字符串转16进制
	 * @param unknown_type $bytes
	 */
	private function bytesToHex($bytes) 
	{
		$str = $this->toStr ( $bytes );
		return $this->strToHex ( $str );
	}
	
	/**
	 * 解密xml数据
	 * @param unknown_type $resultData
	 * @param unknown_type $resData
	 */
	private function decryptResXml($resultData,&$resData)
	{
		$resultXml = simplexml_load_string($resultData);
		$resultObj = json_decode(json_encode($resultXml),TRUE);
		$encryptStr = $resultObj["encrypt"];
		$encryptStr=base64_decode($encryptStr);
		$keys = base64_decode($this->desKey);
		$reqBody = $this->decrypt4HexStr($keys, $encryptStr);
	
		$bodyXml = simplexml_load_string($reqBody);
		$resData = json_decode(json_encode($bodyXml),TRUE);
	
		$inputSign = $resData["sign"];
	
		$startIndex = strpos($reqBody,"<sign>");
		$endIndex = strpos($reqBody,"</sign>");
		$xml = '';
		if($startIndex != false && $endIndex != false)
		{
			$xmls = substr($reqBody, 0,$startIndex);
			$xmle = substr($reqBody,$endIndex+7,strlen($reqBody));
			$xml=$xmls.$xmle;
		}
		
		$sha256SourceSignString = hash("sha256", $xml);
		$decryptStr = $this->decryptByPublicKey($inputSign);
		$flag = false;
		if($decryptStr == $sha256SourceSignString)
		{
			$flag=true;
		}
		
		$resData["version"] = $resultObj["version"];
		$resData["merchant"] = $resultObj["merchant"];
		$resData["result"] = $resultObj["result"];
		
		return $flag;
	}
	
	/**
	 * 加密数据并返回xml
	 * @param unknown_type $param
	 * @return unknown
	 */
	private function encryptReqXml($param)
	{
		$dom = $this->arrtoxml($param);
		$xmlStr = $this->xmlToString($dom);
		$sha256SourceSignString = hash("sha256", $xmlStr);
		$sign = $this->encryptByPrivateKey($sha256SourceSignString);
		$rootDom = $dom->getElementsByTagName("jdpay");
		$signDom = $dom->createElement("sign");
		$signDom = $rootDom->item(0)->appendChild($signDom);
		$signText = $dom->createTextNode($sign);
		$signText = $signDom->appendChild($signText);
		$data = $this->xmlToString($dom);
	
		$keys = base64_decode($this->desKey);
		$encrypt = $this->encrypt2HexStr($keys, $data);
		$encrypt = base64_encode($encrypt);
		$reqParam = array();
		$reqParam["version"] = $param["version"];
		$reqParam["merchant"] = $param["merchant"];
		$reqParam["encrypt"] = $encrypt;
		$reqDom = $this->arrtoxml($reqParam, 0, 0);
		$reqXmlStr = $this->xmlToString($reqDom);
		
		return $reqXmlStr;
	}
	
	/**
	 * 数组转xml
	 * @param unknown_type $arr
	 * @param unknown_type $dom
	 * @param unknown_type $item
	 */
	private function arrtoxml($arr,$dom=0,$item=0)
	{
		//ksort($arr);
		if (!$dom)
		{
	
			$dom = new \DOMDocument("1.0","UTF-8");
		}
		if(!$item)
		{
			$item = $dom->createElement("jdpay");
			$item = $dom->appendChild($item);
		}
	
		foreach ($arr as $key=>$val)
		{
			$itemx = $dom->createElement(is_string($key)?$key:"item");
			$itemx = $item->appendChild($itemx);
			if (!is_array($val))
			{
				$text = $dom->createTextNode($val);
				$text = $itemx->appendChild($text);
					
			}
			else 
			{
				$this->arrtoxml($val,$dom,$itemx);
			}
		}
		
		return $dom;
	}
	
	/**
	 * xml转换成字符串
	 * @param unknown_type $dom
	 */
	private function xmlToString($dom)
	{
		$xmlStr = $dom->saveXML();
		$xmlStr = str_replace("\r", "", $xmlStr);
		$xmlStr = str_replace("\n", "", $xmlStr);
		$xmlStr = str_replace("\t", "", $xmlStr);
		$xmlStr = preg_replace("/>\s+</", "><", $xmlStr);
		$xmlStr = preg_replace("/\s+\/>/", "/>", $xmlStr);
		$xmlStr = str_replace("=utf-8", "=UTF-8", $xmlStr);
		
		return $xmlStr;
	}
	
	/**
	 * 查询curl post请求
	 * @param unknown_type $url
	 * @param unknown_type $data_string
	 */
	private function http_post_data($url, $data_string ) 
	{
	
		$cacert = '';	//CA根证书  (目前暂不提供)
		$CA = false ; 	//HTTPS时是否进行严格认证
		$TIMEOUT = 30;	//超时时间(秒)
		$SSL = substr($url, 0, 8) == "https://" ? true : false;
	
		$ch = curl_init ();
		if ($SSL && $CA) 
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 	// 	只信任CA颁布的证书
			curl_setopt($ch, CURLOPT_CAINFO, $cacert); 			// 	CA根证书（用来验证的网站证书是否是CA颁布）
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 		//	检查证书中是否设置域名，并且是否与提供的主机名匹配
		} 
		else if ($SSL && !$CA) 
		{
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 	// 	信任任何证书
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); 		// 	检查证书中是否设置域名
		}
	
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $TIMEOUT);
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT-2);
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data_string );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array (
				'Content-Type:application/xml;charset=utf-8',
				'Content-Length:' . strlen( $data_string )
		) );
	
		ob_start();
		curl_exec($ch);
		$return_content = ob_get_contents();
		ob_end_clean();
	
		$return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		return array (
				$return_code,
				$return_content
		);
	}

	// 参数加签
	public function createSign($params) 
	{
		$signPars = "";
		ksort($params);
		foreach($params as $k => $v) 
		{
			if("" != $v && "sign" != $k) 
			{
				$signPars .= $k . "=" . $v . "&";
			}
		}
		$signPars .= "key=" . $this->config['md5key'];
		$params['signData'] = strtolower(md5($signPars));
		return $params;	
	}	
}