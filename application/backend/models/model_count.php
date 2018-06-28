<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：充值提现管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_count extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：统计昨日的用户概况数据
     * 修改日期：2014.11.05
     */
    public function count_yestoday($t_start, $days = 86400, $type = 1)
    {
        $y_start = date("Y-m-d H:i:s", $t_start - $days);
        $y_end = date("Y-m-d H:i:s", $t_start - 1);
        $reg_num = $effective_num = $recharge_num = $withdraw_num = $order_suc_sum = 0;
        $recharg_money = $withdraw_money = $order_suc_money = 0;
        $win_num = $win_money = 0;
        $recharge_array = $withdraw_array = $order_suc_array = $win_array = array();
        //注册用户数
        $select = "SELECT count(*) as count FROM {$this->cp_user} 
                    WHERE created >= '{$y_start}' and created <= '{$y_end}'";
        //有效用户数
        $select4 = "SELECT count(*) as count FROM {$this->cp_u_i} 
        WHERE bind_id_card_time >= '{$y_start}' and bind_id_card_time <= '{$y_end}'";
        //充值/提款 用户数
        $select1 = "SELECT ctype,uid,money FROM {$this->cp_w_l}
                    WHERE (ctype = '0' and mark = '1' and recharge_over_time >= '{$y_start}' and recharge_over_time <= '{$y_end}' )
                            or (ctype = '6' and created >= '{$y_start}' and created <= '{$y_end}')";
        //出票用户数
        $select2 = "SELECT uid,(money-failMoney) money FROM {$this->cp_orders}
                    WHERE pay_time >= '{$y_start}' and pay_time <= '{$y_end}' and status != '600'";
        
        //中奖用户数
        $select3 = "SELECT  uid,bonus as money FROM {$this->cp_orders}
                    WHERE  win_time >= '{$y_start}' and win_time <= '{$y_end}'";
        
        $result = $this->BcdDb->query($select)->row();
        $reg_num = $result->count; //注册用户数
        $result4 = $this->BcdDb->query($select4)->row();
        $effective_num = $result4->count; //有效用户数
        
        $result1 = $this->BcdDb->query($select1)->row_array();
        foreach ($result1 as $key1 => $value1)
        {
            if ($value1['ctype'] == '0')
            {
                $recharge_array[] = $value1['uid'];
                $recharg_money += $value1['money']; //充值总额
            }
            else
            {
                $withdraw_array[] = $value1['uid'];
                $withdraw_money += $value1['money']; //提款总额
            }
        }
        $recharge_num = count(array_unique($recharge_array)); //充值用户数
        $withdraw_num = count(array_unique($withdraw_array)); //提款用户数
        
        $result2 = $this->BcdDb->query($select2)->row_array();
        foreach ($result2 as $key2 => $value2)
        {
            $order_suc_array[] = $value2['uid'];
            $order_suc_money += $value2['money']; //出票成功金额
        }
        $order_suc_num = count(array_unique($order_suc_array)); //出票成功用户数
        
        $result3 = $this->BcdDb->query($select3)->row_array();
        foreach ($result3 as $key3 => $value3)
        {
            $win_array[] = $value3['uid'];
            $win_money += $value3['money']; //中奖金额
        }
        $win_num = count(array_unique($win_array)); //中奖用户数   
        
        $addData = array(
            "reg_num" => $reg_num,
            "recharge_num" => $recharge_num,
            "order_suc_num" => $order_suc_num,
            "withdraw_num" => $withdraw_num,
            "effective_num" => $effective_num,
            "recharge_money" => $recharg_money,
            "order_suc_money" => $order_suc_money,
            "withdraw_money" => $withdraw_money,
            "win_num" => $win_num,
            "win_money" => $win_money,
            "addTime" => $t_start - $days,
            "type" => $type
        );
        
        foreach ($addData as $akey => $avalue)
        {
            $keyStr .= "`{$akey}`,";
            $valueStr .= "'{$avalue}',";
        }
        $keyStr = trim($keyStr, ",");
        $valueStr = trim($valueStr, ",");
        $insert = "INSERT INTO {$this->tongji}({$keyStr}) VALUES({$valueStr}) ON DUPLICATE KEY UPDATE addTime = addTime + 0";
        
        $this->master->query($insert);
        unset($select);
        unset($select1);
        unset($select2);
        unset($select3);
        unset($insert);
        unset($addData);
        return $this->master->insert_id();
    }

    /**
     * 参    数：$days 天数
     * 作    者：wangl
     * 功    能：获取最近N天的用户概况数据
     * 修改日期：2014.11.05
     */
    public function get_ndays($days)
    {
        $t_start = strtotime(date("Y-m-d", time())) - $days * 86400;
        $select = "SELECT * FROM {$this->tongji} WHERE addTime = '{$t_start}' and type='{$days}'";
        $result = $this->BcdDb->query($select)->row_array();
        return $result[0];
    }
    
}
