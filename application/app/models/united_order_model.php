<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * 合买 - 订单 - 模型层
 */
class United_Order_Model extends MY_Model
{
    // 合买订单状态
    public $status = array(
        'create' => 0, //订单创建
        'out_of_date' => 20, //过期未付款
        'pay' => 40, //已付款
        'drawing' => 240, //出票中
        'draw' => 500, //出票成功
        'concel' => 600, //出票失败
        'revoke_by_user' => 610, //发起人撤单
        'revoke_by_system' => 620, //未满员撤单
        'notwin' => 1000, //未中
        'win' => 2000, //中奖
    );
    public function __construct()
    {
        parent::__construct();
        $this->load->library('BetCnName');
    }

    public function getStatus()
    {
        return $this->status;
    }

    // 查询认购合买订单信息
    public function getUnitedBuyOrders($orderId)
    {
        $sql = 'SELECT orderId, trade_no, subscribeId, lid, issue, money, uid, puid, buyMoney, status, buyPlatform, orderType, margin, my_status, cstate FROM cp_united_join WHERE orderId = ? ORDER BY created ASC';
        $info = $this->slave->query($sql, array($orderId))->getAll();

        return $info;
    }

    public function getUniteOrderByOrderId($orderId, $fields = null, $where = null)
    {
        if (empty($fields)) {
            $fields = 'orderId, trade_no, uid, lid, unix_timestamp(endTime) as endTime, popularity, buyTotalMoney, money, (buyMoney+guaranteeAmount)/money as qb, openEndtime, guaranteeAmount, orderMargin, commissionRate, 
            		commission, issue, openStatus, ForecastBonusv, `status`, my_status, created, orderBonus, isChase, shopId, isTop, playType, buyPlatform';
        }
        $sql = "select {$fields}
				from cp_united_orders 
				where orderId = ? ";
        if ($where) {
            $sql .= $where;
        }

        return $this->db->query($sql, array('orderId' => $orderId))->getRow();
    }

    public function getJoin($orderId, $cons, $fields, $multi = false, $order = null, $limit = null)
    {
        $sql = 'FROM cp_united_join as o';

        if (isset($cons['userName'])) {
            $fields .= ', ui.uname';
            $sql .= ' left join cp_user as ui on o.uid=ui.uid';
            unset($cons['userName']);
        }
        $sql .= ' WHERE o.orderId = ?';
        foreach ($cons as $k => $con) {
            if (is_array($con)) {
                $con = implode(',', $con);
                $sql .= ' and '.$k." in ({$con})";
                unset($cons[$k]);
            } else {
                $sql .= ' and '.$k.' = ?';
            }
        }
        array_unshift($cons, $orderId);
        $sql = "SELECT {$fields} ".$sql;
        if ($multi) {
            if ($order) {
                $sql .= ' order by '.$order;
            }
            if ($limit) {
                $sql .= ' limit '.$limit;
            }

            return $this->slave->query($sql, $cons)->getAll();
        }

        return $this->slave->query($sql, $cons)->getRow();
    }

    public function getOrder($cons, $fields = null, $multi = false, $order = null, $limit = null)
    {
        if (empty($fields)) {
            $fields = 'o.orderId, o.uid, o.lid, o.popularity, o.buyTotalMoney, o.money, o.guaranteeAmount, o.orderMargin, o.issue, o.openStatus, o.status, o.created, o.orderBonus, o.isChase, o.shopId, o.isTop';
        }
        $sql = 'FROM cp_united_orders as o where 1';
        if (isset($cons['continue'])) {
            $now = date('Y-m-d H:i:s', time());
            $sql .= " and o.money-o.buyTotalMoney>0 and o.endTime>'{$now}'";
            unset($cons['continue']);
        }
        if (isset($cons['time'])) {
            $sql .= " and o.created>='{$cons['time']}'";
            unset($cons['time']);
        }
        foreach ($cons as $k => $con) {
            if (is_array($con)) {
                $con = implode(',', $con);
                $sql .= ' and '.$k." in ({$con})";
            } else {
                $sql .= ' and '.$k." = '{$con}'";
            }
        }
        $sql = "SELECT {$fields} ".$sql;
        if ($multi) {
            if ($order) {
                $sql .= ' order by '.$order;
            }
            if ($limit) {
                $sql .= ' limit '.$limit;
            }

            return $this->slave->query($sql)->getAll();
        }

        return $this->slave->query($sql)->getRow();
    }

