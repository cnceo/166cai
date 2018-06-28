<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** * 吉林快三开奖号码抓取   吉林福彩网 */include_once dirname(__FILE__) . '/base.php';
class Jlks_Fucai extends Base
{    /**     * 排期表名定义     * @var string     */    protected $paiqiTable = 'cp_jlks_paiqi';
	public function __construct()
	{	    parent::__construct();
	}
		/**	 * 抓取入口	 */
	public function capture($lid)
	{
	    $stopFlag = true;	    $issues = $this->CI->awardnumber_model->getLotteryIssues($this->paiqiTable);
		if($issues)
		{
			$url = 'http://www.jlfc.com.cn/View/PCLottery.aspx?PlayTypeID=4';
			$content = $this->get_content($url);
 			$preg = '.*?<table.*?id="KSReport">.*?<tbody>(.*?)<tr id="tr_jrwin1">.*?<\/tbody>.*';
            preg_match("/$preg/is", $content, $match);
            preg_match_all("/(?:<tr>.*?(\d{9}).*?(\d+)&nbsp;.*?(\d+)&nbsp;.*?(\d+)&nbsp;[^<]*?<\/div>[^<]*?<\/td>[^<]*?<\/tr>[^<]*?)+/is", $match[1], $res);
            if(empty($res[1]))            {                $stopFlag = false;                return $stopFlag;            }
			$awardArr = array();
			foreach ($res[1] as $key => $issue)
			{
				$issue = '20'.$issue;
				//获取下一个期次
				if(in_array($issue, $issues))
				{
					$awardNumArr = array($res[2][$key],$res[3][$key],$res[4][$key]);
					sort($awardNumArr);
					$awardArr[$issue] = explode(',', $awardNumArr);
				}
			}						if($awardArr)			{			    $stopFlag = $this->saveAwardData($awardArr, $this->getJlksBonus(), $lid);			}
			//抓取期次和查询期次数量不相等说明有期次未抓取到			if(count($awardArr) != count($issues))			{			    $stopFlag = false;			}
		}				if($stopFlag)		{		    //检查是否存在正在开奖期次		    $iresult = $this->CI->awardnumber_model->getAwardIssue($this->paiqiTable);		    if($iresult)		    {		        $stopFlag = false;		    }		    		}				return $stopFlag;
	}
}
