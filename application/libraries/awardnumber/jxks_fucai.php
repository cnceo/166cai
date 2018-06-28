<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 吉林快三开奖号码抓取   吉林福彩网
 */
include_once dirname(__FILE__) . '/base.php';
class Jxks_Fucai extends Base
{
    /**
     * 排期表名定义
     * @var string
     */
    protected $paiqiTable = 'cp_jxks_paiqi';
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
			$url = 'http://www.jxfczx.cn/report/K3_WinMessage.aspx';
			$content = $this->get_content($url);
 			$preg = 'class="win_deail".*?>(.*)<tr class="hote">';
            preg_match("/$preg/is", $content, $match);
            preg_match_all("/.*?termCode.*?(\d{9}).*?<b id=k3_0>(\d)<\/b><b id=k3_1>(\d)<\/b><b id=k3_2>(\d)<\/b>.*?<\/tr>+/is", $match[1], $res);
            if(empty($res[1])) return ;
			$awardArr = array();
			foreach ($res[1] as $key => $issue)
			{
				$issue = '20'.$issue;
				//获取下一个期次
				if(in_array($issue, $issues))
				{
					$awardNumArr = array($res[2][$key],$res[3][$key],$res[4][$key]);
					sort($awardNumArr);
					$awardArr[$issue] = implode(',', $awardNumArr);
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
}