    public function getOrderInfo($orderId)
    {
        $date = date('Y-m-d H:i:s', strtotime(substr($orderId, 0, 14)));
        $tableSuffix = $this->tools->getTableSuffixByDate($date);
        if ($tableSuffix) {
            $tableSuffix = '_'.$tableSuffix;
        }

        return $this->slave->query("select lid, codes, codecc, betTnum, multi, money, issue, status from cp_orders{$tableSuffix} where orderId = ?", array($orderId))->getRow();
    }

    public function getHotPlanner($lid = 0)
    {
        return $this->slave->query('SELECT uid, isOrdering, monthBonus, bonus/money as hbl FROM cp_united_planner WHERE isHot = 1 and lid = ? order by hbl desc, allTimes desc, money desc limit 10', array($lid))->getAll();
    }
    
    public function getPoints($uid, $lid)
    {
    	if ($lid == 35) $lid = 33;
    	if ($lid == 19) $lid = 11;
    	return $this->slave->query("select united_points from cp_united_planner where lid = ? and uid = ?", array($lid, $uid))->getCol();
    }

    //合买大厅
    public function getHmdtOrders($search, $limit, $order = '00', $uid = null)
    {
        $arr = array('state', 'money', 'commission', 'lid', 'guaranteeAmount');
        foreach ($arr as $v) 
        {
            if (!isset($search[$v])) 
            {
                $search[$v] = 0;
            }
        }
        if (!isset($search['openStatus'])) 
        {
            $search['openStatus'] = 99;
        }

        $stateConArr = array(0 => ' AND uo.buyTotalMoney < uo.money and endTime > NOW() AND uo.status not in (0, 20, 600, 610, 620)', 1 => ' AND uo.money = uo.buyTotalMoney AND STATUS NOT IN (0, 20, 600, 610, 620)', 2 => ' AND uo.status in (600, 610, 620)');
        $orderConArr = array('00'=>" (jd + bd) DESC", '01'=>" (jd + bd) ASC", '10' => ' popularity DESC', '11' => ' popularity ASC', '20' => ' up.united_points DESC', '21' => ' up.united_points ASC', '30' => ' money DESC', '31' => ' money ASC', '40' => ' endTime DESC', '41' => ' endTime ASC');
        $moneyConArr = array(1 => ' AND uo.money <= 10000', 2 => ' AND uo.money > 10000 AND uo.money <= 50000', 3 => ' AND uo.money > 50000 AND uo.money <= 100000', 4 => ' AND uo.money > 100000');

        $fields = 'uo.uid, uo.orderId, uo.lid, uo.popularity, uo.money, uo.buyTotalMoney, truncate(uo.buyTotalMoney / uo.money, 2) as jd , truncate(uo.guaranteeAmount/uo.money, 2) as bd, up.united_points';
        if ($uid) $fields .= " ,case when (SELECT id FROM cp_united_join WHERE orderId=uo.orderId AND uid='".$uid."' LIMIT 1) is not null then 1 else 0 end as ujoin";
        $sql = ' FROM cp_united_orders as uo join cp_united_planner as up ON uo.uid=up.uid and (up.lid = uo.lid OR (up.lid=11 AND uo.lid=19) OR (up.lid=33 AND uo.lid=35))';
        $where = ' WHERE uo.created > date_sub(now(), interval 10 day) '.$stateConArr[$search['state']];

        if (!empty($search['uname'])) 
        {
        	$sql .= " join cp_user as u on uo.uid=u.uid";
            $where .= " and u.uname like '%".$search['uname']."%'";
        }
        if ($search['money']) 
        {
            $where .= $moneyConArr[$search['money']];
        }
        if ($search['commission'] == 1) {
            $where .= ' AND uo.commissionRate >0 ';
        }
        if ($search['commission'] == 2) {
            $where .= ' AND uo.commissionRate =0 ';
        }
        if ($search['guaranteeAmount'] == 1) {
            $where .= ' AND uo.guaranteeAmount >0 ';
        }
        if ($search['guaranteeAmount'] == 2) {
            $where .= ' AND uo.guaranteeAmount =0 ';
        }
        if ($search['openStatus'] != 99) {
            $where .= " AND uo.openStatus = '".$search['openStatus']."'";
        }
        if ($search['lid']) {
            if ($search['lid'] == 33) {
                $where .= ' AND uo.lid in (33, 35)';
            } elseif ($search['lid'] == 11) {
                $where .= ' AND uo.lid in (11, 19)';
            } else {
                $where .= ' AND uo.lid = '.$search['lid'];
            }
            $fields .= ', uo.isTop & 1 as isTop';
        }else {
        	$fields .= ', case when uo.isTop & 2 > 0 then 1 else 0 end as isTop';
        }
        if ($search['issue']) {
            $where .= " AND uo.issue = '".$search['issue']."'";
        }

        if (empty($search['uname'])) {
            $sqlcount = 'select count(*) FROM cp_united_orders as uo'.$where;
        } else {
            $sqlcount = 'select count(*) '.$sql.$where;
        }
        $time = date("Y-m-d H:i:s",time()-10);
        $where.= " and (uo.follow_cstate=1 or uo.pay_time<='{$time}')";
        return array('num' => $this->slave->query($sqlcount)->getCol(), 'data' => $this->slave->query('select '.$fields.$sql.$where.' ORDER BY '.($search['state'] == 0 ? 'isTop desc, ' : '').$orderConArr[$order].', uo.created DESC limit '.$limit)->getAll());
    }

