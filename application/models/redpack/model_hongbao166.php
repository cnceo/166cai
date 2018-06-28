<?php
include_once 'model_hongbao.php';
class Model_Hongbao166 extends Model_Hongbao
{

	public function attend($activityId, $phone, $platformId, $channelId)
    {
        if ($this->hasAttend($activityId, $phone)) return array(FALSE, '已参加过该活动', array());
        if ($this->hasAttend(1, $phone)) return array(FALSE, '您已参加188元红包活动', array());

        $activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime 
        		FROM cp_activity WHERE id = ? AND delete_flag = 0", $activityId)->getRow();
        if (empty($activity)) return array(FALSE, '活动已结束', array());

        $this->load->helper('date');
        $nowTime = now();
        if (mysql_to_unix($activity['startTime']) > $nowTime) return array(FALSE, '活动尚未开始', array());
        if (mysql_to_unix($activity['endTime']) < $nowTime) return array(FALSE, '活动已结束', array());

        $this->db->trans_start();
        $re1 = $this->db->query("INSERT cp_activity_log (aid, phone, platform_id, channel_id, created) VALUES (?, ?, ?, ?, NOW())", array($activityId, $phone, $platformId, $channelId));
        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }

        $userId = $this->getUserIdByPhone($phone);
        if (empty($userId))
        {
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array());
        }
        
        if ($this->hasAttendByUid(1, $userId)) return array(FALSE, '您已参加188元红包活动', array());

        $this->db->query("UPDATE cp_activity_log SET uid = ? WHERE phone = ?", array($userId, $phone));

        $rechargePacks = $this->db->query("SELECT ur.id userId, a.id activityId,
            al.platform_id platformId, al.channel_id channelId,
            al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
            FROM cp_user_register ur
            JOIN cp_activity_log al ON al.phone = ur.phone
            JOIN cp_activity a ON al.aid = a.id AND a.start_time <= now()
                AND a.end_time >= now() AND a.delete_flag = 0
            JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type in ? AND rp.delete_flag = 0
            LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
            WHERE ur.id = ? AND rl.id IS NULL ", array(array(self::TYPE_RECHARGE, self::TYPE_BET), $userId))
            /**
             * @see Red_Pack_Model::TYPE_RECHARGE
             */
            ->getAll();
        $valueAry = $this->composePackAry($rechargePacks);
        if (empty($valueAry))
        {
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array());
        }

        $re2 = $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid,
            valid_start, valid_end, get_time, status, created)
            VALUES (" . implode('), (', $valueAry) . ")");
        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }

        $idCard = $this->db->query("SELECT id_card FROM cp_user_info WHERE uid = ?", $userId) ->getOne();
        if (empty($idCard))
        {
            /**
             * @see Red_Pack_Model::STATUS_RECEIVED
             */
            $packStatus = 0;
            $idCardCount = 0;
        }
        else
        {
            /**
             * @see Red_Pack_Model::STATUS_ACTIVE
             */
            $packStatus = 1;
            /**
             * @see Red_Pack_Model::TYPE_SUNSHINE
             */
            //todo performance?
            $idCardCount = $this->db->query("SELECT count(DISTINCT i.uid)
                FROM cp_user_info i
                JOIN cp_redpack_log l ON l.uid = i.uid
                JOIN cp_redpack r ON r.id = l.rid
                WHERE i.id_card = ? AND r.p_type = ? AND l.delete_flag = 0 AND r.delete_flag = 0",
                array($idCard, 3))
                ->getOne();
        }
        
        if ($idCardCount == 0)
        {

            $re3 = $this->db->query("UPDATE cp_redpack_log SET status = ? WHERE uid = ? AND aid = ?", array($packStatus, $userId, $activityId));
            if ( ! $re3)
            {
                $this->db->trans_rollback();
                return array(FALSE, '激活失败', array());
            }
        }
        else
        {
            $this->db->trans_rollback();
            return array(FALSE, '身份证号已领取过红包', array());
        }

        $this->freshWallet($userId);
        $this->db->trans_complete();

        return array(TRUE, '领取成功', array());
    }

	protected function composePackAry($packs)
    {
        $valueAry = array();
        foreach ($packs as $pack)
        {
            switch ($pack['packType'])
            {
                /**
                 * @see Red_Pack_Model::TYPE_SUNSHINE
                 */
                case 1:
                    $valid_start = date('Y-m-d H:i:s', strtotime(date('Y-m-d')));
                    $valid_end = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime('+5 year'))));
                    // 到期时间前推一秒
                    $valid_end = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($valid_end)));
                    // 春节处理
                    $valid_end = $this->newYearFormat($valid_end);
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
                        /**
                         * @see Red_Pack_Model::STATUS_RECEIVED
                         */
                        0,
                        'NOW()',
                    ));
                    break;
                /**
                 * @see Red_Pack_Model::TYPE_RECHARGE
                 */
                case 2:
               	case 3:
                    $packParams = json_decode($pack['packParams'], TRUE);
                    $start_day = '+' . $packParams['start_day'] . ' day';
                    $startDay = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime($start_day))));
                    $end_day = '+' . $packParams['end_day'] . ' day';
                    $endDay = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime($end_day))));
                    // 到期时间前推一秒
                    $endDay = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($endDay)));
                    // 春节处理
                    $endDay = $this->newYearFormat($endDay);
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
                        /**
                         * @see Red_Pack_Model::STATUS_RECEIVED
                         */
                        0,
                        'NOW()',
                    ));
                    log_message('Log', json_encode($valueAry), 'hongbao');
                    break;
                default:
                    break;
            }
        }

        return $valueAry;
    }

	public function activatePack($activityId, $userId)
    {
        $redPack = $this->db->query("SELECT group_concat(rl.id SEPARATOR ',') idStr,
            sum(r.money) money
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id AND r.p_type in ?
            WHERE rl.aid = ? AND rl.uid = ? AND rl.status = ? AND rl.valid_start <= now()
                AND rl.valid_end >= now() AND r.delete_flag = 0 AND rl.delete_flag = 0",
            array(array(self::TYPE_RECHARGE, self::TYPE_BET), $activityId, $userId, self::STATUS_RECEIVED))
            ->getRow();
        if (empty($redPack) || empty($redPack['idStr']))
        {
            return array(FALSE, '没有红包', array());
        }

        $this->db->trans_start();
        $re1 = $this->db->query("UPDATE cp_redpack_log
            SET status = ?
            WHERE aid = ? and uid = ? AND delete_flag = 0 ",
            array(self::STATUS_ACTIVE, $activityId, $userId));
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
		// todo composite index
		if ($this->db->simple_query ( "UPDATE cp_redpack_log SET delete_flag = 1
	                				WHERE uid = $userId AND aid = $activityId" ))
		{
			list ( $success, $msg ) = array (
					TRUE,
					'删除成功' 
			);
		} else
		{
			list ( $success, $msg ) = array (
					FALSE,
					'删除失败' 
			);
		}
		
		return array (
				$success,
				$msg,
				array () 
		);
	}

	public function checkBound($userId, $idCard)
    {
        $idCardCount = $this->db->query("SELECT count(DISTINCT i.uid)
                FROM cp_user_info i
                JOIN cp_redpack_log l ON l.uid = i.uid
                JOIN cp_redpack r ON r.id = l.rid
                WHERE i.id_card = ? AND r.p_type in ? AND l.uid <> ?
                    AND l.delete_flag = 0 AND r.delete_flag = 0",
            array($idCard, array(self::TYPE_RECHARGE, self::TYPE_BET), $userId))
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
    
    protected function sendOLdUserPack($userId)
    {
    	$this->db->trans_start();
    	$phone = $this->db->query("select phone from cp_user_register where id=?", array($userId))->getOne();
    	$res1 = $this->db->query("update cp_activity_log set uid = ? , aid=8 where phone = ?", array($userId, $phone));
    	if ($this->db->affected_rows())
    	{
    		$packTypeStr = implode(',', array(self::TYPE_RECHARGE, self::TYPE_BET));
    		$redPacks = $this->db->query("SELECT ur.id userId, a.id activityId,
    				al.platform_id platformId, al.channel_id channelId,
    				al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
    				FROM cp_user_register ur
    				JOIN cp_activity_log al ON al.phone = ur.phone
    				JOIN cp_activity a ON al.aid = a.id
    				JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type IN ($packTypeStr) AND rp.delete_flag = 0
    				LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
    				WHERE ur.id = ? AND rl.id IS NULL ", array($userId))->getAll();
    				$valueAry = $this->composePackAry($redPacks);
    				if (empty($valueAry))
    				{
    					return array(TRUE, '发送成功', array());
    				}
    				$res = $this->db->simple_query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid,
				valid_start, valid_end, get_time, status, created) VALUES (" . implode('), (', $valueAry) . ")");
    				if ($this->db->affected_rows())
    				{
    					$this->db->trans_complete();
    					list($success, $msg) = array(TRUE, '发送成功');
    				}
    				else 
    				{
    					$this->db->trans_rollback();
    					list($success, $msg) = array(FALSE, '发送失败');
    				}
    	}
    	else 
    	{
    		$this->db->trans_rollback();
    		list($success, $msg) = array(FALSE, '发送失败');
    	}
    
    	return array($success, $msg, array());
    }

	protected function sendNewUserPack($userId)
    {
        $packTypeStr = implode(',', array(self::TYPE_RECHARGE, self::TYPE_BET));
        $redPacks = $this->db->query("SELECT ur.id userId, a.id activityId,
            al.platform_id platformId, al.channel_id channelId,
            al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
            FROM cp_user_register ur
            JOIN cp_activity_log al ON al.phone = ur.phone
            JOIN cp_activity a ON al.aid = a.id
            JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type IN ($packTypeStr) AND rp.delete_flag = 0
            LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
            WHERE ur.id = ? AND rl.id IS NULL ", array($userId))
            ->getAll();
        $valueAry = $this->composePackAry($redPacks);
        if (empty($valueAry))
        {
            return array(TRUE, '发送成功', array());
        }
        $this->db->trans_start();
        $res = $this->db->simple_query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid,
        		valid_start, valid_end, get_time, status, created)
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
    
	public function distribute($activityId, $phone, $platformId, $channelId)
    {
        if ($this->hasAttend($activityId, $phone)) return array(FALSE, '已参加过该活动', array());
        if ($this->hasAttend(1, $phone)) return array(FALSE, '您已参加188元红包活动', array());

        $activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime 
        		FROM cp_activity WHERE id = ? AND delete_flag = 0", $activityId)->getRow();
        if (empty($activity)) return array(FALSE, '活动已结束', array());

        $this->load->helper('date');
        $nowTime = now();
        if (mysql_to_unix($activity['startTime']) > $nowTime) return array(FALSE, '活动尚未开始', array());
        if (mysql_to_unix($activity['endTime']) < $nowTime) return array(FALSE, '活动已结束', array());

        $this->db->trans_start();
        $this->db->query("INSERT cp_activity_log (aid, phone, platform_id, channel_id, created) VALUES (?, ?, ?, ?, NOW())", array($activityId, $phone, $platformId, $channelId));
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }

        $userId = $this->getUserIdByPhone($phone);
        if (empty($userId)) {
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array());
        }
        
        if ($this->hasAttendByUid(1, $userId)) return array(FALSE, '您已参加188元红包活动', array());

        $this->db->query("UPDATE cp_activity_log SET uid = ? WHERE phone = ?", array($userId, $phone));

        $rechargePacks = $this->db->query("SELECT ur.id userId, a.id activityId,
            al.platform_id platformId, al.channel_id channelId,
            al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
            FROM cp_user_register ur
            JOIN cp_activity_log al ON al.phone = ur.phone
            JOIN cp_activity a ON al.aid = a.id AND a.start_time <= now()
                AND a.end_time >= now() AND a.delete_flag = 0
            JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type in ? AND rp.delete_flag = 0
            LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
            WHERE ur.id = ? AND rl.id IS NULL ", array(array(self::TYPE_RECHARGE, self::TYPE_BET), $userId))
            /**
             * @see Red_Pack_Model::TYPE_RECHARGE
             */
            ->getAll();
        $valueAry = $this->composePackAry($rechargePacks);
        if (empty($valueAry))
        {
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array());
        }

        $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid, valid_start, valid_end, get_time, status, created)
            VALUES (" . implode('), (', $valueAry) . ")");
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }

         $idCard = $this->db->query("SELECT id_card FROM cp_user_info WHERE uid = ?", $userId) ->getOne();
        if (empty($idCard))
        {
            /**
             * @see Red_Pack_Model::STATUS_RECEIVED
             */
            $packStatus = 0;
            $idCardCount = 0;
        }
        else
        {
            /**
             * @see Red_Pack_Model::STATUS_ACTIVE
             */
            $packStatus = 1;
            /**
             * @see Red_Pack_Model::TYPE_SUNSHINE
             */
            //todo performance?
            $idCardCount = $this->db->query("SELECT count(DISTINCT i.uid)
                FROM cp_user_info i
                JOIN cp_redpack_log l ON l.uid = i.uid
                JOIN cp_redpack r ON r.id = l.rid
                WHERE i.id_card = ? AND r.p_type = ? AND l.delete_flag = 0 AND i.uid <> ? AND r.delete_flag = 0",
                array($idCard, 3, $userId))
                ->getOne();
        }
        
        if ($idCardCount == 0)
        {

            $re3 = $this->db->query("UPDATE cp_redpack_log SET status = ? WHERE uid = ? AND aid = ?", array($packStatus, $userId, $activityId));
            if ( ! $re3)
            {
                $this->db->trans_rollback();
                return array(FALSE, '激活失败', array());
            }
        }
        else
        {
            $this->db->trans_rollback();
            return array(FALSE, '身份证号已领取过红包', array());
        }

        $this->freshWallet($userId);
        $this->db->trans_complete();

        return array(TRUE, '领取成功', array());
    }

    // 2018春节处理
    public function newYearFormat($date)
    {
        if($date >= '2018-02-15 00:00:00' && $date <= '2018-02-22 23:59:59')
        {
            $date = date("Y-m-d H:i:s", strtotime("$date +30 day"));
        }
        return $date;
    }
}
