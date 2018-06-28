<?php

/**
 * 用户标签 获取标签内用户集
 */

class Capture
{
	// 筛选条件映射
	private $scope_map = array(
		'lid'			=>	'1',
		'orderType'		=>	'2',
		'channel'		=>	'4',
		'platform'		=>	'8',
		'total_day'		=>	'16',
		'total_buy_num'	=>	'32',
		'last_buy_time'	=>	'64',
		'total_money'	=>	'128',
	);

	// 后台订单类型与实际查询订单类型映射
	private $orderMaps = array(
		'orders'	=>	array(
			0	=>	'0,3',
			1	=>	'1,6',
			2	=>	'4',
		),
		'united'	=>	array(
			3	=>	'1,2,3',
		),
	);

	// 查询分片数
	private $limit = 1000;

	// ZSET键名前缀
	private $redisKeyName = 'usertag:';
	
	private $CI;
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('tag_model');
		$this->CI->load->driver('cache', array('adapter' => 'redis'));
	}

	// 脚本 - 标签数据获取主函数
	public function index($params)
	{
		if(empty($params['id']) || empty($params['conditions']) || empty($params['runtime']))
		{
			return;
		}

		// 检查重复统计
		if(empty($params['basetime']) && date("Y-m-d") <= date("Y-m-d", strtotime($params['runtime'])))
		{
			return;
		}

		// 上次执行时间
		$params['lasttime'] = $params['runtime'];

		// 执行基准时间 默认脚本执行当前时间
		$basetime = !empty($params['basetime']) ? $params['basetime'] : date("Y-m-d H:i:s");
		$params['rundate'] = date("Y-m-d", strtotime($basetime));
		$params['runtime'] = date("Y-m-d H:i:s", strtotime($basetime));

		$conditions = json_decode($params['conditions'], true);

		// 涉及表及字段条件汇总
		$filters = $this->getFilters($params, $conditions['filter']);
		// 统计类条件汇总
		$statistics = $this->getStatistics($params, $conditions['having']);
		// 日期类 date 条件 分日期查询
		$dateCon = $this->getDateArr($params, $conditions['date']);

		if(!empty($dateCon['date']))
		{
			foreach ($dateCon['date'] as $date) 
			{
				$this->handleUsersLogic($params, $date, $filters['data']);
			}

			// 汇总数据
			if(!empty($filters['groups']))
			{
				// 分逻辑条件入 ZSET 缓存求交集，并分片插入临时表，根据 having 条件汇总至 cp_tag_user
				$this->calculateTagLogic($params['rundate'], $params['id'], $filters['groups'], $statistics['sqlCons']);
			}
			else
			{
				// 根据 having 条件直接汇总至 cp_tag_user
				$this->collectTagUser($params, $statistics['sqlCons']);
			}	
		}

		// 非重跑 更新runtime
		if($params['runtime'] > $params['lasttime'])
		{
			$this->CI->tag_model->updateTagName($params, $dateCon['dateType']);
		}
	}

	// 涉及表及字段条件汇总
	public function getFilters($params, $filter)
	{
		// 逻辑类 filter 条件
		$filters = array();
		$allCons = array();		// 所有查询条件
		$sqlCons = array();		// 必要查询条件集 包含逻辑 or 
		$groups = array();		// 逻辑 and 查询条件集
		if(!empty($filter))
		{
			foreach ($filter as $name => $items) 
			{
				if(!empty($this->scope_map[$name]) && (intval($params['scope']) & intval($this->scope_map[$name])) && !empty($items) && $items['val'] !== '')
				{
					if(strpos($items['logic'], 'and') !== FALSE && strpos($items['val'], ',') !== FALSE)
					{
						$groups[$name] = explode(',', $items['val']);
					}
					else
					{
						$sqlCons[$name] = explode(',', trim($items['val']));
					}
					$allCons[$name] = explode(',', $items['val']);
				}
			}
		}

		// 根据 orderType 区分表
		$tableArr = $this->getSelectTables($allCons);

		// 基本查询条件
		$select = array(
			'lid'			=>	!empty($sqlCons['lid']) ? implode(',', $sqlCons['lid']) : '',
			'orderType'		=>	'',
			'buyPlatform'	=>	!empty($sqlCons['platform']) ? implode(',', $sqlCons['platform']) : '',
			'channel'		=>	!empty($sqlCons['channel']) ? implode(',', $sqlCons['channel']) : '',
		);

		// 按表 - 按表条件汇总
		foreach ($tableArr as $table => $orderTypes) 
		{
			if(!empty($groups))
			{
				// 逻辑 & 关系排列组合
				$groupTpl = $groups;
				// $orderTypes 为后台查询的订单类型
				if(!empty($groupTpl['orderType'])) $groupTpl['orderType'] = $orderTypes;
				$combine = $this->combineLogicGroups($groupTpl);

				// 反转数据
				$fieldsData = array_flip($combine['fields']);
				// 根据实际的排列组合数来组装需要查询的子条件模块
				foreach ($combine['coms'] as $k => $items) 
				{
					// 默认的四种条件
					$selectTpl = $select;
					$tplArr = explode('=', $items);
					$baseOrderType = (isset($fieldsData['orderType'])) ? $tplArr[$fieldsData['orderType']] : '0';
					foreach ($tplArr as $key => $value) 
					{
						$name = str_replace('platform', 'buyPlatform', $combine['fields'][$key]);

						// 订单类型实际映射关系
						$selectTpl['baseOrderType'] = $baseOrderType;
						$selectTpl[$name] = $value;
						// orderType 作为排列组合条件之一
						if(in_array('orderType', $combine['fields']))
						{
							$selectTpl['orderType'] = $this->orderMaps[$table][$baseOrderType];
						}
						else
						{
							$types = array();
							// 取后台查询的订单类型整合
							foreach ($orderTypes as $detail) 
							{
								$types[] = $this->orderMaps[$table][$detail];
							}
							$selectTpl['orderType'] = implode(',', $types);
						}
					}
					// 合买无 channel 字段
					if($table == 'united') $selectTpl['channel'] = '';
					$filters[$table]['data'][$k] = $selectTpl;
				}
				$filters[$table]['groups'] = $combine;
			}
			else
			{
				$selectTpl = $select;
				$selectTpl['orderType'] = $orderTypes;
				// 实际映射关系
				$orderTypeArr = array();
				foreach ($selectTpl['orderType'] as $ordertype) 
				{
					$orderTypeArr[] = $this->getActualVal('orderType', $table, trim($ordertype));
				}
				$selectTpl['orderType'] = implode(',', $orderTypeArr);
				
				// 合买无 channel 字段
				if($table == 'united') $selectTpl['channel'] = '';
				$filters[$table]['data'][0] = $selectTpl;
				$filters[$table]['groups'] = array();
			}
		}
		return array('data' => $filters, 'groups' => (!empty($groups)) ? $this->combineLogicGroups($groups) : array());
	}

	// 逻辑关系排列组合
	public function combineLogicGroups($data)
	{
		$groups = array();
		if(!empty($data))
		{
			$groups = array(
				'fields'	=>	array_keys($data),
				'coms'		=>	$this->combineGroup(array_values($data), count($data)),
			);  
		}
		return $groups;
	}

	// 排列组合
	public function combineGroup($data, $len)
    {
        if($len == 1) 
        {
            return $data[0];
        } 

        $tempArr = $data;
        unset($tempArr[0]); 
        $groups = array();
        $len2 = count($data);
        $result = $this->combineGroup(array_values($tempArr), ($len - 1));
        foreach ($data[$len - $len2] as $items) 
        {
            foreach ($result as $val) 
            {
                if (is_array($val)) 
                {
                    array_unshift($val, $items);
                    $groups[] = array_values($val);
                } 
                else 
                {
                    $groups[] = $items . '=' . $val; 
                }
            }
        }
        return $groups;
    }

    // 实际表结构 订单类型映射
    public function getActualVal($name, $table, $value)
    {
    	if($name == 'orderType')
    	{
    		$value = !empty($this->orderMaps[$table][$value]) ? $this->orderMaps[$table][$value] : $value;
    	}
    	return $value;
    }

    // 统计类条件汇总
    public function getStatistics($params, $having)
    {
    	$sqlCons = "";
		foreach ($having as $name => $items) 
		{
			if(!empty($this->scope_map[$name]) && (intval($params['scope']) & intval($this->scope_map[$name])) && !empty($items))
			{
				// 组装SQL
				if($name == 'last_buy_time')
				{
					// 最近购彩时间格式为 天数 或者 时间区间
					if(is_numeric($items))
					{
						$start = date("Y-m-d 00:00:00", strtotime("{$params['runtime']} -{$items} day"));
						$sqlCons .= " AND t1.{$name} < '{$start}'";
					}
					else
					{
						preg_match_all('/(\d{4}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2})/', $items, $matches);
						if(!empty($matches[1]) && count($matches[1]) == 2)
						{
							$sqlCons .= " AND t1.{$name} >= '{$matches[1][0]}' AND t1.{$name} <= '{$matches[1][1]}'";
						}
					}
				}
				elseif (strpos($items, '-') !== FALSE) 
				{
					$rangeArr = explode('-', $items);
					// 针对 * 处理
					if(strpos($items, '*') !== FALSE)
					{
						if(strpos($rangeArr[1], '*') === FALSE)
						{
							$sqlCons .= " AND t1.{$name} <= '{$rangeArr[1]}'";
						}
						else
						{
							$sqlCons .= " AND t1.{$name} >= '{$rangeArr[0]}'";
						}
					}
					else
					{
						$sqlCons .= " AND t1.{$name} >= '{$rangeArr[0]}' AND t1.{$name} <= '{$rangeArr[1]}'";
					}
				}
				else
				{
					$sqlCons .= " AND t1.{$name} >= '{$items}'";
				}
			}
		}
		return array('sqlCons' => $sqlCons);
    }

    // 解析查询表 - 后台订单类型
    public function getSelectTables($data = array())
    {
    	$tables = array();
    	if(!empty($data['orderType']))
    	{
    		// 后台查询类型：0 - 自购 1 - 追号 2 - 发起 3 - 参与
    		foreach ($data['orderType'] as $type) 
    		{
    			if(in_array($type, array(0, 1, 2)))
    			{
    				$tables['orders'][] = $type;
    			}
    			elseif($type == '3' && empty($data['channel']))
    			{
    				// 参与合买
    				$tables['united'][] = $type;
    			}
    		}
    	}
    	else
    	{
    		// 默认查询的订单类型
    		$tables = array(
    			'orders'	=>	array(0, 1, 2),	// 自购、追号、发起
    			// 'united'	=>	array(3),		// 认购
    		);
    	}
    	return $tables;
    }

	// 按天获取时间分片
	public function getDateArr($params, $time)
	{
		$dateType = 0;
		$dateArr = array();
		if(intval($time['range']) > 0 && intval($time['range']) <= 360)
		{
			for ($i = intval($time['range']); $i > 0; $i--) 
			{ 
				$data = array(
					'start'	=>	date("Y-m-d 00:00:00", strtotime("{$params['runtime']} -{$i} day")),
					'end'	=>	date("Y-m-d 23:59:59", strtotime("{$params['runtime']} -{$i} day")),
				);
				array_push($dateArr, $data);
			}
		}
		elseif(strtotime($time['start']) && strtotime($time['end']) && $time['start'] < $time['end'])
		{
			// 区间类事件类型
			$dateType = 1;
			// 已执行过的区间条件不再执行
			if($params['lasttime'] > '0000-00-00 00:00:00')
			{
				return array('dateType' => $dateType, 'date' => $dateArr);
			}

			if(date("Y-m-d", strtotime($time['start'])) == date("Y-m-d", strtotime($time['end'])))
			{
				// 时分秒容错
				$data = array(
					'start'	=>	(strtotime($time['start']) > strtotime(date("Y-m-d", strtotime($time['start'])))) ? date("Y-m-d H:i:s", strtotime($time['start'])) : date("Y-m-d 00:00:00", strtotime($time['start'])),
					'end'	=>	(strtotime($time['end']) > strtotime(date("Y-m-d", strtotime($time['end'])))) ? date("Y-m-d H:i:s", strtotime($time['end'])) : date("Y-m-d 00:00:00", strtotime($time['end'])), $time['end'],
				);
				array_push($dateArr, $data);
			}
			else
			{
				$day = ceil((strtotime($time['end']) - strtotime($time['start'])) / 86400);

				$start = date("Y-m-d 00:00:00", strtotime($time['start']));
				$end = date("Y-m-d 23:59:59", strtotime($time['start']));

				for ($i = 0; $i < $day; $i++) 
				{ 	
					$data = array(
						'start'	=>	($time['start'] >= $start) ? $time['start'] : $start,
						'end'	=>	($time['end'] >= $end) ? $end : $time['end'],
					);
					array_push($dateArr, $data);

					$start = date("Y-m-d 00:00:00", strtotime("$start + 1 day"));
					$end = date("Y-m-d 23:59:59", strtotime($start));
				}
			}
		}
		return array('dateType' => $dateType, 'date' => $dateArr);
	}

	// 按天获取逻辑条件数据 同时汇总用户数据
	public function handleUsersLogic($params, $date, $filters)
	{
		if(empty($filters))
		{
			return;
		}

		foreach ($filters as $table => $items) 
		{
			$this->getDataByTable($params, $date, $table, $items);
		}
	}

	// 按表类型组装 SQL
	public function getDataByTable($params, $date, $table, $filters)
	{	
		$groups = $filters['groups'];
		foreach ($filters['data'] as $index => $items) 
		{
			$this->getDataByFilters($params, $date, $table, $index, $items, $groups);
		}
	}

	// 查询结果入库
	public function getDataByFilters($params, $date, $table, $index, $selects, $groups)
	{
        // 组装 SQL
        $sqlCons = $this->getSqlCons($selects);

		$start = 0;
		$orders = $this->CI->tag_model->getDataByTable($date, $table, $sqlCons, $start, $this->limit);
		while (!empty($orders)) 
		{
			$start ++;

			// 入库 cp_tag_user_collect 累加汇总
            $fields = array('date', 'tag_id', 'uid', 'total_money', 'total_day', 'total_buy_num', 'last_buy_time', 'statistic_time', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

			foreach ($orders as $items) 
			{
				array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, now())");
                array_push($bdata['d_data'], $params['rundate']);
                array_push($bdata['d_data'], $params['id']);
                array_push($bdata['d_data'], $items['uid']);
                array_push($bdata['d_data'], $items['money']);
                array_push($bdata['d_data'], 1);
                array_push($bdata['d_data'], 1);
                array_push($bdata['d_data'], $items['created']);
                array_push($bdata['d_data'], date("Y-m-d", strtotime($items['created'])));
			}

			if(!empty($bdata['s_data']))
            {
                $this->CI->tag_model->insertTagUserCollect($fields, $bdata);
                $bdata['s_data'] = array();
            	$bdata['d_data'] = array();
            }

            if(!empty($groups))
            {
            	// 入库 cp_tag_logic_operator 去重汇总
            	$logicFields = array('date', 'tag_id', 'uid', 'ufiled', 'lid', 'orderType', 'platform', 'channel', 'created');
	            $logicBdata['s_data'] = array();
	            $logicBdata['d_data'] = array();

	            foreach ($orders as $items) 
				{
					array_push($logicBdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, now())");
	                array_push($logicBdata['d_data'], $params['rundate']);
	                array_push($logicBdata['d_data'], $params['id']);
	                array_push($logicBdata['d_data'], $items['uid']);
	                array_push($logicBdata['d_data'], $groups['coms'][$index]);
	                $lid = in_array('lid', $groups['fields']) ? $selects['lid'] : '0';
	                $orderType = in_array('orderType', $groups['fields']) ? $selects['baseOrderType'] : '0';
	                $platform = in_array('platform', $groups['fields']) ? $selects['buyPlatform'] : '0';
	                $channel = in_array('channel', $groups['fields']) ? $selects['channel'] : '0';
	                array_push($logicBdata['d_data'], $lid);
	                array_push($logicBdata['d_data'], $orderType);
	                array_push($logicBdata['d_data'], $platform);
	                array_push($logicBdata['d_data'], $channel);
				}

				if(!empty($logicBdata['s_data']))
	            {
	                $this->CI->tag_model->insertTagLogicOperator($logicFields, $logicBdata);
	                $logicBdata['s_data'] = array();
	            	$logicBdata['d_data'] = array();
	            }
            }
            
			$orders = $this->CI->tag_model->getDataByTable($date, $table, $sqlCons, $start, $this->limit);
		}
	}

	// 组合 SQL 条件
	public function getSqlCons($filters)
	{
		unset($filters['baseOrderType']);
		$sqlCons = "";
		if(!empty($filters))
		{
			foreach ($filters as $field => $val) 
			{
				if($val !== '')
				{
					$sqlCons .= " AND " . $field . " IN (" . $val . ")";
				}
			}
		}
		return $sqlCons;
	}

	// 逻辑关系处理
	public function calculateTagLogic($date, $tag_id, $groups, $having)
	{
		if(!empty($groups))
		{
			$redisArr = array();
			foreach ($groups['coms'] as $items) 
			{
				// 返回 ZSET 键值
				$logicData = $this->getDataByLogic($date, $tag_id, $items, $groups['fields']);
				if(!empty($logicData['redisKey']))
				{
					array_push($redisArr, $logicData['redisKey']);
				}
			}

			if(!empty($redisArr))
			{
				// ZSET 求交集
				$interRedis = $this->redisKeyName . implode(':', array($date, $tag_id));
				$interData = $this->interZset($interRedis, $redisArr);
				array_push($redisArr, $interRedis);

				// 分片插入临时表并联表查询
				$counts = $this->countZset($interRedis);
				$start = 0;
				while ($counts > 0 && $start < $counts) 
        		{
        			$users = $this->rangeZset($interRedis, $start, $start + $this->limit - 1);
        			if(!empty($users))
        			{
        				// 交集插入临时表并联表查询结果集再入实际标签用户
        				$this->handleLogicUser($date, $tag_id, $users, $having);
        			}
        			$start = $start + $this->limit;
        		}

				// 删除缓存数据
				foreach ($redisArr as $redisKey) 
				{
					$this->delZset($redisKey);
				}
			}
		}
	}

	// 按指定条件查询数据并计入缓存
	public function getDataByLogic($date, $tag_id, $data, $fields)
	{
		// 组装 SQL
		$sqlCons = " AND date = '{$date}'";
		$sqlCons .= " AND tag_id = " . $tag_id;
		foreach (explode('=', $data) as $key => $val) 
		{
			$sqlCons .= " AND " . $fields[$key] . " = " . $val;
		}

		// ZSET 键
		$redisKey = $this->redisKeyName . implode('=', array($date, $tag_id, $data));

		$start = 0;
		$users = $this->CI->tag_model->getDataByLogic($sqlCons, $start, $this->limit);
		while (!empty($users)) 
		{
			$start ++;
			foreach ($users as $items) 
			{
				// 入临时 ZSET 缓存
				$this->insertZset($redisKey, $items['uid'], $items['uid']);
			}
			$users = $this->CI->tag_model->getDataByLogic($sqlCons, $start, $this->limit);
		}
		return array('redisKey' => $redisKey);
	}

	// ZSET 写入
	public function insertZset($redisKey, $score, $value)
	{
		return $this->CI->cache->redis->zAdd($redisKey, $score, $value);
	}

	// ZSET 交集
	public function interZset($redisKey, $redisKeyArr)
	{
		return $this->CI->cache->redis->zInter($redisKey, $redisKeyArr);
	}

	// ZSET 并集
	public function unionZset($redisKey, $redisKeyArr)
	{
		return $this->CI->cache->redis->zUnion($redisKey, $redisKeyArr);
	}

	// ZSET 个数
	public function countZset($redisKey)
	{
		return $this->CI->cache->zCard($redisKey);
	}

	// ZSET 范围取值
	public function rangeZset($redisKey, $start, $end)
	{
		return $this->CI->cache->redis->zRange($redisKey, $start, $end);
	}

	// 删除 redisKey
	public function delZset($redisKey)
	{
		return $this->CI->cache->redis->delete($redisKey);
	}

	// 交集插入临时表并联表查询结果集再入实际标签用户
	public function handleLogicUser($date, $tag_id, $users, $having)
	{
		if(!empty($users))
		{
			// 清空临时表
			$this->CI->tag_model->truncateTagLogicTpl();
			// 插入临时表
            $fields = array('date', 'tag_id', 'uid', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

			foreach ($users as $uid) 
			{
				array_push($bdata['s_data'], "(?, ?, ?, now())");
                array_push($bdata['d_data'], $date);
                array_push($bdata['d_data'], $tag_id);
                array_push($bdata['d_data'], $uid);
			}

			if(!empty($bdata['s_data']))
            {
                $this->CI->tag_model->insertTagLogicTpl($fields, $bdata);
            }

			// 联表查询满足条件的数据到实际标签用户表
            $this->CI->tag_model->compareTagUser($date, $tag_id, $having);
		}
	}

	// 汇总统计实际标签用户
	public function collectTagUser($params, $sqlCons = '')
	{
		$start = 0;
		$users = $this->CI->tag_model->collectTagUser($params, $sqlCons, $start, $this->limit);
		while (!empty($users)) 
		{
			$start ++;
			// 入库
            $fields = array('date', 'tag_id', 'uid', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();

			foreach ($users as $items) 
			{
				array_push($bdata['s_data'], "(?, ?, ?, now())");
                array_push($bdata['d_data'], $params['rundate']);
                array_push($bdata['d_data'], $params['id']);
                array_push($bdata['d_data'], $items['uid']);
			}

			if(!empty($bdata['s_data']))
            {
                $this->CI->tag_model->insertTagUser($fields, $bdata);
            }

            $users = $this->CI->tag_model->collectTagUser($params, $sqlCons, $start, $this->limit);
		}
	}

	// 标签集群汇总 - 求并集
	public function clusterInter($cluster_id, $conditions = array(), $fileName)
	{
		// 需要并集的数据
// 		$conditions = array(
//             0 => array(
//                 'tag_id'    =>  '1',
//                 'date'      =>  '2018-01-31',
//             ),
//             1 => array(
//                 'tag_id'    =>  '5',
//                 'date'      =>  '2018-01-31',
//             ),
//         );

        $redisArr = array();
		if(!empty($conditions))
		{
			foreach ($conditions as $items) 
			{
				$TagData = $this->getTagUser($items);
				if(!empty($TagData['redisKey']))
				{
					array_push($redisArr, $TagData['redisKey']);
				}
			}
		}

		// 抽象交集方法
		if(!empty($redisArr))
		{
			// ZSET 求并集
			$interRedis = $this->redisKeyName . implode(':', array($cluster_id));
			$interData = $this->unionZset($interRedis, $redisArr);
			array_push($redisArr, $interRedis);

			// 全部取出
			// $users = $this->rangeZset($interRedis, 0, -1);

			// 分片插入临时表并联表查询
			$counts = $this->countZset($interRedis);
			$start = 0;
			
			$this->CI->load->library('excel');
			$this->CI->excel->setActiveSheetIndex(0);
			$this->CI->excel->getActiveSheet()->setCellValue('A1', 'Uid');
			
			while ($counts > 0 && $start < $counts) 
    		{
    			$users = $this->rangeZset($interRedis, $start, $start + $this->limit - 1);
    			if(!empty($users))
    			{
    				// 分片取出的用户处理 TODO
    				// $users = array('1', '2', '3');
        			foreach ($users as $k => $uid) {
                        $this->CI->excel->getActiveSheet()->setCellValue('A'.($start+$k+2), $uid);
                    }
    			}
    			$start = $start + $this->limit;
    		}
    		
    		header('Content-Type: text/csv');
    		header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');
    		header('Cache-Control: max-age=0');
    		 
    		$objWriter = PHPExcel_IOFactory::createWriter($this->CI->excel, 'CSV');
    		$objWriter->save('php://output');

			// 删除缓存数据
			foreach ($redisArr as $redisKey) 
			{
				$this->delZset($redisKey);
			}
		}
	}

	public function getTagUser($params)
	{
		// ZSET 键
		$redisKey = $this->redisKeyName . implode('=', array($params['date'], $params['tag_id']));

		$start = 0;
		$users = $this->CI->tag_model->getTagUserData($params, $start, $this->limit);
		while (!empty($users)) 
		{
			$start ++;
			foreach ($users as $items) 
			{
				// 入临时 ZSET 缓存
				$this->insertZset($redisKey, $items['uid'], $items['uid']);
			}
			$users = $this->CI->tag_model->getTagUserData($params, $start, $this->limit);
		}
		return array('redisKey' => $redisKey);
	}

	// 脚本 - 标签集群更新逻辑
	public function clusterUpdate()
	{
		$clusters = $this->CI->tag_model->getClusterData();
		if(!empty($clusters))
		{
			foreach ($clusters as $items) 
			{
				$this->handleClusterUpdate($items);
			}
		}
	}

	// 更新时间
	public function handleClusterUpdate($params)
	{
		$tag_ids = explode(',', $params['tag_ids']);
		if(!empty($tag_ids))
		{
			$updateDate = array();
			foreach ($tag_ids as $tag_id) 
			{
				$tagInfo = $this->CI->tag_model->getTagLastDate(trim($tag_id));
				$data = array(
					'tag_id'	=>	trim($tag_id),
					'date'		=>	$tagInfo['date'] ? $tagInfo['date'] : '',
				);
				array_push($updateDate, $data);
			}

			if(!empty($updateDate))
			{
				$updateDate = json_encode($updateDate);
				$this->CI->tag_model->updateClusterDate($params['id'], $updateDate);
			}
		}
	}
}
