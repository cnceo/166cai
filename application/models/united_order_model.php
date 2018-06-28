<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 合买 - 订单 - 模型层
 */
class United_Order_Model extends MY_Model
{
	// 合买订单状态
	public $status = array(
		'create' => 0, //订单创建
		'out_of_date' => 20, //过期未付款
		'pay' => 40, //已付款
		'drawing' => 240, //出票中
		'draw' => 500, //出票成功
		'concel' => 600, //出票失败
		'revoke_by_user' => 610, //发起人撤单
		'revoke_by_system' => 620, //未满员撤单
		'notwin' => 1000, //未中
		'win' => 2000, //中奖
	);
	public function __construct()
	{
		parent::__construct();
		$this->load->library('BetCnName');
	}

	public function getStatus()
	{
		return $this->status;
	}

	// 创建追号订单
	public function createUnitedOrder($params = '')
	{
		// 参数说明
		/*
		$params = array(
			'uid' 				=>	'',
    		'userName' 			=>	'',
			'codes' 			=>	'',
			'lid' 				=>	'',
			'codecc'			=>	'',
			'money' 			=>	'',		// 订单总额
			'multi' 			=>	'',
			'issue' 			=>	'',
			'playType' 			=>	'',
			'isChase' 			=>	'',
			'betTnum' 			=>	'',
			'endTime' 			=>	'',	
			'buyPlatform'		=>	'',
			'channel'			=>	'',
			'buyMoney'			=>	'',		// 认购金额
			'commissionRate'	=>	'',		// 佣金比例
			'guaranteeAmount'	=>	'',		// 保底金额
			'openStatus'		=>	'',		// 公开状态
			'openEndtime'		=>	'',		// 方案公开时间
			'ForecastBonusv'	=>	'',		// 预测奖金描述
		);
		*/
		
		$checkMap = array(
			'51' => 'SsqCheck',
			'23529' => 'DltCheck',
			'23528' => 'QlcCheck',
			'35' => 'PlwCheck',
			'10022' => 'PlcommCheck',
			'33' => 'PlsAnd3dCheck',
			'52' => 'PlsAnd3dCheck',
			'11' => 'LzcCheck',
			'19' => 'LzcCheck',
			'42' => 'JczqCheck',
			'43' => 'JclqCheck',
		);
		
		if(isset($checkMap[$params['lid']]))
		{
			$this->load->library("createcheck/{$checkMap[$params['lid']]}");
			$lName = strtolower($checkMap[$params['lid']]);
			$result = $this->$lName->check($params);
			if($result['status'] == false)
			{
				return $result;
			}
			
			// 事务开始
			$this->db->trans_start();
			// cp_united_orders表初始化
			$unitedInfo = array(
				'orderId'			=>	$this->tools->getIncNum('UNIQUE_KEY'),
				'uid'				=>	$params['uid'],
				'lid'				=>	$params['lid'],
				'issue'				=>	$params['issue'],
				'playType' 			=> 	$this->getLotteryPlayType($params),
				'money'				=>	ParseUnit($params['money']),
				'buyMoney'			=>	ParseUnit($params['buyMoney']),
				'buyTotalMoney'		=>	ParseUnit($params['buyMoney']),
				'commissionRate'	=>	$params['commissionRate'],
				'status'			=>	$this->status['create'],
				'guaranteeAmount'	=>	ParseUnit($params['guaranteeAmount']),
				'openStatus'		=>	$params['openStatus'],
				'buyPlatform'		=>	$params['buyPlatform'],
				'isChase' 			=> 	$params['isChase'],
				'popularity'		=>	0,
				'openEndtime'		=>	$params['openEndtime'],
				'ForecastBonusv'	=>	$params['ForecastBonusv'] ? $params['ForecastBonusv'] : '',
				'endTime'			=>	$params['endTime'],
				'end_sale_time'		=>	$this->getEndSaleTime($params),
			);
			
			$unitedRes = $this->saveUnitedOrder($unitedInfo);

			// 新增合买宣言
			if(!empty($params['united_intro']))
			{
				$details = array(
					'orderId'		=>	$unitedInfo['orderId'],
					'uid'			=>	$unitedInfo['uid'],
					'introduction'	=>	$params['united_intro'],
				);
				$detailRes = $this->saveUnitedDetail($details);
			}
			else
			{
				$detailRes = TRUE;
			}
			
			$REDIS = $this->config->item('REDIS');
			$lotteryConfig = json_decode($this->cache->get($REDIS['LOTTERY_CONFIG']), true);
			// cp_orders表初始化
			$orderInfo = array(
				'orderId'		=>	$unitedInfo['orderId'],
				'uid' 			=>	$params['uid'],
	    		'userName' 		=> 	$params['userName'],
				'codes' 		=> 	$params['codes'],
				'codecc' 		=> 	isset($params['codecc']) ? $params['codecc'] : '',
				'lid' 			=> 	$params['lid'],
				'money' 		=> 	ParseUnit($params['money']),
				'multi' 		=> 	$params['multi'],
				'issue' 		=> 	$params['issue'],
				'playType' 		=> 	$this->getLotteryPlayType($params),
				'isChase' 		=> 	$params['isChase'],
				'betTnum' 		=> 	$params['betTnum'],
				'status'		=>	$this->status['create'],
				'orderType' 	=> 	'4',
				'mark' 			=> 	0,
				'endTime' 		=> 	date('Y-m-d H:i:s', strtotime($params['endTime'])+$lotteryConfig[$params['lid']]['united_ahead'] * 60+$lotteryConfig[$params['lid']]['ahead'] * 60),
				'buyPlatform' 	=> 	$params['buyPlatform'],
				'channel'		=>	$params['channel'],
				'app_version'	=>	isset($params['app_version']) ? $params['app_version'] : '0',
				'singleFlag'    =>  isset($params['singleFlag']) ? $params['singleFlag'] : 0
			);
	
			$orderFields = array_keys($orderInfo);
			$orderSql = "insert cp_orders(" . implode(',', $orderFields) . ", created)
			values(". implode(',', array_map(array($this, 'maps'), $orderFields)) .", now())";
			$orderRes = $this->db->query($orderSql, $orderInfo);
	
			if($unitedRes && $detailRes && $orderRes)
			{
				$this->db->trans_complete();
			
				$result = array(
						'status' => true,
						'msg' => '创建合买订单成功',
						'data' => $unitedInfo
				);
			}
			else
			{
				$this->db->trans_rollback();
			
				$result = array(
						'status' => false,
						'msg' => '创建合买订单失败',
						'data' => $unitedInfo
				);
			}
			return $result;
		}
		else
		{		
			$result = array(
				'status' => false,
				'msg' => '创建合买订单失败',
				'data' => $unitedInfo
			);
		}
		return $result;
	}

	// 针对十一选五、排列三、福彩3D 混合投注playType处理
	public function getLotteryPlayType($params)
	{
		if(in_array($params['lid'], array('21406', '33', '52', '21407', '21408', '54', '55')))
        {
            $typeArry = array();
            $codes = explode(';', $params['codes']);
            foreach ($codes as $code) 
            {
                $codeArry = explode(':', $code);
                array_push($typeArry, intval($codeArry[1]));
            }
            $typeArry = array_unique($typeArry);
            if(count($typeArry) == 1)
            {
                $params['playType'] = $typeArry[0];
            }
            else
            {
                $params['playType'] = 0;
            }
        }
		return $params['playType'];
	}

	// 查询发起人合买订单信息
	public function getUnitedOrderDetail($orderId, $uid = null)
	{
		$sql = "SELECT orderId, trade_no, uid, lid, issue, money, buyMoney, buyTotalMoney, commissionRate, status, guaranteeAmount, webguranteeAmount, openStatus, buyPlatform, commission, orderBonus, orderMargin, margin, popularity, isTop, openEndtime, ForecastBonusv, endTime, refund_time, sendprize_time, my_status, cstate, bet_flag, created 
        FROM cp_united_orders WHERE orderId = ? ";
		$data = array($orderId);
		if ($uid) {
			$sql .= " AND uid = ?";
			$data[] = $uid;
		}
		$sql .= " for update";
		$info = $this->db->query($sql, $data)->getRow();
		return $info;
	}

