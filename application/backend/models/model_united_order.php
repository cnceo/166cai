<?php

/**
 * 摘    要：合买订单模型
 * 作    者：yindefu
 * 修改日期：2016.12.09
 */
class Model_united_order extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }

    /**
     * 合买订单列表分页
     * @param array $searchData
     * @param int   $page
     * @param int   $pageCount
     * @return array
     */
    public function list_orders($searchData, $page, $pageCount)
    {
        $table = "cp_united_orders";
        if ($searchData['fromType'] == 'ajax')
        {
            $table = "cp_united_join";
        }
        $where = "where {$table}.created>='{$searchData['start_time']}' and {$table}.created<='{$searchData['end_time']}' ";
        if ($searchData['name'])
        {
            $sql = "select orderId from cp_united_join where subscribeId = '{$searchData['name']}'";
            $res = $this->BcdDb->query($sql)->getRow();
            if (!empty($res['orderId']))
            {
                $searchData['name'] = $res['orderId'];
            }
            $where.="and ({$this->cp_user}.uname = '{$searchData['name']}' or {$table}.orderId = '{$searchData['name']}') ";
        }
        if($searchData['lid'])
        {
            $where.="and {$table}.lid = '{$searchData['lid']}' ";
        }
        if ($searchData['buyPlatform'] >= 0 && $searchData['buyPlatform']!= '')
        {
            $where.="and {$table}.buyPlatform = '{$searchData['buyPlatform']}'  ";
        }
        if ($searchData['proportion'] > 0 && $searchData['fromType'] != 'ajax')
        {
            $where.="and {$table}.commissionRate <= '{$searchData['proportion']}'  ";
        }
        if($searchData['issue'])
        {
            $where.="and {$table}.issue = '{$searchData['issue']}' ";
        }
        if($searchData['start_money'])
        {
            $where.="and {$table}.money >= {$searchData['start_money']}*100  ";
        }
        if($searchData['end_money'])
        {
            $where.="and {$table}.money <= {$searchData['end_money']}*100  ";
        }
        if ($searchData['status'] >= 0 && !in_array($searchData['status'],array(999,998,997)) && $searchData['status']!==false)
        {
            $where.="and {$table}.status = '{$searchData['status']}'  ";
        }
        elseif ($searchData['status'] == 999)
        {
            $where.="and {$table}.status in (500,1000,2000) ";
        }
        elseif ($searchData['status'] == 998)
        {
            $where.="and {$table}.status in (40,240,500) and {$table}.money>{$table}.buyTotalMoney ";
        }
        elseif ($searchData['status'] == 997)
        {
            $where.="and (({$table}.status in (1000,2000)) or ({$table}.status in (500,240) and {$table}.money<={$table}.buyTotalMoney)) ";
        }
        if ($searchData['my_status'] >= 0 && $searchData['my_status'] != '')
        {
            $where.="and {$table}.my_status ='{$searchData['my_status']}' ";
        }
        if ($searchData['playType'] >= 0 && $searchData['playType']!= '')
        {
            $where.="and {$table}.playType ='{$searchData['playType']}' ";
        }
        if ($searchData['guarantee'] == 1 && $searchData['fromType'] != 'ajax')
        {
            $where.="and {$table}.guaranteeAmount >0  ";
        }
        if ($searchData['guarantee'] == 2 && $searchData['fromType'] != 'ajax')
        {
            $where.="and {$table}.guaranteeAmount =0  ";
        }
        if($searchData['webGurantee'] && $searchData['fromType'] != 'ajax')
        {
            $where.="and {$table}.webguranteeAmount >0  ";
        }
        if($searchData['channel'])
        {
            $where.="and {$this->cp_user}.channel = '{$searchData['channel']}' ";
        }
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where .= "and {$this->cp_user}.reg_type in ('0', '2') ";
            }
            else
            {
                $where .= "and {$this->cp_user}.reg_type = '{$searchData['reg_type']}' ";
            } 
        }
        if ($searchData['uid'])
        {
            $where.="and {$table}.uid='{$searchData['uid']}' ";
        }
        if ($searchData['fromType'] == 'ajax')
        {
            if ($searchData['orderType'] > 0)
            {
                if($searchData['orderType'] == 3)
                {
                    // 定制跟单
                    $where.="and {$table}.orderType='2' and {$table}.subOrderType='1' ";
                }
                else
                {
                   $where.="and {$table}.orderType='{$searchData['orderType']}' and {$table}.subOrderType='0' "; 
                }      
            }
            $sql = "select {$table}.id,{$table}.orderId,{$table}.buyMoney,{$table}.puid,{$table}.uid,{$table}.orderType,{$table}.lid,{$table}.subscribeId,{$table}.issue,{$table}.money,{$table}.margin,{$table}.status,{$table}.my_status,{$table}.buyPlatform,{$table}.created,{$this->cp_user}.channel,{$this->cp_user}.uname,{$table}.subOrderType,{$this->cp_user}.reg_type "
                    . "from {$table} left join {$this->cp_user} on {$table}.puid = {$this->cp_user}.uid " . $where . "order by created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
                    $res = $this->BcdDb->query($sql)->getAll();
            $sql = "select count({$table}.id) as num, sum({$table}.money) as totalMoney,
                    sum(case when {$table}.status in (500, 1000, 2000) then {$table}.money else 0 end) as drawMoney, 
                    sum({$table}.margin) as margin from {$table} left join {$this->cp_user} on {$table}.puid = {$this->cp_user}.uid {$where}";
            $one = $this->BcdDb->query($sql)->getRow();
            return array(
                'data' => $res,
                'count' => $one,
            );
        }
        $sql = "select {$table}.id,{$table}.orderId,{$table}.buyMoney,{$table}.uid,{$table}.lid,{$table}.issue,{$table}.money,{$table}.orderMargin,{$table}.buyTotalMoney,{$table}.guaranteeAmount,{$table}.status,{$table}.my_status,{$table}.isTop,{$table}.buyPlatform,{$table}.created,{$this->cp_user}.channel,{$this->cp_user}.reg_type,{$this->cp_user}.uname "
                . "from {$table} left join {$this->cp_user} on {$table}.uid = {$this->cp_user}.uid " . $where . "order by isTop DESC,created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount; 
                $res = $this->BcdDb->query($sql)->getAll();
        $sql = "select count({$table}.id) as num, sum(case when {$table}.endTime > NOW() and {$table}.buyTotalMoney < {$table}.money and {$table}.status in (40, 240, 500) then 1 else 0 end) as notFull,
        sum(case when {$table}.buyTotalMoney = {$table}.money and {$table}.status not in (0, 20, 600, 610, 620) then 1 else 0 end) as full, sum({$table}.money) as totalMoney, count(distinct {$table}.uid) as countUid,
        sum(case when {$table}.status in (500, 1000, 2000) then {$table}.money else 0 end) as drawMoney, sum({$table}.orderBonus) as bonus, sum({$table}.orderMargin) as margin
        from {$table} left join {$this->cp_user} on {$table}.uid = {$this->cp_user}.uid {$where}";
        $count = $this->BcdDb->query($sql)->getRow();
        return array(
            'data'  => $res,
            'count' => $count,
        );
    }
    
    /**
     * 更新置顶状态
     * @param int $id
     * @param int $top
     * @return array
     */
    public function updateTop($id, $top, $judge)
    {
    	
    	$sql = "select case when isTop > 0 then 1 else 0 end as isTop, status, buyTotalMoney, money, endTime from cp_united_orders where id= ?";
    	$order = $this->master->query($sql, array($id))->getRow();
    	if ($top == 1)
    	{
    		if (in_array($order['status'], array('600', '610', '620')))
    		{
    			return array('status' => 'fail', 'message' => '该合买方案已撤单');
    		}
    		if ($order['status'] == 500)
    		{
    			if ($order['buyTotalMoney'] >= $order['money'])
    			{
    				return array('status' => 'fail', 'message' => '该合买方案已满员');
    			}
    		}
    		if ($order['status'] > 500)
    		{
    			return array('status' => 'fail', 'message' => '该合买方案已满员');
    		}
    		if (strtotime($order['endTime']) <= time())
    		{
    			return array('status' => 'fail', 'message' => '该合买方案已截止');
    		}
    	}
        
        if ($judge != 1)
        {
        	if ($order['isTop'] != $top) {
        		
        		$sql = "update cp_united_orders set `isTop`= ".($top == 1 ? "`isTop` | 2" : '0')." where id= ?";
        		$this->master->query($sql, array($id));
        		return array('status' => 'success', 'message' => '');
        	}else {
        		return array('status' => 'fail', 'message' => "无需".($top > 0 ? '置顶' : '取消置顶') );
        	}
        }
        return array('status' => 'success', 'message' => '');
    }

    /**
     * 获取合买订单
     * @param int $orderId
     * @return array
     */
    public function findByOrderId($orderId)
    {
        $sql = "SELECT u.*, d.introduction FROM cp_united_orders AS u LEFT JOIN cp_united_detail AS d ON u.orderId = d.orderId WHERE u.orderId = '{$orderId}'";
        $res = $this->BcdDb->query($sql)->getRow();
        return $res;
    }
    
    /**
     * 获取所有合买订单记录
     * @param int $orderId
     * @param string $username
     * @param int $page
     * @param int $num
     * @return array
     */
    public function getAllOrders($orderId, $username, $page, $num)
    {
        $sql = "select o.uid,o.status,o.userName,o.money,o.buyMoney,o.buyTotalMoney,o.subscribeId,o.margin,o.buyPlatform,o.created from cp_united_orders as o where o.orderId='{$orderId}'";
        $countSql="select count(o.id) as num from cp_united_orders as o where o.orderId='{$orderId}'";
        if ($username)
        {
            $sql.=" and o.userName like '%{$username}%'";
            $countSql.=" and o.userName like '%{$username}%'";
        }
        $allCount = $this->BcdDb->query($countSql)->getRow();
        $sql.=" order by o.created desc LIMIT " . ($page - 1) * $num . "," . $num;
        $res = $this->BcdDb->query($sql)->getAll();
        return array('data' => $res, 'count' => $allCount['num']);
    }

    /**
     * 取消订单
     * @param int $id
     * @return array
     */
    public function cancelOrder($id)
    {
        $sql = "select * from cp_united_orders where orderId='{$id}'";
        $order = $this->BcdDb->query($sql)->getRow();
        if (strtotime($order['endTime']) <= time())
        {
            return array('status' => 'fail', 'message' => '该合买方案已截止');
        }
        if(in_array($order['status'], array('610', '620', '600')))
        {
            return array('status' => 'fail', 'message' => '该合买方案已撤单');
        }
        if ($order['status'] == 1000 || $order['status'] == 2000)
        {
            return array('status' => 'fail', 'message' => '该合买方案已满员');
        }
        if (round(($order['buyTotalMoney'] + $order['guaranteeAmount']) / $order['money'] * 100) >= 95)
        {
            return array('status' => 'fail', 'message' => '进度+保底已超过95%');
        }
        return array('status' => 'success', 'message' => '');
    }
    
    /**
     * 获取多个合买订单
     * @param int $orderIds
     * @return array
     */
    public function findByOrderIds($orderIds)
    {
        $orderIds = implode(',', $orderIds);
        $sql = "select orderId,money,margin from cp_united_orders where orderId in ({$orderIds})";
        $res = $this->BcdDb->query($sql)->getAll();
        return $res;
    }

    // 合买宣言列表
    public function list_intro($searchData, $page, $pageCount)
    {
        // 默认展示安卓已支付订单
        $where = " WHERE 1 AND o.buyPlatform = 1 AND o.status >= 40 "; 
        if($searchData['name'] !== FALSE && !empty($searchData['name'])) 
        {
            $where .= "AND u.uname like '%{$searchData['name']}%' ";
        }
        if($searchData['orderId'] !== FALSE && !empty($searchData['orderId']))
        {
            $where .= "AND d.orderId = '{$searchData['orderId']}' ";
        }
        if($searchData['check_status'] !== FALSE && $searchData['check_status'] != '-1')
        {
            $where .= "AND d.check_status = {$searchData['check_status']} ";
        }
        if($searchData['number'])
        {
            $where .= "AND d.introduction regexp '[0-9]' ";
        }
        if($searchData['words'])
        {
            $where .= "AND d.introduction regexp '[a-zA-Z]' ";
        }
        if($searchData['chinesenumer'])
        {
            $where .= "AND d.introduction regexp '一|二|三|四|五|六|七|八|九|十' ";
        }
        if($searchData['start_time'])
        {
            $start_time = date("Y-m-d H:i:s", strtotime($searchData['start_time']));
            $end_time = date("Y-m-d H:i:s", strtotime($searchData['end_time']));
            $where .= "AND d.created >= '{$start_time}' AND d.created <= '{$end_time}'";
        }
        $sql = "SELECT d.orderId, d.uid, d.introduction, d.check_status, d.sensitives, d.created, d.delete_flag, u.uname FROM cp_united_detail AS d LEFT JOIN cp_united_orders AS o ON d.orderId = o.orderId LEFT JOIN cp_user AS u ON d.uid = u.uid" . $where . " ORDER BY d.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $lists = $this->BcdDb->query($sql)->getAll();
        $sql = "SELECT count(*) AS count FROM cp_united_detail AS d LEFT JOIN cp_united_orders AS o ON d.orderId = o.orderId LEFT JOIN cp_user AS u ON d.uid = u.uid" . $where;
        $count = $this->BcdDb->query($sql)->getRow();
        return array($lists, $count['count']);
    }

    // 删除
    public function deleteIntroduce($orderId)
    {
        $sql = "UPDATE cp_united_detail SET delete_flag = 1 WHERE orderId = ?";
        return $this->master->query($sql, array($orderId));
    }

    // 手动成功
    public function handleIntroduce($orderId)
    {
        $sql = "UPDATE cp_united_detail SET check_status = 1 WHERE orderId = ?";
        return $this->master->query($sql, array($orderId));
    }
}
