<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class ticket_shancai
{
	private $phone = '18621526831';
	private $id_card = '34128119861106923X';
	private $real_name = '刘书刚';
	private $seller = 'shancai';
	
	// 善彩彩种ID映射
	private $pctype_map = array
	(
		'51'    => array('51', 'ssq'),
	    '57'    => array('14001', 'jxks'),
	    '53'    => array('34001', 'ks'),
	);
	
	private $CI;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->model('ticket_model');
		$this->CI->load->helper('string');
		$this->order_status = $this->CI->ticket_model->orderConfig('orders');
	}

	// 公共POST请求
	private function cmt_comm($service, $reqData, $datas = array())
	{
		if(empty($datas['msgid']))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			$datas['msgid'] = $UId;
			if($service == 'bet')
			{
				$sub_order_ids = array();
				array_push($sub_order_ids, $datas['oids']['sub_order_id']);
				$this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 4);
			}
		}
		else 
		{
			$UId = $datas['msgid'];
		}

		$reqData['merid'] = $this->CI->config->item('sctob_sellerid');
		$reqData['service'] = $service;
		$reqData['timestamp'] = date('YmdHi', time());
		$reqData['digest'] = $this->getDigest($service, $reqData['timestamp']);
		
		/*请求前日志记录*/
		$pathTail = "shancai_$service/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($service . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "shancai_$service/$service");
		log_message('LOG', "{$LogHead}[REQ]: " . json_encode($reqData), $pathTail);
		$result = $this->CI->tools->request($this->CI->config->item('sctob_pji') . '!' . $service, $reqData);
		$resData = json_decode($result, true);
		if($this->CI->tools->recode != 200 || empty($resData))
		{
			if($datas['concel'] && in_array($service, array('queryOrder')))
			{
				$this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
			}
			return ;
		}
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
		$rfun = "result_$service";
		return $this->$rfun($resData, $datas);
	}

	private function getDigest($service, $timestamp)
    {
    	return md5($this->CI->config->item('sctob_secret') . $service . $timestamp . $this->CI->config->item('sctob_secret'));
    }

	// 提票
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
					call_user_func_array(array($this, $mname),array($corder, $lid));
			}
		}
	}

	// 双色球提票
	private function lid_ssq($corders, $lid)
	{
		$this->lid_number($corders, $lid);
	}

	// 数字彩提票
	private function lid_number($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					// 双色球玩法
					$playType = 1;
					// 方式
					$mode = 0;
					// 去除最后一个^
					$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
					$codes = explode('^', $order['codes']);
					$codestrs = array();
					if(empty($codes[1]) && strpos($order['codes'], '#'))
					{
						// 胆拖
						$betCode = str_replace(array('#'), array('$'), $order['codes']);
						$betCode = $this->getBetFormat($betCode, $playType, 5, $mode, $order['multi'], $order['betTnum'], ParseUnit($order['money'], 1));
						array_push($codestrs, $betCode);
					}
					elseif(!empty($codes[1]) || $order['betTnum'] == 1)
					{
						// 单式
						foreach ($codes as $code)
						{
							$betCode = $this->getBetFormat($code, $playType, 1, $mode, $order['multi'], 1, $order['multi'] * 2);
							array_push($codestrs, $betCode);
						}
					}
					elseif(empty($codes[1]) && $order['betTnum'] > 1)
					{
						// 复式
						$betCode = $this->getBetFormat($order['codes'], $playType, 2, $mode, $order['multi'], $order['betTnum'], ParseUnit($order['money'], 1));

						array_push($codestrs, $betCode);
					}
					$order['codes'] = implode($codestrs, ';');
					// 按split小单提票
					$re = $this->med_bet($order, $lid, $issue_tmp);
				}
			}
		}
	}

	// 投注号码样式
	private function getBetFormat($code, $playType, $betType, $mode, $multi, $betTnum, $money)
	{
		// 投注号码^ 玩法^ 投注方式^ 方式^ 倍数^ 注数^ 总金额（元）
		return $code . '^' . $playType . '^' . $betType . '^' . $mode . '^' . $multi . '^' . $betTnum . '^' . $money;
	}

	private function formatIssue($issue, $lid, $pre='')
	{
		$issue_format = array('23529' => 0, '51' => 0, '21406' => 0, '33' => 0, '52' => 0,
		'53' => 0, '35' => 0, '10022' => 0, '23528' => 0, '11' => 0, '19' => 0, '21406' => 0,
		'21407' => 0, '21408' => 0, '57' => 4);
		if(empty($pre))
		{
			return substr($issue, $issue_format[$lid]);
		}
		else
		{
			return "$pre$issue";
		}
	}

	// 组装提票参数
	private function med_bet($orders, $lid, $issue)
	{
		if(!empty($orders))
		{
			$messageid = NULL;
			if(empty($messageid)) $messageid = $orders['message_id'];
			$reqData = array(
				'merid'			=>	$this->CI->config->item('sctob_sellerid'),
				'agentOrderId'	=>	$orders['sub_order_id'],
				'cardType'		=>	1,
				'cardNumber'	=>	$this->id_card,
				'name'			=>	$this->real_name,
				'mobile'		=>	$this->phone,
				'lotteryCode'	=>	$this->pctype_map[$lid][0],
				'periodCode'	=>	$issue,
				'castcode'		=>	$orders['codes'],
				'isAdd'			=>	0,								// 大乐透有用
				'amount'		=>	ParseUnit($orders['money'], 1),	// 单位元
				'count'			=>	$orders['betTnum'] * $orders['multi'],
			); 	
			return $this->cmt_comm('bet', $reqData, array('oids' => $orders, 'lid' => $lid, 'msgid' => $messageid));
		}
	}
	
	private function med_queryWinRecordPageList($issue, $lid)
	{
		//先查一次看看是否开奖并计算页数
		$reqData = array(
			'lotteryCode'	=>	$this->pctype_map[$lid][0],
			'periodCode'	=>	$this->formatIssue($issue, $lid),
			'page'			=>	1,
			'pageSize'		=>	1,
		);
		
		$result = $this->cmt_comm('queryWinRecordPageList', $reqData, array('lid' => $lid));
		if($result['resultCode'] == '0000')
		{
			$succFlag = true;
			if($result['size'] != '0')
			{
				$pages = ceil($result['size'] / 200);
				for ($pg = 1; $pg <= $pages; $pg++)
				{
					$reBouns = $this->result_saveBonus($issue, $pg, $lid);
					if(!$reBouns)
					{
						$succFlag = false;
						break;
					}
				}
			}
			if($succFlag)
			{
				$this->CI->ticket_model->trans_start();
				$re = $this->CI->ticket_model->setIssueStatus($this->pctype_map[$lid][1], $issue, $this->seller);
				$re1 = $this->CI->ticket_model->setCpstate($this->pctype_map[$lid][1], $issue, $this->seller);
				if($re && $re1)
				{
					$this->CI->ticket_model->trans_complete();
					return true;
				}
				else
				{
					$this->CI->ticket_model->trans_rollback();
				}
			}
		}
		return false;
		// 更新拉取时间
		// $this->CI->ticket_model->setCdBonusTime($this->pctype_map[$lid][1], $issue, $this->seller);
	}
	
	private function result_saveBonus($issue, $pg, $lid)
	{
		$return = false;
		$reqData = array(
			'lotteryCode'	=>	$this->pctype_map[$lid][0],
			'periodCode'	=>	$this->formatIssue($issue, $lid),
			'page'			=>	$pg,
			'pageSize'		=>	200,
		);
		$result = $this->cmt_comm('queryWinRecordPageList', $reqData, array('lid' => $lid));
		if($result['resultCode'] == '0000')
		{
			$s_data = array();
			$d_data = array();
			$fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
			foreach ($result['list'] as $row)
			{
				array_push($s_data, '(?, ?, ?, ?, ?)');
				array_push($d_data, "{$row['agentOrderID']}");
				//无税前奖金 根据税后奖金粗略计算
				$bouns = $row['prizeType'] == '1' ? (ParseUnit($row['prizeMoney']) / 0.8) : ParseUnit($row['prizeMoney']);
				array_push($d_data, $bouns);
				array_push($d_data, ParseUnit($row['prizeMoney']));
				array_push($d_data, date('Y-m-d H:i:s'));
				array_push($d_data, 3);
			}
			if(!empty($s_data))
			{
				return $this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data, $lid);
			}
		}
		
		return $return;
	}

	// 同步请求提票回执
	private function result_bet($resData, $datas)
	{
		$orderid = array();
		$suborderid = array();

		$splitConcel = array();
		$lid = $datas['lid'];
		$result = (string)($resData['resultCode']);
		if($result == '0000' && !empty($resData['orderId']))
		{
			$fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'seller_order_id');
			$d_data = array();
			$enum = 0;
			$errArr = array();

			$status = $this->order_status['drawing'];
			array_push($d_data, "('{$datas['oids']['sub_order_id']}', '{$resData['resultCode']}', '$status', date_add(now(), interval 1 minute), '{$resData['orderId']}')");

			if(!empty($d_data))
			{
				$this->CI->ticket_model->trans_start();
				$res = $this->CI->ticket_model->ticket_succ_sc($fields, $d_data, $lid);
				
				if($res)
				{
					$this->CI->ticket_model->trans_complete();
				}
				else
				{
					$this->CI->ticket_model->trans_rollback();
				}
			}
			return true;
		}
		else
		{
			$this->CI->ticket_model->trans_start();
			$updatas = array('sids' => array($datas['oids']['sub_order_id']), 'error' => $result);
			$re1 = $this->CI->ticket_model->ticket_fail($updatas, $lid);
			$msg = "对messageId:{$datas['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $datas['msgid'], $msg, '善彩合作商提票报警');
			if($re1)
			{
				$this->CI->ticket_model->trans_complete();
			}
			else 
			{
				$this->CI->ticket_model->trans_rollback();
			}
			return false;
		}
	}

	// 主动查询出票结果
	public function med_ticketResult($concel = false, $lid = 0)
	{
		$tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);

		if(!empty($tickets))
		{
			foreach ($tickets as $ticket)
			{
				$this->med_queryOrder($ticket['message_id'], $ticket['seller_order_id'], $ticket['lid'], $concel);
			}
		}
	}

	private function med_queryOrder($message_id, $seller_order_id, $lid, $concel)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($message_id, $concel, $lid);

		if(!empty($subOrders))
		{
			foreach ($subOrders as $subOrder)
			{
				$reqData = array(
					'lotteryCode'	=>	$this->pctype_map[$lid][0],
					'agentOrderId'	=>	$subOrder,	
					'orderID'		=>	$seller_order_id,	
				);
				return $this->cmt_comm('queryOrder', $reqData, array('concel' => $concel, 'lid' => $lid, 'batch' => $message_id));
			}
		}
	}

	// 主动查询出票结果回执
	private function result_queryOrder($resData, $data)
	{
		// 非竞彩
		if($resData['resultCode'] == '0000')
		{
			$fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
    		$datas['s_data'] = array();
    		$datas['d_data'] = array();
    		$datas['concelIds'] = array();
    		$concel = $data['concel'];
    		$err_num = "6_{$resData['ticketStatus']}";
    		if($resData['ticketStatus'] == '1')   //出票成功
    		{
    			$err_num = 0;
    			$status = $this->order_status['draw'];
    		}
    		elseif($resData['ticketStatus'] == '-1')//出票失败
    		{
    			$datas['concelIds'][] = $resData['agentOrderId'];
    			$status = $this->order_status['concel'];
    		}
    		else//出票中
    		{
    			$status = $this->order_status['drawing'];
    			if($concel)
    			{
    				$status = $this->order_status['concel'];
    			}
    		}

    		if($status != $this->order_status['concel'] || $concel)
    		{
    			$ticket_time = $resData['ticketTime'];
    			array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
    			if($status == $this->order_status['drawing'])
    			{
    				// 出票中
    				$ticket_time = date('Y-m-d H:i:s', strtotime('+ 1 minute'));
    			}
    			array_push($datas['d_data'], "{$resData['agentOrderId']}");
    			array_push($datas['d_data'], $err_num);
    			array_push($datas['d_data'], $status);
    			array_push($datas['d_data'], "{$ticket_time}");
    			array_push($datas['d_data'], "{$resData['zxTicketID']}");
    		}
    		
    		$this->CI->load->model('api_shancai_model');
    		return $this->CI->api_shancai_model->saveResponse($fields, $datas, $data['lid']);
		}
	}


	//订单中奖明细
	public function med_ticketBonus($lid = 0)
	{
		$lids = array('51', '57');
		foreach ($lids as $mlid)
		{
			$issues = $this->CI->ticket_model->getIssuesForCpBonus($this->pctype_map[$mlid][1], $this->seller);
			if(!empty($issues))
			{
				foreach ($issues as $issue)
				{
					$this->med_queryWinRecordPageList($issue, $mlid);
				}
			}
		}
	}
	
	/**
	 * 返回拉取中奖订单结果
	 * @param unknown_type $resData
	 * @param unknown_type $datas
	 * @return unknown
	 */
	private function result_queryWinRecordPageList($resData, $datas)
	{
		return $resData;
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
		// 只有双色球
		$issues = $this->CI->ticket_model->getIssuesByCdBonus($this->pctype_map[$lid][1], $this->seller);
		if(!empty($issues))
		{
			foreach ($issues as $issue)
			{
				$splitIssue = $this->CI->ticket_model->paiqiToSplit($issue, $lid);
				$counts = $this->CI->ticket_model->countTicketDetailByLid($lid, $splitIssue, $this->seller);
				if($counts > 0)
				{
					$ticketRes = $this->med_queryWinRecordPageList($issue, $lid);
					if($ticketRes)
	                {
	                    $handleCount ++;
	                }
				}
				else
				{
					$this->CI->ticket_model->setIssueStatus($this->pctype_map[$lid][1], $issue, $this->seller);
				}	
			}
		}
		if($handleCount > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
	}
	
	public function met_getIssue($lid)
	{
	    $this->med_queryPeriodInfo($lid);
	}
	
	private function med_queryPeriodInfo($lid) {
	    $reqData = array(
	        'lotteryCode'  => $this->pctype_map[$lid][0],
	        'status'       => '0',
	        'size'         => '50',
	    );
	    return $this->cmt_comm('queryPeriodInfo', $reqData, array('lid' => $lid));
	}
	
	private function result_queryPeriodInfo($resData, $data) {
	    $issues = array();
	    foreach ($resData['list'] as $rdata) {
	        if ($data['lid'] != 57 || substr($rdata['periodCode'], -2) !== '85') {//非新11选5或者江西11选五非85期
	            $start = $rdata['startTime'];
	            $end = $rdata['stopTime'];
	            $issue = substr($start, 0, 4).$rdata['periodCode'];
	            $issues[$issue] = array(
	                'start' => substr($start, 0, 4)."-".substr($start, 4, 2)."-".substr($start, 6, 2)." ".substr($start, 8, 2).":".substr($start, 10, 2).":".substr($start, 12, 2),
	                'end'  => substr($end, 0, 4)."-".substr($end, 4, 2)."-".substr($end, 6, 2)." ".substr($end, 8, 2).":".substr($end, 10, 2).":".substr($end, 12, 2),
	            );
	            $issues[$issue]['end'] = date('Y-m-d H:i:s', strtotime($issues[$issue]['end']) + 60);
	        }
	    }
	    if ($data['lid'] == 57 && count($issues) == 1) {//针对新11选5预排两期
	        while (count($issues) < 3) {
	            $lIssue = $issues[$issue];
	            $issue++;
	            $issue = (string)$issue;
	            if ((int)substr($issue, -2) <= 84) {
	                $issues[$issue] = array(
	                    'start' => date('Y-m-d H:i:s', strtotime($lIssue['start']) + 600),
	                    'end'  => date('Y-m-d H:i:s', strtotime($lIssue['end']) + 600)
	                );
	            }
	        }
	    }
	    $this->CI->load->model('issue_model');
	    $this->CI->issue_model->compareIssue($data['lid'], $issues);
	}
}
