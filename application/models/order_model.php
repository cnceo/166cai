<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Order_Model extends MY_Model
{
	public $tbname;
	public $status = array(
			'create_init' => 0, //订单初始化
			'create' => 10, //创建
			'out_of_date' => 20, //过期未付款
			'out_of_date_pay' => 21, //过期已付款
			'pay_fail' => 30, //付款失败，等待系统再付款
			'pay' => 40, //已付款
			'qualified' => 200, //未满足条件
			'drawing' => 240, //出票中
			'draw' => 500, //出票成功
			'concel' => 600, //出票失败
			'notwin' => 1000, //未中
			'win' => 2000, //中奖
		);
	public function __construct()
	{
		parent::__construct();
		$this->tbname = 'cp_orders';
		$this->load->library('libcomm');
	}
	
	public function SaveOrder($ctype, $datas)
	{
		$fields = array();
		$bdata = array();
		$upfields = array('status', 'bonus', 'margin', 'eachAmount', 'channel', 'codecc', 'qsFlag', 'ticket_time', 'win_time', 'trade_no', 'mark', 'pay_time', 'c_synflag', 'ticket_seller', 'shopId');
		switch ($ctype)
		{
			case 'create':
			case 'create_init':
				$datas['status'] = $this->status[$ctype];
				$datas['created'] = date('Y-m-d H:i:s', strtotime(substr($datas['orderId'], 0, 14))); //取订单创建时间
				$fields = array('uid', 'userName', 'orderId', 'phoneNum', 'buyPlatform', 'codes', 'lid', 'money', 'multi', 'issue',
				'playType','singleFlag', 'betTnum', 'isChase', 'orderType', 'status', 'endTime', 'codecc', 'mark', 'channel','app_version', 
				'activity_ids', 'is_hide', 'forecastBonus', 'created');
				log_message('LOG', serialize($datas), 'RECORD/oc');
				break;
			case 'pay':
			case 'pay_fail':
				$datas['status'] = $this->status[$ctype];
				$datas['pay_time'] = date('Y-m-d H:i:s', time());
				$ticketSeller = $this->getTicketSeller($datas['orderId']);
				$datas['ticket_seller'] = $ticketSeller['ticket_seller'];
				$datas['shopId'] = $ticketSeller['shopId'];
				$fields = array('orderId', 'trade_no', 'status', 'mark', 'pay_time', 'ticket_seller', 'shopId');
				if($ctype == 'pay_fail')
				{
					$this->saveFailOrder($datas['uid'], $datas['orderId'], $datas['trade_no']);
				}
				unset($datas['pay_pwd']);
				log_message('LOG', serialize($datas), 'RECORD/op');
				break;
			case 'notify':
				//附属添加字段
				$addfield = array($this->status['draw'] => 'ticket_time', $this->status['win'] => 'win_time');
				$fields = array('uid', 'userName', 'orderId', 'status', 'bonus', 'margin', 'eachAmount', 'channel', 'codecc', 'qsFlag');
				if(key_exists($datas['status'], $addfield))
				{
					array_push($fields, $addfield[$datas['status']]);
					//中奖订单保存
					if($datas['status'] == $this->status['win'])
					{
						log_message('LOG', serialize($datas), "RECORD/ow");
					}
				}
				break;
			default:
				break;
		}
		foreach ($fields as $field)
		{
			array_push($bdata, $datas[$field]);
		}
		//如果有订单状态要更新，则同时更新追号状态同步状态
		if($datas['status'])
		{
			array_push($fields, 'c_synflag');
			array_push($bdata, '0');
		}
		$sql = "insert {$this->tbname}(" . implode(',', $fields) . ")
		values(". implode(',', array_map(array($this, 'maps'), $fields)) .")" . 
		$this->onduplicate($fields, $upfields);
		try 
		{
			return $this->db->query($sql, $bdata);  
		}
		catch (Exception $e)
		{
			log_message('LOG', "orderSave error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
			return false;
		}
	}
	
	public function CheckStatus($sdate, $edate)
	{
		$this->load->model('wallet_model');
		$this->load->model('user_model');
		$this->load->library('BetCnName');
		$order_temp = "{$this->db_config['tmp']}.cp_orders_temp";
		
		$condition = "((orderType != 4 and (status={$this->status['win']} or (failMoney > 0 and refund_time='0000-00-00 00:00:00'))) 
		or (orderType = 4 and (bonus <> margin or bonus >= 5000000))) 
		and my_status=0 and modified >= '$sdate' and modified < '$edate'";
  		$check_sql = "select count(*) from {$this->tbname} where $condition";

		$cnums = $this->db->query($check_sql)->getOne();
		$re = true;
		while($re && $cnums)
		{
			$fields = array('id','uid','userName','orderId','lid','trade_no','money','status','failMoney',
			'refund_time', 'bonus','margin','my_status', 'orderType', 'created','modified');
			$field_str = implode(', ', $fields);
			
			$this->db->query("truncate $order_temp");
			$sql = "insert $order_temp($field_str) select $field_str 
			from {$this->tbname} where $condition";
			$this->db->query($sql);
			//将detail 数据更新到临时表
			$sql = "update $order_temp t left join cp_orders_detail d on d.orderId=t.orderId
			set t.redpackId=d.redpackId,t.redpackMoney=d.redpackMoney where t.failMoney > 0";
			$this->db->query($sql);
			$this->db->trans_start();
			//走审核
			$sql_check = "update {$this->tbname} m join $order_temp n 
			on m.id = n.id set m.my_status = 2,m.c_synflag = 0, n.my_status = 1
			where (n.bonus > 0 and ((n.bonus != n.margin) or n.bonus >= 5000000)) and n.status = {$this->status['win']}";
			$re1 = $this->db->query($sql_check);
			
			//删除数据
			$re2 = $this->db->query("update $order_temp m join {$this->tbname} n on m.id = n.id and m.my_status = 0
			set n.my_status = if(n.my_status=0 and n.status={$this->status['win']},1,n.my_status), n.sendprize_time = if(n.status = {$this->status['win']}, now(), 0), 
			n.refund_time = if(n.failMoney > 0 and n.refund_time = '0000-00-00 00:00:00', now(), n.refund_time),
			n.c_synflag = 0");
			//保存中奖订单
			$save_fields = array('uid', 'userName', 'orderId', 'trade_no', 'money', 'status', 'bonus', 'margin', 'my_status', 'orderType');
			$re3 = $this->db->query("insert ignore cp_orders_win(" . implode(', ', $save_fields) . ") select " . 
			implode(', ', $save_fields) . " from $order_temp where status = ? and orderType not in(4)", array($this->status['win']));
			$re = $re1 && $re2 && $re3;
			if($re)
			{
				//刷新钱包
				$datas = $this->db->query("select m.uid, '1' mark, m.orderId, m.bonus,
				m.margin, if(m.refund_time=0, m.failMoney, 0) failMoney, n.money, n.must_cost, n.dispatch, m.refund_time,
				if(m.status = {$this->status['win']}, {$this->wallet_model->ctype['reward']}, {$this->wallet_model->ctype['drawback']}) ctype,
				m.lid additions, m.status,m.money as buyMoney, m.redpackId, m.redpackMoney, m.created dates from $order_temp m left join {$this->wallet_model->tbname} n on m.trade_no = n.trade_no
				where m.my_status = 0")->getAll();
				$sms_type = array($this->status['win'] => 'win_prize', $this->status['concel'] => 'tick_fail');
				$flag = true;
				$uids = array();
				foreach ($datas as $user)
				{
					if($user['status'] == $this->status['win'])
					{
						$user['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
						$user['must_cost'] = 0;
						$user['dispatch'] = 0;
						$user['money'] = $user['margin'];
						$result = $this->wallet_model->addMoney($user['uid'], $user, false);
						if(!$result)
						{
							$flag = false;
						}
					}
					if($user['failMoney'] > 0)
					{
						if($user['redpackId'] == 0)
						{
							//短信退款状态
							//$user['status'] = $this->status['concel'];
							$user['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
							$user['must_cost'] = 0;
							$user['dispatch'] = 0;
							$user['money'] = $user['failMoney'];
							$user['ctype'] = $this->wallet_model->ctype['drawback'];
							$result1 = $this->wallet_model->addMoney($user['uid'], $user, false);
							// 入总账流水
							$this->load->model('capital_model');
							$result2 = $this->capital_model->recordCapitalLog(1, $user['trade_no'], 'ticket_fail', $user['failMoney'], '2', false);
							if(!($result1 && $result2))
							{
								$flag = false;
							}
						}
						else
						{
							$user['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
							//整单失败
							if($user['failMoney'] == $user['buyMoney'])
							{
								$user['must_cost'] = 0;
								$user['dispatch'] = 0;
								$user['money'] = $user['failMoney'] - $user['redpackMoney'];
								$user['ctype'] = $this->wallet_model->ctype['drawback'];
								$user['content'] = "返回实际支付".number_format(ParseUnit($user['money'], 1), 2)."元，购彩红包已返回账户";
								$result1 = $this->wallet_model->addMoney($user['uid'], $user, false);
								// 入总账流水
								$this->load->model('capital_model');
								$capitalRedpack = array('capitalId' => '2', 'ctype' => 'redpack', 'money' => $user['redpackMoney'], 'status' => 1);
								$result2 = $this->capital_model->recordCapitalLog(1, $user['trade_no'], 'ticket_fail', $user['money'], '2', false, $capitalRedpack);
								$result3 = $this->db->query("update cp_redpack_log set orderId='',valid_start=?,valid_end=?, status='1',use_time='0' where id=?", array(date('Y-m-d 00:00:00', time()), date('Y-m-d 23:59:59', strtotime("+7 day")), $user['redpackId']));
								if(!($result1 && $result2 && $result3))
								{
									$flag = false;
								}
							}
							else
							{
								//部分失败
								if($user['failMoney'] > $user['money'])
								{
									$realMoney = $user['money'];
									$user['must_cost'] = $user['failMoney'] - $user['money'];
									$user['dispatch'] = 0;
									$user['money'] = $user['failMoney'];
									$user['ctype'] = $this->wallet_model->ctype['drawback'];
									$user['content'] = "返回实际支付".number_format(ParseUnit($realMoney, 1), 2)."元，购彩红包未使用部分".number_format(ParseUnit($user['must_cost'], 1), 2)."元";
								}
								else
								{
									$user['must_cost'] = 0;
									$user['dispatch'] = 0;
									$user['money'] = $user['failMoney'];
									$user['ctype'] = $this->wallet_model->ctype['drawback'];
								}
								$result1 = $this->wallet_model->addMoney($user['uid'], $user, false);
								// 入总账流水
								$this->load->model('capital_model');
								$result2 = $this->capital_model->recordCapitalLog(1, $user['trade_no'], 'ticket_fail', $user['failMoney'], '2', false);
								if(!($result1 && $result2))
								{
									$flag = false;
								}
							}
						}
					}
					$uids[] = $user['uid'];
					if(!$flag)
					{
						$this->db->trans_rollback();
						foreach ($uids as $uid)
						{
							$this->wallet_model->freshWallet($uid);
						}
					}
				}
				
				$this->db->trans_complete();
			}
			else 
			{
				$this->db->trans_rollback();
			}

			$cnums = $this->db->query($check_sql)->getOne();
		}
		return true;
	}
	//过期未付款订单更新
	public function updateFailOrder($sdate)
	{
		$sql = "select count(1) from {$this->tbname} where status in({$this->status['create']}) and endTime >= '$sdate' and endTime < now() and orderType != 4";
		$count = $this->db->query($sql)->getOne();
		if($count > 0)
		{
			$this->db->query("update {$this->tbname} set status = '{$this->status['out_of_date']}'
			where status in({$this->status['create']}) and endTime >= '$sdate' and endTime < now()");
		}
	}
	
	//获得投注信息
	public function getOrders($cons, $limit)
	{
		$sql2 = $sql3 = $sql4 = $sql5 = $sql6 = '';
		
		if ($cons['lid']) $con = " and #TABLE#lid='{$cons['lid']}'";
		if( $cons['marginonly']) $con .= ' AND #TABLE#margin > 0'; // 未支付
                if( $cons['kaijiang']) $con .= ' AND #TABLE#status = 500'; // 等待开奖
        $con .= ' AND (#TABLE#is_hide & 1) = 0 AND (#TABLE#lid not in (44, 45) OR #TABLE#status > 10)'; // 未删除
		
		if (in_array($cons['buyType'], array(0, 4)) && empty($cons['nopay'])) {
			$sql2 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created`,`singleFlag` )
					SELECT orderId, lid, 0, money, buyTotalMoney, buyMoney, actualguranteeAmount, '', 41, '', isChase, issue, `STATUS`, my_status, margin, endTime, 0, created,0 FROM bn_cpiao.cp_united_orders
					WHERE `status` not in (0, 20) and uid = '{$cons['uid']}' and created >= '{$cons['start']}' and created <= '{$cons['end']}'";
			$sql2 .= str_replace('#TABLE#', '', $con);
		}		
		if (in_array($cons['buyType'], array(0, 5, 6)) && empty($cons['nopay'])) {
			$sql3 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created` ,`singleFlag`, `subOrderType`)
					SELECT uj.orderId, uj.lid, u.uid, 0, 0, uj.buyMoney, 0, u.uname, 42, '', 0, 0, uj.status, uj.my_status, uj.margin, '', 0, uj.created,0, uj.subOrderType FROM bn_cpiao.cp_united_join as uj left join bn_cpiao.cp_user as u on uj.puid=u.uid
					WHERE uj.`status` not in (0, 20) and orderType=2 and uj.uid = '{$cons['uid']}' and uj.created >= '{$cons['start']}' and uj.created <= '{$cons['end']}'";
			if($cons['buyType']==6) $sql3 .= " and uj.subOrderType=1";
                        if($cons['buyType']==5) $sql3 .= " and uj.subOrderType!=1";
                        $sql3 .= str_replace('#TABLE#', '', $con);
		}
		
		if (in_array($cons['buyType'], array(0, 1, 2, 3))) {
			$sql4 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created` ,`singleFlag`)
			SELECT o.orderId, o.lid, o.uid, o.money, 0 as buyTotalMoney, money as buyMoney, 0 as guarantee, '' as nick_name, o.orderType, o.playType, o.isChase, o.issue, o.STATUS, o.my_status, o.margin, o.endTime, ajo.add_money, o.created,o.singleFlag
			FROM bn_cpiao.cp_orders as o
			LEFT JOIN bn_cpiao.cp_activity_jj_order AS ajo ON o.orderId = ajo.orderId
			WHERE o.status not in(0, 20) and o.orderType <> 4 and o.uid = '{$cons['uid']}' and o.created >= '{$cons['start']}' and o.created <= '{$cons['end']}' and (o.is_hide & 2) = 0".str_replace('#TABLE#', 'o.', $con);
			if( $cons['nopay']) $sql4 .= ' AND o.status = 10'; // 未支付
			if ($cons['buyType']) {
				if (in_array($cons['buyType'], array(1, 2))) {
					$sql4 .= " and o.orderType = ".($cons['buyType']-1);
				}else {
					$sql4 .= " and o.orderType in (3, 6) ";
				}
			}
			$startSuffix = $this->tools->getTableSuffixByDate($cons['start']);
			if ($startSuffix) {
				$sql5 = str_replace('cp_orders', 'cp_orders_'.$startSuffix, $sql4);
				if ($startSuffix !== date('Y')) $sql6 = str_replace('cp_orders', 'cp_orders_'.($startSuffix+1), $sql4);
			}
		}

		$sql0 = 'select * from cp_get_orders_tmp order by created desc limit '.$limit;
		$sql1 = "select count(*) total, sum(if((status in({$this->status['concel']}, 610, 620, {$this->status['notwin']}, {$this->status['out_of_date']}, {$this->status['out_of_date_pay']}) || (status = {$this->status['win']} && my_status in(1, 3, 4, 5))), 0, 1)) notover, sum(buyMoney) money, sum(if(status in({$this->status['win']}), margin, 0)) prize
		from cp_get_orders_tmp into @total, @notover, @money, @prize";
		
		$this->slave->query("set @total = 0, @notover = 0, @money = 0, @prize = 0");
		$res = $this->slave->query("call bn_cpiao_tmp.cp_get_orders(\"{$sql0}\", \"{$sql1}\", \"{$sql2}\", \"{$sql3}\", \"{$sql4}\", \"{$sql5}\", \"{$sql6}\", @total, @notover, @money, @prize)")->getAll();
		$total = array();
		if ($res) $total = $this->slave->query('select @total as total, @notover as notover, @money as money, @prize as prize')->getRow();
		
		return array('totals' => $total, 'datas' => $res);
	}

    // 根据单号获取订单
    public function getOrder( $cons )
    {
    	$date = date('Y-m-d H:i:s', strtotime(substr($cons['orderId'], 0, 14)));
    	$tableSuffix = $this->tools->getTableSuffixByDate($date);
    	if($tableSuffix && $tableSuffix < '2014')
    	{
    		return array('data' => array());
    	}
    	if($tableSuffix) $tableSuffix = '_' . $tableSuffix;   	
		$sql = "SELECT o.orderId, o.codes, o.codecc, o.lid, o.money, o.multi, o.issue, o.playType, o.isChase, o.orderType,o.singleFlag, o.betTnum, o.status, o.endTime, o.my_status, o.bonus, o.margin, o.shopId, o.failMoney, o.activity_ids, o.activity_status, o.created,o.buyPlatform,d.redpackId,d.redpackMoney 
                FROM {$this->tbname}{$tableSuffix} as o left join cp_orders_detail d on d.orderId=o.orderId
                WHERE o.uid = ? AND o.orderId = ? AND o.orderType in ('0', '1', '3', '6')";
		$order['data'] = $this->slave->query($sql, $cons)->getRow();
        return $order;
    }
    //更新订单状态
    public function upStatus($orderid, $status)
    {
    	$this->db->query("update {$orderName} set status = {$this->status[$status]} where orderId = ?", array($orderid));
    }
    //保存手动付款失败订单
   	private function saveFailOrder($uid, $orderId, $trade_no)
   	{
   		$sql = "insert cp_orders_fail(uid, orderId, trade_no, try_times, created) values(?, ?, ?, 1, now()) 
   		on duplicate key update try_times = try_times + values(try_times)";
   		$this->db->query($sql, array($uid, $orderId, $trade_no));
   	}
   	//系统自动付款
	public function agentPay()
	{
		$sql = "select m.uid, m.orderId, m.trade_no, m.money, m.status, 
		TIMESTAMPDIFF(MINUTE , n.created , now() ) minutes, n.try_times, n.span from {$this->tbname} m 
		join cp_orders_fail n on m.orderId = n.orderId where n.status = 0 and m.status in 
		({$this->status['pay_fail']}, {$this->status['out_of_date_pay']})  limit 100";
		$orders = $this->db->query($sql)->getAll();
		$this->load->model('wallet_model');
		$orderids = array();
		foreach ($orders as $order)
		{
			if($order['status'] == $this->status['pay_fail'] && $order['minutes'] > $order['span'])
			{
				$orderData = array('uid' => $order['uid'], 'ctype' => 'pay', 'orderId' => $order['orderId'], 
				'trade_no' => $order['trade_no'], 'money' => $order['money']);
				$response = $this->wallet_model->dealPay($orderData);
				if($response['code'] == 0)
				{
					array_push($orderids, $order['orderId']);
				}
				$this->db->query("update cp_orders_fail set span = ? where orderId = ?", array($order['minutes'] + $order['try_times'], $order['orderId']));
			}
			elseif($order['status'] == $this->status['out_of_date_pay'])
			{
				$this->wallet_model->payFail($order['uid'], $order['orderId'], $order['trade_no']);
				array_push($orderids, $order['orderId']);
			}
		}
		if(!empty($orderids))
		{
			$this->db->query("update cp_orders_fail set status = 1 where orderId in ('" . implode("','", $orderids) . "')");
		}
	}
	//报警统计
	public function orderAlert($span)
	{
		$sql = "insert cp_alert(cdate, content) select date(now()), count(*) 
		from {$this->tbname} where modified >= date_sub(now(), interval $span hour) and 
		status in ({$this->status['pay_fail']})
		on duplicate key update content = values(content) ";
		$this->db->query($sql);
	}
	
	public function getById($orderId, $db = 'db')
	{
		$date = date('Y-m-d H:i:s', strtotime(substr($orderId, 0, 14)));
		$tableSuffix = $this->tools->getTableSuffixByDate($date);
		if($tableSuffix && $tableSuffix < '2014')
		{
			return array();
		}
		if($tableSuffix) $tableSuffix = '_' . $tableSuffix;
		return $this->$db->query("select o.lid, o.playType, o.codes, o.money, o.status, o.endTime, o.betTnum, o.singleFlag, d.redpackId, d.redpackMoney 
		from {$this->tbname}{$tableSuffix} as o left join cp_orders_detail as d on d.orderId=o.orderId where o.orderId = ?", array($orderId))->getRow();
	}

	//累计中奖金额
	public function totalWin()
	{
		$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$numbers = unserialize($this->cache->redis->get($REDIS['TOTAL_WIN']));
		if(empty($numbers))
		{
			$allwin = 8473;
			$total = $this->BcdDb->query("select sum(bonus) allwin from cp_orders_win")->getOne();
			$allwin = $allwin + intval($total/1000000);
			$allwin = strval($allwin);
			$len = strlen($allwin);
			$numbers = array();
			$pos = 0;
			for($pos; $pos < $len; $pos++)
			{
				array_push($numbers, $allwin[$pos]);
			}
			$this->cache->redis->save($REDIS['TOTAL_WIN'], serialize($numbers), 3600);
		}
		return $numbers;
	}

	/*
     * 查询订单拆票详情
     * @author:liuli
     * @date:2015-02-04
     *
     * @param $uid 用户ID
     * @param $orderId 订单号
     * @return array
     */
    public function getOrderDetail($uid, $orderId)
    {
        $orders = array();
        
        $PostData['JSON'] = array(
            'token' => $uid,
            'uid' => $uid,
            'orderId' => $orderId,
        );

        $orderResponse = $this->tools->request($this->busiApi.'2345/ticket/v1/order/ticket', $PostData);

        $orderResponse = json_decode($orderResponse,true);
        
        if ($orderResponse['code'] == 0) {
            $orders = $orderResponse['data'];
        }

        return $orders;
    }
    
    public function getJjcMatchDetail($lid, $codecc)
    {
    	$data = array();
    	$paiqiTable = '';
    	if($lid == JCZQ)
    	{
    		$paiqiTable = 'cp_jczq_paiqi';
    		$fields = "m_date, mname, mid, league, home, away, full_score, half_score, rq, end_sale_time, m_status";
    	}
    	elseif($lid == JCLQ)
    	{
    		$paiqiTable = 'cp_jclq_paiqi';
    		$fields = "m_date, mname, preScore, mid, league, home, away, full_score, rq, begin_time, m_status";
    	}
    	if($paiqiTable && $codecc)
    	{
    		$mids = explode(' ', $codecc);
    		$mids = implode("','", $mids);
    		
    		$matchs = $this->slaveCfg1->query("select {$fields} from {$paiqiTable} where mid in ('{$mids}')")->getAll();
    		foreach ($matchs as $val)
    		{
    			$match = array();
    			$match['issue'] = $val['m_date'];
    			$match['mid'] = $val['mid'];
    			$match['name'] = $val['league'];
    			$match['home'] = $val['home'];
    			$match['awary'] = $val['away'];
    			$match['score'] = $val['full_score'];
    			$match['scoreHalf'] = $val['half_score'];
    			$match['let'] = $val['rq'];
    			$match['dt'] = isset($val['end_sale_time']) ? strtotime($val['end_sale_time'])*1000 : strtotime($val['begin_time'])*1000;
    			$match['mStatus'] = $val['m_status'];
    			$match['mname'] = $val['mname'];
    			$match['preScore'] = $val['preScore'];
    			array_push($data, $match);
    		}
    	}
    	return $data;
    }
    
    /**
     * 出票详情
     */
    public function getJjcOrderDetail($orderId)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$sql = "SELECT s.lid,s.sub_order_id,s.subCodeId,s.codes,s.money,s.multi,s.betTnum,s.status,s.bonus,s.margin,s.ticket_time,s.playType,r.mid,r.pdetail,r.odds 
                FROM cp_orders_split{$suffix} s JOIN cp_orders_relation{$suffix} r ON s.sub_order_id=r.sub_order_id WHERE s.orderId=? 
        		order by case when s.status='600' then 1 else 0 end";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	foreach ($res as $val)
    	{
    		$data[$val['sub_order_id']]['lid'] = $val['lid'];
    		$data[$val['sub_order_id']]['codes'] = $val['codes'];
    		$data[$val['sub_order_id']]['money'] = $val['money'];
    		$data[$val['sub_order_id']]['multi'] = $val['multi'];
    		$data[$val['sub_order_id']]['betTnum'] = $val['betTnum'];
    		$data[$val['sub_order_id']]['status'] = $val['status'];
    		$data[$val['sub_order_id']]['bonus'] = $val['bonus'];
    		$data[$val['sub_order_id']]['margin'] = $val['margin'];
    		$data[$val['sub_order_id']]['ticket_time'] = $val['ticket_time'];
    		$data[$val['sub_order_id']]['playType'] = $val['playType'];
    		$data[$val['sub_order_id']]['info'][$val['mid']] = $val['pdetail'];
    	}
    	
    	return $data;
    }
    
    public function getGjDetail($teams, $lid) {
    	switch ($lid) {
    		case GJ:
    			$type = 1;
    			break;
    		case GYJ:
    			$type = 2;
    			break;
    	}
    	$this->dcDB = $this->load->database('dc', true);
    	$sql = "select name, status, mid from cp_champion_paiqi where type = ? and mid in (".implode(',', $teams).")";
    	return $this->slaveDc->query($sql, array($type))->getAll();
    }
    
    public function getGjOrderDetail($order, $status) {
    	$data = array();
    	if(!$order)
    	{
    		return $data;
    	}
    	$tableSuffix = '';
    	if (in_array($status, array(1000, 2000))) {
    		$tableSuffix = $this->tools->getTableSuffixByOrder($order);
    	}
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$sql = "select s.lid,s.sub_order_id,s.subCodeId,s.codes,s.money,s.multi,s.betTnum,s.status,s.bonus,s.margin,r.mid,r.pdetail,r.odds,s.ticket_time
    	from cp_orders_split{$suffix} s JOIN cp_orders_relation{$suffix} r ON s.sub_order_id=r.sub_order_id where s.orderId = ? 
    	order by case when s.status='600' then 1 else 0 end";
    	$res = $this->slaveCfg1->query($sql, array($order))->getAll();
    	return $res;
    }

    /**
     * 出票详情
     */
    public function getNumOrderDetail($orderId, $lid)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$tables = $this->getSplitTable($lid);
    	$sql = "select lid, sub_order_id, codes, betTnum, multi, status, bonus, otherBonus, bonus_detail, isChase, multi, playType, ticket_time from {$tables['split_table']}{$suffix} where orderId = ?
    	order by case when status='600' then 1 else 0 end, sub_order_id";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	return $res;
    }
    
    /**
     * 出票详情
     */
    public function getBonusOptDetail($orderId)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$sql = "SELECT lid,sub_order_id,subCodeId,codes,SUM(multi) multi,status,SUM(bonus) bonus,SUM(margin) margin FROM cp_orders_split{$suffix} WHERE orderId=? GROUP BY subCodeId";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	return $res;
    }
    
    /**
     * 数字彩开奖信息
     * @param unknown_type $lid
     * @param unknown_type $issue
     * @return multitype:|multitype:number unknown mixed
     */
    public function getNumIssue($lid, $issue)
    {
    	$t_arr = array(
    			PLS => array('lname' => 'pl3', 'issue' => $this->libcomm->format_issue($issue, 0)),
    			PLW => array('lname' => 'pl5', 'issue' => $this->libcomm->format_issue($issue, 0)),
    			SSQ => array('lname' => 'ssq', 'issue' => $issue),
    			FCSD => array('lname' => 'fc3d', 'issue' => $issue),
    			QXC => array('lname' => 'qxc', 'issue' => $this->libcomm->format_issue($issue, 0)),
    			SYXW => array('lname' => 'syxw', 'issue' => $issue),
    			JXSYXW => array('lname' => 'jxsyxw', 'issue' => $issue),
    			HBSYXW => array('lname' => 'hbsyxw', 'issue' => $issue),
    			QLC => array('lname' => 'qlc', 'issue' => $issue),
    			DLT => array('lname' => 'dlt', 'issue' => $this->libcomm->format_issue($issue, 0)),
    			KS => array('lname' => 'ks', 'issue' => $issue),
    			JLKS => array('lname' => 'jlks', 'issue' => $issue),
    	        JXKS => array('lname' => 'jxks', 'issue' => $issue),
    			KLPK => array('lname' => 'klpk', 'issue' => $issue),
    			CQSSC => array('lname' => 'cqssc', 'issue' => $issue),
    	       GDSYXW => array('lname' => 'gdsyxw', 'issue' => $issue),
    	);
    	$data = array();
    	if(!isset($t_arr[$lid]['lname']) || empty($issue))
    	{
    		return $data;
    	}
    	 
    	$sql = "select * from cp_{$t_arr[$lid]['lname']}_paiqi where issue = ?";
    	$res = $this->slaveCfg1->query($sql, array($t_arr[$lid]['issue']))->getRow();
    	if($res)
    	{
    		$data['seExpect'] = $issue;
    		$data['awardNumber'] = str_replace(array('|', '(', ')'), array(':', ':', ''), $res['awardNum']);
    		$data['bonusDetail'] = json_decode($res['bonusDetail'], true);
    		$data['seLotid'] = $lid;
    		$data['seEndtime'] = strtotime($res['end_time'])*1000;
    		$data['awardTime'] = strtotime($res['award_time'])*1000;
    	}
    	 
    	return $data;
    }
    
    /**
     * 胜负彩、任九开奖详情
     * @param unknown_type $mid
     */
    public function getSfcAward($mid)
    {
    	$data = array();
    	if(empty($mid))
    	{
    		return $data;
    	}
    	
    	$mid = $this->libcomm->format_issue($mid, 0);
    	$sql = "select result from cp_rsfc_paiqi where mid = ?";
    	$res = $this->slaveCfg1->query($sql, array($mid))->getRow();
    	if($res)
    	{
    		$data['seExpect'] = $mid;
    		$data['awardNumber'] = $res['result'];
    	}
    	 
    	return $data;
    }
    
    /**
     * 胜负彩、任九对阵信息
     * @param unknown_type $mid
     * @return multitype:
     */
    public function getSfcMatchs($mid)
    {
    	$data = array();
    	if(empty($mid))
    	{
    		return $data;
    	}
    	$mid = $this->libcomm->format_issue($mid, 0);
    	$sql = "select league, home, away, begin_date from cp_tczq_paiqi where mid = ? and ctype=1";
    	$res = $this->slaveCfg1->query($sql, array($mid))->getAll();
    	foreach ($res as $val)
    	{
    		$match = array();
    		$match['gameName'] = $val['league'];
    		$match['issueId'] = $mid;
    		$match['teamName1'] = $val['home'];
    		$match['teamName2'] = $val['away'];
    		$match['gameTime'] = strtotime($val['begin_date'])*1000;
    		array_push($data, $match);
    	}
    	
    	return $data;
    }

    /**
     * 取最近三个月订单
     */
    public function getNewOrders($uid, $lid, $start, $end) 
    {
    	$sql = "select orderId, codes, created, money, issue, status, my_status, margin, endTime, lid
    			from cp_orders force index(uid)
    			where uid = ? and lid= ? and status not in(0, 20)
    			order by created desc
    			limit {$start}, {$end}";
    	$res = $this->slave->query($sql, array($uid, $lid))->getAll();
    	return $res;
    }
    
    /**
     * 返回票商、投注站信息
     * @param unknown_type $orderId
     */
    public function getTicketSeller($orderId)
    {
    	$data = array(
    		'ticket_seller' => 0,
    		'shopId' => 0
    	);
    	$order = $this->order_model->getById($orderId);
    	if($order)
    	{
    		$REDIS = $this->config->item('REDIS');
    		$this->load->driver('cache', array('adapter' => 'redis'));
    		$ticketSeller = unserialize($this->cache->get($REDIS['TICKET_SELLER'])); //分配票商
    		$data['ticket_seller'] = $ticketSeller[$order['lid']];
    		$data['shopId'] = $this->getBetStation($data['ticket_seller'], $order['lid']);
    	}
    	
    	return $data;
    }
    
    /*
     * 出票订单随机分配投注站
    * @date:2016-02-01
    */
    public function getBetStation($seller, $lid)
    {
    	$partnerArr = $this->config->item('cfg_partner_lid');
    	$search = array(
    			'partner_name' => $seller,
    			'lottery_type' => $this->getLotteryType($lid),
    			'status' => '30',
    			'delete_flag' => '0',
    			'lid' => array_key_exists($lid, $partnerArr) ? $lid : '0'
    	);
    
    	$sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone,
    	qq, webchat, other_contact, address, fail_reason, off_reason,
    	delete_flag, status, created
    	FROM cp_partner_shop
    	WHERE partner_name = ? AND lottery_type = ? AND status = ? AND delete_flag = ? AND lid = ?";
    
    	$stationInfo = $this->slave->query($sql, array($search['partner_name'], $search['lottery_type'], $search['status'], $search['delete_flag'], $search['lid']))->getAll();
    
    	$shopId = '0';
    	if(!empty($stationInfo))
    	{
    		$stationNum = count($stationInfo) - 1;
    		$stationIndex = rand(0, $stationNum);
    		$shopId = $stationInfo[$stationIndex]['id'];
    	}
    	return $shopId;
    }
    
    /**
     * [getLotteryType 获取彩种所属类型(福彩/体彩) 福彩：双色球，福彩3D，七乐彩 经典快3 易快3]
     * @author LiKangJian 2017-11-29
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    public function getLotteryType($lid)
    {
    	if(in_array($lid, array('51', '52', '23528', '53','56','57')))
    	{
    		$lotteryType = 1;
    	}
    	else
    	{
    		$lotteryType = 0;
    	}
    	return $lotteryType;
    }
    
    /*
     * 获取彩种所属类型 福彩 体彩
    * @date:2016-01-27
    */
    public function upOutOfDay($orderId)
    {
    	$this->db->query("update cp_orders set status = ? where orderId = ?", 
    	array($this->status['out_of_date'], $orderId));
    }

    /**
     * 订单加奖信息
     */
    public function getJjDetail($orderId)
    {
        return $this->db->query('SELECT jj_id, orderId, add_money FROM cp_activity_jj_order WHERE orderId = ?', array($orderId))->getRow();
    }
    
    public function savePromote($modified) {
    	$this->db->trans_start();
    	$sql = "SELECT p.key, p.uid, SUM(o.money-o.failMoney) as money, GROUP_CONCAT(CAST(o.id AS CHAR)) as oid FROM cp_orders as o right join cp_promote as p on p.uid=o.uid
				where o.modified >= '".$modified." 00:00:00' and o.modified <= '".$modified." 23:59:59' and (o.cstate & 8) = 0 and o.status in (500, 510, 1000, 2000)
				GROUP BY p.uid limit 500";
    	$res = $this->db->query($sql)->getAll();
    	if (!empty($res)) {
    		foreach ($res as $val) {
    			$this->db->query("insert into cp_promote_log (uid, cdate, `key`, spend, pay, created) values({$val['uid']}, '{$modified}', '{$val['key']}', {$val['money']}, '".(3 * $val['money']/100)."', NOW())");
    			if (!$this->db->affected_rows()) {
    				$this->db->trans_rollback();
    			}
    			$this->db->query('update cp_orders set cstate = cstate | 8 where id in ('.$val['oid'].')');
    			if (!$this->db->affected_rows()) {
    				$this->db->trans_rollback();
    			}
    		}
    		$this->db->trans_complete();
    		$this->savePromote($modified);//在查到数据的情况下，继续递归调用，查询下面500条，如果为空则事务失败
    	}else {
    		$this->db->trans_rollback();
    	}
    }
    
    public function getPromote($cdate) {
    	$sql = "select `key`, sum(spend) as spend, sum(pay) as pay from cp_promote_log where cdate = ? group by `key`";
    	return $this->db->query($sql, array($cdate))->getAll();
    }
    
    /**
     * 出票成功发邮件操作
     * @param string $sdate	开始时间
     * @param string $edate 结束时间
     */
    public function ticketEmail($sdate, $edate)
    {
    	$sql = "select o.uid, o.userName, o.orderId, o.buyPlatform, o.codes, o.lid, o.money, o.multi, o.issue, o.pay_time,
    	o.playType, o.isChase, o.orderType, o.betTnum, o.status, o.codecc, o.created, u.email from cp_orders as o
    	inner join cp_user as u on o.uid=u.uid
    	inner join cp_user_info as i on o.uid=i.uid
    	where ((i.msg_send & 2) = 2) and o.modified >= ? and o.modified < ? and o.status in ('500', '510') 
    	and o.lid not in ('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421') and ((o.cstate & 4) = 0) AND o.orderType != 4
    	limit 50";
    	$orders = $this->db->query($sql, array($sdate, $edate))->getAll();
    	while(!empty($orders) && !empty($orders[0]['orderId']))
    	{
    		$this->load->library('emailtemplate/SuccTemplate');
    		foreach ($orders as $order)
    		{
    			if($order['email'])
    			{
    				$message = $this->succtemplate->index($order);
    				$data = array(
    					'to'	  => $order['email'],
    					'subject' => '出票成功通知',
    					'message' => $message,
                        'bcc'     => '166cai@km.com'
    				);
    				$emailsql = "INSERT INTO cp_order_email_logs(uid,email, orderId, ctype, title,content,created) VALUES (?, ?, ?, 1, '出票成功通知', ?, NOW()) 
    				ON DUPLICATE KEY UPDATE email = VALUES(email), content = VALUES(content)";
    				$this->db->query($emailsql, array($order['uid'], $order['email'], $order['orderId'], $message));
    				//修改成阿里云
    				$result = $this->tools->sendMail($data);
    				//$result = $this->tools->sendMail($data,array(),1);
    				if(!$result)
    				{
    					log_message("log", "订单Id：{$order['orderId']} 发送出票成功邮件失败，邮件未成功送达。", "ticketSuccEmailWaring");
    				}
    			}
    			else
    			{
    				log_message("log", "订单Id：{$order['orderId']} 发送出票成功邮件失败，该用户未绑定邮箱。", "ticketSuccEmailWaring");
    			}
    			
    			$this->db->query("update cp_orders set cstate = cstate | 4 where orderId = ?", array($order['orderId']));
    		}
    		
    		$orders = $this->db->query($sql, array($sdate, $edate))->getAll();
    	}
    }
    
    public function getMaxTickettime($orderId) {//出票邮件竞技彩用
    	$sql = "select max(ticket_time) as ticket_time from cp_orders_split where orderId = ? and status = 500";
    	return $this->cfgDB->query($sql, array($orderId))->getRow();
    }
    
    public function countSplitOrders() {        
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$lidArr = array(21406 => 'SYXW_ISSUE_TZ', 21407 => 'JXSYXW_ISSUE_TZ', 21408 => 'HBSYXW_ISSUE_TZ', 21421 => 'GDSYXW_ISSUE_TZ', 53 => 'KS_ISSUE_TZ', 56 => 'JLKS_ISSUE_TZ', 57 => 'JXKS_ISSUE_TZ', 54 => 'KLPK_ISSUE_TZ', 55 => 'CQSSC_ISSUE_TZ', 42 => '', 43 => '', 
    		51 => 'SSQ_ISSUE', 23529 => 'DLT_ISSUE', 52 => 'FC3D_ISSUE', 33 => 'PLS_ISSUE', 35 => 'PLW_ISSUE', 10022 => 'QXC_ISSUE', 23528 => 'QLC_ISSUE', 11 => '', 19 => '');
    	foreach ($lidArr as $lid => $issue) {
    		$data[$lid] = array('wait' => 0, 'uwait' => 0, 'cwait' => 0, 'draw' => 0, 'problem' => 0, 'havenot' => 0, 'summoney' => 0, 'endTime' => '0000-00-00 00:00:00');
    	}
    	$this->dcDB = $this->load->database('dc', true);
    	$springTime = $this->dcDB->query('select delay_start_time start, DATE_ADD(delay_end_time,INTERVAL 1 DAY) end from cp_issue_rearrange where lid = "ssq"')->getRow();
    	$daynum = ($springTime['start'] < date('Y-m-d H:i:s') && $springTime['end'] > date('Y-m-d H:i:s')) ? 7 : 3;
    	$splitLid = $this->config->item('split_lid');
    	$lidMap = $this->orderConfig('lidmap');
    	$sql = "SELECT count(*) as havenot, sum(money) as summoney, s.* FROM #TABLE# join
				(SELECT SUM(CASE WHEN `status` = '0' AND (error_num IN ('0','1','200021','0_0','1_0','0000','6_0','2002','') || error_num IS NULL) THEN 1 ELSE 0 END) AS wait,
					SUM(CASE WHEN `status` = '240' AND (error_num IN ('0','1','200021','0_0','1_0','0000','6_0','2002','') or error_num is null) THEN 1 ELSE 0 END) AS draw, lid, MIN(endTime) AS endTime,
					SUM(CASE WHEN ((`status` IN ('0', '240') AND error_num NOT IN ('0','1','200021','0_0','1_0','0000','6_0','2002','') and error_num is not null) || (ticket_seller = '')) THEN 1 ELSE 0 END) AS problem
				FROM #TABLE# WHERE modified > '".date('Y-m-d H:i:s', strtotime($daynum.' days ago midnight'))."' AND `status` IN ('0', '240') GROUP BY lid) s
				ON #TABLE#.lid=s.lid AND #TABLE#.endTime=s.endTime
				WHERE #TABLE#.`status` IN (0, 240) AND #TABLE#.modified > '".date('Y-m-d H:i:s', strtotime($daynum.' days ago midnight'))."'
				GROUP BY lid";
    	$res = $this->slaveCfg1->query(str_replace('#TABLE#', 'cp_orders_split', $sql))->getAll();
    	foreach ($splitLid as $lid) {
    		$table = "cp_orders_split_".$lidMap[$lid];
    		$tmp = $this->slaveCfg1->query(str_replace(array('#TABLE#', 'group by lid'), array($table, ''), $sql))->getRow();
    		if (!empty($tmp)) {
    			$res[] = $tmp;
    		}
    	}
    	foreach ($res as $val) {
    		$data[$val['lid']]['lid'] = $val['lid'];
    		$data[$val['lid']]['wait'] = $val['wait'];
    		$data[$val['lid']]['draw'] = $val['draw'];
    		$data[$val['lid']]['problem'] = $val['problem'];
    		$data[$val['lid']]['havenot'] = $val['havenot'];
    		$data[$val['lid']]['summoney'] = $val['summoney'];
    		$data[$val['lid']]['endTime'] = $val['endTime'];
    	}
    	$usql = "SELECT lid, count(*) as uwait FROM cp_united_orders where (buyTotalMoney+guaranteeAmount)*10>=money*9 AND `status` = 40 AND `created` > DATE_SUB(NOW(), INTERVAL 13 DAY) group by lid";
    	$ures = $this->BcdDb->query($usql)->getAll();
    	foreach ($ures as $uval) {
    		$data[$uval['lid']]['lid'] = $uval['lid'];
    		$data[$uval['lid']]['uwait'] = $uval['uwait'];
    	}
    	$cres = array();
    	foreach ($lidArr as $lid => $cacheName) {
    		if (!empty($cacheName)) {
    			$cache = json_decode($this->cache->get($REDIS[$cacheName]), true);
    			$cval = $this->BcdDb->query("select count(*) as cwait from cp_chase_orders as o inner join cp_chase_manage as m on o.chaseId=m.chaseId
    					where o.bet_flag in (0, 1) and o.status=0 AND o.`issue` = '{$cache['cIssue']['seExpect']}' and o.lid = {$lid} and m.status >=240 AND m.created>DATE_SUB(NOW(), INTERVAL 5 DAY)")->getRow();
    			$data[$lid]['lid'] = $lid;
    			$data[$lid]['cwait'] = $cval['cwait'];
    		}
    	}
    	return $data;
    }
    
    public function freshStatics($data) {
    	$this->cfgDB->query("delete from cp_ticket_statistics where 1");
    	$sql = 'insert into cp_ticket_statistics(lid, wait, draw, problem, havenot, summoney, endTime, cwait, uwait) values';
    	$istFlg = false;
    	foreach ($data as $key => $value) {
    		if ($value['havenot'] > 0 || $value['cwait'] > 0 || $value['uwait'] > 0) {
    			$istFlg = true;
    			$sql .= "('{$value['lid']}', '{$value['wait']}', '{$value['draw']}', '{$value['problem']}', '{$value['havenot']}', '{$value['summoney']}', '{$value['endTime']}', '{$value['cwait']}', '{$value['uwait']}'),";
    		}
    	}
    	if ($istFlg) {
    		$sql = substr($sql, 0, -1);
    		$this->cfgDB->query($sql);
    	}
    }
    
    //保存订单详情记录
    public function insertOrderDetail($data)
    {
    	$fields = array_keys($data);
    	$upfields = array('redpackId', 'redpackMoney');
    	$sql = "insert into cp_orders_detail(" . implode(',', $fields) . ", created)values(" .
    	implode(',', array_map(array($this, 'maps'), $fields)) . ", now())" . $this->onduplicate($fields, $upfields);
    	return $this->db->query($sql, $data);
    }
    
    /**
     * 当前uid下单的次数
     * @param int $uid
     * @return array
     */
    public function countOrder($uid)
    {
        $sql = "select count(id) as count from cp_orders where uid=? and status>=40";
        $count = $this->db->query($sql, array($uid))->getRow();
        return $count;
    }
    
    public function getCodesById($orderId)
    {
    	$date = date('Y-m-d H:i:s', strtotime(substr($orderId, 0, 14)));
    	$tableSuffix = $this->tools->getTableSuffixByDate($date);
    	if($tableSuffix && $tableSuffix < '2014') return array();
    	if($tableSuffix) $tableSuffix = '_' . $tableSuffix;
    	return $this->slave->query("select codes from cp_orders{$tableSuffix} where orderId = ?", array($orderId))->getCol();
    }

    // 竞彩不中包赔订单更新
    public function upJcBzbpOrder($orderData)
    {
    	$sql = "UPDATE cp_orders AS o LEFT JOIN cp_activity_jcbp_join AS j ON o.orderId = j.orderId SET o.is_hide = 0, j.status = '240' WHERE o.orderId = ?";
    	$this->db->query($sql, array($orderData['orderId']));
    }

    // 乐善奖奖金
    public function getLsDetail($orderId, $lid)
    {
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$tables = $this->getSplitTable($lid);
    	$sql = "SELECT s.lid, s.sub_order_id, s.codes, s.betTnum, s.multi, s.status, s.bonus, s.otherBonus, s.bonus_detail, s.isChase, s.multi, s.playType, s.ticket_time, d.awardNum, d.bonus_detail, d.margin FROM {$tables['split_table']}{$suffix} AS s LEFT JOIN cp_orders_split_detail AS d ON s.sub_order_id = d.sub_order_id WHERE s.orderId = ?";
    	return $this->slaveCfg->query($sql, array($orderId))->getAll();
    }
}
