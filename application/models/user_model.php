<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends MY_Model
{
	public $smstype = array(
		'captche' => 9,
		'win_prize' => 10,
		'win_prize_jj' => 10,
		'win_prize_jj_jxsyxw'=>10,
		'tick_fail' => 10,
		'lottery_fail' => 10,
	    'app_download' => 11,
		'166_huodong'  => 11,
		'166_hongbao'  => 10,
        'laxin'  => 10,
		'166_hongbao_1'  => 10,
		'166_hongbao_2'  => 10,
		'chase_complete' => 15,
		'chase_activity_complete' => 15,
		'order_draw' => 10,
		'order_drawpart' => 10,
		'order_concel' => 10,
		'join_huodong' => 10,
		'united_tick_fail' => 10,
		'united_win_prize' => 10,
		'order_drawpart_hongbao'   => 10,
		'order_concel_hongbao'     => 10,
		'united_follow_complete' => 10,
		'wechat_register' => 9,
	    'user_birth' => 10,
        'win_rank' => 11,
	);
	public function __construct()
	{
		parent::__construct();
	}
	
	public function SaveUser($info)
	{
		$upd = array('last_login_time', 'visit_times', 'pword', 'salt', 'email', 'passid', 'nick_name', 'uname', 'activity_id');
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
		if(empty($uinfo) || empty($uinfo['uname']) || !isset($uinfo['headimgurl']))
		{
			$uinfo = $this->freshUserInfo($uid);
		}
		if((!empty($uinfo['uname'])) && empty($uinfo['grade']))
		{
		    $growth = $this->freshUserGrowth($uid);
		    $uinfo = array_merge($uinfo,$growth);
		}
		
		return $uinfo;
	}
	
	public function freshUserInfo($uid)
	{
		$REDIS = $this->config->item('REDIS');
		$ukey = "{$REDIS['USER_INFO']}$uid";
		$this->load->driver('cache', array('adapter' => 'redis'));
		$sql = "select m.uid, uname, passid, pword, salt, safe_grade, money, money_hide, blocked, email, last_login_time, visit_times, rebates_level,platform,m.created, m.last_login_channel, 
        n.real_name, uname as nick_name, n.nick_name_modify_time, n.phone, n.id_card, n.bank_id, n.gender, n.qq, n.province, n.city, n.pay_pwd,
        n.bank_province, n.bank_city, n.msg_send, n.push_status, n.app_push, n.userStatus, n.headimg_status, n.headimgurl 
        from cp_user m 
        left join cp_user_info n on m.uid = n.uid 
        where m.uid = ?";
		$uinfo = $this->db->query($sql, array($uid))->getRow();     
        $this->cache->redis->hMSet($ukey, $uinfo);
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
		if(empty($binfo))
		{
			$binfo = $this->freshBankInfo($uid);
		}
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
		}else {
			$this->cache->redis->delete($ukey);
		}
		return $binfo;
	}

	//保存绑定信息
	public function SaveUserBase($info)
	{
		if(!empty($info['id_card']))
		{
			if($this->isIdCardRepeat($info['id_card']))
			{
				return false;
			}
		}
		$oldinfo = $this->db->query("select real_name, id_card from cp_user_info where uid = ?", array($info['uid']))->getRow();
		$place = (isset($info['isbck']) && $info['isbck'] == 1) ? 2 : 1;
		unset($info['isbck']);
		$upd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
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
			if (!empty($oldinfo)) {
			    if ($info['real_name'])
			        $this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 2, '{$oldinfo['real_name']}', ?, ?)", array($info['uid'], $info['real_name'], $place));
			    if ($info['id_card'])
			        $this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 3, '{$oldinfo['id_card']}', ?, ?)", array($info['uid'], $info['id_card'], $place));
			}
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
	        $month = substr($id_card,10,2);
	        $day = substr($id_card,12,2);
	    }
	    else
	    {
	        $month = substr($id_card,8,2);
	        $day = substr($id_card,10,2);
	    }
	    
	    $result = $month . '-' . $day;
	    if($result == '02-29')
	    {
	        $result = '03-01';
	    }
	    
	    return $result;
	}
 	//发发奖短信
	public function sendSms($uid, $vdatas, $ctype, $phone=null, $uip='0', $position = '16', $send = true)
	{
		if(!empty($uid)) $uinfo = $this->getUserInfo($uid);
		if(!empty($phone)) $uinfo['phone'] = $phone;
		$re = false;
		if(!empty($uinfo['phone']))
		{
			$this->load->helper('string');
			$message = $this->config->item('MESSAGE');
			switch ($ctype)
			{
				case 'win_prize':
				case 'win_prize_jj':
				case 'win_prize_jj_jxsyxw':
				case 'tick_fail':
				case 'app_download':
                case '166_huodong':
                case 'chase_complete':
                case 'chase_activity_complete':
				case 'lottery_fail':
				case 'order_draw':
				case 'order_drawpart':
				case 'order_concel':
				case 'order_drawpart_hongbao':
				case 'order_concel_hongbao':
				case 'united_tick_fail':
				case 'united_win_prize':
				case 'united_follow_complete':
				case 'user_birth':
                case 'win_rank':
					$vdatas['#MONEY#'] = number_format(ParseUnit($vdatas['#MONEY#'], 1), 2);
					$vdatas['#MONEY1#'] = number_format(ParseUnit($vdatas['#MONEY1#'], 1), 2);
					$vdatas['#MONEY2#'] = number_format(ParseUnit($vdatas['#MONEY2#'], 1), 2);
					$vdatas['#UNAME#'] = utf8_substr($uinfo['uname'], 0, 2) . '**';
					$vdatas['#MM#月#DD#日'] = date("m月d日", strtotime($vdatas['time']));
					$vdatas['#ADDS#'] = number_format(ParseUnit($vdatas['#ADDS#'], 1), 2);
                    $vdatas['#UNAME2#'] = $vdatas['#UNAME2#'];
                    $vdatas['#CONTENT#'] = $vdatas['#CONTENT#'];
					break;
				default:
					break;
			}
			$msg = $message[$ctype];
			$smstype = $this->smstype[$ctype];
			if(empty($smstype)) $smstype = 0;
			$msg = str_replace(array_keys($vdatas), $vdatas, $msg);
			if ($send) $re = $this->tools->sendSms($uid, $uinfo['phone'], $msg, $smstype, $uip, $position);
			else $re = array('uid' => $uid, 'phone' => $uinfo['phone'], 'content' => $msg, 'ctype' => $smstype, 'uip' => $uip, 'position' => $position);
		}
		return $re;
	}

    public function isPhoneRepeat( $phoneNumber )
    {
        $sql = 'SELECT id FROM cp_user_register WHERE phone = ?';
		$uinfo = $this->db->query($sql, array( $phoneNumber ))->getRow();
		if(!empty($uinfo))
		{
			return true;
		}
        return false;
    }

    public function isPhoneLocked( $phoneNumber )
    {
        $sql = 'SELECT r.id FROM cp_user_register r LEFT JOIN cp_user_info i ON r.id = i.uid WHERE r.phone = ? AND i.userStatus != 1';
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
    //单日内ip不超过两次
    public function isOldIp($ip)
    {
        if(in_array($ip,array('116.228.6.140','180.168.34.146')))
        {
            return false; 
        }
        $sql = "SELECT id FROM cp_sms_logs WHERE uip = ? AND created >\"".date('Y-m-d',time())." 00:00:00\"";
        $uinfo = $this->db->query($sql, array( $ip ))->getAll();
        if(count($uinfo) >= 2)
        {
            return true;
        }
        return false;
    }
    //单日内统一号码不超过三次
    public function isThreeTimes($phoneNum,$ctype)
    {
        $sql = "SELECT id FROM cp_sms_logs WHERE phone = ? AND ctype = ? AND created >\"".date('Y-m-d',time())." 00:00:00\"";
        $uinfo = $this->db->query($sql, array( $phoneNum, $this->smstype[$ctype]))->getAll();
        if(count($uinfo) >= 3)
        {
            return true;
        }
        return false;
    }
    //五分钟以内不准重复发送
    public function isInFiveMinute($phoneNum)
    {
        $sql = "SELECT created FROM cp_sms_logs WHERE phone = ? ORDER BY created desc";
        $uinfo = $this->db->query($sql, array( $phoneNum ))->getRow();
        $delTime = time()-strtotime($uinfo['created']);
        if($delTime <= 300)
        {
            return true;
        }
        return false;
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
     * @author:liuli
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
     * @author:liuli
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

    /*
     * 获取银行卡信息
     * @author:liuli
     * @date:2015-03-04
     */
    public function getBankDetail($info)
    {
    	try 
        {
        	$sql = "select id, uid, bank_id, bank_type, bank_province, bank_city, is_default from cp_user_bank where uid = ? and id = ? and delect_flag = 0";
			$binfo = $this->db->query($sql, array($info['uid'],$info['id']))->getRow();	
        }
        catch (Exception $e)
        {
            log_message('LOG', "getBankDetail error: " . __CLASS__ . ':' . __LINE__ , "ERROR");
        }
        return $binfo;
    }


    /*
     * 获取所有用户信息
     * @author:liuli
     * @date:2015-03-04
     */
    public function getAllBank()
    {
    	$sql = "select uid, bank_id, bank_province, bank_city from cp_user_info where bank_id<>0";
    	$res = $this->db->query($sql)->getAll();
    	return $res;
    }

    public function reFreshByStep($step)
    {
        $milestoneSql = "SELECT MIN(id) min, MAX(id) max FROM cp_user";
        $idRow = $this->db->query($milestoneSql)->getRow();
        $min = $idRow['min'];
        $max = $idRow['max'];
        $count = ceil(($max - $min) / $step);
        $startId = $min - 1;
        while ($count)
        {
            $endId = $startId + $step;
            $fetchSql = "SELECT uid FROM cp_user WHERE id > $startId AND id <= $endId";
            $uIds = $this->db->query($fetchSql)->getCol();
            if ($uIds)
            {
                $this->refresh($uIds);
            }
            unset($uIds);
            $startId = $endId;
            $count --;
        }
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

    /*
     * 检查用户信息是否存在
     * @author:liuli
     * @date:2015-11-3
     */
    public function checkUserInfo($uinfo)
    {
    	$result = false;
    	$sql = "SELECT uid FROM cp_user WHERE uid = ? and passid = ?";
    	$uinfo = $this->db->query($sql, array($uinfo['uid'], $uinfo['passid']))->getRow();
    	if(!empty($uinfo))
    	{
    		$result = true;
    	}
    	return $result; 
    }

    /*
     * 同步更新用户中心手机号码、邮箱
     * @author:liuli
     * @parmas type:email phone
     * @date:2015-10-26
     */
    public function freshUserCenterInfo($uinfo, $type)
    {
    	$result = true;

    	if(!$this->checkUserInfo($uinfo))
		{
			return true;
		}

    	switch ($type) 
    	{
    		case 'email':
    			$cData = array(
    				'uid' => $uinfo['uid'],
    				'email' => $uinfo['email']
    			);
    			$this->SaveUser($cData);
    			$this->freshUserInfo($uinfo['uid']);
    			break;
    		case 'phone':
    			$cData = array(
    				'uid' => $uinfo['uid'],
    				'phone' => $uinfo['phone']
    			);
    			$this->SaveUserBase($cData);
    			break;
    		default:
    			$result = false;
    			break;
    	}
    	return $result;
    }
    
    /**
     * 登录查询
     * @param string $userName	用户名或手机号
     * @param string $pword		密码MD5值
     */
    public function getLogin($userName, $pword)
    {
    	//目前用户名不能是纯数字 所以数据是纯数字并且长度是11位时 执行phone查询条件
    	if(is_numeric($userName) && strlen($userName) == 11)
    	{
            $sql = "SELECT u.uid,u.passid,u.uname,u.pword,u.salt FROM cp_user u
            INNER JOIN cp_user_register r ON r.id=u.uid
            WHERE r.phone= ?";
    	}
    	else
    	{
    	    $sql = "SELECT u.uid,u.passid,u.uname,u.pword,u.salt FROM cp_user u WHERE u.uname= ?";
    	}
    	$user = $this->slave->query($sql, array($userName))->getRow();
        if (!empty($user))
        {
            $newpword = $pword;
            if ($user['salt'])
            {
                $newpword = $pword . $user['salt'];
                $newpword = strCode($newpword, 'ENCODE');
            }
            if ($user['pword'] == $newpword)
            {
                return $user;
            }
        }
        return array();
    }
    
    /**
     * 检查cp_user表记录是否存在
     * @param unknown_type $fkey
     * @param unknown_type $fval
     * @return number
     */
    public function checkUserUnique($fkey, $fval)
    {
    	$sql = "select count(1) from cp_user where $fkey = ?";
    	$num = $this->db->query($sql, array($fval))->getOne();
    	return ($num >= 1) ? 1 : 0;
    }
    
    public function countLogin($isuname) {
    	$ctype = $isuname ? 'uname' : 'phone';
    	$sql = "update cp_login_count set count = count + 1 where ctype = ?";
    	return $this->db->query($sql, array($ctype));
    }

    /**
     * 检查cp_user_info记表录是否被注销
     * @param unknown_type $fkey
     * @param unknown_type $fval
     * @return number
     */
    public function checkUserInfoLocked($fkey, $fval)
    {
    	$sql = "SELECT u.uid FROM cp_user u LEFT JOIN cp_user_info i ON u.uid = i.uid WHERE u.{$fkey} = ? AND i.userStatus = '1'";
    	$num = $this->db->query($sql, array($fval))->getOne();
    	return ($num >= 1) ? 1 : 0;
    }
    
    /**
     * 查询用户名或者手机号是否被注册
     * @param unknown_type $uname
     * @param unknown_type $phone
     */
    public function getRegister($phone)
    {
    	$sql = "select count(1) from cp_user_register where phone = ?";
    	$num = $this->db->query($sql, array($phone))->getOne();
    	return ($num >= 1) ? 1 : 0;
    }
    
    /**
     * 执行注册方法
     * @param string $phone	手机号
     * @param array $user	用户信息
     */
    public function doRegister($phone, $user, $trans = true)
    {
    	$result = array(
    		'code' => false,
    		'uid' => 0,
    		'uname' => '',
    	);
    	// 事务开始
    	if ($trans) $this->db->trans_start();
    	// 生成彩票uid
    	$sql = "insert into cp_user_register(phone, created) values (? , now())";
    	$this->db->query($sql, array($phone));
    	$uid = $this->db->insert_id();
    	if($uid)
    	{
    		$user['uid'] = $uid;
    		$user['uname'] = 'user_' . date('ymdHis') . $uid;  // 默认用户名
    		$insertUser = $this->SaveUser($user);
    		if($insertUser)
    		{
    			$sql1 = "insert into cp_user_info(uid, phone, nick_name) values (? , ?, ?)";
    			$insertUserInfo = $this->db->query($sql1, array($uid, $phone, $user['uname']));
    			if($insertUserInfo)
    			{
    				if ($trans) $this->db->trans_complete();
    				$this->freshUserInfo($uid);
    				$result = array(
    					'code' => true,
    					'uid' => $uid,
    					'uname' => $user['uname'],
    				);
    				
    				return $result;
    			}
    		}
    	}
    	
    	if ($trans) $this->db->trans_rollback();
    	return $result;
    }
    
    /**
     * 根据手机号查询uid
     * @param unknown_type $phone
     */
    public function getUid($phone)
    {
    	$sql = 'SELECT id FROM cp_user_register WHERE phone = ?';
    	return $this->db->query($sql, array( $phone ))->getOne();
    }
    
    /**
     * 更新用户显示/隐藏金额字段
     * @param unknown_type $uid
     * @param unknown_type $moneyHide
     */
    public function updateMoneyHide($uid, $moneyHide)
    {
    	$sql = "update cp_user set money_hide=? where uid=?";
    	$res = $this->db->query($sql, array($moneyHide, $uid));
    	if($res)
    	{
    		$this->freshUserInfo($uid);
    		return true;
    	}
    	
    	return false;
    }
    
    public function ticketSms($sdate, $edate)
    {
    	$this->load->library('BetCnName');
    	// APP消息推送加载类
    	$this->load->library('mipush');
    	$position = $this->config->item('POSITION');
    	$sql = "(select o.uid, o.orderId, o.lid, o.status, o.money, o.failMoney, o.created, u.msg_send as msgSend, (u.msg_send & 1) msg_send, u.app_push, o.cstate, o.margin,d.redpackId,
    	 (o.my_status in(1,3) && (((o.activity_ids & 4)=0) || (((o.activity_ids & 4)=4) && ((o.activity_status&4)=4)))) act_flag,
    	 (((o.activity_ids & 4)=4) && ((o.activity_status&4)=4)) as act
		 from cp_orders as o
		 LEFT JOIN cp_orders_detail as d on d.orderId=o.orderId
		 INNER JOIN cp_user_info as u on o.uid=u.uid
		 where o.modified >= ? and o.modified < ? and o.status 
		 in ('510', '600') and ((o.cstate & 1) = 0) and o.orderType != 4 limit 10)
		 union
		(select o.uid, o.orderId, o.lid, o.status, o.money, o.failMoney, o.created, u.msg_send as msgSend, (u.msg_send & 1) msg_send, u.app_push, o.cstate, o.margin,d.redpackId,
		(o.my_status in(1,3) && (((o.activity_ids & 4)=0) || (((o.activity_ids & 4)=4) && ((o.activity_status&4)=4)))) act_flag,
		(((o.activity_ids & 4)=4) && ((o.activity_status&4)=4)) as act
		 from cp_orders as o
		 LEFT JOIN cp_orders_detail as d on d.orderId=o.orderId
		 INNER JOIN cp_user_info as u on o.uid=u.uid
		 where o.modified >= ? and o.modified < ? and o.status in ('500', '1000', '2000') and ((o.cstate & 1) = 0) and o.orderType != 4 limit 10)";
    	$orders = $this->db->query($sql, array($sdate, $edate, $sdate, $edate))->getAll();
    	while(!empty($orders))
    	{
    		foreach ($orders as $order)
    		{
    			if(($order['cstate'] & 1) == 0)
    			{
    				$msgArr = array(
    					500  => 'order_draw',
    					510  => 'order_drawpart',
    					600  => 'order_concel',
    					1000 => 'order_draw',
    					2000 => 'order_draw',
    				);
	    			$vdatas = array(
	    				'#MONEY#' => $order['money'],
	    				'#MONEY1#' => $order['money']-$order['failMoney'],
	    				'#MONEY2#' => $order['failMoney'],
	    				'#LID#' => BetCnName::getCnName($order['lid']),
	    				'time' => $order['created']
	    			);
	    			if (in_array($order['status'], array(510, 600)) || $order['msg_send']) {
	    				if(!empty($order['redpackId']) && $order['status'] =='600')
	    				{
	    					$msgArr[$order['status']] = 'order_concel_hongbao';
	    				}
	    				$this->sendSms($order['uid'], $vdatas, $msgArr[$order['status']], null, '127.0.0.1', $position[$msgArr[$order['status']]]);
	    			}
	    			$this->db->query("update cp_orders set cstate = cstate | 1 where orderId = ?", array($order['orderId']));

	    			// APP消息推送 出票失败 部分出票
	    			if(in_array($order['status'], array(510, 600)))
	    			{
			            $pushData = array(
			            	'type'      =>  $msgArr[$order['status']],
			            	'uid'       =>  $order['uid'],
			            	'lid'       => 	$order['lid'],
			                'lname'		=> 	BetCnName::getCnName($order['lid']),
			                'orderId'   => 	$order['orderId'],
			                'time'      => 	$order['created'],
			                'trade_no'  => 	'',
			            );
			            $this->mipush->index('user', $pushData);
	    			}
	    			// APP消息推送 出票成功 快慢频
	    			if($order['status'] == 500 && ((in_array($order['lid'], array('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421')) && ($order['app_push'] & 1) == 0) || (!in_array($order['lid'], array('53', '21406', '21407', '21408', '54', '55', '56', '57', '21421')) && ($order['app_push'] & 2) == 0)))
	    			{
			            $pushData = array(
			            	'type'      =>  $msgArr[$order['status']],
			            	'uid'       =>  $order['uid'],
			            	'lid'       => 	$order['lid'],
			                'lname'		=> 	BetCnName::getCnName($order['lid']),
			                'orderId'   => 	$order['orderId'],
			                'time'      => 	$order['created'],
			                'trade_no'  => 	'',
			            );
			            $this->mipush->index('user', $pushData);
	    			}
    			}
    		}
    		$orders = $this->db->query($sql, array($sdate, $edate, $sdate, $edate))->getAll();
    	}
    }
    
    public function updateUserMsgsend($params)
    {
    	// 状态标志位
    	$msgType = array(
    		'phone'		=>	1,	// 出票成功短信开启位
    		'email'		=>	2,	// 出票成功邮件开启位
    		'win_prize'	=>	4,	// 中奖短信关闭位
                'chase_prize'	=>	8,	// 中奖短信关闭位
                'gendan_prize'	=>	16,	// 中奖短信关闭位
    	);
    	$msgSend = $msgType[$params['type']];
    	$sql = "update cp_user_info set msg_send = (msg_send ^ ?) 
    			where uid = ?";
    	$this->db->query($sql, array($msgSend, $params['uid']));
    	$row = $this->db->affected_rows();
    	$this->freshUserInfo($params['uid']);
    	return $row;
    }

    /**
     * 修改中奖推送
     */
    public function updateUserPushsend($params)
    {
    	$sql = "update cp_user_info set push_status = ? 
    			where uid = ?";
    	$res = $this->db->query($sql, array($params['push_status'], $params['uid']));
    	$this->freshUserInfo($params['uid']);
    	return $res;
    }

    /**
     * 修改用户手机号码
     */
    public function modifyUserPhone($userData)
    {
    	// 事务开始
        $this->db->trans_start();

        $sql1 = "SELECT id, phone FROM cp_user_register WHERE id = ? for update";
        $uinfo = $this->db->query($sql1, array($userData['uid']))->getRow();

        if(empty($uinfo))
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status'	=> '0',
                'msg' 		=> '用户信息获取失败',
                'data' 		=> ''
            );
            return $updateStatus;
        }

        if($uinfo['phone'] == $userData['phone'])
        {
        	$this->db->trans_rollback();

            $updateStatus = array(
                'status'	=> '0',
                'msg' 		=> '新号码不能与旧号码一致',
                'data' 		=> ''
            );
            return $updateStatus;
        }

        // 检查新手机号码是否可用
        $sql2 = "SELECT id, phone FROM cp_user_register WHERE phone = ?";
        $userInfo = $this->db->query($sql2, array($userData['phone']))->getRow();

        if(!empty($userInfo))
        {
        	$this->db->trans_rollback();

            $updateStatus = array(
                'status'	=> '0',
                'msg' 		=> '该手机号已被注册使用',
                'data' 		=> ''
            );
            return $updateStatus;
        }

        // 更新cp_user_register表
        $registerSql = "UPDATE cp_user_register SET phone = ? WHERE id = ?";
        $registerRes = $this->db->query($registerSql, array($userData['phone'], $userData['uid']));

        // 同步cp_user_info
        $cpUserInfoData = array(
            'uid' => $userData['uid'],
            'phone' => $userData['phone']
        );

        $infoUpd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 
        'bank_city', 'pay_pwd', 'bind_id_card_time');
        
        $infoFields = array_keys($cpUserInfoData);
        $infoSql = "insert cp_user_info(" . implode(',', $infoFields) . " ) values(" . 
        implode(',', array_map(array($this, 'maps'), $infoFields)) .  " )" . $this->onduplicate($infoFields, $infoUpd);
        $infoRes = $this->db->query($infoSql, $cpUserInfoData);

        if($registerRes && $infoRes)
        {
            $this->db->trans_complete();

            // 刷新缓存
            $this->freshUserInfo($userData['uid']);

            $updateStatus = array(
                'status' => '1',
                'msg' => '手机号码更新成功',
                'data' => ''
            );
        }
        else
        {
            $this->db->trans_rollback();

            $updateStatus = array(
                'status' => '0',
                'msg' => '手机号码更新失败',
                'data' => ''
            );
        }
        $place = (isset($userData['isbck']) && $userData['isbck'] == 1) ? 2 : 1;
        $this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 4, '{$uinfo['phone']}', ?, ?)", array($userData['uid'], $userData['phone'], $place));
        return $updateStatus;
    }
    
    public function saveCpkUser($key, $uid) {
    	$sql = "insert into cp_promote (`key`, uid, created) values (?, ?, NOW())";
    	return $this->db->query($sql, array($key, $uid));
    }
    
    public function checkusersbyrealname() {
    	$res = $this->db->query("SELECT i.real_name, min(bind_id_card_time) as bind_id_card_time 
    	FROM cp_user_info as i 
    	inner JOIN cp_user_register as r on i.uid=r.id
		WHERE i.bind_id_card_time > date_sub(now() ,interval 24 hour) AND i.real_name is not NULL 
		AND r.check_status='0' GROUP BY i.real_name HAVING COUNT(*) > 1")->getAll();
    	if ($res) {
    		$users = array();
    		foreach ($res as $in => $val) {
    			$uids = $this->db->query("select uid from cp_user_info where real_name='".$val['real_name']."' and userStatus<>'2' and bind_id_card_time >= date_sub(now(), interval 75 second)")->getCol();
    			if (!empty($uids)) 
    			{
    				$users[$in] = "{$val['real_name']}(" . implode(',', $uids) . ")";
    			}
    		}
    		if (!empty($users)) {
    			$ustr = implode(';', $users);
    			//写入报警
    			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
    			VALUES ('11','24小时内重名用户报警，请核实','".$ustr."24小时内注册用户过多，存在恶意注册嫌疑，请核实最新实名用户', '0', NOW())";
    			$this->db->query($isql);
    		}
    	}
    }
    
    public function freezeByRealName()
    {
    	$res = $this->db->query("SELECT i.real_name, max(bind_id_card_time) bind_id_card_time 
    	FROM cp_user_info as i 
		WHERE i.bind_id_card_time > date_sub(now() ,interval 72 hour) AND i.real_name is not NULL 
		and i.userStatus = 2 GROUP BY i.real_name")->getAll();
    	if(!empty($res))
    	{
    		$users = array();
    		foreach ($res as $in => $re)
    		{
		    	$uids = $this->db->query("SELECT i.uid 
			    	FROM cp_user_info as i 
			    	inner JOIN cp_user_register as r on i.uid=r.id
					WHERE i.bind_id_card_time > '{$re['bind_id_card_time']}' AND i.real_name is not NULL 
					AND r.check_status='0' and i.userStatus<>'2' and i.real_name = '{$re['real_name']}'")->getCol();
		    	if(!empty($uids))
		    	{
		    		$this->db->query("update cp_user_info set userStatus='2' where uid in ?", array($uids));
    				foreach ($uids as $uid) {
    					$this->freshUserInfo($uid);
    				}
    				$users[$in] = $re['real_name'];
		    	}
		    	
    		}
    		if (!empty($users)) {
    			$ustr = implode(';', $users);
    			//写入报警
    			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
    			VALUES ('11','与72小时内冻结用户重名报警，已冻结最新实名用户','".$ustr."与72小时内冻结状态用户重名，已冻结最新实名用户', '0', NOW())";
    			$this->db->query($isql);
    		}
    		
    	}
    }
    
    public function checkusersByIp() {
    	$res = $this->db->query("SELECT u.reg_ip, min(u.uid) AS uid FROM cp_user AS u INNER JOIN cp_user_register AS r ON u.uid = r.id WHERE r.created > date_sub(now(), INTERVAL 10 MINUTE) 
					GROUP BY u.reg_ip HAVING count(*) > 2")->getAll();
    	$unames = '';
    	foreach ($res as $val) {
    		$uname = $this->db->query("select uname from cp_user as u inner join cp_user_register as r on u.uid=r.id where reg_ip='".$val['reg_ip']."' and uid>=".$val['uid'])->getCol();
    		$unames .= implode(',', $uname).",";
    	}
    	if (!empty($unames)) {
    		$unames = substr($unames, 0, -1);
			//写入报警
			$isql = "INSERT INTO cp_alert_log (ctype,title,content,status,created)
			VALUES ('12','同一IP注册用户超过2报警','".$unames."IP相同，存在恶意注册嫌疑。', '0', NOW())";
			$this->db->query($isql);
    	}
    }

    // 新版推送设置
    public function updatePushStatus($params)
    {
    	if(!empty($params['uid']) && $params['position'] > 0)
    	{
    		if($params['push_status'])
    		{
    			// 开启推送
    			$sql = "UPDATE cp_user_info SET app_push = (app_push ^ {$params['position']}) 
    			WHERE uid = ? AND (app_push & {$params['position']}) != 0";	
    		}
    		else
    		{
    			// 关闭推送
    			$sql = "UPDATE cp_user_info SET app_push = (app_push ^ {$params['position']}) 
    			WHERE uid = ? AND (app_push & {$params['position']}) = 0";		
    		}
    		$this->db->query($sql, array($params['uid']));
    		$this->freshUserInfo($params['uid']);
    		return true;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    /**
     * 清除用户缓存记录
     * @param int $isSpan 是否清除一段时间
     */
    public function clearnUserRedis($isSpan)
    {
    	$date = date('Y-m-d', strtotime("-1 month"));
    	if($isSpan)
    	{
    		$startDate = date('Y-m-d', strtotime("$date -1 month"));
    		$sql = "select uid from cp_user where last_login_time > ? and last_login_time < ?";
    		$uids = $this->db->query($sql, array($startDate, $date))->getCol();
    	}
    	else
    	{
    		$sql = "select uid from cp_user where last_login_time < ?";
    		$uids = $this->db->query($sql, array($date))->getCol();
    	}
    	
    	foreach ($uids as $uid)
    	{
    		$this->delCache($uid);
    	}
    }


    public function findByUid($uid) {
        $sql = "select cp_user.uid,cp_user.uname,cp_user_info.introduction,cp_user_info.introduction_status,cp_user_info.headimgurl from cp_user left join cp_user_info on cp_user.uid=cp_user_info.uid where cp_user.uid= ?";
        $user = $this->db->query($sql, array($uid))->getRow();
        return $user;
    }
    
    public function getHotInfo($uid) {
    	$REDIS = $this->config->item('REDIS');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$hotinfo = json_decode($this->cache->hGet($REDIS['USER_HOT'], $uid), true);
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
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->cache->hSet($this->REDIS['USER_HOT'], $uid, json_encode($hotinfo));
        return $hotinfo;
    }
    
    /**
     * 修改用户名操作
     * @param unknown_type $data
     */
    public function updateUname($data)
    {
        $olduname = $this->db->query("select uname from cp_user where uid = ?", array($data['uid']))->getOne();
    	//事务开始
    	$this->db->trans_start();
    	$sql = "update cp_user set uname = ? where uid =?";
    	$res = $this->db->query($sql, array($data['uname'], $data['uid']));
    	if($res)
    	{
    		$result = $this->db->query("update cp_user_info set nick_name_modify_time = ? where uid = ?", array($data['nick_name_modify_time'], $data['uid']));
    		if($result)
    		{
    			$this->db->trans_complete();
    			$this->freshUserInfo($data['uid']);
    			$place = (isset($data['isbck']) && $data['isbck'] == 1) ? 2 : 1;
    			$this->db->query("insert into cp_user_info_log (uid, type, cbefore, cafter, place) values (?, 1, ?, ?, ?)", array($data['uid'], $olduname, $data['uname'], $place));
    			return true;
    		}
    	}
    	 
    	$this->db->trans_rollback();
    	return false;
    }

    /**
     * 更新个人简介状态
     * @param type $uid
     * @param type $introduction
     * @param type $introduction_status
     */
    public function updateIntroduction($uid,$introduction,$introduction_status = 0,$sensitive_words='')
    {
        $this->db->trans_start();
        $res = $this->db->query("update cp_user_info set introduction = ?,sensitive_words= ?,introduction_status=?,introduction_time=NOW() where uid = ?", array($introduction,$sensitive_words,$introduction_status, $uid));
        if($res)
        {
            $this->db->trans_complete();
        }
        else
        {
            $this->db->trans_rollback();
        }
    }
    
    /**
     * 获取所有填入个人简介未审核的
     * @return array
     */
    public function uncheckSensitive()
    {
        $sql = "select uid,introduction from cp_user_info where introduction_status=0 and introduction!=''";
        return $this->db->query($sql)->getAll();
    }
    
    public function userIdCount($uid)
    {
        $sql = "select id_card from cp_user_info where uid=?";
        $user = $this->db->query($sql, array($uid))->getRow();
        $sql = "select uid from cp_user_info where id_card=?";
        return $this->db->query($sql, array($user['id_card']))->getAll();
    }

    // 检查微信关联账户
    public function checkWxUnionid($unionid)
    {
        $sql = "SELECT uid, wx_unionid FROM cp_user_info WHERE wx_unionid = ?";
        return $this->db->query($sql, array($unionid))->getRow();
    }

    // 检查手机号绑定微信信息
    public function getUserInfoByPhone($phone)
    {
        $sql = "SELECT r.id, i.wx_unionid, u.uname FROM cp_user_register AS r LEFT JOIN cp_user_info AS i ON r.id = i.uid LEFT JOIN cp_user AS u ON r.id = u.uid WHERE r.phone = ?";
        return $this->db->query($sql, array($phone))->getRow();
    }

    // 微信登录注册或绑定
    public function wxRegister($userData)
    {
    	// 事务开始
        $this->db->trans_start();

        $uinfo = $this->getUserInfoByPhone($userData['phone']);

        if(!empty($uinfo))
        {
            // 绑定
            $this->db->query("UPDATE cp_user_info SET wx_unionid = ? WHERE uid = ? AND wx_unionid = ''", array($userData['unionid'], $uinfo['id']));
            $uid = $uinfo['id'];
            $cpUserData['uname'] = $uinfo['uname'];
            $registerRes = $this->db->affected_rows();
        }
        else
        {
        	// 注册 组装cp_user_register数据
            $registerData = array(
                'phone' => $userData['phone']
            );

            // 生成彩票uid
            $registerFields = array_keys($registerData);
            $registerSql = "insert cp_user_register(" . implode(',', $registerFields) . ", created)
            values(". implode(',', array_map(array($this, 'maps'), $registerFields)) .", now())";
            $this->db->query($registerSql, $registerData);

            $uid = $this->db->insert_id();

            if($uid > 0)
            {
                // 组装cp_user数据
                $uniqid = substr(uniqid(), 0, 9);
                $pwd = md5($userData['pword']) . $uniqid;
                $cpUserData = array(
                    'uid'               =>  $uid,
                    'passid'            =>  $userData['passid'],
                    'uname'             =>  'user_' . date('ymdHis') . $uid,
                    'pword'             =>  strCode($pwd, 'ENCODE'),
                    'salt'              =>  $uniqid,
                    'email'             =>  '',
                    'reg_type'          =>  3,							// 微信登录
                    'reg_reffer'        =>  $userData['reg_reffer'],
                    'channel'           =>  $userData['channel'],
                    'reg_ip'            =>  $userData['reg_ip'],
                    'last_login_time'   =>  $userData['last_login_time'],
                    'activity_id'		=>	$userData['activity_id'],
                    'visit_times'       =>  1
                );

                $userUpd = array('last_login_time', 'visit_times', 'pword', 'salt', 'email', 'passid', 'nick_name', 'uname', 'activity_id');
                $userFields = array_keys($cpUserData);
                $userSql = "insert cp_user(" . implode(',', $userFields) . ", created)values(" . 
                implode(',', array_map(array($this, 'maps'), $userFields)) .  ", now())" . $this->onduplicate($userFields, $userUpd);
                $userStatus = $this->db->query($userSql, $cpUserData);

                // 组装cp_user_info数据
                $cpUserInfoData = array(
                    'uid'                   =>  $uid,
                    'phone'                 =>  $userData['phone'],
                    'nick_name'             =>  $cpUserData['uname'],
                    'nick_name_modify_time' =>  '0000-00-00 00:00:00',
                    'wx_unionid'            =>  $userData['unionid'],
                );

                $infoUpd = array('real_name', 'nick_name', 'gender', 'phone', 'qq', 'id_card', 'bank_id', 'province', 'city', 'bank_province', 'bank_city', 'pay_pwd', 'bind_id_card_time');
                $infoFields = array_keys($cpUserInfoData);
                $userInfoSql = "insert cp_user_info(" . implode(',', $infoFields) . " ) values(" . implode(',', array_map(array($this, 'maps'), $infoFields)) .  " )" . $this->onduplicate($infoFields, $infoUpd);
                $userInfoStatus = $this->db->query($userInfoSql, $cpUserInfoData);

                $registerRes = $userStatus && $userInfoStatus;
            }
            else
            {
                $registerRes = FALSE;
            }
        }

        if($registerRes)
        {
            $this->db->trans_complete();

            // 刷新缓存
            $this->freshUserInfo($uid);

            $registerStatus = array(
                'status'    =>  TRUE,
                'msg'       =>  '手机号注册成功',
                'data'      =>  array(
                	'regType'	=>	!empty($uinfo) ? 2 : 1,
                    'uid'       =>  $uid,
                    'uname'		=>	$cpUserData['uname'],
                    'phone'     =>  $userData['phone'],
                    'pword'  	=>  $userData['pword']
                )
            );
        }
        else
        {
            $this->db->trans_rollback();
            
            $registerStatus = array(
                'status'    =>  FALSE,
                'msg'       =>  '微信登录注册或绑定失败',
                'data'      =>  array(
                	'uid'       =>  $uid,
                )
            );
        }
        return $registerStatus;
    }
    
    public function isIdCardBind($uid)
    {
    	$uinfo = $this->getUserInfo($uid);
    	return (!empty($uinfo['id_card']))?true:false;
    }
    
    /**
     * 任务处理失败时入库
     * @param unknown $datas
     * @return unknown
     */
    public function errorRecord($datas)
    {
        $sql = "insert into cp_server_task_retry(data, created) values(?, now())";
        return $this->db->query($sql, array($datas));
    }
    
    public function uploadImg($url, $uid)
    {
        $this->db->query("update cp_user_info set headimgurl = ?,headimg_status=1 where uid = ?", array($url, $uid));
        $this->freshUserInfo($uid);
        $date = date("Y-m-d");
        $this->db->query('insert into cp_headimg_record (uid,date,headimgurl,created) values(?,?,?,now())', array($uid, $date, $url));
    }
    
    public function cleanMust()
    {
        $start = 0;
        $sql = "select id, uid, must_cost from cp_user where id > ? and must_cost > 0 order by id asc limit 500";
        $users = $this->db->query($sql, array($start))->getAll();
        while (!empty($users)) {
            foreach ($users as $user) {
                $res = $this->db->query("update cp_user set must_cost = 0 where uid= ?", array($user['uid']));
                if($res) {
                    $this->freshUserInfo($user['uid']);
                }
                
                $start = $user['id'];
            }
            
            $users = $this->db->query($sql, array($start))->getAll();
        }
    }
}
