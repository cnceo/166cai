<?php
/**
 * 中信微信充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class ZxWeixinPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;
	
	/**
	 * MD5秘钥
	 * @var unknown_type
	 */
	private $md5Key;
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
		$this->merId = $config['merId'];
		$this->md5Key = $config['key'];
		$this->payGateway = $config['payGateway'];
		unset($config['key']);
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
			'data' => array(),
		);
		
		$defaultParams = $this->getParams();
		$defaultParams['orderId'] = $params['trade_no'];
		$defaultParams['txnAmt'] = (string)$params['money'];
		
		$requestData = $this->buildRequestPara($defaultParams);
		$requestData = json_encode($requestData);
		$postData['sendData'] = base64_encode($requestData);
		$postStr = http_build_query($postData);
		//远程获取数据
		$result = $this->curlPost($this->payGateway, $postStr);
		$result = str_replace('#', '+', $result); //将返回结果中#号替换成+号
		if(strpos($result, 'sendData=') !== false)
		{
			$result = substr($result, 9);
			$result = json_decode(base64_decode($result), true);
			if($result['respCode'] == '0000')
			{
				$returnData = array(
					'code' => true,
					'msg' => '请求成功',
					'data' => $result,
				);
				
				return $returnData;
			}
		}
		
		//这里记录错误日志或邮件报警
		$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求中信商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway .'<br/>请求返回数据：' . $this->printJson($result);
		$this->alertEmail($message);
		$this->log($result, __CLASS__, $this->merId, __FUNCTION__);
		
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
			'errMsg' => 'sendData=' . base64_encode('{"respCode":"9999","respMsg":"交易失败"}'),
			'succMsg' => 'sendData=' . base64_encode('{"respCode":"0000","respMsg":"OK"}'),
			'data' => array(),
		);
		
		$result = file_get_contents("php://input");
		$result = str_replace('#', '+', $result);
		if(strpos($result, 'sendData=') === false)
		{
			//记日志
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>中信商户号：'. $this->merId . '异步通知接口接收信息失败，请及时留意。<br/>通知返回数据：' . $this->printJson($result);
			$this->alertEmail($message);
			$this->log($result, __CLASS__, $this->merId, __FUNCTION__);
			
			return $returnData;
		}
		
		$str = substr($result, 9);
		if(empty($str))
		{
			//记日志
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>中信商户号：'. $this->merId . '异步通知接口接收信息失败，请及时留意。<br/>通知返回数据：' . json_encode($str);
			$this->alertEmail($message);
			$this->log($str, __CLASS__, $this->merId, __FUNCTION__);
			
			return $returnData;
		}
		
		$val = base64_decode($str);
		$val = json_decode($val, true);
		$verify_result = $this->verifyNotify($val);
		if($verify_result)
		{
			$payData = array(
				'trade_no' => $val['orderId'],
				'pay_trade_no' => $val['txnSeqId'],
				'status' => '1',	// 成功
			);
			$returnData['code'] = true;
			$returnData['data'] = $payData;
			
			return $returnData;
		}
		
		//记日志
		$message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>中信商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>通知返回数据：' . $this->printJson($val);
		$this->alertEmail($message);
		$this->log($val, __CLASS__, $this->merId, __FUNCTION__);
		
		return $returnData;
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
		$returnData = array(
			'code' => false,
			'msg' => '请求失败',
			'data' => array('charset' => 'utf-8','html' => '<body>请求失败</body>'),
		);
		$respone = $this->requestHttp($params);
		if(!$respone['code'])
		{
			return $returnData;
		}
		
		$html =  '<body onLoad="document.weixin.submit();">';
		$html .= '<form name="weixin" action="/wallet/getWinxin" method="post">';
		$html .= '<input type="hidden" name="orderId" value="'. $respone['data']['orderId'] .'">';
		$html .= '<input type="hidden" name="orderTime" value="'. $respone['data']['orderTime'].'">';
		$html .= '<input type="hidden" name="txnAmt" value="'. $respone['data']['txnAmt'].'">';
		$html .= '<input type="hidden" name="codeUrl" value="'. $respone['data']['codeUrl'].'">';
		$html .= '</form></body>';
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('charset' => 'utf-8','html' => $html),
		);
		
		return $returnData;
	}
	
	public function queryOrder($params)
	{
		$pparams = array(
			'encoding' => $this->config['encoding'],
			'signMethod' => $this->config['signMethod'],
			'txnType' => '38',
			'txnSubType' => '383000', 						//交易子类型 010130：二维码支付 010131：公众号支付 010132：APP支付（主扫）
			'channelType' => '6002', 						//接入渠道  6002：商户互联网渠道;
			'payAccessType' => '02', 						//接入支付类型 02：接口支付
			'merId' => $this->merId, 						//普通商户或一级商户的商户号
			'origOrderId' => $params['trade_no'],
			'fetchOrderNo' => 'Y',
			'origOrderTime' => date('YmdHis', strtotime($params['created'])),
			'orderTime' => date('YmdHis', time()),
		);
		
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($pparams);
		$request_data = json_encode($request_data);
		$postData['sendData'] = base64_encode($request_data);
		$postStr = http_build_query($postData);
		//远程获取数据
		$result = $this->curlPost($this->payGateway, $postStr);
		$result = str_replace('#', '+', $result); //将返回结果中#号替换成+号
		if(strpos($result, 'sendData=') !== false)
		{
			$result = substr($result, 9);
			$result = json_decode(base64_decode($result), true);
			if($result['respCode'] == '0000')
			{
				$ptype = array('payWeix' => '中信微信', 'zxwxSdk' => '中信微信SDK');
				$pstatus = array('SUCCESS' => '已付款', 'NOTPAY' => '未支付');
				$payData = array(
					'code' => '0',
					'ptype' => $ptype[$params['additions']],
					'pstatus' => $pstatus[$result['origRespCode']],
					'ptime' => '',
					'pmoney' => number_format($result['txnAmt'] / 100, 2, ".", ","),
					'pbank' => '',
					'ispay' => $result['origRespCode'] == 'SUCCESS' ? true : false,
					'pay_trade_no' => $result['origSeqId'],
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
			'data' => array('code' => 1, 'msg' => '操作失败'),
		);
		
		return $returnData;
	}
	
	/**
	 * 对账接口
	 * @param unknown_type $params
	 */
	public function queryBill($params)
	{
		$pparams = array(
			'userName' => $this->merId,
			'userPwd'  => $params['userPwd'],
			'date' => $params['bill_date'],
		);
		$request_data = $this->buildRequestPara($pparams);
		$request_data = json_encode($request_data);
		$result = $this->curlPost('https://120.55.176.124:8091/MerWeb/StatementQueryApi', $request_data);
		if($this->recode != '200')
		{
			$data = json_decode($result, true);
			if($data['respCode'] != '1010')
			{
				return array('code' => false, 'msg' => '接口请求失败');
			}
			else
			{
				return array('code' => true, 'msg' => '无数据', 'data' => '1010,没有交易数据');
			}
		}
		
		return array('code' => true, 'msg' => '操作成功', 'data' => $result);
	}
	
	public function refundSubmit($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '操作失败'),
		);
		
		$pparams = array(
			'encoding' => $this->config['encoding'],
			'signMethod' => $this->config['signMethod'],
			'txnType' => '04',
			'txnSubType' => '040441',
			'channelType' => '6002', 						//接入渠道  6002：商户互联网渠道;
			'payAccessType' => '02', 						//接入支付类型 02：接口支付
			'merId' => $this->merId, 					//普通商户或一级商户的商户号
			'origTxnSeqId' => $params['trade_no'],		//商户充值订单号
			'origSettleDate' => date('Ymd', strtotime($params['created'])),
			'orderId' => $params['refundId'],
			'orderTime' => date('YmdHis', time()),
			'txnAmt' => (string)$params['money'],
			'currencyType' => $this->config['currencyType'],
		);
		
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($pparams);
		$request_data = json_encode($request_data);
		$postData['sendData'] = base64_encode($request_data);
		$postStr = http_build_query($postData);
		//远程获取数据
		$result = $this->curlPost($this->payGateway, $postStr);
		$result = str_replace('#', '+', $result); //将返回结果中#号替换成+号
		if(strpos($result, 'sendData=') !== false)
		{
			if($result['respCode'] == '0000')
			{
				$payType = array('payWeix' => 5, 'zxwxSdk' => 8);
				$refundData = array(
					'trade_no' => $params['refundId'],
					'recharge_trade_no' => $params['trade_no'],
					'partner_trade_no' => $result['txnSeqId'],
					'money' => $result['txnAmt'],
					'uid' => $params['uid'],
					'status' => '0',
					'pay_type' => $params['pay_type'],
					'created' => date('Y-m-d H:i:s', strtotime($result['txnTime'])),
				);
				
				$returnData = array(
					'code' => true,
					'msg' => '操作成功',
					'data' => $refundData,
				);
			}
			else
    		{
    			$returnData['data']['msg'] = $result['respCode'] . $result['respMsg'];
    		}
		}
		
		return $returnData;
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
		$params['orderTime'] = date('YmdHis', time());
		$params['orderTimeExpire'] = date('YmdHis', strtotime('+7 day'));
		
		return $params;
	}
	
	/**
	 * 生成要请求给支付的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	private function buildRequestPara($para_temp)
	{
		$para_filter =$this->paraFilter($para_temp);
		$para_sort = $this->argSort($para_filter);
		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['signAture'] = $mysign;
		foreach ($para_sort as $key => $value)
		{
			$para_sort[$key] = $value;
		}
	
		return $para_sort;
	}
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	private function buildRequestMysign($para_sort)
	{
		$prestr = $this->createLinkstring($para_sort);
		$mysign = $this->md5Sign($prestr, $this->md5Key);
		
		return $mysign;
	}
	
	/**
	 * 验证签名
	 * @param $prestr 需要签名的字符串
	 * @param $sign 签名结果
	 * @param $key 私钥
	 * return 签名结果
	 */
	private function md5Verify($prestr, $sign, $key) 
	{
		$prestr = $prestr ."&key=". $key;
		$mysgin = strtoupper(md5($prestr));
		if($mysgin == $sign) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * 签名字符串
	 * @param $prestr 需要签名的字符串
	 * @param $key 私钥
	 * return 签名结果
	 */
	private function md5Sign($prestr, $key) 
	{
		$prestr = $prestr ."&key=". $key;
		return strtoupper(md5($prestr));
	}
	
	/**
	 *对异步通知接收到消息进行验证
	 * @return 验证结果
	 */
	private function verifyNotify($val = array())
	{
		//首先对获得的商户号进行比对
		if ($val['merId'] != $this->merId)
		{
			//商户号错误
			return false;
		}
		if($val['respCode'] != '0000')
		{
			return false;
		}
	
		$parameter = $val;
		unset($parameter['signAture']); //该字段不参与验签
		if (!$this->getSignVeryfy($parameter, $val['signAture']))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @return 签名验证结果
	 */
	private function getSignVeryfy($para_temp, $sign)
	{
		$para_filter = $this->paraFilter($para_temp);
		$para_sort = $this->argSort($para_filter);
		$prestr = $this->createLinkstring($para_sort);
		$isSgin = false;
		$isSgin = $this->md5Verify($prestr, $sign, $this->md5Key);
	
		return $isSgin;
	}
}