<?php

class Cqssc
{
	private $lotteryId = '55';
	private $flag = 'cqssc';
	
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('missed_model');
		$this->CI->load->driver('cache', array('adapter' => 'redis'));
	}
	
	public function exec()
	{
		$missData = array();
		$issue = $this->CI->missed_model->fetchLastIssue($this->lotteryId);
		if($issue)
		{
			// 执行遗漏计算
			$initMissed = $this->CI->missed_model->fetchMissDetail($this->lotteryId, $issue);
			$awards = $this->CI->missed_model->fetchIssueRecords($this->flag, $issue);
			foreach ($awards as $key => $awardInfo)
			{
				//awardNumk 开奖号
				$this->issue = $awardInfo['issue'];
				$preData = $this->missedCal($awardInfo['awardNum'], $initMissed);
				$missData[$awardInfo['issue']] = $preData['detail'];
				//写入
				$this->CI->missed_model->insertMissedData($preData['values']);
				// 重新初始上一期遗漏
				$initMissed = $preData['detail'];
			}
			$this->CI->missed_model->writeRedisSerialize($this->flag, $this->lotteryId, 80); //遗漏入缓存
			$this->CI->missed_model->writeRedisMoreSerialize($this->flag, $this->lotteryId, 1600); //遗漏入缓存
		}

	}
	
	// 遗漏统计
	private function missedCal($award, $preMissed)
	{
		if(empty($award) || empty($preMissed))
		{
			return ;
		}
		$awards = explode(',', $award);
		$values = array();
		$detail = array();
		for($i = 0; $i < 8; $i++)
		{
			$method = "mType{$i}";
			$detail[$i] = $this->$method($awards, $preMissed[$i]);
			array_push($values, "('$this->lotteryId', '$this->issue', $i, '$detail[$i]', NOW())"); 
		}
		$miss = array(
			'detail' => $detail,
			'values' => $values
		);
		return $miss;
	}

	// 五星
	private function mType0($award = array(), $preMissed)
	{
		return $this->comZhix($award, $preMissed);
	}

	// 三星直选
	private function mType1($award = array(), $preMissed)
	{
		// 取后三位
		$award = array($award[2], $award[3], $award[4]);
		return $this->comZhix($award, $preMissed);
	}
	
	// 三星组选
	private function mType2($award = array(), $preMissed)
	{
		// 取后三位
		$award = array($award[2], $award[3], $award[4]);
		return $this->comZux($award, $preMissed);
	}
	
	// 二星直选
	private function mType3($award = array(), $preMissed)
	{
		// 取后两位
		$award = array($award[3], $award[4]);
		return $this->comZhix($award, $preMissed);
	}

	// 二星组选
	private function mType4($award = array(), $preMissed)
	{
		// 取后两位
		$award = array($award[3], $award[4]);
		return $this->comZux($award, $preMissed);
	}

	// 一星直选
	private function mType5($award = array(), $preMissed)
	{
		// 取后一位
		$award = array($award[4]);
		return $this->comZux($award, $preMissed);
	}

	// 大小单双 统计到每一位
	private function mType6($award = array(), $preMissed)
	{
		$awardtypesArr = array(
			0	=>	$this->getDxdsType($award[0]),
			1 	=>	$this->getDxdsType($award[1]),
			2 	=>	$this->getDxdsType($award[2]),
			3 	=>	$this->getDxdsType($award[3]),
			4 	=>	$this->getDxdsType($award[4]),
		);

		$miss = array();
		foreach (explode('|', $preMissed) as $key => $numStr) 
		{
			$numArr = explode(',', $numStr);
			foreach ($numArr as $index => $num) 
			{
				if(in_array($index, $awardtypesArr[$key]))
				{
					$numArr[$index] = 0;
				}
				else
				{
					$numArr[$index]++;
				}
			}
			$miss[$key] = implode(',', $numArr);
		}
		return implode('|', $miss);
	}
	
	// 三星形态 组三、组六、豹子
	private function mType7($award = array(), $preMissed)
	{
		$miss = array();
		$awardNum = array($award[2], $award[3], $award[4]);
		foreach (explode(',', $preMissed) as $i => $numStr) 
		{
			$method = "matchTypes{$i}";
			$matchRes = $this->$method($awardNum);
			if($matchRes)
			{
				$miss[$i] = 0;
			}
			else
			{
				$miss[$i] = $numStr + 1;
			}
		}
		return implode(',', $miss);	
	}

	// 组三形态判断 后三位任意两位号码相同
	private function matchTypes0($award)
	{
		$res = 0;
		if(count(array_unique($award)) == 2)
		{
			$res = 1;
		}
		return $res;
	}

	// 组六形态判断 后三位各不相同
	private function matchTypes1($award)
	{
		$res = 0;
		if(count(array_unique($award)) == 3)
		{
			$res = 1;
		}
		return $res;
	}

	// 豹子形态判断 后三位相同
	private function matchTypes2($award)
	{
		$res = 0;
		if(count(array_unique($award)) == 1)
		{
			$res = 1;
		}
		return $res;
	}

	// 直选按位计算
    private function comZhix($award, $preMissed)
    {
    	$miss = array();
		$missDataArr = explode('|', $preMissed);
		foreach ($missDataArr as $index => $weiStrs) 
		{
			$numArr = explode(',', $weiStrs);
			foreach ($numArr as $key => $num) 
			{
				if($key == $award[$index])
				{
					$numArr[$key] = 0;
				}
				else
				{
					$numArr[$key]++;
				}
			}
			$miss[$index] = implode(',', $numArr);
		}
		return implode('|', $miss);
    }

    // 组选计算
    private function comZux($award, $preMissed)
    {
    	$count = array_count_values($award);
		$award = array_unique($award);
		$missDataArr = explode(',', $preMissed);
		foreach ($missDataArr as $index => $weiStrs) 
		{
			if(in_array($index, $award))
			{
				if($count[$index] == 3)
				{
					$missDataArr[$index] = -3;
				}
				elseif($count[$index] == 2)
				{
					$missDataArr[$index] = -2;
				}
				else
				{
					$missDataArr[$index] = 0;
				}	
			}
			else
			{
				if($missDataArr[$index] >= 0)
				{
					$missDataArr[$index]++;
				}
				else
				{
					$missDataArr[$index] = 1;
				}
			}
		}
		return implode(',', $missDataArr);
    }

    // 大小单双形态
	private function getDxdsType($num)
	{
		$typeArr = array();
		// 大小
		if($num >= 5)
		{
			array_push($typeArr, 0);
		}
		else
		{
			array_push($typeArr, 1);
		}
		// 单双
		if($num % 2 == 0)
		{
			array_push($typeArr, 3);
		}
		else
		{
			array_push($typeArr, 2);
		}
		return $typeArr;
	}
}