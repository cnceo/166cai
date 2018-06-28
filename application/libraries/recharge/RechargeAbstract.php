<?php
/**
 * 充值服务抽象类
 * @author Administrator
 *
 */
abstract class RechargeAbstract
{
	//curl请求http返回码
	public $recode;
	
	/**
	 * 表单提交第三方抽象方法
	 */
	abstract protected function formSubmit($params);
	
	/**
	 * 接口请求第三方抽象方法
	 */
	abstract protected function requestHttp($params);
	
	/**
	 * 异步回调抽象方法
	 */
	abstract protected function notify();

	/**
	 * 同步回调抽象方法
	 */
	abstract protected function syncCallback();
	
	/**
	 * 订单查询抽象方法
	 */
	abstract protected function queryOrder($params);
	
	/**
	 * 退款提交抽象方法
	 */
	abstract protected function refundSubmit($params);
	
	/**
	 * 退款订单查询抽象方法
	 */
	abstract protected function queryRefund($params);
	
	/**
	 * 过滤空值参数
	 * @param unknown_type $params
	 */
	public function paraFilter($params)
	{
		$paraFilter = array();
		foreach ($params as $key => $value)
		{
			if(!empty($value))
			{
				$paraFilter[$key] = $value;
			}
		}

		return $paraFilter;
	}
	
	/**
	 * 对数组排序
	 * @param $para 排序前的数组
	 * return 排序后的数组
	 */
	public function argSort($para) 
	{
		ksort($para);
		reset($para);
		return $para;
	}
	
	/**
	 * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	 * @param $para 需要拼接的数组
	 * return 拼接完成以后的字符串
	 */
	public function createLinkstring($para) {
		$arg  = "";
		foreach ($para as $key => $value)
		{
			$arg .= $key . "=" . $value . "&";
		}
		//去掉最后一个&字符
		$arg = substr($arg,0,count($arg)-2);
		//如果存在转义字符，那么去掉转义
		if(get_magic_quotes_gpc()){
			$arg = stripslashes($arg);
		}
		
		return $arg;
	}
	
	/**
	 * 远程获取数据，POST模式
	 * return 远程输出的数据
	 */
	public function curlPost($url, $para, $cacert_url = '') 
	{
		$curl = curl_init($url);
		if($cacert_url)
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
			curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
		}
		else
		{
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
		curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
		curl_setopt($curl,CURLOPT_POST,true); // post传输数据
		curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
		$responseText = curl_exec($curl);
		$curl_errno = curl_errno($curl);
		$curl_error = curl_error($curl);
		if ($curl_errno || (!empty($curl_error)))
		{
			// 记录错误日志
			log_message('log', "errno:{$curl_errno}\terror:{$curl_error}\trequestData:" . json_encode($para) . "\tcurlInfo:" . json_encode(curl_getinfo($curl)), 'recharge/curl_error');
		}
		$this->recode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
	
		return $responseText;
	}
	
	/**
	 * 邮件内容入报警表
	 */
	protected function alertEmail($message)
	{
		$CI = &get_instance();
		$CI->load->model('pay_model');
		$CI->pay_model->insertAlert($message);
	}
	
	/**
	 * 记录日志  TODO
	 */
	protected function log($params, $library, $merId, $logType)
	{
		$logPath = 'recharge/' . $library . '_' . $merId . '_' . $logType;
		log_message('log', print_r($params, true), $logPath);
	}
	/**
	 * [printJson 数组解析成json不转译]
	 * @author LiKangJian 2018-01-24
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	protected function printJson($arr)
	{
		if(version_compare(PHP_VERSION,'5.4.0','<'))
		{
		  $str = json_encode($arr);
		  $str = preg_replace_callback("#\\\u([0-9a-f]{4})#i",function($matchs){return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));},$str);
		  return $str;
		}else{
		  return json_encode($arr, JSON_UNESCAPED_UNICODE);
		}
	}
        
    protected function getMark($merid)
    {
        $CI = &get_instance();
        $CI->load->model('pay_model');
        $mark = $CI->pay_model->queryMarkByMerid($merid);
        return $mark;
    }
}