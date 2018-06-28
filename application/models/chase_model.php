<?php

class Chase_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('chase_order_model');
        $this->load->model('chase_wallet_model');
        $this->chaseStatus = $this->chase_order_model->getStatus();
        $this->walletStatus = $this->chase_wallet_model->getStatus();
        $this->order_status = $this->orderConfig('orders');
    }
    
    /**
     * 同步订单状态、奖金
     */
    public function syncStatus($lids)
    {
    	$sql = "select orderId from cp_orders where modified > date_sub(now(), interval 20 minute) and orderType in('1', '6') and c_synflag = 0 and lid in ? limit 1000";
    	$orderId = $this->db->query($sql, array($lids))->getCol();
    	while (!empty($orderId))
    	{
    	    $sData = implode(',', array_fill(0, count($orderId), '?'));
    	    $sql1 = "update cp_chase_orders a join cp_orders b on a.orderId = b.orderId set
    	    a.status = b.status,
    	    a.bonus = b.bonus,
    	    a.margin = b.margin,
    	    a.my_status = b.my_status,
    	    a.stats_flag = 0,
    	    b.c_synflag = 1
    	    where a.orderId in ({$sData})";
    	    $this->db->query($sql1, $orderId);
    	    
    	    $orderId = $this->db->query($sql, array($lids))->getCol();
    	}
    }
    
    /**
     * 更新追号管理表信息
     */
    public function calBouns($lids, $tableSuffix = '')
    {
    	$check_sql = "select a.chaseId from cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId = b.chaseId 
    	where a.modified > date_sub(now(), interval 20 minute) and a.stats_flag = '0' 
    	and b.status not in('{$this->chaseStatus['create']}', '{$this->chaseStatus['out_of_date_paying']}', '{$this->chaseStatus['out_of_date_payed']}', '{$this->chaseStatus['chase_over']}') 
    	and a.status > '{$this->order_status['drawing']}' and a.lid in ? limit 1";
    	$chaseId = $this->db->query($check_sql, array($lids))->getOne();
    	if($chaseId)
    	{
    		//将要统计的订单进行标识
    		$sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId = b.chaseId 
    		SET a.`stats_flag`='2'
    		where a.modified > date_sub(now(), interval 20 minute) and a.stats_flag = '0' and a.lid in ? and b.status not in('{$this->chaseStatus['create']}', '{$this->chaseStatus['out_of_date_paying']}', '{$this->chaseStatus['out_of_date_payed']}', '{$this->chaseStatus['chase_over']}') and a.status > '{$this->order_status['drawing']}'";
    		$this->db->query($sql, array($lids));
    		
    		$sql = "select chaseId from cp_chase_orders where modified > date_sub(now(), interval 20 minute) and stats_flag = '2' order by id asc limit 5000";
    		$orders = $this->db->query($sql)->getAll();
    		$table = "{$this->db_config['tmp']}.cp_chase_bonus_temp{$tableSuffix}";
    		while (!empty($orders))
    		{
    		    $this->db->query("truncate {$table}");
    		    $this->insert_select($table, $orders, array('chaseId'));

                $this->db->trans_start();
		        //chase_order表入临时表
		        $sql1 = "insert into $table (chaseId,bonus,max_bonus,margin,chaseIssue,chaseMoney,revokeIssue,sysRevokeIssue,failIssue,totalIssue)
                select m.chaseId,
		        IF(m.bonus > 0 and m.cal_flag & 1 = 0, m.bonus, 0) bonus,
		        m.bonus as max_bonus,
		        IF(m.margin > 0 and m.cal_flag & 1 = 0, m.margin, 0) margin,
                IF(m.`status` IN('{$this->order_status['draw']}','{$this->order_status['notwin']}','{$this->order_status['win']}') and m.cal_flag & 2 = 0, 1, 0) chaseIssue,
                IF(m.`status` IN('{$this->order_status['draw']}','{$this->order_status['notwin']}','{$this->order_status['win']}') and m.cal_flag & 2 = 0, m.money, 0) chaseMoney,
                IF((m.`status` IN('{$this->order_status['revoke_by_user']}','{$this->order_status['revoke_by_system']}','{$this->order_status['revoke_by_award']}') and m.cancel_flag = 1 and m.cal_flag & 4 = 0), 1, 0) revokeIssue, 
                IF((m.`status` IN('{$this->order_status['revoke_by_system']}') and m.cancel_flag = 1 and m.cal_flag & 8 = 0), 1, 0) sysRevokeIssue, 
                IF(m.`status` = '{$this->order_status['concel']}' and m.cal_flag & 16 = 0, 1, 0) failIssue,
                IF((m.`status` IN('{$this->order_status['notwin']}', '{$this->order_status['win']}', '{$this->order_status['concel']}') or (m.`status` IN('{$this->order_status['revoke_by_user']}','{$this->order_status['revoke_by_system']}','{$this->order_status['revoke_by_award']}') and m.cancel_flag = 1)) and m.cal_flag & 32 = 0, 1, 0) totalIssue
                FROM cp_chase_orders m JOIN {$table} n ON n.chaseId=m.chaseId and m.stats_flag = 2
                on duplicate key update 
                bonus = {$table}.bonus + values(bonus),
                max_bonus = IF({$table}.max_bonus > values(max_bonus), {$table}.max_bonus, values(max_bonus)),
                margin = {$table}.margin + values(margin),
                chaseIssue = {$table}.chaseIssue + values(chaseIssue),
                chaseMoney = {$table}.chaseMoney + values(chaseMoney), 
                revokeIssue= {$table}.revokeIssue + values(revokeIssue), 
                sysRevokeIssue= {$table}.sysRevokeIssue + values(sysRevokeIssue),
                failIssue= {$table}.failIssue + values(failIssue),
                totalIssue={$table}.totalIssue + values(totalIssue)";
                $re1 = $this->db->query($sql1);
                //设置统计状态
                $sql1 = "update cp_chase_orders m JOIN {$table} n ON n.chaseId=m.chaseId and m.stats_flag = 2
                set m.cal_flag = m.cal_flag | IF(m.bonus > 0 and m.cal_flag & 1 = 0, 1, 0)
                | IF(m.`status` IN('{$this->order_status['draw']}','{$this->order_status['notwin']}','{$this->order_status['win']}') and m.cal_flag & 2 = 0, 2, 0)
                | IF((m.`status` IN('{$this->order_status['revoke_by_user']}','{$this->order_status['revoke_by_system']}','{$this->order_status['revoke_by_award']}') and m.cancel_flag = 1 and m.cal_flag & 4 = 0), 4, 0) 
                | IF((m.`status` IN('{$this->order_status['revoke_by_system']}') and m.cancel_flag = 1 and m.cal_flag & 8 = 0), 8, 0)
                | IF(m.`status` = '{$this->order_status['concel']}' and m.cal_flag & 16 = 0, 16, 0)
                | IF((m.`status` IN('{$this->order_status['notwin']}', '{$this->order_status['win']}', '{$this->order_status['concel']}') or (m.`status` IN('{$this->order_status['revoke_by_user']}','{$this->order_status['revoke_by_system']}','{$this->order_status['revoke_by_award']}') and m.cancel_flag = 1)) and m.cal_flag & 32 = 0, 32, 0)";
                $re2 = $this->db->query($sql1);
    		    if($re1 && $re2){
                    $this->db->trans_complete();
                }else{
                    $this->db->trans_rollback();
                }
    		    //数据更新到管理表
    		    $sql4 = "UPDATE cp_chase_manage m JOIN {$table} n ON n.chaseId=m.chaseId
    		    SET m.chaseIssue = m.chaseIssue + n.chaseIssue,
    		    m.chaseMoney = m.chaseMoney + n.chaseMoney,
    		    m.revokeIssue = m.revokeIssue + n.revokeIssue,
    		    m.sysRevokeIssue = m.sysRevokeIssue + n.sysRevokeIssue,
    		    m.failIssue = m.failIssue + n.failIssue,
    		    m.bonus = m.bonus + n.bonus,
                m.margin = m.margin + n.margin,
                m.endIssue = m.endIssue + n.totalIssue,
    		    m.`status` = IF((m.endIssue + n.totalIssue = m.totalIssue and m.status <> '{$this->chaseStatus['stop_by_award']}'), {$this->chaseStatus['chase_over']}, IF(m.setStatus > 0, IF(n.max_bonus > m.setMoney, {$this->chaseStatus['stop_by_award']}, m.`status`), m.`status`))";
    		    $this->db->query($sql4);
    		    //更新中奖后剩下子订单状态
    		    $sql5 = "UPDATE cp_chase_orders a
    		    INNER JOIN {$table} b ON a.chaseId = b.chaseId
    		    INNER JOIN cp_chase_manage c ON c.chaseId = b.chaseId
    		    SET a.`status`='{$this->order_status['revoke_by_award']}'
    		    WHERE c.status = '{$this->chaseStatus['stop_by_award']}' AND a.status = '{$this->order_status['create_init']}'";
    		    $this->db->query($sql5);
    		    //更新子订单表状态为已统计
    		    $sql6 = "UPDATE cp_chase_orders a INNER JOIN {$table} b ON a.chaseId=b.chaseId
    		    SET a.`stats_flag`='1' where a.`stats_flag` = 2";
    		    $this->db->query($sql6);
    		    $orders = $this->db->query($sql)->getAll();
    		}
    	}
    }
    
    /**
     * 撤单脚本
     */
    public function chaseCancel($lids)
    {
    	$flag = true;
		while ($flag)
		{
			// 事务开始
			$this->db->trans_start();
			$chases = $this->getCancel($lids);
			$uids = array();
			if($chases)
			{
				foreach ($chases as $val)
				{
					if($val['chaseId'] && ($val['count'] > 0))
					{
						$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
						$res = $this->db->query("update cp_user set money = `money` + {$val['money']}, blocked = `blocked` - {$val['money']}, 
						chaseMoney = chaseMoney - {$val['money']} where uid = ?", array($val['uid']));
						$res1 = $this->db->query("update cp_chase_orders set cancel_flag='1', stats_flag = '0' where chaseId='{$val['chaseId']}' and status in('{$this->order_status['revoke_by_user']}', '{$this->order_status['revoke_by_system']}', '{$this->order_status['revoke_by_award']}') and sequence in({$val['sequence']})");
						$res2 = $this->db->query("insert cp_wallet_logs(uid, mark, money, ctype, additions, trade_no,
						orderId, umoney, created) select '{$val['uid']}', '1', '{$val['money']}', {$this->walletStatus['chase_conceal']}, '{$val['lid']}',
						'$trade_no', '{$val['chaseId']}', money, now() from cp_user where uid = ?", array($val['uid']));
						if($res && $res1 && $res2)
						{
							array_push($uids, $val['uid']);
						}
						else
						{
							$this->db->trans_rollback();
						}
					}
				}
			}
			else
			{
				$flag = false;
			}
			
			$this->db->trans_complete();
			//更新用户钱包缓存
			if($uids)
			{
				foreach ($uids as $uid)
				{
					$this->chase_wallet_model->freshWallet($uid);
				}
			}
		}
    }
    
    /**
     * 需要系统撤单的追单
     */
    private function getCancel($lids)
    {
    	$sql = "select a.uid,a.chaseId,a.lid,b.status,SUM(b.money) money,COUNT(b.id) count, GROUP_CONCAT(CAST(b.sequence AS CHAR)) sequence from cp_chase_manage a
    	INNER JOIN cp_chase_orders b ON b.chaseId=a.chaseId
    	where b.modified > date_sub(now(), interval 1 day) and a.status in('{$this->chaseStatus['is_chase']}', '{$this->chaseStatus['stop_by_award']}', '{$this->chaseStatus['chase_over']}') and b.status in('{$this->order_status['revoke_by_user']}', '{$this->order_status['revoke_by_system']}', '{$this->order_status['revoke_by_award']}') and b.cancel_flag=0 and b.lid in ? GROUP BY b.chaseId,b.status limit 10";
    	return $this->db->query($sql, array($lids))->getAll();
    }
    
    /**
     * 未付款订单状态更新
     */
    public function updateFailOrder($lids)
    {
    	//更新过期未付款操作
    	$sql = "select chaseId from cp_chase_manage where modified > date_sub(now(), interval 7 day) and status = '{$this->chaseStatus['create']}' and endTime < now() and lid in ?";
    	$chases = $this->db->query($sql, array($lids))->getCol();
    	if($chases)
    	{
    		$sData = implode(',', array_fill(0, count($chases), '?'));
    		// 事务开始
    		$this->db->trans_start();
    		$res = $this->db->query("update cp_chase_manage set status = '{$this->chaseStatus['out_of_date_paying']}' where chaseId IN({$sData})", $chases);
    		$res1 = $this->db->query("update cp_chase_orders set status = '{$this->order_status['out_of_date']}' where chaseId IN({$sData})", $chases);
    		if($res && $res1)
    		{
    			$this->db->trans_complete();
    		}
    		else
    		{
    			$this->db->trans_rollback();
    		}
    	}
    }
    
    /**
     * 操作追号单位可投状态
     * @param array() $cData	当前期数组
     * @param int $bTime		提前时间
     * @param int $lIssue		上一期期次
     */
    public function chaseNoSetAwardBet($cData, $bTime, $lIssue = '', $tableSuffix = '')
    {
    	$checkSql = "select count(1) from cp_chase_orders a
    	INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
    	WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}'
    	AND b.`status`='{$this->chaseStatus['is_chase']}' and a.bet_flag='0'";
    	//未设置中奖停止追单操作
        $endtime = date("Y-m-d H:i:s",($cData['seEndtime']/1000));
        $awardtime = date("Y-m-d H:i:s",($cData['awardTime']/1000));
    	$count = $this->db->query($checkSql . " AND b.setStatus='0'", array($cData['seLotid'], $cData['seExpect']))->getOne();
    	if($count > 0)
    	{
            if($cData['seLotid'] == 56)
            {
                $sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
			SET a.bet_flag='1',a.endTime=?,a.award_time=?
    		WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0' 
    		AND b.`status`='{$this->chaseStatus['is_chase']}' AND b.setStatus='0'";
    		$this->db->query($sql, array($endtime,$awardtime,$cData['seLotid'], $cData['seExpect']));
            }
            else
            {       
    		$sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
			SET a.bet_flag='1'
    		WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0' 
    		AND b.`status`='{$this->chaseStatus['is_chase']}' AND b.setStatus='0'";
    		$this->db->query($sql, array($cData['seLotid'], $cData['seExpect']));
            }    
    	}
    	
    	//设置中奖停止追单操作
    	$count = $this->db->query($checkSql . " AND b.setStatus='1'", array($cData['seLotid'], $cData['seExpect']))->getOne();
    	if($count > 0)
    	{
    		if(($cData['seFsendtime'] / 1000 - $bTime) < time() || empty($lIssue))
    		{
                    if($cData['seLotid'] == 56)
                    {
                        $sql1 = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
    			SET a.bet_flag='1',a.endTime=?,a.award_time=?
    			WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0' 
    			AND b.`status`='{$this->chaseStatus['is_chase']}' AND b.setStatus='1'";
    			$this->db->query($sql1, array($endtime,$awardtime,$cData['seLotid'], $cData['seExpect']));
                    }
                    else
                    {
    			$sql1 = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
    			SET a.bet_flag='1'
    			WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0' 
    			AND b.`status`='{$this->chaseStatus['is_chase']}' AND b.setStatus='1'";
    			$this->db->query($sql1, array($cData['seLotid'], $cData['seExpect']));
                    }    
    		}
    		else
    		{
    		    $this->db->query("truncate {$this->db_config['tmp']}.cp_chase_bet_temp{$tableSuffix}");
    			$sql2 = "insert {$this->db_config['tmp']}.cp_chase_bet_temp{$tableSuffix}(chaseId, issue, setMoney)
    			SELECT a.chaseId,a.issue, b.setMoney from cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
				WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' 
    			AND b.`status`='{$this->chaseStatus['is_chase']}' AND b.setStatus='1' and a.bet_flag='0'";
    			$this->db->query($sql2, array($cData['seLotid'], $cData['seExpect']));
    			//判断上一期是否已中奖
    			$sql3 = "UPDATE {$this->db_config['tmp']}.cp_chase_bet_temp{$tableSuffix} a LEFT JOIN cp_chase_orders b ON a.chaseId=b.chaseId
				SET a.`status` = IF((b.`status` IN ('{$this->order_status['draw']}')) OR (b.`status` = '{$this->order_status['win']}' AND b.bonus > a.setMoney), 0, 1)
				WHERE b.lid=? AND b.issue=?";
    			$this->db->query($sql3, array($cData['seLotid'], $lIssue));
    			//更新可投追单
                        if($cData['seLotid'] == 56)
                        {
                            $sql4 = "UPDATE cp_chase_orders a INNER JOIN {$this->db_config['tmp']}.cp_chase_bet_temp{$tableSuffix} b ON a.chaseId=b.chaseId and a.issue=b.issue
                            SET a.bet_flag='1',a.endTime=?,a.award_time=?
                            WHERE a.`status`='{$this->order_status['create_init']}' AND b.`status`='1'";
                            $this->db->query($sql4,array($endtime,$awardtime));
                        }
                        else
                        {
                            $sql4 = "UPDATE cp_chase_orders a INNER JOIN {$this->db_config['tmp']}.cp_chase_bet_temp{$tableSuffix} b ON a.chaseId=b.chaseId and a.issue=b.issue
                            SET a.bet_flag='1'
                            WHERE a.`status`='{$this->order_status['create_init']}' AND b.`status`='1'";
                            $this->db->query($sql4);
                        }
    		}
    	}
    }
    
    /**
     * 操作追号单位可投状态
     * @param int $lid		彩种
     * @param int $issue	当前期上一期期次
     */
    public function chaseLastBet($lid, $issue, $time)
    {
    	if($issue)
    	{
    		$sql = "select count(1) from cp_chase_orders a
    		INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
    		WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}'
    		AND b.`status`='{$this->chaseStatus['is_chase']}' and a.bet_flag='0'";
	    	$count = $this->db->query($sql, array($lid, $issue))->getOne();
	    	if($count > 0)
	    	{
                    if($lid == 56)
                    {
                        $endtime = date("Y-m-d H:i:s",$time['endTime']);
                        $awardtime = date("Y-m-d H:i:s",$time['awardTime']);
                        $sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
		    	SET a.bet_flag='1',a.endTime=?,a.award_time=?
		    	WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0'
		    	AND b.`status`='{$this->chaseStatus['is_chase']}'";
		    	$this->db->query($sql, array($endtime, $awardtime, $lid, $issue));
                    }
                    else
                    {
		    	$sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId
		    	SET a.bet_flag='1'
		    	WHERE a.lid=? AND a.issue=? AND a.`status`='{$this->order_status['create_init']}' and a.bet_flag='0'
		    	AND b.`status`='{$this->chaseStatus['is_chase']}'";
		    	$this->db->query($sql, array($lid, $issue));
                    }    
	    	}
    	}
    }
    
    /**
     * 追号完成发短信通知用户
     */
    public function chaseSendSms()
    {
        $sql = "select m.chaseId, m.uid, m.lid, m.status, m.created, i.msg_send from cp_chase_manage m left join cp_user_info i on m.uid=i.uid where m.modified > date_sub(now(), interval 1 day) and m.status in('{$this->chaseStatus['stop_by_award']}', '{$this->chaseStatus['chase_over']}') 
        and m.sendSms=0 limit 200";
        $result = $this->db->query($sql)->getAll();
        while (!empty($result))
        {
            $this->load->library('BetCnName');
            $this->load->model('user_model');
            $this->load->library('mipush');
            foreach ($result as $val)
            {
                $time = strtotime($val['created']);
                $vdatas = array('#MM#' => date('m', $time), '#DD#' => date('d', $time),
                        '#LID#' => BetCnName::getCnName($val['lid']));
                if(($val['msg_send'] & 8) == 0){
                    $this->user_model->sendSms($val['uid'], $vdatas, 'chase_complete', null, '127.0.0.1', '192');
                }
                $this->db->query("update cp_chase_manage set sendSms=1 where chaseId=?", array($val['chaseId']));

                // APP 消息推送
                $pushData = array(
                    'type'      =>  'chase_complete',
                    'uid'       =>  $val['uid'],
                    'lname'     =>  BetCnName::getCnName($val['lid']),
                    'orderId'   =>  $val['chaseId'],
                    'time'      =>  $val['created'],
                );
                $this->mipush->index('user', $pushData);
            }
            
            $result = $this->db->query($sql)->getAll();
        }
    }

    /**
     * 追号不中包赔完成发短信通知用户
     */
    public function chaseActivitySendSms()
    {
        $sql = "SELECT c.uid, c.chaseId, c.lid, c.chaseType, c.money, c.bonus, c.chaseIssue, c.sysRevokeIssue, c.failIssue, c.totalIssue, c.created, a.issues, a.payMoney 
        FROM cp_chase_manage c INNER JOIN cp_activity_chase_config a ON a.id=c.chaseType 
        WHERE c.modified > date_sub(now(), interval 6 hour) AND c.lid IN('51', '23529') AND c.status=700 AND c.bonus=0 AND (c.revokeIssue - c.sysRevokeIssue = 0) AND ((c.cstate & 1) = 1) AND ((c.cstate & 2) = 0) limit 200";
        $result = $this->db->query($sql)->getAll();
        while (!empty($result))
        {
            $this->load->library('BetCnName');
            $this->load->model('user_model');
            $this->load->library('mipush');
            foreach ($result as $val)
            {
                $money = ($val['payMoney'] / $val['issues']) * $val['chaseIssue'];
                if($money > 0)
                {
                    $time = strtotime($val['created']);
                    $vdatas = array(
                        '#MM#'  =>  date('m', $time), 
                        '#DD#'  =>  date('d', $time),
                        '#LID#' =>  BetCnName::getCnName($val['lid']),
                        '#MONEY#'  =>   $money,
                    );
                    $this->user_model->sendSms($val['uid'], $vdatas, 'chase_activity_complete', null, '127.0.0.1', '192');

                    // APP 消息推送
                    $pushData = array(
                        'type'      =>  'chase_activity_complete',
                        'uid'       =>  $val['uid'],
                        'lname'     =>  BetCnName::getCnName($val['lid']),
                        'orderId'   =>  $val['chaseId'],
                        'time'      =>  $val['created'],
                        'money'     =>  number_format(ParseUnit($money, 1), 2),
                    );
                    $this->mipush->index('user', $pushData);
                }
                $this->db->query("update cp_chase_manage set cstate = cstate | 2 where chaseId = ?", array($val['chaseId']));
            }
            
            $result = $this->db->query($sql)->getAll();
        }
    }
    
    /*
     * 功能：将insert select分离处理
     * 作者：huxm
     * */
    private function insert_select($tname, $datas, $fields)
    {
        $return = true;
        if(!empty($datas))
        {
            $s_data = array();
            $sql1 = "insert $tname(" . implode(',', $fields) . ") values";
            foreach ($datas as $data)
            {
                $s_str = '';
                foreach ($fields as $field)
                {
                    $s_str .= "'{$data[$field]}',";
                }
                $s_str = preg_replace('/,$/', '', $s_str);
                array_push($s_data, "($s_str)");
            }
            $sql1 .= implode(',', $s_data) . $this->onduplicate($fields, $fields);
            $return = $this->db->query($sql1);
        }
        
        return $return;
    }
}
