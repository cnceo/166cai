<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Hengju_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->cfgDB = $this->load->database('cfg', true);
	}
	
	/**
	 * 
	 * @param unknown $fields
	 * @param unknown $allDatas
	 * @return boolean
	 */
	public function saveResponse($fields, $allDatas)
	{
	    $return = true;
	    foreach ($allDatas as $lid => $datas)
	    {
	        if(!empty($datas['s_data']))
	        {
	            $tables = $this->getSplitTable($lid);
	            $this->trans_start();
	            $sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
	            . " on duplicate key update status = if(status < values(status), values(status), status),
	            ticketId = if(values(status) = '{$this->order_status['draw']}', values(ticketId), ticketId),
	            ticket_time = if(values(status) = '{$this->order_status['drawing']}' && status = '{$this->order_status['drawing']}',
	            if(values(ticket_time) > endTime, date_sub(endTime, interval 5 second), values(ticket_time)),
	            if(status = '{$this->order_status['concel']}', ticket_time, values(ticket_time))),
	            error_num = if((status < '{$this->order_status['draw']}' || status = '{$this->order_status['concel']}'), values(error_num), ''),
                message_id = if(message_id is null and (values(status) = '{$this->order_status['draw']}'), values(message_id), message_id),
                ticket_submit_time = if(ticket_submit_time = '0000-00-00 00:00:00' and (values(status) = '{$this->order_status['draw']}'), values(ticket_submit_time), ticket_submit_time)";
	            $re = $this->cfgDB->query($sql, $datas['d_data']);
	            $re1 = true;
	            $re2 = true;
	            if(in_array($lid, array('42', '43')))
	            {
	               if(!empty($datas['relation']))
	               {
	                   $fields = array('sub_order_id', 'mid', 'pdetail', 'status');
	                   $result = $this->dealRelations($datas['relation'], $lid);
	                   $bdatas = $result['data'];
	                   $parserFlag = $result['parserFlag'];
	                   if($bdatas['s_data'] && $parserFlag)
	                   {
	                   		if($lid == '42')
	                   		{
	                   			$sql1 = "insert {$tables['relation_table']}(" . implode(',', $fields) . ") values" .
		   	                   	implode(',', $bdatas['s_data']) . " ON duplicate key UPDATE 
	                           	pdetail= CONCAT(
	                                case ptype when 'RQSPF' then 
	                                    CONCAT('{\"letVs\":{\"letPoint\":{\"0\":\"',REPLACE(substring_index(SUBSTRING(pscores, locate(CONCAT(SUBSTRING(substring_index(VALUES(pdetail), '\":{\"', 1), -1),'{'), pscores)+2), '}', 1), '+', ''), '\"},')
	                                when 'SPF' then 
	                                    '{\"vs\":{\"letPoint\":{\"0\":\"0\"},' 
	                                when 'JQS' then 
	                                    '{\"goal\":{' 
	                                else '' END, VALUES(pdetail)
	                           	),
	                           	status=if(status < values(status), values(status), status)";
	                   		}
	                   		else
	                   		{
	                   			$sql1 = "insert {$tables['relation_table']}(" . implode(',', $fields) . ") values" .
	    						implode(',', $bdatas['s_data']) . $this->onduplicate($fields, array('pdetail', 'status'), array());
	                   		}
	                          
	   	                   	$re1 = $this->cfgDB->query($sql1, $bdatas['d_data']);
	                   	}
	                   	else
	                   	{
	                       	$re1 = false;
	                   	}
	                   
	               	}
	               	if(!empty($datas['relationConcel']))
	               	{
	                   	$sql2 = "update {$tables['relation_table']} set status = ? where sub_order_id in('" .
	                   	implode("','", $datas['relationConcel']) . "')";
	                   	$re2 = $this->cfgDB->query($sql2, array($this->order_status['concel']));
	               	}
	            }
	            if($re && $re1 && $re2)
	            {
	                $this->trans_complete();
	            }
	            else
	            {
	                $this->trans_rollback();
	                $return = false;
	            }
	        }
	        
	        //失败订单特殊处理
	        if(!empty($datas['concelIds']) && $return)
	        {
	            $this->updateTicket($datas['concelIds'], $lid);
	        }
	        //有彩种处理失败直接返回
	        if(!$return)
	        {
	            return $return;
	        }
	    }
	    
	    return $return;
	}
	
	private function dealRelations($relations, $lid)
	{
	    $datas['s_data'] = array();
	    $datas['d_data'] = array();
	    $parserFlag = true;
	    foreach ($relations as $suboid => $code)
	    {
	        preg_match_all('/<match id="(.*?)">(.*?)<\/match>/', $code, $codes);
	        foreach ($codes[1] as $key => $mid)
	        {
	        	// 竞彩篮球格式处理
	        	if($lid == '43')
	        	{
	        		preg_match('/^(\d+).*?/', trim($mid), $mids);
	        		$mid = $mids[1];
	        	}

	            $detail = "getDetail_$lid";
	            $pdetail = $this->$detail($codes[2][$key], $codes[1][$key]);
	            //如果解析错误，标志位置为false
	            if(empty($mid) || ($pdetail == '[]') || (!is_numeric($mid)))
	            {
	                $parserFlag = false;
	            }
	            array_push($datas['s_data'], '(?, ?, ?, ?)');
	            array_push($datas['d_data'], $suboid);
	            array_push($datas['d_data'], "20{$mid}");
	            array_push($datas['d_data'], $pdetail);
	            array_push($datas['d_data'], $this->order_status['draw']);
	        }
	    }
	    
	    $return = array(
	        'parserFlag' => $parserFlag,
	        'data' => $datas
	    );
	    
	    return $return;
	}
	
	//处理竞彩足球出票详情
	private function getDetail_42($str, $midStr = '')
	{
	    $pdetail = array();
	    //半全场
	    if(preg_match('/^\d+-\d+=.*/', $str))
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            
	            $pdetail['half']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    //猜比分
	    elseif(preg_match('/^\d+:\d+=.*/', $str))
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace(':', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['score']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    else
	    {
	        $spvalue = explode('|', $str);
	        foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = substr(json_encode($pdetail) . '}', 1);
	    }
	    
	    return $pdetail;
	}

	//处理竞彩篮球出票详情
	private function getDetail_43($str, $midStr = '')
	{
	    $pdetail = array();
	    if(strpos($midStr, 'rf') !== FALSE)
	    {
	    	// 让分胜负
	    	$spvalue = explode('|', $str);
	    	preg_match('/rf=.*?([-]?[0-9]+([.]{1}[0-9]+){0,1})/', $midStr, $lets);
	    	$pdetail['letVs']['letPoint'] = (Object)array($lets[1]);
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['letVs']["v{$sp[0]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    elseif(strpos($midStr, 'zf') !== FALSE)
	    {
	    	// 大小分
	    	$spvalue = explode('|', $str);
	    	preg_match('/zf=.*?([0-9]+([.]{1}[0-9]+){0,1})/', $midStr, $lets);
	    	$pdetail['bs']['basePoint'] = (Object)array($lets[1]);
	    	$bsMaps = array('3' => 'g', '0' => 'l');
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            $pdetail['bs']["{$bsMaps[$sp[0]]}"] = (Object)array($sp[1]);
	        }
	        $pdetail = json_encode($pdetail);
	    }
	    else
	    {
	    	$spvalue = explode('|', $str);
	    	foreach ($spvalue as $val)
	        {
	            $sp = explode('=', $val);
	            $sp[0] = str_replace('-', '', $sp[0]);
	            //出票赔率空时直接返回
	            if((!isset($sp[0])) || (!isset($sp[1])))
	            {
	                return json_encode(array());
	            }
	            if(strlen($sp[0]) == 1 && in_array($sp[0], array(0, 3)))
	            {
	            	// 胜负
	            	if(!isset($pdetail['vs']['letPoint'])) $pdetail['vs']['letPoint'] = (Object)array('0');
	            	$pdetail['vs']["v{$sp[0]}"] = (Object)array($sp[1]);
	            }
	            else
	            {
	            	// 胜分差
	            	$pdetail['diff']["v{$sp[0]}"] = (Object)array($sp[1]);
	            }
	        }
	        $pdetail = json_encode($pdetail);
	    } 
	    return $pdetail;
	}
	
	/**
	 * 失败订单切换票商操作
	 * @param unknown_type $subIds
	 */
	private function updateTicket($subIds = array(), $lid)
	{
		$tables = $this->getSplitTable($lid);
		$sql = "select message_id, sub_order_id, status, ticket_seller, ticket_flag from {$tables['split_table']} where sub_order_id in ?";
		$result = $this->cfgDB->query($sql, array($subIds))->getAll();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->REDIS = $this->config->item('REDIS');
		$lotteryConfig = json_decode($this->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
		$otherSeller = array(
			'1' => 'qihui',
			'2' => 'caidou',
			'4' => 'shancai',
		    '8' => 'huayang',
		    '32' => 'funiuniu',
		);
		$bdata['s_data'] = array();
		$bdata['d_data'] = array();
		$alertSubid1 = array();
		$alertSubid2 = array();
		foreach ($result as $value)
		{
			//状态大于240或票商已变就不操作
			if($value['status'] > 240 || ($value['ticket_seller'] != 'hengju'))
			{
				continue;
			}
			array_push($bdata['s_data'], "(?, ?, ?, ?)");
			$ticketSeller = '';
			$ticketId = 0;
			if($value['ticket_flag'] != $lotteryConfig[$lid]['ticket_flag'])
			{
				foreach ($otherSeller as $id => $seller)
				{
				    //如果票商id不允许出该彩种 跳过
				    if(!($id & $lotteryConfig[$lid]['ticket_flag']))
				    {
				        continue;
				    }
				    //票商id未切过  执行
					if(!($value['ticket_flag'] & $id))
					{
						$ticketSeller = $seller;
						$ticketId = $id;
						break;
					}
				}
			}
			if($ticketSeller)
			{
				array_push($bdata['d_data'], $value['sub_order_id']);
				array_push($bdata['d_data'], '');
				array_push($bdata['d_data'], 0);
				array_push($bdata['d_data'], $ticketSeller);
				$alertSubid1[] = $value['sub_order_id'];
			}
			else
			{
				array_push($bdata['d_data'], $value['sub_order_id']);
				array_push($bdata['d_data'], $value['message_id']);
				array_push($bdata['d_data'], 0);
				array_push($bdata['d_data'], $ticketSeller);
				$alertSubid2[] = $value['sub_order_id'];
			}
		}
		
		if(!empty($bdata['s_data']))
		{
			$fields = array('sub_order_id', 'message_id', 'status', 'ticket_seller');
			$sql = "insert {$tables['split_table']}(" . implode(', ', $fields) . ") values" .
					implode(', ', $bdata['s_data']) . " on duplicate key update message_id = values(message_id), status = values(status), ticket_seller = values(ticket_seller) ";
			$this->cfgDB->query($sql, $bdata['d_data']);
		}
		
		if($alertSubid1)
		{
			$this->load->library('BetCnName');
			$title = BetCnName::getCnName($lid) . "有订单在hengju出票失败，将切换票商";
			$content = "将切换票商的子订单id信息：" . implode(',', $alertSubid1);
			$sql = "INSERT INTO cp_alert_log
			(ctype,title,content,created) VALUES (?, ?, ?, NOW())";
			$this->db->query($sql, array(4,$title,$content));
		}
		 
		if($alertSubid2)
		{
			$this->load->library('BetCnName');
			$title = BetCnName::getCnName($lid) . "有订单在所有票商均未能出票";
			$content = "所有票商均未能出票的子订单id信息：" . implode(',', $alertSubid2);
			$sql = "INSERT INTO cp_alert_log
			(ctype,title,content,created) VALUES (?, ?, ?, NOW())";
			$this->db->query($sql, array(4,$title,$content));
		}
	}

	// 大乐透乐善奖临时表
    public function saveSplitDetail($datas, $lid = 0)
    {
        $fields = array('sub_order_id', 'lid', 'ticket_seller', 'awardNum', 'created');
        if(!empty($datas['s_data']))
        {
            $sql = "insert cp_orders_split_detail(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
                . " on duplicate key update awardNum = values(awardNum)";
            $this->cfgDB->query($sql, $datas['d_data']);
        }
    }
}
