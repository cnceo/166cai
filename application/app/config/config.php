<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config['app_path'] = BASEPATH . APPPATH;
/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/

if (ENVIRONMENT === 'production')
{
	$config['php_path'] = '/opt/app/php5/bin/php';
	$config['cmd_path'] = '/opt/case/www.166cai.com/app/index.php';
	$config['log_threshold'] = 1; //日志配置
	
    $config['busi_api'] = 'http://www.51caixiang.com/';
    $config['order_api'] = 'http://www.51caixiang.com/2345/ticket/v1/';

    $config['rcg_salt'] = 'https://pay.2345.com/api/refreshToken.php'; //获取支付salt接口
    $config['rcg_sub'] = 'https://pay.2345.com/doPay.php'; //支付冲值接口
    $config['rcg_fail'] = 'https://pay.2345.com/api/doErrorOrder.php'; //充值失败记录接口
    $config['pay_url'] = 'http://pay.2345.com'; //支付中心调用地址

    $config['log_threshold'] = 1; //日志配置
    $config['SECRET'] = '123456789123456789000000';
    $config['cp_host'] = 'http://120.132.33.194/';
    $config['defaultChannel'] = array(
        'web' => 10001,
        'app' => 10002,
    	'ios' => 10003,
    );
}
elseif (ENVIRONMENT === 'checkout')
{
	$config['php_path'] = '/opt/app/php5/bin/php';
	$config['cmd_path'] = '/opt/case/www.166cai.com/app/index.php';
	$config['log_threshold'] = 5; //日志配置
	
    $config['busi_api'] = 'http://www.51caixiangtest.com/';
    $config['order_api'] = 'http://www.51caixiangtest.com/2345/ticket/v1/';

    $config['rcg_salt'] = 'https://183.136.203.137/api/refreshToken.php'; //获取支付salt接口
    $config['rcg_sub'] = 'https://183.136.203.137/doPay.php'; //支付冲值接口
    $config['rcg_fail'] = 'https://183.136.203.137/api/doErrorOrder.php'; //充值失败记录接口
    $config['pay_url'] = 'http://183.136.203.137'; //支付中心调用地址

    $config['log_threshold'] = 1; //日志配置
    $config['SECRET'] = '123456789123456789123456';
    $config['cp_host'] = 'http://123.59.105.39/';
    $config['defaultChannel'] = array(
        'web' => 1,
        'app' => 2,
    	'ios' => 3
    );
}
else
{
	$config['php_path'] = 'D:\wamp\php\php.exe';
	$config['cmd_path'] = 'D:\wamp\web\cpbranch\app\index.php';
	$config['log_threshold'] = 5; //日志配置
	
    $config['busi_api'] = 'http://www.51caixiangtest.com/';
    $config['order_api'] = 'http://www.51caixiangtest.com/2345/ticket/v1/';

    $config['rcg_salt'] = 'https://183.136.203.137/api/refreshToken.php'; //获取支付salt接口
    $config['rcg_sub'] = 'https://183.136.203.137/doPay.php'; //支付冲值接口
    $config['rcg_fail'] = 'https://183.136.203.137/api/doErrorOrder.php'; //充值失败记录接口
    $config['pay_url'] = 'http://183.136.203.137'; //支付中心调用地址
    
    $config['log_threshold'] = 5; //日志配置
    $config['SECRET'] = '123456789123456789123456';
    $config['cp_host'] = 'http://172.15.12.126/';
    $config['defaultChannel'] = array(
        'web' => 1,
        'app' => 2,
    	'ios' => 3
    );
}

