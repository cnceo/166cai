<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：用户管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_user extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：ajax获取用户信息
     * 修改日期：2014.11.05
     */
    public function list_user($searchData, $page, $pageCount)
    {
        $where = " where 1";
        if ($this->emp($searchData['name']))
        {
            $where .= " and ({$this->cp_user}.email = '{$searchData['name']}'  or {$this->cp_u_i}.phone = '{$searchData['name']}' or {$this->cp_u_i}.id_card='{$searchData['name']}' or {$this->cp_u_i}.real_name='{$searchData['name']}'";
            if ($this->emp($searchData["islike"]))
            {
                $where .= " or {$this->cp_user}.uname like '%{$searchData['name']}%' or {$this->cp_u_i}.nick_name like '%{$searchData['name']}%') ";
            }
            else
            {
                $where .= " or {$this->cp_user}.uname = '{$searchData['name']}' or {$this->cp_u_i}.nick_name = '{$searchData['name']}') ";
            }
        }
        if ($this->emp($searchData['is_id_bind']))
        {
            $where .= " and {$this->cp_u_i}.id_card != ''";
        }
        if ($this->emp($searchData['is_bankcard_bind']))
        {
            $where .= " and {$this->cp_u_b}.bank_id != ''";
        }
        if ($this->emp($searchData['is_phone_bind']))
        {
            $where .= " and {$this->cp_u_i}.phone != ''";
        }
        if ($this->emp($searchData['is_email_bind']))
        {
        	$where .= " and {$this->cp_user}.email != ''";
        }
        if ($this->emp($searchData['platform']) && $searchData['platform'] != -1)
        {
            $where .= " and {$this->cp_user}.platform = ".$searchData['platform'];
        }
        if ($searchData['userLockStatus'] > 0)
        {
            $searchData['userLockStatus'] = $searchData['userLockStatus'] - 1;
            $where .= " and {$this->cp_u_i}.userStatus = ".$searchData['userLockStatus'];
        }
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where .= " and {$this->cp_user}.reg_type in ('0', '2')";
            }
            else
            {
                $where .= " and {$this->cp_user}.reg_type = ".$searchData['reg_type'];
            } 
        }
        $where .= $this->condition("{$this->cp_user}.channel", $searchData['registerChannel']);
        $where .= $this->condition("{$this->cp_user}.reg_reffer", $searchData['reg_reffer']);
        $where .= $this->condition("{$this->cp_user}.visit_times", array(
            $searchData['start_v_t'],
            $searchData['end_v_t']
        ), "during");
        $where .= $this->condition("{$this->cp_user}.created", array(
            $searchData['start_r_t'],
            $searchData['end_r_t']
        ), "time");
        $where .= $this->condition("{$this->cp_user}.last_login_time", array(
            $searchData['start_l_t'],
            $searchData['end_l_t']
        ), "time");
        $select1 = "SELECT COUNT(DISTINCT(cp_user.uid)) as count
                            FROM {$this->cp_user} LEFT JOIN {$this->cp_u_i} on {$this->cp_user}.uid = {$this->cp_u_i}.uid
                            LEFT JOIN {$this->cp_u_b} on {$this->cp_user}.uid = {$this->cp_u_b}.uid and {$this->cp_u_b}.is_default=1 and {$this->cp_u_b}.delect_flag=0 
                            {$where}";
        $count = $this->BcdDb->query($select1)->row();
        
        $select2 = "SELECT  {$this->cp_user}.*,{$this->cp_u_i}.real_name,{$this->cp_u_i}.nick_name,
                            {$this->cp_u_i}.phone,{$this->cp_u_i}.id_card,{$this->cp_u_b}.bank_id
                            FROM {$this->cp_user} LEFT JOIN {$this->cp_u_i} on {$this->cp_user}.uid = {$this->cp_u_i}.uid 
                            LEFT JOIN {$this->cp_u_b} on {$this->cp_user}.uid = {$this->cp_u_b}.uid and {$this->cp_u_b}.is_default=1 and {$this->cp_u_b}.delect_flag=0 
                            {$where} 
                            ORDER BY {$this->cp_user}.created DESC
                            LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select2)->row_array();
        return array(
            $result,
            $count->count
        );
    }
    
    /**
     * 参    数：$id 用户ID
     * 作    者：wangl
     * 功    能：根据ID查找用户基本信息
     * 修改日期：2014.11.05
     */
    public function find_user_by_id($id)
    {
        $select = "SELECT {$this->cp_user}.*,{$this->cp_u_i}.*,{$this->cp_user}.uid  FROM  {$this->cp_user}
                        LEFT JOIN {$this->cp_u_i} on {$this->cp_user}.uid = {$this->cp_u_i}.uid
                        WHERE {$this->cp_user}.uid = {$id}
                        LIMIT 1";
        $result = $this->BcdDb->query($select)->row_array();
        if (!empty($result))
        {
            $bank_info = explode("|", $result[0]['bank_id']);
            $result[0]['bank_id'] = $bank_info[0];
            $result[0]['bank_name'] = $bank_info[1];
        }
        return $result[0];
    }
    /**
     * [get_growth_by_uid 获取成长等级]
     * @author LiKangJian 2018-01-02
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function get_growth_by_uid($uid)
    {
        $sql = "select * FROM  cp_user_growth where uid =?";
        return  $this->BcdDb->query($sql,array($uid))->getRow();
    }
    
    /**
     * 参    数：$id 用户ID
     * 作    者：liuli
     * 功    能：根据ID查找用户绑定银行卡信息
     * 修改日期：2015.03.06
     */
    public function find_bank_by_id($id)
    {
        $select = "SELECT id, uid, bank_id, bank_type, bank_province, bank_city, is_default FROM  cp_user_bank
                        WHERE uid = {$id}
                        AND delect_flag = 0";
        $result = $this->BcdDb->query($select)->row_array();
        return $result;
    }
    
    /**
     * 参    数：$id 用户ID
     * 作    者：wangl
     * 功    能：查找用户登录信息
     * 修改日期：2014.11.05
     */
    public function get_login_info($id, $page, $pageCount)
    {
        $select = "SELECT l.*,c.name FROM {$this->cp_l_i} l left join cp_channel c on l.channel=c.id WHERE l.uid = {$id} order by l.created desc LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->row_array();
        $count = $this->BcdDb->query("SELECT COUNT(*) as count FROM {$this->cp_l_i} WHERE uid = {$id}")->row();
        return array(
            $result,
            $count->count
        );
    }
    /**
     * 参    数：$uid 用户ID
     * 作    者：wangl
     * 功    能：更新用户信息
     * 修改日期：2014.11.05
     */
    public function update_user_info($uid)
    {
        $this->master->where("uid", $uid);
        $this->master->update($this->cp_user, array(
            "is_need_code" => 0
        ));
        return $this->master->affected_rows();
    }
    
    public function freshUserInfo($uid)
    {
    	$REDIS = $this->config->item('REDIS');
    	$ukey = "{$REDIS['USER_INFO']}$uid";
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$sql = "select uname, passid, pword, safe_grade, money, money_hide, blocked, united_points, email, last_login_time, visit_times, rebates_level,platform,m.created,
    	n.real_name, uname as nick_name, n.nick_name_modify_time, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd,
    	n.bank_province, n.bank_city, n.msg_send, n.push_status, n.app_push, n.userStatus,n.headimg_status, n.headimgurl
    	from cp_user m
    	left join cp_user_info n on m.uid = n.uid where m.uid = ?";
    	$uinfo = $this->BcdDb->query($sql, array($uid))->getRow();
    	if(!empty($uinfo))
    	{
    		$this->cache->redis->hMSet($ukey, $uinfo);
    	}
    	
    	return $uinfo;
    }
    
    public function useRedbag($id)
    {
    	$this->master->trans_start();
    	$this->load->library('tools');
    	$sql = "SELECT l.uid, r.money, r.use_params FROM cp_redpack_log as l INNER JOIN cp_redpack as r on l.rid=r.id WHERE l.id = ? and l.status = 1 for update";
    	$res = $this->master->query($sql, array($id))->getRow();
    	$use_params = json_decode($res['use_params'], true);
    	if(!empty($res)) {
    		$sqlu = "select id, money, must_cost, dispatch from cp_user where uid = ? for update";
    		$resu = $this->master->query($sqlu, array($res['uid']))->getRow();
    		
    		$ms1 = $use_params['money_bar'] * $this->config->item('txed')/100 > $resu['must_cost'] ? $resu['must_cost'] : $use_params['money_bar'] * $this->config->item('txed')/100;
    		$ms2 = $use_params['money_bar'] > $resu['money'] ? $resu['money'] : $use_params['money_bar'];
    		$sqluser = "update cp_user set money = money + {$res['money']}, must_cost = must_cost - {$ms1} + {$ms2}, dispatch = dispatch + {$res['money']}
    		where uid = ?";
    		$resuser = $this->master->query($sqluser, array($res['uid']));
    		if (!$this->master->affected_rows()) {
    			$this->master->trans_rollback();
    			return false;
    		}
    		$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
    		$sqlwallet = "insert cp_wallet_logs(uid, ctype, mark, trade_no, money, umoney, created, content)
    		select uid, '9', '1', {$trade_no}, {$res['money']}, money, now(), '红包' from cp_user where uid = ?";
    		$reswallet = $this->master->query($sqlwallet, array($res['uid']));
    		if (!$this->master->insert_id()) {
    			$this->master->trans_rollback();
    			return false;
    		}
    		$sqlr = "update cp_redpack_log set status = '2', use_time=NOW() where id = ?";
    		$resr = $this->master->query($sqlr, array($id));
    		if (!$this->master->affected_rows()) {
    			$this->master->trans_rollback();
    			return false;
    		}
    		$this->freshUserInfo($res['uid']);
    		$this->master->trans_complete();
    		return true;
    	}
    	return false;
    }

    // 注销账户
    public function lockUser($uid, $userStatus = 1)
    {
    	$this->master->trans_start();
        $sql = "update cp_user_info set userStatus = ? 
                where uid = ? and userStatus in (0, 2)";
        $this->master->query($sql, array($userStatus, $uid));
        $row = $this->master->affected_rows();
        if ($row) {
        	$this->master->query("update cp_user_register set check_status = '1' where id=?", array($uid));
                if($userStatus == 1){
                    $this->master->query("update cp_user_info set wx_unionid = '' where uid=?", array($uid));
                }
        	// 刷新缓存
        	$rediskeys = $this->config->item("REDIS");
        	$this->load->driver('cache', array('adapter' => 'redis'));
        	if($this->cache->redis->hGet($rediskeys['USER_INFO'] . $uid, "uname"))
        	{
        		$this->cache->redis->hSet($rediskeys['USER_INFO'] . $uid, "userStatus", $userStatus);
        	}
        	else
        	{
        		$this->freshUserInfo($uid);
        	}
        	
        	$this->master->trans_complete($uid);
        	return $row;
        }
        $this->master->trans_rollback();
        return false;
        
        
    }

    // 获得可提资金
    public function getWithDraw($uid)
    {
        $subtract = "if((must_cost + dispatch) > chaseMoney, (must_cost + dispatch - chaseMoney), 0)";
        return $this->BcdDb->query("SELECT if(money >= $subtract, money - $subtract, 0) FROM cp_user WHERE uid = ?",
            array($uid))->getOne();
    }
    
    /**
     * 查询用户信息
     * @param int $uid
     * @param string $field
     * @return array
     */
    public function getUserInfo($uid, $field = '*')
    {
        $sql = "select {$field} from cp_user_info where uid='{$uid}'";
        return $this->master->query($sql)->getRow();
    }

    /**
     * 更新红人缓存
     * @param int $uid
     * @param int $hot
     * @param int $lid
     */
    public function freshUserHot($uid, $hot, $lid)
    {
        $sql = "select isHot,lid from cp_united_planner where uid= ?";
        $unitedInfos = $this->BcdDb->query($sql, array($uid))->getAll();
        $hotinfo = array();
        foreach ($unitedInfos as $unitedInfo)
        {
            $hotinfo['isHot_' . $unitedInfo['lid']] = $unitedInfo['isHot'];
        }
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->hSet($REDIS['USER_HOT'], $uid, json_encode($hotinfo));
        return $hotinfo;
    }

    public function getHasIntroduce($data,$page,$pageCount)
    {
        $where = " where cp_user_info.introduction!='' ";
        if ($data['name'] !== FALSE) {
            $where .= "and cp_user.uname like '%{$data['name']}%' ";
        }
        if ($data['check_status'] !== FALSE && $data['check_status'] != '-1') {
            $where .= "and cp_user_info.introduction_status = {$data['check_status']} ";
        }
        if($data['number'])
        {
            $where .= "and cp_user_info.introduction regexp '[0-9]' ";
        }
        if($data['words'])
        {
            $where .= "and cp_user_info.introduction regexp '[a-zA-Z]' ";
        }
        if($data['chinesenumer'])
        {
            $where .= "and cp_user_info.introduction regexp '一|二|三|四|五|六|七|八|九|十' ";
        }
        if($data['start_time'])
        {
            $start_time = date("Y-m-d H:i:s", strtotime($data['start_time']));
            $end_time = date("Y-m-d H:i:s", strtotime($data['end_time']));
            $where .= "and cp_user_info.introduction_time>='{$start_time}' and cp_user_info.introduction_time<='{$end_time}'";
        }
        $sql = "select cp_user_info.uid,cp_user.uname,cp_user_info.introduction,cp_user_info.sensitive_words,cp_user_info.introduction_status,cp_user_info.introduction_time from cp_user_info left join cp_user on cp_user_info.uid=cp_user.uid" . $where . "order by field(cp_user_info.introduction_status,0,2,3,1) LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $users = $this->BcdDb->query($sql)->getAll();
        $sql = "select count(cp_user_info.uid) as count from cp_user_info left join cp_user on cp_user_info.uid=cp_user.uid" . $where;
        $count = $this->BcdDb->query($sql)->getRow();
        return array(
            'users' => $users,
            'count' => $count['count']
        );
    }
    
    public function emptyIntroduce($uid)
    {
        $sql = "update cp_user_info set introduction_status=3 where uid={$uid}";
        return $this->master->query($sql);
    }
    /**
     * [handSucc 手动设置成功 敏感词被清空]
     * @author LiKangJian 2017-08-24
     * @return [type] [description]
     */
    public function handSucc($uid)
    {
        $sql = "update cp_user_info set introduction_status= ? ,sensitive_words = ? where uid= ?";
        return  $this->master->query($sql,array(1,'',$uid));
    }
    
    public function list_headimg($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        if ($searchData['uname']) {
            $where .= " AND u2.uname like '%{$searchData['uname']}%'";
        }
        if ($searchData['start_time'] && $searchData['end_time']) {
            $where .= " AND h.created >= '{$searchData['start_time']}' AND h.created <= '{$searchData['end_time']}'";
        }
        if ($searchData['forbidden']) {
            $where .= " AND u.headimg_status = 2";
        }
        $select = "select h.*,u2.uname,u.headimg_status,u.headimgurl from (select * from cp_headimg_record order by created desc) h left join cp_user_info  u on h.uid=u.uid left join cp_user u2 on u.uid=u2.uid {$where} group by h.uid ORDER BY h.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->db->query($select)->getAll();
        $count = $this->db->query("select count(*) as count from (SELECT COUNT(*) as count FROM cp_headimg_record h left join cp_user_info u on h.uid=u.uid left join cp_user u2 on u.uid=u2.uid {$where} group by h.uid) a")->getOne();
        return array(
            $result,
            $count
        );
    }

    public function getUploadConfig()
    {
        $sql = "select headimg_config from cp_headimg_config where 1 limit 1";
        return $this->BcdDb->query($sql)->getRow();
    }

    public function updateUploadConfig($type)
    {
        $sql = "update cp_headimg_config set headimg_config = ?";
        return $this->db->query($sql, array($type));
    }

    public function deleteImg($uid)
    {
        $sql = "update cp_user_info set headimgurl = '' where uid in ?";
        $this->db->query($sql, array($uid));
        $unames = array();
        foreach ($uid as $id) {
            $uninfo = $this->freshUserInfo($id);
            $unames[] = $uninfo['uname'];
        }
        return $unames;
    }

    public function forbiddenUpload($uid, $type)
    {
        $sql = "update cp_user_info set headimg_status = ? where uid=?";
        $this->db->query($sql, array($type, $uid));
        $uninfo = $this->freshUserInfo($uid);
        return $uninfo;
    }

    // 合买关注列表
    public function getUnitedFollowed($uid, $page, $pageCount)
    {
        $sql1 = "SELECT f.id, f.puid, u.uname FROM cp_united_follow AS f LEFT JOIN cp_user AS u ON f.puid = u.uid WHERE f.uid = ? AND f.follow_status = 1 ORDER BY f.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $list = $this->BcdDb->query($sql1, array($uid))->getAll();

        $sql2 = "SELECT count(*) FROM cp_united_follow WHERE uid = ? AND follow_status = 1";
        $count = $this->BcdDb->query($sql2, array($uid))->getOne();
        return array($list, $count);
    }

    // 合买取消关注
    public function cancelFollow($id)
    {
        $sql = "UPDATE cp_united_follow SET follow_status = 0 WHERE id = ?";
        return $this->master->query($sql, array($id));
    }
    
    public function list_uinfo_log($uid) {
        return $this->slave->query("select type, cbefore, cafter, place, created from cp_user_info_log where uid = ? order by created desc", array($uid))->getAll();
    }
    
    public function resetuname($uid) {
        $this->master->query("update cp_user_info set nick_name_modify_time = '0000-00-00 00:00:00' where uid = ?", array($uid));
        $this->freshUserInfo($uid);
    }

}
