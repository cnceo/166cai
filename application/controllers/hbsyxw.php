<?php
class Hbsyxw extends MY_Controller
{
	public static $TYPE_MAP = array (
		'rx2' => array (
			'cnName' => '任选二',
			'bonus' => '6元',
			'rule' => array (
				'玩法说明：至少选择2个号码，与开奖号码任意2个数字相同，即中奖6元！',
				'玩法说明：选1个胆码，选2～10个拖码，胆码加拖码不少于3个，单注奖金6元！' 
			) 
		),
		'rx3' => array (
			'cnName' => '任选三',
			'bonus' => '19元',
			'rule' => array (
				'玩法说明：至少选择3个号码，与开奖号码任意3个数字相同，即中奖19元！',
				'玩法说明：选1～2个胆码、2～10个拖码，胆码加拖码不少于4个，单注奖金19元！' 
			) 
		),
		'rx4' => array (
			'cnName' => '任选四',
			'bonus' => '78元',
			'rule' => array (
				'玩法说明：至少选择4个号码，与开奖号码任意4个数字相同，即中奖78元！',
				'玩法说明：选1～3个胆码、2～10个拖码，胆码加拖码不少于5个，单注奖金78元！' 
			) 
		),
		'rx5' => array (
			'cnName' => '任选五',
			'bonus' => '540元',
			'rule' => array (
				'玩法说明：至少选择5个号码，与开奖号码任意5个数字相同，即中奖540元！',
				'玩法说明：选1～4个胆码、2～10个拖码，胆码加拖码不少于6个，单注奖金540元！' 
			) 
		),
		'rx6' => array (
			'cnName' => '任选六',
			'bonus' => '90元',
			'rule' => array (
				'玩法说明：至少选择6个号码，与开奖号码任意5个数字相同，即中奖90元！',
				'玩法说明：选1～5个胆码、2～10个拖码，胆码加拖码不少于7个，单注奖金90元！' 
			) 
		),
		'rx7' => array (
			'cnName' => '任选七',
			'bonus' => '26元',
			'rule' => array (
				'玩法说明：至少选择7个号码，与开奖号码任意5个数字相同，即中奖26元！',
				'玩法说明：选1～6个胆码、2～10个拖码，胆码加拖码不少于8个，单注奖金26元！' 
			) 
		),
		'rx8' => array (
			'cnName' => '任选八',
			'bonus' => '9元',
			'rule' => array (
				'玩法说明：至少选择8个号码，与开奖号码任意5个数字相同，即中奖9元！' 
			) 
		),
		'q1' => array (
			'cnName' => '前一',
			'bonus' => '13元',
			'rule' => array (
				'玩法说明：至少选择1个号码，与开奖号码第1个数字相同，即中奖13元！' 
			) 
		),
		'qzu2' => array (
			'cnName' => '前二组选',
			'bonus' => '65元',
			'rule' => array (
				'玩法说明：至少选择2个号码，与开奖号码的前两位号码相同（顺序不限），即中奖65元！',
				'玩法说明：选1个胆码，选2～10个拖码，胆码加拖码不少于3个，单注奖金65元！' 
			) 
		),
		'qzhi2' => array (
			'cnName' => '前二直选',
			'bonus' => '130元',
			'rule' => array (
				'玩法说明：每位至少选择1个号码，与开奖号码的前两位号码相同且顺序一致，即中奖130元！' 
			) 
		),
		'qzu3' => array (
			'cnName' => '前三组选',
			'bonus' => '195元',
			'rule' => array (
				'玩法说明：至少选择3个号码，与开奖号码的前三位号码相同（顺序不限），即中奖195元！',
				'玩法说明：选1～2个胆码、2～10个拖码，胆码加拖码不少于4个，单注奖金195元！' 
			) 
		),
		'qzhi3' => array (
			'cnName' => '前三直选',
			'bonus' => '1170元',
			'rule' => array (
				'玩法说明：每位至少选择1个号码，与开奖号码的前三位号码相同且顺序一致，即中奖1170元！' 
			) 
		) 
	);

