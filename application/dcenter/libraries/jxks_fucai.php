<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jxks_Fucai
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
	public function capture($param=array())
	{
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_jxks_paiqi');
		if($issues)
		{
			$url = 'http://www.jxfczx.cn/report/K3_WinMessage.aspx';
			$content = $this->CI->tools->get_content($url, __CLASS__);
 			$preg = 'class="win_deail".*?>(.*)<tr class="hote">';
            preg_match("/$preg/is", $content, $match);
            preg_match_all("/.*?termCode.*?(\d{9}).*?<b id=k3_0>(\d)<\/b><b id=k3_1>(\d)<\/b><b id=k3_2>(\d)<\/b>.*?<\/tr>+/is", $match[1], $res);
            if(empty($res[1])) return ;
			$awardArr = array();
			foreach ($res[1] as $key => $issue)
			{
				$issue = '20'.$issue;
				//获取下一个期次
				if(in_array($issue, $issues))
				{
					$awardNumArr = array($res[2][$key],$res[3][$key],$res[4][$key]);
					sort($awardNumArr);
					$awardArr[$issue] = implode(',', $awardNumArr);
				}
			}
			foreach ($issues as  $k=>$v) 
			{
				if(!isset($awardArr[$v]))
				{
					unset($issues[$k]);
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
		$bonusDetail['hz']['z3'] = '240';
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
		$bonusDetail['hz']['z18'] = '240';
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
				$result = $this->CI->gaopin_model->updateByIssue('cp_jxks_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '57', 0);
		}
	}
}
