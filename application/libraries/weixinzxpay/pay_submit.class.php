<?php

/* *
 * 类名：paySubmit
 * 功能：支付接口请求提交类
 * 详细：构造支付各接口表单HTML文本，获取远程HTTP数据
 * 版本：1.1
 * 日期：2014-04-16
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */
require_once ("pay_core.function.php");
require_once ("pay_md5.function.php");

class paySubmit 
{

	var $pay_config;

	function __construct($pay_config) 
	{
		$this->pay_config = $pay_config;
	}
	function paySubmit($pay_config) 
	{
		$this->__construct($pay_config);
	}

	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	function buildRequestMysign($para_sort) 
	{
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		$mysign = "";
		switch ($this->pay_config['signMethod']) 
		{
			case "02" :
				$mysign = md5Sign($prestr, $this->pay_config['key']);
				break;
			default :
				$mysign = "";
		}
		return $mysign;
	}

	/**
	 * 生成要请求给支付的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	function buildRequestPara($para_temp)
	{
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
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
	 * 生成要请求给连连支付的参数数组
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组字符串
	 */
	function buildRequestParaToString($para_temp) 
	{
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);

		//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		$request_data = createLinkstringUrlencode($para);

		return $request_data;
	}

	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果
	 * @param $para_temp 请求参数数组
	 * @return 支付处理结果
	 */
	function buildRequestHttp($para_temp) 
	{
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);
		$request_data = json_encode($request_data);
		$postData['sendData'] = base64_encode($request_data);
		$postStr = http_build_query($postData);
		//远程获取数据
		$result = getHttpResponsePOST($this->pay_config['payGateway'], $postStr);
		$result = str_replace('#', '+', $result); //将返回结果中#号替换成+号
		if(strpos($result, 'sendData=') !== false)
		{
			$result = substr($result, 9);
			$result = json_decode(base64_decode($result), true);
			if($result['respCode'] == '0000')
			{
				return $result;
			}
			else
			{
				log_message('log', print_r($result, true), 'zxPayCallback');
				die('交易出现异常，请稍后重试');
			}
		}
		die('交易出现异常，请稍后重试');
		return $result;
	}
	
	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付结果查询
	 * @param $para_temp 请求参数数组
	 * @return 支付处理结果
	 */
	function buildOrderSelectHttp($para_temp)
	{
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);
		$request_data = json_encode($request_data);
		$postData['sendData'] = base64_encode($request_data);
		$postStr = http_build_query($postData);
		//远程获取数据
		$result = getHttpResponsePOST($this->pay_config['payGateway'], $postStr);
		$result = str_replace('#', '+', $result); //将返回结果中#号替换成+号
		if(strpos($result, 'sendData=') !== false)
		{
			$result = substr($result, 9);
			$result = json_decode(base64_decode($result), true);
		}
		
		return $result;
	}

}
?>