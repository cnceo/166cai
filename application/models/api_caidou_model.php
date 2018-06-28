<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Caidou_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->cfgDB = $this->load->database('cfg', true);
	}
	
	public function saveResponse($fields, $datas, $lid = 0)
    {
    	$return = true;
    	if(!empty($datas['s_data']))
    	{
    		$tables = $this->getSplitTable($lid);
    		//$upfields = array('sub_order_id', 'status', 'ticket_money', 'ticketId');
    		$this->trans_start();
    		$sql = "insert {$tables['split_table']}(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
    		. " on duplicate key update status = if(status < values(status), values(status), status),
    		ticket_money = if(values(status) = '{$this->order_status['draw']}', values(ticket_money), ticket_money),
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
    		if(in_array($lid, array('42', '43', '44', '45')))
    		{
    			if(!empty($datas['relation']))
    			{
    				$fields = array('sub_order_id', 'mid', 'pdetail', 'status');
    				$result = $this->dealRelations($datas['relation']);
    				$bdatas = $result['data'];
    				$parserFlag = $result['parserFlag'];
    				if($parserFlag)
    				{
    				    $sql1 = "insert {$tables['relation_table']}(" . implode(',', $fields) . ") values" .
        				implode(',', $bdatas['s_data']) . $this->onduplicate($fields, array('pdetail', 'status'), array());
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
    	
    	return $return;
    }
    
    private function dealRelations($relation)
    {
    	$datas['s_data'] = array();
    	$datas['d_data'] = array();
    	$midPrefix = array('30' => '20', '31' => '20', '98' => '', '99' => '');
    	$parserFlag = true;
    	foreach ($relation as $lid => $relations)
    	{
	    	foreach ($relations as $suboid => $code)
	    	{
	    		$codes = explode(',', $code);
	    		foreach ($codes as $cstr)
	    		{
	    			$detail = "getDetail_$lid";
	    			$pdetail = $this->$detail($cstr);
	    			//如果解析错误，标志位置为false
	    			if(empty($pdetail['mid']) || ($pdetail['detail'] == '[]') || (!is_numeric($pdetail['mid'])))
	    			{
	    			    $parserFlag = false;
	    			}
	    			array_push($datas['d_data'], $suboid);
	    			array_push($datas['d_data'], "{$midPrefix[$lid]}{$pdetail['mid']}");
	    			array_push($datas['d_data'], $pdetail['detail']);
	    			array_push($datas['d_data'], $this->order_status['draw']);
	    			array_push($datas['s_data'], '(?, ?, ?, ?)');
	    		}
	    	}
    	}
    	
    	$return = array(
    	    'parserFlag' => $parserFlag,
    	    'data' => $datas
    	);
    	
    	return $return;
    }
    //处理竞彩篮球出票详情
    private function getDetail_31($str)
    {
    	$regMaps = array(
    		'vs'    => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'letVs' => array(
    			'check' => '/(\d+)\([\x{4e00}-\x{9fa5}](\-?\d+\.?\d*)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    	       	'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'), 
    		'bs'    => array(
    			'check' => '/(\d+)\((\d+\.?\d*)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'diff'  => array(
    			'check' => '/(\d+)=((?:\([\x{4e00}-\x{9fa5}](?:(?:\d+\-\d+)|(?:\d+\+?))\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/\(([\x{4e00}-\x{9fa5}])((?:\d+\-\d+)|(?:\d+\+?))\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    	);
    	$pdetail = array(); 
    	$inMaps = array('胜' => '3', '平' => '1', '负' => '0', '大' => '3', '小' => '0', '主' => '0', '客' => '1');
    	$diffMaps = array('1-5' => '1', '6-10' => '2', '11-15' => '3', '16-20' => '4', '21-25' => '5', '26+' => '6',);
    	$bsMaps = array('大' => 'g', '小' => 'l');
    	foreach ($regMaps as $mname => $regMap)
    	{
    		if(preg_match($regMap['check'], $str, $matches))
    		{
    			$mid = $matches[1];
    			switch ($mname)
    			{
    				case 'vs':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array('0');
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'letVs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'bs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['basePoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["{$bsMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'diff':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}{$diffMaps[$mtchs[2][$in]]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    			}
    			break;
    		}
    	}
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    //处理竞彩足球出票详情
    private function getDetail_30($str)
    {
    	$regMaps = array(
    		'vs'    => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'letVs' => array(
    			'check' => '/(\d+)\([\x{4e00}-\x{9fa5}](\-?\d+)\)=((?:[\x{4e00}-\x{9fa5}]@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    	       	'map'   => '/([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'), 
    		'score' => array(
    			'check' => '/(\d+)=((?:\(\d+:\d+\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u', 
    			'map'   => '/\((\d+):(\d+)\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'goal'  => array(
    			'check' => '/(\d+)=((?:\(\d+\+?\)@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u', 
    			'map'   => '/\((\d+)\+?\)@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u'),
    		'half'  => array(
    			'check' => '/(\d+)=((?:[\x{4e00}-\x{9fa5}]{2}@\d+\.?\d*[\x{4e00}-\x{9fa5}]\+?)+)/u',
    			'map'   => '/([\x{4e00}-\x{9fa5}])([\x{4e00}-\x{9fa5}])@(\d+\.?\d*)[\x{4e00}-\x{9fa5}]\+?/u')
    	);
    	$pdetail = array(); 
    	$mid = '';
    	$inMaps = array('胜' => '3', '平' => '1', '负' => '0');
    	foreach ($regMaps as $mname => $regMap)
    	{
    		if(preg_match($regMap['check'], $str, $matches))
    		{
    			$mid = $matches[1];
    			switch ($mname)
    			{
    				case 'goal':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$mtchs[1][$in]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'vs':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array('0');
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'letVs':
    					if(preg_match_all($regMap['map'], $matches[3], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]['letPoint'] = (Object)array($matches[2]);
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}"] = (Object)array($mtchs[2][$in]);
    							}
    						}
    					}
    					break;
    				case 'score':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$mtchs[1][$in]}{$mtchs[2][$in]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    				case 'half':
    					if(preg_match_all($regMap['map'], $matches[2], $mtchs))
    					{
    						if(is_array($mtchs[0]))
    						{
    							foreach ($mtchs[0] as $in =>$val)
    							{
    								$pdetail[$mname]["v{$inMaps[$mtchs[1][$in]]}{$inMaps[$mtchs[2][$in]]}"] = (Object)array($mtchs[3][$in]);
    							}
    						}
    					}
    					break;
    			}
    			break;
    		}
    	}
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    //处理冠军彩出票详情
    private function getDetail_98($str)
    {
    	return $this->comm_98_99($str);
    }
    
    //处理冠亚军彩出票详情
    private function getDetail_99($str)
    {
    	return $this->comm_98_99($str);
    }
    
    private function comm_98_99($str)
    {
    	$strArr = explode('|', $str);
    	$mid = $strArr[1];
    	$matchs = explode('+', $strArr[0]);
    	$pdetail = array();
    	//(01 法国)@1.81元+(05 英格兰)@5.62元+(12 乌克兰)@4.38元
    	$rule = '\((\d+).*?\)@(\d+\.?\d+)[\x{4e00}-\x{9fa5}]';
    	foreach ($matchs as $match)
    	{
    		preg_match("/{$rule}/u", $match, $value);
    		if(!empty($value[1]))
    		{
    			$pdetail[$value[1]] = $value[2];
    		}
    	}
    	
    	return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    public function saveTicketId($fields, $datas)
    {
    	$sql = "insert bn_cpiao_cfg_tmp.cp_orders_split_ticketid(" . implode($fields, ',') . ")values" . implode($datas['s_data'], ',')
    	     . " on duplicate key update status = values(status), ticketId = values(ticketId), ticket_time = values(ticket_time)";
    	$re = $this->cfgDB->query($sql, $datas['d_data']);
    }
    
    /**
     * 失败订单切换票商操作
     * @param unknown_type $subIds
     */
    private function updateTicket($subIds, $lid)
    {
    	$tables = $this->getSplitTable($lid);
    	$sql = "select message_id, sub_order_id, status, ticket_seller, ticket_flag from {$tables['split_table']} where sub_order_id in ?";
    	$result = $this->cfgDB->query($sql, array($subIds))->getAll();
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$this->REDIS = $this->config->item('REDIS');
    	$lotteryConfig = json_decode($this->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
    	$otherSeller = array(
    		'1' => 'qihui',
    		'4' => 'shancai',
    	    '8' => 'huayang',
    	    '16' => 'hengju',
    	    '32' => 'funiuniu',
    	);
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$alertSubid1 = array();
    	$alertSubid2 = array();
    	foreach ($result as $value)
    	{
    		//状态大于240或票商已变就不操作
    		if(($value['status'] > 240) || ($value['ticket_seller'] !='caidou'))
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
    		$title = BetCnName::getCnName($lid) . "有订单在caidou出票失败，将切换票商";
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
