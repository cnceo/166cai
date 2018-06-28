<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 新11选5开奖号码抓取   齐汇
 */
include_once dirname(__FILE__) . '/hengju.php';
class Syxw_Hengju extends hengju
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_syxw_paiqi';
    
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
		        $awardNum = $this->getAwardNumber("20".$issue, $lid);
		        if($awardNum == '')
		        {
		            $stopFlag = false;
		        }
		        else
		        {
		            $newAwardNumArr = array();
		            for ($i = 0; $i < 5; $i++) {
		                array_push($newAwardNumArr, substr($awardNum, $i * 2, 2));
		            }
		            $awardArr[$issue] = implode(',', $newAwardNumArr);
		        }
		    }
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getSyxwBonus(), $lid);
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
