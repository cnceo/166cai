<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class ticket_qihui
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
		'16' => '509',
		'17' => '526',
		'18' => '527',
		'19' => '511',
		'20' => '539',
		'21' => '540',
		'22' => '528',
		'23' => '529',
		'24' => '514',
		'25' => '544',
		'26' => '545',
		'27' => '530',
		'28' => '541',
		'29' => '531',
		'30' => '532',
		'31' => '518',
		'32' => '549',
		'33' => '550',
		'34' => '533',
		'35' => '542',
		'36' => '546',
		'37' => '534',
		'38' => '543',
		'39' => '535',
		'40' => '536',
		'41' => '523',
		'42' => '553',
		'43' => '554',
		'44' => '551',
		'45' => '547',
		'46' => '537',
		'47' => '556',
		'48' => '557',
		'49' => '555',
		'50' => '552',
		'51' => '548',
		'52' => '538',
	);
	
	private $pctype_map = array
	(
		'23529' => array('T001', 'dlt'),
		'23528' => array('F7', 'qlc'),
		'10022' => array('D7', 'qxc'),
		'21406' => array('C511', 'syxw'),
		// '21407' => array('JX11X5', 'jxsyxw'),
		'21408' => array('HB11X5', 'hbsyxw'),
		'51'    => array('F001', 'ssq'),
		'33'    => array('D3', 'pls'),
		'35'    => array('D5', 'plw'),
		'42'    => array('1', 'jczq'),
		'43'    => array('0', 'jclq'),
		'52'    => array('F3', 'fc3d'),
		'11'    => array('D14', 'sfc'),
		'19'	=> array('D9', 'rj'),
		'54'    => array('KLPK3', 'klpk'),
		'55'    => array('CQSSC', 'cqssc'),
	);
	
	private $issue_map = array
	(
		'42'    => '1',
		'43'    => '1',
	);
	
	private $saleCode = array(
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
			'13'=> array('0' => 172, '1' => 172),
			'14'=> array('0' => 173, '1' => 173),
			'15'=> array('0' => 174, '1' => 174),
		);

	// 老时时彩销售代码
	private $cqsscSaleCode = array(
		'1'	 =>	'4000',
		'10' => '4010',
		'20' => '4020',
		'21' =>	'4021',
		'23' => '4023',
		'25' => '4025',
		'26' => '4026',
		'27' => '4027 ',
		'30' => '4030',
		'31' => '4031',
		'33' => '4033',
		'34' => '4034',
		'35' => '4035',
		'36' => '4036',
		'37' => '4037',
		'38' => '4038',
		'40' => '4040',
		'41' => '4041',
		'43' => '4043',
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
	
	private function cmt_comm($mdid, $body, $datas = array())
	{
		//测试商户编号TWOTOFIVE
		if(empty($datas['msgid']))
		{
			$UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
			$datas['msgid'] = $UId;
			if($mdid == 1002)
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
				$this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 1);
			}
		}
		else 
		{
			$UId = $datas['msgid'];
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
		/*请求前日志记录*/
		$pathTail = "qihui$mdid/" . date('YmdH');
		if(empty($datas['batch'])) $datas['batch'] = $UId;
		$LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
		log_message('LOG', $LogHead . $pathTail, "qihui$mdid/$mdid");
		log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
		
		$result = $this->CI->tools->request($this->CI->config->item('qhtob_pji'), $header, 20);
		if($this->CI->tools->recode != 200 || empty($result))
		{
			if($datas['concel'] && in_array($mdid, array('1006', '1020')))
			{
				$this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
			}
			return ;
		}
		/*请求返回日志记录*/
		log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
		
		$xmlobj = simplexml_load_string($result);
		$rfun = "result_$mdid";
		return $this->$rfun($xmlobj, $datas);
		
	}
	
	//十一选五开奖结果查询
	public function med_syxwResult($issue)
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<lotteryId>{$this->pctype_map['21406'][0]}</lotteryId>";
		$body .= "<issue>{$issue}</issue>";
		$body .= "</body>";
		return $this->cmt_comm('1003', $body, array('lid' => '21406'));
	}
	
	//十一选五结果入库
	private function result_1003($xmlobj, $params)
	{
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			$issue = (string)$xmlobj->issue;
			// 彩种对应数据库
			$type = $this->pctype_map[$params['lid']][1];

			if((string)$xmlobj->drawCode->baseCode != '')
			{
				$awardNum = (string)$xmlobj->drawCode->baseCode;
				$awardNum = str_replace(' ', ',', $awardNum);

				switch ($params['lid']) 
				{
					case '21406':
					case '21407':
					case '21408':
						$bonusDetail = $this->getSyxwBonus();
						break;
					case '53':
						$bonusDetail = $this->getKsBonus();
						break;
					case '54':
						$bonusDetail = $this->getKlpkBonus();
						break;
					case '55':
						$bonusDetail = $this->getCqsscBonus();
						break;
					default:
						# code...
						break;
				}
				$data = array($awardNum, $bonusDetail, $issue);
				$this->CI->ticket_model->updateByIssue($data, $type);
				//启动同步号码任务
				$this->CI->ticket_model->updateStop(1, $params['lid'], 0);
			}
			else
			{
				$this->CI->ticket_model->updateTryNum($issue, $type);
			}
		}
	}

	// 获取十一选五开奖详情
	public function getSyxwBonus()
	{
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
		$bonusDetail['lx3']['q3']['dzjj'] = '1384';
		$bonusDetail['lx3']['z3']['dzjj'] = '214';
		$bonusDetail['lx3']['r3']['dzjj'] = '19';
		$bonusDetail['lx4']['r44']['dzjj'] = '154';
		$bonusDetail['lx4']['r43']['dzjj'] = '19';
		$bonusDetail['lx5']['r55']['dzjj'] = '1080';
		$bonusDetail['lx5']['r54']['dzjj'] = '90';
		$bonusDetail = json_encode($bonusDetail);

		return $bonusDetail;
	}

	// 获取快三开奖详情
	public function getKsBonus()
	{
		//奖级信息
		$bonusDetail = array();
		$bonusDetail['hz']['z4'] = '80';
		$bonusDetail['hz']['z5'] = '40';
		$bonusDetail['hz']['z6'] = '25';
		$bonusDetail['hz']['z7'] = '16';
		$bonusDetail['hz']['z8'] = '12';
		$bonusDetail['hz']['z9'] = '10';
		$bonusDetail['hz']['z10'] = '9';
		$bonusDetail['hz']['z11'] = '9';
		$bonusDetail['hz']['z12'] = '10';
		$bonusDetail['hz']['z13'] = '12';
		$bonusDetail['hz']['z14'] = '16';
		$bonusDetail['hz']['z15'] = '25';
		$bonusDetail['hz']['z16'] = '40';
		$bonusDetail['hz']['z17'] = '80';
		$bonusDetail['sthtx'] = '40';
		$bonusDetail['sthdx'] = '240';
		$bonusDetail['sbth'] = '40';
		$bonusDetail['slhtx'] = '10';
		$bonusDetail['ethfx'] = '15';
		$bonusDetail['ethdx'] = '80';
		$bonusDetail['ebth'] = '8';
		$bonusDetail = json_encode($bonusDetail);

		return $bonusDetail;
	}

	// 获取快乐扑克开奖详情
	public function getKlpkBonus()
	{
		//奖级信息
		$bonusDetail = array();
		$bonusDetail['thbx']['dzjj'] = '22';
		$bonusDetail['thdx']['dzjj'] = '90';
		$bonusDetail['thsbx']['dzjj'] = '535';
		$bonusDetail['thsdx']['dzjj'] = '2150';
		$bonusDetail['szbx']['dzjj'] = '33';
		$bonusDetail['szdx']['dzjj'] = '400';
		$bonusDetail['bzbx']['dzjj'] = '500';
		$bonusDetail['bzdx']['dzjj'] = '6400';
		$bonusDetail['dzbx']['dzjj'] = '7';
		$bonusDetail['dzdx']['dzjj'] = '88';
		$bonusDetail['r1']['dzjj'] = '5';
		$bonusDetail['r2']['dzjj'] = '33';
		$bonusDetail['r3']['dzjj'] = '116';
		$bonusDetail['r4']['dzjj'] = '46';
		$bonusDetail['r5']['dzjj'] = '22';
		$bonusDetail['r6']['dzjj'] = '12';
		$bonusDetail = json_encode($bonusDetail);

		return $bonusDetail;
	}

	// 获取快乐扑克开奖详情
	public function getCqsscBonus()
	{
		//奖级信息
		$bonusDetail = array();
		$bonusDetail['1xzhix'] = '10';
		$bonusDetail['2xzhix'] = '100';
		$bonusDetail['2xzux'] = '50';
		$bonusDetail['3xzhix'] = '1000';
		$bonusDetail['3xzu3'] = '320';
		$bonusDetail['3xzu6'] = '160';
		$bonusDetail['5xzhix'] = '100000';
		$bonusDetail['5xtx']['qw'] = '20440';
		$bonusDetail['5xtx']['3w'] = '220';
		$bonusDetail['5xtx']['2w'] = '20';
		$bonusDetail['dxds'] = '4';
		$bonusDetail = json_encode($bonusDetail);

		return $bonusDetail;
	}
	
	//出票结果的获取（齐汇出票商）
	public function med_ticketResult($concel = FALSE, $lid = 0)
	{
		$tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);
		if(!empty($tickets))
		{
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
					case '55':
						$this->med_1006($ticket['message_id'], $concel, $ticket['lid']);
						break;
					case '42':
					case '43':
						$this->med_1020($ticket['message_id'], $concel, $ticket['lid']);
						break;
				}
			}
		}
	}
	//订单中奖明细
	public function med_ticketBonus($lid = 0)
	{
		// 快三 江西十一选五 不支持
		if(in_array($lid, array(21406, 21408, 54, 55)))
		{
			$issues = $this->CI->ticket_model->getIssuesForCpBonus($this->pctype_map[$lid][1], $this->seller);
			if(!empty($issues))
			{
				foreach ($issues as $issue)
				{
					$config = array(
						'cid' 	=>	'1301', 
						'ptype' => 	$this->pctype_map[$lid][0], 
						'issue' => 	$issue, 
						'date' 	=> 	date('Ymd'),
						'lid'	=>	$lid
					);
					$this->med_1012($config);
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
						$config = array(
							'cid' 	=>	'1301', 
							'ptype' => 	$this->pctype_map[$mlid][0], 
							'issue' => 	$issue, 
							'date' 	=> 	date('Ymd'),
							'lid'	=>	$mlid
						);
						$this->med_1012($config);
					}
				}
			}
			$tickets = $this->CI->ticket_model->getTicketBonus($this->seller, $lid);
			if(!empty($tickets))
			{
				foreach ($tickets as $ticket)
				{
					$this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
					$this->med_1023($ticket['message_id'], $ticket['lid']);
				}
			}
		}
	}
	
	public function file_bonusDetail($lid)
	{   
		//$issues = $this->CI->ticket_model->getIssuesForCpBonus();
		$issues = array(array('issue' => '17053158'));
		if(!empty($issues))
		{
			foreach ($issues as $issue)
			{
				$config = array('cid' => '1301', 'ptype' => $this->pctype_map[$lid][0], 
				'issue' => $this->formatIssue($issue['issue'], $lid), 'date' => date('Ymd'));
				$this->med_1012($config);
			}
		}
	}
	
	private function med_1012($config)
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<checkType>{$config['cid']}</checkType>";
		if(!in_array($config['cid'], array('1302')))
		{
			$body .= "<lotteryId>{$config['ptype']}</lotteryId>";
			$body .= "<issue>{$config['issue']}</issue>";
		}
		if(!in_array($config['cid'], array('1303')))
		{
			$body .= "<checkDay>{$config['date']}</checkDay>";
		}
		$body .= "</body>"; 
		$config['batch'] = "{$config['lid']}-{$config['issue']}";
		return $this->cmt_comm('1012', $body, $config);
	}
	
	private function med_1023($messageId, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId, $lid);
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
		return $this->cmt_comm('1023', $body, array('lid' => $lid, 'batch' => $messageId));
	}
	
	private function med_1006($messageId, $concel, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel, $lid);
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
			return $this->cmt_comm('1006', $body, array('concel' => $concel, 'lid' => $lid, 'batch' => $messageId));
		}
		
	}
	
	private function med_1020($messageId, $concel, $lid)
	{
		$subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel, $lid);
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
			return $this->cmt_comm('1020', $body, array('concel' => $concel, 'lid' => $lid, 'batch' => $messageId));
		}
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
	
	private function result_1012($xmlobj, $params)
	{
		// split表期次与排期表不一致
		$lids = array('10022', '23529', '33', '35', '11', '19');
		$issue = $params['issue'];
		$oissue = $params['issue'];
		if(in_array($params['lid'], $lids))
		{
			$issue = $this->formatSplitIssue($params['issue'], $params['lid'], '20');
		}
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			$fileName = $xmlobj->fileName;
			$fileName = (string)$fileName;
			if(!empty($fileName))
			{
				$finfo = explode('_', "{$xmlobj->fileName}");
				$url = $this->CI->config->item('qhtob_dzh') . "/{$finfo[0]}/{$this->pctype_map[$params['lid']][0]}" . '/Award/' . $xmlobj->fileName;
				$result = $this->CI->tools->request($url);
				if($this->CI->tools->recode == 200 && !empty($result))
				{
					$xmlRes = simplexml_load_string($result);
					if(!empty($xmlRes->body->record))
					{
						$s_data = array();
						$d_data = array();
						$fields = array('sub_order_id', 'bonus_t', 'margin_t', 'pull_bonus_time', 'cpstate');
						foreach ($xmlRes->body->record as $record) 
						{
							$bonusData = explode(',', (string)$record);
							array_push($s_data, '(?, ?, ?, ?, ?)');
							array_push($d_data, "{$bonusData[0]}");
							array_push($d_data, $bonusData[3]);
							array_push($d_data, $bonusData[6]);
							array_push($d_data, date('Y-m-d H:i:s'));
							$cpstate = 3;
							if(in_array(intval($params['lid']), array(11, 19)))
							{
								$cpstate = 1;
							}
							array_push($d_data, $cpstate);
						}
					}

					if(!empty($s_data))
					{
						$bonusRes = $this->CI->ticket_model->setCdBonus($fields, $s_data, $d_data, $params['lid']);
					}
					else
					{
						$bonusRes = true;
					}

					// 文件名存在标识已确认拉取结果
					$this->CI->ticket_model->trans_start();
					$re = $this->CI->ticket_model->setIssueStatus($this->pctype_map[$params['lid']][1], $oissue, $this->seller);
					$re1 = $this->CI->ticket_model->setCpstate($this->pctype_map[$params['lid']][1], $issue, $this->seller);
					if($re && $re1 && $bonusRes)
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
		return false;
		// 更新拉取时间
		// $this->CI->ticket_model->setCdBonusTime($this->pctype_map[$params['lid']][1], $oissue, $this->seller);
	}
	
	/**
	 * 
	 * @param unknown_type $xmlobj
	 * @param array $orderids 用于存放彩种id  即lid
	 * @param unknown_type $UId
	 */
	private function result_1023($xmlobj, $params)
	{
		if($xmlobj->head->result == 0 && md5($xmlobj->body) == $xmlobj->head->md)
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlobj = simplexml_load_string($datas);
			if(count($xmlobj->records->record));
			{
				$lid = $params['lid'];
				$data['s_datas'] = array();
				$data['d_datas'] = array();
				foreach ($xmlobj->records->record as $record)
				{
					$cpstate = 0;
					if(in_array($lid, array('53', '54', '21406', '21407', '21408', '42', '43', '55'))) $cpstate = 2;
					if(intval($record->result) == 0)
					{
						$cpstate = 3;
						if(in_array($lid, array('11', '19')))
						{
							$cpstate = 1;
						}
						array_push($data['s_datas'], '(?, ?, ?, ?, ?)');
						array_push($data['d_datas'], "$record->id");
						array_push($data['d_datas'], "$record->awardValue");
						array_push($data['d_datas'], "$record->afterTaxValue");
						array_push($data['d_datas'], date('Y-m-d H:i:s'));
						array_push($data['d_datas'], $cpstate);
					}
				}
				if(!empty($data['s_datas']))
				{
					$this->CI->ticket_model->setTicketBonus($data, $lid);
					return true;
				}
			}
		}
		return false;
	}
	
	private function result_1018($xmlobj, $params)
	{
	}
	
	private function result_1006($xmlobj, $params)
	{
		if($xmlobj->head->result == 0 && $xmlobj->head->md == md5($xmlobj->body))
		{
			$this->CI->load->model('api_qihui_model');
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlObj = simplexml_load_string($datas);
			$this->CI->api_qihui_model->numPeilv($xmlObj, $params);
		}
	}
	
	private function result_1020($xmlobj, $params)
	{
		if($xmlobj->head->result == 0 && $xmlobj->head->md == md5($xmlobj->body))
		{
			$this->CI->load->model('api_qihui_model');
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlObj = simplexml_load_string($datas);
			$this->CI->api_qihui_model->delPeilv($xmlObj, $params);
		}
	}
	
	private function result_1002($xmlobj, $params)
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
		$result = (string)$xmlobj->head->result;
		if($result == '0')
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$rxmlobj = simplexml_load_string($datas);
			$errArr = array();
			if(!empty($rxmlobj->records->record))
			{
				$updatas['s_data'] = array();
				$updatas['d_data'] = array();
				foreach ($rxmlobj->records->record as $record)
				{
					$res = (string)$record->result;
					if($res != '0')
					{
						array_push($updatas['s_data'], '(?, ?, 0)');
						array_push($updatas['d_data'], (string)$record->id);
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
				$this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '齐汇合作商提票报警');
			}
			else 
			{
				$updatas = array('sids' => $suborderid);
				$this->CI->ticket_model->ticket_succ($updatas, $params['lid']);
			}
			return true;
		}
		else
		{
			$updatas = array('sids' => $suborderid, 'error' => $result);
			$this->CI->ticket_model->ticket_fail($updatas, $params['lid']);
			$msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '齐汇合作商提票报警');
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
		
		$this->lid_number($corders, '21406', $this->saleCode);
	}
	
	private function lid_hbsyxw($corders)
	{
		$this->lid_number($corders, '21408', $this->saleCode);
	}
	
	private function lid_klpk($corders)
	{
		$this->lid_sbm_klpk($corders, '54');
	}

	private function lid_cqssc($corders)
	{
		$this->lid_sbm_cqssc($corders, '55', $this->cqsscSaleCode);
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
	
	private function lid_sbm_klpk($corders, $lid)
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
					
					$ggtype = $order['playType'];
					$codes = str_replace(array('#', ','), array('*', ''), $order['codes']);
					$codes = preg_replace('/\^$/is', '', $codes);
					$order['codes'] = $codes;
					$order['ggtype'] = $ggtype;
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

	private function lid_sbm_cqssc($corders, $lid, $saleCode = NULL)
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
					// 大小单双处理
					if($order['playType'] == 1)
					{
						$order['codes'] = $this->changeDxds($order['codes']);
					}
					else
					{
						$order['codes'] = $order['codes'];
					}
					$order['ggtype'] = $saleCode[$order['playType']];
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

	// 1,2,4,5 大小单双 齐汇：1,2,4,5 小大双单
	private function changeDxds($codes)
	{
		$dxds = array(
    		'1'	=> '2',
    		'2'	=> '1',
    		'4'	=> '5',
    		'5' => '4',
    	);
    	$arr = array();
    	foreach (explode(',', $codes) as $num) 
    	{
    		if($dxds[$num])
    		{
    			array_push($arr, $dxds[$num]);
    		}
    	}
    	return implode(',', $arr);
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
					if(in_array($lid, array('21406', '21408')) && ($order['playType'] == 9 || $order['playType'] == 10))
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
					}else if(in_array($lid, array('21406')) && ($order['playType'] == 13 || $order['playType'] == 14 || $order['playType'] == 15))
					{
						$ggtype = '0';
						if(strpos($order['codes'], ','))
						{
							$codes = str_replace(',', '', $order['codes']);
						}else{
						
							$codes = str_replace('*', '', $order['codes']);
						}
						$order['betTnum'] = $order['money'] / (200 * $order['multi']);
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
					if(in_array($lid, array('21406', '21408')))
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
		$issue_format = array('23529' => 2, '51' => 0, '21406' => 0, '33' => 2, '52' => 0, 
		'35' => 2, '10022' => 2, '23528' => 0, '11' => 2, '19' => 2, '21408' => 0, '54' => 0, '55' => 0);
		return substr($issue, $issue_format[$lid]);
	}

	private function formatSplitIssue($issue, $lid, $pre='')
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
					if($order['playType'] == 53){
                        $order['ggtype'] = implode(',', $this->get_ggtype($codess[1]));
                    }else{
                        $order['ggtype'] = $this->ggtype_map[$order['playType']];
                    }
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
			return $this->cmt_comm(1002, $body, array('oids' => $orderids, 'lid' => $lid, 'msgid' => $messageid));
		}
	}
	
	private function getWeekDay($mid)
	{
		$weeks = array('7', '1', '2', '3', '4', '5', '6');
		$week = date('w', strtotime($mid));
		return $weeks[$week];
	}

	// 开奖信息
	public function med_kjResult($issue, $lid)
	{
		$body  = "<?xml version='1.0' encoding='utf-8'?>";
		$body .= "<body>";
		$body .= "<lotteryId>{$this->pctype_map[$lid][0]}</lotteryId>";
		$body .= "<issue>{$issue}</issue>";
		$body .= "</body>";
		return $this->cmt_comm('1003', $body, array('lid' => $lid, 'batch' => "{$lid}-$issue"));
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
		if(in_array($lid, array(42, 43)))
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
					$ticketRes = $this->med_1023($ticket['message_id'], $ticket['lid']);
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
			// 按期次处理
			$issues = $this->CI->ticket_model->getIssuesByCdBonus($this->pctype_map[$lid][1], $this->seller);
			if(!empty($issues))
			{
				foreach ($issues as $issue)
				{
					$splitIssue = $this->CI->ticket_model->paiqiToSplit($issue, $lid);
					$counts = $this->CI->ticket_model->countTicketDetailByLid($lid, $splitIssue, $this->seller);
					if($counts > 0)
					{
						$config = array(
							'cid' 	=>	'1301', 
							'ptype' => 	$this->pctype_map[$lid][0], 
							'issue' => 	$issue,
							'date' 	=> 	date('Ymd'),
							'lid'	=>	$lid
						);
						$ticketRes = $this->med_1012($config);
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