	// 查询提前截止时间
	public function getEndSaleTime($params)
	{
		$end_sale_time = $params['endTime'];

		// 查询映射表
		$lidMaps = array(
			// 数字彩
			'23529'	=>	array('cp_dlt_paiqi', 'end_time', 'issue', 2),
			'51'	=>	array('cp_ssq_paiqi', 'end_time', 'issue', 0),
			'52'	=>	array('cp_fc3d_paiqi', 'end_time', 'issue', 0),
			'33'	=>	array('cp_pl3_paiqi', 'end_time', 'issue', 2),
			'35'	=>	array('cp_pl5_paiqi', 'end_time', 'issue', 2),
			'10022'	=>	array('cp_qxc_paiqi', 'end_time', 'issue', 2),
			'23528'	=>	array('cp_qlc_paiqi', 'end_time', 'issue', 0),
			// 老足彩
			'11'	=>	array('cp_tczq_paiqi', 'end_sale_time', 'mid', 2),
			'19'	=>	array('cp_tczq_paiqi', 'end_sale_time', 'mid', 2),
			// 竞彩
			'42'	=>	array('cp_jczq_paiqi', 'end_sale_time', 'mid', 0),
			'43'	=>	array('cp_jclq_paiqi', 'begin_time', 'mid', 0),
		);

		$info = array();
		$exta = '';
		if(!empty($lidMaps[$params['lid']]))
		{
			// 查询字段
			$select = $lidMaps[$params['lid']][2];
			// 表名
			$table = $lidMaps[$params['lid']][0];
			// 条件字段名
			$type = $lidMaps[$params['lid']][1];
			// 投注期次至Paiqi格式
			$splitNum = $lidMaps[$params['lid']][3];

			if(in_array($params['lid'], array(42, 43)))
			{
				$issue = str_replace(" ", "','", trim($params['codecc']));
			}
			elseif(in_array($params['lid'], array(11, 19))) 
			{
				$issue = substr($params['issue'], $splitNum);
				$exta = ' AND ctype = 1';
			}
			else
			{
				$issue = substr($params['issue'], $splitNum);
			}

			// 查询
			$sql = "SELECT {$type} FROM {$table} WHERE {$select} IN ('" . $issue . "'){$exta} ORDER BY {$type} ASC";
			$info = $this->cfgDB->query($sql)->getAll();

			if(!empty($info))
			{
				$end_sale_time = $info[0][$type];
				$end_sale_time = date('Y-m-d H:i:s', strtotime('-5 minute', strtotime($end_sale_time)));
			}
		}
		return $end_sale_time;
	}

	// 查询认购合买订单信息
	public function getUnitedBuyOrders($orderId)
	{
		$sql = "SELECT orderId, trade_no, subscribeId, lid, issue, money, uid, puid, buyMoney, status, buyPlatform, orderType, margin, my_status, cstate FROM cp_united_join WHERE orderId = ? ORDER BY created ASC";
		$info = $this->db->query($sql, array($orderId))->getAll();
		return $info;
	}

	// 保存合买订单信息
	public function saveUnitedOrder($unitedInfo)
	{
		$unitedFields = array_keys($unitedInfo);
		$unitedSql = "insert cp_united_orders(" . implode(',', $unitedFields) . ", created)
		values(". implode(',', array_map(array($this, 'maps'), $unitedFields)) .", now())";
		return $this->db->query($unitedSql, $unitedInfo);
	}

	// 新增认购记录
	public function saveJoinOrder($unitedInfo)
	{
		$unitedFields = array_keys($unitedInfo);
		$unitedSql = "insert cp_united_join(" . implode(',', $unitedFields) . ", created)
		values(". implode(',', array_map(array($this, 'maps'), $unitedFields)) .", now())";
		return $this->db->query($unitedSql, $unitedInfo);
	}

	// 查询已过期的合买订单
	public function getExpiredOrders()
	{
		$sql = "SELECT orderId, trade_no, uid, lid, issue, money, buyMoney, buyTotalMoney, commissionRate, status, guaranteeAmount, webguranteeAmount, openStatus, buyPlatform, commission, orderBonus, orderMargin, margin, popularity, isTop, openEndtime, ForecastBonusv, endTime, refund_time, sendprize_time, my_status, cstate, bet_flag, created FROM cp_united_orders WHERE endTime >= date_sub(now(), interval 1 day) AND endTime <= now() AND status <> " . $this->status['out_of_date'] . " AND status <= " . $this->status['draw'] . " AND ((status = " . $this->status['create'] . ") OR (status = " . $this->status['pay'] . " AND floor(((buyTotalMoney + guaranteeAmount) / money) * 100 ) < 95 AND (cstate & 1 = 0)) OR (guaranteeAmount > 0 AND buyTotalMoney + guaranteeAmount >= money AND (cstate & 2 = 0) AND (cstate & 4 = 0) AND (cstate & 1024 != 0)) OR (buyTotalMoney + guaranteeAmount < money AND (cstate & 2 = 0) AND (cstate & 8 = 0) AND (cstate & 1024 != 0))) ORDER BY orderId ASC LIMIT 300";
		$info = $this->db->query($sql)->getAll();
		return $info;
	}

	// 更新过期订单状态
	public function updateExpiredStatus($orderInfo, $tranc = true)
	{
		if($tranc)
        {
        	// 开启事务
            $this->db->trans_start();
        }

        // 更新cp_united_orders
        $this->db->query("UPDATE cp_united_orders SET status = {$this->status['out_of_date']} WHERE orderId = ? AND endTime <= now()", array($orderInfo['orderId']));
        $unitedRes = $this->db->affected_rows();
		
		// 更新cp_orders
		$this->db->query("UPDATE cp_orders SET status = {$this->status['out_of_date']} WHERE orderId = ?", array($orderInfo['orderId']));
		$orderRes = $this->db->affected_rows();

		if($unitedRes && $orderRes)
		{
			if($tranc)
            {
                $this->db->trans_complete();
            }
            return true;
		}
		else
		{
			if($tranc)
            {
                $this->db->trans_rollback();
            }
            return false;
		}
	}

	// 处理已付款过期订单
	public function handleExpiredOrder($orderInfo)
	{
		// 开启事务
		$this->db->trans_start();

		// 认购订单锁表
		$unitedInfo = $this->getUnitedOrderDetail($orderInfo['orderId']);

		// 尚未出票且认购保底总额小于95%
		$sMoney = intval($unitedInfo['buyTotalMoney']) + intval($unitedInfo['guaranteeAmount']);
		$tMoney = intval($unitedInfo['money']);
		$percent = $this->getUnitedPercent($sMoney, $tMoney);
		if(!empty($unitedInfo) && $unitedInfo['status'] == $this->status['pay'] && $percent < 95 && ($unitedInfo['cstate'] & 1) == 0)
		{
			$this->load->model('united_wallet_model');
			// 查询认购记录（包含发起人认购）
			$buyOrders = $this->getUnitedBuyOrders($orderInfo['orderId']);

			if(!empty($buyOrders))
			{
				$userArr = array();
				$orderCounts = count($buyOrders);
				$count = 0;
				foreach ($buyOrders as $order) 
				{
					array_push($userArr, $order['uid']);
					// 公共方法，单笔认购根据trade_no流水退款，方案撤单
					$refundsRes = $this->united_wallet_model->refunds($order, 2, false);
					if($refundsRes)
					{
						$count ++;
					}
					else
					{
						// 退款失败日志
						log_message('LOG', "认购退款失败: " . json_encode($order), 'united_order');
					}
				}

				// 更新cp_united_orders未满员撤单
				$this->db->query("UPDATE cp_united_orders SET status = {$this->status['revoke_by_system']}, cstate = cstate ^ 1 WHERE orderId = ? AND endTime <= now() AND (cstate & 1 = 0)", array($orderInfo['orderId']));
				$unitedRes = $this->db->affected_rows();

				$this->db->query("UPDATE cp_united_join SET status = {$this->status['revoke_by_system']} WHERE orderId = ?", array($orderInfo['orderId']));
				$unitedStatusRes = $this->db->affected_rows();

				// 更新cp_orders订单过期
				$this->db->query("UPDATE cp_orders SET status = {$this->status['out_of_date']} WHERE orderId = ?", array($orderInfo['orderId']));
				$orderRes = $this->db->affected_rows();

				if($orderCounts == $count && $unitedRes && $unitedStatusRes && $orderRes)
				{
					$this->db->trans_complete();
				}
				else
				{
					$this->db->trans_rollback();
					$userArr = array_unique($userArr);
					// 回滚钱包
					if(!empty($userArr))
					{
						foreach ($userArr as $uid) 
						{
							// 刷新钱包
            				$this->united_wallet_model->freshWallet($uid);
						}
					}
				}
			}
			else
			{
				$this->db->trans_rollback();
			}
		}
		else
		{
			$this->db->trans_rollback();
		}
	}

	// 认购进度取整
	public function getUnitedPercent($sMoney, $tMoney)
	{	
		$percent = 0;
		if($tMoney > 0)
		{
			$percent = floor($sMoney/$tMoney * 100);
		}
		return $percent;
	}

