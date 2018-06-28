<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25
 */
include_once dirname(__FILE__) . '/ticket_base.php';
class ticket_huayang extends ticket_base
{
    protected $seller = 'huayang';
    private $ptype_map = array
    (
        'SPF' => array('209', 1), 'RQSPF' => array('210', 1), 'CBF' => array('211', 3),
        'JQS' => array('212', 1), 'BQC' => array('213', 3),
    );
    
    protected $ggtype_map = array
    (
        '1' => '101', '2' => '102', '3' => '103', '4' => '104', '5' => '105', '6' => '106', '7' => '107',
        '8' => '108', '17' => '603', '18' => '118', '20' => '604', '21' => '120', '22' => '605', '23' => '121',
        '25' => '606', '26' => '123', '27' => '607', '28' => '124', '29' => '608', '30' => '125', '32' => '609',
        '33' => '127', '34' => '610', '35' => '611', '36' => '128', '37' => '612', '38' => '129', '39' => '613',
        '40' => '602', '42' => '702', '43' => '703', '44' => '704', '45' => '705', '46' => '706', '47' => '802',
        '48' => '803', '49' => '804', '50' => '805', '51' => '806', '52' => '807'
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
    
    public function __construct()
    {
        parent::__construct();
    }

    private function getSuborderId($orderids){
        $sub_order_ids = array();
        foreach ($orderids as $oids => $subodis)
        {
            foreach ($subodis as $subodi)
            {
                array_push($sub_order_ids, $subodi);
            }
        }
        return $sub_order_ids;
    }

    private function cmt_comm($mdid, $body, $datas = array())
    {
        //测试商户编号TWOTOFIVE
        if(empty($datas['msgid']))
        {
            $UId = $this->CI->tools->getIncNum('UNIQUE_KEY');
            $datas['msgid'] = $UId;
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

        $senddatas = [
            'post_data' => $header,
            'post_ip'   => (ENVIRONMENT == 'production' ? '124.202.131.178' : '123.126.87.90'),
            'back_data' => [
                'datas' => $datas, 'callfun' => "result_$mdid", 'pathTail' => $pathTail,
                'LogHead' => $LogHead, 'failCall' => "fail_$mdid"
            ],
        ];
        $this->httpasyc->request($this->CI->config->item('hytob_pji'), array($this, 'ticketcallback'), $senddatas);
    }


    // 消息包的摘要（时间戳+代理密码+消息体）
    private function getDigest($timestamp, $body)
    {
        return md5($timestamp . $this->CI->config->item('hytob_secret') . $body);
    }
    
    // 出票结果的获取（华阳出票商）
    public function med_ticketResult($orders, $concel = FALSE)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $lid = 0;
            $message_id = 0;
            $body = "<body>";
            $body .= "<elements>";
            foreach ($orders as $order)
            {
                array_push($sub_order_ids, $order['sub_order_id']);
                $body .= "<element>";
                $body .= "<id>{$order['sub_order_id']}</id>";
                $body .= "</element>";
                if(empty($lid) || empty($message_id))
                {
                    $lid = $order['lid'];
                    $message_id = $order['message_id'];
                }
            }
            $body .= "</elements>";
            $body .= "</body>";
            return $this->cmt_comm('13004', $body, array('sub_order_ids' => $sub_order_ids, 'concel' => $concel, 'lid' => $lid, 'batch' => $message_id));
        }
    }

