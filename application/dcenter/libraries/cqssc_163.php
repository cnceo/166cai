<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cqssc_163
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
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_cqssc_paiqi');
		if($issues)
		{
			$awardArr = array();
			// 最后一期可能在昨天
			foreach ($issues as $issueData) 
			{
				$date = '20' . substr($issueData, 0, 6);
				if($date == date('Ymd'))
				{
					$url = 'http://caipiao.163.com/award/cqssc/';
				}
				else
				{
					$url = 'http://caipiao.163.com/award/cqssc/' . $date . '.html';
				}

				$content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));
				$rule = '<td.*?data-win-number=\'(.*?)\'.*?data-period="(.*?)".*?>.*?<\/td>';
				preg_match_all("/$rule/is", $content, $matches);
				if(empty($matches[1]) || empty($matches[2]))
				{
					continue;
				}
				
				foreach ($matches[2] as $key => $issue)
				{
					$award = trim($matches[1][$key]);
					if($award && in_array($issue, $issues))
					{
						$awards = array_map('trim', explode(' ', $award));
						$awardArr[$issue] = implode(',', $awards);
					}
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
		$bonusDetail['1xzhix'] = '10';
		$bonusDetail['2xzhix'] = '100';
		$bonusDetail['2xzux'] = '50';
		$bonusDetail['3xzhix'] = '1000';
		$bonusDetail['3xzu3'] = '320';
		$bonusDetail['3xzu6'] = '160';
		$bonusDetail['5xzhix'] = '100000';
		$bonusDetail['5xtx']['qw'] = '20440';
		$bonusDetail['5xtx']['3w'] = '220';
		$bonusDetail['5xtx']['2w'] = '20';
		$bonusDetail['dxds'] = '4';
		$bonusDetail = json_encode($bonusDetail);
		$synflag = false;
		foreach ($issues as $issue)
		{
			if(isset($awardArr[$issue]))
			{
				$data = array('awardNum' => $awardArr[$issue], 'bonusDetail' => $bonusDetail, 'state' =>1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
				$result = $this->CI->gaopin_model->updateByIssue('cp_cqssc_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '55', 0);
		}
	}
}
