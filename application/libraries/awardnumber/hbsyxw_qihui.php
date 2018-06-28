<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 惊喜11选5开奖号码抓取   齐汇
 */
include_once dirname(__FILE__) . '/qihui.php';
class Hbsyxw_Qihui extends Qihui
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_hbsyxw_paiqi';
    
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
		        $awardNum = $this->getAwardNumber($issue, $lid);
		        if($awardNum == '')
		        {
		            $stopFlag = false;
		        }
		        else
		        {
		            $awardArr[$issue] = $awardNum;
		        }
		    }
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getHbsyxwBonus(), $lid);
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
