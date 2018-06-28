<?php

class Notice extends CI_Controller 
{
    public function __construct() 
    {
        parent::__construct();
        $this->load->config('order');
        $this->load->library('tools');
   		$this->ordercfg = $this->config->item("cfg_orders");
   		$this->sellerid = $this->config->item('cdtob_sellerid');
   		$this->lid_map = array
			(
				'30'    => '42',
				'31'    => '43',
				'01'	=> '51',
				'03'	=> '52',
				'07' 	=> '23528',
				'98'	=> '44', 
				'99'	=> '45',
				'56'    => '21406',
				'54'    => '21407',
				'58'    => '21408',
				'66'    => '53',
                '57'    => '54',
			    '50'    => '23529',
			    '51'    => '10022',
			    '52'    => '35',
			    '53'    => '33',
			    '55'    => '21421',
			    '80'    => '11',
			    '81'    => '19',
			);
    }
    
    public function index($seller)
    {
    	if(method_exists($this, $seller))
    	{
    		$this->$seller();
    	}
    }
    
    /**
     * 菜豆出票接口
     */
    private function caidou()
    {
    	$content = $_POST['xml'];
    	$sign    = $_POST['sign'];
    	$xmlObj = simplexml_load_string($content);
    	if(md5($content . $this->config->item('cdtob_secret')) == $sign)
    	{
	    	if(intval($xmlObj['agent']) == $this->sellerid && intval($xmlObj['type']) == 30000)
	    	{
		    	$fields = array('sub_order_id', 'error_num', 'status', 'ticket_money', 'ticket_time', 'ticketId', 'message_id', 'ticket_submit_time');
		    	$datas['s_data'] = array();
				$datas['d_data'] = array();
				$datas['relation'] = array();
				$datas['relationConcel'] = array();
				$datas['concelIds'] = array();
				$rexml = '';
				$lid = 0;
                // 乐善奖
                $lsDatas['s_data'] = array();
                $lsDatas['d_data'] = array();
		    	foreach ($xmlObj->ticket as $ticket)
		    	{
			    	$result = intval($ticket['code']);
			    	$err_num = "5_{$result}";
			    	$lid = "{$ticket['gid']}";
		    		if($result == 0)   //出票成功
		    		{
		    			$err_num = 0;
		    			$status = $this->ordercfg['draw'];
		    		}
		    		elseif(in_array($result, array(1, 2)))//限号出票失败
		    		{
		    			$datas['concelIds'][] = (string)$ticket['apply'];
		    			$rexml .= "<ticket gid=\"{$ticket['gid']}\" pid=\"{$ticket['pid']}\" bid=\"{$ticket['bid']}\" apply=\"{$ticket['apply']}\" code=\"0\" desc=\"成功\" />";
		    			continue;
		    		}
		    		else//出票中
		    		{
		    			$status = $this->ordercfg['drawing'];
		    		}
		    		
		    		array_push($datas['s_data'], '(?, ?, ?, ?, ?, ?, ?, ?)');
		    		array_push($datas['d_data'], "{$ticket['apply']}");
		    		array_push($datas['d_data'], $err_num);
		    		array_push($datas['d_data'], $status);
		    		array_push($datas['d_data'], intval($ticket['money']) * 100);
		    		array_push($datas['d_data'], "{$ticket['tdate']}");
		    		array_push($datas['d_data'], "{$ticket['tid']}");
		    		$message_id = $this->tools->getIncNum('UNIQUE_KEY');
		    		array_push($datas['d_data'], "{$message_id}");
		    		array_push($datas['d_data'], date('Y-m-d H:i:s'));
		    		if(!empty($ticket['memo']))
                    {
                        // 出票成功 - 大乐透乐善奖
                        if($status == $this->ordercfg['draw'] && $this->lid_map[$lid] == '23529')
                        {
                            array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                            array_push($lsDatas['d_data'], "{$ticket['apply']}");
                            array_push($lsDatas['d_data'], $this->lid_map[$lid]);
                            array_push($lsDatas['d_data'], 'caidou');
                            array_push($lsDatas['d_data'], "{$ticket['memo']}");
                            array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                        }
                        else
                        {
                            $datas['relation']["{$ticket['gid']}"]["{$ticket['apply']}"] = (in_array($ticket['gid'], array('98', '99'))) ? "{$ticket['memo']}|{$ticket['pid']}" : "{$ticket['memo']}";
                        }
                    }
		    		$rexml .= "<ticket gid=\"{$ticket['gid']}\" pid=\"{$ticket['pid']}\" bid=\"{$ticket['bid']}\" apply=\"{$ticket['apply']}\" code=\"0\" desc=\"成功\" />";
		    	}
		    	$this->load->model('api_caidou_model');
                if(!empty($lsDatas['s_data']))
                {
                    $this->api_caidou_model->saveSplitDetail($lsDatas, $this->lid_map[$lid]);
                }
		    	$re = $this->api_caidou_model->saveResponse($fields, $datas, $this->lid_map[$lid]);
		    	if($re)
		    	{
		    		echo "<response agent=\"{$this->sellerid}\" type=\"30000\">$rexml</response>";
		    	}
				log_message('LOG', $content, "caidou30000/30000");
	    	}
    	}
    }
    
