<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 提现
 * @date:2017-04-13
 */
require_once APPPATH . '/core/CommonController.php';
class Withdraw extends CommonController 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('pay_model');
        $this->load->model('wallet_model', 'Wallet');
    }
    
    /**
     * 通联后台查询   原位置在wallet.php
     * @param unknown_type $id
     */
    public function withdrawStatus($id)
    {
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
    	if (isset($result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG']))
    	{
    		$message = $result['AIPG']['QTRANSRSP']['QTDETAIL']['ERR_MSG'];
    	}
    	else
    	{
    		$message = $result['AIPG']['INFO']['ERR_MSG'];
    	}
    	$res = array(
    			'status' => '1',
    			'msg' => 'success',
    			'data' => $message,
    	);
    	echo json_encode($res);
    }
    
    /**
     * 连连异步通知   原位置在wallet.php
     */
    public function notice()
    {
    	$post = file_get_contents("php://input");
    	log_message('LOG', "异步返回值:" . $post, 'lianlianwithdraw');
    	$result = json_decode($post, true);
    	$this->load->model('withdraw_model');
    	$withdraw = $this->withdraw_model->getStatus($result['no_order']);
    	if ($result['result_pay'] == 'SUCCESS') {
    		if ($withdraw['status'] != '2') {
    			$this->withdraw_model->update_check(array(
    					'start_check' => date('Y-m-d H:i:s', time()),
    					'status' => 5,
    			), array(
    					'trade_no' => $result['no_order'],
    			));
    			log_message('LOG', "提现记录:订单号{$result['no_order']},提现成功" . json_encode($result), 'lianlianwithdraw');
    		}
    	} else {
    		$this->withdraw_model->updateColumn(array(
    				'remark' => $result['info_order'],
    		), array(
    				'trade_no' => $result['no_order'],
    		));
    		$content = "提款订单号{$result['no_order']}，提款人{$withdraw['real_name']}，{$result['info_order']}，未成功。请核实后手动处理。";
    		$this->withdraw_model->alertEmail($content);
    	}
    	echo json_encode(array('ret_code' => '0000', 'ret_msg' => 'success'));
    }
    
    /**
     * 连连后台查询  原位置在wellet.php
     * @param unknown_type $orderid
     */
    public function lianlianQuery($orderid)
    {
    	$this->load->library('lianlianpay/llwithdraw');
    	$params = array(
    			"no_order" => $orderid, //流水号
    			"platform" => $this->config->item('domain'), //平台来源
    			"api_version" => '1.0'//版本号
    	);
    	$result = $this->llwithdraw->queryWithdraw($params);
    	$result = json_decode($result, true);
    	if ($result['result_pay'] == 'SUCCESS') {
    		$message = "成功";
    	} else {
    		$message = "失败";
    	}
    	$res = array(
    			'status' => '1',
    			'msg' => 'success',
    			'data' => $message,
    	);
    	echo json_encode($res);
    }
    
    public function xianfengQuery($orderid)
    {
    	$this->load->library('xianfengpay');
        if (ENVIRONMENT === 'production') {
            $merchantId = 'M200006984';
            $key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg8gP5+tNBhgI7glDZI5myXdvCW1dCyWwVjZIDUvCax/t9qZVRPujhFX77QvPSicTVDKu9AQ4pfE8NXGme+J9vfqeCYLIyjE0ewKt0FuTFfHZ9NBS/fMBdG6j9A5D1drM6gVYMFnUYu+QHpomsEaBIuXWvJB0PeEx3jWVKLYRXUe4mhgiHeTsRBlA23hSmmSX+X9+8NJLfmbr2fFn4X+qBplQcqukQBeZm34bWBDEh6R+VQIT1FQMhduDez06GaFBcPVqtv7ns0cB1YV07knNKDQO7N4+CzofvFKMVbXN03vwuEFLHU6WkfEx7vMopqQCXqy6+1hDb3af1euMW5gxdwIDAQAB';
            $url = 'https://mapi.ucfpay.com/gateway.do';
        } else {
            $merchantId = 'M200000550';
            $key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB';
            $url = 'http://sandbox.firstpay.com/security/gateway.do';
        }
        $params = array(
            "service" => 'REQ_WITHDRAW_QUERY_BY_ID',
            'secId' => 'RSA',
            'version' => '4.0.0',
            'merchantId' => $merchantId,
            'key' => $key,
            'url' => $url,
            "merchantNo" => $orderid, //流水号
        );
        $result = $this->xianfengpay->queryWithdraw($params);
    	$res = array(
    			'status' => '1',
    			'msg' => 'success',
    			'data' => $result['resMessage'],
    	);
    	echo json_encode($res);
    }

    
    public function xfNotice()
    {
        if (!empty($_GET["data"]) || !empty($_POST["data"])) {
            if (!empty($_GET["data"]))
                $data = ($_GET["data"]);
            if (!empty($_POST["data"]))
                $data = ($_POST["data"]);
            log_message('LOG', "异步加密返回值:" . $data, 'xianfengwithdraw');
            $this->load->library('xianfengpay');
            if (ENVIRONMENT === 'production') {
                $key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg8gP5+tNBhgI7glDZI5myXdvCW1dCyWwVjZIDUvCax/t9qZVRPujhFX77QvPSicTVDKu9AQ4pfE8NXGme+J9vfqeCYLIyjE0ewKt0FuTFfHZ9NBS/fMBdG6j9A5D1drM6gVYMFnUYu+QHpomsEaBIuXWvJB0PeEx3jWVKLYRXUe4mhgiHeTsRBlA23hSmmSX+X9+8NJLfmbr2fFn4X+qBplQcqukQBeZm34bWBDEh6R+VQIT1FQMhduDez06GaFBcPVqtv7ns0cB1YV07knNKDQO7N4+CzofvFKMVbXN03vwuEFLHU6WkfEx7vMopqQCXqy6+1hDb3af1euMW5gxdwIDAQAB';
            } else {
                $key = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQChFetx5+VKDoEXzZ+5Wozt3MfWMM/TiKMlWmAKXBViv8/e6j6SU/lSlWkMajd59aiWczs+qf9dMuRpe/l9Qke9DnVMn24JNLXjWD+y+w3yKRwd3CTtF7gx8/ToZl5XqFIT5YB1QfQCdAf8Z18IdQrJIijs8ssczY/RfqKZLo+KLQIDAQAB';
            }
            $result = $this->xianfengpay->notice($data, $key);
            log_message('LOG', "异步解密返回值:" . json_encode($result), 'xianfengwithdraw');
            $this->load->model('withdraw_model');
            $withdraw = $this->withdraw_model->getStatus($result['merchantNo']);
            if ($result['status'] == 'S') {
                if ($withdraw['status'] != '2') {
                    $this->withdraw_model->update_check(array(
                        'start_check' => date('Y-m-d H:i:s', time()),
                        'status' => 5,
                            ), array(
                        'trade_no' => $result['merchantNo'],
                    ));
                    log_message('LOG', "提现记录:订单号{$result['merchantNo']},提现成功" . json_encode($result), 'xianfengwithdraw');
                }
            } else {
                $this->withdraw_model->updateColumn(array(
                    'remark' => $result['resMessage'],
                        ), array(
                    'trade_no' => $result['merchantNo'],
                ));
                $content = "提款订单号{$result['merchantNo']}，提款人{$withdraw['real_name']}，{$result['resMessage']}，未成功。请核实后手动处理。";
                $this->withdraw_model->alertEmail($content);
            }
        }
        echo "SUCCESS";
    }
}
