<?php

/**
 * 齐汇异步处理服务类
 * @author Administrator
 *
 */
include_once dirname(__FILE__) . '/ticket_base.php';
class ticket_qihui extends ticket_base
{
	protected $seller = 'qihui';
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
	
	protected $ggtype_map = array
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
		'21407' => array('JX11X5', 'jxsyxw'),
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
	
	public function __construct()
	{
	    parent::__construct();
	    $this->CI->load->library('encrypt_qihui');
	}
	
	private function cmt_comm($mdid, $body, $datas = array())
	{
	    if(empty($datas['msgid']))
	    {
	        $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
	        $datas['msgid'] = $UId;
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
		$datas['mdid'] = $mdid;
		$senddatas = [
		    'post_data' => $header,
		    'post_ip'   => (ENVIRONMENT == 'production') ? '114.55.19.94' : '112.124.100.187',
		    'back_data' => [
		        'datas' => $datas, 'callfun' => "result_$mdid", 'pathTail' => $pathTail,
		        'LogHead' => $LogHead, 'failCall' => "fail_$mdid"
		    ],
		];
		
		$this->CI->http_client->request($this->CI->config->item('qhtob_pji'), array($this, 'ticketcallback'), $senddatas);
	}
	
	/**
	 * 查询出票结果
	 * @param unknown $orders
	 * @param string $concel
	 */
	public function med_ticketResult($orders, $concel = false)
	{
	    if(!empty($orders))
	    {
	        $sub_order_ids = array();
	        $lid = 0;
	        $message_id = 0;
	        $body  = "<?xml version='1.0' encoding='utf-8'?>";
	        $body .= "<body>";
	        $body .= "<records>";
	        foreach ($orders as $order)
	        {
	            array_push($sub_order_ids, $order['sub_order_id']);
	            if(empty($lid) || empty($message_id))
	            {
	                $lid = $order['lid'];
	                $message_id = $order['message_id'];
	            }
	            
	            $body .= "<id>{$order['sub_order_id']}</id>";
	        }
	        
	        $body .= "</records>";
	        $body .= "</body>";
	        if(in_array($lid, array('42', '43')))
	        {
	            $this->cmt_comm('1020', $body, array('sub_order_ids' => $sub_order_ids, 'concel' => $concel, 'lid' => $lid, 'msgid' => $message_id));
	        }
	        else
	        {
	            $this->cmt_comm('1006', $body, array('sub_order_ids' => $sub_order_ids, 'concel' => $concel, 'lid' => $lid, 'msgid' => $message_id));
	        }
	    }
	}
	
	/**
	 * 查询出票结果  数字彩
	 * @param unknown $xmlobj
	 * @param unknown $params
	 */
	protected function result_1006($xmlobj, $params)
	{
	    $this->result_ticketResult($xmlobj, $params);
	}
	
	/**
	 * 查询出票结果 竞技彩
	 * @param unknown $xmlobj
	 * @param unknown $params
	 */
	protected function result_1020($xmlobj, $params)
	{
	    $this->result_ticketResult($xmlobj, $params);
	}
	
	/**
	 * 查询出票请求失败回调
	 * @param unknown $params
	 */
	protected function fail_1006($params)
	{
	    //TODO  有业务逻辑处理时可处理
	}
	
	/**
	 * 查询出票请求失败回调
	 * @param unknown $params
	 */
	protected function fail_1020($params)
	{
	    //TODO  有业务逻辑处理时可处理
	}
	
	/**
	 * 查询出票结果
	 * @param unknown $xmlobj
	 * @param unknown $params
	 */
	protected function result_ticketResult($xmlobj, $data)
	{
		if($xmlobj->head->result == 0 && $xmlobj->head->md == md5($xmlobj->body))
		{
			$bdatas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$xmlObj = simplexml_load_string($bdatas);
			if(!empty($xmlObj->records->record))
			{
			    $this->CI->load->model('prcworker/ticket_qihui_model');
			    $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
			    $datas['s_data'] = array();
			    $datas['d_data'] = array();
			    $datas['relation'] = array();
			    $datas['relationConcel'] = array();
			    $concelIds = array();
			    // 乐善奖
			    $lsDatas['s_data'] = array();
			    $lsDatas['d_data'] = array();
			    foreach ($xmlObj->records->record as $records)
			    {
			        $result = (string)$records->result;
			        $sub_order_id = (string)$records->id;
			        //快频彩推迟10s 慢频彩推迟60s
			        $time = in_array($data['lid'], $this->gaoping) ? 10 : 1800;
			        $successTime = empty($records->successTime) ? date('Y-m-d H:i:s', time() + $time) : (string)$records->successTime;
			        $err_num = 0;
			        //出票成功
			        if($result == '0' || $result == '200023')
			        {
			            $status = $this->order_status['draw'];
			        }
			        //出票中
			        elseif ($result == '200021')
			        {
			            $err_num = $result;
			            $status = $this->order_status['drawing'];
			            if($data['concel'])
			            {
			                $status = $this->order_status['concel'];
			                //竞技彩需要设置relation表
			                if($data['mdid'] == 1020)
			                {
			                    array_push($datas['relationConcel'], $sub_order_id);
			                }
			            }
			        }
			        else
			        {
			            $err_num = $result;
			            $status = $this->order_status['concel'];
			            //如果是过期主动查询时 应设置失败
			            if($data['concel'] && $data['mdid'] == 1020)
			            {
			                array_push($datas['relationConcel'], $sub_order_id);
			            }
			        }
			        
			        if(($status == $this->order_status['concel']) && empty($data['concel']))
			        {
			            $concelIds[] = $sub_order_id;
			        }
			        else
			        {
			            array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
			            array_push($datas['d_data'], "{$sub_order_id}");
			            array_push($datas['d_data'], $err_num);
			            array_push($datas['d_data'], $status);
			            array_push($datas['d_data'], $successTime);
			            $ticketId = (string)$records->ticketId;
			            array_push($datas['d_data'], "{$ticketId}");
			        }

			        // 出票成功 - 大乐透乐善奖
			        $expand = (string)$records->expand;
			        if($status == $this->order_status['draw'] && !empty($expand) && (strpos($expand, '+') !== false) && $data['lid'] == '23529')
			        {
			        	$lsCode = str_replace(array(' ', '+'), array(',', '|'), trim($expand));
			        	array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
			        	array_push($lsDatas['d_data'], "{$sub_order_id}");
			        	array_push($lsDatas['d_data'], $data['lid']);
			        	array_push($lsDatas['d_data'], $this->seller);
			        	array_push($lsDatas['d_data'], $lsCode);
			        	array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
			        }
			        
			        if((!empty($records->info->item)) && $data['mdid'] == 1020)
			            $datas['relation']["{$sub_order_id}"] = $records->info;
			    }

			    // 大乐透乐善奖临时表
			    if(!empty($lsDatas['s_data']))
			    {
			    	$lsHandle = $this->CI->ticket_qihui_model->getSplitDetailSql($lsDatas, $data['lid']);
			    	$this->back_update('execute', $lsHandle);
			    }
			    
			    $result = $this->CI->ticket_qihui_model->getSplitResponseSql($fields, $datas, $data['lid']);
			    if($result)
			    {
			        //组织sql数据交给连接池处理
			        $this->back_update($result['function'], $result['datas']);
			    }
			    
			    //失败切票商操作
			    if($concelIds)
			    {
			        $conResult = $this->CI->ticket_qihui_model->getUpdateTicket($concelIds, $data['lid']);
			        foreach ($conResult as $conData)
			        {
			            $this->back_update('execute', $conData);
			        }
			    }
			}
		}
	}
	
	/**
	 * 提票返回处理
	 * @param unknown $xmlobj
	 * @param unknown $params
	 * @return boolean
	 */
	protected function result_1002($xmlobj, $params)
	{
	    $suborderid = $params['sub_order_ids'];
		$result = (string)$xmlobj->head->result;
		if($result == '0')
		{
			$datas = $this->CI->encrypt_qihui->decrypt($xmlobj->body);
			$rxmlobj = simplexml_load_string($datas);
			$splitConcel = array();
			$errArr = array();
			if(!empty($rxmlobj->records->record))
			{
			    $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticket_submit_time', 'message_id', 'ticket_flag');
			    $d_data['sql'] = array();
			    $d_data['data'] = array();
				foreach ($rxmlobj->records->record as $record)
				{
					$res = (string)$record->result;
					$status = $this->order_status['drawing'];
					if($res != '0')
					{
					    $status = $this->order_status['split_ini'];
					    array_push($splitConcel, (string)$record->id);
					    array_push($errArr, $res);
					}
					
					array_push($d_data['sql'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?)");
					array_push($d_data['data'], (string)$record->id);
					array_push($d_data['data'], $res);
					array_push($d_data['data'], $status);
					array_push($d_data['data'], $params['msgid']);
					array_push($d_data['data'], 1);
				}
				
				if(!empty($d_data['sql']))
				{
				    $cfparams = array(
				        'db' => 'CF',
				        'sql' => $this->CI->ticket_model_order->ticket_succ($fields, $d_data['sql'], $params['lid']),
				        'data' => $d_data['data']
				    );
				    $this->back_update('execute', $cfparams);
				}
				
				if(!empty($splitConcel))
				{
				    $errArr = array_unique($errArr);
				    $errnum = implode(',', $errArr);
				    $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$errnum} ,请及时处理。";
				    $dbparams = array(
				        'db' => 'DB',
				        'sql' => $this->CI->ticket_model_order->insertAlert(),
				        'data' => array(4, $msg, '齐汇合作商提票报警')
				    );
				    $this->back_update('execute', $dbparams);
				}
			}
			
			return true;
		}
		else
		{
			$cfparams = array(
			    'db' => 'CF',
			    'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
			    'data' => array($params['msgid'], $result, $suborderid)
			);
			$this->back_update('execute', $cfparams);
			$msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
			$dbparams = array(
			    'db' => 'DB',
			    'sql' => $this->CI->ticket_model_order->insertAlert(),
			    'data' => array(4, $msg, '齐汇合作商提票报警')
			);
			$this->back_update('execute', $dbparams);
			
			return false;
		}
	}
	
	/**
	 * 提票失败异步回调方法
	 * @param unknown $params
	 */
	protected function fail_1002($params)
	{
	    $cfparams = array(
	        'db' => 'CF',
	        'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
	        'data' => array($params['msgid'], '', $params['sub_order_ids'])
	    );
	    $this->back_update('execute', $cfparams);
	}
	
	/**
	 * 提票
	 */
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
	
	private function bdy_1002($orders, $lid, $issue)
	{
		$issue = in_array($lid, array_keys($this->issue_map)) ? '1' : $issue;
		if(!empty($orders))
		{
		    $sub_order_ids = array();
			$messageid = NULL;
			$body  = "<?xml version='1.0' encoding='utf-8'?>";
			$body .= "<body>";
			$body .= "<lotteryId>{$this->pctype_map[$lid][0]}</lotteryId>";
			$body .= "<issue>$issue</issue>";
			$body .= "<records>";
			foreach ($orders as $order)
			{
			    $sub_order_ids[] = $order['sub_order_id'];
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
			return $this->cmt_comm(1002, $body, array('sub_order_ids' => $sub_order_ids, 'lid' => $lid, 'msgid' => $messageid));
		}
	}
	
	private function getWeekDay($mid)
	{
		$weeks = array('7', '1', '2', '3', '4', '5', '6');
		$week = date('w', strtotime($mid));
		return $weeks[$week];
	}
}
