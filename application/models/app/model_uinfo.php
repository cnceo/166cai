<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 移动端 用户中心 模型层
 */
class Model_Uinfo extends MY_Model 
{

    public function __construct()
    {
        parent::__construct();
    }

    // 检查微信关联账户
    public function checkWxUnionid($unionid)
    {
        $sql = "SELECT uid, wx_unionid FROM cp_user_info WHERE wx_unionid = ?";
        return $this->db->query($sql, array($unionid))->getRow();
    }

    // 用户红包
    public function getUserRedpack($uid)
    {
        $sql = "(SELECT COUNT(*) AS num FROM cp_redpack_log WHERE uid = ? AND valid_start <= NOW() AND valid_end >= NOW() AND valid_end <= DATE_ADD(NOW(),INTERVAL 3 DAY) AND status = 1 AND delete_flag = 0)
        UNION ALL 
        (SELECT COUNT(*) AS num FROM cp_redpack_log WHERE uid = ? AND valid_start <= NOW() AND valid_end >= NOW() AND status = 1 AND delete_flag = 0) 
        UNION ALL 
        (SELECT COUNT(*) AS num FROM cp_redpack_log WHERE uid = ? AND valid_end >= NOW() AND status = 1 AND delete_flag = 0)";
        return $this->slave->query($sql, array($uid, $uid, $uid))->getAll();
    }
}
