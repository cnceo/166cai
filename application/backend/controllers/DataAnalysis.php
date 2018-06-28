<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：系统日志管理
 * 作    者：shigx@2345.com
 * 修改日期：2015.07.20
 */
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class DataAnalysis extends MY_Controller
{
	//平台
	private $platform = array(
		'1' => '网页',
		'2' => 'app'
	);
	//彩种
	private $lid = array(
		'51' => '双色球',
		'23529' => '大乐透',
		'52' => '福彩3D',
		'33' => '排列3',
		'35' => '排列5',
		'10022' => '七星彩',
		'23528' => '七乐彩',
		'21406' => '十一选五',
		'42' => '竞彩足球',
		'43' => '竞彩篮球',
		'11' => '胜负彩',
		'19' => '任选九'
	);
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_Data_Analysis');
    }
    
    /**
     * 参    数：
     * 作    者：shigx
     * 功    能：投注管理
     * 修改日期：2015.07.20
     */
    public function betting()
    {
    	$this->check_capacity('9_6');
    	$platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
    	$channel = $this->input->get("channel", true) === '0' ? $this->input->get("channel", true) : ($this->input->get("channel", true) ? $this->input->get("channel", true) : 'all');
    	$version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
    	$lid = $this->input->get("lid", true);
    	$timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
    	$searchData = array(
    		'platform' => $platform,
    		'channel' => $channel,
    		'version' => $version,
    		'lid' => $lid,
    		'timeType' => $timeType,
    	);
    	$startDate = $this->Model_Data_Analysis->getStartDate($timeType);
    	$endDate = date('Y-m-d', strtotime('-1 day'));
    	$resutl = $this->Model_Data_Analysis->betting($searchData, $startDate, $endDate);
    	$dates = $this->getAllDates($startDate, $endDate);
    	$totalUv = 0;
    	$dateUv = array();
    	foreach ($resutl['uv'] as $value)
    	{
    		$dateUv[$value['date']] = $value['uv'];
    		$totalUv += $value['uv'];
    	}
    	
    	$total = array(
    		'users' => 0,
    		'conversion_rate' => 0,
    		'total' => 0,
    		'order_nums' => 0,
    		'avg_order_money' => 0,
    		'avg_user_money' => 0,
    		'awardUser' => 0,
    		'awardTotal' => 0,
    	);
    	$listData = array();
    	$orderDate = array();
    	foreach ($resutl['list'] as $value)
    	{
    		$week = $this->getWeek($value['date']);
    		$listData[$value['date']]['date'] = $value['date'] . '|' .$week['name'];
    		$listData[$value['date']]['betting_users'] = $value['betting_users'];
    		$listData[$value['date']]['conversion_rate'] = is_numeric(Division($value['betting_users'],$dateUv[$value['date']],1 )) ? number_format(Precent(Division($value['betting_users'],$dateUv[$value['date']],1)),2)."%":Division($value['betting_users'],$dateUv[$value['date']],1);    //(isset($dateUv[$value['date']]) && $dateUv[$value['date']] > 0) ? round($value['betting_users'] / $dateUv[$value['date']] * 100, 2) : 0;
    		$listData[$value['date']]['total'] = $value['total'];
    		$listData[$value['date']]['order_nums'] = $value['order_nums'];
    		$listData[$value['date']]['avg_order_money'] = is_numeric(Division($value['total'] ,$value['order_nums'],1 )) ? number_format(ParseUnit(Division($value['total'] ,$value['order_nums'],1),1),2):Division($value['total'] ,$value['order_nums'],1);//$value['order_nums'] > 0 ? $value['total'] / $value['order_nums'] : 0;
    		$listData[$value['date']]['avg_user_money'] =  is_numeric(Division($value['total'] ,$value['betting_users'],1 ))?number_format(ParseUnit(Division($value['total'] ,$value['betting_users'],1),1),2):Division($value['total'] ,$value['betting_users'],1); //$value['betting_users'] > 0 ? $value['total'] / $value['betting_users'] : 0;
    		$listData[$value['date']]['award_users'] = $value['award_users'];
    		$listData[$value['date']]['award_total'] = $value['award_total'];
    		$listData[$value['date']]['award_rate'] =  is_numeric(Division($value['award_total'],$value['total'],1 )) ? number_format(Precent(Division($value['award_total'],$value['total'],1)),2)."%":Division($value['award_total'],$value['total'],1);   //$value['total'] > 0 ? round($value['award_total'] / $value['total'] * 100, 2) : 0;
    		$listData[$value['date']]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
    		array_push($orderDate, $value['date']);
    		$total['users'] += $value['betting_users'];
    		$total['total'] += $value['total'];
    		$total['order_nums'] += $value['order_nums'];
    		$total['awardUser'] += $value['award_users'];
    		$total['awardTotal'] += $value['award_total'];
    	}
    	
    	$total['conversion_rate'] = is_numeric(Division($total['users'],$totalUv, 1))?Precent(Division($total['users'],$totalUv , 1)):Division($total['users'],$totalUv , 1);        //$totalUv > 0 ? round($total['users'] / $totalUv * 100, 2) : '0';
    	$total['avg_order_money'] = is_numeric(Division($total['total'],$total['order_nums'],1))?number_format(ParseUnit(Division($total['total'],$total['order_nums'],1),1),2):Division($total['total'],$total['order_nums'],1);          //$total['order_nums'] > 0 ? $total['total'] / $total['order_nums'] : 0;
    	$total['avg_user_money']  = is_numeric(Division($total['total'],$total['users'],1))?number_format(ParseUnit(Division($total['total'],$total['users'],1),1),2):Division($total['total'],$total['users'],1);            //$total['users'] > 0 ? $total['total'] / $total['users'] : 0;
    	$total['award_rate']      = $total['total'] > 0 ? round($total['awardTotal'] / $total['total'] * 100, 2) : '0';
    	
    	$dates = array_diff($dates, $orderDate);  //取差集
    	foreach ($dates as $date)
    	{
    		//初始化数据
    		$week = $this->getWeek($date);
    		$listData[$date]['date'] = $date . '|' . $week['name'];
    		$listData[$date]['betting_users'] = 0;
    		$listData[$date]['conversion_rate'] = "0.00%";
    		$listData[$date]['total'] = 0;
    		$listData[$date]['order_nums'] = 0;
    		$listData[$date]['avg_order_money'] = 0;
    		$listData[$date]['avg_user_money'] = 0;
    		$listData[$date]['award_users'] = 0;
    		$listData[$date]['award_total'] = 0;
    		$listData[$date]['award_rate'] = "--";
    		$listData[$date]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
    	}
    	
    	rsort($listData); //日期重新排序
    	
    	$dayNums = count($listData);
    	$avgTotal = array(
    		'avg_users' => is_numeric(Division($total['users'],$dayNums,1 ))? number_format(Division($total['users'],$dayNums,1 ),2):Division($total['users'],$dayNums,1 ),        //round($total['users'] / $dayNums, 2),
    		'conversion_rate' => $total['conversion_rate'],
    		'avg_total' => is_numeric(Division($total['total'],$dayNums , 1))?number_format(ParseUnit(Division($total['total'],$dayNums,1 ),1),2):Division($total['total'],$dayNums,1 ) ,//$total['total'] / $dayNums ,
    		'avg_nums' => is_numeric(Division($total['order_nums'],$dayNums,1 ))?number_format(round(Division($total['order_nums'],$dayNums,1 ),2),2):Division($total['order_nums'],$dayNums,1 ),      //round($total['order_nums'] / $dayNums, 2),
    		'avg_order_money' => $total['avg_order_money'],
    		'avg_user_money' => $total['avg_user_money'],
    		'awardUser' => is_numeric(Division($total['awardUser'],$dayNums,1 ))? number_format(Division($total['awardUser'],$dayNums,1 ),2):Division($total['awardUser'],$dayNums,1 ),  //round($total['awardUser'] / $dayNums, 2),
    		'awardTotal' => is_numeric(Division($total['awardTotal'],$dayNums,1))?number_format(ParseUnit(Division($total['awardTotal'],$dayNums,1),1),2):Division($total['awardTotal'],$dayNums,1),     // $total['awardTotal'] / $dayNums,
    	);
    	
    	$datas = array(
    			"search"	=> $searchData,
    			"list"	=> $listData,
    			'total' => $total,
    			'avgTotal' => $avgTotal,
    			'platform' => $this->platform,
    			'channels' => $this->Model_Data_Analysis->getChannels($platform),
    			'version' => $this->Model_Data_Analysis->getAppVersion(),
    			'lid' => $this->lid,
    	);
       	$this->load->view("DataAnalysis/betting", $datas);
    }
    
    /**
     * 处理日期数组
     * @param string $s	开始日期
     * @param string $e	结束日期
     * @return array()
     */
    private function getAllDates($s, $e)
    {
    	if (empty($s) || empty($e) || (strtotime($s) > strtotime($e)))
    	{
    		return array();
    	}
    	$res = array();
    	$datetime1 = new DateTime($s);
    	$datetime2 = new DateTime($e);
    	$interval  = $datetime1->diff($datetime2);
    	$days = $interval->format('%a');
    	for ($j = 0; $j <= $days; $j++)
    	{
	    	$time = strtotime("+$j days", strtotime($s));
	    	$val = date("Y-m-d", $time);
	    	array_push($res, $val);
    	}
    	
    	return $res;
    }
    
    /**
     * 渠道ajax请求
     */
    public function getChannels()
    {
    	$platform = intval($this->input->post("platform", true));
    	$data = $this->Model_Data_Analysis->getChannels($platform);
    	$str = '<option value="all">全部</option>';
    	foreach ($data as $val)
    	{
    		$str .= '<option value="'.$val['id'].'">'.$val['name'].'</option>';
    	}
    	echo $str;
    }
    
     /*
     * 提款
     * 
     * */
    public function withdraw()
    {
    	$this->check_capacity('9_5');
    	$platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';	
    	$version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
    	$timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
    	$searchData = array(
    		'platform' => $platform,
    		'version' => $version,
    		'timeType' => $timeType,
    	);
    	$startDate = $this->Model_Data_Analysis->getStartDate($timeType);
    	$endDate = date('Y-m-d', strtotime('-1 day'));
    	$resutl = $this->Model_Data_Analysis->withdraw($searchData, $startDate, $endDate);
    	$dates = $this->getAllDates($startDate, $endDate);
    	$totalUv = 0;
    	$dateUv = array();
    	foreach ($resutl['uv'] as $value)
    	{
    		$dateUv[$value['date']] = $value['uv'];
    		$totalUv += $value['uv'];
    	}
    	 
    	$total = array(
    		'users' => 0,
    		'conversion_rate' => 0,
    		'total' => 0,
    		'withdraw_nums' => 0,
    		'avg_withdraw_money' => 0,
    		'avg_user_money' => 0,
    	);
    	$listData = array();
    	$widthdrawDate = array();
    	foreach ($resutl['list'] as $value)
    	{
            $week = $this->getWeek($value['date']);
    		$listData[$value['date']]['date'] = $value['date'] . '|' .$week['name'];
    		$listData[$value['date']]['users'] = $value['users'];//is_numeric(Division($total_betting_users,$total_uv,1))?Precent(Division($total_betting_users,$total_uv,1),2):Division($total_betting_users,$total_uv,1)
    		$listData[$value['date']]['conversion_rate'] = (isset($dateUv[$value['date']]) && is_numeric(Division($value['users'],$dateUv[$value['date']],1 ))) ? Precent(Division($value['users'],$dateUv[$value['date']],1)):Division($value['users'],$dateUv[$value['date']],1);//(isset($dateUv[$value['date']]) && $dateUv[$value['date']] > 0) ? round($value['users'] / $dateUv[$value['date']] * 100, 2) : 0;
    		$listData[$value['date']]['total'] = $value['total'];
    		$listData[$value['date']]['withdraw_nums'] = $value['withdraw_nums'];
    		$listData[$value['date']]['avg_withdraw_money'] = is_numeric(Division($value['total'], $value['withdraw_nums'], 1))?number_format(ParseUnit(Division($value['total'], $value['withdraw_nums'], 1),1),2):Division($value['total'], $value['withdraw_nums'], 1);//$value['total'] / $value['withdraw_nums'];
    		$listData[$value['date']]['avg_user_money'] = is_numeric(Division($value['total'],$value['users'],1 ))? number_format(ParseUnit(Division($value['total'],$value['users'],1 ),1),2):Division($value['total'],$value['users'],1 );//$value['total'] / $value['users'];
    		$listData[$value['date']]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
    		array_push($widthdrawDate, $value['date']);
    		$total['users'] += $value['users'];
    		$total['total'] += $value['total'];
    		$total['withdraw_nums'] += $value['withdraw_nums'];
    	}

    	
    	$total['conversion_rate'] = is_numeric(Division($total['users'],$totalUv , 1))?Precent(Division($total['users'],$totalUv , 1)):Division($total['users'],$totalUv , 1);//$totalUv > 0 ? round($total['users'] / $totalUv * 100, 2) : '0';
    	$total['avg_withdraw_money'] = is_numeric(Division($total['total'],$total['withdraw_nums'],1))?number_format(ParseUnit(Division($total['total'],$total['withdraw_nums'],1),1),2):Division($total['total'],$total['withdraw_nums'],1);//$total['withdraw_nums'] > 0 ? $total['total'] / $total['withdraw_nums'] : 0;
    	$total['avg_user_money']  = is_numeric(Division($total['total'],$total['users'],1))?number_format(ParseUnit(Division($total['total'],$total['users'],1),1),2):Division($total['total'],$total['users'],1);             //$total['users'] > 0 ? $total['total'] / $total['users'] : 0;
    	
    	$dates = array_diff($dates, $widthdrawDate);  //取差集
    	foreach ($dates as $date)
    	{
    		//初始化数据
    		$week = $this->getWeek($date);
    		$listData[$date]['date'] = $date . '|' .$week['name'];
    		$listData[$date]['users'] = 0;
    		$listData[$date]['conversion_rate'] = "0.00%";
    		$listData[$date]['total'] = 0;
    		$listData[$date]['withdraw_nums'] = 0;
    		$listData[$date]['avg_withdraw_money'] = "--";
    		$listData[$date]['avg_user_money'] = "--";
    		$listData[$date]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
    	}
    	rsort($listData); //日期重新排序
    	
    	$dayNums = count($listData);
    	$avgTotal = array(
    		'avg_users' => is_numeric(Division($total['users'],$dayNums,1))?number_format(Division($total['users'],$dayNums,1),2):Division($total['users'],$dayNums,1),          //round($total['users'] / $dayNums, 2),
    		'conversion_rate' => $total['conversion_rate'],
    		'avg_total' => is_numeric(Division($total['total'],$dayNums,1 ))?number_format(ParseUnit(Division($total['total'],$dayNums,1 ),1),2):Division($total['total'],$dayNums,1 ),    //$total['total'] / $dayNums ,
    		'avg_nums' => is_numeric(Division($total['withdraw_nums'],$dayNums,1 ))?number_format(Division($total['withdraw_nums'],$dayNums,1 ),2):Division($total['withdraw_nums'],$dayNums,1 ),    //round($total['withdraw_nums'] / $dayNums, 2),
    		'avg_withdraw_money' => $total['avg_withdraw_money'],
    		'avg_user_money' => $total['avg_user_money'],
    	);
    	
    	$datas = array(
    			"search"	=> $searchData,
    			"list"	=> $listData,
    			'total' => $total,
    			'avgTotal' => $avgTotal,
    			'platform' => $this->platform,
    			'channels' => $this->Model_Data_Analysis->getChannels(),
    			'version' => $this->Model_Data_Analysis->getAppVersion(),
    	);
       	$this->load->view("DataAnalysis/withdraw", $datas);
    }
    
 	public function recharge()
    {
    	$this->check_capacity('9_4');
        $platform = $this->input->get("platform", true) ? $this->input->get("platform", true) : '1';
        $channel = $this->input->get("channel", true) === '0' ? $this->input->get("channel", true) : ($this->input->get("channel", true) ? $this->input->get("channel", true) : 'all');
        $version = $this->input->get("version", true) ? $this->input->get("version", true) : 'all';
        $timeType = $this->input->get("timeType", true) ? $this->input->get("timeType", true) : 'time1';
        $searchData = array(
            'platform' => $platform,
            'channel' => $channel,
            'version' => $version,
            'timeType' => $timeType,
        );
        $startDate = $this->Model_Data_Analysis->getStartDate($timeType);
        $endDate = date('Y-m-d', strtotime('-1 day'));
        $resutl = $this->Model_Data_Analysis->recharge($searchData, $startDate, $endDate);
        $dates = $this->getAllDates($startDate, $endDate);
        $totalUv = 0;
        $dateUv = array();
        foreach ($resutl['uv'] as $value)
        {
        	$dateUv[$value['date']] = $value['uv'];
        	$totalUv += $value['uv'];
        }
        $total = array(
        	'users' => 0,
        	'conversion_rate' => "0.00%",
        	'total' => 0,
        	'recharge_nums' => 0,
        	'avg_recharge_money' => 0,
        	'avg_user_money' => 0,
        );
        $listData = array();
        $orderDate = array();

        foreach ($resutl['list'] as $value)
        {
            $week = $this->getWeek($value['date']);
            $listData[$value['date']]['date'] = $value['date'] . '|' .$week['name'];
            $listData[$value['date']]['recharge_users'] = $value['users'];
            $listData[$value['date']]['conversion_rate'] = (isset($dateUv[$value['date']]) && is_numeric(Division($value['users'],$dateUv[$value['date']],1 ))) ? Precent(Division($value['users'],$dateUv[$value['date']],1)) : Division($value['users'],$dateUv[$value['date']],1);//(isset($dateUv[$value['date']]) && $dateUv[$value['date']] > 0) ? round($value['users'] / $dateUv[$value['date']] * 100, 2) : 0;
            $listData[$value['date']]['total'] = $value['total'];
            $listData[$value['date']]['recharge_nums'] = $value['recharge_nums'];
            $listData[$value['date']]['avg_recharge_money'] = is_numeric(Division($value['total'], $value['recharge_nums'], 1)) ? number_format(ParseUnit(Division($value['total'], $value['recharge_nums'], 1),1),2) : Division($value['total'], $value['recharge_nums'], 1);//$value['total'] / $value['recharge_nums'];
            $listData[$value['date']]['avg_user_money'] = is_numeric(Division($value['total'],$value['users'],1 )) ? number_format(ParseUnit(Division($value['total'],$value['users'],1 ),1),2) : Division($value['total'],$value['users'],1 );//$value['total'] / $value['users'];
            $listData[$value['date']]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
            array_push($orderDate, $value['date']);
            $total['users'] += $value['users'];
            $total['total'] += $value['total'];
            $total['recharge_nums'] += $value['recharge_nums'];
        }
        $total['conversion_rate'] = is_numeric(Division($total['users'],$totalUv , 1)) ? Precent(Division($total['users'],$totalUv , 1)) : Division($total['users'],$totalUv , 1);//$totalUv > 0 ? round($total['users'] / $totalUv * 100, 2) : '0';
        $total['avg_recharge_money'] = is_numeric(Division($total['total'],$total['recharge_nums'],1)) ? number_format(ParseUnit(Division($total['total'],$total['recharge_nums'],1),1),2): Division($total['total'],$total['recharge_nums'],1);//$total['recharge_nums'] > 0 ? $total['total'] / $total['recharge_nums'] : 0;
        $total['avg_user_money']  = is_numeric(Division($total['total'],$total['users'],1)) ? number_format(ParseUnit(Division($total['total'],$total['users'],1),1),2) : Division($total['total'],$total['users'],1);          //$total['users'] > 0 ? $total['total'] / $total['users'] : 0;
        $dates = array_diff($dates, $orderDate);  //取差集
        foreach ($dates as $date)
        {
            //初始化数据
            $week = $this->getWeek($date);
            $listData[$date]['date'] = $date . '|' . $week['name'];
            $listData[$date]['recharge_users'] = 0;
            $listData[$date]['conversion_rate'] = "0.00%";
            $listData[$date]['total'] = 0;
            $listData[$date]['recharge_nums'] = 0;
            $listData[$date]['avg_recharge_money'] = 0;
            $listData[$date]['avg_user_money'] = 0;
            $listData[$date]['dateClass'] = $week['week'] == 6 ? 'cGreen' : ($week['week'] == 7 ? 'cRed' : '');
        }
        rsort($listData); //日期重新排序  
        $dayNums = count($listData);
        $avgTotal = array(
            'avg_users' => is_numeric(Division($total['users'],$dayNums,1))?number_format(Division($total['users'],$dayNums,1),2):Division($total['users'],$dayNums,1),//round($total['users'] / $dayNums, 2),
            'conversion_rate' => $total['conversion_rate'],
            'avg_total' => is_numeric(Division($total['total'],$dayNums,1 ))?number_format(ParseUnit(Division($total['total'],$dayNums,1 ),1),2):Division($total['total'],$dayNums,1 ),  //$total['total'] / $dayNums ,
            'avg_nums' => is_numeric(Division($total['recharge_nums'],$dayNums,1 ))?number_format(Division($total['recharge_nums'],$dayNums,1 ),2):Division($total['recharge_nums'],$dayNums,1 ),             //round($total['recharge_nums'] / $dayNums, 2),
            'avg_recharge_money' => $total['avg_recharge_money'],
            'avg_user_money' => $total['avg_user_money'],
        );

        $datas = array(
                "search"    => $searchData,
                "list"  => $listData,
                'total' => $total,
                'avgTotal' => $avgTotal,
                'platform' => $this->platform,
                'channels' => $this->Model_Data_Analysis->getChannels($platform),
                'version' => $this->Model_Data_Analysis->getAppVersion(),
        );
        $this->load->view("DataAnalysis/recharge", $datas);
    }
    
    /**
     * 返回星期
     * @param unknown_type $date
     */
    private function getWeek($date)
    {
    	$weeks = array('1' => '星期一', '2' => '星期二', '3' => '星期三', '4' => '星期四', '5' => '星期五', '6' => '星期六', '7' => '星期天');
    	$week = date('N', strtotime($date));
    	return array(
    		'week' => $week,
    		'name' => $weeks[$week],
    	);
    }
}
