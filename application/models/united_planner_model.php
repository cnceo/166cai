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
            $sql = "select uid,winningTimes,bonus,monthBonus,monthWinTimes,united_points from cp_united_planner where lid=? and uid=?";
            $planner = $this->slave->query($sql, array($lid, $uid))->getRow();
        }
        else
        {
            $sql = "select uid,sum(winningTimes) as winningTimes,sum(bonus) as bonus, united_points from cp_united_planner where lid in ? and uid=?";
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
        $sql = "select cp_united_planner.bonus,cp_user.uname,cp_united_planner.uid from cp_united_planner left join cp_user on cp_united_planner.uid=cp_user.uid where cp_united_planner.lid=0 and cp_united_planner.bonus>0 order by cp_united_planner.bonus desc limit ?";
        $planner = $this->slave->query($sql, array(intval($num)))->getAll();
        return $planner; 
    }

    /**
     * 获取关键词
     * @return array
     */
    public function getSensitiveWords()
    {
        $sql = "select word from cp_sensitive_words where status=0";
        return $this->slave->query($sql)->getAll();
    }
    /**
     * 获取关键词一列
     * @return array
     */
    public function getSensitiveWordsCol()
    {
        $sql = "select word from cp_sensitive_words where status=0";
        return $this->slave->query($sql)->getCol();
    }
    // 战绩数据迁移
    public function getPointsPlanner()
    {
        $sql = "SELECT uid FROM cp_user WHERE united_points > 0 ORDER BY uid ASC";
        return $this->slave->query($sql)->getAll();
    }

    public function getUserPointsByLid($uid)
    {
        $sql = "SELECT lid, SUM(united_points) AS points FROM cp_united_orders WHERE uid = ? AND united_points > 0 GROUP BY lid";
        return $this->slave->query($sql, array($uid))->getAll();
    }
    
    public function getUserGendan($uid, $arr)
    {
        $orderby = "order by bonus desc";
        switch ($arr) {
            case 00:$orderby="order by united_points desc";
                break;
            case 01:$orderby="order by united_points";
                break;
            case 10:$orderby="order by winningTimes desc";
                break;
            case 11:$orderby="order by winningTimes";
                break;
            case 20:$orderby="order by bonus desc";
                break;
            case 21:$orderby="order by bonus";
                break;
            case 30:$orderby="order by isFollowNum desc";
                break;
            case 31:$orderby="order by isFollowNum ";
                break;
            case 40:$orderby="order by followTimes desc";
                break;
            case 41:$orderby="order by followTimes ";
                break;            
            default :break;
        }
        $sql = "select lid,winningTimes,bonus,united_points,isFollowNum,followTimes from cp_united_planner where uid=? and lid>0 " . $orderby;
        return $this->slave->query($sql, array($uid))->getAll();
    }
    
    public function getUserInfo($uid, $lid)
    {
        if ($lid == 19) {
            $lid = 11;
        }
        if ($lid == 35) {
            $lid = 33;
        }
        $sql = "select u.uname,u.uid,p.lid,p.winningTimes,p.bonus,p.united_points,p.isFollowNum from cp_united_planner p left join cp_user u on p.uid=u.uid where p.uid=? and p.lid=?";
        return $this->slave->query($sql, array($uid, $lid))->getRow();
    }
    
    /**
     * 跟单大厅红人4个维度信息
     * @return array
     */
    public function getGenDanInfo()
    {
        $sql = "select p.uid,p.lid,p.bonus,p.united_points,p.isFollowNum,u.uname from cp_united_planner p left join cp_user u on p.uid=u.uid where p.lid in (42,43,11,19)  order by p.united_points DESC,p.bonus DESC limit 4";
        $jcUsers = $this->slave->query($sql)->getAll();
        $sql = "select p.uid,p.lid,p.bonus,p.united_points,p.isFollowNum,u.uname from cp_united_planner p left join cp_user u on p.uid=u.uid where p.lid in (33,35,51,23529,52,10022,23528) order by p.united_points DESC,p.bonus DESC limit 4";
        $szcUsers = $this->slave->query($sql)->getAll();
        $sql = "select o.uid,o.lid,o.orderBonus,p.bonus,p.isFollowNum,p.united_points,u.uname, o.uidlid from 
        (select uid,(case when lid=19 THEN 11 when lid=35 THEN 33 ELSE lid END) as lid, orderBonus, concat(uid,lid) as uidlid
        FROM cp_united_orders ORDER BY orderBonus DESC limit 20) o
        JOIN cp_united_planner p ON o.uid = p.uid and o.lid = p.lid
        JOIN cp_user u ON o.uid = u.uid
        GROUP BY o.uidlid order by o.orderBonus DESC 
        limit 4";
        $bigUsers = $this->slave->query($sql)->getAll();
        $points = 0;
        $bonus = 0;
        foreach ($bigUsers as $k=>$bigUser) {
            $points = $bigUser['united_points'];
            $bonus = $bigUser['orderBonus'];
            if ($points > $bigUsers[$k - 1]['united_points'] && $bonus==$bigUsers[$k - 1]['orderBonus']) {
                $user = $bigUsers[$k - 1];
                $bigUsers[$k - 1] = $bigUser;
                $bigUsers[$k] = $user;
            }
        }
        $sql = "select p.uid,p.lid,p.bonus,p.united_points,p.isFollowNum,p.monthBonus,u.uname from cp_united_planner p left join cp_user u on p.uid=u.uid where p.lid!=0 order by p.monthBonus DESC,p.united_points DESC limit 4";
        $jqUsers = $this->slave->query($sql)->getAll();
        return array($jcUsers, $szcUsers, $bigUsers, $jqUsers);
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
        $count = $this->slave->query("select count(p.id) as count from cp_united_planner p left join cp_user u on p.uid=u.uid  where" . $extsql, $params)->getRow();
        $sql .=" limit " . $offset . "," . $num;
        $users = $this->slave->query($sql, $params)->getAll();
        return array($count, $users);
    }

    // 最近发单时间补数据
    public function getPayTimePlanner()
    {
        $sql = "SELECT uid FROM cp_united_planner WHERE uid > 0 AND lastPayTime = '0000-00-00 00:00:00' AND lid = 0";
        return $this->slave->query($sql)->getAll();
    }

    public function getlastPayTime($uid)
    {
        $sql = "SELECT MAX(created) AS pay_time, lid FROM cp_united_orders WHERE uid = ? AND status >= 40 GROUP BY lid";
        return $this->slave->query($sql, array($uid))->getAll();
    }
}
