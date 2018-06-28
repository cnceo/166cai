<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class LibComm
{
	public function refreshCache($ctype)
	{
		$CI = & get_instance();
		$CI->load->library('tools');
		$params = array();
		if (ENVIRONMENT === 'production') 
		{
			$url = "//".$this->config->item('base_url');
		}
		elseif (ENVIRONMENT === 'checkout') 
		{
			$url = "//123.59.105.39";
			$params = array('HOST' => $this->config->item('base_url'));
		}
		else 
		{
			$url = "//127.0.0.3";
			$params = array('HOST' => $this->config->item('base_url'));
		}
		$CI->tools->request("{$url}/api/data/refreshCache/$ctype", $params);
	}
}