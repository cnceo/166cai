<?php if (!defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

// 下载地址
$config['url'] = 'https://itunes.apple.com/us/app/166cai-piao-shuang-se-qiu/id1108268497?l=zh&ls=1&mt=8';

// 版本信息
if (ENVIRONMENT === 'production')
{
	$config['versionData'] = array(
		0 => array(
			'appVersionName' => 'v1.0.0',
			'appVersionCode' => '1',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			),
		1 => array(
			'appVersionName' => 'v1.1.0',
			'appVersionCode' => '3',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			),
		2 => array(
			'appVersionName' => 'v1.1.1',
			'appVersionCode' => '4',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			),
		3 => array(
			'appVersionName' => 'v1.2.0',
			'appVersionCode' => '7',
			'needUpgrade' => '0',
			'versionInfo' => '1.新增福彩3D、排列三、排列五；2.新增追号，一期投注，多期自动预约；3.新增比分直播，和现场共心跳。',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'       //是否审核版本
			)
	);
}
else
{
	$config['versionData'] = array(
		0 => array(
			'appVersionName' => 'v1.0.0',
			'appVersionCode' => '1',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',
			'showRedpack' => '1',
			'isCheck' => '0'
			),
		1 => array(
			'appVersionName' => 'v1.1.0',
			'appVersionCode' => '3',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			),
		2 => array(
			'appVersionName' => 'v1.1.1',
			'appVersionCode' => '4',
			'needUpgrade' => '0',
			'versionInfo' => '新版本升级即可购彩！',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			),
		3 => array(
			'appVersionName' => 'v1.2.0',
			'appVersionCode' => '7',
			'needUpgrade' => '0',
			'versionInfo' => '1.新增福彩3D、排列三、排列五；2.新增追号，一期投注，多期自动预约；3.新增比分直播，和现场共心跳。',
			'showAlert' => '0',		// 首页弹窗
			'showRedpack' => '1',	// 红包展示
			'isCheck' => '0'
			)
	);
}
