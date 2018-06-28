<?php

class Orders extends MY_Controller {

   private $JCLQ_TYPE_MAP = array(
        'hh' => array(
            'cnName' => '混合过关',
        ),
       'sfc' => array(
           'cnName' => '胜分差',
       ),
    );

	private $JCZQ_TYPE_MAP = array(
		'hh' => array(
			'cnName' => '混合过关',
			'playType' => '0',
		),
		'dg' => array(
			'cnName' => '单关',
			'playType' => '6',
		),
		'spf' => array(
			'cnName' => '胜平负',
			'playType' => '1',
		),
		'rqspf' => array(
			'cnName' => '让球胜平负',
			'playType' => '2',
		),
		'cbf' => array(
			'cnName' => '比分',
			'playType' => '5',
		),
	);
	
    public function index() {
        $this->redirect('/');
    }

    public function page() {
        $ps = 10;
        $params = array();
        $pn = $this->input->get('pn');
        if (empty($pn)) {
            $pn = 1;
        }
        $params['pn'] = $pn;
        $type = $this->input->get('type');
        $type = intval($type);
        if ($type > -1) {
            $params['type'] = $type;
        }
        $status = $this->input->get('status');
        $status = intval($status);
        if ($status > -1) {
            $params['status'] = $status;
        }
        $params['ps'] = $ps;
        $recent = $this->input->get('recent');
        if (empty($recent)) {
            $recent = 7;
        }
        $params['begintime'] = strtotime("-{$recent} day") * 1000;  // seconds to milseconds
        $lotteryId = $this->input->get('lid');
        if (!empty($lotteryId)) {
            $params['lid'] = $lotteryId;
        }
        $params['token'] = $this->token;
        $orders = $this->tools->get($this->busiApi . 'ticket/order/list', $params);

        $this->load->view('order/page', array(
            'baseUrl' => $this->config->item('base_url'),
            'orders' => $orders,
            'type' => $type,
        ));
    }
	
    public function info(){
        $orderId = $this->input->post('orderId', true);

		$this->load->library('BetCnName');
        $this->load->model('order_model', 'Order');
        $this->load->model('lottery_model', 'Lottery');
        $this->load->model('neworder_model');
        
        $orderInfo = $this->Order->getOrder(array(
            'uid' => $this->uid,
            'orderId' => $orderId,
        ));

        $jcType = array( JCLQ, JCZQ, GJ, GYJ );
        $numberType = array( DLT, SSQ, SYXW, JXSYXW, FCSD, PLS, PLW, QXC, KS, JLKS, JXKS, QLC, SFC, RJ, HBSYXW, KLPK, CQSSC, GDSYXW);

        $order = $orderInfo['data'];
        if( in_array( $order['lid'], $jcType ) )
        {
            $rst = array(
            	'code' => 0,
            	'lid' => $order['lid'],
                'typeCnName' => BetCnName::getCnName($order['lid']) . ',' . BetCnName::getCnPlaytype($order['lid'], $order['playType']),
                'money' =>  number_format(ParseUnit($order['money'], 1), 2),
                'remain_money' =>  number_format(ParseUnit($this->uinfo['money'], 1), 2),
            );
        }
        elseif( in_array( $orderInfo['data']['lid'], $numberType ) )
        {
            $rst = array(
            	'code' => 0,
            	'lid' => $order['lid'],
                'LotteryCnName' => BetCnName::getCnName($order['lid']), 
                'PlayTypeName' => BetCnName::getCnPlaytype($order['lid'], $order['playType']) == '' ? '普通' : BetCnName::getCnPlaytype($order['lid'], $order['playType']),
                'issue' => $order['issue'], 
                'money' => number_format(ParseUnit($order['money'], 1), 2), 
                'remain_money' => number_format(ParseUnit($this->uinfo['money'], 1), 2),
            );
        }
        if ($order['money'] > $this->uinfo['money']) $rst['code'] = 12;
        $order['money'] = ParseUnit($order['money'], 1);
        $redpack = $this->neworder_model->getBetRedPack($this->uid, $order);
        if($redpack)
        {
        	$rst['redpack'] = $redpack;
        	$rst['redpackId'] = ($redpack[0]['disable'] == 0) ? $redpack[0]['id'] : 0;
        }

        header('Content-type: application/json');
        echo json_encode( $rst );
    }
    
