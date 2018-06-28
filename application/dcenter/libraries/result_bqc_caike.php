<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【半全场】赛果抓取 -- 来源：310win.com
 * @author:shigx
 * @date:2015-03-19
 */

class Result_bqc_caike
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
    	$issues_f = $data['issue'] ? $data['issue'] : array();
    	if(empty($issues_f))
    	{
    		return ;
    	}
    	 
    	$url = 'http://www.310win.com/zucai/6changbanquanchang/kaijiang_zc_3.html';
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	$rule  = '<select\s+name=[\'"]dropIssueNum[\'"]\s+id=[\'"]dropIssueNum[\'"].*?>(.*?<option.*?selected=[\'"]selected[\'"].*?value=[\'"](.*?)[\'"].*?>.*?<\/option>.*?)<\/select>';
    	preg_match("/$rule/is", $content, $matches);
    	if(!isset($matches[1]) || empty($matches[1]))
    	{
    		return ;
    	}
    	preg_match_all('/<option.*?value=[\'"](\d+)[\'"]>(.*?)<\/option>/is', $matches[1], $issues);
    	if(!isset($issues[1]) || empty($issues[1]))
    	{
    		return ;
    	}
    	 
    	$issue_arr = array();
    	foreach ($issues[2] as $key => $val)
    	{
    		$issue = $this->CI->lib_comm->format_issue($val, 2);
    		if(in_array($issue, $issues_f))
    		{
    			$issue_arr[$issue] = $issues[1][$key];
    		}
    	}
    	 
    	$num = $data['num'] ? $data['num'] : 1;
    	$i = 1;
    	foreach ($issue_arr as $issue)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		 
    		$this->get_datas($issue, $param);
    		$i++;
    	}
    }
    
    private function get_datas($issue, $param)
    {
    	$url = "http://www.310win.com/Info/Result/Soccer.aspx?load=ajax&typeID=3&IssueID={$issue}&randomT-_-=" . time();
    	$respone = $this->CI->tools->get_content($url, __CLASS__);
    	$respone = json_decode($respone, true);
    	$this->updateBqcResult($respone, $param['source']);
    }
    
    /*
     * 【进球彩】更新赛果
     * @author:shigx
     * @date:2015-03-19
     */
    private function updateBqcResult($datas, $source)
    {

    	if(!empty($datas))
    	{
    		$result = str_split($datas['Result']);
    		$rule = '.*?<span.*?>(.*?)<\/span>.*?<span.*?>(.*?)<\/span>';
    		preg_match("/$rule/is", $datas['Bottom'], $matchs);
    		$sale = preg_replace('/[^\d\.]/is', '', $matchs[1]);
    		$award = preg_replace('/[^\d\.]/is', '', $matchs[2]);
    		unset($matchs);
    		$award_detail['1dj']['zs'] = $this->CI->lib_comm->format_num($datas['Bonus'][0]['BasicStakes']);
    		$award_detail['1dj']['dzjj'] = $this->CI->lib_comm->format_num(strip_tags($datas['Bonus'][0]['BasicBonus']));
    		$d_data['mid'] = $this->CI->lib_comm->format_issue($datas['IssueNum'], 2);
    		$d_data['result'] = implode(',', $result);
    		$d_data['sale'] = $sale;
    		$d_data['award'] = '0'; //$award
    		$d_data['award_detail'] = json_encode($award_detail);
    		$d_data['status'] = $this->CI->lib_comm->getStatus($d_data['result'], array('lid' => 'tczq', 'issue' => $awardDetail['mid'], 'ctype' => 2));
    		$d_data['rstatus'] = $d_data['sale'] > 0 ? 1 : 0;
    		$s_data = array();
    		array_push($s_data, "(?, ?, ?, ?, ?, ?, ?, $source, now())");
    		$bdata['s_data'] = $s_data;
    		$bdata['d_data'] = $d_data;
    		$res = $this->CI->data_model->insertBqcResult($bdata);
    		if(!$res)
    		{
    			log_message('error', '写入数据库失败');
    		}
    	}
    }
}