	// 查询最近修改过的订单
	public function getModifiedOrders($sdate, $edate)
	{
		$sql = "SELECT orderId, trade_no, uid, lid, issue, money, buyMoney, buyTotalMoney, commissionRate, status, guaranteeAmount, webguranteeAmount, openStatus, buyPlatform, commission, orderBonus, orderMargin, margin, popularity, isTop, openEndtime, ForecastBonusv, endTime, refund_time, sendprize_time, my_status, cstate, bet_flag, created 
			FROM cp_united_orders WHERE modified >= ? AND modified <= ?  AND ((status = {$this->status['pay']} AND floor(((buyTotalMoney + guaranteeAmount) / money) * 100 ) >= 95 AND bet_flag = 0) 
				OR ((status <= {$this->status['draw']}) AND (buyTotalMoney = money) AND (guaranteeAmount > 0) AND (cstate & 2 = 0) AND (cstate & 4 = 0)) 
				OR ((status >= {$this->status['draw']}) AND status NOT IN ('{$this->status['concel']}', '{$this->status['revoke_by_user']}', '{$this->status['revoke_by_system']}') AND (buyTotalMoney + guaranteeAmount >= money) AND (webguranteeAmount = 0) AND (cstate & 8 = 0) AND (cstate & 16 = 0))) 
			ORDER BY orderId ASC LIMIT 300";
		$info = $this->db->query($sql, array($sdate, $edate))->getAll();
		return $info;
	}
	
	/**
	 * 
	 * @param unknown $sdate
	 * @param unknown $edate
	 * @return unknown
	 */
	public function scanModifiedGrowth($sdate, $edate)
	{
	    $sql = "SELECT orderId FROM cp_united_orders WHERE modified >= ? AND modified <= ? AND (buyTotalMoney = money) AND (status >= '{$this->status['pay']}') AND (cstate & 2048 = 0) ORDER BY orderId ASC LIMIT 50";
	    $orders = $this->db->query($sql, array($sdate, $edate))->getCol();
	    while ($orders)
	    {
	        foreach ($orders as $orderId)
	        {
	            $this->db->trans_start();
	            $sql1 = "insert {$this->db_config['tmp']}.cp_orders_trg(orderId,orderType,source2, created)values
	            (?, 4, 1, now()) on duplicate key update source2 = values(source2)";
	            $res1 = $this->db->query($sql1, array($orderId));
	            $sql2 = "update cp_united_orders set cstate = cstate ^ 2048 where orderId = ?";
	            $res2 = $this->db->query($sql2, array($orderId));
	            if($res1 && $res2)
	            {
	                $this->db->trans_complete();
	            }
	            else
	            {
	                $this->db->trans_rollback();
	            }
	        }
	        
	        $orders = $this->db->query($sql, array($sdate, $edate))->getCol();
	    }
	}

	// 汇总所有最近修改过的订单
	public function getModifiedCounts($sdate, $edate)
	{
		$sql = "SELECT count(*) FROM cp_united_orders WHERE modified >= ? AND modified <= ? AND orderType = 1 AND status >= " . $this->status['pay'] . " ORDER BY orderId ASC";
		$info = $this->db->query($sql, array($sdate, $edate))->getOne();
		return $info;
	}

	// 合买投单
	public function doBetOrder($orderInfo)
	{
		// 开启事务
        $this->db->trans_start();

        $unitedInfo = $this->getUnitedOrderDetail($orderInfo['orderId']);

        // 尚未出票且认购保底总额小于95%
        $sMoney = intval($unitedInfo['buyTotalMoney']) + intval($unitedInfo['guaranteeAmount']);
		$tMoney = intval($unitedInfo['money']);
		$percent = $this->getUnitedPercent($sMoney, $tMoney);
        if(!empty($unitedInfo) && $unitedInfo['status'] == $this->status['pay'] && $percent >= 95 && $unitedInfo['bet_flag'] == 0)
        {
        	// 分配票商
        	$this->load->driver('cache', array('adapter' => 'redis'));
        	$REDIS = $this->config->item('REDIS');
        	$ticketSeller = unserialize($this->cache->get($REDIS['TICKET_SELLER']));
        	//双色球票商的特殊分配逻辑 该逻辑不需要了
	        /*if($orderInfo['lid'] == 51)
	        {
	        	$ticketSeller[51] = $this->_getSeller(51, $orderInfo['money']);
	        }*/
			$ticket_seller = $ticketSeller[$orderInfo['lid']];
			$shopId = $this->getBetStation($ticketSeller[$orderInfo['lid']], $orderInfo['lid']);

        	// 更新cp_united_orders
        	$this->db->query("UPDATE cp_united_orders SET bet_flag = 1, shopId = {$shopId} WHERE orderId = ?", array($orderInfo['orderId']));
        	$unitedRes = $this->db->affected_rows();
		
			// 更新cp_orders
			$this->db->query("UPDATE cp_orders SET status = {$this->status['pay']}, ticket_seller = ?, shopId = ?, pay_time = now() WHERE orderId = ?", array($ticket_seller, $shopId, $orderInfo['orderId']));
			$orderRes = $this->db->affected_rows();

			if($unitedRes && $orderRes)
			{
				$this->db->trans_complete();
			}
			else
			{
				$this->db->trans_rollback();
			}
        }
        else
        {
        	$this->db->trans_rollback();
        }
	}

	// 同步订单状态，奖金
	public function syncOrder()
	{
		$sql = "select o.uid, o.lid, o.orderId, o.money, o.status, o.failMoney, o.bonus, o.margin, o.my_status, u.commissionRate, u.webguranteeAmount, u.cstate, u.end_sale_time from cp_orders o LEFT JOIN cp_united_orders u ON o.orderId = u.orderId where o.modified > date_sub(now(), interval 20 minute) AND o.status >= {$this->status['drawing']} AND o.orderType = 4 and o.c_synflag = 0 limit 300";

		$orderInfo = $this->db->query($sql)->getAll();

		if(!empty($orderInfo))
		{
			foreach ($orderInfo as $order) 
			{
				// 事务开始
    			$this->db->trans_start();
    			// 行锁
    			$unitedInfo = $this->getUnitedOrderDetail($order['orderId']);

				if($order['failMoney'] > 0)
				{
					$unitedStatus = $this->status['concel'];
				}
				else
				{
					$unitedStatus = $order['status'];
				}

				// 同步大订单状态
				$this->db->query("update cp_united_orders u join cp_orders o on u.orderId = o.orderId SET u.status = $unitedStatus, u.orderBonus = o.bonus, u.orderMargin = o.margin, u.my_status = o.my_status, o.c_synflag = 1 WHERE u.orderId = ?", array($order['orderId']));
				$unitedRes = $this->db->affected_rows();

				// 同步认购订单状态
				$this->db->query("update cp_united_join set status = ?, my_status = ? where orderId = ?", array($unitedStatus, $order['my_status'], $order['orderId']));
				$joinRes = $this->db->affected_rows();

				// 中奖订单分配奖金
				if($order['status'] == $this->status['win'] && $order['failMoney'] == 0)
				{
					$dispatchRes = $this->dispatchAwards($order);
				}
				else
				{
					$dispatchRes = true;
				}

				// 出票成功订单汇总
				if($unitedStatus >= $this->status['draw'] && !in_array($unitedStatus, array('600', '601', '602')) && ($order['cstate'] & 32) == 0)
				{
					$orderData = array(
						'uid'           =>  $order['uid'],
			            'lid'           =>  $order['lid'],
			            'money'  		=>  $order['money'],
			            'succTimes'     =>  '1',
					);
					$this->recoredPlannerOrder($orderData);
					$recoredRes = $this->db->query("UPDATE cp_united_orders SET cstate = cstate ^ 32 WHERE orderId = ?", array($order['orderId']));
				}
				else
				{
					$recoredRes = true;
				}

				// 出票成功 截止时间更新 endTime = end_sale_time
				if($unitedStatus >= $this->status['draw'] && !in_array($unitedStatus, array('600', '601', '602')) && ($order['cstate'] & 1024) == 0 && $order['end_sale_time'] > '0000-00-00 00:00:00')
				{
					$updateTimeRes = $this->db->query("UPDATE cp_united_orders SET endTime = end_sale_time, cstate = (cstate | 1024) WHERE orderId = ?", array($order['orderId']));
				}
				else
				{
					$updateTimeRes = true;
				}

				if($unitedRes && $joinRes && $dispatchRes && $recoredRes)
				{
					$this->db->trans_complete();
				}
				else
				{
					$this->db->trans_rollback();
				}
			}
		}	
	}

