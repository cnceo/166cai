<?php
/**
 * 微众银行支付宝-caika166@126.com 扫码
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class WzPay extends RechargeAbstract
{
    
    const PHP_SDK_VERSION = 'PHP_2.3.11';
    //api version
    const API_VERSION = '2';
    //online
    const URI_BILL = 'rest/bill'; //支付;支付订单查询(指定id)
    const URI_TEST_BILL = 'rest/sandbox/bill';
    const URI_BILLS = 'rest/bills'; //订单查询
    const URI_TEST_BILLS = 'rest/sandbox/bills';
    const URI_BILLS_COUNT = 'rest/bills/count'; //订单总数查询
    const URI_TEST_BILLS_COUNT = 'rest/sandbox/bills/count';
    const URI_BC_GATEWAY_BANKS = 'rest/bc_gateway/banks'; //获取银行列表
    const URI_REFUND = 'rest/refund';       //退款;预退款批量审核;退款订单查询(指定id)
    const URI_REFUNDS = 'rest/refunds';     //退款查询
    const URI_REFUNDS_COUNT = 'rest/refunds/count'; //退款总数查询
    const URI_REFUND_STATUS = 'rest/refund/status'; //退款状态更新
    //offline
    const URI_OFFLINE_BILL = 'rest/offline/bill'; //线下支付-撤销订单
    const URI_OFFLINE_BILL_STATUS = 'rest/offline/bill/status'; //线下订单状态查询
    const URI_OFFLINE_REFUND = 'rest/offline/refund'; //线下退款
    //coupon
    const UNEXPECTED_RESULT = "非预期的返回结果:";
    const NEED_PARAM = "需要必填字段:";
    const NEED_VALID_PARAM = "字段值不合法:";
    const VALID_SIGN_PARAM = 'APP ID, timestamp,APP(Master) Secret参数值均不能为空,请设置';

    private $config;
    private $defaultParams = array();
    private $values = array();
    private $channels = array("ALI","ALI_WEB","ALI_WAP","ALI_QRCODE","ALI_APP","ALI_OFFLINE_QRCODE","UN","UN_WEB","UN_APP","UN_WAP","WX","WX_JSAPI","WX_NATIVE","WX_WAP","WX_APP","WX_MINI","JD","JD_WEB","JD_WAP","JD_B2B","YEE","YEE_WAP","YEE_WEB","YEE_NOBANKCARD","KUAIQIAN","KUAIQIAN_WAP","KUAIQIAN_WEB","BD","BD_WAP","BD_WEB","PAYPAL","PAYPAL_SANDBOX","PAYPAL_LIVE","BC","BC_GATEWAY","BC_EXPRESS","BC_APP","BC_NATIVE","BC_WX_WAP","BC_WX_JSAPI","BC_WX_SCAN","BC_WX_MINI","BC_CARD_CHARGE","BC_ALI_QRCODE","BC_ALI_SCAN","BC_ALI_WAP","BC_ALI_WEB","BC_ALI_JSAPI");
    private static $appId;
    private static $appSecret;
    private static $masterSecret;
    private static $testSecret;
    private static $mode = false;
    private $apiUrl;
    
    /**
     * [__construct 构造方法]
     * @author LiKangJian 2017-10-24
     * @param  array $config [description]
     */
 	public function __construct($config = array())
 	{
        $this->config = $config;
        $this->apiUrl =  $this->config['url'] . '/' . self::API_VERSION . '/';
        $this->appId = $config['appId'];
        $this->appSecret = $config['appSecret'];
        $this->masterSecret = $config['masterSecret'];
        $this->testSecret = $config['testSecret'];
        $this->defaultParams['timestamp'] = time() * 1000;
        $this->defaultParams['channel'] = $config['channel'];
        $this->defaultParams['return_url'] = $config['return_url'];
        $this->defaultParams['notify_url'] = $config['notify_url'];
        $this->defaultParams['title'] = $config['body'];
        
 	}
    /**
     * [formSubmit 表单提交第三方方法]
     * @author LiKangJian 2017-10-20
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function  formSubmit($params)
    {
        $this->defaultParams['total_fee'] = intval($params['money']);
        $this->defaultParams['bill_no'] = $params['trade_no'];
        $this->defaultParams['optional'] = (object)array("trade_no"=>$params['trade_no']);
        $res = $this->bill($this->defaultParams);
        $returnData = array(
            'code' => false,
            'msg' => '请求失败',
            'data' => array('charset' => 'utf-8','html' =>'',array()),
        );
        if($res['resultCode']==0)
        {
            $params['code_url'] = $res['code_url'];
            $returnData['code'] = true;
            $returnData['msg'] = '请求成功';
            $returnData['data'] = array('charset' => 'utf-8','html' => $this->createForm($params),$params);
        }
        return $returnData;
    }
    /**
     * [createForm 构建表单]
     * @author LiKangJian 2017-10-20
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function createForm($params)
    {
        $html =  '<body onLoad="document.autoForm.submit();">';
        $html .= '<form name="autoForm" action="'.$params['submit_url'].'" method="post">';
        $formParams = array();
        $formParams['orderId'] = $params['trade_no'];
        $formParams['orderTime'] = date('YmdHis', time());
        $formParams['txnAmt'] = $params['money'];
        $formParams['codeUrl'] = $params['code_url'];
        foreach ($formParams as $key => $value)
        {

            $html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/><br/>';
            
        }
        $html .= '</form></body>';
        return $html;
    }

    /**
     * [notify 充值异步通知]
     * @author LiKangJian 2017-07-06
     * @return [type] [description]
     */
    public function notify()
    {

        $returnData = array(
            'code' => false,
            'errMsg' => '验签失败',
            'succMsg' => '验签成功',
            'data' => array(),
        );
        $res = json_decode($GLOBALS['HTTP_RAW_POST_DATA'],true);
        if($res['trade_success'] == 1 && $res['tradeSuccess'] == 1)
        {
            $payData = array(
                'trade_no' => $res['messageDetail']['orderId'],
                'pay_trade_no' => 'wz_' . $res['messageDetail']['orderId'],
                'status' => '1',    // 成功
            );
            $returnData['code'] = true;
            $returnData['data'] = $payData;
            return $returnData;
        }else{
            // TODO 验签失败 这里记录错误日志或邮件报警
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>：微众银行支付宝商户号：'. $this->config['mch_id'] . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($res);
            $this->alertEmail($message);
            $this->log($res, __CLASS__, $this->config['mch_id'], __FUNCTION__);
        }
        return $returnData;

    }

    //充值同步通知
    public function syncCallback(){}
    //对账数据下载
    public function queryBill($params){}
    //退款提交方法
    public function refundSubmit($params){}
    //退款订单查询方法
    public function queryRefund($params){}
    //生成请求
    public function requestHttp($params){}
    /**
     * [queryOrder 订单查询]
     * @author LiKangJian 2017-07-07
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function queryOrder($params)
    {
        //设置结果标识
        $params['flag'] = false;
        $params['timestamp'] = time() * 1000;
        $params['bill_no'] = $params['trade_no'];
        $params['channel'] = $this->defaultParams['channel'];
        $res =  $this->getQueryRes($params);
        $returnData = array(
            'code' => false,
            'msg'  => '操作失败',
            'data' => array('code' => 1, 'msg' => '操作失败'),
        );
        if($res['result_msg'] == 'OK' && $res['result_code'] == '0' && count($res['bills']))
        {
            $ptype = array('wzPay' => '微众银行支付宝');
            $bills = (array) $res['bills'][0];
            //$pstatus = array('SUCCESS' => '已付款', 'REFUND' => '转入退款', 'NOTPAY' => '未支付', 'CLOSED' => '已关闭', 'REVERSE' => '已冲正', 'REVOK' => '已撤销', 'REVOKED' => '已冲正', 'USERPAYING' => '用户支付中', 'PAYERROR' => '支付失败');
            $payData = array(
                'code' => '0',
                'ptype' => $ptype[$params['additions']],
                'pstatus' => $bills['success_time'] ? '已付款' : '未支付',
                'ptime' =>  $bills['success_time'] ? $params['created'] : '',
                'pmoney' => number_format($res['bills'][0]->total_fee / 100, 2, ".", ","),
                'pbank' => '',
                'ispay' => $bills['success_time'] ? true : false,
                'pay_trade_no' => 'wz_'.$bills['bill_no'],
            );
            $returnData = array(
                'code' => true,
                'msg' => '操作成功',
                'data' => $payData,
            );
            return $returnData;
        }
        else
        {
            $returnData['data']['msg'] = $res['return_msg'];

        } 
        return $returnData;
    }
    /**
     * [getQueryRes 订单查询方法]
     * @author LiKangJian 2017-07-07
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function getQueryRes($params)
    {
        $params = $this->mode ? $this->getCommonParams($params, '2') : $this->getCommonParams($params, '0');
        $url = $this->mode ? self::URI_TEST_BILLS : self::URI_BILLS;
        return (array) $this->get($url, $params, 30, false);
    }
    private function bill(array $data, $method = 'post') 
    {
        $data = $this->mode ? $this->getCommonParams($data, '2') : $this->getCommonParams($data, '0');
        $url = $this->mode ? self::URI_TEST_BILL : self::URI_BILL;
        switch ($method) {
            case 'get'://支付订单查询
                if (!isset($data["id"])) 
                {
                    throw new \Exception(self::NEED_PARAM . "id");
                }
                $order_id = $data["id"];
                unset($data["id"]);
                return self::get($url.'/'.$order_id, $data, 30, false);
                break;
            case 'post': // 支付
                $data['bc_analysis'] = (object)array('sdk_version' => self::PHP_SDK_VERSION);
                if (!isset($data["channel"])) {
                    throw new \Exception(self::NEED_PARAM . "channel");
                }
                if (!isset($data["total_fee"])) {
                    throw new \Exception(self::NEED_PARAM . "total_fee");
                } else if(!is_int($data["total_fee"]) || 1>$data["total_fee"]) {
                    throw new \Exception(self::NEED_VALID_PARAM . "total_fee");
                }
                if (!isset($data["bill_no"])) {
                    throw new \Exception(self::NEED_PARAM . "bill_no");
                }
                if (!preg_match('/^[0-9A-Za-z]{8,32}$/', $data["bill_no"])) {
                    throw new \Exception(self::NEED_VALID_PARAM . "bill_no");
                }
                if (!isset($data["title"])) {
                    throw new \Exception(self::NEED_PARAM . "title");
                }
                return $this->post($url, $data, 30, false);
                break;
            default :
                exit('No this method');
                break;
        }
    }
    /**
     * [getCommonParams 获取共同的必填参数0: app_secret , 1: master_secret , 2: test_secret]
     * @author LiKangJian 2017-10-20
     * @param  [type] $data        [description]
     * @param  string $secret_type [description]
     * @return [type]              [description]
     */
    private function getCommonParams($data, $secret_type = '0')
    {
        $secret = '';
        switch($secret_type){
            case '1':
                $secret = $this->masterSecret;
                break;
            case '2':
                $secret = $this->testSecret;
                break;
            case '0':
            default:
                $secret = $this->appSecret;
                break;
        }
        if(empty($secret)){throw new \Exception(self::NEED_PARAM. 'APP(Master/Test) Secret, 请检查!');}
        $data["app_id"] = $this->appId;
        if(!isset($data["timestamp"]))
        {
            $data["timestamp"] = (int)(microtime(true) * 1000);
        }
        $data["app_sign"] = $this->makeSign($this->appId, $data["timestamp"], $secret);
        $this->verifyNeedParams(array('app_id', 'timestamp', 'app_sign'), $data);
        return $data;
    }
    /**
     * [verifyNeedParams 验证必要参数]
     * @author LiKangJian 2017-10-20
     * @param  [type] $params [description]
     * @param  [type] $data   [description]
     * @return [type]         [description]
     */
    private function verifyNeedParams($params, $data)
    {
        if(is_string($params))
        {
            if(!isset($data[$params]) || empty($data[$params]))
            {
                throw new \Exception(self::NEED_PARAM . $params);
            }
        }else if(is_array($params))
        {
            foreach ($params as $field) 
            {
                if(!isset($data[$field]) || empty($data[$field]))
                {
                    throw new \Exception(self::NEED_PARAM . $field);
                }
            }
        }
    }
    /**
     * [makeSign 生成签名]
     * @author LiKangJian 2017-10-20
     * @param  [type] $app_id    [description]
     * @param  [type] $timestamp [description]
     * @param  [type] $secret    [description]
     * @return [type]            [description]
     */
    private function makeSign($app_id, $timestamp, $secret)
    {
        if(empty($app_id) || empty($timestamp) || empty($secret))
        {
            throw new \Exception(self::VALID_SIGN_PARAM);
        }
        return md5($app_id.$timestamp.$secret);
    }
    /**
     * [channelCheck 验证渠道合法性]
     * @author LiKangJian 2017-10-20
     * @param  [type] $channel [description]
     * @return [type]          [description]
     */
    private function channelCheck($channel)
    {
        return in_array($channel,$this->channels) ? true : false;
    }
    /**
     * [post 订单提交post请求]
     * @author LiKangJian 2017-10-23
     * @param  [type] $api         [description]
     * @param  [type] $data        [description]
     * @param  [type] $timeout     [description]
     * @param  [type] $returnArray [description]
     * @return [type]              [description]
     */
    private function post($api, $data, $timeout, $returnArray) 
    {
        return $this->getResult($api, 'post', $data, $timeout, $returnArray);
    }
    /**
     * [get 订单查询get请求]
     * @author LiKangJian 2017-10-23
     * @param  [type]  $api         [description]
     * @param  [type]  $data        [description]
     * @param  [type]  $timeout     [description]
     * @param  [type]  $returnArray [description]
     * @param  boolean $type        [description]
     * @return [type]               [description]
     */
    private function get($api, $data, $timeout, $returnArray, $type = true) 
    {
        return $this->getResult($api, $type ? "get" : 'new_get', $data, $timeout, $returnArray);
    }
    /**
     * [getResult 获取结果]
     * @author LiKangJian 2017-10-20
     * @param  [type] $url       [description]
     * @param  [type] $type      [description]
     * @param  [type] $data      [description]
     * @param  [type] $timeout   [description]
     * @param  [type] $returnArr [description]
     * @return [type]            [description]
     */
    private function getResult($url, $type, $data, $timeout, $returnArr)
    {
        $api_url = $this->apiUrl . $url;
        $httpResultStr = $this->request($api_url, $type, $data, $timeout);
        $result = json_decode($httpResultStr, !$returnArr ? false : true);
        if (!$result) 
        {
            throw new \Exception(self::UNEXPECTED_RESULT . $httpResultStr);
        }
        return (array)$result;
    }
    /**
     * [request 请求]
     * @author LiKangJian 2017-10-20
     * @param  [type]  $url     [description]
     * @param  [type]  $method  [description]
     * @param  array   $data    [description]
     * @param  integer $timeout [description]
     * @return [type]           [description]
     */
    private function request($url, $method, array $data, $timeout = 30) 
    {
        try {
            $ch = curl_init();
            /*支持SSL 不验证CA根验证*/
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            /*重定向跟随*/
            if (ini_get('open_basedir') == '' && !ini_get('safe_mode')) {
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            }
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            //设置 CURLINFO_HEADER_OUT 选项之后 curl_getinfo 函数返回的数组将包含 cURL
            //请求的 header 信息。而要看到回应的 header 信息可以在 curl_setopt 中设置
            //CURLOPT_HEADER 选项为 true
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLINFO_HEADER_OUT, false);
            //fail the request if the HTTP code returned is equal to or larger than 400
            //curl_setopt($ch, CURLOPT_FAILONERROR, true);
            $header = array("Content-Type:application/json;charset=utf-8;", "Connection: keep-alive;");
            $methodIgnoredCase = strtolower($method);
            switch ($methodIgnoredCase) {
                case "post":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "get":
                    curl_setopt($ch, CURLOPT_URL, $url."?para=".urlencode(json_encode($data)));
                    break;
                case "new_get":
                    curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
                    break;
                case "put":
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); //POST数据
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                    curl_setopt($ch, CURLOPT_URL, $url);
                    break;
                case "delete":
                    curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($data));
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
                default:
                    throw new \Exception('不支持的HTTP方式');
                    break;
            }
            $result = curl_exec($ch);
            if (curl_errno($ch) > 0) 
            {
                // 记录错误日志
                log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($data) . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
            }
            curl_close($ch);
            return $result;
        } catch (\Exception $e) 
        {
            return "CURL EXCEPTION: ".$e->getMessage();
        }
    }

}