<?php

/*
 * APP 小米推送 模型层
 * @author:liuli
 * @date:2017-05-24
 */

class Mipush_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    // 记录推送信息
    public function recordPushLog($fields, $bdata)
    {
        if(!empty($bdata['s_data']))
        {
            $sql = "insert cp_push_log(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
            $this->db->query($sql, $bdata['d_data']);
        }
    }

    // 获取最近未推送的信息
    public function getPushLog($mode = 1, $async = false)
    {
        switch ($mode) {
            case 2:
                $where = ' and ptype = 12';
                break;
            case 1:
            default:
                $where = ' and ptype <> 12';
                break;
        }
        $sql = "select id, ctype, content, url, status, platform, created, modified 
            from cp_push_log force index (created) 
            where created > date_sub(now(), interval 2 hour) and status = 0 and messageId < 3$where
            order by id asc 
            limit 500";
        $datas = $this->db->query($sql)->getAll();
        if($async && !empty($datas)){
            $ids = array();
            foreach ($datas as $data){
                array_push($ids, $data['id']);
            }
            $re = $this->db->query("update cp_push_log set status = 2 where id in ?", array($ids));
            if($re){
                return $datas;
            }
        }
        return $datas;
    }

    // 更新推送状态
    public function updatePushStatus($id, $messageId)
    {
        $sql = "update cp_push_log set status = 1, messageId = ? where id = ?";
        $this->db->query($sql, array($messageId, $id));
    }

    // 获取购彩推送配置
    public function getPushConfig($lid, $week)
    {
        $sql = "SELECT id, ctype, lid, week, title, content, send_time FROM cp_push_config WHERE lid = ? AND week = ? AND DATE_FORMAT(send_time,'%Y-%m-%d') <> CURDATE() AND status = 1 ORDER BY send_time ASC LIMIT 1";
        return $this->db->query($sql, array($lid, $week))->getAll();
    }

    // 更新处理时间
    public function updateSendTime($id)
    {
        $sql = "UPDATE cp_push_config SET send_time = now() WHERE id = ?";
        $this->db->query($sql, array($id));
    }

    public function getUserPushConfig($uid)
    {
        $sql = "SELECT uid, bet_push FROM cp_user_push_config WHERE uid = ?";
        return $this->db->query($sql, array($uid))->getRow();
    }

    public function saveUserPushConfig($info)
    {
        $upd = array('push_time', 'bet_push');
        $fields = array_keys($info);
        $sql = "insert cp_user_push_config(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        return $this->db->query($sql, $info);
    }

    // push_log表定期删除七天前数据
    public function deletePushLog()
    {
        $sql = "DELETE FROM cp_push_log WHERE modified <= date_sub(now(), INTERVAL 7 DAY) AND status = 1";
        return $this->db->query($sql);
    }
}
