<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/1/25
 * 修改时间: 19:14
 */
class New_Activity_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
    }

    public function hasAttend($activityId, $phone)
    {
        return $this->db->query("SELECT 1 FROM cp_activity_log
            WHERE aid = ? AND phone = ?", array($activityId, $phone))
            ->getOne();
    }

    public function attend($activityId, $phone, $platformId, $channelId)
    {
        if ($this->hasAttend($activityId, $phone))
        {
            return array(FALSE, '已参加过该活动', array());
        }

        $activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime
            FROM cp_activity
            WHERE id = ? AND delete_flag = 0", $activityId)
            ->getRow();
        if (empty($activity))
        {
            return array(FALSE, '活动已结束', array());
        }

        $this->load->helper('date');
        $nowTime = now();
        if (mysql_to_unix($activity['startTime']) > $nowTime)
        {
            return array(FALSE, '活动尚未开始', array());
        }
        if (mysql_to_unix($activity['endTime']) < $nowTime)
        {
            return array(FALSE, '活动已结束', array());
        }

        $this->db->trans_start();
        $re1 = $this->db->query("INSERT cp_activity_log
            (aid, phone, platform_id, channel_id, created)
            VALUES (?, ?, ?, ?, NOW())",
            array($activityId, $phone, $platformId, $channelId));
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

        $this->db->query("UPDATE cp_activity_log
            SET uid = ?
            WHERE phone = ?",
            array($userId, $phone));

        $rechargePacks = $this->db->query("SELECT ur.id userId, a.id activityId,
            al.platform_id platformId, al.channel_id channelId,
            al.created attendTime, rp.id packId, rp.p_type packType, rp.use_params packParams
            FROM cp_user_register ur
            JOIN cp_activity_log al ON al.phone = ur.phone
            JOIN cp_activity a ON al.aid = a.id AND a.start_time <= now()
                AND a.end_time >= now() AND a.delete_flag = 0
            JOIN cp_redpack rp ON rp.aid = a.id AND rp.p_type = ? AND rp.delete_flag = 0
            LEFT JOIN cp_redpack_log rl ON rl.aid = a.id AND rl.uid = ur.id
            WHERE ur.id = ? AND rl.id IS NULL ", array(3, $userId))
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

        $idCard = $this->db->query("SELECT id_card FROM cp_user_info WHERE uid = ?", $userId)
            ->getOne();
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

            $re3 = $this->db->query("UPDATE cp_redpack_log SET status = ?
                WHERE uid = ? AND aid = ?",
                array($packStatus, $userId, $activityId));
            if ( ! $re3)
            {
                $this->db->trans_rollback();

                return array(FALSE, '激活失败', array());
            }
        }
        else
        {
            $this->db->trans_rollback();

            return array(FALSE, '身份证号已在其他账号下领取过红包', array());
        }

        $this->freshWallet($userId);
        $this->db->trans_complete();

        return array(TRUE, '领取成功', array());
    }

    private function getUserIdByPhone($phone)
    {
        return $this->db->query("SELECT id FROM cp_user_register WHERE phone = ?", $phone)
            ->getOne();
    }

    private function composePackAry($packs)
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

    public function detail($activityId)
    {
        $activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime
            FROM cp_activity
            WHERE id = ? AND delete_flag = 0", $activityId)
            ->getRow();
        if (empty($activity))
        {
            return array(FALSE, '活动已结束', array());
        }

        return array(TRUE, 'Eureka!', $activity);
    }

    //刷新钱包
    private function freshWallet($uid)
    {
        $this->load->model('wallet_model');
		return $this->wallet_model->freshWallet($uid);
    }

    public function initData()
    {
        //initial data
        $this->db->simple_query("BEGIN");
        $this->db->simple_query("CREATE TABLE IF NOT EXISTS `cp_activity` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `a_name` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '活动名称',
            `params` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '活动参数',
            `start_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '活动开始时间',
            `end_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '活动结束时间',
            `remark` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '备注',
            `delete_flag` TINYINT(1) DEFAULT '0' COMMENT '是否删除',
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
            `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动信息表'");
        $this->db->simple_query("DELETE FROM cp_activity");
        $this->db->simple_query("ALTER TABLE cp_activity AUTO_INCREMENT = 1");
        $this->db->simple_query("INSERT cp_activity (a_name, start_time, end_time, created)
            VALUES ('红包188', NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), NOW())");

        $this->db->simple_query("CREATE TABLE IF NOT EXISTS `cp_activity_log` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `aid` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
            `phone` VARCHAR(11) NOT NULL COMMENT '用户手机号',
            `uid` BIGINT(20) UNSIGNED DEFAULT NULL,
            `platform_id` INT(11) NOT NULL DEFAULT '0' COMMENT '平台ID',
            `channel_id` INT(11) NOT NULL DEFAULT '0' COMMENT '渠道ID',
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
            `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
            PRIMARY KEY (`id`),
            UNIQUE KEY `activity_user` (`aid`,`phone`),
            KEY `user` (`phone`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户活动参与记录表'");
        $this->db->simple_query("DELETE FROM cp_activity_log");
        $this->db->simple_query("ALTER TABLE cp_activity_log AUTO_INCREMENT = 1");

        $this->db->simple_query("CREATE TABLE IF NOT EXISTS `cp_redpack` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `aid` INT(11) NOT NULL COMMENT '活动ID',
            `p_type` TINYINT(1) NOT NULL COMMENT '红包类型',
            `c_type` TINYINT(1) NOT NULL COMMENT '具体红包类型',
            `money` INT(11) NOT NULL DEFAULT '0' COMMENT '红包金额，单位分',
            `p_name` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '红包名称',
            `use_params` VARCHAR(200) NOT NULL DEFAULT '' COMMENT '红包参数',
            `cash_back` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '能否提现',
            `use_desc` VARCHAR(64) NOT NULL DEFAULT '',
            `refund_desc` VARCHAR(64) NOT NULL DEFAULT '',
            `delete_flag` TINYINT(1) DEFAULT '0' COMMENT '是否删除',
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
            `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包信息表'");
        $this->db->simple_query("DELETE FROM cp_redpack");
        $this->db->simple_query("ALTER TABLE cp_redpack AUTO_INCREMENT = 1");
        $this->db->simple_query("INSERT cp_redpack (aid, p_type, c_type, money, p_name, use_params, use_desc, refund_desc, cash_back, created)
            VALUES (1, 1, 1, 300, '3元红包', '{\"no_expire\":1}', '实名认证后可用', '红包金额不可提现', 0, NOW()),
            (1, 2, 2, 200, '2元红包', '{\"start_day\":0,\"end_day\":30,\"money_bar\":2000}', '充20元送2元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 2, 200, '2元红包', '{\"start_day\":30,\"end_day\":60,\"money_bar\":2000}', '充20元送2元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 2, 200, '2元红包', '{\"start_day\":60,\"end_day\":90,\"money_bar\":2000}', '充20元送2元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 2, 200, '2元红包', '{\"start_day\":90,\"end_day\":120,\"money_bar\":2000}', '充20元送2元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 2, 200, '2元红包', '{\"start_day\":120,\"end_day\":150,\"money_bar\":2000}', '充20元送2元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 3, 500, '5元红包', '{\"start_day\":0,\"end_day\":30,\"money_bar\":5000}', '充50元送5元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 3, 500, '5元红包', '{\"start_day\":30,\"end_day\":60,\"money_bar\":5000}', '充50元送5元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 3, 500, '5元红包', '{\"start_day\":60,\"end_day\":90,\"money_bar\":5000}', '充50元送5元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 3, 500, '5元红包', '{\"start_day\":90,\"end_day\":120,\"money_bar\":5000}', '充50元送5元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 3, 500, '5元红包', '{\"start_day\":120,\"end_day\":150,\"money_bar\":5000}', '充50元送5元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 4, 1000, '10元红包', '{\"start_day\":0,\"end_day\":30,\"money_bar\":10000}', '充100元送10元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 4, 1000, '10元红包', '{\"start_day\":30,\"end_day\":60,\"money_bar\":10000}', '充100元送10元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 4, 1000, '10元红包', '{\"start_day\":60,\"end_day\":90,\"money_bar\":10000}', '充100元送10元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 4, 1000, '10元红包', '{\"start_day\":90,\"end_day\":120,\"money_bar\":10000}', '充100元送10元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 4, 1000, '10元红包', '{\"start_day\":120,\"end_day\":150,\"money_bar\":10000}', '充100元送10元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 5, 2000, '20元红包', '{\"start_day\":0,\"end_day\":30,\"money_bar\":20000}', '充200元送20元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 5, 2000, '20元红包', '{\"start_day\":30,\"end_day\":60,\"money_bar\":20000}', '充200元送20元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 5, 2000, '20元红包', '{\"start_day\":60,\"end_day\":90,\"money_bar\":20000}', '充200元送20元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 5, 2000, '20元红包', '{\"start_day\":90,\"end_day\":120,\"money_bar\":20000}', '充200元送20元', '充值金额与红包金额均不可提现', 0, NOW()),
            (1, 2, 5, 2000, '20元红包', '{\"start_day\":120,\"end_day\":150,\"money_bar\":20000}', '充200元送20元', '充值金额与红包金额均不可提现', 0, NOW())");

        $this->db->simple_query("CREATE TABLE IF NOT EXISTS `cp_redpack_type` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `p_type` TINYINT(1) NOT NULL COMMENT '红包类型',
            `p_name` VARCHAR(16) NOT NULL DEFAULT '' COMMENT '红包名称',
            `delete_flag` TINYINT(1) DEFAULT '0' COMMENT '是否删除',
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
            `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包类型表'");
        $this->db->simple_query("DELETE FROM cp_redpack_type");
        $this->db->simple_query("ALTER TABLE cp_redpack_type AUTO_INCREMENT = 1");
        $this->db->simple_query("INSERT cp_redpack_type (p_type, p_name, created)
            VALUES (1, '赠送', NOW()), (2, '充值红包', NOW()), (3, '购彩红包', NOW()), (4, '加奖红包', NOW())");

        $this->db->simple_query("CREATE TABLE IF NOT EXISTS `cp_redpack_log` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `aid` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
            `platform_id` INT(11) NOT NULL DEFAULT '0' COMMENT '领取平台ID',
            `channel_id` INT(11) UNSIGNED NOT NULL COMMENT '领取渠道ID',
            `uid` INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
            `rid` INT(11) UNSIGNED NOT NULL COMMENT '红包ID',
            `valid_start` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效期开始时间',
            `valid_end` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '有效期结束时间',
            `get_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '领取时间',
            `status` TINYINT(2) NOT NULL DEFAULT 0 COMMENT '0领取未激活 1已激活 2已使用',
            `use_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '使用时间',
            `remark` VARCHAR(64) NOT NULL DEFAULT '',
            `delete_flag` TINYINT(1) DEFAULT '0' COMMENT '是否删除',
            `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
            `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
            PRIMARY KEY (`id`),
            KEY `user` (`uid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户红包记录表'");
        $this->db->simple_query("DELETE FROM cp_redpack_log");
        $this->db->simple_query("ALTER TABLE cp_redpack_log AUTO_INCREMENT = 1");

        $this->db->simple_query("COMMIT");
    }
}