    // 出票结果查询处理
    protected function result_13004($xmlobj, $params)
    {
        if($xmlobj->body->oelement->errorcode == 0)
        {
            $this->CI->load->model('prcworker/ticket_huayang_model');
            $body = $xmlobj->body;

            // 0：不可出票，1：可出票状态，2：出票成功，3：出票失败(允许再出票） 4：出票中 5：出票中（体彩中心），6：出票失败（不允许出票）

            $concel = $params['concel'];
            if(!empty($body->elements->element))
            {
                $concelIds = array();
                $fields = array('ticket_time', 'sub_order_id', 'error_num', 'status', 'ticketId');
                $datas['s_data'] = array();
                $datas['d_data'] = array();
                $datas['relation'] = array();
                $datas['relationConcel'] = array();
                // 乐善奖
                $lsDatas['s_data'] = array();
                $lsDatas['d_data'] = array();
                foreach ($body->elements->element as $element)
                {
                    $sub_order_id = (string)$element->id;
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
                            if(empty($concel)) $concelIds[] = $sub_order_id;
                            array_push($datas['relationConcel'], $sub_order_id);
                            $status = $this->order_status['concel'];
                        }
                        else
                        {
                            // 出票中
                            $status = $this->order_status['drawing'];
                            if($concel)
                            {
                                $status = $this->order_status['concel'];
                                array_push($datas['relationConcel'], $sub_order_id);
                            }
                        }
                        if($status != $this->order_status['concel'] || $concel)
                        {
                            $successTime = (string)$element->tickettime;
                            if($status == $this->order_status['drawing'])
                            {
                                //设置下次查询时间
                                if(in_array($params['lid'], $this->gaoping))
                                {
                                    $successTime = 'date_add(now(), interval 10 second)';
                                }
                                else
                                {
                                    $successTime = 'date_add(now(), interval 30 minute)';
                                }
                                array_push($datas['s_data'], "($successTime, ?, ?, ?, ?)");
                            }else{
                                array_push($datas['s_data'], "(?, ?, ?, ?, ?)");
                                array_push($datas['d_data'], $successTime);
                            }
                            array_push($datas['d_data'], $sub_order_id);
                            array_push($datas['d_data'], $err_num);
                            array_push($datas['d_data'], $status);
                            array_push($datas['d_data'], (string)$element->ticketid);
                            $spvalue = (string)$element->spvalue;
                            if(!empty($spvalue))
                            {
                                $datas['relation']["{$element->lotteryid}"]["{$sub_order_id}"] = $spvalue;
                            }
                        }

                        // 出票成功 - 大乐透乐善奖
                        $lsCode = (string)$element->lscode;
                        if($status == $this->order_status['draw'] && !empty($lsCode) && $params['lid'] == '23529')
                        {
                            $lsArr = explode(',', $lsCode);
                            $lsCode = implode(',', array_slice($lsArr, 0, 5)) . '|' . implode(',', array_slice($lsArr, 5, 2));

                            array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($lsDatas['d_data'], $sub_order_id);
                            array_push($lsDatas['d_data'], $params['lid']);
                            array_push($lsDatas['d_data'], $this->seller);
                            array_push($lsDatas['d_data'], $lsCode);
                            array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                        }
                    }
                }

                // 大乐透乐善奖临时表
                if(!empty($lsDatas['s_data']))
                {
                    $lsHandle = $this->CI->ticket_huayang_model->getSplitDetailSql($lsDatas, $params['lid']);
                    $this->back_update('execute', $lsHandle);
                }

                $result = $this->CI->ticket_huayang_model->getSplitResponseSql($fields, $datas, $params['lid']);
                if($result){
                    $this->back_update($result['function'], $result['datas']);
                }
                //失败切票商操作
                if($concelIds)
                {
                    $conResult = $this->CI->ticket_huayang_model->getUpdateTicket($concelIds, $params['lid']);
                    foreach ($conResult as $conData)
                    {
                        $this->back_update('execute', $conData);
                    }
                }
            }
        }
    }
    
    /**
     * 查询出票请求失败回调
     * @param unknown $params
     */
    protected function fail_13004($params)
    {
        //TODO  有业务逻辑处理时可处理
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
	
	// 山东11选5提票
	private function lid_syxw($corders)
	{
		$this->lid_number($corders, '21406');
	}
        
    // 新11选5提票
    private function lid_jxsyxw($corders)
    {
        $this->lid_number($corders, '21407');
    }
    
    
    // 新11选5提票
    private function lid_hbsyxw($corders)
    {
        $this->lid_number($corders, '21408');
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
                    if(in_array($lid, array(21406, 21407, 21408)))
                    {
                        $order['isChase'] = $order['playType'];
                        if($order['playType']==9 || $order['playType']==10 || $order['playType']==13)
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
        $issue_format = array('23529' => 2, '21407' => 0, '21406' => 0, '56' => 2, '21408' => 0);
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
            $sub_order_ids = array();
            $messageid = NULL;
            $body = "<body>";
            $body .= "<elements>";
            foreach ($orders as $order)
            {
                $sub_order_ids[] = $order['sub_order_id'];
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
            return $this->cmt_comm(13005, $body, array('sub_order_ids' => $sub_order_ids, 'lid' => $lid, 'msgid' => $messageid));
        }
    }
    
    /**
     * 竞技彩
     * @param unknown $xmlobj
     * @param unknown $params
     */
    protected function result_13010($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }
    
    /**
     * 数字彩
     * @param unknown $xmlobj
     * @param unknown $params
     */
    protected function result_13005($xmlobj, $params)
    {
        $this->result_betting($xmlobj, $params);
    }

    // 投注结果处理
    private function result_betting($xmlobj, $params)
    {
        $result = (string)$xmlobj->body->oelement->errorcode;
        if($result == '0')
        {
            $errArr = array();
            $succ_ids = array();
            if(!empty($xmlobj->body->elements))
            {
                $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticket_submit_time', 'message_id', 'ticket_flag');
                $updatas['s_data'] = array();
                $updatas['d_data'] = array();
                $enum = 0;
                $errArr = array();
                foreach ($xmlobj->body->elements->element as $element)
                {
                    $res = (string)$element->errorcode;
                    $ltappid = (string)$element->ltappid;
                    if($res == '0' || $res == '10032')
                    {
                        $status = $this->order_status['drawing'];
                    }
                    else 
                    {
                        $status = $this->order_status['split_ini'];
                        $errArr[$res] = $res;
                    }
                    array_push($updatas['s_data'], "(?, ?, ?, date_add(now(), interval 1 minute), now(), ?, ?)");
                    array_push($updatas['d_data'], (string)$element->id);
                    array_push($updatas['d_data'], $res);
                    array_push($updatas['d_data'], $status);
                    array_push($updatas['d_data'], $params['msgid']);
                    array_push($updatas['d_data'], 8);
                }
            }
            
            if(!empty($updatas['s_data']))
            {
                $cfparams = array(
                    'db' => 'CF',
                    'sql' => $this->CI->ticket_model_order->ticket_succ($fields, $updatas['s_data'], $params['lid']),
                    'data' => $updatas['d_data']
                );
                $this->back_update('execute', $cfparams);
            }

            if(!empty($errArr))
            {
                $errArr = array_unique($errArr);
                $errNum = implode(',', $errArr);
                $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$errNum} ,请及时处理。";
                $dbparams = array(
                    'db' => 'DB',
                    'sql' => $this->CI->ticket_model_order->insertAlert(),
                    'data' => array(4, $msg, '华阳合作商提票报警')
                );
                $this->back_update('execute', $dbparams);
            }
            return true;
        }
        else
        {
            $cfparams = array(
                'db' => 'CF',
                'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
                'data' => array($params['msgid'], $result, $params['sub_order_ids'])
            );
            $this->back_update('execute', $cfparams);
            $msg = "对messageId:{$params['msgid']}进行提票操作时合作商 {$this->seller} 返回状态码：{$result} ,请及时处理。";
            $dbparams = array(
                'db' => 'DB',
                'sql' => $this->CI->ticket_model_order->insertAlert(),
                'data' => array(4, $msg, '华阳合作商提票报警')
            );
            $this->back_update('execute', $dbparams);
            return false;
        }
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_13005($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败处理
     * @param unknown $params
     * @return unknown
     */
    protected function fail_13010($params)
    {
        return $this->fail_betting($params);
    }
    
    /**
     * 提票失败异步回调方法
     * @param unknown $params
     */
    protected function fail_betting($params)
    {
        $cfparams = array(
            'db' => 'CF',
            'sql' => $this->CI->ticket_model_order->ticket_fail($params['lid']),
            'data' => array($params['msgid'], '', $params['sub_order_ids'])
        );
        $this->back_update('execute', $cfparams);
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
                        $order['saletype'] = 1;
                    }else{
                        $order['childtype'] = $this->ggtype_map[$order['playType']];
                        $order['saletype'] = $order['isChase'];
                    }
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

    private function bdy_13010($orders, $lid, $issue)
    {
        if(!empty($orders))
        {
            $sub_order_ids = array();
            $messageid = NULL;
            $body = "<body>";
            $body .= "<elements>";
            foreach ($orders as $order)
            {
                $sub_order_ids[] = $order['sub_order_id'];
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
            return $this->cmt_comm(13010, $body, array('sub_order_ids' => $sub_order_ids, 'lid' => $lid, 'msgid' => $messageid));
        }
    }
}
