<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Huayang_Model extends MY_Model
{
	private $pctype_map = array
	(
            '106'   => '23529',
	    '208'   => '42',
	    '209'   => '42',
	    '210'   => '42',
	    '211'   => '42',
	    '212'   => '42',
	    '213'   => '42',
			'112'   => '21406',
            '113'   => '21407',
            '124'   => '21408',
            '126'   => '56'
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->cfgDB = $this->load->database('cfg', true);
	}
	
	/**
	 * 
	 * @param unknown $fields
	 * @param unknown $datas
	 * @param number $lotteryid   票商定义的彩种id
	 * @return boolean
	 */
	public function saveResponse($fields, $datas, $lotteryid = 0)
	{
	    $lid = $this->pctype_map[$lotteryid];
	    $return = true;
	    if(!empty($datas['s_data']))
	    {
	        $tables = $this->getSplitTable($lid);
	        //$upfields = array('sub_order_id', 'status', 'ticket_money', 'ticketId');
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
	        if(in_array($lid, array('42')))
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
	        }
	        if($re && $re1)
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
	        $this->updateTicket($datas['concelIds'], array('lid' => $lid));
	    }
	    
	    return $return;
	}
	
	private function dealRelations($relation)
	{
	    $datas['s_data'] = array();
	    $datas['d_data'] = array();
	    $parserFlag = true;
	    foreach ($relation as $lid => $relations)
	    {
	        foreach ($relations as $suboid => $code)
	        {
	            $codes = explode(';', $code);
	            foreach ($codes as $cstr)
	            {
	                if($lid != '208')
	                {
	                    //不是混合投注拼装成混合投注格式
	                    $cstr = $lid . '^' . $cstr;
	                    $lid = 208;
	                }
	                $detail = "getDetail_$lid";
	                $pdetail = $this->$detail($cstr);
	                //如果解析错误，标志位置为false
	                if(empty($pdetail['mid']) || $pdetail['mid'] == '20' || ($pdetail['detail'] == '[]') || (!is_numeric($pdetail['mid'])))
	                {
	                    $parserFlag = false;
	                }
	                array_push($datas['d_data'], $suboid);
	                array_push($datas['d_data'], "{$pdetail['mid']}");
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
	
	//处理竞彩足球出票详情
	private function getDetail_208($str)
	{
	    $regMaps = array(
	        'vs'    => '/209\^(.*?)\((.*?)\)/',
	        'letVs' => '/210\^(.*?)\((.*?)\)/',
	        'score' => '/211\^(.*?)\((.*?)\)/',
	        'goal'  => '/212\^(.*?)\((.*?)\)/',
	        'half'  => '/213\^(.*?)\((.*?)\)/',
	    );
	    $pdetail = array();
	    $mid = '';
	    foreach ($regMaps as $mname => $rule)
	    {
	        if(preg_match($rule, $str, $matches))
	        {
	            $mid = '20' . str_replace('-', '', $matches[1]);
	            switch ($mname)
	            {
	                case 'vs':
	                    $pdetail[$mname]['letPoint'] = (Object)array('0');
	                    $spvalue = explode(',', $matches[2]);
	                    foreach ($spvalue as $val)
	                    {
	                        $sp = explode('_', $val);
	                        $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                    }
	                    break;
	                 case 'letVs':
	                     $letPoint = $this->getzqRqByMid($mid);
	                     $letPoint = str_replace('+', '', $letPoint);
	                     $pdetail[$mname]['letPoint'] = (Object)array($letPoint);
	                     $spvalue = explode(',', $matches[2]);
	                     foreach ($spvalue as $val)
	                     {
	                         $sp = explode('_', $val);
	                         $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                     }
	                     break;
	                 case 'score':    
	                 case 'goal':
	                 case 'half':
	                     $spvalue = explode(',', $matches[2]);
	                     foreach ($spvalue as $val)
	                     {
	                         $sp = explode('_', $val);
	                         $pdetail[$mname]["v{$sp[0]}"] = (Object)array($sp[1]);
	                     }
	                     break;
	            }
	        }
	    }

	    return array('mid' => $mid, 'detail' => json_encode($pdetail));
	}
	
	/**
	 * 足球让球
	 * @param unknown $mid
	 * @return unknown
	 */
	private function getzqRqByMid($mid)
	{
	    return $this->dc->query("select rq from cp_jczq_paiqi where mid = ? ", $mid)->getOne();
	}
	
	/**
	 * 失败订单切换票商操作
	 * @param unknown_type $subIds
	 */
	private function updateTicket($subIds = array(), $params)
	{
		$tables = $this->getSplitTable($params['lid']);
		$sql = "select message_id, sub_order_id, status, ticket_seller, ticket_flag from {$tables['split_table']} where sub_order_id in ?";
		$result = $this->cfgDB->query($sql, array($subIds))->getAll();
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->REDIS = $this->config->item('REDIS');
		$lotteryConfig = json_decode($this->cache->get($this->REDIS['LOTTERY_CONFIG']), true);
		$otherSeller = array(
			'1' => 'qihui',
			'2' => 'caidou',
			'4' => 'shancai',
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
			if($value['status'] > 240 || ($value['ticket_seller'] != 'huayang'))
			{
				continue;
			}
			array_push($bdata['s_data'], "(?, ?, ?, ?)");
			$ticketSeller = '';
			$ticketId = 0;
			if($value['ticket_flag'] != $lotteryConfig[$params['lid']]['ticket_flag'])
			{
				foreach ($otherSeller as $id => $seller)
				{
				    //如果票商id不允许出该彩种 跳过
				    if(!($id & $lotteryConfig[$params['lid']]['ticket_flag']))
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
			$title = BetCnName::getCnName($params['lid']) . "有订单在huayang出票失败，将切换票商";
			$content = "将切换票商的子订单id信息：" . implode(',', $alertSubid1);
			$sql = "INSERT INTO cp_alert_log
			(ctype,title,content,created) VALUES (?, ?, ?, NOW())";
			$this->db->query($sql, array(4,$title,$content));
		}
		 
		if($alertSubid2)
		{
			$this->load->library('BetCnName');
			$title = BetCnName::getCnName($params['lid']) . "有订单在所有票商均未能出票";
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
