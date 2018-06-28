<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class ticket_caidou_search
{
	private $phone = '18621526831';
	
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
		'19'	=> array('D9', 'rj')
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
		'21407' => array('10001', '54'),
		'21408' => array('10001', '58'),
		'53'    => array('10001', '66'),
		'11'    => array('10001', '80'),
		'19'    => array('10001', '81'),
		'35'    => array('10001', '52'),
		'33'    => array('10001', '53'),
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
		if(empty($datas['msgid']))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			if(in_array($mdid, array(10001, 10002, 10003)))
			{
				$sub_order_ids = array();
				foreach ($datas['orderids'] as $oids => $subodis)
				{
					foreach ($subodis as $subodi)
					{
						array_push($sub_order_ids, $subodi);
					}
				}
				$this->CI->ticket_model->saveMessageId($sub_order_ids, $UId);
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
		$result = $this->CI->tools->request($this->CI->config->item('cdtob_pji'), $content, 20);
		if($this->CI->tools->recode != 200 || empty($result))
		{
			return ;
		}
		$xmlobj = simplexml_load_string($result) ;
		$rfun = "result_$mdid";
		return $this->$rfun($xmlobj, $datas);
		
	}
	
	//订单中奖明细
	public function med_ticketBonus()
	{
		$issues = $this->CI->ticket_model->getIssuesForCpBonus('ssq');
		if(!empty($issues))
		{
			foreach ($issues as $issue)
			{
				$this->med_20004($issue);
			}
		}
		$tickets = $this->CI->ticket_model->getTicketBonus($this->seller);
		if(!empty($tickets))
		{
			foreach ($tickets as $ticket)
			{
				$this->med_20009($ticket['message_id'], $ticket['lid']);
			}
		}
	}
	/*
	 * 功能：查询彩豆出票结果
	 * 作者：huxm
	 * 日期：2016-03-09
	 * */
	public function med_ticketResult($concel = false)
	{
		$tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel);
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
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($message_id, $concel);
		$lid = $this->jjc_map[$lid][1];
		if(!empty($subOrders))
		{
			$apply = array();
			foreach ($subOrders as $subOrder)
			{
				array_push($apply, $subOrder);
			}
			$body = "<query gid=\"$lid\" apply=\"" . implode(',', $apply) . "\"/>";
			$this->cmt_comm('20008', $body, array('concel' => $concel));
		}
	}
	
	public function med_search_bonus($suborderid, $lid)
	{
		$subOrders = array($suborderid);
		if(!empty($subOrders))
		{
			$apply = '';
			foreach ($subOrders as $subOrder)
			{
				$apply .= "$subOrder,";
			}
			$apply = preg_replace('/,$/', '', $apply);
			$body .= "<query gid=\"{$this->jjc_map[$lid][1]}\" tid = \"\" apply=\"$apply\" />";
		}
		$this->cmt_comm('20009', $body, array());
	}
	
	private function med_20009($messageId, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId);
		if(!empty($subOrders))
		{
			$apply = '';
			foreach ($subOrders as $subOrder)
			{
				$apply .= "$subOrder,";
			}
			$apply = preg_replace('/,$/', '', $apply);
			$body .= "<query gid=\"{$this->jjc_map[$lid][1]}\" tid = \"\" apply=\"$apply\" />";
		}
		$this->cmt_comm('20009', $body, array());
	}
	
	private function med_20004($issue)
	{
		$body = "<query gid=\"01\" pid=\"$issue\"/>";
		if($this->cmt_comm('20004', $body, array()) == 3)
		{
			$tpg = $this->cmt_comm('20005', $body, array());
			if($tpg > 0)
			{
				for($pg = 1; $pg <= $tpg; $pg++)
				{
					$this->med_20006($issue, $pg);
				}
			}
			$this->CI->ticket_model->trans_start();
			$re = $this->CI->ticket_model->setIssueStatus('ssq', $issue);
			$re1 = $this->CI->ticket_model->setCpstate('ssq', $issue);
			if($re && $re1)
			{
				$this->CI->ticket_model->trans_complete();
			}
			else 
			{
				$this->CI->ticket_model->trans_rollback();
			}
		}
	}
	
	private function med_20006($issue, $pn)
	{
		$body = "<query gid=\"01\" pid=\"$issue\" pn=\"$pn\"/>";
		$this->cmt_comm('20006', $body, array());
	}
	
	private function result_20006($xmlobj, $datas)
	{
		$s_data = array();
		$d_data = array();
		$fields = array('sub_order_id', 'message_id', 'bonus_t', 'margin_t', 'cpstate');
		foreach ($xmlobj->body->rows->row as $row)
		{
			array_push($s_data, '(?, ?, ?, ?, ?)');
			array_push($d_data, "{$row['apply']}");
			array_push($d_data, "{$row['bid']}");
			array_push($d_data, ParseUnit($row['bonus']));
			array_push($d_data, ParseUnit($row['tax']));
			array_push($d_data, 1);
		}
		if(!empty($s_data))
		{
			$this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data);
		}
	}
	
	private function result_20005($xmlobj, $data)
	{
		return intval($xmlobj->body->rows->row['tps']);
	}
	
	private function result_20004($xmlobj, $data)
	{
		return intval($xmlobj->body->rows->row['state']);
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
			$fields = array('sub_order_id', 'status', 'ticketId', 'ticket_time');
	    	$datas['s_data'] = array();
			$datas['d_data'] = array();
			$datas['relation'] = array();
			$datas['relationConcel'] = array();
			foreach ($xmlobj->body->rows->row as $ticket)
			{
	    		
	    		$result = intval($ticket['code']);
	    		if($result == 0)   //出票成功
	    		{
	    			$status = $this->order_status['draw'];
	    		}
	    		elseif($result == 1)//限号出票失败
	    		{
	    			$status = $this->order_status['concel'];
	    			array_push($datas['relationConcel'], (string)$ticket['apply']);
	    		}
	    		elseif($result == 2)//出票中
	    		{
	    			$status = $this->order_status['drawing'];
	    			if($data['concel'])
	    			{
	    				$status = $this->order_status['concel'];
	    				array_push($datas['relationConcel'], (string)$ticket['apply']);
	    			}
	    		}
	    		array_push($datas['s_data'], '(?, ?, ?, ?)');
	    		array_push($datas['d_data'], "{$ticket['apply']}");
	    		//array_push($datas['d_data'], $result);
	    		array_push($datas['d_data'], $status);
	    		//array_push($datas['d_data'], intval($ticket['money']) * 100);
	    		//array_push($datas['d_data'], "{$ticket['tdate']}");
	    		array_push($datas['d_data'], "{$ticket['tid']}");
	    		array_push($datas['d_data'], "{$ticket['tdate']}");
	    		/*if(!empty($ticket['memo']))
	    			$datas['relation']["{$ticket['gid']}"]["{$ticket['apply']}"] = "{$ticket['memo']}";*/
			}
			print_r($xmlobj);
			print_r($datas);
			$this->CI->load->model('api_caidou_model');
		    return $this->CI->api_caidou_model->saveTicketId($fields, $datas);
		}
	}
	
	private function result_20009($xmlobj, $data)
	{
		$s_data = array();
		$d_data = array();
		$fields = array('sub_order_id', 'bonus_t', 'margin_t', 'cpstate');
		print_r($xmlobj); exit;
		foreach ($xmlobj->body->rows->row as $row)
		{
			$cpstate = intval($row['flag']);
			//11选5的对比奖金拉去
			if(intval($row['gid']) == 54)
			{
				if($cpstate == 1)
				{
					$cpstate = 3;
				}
				elseif($cpstate == 0)
				{
					$cpstate = 2;
				}
			}
			array_push($s_data, '(?, ?, ?, ?)');
			array_push($d_data, "{$row['apply']}");
			array_push($d_data, ParseUnit($row['bonus']));
			array_push($d_data, ParseUnit($row['tax']));
			array_push($d_data, $cpstate);
		}
		if(!empty($s_data))
		{
			$this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data);
		}
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
	
	private function result_betting($xmlobj, $data)
	{
		$orderid = array();
		$suborderid = array();
		$splitConcel = array();
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
				$re1 = $this->CI->ticket_model->ticket_succ_cd($fields, $d_data);
				$re2 = true;
				if(!empty($splitConcel))
				{
		    		$re2 = $this->CI->ticket_model->order_split_concel($splitConcel);
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
			$re1 = $this->CI->ticket_model->ticket_fail($updatas);
			$msg = "对messageId:{$messageId}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $messageId, $msg, '彩豆合作商提票报警');
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
					$order['codes'] = $codestr . implode(',', $strArr) . '|' . $this->ggtype_map[count($betcbts)];
					array_push($reorders, $order);
				}
			}

			if(count($reorders))
			{
				$re = $this->bdy_jjc($reorders, $lid);
				$reorders = array();
				if(!$re) break;
			}
		}
	}
	
	private function lid_number($corders, $lid, $saleCode = NULL)
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
					if(empty($codes[1]) && strpos($order['codes'], '#'))
					{
						//胆拖
						//echo str_replace(array('#'), array('$'), $order['codes']) . ':1:5';
						array_push($codestrs, str_replace(array('#'), array('$'), $order['codes']) . ':1:5');
					}
					elseif(!empty($codes[1]) || $order['betTnum'] == 1)
					{
						//单式
						foreach ($codes as $code)
						{
							array_push($codestrs, $code . ':1:1');
						}
					}
					elseif(empty($codes[1]) && $order['betTnum'] > 1)
					{
						//复试
						array_push($codestrs, $order['codes'] . ':1:2');
					}
					$order['codes'] = implode($codestrs, ';');
					array_push($reorders, $order);
				}
				if(count($reorders))
				{
					$re = $this->bdy_10001($reorders, $lid, $issue_tmp);
					$reorders = array();
					if(!$re) break;
				}
			}
		}
	}
	
	private function formatIssue($issue, $lid)
	{
		$issue_format = array('23529' => 2, '51' => 0, '21406' => 0, '33' => 2, '52' => 0, '35' => 2, '10022' => 2, '23528' => 0, '11' => 2, '19' => 2);
		return substr($issue, $issue_format[$lid]);
	}
	
	private function bdy_10001($orders, $lid, $issue)
	{
		if(!empty($orders))
		{
			$orderids = array(); 
			$messageid = NULL;
			$body  = "<user idcard=\"\" name=\"\" mobile=\"\" />";
			$body .= "<tickets gid=\"01\" pid=\"$issue\" >";
			foreach ($orders as $order)
			{
				$orderids[$order['orderId']][] = $order['sub_order_id'];
				if(empty($messageid)) $messageid = $order['message_id'];
				$body .= "<ticket apply=\"{$order['sub_order_id']}\" codes=\"{$order['codes']}\" mulity=\"{$order['multi']}\" money=\"" . $order['money']/100 . "\" />";
			}
			$body .= "</tickets>";
			return $this->cmt_comm(10001, $body, array('orderids' => $orderids, 'msgid' => $messageid));
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
	
	public function med_search_suborderid($subOrders, $lid)
	{
		$lid = $this->jjc_map[$lid][1];
		if(!empty($subOrders))
		{
			$apply = array();
			foreach ($subOrders as $subOrder)
			{
				array_push($apply, $subOrder);
			}
			$body = "<query gid=\"$lid\" apply=\"" . implode(',', $apply) . "\"/>";
			$this->cmt_comm('20008', $body, array('concel' => false));
		}
	}
	
	private function getWeekDay($mid)
	{
		$weeks = array('7', '1', '2', '3', '4', '5', '6');
		$week = date('w', strtotime($mid));
		return $weeks[$week];
	}
}