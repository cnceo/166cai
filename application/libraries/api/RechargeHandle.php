<?php
/**
 * 充值处理类
 */
class RechargeHandle 
{

	public function __construct($config = array())
	{
		$this->CI = &get_instance();
        $this->CI->load->model('pay_model');
        $this->CI->load->model('pay_bank_model');
		
	}
	/**
     * [Request 充值requestHttp方式]
     * @author LiKangJian 2017-11-28
     * @param  [type] $params [description]
     */
	public function request($params)
	{
        $payParams = array();
        $payParams['trade_no'] = $params['trade_no'];
        $payParams['money'] = $params['money'];
        $payParams['uid'] = isset($params['uid']) ? $params['uid'] : '0';
        $payParams['real_name'] = isset($params['real_name']) ? $params['real_name'] : '';
        $payParams['id_card'] = isset($params['id_card']) ? $params['id_card'] : '';
        $payParams['ip'] = isset($params['ip']) ? $params['ip'] : '127.0.0.1';
        $payParams['bank_id'] = isset($params['bank_id']) ? $params['bank_id'] : 0;
        $payParams['pay_agreement_id'] = isset($params['pay_agreement_id']) ? $params['pay_agreement_id'] :0;
        $libery = $params['lib'];

        $respone = array(
            'code' => false,
            'msg'  => '请求充值类型错误',
            'data' => $this->payParams,
        );
        $this->CI->load->library("recharge/{$libery}");
        if(class_exists($libery))
        {
            $configData = $this->CI->pay_model->getPayConfig($params['configId'] );
            $config = array();
            if(!empty($configData['extra']))
            {

                $config = json_decode($configData['extra'], true);
            }
            $paySubmit = new $libery($config);
            $respone = $paySubmit->requestHttp($payParams);
        }
        return  $respone;
	}


    /**
     * [breakPayRequest 解约支付协议]
     * @author LiKangJian 2017-11-28
     * @param  array  $params [description]
     * @return [type]         [description]
     */
    public function breakPayRequest($params = array())
    {
        $respone = array(
            'code' => false,
            'msg'  => '解约支付错误',
            'data' => $params,
        );
        
        if(!empty($params['uid']) && !empty($params['bank_id']))
        {
            $bankInfo = $this->CI->pay_bank_model->getUserBankInfo($params['uid'], $params['bank_id']);

            if(!empty($bankInfo))
            {
                if(!empty($bankInfo['pay_agreement']))
                {
                    $agreement = json_decode($bankInfo['pay_agreement'], true);

                    // 当前支持卡前置的支付类型
                    $typeArr = array(
                        'umpay' =>  array(
                            'library'   =>  'UmPay',
                            'configId'  =>  '72',
                        )
                    );

                    foreach ($typeArr as $name => $items) 
                    {
                        if(!empty($agreement[$name]))
                        {
                            $this->CI->load->library("recharge/{$items['library']}");
                            if(class_exists($items['library']))
                            {
                                $configData = $this->CI->pay_model->getPayConfig($items['configId']);
                                $config = array();
                                if(!empty($configData['extra']))
                                {
                                    $config = json_decode($configData['extra'], true);
                                }
                                $paySubmit = new $items['library']($config);
                                $rparams = array(
                                    'configId'  =>  $items['configId'],
                                    'uid'       =>  $params['uid'],
                                    'bank_id'   =>  $params['bank_id'],
                                    'pay_agreement_id' => $agreement[$name],
                                );
                                $paySubmit->breakPayRequest($rparams);
                            }
                        }
                    }
                }
                // 前端解绑
                $this->CI->pay_bank_model->deleteBankInfo($params);

                $respone = array(
                    'code' => true,
                    'msg'  => '解约支付成功',
                    'data' => $params,
                );
            }
        }
        return $respone;
    }


    /**
     * 中信微信SDK马甲版预订单生成
     */
    public function zxwxSdkByChannel($params)
    {
        $trade_no = $params['trade_no'];
        $money = $params['money'];
        $channel = $params['channel'];
        require_once (in_array(APPPATH,array('../application/app/','../application/ios/')) ? dirname(APPPATH) : APPPATH) . '/libraries/weixinzxpay/pay_submit.class.php';
        $this->CI->config->load('appChannel');
        $config = $this->CI->config->item('zxwxSdk');
        $respone = array();
        if(!empty($config[$channel]))
        {
            $zxwxSdk = $config[$channel];
            $rparams = array(
                'encoding' => $zxwxSdk['encoding'],             //编码方式
                'signMethod' => $zxwxSdk['signMethod'],         //充值金额 单位:元，精确到分.
                'txnType' => '01',                              //交易类型 01：消费；
                'txnSubType' => '010132',                       //交易子类型 010130：二维码支付 010131：公众号支付 010132：APP支付（主扫）
                'channelType' => '6002',                        //接入渠道  6002：商户互联网渠道;
                'payAccessType' => '02',                        //接入支付类型 02：接口支付
                'backEndUrl' => $zxwxSdk['backEndUrl'] . $channel,         //后台通知地址
                'merId' => $zxwxSdk['merId'],                   //普通商户或一级商户的商户号
                'orderId' => $trade_no,                         //商户订单号
                'orderTime' => date('YmdHis', time()),          //交易起始时间  格式为[yyyyMMddHHmmss] ,如2009年12月25日9点10分10秒 表示为20091225091010
                'orderTimeExpire' => date('YmdHis', strtotime('+7 day')),   //交易结束时间
                'productId' => '1',                             //商品ID trade_type=010130，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
                'orderBody' => '彩咖充值',                      //商品描述
                'txnAmt' => (string)$money,                     //交易金额  订单总金额(交易单位为分，例:1.23元=123)，只能整数
                'currencyType' => $zxwxSdk['currencyType'],     //交易币种 默认是156：人民币
            );
            //建立请求
            $paySubmit = new paySubmit($zxwxSdk);
            $respone = $paySubmit->buildRequestHttp($rparams);
        }
        return $respone;
    }

    // 支付宝微信随机比例分配
    public function randomRate($params)
    { 
        return $this->CI->pay_model->getRateDetail($params);
    }
}