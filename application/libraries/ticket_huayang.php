<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25
 */

class ticket_huayang
{
    private $phone = '13636430451';
    private $id_card = '500236199011290653';
    private $real_name = '李军';
    private $seller = 'huayang';
    private $ptype_map = array
    (
        'SPF' => array('209', 1),
        'RQSPF' => array('210', 1),
        'CBF' => array('211', 3),
        'JQS' => array('212', 1),
        'BQC' => array('213', 3),
    );
    
    private $ggtype_map = array
    (
        '1' => '101',
        '2' => '102',
        '3' => '103',
        '4' => '104',
        '5' => '105',
        '6' => '106',
        '7' => '107',
        '8' => '108',
        '17' => '603',
        '18' => '118',
        '20' => '604',
        '21' => '120',
        '22' => '605',
        '23' => '121',
        '25' => '606',
        '26' => '123',
        '27' => '607',
        '28' => '124',
        '29' => '608',
        '30' => '125',
        '32' => '609',
        '33' => '127',
        '34' => '610',
        '35' => '611',
        '36' => '128',
        '37' => '612',
        '38' => '129',
        '39' => '613',
        '40' => '602',
        '42' => '702',
        '43' => '703',
        '44' => '704',
        '45' => '705',
        '46' => '706',
        '47' => '802',
        '48' => '803',
        '49' => '804',
        '50' => '805',
        '51' => '806',
        '52' => '807',
    );
    
    private $pctype_map = array
    (
        '23529' => array('106', 'dlt'),
        '42'    => array('208', 'jczq'),
    	'21406' => array('112', 'syxw'),
        '21407' => array('113', 'jxsyxw'),
        '56'    => array('126', 'jlks'),
        '21408' => array('124', 'hbsyxw'),
    );
    
