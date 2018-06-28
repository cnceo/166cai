<?php

/*
 * 批量处理体现
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Cli_Tixian extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('withdraw_model');
    }

    public function index()
    {
        $start = "2018-06-22 08:23:00";
        $end = "2018-06-25 15:27:41";
        $withdraws = $this->withdraw_model->getAllNeedWd($start, $end);
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
        foreach ($withdraws as $withdraw) {
            $params = array(
                "service" => 'REQ_WITHDRAW_QUERY_BY_ID',
                'secId' => 'RSA',
                'version' => '4.0.0',
                'merchantId' => $merchantId,
                'key' => $key,
                'url' => $url,
                "merchantNo" => $withdraw['trade_no'], //流水号
            );
            $result = $this->xianfengpay->queryWithdraw($params);
            if($result['status'] == 'S' && $result['resMessage'] == '成功'){
                $this->withdraw_model->setWithDrawSuc($withdraw);
            }
        }
    }

}