	// 中奖订单分配奖金（单位：分）
	public function dispatchAwards($orderInfo)
	{
		$result = true;
		// 佣金
		$commission = 0;
		// 发起人首笔实际奖金
		$parentMoney = 0;
		// 发起人认购部分奖金
		$guarantee = 0;
		if($orderInfo['margin'] > 0)
		{
			$fields = array('orderId', 'subscribeId', 'margin', 'commission');
			$orderData['field'] = array();
            $orderData['val'] = array();

            // 盈利金额 = 税后总奖金 - 订单总金额
			$winMoney = $orderInfo['margin'] - $orderInfo['money'];
			if($winMoney > 0 && $orderInfo['commissionRate'] > 0)
			{
				// 发起人盈利佣金计算，取整至少一分钱
				$commission = $orderInfo['margin'] * ($orderInfo['commissionRate'] / 100);
				$commission = ($commission >= 1) ? floor($commission) : 0;

				if($orderInfo['margin'] - $commission > $orderInfo['money'])
				{
					$commission = $commission;
				}
				else
				{
					$commission = 0;
				}
			}

			// 认购人分配
			$sql = "SELECT orderId, trade_no, subscribeId, lid, issue, money, uid, puid, buyMoney, status, buyPlatform, orderType, margin, my_status, cstate, created FROM cp_united_join WHERE orderId = ? ORDER BY created ASC";
			$buyOrders = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

			// 实际分配奖金
			$actualMoney = $orderInfo['margin'] - $commission;
			$buyMargin = 0;
			$count = 0;
			if(!empty($buyOrders))
			{
				foreach ($buyOrders as $order) 
				{
					if($order['orderType'] != 1)
					{
						// 计算每笔奖金
						$margin = $order['buyMoney'] * ($actualMoney / $orderInfo['money']);
						$margin = ($margin >= 1) ? floor($margin) : 0;
						// 发起人认购部分奖金
						if($order['orderType'] == 3)
						{
							$guarantee = $margin;
						}
						$buyMargin += $margin;

						// 组装数据
						array_push($orderData['field'], "(?, ?, ?, ?)");
						array_push($orderData['val'], $orderInfo['orderId']);
						array_push($orderData['val'], $order['subscribeId']);
	                    array_push($orderData['val'], $margin);
	                    array_push($orderData['val'], 0);

	                    if(++$count >= 500)
	                    {
	                        $result = $this->updateAwards($fields, $orderData);
	                        $orderData['field'] = array();
	            			$orderData['val'] = array();
	                        $count = 0;
	                    }
					}
					else
					{
						// 发起人初始订单
						$orderInfo['subscribeId'] = $order['subscribeId'];
					}
				}
			}

			// 发起人实际奖金
			$parentMoney = $actualMoney - $buyMargin;
			
			// 组装数据
			array_push($orderData['field'], "(?, ?, ?, ?)");
			array_push($orderData['val'], $orderInfo['orderId']);
			array_push($orderData['val'], $orderInfo['subscribeId']);
            array_push($orderData['val'], $parentMoney);
            array_push($orderData['val'], $commission);

			if(!empty($orderData['field']))
            {
                $result = $this->updateAwards($fields, $orderData);
                $orderData['field'] = array();
            	$orderData['val'] = array();
                $count = 0;
            }
		}

		$parentMargin = $parentMoney + $guarantee;

		// 更新佣金
		$this->db->query("UPDATE cp_united_orders SET commission = ?, margin = ? WHERE orderId = ?", array($commission, $parentMargin, $orderInfo['orderId']));

		return $result;
	}	

	// 更新奖金明细
	public function updateAwards($fields, $orderData)
	{
		if(!empty($orderData['field']))
        {
            $upd = array('margin', 'commission');
            $sql = "insert cp_united_join(" . implode(', ', $fields) . ") values" . 
            implode(', ', $orderData['field']) . $this->onduplicate($fields, $upd);
            return $this->db->query($sql, $orderData['val']);
        }
        else
        {
        	return false;
        }
	}
	
	public function getUniteOrderByOrderId($orderId, $fields = null, $where = null) {
		if (empty($fields)) $fields = 'orderId, trade_no, uid, lid, unix_timestamp(endTime) as endTime, popularity, buyTotalMoney, playType, money, (buyMoney+guaranteeAmount)/money as qb, openEndtime, guaranteeAmount, orderMargin, commissionRate, commission, issue, openStatus, ForecastBonusv, `status`, my_status, created, orderBonus, isChase, shopId, isTop';
		$sql = "select {$fields}
				from cp_united_orders 
				where orderId = ? ";
		if ($where) $sql .= $where;
		return $this->db->query($sql, array('orderId' => $orderId))->getRow();
	}
	
	public function getPoints($uid, $lid)
	{
		if ($lid == PLW) $lid = PLS;
		if ($lid == RJ) $lid = SFC;
		return $this->slave->query("select united_points from cp_united_planner where lid = ? and uid = ?", array($lid, $uid))->getCol();
	}
	
	public function getJoin($orderId, $cons = array(), $fields, $multi = false, $order = null, $limit = null) {
		$sql = "FROM cp_united_join as o";
		
		if (isset($cons['userName'])) {
			$fields .= ", ui.uname";
			$sql .= " left join cp_user as ui on o.uid=ui.uid";
			unset($cons['userName']);
		}
		$sql .= ' WHERE o.orderId = ?';
		foreach ($cons as $k => $con) {
			if(is_array($con))
			{
				$con = implode(',', $con);
				$sql .= " and " . $k . " in ({$con})";
				unset($cons[$k]);
			}
			else
			{
				$sql .= " and ".$k." = ?";
			}
		}
		array_unshift($cons, $orderId);
		$sql = "SELECT {$fields} ".$sql;
		if ($multi) {
			if ($order) $sql .= " order by ".$order;
			if ($limit) $sql .= " limit ".$limit;
			return $this->slave->query($sql, $cons)->getAll();
		}
		
		return $this->slave->query($sql, $cons)->getRow();
	}
	
	public function getOrder( $cons, $fields = NULL, $multi = false, $order = null, $limit = null)
	{
		if (empty($fields))
			$fields = 'o.orderId, o.uid, o.lid, o.popularity, o.buyTotalMoney, o.united_points, o.money, o.guaranteeAmount, o.orderMargin, o.issue, o.openStatus, o.status, o.created, o.orderBonus, o.isChase, o.shopId, o.isTop';
		$sql = "FROM cp_united_orders as o where 1";
		if (isset($cons['continue'])) {
			$now = date("Y-m-d H:i:s", time());
			$sql.=" and o.money-o.buyTotalMoney>0 and o.endTime>'{$now}'";
			unset($cons['continue']);
		}
		if (isset($cons['time'])) {
			$sql.=" and o.created>='{$cons['time']}'";
			unset($cons['time']);
		}
                if(isset($cons['order'])){
                        $fields.=", o.buyTotalMoney / o.money as jd , o.guaranteeAmount/o.money as bd";
                        $orderConArr = array('00'=>" (jd + bd) DESC", '01'=>" (jd + bd) ASC", '10'=>" popularity DESC", '11'=>" popularity ASC", '30'=>" money DESC", '31'=>" money ASC", '40'=>" (o.money-o.buyTotalMoney) DESC", '41'=>" (o.money-o.buyTotalMoney) ASC");
                        $orderBy=$orderConArr[$cons['order']];
                        unset($cons['order']);
                }
		foreach ($cons as $k => $con) {
			if(is_array($con))
			{
				$con = implode(',', $con);
				$sql .= " and " . $k . " in ({$con})";
			}
			else
			{
				$sql .= " and ".$k." = '{$con}'";
			}
	    }
                $time = date("Y-m-d H:i:s",time()-10);
                $sql.= " and (o.follow_cstate=1 or o.pay_time<='{$time}')";            
		$sql = "SELECT {$fields} ".$sql;
		if ($multi) {
			if ($order) $sql .= " order by ".$order;
                        if(isset($orderBy)){
                            $sql .=",".$orderBy;
                        }
			if ($limit) $sql .= " limit ".$limit;
			return $this->slave->query($sql)->getAll();
		}
		return $this->slave->query($sql)->getRow();
	}
	
	public function getOrderInfo($orderId) {
		$date = date('Y-m-d H:i:s', strtotime(substr($orderId, 0, 14)));
		$tableSuffix = $this->tools->getTableSuffixByDate($date);
		if($tableSuffix) $tableSuffix = '_' . $tableSuffix;
		return $this->slave->query("select lid, codes, codecc, betTnum, multi, money, issue, playType, status from cp_orders{$tableSuffix} where orderId = ?", array($orderId))->getRow();
	}
	
