<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * APP 订单信息 模型层
 * @date:2015-04-17
 */

class Order_Model extends MY_Model 
{   
    public $tbname;
    public $status = array(
        'create_init' => 0, //订单初始化
        'create' => 10, //创建
        'out_of_date' => 20, //过期未付款
        'out_of_date_pay' => 21, //过期已付款
        'pay_fail' => 30, //付款失败，等待系统再付款
        'pay' => 40, //已付款
        'qualified' => 200, //未满足条件
        'drawing' => 240, //出票中
        'draw' => 500, //出票成功
        'concel' => 600, //出票失败
        'notwin' => 1000, //未中
        'win' => 2000, //中奖
    );

	public function __construct() 
	{
		parent::__construct();
		$this->tbname = 'cp_orders';
        $this->load->library('libcomm');
	}
    
    /*
     * 创建订单
     * @date:2015-04-21
     */
    public function SaveOrder($ctype, $datas)
    {

        switch ($ctype) 
        {
            case 'create':
                $datas['status'] = $this->status[$ctype];
                break;
            case 'pay':
                $datas['status'] = $this->status[$ctype];
                break; 
            case 'pay_fail':
                $datas['status'] = $this->status[$ctype];
                break;         
            default:
                # code...
                break;
        }

        $upfields = array('status', 'bonus', 'margin', 'eachAmount', 'channel', 'codecc', 'qsFlag', 'ticket_time', 'win_time', 'trade_no', 'mark');
        $fields = array_keys($datas);
        $sql = "insert {$this->tbname}(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())" . 
        $this->onduplicate($fields, $upfields);
        try 
        {
            return $this->db->query($sql, $datas);  
        }
        catch (Exception $e)
        {
            log_message('LOG', "orderSave error2: " . json_encode($datas) , "ERROR");
            log_message('LOG', "orderSave error2: " . __CLASS__ . ':' . __LINE__ , "ERROR");
            return false;
        }
    }