	public function index()
	{
		// 初始化
		$lotteryId = Lottery_Model::HBSYXW;
		$inputGetArr = $this->input->get ( NULL, true );
		$dateStr = str_replace ( '20', '', date ( 'Ymd' ) );
		$flag = false; // 初始化上一期未开奖标志
		
		// 取缓存
		$this->load->driver ( 'cache', array (
				'adapter' => 'redis' 
		) );
		$REDIS = $this->config->item ( 'REDIS' );
		$awardArr = unserialize($this->cache->get ( $REDIS ['HBSYXW_AWARD'] ));
		$miss = unserialize($this->cache->get ( $REDIS ['HBSYXW_MISS'] ));
		$info = json_decode ( $this->cache->get ( $REDIS ['HBSYXW_ISSUE_TZ'] ), true );
		$lastIssue = empty ( $info ['aIssue'] ) ? $info ['lIssue'] ['seExpect'] : $info ['aIssue'] ['seExpect'];
		$followIssues = json_decode ( $this->cache->hGet ( $REDIS ['ISSUE_COMING'], 'HBSYXW' ), true );
		$multi = !empty( $inputGetArr['multi'] ) ? $inputGetArr['multi'] : 1;
		
		$chases = array();
		$issue = 1;
		while (count($chases) < 81 && $issue)
		{
			$issue = each($followIssues);
			$issue = $issue['value'];
			if ($issue && $issue['show_end_time'] > date('Y-m-d H:i:s'))
			{
				$chases [$issue ['issue']] = array (
						'award_time' => $issue ['award_time'],
						'show_end_time' => $issue ['show_end_time'],
						'multi' => $multi,
						'money' => 0
				);
			}
		}
		
		$count = substr($info ['cIssue'] ['seExpect'], -2)-1;
		$rest = 81-$count;
		
		// 生成遗漏、历史开奖数组
		$history = array_slice ( $awardArr, 0, 10 );
		$miss = $this->getMissNumber ( $miss, $history );
		foreach ( $history as $k => $h )
		{
			$kjCal = $this->calculate ( $h ['awardNum'] );
			$history [$k] ['dx'] = $kjCal [1] . ":" . (5 - $kjCal [1]);
			$history [$k] ['jo'] = $kjCal [0] . ":" . (5 - $kjCal [0]);
			$history [$k] ['he'] = array_sum ( explode ( ',', $h ['awardNum'] ) );
			if ($history [$k] ['issue'] == $lastIssue)
			{
				$flag = true;
			}
		}
		if (! $flag)
		{
			$arr = array (
					'issue' => $lastIssue 
			);
			$awardNum = "'正', '在', '开', '奖', '中'";
			array_unshift ( $history, $arr );
			array_pop ( $history );
		} else
		{
			$awardNum = $history [0] ['awardNum'];
		}
		krsort ( $history );
		$history = array_values ( $history );
		reset ( $miss );
		$ms = each ( $miss );
		
		// 获取订单数据
		if (! empty ( $inputGetArr ['orderId'] )) {
            $this->load->model ( 'order_model' );
			$codes = $this->order_model->getCodesById(trim ( $inputGetArr ['orderId'] ));
		} elseif ($this->uid)
		{
			$this->load->model ( 'order_model' );
		}
		if (! empty ( $inputGetArr ['chaseId'] )) {
            $this->load->model ( 'chase_order_model' );
			$codes = $this->chase_order_model->getCodesById(trim ( $inputGetArr ['chaseId'] ));
		}
		if (! empty ( $inputGetArr ['codes'] )) {
			$codes = $inputGetArr ['codes'];
		}
		
		$odatas = array ();
		if ($this->uid)
		{
			$odatas = $this->order_model->getNewOrders ( $this->uid, HBSYXW, 0, 5);
		}
		
		$data = array (
				'syxwType' => 'rx5',
				'typeMAP' => self::$TYPE_MAP,
				'boxCount' => 1,
				'cnName' => '惊喜11选5',
				'enName' => 'hbsyxw',
				'count' => $count,
				'miss' => $ms ['value'],
				'mall' => $miss,
				'info' => $info,
				'history' => $history,
				'awardNum' => $awardNum,
				'chases' => $chases,
				'chaselength' => 10,
				'multi' => $multi,
				'rest' => $rest,
				'lotteryId' => HBSYXW,
				'tzjqurl' => 'activity/kxzhuihao',
				'codes' => $codes,
		);
		if (count($odatas) > 0) $data ['orders'] = $odatas;
		$this->display ( 'hbsyxw/index', $data, 'v1.2' );
	}

	function calculate($str)
	{
		$arr = explode ( ',', $str );
		$ji = 0;
		$da = 0;
		foreach ( $arr as $v )
		{
			if ($v % 2 > 0)
			{
				$ji ++;
			}
			if ($v >= 6)
			{
				$da ++;
			}
		}
		return array (
				$ji,
				$da 
		);
	}

	public function getMissNumber($miss, $history)
	{
		$missIssue = array_keys ( $miss );
		if ($missIssue [0] < $history [0] ['issue'])
		{
			// 计算最新开奖其次的遗漏
			if (! empty ( $history [0] ['awardNum'] ) && ! empty ( $miss [$history [1] ['issue']] ))
			{
				
				$ballAmount = $this->getBallAmount ();
				$count = count ( $ballAmount );
				
				// 上期遗漏数据
				foreach ( $miss [$history [1] ['issue']] as $playType => $countStr )
				{
					$tmpAry = explode ( ',', $countStr );
					$c = count ( $tmpAry );
					for($i = 0; $i < $c; $i ++)
					{
						$missedCounterAry [$playType] [$i + 1] = intval ( $tmpAry [$i] );
					}
				}
				
				$awardNumber = $history [0] ['awardNum'];
				$numberAry = explode ( ',', $awardNumber );
				// 初始化数据源格式
				$matches = array (
						1 => $awardNumber,
						2 => $numberAry [0],
						3 => $numberAry [1],
						4 => $numberAry [2],
						5 => implode ( ',', array (
								$numberAry [0],
								$numberAry [1] 
						) ),
						6 => implode ( ',', array (
								$numberAry [0],
								$numberAry [1],
								$numberAry [2] 
						) ) 
				);
				
				for($i = 0; $i < $count; $i ++)
				{
					for($j = 1; $j <= $ballAmount [$i]; $j ++)
					{
						if ($j < 10)
						{
							$needle = '0' . $j;
						} else
						{
							$needle = '' . $j;
						}
						if (strstr ( $matches [$i + 1], $needle ))
						{
							$missedCounterAry [$i] [$j] = 0;
						} else
						{
							$missedCounterAry [$i] [$j] += 1;
						}
					}
				}
				
				foreach ( $missedCounterAry as $playType => $countStr )
				{
					$missNum [$playType] = implode ( ',', $countStr );
				}
				array_pop ( $miss );
				$miss [$history [0] ['issue']] = $missNum;
				krsort ( $miss );
			}
		}
		return $miss;
	}
	
	// 遗漏种类统计
	private function getBallAmount()
	{
		$ballAmountConfig = array (
				0 => 11, // 11个任选n
				1 => 11, // 11个前n直选第一位
				2 => 11, // 11个前n直选第二位
				3 => 11, // 11个前n直选第三位
				4 => 11, // 11个前n组选前二位
				5 => 11  // 11个前n组选前三位
				);
		return $ballAmountConfig;
	}
}
