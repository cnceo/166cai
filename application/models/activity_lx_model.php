<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 拉型活动 - 模型层
 * @date:2016-07-22
 */
class Activity_Lx_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

    /*
     * 手机号参与活动
     * @date:2016-07-22
     */
    public function attend($uid, $phone, $from_channel_id, $to_channel_id, $activityId = 5)
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
            'uid' => $uid,
        );

        $userFields = array_keys($userData);
        $userSql = "insert ignore cp_activity_lx_user(" . implode(',', $userFields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $userFields)) .  ", now())";
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
	public function draw($uid, $platformId, $channelId, $activityId = 5)
	{
		// 活动状态检查
		$activity = $this->db->query("SELECT id, a_name name, params, start_time startTime, end_time endTime
            FROM cp_activity
            WHERE id = ? AND delete_flag = 0", array($activityId))
            ->getRow();

        if(empty($activity))
        {
        	$result = array(
				'status' => FALSE,
				'msg' => '活动已结束',
				'data' => '001'	
			);
            return $result;
        }

        $this->load->helper('date');
        $nowTime = now();
        if(mysql_to_unix($activity['startTime']) > $nowTime)
        {
        	$result = array(
				'status' => FALSE,
				'msg' => '活动尚未开始',
				'data' => '001'	
			);
            return $result;
        }
        if(mysql_to_unix($activity['endTime']) < $nowTime)
        {
        	$result = array(
				'status' => FALSE,
				'msg' => '活动已结束',
				'data' => '001'	
			);
			return $result;
        }

        // 开启事务
        $this->db->trans_start();

        // 获取用户抽奖信息
        $userInfo = $this->db->query("SELECT uid, total_num, left_num 
        	FROM cp_activity_lx_user 
        	WHERE uid = ? FOR UPDATE", array($uid))
            ->getRow();

        if(empty($userInfo))
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '您尚未参与过本活动',
				'data' => ''	
			);
			return $result;
        }
        
        if($userInfo['left_num'] <= 0)
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '您的抽奖机会已用完',
				'data' => ''	
			);
			return $result;
        }

        // 检查奖项设置信息
        $prizeSql = "SELECT id, name, lv, num FROM cp_activity_lx_prize WHERE delete_flag = 0";
        $prizeData = $this->db->query($prizeSql)->getAll();

        if(empty($prizeData))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '获取奖品信息失败',
                'data' => '001'    
            );
            return $result;
        }

        // 抽奖逻辑
        $arr = array();
        $prizeInfo = array();
        foreach ($prizeData as $key => $val) 
        { 
            $arr[$val['id']] = $val['lv'] * 10000;
            $prizeInfo[$val['id']] = $val;
        }

        // 指定的用户范围
        $whiteUser = array();
        // 指定的奖品ID
        $rid = '';
        if(!empty($whiteUser) && !empty($rid) && !empty($prizeInfo[$rid]) && in_array($uid, $whiteUser))
        {
            // 获取用户抽奖信息
            $prizes = $this->db->query("SELECT id, name, lv, num 
                FROM cp_activity_lx_prize 
                WHERE id = ? AND delete_flag = 0 FOR UPDATE", array($rid))
                ->getRow();

            $prizeRes = $this->db->query("UPDATE cp_activity_lx_prize SET num = num - 1 WHERE id = ? AND num > 0 AND delete_flag = 0", array($rid));

            $prizeId = $this->db->affected_rows();

            if($prizeId)
            {
                $arr = array();
                $arr[$rid] = 100;
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
				'data' => '001'	
			);
			return $result;
        }

        // 扣除机会
        $opRes = $this->db->query("UPDATE cp_activity_lx_user SET left_num = left_num - 1 WHERE left_num > 0 AND uid = ?", array($uid));

        if(!$opRes)
        {
        	$this->db->trans_rollback();
        	$result = array(
				'status' => FALSE,
				'msg' => '您的抽奖机会已用完',
				'data' => ''	
			);
			return $result;
        }

        // 获取红包详情
        $redpackInfo = $this->db->query("SELECT id, aid, p_type, c_type, money, p_name, use_params, cash_back, use_desc, refund_desc 
            FROM cp_redpack 
            WHERE aid = ? AND c_type = ? AND delete_flag = 0", array($activityId, $rid))
            ->getRow();

        if(empty($redpackInfo))
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => '获取红包详情失败',
                'data' => '001'    
            );
            return $result;
        }

        // 组装红包信息
        $redpack = array(
        	'packType'		=>	$redpackInfo['p_type'],
        	'aid'	        =>	$activityId,
        	'platform_id'	=> 	$platformId,
        	'channel_id'	=>	$channelId,
        	'uid'		    =>	$uid,
        	'rid'		    => 	$redpackInfo['id'],
            'packParams'    =>  $redpackInfo['use_params'],
            'money'         =>  $redpackInfo['money'],
        );

        // 发放红包
        $sendRes = $this->sendRedPack($uid, $redpack);

        if($sendRes)
        {
            // 刷新钱包
            $this->load->model('wallet_model');
            $this->wallet_model->freshWallet($uid);

            $this->db->trans_complete();
            // 刷新中奖缓存
            if ($redpackInfo['money'] >= 500) {
            	$this->refreshLxRecord($uid, $redpackInfo);
            }
            $result = array(
                'status' => TRUE,
                'msg' => '抽奖成功',
                'data' => array(
                    'rid' => $rid,
                    'use_desc' => $redpackInfo['use_desc'],
                    'p_Type' => $redpackInfo['p_type'],
                    'left_num' => $userInfo['left_num'] - 1,
                )    
            );
        }
        else
        {
            $this->db->trans_rollback();
            $result = array(
                'status' => FALSE,
                'msg' => $sendRes['msg'],
                'data' => ''    
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
                    'status'        =>  2,      // 已使用
                );

                // 记录红包
                $redpackRes = $this->recordRedpack($redpackData);

                // 彩金处理
                $money = $this->db->query("SELECT money FROM cp_user WHERE uid = ? FOR UPDATE ", $uid)->getOne();
                $userRes = $this->db->query("UPDATE cp_user SET money = money + ?, dispatch = dispatch + ? WHERE uid = ?", array($redpack['money'], $redpack['money'], $uid));

                // 流水处理
                $walletData = array(
                    'uid'       =>  $uid,
                    'money'     =>  $redpack['money'],
                    'ctype'     =>  9,
                    'mark'      =>  '1',
                    'trade_no'  =>  $this->tools->getIncNum('UNIQUE_KEY'),
                    'umoney'    =>  $money + $redpack['money'],
                    'status'    =>  1,
                    'content'   =>  '红包',
                );
                $walletRes = $this->recordWallet($walletData);

                // 总账记录流水
                // $this->load->model('capital_model');
                // $capitalRes = $this->capital_model->recordCapitalLog('2', $walletData['trade_no'], 'redpack', $redpack['money'], '2', $tranc = FALSE);
                
                if($redpackRes && $userRes && $walletRes)
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
            // 充值红包
            case '2':
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
     * 流水操作
     * @date:2016-07-22
     */
    public function recordWallet($walletData)
    {
        $fields = array_keys($walletData);
        $walletSql = "insert cp_wallet_logs(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        return $this->db->query($walletSql, $walletData);
    }

    /*
     * 获取中奖信息
     * @date:2016-07-22
     */
    public function getLxRecord()
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $info = unserialize($this->cache->get($REDIS['ACTIVITY_LX']));
        return $info;
    }

    /*
     * 刷新中奖信息
     * @date:2016-07-22
     */
    public function refreshLxRecord($uid, $redpackInfo, $size = 70)
    {
        // 获取用户基本信息
        $this->load->model('user_model');
        $uinfo = $this->user_model->getUserInfo($uid);

        $data = array(
            'uname' =>  $uinfo['uname'],
            'money'     =>  $redpackInfo['money'],
        );
        
        $info = $this->getLxRecord();
        $count = count($info);
        if(!empty($info))
        {
            if($count >= $size)
            {
            	$l = 0;
            	foreach ($info as $k => $val) {
            		if ($val['flag'] == 1 && $val['money'] == 500 && $l < 22) {
            			unset($info[$k]);
            			$l++;
            		}
            	}
            }
            array_push($info, $data);
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['ACTIVITY_LX'], serialize($info), 0);
        }
        else
        {
            $this->cshLxRecored();
            $this->refreshLxRecord();
        }
    }
    
    public function cshLxRecored()
    {
    	$info = array();
    	$arr = array(1 => 500000, 1 => 100000, 3 => 50000, 4 => 16600, 13 => 1800, 20 => 500);
    	foreach ($arr as $k => $v) {
    		for ($i = 1; $i <= $k; $i++) {
    			$str = "0123456789abcdefghijklmnopqrstuvwxyz";
    			$nickname = 'user_';
    			for($j=0 ; $j<rand(2,5); $j++){
    				$nickname .= $str[rand(0,35)];
    			}
    			$dt = array('uname' =>  $nickname, 'money' => $v, 'flag' => 1);
    			array_push($info, $dt);
    		}
    	}
    	array_push($info, array('uname' =>  '落叶', 'money' => 1800, 'flag' => 1));
    	array_push($info, array('uname' =>  '棉花', 'money' => 100000, 'flag' => 1));
    	array_push($info, array('uname' =>  '百万', 'money' => 1800, 'flag' => 1));
    	array_push($info, array('uname' =>  '我要', 'money' => 500, 'flag' => 1));
    	array_push($info, array('uname' =>  '中奖', 'money' => 16600, 'flag' => 1));
    	array_push($info, array('uname' =>  '彩神', 'money' => 16600, 'flag' => 1));
    	array_push($info, array('uname' =>  '天天', 'money' => 500, 'flag' => 1));
    	array_push($info, array('uname' =>  '萌新', 'money' => 1800, 'flag' => 1));
    	array_push($info, array('uname' =>  '小冷', 'money' => 500, 'flag' => 1));
    	shuffle($info);
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['ACTIVITY_LX'], serialize($info), 0);
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
        $this->db->query("UPDATE cp_activity_chj_user SET reg_total = reg_total + 1 WHERE uid = ?", array($joinInfo['puid']));
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

        $this->db->trans_complete();
        
        // 增加抽奖机会
        /*$userInfo = $this->db->query("SELECT id, uid, total_num, left_num, created 
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
        }*/

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
    	$sql = "select count(id) from cp_activity_lx_join where puid=? and activity_id=9 and created > DATE_SUB(NOW(),INTERVAL 1 DAY)";
    	return $this->db->query($sql, array($uid))->getCol();
    }
}
