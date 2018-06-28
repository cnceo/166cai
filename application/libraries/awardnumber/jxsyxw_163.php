<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 新11选5开奖号码抓取   网易
 */
include_once dirname(__FILE__) . '/base.php';
class Jxsyxw_163 extends base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_jxsyxw_paiqi';
    
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
	    $url = 'http://caipiao.163.com/award/jx11xuan5/';
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
			$awardArr = array();
			foreach ($matches[1] as $key => $issue)
			{
				$award = trim($matches[2][$key]);
				if($award && in_array($issue, $issues))
				{
					$awardArr[$issue] = str_replace(" ", ",", $award);
				}
			}
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getJxsyxwBonus(), $lid);
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
