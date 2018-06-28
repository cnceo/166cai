<?php
/**
 * 汇聚无限支付宝h5
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class HjPay extends RechargeAbstract
{
    
    private $config;
    private $defaultParams = array();
    private $values = array();
    private $queryMethod ;//查询方法
    private $billMethod ;//对账单方法
    private $merName ;

    public function __construct($config = array())
    {
        $this->config = $config;
        $this->key = $config['mch_key'];
        $this->defaultParams['appid'] = $this->config['appId'];
        $this->defaultParams['body'] =  $this->config['mhtOrderName'];
        $this->defaultParams['mch_id'] = $this->config['mch_id'];
        $this->defaultParams['method'] = isset($this->config['pay_method']) ? $this->config['pay_method'] : 'mbupay.alipay.wap';
        $this->defaultParams['nonce_str'] = $this->getNonceStr() ;
        $this->defaultParams['notify_url'] = $this->config['notifyUrl'];
        $this->defaultParams['version'] = $this->config['version'];
        $this->queryMethod = isset($this->config['query_method']) ? $this->config['query_method'] : 'mbupay.alipay.query';
        $this->billMethod = isset($this->config['bill_method']) ? $this->config['bill_method'] : 'mbupay.alipay.bill';
        $this->merName = isset($this->config['mer_name']) ? $this->config['mer_name'] : '汇聚无限支付宝商户号';
    }
    //表单提交第三方方法
    public function  formSubmit($params){}
    /**
     * [requestHttp 生成请求]
     * @author LiKangJian 2017-07-05
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function requestHttp($params)
    {
        $this->defaultParams['out_trade_no'] = $params['trade_no'];
        $this->defaultParams['total_fee'] = $params['money'];
        if(isset($this->config['return_url']))
        {
            $this->defaultParams['return_url'] = $this->config['return_url'].'/'.$params['trade_no'];
        } 
        $reqParams = $this->defaultParams;
        $this->defaultParams['sign'] = $reqParams['sign'] = $this->makeSign();
        $xml = $this->toXml($reqParams);
        $response = $this->postXmlCurl($xml, $this->config['gateway_url'], 30);
        $result = $this->xmlToArray($response);
        $result['jump_url'] = $this->config['jump_url'].'?trade_no='.$params['trade_no'];
        $returnData = array(
            'code' => true,
            'msg' => '请求成功',
            'data' => $result
        );
        if($result['return_code'] !='SUCCESS' || $result['result_code'] != 'SUCCESS')
        {
            $this->values = $this->defaultParams;
            $returnData['code'] = false;
            $returnData['msg'] = '请求失败';

            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$this->merName.'：'. $this->config['mch_id'] . '请求失败，请及时留意。<br/>uid:'.$params['uid'].',请求地址：' . $this->config['gateway_url'] . '<br/>请求返回数据：' . $this->printJson($result);
            $this->alertEmail($message);
            $this->log($result, __CLASS__, $this->config['mch_id'], __FUNCTION__);
        }else{
            $this->values = $result;
        }
        return $returnData;
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
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $res = $this->fromXml($xml);
        $resdata = array();
        if($res['result_code'] == 'SUCCESS' && $res['return_code'] == 'SUCCESS')
        {
            $payData = array(
                'trade_no' => $res['out_trade_no'],
                'pay_trade_no' => 'hj_' . $res['out_trade_no'],
                'status' => '1',    // 成功
            );
            $returnData['code'] = true;
            $returnData['data'] = $payData;
            
            return $returnData;
        }else{
            // TODO 验签失败 这里记录错误日志或邮件报警
            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>'.$this->merName.'：'. $this->config['mch_id'] . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson(simplexml_load_string($xml));
            $this->alertEmail($message);
            $this->log($res, __CLASS__, $this->config['mch_id'], __FUNCTION__);
        }

        return $returnData;
    }

    /**
     * [syncCallback 充值同步通知]
     * @author LiKangJian 2017-07-07
     * @return [type] [description]
     */
    public function syncCallback()
    {
        $returnData = array(
            'code' => false,
            'errMsg' => 'failure',
            'succMsg' => 'success',
            'data' => array(),
        );
        $get = $_GET;
        $get['flag'] = true;
        $res = $this->getQueryRes($get);
        $param = array();
        if($res['result_code'] == 'SUCCESS' && $res['return_code'] == 'SUCCESS' && $res['trade_state'] == 'SUCCESS' )
        {

            $payData = array(
                'trade_no' => $res['out_trade_no'],
            );
            $returnData = array(
                'code' => true,
                'errMsg' => 'failure',
                'succMsg' => 'success',
                'data' => $payData,
            );
                
            return $returnData;
        }
        else
        {
            //存在支付中的状态暂不报警
            //$message = '汇聚无限支付宝商户号：'. $this->config['mch_id'] . '同步通知接口接收数据异常，请及时留意。<br/>请求返回数据：' . json_encode($res);
            //$this->alertEmail($message);
            //$this->log($res, __CLASS__, $this->config['mch_id'], __FUNCTION__);
        }
        
        return $returnData;
    }
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
        $res = $this->getQueryRes($params);
        $returnData = array(
            'code' => false,
            'msg'  => '操作失败',
            'data' => array('code' => 1, 'msg' => '操作失败'),
        );
        if($res['result_code'] == 'SUCCESS' && $res['result_code'] == 'SUCCESS')
        {
            $ptype = array('hjZfbPay' => '汇聚无限支付宝','hjpay' => '汇聚无限支付宝','hjWxWap' =>'微信H5-兴业银行','hjZfbWap' => '支付宝H5-鸿粤浦发银行','hjZfbSh' => '支付宝H5-上海银行');
            $pstatus = array('SUCCESS' => '已付款', 'REFUND' => '转入退款', 'NOTPAY' => '未支付', 'CLOSED' => '已关闭', 'REVERSE' => '已冲正', 'REVOK' => '已撤销', 'REVOKED' => '已冲正', 'USERPAYING' => '用户支付中', 'PAYERROR' => '支付失败');
            $payData = array(
                'code' => '0',
                'ptype' => $ptype[$params['additions']],
                'pstatus' => $pstatus[$res['trade_state']],
                'ptime' => $res['trade_state'] == 'SUCCESS' ? $params['created'] : '',
                'pmoney' => number_format($res['total_fee'] / 100, 2, ".", ","),
                'pbank' => '',
                'ispay' => $res['trade_state'] == 'SUCCESS' ? true : false,
                'pay_trade_no' => $res['transaction_id'],
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
            $returnData['data']['msg'] = $res['return_msg'] ? $res['return_msg'] : $res['err_code_des'] . $res['err_code'];

        } 
        return $returnData;
    }
    
    /**
     * 对账数据下载
     * @param unknown_type $params
     */
    public function queryBill($params)
    {
        $queryParams = array();
        $queryParams['appid'] = $this->config['appId'];
        $queryParams['mch_id'] = $this->config['mch_id'];
        $queryParams['method'] = $this->billMethod;
        $queryParams['nonce_str'] = $this->getNonceStr() ;
        $queryParams['version'] = '2.0.1';
        $queryParams['bill_date'] = $params['bill_date'];
        $this->defaultParams = $queryParams;
        $this->defaultParams['sign'] =$queryParams['sign'] = $this->makeSign();
        $xml = $this->toXml($queryParams);
        $response = $this->postXmlCurl($xml, $this->config['gateway_url'], 60);
        //返回结果是xml则不成功
        if(strpos($response, 'xml') == true)
        {
            return array('code' => false, 'msg' => '接口请求失败');
        }
        
        return array('code' => true, 'msg' => '操作成功', 'data' => $response);
    }
    
    //退款提交方法
    public function refundSubmit($params){}
    //退款订单查询方法
    public function queryRefund($params){}
    /**
     * [getQueryRes 订单查询方法]
     * @author LiKangJian 2017-07-07
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function getQueryRes($params)
    {
        $queryParams = array();
        $queryParams['appid'] = $this->config['appId'];
        $queryParams['mch_id'] = $this->config['mch_id'];
        $queryParams['method'] = $this->queryMethod;
        $queryParams['nonce_str'] = $this->getNonceStr() ;
        $queryParams['version'] = $this->config['version'];
        $queryParams['out_trade_no'] = $params['trade_no'];
        $this->defaultParams = $queryParams;
        $this->defaultParams['sign'] =$queryParams['sign'] = $this->makeSign();
        $xml = $this->toXml($queryParams);
        $response = $this->postXmlCurl($xml, $this->config['gateway_url'], 30);
        $result = $this->xmlToArray($response,$params['flag']);

        return $result;
    }

    /**
     * [xmlToArray 将xml转为array]
     * @author LiKangJian 2017-07-07
     * @param  [type]  $xml  [description]
     * @param  boolean $flag [description]
     * @return [type]        [description]
     */
    private function xmlToArray($xml,$flag = true)
    {   
        $res = $this->fromXml($xml);
        //无须验签
        if(!$flag)
        {
            return $res;
        }
        $checkRes = $this->checkSign();
        if($checkRes['code'] == true && isset($res['return_code']) && $res['return_code'] == 'SUCCESS')
        {
            return $res;
        }else{
            return $this->defaultParams;
        }
    }
    /**
     * [makeSign 生成签名]
     * @author LiKangJian 2017-07-05
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    private function makeSign()
    {   
        //签名步骤一：按字典序排序参数
        ksort($this->defaultParams);
        $string = urldecode($this->toUrlParams());
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * [checkSign 验签]
     * @author LiKangJian 2017-07-06
     * @return [type] [description]
     */
    private function checkSign()
    {
       
        $returnData = array(
            'code' => false,
            'msg'  => '验签失败',
        );
        if(array_key_exists('sign', $this->defaultParams) 
            && $this->makeSign() == $this->defaultParams['sign']
            )
        {
            $returnData['code'] = true;
            $returnData['msg'] = '验签成功';
        }

        return $returnData;
    }
    /**
     * [getNonceStr 产生随机字符串，不长于32位]
     * @author LiKangJian 2017-07-06
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    private function getNonceStr($length = 32) 
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  
        {  
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        } 
        return $str;
    }
    /**
     * [ToXml 输出xml字符]
     * @author LiKangJian 2017-07-06
     * @param  [type] $reqParams [description]
     */
    private function toXml($reqParams)
    {
        if(!is_array($reqParams) || count($reqParams) <= 0)
        {
            return '';
        }
        $xml = "<xml>";
        foreach ($reqParams as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml; 
    }
    /**
     * [fromXml 将xml转为array]
     * @author LiKangJian 2017-07-06
     * @param  [type] $xml [description]
     * @return [type]      [description]
     */
    private function fromXml($xml)
    {   
        if(!$xml)
        {
            return array();
        }
        //将XML转为array 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }
    /**
     * [toUrlParams 格式化参数格式化成url参数]
     * @author LiKangJian 2017-07-07
     * @return [type] [description]
     */
    private function toUrlParams()
    {
        $buff = "";
        foreach ($this->defaultParams as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v))
            {
                $buff .= $k . "=" . $v . "&";
            }
        }
        
        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * [postXmlCurl 提交请求]
     * @author LiKangJian 2017-07-07
     * @param  [type]  $xml    [需要post的xml数据]
     * @param  [type]  $url    [请求url]
     * @param  integer $second [url执行超时时间，默认30s]
     * @return [type]          [description]
     */
    private  function postXmlCurl($xml, $url,$second = 30)
    {       
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //如果有配置代理这里就设置代理
        if($this->config['curl_proxy_host'] != "0.0.0.0" && $this->config['curl_proxy_port'] != 0)
        {
            curl_setopt($ch,CURLOPT_PROXY, $this->config['curl_proxy_host']);
            curl_setopt($ch,CURLOPT_PROXYPORT, $this->config['curl_proxy_port']);
        }
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);//TRUE
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//2严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        if ($curl_errno || (!empty($curl_error)))
        {
            // 记录错误日志
            log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($xml) . "\tcurlInfo:" . json_encode(curl_getinfo($ch)), 'recharge/curl_error');
        }
        //返回结果
        curl_close($ch);
        
        return $data;
    }
}