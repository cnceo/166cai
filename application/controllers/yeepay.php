<?php

class Yeepay extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->library('yeepay/YeepayComm');
    }

    public function index() 
    {
    	$data = array();
    	$this->load->view('v1.1/wallet/yeepay', $data);
    }
    
    public function req()
    {	
    	#商户订单号,选填.
		##若不为""，提交的订单号必须在自身账户交易中唯一;为""时，易宝支付会自动生成随机的商户订单号.
		$params['p2_Order']	= time();//$this->input->get_post('p2_Order', true);
		#	支付金额,必填.
		##单位:元，精确到分.
		$params['p3_Amt']	= $this->input->get_post('p3_Amt', true);
		#	交易币种,固定值"CNY".
		$params['p4_Cur']	= "CNY";
		#	商品名称
		##用于支付时显示在易宝支付网关左侧的订单产品信息.
		$params['p5_Pid']	= $this->input->get_post('p5_Pid', true);
		#	商品种类
		$params['p6_Pcat']	= $this->input->get_post('p6_Pcat', true);
		#	商品描述
		$params['p7_Pdesc']	= $this->input->get_post('p7_Pdesc', true);
		#	商户接收支付成功数据的地址,支付成功后易宝支付会向该地址发送两次成功通知.
		$params['p8_Url']	= $this->input->get_post('p8_Url', true);	
		#	送货地址
		$params['p9_SAF']	= $this->input->get_post('p9_SAF', true);
		#	商户扩展信息
		##商户可以任意填写1K 的字符串,支付成功时将原样返回.												
		$params['pa_MP']	= $this->input->get_post('pa_MP', true);
		#	支付通道编码
		##默认为""，到易宝支付网关.若不需显示易宝支付的页面，直接跳转到各银行、神州行支付、骏网一卡通等支付页面，该字段可依照附录:银行列表设置参数值.			
		$params['pd_FrpId']	= $this->input->get_post('pd_FrpId', true);
		#订单有效期
		##默认为"7": 7天;
		$params['pm_Period']= "7";
		#	订单有效期单位
		##默认为"day": 天;
		$params['pn_Unit']	= "day";
		#	应答机制
		##默认为"1": 需要应答机制;
		$params['pr_NeedResponse']	= "1";
		#	送货地址
		# 为"1": 需要用户将送货地址留在易宝支付系统;为"0": 不需要，默认为 "0".
		$params['p9_SAF'] = $this->config->item('p9_SAF');
		#	产品通用接口请求地址
		$params['reqURL_onLine'] = $this->config->item('reqURL_onLine');
		# 业务类型
		# 支付请求，固定值"Buy" .
		$params['p0_Cmd'] = $this->config->item('p0_Cmd');
		#	商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得
		$params['p1_MerId'] = $this->config->item('p1_MerId');
		#调用签名函数生成签名串
    	$params['hmac'] = $this->yeepaycomm->getReqHmacString($params);
    	$this->load->view('v1.1/wallet/yeepayreq', array('params' => $params));
    }
    
    public function callBack()
    {
    	$params = array();
    	$this->yeepaycomm->getCallBackValue($params);
    	$bRet = $this->yeepaycomm->CheckHmac($params);
    	if($bRet)
    	{
    		if($params['r1_Code'] == "1")
	    	{
	    		if($params['r9_BType'] == "1")
	    		{
					echo "交易成功";
					echo  "<br />在线支付页面返回";
				}
				elseif($params['r9_BType'] == "2")
				{
					#如果需要应答机制则必须回写流,以success开头,大小写不敏感.
					log_message('LOG', 'success', 'yeebao');
					echo "success";
					echo "<br />交易成功";
					echo  "<br />在线支付服务器返回";      			 
				}
    		}
    	}
    	else 
    	{
    		echo 'test';
    	}
    }
}
