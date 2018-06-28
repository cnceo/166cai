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
		$this->load->driver('cache', array('adapter' => 'redis'));
	}

	// 获取订单状态标示
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * 根据用户ID，追号订单ID查询追号订单信息
	 * 
	 * @param array $params
	 * 参数顺序 uid, chaseId
	 * @return array
	 */
	public function getChaseInfoById($params)
	{
		$this->load->model('lottery_model');
		$sql = "SELECT lid, status, chaseId, created, pay_time, money, setStatus, setMoney, endTime, chaseType,
				status, totalIssue, chaseIssue, revokeIssue, failIssue, codes, isChase, buyPlatform, bonus, created 
				FROM cp_chase_manage 
				WHERE uid = ? and chaseId = ?";
		$chaseInfo = $this->slave->query ( $sql, $params )->getRow ();
		if (!empty($chaseInfo)) 
		{
			$chaseInfo['hasstop'] = '0';
			$sqldtl = "select orderId, issue, money, status, my_status, bonus, award_time, bet_flag
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
	 * 参数顺序 uid, chaseId
	 * @return array
	 */
	public function getManageById($params)
	{
		$sql = "SELECT lid, money, isChase, betTnum, totalIssue FROM cp_chase_manage WHERE uid = ? and chaseId = ?";
		$chaseInfo = $this->db->query ( $sql, $params )->getRow ();
		return $chaseInfo;
	}
	
	public function getById($chaseId){
		$sql = "select lid, codes, playType from cp_chase_manage where chaseId = ?";
		$info = $this->db->query ( $sql, array('chaseId' => $chaseId) )->getRow ();
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
		$conStr = ' and m.uid = ?';

		if($cons['status'])
		{
			$chaseStatus = $this->status[$cons['status']];

			if($chaseStatus)
			{
				$conStr .= " and m.status = ?";
				$cons['status'] = $chaseStatus;
			}
			else
			{
				$conStr .= " and m.bonus > 0";
				unset($cons['status']);
			}			
		}
		else
		{
			$conStr .= " and m.status NOT IN(20, 21) and m.status >= 240"; 
			unset($cons['status']);
		}

		if($cons['lid'])
		{
			$conStr .= " and m.lid = ?";
		}
		else
		{
			unset($cons['lid']);
		}

		if(isset($cons['is_hide']))
		{
			$conStr .= " and (m.is_hide & 1) = 0";
		}
		
		$nsql = "select count(*) total, sum(if(m.status in ('0', '240'), 1, 0)) chaseing, sum(m.money) money, sum(m.bonus) as bonus from " . $table . " as m where 1 " . $conStr;
		$sql = "select m.chaseId, m.lid, m.created, m.totalIssue, m.chaseIssue, m.money, m.status, m.bonus, SUM(IF(o.`status` = '0', 1, 0)) as hasstop, m.endTime
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
			53 => 'KS_ISSUE_TZ',
			54 => 'KLPK_ISSUE_TZ',
			56 => 'JLKS_ISSUE_TZ',
		    57 => 'JXKS_ISSUE_TZ',
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
		// 获取子订单状态参数
		$this->load->config('order');
        $order_status = $this->config->item("cfg_orders");

		// $this->load->model('chase_wallet_model');
		
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
}
