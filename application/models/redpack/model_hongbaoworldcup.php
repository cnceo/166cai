<?php
include_once 'model_hongbao.php';
class Model_Hongbaoworldcup extends Model_Hongbao
{
    
    private $_sectionInfo = array(
        1 => array('23', '世界杯幸运红包'),
        2 => array('27', '世界杯幸运红包'),
        3 => array('40', '世界杯必中红包'),
        4 => array('51', '世界杯必中红包'),
        5 => array('67', '世界杯超级红包'),
        6 => array('140', '世界杯超级红包'),
        7 => array('264', '世界杯豪华红包'),
        8 => array('448', '世界杯豪华红包'),
    );

	public function attend($activityId, $phone, $platformId, $channelId)
    {
        if ($this->hasAttend($activityId, $phone)) return array(FALSE, '您已领取过红包', array('code' => '500'));

        $activity = $this->db->query("SELECT if (start_time < NOW(), 0, 1) start, if (end_time > NOW(), 0, 1) end FROM cp_activity WHERE id = ? AND delete_flag = 0", $activityId)->getRow();

        if ($activity['start']) return array(FALSE, '活动尚未开始', array());
        if ($activity['end']) return array(FALSE, '活动已结束', array());

        $this->db->trans_start();
        $userId = $this->getUserIdByPhone($phone);
        
        $this->db->query("INSERT cp_activity_log (aid, uid, phone, platform_id, channel_id, created) VALUES (?, ?, ?, ?, ?, NOW())", array($activityId, $userId, $phone, $platformId, $channelId));
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }
        
        $rechargePacks = $this->db->query("select r.p_type, wrl.section, wrl.rid as packId, $platformId as platformId, $channelId as channelId, $userId as userId, r.use_params, a.created as attendTime
            from cp_worldcup_redpack_log as wrl
            join cp_redpack as r on wrl.rid = r.id
            join cp_activity_log as a on r.aid = a.aid and a.uid = wrl.uid
            where wrl.uid = ?", array($userId))->getAll();
        
        if (empty($rechargePacks)) {
            if (!$this->createRedpack($userId)) {
                $this->db->trans_rollback();
                return array(FALSE, '领取失败', array());
            }
        }
                
        $rechargePacks = $this->db->query("select r.p_type, wrl.section, wrl.rid as packId, $platformId as platformId, $channelId as channelId, $userId as userId, r.use_params, a.created as attendTime
            from cp_worldcup_redpack_log as wrl
            join cp_redpack as r on wrl.rid = r.id
            join cp_activity_log as a on r.aid = a.aid and a.uid = ?
            left join cp_redpack_log as rl on wrl.uid = rl.uid and wrl.rid = rl.rid
            where wrl.uid = ? and rl.id is null", array($userId, $userId))->getAll();
        $valueAry = $this->composePackAry($rechargePacks);
        if (empty($valueAry)) {
            $this->db->trans_complete();
            return array(TRUE, '领取成功', array());
        }

        $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid, valid_start, valid_end, get_time, status, created) VALUES (" . implode('), (', $valueAry) . ")");
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }
        
