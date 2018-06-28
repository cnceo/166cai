<?php
/**
 * Copyright (c) 2017,上海快猫文化传媒有限公司.
 * 摘    要: 广东十一选5 遗漏统计
 * 作    者: 李康建
 * 修改日期: 2017/05/16
 * 修改时间: 15:19
 */

class Gdsyxw
{
	private $lotteryId = '21421';
	private $flag = 'gdsyxw';
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
			$this->CI->missed_model->writeRedisSerialize($this->flag, $this->lotteryId, 60); //遗漏入缓存
			$this->CI->missed_model->writeRedisMoreSerialize($this->flag, $this->lotteryId, 1200); //遗漏入缓存
		}
		
		//组合遗漏计算
		$this->zuheMissCount();

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
		for($i = 0; $i < 6; $i++)
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
	 * [mType0 任选玩法]
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
			$miss = in_array(($key + 1), $award) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}
	/**
	 * [mType1 前一]
	 * @author LiKangJian 2017-05-17
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
			$miss = intval($award[0]) === ($key+1) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 * [mType2 前二直选 与开奖号码的前两位号码相同且顺序一致]
	 * @author LiKangJian 2017-05-17
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
			$miss = intval($award[1]) === ($key+1) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 * [mType3 前三直选 与开奖号码的前三位号码相同且顺序一致]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType3($award = array(), $preMissed)
	{  
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = intval($award[2]) === ($key+1) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 * [mType4 前二组选 与开奖号码的前两位号码相同（顺序不限），即中奖65元]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType4($award = array(), $preMissed)
	{  
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = intval($award[0]) === ($key+1) || intval($award[1]) === ($key+1) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}
		return implode(',', $data);
	}

	/**
	 * [mType5 前三组选 与开奖号码的前三位号码相同（顺序不限），即中奖65元]
	 * @author LiKangJian 2017-05-17
	 * @param  array  $award     [开奖号码]
	 * @param  [type] $preMissed [上一期的遗漏值]
	 * @return [type]            [description]
	 */
	private function mType5($award = array(), $preMissed)
	{  
		$data = array();
		$preMissed = explode(',', $preMissed);
		foreach ($preMissed as $key => $val)
		{
			$miss = intval($award[0]) === ($key+1) || intval($award[1]) === ($key+1) || intval($award[2]) === ($key+1) ? 0 : 1;
			$data[$key] = $miss == 0 ? 0 : ($val + $miss);
		}

		return implode(',', $data);
	}
	
	/**
	 * 组合遗漏计算
	 */
	private function zuheMissCount()
	{
		$issue = $this->CI->missed_model->getLastIssue($this->lotteryId);
		$awards = $this->CI->missed_model->fetchIssueRecords($this->flag, $issue);
		if($awards)
		{
			$lastResult = $this->CI->missed_model->getMissDataByLid($this->lotteryId);
			foreach ($awards as $award)
			{
				$awardNum = explode(',', $award['awardNum']);
				$datas = array();
				foreach ($lastResult as $value)
				{
					$method = "zuheType{$value['playType']}";
					$miss = $this->$method($awardNum, $value['codes']);
					$data = $this->dataCalculate($miss, $value);
					$data['id'] = $value['id'];
					$data['issue'] = $award['issue'];
					$data['playType'] = $value['playType'];
					$data['modType'] = $value['modType'];
					$data['codes'] = $value['codes'];
					$datas[] = $data;
				}
	
				$lastResult = $datas;
			}
	
			$this->CI->missed_model->insertZhMissedData($lastResult);
		}
	}
	
	//历史数据执行脚本
	public function historyZuheMissCount()
	{
		$issue = $this->CI->missed_model->getLastIssue($this->lotteryId);
		$awards = $this->CI->missed_model->fetchIssueRecords($this->flag, $issue);
		while($awards)
		{
			$lastResult = $this->CI->missed_model->getMissDataByLid($this->lotteryId);
			foreach ($awards as $award)
			{
				$awardNum = explode(',', $award['awardNum']);
				$datas = array();
				foreach ($lastResult as $value)
				{
					$method = "zuheType{$value['playType']}";
					$miss = $this->$method($awardNum, $value['codes']);
					$data = $this->dataCalculate($miss, $value);
					$data['id'] = $value['id'];
					$data['issue'] = $award['issue'];
					$data['playType'] = $value['playType'];
					$data['modType'] = $value['modType'];
					$data['codes'] = $value['codes'];
					$datas[] = $data;
				}
	
				$lastResult = $datas;
				$issue = $award['issue'];
			}
	
			$this->CI->missed_model->insertZhMissedData($lastResult);
			$awards = $this->CI->missed_model->fetchIssueRecords($this->flag, $issue);
		}
	}
	
	/**
	 * 组合遗漏命中判断   前一
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType1($award, $codes)
	{
		$miss = ($award[0] == $codes) ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏命中判断   任二
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType2($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 2);
	}
	
	/**
	 * 组合遗漏命中判断   任三
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType3($award, $codes)
	{
		return $this->zuheR2_8($award, $codes,3);
	}
	
	/**
	 * 组合遗漏命中判断   任四
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType4($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 4);
	}
	
	/**
	 * 组合遗漏命中判断   任五
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType5($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 5);
	}
	
	/**
	 * 组合遗漏命中判断   任六
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType6($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 5);
	}
	
	/**
	 * 组合遗漏命中判断   任七
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType7($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 5);
	}
	
	/**
	 * 组合遗漏命中判断   任八
	 * @param unknown_type $award
	 * @param unknown_type $lastTotal
	 */
	private function zuheType8($award, $codes)
	{
		return $this->zuheR2_8($award, $codes, 5);
	}
	
	/**
	 * 组合遗漏命中判断 任二到任五公用计算
	 * @param unknown_type $award
	 * @param unknown_type $codes
	 * @param unknown_type $num
	 * @return boolean
	 */
	private function zuheR2_8($award, $codes, $num)
	{
		$codes = explode(' ', $codes);
		$intersect = array_intersect($award, $codes);
		$miss = count($intersect) >= $num ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏命中判断   前二直选
	 * @param unknown_type $award
	 * @param unknown_type $codes
	 * @return boolean
	 */
	private function zuheType9($award, $codes)
	{
		$codes = explode('|', $codes);
		$miss = ($award[0] == $codes[0] && $award[1] == $codes[1]) ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏命中判断   前三直选
	 * @param unknown_type $award
	 * @param unknown_type $codes
	 * @return boolean
	 */
	private function zuheType10($award, $codes)
	{
		$codes = explode('|', $codes);
		$miss = ($award[0] == $codes[0] && $award[1] == $codes[1] && $award[2] == $codes[2]) ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏命中判断   前二组选
	 * @param unknown_type $award
	 * @param unknown_type $codes
	 * @return boolean
	 */
	private function zuheType11($award, $codes)
	{
		$codes = explode(' ', $codes);
		$awardArr = array($award[0], $award[1]);
		$intersect = array_intersect($awardArr, $codes);
		$miss = count($intersect) == 2 ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏命中判断   前三组选
	 * @param unknown_type $award
	 * @param unknown_type $codes
	 * @return boolean
	 */
	private function zuheType12($award, $codes)
	{
		$codes = explode(' ', $codes);
		$awardArr = array($award[0], $award[1], $award[2]);
		$intersect = array_intersect($awardArr, $codes);
		$miss = count($intersect) == 3 ? true : false;
		return $miss;
	}
	
	/**
	 * 组合遗漏数据计算
	 * @param boolean $miss  当期开奖号码命中状态   true 命中  false  未命中
	 * @param array $lastTotal	上一期次计算数据
	 * @return array()
	 */
	private function dataCalculate($miss, $lastTotal)
	{
		$lastTenMissingTimes = ($lastTotal['lastTenMissingTimes'] !== '') ? explode(',', $lastTotal['lastTenMissingTimes']) : array();
		$countTen = count($lastTenMissingTimes);
		if($miss)
		{
			if($countTen >= 10)
			{
				array_shift($lastTenMissingTimes);
			}
			else
			{
				$countTen += 1;
			}
			
			$lastTenMissingTimes[] = 0;
			$data = array(
				'curMiss' => 0,
				'lastMiss' => isset($lastTenMissingTimes[$countTen - 2]) ? $lastTenMissingTimes[$countTen - 2] : 0,
				'maxMiss' => $lastTotal['curMiss'] >  $lastTotal['maxMiss'] ? $lastTotal['curMiss'] : $lastTotal['maxMiss'],
				'showTotal' => $lastTotal['showTotal'] + 1,
				'missTotal' => $lastTotal['missTotal'] + $lastTotal['curMiss'],
				'lastTenMissingTimes' => implode(',', $lastTenMissingTimes),
				'curHit' => $lastTotal['curHit'] + 1,
				'maxHit' => $lastTotal['maxHit'],
			);
		}
		else
		{
			array_pop($lastTenMissingTimes);
			$lastTenMissingTimes[] = $lastTotal['curMiss'] + 1;
			$data = array(
				'curMiss' => $lastTotal['curMiss'] + 1,
				'lastMiss' => isset($lastTenMissingTimes[$countTen - 2]) ? $lastTenMissingTimes[$countTen - 2] : 0,
				'maxMiss' => $lastTotal['maxMiss'],
				'showTotal' => $lastTotal['showTotal'],
				'missTotal' => $lastTotal['missTotal'],
				'lastTenMissingTimes' => implode(',', $lastTenMissingTimes),
				'curHit' => 0,
				'maxHit' => $lastTotal['curHit'] >  $lastTotal['maxHit'] ? $lastTotal['curHit'] : $lastTotal['maxHit'],
			);
		}
	
		return $data;
	}
}