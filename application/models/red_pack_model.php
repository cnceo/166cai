<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/1/27
 * 修改时间: 13:58
 */
class Red_Pack_Model extends MY_Model
{
    const USER_OLD = 1;
    const USER_NEW = 2;

    const EVENT_LIST_ALL = 0;
    const EVENT_REGISTER = 1;
    const EVENT_CERTIFY = 2;
    const EVENT_RECHARGE = 3;
    const EVENT_BET = 4;

    const TYPE_SUNSHINE = 1;
    const TYPE_RECHARGE = 2;
    const TYPE_BET 		= 3;

    const C_TYPE_CERTIFY = 1;
    const C_TYPE_RECHARGE_TWO = 2;
    const C_TYPE_RECHARGE_FIVE = 3;
    const C_TYPE_RECHARGE_TEN = 4;
    const C_TYPE_RECHARGE_TWENTY = 5;
    const C_TYPE_BET = 6;

    const USE_RECHARGE = 1;
    const USE_BET = 2;

    const STATUS_RECEIVED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_USED = 2;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
    }

    public function send($userId, $userType)
    {
        $userExist = $this->db->query("SELECT 1 FROM cp_user_register
            WHERE id = ?", $userId)
            ->getOne();
        if ( ! $userExist)
        {
            return array(FALSE, '没有此用户', array());
        }

        switch ($userType)
        {
            case self::USER_OLD:
                //shouldn't reach here
                //has sent when attended the activity
                list($success, $msg, $data) = $this->sendOLdUserPack($userId);
                break;
            case self::USER_NEW:
                list($success, $msg, $data) = $this->sendNewUserPack($userId);
                break;
            default:
                list($success, $msg, $data) = array(FALSE, '请指定用户类型', array());
                break;
        }

        return array($success, $msg, $data);
    }

    private function sendOLdUserPack($userId)
    {
        return array(FALSE, '已在参加活动时发送', compact('userId'));
    }

    private function sendNewUserPack($userId)
    {
        $packTypeStr = implode(',', array(self::TYPE_BET));
        $redPacks = $this->db->query("SELECT ur.id userId, a.id activityId,
            al.platform_id platformId, al.channel_id channelId,
            al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
            FROM cp_user_register ur
            JOIN cp_activity_log al ON al.phone = ur.phone
            JOIN cp_activity a ON al.aid = a.id
            JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type IN ($packTypeStr)
            LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
            WHERE ur.id = ? AND rl.id IS NULL ", $userId)
            ->getAll();
        $valueAry = $this->composePackAry($redPacks);
        if (empty($valueAry))
        {
            return array(TRUE, '发送成功', array());
        }
        $this->db->trans_start();
        $res = $this->db->simple_query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid,
        		valid_start, valid_end, get_time, created)
        		VALUES (" . implode('), (', $valueAry) . ")");
        $phone = $this->db->query("select phone from cp_user_register where id=?", array($userId))->getOne();
        $res1 = $this->db->query("update cp_activity_log set uid = ? where phone = ?", array($userId, $phone));
        if ($res && $res1)
        {
        	$this->db->trans_complete();
            list($success, $msg) = array(TRUE, '发送成功');
        }
        else
        {
        	$this->db->trans_rollback();
            list($success, $msg) = array(FALSE, '发送失败');
        }

        return array($success, $msg, array());
    }

    private function composePackAry($packs)
    {
        $valueAry = array();
        foreach ($packs as $pack)
        {
            switch ($pack['packType'])
            {
                case self::TYPE_SUNSHINE:
                    $valid_start = date('Y-m-d H:i:s', strtotime(date('Y-m-d')));
                    $valid_end = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime('+5 year'))));
                    // 到期时间前推一秒
                    $valid_end = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($valid_end)));
                    $valueAry[] = implode(', ', array(
                        $pack['activityId'],
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        "'" . $valid_start . "'",
                        "'" . $valid_end . "'",
                        // 'NOW()',
                        // 'DATE_ADD(NOW(), INTERVAL 5 YEAR)',
                        "'" . $pack['attendTime'] . "'",
                        'NOW()',
                    ));
                    break;
                case self::TYPE_RECHARGE:
                case self::TYPE_BET:
                    $packParams = json_decode($pack['packParams'], TRUE);
                    $start_day = '+' . $packParams['start_day'] . ' day';
                    $startDay = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime($start_day))));
                    $end_day = '+' . $packParams['end_day'] . ' day';
                    $endDay = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime($end_day))));
                    // 到期时间前推一秒
                    $endDay = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($endDay)));
                    $valueAry[] = implode(', ', array(
                        $pack['activityId'],
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        // 'DATE_ADD(NOW(), INTERVAL ' . $packParams['start_day'] . ' DAY)',
                        // 'DATE_ADD(NOW(), INTERVAL ' . $packParams['end_day'] . ' DAY)',
                        "'" . $startDay . "'",
                        "'" . $endDay . "'",
                        "'" . $pack['attendTime'] . "'",
                        'NOW()',
                    ));
                    break;
                default:
                    break;
            }
        }

        return $valueAry;
    }

    public function fetch($userId, $eventType, $page, $step)
    {
        $userExist = $this->slave->query("SELECT 1 FROM cp_user_register
            WHERE id = ?", $userId)
            ->getOne();
        if ( ! $userExist)
        {
            return array(FALSE, '用户不存在', array());
        }

        switch ($eventType)
        {
            case self::EVENT_LIST_ALL:
                list($success, $msg, $data) = $this->fetchAllPack($userId, $page, $step);
                break;
            case self::EVENT_RECHARGE:
                list($success, $msg, $data) = $this->fetchRechargePack($userId, $page, $step);
                break;
            case self::EVENT_BET:
            	list($success, $msg, $data) = $this->fetchBetPack($userId, $page, $step);
            	break;
            default:
                list($success, $msg, $data) = array(FALSE, '请指定正确的事件类型', array());
                break;
        }

        return array($success, $msg, $data);
    }

    private function fetchAllPack($userId, $page, $step)
    {
        //todo need benchmark here
        //todo 'IN ?' not work, why?
        $sql = "SELECT rl.id, r.money, r.use_desc, r.use_params,
            r.refund_desc, rl.valid_start, rl.valid_end,
            if(rl.status < ? AND rl.valid_end < now(), 1, 0) is_expired,
            rl.status, rl.use_time
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id
            WHERE rl.uid = ? AND rl.delete_flag = 0
            ORDER BY is_expired, rl.status, rl.use_time DESC, r.p_type, rl.valid_end, r.money";
        $params = array(self::STATUS_USED, $userId);
        if ( ! empty($page) && ! empty($step))
        {
            $sql .= " LIMIT ?, ?";
            $params = array_merge($params, array(($page - 1) * $step, $step));
        }
        $redPacks = $this->slave->query($sql, $params)->getAll();

        return array(TRUE, '获取成功', $redPacks);
    }

    private function fetchRechargePack($userId, $page, $step)
    {
        $sql = "SELECT rl.id, r.money, r.use_desc, r.use_params,r.ismobile_used,
            r.refund_desc, rl.valid_start, rl.valid_end, rl.status, rl.use_time
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id AND r.p_type = ?
            WHERE rl.uid = ? AND rl.status = ? AND rl.valid_start <= now()
            AND rl.valid_end > now() AND rl.delete_flag = 0
            ORDER BY rl.status, rl.valid_end, r.money";
        $params = array(self::TYPE_RECHARGE, $userId, self::STATUS_ACTIVE);
        if ( ! empty($page) && ! empty($step))
        {
            $sql .= " LIMIT ?, ?";
            $params = array_merge($params, array(($page - 1) * $step, $step));
        }
        $redPacks = $this->slave->query($sql, $params)->getAll();

        return array(TRUE, '获取成功', $redPacks);
    }

    public function prepare($tradeNo, $userId, $scenario, $packIds)
    {
        switch ($scenario)
        {
            case self::USE_RECHARGE:
                list($success, $msg, $data) = $this->prepareRechargePack($tradeNo, $userId,
                    $packIds);
                break;
            case self::USE_BET:
                list($success, $msg, $data) = array(FALSE, '错误参数', array());
                break;
            default:
                list($success, $msg, $data) = array(FALSE, '错误参数', array());
                break;
        }

        return array($success, $msg, $data);
    }

    private function prepareRechargePack($tradeNo, $userId, $packIds)
    {
        $money = $this->db->query("SELECT money FROM cp_wallet_logs
            WHERE trade_no = ? AND uid = ?", array($tradeNo, $userId))
            ->getOne();
        if (empty($money))
        {
            return array(FALSE, '错误参数', array());
        }

        $packIdStr = implode(',', $packIds);
        $redPacks = $this->db->query("SELECT rl.id, r.money, r.use_params useParams
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id AND r.p_type = ?
            WHERE rl.uid = ? AND rl.id IN ($packIdStr) AND rl.status = ?
            AND rl.valid_start <= now() AND rl.valid_end > now() AND r.delete_flag = 0
            ORDER BY money DESC, valid_start ",
            array(self::TYPE_RECHARGE, $userId, self::STATUS_ACTIVE))
            ->getAll();
        $consumeIds = array();
        $realRecharge = $money;
        foreach ($redPacks as $pack)
        {
            $useParams = json_decode($pack['useParams'], TRUE);
            if ($realRecharge < $useParams['money_bar'])
            {
                continue;
            }
            $realRecharge -= $useParams['money_bar'];
            if ($realRecharge < 0)
            {
                break;
            }
            $consumeIds[] = $pack['id'];
        }

        if (empty($consumeIds))
        {
            return array(TRUE, '成功', array());
        }

        //shouldn't lock here
        $this->db->query("UPDATE cp_wallet_logs SET red_pack = ? WHERE trade_no = ?",
            array(implode(',', $consumeIds), $tradeNo));

        return array(TRUE, '成功', array());
    }

    public function consumeRechargePack($tradeNo, $userId)
    {
        $this->db->simple_query("BEGIN");
        $packIdStr = $this->db->query("SELECT red_pack
            FROM cp_wallet_logs
            WHERE trade_no = ? AND uid = ?", array($tradeNo, $userId))
            ->getOne();
        $this->db->query("UPDATE cp_redpack_log SET status = ? WHERE id IN ($packIdStr)",
            self::STATUS_USED);

        $this->db->simple_query("COMMIT");
    }

    public function checkBound($userId, $idCard)
    {
        $idCardCount = $this->db->query("SELECT count(DISTINCT i.uid)
                FROM cp_user_info i
                JOIN cp_redpack_log l ON l.uid = i.uid
                JOIN cp_redpack r ON r.id = l.rid
                WHERE i.id_card = ? AND r.p_type = ? AND l.uid <> ?
                    AND l.delete_flag = 0 AND r.delete_flag = 0",
            array($idCard, self::TYPE_BET, $userId))
            ->getOne();
        if ($idCardCount == 0)
        {
            return array(TRUE, '可以绑定', array());
        }
        else
        {
            return array(FALSE, '不可绑定', array());
        }
    }

    public function activatePack($userId)
    {
        $redPack = $this->db->query("SELECT group_concat(rl.id SEPARATOR ',') idStr,
            sum(r.money) money
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id AND r.p_type = ?
            WHERE rl.uid = ? AND rl.status = ? AND rl.valid_start <= now()
                AND rl.valid_end >= now() AND r.delete_flag = 0 AND rl.delete_flag = 0",
            array(self::TYPE_BET, $userId, self::STATUS_RECEIVED))
            ->getRow();
        if (empty($redPack) || empty($redPack['idStr']))
        {
            return array(FALSE, '没有红包', array());
        }

        $this->db->trans_start();
        $re1 = $this->db->query("UPDATE cp_redpack_log
            SET status = ?
            WHERE uid = ? AND delete_flag = 0",
            array(self::STATUS_ACTIVE, $userId));
        if ( ! $re1)
        {
            $this->db->trans_rollback();

            return array(FALSE, '激活失败', array());
        }

        $this->db->trans_complete();
        list($success, $msg) = array(TRUE, '成功激活');

        return array($success, $msg, array());
    }

    public function deleteOwnPack($activityId, $userId)
    {
        //todo composite index
        if ($this->db->simple_query("UPDATE cp_redpack_log SET delete_flag = 1
            WHERE uid = $userId AND aid = $activityId")
        )
        {
            list($success, $msg) = array(TRUE, '删除成功');
        }
        else
        {
            list($success, $msg) = array(FALSE, '删除失败');
        }

        return array($success, $msg, array());
    }

    //刷新钱包
    private function freshWallet($uid)
    {
    	$this->load->model('wallet_model');
    	$this->wallet_model->freshWallet($uid);
    }
    
    /**
     * 返回用户红包记录
     * @param unknown_type $cons
     * @param unknown_type $cpage
     * @param unknown_type $psize
     */
    public function getUserRedpacks($cons, $cpage, $psize)
    {
    	$where = " where 1 and a.uid=? and a.delete_flag=0";
    	if($cons['ctype'] == '1')
    	{
    		$where .= " and a.status in(0,1) and a.valid_end >= now()";
    		$orderBy = " ORDER BY a.status DESC, a.valid_start ASC,a.valid_end ASC, c.money_bar ASC, c.money DESC,a.id ASC";
    	}
    	else
    	{
    		$where .= " and (status in(2) or a.valid_end < now())";
    		$orderBy = "ORDER BY a.use_time DESC,a.valid_end DESC";
    	}
    	$sql = "select a.id, a.aid, a.rid, a.valid_start, a.valid_end, a.get_time,
    	a.status, a.use_time, a.created, a.remark, a.orderId,
    	c.p_type, c.money, c.p_name, c.use_desc, c.use_params, c.refund_desc,c.ismobile_used,c.c_name,c.c_type,c.money_bar 
    	from cp_redpack_log a
    	left join cp_redpack c on c.id = a.rid
    	{$where} {$orderBy}
    	LIMIT " . ($cpage - 1) * $psize . "," . $psize;
    	$totalSql = "select count(1) count from cp_redpack_log a left join cp_redpack c on c.id = a.rid {$where}";
    	$res ['datas'] = $this->slave->query($sql, array($cons['uid']))->getAll();
    	$res ['totals'] = $this->slave->query($totalSql, array($cons['uid']))->getOne();
    	return $res;
    }
	
    /**
     * 查询用户是否有红包
     * @param unknown_type $uid
     * @param unknown_type $aid
     */
    public function hasRedpack($uid, $aid)
    {
    	return $this->db->query("SELECT 1 FROM cp_redpack_log
    	WHERE uid = ? AND aid = ? and status=1 and delete_flag=0", array($uid, $aid))->getOne();
    }
    
    /**
     * 返回用户有效的购彩红包
     * @param int $userId	用户uid
     * @return unknown
     */
    public function fetchBetPack($userId, $page = 1, $step = 30, $ctype = false)
    {
    	$now = date('Y-m-d H:i:s');
    	$sql = "SELECT rl.id, r.money, r.use_desc, rl.valid_start, rl.valid_end, rl. STATUS,
    	rl.use_time, r.c_type, r.c_name, r.money_bar, r.ismobile_used, r.p_name 
    	FROM cp_redpack_log rl 
    	JOIN cp_redpack r ON rl.rid = r.id AND r.p_type = ? 
    	WHERE rl.uid = ? AND rl. STATUS = ? AND rl.valid_start <= ? 
    	AND rl.valid_end > ? AND rl.delete_flag = 0 
    	ORDER BY r.money DESC, rl.valid_end ASC";
        if($ctype)
        {
            $sql .= " ,r.c_type DESC";
        }
    	$params = array(self::TYPE_BET, $userId, self::STATUS_ACTIVE, $now, $now);
    	if ( ! empty($page) && ! empty($step))
    	{
    		$sql .= " LIMIT ?, ?";
    		$params = array_merge($params, array(($page - 1) * $step, $step));
    	}
    	$redPacks = $this->slave->query($sql, $params)->getAll();
    	
    	return array(TRUE, '获取成功', $redPacks);
    }
    
    /**
     * 根据id查询红包信息
     * @param unknown_type $uid
     * @param unknown_type $redpackId
     */
    public function getRedpackById($uid, $redpackId)
    {
    	$sql = "SELECT rl.id, r.money, r.use_desc, rl.valid_start, rl.valid_end, rl. STATUS,
    	rl.use_time, r.c_type, r.c_name, r.money_bar, r.ismobile_used, r.p_name
    	FROM cp_redpack_log rl
    	JOIN cp_redpack r ON rl.rid = r.id AND r.p_type = ?
    	WHERE rl.uid = ? and rl.id=? AND rl. STATUS = ? AND rl.delete_flag = 0";
    	$params = array(self::TYPE_BET, $uid, $redpackId, self::STATUS_ACTIVE);
    	return $this->db->query($sql, $params)->getRow();
    }
    /**
     * [checkRealName 验证实名]
     * @author LiKangJian 2017-07-19
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function checkRealName($uid)
    {
        $sql = "SELECT uid FROM  cp_user_info  WHERE uid = ? and userStatus = 0 and real_name !='' and id_card !='' ";
        $uid = $this->db->query($sql,array($uid))->getOne();
        $tag = false;
        if(!empty($uid))
        {
            $tag = true;
        }
        return $tag;
    }
    
    public function countRedpackByTime($start, $end)
    {
        $sql = "select count(*) as count from cp_redpack_log where get_time>=? and get_time<?";
        return $this->db->query($sql, array($start, $end))->getRow();
    }
}