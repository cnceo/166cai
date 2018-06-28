<?php

class Growth_Cli_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 查询需要处理订单
     * @param unknown $orderType
     * @return unknown
     */
    public function getOrderTrg($orderType)
    {
        //合买单
        if($orderType == '4')
        {
            $sql = "select orderId, modified from {$this->db_config['tmp']}.cp_orders_trg where modified > date_sub(now(), interval 1 day) 
            and orderType = '4' and source1 = '1' and source2='1' limit 10";
        }
        else
        {
            $sql = "select orderId, modified from {$this->db_config['tmp']}.cp_orders_trg where modified > date_sub(now(), interval 1 day)
            and orderType != '4' and source1 = '1' limit 50";
        }
        
        return $this->db->query($sql)->getAll();
    }
    
    /**
     * 删除已处理订单
     * @param unknown $orderIds
     * @return unknown
     */
    public function delTrgs($orderIds)
    {
        return $this->db->query("delete from {$this->db_config['tmp']}.cp_orders_trg where orderId in ?", array($orderIds));
    }
    
    /**
     * 查询订单信息
     * @param unknown $orders
     * @return unknown
     */
    public function getOrders($orders)
    {
        $sql = "select o.uid, o.orderId, o.lid, o.money, o.orderType, o.failMoney, d.redpackId, d.redpackMoney from cp_orders o 
        left join cp_orders_detail d on d.orderId = o.orderId where o.orderId in ?";
        return $this->db->query($sql, array($orders))->getAll();
    }
    
    /**
     * 查询合买跟单记录
     * @param unknown $orders
     * @return unknown
     */
    public function getUnitedJoins($orders)
    {
        $sql = "select orderId,subscribeId,lid,uid,buyMoney,orderType from cp_united_join where orderId in ? and orderType in('1', '2', '3')";
        return $this->db->query($sql, array($orders))->getAll();
    }
    
    /**
     * 返回指定等级的用户
     * @param unknown $rade
     * @param unknown $start
     * @param unknown $limit
     * @return unknown
     */
    public function getGrowthUsers($rade, $start, $limit)
    {
        $sql = "select uid, grade_value from cp_user_growth where grade = ? order by grade_value asc limit $start, $limit";
        return $this->db->query($sql, array($rade))->getAll();
    }
    
    /**
     * 查询用户任务状态
     * @param int $jobId    任务id
     * @param int $jobType  任务类型  0 一次性任务  1 每天  7 每周 等等
     * @param array $uids   查询的uid
     * @return unknown
     */
    public function getJobStatus($jobId, $jobType, $uids)
    {
        $sql = "select uid, my_task_get(points_job_params, $jobId, $jobType) as jobStatus from cp_user_growth where uid in ?";
        return $this->db->query($sql, array($uids))->getAll();
    }
    
    /**
     * 返回成长等级信息
     * @return unknown
     */
    public function getGradeInfo()
    {
        return $this->db->query("select grade, grade_name, value_start, value_end, privilege from cp_growth_level where 1")->getAll();
    }
    
    /**
     * 返回指定等级的用户
     * @param unknown $rade
     * @param unknown $start
     * @param unknown $limit
     * @return unknown
     */
    public function getActiveUsers($start, $end)
    {
        $sql = "select DISTINCT uid from cp_growth_logs where created BETWEEN ? AND ?";
        return $this->db->query($sql, array($start, $end))->getCol();
    }
    
    /**
     * 更新用户排名数据
     * @param unknown $fields
     * @param unknown $datas
     * @return unknown
     */
    public function updateUserRank($bdata)
    {
        $fields = array('uid', 'rank');
        $sql = "insert cp_user_growth(" . implode(', ', $fields) . ") values" . implode(', ', $bdata['s_data']) . $this->onduplicate($fields, array('rank'));
        return $this->db->query($sql, $bdata['d_data']);
    }
    
    /**
     * 保级降级操作
     * @param unknown $date
     */
    public function downgrade($dateTime)
    {
        $this->load->model('user_model');
        $gradeInfo = $this->getGradeInfo();
        $grades = array();
        foreach ($gradeInfo as $value)
        {
            $grades[$value['grade']] = $value;
        }
        $sql = "select uid, grade, grade_value from cp_user_growth where cycle_end <= ? limit 500";
        $users = $this->db->query($sql, array($dateTime))->getAll();
        while ($users)
        {
            foreach ($users as $user)
            {
                if($user['grade_value'] < $grades[$user['grade']]['value_start'])
                {
                    $this->db->trans_start();
                    //降级操作
                    $sql1 = "update cp_user_growth set grade = ?, grade_value = ?, grade_days = '0', pop_status = '1', cycle_start = now(), cycle_end = ?, grade_before = ?, grade_after = ? where uid = ?";
                    $grade = $user['grade'] - 1;
                    $grade = $grade < 1 ? 1 : $grade;
                    $res1 = $this->db->query($sql1, array($grade, 0, date('Y-m-d', strtotime("+1 year")) . " 23:59:59", $user['grade'], $user['grade'], $user['uid']));
                    $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
                    $content = '降级为' . $grades[$grade]['grade_name'];
                    $sql2 = "insert cp_growth_logs(uid,value,ctype,cvalue, trade_no, uvalue, content, created) values(?, ?, ?, ?, ?, ?, ?, now())";
                    $res2 = $this->db->query($sql2, array($user['uid'], $user['grade_value'], 4, $user['grade'], $trade_no, 0, $content));
                    if($res1 && $res2)
                    {
                        $this->db->trans_complete();
                        //刷新缓存
                        $this->user_model->freshUserInfo($user['uid']);
                    }
                    else 
                    {
                        $this->db->trans_rollback();
                    }
                }
                else
                {
                    $this->db->trans_start();
                    //保级操作
                    $value_start = $grades[$user['grade']]['value_start'] != 0 ? $grades[$user['grade']]['value_start'] : $user['grade_value'];
                    $grade_value = $user['grade_value'] - $value_start;
                    $sql1 = "update cp_user_growth set grade = ?, grade_value = ?, grade_days = '0', cycle_start = now(), cycle_end = ?, grade_before = ? where uid = ?";
                    $res1 = $this->db->query($sql1, array($user['grade'], $grade_value, date('Y-m-d', strtotime("+1 year")) . " 23:59:59", $user['grade'], $user['uid']));
                    $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
                    $content = '保级为' . $grades[$user['grade']]['grade_name'];
                    $sql2 = "insert cp_growth_logs(uid,value,ctype,cvalue, trade_no, uvalue, content, created) values(?, ?, ?, ?, ?, ?, ?, now())";
                    $res2 = $this->db->query($sql2, array($user['uid'], $value_start, 5, $user['grade'], $trade_no, $grade_value, $content));
                    if($res1 && $res2)
                    {
                        $this->db->trans_complete();
                        //刷新缓存
                        $this->user_model->freshUserInfo($user['uid']);
                    }
                    else
                    {
                        $this->db->trans_rollback();
                    }
                }
            }
            
            $users = $this->db->query($sql, array($dateTime))->getAll();
        }
    }
    
    /**
     * 用户生日礼包派发
     * @param unknown $date
     */
    public function sendBirth($date)
    {
        $this->load->model('user_model');
        $gradeInfo = $this->getGradeInfo();
        $grades = array();
        foreach ($gradeInfo as $grade)
        {
            $privilege = json_decode($grade['privilege'], true);
            if(!empty($privilege['birthday']))
            {
                $gids = array_keys($privilege['birthday']);
                $redpacks = $this->db->query("select id, aid, money, money_bar, use_params from cp_redpack where id in ? and delete_flag='0' order by money asc", array($gids))->getAll();
                if(empty($redpacks))
                {
                    continue;
                }
                
                $id = 0;
                $sql = "SELECT g.id, g.uid, i.birthday FROM cp_user_growth g 
                INNER JOIN cp_user_info i ON i.uid=g.uid WHERE g.id > ? AND g.grade= ? AND i.birthday= ? 
                ORDER BY g.id ASC LIMIT 100";
                $users = $this->db->query($sql, array($id, $grade['grade'], $date))->getAll();
                while ($users)
                {
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $uids = array();
                    foreach ($users as $user)
                    {
                        $id = $user['id'];
                        foreach ($redpacks as $redpack)
                        {
                            array_push($bdata['s_data'], "(?, ?, ?, ?, ?, now(), 1, now())");
                            array_push($bdata['d_data'], $redpack['aid']);
                            array_push($bdata['d_data'], $user['uid']);
                            array_push($bdata['d_data'], $redpack['id']);
                            $useParams = json_decode($redpack['use_params'], true);
                            if(isset($useParams['no_expire']))
                            {
                                array_push($bdata['d_data'], date('Y-m-d H:i:s'));
                                array_push($bdata['d_data'], date('Y-m-d H:i:s', strtotime("+10 year")));
                            }
                            else
                            {
                                array_push($bdata['d_data'], date('Y-m-d H:i:s'));
                                array_push($bdata['d_data'], date('Y-m-d H:i:s', strtotime("+ {$useParams['end_day']} days")));
                            }
                            
                            $uids[$user['uid']] += $redpack['money'];
                        }
                    }
                    
                    if(!empty($bdata['s_data']))
                    {
                        $rsql = "insert ignore cp_redpack_log(aid,uid,rid,valid_start,valid_end,get_time,status,created) values" . implode(', ', $bdata['s_data']);
                        $this->db->query($rsql, $bdata['d_data']);
                    }
                    
                    if($uids)
                    {
                        foreach ($uids as $uid => $money)
                        {
                            $this->user_model->sendSms($uid, array('#MONEY#' => $money), 'user_birth', null, '127.0.0.1', '243');
                        }
                    }
                    
                    $users = $this->db->query($sql, array($id, $grade['grade'], $date))->getAll();
                }
            }
        }
    }
    
    /**
     * 上一年度积分转存
     */
    public function pointTransfer()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        
        $id = 0;
        $sql = "SELECT uid FROM cp_user_growth WHERE id > ? AND points > 0
        ORDER BY id ASC LIMIT 50";
        $users = $this->db->query($sql, array($id))->getCol();
        while ($users)
        {
            $uids = array();
            foreach ($users as $uid)
            {
                $uids[] = $uid;
            }
            //加事务保证并发下刷缓存不会错乱
            $this->db->trans_start();
            $sql1 = "update cp_user_growth set last_year_points= points where uid in ?";
            $res1 = $this->db->query($sql1, array($uids));
            $sql2 = "select id, uid, last_year_points from cp_user_growth where uid in ? ORDER BY id ASC";
            $res2 = $this->db->query($sql2, array($uids))->getAll();
            if($res1 && $res2)
            {
                foreach ($res2 as $user)
                {
                    $ukey = "{$REDIS['USER_INFO']}{$user['uid']}";
                    $this->cache->redis->hSet($ukey, "last_year_points", $user['last_year_points']);
                    $id = $user['id'];
                }
                
                $this->db->trans_complete();
            }
            else
            {
                $this->db->trans_rollback();
            }
            
            $users = $this->db->query($sql, array($id))->getAll();
        }
    }
    
    /**
     * 清除上一年度积分
     */
    public function pointEmpty()
    {
        $REDIS = $this->config->item('REDIS');
        $this->load->driver('cache', array('adapter' => 'redis'));
        
        $sql = "SELECT uid FROM cp_user_growth WHERE last_year_points > 0 ORDER BY id ASC LIMIT 50";
        $users = $this->db->query($sql)->getCol();
        while ($users)
        {
            foreach ($users as $uid)
            {
                $this->db->trans_start();
                $sql1 = "select id, uid, points, last_year_points from cp_user_growth where uid = ? for update";
                $res1 = $this->db->query($sql1, array($uid))->getRow();
                $sql2 = "update cp_user_growth set points= points - last_year_points, last_year_points= 0 where uid = ?";
                $res2 = $this->db->query($sql2, array($uid));
                $sql3 = "insert cp_points_logs(uid, value, mark, ctype, trade_no, uvalue, content, overTime, created)
                values(?, ?, '0', 4, ?, ?, ?, now(), now())";
                $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
                $content = date('Y', strtotime("-1 year")) . '年度过期积分清除';
                $points = $res1['points'] - $res1['last_year_points'];
                $points = $points < 0 ? 0 : $points;
                $res3 = $this->db->query($sql3, array($uid, $res1['last_year_points'], $trade_no, $res1['points'] - $res1['last_year_points'], $content));
                if($res1 && $res2 && $res3)
                {
                    $ukey = "{$REDIS['USER_INFO']}{$uid}";
                    $this->cache->redis->hSet($ukey, "points", $points);
                    $this->cache->redis->hSet($ukey, "last_year_points", 0);
                    $this->db->trans_complete();
                }
                else
                {
                    $this->db->trans_rollback();
                }
            }
            
            $users = $this->db->query($sql, array($id))->getAll();
        }
    }
    
    /**
     * 重置红包今日兑换数量
     * @return unknown
     */
    public function resetRedpackStock()
    {
        return $this->db->query("update cp_redpack_stock set today_out = next_out, already_out = 0 where 1");
    }
    
    /**
     * 查询重新入队数据
     * @return unknown
     */
    public function getTaskRetry()
    {
        $sql = "select id, data from cp_server_task_retry where modified > date_sub(now(), interval 10 minute) limit 50";
        return $this->db->query($sql)->getAll();
    }
    
    /**
     * 删除数据
     * @param unknown $ids
     * @return unknown
     */
    public function delTaskRetry($ids)
    {
        return $this->db->query("delete from cp_server_task_retry where id in ?", array($ids));
    }
}
