<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 客户端APP - 数字彩开奖 - 小米推送
 * @date:2016-02-18
 */
class Cli_Cfg_Award_Jpush extends MY_Controller
{
	// 推送彩种及表名
	private static $DB_NAMES = array(
		51 => array(
			'enName' => 'ssq',
			'cnName' => '双色球',
			'tagName' => 'ssq'
		), 
		23529 => array(
			'enName' => 'dlt',
			'cnName' => '大乐透',
			'tagName' => 'dlt'
		),
		33 => array(
			'enName' => 'pl3',
			'cnName' => '排列三',
			'tagName' => 'pl3'
		),
		35 => array(
			'enName' => 'pl5',
			'cnName' => '排列五',
			'tagName' => 'pl5'
		),
        52 => array(
        	'enName' => 'fc3d',
			'cnName' => '福彩3D',
			'tagName' => 'fucai3d'
        ),
        11 => array(
        	'enName' => 'sfc',
			'cnName' => '胜负彩/任选九',
			'tagName' => 'sfc_rx9'
        ),
    );
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('jpush_model','Jpush');
		$this->load->library('mipush');
	}
	
	/*
	 * 客户端APP - 数字彩开奖小米推送主进程
	 * @date:2016-02-19
	 */
	public function index()
	{
		$lotteryInfo = self::$DB_NAMES;
		if(!empty($lotteryInfo))
		{
			foreach ($lotteryInfo as $lid => $items) 
			{			
				$awardInfo = $this->Jpush->getLastAwards($lid, $items['enName']);

				if(!empty($awardInfo['p_issue']) && !empty($awardInfo['p_awardNum']))
				{
					if( empty($awardInfo['j_issue']) || $awardInfo['j_synflag'] == 0 )
					{
						$issueFormat = (substr($awardInfo['p_issue'], 0, 2) == '20') ? substr($awardInfo['p_issue'], 2) : $awardInfo['p_issue'];
						$msg_content = $items['cnName'] . " 第" . $issueFormat . "期 开奖号";
						$tagName = $items['tagName'];
						$awardNumStr = str_replace('|', ',', $awardInfo['p_awardNum']);

						$pushData = array(
							'type'         	=>	'num_awards', 
			                'topic'        	=>  $tagName,
			                'lid'          	=>  $lid,
			                'title'        	=>  $msg_content,
			                'description'  	=>  '',
			                'awardNum'	   	=>	$awardInfo['p_awardNum']
						);
						$this->mipush->index('topic', $pushData);

						// 更新推送
						$awards = array(
							'lid' => $lid,
							'issue' => $awardInfo['p_issue'],
							'synflag' => 1
						);
						$this->Jpush->updateSynflag($awards);
					}
				}
			}
		}
	}

}