<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：数据分析
 * 作    者：shigx@2345.com
 * 修改日期：2015.07.20
 */
class Model_Data_Analysis extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 查询参数
     * 作    者：shigx
     * 功    能：获取投注统计列表
     * 修改日期：2015.07.20
     */
    public function betting($searchData, $startDate, $endDate)
    {
    	$where = " where 1";
    	$where .= $this->condition("date", array(
    			$startDate,
    			$endDate
    	), "time");
    	$o_table = 'cp_order_statistics_all';
    	if($searchData['lid'])
    	{
    		$o_table = 'cp_order_statistics';
    		$where .= $this->condition("lid", $searchData['lid']);
    	}
    	$where .= $this->condition("platform", $searchData['platform']);
    	if($searchData['channel'] != 'all')
    	{
    		$where .= $this->condition("channel", $searchData['channel']);
    	}
    	if($searchData['version'] != 'all' && $searchData['platform'] == '2')
    	{
    		$where .= $this->condition("version", $searchData['version']);
    	}
    	$list = $this->BcdDb->query("SELECT date, SUM(betting_users) betting_users, SUM(total) total, SUM(order_nums) order_nums, SUM(award_users) award_users, SUM(award_total) award_total FROM {$o_table} {$where} GROUP BY date ORDER BY date DESC")->getAll();
    	$uvWhere = " where 1";
    	$uvWhere .= $this->condition("date", array($startDate, $endDate), "time");
    	if($searchData['channel'] != 'all')
    	{
    		$uvWhere .= $this->condition("channel_id", $searchData['channel']);
    	}
    	if($searchData['platform'] == '2')
    	{
    		//app
    		if($searchData['version'] != 'all')
    		{
    			$uvWhere .= $this->condition("version", $searchData['version']);
    		}
    		$sumUv = $this->BcdDb->query("select date, sum(history_active_num) uv from cp_50bang_app {$uvWhere} GROUP BY date")->getAll();
    	}
    	else
    	{
    		//web
    		if($searchData['channel'] == 'all')
    		{
    		    $sumUv = $this->BcdDb->query("select date, sum(browse_uv) uv from cp_50bang_web_all {$uvWhere} GROUP BY date")->getAll();
    		}
    		else
    		{
    		    $sumUv = $this->BcdDb->query("select date, sum(browse_uv) uv from cp_50bang_web {$uvWhere} GROUP BY date")->getAll();
    		}
    	}
    	return array(
    		'list' => $list,
    		'uv' => $sumUv,
    	);
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：获取渠道列表
     * 修改日期：2015.07.20
     */
    public function getChannels($platform = '')
    {
    	$where = $platform ? " and platform={$platform}" : "";
    	return $this->BcdDb->query("select * from cp_channel where 1 {$where}")->getAll();
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：获取app版本列表
     * 修改日期：2015.07.20
     */
    public function getAppVersion()
    {
        return $this->BcdDb->query("select * from cp_app_version where 1")->getAll();
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
     * 根据查询条件获取提款记录列表
     * 
     * */
    public function withdraw($searchData, $startDate, $endDate)
    {
    	$where = " where 1";
    	$where .= $this->condition("date", array(
    			$startDate,
    			$endDate
    	), "time");
    	$where .= $this->condition("platform", $searchData['platform']);
    	if($searchData['version'] != 'all' && $searchData['platform'] == '2')
    	{
    		$where .= $this->condition("version", $searchData['version']);
    	}
    	$list = $this->BcdDb->query("SELECT date, SUM(users) users, SUM(total) total, SUM(withdraw_nums) withdraw_nums FROM cp_withdraw_statistics {$where} GROUP BY date ORDER BY date DESC")->getAll();
    	$uvWhere = " where 1";
    	$uvWhere .= $this->condition("date", array($startDate, $endDate), "time");
    	if($searchData['channel'] != 'all')
    	{
    		$uvWhere .= $this->condition("channel_id", $searchData['channel']);
    	}
    	if($searchData['platform'] == '2')
    	{
    		//app
    		if($searchData['version'] != 'all')
    		{
    			$uvWhere .= $this->condition("version", $searchData['version']);
    		}
    		$sumUv = $this->BcdDb->query("select date, sum(history_active_num) uv from cp_50bang_app {$uvWhere} GROUP BY date")->getAll();
    	}
    	else
    	{
    		//web
    	    $sumUv = $this->BcdDb->query("select date, sum(browse_uv) uv from cp_50bang_web_all {$uvWhere} GROUP BY date")->getAll();
    	}
    	return array(
    		'list' => $list,
    		'uv' => $sumUv,
    	);
    }
    
    
    public function recharge($searchData, $startDate, $endDate)
    {
        $where = " where 1";
        $where .= $this->condition("date", array(
                $startDate,
                $endDate
        ), "time");
        $where .= $this->condition("platform", $searchData['platform']);
        if($searchData['channel'] != 'all')
        {
            $where .= $this->condition("channel", $searchData['channel']);
        }
        if($searchData['version'] != 'all' && $searchData['platform'] == '2')
        {
            $where .= $this->condition("version", $searchData['version']);
        }
        $list = $this->BcdDb->query("SELECT date, SUM(users) users, SUM(total) total, SUM(recharge_nums) recharge_nums FROM cp_recharge_statistics {$where} GROUP BY date ORDER BY date DESC")->getAll();
        
        $uvWhere = " where 1";
        $uvWhere .= $this->condition("date", array($startDate, $endDate), "time");
        if($searchData['channel'] != 'all')
        {
            $uvWhere .= $this->condition("channel_id", $searchData['channel']);
        }
        if($searchData['platform'] == '2')
        {
            //app
            if($searchData['version'] != 'all')
            {
                $uvWhere .= $this->condition("version", $searchData['version']);
            }
            $sumUv = $this->BcdDb->query("select date, sum(history_active_num) uv from cp_50bang_app {$uvWhere} GROUP BY date")->getAll();
        }
        else
        {
        	//web
    		if($searchData['channel'] == 'all')
    		{
    		    $sumUv = $this->BcdDb->query("select date, sum(browse_uv) uv from cp_50bang_web_all {$uvWhere} GROUP BY date")->getAll();
    		}
    		else
    		{
    		    $sumUv = $this->BcdDb->query("select date, sum(browse_uv) uv from cp_50bang_web {$uvWhere} GROUP BY date")->getAll();
    		}
        }
        return array(
            'list' => $list,
            'uv' => $sumUv,
        );
    }

    /*
     * 获取优质用户相关数据
     * */
    public function get_all($searchData)
    {
        $where = " WHERE m.total_betmoney >= 10000";
        if($searchData['platform'] == '1')
        {
            $where .= ' AND n.platform = 0';//.$searchData['platform'];
        }
        if($searchData['platform'] == '2')
        {
            $where .= ' AND n.platform = 1';//.$searchData['platform'];
        }
        if($searchData['platform'] == '3')
        {
            $where .= ' AND n.platform = 2';//.$searchData['platform'];
        }
        if($searchData['platform'] == '4')
        {
            $where .= ' AND n.platform = 3';//.$searchData['platform'];
        }
        if($searchData['loginTimesEnd'] != 'max')
        {
            $where .= ' AND m.login_times_30day >= ' . $searchData['loginTimesBegin'] . ' AND m.login_times_30day <= ' . $searchData['loginTimesEnd'];
        }
        else
        {
            $where .= ' AND m.login_times_30day >= ' . $searchData['loginTimesBegin'];
        }
        if($searchData['totalMoneyEnd'] != 'max')
        {
            $where .= ' AND m.total_betmoney >= ' . ($searchData['totalMoneyBegin'] * 100) . ' AND m.total_betmoney <= ' . ($searchData['totalMoneyEnd']) * 100;
        }
        else
        {
            $where .= ' AND m.total_betmoney >= ' . ($searchData['totalMoneyBegin'] * 100);
        }
        if($searchData['channel'] != 'all')
        {
            $where .= " AND n.channel = '". $searchData['channel'] . "'";
        }
        if($searchData['platform'] == '2' || $searchData['platform'] == '3')
        {
            if($searchData['version'] != 'all')
            {
                $where .= " AND n.reg_reffer = '" . $searchData['version'] . "'";
            }
        }
        
        $select = "SELECT m.uid,n.uname,login_times_30day,total_betmoney/100 total_betmoney ,total_winmoney/100 total_winmoney, 
        order_num, m.account/100 account, betmoney11/100 betmoney11, betmoney19/100 betmoney19,
        betmoney33/100 betmoney33, betmoney35/100 betmoney35, betmoney41/100 betmoney41,
        betmoney42/100 betmoney42, betmoney43/100 betmoney43, betmoney51/100 betmoney51,
        betmoney52/100 betmoney52, betmoney10022/100 betmoney10022, betmoney21406/100 betmoney21406,
        betmoney23528/100 betmoney23528, betmoney23529/100 betmoney23529, login_time 
        FROM cp_hight_quality_user_index m LEFT JOIN cp_user n ON m.uid = n.uid ".$where;
        $result = $this->BcdDb->query($select)->getAll();
        return $result;
    }

	/*
     * 获取点击相关数据
     * */
    public function getClickNum($platform, $days)
    {
    	if($platform == '1')
    	{
            $sql = "select sum(browse_uv) browse_uv, sum(click_uv) click_uv from cp_50bang_web_all where `date` >= 
                    date(date_sub(now(), interval $days day))";
    	}
    	else
    	{
    		$sql = "select sum(history_active_num) browse_uv, sum(history_actionuv_num) click_uv from cp_50bang_app where `date` >= 
                    date(date_sub(now(), interval $days day))";
    	}    	
    	return $this->BcdDb->query($sql)->getRow();
    }
     /*
     * 获取注册相关数据
     * */
    public function getRegisterNum($platform, $days)
    {
		$sql = "select sum(register_num) register_num, sum(valid_user) valid_user from cp_register_stat where `cdate` >= 
				date(date_sub(now(), interval $days day)) and platform = $platform";
		return $this->BcdDb->query($sql)->getRow();
    }
     /*
     * 获取充值相关数据
     * */
    public function getRechargeNum($platform, $days)
    {
		$sql = "select sum(users) users from cp_recharge_statistics where `date` >= 
				date(date_sub(now(), interval $days day)) and platform = $platform";
		return $this->BcdDb->query($sql)->getRow();
    }
     /*
     * 获取订单相关数据
     * */
    public function getOrdersNum($platform, $days)
    {
		$sql = "select sum(betting_users) betting_users from cp_order_statistics_all where `date` >= 
				date(date_sub(now(), interval $days day)) and platform = $platform";  	
		return $this->BcdDb->query($sql)->getRow();
    }
    
    /*
     * 获取订单相关数据
     * */
    public function getAllSale($platform, $days)
    {
        $sql = "select date, sum(total) as total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day)) and platform = $platform group by date";
        return $this->BcdDb->query($sql)->getAll();
    }

     /*
     * 获取有效用户数
     * */
     public function getValidUser($platform, $days)
     {
        $sql = "select cdate, sum(valid_user) as valid_user from cp_register_stat where `cdate` >= date(date_sub(now(), interval $days day)) and platform = $platform group by cdate";
        return $this->BcdDb->query($sql)->getAll();
     }

     /*
     * 获取指定时间段投注总额
     * */
     public function getTotalSale($platform, $days)
     {
        $sql = "select sum(total) total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day)) and platform = $platform";    
        return $this->BcdDb->query($sql)->getRow();
     }

     /*
     * 各彩种销量占比
     * */
     public function getLotterySale($platform, $days)
     {
        $con = "";
        if(!empty($platform))
        {
            $sql = "select lid, sum(total) as total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day)) and platform = $platform group by lid"; 
            return $this->BcdDb->query($sql)->getAll();
        }
        else
        {
            $sql = "select sum(total) as total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day))"; 
            return $this->BcdDb->query($sql)->getRow();
        }   
        
     }

     /*
     * 各彩种销量占比
     * */
     public function getAllSaleByPlat($platform, $days)
     {
        $sql = "select sum(total) as total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day)) and platform = $platform"; 
        return $this->BcdDb->query($sql)->getRow();
     }

     /*
     * 各彩种返奖占比
     * */
     public function getLotteryAward($platform, $days)
     {
        $con = "";
        if(!empty($platform))
        {
            $con .= " and platform = $platform";
        }
        $sql = "select lid, sum(award_total) as award_total, sum(total) as total from cp_order_statistics where `date` >= date(date_sub(now(), interval $days day))" . $con . " group by lid"; 
        return $this->BcdDb->query($sql)->getAll();
     }

}