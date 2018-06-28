<?php

class Register_Statistics_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function statistics($date)
    {
        $cleanSql = "DELETE FROM cp_register_stat WHERE cdate = '$date'";
        $this->db->query($cleanSql);

        $nextDay = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        //注意cp_user表中的platform字段，0为网站，1为App
        //映射到统计相关表中的platform字段，1为网站，2为App
        //不要问我为什么，这就是人生
        //TODO 检查运行效率
        $sql = "INSERT cp_register_stat(platform, channel_id, version, cdate, register_num)
            SELECT m.platform + 1, m.channel, IF(m.platform = 1, m.reg_reffer, 0) version, '$date', 1
            FROM cp_user m
            WHERE m.created >= '$date' AND m.created < '$nextDay'
            ON DUPLICATE KEY UPDATE register_num = register_num + VALUES(register_num)";
        $this->db->query($sql);

        $prevDay = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $sql = "INSERT cp_register_stat(platform, channel_id, version, cdate, valid_user)
            SELECT u.platform + 1, u.channel, IF(u.platform = 1, u.reg_reffer, 0) version, '$date', 1
            FROM cp_user_info i
            JOIN cp_user u ON i.uid = u.uid
            WHERE i.modified >= '$prevDay' AND i.bind_id_card_time >= '$date' AND i.bind_id_card_time < '$nextDay'
            ON DUPLICATE KEY UPDATE valid_user = valid_user + VALUES(valid_user)";
        $this->db->query($sql);

        $sql = "CREATE TABLE IF NOT EXISTS cp_user_bank_tmp (
            uid INT(11) NOT NULL DEFAULT 0,
            is_repeat TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE KEY uid (uid) USING BTREE
            ) ENGINE=InnoDB";
        $this->db->query($sql);

        $sql = "DELETE FROM cp_user_bank_tmp";
        $this->db->query($sql);

        $sql = "INSERT IGNORE cp_user_bank_tmp (uid)
            SELECT uid
            FROM cp_user_bank
            WHERE modified >= '$prevDay' AND created >= '$date' AND created <'$nextDay'";
        $this->db->query($sql);

        $sql = "UPDATE cp_user_bank b
            JOIN cp_user_bank_tmp t ON b.uid = t.uid
            SET t.is_repeat = 1 WHERE b.created < '$date'";
        $this->db->query($sql);

        $sql = "INSERT cp_register_stat(platform, channel_id, version, cdate, complete_user)
            SELECT u.platform + 1, u.channel, IF(u.platform = 1, u.reg_reffer, 0) version, '$date', 1
            FROM cp_user_bank_tmp t
            JOIN cp_user u ON t.uid = u.uid
            WHERE t.is_repeat = 0
            ON DUPLICATE KEY UPDATE complete_user = complete_user + VALUES(complete_user)";
        $this->db->query($sql);
    }
}
