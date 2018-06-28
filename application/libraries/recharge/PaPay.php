<?php
/**
 * 厦门银行 支付宝扫码 充值服务类
 * @author Administrator
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class PaPay extends RechargeAbstract
{
    /**
     * 商户id
     * @var unknown_type
     */
    private $merId;
        /**
     * 秘钥
     * @var unknown_type
     */
    private $key;
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
        $this->merId = $config['mch_id'];
        $this->key = $config['key'];
        $this->payGateway = $config['url'];
        unset($config['key']);
        unset($config['url']);
        $this->config = $config;
    }
    /**
     * [formSubmit 表单提交]
     * @author LiKangJian 2017-06-16
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function formSubmit($params)
    {
        
        $res = $this->requestHttp($params);
        if($res['code'] != 1) return array();
        $defaultParams = $res['data'];
        $defaultParams['submit_url'] = $params['submit_url'];
        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => array('charset' => 'utf-8','html' => $this->createForm($defaultParams),'params'=>$defaultParams),
        );
        return $returnData;
    }
    /**
     * [createForm 构建表单]
     * @author LiKangJian 2017-06-16
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function createForm($params)
    {
        $html =  '<body onLoad="document.autoForm.submit();">';
        $html .= '<form name="autoForm" action="'.$params['submit_url'].'" method="post">';
        $formParams = array();
        $formParams['orderId'] = $params['out_no'];
        $formParams['orderTime'] = date('Y-m-d H:i:s',$params['time']);
        $formParams['txnAmt'] = $params['trade_amount'];
        $formParams['codeUrl'] = $params['trade_qrcode'];
        foreach ($formParams as $key => $value)
        {

            $html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/><br/>';
            
        }
        $html .= '</form></body>';

        return $html;
    }
    /**
     * 接口请求第三方抽象方法
     */
    public function requestHttp($params)
    {
        $returnData = array(
            'code' => false,
            'msg'  => '请求错误',
            'data' => $params,
        );
        $res = $this->api("payorder",$this->getParams($params));
        if($res['errcode']==0)
        {
            $res['data']['time']=$res['timestamp'] ;
            $returnData = array(
                    'code' => true,
                    'msg' => '请求成功',
                    'data' => $res['data'],
            );
        }
        else
        {
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求平安银行支付宝商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway.'/payorder' . '<br/>请求返回数据：' . $this->printJson($res);
            $this->alertEmail($message);
            $this->log($res, __CLASS__, $this->merId, __FUNCTION__);
        }
        return $returnData;
    }
    /**
     * [getParams 组建基本参数]
     * @author LiKangJian 2018-02-05
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    private function getParams($data)
    {
        $payPara = array();
        $payPara['open_id'] = $this->merId;
        $payPara['timestamp'] = time();
        $payPara['out_no'] = $data['trade_no'];
        $payPara['out_no'] = $data['trade_no'];
        $payPara['pmt_tag'] = $this->config['pmt_tag'];
        $payPara['pmt_name'] = $this->config['pmt_name'];
        $payPara['ord_name'] = $this->config['pmt_name'];
        $payPara['original_amount'] = $data['money'];
        $payPara['trade_amount'] = $data['money'];
        $payPara['notify_url'] = $this->config['notify_url'];

        return $payPara;
    }
    /**
     * [signs 签名]
     * @author LiKangJian 2018-02-06
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function signs($array)
    {
        $signature = array();
        foreach($array as $key=>$value)
        {
            $signature[$key]=$key.'='.$value;
        }
        $signature['open_key']='open_key'.'='.$this->key;
        ksort($signature);
        #先sha1加密 在md5加密
        $sign_str = md5(sha1(implode('&', $signature)));
        return $sign_str;
    }
    /**
     * [signRas 整理数据加密]
     * @author LiKangJian 2018-02-06
     * @param  [type] $array      [description]
     * @param  [type] $signauture [description]
     * @return [type]             [description]
     */
    public function signRas($array,$signauture=OPENSSL_ALGO_SHA1)
    {
        $signature = array();
        foreach($array as $key=>$value)
        {
            $signature[$key]=$key.'='.$value;
        }
        $signature['open_key']='open_key'.'='.$this->key;
        ksort($signature);
        $sign_str = implode('&', $signature);
        $keyFile = dirname(__FILE__).'/papaykey/private.key';
        $privatekey = openssl_get_privatekey(file_get_contents($keyFile));
        openssl_sign($sign_str,$sign_str,$privatekey,$signauture);
        return bin2hex($sign_str);
    }
    /**
     * [api 统一处理接口]
     * @author LiKangJian 2018-02-06
     * @param  [type] $url  [description]
     * @param  [type] $post [description]
     * @return [type]       [description]
     */
    public function api($url,$post)
    {
        #必填参数
        $data = array(
            'open_id'=>$this->merId,
            'timestamp'=>time(),
         );
        $data['data']= $post;
        unset($data['sign_type']);
        if(is_array($data))
        {
            $data['data'] = $this->encrypt(json_encode($post),$this->key);
            if ($url == 'payrefund' || $url == 'paycancel')
            {
                $data['sign_type'] = $post['sign_type'];
                if ($post['sign_type']=='RSA')
                {
                    $data['sign'] = $this->signRas($data);
                }elseif ($post['sign_type']=='RSA2')
                {
                    $data['sign'] = $this->signRas($data,OPENSSL_ALGO_SHA256);
                }
            }else{
                $data['sign'] = $this->signs($data);
            }
        }else{
            $data=null;
        }
        $result = $this->curlPost($url,$data);
        if(isset($result['data']))
        {
            $result['data']=$this->decrypt($result['data'], $this->key);
            $result['data']=json_decode($result['data'],true);
        }
        if(isset($result['sign'])) unset($result['sign']);
        return $result;
    }
    /**
     * [encrypt 加密]
     * @author LiKangJian 2018-02-05
     * @param  [type] $input [description]
     * @param  [type] $key   [description]
     * @return [type]        [description]
     */
    private  function encrypt($input, $key) 
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input = $this->pkcs5Pad($input, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = strtoupper(bin2hex($data));
        return $data;
    }
    /**
     * [pkcs5Pad description]
     * @author LiKangJian 2018-02-05
     * @param  [type] $text      [description]
     * @param  [type] $blocksize [description]
     * @return [type]            [description]
     */
    private  function pkcs5Pad($text, $blocksize) 
    {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    /**
     * [decrypt 解密]
     * @author LiKangJian 2018-02-05
     * @param  [type] $sStr [description]
     * @param  [type] $sKey [description]
     * @return [type]       [description]
     */
    public  function decrypt($sStr, $sKey) 
    {
        $sStr = $this->hex2bins($sStr);
        $decrypted= mcrypt_decrypt(
            MCRYPT_RIJNDAEL_128,
            $sKey,
            $sStr,
            MCRYPT_MODE_ECB
        );
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s-1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }
    /**
     * [hex2bins 十六进制转2进制]
     * @author LiKangJian 2018-02-06
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function hex2bins($str)
    {  
        $len = strlen($str)/2;  
        $re = '';  
        for($i=0;$i<$len;$i++){  
            $pos = $i*2;  
            $re .= chr(hexdec(substr($str,$pos,1))<<4) | chr(hexdec(substr($str,$pos+1,1)));  
        }  
        return $re;  
    }  

    /**
     * [curlPost 请求]
     * @author LiKangJian 2018-02-05
     * @param  [type] $url        [description]
     * @param  [type] $data       [description]
     * @param  string $cacert_url [description]
     * @return [type]             [description]
     */
    public function curlPost($url, $data, $cacert_url = '')
    {
        //启动一个CURL会话
        $ch = curl_init();
        // 设置curl允许执行的最长秒数
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        //忽略证书
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        // 获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL,$this->payGateway.'/'.$url);
        //发送一个常规的POST请求。
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch, CURLOPT_HEADER,0);//是否需要头部信息（否）
        // 执行操作
        $result = curl_exec($ch);
        if($url=='bill/downloadbill' && $result)
        {
            curl_close($ch);
            return $result;
        } 
        $request_header = curl_getinfo($ch);
        if($result){
            curl_close($ch);
            #将返回json转换为数组
            $arr_result=json_decode($result,true);
            if(!is_array($arr_result))
            {
                $arr_result['errcode']=1;
                $arr_result['msg']='服务器繁忙，请稍候重试';
                // 记录错误日志
               log_message('log', "errno:{$arr_result['errcode']}\terror:{'服务器返回数据格式错误'}\trequestData:" . json_encode($result) . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
            }
        }else{
            $err_str=curl_error($ch);
            curl_close($ch);
            $arr_result['errcode']=1;
            $arr_result['msg']='服务器繁忙，请稍候重试';
            // 记录错误日志
            log_message('log', "errno:{$err_str}\terror:{'服务器无响应'}\trequestData:" . json_encode($err_str) . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
        }
        #返回数据
        return $arr_result;
    }
    /**
     * 异步回调抽象方法
     */
    public function notify()
    {
        $returnData = array(
            'code' => false,
            'errMsg' => 'failure',
            'succMsg' => 'success',
            'data' => array(),
        );
        $data = $_POST;
        $res = json_decode($data['trade_result'],true);
        if($res['alipay_trade_query_response']['code'] == '10000' && $res['alipay_trade_query_response']['msg'] == 'Success')
        {
            $payData = array(
                'trade_no' => $data['out_no'],
                'pay_trade_no' => 'pa_' . $data['ord_no'],
                'status' => '1',    // 成功
            );
            $returnData['code'] = true;
            $returnData['data'] = $payData;
            return $returnData;
        }else{
            // TODO 验签失败 这里记录错误日志或邮件报警
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$this->merName.'：'. $this->config['mch_id'] . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($data);
            $this->alertEmail($message);
            $this->log($res, __CLASS__, $this->config['mch_id'], __FUNCTION__);
        }

        return $returnData;

    }

    /**
     * 同步回调抽象方法
     */
    public function syncCallback(){}
    
    /**
     * 订单查询抽象方法
     */
    public function queryOrder($params)
    {
        $data = array('out_no' => $params['trade_no']);
        $res = $this->api("paystatus",$data);
        $returnData = array(
            'code' => false,
            'msg'  => '操作失败',
            'data' => array('code' => 1, 'msg' => '操作失败'),
        );
        //成功
        if($res['errcode'] == 0 )
        {
            $ptype = array('payPaZfb' => '平安银行支付宝');
            $status = $res['data']['status'];
            $pstatus = array('1' => '已付款', '2' => '待支付', '4' => '已取消', '9' => '等待用户输入密码确认');
            $payData = array(
                'code' => '0',
                'ptype' => $ptype[$params['additions']],
                'pstatus' => $pstatus[$status],
                'ptime' => $status == 1 ? $params['created'] : '',
                'pmoney' => number_format($res['data']['trade_amount'] / 100, 2, ".", ","),
                'pbank' => '',
                'ispay' => $status == 1 ? true : false,
                'pay_trade_no' => $res['data']['ord_no'],
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
            $returnData['data']['msg'] = $res['msg'];

        } 
        return $returnData;
    }
    
    /**
     * 退款提交抽象方法
     */
    public function refundSubmit($params){}
    
    /**
     * 退款订单查询抽象方法
     */
    public function queryRefund($params){}
    /**
     * [queryBill 对账文件拉取]
     * @author LiKangJian 2018-02-06
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function queryBill($params)
    {
        $defaultParams = array(
            'day'       =>  $params['bill_date'],
            'pmt_tag'   => 'AlipayPAZH',
        );
        $res = $this->api('bill/downloadbill',$defaultParams);
        if(!empty($res))
        {
            return array('code' => true, 'msg' => '操作成功', 'data' => $res);
        }
        
        return array('code' => false, 'msg' => '接口请求失败');
    }
}