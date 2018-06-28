<?php


/* *
 * 类名：payNotify
 * 功能：支付通知处理类
 * 详细：处理支付各接口通知返回
 * 版本：1.1
 * 日期：2014-04-16
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。

 *************************注意*************************
 * 调试通知返回时，可查看或改写log日志的写入TXT里的数据，来检查通知返回是否正常
 */

require_once ("pay_core.function.php");
require_once ("pay_md5.function.php");

class payNotify {
	var $pay_config;

	function __construct($pay_config) {
		$this->pay_config = $pay_config;
	}
	function payNotify($pay_config) {
		$this->__construct($pay_config);
	}
	/**
	 * 针对notify_url验证消息是否是连连支付发出的合法消息
	 * @return 验证结果
	 */
	function verifyNotify($valStr = '') 
	{
		if(empty($valStr))
		{
			return false;
		}
		//生成签名结果
		$val = base64_decode($valStr);
		$val = json_decode($val, true);
		//首先对获得的商户号进行比对
		if ($val['merId'] != $this->pay_config['merId']) 
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
	function getSignVeryfy($para_temp, $sign) 
	{
		//除去待签名参数数组中的空值和签名参数
		$para_filter = paraFilter($para_temp);
		//对待签名参数数组排序
		$para_sort = argSort($para_filter);
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = createLinkstring($para_sort);
		$isSgin = false;
		switch ($this->pay_config['signMethod']) 
		{
			case "02" :
				$isSgin = md5Verify($prestr, $sign, $this->pay_config['key']);
				break;
			default :
				$isSgin = false;
		}

		return $isSgin;
	}

}
?>
