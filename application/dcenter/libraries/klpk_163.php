<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Klpk_163
{
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('gaopin_model');
		$this->CI->load->library('tools');
	}
	
	/**
	 * 抓取入口
	 * @param unknown_type $param
	 */
	public function capture($param)
	{
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_klpk_paiqi');
		if($issues)
		{
			$url = 'http://caipiao.163.com/award/kuailepuke/';
			$content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));
			$rule = '<td.*?data-period="(.*?)".*?data-award="(.*?)".*?>.*?<\/td>';
			preg_match_all("/$rule/is", $content, $matches);
			if(empty($matches[1]) || empty($matches[2]))
			{
				return ;
			}
			$huase = array('1' => 'S', '2' => 'H', '3' => 'C', '4' => 'D');
			$awardArr = array();
			foreach ($matches[1] as $key => $issue)
			{
				$award = trim($matches[2][$key]);
				if($award && in_array($issue, $issues))
				{
					$awards = explode(' ', $award);
					$awardArr[$issue] = substr($awards[0], -2) . ',' . substr($awards[1], -2) . ',' . substr($awards[2], -2) . '|' . $huase[substr($awards[0], 0, 1)] . ',' . $huase[substr($awards[1], 0, 1)] . ',' . $huase[substr($awards[2], 0, 1)];
				}
			}
			$this->saveData($issues, $awardArr); //保存数据
		}
	}
	
	/**
	 * 开奖信息入库
	 * @param unknown_type $issues
	 * @param unknown_type $awardArr
	 */
	public function saveData($issues, $awardArr)
	{
		//奖级信息
		$bonusDetail = array();
		$bonusDetail['thbx']['dzjj'] = '22';
		$bonusDetail['thdx']['dzjj'] = '90';
		$bonusDetail['thsbx']['dzjj'] = '535';
		$bonusDetail['thsdx']['dzjj'] = '2150';
		$bonusDetail['szbx']['dzjj'] = '33';
		$bonusDetail['szdx']['dzjj'] = '400';
		$bonusDetail['bzbx']['dzjj'] = '500';
		$bonusDetail['bzdx']['dzjj'] = '6400';
		$bonusDetail['dzbx']['dzjj'] = '7';
		$bonusDetail['dzdx']['dzjj'] = '88';
		$bonusDetail['r1']['dzjj'] = '5';
		$bonusDetail['r2']['dzjj'] = '33';
		$bonusDetail['r3']['dzjj'] = '116';
		$bonusDetail['r4']['dzjj'] = '46';
		$bonusDetail['r5']['dzjj'] = '22';
		$bonusDetail['r6']['dzjj'] = '12';
		$bonusDetail = json_encode($bonusDetail);
		$synflag = false;
		foreach ($issues as $issue)
		{
			if(isset($awardArr[$issue]))
			{
				$data = array('awardNum' => $awardArr[$issue], 'bonusDetail' => $bonusDetail, 'state' =>1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
				$result = $this->CI->gaopin_model->updateByIssue('cp_klpk_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '54', 0);
		}
	}
}
