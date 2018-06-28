<?php
/**
 * Copyright (c) 2015,上海二三四五网络科技股份有限公司
 * 摘    要：渠道分析
 * 作    者：yangqy@2345.com
 * 修改日期：2015.07.22
 */
class Model_Channel_Analysis extends MY_Model
{
    private $settleModeArr = array(
            '1' => 'CPA',
            '2' => 'CPS',
            '3' => 'CPT',
        );
    //与数据库中字段对应
    private $settleModeDB = array(
         '1' => 'subtract_coefficient',
         '2' => 'share_ratio',
         '3' => 'month_fee'
    );
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
   public function click($searchData)
    {
        $where = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        //以下为一条语句查询
        if($searchData['platform'] == '2')//手机
        {
            if($searchData['version'] != 'all')
            {
                $where .= $this->condition("version", $searchData['version']);
            }
            $sql = "SELECT c.id,c.name,u.uv ,u.pv,u.click_uv,u.click_pv,u.lv
                FROM cp_channel c
                LEFT JOIN
                    (SELECT channel_id, sum(new_num) pv, sum(history_active_num) uv, sum(history_actionuv_num) click_uv,
                        sum(history_actionpv_num) click_pv,sum(history_launch_num) lv
                    FROM cp_50bang_app {$where}
                    group by channel_id) u
                ON u.channel_id=c.id
                WHERE c.platform = 2";
        }
        else
        {
            $sql = "SELECT c.id,c.name,u.uv,u.pv,u.click_uv,u.click_pv
                FROM cp_channel c
                LEFT JOIN
                    (SELECT channel_id,sum(browse_uv) uv,sum(browse_pv) pv,sum(click_uv) click_uv,
                        sum(click_pv) click_pv
                    FROM cp_50bang_web {$where}
                    group by channel_id) u
                on u.channel_id=c.id
                WHERE 1 AND c.platform = 1";
        }
        $list = $this->BcdDb->query($sql)->getAll();
        
        return $list;
    }
    
    public function register($searchData)
    {
        $where = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        if($searchData['version'] != 'all' && $searchData['platform'] == '2')
        {
            $where .= $this->condition("version", $searchData['version']);
        }
        $where2 = $where;
        $where .= $this->condition("cdate", array(
                $startDate,
                $endDate
        ), "time");//cp_register_stat
        $where .= $this->condition("platform",$searchData["platform"]);
        $where2 .= $this->condition("date",array(
                $startDate,
                $endDate
        ), "time");
        if($searchData['platform'] == '1')
        {
            $sql = "SELECT c.id, c.name, r.register_num, r.valid_user, r.complete_user, u.uv
                FROM cp_channel c
                LEFT JOIN
                    (SELECT channel_id, sum(register_num) register_num, sum(valid_user) valid_user,
                        sum(complete_user) complete_user
                    FROM cp_register_stat {$where}
                    GROUP BY channel_id) r
                ON r.channel_id = c.id
                LEFT JOIN
                    (SELECT channel_id channel,sum(browse_uv) uv
                    FROM cp_50bang_web {$where2}
                    GROUP BY channel) u
                ON u.channel=c.id
                WHERE c.platform = 1";
        }
        else
        {
            $sql = "SELECT c.id, c.name, r.register_num, r.valid_user, r.complete_user, u.uv
                FROM cp_channel c
                LEFT JOIN
                    (SELECT channel_id, sum(register_num) register_num, sum(valid_user) valid_user,
                        sum(complete_user) complete_user
                    FROM cp_register_stat {$where}
                    GROUP BY channel_id) r
                ON r.channel_id = c.id
                LEFT JOIN
                    (SELECT channel_id channel,sum(history_active_num) uv
                    FROM cp_50bang_app {$where2}
                    GROUP BY channel) u
                ON u.channel=c.id
                WHERE c.platform = 2";
        }
        $list = $this->BcdDb->query($sql)->getAll();

        return $list;
    }
    
