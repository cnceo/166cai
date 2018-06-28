<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 排列五  遗漏统计
 * 作    者: 李康建
 * 修改日期: 2017/05/17
 * 修改时间: 15:51
 */

class Pl5
{
	private $lotteryId = '35';
	private $flag = 'pl5';
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
			$this->CI->missed_model->writeRedisSerialize($this->flag, $this->lotteryId, 50); //遗漏入缓存
			$this->CI->missed_model->writeRedisMoreSerialize($this->flag, $this->lotteryId, 1000); //遗漏入缓存
		}

	}
	/**
	 * [missedCal 遗漏统计]
	 * @author LiKangJian 2017-05-17
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
		for($i = 0; $i < 5; $i++)
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
	 * [mType0 万位]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType0($award = array(), $preMissed)
	{
		return $this->comm($preMissed,$award[0]);
	}

	/**
	 * [mType1 千位]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType1($award = array(), $preMissed)
	{
		return $this->comm($preMissed,$award[1]);
	}
	/**
	 * [mType2 百位]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType2($award = array(), $preMissed)
	{
		return $this->comm($preMissed,$award[2]);
	}
	/**
	 * [mType3 十位]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType3($award = array(), $preMissed)
	{
		return $this->comm($preMissed,$award[3]);
	}
	/**
	 * [mType4 个位]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType4($award = array(), $preMissed)
	{
		return $this->comm($preMissed,$award[4]);
	}
	/**
	 * [comm 公用方法]
	 * @author LiKangJian 2017-05-17
	 * @param  [type] $preMissed   [description]
	 * @param  [type] $award_index [description]
	 * @return [type]              [description]
	 */
    private function comm($preMissed,$award_index)
    {
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = intval($award_index) === $key ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
    }
}