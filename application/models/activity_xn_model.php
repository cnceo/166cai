<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 拉型活动 - 模型层
 * @date:2016-07-22
 */
class Activity_Xn_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

    /*
     * 手机号参与活动
     * @date:2016-07-22
     */
    public function attend($uid, $phone, $from_channel_id, $to_channel_id, $activityId = 13)
    {
        // 非空检查
        if(empty($phone))
        {
            $result = array(
                'status' => FALSE,
                'msg' => '手机号不能为空',
                'data' => ''
            );
            return $result;
        }

        // 检查手机号是否被注册
        $registerSql = "SELECT id, phone FROM cp_user_register WHERE phone = ?";
        $registerInfo = $this->db->query($registerSql, array($phone))->getRow();

        if(!empty($registerInfo))
        {
            $result = array(
                'status' => FALSE,
                'msg' => '该手机号已注册',
                'data' => ''
            );
            return $result;
        }

        // 检查是否参与过活动
        $lxSql = "SELECT id FROM cp_activity_lx_join WHERE phone = ?";
        $lxInfo = $this->db->query($lxSql, array($phone))->getRow();

        if(!empty($lxInfo))
        {
            $result = array(
                'status' => FALSE,
                'msg' => '您已领取过红包',
                'data' => ''
            );
            return $result;
        }

        // 发起人检查
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($uid);

        if(empty($uinfo))
        {
            $result = array(
                'status' => FALSE,
                'msg' => '发起人信息错误',
                'data' => ''
            );
            return $result;
        }

        // 记录参与信息
        $recordData = array(
            'activity_id'   =>  $activityId,
            'puid'  =>  $uid,
            'phone' =>  $phone,
            'from_channel_id'   =>  $from_channel_id,
            'to_channel_id' =>  $to_channel_id,
        );

        $fields = array_keys($recordData);
        $joinSql = "insert ignore cp_activity_lx_join(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        $joinRes = $this->db->query($joinSql, $recordData);

        // 组装参数
        $userData = array(
            'activity_id' => $activityId,
            'uid' => $uid,
            'uname' => $uinfo['uname'],
            'platform' => $from_channel_id,
            'total' => 1,
        );

        $userFields = array_keys($userData);
        $userSql = "insert cp_activity_chj_user(" . implode(',', $userFields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $userFields)) .  ", now())
        on duplicate key update total = total + VALUES(total)";
        $userRes = $this->db->query($userSql, $userData);

        if($joinRes && $userRes)
        {
            $result = array(
                'status' => TRUE,
                'msg' => '参与成功',
                'data' => ''
            );
        }
        else
        {
            $result = array(
                'status' => FALSE,
                'msg' => '参与失败',
                'data' => ''
            );
        }
        return $result;
    }
	
	/*
	 * 抽取奖品
	 * @date:2016-07-22
	 */
	public function draw($uid, $platformId, $activityId)
	{
	    $this->load->model('user_model');
	    
	    $uinfo = $this->user_model->getUserInfo($uid);
	    if(empty($uinfo)){
	        $result = array(
	            'status' => FALSE,
	            'msg' => '用户信息错误',
	            'data' => '002'
	        );
	        return $result;
	    }
	    
        // 开启事务
        $this->db->trans_start();
        // 获取用户抽奖信息
        $userInfo = $this->db->query("SELECT uid, total_num, left_num 
        	FROM cp_activity_chj_user 
        	WHERE uid = ? FOR UPDATE", array($uid))->getRow();

        if(empty($userInfo))
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '暂无抽奖次数',
				'data' => '001'	//需要弹制作贺卡弹窗
			);
			return $result;
        }
        
        if($userInfo['left_num'] <= 0)
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '暂无抽奖次数',
        	    'data' => '001'	//需要弹制作贺卡弹窗
			);
			return $result;
        }
        
        if(date('Y-m-d') >= '2018-02-29')
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '亲，来晚啦，活动已结束',
                'data' => '002'	//需要弹活动结束
            );
            return $result;
        }

        // 检查奖项设置信息
        $prizeSql = "SELECT id, name, lv, num,rid FROM cp_activity_year_prize WHERE delete_flag = 0 and num > 0";
        $prizeData = $this->db->query($prizeSql)->getAll();

        if(empty($prizeData))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '获取奖品信息失败',
                'data' => '002'    
            );
            return $result;
        }

        // 抽奖逻辑
        $arr = array();
        $prizeInfo = array();
        foreach ($prizeData as $key => $val) 
        { 
            $lv = $val['lv'] * 10000;
            if($lv > 0)
            {
                $arr[$val['id']] = $lv;
                $prizeInfo[$val['id']] = $val;
            }
        }

        // 抽奖
        $rid = $this->getRandPrize($arr);

        if(empty($prizeInfo[$rid]))
        {
            $this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '抽奖信息异常',
				'data' => '002'	
			);
			return $result;
        }

        $prizeSql = "SELECT id, name, lv, num,rid FROM cp_activity_year_prize WHERE id = ? and num > 0 for update";
        $prize = $this->db->query($prizeSql, array($rid))->getRow();
        if(empty($prize))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '抽奖信息异常',
                'data' => '002'
            );
            return $result;
        }
        // 扣除机会
        $opRes = $this->db->query("UPDATE cp_activity_chj_user SET left_num = left_num - 1 WHERE left_num > 0 AND uid = ?", array($uid));

        if(!$opRes)
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '您的抽奖机会已用完',
				'data' => '002'	
			);
			return $result;
        }
        
        // 扣除红包数量
        $this->db->query("UPDATE cp_activity_year_prize SET num = num - 1 WHERE id = ?", array($rid));
        $res = $this->db->query("insert into cp_activity_chj_logs(activity_id, uid, uname, award_id,mark, created)values
        (?,?,?,?,?,now())", array($activityId, $uid, $uinfo['uname'], $rid, $prize['name']));
        if(!$res)
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '系统错误',
                'data' => '002'
            );
            return $result;
        }

        // 获取红包详情
        $redpackInfo = $this->db->query("SELECT id, aid, p_type, c_type, money, p_name, use_params, cash_back, use_desc, refund_desc 
            FROM cp_redpack 
            WHERE aid = ? AND id = ? AND delete_flag = 0", array($activityId, $prize['rid']))->getRow();

        if(empty($redpackInfo))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '获取红包详情失败',
                'data' => '002'    
            );
            return $result;
        }

        // 组装红包信息
        $redpack = array(
        	'packType'		=>	$redpackInfo['p_type'],
        	'aid'	        =>	$activityId,
        	'platform_id'	=> 	$platformId,
        	'channel_id'	=>	0,
        	'uid'		    =>	$uid,
        	'rid'		    => 	$redpackInfo['id'],
            'packParams'    =>  $redpackInfo['use_params'],
            'money'         =>  $redpackInfo['money'],
        );

        // 发放红包
        $sendRes = $this->sendRedPack($uid, $redpack);

        if($sendRes['status'])
        {
            $this->db->trans_complete();
            $result = array(
                'status' => TRUE,
                'msg' => '抽奖成功',
                'data' => array(
                    'rid' => $rid,
                    'use_desc' => $prize['name'],
                    'left_num' => $userInfo['left_num'] - 1,
                    'created' => date('Y-m-d H:i'),
                )    
            );
        }
        else
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => $sendRes['msg'],
                'data' => '002'    
            );
        }   
		return $result;
	}

	/*
	 * 抽奖逻辑
	 * @date:2016-07-22
	 */
	public function getRandPrize($proArr)
    {
        $result = '';

        //概率数组的总概率精度
        $proSum = array_sum($proArr);

        //概率数组循环
        foreach ($proArr as $key => $proCur) 
        { 
            $randNum = mt_rand(1, $proSum); 
            if ($randNum <= $proCur) 
            { 
                $result = $key; 
                break; 
            } 
            else 
            { 
                $proSum -= $proCur; 
            } 
        } 
        unset ($proArr); 

        return $result; 
    }

    /*
     * 发送红包
     * @date:2016-07-22
     */
    public function sendRedPack($uid, $redpack)
    {
        // 根据红包类型处理
        switch ($redpack['packType']) 
        {
            // 彩金红包
            case '1':
                $redpackData = array(
                    'aid'           =>  $redpack['aid'],
                    'platform_id'   =>  $redpack['platform_id'],
                    'channel_id'    =>  $redpack['channel_id'],
                    'uid'           =>  $uid,
                    'rid'           =>  $redpack['rid'],
                    'valid_start'   =>  date('Y-m-d H:i:s', strtotime(date('Y-m-d'))),
                    'valid_end'     =>  date('Y-m-d H:i:s', strtotime('-1 second', strtotime(date('Y-m-d',strtotime('+5 year'))))),
                    'get_time'      =>  date('Y-m-d H:i:s'),
                    'status'        =>  1, 
                );

                // 记录红包
                $redpackRes = $this->recordRedpack($redpackData);
                
                if($redpackRes)
                {
                    $result = array(
                        'status' => TRUE,
                        'msg' => '红包操作成功',
                        'data' => ''
                    );
                }
                else
                {
                    $result = array(
                        'status' => FALSE,
                        'msg' => '红包操作失败',
                        'data' => ''
                    );
                }
                break;
            // 购彩红包
            case '3':
                $packParams = json_decode($redpack['packParams'], TRUE);
                $start = '+' . $packParams['start_day'] . ' day';
                $end = '+' . $packParams['end_day'] . ' day';
                $redpackData = array(
                    'aid'           =>  $redpack['aid'],
                    'platform_id'   =>  $redpack['platform_id'],
                    'channel_id'    =>  $redpack['channel_id'],
                    'uid'           =>  $uid,
                    'rid'           =>  $redpack['rid'],
                    'valid_start'   =>  date('Y-m-d H:i:s', strtotime(date('Y-m-d',strtotime($start)))),
                    'valid_end'     =>  date('Y-m-d H:i:s', strtotime('-1 second', strtotime(date('Y-m-d',strtotime($end))))),
                    'get_time'      =>  date('Y-m-d H:i:s'),
                    'status'        =>  1,      // 已激活
                );

                // 记录红包
                $redpackRes = $this->recordRedpack($redpackData);

                if($redpackRes)
                {
                    $result = array(
                        'status' => TRUE,
                        'msg' => '红包操作成功',
                        'data' => ''
                    );
                }
                else
                {
                    $result = array(
                        'status' => FALSE,
                        'msg' => '红包操作失败',
                        'data' => ''
                    );
                }
                break;
            
            default:
                $result = array(
                    'status' => FALSE,
                    'msg' => '红包操作失败，未知红包类型',
                    'data' => ''
                );
                break;
        }
        return $result;
    }

    /*
     * 记录红包信息
     * @date:2016-07-22
     */
    public function recordRedpack($redpackData)
    {
        $fields = array_keys($redpackData);
        $redpackSql = "insert cp_redpack_log(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        return $this->db->query($redpackSql, $redpackData);
    }

    /*
     * 注册 - 更新活动信息
     * @date:2016-07-22
     */
    public function regAdd($uid, $phone, $activityId = 5)
    {
        $this->db->trans_start();

        // 检查是否参与活动
        $joinInfo = $this->db->query("SELECT id, activity_id, puid, from_channel_id, phone, to_channel_id, uid, status, created 
            FROM cp_activity_lx_join
            WHERE phone = ? AND status = 0 FOR UPDATE", array($phone, $uid))
            ->getRow();

        if(empty($joinInfo))
        {
            $this->db->trans_rollback();   
            return FALSE;
        }

        if($joinInfo['puid'] == $uid)
        {
            $this->db->trans_rollback();   
            return FALSE;
        }

        // 更新参与状态
        $joinRes = $this->db->query("UPDATE cp_activity_lx_join SET uid = ?, status = 1 WHERE phone = ? AND status = 0", array($uid, $phone));

        if($joinRes)
        {
            $this->db->trans_complete();
            return TRUE;
        }
        else
        {
            $this->db->trans_rollback();   
            return FALSE;
        }
    }

    /*
     * 实名 - 更新活动信息
     * @date:2016-07-22
     */
    public function idcardAdd($uid, $idCard, $activityId = 5)
    {
        $this->db->trans_start();

        // 检查是否参与活动
        $joinInfo = $this->db->query("SELECT id, activity_id, puid, from_channel_id, phone, to_channel_id, uid, status, created 
            FROM cp_activity_lx_join
            WHERE uid = ? AND status = 1 FOR UPDATE", array($uid))
            ->getRow();

        if(empty($joinInfo))
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        // 检查是否为新用户
//        $idCardCount = $this->db->query("SELECT count(*) 
//            FROM cp_user_info
//            WHERE id_card = ? AND uid <> ?", array($idCard, $uid))
//            ->getOne();
//
//        if($idCardCount > 0)
//        {
//            $this->db->trans_rollback();
//            return FALSE;
//        }

        // 更新参与状态
        $joinRes = $this->db->query("UPDATE cp_activity_lx_join SET status = 2 WHERE uid = ? AND status = 1", array($uid));

        if(!$joinRes)
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        // 增加抽奖机会
        $userInfo = $this->db->query("SELECT id, uid, total_num, left_num, created 
            FROM cp_activity_lx_user
            WHERE uid = ? FOR UPDATE", array($joinInfo['puid']))
            ->getRow();

        if(empty($userInfo))
        {
            $this->db->trans_rollback();
            return FALSE;
        }

        $userRes = $this->db->query("UPDATE cp_activity_lx_user SET total_num = total_num + 1, left_num = left_num + 1 WHERE uid = ?", array($joinInfo['puid']));

        if($userRes)
        {
            $this->db->trans_complete();
            $this->load->model('user_model');
            $position = $this->config->item('POSITION');
            //$this->user_model->sendSms($joinInfo['puid'], array(), 'join_huodong', null, UCIP, $position['join_huodong']);
            return TRUE;
        }
        else
        {
            $this->db->trans_rollback();
            return FALSE;
        }

    }
    
    public function getJoined($uid){
    	$sql = "select u.uname, j.modified from cp_activity_lx_join j
    		left join cp_user u on j.uid=u.uid
    		where puid = ? and status = 2 order by j.modified desc";
    	return $this->db->query($sql, array($uid))->getAll();
    }
    
    public function getchoujiang($uid) {
    	$sql = "select total_num, left_num from cp_activity_lx_user where uid = ?";
    	return $this->db->query($sql, array($uid))->getRow();
    }
    
    public function getInfo() {
    	$sql = "select start_time, end_time from cp_activity where id = 5";
    	return $this->db->query($sql, array($uid))->getRow();
    }

    public function isJoinLx($phone)
    {
        $lxSql = "SELECT id FROM cp_activity_lx_join WHERE phone = ?";
        $lxInfo = $this->db->query($lxSql, array($phone))->getRow();

        if(!empty($lxInfo))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /**
     * 获取3个月内满足拉新条件的uid
     * @return array
     */
    public function getlxUsers()
    {
        $time = date("Y-m-d H:i:s", strtotime("-3 month"));
        $sql = "select a.puid,a.uid,u.phone from cp_activity_lx_join a left join cp_user_info u on a.puid=u.uid where activity_id=9 and status=2 and created>='{$time}'";
        $users = $this->db->query($sql)->getAll();
        return $users;
    }
    
    public function getlxEndtime()
    {
        $sql = "select start_time,end_time from cp_activity where id=9";
        return $this->db->query($sql)->getRow();
    }
    
    public function countLxRed($uid)
    {
        $sql = "select count(id) as count from cp_activity_lx_join where puid=? and status = 3 and activity_id=9";
        return $this->db->query($sql, array($uid))->getRow();
    }
    
    public function hasLxjoin($alluid,$puid,$uid)
    {
        $uids=  implode(',', $alluid);
        $sql ="select count(id) as count from cp_activity_lx_join where activity_id=9 and uid in ({$uids}) and status in (3,4)";
        $count=$this->db->query($sql)->getRow();
        if($count['count'] >= 1)
        {
            $this->db->query("update cp_activity_lx_join set status = 5 where puid = ? and uid=? and activity_id=9", array($puid, $uid));
            return TRUE;
        }
        return FALSE;
    }
    
    public function updateStatus($puid,$uid,$status)
    {
        $this->db->query("update cp_activity_lx_join set status = ? where puid = ? and uid=? and activity_id=9", array($status, $puid, $uid));
    }
    
    public function countHasLx($uid)
    {
    	$sql = "select count(id) from cp_activity_lx_join where puid=? and activity_id=13 and created > DATE_SUB(NOW(),INTERVAL 1 DAY)";
    	return $this->db->query($sql, array($uid))->getCol();
    }
    
    /**
     * 春节活动获得抽奖次数
     */
    public function luckSend()
    {
        $activity = $this->slave->query("select * from cp_activity where id = '13' and delete_flag ='0'")->getRow();
        if(empty($activity) || ($activity['end_time'] < date('Y-m-d H:i:s')))
        {
            return ;
        }
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $startTime = date('Y-m-d H:i:s', strtotime("-1 min"));
        $endTime   = date('Y-m-d H:i:s');
        $cacheName = "activityxnlucksend:";
        $cacheTime = $this->cache->redis->get($cacheName);
        if(!empty($cacheTime))
        {
            $startTime = $cacheTime;
        }
        
        $sql = "select uid from cp_wallet_logs WHERE modified > date_sub(now(), interval 10 minute) 
        AND (recharge_over_time BETWEEN ? AND ?) AND ctype= '0' AND mark = '1'";
        $uids = $this->slave->query($sql, array($startTime, $endTime))->getCol();
        foreach ($uids as $uid)
        {
            $activityJoin = $this->slave->query("select puid, from_channel_id from cp_activity_lx_join where activity_id=13 and uid= ? and status = '2'", array($uid))->getRow();
            if($activityJoin)
            {
                $chj_user = $this->slave->query("select today_num, today from cp_activity_chj_user where activity_id=13 and uid = ?", array($activityJoin['puid']))->getRow();
                if($chj_user)
                {
                    //相同身份证id处理
                    $userCount = $this->userCount($uid);
                    if(count($userCount) > 1)
                    {
                        $sql ="select count(id) as count from cp_activity_lx_join where activity_id=13 and uid in ? and status in (3,4,5)";
                        $count=$this->slave->query($sql, array($userCount))->getOne();
                        if($count > 0)
                        {
                            $this->db->query("update cp_activity_lx_join set status = '5' where activity_id = 13 and uid = ?", array($uid));
                            $this->db->query("update cp_activity_chj_user set buy_num = buy_num + 1 where activity_id = 13 and uid = ?", array($activityJoin['puid']));
                            continue;
                        }
                    }
                    
                    $today = date('Y-m-d');
                    if(($chj_user['today'] != $today) || ($chj_user['today_num'] < 5))
                    {
                        //加抽奖次数
                        $today_num = ($chj_user['today'] != $today) ? 1 : $chj_user['today_num'] + 1;
                        $this->db->query("update cp_activity_chj_user set total_num = total_num + 1, left_num = left_num + 1, buy_num = buy_num + 1, today_num = '{$today_num}', today = '{$today}' where activity_id = 13 and uid = ?", array($activityJoin['puid']));
                        $this->db->query("update cp_activity_lx_join set status = '3' where activity_id = 13 and uid = ?", array($uid));
                        $this->load->library('mipush');
                        $pushData = array(
                            'type'  => 'activity_draw',
                            'uid'  =>  $activityJoin['puid'],
                            'title'  =>  "抽大奖啦！",
                            'content' =>  '亲，邀请好友成功，恭喜获得1次抽奖机会，快来使用>>',
                            'time_to_live' => 600000
                        );
                        $this->mipush->index('user', $pushData);
                    }
                    else
                    {
                        $this->db->query("update cp_activity_chj_user set buy_num = buy_num + 1, today_num = today_num + 1 where activity_id = 13 and uid = ?", array($activityJoin['puid']));
                        $this->db->query("update cp_activity_lx_join set status = '4' where activity_id = 13 and uid = ?", array($uid));
                    }
                }
            }
        }
        
        $this->cache->redis->save($cacheName, $endTime, 0);
    }
    
    /**
     * 查询身份证重复的用户
     * @param unknown $uid
     * @return unknown
     */
    private function userCount($uid)
    {
        $sql = "select id_card from cp_user_info where uid=?";
        $user = $this->slave->query($sql, array($uid))->getRow();
        $sql = "select uid from cp_user_info where id_card=?";
        return $this->slave->query($sql, array($user['id_card']))->getCol();
    }
    
    /**
     * 查询手机号是否参加166红包
     * @param unknown $phone
     * @return unknown
     */
    public function has166Attend($phone)
    {
        return $this->slave->query ("SELECT aid, phone,uid FROM cp_activity_log WHERE aid = 8 AND phone = ?", array ($phone))->getRow();
    }
}