	// 统计近期订单
	public function getStatisticOrder()
	{
		$sql = "SELECT uid, lid, isHot, isOrdering, money, allTimes, succTimes, winningTimes, monthBonus, bonus, created FROM cp_united_planner WHERE lid = 0 AND modified >= DATE_SUB(NOW(),INTERVAL 1 DAY) AND lastPayTime >= DATE_SUB(NOW(),INTERVAL 31 DAY) ORDER BY modified DESC";
		return $this->slave->query($sql)->getAll();
	}

	// 统计订单
	public function recoredPlannerOrder($orderInfo)
	{
		// 排列三/五，胜负彩任九二合一
		$orderInfo = $this->checkParams($orderInfo);

		$info = array(
            'uid'           =>  $orderInfo['uid'],
            'lid'           =>  $orderInfo['lid'],
            'money'         =>  $orderInfo['money'] ? $orderInfo['money'] : '0',
            'allTimes'      =>  $orderInfo['allTimes'] ? $orderInfo['allTimes'] : '0',
            'succTimes'     =>  $orderInfo['succTimes'] ? $orderInfo['succTimes'] : '0',
            'winningTimes'  =>  $orderInfo['winningTimes'] ? $orderInfo['winningTimes'] : '0',
            'monthBonus'    =>  $orderInfo['monthBonus'] ? $orderInfo['monthBonus'] : '0',
            'bonus'         =>  $orderInfo['bonus'] ? $orderInfo['bonus'] : '0',
            'united_points'	=>	$orderInfo['united_points'] ? $orderInfo['united_points'] : '0',
        );

		// 最近发单时间
        if($orderInfo['lastPayTime'])
        {
        	$info['lastPayTime'] = $orderInfo['lastPayTime'];
        }

		for($i = 0; $i < 2; $i++) 
        {
            if($i == 1)
            {
                $info['lid'] = '0';
            }
            $this->savePlannerOrder($info);
        }
        return true;
	}

	// 保存统计结果
	public function savePlannerOrder($info)
	{
		$upd = array('money', 'allTimes', 'succTimes', 'winningTimes', 'monthBonus', 'bonus', 'united_points', 'lastPayTime');
		$apd = array('money', 'allTimes', 'succTimes', 'winningTimes', 'monthBonus', 'bonus', 'united_points');
		$fields = array_keys($info);
		$sql = "insert cp_united_planner(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd, $apd);
		return $this->db->query($sql, $info);
	}

	// 更新进行中订单
	public function updateIsOrdering($orderInfo)
	{
		// 排列三/五，胜负彩任九二合一
		$orderInfo = $this->checkParams($orderInfo);

		$lidArr = array(0);
		array_push($lidArr, $orderInfo['lid']);
		$this->db->query("UPDATE cp_united_planner SET isOrdering = 1 WHERE uid = ? AND lid IN (" . implode(',', $lidArr) . ")", array($orderInfo['uid']));
		return true;
	}

	// 红人统计 - 近一个月中奖总金额
	public function getStatisticDetail($uid)
	{
        // 近一个月中奖总金额
		$bonusSql = "SELECT lid, SUM(orderBonus) AS orderBonus, SUM(orderMargin) AS orderMargin, COUNT(*) AS winTimes FROM cp_united_orders WHERE uid = ? AND created >= date_sub(now(), interval 30 day) AND status = '{$this->status['win']}' AND sendprize_time > '0000-00-00 00:00:00' GROUP BY lid";
		$bonusInfo = $this->db->query($bonusSql, array($uid))->getAll();

		// 清空
		$this->db->query("UPDATE cp_united_planner SET monthBonus = 0, monthWinTimes = 0 WHERE uid = ?", array($uid));

		if(!empty($bonusInfo))
		{
			$bonusSum = 0;
			$winTimes = 0;
			foreach ($bonusInfo as $key => $items) 
			{
				// 排列三/五，胜负彩任九二合一
				$items = $this->checkParams($items);

				$bonusSum += $items['orderBonus'];
				$items['winTimes'] = $items['winTimes'] ? $items['winTimes'] : 0;
				$winTimes += $items['winTimes'];
				$this->db->query("UPDATE cp_united_planner SET monthBonus = ?, monthWinTimes = ? WHERE uid = ? AND lid = ?", array($items['orderBonus'], $items['winTimes'], $uid, $items['lid']));
			}
			$this->db->query("UPDATE cp_united_planner SET monthBonus = ?, monthWinTimes = ? WHERE uid = ? AND lid = 0", array($bonusSum, $winTimes, $uid));
		}
	}

	// 统计近期订单
	public function getIsOrdering()
	{
		$sql = "SELECT uid, lid, isHot, isOrdering, money, allTimes, succTimes, winningTimes, monthBonus, bonus, created FROM cp_united_planner WHERE lid = 0 AND isOrdering = 1 ORDER BY modified DESC";
		return $this->db->query($sql)->getAll();
	}

	// 红人统计 - 统计正在进行的订单
	public function checkIsOrdering($uid)
	{
		// 统计正在进行的订单
		$countSql = "SELECT lid, IF(count(*) > 0, 1, 0) AS isOrdering FROM cp_united_orders WHERE uid = ? AND status >= '{$this->status['pay']}' AND status NOT IN ('{$this->status['revoke_by_user']}', '{$this->status['revoke_by_system']}') AND endTime >= now() AND buyTotalMoney < money GROUP BY lid";
		$count = $this->db->query($countSql, array($uid))->getAll();

		// 更新进行中
		if(!empty($count))
		{
			$lidArr = array();
			foreach ($count as $key => $items) 
			{
				// 排列三/五，胜负彩任九二合一
				$items = $this->checkParams($items);
				
				array_push($lidArr, $items['lid']);
			}
			array_push($lidArr, '0');

			$this->db->query("UPDATE cp_united_planner SET isOrdering = 0 WHERE uid = ? AND lid NOT IN (" . implode(',', $lidArr) . ")", array($uid));
		}
		else
		{
			$this->db->query("UPDATE cp_united_planner SET isOrdering = 0 WHERE uid = ?", array($uid));
		}	
	}

	// 方案撤单
	public function cancelOrder($orderId, $cancelType = 0, $trans = true, $uid = null)
	{
		$type = array(
			0 => 50,	// 用户撤单进度
			1 => 95		// 后台撤单进度
		);

		$result = array(
			'status' => false,
			'msg' => '该合买方案撤单条件不满足',
		);

		if(empty($type[$cancelType]))
		{
			return $result;
		}

		// 事务开始
    	if($trans) $this->db->trans_start();
    	// 行锁
    	$unitedInfo = $this->getUnitedOrderDetail($orderId, $uid);

    	// 尚未出票且认购保底总额小于95%
		$sMoney = intval($unitedInfo['buyTotalMoney']) + intval($unitedInfo['guaranteeAmount']);
		$tMoney = intval($unitedInfo['money']);
		$percent = $this->getUnitedPercent($sMoney, $tMoney);

		if(empty($unitedInfo))
		{
			if($trans)
			{
				$this->db->trans_rollback();
			}
			return $result;
		}

		if($unitedInfo['money'] == $unitedInfo['buyTotalMoney'])
		{
			if($trans)
			{
				$this->db->trans_rollback();
			}
			$result = array(
				'status' => false,
				'msg' => '该合买方案已满员',
			);
			return $result;
		}

		if($unitedInfo['endTime'] <= date('Y-m-d H:i:s'))
		{
			if($trans)
			{
				$this->db->trans_rollback();
			}
			$result = array(
				'status' => false,
				'msg' => '该合买方案已截止',
			);
			return $result;
		}

		if(($unitedInfo['cstate'] & 1) != 0)
		{
			if($trans)
			{
				$this->db->trans_rollback();
			}
			$result = array(
				'status' => false,
				'msg' => '该合买方案已撤单',
			);
			return $result;
		}

		if($unitedInfo['status'] == $this->status['pay'] && $percent < $type[$cancelType])
		{
			$this->load->model('united_wallet_model');
			// 查询认购记录（包含发起人认购）
			$buyOrders = $this->getUnitedBuyOrders($orderId);

			if(!empty($buyOrders))
			{
				$userArr = array();
				$orderCounts = count($buyOrders);
				$count = 0;
				foreach ($buyOrders as $order) 
				{
					array_push($userArr, $order['uid']);
					// 公共方法，单笔认购根据trade_no流水退款，方案撤单
					$refundsRes = $this->united_wallet_model->refunds($order, 1, false);
					if($refundsRes)
					{
						$count ++;
					}
					else
					{
						// 退款失败日志
						log_message('LOG', "撤单退款失败: " . json_encode($order), 'united_order');
					}
				}

				// 更新cp_united_orders未满员撤单
				$this->db->query("UPDATE cp_united_orders SET status = {$this->status['revoke_by_user']}, cstate = cstate ^ 1 WHERE orderId = ? AND endTime > now() AND (cstate & 1 = 0)", array($orderId));
				$unitedRes = $this->db->affected_rows();

				$this->db->query("UPDATE cp_united_join SET status = {$this->status['revoke_by_user']} WHERE orderId = ?", array($orderId));
				$unitedStatusRes = $this->db->affected_rows();

				// 更新cp_orders订单过期
				$this->db->query("UPDATE cp_orders SET status = {$this->status['out_of_date']} WHERE orderId = ? AND endTime > now()", array($orderId));
				$orderRes = $this->db->affected_rows();

				if($orderCounts == $count && $unitedRes && $unitedStatusRes && $orderRes)
				{
					if($trans)
					{
						$this->db->trans_complete();
					}	
					$result = array(
						'status' => true,
						'msg' => '您好，撤单操作成功，将退款至您的账户，请注意查收',
					);
				}
				else
				{
					if($trans)
					{
						$this->db->trans_rollback();
					}		
					$userArr = array_unique($userArr);
					// 回滚钱包
					if(!empty($userArr))
					{
						foreach ($userArr as $uid) 
						{
							// 刷新钱包
            				$this->united_wallet_model->freshWallet($uid);
						}
					}
				}
			}
			else
			{
				if($trans)
				{
					$this->db->trans_rollback();
				}
			}
		}
		else
		{
			if($trans)
			{
				$this->db->trans_rollback();
			}
			$result = array(
				'status' => false,
				'msg' => '该合买方案撤单条件不满足',
			);
		}
		return $result;
	}

