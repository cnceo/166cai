<?php

/*
 * 合买跟单
 * @date:2017-08-22
 */
class Model_United_follow extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
        $this->get_db();
    }
    
    public function list_orders($searchData, $page, $pageCount)
    {
        $table = "cp_united_follow_orders";
        $where = "WHERE {$table}.created >= '{$searchData['start_time']}' AND {$table}.created <= '{$searchData['end_time']}' ";
        if($searchData['name'])
        {
            $where .= "AND (cp_user1.uname = '{$searchData['name']}' OR {$table}.followId = '{$searchData['name']}' OR cp_user2.uname = '{$searchData['name']}') ";
        }
        if($searchData['lid'])
        {
            $where .= "AND {$table}.lid = '{$searchData['lid']}' ";
        }
        if($searchData['buyPlatform'] >= 0 && $searchData['buyPlatform'] != '')
        {
            $where .= "AND {$table}.buyPlatform = '{$searchData['buyPlatform']}' ";
        }
        if($searchData['followType'] >= 0 && $searchData['followType'] != '')
        {
            $where .= "AND {$table}.followType = '{$searchData['followType']}' ";
        }
        if($searchData['start_money'])
        {
            $where .= "AND {$table}.totalMoney >= {$searchData['start_money']} * 100  ";
        }
        if($searchData['end_money'])
        {
            $where .= "AND {$table}.totalMoney <= {$searchData['start_money']} * 100  ";
        }
        if($searchData['channel'] > 0)
        {
            $where.="and cp_user1.channel = '{$searchData['channel']}' ";
        }
        if($searchData['payType'] >= 0 && $searchData['payType'] != '')
        {
            $where .= "AND {$table}.payType = '{$searchData['payType']}' ";
        }
        if($searchData['status'] >= 0 && $searchData['status'] != '')
        {
            // 跟单中
            if($searchData['status'] == 1)
            {
                $where .= "AND {$table}.status >= '1' AND {$table}.my_status = '0' ";
            }
            elseif($searchData['status'] == 5)
            {
                $where .= "AND {$table}.status >= '1' AND {$table}.payType = 0 ";
            }
            elseif($searchData['status'] > 1)
            {
                $where .= "AND {$table}.status = '{$searchData['status']}' AND {$table}.my_status = '1' ";
            }
            else
            {
                $where .= "AND {$table}.status = '{$searchData['status']}' AND {$table}.payType = 0 ";
            } 
        }
        if($searchData['fromType'] == 'ajax')
        {
            if($searchData['uid'])
            {
                $where .= "AND {$table}.uid = '{$searchData['uid']}' ";
            }
        }
        $sql = "SELECT {$table}.id, {$table}.followId, {$table}.uid, {$table}.puid, {$table}.lid, {$table}.payType, {$table}.followType, {$table}.totalMoney, {$table}.blockMoney, {$table}.buyMoney, {$table}.buyMoneyRate, {$table}.buyMaxMoney, {$table}.followTimes, {$table}.followTotalTimes, {$table}.status, {$table}.my_status, {$table}.lastFollowTime, {$table}.totalMargin, {$table}.effectTime, {$table}.buyPlatform, {$table}.created, cp_user1.channel, cp_user1.uname AS uname, cp_user2.uname AS puname FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user1 ON {$table}.uid = cp_user1.uid LEFT JOIN {$this->cp_user} AS cp_user2 ON {$table}.puid = cp_user2.uid " . $where . "ORDER BY {$table}.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();

        $sql = "SELECT count({$table}.id) as num, sum({$table}.totalBuyMoney) as totalBuyMoney, sum({$table}.totalMargin) as totalMargin, count(distinct({$table}.uid)) as totalUsers, sum(case when {$table}.payType = 0 then 1 else 0 end) as payByAdvance, sum(case when {$table}.payType = 1 then 1 else 0 end) as payByTime FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user1 ON {$table}.uid = cp_user1.uid LEFT JOIN {$this->cp_user} AS cp_user2 ON {$table}.puid = cp_user2.uid {$where}";
        $count = $this->BcdDb->query($sql)->getRow();

        return array(
            'data'  => $res,
            'count' => $count,
        );
    }

    // 定制管理
    public function list_planner($searchData, $page, $pageCount)
    {
        $table = "cp_united_planner";
        $where = "WHERE 1 AND {$table}.followTimes > 0 ";
        if($searchData['name'])
        {
            $where .= "AND cp_user.uname like '%{$searchData['name']}%' ";
        }
        if($searchData['lid'])
        {
            $where .= "AND  {$table}.lid = '{$searchData['lid']}' ";
        }
        if($searchData['status'] >= 0)
        {
            if($searchData['status'])
            {
                $where .= "AND  {$table}.isFollowNum >= 2000 ";
            }
            else
            {
                $where .= "AND  {$table}.isFollowNum < 2000 ";
            }
        }

        $sql = "SELECT {$table}.id, {$table}.uid, {$table}.lid, {$table}.united_points, {$table}.winningTimes, {$table}.bonus, {$table}.isFollowNum, {$table}.followTimes, cp_user.uname FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.uid = cp_user.uid " . $where . "ORDER BY {$table}.bonus DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();

        $sql = "SELECT count({$table}.id) as num FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.uid = cp_user.uid {$where}";
        $count = $this->BcdDb->query($sql)->getRow();

        return array(
            'data'  => $res,
            'count' => $count,
        );
    }

    // 发起人粉丝信息
    public function list_followers($searchData, $page, $pageCount)
    {
        $table = "cp_united_follow_orders";
        $where = "WHERE 1 AND status > 0 ";
        if($searchData['puid'])
        {
            $where .= "AND {$table}.puid = '{$searchData['puid']}' ";
        }
        if($searchData['lid'])
        {
            $where .= "AND {$table}.lid = '{$searchData['lid']}' ";
        }
        if($searchData['status'] >= 0 && $searchData['status'] != '')
        {
            // 跟单中
            if($searchData['status'] == 1)
            {
                $where .= "AND {$table}.status >= '1' AND {$table}.my_status = '0' ";
            }
            elseif($searchData['status'] > 1)
            {
                $where .= "AND {$table}.status = '{$searchData['status']}' AND {$table}.my_status = '1' ";
            }
            else
            {
                $where .= "AND {$table}.status = '{$searchData['status']}' ";
            } 
        }
        if($searchData['name'])
        {
            $where .= "AND cp_user.uname like '%{$searchData['name']}%' ";
        }

        $sql = "SELECT {$table}.followId, {$table}.uid, {$table}.lid, {$table}.puid, {$table}.lid, {$table}.payType, {$table}.followType, {$table}.totalMoney, {$table}.buyMoney, {$table}.buyMoneyRate, {$table}.buyMaxMoney, {$table}.completeTimes, {$table}.followTimes, {$table}.followTotalTimes, {$table}.my_status, {$table}.status, {$table}.effectTime, {$table}.created, cp_user.uname FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.uid = cp_user.uid " . $where . "ORDER BY {$table}.effectTime DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();

        $sql = "SELECT count({$table}.id) as num FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.uid = cp_user.uid {$where}";
        $count = $this->BcdDb->query($sql)->getRow();

        return array(
            'data'  => $res,
            'count' => $count,
        );
    }

    // 跟单详情
    public function list_detail($searchData, $page, $pageCount)
    {
        $countSql = "SELECT f.followId, f.uid, f.lid, f.payType, f.followType, f.totalMoney, f.blockMoney, f.buyMoney, f.buyMoneyRate, f.buyMaxMoney, f.completeTimes, f.followTimes, f.followTotalTimes, f.my_status, f.status, f.lastFollowTime, f.totalBuyMoney, f.totalMargin, f.effectTime, f.buyPlatform, f.channel, f.created, u.uname FROM cp_united_follow_orders AS f LEFT JOIN cp_user AS u ON f.uid = u.uid WHERE f.followId = ?";
        $info = $this->BcdDb->query($countSql, array($searchData['followId']))->getRow();

        $table = "cp_united_follow_join";
        $where = "WHERE 1 AND {$table}.followId = '{$searchData['followId']}' ";

        $sql = "SELECT {$table}.followId, {$table}.uid, {$table}.puid, {$table}.hmOrderId, {$table}.subscribeId, {$table}.buyMoney, {$table}.refundMoney, cp_user.uname, uj.issue, uj.money, uj.buyMoney, uj.created, uj.margin, uj.status FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.puid = cp_user.uid LEFT JOIN cp_united_join AS uj ON {$table}.subscribeId = uj.subscribeId " . $where . "ORDER BY {$table}.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $res = $this->BcdDb->query($sql)->getAll();

        $sql = "SELECT count({$table}.id) as num FROM {$table} LEFT JOIN {$this->cp_user} AS cp_user ON {$table}.uid = cp_user.uid {$where}";
        $count = $this->BcdDb->query($sql)->getRow();

        return array(
            'info'  => $info,
            'data'  => $res,
            'count' => $count,
        );
    }
    
}
