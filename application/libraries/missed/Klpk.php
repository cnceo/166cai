<?php
/**
 * 上海快三遗漏数据统计
 * @author shigx
 *
 */
class Klpk
{
	private $CI;
	private $issue;

	// 出现多次统计
	private $countArray = array('1' => 0, '2' => -2, '3' => -3);

    // 对子格式
    private $dz = array('01,01', '02,02', '03,03', '04,04', '05,05', '06,06', '07,07', '08,08', '09,09', '10,10', '11,11', '12,12', '13,13', '00,00');

    // 同花格式
    private $th = array(
    	'S' => '黑桃',
        'H' => '红桃',
        'C' => '梅花',
        'D' => '方块'
    );

    // 顺子格式
    private $sz = array('01,02,03', '02,03,04', '03,04,05', '04,05,06', '05,06,07', '06,07,08', '07,08,09', '08,09,10', '09,10,11', '10,11,12', '11,12,13', '01,12,13');
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('missed_model');
		$this->CI->load->driver('cache', array('adapter' => 'redis'));
	}
	
	// 执行方法
	public function exec()
	{
		$missData = array();

		$issue = $this->CI->missed_model->fetchLastIssue(54);

		if($issue)
		{
			// 执行遗漏计算
			$initMissed = $this->CI->missed_model->fetchMissDetail(54, $issue);

			$awards = $this->CI->missed_model->fetchIssueRecords('klpk', $issue);

			if(!empty($awards))
			{
				foreach ($awards as $key => $awardInfo)
				{
					$preData = $this->missedCal($awardInfo, $initMissed);
					$missData[$awardInfo['issue']] = $preData['detail'];

					$this->CI->missed_model->insertMissedData($preData['values']);
					// 重新初始上一期遗漏
					$initMissed = $preData['detail'];
				}
			}

			$this->CI->missed_model->writeRedis('klpk', 54, 70); //遗漏入缓存
			$this->CI->missed_model->writeMissMore('klpk', 54, 1400); //遗漏入缓存
		}
	}
	
	/**
	 * 执行遗漏统计
	 * @param unknown_type $preMissed 上一期遗漏数据
	 */
	private function missedCal($awardInfo, $preMissed)
	{
		$miss = array();

		$values = array();

		if(empty($awardInfo) || empty($preMissed))
		{
			return ;
		}

 		// 开奖信息处理号码及花色
		$awardData = explode('|', $awardInfo['awardNum']);

		foreach ($preMissed as $playType => $missData)
		{
			$method = "mType{$playType}";
			$detail[$playType] = $this->$method($awardData, $missData);

			// 组装数据
			array_push($values, "('54', '$awardInfo[issue]', $playType, '$detail[$playType]', NOW())"); 
		}

		$miss = array(
			'detail' => $detail,
			'values' => $values
		);
		return $miss;		
	}
	
	/**
	 * 类型0  任选遗漏
	 * @param array() $award 开奖号码
	 * @param string $preMissed 上一期的遗漏值
	 * @return string
	 */
	private function mType0($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 按号码判断
		$match = explode(',', $award[0]);
		// 统计出现次数
		$countData = array_count_values($match);

		foreach ($preMissed as $key => $item)
		{
			$needle = $key + 1;
			$needle = str_pad($needle, 2, "0", STR_PAD_LEFT);

			if(in_array($needle, $match))
			{
				// 检查出现次数
				$data[$key] = $this->countArray[$countData[$needle]];
			}
			else
			{
				if($item >= 0)
				{
					$data[$key] = $item + 1;
				}
				else
				{
					$data[$key] = 1;
				}
			}
		}
		return implode(',', $data);
	}
	
	/**
	 * 类型1 对子
	 * @param array() $award	开奖号码
	 * @param string $preMissed	上一期的遗漏值
	 * @return string
	 */
	private function mType1($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 检查对子
		$needle = $this->checkDz($award);

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if($needle >= 0 && $i == $needle)
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位包选去最小值
		$pos = array_search(min($data), $data);
        $min = $data[$pos];
        $data[$length] = $min;
		
		return implode(',', $data);
	}

	// 检查对子
	private function checkDz($award = array())
	{
		// 按号码判断
		$match = explode(',', $award[0]);
		sort($match);
		// 统计出现次数
		$countData = array_count_values($match);
		$countData = array_flip($countData);

		// 是否存在出现两次的对子
		$needle = (isset($countData[2])) ? (intval($countData[2]) - 1) : '-1';

		return $needle;
	}
	
	/**
	 * 类型2 同花
	 * @param array() $award	开奖号码
	 * @param int $preMissed	上期遗漏数据
	 * @return string
	 */
	private function mType2($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 检查同花
		$needle = $this->checkTh($award);

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if($needle >= 0 && $i == $needle)
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位包选去最小值
		$pos = array_search(min($data), $data);
        $min = $data[$pos];
        $data[$length] = $min;

		return implode(',', $data);
	}

	// 检查同花
	private function checkTh($award = array())
	{
		// 按花色判断
		$match = explode(',', $award[1]);
		// 统计出现次数
		$countData = array_count_values($match);
		$countData = array_flip($countData);

		$indexData = array_flip(array_keys($this->th));

		// 是否存在出现三次的同花
		$needle = (!empty($countData[3])) ? $indexData[$countData[3]] : '-1';

		return $needle;
	}
	
	/**
	 * 类型3 顺子
	 * @param array() $award	开奖号码
	 * @param string $preMissed	上期遗漏数据
	 * @return string
	 */
	private function mType3($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 检查顺子
		$needle = $this->checkSz($award);

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if($needle >= 0 && $i == $needle)
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位包选去最小值
		$pos = array_search(min($data), $data);
        $min = $data[$pos];
        $data[$length] = $min;

		return implode(',', $data);
	}

	// 检查顺子
	private function checkSz($award = array())
	{
		// 按花色判断
		$match = explode(',', $award[0]);
		// 排序
		sort($match);

		$preMatch = implode(',', $match);
		
		// 判断是否是顺子
		if( in_array($preMatch, $this->sz) )
		{
			$szArry = array_flip($this->sz);
			$needle = $szArry[$preMatch];
		}
		else
		{
			$needle = -1;
		}
		return $needle;
	}
	
	/**
	 * 类型4 同花顺
	 * @param array() $award	开奖号码
	 * @param string $preMissed	上期遗漏数据
	 * @return string
	 */
	private function mType4($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 按号码判断 - 顺子
		$matchNum = explode(',', $award[0]);
		// 排序
		sort($matchNum);

		$preMatchNum = implode(',', $matchNum);
		
		// 判断是否是顺子
		if( in_array($preMatchNum, $this->sz) )
		{
			$numFlag = TRUE;
		}
		else
		{
			$numFlag = FALSE;
		}

		// 按花色判断 - 同花
		$matchType = explode(',', $award[1]);

		// 是否存在出现三次的同花
		$countData = array_count_values($matchType);
		$countData = array_flip($countData);
		$indexData = array_flip(array_keys($this->th));

		if(!empty($countData[3]))
		{
			$typeFlag = TRUE;
			$needle = $indexData[$countData[3]];
		}
		else
		{
			$typeFlag = FALSE;
			$needle = -1;
		}

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if($numFlag && $typeFlag && $needle >= 0 && $i == $needle)
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位包选去最小值
		$pos = array_search(min($data), $data);
        $min = $data[$pos];
        $data[$length] = $min;

		return implode(',', $data);
	}
	
	/**
	 * 类型5 豹子
	 * @param array() $award	开奖号码
	 * @param string $preMissed	上期遗漏数据
	 * @return string
	 */
	private function mType5($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);

		// 检查豹子
		$needle = $this->checkBz($award);

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if($needle >= 0 && $i == $needle)
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位包选去最小值
		$pos = array_search(min($data), $data);
        $min = $data[$pos];
        $data[$length] = $min;

		return implode(',', $data);
	}

	// 检查豹子
	private function checkBz($award = array())
	{
		// 按号码判断
		$match = explode(',', $award[0]);
		sort($match);

		// 是否豹子
		$needle = ($match[0] == $match[1] && $match[1] == $match[2] && $match[1] == $match[2]) ? (intval($match[0]) - 1) : '-1';

		return $needle;
	}

	/**
	 * 类型6 包选
	 * @param array() $award	开奖号码
	 * @param string $preMissed	上期遗漏数据
	 * @return string
	 */
	private function mType6($award = array(), $preMissed)
	{	
		$data = array();
		$preMissed = explode(',', $preMissed);

		$needleArr = array();
		// 对子,同花,顺子,同花顺,豹子,散牌
		$dzNeedle = $this->checkDz($award);

		if($dzNeedle >= 0)
		{
			array_push($needleArr, '0');
		}

		$thNeedle = $this->checkTh($award);

		if($thNeedle >= 0)
		{
			array_push($needleArr, '1');
		}

		$szNeedle = $this->checkSz($award);

		if($szNeedle >= 0)
		{
			array_push($needleArr, '2');
		}

		// 同花顺
		if($thNeedle >= 0 && $szNeedle >= 0)
		{
			array_push($needleArr, '3');
		}

		$bzNeedle = $this->checkBz($award);

		if($bzNeedle >= 0)
		{
			array_push($needleArr, '4');
		}

		$length = count($preMissed) - 1;

		for ($i = 0; $i < $length; $i++) 
		{ 
			if(!empty($needleArr) && in_array($i, $needleArr))
			{
				$data[$i] = 0;
			}
			else
			{
				$data[$i] = $preMissed[$i] + 1;
			}
		}

		// 最后一位散牌判断
		if(empty($needleArr))
		{
			$data[$length] = 0;
		}
		else
		{
			$data[$length] = $preMissed[$length] + 1;
		}

		return implode(',', $data);
	}
}