    public function detail($orderId)
    {
    	$this->load->library('BetCnName');
    	$this->load->model('order_model', 'Order');
    	$this->load->model('lottery_model', 'Lottery');
    	//# 查出订单的情况
    	$orderInfo = $this->Order->getOrder(array(
    			'uid' => $this->uid,
    			'orderId' => $orderId
    	));
    	if(!empty($orderInfo['data']))
    	{
    		$order = $orderInfo['data'];
    		if (in_array($order['lid'], array(JCZQ, JCLQ, GJ, GYJ)))
    		{
    			//竞足、竞篮
    			$this->jcDetail($order);
    		}
    		elseif (in_array($order['lid'], array(SFC, RJ)) == SFC)
    		{
    			$this->lzcController(SFC, $order);
    		}
    		else
    		{
    			//数字彩
    			$this->load->driver('cache', array('adapter' => 'redis'));
    			$REDIS = $this->config->item('REDIS');
    			$info = $this->Lottery->getKjinfo($order['lid']);
    			$award = $this->Order->getNumIssue($order['lid'], $order['issue']);
    			$this->load->library('LotteryDetail');
    			$awardNum = $this->lotterydetail->renderAward($award['awardNumber'], $order['lid']);
    			$otherBonus = 0;
    			if($order['status'] < 240)
    			{
    				foreach (explode(';', $order['codes']) as $code) {
    					$isChase = false;
    					if ($orderInfo['lid'] == DLT && strpos($code, '135:1') !== false) $isChase = true;
    					$res = $this->lotterydetail->renderCode($code, $order['lid'], null, null, $isChase);
    					$res['multi'] = $order['multi'];
    					$res['ticketStatus'] = $this->lotterydetail->getTicketStatus($order['status']);
    					$res['bonusStatus'] = '---';
    					$ticketDetail[] = $res;
    				}
    			}
    			else
    			{
    				//出票明细
    				$splitOrders = $this->Order->getNumOrderDetail($order['orderId'], $order['lid']);
                    // 乐善奖金
                    $order['lsDetail'] = array();
                    if($orderInfo['data']['lid'] == DLT && $orderInfo['data']['isChase'])
                    {
                        $order['lsDetail'] = $this->getLsDetail($order['orderId'], $order['lid']);
                    }
    				foreach ($splitOrders as $sorder) {
    					$codes = explode('^', $sorder['codes']);
    					foreach ($codes as $key => $code) {
    						if ($code !== '') {
    							$isChase = false;
    							if ($orderInfo['data']['lid'] == DLT && $orderInfo['data']['isChase']) $isChase = true;
    							$res = $this->lotterydetail->renderCode($code, $sorder['lid'], $sorder['playType'], $award['awardNumber'], $isChase);
    							if($sorder['status'] == '2000') {
    								if (count($codes) > 1) {
    									$res['bonus'] = $this->lotterydetail->getBonus($key, $sorder, $award);
    								}else {
    									$res['bonus'] = $sorder['bonus'] - $sorder['otherBonus'];
                                        // 乐善奖金
                                        if(!empty($order['lsDetail']))
                                        {
                                            $lsMargin = $order['lsDetail']['detail'][$sorder['sub_order_id']]['margin'] ? $order['lsDetail']['detail'][$sorder['sub_order_id']]['margin'] : 0;
                                            $res['bonus'] = $res['bonus'] - $lsMargin;
                                        }
    								}
    							}
    							$res['multi'] = $sorder['multi'];
    							$res['ticketStatus'] = $this->lotterydetail->getTicketStatus($sorder['status']);
    							$res['bonusStatus'] = $this->lotterydetail->getTicketBonus($sorder['status'], $res['bonus']);
    							$ticketDetail[] = $res;
    						}
    					}
    					$otherBonus += $sorder['otherBonus'];
    				}
    			}
                // 出票时间
                $order['ticket_time'] = '';
                if(!empty($splitOrders))
                {
                    foreach ($splitOrders as $items) 
                    {
                        // 最晚出票时间
                        if($items['status'] >= 500)
                        {
                            if(empty($order['ticket_time']) || (!empty($order['ticket_time']) && $order['ticket_time'] <= $items['ticket_time']))
                            {
                                $order['ticket_time'] = $items['ticket_time'];
                            }
                        }
                    }
                }
    			$this->display('order/detail', array(
    				'uname' => $this->uname,
    				'lotteryId' => $order['lid'],
    				'cnName' => $this->Lottery->getCnName($order['lid']),
    				'enName' => $this->Lottery->getEnName($order['lid']),
    				'order' => $order,
    				'info' => $info,
    				'award' => $awardNum,
    				'topBanner' => 'account',
    				'orderId' => trim($orderId),
    				'playType' => $order['playType'],
    				'isChase' => $order['isChase'],
                    'ticketDetail' => $ticketDetail,
    				'otherBonus' => $otherBonus,
    				'tzjqurl' => '/academy',
    			), 'v1.1');
    		}
    	}
    	else
    	{
    		$this->redirect('/error/');
    	}
    }