    /*
     * 渠道管理
     *
     */
    public function manage($searchData)
    {
        $where = " where 1";
        $where .= $this->condition("c.platform", $searchData['platform']);
        $where .= $this->condition("c.name", $searchData['name'], 'like');
        $where .= $this->condition("c.id", $searchData['id']);
        $where .= $this->condition("c.status", $searchData['status']);
        $searchData['settlemode'] != 0
        &&
        $where .= $this->condition("c.settle_mode", $searchData['settlemode']);
        $searchData['package'] != 0
        &&
        $where .= $this->condition("c.package", $searchData['package']);
        $list = $this->BcdDb->query("SELECT c.id, c.name, c.settle_mode, c.unit_price, c.subtract_coefficient, c.share_ratio, c.month_fee, c.app_path, c.ios_download, c.cstate, c.status, c.reg_time, c.nick_name, c.package, p.name AS pname FROM cp_channel AS c LEFT JOIN cp_channel_package AS p ON c.package = p.id {$where}")->getAll();
        return array(
            'list' => $list
        );
    }
    
    /*
     * 
     * 更新字段的值
     * */
    public function updateCol($searchData)
    {
        //查询渠道是否已有
        if($this->checkIsRepeat($searchData['name'],$searchData['id']) == false)
        {
            return "tn";
        }
        $where = " where 1";
        $querywhere = $where.$this->condition("id", intval($searchData['id']));
        $query .= "select id,name,settle_mode,unit_price,subtract_coefficient,share_ratio,
                    month_fee, modified ,reg_time,nick_name from cp_channel {$querywhere}";//查询
        $channelLogquery .= "insert into cp_channel_change_log(channel_id,name,settle_mode,unit_price,subtract_coefficient,share_ratio,month_fee,change_time,reg_time,nick_name) ".$query;
        if(isset($searchData['settle_mode']))
        {
            //更新其他支付方式为默认值0.00
            foreach($this->settleModeArr as $key => $val)
            {
                if($searchData['settle_mode'] != $key)
                {
                    $searchData[$this->settleModeDB[$key]] = 0.00;                  
                }
                if($searchData['settle_mode'] != 1)
                {
                    $searchData["unit_price"] = 0.00;//如果要修改的结算方式为cps，cpt，将单价清零
                }
            }
        }
        //开启事务，确保更新和记录一起完成
        $this->master->trans_strict(FALSE);//不启用严格模式
        $this->master->trans_start();
        $this->master->where('id',$searchData['id']);
        $tag = $this->master->update('cp_channel',$searchData);
        $this->master->query($channelLogquery);
        $this->master->trans_complete(); 
        if ($this->master->trans_status() === FALSE)
        {
            return 'n';
        }else
        {
            //如果更改渠道名，刷新redis
            if(isset($searchData['name']))
            {
                $this->flushRedis();
            }
            return 'y';
        }
    }

    /*
     *新增渠道
     * */
    public function update($searchData)
    {
        //查询渠道是否已有
        if($this->checkChannelName($searchData['name']) == false)
        {
            return array('status' => 'tn', 'channel_id' => 0);
        }
        $searchData["created"] = date('Y-m-d H:i:s',time());
        $insertData = array();
        foreach($searchData as $key => $val)
        {
            if($key !== "created")
            {
                $insertData[$key] = $val;
            }else
            {
                $insertData['change_time'] = $val;
            }
        }

        $this->master->trans_strict(FALSE);//不启用严格模式
        $this->master->trans_start();
        $this->master->insert('cp_channel',$searchData);
        $insertId = $this->master->insert_id();
        $insertData['channel_id'] = $insertId;
        unset($insertData['package']);
        $this->master->insert('cp_channel_change_log',$insertData); 
        $this->master->trans_complete();
        if ($this->master->trans_status() === FALSE)
        {
            return array('status' => 'n', 'channel_id' => 0);
        }else
        {
            $this->flushRedis();
            return array('status' => 'y', 'channel_id' => $insertId);
        }
         
    }
    
    
    /*
     * 更改日志查看
     * */
    public function loglist($searchData)
    {
        $where = " where 1";
        $where .= $this->condition("channel_id",$searchData['channel_id']);
        $list = $this->BcdDb->query("select * from cp_channel_change_log {$where} order by change_time desc")->getAll();
        return array(
                        'list'=>$list
        );
    }
    
    
    /**
     * 根据类型返回开始日期
     * @param string $timeType
     */
    public function getStartDate($timeType)
    {
        $date = date('Y-m-d', strtotime('-7 day'));
        if($timeType == 'time2')
        {
            $date = date('Y-m-d', strtotime('-30 day'));
        }
        elseif($timeType == 'time3')
        {
            $date = date('Y-m-d', strtotime('-60 day'));
        }
        
        return $date;
    }
    
