<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 竞彩（竞足/竞篮/胜负彩）投注比例占比
 * @date:2017-05-25
 */

class Cli_Zhisheng_Live extends MY_Controller 
{
	public function __construct() 
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->load->model('api_zhisheng_model', 'api_model');
		$this->config->load('jcMatch');
		$this->redis = $this->config->item('redisList');
		$this->load->library('mipush');
	}
	
	/**
	 * 入口方法
	 */
	public function index()
	{
		$this->jclqLive();
		$this->jczqLive();
	}
	
	/*
	 * 竞足直播
	 * @date:2017-05-25
	 */
	public function jczqLive()
	{
		// 查询正在进行中的比赛
		$matches = $this->api_model->getJczqLiveMatch();

		if(!empty($matches))
		{
			// 获取缓存数据
			$data = $this->cache->get($this->redis['JCZQ_LIVEMATCH']);
			$data = json_decode($data, true);

			if(!empty($data))
			{
				$lastMatch = $this->matchFormat($data);
				// 对比数据
				foreach ($matches as $key => $match) 
				{
					$this->checkMatchLive('42' ,$match, $lastMatch);
				}
			}

			// 保存当前场次信息
			$data = $this->cache->save($this->redis['JCZQ_LIVEMATCH'], json_encode($matches), 0);
		}
	}

	/*
	 * 竞篮直播
	 * @date:2017-05-25
	 */
	public function jclqLive()
	{
		// 查询正在进行中的比赛
		$matches = $this->api_model->getJclqLiveMatch();

		if(!empty($matches))
		{
			// 获取缓存数据
			$data = $this->cache->get($this->redis['JCLQ_LIVEMATCH']);
			$data = json_decode($data, true);

			if(!empty($data))
			{
				$lastMatch = $this->matchFormat($data);
				// 对比数据
				foreach ($matches as $key => $match) 
				{
					$this->checkMatchLive('43' ,$match, $lastMatch);
				}
			}

			// 保存当前场次信息
			$data = $this->cache->save($this->redis['JCLQ_LIVEMATCH'], json_encode($matches), 0);
		}
	}
	
	public function matchFormat($data)
	{
		$match = array();
		if(!empty($data))
		{
			foreach ($data as $key => $items) 
			{
				$match[$items['mid']] = $items;
			}
		}
		return $match;
	}

	public function checkMatchLive($lid, $match, $lastMatch)
	{
		$cache = $lastMatch[$match['mid']];

		// 日期
		$weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
		$issue = '20' . substr($match['xid'], 0, 6);
		$week = $weekDays[date('w', strtotime($issue))] . substr($match['xid'], 6);
		
		if(!empty($cache))
		{
			if($lid == 42)
			{
				// 对比状态、比分
				if($match['state'] > 0 && $match['state'] <= 4)
				{
					// 中场推送
					if($cache['state'] < $match['state'] && $match['state'] == '2')
					{
						$pushData = array(
							'type'		=>	'jclive',
							'mid'		=>	$match['mid'],
							'lid'		=>	$lid,
							'title'		=>	'半场比分推送',
							'description'	=>	$week . ' ' . $match['home'] . ' ' . $match['bc'] . ' ' . $match['away'] . '（半场）',
						);
						$this->mipush->index('topic', $pushData);
					}
					// 完场推送
					if($cache['state'] < $match['state'] && $match['state'] == '4')
					{
						$pushData = array(
							'type'		=>	'jclive',
							'mid'		=>	$match['mid'],
							'lid'		=>	$lid,
							'title'		=>	'全场比分推送',
							'description'	=>	$week . ' ' . $match['home'] . ' ' . $match['hqt'] . ':' . $match['aqt'] . ' ' . $match['away'] . '（全场）',
						);
						$this->mipush->index('topic', $pushData);
					}
					// 竞足比分变化
					if(($cache['hqt'] != $match['hqt']) || ($cache['aqt'] != $match['aqt']))
					{		
						$content = $week;
						// 事件时间
						// $tm = ' ' . $this->getScoreTime($match['mid']);
						// $content .= $tm;
						$content .= ' ';
						$content .= $match['home'];
						$content .= ($cache['hqt'] != $match['hqt']) ? '（进球）' : '';
						$content .= ' ' . $match['hqt'] . ':' . $match['aqt'] . ' ';
						$content .= $match['away'];
						$content .= ($cache['aqt'] != $match['aqt']) ? '（进球）' : '';

						$pushData = array(
							'type'		=>	'jclive',
							'mid'		=>	$match['mid'],
							'lid'		=>	$lid,
							'title'		=>	'比分进球推送',
							'description'	=>	$content,
						);
						$this->mipush->index('topic', $pushData);
					}
				}
			}
			else
			{
				// 对比状态、比分
				if($match['state'] > 0 && $match['state'] <= 11)
				{
					// 中场推送
					if($cache['state'] < $match['state'] && $match['state'] == '4')
					{
						$abc = $match['as1'] + $match['as2'];
						$hbc = $match['hs1'] + $match['hs2'];
						$pushData = array(
							'type'		=>	'jclive',
							'mid'		=>	$match['mid'],
							'lid'		=>	$lid,
							'title'		=>	'半场比分推送',
							'description'	=>	$week . ' ' . $match['away'] . ' ' . $abc . ':' . ' ' . $hbc  . ' ' . $match['home'] . '（半场）',
						);
						$this->mipush->index('topic', $pushData);
					}
					// 完场推送
					if($cache['state'] < $match['state'] && ($match['state'] == '9' || $match['state'] == '11'))
					{
						$pushData = array(
							'type'		=>	'jclive',
							'mid'		=>	$match['mid'],
							'lid'		=>	$lid,
							'title'		=>	'全场比分推送',
							'description'	=>	$week . ' ' . $match['away'] . ' ' . $match['aqt'] . ':' . $match['hqt'] . ' ' . $match['home'] . '（全场）',
						);
						$this->mipush->index('topic', $pushData);
					}
				}
			}
			
		}
	}

	public function getScoreTime($mid)
	{
		$tm = '';
		$info = $this->api_model->getZqStatistics($mid);
		if(!empty($info['event']))
		{
			$event = json_decode($info['event'], true);
			krsort($event);
			foreach ($event as $key => $items) 
			{
				if(in_array($items['la_img'], array('1', '2', '3')) || in_array($items['lb_img'], array('1', '2', '3')))
				{
					$tm = $items['tm'];
					break;
				}
			}
		}
		return $tm;
	}
}
