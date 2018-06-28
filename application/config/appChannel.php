<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (ENVIRONMENT === 'production')
{
	$config['zxwxSdk'] = array(
		'10091' => array(
			'merId' => '886600000002569',
			'key' => '83770700749054633541380048485998',
			'encoding' => 'UTF-8',
			'signMethod' => '02', //00：默认不加密；01：数字证书；02：MD5；03：RSA
			'backEndUrl' => 'https://www.166cai.com/api/pay/zxpaySdkAsyncByChannel/', //异步回调地址
			'currencyType' => '156', //156：人民币
			'payGateway' => 'https://120.55.176.124:8092/MPay/backTransAction.do', //支付网关地址
		)
	);
}
else
{
    $config['zxwxSdk'] = array(
    	'10079' => array(
    		'merId' => '886600000002569',
			'key' => '83770700749054633541380048485998',
			'encoding' => 'UTF-8',
			'signMethod' => '02',
			'backEndUrl' => 'https://123.59.105.39/api/pay/zxpaySdkAsyncByChannel/',
			'currencyType' => '156',
			'payGateway' => 'https://120.55.176.124:8092/MPay/backTransAction.do',
    	)
    );
}
