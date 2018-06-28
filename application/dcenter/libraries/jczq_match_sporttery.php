<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：竞彩足球赛事抓取操作
 * 作    者：shigx
 * 修改日期：2015.03.12
 */
class Jczq_Match_Sporttery
{
	private $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json';
	private $typeMap = array(
		'spf' 	=> 'had', //ctype = 1
		'rqspf' => 'hhad', //ctype = 2
		'cbf' 	=> 'crs', //ctype = 5
		'jqs' 	=> 'ttg', //ctype = 4
		'bqc' 	=> 'hafu', //ctype = 3
	);
	private $source = 1;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
		$this->CI->load->library('tools');
	}

	/**
	 * 参    数：type,字符型,抓取类型
	 * 作    者：shigx
	 * 功    能：根据类型抓取赛事信息
	 * 修改日期：2015-03-12
	 */
	public function capture($param)
	{
		$this->source = $param['source'];
		$type = $param['type'];
		$url = $this->url."&poolcode[]={$this->typeMap[$type]}&_=".time();
		$response = $this->CI->tools->getCurlUrl($url);
		$result = json_decode($response, true);
		if($result['data'])
		{
			$inserData = array();
			foreach ($result['data'] as $value)
			{
				$endTime = $value['date'] . ' ' . $value['time'];
				if(strtotime($endTime) <= (time() + 180))
				{
					//时间在3分钟之内时停止更新数据
					continue;
				}
				$data = array();
				$b_date = str_replace('-', '', $value['b_date']);
				$num = substr($value['num'], -3);
				$data['mid'] = $b_date.$num;
				$data['m_date'] = $value['b_date'];
				$data['mname'] = $value['num'];
				$data['league'] = $value['l_cn'];
				$data['home'] = $value['h_cn'];
				$data['away'] = $value['a_cn'];
				$data['league_abbr'] = $value['l_cn_abbr'];
				$data['home_abbr'] = $value['h_cn_abbr'];
				$data['away_abbr'] = $value['a_cn_abbr'];
				$data['end_sale_date'] = $value['date'];
				$data['end_sale_time'] = $value['time'];
				$data['l_background_color'] = $value['l_background_color'];
				$data['status'] = $value['status'] == 'Selling' ? 1 : 0;
				$data['source'] = $this->source;
				$data['codes'] = $value[$this->typeMap[$type]];
				$data = $this->$type($data);
				$inserData[] = $data;
			}
			
			$this->CI->Match_model->saveJczq($inserData);
		}
		else
		{
			log_message('Error', '接口抓取数据失败');
		}
	}
	
	/**
	 * curl请求页面URL
	 * @param unknown $url
	 * @return void|array
	 */
	public function getCurlUrl($url)
	{
	    $curl = curl_init(); // 启动一个CURL会话
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36");
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
	    curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	    curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	    
	    $result = curl_exec($curl); // 执行操作
	    $info = curl_getinfo($curl);
	    if ($info['http_code'] == 302)
	    {
	        //解析HTTP数据流
	        list($header, $body) = explode("\r\n\r\n", $result, 2);
	        $location= $info['redirect_url'];
	        preg_match("/Set-Cookie:(.*?)$/",$header,$match_cookie);
	        //cookie获取成功
	        $cookie = '';
	        if (count($match_cookie) ==2 )
	        {
	            $cookie =$match_cookie[1];
	        }    
	        
	        $curl = curl_init(); // 启动一个CURL会话
	        curl_setopt($curl, CURLOPT_URL, $location); // 要访问的地址
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
	        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36");
	        
	        if ($cookie!="")
	        {
	            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
	        }
	        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
	        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
	        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
	        curl_setopt($curl, CURLOPT_HEADER, 1); // 显示返回的Header区域内容
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
	        $result = curl_exec($curl); // 执行操作
	    }
	    
	    if (curl_errno($curl)) 
	    {
	        log_message('Error', 'Errno'. curl_error($curl));
	        return;
	    }
	    
	    curl_close($curl); // 关闭CURL会话
	    
	    //解析HTTP数据流
	    list($header, $body) = explode("\r\n\r\n", $result, 2);
	    return $body;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：处理胜平负数据并入库操作
	 * 修改日期：2015-03-12
	 */
	private function spf($data = array())
	{
		//处理数据逻辑
		$data['ctype'] = 1;
		$codes['a'] = $data['codes']['a'];
		$codes['d'] = $data['codes']['d'];
		$codes['h'] = $data['codes']['h'];
		$codes['single'] = $data['codes']['single'];
		$codes['fixedodds'] = $data['codes']['fixedodds'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：处理让球胜平负数据并入库操作
	 * 修改日期：2015-03-12
	 */
	private function rqspf($data = array())
	{
		//处理数据逻辑
		$data['ctype'] = 2;
		$codes['a'] = $data['codes']['a'];
		$codes['d'] = $data['codes']['d'];
		$codes['h'] = $data['codes']['h'];
		$codes['single'] = $data['codes']['single'];
		$codes['fixedodds'] = $data['codes']['fixedodds'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：处理猜比分数据并入库操作
	 * 修改日期：2015-03-12
	 */
	private function cbf($data = array())
	{
		//处理数据逻辑
		$data['ctype'] = 5;
		$codes['1:0'] = $data['codes']['0100'];
		$codes['2:0'] = $data['codes']['0200'];
		$codes['2:1'] = $data['codes']['0201'];
		$codes['3:0'] = $data['codes']['0300'];
		$codes['3:1'] = $data['codes']['0301'];
		$codes['3:2'] = $data['codes']['0302'];
		$codes['4:0'] = $data['codes']['0400'];
		$codes['4:1'] = $data['codes']['0401'];
		$codes['4:2'] = $data['codes']['0402'];
		$codes['5:0'] = $data['codes']['0500'];
		$codes['5:1'] = $data['codes']['0501'];
		$codes['5:2'] = $data['codes']['0502'];
		$codes['h_o'] = $data['codes']['-1-h'];
		$codes['0:0'] = $data['codes']['0000'];
		$codes['1:1'] = $data['codes']['0101'];
		$codes['2:2'] = $data['codes']['0202'];
		$codes['3:3'] = $data['codes']['0303'];
		$codes['d_o'] = $data['codes']['-1-d'];
		$codes['0:1'] = $data['codes']['0001'];
		$codes['0:2'] = $data['codes']['0002'];
		$codes['1:2'] = $data['codes']['0102'];
		$codes['0:3'] = $data['codes']['0003'];
		$codes['1:3'] = $data['codes']['0103'];
		$codes['2:3'] = $data['codes']['0203'];
		$codes['0:4'] = $data['codes']['0004'];
		$codes['1:4'] = $data['codes']['0104'];
		$codes['2:4'] = $data['codes']['0204'];
		$codes['0:5'] = $data['codes']['0005'];
		$codes['1:5'] = $data['codes']['0105'];
		$codes['2:5'] = $data['codes']['0205'];
		$codes['a_o'] = $data['codes']['-1-a'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：处理进球数数据并入库操作
	 * 修改日期：2015-03-12
	 */
	private function jqs($data = array())
	{
		//处理数据逻辑
		$data['ctype'] = 4;
		$codes['s0'] = $data['codes']['s0'];
		$codes['s1'] = $data['codes']['s1'];
		$codes['s2'] = $data['codes']['s2'];
		$codes['s3'] = $data['codes']['s3'];
		$codes['s4'] = $data['codes']['s4'];
		$codes['s5'] = $data['codes']['s5'];
		$codes['s6'] = $data['codes']['s6'];
		$codes['s7'] = $data['codes']['s7'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：处理半全场数据并入库操作
	 * 修改日期：2015-03-12
	 */
	private function bqc($data = array())
	{
		//处理数据逻辑
		$data['ctype'] = 3;
		$codes['hh'] = $data['codes']['hh'];
		$codes['hd'] = $data['codes']['hd'];
		$codes['ha'] = $data['codes']['ha'];
		$codes['dh'] = $data['codes']['dh'];
		$codes['dd'] = $data['codes']['dd'];
		$codes['da'] = $data['codes']['da'];
		$codes['ah'] = $data['codes']['ah'];
		$codes['ad'] = $data['codes']['ad'];
		$codes['aa'] = $data['codes']['aa'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
	}
}
