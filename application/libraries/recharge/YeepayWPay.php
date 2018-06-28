<?php
/**
 * 易宝网页端充值服务类
 * @author Administrator
 *
 */
date_default_timezone_set('prc');
require_once dirname(__FILE__) . '/RechargeAbstract.php';
class YeepayWPay extends RechargeAbstract
{
	/**
	 * 商户id
	 * @var unknown_type
	 */
	private $merId;
	
	/**
	 * MD5秘钥
	 * @var unknown_type
	 */
	private $md5Key;
	/**
	 * MD5秘钥
	 * @var unknown_type
	 */
	private $p9_SAF;
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
	
	private $CI;
	
	public function __construct($config = array())
	{
		$this->CI = &get_instance();
		$this->merId = $config['p1_MerId'];
		$this->md5Key = $config['merchantKey'];
		$this->payGateway = $config['reqURL_onLine'];
		$this->p9_SAF	  = $config['p9_SAF'];
		unset($config['merchantKey']);
		$this->config = $config;
	}
	
	/**
	 * curl请求支付信息
	 * @see RechargeAbstract::requestHttp()
	 */
	public function requestHttp($params)
	{
	}
	
	/**
	 * 充值同步/异步通知
	 * @see RechargeAbstract::notify()
	 */
	public function notify()
	{
		$returnData = array(
			'code' => false,
			'errMsg' => 'failure',
			'succMsg' => 'SUCCESS',
			'isSync' => false,
			'data' => array(),
		);
		$params = array();
		$params['r0_Cmd']   = $this->CI->input->get_post('r0_Cmd', true);
		$params['r1_Code']  = $this->CI->input->get_post('r1_Code', true);
		$params['r2_TrxId'] = $this->CI->input->get_post('r2_TrxId', true);
		$params['r3_Amt']   = $this->CI->input->get_post('r3_Amt', true);
		$params['r4_Cur']   = $this->CI->input->get_post('r4_Cur', true);
		$params['r5_Pid']   = $this->CI->input->get_post('r5_Pid', true);
		$params['r6_Order'] = $this->CI->input->get_post('r6_Order', true);
		$params['r7_Uid']   = $this->CI->input->get_post('r7_Uid', true);
		$params['r8_MP']    = $this->CI->input->get_post('r8_MP', true);
		$params['r9_BType'] = $this->CI->input->get_post('r9_BType', true);
		$params['hmac']     = $this->CI->input->get_post('hmac', true);
		$params['p1_MerId'] = $this->merId;
		$params['merchantKey'] = $this->md5Key;
		if(empty($params['hmac']) || empty($params['r6_Order']))
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>易宝网页端商户号：'. $this->merId . '通知接口接收信息失败，请及时留意。<br/>通知返回数据：'. $this->printJson($params);
			$this->alertEmail($message);
			$this->log($params, __CLASS__, $this->merId, __FUNCTION__);
			
			return $returnData;
		}
		$checkResult = $this->CheckHmac($params);
		if($checkResult && $params['r1_Code'] == '1')
		{
			$data = array(
				'trade_no' => $params['r6_Order'],
				'pay_trade_no' => $params['r2_TrxId'],
				'status' => '1',	// 成功
			);
			switch ($params['r9_BType']) 
			{
				case '1':
					$returnData['code'] = true;
					$returnData['isSync'] = true;
					$returnData['data'] = $data;
					break;
				
				case '2':
					$returnData['code'] = true;
					$returnData['data'] = $data;
					break;
				default:
					break;
			}
		}
		else
		{
		    $message = '服务器IP：' . $_SERVER['SERVER_ADDR'] . '<br/>易宝网页端商户号：'. $this->merId . '通知接口验签失败，请及时留意。<br/>通知返回数据：' . $this->printJson($params);
			$this->alertEmail($message);
			$this->log($params, __CLASS__, $this->merId, __FUNCTION__);
		}
		