    private function qihui()
    {
    	$this->load->model('api_qihui_model');
    	$this->load->library('encrypt_qihui');
    	$result = $GLOBALS['HTTP_RAW_POST_DATA'];
    	$xmlObj = simplexml_load_string($result);
		$data_sign = md5($xmlObj->body);
		if($data_sign == $xmlObj->head->md)
		{
			$command = (string)$xmlObj->head->command;
			$method = "qihui_$command";
			if(method_exists($this, $method))
			{
				$this->$method($xmlObj);
			}
			else 
			{
				echo '<result>0</result>';
			}
		}
		else 
		{
			echo '<result>0</result>';
		}
    }
    //竞彩足球出票通知接口
    private function qihui_1101($xmlObj)
    {
		$datas = $this->encrypt_qihui->decrypt($xmlObj->body);
		log_message('LOG', $datas, "qihui1101/1101");
		$xmlObj = simplexml_load_string($datas);
		$res = $this->api_qihui_model->delPeilv($xmlObj);
		if($res)
		{
			echo '<result>0</result>';
		}
		else
		{
			echo '<result>1</result>';
		}
	
    }
    
    //数字彩出票结果
    private function qihui_1100($xmlObj)
    {
    	$datas = $this->encrypt_qihui->decrypt($xmlObj->body);
		log_message('LOG', $datas, "qihui1100/1100");
    	$xmlObj = simplexml_load_string($datas);
    	$res = $this->api_qihui_model->numPeilv($xmlObj);
    	if($res)
    	{
    		echo '<result>0</result>';
    	}
    	else
    	{
    		echo '<result>1</result>';
    	}
    
    }

