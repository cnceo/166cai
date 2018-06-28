<?php
/**
 * 支付接口调测例子
 * ================================================================
 * index 进入口，方法中转
 * submitOrderInfo 提交订单信息
 * queryOrder 查询订单
 * 
 * ================================================================
 */
require('Utils.class.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');

Class Request{
    //$url = 'http://192.168.1.185:9000/pay/gateway';

    private $resHandler = null;
    private $reqHandler = null;
    private $pay = null;
    private $cfg = null;
    
    public function __construct($pay_config){
        $this->Request($pay_config);
    }

    public function Request($pay_config){
        $this->resHandler = new ClientResponseHandler();
        $this->reqHandler = new RequestHandler();
        $this->pay = new PayHttpClient();
        $this->cfg = $pay_config;

        $this->reqHandler->setGateUrl($this->cfg['url']);
        $this->reqHandler->setKey($this->cfg['key']);
    }
    
    public function index(){
        $method = isset($_REQUEST['method'])?$_REQUEST['method']:'submitOrderInfo';
        switch($method){
            case 'submitOrderInfo'://提交订单
                $this->submitOrderInfo();
            break;
            case 'queryOrder'://查询订单
                $this->queryOrder();
            break;
            case 'submitRefund'://提交退款
                $this->submitRefund();
            break;
            case 'queryRefund'://查询退款
                $this->queryRefund();
            break;
            case 'callback':
                $this->callback();
            break;
        }
    }
    
    /**
     * 提交订单信息
     */
    public function submitOrderInfo($params){
        $this->reqHandler->setReqParams($params);
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        
        $data = Utils::toXml($this->reqHandler->getAllParameters());
        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call())
        {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign())
            {
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0)
                {
                	$result = array(
                		'code_img_url' => $this->resHandler->getParameter('code_img_url'),
                		'codeUrl' => $this->resHandler->getParameter('code_url'),
                		'code_status' => $this->resHandler->getParameter('code_status'),
                	);
                    return $result;
                }
                else
                {
                    log_message('log', print_r(array('msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')), true), 'wftPayCallback');
                    die('交易出现异常，请稍后重试');
                }
            }

            log_message('log', print_r(array('msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')), true), 'wftPayCallback');
            die('交易出现异常，请稍后重试');
        }
        else
        {
        	log_message('log', print_r(array('msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()), true), 'wftPayCallback');
        	die('交易出现异常，请稍后重试');
        }
    }
    
    /**
     * SDK提交订单信息
     */
    public function sdkOrderInfo($params)
    {
    	$this->reqHandler->setReqParams($params);
    	$this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
    	$this->reqHandler->createSign();//创建签名
    
    	$data = Utils::toXml($this->reqHandler->getAllParameters());
    	$this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
    	$result = array(
    		'code' => false,
    		'msg' => '交易出现异常',
    		'data' => array(),
    	);
    	if($this->pay->call())
    	{
    		$this->resHandler->setContent($this->pay->getResContent());
    		$this->resHandler->setKey($this->reqHandler->getKey());
    		if($this->resHandler->isTenpaySign())
    		{
    			//当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
    			if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0)
    			{
    				$res = $this->resHandler->getAllParameters();
    				$result = array(
    					'code' => true,
    					'msg' => '交易成功',
    					'data' => json_decode($res['pay_info'], true),
    				);
    				return $result;
    			}
    			else
    			{
    				log_message('log', print_r(array('msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')), true), 'wftPayCallback');
    			}
    		}
    
    		log_message('log', print_r(array('msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')), true), 'wftPayCallback');
    	}
    	else
    	{
    		log_message('log', print_r(array('msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()), true), 'wftPayCallback');
    	}
    	
    	return $result;
    }

    /**
     * 查询订单
     */
    public function queryOrder($params)
    {
        $this->reqHandler->setReqParams($params);
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        $result = array(
        	'code' => false,
        	'data' => array(),
        );
        if($this->pay->call())
        {
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign())
            {
            	$result = array(
            		'code' => true,
            		'data' => $this->resHandler->getAllParameters(),
            	);
                return $result;
            }
            
            $result['data']['message'] = $this->resHandler->getParameter('message');
        }
        else
        {
        	$result['data']['message'] = $this->pay->getErrInfo();
        }
        
        return $result;
    }
    
    /**
     * 提交退款
     */
    public function submitRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        $reqParam = $this->reqHandler->getAllParameters();
        if(empty($reqParam['transaction_id']) && empty($reqParam['out_trade_no'])){
            echo json_encode(array('status'=>500,
                                   'msg'=>'请输入商户订单号或威富通订单号!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refund');//接口类型：unified.trade.refund(如要开发退款接口以这个为准，文档没有及时更新)
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        $this->reqHandler->setParameter('op_user_id',$this->cfg->C('mchId'));//必填项，操作员帐号,默认为商户号

        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                 'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                 'out_refund_no'=>$this->resHandler->getParameter('out_refund_no'),
                                 'refund_id'=>$this->resHandler->getParameter('refund_id'),
                                 'refund_channel'=>$this->resHandler->getParameter('refund_channel'),
                                 'refund_fee'=>$this->resHandler->getParameter('refund_fee'),
                                 'coupon_refund_fee'=>$this->resHandler->getParameter('coupon_refund_fee'));*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('提交退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'提交退款成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code').' Error Message:'.$this->resHandler->getParameter('err_msg')));
                    exit();
                }
            }
            echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('status').' Error Message:'.$this->resHandler->getParameter('message')));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }

    /**
     * 查询退款
     */
    public function queryRefund(){
        $this->reqHandler->setReqParams($_POST,array('method'));
        if(count($this->reqHandler->getAllParameters()) === 0){
            echo json_encode(array('status'=>500,
                                   'msg'=>'请输入商户订单号,威富通订单号,商户退款单号,威富通退款单号其中一个!'));
            exit();
        }
        $this->reqHandler->setParameter('version',$this->cfg->C('version'));
        $this->reqHandler->setParameter('service','unified.trade.refundquery');//接口类型：unified.trade.refundquery
        $this->reqHandler->setParameter('mch_id',$this->cfg->C('mchId'));//必填项，商户号，由威富通分配
        $this->reqHandler->setParameter('nonce_str',mt_rand(time(),time()+rand()));//随机字符串，必填项，不长于 32 位
        
        $this->reqHandler->createSign();//创建签名
        $data = Utils::toXml($this->reqHandler->getAllParameters());//将提交参数转为xml，目前接口参数也只支持XML方式

        $this->pay->setReqContent($this->reqHandler->getGateURL(),$data);//设置请求地址与请求参数
        if($this->pay->call()){
            $this->resHandler->setContent($this->pay->getResContent());
            $this->resHandler->setKey($this->reqHandler->getKey());
            if($this->resHandler->isTenpaySign()){
                //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
                if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0){
                    /*$res = array('transaction_id'=>$this->resHandler->getParameter('transaction_id'),
                                  'out_trade_no'=>$this->resHandler->getParameter('out_trade_no'),
                                  'refund_count'=>$this->resHandler->getParameter('refund_count'));
                    for($i=0; $i<$res['refund_count']; $i++){
                        $res['out_refund_no_'.$i] = $this->resHandler->getParameter('out_refund_no_'.$i);
                        $res['refund_id_'.$i] = $this->resHandler->getParameter('refund_id_'.$i);
                        $res['refund_channel_'.$i] = $this->resHandler->getParameter('refund_channel_'.$i);
                        $res['refund_fee_'.$i] = $this->resHandler->getParameter('refund_fee_'.$i);
                        $res['coupon_refund_fee_'.$i] = $this->resHandler->getParameter('coupon_refund_fee_'.$i);
                        $res['refund_status_'.$i] = $this->resHandler->getParameter('refund_status_'.$i);
                    }*/
                    $res = $this->resHandler->getAllParameters();
                    Utils::dataRecodes('查询退款',$res);
                    echo json_encode(array('status'=>200,'msg'=>'查询成功,请查看result.txt文件！','data'=>$res));
                    exit();
                }else{
                    echo json_encode(array('status'=>500,'msg'=>'Error Code:'.$this->resHandler->getParameter('err_code')));
                    exit();
                }
            }
            echo json_encode(array('status'=>500,'msg'=>$this->resHandler->getContent()));
        }else{
            echo json_encode(array('status'=>500,'msg'=>'Response Code:'.$this->pay->getResponseCode().' Error Info:'.$this->pay->getErrInfo()));
        }
    }
    
    /**
     * 提供给威富通的回调方法
     */
    public function callback(){
        $xml = file_get_contents('php://input');
        $this->resHandler->setContent($xml);
        $this->resHandler->setKey($this->cfg['key']);
        $result = array(
        	'code' => false,
        	'data' => array(),
        );
        if($this->resHandler->isTenpaySign())
        {
            if($this->resHandler->getParameter('status') == 0 && $this->resHandler->getParameter('result_code') == 0)
            {
            	$result = array(
            		'code' => true,
            		'data' => $this->resHandler->getAllParameters(),
            	);
            	
				return $result;
            }
        }
        
        return $result;
    }
}
?>