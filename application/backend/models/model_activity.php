<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
class Model_activity extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：shigx
     * 功    能：获取活动参与记录列表
     * 修改日期：2014.11.05
     */
    public function listActivityLog($searchData, $page, $pageCount)
    {
        $where = " where 1";
        $where .= $this->condition("a.aid", $searchData['aid']);
        $where .= $this->condition("a.channel_id", $searchData['channelId']);
        $where .= $this->condition("a.created", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "time");
        $where .= $this->condition("a.phone", $searchData['phone']);
        if ($this->emp($searchData['isRegister']))
        {
            if($searchData['isRegister'] == '1')
            {
                $where .= " and a.uid is not null";
            }
            else
            {
                $where .= " and a.uid is null";
            }
        }
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where .= " and b.reg_type in ('0', '2')";
            }
            else
            {
                $where .= " and b.reg_type = ".$searchData['reg_type'];
            } 
        }
        $where .= $this->condition("b.channel", $searchData['registerChannel']);
        $where .= $this->condition("b.platform", $searchData['registerPlatform']);
        $where .= $this->condition("b.reg_reffer", $searchData['registerVersion']);
        $count = $this->BcdDb->query("select count(1) from cp_activity_log a left join cp_user b on a.uid = b.uid {$where}")->getOne();
        $select = "select a.aid, a.phone, a.created, a.platform_id, a.channel_id, a.uid,
        b.created rTime, b.channel rChannel, b.platform rPlatform, b.reg_type reg_type, b.reg_reffer rVersion
        from cp_activity_log a left join cp_user b on a.uid = b.uid {$where}
        ORDER BY a.created DESC,a.id DESC
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->getAll();
        return array(
            $result,
            $count
        );
    }
    
    /**
     * 参    数： $searchData 查询条件数组
     * 作    者：shigx
     * 功    能：获取导出数据
     * 修改日期：2016.01.27
     */
    public function getExportData($searchData)
    {
        $where = " where 1";
        $where .= $this->condition("a.aid", $searchData['aid']);
        $where .= $this->condition("a.channel_id", $searchData['channelId']);
        $where .= $this->condition("a.created", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "time");
        $where .= $this->condition("a.phone", $searchData['phone']);
        if ($this->emp($searchData['isRegister']))
        {
            if($searchData['isRegister'] == '1')
            {
                $where .= " and a.uid is not null";
            }
            else
            {
                $where .= " and a.uid is null";
            }
        }
        $where .= $this->condition("b.channel", $searchData['registerChannel']);
        $where .= $this->condition("b.platform", $searchData['registerPlatform']);
        $where .= $this->condition("b.reg_reffer", $searchData['registerVersion']);
        $select = "select a.aid, a.phone, a.created, a.platform_id, a.channel_id, a.uid,
        b.created rTime, b.channel rChannel, b.platform rPlatform, b.reg_reffer rVersion
        from cp_activity_log a left join cp_user b on a.uid = b.uid {$where}
        ORDER BY a.created DESC,a.id DESC";
        $result = $this->BcdDb->query($select)->getAll();
        return $result;
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：shigx
     * 功    能：获取用户红包记录列表
     * 修改日期：2014.11.05
     */
    public function listRedpack($searchData, $page, $pageCount)
    {
        $where = " where 1";
        if ($searchData['uname']) $searchData['uid'] = $this->BcdDb->query('select uid from cp_user where uname = ?', array($searchData['uname']))->getOne();
        if ($searchData['phone']) $searchData['uid'] = $this->BcdDb->query('select uid from cp_user_info where phone = ?', array($searchData['phone']))->getOne();
        $where .= $this->condition("a.uid", $searchData['uid']);
        $where .= $this->condition("c.p_type", $searchData['p_type']);
        //默认设置 显示当天的领取的红包
        $where .= $this->condition("a.get_time", array(
                $searchData['g_start_time'],
                $searchData['g_end_time']
        ), "datetime");
        $where .= $this->condition("a.aid", $searchData['aid']);
        $where .= $this->condition("a.channel_id", $searchData['channel_id']);
        $where .= $this->condition("a.valid_start", array(
                $searchData['v_start_time'],
                $searchData['v_end_time']
        ), "datetime");
        if ($this->emp($searchData['money']))
        {
            $where .= $this->condition("c.money", $searchData['money'] * 100);
        }

        $where .= $this->condition("a.status", $searchData['status']);
        $where .= $this->condition("c.ismobile_used", $searchData['ismobile_used']);
        $where .= $this->condition("a.valid_end", array(
                $searchData['s_start_time'],
                $searchData['s_end_time']
        ), "datetime");
        $where .= $this->condition("a.use_time", array(
                $searchData['u_start_time'],
                $searchData['u_end_time']
        ), "datetime");
        $where .= " and a.delete_flag = '0'";
        //红包派发处理
        $join = "cp_user b on  a.uid = b.uid";
        //$join = "cp_activity_log b on a.aid = b.aid and a.uid=b.uid";
        $select = "select a.id, a.aid, a.channel_id, a.uid, a.rid, a.valid_start, a.valid_end, a.get_time,
        a.status, a.use_time, a.created, a.remark,
        b.uname, c.p_type, c.money, c.p_name, c.use_desc, c.refund_desc,c.ismobile_used
        from cp_redpack_log a left join $join
        left join cp_redpack c on c.id = a.rid
        {$where} ORDER BY a.get_time DESC,a.id DESC
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql = "select count(1) count, count(DISTINCT a.uid) countUser, sum(c.money) countMoney 
        from cp_redpack_log a left join $join
        left join cp_redpack c on c.id = a.rid {$where}";
        $count = $this->BcdDb->query($totalSql)->getRow();
        $result = $this->BcdDb->query($select)->getAll();
        return array(
            $result,
            $count['count'],
            $count['countUser'],
            $count['countMoney']
        );
    }
    
    /**
     * 参    数： $searchData 查询条件数组
     * 作    者：shigx
     * 功    能：获取导出数据
     * 修改日期：2016.01.27
     */
    public function redpackExport($searchData)
    {
        $where = " where 1";
        $where .= $this->condition("a.uid", $searchData['uid']);
        $where .= $this->condition("b.phone", $searchData['phone']);
        $where .= $this->condition("c.p_type", $searchData['p_type']);
        //默认设置 显示当天的领取的红包
        $where .= $this->condition("a.get_time", array(
                $searchData['g_start_time'],
                $searchData['g_end_time']
        ), "datetime");
        $where .= $this->condition("a.aid", $searchData['aid']);
        $where .= $this->condition("a.channel_id", $searchData['channel_id']);
        $where .= $this->condition("a.valid_start", array(
                $searchData['v_start_time'],
                $searchData['v_end_time']
        ), "datetime");
        if ($this->emp($searchData['money']))
        {
            $where .= $this->condition("c.money", $searchData['money'] * 100);
        }

        $where .= $this->condition("a.status", $searchData['status']);
        $where .= $this->condition("c.ismobile_used", $searchData['ismobile_used']);
        $where .= $this->condition("a.valid_end", array(
                $searchData['s_start_time'],
                $searchData['s_end_time']
        ), "datetime");
        $where .= $this->condition("a.use_time", array(
                $searchData['u_start_time'],
                $searchData['u_end_time']
        ), "datetime");
        $where .= " and a.delete_flag = '0'";
        //红包派发处理
        $join = "cp_user_info b on  a.uid = b.uid";
        //$join = "cp_activity_log b on a.aid = b.aid and a.uid=b.uid";
        $select = "select a.id, a.aid, a.channel_id, a.uid, a.rid, a.valid_start, a.valid_end, a.get_time,
        a.status, a.use_time, a.created, a.remark,
        b.phone, c.p_type, c.money, c.p_name, c.use_desc, c.refund_desc
        from cp_redpack_log a left join $join 
        left join cp_redpack c on c.id = a.rid
        {$where} ORDER BY a.get_time DESC,a.id DESC";
        $result = $this->BcdDb->query($select)->getAll();
        return $result;
    }
    
    /**
     * 参    数：$ids id
     * 作    者：shigx
     * 功    能：删除红包
     * 修改日期：2015.07.20
     */
    public function redpackDelete($ids)
    {
        $sql = "UPDATE cp_redpack_log SET `delete_flag`='1' WHERE id in('{$ids}')";
        return $this->master->query($sql);
    }
    
    /**
     * 检查红包是否被使用
     * @param unknown_type $ids
     */
    public function checkRedpackUsed($ids)
    {
        $count = $this->master->query("select count(1) from cp_redpack_log where id in('{$ids}') and status = '2'")->getOne();
        if($count > 0)
        {
            return true;
        }
        return false;
    }
     
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：返回活动名称
     * 修改日期：2016.01.27
     */
    public function getActivityName()
    {
       $sql = "SELECT id, a_name FROM cp_activity";
       return $this->BcdDb->query($sql)->getAll();
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：获取红包类型列表
     * 修改日期：2015.07.20
     */
    public function getRedpackType()
    {
        return $this->BcdDb->query("select * from cp_redpack_type where 1")->getAll();
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：获取版本列表
     * 修改日期：2015.07.20
     */
    public function getVersion()
    {
        return $this->BcdDb->query("select * from cp_app_version where 1")->getAll();
    }

    /**
     * 竞彩活动 - 竞彩活动列表概览
     */
    public function list_JcActivity($searchData, $page, $pageCount)
    {
        $where = " where 1";
        if(!empty($searchData['name']))
        {
            $where .= " and (b.uname = '{$searchData['name']}' or a.orderId='{$searchData['name']}')";
        }
        if($searchData['platform'] !== '')
        {
            $where .= " and a.buyPlatform = '{$searchData['platform']}'";
        }
        if(!empty($searchData['activity_issue']))
        {
            $where .= " and a.activity_issue = '{$searchData['activity_issue']}'";
        }
        if($searchData['start_r_m'] !== '' && $searchData['end_r_m'] !== '' && $searchData['start_r_m'] < $searchData['end_r_m'])
        {
            $searchData['start_r_m'] = ParseUnit($searchData['start_r_m']);
            $searchData['end_r_m'] = ParseUnit($searchData['end_r_m']);
            $where .= " and a.money >= '{$searchData['start_r_m']}' and a.money <= '{$searchData['end_r_m']}'";
        }
        $select = "SELECT a.uid, a.activity_id, a.id_card, b.uname as userName, a.activity_issue, a.orderId, a.money, a.status, a.bonus, a.pay_status, a.buyPlatform, a.created FROM cp_activity_jc_join a left join cp_user b on b.uid=a.uid" . $where . " ORDER BY a.id ASC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = "SELECT count(*) FROM cp_activity_jc_join a left join cp_user b on b.uid=a.uid" . $where;
        $total1 = "SELECT count(*) AS number, SUM(a.money) AS totalMoney, count(DISTINCT a.activity_issue) AS activity_issue FROM cp_activity_jc_join a left join cp_user b on b.uid=a.uid" . $where;
        $total2 = "SELECT SUM(a.money) AS totalPayMoney FROM cp_activity_jc_join a left join cp_user b on b.uid=a.uid" . $where . " and a.pay_status = 1";
        $total3 = "SELECT count(DISTINCT a.activity_issue) as activity_issue FROM cp_activity_jc_join a left join cp_user b on b.uid=a.uid" . $where . " and a.status != 0";
        $result = $this->BcdDb->query($select)->getAll();
        $count = $this->BcdDb->query($count)->getOne();
        $total1 = $this->BcdDb->query($total1)->getRow();
        $total2 = $this->BcdDb->query($total2)->getRow();
        $total3 = $this->BcdDb->query($total3)->getRow();
        return array($result, $count, $total1, $total2, $total3);
    }

    public function getJcMatchInfo()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    /**
     * 竞彩活动 - 新建活动
     */
    public function createJc($activityData)
    {
        $result = false;

        $select = "SELECT activity_id, activity_issue from cp_activity_jc_config where activity_id = ? and activity_issue = ?";
        $info = $this->master->query($select, array($activityData['activity_id'], $activityData['activity_issue']))->getRow();

        if(!empty($info))
        {
            return false;
        }

        $activityFields = array_keys($activityData);
        $sql = "insert cp_activity_jc_config(" . implode(',', $activityFields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $activityFields)) .", now())";

        $this->master->query($sql, $activityData);

        $lastId = $this->master->query("select last_insert_id()")->getOne();

        if(!empty($lastId))
        {
            $result = true;
        }
        return $result;
    }

    /**
     * 竞彩活动 - 活动管理
     */
    public function getAllJcInfo($searchData, $page, $pageCount)
    {
        
        $countSql = "SELECT count(*) FROM cp_activity_jc_config ";
        $select = "SELECT activity_id, activity_issue, mid, plan, lid, playType, startTime, pay_money, left_money, join_num, status, pay_status FROM cp_activity_jc_config ORDER BY id ASC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql1 = "SELECT COUNT(*) AS totalPeople, COUNT(DISTINCT activity_issue) AS totalActivity FROM cp_activity_jc_join";
        $totalSql2 = "SELECT SUM(money) AS totalPayMoney, COUNT(DISTINCT activity_issue) AS totalActivity FROM `cp_activity_jc_join` WHERE `status` = 1000 AND pay_status = 1";
        $count = $this->BcdDb->query($countSql)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        $total1 = $this->BcdDb->query($totalSql1)->getRow();
        $total2 = $this->BcdDb->query($totalSql2)->getRow();
        return  array($result, $count, $total1, $total2);
    }

    /**
     * 竞彩活动 - 加奖 - 创建活动
     */
    public function createJj($activityData)
    {
        $activityFields = array_keys($activityData);
        $sql = "insert cp_activity_jj_config(" . implode(',', $activityFields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $activityFields)) .", now())";

        $this->master->query($sql, $activityData);

        $lastId = $this->master->query("select last_insert_id()")->getOne();

        return $lastId;
    }

    /**
     * 活动 - 获取活动详情
     */
    public function getActivityInfo($activity_id)
    {
        $sql = "SELECT id, a_name, params, start_time, end_time, remark, delete_flag from cp_activity where id = ?";
        $info = $this->BcdDb->query($sql, array($activity_id))->getRow();
        return $info;
    }

    /**
     * 活动 - 检查是否有进行中的活动
     */
    public function checkActivityInfo($activityInfo)
    {
        $sql = "SELECT id, activity_id, lid, playType, startTime, endTime from cp_activity_jj_config where lid = ? and playType = ? and startTime <= ? and endTime >= ? and status = 0";
        $info = $this->master->query($sql, array($activityInfo['lid'], $activityInfo['playType'], $activityInfo['endTime'], $activityInfo['startTime']))->getRow();
        return $info;
    }

    /**
     * 活动 - 更新活动详情
     */
    public function updateActivityInfo($activityInfo)
    {
        $sql = "UPDATE cp_activity SET start_time = ?, end_time = ? where id = ?";
        return $this->master->query($sql, array($activityInfo['start_time'], $activityInfo['end_time'], $activityInfo['id']));
    }

    public function list_JjActivity($searchData, $page, $pageCount)
    {
        $where = " where 1";
        if(!empty($searchData['name']))
        {
            $where .= " and (u.uname = '{$searchData['name']}' or o.orderId='{$searchData['name']}')";
        }
        if($searchData['platform'] !== false && $searchData['platform'] >= 0)
        {
            $where .= " and o.buyPlatform = '{$searchData['platform']}'";
        }
        if(!empty($searchData['lid']))
        {
            $where .= " and o.lid = '{$searchData['lid']}'";
        }
        if($searchData['start_r_m'] !== '' && $searchData['end_r_m'] !== '' && $searchData['start_r_m'] < $searchData['end_r_m'])
        {
            $searchData['start_r_m'] = ParseUnit($searchData['start_r_m']);
            $searchData['end_r_m'] = ParseUnit($searchData['end_r_m']);
            $where .= " and o.money >= '{$searchData['start_r_m']}' and o.money <= '{$searchData['end_r_m']}'";
        }
        if(!empty($searchData['jj_id']))
        {
            $where .= " and j.jj_id = '{$searchData['jj_id']}'";
        }
        if(!empty($searchData['status']))
        {
            $where .= " and o.status = '{$searchData['status']}'";
        }
        else
        {
            $where .= " and o.status in ('40', '200', '240', '500', '510', '600', '1000', '2000')";
        }
        if($searchData['start_r_c'] !== '' && $searchData['end_r_c'] !== '' && $searchData['start_r_c'] < $searchData['end_r_c'])
        {
            $where .= " and o.created >= '{$searchData['start_r_c']}' and o.created <= '{$searchData['end_r_c']}'";
        }
        $total = "SELECT count(*) FROM `cp_activity_jj_order` AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId LEFT JOIN cp_user AS u ON o.uid=u.uid LEFT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id" . $where;
        $select = "SELECT j.jj_id, j.orderId, u.uname userName, o.lid, o.issue, o.created, o.money, o.margin, j.add_money, o.`status`, o.buyPlatform FROM `cp_activity_jj_order` AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId LEFT JOIN cp_user AS u ON o.uid=u.uid LEFT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id" . $where . " ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = "SELECT SUM(o.money) AS totalMoney, SUM(o.margin) AS totalMargin, SUM(j.add_money) AS totalAddMoney, COUNT(DISTINCT(o.uid)) AS totalPeople FROM `cp_activity_jj_order` AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId LEFT JOIN cp_user AS u ON o.uid=u.uid LEFT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id" . $where;
        $total = $this->BcdDb->query($total)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        $count = $this->BcdDb->query($count)->getRow();
        return array($result, $count, $total);
    }

    public function getAllJjInfo($searchData, $page, $pageCount)
    {
        $countSql = "SELECT count(DISTINCT(c.id)) AS activityNum, sum(j.add_money) AS totalMoney, count(DISTINCT(o.uid)) AS totalPeople FROM cp_activity_jj_order AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId RIGHT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id";
        $activitySql = "SELECT c.id, c.startTime, c.endTime, c.lid, c.playType, c.ctype, COUNT(DISTINCT(o.uid)) AS num, SUM(o.money) AS money, SUM(o.margin) AS margin, SUM(j.add_money) AS add_money FROM cp_activity_jj_order AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId RIGHT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id GROUP BY c.id ORDER BY c.id DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getRow();
        $result = $this->BcdDb->query($activitySql)->getAll();
        return array($count, $result);
    }

    public function getJjDetail($activityId)
    {
        $activitySql = "SELECT c.id, c.startTime, c.endTime, c.lid, c.playType, c.ctype, c.params, c.buyPlatform, COUNT(DISTINCT(o.uid)) AS num, SUM(o.money) AS money, SUM(o.margin) AS margin, SUM(j.add_money) AS add_money FROM cp_activity_jj_order AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId RIGHT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id WHERE c.id = ?";
        $result = $this->BcdDb->query($activitySql, array($activityId))->getRow();
        return $result;
    }
    
    public function getJjMoney($orderId)
    {
        $activitySql = "SELECT c.id, c.startTime, c.endTime, c.lid, c.playType, c.ctype, c.params, COUNT(DISTINCT(o.uid)) AS num, SUM(o.money) AS money, SUM(o.margin) AS margin, SUM(j.add_money) AS add_money FROM cp_activity_jj_order AS j LEFT JOIN cp_orders AS o ON j.orderId = o.orderId RIGHT JOIN cp_activity_jj_config AS c ON j.jj_id = c.id WHERE j.orderId = ?";
        $result = $this->BcdDb->query($activitySql, array($orderId))->getRow();
        return $result;
    }

    // 拉新 - 邀请人
    public function getLxInviter($searchData, $page, $pageCount)
    {
        $where = '';
        if(!empty($searchData['from_channel_id']))
        {
            $where .= " AND j.from_channel_id = {$searchData['from_channel_id']}";
        }
        if(!empty($searchData['phone']))
        {
            $where .= " AND i.phone = {$searchData['phone']}";
        }
        $countSql = "SELECT count(*) FROM (SELECT i.phone, j.from_channel_id, COUNT(*) FROM cp_activity_lx_join AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status = 2 GROUP BY j.puid,j.from_channel_id) t";
        $select = "SELECT i.phone, j.from_channel_id, COUNT(*) AS joinNum FROM cp_activity_lx_join AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status = 2 GROUP BY j.puid, j.from_channel_id ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql = "SELECT COUNT(*) FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status = 2";
        $count = $this->BcdDb->query($countSql)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        $total = $this->BcdDb->query($totalSql)->getOne();
        return array($count, $result, $total);
    }

    // 
    public function getLxInvitee($searchData, $page, $pageCount)
    {
        $where = '';
        if(!empty($searchData['phone']))
        {
            $where .= " AND j.phone = {$searchData['phone']}";
        }
        if(!empty($searchData['to_channel_id']))
        {
            $where .= " AND j.to_channel_id = {$searchData['to_channel_id']}";
        }
        if(!empty($searchData['start_r_m']) && !empty($searchData['end_r_m']))
        {
            $where .= " AND j.created >= '{$searchData[start_r_m]}' AND j.created <= '{$searchData[end_r_m]}'";
        }
        if(!empty($searchData['uname']))
        {
            $where .= " AND u1.uname = '{$searchData[uname]}'";
        }
        if(!empty($searchData['isReg']))
        {
            if($searchData['isReg'] == '1')
            {
                $where .= " AND j.status = 0";
            }
            elseif($searchData['isReg'] == '2')
            {
                $where .= " AND j.status >= 1";
            }
        }
        if(!empty($searchData['isBind']))
        {
            if($searchData['isBind'] == '1')
            {
                $where .= " AND j.status < 2";
            }
            elseif($searchData['isBind'] == '2')
            {
                $where .= " AND j.status = 2";
            }
        }
        if(!empty($searchData['puname']))
        {
            $where .= " AND u2.uname = '{$searchData[puname]}'";
        }
        $countSql = "SELECT count(*) FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where;
        $select = "SELECT j.phone, j.uid, u1.uname AS uname, j.created, j.to_channel_id, j.status, j.puid, u2.uname AS puname FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where . " ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        return array($count, $result);
    }

    // 拉新 - 获取奖品信息
    public function getlxPrize()
    {
        $sql = "SELECT id, name, lv, num FROM cp_activity_lx_prize WHERE delete_flag = 0";
        $info = $this->BcdDb->query($sql)->getAll();
        return $info;
    }
    
    // 新年活动 - 获取奖品信息
    public function getNewYearPrize()
    {
        $sql = "SELECT id, name, lv, num FROM cp_activity_year_prize WHERE delete_flag = 0";
        $info = $this->BcdDb->query($sql)->getAll();
        return $info;
    }

    // 拉新 - 更新奖品概率
    public function updatePrize($datas)
    {
        if (!empty($datas))
        {
            $upd = array('lv');
            $field = '';
            $fields = array_keys($datas[0]);
            foreach ($fields as $value)
            {
                $field .= $value.", ";
            }

            $field = substr($field, 0, -2);

            $sql = "insert cp_activity_lx_prize ({$field}) values ";
            foreach ($datas as $data)
            {
                $sql .= "(";
                foreach ($fields as $value)
                {
                    $sql .= "'{$data[$value]}', ";
                }
                $sql = substr($sql, 0, -2)."), ";
            }
            $sql = substr($sql, 0, -2);
            $sql .=  $this->onduplicate($fields, $upd);
            return $this->master->query($sql);
        }
    }
    
    // 新年活动 - 更新奖品概率
    public function updateNewYearPrize($datas)
    {
        if (!empty($datas))
        {
            $upd = array('lv', 'num');
            $field = '';
            $fields = array_keys($datas[0]);
            foreach ($fields as $value)
            {
                $field .= $value.", ";
            }
            
            $field = substr($field, 0, -2);
            
            $sql = "insert cp_activity_year_prize ({$field}) values ";
            foreach ($datas as $data)
            {
                $sql .= "(";
                foreach ($fields as $value)
                {
                    $sql .= "'{$data[$value]}', ";
                }
                $sql = substr($sql, 0, -2)."), ";
            }
            $sql = substr($sql, 0, -2);
            $sql .=  $this->onduplicate($fields, $upd);
            return $this->master->query($sql);
        }
    }

    public function getJjHover($lname = 'JCZQ')
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCJJ_HOVER']}$lname";
        $info = $this->cache->redis->hGetAll($ukey);
        return $info;
    }

    public function saveJjHover($lname, $hoverInfo)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCJJ_HOVER']}$lname";
        $info = $this->cache->redis->hMSet($ukey, $hoverInfo);
    }
    /**
     * [getPfRedPack 获取派发活动红包]
     * @author JackLee 2017-03-21
     * @return [type] [description]
     */
    public function getPfRedPack()
    {
        $select = 'SELECT p.id,p.p_type,p.c_type,p.use_desc,p.c_name,p.use_params,p.ismobile_used,p.hidden_flag,p.created,t.p_name FROM cp_redpack AS p LEFT JOIN cp_redpack_type AS t ON p.p_type = t.p_type WHERE p.aid=7  ORDER BY p.created DESC';
        $result = $this->BcdDb->query($select)->getAll();
        return $result;
    }

    /**
     * [storeRedPack 保存红包数据]
     * @author JackLee 2017-03-22
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function storeRedPack($params)
    {
        //验证是否存在
        $isExist = $this->checkIsExist($params);
        $flag = 0;//默认失败
        if($isExist)
        {
            $flag = 2; //存在
        }
        else
        {
            $fields = array_keys($params);
            $sql = "insert into cp_redpack(" . implode(',', $fields) . ", created)
            values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
            $this->master->query($sql,array_values($params));        
            $lastId = $this->master->query("select last_insert_id()")->getOne();
            if(!empty($lastId))
            {
                $flag = 1;//成功
            }
        }

        return $flag;
    }

    /**
     * [checkIsExist 检查红包的唯一性]
     * @author JackLee 2017-03-22
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkIsExist($params)
    {
        $sql  = 'SELECT * FROM cp_redpack WHERE aid = ? AND c_type = ? AND p_type = ? AND money = ? AND money_bar = ? AND ismobile_used = ? AND use_params = ?';
        $data = $this->master->query($sql,array($params['aid'],$params['c_type'],$params['p_type'],$params['money'],$params['money_bar'],$params['ismobile_used'],$params['use_params']))->getOne();
        $res = false;
        if(!empty($data))
        {
            $res = true;
        }

        return $res;
    }
    /**
     * [hideRedPack 更新红包隐藏状态]
     * @author JackLee 2017-03-23
     * @param  [type] $redPackIds [红包IDs]
     * @param  [type] $pType      [红包类型]
     * @return [type]             [description]
     */
    public function hideRedPack($redPackIds,$pType)
    {
        $ids = implode(',', $redPackIds);
        //更新取消隐藏和加上隐藏
        $hideSql = "UPDATE cp_redpack SET `hidden_flag`='1' WHERE id in(".$ids.")";
        if(empty($ids))
        {
            $moveHideSql = "UPDATE cp_redpack SET `hidden_flag`='0' WHERE  p_type ='{$pType}' AND  aid = 7";
        }else{
            $moveHideSql = "UPDATE cp_redpack SET `hidden_flag`='0' WHERE  id NOT IN(".$ids.") AND p_type ='{$pType}' AND  aid = 7"; 
        }
        $tag1 = $this->master->query($moveHideSql);
        $tag2 = true;
        if(!empty($ids)) $tag2 = $this->master->query($hideSql);
        if($tag1.$tag2) return true;
        return false;
    }
    /**
     * [storePullRedPack 派发处理]
     * @author JackLee 2017-03-23
     * @param  [type] $uids       [description]
     * @param  [type] $redPackIds [description]
     * @return [type]             [description]
     */
    public function storePullRedPack($uids,$params)
    {
        //开启事务处理
        $this->master->trans_start();
        $uids = is_array($uids) ? $uids : array($uids);
        //写入审核
        $tag1 = $this->storeRedPackAudit($uids,$params);
        if($tag1 === false) return false;
        $audit_id = $tag1;
        //写入派发记录 cp_redpack_send_log
        $tag2 = $this->storeRedPackSendLog($audit_id,$uids,$params);
        if($tag1 && $tag2)
        {
            $this->master->trans_complete();
            return $audit_id;
        }
        else
        {
            $this->master->trans_rollback();
            return false;
        }
    }
    /**
     * [storeRedPackSendLog 写入派发日志]
     * @author JackLee 2017-03-23
     * @param  [type] $audit_id   [description]
     * @param  [type] $uids       [description]
     * @param  [type] $redPackIds [description]
     * @return [type]             [description]
     */
    public function storeRedPackSendLog($audit_id,$uids,$params)
    {
        $tag = true;
        $values = array();
        foreach ($uids as $uid) 
        {
            foreach ($params['red_pack_id'] as  $rid) 
            {
                $num = $params['pack_num_'.$rid];
                $values[] = "('".$uid."','".$rid."','".$audit_id."','".$num."',now(),'".$params['validity']."')";
            }
        }
        $values = implode(',', $values);
        $sql = "INSERT INTO cp_redpack_send_log(uid,redpack_id,audit_id,num,created,validity) VALUES $values";
        $flag = $this->master->query($sql);
        if($flag === false) $tag = false;
        return $tag;
    }
    /**
     * [storeRedPackAudit 写入审核]
     * @author JackLee 2017-03-23
     * @param  [type] $uids       [description]
     * @param  [type] $params     [description]
     * @return [type]             [description]
     */
    public function storeRedPackAudit($uids,$params)
    {
        //dd($params);
        $tag = false;
        $countSql = "SELECT money AS m,p_type,id FROM cp_redpack  WHERE aid = 7 AND id IN ? ";
        $rows = $this->master->query($countSql,array($params['red_pack_id']))->getAll();
        $userNum = count($uids);
        //购彩总额
        $count_gc = 0;
        //充值总额
        $count_cz = 0;
        foreach ($rows as $k => $v) 
        {
            if($v['p_type'] == 3)
            {
                $count_gc += $v['m'] * $params['pack_num_'.$v['id']]/100;
            }
            else if($v['p_type'] == 2)
            {
                $count_cz += $v['m'] * $params['pack_num_'.$v['id']]/100;
            }
        }
        $count_gc = $count_gc * $userNum;
        $count_cz = $count_cz * $userNum;
        //写入统计记录 返回 ID
        $insertSql = 'INSERT INTO cp_redpack_audit(user_count,buy_money,recharge_money,message,created) value(?,?,?,?,now())';
        $insertTag = $this->master->query($insertSql,array($userNum,$count_gc,$count_cz,$params['message']));
        if($insertTag)
        {
            return $this->master->query("select last_insert_id()")->getOne();
        }

        return $tag;
    }
    /**
     * [checkUser 检验用户正确性]
     * @author JackLee 2017-03-23
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkUser($params)
    {
        if(is_array($params))
        {
            $tag = $this->checkbatchUser($params);
        }
        else
        {
            $tag = $this->checkOneUser($params);
        }

        return $tag;
    }
    /**
     * [checkbatchUser 批量检验用户正确性]
     * @author JackLee 2017-03-23
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function checkbatchUser($params)
    {
        //uid real_name uname
        $tag  = false;
        return $tag;
    }
    /**
     * [checkOneUser 单个用户验证正确性]
     * @author JackLee 2017-03-23
     * @param  [type] $search [description]
     * @return [type]         [description]
     */
    public function checkOneUser($search)
    {
        //检索关键词 user->uname或者
        $tag = false;
        //$sql = "SELECT u.uid FROM cp_user AS u LEFT JOIN cp_user_info AS i ON u.uid = i.uid WHERE ( u.uname='{$search}' or i.phone='{$search}' ) and i.userStatus = 0 ";
        $sql = "SELECT u.uid FROM cp_user AS u LEFT JOIN cp_user_info AS i ON u.uid = i.uid WHERE  u.uname='{$search}'   and i.userStatus = 0 
              UNION 
              SELECT u.uid FROM cp_user AS u LEFT JOIN cp_user_info AS i ON u.uid = i.uid WHERE  i.phone='{$search}'   and i.userStatus = 0 
              ";
        $uid = $this->master->query($sql)->getOne();
        if(!empty($uid))
        {
            $tag = $uid;
        }
        return $tag;
    }
    /**
     * [getAuditListData 获取红包派发审核列表]
     * @author JackLee 2017-03-24
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function getAuditListData($searchData, $page, $pageCount)
    {
        $where = " where 1";
        if($searchData['status'] == 3)
        {
            $where .= ' and a.status != 0';
        }else{
            $where .= $this->condition("a.status", $searchData['status']);
        }
        //获取批次ID
        if($searchData['uname'])
        {
            $audit_id = $this->BcdDb->query("SELECT audit_id FROM cp_redpack_send_log AS l LEFT JOIN cp_user AS u ON u.uid = l.uid WHERE u.uname ='{$searchData['uname']}'")->getOne();
            $audit_id = $audit_id ? $audit_id : 0;
            $where .= $this->condition("a.id", $audit_id);
        }
        $where .= $this->condition("a.created", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "datetime");
        $count = $this->BcdDb->query("SELECT count(*) FROM cp_redpack_audit AS a  {$where}")->getOne();
        $select = "SELECT * FROM cp_redpack_audit  AS a {$where} ORDER BY a.created DESC,a.id DESC
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->getAll();
        return array(
            $result,
            $count
        );  
    }
    /**
     * [getAuditDetail 获取审核详情]
     * @author JackLee 2017-03-23
     * @param  [type] $auditId [description]
     * @return [type]          [description]
     */
    public function getAuditDetailData($auditId)
    {
        $sql = 'SELECT  u.uname,u.uid,i.real_name, t.p_name,r.money,r.money_bar,r.use_desc,r.ismobile_used,r.use_params,l.num,l.created, a.status AS check_status,l.validity,l.status FROM cp_redpack_send_log AS l LEFT JOIN cp_user AS u ON u.uid = l.uid LEFT JOIN cp_user_info AS i ON i.uid = l.uid LEFT JOIN cp_redpack AS r ON r.id = l.redpack_id LEFT JOIN cp_redpack_type AS t ON t.p_type = r.p_type LEFT JOIN cp_redpack_audit AS a ON a.id = l.audit_id WHERE l.audit_id = ?';
        $result = $this->BcdDb->query($sql,array($auditId))->getAll();
        $count = $this->BcdDb->query("SELECT user_count,buy_money,recharge_money,message FROM cp_redpack_audit where id = ?", array($auditId))->getRow();
        return array('result' => $result,'count' => $count);
    }
    /**
     * [ajaxAudit 审核]
     * @author JackLee 2017-03-24
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function ajaxAudit($params)
    {
        $tag = false;
        $sql = "UPDATE cp_redpack_audit SET status = ? ,modified = now() WHERE id = ?";
        $res = $this->master->query($sql,array($params['status'],$params['id']));
        if($res) $tag = true;
        return $tag;
    }
    /**
     * [batchWrite description]
     * @author JackLee 2017-03-24
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function batchWrite($params)
    {
        $uids = array();
        $str_uids = '';
        $str_phone = '';
        $params_uids = array();
        foreach ($params as $k => $v){
            $params_uids[]  = $v[0];
            $str_uids .=  trim(iconv('GB2312', 'UTF-8', $v[0])).',';
            $str_phone .= "'".trim(iconv('GB2312', 'UTF-8', $v[1]))."'".',';
        }
        $str_uids = trim($str_uids,',');
        $str_phone = trim($str_phone,',');
        $sql = "select uid from cp_user_info where uid IN ({$str_uids}) and phone IN  ({$str_phone}) and userStatus = 0"; //查询所有符合条件的用户
        $tag  = $this->BcdDb->query($sql)->getAll();
        if($tag){
            foreach ($tag as $v){
                array_push($uids,$v['uid']);
            }
        }
        $error = array_diff($params_uids,$uids); //差集就是不存在的UID
        if(count($error)) return array('flag'=>-2,'uids'=>$error);//存在不合法用户
        return $uids;
        
    }
    /**
     * [getRedpackDataByIds description]
     * @author JackLee 2017-03-30
     * @param  [type] $redPackIds [description]
     * @return [type]             [description]
     */
    public function getRedpackDataByIds($redPackIds)
    {
        $sql = "SELECT p_name,c_name,ismobile_used,use_params,use_desc FROM cp_redpack WHERE id IN ?";
        return $this->BcdDb->query($sql,array($redPackIds))->getAll();
    }
    
    // 拉新 - 邀请人
    public function getNewLxInviter($searchData, $page, $pageCount)
    {
        $where = ' AND j.activity_id=9';
        if(!empty($searchData['from_channel_id']))
        {
            $where .= " AND j.from_channel_id = {$searchData['from_channel_id']}";
        }
        if(!empty($searchData['phone']))
        {
            $where .= " AND i.phone = {$searchData['phone']}";
        }
        $countSql = "SELECT i.phone, j.from_channel_id, COUNT(*) AS joinNum FROM cp_activity_lx_join AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status in (3,4) GROUP BY i.phone";
        $select = "SELECT i.phone, j.from_channel_id, COUNT(*) AS joinNum FROM cp_activity_lx_join AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status in (3,4) GROUP BY i.phone ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql = "SELECT COUNT(*) FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user_info AS i ON j.puid = i.uid WHERE 1" . $where . " AND j.status in (3,4)";
        $count = $this->BcdDb->query($countSql)->getAll();
        $result = $this->BcdDb->query($select)->getAll();
        $total = $this->BcdDb->query($totalSql)->getOne();
        return array($count, $result, $total);
    }
    
    // 新年活动 - 邀请人
    public function getNewYearActivity($searchData, $page, $pageCount)
    {
        $where = ' AND j.activity_id=13';
        if(!empty($searchData['phone']))
        {
            $where .= " AND i.phone = '{$searchData['phone']}'";
        }
        if(!empty($searchData['uname']))
        {
            $where .= " AND j.uname = '{$searchData['uname']}'";
        }
        if($searchData['platform'] != '')
        {
            $where .= " AND j.platform = '{$searchData['platform']}'";
        }
        $countSql = "SELECT sum(j.total) total, sum(reg_total) reg_total, sum(buy_num) buy_num, COUNT(*) AS joinNum FROM cp_activity_chj_user AS j LEFT JOIN cp_user_info AS i ON j.uid = i.uid WHERE 1" . $where;
        $select = "SELECT i.phone, j.uid, j.uname, j.platform, j.total_num, j.left_num, j.buy_num, j.total, j.reg_total FROM cp_activity_chj_user AS j LEFT JOIN cp_user_info AS i ON j.uid = i.uid WHERE 1" . $where . " ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getRow();
        $result = $this->BcdDb->query($select)->getAll();
        return array($result, $count);
    }
    
    public function getNewLxInvitee($searchData, $page, $pageCount)
    {
        $where = ' AND j.activity_id = 9';
        if(!empty($searchData['phone']))
        {
            $where .= " AND j.phone = {$searchData['phone']}";
        }
        if(!empty($searchData['to_channel_id']))
        {
            $where .= " AND j.to_channel_id = {$searchData['to_channel_id']}";
        }
        if(!empty($searchData['start_r_m']) && !empty($searchData['end_r_m']))
        {
            $where .= " AND j.created >= '{$searchData[start_r_m]}' AND j.created <= '{$searchData[end_r_m]}'";
        }
        if(!empty($searchData['uname']))
        {
            $where .= " AND u1.uname = '{$searchData[uname]}'";
        }
        if($searchData['status']!=='-1' && $searchData['status']!==false)
        {
            if($searchData['status'] ==1)
            {
                $where .= " AND j.status in (1,2,3,4,5) ";
            }else if($searchData['status'] ==2)
            {
                $where .= " AND j.status in (2,3,4,5) ";
            }else{
               $where .= " AND j.status = '{$searchData['status']}'";  
            }
            
        }
        if(!empty($searchData['puname']))
        {
            $where .= " AND u2.uname = '{$searchData[puname]}'";
        }
        $countSql = "SELECT count(*) FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where;
        $select = "SELECT j.phone, j.uid, u1.uname AS uname, j.created, j.to_channel_id, j.status, j.puid, u2.uname AS puname FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where . " ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        return array($count, $result);
    }
    
    /**
     * 新年活动  受邀人列表
     * @param unknown $searchData
     * @param unknown $page
     * @param unknown $pageCount
     * @return unknown[]
     */
    public function getNewYearInvitee($searchData, $page, $pageCount)
    {
        $where = ' AND j.activity_id = 13';
        if(!empty($searchData['phone']))
        {
            $where .= " AND j.phone = {$searchData['phone']}";
        }
        if(!empty($searchData['start_r_m']) && !empty($searchData['end_r_m']))
        {
            $where .= " AND j.created >= '{$searchData[start_r_m]}' AND j.created <= '{$searchData[end_r_m]}'";
        }
        if(!empty($searchData['uname']))
        {
            $where .= " AND u1.uname = '{$searchData[uname]}'";
        }
        if($searchData['status']!=='-1' && $searchData['status']!==false)
        {
            if($searchData['status'] ==1)
            {
                $where .= " AND j.status in (1,2,3,4,5) ";
            }else if($searchData['status'] ==2)
            {
                $where .= " AND j.status in (2,3,4,5) ";
            }else{
                $where .= " AND j.status = '{$searchData['status']}'";
            }
            
        }
        if(!empty($searchData['puname']))
        {
            $where .= " AND u2.uname = '{$searchData[puname]}'";
        }
        $countSql = "SELECT count(*) FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where;
        $select = "SELECT j.phone, j.uid, u1.uname AS uname, u1.created AS reg_created, j.created, j.to_channel_id, j.status, j.puid, u2.uname AS puname FROM `cp_activity_lx_join` AS j LEFT JOIN cp_user AS u1 ON j.uid = u1.uid LEFT JOIN cp_user AS u2 ON j.puid = u2.uid WHERE 1" . $where . " ORDER BY j.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getOne();
        $result = $this->BcdDb->query($select)->getAll();
        return array($count, $result);
    }
    
    public function updateActivityRemark($id, $ios_status, $app_status, $ios_content, $app_content)
    {
        $activity = $this->getActivityInfo(9);
        $status = json_decode($activity['remark'], true);
        $status['ios'] = $ios_status;
        $status['app'] = $app_status;
        $status['ios_content'] = $ios_content;
        $status['app_content'] = $app_content;
        $status = json_encode($status);
        $sql = "UPDATE cp_activity SET remark = ? where id = ?";
        return $this->master->query($sql, array($status, $id));
    }

    
    public function NewJcActivity($searchData, $page, $pageCount)
    {
        $where = " where a.status>=240 and o.created>'2017-12-15'";
        if(!empty($searchData['name']))
        {
            $where .= " and (b.uname = '{$searchData['name']}' or a.orderId='{$searchData['name']}')";
        }
        if($searchData['platform'] !== '' && $searchData['platform'] !== FALSE)
        {
            $where .= " and a.buyPlatform = '{$searchData['platform']}'";
        }
        if(!empty($searchData['activity_issue']))
        {
            $where .= " and a.jcbp_id = '{$searchData['activity_issue']}'";
        }
        if($searchData['start_r_m'] !== '' || $searchData['end_r_m'] !== '')
        {
            $searchData['start_r_m'] = ParseUnit($searchData['start_r_m']);
            $searchData['end_r_m'] = ParseUnit($searchData['end_r_m']);
            if ($searchData['start_r_m']) {
                $where .= " and a.money >= '{$searchData['start_r_m']}'";
            }
            if ($searchData['end_r_m']) {
                $where .= " and a.money <= '{$searchData['end_r_m']}'";
            }
        }
        if($searchData['start_r_c'] !== '' || $searchData['end_r_c'] !== '')
        {
            if ($searchData['start_r_c']) {
                $where .= " and o.pay_time >= '{$searchData['start_r_c']}'";
            }
            if ($searchData['end_r_c']) {
                $where .= " and o.pay_time <= '{$searchData['end_r_c']}'";
            }
        }
        if(!empty($searchData['hongbao']))
        {
            if($searchData['hongbao']=='1')
            {
                $where .= " and r.status = 2";
            }
            if($searchData['hongbao']=='2')
            {
                $where .= " and r.status < 2 and r.valid_end>now()";
            }
            if($searchData['hongbao']=='3')
            {
                $where .= " and r.status < 2 and r.valid_end<now()";
            }
            if($searchData['hongbao']=='4')
            {
                $where .= " and (a.status = 2000 or a.status<1000)";
            }
        }
        $select = "SELECT a.uid, a.jcbp_id, b.uname as userName, a.orderId, a.money, o.status, o.pay_time,a.pay_status, a.buyPlatform, r.rid,  r.status as hongbaostatus,r.valid_end FROM cp_activity_jcbp_join a left join cp_user b on b.uid=a.uid left join cp_orders o on o.orderId=a.orderId left join (SELECT uid,rid,STATUS,valid_end from cp_redpack_log where (rid=110 or rid=111)) r on r.uid=a.uid" . $where . " ORDER BY o.pay_time DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = "SELECT count(*) FROM cp_activity_jcbp_join a left join cp_user b on b.uid=a.uid left join cp_orders o on o.orderId=a.orderId left join (SELECT uid,rid,STATUS,valid_end from cp_redpack_log where (rid=110 or rid=111)) r on r.uid=a.uid" . $where;
        $result = $this->BcdDb->query($select)->getAll();
        $count = $this->BcdDb->query($count)->getOne();
        return array($result, $count);
    }
    
    public function getJcbpInfo()
    {
        $sql = "select * from cp_activity_jcbp_config where delete_flag=0 order by id desc";
        return $this->BcdDb->query($sql)->getAll();
    }
    
    public function getLastJcbp()
    {
        $sql = "select id from cp_activity_jcbp_config order by id desc limit 1";
        return $this->master->query($sql)->getRow();
    }

    public function getJclqMatchInfo()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }
    
    public function insertJcbp($data)
    {
        $sql = "insert into cp_activity_jcbp_config (lid,playType,payType,plan,startTime,endTime,buyMoney,created) values(?,?,?,?,?,?,?,now())";
        $this->master->query($sql, array($data['lid'], $data['playType'], $data['payType'], json_encode($data['plan']), $data['startTime'], $data['endTime'], $data['buyMoney']));
    }
    
    /**
     * 新年抽奖记录
     * @return unknown[]
     */
    public function newYearChjList($searchData, $page, $pageCount)
    {
        $where = ' AND activity_id = 13';
        if(!empty($searchData['uname']))
        {
            $where .= " AND uname = '{$searchData['uname']}'";
        }
        if(!empty($searchData['start_r_m']) && !empty($searchData['end_r_m']))
        {
            $where .= " AND created >= '{$searchData[start_r_m]}' AND created <= '{$searchData[end_r_m]}'";
        }
        if(!empty($searchData['award']))
        {
            $where .= " AND award_id = '{$searchData['award']}'";
        }
        $countSql = "SELECT COUNT(DISTINCT(uid)) total_uid, count(*) total FROM `cp_activity_chj_logs` WHERE 1" . $where;
        $select = "SELECT uid, uname, award_id, mark, created FROM `cp_activity_chj_logs`  WHERE 1" . $where . " ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $count = $this->BcdDb->query($countSql)->getRow();
        $result = $this->BcdDb->query($select)->getAll();
        return array($result, $count);
    }
    
    /**
     * 活动状态修改
     * @param unknown $id
     * @param array $data
     * @return unknown
     */
    public function activity_update($id, $data = array()){
        $this->master->where('id', $id);
        $this->master->update('cp_activity', $data);
        return $this->master->affected_rows();
    }

    // 排行榜 - 审核列表
    public function rankCheckList($searchData, $page, $pageCount)
    {
        $where = " created >= '{$searchData[start_time]}' AND created <= '{$searchData[end_time]}'";
        if($searchData['status'] == 1)
        {
            $where .= " AND cstate = 0";
        }
        elseif($searchData['status'] == 2)
        {
            $where .= " AND cstate > 0";
        }

        $countSql = "SELECT count(*) FROM cp_win_rank_check WHERE " . $where;
        $count = $this->master->query($countSql)->getOne();

        $select = "SELECT id, plid, pissue, lids, start_time, end_time, cstate, created FROM cp_win_rank_check WHERE " . $where . " ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->master->query($select)->getAll();
        return array($result, $count);
    }

    // 更新审核状态
    public function updateRankCheck($id, $status)
    {
        // 开启事务
        $this->master->trans_start();

        $info = $this->getRankCheckById($id);
        if(empty($info) || $info['cstate'] > 0)
        {
            $this->master->trans_rollback();
            $result = array(
                'status'    =>  FALSE,
                'message'   =>  '审核期次状态错误',
            );
            return $result;
        }

        if($status)
        {
            // 审核成功
            if($info['end_time'] < date("Y-m-d H:i:s"))
            {
                $this->master->trans_rollback();
                $result = array(
                    'status'    =>  FALSE,
                    'message'   =>  '活动结束时间小于当前时间',
                );
                return $result;
            }

            // 检查是否存在
            $config = $this->getRankConfigDetail($info['plid'], $info['pissue']);
            if(!empty($config))
            {
                $this->master->trans_rollback();
                $result = array(
                    'status'    =>  FALSE,
                    'message'   =>  '当前期次的进行活动已存在',
                );
                return $result;
            }

            // 记录cp_win_rank_config表
            $configData = array(
                'plid'                  =>  $info['plid'],
                'pissue'                =>  $info['pissue'],
                'lids'                  =>  $info['lids'],
                'start_time'            =>  $info['start_time'],
                'end_time'              =>  $info['end_time'],
                'statistics_end_time'   =>  $info['statistics_end_time'],
                'imgUrl'                =>  $info['imgUrl'],
                'rule'                  =>  $info['rule'],
                'extra'                 =>  $info['extra'],
            );

            $insertRes = $this->insertRankConfig($configData);
            if(!$insertRes)
            {
                $this->master->trans_rollback();
                $result = array(
                    'status'    =>  FALSE,
                    'message'   =>  '审核成功操作失败',
                );
                return $result;
            }
        }

        $status = $status ? '1' : '2';
        // 更新审核表状态
        $this->master->query("UPDATE cp_win_rank_check SET cstate = ? WHERE id = ? AND cstate = 0", array($status, $id));
        $updateRes = $this->master->affected_rows();
        if($updateRes)
        {
            $this->master->trans_complete();
            $result = array(
                'status'    =>  TRUE,
                'info'      =>  $info,
                'message'   =>  '审核操作成功',
            );
        }
        else
        {
            $this->master->trans_rollback();
            $result = array(
                'status'    =>  FALSE,
                'message'   =>  '审核操作失败',
            );
        }
        return $result;
    }

    public function getRankCheckById($id)
    {
        $sql = "SELECT id, plid, pissue, lids, start_time, end_time, statistics_end_time, imgUrl, rule, extra, cstate, created FROM cp_win_rank_check WHERE id = ? for update";
        return $this->master->query($sql, array($id))->getRow();
    }

    public function getRankConfigDetail($plid, $pissue)
    {
        $sql = "SELECT id, plid, pissue, lids, start_time, end_time, statistics_end_time, totalNum, totalMoney, totalMargin, totalAdd, imgUrl, rule, extra, cstate, created FROM cp_win_rank_config WHERE plid = ? AND pissue = ?";
        return $this->master->query($sql, array($plid, $pissue))->getRow();
    }

    public function insertRankCheck($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_win_rank_check(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
        return $this->master->query($sql, $info);
    }

    public function insertRankConfig($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_win_rank_config(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
        return $this->master->query($sql, $info);
    }

    public function rankList($searchData, $page, $pageCount)
    {
        $where = " plid = '{$searchData[plid]}'";
        // 活动状态
        if($searchData['status'] == '2')
        {
            $where .= " AND start_time > now()";
        }
        elseif($searchData['status'] == '3')
        {
            $where .= " AND start_time < now() AND end_time > now()";
        }
        elseif($searchData['status'] == '4') 
        {
            $where .= " AND end_time < now()";
        }
        // 派奖状态
        if($searchData['cstate'] == '2')
        {
            $where .= " AND cstate = 0";
        }
        elseif($searchData['cstate'] == '3')
        {
            $where .= " AND cstate = 1";
        }

        $countSql = "SELECT count(*) FROM cp_win_rank_config WHERE " . $where;
        $count = $this->master->query($countSql)->getOne();

        $select = "SELECT id, plid, pissue, lids, start_time, end_time, totalNum, totalMoney, totalMargin, totalAdd, cstate, created FROM cp_win_rank_config WHERE " . $where . " ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->master->query($select)->getAll();

        // 数据汇总
        $calSql = "SELECT count(*) as totalIssue, sum(totalAdd) as totalAdd, sum(totalNum) as totalNum FROM cp_win_rank_config WHERE " . $where;
        $total = $this->master->query($calSql)->getRow();
        return array($result, $count, $total);
    }

    public function checkRankConfig($info)
    {
        $sql = "SELECT id FROM cp_win_rank_check WHERE plid = ? AND cstate = 0";
        return $this->master->query($sql, array($info['plid']))->getRow();
    }

    public function getLastRankConfig($info)
    {
        $sql = "SELECT id, plid, pissue, start_time, end_time FROM cp_win_rank_config WHERE plid = ? ORDER BY pissue DESC LIMIT 1";
        return $this->master->query($sql, array($info['plid']))->getRow();
    }

    public function rankListDetail($searchData, $page, $pageCount)
    {
        $where = " plid = '{$searchData[plid]}' AND pissue = '{$searchData[pissue]}'";
        if(!empty($searchData['uname']))
        {
            $where .= " AND userName = '{$searchData[uname]}'";
        }

        $countSql = "SELECT count(*) FROM cp_win_rank_user WHERE " . $where;
        $count = $this->master->query($countSql)->getOne();

        $select = "SELECT id, plid, pissue, rankId, uid, userName, money, margin, addMoney, created FROM cp_win_rank_user WHERE " . $where . " ORDER BY id ASC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->master->query($select)->getAll();

        return array($result, $count);
    }
    
    public function getWorldcupRedpacks($search, $start, $limit) {
        $where = '';
        $sdata = array();
        if ($search['uname']) {
            $uid = $this->BcdDb->query('select uid from cp_user where uname = ?', array($search['uname']))->getOne();
            $where .= " and wrl.uid = ?";
            array_push($sdata, $uid);
        }
        if ($search['phone']) {
            $uid = $this->BcdDb->query('select uid from cp_user_info where phone = ?', array($search['phone']))->getOne();
            $where .= " and wrl.uid = ?";
            array_push($sdata, $uid);
        }
        if ($search['section']) {
            $where .= ' and wrl.section = ?';
            array_push($sdata, $search['section']);
        }
        if ($search['p_type']) {
            $where .= ' and r.p_type = ?';
            array_push($sdata, $search['p_type']);
        }
        if ($search['money']) {
            $where .= ' and r.money = ?';
            array_push($sdata, $search['money'] * 100);
        }
        if ($search['status']) {
            $where .= ' and rl.status = ?';
            array_push($sdata, $search['status']);
        }
        if ($search['platform']) {
            $where .= ' and rl.platform_id = ?';
            array_push($sdata, $search['platform'] - 1);
        }
        if ($search['get_time0'] && $search['get_time1']) {
            $where .= ' and rl.get_time > ? and rl.get_time < ?';
            array_push($sdata, $search['get_time0'], $search['get_time1']);
        }
        if ($search['use_time0'] && $search['use_time1']) {
            $where .= ' and rl.use_time > ? and rl.use_time < ?';
            array_push($sdata, $search['use_time0'], $search['use_time1']);
        }
        if ($search['valid_end0'] && $search['valid_end1']) {
            $where .= ' and rl.valid_end > ? and rl.valid_end < ?';
            array_push($sdata, $search['valid_end0'], $search['valid_end1']);
        }
        $total = $this->slave->query("select count(*) as num, count(distinct rl.uid) as unum, sum(r.money) as umoney
            from cp_redpack_log rl
            left join cp_worldcup_redpack_log wrl on rl.rid = wrl.rid and rl.uid = wrl.uid
            left join cp_redpack r on rl.rid = r.id
            where rl.aid = 14$where", $sdata)->getRow();
            $data = $this->BcdDb->query("select rl.uid, rl.get_time, wrl.section, r.money, r.p_type, rl.status, rl.platform_id, rl.use_time, rl.valid_end, r.use_desc
            from cp_redpack_log rl
            left join cp_worldcup_redpack_log wrl on rl.rid = wrl.rid and rl.uid = wrl.uid
            left join cp_redpack r on rl.rid = r.id
            where rl.aid = 14$where
            order by rl.created desc
            limit $start, $limit", $sdata)->getAll();
        $users = array();
        if (!empty($data)) {
            $uids = array();
            foreach ($data as $val) {
                array_push($uids, $val['uid']);
            }
            $uinfos = $this->BcdDb->query('select u.uname, ui.phone, u.uid
            from cp_user u join cp_user_info ui on u.uid = ui.uid
            where u.uid in ?', array($uids))->getAll();
            foreach ($uinfos as $uinfo) {
                $users[$uinfo['uid']] = $uinfo;
            }
        }
        return array('total' => $total, 'data' => $data, 'users' => $users);
    }
    
    public function insertQuestion($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_win_question_config(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
        $this->master->query($sql, $info);
        return $this->master->insert_id();
    }
    
    public function getRedCount()
    {
        $sql1 = "SELECT count(*) as count,rid,questionId as orderId,p_name,use_desc from(SELECT
	u.rid,
	u.questionId,
  l.`status`,
  r.p_name,r.use_desc
FROM
	cp_win_question_user u
LEFT JOIN cp_redpack_log l on u.uid=l.uid and u.rid=l.rid
LEFT JOIN cp_redpack r on u.rid=r.id
WHERE
u.rid>0
and l.aid=15
and l.get_time>'2018-05-01') a GROUP BY questionId,rid
";
        $sql2 ="SELECT count(*) as count,rid,questionId as orderId,p_name,use_desc,valid_start from(SELECT
	u.rid,
	u.questionId,
  l.`status`,
  r.p_name,r.use_desc,l.valid_start
FROM
	cp_win_question_user u
LEFT JOIN cp_redpack_log l on u.uid=l.uid and u.rid=l.rid
LEFT JOIN cp_redpack r on u.rid=r.id
WHERE
u.rid>0
and l.aid=15
and l.get_time>'2018-05-01' and l.`status`=2) a GROUP BY questionId,rid
";
        $res1 = $this->BcdDb->query($sql1)->getAll();
        $res2 = $this->BcdDb->query($sql2)->getAll();
        return array($res1, $res2);
    }

    public function getDtConfig($page, $num)
    {
        $sql = "select * from cp_win_question_config where 1 order by id desc limit " . (($page - 1) * $num) . ",$num";
        $all = $this->BcdDb->query($sql)->getAll();
        $count = $this->BcdDb->query("select count(*) as count from cp_win_question_config where 1")->getRow();
        return array('data' => $all, 'total' => $count['count']);
    }
    
    public function countDtUser()
    {
        $sql = "select count(*) as count from (SELECT id from cp_win_question_user where 1 group by uid) a";
        return $this->BcdDb->query($sql)->getRow();
    }
    
    public function getRedDesc($rid)
    {
        $sql = "select p_name,use_desc from cp_redpack where id = ?";
        return $this->BcdDb->query($sql, array($rid))->getRow();
    }
    
    public function closeQuestionActivity($id)
    {
        $sql = "update cp_win_question_config set status = 0 where id =?";
        return $this->master->query($sql, array($id));
    }

    public function createRedpack($money)
    {
        $sql = "select id from cp_redpack where aid = 15 and p_type = 1 and money = ?";
        $redpack = $this->BcdDb->query($sql, array($money * 100))->getRow();
        if (!empty($redpack)) {
            return $redpack;
        }
        $m = $money * 100;
        $use_params = json_encode(array('no_expire'=>1));
        $sql = "insert into cp_redpack (aid,p_type,c_type,money,p_name,use_params,use_desc,refund_desc,created) values(15,1,1,{$m},'{$money}元红包','{$use_params}','实名认证后可用','红包金额不可提现',NOW())";
        $this->master->query($sql);
        $id = $this->master->insert_id();
        return array('id' => $id);
    }

}
