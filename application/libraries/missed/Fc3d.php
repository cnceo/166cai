<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 福彩3D  遗漏统计
 * 作    者: 李康建
 * 修改日期: 2017/05/17
 * 修改时间: 13:19
 */

class Fc3d
{
	private $lotteryId = '52';
	private $flag = 'fc3d';
	/**
	 * [__construct 自动执行]
	 * @author LiKangJian 2017-05-16
	 */
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('missed_model');
		$this->CI->load->driver('cache', array('adapter' => 'redis'));
	}
	/**
	 * [exec 执行遗漏统计入口]
	 * @author LiKangJian 2017-05-16
	 * @return [type] [description]
	 */
	public function exec()
	{
		$missData = array();
		$issue = $this->CI->missed_model->fetchLastIssue($this->lotteryId);
		if($issue)
		{
			// 执行遗漏计算 5,6,6
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
			$this->CI->missed_model->writeRedisSerialize($this->flag, $this->lotteryId, 40); //遗漏入缓存
			$this->CI->missed_model->writeRedisMoreSerialize($this->flag, $this->lotteryId, 800); //遗漏入缓存
		}

	}
	/**
	 * [missedCal 遗漏统计]
	 * @author LiKangJian 2017-05-16
	 * @param  [type] $award     [description]
	 * @param  [type] $preMissed [description]
	 * @return [type]            [description]
	 */
	private function missedCal($award, $preMissed)
	{
		if(empty($award) || empty($preMissed))
		{
			return ;
		}
		$awards = explode(',', $award);
		$values = array();
		$detail = array();
		for($i = 0; $i < 4; $i++)
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

	/**
	 *
	 * [mType0 直选玩法百位]
	 * @author LiKangJian 2017-05-16
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType0($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = $key  == $award[0] ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 *
	 * [mType1 直选玩法十位]
	 * @author LiKangJian 2017-05-16
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType1($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = $key  == $award[1] ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 *
	 * [mType2 直选玩法个位]
	 * @author LiKangJian 2017-05-16
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType2($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = $key  == $award[2] ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}

		return implode(',', $data);
	}

	/**
	 *
	 * [mType3 组选玩法 所选号码与开奖号码一致(顺序不限)]
	 * @author LiKangJian 2017-05-16
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType3($award = array(), $preMissed)
	{
		$data = array();
		$preMissed = explode(',', $preMissed);
		$count = array_count_values($award);
		foreach ($preMissed as $key => $val)
		{
			if(in_array( $key , $award ))
			{
				if($count[$key] == 2)
				{
					$miss = -2;
				}else if($count[$key] == 3)
				{
					$miss = -3;
				}else
				{
					$miss = 0;
				}
			}else{
				$miss = 1;
			}
			if( ($val == -2 || $val == -3) && !in_array( $key , $award )) 
			{
				$val = 0;
			}
			$data[$key] = $miss == 1 ? ($val + $miss) : $miss;
		}

		return implode(',', $data);
	}
}