    public function getSfcIssues($num = 2)
    {
        return $this->cfgDB->query("select distinct CONCAT('20',mid) from cp_tczq_paiqi where end_sale_time < NOW() order by end_sale_time desc limit ".$num)->getCol();
    }

    public function getBetstationByShopid($shopId)
    {
        return $this->slave->query('select cname, address from cp_partner_shop where id = ?', array($shopId))->getRow();
    }
    
     /**
     * 取消订单
     * @param int $id
     * @return array
     */
    public function cancelOrder($id)
    {
        $sql = "select * from cp_united_orders where orderId='{$id}'";
        $order = $this->slave->query($sql)->getRow();
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
        if (round(($order['buyTotalMoney']) / $order['money'] * 100) == 100)
        {
            return array('status' => 'fail', 'message' => '该合买方案已满员');
        }
        if (round(($order['buyTotalMoney'] + $order['guaranteeAmount']) / $order['money'] * 100) >= 50)
        {
            return array('status' => 'fail', 'message' => '该合买方案撤单条件不满足');
        }
        return array('status' => 'success', 'message' => '');
    }

    public function getUnitedUser($uname)
    {
        $sql = "SELECT p.uid FROM cp_united_planner AS p LEFT JOIN cp_user AS u ON p.uid = u.uid WHERE p.lid = 0 AND u.uname = ?";
        $userData = $this->slave->query($sql, array($uname))->getRow();
        return $userData;
    }
    
    public function checkParams($orderInfo)
    {
    	if(in_array($orderInfo['lid'], array('33', '35')))
		{
			$orderInfo['lid'] = '33';
		}
		if(in_array($orderInfo['lid'], array('11', '19')))
		{
			$orderInfo['lid'] = '11';
		}
		return $orderInfo;
    }   

    public function countJoinOrders($uid, $orderId, $is_hide = 0)
    {
        $con = '';
        if($is_hide) $con .= ' and (is_hide & 1) = 0';
        $sql = "SELECT count(*) FROM cp_united_join WHERE orderId = ? AND uid = ?{$con}";
        return $this->slave->query($sql, array($orderId, $uid))->getOne();
    }

    public function hideUnitedOrder($puid, $uid, $orderId)
    {
        if($puid == $uid)
        {
            $sql = "UPDATE cp_united_orders SET is_hide = (is_hide | 1) WHERE orderId = ? AND uid = ? AND status > {$this->status['draw']}";
            $this->db->query($sql, array($orderId, $uid));
        }

        // 更新所有认购记录
        $sql = "UPDATE cp_united_join SET is_hide = (is_hide | 1) WHERE orderId = ? AND uid = ?";
        return $this->db->query($sql, array($orderId, $uid));
    } 

    // 合买订单宣言
    public function getUnitedIntro($orderId)
    {
        $sql = "SELECT orderId, uid, introduction, check_status, delete_flag FROM cp_united_detail WHERE orderId = ?";
        return $this->slave->query($sql, array($orderId))->getRow();
    }
}
