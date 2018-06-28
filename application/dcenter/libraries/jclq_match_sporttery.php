<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：竞彩篮球赛事抓取操作
 * 作    者：shigx
 * 修改日期：2015.03.13
 */
 
class jclq_match_sporttery
{
	private $url = 'http://i.sporttery.cn/odds_calculator/get_odds?i_format=json';
	private $typeMap = array(
		'sf' 	=> 'mnl',
		'rfsf' 	=> 'hdc',
		'sfc' 	=> 'wnm',
		'dxf' 	=> 'hilo',
	);
	private $ctypeMap = array(
		'sf'	=> 1,
		'rfsf'	=> 2,
		'sfc'	=> 3,
		'dxf'	=> 4,
	);
	private $source = 1;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
		$this->CI->load->library('tools');
	}

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
				$data['begin_time'] = $value['date'] . ' ' . $value['time'];
				$data['l_background_color'] = $value['l_background_color'];
				$data['status'] = $value['status'] == 'Selling' ? 1 : 0;
				$data['source'] = $this->source;
				$data['ctype'] = $this->ctypeMap[$type];
				$data['codes'] = $value[$this->typeMap[$type]];
				$data = $this->$type($data);
				$inserData[] = $data;
			}
			$this->CI->Match_model->saveJclq($inserData);
		}
		else
		{
			log_message('Error', '接口抓取数据失败');
		}
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：抓取胜负数据并进行入库操作
	 * 修改日期：2015-03-13
	 */
	private function sf($data = array())
	{
		//处理数据逻辑
		$codes['a'] = $data['codes']['a'];
		$codes['h'] = $data['codes']['h'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：抓取让分胜负数据并进行入库操作
	 * 修改日期：2015-03-13
	 */
	private function rfsf($data = array())
	{
		//处理数据逻辑
		$codes['a'] = $data['codes']['a'];
		$codes['h'] = $data['codes']['h'];
		$codes['single'] = $data['codes']['single'];
		$codes['fixedodds'] = $data['codes']['fixedodds'];
		$data['codes'] = serialize($codes);
		return $data;
	}
	

	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：抓取胜分差数据并进行入库操作
	 * 修改日期：2015-03-13
	 */
	private function sfc($data = array())
	{
		$codes['h_1-5'] = $data['codes']['w1'];
		$codes['h_6-10'] = $data['codes']['w2'];
		$codes['h_11-15'] = $data['codes']['w3'];
		$codes['h_16-20'] = $data['codes']['w4'];
		$codes['h_21-25'] = $data['codes']['w5'];
		$codes['h_26+'] = $data['codes']['w6'];
		$codes['a_1-5'] = $data['codes']['l1'];
		$codes['a_6-10'] = $data['codes']['l2'];
		$codes['a_11-15'] = $data['codes']['l3'];
		$codes['a_16-20'] = $data['codes']['l4'];
		$codes['a_21-25'] = $data['codes']['l5'];
		$codes['a_26+'] = $data['codes']['l6'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
	}

	
	/**
	 * 参    数：$data,数组,赛事信息数组
	 * 作    者：shigx
	 * 功    能：抓取大小分数据并进行入库操作
	 * 修改日期：2015-03-13
	 */
	private function dxf($data = array())
	{
		$codes['score'] = $data['codes']['fixedodds'];
		$codes['b_s'] = $data['codes']['h'];
		$codes['m_s'] = $data['codes']['l'];
		$codes['single'] = $data['codes']['single'];
		$data['codes'] = serialize($codes);
		return $data;
			
	}
}
