<?php

/**
 * 善彩异步处理服务类
 * @author Administrator
 *
 */
include_once dirname(__FILE__) . '/ticket_base.php';
class ticket_shancai extends ticket_base
{
	protected $seller = 'shancai';
	// 善彩彩种ID映射
	private $pctype_map = array
	(
		'51'    => array('51', 'ssq'),
	    '57'    => array('14001', 'jxks'),
	    '53'    => array('34001', 'ks'),
	);
	
	private $saleCode = array(
	    '57'   => array(1 => 5, 2 => 8, 3 => 3, 4 => 1, 5 => 9, 6 => 7, 7 => 2, 8 => 6),
	    '53'   => array(1 => 5, 2 => 8, 3 => 3, 4 => 1, 5 => 9, 6 => 7, 7 => 2, 8 => 6),
	);

	public function __construct()
	{
		parent::__construct();
	}

	// 公共POST请求
	private function cmt_comm($service, $reqData, $datas = array())
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
		$parse_url =  parse_url($this->CI->config->item('sctob_pji'));
		$senddatas = [
		    'post_data' => $reqData,
		    'post_ip'   => $parse_url['host'],
		    'back_data' => [
		        'datas' => $datas, 'callfun' => "result_$service", 'pathTail' => $pathTail,
		        'LogHead' => $LogHead, 'failCall' => "fail_$service"
		    ],
		];
		$this->CI->http_client->request($this->CI->config->item('sctob_pji') . '!' . $service, array($this, 'ticketcallback'), $senddatas);
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
	
	private function lid_jxks($corders, $lid)
	{
	    $this->lid_ks($corders, $lid);
	}
	
