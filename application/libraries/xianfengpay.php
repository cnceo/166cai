<?php

/**
 * 先锋单笔实时接口.
 */
class XianFengPay
{

    public function __construct()
    {
        
    }

    public function withdraw($params)
    {
        $data = json_encode(array(
            'merchantNo' => $params['merchantNo'],
            'source' => $params['source'],
            'amount' => $params['amount'],
            'transCur' => $params['transCur'],
            'userType' => $params['userType'],
            'accountNo' => $params['accountNo'],
            'accountName' => $params['accountName'],
            'bankNo' => $params['bankNo'],
            'noticeUrl' => $params['noticeUrl'],
            'memo' => '166彩票提现'
        ));
        $params['reqSn'] = $this->createUnRepeatCode($params['merchantId'], $params['service'], $params['merchantNo']);
        $data = $this->encrypt($data, $params['key']);
        $param = array(
            'data' => $data,
            'merchantId' => $params['merchantId'],
            'reqSn' => $params['reqSn'],
            'secId' => $params['secId'],
            'service' => $params['service'],
            'version' => $params['version']
        );
        $md5sign = $this->createMd5Sign($param);
        $sign = $this->rsa_encrypt($md5sign, $params['key']);
        $posts = array(
            'service' => $params['service'],
            'secId' => $params['secId'],
            'version' => $params['version'],
            'reqSn' => $params['reqSn'],
            'merchantId' => $params['merchantId'],
            'data' => $data,
            'sign' => $sign,
        );
        $res = $this->postUrl($params['url'], $posts);
        //$res = "K7Sl2rxIkQhjvThz7ZqJayy0fBrbRQReyWnieSQZK3oe9o23u+3HBuRMCKNbCyH1iiphwqbViQGKtQkmy2UlN66LwBjZZl8slVuADVlXLN0TkcmF5AWrcs8zYQ4pLjiEbTYrf3BM1j/wZDRVnk+MNa7QE8sDyWjJmIpXagvf8SH1p73DLMSvXcRVBtalFUx0fbOF8sYEm8Ifg25nid+PDnA7Ui3Mex2kXoq47rFpIFlJLc/FB1lyWMRIZNXkacAta3NdvtIxskePb1HkVTUzpw==";
        $res = $this->aes_decrypt($res, $params['key']);
        if ($res) {
            $result = json_decode($res, true);
        } else {
            $result = array();
        }
        return $result;
    }

    private function encrypt($input, $key)
    {
        $md5key = strtoupper(md5($key));
        $key = hex2bin($md5key);
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5_pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }

    private function aes_decrypt($encrypted, $key)
    {
        $md5key = strtoupper(md5($key));
        $key = $this->hexTobin($md5key);
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_ECB);
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    private function pkcs5_pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    private function decrypt($sStr, $sKey)
    {
        $sStr = $this->hex2bins($sStr);
        $decrypted = mcrypt_decrypt(
                MCRYPT_RIJNDAEL_128, $sKey, $sStr, MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    private function hex2bins($str)
    {
        $len = strlen($str) / 2;
        $re = '';
        for ($i = 0; $i < $len; $i++) {
            $pos = $i * 2;
            $re .= chr(hexdec(substr($str, $pos, 1)) << 4) | chr(hexdec(substr($str, $pos + 1, 1)));
        }
        return $re;
    }

    private function createMd5Sign($params)
    {
        $signPars = "";
        ksort($params);
        foreach ($params as $k => $v) {
            if ("" != $v && "sign" != $k) {
                $signPars .= $k . "=" . $v . "&";
            }
        }
        $signPars = substr($signPars, 0, count($signPars) - 2);
        $sign = md5($signPars);
        return strtolower($sign);
    }

    private function createUnRepeatCode($merchantId, $service, $merchantNo)
    {
        $reqSn = "";
        if (is_null($merchantId) || (empty($merchantId)))
            return "";
        if (is_null($service) || (empty($service)))
            return "";
        if (is_null($merchantNo) || (empty($merchantNo))) {
            $merchantNo = $this->getMillisecond();
        }
        $randomVal = $this->getUuid();
        $reqSn = $merchantId . $service . $merchantNo . $randomVal;
        return strtoupper(md5($reqSn));
    }

    private function getUuid()
    {
        mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.随便数播种，4.2.0以后不需要了。
        $charid = strtoupper(md5(uniqid(rand(), true))); //根据当前时间（微秒计）生成唯一id.
        $hyphen = chr(45); // "-"
        $uuid = '' . substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        return $uuid;
    }

    private function getMillisecond()
    {
        list($usec, $sec) = explode(' ', microtime());
        return $sec . ceil(($usec * 1000));
    }

    private function getMicrosecond()
    {
        list($usec, $sec) = explode(" ", microtime());
        $millisecond = round($usec * 1000);
        $millisecond = str_pad($millisecond, 3, '0', STR_PAD_RIGHT);
        return date("YmdHis") . $millisecond;
    }

    private function rsa_encrypt($originalData, $publicKey)
    {
        $encryptData = '';
        $pem = chunk_split($publicKey, 64, "\n"); //转换为pem格式的私钥
        $pem = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
        openssl_public_encrypt($originalData, $encryptData, $pem);
        return base64_encode($encryptData);
    }

    private function postUrl($url, $send_data)
    {
        $TIMEOUT = 15; //超时时间(秒)
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $TIMEOUT);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT - 2);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 不验证证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 不验证HOST
        curl_setopt($ch, CURLOPT_SSLVERSION, 1); // http://php.net/manual/en/function.curl-setopt.php页面搜CURL_SSLVERSION_TLSv1
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type:application/x-www-form-urlencoded;charset=UTF-8',
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }

    public function queryWithdraw($params)
    {
        $params['reqSn'] = $this->createUnRepeatCode($params['merchantId'], $params['service'], $params['merchantNo']);
        $signArray = array(
            "service" => $params['service'],
            "secId" => $params['secId'],
            "version" => $params['version'],
            "reqSn" => $params['reqSn'],
            "merchantId" => $params['merchantId'],
            "merchantNo" => $params['merchantNo']
        );
        $md5sign = $this->createMd5Sign($signArray);
        $sign = $this->rsa_encrypt($md5sign, $params['key']);
        $posts = array(
            'service' => $params['service'],
            'secId' => $params['secId'],
            'version' => $params['version'],
            'reqSn' => $params['reqSn'],
            'merchantId' => $params['merchantId'],
            "merchantNo" => $params['merchantNo'],
            'sign' => $sign,
        );
        $res = $this->postUrl($params['url'], $posts);
        $res = $this->aes_decrypt($res, $params['key']);
        if ($res) {
            $result = json_decode($res, true);
        } else {
            $result = array();
        }
        return $result;
    }
    
    public function notice($data, $key)
    {
        $dataDecrypted = $this->aes_decrypt($data, $key);
        $dataArray = json_decode($dataDecrypted, true);
        return $dataArray;
    }
    
    private function hexTobin($hexstr)
    {
        $n = strlen($hexstr);
        $sbin = "";
        $i = 0;
        while ($i < $n) {
            $a = substr($hexstr, $i, 2);
            $c = pack("H*", $a);
            if ($i == 0) {
                $sbin = $c;
            } else {
                $sbin .= $c;
            }

            $i += 2;
        }

        return $sbin;
    }

}
