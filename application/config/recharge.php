<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 微信、支付宝 支付中心
if (ENVIRONMENT === 'production')
{
	// 中信支付  微信SDK
	$config['zxwxSdk'] = array(
		'886600000001432' => array(
			'merId' => '886600000001432',
			'key' => '86442638095766090284169800806464',
			'encoding' => 'UTF-8',
			'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
			'backEndUrl' => 'https://www.166cai.cn/api/recharge/zxpaySdkAsync', //异步回调地址
			'currencyType' => '156', //156：人民币
			'payGateway' => 'https://120.55.176.124:8092/MPay/backTransAction.do', //支付网关地址
		)
	);
	
	// 威富通 微信SDK
	$config['wftWxSdk'] = array(
		'101590046608' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '101590046608',
			'key' => 'bff7c1931379f18a92ac8982b3b2b90b',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'102510424357' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510424357',
			'key' => '37279adf40de130e9f086e845abb0f06',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'102510424356' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510424356',
			'key' => '82b808dba58a782c0b2b5eda41fcf8ef',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'102510034424' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510034424',
			'key' => 'ff0b0dba800de94472b69c638e913617',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'102510034423' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510034423',
			'key' => '2720e16c6905fb9239479e221b8753a9',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'103560003958' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '103560003958',
			'key' => '8e281a97c850b43214ecf0f7662eac9f',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'103520004059' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '103520004059',
			'key' => 'da6eff1ca2fbd7049f974f493cbc8077',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		)
	);
	
	//威富通 微信PC/APP扫码
	$config['wftWx'] = array(
		'102570424364' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102570424364',
			'key' => '861f99ff081a4a70e39f49191532c735',
			'service' => 'pay.weixin.native',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxCallback',
		),
		'100530025282' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '100530025282',
			'key' => '2be82a2f528712815857abb9b8e4e5f6',
			'service' => 'pay.weixin.native',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxCallback',
		)
	);

	// 现在 支付宝h5支付
	$config['xzZfbWap'] = array(
		'1491532560057310' => array(
			'appId' => '1491532560057310',	// 应用ID
			'secure_key' => 'vc1L3A8ze7dQOK9LN7JCwDeompRqw0aQ', // 商户秘钥
			'timezone' => 'Shanghai',
			'front_notify_url' => 'https://www.166cai.cn/api/recharge/xzZfbWapFront',	// 前台回调
			'back_notify_url' => 'https://www.166cai.cn/api/recharge/xzZfbWapBack',		// 后台回调
		)
	);
}
else
{
	// 中信支付  微信SDK
	$config['zxwxSdk'] = array(
		'886600000001432' => array(
			'merId' => '886600000001432',
			'key' => '86442638095766090284169800806464',
			'encoding' => 'UTF-8',
			'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
			'backEndUrl' => 'https://123.59.105.39/api/recharge/zxpaySdkAsync', //异步回调地址
			'currencyType' => '156', //156：人民币
			'payGateway' => 'https://120.55.176.124:8092/MPay/backTransAction.do', //支付网关地址
		)
	);
	
	// 威富通 微信SDK
	$config['wftWxSdk'] = array(
		'101590046608' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '101590046608',
			'key' => 'bff7c1931379f18a92ac8982b3b2b90b',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		),
		'102510424357' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510424357',
			'key' => '37279adf40de130e9f086e845abb0f06',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://www.166cai.cn/api/recharge/wftWxSdkCallback',
		),
		'102510424356' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510424356',
			'key' => '82b808dba58a782c0b2b5eda41fcf8ef',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		),
		'102510034424' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510034424',
			'key' => 'ff0b0dba800de94472b69c638e913617',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		),
		'102510034423' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102510034423',
			'key' => '2720e16c6905fb9239479e221b8753a9',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		),
		'103560003958' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '103560003958',
			'key' => '8e281a97c850b43214ecf0f7662eac9f',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		),
		'103520004059' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '103520004059',
			'key' => 'da6eff1ca2fbd7049f974f493cbc8077',
			'service' => 'pay.weixin.raw.app',
			'appid' => 'wx1315f7bd05e62fed',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxSdkCallback',
		)
	);
	
	// 威富通 微信PC/APP扫码
	$config['wftWx'] = array(
		'102570424364' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '102570424364',
			'key' => '861f99ff081a4a70e39f49191532c735',
			'service' => 'pay.weixin.native',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxCallback',
		),
		'100530025282' => array(
			'url' => 'https://pay.swiftpass.cn/pay/gateway',
			'mch_id' => '100530025282',
			'key' => '2be82a2f528712815857abb9b8e4e5f6',
			'service' => 'pay.weixin.native',
			'version' => ' 2.0',
			'charset' => 'UTF-8',
			'sign_type' => 'MD5',
			'notify_url' => 'https://123.59.105.39/api/recharge/wftWxCallback',
		)
	);
	
	// 现在 支付宝h5支付
	$config['xzZfbWap'] = array(
		'1491532560057310' => array(
			'appId' => '1491532560057310',	// 应用ID
			'secure_key' => 'vc1L3A8ze7dQOK9LN7JCwDeompRqw0aQ', // 商户秘钥
			'timezone' => 'Shanghai',
			'front_notify_url' => 'https://123.59.105.39/api/recharge/xzZfbWapFront',	// 前台回调
			'back_notify_url' => 'https://123.59.105.39/api/recharge/xzZfbWapBack',	// 后台回调
		)
	);
}

