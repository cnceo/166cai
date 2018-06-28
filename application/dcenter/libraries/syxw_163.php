<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Syxw_163
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
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_syxw_paiqi');
		if($issues)
		{
			$url = 'http://caipiao.163.com/award/11xuan5/';
			$content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));
			$rule = '<td.*?data-period="(.*?)".*?data-award="(.*?)".*?>.*?<\/td>';
			preg_match_all("/$rule/is", $content, $matches);
			if(empty($matches[1]) || empty($matches[2]))
			{
				return ;
			}
			$awardArr = array();
			foreach ($matches[1] as $key => $issue)
			{
				$award = trim($matches[2][$key]);
				if($award && in_array($issue, $issues))
				{
					$awardArr[$issue] = str_replace(" ", ",", $award);
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
		$bonusDetail['qy']['dzjj'] = '13';
		$bonusDetail['r2']['dzjj'] = '6';
		$bonusDetail['r3']['dzjj'] = '19';
		$bonusDetail['r4']['dzjj'] = '78';
		$bonusDetail['r5']['dzjj'] = '540';
		$bonusDetail['r6']['dzjj'] = '90';
		$bonusDetail['r7']['dzjj'] = '26';
		$bonusDetail['r8']['dzjj'] = '9';
		$bonusDetail['q2zhix']['dzjj'] = '130';
		$bonusDetail['q2zux']['dzjj'] = '65';
		$bonusDetail['q3zhix']['dzjj'] = '1170';
		$bonusDetail['q3zux']['dzjj'] = '195';
		$bonusDetail['lx3']['q3']['dzjj'] = '1384';
		$bonusDetail['lx3']['z3']['dzjj'] = '214';
		$bonusDetail['lx3']['r3']['dzjj'] = '19';
		$bonusDetail['lx4']['r44']['dzjj'] = '154';
		$bonusDetail['lx4']['r43']['dzjj'] = '19';
		$bonusDetail['lx5']['r55']['dzjj'] = '1080';
		$bonusDetail['lx5']['r54']['dzjj'] = '90';
		$bonusDetail = json_encode($bonusDetail);
		$synflag = false;
		foreach ($issues as $issue)
		{
			if(isset($awardArr[$issue]))
			{
				$data = array('awardNum' => $awardArr[$issue], 'bonusDetail' => $bonusDetail, 'state' =>1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
				$result = $this->CI->gaopin_model->updateByIssue('cp_syxw_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '21406', 0);
		}
	}
}
