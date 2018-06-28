<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bqc_500
{

	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('tools');
		$this->CI->load->library('lib_comm');
		$this->CI->load->model('data_model');
	}
	
    public function capture($param, $data)
    {
        $num = $data['num'] ? $data['num'] : 1;
        unset($data['num']);
    	$issues = $data ? $data : array();
    	if(empty($issues))
    	{
    		return ;
    	}
    	$i = 1;
    	foreach ($issues as $issue => $mname)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		 
    		$this->get_datas($param, $issue, $mname);
    		$i++;
    	}    
    }
    
    private function get_datas($param, $issue, $mname)
    {
    	$url = 'http://live.500.com/6chang.php?e=' . $issue;
    	$content = $this->CI->tools->get_content($url, __CLASS__ . $issue);
    	preg_match('/<table.*?table_match.*?>.*?<tbody>(.*?)<\/table>/is', $content, $matches);
    	unset($matches[0]);
    	$rule  = '<tr.*?fid=[\'"](.*?)[\'"].*?>.*?<td.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?>.*?<a.*?>(.*?)<\/a><\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?>(?:<span.*?>)?(.*?)(?:<\/span>)?<\/td>.*?';
    	$rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?<a.*?clt1.*?>(.*?)<\/a>.*?<a.*?clt3.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?>.*?<strong.*?>(.*?)<\/strong>.*?<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<\/tr>.*?<tr.*?>.*?<td.*?>.*?<strong.*?>(.*?)<\/strong>.*?<\/td>.*?<\/tr>';
    	preg_match_all("/$rule/is", $matches[1], $content);
    	unset($content[0]);
    	$this->updateBqcScore($content, $param['source'], $issue, $mname);
    }
    
	private function updateBqcScore($matches, $source, $issue, $mname)
    {
    	if(!empty($matches))
    	{
    		$url = "http://live.500.com/static/info/bifen/xml/livedata/zc6/{$issue}Full.txt?_=".time();
    		$json = $this->CI->tools->request($url);
    		$score = $this->CI->lib_comm->getTczqScore($json);
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'half_result', 'full_result', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
            foreach ($matches[1] as $key => $fid)
            {
                if(!in_array($matches[2][$key], $mname))
                {
                    continue;
                }
            	array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, $source, now())");
            	array_push($bdata['d_data'], $this->CI->lib_comm->format_issue($issue, 0));
    			array_push($bdata['d_data'], trim($matches[2][$key]));
    			array_push($bdata['d_data'], trim($matches[3][$key]));
    			array_push($bdata['d_data'], trim($matches[5][$key]));
    			array_push($bdata['d_data'], trim($matches[8][$key]));
    			$half_score = isset($score[$fid][1]) ? $score[$fid][1] : '';
    			array_push($bdata['d_data'], $half_score);
    			$full_score = isset($score[$fid][0]) ? $score[$fid][0] : '';
    			array_push($bdata['d_data'], $full_score);
    			array_push($bdata['d_data'], $matches[10][$key]);
    			array_push($bdata['d_data'], $matches[11][$key]);
    			array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertBqcScore($fields, $bdata);
	    			$bdata['s_data'] = array();
	    			$bdata['d_data'] = array();
	    			$count = 0;
    			}
            }
    		if(!empty($bdata['s_data']))
	        {
    			$this->CI->data_model->insertBqcScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
	        }
        }
    }
}