    private function jcDetail($order) 
    {
        $award = $this->Order->getJjcMatchDetail($order['lid'], $order['codecc']);
        $TYPE_MAP = array();
        if( in_array($order['lid'], array(JCZQ, GJ, GYJ)))
        {
        	$TYPE_MAP = $this->JCZQ_TYPE_MAP;
        }
        else if( $order['lid'] == JCLQ )
        {
        	$TYPE_MAP = $this->JCLQ_TYPE_MAP;
        }  
        $bonusOpt = array();
        //出票成功 显示订单详情
        if (in_array($order['lid'], array(JCZQ, JCLQ))) 
        {
        	if($order['status'] >=500 && $order['status'] != 600)
        	{
        		//查询拆单详情
        		$orderDetail = $this->orderDetail($order['orderId'], $award);
        		if($order['playType'] == '7')
        		{
        			$bonusOpt = $this->bonusOpt($order['orderId'], $award);
        		}
        	}
        }
        else 
        {
        	$orderDetail = $this->gjorderDetail($order);
        }

        // 加奖
        $order['add_money'] = $this->getJjActivity($order);
        // 出票时间
        $order['ticket_time'] = $orderDetail['ticketTime'];
        
        $data = array(
            'uname' => $this->uname,
            'lotteryId' => $order['lid'],
            'cnName' => $this->Lottery->getCnName($order['lid']),
            'enName' => $this->Lottery->getEnName($order['lid']),
            'order' => $order,
            'award' => $award,
            'typeMAP' => $TYPE_MAP,
        	'playType' => $order['playType'],
            'topBanner' => 'account',
            'orderDetail' => $orderDetail,
        	'bonusOpt' => $bonusOpt
        );
        if($order['playType'] == '6')
        {
        	$this->display('order/dg_detail', $data, 'v1.1');
        }
        elseif ($order['playType'] == '7')
        {
        	$this->display('order/yh_detail', $data, 'v1.1');
        }
        else
        {
        	$this->display('order/jc_detail', $data, 'v1.1');
        }
    }
    
    public function gjorderDetail($order) {
    	preg_match_all('/(\d+)\((\d+\.*\d*)\)/', $order['codes'], $matches);
    	if (!empty($matches[1])) {
    	    $statusArr = array(
    	        44 => array('---', '出局', '夺冠', '---'),
    	        45 => array('---', '出局', '晋级决赛', '---')
    	    );
    		if (in_array($order['status'], array(500, 510, 1000, 1010, 2000))) $orderDetail = $this->Order->getGjOrderDetail($order['orderId'], $order['status']);
    		$res = $this->Order->getGjDetail($matches[1], $order['lid']);
    		if (isset($orderDetail)) $pDetail = json_decode($orderDetail[0]['pdetail'], true);
    		foreach ($res as $val) {
    			$odres[$val['mid']] = $val;
    		}
    		$strfa = '';
    		$info = array();
    		foreach ($matches[1] as $k => $val) {
    			$tdStr = '<td>';
    			if ($odres[intval($val)]['status'] == 2) $tdStr = "<td class='main-color-s'>";
    			$strfa .= "<tr>".$tdStr.$val."</td>".$tdStr.$odres[intval($val)]['name'].(isset($pDetail) ? "/".$pDetail[$val] : '')."</td>".$tdStr.$statusArr[$order['lid']][$odres[intval($val)]['status']]."</td></tr>";
    			$info[] = array(
    				'mid'  => $val,
    				'name' => $odres[intval($val)]['name'],
    				'status' => $odres[intval($val)]['status']
    			);
    		}
    	}
    	return array($strfa, 'detail' => $orderDetail, 'info' => $info, 'pDetail' => $pDetail);
    }
    
    /*
     * 【老足彩 -- 胜负彩/任选九】订单详情 控制器
     * @author:liuli
     * @date:2015-02-03
     */
    private function lzcController($lotteryId, $order)
    {
        $this->load->model('award_model', 'Award');
        //# 获知档期彩票中奖号码
        $award = $this->Order->getSfcAward($order['issue']);
        //获取比赛对阵信息
        $matchInfo = $this->Order->getSfcMatchs($order['issue']);
        //获取订单投注信息
        $betInfo = $this->splitBetStr($order['lid'],$order['codes']);
        //获取开奖内容
        $awardNumber = explode(',', $award['awardNumber']);
        //方案与开奖结果对比
        $betArr = array();
        foreach ($betInfo as $value)
        {
        	$betStr = array();
        	foreach ($value as $key => $val)
        	{
        		//单场选择结果
        		$bet1 = $this->strToAry($val);
        		rsort($bet1);
        		$betArray = array();
        		foreach ($bet1 as $k => $v)
        		{
        			if(!empty($awardNumber))
        			{
        				if($v == $awardNumber[$key])
        				{
        					$betArray[$k]['is_win'] = 1;
        				}
        				else
        				{
        					$betArray[$k]['is_win'] = 0;
        				}
        			}
        			else
        			{
        				$betArray[$k]['is_win'] = 0;
        			}
        			$betArray[$k]['bet'] = $v;
        		}
        		$betStr[$key] = $betArray;
        	}
        	array_push($betArr, $betStr);
        }

        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $info = $this->Lottery->getKjinfo($order['lid']);
        $this->load->library('LotteryDetail');
        $awardNum = $this->lotterydetail->renderAward($award['awardNumber']);
        //出票明细
        $splitOrders = $this->Order->getNumOrderDetail($order['orderId'], $order['lid']);
        $ticketDetail = $this->parselzcOrder($splitOrders, $awardNumber);
        // 出票时间
        $order['ticket_time'] = '';
        if(!empty($splitOrders))
        {
            foreach ($splitOrders as $items) 
            {
                // 最晚出票时间
                if($items['status'] >= 500)
                {
                    if(empty($order['ticket_time']) || (!empty($order['ticket_time']) && $order['ticket_time'] <= $items['ticket_time']))
                    {
                        $order['ticket_time'] = $items['ticket_time'];
                    }
                }
            }
        }
        $this->display('order/lzc_detail', array(
            'uname' => $this->uname,
            'lotteryId' => $order['lid'],
            'cnName' => $this->Lottery->getCnName($order['lid']),
            'enName' => $this->Lottery->getEnName($order['lid']),
            'order' => $order,
            'award' => $awardNum,
            'topBanner' => 'account',
            'orderId' => trim($order['orderId']),
            'playType' => $order['playType'],
            'matchInfo' => $matchInfo,               //比赛赛事信息
            'betArr' => $betArr,
        	'info' => $info,
            'ticketDetail' => $ticketDetail            
        ), 'v1.1');
    }
    
