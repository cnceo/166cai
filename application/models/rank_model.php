<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 中奖排行榜 - 模型层
 */

class Rank_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	private $limits = 5000;

	// 获取标签配置信息
	public function getConfigData()
	{
		$sql = "SELECT id, plid, pissue, lids FROM cp_win_rank_config WHERE last_statistics < statistics_end_time AND start_time <= now() AND cstate = 0 AND status = 0 ORDER BY id LIMIT 10";
		return $this->db->query($sql)->getAll();
	}

	// 全站用户数
	public function getAllUser()
	{
		$sql = "SELECT max(id) FROM cp_user_register";
		return $this->slave->query($sql)->getOne();
	}

	// 统计用户
	public function calConfigUser($params, $limitType, $counts)
	{
        $config = $this->getCinfigDetail($params['plid'], $params['pissue']);

        if(empty($config))
        {
            $result = array(
                'status'    =>  FALSE,
                'message'   =>  '活动配置信息错误',
            );
            return $result;
        }

        // 更新统计状态
        $curDate = date("Y-m-d H:i:s");
        // 新增逻辑 11选5系列、快3系列 提前40分钟，竞彩系列提前1小时停止统计,更新统计时间
        $ahead = ($config['plid'] == '3') ? 60 : 40;
        if($curDate >= date('Y-m-d H:i:s', (strtotime($config['statistics_end_time']) - $ahead * 60)))
        {
        	$result = array(
                'status'    =>  FALSE,
                'message'   =>  '停止统计',
            );
        	return $result;
        }

        $updateRes = $this->updateConfigStatus($config, $status);
        if($updateRes <= 0)
        {
        	$result = array(
                'status'    =>  FALSE,
                'message'   =>  '活动配置信息错误',
            );
            return $result;
        }

        // 累计所有用户
        // $this->AddupUser($config, $counts);

        $limit = $limitType[$config['plid']] ? $limitType[$config['plid']] : 3000;

        // 仅统计前几千名用户
        $allUsers = $this->collectUser($config, $limit);
        if(!empty($allUsers))
        {
        	$fields = array('plid', 'pissue', 'uid', 'userName', 'money', 'margin', 'created');
	        $bdata['s_data'] = array();
	        $bdata['d_data'] = array();
	        $count = 0;
	        
        	foreach ($allUsers as $items) 
			{
				// 查询指定用户累计出票总额
				// $total = $this->getUserTotalMoney($config, $items['uid']);
				array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
		        array_push($bdata['d_data'], $config['plid']);
		        array_push($bdata['d_data'], $config['pissue']);
		        array_push($bdata['d_data'], $items['uid']);
		        array_push($bdata['d_data'], $items['uname']);
		        array_push($bdata['d_data'], $items['tmoney']);
		        array_push($bdata['d_data'], $items['tmargin']);

		        if(++$count >= 500)
	            {
	                $this->insertAllRankUser($fields, $bdata);
	                $bdata['s_data'] = array();
	                $bdata['d_data'] = array();
	                $count = 0;
	            }
			}

			if(!empty($bdata['s_data']))
	        {
	            $this->insertAllRankUser($fields, $bdata);
	            $bdata['s_data'] = array();
	            $bdata['d_data'] = array();
	            $count = 0;
	        }
        }

        // 统计排行
        $info = $this->getRandUser($config, $limit);

        $prizeConfig = $this->getPrizeConfig($config);
        $fields = array('plid', 'pissue', 'rankId', 'uid', 'userName', 'money', 'margin', 'addMoney', 'created');
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        $count = 0;
        for($i = 1; $i <= $limit; $i++) 
        { 
        	$index = $i - 1;
        	array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, now())");
	        array_push($bdata['d_data'], $config['plid']);
	        array_push($bdata['d_data'], $config['pissue']);
	        array_push($bdata['d_data'], $i);
	        array_push($bdata['d_data'], (!empty($info[$index]['uid']) ? $info[$index]['uid'] : 0));
	        array_push($bdata['d_data'], (!empty($info[$index]['userName']) ? $info[$index]['userName'] : ''));
	        array_push($bdata['d_data'], (!empty($info[$index]['money']) ? $info[$index]['money'] : 0));
	        array_push($bdata['d_data'], (!empty($info[$index]['margin']) ? $info[$index]['margin'] : 0));
	        // 实际奖励金额
	        if(!empty($info[$index]['uid']))
	        {
	        	$addMoney = $this->getAddMoney($i, $prizeConfig, $info[$index]['uid']);
	        }
	        else
	        {
	        	$addMoney = 0;
	        }
	        
	        array_push($bdata['d_data'], $addMoney);

	        if(++$count >= 500)
            {
                $this->insertRankUser($fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
                $count = 0;
            }
        }

        if(!empty($bdata['s_data']))
        {
            $this->insertRankUser($fields, $bdata);
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;
        } 

        // 汇总排行
        $total = $this->getTotalConfig($config);
        $sql = "UPDATE cp_win_rank_config SET last_statistics = ?, status = 0, totalNum = ?, totalMoney = ?, totalMargin = ? WHERE plid = ? AND pissue = ?";
        $this->db->query($sql, array($curDate, $total['totalNum'], $total['totalMoney'], $total['totalMargin'], $config['plid'], $config['pissue']));

        $result = array(
            'status'    =>  TRUE,
            'message'   =>  '统计完成',
        );
        return $result;
	}

	public function getCinfigDetail($plid, $pissue)
	{
		$sql = "SELECT id, plid, pissue, lids, start_time, end_time, statistics_end_time, imgUrl, rule, extra, cstate, created FROM cp_win_rank_config WHERE plid = ? AND pissue = ? AND cstate = 0 for update";
        return $this->db->query($sql, array($plid, $pissue))->getRow();
	}

	public function updateConfigStatus($config, $status)
	{
		$sql = "UPDATE cp_win_rank_config SET status = 1 WHERE plid = ? AND pissue = ? AND status = 0 AND cstate = 0";
		$this->db->query($sql, array($config['plid'], $config['pissue']));
		return $this->db->affected_rows();
	}

	public function AddupUser($config, $counts)
	{
		$start = 0;
		$end = $this->limits;

		$fields = array('plid', 'pissue', 'uid', 'userName', 'money', 'margin', 'created');
        $bdata['s_data'] = array();
        $bdata['d_data'] = array();
        $count = 0;

		while ($end <= ($counts + $this->limits)) 
		{
			$users = $this->getConfigUser($config, $start, $end);
			if(!empty($users))
			{
				foreach ($users as $items) 
				{
					// 查询指定用户累计出票总额
					$total = $this->getUserTotalMoney($config, $items['uid']);
					array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, now())");
			        array_push($bdata['d_data'], $config['plid']);
			        array_push($bdata['d_data'], $config['pissue']);
			        array_push($bdata['d_data'], $items['uid']);
			        array_push($bdata['d_data'], $items['uname']);
			        array_push($bdata['d_data'], $total['tmoney']);
			        array_push($bdata['d_data'], $items['tmargin']);

			        if(++$count >= 500)
		            {
		                $this->insertAllRankUser($fields, $bdata);
		                $bdata['s_data'] = array();
		                $bdata['d_data'] = array();
		                $count = 0;
		            }
				}

				if(!empty($bdata['s_data']))
		        {
		            $this->insertAllRankUser($fields, $bdata);
		            $bdata['s_data'] = array();
		            $bdata['d_data'] = array();
		            $count = 0;
		        }
			}
			$start += $this->limits;
			$end += $this->limits;
		}
	}

	public function getConfigUser($config, $start, $end)
	{
		$sql = "SELECT o.uid, u.uname, SUM(o.margin) AS tmargin, SUM(o.money) AS tmoney FROM cp_orders AS o FORCE INDEX (created) LEFT JOIN cp_user AS u ON o.uid = u.uid WHERE o.created >= ? AND o.created <= ? AND o.lid in ({$config['lids']}) AND o.uid > ? AND o.uid <= ? AND o.status = 2000 AND o.orderType <> 4 GROUP BY o.uid";
		return $this->slave->query($sql, array($config['start_time'], $config['end_time'], $start, $end))->getAll();
	}

	public function getUserTotalMoney($config, $uid)
	{
		$sql = "SELECT uid, SUM(money) AS tmoney FROM `cp_orders` FORCE INDEX (created) WHERE created >= ? AND created <= ? AND uid = ? AND lid IN ({$config['lids']}) AND status >= 500 AND status <> 600 AND orderType <> 4;";
		return $this->slave->query($sql, array($config['start_time'], $config['end_time'], $uid))->getRow();
	}

	public function getRandUser($config, $limit)
	{
		$sql = "SELECT plid, pissue, uid, userName, money, margin FROM cp_win_rank_detail WHERE plid = ? AND pissue = ? ORDER BY margin DESC, money DESC LIMIT " . $limit;
		return $this->db->query($sql, array($config['plid'], $config['pissue']))->getAll();
	}

	// 总排名
	public function insertAllRankUser($fields, $bdata)
	{
		$sql = "INSERT cp_win_rank_detail(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . " on duplicate key update userName = values(userName), money = values(money), margin = values(margin), addMoney = values(addMoney)";
        $this->db->query($sql, $bdata['d_data']);
	}

	// 部分前排名
	public function insertRankUser($fields, $bdata)
	{
		$sql = "INSERT cp_win_rank_user(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']) . " on duplicate key update uid = values(uid), userName = values(userName), money = values(money), margin = values(margin), addMoney = values(addMoney)";
        $this->db->query($sql, $bdata['d_data']);
	}

	public function getTotalConfig($config)
	{
		$sql = "SELECT COUNT(DISTINCT(uid)) AS totalNum, SUM(margin) AS totalMargin, SUM(money) AS totalMoney FROM cp_win_rank_detail WHERE plid = ? AND pissue = ?";
		return $this->db->query($sql, array($config['plid'], $config['pissue']))->getRow();
	}

	public function getPrizeConfig($config)
	{
		return json_decode($config['extra'], true);
	}

	public function getAddMoney($rankId, $prizeConfig, $uid = 0)
	{
		$addMoney = 0;
		if($uid && !empty($prizeConfig))
		{
			foreach ($prizeConfig as $items) 
			{
				if($items['min'] <= $rankId && $items['max'] >= $rankId)
				{
					$addMoney = $items['money'];
					break;
				}
			}
		}
		return $addMoney;
	}

	public function getConfigPrize()
	{
		$sql = "SELECT id, plid, pissue, lids FROM cp_win_rank_config WHERE statistics_end_time < NOW() AND cstate = 0 AND status = 0 ORDER BY id LIMIT 10";
		return $this->db->query($sql)->getAll();
	}

	// 派奖
	public function sendPrizeUser($config)
	{
		// 开启事务
		$this->db->trans_start();

		$info = $this->getCinfigDetail($config['plid'], $config['pissue']);

		if($info['status'] == '1')
		{
			$this->db->trans_rollback();
			$result = array(
                'status'    =>  FALSE,
                'message'   =>  '活动状态错误',
            );
            return $result;
		}

		// 总人数
		$userArr = array();
		$count = 0;
		$userCount = $this->getCountAddUser($config);

		if($userCount > 0)
		{
			// 分批处理
			$start = 0;
			$users = $this->getAddUser($config, $start, 500);
			while (!empty($users)) 
			{
				$start ++;
				foreach ($users as $items) 
				{
					$handle = $this->addMoney($items, $config);
					if($handle['status'])
					{
						$count ++;
						$totalAdd += $handle['addMoney'];
						array_push($userArr, $items['uid']);
					}
				}
				$users = $this->getAddUser($config, $start, 500);
			}

			if($count == $userCount)
			{
				$addRes = TRUE;
			}
			else
			{
				$addRes = FALSE;
			}
		}
		else
		{
			$addRes = TRUE;
		}

		// 更新 cp_win_rank_config 加奖总额 派奖状态
		$this->db->query("UPDATE cp_win_rank_config SET totalAdd = ?, cstate = 1 WHERE plid = ? AND pissue = ? AND cstate = 0", array($totalAdd, $config['plid'], $config['pissue']));
		$updateRes = $this->db->affected_rows();

		if($addRes && $updateRes)
		{
			$this->db->trans_complete();
			$result = array(
                'status'    =>  TRUE,
                'message'   =>  '派奖完成',
            );
		}
		else
		{
			$this->db->trans_rollback();
			$result = array(
                'status'    =>  FALSE,
                'message'   =>  '派奖失败',
            );
            // 回滚钱包
			if(!empty($userArr))
			{
				foreach ($userArr as $uid)
				{
					// 刷新钱包
    				$this->freshWallet($uid);
				}
			}
		}
		return $result;
	}

	public function getCountAddUser($config)
	{
		$sql = "SELECT count(*) FROM cp_win_rank_user WHERE plid = ? AND pissue = ? AND uid > 0 AND addMoney > 0 AND cstate = 0";
		return $this->db->query($sql, array($config['plid'], $config['pissue']))->getOne();
	}

	public function getAddUser($config, $start, $limit)
	{
		$sql = "SELECT rankId, uid, userName, addMoney FROM cp_win_rank_user WHERE plid = ? AND pissue = ? AND uid > 0 AND addMoney > 0 AND cstate = 0 ORDER BY id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->db->query($sql, array($config['plid'], $config['pissue']))->getAll();
	}

	public function getSmsUser($config, $start, $limit)
	{
		$sql = "SELECT rankId, uid, userName, addMoney FROM cp_win_rank_user WHERE plid = ? AND pissue = ? AND uid > 0 AND addMoney > 0 AND cstate = 1 ORDER BY id ASC LIMIT " . $start * $limit . ", $limit";
		return $this->db->query($sql, array($config['plid'], $config['pissue']))->getAll();
	}

	// 获得余额
	public function getUserMoney($uid)
	{
		return $this->db->query('SELECT money, blocked, must_cost, dispatch FROM cp_user WHERE uid = ? for update', array($uid))->getRow();
	}

	public function addMoney($user, $config)
	{
		if($user['addMoney'] > 0)
		{
			// 用户信息锁表
			$uinfo = $this->getUserMoney($user['uid']);

			// 更新钱包
			$this->db->query("UPDATE cp_user SET money = money + ? WHERE uid = ?", array($user['addMoney'], $user['uid']));
			$res1 = $this->db->affected_rows();

			// 更新流水
			$wallet_log = array(
	            'uid'       =>	$user['uid'],
	            'money'     =>	$user['addMoney'],
	            'ctype'     =>	9,	// 系统奖金派送
	            'additions'	=>	'',
	            'trade_no'  =>	$this->tools->getIncNum('UNIQUE_KEY'),
	            'umoney'    =>	($uinfo['money'] + $user['addMoney']),
	            'must_cost' =>	0,
	            'dispatch'  =>	0,
	            'mark'      =>	'1',
	            'status'    =>  0,	// 默认
	            'orderId'   =>	'',
	            'subscribeId'   =>	'',
	            'content'   =>	'中奖排行榜奖励',
	        );
			$res2 = $this->db->query("insert cp_wallet_logs(". implode(',', array_keys($wallet_log)) .', created) values('. implode(',', array_map(array($this, 'maps'), $wallet_log)) .', now())', $wallet_log);

			// 更新 cp_win_rank_user 派奖状态
			$this->db->query("UPDATE cp_win_rank_user SET cstate = 1 WHERE plid = ? AND pissue = ? AND uid = ? AND cstate = 0", array($config['plid'], $config['pissue'], $user['uid']));
			$userRes = $this->db->affected_rows();

			// 更新成本
			$this->load->model('capital_model');
			$capitalRes = $this->capital_model->recordCapitalLog(2, $wallet_log['trade_no'], 'redpack', $user['addMoney'], '2', false);

			if($res1 && $res2 && $userRes && $capitalRes)
			{
				// 刷新钱包
		        $this->freshWallet($user['uid']);
				$result = array(
					'status'	=>	TRUE,
					'addMoney'	=>	$user['addMoney'],
				);
			}
			else
			{
				$result = array(
					'status'	=>	FALSE,
					'addMoney'	=>	$user['addMoney'],
				);
			}
		}
		else
		{
			$result = array(
				'status'	=>	TRUE,
				'addMoney'	=>	0,
			);
		}
		return $result;
	}

	// 刷新钱包
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
			$wallet = $this->db->query('select m.money, m.blocked, m.dispatch from cp_user m left join cp_user_info n on m.uid = n.uid where m.uid = ?', array($uid))->getRow();
			return $this->cache->redis->hMSet($ukey, $wallet);
		}
    }

    // 查询已派奖未推送活动
    public function getPrizedConfig()
    {
    	$sql = "SELECT id, plid, pissue, lids FROM cp_win_rank_config WHERE statistics_end_time < now() AND cstate = 1 AND push_status = 0 ORDER BY id ASC LIMIT 10";
		return $this->db->query($sql)->getAll();
    }

    public function sendSmsUser($config)
    {
    	$this->load->model('user_model');
    	$this->load->library('mipush');
    	// 分批处理
		$start = 0;
		$users = $this->getSmsUser($config, $start, 500);
		while (!empty($users)) 
		{
			$start ++;
			foreach ($users as $items) 
			{
				// 短信
				$vdatas = array(
		    		'#UNAME2#'	=>	$items['userName'], 
		    		'#CONTENT#'	=> 	$items['rankId'], 
		    		'#MONEY#' 	=> 	$items['addMoney'], 
		    	);
		    	$this->user_model->sendSms($items['uid'], $vdatas, 'win_rank', null, '127.0.0.1', '195');

		    	// 推送
		    	$pushData = array(
                    'type'      =>  'open_app',
                    'uid'       =>  $items['uid'],
                    'title'     =>  '您有彩金到账啦',
                    'content'   =>  '恭喜大神排行榜获得第' . $items['rankId'] . '名，小六特为您送上' . number_format(ParseUnit($items['addMoney'], 1), 2) . '元彩金，点击查看>>',
                    'time_to_live' =>  10 * 60 * 1000,
                );
                $this->mipush->index('user_com', $pushData);	
			}
			$users = $this->getSmsUser($config, $start, 500);
		}

		// 更新推送状态
		$this->db->query("UPDATE cp_win_rank_config SET push_status = 1 WHERE plid = ? AND pissue = ? AND push_status = 0", array($config['plid'], $config['pissue']));
    }

    public function collectUser($config, $limit)
    {
    	$sql = "SELECT o.uid, u.uname, SUM(o.margin) AS tmargin, SUM(o.money-o.failMoney) AS tmoney FROM cp_orders AS o FORCE INDEX (created) LEFT JOIN cp_user AS u ON o.uid = u.uid WHERE o.created >= ? AND o.created <= ? AND o.lid in ({$config['lids']}) AND o.status >= 500 AND o.orderType <> 4 GROUP BY o.uid ORDER BY tmargin DESC, tmoney DESC LIMIT " . $limit;
		return $this->slave->query($sql, array($config['start_time'], $config['end_time']))->getAll();
    }
}