if(empty($config['merchantaccount']))
{
	/*
     * merchantaccount    商户编号
     * merchantPrivateKey 商户私钥
     * merchantPublicKey  商户公钥
     * yeepayPublicKey    易宝公钥
     * */
	$config['merchantaccount']    = '10000418926';
	$config['merchantPrivateKey'] = 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBALD0Tou2w7EHbP3q5wi5PG5xrvC0CBawXxSI1PlZAGo2iFYhaBK6SsB5UiYT64fSR3YemQGS2vSqQii5vYdOfrffvvDprrr7Vo7BziS6sJQ9B0/DzwN2zY7jJBCz55CLMBsZCtuqDNVxTcsOcZnrgSSMqnhk+usuR4hPoV9qABeHAgMBAAECgYAfnth2UOdxN/F7AkHcpjUtSzVGn/UeENA8vCLKl+PiFvKP6ZJOXmnDMSrD0SVydNn+OoN+634i4FXIL0C18Anmh4IlQM9hj+rFTg1bMSUHvSPKoZpoEfjR0R+3TQF8PycBbaIWgLV/5NA8dMld0DvF5d8bbqpgH6FzEXZPvF8OgQJBANwHRhCu+o/JoCoH0coVhNFuobVYZU0pQRlfDaE4ph0+daiJ4HlT630JrBFb728Ga7E81dsfGMSi1N6QSipJMEECQQDN4kb+O/ecDNQrEsjA0LqDXkaKsRP6iU/HVNyr4Z/7ojHws0F5Vypj1euCII+V6U7StMKRbSaB1GI8Bs34llXHAkEAnIc0KiRBLk+S+LOtZGVgoplgwyEKmBUUMdd0W9BwJHfNvkOwBMBV1BMwbP0JXeOkc2dDAGqj9Sed5mOhz2lXwQJAVeA0TIcm2Ohg9zZ2ljZ6FaGVOvRxqObtZ+91vBv4ZzVYL1YV0U8SV2I7QaPjQFx4jFrpbU9h6HV2JCOSdkX+sQJBAJ+PfNA0b25HuY9n4cTk/hLc2TCWVDsPnONuhNpuRpXqxu9L0p2aHX5JLf1kTUoYxqmlEjx6IYcObcB9Snw0Tf0=';
	$config['merchantPublicKey']  = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCw9E6LtsOxB2z96ucIuTxuca7wtAgWsF8UiNT5WQBqNohWIWgSukrAeVImE+uH0kd2HpkBktr0qkIoub2HTn63377w6a66+1aOwc4kurCUPQdPw88Dds2O4yQQs+eQizAbGQrbqgzVcU3LDnGZ64EkjKp4ZPrrLkeIT6FfagAXhwIDAQAB';
	$config['yeepayPublicKey']    = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCKcSa7wS6OMUL4oTzJLCBsE5KTkPz9OTSiOU6356BsR6gzQ9kf/xa+Wi1ZANTeNuTYFyhlCI7ZCLW7QNzwAYSFStKzP3UlUzsfrV7zge8gTgJSwC/avsZPCWMDrniC3HiZ70l1mMBK5pL0H6NbBFJ6XgDIw160aO9AxFZa5pfCcwIDAQAB';
}