    /**
     * 解析订单
     * @param unknown_type $orders
     * @param unknown_type $awardNumber
     */
    private function parselzcOrder($orders, $awardNumber)
    {
    	$result = array();
    	if($orders)
    	{
    		foreach ($orders as $order)
    		{
    			$order['ticketInfo'] = $this->renderlzcCast($order, $awardNumber);
    			$order['ticketStatus'] = $this->lotterydetail->getTicketStatus($order['status']);
    			$order['bonusStatus'] = $this->lotterydetail->getTicketBonus($order['status'], $order['bonus']);
    			array_push($result, $order);
    		}
    	}
    	
    	return $result;
    }
    
    /**
     * 解析老足彩投注串
     */
    private function renderlzcCast($order, $award)
    {
    	$tpl = '';
    	$code = str_replace('4', '-', $order['codes']);
    	$codes = explode('*', $code);
    	foreach ($codes as $key => $number)
    	{
    		if($tpl != '')
    		{
    			$tpl .= ',';
    		}
    		$num = explode(',', $number);
    		$number = str_replace(',', '', $number);
    		if(in_array($award[$key], $num))
    		{
    			$number = str_replace($award[$key], '<span style="color:red;">' . $award[$key] . '</span>', $number);
    		}
    		$tpl .= $this->lotterydetail->renderGrayDetail($number);
    	}
    	
    	return $tpl;
    }

    /*
     * 投注串拆分
     * @author:liuli
     * @date:2015-02-03
     */
    public function splitBetStr($lid, $codes)
    {
        $data = array();
        //胜负彩
        if($lid == '11' || $lid == '19')
        {
        	$codes = explode(';', $codes);
        	foreach ($codes as $code)
        	{
        		$codesStr = explode(':',$code,2);
        		$betStr = str_replace('#', '-', $codesStr[0]);
        		$tmp = explode(',',$betStr);
        		array_push($data, $tmp);
        	}
        }
        return $data;
    }

    /*
     * 字符串拆分成数组
     * @author:liuli
     * @date:2015-02-03
     */
    public function strToAry($str,$charset='utf8') {
        $strlen = mb_strlen($str);
        $array = array();
        for($i=0;$i<$strlen;$i++){
            $array[$i] = mb_substr($str,$i,1,$charset);
        }
        return $array;
    }

