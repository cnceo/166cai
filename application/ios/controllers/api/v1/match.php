<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 比赛信息 接口
 * @version:V1.2
 * @date:2016-04-11
 */
class Match extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_zhisheng_match_model','Match');
	}

	public function index()
    {
        $result = array(
			'status' => '1',
			'msg' => '通讯成功',
			'data' => $this->getRequestHeaders()
		);
		echo json_encode($result);
    }

    // 配置显示的联赛或杯赛信息
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
		5 => array(
			'cn' => '中国超级联赛',
			'cnshort' => '中超',
			'en' => 'China Premier League',
			'lid' => '152',
			'kind' => '1',
			'logo' => 'zc.png',
		),
		6 => array(
			'cn' => '欧洲联赛冠军杯',
			'cnshort' => '欧冠杯',
			'en' => 'UEFA Champions League',
			'lid' => '74',
			'kind' => '2',
			'logo' => 'og.png',
		),
		7 => array(
			'cn' => '欧洲足球锦标赛',
			'cnshort' => '欧锦赛',
			'en' => 'UEFA European Championship',
			'lid' => '87',
			'kind' => '2',
			'logo' => 'ozb.png',
		),
		8 => array(
			'cn' => '亚洲联赛冠军杯',
			'cnshort' => '亚冠杯',
			'en' => 'Asia Champions League Cup',
			'lid' => '139',
			'kind' => '2',
			'logo' => 'yg.png',
		),
    );

	// 赛事状态
	public $matchState = array(
		'0' => '未知',
		'1' => '上半场',
		'2' => '中场',
		'3' => '下半场',
		'4' => '完场',
		'5' => '中断',
		'6' => '取消',
		'7' => '加时',
		'8' => '加时',
		'9' => '加时',
		'10' => '完场',
		'11' => '点球',
		'12' => '全',
		'13' => '延期',
		'14' => '腰斩',
		'15' => '待定',
		'16' => '金球',
		'17' => '未开赛',
	);

	// 欧赔主流博彩公司
	public $cidInfo = array(
		'10000'	=> '竞彩官方',
		'451' 	=> '威廉希尔',
		'442' 	=> '澳门',
		'30' 	=> 'bet 365',	
		'2' 	=> '10BET',
		'124' 	=> 'Centrebet',
		'133' 	=> 'Coral',
		'156' 	=> 'Eurobet',
		'164' 	=> 'Expekt',
		'175' 	=> 'gamebookers',
		'537' 	=> 'IBCBET',
		'211' 	=> 'Interwetten',
		'258' 	=> 'Nike',
		'260' 	=> 'Nordicbet',
		'267' 	=> 'Oddset',
		'286' 	=> 'PinnacleSports',
		'325' 	=> 'SNAI',
		'577' 	=> 'STSBet',
		'395' 	=> 'TOTO',
		'47' 	=> 'bet-at-home',
		'116' 	=> 'bwin',
		'893' 	=> '伟德',
		'654' 	=> '利记sbobet',
		'444' 	=> '博天堂',
		'450' 	=> '明陞',
		'454' 	=> '易胜博',
		'449' 	=> '立博',
		'448' 	=> '金宝博',
	);
	
	/*
 	 * 赛事列表
 	 * @date:2016-04-11
 	 */
	public function matchList()
	{
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";

		$leagueInfo = $this->league;

		if(!empty($leagueInfo))
		{
			foreach ($leagueInfo as $key => $items) 
			{
				$leagueInfo[$key]['logo'] = $protocol . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/' . $items['logo'];
			}
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $leagueInfo
		);

		echo json_encode($result);
	}

	/*
 	 * 获取联赛最新一期的赛程
 	 * @date:2016-04-11
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
 	 * 获取联赛赛程轮次
 	 * @date:2016-04-11
 	 */
	public function getRoundData($match, $kind)
	{
		switch ($kind) 
		{
			case '1':
				return array($match['rid'] => '第' . $match['rid'] . '轮');
				break;

			case '2':
				return array($match['oid'] => $match['oname']);
				break;
			
			default:
				return array();
				break;
		}
	}

	/*
 	 * 获取 联赛 - 赛程
 	 * @date:2016-04-11
 	 */
	public function getMatchSchedule()
	{
		$lid = $this->input->get("lid", true);
		$round = $this->input->get("round", true);

		if(empty($lid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少联赛标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛最新一期的赛程
		$sidResult = $this->getLastSid($lid);
		$sidData = current($sidResult);

		if(empty($sidData['sid']))
		{
			$result = array(
				'status' => '0',
				'msg' => '获取联赛赛程失败',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛赛程赛果
		$data = $this->Match->getMatchSchedule($lid, $sidData['sid']);

		// 数据格式处理
		$orderType = array('1' => 'rid', '2' => 'oid');
		$roundData = array();	// 轮次信息 联赛取rid 杯赛取oid
		$roundArry = array();
		$roundSort = array();	
		$matchData = array();	// 赛程
		$defaultRound = '';
		if(!empty($data))
		{
			// 获取联赛类型
			$mData = $this->Match->getScoreRank($lid, $sidData['sid']);
			$kind = (!empty($mData['c']['type']) && $mData['c']['type'] == 'league')?'1':'2';

			$lastMtime = 0;
			foreach ($data['row'] as $key => $items) 
			{
				if($items[$orderType[$kind]] !== '')
				{
					$roundArry += $this->getRoundData($items, $kind);
					$items['mtime'] = date('Y-m-d H:i:s', $items['mtime']);
					$items['remark'] = '';
					$matchData[$items[$orderType[$kind]]][] = $items;
					// 获取默认轮次
					if(empty($items['hs']) && empty($items['as']) && $items['mtime'] >= date('Y-m-d H:i:s'))
					{
						$cround = $items[$orderType[$kind]];
						if( $lastMtime == 0 || $items['mtime'] <= $lastMtime )
						{
							// 检查是否为延期轮次
							if($defaultRound === '')
							{
								$lastMtime = $items['mtime'];
								$defaultRound = $items[$orderType[$kind]];
							}

							if($defaultRound !== '' && ($defaultRound - $items[$orderType[$kind]] <= 1))
							{
								$lastMtime = $items['mtime'];
								$defaultRound = $items[$orderType[$kind]];
							}
						}
					}
				}
			}
		}

		if(!empty($roundArry))
		{
			foreach ($roundArry as $key => $rname) 
			{
				$roundSort[] = $key;
				$rname = $rname?$rname:'--';
				array_push($roundData, array('rname' => $rname, 'round' => $key));
			}
		}

		// 轮次 正序排序
		array_multisort($roundSort, SORT_ASC, $roundData);

		// 取不到默认轮次 取最新轮次
		if($defaultRound === '')
		{
			$endRound = end($roundData);
			$defaultRound = $endRound['round'];
		}

		// 默认最新轮次比赛赛程
		if( $round != '' && !empty($matchData[$round]) )
		{
			$round = $round;
			$match = $matchData[$round];
		}
		else
		{
			$round = $defaultRound;
			$match = $matchData[$round];
		}

		// 赛程 按时间排序
		$mtime = array();
		foreach ($match as $matchItems) 
		{
		    $mtime[] = $matchItems['mtime'];
		}
		array_multisort($mtime, SORT_ASC, $match);

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => array(
				'statistic' => array(
					'lid' => $lid,
					'sid' => $sidData['sid'],
					'sname' => $sidData['sname'],
					'kind' => $kind,
					'round' => (string)$round
				),
				'roundData' => $roundData,
				'detail' => $match
			)
		);

		echo json_encode($result);die;
	}

	/*
 	 * 获取 联赛 - 积分榜
 	 * @date:2016-04-12
 	 */
	public function getScoreRank()
	{
		$lid = $this->input->get("lid", true);

		if(empty($lid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少联赛标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛最新一期的赛程
		$sidResult = $this->getLastSid($lid);
		$sidData = current($sidResult);

		if(empty($sidData['sid']))
		{
			$result = array(
				'status' => '0',
				'msg' => '获取联赛赛程失败',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛赛程赛果
		$data = $this->Match->getScoreRank($lid, $sidData['sid']);

		$rankData = array();
		if(!empty($data))
		{
			foreach ($data['row'] as $key => $items) 
			{
				$rankData[$key]['tid'] = $items['tid'];
				$rankData[$key]['name'] = $items['name'];
				$rankData[$key]['num'] = $items['enum'];	// 已赛全部
				$rankData[$key]['w'] = $items['w'];			// 胜全部
				$rankData[$key]['d'] = $items['d'];			// 平场数
				$rankData[$key]['l'] = $items['l'];			// 负全部
				$rankData[$key]['goal'] = $items['goal'];	// 进
				$rankData[$key]['loss'] = $items['loss'];	// 失
				$rankData[$key]['diff'] = (string)($items['goal'] - $items['loss']);	// 净
				$rankData[$key]['score'] = $items['score'];	// 积分
				$rankData[$key]['group'] = $items['group'] ? $items['group'] : '';	// 杯赛分组
			}
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $rankData
		);
		
		echo json_encode($result);die;
	}

	/*
 	 * 获取 联赛 - 射手榜
 	 * @date:2016-04-12
 	 */
	public function getShotRank()
	{
		$lid = $this->input->get("lid", true);

		if(empty($lid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少联赛标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛最新一期的赛程
		$sidResult = $this->getLastSid($lid);
		$sidData = current($sidResult);

		if(empty($sidData['sid']))
		{
			$result = array(
				'status' => '0',
				'msg' => '获取联赛赛程失败',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取联赛赛程赛果
		$data = $this->Match->getShotRank($lid, $sidData['sid']);

		$rankData = array();
		if(!empty($data['row']))
		{
			foreach ($data['row'] as $key => $items) 
			{
				$rankData[$key]['tid'] = $items['tid'];
				$rankData[$key]['name'] = $items['name'];
				$rankData[$key]['pid'] = $items['pid'];
				$rankData[$key]['pname'] = $items['pname'];
				// 总进球 = 进球 + 点球
				$rankData[$key]['jq'] = (string)($items['jq'] + $items['dq']);
				$rankData[$key]['dq'] = $items['dq'];
				$jqSort[] = $rankData[$key]['jq'];
			}
			array_multisort($jqSort, SORT_DESC, $rankData);
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $rankData
		);
		
		echo json_encode($result);die;
	}

	/*
 	 * 获取 联赛 - 直播列表
 	 * @date:2016-04-22
 	 */
	public function liveList()
	{
		// 获取当前日期前后两天的期次信息
		$exceptData = array(
			0 => date("Y-m-d", time()-86400*2),
			1 => date("Y-m-d", time()-86400),
			2 => date("Y-m-d", time()),
			3 => date("Y-m-d", time()+86400),
			4 => date("Y-m-d", time()+86400*2),
		);

		$matchInfo = array();
		if(!empty($exceptData))
		{
			foreach ($exceptData as $key => $except) 
			{
				$match = $this->getZcMatch($except);
				$matchInfo = array_merge($matchInfo, $match);
			}
		}

		// 获取最新消息编号
		$msgData = $this->Match->getLiveNew();

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => array(
				'msgId' => $msgData['msg']['msgId'],
				'reset' => $msgData['msg']['reset'],
				'ctime' => date('Y-m-d H:i:s'),
				'matchInfo' => $matchInfo
			)			
		);
		
		echo json_encode($result);die;
	}

	/*
 	 * 获取 联赛 - 期次详情
 	 * @date:2016-04-22
 	 */
	public function getZcMatch($except)
	{
		$match = $this->Match->getMatchScore($except, 6);

		$matchArry = array();
		if(!empty($match['row'][0]))
		{
			$matchArry = $match['row'];
		}
		elseif(!empty($match['row']['xid']))
		{
			$matchArry[0] = $match['row'];
		}

		$matchInfo = array();
		if(!empty($matchArry))
		{
			$weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
			$matchState = $this->matchState;
			foreach ($matchArry as $key => $items) 
			{
				$matchInfo[$key]['issue'] = $except;
				$matchInfo[$key]['xid'] = $items['xid'];
				$matchInfo[$key]['mid'] = $items['mid'];
				$matchInfo[$key]['ln'] = $items['ln'];
				$matchInfo[$key]['mtime'] = $this->getMatchTime($items['mtime']);
				$matchInfo[$key]['hteam'] = $items['hteam'];
				$matchInfo[$key]['ateam'] = $items['ateam'];
				$matchInfo[$key]['hscore'] = $items['hscore'];
				$matchInfo[$key]['ascore'] = $items['ascore'];
				$matchInfo[$key]['htid'] = $items['htid'];
				$matchInfo[$key]['atid'] = $items['atid'];
				$matchInfo[$key]['lid'] = $items['lid'];
				$matchInfo[$key]['lc'] = $items['lc'];
				$matchInfo[$key]['state'] = $items['state']?$items['state']:'0';
				$matchInfo[$key]['stateMsg'] = $matchState[$matchInfo[$key]['state']];
				$matchInfo[$key]['ktime'] = $this->getMatchTime($items['ktime']);
				$matchInfo[$key]['week'] = $weekDays[date('w', strtotime($except))];
				$matchInfo[$key]['nameType'] = $this->getNameType($items['lid']);
			}
		}

		return $matchInfo;
	}

	/*
 	 * 获取 五大联赛标识
 	 * @date:2016-04-22
 	 */
	public function getNameType($lid)
	{
		$type = '0';
		if(in_array($lid, array('34', '92', '85', '39', '93')))
		{
			$type = '1';
		}
		return $type;
	}

	/*
 	 * 获取 及时比分消息
 	 * @date:2016-04-22
 	 */
	public function liveScore()
	{
		$reset = $this->input->get("reset", true);	// 刷新标记
		$msgId = $this->input->get("msgId", true);	// 消息编号

		if(empty($reset) || empty($msgId))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少必要标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取最新的消息编号
		$msgData = $this->Match->getLiveNew();

		// 刷新直播列表
		if($msgData['msg']['reset'] != $reset)
		{
			$result = array(
				'status' => '2',
				'msg' => '消息列表过期',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$liveData = array();
		// 获取最新的消息
		if($msgData['msg']['msgId'] > $msgId)
		{
			// 检查消息间隔最大值
			if($msgData['msg']['msgId'] - $msgId > 50)
			{
				$result = array(
					'status' => '2',
					'msg' => '消息列表id超过最大值',
					'data' => ''
				);
				echo json_encode($result);die;
			}

			if(!empty($msgData['detail']))
			{
				$last = array();
				$matchState = $this->matchState;
				foreach ($msgData['detail'] as $key => $items) 
				{
					$last[$items['bh']]['mid'] = $items['bh'];
					$last[$items['bh']]['state'] = $items['state'];
					$last[$items['bh']]['stateMsg'] = $matchState[$items['state']];
					$last[$items['bh']]['hscore'] = $items['hf'];
					$last[$items['bh']]['ascore'] = $items['af'];
					$last[$items['bh']]['hr'] = $items['hr'];
					$last[$items['bh']]['ar'] = $items['ar'];
					$last[$items['bh']]['tstime'] = $this->getMatchTime($items['tstime']);
					$last[$items['bh']]['bc'] = $items['bc'];
					$last[$items['bh']]['stime'] = $this->getMatchTime($items['stime']);
				}
				$liveData += $last;
			}

			$msgIndex = $msgData['msg']['msgId'] - 1;
			for ($i = $msgIndex; $i >= $msgId; $i--) 
			{ 
				$liveDetail = $this->getMsgLiveDetail($i);

				if(!empty($liveDetail))
				{
					$liveData += $liveDetail;
				}			
			}

			$liveData = array_values($liveData);
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => array(
				'msgId' => $msgData['msg']['msgId'],
				'reset' => $msgData['msg']['reset'],
				'ctime' => date('Y-m-d H:i:s'),
				'liveData' => $liveData
			)			
		);

		echo json_encode($result);die;
	}

	/*
 	 * 获取 指定消息即时比分信息
 	 * @date:2016-04-22
 	 */
	public function getMsgLiveDetail($msgId)
	{
		$liveArry = $this->Match->getMsgLiveDetail($msgId);

		$liveData = array();
		if(!empty($liveArry))
		{
			$matchState = $this->matchState;
			foreach ($liveArry as $key => $items) 
			{
				$liveData[$items['bh']]['mid'] = $items['bh'];
				$liveData[$items['bh']]['state'] = $items['state'];
				$liveData[$items['bh']]['stateMsg'] = $matchState[$items['state']];
				$liveData[$items['bh']]['hscore'] = $items['hf'];
				$liveData[$items['bh']]['ascore'] = $items['af'];
				$liveData[$items['bh']]['hr'] = $items['hr'];
				$liveData[$items['bh']]['ar'] = $items['ar'];
				$liveData[$items['bh']]['ar'] = $items['ar'];
				$liveData[$items['bh']]['tstime'] = $this->getMatchTime($items['tstime']);
				$liveData[$items['bh']]['bc'] = $items['bc'];
				$liveData[$items['bh']]['stime'] = $this->getMatchTime($items['stime']);
			}
		}

		return $liveData;
	}


	/*
 	 * 赛事 - 详情
 	 * @date:2016-04-14
 	 */
	// http://www.166cai.com/app/api/v1/match/matchDetail?mid=930150
	public function matchDetail()
	{
		// 比赛详情 1022
		$mid = $this->input->get("mid", true);

		if(empty($mid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$match = $this->Match->getMatchDetail($mid);

		$matchData = array();
		if(!empty($match))
		{
			$matchState = $this->matchState;
			$matchData['mid'] = $match['mid'];
			$matchData['hteam'] = $match['home'];
			$matchData['ateam'] = $match['away'];
			$matchData['htid'] = $match['htid'];
			$matchData['atid'] = $match['atid'];
			$matchData['mtime'] = date('Y-m-d H:i:s', $match['mtime']);
			$matchData['lid'] = $match['lid'];
			$matchData['sid'] = $match['sid'];
			$matchData['rid'] = $match['rid'];
			$matchData['ln'] = $match['ln'];
			$matchData['homelogo'] = str_replace('http://', 'https://', $match['homelogo']);
			$matchData['awaylogo'] = str_replace('http://', 'https://', $match['awaylogo']);
			$matchData['hscore'] = (is_numeric($match['_hs']))?(string)$match['_hs']:(is_numeric($match['hs'])?(string)$match['hs']:'0');
			$matchData['ascore'] = (is_numeric($match['_as']))?(string)$match['_as']:(is_numeric($match['as'])?(string)$match['as']:'0');
			$matchData['hhscore'] = is_numeric($match['hhs'])?(string)$match['hhs']:'0';
			$matchData['ahscore'] = is_numeric($match['has'])?(string)$match['has']:'0';
			$matchData['ktime'] = $this->getMatchTime($match['_ktime']);
			$matchData['state'] = $match['state']?$match['state']:'0';
			$matchData['stateMsg'] = $matchState[$matchData['state']];
			$matchData['ctime'] = date('Y-m-d H:i:s');
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $matchData
		);
		
		echo json_encode($result);die;
	}

	/*
 	 * 获取比赛时间
 	 * @date:2016-04-18
 	 */
	public function getMatchTime($time)
	{
		$mtime = '';
		if(!empty($time))
		{
			$timeArry = explode(',', $time);
			$mtime = $timeArry[0] . '-' . $timeArry[1] . '-' . $timeArry[2] . ' ' . $timeArry[3] . ':' . $timeArry[4] . ':00';
		}
		return $mtime;
	}

	/*
 	 * 赛事 - 预计比赛阵容
 	 * @date:2016-04-18
 	 */
	public function getMatchPlayer()
	{
		$mid = $this->input->get("mid", true);

		if(empty($mid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$matchData = $this->Match->getMatchPlayer($mid);

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $matchData
		);

		echo json_encode($result);die;
	}

	/*
 	 * 赛事 - 直播
 	 * @date:2016-04-18
 	 */
	// http://www.166cai.com/app/api/v1/match/getMatchLive?mid=930150
	public function getMatchLive()
	{
		$mid = $this->input->get("mid", true);

		if(empty($mid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$matchData = $this->Match->getMatchLive($mid);

		// 获取赛事状态
		$matchInfo = $this->Match->getMatchDetail($mid);

        $liveData = array();

        $weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
		if(!empty($matchData) && !empty($matchInfo))
		{
			$matchState = $this->matchState;
			$scoreArry = explode('-', $matchData['match']['score']);
			$liveData['match']['lname'] = $matchData['match']['lname'];
			$liveData['match']['hteam'] = $matchData['match']['hname'];
			$liveData['match']['ateam'] = $matchData['match']['aname'];
            $liveData['match']['hscore'] = $matchInfo['_hs']?$matchInfo['_hs']:($scoreArry[0]?trim($scoreArry[0]):'0');
            $liveData['match']['ascore'] = $matchInfo['_as']?$matchInfo['_as']:($scoreArry[1]?trim($scoreArry[1]):'0');
            $liveData['match']['hhscore'] = $matchInfo['hhs']?$matchInfo['hhs']:'';
            $liveData['match']['ahscore'] = $matchInfo['has']?$matchInfo['has']:'';
            $liveData['match']['ktime'] = $this->getMatchTime($matchInfo['_ktime']);
            $liveData['match']['stime'] = $this->getMatchTime($matchData['match']['stime']);
            $liveData['match']['ctime'] = date('Y-m-d H:i:s');
            $liveData['match']['week'] = $weekDays[date('w', strtotime($liveData['match']['stime']))];
            $liveData['match']['state'] = $matchInfo['state'];
            $liveData['match']['stateMsg'] = $matchState[$matchInfo['state']];
			
			// 由于接口数据一条和多条格式不一致 做区分处理
			if(empty($matchData['event']['row'][0]) && !empty($matchData['event']['row']))
			{
				$liveData['event'][0] = $matchData['event']['row'];
			}
			else
			{
				$liveData['event'] = $matchData['event']['row']?$matchData['event']['row']:array();
			}

			// 进球事件排序
			if(!empty($liveData['event']))
			{
				$sortArry = array();
				foreach ($liveData['event'] as $key => $items) 
				{
					if($items['lb_img'] == '7')
					{
						$playerArr = explode("|", $items['lb']);
						$playerArr[0] = $playerArr[0] ? $playerArr[0] : '无数据';
						$playerArr[1] = $playerArr[1] ? $playerArr[1] : '无数据';
						$liveData['event'][$key]['lb'] = implode('|', $playerArr);
					}
					$sortArry[] = str_replace(array("'", "+"), array("", ".1"), $items['tm']);
				}
				array_multisort($sortArry, SORT_ASC, $liveData['event']);
			}

			$liveData['total']['smcu'] = $matchData['total']['smcu'];
			$liveData['total']['szqmcs'] = $matchData['total']['szqmcs'];
			$liveData['total']['fgcs'] = $matchData['total']['fgcs'];
			$liveData['total']['jqcs'] = $matchData['total']['jqcs'];
			$liveData['total']['ywcs'] = $matchData['total']['ywcs'];
			$liveData['total']['hps'] = $matchData['total']['hps'];
			$liveData['total']['kqsj'] = $matchData['total']['kqsj'];
			$liveData['total']['jq'] = $matchData['total']['jq'];
			$liveData['total']['rcard'] = $matchData['total']['rcard']?$matchData['total']['rcard']:',';
		
			$result = array(
				'status' => '1',
				'msg' => 'succ',
				'data' => $liveData
			);
		}
		else
		{
			$result = array(
				'status' => '1',
				'msg' => 'succ',
				'data' => array()
			);
		}
		
		echo json_encode($result);die;

	}

	// http://www.166cai.com/app/api/v1/match/getMatchAnaly?mid=855285
	/*
 	 * 赛事 - 分析
 	 * @date:2016-04-18
 	 */
	public function getMatchAnaly()
	{
		$mid = $this->input->get("mid", true);

		if(empty($mid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		// 获取比赛详情
		$matchInfo = $this->Match->getMatchDetail($mid);

		if(empty($matchInfo))
		{
			$result = array(
				'status' => '1',
				'msg' => 'succ',
				'data' => array()
			);
			echo json_encode($result);die;
		}

		// 比赛信息
		$matchDetail = array(
			'mid' => $matchInfo['mid'],
			'hteam' => $matchInfo['home'],
			'ateam' => $matchInfo['away'],
			'htid' => $matchInfo['htid'],
			'atid' => $matchInfo['atid'],
			'lid' => $matchInfo['lid'],
			'ln' => $matchInfo['ln'],
			'state' => $matchInfo['state']
		);

		// 历史交锋
		$historyData = $this->Match->getHistoryMatch($matchInfo);
		$historyData = json_decode($historyData, true);

		// 杯赛联赛积分
		$rankData = $this->manageScoreRank($matchInfo);

		// 近期战绩
		$recentData = $this->manageLastMatch($matchInfo);

		// 未来比赛
		$futureData = $this->manageFutureMatch($matchInfo);

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => array(
				'matchInfo' => $matchDetail,
				'historyMatch' => $historyData,
				'rankScore' => $rankData,
				'recentMatch' => $recentData,
				'futureMatch' => $futureData
			)
		);
		echo json_encode($result);die;
	}

	/*
 	 * 赛事 - 分析 - 积分
 	 * @date:2016-04-18
 	 */
	public function manageScoreRank($matchInfo)
	{
		$rankData = array(
			'statistic' => '',
			'detail' => array()
		);

		if(!empty($matchInfo['lid']) && !empty($matchInfo['sid']))
		{
			$data = $this->Match->getScoreRank($matchInfo['lid'], $matchInfo['sid']);

			if(!empty($data['row']) && !empty($data['c']['type']))
			{
				$rankData = array(
					'statistic' => array(
						'lid' => $data['c']['lid'],
						'sid' => $data['c']['sid'],
						'ln' => $matchInfo['ln'],
						'type' => $data['c']['type']
					),
					'detail' => array()
				);

				// 联赛展示当前两场 杯赛展示小组赛
				if($data['c']['type'] == 'league')
				{
					foreach ($data['row'] as $key => $items) 
					{
						if(in_array($items['tid'], array($matchInfo['htid'], $matchInfo['atid'])))
						{
							$rank = array(
								'tid' => $items['tid'],
								'name' => $items['name'],
								'rank' => (string)($key + 1),
								'num' => $items['enum'],
								'w' => $items['w']?$items['w']:'0',
								'd' => $items['d']?$items['d']:'0',
								'l' => $items['l']?$items['l']:'0',
								'goal' => $items['goal'],
								'loss' => $items['loss'],
								'diff' => (string)($items['goal'] - $items['loss']),
								'group' => $items['group'] ? $items['group'] : '',
								'score' => (string)($items['w'] * 3 + $items['d'] * 1 + $items['l'] * 0)
							);
							array_push($rankData['detail'], $rank);
						}
					}
				}

				if($data['c']['type'] == 'cup')
				{
					$rankArray = array();
					$rankGroup = array();
					foreach ($data['row'] as $key => $items) 
					{
						$rank = array(
							'tid' => $items['tid'],
							'name' => $items['name'],
							'rank' => (string)($key + 1),
							'num' => $items['enum'],
							'w' => $items['w']?$items['w']:'0',
							'd' => $items['d']?$items['d']:'0',
							'l' => $items['l']?$items['l']:'0',
							'goal' => $items['goal'],
							'loss' => $items['loss'],
							'diff' => (string)($items['goal'] - $items['loss']),
							'group' => $items['group'] ? $items['group'] : '',
							'score' => (string)($items['w'] * 3 + $items['d'] * 1 + $items['l'] * 0)
						);
						$rankArray[$items['group']][] = $rank;
						$rankGroup[$items['tid']] = $items;
					}

					if($rankGroup[$matchInfo['htid']]['group'] == $rankGroup[$matchInfo['atid']]['group'])
					{
						$rankData['detail'] = $rankArray[$rankGroup[$matchInfo['htid']]['group']];
						if(!empty($rankData['detail']))
						{
							foreach ($rankData['detail'] as $key => $items) 
							{
								$items['rank'] = (string)($key + 1);
								$rankData['detail'][$key] = $items;
							}
						}
						else
						{
							$rankData['detail'] = array();
						}
						$rankData['statistic']['ln'] = $matchInfo['ln'] . $rankGroup[$matchInfo['htid']]['group'] . '组';
					}				
				}
			}
		}

		return $rankData;
	}

	/*
 	 * 赛事 - 分析 - 近期战绩 
 	 * @date:2016-04-18
 	 */
	public function manageLastMatch($matchInfo)
	{
		$hMatch = $this->Match->getTeamState($matchInfo['htid']);
		$hMatch = $this->handleLastMatch($hMatch, $matchInfo['mid']);

		$aMatch = $this->Match->getTeamState($matchInfo['atid']);
		$aMatch = $this->handleLastMatch($aMatch, $matchInfo['mid']);

		$match = array(
			'hMatch' => $hMatch?$hMatch:array(),
			'aMatch' => $aMatch?$aMatch:array(),
		);

		return $match;
	}

	/*
 	 * 赛事 - 分析 - 近期战绩 筛选最近50场
 	 * @date:2016-04-18
 	 */
	public function handleLastMatch($match, $mid)
	{
		$matchData = array();

		// 获取所选场次之前的近期状态
        if(!empty($match[$mid]))
        {
            $matchArray = array_slice($match, $match[$mid]['index'] + 1, 50);
        }
        else
        {
            $matchArray = array_slice($match, 0, 50);
        }

        if(!empty($matchArray))
        {
        	foreach ($matchArray as $key => $items) 
            {
            	$data['mid'] = $items['mid'];
            	$data['sid'] = $items['sid'];
            	$data['rid'] = $items['rid'];
            	$data['ln'] = $items['ln'];
            	$data['hteam'] = $items['hteam'];
            	$data['ateam'] = $items['ateam'];
            	$data['mtime'] = date('Y-m-d H:i:s', $items['mtime']);
            	$data['hscore'] = $items['hscore'];
            	$data['ascore'] = $items['ascore'];
            	$data['binfo'] = $items['binfo'];
            	$data['htid'] = $items['htid'];
            	$data['atid'] = $items['atid'];
            	array_push($matchData, $data);
            }
        }
            
		return $matchData;
	}

	/*
 	 * 赛事 - 分析 - 未来比赛
 	 * @date:2016-04-18
 	 */
	public function manageFutureMatch($matchInfo)
	{
		$hMatch = $this->Match->getFutureMatch($matchInfo['htid'], 3);
		$hMatch = $this->handleFutureMatch($hMatch);

		$aMatch = $this->Match->getFutureMatch($matchInfo['atid'], 3);
		$aMatch = $this->handleFutureMatch($aMatch);

		$match = array(
			'hMatch' => $hMatch?$hMatch:array(),
			'aMatch' => $aMatch?$aMatch:array(),
		);

		return $match;
	}

	/*
 	 * 赛事 - 分析 - 未来比赛 格式处理
 	 * @date:2016-04-18
 	 */
	public function handleFutureMatch($match)
	{
		$matchData = array();

		if(!empty($match))
		{
			$ctime = date('Y-m-d H:i:s');
			foreach ($match as $key => $items) 
			{
				$matchData[$key]['mid'] = $items['mid'];
				$matchData[$key]['hteam'] = $items['home'];
				$matchData[$key]['ateam'] = $items['away'];
				$matchData[$key]['htid'] = $items['htid'];
				$matchData[$key]['atid'] = $items['atid'];
				$matchData[$key]['mtime'] = date('Y-m-d H:i:s', $items['mtime']);
				$matchData[$key]['lid'] = $items['lid'];
				$matchData[$key]['ln'] = $items['ln'];
				$matchData[$key]['timeMsg'] = $this->getFutureTime($matchData[$key]['mtime'], $ctime);
			}
		}

		return $matchData;
	}

	/*
 	 * 赛事 - 分析 - 未来比赛 相隔处理
 	 * @date:2016-04-18
 	 */
	public function getFutureTime($mtime, $ctime)
	{
		// 开赛时间
        $time = strtotime($mtime);
        // 当前时间
        $now = strtotime($ctime);

        $timeDif = $time - $now;

        $msg = '';
        if($timeDif > 0)
        {
            $remain = $timeDif%86400;
            $difH = intval($remain/3600);
            $difM = intval(($remain%3600)/60);

            $day = $timeDif/86400;
            if(intval($day) > 0)
            {
                $msg .= intval($day) . '天后';
            }
            else
            {
                if($difH > 0)
                {
                    $msg .= $difH . "小时后";
                }
            }
        }
        else
        {
            $msg .= '1小时内';
        }
        return $msg;
	}

	/*
 	 * 赛事 - 欧赔亚赔 - 列表
 	 * @date:2016-04-18
 	 */
	public function getOddList()
	{
		$mid = $this->input->get("mid", true);
		$type = $this->input->get("type", true);

		if(empty($mid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		if(!in_array($type, array('o', 'y')))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$oddArray = $this->Match->getOddList($type, $mid);

		$oddData = array();
		if(!empty($oddArray))
		{
			if($type == 'o')
			{
				$k = 0;
				// 欧赔删选主流公司
				$cidInfo = $this->cidInfo;
				foreach ($oddArray as $key => $items) 
				{
					if(!empty($cidInfo[$items['cid']]))
					{
						$oddData[$k]['cid'] = $items['cid'];
						$oddData[$k]['cname'] = $items['cname'];
						$lite = $this->getLite($items['lite']);
						$oddData[$k]['olite'] = $lite[0]?$lite[0]:'';
						$oddData[$k]['clite'] = $lite[1]?$lite[1]:'';
						$k++;
					}				
				}
			}
			else
			{
				foreach ($oddArray as $key => $items) 
				{
					$oddData[$key]['cid'] = $items['cid'];
					$oddData[$key]['cname'] = $items['cname'];
					$lite = $this->getLite($items['lite']);
					$oddData[$key]['olite'] = $lite[0]?$lite[0]:'';
					$oddData[$key]['clite'] = $lite[1]?$lite[1]:'';
				}
			}
		}
		
		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $oddData
		);
		echo json_encode($result);die;
	}

	/*
 	 * 赛事 - 赔率处理
 	 * @date:2016-04-18
 	 */
	public function getLite($lite)
	{
		$liteData = array();
		if(!empty($lite))
		{
			$liteData = explode('|', $lite);
		}
		return $liteData;
	}

	/*
 	 * 赛事 - 欧赔亚赔 - 详情
 	 * @date:2016-04-18
 	 */
	public function getOddDetail()
	{
		$mid = $this->input->get("mid", true);
		$type = $this->input->get("type", true);
		$cid = $this->input->get("cid", true);

		if(empty($mid) || empty($cid))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		if(!in_array($type, array('o', 'y')))
		{
			$result = array(
				'status' => '0',
				'msg' => '缺少赛事标识',
				'data' => ''
			);
			echo json_encode($result);die;
		}

		$oddArray = $this->Match->getOddDetail($type, $cid, $mid);

		$oddData = array();
		if(!empty($oddArray))
		{
			$count = count($oddArray);

			foreach ($oddArray as $key => $items) 
			{
				$oddData[$key]['oh'] = ($type == 'o')?$items['oh']:$items['ab'];
				$oddData[$key]['od'] = ($type == 'o')?$items['od']:$items['bet'];
				$oddData[$key]['oa'] = ($type == 'o')?$items['oa']:$items['be'];
				$oddData[$key]['type'] = ($type == 'o')?$items['type']:'';
				$oddData[$key]['time'] = date('Y-m-d H:i:s', $items['time']);

				if($count == $key + 1)
				{
					$oddData[$key]['timeMsg'] = '初盘';
				}
				else
				{
					$oddData[$key]['timeMsg'] = $this->calCurTime($oddData[$key]['time']);
				}
			}
		}

		$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => $oddData
		);
		echo json_encode($result);die;
	}

	/*
 	 * 赛事 - 欧赔亚赔 - 距离时间
 	 * @date:2016-04-18
 	 */
	public function calCurTime($time)
	{
		if(empty($time))
		{
			return '赛前1分钟';
		}

		$now = time();
		$time = strtotime($time);

		$timeDif = $now - $time;     

        $msg = '赛前';
        if($timeDif > 0)
        {
            $remain = $timeDif%86400;
            $difH = intval($remain/3600);
            $difM = intval(($remain%3600)/60);

            $day = $timeDif/86400;
            if(intval($day) > 0)
            {
                $msg .= intval($day) . '天';
                if($difH > 0)
                {
                    $msg .= $difH . "小时";
                }
            }
            else
            {
                if($difH > 0)
                {
                    $msg .= $difH . "小时";
                }

                if($difM > 0)
                {
                    $msg .= $difM . "分钟";
                }
            }
        }
        else
        {
            $msg .= '1分钟';
        }
        return $msg;
	}
}
