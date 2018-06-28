<?php
/* *
 * MD5
 * 详细：MD5加密
 * 版本：1.2
 * 日期：2015-08-20
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 对秘钥进行加码处理
 */

/**
 * 签名字符串
 * @param $prestr 需要签名的字符串
 * @param $key 私钥
 * return 签名结果
 */
function md5Sign($prestr, $key) 
{
	$prestr = $prestr ."&key=". $key;
	return strtoupper(md5($prestr));
}

/**
 * 验证签名
 * @param $prestr 需要签名的字符串
 * @param $sign 签名结果
 * @param $key 私钥
 * return 签名结果
 */
function md5Verify($prestr, $sign, $key) {
	$prestr = $prestr ."&key=". $key;
	$mysgin = strtoupper(md5($prestr));
	if($mysgin == $sign) {
		return true;
	}
	else {
		return false;
	}
}
?>