<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【胜负彩】赛果抓取 -- 来源：彩客
 * @author:liuli
 * @date:2015-03-23
 */

class Result_sfc_310
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('tools');
        $this->CI->load->library('lib_comm');
        $this->CI->load->model('data_model');
    }

    //主函数
    public function capture($param, $data)
    {
    	$issues = $data['issue'] ? $data['issue'] : array();
    	if(empty($issues))
    	{
    		return ;
    	}
    	$num = $data['num'] ? $data['num'] : 1;
    	$i = 1;
    	foreach ($issues as $issue)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		 
    		$this->get_content($param, $issue);
    		$i++;
    	}    
    }
    
    private function get_content($param, $issue)
    {
    	$issues = '20'.$issue;    //2015073
    	$url = 'http://www.310win.com/zucai/14changshengfucai/kaijiang_zc_1.html';
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	$reg = '<select.*?id=[\'"]dropIssueNum[\'"].*?>(.*?)<\/select>';
    	preg_match("/$reg/is", $content, $selects);
    	unset($selects[0]);
    	$result = preg_match_all('/<option(?:.*?selected=[\'"](.*?)[\'"])?.*?value=[\'"](.*?)[\'"].*?>(.*?)<\/option>/is', $selects[1], $matches);
    	
    	if($result)
    	{
    		unset($matches[0]);
    		foreach ($matches[1] as $in => $issue)
    		{
    			if(!empty($issues))
    			{
    				if($matches[3][$in] == $issues)
    				{
    					$this->get_datas($matches[2][$in],$issues,$param);
    					break;
    				}
    			}
    			elseif(!empty($issue))
    			{
    				$this->get_datas($matches[2][$in],$matches[3][$in],$param);
    				break;
    			}
    		}
    	}
    }

    private function get_datas($issueID,$issue,$param)
    {
        //开奖详情请求接口
        $request_url = "http://www.310win.com/Info/Result/Soccer.aspx?load=ajax&typeID=1&IssueID={$issueID}&randomT-_-=" . time();

        $awardResponse = $this->CI->tools->request($request_url);

        if(!empty($awardResponse))
        {
            $awardResponse = json_decode($awardResponse,true);           
        }

        $result = str_split($awardResponse['Result']);
        $rule = '/<span.*?mm.*?>(.*?)<\/span>/i';
        $rulePool = '/<span.*?>(.*?)<\/span>.*?<span.*?>(.*?)<\/span>.*?<span.*?>(.*?)<\/span>/i';
        preg_match($rule, $awardResponse['Bonus'][0]['BasicBonus'], $ydj);
        preg_match($rule, $awardResponse['Bonus'][1]['BasicBonus'], $edj);
        preg_match($rulePool, $awardResponse['Bottom'], $pools);

        $awardDetail = array();
        $awardDetail['lid'] = 'sfc';
        $awardDetail['mid'] = $this->CI->lib_comm->format_issue($issue, 2);
        $awardDetail['result'] = implode(',', $result);
        
        $awardDetail['sfc_sale'] = $this->CI->lib_comm->format_num($pools[1]);
        $awardDetail['rj_sale'] = $this->CI->lib_comm->format_num($pools[2]);
        $awardDetail['award'] = $this->CI->lib_comm->format_num($pools[3]);

        $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][0]['BasicStakes']);
        $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][0]['BasicBonus']);
        $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][1]['BasicStakes']);
        $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][1]['BasicBonus']);
        $dfields['rj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][2]['BasicStakes']);
        $dfields['rj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Bonus'][2]['BasicBonus']);
        $awardDetail['award_detail'] = json_encode($dfields);

        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($awardDetail['result'], array('lid' => 'tczq', 'issue' => $awardDetail['mid'], 'ctype' => 1));
		// $awardDetail['rstatus'] = $awardDetail['sfc_sale'] > 0 ? 1 : 0;
        $awardDetail['rstatus'] = $dfields['rj']['dzjj'] > 0 ? 1 : 0;
        $this->CI->data_model->insertSfcAwards($awardDetail);
    }

}