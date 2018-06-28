<?php

if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
/*
 * APP 基础数据接口
 * @date:2016-01-18
 */
class Data extends MY_Controller 
{

	public function __construct() 
	{
		parent::__construct();
		$this->load->library('comm');
		$this->load->model('api_data','Data');
		$this->load->model('cache_model','Cache');
		$this->load->model('user_model');
		$this->headerInfo = $this->getRequestHeaders();
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

	/*
 	 * 彩种信息
 	 * @date:2015-04-17
 	 */
	public function lotteryInfo()
	{
		// 按彩种类型处理
		$data = array();

		$linfo = $this->Data->getLotteryInfo('android');

		$lotterys = array();
		if(!empty($linfo))
		{

			// 获取双色球、大乐透奖池信息
			$awardInfo = $this->getAwardInfo();

			// 奖池信息
			foreach ($linfo as $key => $items)
			{
				if(in_array($items['lid'], array('42', '43')))
				{
					// V3.3以下版本
					if($this->headerInfo['appVersionCode'] < '18')
					{
						$items['memo'] = $items['memo'] ? $items['memo'] : $this->countJc($items['lid']);
					}
					else
					{
						$items['awardPool'] = $this->countJc($items['lid']);
					}		
				}
				else
				{
					$items['awardPool'] = $this->comm->NumToCNMoney($awardInfo[$items['lid']]['awardPool']);
				}

				// 今日开奖
				$items['kaijiang'] = $this->isAwarding($items['lid']);

				// 按渠道彩种停售
        		$channelArr = $this->Cache->getLimitChannel();
				if(in_array($this->recordChannel($this->headerInfo['channel']), $channelArr))
				{
					$items['isSale'] = '0';
				}
				else
				{
					$items['isSale'] = '1';
				}

				// 加奖标识
				$items['attachFlag'] = (($items['attachFlag'] & 1) == 1) ? '1' : '0';
				// 副标题标红
				$items['memoFlag'] = (($items['attachFlag'] & 2) == 2) ? '1' : '0';

				if(!empty($items['channels']) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])))
				{
					$items['imgUrl'] = (strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'];
					unset($items['channels']);
					array_push($lotterys, $items);
				}
			}

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => $lotterys
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => 'Error',
				'data' => array()
			);
		}

		echo json_encode($result);
	}

	/*
 	 * 双色球 大乐透 今日开奖	
 	 * @date:2015-04-17
 	 */
	public function isAwarding($lid)
	{
		$result = '0';
		if(in_array($lid, array('51', '23529')))
		{
			$info = $this->Cache->getCurrentByLid($lid);

			if(!empty($info[0]['awardTime']) && ( date('Y-m-d') == date('Y-m-d',substr($info[0]['awardTime'], 0, 10)) ) && ( date('Y-m-d H:i:s') <= date('Y-m-d H:i:s',substr($info[0]['awardTime'], 0, 10)) ))
			{
				$result = '1';
			}
		}
		return $result;
	}

	/*
 	 * 广告轮播	
 	 * @parmas:2015-04-17
 	 * @date:2015-04-17
 	 */
	public function adInfo()
	{
		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => array()
		);

		$info = $this->Data->getAddInfo('android');

		$adInfo = array();
		if(!empty($info))
		{
			foreach ($info as $key => $items) 
			{
				if(!empty($items['channels']) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])))
				{
					$items['imgUrl'] = (strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'];
					unset($items['channels']);
					array_push($adInfo, $items);
				}
			}

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => $adInfo
			);
		}
		echo json_encode($result);
	}

	/*
 	 * 数字彩开奖信息接口
 	 * @date:2015-04-20
 	 */
	public function awardsInfo()
	{
		$lotteryId = $this->input->get("lid", true);
		$page = intval($this->input->get("page", true));
		$page = $page ? $page : 1;

		$number = intval($this->input->get("number", true));
		$number = $number ? $number : 10;

		// 遗漏信息
		$missNum = $this->input->get("missNum", true);
		$missNum = $missNum ? 1 : 0;

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => ''
		);

		// 数据中心
		$this->load->model('award_model', 'Award');
		$awardsInfo = $this->Award->getAwardListByDcenter($lotteryId, $missNum, $page, $number);

		if (!empty($awardsInfo)) 
		{	
			foreach ($awardsInfo as $key => $items) 
			{
				$awardsInfo[$key]['awardNumber'] = str_replace(':', '|', $items['awardNumber']);
				$awardsInfo[$key]['seEndtime'] = date('Y-m-d H:i:s',substr($items['seEndtime'], 0, 10));
				$awardsInfo[$key]['seFsendtime'] = date('Y-m-d H:i:s',substr($items['seFsendtime'], 0, 10));
                                $awardsInfo[$key]['awardTime'] = date('Y-m-d H:i:s',substr($items['awardTime'], 0, 10));
				$awardsInfo[$key]['seDsendtime'] = date('Y-m-d H:i:s',substr($items['seDsendtime'], 0, 10));
				$awardsInfo[$key]['seAddtime'] = date('Y-m-d H:i:s',substr($items['seAddtime'], 0, 10));

				// 近【十期】遗漏数据
				if(!empty($missNum))
				{	
					$awardsInfo[$key]['missNum'] = $this->Award->getMissNumber($items['seLotid'], $items['seExpect'], $awardsInfo, $key);
				}

				// 福彩3D 排列三 新增形态
				if(in_array($lotteryId, array('52', '33')))
				{
					$awardsInfo[$key]['pattern'] = $this->getLotteryMark($lotteryId, $awardsInfo[$key]['awardNumber']);
				}
			}

			// 获取正在开奖期次信息
			$awardInfo = $this->getAwarding($lotteryId, $awardsInfo);

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => array(
					'awardData' => $awardsInfo,
					'awardInfo' => $awardInfo,
					'currentMiss' => $this->getCurrentMiss($lotteryId),
				)
			);
        }
        echo json_encode($result);
	}

	/*
 	 * 查询各彩种最新一期开奖信息
 	 * @date:2015-12-24
 	 */
	public function getAwardInfo()
	{
		$this->load->model('award_model', 'Award');
		$awardData = $this->Award->getLastByDcenter();

		$awardInfo = array();
		if(!empty($awardData))
		{
			foreach ($awardData as $items) 
			{
				$awardInfo[$items['seLotid']] = $items;
			}
		}
		return $awardInfo;
	}
	

	/*
 	 * 查询正在开奖的期次信息
 	 * @date:2015-12-24
 	 */
	public function getAwarding($lotteryId, $awardsInfo)
	{
		$awardInfo = array(
			'issue' => '',
			'status' => 0
		);

		$issueInfo = $this->Cache->getIssueInfo($lotteryId);
		if(!empty($issueInfo['aIssue']['seExpect']))
		{
			$this->load->library('libcomm');
			$cIssue = $this->libcomm->getIssueFormat($lotteryId, $issueInfo['aIssue']['seExpect']);
			if($cIssue != $awardsInfo[0]['seExpect'])
			{
				$awardInfo = array(
					'issue'			=>	$cIssue,
					'status' 		=> 	1,
					'awardTime'		=>	date('Y-m-d H:i:s', substr($issueInfo['aIssue']['awardTime'], 0, 10)),
					'currentTime'	=>	date('Y-m-d H:i:s'),
				);
			}
		}
		return $awardInfo;
	}

	/*
 	 * 查询指定彩种当前【在售】期信息
 	 * @date:2015-04-20
 	 */
	public function getCurrentByLid()
	{
		$lid = $this->input->get("lid", true);

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => array()
		);

		// 数据中心
		$info = $this->Cache->getCurrentByLid($lid);

		// 获取最新期次开奖信息缓存
		$awardInfo = $this->getAwardInfo();
                
                //合买提前截止时间
                $this->load->model('lottery_model');
                $lotteryConfig = $this->lottery_model->getLotteryConfig($lid, 'united_ahead,ahead');

		$lotteryInfo = array();
		if(!empty($info[0]))
		{
			//处理时间戳
			foreach ($info as $key => $items) 
			{
				$lotteryInfo[$key]['seExpect'] = $items['seExpect'];
				$lotteryInfo[$key]['seLotid'] = $items['seLotid'];
				$lotteryInfo[$key]['awardNumber'] = $items['awardNumber'] ? $items['awardNumber'] : '';
				$lotteryInfo[$key]['seFsendtime'] = date('Y-m-d H:i:s',substr($items['seFsendtime'], 0, 10));
                                $lotteryInfo[$key]['HmseFsendtime'] = date('Y-m-d H:i:s', (strtotime($lotteryInfo[$key]['seFsendtime']) - $lotteryConfig['united_ahead'] * 60));
                                $lotteryInfo[$key]['GfseFsendtime'] = date('Y-m-d H:i:s', (strtotime($lotteryInfo[$key]['seFsendtime']) + $lotteryConfig['ahead'] * 60));
				$lotteryInfo[$key]['awardPool'] = $awardInfo[$lid]['awardPool'] ? $awardInfo[$lid]['awardPool'] : '';
				$lotteryInfo[$key]['awardTime'] = date('Y-m-d H:i:s',substr($items['awardTime'], 0, 10));
				$lotteryInfo[$key]['currentTime'] = date('Y-m-d H:i:s');
				$lotteryInfo[$key]['missData'] = $this->getMissNumber($lid);
				// 新增活动标识
				$lotteryInfo[$key]['mark'] = $this->getActivityMark($lid);
			}

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => $lotteryInfo
			);
		}
		echo json_encode($result);
	}

	/*
 	 * 查询老足彩当前【在售】期的信息
 	 * @date:2015-04-20
 	 */
	public function getTczqInfo()
	{
		$lid = $this->input->get("lid", true);

		if(empty($lid))
		{
			$result = array(
				'status' => '0',
				'msg' => 'Error',
				'data' => array()
			);
			echo json_encode($result);
		}

		if(in_array($lid, array('11','19')))
		{
			$this->load->driver('cache', array('adapter' => 'redis'));
			$REDIS = $this->config->item('REDIS');
	    	$cache = $this->cache->get($REDIS['SFC_ISSUE']);
	    	$cache = json_decode($cache, true);
	    	if($cache['cIssue']['seDsendtime']/1000 <= time() && $cache['nIssue'])
	    	{
	    		$currIssue = $cache['nIssue'];
	    	}
	    	else
	    	{
	    		$currIssue = $cache['cIssue'];
	    	}

	    	// 截止时间
	    	$currentInfo = $this->Cache->getCurrentByLid($lid);
	    	$seFsendtime = $currentInfo[0]['seFsendtime'];

	    	// 开售时间
	    	$sale_time = $currentInfo[0]['sale_time'];

	    	$info = $this->Cache->getTczqInfo($lid);

	    	// 历史交锋
	    	$matchData = $this->Cache->getSfcMatchHistory();

	    	// 投注比例
	    	$betInfo = $this->Cache->getJcBetInfo($lid);

	    	$matches = array();

	    	if(!empty($info))
	    	{
	    		foreach ($info as $key => $items)
	    		{
	    			$xid = $currIssue['seExpect'] . str_pad($key + 1, 2, "0", STR_PAD_LEFT);
	    			$matches[$key]['lid'] = $lid;
	    			$matches[$key]['issue'] = $currIssue['seExpect'];
	    			$matches[$key]['gameName'] = $items['gameName'];
	    			$matches[$key]['teamName1'] = $items['teamName1'];
	    			$matches[$key]['teamName2'] = $items['teamName2'];
	    			$matches[$key]['gameTime'] = date('Y-m-d H:i:s',substr($items['gameTime'], 0, 10));
	    			// 获取智胜数据
	    			$oddData = $this->getZhishengData($matches[$key]['issue']);
	    			$matches[$key]['odds1'] = $oddData[$key+1]['oh'];
	    			$matches[$key]['odds2'] = $oddData[$key+1]['od'];
	    			$matches[$key]['odds3'] = $oddData[$key+1]['oa'];
	    			// 竞彩数据汇总
	    			$matches[$key]['jcMid'] = $matchData[$xid]['mid']?$matchData[$xid]['mid']:'';
	    			$matches[$key]['his'] = $matchData[$xid]['his']?$matchData[$xid]['his']:'';
	    			$matches[$key]['hstate'] = $matchData[$xid]['hstate']?$matchData[$xid]['hstate']:'';
	    			$matches[$key]['astate'] = $matchData[$xid]['astate']?$matchData[$xid]['astate']:'';
	    			$matches[$key]['hrank'] = $matchData[$xid]['hrank']?$matchData[$xid]['hrank']:'';
	    			$matches[$key]['arank'] = $matchData[$xid]['arank']?$matchData[$xid]['arank']:'';
	    			// 投注比例
	    			$matches[$key]['spf_bet'] = $this->getBetFormat($betInfo, $xid, $lid, '');
	    		}
	    	}

	    	// 针对竞彩篮球出票时间调整v2.7
	    	$headerInfo = $this->getRequestHeaders();

	    	if($headerInfo['appVersionCode'] >= '11')
	    	{
	    		//合买提前截止时间
	    		$this->load->model('lottery_model');
	    		$lotteryConfig = $this->lottery_model->getLotteryConfig($lid, 'united_ahead,ahead');
	    		$result = array(
	    			'status'	=>	'1',
	    			'msg' 		=> 	'Success',
	    			'data' 		=> 	array(
	    				'mark'			=>	$this->comm->getTimeTran(substr($sale_time, 0, 10)),
	    				'currentTime'	=>	date('Y-m-d H:i:s'),
	    				'seFsendtime'	=>	$seFsendtime ? date('Y-m-d H:i:s',substr($seFsendtime, 0, 10)) : '',
	    				'HmseFsendtime'	=> 	$seFsendtime ? date('Y-m-d H:i:s', (substr($seFsendtime, 0, 10)) - $lotteryConfig['united_ahead'] * 60) : '',
	    				'GfseFsendtime' => 	$seFsendtime ? date('Y-m-d H:i:s', (substr($seFsendtime, 0, 10)) + $lotteryConfig['ahead'] * 60) : '',
	    				'match' 		=> 	$matches
	    			)
	    		);
	    		die(json_encode($result));
	    	}

	    	$result = array(
	    		'status' => '1',
	    		'msg' => 'Success',
	    		'data' => $matches
	    	);
	    }
	    echo json_encode($result);
	}

	/*
 	 * 查询智胜接口数据
 	 * @date:2016-04-05
 	 */
	public function getZhishengData($issue, $cid = 0)
	{	
		$this->load->model('api_zhisheng_model', 'dataSource');
		$content = $this->dataSource->readEuropeOdds('11', $issue, $cid);
		$contentAry = json_decode($content, TRUE);
		for ($mid = 1; $mid <= 14; $mid ++)
		{
			$matchToOdds[$mid] = (isset($contentAry[$mid]) && isset($contentAry[$mid]['odds']) && is_numeric($contentAry[$mid]['odds']['oh']))
				? $contentAry[$mid]['odds']
				: array('od' => '0.00', 'oa' => '0.00', 'oh' => '0.00',);
		}

		return $matchToOdds;
	}

	/*
 	 * 查询竞彩彩种在售场次信息 jczq /jclq/bjdc
 	 * @date:2015-04-20
 	 */
	public function getJjcInfo()
	{
		$lid = $this->input->get("lid", true);

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => array()
		);

		// 数据中心
		$info = $this->Cache->getJjcInfo($lid);

		$matches = array();
		$weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');

		//合买提前截止时间
		$this->load->model('lottery_model');
		$lotteryConfig = $this->lottery_model->getLotteryConfig($lid, 'united_ahead,ahead');

		// 投注比例
		$betInfo = $this->Cache->getJcBetInfo($lid);
		// 热门赛事
		$hotArr = array();
		// 比赛期次
		$keyArr = array();
		$index = 0;

		if(!empty($info))
		{
			switch ($lid)
			{
				// 竞彩足球
				case '42':
					// 获取竞彩对阵数据 比赛编号 历史交锋
					$matchData = $this->Cache->getJcMatchHistory();
					foreach ($info as $key => $items)
					{
						if( empty($items['spfGd']) && empty($items['rqspfGd']) && empty($items['bqcGd']) && empty($items['jqsGd']) && empty($items['bfGd']) && empty($items['spfFu']) && empty($items['rqspfFu']) && empty($items['bqcFu']) && empty($items['jqsFu']) && empty($items['bfFu']) )
						{
							// 过滤该场比赛
						}
						else
						{
							$match = array();
							$match[$key]['issue'] = $items['issue'];
							$match[$key]['spfGd'] = $items['spfGd'];
							$match[$key]['rqspfGd'] = $items['rqspfGd'];
							$match[$key]['bqcGd'] = $items['bqcGd'];
							$match[$key]['jqsGd'] = $items['jqsGd'];
							$match[$key]['bfGd'] = $items['bfGd'];
							$match[$key]['spfFu'] = $items['spfFu'];
							$match[$key]['rqspfFu'] = $items['rqspfFu'];
							$match[$key]['bqcFu'] = $items['bqcFu'];
							$match[$key]['jqsFu'] = $items['jqsFu'];
							$match[$key]['bfFu'] = $items['bfFu'];
							$match[$key]['mid'] = $items['mid'];
							$match[$key]['id'] = $items['mid'];
							$match[$key]['awary'] = $items['awarySname']?$items['awarySname']:$items['awary'];
							$match[$key]['dt'] = date('Y-m-d H:i:s',substr($items['dt'], 0, 10));
							$match[$key]['jzdt'] = date('Y-m-d H:i:s',substr($items['jzdt'], 0, 10));
							$match[$key]['hmjzdt'] = date('Y-m-d H:i:s', (substr($items['jzdt'], 0, 10) - $lotteryConfig['united_ahead'] * 60));
							$match[$key]['gfjzdt'] = date('Y-m-d H:i:s', (substr($items['jzdt'], 0, 10) + $lotteryConfig['ahead'] * 60));
							$match[$key]['let'] = $items['let']?str_replace('+', '', $items['let']):0;
							// 赔率数组格式化
							$match[$key]['spfSp'] = array(
								'spfSp3' => $items['spfSp3']?$items['spfSp3']:0,
								'spfSp1' => $items['spfSp1']?$items['spfSp1']:0,
								'spfSp0' => $items['spfSp0']?$items['spfSp0']:0
							);
							$match[$key]['spfSp'] = implode(',', $match[$key]['spfSp']);
							$match[$key]['rqspfSp'] = array(
								'rqspfSp3' => $items['rqspfSp3']?$items['rqspfSp3']:0,
								'rqspfSp1' => $items['rqspfSp1']?$items['rqspfSp1']:0,
								'rqspfSp0' => $items['rqspfSp0']?$items['rqspfSp0']:0
							);
							$match[$key]['rqspfSp'] = implode(',', $match[$key]['rqspfSp']);
							$match[$key]['bfSp'] = array(
								'bfSp10' => $items['bfSp10']?$items['bfSp10']:0,
								'bfSp20' => $items['bfSp20']?$items['bfSp20']:0,
								'bfSp21' => $items['bfSp21']?$items['bfSp21']:0,
								'bfSp30' => $items['bfSp30']?$items['bfSp30']:0,
								'bfSp31' => $items['bfSp31']?$items['bfSp31']:0,
								'bfSp32' => $items['bfSp32']?$items['bfSp32']:0,
								'bfSp40' => $items['bfSp40']?$items['bfSp40']:0,
								'bfSp41' => $items['bfSp41']?$items['bfSp41']:0,
								'bfSp42' => $items['bfSp42']?$items['bfSp42']:0,
								'bfSp50' => $items['bfSp50']?$items['bfSp50']:0,
								'bfSp51' => $items['bfSp51']?$items['bfSp51']:0,
								'bfSp52' => $items['bfSp52']?$items['bfSp52']:0,
								'bfSp93' => $items['bfSp93']?$items['bfSp93']:0,	
								'bfSp00' => $items['bfSp00']?$items['bfSp00']:0,
								'bfSp11' => $items['bfSp11']?$items['bfSp11']:0,
								'bfSp22' => $items['bfSp22']?$items['bfSp22']:0,
								'bfSp33' => $items['bfSp33']?$items['bfSp33']:0,
								'bfSp91' => $items['bfSp91']?$items['bfSp91']:0,
								'bfSp01' => $items['bfSp01']?$items['bfSp01']:0,
								'bfSp02' => $items['bfSp02']?$items['bfSp02']:0,
								'bfSp12' => $items['bfSp12']?$items['bfSp12']:0,
								'bfSp03' => $items['bfSp03']?$items['bfSp03']:0,
								'bfSp13' => $items['bfSp13']?$items['bfSp13']:0,
								'bfSp23' => $items['bfSp23']?$items['bfSp23']:0,
								'bfSp04' => $items['bfSp04']?$items['bfSp04']:0,
								'bfSp14' => $items['bfSp14']?$items['bfSp14']:0,
								'bfSp24' => $items['bfSp24']?$items['bfSp24']:0,
								'bfSp05' => $items['bfSp05']?$items['bfSp05']:0,	
								'bfSp15' => $items['bfSp15']?$items['bfSp15']:0,		
								'bfSp25' => $items['bfSp25']?$items['bfSp25']:0,
								'bfSp90' => $items['bfSp90']?$items['bfSp90']:0
							);
							$match[$key]['bfSp'] = implode(',', $match[$key]['bfSp']);
							$match[$key]['jqsSp'] = array(
								'jqsSp0' => $items['jqsSp0']?$items['jqsSp0']:0,
								'jqsSp1' => $items['jqsSp1']?$items['jqsSp1']:0,
								'jqsSp2' => $items['jqsSp2']?$items['jqsSp2']:0,
								'jqsSp3' => $items['jqsSp3']?$items['jqsSp3']:0,
								'jqsSp4' => $items['jqsSp4']?$items['jqsSp4']:0,
								'jqsSp5' => $items['jqsSp5']?$items['jqsSp5']:0,
								'jqsSp6' => $items['jqsSp6']?$items['jqsSp6']:0,
								'jqsSp7' => $items['jqsSp7']?$items['jqsSp7']:0
							);
							$match[$key]['jqsSp'] = implode(',', $match[$key]['jqsSp']);
							$match[$key]['bqcSp'] = array(
								'bqcSp33' => $items['bqcSp33']?$items['bqcSp33']:0,
								'bqcSp31' => $items['bqcSp31']?$items['bqcSp31']:0,
								'bqcSp30' => $items['bqcSp30']?$items['bqcSp30']:0,
								'bqcSp13' => $items['bqcSp13']?$items['bqcSp13']:0,
								'bqcSp11' => $items['bqcSp11']?$items['bqcSp11']:0,
								'bqcSp10' => $items['bqcSp10']?$items['bqcSp10']:0,
								'bqcSp03' => $items['bqcSp03']?$items['bqcSp03']:0,
								'bqcSp01' => $items['bqcSp01']?$items['bqcSp01']:0,
								'bqcSp00' => $items['bqcSp00']?$items['bqcSp00']:0
							);
							$match[$key]['bqcSp'] = implode(',', $match[$key]['bqcSp']);
							$match[$key]['home'] = $items['homeSname']?$items['homeSname']:$items['home'];
							$match[$key]['name'] = $items['nameSname'];
							$match[$key]['nameType'] = $this->comm->getNameType($items['nameSname']);
							$match[$key]['week'] = $weekDays[date('w', strtotime($items['issue']))];
							$match[$key]['hot'] = $items['hot']?$items['hot']:'0';
							// 竞彩数据汇总
							$match[$key]['jcMid'] = $matchData[$items['mid']]['mid']?$matchData[$items['mid']]['mid']:'';
							$match[$key]['jcLid'] = $matchData[$items['mid']]['lid']?$matchData[$items['mid']]['lid']:'';
							$match[$key]['his'] = $matchData[$items['mid']]['his']?$matchData[$items['mid']]['his']:'';
							$match[$key]['hstate'] = $matchData[$items['mid']]['hstate']?$matchData[$items['mid']]['hstate']:'';
							$match[$key]['astate'] = $matchData[$items['mid']]['astate']?$matchData[$items['mid']]['astate']:'';
							$match[$key]['hrank'] = $matchData[$items['mid']]['hrank']?$matchData[$items['mid']]['hrank']:'';
							$match[$key]['arank'] = $matchData[$items['mid']]['arank']?$matchData[$items['mid']]['arank']:'';
							// 投注比例
							$match[$key]['spf_bet'] = $this->getBetFormat($betInfo, $items['mid'], $lid, 'SPF');
							$match[$key]['rqspf_bet'] = $this->getBetFormat($betInfo, $items['mid'], $lid, 'RQSPF');
							$hotArr[] = $match[$key]['hot'] ? (($items['hotid'] == '0') ? '10' : $items['hotid']) : '100';
							$keyArr[] = $index++;
							array_push($matches, $match[$key]);
						}
					}
					break;
				// 竞彩篮球
				case '43':
					// 获取竞彩对阵数据 比赛编号 历史交锋
					$matchData = $this->Cache->getLqMatchHistory();
					foreach ($info as $key => $items) 
					{
						if( empty($items['rfsfGd']) && empty($items['sfGd']) && empty($items['sfcGd']) && empty($items['dxfGd']) && empty($items['sfFu']) && empty($items['rfsfFu']) && empty($items['sfcFu']) && empty($items['dxfFu']) )
						{
							// 过滤该场比赛
						}
						else
						{
							$match = array();
							$match[$key]['issue'] = $items['issue'];
							$match[$key]['rfsfGd'] = $items['rfsfGd'];
							$match[$key]['sfGd'] = $items['sfGd'];
							$match[$key]['sfcGd'] = $items['sfcGd'];
							$match[$key]['dxfGd'] = $items['dxfGd'];
							$match[$key]['sfFu'] = $items['sfFu'];
							$match[$key]['rfsfFu'] = $items['rfsfFu'];
							$match[$key]['sfcFu'] = $items['sfcFu'];
							$match[$key]['dxfFu'] = $items['dxfFu'];
							$match[$key]['mid'] = $items['mid'];
							$match[$key]['id'] = $items['mid'];
							$match[$key]['awary'] = $items['awarySname']?$items['awarySname']:$items['awary'];
							$match[$key]['dt'] = date('Y-m-d H:i:s',substr($items['dt'], 0, 10));
							$match[$key]['jzdt'] = date('Y-m-d H:i:s',substr($items['jzdt'], 0, 10));
							$match[$key]['hmjzdt'] = date('Y-m-d H:i:s', (substr($items['jzdt'], 0, 10) - $lotteryConfig['united_ahead'] * 60));
							$match[$key]['gfjzdt'] = date('Y-m-d H:i:s', (substr($items['jzdt'], 0, 10) + $lotteryConfig['ahead'] * 60));
							$match[$key]['let'] = $items['let']?str_replace('+', '', $items['let']):0;
							$match[$key]['preScore'] = $items['preScore']?number_format($items['preScore'], 1, '.', ''):0;
							// 赔率数组格式化
							$match[$key]['sfSp'] = array(
								'sfHf' => $items['sfHf']?$items['sfHf']:0,
								'sfHs' => $items['sfHs']?$items['sfHs']:0				
							);
							$match[$key]['sfSp'] = implode(',', $match[$key]['sfSp']);
							$match[$key]['rfsfSp'] = array(
								'rfsfHf' => $items['rfsfHf']?$items['rfsfHf']:0,
								'rfsfHs' => $items['rfsfHs']?$items['rfsfHs']:0
							);
							$match[$key]['rfsfSp'] = implode(',', $match[$key]['rfsfSp']);
							$match[$key]['dxfSp'] = array(
								'dxfBig' => $items['dxfBig']?$items['dxfBig']:0,
								'dxfSmall' => $items['dxfSmall']?$items['dxfSmall']:0
							);
							$match[$key]['dxfSp'] = implode(',', $match[$key]['dxfSp']);
							$match[$key]['sfcSp'] = array(
								'sfcAs15' => $items['sfcAs15']?$items['sfcAs15']:0,
								'sfcAs610' => $items['sfcAs610']?$items['sfcAs610']:0,
								'sfcAs1115' => $items['sfcAs1115']?$items['sfcAs1115']:0,
								'sfcAs1620' => $items['sfcAs1620']?$items['sfcAs1620']:0,
								'sfcAs2125' => $items['sfcAs2125']?$items['sfcAs2125']:0,
								'sfcAs26' => $items['sfcAs26']?$items['sfcAs26']:0,
								'sfcHs15' => $items['sfcHs15']?$items['sfcHs15']:0,
								'sfcHs610' => $items['sfcHs610']?$items['sfcHs610']:0,
								'sfcHs1115' => $items['sfcHs1115']?$items['sfcHs1115']:0,
								'sfcHs1620' => $items['sfcHs1620']?$items['sfcHs1620']:0,
								'sfcHs2125' => $items['sfcHs2125']?$items['sfcHs2125']:0,
								'sfcHs26' => $items['sfcHs26']?$items['sfcHs26']:0
							);
							$match[$key]['sfcSp'] = implode(',', $match[$key]['sfcSp']);
							$match[$key]['home'] = $items['homeSname']?$items['homeSname']:$items['home'];
							$match[$key]['name'] = $items['nameSname'];
							$match[$key]['week'] = $weekDays[date('w', strtotime($items['issue']))];
							$match[$key]['hot'] = $items['hot']?$items['hot']:'0';
							// 竞彩数据汇总
							$match[$key]['jcMid'] = $matchData[$items['mid']]['mid']?$matchData[$items['mid']]['mid']:'';
							$match[$key]['his'] = $matchData[$items['mid']]['his']?$matchData[$items['mid']]['his']:'';
							$match[$key]['hstate'] = $matchData[$items['mid']]['hstate']?$matchData[$items['mid']]['hstate']:'';
							$match[$key]['astate'] = $matchData[$items['mid']]['astate']?$matchData[$items['mid']]['astate']:'';
							$match[$key]['hrank'] = $matchData[$items['mid']]['hrank']?$matchData[$items['mid']]['hrank']:'';
							$match[$key]['arank'] = $matchData[$items['mid']]['arank']?$matchData[$items['mid']]['arank']:'';
							// 投注比例
							$match[$key]['sf_bet'] = $this->getBetFormat($betInfo, $items['mid'], $lid, 'SF');
							$match[$key]['rfsf_bet'] = $this->getBetFormat($betInfo, $items['mid'], $lid, 'RFSF');
							$hotArr[] = $match[$key]['hot'] ? (($items['hotid'] == '0') ? '10' : $items['hotid']) : '100';
							$keyArr[] = $index++;
							array_push($matches, $match[$key]);
						}
					}
					break;
				default:
					# code...
					break;
			}
		}

		// 按热门赛事热度排序（优先级1-9，0）
		// 按比赛的issue升序排
		// 按比赛截止时间，升序排列
		array_multisort($hotArr, SORT_ASC, $keyArr, SORT_ASC, $matches);

		$headerInfo = $this->getRequestHeaders();

		// 针对竞足竞篮加奖统一调整v4.1
		if($headerInfo['appVersionCode'] >= '40100')
		{
			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => array(
					'match' => $matches,
					'showTips' => ($lid == '43') ? $this->getJclqTicket() : '0',
					'showJj' => $this->getJcjjInfo($lid, $headerInfo['appVersionCode']),
				) 
			);
			die(json_encode($result));
		}

		// 针对竞彩篮球出票时间调整v2.7
		if($lid == '43' && $headerInfo['appVersionCode'] >= '11')
		{
			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => array(
					'match' => $matches,
					'showTips' => $this->getJclqTicket()
				) 
			);
			die(json_encode($result));
		}

		$result = array(
			'status' => '1',
			'msg' => 'Success',
			'data' => $matches
		);

		echo json_encode($result);
	}

	/*
 	 * 开奖公告首页接口
 	 * @date:2015-05-21
 	 */
	public function getAwards()
	{
		// 获取版本信息
		$headerInfo = $this->getRequestHeaders();

		$this->load->model('award_model', 'Award');
		$awardData = $this->Award->getLastByDcenter();

		if(!empty($awardData))
		{
			$lists = array();
			foreach ($awardData as $award) 
    		{
    			$lists[$award['seLotid']] = $award;
    		}

    		// 重新排序
			$awards = array();
			if($headerInfo['appVersionCode'] < '6')
			{
				$lotteryInfo = array('51','23529','52','42','43','21406','33','35','11','19');
			}
			elseif($headerInfo['appVersionCode'] == '6')
			{
				$lotteryInfo = array('51','23529','52','42','43','21406','21407','33','35','11','19');
			}
			else
			{
				$lotteryInfo = array('51','23529','52','42','43','21406','21407','21408','33','35','11','19', '53', '10022', '23528', '54', '55', '56', '57', '21421');
			}
		
			foreach ($lotteryInfo as $key => $lid) 
    		{
    			if($lists[$lid])
    			{
    				$awards[$key] = $lists[$lid];
    			}
    			else
    			{
    				$awards[$key]['seLotid'] = $lid;
    			}			
    		}

    		if(!empty($awards))
    		{
    			$result = array(
					'status' => '1',
					'msg' => 'Success',
					'data' => $awards
				);
    		}
    		else
    		{
    			$result = array(
					'status' => '0',
					'msg' => 'Error',
					'data' => array()
				);
    		}
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => 'Error',
				'data' => array()
			);
		}
    	echo json_encode($result);
	}

	/*
 	 * 获取省市信息的接口
 	 * @date:2015-04-20
 	 */
	public function cityInfo()
	{

		$result = array(
			'status' => '0',
			'msg' => 'Error',
			'data' => array()
		);

		$info = $this->Data->getProvince();

		if(!empty($info))
		{
			$cityList = array();
			foreach ($info['province'] as $list) 
			{
				foreach ($info['details'] as $key => $items) 
				{
					if($list['province'] == $items['province'])
					{
						$cityList[$list['province']][] = $items['city'];
					}
				}
			}

			$result = array(
				'status' => '1',
				'msg' => 'Success',
				'data' => $cityList
			);
		}
		echo json_encode($result);
	}

	/*
 	 * 提交用户意见反馈
 	 * @date:2015-05-14
 	 */
	public function setFeedback()
	{
		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => array()
		);

		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		// 检查必要参数
		if( empty($data['uid']) || empty($data['content']) )
		{
			echo json_encode($result);
			exit();
		}

		// 检查用户信息
		$info = $this->user_model->getUserInfo($data['uid']);

		if(empty($info))
		{
			$result = array(
				'status' => '0',
				'msg' => '无效的用户信息',
				'data' => array()
			);
            echo json_encode($result);
			exit();
		}	

		// 检查内容长度
		if($this->comm->abslength($data['content']) > 500)
		{
            $result = array(
				'status' => '0',
				'msg' => '反馈内容过长，请重新输入！',
				'data' => array()
			);
            echo json_encode($result);
			exit();
        }

        $headerInfo = $this->getRequestHeaders();
        
        //组装数据
        $record = array(
            'uid' => $data['uid'],
            'name' => $info['uname'],
            'content' => $this->security->xss_clean($data['content']),
            'if_reply' => 0,
            'type' => 2,
            'platform' => $this->config->item('platform'),
            'model' => $headerInfo['model'] ? $headerInfo['model'] : '',
    		'system' => $headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
    		'version' => $headerInfo['appVersionName'],
        );

        $this->load->model('news_model');
        $res = $this->news_model->insertNewsList($record);

        if($res)
        {
        	$result = array(
				'status' => '1',
				'msg' => '反馈提交成功',
				'data' => array()
			);
        }
        else
        {
        	$result = array(
				'status' => '0',
				'msg' => '反馈提交失败',
				'data' => array()
			);
        }
        
        echo json_encode($result);
		exit();
	}

	/*
 	 * 查询用户意见反馈
 	 * @date:2015-05-14
 	 */
	public function getFeedback()
	{
		// $data = array(
		// 	'uid' => '',
		// 	'cpage' => '',
		// 	'pagesize' => ''
		// );

		$data = $this->strCode($this->input->post('data'));
		
		$data = json_decode($data, true);

		$result = array(
			'status' => '0',
			'msg' => '缺少必要参数',
			'data' => ''
		);

		if(empty($data['uid']))
		{
			echo json_encode($result);
			exit();
		}

		// 默认每页10条
		$pagesize = $data['pagesize'] ? $data['pagesize'] : 10;

		if( !is_numeric($data['cpage']) || empty($data['cpage']) || $data['cpage'] <= 0 )
		{
            $cpage = 1;
        }
        else
        {
        	$cpage = $data['cpage'];
        }

		$this->load->model('news_model');
		//统计总消息数量
        $count = $this->news_model->countNewsList($data['uid']);

        $count = $count[0];
        $maxpage = ceil(intval($count)/$pagesize);

        if( $cpage > $maxpage )
        {
            $cpage = 1;
        }

        $pagenum = $pagesize*($cpage-1);

        $newsList = array();
        //分页统计
        $listInfo = $this->news_model->getNewsList($data['uid'], $pagenum, $pagesize);

        if($listInfo)
        {
            foreach ($listInfo as $k => $items) 
            {
            	$newsList[$k]['uname'] = $items['name'];
            	$newsList[$k]['content'] = $items['content'];
            	$newsList[$k]['date'] = $items['created'];
            	$newsList[$k]['reply_content'] = '';
            	$newsList[$k]['reply_date'] = '';
                $replyInfo = $this->news_model->getReplyList($listInfo[$k]['id']);
                if($replyInfo)
                {
                	// 仅显示一条
            		$newsList[$k]['reply_content'] = $replyInfo[0]['content'];
        			$newsList[$k]['reply_date'] = $replyInfo[0]['created'];
                }
            }
        }
        
        //更新所有的消息为已读
        $this->news_model->updateNewsList($data['uid']);

        $result = array(
			'status' => '1',
			'msg' => 'Success',
			'data' => array(
				'newsList' => $newsList,
				'pageNumber' => $maxpage
			)
		);

		echo json_encode($result);
		exit();
	}

	/*
 	 * 获取最新期次的遗漏数据
 	 * @date:2015-10-14
 	 */
	private function getMissNumber($lid)
	{
		// 在售期缓存
		$info = $this->Cache->getLastByLid($lid);

		// 遗漏数据缓存
		$missInfo = $this->Cache->getMissInfo($lid);

		$missData = array();
		
		if(!empty($info[0]['awardNumber']) && !empty($missInfo))
		{
			$this->load->library('libcomm');
			$issue = $this->libcomm->getIssueFormat($lid, $info[0]['seExpect']);

			// 判断期次缓存是否及时
			$missIssueArr = array_keys($missInfo);
			// 遗漏内最新期次
			$lastMiss = $missIssueArr[0];

			if($lastMiss >= $issue)
			{
				if(in_array($lid, array('51', '23529', '23528')))
				{
					$missArry = explode("|", $missInfo[$lastMiss]);
				}
				elseif (in_array($lid, array('53', '56', '57')))
				{
					$missArry = explode('|', $missInfo[$lastMiss][0]);
				}
				else
				{
					$missArry = $missInfo[$lastMiss];
				}
				if($lid == '54')
				{
					$keyName = array('renxuan', 'duizi', 'tonghua', 'shunzi', 'tonghuashun', 'baozi', 'baoxuan');
					foreach ($missArry as $key => $items)
					{
						$missData[$keyName[$key]] = $items;
					}
				}
				if($lid == '55')
				{
					$zhixArr = explode('|', $missArry[0]);
					$dxdsArr = explode('|', $missArry[6]);
					$missData = array(
						'gewei'       =>  $zhixArr[4] ? $zhixArr[4] : '',
						'shiwei'      =>  $zhixArr[3] ? $zhixArr[3] : '',
						'baiwei'      =>  $zhixArr[2] ? $zhixArr[2] : '',
						'qianwei'     =>  $zhixArr[1] ? $zhixArr[1] : '',
						'wanwei'      =>  $zhixArr[0] ? $zhixArr[0] : '',
						'exzhux'      =>  $missArry[4] ? $missArry[4] : '',
						'sxzhux'      =>  $missArry[2] ? $missArry[2] : '',
						'dxds_ge'     =>  $dxdsArr[4] ? $dxdsArr[4] : '',
						'dxds_shi'    =>  $dxdsArr[3] ? $dxdsArr[3] : '',
						'sx_xingtai'  =>  $missArry[7] ? $missArry[7] : '',
		            );
				}
				else
				{
					foreach ($missArry as $playType => $items)
					{
						$typeName = $this->libcomm->getTypeName($lid, $playType);
						$missData[$typeName] = $items;
					}
				}
			}
		}
		return $missData;
	}

	/*
 	 * APP 启动验证
 	 * @date:2015-04-17
 	 */
	public function checkSignCode()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		if(!empty($data['sign']) && $data['sign'] == $this->config->item('hashCode'))
		{
			$result = array(
				'status' => '1',
				'msg' => 'sign验证成功',
				'data' => ''
			);
		}
		else
		{
			$result = array(
				'status' => '0',
				'msg' => 'sign验证失败',
				'data' => ''
			);
		}
		
		echo json_encode($result);
	}

	/*
 	 * APP 启动页
 	 * @date:2016-05-26
 	 */
	public function preload()
	{
		// 获取缓存
		$info = $this->Cache->getBannerInfo(2, 1, $platform = 'android');

		$preloadInfo = '';
		if(!empty($info))
		{
			foreach ($info as $items) 
			{
				if(!empty($items) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])) && !empty($items['imgUrl']))
				{
					$preloadInfo = (strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'];
				}
			}
		}

		if($preloadInfo)
		{
			$result = array(
				'status'	=>	'1',
				'msg' 		=>	'通讯成功',
				'data' 		=> 	$preloadInfo
			);
		}
		else
		{
			$result = array(
				'status'	=>	'0',
				'msg' 		=>	'通讯成功',
				'data' 		=> 	''
			);
		}
		echo json_encode($result);
	}

	/*
 	 * APP 中奖通知
 	 * @date:2016-06-26
 	 */
	public function awardsNotice()
	{
		// 获取中奖缓存
		$info = $this->Cache->getOrderWin();

		$awardInfo = array();
		if(!empty($info['orderInfo']))
		{
			$info['orderInfo'] = array_slice($info['orderInfo'], 0, 200);

			$this->load->library('BetCnName');
			foreach ($info['orderInfo'] as $key => $orders) 
			{
				$awardInfo[$key]['detail'] = "恭喜 " . mb_substr($orders['nick_name'], 0, 4) . "*** 投注" . BetCnName::$BetCnName[$orders['lid']] . "中奖" . number_format(ParseUnit($orders['margin'], 1), 2) . "元";
			}
		}
		// 调试
		$data = array(
			'statistics' => $info['count']['margin'] ? number_format(ParseUnit($info['count']['margin'], 1), 2) : '0',
			'info' => $awardInfo
		);

		$result = array(
			'status' => '1',
			'msg' => '通讯成功',
			'data' => $data
		);

		echo json_encode($result);
	}

	/*
 	 * 智能追号 - 获取两天内期次
 	 * @date:2016-08-08
 	 */
	public function getSmartIssue()
	{
		$lid = $this->input->get("lid", true);

		// 获取追号期次缓存
		$followIssues = $this->Cache->getSmartIssue($lid);

		$issueData = array();
		if(!empty($followIssues))
		{
			foreach ($followIssues as $key => $items) 
			{
				$issueData[$key]['issue'] = $items['issue'];
			}
		}

        $result = array(
			'status' => '1',
			'msg' => 'Success',
			'data' => $issueData
		);
		echo json_encode($result);
	}

	/*
 	 * 启动页 - 包签名、开机广告、中奖轮播、弹窗、中奖弹窗
 	 * @date:2016-11-28
 	 */
	public function loading()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);
		//$data['uid'] = 1024;
		$headerInfo = $this->getRequestHeaders();
		// 检查用户信息
		$replyId = 0;
                $birthDayPop = "";
		if(!empty($data['uid']))
		{
			$uinfo = $this->user_model->getUserInfo($data['uid']);
			if(empty($uinfo))
			{
				$data['uid'] = '';
				$uinfo = array();
			}
			else
			{
			    //小于4.2版本执行
			    if(!empty($headerInfo['deviceId']) && $headerInfo['appVersionCode'] < '40200')
			    {
			        // 更新用户登录信息
			        $this->user_model->saveUser(
			            array(
			                'uid' => $uinfo['uid'],
			                'last_login_time' => date('Y-m-d H:i:s'),
			                'last_login_channel' => $this->recordChannel($headerInfo['channel']),
			            )
			        );
			        
			        //消息入队
			        apiRequest('common_stomp_send', 'login',array('uid' => $uinfo['uid'], 'last_login_time' => $uinfo['last_login_time']));
			    }
			}

			// 消息中心
			if($data['uid'])
			{
				$this->load->model('info_comments_model');
				$replyId = $this->info_comments_model->getReplyId($data['uid']);
			}
                        
                        $birthDay = $this->user_model->getBirthDay($data['uid']);
                        $date = date("m-d");
                        if(($birthDay['birthday'] == $date) && $uinfo['grade'] > 3 ){
                            $birthDayPop = array(
                                'type' => 0,
                                'congratulations' => '生日快乐',
                                'des' => '别忘了这个重要的日子哦',
                                'lid' => 51,
                                'btnText' => '试试手气',
                                'picurl' => $this->config->item('protocol') . $this->config->item('pages_url').'caipiaoimg/static/images/prompt/birthday_icon.png',
                            );
                        }
		}

		$prel = $this->getPreload();
		$this->load->model('activity_model');
		$activity = $this->activity_model->getRemarkById(9);
		$status=  json_decode($activity['remark'], true);
		$result = array(
			'status' => '1',
			'msg' => 'Success',
			'data' => array(
				'signCode'	=>	$this->checkPackage($data),
				'preload'	=>	$prel['imgUrl'],
				'preloadUrl'=>	$prel['hrefUrl'],
				'lid' 		=>  $prel['lid'],
				'playTypeName'	=>	$prel['playTypeName'],
				'popInfo'	=>	$this->getPopInfo($data, $uinfo),
				'lxStatus'  =>  ($status['app'] == 2) ? 0 : 1,
				'lxContent'	=>	($status['app'] != 2 && $status['app_content']) ? $status['app_content'] : '',
				//'winPop'	=>	$this->getUserAwards($data['uid']),
			    'winPop'	=>	'', //暂时停掉中奖弹层 2018.6.15
				'isWxLogin'	=>	$this->isWxLogin($this->config->item('platform')),
				'replyId'	=>	$replyId,
				'eventGate'	=>	$this->getEventGate($this->config->item('platform')),
                                'birthdayPop'   =>      $birthDayPop
			)
		);
        
		//小于4.2版本执行
		if(!empty($headerInfo['deviceId']) && $headerInfo['appVersionCode'] < '40200')
		{
		    // 登录信息统计
		    $this->recordLogin($data);
		}
		
		echo json_encode($result);
	}

	/*
 	 * 启动页 - 包签名检查
 	 * @date:2016-11-28
 	 */
	private function checkPackage($data)
	{
		$result = '0';
		if(!empty($data['sign']) && $data['sign'] == $this->config->item('hashCode'))
		{
			$result = '1';
		}
		return $result;
	}

	/*
 	 * 启动页 - 开机启动页
 	 * @date:2016-11-28
 	 */
	private function getPreload()
	{
		$data = array(
			'imgUrl' => '',
			'hrefUrl' => '',
			'lid' => 0,
		);

		$info = $this->Cache->getBannerInfo(2, 1, $platform = 'android');

		$preloadInfo = array(
			'imgUrl'	=>	'',
			'hrefUrl' 	=>	'',
			'lid' 		=>	0,
			'playTypeName'	=>	'',
		);

		// 竞彩足球玩法
	    $jczqPlayArr = array(
	        '1' =>  '胜平负',
	        '2' =>  '让球胜平负',
	        '3' =>  '单关',
	        '4' =>  '总进球',
	        '5' =>  '比分',
	        '6' =>  '半全场',
	    );

		if(!empty($info))
		{
			foreach ($info as $items) 
			{
				if(!empty($items) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])) && !empty($items['imgUrl']))
				{
					// 启动页竞彩足球区分玩法
					$playTypeName = '';
					if($items['lid'] == '42' && !empty($items['extra']))
                	{
                		$extra = json_decode($items['extra'], true);
	                    if($jczqPlayArr[$extra['playType']])
	                    {
	                    	$playTypeName = $jczqPlayArr[$extra['playType']];
	                    }
                	}
					$preloadInfo = array(
						'imgUrl'	=>	(strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'],
						'hrefUrl' 	=>	(strpos($items['url'], 'http') !== FALSE) ? $items['url'] : $this->config->item('protocol') . $items['url'],
						'lid' 		=>	$items['lid'],
						'playTypeName'	=>	$playTypeName,
					);
				}
			}
		}		
		return $preloadInfo;
	}

	/*
 	 * 启动页 - 首页弹框
 	 * @date:2016-11-28
 	 */
	private function getPopInfo($data, $uinfo = array())
	{
		// 获取弹窗信息缓存
		$info = $this->Cache->getBannerInfo(3, 1, $platform = 'android');

		$popInfo = '';
		$popData = array();
		if(!empty($info))
		{
			foreach ($info as $items) 
			{	
				if(!empty($items['channels']) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])))
				{
					$extra = json_decode($items['extra'], true);
					$items['needLogin'] = $extra['needLogin'];
					$items['appAction'] = $extra['appAction'];
					$items['time'] = $extra['time'];
					$items['isShow'] = $extra['isShow'];

					if(!empty($items['isShow']))
					{
						if(!empty($data['uid']) && in_array($items['needLogin'], array('-1', '1')))
						{
							// 已登录
							if(empty($popData))
							{
								$popData = $items;
							}
							elseif($items['needLogin'] > $popData['needLogin'])
							{
								$popData = $items;
							}
						}
						elseif(empty($data['uid']) && in_array($items['needLogin'], array('-1', '0')))
						{
							// 未登录
							if(empty($popData))
							{
								$popData = $items;
							}
							elseif($items['needLogin'] > $popData['needLogin'])
							{
								$popData = $items;
							}
						}
					}
				}
			}
		}

		if(!empty($popData) && !empty($popData['imgUrl']))
		{
			// 已绑定邮箱，不提示
			if($popData['appAction'] == 'email' && !empty($uinfo['email']))
			{
				$popInfo = '';
			}
			else
			{
				$popInfo = array(
					'imgUrl'	=>	(strpos($popData['imgUrl'], 'http') !== FALSE) ? $popData['imgUrl'] : $this->config->item('protocol') . $popData['imgUrl'],
					'webUrl'	=>	$popData['url'] ? $popData['url'] : '',
					'appAction'	=>	$popData['appAction'],
					'lid'		=>	$popData['lid'],
					'modify'	=>	(string)(strtotime($popData['time']) * 1000),
				);
			}
		}
		return $popInfo;
	}

	/*
 	 * 启动页 - 记录登录信息
 	 * @date:2016-11-28
 	 */
	private function recordLogin($data)
	{
		if(!empty($data['uid']))
		{
			$headerInfo = $this->getRequestHeaders();

			$refe = REFE ? REFE : '';
        	$loginRecord = array(
        		'login_time' => date('Y-m-d H:i:s'), 
        		'uid' => $data['uid'], 
        		'ip' => UCIP, 
        		'area' => $this->tools->convertip(UCIP), 
        		'reffer' => $refe, 
        		'platform' => $this->config->item('platform'),
        		'model' => $headerInfo['model'] ? $headerInfo['model'] : '',
        		'system' => $headerInfo['OSVersion'] ? $headerInfo['OSVersion'] : '',
        		'version' => $headerInfo['appVersionName'],
                        'channel' => $this->recordChannel($headerInfo['channel']),
        	);
        	$this->user_model->loginRecord($loginRecord);
		}
	}

	/*
 	 * 数字彩投注页 - 活动信息
 	 * @date:2016-11-28
 	 */
	private function getActivityMark($lid)
	{
		$info = array(
			'imgUrl' => '',
			'webUrl' => ''
		);

		$banner = $this->Cache->getBannerInfo(5, 1, $platform = 'android');
		if(!empty($banner))
		{
			foreach ($banner as $items) 
			{
				if($items['lid'] == $lid && !empty($items['imgUrl']) && !empty($items['url']))
				{
					$info = array(
						'imgUrl'	=>	(strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'],
						'webUrl' 	=> 	$items['url'],
					);
					break;
				}
			}
		}
		return $info;
	}

	/*
 	 * 竞彩篮球出票时间
 	 * @date:2016-11-28
 	 */
	private function getJclqTicket()
	{
		$res = false;
		$time = time();
		$current = date('H:i:s', $time);
		$week = date('w', $time);
		// 周一二五 9:30 - 23:45
		if(in_array($week, array('1', '2', '5')) && $current >= '09:00:00' && $current <= '23:45:00')
		{
			$res = true;
		}
		// 周三四 8:00 - 23:45
		if(in_array($week, array('3', '4')) && $current >= '07:30:00' && $current <= '23:45:00')
		{
			$res = true;
		}
		// 周六日 9:00 - 00:45
		if(in_array($week, array('0', '6')) && $current >= '09:00:00')
		{
			$res = true;
		}
		if(in_array($week, array('0', '1')) && $current <= '00:45:00')
		{
			$res = true;
		}

		return $res ? '0' : '1';
	}

	// 统计竞彩在售场次
	public function countJc($lid)
	{
		$count = 0;
		// 数据中心
		$info = $this->Cache->getJjcInfo($lid);
		if(!empty($info))
		{
			// 全部玩法均被停售
			if($lid == 42)
			{
				foreach ($info as $key => $items) 
				{
					if( empty($items['spfGd']) && empty($items['rqspfGd']) && empty($items['bqcGd']) && empty($items['jqsGd']) && empty($items['bfGd']) && empty($items['spfFu']) && empty($items['rqspfFu']) && empty($items['bqcFu']) && empty($items['jqsFu']) && empty($items['bfFu']) )
					{
						// 过滤该场比赛
					}
					else
					{
						$count ++;
					}
				}
			}
			else
			{
				foreach ($info as $key => $items) 
				{
					if( empty($items['rfsfGd']) && empty($items['sfGd']) && empty($items['sfcGd']) && empty($items['dxfGd']) && empty($items['sfFu']) && empty($items['rfsfFu']) && empty($items['sfcFu']) && empty($items['dxfFu']) )
					{
						// 过滤该场比赛
					}
					else
					{
						$count ++;
					}
				}
			}
		}
		return $count . '场比赛在售';
	}

	// 投注比例格式化
	public function getBetFormat($betInfo = array(), $id = 0, $lid = 0, $playType = '')
	{
		$default = ($lid == 43) ? '--,--' : '--,--,--';
		
		if(!empty($betInfo))
		{
			$detail = $playType ? $betInfo[$id][$playType] : $betInfo[$id];
			if($detail)
			{
				$betArr = explode(',', $detail);
				if(array_sum($betArr) > 0)
				{
					$default = $detail;
				}
			}
		}
		return $default;
	}

	// 组合遗漏
	public function getCurrentMiss($lid)
	{
		$missData = array();
		if(in_array($lid, array(21406, 21407, 21408, 21421)))
		{
			$this->load->model('miss_model','Miss');
			$missData = $this->Miss->getMissDataOrder($lid);
		}
		// 空容错
		if(empty($missData))
		{
			$missData = '';
		}
		return $missData;
	}

	// 近四天中奖信息
	public function getUserAwards($uid = 0)
	{
		$winInfo = '';
		if(!empty($uid))
		{
			$this->load->model('award_model', 'Award');
			$info = $this->Award->getUserAwards($uid);
			if(!empty($info['orderId']))
			{
				$this->load->library('BetCnName');
				$winInfo = array(
					'lid' => $info['additions'],
					'lname' => BetCnName::$BetCnName[$info['additions']],
					'money' => number_format(ParseUnit($info['money'], 1), 2),
					'url' => $this->getOrderUrl($info),
					'ctype' => (in_array($info['status'], array('3'))) ? 1 : 0,
					'isGaopin' => (in_array($info['additions'], array('21406', '21407', '21408', '53', '54', '55', '56', '57', '21421'))) ? 1 : 0,
				);	
			}
		}
		return $winInfo;
	}

	public function getOrderUrl($info)
	{
		$token = $this->strCode(json_encode(array(
            'uid' => $info['uid'],
        )), 'ENCODE');
		$url = $this->config->item('protocol') . $this->config->item('pages_url');
		if($info['status'] == 1)
		{
			$url .= 'app/chase/detail/';
		}
		elseif($info['status'] == 3)
		{
			$url .= 'app/hemai/detail/hm';
		}
		else
		{
			$url .= 'app/order/detail/';
		}
		$url .= $info['orderId'] . '/' . urlencode($token);
		return $url;
	}

	// 检查微信登录开关
	public function isWxLogin($platform)
	{
		$info = $this->Cache->getBannerInfo(4, 1, $platform = 'android');

		$isWxLogin = '0';
		if(!empty($info[0]['channels']) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $info[0]['channels'])))
		{
			$isWxLogin = '1';
		}
		return $isWxLogin;
	}

	public function getEventGate($platform)
	{
		$info = $this->Cache->getEventInfo($platform = 'android', 'eventStatus');
		return $info ? '1' : '0';
	}

	public function getLotteryMark($lid, $numbers)
	{
		$numbers = explode(',', $numbers);
		$count = count(array_unique(array_values($numbers)));

		if($count == 1)
		{
			$mark = '豹子';
		}
		elseif($count == 2)
		{
			$mark = '组三';
		}
		else
		{
			$mark = '组六';
		}
		return $mark;
	}

	// 竞彩加奖Hover信息
	public function getJcjjInfo($lid, $appVersionCode = 0)
	{
		$this->load->model('lottery_model', 'Lottery');
		$info = $this->Cache->getJjHover(strtoupper($this->Lottery->getEnName($lid)));

		$playTypeArr = array('单关', '2串1');

		$jjInfo = '';
		if(!empty($info) && $info['startTime'] <= date('Y-m-d H:i:s') && $info['endTime'] >= date('Y-m-d H:i:s') && ($info['platform'] & 2) && !empty($info['playType']))
		{
			$params = json_decode($info['params'], true);
			if(!empty($params))
			{
				// V4.6版本区分玩法 playType 1 - 单关，2 - 2串1
				$playTypes = explode(',', $info['playType']);
				if($appVersionCode >= '40600')
				{
					foreach ($playTypes as $playType) 
					{
						$details = array(
							'slogan'	=>	$info['slogan'],
							'money'		=>	$params[0]['min'],
							'playType'	=>	(string)($playType + 1),
							'title'		=>	'竞彩' . $playTypeArr[$playType] . '加奖规则',
						);

						$list = array(
							0	=>	array(
								'name'	=>	'竞彩' . $playTypeArr[$playType] . '奖金分布',
								'desc'	=>	$playTypeArr[$playType] . '加奖金额',
							)	
						);
						$ctype = $playType ? '2c1' : 'dg';
						foreach ($params as $items) 
						{
							$data = array(
								'name'	=>	($items['max'] != '*') ? ParseMoney($items['min']) . '<奖金<=' . ParseMoney($items['max']) : '奖金>' . ParseMoney($items['min']),
								'desc'	=>	ParseMoney($items[$ctype]),
							);
							array_push($list, $data);
						}
						$details['list'] = $list;
						$jjInfo[] = $details;
					}
				}
				else
				{
					$info['playType'] = $playTypes[0];
					$jjInfo = array(
						'slogan'	=>	$info['slogan'],
						'money'		=>	$params[0]['min'],
						'playType'	=>	$info['playType'],
						'title'		=>	'竞彩' . $playTypeArr[$info['playType']] . '加奖规则',
					);

					$list = array(
						0	=>	array(
							'name'	=>	'竞彩' . $playTypeArr[$info['playType']] . '奖金分布',
							'desc'	=>	$playTypeArr[$info['playType']] . '加奖金额',
						)	
					);
					$ctype = $info['playType'] ? '2c1' : 'dg';
					foreach ($params as $items) 
					{
						$data = array(
							'name'	=>	($items['max'] != '*') ? ParseMoney($items['min']) . '<奖金<=' . ParseMoney($items['max']) : '奖金>' . ParseMoney($items['min']),
							'desc'	=>	ParseMoney($items[$ctype]),
						);
						array_push($list, $data);
					}
					$jjInfo['list'] = $list;
				}
			}
		}
		return $jjInfo;
	}

	// 注册实名认证引导配置
	public function getbindEmail($checkData = array(), $platform = 'android')
	{
		$data = '';
		$info = $this->Cache->getBannerInfo(1, 1, $platform = 'android');

		if(!empty($info))
		{
			foreach ($info as $items) 
			{
				$extra = json_decode($items['extra'], true);
				if(!empty($items['channels']) && in_array($this->recordChannel($this->headerInfo['channel']), explode(',', $items['channels'])))
				{
					// 检查实名
					$uinfo = $this->user_model->getUserInfo($checkData['uid']);
					if($uinfo['email'] && $extra['appAction'] == 'email')
					{
						break;
					}

					$data = array(
						'title'		=>	$items['title'],
						'imgUrl'	=>	(strpos($items['imgUrl'], 'http') !== FALSE) ? $items['imgUrl'] : $this->config->item('protocol') . $items['imgUrl'],
						'url'		=>	($extra['appAction'] == 'redpack') ? $this->getRedpackUrl($checkData['uid']) : $items['url'],
						'lid'		=>	$items['lid'] ? $items['lid'] : '0',
						'btnMsg'	=>	$extra['btnMsg'],
						'mark'		=>	$extra['mark'],
						'appAction'	=>	$extra['appAction'] ? $extra['appAction'] : 'webview',
					);
				}
			}
		}
		return $data;
	}

	public function getRedpackUrl($uid)
	{
		$token = $this->strCode(json_encode(array('uid' => $uid)), 'ENCODE');

		$info = file_get_contents('http://www.166cai.net/domain.php');
        $info = json_decode($info, true);

        return $info['data']['android'] . '/app/redpack/index/' . urlencode($token);
	}

	public function getPageConfig()
	{
		$data = $this->strCode($this->input->post('data'));
		$data = json_decode($data, true);

		$type = $type ? intval($data['type']) : 0;

		$typeArr = array(
			0	=>	'bindEmail',	// 实名页配置
		);

		if(empty($data['uid']))
		{
			$result = array(
				'status'	=>	'0',
				'msg'		=>	'用户信息不能为空',
				'data'		=>	array(
					'isOpen'	=>	'0',
					'info'		=> 	''
				),
			);
			die(json_encode($result));
		}

		if(!empty($typeArr[$type]))
		{
			$fun = 'get' . $typeArr[$type];
			$data = $this->$fun($data);
		}

		$result = array(
			'status'	=>	'1',
			'msg'		=>	'',
			'data'		=>	array(
				'isOpen'	=>	$data ? '1' : '0',
				'info'		=> 	$data
			),
		);
		die(json_encode($result));
	}
}