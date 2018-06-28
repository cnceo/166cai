<?php
class Hemai extends MY_Controller 
{
	
	public function __construct() 
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->load->model('user_model');
		$this->load->model('united_order_model', 'UOrder');
	}
	
	public function detail($orderId, $strCode = null) 
	{
		$this->checkUserAgent();
		$versionInfo = $this->getUserAgentInfo();
		$data = json_decode($this->strCode(urldecode($strCode)), true);
		$uid = isset($data['uid']) ? $data['uid'] : '';
		if(empty($uid) && $this->uid){
                    $uid = $this->uid;
                    $data['uid'] = $uid;
                }
		$orderId = preg_match('/hm(\d{20})/', $orderId, $matches);
		$orderId = $matches[1];
		
		$this->load->library('BetCnName');
		$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, null, ' and status not in (0, 20)');
		if (!empty($orderInfo)) 
		{
			if (!empty($uid)) 
			{
				$data['joinInfo'] = $this->UOrder->getJoin($orderId, array('uid' => $uid), "sum(buyMoney) as buyMoney".($uid == $orderInfo['uid'] ? ',
					sum(case when orderType=3 then buyMoney else 0 end) as bdzrg' : '').', sum(margin) as margin');
				if (empty($data['joinInfo']['buyMoney'])) unset($data['joinInfo']);
				$data['uinfo'] = $this->user_model->getUserInfo($uid);
				// 跟单检查
				$data['unfollow'] = $this->getFollowStatus($uid, $orderInfo);
			}
			$uinfo = $this->user_model->getUserInfo($orderInfo['uid']);
			$orderInfo['uname'] = $uinfo['uname'];
			$points = $this->UOrder->getPoints($orderInfo['uid'], $orderInfo['lid']);
            $orderInfo['points'] = $points[0];
			if ($orderInfo['shopId']) 
			{
				$shop = $this->UOrder->getBetstationByShopid($orderInfo['shopId']);
				$orderInfo['cname'] = $shop['cname'];
				$orderInfo['address'] = $shop['address'];
			}
			$showdetail = $this->getShowDetail($orderInfo, $uid);
			$data['orderStatus'] = $this->order_status;
			if ($showdetail) 
			{
				$this->load->model('order_model','Order');
				$this->load->model('lottery_model');
				$detail = $this->UOrder->getOrderInfo($orderId);
				$orderInfo['betNum'] = $detail['betTnum'];
				$orderInfo['multi'] = $detail['multi'];
				$orderInfo['codecc'] = $detail['codecc'];
				$orderInfo['codes'] = $detail['codes'];
				if($orderInfo['lid'] == BetCnName::JCZQ || $orderInfo['lid'] == BetCnName::JCLQ)
				{
					// 切换出票
					$award = $this->Order->getJjcMatchDetail($orderInfo['lid'], $orderInfo['codecc']);

					//出票成功 显示订单详情
					$ticketData = array();
					if($orderInfo['status'] >= $this->order_status['draw'])
					{
						$orderDetail = $this->orderDetail($orderId);
						$ticketData = $orderDetail['ticketData'];
					} 
				
					if($orderInfo['lid'] == BetCnName::JCZQ)
					{
						switch ($orderInfo['playType']) 
						{
							case 6:
								$this->load->library('JcDgBuyContent', '', 'jcbuy');
								break;
							case 7:
								$this->load->library('JcBonusBuyContent', '', 'jcbuy');
								break;
							default:
								$this->load->library('JcBuyContent', '', 'jcbuy');
								break;
						}
					}
					else
					{
						$this->load->library('JclqBuyContent', '', 'jcbuy');
					}
					$award = $this->jcbuy->index($orderInfo['codes'], $award, $ticketData, $versionInfo);
				
					//玩法
					if($orderInfo['playType'] == 7)
					{
						//竞彩足球奖金优化 玩法
						$passWayArr = array();
						$codeStr = explode(';', $orderInfo['codes']);
						foreach ($codeStr as $code)
						{
							$codes = explode('|', $code);
							array_push($passWayArr, $this->getType(trim($codes[3])));
						}
						$passWayArr = array_unique($passWayArr);
						$passWay = implode(',', $passWayArr);
					}
					else
					{
						$codes = explode('|', $orderInfo['codes']);
						$passWay = $this->getType($codes[2]);
					}
					$data['passWay'] = $passWay;
				
					// 2.5版本新增
					if($versionInfo['appVersionCode'] >= '9')
					{
						$orderPlan = array(
								'codes' 	=> '',
								'lid' 		=> $orderInfo['lid'],
								'isChase'	=> $orderInfo['isChase'],
						);
					}
				}
				elseif($orderInfo['lid'] == BetCnName::SFC || $orderInfo['lid'] == BetCnName::RJ)
				{
				
					//# 获知档期彩票中奖号码
					$awardDetail = $this->Order->getSfcAward($orderInfo['issue']);
					$awardDetail['seLotid'] = $orderInfo['lid'];
					$this->load->library('Lottery');
					$award = $this->lottery->index($orderInfo['codes'], $awardDetail);
					//获取比赛对阵信息
					$data['matches'] = $matches = $this->Order->getSfcMatchs($orderInfo['issue']);
					//获取订单投注信息
					$betInfos = $this->splitBetStr($orderInfo['lid'],$orderInfo['codes']);
					//获取开奖内容
					$awardNumber = explode(',', $awardDetail['awardNumber']);
					//方案与开奖结果对比
					$betStr = array();
					foreach ($betInfos as $in => $betInfo)
					{
						foreach ($betInfo['bet'] as $key => $value)
						{
							//单场选择结果
							$bet1 = $this->strToAry($value);
							rsort($bet1);
				
							$betArray = array();
							foreach ($bet1 as $k => $v)
							{
								$betArray[$k]['is_win'] = 0;
								if(!empty($awardNumber) && $v == $awardNumber[$key]) $betArray[$k]['is_win'] = 1;
								$betArray[$k]['bet'] = $v;
							}
							$betStr[$in][$key] = $betArray;
						}
					}
					$data['betStr'] = $betStr;
					$data['awardDetail'] = $awardDetail;
				}
				else
				{
					//# 获知档期彩票中奖号码
					$awardDetail = $this->Order->getNumIssue($orderInfo['lid'], $orderInfo['issue']);
					
					$this->load->library('Lottery');
					$award = $this->lottery->index($orderInfo['codes'], $awardDetail);
				
					if(empty($awardDetail['awardNumber'])) $awardDetail['tip'] = $this->getAwardTime($orderInfo['lid'], $awardDetail['awardTime']);
				
					// 数字彩继续购买方案
					if(in_array($orderInfo['lid'], array(BetCnName::FCSD, BetCnName::PLS)) && strpos($orderInfo['codes'], ':2:1') !== FALSE && $versionInfo['appVersionCode'] < '11')
						$orderInfo['codes'] = '';
					$data['awardDetail'] = $awardDetail;
				}
			}
			$orderInfo['zjLid'] = ($orderInfo['lid'] == '19') ? '11' : ($orderInfo['lid'] == '35' ? '33' : $orderInfo['lid']);
			$data['orderInfo'] = $orderInfo;
			$data['award'] = $award;
			$data['showdetail'] = $showdetail;
			$data['infostrCode'] = urlencode($this->strCode(json_encode(array('uid' => $orderInfo['uid'], 'lid' => $orderInfo['lid'])), 'ENCODE'));
			$data['strCode'] = $strCode;
			$data['title'] = '合买详情';
			// 新增继续购买方案
			$data['continuebuy'] = $this->continuebuy($orderInfo, $uid);
			$data['versionInfo'] = $versionInfo;
            $data['postData'] = $this->strCode(json_encode(array('uid' => $this->uid, 'puid' => $orderInfo['uid'], 'lid' => $orderInfo['lid'])), 'ENCODE');
            $data['codeStr'] = $this->strCode(json_encode(array(
            	'uid' => $orderInfo['uid'], 
            	'orderId' => $orderId)), 
            'ENCODE');
            $data['is_hide'] = $this->checkIsHide($orderId, $this->uid);
            // 大乐透乐善奖
			$data['lsDetail'] = $this->getLsDetail($orderInfo);
			// 合买宣言
			$data['united_intro'] = $this->getUnitedIntro($orderInfo);
			$data['exception'] = $this->getJjcException($award['exception']);
            $this->load->view('/hemai/detail', $data);
        }
		else 
		{
			exit('访问错误！');
		}
	}
	
	private function getAwardTime($lid, $awardTime)
	{
		$nowTime = strtotime('now');
		$awardTime = substr($awardTime, 0, 10);
		if($nowTime >= $awardTime)
		{
			$tip = "开奖中";
		}
		else
		{
			$timeDif = $awardTime-$nowTime;
			$nowTime = strtotime(date('Y-m-d'));
			$dif = intval(($awardTime-$nowTime)/3600/24);
			$dayInfo = array('今天', '明天', '后天');
			if(in_array($lid, array('51','23529','23528','10022','33','35','52')) && $dif >2 )
			{
				$tip = date('m-d H:i',$awardTime) . "开奖";
			}else{
				$tip = $dayInfo[$dif] . date('H:i',$awardTime) . "开奖";
			}
		}
		return $tip;
	}
	
	private function getShowDetail($orderInfo, $uid) 
	{
		$showdetail = 0;
		switch ($orderInfo['openStatus']) 
		{
			case 2:
				if ($uid == $orderInfo['uid'] || time() > strtotime($orderInfo['openEndtime'])) $showdetail = 1;
				break;
			case 1:
				if ($uid) 
				{
					$res = $this->UOrder->getJoin($orderInfo['orderId'], array('o.uid' => $uid), 'o.id');
					if (!empty($res)) $showdetail = 1;
				}
				break;
			case 0:
			default:
				$showdetail = 1;
				break;
		}
		return $showdetail;
	}
	
	/*
	 * 竞彩足球出票拆单明细
	* @author:shigx
	* @date:2015-06-09
	*/
	public function orderDetail($orderId)
	{
		$orderDetail = $this->Order->getJjcOrderDetail($orderId);
		$counts = 0;
		$orders = array();
		if(!empty($orderDetail))
		{
			//统计总方案数
			$counts = count($orderDetail);
			$key = 1;
			foreach ($orderDetail as $detail)
			{
				$orderInfo = array();
				$orderInfo['id'] = $key++;
				//场次、过关方式从【ticketMix】拆分获取
				$ticketInfo = explode('|', $detail['codes']);
				$ticketDetail = $this->ticketMix($ticketInfo[0], $detail['info'], $detail['odds']);
				// 汇总出票信息
                $ticketData = $this->recordTicketInfo($ticketData, $ticketDetail['ticketData']);
                $orderInfo['matchInfo'] = $ticketDetail['detailInfo'];
				$orderInfo['type'] = $this->getType(count($detail['info']).'*1');
				$orderInfo['betNum'] = $detail['betTnum'];
				$orderInfo['multi'] = $detail['multi'];
				$orderInfo['money'] = $detail['money'];
				$orderInfo['bonus'] = $detail['bonus'];
				$orderInfo['status'] = $detail['status'];
				array_push($orders, $orderInfo);
			}
	
		}
	
		$orders=array(
				'detail' => $orders,
				'counts' => $counts,
				'ticketData' => $ticketData,
		);
		return $orders;
	}
	
	public function ticketMix($ticket, $info, $odds)
	{
		$detailInfo = '';
		$ticketInfo = explode('*', $ticket);
		$count = count($ticketInfo);
		foreach ($ticketInfo as $k_ticket => $v_ticket)
		{
			$ticketDetail = explode(',', $v_ticket);
			$resDetail = $this->ticketDetail($ticketDetail[0],$ticketDetail[1],$ticketDetail[2], $info[$ticketDetail[0]], $odds[$ticketDetail[0]]);
			$detailInfo .= $resDetail['detail'];
            // 赔率信息
            $ticketData[$ticketDetail[0]][$ticketDetail[1]][] = $resDetail['ticketInfo'];
			if($k_ticket < $count - 1)
			{
				$detailInfo .= ' X ';
			}
			 
		}
	
		return array('detailInfo' => $detailInfo, 'ticketData' => $ticketData);
	}
	
	public function ticketDetail($mid, $playType, $tickets, $info, $odds)
	{
		// 出票盘口及赔率信息
        $ticketInfo = array();
		$matchDetail = '';
		//胜平负
		$spf = array(
				'0' => '负',
				'1' => '平',
				'3' => '胜'
		);
	
		//大小分
		$dxf = array(
				'0' => '小分',
				'3' => '大分',
		);
	
		//胜分差
		$sfc = array(
				'01' => '主胜1-5分',
				'02' => '主胜6-10分',
				'03' => '主胜11-15分',
				'04' => '主胜16-20分',
				'05' => '主胜21-25分',
				'06' => '主胜26+分',
				'11' => '主负1-5分',
				'12' => '主负6-10分',
				'13' => '主负11-15分',
				'14' => '主负16-20分',
				'15' => '主负21-25分',
				'16' => '主负26+分',
		);
	
	
		//获取赛事日期
		$date = substr($mid, 0,4).'-'.substr($mid, 4,2).'-'.substr($mid, 6,2);
		//拆分获取 日期 + 赛事编号
		$weekarray = array("日","一","二","三","四","五","六");
		$matchDetail = "<b>周".$weekarray[date("w",strtotime($date))];
		$matchDetail .= substr($mid, 8).'</b>';
		$matchDetail .= '<em>[';
		//拆分获取 赛果选择 + 赔率  存在单场复试 分隔符 /
		$resBet =  explode('/', $tickets);
		$info = json_decode($info, true);
		$count = count($resBet);
		switch ($playType)
		{
			//胜平负
            case 'SPF':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                	// 出票盘口及赔率
                    $pk = '-';
                	$pl = $info["vs"]["v{$matches[1]}"][0];
                	$matchDetail .= "<i>".$spf[$matches[1]].':('.print_str($pl). ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //让球胜平负
            case 'RQSPF': 
                foreach ($resBet as $kBet => $vBet) 
                {
                	// 出票盘口
                	$pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                	$pl = $info["letVs"]["v{$matches[1]}"][0];
                	$matchDetail .= '<i>让' . $spf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl) . ')</i>';
                	if($kBet < $count - 1)
                	{
                		$matchDetail .= ', ';
                	}
                	$ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //猜比分
            case 'CBF':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    $index = preg_replace('/[^\d]/is', '', $matches[1]);                
                    $bf = $this->getCbf($matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["score"]["v$index"][0];
                    $matchDetail .= "<i>".$bf.':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //总进球
            case 'JQS':
                foreach ($resBet as $kBet => $vBet) 
                {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                	// 出票盘口及赔率
                	$pk = '-';
                    if($matches[1] >= 7)
                    {
                    	$pl = $info["goal"]["v7"][0];
                        $matches[1] = '7+';
                    }
                    else
                    {
                    	$pl = $info["goal"]["v".$matches[1]][0];
                    }
                    $matchDetail .= "<i>".$matches[1] . ':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //半全场
            case 'BQC':
                foreach ($resBet as $kBet => $vBet) 
                {
                    preg_match('/^(.*?)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $spfInfo = explode('-', $matches[1]);
                    $pl = $info["half"]["v$spfInfo[0]$spfInfo[1]"][0];            
                    $matchDetail .= "<i>".$spf[$spfInfo[0]] . '-'. $spf[$spfInfo[1]] . ':(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 让分胜负
            case 'RFSF':
                foreach ($resBet as $kBet => $vBet)
                {
                	// 出票盘口及赔率
                	$pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                   	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                   	$pl = $info['letVs']["v$matches[1]"][0];
                   	$matchDetail .= '<i>让' . $spf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜负
            case 'SF':
                foreach ($resBet as $kBet => $vBet) 
                {                  
                    preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["vs"]["v{$matches[1]}"][0];
                    $matchDetail .= "<i>".$spf[$matches[1]].':('.print_str($pl) . ')</i>';
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 大小分
            case 'DXF':
                foreach ($resBet as $kBet => $vBet)
                {
                	$in_map = array('0' => 'l', '3' => 'g');
                	preg_match('/^(\d+)\(.*?\).*?$/is', $vBet, $matches);
                	// 出票盘口及赔率
                	$pk = $info['bs']['basePoint'][0];
                	$pl = $info['bs'][$in_map[$matches[1]]][0];
                	// 出票盘口
                	$matchDetail .= "<i>".$dxf[$matches[1]] . '[' . print_str($pk) .']:(' . print_str($pl).")</i>";
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;
            //竞彩篮球 胜分差
            case 'SFC':
                foreach ($resBet as $kBet => $vBet) {
                	preg_match('/^(\d+)\(.*?\)$/is', $vBet, $matches);  
                	// 出票盘口及赔率
                    $pk = '-'; 
                	$pl = $info['diff']["v{$matches[1]}"][0];
                	$matchDetail .= "<i>".$sfc[$matches[1]] . ':(' . print_str($pl).")</i>";
                    if($kBet < $count - 1)
                    {
                        $matchDetail .= ', ';
                    }
                    $ticketInfo[$pk][$matches[1]][] = $pl;
                }
                break;    
            default:
                # code...
                break;
		}
	
		$matchDetail .= ']</em>';
	
		return array('detail' => $matchDetail, 'ticketInfo' => $ticketInfo);
	}
	
	public function getCbf($bf)
	{
		$bfStr = explode(':', $bf);
		if($bfStr[0] == $bfStr[1])
		{
			if($bfStr[0] >3)
			{
				$bfRes = '平其他';
			}
			else
			{
				$bfRes = $bf;
			}
		}
		elseif($bfStr[0]>5)
		{
			$bfRes = '胜其他';
		}
		elseif($bfStr[1]>5)
		{
			$bfRes = '负其他';
		}
		else
		{
			$bfRes = $bf;
		}
		return $bfRes;
	}
	
	public function getType($type)
	{
		$types = '';
	
		if($type == '1*1')
		{
			$types = '单关';
		}
		else
		{
			$types = str_replace('*', '串', $type);
		}
		return $types;
	}
	
	public function splitBetStr($lid, $codes)
	{
		$data = array();
		//胜负彩
		if($lid == '11' || $lid == '19')
		{
			$preCodes = explode(';',$codes);
			foreach ($preCodes as $key => $preCode)
			{
				$codesStr = explode(':',$preCode,2);
				$betStr = str_replace('#', '-', $codesStr[0]);
				$data[$key]['bet'] = explode(',',$betStr);
				$data[$key]['mode'] = $codesStr[1];
			}
				
		}
		return $data;
	}
	
	public function strToAry($str,$charset='utf8') 
	{
		$strlen = mb_strlen($str);
		$array = array();
		for($i=0;$i<$strlen;$i++)
		{
			$array[$i] = mb_substr($str,$i,1,$charset);
		}
		return $array;
	}
	
	// 汇总出票赔率
    public function recordTicketInfo($info, $details = array())
    {
        if(!empty($details))
        {
            foreach ($details as $mid => $playItems) 
            {
                foreach ($playItems as $playType => $tickets) 
                {
                    foreach ($tickets as $key => $ticketArr) 
                    {
                        foreach ($ticketArr as $pk => $items) 
                        {
                            foreach ($items as $fa => $plArr)
                            {
                                foreach ($plArr as $k => $pl)
                                {
                                    if(!empty($info[$mid][$playType][$pk][$fa]) && in_array($pl, $info[$mid][$playType][$pk][$fa]))
                                    {
                                        continue;
                                    }
                                    else
                                    {
                                    	if(!empty($pl))
                                    	{
                                    		$info[$mid][$playType][$pk][$fa][] = $pl;
                                    	}
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $info;
    }

    public function continuebuy($orderInfo, $uid)
    {
    	$res = array(
    		'codes' => '',
    		'isChase' => ''
    	);
    	// 发起人本人 已截止、已满员、发起人撤单、方案撤单 原生继续购买按钮
    	if( !empty($uid) && ($uid == $orderInfo['uid']) && ((time() >= $orderInfo['endTime']) || ($orderInfo['money'] - $orderInfo['buyTotalMoney']) == 0 || in_array($orderInfo['status'], array(610, 620))) && in_array($orderInfo['lid'], array(51, 23529, 10022, 23528, 52, 33, 35)))
    	{
    		$detail = $this->UOrder->getOrderInfo($orderInfo['orderId']);
    		if($detail['codes'])
    		{
    			$res = array(
    				'codes' => $detail['codes'],
    				'isChase' => $orderInfo['isChase'] ? '1' : '0'
    			);
    		}
    	}
    	return $res;
    }
    
    public function userinfo($str)
    {
    	$data = json_decode($this->strCode(urldecode($str)), true);
    	$this->load->model(array('united_planner_model', 'user_model'));
    	$info = $this->united_planner_model->findByUid($data['uid'], null, $data['lid']);
    	$info['uinfo'] = $this->user_model->getUserInfo($data['uid']);
    	$info['uid'] = $data['uid'];
    	$info['lid'] = $data['lid'];
    	$info['title'] = '战绩记录';
    	$this->load->view('/hemai/userinfo', $info);
    }
    
    public function gdetail($orderId, $strCode = null) 
    {
        $this->checkUserAgent();
        $data = json_decode($this->strCode(urldecode($strCode)), true);
        $uid = isset($data['uid']) ? $data['uid'] : '';
        $orderId = preg_match('/gd(\d{20})/', $orderId, $matches);
        $orderId = $matches[1];
        $this->load->model('follow_order_model');
        $orderInfo = $this->follow_order_model->followOrderDetail($orderId, $uid);
        if (empty($orderInfo)) {
            exit('访问错误！');
        }
        $orders = $this->follow_order_model->getAllFollowOrders($orderId);
        $this->load->library('BetCnName');
        if ($orderInfo['my_status'] == 0) {
            $data['orderStatus'] = "跟单中";
        }
        if ($orderInfo['my_status'] == 1 && $orderInfo['status'] > 1) {
            $data['orderStatus'] = "跟单完成";
        }
        $uinfo = $this->user_model->getUserInfo($orderInfo['puid']);
        $this->load->model('united_planner_model');
        $info = $this->united_planner_model->findByUid($uid, 'united_points', $orderInfo['lid']);
        $data['orderInfo'] = $orderInfo;
        $data['orders'] = $orders;
        $data['orderId'] = $orderId;
        $data['strCode'] = $strCode;
        $data['title'] = '跟单详情';
        $data['uname'] = $uinfo['uname'];
        $data['points'] = $info['united_points'];
        $this->load->view('/hemai/gdetail', $data);
    }
    
    public function cancelGendan()
    {
        $orderId = $this->input->post('orderId', true);
        if(empty($this->uid))
        {
            $result = array(
                'status' => '0',
                'msg' => '订单信息失效',
                'data' => ''
            );
            echo json_encode($result);
            exit();
        }
        if($this->uid && $orderId)
        {
            $this->load->model('follow_order_model');
            $res = $this->follow_order_model->cancelFollowOrder($this->uid, $orderId);
            if ($res['code'] == '200') {
                $result = array(
                    'status' => '1',
                    'msg' => "停止跟单成功",
                    'data' => ''
                );
            } else {
                $result = array(
                    'status' => '0',
                    'msg' => "停止跟单异常，请重新尝试",
                    'data' => ''
                );                
            }
            echo json_encode($res);
        }
    }

    // 检查是否有未删除订单
    public function checkIsHide($orderId, $uid = 0)
    {
    	$versionInfo = $this->getUserAgentInfo();
    	$is_hide = 1;
    	if($uid && $versionInfo['appVersionCode'] >= '40100')
    	{
    		$count = $this->UOrder->countJoinOrders($uid, $orderId, 1);
    		if($count > 0)
    		{
    			$is_hide = 0;
    		}
    	}
    	return $is_hide;
    }

    // 统计跟单数
    public function countJoinOrders()
    {
    	$data = $this->strCode(urldecode($this->input->post('codeStr')));
        $data = json_decode($data, true);

        $result = array(
            'status' => '0',
            'msg' => '订单删除失败，请稍后再试',
            'data' => ''
        );

        if(!empty($data['uid']) && !empty($data['orderId']) && !empty($this->uid))
    	{
    		$count = $this->UOrder->countJoinOrders($this->uid, $data['orderId']);
    		
    		$result = array(
	            'status' => '1',
	            'msg' => '查询成功',
	            'data' => $count
	        );
    	}
    	die(json_encode($result));
    }

    // 删除订单
    public function unitedOrderDel()
    {
    	$data = $this->strCode(urldecode($this->input->post('codeStr')));
        $data = json_decode($data, true);

        $result = array(
            'status' => '0',
            'msg' => '订单删除失败，请稍后再试',
            'data' => ''
        );

    	if(!empty($data['uid']) && !empty($data['orderId']) && !empty($this->uid))
    	{
    		if($this->UOrder->hideUnitedOrder($data['uid'], $this->uid, $data['orderId']))
    		{
    			$result = array(
	                'status' => '1',
	                'msg' => '订单删除成功',
	                'data' => ''
	            );
    		} 
    	}
        die(json_encode($result));
    }

    // 乐善奖
    public function getLsDetail($orderInfo)
    {
        $lsDetail = array();
        $totalMargin = 0;
        $this->load->model('order_model', 'Order');
        $info = $this->Order->getLsDetail($orderInfo['orderId'], $orderInfo['lid']);
        if(!empty($info))
        {
            foreach ($info as $items) 
            {
                if(empty($items['awardNum']))
                {
                    continue;
                }
                $lsDetail[$items['sub_order_id']] = $items;
                $totalMargin += $items['margin'];
            }
        }
        return array('detail' => $lsDetail, 'totalMargin' => $totalMargin);
    }

    // 乐善奖详情
    public function lsDetail($orderId)
    {
		$uid = $this->uid ? $this->uid : 0;
		$orderInfo = $this->UOrder->getUniteOrderByOrderId($orderId, null, ' and status not in (0, 20)');
		$ticketDetail = array();
		$totalMargin = 0;
		if(!empty($orderInfo)) 
		{
			// 检查当前用户合法性
	    	$showdetail = $this->getShowDetail($orderInfo, $uid);
			if(!empty($showdetail))
			{
				$this->load->model('order_model','Order');
				$splitOrders = $this->Order->getLsDetail($orderId, $orderInfo['lid']);
				foreach ($splitOrders as $order)
				{
					if(empty($order['awardNum']))
	                {
	                    continue;
	                }

	                $margin = $order['margin'] ? $order['margin'] : 0;
	                $totalMargin += $margin;
	                // 组装数据
	                $data = array(
	                    'code'          =>  array(),
	                    'awardNum'      =>  $this->lsAwardFormat($order['awardNum']),
	                    'bonusStatus'   =>  $this->getTicketBonus($order['status'], $margin),
	                );

	                $codesArr = array_diff(explode('^', $order['codes']), array(''));
	                $betNum = 1;
	                if(count($codesArr) == 1)
	                {
	                	$betNum = $order['betTnum'];
	                }

					$codes = explode('^', $order['codes']);
	                foreach($codes as $key => $code) 
	                {
	                    if($code !== '') 
	                    {
	                    	$res = $this->renderLsCode($code, $order['playType'], $order['awardNum'], $betNum);
	                        $data['code'][] = $res;
	                    }
	                }
	                array_push($ticketDetail, $data);
				}
			}
		}

		$this->load->view('order/ls_detail', array(
			'title' 		=>  '乐善奖详情',
			'bonusStatus'	=>	$this->getTicketBonus($orderInfo['status'], $totalMargin),
			'ticketDetail'	=>	$ticketDetail,
        ));
    }

    public function lsAwardFormat($awardNum)
    {
        $span = '';
        $numArr = explode('|', $awardNum);
        if($numArr[0] && $numArr[1])
        {
            foreach (explode(',', $numArr[0]) as $num) 
            {
                $span .= '<span>';
                $span .= $num;
                $span .= '</span>';
            }
            foreach (explode(',', $numArr[1]) as $num) 
            {
                $span .= '<span class="blue-ball">';
                $span .= $num;
                $span .= '</span>';
            }
        }
        return $span;
    }

    public function renderLsCode($codes, $playType, $awardNum, $betNum)
    {
    	$span = '';
    	// 开奖号码
    	$awardArr = array();
    	foreach (explode('|', $awardNum) as $nums) 
		{
			$awardArr[] = explode(',', $nums);
		}
		$hasDan = false;
		if(strpos($codes, '#') !== false)
		{
			$hasDan = true;
		}

		if($hasDan)
		{
			$getPlayTypeName = '胆拖';
		}
		else
		{
			$getPlayTypeName = $betNum > 1 ? '复式' : '单式';
		}
		
		$span .= '<span class="num-group-tag">';
		$span .= $getPlayTypeName . '追加';
		$span .= '</span>';
		$span .= '<div class="num-group">';
    	foreach(explode('|', str_replace('*', '|', $codes)) as $ck => $code)
    	{
    		foreach(explode('#', $code) as $dk => $cd) 
    		{
    			if($dk == 0 && count(explode('#', $code)) > 1)
    			{
    				$span .= '<span class="symbol-bracket">(</span>';
    			}
    			foreach(explode(',', $cd) as $c)
    			{
    				$span .= '<span ';
    				if((!empty($awardArr[$ck]) || $awardArr[$ck] === '0') && in_array($c, $awardArr[$ck]))
    				{
    					$span .= "class='bingo'";
    				}
    				$span .= '>';
    				$span .= $c . '</span>';
    			}
    			if($dk == 0 && count(explode('#', $code)) > 1) 
    			{
    				$span .= '<span>)</span>';
    			}			
    		}
    		if($ck + 1 !== count(explode('|', str_replace('*', '|', $codes))))
    		{
    			$span .= '<span class="symbol-colon">:</span>';
    		}
    	}
    	$span .= '</div>';
    	return $span;
    }

    public function getTicketBonus($status, $margin = 0)
    {
    	$bonusStatus = '<b>等待开奖</b>';
    	if(in_array($status, array(1000, 2000)))
    	{
    		if($margin > 0)
    		{
    			$bonusStatus = '<em>' . number_format(ParseUnit($margin, 1), 2) . '元</em>';
    		}
    		else
    		{
    			$bonusStatus = '<b>未中奖</b>';
    		}
    	}
    	return $bonusStatus;
    }

    // 合买宣言 优先级：订单宣言>个人简介>默认
    public function getUnitedIntro($orderInfo)
    {
    	$desInfo = $this->UOrder->getUnitedIntro($orderInfo['orderId']);
    	if(!empty($desInfo))
    	{
    		if(!empty($this->uid) && $this->uid == $orderInfo['uid'] && $desInfo['check_status'] != '2' && $desInfo['delete_flag'] == 0)
    		{
    			return $desInfo['introduction'];
    		}
    		elseif($desInfo['check_status'] == '1' && $desInfo['delete_flag'] == '0')
    		{
    			return $desInfo['introduction'];
    		}
    		else
    		{
    			return $this->config->item('united_intro');
    		}
    	}
    	else
    	{
    		$this->load->model('united_planner_model');
	    	$userDes = $this->united_planner_model->getUserDescription($orderInfo['uid']);
	    	if(!empty($userDes) && !empty($userDes['introduction']) && $userDes['introduction_status'] == '1')
	    	{
	    		return $userDes['introduction'];
	    	}
    	}
    	return $this->config->item('united_intro');
    }

    // 跟单检查
    public function getFollowStatus($uid, $orderInfo)
    {
    	$result = FALSE;
    	if($uid != $orderInfo['uid'])
    	{
    		$this->load->model('follow_order_model');
        	$res = $this->follow_order_model->checkHasGendan($uid, $orderInfo['uid'], $orderInfo['lid']);
        	if($res['code'] != 1 && $res['code'] != 2)
        	{
        		$result = TRUE;
        	}
    	}
    	return $result;
    }
    
    // 竞技彩取消或延期公告
    public function getJjcException($exception = array())
    {
    	$result = '';
    	if(!empty($exception))
    	{
    		$ctypeArr = array(
    			'1'	=>	'延期',
    			'2'	=>	'取消',
    		);
    		$matches = array();
    		$ctypes = array();
    		foreach ($exception as $items) 
    		{
    			array_push($matches, $items['match']);
    			array_push($ctypes, $items['ctype']);
    		}
    		$ctypes = array_unique($ctypes);
    		$ctypeName = (count($ctypes) > 1) ? '延期取消' : $ctypeArr[$ctypes[0]];
    		$result = '注：';
    		if(count($matches) > 2)
    		{
    			$result .= implode('、', array_slice($matches, 0, 2)) . '等多场';
    		}
    		else
    		{
    			$result .= implode('、', array_slice($matches, 0, 2)) . '比赛';
    		}
    		$result .= $ctypeName . '，点击查看' . $ctypeName . '规则';
    	}
    	return $result;
    }
}