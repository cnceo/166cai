<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 定制跟单 - 订单 - 模型层
 */
class Follow_Order_Model extends MY_Model
{
	public function __construct() 
	{
		parent::__construct();
	}

	// 跟单订单状态
	public $status = array(
		'create' 			=> 	0, 	// 预支付创建
		'following' 		=> 	1, 	// 跟单进行中
		'followed' 			=> 	2, 	// 跟单完成
		'revoke_by_user'	=> 	3, 	// 用户撤单
		'revoke_by_system'	=>	4,	// 系统撤单
	);

	public function getStatus()
	{
		return $this->status;
	}

	// 即时及预支付跟单创建
	public function createFollowOrder($params = array(), $trans = true)
	{
		/*
    	$params = array(
    		'uid'				=>	'',
    		'puid'				=>	'',
    		'lid'				=>	'',
    		'payType'			=>	'',
    		'followType'		=>	'',
    		'totalMoney'		=>	'',
    		'buyMoney'			=>	'',
    		'buyMoneyRate'		=>	'',
    		'buyMaxMoney'		=>	'',
    		'followTotalTimes'	=>	'',
    		'buyPlatform'		=>	'',
    		'channel'			=>	'',
    	);
    	*/

    	// 参数检查
    	$checkRes = $this->checkFollowParams($params);
    	if(!$checkRes['status'])
    	{
    		$result = array(
    			'code' 	=>	$checkRes['code'],
				'msg' 	=> 	$checkRes['msg'],
				'data'	=> 	$params
    		);
    		return $result;
    	}

    	// 事务开始
    	if($trans) $this->db->trans_start();

    	// 用户信息锁表
    	$this->load->model('follow_wallet_model');
		$uinfo = $this->follow_wallet_model->getUserMoney($params['uid']);

		if(empty($uinfo))
		{
			if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	400,
				'msg' 	=> 	'用户信息获取失败',
				'data'	=> 	$params
    		);
    		return $result;
		}

