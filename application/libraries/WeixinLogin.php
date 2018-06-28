<?php

/*
 * 微信登录
 * @date:2017-09-01
 */

class WeixinLogin
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $url_prefix = $this->CI->config->item('url_prefix');
        $this->urlprefix = isset($url_prefix[$this->CI->config->item('domain')]) ? $url_prefix[$this->CI->config->item('domain')] : 'http';
    }

    private $snsApi = "https://api.weixin.qq.com/sns/oauth2";

    /*
    * 网站应用配置
    * @date:2017-09-01
    */
    private $config = array(
        'appId'     =>  'wx55ee01e62c0ebb7a',
        'appSecret' =>  'ac52e93d93dc23e7d7e46378e77a7689',
    );

    /*
    * 微信登录 - 二维码扫码登录
    * @date:2017-09-01
    */
    public function qrbLogin()
    {
        // 请求CODE
        $url = "https://open.weixin.qq.com/connect/qrconnect?appid={$this->config['appId']}";
        $url .= "&redirect_uri=" . urlencode($this->urlprefix . ':'. $this->CI->config->item('pages_url') . 'wechat/callback');
        $url .= "&response_type=code&scope=snsapi_login";

        return $url;
    }


    /*
    * 微信登录 - 通过code获取access_token
    * @date:2017-09-01
    */
    public function getAccessToken($code)
    {
        $info = array(
            'access_token'  =>  '',
            'expires_in'    =>  0,
            'refresh_token' =>  '',
            'openid'        =>  '',
            'scope'         =>  '',
            'unionid'       =>  '',
        );

        // 获取access_token
        $url = $this->snsApi . "/access_token?appid=" . $this->config['appId'] . "&secret=" . $this->config['appSecret'] . "&code=" . $code . "&grant_type=authorization_code";
        $res = json_decode($this->httpGet($url), true);

        if($res['unionid'])
        {
            $info = array(
                'access_token'  =>  $res['access_token'],
                'expires_in'    =>  $res['expires_in'],
                'refresh_token' =>  $res['refresh_token'],
                'openid'        =>  $res['openid'],
                'scope'         =>  $res['scope'],
                'unionid'       =>  $res['unionid']
            );
        }
        
        if($res['errcode'])
        {
            // TODO 调用报错
        }

        return $info;
    }

    /*
    * 微信登录 - 获取用户个人信息
    * @date:2017-09-01
    */
    public function getUserinfo($access_token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid;
        $info = json_decode($this->httpGet($url), true);
        if($info['unionid'])
        {
            return $info;
        }

        return array();
    }


    /*
    * 获取微信请求
    * @date:2017-09-01
    */
    public function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        $curl_error = curl_error($curl);
        curl_close($curl);

        return $res;
    }
}