    /*
     * 获取渠道号
     * */
    public function getChannels()
    {
        return $this->BcdDb->query("select id, name from cp_channel where 1")->getAll();
    }
    
    public function getAppVersion()
    {
        return $this->BcdDb->query("select * from cp_app_version where 1")->getAll();
    }
    
    
    public function checkChannelName($name)
    {
        //查询是否渠道名重复
        $where = " where 1";
        $where .= $this->condition("name",$name);
        $queryRep = "select name from cp_channel {$where}";
        if($this->BcdDb->query($queryRep)->num_rows()>0)
        {
             return false;
        }
         return true;
    }
    public function checkIsRepeat($name,$id)
    {
        //查询是否渠道名重复
        $queryRep = "select name from cp_channel where id !='{$id}' and name='{$name}'";
        if($this->BcdDb->query($queryRep)->num_rows()>0)
        {
             return false;
        }
         return true;
    }    
    
    /*
     * 对插入数据库的字段进行转义
     * 
     */
    public function stripDatabase(&$arr)
    {
        foreach($arr as $key => $val)
        {
            $arr[$key] = $this->BcdDb->escape($val);
        }
    }

    
    /**
     * 参    数：$searchData 查询参数
     * 作    者：linw
     * 功    能：获取投注统计列表
     * 修改日期：2015.07.22
     */
    public function betting($searchData)
    {
        $where = " where 1";
        $webWhere = " where 1";
        $appWhere = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $webWhere .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $appWhere .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $where .= $this->condition("platform", $searchData['platform']);
        if($searchData['version'] != 'all' && $searchData['platform'] == '2')
        {
            $where .= $this->condition("version", $searchData['version']);
            $appWhere .= $this->condition("version", $searchData['version']);
        }
        if($searchData['platform'] == '2')
        {
            $uvSql = "SELECT channel_id channel,sum(history_active_num) uv FROM cp_50bang_app {$appWhere} GROUP BY channel_id";
        }
        else
        {
            $uvSql = "SELECT channel_id channel,sum(browse_uv) uv FROM cp_50bang_web {$webWhere} GROUP BY channel_id";
        }
        $list = $this->BcdDb->query("SELECT 
                                        c.id,
                                        c.name,
                                        a.betting_users,
                                        b.total,
                                        b.gaopin_total,
                                        b.manpin_total,
                                        b.jingcai_total,
                                        b.order_nums,
                                        b.award_total,
                                        u.uv
                                    FROM cp_channel c 
                                    LEFT JOIN (select channel, sum(total) as total, sum(if(lid in (21406), total, 0)) as gaopin_total, 
                                    sum(if(lid in (33, 35, 51, 52, 10022, 23528, 23529), total, 0)) as manpin_total, 
                                    sum(if(lid in (11, 19, 41, 42, 43), total, 0)) as jingcai_total, sum(order_nums) as order_nums, 
                                    sum(award_total) as award_total from cp_order_statistics {$where} group by channel) b 
                                    ON b.channel=c.id
                                    LEFT JOIN (select channel, sum(betting_users) as betting_users from cp_order_statistics_all {$where} group by channel) a 
                                    ON a.channel=c.id
                                    LEFT JOIN ({$uvSql}) u ON u.channel=c.id
                                    WHERE
                                        1
                                    AND c.platform = {$searchData['platform']}")->getAll();
        return array(
            'list' => $list,
            'totalUv' => $searchData['platform'] == 1 ? $this->BcdDb->query("select sum(browse_uv) from cp_50bang_web_all {$webWhere} limit 1")->getOne() : 0,
        );
    }
    
        /**
     * 参    数：$searchData 查询参数
     * 作    者：linw
     * 功    能：获取充值统计列表
     * 修改日期：2015.07.22
     */
    public function recharge($searchData)
    {
        $where = " where 1";
        $webWhere = " where 1";
        $appWhere = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $webWhere .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $appWhere .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $where .= $this->condition("platform", $searchData['platform']);
        if($searchData['version'] != 'all' && $searchData['platform'] == '2')
        {
            $where .= $this->condition("version", $searchData['version']);
            $appWhere .= $this->condition("version", $searchData['version']);
        }
        if($searchData['platform'] == '2')
        {
            $uvSql = "SELECT channel_id channel,sum(history_active_num) uv FROM cp_50bang_app {$appWhere} GROUP BY channel_id";
        }
        else
        {
            $uvSql = "SELECT channel_id channel,sum(browse_uv) uv FROM cp_50bang_web {$webWhere} GROUP BY channel_id";
        }
        $list = $this->BcdDb->query("SELECT
                                        c.id,
                                        c.name,
                                        r.users,
                                        r.total,
                                        r.recharge_nums,
                                        u.uv
                                    FROM
                                        cp_channel c LEFT JOIN (SELECT channel, sum(users) users, sum(total) total, sum(recharge_nums) recharge_nums FROM cp_recharge_statistics {$where} GROUP BY channel) r ON r.channel=c.id
                                        LEFT JOIN ({$uvSql}) u ON u.channel=c.id
                                    WHERE
                                        1
                                    AND c.platform = {$searchData['platform']}")->getAll();
        return array(
            'list' => $list,
            'totalUv' => $searchData['platform'] == 1 ? $this->BcdDb->query("select sum(browse_uv) from cp_50bang_web_all {$webWhere} limit 1")->getOne() : 0,
        );
    }

    /**
     * 参    数：$searchData 查询参数
     * 作    者：linw
     * 功    能：成本统计
     * 修改日期：2015.07.23
     */
    public function cost($searchData)
    {
        $where = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $where .= $this->condition("platform", $searchData['platform']);

        $validWhere = " where 1";
        $startDate = $this->getStartDate($searchData['timeType']);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $validWhere .= $this->condition("cdate", array(
                $startDate,
                $endDate
        ), "time");
        $validWhere .= $this->condition("platform", $searchData['platform']);
        $list = $this->BcdDb->query("SELECT
                                        a.id channel,
                                        a.name,
                                        b.cost,
                                        SUM(c.valid_user) valid_user,
                                        SUM(d.total) total,
                                        SUM(d.betting_users) betting_users,
                                        SUM(e.recharge_users) recharge_users
                                    FROM
                                        cp_channel a
                                    LEFT JOIN (SELECT channel,SUM(total) cost FROM cp_cost_statistics {$where} GROUP BY channel) b ON b.channel=a.id
                                    LEFT JOIN (SELECT channel_id channel,SUM(valid_user) valid_user FROM cp_register_stat {$validWhere} GROUP BY channel) c ON c.channel=a.id
                                    LEFT JOIN (SELECT channel,SUM(total) total,SUM(betting_users) betting_users FROM cp_order_statistics_all {$where} GROUP BY channel) d ON d.channel=a.id
                                    LEFT JOIN (SELECT channel,SUM(users) recharge_users FROM cp_recharge_statistics {$where} GROUP BY channel) e ON e.channel=a.id
                                    WHERE
                                        a.platform = {$searchData['platform']}
                                    GROUP BY a.id")->getAll();
        return array(
            'list' => $list,
        );
    }
    
    //刷新redis
    public function flushRedis()
    {
        //更新redis
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $lastSql = "SELECT id, name FROM cp_channel";
        //数据延迟
        $channels = $this->master->query($lastSql)->getAll();
        $validChannels = array();
        foreach ($channels as $ch)
        {
            $validChannels[$ch['id']] = $ch['name'];
        }
        return $this->cache->save($redis["validChannels"], serialize($validChannels), 0);
    }
    
    /**
     * web平台根据日期返回uv数量
     * @param unknown_type $timeType
     */
    public function getWebUvBydate($timeType)
    {
        $where = " where 1";
        $startDate = $this->getStartDate($timeType);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        return $this->BcdDb->query("select sum(browse_uv) browse_uv, sum(browse_pv) browse_pv,sum(click_pv) click_pv, sum(click_uv) click_uv from cp_50bang_web_all {$where} limit 1")->getRow();
    }
    /**
     * [getData 获取所有渠道数据]
     * @author LiKangJian 2017-04-28
     * @return [type] [description]
     */
    public function getData($searchData = array())
    {
        $where = " where 1";
        $where .= $this->condition("platform", $searchData['platform']);
        $where .= $this->condition("name", $searchData['channel'], 'like');
        $where .= $this->condition("id", $searchData['channelId']);
        $sql = "SELECT id,name,ret_ratio,settle_mode,created,modify_retRatio_time,modified FROM cp_channel {$where}";
        return $this->BcdDb->query($sql)->getAll();
    }
    /**
     * [getRuleData 获取规则数据]
     * @author Likangjian  2017-04-29
     * @return [type] [description]
     */
    public function getRuleData()
    {
        $sql = "SELECT c.*, r.id as rid,r.min_percent,r.max_percent,r.score from cp_channel_coeff as c LEFt JOIN cp_channel_coeff_rule as r on r.coeff_id = c.id";
        return $this->BcdDb->query($sql)->getAll();
    }
    /**
     * [upDateRule 更新规则]
     * @author Likangjian  2017-04-30
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function upDateRule($data)
    {
        $this->master->trans_start();
        //删除规则数据
        $tag = $this->master->empty_table('cp_channel_coeff_rule'); 
        //插入规则数据
        $tag1 = $this->master->insert_batch('cp_channel_coeff_rule', $data['rule']);
        //批量更新操作
        $tag2 = true;
        foreach ($data['coeff'] as $k => $v) {
            $id = $v['id'];
            unset($data['coeff'][$k]['id']);
            $flag = $this->db->update('cp_channel_coeff', $data['coeff'][$k], array('id'=>$id));
            if(!$flag) return $tag2 = false;
        }
        if($tag && $tag1 && $tag2)
        {
            $this->master->trans_complete();
            return true;
        }else{
            $this->master->trans_rollback();
            return false;
        }
        
    }
    /**
     * [updateChannelPwd 更新密码]
     * @author Likangjian  2017-04-30
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateChannelPwd($data)
    {
        //更新
        do{
          $str = rand_str(16);
         }while (strlen($str)!=16);
        $tag = $this->master->query('UPDATE cp_channel SET password="'.md5($str).'", modified = now() WHERE id="'.$data['channel_id'].'"');
        if($tag === false) return false;
        return $str;
    }

    /**
     * [getCountData 获取统计数据]
     * @author Likangjian  2017-04-30
     * @param  [type] $searchData [description]
     * @param  [type] $page       [description]
     * @param  [type] $pageCount  [description]
     * @return [type]             [description]
     */
    public function getCountData($searchData, $page, $pageCount)
    {
        $where = " where 1 ";
        if(!empty($searchData['platform']))
        {
            $where .=" and c.platform ='{$searchData['platform']}' ";
        }
        if($searchData['settle_mode']!='')
        {
            $where .=" and c.settle_mode = '{$searchData['settle_mode']}' ";
        }        
        if(!empty($searchData['channel']))
        {
            $where .=" and l.name like '%{$searchData['channel']}%' ";   
        } 
        $where .= $this->condition("c.date", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "date");
        $select = "SELECT c.*,l.name FROM cp_channel_count as c LEFT JOIN cp_channel as l ON l.id = c.channel_id {$where} ORDER BY c.date DESC,c.channel_id ASC
        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $totalSql = "SELECT count(*) as rows, 
        sum(c.add_active) as add_actives,
        sum(c.reg_num) as reg_nums, 
        sum(c.real_num) as real_nums,
        sum(c.total_amount) as total_amounts,
        sum(c.balance_amount) as balance_amounts,
        sum(c.actual_division) as actual_divisions, 
        sum(c.balance_yj) as balance_yjs
        FROM cp_channel_count as c LEFT JOIN cp_channel as l ON l.id = c.channel_id {$where}";
        $count = $this->BcdDb->query($totalSql)->getRow();
        $result = $this->BcdDb->query($select)->getAll();
        return array(
            $result,
            $count['rows'],
            $count['add_actives'],
            $count['reg_nums'],
            $count['real_nums'],
            $count['total_amounts'],
            $count['balance_amounts'],
            $count['actual_divisions'],
            $count['balance_yjs'],
        );
    }
    /**
     * [getExportCountData 导出数据源]
     * @author LiKangJian 2017-05-02
     * @param  [type] $searchData [description]
     * @return [type]             [description]
     */
    public function getExportCountData($searchData)
    {
        $where = " where 1 ";
        if(!empty($searchData['platform']))
        {
            $where .=" and c.platform ='{$searchData['platform']}' ";
        }
        if($searchData['settle_mode']!='')
        {
            $where .=" and c.settle_mode = '{$searchData['settle_mode']}' ";
        }        
        if(!empty($searchData['channel']))
        {
            $where .=" and l.name like '%{$searchData['channel']}%' ";   
        } 
        $where .= $this->condition("c.date", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "date");
        $select = "SELECT c.*,l.name FROM cp_channel_count as c LEFT JOIN cp_channel as l ON l.id = c.channel_id {$where} ORDER BY c.date DESC,c.channel_id ASC";
        $totalSql = "SELECT count(*) as rows, 
        -- sum(c.add_active) as add_actives,
        -- sum(c.reg_num) as reg_nums,
        -- sum(c.real_num) as real_nums ,
        -- sum(c.balance_active) as jsjh ,
        -- sum(c.lottery_total_amount) as lottery_total_amounts,
        -- sum(c.lottery_total_amount*c.unit_price/100) as count_yj
        sum(c.add_active) as add_actives,
        sum(c.reg_num) as reg_nums, 
        sum(c.real_num) as real_nums,
        sum(c.total_amount) as total_amounts,
        sum(c.balance_amount) as balance_amounts,
        sum(c.actual_division) as actual_divisions, 
        sum(c.balance_yj) as balance_yjs
        FROM cp_channel_count as c LEFT JOIN cp_channel as l ON l.id = c.channel_id {$where}";
        $count = $this->BcdDb->query($totalSql)->getRow();
        $result = $this->BcdDb->query($select)->getAll();
        return array(
            'res' => $result,
            'count' => array( 
                'rows' => $count['rows'],
                'add_actives' => $count['add_actives'],
                'reg_nums' => $count['reg_nums'],
                'real_nums' => $count['real_nums'],
                'total_amounts' => $count['total_amounts'],
                'balance_amounts' => $count['balance_amounts'],
                'actual_divisions' => $count['actual_divisions'],
                'balance_yjs' => $count['balance_yjs'],
            )
        );
    }
    /**
     * 查询佣金金额
     */
    public function getExportBalanceData($searchData)
    {
        $where = " where 1 ";
        if(!empty($searchData['platform']))
        {
            $where .=" and a.platform ='{$searchData['platform']}' ";
        }
        if(!empty($searchData['settle_mode']))
        {
            $where .=" and a.settle_mode = '{$searchData['settle_mode']}' ";
        }        
        if(!empty($searchData['channel']))
        {
            $where .=" and b.name like '%{$searchData['channel']}%' ";   
        } 
        $where .= $this->condition("a.date", array(
                $searchData['start_time'],
                $searchData['end_time']
        ), "date");
        $select = "select sum(balance_yj) as balance_yj from cp_channel_count a left join cp_channel b on a.channel_id = b.id {$where}";
        return $this->BcdDb->query($select, $searchData)->getRow();
    }
    /**
     * [updateRetRatio 更新留存扣减比例]
     * @author LiKangJian 2017-05-05
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function updateRetRatio($data)
    {
        $tag = false;
        $sql = "UPDATE cp_channel SET ret_ratio = ? ,modify_retRatio_time = ? WHERE id = ? ";
        $res = $this->master->query($sql,array($data['ret_ratio'],$data['modify_retRatio_time'],$data['id']));
        if($res)
        {
            $tag = true;
        }

        return $tag;
    }
    /**
     * [getChannelById description]
     * @author LiKangJian 2017-05-05
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getChannelByIds($ids)
    {
        $sql = "SELECT * FROM cp_channel where id in (".implode(',', $ids).")";
        $data = $this->BcdDb->query($sql)->getAll();
        return $data;
    }

    // 更新渠道停开售
    public function updateChnnelSale($id, $cstate)
    {
        if($cstate)
        {
            // 开售
            $sql = "UPDATE cp_channel SET cstate = (cstate ^ 1) WHERE id = ? AND (cstate & 1) = 1";
        }
        else
        {
            // 停售
            $sql = "UPDATE cp_channel SET cstate = (cstate | 1) WHERE id = ?";
        }
        $this->master->query($sql, array($id));
        $row = $this->master->affected_rows();
        // 刷新缓存
        $this->refreshLimitChannel();
        return $row;
    }

    // 刷新停售渠道缓存 平台间渠道号不会重复
    public function refreshLimitChannel($db = 'master')
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');

        $sql = "SELECT id FROM cp_channel WHERE (cstate & 1) = 1";
        $info = $this->{$db}->query($sql)->getCol();
        if(!empty($info))
        {
            $this->cache->save($redis["LIMIT_CHANNEL"], json_encode($info), 0);
        }
        else
        {
            $this->cache->save($redis["LIMIT_CHANNEL"], json_encode(array()), 0);
        }
    }

    // 获取主包名
    public function getPackages($platform)
    {
        $sql = "SELECT id, name, platform FROM cp_channel_package WHERE platform = ?";
        return $this->master->query($sql, array($platform))->getAll();
    }

    public function addPackage($info)
    {
        $fields = array_keys($info);
        $sql = "insert cp_channel_package(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
        $this->master->query($sql, $info);
    }

    public function updatePackage($info)
    {
        $sql = "UPDATE cp_channel SET package = ? WHERE id = ?";
        return $this->master->query($sql, array($info['package'], $info['id']));
    }

    public function updateStatus($info)
    {
        $sql = "UPDATE cp_channel SET status = ? WHERE id = ?";
        return $this->master->query($sql, array($info['status'], $info['id']));
    }

    public function getPackageUnique($info)
    {
        $sql = "SELECT id FROM cp_channel_package WHERE name = ? AND platform = ?";
        return $this->master->query($sql, array($info['name'], $info['platform']))->getRow();
    }

    public function getChannelInfo($table, $platform)
    {
        $sql = "SELECT id, channels FROM {$table} WHERE platform = ?";
        return $this->master->query($sql, array($platform))->getAll();
    }

    public function recodeChannels($table, $fields, $bdata)
    {
        $sql = "INSERT {$table}(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . " on duplicate key update channels = values(channels)";
        $this->master->query($sql, $bdata['d_data']);
    }

    /*
     * 获取渠道账号数据，列表呈现
     */
    public function getUserChannels($searchData, $page, $pageCount)
    {
        $where = " where 1";
        $where .= $this->condition("c.status", $searchData['accountStatus']);
        $where .= $this->condition("c.uname", $searchData['account']);
        $where .= $this->condition("c.id", $searchData['id']);
        $list = $this->BcdDb->query("SELECT c.id, c.uname, c.created, c.last_login_time, c.status, c.mark, c.fields, c.channels, c.password FROM cp_channel_user AS c {$where} ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount)->getAll();
        $count = $this->BcdDb->query("SELECT count(*) as rows FROM cp_channel_user AS c {$where}")->getRow();
        return array(
            'rows' => $count['rows'],
            'list' => $list
        );
    }

    /**
     * 获取一条渠道账号数据
     */
    public function getUserChannel($searchData)
    {
        $where = " where 1";
        $where .= $this->condition("c.status", $searchData['accountStatus']);
        $where .= $this->condition("c.uname", $searchData['account']);
        $where .= $this->condition("c.id", $searchData['id']);
        $list = $this->BcdDb->query("SELECT c.id, c.uname, c.created, c.last_login_time, c.status, c.mark, c.fields, c.channels, c.password FROM cp_channel_user AS c {$where}")->getRow();
        return $list;
    }
    /*
     * 更新渠道用户表的用户状态
     */
    public function updateAccountStatus($info)
    {
        $sql = "UPDATE cp_channel_user SET status = ? WHERE id = ?";
        return $this->master->query($sql, array($info['status'], $info['id']));
    }

    /*
     * 获取渠道ID和名称(之前的代码有实现)
     */
    public function getChannelSelectData()
    {
        $sql = "SELECT id, name from cp_channel";
        $list = $this->BcdDb->query($sql)->getAll();
        return $list;
    }

    /*
     * 插入新增渠道用户
     */
    public function insertNewChannelUser($channelUser)
    {
        $keys = array_keys($channelUser);
        $values = array_values($channelUser);
        foreach ($values as $k => $v) {
            $values[$k] = addslashes($v);
        }
        $sql = 'insert into cp_channel_user ('.implode(', ', $keys).') values ("'.implode('", "', $values).'") on duplicate key update channels = values(channels), fields = values(fields), mark = values(mark)';
        $this->master->query($sql);
    }

    /**
     * 验证渠道账户是否已经存在
     */
    public function channelUserExists($user)
    { 
        $sql = 'select id from cp_channel_user where uname = ?';
        $res = $this->BcdDb->query($sql, array($user))->getRow();
        return $res ? TRUE : FALSE;
    }

    /**
     * 更新密码
     */
    public function updateChannelUserPwd($data, $str)
    {
        $tag = $this->master->query('UPDATE cp_channel_user SET password="'.md5($str).'" WHERE id="'.$data['user_id'].'"');
        if($tag === false) return false;
        return $str;
    }
}