<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 重庆时时彩开奖号码抓取   网易
 */
include_once dirname(__FILE__) . '/base.php';
class Cqssc_163 extends base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_cqssc_paiqi';
    
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 抓取入口
	 */
	public function capture($lid)
	{
	    $stopFlag = true;
	    $issues = $this->CI->awardnumber_model->getLotteryIssues($this->paiqiTable);
		if($issues)
		{
		    $url = 'http://caipiao.163.com/award/cqssc/';
		    $content = $this->get_content($url, array('ENCODING' => 'gzip'));
		    $rule = '<td.*?data-win-number=\'(.*?)\'.*?data-period="(.*?)".*?>.*?<\/td>';
			preg_match_all("/$rule/is", $content, $matches);
			if(empty($matches[1]) || empty($matches[2]))
			{
			    $stopFlag = false;
			    return $stopFlag;
			}
			$awardArr = array();
			foreach ($matches[2] as $key => $issue)
			{
			    $award = trim($matches[1][$key]);
				if($award && in_array($issue, $issues))
				{
				    $awards = array_map('trim', explode(' ', $award));
				    $awardArr[$issue] = implode(',', $awards);
				}
			}
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getCqsscBonus(), $lid);
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
