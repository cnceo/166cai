<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hbsyxw_Hbtycp
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
		$issues = $this->CI->gaopin_model->getLotteryIssues('cp_hbsyxw_paiqi');
		if($issues)
		{
			$awardArr = array();
			for ($page = 1; $page > 0; $page--) {//需要修复老数据时，调整page值即可
				$url = "http://server.baibaocp.com/webticai/Foreignsyxw/foreign/wuhan/page/".$page."/num/30";
				$content = $this->CI->tools->get_content($url, __CLASS__);
				$datas = json_decode($content, true);
				if($datas['ret'] != '0' || empty($datas['data'])) return ;
				foreach ($datas['data'] as $values)
				{
					if(isset($values['ball1']) && isset($values['ball2']) && isset($values['ball3']) && isset($values['ball4']) && isset($values['ball5']) && in_array($values['issue'], $issues))
						$awardArr[$values['issue']] = $values['ball1'].",".$values['ball2'].",".$values['ball3'].",".$values['ball4'].",".$values['ball5'];
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
		$bonusDetail = json_encode($bonusDetail);
		$synflag = false;
		foreach ($issues as $issue)
		{
			if(isset($awardArr[$issue]))
			{
				$data = array('awardNum' => $awardArr[$issue], 'bonusDetail' => $bonusDetail, 'state' =>1, 'status' => 50, 'rstatus' => 50, 'd_synflag' => 0);
				$result = $this->CI->gaopin_model->updateByIssue('cp_hbsyxw_paiqi', $issue, $data);
				if($result)
				{
					$synflag = true;
				}
			}
		}
		
		if($synflag)
		{
			//启动同步号码任务
			$this->CI->gaopin_model->updateTicketStop(1, '21408', 0);
		}
	}
}
