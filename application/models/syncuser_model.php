<?php

class Syncuser_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    // 捞取未同步的用户
    public function getSynflagUser()
    {
        $sql = "select uid, passid from cp_user_temp where synflag = 0 order by uid asc limit 500";
        $info = $this->db->query($sql)->getAll();
        return $info;
    }

    // 记录用户信息
    public function insertUserTemp($info)
    {
        $upd = array('passid' ,'username', 'm_uid', 'gid', 'reg_ip', 'reg_time', 'login_ip', 'login_time', 'name', 'gender', 'bday', 'qq', 'area', 'email', 'email_status', 'phone', 'phone_redundancy');
        $fields = array_keys($info);
        $sql = "insert cp_user_temp(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        return $this->db->query($sql, $info);
    }

    // 更新同步状态
    public function updateSynflagUser($uid)
    {
        $sql = "update cp_user_temp set synflag = 1 where uid = ? and synflag = 0";
        $res = $this->db->query($sql, array($uid));
        return $res;
    }

    // 获取手机号、邮箱不为空的用户信息
    public function getAlreadyUserInfo()
    {
        $sql = "select uid, passid from cp_user_temp where synflag <= 1 order by uid asc limit 50";
        $info = $this->db->query($sql)->getAll();
        return $info;
    }

    // 覆盖原表用户信息
    public function updateUserInfo($uinfo)
    {
        if(!empty($uinfo['email']))
        {
            $this->db->query("update cp_user set email = ? where uid = ? and passid = ?", array($uinfo['email'], $uinfo['uid'], $uinfo['passid']));
            $this->freshUserCache($uinfo['uid']);
        }

        if(!empty($uinfo['phone']))
        {
            $cData = array(
                'uid' => $uinfo['uid'],
                'phone' => $uinfo['phone']
            );
            $this->SaveUserBase($cData);
        }
    }

    // 保存用户
    public function SaveUser($info)
    {
        $upd = array('last_login_time', 'visit_times', 'pword', 'email', 'passid', 'nick_name', 'uname', 'activity_id');
        $apd = array('visit_times');
        $fields = array_keys($info);
        $sql = "insert cp_user(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd, $apd);
        return $this->db->query($sql, $info);
    }

    // 保存用户
    public function SaveUserBase($info)
    {
        $unique = array('id_card');
        foreach ($unique as $field)
        {
            if(!empty($info[$field]))
            {
                if($this->checkUnique($field, $info[$field]))
                {
                    return false;
                }
            }
        }
        $upd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
        'bank_city', 'pay_pwd', 'bind_id_card_time');
        if(!empty($info['id_card']))
            $info['bind_id_card_time'] = date('Y-m-d H:i:s');
        
        $fields = array_keys($info);
        $sql = "insert cp_user_info(" . implode(',', $fields) . " ) values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  " )" . $this->onduplicate($fields, $upd);
        $this->db->trans_start();
        $re = $this->db->query($sql, $info);
        if($re)
        {
            $this->db->trans_complete();
            $this->freshUserInfo($info['uid']);
        }
        else
            $this->db->trans_rollback();
        return $re;
    }

    public function freshUserInfo($uid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "select uname, passid, pword, safe_grade, money, blocked, email, visit_times,platform,
        n.real_name, uname as nick_name, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd, 
        n.bank_province, n.bank_city
        from cp_user m 
        left join cp_user_info n on m.uid = n.uid where m.uid = ?";
        $uinfo = $this->db->query($sql, array($uid))->getRow();
        if(!empty($uinfo))
        {
            $this->cache->redis->hMSet($ukey, $uinfo);
        }
        return $uinfo;
    }

    // 检查
    public function checkUnique($fkey, $fval)
    {
        $sql = "select count(*) from cp_user_info where $fkey = ?";
        $num = $this->db->query($sql, array($fval))->getOne();
        return ($num >= 1) ? 1 : 0; 
    }

    // 刷新缓存
    public function freshUserCache($uid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "select uname, passid, pword, safe_grade, money, blocked, email, visit_times,platform,
        n.real_name, uname as nick_name, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd, 
        n.bank_province, n.bank_city
        from cp_user m 
        left join cp_user_info n on m.uid = n.uid where m.uid = ?";
        $uinfo = $this->db->query($sql, array($uid))->getRow();
        if(!empty($uinfo))
        {
            $this->cache->redis->hMSet($ukey, $uinfo);
        }
        return $uinfo;
    }

    public function updateSynflagUser2($uid)
    {
        $sql = "update cp_user_temp set synflag = 2 where uid = ? and synflag <= 1";
        $res = $this->db->query($sql, array($uid));
        return $res;
    }
    
}
