<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class ticket_qihui_search
{
	private $phone = '18621526831';
	private $id_card = '34128119861106923X';
	private $real_name = '刘书刚';
	private $seller = 'qihui';
	private $ptype_map = array
	(
		'RQSPF' => array('FT006', 1),
		'SPF' => array('FT001', 1),
		'HH42' => array('FT005', 0),
		'CBF' => array('FT002', 3),
		'JQS' => array('FT003', 1),
		'BQC' => array('FT004', 3),
	
		'SF' => array('BSK001', 1),
		'RFSF' => array('BSK002', 1),
		'SFC' => array('BSK003', 2),
		'DXF' => array('BSK004', 1),
		'HH43' => array('BSK005', 0)
	);
	
	private $ggtype_map = array
	(
		'1' => '500',
		'2' => '502',
		'3' => '503',
		'4' => '504',
		'5' => '505',
		'6' => '506',
		'7' => '507',
		'8' => '508',
	);
	
	private $pctype_map = array
	(
		'23529' => array('T001', 'dlt'),
		'23528' => array('F7', 'qlc'),
		'10022' => array('D7', 'qxc'),
		'21406' => array('C511', 'syxw'),
		'21408' => array('HB11X5', 'hbsyxw'),
		'51'    => array('F001', 'ssq'),
		'33'    => array('D3', 'pls'),
		'35'    => array('D5', 'plw'),
		'42'    => array('1', 'jczq'),
		'43'    => array('0', 'jclq'),
		'52'    => array('F3', 'fc3d'),
		'11'    => array('D14', 'sfc'),
		'19'	=> array('D9', 'rj'),
		'54'    => array('KLPK3', 'klpk')
	);
	
	private $issue_map = array
	(
		'42'    => '1',
		'43'    => '1',
	);
	
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->library('encrypt_qihui');
		$this->CI->load->model('ticket_model');
		$this->order_status = $this->CI->ticket_model->orderConfig('orders');
	}
	
	private function cmt_comm($mdid, $body, $orderids, $msgid = NULL)
	{
		//测试商户编号TWOTOFIVE
		$mdidmp = array('med1006' => '1006', 'med1020' => 1020);
		if(key_exists($mdid, $mdidmp))
		{
			$mdid = $mdidmp[$mdid];
		}
		if(empty($msgid))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			if($mdid == 1002)
			{
				$sub_order_ids = array();
				foreach ($orderids as $oids => $subodis)
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
			$UId = $msgid;
		}
		$body = $this->CI->encrypt_qihui->encrypt($body);
		$header  = "<?xml version='1.0' encoding='utf-8'?>";
		$header .= "<message>";
		$header .= "<head>";
		$header .= "<version>V1</version>";
		$header .= "<command>$mdid</command>";
		$header .= "<venderId>".$this->CI->config->item('qhtob_sellerid')."</venderId>";
		$header .= "<messageId>$UId</messageId>";
		$header .= "<md>" . md5($body) . "</md>";
		$header .= "</head>";
		$header .= "<body>$body</body>";
		$header .= "</message>"; 
		$result = $this->CI->tools->request($this->CI->config->item('qhtob_pji'), $header, 20);
		if($this->CI->tools->recode != 200 || empty($result))
		{
			return ;
		}
		$xmlobj = simplexml_load_string($result) ;
		$rfun = "result_$mdid";
		return $this->$rfun($xmlobj, $orderids, $UId);
		
	}
	
	//十一选五开奖结果查询
	public function med_syxwResult($issue)
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<lotteryId>{$this->pctype_map['21406'][0]}</lotteryId>";
		$body .= "<issue>{$issue}</issue>";
		$body .= "</body>";
		return $this->cmt_comm('1003', $body, array());
	}
	
	//十一选五结果入库
	private function result_1003($xmlobj, $orderids, $UId)
	{
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			$issue = (string)$xmlobj->issue;
			if((string)$xmlobj->drawCode->baseCode != '')
			{
				$awardNum = (string)$xmlobj->drawCode->baseCode;
				$awardNum = str_replace(' ', ',', $awardNum);
				//奖级信息
				$bonusDetail = array();
				$bonusDetail['qy']['dzjj'] = '13';
				$bonusDetail['r2']['dzjj'] = '6';
				$bonusDetail['r3']['dzjj'] = '19';
				$bonusDetail['r4']['dzjj'] = '78';
				$bonusDetail['r5']['dzjj'] = '540';
				$bonusDetail['r6']['dzjj'] = '90';
				$bonusDetail['r7']['dzjj'] = '26';
				$bonusDetail['r8']['dzjj'] = '9';
				$bonusDetail['q2zhix']['dzjj'] = '130';
				$bonusDetail['q2zux']['dzjj'] = '65';
				$bonusDetail['q3zhix']['dzjj'] = '1170';
				$bonusDetail['q3zux']['dzjj'] = '195';
				$bonusDetail = json_encode($bonusDetail);
				
				$data = array($awardNum, $bonusDetail, $issue);
				$this->CI->ticket_model->updateByIssue($data);
			}
			else
			{
				$this->CI->ticket_model->updateTryNum($issue);
			}
		}
	}
	
	//出票结果的获取（齐汇出票商）
	public function med_ticketResult($concel = FALSE)
	{
		$tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel);
		foreach ($tickets as $ticket)
		{
			switch ($ticket['lid'])
			{
				case '11':
				case '19':
				case '33':
				case '35':
				case '51':
				case '52':
				case '10022':
				case '21406':
				case '21408':
				case '23528':
				case '23529':
				case '54':
					$this->med_1006($ticket['message_id'], $concel);
					break;
				case '42':
				case '43':
					$this->med_1020($ticket['message_id'], $concel);
					break;
			}
		}
	}
	//订单中奖明细
	public function med_ticketBonus()
	{
		$tickets = $this->CI->ticket_model->getTicketBonus($this->seller);
		if(!empty($tickets))
		{
			foreach ($tickets as $ticket)
			{
				$this->med_1023($ticket['message_id'], $ticket['lid']);
			}
		}
	}
	
	public function file_bonusDetail()
	{   
		$issues = $this->CI->ticket_model->getIssuesForCpBonus();
		if(!empty($issues))
		{
			foreach ($issues as $issue)
			{
				$config = array('cid' => '1301', 'ptype' => $this->pctype_map[$issue['lid']][0], 
				'issue' => $this->formatIssue($issue['issue'], $issue['lid']), 'date' => str_replace('-', '', $issue['dates']));
				$this->med_1012($config);
			}
		}
	}
	
	private function med_1012($config)
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<checkType>{$config['cid']}</checkType>";
		if(!in_array($cid, array('1302')))
		{
			$body .= "<lotteryId>{$config['ptype']}</lotteryId>";
			$body .= "<issue>{$config['issue']}</issue>";
		}
		if(!in_array($cid, array('1303')))
		{
			$body .= "<checkDay>{$config['date']}</checkDay>";
		}
		$body .= "</body>"; 
		return $this->cmt_comm('1012', $body, array());
	}
	
	public function med_search_bonus($suborderid, $lid)
	{
		$subOrders = array($suborderid);
		if(!empty($subOrders))
		{
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<records>";
			foreach ($subOrders as $subOrder)
			{
				$body .= "<id>$subOrder</id>";
			}
			$body .= "</records>";
			$body .= "</body>"; 
		}
		return $this->cmt_comm('1023', $body, array($lid));
	}
	
	private function med_1023($messageId, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId);
		if(!empty($subOrders))
		{
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<records>";
			foreach ($subOrders as $subOrder)
			{
				$body .= "<id>$subOrder</id>";
			}
			$body .= "</records>";
			$body .= "</body>"; 
		}
		return $this->cmt_comm('1023', $body, array($lid));
	}
	
	private function med_1006($messageId, $concel)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel);
		if(!empty($subOrders))
		{
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<records>";
			foreach ($subOrders as $subOrder)
			{
				$body .= "<id>$subOrder</id>";
			}
		}
		$body .= "</records>";
		$body .= "</body>"; 
		return $this->cmt_comm('1006', $body, array('concel' => $concel));
	}
	
	private function med_1020($messageId, $concel)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel);
		if(!empty($subOrders))
		{
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<records>";
			foreach ($subOrders as $subOrder)
			{
				$body .= "<id>$subOrder</id>";
			}
		}
		$body .= "</records>";
		$body .= "</body>"; 
		return $this->cmt_comm('1020', $body, array('concel' => $concel));
	}
	
	public function med_peilv()
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<type>1</type>";
		$body .= "<valueType>1</valueType>";
		$body .= "<matchList>";
		$body .= "<id>20150430_4_001</id>";
		$body .= "</matchList>";
		$body .= "</body>";
		return $this->cmt_comm('1018', $body, array());
	}
	
	private function result_1012($xmlobj, $orderids, $UId)
	{
		$lids = array('SFC' => 11, 'RJ' => 19, 'PLS' => 33, 'PLW' => 35, 'BJDC' => 41, 'JCZQ' => 42, 'JCLQ' => 43, 
     		'SSQ' => 51, 'FCSD' => 52, 'QXC' => 10022, 'SYYDJ' => 21406, 'QLC' => 23528, 'DLT' => 23529);
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			$finfo = explode('_', "{$xmlobj->fileName}");
			$url = $this->CI->config->item('qhtob_dzh') . "/{$finfo[0]}/" . $this->pctype_map[$lids[$finfo[2]]][0] . '/Award/' . $xmlobj->fileName;
			$result = $this->CI->tools->request($url);
		}
	}
	
	/**
	 * 
	 * @param unknown_type $xmlobj
	 * @param array $orderids 用于存放彩种id  即lid
	 * @param unknown_type $UId
	 */
	private function result_1023($xmlobj, $orderids, $UId)
	{
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			print_r($xmlobj); exit;
			if(count($xmlobj->records->record));
			{
				$lid = $orderids[0];
				$data['s_datas'] = array();
				$data['d_datas'] = array();
				foreach ($xmlobj->records->record as $record)
				{
					$cpstate = 0;
					if($lid == '21406') $cpstate = 2;
					if(intval($record->result) == 0)
					{
						$cpstate = 1;
						if($lid == '21406')
						{
							$cpstate = 3;
						}
						array_push($data['s_datas'], '(?, ?, ?, ?)');
						array_push($data['d_datas'], "$record->id");
						array_push($data['d_datas'], "$record->awardValue");
						array_push($data['d_datas'], "$record->afterTaxValue");
						array_push($data['d_datas'], $cpstate);
					}
				}
				if(!empty($data['s_datas']))
				{
					$this->CI->ticket_model->setTicketBonus($data);
				}
			}
		}
	}
	
	private function result_1018($xmlobj, $orderids, $UId)
	{
		print_r($this->CI->encrypt_qihui->decrypt($xmlobj->body));
	}
	
	private function result_1006($xmlobj, $orderids, $UId)
	{
		if($xmlobj->head->result == 0 && $xmlobj->head->md == md5($xmlobj->body))
		{
			$this->CI->load->model('api_qihui_model');
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlObj = simplexml_load_string($datas);
			$concel = $orderids['concel'];
			if(!empty($xmlObj->records->record))
			{
				$allData = array();
				$errmsg = array();
				$delaysids = array();
				foreach ($xmlObj->records->record as $records)
				{
					$sub_order_id = (string)$records->id;
					$lid = (string)$records->lotteryId;
					if(empty($params['lid'])) $params['lid'] = $this->pctype_map[$lid];
					if(!empty($sub_order_id))
					{
						$orderTicket = array();
						$result = (string)$records->result;
						$successTime = empty($records->successTime) ? date('Y-m-d H:i:s', time() + 60) : (string)$records->successTime;
						if($result == '0')
						{
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['ticketId'] = (string)$records->ticketId;
							$orderTicket['status'] = $this->order_status['draw'];//$records->result;
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
						}
						elseif($result == '200021')
						{
							$status = $concel ? $this->order_status['concel'] : $this->order_status['drawing'];
							array_push($delaysids, $sub_order_id);
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['ticketId'] = 0;
							$orderTicket['status'] = $status;
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
						}
						else
						{
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['ticketId'] = 0;
							$orderTicket['status'] = $this->order_status['concel'];
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
						}
						if(!empty($orderTicket))
						{
							array_push($allData, $orderTicket);
						}
					}
				}
			}
			
			if(!empty($allData))
			{
				$fields = array('sub_order_id', 'status', 'ticketId', 'ticket_time');
				$dates['s_data'] = array();
				$dates['d_data'] = array();
				foreach ($allData as $ticket)
				{
					array_push($dates['s_data'], '(?, ?, ?, ?)');
					array_push($dates['d_data'], $ticket['sub_order_id']);
					array_push($dates['d_data'], $ticket['status']);
					array_push($dates['d_data'], $ticket['ticketId']);
					array_push($dates['d_data'], $ticket['ticket_time']);
				}
				$this->CI->load->model('api_caidou_model');
		    	return $this->CI->api_caidou_model->saveTicketId($fields, $dates);
			}
		}
	}
	
	private function result_med1006($xmlobj, $orderids, $UId)
	{
		$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
		$xmlobj = simplexml_load_string($datas);
		print_r($xmlobj);
	}
	
	private function result_med1020($xmlobj, $orderids, $UId)
	{
		$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
		$xmlobj = simplexml_load_string($datas);
		print_r($xmlobj);
	}
	
	private function result_1020($xmlobj, $orderids, $UId)
	{
		if($xmlobj->head->result == 0 && $xmlobj->head->md == md5($xmlobj->body))
		{
			$this->CI->load->model('api_qihui_model');
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlObj = simplexml_load_string($datas);
			$concel = $orderids['concel'];
			if(!empty($xmlObj->records->record))
			{
				$allDatas = array();
				$errmsg = array();
				$delaysids = array();
				$flag = 0;
				foreach ($xmlObj->records->record as $records)
				{
					$sub_order_id = (string)$records->id;
					$result = (string)$records->result;
					$lid = (string)$records->lotteryId;
					if(empty($params['lid'])) $params['lid'] = $this->pctype_map[$lid];
					if(!empty($sub_order_id))
					{
						$orderTicket = array();
						$orderMatches = array();
						$allData = array();
						$successTime = empty($records->successTime) ? date('Y-m-d H:i:s', time() + 60) : (string)$records->successTime;
						if($result == '0')
						{
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['ticketId'] = (string)$records->ticketId;
							$orderTicket['status'] = $this->order_status['draw'];//$records->result;
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
							if(!empty($records->info->item))
							{
								foreach ($records->info->item as $items)
								{
									if(empty($items->id)) continue;
									$orderResult = array();
									foreach ($items as $key => $item)
									{
										if(count($item))
										{
											foreach ($item as $fk => $val)
											{
												$orderResult['pdetail'][$key][$fk] = $val;
											}
										}
										else
										{
											if($key == 'id')
											{
												$item = explode('_', $item);
												$orderResult['mid'] = "{$item[0]}{$item[2]}";
											}
											else 
											{
												$orderResult['pdetail'][$key] = "{$item[0]}{$item[2]}";
											}
											
										}
									}
									$orderMatches[] = $orderResult;
								}
							}
						}
						elseif($result == '200021')
						{
							$status = $this->order_status['drawing'];
							if($concel)
							{
								$status = $this->order_status['concel'];
							}
							array_push($delaysids, $sub_order_id);
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['status'] = $status;
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
							$orderTicket['ticketId'] = 0;
						}
						else 
						{
							array_push($errmsg, $sub_order_id);
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['status'] = $this->order_status['concel'];
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
							$orderTicket['ticketId'] = 0;
						}
						if(!empty($orderTicket))
							$allData['orderTicket'] = $orderTicket;
						if(!empty($orderMatches))
							$allData['orderMatche'] = $orderMatches;
						$allDatas[] = $allData;
					}
				}
				if(!empty($allDatas))
				{
					$fields = array('sub_order_id', 'status', 'ticketId', 'ticket_time');
					$dates['s_data'] = array();
					$dates['d_data'] = array();
					foreach ($allDatas as $allData)
					{
						array_push($dates['s_data'], '(?, ?, ?, ?)');
						array_push($dates['d_data'], $allData['orderTicket']['sub_order_id']);
						array_push($dates['d_data'], $allData['orderTicket']['status']);
						array_push($dates['d_data'], $allData['orderTicket']['ticketId']);
						array_push($dates['d_data'], $allData['orderTicket']['ticket_time']);
					}
					$this->CI->load->model('api_caidou_model');
		    		return $this->CI->api_caidou_model->saveTicketId($fields, $dates);
				}
			}
			
		}
		/*else 
		{
			$this->CI->api_qihui_model->updateRelation($orderids['msgid']);
			$orders = $this->CI->ticket_model->getOrdersByMsg($orderids['msgid']);
			$this->CI->ticket_model->updateSplitFail($orders, $this->order_status['concel'], $xmlobj->head->result);
			$this->CI->ticket_model->updateOrdersOriStatus($orders, $this->order_status['concel']);
		}*/
	}
	
	private function result_1002($xmlobj, $orderids, $UId)
	{
		$orderid = array();
		$suborderid = array();
		foreach ($orderids as $order => $suborder)
		{
			$orderid[] = $order;
			foreach ($suborder as $subid)
			{
				$suborderid[] = $subid;
			}
		}
		$result = (string)$xmlobj->head->result;
		if($result == '0')
		{
			$this->CI->ticket_model->trans_start();
			$updatas = array('sids' => $suborderid);
			$re1 = $this->CI->ticket_model->ticket_succ($updatas);
			if($re1)
			{
				$this->CI->ticket_model->trans_complete();
			}
			else 
			{
				$this->CI->ticket_model->trans_rollback();
			}
			return true;
		}
		else
		{
			$this->CI->ticket_model->trans_start();
			$updatas = array('sids' => $suborderid, 'error' => $result);
			$re1 = $this->CI->ticket_model->ticket_fail($updatas);
			$msg = "对messageId:{$UId}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $UId, $msg, '齐汇合作商提票报警');
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
	
	private function lid_syxw($corders)
	{
		$saleCode = array(
			'1' => array('0' => 101, '1' => 101),
			'2' => array('0' => 111, '1' => 102, '2' => 121),
			'3' => array('0' => 112, '1' => 103, '2' => 122),
			'4' => array('0' => 113, '1' => 104, '2' => 123),
			'5' => array('0' => 114, '1' => 105, '2' => 124),
			'6' => array('0' => 115, '1' => 106, '2' => 125),
			'7' => array('0' => 116, '1' => 107, '2' => 126),
			'8' => array('0' => 117),
			'9' => array('0' => 141, '1' => 142),
			'10'=> array('0' => 161, '1' => 162),
			'11'=> array('0' => 131, '1' => 108, '2' => 133),
			'12'=> array('0' => 151, '1' => 109, '2' => 153),
		);
		$this->lid_number($corders, '21406', $saleCode);
	}
	
	private function lid_hbsyxw($corders)
	{
		$this->lid_number($corders, '21408', $this->saleCode);
	}
	
	private function lid_ssq($corders)
	{
		$this->lid_number($corders, '51');
	}
	
	private function lid_dlt($corders)
	{
		$this->lid_number($corders, '23529');
	}
	
	private function lid_qlc($corders)
	{
		$this->lid_number($corders, '23528');
	}
	
	private function lid_pls($corders)
	{
		$this->lid_pls_3d($corders, '33');
	}
	
	private function lid_fc3d($corders)
	{
		$this->lid_pls_3d($corders, '52');
	}
	
	private function lid_plw($corders)
	{
		$this->lid_comm($corders, '35');
	}
	
	private function lid_qxc($corders)
	{
		$this->lid_comm($corders, '10022');
	}
	
	private function lid_sfc($corders)
	{
		$this->lid_comm($corders, '11');
	}
	
	private function lid_rj($corders)
	{
		$this->lid_comm($corders, '19');
	}
	
	private function lid_comm($corders, $lid)
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
					$ggtype = '0';
					if(strpos($order['codes'], ',') == true)
					{
						$ggtype = 1;
					}
					$order['ggtype'] = $ggtype;
					$order['codes'] = str_replace(',', '', $order['codes']);
					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_1002($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
	private function lid_pls_3d($corders, $lid)
	{
		if(!empty($corders))
		{
			$saleCode = array('1' => array('0' => 0, '1' => 1), '2' => array('0' => 3, '1' => 5), '3' => array('0' => 3, '1' => 4));
			foreach ($corders as $issue => $orders)
			{
				$count = 0;
				$reorders = array();
				$issue_tmp = $this->formatIssue($issue, $lid);
				foreach ($orders as $in => $order)
				{
					$ggtype = '0';
					$isComma = strpos($order['codes'], ','); //投注串是否有逗号
					if($isComma)
					{
						$ggtype = 1;
					}
					$order['ggtype'] = $saleCode[$order['playType']][$ggtype];
					if($order['playType'] == 1)
					{
						$order['codes'] = str_replace(',', '', $order['codes']);
					}
					else
					{
						$codes = str_replace(',', '', $order['codes']);
						$order['codes'] = $ggtype ? '**'.$codes : $codes;
					}
					
					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_1002($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
	private function lid_number($corders, $lid, $saleCode = NULL)
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
					if(in_array($lid, array('21406')) && ($order['playType'] == 9 || $order['playType'] == 10))
					{	//前二、前三直选支持单式复式投注
						$ggtype = '0';
						if(strpos($order['codes'], ','))
						{
							$ggtype = '1';
							$codes = str_replace(',', '', $order['codes']);
						}
						else 
						{
							$codes = str_replace('*', '', $order['codes']);
						}
					}
					else 
					{
						$codes = str_replace(array('#', ','), array('*', ''), $order['codes']);
						$codes = preg_replace('/\^$/is', '', $codes);
						$bets = explode('^', $codes);
						//销售代码
						$ggtype = '0';
						if(count($bets) <= 1 && $order['betTnum'] > 1)
						{
							$ggtype = '1'; //复试
							if(strpos($codes, '*'))
							{
								$ggtype = '2'; //胆拖
							}
						}
					}
					
					$order['codes'] = $codes;
					if(in_array($lid, array('21406')))
					{//设置销售代码
						$order['ggtype'] = $saleCode[$order['playType']][$ggtype];
					}
					else 
					{
						$order['ggtype'] = $ggtype;
					}
					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_1002($reorders, $lid, $issue_tmp);
				}
			}
		}
	}
	
	private function formatIssue($issue, $lid)
	{
		$issue_format = array('23529' => 2, '51' => 0, '21406' => 0, '33' => 2, '52' => 0, '35' => 2, '10022' => 2, '23528' => 0, '11' => 2, '19' => 2);
		return substr($issue, $issue_format[$lid]);
	}
	
	private function lid_jczq($corders)
	{
		$this->lid_jjc($corders, 42);
	}
	
	private function lid_jclq($corders)
	{
		$this->lid_jjc($corders, 43);
	}
	
	private function lid_jjc($corders, $lid)
	{
		if(!empty($corders))
		{
			foreach ($corders as $issue => $orders)
			{
				$count = 0;
				$reorders = array();
				foreach ($orders as $in => $order)
				{
					$codes = $order['codes'];
					$codess = explode('|', $codes);
					$betcbts = explode('*', $codess[0]);
					$ptypes = array();
					$codestr = '';
					foreach ($betcbts as $betcbt)
					{
						$fields = explode(',', $betcbt);
						$date = substr($fields[0], 0, 8);
						$week = $this->getWeekDay($date);
						$ccid = substr($fields[0], 8);
						$ptype = $this->ptype_map[trim($fields[1])][0];
						$ptypes[$ptype] = "|$ptype|";
						$plvs = explode('/', $fields[2]);
						$zmcde = '';
						if(!empty($plvs))
						{
							foreach ($plvs as $plv)
							{
								$cde = substr($plv, 0, $this->ptype_map[$fields[1]][1]);
								if($lid == '43' && $fields[1] == 'DXF')
								{
									$dxf_map = array('0' => '2', '3' => '1');
									$cde = $dxf_map[$cde];
								}
								$zmcde .= $cde;
							}
						}
						$zmcde = preg_replace('/[^\d]/is', '', $zmcde);
						$codestr .= "$date|$week|$ccid|$ptype|$zmcde^";
					}
					$codestr = preg_replace('/\^$/is', '', $codestr);
					if(count($ptypes) == 1)
					{
						$codestr = str_replace($ptypes, array('|'), $codestr);
					}
					else 
					{
						$ptype = $this->ptype_map["HH$lid"][0];
					}
					$order['codes'] = $codestr;
					$order['ptype'] = $ptype;
					$order['ggtype'] = $this->ggtype_map[count($betcbts)];
					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_1002($reorders, $lid, $issue);
				}
			}
		}
	}
	
	private function bdy_1002($orders, $lid, $issue)
	{
		$issue = in_array($lid, array_keys($this->issue_map)) ? '1' : $issue;
		if(!empty($orders))
		{
			$orderids = array(); 
			$messageid = NULL;
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<lotteryId>{$this->pctype_map[$lid][0]}</lotteryId>";
			$body .= "<issue>$issue</issue>";
			$body .= "<records>";
			foreach ($orders as $order)
			{
				$orderids[$order['orderId']][] = $order['sub_order_id'];
				if(empty($messageid)) $messageid = $order['message_id'];
				$body .= "<record>";
				$body .= "<id>{$order['sub_order_id']}</id>";
				if(!empty($order['ptype']))
				{
					$body .= "<playType>{$order['ptype']}</playType>";
				}
				$body .= "<lotterySaleId>{$order['ggtype']}</lotterySaleId>";
				$body .= "<userName>{$this->real_name}</userName>";
				$body .= "<phone>{$this->phone}</phone>";
				$body .= "<idCard>{$this->id_card}</idCard>";
				$body .= "<code>{$order['codes']}</code>";
				$body .= "<money>{$order['money']}</money>";
				$body .= "<timesCount>{$order['multi']}</timesCount>";
				$body .= "<issueCount>1</issueCount>";
				$body .= "<investCount>{$order['betTnum']}</investCount>";
				$body .= "<investType>{$order['isChase']}</investType>";
				$body .= "</record>";
			}
			$body .= "</records>";
			$body .= "</body>";
			return $this->cmt_comm(1002, $body, $orderids, $messageid);
		}
	}
	
	public function med_search_suborderid($subOrders, $lid)
	{
		$mid = '';
		switch ($lid)
		{
			case '11':
			case '19':
			case '33':
			case '35':
			case '51':
			case '52':
			case '10022':
			case '21406':
			case '23528':
			case '23529':
			case '54':
				$mid = med1006;
				break;
			case '42':
			case '43':
				$mid = med1020;
				break;
		}
		if(!empty($subOrders))
		{
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<records>";
			foreach ($subOrders as $subOrder)
			{
				$body .= "<id>$subOrder</id>";
			}
		}
		$body .= "</records>";
		$body .= "</body>"; 
		return $this->cmt_comm($mid, $body, array('concel' => $concel));
	}
	
	private function getWeekDay($mid)
	{
		$weeks = array('7', '1', '2', '3', '4', '5', '6');
		$week = date('w', strtotime($mid));
		return $weeks[$week];
	}
}