	// 获取发送短信的订单
	public function getMessageOrder()
	{
		$sql = "SELECT orderId, trade_no, uid, lid, issue, money, buyMoney, buyTotalMoney, commissionRate, status, guaranteeAmount, webguranteeAmount, openStatus, buyPlatform, commission, orderBonus, orderMargin, margin, popularity, isTop, openEndtime, ForecastBonusv, endTime, refund_time, sendprize_time, my_status, cstate, bet_flag, created FROM cp_united_orders WHERE modified > date_sub(now(), interval 30 minute) AND ((status = '{$this->status[concel]}' AND refund_time > '0000-00-00 00:00:00') OR (status = '{$this->status[win]}' AND sendprize_time > '0000-00-00 00:00:00')) AND (cstate & 64 = 0) ORDER BY orderId ASC LIMIT 50";
		return $this->db->query($sql)->getAll();
	}

	// 处理短信发送
	public function handleMessage($orderInfo)
	{
		$this->load->model('user_model');
		$position = $this->config->item('POSITION');

		$msgType = array(
			600		=>	'united_tick_fail',
			2000	=>	'united_win_prize'
		);

		if($orderInfo['status'] == $this->status['win'])
		{
			// 中奖
			$sql = "SELECT j.uid, SUM(j.margin) AS margin, SUM(j.commission) AS commission, SUM(j.buyMoney) AS buyMoney, MIN(j.created) AS created, i.msg_send FROM cp_united_join AS j LEFT JOIN cp_user_info AS i ON j.uid = i.uid WHERE j.orderId = ? AND j.uid != '0' GROUP BY j.uid";
			$buyInfo = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

			if(!empty($buyInfo))
			{
				foreach ($buyInfo as $info) 
				{	
					$actMoney = $info['margin'] + $info['commission'];

					if($actMoney > 0 && ($info['msg_send'] & 4) == 0)
					{
						$msgData = array(
							'#MONEY#'	=>	$actMoney,
							'#LID#' 	=> 	BetCnName::getCnName($orderInfo['lid']),
							'time' 		=> 	$info['created']
						);

						$this->user_model->sendSms($info['uid'], $msgData, $msgType[$orderInfo['status']], null, '127.0.0.1', $position[$msgType[$orderInfo['status']]]);
					}
				}	
			}
		}
		else
		{
			// 出票失败订单判断实际退款金额
			$sql = "SELECT uid, SUM(margin) AS margin, SUM(commission) AS commission, SUM(buyMoney) AS buyMoney, MIN(created) AS created FROM cp_united_join WHERE orderId = ? AND orderType NOT IN (3, 4) GROUP BY uid";
			$buyInfo = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

			if(!empty($buyInfo))
			{
				foreach ($buyInfo as $info) 
				{
					// 发起人实际退款 正常认购 + 保底 - 退保 
					if($info['uid'] == $orderInfo['uid'])
					{
						$this->load->model('united_wallet_model');
						$refundInfo = $this->united_wallet_model->getRefundsWalletLog($orderInfo['orderId']);
						$refundMoney = $refundInfo['money'] ? $refundInfo['money'] : 0;
						$actMoney = $info['buyMoney'] + $orderInfo['guaranteeAmount'] - $refundMoney;
					}
					else
					{
						$actMoney = $info['buyMoney'];
					}

					$msgData = array(
						'#MONEY#'	=>	$actMoney,
						'#LID#' 	=> 	BetCnName::getCnName($orderInfo['lid']),
						'time' 		=> 	$info['created']
					);

					$this->user_model->sendSms($info['uid'], $msgData, $msgType[$orderInfo['status']], null, '127.0.0.1', $position[$msgType[$orderInfo['status']]]);
				}	
			}
		}

		$this->db->query("UPDATE cp_united_orders SET cstate = cstate ^ 64 WHERE orderId = ? AND (cstate & 64 = 0)", array($orderInfo['orderId']));
	}

	// 获取发送邮件的订单
	public function getEmailOrder()
	{
		$sql = "SELECT u.orderId, u.uid, u.lid, u.issue, u.money, u.openStatus, u.buyTotalMoney, u.status, u.created, o.betTnum, o.multi, o.playType, o.codecc, o.codes, i.uname FROM cp_united_orders AS u INNER JOIN cp_orders AS o ON u.orderId = o.orderId INNER JOIN cp_user AS i ON u.uid = i.uid WHERE u.modified > date_sub(now(), interval 30 minute) AND u.status IN ('" . $this->status['draw'] . "', '" . $this->status['revoke_by_user'] . "', '" . $this->status['revoke_by_system'] . "') AND (u.cstate & 256 = 0) ORDER BY u.modified DESC LIMIT 100";
		return $this->db->query($sql)->getAll();
	}

	// 处理邮件发送
	public function handleEmail($orderInfo)
	{
		$emailType = array(
			500	=>	'出票成功',
			610	=>	'发起人撤单',
			620	=> 	'未满员撤单'
		);
		
		$url_prefix = $this->config->item('url_prefix');
		$this->url_prefix = isset($url_prefix[$this->config->item('domain')]) ? $url_prefix[$this->config->item('domain')] : 'http';

		$sql = "SELECT o.orderId, o.trade_no, o.subscribeId, o.lid, o.uid, o.subOrderType, u.email, i.msg_send, u.uname, o.buyMoney, o.cstate, o.created as pay_time
				FROM cp_united_join AS o 
				INNER JOIN cp_user AS u ON o.uid = u.uid 
				INNER JOIN cp_user_info AS i ON o.uid = i.uid 
				WHERE o.orderId = ? AND (o.cstate & 128 = 0) ORDER BY o.created ASC";
		$buyInfo = $this->db->query($sql, array($orderInfo['orderId']))->getAll();

		if(!empty($buyInfo))
		{
			$this->load->library('emailtemplate/UnitedTemplate');
			// 按单笔认购记录
			foreach ($buyInfo as $order)
			{
				if(!empty($emailType[$orderInfo['status']]) && !empty($order['uid']))
				{
					if(!empty($order['email']) && ($order['msg_send'] & 2) == 2)
					{
						$url_prefix = $this->config->item('url_prefix');
						$url_prefix = isset($url_prefix[$this->config->item('domain')]) ? isset($url_prefix[$this->config->item('domain')]) : 'http:';
						// 组装参数
						$emailData = array(
							'userName'	=>	$order['uname'],
							'orderName' =>  $orderInfo['uname'],
							'status'	=>	$orderInfo['status'],
							'statusMsg'	=>	$emailType[$orderInfo['status']],
							'lid'		=>	$orderInfo['lid'],
							'orderType' =>	'4',
							'betTnum'	=>	$orderInfo['betTnum'],
							'multi'		=>	$orderInfo['multi'],
							'issue'     =>	$orderInfo['issue'],
							'codes'		=>	$orderInfo['codes'],
							'codecc'	=>	$orderInfo['codecc'],
							'playType'	=>	$orderInfo['playType'],
							'orderId'	=>	$orderInfo['orderId'],
							'subscribeId'	=>	$order['subscribeId'],
							'created'   =>	$orderInfo['created'],
							'openStatus' => ($orderInfo['uid'] != $order['uid']) ? $orderInfo['openStatus'] : 0,
							'money'		=>	$orderInfo['money'],
							'buyMoney'	=>	$order['buyMoney'],
							'buyTime'   =>	$order['created'],
							'pay_time'  =>  $order['pay_time'],
							'subOrderType'	=>	$order['subOrderType'],
							'unitedUrl' =>	$this->url_prefix.'://'.$this->config->item('domain').'/hemai/detail/hm' . $orderInfo['orderId']
						);

						$data = array(
	    					'to'	  =>	$order['email'],
	    					'subject' =>	"合买".$emailType[$orderInfo['status']] . '通知',
	    					'message' =>	$this->unitedtemplate->index($emailData),
	                        'bcc'     =>	'166cai@km.com'
	    				);

	    				// 入库记录
	    				$sendType = ($orderInfo['status'] == 500) ? 2 : 3;
	    				$emailsql = "INSERT INTO cp_order_email_logs(uid,email, orderId, ctype, title,content,created) VALUES (?, ?, ?, {$sendType}, ?, ?, NOW()) ON DUPLICATE KEY UPDATE email = VALUES(email), content = VALUES(content)";
    					$this->db->query($emailsql, array($order['uid'], $order['email'], $order['subscribeId'], $data['subject'], $data['message']));
	    				//修改成阿里云
	    				$result = $this->tools->sendMail($data);
	    				//$result = $this->tools->sendMail($data,array(),1);
	    				// 更新单笔状态
						$this->db->query("UPDATE cp_united_join SET cstate = cstate ^ 128 WHERE subscribeId = ? AND (cstate & 128 = 0)", array($order['subscribeId']));
					}
				}
			}
		}

		if(in_array($orderInfo['status'], array(610, 620)) || ($orderInfo['money'] == $orderInfo['buyTotalMoney']))
		{
			$this->db->query("UPDATE cp_united_orders SET cstate = cstate ^ 256 WHERE orderId = ? AND (cstate & 256 = 0)", array($orderInfo['orderId']));
		}
	}
	
