<?php

/**
 * 摘    要：合买红人模型
 * 作    者：yindefu
 * 修改日期：2016.12.21
 */
class Model_united_planner extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /**
     * 查询所有的合买人
     * @param int $num
     * @param int $lid
     * @param string $username
     * @return array
     */
    public function getAllPlanner($lid, $username, $start, $offset)
    {
        $where = "where o.lid='{$lid}'";
        if ($username != '') $where.=" and {$this->cp_user}.uname like '%{$username}%'";
        $fields = "o.id, o.isHot, o.money, o.bonus, o.allTimes, o.succTimes, o.uid, o.lid, {$this->cp_user}.uname";
        if ($lid == 0) $fields .= ", (select sum(money) from cp_united_planner where uid=o.uid and lid in (51,23529,10022,52,23528,33)) as m,
        		(select sum(money) from cp_united_planner where uid=o.uid and lid in (11,42,43)) as j";
        $sql = "select {$fields} from cp_united_planner o inner join {$this->cp_user} on o.uid = {$this->cp_user}.uid {$where} order by o.isHot desc, o.money desc limit {$start}, {$offset}";
        $res = $this->BcdDb->query($sql)->getAll();
        $count = $this->BcdDb->query("select count(*) as num from cp_united_planner o inner join {$this->cp_user} on o.uid = {$this->cp_user}.uid {$where}")->getOne();
        return array($res, $count);
    }

    /**
     * 更新合买红人
     * @param int $id
     * @param int $lid
     * @param int $hot
     * @param int $judge
     * @return array
     */
    public function updatePlannerTop($id, $lid, $hot, $judge)
    {
        if ($hot == 1)
        {
            $sql = "select count(id) as num from cp_united_planner where lid= ? and cstate & 1 = 1 and isHot=1";
            $res = $this->master->query($sql, array($lid))->getRow();
            if ($res['num'] >= 7)
            {
                return array('status' => 'fail', 'message' => '至多添加7个合买红人');
            }
        }
        if ($judge != 1)
        {
            $sql = "update cp_united_planner set `isHot`= ?, cstate = (cstate | 1) & ? where id= ?";
            $this->master->query($sql, array($hot, $hot, $id));
            $res = $this->master->query("select {$this->cp_user}.uname, {$this->cp_user}.uid from cp_united_planner left join {$this->cp_user} on cp_united_planner.uid = {$this->cp_user}.uid where cp_united_planner.id= ?", array($id))->getRow();
        }
        $this->load->model('user_model');
        $this->user_model->freshHotInfo($res['uid']);
        return array('status' => 'success', 'message' => "{$res['uname']}");
    }

    /**
     * 通过uid,lid查询红人信息
     * @param int $uid
     * @param int $lid
     * @return array
     */
    public function findByUid($uid, $lid = 0)
    {
        $sql = "select * from cp_united_planner where uid= ? and lid= ?";
        $res = $this->BcdDb->query($sql, array($uid, $lid))->getRow();
        return $res;
    }

}