$config['base_url'] = '//www.ka5188.com/app/';
$config['pages_url'] = '//www.ka5188.com/';
$config['domain']   = 'www.ka5188.com';
$config['download_url'] = 'http://download.166cai.cn/';
//图片静态资源域名
$config['img_url'] = array(
    '//img1.166cai.cn/',
    '//img2.166cai.cn/',
    '//img3.166cai.cn/',
    '//img4.166cai.cn/',
    '//img5.166cai.cn/',
    '//img6.166cai.cn/'
);
// 请求头
$config['protocol'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

$config['phone_voice'] = 'http://voice.2345.cn'; //语音接口
$config['sms_url'] = 'http://duanxin.km.com/Api/Sms/Send'; //短信接口
$config['qq_appid'] = '1104545241'; //qq账号登陆appid
/*
 * 彩种销售及充值开关 1：开启 0：关闭
* */
$config['is_sale'] = 1;
$config['is_recharge'] = 1;

/*
 * APP平台标识 0: 网站 
* */
$config['platform'] = 1;

$config['defaultName'] = '设一个昵称，中一份大奖';

/*
 * APP 安卓 hashCode 验证
* */
$config['hashCode'] = '-1196329524';

/*
 * 根目录定义
* */
$config['base_path'] = dirname(BASEPATH);
/**
 * redis keys
 */
$config['REDIS'] = array(
	'UNIQUE_KEY' => '_unique_key:',
	'ORDERS_CHECK_START_TIME' => '_orders_check_start_time:',
	'USER_INFO' => '_user_info:',
	'BANK_INFO' => '_bank_info:',
	'CX_DATA' => '_cx_data:',
	'CX_API' => '_cx_api:',
	'CX_API_PARAMS' => '_cx_params:',
	'CX_API_SETS' => '_cx_api_sets:',
	'CACHE_TYPE' => array(
			'ticket_data_pl0' => '_cache_key0:6:1',//key:timeout:switch
	),
	'NOTICE_RECORDS' => '_notice_records:',
	'TOTAL_WIN' => '_total_win:',
	'LOTTERY_INFO' => '_lottery_info:',
	'ADD_INFO' => '_add_info:',
	'JCZQ_MATCH' => '_jczq_match:',
	'JCLQ_MATCH' => '_jclq_match:',
    'JCZQ_HISTORY' => '_jczq_history:',
	'JCLQ_HISTORY' => '_jclq_history:',
    'SFC_HISTORY' => '_sfc_history:',
	'SSQ_ISSUE' => '_ssq_issue:',
	'DLT_ISSUE' => '_dlt_issue:',
	'FC3D_ISSUE' => '_fc3d_issue:',
	'PLS_ISSUE'  => '_pls_issue:',
	'PLW_ISSUE'  => '_plw_issue:',
	'QXC_ISSUE'  => '_qxc_issue:',
	'QLC_ISSUE'  => '_qlc_issue:',
	'SFC_MATCH'  => '_sfc_match:',
	'SFC_ISSUE'  => '_sfc_issue:',
	'RJ_ISSUE'   => '_rj_issue:',
    'SFC_MATCH_NEW'               => '_sfc_match_new:',
    'SFC_ISSUE_NEW'               => '_sfc_issue_new:',
    'RJ_ISSUE_NEW'               => '_rj_issue_new:',
	'APP_VERSION' => '_app_version:',
	'validChannels' => '_validChannels',
    'SSQ_MISS'	=> '_ssq_miss',
    'DLT_MISS'	=> '_dlt_miss',
    'SYXW_MISS'	=> '_syxw_miss',
    'JXSYXW_MISS' => '_jxsyxw_miss',
    'HBSYXW_MISS' => '_hbsyxw_miss',
    'GDSYXW_MISS' => '_gdsyxw_miss',
    'KS_MISS'   => '_ks_miss',
    'JLKS_MISS'   => '_jlks_miss',
    'JXKS_MISS'   => '_jxks_miss',
    'KLPK_MISS'   => '_klpk_miss',
    'CQSSC_MISS'  => '_cqssc_miss',
    'QLC_MISS'  => '_qlc_miss',
    'QXC_MISS'  => '_qxc_miss',
    'PL3_MISS'  => '_pl3_miss',
    'PL5_MISS'  => '_pl5_miss',
    'FC3D_MISS' => '_fc3d_miss',
    'SSQ_MISS_MORE'   => '_ssq_miss_more',
    'DLT_MISS_MORE'   => '_dlt_miss_more',
    'SYXW_MISS_MORE'  => '_syxw_miss_more',
    'JXSYXW_MISS_MORE'  => '_jxsyxw_miss_more',
    'HBSYXW_MISS_MORE'  => '_hbsyxw_miss_more',
    'GDSYXW_MISS_MORE'  => '_gdsyxw_miss_more',
    'KS_MISS_MORE'    => '_ks_miss_more',
    'JLKS_MISS_MORE'    => '_jlks_miss_more',
    'JXKS_MISS_MORE'    => '_jxks_miss_more',
    'KLPK_MISS_MORE'    => '_klpk_miss_more',
    'CQSSC_MISS_MORE' => '_cqssc_miss_more',
    'QLC_MISS_MORE'   => '_qlc_miss_more',
    'QXC_MISS_MORE'   => '_qxc_miss_more',
    'PL3_MISS_MORE'   => '_pl3_miss_more',
    'PL5_MISS_MORE'   => '_pl5_miss_more',
    'FC3D_MISS_MORE'  => '_fc3d_miss_more',
    'SYXW_AWARD' => '_syxw_award:',
    'JXSYXW_AWARD'    => '_jxsyxw_award:',
    'GDSYXW_AWARD'    => '_gdsyxw_award:',
    'KS_AWARD'        => '_ks_award:',
    'JLKS_AWARD'        => '_jlks_award:',//易快3
    'JXKS_AWARD'        => '_jxks_award:',
    'KLPK_AWARD'        => '_klpk_award:',
    'LOTTERY_CONFIG'  => '_lottery_config:',
    'JCZQ_AWARD' => '_jczq_award:',
    'JCLQ_AWARD' => '_jclq_award:',
    'JCZQ_AWARD_LAST' => '_jczq_award_last:',
    'JCLQ_AWARD_LAST' => '_jclq_award_last:',
    'SYXW_ISSUE_TZ' => '_syxw_issue_tz:',
    'JXSYXW_ISSUE_TZ' => '_jxsyxw_issue_tz:',
    'HBSYXW_ISSUE_TZ'         => '_hbsyxw_issue_tz:',
    'GDSYXW_ISSUE_TZ'         => '_gdsyxw_issue_tz:',
    'KS_ISSUE_TZ'         	  => '_ks_issue_tz:',
    'JLKS_ISSUE_TZ'             => '_jlks_issue_tz:',//易快3
    'JXKS_ISSUE_TZ'             => '_jxks_issue_tz:',
    'KLPK_ISSUE_TZ'         	  => '_klpk_issue_tz:',
    'CQSSC_ISSUE_TZ'          => '_cqssc_issue_tz:',
    'ISSUE_COMING' => '_issue_coming:',  // 追号投注期次
    'JCZQ_EUROPE_ODDS'        => '_jczq_europe_odds:',
    'JCLQ_EUROPE_ODDS'        => '_jclq_europe_odds:',
    'SFC_EUROPE_ODDS'         => '_sfc_europe_odds:',
    'APP_CONFIG'              => '_app_config:',
    'AWARD_NOTICE'            => '_award_notice:',
    'ACTIVITY_LX'             => '_activity_lx:',
    'CHECK_LIMIT'             => '_check_limit:',
    'USER_HOT'                => '_isHot:',
    'RCG_DISPATCH'            => '_rcg_dispatch:',
    'PAY_CONFIG'              => '_pay_config:',
    'CS_PAY_CONFIG'	      => '_cs_pay_config:',         //199,194测试充值渠道分配缓存
    'CS_RCG_DISPATCH'            => '_cs_rcg_dispatch:',      //199,194测试充值渠道分配缓存
    'PAY_RATE_CONFIG'         => '_pay_rate_config:',   // 支付宝微信比例
    'PAY_BANK_INFO'	      => '_pay_bank_info:',
    'JCZQ_BET_COUNT'          => 'jczq_bet_count:',     //竞彩投注比例
    'JCLQ_BET_COUNT'          => 'jclq_bet_count:',     
    'SFC_BET_COUNT'           => 'sfc_bet_count:',
    'APP_INDEX'               => '_app_index:',
    'APP_INDEX_NEW'           => '_app_index_new:',
    'LIMIT_CHANNEL'           => '_limit_channels:',
    'JCJJ_HOVER'              => 'jcjj_hover:',
    'WC_RP_MONEY'             => '_wc_rp_money:',
);

$config['OUTTIME'] = array('captche' => 30, 'cx_data' => 5);
$config['MESSAGE'] = array(
	'captche' => "验证码：#CODE#，请及时正确输入，切勿将验证码告知他人！",
	'tick_fail' => '您的帐号#UNAME#于#MM#月#DD#日#HH#：#II#投注的#LID#出票失败，支付金额已返还您的帐户。',
	'lottery_fail' => '您的帐号#UNAME#于#MM#月#DD#日 #HH#：#II#投注的#LID#投注失败，支付金额已返还您的帐户。',
	'withdraw_succ' => '您的帐号#UNAME#于#MM#月#DD#日#HH#：#II#提交的提款申请已处理完成，提款金额#MONEY#元，请注意查收。',
	'withdraw_fail' => '您的帐号#UNAME#于#MM#月#DD#日#HH#：#II#提交的提款申请处理失败，请登录网站查看详情。',
	'win_prize' => '恭喜您！您的帐号#UNAME#于#MM#月#DD#日#HH#时#II#分投注的#LID#中奖#MONEY#元，已派奖到您的帐户。',
    'hongbao_dkw' => '您已成功领取188元红包，马上下载APP使用红包： t.cn/RMJB2vu ，小六祝您购彩愉快！',
	'hongbao_newUser' => '您已成功领取188元红包，马上下载APP使用红包： t.cn/RijdCrn ，小六祝您购彩愉快！',
);
// 短信验证码类型
$config['SMSTYPE'] = array(
    // APP修改手机号码
    'modifyPhone' => array(
        'smsType' => 9,
        'positionId' => 189
    ),
    // APP修改登录密码
    'modifyPword' => array(
        'smsType' => 9,
        'positionId' => 189
    ),
    // APP忘记登录密码
    'forgetPword' => array(
        'smsType' => 9,
        'positionId' => 189
    ),
    // APP提现 
    'withdraw' => array(
        'smsType' => 9,
        'positionId' => 191
    ),
    // APP完善信息(删除) 
    'updateUinfo' => array(
        'smsType' => 9,
        'positionId' => 189
    ),
    // APP注册
    'register' => array(
        'smsType' => 9,
        'positionId' => 251
    ),
    // APP登陆
    'login' => array(
        'smsType' => 9,
        'positionId' => 188
    ),
    // 送188红包活动页
    '188hb' => array(
        'smsType' => 9,
        'positionId' => 190
    )
);

/**
 * 网站公告类型
 * @Author liusijia
 */
$config['noticeType'] = array(
    "1" => "公告",
    "2" => "博文",
    "3" => "新闻",
    "4" => "活动"
);

/*
|--------------------------------------------------------------------------
| Index File
|--------------------------------------------------------------------------
|
| Typically this will be your index.php file, unless you've renamed it to
| something else. If you are using mod_rewrite to remove the page set this
| variable so that it is blank.
|
*/
$config['index_page'] = 'index.php';

/*
|--------------------------------------------------------------------------
| URI PROTOCOL
|--------------------------------------------------------------------------
|
| This item determines which server global should be used to retrieve the
| URI string.  The default setting of 'AUTO' works for most servers.
| If your links do not seem to work, try one of the other delicious flavors:
|
| 'AUTO'			Default - auto detects
| 'PATH_INFO'		Uses the PATH_INFO
| 'QUERY_STRING'	Uses the QUERY_STRING
| 'REQUEST_URI'		Uses the REQUEST_URI
| 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
|
*/
$config['uri_protocol']	= 'AUTO';

/*
|--------------------------------------------------------------------------
| URL suffix
|--------------------------------------------------------------------------
|
| This option allows you to add a suffix to all URLs generated by CodeIgniter.
| For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/urls.html
*/

$config['url_suffix'] = '';

/*
|--------------------------------------------------------------------------
| Default Language
|--------------------------------------------------------------------------
|
| This determines which set of language files should be used. Make sure
| there is an available translation if you intend to use something other
| than english.
|
*/
$config['language']	= 'english';

/*
|--------------------------------------------------------------------------
| Default Character Set
|--------------------------------------------------------------------------
|
| This determines which character set is used by default in various methods
| that require a character set to be provided.
|
*/
$config['charset'] = 'UTF-8';

/*
|--------------------------------------------------------------------------
| Enable/Disable System Hooks
|--------------------------------------------------------------------------
|
| If you would like to use the 'hooks' feature you must enable it by
| setting this variable to TRUE (boolean).  See the user guide for details.
|
*/
$config['enable_hooks'] = FALSE;


/*
|--------------------------------------------------------------------------
| Class Extension Prefix
|--------------------------------------------------------------------------
|
| This item allows you to set the filename/classname prefix when extending
| native libraries.  For more information please see the user guide:
|
| http://codeigniter.com/user_guide/general/core_classes.html
| http://codeigniter.com/user_guide/general/creating_libraries.html
|
*/
$config['subclass_prefix'] = 'MY_';


/*
|--------------------------------------------------------------------------
| Allowed URL Characters
|--------------------------------------------------------------------------
|
| This lets you specify with a regular expression which characters are permitted
| within your URLs.  When someone tries to submit a URL with disallowed
| characters they will get a warning message.
|
| As a security measure you are STRONGLY encouraged to restrict URLs to
| as few characters as possible.  By default only these are allowed: a-z 0-9~%.:_-
|
| Leave blank to allow all characters -- but only if you are insane.
|
| DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
|
*/
$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';


/*
|--------------------------------------------------------------------------
| Enable Query Strings
|--------------------------------------------------------------------------
|
| By default CodeIgniter uses search-engine friendly segment based URLs:
| example.com/who/what/where/
|
| By default CodeIgniter enables access to the $_GET array.  If for some
| reason you would like to disable it, set 'allow_get_array' to FALSE.
|
| You can optionally enable standard query string based URLs:
| example.com?who=me&what=something&where=here
|
| Options are: TRUE or FALSE (boolean)
|
| The other items let you set the query string 'words' that will
| invoke your controllers and its functions:
| example.com/index.php?c=controller&m=function
|
| Please note that some of the helpers won't work as expected when
| this feature is enabled, since CodeIgniter is designed primarily to
| use segment based URLs.
|
*/
$config['allow_get_array']		= TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger']	= 'c';
$config['function_trigger']		= 'm';
$config['directory_trigger']	= 'd'; // experimental not currently in use

/*
|--------------------------------------------------------------------------
| Error Logging Threshold
|--------------------------------------------------------------------------
|
| If you have enabled error logging, you can set an error threshold to
| determine what gets logged. Threshold options are:
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
*/
//$config['log_threshold'] = 0;

/*
|--------------------------------------------------------------------------
| Error Logging Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ folder. Use a full server path with trailing slash.
|
*/
$config['log_path'] = '';

/*
|--------------------------------------------------------------------------
| Date Format for Logs
|--------------------------------------------------------------------------
|
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
*/
$config['log_date_format'] = 'Y-m-d H:i:s';

/*
|--------------------------------------------------------------------------
| Cache Directory Path
|--------------------------------------------------------------------------
|
| Leave this BLANK unless you would like to set something other than the default
| system/cache/ folder.  Use a full server path with trailing slash.
|
*/
$config['cache_path'] = '';

/*
|--------------------------------------------------------------------------
| Encryption Key
|--------------------------------------------------------------------------
|
| If you use the Encryption class or the Session class you
| MUST set an encryption key.  See the user guide for info.
|
*/
$config['encryption_key'] = 'caipiao-2345-com';

/*
|--------------------------------------------------------------------------
| Session Variables
|--------------------------------------------------------------------------
|
| 'sess_cookie_name'		= the name you want for the cookie
| 'sess_expiration'			= the number of SECONDS you want the session to last.
|   by default sessions last 7200 seconds (two hours).  Set to zero for no expiration.
| 'sess_expire_on_close'	= Whether to cause the session to expire automatically
|   when the browser window is closed
| 'sess_encrypt_cookie'		= Whether to encrypt the cookie
| 'sess_use_database'		= Whether to save the session data to a database
| 'sess_table_name'			= The name of the session database table
| 'sess_match_ip'			= Whether to match the user's IP address when reading the session data
| 'sess_match_useragent'	= Whether to match the User Agent when reading the session data
| 'sess_time_to_update'		= how many seconds between CI refreshing Session Information
|
*/
$config['sess_cookie_name']		= 'ci_session';
$config['sess_expiration']		= 7200;
$config['sess_expire_on_close']	= FALSE;
$config['sess_encrypt_cookie']	= FALSE;
$config['sess_use_database']	= FALSE;
$config['sess_table_name']		= 'ci_sessions';
$config['sess_match_ip']		= FALSE;
$config['sess_match_useragent']	= TRUE;
$config['sess_time_to_update']	= 300;

/*
|--------------------------------------------------------------------------
| Cookie Related Variables
|--------------------------------------------------------------------------
|
| 'cookie_prefix' = Set a prefix if you need to avoid collisions
| 'cookie_domain' = Set to .your-domain.com for site-wide cookies
| 'cookie_path'   =  Typically will be a forward slash
| 'cookie_secure' =  Cookies will only be set if a secure HTTPS connection exists.
|
*/
$config['cookie_prefix']	= "";
$config['cookie_domain']	= "";
$config['cookie_path']		= "/";
$config['cookie_secure']	= FALSE;

/*
|--------------------------------------------------------------------------
| Global XSS Filtering
|--------------------------------------------------------------------------
|
| Determines whether the XSS filter is always active when GET, POST or
| COOKIE data is encountered
|
*/
$config['global_xss_filtering'] = FALSE;

/*
|--------------------------------------------------------------------------
| Cross Site Request Forgery
|--------------------------------------------------------------------------
| Enables a CSRF cookie token to be set. When set to TRUE, token will be
| checked on a submitted form. If you are accepting user data, it is strongly
| recommended CSRF protection be enabled.
|
| 'csrf_token_name' = The token name
| 'csrf_cookie_name' = The cookie name
| 'csrf_expire' = The number in seconds the token should expire.
*/
$config['csrf_protection'] = FALSE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;

/*
|--------------------------------------------------------------------------
| Output Compression
|--------------------------------------------------------------------------
|
| Enables Gzip output compression for faster page loads.  When enabled,
| the output class will test whether your server supports Gzip.
| Even if it does, however, not all browsers support compression
| so enable only if you are reasonably sure your visitors can handle it.
|
| VERY IMPORTANT:  If you are getting a blank page when compression is enabled it
| means you are prematurely outputting something to your browser. It could
| even be a line of whitespace at the end of one of your scripts.  For
| compression to work, nothing can be sent before the output buffer is called
| by the output class.  Do not 'echo' any values with compression enabled.
|
*/
$config['compress_output'] = FALSE;

/*
|--------------------------------------------------------------------------
| Master Time Reference
|--------------------------------------------------------------------------
|
| Options are 'local' or 'gmt'.  This pref tells the system whether to use
| your server's local time as the master 'now' reference, or convert it to
| GMT.  See the 'date helper' page of the user guide for information
| regarding date handling.
|
*/
$config['time_reference'] = 'local';


/*
|--------------------------------------------------------------------------
| Rewrite PHP Short Tags
|--------------------------------------------------------------------------
|
| If your PHP installation does not have short tag support enabled CI
| can rewrite the tags on-the-fly, enabling you to utilize that syntax
| in your view files.  Options are TRUE or FALSE (boolean)
|
*/
$config['rewrite_short_tags'] = FALSE;


/*
|--------------------------------------------------------------------------
| Reverse Proxy IPs
|--------------------------------------------------------------------------
|
| If your server is behind a reverse proxy, you must whitelist the proxy IP
| addresses from which CodeIgniter should trust the HTTP_X_FORWARDED_FOR
| header in order to properly identify the visitor's IP address.
| Comma-delimited, e.g. '10.0.1.200,10.0.1.201'
|
*/
$config['proxy_ips'] = '';

$config['encrypt_hash'] = 'M#jM0NeSv#wMDG9+8rVsti80A==3g.0';

//投注站所接彩种
$config['cfg_partner_lid'] = array(
		//'53' => array('ename' => 'ks', 'cname' => '上海快三'),
		'21406' => array('ename' => 'syxw', 'cname' => '老11选5'),
		'21407' => array('ename' => 'jxsyxw', 'cname' => '新11选5'),
        '21408' => array('ename' => 'hbsyxw', 'cname' => '惊喜11选5'),
		'54' => array('ename' => 'klpk', 'cname' => '快乐扑克'),
        '21421' => array('ename' => 'gdsyxw', 'cname' => '乐11选5'),
);

//高频彩种
$config['split_lid'] = array('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421');
//福彩
$config['fc_lid'] = array('51', '23528', '52');
//体彩
$config['tc_lid'] = array('23529', '10022', '33', '35');
// 合买宣言
$config['united_intro'] = '想中大奖的，抓紧跟单啦！';
/* End of file config.php */
/* Location: ./application/config/config.php */
