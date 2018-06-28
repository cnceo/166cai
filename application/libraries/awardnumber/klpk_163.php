<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 快乐扑克开奖号码抓取   网易
 */
include_once dirname(__FILE__) . '/base.php';
class Klpk_163 extends Base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_klpk_paiqi';
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 抓取入口
	 * @param unknown_type $param
	 */
	public function capture($lid)
	{
	    $stopFlag = true;
	    $issues = $this->CI->awardnumber_model->getLotteryIssues($this->paiqiTable);
	    $url = 'http://caipiao.163.com/award/kuailepuke/';
		if($issues)
		{
		    $content = $this->get_content($url, array('ENCODING' => 'gzip'));
			$rule = '<td.*?data-period="(.*?)".*?data-award="(.*?)".*?>.*?<\/td>';
			preg_match_all("/$rule/is", $content, $matches);
			if(empty($matches[1]) || empty($matches[2]))
			{
			    $stopFlag = false;
			    return $stopFlag;
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
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getKlpkBonus(), $lid);
			}
			//抓取期次和查询期次数量不相等说明有期次未抓取到
			if(count($awardArr) != count($issues))
			{
			    $stopFlag = false;
			}
		}
		
		if($stopFlag)
		{
		    //检查是否存在正在开奖期次
		    $iresult = $this->CI->awardnumber_model->getAwardIssue($this->paiqiTable);
		    if($iresult)
		    {
		        $stopFlag = false;
		    }
		}
		
		return $stopFlag;
	}
}