	private function lid_ks($corders, $lid) 
	{
	    if(!empty($corders))
	    {
	        foreach ($corders as $issue => $orders)
	        {
	            $issue_tmp = $this->formatIssue($issue, $lid);
	            foreach ($orders as $in => $order)
	            {
	                $method = $this->saleCode[$lid][$order['playType']];
	                $playType = 1;
	                switch ($order['playType']) {
	                    case 1:
	                        switch ($order['codes']) {
	                            case 3:
	                            case 18:
	                                $c = (int)$order['codes']/3;
	                                $order['codes'] = "0$c,0$c,0$c";
	                                $method = 3;
	                                break;
	                            default:
	                                $order['codes'] = str_pad($order['codes'], 2, "0", STR_PAD_LEFT);
	                                break;
	                        }
	                        break;
	                    case 2:
	                    case 5:
	                        $order['codes'] = '0';
	                        break;
	                    case 3:
	                    case 4:
	                    case 7:
	                        $codeArr = array();
	                        foreach (explode(',', $order['codes']) as $c) {
	                            array_push($codeArr, str_pad($c, 2, "0", STR_PAD_LEFT));
	                        }
	                        sort($codeArr);
	                        $order['codes'] = implode(',', $codeArr);
	                        break;
	                    case 6:
	                    case 8:
	                        $order['codes'] = str_replace(array(',', '*'), '', $order['codes']);
	                        break;
	                }
	                $order['codes'] = $this->getBetFormat($order['codes'], $playType, $method, 0, $order['multi'], $order['betTnum'], ParseUnit($order['money'], 1));;
	                // 按split小单提票
	                $re = $this->med_bet($order, $lid, $issue_tmp);
	            }
	        }
	    }
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
			return $this->cmt_comm('bet', $reqData, array('sub_order_ids' => array($orders['sub_order_id']), 'lid' => $lid, 'msgid' => $messageid));
		}
	}
	
	/**
	 * 提票请求成功后异步回调处理方法
	 * @param unknown $resData
	 * @param unknown $data
	 * @return boolean
	 */
    protected function result_bet($resData, $datas)
    {
        $lid = $datas['lid'];
        $result = (string)($resData['resultCode']);
        if(in_array($result, array('0000', '3018')))
        {
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticket_submit_time', 'message_id', 'ticket_flag');
            $d_data['sql'] = array();
            $d_data['data'] = array();
            $enum = 0;
            $status = $this->order_status['drawing'];
            array_push($d_data['data'], $datas['sub_order_ids']['0']);
            array_push($d_data['data'], $enum);
            array_push($d_data['data'], $status);
            array_push($d_data['data'], $datas['msgid']);
            array_push($d_data['data'], 4);
            if(!empty($resData['orderId'])){
                array_push($d_data['sql'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?, ?)");
                array_push($fields, 'seller_order_id');
                array_push($d_data['data'], $resData['orderId']);
            }else{
                array_push($d_data['sql'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?)");
            }
            if(!empty($d_data['sql']))
            {
                $cfparams = array(
                    'db' => 'CF',
                    'sql' => $this->ticket_model_order->ticket_succ($fields, $d_data['sql'], $lid),
                    'data' => $d_data['data']
                );

                $this->back_update('execute', $cfparams);
            }

            return true;
        }
        else
        {
            $cfparams = array(
                'db' => 'CF',
                'sql' => $this->ticket_model_order->ticket_fail($lid),
                'data' => array($datas['msgid'], $result, $datas['sub_order_ids'])
            );
            $this->back_update('execute', $cfparams);
            $msg = "对messageId:{$datas['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
            $dbparams = array(
                'db' => 'DB',
                'sql' => $this->ticket_model_order->insertAlert(),
                'data' => array(4, $msg, '善彩合作商提票报警')
            );
            $this->back_update('execute', $dbparams);
            return false;
        }
    }
	
	/**
	 * 提票失败异步回调方法
	 * @param unknown $params
	 */
	protected function fail_bet($params)
	{
	    $cfparams = array(
	        'db' => 'CF',
	        'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
	        'data' => array($params['msgid'], '', $params['sub_order_ids'])
	    );
	    $this->back_update('execute', $cfparams);
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
	        foreach ($orders as $order)
	        {
	            $reqData = array(
	                'lotteryCode'	=>	$this->pctype_map[$order['lid']][0],
	                'agentOrderId'	=>	$order['sub_order_id'],
	                'orderID'		=>	empty($order['seller_order_id']) ? '' : $order['seller_order_id'],
	            );
	            $this->cmt_comm('queryOrder', $reqData, array('sub_order_ids' => array($order['sub_order_id']), 'concel' => $concel, 'lid' => $order['lid'], 'msgid' => $order['message_id']));
	        }
	    }
	}

	// 主动查询出票结果回执
	protected function result_queryOrder($resData, $data)
	{
		// 非竞彩
		if($resData['resultCode'] == '0000')
		{
			$fields = array('ticket_time', 'sub_order_id', 'error_num', 'status', 'ticketId');
    		$datas['s_data'] = array();
    		$datas['d_data'] = array();
    		$concelIds = array();
    		$err_num = "6_{$resData['ticketStatus']}";
    		if($resData['ticketStatus'] == '1')   //出票成功
    		{
    			$err_num = 0;
    			$status = $this->order_status['draw'];
    		}
    		elseif($resData['ticketStatus'] == '-1')//出票失败
    		{
                if(empty($data['concel'])) $concelIds[] = $resData['agentOrderId'];
    			$status = $this->order_status['concel'];
    		}
    		else//出票中
    		{
    			$status = $this->order_status['drawing'];
    			if($data['concel'])
    			{
    				$status = $this->order_status['concel'];
    			}
    		}

    		if($status != $this->order_status['concel'] || $data['concel'])
    		{
    			$ticket_time = $resData['ticketTime'];
    			if($status == $this->order_status['drawing'])
    			{
                    //设置下次查询时间
                    if(in_array($data['lid'], $this->gaoping))
                    {
                        $ticket_time = 'date_add(now(), interval 10 second)';
                    }
                    else
                    {
                        $ticket_time = 'date_add(now(), interval 30 minute)';
                    }
                    array_push($datas['s_data'], "($ticket_time, ?, ?, ?, ?)");
    			}else{
                    array_push($datas['s_data'], "(?, ?, ?, ?, ?)");
                    array_push($datas['d_data'], "{$ticket_time}");
                }
    			array_push($datas['d_data'], "{$resData['agentOrderId']}");
    			array_push($datas['d_data'], $err_num);
    			array_push($datas['d_data'], $status);
    			array_push($datas['d_data'], "{$resData['zxTicketID']}");
    		}
    		
    		$this->CI->load->model('prcworker/ticket_shancai_model');
    		$result = $this->CI->ticket_shancai_model->getSplitResponseSql($fields, $datas, $data['lid']);
    		if($result)
    		{
    		    //组织sql数据交给连接池处理
    		    $this->back_update($result['function'], $result['datas']);
    		}
    		//失败切票商操作
    		if($concelIds)
    		{
    		    $conResult = $this->CI->ticket_shancai_model->getUpdateTicket($concelIds, $data['lid']);
    		    foreach ($conResult as $conData)
    		    {
    		        $this->back_update('execute', $conData);
    		    }
    		}
		}
	}
	
	/**
	 * 查询出票请求失败回调
	 * @param unknown $params
	 */
	protected function fail_queryOrder($params)
	{
	    //TODO  有业务逻辑处理时可处理
	}

}
