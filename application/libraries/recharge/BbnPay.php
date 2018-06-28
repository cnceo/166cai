<?php
/**
 * 浦发白名单
 * @author yindefu
 *
 */
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class BbnPay extends RechargeAbstract
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
	 * 秘钥类型
	 * @var unknown_type
	 */
	private $sign_type;
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
		$this->sign_type = $config['sign_type'];
		unset($config['key']);
		unset($config['url']);
		unset($config['sign_type']);
		$this->config = $config;
	}
	
	/**
	 * curl请求支付信息
	 * @see RechargeAbstract::requestHttp()
	 */
	public function requestHttp($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '请求错误',
			'data' => $params,
		);
		
		$defaultParams = $this->getParams();
                $backurl = $defaultParams['backurl'] . '?trade_no=' . $params['trade_no'];
	        //$defaultParams['goodsid'] = 8188;
                $defaultParams['pcorderid'] = $params['trade_no'];
		$defaultParams['money'] = $params['money'];
                $defaultParams['pcprivateinfo'] = '充值';
		$reqParams = $this->setReqParams($defaultParams);
		// 创建签名
		$postStr = $this->createSign($reqParams);
		// 远程获取数据
		$respData = $this->newPost($this->payGateway, $postStr);
		if(empty($respData))
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求浦发白名单商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . json_encode(simplexml_load_string($respData));
			$this->alertEmail($message);
			
			return $returnData;
		}
		$resParams = urldecode($respData);
                $resParams = explode('=', $resParams);
                $joson = str_replace('&sign', '', $resParams[1]);
                $resParams = json_decode($joson, true);
                if($resParams['code'] == 200)
                {
                        $resParams['orderId'] = $params['trade_no'];
                        $resParams['orderTime'] = $defaultParams['time_start'];
                        $resParams['txnAmt'] = $defaultParams['money'];
                        $resParams['transid'] = $resParams['transid'];
                        $resParams['appid'] = $defaultParams['appid'];
                        $resParams['key'] = $this->key;
                        $resParams['backurl'] = $backurl;
                        $returnData = array(
                                'code' => true,
                                'msg' => '请求成功',
                                'data' => $resParams,
                        );
                }
                else
                {
                        // err_code err_msg
                        // TODO 交易异常 这里记录错误日志或邮件报警
                    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>uid:'.$params['uid'].',请求浦发白名单商户号：'. $this->merId . '付款信息时失败，请及时留意。<br/>请求地址：' . $this->payGateway . '<br/>请求返回数据：' . $this->printJson($resParams);
                    $this->alertEmail($message);
                    $this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
                }
		return $returnData;
	}
	
	/**
	 * 充值异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => 'failure',
			'succMsg' => 'SUCCESS',
			'data' => array(),
		);
                
		$res = file_get_contents('php://input');
                $resParams = urldecode($res);
                $resParams = explode('=', $resParams);
                $joson = str_replace('&sign', '', $resParams[1]);
                $sign = explode('&', $resParams[2]);
                $resParams = json_decode($joson, true);
		$checkData = $this->createSign($resParams);
		//if($checkData['sign'] == $sign[0])
		{
			if($resParams['result'] == 1)
			{
				$payData = array(
					'trade_no' => $resParams['cporderid'],
					'pay_trade_no' => $resParams['transid'],
					'status' => '1',	// 成功
				);

				$returnData = array(
					'code' => true,
					'errMsg' => 'failure',
					'succMsg' => 'success',
					'data' => $payData,
				);
			}
			else
			{
                            // err_code err_msg
                            // TODO 交易异常 这里记录错误日志或邮件报警
                            $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>浦发白名单商户号：'. $this->merId . '异步通知接口交易异常，请及时留意。';
                            // $this->alertEmail($message);
                            $this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
			}
		}
//		else
//		{
//                    // TODO 验签失败 这里记录错误日志或邮件报警
//                    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>浦发白名单商户号：'. $this->merId . '异步通知接口验签失败，请及时留意。<br/>请求返回数据：' . $this->printJson($resParams);
//                    $this->alertEmail($message);
//                    $this->log($resParams, __CLASS__, $this->merId, __FUNCTION__);
//		}
		return $returnData;
	}

	/**
	 * 充值同步通知
	 * @see RechargeAbstract::syncCallback()
	 */
	public function syncCallback()
	{
        $returnData = array(
            'code' => false,
            'errMsg' => 'failure',
            'succMsg' => 'success',
            'data' => array(),
        );
        return $returnData;
	}
	/**
	 * [formSubmit 表单提交]
	 * @author LiKangJian 2017-06-16
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function formSubmit($params)
	{
		$params['money'] = $params['money'] ;//* 100;
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
		$formParams['orderId'] = $params['orderId'];
		$formParams['orderTime'] = $params['orderTime'];
		$formParams['txnAmt'] = $params['txnAmt'];
		$formParams['codeUrl'] = $params['code_url'];
		foreach ($formParams as $key => $value)
		{

			$html .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/><br/>';
			
		}
		$html .= '</form></body>';

		return $html;
	}
	public function queryOrder($params)
	{
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '操作失败'),
		);
		
		$rparams = array(
			'appid' => $this->config['appid'],
                        'pcorderid' => $params['trade_no'],
		);
		$postStr = $this->createSign($rparams);
		// 远程获取数据
		$respData = $this->newPost("https://payh5.bbnpay.com/cpapi/query_order.php", $postStr);
		$resParams = urldecode($respData);
                $resParams = explode('=', $resParams);
                $joson = str_replace('&sign', '', $resParams[1]);
                $resParams = json_decode($joson, true);
                if($resParams['code'] == 200)
                {
                        $pstatus = array('1' => '待支付', '2' => '支付成功', '3' => '支付失败', '4' => '取消订单', '5' => '支付超时', '6' => '退款', '9' => '其他错误');
                        $payData = array(
                        'code' => '0',
                        'ptype' => '浦发白名单',
                        'pstatus' => $pstatus[$resParams['result']],
                        'ptime' => $resParams['result'] == '2' ? date('Y-m-d H:i:s', strtotime($resParams['transtime'])) : '',
                        'pmoney' => number_format($resParams['money'] / 100, 2, ".", ","),
                        'pbank' => '',
                        'ispay' => $resParams['result'] == '2' ? true : false,
                        'pay_trade_no' => $resParams['transid'],
                    );
                    $returnData = array(
                        'code' => true,
                        'msg' => '操作成功',
                        'data' => $payData,
                    );

                    return $returnData;
                } else {
                    $returnData['data']['msg'] = $resParams['err_msg'];
                }
                return $returnData;
	}
	
	public function refundSubmit($params)
	{
		
	}
	
	public function queryRefund($params)
	{
		
	}
	
	/**
	 * 对账接口
	 * @param unknown_type $params
	 */
	public function queryBill($params)
	{
            
	}
	
	/**
	 * 返回请求参数数组
	 * @return multitype:string unknown_type
	 */
	private function getParams()
	{
		$params = array();
		foreach ($this->config as $key => $value)
		{
			$params[$key] = $value;
		}
		
		return $params;
	}

	// 过滤参数
	public function setReqParams($params, $filterField = null)
	{
		if($filterField !== null)
		{
            forEach($filterField as $k => $v)
            {
                unset($params[$v]);
            }
        }
        
        //判断是否存在空值，空值不提交
        forEach($params as $k => $v)
        {
            if(empty($v))
            {
                unset($params[$k]);
            }
        }
        return $params;
	}

	// 添加参数
	public function setParameter($params = array(), $parameter, $parameterValue) 
	{
		$params[$parameter] = $parameterValue;
		return $params;
	}

	// 检查参数
	public function getParameter($params = array(), $parameter) 
	{
		return isset($params[$parameter]) ? $params[$parameter] : '';
	}

	// 参数加签
	public function createSign($params) 
	{
            $signPars = "";
            ksort($params);
            foreach($params as $k => $v) 
            {
                    if("" != $v && "sign" != $k) 
                    {
                            $signPars .= $k . "=" . $v . "&";
                    }
            }
            $signPars .= "key=" . $this->key;
            $sign = md5($signPars);
            $data = array();
            $data['transdata'] = urlencode(json_encode($params));
            $data['sign'] = urlencode($sign);
            $data['signtype'] = 'MD5';
            return $data;
        }	


	public function newPost($url, $para, $cacert_url = '')
        {
            $ch      = curl_init();
            $timeout = 30;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
            $html     = curl_exec($ch);
            $curlinfo = curl_getinfo($ch);
            curl_close($ch);
            return $html;
        }
}