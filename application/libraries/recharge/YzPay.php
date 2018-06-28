<?php
// +----------------------------------------------------------------------
// | Created by  PhpStorm.
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 上海彩咖网络科技有限公司.
// +----------------------------------------------------------------------
// | Create Time (2018/3/30-14:02)
// +----------------------------------------------------------------------
// | Author: 唐轶俊 <tangyijun@km.com>
// +----------------------------------------------------------------------
// | 用户充值-盈中平安银行渠道
// +----------------------------------------------------------------------
require_once dirname(__FILE__) . '/RechargeAbstract.php';
header("Content-type: text/html; charset=utf-8"); 
class YzPay extends RechargeAbstract
{
    /**
     * 商户id
     * @var unknown_type
     */
    private $merId;

    /**
     * 配置信息
     * @var unknown_type
     */
    private $config;

    /**
     * 请求第三方网关地址
     * @var unknown_type
     */
    private $payGateway;


    public function __construct($config = array())
    {
        $this->merId = $config['mer_id'];
        $this->key   = $config['key'];
        $this->payGateway = $config['plat_url'];
        $this->config = $config;
    }

    /**
     * @return float
     * 返回13位数的时间戳
     */
    public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

    /**
     * curl请求支付信息
     * @see RechargeAbstract::requestHttp()
     */
    public function requestHttp($params)
    {
        $returnData = array(
            'code' => false,
            'msg'  => '请求错误',
            'data' => $params,
        );
        //组装盈中支付需求参数
        $sendParams =  array(
            'merchant_order_no' => $params['trade_no'],
            'merchant_no'       => $this->config['mer_id'],
            'callback_url'      => $this->config['notify_url'],
            'order_smt_time'    => (string)$this->getMillisecond(),
            'order_type'        => '02',
            'trade_amount'      => (string)$params['money'],
            'goods_name'        => $this->config['goods_inf'],
            'goods_type'        => '02',
            'trade_desc'        => '用户充值',
            'sign_type'         => '01',
        );

        if($this->config['return_url']){
            $sendParams['return_url'] = $this->config['return_url'];
        }
        if($this->config['result_url']){
            $sendParams['result_url'] = $this->config['result_url'].'?trade_no='.$params['trade_no'];
        }
        if(isset($this->config['qr_pay_mode'])){
            $sendParams['qr_pay_mode'] = $this->config['qr_pay_mode'];
            $sendParams['qrcode_width'] = $this->config['qrcode_width'];
        }
        if (isset($this->config['scene_info'])) {
            $scene = array('h5_info' => array('type' => 'Wap', 'wap_url' => 'www.ka5188.com', 'wap_name' => '166充值'));
            $strs = $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i", function($matchs) {
                return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
            }, json_encode($scene));
            $sendParams['scene_info'] = $strs;
            unset($sendParams['order_type']);
            unset($sendParams['goods_type']);
            unset($sendParams['trade_desc']);
        }
        //对数据进行签名
        $sign = $this->makeSign($sendParams,$this->config['key']);
        $sendParams['sign'] = $sign;
        $respData = $this->http_post_data($this->payGateway, $sendParams);
        if(empty($respData))
        {
            $mark = $this->getMark($this->merId);
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求'.$mark['mark'].'商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode($respData);
            $this->alertEmail($message);
            return $returnData;
        }
        //支付状态为成功
        if($respData['code'] == '608' || $respData['code'] == '500'){
            $resParams['orderId'] = $params['trade_no'];
            $resParams['orderTime'] = date("Y-m-d H:i:s");
            $resParams['txnAmt']    = $params['money'];
            $resParams['code_url']  = $respData['code'] == '608'?$respData['body']['params']:$respData['body']['pay_url'];
            $returnData = array(
                'code' => true,
                'msg' => '请求成功',
                'data' => $resParams,
            );
        } else {
            $mark = $this->getMark($this->merId);
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求'.$mark['mark'].'商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode($respData);
            $this->alertEmail($message);
        }
        return $returnData;
    }

    /**
     * @param $data
     * @param $key
     * @return string
     * 加密算法
     */
    public function makeSign($data,$key){
        //sign不参与签名
        if($data['sign']){
            unset($data['sign']);
        }
        //空值不参与排序
        foreach ($data as $k => $v){
            if(empty($v)){
                unset($data[$k]);
            }
        }
        //字典排序
        ksort($data);
        $strs = $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i",function($matchs){
            return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
        },json_encode($data));
        $str = $key. str_replace("\\/", "/", $strs).$key; //防止被转义
        return md5($str);
    }

    private function createString($para, $sort, $encode)
    {
        if ($para == null || !is_array($para)) {
            return "";
        }

        $linkString = "";
        if ($sort) {
            ksort($para);
            reset($para);
        }
        while (list($key, $value) = each($para)) {
            if ($encode) {
                $value = urlencode($value);
            }
            $linkString .= $key . "=" . $value . "&";
        }
        // 去掉最后一个&字符
        $linkString = substr($linkString, 0, count($linkString) - 2);

        return $linkString;
    }



    /**
     * 充值异步通知
     * @see RechargeAbstract::notify()
     */
    public function notify()
    {
        $returnData = array(
            'code' => false,
            'errMsg' => 'error',
            'succMsg' => 'success',
            'data' => array(),
        );
        $data = $_POST;
        //对返回参数进行签名
        $sign = $this->makeSign($data,$this->config['key']);
        //签名正确
        if($sign == trim($data['sign'],' ')){
            if($data['status'] == 'Success')
            {
                $payData = array(
                    'trade_no' => $data['merchant_order_no'],
                    'pay_trade_no' => $data['trade_no']?$data['trade_no']:$data['prd_ord_no'],
                    'status' => '1',	// 成功
                );
                $returnData['code'] = true;
                $returnData['data'] = $payData;
                $res = '订单号'.$data['merchant_order_no'] .'支付成功';
                $this->log(array($res), __CLASS__, $this->merId, __FUNCTION__);
                return $returnData;
            }elseif('Fail'==$data['status'] && '交易关闭' == $data['msg']){
                die();
            } else {
                $mark = $this->getMark($this->merId);
                $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$mark['mark'].'商户号：'. $this->merId . '异步通知接口支付失败，请及时留意。<br/>请求返回数据：' . json_encode($data);
                $this->alertEmail($message);
                $this->log($data, __CLASS__, $this->merId, __FUNCTION__);
            }
        }else{
            $mark = $this->getMark($this->merId);
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$mark['mark'].'商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . json_encode($data);
            $this->alertEmail($message);
            $this->log($data, __CLASS__, $this->merId, __FUNCTION__);
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
        $data = $_GET;
        $this->log(array('同步回调成功',json_encode($data)), __CLASS__, $this->merId, __FUNCTION__);
        if($data['status'] == 'TRADE_FINISHED'){
            $returnData = array(
                'code' => true,
                'errMsg' => '订单支付成功',
                'succMsg' => 'success',
                'data' => array(
                    'trade_no' => $data['merchant_order_no'],
                ),
            );
        }
        return $returnData;
    }

    public function formSubmit($params)
    {
        //$params['money'] = 1 ;//* 100;
        $res = $this->requestHttp($params);
        if($res['code'] != true) return array();
        $defaultParams = $res['data'];
        $defaultParams['submit_url'] = $params['submit_url'];
        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => array('charset' => 'utf-8','html' => $this->createForm($defaultParams),'params'=>$defaultParams),
        );
        return $returnData;
    }


    public function refundSubmit($params)
    {

    }


    public function queryOrder($params)
    {
        header("Content-type:text/html;charset=UTF-8");
        $url = "http://api.mposbank.com/tdcctp/alipay_query/single_trade_query.tran";
        $send_params = array(
            'merchant_no' => $this->merId,
            'merchant_order_no' => $params['trade_no']
        );

        $result = $this->http_post_data($url,$send_params);
        if($result['code'] == '0000'){
            $status = array('Process' => '交易处理中', 'Fail' => '交易失败','Success' => '交易成功','Wait' => '等待支付','Finish' => '交易结束');
            if('Success' === $result['body']['status']){
                $payData = array(
                    'code' => '0',
                    'ptype' => '盈中平安银行',
                    'pstatus' => $status[$result['body']['status']],
                    'ptime' => ($result['body']['status'] == 'Success' || $result['body']['status'] == 'Process' || $result['body']['status'] == 'Finish') ? date('Y-m-d H:i:s', strtotime($result['body']["payment_time"])) : '',
                    'pmoney' => number_format($result['body']['amount'] / 100, 2, ".", ","),
                    'pbank' => '',
                    'ispay' => ($result['body']['status'] == 'Success' || $result['body']['status'] ) == 'Finish' ? true : false,
                    'pay_trade_no' => 'Yz_'.$params['trade_no'],
                );
                $returnData = array(
                    'code' => true,
                    'msg' => '操作成功',
                    'data' => $payData,
                );
                return $returnData;
            }
        }
        $returnData = array(
            'code' => false,
            'msg'  => '操作失败',
            'data' => array('code' => 1, 'msg' => '补单失败'),
        );

        return $returnData;
    }

    public function queryRefund($params)
    {

    }

    private function createForm($params)
    {
        $html =  '<body onLoad="document.autoForm.submit();">';
        $html .= '<form name="autoForm" action="'.$params['submit_url'].'" method="post">';
        $formParams = array();
        $formParams['orderId'] = $params['orderId'];
        $formParams['orderTime'] = $params['orderTime'];
        $formParams['txnAmt'] = $params['txnAmt'];
        $formParams['codeUrl'] = urlencode(base64_encode($params['code_url']));
        foreach ($formParams as $key => $value)
        {

            $html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/><br/>';

        }
        $html .= '</form></body>';

        return $html;
    }


    /**
     * @param $url
     * @param $send_data
     * @return mixed
     * 发送post 请求
     */
    private function http_post_data($url, $send_data )
    {
        $TIMEOUT = 30;	//超时时间(秒)
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $TIMEOUT);
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $TIMEOUT-2);
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
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        if ($curl_errno || (!empty($curl_error)))
        {
            // 记录错误日志
            log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($send_data) . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
        }
        curl_close($ch);
        $result_arr = json_decode($html,true);
        return $result_arr;
    }

}