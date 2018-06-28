<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 双色球  遗漏统计
 * 作    者: 李康建
 * 修改日期: 2017/05/17
 * 修改时间: 13:19
 */

class Ssq
{
	private $lotteryId = '51';
	private $flag = 'ssq';
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
			// 执行遗漏计算
			$initMissed = $this->CI->missed_model->fetchMissDetail($this->lotteryId, $issue);
			$initMissed = $initMissed[0];
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
			$this->CI->missed_model->writeRedisSerialize($this->flag, $this->lotteryId, 10); //遗漏入缓存
			$this->CI->missed_model->writeRedisMoreSerialize($this->flag, $this->lotteryId, 200); //遗漏入缓存
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
		$awards = explode('|', $award);
		$redawards = explode(',', $awards[0]);
		$preMisseds = explode('|', $preMissed);
		$redMiss = explode(',', $preMisseds[0]);
		$blueMiss = explode(',', $preMisseds[1]);
		$red = array();
		$blue = array();
		$values = array();
		//对红球处理 13 14 18 19 21 28
		foreach ($redMiss as $key => $val)
		{
			$miss = in_array( ($key + 1), $redawards ) ? 0 : 1;
			$red[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		//对篮球处理
		foreach ($blueMiss as $key => $val)
		{
			$miss = $key + 1 === intval($awards[1])  ? 0 : 1;
			$blue[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		$detail = implode(',', $red).'|'.implode(',', $blue);
		array_push($values, "('$this->lotteryId', '$this->issue', '0', '$detail', NOW())");
		$miss = array(
			'detail' => $detail,
			'values' => $values
		);
		
		return $miss;
	}


}