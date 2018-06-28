<?php

define('XIANZAIPAY_PATH', dirname(__FILE__));
require_once XIANZAIPAY_PATH . '/conf/Config.php';

class XianZaiPay 
{
	private $CI;
 	public function __construct()
 	{
        $this->CI = &get_instance();
 	}
 	
    // 请求支付
 	public function pay($datas, $secureKey)
 	{
		$req = array();
        $req["funcode"] = Config::TRADE_FUNCODE;                        //功能码
        $req["mhtOrderType"] = Config::TRADE_TYPE;                      //商户交易类型
        $req["mhtCurrencyType"] = Config::TRADE_CURRENCYTYPE;           //商户订单币种类型
        $req["mhtCharset"] = Config::TRADE_CHARSET;                     //商户字符编码
        $req["deviceType"] = Config::TRADE_DEVICE_TYPE;                 //设备类型
        $req = array_merge($req, $datas);
        $req["mhtSignature"] = $this->buildSignature($req, $secureKey); //商户数据签名
        $req["mhtSignType"] = Config::TRADE_SIGN_TYPE;                  //商户签名方法

        return $this->trade($req);
        // $req_str = $this->trade($req);
        // header("Location:".Config::TRADE_URL."?".$req_str);
 	}

    public function trade($params = array())
    {
        $req_str = $this->buildReq($params);
        $resp_str = $this->sendMessage($req_str, Config::TRADE_URL);
        $reg  = '<form\s+name.*?punchout_form.*?action\s*=\s*["\'](.*?)["\'].*?>.*?';
        $reg .= '<input.*?name.*?biz_content.*?value\s*=\s*["\'](.*?)["\'].*?>';
        $res = preg_match("/$reg/is", $resp_str, $matches);
        if($res)
        {
            $response['action'] = $matches[1];
            $response['biz_content'] = $matches[2];
            return $response;
        }
        return $res;
    }

    // 加签
    public function buildSignature($params = array(), $secureKey)
    {
        $filteredReq = $this->paraFilter($params);
        return $this->buildCoreSignature($filteredReq, $secureKey);
    }

    public function paraFilter($params = array())
    {
        $result = array();
        $flag = $params[Config::TRADE_FUNCODE_KEY];
        foreach($params as $key => $value)
        {
            if(($flag == Config::TRADE_FUNCODE) && !($key == Config::TRADE_FUNCODE_KEY || $key == Config::TRADE_DEVICETYPE_KEY || $key == Config::TRADE_SIGNTYPE_KEY || $key == Config::TRADE_SIGNATURE_KEY))
            {
                $result[$key] = $value;
                continue;
            }
            if(($flag == Config::NOTIFY_FUNCODE || $flag == Config::FRONT_NOTIFY_FUNCODE)&& !($key == Config::SIGNTYPE_KEY || $key == Config::SIGNATURE_KEY))
            {
                $result[$key] = $value;
                continue;
            }
            if (($flag == Config::QUERY_FUNCODE) && !($key == Config::TRADE_SIGNTYPE_KEY|| $key == Config::TRADE_SIGNATURE_KEY || $key == Config::SIGNTYPE_KEY || $key == Config::SIGNATURE_KEY)) 
            {
                $result[$key] = $value;
                continue;
            }
        }
        return $result;
    }

    public function buildCoreSignature($para = array(), $secureKey)
    {
        $prestr = $this->createLinkString($para, true, false);
        $prestr .= Config::TRADE_QSTRING_SPLIT . md5($secureKey);
        return md5($prestr);
    }

    public function createLinkString($para = array(), $sort, $encode) 
    {
        if($sort) 
        {
            $para = $this->argSort($para);
        }
        foreach ($para as $key => $value)
        {
            if ($encode) 
            {
                $value = urlencode($value);
            }
            $linkStr .= $key . Config::TRADE_QSTRING_EQUAL . $value . Config::TRADE_QSTRING_SPLIT;
        }
        $linkStr = substr($linkStr, 0, count($linkStr)-2);
        return $linkStr;
    }

    private function argSort($para) 
    {
        ksort($para);
        reset($para);
        return $para;
    }

    private function buildReq($params = array()) 
    {
        return $this->createLinkString($params, false, true);
    }

    public function sendMessage($req_content, $url)
    {
        if(function_exists("curl_init"))
        {
            $curl = curl_init();
            $option = array(
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $req_content,
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_SSL_VERIFYPEER => Config::VERIFY_HTTPS_CERT,
                CURLOPT_SSL_VERIFYHOST => Config::VERIFY_HTTPS_CERT
            );
            curl_setopt_array($curl, $option);
            $resp_data=  curl_exec($curl);
            if($resp_data==FALSE)
            {
                curl_close($curl);
            }
            else
            {
                curl_close($curl);
                return $resp_data;
            }
        }
        
    }

    // 回调
    public function notify($request, $secureKey)
    {
        parse_str($request, $request_form);
        if ($this->verifySignature($request_form, $secureKey))
        {
            // 验证签名成功
            $result = array(
                'status' => true,
                'data' => $request_form
            );
        } 
        else 
        {
            // 验证签名失败
            $result = array(
                'status' => false,
                'data' => $request_form
            );
        }
        return $result;
    }

    // 验签
    public function verifySignature($para, $secureKey)
    {
        $respSignature = $para[Config::SIGNATURE_KEY];
        $filteredReq = $this->paraFilter($para);
        $signature = $this->buildCoreSignature($filteredReq, $secureKey);
        if ($respSignature != "" && $respSignature == $signature) 
        {
            return TRUE;
        }
        else 
        {
            return FALSE;
        }
    }

    // 查询
    public function queryOrder($params)
    {
        $req = array();
        $req["funcode"] = Config::QUERY_FUNCODE;
        $req["appId"] = $params['appId'];
        $req["mhtOrderNo"] = $params['trade_no'];
        $req["mhtCharset"] = Config::TRADE_CHARSET;
        $req["mhtSignature"] = $this->buildSignature($req, $params['secureKey']);
        $req["mhtSignType"] = Config::TRADE_SIGN_TYPE;

        $resp=array();
        $this->query($req, $resp, $params['secureKey']);
        return $resp;
    }

    public function query($params = array(), &$resp = array(), $secureKey) 
    {
        $req_str = $this->buildReq($params);
        $resp_str = $this->sendMessage($req_str, Config::QUERY_URL);
        return $this->verifyResponse($resp_str, $resp, $secureKey);
    }

    public function verifyResponse($resp_str, &$resp, $secureKey)
    {
        if ($resp_str != "") 
        {
            parse_str($resp_str, $para);
            $signIsValid = $this->verifySignature($para, $secureKey);
            $resp = $para;
            if($signIsValid) 
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
    }
}