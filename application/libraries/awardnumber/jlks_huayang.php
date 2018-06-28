<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 吉林快三开奖号码抓取  华阳
 */
include_once dirname(__FILE__) . '/huayang.php';
class Jlks_Huayang extends huayang
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_jlks_paiqi';
    
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
		    $awardArr = array();
		    foreach ($issues as $issue)
		    {
                        $issue = $this->formatIssue($issue, 56);
		        $awardNum = $this->getAwardNumber($issue, $lid);
		        if($awardNum == '')
		        {
		            $stopFlag = false;
		        }
		        else
		        {
                            $issue = '20' . $issue;
		            $awardArr[$issue] = $awardNum;
		        }
		    }
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getJlksBonus(), $lid);
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
        
    // 期次格式
    private function formatIssue($issue, $lid, $pre='')
    {
        $issue_format = array('56' => 2);
        if(empty($pre))
        {
            return substr($issue, $issue_format[$lid]);
        }
        else
        {
            return "$pre$issue";
        }
    }    
}
