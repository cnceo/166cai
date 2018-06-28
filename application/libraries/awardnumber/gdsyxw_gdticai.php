<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 乐11选5开奖号码抓取   广东体彩网
 */
include_once dirname(__FILE__) . '/base.php';
class Gdsyxw_Gdticai extends Base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_gdsyxw_paiqi';
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
			$url = 'https://www.gdlottery.cn/odata/zst11xuan5.jspx';
			$content = $this->get_content($url);
			preg_match_all('/<tr>.*?<td.*?height="20".*?>(.*?)<\/td>.*?<td.*?height="20".*?>.*?<span.*?>.*?<strong>(.*?)<\/strong>.*?<\/td>.*?<\/tr>/is', $content, $res);
            if(empty($res[1])) return ;
			$awardArr = array();
			foreach ($res[1] as $key => $issue)
			{
			    $issue = trim($issue);
				//获取下一个期次
				if(in_array($issue, $issues))
				{
				    $awardArr[$issue] = str_replace('，', ',', trim($res[2][$key]));
				}
			}
			
			if($awardArr)
			{
			    $stopFlag = $this->saveAwardData($awardArr, $this->getGdsyxwBonus(), $lid);
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

