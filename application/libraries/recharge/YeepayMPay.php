<?php
/**
 * 易宝移动端充值服务类
 * @author Administrator
 *
 */
define('YEEPAY_PATH', dirname(__FILE__));
// 包含RSA、AES相关加解密包
if (!class_exists('Crypt_Rijndael')) include YEEPAY_PATH . '/crypt/Crypt_Rijndael.php';
if (!class_exists('Crypt_AES'))      include YEEPAY_PATH . '/crypt/Crypt_AES.php';
if (!class_exists('Crypt_DES'))		 include YEEPAY_PATH . '/crypt/Crypt_DES.php';
if (!class_exists('Crypt_Hash'))	 include YEEPAY_PATH . '/crypt/Crypt_Hash.php';
if (!class_exists('Crypt_RSA'))		 include YEEPAY_PATH . '/crypt/Crypt_RSA.php';
if (!class_exists('Crypt_TripleDES'))include YEEPAY_PATH . '/crypt/Crypt_TripleDES.php';
if (!class_exists('Math_BigInteger'))include YEEPAY_PATH . '/crypt/Math_BigInteger.php';
require_once YEEPAY_PATH . '/RechargeAbstract.php';
class YeepayMPay extends RechargeAbstract
{
	// CURL 请求相关参数
	public $useragent = 'Yeepay MobilePay PHPSDK v1.1x';
	public $connecttimeout = 30;
	public $timeout = 30;
	public $ssl_verifypeer = FALSE;
	// CURL 请求状态相关数据
	public $http_header = array();
	public $http_code;
	public $http_info;
	public $url;
	// 相关配置参数
	protected $account;
	protected $merchantPublicKey;
	protected $merchantPrivateKey;
	protected $yeepayPublicKey;
	// 请求AES密钥
	private $AESKey;
	// 请求加密/解密相关算法工具
	private $RSA;
	private $AES;
	/**
	 * 配置信息
	 * @var unknown_type
	 */
	private $config;
	
	/**
	 * 请求第三方网关地址
	 * @var unknown_type
	 */
	private $payGateway = 'https://ok.yeepay.com/paymobile/api/pay/request';
	
	private $API_Merchant_Base_Url = 'https://ok.yeepay.com/merchant/';
	private $CI;
	
	public function __construct($config = array())
	{
		$this->CI = &get_instance();
		$this->account = $config['merchantaccount'];
		$this->merchantPrivateKey = $config['merchantPrivateKey'];
		$this->merchantPublicKey = $config['merchantPublicKey'];
		$this->yeepayPublicKey = $config['yeepayPublicKey'];
		$this->RSA = new Crypt_RSA();
		$this->RSA->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$this->RSA->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
		$this->AES = new Crypt_AES(CRYPT_AES_MODE_ECB);
		unset($config['merchantaccount'], $config['merchantPrivateKey'], $config['merchantPublicKey'], $config['yeepayPublicKey']);
		$this->config = $config;
	}
	
	/**
	 * curl请求支付信息
	 * @see RechargeAbstract::requestHttp()
	 */
	public function requestHttp($params)
	{
		$defaultParams = $this->getParams();
		$defaultParams['orderid'] = $params['trade_no'];
		$defaultParams['amount'] = (int)$params['money'];
		$defaultParams['identityid'] = (string)$params['uid'];
		$defaultParams['userip'] = $params['ip'];
		$defaultParams['idcard'] = (string)$params['id_card'];
		$defaultParams['owner'] = $params['real_name'];
		$request = $this->buildRequest($defaultParams);
		$url = $this->payGateway . '?' . http_build_query($request);
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('url' => $url),
		);
		