    /*
     * 竞彩足球出票拆单明细
     * @author:shigx
     * @date:2015-06-09
     */
    public function orderDetail($orderId, $award)
    {
        // 获取对阵信息
        $matchData = $this->getMatchData($award);
        $orderDetail = $this->Order->getJjcOrderDetail($orderId);
        $counts = 0;
        $orders = array();
        // 出票赔率汇总
        $ticketData = array();
        // 出票时间
        $ticketTime = '';
        $this->load->config('wenan');
        $this->wenan = $this->config->item('wenan');
        if(!empty($orderDetail))
        {
            $this->load->config('order');
            $orderStatus = $this->config->item("cfg_orders");
            //统计总方案数
            $counts = count($orderDetail);
            $key = 1;
            $this->load->library('LotteryDetail');
            $playTypes = $this->jjcPlayType();
            foreach ($orderDetail as $detail) 
            {
            	$orderInfo = array();
                $orderInfo['id'] = $key++;
                // 场次、过关方式从【ticketMix】拆分获取
                $ticketInfo = explode('|', $detail['codes']);
                $ticketDetail = $this->ticketMix($ticketInfo[0],$detail['info'],$matchData);
                // 汇总出票信息
                $ticketData = $this->recordTicketInfo($ticketData, $ticketDetail['ticketData']);
                $orderInfo['matchInfo'] = $ticketDetail['detailInfo'];
                if($detail['playType'] == 53){
                    //自由过关设置过关方式
                    $orderInfo['type'] = '自由过关';
                }else{
                    $orderInfo['type'] = $this->getType($playTypes[$detail['playType']]? $playTypes[$detail['playType']] : count($detail['info']).'*1');
                }
                $orderInfo['pourNum'] = $detail['betTnum'];
                $orderInfo['multis'] = $detail['multi'];
                $orderInfo['money'] = $detail['money'];
                $orderInfo['bonus'] = $detail['bonus'];
                $orderInfo['status'] = $detail['status'];
                $orderInfo['ticketStatus'] = $this->lotterydetail->getTicketStatus($detail, $orderStatus);
                $orderInfo['bonusStatus'] = $this->lotterydetail->getTicketBonus($detail, $orderStatus);

                array_push($orders, $orderInfo);

                // 最晚出票时间
                if($detail['status'] >= 500)
                {
                    if(empty($ticketTime) || (!empty($ticketTime) && $ticketTime <= $detail['ticket_time']))
                    {
                        $ticketTime = $detail['ticket_time'];
                    }
                }
            }
        }
        $orders=array(
            'detail' => $orders,
            'counts' => $counts,
            'ticketData' => $ticketData,
            'ticketTime' => $ticketTime
        );
        return $orders;
    }

    public function ticketMix($ticket,$info,$matchData)
    {
        $detailInfo = '';
        $ticketData = array();
        $ticketInfo = explode('*', $ticket);
        $count = count($ticketInfo);
        foreach ($ticketInfo as $k_ticket => $v_ticket)
        {
        	$ticketDetail = explode(',', $v_ticket);
        	$resDetail = $this->ticketDetail($ticketDetail[0],$ticketDetail[1],$ticketDetail[2], $info[$ticketDetail[0]], $matchData);
            $detailInfo .= $resDetail['detail'];
            // 赔率信息
            $ticketData[$ticketDetail[0]][$ticketDetail[1]][] = $resDetail['ticketInfo'];
            if($k_ticket < $count - 1)
            {
            	$detailInfo .= ' X ';
            }
        }
        return array('detailInfo' => $detailInfo, 'ticketData' => $ticketData) ;
    }

