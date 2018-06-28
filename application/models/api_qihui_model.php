<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api_Qihui_Model extends MY_Model
{
	private $pctype_map = array
	(
		'T001'   => '23529',
		'F7'     => '23528',
		'D7'     => '10022',
		'C511'   => '21406',
		'HB11X5' => '21408',
		'F001'   => '51',
		'D3'     => '33',
		'D5'     => '35',
		'F3'     => '52',
		'D14'    => '11',
		'D9'     => '19',
		'KLPK3'  => '54',
		'CQSSC'	 => '55',
	);
	
	//竞足、竞篮玩法映射lid
	private $jjc_pctype_map = array(
		'FT001' => '42',
		'FT002' => '42',
		'FT003' => '42',
		'FT004' => '42',
		'FT005' => '42',
		'FT006' => '42',
		'BSK001' => '43',
		'BSK002' => '43',
		'BSK003' => '43',
		'BSK004' => '43',
		'BSK005' => '43',
	);
	
	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->load->model('Ticket_Model');
		$this->cfgDB = $this->load->database('cfg', true);
		$this->load->library('tools');
	}
	
	public function delPeilv($xmlObj, $params = array())
	{
		$concel = $params['concel'];
		if(!empty($xmlObj->records->record))
		{
			$allDatas = array();
			$errmsg = array();
			$delaysids = array();
			$concelsids = array();
			foreach ($xmlObj->records->record as $records)
			{
				$sub_order_id = (string)$records->id;
				$result = (string)$records->result;
				$lid = (string)$records->lotteryId;
				if(empty($params['lid'])) $params['lid'] = $this->jjc_pctype_map[$lid];
				if(!empty($sub_order_id))
				{
					$orderTicket = array();
					$orderMatches = array();
					$allData = array();
					$successTime = empty($records->successTime) ? date('Y-m-d H:i:s', time() + 60) : (string)$records->successTime;
					if($result == '0' || $result == '200023')
					{
						$orderTicket['sub_order_id'] = $sub_order_id;
						$orderTicket['ticketId'] = (string)$records->ticketId;
						$orderTicket['status'] = $this->order_status['draw'];//$records->result;
						$orderTicket['ticket_time'] = $successTime;
						$orderTicket['error_num'] = $result;
						$orderTicket['message_id'] = $this->tools->getIncNum('UNIQUE_KEY');
						$orderTicket['ticket_submit_time'] = date('Y-m-d H:i:s');
						if(!empty($records->info->item))
						{
							foreach ($records->info->item as $items)
							{
								if(empty($items->id)) continue;
								$orderResult = array();
								foreach ($items as $key => $item)
								{
									if(count($item))
									{
										foreach ($item as $fk => $val)
										{
											$orderResult['pdetail'][$key][$fk] = $val;
										}
									}
									else
									{
										if($key == 'id')
										{
											$item = explode('_', $item);
											$orderResult['mid'] = "{$item[0]}{$item[2]}";
										}
										else 
										{
											$orderResult['pdetail'][$key] = "{$item[0]}{$item[2]}";
										}
										
									}
								}
								$orderMatches[] = $orderResult;
							}
						}
					}
					elseif($result == '200021')
					{
						$status = $this->order_status['drawing'];
						if($concel)
						{
							$status = $this->order_status['concel'];
						}
						array_push($delaysids, $sub_order_id);
						$orderTicket['sub_order_id'] = $sub_order_id;
						$orderTicket['status'] = $status;
						$orderTicket['ticket_time'] = $successTime;
						$orderTicket['error_num'] = $result;
						$orderTicket['ticketId'] = 0;
						$orderTicket['message_id'] = $this->tools->getIncNum('UNIQUE_KEY');
						$orderTicket['ticket_submit_time'] = date('Y-m-d H:i:s');
					}
					else 
					{
						//如果是过期主动查询时 应设置失败
						if($concel)
						{
							array_push($errmsg, $sub_order_id);
							$orderTicket['sub_order_id'] = $sub_order_id;
							$orderTicket['status'] = $this->order_status['concel'];
							$orderTicket['ticket_time'] = $successTime;
							$orderTicket['error_num'] = $result;
							$orderTicket['ticketId'] = 0;
							$orderTicket['message_id'] = $this->tools->getIncNum('UNIQUE_KEY');
							$orderTicket['ticket_submit_time'] = date('Y-m-d H:i:s');
						}
						else
						{
							$concelsids[] = $sub_order_id;
						}
					}
					if(!empty($orderTicket))
						$allData['orderTicket'] = $orderTicket;
					if(!empty($orderMatches))
						$allData['orderMatche'] = $orderMatches;
					$allDatas[] = $allData;
				}
			}
		}
		
		//失败订单处理
		if($concelsids)
		{
			$this->updateTicket($concelsids, $params);
		}
		
		return $this->upPeilv($allDatas, $delaysids, $errmsg, $params);
	}
	
	public function numPeilv($xmlObj, $params = array())
	{
		$concel = $params['concel'];
		if(!empty($xmlObj->records->record))
		{
			$allData = array();
			$errmsg = array();
			$delaysids = array();
			$concelsids = array();
			// 乐善奖
			$lsDatas['s_data'] = array();
			$lsDatas['d_data'] = array();
			foreach ($xmlObj->records->record as $records)
			{
				$sub_order_id = (string)$records->id;
				$lid = (string)$records->lotteryId;
				if(empty($params['lid'])) $params['lid'] = $this->pctype_map[$lid];
				if(!empty($sub_order_id))
				{
					$orderTicket = array();
					$result = (string)$records->result;
					$successTime = empty($records->successTime) ? date('Y-m-d H:i:s', time() + 60) : (string)$records->successTime;
					if($result == '0' || $result == '200023')
					{
						$orderTicket['sub_order_id'] = $sub_order_id;
						$orderTicket['ticketId'] = (string)$records->ticketId;
						$orderTicket['status'] = $this->order_status['draw'];//$records->result;
						$orderTicket['ticket_time'] = $successTime;
						$orderTicket['error_num'] = $result;
						$orderTicket['message_id'] = $this->tools->getIncNum('UNIQUE_KEY');
						$orderTicket['ticket_submit_time'] = date('Y-m-d H:i:s');
					}
					elseif($result == '200021')
					{
						$status = $concel ? $this->order_status['concel'] : $this->order_status['drawing'];
						array_push($delaysids, $sub_order_id);
						$orderTicket['sub_order_id'] = $sub_order_id;
						$orderTicket['ticketId'] = 0;
						$orderTicket['status'] = $status;
						$orderTicket['ticket_time'] = $successTime;
						$orderTicket['error_num'] = $result;
						$orderTicket['message_id'] = $this->tools->getIncNum('UNIQUE_KEY');
						$orderTicket['ticket_submit_time'] = date('Y-m-d H:i:s');
					}
					else
					{
						$concelsids[] = $sub_order_id;
					}

					// 出票成功 - 大乐透乐善奖
					$expand = (string)$records->expand;
					if($orderTicket['status'] == $this->order_status['draw'] && !empty($expand) && (strpos($expand, '+') !== false) && $this->pctype_map[$lid] == '23529')
					{
						$lsCode = str_replace(array(' ', '+'), array(',', '|'), trim($expand));
						array_push($lsDatas['s_data'], '(?, ?, ?, ?, ?)');
						array_push($lsDatas['d_data'], $sub_order_id);
						array_push($lsDatas['d_data'], $this->pctype_map[$lid]);
						array_push($lsDatas['d_data'], 'qihui');
						array_push($lsDatas['d_data'], $lsCode);
						array_push($lsDatas['d_data'], date('Y-m-d H:i:s'));
					}
					
					if(!empty($orderTicket))
					{
						array_push($allData, $orderTicket);
					}
				}
			}

			if(!empty($lsDatas['s_data']))
			{
				$this->saveSplitDetail($lsDatas, "23529");
			}
		}
		//失败订单处理
		if($concelsids)
		{
			$this->updateTicket($concelsids, $params);
		}
		
		return $this->upNumPeilv($allData, $delaysids, $params);
	}
	
	public function upPeilv(&$alldatas, $delayoids, $erroids, $params)
	{
	    $tfields = array('sub_order_id', 'ticketId', 'status', 'error_num', 'ticket_time', 'message_id', 'ticket_submit_time');
		$mfields = array('sub_order_id', 'mid', 'pdetail');
		$s_datas_t = array();
		$d_datas_t = array();
		$s_datas_m = array();
		$d_datas_m = array();
		$m_id = array();
		foreach ($alldatas as $alldata)
		{
			if(empty($alldata['orderTicket']))
			{
				continue;
			}
			$orderTicket = $alldata['orderTicket'];
			array_push($s_datas_t, "(?, ?, ?, ?, ?, ?, ?, 1)");
			foreach ($tfields as $tfield)
			{
				array_push($d_datas_t, $orderTicket[$tfield]);
			}
			$orderMatche = isset($alldata['orderMatche']) ? $alldata['orderMatche'] : array();
			foreach ($orderMatche as $orderMatch)
			{
				array_push($s_datas_m, "(?, ?, ?, {$this->order_status['draw']})");
				foreach ($mfields as $mfield)
				{
					$val = $orderMatch[$mfield];
					if($mfield == 'pdetail')
					{
						$val = json_encode($orderMatch[$mfield]);
					}
					if($mfield == 'sub_order_id')
					{
						$val = $orderTicket[$mfield];
					}
					array_push($d_datas_m, $val);
				}
			}
		}
		$this->trans_start();
		$re = true;
		$re1 = true;
		$re2 = true;
		$re3 = true;
		if(!empty($d_datas_t))
		{
			$tfields[] = 'getplv';
			$sqlt  = "insert cp_orders_split(" . implode(',', $tfields) . ") values";
			$sqlt .= implode(',', $s_datas_t); 
			$sqlt .= $this->onduplicate($tfields, array('status', 'getplv'));
			$sqlt .= ", ticket_time = if(status = '{$this->order_status['concel']}', ticket_time, values(ticket_time)), ticketId = if(values(status) = '{$this->order_status['draw']}', values(ticketId), ticketId)";
			$sqlt .= ", error_num = if((status < '{$this->order_status['draw']}' || status = '{$this->order_status['concel']}'), values(error_num), '')";
			$sqlt .= ", message_id = if(message_id is null and (values(status) = '{$this->order_status['draw']}'), values(message_id), message_id)";
			$sqlt .= ", ticket_submit_time = if(ticket_submit_time = '0000-00-00 00:00:00' and (values(status) = '{$this->order_status['draw']}'), values(ticket_submit_time), ticket_submit_time)";
			$re = $this->cfgDB->query($sqlt, $d_datas_t);
		}
		if(!empty($d_datas_m))
		{
			$mfields[] = 'status';
			$sqlm  = "insert cp_orders_relation(" . implode(',', $mfields) . ") values";
			$sqlm .= implode(',', $s_datas_m); 
			$sqlm .= $this->onduplicate($mfields, array('pdetail', 'status'));
			$re1 = $this->cfgDB->query($sqlm, $d_datas_m);
		}
		if(!empty($delayoids))
		{
			$re2 = $this->updateSplitDelay($delayoids, $params);
		}
		if(!empty($erroids))
		{
			$re3 = $this->updateRelation($erroids);
		}
		if($re && $re1 && $re2 && $re3)
		{
			$this->trans_complete();
			return true;
		}
		else
		{
			$this->trans_rollback();
			return false;
		}
	}
	
	public function upNumPeilv($alldatas, $delayoids, $params)
	{
	    $tfields = array('sub_order_id', 'ticketId', 'status', 'error_num', 'ticket_time', 'message_id', 'ticket_submit_time');
		$s_datas_t = array();
		$d_datas_t = array();
		$m_id = array();
		$re = true;
		$re1 = true;
		foreach ($alldatas as $alldata)
		{
			array_push($s_datas_t, "(?, ?, ?, ?, ?, ?, ?, 1)");
			foreach ($tfields as $tfield)
			{
				array_push($d_datas_t, $alldata[$tfield]);
			}
		}
		$this->trans_start();
		if(!empty($d_datas_t))
		{
			$tables = $this->getSplitTable($params['lid']);
			$tfields[] = 'getplv';
			$sqlt  = "insert {$tables['split_table']}(" . implode(',', $tfields) . ") values";
			$sqlt .= implode(',', $s_datas_t);
			$sqlt .= $this->onduplicate($tfields, array('status', 'getplv'));
			$sqlt .= ", ticket_time = if(status = '{$this->order_status['concel']}', ticket_time, values(ticket_time)), 
			ticketId = if(values(status) = '{$this->order_status['draw']}', values(ticketId), ticketId)";
			$sqlt .= ", error_num = if((status < '{$this->order_status['draw']}' || status = '{$this->order_status['concel']}'), values(error_num), '')";
			$sqlt .= ", message_id = if(message_id is null and (values(status) = '{$this->order_status['draw']}'), values(message_id), message_id)";
			$sqlt .= ", ticket_submit_time = if(ticket_submit_time = '0000-00-00 00:00:00' and (values(status) = '{$this->order_status['draw']}'), values(ticket_submit_time), ticket_submit_time)";
			$re = $this->cfgDB->query($sqlt, $d_datas_t);
		}
		if(!empty($delayoids))
		{
	    	$re1 = $this->updateSplitDelay($delayoids, $params);
		}
		if($re && $re1)
		{
			$this->trans_complete();
			return true;
		}
		else
		{
			$this->trans_rollback();
			return false;
		}
	}
	//设置下次读取出票结果时间
	public function updateSplitDelay($soids, $params)
    {
    	if($params['concel'])
    	{
    		$sql = "update cp_orders_relation
    		set status = '{$this->order_status['concel']}'
    		where sub_order_id in('" . implode("','", $soids). "')";
    	}
    	else 
    	{
    		$tables = $this->getSplitTable($params['lid']);
    		$sql = "update {$tables['split_table']} set 
	    	ticket_time = if(lid in (53, 54, 21406, 21407, 21408, 55),
	    	if(date_add(now(), interval 10 second) > endTime, date_sub(endTime, interval 5 second), date_add(now(), interval 10 second)),
	    	if(date_add(now(), interval 1 minute) > endTime, date_sub(endTime, interval 5 second), date_add(now(), interval 1 minute))) 
	    	where sub_order_id in('" . implode("','", $soids). "') and status = ?";
    	}
    	return $this->cfgDB->query($sql, array($this->order_status['drawing']));
    }
	//更新relation的数据
	public function updateRelation($subids)
	{
		$result = $this->cfgDB->query("update cp_orders_relation set status = ? 
		where sub_order_id in('" . implode("','", $subids) . "')", 
		array($this->order_status['concel']));
		return $result;
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
			if($value['status'] > 240 || ($value['ticket_seller'] !='qihui'))
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
			$title = BetCnName::getCnName($params['lid']) . "有订单在qihui出票失败，将切换票商";
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
