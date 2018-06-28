<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：体彩足球赛事抓取操作
 * 作    者：shigx
 * 修改日期：2016.09.23
 */
class Tczq_Match_Sporttery
{
	public static $TCZQ_TYPE_MAP = array(
		'sfc' => array(
			'url' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_nums?key=wilo',
			'mUrl' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_match?key=wilo',
			'ctype' => 1,
		),
		'bqc' => array(
			'url' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_nums?key=hafu',
			'mUrl' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_match?key=hafu',
			'ctype' => 2,
		),
		'jqc' => array(
			'url' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_nums?key=goal',
			'mUrl' => 'http://i.sporttery.cn/wap/fb_lottery/fb_lottery_match?key=goal',
			'ctype' => 3,
		),
	);

	private $type = '';
	private $source = 2;
	private $mid = '';
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
	}
	
	public function capture($param)
	{
		$this->source = $param['source'];
		$this->type = $param['type'];
		$this->mid = '';
		for ($i = 0; $i < 3; $i++)
		{
			$url = self::$TCZQ_TYPE_MAP[$this->type]['url'] . ($this->mid ? '&num=' . $this->mid : '');
			$result = $this->CI->tools->getCurlUrl($url);
			$result = json_decode($result, true);
			if($result['result'] && $result['result']['num'])
			{
			    $mUrl = self::$TCZQ_TYPE_MAP[$this->type]['mUrl'] . '&num=' . $result['result']['num'];
				$this->getDataInsert($mUrl, $result['result']);
				$this->mid = $result['result']['next'];
			}
		}
	}
	
	/**
	 * 页面抓取入库操作
	 * @param unknown_type $url
	 */
	private function getDataInsert($url, $result)
	{
		$data = array();
		$content = $this->CI->tools->getCurlUrl($url);
		$content = json_decode($content, true);
		if(empty($content['result']))
		{
			log_message('Error', '体彩信息抓取失败');
			return ;
		}
		ksort($content['result']); //对场次进行排序
		$method = $this->type . 'Parse';
		$source = $this->$method($content['result']);
		$data['mid'] = $result['num'];
		$data['ctype'] = self::$TCZQ_TYPE_MAP[$this->type]['ctype'];
		$data['start_sale_time'] = date('Y-m-d H:i:s', strtotime($result['start']));
		$data['end_sale_time'] = date('Y-m-d H:i:s', strtotime($result['end']));
		$this->insertData($data, $source);
	}
	
	/**
	 * 胜负彩内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function sfcParse($result)
	{
		$datas = array();
		$key = 1;
		foreach ($result as $value)
		{
			$data = array();
			$data['mname'] = $key;
			$data['league'] = $value['league'];
			$data['home'] = $value['h_cn'];
			$data['away'] = $value['a_cn'];
			$data['date'] = $value['date'];
			$datas[] = $data;
			$key++;
		}
		return $datas;
	}
	
	/**
	 * 半全场内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function bqcParse($result)
	{
		$datas = array();
		$key = 0;
		$mname = 1;
		foreach ($result as $value)
		{
			if(($key % 2) == 0)
			{
				$data = array();
				$data['mname'] = $mname;
				$data['league'] = $value['league'];
				$data['home'] = $value['h_cn'];
				$data['away'] = $value['a_cn'];
				$data['date'] = $value['date'];
				$datas[] = $data;
				$mname++;
			}
			
			$key++;
			
		}
	
		return $datas;
	}
	
	/**
	 * 进球彩内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function jqcParse($result)
	{
		$datas = array();
		$key = 1;
		foreach ($result as $value)
		{
			$data = array();
			$data['mname'] = $key;
			$data['league'] = $value['league'];
			$data['home'] = $value['h_cn'];
			$data['away'] = $value['a_cn'];
			$data['date'] = $value['date'];
			$datas[] = $data;
			$key++;
		}
		return $datas;
	}
	
	/**
	 * 参    数：$matches,数组, 
	 * 作    者：shigx
	 * 功    能：将HTML表格的每行每列转为数组，采集表格数据
	 * 修改日期：2015-03-13
	 */
	private function insertData($data, $source)
	{
		if(empty($source))
		{
			log_message('Error', "抓取期次{$data['mid']}信息失败");
			return ;
		}
		$datas = array();
		foreach ($source as $key => $value)
		{
			$datas[$key]['mid'] = $data['mid'];
			$datas[$key]['ctype'] = $data['ctype'];
			$datas[$key]['start_sale_time'] = $data['start_sale_time'];
			$datas[$key]['end_sale_time'] = $data['end_sale_time'];
			$datas[$key]['status'] = 1;
			$datas[$key]['mname'] = $value['mname'];
			$datas[$key]['league'] = $value['league'];
			$datas[$key]['begin_date'] = $value['date'] . ' 00:00';
			$datas[$key]['home'] = $value['home'];
			$datas[$key]['away'] = $value['away'];
			$datas[$key]['source'] = $this->source;
		}
		$res = $this->CI->Match_model->saveTczq($datas);
		if(!$res)
			log_message('Error', '数据写入数据库失败|data:'.print_r($datas, true));
	}
}
