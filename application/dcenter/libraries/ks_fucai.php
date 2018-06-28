<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ks_Fucai
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
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_ks_paiqi');
		if($issues)
		{
			$url = 'http://fucai.eastday.com/LotteryNew/K3Result.aspx';
			$content = $this->CI->tools->get_content($url, __CLASS__);
			$rule  = '<tr>.*?';
			$rule .= '<td.*?class="first">(.*?)<\/td>.*?';
			$rule .= '<td>.*?<\/td>.*?';
			$rule .= '<td><span>(.*?)<\/span><\/td>.*?';
			$rule .= '<td><span>(.*?)<\/span><\/td>.*?';
			$rule .= '<td.*?class="last".*?><span>(.*?)<\/span><\/td>.*?';
			$rule .= '<\/tr>';
			preg_match_all("/$rule/is", $content, $matches);
			if(empty($matches[1]) || empty($matches[2]) || empty($matches[3]) || empty($matches[4]))
			{
				return ;
			}
			$awardArr = array();
			foreach ($matches[1] as $key => $issue)
			{
				$issue = str_replace('-', '0', $issue);
				if(in_array($issue, $issues))
				{
					$awardArr[$issue] = $matches[2][$key] . ',' . $matches[3][$key] . ',' . $matches[4][$key];
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
		$bonusDetail['hz']['z4'] = '80';
		$bonusDetail['hz']['z5'] = '40';
		$bonusDetail['hz']['z6'] = '25';
		$bonusDetail['hz']['z7'] = '16';
		$bonusDetail['hz']['z8'] = '12';
		$bonusDetail['hz']['z9'] = '10';
		$bonusDetail['hz']['z10'] = '9';
		$bonusDetail['hz']['z11'] = '9';
		$bonusDetail['hz']['z12'] = '10';
		$bonusDetail['hz']['z13'] = '12';
		$bonusDetail['hz']['z14'] = '16';
		$bonusDetail['hz']['z15'] = '25';
		$bonusDetail['hz']['z16'] = '40';
		$bonusDetail['hz']['z17'] = '80';
		$bonusDetail['sthtx'] = '40';
		$bonusDetail['sthdx'] = '240';
		$bonusDetail['sbth'] = '40';
		$bonusDetail['slhtx'] = '10';
		$bonusDetail['ethfx'] = '15';
		$bonusDetail['ethdx'] = '80';
		$bonusDetail['ebth'] = '8';
		$bonusDetail = json_encode($bonusDetail);
		$synflag = false;
		foreach ($issues as $issue)
		{
			if(isset($awardArr[$issue]))
			{
				$data = array('awardNum' => $awardArr[$issue], 'bonusDetail' => $bonusDetail, 'state' =>1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
				$result = $this->CI->gaopin_model->updateByIssue('cp_ks_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '53', 0);
		}
	}
}
