<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 合买 - 红人 - 模型层
 */
class United_Planner_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 通过uid查询
     * @param int $uid
     * @param string $fileds
     * @param int $lid
     * @return array
     */
	public function findByUid($uid, $fileds = '', $lid = 0)
    {
        if (!is_array($lid))
        {
        	if ($lid == 35) $lid = 33;
        	if ($lid == 19) $lid = 11;
            $sql = "select uid,winningTimes,bonus,monthBonus,monthWinTimes,united_points from cp_united_planner where lid= ? and uid= ?";
            $planner = $this->slave->query($sql, array($lid, $uid))->getRow();
        }
        else
        {
        	foreach ($lid as &$l) {
        		if ($l == 35) $l = 33;
        		if ($l == 19) $l = 11;
        	}
            $sql = "select uid,sum(winningTimes) as winningTimes,sum(bonus) as bonus,united_points from cp_united_planner where lid in ? and uid= ?";
            $planner = $this->slave->query($sql, array($lid, $uid))->getRow();
        }
        return $planner;
    }

    /**
     * 查询部分红人信息
     * @param int $num
     * @return array
     */
    public function getPlanners($num)
    {
        $sql = "select cp_united_planner.bonus,cp_user.uname,cp_united_planner.uid from cp_united_planner left join cp_user on cp_united_planner.uid=cp_user.uid where cp_united_planner.isHot=1 and cp_united_planner.lid=0 order by cp_united_planner.bonus desc limit ?";
        $planner = $this->slave->query($sql, array(intval($num)))->getAll();
        return $planner; 
    }

    /**
     * 查询红人的发单状态
     * @param int $uid
     * @return array
     */
    public function findUserStatus($uid)
    {
        $sql = "select u.uname,p.isOrdering from cp_united_planner as p left join cp_user as u on p.uid=u.uid where p.lid=0 and p.uid= ?";
        $planner = $this->slave->query($sql, array($uid))->getRow();
        return $planner;
    }
    
    /**
     * 跟单大厅发起人查询
     * @param int $lid
     * @param array $data
     * @param int $offset
     * @param int $num
     * @return array
     */
    public function getAllUser($lid, $data, $offset, $num)
    {
        $params = array();
        $sql = "select p.uid,p.lid,p.bonus,p.united_points,p.isFollowNum,p.lastPayTime,u.uname,p.winningTimes,p.monthBonus,p.followTimes from cp_united_planner p left join cp_user u on p.uid=u.uid where";
        $extsql = "";
        if ($lid == 0) {
            $extsql .= " p.lid>0";
        } else {
            $extsql .= " p.lid=?";
            $params[] = $lid;
        }
        if ($data['type'] == 0) {
            $extsql .= " and p.isFollowNum<2000";
        } else {
            $extsql .= " and p.isFollowNum=2000";
        }
        if ($data['nickname']) {
            $extsql .= " and u.uname like ?";
            $params[] = "%{$data['nickname']}%";
        }
        $sql = $sql . $extsql;
        switch ($data['order']){
            case "00":$sql.=" order by p.united_points desc";
                break;
            case "01":$sql.=" order by p.united_points";
                break;     
            case "10":$sql.=" order by p.winningTimes desc";
                break;
            case "11":$sql.=" order by p.winningTimes";
                break;      
            case "20":$sql.=" order by p.bonus desc";
                break;
            case "21":$sql.=" order by p.bonus";
                break;         
            case "30":$sql.=" order by p.monthBonus desc";
                break;
            case "31":$sql.=" order by p.monthBonus";
                break;
            case "40":$sql.=" order by p.isFollowNum desc";
                break;
            case "41":$sql.=" order by p.isFollowNum";
                break;
            case "50":$sql.=" order by p.followTimes desc";
                break;
            case "51":$sql.=" order by p.followTimes";                
                break;            
        }
        $sql .=" limit " . $offset . "," . $num;
        $users = $this->slave->query($sql, $params)->getAll();
        return $users;
    }

    public function getAllFollow($uid, $lid)
    {
        if ($lid > 0) {
            $sql = "select uid,lid,isFollowNum from cp_united_planner where uid=? and lid=? and isFollowNum>0 order by isFollowNum desc";
            $users = $this->slave->query($sql, array($uid, $lid))->getAll();
        } else {
            $sql = "select uid,lid,isFollowNum from cp_united_planner where uid=? and lid>0 and isFollowNum>0 order by isFollowNum desc";
            $users = $this->slave->query($sql, $uid)->getAll();
        }
        return $users;
    }

    // 合买注人数、定制人数
    public function getCountFollowed($uid, $lid = 0)
    {
        $sql1 = "SELECT count(*) FROM cp_united_follow WHERE puid = ? AND follow_status = 1";
        $followCount = $this->slave->query($sql1, array($uid))->getOne();

        if($lid > 0)
        {
            $sql2 = "SELECT isFollowNum FROM cp_united_planner WHERE uid = ? AND lid = ?";
        }
        else
        {
            $sql2 = "SELECT SUM(isFollowNum) AS isFollowNum FROM cp_united_planner WHERE uid = ? AND lid <> ?";
        }
        
        $planner = $this->slave->query($sql2, array($uid, $lid))->getRow();
        return array('followCount' => $followCount, 'gendanCount' => $planner['isFollowNum']);
    }

    // 检查合买关注状态
    public function getFollowStatus($puid, $uid)
    {
        $sql = "SELECT id, puid, uid, follow_status FROM cp_united_follow WHERE puid = ? AND uid = ?";
        $info = $this->slave->query($sql, array($puid, $uid))->getRow();
        return ($info['follow_status'] == '1') ? '1' : '0';
    }

    // 合买宣言
    public function getUserDescription($uid)
    {
        $sql = "SELECT introduction, introduction_status FROM cp_user_info WHERE uid = ?";
        return $this->slave->query($sql, array($uid))->getRow();
    }
}