    /*
     * APP 获得余额
     * @date:2015-04-21
     */
    public function getMoney($uid)
    {
        // var_dump($this->db);
        return $this->db->query('select money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
    }

    /*
     * APP 订单接口数据处理
     * @date:2015-04-21
     */
    public function _padBusiParams($data) 
    {
        $uarray = array('url', 'isToken', 'isJson', 'ctype', 'endTime', 'pay_pwd', 'codecc');
        $data['token'] = $data['uid'];
        if($data['ctype'] == 'create')
        {
            $data['userName'] = $data['uid'];
            $data['orderId'] = $this->tools->getIncNum('UNIQUE_KEY');
        }
        foreach ($uarray as $val)
        {
            if(array_key_exists($val, $data))
                unset($data[$val]);
        }
        return $data;
    }

    /*
     * APP 获取订单信息
     * @date:2015-04-21
     */
    public function getById($orderId)
    {
        return $this->db->query("select o.uid, o.userName, o.orderId, o.trade_no, o.buyPlatform, o.codes, o.lid, o.money, o.multi, o.issue, o.playType, o.isChase, o.betTnum, o.orderType, o.endTime, o.status, d.redpackId, d.redpackMoney 
        from {$this->tbname} as o left join cp_orders_detail as d on d.orderId = o.orderId where o.orderId = ?", array($orderId))->getRow();
    }
    
    //获得投注信息
    public function getOrders($cons, $cpage, $psize)
    {
    	$constr = " AND uid = ? AND (is_hide & 1) = 0";
        if(isset($cons['orderType']))
        {
            $constr .= " AND orderType = ?";
        }
    	if($cons['status'])
    	{
    		$constr .= " AND status = ?";
    	}
    	else
    	{
            //冠军彩、冠亚军彩未支付订单不显示
    		$constr .= " AND status NOT IN(0, 20) AND status >= 40 AND (lid not in (44, 45) or status <> 10)";
    	}

        if($cons['lid'])
        {
            $constr .= " AND lid = ?";
        }
        
        // 合买类型过滤
        $constr .= " AND orderType != 4";
    
    	$sql = "select orderId, lid, money, playType, issue, status, orderType, my_status, margin, endTime, created from {$this->tbname} force index(uid)
    	where 1 " . $constr . " order by created DESC limit " . ($cpage-1)*$psize . ", $psize";
    	$vdata['datas'] = $this->slave->query($sql, $cons)->getAll();
    	return $vdata;
    }
    
    // 根据单号获取订单
    public function getOrder( $cons )
    {
    	$date = date('Y-m-d H:i:s', strtotime(substr($cons['orderId'], 0, 14)));
        $tableSuffix = $this->tools->getTableSuffixByDate($date);
        if($tableSuffix && $tableSuffix < '2014')
        {
            return array('data' => array());
        }
        if($tableSuffix) $tableSuffix = '_' . $tableSuffix;     
        $sql = "SELECT o.orderId, o.codes, o.codecc, o.lid, o.money, o.multi, o.issue, o.playType, o.isChase, o.orderType, o.betTnum, o.status, o.my_status, o.bonus, o.margin, o.shopId, o.failMoney, o.activity_ids, o.activity_status, o.pay_time, o.created,o.buyPlatform,o.forecastBonus,d.redpackId,d.redpackMoney 
                FROM {$this->tbname}{$tableSuffix} o
                LEFT JOIN cp_orders_detail d ON d.orderId=o.orderId
                WHERE o.uid = ? AND o.orderId = ? and o.orderType in ('0', '1', '3', '6')";
        $order['data'] = $this->slave->query($sql, $cons)->getRow();
        return $order;
    }
    
    /*
     * 查询订单拆票详情（已废弃）
    * @author:liuli
    * @date:2015-02-04
    *
    * @param $uid 用户ID
    * @param $orderId 订单号
    * @return array
    */
    public function getOrderDetail($uid, $orderId)
    {
    	$orders = array();
    
    	$PostData['JSON'] = array(
    			'token' => $uid,
    			'uid' => $uid,
    			'orderId' => $orderId,
    	);
    	$orderResponse = $this->tools->request($this->config->item('busi_api').'2345/ticket/v1/order/ticket', $PostData);
    
    	$orderResponse = json_decode($orderResponse,true);
    	if ($orderResponse['code'] == 0) {
    		$orders = $orderResponse['data'];
    	}
    
    	return $orders;
    }

    public function getJjcMatchDetail($lid, $codecc)
    {
        $data = array();
        $paiqiTable = '';
        if($lid == 42)
        {
            $paiqiTable = 'cp_jczq_paiqi';
            $matchTable = 'cp_data_zq_matchs';
            $fields = "m_date, mname, mid, league, home, away, full_score, half_score, rq, end_sale_time, m_status, cstate";
        }
        elseif($lid == 43)
        {
            $paiqiTable = 'cp_jclq_paiqi';
            $matchTable = 'cp_data_lq_matchs';
            $fields = "m_date, mname, preScore, mid, league, home, away, full_score, rq, begin_time, m_status, cstate";
        }
        if($paiqiTable && $codecc)
        {
            $midArr = explode(' ', $codecc);
            $mids = implode("','", $midArr);
            $matchs = $this->slaveCfg1->query("select {$fields} from {$paiqiTable} where mid in ('{$mids}')")->getAll();

            // 查询指定场次致胜xid
            $xidData = $this->getMatchXid($matchTable, $midArr);

            foreach ($matchs as $val)
            {
                $match = array();
                $match['issue'] = $val['m_date'];
                $match['mid'] = $val['mid'];
                $match['jcMid'] = $xidData[$val['mid']] ? $xidData[$val['mid']] : 0;
                $match['name'] = $val['league'];
                $match['home'] = $val['home'];
                $match['awary'] = $val['away'];
                $match['score'] = $val['full_score'];
                $match['scoreHalf'] = $val['half_score'];
                $match['let'] = $val['rq'];
                $match['dt'] = isset($val['end_sale_time']) ? strtotime($val['end_sale_time'])*1000 : strtotime($val['begin_time'])*1000;
                $match['m_status'] = $val['m_status'];
                $match['cstate'] = $val['cstate'];
                array_push($data, $match);
            }
        }
        return $data;
    }

    /**
     * 出票详情
     */
    public function getJjcOrderDetail($orderId)
    {
        $data = array();
        if(!$orderId)
        {
            return $data;
        }
        $tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
        $suffix = '';
        if($tableSuffix)
        {
            $suffix = '_' . $tableSuffix;
        }
        $sql = "SELECT s.lid,s.sub_order_id,s.subCodeId,s.codes,s.money,s.playType,s.multi,s.betTnum,s.status,s.bonus,s.margin,s.ticket_time,r.mid,r.pdetail,r.odds
                FROM cp_orders_split{$suffix} s JOIN cp_orders_relation{$suffix} r ON s.sub_order_id=r.sub_order_id WHERE s.orderId=? 
        		order by case when s.status='600' then 1 else 0 end";
        $res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
        foreach ($res as $val)
        {
            $data[$val['sub_order_id']]['lid'] = $val['lid'];
            $data[$val['sub_order_id']]['subCodeId'] = $val['subCodeId'];
            $data[$val['sub_order_id']]['codes'] = $val['codes'];
            $data[$val['sub_order_id']]['money'] = $val['money'];
            $data[$val['sub_order_id']]['multi'] = $val['multi'];
            $data[$val['sub_order_id']]['betTnum'] = $val['betTnum'];
            $data[$val['sub_order_id']]['status'] = $val['status'];
            $data[$val['sub_order_id']]['bonus'] = $val['bonus'];
            $data[$val['sub_order_id']]['margin'] = $val['margin'];
            $data[$val['sub_order_id']]['playType'] = $val['playType'];
            $data[$val['sub_order_id']]['ticket_time'] = $val['ticket_time'];
            $data[$val['sub_order_id']]['odds'][$val['mid']] = $val['odds'];
            $data[$val['sub_order_id']]['info'][$val['mid']] = $val['pdetail'];
        }
        
        return $data;
    }
    
    /**
     * 出票详情
     */
    public function getNumOrderDetail($orderId, $lid)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$tables = $this->getSplitTable($lid);
    	$sql = "select sub_order_id, codes, betTnum, multi, status, bonus, bonus_detail, isChase, multi, playType, ticket_time from {$tables['split_table']}{$suffix} where orderId = ?
    	order by case when status='600' then 1 else 0 end";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	return $res;
    }
    
    /**
     * 返回加奖金额
     */
    public function getOtherBonus($orderId, $lid)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$tables = $this->getSplitTable($lid);
    	$sql = "select sum(otherBonus) from {$tables['split_table']}{$suffix} where orderId = ?";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getOne();
    	return $res;
    }
    
