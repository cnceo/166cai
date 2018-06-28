<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cli_Champion extends MY_Controller
{
	public function __construct() {
		parent::__construct();
		$this->load->model('match_model');
		$this->load->library('tools');
	}
	
	/**
	 * 赔率抓取
	 */
	public function index()
	{
	    //冠军彩
		$url = 'http://i.sporttery.cn/rank_calculator/get_list?tid[]=104895&pcode[]=chp&i_callback=cphData&_=' . time();
		$content = $this->tools->request($url);
		preg_match("/cphData\\((.*?)\\);/is", $content, $match);
		if($match[1])
		{
			$response = json_decode($match[1], true);
			//冠军赛赔率更新
			$data = $response['data'][0]['data'];
			$this->updateData($data, 1);
			
		}
		//冠亚军彩
		$url = 'http://i.sporttery.cn/rank_calculator/get_list?tid[]=104895&&pcode[]=fnl&i_callback=getList&_=' . time();
		$content = $this->tools->request($url);
		preg_match("/getList\\((.*?)\\);/is", $content, $match);
		if($match[1])
		{
		    $response = json_decode($match[1], true);
		    //冠亚军赛赔率更新
		    $data = $response['data'][0]['data'];
		    $this->updateData($data, 2);
		    
		}
	}
	
	/**
	 * 更新赔率
	 * @param unknown_type $data
	 * @param unknown_type $type
	 */
	private function updateData($data, $type)
	{
		$issue = '18001'; //2018世界杯期次设置  下次可以重新设置
		$matchs = explode('|', $data);
		$embData = array();
		foreach ($matchs as $match)
		{
			if(empty($match))
			{
				continue;
			}
			$values = explode('-', $match);
			if(count($values) < 7)
			{
				continue;
			}
			$embData[trim($values[0])]['odds'] = $values[3];
			$embData[trim($values[0])]['status'] = $this->getStatus($values[2]);
		}
		
		$champion = $this->match_model->getChampion($issue, $type);
		foreach ($champion as $val)
		{
			if($embData[$val['mid']['odds']] && (($val['odds'] != $embData[$val['mid']]['odds']) || ($val['status'] != $embData[$val['mid']]['status'])))
			{
				$this->match_model->updateChampionOdds($issue, $type, $val['mid'], $embData[$val['mid']]['odds'], $embData[$val['mid']]['status']);
			}
		}
	}
	
	/**
	 * 对阵状态
	 * @param unknown_type $info
	 * @return number
	 */
	private function getStatus($info)
	{
		$status = '';
		if($info == '开售')
		{
			$status = 0;
		}
		else if($info == '出局')
		{
			$status = 1;
		}
		else if($info == '胜出')
		{
			$status = 2;
		}
		elseif($info == '停售')
		{
			$status = 3;
		}
		return $status;
	}
	
	/**
	 * 更新赛程球队之后跑一遍获取mid
	 */
	public function schedule()
	{
		$res = $this->match_model->getSchedule();
		foreach ($res as $val) {
			$this->match_model->updateSchedule($val['mid'], $val['id']);
		}
	}
}
