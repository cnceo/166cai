<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 追号 - 订单 - 模型层
 * @date:2015-12-01
 */
class Chase_Order_Model extends MY_Model
{
	// 追号订单状态
	public $status = array(
		'create' => 0, //未付款
		'out_of_date_paying' => 20, //订单过期未付款
		'out_of_date_payed' => 21, //订单过期已退款
		'is_chase' => 240, //已付款追号中
		'stop_by_award' => 500, //中奖停止追号
		'chase_over' => 700, //追号完成
	);

	public function __construct() 
	{
		parent::__construct();
	}

	// 获取订单状态标示
	public function getStatus()
	{
		return $this->status;
	}

	// 创建追号订单
	public function createChaseOrder($params)
	{
		$checkMap = array(
			'21406' => 'SyxwCheck',
			'21407' => 'SyxwCheck',
			'53' => 'KsCheck',
			'51' => 'SsqCheck',
			'23529' => 'DltCheck',
			'23528' => 'QlcCheck',
			'35' => 'PlwCheck',
			'10022' => 'PlcommCheck',
			'33' => 'PlsAnd3dCheck',
			'52' => 'PlsAnd3dCheck',
			'21408' => 'SyxwCheck',
			'54' => 'KlpkCheck',
			'55' => 'CqsscCheck',
			'56' => 'KsCheck',
		    '57' => 'KsCheck',
		    '21421' => 'SyxwCheck',
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
			// 彩种销售状态判断 为了获取提前截止时间的提前量
			$this->load->driver('cache', array('adapter' => 'redis'));
			$REDIS = $this->config->item('REDIS');
			$lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
			$lotteryConfig = json_decode($lotteryConfig, true);
			// 追号组装数据
			$chaseInfo = array(
				'uid' => $params['uid'],
				'userName' => $params['userName'],
				'chaseId' => $this->tools->getIncNum('UNIQUE_KEY'),
				'buyPlatform' => $params['buyPlatform'],
				'codes' => $params['codes'],
				'lid' => $params['lid'],
				'money' => ParseUnit($params['money']),
				'playType' => $this->getLotteryPlayType($params),	// 针对十一选五、排列三、福彩3D 混合投注playType处理
				'betTnum' => $params['betTnum'],
				'isChase' => $params['isChase'],
				'chaseType' => empty($params['chaseType']) ? 0 : $params['chaseType'],
				'bonus' => 0,
				'chaseIssue' => 0,
				'totalIssue' => $params['totalIssue'],
				'status' => $this->status['create'],
				'setStatus' => (in_array($params['setStatus'], array(0, 1))) ? intval($params['setStatus']) : 0,
				'setMoney' => ParseUnit($params['setMoney']),
				'endTime' => date('Y-m-d H:i:s', strtotime("+{$lotteryConfig[$params['lid']]['ahead']} minute", strtotime($params['endTime']))),
				'channel' => $params['channel'],
				'app_version' => $params['app_version'],
				'singleFlag'    =>  isset($params['singleFlag']) ? $params['singleFlag'] : 0
			);
			
			// 事务开始
			$this->db->trans_start();
			// cp_chase_manage表初始化
			$chaseUpfields = array('bonus', 'chaseIssue', 'status');
			$chaseFields = array_keys($chaseInfo);
			$sql1 = "insert cp_chase_manage(" . implode(',', $chaseFields) . ", created)
			values(". implode(',', array_map(array($this, 'maps'), $chaseFields)) .", now())" . $this->onduplicate($chaseFields, $chaseUpfields);
			$manageStatus = $this->db->query($sql1, $chaseInfo);
			// cp_chase_orders表初始化
			$params['chaseId'] = $chaseInfo['chaseId'];
			$chaseDetail = json_decode($params['chaseDetail'], TRUE);
			$orderFields = array('sequence', 'chaseId', 'lid', 'issue', 'money', 'multi', 'status', 'endTime', 'award_time', 'created');
			$bdata['s_data'] = array();
			$bdata['d_data'] = array();
			
			foreach ($chaseDetail as $key => $detail)
			{
				$endTime = date('Y-m-d H:i:s', strtotime("+{$lotteryConfig[$params['lid']]['ahead']} minute", strtotime($detail['endTime'])));
				$detail['sequence'] = $key + 1;
				array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
				array_push($bdata['d_data'], $detail['sequence']);
				array_push($bdata['d_data'], $chaseInfo['chaseId']);
				array_push($bdata['d_data'], $params['lid']);
				array_push($bdata['d_data'], $detail['issue']);
				array_push($bdata['d_data'], ParseUnit($detail['money']));
				array_push($bdata['d_data'], $detail['multi']);
				array_push($bdata['d_data'], $this->status['create']);	//订单状态
				array_push($bdata['d_data'], $endTime);
				array_push($bdata['d_data'], $detail['award_time']);
			}
			$orderUpfields = array('bonus', 'endTime', 'award_time', 'status');
			$sql2 = "insert cp_chase_orders(" . implode(', ', $orderFields) . ") values" .
					implode(', ', $bdata['s_data']) . $this->onduplicate($orderFields, $orderUpfields);
			$orderStatus = $this->db->query($sql2, $bdata['d_data']);
			
			if($manageStatus && $orderStatus)
			{
				$this->db->trans_complete();
			
				$createStatus = array(
					'status' => TRUE,
					'msg' => '创建追号订单成功',
					'data' => $params
				);
			}
			else
			{
				$this->db->trans_rollback();
			
				$createStatus = array(
					'status' => FALSE,
					'msg' => '创建追号订单失败',
					'data' => $params
				);
			}
		}
		else
		{
			$createStatus = array(
				'status' => FALSE,
				'msg' => '创建追号订单失败',
				'data' => $params
			);
		}
		
        return $createStatus;
	}
	
