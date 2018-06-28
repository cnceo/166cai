<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Activity_Model extends MY_Model
{
	public $acname;
	public $ucname;
	public $ccname;
	public function __construct()
	{
		$this->acname = 'cp_channel_activity';
		$this->ucname = 'cp_activity_user';
		$this->ccname = 'cp_channel_ad';
        parent::__construct();
	}
	
	//活动派奖金
	public function dispatch($uid, $acid, $trans = true)
	{
		$dispatchs = $this->getDispatch($acid);
		if(!empty($dispatchs))
		{
			if($trans) $this->db->trans_start();
			$activity_num = $this->db->query("select activity_num from {$this->ucname} 
			where uid = ? and activity_id = ? for update", array($uid, $acid))->getOne();
			if($activity_num < $dispatchs['activity_num'])
			{
				$this->load->model('wallet_model');
				$money = $this->wallet_model->getMoney($uid);
				$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
				$datas = array('uid' => $uid, 'mark' => '1', 'trade_no' => $trade_no, 'money' => $dispatchs['dispatch'], 
				'dispatch' => $dispatchs['dispatch'], 'ctype' => $this->wallet_model->ctype['dispatch'], 'umoney' => $money['money']);
				$this->wallet_model->addMoney($uid, $datas, false);
				$this->db->query("insert {$this->ucname}(uid, activity_id, activity_num, created) values(?, ?, ?, now()) 
				on duplicate key update activity_num = activity_num + values(activity_num)", array($uid, $acid, 1));
				if($trans) $this->db->trans_complete();
				return 0; //成功
			}
			else 
			{
				if($trans) 
				{
					$this->db->trans_rollback();
					$this->wallet_model->freshWallet($uid);
				}
				return 1; //失败
			}
		}
		else 
		{
			return 2; //活动结束
		}
		
	}
	
	public function getDispatch($acid)
	{
		return $this->db->query("select m.dispatch, m.activity_num from {$this->acname} m join 
		{$this->ccname} n on m.channel_id = n.id 
		where m.id = ? and n.delect_flag = 0 and m.delect_flag = 0 and m.start_time < now()
		and m.end_time > now()", array($acid))->getRow();
	}
	
	public function getIsdispatch($uid, $acid)
	{
		$Isdispatch = $this->db->query("select if(n.activity_num > if(u.activity_num > 0, u.activity_num, 0), 1, 0) isdispatch 
		from {$this->ccname} m join {$this->acname} n on m.id = n.channel_id and n.id = ?
		left join {$this->ucname} u on u.activity_id = n.id and u.uid = ?
		where m.delect_flag = 0 and n.delect_flag = 0 and n.start_time < now() and n.end_time > now()",
		array($acid, $uid))->getOne();
		return $Isdispatch;
	}
	/**
     * 查看用户是否参加返点活动
     * @param $uid
     */
    public function checkRebateByUid( $uid )
    {
    	$sql = "select count(*) from cp_relationship where stop_flag = 0 and uid = ?";
    	$ispart = $this->db->query($sql, $uid)->getOne();
    	return $ispart > 0 ? 1 : 0;
    }
    /**
     * 依据活动类型启动不同的活动处理逻辑
     * @param $uid
     */
    public function activity_deal($sdate, $edate)
    {
    	$act_maps = array(
    		'2' => array(array('500', '510', '1000', '2000'), 1),
    		'3' => array(array('600', '1000', '2000'), 2),
    		'4' => array(array('600', '1000', '2000'), 4),
    		'5' => array(array('600', '1000', '2000'), 8),
    	);

    	log_message('LOG', "开始时间: " . $sdate, 'activity_deal');
    	log_message('LOG', "结束时间: " . $edate, 'activity_deal');

    	// 检查是否有进行的活动 当前只考虑 4 加奖
    	$activityInfo = $this->getDoingJjActivity();

    	if(!empty($activityInfo))
    	{
    		// 打活动标签
	    	$sql = "select uid, orderId, lid, status, activity_ids, activity_status, money, failMoney, 
	    	(money-failMoney) as calMoney, issue, userName, bonus, codes, buyPlatform, created from cp_orders where modified >= ? 
	    	and modified < ? and status >= 40 and (activity_status & 4) = 0 and orderType != 4 order by orderId asc";

	    	$orderData = $this->db->query($sql, array($sdate, $edate))->getAll();

	    	if(!empty($orderData))
	    	{
	    		$this->load->library("activity/dispatch");
	    		foreach ($orderData as $orderDetail) 
	    		{
	    			$this->dispatch->index($orderDetail, $act_maps);
	    		}
	    	}
    	}
    	
    	// 处理活动标签
    	$act_fields = array();
    	$act_status = array();
    	foreach ($act_maps as $id => $act)
    	{
    		$act_fields[$id] = "(((activity_ids & {$act[1]}) = {$act[1]}) && ((activity_status & {$act[1]}) = 0))";
    		foreach ($act[0] as $status)
    		{
    			$act_status[$status] = $status; 
    		}
    	}
    	$sql = "select uid, orderId, lid, status, my_status, activity_ids, activity_status, money, failMoney, 
    	(money-failMoney) as calMoney, issue, userName, bonus, margin, created from cp_orders where modified >= ? 
    	and modified < ? and status 
    	in(" . implode(', ', $act_status) . ") and (" . implode(' || ', $act_fields) . ")";
    	$orders = $this->db->query($sql, array($sdate, $edate))->getAll();

    	if(!empty($orders))
    	{
    		foreach ($orders as $order)
    		{
	    		foreach ($act_maps as $id => $act)
	    		{
	    			if((($order['activity_ids'] & $act[1]) == $act[1]) && (!($order['activity_status'] & $act[1])))
	    			{
	    				$this->trans_start('db');
	    				$actlib = "act_$id";
	    				$this->load->library("activity/$actlib");
	    				if($this->$actlib->index($order))
	    				{
	    					$re = $this->db->query("update cp_orders set activity_status = activity_status | {$act[1]} where orderId = ?", array($order['orderId']));
	    					if($re)	$this->trans_complete('db');
	    				}
	    				else 
	    				{
	    					$this->trans_rollback('db');
	    				}
	    			}
	    		}
    		}
    	}
    }
    
    public function getRelations($uid)
    {
    	$sql = "select m.rebate_odds, n.rebate_odds prebate_odds, m.uid, n.uid puid, n.stop_flag pstop_flag, 
    		m.stop_flag from cp_relationship m 
			left join cp_relationship n on m.puid = n.uid and n.puid = 0 where m.uid = ?";
    	return $this->db->query($sql, array($uid))->getRow();
    }
    
    public function saveDetails($relation, $order)
    {
		$data['d_sql'] = array();
		$data['d_val'] = array();
		if( empty($relation['puid']) && $relation['rebate'] > 0 )
		{
			array_push($data['d_sql'], '(?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
			array_push($data['d_val'], $relation['uid']);
			array_push($data['d_val'], $relation['uid']);
			array_push($data['d_val'], $order['userName']);
			array_push($data['d_val'], $order['orderId']);
			array_push($data['d_val'], $order['lid']);
			array_push($data['d_val'], $order['issue']);
			array_push($data['d_val'], $order['calMoney']);
			array_push($data['d_val'], $relation['rebate']);
			array_push($data['d_val'], $relation['rebate_odds']);
			
			$walletlogs[0]['uid'] = $relation['uid'];
			$walletlogs[0]['mark'] = '1';
			$walletlogs[0]['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
			$walletlogs[0]['orderId'] = $order['orderId'];
			$walletlogs[0]['money'] = $relation['rebate'];
			$walletlogs[0]['ctype'] = '14';
		}
		else
		{
			if($relation['rebate'] > 0)
			{
				array_push($data['d_sql'], '(?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
				array_push($data['d_val'], $relation['puid']);
				array_push($data['d_val'], $relation['uid']);
				array_push($data['d_val'], $order['userName']);
				array_push($data['d_val'], $order['orderId']);
				array_push($data['d_val'], $order['lid']);
				array_push($data['d_val'], $order['issue']);
				array_push($data['d_val'], $order['calMoney']);
				array_push($data['d_val'], $relation['rebate']);
				array_push($data['d_val'], $relation['rebate_odds']);
				
				$walletlogs[0]['uid'] = $relation['uid'];
				$walletlogs[0]['mark'] = '1';
				$walletlogs[0]['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
				$walletlogs[0]['orderId'] = $order['orderId'];
				$walletlogs[0]['money'] = $relation['rebate'];
				$walletlogs[0]['ctype'] = '14';
			}
			
			if($relation['prebate'] > 0)
			{
				array_push($data['d_sql'], '(?, ?, ?, ?, ?, ?, ?, ?, ?, now())');
				array_push($data['d_val'], $relation['puid']);
				array_push($data['d_val'], $relation['puid']);
				array_push($data['d_val'], $order['userName']);
				array_push($data['d_val'], $order['orderId']);
				array_push($data['d_val'], $order['lid']);
				array_push($data['d_val'], $order['issue']);
				array_push($data['d_val'], $order['calMoney']);
				array_push($data['d_val'], $relation['prebate']);
				array_push($data['d_val'], $relation['prebate_odds']);
				
				$walletlogs[1]['uid'] = $relation['puid'];
				$walletlogs[1]['mark'] = '1';
				$walletlogs[1]['trade_no'] = $this->tools->getIncNum('UNIQUE_KEY');
				$walletlogs[1]['orderId'] = $order['orderId'];
				$walletlogs[1]['money'] = $relation['prebate'];
				$walletlogs[1]['ctype'] = '14';
			}
		}
		return $this->saveActData($data, $walletlogs, $order, $relation);
    }

    private function saveActData($data, $walletlogs, $order, $relation)
    {
    	$rebatefields = array('puid', 'uid', 'userName', 'orderid', 'lid', 'issue', 'money', 'income',
		'rebate_odds', 'created');
		
		$this->load->model('wallet_model');
		$this->load->model('capital_model');
		$re1 = true; $re2 = true; $re3 = true; $re4 = true;
		$re5 = true; $re6 = true; $re7 = true;
		if(!empty($walletlogs[0]))
		{
			$re1 = $this->wallet_model->addMoney($walletlogs[0]['uid'], $walletlogs[0], false);
			$re2 = $this->capital_model->recordCapitalLog(2, $walletlogs[0]['trade_no'], 'rebate', $walletlogs[0]['money'], '2', false);
		}
		if(!empty($relation['uid']))
		{
			//购买用户收入汇总
			$re6 = $this->db->query("update cp_relationship set total_income = total_income + ?,
				total_purchase = total_purchase + ?, total_sale = total_sale + ? where uid = ?", 
				array($relation['rebate'], $order['calMoney'], $order['calMoney'], $relation['uid']));
		}
		if(!empty($walletlogs[1]))
		{
			$re3 = $this->wallet_model->addMoney($walletlogs[1]['uid'], $walletlogs[1], false);
			$re4 = $this->capital_model->recordCapitalLog(2, $walletlogs[1]['trade_no'], 'rebate', $walletlogs[1]['money'], '2', false);
		}
		if(!empty($relation['puid']))
		{
			//上级用户收入汇总
			$re7 = $this->db->query("update cp_relationship set total_income = total_income + ?,
				total_sale = total_sale + ? where uid = ?", 
				array($relation['prebate'], $order['calMoney'], $relation['puid']));
		}
		if(!empty($data['d_sql']))
		{
			$sqlrebate = "insert cp_rebate_details(" . implode(', ', $rebatefields) . ") values" . 
			implode(', ', $data['d_sql']);
			$re5 = $this->db->query($sqlrebate, $data['d_val']);
		}
		if($re1 && $re2 && $re3 && $re4 && $re5 && $re6 && $re7)
		{
			return true;
		}
		else 
		{
			return false;
		}
    }

    public function getActivityInfo($activityId)
    {
    	$sql = "select id, a_name, params, start_time, end_time, remark, delete_flag from cp_activity where id in(" . implode(', ', $activityId) . ");";
    	$activity = $this->db->query($sql)->getAll();
    	return $activity;
    }

    public function getUserRebate($uid)
    {
    	$sql = "select puid, uid, pro_link, rebate_odds, total_income, total_purchase, stop_flag from cp_relationship where uid = ?";
    	return $this->db->query($sql, array($uid))->getRow();
    }

    public function updateActivityInfo($orderId, $activity_ids)
    {
    	$sql = "UPDATE cp_orders SET activity_ids = ? WHERE orderId = ?";
    	return $this->db->query($sql, array($activity_ids, $orderId));
    }

    public function updateActivityComplete($orderId, $activity_status)
    {
    	$sql = "UPDATE cp_orders SET activity_status = ? WHERE orderId = ?";
    	return $this->db->query($sql, array($activity_status, $orderId));
    }

    public function getDoingJjActivity()
    {
    	$sql = "SELECT id, activity_id, lid, playType, startTime, endTime, mark, ctype, params FROM cp_activity_jj_config WHERE startTime <= NOW() AND status = 0";
    	$activity = $this->db->query($sql)->getAll();
    	return $activity;
    }
    
    /**
     * 追号不中包赔活动返奖
     */
    public function chaseActivity()
    {
    	$sql = "SELECT c.uid,c.chaseId, c.lid, c.chaseType,c.money,c.bonus, c.chaseIssue, c.sysRevokeIssue, c.failIssue, c.totalIssue, a.issues,a.payMoney 
    	FROM cp_chase_manage c INNER JOIN cp_activity_chase_config a ON a.id=c.chaseType 
    	WHERE c.modified > date_sub(now(), interval 3 day) AND c.lid IN('51', '23529') AND c.status=700 AND c.bonus=0 AND (c.revokeIssue - c.sysRevokeIssue = 0) AND ((c.cstate & 1) = 0)
    	limit 50";
    	$orders = $this->db->query($sql)->getAll();
    	$this->load->model('wallet_model');
    	while(!empty($orders))
    	{
    		$this->db->trans_start();
    		$uids = array();
    		foreach ($orders as $order)
    		{
    			$money = ($order['payMoney'] / $order['issues']) * $order['chaseIssue'];
    			if($money > 0)
    			{
    				$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
    				$res = $this->db->query("update cp_user set money = money + {$money}, dispatch = dispatch + {$money} where uid = ?", array($order['uid']));
    				$res1 = $this->db->query("insert cp_wallet_logs(uid, mark, money, ctype, trade_no, orderId, umoney, content, created)
    				select '{$order['uid']}', '1', '{$money}', 9, '$trade_no', '{$order['chaseId']}', money, '追号不中包赔', now() from cp_user where uid = ?", array($order['uid']));
    				$res2 = $this->db->query("update cp_chase_manage set cstate = cstate | 1 where chaseId = ?", array($order['chaseId']));
    				// 总账记录流水
    				$this->load->model('capital_model');
    				$res3 = $this->capital_model->recordCapitalLog('2', $trade_no, 'repaid', $money, '2', $tranc = FALSE);
    				if($res && $res1 && $res2 && $res3)
    				{
    					array_push($uids, $order['uid']);
    				}
    				else
    				{
    					$this->db->trans_rollback();
    				}
    			}
    			else
    			{
    				$this->db->query("update cp_chase_manage set cstate = cstate | 1 where chaseId = ?", array($order['chaseId']));
    			}
    		}
    		$this->db->trans_complete();
    		//更新用户钱包缓存
    		if($uids)
    		{
    			foreach ($uids as $uid)
    			{
					$this->wallet_model->freshWallet($uid);
    			}
    		}
    		
    		$orders = $this->db->query($sql)->getAll();
    	}
    }
    
    /**
     * [doPullRedPack 红包派发活动]
     * @author JackLee 2017-03-27
     * @return [type] [description]
     */
    public function doPullRedPack()
    {
    	//没有派发的批次
    	$notPullAuditIds = $this->getNotPullAuditIds();
        if(!count($notPullAuditIds)) return false;
    	//需要派发的记录
    	$eachData = $pullData = $this->getPullData($notPullAuditIds);
        $userInfo = array();
    	$returnTag = true;
    	//循环发放按照批次
    	foreach ($notPullAuditIds as  $id) 
    	{
            $userInfo[$id] = array();
            $userInfo[$id]['uid'] = array();
            $userInfo[$id]['data'] = array();
            //批次成功标识
    		$batchFlag = true;
            //开启事务处理
            $this->db->trans_start();
            $values = array();
    		foreach ($eachData as $k => $v) 
    		{
    			if($v['audit_id'] == $id)
    			{
                    if(!in_array($v['uid'],$userInfo[$id]['uid']))
                    {
                        $userInfo[$id]['uid'][] = $v['uid'];
                        $userInfo[$id]['data'][] = array('uid'=>$v['uid'],'uname'=>$v['uname'],'phone'=>$v['phone'],'message'=>$v['message']);
                    }
                    //红包记录多条
                    for($i = 0 ; $i < $v['num']; $i++)
                    {
						$values[] = $this->getInsertSql($v);
                    }
                    unset($eachData[$k]);
    			}
    		}
            $userInfo[$id] = $userInfo[$id]['data'];
            $values = implode(',', $values);
            //插入红包
            $batchLogSql = "INSERT INTO cp_redpack_log(aid,uid,rid,status,valid_start,valid_end,remark,get_time,created) VALUES $values"; 
            $batchLogSqlTag = $this->db->query($batchLogSql);
            //更新cp_redpack_send_log
            $updateSendLogSql = "UPDATE cp_redpack_send_log SET status = '1' , modified = now() WHERE audit_id = ?";
            $updateSendLogTag = $this->db->query($updateSendLogSql,array($id));
            //更新批次
            $updateBatchSql = "UPDATE cp_redpack_audit SET send_flag = '1' , modified = now() WHERE id = ?";
            $updateBatchTag = $this->db->query($updateBatchSql,array($id));
            if($batchLogSqlTag === false && $updateSendLogTag === false && $updateBatchTag === false )
            {
                $this->db->trans_rollback();
                return $batchFlag = false;
            }
            else
            {
                $this->db->trans_complete();
            }
    	}
        //批量派发短信 一个批次发送一次短信
        $sendData = $this->batchSendMsg($userInfo);
    	return array('tag' => $returnTag,'auditIds' => $notPullAuditIds);
    	
    }
    /**
     * [getNotPullAuditIds 获取没有派发的期次ID]
     * @author JackLee 2017-03-27
     * @return [type] [description]
     */
    public function getNotPullAuditIds()
    {
        $sql = "SELECT id FROM cp_redpack_audit where send_flag = 0 and status = 1 and modified >= date_sub(now(), interval 1 day)";
        return $this->db->query($sql)->getCol();
    }
    /**
     * [getPullData 获取将要派发的红包]
     * @author JackLee 2017-03-27
     * @param  [type] $notPullAuditIds [description]
     * @return [type]                  [description]
     */
    public function getPullData($notPullAuditIds)
    {
        $sql = "SELECT l.id,l.uid,l.num,l.validity,u.uname,i.real_name,i.phone,a.message,l.redpack_id,l.audit_id,l.status,r.use_params,r.p_name,r.use_desc 
                FROM cp_redpack_send_log AS l 
                LEFT JOIN cp_redpack AS r ON l.redpack_id = r.id 
                LEFT JOIN cp_redpack_audit AS a ON a.id = l.audit_id 
                LEFT JOIN cp_user AS u ON l.uid = u.uid 
                LEFT JOIN cp_user_info AS i ON l.uid = i.uid 
                WHERE l.status = 0 AND l.audit_id IN ? AND l.num > 0";
        $rows = $this->db->query($sql,array($notPullAuditIds))->getAll();
        return $rows;
    }
    /**
     * [getInsertSql 红包派发记录插入sql组合]
     * @author JackLee 2017-03-28
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function getInsertSql($params)
    {
    	$use_params = json_decode($params['use_params'],true);
    	date_default_timezone_set('PRC');
        $cur_day_str = $params['validity'];
    	$valid_start = date('Y-m-d H:i:s',strtotime($cur_day_str));
    	$valid_end = date('Y-m-d H:i:s',strtotime($cur_day_str)+($use_params['end_day'] - $use_params['start_day'])*24*60*60-1);
    	$remark = $params['use_desc'].$params['p_name'];
        $aid = 7;
        $status = 1;
        return "('".$aid."','".$params['uid']."','".$params['redpack_id']."','".$status."','".$valid_start."','".$valid_end."','".$remark."',now(),now())";
    }

    /**
     * [batchSendMsg 批量派发短信]
     * @author JackLee 2017-03-29
     * @param  [type] $batchMsgUsers [description]
     * @return [type]                [description]
     */
    public function batchSendMsg($batchMsgUsers)
    {
        $succ = 0;
        foreach ($batchMsgUsers as $k => $v) 
        {
            foreach ($v as $k1 => $user)
            {
                if(!empty($user['message']))
                {
                    $msg = "尊敬的".$user['uname']."您好，".$user['message']."，下载APP：t.cn/R9SyzIp";
                    $tag = $this->tools->sendSms($user['uid'], $user['phone'],$msg, '11', '127.0.0.1', '193');
                    $tag = json_decode($tag,true);
                    if($tag['status'] == 1) $succ++;
                }

            }
        }

        return $succ;
    }
    
    public function getTimeById($id)
    {
    	return $this->slave->query("select start_time as startTime, end_time as endTime from cp_activity where id = ?", array($id))->getRow();
    }

    public function calJcbpData()
    {
        $start = date("Y-m-d 00:00:00", "-10 days");
        $sql = "select id from cp_activity_jcbp_config where startTime>?";
        $res = $this->db->query($sql, array($start))->getAll();
        $ids = array();
        foreach ($res as $r) {
            $ids[] = $r['id'];
        }
        $sql = "SELECT jcbp_id,count(id) as count,max(status) as status from cp_activity_jcbp_join WHERE status>=240 and created>=? and jcbp_id in ? GROUP BY jcbp_id;";
        $rows = $this->db->query($sql, array($start, $ids))->getAll();
        $sql = "SELECT jcbp_id,sum(money)as money from(select j.jcbp_id,p.money from cp_activity_jcbp_join j left join cp_redpack_log r on j.uid=r.uid left join cp_redpack p on r.rid=p.id where j.status=1000 and j.created>=? and (r.rid=? or r.rid=?) and j.jcbp_id in ? and r.status = 2) o GROUP BY jcbp_id";
        $costs = $this->db->query($sql, array($start, 110, 111, $ids))->getAll();
        foreach ($rows as $k => $row) {
            $rows[$k]['cost'] = 0;
            foreach ($costs as $cost) {
                if ($row['jcbp_id'] == $cost['jcbp_id']) {
                    $rows[$k]['cost'] = $cost['money'];
                }
            }
        }
        foreach ($rows as $row) {
            $sql = "update cp_activity_jcbp_config set joinNum=?,status=?,cost=? where id=?";
            $this->db->query($sql, array($row['count'], $row['status'], $row['cost'], $row['jcbp_id']));
        }
    }
    
    public function getJzRankLists()
    {
        $time = date("Y-m-d H:i:s");
        $sql = "select plid,pissue from cp_win_rank_config where plid=3 and lids like '%42%' order by pissue desc limit 1";
        $res = $this->slave->query($sql, array($time, $time))->getRow();
        if (!empty($res['plid'])) {
            $sql = "select rankId,userName,margin,addMoney from cp_win_rank_user where plid=? and pissue=? and uid!=0 order by rankId limit 10";
            return $this->slave->query($sql, array($res['plid'], $res['pissue']))->getAll();
        }
        return array();
    }

} 
