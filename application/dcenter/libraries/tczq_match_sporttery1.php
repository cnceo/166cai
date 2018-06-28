<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：体彩足球赛事抓取操作
 * 作    者：shigx
 * 修改日期：2016.09.23
 */
class Tczq_Match_Sporttery1
{
	public static $TCZQ_TYPE_MAP = array(
		'sfc' => array(
			'url' => 'lottery_iframe_2015.php?key=wilo',
			'ctype' => 1,
		),
		'bqc' => array(
			'url' => 'lottery_iframe_2015.php?key=hafu',
			'ctype' => 2,
		),
		'jqc' => array(
			'url' => 'lottery_iframe_2015.php?key=goal',
			'ctype' => 3,
		),
	);

	private $type = '';
	private $source = 2;
	private $uri = '';
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
	}
	
	public function capture($param)
	{
		$this->source = $param['source'];
		$this->type = $param['type'];
		$this->uri = self::$TCZQ_TYPE_MAP[$this->type]['url'];
		for ($i = 0; $i < 3; $i++)
		{
			if(empty($this->uri))
			{
				continue;
			}
			$url = 'http://info.sporttery.cn/iframe/' . $this->uri;
			$this->getDataInsert($url);
		}
	}
	
	/**
	 * 页面抓取入库操作
	 * @param unknown_type $url
	 */
	private function getDataInsert($url)
	{
		$data = array();
		$content = $this->CI->tools->getCurlUrl($url);
		$pregstr  = '<div.*?class="paging">.*?<a.*?><img.*?<\/a>.*?<span.*?>(.*?)期<\/span>.*?';
		$pregstr .= '<a.*?href="(.*?)".*?>.*?<\/div>.*?';
		$pregstr .= '<div.*?class="calculator">.*?<table.*?>.*?<tr>.*?<\/tr>(.*?)<\/table>.*?';
		$pregstr .= '<div.*?class="time">.*?<span.*?>开售时间:(.*?);  停售时间:(.*?);.*?<\/div>';
		$matches = array();
		preg_match("/$pregstr/is", $content, $matches);
		if(empty($matches[1]) || empty($matches[2]))
		{
			log_message('Error', '体彩信息抓取失败');
			$this->uri = '';
			return ;
		}
		$this->uri = trim($matches[2]); //对URI重新赋值
		$method = $this->type . 'Parse';
		$source = $this->$method($matches[3]);
		$data['mid'] = trim($matches[1]);
		$data['ctype'] = self::$TCZQ_TYPE_MAP[$this->type]['ctype'];
		$data['start_sale_time'] = trim($matches[4]);
		$data['end_sale_time'] = trim($matches[5]);
		$this->insertData($data, $source);
	}
	
	/**
	 * 胜负彩内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function sfcParse($content)
	{
		$content = strip_tags($content, "<tr><td>");
		$content = str_replace(array('<tr>', '</tr>'), array('', '{TR}'), $content);
		$array = explode('{TR}', $content);
		unset($array[count($array) - 1]);
		$data = array();
		$league = '';
		foreach ($array as $value)
		{
			$vArr = explode('</td>', $value);
			$count = count($vArr);
			unset($vArr[$count -1], $vArr[$count -2], $vArr[$count -3], $vArr[$count -4], $vArr[$count -5]);
			$tmp = array();
			foreach ($vArr as $key =>$val)
			{
				if((strpos($val, 'rowspan') !== false) && (strpos($val, 'justify') !== false))
				{
					continue;
				}
				if(strpos($val, 'rowspan') !== false)
				{
					$league = trim(strip_tags($val));
					continue;
				}
				$tmp['league'] = $league;
				$tmp[] = trim(strip_tags($val));
			}
			array_push($data, $tmp);
		}
		
		return $data;
	}
	
	/**
	 * 半全场内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function bqcParse($content)
	{
		$content = strip_tags($content, "<tr><td>");
		$content = str_replace(array('<tr>', '</tr>'), array('', '{TR}'), $content);
		$array = explode('{TR}', $content);
		unset($array[count($array) - 1]);
		$data = array();
		$league = '';
		$mname = 1;
		foreach ($array as $key => $value)
		{
			if(($key % 2) != 0)
			{
				continue;
			}
			$vArr = explode('</td>', $value);
			$count = count($vArr);
			unset($vArr[$count -1], $vArr[$count -2], $vArr[$count -3], $vArr[$count -4]);
			$tmp = array();
			foreach ($vArr as $val)
			{
				if((strpos($val, 'rowspan') !== false) && (strpos($val, 'justify') !== false))
				{
					continue;
				}
				if(strpos($val, 'rowspan') !== false)
				{
					$league = trim(strip_tags($val));
					continue;
				}
				$tmp['league'] = $league;
				$tmp[] = trim(strip_tags($val));
			}
			array_push($data, $tmp);
		}
		foreach ($data as $key => $val)
		{
			$data[$key]['0'] = $key + 1;
		}
	
		return $data;
	}
	
	/**
	 * 进球彩内容解析
	 * @param unknown_type $content
	 * @return multitype:
	 */
	private function jqcParse($content)
	{
		$content = strip_tags($content, "<tr><td>");
		$content = str_replace(array('<tr>', '</tr>'), array('', '{TR}'), $content);
		$array = explode('{TR}', $content);
		unset($array[count($array) - 1]);
		$data = array();
		$league = '';
		foreach ($array as $key => $value)
		{
			$vArr = explode('</td>', $value);
			$count = count($vArr);
			unset($vArr[$count -1], $vArr[$count -2], $vArr[$count -3], $vArr[$count -4]);
			$tmp = array();
			foreach ($vArr as $val)
			{
				if((strpos($val, 'rowspan') !== false) && (strpos($val, 'justify') !== false))
				{
					continue;
				}
				if(strpos($val, 'rowspan') !== false)
				{
					$league = trim(strip_tags($val));
					continue;
				}
				$tmp['league'] = $league;
				$tmp[] = trim(strip_tags($val));
			}
			array_push($data, $tmp);
		}
	
		return $data;
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
			$datas[$key]['mname'] = $value[0];
			$datas[$key]['league'] = $value['league'];
			$datas[$key]['begin_date'] = $value[2] . ' 00:00';
			$names = explode(' VS ', $value[1]);
			$datas[$key]['home'] = $names[0];
			$datas[$key]['away'] = $names[1];
			$datas[$key]['source'] = $this->source;
		}
		$res = $this->CI->Match_model->saveTczq($datas);
		if(!$res)
			log_message('Error', '数据写入数据库失败|data:'.print_r($datas, true));
	}
}
