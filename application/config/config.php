<?php

if ( ! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

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

$config['api_info'] = 'http://info.166cai.cn/';
$config['api_odds'] = 'http://odds.166cai.cn/';
$config['api_bf'] = 'http://bf.166cai.cn/';

if (ENVIRONMENT === 'production')
{
    $config['log_threshold'] = 1; //日志配置
    $config['SECRET'] = '123456789123456789000000';

    $config['php_path'] = '/opt/app/php5/bin/php';
    $config['cmd_path'] = '/opt/case/www.166cai.com/index.php';
    //是否开启多进程
    $config['multi_process'] = array(
        'process_num_limit'         => 2,
        'cli_cfg_syncr_data'        => FALSE,
    	'cli_cfg_syncr_data_orders' => TRUE,
        'clii_cfg_dismantle_order'  => TRUE,
        'cli_cfg_submit_ticket'     => TRUE,
        'cli_cfg_cal_bonus'         => TRUE,
        'cli_cfg_backaward_bonus'   => TRUE,
        'cli_chase_statistics'      => FALSE,
        'cli_chase_bet'             => FALSE,
        'cli_chase_bet_all'         => FALSE,
        'cli_zhisheng'              => FALSE,
    	'clii_cfg_collect_status'   => TRUE
    );
    $config['cp_host'] = '//120.132.33.194/';
    $config['qhtob_pji'] = 'http://svc.caipiaoagent.com/CAIKA166/V2'; //齐汇票机接口
    $config['qhtob_dzh'] = 'http://114.215.190.93/prizefile'; //齐汇对账接口
    $config['qhtob_secret'] = 'AknQasVDkDj4YtacnmiQ0bty';   //齐汇票机秘钥
    $config['qhtob_sellerid'] = 'CAIKA166';   //齐汇票机销售商id
    $config['defaultChannel'] = array(
        'web' => 10001,
        'app' => 10002,
    );
    $config['cdtob_pji'] = 'http://123.57.175.58:8080/ctm/service.go';
    $config['cdtob_secret'] = '88882o3i4u5y6t7r';   //彩豆票机秘钥
    $config['cdtob_sellerid'] = '8888';   //彩豆票机销售商id
    // 善彩
    $config['sctob_pji'] = 'http://150.242.239.195:8047/ChinaLotteryPlatform/lotteryAgent';
    $config['sctob_secret'] = 'AUTOLOTTERY';   //善彩票机秘钥
    $config['sctob_sellerid'] = '29002000143';   //善彩票机销售商id
    //华阳
    $config['hytob_pji'] = 'http://b2bmid.198tc.com/b2blib/lotteryxml.php';
    $config['hytob_sellerid'] = '10001962';   //代理号
    $config['hytob_username'] = '10001962';
    $config['hytob_secret'] = '85d663e42ec525fc1c1cb1dbb7668ed3';   //票机秘钥
    //恒钜
    $config['hjtob_pji'] = 'http://120.26.49.231:9903';
    $config['hjtob_sellerid'] = '4003';   //代理号
    $config['hjtob_username'] = '166cp';   //用户名
    $config['hjtob_password'] = 'Caika#166'; //密码
    $config['hjtob_secret'] = '43623245522136';   //票机签名
    //福牛牛
    $config['fnntob_pji'] = 'http://101.37.91.80:16390/service?wsdl';
    $config['fnntob_sellerid'] = '800152';   //代理号
    $config['fnntob_secret'] = '1F0D854FF2CD';   //票机签名
}
elseif (ENVIRONMENT === 'checkout')
{
    $config['busi_api'] = 'http://www.51caixiangtest.com/';
    $config['order_api'] = 'http://www.51caixiangtest.com/2345/ticket/v1/';

    $config['rcg_salt'] = 'https://183.136.203.137/api/refreshToken.php'; //获取支付salt接口
    $config['rcg_sub'] = 'https://183.136.203.137/doPay.php'; //支付冲值接口
    $config['rcg_fail'] = 'https://183.136.203.137/api/doErrorOrder.php'; //充值失败记录接口

    $config['log_threshold'] = 1; //日志配置
    $config['SECRET'] = '123456789123456789123456';

    $config['php_path'] = '/opt/app/php5/bin/php';
    $config['cmd_path'] = '/opt/case/www.166cai.com/index.php';
    //是否开启多进程
    $config['multi_process'] = array(
        'process_num_limit'         => 2,
        'cli_cfg_syncr_data'        => FALSE,
    	'cli_cfg_syncr_data_orders' => TRUE,
        'clii_cfg_dismantle_order'  => TRUE,
        'cli_cfg_submit_ticket'     => TRUE,
        'cli_cfg_cal_bonus'         => TRUE,
        'cli_cfg_backaward_bonus'   => TRUE,
        'cli_chase_statistics'      => FALSE,
        'cli_chase_bet'             => FALSE,
        'cli_chase_bet_all'         => FALSE,
        'cli_zhisheng'              => FALSE,
    	'clii_cfg_collect_status'   => TRUE
    );
    $config['cp_host'] = '//123.59.105.39/';
    $config['defaultChannel'] = array(
        'web' => 1,
        'app' => 2,
    );
    
}
else
{
    $config['busi_api'] = 'http://www.51caixiangtest.com/';
    $config['order_api'] = 'http://www.51caixiangtest.com/2345/ticket/v1/';

    $config['rcg_salt'] = 'https://183.136.203.137/api/refreshToken.php'; //获取支付salt接口
    $config['rcg_sub'] = 'https://183.136.203.137/doPay.php'; //支付冲值接口
    $config['rcg_fail'] = 'https://183.136.203.137/api/doErrorOrder.php'; //充值失败记录接口

    $config['log_threshold'] = 5; //日志配置
    $config['SECRET'] = '123456789123456789123456';

    $config['php_path'] = '/usr/local/php7/bin/php';
    $config['cmd_path'] = '/mnt/hgfs/web/www.166cai.com/index.php';

    //是否开启多进程
    $config['multi_process'] = array(
        'process_num_limit'         => 1,
        'cli_cfg_syncr_data'        => FALSE,
    	'cli_cfg_syncr_data_orders' => FALSE,
        'cli_cfg_dismantle_order'   => FALSE,
        'cli_cfg_submit_ticket'     => FALSE,
        'cli_cfg_cal_bonus'         => FALSE,
        'cli_cfg_backaward_bonus'   => FALSE,
        'cli_chase_statistics'      => FALSE,
        'cli_chase_bet'             => FALSE,
        'cli_chase_bet_all'         => FALSE,
        'cli_zhisheng'              => FALSE,
    	'cli_cfg_collect_status'    => FALSE
    );
    $config['cp_host'] = 'http://172.15.12.126/';
    $config['defaultChannel'] = array(
        'web' => 1,
        'app' => 2,
    );
}

$config['app_path'] = BASEPATH . '../' . APPPATH;
$config['base_url'] = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http') . '://888.166cai.cn/';
$config['phone_voice'] = 'http://voice.2345.cn'; //语音接口
$config['pages_url'] = '//888.166cai.cn/';
$config['m_pages_url'] = 'http://8.166cai.cn/';  // M版地址
$config['app_pages_url'] = '//www.ka5188.com/';
$config['domain'] = '888.166cai.cn';
$config['ios_domain'] = 'https://www.ka5188.com';
$config['android_domain'] = 'https://www.ka5188.com';
$config['url_prefix'] = array(
	'www.166cai.com' => 'https',
	'www.166cai.cn' => 'https',
	'888.166cai.cn' => 'https',
);

//图片静态资源域名
$config['img_url'] = array(
    '//img1.166cai.cn/',
    '//img2.166cai.cn/',
    '//img3.166cai.cn/',
    '//img4.166cai.cn/',
    '//img5.166cai.cn/',
    '//img6.166cai.cn/'
);

$config['web_instance'] = 2;

if (empty($config['qhtob_pji']))
{
    $config['qhtob_pji'] = 'http://112.124.100.187/twotofive/v1';
    $config['qhtob_secret'] = '123456789123456789123456';//'123456789123456789123456';   //齐汇票机秘钥
    $config['qhtob_sellerid'] = 'CP166test';//'CP2345-test';   //齐汇票机销售商id
}
if (empty($config['qhtob_dzh']))
{
    $config['qhtob_dzh'] = 'http://112.124.100.187:8001';
}
//彩豆测试机
if (empty($config['cdtob_pji']))
{
    $config['cdtob_pji'] = 'http://180.166.119.206:8081/test/test.go';
    $config['cdtob_secret'] = '123456';   //彩豆票机秘钥
    $config['cdtob_sellerid'] = '8166';   //彩豆票机销售商id
}
//善彩测试机
if (empty($config['sctob_pji']))
{
    $config['sctob_pji'] = 'http://150.242.239.196:8047/ChinaLotteryPlatform/lotteryAgent';
    $config['sctob_secret'] = 'AUTOLOTTERY';   //善彩票机秘钥
    $config['sctob_sellerid'] = '29002000147';   //善彩票机销售商id
}

//华阳测试机
if(empty($config['hytob_pji'])){
    $config['hytob_pji'] = 'http://b2blib.198fc.cn:8081/b2blib/lotteryxml.php';
    $config['hytob_sellerid'] = '10001352';   //代理号
    $config['hytob_username'] = '10001352';
    $config['hytob_secret'] = 'de59b1a942f087d90504b3401fc7b980';   //票机秘钥
}

//恒钜测试机
if(empty($config['hjtob_pji'])){
    $config['hjtob_pji'] = 'http://120.26.57.104:9094';
    $config['hjtob_sellerid'] = '7041';   //代理号
    $config['hjtob_username'] = 'test';   //用户名
    $config['hjtob_password'] = '123456'; //密码
    $config['hjtob_secret'] = '23068483872946';   //票机签名
}

//福牛牛测试机
if(empty($config['fnntob_pji'])) {
    $config['fnntob_pji'] = 'http://123.57.147.232:8088/service?wsdl';
    $config['fnntob_sellerid'] = '800152';   //代理号
    $config['fnntob_secret'] = '166caitest';   //票机签名
}

$config['sms_url'] = 'http://duanxin.km.com/Api/Sms/Send'; //短信接口
/*
 * 根目录定义 
 * */
$config['base_path'] = dirname(BASEPATH);
//监控内存
$config['dieMemSize'] = 800;
/**
 * redis keys
 */
$config['REDIS'] = array(
    'UNIQUE_KEY'              => '_unique_key:',
    'ORDERS_CHECK_START_TIME' => '_orders_check_start_time:',
	'ORDERS_SCAN_START_TIME'  => '_orders_scan_start_time:',
	'ORDERS_EMAIL_START_TIME'  => '_orders_email_start_time:',
  'UNITED_CHECK_START_TIME' => '_united_check_start_time:',
    'UNITED_ORDERS_CHECK_START_TIME' => '_united_orders_check_start_time:',
    'USER_INFO'               => '_user_info:',
    'APP_LOGIN'               => '_app_login:',
	'BANK_INFO'               => '_bank_info:',
    'CX_DATA'                 => '_cx_data:',
    'CX_API'                  => '_cx_api:',
    'CX_API_PARAMS'           => '_cx_params:',
    'CX_API_SETS'             => '_cx_api_sets:',
    'CACHE_TYPE'              => array(
        'ticket_data_pl0' => '_cache_key0:6:1',//key:timeout:switch
    ),
    'NOTICE_RECORDS'          => '_notice_records:',
    'TOTAL_WIN'               => '_total_win:',
    'CLICK_NUM'               => '_click_num:',
    'JCZQ_EUROPE_ODDS'        => '_jczq_europe_odds:',
    'JCLQ_EUROPE_ODDS'        => '_jclq_europe_odds:',
    'SFC_EUROPE_ODDS'         => '_sfc_europe_odds:',
    'JCZQ_MATCH'              => '_jczq_match:',
    'JCZQ_OLD_MATCH'              => '_jczq_old_match:',
    'JCLQ_MATCH'              => '_jclq_match:',
    'JCZQ_HISTORY'            => '_jczq_history:',
    'JCLQ_HISTORY'            => '_jclq_history:',
    'validChannels'           => '_validChannels',
    'SSQ_ISSUE'               => '_ssq_issue:',
    'DLT_ISSUE'               => '_dlt_issue:',
    'FC3D_ISSUE'              => '_fc3d_issue:',
    'PLS_ISSUE'               => '_pls_issue:',
    'PLW_ISSUE'               => '_plw_issue:',
    'QXC_ISSUE'               => '_qxc_issue:',
    'QLC_ISSUE'               => '_qlc_issue:',
    'SFC_MATCH'               => '_sfc_match:',
    'SFC_ISSUE'               => '_sfc_issue:',
    'RJ_ISSUE'                => '_rj_issue:',
    'SFC_MATCH_NEW'           => '_sfc_match_new:',
    'SFC_ISSUE_NEW'           => '_sfc_issue_new:',
    'RJ_ISSUE_NEW'            => '_rj_issue_new:',
    'SSQ_MISS'                => '_ssq_miss',
    'DLT_MISS'                => '_dlt_miss',
    'SYXW_MISS'               => '_syxw_miss',
    'JXSYXW_MISS'             => '_jxsyxw_miss',
    'HBSYXW_MISS'             => '_hbsyxw_miss',
    'GDSYXW_MISS'             => '_gdsyxw_miss',
    'KS_MISS'                 => '_ks_miss',
    'JLKS_MISS'               => '_jlks_miss',
    'JXKS_MISS'               => '_jxks_miss',
    'KLPK_MISS'               => '_klpk_miss',
    'CQSSC_MISS'              => '_cqssc_miss',
    'QLC_MISS'                => '_qlc_miss',
    'QXC_MISS'                => '_qxc_miss',
    'PL3_MISS'                => '_pl3_miss',
    'PL5_MISS'                => '_pl5_miss',
    'FC3D_MISS'               => '_fc3d_miss',
    'SSQ_MISS_MORE'           => '_ssq_miss_more',
    'DLT_MISS_MORE'           => '_dlt_miss_more',
    'SYXW_MISS_MORE'          => '_syxw_miss_more',
    'JXSYXW_MISS_MORE'        => '_jxsyxw_miss_more',
    'HBSYXW_MISS_MORE'        => '_hbsyxw_miss_more',
    'GDSYXW_MISS_MORE'        => '_gdsyxw_miss_more',
    'KS_MISS_MORE'        	  => '_ks_miss_more',
    'JLKS_MISS_MORE'          => '_jlks_miss_more',
    'JXKS_MISS_MORE'          => '_jxks_miss_more',
    'KLPK_MISS_MORE'          => '_klpk_miss_more',
    'CQSSC_MISS_MORE'         => '_cqssc_miss_more',
    'QLC_MISS_MORE'           => '_qlc_miss_more',
    'QXC_MISS_MORE'           => '_qxc_miss_more',
    'PL3_MISS_MORE'           => '_pl3_miss_more',
    'PL5_MISS_MORE'           => '_pl5_miss_more',
    'FC3D_MISS_MORE'          => '_fc3d_miss_more',
    'JCZQ_AWARD'              => '_jczq_award:',
    'JCLQ_AWARD'              => '_jclq_award:',
    'JCZQ_AWARD_LAST'         => '_jczq_award_last:',
    'JCLQ_AWARD_LAST'         => '_jclq_award_last:',
    'SYXW_AWARD'              => '_syxw_award:',
    'JXSYXW_AWARD'            => '_jxsyxw_award:',
    'HBSYXW_AWARD'            => '_hbsyxw_award:',
    'GDSYXW_AWARD'            => '_gdsyxw_award:',
    'KS_AWARD'            	  => '_ks_award:',
    'JLKS_AWARD'              => '_jlks_award:',
    'JXKS_AWARD'              => '_jxks_award:',
    'KLPK_AWARD'              => '_klpk_award:',
    'CQSSC_AWARD'             => '_cqssc_award:',
    'LOTTERY_CONFIG'          => '_lottery_config:',
    'SSQ_HISTORY'             => '_ssq_history:',
    'DLT_HISTORY'             => '_dlt_history:',
    'QLC_HISTORY'             => '_qlc_history:',
    'QXC_HISTORY'             => '_qxc_history:',
    'PLS_HISTORY'             => '_pls_history:',
    'PLW_HISTORY'             => '_plw_history:',
    'FC3D_HISTORY'            => '_fc3d_history:',
    'SYXW_ISSUE_TZ'           => '_syxw_issue_tz:',
    'JXSYXW_ISSUE_TZ'         => '_jxsyxw_issue_tz:',
    'HBSYXW_ISSUE_TZ'         => '_hbsyxw_issue_tz:',
    'GDSYXW_ISSUE_TZ'         => '_gdsyxw_issue_tz:',
    'KS_ISSUE_TZ'         	  => '_ks_issue_tz:',
    'JLKS_ISSUE_TZ'           => '_jlks_issue_tz:',
    'JXKS_ISSUE_TZ'           => '_jxks_issue_tz:',
    'KLPK_ISSUE_TZ'           => '_klpk_issue_tz:',
    'CQSSC_ISSUE_TZ'          => '_cqssc_issue_tz:',
    'CHASE_BET_NUM'           => '_chase_bet_num:',      // 追号投注数量
    'CHASE_BET_ISSUE'         => '_chase_bet_issue:',  // 追号投注期次
    'ISSUE_COMING'            => '_issue_coming:',
    'TICKET_SELLER'           => '_ticket_seller:',
    'ssSelling'               => '_ssSelling:',         //春节停售
    'SHOUYE'                  => '_shouye:',            //首页信息
    'SSQ_LUCKY'               => '_ssq_lucky:',
    'DLT_LUCKY'               => '_dlt_lucky:',
    'QLC_LUCKY'               => '_qlc_lucky:',
    'NUM_LUCKY'               => '_num_lucky:',
    'LUCKY_TIME'              => '_lucky_time:',
    'NBA'                     => '_nba:',
    'AWARD_NOTICE'            => '_award_notice:',
    'ACTIVITY_LX'             => '_activity_lx:',
    'WEIXIN_TICKET'           => '_weixin_ticket:',  // 微信公共平台ticket
    'WEIXIN_TOKEN'            => '_weixin_token:',   // 微信公共平台token
    'EUROPE_SCORE'            => '_europe_score:',   // 五大联赛积分榜
    'EUROPE_SCHEDULE'         => '_europe_schedule:',   // 五大联赛赛程
    'CHECK_LIMIT'             => '_check_limit:',
    'BANNER'                  => '_banner',
    'JCJJ_HOVER'              => 'jcjj_hover:',
    'CLICK_COUNT'			  => 'click_count',
    'USERVPOP'                => 'uservpop',            //用户服务弹层
    'CLIRUNPARAMS'            => '_clirunparams:',      //拆票脚本执行使用参数
    'CRONTAB_CONFIG'          => '_crontab_config:',    //脚本配置缓存
    'NEWLY_ISSUES'            => '_newly_issues',        //最近期次（目前用于合买大厅）
    'BANNERS'                 => '_banners',
    'USER_HOT'                => '_isHot:',
    'RCG_DISPATCH'            => '_rcg_dispatch:',      //充值渠道分配缓存
    'PAY_BANK_INFO'           => '_pay_bank_info:',
    'CS_PAY_CONFIG'	      => '_cs_pay_config:',         //199,194测试充值渠道分配缓存
    'CS_RCG_DISPATCH'            => '_cs_rcg_dispatch:',      //199,194测试充值渠道分配缓存
    'PAY_RATE_CONFIG'         => '_pay_rate_config:',   // 支付宝微信比例
    'JCZQ_BET_COUNT'          => 'jczq_bet_count:',     //竞彩投注比例
    'JCLQ_BET_COUNT'          => 'jclq_bet_count:',
    'SFC_BET_COUNT'           => 'sfc_bet_count:',
    'LIMIT_CODE'			  => '_limit_code:',
    'COMBINE_MISS'            => '_combine_miss:',      //组合遗漏
    'UNITED_FOLLOW'           => '_united_follow',      //跟单任务列表
    'ADD_INFO'                => '_add_info:',
    'LOTTERY_INFO'            => '_lottery_info:',
    'APP_INDEX'               => '_app_index:',
    'APP_INDEX_NEW'           => '_app_index_new:',
    'JIFEN'                   => '_jifen:',//积分
    'USER_TAG_TEAM'           => '_user_tag_team:',      //用户标签任务队列
    'PC_PAY_CONFIG'	      => '_pc_pay_config:',
    'CS_PC_PAY_CONFIG'	      => '_cs_pc_pay_config:',
    'WC_RP_MONEY'           => '_wc_rp_money:',
    'PC_PAY_GUIDE_CONFIG'     => '_pc_pay_guide_config:',
    'CHANNEL_INFO'            => '_channel_info:',
    'APP_CONFIG'              => '_app_config:',
);

$config['OUTTIME'] = array('captche' => 30, 'cx_data' => 5);
$config['MESSAGE'] = array(
    'captche'       => '验证码：#CODE#，请及时正确输入，切勿将验证码告知他人！',
    'tick_fail'     => '您的帐号#UNAME#于#MM#月#DD#日#HH#：#II#投注的#LID#出票失败，支付金额已返还您的帐户。',
    'lottery_fail'  => '您的帐号#UNAME#于#MM#月#DD#日 #HH#：#II#投注的#LID#投注失败，支付金额已返还您的帐户。',
    'win_prize'     => '恭喜您！您于#MM#月#DD#日预约的#LID#中奖#MONEY#元，已派奖到您的帐户。下载APP查看： t.cn/R9SyzIp ',
    'win_prize_jj'  => '恭喜您！您于#MM#月#DD#日预约的#LID#中奖#MONEY#元，加奖#ADDS#元，已派奖到您的帐户。下载APP查看： t.cn/R9SyzIp',
    'chase_complete'=> '您于#MM#月#DD#日发起的#LID#追号方案已完成，马上下载APP发起新一轮追号: t.cn/R9SyzIp，小六祝您购彩愉快！',
    'chase_activity_complete'=> '您于#MM#月#DD#日发起的#LID#追号不中包赔方案已完成，已返#MONEY#元彩金到您的帐户。下载APP查看: t.cn/R9SyzIp',
    'app_download'  => '下载166彩票手机客户端，为生活添彩！http://www.166cai.cn/source/download/android/app-release.apk 退订回T',
    '166_huodong'   => '红包地址：http://www.166cai.cn/app/activity/appOutScj 点击领取！退订回T',
	'188_hongbao'   => '您已成功领取188元红包，马上下载APP使用红包： t.cn/R9SyzIp ，小六祝您购彩愉快！',
    '166_hongbao'   => '您已成功领取166元红包，马上下载APP使用红包： t.cn/R9SyzIp ，小六祝您购彩愉快！',
    'laxin'   => '您已成功领取1分钱买彩票红包，马上下载APP使用红包： t.cn/RKEsdaQ ，小六祝您购彩愉快！',
	'166_hongbao_1' => '您已成功领取188元红包，马上下载APP使用红包： t.cn/R9SyzIp ，小六祝您购彩愉快！',
	'166_hongbao_2' => '您已成功领取188元红包，马上下载APP使用红包： t.cn/R9SyzIp ，小六祝您购彩愉快！',
	'order_draw'    => '您于#MM#月#DD#日预约的#MONEY#元#LID#已成功出票！下载APP查看： t.cn/R9SyzIp ',
	'order_drawpart'=> '您于#MM#月#DD#日预约投注的#MONEY#元#LID#成功出票#MONEY1#元，失败#MONEY2#元。下载APP查看： t.cn/R9SyzIp ',
	'order_concel'  => '您于#MM#月#DD#日预约的#MONEY#元#LID#出票失败。下载APP查看： t.cn/R9SyzIp ',
	'order_concel_hongbao' => '您于#MM#月#DD#日预约的#MONEY#元#LID#出票失败，出票失败订单金额和所使用的购彩红包已返还到您账户中。下载APP查看： t.cn/R9SyzIp ',
	'join_huodong'  => '166彩票提醒您，您邀请好友成功，获得1次抽奖机会，赶快来抽取千元彩金吧！',
    'united_tick_fail' => '您于#MM#月#DD#日参与合买的#MONEY#元#LID#方案撤单。下载APP查看： t.cn/R9SyzIp ',
    'united_win_prize' => '恭喜您！您于#MM#月#DD#日参与#LID#合买中奖#MONEY#元，已派奖到您的帐户。下载APP查看： t.cn/R9SyzIp',
    'withdraw_succ' => '您于#MM#月#DD#日申请提现已处理，提现金额#MONEY#元。下载APP查看进度： t.cn/R9SyzIp ',
    'withdraw_fail' => '您于#MM#月#DD#日申请提现未完成，#REASON#，提现金额已返还您的彩票帐户。下载APP查看进度： t.cn/R9SyzIp ',
    'united_follow_complete' => '您于#MM#月#DD#日发起的#LID#定制跟单次数用完，马上下载APP发起新一轮定制： t.cn/R9SyzIp ',
    'wechat_register' => '您已成功加入166彩票，初始登录密码为#CODE#，可到安全中心修改，小六祝您购彩愉快！',
    'win_prize_jj_jxsyxw'  => '恭喜您！您于#MM#月#DD#日预约的#LID#中奖#MONEY#元，加奖#ADDS#元，已派奖到您的帐户。下载APP查看： t.cn/RfEXK3b',
    'user_upgrade' => '亲爱的#UNAME#，恭喜您晋升为#GRADENAME#，小六为您送上#MONEY#元彩金红包，下载APP领取使用： t.cn/R9SyzIp ',
    'user_birth' => '亲爱的#UNAME#，生日快乐！再忙也别忘了这个重要的日子，小六为您送上#MONEY#元生日礼包，下载APP领取使用： t.cn/R9SyzIp',
    'activity_xnhk' => '您已成功领取166元红包，马上下载APP使用红包： t.cn/R8HlC8U，小六祝您购彩愉快！',
    'win_rank' => '亲爱的#UNAME2#，恭喜大神排行榜获得第#CONTENT#名，小六特为您送上#MONEY#元彩金，马上下载APP使用红包： t.cn/R9SyzIp',
    'worldcup_redpack' => '亲，您的#CONTENT#已生效！红包在手，大奖不愁~快来使用： t.cn/Rm15h9K',
    'activity_jchd' => '恭喜您！您于#MM#月#DD#日完成的第#ISSUE#期世界杯竞猜分得奖金#MONEY#元，已经派奖到您的账户。下载APP查看： t.cn/R9SyzIp',
);
$config['POSITION'] = array(
	'register_captche' => 249,
	'login_captche'    => 188,
	'phone_captche'    => 189,
	'activity_captche' => 190,
	'withdraw_captche' => 191,
	'win_prize'        => 192,
	'tick_fail'        => 193,
	'withdraw_succ'    => 194,
	'withdraw_fail'    => 194,
	'app_download'     => 195,
	'166_huodong'      => 195,
	'chase_complete'   => 192,
	'166_hongbao'      => 193,
	'order_draw'       => 193,
	'order_drawpart'   => 193,
	'order_concel'     => 193,
	'join_huodong'     => 193,
  'united_tick_fail' => 193,
  'united_win_prize' => 192,
	'order_drawpart_hongbao'   => 193,
	'order_concel_hongbao'     => 193,
    'united_follow_complete'   => 192,
    'wechat_register'  => 249,
);

$config['own_ip'] = array(
    '127.0.0.1',
    '221.228.75.166',
    '221.228.75.146',
    '221.228.75.173',
    '183.136.203.69',
	'183.136.203.74',
    "172.15.12.197",
    '42.62.31.39',
	'183.136.203.154',
	'183.136.203.111',
	'180.168.34.146',
	'172.16.18.67',
	'172.16.16.74',
	'101.71.94.147',
	'101.71.94.159',
	'116.228.6.140', //博霞路IP
	'221.228.75.209',//影视线上IP
	'180.167.67.10',  //亮秀路IP
	'183.136.203.74', //彩票内页
	'183.136.203.121', //积分
	'183.136.203.119',//积分
	'180.168.34.146',//积分
	'172.16.0.39', //166caiIP
    '172.16.0.34', 
    '172.16.0.35', 
    '172.16.0.36', 
    '172.16.0.37',
    '42.62.31.34', 
    '42.62.31.35', 
    '42.62.31.36', 
    '42.62.31.37',
	'203.156.200.226',
	'222.66.88.98',
    '180.169.86.54',
    '120.132.33.194',
    '120.132.33.195',
    '120.132.33.196',
    '120.132.33.197',
    '120.132.33.198',
    '123.59.105.39',
    '120.132.33.200',
    '120.132.33.201',
    '120.132.33.202',
    '120.132.33.203',
    '120.132.33.204',
    '120.132.33.205',
    '120.132.33.206',
	'203.156.250.242', //彩票内页IP
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
$config['uri_protocol'] = 'AUTO';

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
$config['language'] = 'english';

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
$config['enable_hooks'] = TRUE;


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
$config['allow_get_array'] = TRUE;
$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use

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
  |   1 = Log Messages
  |	2 = Error Messages (including PHP errors)
  |	3 = Debug Messages
  |	4 = Informational Messages
  |	5 = All Messages
  |
  | For a live site you'll usually only enable Errors (1) to be logged otherwise
  | your log files will fill up very fast.
  |
 */

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
$config['sess_cookie_name'] = 'ci_session';
$config['sess_expiration'] = 7200;
$config['sess_expire_on_close'] = TRUE;
$config['sess_encrypt_cookie'] = TRUE;
$config['sess_use_database'] = FALSE;
$config['sess_table_name'] = 'ci_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_match_useragent'] = TRUE;
$config['sess_time_to_update'] = 300;

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
$config['cookie_prefix'] = "";
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";
$config['cookie_secure'] = FALSE;
$config['cookie_expire'] = 30;

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

// 移动端回调加密
$config['encrypt_hash'] = 'M#jM0NeSv#wMDG9+8rVsti80A==3g.0';

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

//投注站所接彩种
$config['cfg_partner_lid'] = array(
		KS => array('ename' => 'ks', 'cname' => '上海快三'), 
		SYXW => array('ename' => 'syxw', 'cname' => '老11选5'),
		JXSYXW => array('ename' => 'jxsyxw', 'cname' => '新11选5'),
		HBSYXW => array('ename' => 'hbsyxw', 'cname' => '惊喜11选5'),
		DLT => array('ename' => 'dlt', 'cname' => '大乐透'),
        KLPK => array('ename' => 'klpk', 'cname' => '快乐扑克'),
        CQSSC => array('ename' => 'cqssc', 'cname' => '老时时彩'),
		JLKS => array('ename' => 'jlks', 'cname' => '吉林快三'),
        JXKS => array('ename' => 'jxks', 'cname' => '江西快三'),
        GDSYXW => array('ename' => 'gdsyxw', 'cname' => '乐11选5')
);

$config['split_lid'] = array('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421');
//提现额度(百分之)
$config['txed'] = 50;
/* End of file config.php */
/* Location: ./application/config/config.php */
