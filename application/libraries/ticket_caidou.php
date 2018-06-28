<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class ticket_caidou
{
	private $phone = '18621526831';
	private $id_card = '34128119861106923X';
	private $seller = 'caidou';
	
	private $pctype_map = array
	(
		'23529' => array('T001', 'dlt'),
		'23528' => array('F7', 'qlc'),
		'10022' => array('D7', 'qxc'),
		'21406' => array('C511', 'syxw'),
		'51'    => array('F001', 'ssq'),
		'33'    => array('D3', 'pls'),
		'35'    => array('D5', 'plw'),
		'42'    => array('3', 'jczq'),
		'43'    => array('0', 'jclq'),
		'52'    => array('F3', 'fc3d'),
		'11'    => array('D14', 'sfc'),
		'19'	=> array('D9', 'rj'),
		'44'	=> array('GJ', 'gj'),
		'45'	=> array('GYJ', 'gyj'),
		'21406' => array('56', 'syxw'),
		'21407' => array('54', 'jxsyxw'),
		'21408' => array('58', 'hbsyxw'),
	    '21421' => array('55', 'gdsyxw'),
		'53'    => array('SHKS', 'ks'),
                '54'    => array('57', 'klpk')
	);
	private $ptype_map = array
	(
		'RQSPF' => array('RSPF', 1),
		'SPF' => array('SPF', 1),
		'CBF' => array('CBF', 3),
		'JQS' => array('JQS', 1),
		'BQC' => array('BQC', 3),
		'HH42' => array('ZQHH', 0),
			
		'SF' => array('SF', 1),
		'RFSF' => array('RFSF', 1),
		'SFC' => array('SFC', 2),
		'DXF' => array('DXF', 1),
		'HH43' => array('LQHH', 0),
	);
	private $ggtype_map = array
	(
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
	
	private $jjc_map = array
	(
		'42'    => array('10002', '30'),
		'43'    => array('10003', '31'),
		'51'	=> array('10001', '01'),
		'52'	=> array('10001', '03'),
		'23528' => array('10001', '07'),
		'23529' => array('10001', '50'),
		'10022' => array('10001', '51'),
		'44'	=> array('10006', '98', 'GJ'), 
		'45'	=> array('10006', '99', 'GYJ'),
		'21406' => array('10001', '56'),
		'21407' => array('10001', '54'),
		'21408' => array('10001', '58'),
	    '21421' => array('10001', '55'),
		'53'    => array('10001', '66'),
		'11'    => array('10001', '80'),
		'19'    => array('10001', '81'),
		'35'    => array('10001', '52'),
		'33'    => array('10001', '53'),
                '54'    => array('10001', '57'),
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
	
	private function cmt_comm($mdid, $body, $datas)
	{
		$lid = 0;
		if(!empty($datas['lid'])) $lid = $datas['lid'];
		if(empty($datas['msgid']))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			$datas['msgid'] = $UId;
			if(in_array($mdid, array(10001, 10002, 10003, 10006)))
			{
				$sub_order_ids = array();
				foreach ($datas['orderids'] as $oids => $subodis)
				{
					foreach ($subodis as $subodi)
					{
						array_push($sub_order_ids, $subodi);
					}
				}
				$this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $lid, 2);
			}
		}
		else 
		{
			$UId = $datas['msgid'];
		}
		$header  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
		$header .= "<request>";
		$header .= "<head sid=\"$mdid\" agent=\"" . $this->CI->config->item('cdtob_sellerid') 
				.  "\" messageid=\"$UId\" timestamp=\"" . date('Y-md H:i:s') . "\" memo=\"$mdid\" />";
		$header .= "<body>";
		$header .= $body;
		$header .= "</body>";
		$header .= "</request>";
		$content['xml'] = $header;
		$content['sign'] = md5($header . $this->CI->config->item('cdtob_secret'));
		/*请求前日志记录*/
		$pathTail = "caidou$mdid/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "caidou$mdid/$mdid");
		log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
		
		$result = $this->CI->tools->request($this->CI->config->item('cdtob_pji'), $content, 20);
		if($this->CI->tools->recode != 200 || empty($result))
		{
			if($datas['concel'] && in_array($mdid, array('20008')))
			{
				$this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
			}
			return ;
		}
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . preg_replace('/[\n\r]+/is', '', $result), $pathTail);
		$xmlobj = simplexml_load_string($result) ;
		$rfun = "result_$mdid";
		return $this->$rfun($xmlobj, $datas);
		
	}
	
	//订单中奖明细
	public function med_ticketBonus($lid = 0)
	{
		if(in_array($lid, array(53, 21406, 21407, 21408, 21421, 54, 55)))
		{
			if($lid == 55)
			{	
				return ;
			}
			$issues = $this->CI->ticket_model->getIssuesForCpBonus($this->pctype_map[$lid][1], $this->seller);
			if(!empty($issues))
			{
				foreach ($issues as $issue)
				{
					$this->med_20004($issue, $lid);
				}
			}
		}
		else 
		{
			$lids = array('51', '52', '23528', '10022', '23529', '33', '35', '11', '19');
			foreach ($lids as $mlid)
			{
				$issues = $this->CI->ticket_model->getIssuesForCpBonus($this->pctype_map[$mlid][1], $this->seller);
				if(!empty($issues))
				{
					foreach ($issues as $issue)
					{
						$this->med_20004($issue, $mlid);
					}
				}
			}
			$tickets = $this->CI->ticket_model->getTicketBonus($this->seller, $lid);
			if(!empty($tickets))
			{
				foreach ($tickets as $ticket)
				{
					$this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
					$this->med_20009($ticket['message_id'], $ticket['lid']);
				}
			}
		}
	}
	
	public function med_kjResult($issue, $lid)
	{
		$this->med_20002($issue, $lid);
	}
	
	private function med_20002($issue, $lid)
	{
		// 快乐扑克期次处理
		$pissue = ($lid == 54) ? '20' . $issue : $issue;
		$body = "<query gid=\"{$this->jjc_map[$lid][1]}\" pid=\"{$pissue}\"/>";
		$this->cmt_comm('20002', $body, array('lid' => $lid, 'issue' => $issue, 'batch' => "{$lid}-$issue"));
	}
	
	/*
	 * 功能：查询彩豆出票结果
	 * 作者：huxm
	 * 日期：2016-03-09
	 * */
	public function med_ticketResult($concel = false, $lid = 0)
	{
		$tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);
		if(!empty($tickets))
		{
			foreach ($tickets as $ticket)
			{
				$this->med_20008($ticket['message_id'], $ticket['lid'], $concel);
			}
		}
	}
	
	private function med_20008($message_id, $lid, $concel)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($message_id, $concel, $lid);
		$nlid = $this->jjc_map[$lid][1];
		if(!empty($subOrders))
		{
			$apply = array();
			foreach ($subOrders as $subOrder)
			{
				array_push($apply, $subOrder);
			}
			$body = "<query gid=\"$nlid\" apply=\"" . implode(',', $apply) . "\"/>";
			$this->cmt_comm('20008', $body, array('concel' => $concel, 'lid' => $lid, 'batch' => $message_id));
		}
	}
	
	private function med_20009($messageId, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId, $lid);
		if(!empty($subOrders))
		{
			$apply = '';
			$body = '';
			foreach ($subOrders as $subOrder)
			{
				$apply .= "$subOrder,";
			}
			$apply = preg_replace('/,$/', '', $apply);
			$body .= "<query gid=\"{$this->jjc_map[$lid][1]}\" tid = \"\" apply=\"$apply\" />";
			
			return $this->cmt_comm('20009', $body, array('batch' => $messageId));
		}
	}
	
	private function med_20004($issue, $lid)
	{
		$lids = array('10022', '23529', '33', '35', '11', '19', '54', '21421');
		$oissue = $issue;
		if(in_array($lid, $lids))
		{
			$issue = $this->formatIssue($issue, $lid, '20');
		}
		$body = "<query gid=\"{$this->jjc_map[$lid][1]}\" pid=\"$issue\"/>";
		$result20004 = $this->cmt_comm('20004', $body, array('batch' => "{$lid}-$issue"));
		if($result20004 == 3)
		{
			$tpg = $this->cmt_comm('20005', $body, array('batch' => "{$lid}-$issue"));
			if($tpg['result'])
			{
				if($tpg['tpg'] >= 0)
				{
					for($pg = 1; $pg <= $tpg['tpg']; $pg++)
					{
						$re20006 = $this->med_20006($issue, $pg, $lid);
						if(!$re20006) break;
					}
				}
				if($pg > $tpg['tpg'])
				{
					$this->CI->ticket_model->trans_start();
					$re = $this->CI->ticket_model->setIssueStatus($this->pctype_map[$lid][1], $oissue, $this->seller);
                    $re1 = $this->CI->ticket_model->setCpstate($this->pctype_map[$lid][1], (in_array($lid, array(54, 21421)) ? $oissue : $issue), $this->seller);
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
		}
		//如果返回查无数据直接更新排期表
		if($result20004 == 1000)
		{
		    $this->CI->ticket_model->setIssueStatus($this->pctype_map[$lid][1], $oissue, $this->seller);
		    return false;
		}
		return false;
		// 更新拉取时间
		// $this->CI->ticket_model->setCdBonusTime($this->pctype_map[$lid][1], $oissue, $this->seller);
	}
	
	private function med_20006($issue, $pn, $lid)
	{
		$body = "<query gid=\"{$this->jjc_map[$lid][1]}\" pid=\"$issue\" pn=\"$pn\"/>";
		return $this->cmt_comm('20006', $body, array('lid' => $lid, 'batch' => "{$lid}-$issue"));
	}
	
	private function result_20006($xmlobj, $datas)
	{
		$s_data = array();
		$d_data = array();
		$fields = array('sub_order_id', 'message_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
		foreach ($xmlobj->body->rows->row as $row)
		{
			array_push($s_data, '(?, ?, ?, ?, ?, ?)');
			array_push($d_data, "{$row['apply']}");
			array_push($d_data, "{$row['bid']}");
			array_push($d_data, ParseUnit($row['bonus']));
			array_push($d_data, ParseUnit($row['tax']));
			array_push($d_data, date('Y-m-d H:i:s'));
			$cpstate = 3;
			if(in_array(intval($datas['lid']), array(11,19)))
			{
				$cpstate = 1;
			}
			array_push($d_data, $cpstate);
		}
		if(!empty($s_data))
		{
			return $this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data, $datas['lid']);
		}
	}
	
	private function result_20005($xmlobj, $data)
	{
		return array('result' => true, 'tpg' => intval($xmlobj->body->rows->row['tps']));
	}
	
	private function result_20004($xmlobj, $data)
	{
	    $result = (string)$xmlobj->result['code'];
	    if($result == '0')
	    {
	        return intval($xmlobj->body->rows->row['state']);
	    } 
	    else 
	    {
	        return $result;
	    }
	}
	/*
	 * 功能：彩豆查询出票回调函数
	 * 作者：huxm
	 * 日期：2016-03-09
	 * */
	private function result_20008($xmlobj, $data)
	{
		$result = (string)$xmlobj->result['code'];
		if($result == '0')
		{
			$fields = array('sub_order_id', 'error_num', 'status', 'ticket_money', 'ticket_time', 'ticketId');
	    	$datas['s_data'] = array();
			$datas['d_data'] = array();
			$datas['relation'] = array();
			$datas['relationConcel'] = array();
			$datas['concelIds'] = array();
			foreach ($xmlobj->body->rows->row as $ticket)
			{
	    		$result = intval($ticket['state']);
	    		$tcode  = intval($ticket['tcode']);
	    		$ticket_time = date('Y-m-d H:i:s');
	    		$err_num = "{$result}_{$tcode}";
	    		if(in_array($result, array(0, 1)))   //出票中
	    		{
	    			$status = $this->order_status['drawing'];
	    			if($data['concel'])
	    			{
	    				$status = $this->order_status['concel'];
	    				array_push($datas['relationConcel'], (string)$ticket['apply']);
	    			}
	    		}
	    		elseif($result == 2 && $tcode == 0)//出票成功
	    		{
	    			$err_num = 0;
	    			$status = $this->order_status['draw'];
	    			$ticket_time = "{$ticket['tdate']}";
	    		}
				elseif($result == 2 && in_array($tcode, array(1, 2))) //限号出票失败
	    		{
	    			$status = $this->order_status['concel'];
	    			//如果是过期主动查询时 应设置失败
	    			if($data['concel'])
	    			{
	    				array_push($datas['relationConcel'], (string)$ticket['apply']);
	    			}
	    		}
	    		if($status == $this->order_status['drawing'])
	    		{
	    			$ticket_time = 'date_add(now(), interval 1 minute)';
		    		array_push($datas['s_data'], "(?, ?, ?, ?, $ticket_time, ?)");
		    		array_push($datas['d_data'], "{$ticket['apply']}");
		    		array_push($datas['d_data'], "{$result}_{$tcode}");
		    		array_push($datas['d_data'], $status);
		    		array_push($datas['d_data'], intval($ticket['money']) * 100);
		    		array_push($datas['d_data'], "{$ticket['tid']}");
	    		}
	    		else if(($status == $this->order_status['concel']) && empty($data['concel']))
	    		{
	    			$datas['concelIds'][] = (string)$ticket['apply'];
	    		}
	    		else 
	    		{
	    			array_push($datas['s_data'], '(?, ?, ?, ?, ?, ?)');
		    		array_push($datas['d_data'], "{$ticket['apply']}");
		    		array_push($datas['d_data'], $err_num);
		    		array_push($datas['d_data'], $status);
		    		array_push($datas['d_data'], intval($ticket['money']) * 100);
		    		array_push($datas['d_data'], $ticket_time);
		    		array_push($datas['d_data'], "{$ticket['tid']}");
	    		}
	    		if(!empty($ticket['memo']))
	    			$datas['relation']["{$ticket['gid']}"]["{$ticket['apply']}"] = "{$ticket['memo']}";
			}
			$this->CI->load->model('api_caidou_model');
		    return $this->CI->api_caidou_model->saveResponse($fields, $datas, $data['lid']);
		}
	}
	
	private function result_20009($xmlobj, $data)
	{
		$s_data = array();
		$d_data = array();
		$fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
		foreach ($xmlobj->body->rows->row as $row)
		{
			$cpstate = intval($row['flag']);
			if($cpstate == 1)
			{
				//11选5的对比奖金拉去
				if(in_array(intval($row['gid']), array('54', '30', '31')))
				{
					$cpstate = 3;
				}
				array_push($s_data, '(?, ?, ?, ?, ?)');
				array_push($d_data, "{$row['apply']}");
				array_push($d_data, ParseUnit($row['bonus']));
				array_push($d_data, ParseUnit($row['tax']));
				array_push($d_data, date('Y-m-d H:i:s'));
				array_push($d_data, $cpstate);
			}
		}
		if(!empty($s_data))
		{
			$this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data);
			return true;
		}
		return false;
	}
	
	private function result_20002($xmlobj, $data)
	{
		$result = intval($xmlobj->result['code']);
		$issue = $data['issue'];
		$lname = $this->pctype_map[$data['lid']][1];
		if($result == 0)
		{
			$row = $xmlobj->body->rows->row[0];
			$awardNum = $row['awardcode'];
			if($awardNum != '')
			{
				// 快乐扑克号码处理
				if($data['lid'] == 54)
				{
					$awardNum = $this->awardNumFormat($awardNum);
				}
				$bonusDetail = $this->getBonusDetail($data['lid']);
				$lid = $data['lid'];
				$data = array($awardNum, json_encode($bonusDetail), $issue);
				$this->CI->ticket_model->updateByIssue($data, $lname);
				//启动同步号码任务
				$this->CI->ticket_model->updateStop(1, $lid, 0);
			}
		}
		
		$this->CI->ticket_model->updateTryNum($issue, $lname);
	}
	
	private function result_10001($xmlobj, $datas)
	{
		return $this->result_betting($xmlobj, $datas);
	}
	
	private function result_10002($xmlobj, $datas)
	{
		return $this->result_betting($xmlobj, $datas);
	}
	
	private function result_10003($xmlobj, $datas)
	{
		return $this->result_betting($xmlobj, $datas);
	}
	
	private function result_10006($xmlobj, $datas)
	{
		return $this->result_betting($xmlobj, $datas);
	}
	
	private function result_betting($xmlobj, $data)
	{
		$orderid = array();
		$suborderid = array();
		$splitConcel = array();
		$lid = $data['lid'];
		foreach ($data['orderids'] as $order => $suborder)
		{
			$orderid[] = $order;
			foreach ($suborder as $subid)
			{
				$suborderid[] = $subid;
			}
		}
		$messageId = (string)$xmlobj->head['messageid'];
		$result = intval($xmlobj->result['code']);
		if( $result == 0)
		{
			$fields = array('sub_order_id', 'error_num', 'status', 'ticket_time');
			$d_data = array();
			$enum = 0;
			$errArr = array();
			foreach ($xmlobj->body->tickets->ticket as $ticket)
			{
				$status = $this->order_status['drawing'];
				if(intval($ticket['code']) > 1)
				{
					$status = $this->order_status['split_ini'];
		    		array_push($splitConcel, (string)$ticket['apply']);
		    		array_push($errArr, intval($ticket['code']));
				}
				array_push($d_data, "('{$ticket['apply']}', '{$ticket['code']}', '$status', date_add(now(), interval 1 minute))");
			}
			if(!empty($d_data))
			{
				$this->CI->ticket_model->trans_start();
				$re1 = $this->CI->ticket_model->ticket_succ_cd($fields, $d_data, $lid);
				$re2 = true;
				if(!empty($splitConcel))
				{
		    		$re2 = $this->CI->ticket_model->order_split_concel($splitConcel, $lid);
		    		$errArr = array_unique($errArr);
		    		$errNum = implode(',', $errArr);
		    		$msg = "对messageId:{$messageId}进行提票操作时合作商 {$this->seller} 返回状态码：{$errNum} ,请及时处理。";
		    		$this->CI->ticket_model->insertAlert(4, $messageId, $msg, '彩豆合作商提票报警');
				}
				if($re1 && $re2)
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
			$updatas = array('oids' => $orderid, 'seller' => $this->seller, 'sids' => $suborderid, 
			'error' => $result);
			$re1 = $this->CI->ticket_model->ticket_fail($updatas, $lid);
			$msg = "对messageId:{$data['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $data['msgid'], $msg, '彩豆合作商提票报警');
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
	
	/**
	 * 冠军彩种
	 * @param unknown_type $corders
	 */
	private function lid_gj($corders)
	{
		$this->lid_gyj_comm($corders, '44');
	}
	
	/**
	 * 冠亚军操作
	 * @param unknown_type $corders
	 */
	private function lid_gyj($corders)
	{
		$this->lid_gyj_comm($corders, '45');
	}
	
	private function lid_gyj_comm($corders, $lid)
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
					$betcbts = explode('=', $codess[0]);
					$plvs = explode('/', $betcbts[1]);
					if(!empty($plvs))
					{
						$newPlvs = array();
						foreach ($plvs as $plv)
						{
							$cde = substr($plv, 0, 2);
							$newPlvs[] = $cde;
						}
						$plvs = implode('/', $newPlvs);
						$order['codes'] = $this->jjc_map[$lid][2] . '|' . $betcbts[0] . '=' . $plvs;
						array_push($reorders, $order);
					}
				}
				if(count($reorders))
				{
					$re = $this->bdy_10006($reorders, $lid, $issue);
				}
			}
		}
	}
	
	/*
	 * 对接彩豆的福彩3D彩种
	 * */
	private function lid_fc3d($corders)
	{
		$this->lid_fcsd($corders, '52');
	}
	
	/*
	 * 对接彩豆的福彩QLC彩种
	 * */
	private function lid_qlc($corders)
	{
		$this->lid_number($corders, '23528');
	}
	
	private function lid_ssq($corders)
	{
		$this->lid_number($corders, '51');
	}
	
	private function lid_jczq($corders)
	{
		$this->lid_jjc($corders, '42');
	}
	
	private function lid_jclq($corders)
	{
		$this->lid_jjc($corders, '43');
	}
	
	private function lid_syxw($corders)
	{
		$this->lid_number($corders, '21406');
	}
	
	private function lid_jxsyxw($corders)
	{
		$this->lid_number($corders, '21407');
	}
	
	private function lid_hbsyxw($corders)
	{
		$this->lid_number($corders, '21408');
	}
	
	private function lid_ks($corders)
	{
		$this->lid_kscom($corders, '53');
	}
	
        private function lid_klpk($corders)
	{
		$this->lid_klpkcom($corders, '54');
	}
        
	private function lid_sfc($corders)
	{
		$this->lid_tcomm($corders, '11');
	}
	
	private function lid_rj($corders)
	{
		$this->lid_tcomm($corders, '19');
	}
	
	private function lid_dlt($corders)
	{
		$this->lid_number($corders, '23529');
	}
	
	private function lid_qxc($corders)
	{
		$this->lid_number($corders, '10022');
	}
	
	private function lid_plw($corders)
	{
		$this->lid_number($corders, '35');
	}
	
	private function lid_pls($corders)
	{
		$this->lid_number($corders, '33');
	}
	
	private function lid_tcomm($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$count = 0;
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					$bettype = '1';
					if(strpos($order['codes'], ',') == true)
					{
						$bettype = 2;
					}
					$order['codes'] = str_replace(array(',', '*', '4'), array('', ',', '#'), $order['codes']) . 
					":1:$bettype";
					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
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
						$dtmp = array();
						$fields = explode(',', $betcbt);
						$dtmp['mid'] = substr($fields[0], 2);
						$dtmp['ptype'] = $this->ptype_map[trim($fields[1])][0];
						$ptypes[] = $dtmp['ptype'];
						$plvs = explode('/', $fields[2]);
						if(!empty($plvs))
						{
							$newPlvs = array();
							foreach ($plvs as $plv)
							{
								$cde = substr($plv, 0, $this->ptype_map[trim($fields[1])][1]);
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
						//混合玩法
						$codestr = $this->ptype_map["HH" . $lid][0]. "|";
						foreach ($codesData as $val)
						{
							$strArr[] = $val['mid'] . '>' . $val['ptype'] . '=' . $val['plvs'];
						}
					}
					else
					{
						$codestr = $ptypes[0] . "|";
						foreach ($codesData as $val)
						{
							$strArr[] = $val['mid'] . '=' . $val['plvs'];
						}
					}
					if($order['playType'] == 53){
                        $ggtype = implode(',', $this->get_ggtype($codess[1]));
                    }else{
					    $ggtype = $this->ggtype_map[$order['playType']];
                    }
					$order['codes'] = $codestr . implode(',', $strArr) . '|' . $ggtype;
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_jjc($reorders, $lid);
				}
			}
		}
	}

	private function get_ggtype($codess){
        $ggmaps = array(2, 3, 4, 5, 6, 7, 8);
        preg_match('/ZS=\d+,BS=\d+,JE=\d+,GG=(\d+)/is', $codess, $matches);
        $datas = array();
        foreach ($ggmaps as $ggmap){
            if($matches[1] & (1 << ($ggmap -2))){
                array_push($datas, $this->ggtype_map[$ggmap]);
            }
        }
        return $datas;
    }
	
	private function lid_fcsd($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
					$codes = explode('^', $order['codes']);
					$codestrs = array();
					if($order['playType'] == 1)
					{
						if(count($codes) == 1 && $order['betTnum'] > 1)
						{
							array_push($codestrs, str_replace(array(',', '*'), array('', ','), $codes[0]) . ":{$order['playType']}:2");
						}
						elseif(count($codes) > 1 || $order['betTnum'] == 1)
						{
							//单式
							foreach ($codes as $code)
							{
								array_push($codestrs, str_replace(array('*'), array(','), $code) . ":{$order['playType']}:1");
							}
						}
					}
					elseif(in_array($order['playType'], array(2, 3)))
					{
						if($order['betTnum'] > 1)
						{
							array_push($codestrs, $codes[0] . ":{$order['playType']}:3");
						}
						else 
						{
							array_push($codestrs, str_replace(array('*'), array(','), $codes[0]) . ":{$order['playType']}:1");
						}
					}
					$order['codes'] = implode($codestrs, ';');
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
	private function lid_kscom($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					switch ($order['playType'])
					{
						case 1:
							$cnum = intval($order['codes']) / 3;
							if(in_array($cnum, array(1, 6)))
							{
								$order['codes'] = "$cnum,$cnum,$cnum:3:1";
							}
							else 
							{
								$order['codes'] .= ":{$order['playType']}:4";
							}
							break;
						case 2:
						case 3:
						case 4:
						case 5:
							$order['codes'] .= ":{$order['playType']}:1";
							break;
						case 6:
							$betnums = array_map('trim', explode(',', $order['codes']));
							$onums = array('1', '2', '3', '4', '5', '6');
							$crsbnum = array_intersect($onums, $betnums);
							if(!empty($crsbnum))
							{
								$order['codes'] = implode(' ', $crsbnum) . ":{$order['playType']}:1";
							}
							break;
						case 7:
							$betnums = array_map('trim', explode(',', $order['codes']));
							$onums = array('1', '2', '3', '4', '5', '6');
							$crsbnum = array_intersect($onums, $betnums);
							if(count($crsbnum) == 2)
							{
						    	$vkcount = array_flip(array_count_values($betnums));
						    	krsort($vkcount);
								$order['codes'] = implode('|', $vkcount) . ":{$order['playType']}:1";
							}
							break;
						case 8:
							$betnums = array_map('trim', explode(',', $order['codes']));
							$onums = array('1', '2', '3', '4', '5', '6');
							$crsbnum = array_intersect($onums, $betnums);
							if(!empty($crsbnum))
							{
								$order['codes'] = implode(',', $crsbnum) . ":{$order['playType']}:1";
							}
							break;
						default:
							break;
					}
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
        private function lid_klpkcom($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid, '20');
				foreach ($orders as $in => $order)
				{
					switch ($order['playType'])
					{
                                                case $order['playType'] <= 6 && $order['playType'] >= 1:
                                                    if($order['betTnum'] > 1)
                                                    {
                                                        $codes = explode('^', $order['codes']);
                                                        if(isset($codes[1]))
                                                        {
                                                           $codestrs = array();
                                                           foreach ($codes as $code)
                                                           {
                                                               array_push($codestrs, $code . ':'.$order['playType'].':1');
                                                           }
                                                           $order['codes'] = implode($codestrs, ';');
                                                        }
                                                        else
                                                        {
                                                           $order['codes'] .= ":{$order['playType']}:2"; 
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $order['codes'] .= ":{$order['playType']}:1";
                                                    }
                                                    break;
                                                case $order['playType'] >= 7 && $order['playType'] <= 11:
                                                    if($order['codes']=='00')
                                                    {
                                                         $order['codes'] = sprintf("%02d",$order['playType']).":{$order['playType']}:3";
                                                    }
                                                    else
                                                    {
                                                        if($order['betTnum'] > 1)
                                                        {
                                                            $codes = explode('^', $order['codes']);
                                                            if(isset($codes[1]))
                                                            {
                                                               $codestrs = array();
                                                               foreach ($codes as $code)
                                                               {
                                                                   array_push($codestrs, $code . ':'.$order['playType'].':1');
                                                               }
                                                               $order['codes'] = implode($codestrs, ';');
                                                            }
                                                            else
                                                            {
                                                               $order['codes'] .= ":{$order['playType']}:2"; 
                                                            }
                                                        }
                                                        else
                                                        {
                                                            $order['codes'] .= ":{$order['playType']}:1";
                                                        }
                                                    }
						    break;
                                                case 21:
                                                    $order['codes'] .= ":2:2";
						    break;
                                                case 31:
                                                    $order['codes'] .= ":3:2";
						    break;
                                                case 41:
                                                    $order['codes'] .= ":4:2";
						    break;
                                                case 51:
                                                    $order['codes'] .= ":5:2";
						    break;
                                                case 61:
                                                    $order['codes'] .= ":6:2";
						    break;   
						case 22:
                                                    $order['codes'] = str_replace('#', '$', $order['codes']);
                                                    $order['codes'] .= ":2:5";
						    break;
                                                case 32:
                                                    $order['codes'] = str_replace('#', '$', $order['codes']);
                                                    $order['codes'] .= ":3:5";
						    break;
                                                case 42:
                                                    $order['codes'] = str_replace('#', '$', $order['codes']);
                                                    $order['codes'] .= ":4:5";
						    break;
                                                case 52:
                                                    $order['codes'] = str_replace('#', '$', $order['codes']);
                                                    $order['codes'] .= ":5:5";
						    break;
                                                case 62:
                                                    $order['codes'] = str_replace('#', '$', $order['codes']);
                                                    $order['codes'] .= ":6:5";
						    break;
						default:
						    break;
					}
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
        
	private function lid_number($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					$playType = 1;
					switch ($lid)
					{
						case '21406':
							$playType = $order['playType'];
							if($playType==13)
							{
								$order['codes'] = str_replace('*', ',', $order['codes']);
							}
							else
							{
								$order['codes'] = str_replace('*', '|', $order['codes']);
							}
							break;
						case '21407':
						case '21408':
							$playType = $order['playType'];
							$order['codes'] = str_replace('*', '|', $order['codes']);
							break;
						case '10022':
						case    '35':
							$order['codes'] = str_replace(array(',', '*'), array('', ','), $order['codes']);
							break;
						case 	'33':
							$playType = $order['playType'];
							if($playType == '1')
							{
								$order['codes'] = str_replace(array(',', '*'), array('', ','), $order['codes']);
							}
							elseif($playType == '2')
							{
								$order['codes'] = str_replace('*', ',', $order['codes']);
							}
							elseif($playType == '3')
							{
								$order['codes'] = str_replace('*', ',', $order['codes']);
							}
							break;
						case '23529':
							if($order['isChase'] == '1')
							{
								$playType = 2;
							}
							break;
						default:
							break;
					}
					$order['codes'] = preg_replace('/\^$/is', '', $order['codes']);
					$codes = explode('^', $order['codes']);
					$codestrs = array();
					if(empty($codes[1]) && strpos($order['codes'], '#'))
					{
						//胆拖
						if((strpos($order['codes'], '#') > strpos($order['codes'], '|')) && $lid =='23529')
						{
							$order['codes'] = "#" . $order['codes'];
						}
						array_push($codestrs, str_replace(array('#'), array('$'), $order['codes']) . ':'.$playType.':5');
					}
					elseif(!empty($codes[1]) || $order['betTnum'] == 1)
					{
						//单式
						foreach ($codes as $code)
						{
							array_push($codestrs, $code . ':'.$playType.':1');
						}
					}
					elseif(empty($codes[1]) && $order['betTnum'] > 1)
					{
						//排列三组三组六复式投注
						if($lid == '33' && in_array($playType, array('2', '3')))
						{   
							array_push($codestrs, $order['codes'] . ':'.$playType.':3');
						}
						else 
						{
							array_push($codestrs, $order['codes'] . ':'.$playType.':2');
						}
					}
					$order['codes'] = implode($codestrs, ';');
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
	private function formatIssue($issue, $lid, $pre='')
	{
		$issue_format = array('23529' => 0, '51' => 0, '21406' => 0, '33' => 0, '52' => 0,
		'53' => 0, '35' => 0, '10022' => 0, '23528' => 0, '11' => 0, '19' => 0, '21406' => 0,
		'21407' => 0, '21408' => 0);
		if(empty($pre))
		{
			return substr($issue, $issue_format[$lid]);
		}
		else
		{
			return "$pre$issue";
		}
	}
	
	private function bdy_10001($orders, $lid, $issue)
	{
		if(!empty($orders))
		{
			$orderids = array(); 
			$messageid = NULL;
			$body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
			$body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" pid=\"$issue\" >";
			foreach ($orders as $order)
			{
				$orderids[$order['orderId']][] = $order['sub_order_id'];
				if(empty($messageid)) $messageid = $order['message_id'];
				$body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
			}
			$body .= "</tickets>";
			return $this->cmt_comm(10001, $body, array('orderids' => $orderids, 'msgid' => $messageid, 'lid' => $lid));
		}
	}
	
	private function bdy_jjc($orders, $lid)
	{
		if(!empty($orders))
		{
			$orderids = array();
			$messageid = NULL;
			$body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
			$body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" >";
			foreach ($orders as $order)
			{
				$orderids[$order['orderId']][] = $order['sub_order_id'];
				if(empty($messageid)) $messageid = $order['message_id'];
				$body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
			}
			$body .= "</tickets>";
			return $this->cmt_comm($this->jjc_map[$lid][0], $body, array('orderids' => $orderids, 'msgid' => $messageid));
		}
	}
	
	private function bdy_10006($orders, $lid, $issue)
	{
		if(!empty($orders))
		{
			$orderids = array();
			$messageid = NULL;
			$body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
			$body .= "<tickets gid=\"{$this->jjc_map[$lid][1]}\" pid=\"{$issue}\" >";
			foreach ($orders as $order)
			{
				$orderids[$order['orderId']][] = $order['sub_order_id'];
				if(empty($messageid)) $messageid = $order['message_id'];
				$body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
			}
			$body .= "</tickets>";
			return $this->cmt_comm($this->jjc_map[$lid][0], $body, array('orderids' => $orderids, 'msgid' => $messageid));
		}
	}
	
	private function getWeekDay($mid)
	{
		$weeks = array('7', '1', '2', '3', '4', '5', '6');
		$week = date('w', strtotime($mid));
		return $weeks[$week];
	}
	
	private function getBonusDetail($lid)
	{
		$bonusDetail = array();
		switch ($lid) 
		{
			case 21406:
			case 21407:
			case 21408:
			case 21421:
				$detail['qy']['dzjj'] = '13';
				$detail['r2']['dzjj'] = '6';
				$detail['r3']['dzjj'] = '19';
				$detail['r4']['dzjj'] = '78';
				$detail['r5']['dzjj'] = '540';
				$detail['r6']['dzjj'] = '90';
				$detail['r7']['dzjj'] = '26';
				$detail['r8']['dzjj'] = '9';
				$detail['q2zhix']['dzjj'] = '130';
				$detail['q2zux']['dzjj'] = '65';
				$detail['q3zhix']['dzjj'] = '1170';
				$detail['q3zux']['dzjj'] = '195';
				$bonusDetail = $detail;
				break;
			case 53:
				$detail['hz']['z4'] = '80';
				$detail['hz']['z5'] = '40';
				$detail['hz']['z6'] = '25';
				$detail['hz']['z7'] = '16';
				$detail['hz']['z8'] = '12';
				$detail['hz']['z9'] = '10';
				$detail['hz']['z10'] = '9';
				$detail['hz']['z11'] = '9';
				$detail['hz']['z12'] = '10';
				$detail['hz']['z13'] = '12';
				$detail['hz']['z14'] = '16';
				$detail['hz']['z15'] = '25';
				$detail['hz']['z16'] = '40';
				$detail['hz']['z17'] = '80';
				$detail['sthtx'] = '40';
				$detail['sthdx'] = '240';
				$detail['sbth'] = '40';
				$detail['slhtx'] = '10';
				$detail['ethfx'] = '15';
				$detail['ethdx'] = '80';
				$detail['ebth'] = '8';
				$bonusDetail = $detail;
				break;
			case 54:
				$detail['thbx']['dzjj'] = '22';
				$detail['thdx']['dzjj'] = '90';
				$detail['thsbx']['dzjj'] = '535';
				$detail['thsdx']['dzjj'] = '2150';
				$detail['szbx']['dzjj'] = '33';
				$detail['szdx']['dzjj'] = '400';
				$detail['bzbx']['dzjj'] = '500';
				$detail['bzdx']['dzjj'] = '6400';
				$detail['dzbx']['dzjj'] = '7';
				$detail['dzdx']['dzjj'] = '88';
				$detail['r1']['dzjj'] = '5';
				$detail['r2']['dzjj'] = '33';
				$detail['r3']['dzjj'] = '116';
				$detail['r4']['dzjj'] = '46';
				$detail['r5']['dzjj'] = '22';
				$detail['r6']['dzjj'] = '12';
				$bonusDetail = $detail;
				break;
			default:
				break;
		}
		return $bonusDetail;
	}

	public function awardNumFormat($awardNum)
	{
		$klpkType = array(
			'1' => 'S',	// 黑
			'2' => 'H',	// 红
			'3' => 'C',	// 梅
			'4' => 'D',	// 方
		);
		$number = '';
		$p1 = array();
		$p2 = array();
		$awardNumArr = array_map('trim', explode(',', $awardNum));
		if($awardNumArr)
		{
			foreach ($awardNumArr as $nums) 
			{
				array_push($p1, substr($nums, 1, 2));
				$type = substr($nums, 0, 1);
				array_push($p2, $klpkType[$type]);
			}
		}
		$number = implode(',', $p1) . '|' . implode(',', $p2);
		return $number;
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
		if(in_array($lid, array(42, 43, 44, 45)))
		{
			// 按单处理
			$handle = true;
			$tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
			while ($handle && !empty($tickets)) 
			{
				$succNum = 0;
				foreach ($tickets as $ticket)
				{
					$this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
					$ticketRes = $this->med_20009($ticket['message_id'], $ticket['lid']);
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
		else
		{
			$issues = $this->CI->ticket_model->getIssuesByCdBonus($this->pctype_map[$lid][1], $this->seller);
			if(!empty($issues))
			{
				foreach ($issues as $issue)
				{
					$splitIssue = $this->CI->ticket_model->paiqiToSplit($issue, $lid);
					$counts = $this->CI->ticket_model->countTicketDetailByLid($lid, $splitIssue, $this->seller);
					if($counts > 0)
					{
						$ticketRes = $this->med_20004($issue, $lid);
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
}
