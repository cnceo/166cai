<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 支付中心
if (ENVIRONMENT === 'production')
{
	// 易宝支付 移动端
	$config['yeepay'] = array(
		'merchantaccount' => '10013356857',
		'merchantPrivateKey' => 'MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAMOuU5/kGQnwHgA/KnapiSIBF93QPz557SjZLhD+d9coK+RShcPaWd8Lbewe4QX8RCK4BpZ0Hx2EkHrui0xnceTWQBuKTvS894N+RXPOFkoDaF3CbRWHtnq5uiJuEKahT2nRxWjBlrty63mTjsHPIg7HCvjVQwhID17Zyf35+BQzAgMBAAECgYAcDf5HSjicyCRE/MllweC7U1TMpHKszmZGGP0VyqX73vKMBZjP/5oq9ESKOdMhPI24PJIVOAjN/peISHMIPTyCxdL6ruBLh1cvquS2CAjE4HoCE6j+/3NwtlG3YS2KKhYCxZL6Iccd7Q3d9Hs7lpnYtVvJBDbNo5hBD653hCjyoQJBAP4UjSZGyp17a6+OowDq2k0yNN3IaaSUd053bZSJCFvEvnk6XdN7OxQf8++ONIyFIY+rJkY8a9Kl8Kw69m5mT+8CQQDFKNFdTYVZ7zShlcTLBdAEtZmJH4gFEalZ+aRQg36tSc0kSvgOOScb4qdAk+Tw8it3jTQZHJM9IBE3tPotgTv9AkEAiRNTV0wn8bBtV2hvnoYVwkIM7X47KHSErUuXTeRkIwZQ8JxBlF/ObrwSYbJpvUnx4k2mt4vPa/TklDa3TrZZ9wJAA5tmkS1s5iNRNC+YRRqbHqrv3ylbhLQ5A/NkRTDSrXrvLN3OQfxbsc/ovW63Po0/rFVCAb+bFgAzMHC4LwLxRQJAGqFzRALbvIqQXeO3lMgKJmHduYd2jMbFr8pitvm7ssBHmGHF9soPPs9FEGmRw+nhNfUaNtu43/l3nf70WwFlrA==',
		'merchantPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDrlOf5BkJ8B4APyp2qYkiARfd0D8+ee0o2S4Q/nfXKCvkUoXD2lnfC23sHuEF/EQiuAaWdB8dhJB67otMZ3Hk1kAbik70vPeDfkVzzhZKA2hdwm0Vh7Z6uboibhCmoU9p0cVowZa7cut5k47BzyIOxwr41UMISA9e2cn9+fgUMwIDAQAB',
		'yeepayPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqO8vNhPFGIpAZjqaOh9A7Xc7gYCMIMGTnQn0xBjtnjPHdk973EUwMUpiBCvZaNulv/GRpt8d/FUaWMcFfkPZxbt0Eu9ry1DavaoysAOCmH9AyOurfinYojaoy46JEkrVlCjtGpvxTDnx3gpV1qyx1ekx1DKTeMRTyicqr/eT/3wIDAQAB',
		'callbackurl' => 'https://www.166cai.com/api/pay/yeepayMPayCallback',  //后台回调地址
		'fcallbackurl' => 'http://www.166cai.com/api/pay/yeepayMPayReturnUrl', //前台回调地址
	);
	//易宝支付 网页端
	$config['yeepayWeb'] = array(
		'weixin' => array(
			'p1_MerId' => '10013356857',
			'merchantKey' => '4F51gPHeY234zq4IV042v510hZw833V7Z4zp7p9P96I3F7yA7ZjY0yq2m258',
			'reqURL_onLine' => 'https://www.yeepay.com/app-merchant-proxy/node',
			'callback' => 'https://www.166cai.com/api/pay/yeepayWebCallback'
		),
		'other' => array(
			'p1_MerId' => '10013415282',
			'merchantKey' => 'af7mZ4DIM2Z57YAn381Y85y4046m7Xw6a131X9k85Fc49QSY83I31dJ9aCj0',
			'reqURL_onLine' => 'https://www.yeepay.com/app-merchant-proxy/node',
			'callback' => 'https://www.166cai.com/api/pay/yeepayWebCallback1'
		),
		'logName' => 'YeePay_HTML.log',
		'p0_Cmd' => 'Buy',
		'p9_SAF' => '0',
	);
	//连连支付  网页端
	$config['llpayWeb'] = array(
			'oid_partner' => '201605111000852692',
			'version' => '1.0',
			'sign_type' => 'MD5',
			'key' => 'swssfsgffdk670934',
			//'RSA_PRIVATE_KEY' => '',
			'id_type' => '0',
			'valid_order' => '10080',
			'notify_url' => 'https://www.166cai.com/api/pay/llpayWebAsync', //异步回调地址
			'url_return' => 'https://www.166cai.com/api/pay/llpayWebSync', //同步回调地址
			'input_charset' => 'utf-8',
			'transport' => 'http',
			'cardBinUrl' => 'https://yintong.com.cn/queryapi/bankcardbin.htm', //银行卡bin查询地址
			'cardListUrl' => 'https://yintong.com.cn/queryapi/bankcardbindlist.htm', //查询用户签约银行列表地址
			'cardUnbindUrl' => 'https://yintong.com.cn/traderapi/bankcardunbind.htm', //解绑用户签约银行地址
	);
	
	//中信支付  微信
	$config['zxpayWeb'] = array(
			'merId' => '886600000001307',
			'key' => '85820913713856386068566955027260',
			'encoding' => 'UTF-8',
			'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
			'backEndUrl' => 'https://www.166cai.com/api/pay/zxpayWebAsync', //异步回调地址
			'currencyType' => '156', //156：人民币
			'payGateway' => 'https://120.55.176.124:8090/MPay/backTransAction.do', //支付网关地址
	);

	// 统统付 wap
	$config['sumpayWap'] = array(
		'payUrl' => 'https://wapcashier.sumpay.cn/service/rest.htm',
		'mer_id' => '100013650',
		'app_id' => '100013650',
		'sign_type' => 'RSA',	// 签名方式
		'goods_num' => '1',		// 商品数量
		'goods_type' => '2',	// 商品类型
		'logistics' => '0',		// 是否物流
		'notify_url' => 'https://www.166cai.com/api/pay/sumpayWapAsync',		// 后台异步回调地址
		'return_url' => 'http://www.166cai.com/api/pay/sumpayWapSync',		// 前台同步回调地址
		'terminal_type' => 'wap',	// 终端类型
		'version' => '1.0',		// API版本号
		'service' => 'sumpay.wap.trade.order.apply',	//接口名称
		'trade_code' => 'T0002',
		'apiUrl' => 'https://open.sumpay.cn/api.htm',
	);

	// 统统付 网页网关
	$config['sumpayWeb'] = array(
		'payUrl' => 'https://pc.sumpay.cn/cashier/service/rest.htm',
		'mer_id' => '100013650',
		'app_id' => '100013650',
		'sign_type' => 'RSA',	// 签名方式
		'goods_num' => '1',		// 商品数量
		'goods_type' => '2',	// 商品类型
		'logistics' => '0',		// 是否物流
		'notify_url' => 'https://www.166cai.com/api/pay/sumpayWapAsync',		// 后台异步回调地址
		'return_url' => 'https://www.166cai.com/api/pay/sumpayWebSync',		// 前台同步回调地址
		'terminal_type' => 'web',
		'version' => '1.0',
		'service' => 'sumpay.web.trade.order.apply',
		'trade_code' => 'T0002',
		'apiUrl' => 'https://open.sumpay.cn/api.htm',
	);

	//中信支付  微信SDK
	$config['zxwxSdk'] = array(
		'merId' => '886600000001432',
		'key' => '86442638095766090284169800806464',
		'encoding' => 'UTF-8',
		'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
		'backEndUrl' => 'https://www.166cai.com/api/pay/zxpaySdkAsync', //异步回调地址
		'currencyType' => '156', //156：人民币
		'payGateway' => 'https://120.55.176.124:8092/MPay/backTransAction.do', //支付网关地址
	);
	
	//威富通  支付宝pc扫码支付
	$config['wftZfb'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '100530025282',
		'key' => '2be82a2f528712815857abb9b8e4e5f6',
		'service' => 'pay.alipay.native',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://www.166cai.com/api/pay/wftZfbCallback',
	);
	
	//威富通 微信SDK
	// 'mch_id' => '100530025282',
	// 'key' => '2be82a2f528712815857abb9b8e4e5f6',
	//'mch_id' => '102510424356',
	//'key' => '82b808dba58a782c0b2b5eda41fcf8ef',
	$config['wftWxSdk'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '101590046608',
		'key' => 'bff7c1931379f18a92ac8982b3b2b90b',
		'service' => 'pay.weixin.raw.app',
		'appid' => 'wx1315f7bd05e62fed',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://www.166cai.com/api/pay/wftWxSdkCallback',
	);
	
	//威富通 微信PC/APP扫码
	$config['wftWx'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '102570424364',
		'key' => '861f99ff081a4a70e39f49191532c735',
		'service' => 'pay.weixin.native',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://www.166cai.com/api/pay/wftWxCallback',
	);

	//现在 支付宝h5支付
	$config['xzZfbWap'] = array(
		'appId' => '1491532560057310',	// 应用ID
		'secure_key' => 'vc1L3A8ze7dQOK9LN7JCwDeompRqw0aQ', // 商户秘钥
		'timezone' => 'Shanghai',
		'front_notify_url' => 'https://www.166cai.com/api/pay/xzZfbWapFront',	// 前台回调
		'back_notify_url' => 'https://www.166cai.com/api/pay/xzZfbWapBack',		// 后台回调
	);
}
else
{
	// 易宝支付 移动端
	$config['yeepay'] = array(
		'merchantaccount' => '10000418926',
		'merchantPrivateKey' => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBALD0Tou2w7EHbP3q5wi5PG5xrvC0CBawXxSI1PlZAGo2iFYhaBK6SsB5UiYT64fSR3YemQGS2vSqQii5vYdOfrffvvDprrr7Vo7BziS6sJQ9B0/DzwN2zY7jJBCz55CLMBsZCtuqDNVxTcsOcZnrgSSMqnhk+usuR4hPoV9qABeHAgMBAAECgYAfnth2UOdxN/F7AkHcpjUtSzVGn/UeENA8vCLKl+PiFvKP6ZJOXmnDMSrD0SVydNn+OoN+634i4FXIL0C18Anmh4IlQM9hj+rFTg1bMSUHvSPKoZpoEfjR0R+3TQF8PycBbaIWgLV/5NA8dMld0DvF5d8bbqpgH6FzEXZPvF8OgQJBANwHRhCu+o/JoCoH0coVhNFuobVYZU0pQRlfDaE4ph0+daiJ4HlT630JrBFb728Ga7E81dsfGMSi1N6QSipJMEECQQDN4kb+O/ecDNQrEsjA0LqDXkaKsRP6iU/HVNyr4Z/7ojHws0F5Vypj1euCII+V6U7StMKRbSaB1GI8Bs34llXHAkEAnIc0KiRBLk+S+LOtZGVgoplgwyEKmBUUMdd0W9BwJHfNvkOwBMBV1BMwbP0JXeOkc2dDAGqj9Sed5mOhz2lXwQJAVeA0TIcm2Ohg9zZ2ljZ6FaGVOvRxqObtZ+91vBv4ZzVYL1YV0U8SV2I7QaPjQFx4jFrpbU9h6HV2JCOSdkX+sQJBAJ+PfNA0b25HuY9n4cTk/hLc2TCWVDsPnONuhNpuRpXqxu9L0p2aHX5JLf1kTUoYxqmlEjx6IYcObcB9Snw0Tf0=',
		'merchantPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCw9E6LtsOxB2z96ucIuTxuca7wtAgWsF8UiNT5WQBqNohWIWgSukrAeVImE+uH0kd2HpkBktr0qkIoub2HTn63377w6a66+1aOwc4kurCUPQdPw88Dds2O4yQQs+eQizAbGQrbqgzVcU3LDnGZ64EkjKp4ZPrrLkeIT6FfagAXhwIDAQAB',
		'yeepayPublicKey' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCKcSa7wS6OMUL4oTzJLCBsE5KTkPz9OTSiOU6356BsR6gzQ9kf/xa+Wi1ZANTeNuTYFyhlCI7ZCLW7QNzwAYSFStKzP3UlUzsfrV7zge8gTgJSwC/avsZPCWMDrniC3HiZ70l1mMBK5pL0H6NbBFJ6XgDIw160aO9AxFZa5pfCcwIDAQAB',
		'callbackurl' => 'https://123.59.105.39/api/pay/yeepayMPayCallback',  //后台回调地址
		'fcallbackurl' => 'http://123.59.105.39/api/pay/yeepayMPayReturnUrl', //前台回调地址
	);
	//易宝支付 网页端
	$config['yeepayWeb'] = array(
		'weixin' => array(
			'p1_MerId' => '10000457067',
			'merchantKey' => 'U26po59182dV8d7654bo24o5z369408u4sQ3To9j6QuopAbo3gwj4h33mro4',
			'reqURL_onLine' => 'https://www.yeepay.com/app-merchant-proxy/node',
			'callback' => 'https://123.59.105.39/api/pay/yeepayWebCallback'
		),
		'other' => array(
			'p1_MerId' => '10000457067',
			'merchantKey' => 'U26po59182dV8d7654bo24o5z369408u4sQ3To9j6QuopAbo3gwj4h33mro4',
			'reqURL_onLine' => 'https://www.yeepay.com/app-merchant-proxy/node',
			'callback' => 'https://123.59.105.39/api/pay/yeepayWebCallback1'
		),
		'logName' => 'YeePay_HTML.log',
		'p0_Cmd' => 'Buy',
		'p9_SAF' => '0',
	);
	
	//连连支付  网页端
	$config['llpayWeb'] = array(
		'oid_partner' => '201605111000852692',
		'version' => '1.0',
		'sign_type' => 'MD5',
		'key' => 'swssfsgffdk670934',
		//'RSA_PRIVATE_KEY' => '',
		'id_type' => '0',
		'valid_order' => '10080',
		'notify_url' => 'https://123.59.105.39/api/pay/llpayWebAsync', //异步回调地址
		'url_return' => 'https://123.59.105.39/api/pay/llpayWebSync', //同步回调地址
		'input_charset' => 'utf-8',
		'transport' => 'http',
		'cardBinUrl' => 'https://yintong.com.cn/queryapi/bankcardbin.htm', //银行卡bin查询地址
		'cardListUrl' => 'https://yintong.com.cn/queryapi/bankcardbindlist.htm', //查询用户签约银行列表地址
		'cardUnbindUrl' => 'https://yintong.com.cn/traderapi/bankcardunbind.htm', //解绑用户签约银行地址
	);
	
	//中信支付  微信
	$config['zxpayWeb'] = array(
		'merId' => '886600000000073',
		'key' => '87181822400416731730948330827680',
		'encoding' => 'UTF-8',
		'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
		'backEndUrl' => 'https://123.59.105.39/api/pay/zxpayWebAsync', //异步回调地址
		'currencyType' => '156', //156：人民币
		'payGateway' => 'https://120.27.165.177:8099/MPay/backTransAction.do', //支付网关地址
	);

	// 统统付 wap
	$config['sumpayWap'] = array(
		'payUrl' => 'http://101.71.243.74:8891/wapcashier/service/rest.htm',
		'mer_id' => 's100000040',
		'app_id' => 's100000040',
		'sign_type' => 'RSA',	// 签名方式
		'goods_num' => '1',		// 商品数量
		'goods_type' => '2',	// 商品类型
		'logistics' => '0',		// 是否物流
		'notify_url' => 'https://123.59.105.39/api/pay/sumpayWapAsync',		// 后台异步回调地址
		'return_url' => 'http://123.59.105.39/api/pay/sumpayWapSync',		// 前台同步回调地址
		'terminal_type' => 'wap',	// 终端类型
		'version' => '1.0',		// API版本号
		'service' => 'sumpay.wap.trade.order.apply',	//接口名称
		'trade_code' => 'T0002',
		'apiUrl' => 'http://101.71.243.74:8891/sumpayapi/api.htm',
	);

	// 统统付 网页网关
	$config['sumpayWeb'] = array(
		'payUrl' => 'http://101.71.243.74:8891/cashier/service/rest.htm',
		'mer_id' => 's100000040',
		'app_id' => 's100000040',
		'sign_type' => 'RSA',	// 签名方式
		'goods_num' => '1',		// 商品数量
		'goods_type' => '2',	// 商品类型
		'logistics' => '0',		// 是否物流
		'notify_url' => 'https://123.59.105.39/api/pay/sumpayWapAsync',		// 后台异步回调地址
		'return_url' => 'https://123.59.105.39/api/pay/sumpayWebSync',		// 前台同步回调地址
		'terminal_type' => 'web',
		'version' => '1.0',
		'service' => 'sumpay.web.trade.order.apply',
		'trade_code' => 'T0002',
		'apiUrl' => 'http://101.71.243.74:8891/sumpayapi/api.htm',
	);

	//中信支付  微信SDK
	$config['zxwxSdk'] = array(
		'merId' => '996600000000115',
		'key' => '86207547067182003011355479326019',
		'encoding' => 'UTF-8',
		'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
		'backEndUrl' => 'https://123.59.105.39/api/pay/zxpaySdkAsync', //异步回调地址
		'currencyType' => '156', //156：人民币
		'payGateway' => 'https://120.27.165.177:8099/MPay/backTransAction.do', //支付网关地址
	);
	
	//威富通  支付宝pc扫码支付
	$config['wftZfb'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '100530025282',
		'key' => '2be82a2f528712815857abb9b8e4e5f6',
		'service' => 'pay.alipay.native',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://123.59.105.39/api/pay/wftZfbCallback',
	);
	
	//威富通 微信SDK
	$config['wftWxSdk'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '100530025282',
		'key' => '2be82a2f528712815857abb9b8e4e5f6',
		'service' => 'pay.weixin.raw.app',
		'appid' => 'wx1315f7bd05e62fed',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://123.59.105.39/api/pay/wftWxSdkCallback',
	);
	
	//威富通 微信PC/APP扫码
	$config['wftWx'] = array(
		'url' => 'https://pay.swiftpass.cn/pay/gateway',
		'mch_id' => '100530025282',
		'key' => '2be82a2f528712815857abb9b8e4e5f6',
		'service' => 'pay.weixin.native',
		'version' => ' 2.0',
		'charset' => 'UTF-8',
		'sign_type' => 'MD5',
		'notify_url' => 'https://123.59.105.39/api/pay/wftWxCallback',
	);
	
	//现在 支付宝h5支付
	$config['xzZfbWap'] = array(
		'appId' => '1491532560057310',	// 应用ID
		'secure_key' => 'vc1L3A8ze7dQOK9LN7JCwDeompRqw0aQ', // 商户秘钥
		'timezone' => 'Shanghai',
		'front_notify_url' => 'https://123.59.105.39/api/pay/xzZfbWapFront',	// 前台回调
		'back_notify_url' => 'https://123.59.105.39/api/pay/xzZfbWapBack',	// 后台回调
	);
}

