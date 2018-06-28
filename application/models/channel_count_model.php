<?php
date_default_timezone_set('Asia/Shanghai');
/**
 * Copyright (c) 2015,上海快猫文化.
 * 摘    要: 渠道Model
 * 作    者: 李康建
 * 修改日期: 2017/05/08
 * 修改时间: 00:21
 */

class Channel_Count_Model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
     
    /**
     * 统计渠道数据
     * @param string $start 统计开始时间
     * @param string $end   统计结束时间
     * @param number $real  实时标识  0 实时  1 按天统计
     */
    public function channelCount($start, $end, $real = 0)
    {
        $userReg = $this->getUserRegByDate($start, $end);
        $userReal = $this->getUserRealByDate($start, $end);
        $userLottery = $this->getUserLotteryByDate($start, $end);
        $userChannel = $this->getUserChannels($start);
        $channelStart = 0;
        $sql = "select id, name, platform, settle_mode, unit_price, share_ratio, reg_time,
        ret_ratio from cp_channel where id > ? order by id asc limit 500";
        $channels = $this->slave->query($sql, array($channelStart))->getAll();
        while ($channels) {
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            foreach ($channels as $channel) {
                array_push($bdata['s_data'], "('{$start}', ? , ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $channel['id']);
                array_push($bdata['d_data'], $channel['platform']);
                $unit_price = $channel['settle_mode'] == 1 ? $channel['unit_price'] : $channel['share_ratio'];
                array_push($bdata['d_data'], $unit_price);
                $reg_num = isset($userReg[$channel['id']]) ? $userReg[$channel['id']]['reg_num'] : 0;   //真实注册
                $real_num = isset($userReal[$channel['id']]) ? $userReal[$channel['id']]['real_num'] : 0; //真实实名
                $balance_reg = floor($reg_num * $channel['ret_ratio']); //结算注册
                $balance_real = floor($real_num * $channel['ret_ratio']);   //结算实名
                $per_real = $reg_num > 0 ? round(($real_num / $reg_num) * 100, 2) : 0;  //实名率
                $active_lottery_num = isset($userLottery[$channel['id']]) ? $userLottery[$channel['id']]['active_lottery_num'] : 0; //新用户购彩人数
                $partner_active_lottery_num = floor($active_lottery_num * $channel['ret_ratio']); //合作商新用户购彩人数
                $curr_lottery_total_amount = isset($userLottery[$channel['id']]) ? $userLottery[$channel['id']]['curr_lottery_total_amount'] : 0; //新用户购彩总额
                $partner_curr_lottery_total_amount = floor($curr_lottery_total_amount / 100 * $channel['ret_ratio']) * 100; //合作商新用户购彩总额
                $per_curr_lottery = $reg_num > 0 ? round(($active_lottery_num / $reg_num) * 100, 2) : 0;    //新用户购彩率
                $total_amount = isset($userLottery[$channel['id']]) ? $userLottery[$channel['id']]['total_amount'] : 0; //真实渠道购彩(不包含时限)
                $lottery_total_amount = isset($userLottery[$channel['id']]) ? $userLottery[$channel['id']]['lottery_total_amount'] : 0; //真实渠道购彩(包含时限)
                $balance_amount = floor($lottery_total_amount / 100 * $channel['ret_ratio']) * 100; //结算渠道购彩
                $lottery_num = isset($userLottery[$channel['id']]) ? $userLottery[$channel['id']]['lottery_num'] : 0; //渠道购彩总人数
                $partner_lottery_num = floor($lottery_num * $channel['ret_ratio']); //合作商渠道购彩总人数
                $avg_curr_lottery_amount = $active_lottery_num > 0 ? round($curr_lottery_total_amount / $active_lottery_num) : 0; //日均新用户购彩
                $add_active = isset($userChannel[$channel['name']]) ? $userChannel[$channel['name']]['install'] : 0; //真实新增
                $active_num = isset($userChannel[$channel['name']]) ? $userChannel[$channel['name']]['active_user'] : 0; //渠道活跃
                $per_reg = $add_active > 0 ? round(($reg_num / $add_active) * 100, 2) : 0; //注册率
                if($channel['settle_mode'] == '1') {
                    //CPA
                    $actual_division = $channel['unit_price'] * $add_active;    //真实分成
                    $balance_yj = 0; //结算分成  CPA需要隔2日计算
                } else {
                    // CPS
                    $actual_division = round($channel['share_ratio'] / 100 * $lottery_total_amount); //真实分成
                    $balance_yj = round($actual_division * $channel['ret_ratio']); //结算分成
                } 
                
                $balance_real_amount = $real_num > 0 ? round($actual_division / $real_num) : 0; //实名成本
                $balance_reg_amount = $reg_num > 0 ? round($actual_division / $reg_num) : 0;    //注册成本
                $balance_lottery_amount = $active_lottery_num > 0 ? round($actual_division / $active_lottery_num) : 0; // 真实新增购彩成本
                $balance_lottery_money = $active_lottery_num > 0 ? round($balance_yj / $active_lottery_num) : 0; //结算新增购彩成本
                $curr_lottery_divided_active = $add_active > 0 ? round(($active_lottery_num / $add_active) * 100, 2) : 0; //新用户购彩人数/激活
                array_push($bdata['d_data'], $reg_num);
                array_push($bdata['d_data'], $real_num);
                array_push($bdata['d_data'], $balance_reg);
                array_push($bdata['d_data'], $balance_real);
                array_push($bdata['d_data'], $per_real);
                array_push($bdata['d_data'], $active_lottery_num);
                array_push($bdata['d_data'], $partner_active_lottery_num);
                array_push($bdata['d_data'], $curr_lottery_total_amount);
                array_push($bdata['d_data'], $partner_curr_lottery_total_amount);
                array_push($bdata['d_data'], $per_curr_lottery);
                array_push($bdata['d_data'], $total_amount);
                array_push($bdata['d_data'], $lottery_total_amount);
                array_push($bdata['d_data'], $balance_amount);
                array_push($bdata['d_data'], $lottery_num);
                array_push($bdata['d_data'], $partner_lottery_num);
                array_push($bdata['d_data'], $avg_curr_lottery_amount);
                array_push($bdata['d_data'], $add_active);
                array_push($bdata['d_data'], $active_num);
                array_push($bdata['d_data'], $per_reg);
                array_push($bdata['d_data'], $actual_division);
                array_push($bdata['d_data'], $balance_real_amount);
                array_push($bdata['d_data'], $balance_reg_amount);
                array_push($bdata['d_data'], $balance_yj);
                array_push($bdata['d_data'], $balance_lottery_amount);
                array_push($bdata['d_data'], $balance_lottery_money);
                array_push($bdata['d_data'], $curr_lottery_divided_active);
                array_push($bdata['d_data'], $channel['reg_time']);
                array_push($bdata['d_data'], $channel['ret_ratio']);    //扣减比例
                array_push($bdata['d_data'], $channel['settle_mode']);
                $cpstate = $real == 1 ? 1 : 0;
                array_push($bdata['d_data'], $cpstate);
                
                $channelStart = $channel['id'];
            }
            
            $fields = array('date', 'channel_id', 'platform', 'unit_price', 'reg_num', 'real_num', 
                'balance_reg', 'balance_real', 'per_real', 'active_lottery_num', 'partner_active_lottery_num', 'curr_lottery_total_amount', 
                'partner_curr_lottery_total_amount', 'per_curr_lottery', 'total_amount', 'lottery_total_amount', 'balance_amount', 
                'lottery_num', 'partner_lottery_num', 'avg_curr_lottery_amount', 'add_active', 'active_num', 'per_reg', 'actual_division', 
                'balance_real_amount', 'balance_reg_amount', 'balance_yj', 'balance_lottery_amount', 
                'balance_lottery_money', 'curr_lottery_divided_active', 'reg_time', 'ret_ratio', 'settle_mode', 'cpstate', 'created');
            $sql1  = "insert cp_channel_count(" . implode(',', $fields) . ") values" . implode(',', $bdata['s_data']);
            $sql1 .= $this->onduplicate($fields, array('unit_price', 'reg_num', 'real_num', 'balance_reg', 'balance_real', 
                'per_real', 'active_lottery_num', 'partner_active_lottery_num', 'curr_lottery_total_amount', 'partner_curr_lottery_total_amount', 'per_curr_lottery', 'total_amount', 
                'lottery_total_amount', 'balance_amount', 'lottery_num', 'partner_lottery_num', 'avg_curr_lottery_amount', 'add_active', 
                'active_num', 'per_reg', 'actual_division', 'balance_real_amount', 'balance_reg_amount', 'balance_yj', 
                'balance_lottery_amount', 'balance_lottery_money', 'curr_lottery_divided_active', 'reg_time', 'ret_ratio', 'settle_mode', 'cpstate'));
            $this->db->query($sql1, $bdata['d_data']);
            
            $channels = $this->slave->query($sql, array($channelStart))->getAll();
        }
    }
    
    /**
     * 统计核减系数得分相关字段
     * @param unknown $date
     */
    public function delayedCount($date)
    {
        $userNextLotteryNum = $this->getUserNextLotteryNums($date);
        $channelCoeff = $this->getChannelCoeff();
        $channelRule = $this->getChannelCoeffRule();
        $channelStart = 0;
        $sql = "select channel_id, add_active, reg_num, balance_yj, balance_lottery_money, 
        curr_lottery_divided_active, settle_mode, avg_curr_lottery_amount, actual_division, ret_ratio,
        active_lottery_num
        from cp_channel_count where date = ? and channel_id > ? order by id asc limit 500";
        $channels = $this->slave->query($sql, array($date, $channelStart))->getAll();
        while ($channels) {
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            foreach ($channels as $channel) {
                array_push($bdata['s_data'], "('{$date}', ? , ?, ?, ?, ?, ?, ?, 2)");
                array_push($bdata['d_data'], $channel['channel_id']);
                //当日注册在次日留存
                $next_lottery_num = isset($userNextLotteryNum[$channel['channel_id']]) ? $userNextLotteryNum[$channel['channel_id']] : 0;
                //次日购彩留存
                $per_next_lottery_num = $channel['reg_num'] > 0 ? round(($next_lottery_num / $channel['reg_num']) * 100, 2) : 0;
                //核减系数得分
                $redu_coeff_score = $this->getUserCoeffScore($channel['curr_lottery_divided_active'], $channel['avg_curr_lottery_amount'], $per_next_lottery_num, $channelCoeff, $channelRule);
                //结算新增
                $balance_active = floor($channel['add_active'] * $redu_coeff_score * $channel['ret_ratio']);
                if($channel['settle_mode'] == '1') {
                    //CPA
                    //结算分成
                    $balance_yj = round($channel['actual_division'] * $channel['ret_ratio'] * $redu_coeff_score);
                    $balance_lottery_money = $channel['active_lottery_num'] > 0 ? round($balance_yj / $channel['active_lottery_num']) : 0;
                } else {
                    $balance_yj = $channel['balance_yj'];
                    $balance_lottery_money = $channel['balance_lottery_money'];
                }
                array_push($bdata['d_data'], $balance_active);
                array_push($bdata['d_data'], $balance_yj);
                array_push($bdata['d_data'], $balance_lottery_money);
                array_push($bdata['d_data'], $redu_coeff_score);
                array_push($bdata['d_data'], $per_next_lottery_num);
                array_push($bdata['d_data'], $next_lottery_num);
                
                $channelStart = $channel['channel_id'];
            }
            
            $fields = array('date', 'channel_id', 'balance_active', 'balance_yj', 'balance_lottery_money',
                'redu_coeff_score', 'per_next_lottery_num', 'next_lottery_num', 'cpstate'
            );
            $sql1  = "insert cp_channel_count(" . implode(',', $fields) . ") values" . implode(',', $bdata['s_data']);
            $sql1 .= $this->onduplicate($fields, array('balance_active', 'balance_yj', 'balance_lottery_money', 'redu_coeff_score', 'per_next_lottery_num', 'next_lottery_num', 'cpstate'));
            $this->db->query($sql1, $bdata['d_data']);
            
            $channels = $this->slave->query($sql, array($date, $channelStart))->getAll();
        }
    }
    
    /**
     * 计算渠道和减系数得分
     * @param int $curr_lottery_divided_active  激活且购彩/激活
     * @param int $avg_curr_lottery_amount      日均新用户购彩
     * @param int $per_next_lottery_num         次日购彩留存
     * @param array $channelCoeff               渠道系数占比数组
     * @param array $channelRule                渠道系数规则数组
     * @return number
     */
    private function getUserCoeffScore($curr_lottery_divided_active, $avg_curr_lottery_amount, $per_next_lottery_num, &$channelCoeff, &$channelRule)
    {
        $aScore = 0;
        foreach ($channelRule[1] as $val) {
            $val['max_percent'] = $val['max_percent'] == '*' ? 99999999999999999 : $val['max_percent'];
            if($curr_lottery_divided_active > $val['min_percent'] && $curr_lottery_divided_active <= $val['max_percent']) {
                $aScore = $val['score'];
                break;
            }
        }
        
        $aScore *= $channelCoeff[1];
        
        $bScore = 0;
        $avg_curr_lottery_amount = $avg_curr_lottery_amount / 100;
        foreach ($channelRule[2] as $val) {
            $val['max_percent'] = $val['max_percent'] == '*' ? 99999999999999999 : $val['max_percent'];
            
            if($avg_curr_lottery_amount > $val['min_percent'] && $avg_curr_lottery_amount <= $val['max_percent']) {
                $bScore = $val['score'];
                break;
            }
        }
        
        $bScore *= $channelCoeff[2];
        
        $cScore = 0;
        foreach ($channelRule[3] as $val) {
            $val['max_percent'] = $val['max_percent'] == '*' ? 99999999999999999 : $val['max_percent'];
            if($per_next_lottery_num > $val['min_percent'] && $per_next_lottery_num <= $val['max_percent']) {
                $cScore = $val['score'];
                break;
            }
        }
        
        $cScore *= $channelCoeff[3];
        
        return round(($aScore + $bScore + $cScore) / 10, 2);
    }
    
    /**
     * 返回渠道次日留存用户数量
     * @param unknown $date
     * @return unknown[]
     */
    private function getUserNextLotteryNums($date)
    {
        $data = array();
        $nextDate = date('Y-m-d', strtotime($date) + 86400);
        $endDate = date('Y-m-d', strtotime($date) + 172800);
        $sql = "SELECT count(distinct o.uid ) as count ,u.channel
        FROM cp_orders as o 
        LEFT JOIN cp_user as u on u.uid = o.uid 
        where  o.created >= '{$nextDate}' and o.created<'{$endDate}' and o.status in (500,510,1000,2000)
        and u.created>= '{$date}' and u.created<'{$nextDate}'
        GROUP BY u.channel";
        $res = $this->slave->query($sql)->getAll();
        foreach ($res as $val) {
            $data[$val['channel']] = $val['count'];
        }
        
        return $data;
    }
    
    /**
     * 返回评分系数
     * @return unknown[]
     */
    private function getChannelCoeff()
    {
        $data = array();
        $sql = "select id, percent from cp_channel_coeff where 1";
        $res = $this->slave->query($sql)->getAll();
        foreach ($res as $val) {
            $data[$val['id']] = $val['percent'];
        }
        
        return $data;
    }
    
    /**
     * 返回评分系数区间规则
     * @return array|unknown
     */
    private function getChannelCoeffRule()
    {
        $data = array();
        $sql = "select coeff_id, min_percent, max_percent, score from cp_channel_coeff_rule where 1";
        $res = $this->slave->query($sql)->getAll();
        foreach ($res as $val) {
            $data[$val['coeff_id']][] = $val;
        }
        
        return $data;
    }
    
    /**
     * 查询指定时间内的注册人数
     * @param string $start 开始时间
     * @param string $end   结束时间
     */
    private function getUserRegByDate($start, $end)
    {
        $data = array();
        $sql = "SELECT u.channel, COUNT(*) as reg_num FROM cp_user as u INNER JOIN cp_user_info as i ON i.uid = u.uid 
        WHERE u.created >= ? AND u.created <= ? and i.userStatus = 0 GROUP BY u.channel";
        $res = $this->slave->query($sql, array($start, $end))->getAll();
        foreach ($res as $val) {
            $data[$val['channel']] = $val;
        }
        
        return $data;
    }
    
    /**
     * 查询指定时间内的实名人数
     * @param string $start 开始时间
     * @param string $end   结束时间
     */
    private function getUserRealByDate($start, $end)
    {
        $data = array();
        $sql = "SELECT u.channel, COUNT(*) as real_num FROM cp_user as u INNER JOIN cp_user_info as i ON i.uid = u.uid
        WHERE i.bind_id_card_time >= ? AND i.bind_id_card_time <= ? and i.userStatus = 0 GROUP BY u.channel";
        $res = $this->slave->query($sql, array($start, $end))->getAll();
        foreach ($res as $val) {
            $data[$val['channel']] = $val;
        }
        
        return $data;
    }
    
    /**
     * 查询指定时间内的购彩数据
     * @param unknown $start
     * @param unknown $end
     * @return unknown[]
     */
    private function getUserLotteryByDate($start, $end)
    {
        $data = array();
        $sql = "select u.channel, 
                sum(if(
                    (c.settle_mode=1 || CAST(c.reg_time AS signed) = 0), o.money - o.failMoney, 
                  if(
                    u.created >= date_sub('{$start}', interval cast(c.reg_time AS signed) day) && 
                    u.created <= '{$end}', o.money - o.failMoney, 0)
                )) lottery_total_amount,
                sum(o.money - o.failMoney) total_amount,
                count(distinct(o.uid)) lottery_num, 
                count(distinct(if(u.created >= '{$start}' && u.created <= '{$end}', o.uid, null))) active_lottery_num,
                sum(if(u.created >= '{$start}' && u.created <= '{$end}', o.money - o.failMoney, 0)) curr_lottery_total_amount
                from cp_orders o force INDEX(created)
                left join cp_user u on o.uid = u.uid
                left join bn_cpiao.cp_channel c on u.channel = c.id
                where o.created >= '{$start}' and o.created <= '{$end}'
                and o.status in(500, 510, 1000, 2000)
                group by u.channel";
        $res = $this->slave->query($sql)->getAll();
        foreach ($res as $val) {
            $data[$val['channel']] = $val;
        }
        
        return $data;
    }
    
    /**
     * 返回指定日期渠道的新增与活跃数
     * @param unknown $date
     * @return unknown[][]|[type][][]
     */
    public function getUserChannels($date)
    {
        $data = array();
        $url = 'http://api.umeng.com/apps?per_page=50&page=1';
        $list = $this->curl_head_get($url) ;
        if($list) {
            foreach ($list as $app) {
                $channelUrl = 'http://api.umeng.com/channels?appkey='.$app['appkey'].'&per_page=10000&date=' . $date;
                $channels = $this->curl_head_get($channelUrl);
                if(is_array($channels)) {
                    foreach ($channels as $channel) {
                        $data[$channel['channel']] = array('install' => $channel['install'], 'active_user' => $channel['active_user']);
                    }
                }
            }
        }
        
        return $data;
    }
    
    /**
     * [curl_head_get 友盟接口入口]
     * @author LiKangJian 2017-05-03
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    private function curl_head_get($url)
    {
        $header = array();
        $header[] = "Authorization: Basic ".base64_encode('caipiao@2345.com:166caipiao');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL,$url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response,true);
    }
    
    /**
     * 渠道老数据处理
     */
    public function historyChannelCount()
    {
        $idStart = 0;
        $sql = "select * from cp_channel_count where id > ? order by id asc limit 500";
        $channels = $this->slave->query($sql, array($idStart))->getAll();
        while ($channels) {
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            foreach ($channels as $channel) {
                $channel['type'] = $channel['type'] + 1;
                array_push($bdata['s_data'], "('{$channel['date']}', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $channel['channel_id']);
                array_push($bdata['d_data'], $channel['platform']);
                $unit_price = $channel['type'] == 1 ? $channel['unit_price'] : $channel['share_ratio'];
                array_push($bdata['d_data'], $unit_price);
                $reg_num = $channel['reg_num'];   //真实注册
                $real_num = $channel['real_num']; //真实实名
                $balance_reg = floor($reg_num * $channel['ret_ratio']); //结算注册
                $balance_real = floor($real_num * $channel['ret_ratio']);   //结算实名
                $per_real = $reg_num > 0 ? round(($real_num / $reg_num) * 100, 2) : 0;  //实名率
                $active_lottery_num = $channel['active_lottery_num']; //新用户购彩人数
                $partner_active_lottery_num = floor($active_lottery_num * $channel['ret_ratio']); //合作商新用户购彩人数
                $curr_lottery_total_amount = $channel['curr_lottery_total_amount']; //新用户购彩总额
                $partner_curr_lottery_total_amount = floor($curr_lottery_total_amount / 100 * $channel['ret_ratio']) * 100; //合作商新用户购彩总额
                $per_curr_lottery = $reg_num > 0 ? round(($active_lottery_num / $reg_num) * 100, 2) : 0;    //新用户购彩率
                $total_amount = 0; //真实渠道购彩(不包含时限)
                $lottery_total_amount = $channel['lottery_total_amount']; //真实渠道购彩(包含时限)
                $balance_amount = floor($lottery_total_amount / 100 * $channel['ret_ratio']) * 100; //结算渠道购彩
                $lottery_num = $channel['lottery_num']; //渠道购彩总人数
                $partner_lottery_num = floor($lottery_num * $channel['ret_ratio']); //合作商渠道购彩总人数
                $avg_curr_lottery_amount = $active_lottery_num > 0 ? round($curr_lottery_total_amount / $active_lottery_num) : 0; //日均新用户购彩
                $add_active = $channel['add_active']; //真实新增
                $active_num = $channel['active_num']; //渠道活跃
                $per_reg = $add_active > 0 ? round(($reg_num / $add_active) * 100, 2) : 0; //注册率
                if($channel['type'] == '1') {
                    //CPA
                    $actual_division = $channel['unit_price'] * $add_active;    //真实分成
                    $balance_yj = $channel['balance_yj'];
                } else {
                    // CPS
                    $actual_division = round($channel['share_ratio'] / 100 * $lottery_total_amount); //真实分成
                    $balance_yj = $channel['balance_yj']; //结算分成
                }
                
                $balance_real_amount = $real_num > 0 ? round($actual_division / $real_num) : 0; //实名成本
                $balance_reg_amount = $reg_num > 0 ? round($actual_division / $reg_num) : 0;    //注册成本
                $balance_lottery_amount = $active_lottery_num > 0 ? round($actual_division / $active_lottery_num) : 0; // 真实新增购彩成本
                $balance_lottery_money = $active_lottery_num > 0 ? round($balance_yj / $active_lottery_num) : 0; //结算新增购彩成本
                $curr_lottery_divided_active = $add_active > 0 ? round(($active_lottery_num / $add_active) * 100, 2) : 0; //新用户购彩人数/激活
                //当日注册在次日留存
                $next_lottery_num = $channel['next_lottery_num'];
                //次日购彩留存
                $per_next_lottery_num = $reg_num > 0 ? round(($next_lottery_num / $reg_num) * 100, 2) : 0;
                //核减系数得分
                $redu_coeff_score = $channel['redu_coeff_score'];
                $balance_active = floor($add_active * $redu_coeff_score * $channel['ret_ratio']);
                array_push($bdata['d_data'], $reg_num);
                array_push($bdata['d_data'], $real_num);
                array_push($bdata['d_data'], $balance_reg);
                array_push($bdata['d_data'], $balance_real);
                array_push($bdata['d_data'], $per_real);
                array_push($bdata['d_data'], $active_lottery_num);
                array_push($bdata['d_data'], $partner_active_lottery_num);
                array_push($bdata['d_data'], $curr_lottery_total_amount);
                array_push($bdata['d_data'], $partner_curr_lottery_total_amount);
                array_push($bdata['d_data'], $per_curr_lottery);
                array_push($bdata['d_data'], $total_amount);
                array_push($bdata['d_data'], $lottery_total_amount);
                array_push($bdata['d_data'], $balance_amount);
                array_push($bdata['d_data'], $lottery_num);
                array_push($bdata['d_data'], $partner_lottery_num);
                array_push($bdata['d_data'], $avg_curr_lottery_amount);
                array_push($bdata['d_data'], $add_active);
                array_push($bdata['d_data'], $active_num);
                array_push($bdata['d_data'], $per_reg);
                array_push($bdata['d_data'], $actual_division);
                array_push($bdata['d_data'], $balance_real_amount);
                array_push($bdata['d_data'], $balance_reg_amount);
                array_push($bdata['d_data'], $balance_yj);
                array_push($bdata['d_data'], $balance_lottery_amount);
                array_push($bdata['d_data'], $balance_lottery_money);
                array_push($bdata['d_data'], $curr_lottery_divided_active);
                array_push($bdata['d_data'], $channel['reg_time']);
                array_push($bdata['d_data'], $channel['ret_ratio']);    //扣减比例
                array_push($bdata['d_data'], $channel['type']);
                array_push($bdata['d_data'], $next_lottery_num);
                array_push($bdata['d_data'], $per_next_lottery_num);
                array_push($bdata['d_data'], $redu_coeff_score);
                array_push($bdata['d_data'], $balance_active);
                array_push($bdata['d_data'], 2);
                
                $idStart = $channel['id'];
            }
            
            $fields = array('date', 'channel_id', 'platform', 'unit_price', 'reg_num', 'real_num',
                'balance_reg', 'balance_real', 'per_real', 'active_lottery_num', 'partner_active_lottery_num', 'curr_lottery_total_amount',
                'partner_curr_lottery_total_amount','per_curr_lottery', 'total_amount', 'lottery_total_amount', 'balance_amount',
                'lottery_num', 'partner_lottery_num', 'avg_curr_lottery_amount', 'add_active', 'active_num', 'per_reg', 'actual_division',
                'balance_real_amount', 'balance_reg_amount', 'balance_yj', 'balance_lottery_amount',
                'balance_lottery_money', 'curr_lottery_divided_active', 'reg_time', 'ret_ratio', 'settle_mode', 'next_lottery_num', 
                'per_next_lottery_num', 'redu_coeff_score', 'balance_active', 'cpstate', 'created');
            $sql1  = "insert cp_channel_count(" . implode(',', $fields) . ") values" . implode(',', $bdata['s_data']);
            $sql1 .= $this->onduplicate($fields, array('unit_price', 'reg_num', 'real_num', 'balance_reg', 'balance_real',
                'per_real', 'active_lottery_num', 'partner_active_lottery_num', 'curr_lottery_total_amount', 'partner_curr_lottery_total_amount', 'per_curr_lottery', 'total_amount',
                'lottery_total_amount', 'balance_amount', 'lottery_num', 'partner_lottery_num', 'avg_curr_lottery_amount', 'add_active',
                'active_num', 'per_reg', 'actual_division', 'balance_real_amount', 'balance_reg_amount', 'balance_yj',
                'balance_lottery_amount', 'balance_lottery_money', 'curr_lottery_divided_active', 'reg_time', 'ret_ratio', 'settle_mode', 
                'next_lottery_num', 'per_next_lottery_num', 'redu_coeff_score', 'balance_active', 'cpstate'));
            $this->db->query($sql1, $bdata['d_data']);
            
            $channels = $this->slave->query($sql, array($idStart))->getAll();
        }
    }
} 