	public function getHotPlanner($lid = 0) {
		$res = $this->slave->query("SELECT uid, isOrdering, monthBonus, bonus/money as hbl, cstate & 1 as cstate, united_points FROM cp_united_planner WHERE isHot = 1 and lid = ? order by hbl desc, allTimes desc, money desc", array($lid))->getAll();
		$data = array();
		if (!empty($res)) {
			foreach ($res as $val) {
				if (!is_array($data[$val['cstate']])) $data[$val['cstate']] = array();
				array_push($data[$val['cstate']], $val);
			}
		}
		return $data;
	}
	
	//合买大厅
	public function getHmdtOrders($search, $limit, $order = '00', $uid = null) {
		$replaceData = array();
		$arr = array('state', 'money', 'commission', 'lid');
		foreach ($arr as $v) {
			if (!isset($search[$v])) $search[$v] = 0;
		}
		
		$stateConArr = array(0 => " AND uo.buyTotalMoney < uo.money and endTime > NOW() AND uo.status not in (0, 20, 600, 610, 620)", 1 => " AND uo.money = uo.buyTotalMoney AND STATUS NOT IN (0, 20, 600, 610, 620)", 2 => " AND uo.status in (600, 610, 620)");
		$orderConArr = array('00'=>" (jd + bd) DESC", '01'=>" (jd + bd) ASC", '10'=>" popularity DESC", '11'=>" popularity ASC", '20'=>" up.united_points DESC", '21'=>" up.united_points ASC", '30'=>" money DESC", '31'=>" money ASC");
		$moneyConArr = array(1=>" AND uo.money <= 10000", 2=>" AND uo.money > 10000 AND uo.money <= 50000", 3=>" AND uo.money > 50000 AND uo.money <= 100000", 4=>" AND uo.money > 100000");
		
		$fields = 'uo.orderId, uo.lid, uo.uid, uo.popularity, uo.money, uo.buyTotalMoney, uo.buyTotalMoney / uo.money as jd , truncate(uo.guaranteeAmount/uo.money, 2) as bd, uo.issue, up.united_points';
		if ($uid) $fields .= " ,(SELECT id FROM cp_united_join WHERE orderId=uo.orderId AND uid='".intval($uid)."' LIMIT 1) as ujoin";
		$sql = " FROM cp_united_orders as uo JOIN cp_united_planner AS up ON uo.uid = up.uid AND (up.lid = uo.lid OR (up.lid=11 AND uo.lid=19) OR (up.lid=33 AND uo.lid=35))";
		$where = " WHERE uo.created > date_sub(now(), interval 10 day) ".$stateConArr[$search['state']];
		
		if (!empty($search['uname']))
		{
			$sql .= " join cp_user as u on uo.uid=u.uid";
			$where .= " and u.uname like ?";
			$replaceData[] = '%' . $search['uname'] .'%';
		} 
		if ($search['money']) $where .= $moneyConArr[$search['money']];
		if ($search['commission']) $where .= " AND uo.commissionRate <= ".(intval($search['commission'])-1);
		if ($search['lid']) {
			if ($search['lid'] == PLS) {
				$where .= " AND uo.lid in (33, 35)";
			}elseif ($search['lid'] == SFC) {
				$where .= " AND uo.lid in (11, 19)";
			}else {
				$where .= " AND uo.lid = ". intval($search['lid']);
			}
			$fields .= ', uo.isTop & 1 as isTop';
		}else {
			$fields .= ', uo.isTop & 2 as isTop';
		}
		if ($search['issue']) $where .= " AND uo.issue = '". intval($search['issue'])."'";
		
		if (empty($search['uname'])) {
			$sqlcount = "select count(*) FROM cp_united_orders as uo".$where;
		}else {
			$sqlcount = "select count(*) ".$sql.$where;
		}
                $time = date("Y-m-d H:i:s",time()-10);
                $where.= " and (uo.follow_cstate=1 or uo.pay_time<='{$time}')";
		return array('num' => $this->slave->query($sqlcount, $replaceData)->getCol(), 'data' => $this->slave->query("select ".$fields.$sql.$where." ORDER BY ".($search['state'] == 0 ? 'isTop desc, ' : '').$orderConArr[$order].", uo.created DESC limit ".$limit, $replaceData)->getAll());
	}
	
	public function getSzcIssues($lid, $num = 3) {
		$this->load->driver('cache', array('adapter' => 'redis'));
		$REDIS = $this->config->item('REDIS');
		$cacheArr = array(SSQ => 'SSQ_ISSUE', DLT => 'DLT_ISSUE', FCSD => 'FC3D_ISSUE', QLC => 'QLC_ISSUE', QXC => 'QXC_ISSUE', PLS => 'PLS_ISSUE', SFC => 'SFC_ISSUE');
		$current = json_decode($this->cache->get($REDIS[$cacheArr[$lid]]), true);
		$issues = json_decode ( $this->cache->hGet ( $REDIS ['NEWLY_ISSUES'], $lid."_".$num ), true );
		if (empty($issues) || max($issues) != $current['cIssue']['seExpect']) {
			$tableArr = array(SSQ => 'cp_ssq_paiqi', DLT => 'cp_dlt_paiqi', FCSD => 'cp_fc3d_paiqi', QLC => 'cp_qlc_paiqi', QXC => 'cp_qxc_paiqi', PLS => 'cp_pl3_paiqi', SFC => 'cp_tczq_paiqi');
			$fields = $field = 'issue';
			$issue = $current['cIssue']['seExpect'];
			if (in_array($lid, array(DLT, QXC, PLS))) {
				$fields = "CONCAT('20',issue)";
				$issue = substr($issue, 2);
			}else if ($lid == SFC) {
				$fields = "distinct CONCAT('20',mid)";
				$issue = substr($issue, 2);
				$field = 'mid';
			}
			$issues = $this->slaveCfg->query("select {$fields} from {$tableArr[$lid]} where {$field} <= {$issue} order by {$field} desc limit 3")->getCol();
			$this->cache->hSet($this->REDIS['NEWLY_ISSUES'], $lid."_".$num, json_encode($issues));
		}
		return array('issues' => $issues, 'seFsendtime' => $current['cIssue']['seFsendtime']);
	}

	// 出票订单随机分配投注站
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

        $stationInfo = $this->db->query($sql, array($search['partner_name'], $search['lottery_type'], $search['status'], $search['delete_flag'], $search['lid']))->getAll();
    
