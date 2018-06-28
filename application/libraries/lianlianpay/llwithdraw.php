<?php
/**
 * 连连单笔实时接口.
 */
require_once ("lib/llpay_apipost_submit.class.php");
require_once ("lib/llpay_security.function.php");
class LlWithdraw
{
    public function __construct()
    {
        header('Content-Type: text/html; Charset=UTF-8');
    }

    public function withdraw($params)
    {
        require ("llpay.config.php");
        log_message('LOG', "配置参数:" . json_encode($llpay_config), 'lianlianwithdraw');
        $parameter = array(
            "oid_partner" => trim($llpay_config['oid_partner']),
            "sign_type" => trim($llpay_config['sign_type']),
            "no_order" => $params['no_order'],
            "dt_order" => $params['dt_order'],
            "money_order" => $params['money_order'],
            "acct_name" => $params['acct_name'],
            "card_no" => $params['card_no'],
            "info_order" => $params['info_order'],
            "flag_card" => $params['flag_card'],
            "notify_url" => $params['notify_url'],
            "platform" => $params['platform'],
            "api_version" => $params['api_version']
        );
        //建立请求
        $llpaySubmit = new LLpaySubmit($llpay_config);
        
        //对参数排序加签名
        $sortPara = $llpaySubmit->buildRequestPara($parameter);
        //传json字符串
        $json = json_encode($sortPara);

        $parameterRequest = array(
            "oid_partner" => trim($llpay_config['oid_partner']),
            "pay_load" => ll_encrypt($json, $llpay_config['LIANLIAN_PUBLICK_KEY']) //请求参数加密
        );
        $llpay_payment_url = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';
        log_message('LOG', "请求值:" . json_encode($parameterRequest), 'lianlianwithdraw');
        $html_text = $llpaySubmit->buildRequestJSON($parameterRequest, $llpay_payment_url);
        //调用付款接口，同步返回0000，是指创建连连支付单成功，订单处于付款处理中状态，最终的付款状态由异步通知告知
        //出现1002，2005，4006，4007，4009，9999这6个返回码时或者没返回码，抛exception（或者对除了0000之后的code都查询一遍查询接口）调用付款结果查询接口，明确订单状态，不能私自设置订单为失败状态，以免造成这笔订单在连连付款成功了，
        //而商户设置为失败,用户重新发起付款请求,造成重复付款，商户资金损失
        return $html_text;
    }
    
    public function queryWithdraw($params)
    {
        require_once ("llpay.config.php");
        require_once ("lib/llpay_apipost_submit.class.php");
        require_once ("lib/llpay_security.function.php");
        $parameter = array(
            "oid_partner" => trim($llpay_config['oid_partner']),
            "sign_type" => trim($llpay_config['sign_type']),
            "no_order" => $params['no_order'],
            "platform" => $params['platform'],
            "api_version" => $params['api_version']
        );
        //建立请求
        $llpaySubmit = new LLpaySubmit($llpay_config);
        
        //对参数排序加签名
        $sortPara = $llpaySubmit->buildRequestPara($parameter);
        $llpay_query_url = 'https://instantpay.lianlianpay.com/paymentapi/queryPayment.htm';
        $html_text = $llpaySubmit->buildRequestJSON($sortPara, $llpay_query_url);
        return $html_text;
    }
    
    public function confirm($params)
    {
        require ("llpay.config.php");
        $parameter = array(
            "oid_partner" => trim($llpay_config['oid_partner']),
            "sign_type" => trim($llpay_config['sign_type']),
            "no_order" => $params['no_order'],
            "notify_url" => $params['notify_url'],
            "platform" => $params['platform'],
            "api_version" => $params['api_version'],
            "confirm_code" => $params['confirm_code']
        );
        //建立请求
        $llpaySubmit = new LLpaySubmit($llpay_config);
        
        //对参数排序加签名
        $sortPara = $llpaySubmit->buildRequestPara($parameter);
        //传json字符串
        $json = json_encode($sortPara);

        $parameterRequest = array(
            "oid_partner" => trim($llpay_config['oid_partner']),
            "pay_load" => ll_encrypt($json, $llpay_config['LIANLIAN_PUBLICK_KEY']) //请求参数加密
        );
        $llpay_payment_url = 'https://instantpay.lianlianpay.com/paymentapi/confirmPayment.htm';
        log_message('LOG', "请求值:" . json_encode($parameterRequest), 'lianlianwithdraw');
        $html_text = $llpaySubmit->buildRequestJSON($parameterRequest, $llpay_payment_url);
        //调用付款接口，同步返回0000，是指创建连连支付单成功，订单处于付款处理中状态，最终的付款状态由异步通知告知
        //出现1002，2005，4006，4007，4009，9999这6个返回码时或者没返回码，抛exception（或者对除了0000之后的code都查询一遍查询接口）调用付款结果查询接口，明确订单状态，不能私自设置订单为失败状态，以免造成这笔订单在连连付款成功了，
        //而商户设置为失败,用户重新发起付款请求,造成重复付款，商户资金损失
        return $html_text;
    }
}