    private $issue_map = array
    (
        '42'    => '20000',
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
    
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('tools');
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
            if(in_array($mdid, array('13005', '13010')))
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
                $this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 8);
            }
        }
        else
        {
            $UId = $datas['msgid'];
        }
        // 时间戳
        $timestamp = date('YmdHis', time());
        $digest = $this->getDigest($timestamp, $body);

        $header  = "<?xml version='1.0' encoding='utf-8'?>";
        $header .= "<message version='1.0'>";
        $header .= "<header>";
        $header .= "<messengerid>$UId</messengerid>";
        $header .= "<timestamp>$timestamp</timestamp>";
        $header .= "<transactiontype>$mdid</transactiontype>";
        $header .= "<digest>$digest</digest>";
        $header .= "<agenterid>{$this->CI->config->item('hytob_sellerid')}</agenterid>";
        $header .= "<username>{$this->CI->config->item('hytob_username')}</username>";
        $header .= "</header>";
        $header .= $body;
        $header .= "</message>";

        /*请求前日志记录*/
        $pathTail = "huayang$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "huayang$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
        
        $result = $this->CI->tools->request($this->CI->config->item('hytob_pji'), $header, 20);
        if($this->CI->tools->recode != 200 || empty($result))
        {
            if($datas['concel'] && in_array($mdid, array('13004', '1020')))
            {
                $this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
            }
            return ;
        }
        /*请求返回日志记录*/
        log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
        $datas['result'] = $result;
        $xmlobj = simplexml_load_string($result);
        $rfun = "result_$mdid";
        return $this->$rfun($xmlobj, $datas);
        
    }

    // 消息包的摘要（时间戳+代理密码+消息体）
    private function getDigest($timestamp, $body)
    {
        return md5($timestamp . $this->CI->config->item('hytob_secret') . $body);
    }
    
    // 出票结果的获取（华阳出票商）
    public function med_ticketResult($concel = FALSE, $lid = 0)
    {
        $tickets = $this->CI->ticket_model->getTicketResult($this->seller, $concel, $lid);
        
        if(!empty($tickets))
        {
            foreach ($tickets as $ticket)
            {
                $this->med_13004($ticket['message_id'], $concel, $ticket['lid']);
            }
        }
    }

    private function med_13004($messageId, $concel, $lid)
    {
        $subOrders = $this->CI->ticket_model->getSubOrdersByMsg($messageId, $concel, $lid);

        if(!empty($subOrders))
        {
            $body = "<body>";
            $body .= "<elements>";
            foreach ($subOrders as $subOrder)
            {
                $body .= "<element>";
                $body .= "<id>$subOrder</id>";
                $body .= "</element>";
            }
            $body .= "</elements>";
            $body .= "</body>"; 
            return $this->cmt_comm('13004', $body, array('concel' => $concel, 'lid' => $lid, 'batch' => $messageId));
        }
    }

    // 出票结果查询处理
    private function result_13004($xmlobj, $params)
    {
        if($xmlobj->body->oelement->errorcode == 0)
        {
            $this->CI->load->model('api_huayang_model');
            $body = $xmlobj->body;

            // 0：不可出票，1：可出票状态，2：出票成功，3：出票失败(允许再出票） 4：出票中 5：出票中（体彩中心），6：出票失败（不允许出票）

            $concel = $params['concel'];
            if(!empty($body->elements->element))
            {
                $allData = array();
                $errmsg = array();
                $delaysids = array();

                $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId');
                $datas['s_data'] = array();
                $datas['d_data'] = array();
                $datas['relation'] = array();
                $datas['concelIds'] = array();
                foreach ($body->elements->element as $element)
                {
                    $sub_order_id = (string)$element->id;
                    $lid = $this->pctype_map[$params['lid']][0];
                    if(!empty($sub_order_id))
                    {
                        $orderTicket = array();
                        $tickStatus = (string)$element->status;

                        $err_num = 0;

                        if($tickStatus == '2')
                        {
                            // 出票成功
                            $status = $this->order_status['draw'];
                        }
                        elseif($tickStatus == '6')
                        {
                            // 出票失败
                            $datas['concelIds'][] = $sub_order_id;
                            $status = $this->order_status['concel'];
                        }
                        else
                        {
                            // 出票中
                            $status = $this->order_status['drawing'];
                            if($concel)
                            {
                                $status = $this->order_status['concel'];
                            }
                        }

                        if($status != $this->order_status['concel'] || $concel)
                        {
                            $successTime = (string)$element->tickettime;
                            if($status == $this->order_status['drawing'])
                            {
                                // 出票中
                                $successTime = empty($successTime) ? date('Y-m-d H:i:s', time() + 60) : $successTime;
                            }
                            array_push($datas['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($datas['d_data'], $sub_order_id);
                            array_push($datas['d_data'], $err_num);
                            array_push($datas['d_data'], $status);
                            array_push($datas['d_data'], $successTime);
                            array_push($datas['d_data'], (string)$element->ticketid);
                        }

                        return $this->CI->api_huayang_model->saveResponse($fields, $datas, $lid);
                    }
                }
            }
        }
    }

    // 订单中奖明细
    public function med_ticketBonus($lid = 0)
    {
        $lid = ($lid == 55) ? 0 : $lid;
        $tickets = $this->CI->ticket_model->getTicketBonus($this->seller, $lid);
        if(!empty($tickets))
        {
            foreach ($tickets as $ticket)
            {
                $this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
                $this->med_13011($ticket['message_id'], $ticket['lid']);
            }
        }
    }

    private function med_13011($messageId, $lid)
    {
        $subOrders = $this->CI->ticket_model->getSubOrdersByMsg_bonus($messageId, $lid);
        if(!empty($subOrders))
        {
            $body = "<body>";
            $body .= "<elements>";
            foreach ($subOrders as $subOrder)
            {
                $body .= "<element>";
                $body .= "<id>$subOrder</id>";
                $body .= "</element>";
            }
            $body .= "</elements>";
            $body .= "</body>";
        }
        return $this->cmt_comm('13011', $body, array('lid' => $lid, 'batch' => $messageId));
    }

    // 订单中奖明细处理
    private function result_13011($xmlobj, $params)
    {
        // asXML 对空元素自动闭合 改用正则
        preg_match('/(<body.*?<\/body>)/', $params['result'], $match);
        $body = $match[1] ? $match[1] : '';

        if( $xmlobj->body->oelement->errorcode == 0 && $xmlobj->header->digest == $this->getDigest('', $body) )
        {
            if(!empty($xmlobj->body->elements));
            {
                $lid = $params['lid'];
                $data['s_datas'] = array();
                $data['d_datas'] = array();
                foreach ($xmlobj->body->elements->element as $element)
                {
                    // 0：未开奖 1：未中奖 2：中奖 5：可算奖状态（当做未开奖）
                    if(intval($element->status) == 1 || intval($element->status) == 2)
                    {
                        $cpstate = 3;
                        if(in_array($lid, array('11', '19')))
                        {
                            $cpstate = 1;
                        }
                        array_push($data['s_datas'], '(?, ?, ?, ?, ?)');
                        array_push($data['d_datas'], "$element->id");
                        array_push($data['d_datas'], "$element->prebonusvalue");
                        array_push($data['d_datas'], "$element->bonusvalue");
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

    // 大乐透提票
    private function lid_dlt($corders)
	{
		$this->lid_number($corders, '23529');
	}
        
    // 新11选5提票
    private function lid_jxsyxw($corders)
    {
        $this->lid_number($corders, '21407');
    }
    
    // 易快3提票
    private function lid_jlks($corders)
    {
        $this->lid_number_jlks($corders, '56');
    }
    
    private function lid_number_jlks($corders, $lid)
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
                    $order['isChase'] = $order['playType'];
                    $order['ggtype'] = 0;
                    switch ($order['playType'])
                    {
                        case 1:
                            if($order['codes'] == '3'){
                                $order['codes'] = '1*1*1';
                                $order['isChase'] = 2 ;
                                $order['ggtype'] = 1;
                            }elseif($order['codes'] == '18'){
                                $order['codes'] = '6*6*6';
                                $order['isChase'] = 2 ;
                                $order['ggtype'] = 1;
                            }else{
                                $order['codes'] = $order['codes'];
                            }
                            break;
                        case 2:
                            $order['codes'] = '1*2*3*4*5*6';
                            break;
                        case 3:
                            $order['codes'] = str_replace(',','*',$order['codes']);
                            $order['isChase'] = 2 ;
                            $order['ggtype'] = 1;
                            break;
                        case 4:
                            $order['codes'] = str_replace(',','*',$order['codes']);
                            $order['isChase'] = 3 ;
                            break;
                        case 5:
                            $order['codes'] = '1*2*3*4*5*6';
                            $order['isChase'] = 4 ;
                            break;  
                        case 6:
                            $code = substr($order['codes'],0,3);
                            $order['codes'] = str_replace(',','*',$code);
                            $order['isChase'] = 5 ;
                            $order['ggtype'] = 1;
                            break;  
                        case 7:
                            $order['codes'] = str_replace(',','*',$order['codes']);
                            $order['isChase'] = 5 ;
                            break;  
                        case 8:
                            $code = substr($order['codes'],0,3);
                            $order['codes'] = str_replace(',','*',$code);
                            $order['isChase'] = 6 ;
                            break;                  
                        default:
                            break;
                    }
                    array_push($reorders, $order);
                    ++$count;
                }
                if($count > 0)
                {
                    $re = $this->bdy_13005($reorders, $lid, $issue_tmp);
                }
            }
        }
    }    
    
	// 数字彩提票
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
                                       $codes = str_replace(array('#', ','), array('*', ''), $order['codes']);
                                        if($lid == 21407)
                                        {
                                            $order['isChase'] = $order['playType'];
                                            if($order['playType']==9 || $order['playType']==10)
                                            {
                                                $codes = str_replace(array('*'), array(''), $order['codes']);
                                            }
                                        }    
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
                                                    // 胆拖格式 01020315*080931|02040810 前后区无胆码需补齐*
                                                    $codes = $this->danCodesFormat($codes);
						}
					}

					$order['codes'] = $codes;
					$order['ggtype'] = $ggtype;

					array_push($reorders, $order);
					++$count;
				}
				if($count > 0)
				{
					$re = $this->bdy_13005($reorders, $lid, $issue_tmp);
				}
			}
		}
	}

    // 大乐透胆拖格式补齐
    private function danCodesFormat($codes)
    {
        $tplArr = explode('|', $codes);
        $codeArr = array();
        foreach ($tplArr as $code) 
        {
            if(strpos($code, '*'))
            {
                $codeArr[] = $code;
            }
            else
            {
                $codeArr[] = '*' . $code;
            }
        }
        return implode('|', $codeArr);
    }

    // 期次格式
    private function formatIssue($issue, $lid, $pre='')
    {
        $issue_format = array('23529' => 2, '21406' => 0, '21407' => 0, '56' => 2);
        if(empty($pre))
        {
            return substr($issue, $issue_format[$lid]);
        }
        else
        {
            return "$pre$issue";
        }
    }

    // 数字彩投注
    private function bdy_13005($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $orderids = array();
            $messageid = NULL;
            $body = "<body>";
            $body .= "<elements>";
            foreach ($orders as $order)
            {
                $orderids[$order['orderId']][] = $order['sub_order_id'];
                if(empty($messageid)) $messageid = $order['message_id'];
                $body .= "<element>";
                $body .= "<ticketuser>{$this->real_name}</ticketuser>";
                $body .= "<identify>{$this->id_card}</identify>";
                $body .= "<phone>{$this->phone}</phone>";
                $body .= "<id>{$order['sub_order_id']}</id>";
                $body .= "<lotteryid>{$this->pctype_map[$lid][0]}</lotteryid>";
                $body .= "<issue>$issue</issue>";
                $body .= "<childtype>{$order['isChase']}</childtype>";
                $body .= "<saletype>{$order['ggtype']}</saletype>";
                $body .= "<lotterycode>{$order['codes']}</lotterycode>";
                $body .= "<appnumbers>{$order['multi']}</appnumbers>";
                $body .= "<lotterynumber>{$order['betTnum']}</lotterynumber>";
                $body .= "<lotteryvalue>{$order['money']}</lotteryvalue>";
                $body .= "</element>";
            }
            $body .= "</elements>";
            $body .= "</body>";
            return $this->cmt_comm(13005, $body, array('oids' => $orderids, 'lid' => $lid, 'msgid' => $messageid));
        }
    }
    
    /**
     * 竞技彩
     * @param unknown $xmlobj
     * @param unknown $params
     */
    private function result_13010($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }
    
    /**
     * 数字彩
     * @param unknown $xmlobj
     * @param unknown $params
     */
    private function result_13005($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }

    // 数字彩投注结果处理
    private function result_betting($xmlobj, $params)
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
        $result = (string)$xmlobj->body->oelement->errorcode;
        if($result == '0')
        {
            $errArr = array();
            $succ_ids = array();
            if(!empty($xmlobj->body->elements))
            {
                $updatas['s_data'] = array();
                $updatas['d_data'] = array();
                foreach ($xmlobj->body->elements->element as $element)
                {
                    $res = (string)$element->errorcode;
                    $ltappid = (string)$element->ltappid;
                    if($res == '0' || $res == '10032')
                    {
                        $succ_ids[] = (string)$element->id;
                    }
                    else 
                    {
                        array_push($updatas['s_data'], '(?, ?, 0)');
                        array_push($updatas['d_data'], (string)$element->id);
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
                $this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '华阳合作商提票报警');
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
            $this->CI->ticket_model->insertAlert(4, $params['msgid'], $msg, '华阳合作商提票报警');
            return false;
        }
    }
    
    /**
     * 竞彩足球提票
     * @param unknown $corders
     */
    private function lid_jczq($corders)
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
                    $coArr = array();
                    $ccids = array();
                    $lotteryid = $this->pctype_map[$order['lid']]['0'];
                    foreach ($betcbts as $betcbt)
                    {
                        $fields = explode(',', $betcbt);
                        $ccid = substr($fields[0], 2);
                        $ccid = join('-', str_split($ccid,6));
                        array_push($ccids, $ccid);
                        if($order['playType'] == '1')
                        {
                            //单关特殊处理
                            $lotteryid = $this->ptype_map[$fields['1']]['0'];
                            $codStr = $ccid;
                        }
                        else 
                        {
                            $codStr = $this->ptype_map[$fields['1']]['0'] . '^' . $ccid;
                        }
                        $plvs = explode('/', $fields[2]);
                        $zmcde = '';
                        foreach ($plvs as $plv)
                        {
                            $cde = substr($plv, 0, $this->ptype_map[$fields['1']]['1']);
                            $cde = preg_replace('/[^\d]/is', '', $cde);
                            $zmcde .= $cde . ',';
                        }
                        
                        $zmcde = preg_replace('/\,$/is', '', $zmcde);
                        $codStr .= '(' . $zmcde . ')';
                        array_push($coArr, $codStr);
                    }
                    $order['lotterycode'] = implode(';', $coArr);
                    $order['lotteryid'] = $lotteryid;
                    //自由过关的特殊处理逻辑
                    if($order['playType'] == 53){
                        $order['childtype'] = implode('^', $this->get_ggtype($codess[1]));
                    }else{
                        $order['childtype'] = $this->ggtype_map[$order['playType']];
                    }
                    $order['saletype'] = $order['isChase'];
                    $order['betlotterymode'] = implode('^', array(min($ccids), max($ccids)));
                    array_push($reorders, $order);
                    ++$count;
                }
                
            }

            if($count > 0)
            {
                $re = $this->bdy_13010($reorders, 42, $this->issue_map['42']);
            }
        }
    }

    private function get_ggtype($codes)
    {
        preg_match('/ZS=\d+,BS=\d+,JE=\d+,GG=(\d+)/is', $codes, $matches);
        if(!empty($matches[1])){
            $ggarr = array();
            $ggmaps = array(2, 3, 4, 5, 6, 7, 8);
            foreach ($ggmaps as $ggtype){
                if($matches[1] & (1 << ($ggtype - 2))){
                   array_push($ggarr, $this->ggtype_map[$ggtype]);
                }
            }
            return $ggarr;
        }
    }
    
    private function bdy_13010($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $orderids = array();
            $messageid = NULL;
            $body = "<body>";
            $body .= "<elements>";
            foreach ($orders as $order)
            {
                $orderids[$order['orderId']][] = $order['sub_order_id'];
                if(empty($messageid)) $messageid = $order['message_id'];
                $body .= "<element>";
                $body .= "<ticketuser>{$this->real_name}</ticketuser>";
                $body .= "<identify>{$this->id_card}</identify>";
                $body .= "<id>{$order['sub_order_id']}</id>";
                $body .= "<lotteryid>{$order['lotteryid']}</lotteryid>";
                $body .= "<issue>{$issue}</issue>";
                $body .= "<childtype>{$order['childtype']}</childtype>";
                $body .= "<saletype>{$order['saletype']}</saletype>";
                $body .= "<lotterycode>{$order['lotterycode']}</lotterycode>";
                $body .= "<appnumbers>{$order['multi']}</appnumbers>";
                $body .= "<lotterynumber>{$order['betTnum']}</lotterynumber>";
                $body .= "<lotteryvalue>{$order['money']}</lotteryvalue>";
                $body .= "<betlotterymode>{$order['betlotterymode']}</betlotterymode>";
                $body .= "</element>";
            }
            $body .= "</elements>";
            $body .= "</body>";
            return $this->cmt_comm(13010, $body, array('oids' => $orderids, 'lid' => $lid, 'msgid' => $messageid));
        }
    }
    
    public function med_kjResult($issue, $lid)
    {
        $this->med_13007($issue, $lid);
    }
    
    private function med_13007($issue, $lid)
    {
        if($lid == 56)
        {
            $issue = $this->formatIssue($issue, $lid);
        }
        $body = "<body>";
        $body .= "<elements>";
        $body .= "<element>";
        $body .= "<lotteryid>".$this->pctype_map[$lid][0]."</lotteryid>";
        $body .= "<issue>".$issue."</issue>";
        $body .= "</element>";
        $body .= "</elements>";
        $body .= "</body>";
        return $this->cmt_comm('13007', $body, array('issue' => $issue, 'lid' => $lid, 'batch' => "{$lid}-$issue"));
    }
    
    private function result_13007($xmlobj, $data)
    {
        $result = $xmlobj->body->oelement->errorcode;
        $issue = $data['issue'];
        if ($data['lid'] == 56) {
            $issue = '20' . $issue;
        }
        $lname = $this->pctype_map[$data['lid']][1];
        if($result == 0)
        {
                $awardNum = $xmlobj->body->elements->element->bonuscode;
                if($awardNum != '')
                {
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
    
    private function getBonusDetail($lid)
    {
            $bonusDetail = array();
            switch ($lid) 
            {
                    case 21406:
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
                    	$detail['lx3']['q3']['dzjj'] = '1384';
                    	$detail['lx3']['z3']['dzjj'] = '214';
                    	$detail['lx3']['r3']['dzjj'] = '19';
                    	$detail['lx4']['r44']['dzjj'] = '154';
                    	$detail['lx4']['r43']['dzjj'] = '19';
                    	$detail['lx5']['r55']['dzjj'] = '1080';
                    	$detail['lx5']['r54']['dzjj'] = '90';
                    case 21407:
                    case 21408:
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
                    case 56:
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
                    default:
                            break;
            }
            return $bonusDetail;
    }

    /**
     * 获取吉林快三期次
     * @param int $lid
     */
    public function met_getIssue($lid)
    {
        $this->CI->load->model('task_model');
        $issue = $this->CI->task_model->getCurJLKS();
        if (isset($issue[0]['issue'])) {
            $this->new_med_13007($issue[0]['issue'], $lid);
        }
    }
    
    private function new_med_13007($issue, $lid)
    {
        if($lid == 56)
        {
            $issue = $this->formatIssue($issue, $lid);
        }
        $body = "<body>";
        $body .= "<elements>";
        $body .= "<element>";
        $body .= "<lotteryid>".$this->pctype_map[$lid][0]."</lotteryid>";
        $body .= "<issue>".$issue."</issue>";
        $body .= "</element>";
        $body .= "</elements>";
        $body .= "</body>";
        return $this->new_cmt_comm('13007', $body, array('issue' => $issue, 'lid' => $lid, 'batch' => "{$lid}-$issue"));
    }

    private function new_cmt_comm($mdid, $body, $datas = array())
    {
        //测试商户编号TWOTOFIVE
        if(empty($datas['msgid']))
        {
            $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
            $datas['msgid'] = $UId;
            if(in_array($mdid, array('13005', '13010')))
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
                $this->CI->ticket_model->saveMessageId($sub_order_ids, $UId, $datas['lid'], 8);
            }
        }
        else
        {
            $UId = $datas['msgid'];
        }
        // 时间戳
        $timestamp = date('YmdHis', time());
        $digest = $this->getDigest($timestamp, $body);

        $header  = "<?xml version='1.0' encoding='utf-8'?>";
        $header .= "<message version='1.0'>";
        $header .= "<header>";
        $header .= "<messengerid>$UId</messengerid>";
        $header .= "<timestamp>$timestamp</timestamp>";
        $header .= "<transactiontype>$mdid</transactiontype>";
        $header .= "<digest>$digest</digest>";
        $header .= "<agenterid>{$this->CI->config->item('hytob_sellerid')}</agenterid>";
        $header .= "<username>{$this->CI->config->item('hytob_username')}</username>";
        $header .= "</header>";
        $header .= $body;
        $header .= "</message>";

        /*请求前日志记录*/
        $pathTail = "huayang$mdid/" . date('YmdH');
        if(empty($datas['batch'])) $datas['batch'] = $UId;
        $LogHead = "{$datas['batch']}-" . md5($mdid . microtime(true));
        log_message('LOG', $LogHead . $pathTail, "huayang$mdid/$mdid");
        log_message('LOG', "{$LogHead}[REQ]: " . $header, $pathTail);
        
        $result = $this->CI->tools->request($this->CI->config->item('hytob_pji'), $header, 20);
        if($this->CI->tools->recode != 200 || empty($result))
        {
            if($datas['concel'] && in_array($mdid, array('13004', '1020')))
            {
                $this->ticket_model->ticketConcel_searchFail($UId, $datas['lid']);
            }
            return ;
        }
        /*请求返回日志记录*/
        log_message('LOG', "{$LogHead}[RES]: " . $result, $pathTail);
        $datas['result'] = $result;
        $xmlobj = simplexml_load_string($result);
        $rfun = "new_result_$mdid";
        return $this->$rfun($xmlobj, $datas);
    }
    
    private function new_result_13007($xmlobj, $data)
    {
        $result = $xmlobj->body->oelement->errorcode;
        $issue = $data['issue'];
        if ($data['lid'] == 56) {
            $issue = '20' . $issue;
        }
        if($result == 0)
        {
                if($awardNum == '')
                {
                    $start = (string)$xmlobj->body->elements->element->starttime;
                    $end = (string)$xmlobj->body->elements->element->endtime;
                    $issues = array(
                        $issue => array(
                            'start' => $start,
                            'end' => $end
                        )
                    );
                    $this->CI->load->model('issue_model');
                    $this->CI->issue_model->compareIssue($data['lid'], $issues);
                }
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
        // 按单处理
        $handle = true;
        $tickets = $this->CI->ticket_model->getTicketBonusByLid($this->seller, $lid);
        while ($handle && !empty($tickets)) 
        {
            $succNum = 0;
            foreach ($tickets as $ticket)
            {
                $this->CI->ticket_model->saveBonustime($ticket['message_id'], $ticket['lid']);
                $ticketRes = $this->med_13011($ticket['message_id'], $ticket['lid']);
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
