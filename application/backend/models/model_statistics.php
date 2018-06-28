<?php

/*
 * 后台 财务对账 模型层
 * @date:2015-12-15
 */
class Model_statistics extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    /*
     * 查询指定时间段内的对账信息
     */
    function list_statistics($searchData, $page, $pageCount)
    {
        $where = 'where 1 ';

        if($searchData['searchType'] == 'day')
        {
            $start_time = date('Y-m-d', strtotime($searchData['start_time']));  
            $end_time = date('Y-m-d', strtotime($searchData['end_time']));
            $where .= "and date >= '{$start_time}' and date <= '{$end_time}' ";
            $where .= "order by date DESC ";
            // 条数统计
            $count = $this->BcdDb->query("SELECT count(*) FROM cp_wallet_statistics {$where}")->getOne();
            // 字段汇总统计
            $sum = $this->BcdDb->query("SELECT SUM(recharge) as recharge,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(withdraw) as withdraw,SUM(withdraw_fail) as withdraw_fail,SUM(activity) as activity,SUM(oplus) as oplus,SUM(ominus) as ominus,SUM(rebate) as rebate,SUM(money) as money FROM cp_wallet_statistics {$where}")->getAll();

            $where .= "LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            // 每日统计
            $result = $this->BcdDb->query("SELECT * FROM cp_wallet_statistics {$where}")->row_array();
        }
        else
        {
            $start_time = date('Y-m-01', strtotime($searchData['start_time']));
            $lastday = date('Y-m-01', strtotime($searchData['end_time']));
            $end_time = date('Y-m-d', strtotime("$lastday +1 month -1 day"));
            $where .= "and date >= '{$start_time}' and date <= '{$end_time}' ";
            $where .= "GROUP BY months ORDER BY months DESC ";
            // 条数统计
            $count = $this->BcdDb->query("SELECT DATE_FORMAT(date,'%Y-%m') AS months, SUM(recharge) as recharge,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(withdraw) as withdraw,SUM(withdraw_fail) as withdraw_fail,SUM(activity) as activity,SUM(oplus) as oplus,SUM(ominus) as ominus,SUM(rebate) as rebate,SUM(money) as money FROM cp_wallet_statistics {$where}")->row_array();
            $count = count($count);
            // 字段汇总统计
            $sum = $this->BcdDb->query("SELECT SUM(recharge) as recharge,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(withdraw) as withdraw,SUM(withdraw_fail) as withdraw_fail,SUM(activity) as activity,SUM(oplus) as oplus,SUM(ominus) as ominus,SUM(rebate) as rebate,SUM(money) as money FROM cp_wallet_statistics WHERE date >= '{$start_time}' and date <= '{$end_time}'")->getAll();

            $where .= "LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            // 每月统计
            $result = $this->BcdDb->query("SELECT DATE_FORMAT(date,'%Y-%m') AS months, SUM(recharge) as recharge,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(withdraw) as withdraw,SUM(withdraw_fail) as withdraw_fail,SUM(activity) as activity,SUM(oplus) as oplus,SUM(ominus) as ominus,SUM(rebate) as rebate,SUM(money) as money FROM cp_wallet_statistics {$where}")->row_array();
            // 余额统计 当前月最后一天的余额数据
            if(!empty($result))
            {
                $monthArry = array();
                foreach ($result as $key => $monthData) 
                {
                    $data = $this->getDateMoney($monthData['months']);
                    $result[$key]['money'] = $data['money'];
                }
            }
        }      
        return array($result, $count, $sum);
    }

    /*
     * 查询指定月最后有效余额
     */
    public function getDateMoney($date)
    {
        $result = $this->BcdDb->query("SELECT date, money FROM cp_wallet_statistics WHERE DATE_FORMAT(date,'%Y-%m') = '{$date}' ORDER BY date DESC LIMIT 1")->getRow();
        return $result;
    }

    /*
     * 查询指定时间段内的对账信息
     */
    public function getUserDetail($searchData, $page, $pageCount)
    {
        $where1 = "where 1 ";
        if($searchData['searchType'] == 'day')
        {
            $where1 .= "and date = '{$searchData['date']}' ";
            $con = "s.date = '{$searchData['date']}' ORDER BY s.uid DESC";
        }
        else
        {
            $where1 .= "and date_format(date,'%Y-%m') = '{$searchData['date']}' ";
            $where1 .= "ORDER BY date DESC ";
            $con = "date_format(s.date,'%Y-%m') = '{$searchData['date']}' ORDER BY s.date DESC";    
        }

        $count = $this->BcdDb->query("SELECT count(*) FROM cp_wallet_statistics_user {$where1}")->getOne();

        $sum = $this->BcdDb->query("SELECT SUM(recharge) as recharge,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(withdraw) as withdraw,SUM(withdraw_fail) as withdraw_fail,SUM(activity) as activity,SUM(oplus) as oplus,SUM(ominus) as ominus,SUM(rebate) as rebate,SUM(money) as money FROM cp_wallet_statistics_user {$where1}")->getAll();

        $where2 = "LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $result = $this->BcdDb->query("SELECT s.date AS date, s.uid AS uid, u.uname AS uname, s.recharge, s.bonus,s.cost, s.refund, s.withdraw, s.withdraw_fail, s.activity, s.oplus, s.ominus, s.rebate, s.money FROM cp_wallet_statistics_user AS s LEFT JOIN cp_user AS u ON s.uid = u.uid WHERE {$con} {$where2}")->row_array();

        return array($result, $count, $sum);
    }

    // 合作商对账
    public function partner_statistics($searchData, $page, $pageCount)
    {
        $where = 'where 1 ';
        if($searchData['searchType'] == 'day')
        {
            $start_time = date('Y-m-d', strtotime($searchData['start_time']));  
            $end_time = date('Y-m-d', strtotime($searchData['end_time']));
            $where .= "and date >= '{$start_time}' and date <= '{$end_time}' and seller = '{$searchData[partnerType]}' ";
            $where .= "order by date DESC ";
            // 条数统计
            $count = $this->BcdDb->query("SELECT count(*) FROM cp_wallet_statistics_partner {$where}")->getOne();
            // 字段汇总统计
            // $sum = $this->BcdDb->query("SELECT SUM(deposit) as deposit,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(money) as money FROM cp_wallet_statistics_partner {$where}")->getAll();

            $where .= "LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            // 每日统计
            $result = $this->BcdDb->query("SELECT * FROM cp_wallet_statistics_partner {$where}")->row_array();
        }
        else
        {
            $start_time = date('Y-m-01', strtotime($searchData['start_time']));
            $lastday = date('Y-m-01', strtotime($searchData['end_time']));
            $end_time = date('Y-m-d', strtotime("$lastday +1 month -1 day"));
            $where .= "and date >= '{$start_time}' and date <= '{$end_time}' and seller = '{$searchData[partnerType]}' ";

            // 字段汇总统计
            // $sum = $this->BcdDb->query("SELECT SUM(deposit) as deposit,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(money) as money FROM cp_wallet_statistics_partner {$where}")->getAll();

            $where .= "GROUP BY months,seller ORDER BY months DESC ";
            // 条数统计
            $count = $this->BcdDb->query("SELECT DATE_FORMAT(date,'%Y-%m') AS months, SUM(deposit) as deposit,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(money) as money FROM cp_wallet_statistics_partner {$where}")->row_array();
            $count = count($count);

            $where .= "LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
            // 每月统计
            $result = $this->BcdDb->query("SELECT DATE_FORMAT(date,'%Y-%m') AS months, seller, SUM(deposit) as deposit,SUM(bonus) as bonus,SUM(cost) as cost,SUM(refund) as refund,SUM(money) as money FROM cp_wallet_statistics_partner {$where}")->row_array();

            // 余额统计 当前月最后一天的余额数据
            if(!empty($result))
            {
                $monthArry = array();
                foreach ($result as $key => $monthData) 
                {
                    $data = $this->getPartnerMoney($monthData['months'], $searchData['partnerType']);
                    $result[$key]['money'] = $data['money'];
                }
            }
        }      
        return array($result, $count);
    }

    // 合作商统计
    public function getPartner()
    {
        $partners = $this->slaveCfg1->query("SELECT name as seller FROM cp_ticket_sellers")->getAll();
        return $partners;
    }
        

    /*
     * 查询指定月商户最后有效余额
     */
    public function getPartnerMoney($date, $partner)
    {
        $result = $this->BcdDb->query("SELECT date, money FROM cp_wallet_statistics_partner WHERE DATE_FORMAT(date,'%Y-%m') = '{$date}' AND seller = '{$partner}' ORDER BY date DESC LIMIT 1")->getRow();
        return $result;
    }
    
    /**
     * 查询对账统计列表
     * @param unknown_type $searchData
     * @param unknown_type $page
     * @param unknown_type $pageCount
     */
    public function listCheckData($searchData, $tabId, $page = '', $pageCount = '')
    {
    	$table = $this->getTableByTabId($tabId);
    	$where = " where 1";
    	if ($this->emp($searchData['config_id']) && $searchData['config_id'] != 'all')
    	{
    		$where .= " AND a.config_id='{$searchData['config_id']}'";
    	}
    	 
    	$where .= $this->condition(" a.date", array(
    			$searchData['start_time'],
    			$searchData['end_time']
    	), "time");
    	$countSql = "select count(*) from {$table} a inner join cp_data_check_config b on b.id=a.config_id {$where}";
    	$count = $this->BcdDb->query($countSql)->getOne();
    	$limit = ($page && $pageCount) ? " limit ".($page - 1) * $pageCount.", ". $pageCount : "";
    	$sql = "select a.id, a.config_id, b.name, a.date, a.s_money, a.o_money, a.mark, a.status, a.e_flag, a.r_flag from {$table} a 
    	inner join cp_data_check_config b on b.id = a.config_id 
    	{$where} order by a.id desc {$limit}";
    	$result = $this->BcdDb->query($sql)->getAll();
    	
    	return array($result, $count);
    }
    
    /**
     * 查询差错池列表
     * @param unknown_type $config_id
     * @param unknown_type $tabId
     * @param unknown_type $date
     */
    public function listErrorCheckData($config_id, $tabId, $date)
    {
    	$table = $this->getErrorTableByTabId($tabId);
    	$name = $this->BcdDb->query("select name from cp_data_check_config where id = ?", array($config_id))->getOne();
    	if($tabId == '1')
    	{
    		$sql = "select sub_order_id as trade_no, date, s_status, o_status, s_money, o_money from {$table} where config_id = ? and date = ?";
    	}
    	else
    	{
    		$sql = "select trade_no, date, s_status, o_status, s_money, o_money from {$table} where config_id = ? and date = ?";
    	}
    	
    	$result = $this->BcdDb->query($sql, array($config_id, $date))->getAll();
    	
    	return array($result, $name);
    }
    
    /**
     * 返回对账类型信息
     * @param unknown_type $type
     */
    public function getDataCheckNames($type)
    {
    	$sql = "select id, name from cp_data_check_config where type = ?";
    	return $this->BcdDb->query($sql, array($type))->getAll();
    }
    
    /**
     * 更新对账统计表
     * @param unknown_type $id
     * @param unknown_type $tabId
     * @param unknown_type $data
     */
    public function updateDataCheckTotal($id, $tabId, $data)
    {
    	$table = $this->getTableByTabId($tabId);
    	$this->master->where('id', $id);
    	$this->master->update($table, $data);
    	return $this->master->affected_rows();
    }
    
    /**
     * 根据tabId 返回对应表名
     * @param unknown_type $tabId
     * @return Ambigous <string>
     */
    private function getTableByTabId($tabId)
    {
    	$tableArr = array(
    		'1' => 'cp_data_check_total_split',
    		'2' => 'cp_data_check_total_recharge',
    		'3' => 'cp_data_check_total_withdraw',
    	);
    	
    	return $tableArr[$tabId];
    }
    
    /**
     * 根据tabId 返回对应表名
     * @param unknown_type $tabId
     * @return Ambigous <string>
     */
    private function getErrorTableByTabId($tabId)
    {
    	$tableArr = array(
    			'1' => 'cp_data_check_error_split',
    			'2' => 'cp_data_check_error_recharge',
    			'3' => 'cp_data_check_error_withdraw',
    	);
    	 
    	return $tableArr[$tabId];
    }
}
