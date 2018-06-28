<?php

/*
 * 半自动化推送 模型层
 * @date:2018-01-10
 */

class Autopush_Model extends MY_Model 
{
    public function __construct() 
    {
        parent::__construct();
        $this->tplTable = "{$this->db_config['datatmp']}.cp_auto_push_user_tpl";
    }

    // 查询推送配置表
    public function getPushConfig()
    {
        $sql = "SELECT id, topic, config, ptype, rid, rname, title, content, action, url, status, lastId, created FROM cp_auto_push_config WHERE status = 1 ORDER BY id ASC;";
        return $this->data->query($sql)->getAll();
    }

    // 新建每日推送配置
    public function insertPushList($fields, $bdata)
    {
        $sql = "INSERT cp_auto_push_list(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
        $this->data->query($sql, $bdata['d_data']);
    }

    // 插入用户推送配置
    public function insertPushDetail($fields, $bdata)
    {
        $sql = "INSERT cp_auto_push_detail(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
        $this->data->query($sql, $bdata['d_data']);
    }

    // 更新推移字段
    public function updateLastId($ctype, $lastId = 0)
    {
        $sql = "UPDATE cp_auto_push_config SET lastId = ? WHERE id = ? AND $lastId > lastId";
        $this->data->query($sql, array($lastId, $ctype));
    }

    // 当前需要启动的任务
    public function getPushTask()
    {
        $sql = "SELECT l.id, l.pdate, l.ctype, l.cid, l.config, l.ptype, c.lastId FROM cp_auto_push_list AS l LEFT JOIN cp_auto_push_config AS c ON l.ctype = c.id WHERE l.pdate = DATE_FORMAT(NOW(), '%Y-%m-%d') AND l.cid > 0 ORDER BY id ASC";
        return $this->data->query($sql)->getAll();
    }

    // 注册未领红包
    public function getRegisterRedpack($lastId = 0)
    {
        $sql = "SELECT id, phone FROM cp_activity_log WHERE created < date_sub(now(), interval 30 MINUTE) AND created >= date_sub(now(), interval 1 day) AND id > ? AND aid = 8 AND uid IS NULL ORDER BY id ASC LIMIT 100";
        return $this->slave->query($sql, array($lastId))->getAll();
    }

    // 注册未实名
    public function getRegisterAuth($lastId = 0, $platform = '')
    {
        $sql = "SELECT u.id, u.uid, i.phone FROM cp_user AS u LEFT JOIN cp_user_info AS i ON u.uid = i.uid WHERE u.created < date_sub(now(), interval 30 MINUTE) AND u.created >= date_sub(now(), interval 1 day) AND u.id > ? AND i.bind_id_card_time = '0000-00-00 00:00:00' AND u.platform IN ({$platform}) ORDER BY u.id ASC LIMIT 100";
        return $this->slave->query($sql, array($lastId))->getAll();
    }

    // 实名未购彩 昨天一天内实名的
    public function getAuthWithdraw($platform = '')
    {
        $startTime = date('Y-m-d', strtotime('-1 day')) . ' 00:00:00';
        $endTime = date('Y-m-d', strtotime('-1 day')) . ' 23:59:59';
        $sql = "SELECT i.id, i.uid, i.phone FROM cp_user_info AS i LEFT JOIN cp_user AS u ON i.uid = u.uid LEFT JOIN cp_wallet_logs AS w ON i.uid = w.uid AND w.ctype = '0' AND w.mark = '1' WHERE i.bind_id_card_time >= '{$startTime}' AND i.bind_id_card_time <= '{$endTime}' AND u.platform IN ({$platform}) AND w.id IS NULL";
        return $this->slave->query($sql)->getAll();
    }

    // 查询可通知用户
    public function getPushToUser()
    {
        $sql = "SELECT d.id, d.ctype, d.cid, d.listId, d.uid, d.phone,l.pdate, l.config, l.ptype, l.rid, l.title, l.content, l.action, l.url FROM cp_auto_push_detail AS d LEFT JOIN cp_auto_push_list AS l ON d.listId = l.id WHERE d.startTime >= date_sub(now(), interval 30 MINUTE) AND d.startTime <= NOW() AND d.state = 0 AND d.cstate = 0 ORDER BY d.startTime ASC LIMIT 100";
        return $this->data->query($sql)->getAll();
    }

    // 检查 - 注册未领红包
    public function getUserByPhone($phone)
    {
        $sql = "SELECT uid FROM cp_user_info WHERE phone = ?";
        return $this->slave->query($sql, array($phone))->getRow();
    }

    // 检查 - 注册未实名、实名未购彩
    public function getUserByUid($uid)
    {
        $sql = "SELECT i.bind_id_card_time, w.id FROM cp_user_info AS i LEFT JOIN cp_wallet_logs AS w ON i.uid = w.uid AND w.ctype = '0' AND w.mark = '1' WHERE i.uid = ?";
        return $this->slave->query($sql, array($uid))->getRow();
    }

