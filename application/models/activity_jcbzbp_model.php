<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 竞彩不中包赔 - 模型层
 */
class Activity_Jcbzbp_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('neworder_model');
	}

	// 活动配置项 测试环境与线上参数保持一致
	private $payTypeArr = array
	(
        0 	=> 	'110',		// 满三减二购彩红包
        1	=>	'111',		// 充值红包
	);

	// 汇总活动信息
	public function getActivityInfo()
	{
		$data = array();
		$info = $this->getLastActivityInfo();
		if(!empty($info))
		{
			// 投注缓存更新赔率
			$matchData = $this->getJjcInfo($info['lid']);

			$plan = json_decode($info['plan'], true);

			$playNameArr = array();
			$matches = array();
			foreach ($plan as $items) 
			{
				$playNameArr[] = $items['playType'];
				if(!empty($matchData[$items['mid']]))
				{
					$logoInfo = $this->getMatchDetail($info['lid'], substr($items['mid'], 2));

					$match = array(
						'issue'		=>	$matchData[$items['mid']]['issue'],
						'mid'		=>	$items['mid'],
						'name'		=>	$matchData[$items['mid']]['nameSname'],
						'home'		=>	$matchData[$items['mid']]['homeSname'],
						'awary'		=>	$matchData[$items['mid']]['awarySname'],
						'let'		=>	$matchData[$items['mid']]['let'] ? str_replace('+', '',$matchData[$items['mid']]['let']) : '0',
						'dt'		=>	date('Y-m-d H:i:s', substr($matchData[$items['mid']]['dt'], 0, 10)),
						'endTime'	=>	date('Y-m-d H:i:s', substr($matchData[$items['mid']]['jzdt'], 0, 10)),
						'homelogo'	=>	$logoInfo['homelogo'],
						'awaylogo'	=>	$logoInfo['awaylogo'],
						'playType'	=>	$items['playType'],
						'sp'		=>	$this->getSpData($matchData[$items['mid']], $items['playType']),
						'res'		=>	$items['res'],
						'kstime'	=>	$this->getKstime(date('Y-m-d H:i:s', substr($matchData[$items['mid']]['dt'], 0, 10))),
					);
				}
				else
				{
					$logoInfo = $this->getMatchDetail($info['lid'], substr($items['mid'], 2));
					$match = array(
						'issue'		=>	$items['issue'],
						'mid'		=>	$items['mid'],
						'name'		=>	$items['name'],
						'home'		=>	$items['home'],
						'awary'		=>	$items['awary'],
						'let'		=>	$items['let'] ? str_replace('+', '',$items['let']) : '0',
						'dt'		=>	$items['dt'],
						'endTime'	=>	$items['endTime'],
						'homelogo'	=>	$logoInfo['homelogo'],
						'awaylogo'	=>	$logoInfo['awaylogo'],
						'playType'	=>	$items['playType'],
						'sp'		=>	$items['sp'],
						'res'		=>	$items['res'],
						'kstime'	=>	$this->getKstime($items['dt']),
					);
				}
				array_push($matches, $match);
			}

			$playNameArr = array_unique($playNameArr);

			// 获取红包信息
			$redpack = $this->getRedpackInfo($this->payTypeArr[$info['payType']]);
			$use_params = json_decode($redpack['use_params'], true);

			$data = array(
				'id'		=>	$info['id'],
				'lid'		=>	$info['lid'],
				'startTime'	=>	$info['startTime'],
				'endTime'	=>	$info['endTime'],
				'current'	=>	date('Y-m-d H:i:s'),
				'match'		=>	$matches,
				'playName'	=>	$this->getPlayName($playNameArr),
				'ggName'	=>	count($matches) > 1 ? count($matches) . '串1' : '单关',
				'money'		=>	ParseUnit($info['buyMoney'], 1),
				'time'		=>	date('Y年m月d日H:i', strtotime($info['startTime'])) . '-' . date('Y年m月d日H:i', strtotime($info['endTime'])),
				'title'		=>	'不中即返' . ParseUnit($redpack['money'], 1) . '元红包',
				'slogan'	=>	$redpack['use_desc'] . '红包1个，红包有效期' . ($use_params['end_day'] - $use_params['start_day']) . '日',
				'btnMsg'	=>	$this->getBtnMsg($info),
			);
		}
		return $data;
	}

	// 查询最新一期活动
	public function getLastActivityInfo()
	{
		$sql = "SELECT id, lid, playType, plan, startTime, endTime, buyMoney, payType FROM cp_activity_jcbp_config ORDER BY id DESC LIMIT 1";
		return $this->slave->query($sql)->getRow();
	}

	public function getJjcInfo($lotteryId)
    {
        $info = array();

        // 彩票数据中心
        switch ($lotteryId) 
        {
            // 竞彩足球
            case '42':
                $info = $this->getJczqMatch();
                break;
            // 竞彩篮球
            case '43':
                $info = $this->getJclqMatch();
                break;
            default:
                # code...
                break;
        }
        return $info;
    }

    public function getJczqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCZQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    public function getJclqMatch()
    {
        $REDIS = $this->config->item('REDIS');
        $ukey = "{$REDIS['JCLQ_MATCH']}";
        $info = $this->cache->redis->get($ukey);
        $info = json_decode($info, true);
        return $info;
    }

    // 获取致胜比赛信息
    public function getMatchDetail($lid, $mid)
    {
        $table = ($lid == '42') ? 'cp_data_zq_matchs' : 'cp_data_lq_matchs';
        $sql = "SELECT homelogo, awaylogo FROM {$table} WHERE xid = ?;";
        return $this->slaveDc->query($sql, array($mid))->getRow();
    }

    public function getPlayName($playNameArr)
    {
    	$playType = array(
    		'spf'	=>	'胜平负',
    		'rqspf'	=>	'让球胜平负',
    		'sf'	=>	'胜负',
    		'rfsf'	=>	'让分胜负',
    	);
    	$playName = '混合过关';
    	if(count($playNameArr) == 1 && $playType[$playNameArr[0]])
    	{
    		$playName = $playType[$playNameArr[0]];
    	}
    	return $playName;
    }

    // 获取赔率信息
    public function getSpData($items, $playType)
    {
    	$sp = array();
    	if($playType == 'spf')
    	{
    		$sp = array(
    			'spfSp3' => $items['spfSp3'] ? $items['spfSp3'] : '0',
				'spfSp1' => $items['spfSp1'] ? $items['spfSp1'] : '0',
				'spfSp0' => $items['spfSp0'] ? $items['spfSp0'] : '0'
    		);
    	}
    	elseif($playType == 'rqspf')
    	{
    		$sp = array(
    			'rqspfSp3' => $items['rqspfSp3'] ? $items['rqspfSp3'] : '0',
				'rqspfSp1' => $items['rqspfSp1'] ? $items['rqspfSp1'] : '0',
				'rqspfSp0' => $items['rqspfSp0'] ? $items['rqspfSp0'] : '0'
    		);
    	}
    	elseif($playType == 'sf')
    	{
    		$sp = array(
    			'sfHs' => $items['sfHs'] ? $items['sfHs'] : '0',
    			'sfHf' => $items['sfHf'] ? $items['sfHf'] : '0',
    		);
    	}
    	elseif($playType == 'rfsf')
    	{
    		$sp = array(
    			'rfsfHs' => $items['rfsfHs'] ? $items['rfsfHs'] : '0',
    			'rfsfHf' => $items['rfsfHf'] ? $items['rfsfHf'] : '0',
    		);
    	}
    	return implode(',', $sp);
    }

    // 创建订单
	public function createOrder($params = array())
	{
		/*
		$params = array(
			'uid'			=>	'151',
			'activityId'	=>	'5',
			'buyPlatform'	=>	'1',
			'channel'		=>	'0',
		);
		*/

		if(empty($params['uid']))
		{
			$result = array(
				'status'	=>	'300',
				'msg' 		=> 	'尚未登录',
				'data' 		=> 	''
			);
			return $result;
		}

		$this->load->model('user_model');
		$uinfo = $this->user_model->getUserInfo($params['uid']);

		// 开启事务
        $this->db->trans_start();

		// 行锁用户
		$this->getUserInfo($params['uid']);

		// 实名检查
		if(empty($uinfo['id_card']))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'500',
				'msg' 		=> 	'您的账户尚未实名。',
				'data' 		=> 	''
			);
			return $result;
		}

		// 注销
		if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '1')
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg' 		=> 	'您的账户已注销，如有疑问请联系客服。',
				'data' 		=> 	''
			);
			return $result;
		}

		// 冻结检查
		if(isset($uinfo['userStatus']) && $uinfo['userStatus'] == '2')
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg' 		=> 	'您的账户已被冻结，如需解冻请联系客服。',
				'data' 		=> 	''
			);
			return $result;
		}

		// 行锁活动
		$activityInfo = $this->getActivityById($params['activityId']);
		if(empty($activityInfo))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'活动信息不存在',
				'data'		=>	'',
			);
			return $result;
		}

		if($activityInfo['startTime'] > date('Y-m-d H:i:s'))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'活动未开始',
				'data'		=>	'',
			);
			return $result;
		}

		if($activityInfo['endTime'] < date('Y-m-d H:i:s'))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'活动已结束',
				'data'		=>	'',
			);
			return $result;
		}

		// 检查是否参与过
		$orders = $this->checkJoinStatus($uinfo['id_card']);
		if(!empty($orders))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'您已参与过此活动',
				'data'		=>	'',
			);
			return $result;
		}

		// 检查当前期订单
		$orders = $this->getJoinInfoById($params['activityId'], $params['uid']);
		if(!empty($orders))
		{
			$this->db->trans_rollback();
			// 继续支付
			$result = array(
				'status'	=>	'200',
				'msg'		=>	'订单待支付',
				'data'		=>	array(
					'uid'			=>	$params['uid'],
					'id_card'		=>	$uinfo['id_card'],
					'jcbp_id'		=>	$params['activityId'],
					'orderId'		=>	$orders['orderId'],
					'money'			=>	$activityInfo['buyMoney'],
				),
			);
			return $result;
		}

		// 组装投注串信息
		$betInfo = $this->getBetInfo($activityInfo);
		if(empty($betInfo))
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'投注信息错误',
				'data'		=>	'',
			);
			return $result;
		}

		// 组装参数
		$orderData = array(
			'ctype'			=> 	'create',
			'uid' 			=> 	$params['uid'],
    		'userName' 		=>	$uinfo['uname'],
			'buyPlatform'	=> 	$params['buyPlatform'],
			'codes' 		=> 	$betInfo['codes'],
			'lid' 			=> 	$activityInfo['lid'],
			'money' 		=> 	ParseUnit($activityInfo['buyMoney'], 1),
			'multi' 		=> 	intval($activityInfo['buyMoney']/200),
			'issue' 		=> 	$betInfo['issue'],
			'playType' 		=> 	'0',
			'isChase' 		=> 	'0',
			'betTnum' 		=> 	'1',
			'orderType' 	=> 	'0',
			'endTime' 		=> 	$betInfo['endTime'],
			'codecc'		=>	$betInfo['codecc'],
			'channel'		=>	$params['channel'] ? $params['channel'] : '0',
			'activity_ids'	=>	'8',
			'is_hide'		=>	'2',
		);

		// 创建新订单
		$res = $this->neworder_model->createOrder($orderData);

		if(!$res['status'])
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	$res['msg'],
				'data'		=>	'',
			);
			return $result;
		}

		// 创建参与记录
		$joinData = array(
			'uid'			=>	$params['uid'],
			'id_card'		=>	$uinfo['id_card'],
			'jcbp_id'		=>	$params['activityId'],
			'orderId'		=>	$res['data']['orderId'],
			'money'			=>	$activityInfo['buyMoney'],
			'buyPlatform'	=>	$params['buyPlatform'],
		);

		$joinRes = $this->saveJoinOrder($joinData);
		if($joinRes)
		{
			$this->db->trans_complete();
			$result = array(
				'status'	=>	'200',
				'msg'		=>	'订单创建成功',
				'data'		=>	$joinData,
			);
		}
		else
		{
			$this->db->trans_rollback();
			$result = array(
				'status'	=>	'400',
				'msg'		=>	'订单创建失败',
				'data'		=>	$joinData,
			);
		}
		return $result;
	}

	// 解析投注串信息
	public function getBetInfo($activityInfo)
	{
		$betInfo = array();
		
		$plan = json_decode($activityInfo['plan'], true);
		// 升序排序
		$sortArr = array();
		foreach ($plan as $items) 
		{
			$sortArr[] = $items['mid'];
		}
		array_multisort($sortArr, SORT_ASC, $plan);

		// 投注缓存更新赔率
		$matchData = $this->getJjcInfo($activityInfo['lid']);

		$codes = 'HH|';
		$tpl = array();
		$matchArr = array();
		$endTime = '';
		foreach ($plan as $items) 
		{	
			if(!empty($matchData[$items['mid']]))
			{
				// 投注串
				$code = strtoupper($items['playType']) . '>' . $items['mid'] . '=' . $items['res'];
				if(in_array($items['playType'], array('rqspf', 'rfsf')))
				{
					$code .= '{' . $matchData[$items['mid']]['let'] . '}';
				}
				// 赔率
				if($activityInfo['lid'] == '42')
				{
					$sp = $items['playType'] . 'Sp' . $items['res'];
				}
				else
				{
					$tips = ($items['res'] == 3) ? 'Hs' : 'Hf';
					$sp = $items['playType'] . $tips;
				}
				
				$code .= '(' . $matchData[$items['mid']][$sp] . ')';
				// 截止时间
				$jzdt = date('Y-m-d H:i:s', substr($matchData[$items['mid']]['jzdt'], 0, 10));
				if(empty($endTime) || $endTime > $jzdt)
				{
					$endTime = $jzdt;
				}
				array_push($tpl, $code);
				array_push($matchArr, $items['mid']);
			}
		}
		$codes .= implode(',', $tpl);
		$codes .= '|' . count($matchArr) . '*1';
		if(count($plan) == count($matchArr))
		{
			$betInfo = array(
				'issue'		=>	date('Ymd'),
				'codes'		=>	$codes,
				'endTime'	=>	$endTime,
				'codecc'	=>	implode(' ', $matchArr),
			);
		}
		return $betInfo;
	}

    public function getUserInfo($uid)
	{
		return $this->db->query('SELECT uid, id_card FROM cp_user_info WHERE uid = ? for update', array($uid))->getRow();
	}

	public function getActivityById($activityId)
	{
		return $this->db->query('SELECT lid, playType, plan, startTime, endTime, buyMoney, payType FROM cp_activity_jcbp_config WHERE id = ? for update', array($activityId))->getRow();
	}

	public function checkJoinStatus($id_card)
	{
		return $this->db->query('SELECT count(*) FROM cp_activity_jcbp_join WHERE id_card = ? AND status >= 240', array($id_card))->getOne();
	}

	public function getJoinInfoById($activityId, $uid)
	{
		return $this->db->query('SELECT uid, id_card, jcbp_id, orderId FROM cp_activity_jcbp_join WHERE uid = ? AND jcbp_id = ? LIMIT 1', array($uid, $activityId))->getRow();
	}

	// 保存参与订单信息
	public function saveJoinOrder($info)
	{
		$fields = array_keys($info);
		$sql = "insert cp_activity_jcbp_join(" . implode(',', $fields) . ", created)
		values(". implode(',', array_map(array($this, 'maps'), $fields)) .", now())";
		return $this->db->query($sql, $info);
	}

	// 脚本处理包赔订单 - 外事务
	public function dealOrder($orderInfo)
	{
		$result = FALSE;

		if(!empty($orderInfo))
        {
        	if(in_array($orderInfo['status'], array('600', '1000', '2000')))
        	{
        		// 未中奖赔款处理
        		$pay_status = 0;
        		$returnFlag = TRUE;
        		if($orderInfo['status'] == '1000')
        		{
        			// 赔付
        			$pay_status = $this->returnPayMoney($orderInfo);

        			if(!$pay_status)
        			{
        				$returnFlag = FALSE;
        			}
        		}

        		// 同步至活动订单
        		$orderArry = array(
        			'orderId'		=>	$orderInfo['orderId'],
        			'uid' 			=> 	$orderInfo['uid'],
        			'status' 		=> 	$orderInfo['status'],
        			'pay_status'	=>	$pay_status
        		);

        		$updateRes = $this->updateJoinInfo($orderArry);

        		if($updateRes && $returnFlag)
        		{
        			$result = TRUE;
        		}
        	}
        }
        return $result;
	}

	// 订单赔付
	public function returnPayMoney($orderInfo)
	{
		$res = FALSE;

		// 行锁用户信息
		$this->getUserInfo($orderInfo['uid']);

		$activityInfo = $this->getJjcInfoByOrderId($orderInfo['orderId']);
		if(!empty($activityInfo))
		{
			switch ($activityInfo['payType']) 
			{
				case '0':
				case '1':
					$res = $this->sendRedpack($orderInfo, $activityInfo);
					break;
				default:
					$res = FALSE;
					break;
			}
		}
		return $res;
	}

	// 查询指定订单详情
	public function getJjcInfoByOrderId($orderId)
	{
		$sql = "SELECT j.orderId, c.payType FROM cp_activity_jcbp_join AS j LEFT JOIN cp_activity_jcbp_config AS c ON j.jcbp_id = c.id WHERE j.orderId = ?";
		return $this->db->query($sql, array($orderId))->getRow();
	}

	// 同步赔付状态
	public function updateJoinInfo($orderInfo)
    {
    	return $this->db->query("UPDATE cp_activity_jcbp_join SET status = ?, pay_status = ? where uid = ? and orderId = ? and pay_status = 0", array($orderInfo['status'], $orderInfo['pay_status'], $orderInfo['uid'], $orderInfo['orderId']));
    }

    // 派送满三减二红包
    public function sendRedpack($orderInfo, $activityInfo)
    {
    	// 红包类型
    	$rid = $this->payTypeArr[$activityInfo['payType']];
    	if(empty($rid))
    	{
    		return FALSE;
    	}

		$redpack = $this->getRedpackInfo($rid);
		if(!empty($redpack))
		{
			$use_params = json_decode($redpack['use_params'], true);
			$start = "+ " . $use_params['start_day'] . " day";
			$valid_start = date('Y-m-d H:i:s', strtotime($start));
			$end = "+ " . $use_params['end_day'] . " day";
			$valid_end = date('Y-m-d H:i:s', strtotime($end, strtotime($valid_start)));
			$redpackData = array(
	            'aid'           => 	$redpack['aid'],
	            'platform_id'   =>  '0',
	            'channel_id'    =>  '0',
	            'uid'           =>  $orderInfo['uid'],
	            'rid'           =>  $redpack['id'],
	            'valid_start'   =>  $valid_start,
	            'valid_end'     =>  $valid_end,
	            'get_time'      =>  date('Y-m-d H:i:s'),
	            'status'        =>  1,      // 已激活
	        );

	        // 记录红包
        	$sendRes = $redpackRes = $this->recordRedpack($redpackData);
		}
		else
		{
			$sendRes = FALSE;
		}
		return $sendRes;
    }

    // 记录红包
    public function recordRedpack($redpackData)
    {
        $fields = array_keys($redpackData);
        $redpackSql = "insert cp_redpack_log(" . implode(',', $fields) . ", created)values(" . implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())";
        return $this->db->query($redpackSql, $redpackData);
    }

    // 获取红包信息
    public function getRedpackInfo($rid)
    {
    	$sql = "SELECT id, aid, money, p_type, use_desc, use_params FROM cp_redpack WHERE id = ?";
		return $this->db->query($sql, array($rid))->getRow();
    }

    // 活动按钮文案
    public function getBtnMsg($activityInfo)
    {
    	if($activityInfo['startTime'] > date('Y-m-d H:i:s'))
    	{
    		$btnMsg = "活动将于" . date('m-d H:i', strtotime($activityInfo['startTime'])) . "开始";
    	}
    	elseif($activityInfo['endTime'] < date('Y-m-d H:i:s'))
    	{
    		$btnMsg = "本期活动已结束";
    	}
    	else
    	{
    		$btnMsg = "下单" . ParseUnit($activityInfo['buyMoney'], 1) . "元，不中全赔";
    	}
    	return $btnMsg;
    }

    // 开赛时间
    public function getKstime($date)
    {
    	$nowTime = strtotime(date('Y-m-d'));
        $endTime = strtotime(date('Y-m-d', strtotime($date)));
        $dif = intval(($endTime-$nowTime)/3600/24);
        $dayInfo = array('今天', '明天');

        $info = '';
        if(isset($dayInfo[$dif]))
        {
            $info .= $dayInfo[$dif] . date('H:i', strtotime($date));
        }
        else
        {
            $info .= date('m-d H:i', strtotime($date));
        }
        $info .= '开赛';
        return $info;
    }
}