        $shopId = '0';
        if(!empty($stationInfo))
        {
            $stationNum = count($stationInfo) - 1;
            $stationIndex = rand(0, $stationNum);
            $shopId = $stationInfo[$stationIndex]['id'];
        }
        return $shopId;
    }
    
    public function getBetstationByShopid($shopId) {
    	return $this->db->query('select cname from cp_partner_shop where id = ?', array($shopId))->getRow();
    }

    // 获取彩种所属类型
    public function getLotteryType($lid)
    {
        // 福彩：双色球，福彩3D，七乐彩
        if(in_array($lid, array('51', '52', '23528', '53','56')))
        {
            $lotteryType = 1;
        }
        else
        {
            $lotteryType = 0;
        }
        return $lotteryType;
    }

    public function checkParams($orderInfo)
    {
    	if(in_array($orderInfo['lid'], array('33', '35')))
		{
			$orderInfo['lid'] = '33';
		}
		if(in_array($orderInfo['lid'], array('11', '19')))
		{
			$orderInfo['lid'] = '11';
		}
		return $orderInfo;
    }

    // 检查取消置顶
    public function checkIsTop()
    {
    	$this->db->query("UPDATE cp_united_orders SET isTop = 0 WHERE modified >= date_sub(now(), interval 1 day) AND ((status IN (600, 610, 620)) OR (endTime <= NOW()) OR (buyTotalMoney = money)) AND isTop <> 0");
    }
    
    /**
     * 分彩种置顶合买订单
     */
    public function getIstop()
    {
    	$lidArr = array(SSQ, DLT, JCZQ, JCLQ, array(SFC, RJ), FCSD, QLC, QXC, array(PLS, PLW));
        $this->db->trans_start();
    	$this->db->query("UPDATE cp_united_orders SET isTop = 0 where 1");
    	foreach ($lidArr as $k => $lid)
    	{
    		if (is_array($lid))
                {
    			$lidwhere = " and lid in (".implode(',', $lid).")";
    		}
                else
                {
    			$lidwhere = " and lid=".$lid;
    		}
                $sql = "select uid,orderId from cp_united_orders WHERE 1 {$lidwhere} and buyTotalMoney < money
				and `status` NOT IN (0, 20, 600, 610, 620) and created > date_sub(now(), INTERVAL 10 DAY) AND endTime > NOW()
                                and ((buyTotalMoney+guaranteeAmount)/money)>=0.95
    			ORDER BY money desc";
    		$orders = $this->db->query($sql)->getAll();
                $orderId=$this->needTopOrder($orders, 8);
                if($orderId)
                {
                    $sql = "UPDATE cp_united_orders SET isTop=isTop | 1 where orderId in ({$orderId})";
                    $re = $this->db->query($sql);
                }
    	}
    	$orders = $this->db->query("select uid,orderId from cp_united_orders
    			where 1 and buyTotalMoney < money
				and `status` NOT IN (0, 20, 600, 610, 620) and created > date_sub(now(), INTERVAL 10 DAY) AND endTime > NOW() 
                                and ((buyTotalMoney+guaranteeAmount)/money)>=0.95
    			ORDER BY money desc")->getAll();
        $orderId=$this->needTopOrder($orders, 15);
        if($orderId)
        {
            $sql = "UPDATE cp_united_orders SET isTop=isTop | 2 where orderId in ({$orderId})";
            $this->db->query($sql);
        }
        $this->db->trans_complete();
    }
    
    public function setHot()
    {
    	$this->db->trans_start();
    	$lidArr = array(0, SSQ, DLT, JCZQ, JCLQ, array(SFC, RJ), FCSD, QLC, QXC, array(PLS, PLW));
    	$this->db->query("update cp_united_planner set isHot = 0 where cstate & 1 <> 1");
    	foreach ($lidArr as $k => $lid)
    	{
    		$lidwhere = '';
    		if (is_array($lid)) {
    			$lidwhere = " and up.lid in (".implode(',', $lid).")";
    		}else {
    			$lidwhere = " and up.lid=".$lid;
    		}
    		$re=$this->db->query("UPDATE cp_united_planner as tb INNER JOIN
				(SELECT DISTINCT uo.uid FROM cp_united_orders as uo
    				INNER JOIN cp_united_planner as up on uo.uid=up.uid AND up.lid=".(($lid == 0) ? '0' : (is_array($lid) ? $lid[0] : $lid))."
	    			WHERE uo.created > DATE_SUB(NOW(),INTERVAL 7 DAY) and up.cstate & 1 <> 1 ORDER BY up.united_points DESC LIMIT 14) tmp
	    		ON tb.uid=tmp.uid and tb.lid = ".(($lid == 0) ? '0' : (is_array($lid) ? $lid[0] : $lid))."
	    		SET tb.isHot = 1");
    		if (!$this->db->affected_rows()) $this->db->trans_rollback();
    	}
    	$REDIS = $this->config->item('REDIS');
    	$uids = $this->db->query("select distinct uid from cp_united_planner where isHot = 1")->getCol();
    	foreach ($uids as $uid) {
    		$this->load->model('user_model');
    		$this->user_model->freshHotInfo($uid);
    	}
        $this->db->trans_complete();
    }
    /**
     * [getFollowPushOrder 获取已支付未推送的订单]
     * @author LiKangJian 2017-05-26
     * @return [type] [description]
     */
    public function getFollowPushOrder()
    {
    	$sql = "SELECT o.uid,o.lid,o.orderId,u.uname 
    	FROM cp_united_orders as o
    	LEFT JOIN cp_user as u on u.uid = o.uid
    	WHERE o.modified > date_sub(now(), interval 30 minute) AND o.status >= 40 AND (o.cstate & 512 = 0) ORDER BY o.orderId ASC LIMIT 50";
    	return $this->db->query($sql)->getAll();
    }
    /**
     * [handleFollowPush 处理推送]
     * @author LiKangJian 2017-05-26
     * @param  [type] $orderInfo [description]
     * @return [type]            [description]
     */
    public function handleFollowPush($orderInfo)
    {
		$this->load->library('mipush');
		//获取UId
		$info = $this->db->query("SELECT f.uid FROM cp_united_follow AS f LEFT JOIN cp_user_info AS u ON f.uid = u.uid WHERE f.puid = ? AND f.follow_status = ? AND f.last_push_time < DATE_SUB(NOW(),INTERVAL 10 minute) AND (u.app_push & 8) = 0", array($orderInfo['uid'], 1))->getAll();
		if(!empty($info))
		{
			foreach ($info as $v) 
			{
				$pushData = array(
					 'type'      =>  'united_follow',
					 'uid'       =>  $v['uid'],
					 'lid'       =>  $orderInfo['lid'],
					 'uname'     =>  $orderInfo['uname'],   // 用户名
					 'orderId'   =>  $orderInfo['orderId'], // 合买订单号不含hm
				);
				$this->mipush->index('user', $pushData);
				// 更新推送时间
				$this->db->query("UPDATE cp_united_follow SET last_push_time = now() WHERE puid = ? AND uid = ?", array($orderInfo['uid'], $pushData['uid']));
			}
		}
		
		$this->db->query("UPDATE cp_united_orders SET cstate = cstate ^ 512 WHERE orderId = ? AND (cstate & 512 = 0)", array($orderInfo['orderId']));
    }
    
    /**
     * 获取可以置顶订单
     * @param type $orders
     * @param type $num
     * @return array
     */
    private function needTopOrder($orders,$num)
    {
        $orderIds = array();
        $count = array();
        foreach ($orders as $order) {
            if (count($count[$order['uid']]) < 3) {
                if (count($orderIds) >= $num) {
                    break;
                } else {
                    $orderIds[] = "'{$order['orderId']}'";
                    $count[$order['uid']][] = $order['orderId'];
                }
            }
        }
        return implode(',', $orderIds);
    }

    // 初始化近一月未投单的中奖总额和中奖次数
    public function clearStatisticOrder()
    {
    	$this->db->query("UPDATE cp_united_planner SET monthBonus = 0, monthWinTimes = 0 WHERE lastPayTime < DATE_SUB(NOW(),INTERVAL 31 DAY)");
    }

    // 保存合买详情信息
	public function saveUnitedDetail($info)
	{
		$fields = array_keys($info);
		$sql = "insert cp_united_detail(" . implode(',', $fields) . ", created)
		values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
		return $this->db->query($sql, $info);
	}

    // 合买宣言
    public function getUncheckedIntro()
    {
    	$sql = "SELECT orderId, introduction, check_status FROM cp_united_detail WHERE created >= date_sub(now(), interval 1 HOUR) AND check_status = 0 AND delete_flag = 0 ORDER BY created ASC LIMIT 500";
    	return $this->db->query($sql)->getAll();
    }

    // 合买宣言审核
    public function updateCheckIntro($orderId, $checkRes, $sensitives = '')
    {
    	$sql = "UPDATE cp_united_detail SET check_status = ?, sensitives = ? WHERE orderId = ?";
        $this->db->query($sql, array($checkRes, $sensitives, $orderId));
    }
}
