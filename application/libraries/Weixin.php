<?php

/*
 * 微信公共平台接入
 * @date:2016-08-03
 */

class Weixin
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->driver('cache', array('adapter' => 'redis'));
    }

    /*
    * 公众号配置
    * @date:2016-08-03
    */
    private $appId = 'wx025339b960f9fb5a';
    private $appSecret = '07d742942e29bded1f0f9d153aa19434';

    /*
    * 主函数 - 获取微信配置
    * @date:2016-08-03
    */
    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId"     => $this->appId,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );

        return $signPackage;
    }

    /*
    * 获取微信API证明
    * @date:2016-08-03
    */
    public function getJsApiTicket()
    {   
        // jsapi_ticket 应该全局存储与更新
        $data = $this->getTicketInfo();

        if($data['expire_time'] < time())
        {
            // 重新获取
            $accessToken = $this->getAccessToken();

            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
            $res = json_decode($this->httpGet($url), true);

            $ticket = $res['ticket'];

            if($ticket)
            {
                $data = array(
                    'expire_time'   =>  time() + 7000,
                    'jsapi_ticket'  =>  $ticket,
                );
                $this->saveTicketInfo($data);
            }
        }
        else
        {
            $ticket = $data['jsapi_ticket'];
        }
        return $ticket;
    }

    /*
    * 获取jsapi_ticket
    * @date:2016-08-03
    */
    public function getTicketInfo()
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['WEIXIN_TICKET']}$this->appId";
        $info = $this->CI->cache->redis->hGetAll($ukey);

        if(empty($info))
        {
            $info = array(
                'jsapi_ticket'  => '',
                'expire_time'   => 0,
            );
        }
        return $info;
    }

    /*
    * 保存jsapi_ticket
    * @date:2016-08-03
    */
    public function saveTicketInfo($data)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['WEIXIN_TICKET']}$this->appId";
        $this->CI->cache->redis->hMSet($ukey, $data);
    }

    /*
    * 获取微信token证明
    * @date:2016-08-03
    */
    public function getAccessToken()
    {
        // access_token 应该全局存储与更新
        $data = $this->getTokenInfo();

        if($data['expire_time'] < time())
        {
            // 请求重新获取
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $res = json_decode($this->httpGet($url), true);

            $access_token = $res['access_token'];

            if($access_token)
            {
                $data = array(
                    'expire_time'   =>  time() + 7000,
                    'access_token'  =>  $access_token,
                );
                $this->saveTokenInfo($data);
            }  
        }
        else
        {
            $access_token = $data['access_token'];
        }
        return $access_token;
    }

    /*
    * 获取微信请求
    * @date:2016-08-03
    */
    public function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

    /*
    * 获取jsapi_ticket
    * @date:2016-08-03
    */
    public function getTokenInfo()
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['WEIXIN_TOKEN']}$this->appId";
        $info = $this->CI->cache->redis->hGetAll($ukey);
        if(empty($info))
        {
            $info = array(
                'access_token'  => '',
                'expire_time'   => 0,
            );
        }
        return $info;
    }

    /*
    * 保存jsapi_ticket
    * @date:2016-08-03
    */
    public function saveTokenInfo($data)
    {
        $REDIS = $this->CI->config->item('REDIS');
        $ukey = "{$REDIS['WEIXIN_TOKEN']}$this->appId";
        $this->CI->cache->redis->hMSet($ukey, $data);
    }

    /*
    * 获取随机字符串
    * @date:2016-08-03
    */
    public function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) 
        {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}