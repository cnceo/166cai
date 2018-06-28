<?php

/**
 * 竞猜活动api model服务类
 * @author Administrator
 *
 */
class Api_Jchd_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 返回最近一期竞猜活动
     * @return unknown
     */
    public function getCurrentMatchs() {
        $sql = "select * from cp_activity_jchd_config where modified > date_sub(now(), interval 7 day) order by id desc limit 1";
        return $this->slave->query($sql)->getRow();
    }
    
    /**
     * 查询用户参与订单信息
     * @param unknown $uid
     * @param unknown $theme_id
     * @param unknown $issue
     * @return unknown
     */
    public function getJoinOrder($uid, $theme_id, $issue) {
        $sql = "select uid, theme_id, issue, orderId, status, code1, code2, code3, code4, 
        forecast_bouns, win_num, defeat_num, show_status from cp_activity_jchd_join where 
        uid = ? and theme_id = ? and issue = ?";
        
        return $this->slave->query($sql, array($uid, $theme_id, $issue))->getRow();
    }
    
    /**
     * 查询最近3天内首页弹窗数据
     * @param unknown $uid
     * @param unknown $theme_id
     * @param unknown $issue
     * @return unknown
     */
    public function getPopInfo($uid, $theme_id, $issue) {
        $sql = "select j.uid, j.theme_id, j.issue, j.orderId, j.status, j.code1, j.code2, 
        j.code3, j.code4, j.defeat_num, j.defeat_ratio, j.show_status, j.win_num, c.money, c.plan, c.bouns, join_num,
        u.headimgurl
        from cp_activity_jchd_join j 
        inner join cp_activity_jchd_config c on j.theme_id = c.theme_id and j.issue = c.issue
        left join cp_user_info u on u.uid = j.uid
        where j.modified > date_sub(now(), interval 3 day) and j.uid = ? and j.theme_id = ? 
        and j.issue < ? and j.status > 0 and j.show_flag = '0'
        order by j.id desc limit 1";
        $result = $this->slave->query($sql, array($uid, $theme_id, $issue))->getRow();
        if($result) {
            //如果有数据更新弹窗标识
            $this->db->query("update cp_activity_jchd_join set show_flag = 1 where orderId = ?", array($result['orderId']));
        }
        
        return $result;
    }
    
    /**
     * 根据主题id和期次查询活动信息
     * @param unknown $theme_id
     * @param unknown $issue
     * @return unknown
     */
    public function getJchdConfig($theme_id, $issue) {
        return $this->slave->query("select * from cp_activity_jchd_config where theme_id = ? and issue = ?", array($theme_id, $issue))->getRow();
    }
    
    /**
     * 提交订单
     * @param unknown $data
     * @return boolean
     */
    public function saveOrder($data) {
        $this->db->trans_start();
        $count = $this->db->query("select count(*) from cp_activity_jchd_join where uid = ? and theme_id = ? and issue = ? for update", array($data['uid'], $data['theme_id'], $data['issue']))->getOne();
        if ($count > 0) {
            $this->db->trans_rollback();
            return false;
        }
        
        $fields = array_keys($data);
        $sql = "insert ignore cp_activity_jchd_join(" . implode(',', $fields) . ", created)values(" .
        implode(',', array_map(array($this, 'maps'), $fields)) . ", now())";
        $this->db->query($sql, $data);
        $re = $this->db->affected_rows();
        $res = true;
        if($re > 0) {
            $res = $this->db->query("update cp_activity_jchd_config set join_num = join_num + 1 where theme_id = ? and issue = ?", array($data['theme_id'], $data['issue']));
            if(!$res) {
                $this->db->trans_rollback();
            }
        }
        
        $this->db->trans_complete();
        
        return $res;
    }
    
    /**
     * 查询用户参与记录
     * @param unknown $uid
     * @param unknown $theme_id
     * @return unknown
     */
    public function getUserOrders($uid, $theme_id) {
        $sql = "select j.uid, j.theme_id, j.issue, j.orderId, j.code1, j.code2, j.status oStatus,
        j.code3, j.code4, j.forecast_bouns, c.money, c.plan, c.bouns, c.start_time, c.end_time, c.award_time, c.status
        from cp_activity_jchd_join j 
        inner join cp_activity_jchd_config c on j.theme_id = c.theme_id and j.issue = c.issue 
        where j.uid = ? and j.theme_id = ? and c.status > 0 
        order by j.id desc";
        
        return $this->slave->query($sql, array($uid, $theme_id))->getAll();
    }
    
    /**
     * 查询大神榜前10名用户
     * @param unknown $theme_id
     * @param unknown $issue
     * @return unknown
     */
    public function getRankList($theme_id, $issue) {
        $sql = "select r.uid, r.theme_id, r.rank, r.issue_num, r.match_num, r.bouns, j.issue, j.code1, j.code2,
        j.code3, j.code4, j.orderId, j.forecast_bouns, u.uname from cp_activity_jchd_rank r 
        left join cp_activity_jchd_join j on j.uid = r.uid and j.issue = ?
        left join cp_user u on u.uid = r.uid
        where r.theme_id = ? order by r.rank asc limit 10";
        
        return $this->slave->query($sql, array($issue, $theme_id))->getAll();
    }
    
    /**
     * 根据uid查询用户排行信息
     * @param unknown $uid
     * @param unknown $theme_id
     * @param unknown $issue
     * @return unknown
     */
    public function getUserRank($uid, $theme_id, $issue) {
        $sql = "select r.uid, r.theme_id, r.rank, r.issue_num, r.match_num, r.bouns, j.issue, j.code1, j.code2,
        j.code3, j.code4, j.orderId, j.forecast_bouns from cp_activity_jchd_rank r 
        left join cp_activity_jchd_join j on j.uid = r.uid and j.issue = ?
        where r.uid = ? and r.theme_id = ? limit 1";
        
        return $this->slave->query($sql, array($issue, $uid, $theme_id))->getRow();
    }
    
    /**
     * 根据订单查询开奖信息
     * @param unknown $orderId
     * @return unknown
     */
    public function getOrderDetail($orderId) {
        $sql = "select j.uid, j.theme_id, j.issue, j.code1, j.code2, j.code3, j.code4, j.defeat_num, j.defeat_ratio, 
        j.show_status, j.win_num, c.money, c.plan, c.join_num, c.bouns_num, c.bouns
        from cp_activity_jchd_join j inner join cp_activity_jchd_config c 
        on c.theme_id = j.theme_id and c.issue = j.issue
        where j.orderId = ?";
        
        return $this->slave->query($sql, array($orderId))->getRow();
    }
}