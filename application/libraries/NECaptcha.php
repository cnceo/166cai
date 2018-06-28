<?php
/**
 * 易盾验证码二次校验SDK
 * @author yangweiqiang
 */
class NECaptcha {
    
    private $_VERSION = 'v2';
    private $_TIMEOUT = '5';
    private $_URL = 'http://c.dun.163yun.com/api/v2/verify';
    private $captcha_id = '5bd03d0922574cc9a4ecab373d13b88b';
    private $secret_id = '72c2ba636670a95ab65ebbe7833901a9';
    private $secret_key = '365396267ac9243560d5475c837832a0';
    private $secret_pair;
    private $CI;
    /**
     * 构造函数
     * @param $captcha_id 验证码id
     * @param $secret_pair 密钥对
     */
    public function __construct() {
        $this->CI = &get_instance();
    }

    /**
     * 发起二次校验请求
     * @param $validate 二次校验数据
     */
    public function verify($validate) {
        $params = array();
        $params["captchaId"] = $this->captcha_id;
        $params["validate"] = $validate;
        $params["user"] = '';
        // 公共参数
        $params["secretId"] = $this->secret_pair->secret_id;
        $params["version"] = $this->_VERSION;
        $params["timestamp"] = sprintf("%d", round(microtime(true)*1000));// time in milliseconds
        $params["nonce"] = sprintf("%d", rand()); // random int
        $params["signature"] = $this->sign($this->secret_pair->secret_key, $params);

        $result = $this->send_http_request($params);
        if (array_key_exists('msg', $result) && $result['msg'] === 'OK' && $result['result'] == '1'){
            return true;
        }
        return false;
    }

    /**
     * 计算参数签名
     * @param $secret_key 密钥对key
     * @param $params 请求参数
     */
    private function sign($secret_key, $params){
        ksort($params); // 参数排序
        $buff="";
        foreach($params as $key=>$value){
            $buff .=$key;
            $buff .=$value;
        }
        $buff .= $secret_key;
        return md5($buff);
    }

    /**
     * 发送http请求
     * @param $params 请求参数
     */
    private function send_http_request($params){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->_TIMEOUT);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        /*
         * Returns TRUE on success or FALSE on failure. 
         * However, if the CURLOPT_RETURNTRANSFER option is set, it will return the result on success, FALSE on failure.
         */
        log_message('LOG', 'Request:'.json_encode($params), 'necaptcha/');
        $result = curl_exec($ch);
        log_message('LOG', 'Response:'.json_encode($result), 'necaptcha/');
//          var_dump($result);
        
        if(curl_errno($ch)){
            $msg = curl_error($ch);
            curl_close($ch);
            return array("error"=>500, "msg"=>$msg, "result"=>false);
        }else{
            curl_close($ch);
            return json_decode($result, true);  
        }
    }
    
    public function verifier($validate) {
        $this->secret_pair = new SecretPair($this->secret_id, $this->secret_key);
        if(get_magic_quotes_gpc()) $validate = stripcslashes($validate);
        return $this->verify($validate);
    }
}


class SecretPair {
    public $secret_id;
    public $secret_key;

    /**
     * 构造函数
     * @param $secret_id 密钥对id
     * @param $secret_key 密钥对key
     */
    public function __construct($secret_id, $secret_key) {
        $this->secret_id  = $secret_id;
        $this->secret_key = $secret_key;
    }
}
?>