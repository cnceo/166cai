<?php
/**
 * 现在支付宝
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class XzPay extends RechargeAbstract
{
    private $sort;

    private $encode;

    private $config;

    // 默认配置
    private static $default = array(
        'TRADE_URL' => "https://pay.ipaynow.cn",
        'QUERY_URL' => "https://pay.ipaynow.cn",
        'TRADE_FUNCODE' => "WP001",
        'QUERY_FUNCODE' => "MQ002",
        'NOTIFY_FUNCODE' => "N001",
        'FRONT_NOTIFY_FUNCODE' => "N002",
        'TRADE_TYPE' => "01",
        'TRADE_CURRENCYTYPE' => "156",
        'TRADE_CHARSET' => "UTF-8",
        'TRADE_DEVICE_TYPE' => "06",
        'TRADE_SIGN_TYPE' => "MD5",
        'TRADE_QSTRING_EQUAL' => "=",
        'TRADE_QSTRING_SPLIT' => "&",
        'TRADE_FUNCODE_KEY' => "funcode",
        'TRADE_DEVICETYPE_KEY' => "deviceType",
        'TRADE_SIGNTYPE_KEY' => "mhtSignType",
        'TRADE_SIGNATURE_KEY' => "mhtSignature",
        'SIGNATURE_KEY' => "signature",
        'SIGNTYPE_KEY' => "signType",
        'VERIFY_HTTPS_CERT' => false,
    );

 	public function __construct($config = array())
 	{
        $this->config = $config;
        $this->secureKey = $config['secure_key'];
 	}

    public function requestHttp($params)
    {
        $returnData = array(
            'code' => false,
            'msg'  => '请求错误',
            'data' => $params,
        );        
        //组合参数

        $defaultParams = array(
            'mhtOrderAmt'       =>  $params['money'],
            'mhtOrderNo'        =>  $params['trade_no'],
            'notifyUrl'         =>  $this->config['notifyUrl'],
            'frontNotifyUrl'    =>  $this->config['frontNotifyUrl'],
            'mhtOrderName'      =>  $this->config['mhtOrderName'],
            'mhtOrderDetail'    =>  $this->config['mhtOrderDetail'],
            'appId'             =>  $this->config['appId'],
            'mhtOrderStartTime' =>  date("YmdHis"),
            'mhtReserved'       =>  '166cai',   
            'payChannelType'    =>  $this->config['payChannelType'],
        );

        $respData = array();
        $respData["funcode"] = self::$default['TRADE_FUNCODE'];      //功能码
        $respData["mhtOrderType"] = self::$default['TRADE_TYPE'];    //商户交易类型
        $respData["mhtCurrencyType"] = self::$default['TRADE_CURRENCYTYPE']; //商户订单币种类型
        $respData["mhtCharset"] = self::$default['TRADE_CHARSET'];           //商户字符编码
        $respData["deviceType"] = self::$default['TRADE_DEVICE_TYPE'];       //设备类型
        $respData = array_merge($respData, $defaultParams);
        $respData["mhtSignature"] = $this->buildSignature($respData, $this->secureKey); //商户数据签名
        $respData["mhtSignType"] = self::$default['TRADE_SIGN_TYPE'];        //商户签名方法

        $req_str = $this->buildReq($respData);

        $payUrl = self::$default['TRADE_URL'] . "?" . $req_str;

        /*
        $resp_str = $this->sendMessage($req_str, self::$default['TRADE_URL']);
        $reg  = '<form\s+name.*?punchout_form.*?action\s*=\s*["\'](.*?)["\'].*?>.*?';
        $reg .= '<input.*?name.*?biz_content.*?value\s*=\s*["\'](.*?)["\'].*?>';
        $res = preg_match("/$reg/is", $resp_str, $matches);
        */

        if($payUrl)
        {
            $response['action'] = $payUrl; // $matches[1];
            $response['biz_content'] = ''; // $matches[2];
        }
        else
        {
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求现在支付宝appId：'. $this->config['appId'] . '付款信息时失败，请及时留意。<br/>请求地址：' . $payUrl . '<br/>请求参数数据：' . $this->printJson($params);
            $this->alertEmail($message);  
            $this->log($returnData, __CLASS__, $this->config['appId'], __FUNCTION__); 

            $returnData = array(
                'code' => false,
                'msg'  => '请求错误',
                'data' => $params,
            ); 
            return $returnData;
        }

        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => $response,
        );

        return $returnData;    
    }
    
    /**
     * 充值异步通知
     * @see RechargeAbstract::notify()
     */
    public function notify()
    {
        $returnData = array(
            'code' => false,
            'errMsg' => 'failure',
            'succMsg' => 'success=Y',
            'data' => array(),
        );
        
        $request = file_get_contents('php://input');
        $result = $this->verifyNotify($request, $this->secureKey);

        if($result['status'])
        {
            $resParams = $result['data'];
            if($resParams['tradeStatus'] != "" && $resParams['tradeStatus'] == "A001")
            {
                $payData = array(
                    'trade_no' => $resParams['mhtOrderNo'],
                    'pay_trade_no' => $resParams['nowPayOrderNo'],
                    'status' => '1',    // 成功
                );

                $returnData = array(
                    'code' => true,
                    'errMsg' => 'failure',
                    'succMsg' => 'success=Y',
                    'data' => $payData,
                );
            }
            else
            {
                // err_code err_msg
                // TODO 交易异常 这里记录错误日志或邮件报警
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>现在支付商户号：'. $this->config['appId'] . '异步通知接口交易异常，请及时留意。';
                // $this->alertEmail($message);
                $this->log($resParams, __CLASS__, $this->config['appId'], __FUNCTION__);
            }   
        }
        else
        {
            // TODO 验签失败 这里记录错误日志或邮件报警
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>现在支付商户号：'. $this->config['appId'] . '异步通知接口验签失败，请及时留意。<br/>通知返回数据：' . $this->printJson($resParams);
            $this->alertEmail($message);
            $this->log($resParams, __CLASS__, $this->config['appId'], __FUNCTION__);
        }   
        return $returnData;
    }

    /**
     * 充值同步通知
     * @see RechargeAbstract::syncCallback()
     */
    public function syncCallback()
    {
        $returnData = array(
            'code' => false,
            'errMsg' => 'failure',
            'succMsg' => 'success',
            'data' => array(),
        );

        $request = $_GET;
        $request['appId'] = $this->config['appId'];//为了防止验签失败
        if($request['orderId'])
        {
            unset($request['orderId']);
            unset($request['orderType']);
            unset($request['buyMoney']);
        }
        if($request)
        {
            if($this->verifySignature($request, $this->secureKey))
            {
                $payData = array(
                    'trade_no' => $request['mhtOrderNo'],
                );

                $returnData = array(
                    'code' => true,
                    'errMsg' => 'failure',
                    'succMsg' => 'success',
                    'data' => $payData,
                );
            }
            else
            {
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>现在支付商户号：'. $this->config['appId'] . '同步通知接口验签失败，请及时留意。<br/>通知返回数据：' . $this->printJson($request);
                $this->alertEmail($message);
                $this->log($request, __CLASS__, $this->config['appId'], __FUNCTION__);
            }
        }
        return $returnData;
    }
    
    public function formSubmit($params)
    {
        
    }
    
    public function queryOrder($params)
    {
        $returnData = array(
            'code' => false,
            'msg'  => '操作失败',
            'data' => array(),
        );

        $resParams = array(
            'appId' => $this->config['appId'],
            'trade_no' => $params['trade_no'],
            'secureKey' => $this->secureKey
        );

        $respone = $this->queryOrderInfo($resParams);
        if(!empty($respone))
        {
            $pstatus = array(
                'A001' => '订单支付成功', 
                'A002' => '订单支付失败', 
                'A003' => '支付结果未知',
                'A004' => '订单受理成功，处理中',
                'A005' => '订单受理失败',
            );

            $payData = array(
                'code' => '0',
                'ptype' => '现在支付宝H5',
                'pstatus' => $pstatus[$respone['transStatus']],
                'ptime' => ($respone['responseCode'] == 'A001' && $respone['transStatus'] == 'A001') ? date('Y-m-d H:i:s', $respone['mhtOrderStartTime']) : '',
                'pmoney' => number_format($respone['mhtOrderAmt'] / 100, 2, ".", ","),
                'pbank' => '',
                'ispay' => ($respone['responseCode'] == 'A001' && $respone['transStatus'] == 'A001') ? true : false,
                'pay_trade_no' => $respone['nowPayOrderNo'],
            );

            $returnData = array(
                'code' => true,
                'msg' => '操作成功',
                'data' => $payData,
            );
        }
        return $returnData;
    }
    
    public function refundSubmit($params)
    {
        
    }
    
    public function queryRefund($params)
    {
        
    }

    public function buildSignature($params = array(), $secureKey)
    {
        $filteredReq = $this->paraFilter($params);
        return $this->buildCoreSignature($filteredReq, $secureKey);
    }

    public function paraFilter($params = array())
    {
        $result = array();
        $flag = $params[self::$default['TRADE_FUNCODE_KEY']];
        foreach($params as $key => $value)
        {
            if(($flag == self::$default['TRADE_FUNCODE']) && !($key == self::$default['TRADE_FUNCODE_KEY'] || $key == self::$default['TRADE_DEVICETYPE_KEY'] || $key == self::$default['TRADE_SIGNTYPE_KEY'] || $key == self::$default['TRADE_SIGNATURE_KEY']))
            {
                $result[$key] = $value;
                continue;
            }
            if(($flag == self::$default['NOTIFY_FUNCODE'] || $flag == self::$default['FRONT_NOTIFY_FUNCODE'])&& !($key == self::$default['SIGNTYPE_KEY'] || $key == self::$default['SIGNATURE_KEY']))
            {
                $result[$key] = $value;
                continue;
            }
            if (($flag == self::$default['QUERY_FUNCODE']) && !($key == self::$default['TRADE_SIGNTYPE_KEY'] || $key == self::$default['TRADE_SIGNATURE_KEY'] || $key == self::$default['SIGNTYPE_KEY'] || $key == self::$default['SIGNATURE_KEY'])) 
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
        $prestr .= self::$default['TRADE_QSTRING_SPLIT'] . md5($secureKey);
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
            $linkStr .= $key . self::$default['TRADE_QSTRING_EQUAL'] . $value . self::$default['TRADE_QSTRING_SPLIT'];
        }
        $linkStr = substr($linkStr, 0, count($linkStr)-2);
        return $linkStr;
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
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_HEADER => 0,
                CURLOPT_SSL_VERIFYPEER => self::$default['VERIFY_HTTPS_CERT'],
                CURLOPT_SSL_VERIFYHOST => self::$default['VERIFY_HTTPS_CERT']
            );
            curl_setopt_array($curl, $option);
            $resp_data =  curl_exec($curl);
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
    public function verifyNotify($request, $secureKey)
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
        $respSignature = $para[self::$default['SIGNATURE_KEY']];
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
    public function queryOrderInfo($params)
    {
        $req = array();
        $req["funcode"] = self::$default['QUERY_FUNCODE'];
        $req["appId"] = $params['appId'];
        $req["mhtOrderNo"] = $params['trade_no'];
        $req["mhtCharset"] = self::$default['TRADE_CHARSET'];
        $req["mhtSignature"] = $this->buildSignature($req, $this->secureKey);
        $req["mhtSignType"] = self::$default['TRADE_SIGN_TYPE'];

        $resp=array();
        $this->query($req, $resp, $params['secureKey']);
        return $resp;
    }

    public function query($params = array(), &$resp = array(), $secureKey) 
    {
        $req_str = $this->buildReq($params);
        $resp_str = $this->sendMessage($req_str, self::$default['QUERY_URL']);
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