		return $returnData;
	}
	
	/**
	 * 充值异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$params = $this->CI->input->post();
		}
		else
		{
			$params = $this->CI->input->get();
			if(isset($params['orderId']))
			{
				unset($params['orderId']);
				unset($params['orderType']);
				unset($params['buyMoney']);
			}
		}
		
		$returnData = array(
			'code' => false,
			'errMsg' => '参数错误',
			'succMsg' => 'SUCCESS',
			'data' => array(),
		);
		
		if(empty($params['data']) || empty($params['encryptkey']))
		{
			//TODO 记录日志
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>易宝移动端商户号：'. $this->account . '通知接口接收信息失败，请及时留意。<br/>通知返回数据：' . $this->printJson($params);
			$this->alertEmail($message);
			$this->log($params, __CLASS__, $this->account, __FUNCTION__);
			
			return $returnData;
		}
		
		$AESKey = $this->getYeepayAESKey($params['encryptkey']);
		$return = $this->AESDecryptData($params['data'], $AESKey);
		$return = json_decode($return,true);
		if(!array_key_exists('sign', $return))
		{
			$errMsg = '请求返回异常';
			if(array_key_exists('error_code', $return))
			{
				$errMsg = $return['error_code'] . '|' . $return['error_msg'];
			}
			
			//TODO 记录日志
			$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>易宝移动端商户号：'. $this->account . '通知接口异常“'.$errMsg.'”，请及时留意。<br/>通知返回数据：' . $this->printJson($params);
			$this->alertEmail($message);
			$this->log($params, __CLASS__, $this->account, __FUNCTION__);
			
			return $returnData;
		}
		else
		{
			if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
			{
				$errMsg = $return['error_code'] . '|' . $return['error_msg'];
				//TODO 记录日志
				$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>易宝移动端商户号：'. $this->account . '通知接口异常“'.$errMsg.'”，请及时留意。<br/>通知返回数据：' . $this->printJson($return);
				$this->alertEmail($message);
				$this->log($return, __CLASS__, $this->account, __FUNCTION__);
				
				return $returnData;
			}
			
			if($return['status'] == 1)
			{
				$payData = array(
					'trade_no' => $return['orderid'],
					'pay_trade_no' => $return['yborderid'],
					'status' => '1',	// 成功
				);
				$returnData['code'] = true;
				$returnData['data'] = $payData;
				
				return $returnData;
			}
			
			//TODO 记录日志
			$this->log($return, __CLASS__, $this->account, __FUNCTION__);
		}
		
		return $returnData;
	}

	/**
	 * 充值同步通知
	 * @see RechargeAbstract::syncCallback()
	 */
	public function syncCallback()
	{
		return $this->notify();
	}
	
	public function formSubmit($params)
	{
		
	}
	
	public function queryOrder($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array(),
		);
		$query = array(
			'orderid'	=>	(string)$params['trade_no'],
			'yborderid'	=>	$params['yborder_id'], //空  调用时不需要传值
		);
		
		$request = $this->buildRequest($query);
		$url = $this->API_Merchant_Base_Url . 'query_server/pay_single';
		$url .= '?'.http_build_query($request);
		$data = $this->http($url, 'GET');
		if(empty($data))
		{
			return $returnData;
		}
		$return = json_decode($data,true);
		if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
		{
			$returnData['msg'] = $return['error_code'] . '|' . $return['error_msg'];
			return $returnData;
		}
		$AESKey = $this->getYeepayAESKey($return['encryptkey']);
		$return = $this->AESDecryptData($return['data'], $AESKey);
		$return = json_decode($return,true);
		if(!array_key_exists('sign', $return))
		{
			if(array_key_exists('error_code', $return))
			{
				$returnData['msg'] = $return['error_code'] . '|' . $return['error_msg'];
			}
			
			return $returnData;
		}
		else
		{
			if(array_key_exists('error_code', $return) && !array_key_exists('status', $return))
			{
				$returnData['msg'] = $return['error_code'] . '|' . $return['error_msg'];
				return $returnData;
			}
			
			$pstatus = array('0' => '未支付', '1' => '已支付', '2' => '已撤销', '3' => '阻断交易', '4' => '失败', '5' => '处理中');
			$payData = array(
				'code' => '0',
				'ptype' => '易宝Wap',
				'pstatus' => $pstatus[$return['status']],
				'ptime' => $return['status'] == '1' ? date('Y-m-d H:i:s', $return['closetime']) : '',
				'pmoney' => number_format($return['sourceamount'] / 100, 2, ".", ","),
				'pbank' => $return['bank'],
				'ispay' => $return['status'] == '1' ? true : false,
				'pay_trade_no' => $return['yborderid'],
			);
			
			$returnData = array(
				'code' => true,
				'msg' => '操作成功',
				'data' => $payData,
			);
			
			return $returnData;
		}
	}
	
	/**
	 * 对账单下载接口
	 * @param unknown_type $params
	 * @return multitype:boolean string |multitype:boolean string mixed
	 */
	public function queryBill($params)
	{
		$query = array(
			'startdate' => $params['bill_date'],
			'enddate' => $params['bill_date'],
		);
		$request = $this->buildRequest($query);
		$url = $this->API_Merchant_Base_Url . 'query_server/pay_clear_data';
		$url .= '?'.http_build_query($request);
		$response = $this->http($url, 'GET');
		if(strpos($response, 'data') == true && strpos($response, 'encryptkey') == true)
		{
			return array('code' => false, 'msg' => '接口请求失败');
		}
		
		return array('code' => true, 'msg' => '操作成功', 'data' => $response);
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
		$params['transtime'] = (int)time();
		$params['orderexpdate'] = 1440;
		
		return $params;
	}
	
	/**
	 * 创建提交到易宝的最终请求
	 *
	 * @param array $query
	 * @return array
	 */
	private function buildRequest(array $query)
	{
		$query['merchantaccount'] = $this->account;
		$sign = $this->RSASign($query);
		$query['sign'] = $sign;
		$request = array();
		$request['merchantaccount'] = $this->account;
		$request['encryptkey'] = $this->getEncryptkey();
		$request['data'] = $this->AESEncryptRequest($query);
		return $request;
	}
	
	/**
	 * 用RSA 签名请求
	 *
	 * @param array $query
	 * @return string
	 */
	protected function RSASign(array $query)
	{
		if(array_key_exists('sign', $query))
			unset($query['sign']);
		ksort($query);
		$this->RSA->loadKey($this->merchantPrivateKey);
		$sign = base64_encode($this->RSA->sign(join('', $query)));
		
		return $sign;
	}
	
	/**
	 * 通过RSA，使用易宝公钥，加密本次请求的AESKey
	 *
	 * @return string
	 */
	protected function getEncryptkey()
	{
		if(!$this->AESKey)
			$this->generateAESKey();
		$this->RSA->loadKey($this->yeepayPublicKey);
		$encryptKey = base64_encode($this->RSA->encrypt($this->AESKey));
		return $encryptKey;
	}
	
	/**
	 * 生成一个随机的字符串作为AES密钥
	 *
	 * @param number $length
	 * @return string
	 */
	protected function generateAESKey($length=16)
	{
		$baseString = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$AESKey = '';
		$_len = strlen($baseString);
		for($i=1;$i<=$length;$i++)
		{
			$AESKey .= $baseString[rand(0, $_len-1)];
		}
		$this->AESKey = $AESKey;
		return $AESKey;
	}
	
	/**
	 * 通过AES加密请求数据
	 *
	 * @param array $query
	 * @return string
	 */
	protected function AESEncryptRequest(array $query)
	{
		if(!$this->AESKey)
			$this->generateAESKey();
		$this->AES->setKey($this->AESKey);
	
		return base64_encode($this->AES->encrypt(json_encode($query)));
	}
	
	/**
	 * 返回易宝返回数据的AESKey
	 *
	 * @param unknown $encryptkey
	 * @return Ambigous <string, boolean, unknown>
	 */
	protected function getYeepayAESKey($encryptkey)
	{
		$this->RSA->loadKey($this->merchantPrivateKey);
		$yeepayAESKey = $this->RSA->decrypt(base64_decode($encryptkey));
		return $yeepayAESKey;
	}
	
	/**
	 * 通过AES解密易宝返回的数据
	 *
	 * @param string $data
	 * @param string $AESKey
	 * @return Ambigous <boolean, string, unknown>
	 */
	protected function AESDecryptData($data,$AESKey)
	{
		$this->AES->setKey($AESKey);
		return $this->AES->decrypt(base64_decode($data));
	}
	
	/**
	 *
	 * @param string $url
	 * @param string $method
	 * @param string $postfields
	 * @return mixed
	 */
	protected function http($url, $method, $postfields = NULL)
	{
		$this->http_info = array();
		$ci = curl_init();
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
		//curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);
		@curl_setopt($ci, CURLOPT_FOLLOWLOCATION, TRUE);
		$method = strtoupper($method);
		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields))
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields))
					$url = "{$url}?{$postfields}";
		}
		curl_setopt($ci, CURLOPT_URL, $url);
		$response = curl_exec($ci);
		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;
		curl_close ($ci);
		return $response;
	}
}