    /**
     * 善彩票商出票通知
     */
    private function shancai()
    {
    	$postData = $_POST;
    	$sctob_secret = $this->config->item('sctob_secret');
    	$digest = md5($sctob_secret . $postData['service'] . $postData['timestamp'] . $sctob_secret);
    	if(($digest == $postData['digest']) && ($postData['service'] == 'notify'))
    	{
    	    $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId', 'message_id', 'ticket_submit_time');
    		$datas['s_data'] = array();
    		$datas['d_data'] = array();
    		$datas['concelIds'] = array();
    		$err_num = "6_{$postData['ticketStatus']}";
    		if($postData['ticketStatus'] == '1')   //出票成功
    		{
    			$err_num = 0;
    			$status = $this->ordercfg['draw'];
    		}
    		elseif($postData['ticketStatus'] == '-1')//出票失败
    		{
    			$datas['concelIds'][] = $postData['agentOrderId'];
    			$status = $this->ordercfg['concel'];
    		}
    		else//出票中
    		{
    			$status = $this->ordercfg['drawing'];
    		}
    		
    		if($status != $this->ordercfg['concel'])
    		{
                if($status == $this->ordercfg['drawing'])
                {
                    // 出票中
                    $ticket_time = 'date_add(now(), interval 1 minute)';
                    array_push($datas['s_data'], "(?, ?, ?, $ticket_time, ?, ?, ?)");
                    array_push($datas['d_data'], "{$postData['agentOrderId']}");
                    array_push($datas['d_data'], $err_num);
                    array_push($datas['d_data'], $status);
                    array_push($datas['d_data'], "{$postData['zxTicketID']}");
                    $message_id = $this->tools->getIncNum('UNIQUE_KEY');
                    array_push($datas['d_data'], "{$message_id}");
                    array_push($datas['d_data'], date('Y-m-d H:i:s'));
                }
                else
                {
                    array_push($datas['s_data'], '(?, ?, ?, ?, ?, ?, ?)');
                    array_push($datas['d_data'], "{$postData['agentOrderId']}");
                    array_push($datas['d_data'], $err_num);
                    array_push($datas['d_data'], $status);
                    array_push($datas['d_data'], "{$postData['ticketTime']}");
                    array_push($datas['d_data'], "{$postData['zxTicketID']}");
                    $message_id = $this->tools->getIncNum('UNIQUE_KEY');
                    array_push($datas['d_data'], "{$message_id}");
                    array_push($datas['d_data'], date('Y-m-d H:i:s'));
                }
    		}
    		
    		$this->load->model('api_shancai_model');
    		$re = $this->api_shancai_model->saveResponse($fields, $datas, $postData['lotteryCode']);
    		if($re)
    		{
    			echo "{$postData['orderID']}:接收成功";
    		}
    		
    		log_message('LOG', json_encode($postData), "shancai/notify");
    	}
    }
    /*
     * 华阳票商出票推送处理
     * */
    private function huayang()
    {
        $result = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xmlObj = simplexml_load_string($result);
        preg_match('/(<body.*?<\/body>)/', $result, $matches);
        if(isset($matches['1']) && (md5((string)$xmlObj->header->timestamp . $this->config->item('hytob_secret') . $matches['1']) == (string)$xmlObj->header->digest))
        {
            log_message('LOG', $result, "huayang13006/13006");
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId', 'message_id', 'ticket_submit_time');
            $datas['s_data'] = array();
            $datas['d_data'] = array();
            $datas['relation'] = array();
            $datas['concelIds'] = array();
            // 乐善奖
            $lsDatas['s_data'] = array();
            $lsDatas['d_data'] = array();
            $postData = $xmlObj->body->elements;
            foreach ($postData as $value)
            {
                $tickStatus = (string)$value->element->errorcode;
                $err_num = $tickStatus;
                //出票成功
                if($tickStatus === '0')
                {
                    $err_num = 0;
                    $status = $this->ordercfg['draw'];
                }
                else
                {
                    //出票失败
                    $datas['concelIds'][] = (string)$value->element->id;
                    $status = $this->ordercfg['concel'];
                }
                if($status == $this->ordercfg['draw'])
                {
                    array_push($datas['s_data'], '(?, ?, ?, ?, ?, ?, ?)');
                    array_push($datas['d_data'], (string)$value->element->id);
                    array_push($datas['d_data'], $err_num);
                    array_push($datas['d_data'], $status);
                    array_push($datas['d_data'], (string)$value->element->tickettime);
                    array_push($datas['d_data'], (string)$value->element->ticketid);
                    $message_id = $this->tools->getIncNum('UNIQUE_KEY');
                    array_push($datas['d_data'], "{$message_id}");
                    array_push($datas['d_data'], date('Y-m-d H:i:s'));
                    $spvalue = (string)$value->element->spvalue;
                    if(!empty($spvalue))
                    {
                        $datas['relation']["{$value->element->lotteryid}"]["{$value->element->id}"] = $spvalue;
                    }
                }

                // 出票成功 - 大乐透乐善奖
                $lsCode = (string)$value->element->lscode;
                if($status == $this->ordercfg['draw'] && !empty($lsCode) && $value->element->lotteryid == '106')
                {
                    $lsArr = explode(',', $lsCode);
                    $lsCode = implode(',', array_slice($lsArr, 0, 5)) . '|' . implode(',', array_slice($lsArr, 5, 2));

                    array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                    array_push($lsDatas['d_data'], (string)$value->element->id);
                    array_push($lsDatas['d_data'], "23529");
                    array_push($lsDatas['d_data'], 'huayang');
                    array_push($lsDatas['d_data'], $lsCode);
                    array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                }
            }
             
            $this->load->model('api_huayang_model');
            if(!empty($lsDatas['s_data']))
            {
                $this->api_huayang_model->saveSplitDetail($lsDatas, "23529");
            }
            $re = $this->api_huayang_model->saveResponse($fields, $datas, "{$value->element->lotteryid}");
            if($re)
            {
                $body  = '<body>';
                $body .= '<oelement>';
                $body .= '<errorcode>0</errorcode>';
                $body .= '<errormsg>成功，系统处理正常</errormsg>';
                $body .= '</oelement>';
                $body .= '</body>';
                $result = preg_replace('/(<body.*?<\/body>)/', $body, $result);
                echo $result;
            }
        }
    }
    
