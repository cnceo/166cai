<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 追号方案投注脚本 - 模型层
 * @date:2015-12-08
 */
class Chase_Bet_Model extends MY_Model
{
	public function __construct() 
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
		$this->load->model('chase_wallet_model');
	}

	// 快频彩追号彩种设置
	public $quickLotteryConfig = array(
		// 十一选五
		'21406' => array(
			'lname' => 'syxw',
			'cache' => 'SYXW_ISSUE_TZ',
			'tname' => 'syxw'
		),
		//江西十一选五
		'21407' => array(
			'lname' => 'jxsyxw',
			'cache' => 'JXSYXW_ISSUE_TZ',
			'tname' => 'jxsyxw'
		),
		//湖北十一选五
		'21408' => array(
			'lname' => 'hbsyxw',
			'cache' => 'HBSYXW_ISSUE_TZ',
			'tname' => 'hbsyxw'
		),
	    //广东十一选五
	    '21421' => array(
	        'lname' => 'gdsyxw',
	        'cache' => 'GDSYXW_ISSUE_TZ',
	        'tname' => 'gdsyxw'
	    ),
		'53' => array(
			'lname' => 'ks',
			'cache' => 'KS_ISSUE_TZ',
			'tname' => 'ks'
		),
		'56' => array(
			'lname' => 'jlks',
			'cache' => 'JLKS_ISSUE_TZ',
			'tname' => 'jlks'
		),
	    '57' => array(
	        'lname' => 'jxks',
	        'cache' => 'JXKS_ISSUE_TZ',
	        'tname' => 'jxks'
	    ),
		'54' => array(
			'lname' => 'klpk',
			'cache' => 'KLPK_ISSUE_TZ',
			'tname' => 'klpk'
		),
		'55' => array(
			'lname' => 'cqssc',
			'cache' => 'CQSSC_ISSUE_TZ',
			'tname' => 'cqssc'
		),
	);
	
	// 慢频彩追号彩种设置
	public $slowLotteryConfig = array(
	    // 双色球
	    '51' => array(
	        'lname' => 'ssq',
	        'cache' => 'SSQ_ISSUE',
	        'tname' => 'ssq'
	    ),
	    // 福彩3D
	    '52' => array(
	        'lname' => 'fc3d',
	        'cache' => 'FC3D_ISSUE',
	        'tname' => 'fc3d'
	    ),
	    // 排列三
	    '33' => array(
	        'lname' => 'pls',
	        'cache' => 'PLS_ISSUE',
	        'tname' => 'pl3'
	    ),
	    // 排列五
	    '35' => array(
	        'lname' => 'plw',
	        'cache' => 'PLW_ISSUE',
	        'tname' => 'pl5'
	    ),
	    // 七星彩
	    '10022' => array(
	        'lname' => 'qxc',
	        'cache' => 'QXC_ISSUE',
	        'tname' => 'qxc'
	    ),
	    // 七乐彩
	    '23528' => array(
	        'lname' => 'qlc',
	        'cache' => 'QLC_ISSUE',
	        'tname' => 'qlc'
	    ),
	    // 大乐透
	    '23529' => array(
	        'lname' => 'dlt',
	        'cache' => 'DLT_ISSUE',
	        'tname' => 'dlt'
	    ),
	);

	// 获取订单状态标示
	public function getLotteryConfig($type = 'quick')
	{
	    if($type == 'quick')
	    {
	        return $this->quickLotteryConfig;
	    }
	    else
	    {
	        return $this->slowLotteryConfig;
	    }
	}

	// 获取彩种追号池缓存信息
    public function getBetNum($lname)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHASE_BET_NUM']}$lname";
        $betNum = $this->cache->redis->get($ukey);
        return $betNum;
    }

    // 追号池 -1
    public function decrBetNum($lname)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHASE_BET_NUM']}$lname";
        $betNum = $this->cache->redis->decrement($ukey, 1, 0);
        return $betNum;
    } 

	// 获取指定彩种的当前期信息
	public function getCurrentLottery($ukey)
    {
        $info = array(
        	'cIssue' => array(
        		'issue' => '',
				'sale_time' => '',
				'show_end_time' => ''
        	),
        	'nIssue' => array(
        		'issue' => '',
				'sale_time' => '',
				'show_end_time' => ''
        	),
		);
		$REDIS = $this->config->item('REDIS');
		$this->load->driver('cache', array('adapter' => 'redis'));
		$caches = $this->cache->get($REDIS[$ukey]);
        $caches = json_decode($caches, true);
 
        if(!empty($caches['cIssue']))
        {
        	$info['cIssue'] = array(
        		'issue' => $caches['cIssue']['seExpect'],
				'sale_time' => date('Y-m-d H:i:s',substr($caches['cIssue']['sale_time'], 0, 10)),
				'show_end_time' => date('Y-m-d H:i:s',substr($caches['cIssue']['seFsendtime'], 0, 10)),
				'end_time' => date('Y-m-d H:i:s',substr($caches['cIssue']['seEndtime'], 0, 10))
        	);
        }

        if(!empty($caches['nIssue']))
        {
        	$info['nIssue'] = array(
        		'issue' => $caches['nIssue']['seExpect'],
				'sale_time' => date('Y-m-d H:i:s',substr($caches['nIssue']['sale_time'], 0, 10)),
				'show_end_time' => date('Y-m-d H:i:s',substr($caches['nIssue']['seFsendtime'], 0, 10)),
				'end_time' => date('Y-m-d H:i:s',substr($caches['nIssue']['seEndtime'], 0, 10))
        	);
        }
        return $info;
    }

    // 获取投注缓存期次信息
    public function getBetCache($lname)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHASE_BET_ISSUE']}$lname";
        $betCache = unserialize($this->cache->redis->get($ukey));
        return  $betCache;
    }

    // 刷新投注缓存期次信息
    public function refreshBetCache($lname, $info)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['CHASE_BET_ISSUE']}$lname";
        return $this->cache->redis->save($ukey, serialize($info), 0);
    }

    // 捞取指定彩种指定期次的订单
    public function getBetOrderInfo($lid, $orderStatus)
    {

    	$sql = "SELECT m.chaseId AS chaseId, m.uid AS uid, m.lid AS lid, 
    	m.userName AS userName, m.buyPlatform AS buyPlatform, m.codes AS codes, 
    	m.playType AS playType, m.betTnum AS betTnum, m.isChase AS isChase, 
    	m.bonus AS bonus, m.margin AS margin, m.chaseIssue AS chaseIssue, 
    	m.revokeIssue AS revokeIssue, m.totalIssue AS totalIssue, m.status AS mStatus, 
    	m.setStatus AS mSetStatus, m.channel AS channel, m.app_version AS app_version, m.chaseType AS chaseType,
    	m.singleFlag, o.id AS chaseOrderId, o.issue AS issue, o.money AS money, o.multi AS multi, o.status AS status, 
    	o.endTime AS endTime 
    	FROM cp_chase_manage AS m 
    	INNER JOIN cp_chase_orders AS o 
    	ON m.chaseId = o.chaseId 
    	WHERE m.lid = ? AND m.status = ? AND o.status = ? AND o.bet_flag = ? AND o.modified > date_sub(now(), interval 30 MINUTE) 
    	ORDER BY m.chaseId,o.issue ASC LIMIT 300";

    	$orderInfo = $this->db->query($sql, array($lid, $orderStatus['is_chase'], 0, 1))->getAll();
    	return $orderInfo;
    }

    // 投注订单
    public function doBet($chaseOrderData)
    {
    	// 事务开始
    	$this->db->trans_start();

    	// 生成子订单订单编号
    	$chaseOrderData['orderId'] = $this->tools->getIncNum('UNIQUE_KEY');

    	// 1.冻结扣款
    	$payStatus = $this->payChaseOrder($chaseOrderData['uid'], $chaseOrderData['money'], $chaseOrderData['issue'], $chaseOrderData['orderId'], $chaseOrderData['chaseId']);

    	if($payStatus['trade_no'])
    	{
    		// 2.创建订单 类型追号
    		$chaseOrderData['trade_no'] = $payStatus['trade_no'];
        	$orderStatus = $this->dealChaseOrder($chaseOrderData);

        	if($orderStatus['status'])
        	{
        		// 3.更新追号订单表状态
        		$bet_flag = 2;	// 订单已投
        		$chaseOrderStatus = $this->updateChaseOrder($chaseOrderData, $orderStatus['data'], $bet_flag);
        		
        		if($chaseOrderStatus)
        		{
        			$this->db->trans_complete();
        			$this->chase_wallet_model->freshWallet($chaseOrderData['uid']);
        			$betStatus = array(
		    			'code' => TRUE,
						'msg' => '追号投注成功',
						'data' => $orderStatus['data']
		    		);
        		}
        		else
        		{
        			$this->db->trans_rollback();

        			$betStatus = array(
		    			'code' => FALSE,
						'msg' => '追号投注失败',
						'data' => $chaseOrderData
		    		);
        		}
        	}
        	else
        	{
        		$this->db->trans_rollback();

        		$betStatus = array(
	    			'code' => FALSE,
					'msg' => $orderStatus['msg'],
					'data' => $chaseOrderData
	    		);
        	}
    	}
    	else
    	{
    		$this->db->trans_rollback();

        	$betStatus = array(
    			'code' => FALSE,
				'msg' => $payStatus['msg'],
				'data' => $chaseOrderData
    		);
    	}    	

    	return $betStatus;
    }

    // 冻结金额 扣款
    public function payChaseOrder($uid, $cost, $issue, $orderId, $chaseId=0)
    {
    	$payStatus = array(
			'code' => 400,
			'trade_no' => false,
			'msg' => '支付失败'
		);

		$cost = intval($cost);
		// 用户信息行锁
		$uinfo = $this->chase_wallet_model->getUserMoney($uid);
		
		if($uinfo['blocked'] >= $cost)
		{
			if(empty($chaseId))
			{
				$payStatus = array(
					'code' => 301,
					'trade_no' => false,
					'msg' => '订单支付失败，订单信息错误！'
				);
				return $payStatus;
			}

			// 流水号生成
			$trade_no = $this->tools->getIncNum('UNIQUE_KEY');
			
			// 流水日志组装数据
			$walletStatus = $this->chase_wallet_model->getStatus();
			// 计算其他余额花费
			$bmoney = ($uinfo['must_cost'] + $uinfo['dispatch']) - $cost;
			$dispatch = 0;
			$must_cost = 0;
			if($bmoney >= $uinfo['must_cost'])
			{
				$dispatch = $cost;
			}
			elseif($bmoney >=0)
			{
				$dispatch = $uinfo['dispatch'];
				$must_cost = abs($dispatch - $cost);
			}
			else 
			{
				$dispatch = $uinfo['dispatch'];
				$must_cost = $uinfo['must_cost'];
			}

			$wallet_log = array(
				'uid' => $uid,
				'money' => $cost,
				'ctype' => $walletStatus['pay'],
				'trade_no' => $trade_no,
				'umoney' => $uinfo['money'],	//用户余额不变
				'must_cost' => $must_cost,
				'status' => 1,
				'dispatch' => $dispatch, 
				'additions' => (empty($order['lid'])? 0 : $order['lid']), 
				'orderId' => $orderId
			);
			// 记录扣款流水
			$sql1 = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created)
			values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);
			
			// 扣款
			$sql2 = $this->db->query("update cp_user set blocked = blocked - $cost, chaseMoney = chaseMoney - $cost,
			must_cost = if((must_cost + dispatch) > $cost, 
			if(dispatch - $cost > 0, must_cost, must_cost + (dispatch - $cost)), 0),
			dispatch = if(dispatch > $cost, dispatch - $cost, 0)
			where blocked >= $cost and uid = ?", array($uid));
			
			// 入总账流水
			$this->load->model('capital_model');
			$sql3 = $this->capital_model->recordCapitalLog(1, $trade_no, 'pay', $cost, '1', false);
			
			$re = $sql1 && $sql2 && $sql3;
			if($re) 
			{
				$payStatus = array(
					'code' => 200,
					'trade_no' => $trade_no,
					'msg' => '订单扣款成功！'
				);
				return $payStatus;
			}
			else 
			{
				$payStatus = array(
					'code' => 400,
					'trade_no' => false,
					'msg' => '订单扣款失败！'
				);
				return $payStatus;
			}
		}
		else 
		{
			$payStatus = array(
				'code' => 400,
				'trade_no' => false,
				'msg' => '订单支付失败,余额不足！'
			);
			return $payStatus;
		}	
    }

    // 获得余额
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch from cp_user where uid = ? for update', array($uid))->getRow();
	}

	// 处理追号订单状态
	public function dealChaseOrder($orderData)
	{
		$orderStatus = array(
			'status' => FALSE,
			'msg' => '订单创建失败',
			'data' => ''
		);

		$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $ticketSeller = unserialize($this->cache->get($REDIS['TICKET_SELLER'])); //分配票商
		$this->load->model('activity_model');
		// 组装追号信息数据
		$orderInfo = array(
			'uid' => $orderData['uid'],
			'userName' => $orderData['userName'],
			'orderId' => $orderData['orderId'],
			'trade_no' => $orderData['trade_no'],
			'buyPlatform' => $orderData['buyPlatform'],
			'codes' => $orderData['codes'],
			'lid' => $orderData['lid'],
			'money' => $orderData['money'],
			'multi' => $orderData['multi'],
			'issue' => $orderData['issue'],
			'playType' => $orderData['playType'],
			'isChase' => $orderData['isChase'],
			'orderType' => $orderData['chaseType'] > 0 ? 6 : 1, // 追号类型
			'singleFlag' => $orderData['singleFlag'],
			'betTnum' => $orderData['betTnum'],
			'status' => $this->order_status['pay'],
			'channel' => $orderData['channel'],
			'app_version' => $orderData['app_version'],
			'mark' => 0,
			'pay_time' => date('Y-m-d H:i:s'),
			'endTime' => $orderData['endTime'],
			'ticket_seller' => $ticketSeller[$orderData['lid']],
			'shopId' => $this->getBetStation($ticketSeller[$orderData['lid']], $orderData['lid']),
			'activity_ids' => $this->activity_model->checkRebateByUid($orderData['uid'])
		);

		// 创建已支付订单
		if($this->doBetOrder($orderInfo))
		{
			$orderStatus = array(
				'status' => TRUE,
				'msg' => '订单创建成功',
				'data' => $orderInfo
			);
		}
		else
		{
			$orderStatus = array(
				'status' => FALSE,
				'msg' => '订单创建失败',
				'data' => $orderInfo
			);
		}
		
		return $orderStatus;
	}

	// 投注订单
	public function doBetOrder($orderInfo)
	{
		$upfields = array('status', 'bonus', 'margin', 'eachAmount', 'channel', 'codecc', 'qsFlag', 'ticket_time', 'win_time', 'trade_no', 'mark', 'pay_time', 'singleFlag');
        $fields = array_keys($orderInfo);
        $sql = "insert cp_orders(" . implode(',', $fields) . ", created)
        values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())" . $this->onduplicate($fields, $upfields);
		return $this->db->query($sql, $orderInfo);
	}
	

	// 更新追号订单表状态
	public function updateChaseOrder($chaseOrderData, $orderData, $bet_flag)
	{
		$sql = "UPDATE cp_chase_orders SET orderId = ?, status = ?, bet_flag = ? WHERE id = ?";
		return $this->db->query($sql, array($orderData['orderId'],  $orderData['status'], $bet_flag, $chaseOrderData['chaseOrderId']));
	}
	
	//失败更新追号订单状态
	public function failChaseOrder($chaseOrderData, $orderData, $betFlag)
	{
		$sql = "UPDATE cp_chase_orders SET status = ?, bet_flag = ? WHERE id = ?";
		return $this->db->query($sql, array($orderData['status'], $betFlag, $chaseOrderData['chaseOrderId']));
	}

	/*
     * 出票订单随机分配投注站
     * @date:2016-02-01
     */
    public function getBetStation($seller, $lid)
    {
    	$partnerArr = $this->config->item('cfg_partner_lid');
        $search = array(
            'partner_name' => $seller,
            'lottery_type' => $this->getLotteryType($lid),
            'status' => '30',
            'delete_flag' => '0',
        	'lid' => array_key_exists($lid, $partnerArr) ? $lid : '0'
        );

        $sql = "SELECT id, partnerId, partner_name, shopNum, cname, lottery_type, phone, 
        qq, webchat, other_contact, address, fail_reason, off_reason, 
        delete_flag, status, created 
        FROM cp_partner_shop
        WHERE partner_name = ? AND lottery_type = ? AND status = ? AND delete_flag = ? AND lid = ?";

        $stationInfo = $this->db->query($sql, array($search['partner_name'], $search['lottery_type'], $search['status'], $search['delete_flag'], $search['lid']))->getAll();
    
        $shopId = '0';
        if(!empty($stationInfo))
        {
            $stationNum = count($stationInfo) - 1;
            $stationIndex = rand(0, $stationNum);
            $shopId = $stationInfo[$stationIndex]['id'];
        }
        return $shopId;
    }

    /**
     * [getLotteryType  获取彩种所属类型(福彩/体彩) 福彩：双色球，福彩3D，七乐彩 经典快3 易快3 红快3]
     * @author LiKangJian 2017-11-29
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    public function getLotteryType($lid)
    {
        if(in_array($lid, array('51', '52', '23528', '53','56','57')))
        {
            $lotteryType = 1;
        }
        else
        {
            $lotteryType = 0;
        }
        return $lotteryType;
    }

}