    public function ticketDetail($mid, $playType, $tickets, $info, $matchData)
    {
        // 出票盘口及赔率信息
        $ticketInfo = array();
        $matchDetail = '';
        //胜平负
        $spf = array(
            '0' => $this->wenan['jzspf']['0'],
            '1' => $this->wenan['jzspf']['1'],
            '3' => $this->wenan['jzspf']['3']
        );
        
        $rqspf = array(
            '0' => $this->wenan['jzspf']['r0'],
            '1' => $this->wenan['jzspf']['r1'],
            '3' => $this->wenan['jzspf']['r3']
        );
        $sf = array(
    	    '0' => $this->wenan['jlsf']['0'],
    		'3' => $this->wenan['jlsf']['3']
    	);
    	$rfsf = array(
    	    '0' => $this->wenan['jlsf']['r0'],
    		'3' => $this->wenan['jlsf']['r3']
    	);
        //大小分
        $dxf = array(
            '0' => '小分',
            '3' => '大分'
        );
    	
        //胜分差
        $sfc = array(
            '01' => $this->wenan['jlsf']['3']."1-5分",
    		'02' => $this->wenan['jlsf']['3']."6-10分",
    		'03' => $this->wenan['jlsf']['3']."11-15分",
    		'04' => $this->wenan['jlsf']['3']."16-20分",
    		'05' => $this->wenan['jlsf']['3']."21-25分",
    		'06' => $this->wenan['jlsf']['3']."26+分",
    		'11' => $this->wenan['jlsf']['0']."1-5分",
    		'12' => $this->wenan['jlsf']['0']."6-10分",
    		'13' => $this->wenan['jlsf']['0']."11-15分",
    		'14' => $this->wenan['jlsf']['0']."16-20分",
    		'15' => $this->wenan['jlsf']['0']."21-25分",
    		'16' => $this->wenan['jlsf']['0']."26+分",
        );
        //获取赛事日期
        $date = substr($mid, 0,4).'-'.substr($mid, 4,2).'-'.substr($mid, 6,2);
        //拆分获取 日期 + 赛事编号
        $weekarray = array("日","一","二","三","四","五","六");
        $matchDetail = "<b";
        // 新增对阵信息
        if(!empty($matchData[$mid]))
        {
            $matchDetail .= " class='bubble-tip' tiptext='";
            if(in_array($playType, array('RFSF', 'SF', 'DXF', 'SFC')))
            {
                $matchDetail .= $matchData[$mid]['awary'] . " VS " . $matchData[$mid]['home'];
            }
            else
            {
                $matchDetail .= $matchData[$mid]['home'] . " VS " . $matchData[$mid]['awary'];
            }
            $matchDetail .= "'";
        }
        $matchDetail .= "'>周".$weekarray[date("w",strtotime($date))];
        $matchDetail .= substr($mid, 8).'</b>';
        $matchDetail .= '(';
        
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
                    $matchDetail .= $spf[$matches[1]] . ':' . $pl . ' ';
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
                	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info["letVs"]["v{$matches[1]}"][0];
                	$matchDetail .= $rqspf[$matches[1]]. '[' . $pk . ']' .':' . $pl . ' ';
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
                    $matchDetail .= $bf.':' . $pl . ' ';
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
                    $matchDetail .= $matches[1] . ':' . $pl . ' ';
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
                    $spfInfo = explode('-', $matches[1]);
                    // 出票盘口及赔率
                    $pk = '-';
                    $pl = $info["half"]["v$spfInfo[0]$spfInfo[1]"][0];
                    $matchDetail .= $spf[$spfInfo[0]] . '-'. $spf[$spfInfo[1]] . ':' . $pl . ' ';
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
                   	preg_match('/^(\d+)(?:{(.*?)})?\(.*?\)$/is', $vBet, $matches);
                    // 出票盘口及赔率
                    $pk = $info['letVs']['letPoint'][0] > 0 ? '+' . $info['letVs']['letPoint'][0] : $info['letVs']['letPoint'][0];
                    $pl = $info['letVs']["v$matches[1]"][0];
                    $matchDetail .= $rfsf[$matches[1]] . '[' . $pk . ']' . ':' . $pl . ' ';
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
                    $matchDetail .= $sf[$matches[1]] . ':' . $pl . ' ';
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
                    $matchDetail .= $dxf[$matches[1]] . '[' . $pk . ']' . ':' . $pl;
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
                    $pl = $info['diff']["v$matches[1]"][0];      
                    $matchDetail .= $sfc[$matches[1]] . ':' . $pl;
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
        
        $matchDetail .= ')';
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
    
    /**
     * 奖金优化数据
     * @param unknown_type $orderId
     * @param unknown_type $matchs
     */
    private function bonusOpt($orderId, $matchs)
    {
    	$data = array();
    	$split = $this->Order->getBonusOptDetail($orderId);
    	$matchInfo = array();
    	foreach ($matchs as $match)
    	{
    		$matchInfo[$match['mid']] = $match;
    	}
    	$preg = array(
    		'42' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?/is',
    		'43' => '/([\d\-\:]*)(?:\{(.*)\})?\(?(\d*\.?\d*)?\)?(?:\{(.*)\})?/is'
    	);
    	//胜平负
    	$spf = array(
    		'0' => $this->wenan['jzspf']['0'],
    		'1' => $this->wenan['jzspf']['1'],
    		'3' => $this->wenan['jzspf']['3']
    	);
    	//胜平负
    	$rqspf = array(
    	    '0' => $this->wenan['jzspf']['r0'],
    		'1' => $this->wenan['jzspf']['r1'],
    		'3' => $this->wenan['jzspf']['r3']
    	);
    	$sf = array(
    	    '0' => $this->wenan['jlsf']['0'],
    		'3' => $this->wenan['jlsf']['3']
    	);
    	$rfsf = array(
    	    '0' => $this->wenan['jlsf']['r0'],
    		'3' => $this->wenan['jlsf']['r3']
    	);
    	//大小分
    	$dxf = array(
    	    '0' => '小分',
    	    '3' => '大分'
    	);
    	//胜分差
    	$sfc = array(
    		'01' => $this->wenan['jlsf']['3']."1-5分",
    		'02' => $this->wenan['jlsf']['3']."6-10分",
    		'03' => $this->wenan['jlsf']['3']."11-15分",
    		'04' => $this->wenan['jlsf']['3']."16-20分",
    		'05' => $this->wenan['jlsf']['3']."21-25分",
    		'06' => $this->wenan['jlsf']['3']."26+分",
    		'11' => $this->wenan['jlsf']['0']."1-5分",
    		'12' => $this->wenan['jlsf']['0']."6-10分",
    		'13' => $this->wenan['jlsf']['0']."11-15分",
    		'14' => $this->wenan['jlsf']['0']."16-20分",
    		'15' => $this->wenan['jlsf']['0']."21-25分",
    		'16' => $this->wenan['jlsf']['0']."26+分",
    	);
    	foreach ($split as $value)
    	{
    		$info = array();
    		$info['subCodeId'] = $value['subCodeId'];
    		$info['multi'] = $value['multi'];
    		$info['status'] = $value['status'];
    		$info['bonus'] = $value['bonus'];
    		$info['margin'] = $value['margin'];
    		$codes = explode('|', $value['codes']);
    		$codeArr = explode('*', $codes[0]);
    		$type = count($codeArr);
    		$info['type'] = $this->getType($type . '*1');
    		$singleMoney = 1;
    		$matchDetail = '';
    		foreach ($codeArr as $key => $val)
    		{
    			$cArr = explode(',', $val);
    			preg_match($preg[$value['lid']], $cArr['2'], $matches);
    			$cast = $matches[1];
    			$odd = floatval($matches[3]);
    			$singleMoney *= $odd;
    			$oddStr = '';
    			switch ($cArr['1'])
    			{
    				case 'SPF':
    				    $odd = $spf[$cast] . ': ' . $odd;
    				    break;
    				case 'SF':
    					$odd = $spf[$cast] . ': ' . $odd;
    					break;
    				case 'RQSPF':
    				    $odd = $rqspf[$cast] . ': ' . $odd;
    				    break;
    				case 'RFSF':
    					$odd = $rfsf[$cast] . ': ' . $odd;
    					break;
    				case 'CBF':
    					$bf = $this->getCbf($cast);
    					$odd = $bf.': ' . $odd;
    					break;
    				case 'BQC':
    				case 'JQS':
    					if($cast == 7)
    					{
    						$cast = '7+';
    					}
    					$odd = $cast . ': ' . $odd;
    					break;
    				case 'DXF':
    					$odd = $dxf[$cast] . ': ' . $odd;
    					break;
    				case 'SFC':
    					$odd = $sfc[$cast] . ': ' . $odd;
    			}
    			$matchDetail .=  "<b>{$matchInfo[$cArr['0']]['home']}</b>({$odd})";
    			if($key < $type - 1)
    			{
    				$matchDetail .= ' X ';
    			}
    		}
    		$info['detail'] = $matchDetail;
    		$info['singleMoney'] = $singleMoney * 2 * 100;
    		$info['money'] = $singleMoney * 2 * $value['multi'] * 100;
    		array_push($data, $info);
    	}
    	
    	return $data;
    }

    public function getJjActivity($order)
    {
        $add_money = 0;
        if(($order['activity_ids'] & 4) == 4)
        {
            // 查询订单加奖金额
            $detail = $this->Order->getJjDetail($order['orderId']);

            if(!empty($detail))
            {
                $add_money = $detail['add_money'];
            }
        }
        return $add_money;
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

    // 汇总对阵信息
    public function getMatchData($award)
    {
        $matchData = array();
        if(!empty($award))
        {
            foreach ($award as $key => $items) 
            {
                $matchData[$items['mid']]['home'] = $items['home'];
                $matchData[$items['mid']]['awary'] = $items['awary'];
            }
        }
        return $matchData;
    }
    
    /**
     * 竞技彩串关定义
     */
    private function jjcPlayType()
    {
    	return array(
    		'1' => '1*1',
    		'2' => '2*1',
    		'3' => '3*1',
    		'4' => '4*1',
    		'5' => '5*1',
    		'6' => '6*1',
    		'7' => '7*1',
    		'8' => '8*1',
    		'9' => '9*1',
    		'10' => '10*1',
    		'11' => '11*1',
    		'12' => '12*1',
    		'13' => '13*1',
    		'14' => '14*1',
    		'15' => '15*1',
    		'16' => '2*3',
    		'17' => '3*3',
    		'18' => '3*4',
    		'19' => '3*7',
    		'20' => '4*4',
    		'21' => '4*5',
    		'22' => '4*6',
    		'23' => '4*11',
    		'24' => '4*15',
    		'25' => '5*5',
    		'26' => '5*6',
    		'27' => '5*10',
    		'28' => '5*16',
    		'29' => '5*20',
    		'30' => '5*26',
    		'31' => '5*31',
    		'32' => '6*6',
    		'33' => '6*7',
    		'34' => '6*15',
    		'35' => '6*20',
    		'36' => '6*22',
    		'37' => '6*35',
    		'38' => '6*42',
    		'39' => '6*50',
    		'40' => '6*57',
    		'41' => '6*63',
    		'42' => '7*7',
    		'43' => '7*8',
    		'44' => '7*21',
    		'45' => '7*35',
    		'46' => '7*120',
    		'47' => '8*8',
    		'48' => '8*9',
    		'49' => '8*28',
    		'50' => '8*56',
    		'51' => '8*70',
    		'52' => '8*247',
    	);
    }
    
    public function createGendan()
    {
        $uid = $this->input->post('uid', true);
        $lid = $this->input->post('lid', true);
        $money = $this->input->post('money', true);
        $type = $this->input->post('type', true);
        $num = $this->input->post('num', true);
        $payType = $this->input->post('payType', true);
        $percent = $this->input->post('percent', true);        
        $max = $this->input->post('max', true);
        $check = true;
        // 账户是否注销
        if(isset($this->uinfo['userStatus']) && in_array($this->uinfo['userStatus'], array(1, 2)))
        {
            $check = FALSE;
            if($this->uinfo['userStatus'] == '1')
            {
                $response = array(
                    'code' => 100,
                    'msg'  => '您的登录已超时，请重新登录！',
                    'data' => array(),
                );
            }
            else
            {
                $response = array(
                    'code' => 100,
                    'msg'  => '您的账户已被冻结，如需解冻请联系客服。',
                    'data' => array(),
                );
            }
        }
        if(empty($this->uinfo['real_name']))
        {
            $check = FALSE;
            $response = array(
                'code' => 100,
                'msg'  => '您尚未进行实名认证，请刷新页面后重试。',
                'data' => array(),
            );
        }
        if($type == 0)
        {
            $totalMoney = $money * $num;
        }
        if($type == 1)
        {
            $money = $max;
            $totalMoney = $max * $num;
        }
        if($this->uid && $check)
        {
            $this->load->model('follow_order_model');
            $params = array(
                'uid' => $this->uid,
                'puid' => $uid,
                'lid' => $lid,
                'payType' => $payType - 1, // 0 预付款 1 实时付款
                'followType' => $type, // 0 按固定金额 1 按百分比
                'totalMoney' => $totalMoney,
                'buyMoney' => $money,
                'buyMoneyRate' => $percent,
                'buyMaxMoney' => $max,
                'followTotalTimes' => $num,
                'buyPlatform' => '0',
                'channel' => '0',
            );
            $res = $this->follow_order_model->createFollowOrder($params);
            if ($res['code'] == 200)
            {
                $this->load->library('BetCnName');
                $res['data']['remain_money'] = number_format(ParseUnit($this->uinfo['money'], 1), 2);
                $res['data']['issue'] = '';
                $res['data']['typeCnName'] = BetCnName::getCnName($res['data']['lid']);
                $res['data']['code'] = 0;
                $res['data']['orderId'] = $res['data']['followId'];
            }
            echo json_encode($res);
        } else {
            echo json_encode($response);
        }
    }

    // 乐善奖
    public function getLsDetail($orderId, $lid)
    {
        $lsDetail = array();
        $totalMargin = 0;
        $info = $this->Order->getLsDetail($orderId, $lid);
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
        $this->load->model('order_model', 'Order');
        //# 查出订单的情况
        $orderInfo = $this->Order->getOrder(array(
            'uid'       =>  $this->uid,
            'orderId'   =>  $orderId
        ));
        $ticketDetail = array();
        if(!empty($orderInfo['data']))
        {
            // 出票明细
            $this->load->library('LotteryDetail');
            $splitOrders = $this->Order->getLsDetail($orderId, $orderInfo['data']['lid']);
            if(!empty($splitOrders))
            {
                foreach ($splitOrders as $sorder) 
                {
                    if(empty($sorder['awardNum']))
                    {
                        continue;
                    }

                    $margin = $sorder['margin'] ? $sorder['margin'] : 0;
                    // 组装数据
                    $data = array(
                        'code'          =>  array(),
                        'awardNum'      =>  $this->lsAwardFormat($sorder['awardNum']),
                        'bonusStatus'   =>  $this->lotterydetail->getTicketBonus($sorder['status'], $margin),
                    );
                    $codes = explode('^', $sorder['codes']);
                    foreach ($codes as $key => $code) 
                    {
                        if ($code !== '') 
                        {
                            $isChase = false;
                            if ($orderInfo['data']['lid'] == DLT && $orderInfo['data']['isChase']) $isChase = true;
                            $sorder['awardNum'] = str_replace(array('|', '(', ')'), array(':', ':', ''), $sorder['awardNum']);
                            $res = $this->lotterydetail->renderCode($code, $sorder['lid'], $sorder['playType'], $sorder['awardNum'], $isChase);
                            $data['code'][] = $res['code'];
                        }
                    }
                    array_push($ticketDetail, $data);
                }
            }
            else
            {
                $this->redirect('/error/');
            }
        }
        else
        {
            $this->redirect('/error/');
        }

        $this->display('order/ls_detail', array(
            'ticketDetail' => $ticketDetail,
        ), 'v1.1');
    }

    public function lsAwardFormat($awardNum)
    {
        $span = '';
        $numArr = explode('|', $awardNum);
        if($numArr[0] && $numArr[1])
        {
            foreach (explode(',', $numArr[0]) as $num) 
            {
                $span .= '<span class="ball ball-red">';
                $span .= $num;
                $span .= '</span>';
            }
            foreach (explode(',', $numArr[1]) as $num) 
            {
                $span .= '<span class="ball ball-blue">';
                $span .= $num;
                $span .= '</span>';
            }
        }
        return $span;
    }
}