    /**
     * 恒钜票商出票通知
     */
    private function hengju()
    {
        $postData = $_POST;
        $hjtob_secret = $this->config->item('hjtob_secret');
        $digest = md5($postData['xAgent'] . $postData['xAction'] . $postData['xValue'] . $hjtob_secret);
        if(($digest == $postData['xSign']))
        {
            log_message('LOG', print_r($postData, true), "hengju501/501");
            $allDatas = array();
            $xValues = explode(',', $postData['xValue']);
            $nlid = $this->config->item("cfg_nlid");
            $nlid = array_flip($nlid);
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId', 'message_id', 'ticket_submit_time');
            // 乐善奖
            $lsDatas['s_data'] = array();
            $lsDatas['d_data'] = array();
            foreach ($xValues as $xValue)
            {
                $values = explode('_', $xValue);
                $lid = $nlid[substr($values[0], -2)];
                $err_num = $values[1];
                //出票成功
                if($values[1] == '1')
                {
                    $allDatas[$lid]['s_data'][] = '(?, ?, ?, ?, ?, ?, ?)';
                    $allDatas[$lid]['d_data'][] = $values[0];  //sub_order_id
                    $allDatas[$lid]['d_data'][] = 0;
                    $allDatas[$lid]['d_data'][] = $this->ordercfg['draw'];
                    $allDatas[$lid]['d_data'][] = date('Y-m-d H:i:s', strtotime($values[3]));
                    $allDatas[$lid]['d_data'][] = $values[2];
                    $allDatas[$lid]['d_data'][] = $this->tools->getIncNum('UNIQUE_KEY');
                    $allDatas[$lid]['d_data'][] = date('Y-m-d H:i:s');
                    if(strpos($values[4], 'match') !== false)
                    {
                        $allDatas[$lid]['relation']["$values[0]"] = $values[4];
                    }
                    elseif(!empty($values[4]) && (strpos($values[4], '#') !== false) && $lid == '23529')
                    {
                        $lsArr = explode('#', $values[4]);
                        $numArr = str_split($lsArr[1], 2);
                        $lsCode = implode(',', array_slice($numArr, 0, 5)) . '|' . implode(',', array_slice($numArr, 5, 2));

                        array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
                        array_push($lsDatas['d_data'], $values[0]);
                        array_push($lsDatas['d_data'], $lid);
                        array_push($lsDatas['d_data'], 'hengju');
                        array_push($lsDatas['d_data'], $lsCode);
                        array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
                    }
                }
                //投注中
                elseif($values[1] == '2002')
                {
                    $ticket_time = 'date_add(now(), interval 1 minute)';
                    $allDatas[$lid]['s_data'][] = "(?, ?, ?, $ticket_time, ?, ?, ?)";
                    $allDatas[$lid]['d_data'][] = $values[0];  //sub_order_id
                    $allDatas[$lid]['d_data'][] = $err_num;
                    $allDatas[$lid]['d_data'][] = $this->ordercfg['drawing'];
                    $allDatas[$lid]['d_data'][] = '';
                    $allDatas[$lid]['d_data'][] = $this->tools->getIncNum('UNIQUE_KEY');
                    $allDatas[$lid]['d_data'][] = date('Y-m-d H:i:s');
                }
                else
                {
                    $allDatas[$lid]['concelIds'][] = $values[0];
                }


            }
            
            $this->load->model('api_hengju_model');
            if(!empty($lsDatas['s_data']))
            {
                $this->api_hengju_model->saveSplitDetail($lsDatas, $lid);
            }
            $re = $this->api_hengju_model->saveResponse($fields, $allDatas);
            if($re)
            {
				echo '1';
                die();
            }
        }
		else
        {
            log_message('LOG', print_r($postData, true), "hengju501/error");
        }
        
        echo '0';
    }

