<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 智胜竞彩数据接口缓存
 * @date:2016-04-16
 */
class Cli_Cfg_Zhisheng_Match extends MY_Controller
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_zhisheng_match_model','Match');
	}
	
	/*
	 * 获取竞彩足球在售期次信息
	 * @date:2016-04-16
	 */
	public function getJczqIssue()
	{
		$match = $this->Match->getJczqMatch();

		$issueAry = array();
		// 获取当前在售的所有期次
		if(!empty($match))
		{		
			foreach ($match as $key => $items) 
			{
				$items['issue'] = str_replace('-', '', $items['issue']);
				$issueAry[$items['issue']] = array();
			}
			$issueAry = array_keys($issueAry);	
		}

		// 获取当前竞足数据缓存
		$historyData = $this->Match->getJczqHistory();

		$historyArry = array();
		if(!empty($historyData))
		{
			foreach ($historyData as $key => $history) 
			{
				$historyArry[$history['expect']] = array();
			}
			$historyArry = array_keys($historyArry);
		}

		sort($historyArry);
		sort($issueAry);
		// 相同则返回空 不再执行
		if($historyArry == $issueAry)
		{
			$issueAry = array();
		}

		return $issueAry;
	}

	/*
	 * 处理竞彩足球 对阵数据
	 * @date:2016-04-16
	 */
	public function index()
	{
		// set_time_limit(0);
		// $issueAry = $this->getJczqIssue();

		// $matchData = array();
		// if(!empty($issueAry))
		// {
		// 	foreach ($issueAry as $key => $issue) 
		// 	{
		// 		// 处理对阵数据
		// 		$match = $this->Match->setJczqMatch($issue, 6);
		// 		$matchData += $match;
		// 	}		
		// 	$this->Match->saveJczqMatch($matchData);
		// }

		// 五大联赛
		$this->europeLeague();
	}

	// 五大联赛信息
    public $league = array(
    	0 => array(
			'cn' => '英格兰超级联赛',
			'cnshort' => '英超',
			'en' => 'English Premier League',
			'lid' => '92',
			'kind' => '1',
			'logo' => 'yc.png',
		),
		1 => array(
			'cn' => '西班牙甲级联赛',
			'cnshort' => '西甲',
			'en' => 'Spanish Primera Division',
			'lid' => '85',
			'kind' => '1',
			'logo' => 'xj.png',
		),
		2 => array(
			'cn' => '德国甲级联赛',
			'cnshort' => '德甲',
			'en' => 'German Bundesliga',
			'lid' => '39',
			'kind' => '1',
			'logo' => 'dj.png',
		),
		3 => array(
			'cn' => '法国甲级联赛',
			'cnshort' => '法甲',
			'en' => 'French Le Championnat Ligue 1',
			'lid' => '93',
			'kind' => '1',
			'logo' => 'fj.png',
		),
		4 => array(
			'cn' => '意大利甲级联赛',
			'cnshort' => '意甲',
			'en' => 'Italian Serie A',
			'lid' => '34',
			'kind' => '1',
			'logo' => 'yj.png',
		),
    );

    /*
 	 * 获取联赛最新一期的赛程
 	 * @date:2016-08-16
 	 */
	public function getLastSid($lid)
	{
		$data = array();
		if(!empty($lid))
		{
			$data = $this->Match->getLastSid($lid);
		}
		return $data;
	}

	/*
	 * 五大联赛资讯 - 积分榜
	 * @date:2016-08-16
	 */
	public function europeLeague()
	{
		$leagueInfo = $this->league;

		if(!empty($leagueInfo))
		{
			foreach ($leagueInfo as $key => $items) 
			{
				// 获取联赛赛季
				$sidResult = $this->getLastSid($items['lid']);
				$sidData = current($sidResult);

				if(!empty($sidData['sid']))
				{
					$this->getLeagueRank($items['lid'], $sidData['sid'], $items['kind']);
					$this->getLeagueSchedule($items['lid'], $sidData['sid'], $items['kind']);
				}
			}
		}
	}

	/*
	 * 五大联赛资讯 - 获取联赛积分榜信息
	 * @date:2016-08-16
	 */
	public function getLeagueRank($lid, $sid, $kind = '1')
	{
		$info = $this->Match->getScoreRank($lid, $sid);
	
		$rankData = array();
		if(!empty($info['row']))
		{
			foreach ($info['row'] as $key => $items) 
			{
				$rankData[$key]['tid'] = $items['tid'];
				$rankData[$key]['name'] = $items['name'];
				$rankData[$key]['w'] = $items['w'];
				$rankData[$key]['d'] = $items['d'];
				$rankData[$key]['l'] = $items['l'];
				$rankData[$key]['score'] = $items['score'];
			}
		}
		
		if(!empty($rankData))
		{
			$this->Match->saveEuropeScoreRank($lid, $rankData);
		}
	}

	/*
	 * 五大联赛资讯 - 赛程
	 * @date:2016-08-16
	 */
	public function getLeagueSchedule($lid, $sid, $kind = '1')
	{
		$info = $this->Match->getMatchSchedule($lid, $sid);

		$scheduleData = array();
		if(!empty($info['row'][0]))
		{
			// 按时间排序
			$mtimeArry = array();
			foreach ($info['row'] as $key => $items) 
			{
				$mtimeArry[] = $items['mtime'];
			}

			array_multisort($mtimeArry, SORT_ASC, $info['row']);

			$lastMtime = 0;
			$matches = array();
			$currentRound = '';
			// 按联赛轮次分组
			foreach ($info['row'] as $key => $items) 
			{
				// 获取默认轮次
				if(empty($items['hs']) && empty($items['as']) && $items['mtime'] >= time())
				{
					if( $lastMtime == 0 || $items['mtime'] <= $lastMtime )
					{
						// 检查是否为延期轮次
						if($currentRound === '')
						{
							$lastMtime = $items['mtime'];
							$currentRound = $items['rid'];
						}

						if($currentRound !== '' && ($currentRound - $items['rid'] <= 1))
						{
							$lastMtime = $items['mtime'];
							$currentRound = $items['rid'];
						}
					}
				}

				$data = array(
					'mid' => $items['mid'],
					'home' => $items['home'],
					'away' => $items['away'],
					'htid' => $items['htid'],
					'atid' => $items['atid'],
					'hs' => $items['hs'],
					'as' => $items['as'],
					'mtime' => $items['mtime'],
					'rid' => $items['rid'],
				);
				$matches[$items['rid']][] = $data;
			}

			// 当前轮次信息存在并且不是最后三轮
			if(!empty($matches[$currentRound]))
			{
				$roundArry = array_keys($matches);
				sort($roundArry);
				// 最后三轮
				$lastRound = array_slice($roundArry, -3, 3);
				if(in_array($currentRound, array_slice($roundArry, -3, 3)))
				{
					$nextRound = $lastRound;
				}
				else
				{
					$roundIndex = array_flip($roundArry);
					$index = $roundIndex[$currentRound];
					$nextRound = array_slice($roundArry, $index, 3);
				}

				foreach ($nextRound as $key => $round) 
				{
					$scheduleData[$round] = $matches[$round];
				}
			}
		}
		if(!empty($scheduleData))
		{
			$data = array(
				'current' => $currentRound,
				'schedule' => $scheduleData,
			);
			$this->Match->saveEuropeSchedule($lid, $data);
		}
	}

}