<?php

class server_task_model extends CI_Model
{
    //处理成功code
    const CODE_SUCESS = 0;
    //需要重试code
    const CODE_RETRY = 1;
    //错误code
    const CODE_ERROR = 3;
    public function __construct()
    {
        parent::__construct();
        $this->load->library('tools');
    }
    
    /**
     * 任务处理失败时入库
     * @param unknown $datas
     * @return unknown
     */
    public function errorRecord($datas)
    {
        $db = $this->load->database('default', true);
        $sql = "insert cp_server_task_retry(data, created) values(?, now())";
        return $db->query($sql, array($datas));
    }
    
    /**
     * 首次登录获得成长值方法
     * @param unknown $data
     * @return string[]
     */
    public function loginGrowth($data)
    {
        if(empty(intval($data['uid'])) || empty(intval($data['value'])) || empty($data['overTime']))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '登录-参数错误'];
        }
        
        $data['value'] = floor($data['value']); //舍弃取整操作
        $db = $this->load->database('default', true);
        $db->trans_start();
        //加锁锁住uid用户成长值记录
        $growth = $db->query("select uid, grade_value from cp_user_growth where uid = ? for update", array($data['uid']))->getRow();
        
        $sql = "select uid from cp_growth_logs where overTime >= ? and overTime <= ? and uid=? and ctype=1 and mark = '1' limit 1";
        $overTime = date('Y-m-d', strtotime($data['overTime']));
        $uid = $db->query($sql, array($overTime, $overTime . ' 23:39:59', $data['uid']))->getCol();
        //当天未加过
        if(empty($uid))
        {
            $sql1 = "insert cp_user_growth(uid, grade_value, grade_days, cycle_start, cycle_end, grade_after,created)
            values(?, ?, 1, now(), '".date('Y-m-d', strtotime("+1 year"))." 23:59:59', 2, now()) on duplicate key update
            grade_value = grade_value + values(grade_value), grade_days = grade_days + values(grade_days)";
            $res1 = $db->query($sql1, array($data['uid'], $data['value']));
            $sql2 = "insert cp_growth_logs(uid, value, mark, ctype, trade_no, uvalue, overTime, created)
            values(?, ? , '1', 1, ?, ?, ?, now())";
            $sql2Data = [
                $data['uid'],
                $data['value'],
                $this->tools->getIncNum('UNIQUE_KEY'),
                intval($growth['grade_value']) + $data['value'],
                $data['overTime']
            ];
            $res2 = $db->query($sql2, $sql2Data);
            if($res1 && $res2)
            {
                $db->trans_complete();
                $uGrowth = $this->freshUserGrowth($data['uid']);
                //判断是否升级
                $this->isGrowthUp($uGrowth);
                return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
            }
            else
            {
                $db->trans_rollback();
                return ['code' => self::CODE_RETRY, 'msg' => '操作失败，需要重试'];
            }
        }
        else
        {
            //如果已加过 直接返回成功
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        
    }
    
    /**
     * 购彩增加成长值操作
     */
    public function buyLotteryGrowth($data)
    {
        if(empty(intval($data['uid'])) || empty(intval($data['value'])) || empty($data['overTime']) || empty($data['orderId']))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '购彩-成长值-参数错误'];
        }
        
        $data['value'] = floor($data['value']); //舍弃取整操作
        $db = $this->load->database('default', true);
        $db->trans_start();
        //加锁锁住uid用户成长值记录
        $res = $db->query("select uid, grade_value from cp_user_growth where uid = ? for update", array($data['uid']))->getRow();
        if(!$res)
        {
            //成长值表无记录直接返回成功
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        
        $sql = "select grade_total from cp_user_growth_total where uid = ? and date = ?";
        $grade_total = $db->query($sql, array($data['uid'], date('Y-m-d', strtotime($data['overTime']))))->getOne();
        $grade_total = intval($grade_total);
        if($grade_total >= 5000)
        {
            //如果达到上限 直接返回成功
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        
        $data['value'] = ((5000 - $grade_total) > $data['value']) ? $data['value'] : (5000 - $grade_total);
        
        if($data['orderType'] == '4')
        {
            //合买单处理
            $sql = "select uid from cp_growth_logs where uid=? and mark='1' and ctype='3' and orderId=? and subscribeId = ? and status= ? limit 1";
            $uid = $db->query($sql, array($data['uid'], $data['orderId'], $data['subscribeId'], $data['status']))->getCol();
            if(!empty($uid))
            {
                //如果已加过 直接返回成功
                $db->trans_complete();
                return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
            }
            $sql2 = "insert cp_growth_logs(uid, value, mark, ctype, trade_no, orderId, subscribeId, uvalue, status, content, overTime, created)
            values(?, ? , '1', 3, ?, ?, ?, ?, ?, ?, ?, now())";
            $sql2Data = [
                $data['uid'],
                $data['value'],
                $this->tools->getIncNum('UNIQUE_KEY'),
                $data['orderId'],
                $data['subscribeId'],
                intval($res['grade_value']) + $data['value'],
                $data['status'],
                $data['content'],
                $data['overTime']
            ];
        }
        else
        {
            //普通单处理
            $sql = "select uid from cp_growth_logs where uid=? and mark='1' and ctype='3' and orderId=? and status = ? limit 1";
            $uid = $db->query($sql, array($data['uid'], $data['orderId'], $data['status']))->getCol();
            if(!empty($uid))
            {
                //如果已加过 直接返回成功
                $db->trans_complete();
                return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
            }
            
            $sql2 = "insert cp_growth_logs(uid, value, mark, ctype, trade_no, orderId, uvalue, status, content, overTime, created)
            values(?, ? , '1', 3, ?, ?, ?, ?, ?, ?, now())";
            $sql2Data = [
                $data['uid'],
                $data['value'],
                $this->tools->getIncNum('UNIQUE_KEY'),
                $data['orderId'],
                intval($res['grade_value']) + $data['value'],
                $data['status'],
                $data['content'],
                $data['overTime']
            ];
        }
        
        $sql1 = "update cp_user_growth set grade_value = grade_value + {$data['value']} where uid= ?";
        $res1 = $db->query($sql1, array($data['uid']));
        $res2 = $db->query($sql2, $sql2Data);
        $sql3 = "insert cp_user_growth_total(uid, date, grade_total, created)values(?, ?, ?, now()) on duplicate key update
            grade_total = grade_total + values(grade_total)";
        $res3 = $db->query($sql3, array($data['uid'], date('Y-m-d', strtotime($data['overTime'])), $data['value']));
        if($res1 && $res2 && $res3)
        {
            $db->trans_complete();
            $uGrowth = $this->freshUserGrowth($data['uid']);
            //判断是否升级
            $this->isGrowthUp($uGrowth);
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        else
        {
            $db->trans_rollback();
            return ['code' => self::CODE_RETRY, 'msg' => '操作失败，需要重试'];
        }
    }
    
    /**
     * 购彩加积分操作
     * @param unknown $data
     * @return string[]
     */
    public function buyLotteryPoint($data)
    {
        if(empty(intval($data['uid'])) || empty(intval($data['value'])) || empty($data['overTime']) || empty($data['orderId']))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '购彩-积分-参数错误'];
        }
        
        $data['value'] = floor($data['value']); //舍弃取整操作
        $db = $this->load->database('default', true);
        $db->trans_start();
        //加锁锁住uid用户成长值记录
        $uinfo = $db->query("select uid, grade, points from cp_user_growth where uid = ? for update", array($data['uid']))->getRow();
        if(!$uinfo)
        {
            //成长值表无记录直接返回成功
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        
        $data['points'] = $uinfo['points'];
        $data['p_total'] = 'points_total';
        $res1 = $this->addPoint($db, $data);
        $res2 = true;
        $grade = $db->query("select privilege from cp_growth_level where grade = ?", array($uinfo['grade']))->getRow();
        if($grade)
        {
            $privilege = json_decode($grade['privilege'], true);
            if($privilege['double'] > 0)
            {
                //积分加倍的情况下
                $data['points'] = $uinfo['points'] + $data['value'];
                $data['p_total'] = 'double_points_total';
                $data['content'] = '双倍购彩积分';
                $res2 = $this->addPoint($db, $data);
            }
        }
        
        if($res1 && $res2)
        {
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        else
        {
            $db->trans_rollback();
            $this->freshUserGrowth($data['uid'], $db);
            return ['code' => self::CODE_RETRY, 'msg' => '操作失败，需要重试'];
        }
    }
    
    /**
     * 加积分操作
     * @param unknown $data
     * @return boolean
     */
    private function addPoint($db, $data)
    {
        $sql = "select {$data['p_total']} from cp_user_growth_total where uid = ? and date = ?";
        $p_total = $db->query($sql, array($data['uid'], date('Y-m-d', strtotime($data['overTime']))))->getOne();
        $p_total = intval($p_total);
        if($p_total >= 5000)
        {
            return true;
        }
        
        $data['value'] = ((5000 - $p_total) > $data['value']) ? $data['value'] : (5000 - $p_total);
        //判断积分类型  0 购彩  2 赠送
        $ctype = $data['p_total'] == 'points_total' ? 0 : 2;
        if($data['orderType'] == '4')
        {
            //合买单处理
            $sql = "select uid from cp_points_logs where uid=? and mark='1' and ctype= ? and orderId=? and subscribeId = ? and status= ? limit 1";
            $uid = $db->query($sql, array($data['uid'], $ctype, $data['orderId'], $data['subscribeId'], $data['status']))->getCol();
            if(!empty($uid))
            {
                return true;
            }
            $sql2 = "insert cp_points_logs(uid, value, mark, ctype, cvalue, trade_no, orderId, subscribeId, uvalue, status, content, overTime, created)
            values(?, ? , '1', ?, ?, ?, ?, ?, ?, ?, ?, ?, now())";
            $sql2Data = [
                $data['uid'],
                $data['value'],
                $ctype,
                $data['lid'],
                $this->tools->getIncNum('UNIQUE_KEY'),
                $data['orderId'],
                $data['subscribeId'],
                intval($data['points']) + $data['value'],
                $data['status'],
                $data['content'],
                $data['overTime']
            ];
        }
        else
        {
            //普通单处理
            $sql = "select uid from cp_points_logs where uid=? and mark='1' and ctype= ? and orderId=? and status = ? limit 1";
            $uid = $db->query($sql, array($data['uid'], $ctype, $data['orderId'], $data['status']))->getCol();
            if(!empty($uid))
            {
                return true;
            }
            $sql2 = "insert cp_points_logs(uid, value, mark, ctype, cvalue, trade_no, orderId, uvalue, status, content, overTime, created)
            values(?, ? , '1', ?, ?, ?, ?, ?, ?, ?, ?, now())";
            $sql2Data = [
                $data['uid'],
                $data['value'],
                $ctype,
                $data['lid'],
                $this->tools->getIncNum('UNIQUE_KEY'),
                $data['orderId'],
                intval($data['points']) + $data['value'],
                $data['status'],
                $data['content'],
                $data['overTime']
            ];
        }
        
        $sql1 = "update cp_user_growth set points = points + {$data['value']} where uid= ?";
        $res1 = $db->query($sql1, array($data['uid']));
        $res2 = $db->query($sql2, $sql2Data);
        $sql3 = "insert cp_user_growth_total(uid, date, {$data['p_total']}, created)values(?, ?, ?, now()) on duplicate key update
            {$data['p_total']} = {$data['p_total']} + values({$data['p_total']})";
        $res3 = $db->query($sql3, array($data['uid'], date('Y-m-d', strtotime($data['overTime'])), $data['value']));
        if($res1 && $res2 && $res3)
        {
            $this->freshUserGrowth($data['uid'], $db);
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * 购彩完成任务操作
     * @param unknown $data
     */
    public function buyLotteryJob($data)
    {
        $uinfo = $this->getUserInfo($data['uid']);
        if(empty(intval($data['uid'])) || empty($data['overTime']) || empty($data['jobId']) || (!isset($data['jobType'])) || empty($uinfo))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '购彩-任务-参数错误'];
        }
        
        $db = $this->load->database('default', true);
        $job = $db->query("select id from cp_points_jobs where id = ? and type = ? and is_delete = '0'", array($data['jobId'], $data['jobType']))->getRow();
        if(empty($job))
        {
            return ['code' => self::CODE_ERROR, 'msg' => '购彩-任务-任务不存在或已下线'];
        }
        $db->trans_start();
        $res = $db->query("select uid, my_task_get(points_job_params, {$data['jobId']}, {$data['jobType']}) as jobStatus from cp_user_growth where uid = ? for update", array($data['uid']))->getRow();
        if(empty($res) || $res['jobStatus'] != '0')
        {
            //成长值表无记录或认为已完成直接返回成功
            $db->trans_complete();
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        
        $sql1 = "update cp_user_growth set points_job_params = my_task_set(points_job_params, {$data['jobId']}, 1) where uid = ?";
        $res1 = $db->query($sql1, array($data['uid']));
        $sql2 = "insert into cp_points_jobs_logs(uid, job_id, created) values(?, ?, ?)";
        $res2 = $db->query($sql2, array($data['uid'], $data['jobId'], $data['overTime']));
        if($res1 && $res2)
        {
            $db->trans_complete();
            $this->freshUserGrowth($data['uid']);
            return ['code' => self::CODE_SUCESS, 'msg' => '操作成功'];
        }
        else
        {
            $db->trans_rollback();
            return ['code' => self::CODE_RETRY, 'msg' => '操作失败，需要重试'];
        }
    }
    
    /**
     * 获取用户缓存信息
     * @param unknown $uid
     * @return unknown
     */
    public function getUserInfo($uid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $uinfo = $this->cache->redis->hGetAll($ukey);
        
        return $uinfo;
    }
    
    /**
     * 刷新用户成长缓存信息
     * @param unknown $uid
     */
    public function freshUserGrowth($uid, $db = '')
    {
        if(empty($db))
        {
            $db = $this->load->database('default', true);
        }
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $sql = "select uid, grade, grade_value, points, last_year_points, grade_days, cycle_start, cycle_end, grade_before, grade_after, rank, points_job_params
        from cp_user_growth where uid = ?";
        $uGrowth = $db->query($sql, array($uid))->getRow();
        if(empty($uGrowth))
        {
            //防止有遗漏进行初始化数据
            $db->query("insert ignore cp_user_growth(uid, cycle_start, cycle_end,created) values (?, now(), ?, now())", array($uid, date('Y-m-d', strtotime("+1 year")) . " 23:59:59"));
            $uGrowth = $db->query($sql, array($uid))->getRow();
        }
        
        $this->cache->redis->hMSet($ukey, $uGrowth);
        
        return $uGrowth;
    }
    
    /**
     * 升级操作
     * @param unknown $uGrowth
     */
    public function isGrowthUp($uGrowth)
    {
        $db = $this->load->database('default', true);
        $sql = "select * from cp_growth_level where grade >= ? limit 3";
        $levels = $db->query($sql, array($uGrowth['grade_after']))->getAll();
        foreach ($levels as $key => $level)
        {
            if($level['value_start'] <= $uGrowth['grade_value'] && $uGrowth['grade'] < $level['grade'])
            {
                $db->trans_start();
                $sql = "select uid from cp_growth_logs where overTime >= ? and uid=? and ctype=2 and cvalue = ? limit 1 for update";
                $count = $db->query($sql, array($uGrowth['cycle_start'], $uGrowth['uid'], $uGrowth['grade_after']))->getOne();
                if($count < 1)
                {
                    $sql1 = "insert cp_growth_logs(uid, value, mark, ctype, cvalue, trade_no, uvalue, content, overTime, created)
                    values(?, ?, '0', 2, ?, ?, ?, ?, ?, now())";
                    $sql1Data = [
                        $uGrowth['uid'],
                        $level['value_start'],
                        $level['grade'],
                        $this->tools->getIncNum('UNIQUE_KEY'),
                        $uGrowth['grade_value'] - $level['value_start'],
                        '升级为' . $level['grade_name'],
                        date('Y-m-d H:i:s'),
                    ];
                    
                    $res1 = $db->query($sql1, $sql1Data);
                    $grade_after = isset($levels[$key + 1]) ? $levels[$key + 1]['grade'] : $level['grade'];
                    $sql2 = "update cp_user_growth set grade = '{$level['grade']}',
                    grade_value = grade_value - {$level['value_start']},
                    grade_days = '0',
                    pop_status = '0',
                    cycle_start = now(),
                    cycle_end = '".date('Y-m-d', strtotime("+1 year"))." 23:59:59',
                    grade_before = '{$uGrowth['grade']}',
                    grade_after = '{$grade_after}' where uid= '{$uGrowth['uid']}'";
                    $res2 = $db->query($sql2);
                    $res3 = true;
                    $sendsms = false;
                    $privilege = json_decode($level['privilege'], true);
                    //发红包
                    if(!empty($privilege['upgrade']))
                    {
                        $rids = array_keys($privilege['upgrade']);
                        $rid = $rids[0];
                        $redpack = $db->query("select aid,money,p_name,use_params,use_desc from cp_redpack where id = ?", array($rid))->getRow();
                        $sql3 = "insert cp_redpack_log(aid,uid,rid,valid_start,valid_end, get_time, status,created)values
                        ('{$redpack['aid']}', {$uGrowth['uid']}, $rid, now(), '".date('Y-m-d H:i:s', strtotime("+10 year"))."', now(), 1, now())";
                        $res2 = $db->query($sql3);
                        $sendsms = true;
                    }
                    if($res1 && $res2 && $res3)
                    {
                        $db->trans_complete();
                        $uGrowth = $this->freshUserGrowth($uGrowth['uid']);
                        if($sendsms)
                        {
                            //发短信
                            $uinfo = $this->getUserInfo($uGrowth['uid']);
                            $money = number_format((doubleval($redpack['money']) / 1000) * 10);
                            $message = $this->config->item('MESSAGE');
                            $msg = $message['user_upgrade'];
                            $uinfo['uname'] = utf8_substr($uinfo['uname'], 0, 2) . '**';
                            $msg = str_replace(array('#UNAME#', '#GRADENAME#', '#MONEY#'), array($uinfo['uname'], $level['grade_name'], $money), $msg);
                            $this->tools->sendSms($uGrowth['uid'], $uinfo['phone'], $msg, 10, '127.0.0.1', '243');
                        }
                    }
                    else
                    {
                        $db->trans_rollback();
                        return ;
                    }
                }
                else
                {
                    $db->trans_complete();
                }
            }
        }
        
        return true;
    }
}

?>