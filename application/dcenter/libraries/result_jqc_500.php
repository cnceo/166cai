<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Result_Jqc_500
{

	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('lib_comm');
		$this->CI->load->library('tools');
		$this->CI->load->model('data_model');
	}
	
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
    		 
    		$url = "http://kaijiang.500.com/shtml/jq4/{$issue}.shtml";
    		$content = $this->CI->tools->get_content($url, __CLASS__);
    		$this->get_datas($param['source'], $content);
    		$i++;
    	}
    }
    
    private function get_datas($source, $content)
    {
    	$d_data = array();
    	$reg  = '<a.*?change_date.*?>(.*?)<\/a>.*?';
    	$reg .= '<div.*?class=[\'"]iSelectList[\'"]>(.*?)<\/div>.*?';
    	$reg .= '<table.*?kj_tablelist02.*?>.*?<tr.*?<\/tr>.*?<tr.*?<\/tr>.*?';
    	$reg .= '<tr.*?>(.*?)<\/tr>.*?<tr>.*?<span.*?>(.*?)<\/span>.*?<span.*?>(.*?)<\/span>.*?<\/table>.*?';
    	$reg .= '<table.*?kj_tablelist02.*?>.*?<tr.*?<\/tr>.*?<tr.*?<\/tr>';
    	$reg .= '((?:.*?<tr.*?align=[\'"]center[\'"].*?\/tr>.*?)+).*?';
    	$reg .= '<\/table>';
    	
    	preg_match("/$reg/is", $content, $matches);
		unset($matches[0]);
		$issue = $matches[1]; 
		preg_match_all('/<a.*?>(.*?)<\/a>/is', $matches[2], $issues);
		unset($issues[0]);
		preg_match_all('/<td.*?span.*?>(.*?)<\/span>.*?<\/td>/is', $matches[3], $results);
		unset($results[0]);
		$s_data = array();
		array_push($s_data, "(?, ?, ?, ?, ?, ?, ?, $source, now())");
		$d_data['mid'] = $this->CI->lib_comm->format_issue($issue, 0);
		foreach ($results[1] as $key=>$val)
		{
			$val = trim($val);
			if(!is_numeric($val))
			{
				$results[1][$key] = '*';
			}
		}
		$d_data['result'] = implode(',', $results[1]);
		$d_data['sale'] = $this->CI->lib_comm->format_num($matches[4]);
		$d_data['award'] = '0';
		$reg = '<tr.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>';
		$pre_re = preg_match_all("/$reg/is", $matches[6], $details);
		$dfields = array();
		if($pre_re)
		{
			unset($details[0]);
			foreach ($details[1] as $in => $lev)
			{
				$levs = array('一等奖' => '1dj', '二等奖' => '2dj', '三等奖' => '3dj');
				$dfields[$levs[trim($lev)]]['zs'] = $this->CI->lib_comm->format_num($details[2][$in]);
				$dfields[$levs[trim($lev)]]['dzjj'] = $this->CI->lib_comm->format_num($details[3][$in]);
			}
		}
		$d_data['award_detail'] = json_encode($dfields);
		$d_data['status'] = $this->CI->lib_comm->getStatus($d_data['result'], array('lid' => 'tczq', 'issue' => $awardDetail['mid'], 'ctype' => 3));
		$d_data['rstatus'] = $d_data['sale'] > 0 ? 1 : 0;
		
		$this->updateJqcScore($s_data, $d_data);
		return $issues[1];
    }
    
	private function updateJqcScore($s_data, $d_data)
    {
    	$bdata['s_data'] = $s_data;
    	$bdata['d_data'] = $d_data;
    	$this->CI->data_model->insertJqcResult($bdata);
    }
}