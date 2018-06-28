<?php

/*
 * APP 小米推送类
 * @date:2017-05-24
 */

class Mipush
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('mipush_model');
        $this->CI->load->model('user_model');
        $this->CI->load->model('channel_model');
        $url_prefix = $this->CI->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->CI->config->item('domain')]) ? $url_prefix[$this->CI->config->item('domain')] : 'http';
    }

    /*
    * 配置信息
    * @date:2017-05-24
    */
    private $pushConfig = array(
        // 安卓配置
        0   =>  array(
            'plat'      =>  'android',
            'package'   =>  'com.caipiao166',
            'appSecret' =>  'pnYQnu3N82RIS95iC1hxug==',
        ),
        // IOS配置
        1   =>  array(
            'plat'      =>  'ios',
            'package'   =>  'com.166cai.lottery',
            'appSecret' =>  'dEDI0cjQeFF8LVbZTsl+/A==',
        ),
        // IOS马甲版
        // 2   =>  array(
        //     'plat'      =>  'iosMajia',
        //     'package'   =>  'com.166cai.ssq',
        //     'appSecret' =>  'sn+A1M/KwrffNBUjSjTGdw==',
        // ),
        // 竞彩166
        3   =>  array(
            'plat'      =>  'android',
            'package'   =>  'com.caipiao169',
            'appSecret' =>  '42mp93f1E5ddGXpq9FF0EA==',
        ),
        // 超级大乐透
        4   =>  array(
            'plat'      =>  'android',
            'package'   =>  'com.caipiao170',
            'appSecret' =>  'e6t632H/SrweNBlt1tCXYw==',
        ),
    );

    /*
    * 获取主域名
    * @date:2017-05-24
    */
    public function getDomain($type)
    {
        return $this->CI->config->item($type."_domain");
    }

    /*
    * 主函数
    * @date:2017-05-24
    */
	public function index($pushType, $pushData)
	{
        if(!empty($pushType) && !empty($pushData))
        {
            switch ($pushType) 
            {
                // 按用户推送
                case 'user':
                    $pushRes = $this->pushByUser($pushData);
                    break;
                // 按主题推送
                case 'topic':
                    $pushRes = $this->pushByTopic($pushData);
                    break;  
                // 按用户打开客户端或指定url
                case 'user_com':
                    $pushRes = $this->pushByUserCom($pushData);
                    break;  
                default:
                    $pushRes = array(
                        'status' => FALSE,
                        'msg' => '推送类型错误'
                    );
                    break;
            }
        }
        else
        {
            $pushRes = array(
                'status' => FALSE,
                'msg' => '推送信息错误'
            );
        }
        return $pushRes;
	}
	
	public function getSendData($pushType, $pushData) {
	    if(!empty($pushType) && !empty($pushData))
	    {
	        switch ($pushType)
	        {
	            // 按用户推送
	            case 'user':
	                $pushRes = $this->pushByUser($pushData, false);
	                break;
	                // 按主题推送
	            case 'topic':
	                $pushRes = $this->pushByTopic($pushData, false);
	                break;
	                // 按用户打开客户端或指定url
	            case 'user_com':
	                $pushRes = $this->pushByUserCom($pushData, false);
	                break;
	            default:
	                $pushRes = array(
	                'status' => FALSE,
	                'msg' => '推送类型错误'
	                    );
	                    break;
	        }
	    }
	    else
	    {
	        $pushRes = array(
	            'status' => FALSE,
	            'msg' => '推送信息错误'
	        );
	    }
	    return $pushRes;
	}

    /*
    * 获取配置信息
    * @date:2017-05-24
    */
    public function getConfig()
    {
        return $this->pushConfig;
    }

    /*
    * 按用户推送
    * @date:2017-05-24
    */
    public function pushByUser($pushData, $send = true)
    {
        /*
        可能提交的字段说明：
        $pushData = array(
            'uid'       =>  '',     // 用户uid
            'lid'       =>  '',     // 彩种ID
            'lname'     =>  '',     // 彩种名称
            'time'      =>  '',     // 时间
            'orderId'   =>  '',     // 订单编号
            'money'     =>  '',     // 金额
            'add_money' =>  '',     // 加奖金额
            'trade_no'  =>  '',     // 流水编号
            'content'   =>  '',     // 内容
            'uname'     =>  '',     // 用户名
            'title'     =>  '',     // 标题
        );
        */

        if($pushData['uid'])
        {
            // 用户加密信息
            $token = $this->strCode(json_encode(array('uid' => (string)$pushData['uid'])), 'ENCODE');
            switch ($pushData['type']) 
            {
                // 中奖推送
                case 'win_prize':
                    $sendData = array(
                        'ptype'         =>  1,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '中奖',
                        'description'   =>  '恭喜您！您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '预约的' . $pushData['lname'] . '中奖' . $pushData['money'] . '元，已派奖到您的帐户，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                        'time_to_live'  =>  24 * 60 * 60 * 1000,
                    );
                    break;

                // 中奖加奖推送
                case 'win_prize_jj':
                    $sendData = array(
                        'ptype'         =>  2,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '中奖',
                        'description'   =>  '恭喜您！您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '预约的' . $pushData['lname'] . '中奖' . $pushData['money'] . '元，加奖' . $pushData['add_money'] . '已派奖到您的帐户，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                        'time_to_live'  =>  24 * 60 * 60 * 1000,
                    );
                    break;

                // 提现申请推送
                case 'withdraw_succ':
                    $sendData = array(
                        'ptype'         =>  3,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  '提现通知',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '提交的提款申请已处理，提现金额' . $pushData['money'] . '元，请注意查收。具体到账时间以银行处理时间为准。',
                        'app_url'       =>  $this->getDomain('android') . '/app/trade/detail/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/trade/detail/',
                        'token'         =>  $this->strCode(json_encode(array('uid' => $pushData['uid'], 'tradeNo' => $pushData['trade_no'])), 'ENCODE'),
                        'ios_action'    =>  'openURLWithToken',
                    );
                    break;

                // 提现失败推送
                case 'withdraw_fail':
                    $sendData = array(
                        'ptype'         =>  4,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  '提现失败',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '申请提现未完成，' . $pushData['content'] . '，提现金额已返还您的彩票帐户，您可确认后重新申请提现，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/trade/detail/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/trade/detail/',
                        'token'         =>  $this->strCode(json_encode(array('uid' => $pushData['uid'], 'tradeNo' => $pushData['trade_no'])), 'ENCODE'),
                        'ios_action'    =>  'openURLWithToken',
                    );
                    break;

                // 反馈推送
                case 'feedback':
                    $sendData = array(
                        'ptype'         =>  5,
                        'type'          =>  'feedback',
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  '您的反馈有了新回复：',
                        'description'   =>  $pushData['content'],
                        'app_url'       =>  '',
                        'ios_url'       =>  '',
                        'token'         =>  '',
                        'app_action'    =>  'intent:#Intent;component=com.caipiao166/.setting.feedback.UserFeedbackActivity;end',
                        'ios_action'    =>  'openAPP',
                        'ios_path'      =>  'feedback',
                    );
                    break;

                // 追号完成推送
                case 'chase_complete':
                    $sendData = array(
                        'ptype'         =>  6,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '追号完成',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '发起的' . $pushData['lname'] . '追号方案已完成，现在可以发起新一轮追号，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/chase/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/chase/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;

                // 出票成功
                case 'order_draw':
                    $sendData = array(
                        'ptype'         =>  7,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '出票成功',
                        'description'   =>  '您的' . $pushData['lname'] . '订单' . $pushData['orderId'] . '已成功出票，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;

                // 部分出票失败推送
                case 'order_drawpart':
                    $sendData = array(
                        'ptype'         =>  8,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '部分出票成功',
                        'description'   =>  '您的' . $pushData['lname'] . '订单' . $pushData['orderId'] . '部分出票成功，失败金额已返还您的帐户，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;

                // 出票失败推送
                case 'order_concel':
                    $sendData = array(
                        'ptype'         =>  9,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '出票失败',
                        'description'   =>  '您的' . $pushData['lname'] . '订单' . $pushData['orderId'] . '出票失败，支付金额已返还您的帐户，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;

                // 投注失败推送
                case 'lottery_fail':
                    $sendData = array(
                        'ptype'         =>  10,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '投注失败',
                        'description'   =>  '您的' . $pushData['lname'] . '订单' . $pushData['orderId'] . '预约失败，支付金额已返还您的帐户，点击查看详情。',
                        'app_url'       =>  $this->getDomain('android') . '/app/order/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/order/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;

                // 合买关注推送
                case 'united_follow':
                    $sendData = array(
                        'ptype'         =>  11,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '合买关注提醒',
                        'description'   =>  $pushData['uname'] . '发合买啦，快来参与赢大奖，一起登上人生巅峰啊！',
                        'app_url'       =>  $this->getDomain('android') . '/app/hemai/detail/hm' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/hemai/detail/hm' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                    );
                    break;  

                // 红包推送
                case 'redpack_use':
                    $sendData = array(
                        'ptype'         =>  12,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  $pushData['title'],
                        'description'   =>  $pushData['content'],
                        'app_url'       =>  $this->getDomain('android') . '/app/redpack/index/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/redpack/index/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                        'time_to_live'  =>  $pushData['time_to_live'],
                    );
                    break; 

                // 追号不中包赔完成推送
                case 'chase_activity_complete':
                    $sendData = array(
                        'ptype'         =>  16,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  $pushData['lid'],
                        'title'         =>  '追号不中包赔彩金到账啦',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '发起的' . $pushData['lname'] . '追号不中包赔方案已完成，已返' . $pushData['money'] . '元彩金到您的帐户>>',
                        'app_url'       =>  $this->getDomain('android') . '/app/chase/detail/' . $pushData['orderId'] . '/',
                        'ios_url'       =>  $this->getDomain('ios') . '/ios/chase/detail/' . $pushData['orderId'] . '/',
                        'token'         =>  $token,
                        'ios_action'    =>  'openURLWithTokenForOrder',
                        'time_to_live'  =>  2 * 60 * 60 * 1000,
                    );
                    break; 

                // 抽奖推送
                case 'activity_draw':
                    $sendData = array(
                    'ptype'         =>  18,
                    'uid'           =>  $pushData['uid'],
                    'lid'           =>  '',
                    'title'         =>  $pushData['title'],
                    'description'   =>  $pushData['content'],
                    'app_url'       =>  $this->getDomain('android') . '/app/activity/xnhk/',
                    'ios_url'       =>  $this->getDomain('ios') . '/ios/activity/xnhk/',
                    'token'         =>  $token,
                    'ios_action'    =>  'openURLWithTokenForOrder',
                    'time_to_live'  =>  $pushData['time_to_live'],
                    );
                    break;
                
                default:
                    $sendData = array();
                    break;
            }
            // 记录操作
            if ($send) {
                if(!empty($sendData)) $this->recodeUserLog($sendData);
            } else {
                return $sendData;
            }
            
        }
    }

    /*
    * 组装推送信息
    * @date:2017-05-24
    */
    public function recodeUserLog($pushData)
    {
        $status = 0;
        // 测试环境不推送
        if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35')
        {
            $status = 1;
        }

        // 根据用户最近登录渠道推送
        $pushConfig = array();
        if(!empty($pushData['uid']))
        {
            $pushConfig = $this->getPlatByUser($pushData['uid']);
        }

        if(!empty($pushConfig))
        {
            $fields = array('url', 'content', 'ctype', 'ptype', 'platform', 'status', 'created');
            $pdata['s_data'] = array();
            $pdata['d_data'] = array();

            foreach ($pushConfig as $plat => $items) 
            {    
                $url = $this->pushUserData($pushData, $items['plat'], $items['package']);

                array_push($pdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
                array_push($pdata['d_data'], $url);
                array_push($pdata['d_data'], $pushData['description']);
                array_push($pdata['d_data'], 0);
                array_push($pdata['d_data'], $pushData['ptype']);
                array_push($pdata['d_data'], $plat);
                array_push($pdata['d_data'], $status);
            }

            if(!empty($pdata['s_data']))
            {
                $this->CI->mipush_model->recordPushLog($fields, $pdata);
            }
        }
    }
    
    public function recodeUserLogs($pushDatas)
    {
        if(!empty($pushDatas[0]['uid']))
        {
            $pushConfig = $this->getPlatByUser($pushDatas[0]['uid']);
        }
        if(!empty($pushConfig)) {
            $fields = array('url', 'content', 'ctype', 'ptype', 'platform', 'status', 'created');
            $pdata['s_data'] = array();
            $pdata['d_data'] = array();
            foreach ($pushDatas as $pushData) {
                $status = 0;
                // 测试环境不推送
                if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35') $status = 1;
                
                foreach ($pushConfig as $plat => $items) {
                    $url = $this->pushUserData($pushData, $items['plat'], $items['package']);
                    array_push($pdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
                    array_push($pdata['d_data'], $url);
                    array_push($pdata['d_data'], $pushData['description']);
                    array_push($pdata['d_data'], 0);
                    array_push($pdata['d_data'], $pushData['ptype']);
                    array_push($pdata['d_data'], $plat);
                    array_push($pdata['d_data'], $status);
                }
            }
            if(!empty($pdata['s_data'])) $this->CI->mipush_model->recordPushLog($fields, $pdata);
        }
        
    }

    public function pushUserData($pushData, $plat, $package)
    {
        $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
        // 指定用户    
        $push_url .= '?user_account=' . $pushData['uid'];  
        $push_url .= '&payload=' . '';
        // 包名
        $push_url .= '&restricted_package_name=' . $package;
        // 标题和描述
        $push_url .= '&title=' . urlencode($pushData['title']);
        if($pushData['description'])
        {
           $push_url .= '&description=' . urlencode($pushData['description']); 
        }     
        // 消息类型
        $push_url .= '&notify_type=2';
        $push_url .= '&notify_id=' . date('His') . rand(10, 99);
        // 有效时间 
        $time_to_live = $pushData['time_to_live'] ? $pushData['time_to_live'] : 10 * 60 * 1000;
        $push_url .= '&time_to_live=' . $time_to_live;
        // 透传                                          
        $push_url .= '&pass_through=0';

        // 安卓 - 自定义字段
        if($plat == 'android')
        { 
            if(!empty($pushData['app_action']))
            {
                // 安卓马甲包替换
                if($pushData['type'] == 'feedback' && strpos($pushData['app_action'], $package) == FALSE)
                {
                    // intent:#Intent;component=com.caipiao166/.setting.feedback.UserFeedbackActivity;end
                    // intent:#Intent;component=xxxxx/com.caipiao166.setting.feedback.UserFeedbackActivity;end
                    $places = $package . '/com.caipiao166';
                    $pushData['app_action'] = str_replace('com.caipiao166/', $places, $pushData['app_action']);
                }
                // 原生推送
                $push_url .= '&extra.notify_effect=2';  
                $push_url .= '&extra.intent_uri=' . urlencode($pushData['app_action']);
            }
            else
            {
                // web推送
                $push_url .= '&extra.lid=' . $pushData['lid'];
                $push_url .= '&extra.url=' . $pushData['app_url'];
                $push_url .= '&extra.token=' . $pushData['token'];
            }
            // 用户反馈原生类型
            if($pushData['type'])
            {
                $push_url .= '&extra.pushType=' . $pushData['type'];
            } 
        }
        else
        {
            // IOS - 自定义字段
            $push_url .= '&extra.action=' . $pushData['ios_action'];                    
            $push_url .= '&extra.title=' . urlencode($pushData['title']);           
            $push_url .= '&extra.result=' . urlencode($pushData['description']);

            if(!empty($pushData['ios_path']))
            {
                // 原生推送 
                $push_url .= '&extra.path=' . $pushData['ios_path'];
            }
            else
            {
                // web推送
                $push_url .= '&extra.lid=' . $pushData['lid'];
                $push_url .= '&extra.url=' . $pushData['ios_url'];                         
                $push_url .= '&extra.token=' . $pushData['token'];   
            }  
        }
        return $push_url;
    }

    public function strCode( $str , $action = 'DECODE' )
    {
        $action == 'DECODE' && $str = base64_decode ($str);
        $code = '';
        $hash = 'M#jM0NeSv#wMDG9+8rVsti80A==3g.0';
        $key = md5 ( $hash );
        $keylen = strlen ( $key );
        $strlen = strlen ( $str );
        for($i = 0; $i < $strlen; $i ++)
        {
           $k = $i % $keylen; //余数  将字符全部位移
           $code .= $str[$i] ^ $key[$k]; //位移
        }
        return ($action == 'DECODE' ? $code : base64_encode ( $code ));
    }

    /*
    * 按主题推送
    * @date:2017-05-24
    */
    public function pushByTopic($pushData, $send = true)
    {
        /*
        可能提交的字段说明：
        $pushData = array(
            'lid'       =>  '',     // 彩种ID
            'title'     =>  '',     // 标题
            'description'  =>  '',     // 标题
        );
        */

        switch ($pushData['type']) 
        {
            // 竞彩比分直播
            case 'jclive':
                $sendData = array(
                    'ptype'         =>  13,
                    'type'          =>  $pushData['type'],
                    'topic'         =>  $pushData['type'] . '_' . $pushData['mid'],
                    'jcType'        =>  ($pushData['lid'] == '42') ? '0' : '1',       // 0 竞足 1 竞篮
                    'mid'           =>  $pushData['mid'],
                    'title'         =>  $pushData['title'],
                    'description'   =>  $pushData['description'],
                    'notify_type'   =>  2,
                    'pass_through'  =>  0,
                    'app_url'       =>  '',
                    'ios_url'       =>  '',
                    'ios_action'    =>  '',
                    'time_to_live'  =>  600000,
                );
                break;
            // 购彩推送
            case 'lid_bet':
                $sendData = array(
                    'ptype'         =>  14,
                    'type'          =>  $pushData['type'],
                    'topic'         =>  $pushData['topic'],
                    'lid'           =>  $pushData['lid'],
                    'title'         =>  $pushData['title'],
                    'description'   =>  $pushData['description'],
                    'notify_type'   =>  2,
                    'pass_through'  =>  0,
                    'app_url'       =>  '',
                    'ios_url'       =>  '',
                    'ios_action'    =>  '',
                    'time_to_send'  =>  $pushData['time_to_send'],
                );
                break;
            // 数字彩开奖号码推送
            case 'num_awards':
                $sendData = array(
                    'ptype'         =>  15,
                    'type'          =>  $pushData['type'],
                    'topic'         =>  $pushData['topic'],
                    'lid'           =>  $pushData['lid'],
                    'title'         =>  $pushData['title'],
                    'description'   =>  $pushData['description'],
                    'notify_type'   =>  0,
                    'pass_through'  =>  1,
                    'awardNum'      =>  $pushData['awardNum'],  // 开奖号码
                    'app_url'       =>  '',
                    'ios_url'       =>  '',
                    'ios_action'    =>  '',
                    'time_to_live'  =>  3600000,    // 一小时
                );
                break;    
            default:
                $sendData = array();
                break;
        }

        // 记录操作
        if ($send) {
            if(!empty($sendData)) $this->recodeTopicLog($sendData);
        } else {
            return $sendData;
        }

    }

    /*
    * 组装按主题推送信息
    * @date:2017-05-24
    */
    public function recodeTopicLog($pushData)
    {
        $status = 0;
        // 测试环境不推送
        if(ENVIRONMENT !== 'production')
        {
            $status = 1;
        }

        $pushConfig = $this->pushConfig;
        if(!empty($pushConfig))
        {
            $fields = array('url', 'content', 'ctype', 'ptype', 'platform', 'status', 'created');
            $pdata['s_data'] = array();
            $pdata['d_data'] = array();

            foreach ($pushConfig as $plat => $items) 
            {   
                $url = $this->pushTopicData($pushData, $items['plat'], $items['package']);

                array_push($pdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
                array_push($pdata['d_data'], $url);
                array_push($pdata['d_data'], $pushData['description']);
                array_push($pdata['d_data'], 1);    
                array_push($pdata['d_data'], $pushData['ptype']);
                array_push($pdata['d_data'], $plat);
                array_push($pdata['d_data'], $status);
            }

            if(!empty($pdata['s_data']))
            {
                $this->CI->mipush_model->recordPushLog($fields, $pdata);
            }
        }
    }

    public function pushTopicData($pushData, $plat, $package)
    {
        $push_url = 'https://api.xmpush.xiaomi.com/v2/message/topic';
        // 指定用户    
        $push_url .= '?topic=' . $pushData['topic'];  
        $push_url .= '&payload=' . '';
        // 包名
        $push_url .= '&restricted_package_name=' . $package;
        // 标题和描述
        $push_url .= '&title=' . urlencode($pushData['title']);
        if($pushData['description'])
        {
           $push_url .= '&description=' . urlencode($pushData['description']); 
        }     
        // 消息类型
        if($pushData['notify_type'])
        {
            $push_url .= '&notify_type=' . $pushData['notify_type'];
        }   
        if($pushData['type'] == 'jclive' && !empty($pushData['mid']))
        {
            $push_url .= '&notify_id=' . '166' . $pushData['mid'];
        }
        else
        {
            $push_url .= '&notify_id=' . date('mdHis', time());
        }  
        // 有效时间 
        $time_to_live = $pushData['time_to_live'] ? $pushData['time_to_live'] : 10 * 60 * 1000;
        $push_url .= '&time_to_live=' . $time_to_live;
        // 透传  
        if($pushData['pass_through'] >= 0)
        {
            $push_url .= '&pass_through=' . $pushData['pass_through'];
        }                                      
        // 定时推送 毫秒时间
        if($pushData['time_to_send'])
        {
            $push_url .= '&time_to_send=' . strtotime($pushData['time_to_send']) * 1000;
        }

        // 安卓 - 自定义字段
        if($plat == 'android')
        { 
            if($pushData['mid'])
            {
                $push_url .= '&extra.mid=' . $pushData['mid'];
            }
            if($pushData['jcType'])
            {
                $push_url .= '&extra.jcType=' . $pushData['jcType'];
            }  
            if($pushData['type'])
            {
                $push_url .= '&extra.pushType=' . $pushData['type'];
            } 
            if($pushData['lid'])
            {
                $push_url .= '&extra.lid=' . $pushData['lid'];
            }
            if($pushData['awardNum'])
            {
                $awardNum = str_replace('|', ',', $pushData['awardNum']);
                $push_url .= '&extra.result=' . $awardNum;
            }
            if($pushData['title'] && $pushData['type'] == 'num_awards')
            {
                $push_url .= '&extra.title=' . urlencode($pushData['title']);
            }
        }
        else
        {
            if($pushData['type'] == 'num_awards')
            {
                $push_url .= '&description=' . urlencode($pushData['title'] . $pushData['awardNum']);
                $push_url .= '&extra.title=' . urlencode($pushData['title']);
                $push_url .= '&extra.result=' . $pushData['awardNum'];
                $push_url .= '&extra.lid=' . $pushData['lid'];
            }

            if($pushData['type'] == 'lid_bet')
            {
                $push_url .= '&extra.action=bet';
                $push_url .= '&extra.lotteryid=' . $pushData['lid'];
                $push_url .= '&extra.title=' . urlencode($pushData['title']);
                $push_url .= '&extra.result=' . urlencode($pushData['description']);
                // IOS10及以上新增标题
                $push_url .= '&aps_proper_fields.title=' . urlencode($pushData['title']);
                $push_url .= '&aps_proper_fields.body=' . urlencode($pushData['description']);
            }     

            // IOS比分直播
            if($pushData['type'] == 'jclive')
            {
                $push_url .= '&extra.action=livelist';
                $push_url .= '&extra.title=' . urlencode($pushData['title']);
                $push_url .= '&extra.result=' . urlencode($pushData['description']);
                $push_url .= '&extra.jcType=' . $pushData['jcType'];
                $push_url .= '&extra.mid=' . $pushData['mid'];
                // IOS10及以上新增标题
                $push_url .= '&aps_proper_fields.title=' . urlencode($pushData['title']);
                $push_url .= '&aps_proper_fields.body=' . urlencode($pushData['description']);
            } 
        }
        return $push_url;
    }

    // 新版按用户推送
    public function pushByUserCom($pushData, $send = true)
    {
        if($pushData['uid'])
        {
            // 用户加密信息
            $token = $this->strCode(json_encode(array('uid' => (string)$pushData['uid'])), 'ENCODE');

            switch ($pushData['type']) 
            {
                // 打开客户端
                case 'open_app':
                    $sendData = array(
                        'type'          =>  $pushData['type'],
                        'ptype'         =>  19,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  $pushData['title'],
                        'description'   =>  $pushData['content'],
                        'token'         =>  '',
                        'time_to_live'  =>  $pushData['time_to_live'],
                    );
                    break;
                    
                // 打开指定URL
                case 'open_url':
                    $sendData = array(
                        'type'          =>  $pushData['type'],
                        'ptype'         =>  17,
                        'uid'           =>  $pushData['uid'],
                        'lid'           =>  '',
                        'title'         =>  $pushData['title'],
                        'description'   =>  $pushData['content'],
                        'app_url'       =>  $pushData['app_url'],
                        'ios_url'       =>  $pushData['ios_url'],
                        'token'         =>  '',
                        'time_to_live'  =>  $pushData['time_to_live'],
                    );
                    break;
                
                default:
                    $sendData = array();
                    break;
            }

            // 记录操作
            if ($send) {
                if(!empty($sendData)) $this->recodeUserComLog($sendData);
            } else {
                return $sendData;
            }
        }
    }

    public function recodeUserComLog($pushData)
    {
        $status = 0;
        // 测试环境不推送
        if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35')
        {
            $status = 1;
        }

        // 根据用户最近登录渠道推送
        $pushConfig = array();
        if(!empty($pushData['uid']))
        {
            $pushConfig = $this->getPlatByUser($pushData['uid']);
        }

        if(!empty($pushConfig))
        {
            $fields = array('url', 'content', 'ctype', 'ptype', 'platform', 'status', 'created');
            $pdata['s_data'] = array();
            $pdata['d_data'] = array();

            foreach ($pushConfig as $plat => $items) 
            {    
                $url = $this->pushUserComData($pushData, $items['plat'], $items['package']);
                array_push($pdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
                array_push($pdata['d_data'], $url);
                array_push($pdata['d_data'], $pushData['description']);
                array_push($pdata['d_data'], 0);
                array_push($pdata['d_data'], $pushData['ptype']);
                array_push($pdata['d_data'], $plat);
                array_push($pdata['d_data'], $status);
            }

            if(!empty($pdata['s_data']))
            {
                $this->CI->mipush_model->recordPushLog($fields, $pdata);
            }
        }
    }

    public function pushUserComData($pushData, $plat, $package)
    {
        // 组装数据
        $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
        // 指定用户    
        $push_url .= '?user_account=' . $pushData['uid']; 
        // 包名
        $push_url .= '&restricted_package_name=' . $package;
        // 标题
        $push_url .= '&title=' . urlencode($pushData['title']);
        // 描述
        $push_url .= '&description=' . urlencode($pushData['description']);
        // 消息号
        $push_url .= '&notify_id=' . date('His') . rand(10, 99);
        // 有效时间
        $time_to_live = $pushData['time_to_live'] ? $pushData['time_to_live'] : 10 * 60 * 1000;
        $push_url .= '&time_to_live=' . $time_to_live;
        // 透传                                          
        $push_url .= '&pass_through=0';
        $push_url .= '&notify_type=2';

        if($plat == 'android')
        {
            // 安卓 - 打开指定页
            if($pushData['type'] == 'open_url')
            {
                $push_url .= '&payload=' . $pushData['app_url'];
            }
            else
            {
                // 唤起APP
                $push_url .= '&extra.notify_effect=1';
            }

        }
        else
        {
            // IOS - 打开指定页
            if($pushData['type'] == 'open_url')
            {
                $push_url .= '&extra.action=' . 'openURLWithToken';
                $push_url .= '&extra.url=' . $pushData['ios_url'];
                $push_url .= '&extra.token=' . '';
                $push_url .= '&extra.title=' . urlencode($pushData['title']);
                $push_url .= '&extra.result=' . urlencode($pushData['description']);
            }
            else
            {
                // 唤起APP
                $push_url .= '&extra.notify_effect=1';
            }
            
            // 高版本新增标题
            $push_url .= '&aps_proper_fields.title=' . urlencode($pushData['title']);
            $push_url .= '&aps_proper_fields.body=' . urlencode($pushData['description']);
        }
        return $push_url;
    }

    // 根据用户最近登录渠道推送
    public function getPlatByUser($uid)
    {
        // 测试环境部分用户全推方便测试
        if(ENVIRONMENT !== 'production' && $uid < '35')
        {
            return $this->pushConfig;
        }
        
        $platData = array();
        $uinfo = $this->CI->user_model->getUserInfo($uid);
        if(!empty($uinfo) && !empty($uinfo['last_login_channel']))
        {
            // 获取渠道对应主包
            $channelInfo = $this->CI->channel_model->getChannelInfo($uinfo['last_login_channel']);
            if(!empty($channelInfo))
            {
                // 映射关系
                $platform = ($channelInfo['package'] <= 2) ? $channelInfo['package'] - 1 : $channelInfo['package'];
                if(!empty($this->pushConfig[$platform]))
                {
                    $platData[$platform] = $this->pushConfig[$platform];
                }
            }
        }
        return $platData;
    }
}