    /**
     * 出票详情
     */
    public function getSfcOrderDetail($orderId)
    {
    	$data = array();
    	if(!$orderId)
    	{
    		return $data;
    	}
    	$tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
    	$suffix = '';
    	if($tableSuffix)
    	{
    		$suffix = '_' . $tableSuffix;
    	}
    	$sql = "select sub_order_id, codes, betTnum, multi, status, bonus from cp_orders_split{$suffix} where orderId = ?
    	order by case when status='600' then 1 else 0 end";
    	$res = $this->slaveCfg1->query($sql, array($orderId))->getAll();
    	return $res;
    }

    /**
     * 数字彩开奖信息
     * @param unknown_type $lid
     * @param unknown_type $issue
     * @return multitype:|multitype:number unknown mixed
     */
    public function getNumIssue($lid, $issue)
    {
        $t_arr = array(
                Lottery_Model::PLS => array('lname' => 'pl3', 'issue' => $this->libcomm->format_issue($issue, 0)),
                Lottery_Model::PLW => array('lname' => 'pl5', 'issue' => $this->libcomm->format_issue($issue, 0)),
                Lottery_Model::SSQ => array('lname' => 'ssq', 'issue' => $issue),
                Lottery_Model::FCSD => array('lname' => 'fc3d', 'issue' => $issue),
                Lottery_Model::QXC => array('lname' => 'qxc', 'issue' => $this->libcomm->format_issue($issue, 0)),
                Lottery_Model::SYYDJ => array('lname' => 'syxw', 'issue' => $issue),
                Lottery_Model::QLC => array('lname' => 'qlc', 'issue' => $issue),
                Lottery_Model::DLT => array('lname' => 'dlt', 'issue' => $this->libcomm->format_issue($issue, 0)),
                Lottery_Model::JXSYXW => array('lname' => 'jxsyxw', 'issue' => $issue),
                Lottery_Model::HBSYXW => array('lname' => 'hbsyxw', 'issue' => $issue),
        		Lottery_Model::KS => array('lname' => 'ks', 'issue' => $issue),
        		Lottery_Model::KLPK => array('lname' => 'klpk', 'issue' => $issue),
        		Lottery_Model::CQSSC => array('lname' => 'cqssc', 'issue' => $issue),
        		Lottery_Model::JLKS => array('lname' => 'jlks', 'issue' => $issue),
                Lottery_Model::JXKS => array('lname' => 'jxks', 'issue' => $issue),
                Lottery_Model::GDSYXW => array('lname' => 'gdsyxw', 'issue' => $issue),
        );
        $data = array();
        if(!isset($t_arr[$lid]['lname']) || empty($issue))
        {
            return $data;
        }
         
        $sql = "select * from cp_{$t_arr[$lid]['lname']}_paiqi where issue = ?";
        $res = $this->slaveCfg1->query($sql, array($t_arr[$lid]['issue']))->getRow();
        if($res)
        {
            $data['seExpect'] = $issue;
            $data['awardNumber'] = str_replace(array('|', '(', ')'), array(':', ':', ''), $res['awardNum']);
            $data['bonusDetail'] = json_decode($res['bonusDetail'], true);
            $data['seLotid'] = $lid;
            $data['seEndtime'] = strtotime($res['end_time'])*1000;
            $data['awardTime'] = strtotime($res['award_time'])*1000;
        }
         
        return $data;
    }

