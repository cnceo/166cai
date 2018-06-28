<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 活动 - 模型层
 * @date:2016-07-22
 */
class Activity_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

    /*
     * 拉新 - 获取中奖信息
     * @date:2016-07-22
     */
    public function getLxRecord()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['ACTIVITY_LX']));
        return $info;
    }

    public function getJoined($uid)
    {
        $sql = "select u.uname, j.modified from cp_activity_lx_join j
            left join cp_user u on j.uid=u.uid
            where puid = ? and status = 2 order by j.modified desc";
        return $this->slave->query($sql, array($uid))->getAll();
    }

    public function getchoujiang($uid) 
    {
        $sql = "select total_num, left_num from cp_activity_lx_user where uid = ?";
        return $this->slave->query($sql, array($uid))->getRow();
    }
    
    /**
     * 查询发起人抽奖次数
     * @param unknown $uid
     * @param unknown $activityId
     * @return unknown
     */
    public function getChjUser($uid, $activityId)
    {
        $sql = "select total_num, left_num from cp_activity_chj_user where activity_id = ? and uid = ?";
        $result = $this->slave->query($sql, array($activityId, $uid))->getRow();
        if(empty($result))
        {
            return array('total_num' => 0, 'left_num' => 0);
        }
        else
        {
            return $result;
        }
    }
    
    public function getInfo() {
    	$sql = "select start_time, end_time from cp_activity where id = 5";
    	return $this->slave->query($sql, array($uid))->getRow();
    }

    public function getChaseActivity()
    {
        return $this->slave->query("select startTime, endTime, issues from cp_activity_chase_config")->getAll();
    }

    public function getChaseActivityById($id)
    {
        return $this->slave->query("select startTime, endTime, issues from cp_activity_chase_config where id = ?", array($id))->getRow();
    }
    
    public function getTimeById($id)
    {
    	return $this->slave->query("select start_time as startTime, end_time as endTime, delete_flag from cp_activity where id = ?", array($id))->getRow();
    }
    
    public function getInvitation($id, $uid)
    {
        $sql = "SELECT cp_activity_lx_join.uid,cp_user.created
            FROM cp_activity_lx_join LEFT JOIN cp_user ON cp_activity_lx_join.uid = cp_user.uid
            WHERE cp_activity_lx_join.activity_id =?
            AND cp_activity_lx_join.puid =?
            AND cp_activity_lx_join. STATUS IN (3, 4)
            ORDER BY cp_user.created DESC
            LIMIT 10";
        $res = $this->slave->query($sql, array($id, $uid))->getAll();
        $sql = "select count(id) as count from cp_activity_lx_join where activity_id=? and puid=? and status in (3,4)";
        $count = $this->slave->query($sql, array($id, $uid))->getRow();
        return array($res, $count);
    }
    
    public function getRemarkById($id)
    {
    	return $this->slave->query("select remark from cp_activity where id = ?", array($id))->getRow();
    }

    public function getAppEvent($platform, $page, $number = 10)
    {
        $sql = "SELECT title, path, url, lid, weight, start_time, end_time, created FROM cp_app_event WHERE platform = ? AND delete_flag = 0 ORDER BY weight DESC LIMIT " . ($page - 1) * $number . "," . $number;
        return $this->slave->query($sql, array($platform))->getAll();
    }
    
    /**
     * 返回抽奖记录流水信息
     * @param unknown $uid
     * @param unknown $activityId
     */
    public function getChjLogs($uid, $activityId)
    {
        return $this->slave->query("select activity_id,uid,award_id,mark, created from cp_activity_chj_logs where activity_id = ? and uid = ? order by created desc", array($activityId, $uid))->getAll();
    }
    
    /**
     * 返回中奖记录列表
     * @param unknown $activityId
     * @return array
     */
    public function getPrizeList($activityId)
    {
        $datas = array();
        //默认数据
        $default = $this->slave->query("select uname, mark from cp_activity_chj_logs where activity_id = ? and uid = ? order by id asc limit 50", array($activityId, 0))->getAll();
        $data = $this->slave->query("select uname, mark from cp_activity_chj_logs where activity_id = ? and uid <> ? order by created desc,id desc limit 50", array($activityId, 0))->getAll();
        $count = count($data);
        if($count <= 22)
        {
            $default = array_slice($default, 0, 50-$count);
            $datas = array_merge($data, $default);
        }
        else
        {
            $default = array_slice($default, 0, 28);
            $data = array_slice($data, 0, 22);
            $datas = array_merge($data, $default);
        }
        
        return $datas;
    }
    
    public function getQuestionConfig()
    {
        $sql = "select id,questionUrl,titleDesc,start_time,end_time,rule,extra from cp_win_question_config where status =1 limit 1";
        $countSql = "select count(*) as count from cp_win_question_config where 1";
        $config = $this->slave->query($sql)->getRow();
        $count = $this->slave->query($countSql)->getRow();
        return array($config, $count);
    }

    public function queryRedpack($rid)
    {
        $sql = "select p_type,money,money_bar,p_name,use_desc from cp_redpack where id = ?";
        return $this->slave->query($sql, array($rid))->getRow();
    }

    public function insertQuestionUser($data)
    {
        $sql = "select rid from cp_win_question_user where uid=? and questionId=?";
        $record = $this->db->query($sql, array($data['uid'], $data['question']))->getRow();
        if (empty($record)) {
            $this->db->query("INSERT cp_win_question_user (questionId, uid, rid, created) VALUES (?, ?, ?, NOW())", array($data['question'], $data['uid'], $data['rid'], 'NOW()'));
            return 0;
        } else {
            return $record['rid'];
        }
    }

}