        $this->db->query('update cp_worldcup_redpack_log set status = 1 where uid = ?', $userId);
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(FALSE, '领取失败', array());
        }

        $idCard = $this->db->query("SELECT id_card FROM cp_user_info WHERE uid = ?", $userId) ->getOne();

        if (!empty($idCard)) {
           $this->db->query("UPDATE cp_redpack_log SET status = 1 WHERE uid = ? AND aid = 14", array($userId));
            if ( ! $this->db->affected_rows()) {
                $this->db->trans_rollback();
                return array(FALSE, '激活失败', array());
            }
        }

        $this->db->trans_complete();

        return array(TRUE, '领取成功', $this->_sectionInfo[$rechargePacks['0']['section']]);
    }
    
    public function createRedpack($uid = null) {
        if ($uid) {
            $this->db->query("insert ignore into cp_worldcup_redpack_log (uid, section, rid, created) 
                select $uid, 1, id, NOW() from cp_redpack where aid = 14 and `group` = 1");
            return $this->db->affected_rows();
        } else {
            $this->db->query("insert ignore into cp_worldcup_redpack_log (uid, section, rid, created) 
                select u.uid, 1, r.id, NOW()
                from cp_user u
                join cp_redpack r
                WHERE u.created > DATE_SUB(NOW(),INTERVAL 1.5 DAY) AND r.aid=14 AND r.`group` = 1");
        }
    }
    
    public function sendAll() {
        $start = 6145441;
        $maxid = $this->db->query('select max(id) from cp_worldcup_redpack_log')->getOne();
        do {
            $limit = 200;
            $datas = $this->db->query("select distinct wrl.uid, ui.phone
                from cp_worldcup_redpack_log wrl
                left join cp_user_info ui on wrl.uid = ui.uid
								WHERE wrl.status=0 and wrl.id >= $start and wrl.id < ".($start + $limit))->getAll();
            $uids = array();
            if (!empty($datas)) {
                $this->db->trans_start();
                foreach ($datas as $data) {
                    $this->db->query("INSERT ignore cp_activity_log (aid, uid, phone, platform_id, channel_id, created) VALUES (?, ?, ?, ?, ?, NOW())", array(14, $data['uid'], $data['phone'], 0, 0));
                    array_push($uids, $data['uid']);
                }
                $rechargePacks = $this->db->query("select r.p_type, wrl.section, wrl.rid as packId, 0 as platformId, 0 as channelId, wrl.uid as userId, r.use_params, if (ui.id_card, 1, 0) as status, a.created as attendTime
                from cp_worldcup_redpack_log wrl
                join cp_redpack r on wrl.rid = r.id
                join cp_activity_log a on wrl.uid=a.uid AND a.aid=14
                join cp_user_info ui on wrl.uid = ui.uid
                where wrl.uid in ?", array($uids))->getAll();
                $valueAry = $this->composePackAry($rechargePacks);
                $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid, valid_start, valid_end, get_time, status, created) VALUES (" . implode('), (', $valueAry) . ")");
                if ( ! $this->db->affected_rows()) $this->db->trans_rollback();
                $this->db->query('update cp_worldcup_redpack_log set status = 1 where uid in ?', array($uids));
                if ( ! $this->db->affected_rows()) $this->db->trans_rollback();
                $this->db->trans_complete();
            }
            $start += $limit;
        }
        while ($start <= $maxid);
//         if (ENVIRONMENT === 'development')
//             $filepath = 'E:\wamp\www\166cai\source\cache';
//         else
//             $filepath = '/opt/case/www.166cai.com/source/cache';
//         $section = 1;
//         $start = 0;
//         $offset = 2000;
//         $persheet = 50000;
//         $sheet = 0;
        
//         $this->load->library('excel');
//         $this->excel->setActiveSheetIndex(0);
//         $this->excel->getActiveSheet()->setTitle('worldcupUids');
        
//         do {
//             $uids = $this->db->query("SELECT DISTINCT wrl.uid, ui.phone FROM cp_worldcup_redpack_log as wrl
//                 join cp_user_info ui on wrl.uid = ui.uid 
//                 WHERE wrl.section = $section LIMIT $start, $offset")->getAll();
//             $objSheet = $this->excel->getActiveSheet()->setCellValue('A1', 'Uid')->setCellValue('B1', 'phone');
//             foreach ($uids as $key => $value) {
//                 $this->excel->getActiveSheet()->setCellValue('A'.($start - $sheet * $persheet +$key+2), $value['uid'])->setCellValue('B'.($start - $sheet * $persheet+$key+2), $value['phone']);
//             }
            
//             $start += $offset;
//             if (empty($uids) || $start % $persheet == 0) {
//                 $sheet = ceil($start/$persheet);
//                 $fileName = "$filepath/worldcup$section-$sheet.csv";
//                 $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'CSV');
//                 $objWriter->save($fileName);
//                 if (empty($uids)) {
//                     $start = $sheet = 0;
//                     $section++;
//                 }
//             }
//         }
//         while (!empty($uids) || $section <= 8);
        
    }
    
    public function sendSms() {
        $message = $this->config->item('MESSAGE');
        $position = $this->config->item('POSITION');
        $this->load->library('mipush');
        $start = 0;
        $limit = 500;
        $maxid = $this->db->query('select max(id) from cp_worldcup_redpack_log')->getOne();
        $where = '';
        if (ENVIRONMENT !== 'production') $where .= ' and wrl.uid < 5000';
        do {
            $result = $this->db->query("select distinct wrl.uid, wrl.section, ui.phone
            from cp_worldcup_redpack_log as wrl
            join cp_user_info as ui on wrl.uid = ui.uid
            where wrl.status = 1$where and wrl.id >= $start and wrl.id < ".($start + $limit))->getAll();
            if (!empty($result)) {
                $values = array();
                $pushDatas = array();
                foreach ($result as $val) {
                    array_push($values, "(".implode(',', array(
                        "'".$val['uid']."'",
                        "'".$val['phone']."'",
                        11,
                        "'".str_replace('#CONTENT#', $this->_sectionInfo[$val['section']][1], $message['worldcup_redpack'])."'",
                        'NOW()',
                        "'127.0.0.1'",
                        $position['166_huodong']
                    )).")");
                    array_push($pushDatas, $this->mipush->getSendData('user', array(
                        'type'      =>  'redpack_use',
                        'uid'       =>  $val['uid'],
                        'title'     =>  '红包生效提醒',
                        'content'   =>  str_replace('#CONTENT#', $this->_sectionInfo[$val['section']][1], '亲，您的#CONTENT#已生效！红包在手，大奖不愁~快来使用>>')
                    )));
                }
                $this->db->query("insert into cp_sms_logs (uid, phone, ctype, content, created, uip, position) values ".implode(',', $values));
                $this->mipush->recodeUserLogs($pushDatas);
            }
            $start += $limit;
        }while ($start <= $maxid);
    }
    
	protected function composePackAry($packs)
    {
        $valueAry = array();
        foreach ($packs as $pack)
        {
            switch ($pack['p_type'])
            {
                case 2:
                    $packParams = json_decode($pack['use_params'], TRUE);
                    $valueAry[] = implode(', ', array(
                        14,
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        "NOW()",
                        "DATE_ADD(NOW(), INTERVAL ({$packParams['end_day']} - 1) * 24 * 3600 - 1 SECOND)",
                        "'" . $pack['attendTime'] . "'",
                        isset($pack['status']) ? $pack['status'] : 0,
                        'NOW()',
                    ));
                    log_message('Log', json_encode($valueAry), 'hongbao');
                    break;
               	case 3:
                    $packParams = json_decode($pack['use_params'], TRUE);
                    $valueAry[] = implode(', ', array(
                        14,
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        "'2018-06-15 00:00:00'",
                        "DATE_ADD('2018-06-15', INTERVAL ({$packParams['end_day']} - 1) * 24 * 3600 - 1 SECOND)",
                        "'" . $pack['attendTime'] . "'",
                        isset($pack['status']) ? $pack['status'] : 0,
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
    
    public function countWordcupUsers() {
        $start = 0;
        $offset = 500;
        $created = '2016-04-01';
//         do {
//             $continue = false;
//             if ($created >= date('2018-m-01', strtotime('-1 month'))) {
//                 $result = $this->db->query("SELECT uid, SUM(money) as money, ".date('Ym', strtotime($created))."
//                     FROM cp_orders
//                     WHERE created >= '$created' AND created < '".date('Y-m-d H:i:s', strtotime($created.' +1 month'))."' AND `status` in (500, 1000, 2000)
//                     GROUP BY uid
//                     LIMIT $start, $offset")->getAll();
//                 if (!empty($result)) {
//                     $values = array();
//                     foreach ($result as $val) {
//                         array_push($values, "({$val['uid']}, {$val['money']}, ".date('Ym', strtotime($created)).")");
//                     }
//                     $this->db->query("insert ignore into bn_cpiao_tmp.worldcup_count_tmp (uid, money, month) values ".implode(',', $values));
//                 } else {
//                     $continue = true;
//                 }
//             } else {
//                 $result = $this->cfgDB->query("SELECT uid, SUM(money) as money, ".date('Ym', strtotime($created))."
//                 FROM bn_cpiao_cfg.cp_orders_ori_".date('Ym', strtotime($created))."
//                     WHERE `status` in (500, 1000, 2000)
//                     GROUP BY uid
//                     LIMIT $start, $offset")->getAll();
//                 if (!empty($result)) {
//                     $values = array();
//                     foreach ($result as $val) {
//                         array_push($values, "({$val['uid']}, {$val['money']}, ".date('Ym', strtotime($created)).")");
//                     }
//                     $this->db->query("insert ignore into bn_cpiao_tmp.worldcup_count_tmp (uid, money, month) values ".implode(',', $values));
//                 } else {
//                     $continue = true;
//                 }
//             }
//             $start += $offset;
//             if ($continue) {
//                 $start = 0;
//                 $created = date('Y-m-d', strtotime($created.' +1 month'));
//             }
//         }
        while ($this->db->affected_rows() || $created !== '2018-04-01') ;
        $start = 0;
        do {
            $this->db->query("INSERT ignore INTO cp_worldcup_redpack_log (uid, section, rid, created)
                SELECT tmp.uid, tmp.userlevel, r.id, NOW() FROM
                (SELECT u.uid,
                CASE
                WHEN t.id IS null THEN 1
                WHEN MAX(t.money) <= 15000 THEN 2
                WHEN MAX(t.money) > 15000 && MAX(t.money) <= 30000 THEN 3
                WHEN MAX(t.money) > 30000 && MAX(t.money) <= 60000 THEN 4
                WHEN MAX(t.money) > 60000 && MAX(t.money) <= 120000 THEN 5
                WHEN MAX(t.money) > 120000 && MAX(t.money) <= 240000 THEN 6
                WHEN MAX(t.money) > 240000 && MAX(t.money) <= 800000 THEN 7
                WHEN MAX(t.money) > 800000 THEN 8
                END
                as userlevel
                FROM
                cp_user_info as u
                LEFT JOIN bn_cpiao_tmp.worldcup_count_tmp as t on u.uid=t.uid
                WHERE u.userStatus = 0
                GROUP BY u.uid limit $start, $offset) tmp
                INNER JOIN cp_redpack r on tmp.userlevel=r.`group`
                WHERE r.aid=14");
            $start += $offset;
        }
        while ($this->db->affected_rows());
    }
    
    public function sendHongbao($aid, $data)
    {
        $sql = "select id,rid from cp_win_question_user where uid=? and questionId=?";
        $record = $this->db->query($sql, array($data['uid'], $data['question']))->getRow();
        if (!empty($record) && $record['rid'] != 0) {
            return array(300, '您已领取过红包', array());
        }
        if (!empty($record)) {
            $this->db->query("update cp_win_question_user set rid =? where id=?", array($data['rid'], $record['id']));
            $id = $record['id'];
        } else {
            $this->db->query("INSERT cp_win_question_user (questionId, uid, rid, created) VALUES (?, ?, ?, NOW())", array($data['question'], $data['uid'], $data['rid'], 'NOW()'));
            $id = $this->db->insert_id();
        }
        $this->db->trans_start();
        $rechargePacks = $this->db->query("select r.p_type, wrl.rid as packId, {$data['platformId']} as platformId, {$data['channel']} as channelId, {$data['uid']} as userId, r.use_params, wrl.created as attendTime,wrl.questionId
            from cp_win_question_user as wrl
            join cp_redpack as r on wrl.rid = r.id
            left join cp_redpack_log as rl on wrl.uid = rl.uid and wrl.rid = rl.rid
            where wrl.uid = ? and wrl.id = ?", array($data['uid'], $id))->getAll();
        $sql = "select extra from cp_win_question_config where id =?";
        $question = $this->db->query($sql, array($data['question']))->getRow();
        $extras = json_decode($question['extra'], true);
        $time = '';
        foreach ($extras as $extra) {
            if ($extra['rid'] == $data['rid']) {
                $time = $extra['ridTime'];
            }
        }
        $valueAry = $this->newcomposePackAry($rechargePacks, $time);
        if (empty($valueAry)) {
            $this->db->trans_complete();
            return array(200, '领取成功', array());
        }
        $this->db->query("INSERT cp_redpack_log (aid, platform_id, channel_id, uid, rid, orderId, valid_start, valid_end, get_time, status, created) VALUES (" . implode('), (', $valueAry) . ")");
        if ( ! $this->db->affected_rows()) {
            $this->db->trans_rollback();
            return array(400, '领取失败', array());
        }
        $idCard = $this->db->query("SELECT id_card FROM cp_user_info WHERE uid = ?", $data['uid']) ->getOne();
        if (!empty($idCard)) {
           $this->db->query("UPDATE cp_redpack_log SET status = 1 WHERE uid = ? AND aid = 15 and status = 0", array($data['uid']));
            if ( ! $this->db->affected_rows()) {
                $this->db->trans_rollback();
                return array(400, '激活失败', array());
            }
        }
        $this->db->trans_complete();
        return array(200, '领取成功', array());
    }

    
    protected function newcomposePackAry($packs, $time)
    {
        $valueAry = array();
        foreach ($packs as $pack) {
            switch ($pack['p_type']) {
                case 1:
                    $valid_start = date('Y-m-d H:i:s', strtotime(date('Y-m-d')));
                    $valid_end = date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime('+5 year'))));
                    // 到期时间前推一秒
                    $valid_end = date('Y-m-d H:i:s', strtotime('-1 second', strtotime($valid_end)));
                    $valueAry[] = implode(', ', array(
                        15,
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        $pack['questionId'],
                        "'" . $valid_start . "'",
                        "'" . $valid_end . "'",
                        "'" . $pack['attendTime'] . "'",
                        0,
                        'NOW()',
                    ));
                    break;
                case 2:
                    $packParams = json_decode($pack['use_params'], TRUE);
                    $valueAry[] = implode(', ', array(
                        15,
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        $pack['questionId'],
                        "'$time'",
                        "DATE_ADD('$time', INTERVAL {$packParams['end_day']} DAY)",
                        "'" . $pack['attendTime'] . "'",
                        isset($pack['status']) ? $pack['status'] : 0,
                        'NOW()',
                    ));
                    log_message('Log', json_encode($valueAry), 'hongbao');
                    break;
                case 3:
                    $packParams = json_decode($pack['use_params'], TRUE);
                    $valueAry[] = implode(', ', array(
                        15,
                        $pack['platformId'],
                        $pack['channelId'],
                        $pack['userId'],
                        $pack['packId'],
                        $pack['questionId'],
                        "'$time'",
                        "DATE_ADD('$time', INTERVAL {$packParams['end_day']} DAY)",
                        "'" . $pack['attendTime'] . "'",
                        isset($pack['status']) ? $pack['status'] : 0,
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
        $typeArr = array(self::TYPE_RECHARGE, self::TYPE_BET);
        //答题活动临时放在这里，以后优化
        if ($activityId == 15) $typeArr = array(self::TYPE_SUNSHINE, self::TYPE_RECHARGE, self::TYPE_BET);
        $redPack = $this->db->query("SELECT group_concat(rl.id SEPARATOR ',') idStr,
            sum(r.money) money
            FROM cp_redpack_log rl
            JOIN cp_redpack r ON rl.rid = r.id AND r.p_type in ?
            WHERE rl.aid = ? AND rl.uid = ? AND rl.status = ?
                AND rl.valid_end >= now() AND r.delete_flag = 0 AND rl.delete_flag = 0",
            array($typeArr, $activityId, $userId, self::STATUS_RECEIVED))
            ->getRow();
        if (empty($redPack) || empty($redPack['idStr']))
        {
            return array(FALSE, '没有红包', array());
        }
    
        $this->db->trans_start();
        $re1 = $this->db->query("UPDATE cp_redpack_log
            SET status = ?
            WHERE aid = ? AND uid = ? AND delete_flag = 0",
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

    public function getTotalMoney() {
        $this->load->driver ( 'cache', array ('adapter' => 'redis') );
        $REDIS = $this->config->item ( 'REDIS' );
        $money = $this->db->query('SELECT SUM(r.money) FROM cp_redpack_log as rl JOIN cp_redpack as r on rl.rid = r.id WHERE rl.aid=14')->getOne();
        $this->cache->save ( $REDIS ['WC_RP_MONEY'], $money, 0);
    }

}
