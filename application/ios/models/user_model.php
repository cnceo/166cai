<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function saveUser($info)
	{
		$upd = array('last_login_time', 'visit_times', 'pword', 'salt', 'email', 'passid', 'nick_name', 'uname', 'activity_id', 'last_login_channel');
		$apd = array('visit_times');
		$fields = array_keys($info);
		$sql = "insert cp_user(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd, $apd);
		$res = $this->db->query($sql, $info);
		if($res)
		{
		    $this->freshUserInfo($info['uid']);
		}
		
		return $res;
	}

	public function getUserInfo($uid)
	{
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['USER_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$uinfo = $this->cache->redis->hGetAll($ukey);
		if(empty($uinfo) || empty($uinfo['uname']) || !isset($uinfo['salt']) || !isset($uinfo['headimgurl']))
		{
			$uinfo = $this->freshUserInfo($uid);
		}
		
		if((!empty($uinfo['uname'])) && empty($uinfo['grade']))
		{
		    $growth = $this->freshUserGrowth($uid);
		    $uinfo = array_merge($uinfo,$growth);
		}

		// 增加已绑定银行卡标识
		if(!empty($uinfo))
		{
			unset($uinfo['bank_id']);
			unset($uinfo['bank_province']);
			unset($uinfo['bank_city']);
			// 增加已绑定银行卡标识
			$binfo = $this->getBankInfo($uid);
			if(!empty($binfo))
			{
				$uinfo['bank_bind'] = 1;
			}
			else
			{
				$uinfo['bank_bind'] = 0;
			}
		}
                return $uinfo;
	}
	
	public function freshUserInfo($uid)
	{
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['USER_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$sql = "select m.uid, uname, passid, pword, salt, safe_grade, money, money_hide, blocked, email, last_login_time, visit_times, rebates_level, platform, m.created, m.last_login_channel, 
        n.real_name, uname as nick_name, n.nick_name_modify_time, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd, 
        n.bank_province, n.bank_city, n.msg_send, n.push_status, n.app_push, n.userStatus, n.headimg_status, n.headimgurl
		from cp_user m 
        left join cp_user_info n on m.uid = n.uid where m.uid = ?";
		$uinfo = $this->db->query($sql, array($uid))->getRow();
		if(!empty($uinfo))
		{
			$this->cache->redis->hMSet($ukey, $uinfo);
		}
		return $uinfo;
	}
	
	/**
	 * 刷新成长值缓存
	 * @param unknown $uid
	 * @return unknown
	 */
	public function freshUserGrowth($uid)
	{
	    $REDIS = $this->config->item('REDIS');
	    $ukey = "{$REDIS['USER_INFO']}$uid";
	    $this->load->driver('cache', array('adapter' => 'redis'));
	    //查询是否含有成长值相关数据
	    $selSQl = "select grade,grade_value,points,last_year_points,grade_days,cycle_start,cycle_end,grade_before,grade_after,rank,points_job_params FROM cp_user_growth where uid = ?";
	    $growth =  $this->db->query($selSQl, array($uid))->getRow();
	    if(!$growth)
	    {
	        $insertSql = "insert ignore cp_user_growth(uid, cycle_start, cycle_end,created) values (?, now(), ?, now())";
	        $this->db->query($insertSql, array($uid, date('Y-m-d', strtotime("+1 year")) . " 23:59:59"));
	        //获取
	        $growth =  $this->db->query($selSQl, array($uid))->getRow();
	    }
	    
	    $this->cache->redis->hMSet($ukey, $growth);
	    
	    return $growth;
	}

	//获取绑定银行卡信息
	public function getBankInfo($uid)
	{
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['BANK_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$binfo = unserialize($this->cache->redis->get($ukey));
		// if(empty($binfo))
		// {
		// 	$binfo = $this->freshBankInfo($uid);
		// }
		return $binfo;
	}

	//更新绑定银行卡信息
	public function freshBankInfo($uid)
	{
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['BANK_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$sql = "select id, bank_id, bank_type, bank_province, bank_city, is_default from cp_user_bank where uid = ? and delect_flag = 0 ORDER BY is_default DESC";
		$binfo = $this->db->query($sql, array($uid))->getAll();
		if(!empty($binfo))
		{
			$this->cache->redis->save($ukey, serialize($binfo), 0);
		}
		else
		{
			$this->cache->redis->save($ukey, serialize(array()), 0);
		}
		return $binfo;
	}

	//保存绑定信息
	public function saveUserBase($info)
	{
		$unique = array('id_card');
		foreach ($unique as $field)
		{
			if(!empty($info[$field]))
			{
				if($this->isIdCardRepeat($info['id_card']))
				{
					return false;
				}
				// 是否已绑定 id_card
				if($this->isIdCardBind($info['uid']))
				{
					return false;
				}
			}
		}
		$upd = array('real_name', 'nick_name', 'nick_name_modify_time', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
		'bank_city', 'pay_pwd', 'bind_id_card_time', 'birthday');
		if(!empty($info['id_card']))
		{
		    $info['bind_id_card_time'] = date('Y-m-d H:i:s');
		    $info['birthday'] = $this->getIDBirthday($info['id_card']);
		}
		
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
	
	/**
	 * 根据身份证id返回生日
	 * @param string $id_card 身份证id
	 * @return string
	 */
	public function getIDBirthday($id_card)
	{
	    $strlen = strlen($id_card);
	    if($strlen == 18)
	    {
	        $month = substr($id_card, 10, 2);
	        $day = substr($id_card, 12, 2);
	    }
	    else
	    {
	        $month = substr($id_card, 8, 2);
	        $day = substr($id_card, 10, 2);
	    }
	    
	    $result = $month . '-' . $day;
	    if($result == '02-29')
	    {
	        $result = '03-01';
	    }
	    
	    return $result;
	}
	
 	//发发奖短信
	public function sendSms($uid, $vdatas, $ctype, $phone=null)
	{
		if(!empty($uid)) $uinfo = $this->getUserInfo($uid);
		if(!empty($phone)) $uinfo['phone'] = $phone;
		$re = false;
		if(!empty($uinfo['phone']))
		{
			$this->load->helper('string');
			$message = $this->config->item('MESSAGE');
			// 短信内容 - 发送语音验证码
			$msg = $message['captche'];
			// 短信类型
			$smsType = $this->config->item('SMSTYPE');
			$smstype = $smsType[$ctype]['smsType'];
			if(empty($smstype)) $smstype = 8;
			// 短信渠道
			$position = $smsType[$ctype]['positionId'];
			if(empty($position)) $position = 120;
			$msg = str_replace(array_keys($vdatas), $vdatas, $msg);
			$re = $this->tools->sendSms($uid, $uinfo['phone'], $msg, $smstype, UCIP, $position);
		}
		return $re;
	}

    public function isPhoneRepeat( $phoneNumber )
    {
        $sql = 'SELECT id FROM cp_user_info WHERE phone = ?';
		$uinfo = $this->db->query($sql, array( $phoneNumber ))->getRow();
		if(!empty($uinfo))
		{
			return true;
		}
        return false;
    }

    public function isIdCardRepeat( $idCardNumber )
    {
    	$sql = 'SELECT count(*) FROM cp_user_info WHERE id_card = ?';
		$num = $this->db->query($sql, array( $idCardNumber ))->getOne();
		return ($num >= 5) ? true : false;
    }
    
    //保存登录记录
    public function loginRecord($record)
    {
    	$fields = array_keys($record);
		$sql = "insert cp_login_info(" . implode(',', $fields) . ", created)values(" . 
		implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" ;
		return $this->db->query($sql, $record);
    }
    
    public function checkUnique($fkey, $fval)
    {
    	$sql = "select count(*) from cp_user_info where $fkey = ?";
    	$num = $this->db->query($sql, array($fval))->getOne();
    	return ($num >= 1) ? 1 : 0; 
    }
    
    //刷新用户缓存
    public function refresh($del = 0, $uids=array())
    {
    	if(empty($uids))
    		$uids = $this->db->query("select uid from cp_user where 1")->getCol();
    	foreach ($uids as $uid)
    	{
    		if($del)
    		{
    			$this->delCache($uid);
    		}else 
    		{
    			$this->freshUserInfo($uid);
    		}
    	}
    }
    
    //删除用户缓存
    private function delCache($uid)
    {
    	$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['USER_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$this->cache->redis->delete($ukey);
    }
    
    //更新安全等级
    public function saveSafeGrade($uid, $grade)
    {
    	$this->db->query("update cp_user set safe_grade = ? where uid = ?", array($grade, $uid));
    	$this->freshUserInfo($uid);
    }

    /*
     * 根据用户名 uname 获取用户基本信息
     * @date:2015-01-05
     */
    public function get_uinfo_by_umane($umane){
    	if($umane){
    		$sql = "SELECT m.uid, m.passid, m.email, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE m.uname = ?";
			$uinfo = $this->db->query($sql, array($umane))->getRow();
    	}
    	return $uinfo;
    }

    /*
     * 根据用户名 passid 获取用户基本信息
     * @date:2015-01-05
     */
    public function get_uinfo_by_passid($passid){
    	if($passid){
    		$sql = "SELECT m.uid, m.uname, m.passid, m.email, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE m.passid = ?";
			$uinfo = $this->db->query($sql, array($passid))->getRow();
    	}
    	return $uinfo;
    }

    /*
     * 根据手机号、邮箱获取用户信息
     * @author:liuli
     * @date:2015-01-05
     */
    public function get_uinfo_by_type($data,$type){
    	if($type == 'phoneType'){
    		$sql = "SELECT m.uid, m.uname, m.passid, m.email,m.visit_times, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE n.phone = ?";
			$uinfo = $this->db->query($sql, array($data))->getRow();
    	}elseif($type == 'emailType'){
    		$sql = "SELECT m.uid, m.uname, m.passid, m.email,m.visit_times, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE m.email = ?";
			$uinfo = $this->db->query($sql, array($data))->getRow();
    	}
    	return $uinfo;
    }

    /*
     * 绑定邮箱 - 检查邮箱是否已经绑定过
     * @author:liuli
     * @date:2015-01-20
     */
    public function checkEmailBind($email){
    	$sql = "select uid from cp_user where email = ?;";
		$bindInfo = $this->db->query($sql, array($email))->getRow();
		return $bindInfo?1:0;
    }
    
    /**
     * 检查用户名是否已经存在
     * @param unknown_type $uname
     * @return number
     */
    public function checkUname($uname)
    {
    	$sql = "select uname from cp_user where uname = ?;";
    	$bindInfo = $this->db->query($sql, array($uname))->getRow();
    	return $bindInfo?1:0;
    }
    
    /**
     * 修改昵称操作
     * @param unknown_type $data
     */
    public function updateUname($data)
    {
        $olduname = $this->db->query("select uname from cp_user where uid = ?", array($data['uid']))->getOne();
    	//事务开始
    	$this->db->trans_start();
    	$sql = "update cp_user set uname = ? where uid =?";
    	$res = $this->db->query($sql, array($data['nick_name'], $data['uid']));
    	if($res)
    	{
    		$result = $this->db->query("update cp_user_info set nick_name_modify_time = ? where uid = ?", array($data['nick_name_modify_time'], $data['uid']));
    		if($result)
    		{
    			$this->db->trans_complete();
    			$this->freshUserInfo($data['uid']);
    			$this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 1, ?, ?, 1)", array($data['uid'], $olduname, $data['nick_name']));
    			return true;
    		}
    	}
    	
    	$this->db->trans_rollback();
    	return false;
    }

    /*
     * 统计红包参与情况
     * @date:2016-02-17
     */
    public function recordRedpack($uid, $phone)
    {
    	$this->db->query("update cp_activity_log set uid = ? where phone = ?", array($uid, $phone));
    }


    /*
     * 根据手机号、邮箱获取用户信息
     * @date:2015-01-05
     */
    public function getUinfoByType($data,$type)
    {
    	if($type == 'phone'){
    		$sql = "SELECT m.uid, m.uname, m.passid, m.email, m.visit_times, m.pword, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE n.phone = ?";
			$uinfo = $this->db->query($sql, array($data))->getRow();
    	}elseif($type == 'email'){
    		$sql = "SELECT m.uid, m.uname, m.passid, m.email, m.visit_times, m.pword, n.phone ,n.pay_pwd FROM cp_user m LEFT JOIN cp_user_info n ON m.uid = n.uid WHERE m.email = ?";
			$uinfo = $this->db->query($sql, array($data))->getRow();
    	}
    	return $uinfo;
    }

    /*
     * 获取用户中心个人信息
     * @author:liuli
     * @date:2015-10-30
     */
    public function getUcUserInfo($passid)
    {
        $postData = array(
            'uid' => $passid
        );
        $info_url = $this->config->item('get_uinfo');
        // 用户中心接口切换
        if(ENVIRONMENT != 'production')
        {
            $postData['HOST'] = 'login.2345.com';
        }
        $redata = $this->tools->request($info_url, $postData);
        $redata = unserialize($redata);
        return $redata;
    }

    public function isIdCardBind($uid)
    {
    	$uinfo = $this->getUserInfo($uid);
    	return (!empty($uinfo['id_card']))?true:false;
    }

    public function queryUserName($name)
    {
        $sql = "select uname from cp_user where uname like '%{$name}%' limit 5";
        $users = $this->slave->query($sql)->getAll();
        return $users;
    }
    
    public function getHotInfo($uid)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
    	$hotinfo = json_decode($this->cache->redis->hGet($this->REDIS['USER_HOT'], $uid), true);
    	if(empty($hotinfo)) $hotinfo = $this->freshHotInfo($uid);
    	return $hotinfo;
    }
    
    public function freshHotInfo($uid)
    {
        $sql = "select isHot,lid from cp_united_planner where uid= ?";
        $unitedInfos = $this->db->query($sql, array($uid))->getAll();
        $hotinfo = array();
        foreach ($unitedInfos as $unitedInfo)
        {
            $hotinfo['isHot_' . $unitedInfo['lid']] = $unitedInfo['isHot'];
        }
        $REDIS = $this->config->item('REDIS');
        $this->cache->hSet($this->REDIS['USER_HOT'], $uid, json_encode($hotinfo));
        return $hotinfo;
    }
    
    public function getId($uid, $type = 1)
    {
        if ($type == 1)
        {
            $sql = "select id,uname from cp_user where uid= ?";
        }
        else
        {
            $sql = "select uid,uname from cp_user where id= ?";
        }
        return $this->db->query($sql, array($uid))->getRow();
    }
    
    public function uploadImgConfig($plateForm)
    {
        $sql = "select headimg_config from cp_headimg_config where buyPlatform = ?";
        return $this->db->query($sql, array($plateForm))->getRow();
    }
    
    public function countUploadImg($uid, $date)
    {
        $sql = "select count(*) as count from cp_headimg_record where uid = ? and date= ?";
        return $this->db->query($sql, array($uid, $date))->getRow();
    }
    
    public function uploadImg($url, $uid)
    {
        $this->db->query("update cp_user_info set headimgurl = ?,headimg_status=1 where uid = ?", array($url, $uid));
        $this->freshUserInfo($uid);
        $date = date("Y-m-d");
        $this->db->query('insert into cp_headimg_record (uid,date,headimgurl,created) values(?,?,?,now())', array($uid, $date, $url));
    }
    
    public function getBirthDay($uid)
    {
        $sql = "select birthday from cp_user_info where uid = ?";
        return $this->slave->query($sql, array($uid))->getRow();
    }
    
    public function getPopStatus($uid)
    {
        $sql = "select uid,grade,pop_status from cp_user_growth where uid = ? and pop_status = 0";
        return $this->db->query($sql, array($uid))->getRow();
    }
    
    public function setPopStatus($uid)
    {
        $sql = "update cp_user_growth set pop_status = 1 where uid = ?";
        return $this->db->query($sql, array($uid));
    }
}