	// 针对十一选五、排列三、福彩3D 混合投注playType处理
	public function getLotteryPlayType($params)
	{
		if(in_array($params['lid'], array('21406', '33', '52', '21407', '21408', '54', '55', '21421')))
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

	/**
	 * 根据用户ID，追号订单ID查询追号订单信息
	 * 
	 * @param array $params
	 *        	参数顺序 uid, chaseId
	 * @return array
	 */
	public function getChaseInfoById($params)
	{
		$this->load->model('lottery_model');
		//$cfgDB = $this->load->database('cfg', true);
		$sql = "SELECT lid, status, chaseId, created, pay_time, money, singleFlag,setStatus, setMoney, endTime,
				status, totalIssue, chaseIssue, revokeIssue, failIssue, codes, bonus 
				FROM cp_chase_manage 
				WHERE uid = ? and chaseId = ?";
		$chaseInfo = $this->slave->query ( $sql, $params )->getRow ();
		if (!empty($chaseInfo)) 
		{
			$chaseInfo['hasstop'] = '0';
			$sqldtl = "select orderId, issue, money, status, bonus, award_time, bet_flag
				from cp_chase_orders where chaseId = ?";
			$chaseDetail = $this->slave->query ( $sqldtl, array('chaseId' => $params['chaseId']) )->getAll ();
			$issue = array();
			foreach ($chaseDetail as $detail) 
			{
				$issue[] = in_array($chaseInfo['lid'], array(23529, 10022, 33, 35)) ? preg_replace('/20(\d+)/', '$1', $detail['issue']) : $detail['issue'];
				if ($chaseInfo['status'] == 240 && $detail['status'] == 0 && $detail['bet_flag'] == 0) 
				{
					$chaseInfo['hasstop'] = '1';
				}
			}
			$sqlisue = "select issue, awardNum from ".$this->lottery_model->getTbName($chaseInfo['lid'])." where issue in (".implode(',', $issue).")";
			$award = $this->slaveCfg->query ( $sqlisue )->getAll ();
			foreach ($award as $aw) 
			{
				$key = in_array($chaseInfo['lid'], array(23529, 10022, 33, 35)) ? '20'.$aw['issue'] : $aw['issue'];
				$resaward[$key] = $aw['awardNum'];
			}
			return array('info' => $chaseInfo, 'detail' => $chaseDetail, 'award' => $resaward);
		}
		return false;
	}
	
	/**
	 * 根据用户ID，追号订单ID查询追号大订单信息
	 *
	 * @param array $params
	 *        	参数顺序 uid, chaseId
	 * @return array
	 */
	public function getManageById($params)
	{
		$sql = "SELECT lid, money, isChase, betTnum, totalIssue FROM cp_chase_manage WHERE uid = ? and chaseId = ?";
		$chaseInfo = $this->slave->query ( $sql, $params )->getRow ();
		return $chaseInfo;
	}
	
	public function getById($chaseId){
		$sql = "select lid, codes, playType from cp_chase_manage where chaseId = ?";
		$info = $this->slave->query ( $sql, array('chaseId' => $chaseId) )->getRow ();
		return $info;
	}
	
	// 保存追号订单信息
	public function saveChaseInfo($chaseInfo)
	{
		$upfields = array('trade_no', 'bonus', 'chaseIssue', 'status', 'pay_time');
        $fields = array_keys($chaseInfo);
        $sql = "insert cp_chase_manage(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())" . 
        $this->onduplicate($fields, $upfields);
        return $this->db->query($sql, $chaseInfo);
	}
	
	/**
	 * 追号记录页面，查询追号大订单信息
	 * @param unknown $cons
	 * @param unknown $cpage
	 * @param unknown $psize
	 * @return unknown
	 */
	public function getChases($cons, $cpage, $psize)
	{
		$table = 'cp_chase_manage';
		$conStr = ' and m.uid = ? and m.created >= ? and m.created <= ? and (m.is_hide & 1) = 0';
		foreach ( $cons as $k => $con )
		{
			if ($k === 'other')
			{
				foreach ( $con as $cn )
				{
					$conStr .= $cn;
				}
			} elseif (! in_array ( $k, array (
					'uid',
					'start',
					'end' 
			) ))
			{
				$conStr .= " and m." . $k . " = ?";
			}
		}
		unset ( $cons ['other'] );
		$nsql = "select count(*) total, sum(if(m.status in ('0', '240'), 1, 0)) chaseing, sum(m.money) money, sum(m.bonus) as bonus from " . $table . " as m where 1 " . $conStr;
		$sql = "select m.chaseId, m.lid, m.created, m.totalIssue, m.chaseIssue, m.money, m.status, m.bonus, SUM(IF(o.`status` = '0', 1, 0)) as hasstop, m.endTime, m.chaseType,m.singleFlag
				from " . $table . " as m
				JOIN cp_chase_orders AS o ON o.chaseId = m.chaseId
				where 1 " . $conStr . " 
				GROUP BY o.chaseId
				ORDER BY m.created DESC
				limit " . ($cpage - 1) * $psize . "," . $psize;
		$res ['totals'] = $this->slave->query ( $nsql, $cons )->getRow ();
		$res ['datas'] = $this->slave->query ( $sql, $cons )->getAll ();
		return $res;
	}
	
	/**
	 * 手动撤单操作
	 * @param 用户uid int $uid
	 * @param 追号订单ID int $chaseId
	 * @param 彩种ID int $lid
	 * @param 期次数组  array $issues
	 * @return 状态码  2:存在不符合条件的期次，1:更新失败，0:成功
	 */
	public function stopOrders($uid, $chaseId, $lid, $issues = array())
	{
		//检查期次
		$CacheArr = array(
			51    => 'SSQ_ISSUE',
			23529 => 'DLT_ISSUE',
			33    => 'PLS_ISSUE',
			35    => 'PLW_ISSUE',
			52    => 'FC3D_ISSUE',
			10022 => 'QXC_ISSUE',
			23528 => 'QLC_ISSUE',
			21406 => 'SYXW_ISSUE_TZ',
			21407 => 'JXSYXW_ISSUE_TZ',
			21408 => 'HBSYXW_ISSUE_TZ',
			53	  => 'KS_ISSUE_TZ',
			54	  => 'KLPK_ISSUE_TZ',
			55	  => 'CQSSC_ISSUE_TZ',
			56	  => 'JLKS_ISSUE_TZ',
		    57	  => 'JXKS_ISSUE_TZ',
		    21421 => 'GDSYXW_ISSUE_TZ',
		);
		$REDIS = $this->config->item('REDIS');
		$current = json_decode($this->cache->get($REDIS[$CacheArr[$lid]]), TRUE);
		foreach ($issues as $issue) 
		{
			if ($issue < $current['cIssue']['seExpect']) 
			{
				return '2';
			}
		}
		
		$this->db->trans_start();
		$order_status = $this->orderConfig('orders');
		$this->load->model('chase_wallet_model');
		
		$sql = "select id as num from cp_chase_orders where bet_flag = 0 and chaseId = ?";
		if (!empty($issues)) 
		{
			$sql .= " and issue in (".implode(',', $issues).")";
		}
		$sql .= " and status='".$order_status['create_init']."'";
		$data = $this->db->query($sql, array('chaseId' => $chaseId))->getCol();
		
		if (count($data) < count($issues)) 
		{
			$this->db->trans_rollback();
			return '2';
		}
		
		$sql = "select id from cp_chase_orders where id in (".implode(',', $data).") for update";
		$this->db->query($sql);
		//更新状态
		$sql = "update cp_chase_orders set status = '".$order_status['revoke_by_user']."' where id in (".implode(',', $data).")";
		$res = $this->db->query($sql);
		if ($res) 
		{
			$this->db->trans_complete();
			$res = $this->db->query("select id from cp_chase_orders where chaseId = ? and status in ('40', '240', '500') limit 1", array('chaseId' => $chaseId))->getRow();
			if ($res) 
			{
				return '1';//返回追号中
			}else 
			{
				return '0';//返回追号完成
			}
		}else 
		{
			$this->db->trans_rollback();
			return '3';
		}
	}
	
	// 刷新钱包
	public function freshWallet($uid)
	{
		$this->load->model('wallet_model');
		return $this->wallet_model->freshWallet($uid);
	}
	
	public function upOutOfDay($chaseId)
	{
		$this->db->query("update cp_chase_manage set status = ? where chaseId = ?", 
		array($this->status['out_of_date_paying'], $chaseId));
	}
	
	public function getCodesById($chaseId)
	{
		return $this->slave->query('select codes from cp_chase_manage where chaseId = ?', array($chaseId))->getCol();
	}
}