    // 获取红包信息
    public function getRedpackInfo($rid)
    {
        $sql = "SELECT id, aid, money, p_type, use_desc, use_params FROM cp_redpack WHERE id = ?";
        return $this->slave->query($sql, array($rid))->getRow();
    }

    // 记录红包
    public function recordRedpack($redpackData)
    {
        $fields = array_keys($redpackData);
        $redpackSql = "insert cp_redpack_log(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        return $this->db->query($redpackSql, $redpackData);
    }

    // 更新推送成功
    public function updateNoticeUser($info)
    {
        $sql = "UPDATE cp_auto_push_detail SET state = 1 WHERE phone = ? AND ctype = ? AND cid = ?";
        $this->data->query($sql, array($info['phone'], $info['ctype'], $info['cid']));
    }

    // 更新推送记录表涉及人数
    public function updateListInfo($info)
    {
        $sql = "UPDATE cp_auto_push_list SET totalNum = totalNum + 1 WHERE pdate = ? AND ctype = ? AND cid in (0, {$info['cid']})";
        $this->data->query($sql, array($info['pdate'], $info['ctype']));
    }

    // 更新统计表
    public function recordUserTpl($info)
    {
        $fields = array_keys($info);
        $sql = "insert {$this->tplTable}(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . " on duplicate key update listId = IF(listId >= values(listId), listId, values(listId)), cid = IF(cid >= values(cid), cid, values(cid)), cstate = 0;";
        return $this->data->query($sql, $info);
    }

    // 更新失效用户
    public function updateExpiredUser($info)
    {
        $sql = "UPDATE cp_auto_push_detail SET cstate = (cstate | 1) WHERE phone = ? AND ctype = ? AND cid >= ?";
        $this->data->query($sql, array($info['phone'], $info['ctype'], $info['cid']));
    }

    public function sendSms($info)
    {
        $this->load->library('tools');
        $this->tools->sendSms($info['uid'], $info['phone'], $info['msg'], 10, '127.0.0.1', 246);
    }

    public function deletePushTpl()
    {
        $this->data->query("DELETE FROM {$this->tplTable} WHERE pdate < DATE_FORMAT(date_sub(now(), interval 5 DAY), '%Y-%m-%d');");
    }

    // 查询已注册的用户
    public function getRegisterUser()
    {
        $sql = "SELECT t.pdate, t.ctype, t.cid, t.listId, t.phone, r.id FROM {$this->tplTable} AS t LEFT JOIN bn_cpiao.cp_user_register AS r ON t.phone = r.phone WHERE t.ctype = 1 AND t.cstate = 0 AND r.id > 0";
        return $this->data->query($sql)->getAll();
    }

    // 查询已实名的用户
    public function getAuthUser()
    {
        $sql = "SELECT t.pdate, t.ctype, t.cid, t.listId, t.phone FROM {$this->tplTable} AS t LEFT JOIN bn_cpiao.cp_user_info AS i ON t.uid = i.uid WHERE t.ctype in (2, 3, 4) AND t.cstate = 0 AND i.bind_id_card_time > '0000-00-00 00:00:00'";
        return $this->data->query($sql)->getAll();
    }

    // 查询已购彩的用户
    public function getRechargeUser()
    {
        $sql = "SELECT t.pdate, t.ctype, t.cid, t.listId, t.phone FROM {$this->tplTable} AS t LEFT JOIN bn_cpiao.cp_wallet_logs AS w ON t.uid = w.uid AND w.ctype = '0' AND w.mark = '1' WHERE t.ctype in (5, 6, 7) AND t.cstate = 0 AND w.id > 0 GROUP BY t.uid";
        return $this->data->query($sql)->getAll();
    }

    // 更新临时表cstate
    public function updateTplDetail($info)
    {
        $sql = "UPDATE {$this->tplTable} SET cstate = 1 WHERE ctype = ? AND phone = ?";
        $this->data->query($sql, array($info['ctype'], $info['phone']));
    }

    // 更新list表统计值
    public function updateListDetail($info, $fields)
    {
        $sql = "UPDATE cp_auto_push_list SET {$fields} = {$fields} + 1 WHERE pdate = ? AND ctype = ? AND cid in (0, {$info['cid']})";
        $this->data->query($sql, array($info['pdate'], $info['ctype']));
    }

    // 查询已实名、已购彩的用户
    public function getAuthRechargeUser()
    {
        $sql = "SELECT t.pdate, t.ctype, t.cid, t.listId, t.phone, t.uid, i.bind_id_card_time, w.id FROM {$this->tplTable} AS t LEFT JOIN bn_cpiao.cp_user_info AS i ON t.uid = i.uid LEFT JOIN bn_cpiao.cp_wallet_logs AS w ON t.uid = w.uid AND w.ctype = '0' AND w.mark = '1' WHERE t.uid > 0 AND t.cstate = 0 AND i.bind_id_card_time > '0000-00-00 00:00:00' AND t.status < 3";
        return $this->data->query($sql)->getAll();
    }
}