    /**
     * 胜负彩、任九开奖详情
     * @param unknown_type $mid
     */
    public function getSfcAward($mid)
    {
        $data = array();
        if(empty($mid))
        {
            return $data;
        }
        
        $mid = $this->libcomm->format_issue($mid, 0);
        $sql = "select result from cp_rsfc_paiqi where mid = ?";
        $res = $this->slaveCfg1->query($sql, array($mid))->getRow();
        if($res)
        {
            $data['seExpect'] = $mid;
            $data['awardNumber'] = $res['result'];
        }
         
        return $data;
    }

    /**
     * 胜负彩、任九对阵信息
     * @param unknown_type $mid
     * @return multitype:
     */
    public function getSfcMatchs($mid)
    {
        $data = array();
        if(empty($mid))
        {
            return $data;
        }
        $mid = $this->libcomm->format_issue($mid, 0);
        $sql = "select * from cp_tczq_paiqi where mid = ? and ctype=1";
        $res = $this->slaveCfg1->query($sql, array($mid))->getAll();
        foreach ($res as $val)
        {
            $match = array();
            $match['gameName'] = $val['league'];
            $match['issueId'] = $mid;
            $match['teamName1'] = $val['home'];
            $match['teamName2'] = $val['away'];
            $match['gameTime'] = strtotime($val['begin_date'])*1000;
            array_push($data, $match);
        }
        
        return $data;
    }
    /**
     * 订单加奖信息
     */
    public function getGjOrderDetail($order) 
    {
        $data = array();
        if(!$order)
        {
            return $data;
        }
        $tableSuffix = $this->tools->getTableSuffixByOrder($order);
        $suffix = '';
        if($tableSuffix)
        {
            $suffix = '_' . $tableSuffix;
        }
        $sql = "select s.lid,s.sub_order_id,s.subCodeId,s.codes,s.money,s.multi,s.betTnum,s.status,s.bonus,s.margin,s.ticket_time,r.mid,r.pdetail,r.odds 
        from cp_orders_split{$suffix} s JOIN cp_orders_relation{$suffix} r ON s.sub_order_id=r.sub_order_id where s.orderId = ? 
        order by case when s.status='600' then 1 else 0 end";
        $res = $this->slaveCfg1->query($sql, array($order))->getAll();
        return $res;
    }