		return $returnData;
	}

	/**
	 * 充值同步通知
	 * @see RechargeAbstract::syncCallback()
	 */
	public function syncCallback()
	{
		return $this->notify();
	}
	
	public function formSubmit($params)
	{
         if(in_array($params['mode'],array('yeepayWangy','yeepayCredit')))
        {
            $params['real_name'] = '';
            $params['id_card'] = '';
        }
		$defaultParams = $this->getParams();
		$defaultParams['p2_Order'] = $params['trade_no'];
		$defaultParams['p3_Amt'] = number_format($params['money'] /100, 2, ".", "");
		if($params['pd_FrpId'] && empty($defaultParams['pd_FrpId']))
		{
			$defaultParams['pd_FrpId'] = $params['pd_FrpId'];
		}
		$defaultParams['pt_UserName'] = $params['real_name'];
		$defaultParams['pt_PostalCode'] = $params['id_card'];
		$defaultParams['hmac'] = $this->getReqHmacString($defaultParams);
		$html = '<body onLoad="document.yeepay.submit(); ">';
		$html .= '<form name="yeepay" action="'. $defaultParams['reqURL_onLine'] .'" method="post" accept-charset="gbk" onsubmit="document.charset=\'gbk\';" >';
		$html .= '<input type="hidden" name="p0_Cmd" value="'. $defaultParams['p0_Cmd'].'" />';
		$html .= '<input type="hidden" name="p1_MerId" value="'. $defaultParams['p1_MerId'].'" />';
		$html .= '<input type="hidden" name="p2_Order" value="'. $defaultParams['p2_Order'].'" />';
		$html .= '<input type="hidden" name="p3_Amt" value="'. $defaultParams['p3_Amt'].'" />';
		$html .= '<input type="hidden" name="p4_Cur" value="'. $defaultParams['p4_Cur'].'" />';
		$html .= '<input type="hidden" name="p5_Pid" value="'. $defaultParams['p5_Pid'].'" />';
		$html .= '<input type="hidden" name="p6_Pcat" value="'. $defaultParams['p6_Pcat'].'" />';
		$html .= '<input type="hidden" name="p7_Pdesc" value="'. $defaultParams['p7_Pdesc'].'" />';
		$html .= '<input type="hidden" name="p8_Url" value="'. $defaultParams['p8_Url'].'" />';
		$html .= '<input type="hidden" name="p9_SAF" value="'. $defaultParams['p9_SAF'].'" />';
		$html .= '<input type="hidden" name="pa_MP" value="'. $defaultParams['pa_MP'].'" />';
		$html .= '<input type="hidden" name="pd_FrpId" value="'. $defaultParams['pd_FrpId'].'" />';
		$html .= '<input type="hidden" name="pm_Period"	value="'. $defaultParams['pm_Period'].'" />';
		$html .= '<input type="hidden" name="pn_Unit" value="'. $defaultParams['pn_Unit'].'" />';
		$html .= '<input type="hidden" name="pr_NeedResponse" value="'. $defaultParams['pr_NeedResponse'].'" />';
		if(!empty($defaultParams['pt_UserName']))
		{
			$html .= '<input type="hidden" name="pt_UserName" value="'. $defaultParams['pt_UserName'].'" />';
		}
		if(!empty($defaultParams['pt_PostalCode']))
		{
			$html .= '<input type="hidden" name="pt_PostalCode" value="'. $defaultParams['pt_PostalCode'].'" />';
		}
		$html .= '<input type="hidden" name="hmac" value="'. $defaultParams['hmac'].'" />';
		$html .= '</form></body>';
		$returnData = array(
			'code' => true,
			'msg' => '请求成功',
			'data' => array('charset' => 'gbk','html' => $html),
		);
		return $returnData;
	}

	public function queryOrder($params)
	{
		$postParams = array(
			'p0_Cmd' => 'QueryOrdDetail',
			'p1_MerId' => $this->merId,
			'p2_Order' => $params['trade_no'],
			'pv_Ver' => '3.0',
			'p3_ServiceType' => '2',
		);
		#进行签名处理，一定按照文档中标明的签名顺序进行
		$sbOld = "";
		#加入业务类型
		$sbOld = $sbOld . $postParams['p0_Cmd'];
		#加入商户编号
		$sbOld = $sbOld . $postParams['p1_MerId'];
		#加入商户订单号
		$sbOld = $sbOld . $postParams['p2_Order'];
		#加入版本号
		$sbOld = $sbOld . $postParams['pv_Ver'];
		#加入查询类型
		$sbOld = $sbOld . $postParams['p3_ServiceType'];
		$postParams['hmac'] = $this->HmacMd5($sbOld, $this->md5Key);
		$url = 'https://cha.yeepay.com/app-merchant-proxy/command';
		$postStr = http_build_query($postParams);
		$respone = $this->curlPost($url, $postStr);
		$respone = explode("\n", $respone);
		$returnData = array(
			'code' => false,
			'msg'  => '操作失败',
			'data' => array('code' => 1, 'msg' => '操作失败'),
		);
		if(empty($respone))
		{
			return $returnData;
		}
		
		$res = array();
		foreach ($respone as $val)
		{
			$tmp = explode('=', $val);
			if(isset($tmp[0]) && isset($tmp[1]))
			{
				$res[$tmp[0]] = $tmp[1];
			}
		}
		
		if(isset($res['r1_Code']) && $res['r1_Code'] == '1')
		{
			$ptype = array('yeepayWangy' => '易宝网银', 'yeepayCredit' => '易宝信用卡', 'yeepayWeix' => '易宝微信', 'yeepayKuaij' => '易宝快捷');
			$pstatus = array('INIT' => '未支付', 'CANCELED' => '已取消', 'SUCCESS' => '已支付');
			$payData = array(
				'code' => '0',
				'ptype' => $ptype[$params['additions']],
				'pstatus' => $pstatus[$res['rb_PayStatus']],
				'ptime' => $res['rb_PayStatus'] == 'SUCCESS' ? date('Y-m-d H:i:s', strtotime($res['ry_FinshTime'])) : '',
				'pmoney' => number_format($res['r3_Amt'], 2, ".", ","),
				'pbank' => '',
				'ispay' => $res['rb_PayStatus'] == 'SUCCESS' ? true : false,
				'pay_trade_no' => $res['r2_TrxId'],
			);
			
			$returnData = array(
				'code' => true,
				'msg' => '操作成功',
				'data' => $payData,
			);
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
	
	/**
	 * 签名函数生成签名串
	 * @param unknown_type $params
	 */
	private function getReqHmacString($params)
	{
		#进行签名处理，一定按照文档中标明的签名顺序进行
		$sbOld = "";
		empty($params['p9_SAF']) || $params['p9_SAF'] = $this->p9_SAF;
		#加入业务类型
		$sbOld = $sbOld . $params['p0_Cmd'];
		#加入商户编号
		$sbOld = $sbOld . $params['p1_MerId'];
		#加入商户订单号
		$sbOld = $sbOld . $params['p2_Order'];
		#加入支付金额
		$sbOld = $sbOld . $params['p3_Amt'];
		#加入交易币种
		$sbOld = $sbOld . $params['p4_Cur'];
		#加入商品名称
		$sbOld = $sbOld . $params['p5_Pid'];
		#加入商品分类
		$sbOld = $sbOld . $params['p6_Pcat'];
		#加入商品描述
		$sbOld = $sbOld . $params['p7_Pdesc'];
		#加入商户接收支付成功数据的地址
		$sbOld = $sbOld . $params['p8_Url'];
		#加入送货地址标识
		$sbOld = $sbOld . $params['p9_SAF'];
		#加入商户扩展信息
		$sbOld = $sbOld . $params['pa_MP'];
		#加入支付通道编码
		$sbOld = $sbOld . $params['pd_FrpId'];
		#加入订单有效期
		$sbOld = $sbOld . $params['pm_Period'];
		#加入订单有效期单位
		$sbOld = $sbOld . $params['pn_Unit'];
		#加入是否需要应答机制
		$sbOld = $sbOld . $params['pr_NeedResponse'];
		#加入是否需要用户姓名
		if(!empty($params['pt_UserName']))
		{
			$sbOld = $sbOld . $params['pt_UserName'];
		}
		#加入是否需要身份证号
		if(!empty($params['pt_PostalCode']))
		{
			$sbOld = $sbOld . $params['pt_PostalCode'];
		}
		return $this->HmacMd5($sbOld, $this->md5Key);
	}
	
	private function HmacMd5($data, $key, $true = false)
	{
		// RFC 2104 HMAC implementation for php.
		// Creates an md5 HMAC.
		// Eliminates the need to install mhash to compute a HMAC
		// Hacked by Lance Rushing(NOTE: Hacked means written)
		//需要配置环境支持iconv，否则中文参数不能正常处理
		if($true)
		{
			$key = iconv("GB2312", "UTF-8", $key);
			$data = iconv("GB2312", "UTF-8", $data);
		}
	
		$b = 64; // byte length for md5
		if (strlen($key) > $b) {
			$key = pack("H*", md5($key));
		}
		$key  = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
	
		return md5($k_opad . pack("H*", md5($k_ipad . $data)));
	}
	
	/**
	 * 验签操作
	 * @param unknown_type $params
	 * @return boolean
	 */
	public function CheckHmac($params)
	{
		$hmac = $params['hmac'];
		unset($params['hmac']);
		if($hmac == $this->getCallbackHmacString($params))
			return true;
		else
			return false;
	}
	
	/**
	 * 异步通知验签
	 * @param unknown_type $params
	 */
	private function getCallbackHmacString($params)
	{
		#取得加密前的字符串
		$sbOld = "";
		#加入商家ID
		$sbOld = $sbOld . $params['p1_MerId'];
		#加入消息类型
		$sbOld = $sbOld . $params['r0_Cmd'];
		#加入业务返回码
		$sbOld = $sbOld . $params['r1_Code'];
		#加入交易ID
		$sbOld = $sbOld . $params['r2_TrxId'];
		#加入交易金额
		$sbOld = $sbOld . $params['r3_Amt'];
		#加入货币单位
		$sbOld = $sbOld . $params['r4_Cur'];
		#加入产品Id
		$sbOld = $sbOld . $params['r5_Pid'];
		#加入订单ID
		$sbOld = $sbOld . $params['r6_Order'];
		#加入用户ID
		$sbOld = $sbOld . $params['r7_Uid'];
		#加入商家扩展信息
		$sbOld = $sbOld . $params['r8_MP'];
		#加入交易结果返回类型
		$sbOld = $sbOld . $params['r9_BType'];
		
		return $this->HmacMd5($sbOld, $params['merchantKey'], true);
	}
}