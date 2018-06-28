<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 上海快三开奖号码抓取   福彩网
 */
include_once dirname(__FILE__) . '/base.php';
class Ks_Fucai extends base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_ks_paiqi';
    
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
	    $url = 'http://fucai.eastday.com/LotteryNew/K3Result.aspx';
		if($issues)
		{
		    $content = $this->get_content($url);
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
		        $stopFlag = false;
		        return $stopFlag;
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
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getKsBonus(), $lid);
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