    public function getGjDetail($teams, $lid) 
    {
        switch ($lid) {
            case Lottery_Model::GJ:
                $type = 1;
                break;
            case Lottery_Model::GYJ:
                $type = 2;
                break;
        }
        //$this->dcDB = $this->load->database('dc', true);
        $sql = "select name, status, mid from cp_champion_paiqi where type = ? and mid in (".implode(',', $teams).")";
        return $this->slaveDc->query($sql, array($type))->getAll();
    }
    /**
     * 订单加奖信息
     */
    public function getJjDetail($orderId)
    {
        return $this->db->query('SELECT jj_id, orderId, add_money FROM cp_activity_jj_order WHERE orderId = ?', array($orderId))->getRow();
    }
    
    public function getAllOrder($cons, $limit)
    {
        $sql2 = $sql3 = $sql4 = $sql5 = $sql6 = '';
		
        $con = "";
		if ($cons['lid']) $con .= " and #TABLE#lid='{$cons['lid']}'";
        if (isset($cons['is_hide'])) $con .= " and (#TABLE#is_hide & 1) = 0";
		if( $cons['marginonly']) $con .= ' AND #TABLE#margin > 0'; // 未支付
        if( $cons['status']==500) $con .= ' AND #TABLE#status =500'; // 未支付
		
		if (in_array($cons['buyType'], array(0, 4)) && empty($cons['nopay'])) {
			$sql2 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created`, `is_hide` )
					SELECT orderId, lid, 0, money, buyTotalMoney, buyMoney, actualguranteeAmount, '', 41, '', isChase, issue, `STATUS`, my_status, margin, endTime, 0, created, is_hide FROM bn_cpiao.cp_united_orders
					WHERE `status` not in (0, 20) and uid = '{$cons['uid']}' and created >= '{$cons['start']}' and created <= '{$cons['end']}'";
			$sql2 .= str_replace('#TABLE#', '', $con);
		}
		
		if (in_array($cons['buyType'], array(0, 5)) && empty($cons['nopay'])) {
			$sql3 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created`, `subOrderType`, `is_hide` )
					SELECT uj.orderId, uj.lid, u.uid, 0, 0, uj.buyMoney, 0, u.uname, 42, '', 0, uj.issue, uj.status, uj.my_status, uj.margin, '', 0, uj.created, uj.subOrderType, uj.is_hide FROM bn_cpiao.cp_united_join as uj left join bn_cpiao.cp_user as u on uj.puid=u.uid
					WHERE uj.`status` not in (0, 20) and orderType=2 and uj.uid = '{$cons['uid']}' and uj.created >= '{$cons['start']}' and uj.created <= '{$cons['end']}'";
			$sql3 .= str_replace('#TABLE#', '', $con);
		}
		
		if (in_array($cons['buyType'], array(0, 1, 2, 3))) {
			$sql4 = "INSERT cp_get_orders_tmp ( `orderId`, `lid`, `uid`, `money`, `buyTotalMoney`, `buyMoney`, `guarantee`, `nick_name`, `orderType`, `playType`, `isChase`, `issue`, `status`, `my_status`, `margin`, `endTime`, `add_money`, `created`, `is_hide` )
			SELECT o.orderId, o.lid, o.uid, o.money, 0 as buyTotalMoney, money as buyMoney, 0 as guarantee, '' as nick_name, o.orderType, o.playType, o.isChase, o.issue, o.STATUS, o.my_status, o.margin, o.endTime, ajo.add_money, o.created, o.is_hide
			FROM bn_cpiao.cp_orders as o
			LEFT JOIN bn_cpiao.cp_activity_jj_order AS ajo ON o.orderId = ajo.orderId
			WHERE o.status not in(0, 20) and o.orderType <> 4 and o.uid = '{$cons['uid']}' and o.created >= '{$cons['start']}' and o.created <= '{$cons['end']}' and (o.is_hide & 2) = 0".str_replace('#TABLE#', 'o.', $con);
                        if( $cons['nopay']){
                            $sql4 .= ' AND o.status = 10'; // 未支付
                        }else{
                            $sql4 .= ' AND o.status <> 10';
                        }
			if ($cons['buyType']) {
				if (in_array($cons['buyType'], array(1, 2))) {
					$sql4 .= " and o.orderType = ".($cons['buyType']-1);
				}else {
					$sql4 .= " and o.orderType in (3, 6) ";
				}
			}
			$startSuffix = $this->tools->getTableSuffixByDate($cons['start']);
			if ($startSuffix) {
				$sql5 = str_replace('cp_orders', 'cp_orders_'.$startSuffix, $sql4);
				if ($startSuffix !== date('Y')) $sql6 = str_replace('cp_orders', 'cp_orders_'.($startSuffix+1), $sql4);
			}
		}

