<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 惊喜11选5开奖号码抓取   湖北体彩网
 */
include_once dirname(__FILE__) . '/base.php';
class Hbsyxw_Hbtycp extends base
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
	    $url = 'http://caipiao.163.com/award/11xuan5/';
		if($issues)
		{
		    $awardArr = array();
		    for ($page = 1; $page > 0; $page--) 
		    {
		        //需要修复老数据时，调整page值即可
		        $url = "http://server.baibaocp.com/webticai/Foreignsyxw/foreign/wuhan/page/".$page."/num/30";
		        $content = $this->get_content($url);
		        $datas = json_decode($content, true);
		        if($datas['ret'] != '0' || empty($datas['data']))
		        {
		            $stopFlag = false;
		            return $stopFlag;
		        }
		        
		        foreach ($datas['data'] as $values)
		        {
		            if(isset($values['ball1']) && isset($values['ball2']) && isset($values['ball3']) && isset($values['ball4']) && isset($values['ball5']) && in_array($values['issue'], $issues))
		            {
		                $awardArr[$values['issue']] = $values['ball1'].",".$values['ball2'].",".$values['ball3'].",".$values['ball4'].",".$values['ball5'];
		            }
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
