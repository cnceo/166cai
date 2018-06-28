<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 智胜直播数据抓取脚本
 * @author shigx
 *
 */
class Cli_Zhisheng extends MY_Controller 
{
	private $apiUrl;
	public function __construct() 
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->load->library('tools');
		$this->load->model('api_zhisheng_model', 'api_model');
		$this->config->load('jcMatch');
		$this->redis = $this->config->item('redisList');
		$this->apiUrl = $this->config->item('apiUrl');
	}
	
	/**
	 * 赛事赛程  1天执行一次
	 */
	public function schedule()
	{
		$this->jclqSchedule();
		$this->jczqSchedule();
	}
	
	/**
	 * 对阵列表 1分钟执行一次
	 */
	public function matchs()
	{
		$this->jclqMatchs();
		$this->jczqMatchs();
		$this->sfcMatchs();
	}
	
	/**
	 * 技术统计 10分钟执行一次
	 */
	public function statistics()
	{
		$this->jclqMatchStatistics();
		$this->jczqMatchStatistics();
	}
	
	/**
	 * 预计阵容  10分钟执行一次
	 */
	public function expected()
	{
		$this->jclqMatchExpected();
		$this->jczqMatchExpected();
	}
	
	/**
	 * 历史交锋数据   1小时执行一次
	 */
	public function matchClash()
	{
		$this->jczqMatchClash();
		$this->jclqMatchClash();
	}
	
	/**
	 * 近期赛事数据抓取 1小时执行一次
	 */
	public function matchRecent()
	{
		$this->jczqMatchRecent();
		$this->jclqMatchRecent();
	}
	
	/**
	 * 未来赛事数据抓取  1小时执行一次
	 */
	public function matchFuture()
	{
		$this->jczqMatchFuture();
		//篮球投注展示统计
		$this->jclqTzHistory();
		//足球投注展示统计
		$this->jczqTzHistory();
		//胜负彩投注展示统计
		$this->sfcTzHistory();
	}
	
	/**
	 * 积分排名   1小时抓取一次
	 */
	public function rank()
	{
		$this->jclqRanking();
		$this->jczqRanking();
		$this->jczqShotRank();
	}
	
	/**
	 * 篮球赛程信息抓取
	 */
	public function jclqSchedule()
	{
		$mids = array();
		$tids = array();
		$sMids = array();
		$sTids = array();
		$leagues = $this->api_model->getLeague(2);
		foreach ($leagues as $league)
		{
			//获取联赛信息
			$url = $this->apiUrl . "home/base/?oid=4003&lid={$league['lid']}";
			$result = $this->getApi($url);
			if(empty($result) || empty($result['row']))
			{
				continue;
			}
			$sidData = current($result['row']);
			if(empty($sidData['sid']))
			{
				continue;
			}
			$surl = $this->apiUrl . "home/base/?oid=4004&lid={$league['lid']}&sid={$sidData['sid']}";
			$result = $this->getApi($surl);
			if(empty($result) || empty($result['row']))
			{
				continue;
			}
			
			$this->api_model->updateLeague($league['id'], array('sid' => $sidData['sid']));
			$inserData = array();
			$nowDate = date('Y-m-d H:i:s');
			foreach ($result['row'] as $value)
			{
				if(empty($value['mid']))
				{
					continue;
				}
				$data = array();
				$data['mid'] = $value['mid'];
				$data['home'] = $value['home'];
				$data['away'] = $value['away'];
				$data['htid'] = $value['htid'];
				$data['atid'] = $value['atid'];
				$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
				$data['lid'] = $league['lid'];
				$data['ln'] = $league['cnshort'];
				$data['hs1'] = $value['hs1'];
				$data['hs2'] = $value['hs2'];
				$data['hs3'] = $value['hs3'];
				$data['hs4'] = $value['hs4'];
				$data['hot'] = $value['oth'];
				$data['hqt'] = $value['hs1'] + $value['hs2'] + $value['hs3'] + $value['hs4'] + $value['oth'];
				$data['as1'] = $value['as1'];
				$data['as2'] = $value['as2'];
				$data['as3'] = $value['as3'];
				$data['as4'] = $value['as4'];
				$data['aot'] = $value['ota'];
				$data['aqt'] = $value['as1'] + $value['as2'] + $value['as3'] + $value['as4'] + $value['ota'];
				$data['state'] = $value['state'];
				$data['oname'] = $value['oname'];
				$data['coname'] = $value['coname'];
				$data['sid'] = $sidData['sid'];
				$data['type'] = $value['type'];
				$data['oid'] = $value['oid'];
				$inserData[] = $data;
				$mids[] = $value['mid'];
				$tids[] = $value['htid'];
				$tids[] = $value['atid'];
				if($data['mtime'] > $nowDate)
				{
					$sMids[] = $value['mid'];
					$sTids[] = $value['htid'];
					$sTids[] = $value['atid'];
				}
			}
			
			if($inserData)
			{
				$this->api_model->saveLqMatchs($inserData);
			}
			//积分榜数据入库
			$rankData = $this->getLqRank($league['lid'], $sidData['sid']);
			if($rankData)
			{
				$this->api_model->saveLqRanking($rankData);
			}
		}
		$mids = array_unique($mids);
		$tids = array_unique($tids);
		$sMids = array_unique($sMids);
		$sTids = array_unique($sTids);
		//历史交锋
		$count = 0;
		$inserData = array();
		foreach ($sMids as $mid)
		{
			$result = $this->getLqClashMatch($mid);
			foreach ($result as $mid => $value)
			{
				$inserData[$mid] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveLqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveLqHfmatchs($inserData);
		}
		
		//最近赛事
		$count = 0;
		$inserData = array();
		foreach ($sTids as $tid)
		{
			$result = $this->getLqRecentMatch($tid);
			foreach ($result as $mid => $value)
			{
				$inserData[$mid] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveLqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveLqHfmatchs($inserData);
		}
		
		//预计阵容数据入库
		/*foreach ($mids as $mid)
		{
			$expectedData = $this->getLqExpected($mid);
			if($expectedData)
			{
				$this->api_model->saveLqExpected($expectedData);
			}
		}*/
		
		//事件信息入库
		/*$count = 0;
		$inserData = array();
		foreach ($mids as $mid)
		{
			$result = $this->getLqStatistics($mid);
			foreach ($result as $value)
			{
				$inserData[] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveLqStatistics($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		
		if($inserData)
		{
			$this->api_model->saveLqStatistics($inserData);
		}*/
	}
	
	/**
	 * 足球赛程信息抓取
	 */
	public function jczqSchedule()
	{
		$mids = array();
		$tids = array();
		$sMids = array();
		$sTids = array();
		$leagues = $this->api_model->getLeague(1);
		foreach ($leagues as $league)
		{
			//获取联赛信息
			$url = $this->apiUrl . "home/base/?oid=1003&lid={$league['lid']}";
			$result = $this->getApi($url);
			if(empty($result) || empty($result['row']))
			{
				continue;
			}
			$sidData = current($result['row']);
			if(empty($sidData['sid']))
			{
				continue;
			}
			$surl = $this->apiUrl . "home/base/?oid=1004&lid={$league['lid']}&sid={$sidData['sid']}";
			$result = $this->getApi($surl);
			//请求失败或无数据直接跳过
			if(empty($result) || empty($result['row']))
			{
				continue;
			}
	
			$this->api_model->updateLeague($league['id'], array('sid' => $sidData['sid']));
			$inserData = array();
			$nowDate = date('Y-m-d H:i:s');
			foreach ($result['row'] as $value)
			{
				if(empty($value['mid']))
				{
					continue;
				}
				$data = array();
				$data['mid'] = $value['mid'];
				$data['home'] = $value['home'];
				$data['away'] = $value['away'];
				$data['htid'] = $value['htid'];
				$data['atid'] = $value['atid'];
				$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
				$data['lid'] = $league['lid'];
				$data['ln'] = $league['cnshort'];
				$data['sid'] = $sidData['sid'];
				$data['bc'] = $value['bc'];
				$data['hqt'] = $value['hs'];
				$data['aqt'] = $value['as'];
				if($league['kind'] == '1')
				{
					//联赛赛事
					$data['oname'] = '第' . $value['rid'] . '轮';
					$data['type'] = '1';
				}
				else
				{
				    if ($league['lid'] == 149) {
				        $data['oname'] = str_replace('半准决赛', '八强', $value['oname']);
				    } else {
				        $data['oname'] = $value['oname'];
				    }
					$data['type'] = '0';
					
				}
				$data['coname'] = $value['gn'];
				$data['oid'] = $value['oid'];
				$inserData[] = $data;
				$mids[] = $value['mid'];
				$tids[] = $value['htid'];
				$tids[] = $value['atid'];
				if($data['mtime'] > $nowDate)
				{
					$sMids[] = $value['mid'];
					$sTids[] = $value['htid'];
					$sTids[] = $value['atid'];
				}
			}
				
			if($inserData)
			{
				$this->api_model->saveZqMatchs($inserData);
			}
			//积分榜数据入库
			$rankData = $this->getZqRank($league['lid'], $sidData['sid']);
			if($rankData)
			{
				$this->api_model->saveZqRanking($rankData);
			}
			//射手榜数据入库
			$shotData = $this->getZqShot($league['lid'], $sidData['sid']);
			if($shotData)
			{
				$this->api_model->saveZqShotRank($shotData);
			}
		}
		
		//更新非竞彩结期比赛状态
		$this->api_model->updateZqMatchState();
		
		$mids = array_unique($mids);
		$tids = array_unique($tids);
		$sMids = array_unique($sMids);
		$sTids = array_unique($sTids);
		//未来比赛
		$count = 0;
		$inserData = array();
		foreach ($sMids as $mid)
		{
			$result = $this->getZqMatchFuture($mid);
			foreach ($result as $key => $value)
			{
				$inserData[$key] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveZqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveZqHfmatchs($inserData);
		}
		
		//历史交锋
		$count = 0;
		$inserData = array();
		foreach ($sMids as $mid)
		{
			$result = $this->getZqClashMatch($mid);
			foreach ($result as $key => $value)
			{
				$inserData[$key] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveZqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveZqHfmatchs($inserData);
		}
		
		//最近赛事
		$count = 0;
		$inserData = array();
		foreach ($sTids as $tid)
		{
			$result = $this->getZqRecentMatch($tid);
			foreach ($result as $mid => $value)
			{
				$inserData[$mid] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveZqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveZqHfmatchs($inserData);
		}
		
		//预计阵容数据入库
		/*foreach ($mids as $mid)
		{
			$expectedData = $this->getZqExpected($mid);
			if($expectedData)
			{
				$this->api_model->saveZqExpected($expectedData);
			}
		}*/
		
		//事件信息入库
		/*$count = 0;
		$inserData = array();
		foreach ($mids as $mid)
		{
			$data = $this->getZqStatistics($mid);
			if($data)
			{
				$inserData[] = $data;
				if(++$count >= 500)
				{
					$this->api_model->saveZqStatistics($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveZqStatistics($inserData);
		}*/
		
	}
	
	/**
	 * 篮球赛事对阵信息抓取
	 */
	private function jclqMatchs()
	{
		$url = $this->apiUrl . "home/base/?oid=6002";
		$content = $this->getApi($url);
		$mids = array();
		$inserData = array();
		$xids = array();
		if($content && !empty($content['row']))
		{
			foreach ($content['row'] as $value)
			{
				$data = array();
				$data['mid'] = $value['mid'];
				$data['state'] = $value['state'];
				$times = explode(',', $value['mtime']);
				$data['mtime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
				$data['stime'] = $value['stime'] ? '00:' . $value['stime'] : 0;
				$data['type'] = $value['csh'];
				$data['lid'] = $value['lid'];
				$data['ln'] = $value['ln'];
				$data['htid'] = $value['hid'];
				$data['atid'] = $value['aid'];
				$data['home'] = $value['hn'];
				$data['away'] = $value['an'];
				$data['homelogo'] = $value['hlogo'];
				$data['awaylogo'] = $value['alogo'];
				$data['hs1'] = $value['hs1'];
				$data['hs2'] = $value['hs2'];
				$data['hs3'] = $value['hs3'];
				$data['hs4'] = $value['hs4'];
				$data['hot'] = $value['hsj'];
				$data['hqt'] = $value['hs1'] + $value['hs2'] + $value['hs3'] + $value['hs4'] + $value['hsj'];
				$data['as1'] = $value['as1'];
				$data['as2'] = $value['as2'];
				$data['as3'] = $value['as3'];
				$data['as4'] = $value['as4'];
				$data['aot'] = $value['asj'];
				$data['aqt'] = $value['as1'] + $value['as2'] + $value['as3'] + $value['as4'] + $value['asj'];
				$data['hpm'] = str_replace('東', '东', $value['hpm']);
				$data['apm'] = str_replace('東', '东', $value['apm']);
				$data['xid'] = $value['xid'];
				$data['let'] = $value['letscore'];
				$xids[$value['xid']] = $value['mid'];
				$inserData[] = $data;
				//正在进行的场次mid
				if(in_array($value['state'], array(1, 2, 3, 4, 5, 6, 7, 8, 10, 11)))
				{
					$mids[] = $value['mid'];
				}
			}
		}
		if($inserData)
		{
			//针对更换xid与mid对应关系比赛处理
			$xMids = $this->api_model->getLqMidByXids(array_keys($xids));
			$uMids = array();
			foreach ($xMids as $val)
			{
				if($val['mid'] != $xids[$val['xid']])
				{
					$uMids[] = $val['mid'];
				}
			}
			if($uMids)
			{
				$this->api_model->deleteLqXid($uMids);
			}
			$this->api_model->saveLqMatchs($inserData);
			unset($inserData);
				
			$matchs = $this->api_model->getLqSidMatchs();
			$datas = array();
			foreach ($matchs as $match)
			{
				$url = $this->apiUrl . "home/base/?oid=4011&mid={$match['mid']}";
				$content = $this->getApi($url);
				if($content && !empty($content['row']))
				{
					if(empty($content['row'][0]['sid']))
					{
						//赛季编号空时直接跳出
						continue;
					}
					$data = array();
					$data['mid'] = $match['mid'];
					$data['homelogo'] = $content['row'][0]['homelogo'];
					$data['awaylogo'] = $content['row'][0]['awaylogo'];
					$data['sid'] = $content['row'][0]['sid'];
					$data['type'] = $content['row'][0]['type'];
					$datas[] = $data;
				}
		
			}
			if($datas)
			{
				$this->api_model->saveLqMatchs($datas);
			}
				
			$url = $this->apiUrl . "home/base/?oid=6005";
			$content = $this->getApi($url);
			if($content['desc'] == 'succ' && $content['code'] == '0000')
			{
				$this->cache->save($this->redis['JCLQ_MESSGEID'], $content['c']['fnum'], 600);
			}
		}
			
		//非竞彩开出对阵处理
		$noXidMatchs = $this->api_model->getLqNoXidMatchs();
		$result = array();
		foreach ($noXidMatchs as $match)
		{
			$result[$match['mid']] = $match['mid'];
		}
		if($result)
		{
			$nurl = $this->apiUrl . 'home/base/?oid=6001';
			$content = $this->getApi($nurl);
			if($content['desc'] == 'succ' && $content['code'] == '0000' && !empty($content['row']))
			{
				$nMatchs = array();
				foreach ($content['row'] as $value)
				{
					if(in_array($value['mid'], $result))
					{
						$data = array();
						$data['mid'] = $value['mid'];
						$data['state'] = $value['state'];
						$times = explode(',', $value['mtime']);
						$data['mtime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
						$data['hs1'] = $value['hs1'];
						$data['hs2'] = $value['hs2'];
						$data['hs3'] = $value['hs3'];
						$data['hs4'] = $value['hs4'];
						$data['hot'] = $value['hsj'];
						$data['hqt'] = $value['hs1'] + $value['hs2'] + $value['hs3'] + $value['hs4'] + $value['hsj'];
						$data['as1'] = $value['as1'];
						$data['as2'] = $value['as2'];
						$data['as3'] = $value['as3'];
						$data['as4'] = $value['as4'];
						$data['aot'] = $value['asj'];
						$data['aqt'] = $value['as1'] + $value['as2'] + $value['as3'] + $value['as4'] + $value['asj'];
						$nMatchs[] = $data;
						$mids[] = $value['mid'];
					}
				}
				if($nMatchs)
				{
					$this->api_model->saveLqMatchs($nMatchs);
					unset($nMatchs);
				}
			}
		}
			
		//正在进行的比赛更新事件
		if($mids)
		{
			foreach ($mids as $mid)
			{
				$result = $this->getLqStatistics($mid);
				if($result)
				{
					$this->api_model->saveLqStatistics($result);
				}
			}
		}
	}
	
	/**
	 * 足球赛事对阵信息抓取
	 */
	private function jczqMatchs()
	{
		$startDate = date('Y-m-d', strtotime("-2 day"));
		$endDate = date('Y-m-d', strtotime("+2 day"));
		$allDates = $this->getAllDates($startDate, $endDate);
		$mids = array();
		$inserData = array();
		$xids = array();
		foreach ($allDates as $date)
		{
			$url = $this->apiUrl . 'home/base/?oid=3001&lottid=6&expect=' . $date;
			$content = $this->getApi($url);
			if($content && !empty($content['row']))
			{
				foreach ($content['row'] as $value)
				{
					$data = array();
					$data['mid'] = $value['mid'];
					$data['state'] = $this->getZqState($value['state']);
					$times = explode(',', $value['mtime']);
					$data['mtime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
					if($value['ktime'])
					{
						$times = explode(',', $value['ktime']);
						$data['stime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
					}
					else
					{
						$data['stime'] = '';
					}
					$data['lid'] = $value['lid'];
					$data['ln'] = $value['ln'];
					$data['htid'] = $value['htid'];
					$data['atid'] = $value['atid'];
					$data['home'] = $value['hteam'];
					$data['away'] = $value['ateam'];
					$data['homelogo'] = $value['hlogo'];
					$data['awaylogo'] = $value['alogo'];
					$data['bc'] = str_replace('-', ':', $value['bc']);
					$data['hqt'] = $value['hscore'];
					$data['aqt'] = $value['ascore'];
					$data['hpm'] = $value['hpm'];
					$data['apm'] = $value['apm'];
					$data['xid'] = $value['xid'];
					$data['oname'] = $value['lc'];
					$data['exflag'] = $value['exflag'];
					$data['scoreState'] = $this->getZqScoreState($value['remark']);
					$xids[$value['xid']] = $value['mid'];
					$inserData[] = $data;
					//正在进行的场次mid
					if(in_array($data['state'], array(1, 2, 3, 7, 8, 9)))
					{
						$mids[] = $value['mid'];
					}
				}
			}
		}
		if($inserData)
		{
			//针对更换xid与mid对应关系比赛处理
			$xMids = $this->api_model->getZqMidByXids(array_keys($xids));
			$uMids = array();
			foreach ($xMids as $val)
			{
				if($val['mid'] != $xids[$val['xid']])
				{
					$uMids[] = $val['mid'];
				}
			}
			if($uMids)
			{
				$this->api_model->deleteZqXid($uMids);
			}
			$this->api_model->saveZqMatchs($inserData);
			unset($inserData);
				
			$matchs = $this->api_model->getZqSidMatchs();
			$datas = array();
			foreach ($matchs as $match)
			{
				$url = $this->apiUrl . "home/base/?oid=1022&mid={$match['mid']}";
				$content = $this->getApi($url);
				if($content && !empty($content['row']))
				{
					if(empty($content['row']['sid']))
					{
						//赛季编号空时直接跳出
						continue;
					}
					$data = array();
					$data['mid'] = $match['mid'];
					$data['homelogo'] = $content['row']['homelogo'];
					$data['awaylogo'] = $content['row']['awaylogo'];
					$data['sid'] = $content['row']['sid'];
					$data['type'] = $content['row']['rid'] ? '1' : '0';
					$datas[] = $data;
				}
	
			}
			if($datas)
			{
				$this->api_model->saveZqMatchs($datas);
			}
				
			$url = $this->apiUrl . "home/base/?oid=3003";
			$content = $this->getApi($url);
			if($content['desc'] == 'succ' && $content['code'] == '0000')
			{
				$this->cache->save($this->redis['JCZQ_MESSGEID'], $content['c']['fnum'], 600);
			}
		}
		//非竞彩开出对阵处理
		$noXidMatchs = $this->api_model->getZqNoXidMatchs();
		$result = array();
		foreach ($noXidMatchs as $match)
		{
			$result[$match['mid']] = $match['mid'];
		}
		if($result)
		{
			$nurl = $this->apiUrl . 'home/base/?oid=3009';
			$content = $this->getApi($nurl);
			if($content['desc'] == 'succ' && $content['code'] == '0000' && !empty($content['row']))
			{
				$nMatchs = array();
				foreach ($content['row'] as $value)
				{
					if(in_array($value['mid'], $result))
					{
						$data = array();
						$data['mid'] = $value['mid'];
						$data['state'] = $this->getZqState($value['state']);
						$times = explode(',', $value['mtime']);
						$data['mtime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
						$data['bc'] = str_replace('-', ':', $value['bc']);
						$data['hqt'] = $value['hscore'];
						$data['aqt'] = $value['ascore'];
						$nMatchs[] = $data;
						$mids[] = $value['mid'];
					}
				}
				if($nMatchs)
				{
					$this->api_model->saveZqMatchs($nMatchs);
					unset($nMatchs);
				}
			}
		}
		
		//正在进行的比赛更新事件
		if($mids)
		{
			$inserData = array();
			foreach ($mids as $mid)
			{
				$data = $this->getZqStatistics($mid);
				if($data)
				{
					$inserData[] = $data;
				}
			}
			if($inserData)
			{
				$this->api_model->saveZqStatistics($inserData);
			}
		}
	}
	
	/**
	 * 篮球技术统计
	 */
	private function jclqMatchStatistics()
	{
		$matchs = $this->api_model->getLqMatchs();
		//技术统计
		$inserData = array();
		foreach ($matchs as $match)
		{
			//未开赛直接跳出
			if($match['state'] == '0')
			{
				continue;
			}
			$result = $this->getLqStatistics($match['mid']);
			foreach ($result as $value)
			{
				$inserData[] = $value;
			}
		}
		
		if($inserData)
		{
			$this->api_model->saveLqStatistics($inserData);
		}
	}
	
	/**
	 * 足球技术统计
	 */
	private function jczqMatchStatistics()
	{
		$matchs = $this->api_model->getZqMatchs();
		$inserData = array();
		foreach ($matchs as $match)
		{
			if($match['state'] == '0')
			{
				//未开赛的场次直接返回
				continue;
			}
			$data = $this->getZqStatistics($match['mid']);
			if($data)
			{
				$inserData[] = $data;
			}
		}
		
		if($inserData)
		{
			$this->api_model->saveZqStatistics($inserData);
		}
	}
	
	/**
	 * 篮球预计阵容数据抓取
	 */
	public function jclqMatchExpected()
	{
		$matchs = $this->api_model->getLqMatchs();
		$inserData = array();
		$count = 0;
		foreach ($matchs as $match)
		{
			if($match['state'] != '0')
			{
				//已开赛跳出
				continue;
			}
			$expectedData = $this->getLqExpected($match['mid']);
			foreach ($expectedData as $data)
			{
				$inserData[] = $data;
				if(++$count >= 500)
				{
					$this->api_model->saveLqExpected($inserData);
					$inserData = array();
					$count = 0;
				}
			}
		}
		
		if($inserData)
		{
			$this->api_model->saveLqExpected($inserData);
		}
	}
	
	/**
	 * 足球预计阵容数据入库
	 */
	private function jczqMatchExpected()
	{
		$matchs = $this->api_model->getZqMatchs();
		$inserData = array();
		$count = 0;
		foreach ($matchs as $match)
		{
			if($match['state'] != '0')
			{
				//已开赛跳出
				continue;
			}
			$expectedData = $this->getZqExpected($match['mid']);
			foreach ($expectedData as $data)
			{
				$inserData[] = $data;
				if(++$count >= 500)
				{
					$this->api_model->saveZqExpected($inserData);
					$inserData = array();
					$count = 0;
				}
			}
		}
		
		if($inserData)
		{
			$this->api_model->saveZqExpected($inserData);
		}
	}
	
	/**
	 * 足球历史交锋信息抓取
	 */
	private function jczqMatchClash()
	{
		$matchs = $this->api_model->getZqMatchs();
		$count = 0;
		$inserData = array();
		foreach ($matchs as $match)
		{
			if(!in_array($match['state'], array(0, 6, 10, 12)))
			{
				continue;
			}
			$result = $this->getZqClashMatch($match['mid']);
			foreach ($result as $mid => $value)
			{
				$inserData[$mid] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveZqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveZqHfmatchs($inserData);
		}
	}
	
	/**
	 * 足球近期战绩
	 */
	private function jczqMatchRecent()
	{
		$matchs = $this->api_model->getZqMatchs();
		$tids = array();
		foreach ($matchs as $match)
		{
			if(!in_array($match['state'], array(0, 6, 10, 12)))
			{
				continue;
			}
			$tids[] = $match['htid'];
			$tids[] = $match['atid'];
		}
		if($tids)
		{
			//最近赛事
			$count = 0;
			$inserData = array();
			foreach ($tids as $tid)
			{
				$result = $this->getZqRecentMatch($tid);
				foreach ($result as $mid => $value)
				{
					$inserData[$mid] = $value;
					if(++$count >= 500)
					{
						$this->api_model->saveZqHfmatchs($inserData);
						$count = 0;
						$inserData = array();
					}
				}
			}
			
			if($inserData)
			{
				$this->api_model->saveZqHfmatchs($inserData);
			}
		}
	}
	
	/**
	 * 竞彩篮球近期战绩
	 */
	private function jclqMatchRecent()
	{
		$matchs = $this->api_model->getLqMatchs();
		$tids = array();
		foreach ($matchs as $match)
		{
			if(!in_array($match['state'], array(0, 13, 14, 16)))
			{
				continue;
			}
			$tids[] = $match['htid'];
			$tids[] = $match['atid'];
		}
		if($tids)
		{
			$count = 0;
			$inserData = array();
			foreach ($tids as $tid)
			{
				$result = $this->getLqRecentMatch($tid);
				foreach ($result as $mid => $value)
				{
					$inserData[$mid] = $value;
					if(++$count >= 500)
					{
						$this->api_model->saveLqHfmatchs($inserData);
						$count = 0;
						$inserData = array();
					}
				}
			}
			if($inserData)
			{
				$this->api_model->saveLqHfmatchs($inserData);
			}
		}
	}
	
	/**
	 * 足球未来三场比赛抓取
	 */
	public function jczqMatchFuture()
	{
		$matchs = $this->api_model->getZqMatchs();
		$mids = array();
		foreach ($matchs as $match)
		{
			if(!in_array($match['state'], array(0, 6, 10, 12)))
			{
				continue;
			}
			$mids[] = $match['mid'];
		}
		if($mids)
		{
			$count = 0;
			$inserData = array();
			foreach ($mids as $mid)
			{
				$result = $this->getZqMatchFuture($mid);
				foreach ($result as $key => $value)
				{
					$inserData[$key] = $value;
					if(++$count >= 500)
					{
						$this->api_model->saveZqHfmatchs($inserData);
						$count = 0;
						$inserData = array();
					}
				}
			}
			if($inserData)
			{
				$this->api_model->saveZqHfmatchs($inserData);
			}
		}
	}
	
	/**
	 * 竞彩篮球投注对阵统计
	 */
	private function jclqTzHistory()
	{
		$matchInfo = array();
		$matchs = $this->api_model->getLqMatchs();
		foreach ($matchs as $match)
		{
			//只对竞彩未开赛的场次处理
			if($match['state'] != '0' || empty($match['xid']))
			{
				continue;
			}
			$data = array();
			$xid = '20' . $match['xid'];
			$data['mid'] = $match['mid'];
			$data['hrank'] = $match['hpm'];
			$data['arank'] = $match['apm'];
			$history = $this->api_model->getLqHistoryMatch($match);
			$data['his'] = $this->lqWinCalcu($match['htid'], $history);
			$hmatchs = $this->api_model->getLqRecentMatch($match['htid']);
			$data['hstate'] = $this->lqWinCalcu($match['htid'], $hmatchs);
			$amatchs = $this->api_model->getLqRecentMatch($match['atid']);
			$data['astate'] = $this->lqWinCalcu($match['atid'], $amatchs);
			$matchInfo[$xid] = $data;
		}
	
		$REDIS = $this->config->item('REDIS');
		$this->cache->save($REDIS['JCLQ_HISTORY'], json_encode($matchInfo), 0);
	}

	/**
	 * 竞彩篮球投注对阵统计
	 */
	private function jczqTzHistory()
	{
		$matchInfo = array();
		$matchs = $this->api_model->getZqMatchs();
		foreach ($matchs as $match)
		{
			//只对竞彩未开赛的场次处理
			if($match['state'] != '0' || empty($match['xid']))
			{
				continue;
			}
			$data = array();
			$xid = '20' . $match['xid'];
			$data['mid'] = $match['mid'];
			$data['hrank'] = preg_replace('/[^0-9]/', '', $match['hpm']);
			$data['arank'] = preg_replace('/[^0-9]/', '', $match['apm']);
			$history = $this->api_model->getzqHistoryMatch($match);
			$data['his'] = $this->zqWinCalcu($match['htid'], $history, 1);
			$hmatchs = $this->api_model->getzqRecentMatch($match['htid']);
			$data['hstate'] = $this->zqWinCalcu($match['htid'], $hmatchs);
			$amatchs = $this->api_model->getzqRecentMatch($match['atid']);
			$data['astate'] = $this->zqWinCalcu($match['atid'], $amatchs);
			$matchInfo[$xid] = $data;
		}

		$REDIS = $this->config->item('REDIS');
		$this->cache->save($REDIS['JCZQ_HISTORY'], json_encode($matchInfo), 0);
	}
	
	/**
	 * 竞彩篮球历史交锋数据抓取
	 */
	private function jclqMatchClash()
	{
		$matchs = $this->api_model->getLqMatchs();
		//历史交锋
		$count = 0;
		$inserData = array();
		foreach ($matchs as $match)
		{
			if(!in_array($match['state'], array(0, 13, 14, 16)))
			{
				continue;
			}
			$result = $this->getLqClashMatch($match['mid']);
			foreach ($result as $mid => $value)
			{
				$inserData[$mid] = $value;
				if(++$count >= 500)
				{
					$this->api_model->saveLqHfmatchs($inserData);
					$count = 0;
					$inserData = array();
				}
			}
		}
		if($inserData)
		{
			$this->api_model->saveLqHfmatchs($inserData);
		}
	}
	
	/**
	 * 篮球积分榜
	 */
	private function jclqRanking()
	{
		$matchs = $this->api_model->getLqLids();
		foreach ($matchs as $match)
		{
			//积分榜数据入库
			$rankData = $this->getLqRank($match['lid'], $match['sid']);
			if($rankData)
			{
				$this->api_model->saveLqRanking($rankData);
			}
		}
	}
	
	/**
	 * 足球积分榜
	 */
	private function jczqRanking()
	{
		$matchs = $this->api_model->getZqLids();
		foreach ($matchs as $match)
		{
			//积分榜数据入库
			$rankData = $this->getZqRank($match['lid'], $match['sid']);
			if($rankData)
			{
				$this->api_model->saveZqRanking($rankData);
			}
		}
	}
	
	/**
	 * 足球射手榜
	 */
	private function jczqShotRank()
	{
		$matchs = $this->api_model->getZqLids();
		foreach ($matchs as $match)
		{
			//射手榜数据入库
			$shotData = $this->getZqShot($match['lid'], $match['sid']);
			if($shotData)
			{
				$this->api_model->saveZqShotRank($shotData);
			}
		}
	}
	
	/**
	 * 接口远程请求数据返回
	 * @param unknown_type $url
	 * @return Ambigous <multitype:, mixed>
	 */
	private function getApi($url)
	{
		$data = array();
		$content = $this->tools->request($url);
		$content = json_decode($content, true);
		if($content['desc'] == 'succ' && $content['code'] == '0000')
		{
			$data = $content;
		}
		
		return $data;
	}
	
	/**
	 * 足球积分数据返回
	 * @param int $lid	联赛id
	 * @param int $sid	赛季id
	 */
	private function getZqRank($lid, $sid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1007&lid={$lid}&sid={$sid}";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			foreach ($result['row'] as $key => $value)
			{
				$data = array();
				$data['sid'] = $result['c']['sid'];
				$data['type'] = $result['c']['type'];
				$data['tid'] = $value['tid'];
				$data['name'] = $value['name'];
				$data['num'] = $value['enum'];	// 已赛全部
				$data['w'] = $value['w'];			// 胜全部
				$data['d'] = $value['d'];			// 平场数
				$data['l'] = $value['l'];			// 负全部
				$data['goal'] = $value['goal'];	// 进
				$data['loss'] = $value['loss'];	// 失
				$data['diff'] = (string)($value['goal'] - $value['loss']);	// 净
				$data['score'] = $value['score'];	// 积分
				$data['grouping'] = isset($value['group'] ) ? $value['group'] : '';	// 杯赛分组
				$data['sort'] = $key;
				$data['oname'] = isset($value['oname']) ? $value['oname'] : '';
				$datas[] = $data;
			}
		}
		
		return $datas;
	}
	
	/**
	 * 篮球积分数据返回
	 * @param int $lid	联赛id
	 * @param int $sid	赛季id
	 */
	private function getLqRank($lid, $sid)
	{
		$return = array();
		$url = $this->apiUrl . "home/base/?oid=4012&lid={$lid}&seasonid={$sid}";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			$datas = array();
			foreach ($result['row'] as $value)
			{
				$datas[$value['loc']][] = $value;
			}
			//处理分案排名
			foreach ($datas as $loc =>$locs)
			{
				foreach ($locs as $key =>$value)
				{
					$data = array();
					$data['sid'] = $sid;
					$data['tid'] = $value['tid'];
					$data['sname'] = $result['c']['sname'];
					$data['loc'] = $this->lqLocName($value['loc']);
					$data['name'] = $value['name'];
					$data['win'] = intval($value['win']);
					$data['lose'] = intval($value['lose']);
					$data['wrate'] = floatval($value['wrate']);
					$data['cc'] = floatval($value['cc']);
					$data['lwin'] = intval($value['lwin']);
					$data['llose'] = intval($value['llose']);
					$data['qwin'] = intval($value['qwin']);
					$data['qlose'] = intval($value['qlose']);
					$data['hwin'] = intval($value['hwin']);
					$data['hlose'] = intval($value['hlose']);
					$data['awin'] = intval($value['awin']);
					$data['alose'] = intval($value['alose']);
					$data['jwin'] = intval($value['jwin']);
					$data['jlose'] = intval($value['jlose']);
					$data['lx'] = intval($value['lx']);
					$data['zdf'] = intval($value['zdf']);
					$data['zsf'] = intval($value['zsf']);
					$data['rank'] = $key + 1;
					$data['sort'] = $key;
					$return[] = $data;
				}
			}
		}
	
		return $return;
	}
	
	/**
	 * 足球射手榜数据返回
	 * @param int $lid	联赛id
	 * @param int $sid	赛季编号
	 * @return multitype:multitype:NULL number unknown
	 */
	private function getZqShot($lid, $sid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1012&lid={$lid}&sid={$sid}";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			foreach ($result['row'] as $key => $value)
			{
				$data = array();
				$data['sid'] = $result['c']['sid'];
				$data['tid'] = $value['tid'];
				$data['name'] = $value['name'];
				$data['pid'] = $value['pid'];
				$data['pname'] = $value['pname'];
				// 总进球 = 进球 + 点球
				$data['jq'] = $value['jq'] + $value['dq'];
				$data['dq'] = $value['dq'];
				$data['sort'] = $key;
				$datas[] = $data;
			}
		}
		
		return $datas;
	}
	
	/**
	 * 足球未来比赛数据返回
	 * @param unknown_type $mid
	 * @return multitype:multitype:string unknown
	 */
	private function getZqMatchFuture($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1025&mid={$mid}&nums=3";
		$result = $this->getApi($url);
		if($result)
		{
			if($result['home']['row'])
			{
				foreach ($result['home']['row'] as $key => $value)
				{
					$data = array();
					$data['mid'] = $value['mid'];
					$data['home'] = $value['home'];
					$data['away'] = $value['away'];
					$data['htid'] = $value['htid'];
					$data['atid'] = $value['atid'];
					$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
					$data['lid'] = $value['lid'];
					$data['ln'] = $value['ln'];
					$data['state'] = '0';
					$data['int_mtime'] = $value['mtime'];
					$datas[$value['mid']] = $data;
				}
			}
			
			if($result['away']['row'])
			{
				foreach ($result['away']['row'] as $key => $value)
				{
					$data = array();
					$data['mid'] = $value['mid'];
					$data['home'] = $value['home'];
					$data['away'] = $value['away'];
					$data['htid'] = $value['htid'];
					$data['atid'] = $value['atid'];
					$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
					$data['lid'] = $value['lid'];
					$data['ln'] = $value['ln'];
					$data['state'] = '0';
					$data['int_mtime'] = $value['mtime'];
					$datas[$value['mid']] = $data;
				}
			}
		}
		
		return $datas;
	}
	
	/**
	 * 格式化对阵信息  足球
	 * @param unknown_type $value
	 * @return multitype:unknown string
	 */
	private function formtZqHfMatch($value, $clashFlag = true)
	{
		$data = array();
		$data['mid'] = $value['mid'];
		if($clashFlag)
		{
			$data['lid'] = $value['lid'];
		}
		$data['sid'] = $value['sid'];
		$data['ln'] = $value['ln'];
		$data['home'] = $value['hteam'];
		$data['away'] = $value['ateam'];
		$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
		$data['bc'] = str_replace('-', ':', $value['bc']);
		$data['hqt'] = $value['hscore'];
		$data['aqt'] = $value['ascore'];
		$data['bet'] = $value['lbet'];
		$data['binfo'] = $value['lbinfo'];
		$data['htid'] = $value['htid'];
		$data['atid'] = $value['atid'];
		$data['state'] = '1';
		$data['int_mtime'] = $value['mtime'];
	
		return $data;
	}
	
	/**
	 * 篮球数据格式化操作
	 * @param unknown_type $value
	 * @return multitype:number unknown string Ambigous <number, unknown>
	 */
	private function formtLqHfMatch($value)
	{
		$data = array();
		$data['mid'] = $value['mid'];
		$data['lid'] = isset($value['lid']) ? $value['lid'] : 0;
		$data['sid'] = $value['sid'];
		$data['ln'] = $value['lname'];
		$data['home'] = $value['home'];
		$data['away'] = $value['away'];
		$data['htid'] = $value['htid'];
		$data['atid'] = $value['atid'];
		$data['mtime'] = date('Y-m-d H:i:s', $value['mtime']);
		$data['hqt'] = $value['hs1'] + $value['hs2'] + $value['hs3'] + $value['hs4'] + $value['hsot'];
		$data['aqt'] = $value['as1'] + $value['as2'] + $value['as3'] + $value['as4'] + $value['asot'];
		$data['bet'] = $value['letscore'];
		$data['state'] = '1';
		$data['int_mtime'] = $value['mtime'];
	
		return $data;
	}
	
	/**
	 * 足球历史交锋数据返回
	 * @param unknown_type $mid
	 * @return multitype:NULL
	 */
	private function getZqClashMatch($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1017&mid={$mid}";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			foreach ($result['row'] as $key => $value)
			{
				//历史交锋数据大于9就跳出
				if($key > 9)
				{
					break;
				}
				$datas[$value['mid']] = $this->formtZqHfMatch($value);
			}
		}
		
		return $datas;
	}
	
	/**
	 * 篮球历史交锋数据返回
	 * @param unknown_type $mid
	 * @return multitype:NULL
	 */
	private function getLqClashMatch($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=4014&mid={$mid}";
		$content = $this->getApi($url);
		if($content && !empty($content['row']))
		{
			$result = array();
			foreach ($content['row'] as $key => $value)
			{
				$result[$value['mid']] = $value;
			}
			rsort($result);
			$key = 0;
			foreach ($result as $value)
			{
				//历史交锋数据大于9就跳出
				if($key > 9)
				{
					break;
				}
				$datas[$value['mid']] = $this->formtLqHfMatch($value);
				$key++;
			}
		}
	
		return $datas;
	}
	
	/**
	 * 足球最近赛事返回
	 * @param unknown_type $mid
	 * @return multitype:NULL
	 */
	private function getZqRecentMatch($tid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1015&tid={$tid}&type=0";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			foreach ($result['row'] as $key => $value)
			{
				//最近战绩数据大于9就跳出
				if($key > 9)
				{
					break;
				}
				if($tid != $value['htid'])
				{
					$binfo = array('赢' => '输', '输' => '赢', '走' => '走', '-' => '-');
					$value['lbinfo'] = $binfo[$value['lbinfo']];
				}
				$datas[$value['mid']] = $this->formtZqHfMatch($value, false);
			}
		}
	
		return $datas;
	}
	
	/**
	 * 篮球最近赛事返回
	 * @param unknown_type $mid
	 * @return multitype:NULL
	 */
	private function getLqRecentMatch($tid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=4014&tid={$tid}";
		$result = $this->getApi($url);
		if($result && !empty($result['row']))
		{
			foreach ($result['row'] as $key => $value)
			{
				//最近战绩数据大于9就跳出
				if($key > 9)
				{
					break;
				}
				$datas[$value['mid']] = $this->formtLqHfMatch($value);
			}
		}
	
		return $datas;
	}
	
	/**
	 * 足球预计阵容数据返回
	 * @param unknown_type $mid
	 * @return multitype:multitype:number unknown
	 */
	private function getZqExpected($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=1016&mid={$mid}";
		$result = $this->getApi($url);
		if($result)
		{
			if($result['hteam']['r'])
			{
				foreach ($result['hteam']['r'] as $value)
				{
					$data = array();
					$data['mid'] = $mid;
					$data['type'] = 1;
					$data['pid'] = $value['pid'];
					$data['name'] = $value['pname'];
					$data['number'] = $value['pnumber'];
					$data['point'] = $value['point'];
					$data['status'] = $value['pstatus'];
					$datas[] = $data;
				}
			}
			if($result['ateam']['r'])
			{
				foreach ($result['ateam']['r'] as $value)
				{
					$data = array();
					$data['mid'] = $mid;
					$data['type'] = 2;
					$data['pid'] = $value['pid'];
					$data['name'] = $value['pname'];
					$data['number'] = $value['pnumber'];
					$data['point'] = $value['point'];
					$data['status'] = $value['pstatus'];
					$datas[] = $data;
				}
			}
		}
		
		return $datas;
	}
	
	/**
	 * 篮球预计阵容数据返回
	 * @param unknown_type $mid
	 * @return multitype:multitype:number unknown
	 */
	private function getLqExpected($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=4009&mid={$mid}";
		$result = $this->getApi($url);
		if($result)
		{
			if($result['home']['row'])
			{
				foreach ($result['home']['row'] as $value)
				{
					$data = array();
					$data['mid'] = $mid;
					$data['type'] = 1;
					$data['pid'] = $value['pid'];
					$nameArr = explode(') ', $value['name']);
					if(count($nameArr) == 2)
					{
						$data['name'] = $nameArr[1];
						$data['number'] = str_replace('(', '', $nameArr[0]);
					}
					else
					{
						$data['name'] = $value['name'];
						$data['number'] = '-1';
					}
					$data['point'] = $value['point'];
					$data['status'] = $this->getLqStatus($value['status']);
					$data['info'] = isset($value['info']) ? $value['info'] : '';
					$datas[] = $data;
				}
			}
			if($result['away']['row'])
			{
				foreach ($result['away']['row'] as $value)
				{
					$data = array();
					$data['mid'] = $mid;
					$data['type'] = 2;
					$data['pid'] = $value['pid'];
					$nameArr = explode(') ', $value['name']);
					if(count($nameArr) == 2)
					{
						$data['name'] = $nameArr[1];
						$data['number'] = str_replace('(', '', $nameArr[0]);
					}
					else
					{
						$data['name'] = $value['name'];
						$data['number'] = '-1';
					}
					$data['point'] = $value['point'];
					$data['status'] = $this->getLqStatus($value['status']);
					$data['info'] = isset($value['info']) ? $value['info'] : '';
					$datas[] = $data;
				}
			}
		}
	
		return $datas;
	}
	
	/**
	 * 足球事件信息返回
	 * @param unknown_type $mid
	 * @return multitype:unknown string
	 */
	private function getZqStatistics($mid)
	{
		$data = array();
		$url = $this->apiUrl . "home/base/?oid=3010&mid={$mid}";
		$content = $this->getApi($url);
		if($content)
		{
			// 由于接口数据一条和多条格式不一致 做区分处理
			if(empty($content['event']['row'][0]) && !empty($content['event']['row']))
			{
				$result[] = $content['event']['row'];
			}
			else
			{
				$result = $content['event']['row'] ? $content['event']['row'] : array();
			}
			
			if(!empty($result))
			{
				$sortArry = array();
				foreach ($result as $key => $items)
				{
					if($items['lb_img'] == '7')
					{
						$playerArr = explode("|", $items['lb']);
						$playerArr[0] = $playerArr[0] ? $playerArr[0] : '无数据';
						$playerArr[1] = $playerArr[1] ? $playerArr[1] : '无数据';
						$result[$key]['lb'] = implode('|', $playerArr);
					}
					$sortArry[] = str_replace(array("'", "+"), array("", ".1"), $items['tm']);
				}
				array_multisort($sortArry, SORT_ASC, $result);
				$data['mid'] = $mid;
				$data['event'] = json_encode($result);
				$content['total']['rcard'] = $content['total']['rcard'] ? $content['total']['rcard'] : ',';
				$data['total'] = json_encode($content['total']);
			}
		}
		
		return $data;
	}
	
	/**
	 * 足球事件信息返回
	 * @param unknown_type $mid
	 * @return multitype:unknown string
	 */
	private function getLqStatistics($mid)
	{
		$datas = array();
		$url = $this->apiUrl . "home/base/?oid=4010&mid={$mid}";
		$content = $this->getApi($url);
		if($content)
		{
			if($content['home']['row'])
			{
				$content['home']['mid'] = $mid;
				$datas[] = $this->lqStatistics($content['home'], 1);
			}
			if($content['away']['row'])
			{
				$content['away']['mid'] = $mid;
				$datas[] = $this->lqStatistics($content['away'], 2);
			}
		}
	
		return $datas;
	}
	
	/**
	 * 篮球分案信息转换
	 * @param unknown_type $loc
	 * @return Ambigous <unknown, string>
	 */
	private function lqLocName($loc)
	{
		$arr = array(
			'e' => '东部',
			'w' => '西部',
		);
		$name = isset($arr[$loc]) ? $arr[$loc] : $loc;
		return $name;
	}
	
	/**
	 * 篮球预计阵容状态转换
	 * @param unknown_type $status
	 * @return Ambigous <string>
	 */
	private function getLqStatus($status)
	{
		$statusArr = array(
			'0' => '替补',
			'1' => '首发',
			'-1' => '预计缺阵',
		);
	
		return $statusArr[$status];
	}
	
	/**
	 * 篮球技术统计
	 * @param unknown_type $datas
	 * @param unknown_type $type
	 * @return multitype:number unknown
	 */
	private function lqStatistics($datas, $type)
	{
		$data = array(
			'mid' => $datas['mid'],
			'type' => $type,
			's2num' => 0,
			's2hit' => 0,
			's3num' => 0,
			's3hit' => 0,
			'sbnum' => 0,
			'sbhit' => 0,
			'ords' => 0,
			'drds' => 0,
			'assists' => 0,
			'steals' => 0,
			'bshots' => 0,
			'fouls' => 0,
			'turnovers' => 0,
			'fbnum' => 0,
			'fbhit' => 0,
			'mlead' => $datas['mlead'],
		);
		foreach ($datas['row'] as $value)
		{
			$data['s2num'] += intval($value['s2num']);
			$data['s2hit'] += intval($value['s2hit']);
			$data['s3num'] += intval($value['s3num']);
			$data['s3hit'] += intval($value['s3hit']);
			$data['sbnum'] += intval($value['sbnum']);
			$data['sbhit'] += intval($value['sbhit']);
			$data['ords'] += intval($value['ords']);
			$data['drds'] += intval($value['drds']);
			$data['assists'] += intval($value['assists']);
			$data['steals'] += intval($value['steals']);
			$data['bshots'] += intval($value['bshots']);
			$data['fouls'] += intval($value['fouls']);
			$data['turnovers'] += intval($value['turnovers']);
			$data['fbnum'] += intval($value['fbnum']);
			$data['fbhit'] += intval($value['fbhit']);
		}
	
		return $data;
	}
	
	/**
	 * 处理日期数组
	 * @param string $s	开始日期
	 * @param string $e	结束日期
	 * @return array()
	 */
	private function getAllDates($s, $e)
	{
		if (empty($s) || empty($e) || (strtotime($s) > strtotime($e)))
		{
			return array();
		}
		$res = array();
		$datetime1 = new DateTime($s);
		$datetime2 = new DateTime($e);
		$interval  = $datetime1->diff($datetime2);
		$days = $interval->format('%a');
		for ($j = 0; $j <= $days; $j++)
		{
			$time = strtotime("+$j days", strtotime($s));
			$val = date("Y-m-d", $time);
			array_push($res, $val);
		}
	
		return $res;
	}
	
	/**
	 * 足球状态转换处理
	 * @param unknown_type $state
	 * @return Ambigous <string>
	 */
	private function getZqState($state)
	{
		$stateArr = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '7',
			'9' => '7',
			'10' => '8',
			'11' => '9',
			'12' => '4',
			'13' => '10',
			'14' => '11',
			'15' => '12',
			'16' => '13',
			'17' => '0',
		);
	
		return $stateArr[$state];
	}
	
	/**
	 * 篮球计算对阵输赢次数
	 * @param unknown_type $tid
	 * @param unknown_type $matchData
	 */
	private function lqWinCalcu($tid, $matchData)
	{
		$result = '0,0';
		if($matchData)
		{
			$win = 0;
			$lose = 0;
			foreach ($matchData as $val)
			{
				if($tid == $val['atid'])
				{
					$win += ($val['aqt'] > $val['hqt']) ? 1 : 0;
					$lose += ($val['aqt'] > $val['hqt']) ? 0 : 1;
				}
				else
				{
					$win += ($val['hqt'] > $val['aqt']) ? 1 : 0;
					$lose += ($val['hqt'] > $val['aqt']) ? 0 : 1;
				}
			}
			$result = $win . ',' . $lose;
		}
	
		return $result;
	}

	/**
	 * 足球计算对阵胜平负 
	 * @param unknown_type $tid
	 * @param unknown_type $matchData
	 */
	private function zqWinCalcu($tid, $matchData, $wl = 0)
	{
		if($wl)
		{
			// 胜平负进失
			$result = '0,0,0,0,0';
		}
		else
		{
			// 胜平负
			$result = '0,0,0';
		}
		
		if($matchData)
		{
			$win = 0;
			$draw = 0;
			$lose = 0;
			$w = 0;
			$l = 0;
			foreach ($matchData as $val)
			{
				if($tid == $val['atid'])
				{
					$win += ($val['aqt'] > $val['hqt']) ? 1 : 0;
					$draw += ($val['aqt'] == $val['hqt']) ? 1 : 0;
					$lose += ($val['aqt'] < $val['hqt']) ? 1 : 0;
					$w += $val['aqt'];
					$l += $val['hqt'];
				}
				else
				{
					$win += ($val['hqt'] > $val['aqt']) ? 1 : 0;
					$draw += ($val['hqt'] == $val['aqt']) ? 1 : 0;
					$lose += ($val['hqt'] < $val['aqt']) ? 1 : 0;
					$w += $val['hqt'];
					$l += $val['aqt'];
				}
			}

			if($wl)
			{
				$result = $win . ',' . $draw . ',' . $lose . ',' . $w . ',' . $l;
			}
			else
			{
				$result = $win . ',' . $draw . ',' . $lose;
			}
		}
	
		return $result;
	}

	/**
	 * 胜负彩对阵信息抓取
	 */
	private function sfcMatchs()
	{
		$mids = array();
		$inserData = array();
		$xids = array();
		// 获取胜负彩期次
		$REDIS = $this->config->item('REDIS');
        $issues = json_decode($this->cache->get($REDIS['SFC_ISSUE_NEW']), TRUE);
        if(!empty($issues))
        {
        	foreach ($issues as $items) 
        	{
        		if ($items['seFsendtime'] >= time() * 1000)
                {
                	$url = $this->apiUrl . 'home/base/?oid=3001&lottid=1&expect=' . $items['seExpect'];
	    			$content = $this->getApi($url);

	    			if($content && !empty($content['row']))
					{
						foreach ($content['row'] as $value)
						{
							$data = array();
							$data['mid'] = $value['mid'];
							$data['state'] = $this->getZqState($value['state']);
							$times = explode(',', $value['mtime']);
							$data['mtime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
							if($value['ktime'])
							{
								$times = explode(',', $value['ktime']);
								$data['stime'] = date("Y-m-d H:i:s", mktime($times[3], $times[4], $times[5], $times[1], $times[2], $times[0]));
							}
							else
							{
								$data['stime'] = '';
							}
							$data['lid'] = $value['lid'];
							$data['ln'] = $value['ln'];
							$data['htid'] = $value['htid'];
							$data['atid'] = $value['atid'];
							$data['home'] = $value['hteam'];
							$data['away'] = $value['ateam'];
							$data['homelogo'] = $value['hlogo'];
							$data['awaylogo'] = $value['alogo'];
							$data['bc'] = str_replace('-', ':', $value['bc']);
							$data['hqt'] = $value['hscore'];
							$data['aqt'] = $value['ascore'];
							$data['hpm'] = $value['hpm'];
							$data['apm'] = $value['apm'];
							$data['oname'] = $value['lc'];
							$data['exflag'] = $value['exflag'];
							$data['scoreState'] = $this->getZqScoreState($value['remark']);
							$xids[$value['xid']] = $value['mid'];
							$inserData[] = $data;
							//正在进行的场次mid
							if(in_array($data['state'], array(1, 2, 3, 7, 8, 9)))
							{
								$mids[] = $value['mid'];
							}
						}
					}
                }	
        	}
        }

        if($inserData)
		{
			// 入库
			$this->api_model->saveZqMatchs($inserData);
			unset($inserData);
		}

		//正在进行的比赛更新事件
		if($mids)
		{
			$inserData = array();
			foreach ($mids as $mid)
			{
				$data = $this->getZqStatistics($mid);
				if($data)
				{
					$inserData[] = $data;
				}
			}
			if($inserData)
			{
				$this->api_model->saveZqStatistics($inserData);
			}
		}
	}

	/**
	 * 胜负彩投注对阵统计
	 */
	private function sfcTzHistory()
	{
		$matchInfo = array();
		// 获取胜负彩期次
		$REDIS = $this->config->item('REDIS');
        $issues = json_decode($this->cache->get($REDIS['SFC_ISSUE_NEW']), TRUE);
        if(!empty($issues))
        {
        	foreach ($issues as $items) 
        	{
        		if ($items['seFsendtime'] >= time() * 1000)
                {
                	$url = $this->apiUrl . 'home/base/?oid=3001&lottid=1&expect=' . $items['seExpect'];
	    			$content = $this->getApi($url);

	    			if($content && !empty($content['row']))
					{
						foreach ($content['row'] as $match)
						{
							$data = array();
							$xid = $items['seExpect'] . str_pad($match['xid'], 2, "0", STR_PAD_LEFT);
							$data['mid'] = $match['mid'];
							$data['hrank'] = preg_replace('/[^0-9]/', '', $match['hpm']);
							$data['arank'] = preg_replace('/[^0-9]/', '', $match['apm']);
							$history = $this->api_model->getZqHistoryMatch($match);
							$data['his'] = $this->zqWinCalcu($match['htid'], $history, 1);
							$hmatchs = $this->api_model->getZqRecentMatch($match['htid']);
							$data['hstate'] = $this->zqWinCalcu($match['htid'], $hmatchs);
							$amatchs = $this->api_model->getZqRecentMatch($match['atid']);
							$data['astate'] = $this->zqWinCalcu($match['atid'], $amatchs);
							$matchInfo[$xid] = $data;
						}
					}
                }	
        	}
        }
		$REDIS = $this->config->item('REDIS');
		$this->cache->save($REDIS['SFC_HISTORY'], json_encode($matchInfo), 0);
	}
	
	/**
	 * 返回足球状态描述信息
	 * @param unknown $value
	 */
	private function getZqScoreState($value)
	{
	    if($value)
	    {
	        $value = explode('~', $value);
	        foreach ($value as $val)
	        {
	            if(strpos($val, '90分钟') !== false)
	            {
	                $val = str_replace(array(','), array(' , '), $val);
	                return $val;
	            }
	        }
	    }
	    
	    return '';
	}
}
