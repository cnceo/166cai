<?php

/**
 * 票商 - 福牛牛
 */

include_once APPPATH . 'libraries/nusoap/lib/nusoap.php';
class ticket_funiuniu
{
	private $seller = 'funiuniu';
	private $pctype_map = array
	(
		'42'    => array('F', 'jczq'),
	);

	private $ptype_map = array
	(
		'SPF' => array('11', 1),
		'CBF' => array('12', 3),
		'JQS' => array('13', 1),
		'BQC' => array('14', 3),
		'RQSPF' => array('15', 1),
		'HH42' => array('16', 0),
	);

	private $ggtype_map = array
	(
		'1' => '0',
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
	
	private $CI;
	
	private $fnntob_sellerid;
	private $fnntob_secret;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->model('ticket_model');
		$this->CI->load->helper('string');
		$this->order_status = $this->CI->ticket_model->orderConfig('orders');
		$this->client = new nusoap_client($this->CI->config->item('fnntob_pji'), 'wsdl');
		$this->client->soap_defencoding = 'UTF-8';
        $this->client->decode_utf8 = false;
        $this->fnntob_sellerid = $this->CI->config->item('fnntob_sellerid');
        $this->fnntob_secret = $this->CI->config->item('fnntob_secret');
	}
	
	private function cmt_comm($service, $reqDatas, $datas = array())
	{
		$lid = 0;
		if(!empty($datas['lid'])) $lid = $datas['lid'];
		if(empty($datas['msgid']))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			$params['msgid'] = $UId;
			if($service == 'BetTicket')
			{
				$sub_order_ids = array();
				$orderids = $datas['oids'];
				foreach ($orderids as $oids => $subodis)
				{
					foreach ($subodis as $subodi)
					{
						array_push($sub_order_ids, $subodi);
					}
				}
				$this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 32);
			}
		}
		else 
		{
			$UId = $datas['msgid'];
		}

		/*请求前日志记录*/
		$pathTail = "funiuniu$service/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($service . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "funiuniu$service/$service");
		log_message('LOG', "{$LogHead}[REQ]: " . print_r($reqDatas, true), $pathTail);

		// 统一处理请求参数
		$reqDatas = $this->getReqDatas($reqDatas);
		$result = $this->client->call($service, $reqDatas, '', '', false, true);
		if($this->client->fault) 
		{
			log_message('LOG', $LogHead . print_r($result, true), "funiuniu$service/$service");
		}

		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . print_r($result, true), $pathTail);

		$xmlobj = simplexml_load_string($result['return']);
		if((string)$xmlobj->header->errorCode != '0' || empty($xmlobj))
		{
			if($datas['concel'] && $service == 'QueryTicket')
			{
				$this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
			}
			return ;
		}
	
		$rfun = "result_$service";
		return $this->$rfun($xmlobj, $datas);
	}

	private function getReqDatas($params = array())
	{
		$data = array();
		$i = 0;
		if(!empty($params))
		{
			foreach ($params as $key => $val) 
			{
				$data['arg' . $i] = $val;
				$i ++;
			}
		}
		return $data;
	}

	public function med_betting($orders)
	{
		if(!empty($orders))
		{
			$corders = array();
			foreach ($orders as $order)
			{
				$corders[$order['lid']][$order['issue']][] = $order;
			}
			foreach ($corders as $lid => $corder)
			{
				$mname = "lid_{$this->pctype_map[$lid][1]}";
				if(method_exists($this, $mname))
					call_user_func_array(array($this, $mname),array($corder));
			}
		}
	}
	
	private function lid_jczq($corders)
	{
		$this->lid_jjc($corders, '42');
	}
	
	// 竞技彩统一投注格式
	private function lid_jjc($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$reorders = array();
				foreach ($orders as $in => $order)
				{
					$codes = $order['codes'];
					$codess = explode('|', $codes);
					$betcbts = explode('*', $codess[0]);
					$codesData = array();
					$ptypes = array();
					foreach ($betcbts as $betcbt)
					{
						// 投注号码:对阵编号,投注号/投注号//对阵编号,投注号/投注号
						// 对阵编号:由F(足球)/B(篮球)+日期+编号组成
						$dtmp = array();
						$fields = explode(',', $betcbt);
						$dtmp['lid'] = $this->pctype_map[$lid][0];
						$dtmp['mid'] = $fields[0];
						$dtmp['ptype'] = $this->ptype_map[trim($fields[1])][0];
						$ptypes[] = $dtmp['ptype'];
						$plvs = explode('/', $fields[2]);
						if(!empty($plvs))
						{
							$newPlvs = array();
							foreach ($plvs as $plv)
							{
								$cde = substr($plv, 0, $this->ptype_map[trim($fields[1])][1]);
								if(trim($fields[1]) == 'BQC') $cde = str_replace('-', '_', $cde);
								if(trim($fields[1]) == 'CBF') $cde = str_replace(array('9:0', '9:9', '0:9'), array('4:3', '4:4', '3:4'), $cde);
								$newPlvs[] = $cde;
							}
							$dtmp['plvs'] = implode('/', $newPlvs);
						}
						$codesData[] = $dtmp;
					}
					$ptypes = array_unique($ptypes);
					$strArr = array();
					if(count($ptypes) > 1)
					{
						// 混合玩法
						$order['lotteryID'] = $this->ptype_map["HH" . $lid][0];
						foreach ($codesData as $val)
						{
							$strArr[] = $val['lid'] . $val['mid'] . ',' . $val['plvs'] . '-' . $val['ptype'];
						}
					}
					else
					{
						$order['lotteryID'] = $ptypes[0];
						foreach ($codesData as $val)
						{
							$strArr[] = $val['lid'] . $val['mid'] . ',' . $val['plvs'];
						}
					}

					if($order['playType'] == 53)
					{
						// 暂不支持
                        // $ggtype = implode(',', $this->get_ggtype($codess[1]));
                        $ggtype = '';
                    }
                    else
                    {
					    $ggtype = $this->ggtype_map[$order['playType']];
                    }
                    // 过关方式^投注号码
					$order['codes'] = $ggtype . '^' . implode('//', $strArr);
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bet_jjc($reorders, $lid);
				}
			}
		}
	}
	
	private function bet_jjc($orders, $lid)
	{
		if(!empty($orders))
		{
			$orderids = array(); 
			$messageid = NULL;
			$betOrders = array();
            $reqDatas = array();
            $codeStr = array();
            foreach ($orders as $order)
            {
            	$betOrders[$order['lotteryID']][] = $order;
            }

            // 单期次单玩法投注 足球全走混合玩法
            foreach ($betOrders as $lotteryID => $items) 
            {
            	// 最多100张
            	$newOrders = array_chunk($items, 100);
            	foreach ($newOrders as $items) 
            	{
            		$orderids = array(); 
					$messageid = NULL;
					$codeStr = array();
            		foreach ($items as $order) 
            		{
            			$orderids[$order['orderId']][] = $order['sub_order_id'];
						if(empty($messageid)) $messageid = $order['message_id'];
	            		// 代理商订单号#投注内容#注数#倍数#金额
	                	$codeStr[] = $order['sub_order_id'] . '#' . $order['codes'] . '#' . $order['betTnum'] . '#' . $order['multi'] . '#' . ParseUnit($order['money'], 1);
	            	}
	            	$reqData = array(
		                'agentID'       =>  $this->fnntob_sellerid,
		                'lotteryID'     =>  $lotteryID,
		                'issue'      	=>  '',
		                'msg'      		=>  implode('|', $codeStr),	
		            );
		            $reqData['sign'] = strtolower(md5($reqData['agentID'] . $reqData['lotteryID'] . $reqData['issue '] . $reqData['msg'] . $this->fnntob_secret));
		            $this->cmt_comm('BetTicket', $reqData, array('oids' => $orderids, 'lid' => $lid, 'msgid' => $messageid));
            	}
            }
		}
	}

	public function result_BetTicket($xmlobj, $params)
	{
		$orderid = array();
		$suborderid = array();
		foreach ($params['oids'] as $order => $suborder)
		{
			$orderid[] = $order;
			foreach ($suborder as $subid)
			{
				$suborderid[] = $subid;
			}
		}
		$result = (string)$xmlobj->header->errorCode;
		if($result == '0')
		{
			$errArr = array();
			$succ_ids = array();
			if(!empty($xmlobj->body->tickets))
			{
				$updatas['s_data'] = array();
				$updatas['d_data'] = array();
				foreach ($xmlobj->body->tickets->ticket as $ticket)
				{
					$res = (string)$ticket->errorCode;
					if($res == '0' || $res == '1019')
					{
						$succ_ids[] = (string)$ticket->ordersID;				
					}
					else
					{
						array_push($updatas['s_data'], '(?, ?, 0)');
						array_push($updatas['d_data'], (string)$ticket->ordersID);
						array_push($updatas['d_data'], $res);
						$errArr[$res] = $res;
					}
				}
			}

			if(!empty($updatas['s_data']))
			{
				$this->CI->ticket_model->ticket_fail_md($updatas, $params['lid']);
				$errnum = implode(',', $errArr);
				$msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$errnum} ,请及时处理。";
				$this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '福牛牛合作商提票报警');
			}
			if(!empty($succ_ids)) 
			{
				$updatas = array('sids' => $succ_ids);
				$this->CI->ticket_model->ticket_succ($updatas, $params['lid']);
			}
			return true;
		}
		else
		{
			$updatas = array('sids' => $suborderid, 'error' => $result);
			$this->CI->ticket_model->ticket_fail($updatas, $params['lid']);
			$msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '福牛牛合作商提票报警');
			return false;
		}
	}

	// 新订单中奖明细
	public function med_getTicketBonus($lid = 0)
	{
		// 票商不支持彩种处理
		if(empty($this->pctype_map[$lid]))
		{
			return false;
		}
		$handleCount = 0;
		if(in_array($lid, array(42)))
		{
			// 按单处理
			$handle = true;
			$tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
			while ($handle && !empty($tickets)) {
				$succNum = 0;
				foreach ($tickets as $ticket) {
					$this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
					$ticketRes = $this->med_QueryAwardTicket($ticket['message_id'], $ticket['lid']);
					if($ticketRes)
					{
						$succNum ++;
					}
				}
				if($succNum > 0)
				{
					$handleCount ++;
					// 继续循环
					$tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
				}
				else
				{
					// 退出循环
					$handle = false;
				}
			}
		}
		
		if($handleCount > 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function med_QueryAwardTicket($messageId, $lid) 
	{
	    $subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId, $lid);
	    if(!empty($subOrders)) {
	        $subids = implode(',', $subOrders);
	        $param = array(
	            'agentID' => $this->fnntob_sellerid,
	            'ordersID' => $subids,
	            'sign' => md5($this->fnntob_sellerid . $subids . $this->fnntob_secret),
	        );
	        
	        return $this->cmt_comm('QueryAwardTicket', $param, array('lid' => $lid, 'batch' => $messageId));
	    }
	}
	
	private function result_QueryAwardTicket($xmlobj, $params)
	{
	    $result = (string)$xmlobj->header->errorCode;
	    if($result == '0') {
	        $data['s_datas'] = array();
	        $data['d_datas'] = array();
	        $fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
	        foreach ($xmlobj->body->tickets->ticket as $row)
	        {
	            //3-中奖（未派奖），2-中奖（已派奖），1-未中奖，0-未开奖
	            $status = intval($row->awardStatus);
	            if(in_array($status, array('2', '1'))) {
	                $cpstate = 3;
	                array_push($data['s_datas'], '(?, ?, ?, ?, ?)');
	                array_push($data['d_datas'], "{$row->ordersID}");
	                $bonus_t = (float)$row->awardBets * 100;
	                $margin_t = (float)$row->taxAwardBets * 100;
	                array_push($data['d_datas'], $bonus_t);
	                array_push($data['d_datas'], $margin_t);
	                array_push($data['d_datas'], date('Y-m-d H:i:s'));
	                array_push($data['d_datas'], $cpstate);
	            }
	        }
	        if(!empty($data['s_datas']))
	        {
	            $this->CI->ticket_model->setTicketBonus($data, $params['lid']);
	            return true;
	        }
	    }
	    
	    return false;
	}
	
	/**
	 * 查询出票结果
	 * @param string $concel
	 * @param number $lid
	 */
	public function med_ticketResult($concel = false, $lid = 0) 
	{
	    $tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);
	    if(!empty($tickets)) {
	        foreach ($tickets as $ticket) {
	            $this->med_QueryTicket($ticket['message_id'], $ticket['lid'], $concel);
	        }
	    }
	}
	
	private function med_QueryTicket($message_id, $lid, $concel) {
	    $subOrders = $this->CI->ticket_model->getSubOrdersByMsg($message_id, $concel, $lid);
	    if(!empty($subOrders))
	    {
	        $subids = implode(',', $subOrders);
	        $param = array(
	            'agentID' => $this->fnntob_sellerid,
	            'ordersID' => $subids,
	            'sign' => md5($this->fnntob_sellerid . $subids . $this->fnntob_secret),
	        );
	        
	        $this->cmt_comm('QueryTicket', $param, array('concel' => $concel, 'lid' => $lid, 'batch' => $message_id));
	    }
	}
	
	/*
	 * 功能：福牛牛查询出票回调函数
	 * */
	private function result_QueryTicket($xmlobj, $data)
	{
	    $result = (string)$xmlobj->header->errorCode;
	    if($result == '0') {
	        $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
	        $datas['s_data'] = array();
	        $datas['d_data'] = array();
	        $datas['relation'] = array();
	        $datas['relationConcel'] = array();
	        $datas['concelIds'] = array();
	        foreach ($xmlobj->body->tickets->ticket as $ticket) {
	            $result = (string)$ticket->status;
	            $ticket_time = date('Y-m-d H:i:s');
	            $err_num = "{$result}";
	            if ($result == '2') {
	                //出票成功
	                $err_num = 0;
	                $status = $this->order_status['draw'];
	                $ticket_time = date('Y-m-d H:i:s', strtotime($ticket->printTime));
	            } elseif ($result == '-2') {
	                //退票
	                $status = $this->order_status['concel'];
	                //如果是过期主动查询时 应设置失败
	                if($data['concel'])
	                {
	                    array_push($datas['relationConcel'], (string)$ticket->ordersID);
	                }
	            } else {
	                //出票中
	                $status = $this->order_status['drawing'];
	                if($data['concel'])
	                {
	                    $status = $this->order_status['concel'];
	                    array_push($datas['relationConcel'], (string)$ticket->ordersID);
	                }
	            }
	            if($status == $this->order_status['drawing']) {
	                $ticket_time = 'date_add(now(), interval 30 minute)';
	                array_push($datas['s_data'], "(?, ?, ?, $ticket_time, ?)");
	                array_push($datas['d_data'], "{$ticket->ordersID}");
	                array_push($datas['d_data'], $err_num);
	                array_push($datas['d_data'], $status);
	                array_push($datas['d_data'], "{$ticket->ticketId}");
	            } else if(($status == $this->order_status['concel']) && empty($data['concel'])) {
	                $datas['concelIds'][] = (string)$ticket->ordersID;
	            } else {
	                array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
	                array_push($datas['d_data'], "{$ticket->ordersID}");
	                array_push($datas['d_data'], $err_num);
	                array_push($datas['d_data'], $status);
	                array_push($datas['d_data'], $ticket_time);
	                array_push($datas['d_data'], "{$ticket->ticketId}");
	            }
	            if(!empty($ticket->odds))
	                $datas['relation']["{$ticket->lotteryID}"]["{$ticket->ordersID}"] = (string)$ticket->odds;
	        }
	        $this->CI->load->model('api_funiuniu_model');
	        return $this->CI->api_funiuniu_model->saveResponse($fields, $datas, $data['lid']);
	    }
	}
}
