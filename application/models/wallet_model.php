<?php if ( ! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

class Wallet_Model extends MY_Model
{
    public $tbname;
    public $ctype = array(
        'recharge'                   => 0, //充值-添加预付款
        'pay'                        => 1, //付款-购买彩票
        'reward'                     => 2, //奖金-奖金派送
        'drawback'                   => 3, //订单退款-订单失败返款
        'apply_for_withdraw'         => 4, //提款
        'apply_for_withdraw_succ'    => 5, //提款成功解除冻结预付款(已废弃)
        'withdraw_succ'              => 6, //申请提款成功-扣除预付款成功(已废弃)
        'apply_for_withdraw_conceal' => 7, //申请提款撤销(已废弃)
        'apply_for_withdraw_fail'    => 8, //提款失败还款
        'dispatch'                   => 9, //系统奖金派送
        'addition'                   => 10, //其他应收款项
        'transfer'                   => 11, //转帐
    	'rebate'                     => 14, //联盟返点
        'united_refund'              => 15  //合买返还预付款
    );
    public $status = array(
        'withdraw_ini'     => 0, //提款申请状态
        'withdraw_lock'    => 1, //提款锁定后台处理中(已废弃)
        'withdraw_over'    => 2, //处理结束 
        'withdraw_concel'  => 3, //申请取消(已废弃)
        'withdraw_fail'    => 4, //财务打款失败
        'withdraw_operate' => 5, //已操作打款
    );

    public function __construct()
    {
        parent::__construct();
        $this->tbname = 'cp_wallet_logs';
        $this->load->library('tools');
    }
    
	private function getLotteryConfig()
	{
		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $lotteryConfig = $this->cache->get($REDIS['LOTTERY_CONFIG']);
        return json_decode($lotteryConfig, true);
	}

    public function InitSalt()
    {
        return $this->db->query('UPDATE cp_secret SET content = "" WHERE cid = 1');
    }

    //获得钱包密钥
    public function GetSalt()
    {
        $redata = $this->db->query('SELECT content FROM cp_secret WHERE cid = 1')->getOne();
        if ( ! empty($redata))
        {
            $redata = unserialize($redata);

            return $redata['new_token'];
        }
    }

    //保存充值记录
    public function SaveOrder($data)
    {
        $trade_check = $this->db->query("select count(*) from {$this->tbname} where trade_no = ? ",
            array($data['trade_no']))->getOne();
        $re = FALSE;
        if ($trade_check < 1)
        {
            $this->db->trans_start();
            $wallet = $this->getMoney($data['uid']);
            $data['umoney'] = $wallet['money'];
            $fields = array_keys($data);
            $sql = "insert ignore {$this->tbname}(" . implode(',', $fields) . ", created)values(" .
                implode(',', array_map(array($this, 'maps'), $fields)) . ", now())";
            $re = $this->db->query($sql, $data);
            if ($re)
            {
                $this->db->trans_complete();
            }
            else
            {
                $this->db->trans_rollback();
            }
        }

        return $re;
    }

    //获得余额
    public function getMoney($uid)
    {
        return $this->db->query('SELECT money, blocked, must_cost, dispatch FROM cp_user WHERE uid = ? FOR UPDATE',
            array($uid))->getRow();
    }

    //获得可提资金
    public function getWithDraw($uid)
    {
    	$subtract = "if((must_cost + dispatch) > chaseMoney, (must_cost + dispatch - chaseMoney), 0)";
        return $this->db->query("SELECT if(money >= $subtract, money - $subtract, 0) FROM cp_user WHERE uid = ? FOR UPDATE",
            array($uid))->getOne();
    }

    //付款
    public function payMoney($uid, $cost, $orderId = 0, $tranc = TRUE)
    {
        if ($tranc)
        {
            $this->db->trans_start();
        }
        $cost = intval($cost);
        $money = $this->getMoney($uid);
        $return = array(
            'trade_no' => FALSE,
            'code'     => 0,
        );
        if ($money['money'] >= $cost)
        {
            if ( ! empty($orderId))
            {
            	$flag = true;
            	$lcfgs = $this->getLotteryConfig();
                $this->load->model('order_model');
                $order = $this->order_model->getById($orderId);
                $this->lids = array_flip($this->orderConfig('lidmap'));
                $sublids = array($this->lids['syxw'], $this->lids['jxsyxw'], $this->lids['ks'], $this->lids['jlks'], $this->lids['jxks'], $this->lids['hbsyxw'], $this->lids['klpk'], $this->lids['cqssc'], $this->lids['gdsyxw']);
            	$ahead = !in_array($order['lid'], $sublids) ? 
            	($lcfgs[$order['lid']]['ahead'] - (in_array($order['lid'], array($this->lids['jczq'], $this->lids['jclq'])) ? 1 : 5)) . " minute" : ($lcfgs[$order['lid']]['ahead'] * 60 - 0) . ' second';
            	if ($order['singleFlag']) {
            		$this->load->library('createcheck/BaseCheck', array(), 'check');
            		$return = $this->check->sigleEndtime($order['lid'], $order['endTime'], $order['betTnum']);
            		if ($return == false) $flag = false;
            	}
                if ($order['money'] != ($cost + $order['redpackMoney']) || $order['status'] != 10)
                {
                    $flag = false;
                    $return['code'] = 16;
                }
                elseif(strtotime($ahead, time()) >= strtotime($order['endTime']))
                {
                	$flag = false;
                	$return['code'] = 17;
                }
                //判断红包使用状态
                if($order['redpackId'] && $flag)
                {
                	$repack = $this->db->query("select id,uid from cp_redpack_log where id=? and status= '1' and delete_flag = '0' for update", array($order['redpackId']))->getRow();
                	if(!$repack)
                	{
                		$flag = false;
                		$return['code'] = 18;
                	}
                }
                if(!$flag)
                {
                	if ($tranc)
                    {
                        $this->db->trans_rollback();
                    }
                    return $return;
                }
            }
            else
            {
                if ($tranc)
                {
                    $this->db->trans_rollback();
                }
                $return['code'] = 16;

                return $return;
            }
            $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
            //计算其他余额花费
            $bmoney = ($money['must_cost'] + $money['dispatch']) - $cost;
            $must_cost = 0;
            if ($bmoney >= $money['must_cost'])
            {
                $dispatch = $cost;
            }
            elseif ($bmoney >= 0)
            {
                $dispatch = $money['dispatch'];
                $must_cost = abs($dispatch - $cost);
            }
            else
            {
                $dispatch = $money['dispatch'];
                $must_cost = $money['must_cost'];
            }

            $wallet_log = array(
                'uid'       => $uid,
                'money'     => $cost,
                'ctype'     => $this->ctype['pay'],
                'trade_no'  => $trade_no,
                'umoney'    => ($money['money'] - $cost),
                'must_cost' => $must_cost,
                'dispatch'  => $dispatch,
                'additions' => (empty($order['lid']) ? 0 : $order['lid']),
                'orderId'   => $orderId
            );
            // 入总账流水
            $this->load->model('capital_model');
            $re4 = true;
            if($order['redpackId'])
            {
            	$capitalRedpack = array('capitalId' => '2', 'ctype' => 'redpack', 'money' => $order['redpackMoney'], 'status' => 2);
            	$re3 = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false, $capitalRedpack);
            	$wallet_log['content'] = '订单金额' . number_format(ParseUnit($order['money'], 1), 2) . '元，' . '其中实付' . number_format(ParseUnit($cost, 1), 2) . '元，红包' . number_format(ParseUnit($order['redpackMoney'], 1), 2) . '元';
            	$re4 = $this->db->query("update cp_redpack_log set use_time = now(), status='2',orderId=? where id=?", array($orderId, $order['redpackId']));
            }
            else
            {
            	$re3 = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false);
            }
            $re1 = $this->db->query("insert {$this->tbname}(" . implode(',', array_keys($wallet_log)) . ', created)
			values(' . implode(',', array_map(array($this, 'maps'), $wallet_log)) . ', now())', $wallet_log);
            $re2 = $this->db->query("update cp_user set money = money - $cost,
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where money >= $cost and uid = ?", array($uid));
           	
			$re = $re1 && $re2 && $re3 && $re4;
            if ($re)
            {
                if ($tranc)
                {
                    $this->db->trans_complete();
                }
                $this->freshWallet($uid);
                $return['trade_no'] = $trade_no;

                return $return;
            }
            else
            {
                if ($tranc)
                {
                    $this->db->trans_rollback();
                }
                $return['code'] = 16;

                return $return;
            }
        }
        else
        {
            if ($tranc)
            {
                $this->db->trans_rollback();
            }
            $return['code'] = 12;

            return $return;
        }

    }

    //订单失败退款
    public function payFail($uid, $orderid, $tradeno)
    {
        $this->load->model('order_model');
        $order_table = $this->order_model->tbname;
        $trade_no = $this->tools->getIncNum('UNIQUE_KEY');
        $this->db->trans_start();

        $re1 = $this->db->query("update cp_user m
		join {$this->tbname} w on m.uid = w.uid 
		set m.money = m.money + w.money, m.must_cost = m.must_cost + w.must_cost,
		m.dispatch = m.dispatch + w.dispatch where w.trade_no = ?", array($tradeno));

        $re2 = $this->db->query("insert {$this->tbname}(uid, mark, money, must_cost, dispatch, ctype, additions, trade_no,
		orderId, umoney, created) select m.uid, '1', m.money, w.must_cost, w.dispatch, {$this->ctype['drawback']}, m.lid, 
		'$trade_no', m.orderId,	n.money, now() from $order_table m join cp_user n on m.uid = n.uid 
		join {$this->tbname} w on m.trade_no = w.trade_no
		where m.orderId = ?", array($orderid));
        $re = $re1 && $re2;
        if ($re)
        {
            $this->db->trans_complete();
            $this->load->model('user_model');
            $order = $this->order_model->getById($orderid);
            $this->load->library('BetCnName');
            $vdatas = array(
                '#MM#'  => date('m', $time),
                '#DD#'  => date('d', $time),
                '#HH#'  => date('H', $time),
                '#II#'  => date('i', $time),
                '#LID#' => BetCnName::getCnName($order['lid'])
            );
            $this->user_model->sendSms($uid, $vdatas, 'lottery_fail', null, '127.0.0.1', '193');

            // APP 消息推送
            $pushData = array(
                'type'      =>  'lottery_fail',
                'uid'       =>  $user['uid'],
                'lid'       =>  $order['lid'],
                'lname'     =>  BetCnName::getCnName($order['lid']),
                'orderId'   =>  $orderid,
            );
            $this->load->library('mipush');
            $this->mipush->index('user', $pushData);
        }
        else
        {
            $this->db->trans_rollback();
        }
        if ($re)
        {
            $this->freshWallet($uid);
        }

        return $re;
    }

    //充值
    public function recharge($tradeNo, $real_amount, $trans = true)
    {
        if ($real_amount <= 0)
        {
            return FALSE;
        }
        $re1 = true; $re2 = true; $re3 = true; $re4 = true; $re5 = true; $packRes1 = true; $packRes2 = true;
		if($trans) $this->db->trans_start();
        $real_amount = ParseUnit($real_amount);
       
        $re1 = $this->db->query("UPDATE {$this->tbname} m
            JOIN cp_user n ON m.uid = n.uid
			SET m.money = ?, m.umoney = m.money + n.money
			WHERE m.mark = '2' AND m.trade_no = ?", array($real_amount, $tradeNo));

        $walletRow = $this->db->query("SELECT uid, red_pack packIdStr, umoney, channel 
            FROM cp_wallet_logs
            WHERE trade_no = ? AND mark = '2'", array($tradeNo))->getRow();
        $packSum = 0;
        if (!empty($walletRow['packIdStr']))
        {
            /**
             * @see Red_Pack_Model::STATUS_ACTIVE
             */
            $redPacks = $this->db->query("SELECT rl.id, r.money
                FROM cp_redpack_log rl
                JOIN cp_redpack r ON rl.rid = r.id
                WHERE rl.id IN ({$walletRow['packIdStr']}) AND rl.`status` = 1
                    AND rl.valid_start <= now() AND rl.valid_end >= now()
                    AND rl.delete_flag = 0
                FOR UPDATE ")->getAll();
            $values = array();
            $uMoney = $walletRow['umoney'];
            $usePackIds = array();
            $capitalValues = array();
            foreach ($redPacks as $pack)
            {
                $usePackIds[] = $pack['id'];
                $uMoney += $pack['money'];
                $packSum += $pack['money'];
                $packTradeNo = $this->tools->getIncNum('UNIQUE_KEY');
                /**
                 * @see Red_Pack_Model::TYPE_RECHARGE
                 */
                $values[] = "({$walletRow['uid']}, {$pack['money']}, 9, '1', '$packTradeNo',
                    $uMoney, 2, {$walletRow['channel']}, '红包', now())";
                $capitalValues[] = "('2', '{$packTradeNo}', '9', '{$pack['money']}', '2', now())";
            }
            if (!empty($values))
            {
                $re2 = $this->db->query("INSERT cp_wallet_logs (uid, money, ctype,
                    mark, trade_no, umoney, status, channel, content, created)
                    VALUES " . implode(',', $values));
                $packRes1 = $this->db->query("INSERT cp_capital_log (capital_id, trade_no, ctype, money, status, created)
                    VALUES " . implode(',', $capitalValues));
                //$packRes2 = $this->db->query("update cp_capital set money = money - {$packSum} where id = 2");
                $packRes2 = true;
            }
            /**
             * @see Red_Pack_Model::STATUS_USED
             */
            if (count($usePackIds)) 
            {
                $re3 = $this->db->query("UPDATE cp_redpack_log
                    SET `status` = 2, use_time = now()
                    WHERE id IN (" . implode(',', $usePackIds) . ")");
            }
        }

        if (!empty($values))
        {
            $setCost = "m.must_cost = m.must_cost + n.money,
                m.dispatch = m.dispatch + $packSum";
        }
        else
        {
            $setCost = 'm.must_cost = m.must_cost + ceil(n.money * '.$this->config->item('txed').'/100)';
        }

        $re4 = $this->db->query("UPDATE cp_user m
            JOIN {$this->tbname} n ON m.uid = n.uid
            SET m.money = m.money + n.money + $packSum, n.mark = '1',
				$setCost,
				n.recharge_over_time = NOW()
			WHERE n.trade_no = ? AND n.mark = '2'", array($tradeNo));
		
        $wInfo = $this->db->query("SELECT uid, ctype, platform, status
            FROM {$this->tbname} WHERE trade_no = ?", array($tradeNo))->getRow();
        
        if ($wInfo['uid'])
        {
            $re5 = $this->freshWallet($wInfo['uid']);
        }
        $return = $re1 && $re2 && $re3 && $re4 && $re5 && $packRes1 && $packRes2;
        if($trans)
		{
			if($return)
			{
				$this->db->trans_complete();
			}
			else 
			{
				$this->db->trans_rollback();
				return false;
			}
		}	

        if ($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 1 && ($wInfo['platform'] == 0 || $wInfo['platform'] == 3 || $wInfo['platform'] == 1 || $wInfo['platform'] == 4))
        {
            $this->load->model('chase_wallet_model');
            $this->chase_wallet_model->autoPay($tradeNo, $trans);
        }
        elseif($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 3 && ($wInfo['platform'] == 0 || $wInfo['platform'] == 3 || $wInfo['platform'] == 1 || $wInfo['platform'] == 4))
        {
            $this->load->model('united_wallet_model');
            $this->united_wallet_model->autoPay($tradeNo, $trans);
        }
        elseif($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 4 && ($wInfo['platform'] == 0 || $wInfo['platform'] == 3 || $wInfo['platform'] == 1 || $wInfo['platform'] == 4))
        {
            $this->load->model('follow_wallet_model');
            $this->follow_wallet_model->autoPay($tradeNo, $trans);
        }        
        elseif ($wInfo['platform'] == 0 || $wInfo['platform'] == 3 || $wInfo['platform'] == 1 || $wInfo['platform'] == 4)
        {
            // 网站订单充值自动支付逻辑
            $this->autoPay($tradeNo, $trans);
        }

        return $return;
    }

    //查看手机所属地
    public function getArea($key)
    {
        $sql = "SELECT province FROM cp_phone_location_map WHERE phone = ?";

        return $this->slave->query($sql, array($key))->getOne();
    }

    // 取交易详情
    public function getTradeDetail($cons, $cpage, $psize)
    {
        $constr = "and (mark != '2' || (mark = '2' && additions in(1, 2, 3))) and uid = ? and created >= ? and created <= ? and (ctype <> 1 || status <> 1) and status != 5";
        switch ($cons['ctype'])
        {
            case 'all':
                unset($cons['ctype']);
                break;
            case 'income':
                $constr .= " AND mark = '1'";
                unset($cons['ctype']);
                break;
            case 'expand':
                $constr .= " AND mark = '0'";
                unset($cons['ctype']);
                break;
            default:
                $constr .= " AND ctype = ?";
                break;
        }
        $startSuffix = $this->tools->getTableSuffixByDate($cons['start']);
        $endSuffix = $this->tools->getTableSuffixByDate($cons['end']);
        $year = date('Y');
        $middleSuffix = (($startSuffix != $endSuffix) && ($startSuffix < $year)) ? '_' . $year : '';
        if ($startSuffix)
        {
            $startSuffix = '_' . $startSuffix;
        }
        if ($endSuffix)
        {
            $endSuffix = '_' . $endSuffix;
        }
        if ($startSuffix == $endSuffix)
        {
            $nsql = "select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 " . $constr;
            $vdata = $this->slave->query($nsql, $cons)->getRow();

            $sql = "select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income, cstate,
   			umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content
   			from {$this->tbname}{$startSuffix}
   			where 1 " . $constr .
                " order by created DESC, trade_no DESC limit " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $cons)->getAll();

            return $vdata;
        }
        elseif ($middleSuffix)
        {
            $newCons = $midCons = $cons;
            foreach ($midCons as $val)
            {
                array_push($newCons, $val);
            }
            foreach ($cons as $val)
            {
                array_push($newCons, $val);
            }
            $nsql = "SELECT SUM(total) total, SUM(income) income, SUM(umoney) umoney FROM (
   				select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 {$constr} 
   			UNION
   				select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$middleSuffix} where 1 {$constr}
   			UNION
   				select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$endSuffix} where 1 {$constr}
   			) tmp";
            $vdata = $this->slave->query($nsql, $newCons)->getRow();

            $sql = "SELECT * FROM (
   				select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, cstate, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content from {$this->tbname}{$startSuffix} where 1 {$constr} 
   			UNION
   				select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, cstate, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content from {$this->tbname}{$middleSuffix} where 1 {$constr}
   			UNION
   				select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, cstate, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content from {$this->tbname}{$endSuffix} where 1 {$constr}
   			) tmp WHERE 1 ORDER BY tmp.created desc, tmp.trade_no DESC LIMIT " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $newCons)->getAll();

            return $vdata;
        }
        else
        {
            $newCons = $cons;
            foreach ($cons as $val)
            {
                array_push($newCons, $val);
            }
            $nsql = "SELECT SUM(total) total, SUM(income) income, SUM(umoney) umoney FROM (select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 {$constr} UNION
   			select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$endSuffix} where 1 {$constr}) tmp";
            $vdata = $this->slave->query($nsql, $newCons)->getRow();

            $sql = "SELECT * FROM (select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income, cstate,
   			umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content from {$this->tbname}{$startSuffix} where 1 {$constr} UNION 
   			select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income, cstate,
   			umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate, content from {$this->tbname}{$endSuffix} where 1 {$constr}) tmp WHERE 1 ORDER BY tmp.created desc, tmp.trade_no DESC LIMIT " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $newCons)->getAll();

            return $vdata;
        }

    }

    // 取交易详情
    public function getRechargeDetail($cons, $cpage, $psize)
    {
        $constr = "and (mark != '2' || (mark = '2' && additions in(1, 2, 3))) and uid = ? and created >= ? and created <= ? AND ctype = 0";
        $startSuffix = $this->tools->getTableSuffixByDate($cons['start']);
        $endSuffix = $this->tools->getTableSuffixByDate($cons['end']);
        $year = date('Y');
        $middleSuffix = (($startSuffix != $endSuffix) && ($startSuffix < $year)) ? '_' . $year : '';
        if ($startSuffix)
        {
            $startSuffix = '_' . $startSuffix;
        }
        if ($endSuffix)
        {
            $endSuffix = '_' . $endSuffix;
        }
        if ($startSuffix == $endSuffix)
        {
            $nsql = "select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 " . $constr;

            $vdata = $this->slave->query($nsql, $cons)->getRow();

            $sql = "select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,
    		umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate
    		from {$this->tbname}{$startSuffix}
    		where 1 " . $constr .
                " order by created DESC limit " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $cons)->getAll();

            return $vdata;
        }
        elseif ($middleSuffix)
        {
            $newCons = $midCons = $cons;
            foreach ($midCons as $val)
            {
                array_push($newCons, $val);
            }
            foreach ($cons as $val)
            {
                array_push($newCons, $val);
            }
            $nsql = "SELECT SUM(total) total, SUM(income) income, SUM(umoney) umoney FROM (
    			select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 {$constr} 
    		UNION
    			select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$middleSuffix} where 1 {$constr}
    		UNION
    			select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$endSuffix} where 1 {$constr}
    		) tmp";
            $vdata = $this->slave->query($nsql, $newCons)->getRow();

            $sql = "SELECT * FROM (
    			select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate from {$this->tbname}{$startSuffix} where 1 {$constr} 
    		UNION
    			select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate from {$this->tbname}{$middleSuffix} where 1 {$constr}
    		UNION
    			select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate from {$this->tbname}{$endSuffix} where 1 {$constr}
    		) tmp WHERE 1 ORDER BY tmp.created desc LIMIT " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $newCons)->getAll();

            return $vdata;
        }
        else
        {
            $newCons = $cons;
            foreach ($cons as $val)
            {
                array_push($newCons, $val);
            }
            $nsql = "SELECT SUM(total) total, SUM(income) income, SUM(umoney) umoney FROM (select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$startSuffix} where 1 {$constr} UNION
    		select count(*) total, sum(if(mark = '1', money, 0)) income, sum(if(mark = '0', money, 0)) umoney from {$this->tbname}{$endSuffix} where 1 {$constr}) tmp";
            $vdata = $this->slave->query($nsql, $newCons)->getRow();

            $sql = "SELECT * FROM (select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,
    		umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate
    		from {$this->tbname}{$startSuffix} where 1 {$constr} UNION 
    		select created, trade_no, orderId, additions, status, if(mark = '0', money, 0) expend, if(mark = '1', money, 0) income,
    		umoney, ctype, mark, if(mark = '2' && additions in(1, 2, 3), money, 0) kmoney, if(date_add(created, interval 12 hour) < now(), 1, 0) outdate
    		from {$this->tbname}{$endSuffix} where 1 {$constr}) tmp WHERE 1 ORDER BY tmp.created desc LIMIT " . ($cpage - 1) * $psize . ", $psize";
            $vdata['datas'] = $this->slave->query($sql, $newCons)->getAll();

            return $vdata;
        }
    }

    // 取交易详情
    public function getWithdrawDetail($cons, $cpage, $psize)
    {
        $constr = "and uid = ? and created >= ? and created <= ? ";

        $nsql = "SELECT count(*) total, sum(money) money FROM cp_withdraw WHERE 1 " . $constr;

        $vdata = $this->slave->query($nsql, $cons)->getRow();

        $sql = "select created, trade_no, status, money, additions, content
                from cp_withdraw where 1 " . $constr .
            " order by created DESC limit " . ($cpage - 1) * $psize . ", $psize";
        $vdata['datas'] = $this->slave->query($sql, $cons)->getAll();

        return $vdata;
    }

    //刷新钱包
    public function freshWallet($uid)
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['USER_INFO']}$uid";
        $this->load->driver('cache', array('adapter' => 'redis'));
        $uinfo = $this->cache->redis->hGet($ukey, "uname");
        if(empty($uinfo))
        {
        	$this->load->model('user_model');
        	$this->user_model->freshUserInfo($uid);
        	return true;
        }
        else
        {
        	$wallet = $this->db->query('SELECT money, blocked, dispatch FROM cp_user WHERE uid = ?', array($uid))->getRow();
        	return $this->cache->redis->hMSet($ukey, $wallet);
        }
    }

    //订单支付
    public function payOrder($uid, $PostData, $orderData, $money = 0, $trans=true)
    {
        //$url = $this->config->item('order_api') . 'order/pay';
        if($trans) $this->db->trans_start();
        //红包校验
        if(isset($orderData['redpackId']))
        {
        	$res = $this->checkBetRedpack($uid, $orderData['orderId'], $orderData['redpackId']);
        	if($res['code'] == '0')
        	{
        		$money = $res['data']['cost'];
        	}
        	else
        	{
        		return $res;
        	}
        }
        if ($money > 0)
        {
            $return = $this->payMoney($uid, $money, $orderData['orderId'], FALSE);
            $trade_no = $return['trade_no'];
        }
        else
        {
            $trade_no = $orderData['trade_no'];
            $orderData['money'] = ParseUnit($orderData['money'], 1);
        }

        if ($trade_no)
        {
            $orderData['trade_no'] = $trade_no;
            // $response = $this->tools->request($url, $PostData);
            // $response = json_decode($response, true);
            $response = array(
                'code' => '0',
            );
            $response = $this->dealResult($response, $orderData);
            if ( ! $response || $response['code'] != 0)
            {
                if($trans) $this->db->trans_rollback();
                if ( ! $response)
                {
                    $response = array(
                        'code' => 11,
                        'msg'  => '订单支付，失败！',
                        'data' => array(
                            'orderId' => $orderData['orderId'],
                            'money'   => $orderData['money']
                        ),
                    );
                }
            }
            else
            {
                if($trans) $this->db->trans_complete();
                $this->order_model->upJcBzbpOrder($orderData);
            }
        }
        else
        {
            if($trans) $this->db->trans_rollback();
            if ($return['code'] == 12)
            {
                $response = array(
                    'code' => 12,
                    'msg'  => '订单支付失败，余额不足！',
                    'data' => array(
                        'orderId' => $orderData['orderId'],
                        'money'   => $orderData['money']
                    ),
                );
            }
            elseif($return['code'] == 17)
            {
            	 $response = array(
                    'code' => 16,
                    'msg'  => '订单支付失败，已过支付时间！',
                    'data' => array(),
                );
            	$this->order_model->upOutOfDay($orderData['orderId']);
            }
            elseif ($return['code'] == 18)
            {
            	$response = array(
            		'code' => 16,
            		'msg'  => '订单支付失败，红包已被使用！',
            		'data' => array(),
            	);
            }
            else
            {
                $response = array(
                    'code' => 16,
                    'msg'  => '订单支付失败，状态不满足！',
                    'data' => array(),
                );
            }

        }

        //log_message('LOG', serialize($response), "ORDER");
        return $response;
    }

    public function dealResult($response, $data)
    {
        //创建失败状态
        $fails = array('create' => 'create_init', 'pay' => 'pay_fail');
        if ($response == NULL)
        {
            $response = array(
                'code' => - 9999,
                'msg'  => '网络出了问题，请稍后再试！',
                'data' => array(),
            );
            $data['ctype'] = $fails[$data['ctype']];
            //log_message('LOG', print_r($data, true), 'ORDER');
        }
        else
        {
            if ($response['code'] != 0)
            {
                $data['ctype'] = $fails[$data['ctype']];
            }
            if ($response['code'] == 6)
            {
                $response['msg'] = '请退出并重新登录';
            }
        }
        //记录返回状态
        $data['mark'] = $response['code'];
        $response['data'] = array();
        $response['data']['orderId'] = $data['orderId'];
        $response['data']['money'] = $data['money'];
        $data['money'] = ParseUnit($data['money']);
        $this->load->model('order_model');
        if ($this->order_model->SaveOrder($data['ctype'], $data))
        {
            return $response;
        }
        else
        {
            return FALSE;
        }
    }

    //充值直接支付订单功能
    public function autoPay($trade_no, $trans)
    {
        if ($trade_no)
        {
            $this->load->model('order_model');
            $orderName = $this->order_model->tbname;
            $orders = $this->db->query("select n.uid, n.money, n.orderId, n.lid, n.endTime,d.redpackId,d.redpackMoney from {$this->tbname} m
			join {$orderName} n on m.orderId = n.orderId 
			left join cp_orders_detail d on d.orderId = n.orderId 
			where m.trade_no = ? and n.status = 10 and 
			m.ctype={$this->ctype['recharge']}", array($trade_no))->getRow();
            
            if ( ! empty($orders))
            {
            	$orderData = array(
                    'uid'     => $orders['uid'],
                    'ctype'   => 'pay',
                    'orderId' => $orders['orderId'],
                    'money'   => ($orders['redpackId'] && $orders['redpackMoney']) ? ($orders['money'] - intval($orders['redpackMoney'])) : $orders['money'], 
                );
                $this->dealPay($orderData, $trans);
            }
        }
    }

    //处理付款请求和订单数据
    public function dealPay($orderData, $trans=true)
    {
        $this->load->helper('string');
        $postData = $this->_padBusiParams($orderData, $orderData['uid']);
        $postData['money'] = ParseUnit($postData['money'], 1);
        /*$encryptStr = $this->tools->encrypt(json_encode($postData));
        $PostData['JSON'] = array('encryptStr'=>$encryptStr);*/
        if (empty($orderData['trade_no']))
        {
            //未付款订单提交
            $response = $this->payOrder($orderData['uid'], $PostData, $orderData, $orderData['money'], $trans);
        }
        else
        {
            //已付款订单提交
            $response = $this->payOrder($orderData['uid'], $PostData, $orderData, 0, $trans);
        }

        return $response;
    }

    //处理订单接口数据，为了公用所以放在此处
    public function _padBusiParams($data, $uid)
    {
        $uarray = array('url', 'isToken', 'isJson', 'ctype', 'endTime', 'pay_pwd', 'codecc');
        $data['token'] = $uid;
        $data['uid'] = $uid;
        if ($data['ctype'] == 'create')
        {
            $data['userName'] = $uid;
            //$data['orderId'] = $this->tools->getIncNum('UNIQUE_KEY');
        }
        foreach ($uarray as $val)
        {
            if (array_key_exists($val, $data))
            {
                unset($data[$val]);
            }
        }

        return $data;
    }

    //获得提款记录
    public function getWithdrawLog($uid)
    {
        $count = $this->db->query("SELECT count(*) FROM cp_withdraw
		WHERE status != {$this->status['withdraw_fail']} AND uid = ? AND created >= date(now()) && created < date(date_add(now(), INTERVAL 1 DAY))",
            array($uid))->getOne();
        $privilege = $this->db->query("select l.privilege from cp_growth_level l inner join cp_user_growth g on l.grade= g.grade where g.uid = ?", array($uid))->getRow();
        $countLimit = 3;
        if($privilege)
        {
            $privilege = json_decode($privilege['privilege'], true);
            $countLimit = $privilege['withdraw'];
        }
        if($count >= $countLimit)
        {
        	return true;
        }
        return false;
    }

    public function addMoney($uid, $data, $trans = TRUE)
    {
        $fields = array(
            'uid',
            'mark',
            'trade_no',
            'orderId',
            'money',
            'must_cost',
            'dispatch',
            'ctype',
            'additions',
            'umoney'
        );
        $datas = array();
        foreach ($fields as $field)
        {
            $datas[$field] = empty($data[$field]) ? 0 : $data[$field];
        }
        if(isset($data['content']))
        {
        	$datas['content'] = $data['content'];
        }
        $datas['money'] = intval($datas['money']);
        $datas['must_cost'] = intval($datas['must_cost']);
        $datas['dispatch'] = intval($datas['dispatch']);
        if ($datas['money'] > 0)
        {
            if ($trans)
            {
                $this->db->trans_start();
            }
            $re1 = $this->db->query("UPDATE cp_user SET money = money + ?, must_cost = must_cost + ?,
			dispatch = dispatch + ? WHERE uid = ?",
                array($datas['money'], $datas['must_cost'], $datas['dispatch'], $uid));
            $money = $this->getMoney($uid);
            $datas['umoney'] = empty($money['money']) ? 0 : $money['money'];
            $re2 = $this->db->query("insert {$this->tbname}(" . implode(',', array_keys($datas)) . ', created)
			values(' . implode(',', array_map(array($this, 'maps'), $datas)) . ', now())', $datas);
            $re = $re1 && $re2;
            if ($re)
            {
                if ($trans)
                {
                    $this->db->trans_complete();
                }
                $this->freshWallet($uid);
            }
            else
            {
                if ($trans)
                {
                    $this->db->trans_rollback();
                }
            }
            return $re;
        }
    }

    //充值更新失败处理
    public function rechargeFail()
    {
        $orders = $this->requestPay('query');
        if ( ! empty($orders))
        {
            foreach ($orders as $order)
            {
                $real_amount = 0;
                if ( ! empty($order['real_amount']))
                {
                    $real_amount = intval($order['real_amount']);
                }
                if ($this->recharge($order['trade_no'], $real_amount))
                {
                    $this->requestPay('del', $order['trade_no']);
                }
            }
        }
    }

    private function requestPay($act, $trade_no = '')
    {
        $time = time();
        $salt = $this->GetSalt();
        $url = $this->config->item('rcg_fail');
        $this->load->library('tools');
        $PostData['act'] = $act;
        if ( ! empty($trade_no))
        {
            $PostData['trade_no'] = $trade_no;
        }
        $PostData['mid'] = 'CP';
        $PostData['dateline'] = $time;
        $PostData['token'] = md5("CP{$act}{$time}{$salt}");
        $response = $this->tools->request($url, $PostData);
        $orders = unserialize($response);

        return $orders;
    }

    public function getWalletLog($trade_no)
    {
        $sql = "SELECT uid, money, must_cost, dispatch, mark, ctype, additions, trade_no, orderId, umoney, red_pack, status, platform, created FROM cp_wallet_logs WHERE trade_no = ?";
        $walletInfo = $this->db->query($sql, array($trade_no))->getRow();
        return $walletInfo;
    }
    
    /**
     * 检查订单付款状态
     * @param unknown_type $orderId
     * @param unknown_type $orderType
     */
    public function getOrderStatus($orderId, $orderType)
    {
    	if ($orderType == 3) {
    		$sql = "select status from cp_united_orders where orderId = ?";
    	}else if($orderType == 1)
    	{
    		$sql = "select status from cp_chase_manage where chaseId = ?";
    	}
        else if($orderType == 4)
        {
            $sql = "SELECT if(status > 0, 240, 0) as status FROM cp_united_follow_orders WHERE followId = ?";
        }
    	else
    	{
    		$sql = "select status from cp_orders where orderId = ?";
    	}
    	
    	return $this->db->query($sql, array($orderId))->getOne();
    }
    
    public function getUnRecharge($type, $num)
    {
        $res = $this->slave->query("SELECT platform FROM
                            (SELECT mark, platform
                            FROM cp_wallet_logs
                            WHERE created > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND ctype = '0' AND additions = ?
                            ORDER BY `created` DESC 
                            LIMIT {$num}) tmp
                            WHERE mark = '2'", array($type))->getCol();
        if (count($res) == $num) return $res;
        return false;
    }
    
    public function getStopPaytype() {
    	return $this->slave->query("select otherCondition from cp_alert_config where id = 19")->getCol();
    }
    
    public function alertEmail($content)
    {
        $alertRow = array('19', '充值或存在异常报警', $content, date('Y-m-d H:i:s', time()));
        $isql = 'insert cp_alert_log(ctype, title, content, created) values (?, ?, ?, ?)';
        $this->db->query($isql, $alertRow);
    }
    /**
     * 购彩红包检查操作
     * @param int $uid		用户uid
     * @param int $orderId	订单id
     * @param int $redpackId	红包id
     * @return multitype:number string multitype: |multitype:number string multitype:number
     */
    public function checkBetRedpack($uid, $orderId, $redpackId)
    {
    	$response = array(
    		'code' => 16,
    		'msg'  => '订单支付失败，状态不满足！',
    		'data' => array(),
    	);
    	$this->load->model('order_model');
    	$order = $this->order_model->getById($orderId);
    	if(!$order)
    	{
    		return $response;
    	}
    	//不使用红包
    	if($redpackId == '0')
    	{
    		if(($order['redpackId'] != $redpackId) || ($order['redpackId'] == '' && $order['redpackMoney'] == ''))
    		{
    			$orderDetail = array(
    				'orderId' => $orderId,
    				'redpackId' => 0,
    				'redpackMoney' => 0,
    			);
    			$res = $this->order_model->insertOrderDetail($orderDetail);
    			if(!$res)
    			{
    				return $response;
    			}
    		}
    		
    		$response = array(
    			'code' => 0,
    			'msg'  => '操作成功',
    			'data' => array('cost' => $order['money']),
    		);
    		
    		return $response;
    	}
    	else
    	{
    		$this->load->model('red_pack_model');
    		$redpack = $this->red_pack_model->getRedpackById($uid, $redpackId);
    		if(!$redpack)
    		{
    			$response['msg'] = '订单支付失败，红包不存在或已使用！';
    			return $response;
    		}
    		
    		$this->load->config('order');
    		$cType = $this->config->item("redpack_c_type");
    		if((!in_array($redpack['c_type'], $cType[$order['lid']])) || ($order['money'] < $redpack['money_bar'])
    				|| (($order['buyPlatform'] == '0') && !empty($redpack['ismobile_used'])) || (($redpack['valid_start'] > date('Y-m-d H:i:s')) || ($redpack['valid_end'] < date('Y-m-d H:i:s'))))
    		{
    			$response['msg'] = '订单支付失败，红包不符合使用条件！';
    			return $response;
    		}
    		if($order['redpackId'] != $redpackId)
    		{
    			$orderDetail = array(
    				'orderId' => $orderId,
    				'redpackId' => $redpackId,
    				'redpackMoney' => $redpack['money'],
    			);
    			$res = $this->order_model->insertOrderDetail($orderDetail);
    			if(!$res)
    			{
    				$response['msg'] = '订单支付失败，状态不满足！';
    				return $response;
    			}
    		}
    		
    		$response = array(
    			'code' => 0,
    			'msg'  => '操作成功',
    			'data' => array('cost' => ($order['money'] - $redpack['money']))
    		);
    	}
    	
    	return $response;
    }

    public function getPayDetail($trade_no)
    {
        $sql = "SELECT w.uid, w.money, w.must_cost, w.dispatch, w.mark, w.ctype, w.additions, w.trade_no, w.orderId, w.umoney, w.red_pack, w.status, w.platform, w.content, w.created, p.rcg_serial, p.pay_type, p.bank_id, p.pay_trade_no FROM cp_wallet_logs AS w LEFT JOIN cp_pay_logs AS p ON w.trade_no = p.trade_no WHERE w.trade_no = ?";
        $walletInfo = $this->db->query($sql, array($trade_no))->getRow();
        return $walletInfo;
    }
    
    public function newAutopay($tradeNo)
    {
        $trans = true;
        $wInfo = $this->db->query("SELECT uid, ctype, platform, status
            FROM {$this->tbname} WHERE trade_no = ?", array($tradeNo))->getRow();
        if ($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 1)
        {
            $this->load->model('chase_wallet_model');
            $this->chase_wallet_model->autoPay($tradeNo, $trans);
        }
        elseif($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 3)
        {
            $this->load->model('united_wallet_model');
            $this->united_wallet_model->autoPay($tradeNo, $trans);
        }
        elseif($wInfo['ctype'] == $this->ctype['recharge'] && $wInfo['status'] == 4)
        {
            $this->load->model('follow_wallet_model');
            $this->follow_wallet_model->autoPay($tradeNo, $trans);
        }        
        else
        {
            $this->autoPay($tradeNo, $trans);
        }
    }
    
    /**
     * 查询未支付订单用于补单
     * @param string $start 开始时间
     * @param string $end   结束时间
     * @param number $id
     * @return unknown
     */
    public function getRecharge($start, $end, $id = 0) 
    {
        $sql = "SELECT id, trade_no FROM cp_wallet_logs WHERE ctype = '0' and  
        created >= ? and  created <= ? and id > ?
        and mark = '2' ORDER BY id ASC limit 100";
        $result = $this->slave->query($sql, array($start, $end, $id))->getAll();
        return $result;
    }
}
