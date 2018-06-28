<?php

if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * 新版支付
 * @date:2017-11-07
 */
require_once APPPATH . '/core/CommonController.php';
class Aso extends CommonController 
{
	public function __construct() 
    {
        parent::__construct();
        $this->load->model('api_aso_model');
    }

    private $salt = 'aso166cai';

    // 去重接口
    public function filter()
    {
        $postData = $this->input->post(null, true);

        /*
        $postData = array(
            'idfa'  =>  '5A58EF1E-EEF2-478D-94EE-709B98407589, 5A58EF1E-EEF2-478D-94EE-709B98407589111',
        );
        */

        $filter = array();
        if(!empty($postData['idfa']))
        {
            $postData['idfa'] = preg_replace('/\s/', '', $postData['idfa']);
            $idfas = explode(',', strtoupper($postData['idfa']));
            $info = $this->api_aso_model->checkIdfa($idfas);
            $info = $this->getParams($info);
            
            foreach ($idfas as $idfa) 
            {
                $filter[$idfa] = (isset($info[$idfa])) ? '1' : '0';
            }
        }
        die(json_encode($filter));
    }

    public function getParams($info = array())
    {
        $data = array();
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                $data[$items['idfa']] = $items;
            }
        }
        return $data;
    }

    // 点击通知接口
    public function notice()
    {
        $postData = $this->input->post();

        $result = array(
            'status'    =>  FALSE,
            'message'   =>  '请求参数错误' ,    
        );

        /*
        $postData = array(
            'appid'         =>  '1108268497',
            'idfa'          =>  '555EA709-739C-4960-BF46-F1FAF31C04B1',
            'callback'      =>  'http://api.plat.adjuz.net/callback?adid=53659&idfa=555EA709-739C-4960-BF46-F1FAF31C04B1&otherclickid=',
            'timestamp'     =>  '1510314847',
            'sign'          =>  'a18ec1944210d74745e101816ce7f77f',
            'source'        =>  'juzhang',
        );
        */

        if(!empty($postData) && !empty($postData['idfa']) && !empty($postData['callback']))
        {
            if($this->validateSign($postData))
            {
                // 去空格
                $postData['idfa'] = preg_replace('/\s/', '', $postData['idfa']);
                // 激活设备号
                $info = array(
                    'idfa'          =>  strtoupper($postData['idfa']),
                    'callback'      =>  $postData['callback'],
                    'cstate'        =>  1,
                );
                $rows = $this->api_aso_model->activateIdfa($info);
                if($rows > 0)
                {
                    $result = array(
                        'status'    =>  TRUE,
                        'message'   =>  '激活成功' ,  
                    );
                }
                else
                {
                    $result = array(
                        'status'    =>  FALSE,
                        'message'   =>  '激活设备已存在' ,  
                    );
                }
            }  
            else
            {
                $result = array(
                    'status'    =>  FALSE,
                    'message'   =>  '签名验证错误' ,  
                );
            }       
        }
        die(json_encode($result));
    }

    // 签名校验
    public function validateSign($data)
    {
        $check = FALSE;
        $sign = $data['sign'];
        unset($data['sign']);
        $params = '';
        if(!empty($data))
        {
            ksort($data);
            foreach ($data as $key => $val) 
            {
                $params .= $key . "=" . $val . "&";
            }

            if(md5($params . $this->salt) == $sign)
            {
                $check = TRUE;
            }
        }
        return $check;
    }
}

