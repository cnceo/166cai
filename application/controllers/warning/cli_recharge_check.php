<?php
/**
 * 充值异常报警.
 * 
 * @date:2017-04-07
 */
defined('BASEPATH') or exit('No direct script access allowed');
class Cli_Recharge_Check extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $num = 20;
        $this->load->model('wallet_model');
        $payTypeName = array(
            'llpayWeb' => '连连快捷',
            //'llpaySdk' => '连连SDK',
            'payWeix'     =>  '中信微信',
            'sumpayWap'   =>  '统统付Wap',
            'sumpayWeb'   =>  '统统付快捷',
            //'yeepay'      =>  '易宝',
            'yeepayCredit'=>  '易宝信用卡',
            'yeepayKuaij' =>  '易宝快捷',
            'yeepayMPay'  =>  '易宝Wap',
            'yeepayWangy' =>  '易宝网银',
            'yeepayWeix'  =>  '易宝微信', 
            'zxwxSdk'     =>  '中信微信SDK', 
            'payZfb'      =>  '全付通支付宝',
            'wftWxSdk'    =>  '全付通微信SDK',
            'wftWx'       =>  '全付通微信PC',
            'jdPay'       =>  '京东支付',
            'umPay'       =>  '联动快捷',
            'xzZfbWap'    =>  '现在支付宝H5',  
            'hjZfbPay'    =>  '汇聚无限支付宝',
            'wftZfbWap'   =>  '兴业支付宝H5',
            'wftWxWap'    =>  '鸿粤兴业银行微信H5',
            'xzpay'       =>  '现在支付宝H5',
            'wftpay'      =>  '兴业支付宝H5',
            'hjpay'       =>  '汇聚无限支付宝',
            'wzPay'       =>  '微众银行支付宝',
            'hjWxWap'     =>  '微信H5-兴业银行',
            'hjZfbWap'    =>  '支付宝H5-鸿粤浦发银行',
            'payPaZfb'    =>  '平安银行支付宝',
            'payXmZfb'    =>  '厦门银行支付宝',
            'pfWxWap'     =>  '浦发白名单',
            'payYlyZf'    =>  '银联云支付',
            'tomatoZfbWap'=> '番茄支付宝h5',
            'ulineWxWap'  => '上海银行微信h5',
            'yzWxWap'     =>  '微信H5-盈中平安银行渠道',
            'hjZfbSh'     => '支付宝H5-上海银行',
            'jdSdk'       =>  '京东支付SDK',
            'wftwxzx'     => '微信扫码-长沙中信银行渠道',
            'wftzfbzx'    => '支付宝扫码-长沙中信银行渠道',
            'tomatoWxWap' => '番茄微信h5',
        );
        $platforms = array('网页', 'Android', 'IOS', 'M版');
        $resotherCondition = $this->wallet_model->getStopPaytype();
        $payType = array();
        if (trim($resotherCondition[0]) !== '') {
        	$otherCondition = json_decode($resotherCondition[0], true);
        	$payType = $otherCondition['payType'];
        }
        $alerts = array();
        foreach ($payTypeName as $key => $name) {
        	if (in_array($key, $payType)) {
        		if ($platform = $this->wallet_model->getUnRecharge($key, $num)) 
        		    $alerts[$key] = "{$payTypeName[$key]}, ".str_replace(array_keys($platforms), array_values($platforms), implode(',', array_unique($platform)))."连续{$num}笔交易未支付，请尽快查看！";
        	}
        }
        if ($alerts) $this->wallet_model->alertEmail(implode('<br>', $alerts));
    }
}
