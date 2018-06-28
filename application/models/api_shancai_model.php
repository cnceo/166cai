<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Shancai_Model extends MY_Model
{
    
    private $pctype_map = array (
        '51'    => '51',
        '14001' => '57',
        '34001' => '53',
    );
    
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->cfgDB = $this->load->database('cfg', true);
	}
	
	/**
	 * 保存出票信息
	 * @param unknown_type $fields
	 * @param unknown_type $datas
	 * @param unknown_type $lid
	 */
	public function saveResponse($fields, $datas, $lid = 0)
    {
    	$return = true;
    	if(!empty($datas['s_data']))
    	{
    	    $lid = $this->pctype_map[$lid];
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
    		if($re)
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
		);
		$bdata['s_data'] = array();
		$bdata['d_data'] = array();
		$alertSubid1 = array();
		$alertSubid2 = array();
		foreach ($result as $value)
		{
			//状态大于240或票商已变就不操作
			if($value['status'] > 240 || ($value['ticket_seller'] !='shancai'))
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
			$title = BetCnName::getCnName($lid) . "有订单在shancai出票失败，将切换票商";
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
