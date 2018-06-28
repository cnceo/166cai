<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Funiuniu_Model extends MY_Model
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
    	$parserFlag = true;
    	foreach ($relation as $lid => $relations)
    	{
	    	foreach ($relations as $suboid => $code)
	    	{
	    		$codes = explode('//', $code);
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
	    			array_push($datas['d_data'], $pdetail['mid']);
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
    
    //胜平负
    private function getDetail_11($str)
    {
        $pdetail = array();
        $match = explode(',', $str);
        $mid = substr($match['0'], 1);
        $pdetail = $this->getSpf($match['1']);
        
        return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    private function getSpf($match)
    {
        $pdetail = array();
        $spvalue = explode('/', $match);
        $pdetail['vs']['letPoint'] = (Object)array('0');
        foreach ($spvalue as $val) {
            $sp = explode(':', $val);
            $pdetail['vs']["v{$sp['0']}"] = (Object)array($sp[1]);
        }
        
        return $pdetail;
    }
    
    //猜比分
    private function getDetail_12($str)
    {
        $pdetail = array();
        $match = explode(',', $str);
        $mid = substr($match['0'], 1);
        $pdetail = $this->getCbf($match['1']);
        
        return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    private function getCbf($match)
    {
        $pdetail = array();
        $spvalue = explode('/', $match);
        foreach ($spvalue as $val) {
            $sp = explode(':', $val);
            $score = $sp[0] . $sp[1];
            $dscore = array('43' => '90', '44' => '99', '34' => '09');
            $score = isset($dscore[$score]) ? $dscore[$score] : $score;
            $pdetail['score']["v{$score}"] = (Object)array($sp[2]);
        }
        
        return $pdetail;
    }
    
    //进球数
    private function getDetail_13($str)
    {
        $pdetail = array();
        $match = explode(',', $str);
        $mid = substr($match['0'], 1);
        $pdetail = $this->getJqs($match['1']);
        
        return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    private function getJqs($match)
    {
        $pdetail = array();
        $spvalue = explode('/', $match);
        foreach ($spvalue as $val) {
            $sp = explode(':', $val);
            $pdetail['goal']["v{$sp['0']}"] = (Object)array($sp[1]);
        }
        
        return $pdetail;
    }
    
    //半全场
    private function getDetail_14($str) 
    {
        $pdetail = array();
        $match = explode(',', $str);
        $mid = substr($match['0'], 1);
        $pdetail = $this->getBqc($match['1']);
        
        return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    private function getBqc($match)
    {
        $pdetail = array();
        $spvalue = explode('/', $match);
        foreach ($spvalue as $val) {
            $sp = explode(':', $val);
            $sp['0'] = str_replace('_', '', $sp['0']);
            $pdetail['half']["v{$sp['0']}"] = (Object)array($sp[1]);
        }
        
        return $pdetail;
    }
    
    //让球胜平负
    private function getDetail_15($str)
    {
        $pdetail = array();
        $match = explode(',', $str);
        $mid = substr($match['0'], 1);
        $pdetail = $this->getRqspf($match['1'], $mid);
        
        return array('mid' => $mid, 'detail' => json_encode($pdetail));
    }
    
    private function getRqspf($match, $mid)
    {
        $pdetail = array();
        $spvalue = explode('/', $match);
        $letPoint = $this->getzqRqByMid($mid);
        $letPoint = str_replace('+', '', $letPoint);
        $pdetail['letVs']['letPoint'] = (Object)array($letPoint);
        foreach ($spvalue as $val) {
            $sp = explode(':', $val);
            $pdetail['letVs']["v{$sp['0']}"] = (Object)array($sp[1]);
        }
        
        return $pdetail;
    }
    
    //处理竞彩足球出票详情
    private function getDetail_16($str)
    {
        $pdetail = array();
        $strs = explode('-', $str);
        $match = explode(',', $strs['0']);
        $mid = substr($match['0'], 1);
        $spvalue = explode('/', $match['1']);
        if($strs['1'] == '11') {
            //胜平负
            $pdetail = $this->getSpf($match['1']);
        }
        elseif($strs['1'] == '12') {
            //比分
            $pdetail = $this->getCbf($match['1']);
        }
        elseif($strs['1'] == '13') {
            //总进球
            $pdetail = $this->getJqs($match['1']);
        }
        elseif($strs['1'] == '14') {
            //半全场
            $pdetail = $this->getBqc($match['1']);
        }
        elseif($strs['1'] == '15') {
            //让球胜平负
            $pdetail = $this->getRqspf($match['1'], $mid);
        }
        else {
            return array();
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
        return $this->slaveDc->query("select rq from cp_jczq_paiqi where mid = ? ", $mid)->getOne();
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
    	    '2' => 'caidou',
    		'4' => 'shancai',
    	    '8' => 'huayang',
    	    '16' => 'hengju',
    	);
    	$bdata['s_data'] = array();
    	$bdata['d_data'] = array();
    	$alertSubid1 = array();
    	$alertSubid2 = array();
    	foreach ($result as $value)
    	{
    		//状态大于240或票商已变就不操作
    		if(($value['status'] > 240) || ($value['ticket_seller'] !='funiuniu'))
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
    		$title = BetCnName::getCnName($lid) . "有订单在funiuniu出票失败，将切换票商";
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
}
