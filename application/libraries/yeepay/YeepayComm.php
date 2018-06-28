<?php
/*
 * @Description 易宝支付产品通用接口范例 
 * @V3.0
 * @Author xin.li
 */
define('YEEPAY_PATH', dirname(__FILE__));
#时间设置
date_default_timezone_set('prc');
class YeepayComm
{
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->config->load('pay');
		$yeepayWeb = $this->CI->config->item('yeepayWeb');
		$this->logName	   = $this->CI->config->item('app_path') . 'logs/' . $yeepayWeb['logName'];
		$this->p0_Cmd	   = $yeepayWeb['p0_Cmd'];
		$this->p9_SAF	   = $yeepayWeb['p9_SAF'];
	}
	#签名函数生成签名串
	public function getReqHmacString($params)
	{
		#进行签名处理，一定按照文档中标明的签名顺序进行
		$sbOld = "";
		empty($params['p9_SAF']) || $params['p9_SAF'] = $this->p9_SAF;
		#加入业务类型
		$sbOld = $sbOld . $this->p0_Cmd;
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
		$this->logstr($params['p2_Order'], $sbOld, $this->HmacMd5($sbOld, $params['merchantKey']));
		return $this->HmacMd5($sbOld, $params['merchantKey']);
	} 
	
	/**
	 * 订单查询签名生成
	 * @param unknown_type $params
	 */
	public function getCommandHmacString($params)
	{
	#进行签名处理，一定按照文档中标明的签名顺序进行
	$sbOld = "";
	#加入业务类型
	$sbOld = $sbOld . $params['p0_Cmd'];
	#加入商户编号
	$sbOld = $sbOld . $params['p1_MerId'];
	#加入商户订单号
	$sbOld = $sbOld . $params['p2_Order'];
	#加入版本号
	$sbOld = $sbOld . $params['pv_Ver'];
	#加入查询类型
	$sbOld = $sbOld . $params['p3_ServiceType'];
	$this->logstr($params['p2_Order'], $sbOld, $this->HmacMd5($sbOld, $params['merchantKey']));
	return $this->HmacMd5($sbOld, $params['merchantKey']);
	}
	
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
	
		$this->logstr($params['r6_Order'], $sbOld, $this->HmacMd5($sbOld, $params['merchantKey'], true));
		return $this->HmacMd5($sbOld, $params['merchantKey'], true);
	
	}
	
	#	取得返回串中的所有参数
	public function getCallBackValue(&$params)
	{  
		$params['r0_Cmd']	= $this->CI->input->get_post('r0_Cmd', true);
		$params['r1_Code']	= $this->CI->input->get_post('r1_Code', true);
		$params['r2_TrxId']	= $this->CI->input->get_post('r2_TrxId', true);
		$params['r3_Amt']	= $this->CI->input->get_post('r3_Amt', true);
		$params['r4_Cur']	= $this->CI->input->get_post('r4_Cur', true);
		$params['r5_Pid']	= $this->CI->input->get_post('r5_Pid', true);
		$params['r6_Order']	= $this->CI->input->get_post('r6_Order', true);
		$params['r7_Uid']	= $this->CI->input->get_post('r7_Uid', true);
		$params['r8_MP']	= $this->CI->input->get_post('r8_MP', true);
		$params['r9_BType']	= $this->CI->input->get_post('r9_BType', true); 
		$params['hmac']		= $this->CI->input->get_post('hmac', true);
		return null;
	}
	
	public function CheckHmac($params)
	{
		$hmac = $params['hmac'];
		unset($params['hmac']);
		if($hmac == $this->getCallbackHmacString($params))
			return true;
		else
			return false;
	}
	
	private function logstr($orderid, $str, $hmac)
	{
		$james=fopen($this->logName,"a+");
		fwrite($james,"\r\n" . date("Y-m-d H:i:s") . "|orderid[" . $orderid . "]|str[" . $str . "]|hmac[" . $hmac . "]");
		fclose($james);
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
}