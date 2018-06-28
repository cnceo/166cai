<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Copyright (c) 2015,上海瑞创网络科技股份有限公司
 * 摘    要：北京单场赛事抓取操作
 * 作    者：shigx
 * 修改日期：2015.03.16
 */

class Bjdc_Match_Bjlot
{
	public static $BJDC_TYPE_MAP = array(
		'sfgg' => array(
			'url' => 'http://www.bjlot.com/data/270ParlayGetGame.xml',
			'ctype' => 1,
		),
		'spf' => array(
			'url' => 'http://www.bjlot.com/data/200ParlayGetGame.xml',
			'ctype' => 2,
		),
		'jqs' => array(
			'url' => 'http://www.bjlot.com/data/230ParlayGetGame.xml',
			'ctype' => 3,
		),
		'bqc' => array(
			'url' => 'http://www.bjlot.com/data/240ParlayGetGame.xml',
			'ctype' => 4,
		),
		'dss' => array(
			'url' => 'http://www.bjlot.com/data/210ParlayGetGame.xml',
			'ctype' => 5,
		),
		'dcbf' => array(
			'url' => 'http://www.bjlot.com/data/250ParlayGetGame.xml',
			'ctype' => 6,
		),
		'xbcbf' => array(
			'url' => 'http://www.bjlot.com/data/260ParlayGetGame.xml',
			'ctype' => 7,
		),
	);
	private $type = '';
	private $source = 1;
	private $status = array(
		'销售中' => 1,
		'已停售' => 2,
		'已开奖' => 3,
	);

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('Match_model');
	}

	public function capture($param)
	{
		$this->source = $param['source'];
		$this->type = $param['type'];
		$this->$param['type']();
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取胜负过关数据操作
	 * 修改日期：2015-03-16
	 */
	private function sfgg()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['fixedodds'] = (string)$item->handicap;
					$codes['h'] = (string)$item->spitem->sp1;
					$codes['a'] = (string)$item->spitem->sp2;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] =  $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
				
				unset($data);
			}
			unset($matches);
		}
	}

	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取胜负过关数据操作
	 * 修改日期：2015-03-16
	 */
	private function spf()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['fixedodds'] = (string)$item->handicap;
					$codes['h'] = (string)$item->spitem->sp1;
					$codes['d'] = (string)$item->spitem->sp2;
					$codes['a'] = (string)$item->spitem->sp3;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] =  $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:' . print_r($data, true));
	
				unset($data);
			}
				
			unset($matches);
		}
	}
	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取进球数数据操作
	 * 修改日期：2015-03-16
	 */
	private function jqs()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['s0'] = (string)$item->spitem->sp1;
					$codes['s1'] = (string)$item->spitem->sp2;
					$codes['s2'] = (string)$item->spitem->sp3;
					$codes['s3'] = (string)$item->spitem->sp4;
					$codes['s4'] = (string)$item->spitem->sp5;
					$codes['s5'] = (string)$item->spitem->sp6;
					$codes['s6'] = (string)$item->spitem->sp7;
					$codes['s7'] = (string)$item->spitem->sp8;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] = $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
	
				unset($data);
			}
	
			unset($matches);
		}
	}

	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取半全场数据操作
	 * 修改日期：2015-03-16
	 */
	private function bqc()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['hh'] = (string)$item->spitem->sp1;
					$codes['hd'] = (string)$item->spitem->sp2;
					$codes['ha'] = (string)$item->spitem->sp3;
					$codes['dh'] = (string)$item->spitem->sp4;
					$codes['dd'] = (string)$item->spitem->sp5;
					$codes['da'] = (string)$item->spitem->sp6;
					$codes['ah'] = (string)$item->spitem->sp7;
					$codes['ad'] = (string)$item->spitem->sp8;
					$codes['aa'] = (string)$item->spitem->sp9;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] = $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
	
				unset($data);
			}
	
			unset($matches);
		}
	}

	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取上下盘单双数数据操作
	 * 修改日期：2015-03-16
	 */
	private function dss()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['u_s'] = (string)$item->spitem->sp1;
					$codes['u_d'] = (string)$item->spitem->sp2;
					$codes['d_s'] = (string)$item->spitem->sp3;
					$codes['d_d'] = (string)$item->spitem->sp4;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] = $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
	
				unset($data);
			}
	
			unset($matches);
		}
	}

	
	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取单场比分数据操作
	 * 修改日期：2015-03-16
	 */
	private function dcbf()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['1:0'] = (string)$item->spitem->sp1;
					$codes['2:0'] = (string)$item->spitem->sp2;
					$codes['2:1'] = (string)$item->spitem->sp3;
					$codes['3:0'] = (string)$item->spitem->sp4;
					$codes['3:1'] = (string)$item->spitem->sp5;
					$codes['3:2'] = (string)$item->spitem->sp6;
					$codes['4:0'] = (string)$item->spitem->sp7;
					$codes['4:1'] = (string)$item->spitem->sp8;
					$codes['4:2'] = (string)$item->spitem->sp9;
					$codes['h_o'] = (string)$item->spitem->sp10;
					$codes['0:0'] = (string)$item->spitem->sp11;
					$codes['1:1'] = (string)$item->spitem->sp12;
					$codes['2:2'] = (string)$item->spitem->sp13;
					$codes['3:3'] = (string)$item->spitem->sp14;
					$codes['d_o'] = (string)$item->spitem->sp15;
					$codes['0:1'] = (string)$item->spitem->sp16;
					$codes['0:2'] = (string)$item->spitem->sp17;
					$codes['1:2'] = (string)$item->spitem->sp18;
					$codes['0:3'] = (string)$item->spitem->sp19;
					$codes['1:3'] = (string)$item->spitem->sp20;
					$codes['2:3'] = (string)$item->spitem->sp21;
					$codes['0:4'] = (string)$item->spitem->sp22;
					$codes['1:4'] = (string)$item->spitem->sp23;
					$codes['2:4'] = (string)$item->spitem->sp24;
					$codes['a_o'] = (string)$item->spitem->sp25;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] = $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
	
				unset($data);
			}
	
			unset($matches);
		}
	}

	/**
	 * 参    数：无
	 * 作    者：shigx
	 * 功    能：抓取下半场比分数据操作
	 * 修改日期：2015-03-16
	 */
	private function xbcbf()
	{
		$data = array();
		$url = self::$BJDC_TYPE_MAP[$this->type]['url']."?dt=".date('r')."&_=".time();
		$xml = simplexml_load_file($url);
		if(!$xml)
		{
			log_message('Error', '信息抓取失败');
			return ;
		}
	
		$mid = (string)$xml->gameinfos->thisDraw->no;
		$matches = $xml->matches;
		unset($xml);
		if($matches)
		{
			foreach ($matches->matchInfo as $matchInfo)
			{
				$m_date = (string)$matchInfo->matchTime->attributes();
				$i = 0;
				foreach ($matchInfo->matchelem->item as $item)
				{
					$data[$i]['mid'] = $mid;
					$data[$i]['m_date'] = $m_date;
					$data[$i]['mname'] = (string)$item->no;
					$data[$i]['ctype'] = self::$BJDC_TYPE_MAP[$this->type]['ctype'];
					$data[$i]['league'] = (string)$item->leagueName;
					$data[$i]['home'] = (string)$item->hostFull;
					$data[$i]['away'] = (string)$item->guestFull;
					$data[$i]['begin_time'] = (string)$item->endTime;
					$codes['1:0'] = (string)$item->spitem->sp1;
					$codes['2:0'] = (string)$item->spitem->sp2;
					$codes['2:1'] = (string)$item->spitem->sp3;
					$codes['3:0'] = (string)$item->spitem->sp4;
					$codes['3:1'] = (string)$item->spitem->sp5;
					$codes['3:2'] = (string)$item->spitem->sp6;
					$codes['4:0'] = (string)$item->spitem->sp7;
					$codes['4:1'] = (string)$item->spitem->sp8;
					$codes['4:2'] = (string)$item->spitem->sp9;
					$codes['h_o'] = (string)$item->spitem->sp10;
					$codes['0:0'] = (string)$item->spitem->sp11;
					$codes['1:1'] = (string)$item->spitem->sp12;
					$codes['2:2'] = (string)$item->spitem->sp13;
					$codes['3:3'] = (string)$item->spitem->sp14;
					$codes['d_o'] = (string)$item->spitem->sp15;
					$codes['0:1'] = (string)$item->spitem->sp16;
					$codes['0:2'] = (string)$item->spitem->sp17;
					$codes['1:2'] = (string)$item->spitem->sp18;
					$codes['0:3'] = (string)$item->spitem->sp19;
					$codes['1:3'] = (string)$item->spitem->sp20;
					$codes['2:3'] = (string)$item->spitem->sp21;
					$codes['0:4'] = (string)$item->spitem->sp22;
					$codes['1:4'] = (string)$item->spitem->sp23;
					$codes['2:4'] = (string)$item->spitem->sp24;
					$codes['a_o'] = (string)$item->spitem->sp25;
					$data[$i]['game_type'] = (string)$item->gameTypeName;
					$data[$i]['codes'] = serialize($codes);
					$status = (string)$item->matchstopstate;
					$data[$i]['status'] = $this->status[$status];
					$data[$i]['source'] = $this->source;
					unset($codes, $status);
					$i++;
				}
	
				$result = $this->CI->Match_model->saveBjdc($data);
				if(!$result)
					log_message('Error', '信息更新到数据库失败|data:'.print_r($data, true));
	
				unset($data);
			}
	
			unset($matches);
		}
	}
}
