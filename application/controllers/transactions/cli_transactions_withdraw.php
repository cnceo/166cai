<?php

/**
 * 自动提现脚本.
 *
 * @date:2016-12-08
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Transactions_Withdraw extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->controlRestart($this->con);
        $this->load->model('withdraw_model');
        $channel = $this->withdraw_model->getWithdrawChannel();
//        if (ENVIRONMENT !== 'production')
//        {
//        	$this->tonglian($channel['audit']);
//        	return;
//        }
        if($channel['channel']=='tonglian'){
            $this->tonglian($channel['audit']);
        }
        if($channel['channel']=='lianlian'){
            $this->lianlian($channel['audit']);
        }
        if($channel['channel']=='xianfeng'){
            $this->xianfeng($channel['audit']);
        }
    }
    
    public function query($content)
    {
        $this->load->model('withdraw_model');
        $withdraws = $this->withdraw_model->getNeedQueryWithdraw();
        $this->load->library('tonglpay/tlwithdraw');
        foreach ($withdraws as $id) {
            if (ENVIRONMENT === 'production') {
                $user = '20029000001702704';
                $password = '111111';
                $merchant = '200290000017027';
            } else {
                $user = '20060400000044502';
                $password = '`12qwe';
                $merchant = '200604000000445';
            }
            $params = array(
                'INFO' => array(
                    'TRX_CODE' => '200004',
                    'VERSION' => '03',
                    'DATA_TYPE' => '2',
                    'LEVEL' => '6',
                    'USER_NAME' => $user,
                    'USER_PASS' => $password,
                    'REQ_SN' => $id,
                ),
                'QTRANSREQ' => array(
                    'QUERY_SN' => $id,
                    'MERCHANT_ID' => $merchant,
                    'STATUS' => '2',
                    'TYPE' => '1',
                    'START_DAY' => '',
                    'END_DAY' => ''
                ),
            );
            $result = $this->tlwithdraw->main('withdraw', array('withdraw' => $params));
            if ($result['AIPG']['INFO']['RET_CODE'] == '0000' && $result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE'] == '0000') 
            {
                $this->withdraw_model->update_check(array(
                    'start_check' => date('Y-m-d H:i:s', time()),
                    'status' => 5,
                    'withdraw_channel'=>'tonglian'
                        ), array(
                    'trade_no' => $id,
                ));
                log_message('LOG', "提现记录:订单号{$id},提现成功" . json_encode($result), 'tonglianwithdraw');
            }
            elseif($result['AIPG']['INFO']['RET_CODE'] == '2000' || $result['AIPG']['INFO']['RET_CODE'] == '2008')
            {
            	//中间状态不处理
            	log_message('LOG', "提现记录:订单号{$id},提现失败,接口异常" . json_encode($result), 'tonglianwithdraw');
            	continue;
            }
            else
            {
            	$this->withdraw_model->updateColumn(array(
                    'remark' => $result['AIPG']['INFO']['RET_CODE'] . '/' . (isset($result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE']) ? $result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE'] : '') . '/' . (isset($result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG']) ? $result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG'] : ''),
                    'withdraw_channel' => 'tonglian'
                        ), array(
                    'trade_no' => $id,
                ));

                log_message('LOG', "提现记录:订单号{$id},提现失败,接口异常" . json_encode($result), 'tonglianwithdraw');
            	$content = str_replace("#不成功原因#", $result['AIPG']['INFO']['RET_CODE'] . '/' . (isset($result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE']) ? $result['AIPG']['QTRANSRSP']['QTDETAIL']['RET_CODE'] : '') . '/' . (isset($result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG']) ? $result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG'] : ''), $content);
                $this->withdraw_model->alertEmail($content);
            }
        }
    }
    
    public function tonglian($audit)
    {
        $this->load->model('withdraw_model');
        $withdraws = $this->withdraw_model->getAllWithdraw($audit);
        $this->load->library('tonglpay/tlwithdraw');
        if (ENVIRONMENT === 'production')
        {
            $user = '20029000001702704';
            $password = '111111';
            $merchant = '200290000017027';
        } else {
            $user = '20060400000044502';
            $password = '`12qwe';
            $merchant = '200604000000445';
        }
        foreach ($withdraws as $withdraw) {
            $params = array(
                'INFO' => array(
                    'TRX_CODE' => '100014', //交易代码默认值
                    'VERSION' => '03', //版本默认值
                    'DATA_TYPE' => '2', //数据包类型 xml 默认值
                    'LEVEL' => '6', //级别 默认值
                    'USER_NAME' => $user, //帐户名 分测试环境和正式环境
                    'USER_PASS' => $password, //用户密码 分测试环境和正式环境
                    'REQ_SN' => $withdraw['trade_no'], //交易流水号
                ),
                'TRANS' => array(
                    'BUSINESS_CODE' => '09900', //业务代码 默认值
                    'MERCHANT_ID' => $merchant, //商户号 分测试环境和正式环境
                    'SUBMIT_TIME' => microtime(), //提交时间
                    'BANK_CODE' => $withdraw['bank_type'], //银行代码
                    'ACCOUNT_TYPE' => '00', //账号类型 默认值
                    'ACCOUNT_NO' => $withdraw['bank_id'], //银行卡号
                    'ACCOUNT_NAME' => $withdraw['real_name'], //银行卡户主姓名
                    'ACCOUNT_PROP' => '0', //账号属性 默认值
                    'AMOUNT' => $withdraw['money'], //金额 单位 分
                    'SUMMARY' => '166彩票提现',
                    'REMARK'=> '166彩票提现'
                ),
            );
            $result = $this->tlwithdraw->main('withdraw', array('withdraw' => $params));
            $content = "提款订单号{$withdraw['trade_no']}，提款人{$withdraw['real_name']}，#不成功原因#，未成功。请核实后手动处理。";
            if (!isset($result['AIPG']['INFO']['RET_CODE'])) {
                $result = $this->tlwithdraw->main('withdraw', array('withdraw' => $params));
                if (!isset($result['AIPG']['INFO']['RET_CODE'])) {
                    $this->withdraw_model->updateColumn(array(
                        'remark' => '接口异常',
                        'start_check' => date('Y-m-d H:i:s', time()),
                        'status' => 5,
                        'withdraw_channel'=>'tonglian'
                            ), array(
                        'trade_no' => $withdraw['trade_no'],
                    ));
                    log_message('LOG', "提现记录:订单号{$withdraw['trade_no']},提现失败,接口异常" . json_encode($result), 'tonglianwithdraw');
                    $content = str_replace("#不成功原因#", "接口异常", $content);
                    $this->withdraw_model->alertEmail($content);
                }
            }
            if(isset($result['AIPG']['INFO']['RET_CODE']))
            {
                if ($result['AIPG']['INFO']['RET_CODE'] == '0000') {
                    $this->withdraw_model->update_check(array(
                    'start_check' => date('Y-m-d H:i:s', time()),
                    'status' => 5,
                    'withdraw_channel'=>'tonglian'    
                        ), array(
                    'trade_no' => $withdraw['trade_no'],
                ));
                    log_message('LOG', "提现记录:订单号{$withdraw['trade_no']},提现成功" . json_encode($result), 'tonglianwithdraw');
                }  elseif ($result['AIPG']['INFO']['RET_CODE'] == '2000' || $result['AIPG']['INFO']['RET_CODE'] == '2008') {
                    $this->withdraw_model->updateColumn(array(
                        'remark' => $result['AIPG']['INFO']['RET_CODE'] . '/' . $result['AIPG']['INFO']['ERR_MSG'],
                        'start_check' => date('Y-m-d H:i:s', time()),
                        'status' => 5,
                        'withdraw_channel'=>'tonglian'
                            ), array(
                        'trade_no' => $withdraw['trade_no'],
                    ));
                    log_message('LOG', "提现记录:订单号{$withdraw['trade_no']},提现失败,接口异常" . json_encode($result), 'tonglianwithdraw');
                } else {
                    $this->withdraw_model->updateColumn(array(
                    'remark' => $result['AIPG']['INFO']['RET_CODE'].'/'.$result['AIPG']['INFO']['ERR_MSG'],
                    'start_check' => date('Y-m-d H:i:s', time()),
                    'status' => 5,
                    'withdraw_channel'=>'tonglian'    
                        ), array(
                    'trade_no' => $withdraw['trade_no'],
                ));
                    log_message('LOG', "提现记录:订单号{$withdraw['trade_no']},提现失败,接口异常" . json_encode($result), 'tonglianwithdraw');
                    $content = str_replace("#不成功原因#", $result['AIPG']['INFO']['RET_CODE'] . '/' . $result['AIPG']['INFO']['ERR_MSG'], $content);
                    $this->withdraw_model->alertEmail($content);
                }
            }
        }
        $this->query($content);
    }
    
    public function lianlian($audit)
    {
        $this->load->model('withdraw_model');
        $withdraws = $this->withdraw_model->getAllWithdraw($audit);
        $time = date("YmdHis", time());
        if (ENVIRONMENT === 'production') {
            $ip = "120.132.33.198:4443";
        } else {
            $ip = "123.59.105.39:4443";
        }
        foreach ($withdraws as $withdraw) {
            $params = array(
                "no_order" => $withdraw['trade_no'], //流水号
                "dt_order" => $time, //时间
                "money_order" => $withdraw['money']/100, //金额
                "acct_name" => $withdraw['real_name'], //姓名
                "card_no" => $withdraw['bank_id'], //银行卡号
                "info_order" => '166彩票提现', //订单描述
                "flag_card" => '0', //对私标志
                "notify_url" => 'https://'.$ip.'/api/withdraw/notice', //服务器异步通知地址
                "platform" => $this->config->item('domain'), //平台来源
                "api_version" => '1.0'//版本号
            );
            if (ENVIRONMENT !== 'production') 
            {
                $params['money_order'] = 0.01;
            }
            $this->load->library('lianlianpay/llwithdraw');
            $result = $this->llwithdraw->withdraw($params);
            $result = json_decode($result, true);
            $content = "提款订单号{$withdraw['trade_no']}，提款人{$withdraw['real_name']}，#不成功原因#，未成功。请核实后手动处理。";
            if (!isset($result['ret_code'])) {
                $result = $this->llwithdraw->withdraw($params);
                $result = json_decode($result, true);
                if (!isset($result['ret_code'])) {
                    $this->failWithdraw(array('ret_code' => '', 'ret_msg' => '接口异常'), $content, $withdraw['trade_no']);
                }
            }
            if (isset($result['ret_code'])) {
                if ($result['ret_code'] == '0000') {
                    $this->successWithdraw($result, $withdraw['trade_no']);
                }elseif ($result['ret_code'] == '4002') {
                    log_message('LOG', "待确认：" . json_encode($result), 'lianlianwithdraw');
                    $param = array(
                        "no_order" => $withdraw['trade_no'], //流水号
                        "notify_url" => 'https://' . $ip . '/api/withdraw/notice', //服务器异步通知地址
                        "platform" => $this->config->item('domain'), //平台来源
                        "api_version" => '1.0', //版本号,
                        'confirm_code' => $result['confirm_code']//确认码
                    );
                    $res = $this->llwithdraw->confirm($param);
                    $res = json_decode($res, true);
                    if($res['ret_code'] == '0000'){
                        $this->successWithdraw($res, $withdraw['trade_no'], '4002/需等待查询');
                    }else{
                        $this->failWithdraw($res, $content, $withdraw['trade_no']);
                    }
                }else{
                    $this->failWithdraw($result, $content, $withdraw['trade_no']);
                }
            }
        }
    }
    
    private function failWithdraw($result, $content, $id)
    {
        $this->withdraw_model->updateColumn(array(
            'remark' => $result['ret_code'] . '/' . $result['ret_msg'],
            'start_check' => date('Y-m-d H:i:s', time()),
            'status' => 5,
            'withdraw_channel' => 'lianlian'
                ), array(
            'trade_no' => $id,
        ));
        log_message('LOG', "提现记录:订单号{$id},提现失败,接口异常" . json_encode($result), 'lianlianwithdraw');
        $content = str_replace("#不成功原因#", $result['ret_code'] . '/' . $result['ret_msg'], $content);
        $this->withdraw_model->alertEmail($content);
    }

    private function successWithdraw($result, $id, $message = '')
    {
        $this->withdraw_model->updateColumn(array(
            'remark' => $message,
            'start_check' => date('Y-m-d H:i:s', time()),
            'status' => 5,
            'withdraw_channel' => 'lianlian'
                ), array(
            'trade_no' => $id,
        ));
        log_message('LOG', "提现记录:订单号{$id},等待异步返回结果" . json_encode($result), 'lianlianwithdraw');
    }

    public function xianfeng($audit)
    {
        $this->load->model('withdraw_model');
        $withdraws = $this->withdraw_model->getAllWithdraw($audit);
        $time = date("YmdHis", time());
        if (ENVIRONMENT === 'production') {
            $ip = "120.132.33.198:443";
            $merchantId = 'M200006984';
            $key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg8gP5+tNBhgI7glDZI5myXdvCW1dCyWwVjZIDUvCax/t9qZVRPujhFX77QvPSicTVDKu9AQ4pfE8NXGme+J9vfqeCYLIyjE0ewKt0FuTFfHZ9NBS/fMBdG6j9A5D1drM6gVYMFnUYu+QHpomsEaBIuXWvJB0PeEx3jWVKLYRXUe4mhgiHeTsRBlA23hSmmSX+X9+8NJLfmbr2fFn4X+qBplQcqukQBeZm34bWBDEh6R+VQIT1FQMhduDez06GaFBcPVqtv7ns0cB1YV07knNKDQO7N4+CzofvFKMVbXN03vwuEFLHU6WkfEx7vMopqQCXqy6+1hDb3af1euMW5gxdwIDAQAB';
            $url = 'https://mapi.ucfpay.com/gateway.do';
        } else {
            $ip = "123.59.105.39:443";
            $merchantId = 'M200000550';
            $key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB';
            $url = 'http://sandbox.firstpay.com/security/gateway.do';
        }
        foreach ($withdraws as $withdraw) {
            $params = array(
                "service" => 'REQ_WITHDRAW',
                'secId' => 'RSA',
                'version' => '4.0.0',
                'merchantId' => $merchantId,
                'key' => $key,
                'url' => $url,
                "merchantNo" => $withdraw['trade_no'], //流水号
                'source' => 1,
                "transCur" => 156, //时间
                "amount" => $withdraw['money'], //金额
                "accountName" => $withdraw['real_name'], //姓名
                "accountNo" => $withdraw['bank_id'], //银行卡号
                "bankNo" => $withdraw['bank_no'],
                "userType" => '1', //对私标志
                "noticeUrl" => 'https://'.$ip.'/api/withdraw/xfNotice', //服务器异步通知地址
            );
            if (ENVIRONMENT !== 'production') 
            {
                $params['amount'] = 1;
            }
            $this->load->library('xianfengpay');
            $result = $this->xianfengpay->withdraw($params);
            $content = "提款订单号{$withdraw['trade_no']}，提款人{$withdraw['real_name']}，#不成功原因#，未成功。请核实后手动处理。";
            if (!isset($result['resCode'])) {
                $result = $this->xianfengpay->withdraw($params);
                if (!isset($result['resCode'])) {
                    $this->xianfengFailWithdraw(array('resCode' => '', 'resMessage' => '接口异常'), $content, $withdraw['trade_no']);
                }
            }
            if (isset($result['resCode'])) {
                if ($result['resCode'] == '00000' || $result['resCode'] == '00001' || $result['resCode'] == '00002') {
                    $this->xianfengSuccessWithdraw($result, $withdraw['trade_no']);
                }else{
                    $this->xianfengFailWithdraw($result, $content, $withdraw['trade_no']);
                }
            }
        }
    }
    
    private function xianfengFailWithdraw($result, $content, $id)
    {
        $this->withdraw_model->updateColumn(array(
            'remark' => $result['resCode'] . '/' . $result['resMessage'],
            'start_check' => date('Y-m-d H:i:s', time()),
            'status' => 5,
            'withdraw_channel' => 'xianfeng'
                ), array(
            'trade_no' => $id,
        ));
        log_message('LOG', "提现记录:订单号{$id},提现失败,接口异常" . json_encode($result), 'xianfengwithdraw');
        $content = str_replace("#不成功原因#", $result['resCode'] . '/' . $result['resMessage'], $content);
        $this->withdraw_model->alertEmail($content);
    }

    private function xianfengSuccessWithdraw($result, $id, $message = '')
    {
        $this->withdraw_model->updateColumn(array(
            'remark' => $message,
            'start_check' => date('Y-m-d H:i:s', time()),
            'status' => 5,
            'withdraw_channel' => 'xianfeng'
                ), array(
            'trade_no' => $id,
        ));
        log_message('LOG', "提现记录:订单号{$id},等待异步返回结果" . json_encode($result), 'xianfengwithdraw');
    }
}
