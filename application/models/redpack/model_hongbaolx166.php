<?php
include_once 'model_hongbao.php';
class Model_Hongbaolx166 extends Model_Hongbao
{

	public function attend($phone, $platformId, $channelId, $ip, $reffer)
    {
        if ($this->hasAttend(8, $phone)) return array(FALSE, '已参加过该活动', array('code' => 401));
        if ($this->hasAttend(1, $phone)) return array(FALSE, '您已参加188元红包活动', array('code' => 402));

        $activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime 
        		FROM cp_activity WHERE id = ? AND delete_flag = 0", 8)->getRow();
        if (empty($activity)) return array(FALSE, '活动已结束', array('code' => 403));

        $this->load->helper('date');
        $nowTime = now();
        if (mysql_to_unix($activity['startTime']) > $nowTime) return array(FALSE, '活动尚未开始', array('code' => 404));
        if (mysql_to_unix($activity['endTime']) < $nowTime) return array(FALSE, '活动已结束', array('code' => 403));
        
        $rid = 58;
        if (ENVIRONMENT !== 'production') $rid = 197;

        $this->db->trans_start();
        $re1 = $this->db->query("INSERT cp_activity_log (aid, phone, platform_id, channel_id, created) VALUES (?, ?, ?, ?, NOW())", array(8, $phone, $platformId, $channelId));
        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array('code' => 400));
        }

        $userId = $this->getUserIdByPhone($phone);
        if (empty($userId))
        {
        	$pword = $this->createNonceStr(rand(6, 8));
        	$uniqid = substr(uniqid(), 0, 9);
        	$pwd = md5($pword) . $uniqid;
        	$user = array(
        		'passid' => 0,
        		'salt' => $uniqid,
        		'pword' => strCode($pwd, 'ENCODE'),
        		'reg_type' => 0,
        		'reg_reffer' => $reffer,
        		'activity_id' => 8,
        		'reg_ip' => $ip,
        		'last_login_time' => date('Y-m-d H:i:s'),
        		'visit_times' => 1,
        		'platform' => $platformId,
        		'channel' => $channelId
        	);
        	
        	$this->load->model('user_model');
        	$result = $this->user_model->doRegister($phone, $user, false);
        	$userId = $result['uid'];
        	// 发送短信
        	$msgData = array('#CODE#' => $pword);
        	$position = $this->config->item('POSITION');
        	$this->user_model->sendSms($userId, $msgData, 'wechat_register', null, '127.0.0.1', $position['register_captche']);
        }
        
        if ($this->hasAttendByUid(1, $userId)) return array(FALSE, '您已参加188元红包活动', array('code' => 402));

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
        	$redPackId = 0;
            $redPackRes = $this->db->query("select id from cp_redpack_log where uid = ? and rid = ? and delete_flag = 0", array($userId, $rid))->getCol();
            if (!empty($redPackRes)) $redPackId = $redPackRes[0];
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array('code' => 200, 'uid' => $userId, 'rid' => $redPackId));
        }

        $re2 = $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid,
            valid_start, valid_end, get_time, status, created)
            VALUES (" . implode('), (', $valueAry) . ")");
        if ( ! $this->db->affected_rows())
        {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array('code' => 400));
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
            $re3 = $this->db->query("UPDATE cp_redpack_log SET status = ? WHERE uid = ? AND aid = ?", array($packStatus, $userId, 8));
            if ( ! $re3)
            {
                $this->db->trans_rollback();
                return array(FALSE, '激活失败', array('code' => 400));
            }
        }
        else
        {
            $this->db->trans_rollback();
            return array(FALSE, '身份证号已领取过红包', array('code' => 401));
        }

        $this->freshWallet($userId);
        $redPackId = 0;
        $redPackRes = $this->db->query("select id from cp_redpack_log where uid = ? and rid = ? and delete_flag = 0", array($userId, $rid))->getCol();
        if (!empty($redPackRes)) $redPackId = $redPackRes[0];
        $this->db->trans_complete();

        return array(TRUE, '领取成功', array('code' => 200, 'uid' => $userId, 'rid' => $redPackId));
    }
    
    public function createNonceStr($length = 6)
    {
    	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$str = "";
    	for ($i = 0; $i < $length; $i++)
    	{
    	$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    	}
    	return $str;
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