		// 检查进行中的方案
		$following = $this->checkFollowingOrder($params['puid'], $params['uid'], $params['lid']);
		if($following > 0)
		{
			if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	401,
				'msg' 	=> 	'您已定制发起人的方案，换个彩种试试吧',
				'data'	=> 	$params
    		);
    		return $result;
		}

		// 方案入库
		$currentTime = date('Y-m-d H:i:s');
    	$followInfo = array(
			'followId'			=>	$this->tools->getIncNum('UNIQUE_KEY'),
			'uid'				=>	$params['uid'],
			'puid'				=>	$params['puid'],
			'lid'				=>	$params['lid'],
			'payType' 			=> 	$params['payType'] ? 1 : 0,
			'followType'		=>	$params['followType'] ? 1 : 0,
			'totalMoney'		=>	ParseUnit($params['totalMoney']),
			'blockMoney'		=>	$params['payType'] ? 0 : ParseUnit($params['totalMoney']),
			'buyMoney'			=>	($params['followType'] == '0' && $params['buyMoney'] > 0) ? ParseUnit($params['buyMoney']) : 0,
			'buyMoneyRate'		=>	($params['followType'] == '1' && $params['buyMoneyRate'] > 0) ? $params['buyMoneyRate'] : 0,
			'buyMaxMoney'		=>	($params['followType'] == '1' && $params['buyMaxMoney'] > 0) ? ParseUnit($params['buyMaxMoney']) : 0,
			'followTotalTimes'	=>	$params['followTotalTimes'],
			'status'			=>	$params['payType'] ? $this->status['following'] : $this->status['create'],
			'effectTime'		=>	$params['payType'] ? $currentTime : '0000-00-00 00:00:00',
			'endTime'			=>	$params['payType'] ? date('Y-m-d H:i:s', strtotime('+60 days')) : '0000-00-00 00:00:00',
			'buyPlatform'		=>	$params['buyPlatform'],
			'channel' 			=> 	$params['channel'],
			'created'			=>	$currentTime,
		);

    	$followRes = $this->saveFollowOrder($followInfo);

    	// 红人跟单统计
    	$plannerInfo = array(
    		'uid'	=>	$params['puid'],
    		'lid'	=>	$params['lid'],
    	);

    	if($params['payType'] == '1')
    	{
    		// 实时付款 更新跟单红人
    		$recoredRes = $this->recoredPlannerInfo($plannerInfo, 1, false);
    	}
    	else
    	{
    		// 预付款 检查跟单红人上限
    		$this->load->model('united_order_model');
			$plannerInfo = $this->united_order_model->checkParams($plannerInfo);
    		$plannerData = $this->getPlannerInfo($plannerInfo['uid'], $plannerInfo['lid']);

    		if(empty($plannerData) || $plannerData['isFollowNum'] >= 2000)
    		{
    			if($trans) $this->db->trans_rollback();
				$result = array(
	    			'code' 	=>	402,
					'msg' 	=> 	'定制人数已达上限，换个彩种试试吧',
					'data'	=> 	$params
	    		);
	    		return $result;
    		}
    		else
    		{
    			$recoredRes = TRUE;
    		}	
    	}
    	
    	if($followRes && $recoredRes)
    	{
    		if($trans) $this->db->trans_complete();
			$result = array(
    			'code' 	=>	200,
				'msg' 	=> 	'跟单方案创建成功',
				'data'	=> 	$followInfo
    		);
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();
			$result = array(
    			'code' 	=>	402,
				'msg' 	=> 	'定制人数已达上限，换个彩种试试吧',
				'data'	=> 	$params
    		);
    	}
    	return $result;
	}

	// 保存跟单方案信息
	public function saveFollowOrder($followData)
	{
		$fields = array_keys($followData);
		$sql = "insert cp_united_follow_orders(" . implode(',', $fields) . ")
		values(". implode(',', array_map(array($this, 'maps'), $fields)) .")";
		return $this->db->query($sql, $followData);
	}

	// 检查进行中方案
	public function checkFollowingOrder($puid, $uid, $lid)
	{
		$sql = "SELECT count(*) FROM cp_united_follow_orders WHERE puid = ? AND uid = ? AND lid = ? AND status = {$this->status['following']}";
		return $this->db->query($sql, array($puid, $uid, $lid))->getOne();
	}

	// 合买红人检查
	public function recoredPlannerInfo($info, $flag = 1, $trans = true)
	{
		// 胜负彩/任九 排列三/五 合并彩种
		$this->load->model('united_order_model');
		$info = $this->united_order_model->checkParams($info);

		if($trans) $this->db->trans_start();

		// 行锁跟单红人
		$plannerInfo = $this->getPlannerInfo($info['uid'], $info['lid']);
		
		if(empty($plannerInfo))
		{
			if($trans) $this->db->trans_rollback();
			return FALSE;
		}

		if($flag)
		{
			$updateSql = "UPDATE cp_united_planner SET isFollowNum = isFollowNum + 1, followTimes = followTimes + 1 WHERE uid = ? AND lid = ? AND isFollowNum < 2000";
		}
		else
		{
			$updateSql = "UPDATE cp_united_planner SET isFollowNum = isFollowNum - 1 WHERE uid = ? AND lid = ? AND isFollowNum > 0";
		}
		$this->db->query($updateSql, array($info['uid'], $info['lid']));
		$handleRes = $this->db->affected_rows();

		if($handleRes)
		{
			if($trans) $this->db->trans_complete();
			return TRUE;
		}
		else
		{
			if($trans) $this->db->trans_rollback();
			return FALSE;
		}	
	}

	// 提交参数检查
	public function checkFollowParams($params = array())
	{
		$money = 0;
		// 必要参数检查
		$checkParams = array('uid', 'puid', 'lid', 'payType', 'followType', 'totalMoney', 'followTotalTimes');
		$check = TRUE;
		foreach ($checkParams as $val) 
		{
			if(!isset($params[$val]) || $params[$val] === '')
			{
				$check = FALSE;
				break;
			}
		}
		if(!$check)
		{
			$result = array(
				'status'=>	FALSE,
    			'code' 	=>	300,
				'msg' 	=> 	'跟单方案缺少必要参数',
    		);
    		return $result;
		}

		// 扣款方式检查
		if(!in_array($params['payType'], array('0', '1')) || !in_array($params['followType'], array('0', '1')))
		{
			$result = array(
				'status'=>	FALSE,
    			'code' 	=>	301,
				'msg' 	=> 	'定制跟单扣款方式错误',
    		);
    		return $result;
		}
		// 定制次数 最低1次 最高100次
		if(!preg_match("/^[1-9]\d*$/", $params['followTotalTimes']) || $params['followTotalTimes'] < 1 || $params['followTotalTimes'] > 100)
		{
			$result = array(
				'status'=>	FALSE,
    			'code' 	=>	302,
				'msg' 	=> 	'定制跟单定制次数最低1次最高100次',
    		);
    		return $result;
		}
		// 定制方式-按固定金额 最低1元 最高10000元
		if($params['followType'] == '0')
		{
			if(!preg_match("/^[1-9]\d*$/", $params['buyMoney']) || $params['buyMoney'] < 1 || $params['buyMoney'] > 10000)
			{
				$result = array(
					'status'=>	FALSE,
	    			'code' 	=>	303,
					'msg' 	=> 	'定制跟单固定金额格式错误',
	    		);
	    		return $result;
			}
			// 总金额
			$money = $params['buyMoney'] * $params['followTotalTimes'];
		}
		// 定制方式-按百分比 最低1 最高100 最低1元 最高10000元
		if($params['followType'] == '1')
		{
			if(!preg_match("/^[1-9]\d*$/", $params['buyMoneyRate']) || $params['buyMoneyRate'] < 1 || $params['buyMoneyRate'] > 100)
			{
				$result = array(
					'status'=>	FALSE,
	    			'code' 	=>	304,
					'msg' 	=> 	'定制跟单单次认购比例格式错误',
	    		);
	    		return $result;
			}

			if(!preg_match("/^[1-9]\d*$/", $params['buyMaxMoney']) || $params['buyMaxMoney'] < 1 || $params['buyMaxMoney'] > 10000)
			{
				$result = array(
					'status'=>	FALSE,
	    			'code' 	=>	305,
					'msg' 	=> 	'定制跟单单次认购最大金额格式错误',
	    		);
	    		return $result;
			}
			// 总金额
			$money = $params['buyMaxMoney'] * $params['followTotalTimes'];
		}
		// 总金额检查
		if(!preg_match("/^[1-9]\d*$/", $params['totalMoney']) || $money != $params['totalMoney'])
		{
			$result = array(
				'status'=>	FALSE,
    			'code' 	=>	306,
				'msg' 	=> 	'定制跟单支付金额错误',
    		);
    		return $result;
		}

		$result = array(
			'status'=>	TRUE,
			'code' 	=>	200,
			'msg' 	=> 	'参数校验正确',
		);
		return $result;
	}

	// 查询跟单记录表指定订单
	public function getFollowOrderDetail($followId)
	{
		$sql = "SELECT followId, trade_no, uid, puid, lid, payType, followType, totalMoney, blockMoney, buyMoney, buyMoneyRate, buyMaxMoney, followTimes, followTotalTimes, status, lastFollowTime, totalMargin, effectTime, buyPlatform, cstate, created, modified FROM cp_united_follow_orders WHERE followId = ? for update";
		return $this->db->query($sql, array($followId))->getRow();
	}

	// 查询可跟单的定制方案
	public function getFollowOrders($puid, $lid)
	{
		$sql = "SELECT followId, trade_no, uid, puid, lid, payType, followType, totalMoney, blockMoney, buyMoney, buyMoneyRate, buyMaxMoney, followTimes, followTotalTimes, status, lastFollowTime, totalMargin, effectTime, cstate, buyPlatform, created, modified 
        FROM cp_united_follow_orders 
        WHERE puid = ? AND lid = ? AND status = {$this->status['following']} 
        AND followTimes < followTotalTimes ORDER BY effectTime ASC LIMIT 2000";
		return $this->db->query($sql, array($puid, $lid))->getAll();
	}

	public function getJoinOrderDetail($followId, $hmOrderId)
	{
		$sql = "SELECT followId, trade_no, hmOrderId, subscribeId, uid, puid, buyMoney, refundMoney, cstate FROM cp_united_follow_join WHERE followId = ? AND hmOrderId = ?";
		return $this->db->query($sql, array($followId, $hmOrderId))->getRow();
	}

	// 新增跟单记录
	public function saveJoinOrder($followData)
	{
		$fields = array_keys($followData);
		$sql = "insert cp_united_follow_join(" . implode(',', $fields) . ", created)
		values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
		return $this->db->query($sql, $followData);
	}

	public function updateUnitedFollowed($unitedData)
	{
		$this->db->query("UPDATE cp_united_orders SET follow_cstate = (follow_cstate | 1), follow_time = now() WHERE orderId = ? AND uid = ?", array($unitedData['orderId'], $unitedData['uid']));
	}

	public function getAllFollowOrders($followId)
    {
        $sql="SELECT j.issue,j.money,j.buyMoney,j.status,j.margin,j.created,j.orderId,j.my_status from cp_united_follow_join f LEFT JOIN cp_united_join j on
              f.subscribeId=j.subscribeId where f.followId=? ORDER BY f.created";
        return $this->db->query($sql, array($followId))->getAll();
    }
        
    public function getAllOrders($cons, $cpage, $psize)
    {
        $table = 'cp_united_follow_orders';
        $conStr = ' and m.uid = ? and m.created >= ? and m.created <= ?';
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
        $nsql = "select count(*) total from " . $table . " as m where 1 " . $conStr;
        $sql = "select m.followId, m.lid, m.created, m.status,m.payType,m.followType,m.totalMoney,m.buyMoney,m.buyMoneyRate,m.followTimes,m.followTotalTimes,m.effectTime,m.totalMargin,m.my_status,o.uname,o.uid 
        from " . $table . " as m 
        JOIN cp_user AS o ON o.uid = m.puid 
        where 1 " . $conStr . " 
        ORDER BY m.created DESC 
        limit " . ($cpage - 1) * $psize . "," . $psize;
        $res ['totals'] = $this->slave->query ( $nsql, $cons )->getRow ();
        $res ['datas'] = $this->slave->query ( $sql, $cons )->getAll ();
        return $res;
    }

    // 跟单方案撤单
    public function cancelFollowOrder($uid, $followId, $cancelType = 0, $trans = true)
    {
    	$cancelTypes = array(
			0	=>	'revoke_by_user',
			1 	=> 	'revoke_by_system'
		);

		$result = array(
			'code'	=>	400,
			'msg' 	=>	'该跟单方案撤单条件不满足',
			'data'	=>	''
		);

		$ctype = $this->status[$cancelTypes[$cancelType]];
		if(empty($ctype))
		{
			$result = array(
				'code'	=>	400,
				'msg' 	=>	'该跟单方案撤单类型错误',
				'data'	=>	''
			);
			return $result;
		}

		// 事务开始
    	if($trans) $this->db->trans_start();

    	// 行锁
    	$followInfo = $this->getFollowOrderDetail($followId);

    	if(empty($followInfo) || $followInfo['uid'] != $uid || $followInfo['status'] != $this->status['following'])
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
				'code'	=>	400,
				'msg' 	=>	'该跟单方案撤单条件不满足',
				'data'	=>	''
			);
			return $result;
    	}

    	// 预付款退款处理
    	if($followInfo['payType'] == '0')
    	{
    		$this->load->model('follow_wallet_model');
    		$refundRes = $this->follow_wallet_model->refundFollowOrder($followInfo, FALSE);
			$handleRes = $refundRes['status'];
    	}
    	else
    	{
    		$handleRes = TRUE;
    	}

    	// 更新跟单状态
    	$this->db->query("UPDATE cp_united_follow_orders SET blockMoney = 0, status = ?,  my_status = (if(completeTimes = followTimes, 1, 0)) WHERE followId = ? AND uid = ? AND status = {$this->status['following']}", array($ctype, $followInfo['followId'], $followInfo['uid']));
    	$followRes = $this->db->affected_rows();

    	// 更新跟单红人统计
    	$plannerInfo = array(
    		'uid'	=>	$followInfo['puid'],
    		'lid'	=>	$followInfo['lid'],
    	);
    	$recoredRes = $this->recoredPlannerInfo($plannerInfo, 0, false);

    	if($handleRes && $followRes && $recoredRes)
    	{
    		if($trans) $this->db->trans_complete();
    		$result = array(
				'code'	=>	200,
				'msg' 	=>	'跟单方案撤单成功',
				'data'	=>	''
			);
    	}
    	else
    	{
    		if($trans) $this->db->trans_rollback();
    		$result = array(
				'code'	=>	400,
				'msg' 	=>	'跟单方案撤单失败',
				'data'	=>	''
			);
    	}
    	return $result;
    }

    // 获取过期跟单方案
    public function getExpiredOrders()
    {
    	$sql = "SELECT followId, uid, puid, lid, payType FROM cp_united_follow_orders WHERE endTime >= date_sub(now(), interval 1 day) AND endTime <= now() AND status = {$this->status['following']} ORDER BY endTime ASC LIMIT 300";
		$info = $this->db->query($sql)->getAll();
		return $info;
    }

    // 获取发送跟单完成短信方案
    public function getMessageOrder()
    {
    	$sql = "SELECT f.followId, f.uid, f.puid, f.lid, f.payType, f.created, i.msg_send FROM cp_united_follow_orders f left join cp_user_info i on f.uid=i.uid WHERE f.modified > date_sub(now(), interval 30 minute) AND ((f.followTimes = followTotalTimes) OR (f.status = {$this->status['revoke_by_user']}) OR (f.status = {$this->status['revoke_by_system']})) AND (f.cstate & 1 = 0) ORDER BY f.followId ASC LIMIT 300";
    	$info = $this->db->query($sql)->getAll();
		return $info;
    }

    // 发送短信
    public function handleMessage($orderInfo)
    {
    	$this->load->library('BetCnName');
    	$this->load->model('user_model');
		$position = $this->config->item('POSITION');
		
		$msgData = array(
			'#LID#' 	=> 	BetCnName::getCnName($orderInfo['lid']),
			'time' 		=> 	$orderInfo['created']
		);
                if(($orderInfo['msg_send'] & 16) == 0){
		    $this->user_model->sendSms($orderInfo['uid'], $msgData, 'united_follow_complete', null, '127.0.0.1', $position['united_follow_complete']);
                }
		// 更新短信状态位
		$this->db->query("UPDATE cp_united_follow_orders SET cstate = cstate | 1 WHERE followId = ? AND (cstate & 1 = 0)", array($orderInfo['followId']));
    }
    
    /**
     * 获取用户正在进行的跟单
     * @param type $uid
     * @return type
     */
    public function getHasGendan($uid)
    {
        $sql = "select puid,lid,followId from cp_united_follow_orders where uid=? and status=1 and my_status=0";
        return $this->db->query($sql, array($uid))->getAll();
    }
    
    /**
     * 分页查询进行跟单的用户列表
     * @param int $uid
     * @param int $lid
     * @param int $offset
     * @param int $num
     * @return array
     */
    public function gendanList($uid, $lid, $offset, $num)
    {
        $sql = "select u.uname,u.uid,f.effectTime,f.followType,f.buyMoney,f.buyMoneyRate from cp_united_follow_orders f left join cp_user u on u.uid=f.uid where f.puid=? and f.lid=? and f.status=1 and f.my_status=0 order by f.effectTime desc limit " . $offset . "," . $num;
        $countsql = "select count(*) as count from cp_united_follow_orders where puid=? and lid=? and status=1 and my_status=0";
        if ($lid == 33) {
            $sql = "select u.uname,u.uid,f.effectTime,f.followType,f.buyMoney,f.buyMoneyRate from cp_united_follow_orders f left join cp_user u on u.uid=f.uid where f.puid=? and f.lid in ? and f.status=1 and f.my_status=0 order by f.effectTime desc limit " . $offset . "," . $num;
            $countsql = "select count(*) as count from cp_united_follow_orders where puid=? and lid in ? and status=1 and my_status=0";
            $lid = array(33, 35);
        }
        if ($lid == 11) {
            $sql = "select u.uname,u.uid,f.effectTime,f.followType,f.buyMoney,f.buyMoneyRate from cp_united_follow_orders f left join cp_user u on u.uid=f.uid where f.puid=? and f.lid in ? and f.status=1 and f.my_status=0 order by f.effectTime desc limit " . $offset . "," . $num;
            $countsql = "select count(*) as count from cp_united_follow_orders where puid=? and lid in ? and status=1 and my_status=0";
            $lid = array(11, 19);
        }
        $users = $this->db->query($sql, array($uid, $lid))->getAll();
        $count = $this->db->query($countsql, array($uid, $lid))->getRow();
        return array($users, $count);
    }    

    // 一小时内为处理的跟单认购订单
    public function getFollowJoinInfo()
    {
    	$sql = "SELECT j.orderId, j.subscribeId, j.lid, j.margin, j.cstate FROM cp_united_join AS j JOIN cp_united_orders AS u ON j.orderId = u.orderId WHERE j.modified > date_sub(now(), interval 1 hour) AND j.orderType = 2 AND j.subOrderType = 1 AND (j.cstate & 512 = 0) AND (j.status in (610, 620, 1000) OR (j.status = 600 AND u.refund_time > '0000-00-00 00:00:00') OR (j.status = 2000 AND u.sendprize_time > '0000-00-00 00:00:00')) ORDER BY j.subscribeId ASC LIMIT 500";
    	return $this->db->query($sql)->getAll();
    }

    // 同步更新跟单子订单状态
    public function handleCompleteOrder($joinInfo)
    {
    	// 更新 my_status
    	$this->db->query("UPDATE cp_united_follow_orders AS f LEFT JOIN cp_united_follow_join AS j ON f.followId = j.followId SET f.completeTimes = f.completeTimes + 1, f.totalMargin = f.totalMargin + ?, f.my_status = (if(((f.completeTimes + 1) = f.followTimes AND f.status IN ('{$this->status[followed]}', '{$this->status[revoke_by_user]}', '{$this->status[revoke_by_system]}')), 1, 0)) WHERE j.subscribeId = ? AND f.completeTimes <= f.followTimes", array($joinInfo['margin'], $joinInfo['subscribeId']));

    	// 更新cp_united_join跟单同步状态
    	$this->db->query("UPDATE cp_united_join SET cstate = (cstate | 512) WHERE orderId = ? AND subscribeId = ?", array($joinInfo['orderId'], $joinInfo['subscribeId']));
    }
    
    
    /**
     * 检查能否跟单
     * @param int $uid
     * @param int $puid
     * @param int $lid
     * @return array
     */
    public function checkHasGendan($uid, $puid, $lid)
    {
        $sql = "SELECT count(*) FROM cp_united_follow_orders WHERE uid = ? AND lid = ? and puid=? AND status = {$this->status['following']}";
        $count = $this->db->query($sql, array($uid, $lid, $puid))->getOne();
        if ($count > 0) {
            return array(
                'code' => 1,
                'msg' => '已跟单',
                'data' => ''
            );
        }
        if ($lid == 19) {
            $lid = 11;
        }
        if ($lid == 35) {
            $lid = 33;
        }
        $sql = "select isFollowNum from cp_united_planner where uid=? and lid=?";
        $num = $this->db->query($sql, array($puid, $lid))->getRow();
        if ($num['isFollowNum'] >= 2000) {
            return array(
                'code' => 2,
                'msg' => '跟单人数已满',
                'data' => ''
            );            
        }
        return array(
            'code' => 200,
            'msg' => 'sucess',
            'data' => ''
        );              
    }

    // 跟单未及时处理报警
    public function handleFollowWarning()
    {
    	$sql = "SELECT o.orderId, o.lid, o.issue, o.follow_time, u.uname FROM cp_united_orders AS o LEFT JOIN cp_user AS u ON o.uid = u.uid WHERE o.pay_time > '0000-00-00 00:00:00' AND (o.follow_cstate & 2 = 0) AND ((o.pay_time <= DATE_SUB(NOW(),INTERVAL 10 SECOND) AND (o.follow_cstate & 1 = 0)) OR (o.pay_time <= DATE_SUB(o.follow_time, INTERVAL 10 SECOND) AND o.follow_time > '0000-00-00 00:00:00')) ORDER BY o.orderId DESC";
    	$orders = $this->db->query($sql)->getAll();

    	if(!empty($orders))
    	{
    		$this->load->library('BetCnName');
    		foreach ($orders as $order) 
    		{
    			if($order['follow_time'] > '0000-00-00 00:00:00')
    			{
    				$msg = "启动慢";
    			}
    			else
    			{
    				$msg = "未及时启动";
    			}
    			$title = '跟单脚本' . $msg . '报警';
    			$content = $order['uname'] . '，';
    			$content .= BetCnName::getCnName($order['lid']);
    			$content .= '第' . $order['issue'] . '期，';
    			$content .= 'hm' . $order['orderId'] . '，' ;
    			$content .= '跟单脚本';
    			$content .= $msg; 
    			$content .= '，请尽快处理';
    			$this->insertAlert($title, $content);
    			$this->updateAlert($order['orderId']);
    		}
    	}
    }

    // 报警记录
    public function insertAlert($title, $content)
    {
        $sql = "INSERT INTO cp_alert_log
        (ctype, title,content,created) VALUES ('25', ?, ?, NOW())";
        $this->db->query($sql, array($title, $content));
    }

    // 更新报警状态
    public function updateAlert($orderId)
    {
        $this->db->query("UPDATE cp_united_orders SET follow_cstate = (follow_cstate | 2) WHERE orderId = ?", array($orderId));
    }
        
    // 查询跟单记录表指定订单
    public function followOrderDetail($followId, $uid)
    {
        $sql = "SELECT followId, trade_no, uid, puid, lid, payType, followType, totalMoney, blockMoney, buyMoney, buyMoneyRate, buyMaxMoney, followTimes, followTotalTimes, status, lastFollowTime, totalMargin, effectTime, buyPlatform, cstate, created, my_status, modified FROM cp_united_follow_orders WHERE followId = ?  and uid = ? for update";
        return $this->db->query($sql, array($followId, $uid))->getRow();
    }    

    public function getPlannerInfo($uid, $lid)
    {
    	$sql = "SELECT id, uid, isFollowNum FROM cp_united_planner WHERE uid = ? AND lid = ? for update";
		return $this->db->query($sql, array($uid, $lid))->getRow();
    }
    
    public function getOrders($cons, $cpage, $psize)
    {
        $table = 'cp_united_follow_orders';
        if (!isset($cons['lid'])) {
            $conStr = ' and m.uid = ? and m.created >= ? and m.created <= ? ';
        }else{
            if ($cons['lid'] > 0 && $cons['lid'] != 11 && $cons['lid'] != 33) {
                $conStr = ' and m.uid = ? and m.created >= ? and m.created <= ? and m.lid =? ';
            }
            if ($cons['lid'] == 11 || $cons['lid'] == 33) {
                $conStr = ' and m.uid = ? and m.created >= ? and m.created <= ? and m.lid in ? ';
                $lids = array(11 => array(11, 19), 33 => array(33, 35));
                $cons['lid'] = $lids[$cons['lid']];
            }
        }
        $sql = "select m.followId, m.lid, m.status,m.followTimes,m.followTotalTimes,m.my_status,m.totalMargin,m.effectTime,o.uname,o.uid 
        from " . $table . " as m 
        JOIN cp_user AS o ON o.uid = m.puid 
        where 1 " . $conStr . " 
        and status > 0 ORDER BY m.created DESC 
        limit " . ($cpage - 1) * $psize . "," . $psize;
        $res = $this->db->query ( $sql, $cons )->getAll ();
        return $res;
    }
    
    public function getAllCanGendan($uid, $puid ,$lids)
    {
        $sql = "select lid from cp_united_follow_orders WHERE uid = ? AND lid in ? and puid=? AND status = {$this->status['following']}";
        $hasGendans = $this->slave->query($sql, array($uid, $lids, $puid))->getAll();
        $gendanLid = array();
        foreach ($hasGendans as $hasGendan) 
        {
            $gendanLid[] = $hasGendan['lid'];
        }
        // 检查发起人定制资格
        $sql = "select lid from cp_united_planner where uid=? and isFollowNum<2000 and allTimes>0 and lid in ? order by bonus desc";
        $counts = $this->slave->query($sql, array($puid, $lids))->getAll();
        $followed = FALSE;
        $gendanLids = array();
        if(!empty($counts))
        {
        	foreach($counts as $count) 
	        {
	            if($count['lid'] == 11) 
	            {
	                $gendanLids[] = 19;
	            }
	            if($count['lid'] == 33) 
	            {
	                $gendanLids[] = 35;
	            }
	            $gendanLids[] = $count['lid'];
	        }
	        $followed = TRUE;
        }
        
        $alllids = array_diff($gendanLids, $gendanLid);
        return array('lids' => $alllids, 'followed' => $followed);
    }
}
