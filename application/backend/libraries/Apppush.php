<?php

/*
 * APP 推送类
 * @date:2016-05-26
 */

class Apppush
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $url_prefix = $this->CI->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->CI->config->item('domain')]) ? $url_prefix[$this->CI->config->item('domain')] : 'http';
    }

    /*
    * 主函数
    * @date:2016-05-26
    */
	public function index($uid, $pushType, $pushData)
	{
        /*
            $pushData = array(
                'lid'       => '',      // 彩种编号
                'lname'     => '',      // 彩种名称
                'orderId'   => '',      // 订单号
                'money'     => '',      // 税后金额元
                'time'      => '',      // 发起时间
                'trade_no'  => '',      // 交易流水
                'add_money' => '',      // 加奖金额元     
            );
        */
		if(!empty($uid))
        {
            switch ($pushType) 
            {
                // 中奖推送
                case 'win_prize':
                    # code...
                    break;

                // 中奖加奖推送
                case 'win_prize_jj':
                    # code...
                    break;

                // 提现申请推送
                case 'withdraw_succ':
                    $sendData = array(
                        'uid'           =>  $uid,
                        'lid'           =>  '',
                        'title'         =>  '提现通知',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '提交的提款申请已处理，提现金额' . $pushData['money'] . '元，请注意查收。具体到账时间以银行处理时间为准。',
                        'url1'          =>  $this->url_prefix."://{$this->CI->config->item('base_url')}/app/trade/detail/",
                        'url2'          =>  $this->url_prefix."://{$this->CI->config->item('base_url')}/ios/trade/detail/",
                        'token'         =>  $this->strCode(json_encode(array('uid' => $uid, 'tradeNo' => $pushData['trade_no'])), 'ENCODE'),
                        'action'        =>  'openURLWithToken',
                    );
                    break;

                // 提现失败推送
                case 'withdraw_fail':
                    $sendData = array(
                        'uid'           =>  $uid,
                        'lid'           =>  '',
                        'title'         =>  '提现失败',
                        'description'   =>  '您于' . date('m月d日H时i分', strtotime($pushData['time'])) . '申请提现未完成，' . $pushData['content'] . '，提现金额已返还您的彩票帐户，您可确认后重新申请提现，点击查看详情。',
                        'url1'          =>  $this->url_prefix."://{$this->CI->config->item('base_url')}/app/trade/detail/",
                        'url2'          =>  $this->url_prefix."://{$this->CI->config->item('base_url')}/ios/trade/detail/",
                        'token'         =>  $this->strCode(json_encode(array('uid' => $uid, 'tradeNo' => $pushData['trade_no'])), 'ENCODE'),
                        'action'        =>  'openURLWithToken',
                    );
                    break;

                // 反馈推送
                case 'feedback':
                    $sendData = array(
                        'uid'           =>  $uid,
                        'lid'           =>  '',
                        'title'         =>  '您的反馈有了新回复：',
                        'description'   =>  $pushData['content'],
                        'url1'          =>  '',
                        'url2'          =>  '',
                        'token'         =>  '',
                        'intent_uri1'   =>  'intent:#Intent;component=com.caipiao166/.setting.feedback.UserFeedbackActivity;end',
                        'action'        =>  'openAPP',
                        'path'          =>  'feedback',
                    );
                    break;
                
                // 追号完成推送
                case 'chase_complete':
                    # code...
                    break;
                
                // 部分出票失败推送
                case 'order_drawpart':
                    # code...
                    break;
                
                // 出票失败推送
                case 'order_concel':
                    # code...
                    break;

                // 投注失败推送
                case 'lottery_fail':
                    # code...
                    break;

                default:
                    $sendData = array();
                    break;
            }

            if(!empty($sendData))
            {
                $this->pushByAndroid($sendData);
                $this->pushByIos($sendData);
                // IOS马甲版
                $this->pushByIos01($sendData);
            }
        }
	}

    /*
     * 加密解密公共函数
     * @date:2016-01-18
     */
    public function strCode ( $str , $action = 'DECODE' )
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

    // 安卓推送
    public function pushByAndroid($pushData = '')
    {
        /*
        $pushData = array(
            'uid' => '1',
            'title' => '这里是标题',
            'description' => '这里是描述',
            // 'url1' => 'http://www.166cai.com/app/order/detail/20160621134327785496/',
            'url1' => 'http://www.166cai.com/app/trade/detail/',
            'token' => 'SkQTCF0XWxJVVgRGGxtDQFJQUisOQAobVAFVBFUDAFUAUlJVCgBWCVZVDVEVRA==',
            'lid' => '42'
        );
        */

        // 测试环境限制推送
        if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35')
        {
            return true;
        }

        // KEY
        $parmas = array(
            'appSecret' => 'pnYQnu3N82RIS95iC1hxug==',
        );

        $postData['MIPUSHJSON'] = $parmas;

        $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';    // 推送接口
        $push_url .= '?user_account=' . $pushData['uid'];                       // 指定用户
        $push_url .= '&description=' . urlencode($pushData['description']);     // 内容
        $push_url .= '&payload=' . '';                                          // 打开地址
        $push_url .= '&restricted_package_name=com.caipiao166';                 // 包名     
        $push_url .= '&title=' . urlencode($pushData['title']);                 // 标题
        $push_url .= '&notify_type=2';                                          // 消息类型
        $push_url .= '&notify_id=' . date('mdHis', time());
        $push_url .= '&time_to_live=1000';                                      // 有效时间
        $push_url .= '&pass_through=0';                                         // 透传
        
        if(!empty($pushData['intent_uri1']))
        {
            // 原生推送
            $push_url .= '&extra.notify_effect=2';  
            $push_url .= '&extra.intent_uri=' . urlencode($pushData['intent_uri1']);
        }
        else
        {
            // web推送
            $push_url .= '&extra.lid=' . $pushData['lid'];                          // 彩种ID
            $push_url .= '&extra.url=' . $pushData['url1'];                         // 点击链接地址
            $push_url .= '&extra.token=' . $pushData['token'];                      // 用户信息token     
        }

        $pushResponse = $this->CI->tools->request($push_url, $postData);
        $pushResponse = json_decode($pushResponse, true);
        return $pushResponse;
    }

    // IOS推送
    public function pushByIos($pushData = '')
    {
        // 测试环境限制推送
        if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35')
        {
            return true;
        }

        // KEY
        $parmas = array(
            'appSecret' => 'dEDI0cjQeFF8LVbZTsl+/A==',
        );

        $postData['MIPUSHJSON'] = $parmas;

        if(ENVIRONMENT == 'production')
        {
            $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
        }
        else
        {
            $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
            // $push_url = 'https://sandbox.xmpush.xiaomi.com/v2/message/user_account';
        }
        $push_url .= '?user_account=' . $pushData['uid'];                       // 指定用户
        $push_url .= '&description=' . urlencode($pushData['description']);     // 内容
        $push_url .= '&payload=' . '';                                          // 打开地址
        $push_url .= '&restricted_package_name=com.166cai.lottery';             // 包名     
        $push_url .= '&title=' . urlencode($pushData['title']);                 // 标题
        $push_url .= '&notify_type=2';                                          // 消息类型
        $push_url .= '&notify_id=' . date('mdHis', time());
        $push_url .= '&time_to_live=1000';                                      // 有效时间
        $push_url .= '&pass_through=0';                                         // 透传
        $push_url .= '&extra.action=' . $pushData['action'];                    // APP推送类型
        $push_url .= '&extra.title=' . urlencode($pushData['title']);           // APP内标题
        $push_url .= '&extra.result=' . urlencode($pushData['description']);    // APP内内容

        if(!empty($pushData['path']))
        {
            // 原生推送 
            $push_url .= '&extra.path=' . $pushData['path'];
        }
        else
        {
            // web推送
            $push_url .= '&extra.lid=' . $pushData['lid'];                          // 彩种ID
            $push_url .= '&extra.url=' . $pushData['url2'];                         // 点击链接地址
            $push_url .= '&extra.token=' . $pushData['token'];                      // 用户信息token     
        }
        // log_message('LOG', "请求地址: " . $push_url, 'pushByIos');
        $pushResponse = $this->CI->tools->request($push_url, $postData);
        $pushResponse = json_decode($pushResponse, true);
        // log_message('LOG', "请求返回: " . json_encode($pushResponse), 'pushByIos');
        return $pushResponse;
    }

    // IOS推送
    public function pushByIos01($pushData = '')
    {
        // 测试环境限制推送
        if(ENVIRONMENT !== 'production' && $pushData['uid'] >= '35')
        {
            return true;
        }

        // KEY
        $parmas = array(
            'appSecret' => 'sn+A1M/KwrffNBUjSjTGdw==',
        );

        $postData['MIPUSHJSON'] = $parmas;

        if(ENVIRONMENT == 'production')
        {
            $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
        }
        else
        {
            $push_url = 'https://api.xmpush.xiaomi.com/v2/message/user_account';
        }
        $push_url .= '?user_account=' . $pushData['uid'];                       // 指定用户
        $push_url .= '&description=' . urlencode($pushData['description']);     // 内容
        $push_url .= '&payload=' . '';                                          // 打开地址
        $push_url .= '&restricted_package_name=com.166cai.ssq';             // 包名     
        $push_url .= '&title=' . urlencode($pushData['title']);                 // 标题
        $push_url .= '&notify_type=2';                                          // 消息类型
        $push_url .= '&notify_id=' . date('mdHis', time());
        $push_url .= '&time_to_live=1000';                                      // 有效时间
        $push_url .= '&pass_through=0';                                         // 透传
        $push_url .= '&extra.action=' . $pushData['action'];                    // APP推送类型
        $push_url .= '&extra.title=' . urlencode($pushData['title']);           // APP内标题
        $push_url .= '&extra.result=' . urlencode($pushData['description']);    // APP内内容

        if(!empty($pushData['path']))
        {
            // 原生推送 
            $push_url .= '&extra.path=' . $pushData['path'];
        }
        else
        {
            // web推送
            $push_url .= '&extra.lid=' . $pushData['lid'];                          // 彩种ID
            $push_url .= '&extra.url=' . $pushData['url2'];                         // 点击链接地址
            $push_url .= '&extra.token=' . $pushData['token'];                      // 用户信息token     
        }
        $pushResponse = $this->CI->tools->request($push_url, $postData);
        $pushResponse = json_decode($pushResponse, true);
        return $pushResponse;
    }

    // 调试
    public function test()
    {
        // 安卓推送
        if(ENVIRONMENT == 'production')
        {
            $pushResponse = $this->pushByAndroid();
        }

        var_dump($pushResponse);die;
    }
}