		$sql0 = 'select * from cp_get_orders_tmp order by created desc limit '.$limit;
		$sql1 = "select count(*) total, sum(if((status in({$this->status['concel']}, 610, 620, {$this->status['notwin']}, {$this->status['out_of_date']}, {$this->status['out_of_date_pay']}) || (status = {$this->status['win']} && my_status in(1, 3, 4, 5))), 0, 1)) notover, sum(buyMoney) money, sum(if(status in({$this->status['win']}), margin, 0)) prize
		from cp_get_orders_tmp into @total, @notover, @money, @prize";
		
		$this->slave->query("set @total = 0, @notover = 0, @money = 0, @prize = 0");
		$res = $this->slave->query("call bn_cpiao_tmp.cp_get_orders(\"{$sql0}\", \"{$sql1}\", \"{$sql2}\", \"{$sql3}\", \"{$sql4}\", \"{$sql5}\", \"{$sql6}\", @total, @notover, @money, @prize)")->getAll();
		$total = array();
		if ($res) $total = $this->slave->query('select @total as total, @notover as notover, @money as money, @prize as prize')->getRow();
		
		return array('totals' => $total, 'datas' => $res);
    }

    //保存订单详情记录
    public function insertOrderDetail($data)
    {
        $fields = array_keys($data);
        $upfields = array('redpackId', 'redpackMoney');
        $sql = "insert into cp_orders_detail(" . implode(',', $fields) . ", created)values(" .
        implode(',', array_map(array($this, 'maps'), $fields)) . ", now())" . $this->onduplicate($fields, $upfields);
        return $this->db->query($sql, $data);
    }

    public function getMatchXid($matchTable, $midArr)
    {
        $xidData = array();
        $mids = array();
        if(!empty($midArr))
        {
            foreach ($midArr as $mid) 
            {
                $mids[] = (strlen($mid) >= 10) ? substr($mid, 2) : $mid;
            }
        }    
        $mids = implode("','", $mids);
        $matchData = $this->slaveDc->query("select xid, mid from {$matchTable} where xid in ('{$mids}')")->getAll();

        if(!empty($matchData))
        {
            foreach ($matchData as $item) 
            {
                $xid = '20' . $item['xid'];
                $xidData[$xid] = $item['mid'];
            }
        }

        return $xidData;
    }

    // 乐善奖奖金
    public function getLsDetail($orderId, $lid)
    {
        $tableSuffix = $this->tools->getTableSuffixByOrder($orderId);
        $suffix = '';
        if($tableSuffix)
        {
            $suffix = '_' . $tableSuffix;
        }
        $tables = $this->getSplitTable($lid);
        $sql = "SELECT s.lid, s.sub_order_id, s.codes, s.betTnum, s.multi, s.status, s.bonus, s.otherBonus, s.bonus_detail, s.isChase, s.multi, s.playType, s.ticket_time, d.awardNum, d.bonus_detail, d.margin FROM {$tables['split_table']}{$suffix} AS s LEFT JOIN cp_orders_split_detail AS d ON s.sub_order_id = d.sub_order_id WHERE s.orderId = ?";
        return $this->slaveCfg1->query($sql, array($orderId))->getAll();
    }
}