    private function shancaiIssue()
    {
    	// TODO
    }
    
    /**
     * 福牛牛
     */
    private function funiuniu()
    {
        $postData = $_POST;
        $xmlObj = simplexml_load_string($postData['msg']);
        preg_match('/(<body.*?<\/body>)/', $postData['msg'], $matches);
        $fnntob_sellerid = $this->config->item('fnntob_sellerid');
        $fnntob_secret = $this->config->item('fnntob_secret');
        $sign = md5(urlencode($fnntob_sellerid . $matches[1] . $fnntob_secret));
        if($postData['cmd'] == '1001' && isset($matches[1]) && $sign == $xmlObj->header->sign) {
            log_message('LOG', print_r($postData, true), "funiuniu1001/1001");
            $fields = array('sub_order_id', 'error_num', 'status', 'ticket_time', 'ticketId', 'message_id', 'ticket_submit_time');
            $datas['s_data'] = array();
            $datas['d_data'] = array();
            $datas['relation'] = array();
            $datas['relationConcel'] = array();
            $datas['concelIds'] = array();
            $lid = 0;
            $body = $xmlObj->body->tickets;
            foreach ($body->ticket as $ticket) {
                $result = intval($ticket->status);
                $err_num = $result;
                $lid = $this->getFuniuniuLid((string)$ticket->lotteryID);
                if($result == 2)   //出票成功
                {
                    $err_num = 0;
                    $status = $this->ordercfg['draw'];
                }
                elseif($result == -2)//限号出票失败
                {
                    $datas['concelIds'][] = (string)$ticket->ordersID;
                    continue;
                }
                else//出票中
                {
                    $status = $this->ordercfg['drawing'];
                }
                
                array_push($datas['s_data'], '(?, ?, ?, ?, ?, ?, ?)');
                array_push($datas['d_data'], "{$ticket->ordersID}");
                array_push($datas['d_data'], $err_num);
                array_push($datas['d_data'], $status);
                $printTime = date('Y-m-d H:i:s', strtotime($ticket->printTime));
                array_push($datas['d_data'], $printTime);
                array_push($datas['d_data'], "{$ticket->ticketId}");
                $message_id = $this->tools->getIncNum('UNIQUE_KEY');
                array_push($datas['d_data'], "{$message_id}");
                array_push($datas['d_data'], date('Y-m-d H:i:s'));
                if(!empty($ticket->odds))
                {
                    $datas['relation']["{$ticket->lotteryID}"]["{$ticket->ordersID}"] = (string)$ticket->odds;
                }
            }
            $this->load->model('api_funiuniu_model');
            $re = $this->api_funiuniu_model->saveResponse($fields, $datas, $lid);
            if($re)
            {
                echo "SUCCESS";
            }
        }
    }
    
    /**
     * 福牛牛彩种id映射
     * @param unknown $olid
     * @return string
     */
    private function getFuniuniuLid($olid)
    {
        $arr = array(
            '11' => '42',
            '12' => '42',
            '13' => '42',
            '14' => '42',
            '15' => '42',
            '16' => '42',
        );
        return $arr[$olid];
    }
}
