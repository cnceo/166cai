<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：体彩足球赛事抓取操作
 * 作    者：shigx
 * 修改日期：2015.03.13
 */
class Tczq_Match_Lottery 
{
	public static $TCZQ_TYPE_MAP = array(
		'sfc' => array(
			'url' => 'http://lottery.gov.cn/lottery/sfc/SFC.aspx',
			'ctype' => 1,
		),
		'bqc' => array(
			'url' => 'http://lottery.gov.cn/lottery/bqc/BQC.aspx',
			'ctype' => 2,
		),
		'jqc' => array(
			'url' => 'http://lottery.gov.cn/lottery/jqc/JQC.aspx',
			'ctype' => 3,
		),
	);

	private $type = '';
	private $source = 1;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
	}
	
	public function capture($param)
	{
		$this->source = $param['source'];
		$this->type = $param['type'];
		$this->getDataInsert();
	}

	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取网页数据并进行入库操作
	 * 修改日期：2015-03-13
	 */
	private function getDataInsert()
	{
		$data = array();
		$url = self::$TCZQ_TYPE_MAP[$this->type]['url'];
		$content = $this->CI->tools->request($url);
		$pregstr = '<input.*?name=[\'"]__VIEWSTATE[\'"].*?value=[\'"](.*?)[\'"].*?';
		$pregstr .= '<input.*?name=[\'"]__VIEWSTATEGENERATOR[\'"].*?value=[\'"](.*?)[\'"].*?';
		$pregstr .= '<input.*?name=[\'"]__EVENTVALIDATION[\'"].*?value=[\'"](.*?)[\'"].*?';
		$pregstr .= '<select.*?name=[\'"]DropDownListEvents[\'"].*?>(.*?<option.*?selected=[\'"]selected[\'"].*?value=[\'"](\d+)[\'"]>(\d+)<\/option>.*?)<\/select>.*?';
		$pregstr .= '<span.*?id=[\'"]LabelStartDate[\'"]>(.*?)<\/span>.*?';
		$pregstr .= '<span.*?id=[\'"]LabelEndDate[\'"]>(.*?)<\/span>.*?';
		$pregstr .= '<TABLE.*?>(.*)<\/TABLE>';
		$matches = array();
		preg_match("/$pregstr/is", $content, $matches);
		if(!isset($matches[6]) || !isset($matches[7]) || !isset($matches[8]) || !isset($matches[9]))
		{
			log_message('Error', '体彩信息抓取失败');
			return ;
		}
		if($this->type != 'jqc')
		{
			$source = $this->getTableArray($matches[9]);
		}
		else
		{
			$source = $this->getJqcTableArray($matches[9]);
		}
		
		$data['mid'] = $matches[6];
		$data['ctype'] = self::$TCZQ_TYPE_MAP[$this->type]['ctype'];
		$data['start_sale_time'] = $matches[7];
		$data['end_sale_time'] = $matches[8];
		$this->insertData($data, $source);
		
		unset($content, $data, $source);
		
		if(isset($matches[4]))
		{
			$step2 = preg_match_all('/<option.*?value=[\'"](\d+)[\'"]>(\d+)<\/option>/is', $matches[4], $issues);
			if($step2)
			{
				$pos = array_search($matches[5], $issues[1]);
				$mid_2 = isset($issues[1][$pos - 1]) ? $issues[1][$pos - 1] : '';
				$mid_3 = isset($issues[1][$pos - 2]) ? $issues[1][$pos - 2] : '';
				$mids_arr = array($mid_2, $mid_3);
				$mids_arr = array_filter($mids_arr);
			}
		}
		
		foreach ($mids_arr as $mid)
		{
			$post_data['__EVENTARGUMENT'] = '';
			$post_data['__LASTFOCUS'] = '';
			$post_data['__EVENTTARGET'] = 'DropDownListEvents';
			$post_data['__VIEWSTATE'] = $matches[1];
			$post_data['__VIEWSTATEGENERATOR'] = $matches[2];
			$post_data['__EVENTVALIDATION'] = $matches[3];
			
			$post_data['DropDownListEvents'] = $mid;
			
			$content = $this->CI->tools->request($url, $post_data);
			unset($matches);
			preg_match("/$pregstr/is", $content, $matches);
			if(!isset($matches[6]) || !isset($matches[7]) || !isset($matches[8]) || !isset($matches[9]))
			{
				log_message('Error', '抓取体彩信息失败');
				return ;
			}
			
			if($this->type != 'jqc')
			{
				$source = $this->getTableArray($matches[9]);
			}
			else
			{
				$source = $this->getJqcTableArray($matches[9]);
			}
			
			$data['mid'] = $matches[6];
			$data['ctype'] = self::$TCZQ_TYPE_MAP[$this->type]['ctype'];
			$data['start_sale_time'] = $matches[7];
			$data['end_sale_time'] = $matches[8];
			$this->insertData($data, $source);
		}
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
			$datas[$key]['league'] = $value[1];
			$date = str_replace(array('月', '日'), array('-', ''), $value[2]);
			$datas[$key]['begin_date'] = substr($data['end_sale_time'], 0, 4).'-'.$date.' 00:00';
			$datas[$key]['home'] = $value[3];
			$datas[$key]['away'] = $value[4];
			$datas[$key]['source'] = $this->source;
		}
		
		$res = $this->CI->Match_model->saveTczq($datas);
		if(!$res)
			log_message('Error', '数据写入数据库失败|data:'.print_r($datas, true));
	}
	
	/**
	 * 参    数：$table,字符型, 表格字符串
	 * 作    者：shigx
	 * 功    能：将HTML表格的每行每列转为数组，采集表格数据
	 * 修改日期：2015-03-13
	 */
	private function getTableArray($table)
	{
		$data = array();
		// 去掉 HTML 标记属性
		$table = preg_replace("#<TABLE.*?>.*?<\/TABLE>#si", "", $table);
		$table = preg_replace("#<TR[^>]*?>#si", "", $table);
		$table = preg_replace("#<TD[^>]*?>#si", "", $table);
		$table = str_replace("</TR>", "{TR}", $table);
		$table = str_replace("</TD>", "{TD}", $table);
		// 去掉 HTML 标记
		$table = preg_replace("#<[\/\!]*?[^<>]*?>#si", "", $table);
		// 去掉空白字符
		$table = preg_replace("#([\r\n])[\s]+#", "", $table);
		$table = str_replace("&nbsp;", "", $table);
		$table = str_replace(" ", "", $table);
		$table = explode('{TR}', $table);
		//将标题栏删除
		unset($table[0]); 
		array_pop($table);
		foreach ($table as $key => $tr)
		{
			$td = explode('{TD}', $tr);
			$td[0] = intval($td[0]);
			if(empty($td[0])) continue;
			$data[$td[0]][0] = $td[0];
			$data[$td[0]][1] = $td[1];
			$data[$td[0]][2] = $td[2];
			$tmp = explode('vs', $td[3]);
			$data[$td[0]][3] = isset($tmp[0]) ? $tmp[0] : $td[3];
			$data[$td[0]][4] = isset($tmp[1]) ? $tmp[1] : '';
		}
		
		return $data;
	}

	/**
	 * 参    数：$table,字符型, 表格字符串
	 * 作    者：shigx
	 * 功    能：将HTML表格的每行每列转为数组，采集表格数据。进球彩格式特殊，单独写方法处理
	 * 修改日期：2015-03-13
	 */
	private function getJqcTableArray($table)
	{
		$data = array();
		$table = str_replace("&nbsp;", "", $table);
		preg_match_all('#<TR\s+bgcolor.*?>.*?<TD.*?height.*?rowspan.*?>(.*?)<\/TD>.*?<TD.*?rowspan.*?>(.*?)<\/TD>.*?<TD.*?rowspan.*?>(.*?)<\/TD>.*?<TD.*?bgcolor.*?height.*?>(.*?)<\/TD>.*?<TD.*?rowspan.*?>.*?<\/TD>.*?<TD>.*?<\/TD>.*?<\/TR>.*?<TR.*?>.*?<TD.*?>(.*?)<\/TD>#is', $table, $matches);
		if(!empty($matches[1]))
		{
			foreach($matches[1] as $key => $val)
			{
				$val = trim($val);
				$data[$val][0] = $val;
				$data[$val][1] = isset($matches[2][$key]) ? trim($matches[2][$key]) : '';
				$data[$val][2] = isset($matches[3][$key]) ? trim($matches[3][$key]) : '';
				$data[$val][3] = isset($matches[4][$key]) ? trim($matches[4][$key]) : '';
				$data[$val][4] = isset($matches[5][$key]) ? trim($matches[5][$key]) : '';
			}
		}
		
		return $data;
	}
}
