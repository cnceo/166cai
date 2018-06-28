<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩篮球】赛果抓取 -- 来源：澳客网
 * @author:shigx
 * @date:2017-06-07
 */

class Jclq_Okooo
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
    	$num = $data['num'] ? $data['num'] : 1;
    	unset($data['num']);
    	if(empty($data))
    	{
    		return ;
    	}
    	$i = 1;
    	foreach ($data as $date => $mids)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		$this->get_datas($date, $param, $mids);
    		$i++;
    	}
    }
    
    private function get_datas($date, $param, $mids)
    {
    	$endDate = date('Y-m-d', strtotime('+3 day', strtotime($date)));
    	$url = "http://www.okooo.com/jingcailanqiu/kaijiang/?StartDate={$date}&EndDate={$endDate}";
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	$rule = '.*?<table.*?class="tableborder.*?>.*?<tr.*?>.*?<\/tr>.*?<tr.*?>.*?<\/tr>(.*?)<\/table>.*?';
    	preg_match("/$rule/is", $content, $matches);
    	if(!isset($matches[1]) || empty($matches[1]))
        {
        	//TODO error记录
        	return ;
        }
        
        $content = $matches[1];
        $rule  = '<tr.*?class=.*?>.*?<td.*?>(.*?)(\d+)<\/td>.*?';
        $rule .= '<td.*?>.*?<span.*?><a.*?>(.*?)<\/a><\/span>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
        $rule .= '<td.*?>(.*?)<\/td>.*?';
        $rule .= '<\/tr>';
        preg_match_all("/$rule/is", $content, $matches);
        unset($matches[0]);
        if(!isset($matches[1]) || empty($matches[1]))
        {
        	//TODO error记录
        	return ;
        }
        $weekes = $this->CI->tools->getWeekArrayByDate($date);
        $this->updateJclqScore($matches, $param['source'], $weekes, $mids);
    }
    
    private function updateJclqScore($matches, $source, $weeks, $mids)
    {
    	if(!empty($matches))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'full_score', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
	    	foreach ($matches[1] as $key => $match) 
	       	{
	       		$mid = $weeks[$match].trim($matches[2][$key]);
	       		if(!in_array($mid, $mids))
	       		{
	       			continue;
	       		}
	       		array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, $source, now())");
    			array_push($bdata['d_data'], $mid);
    			array_push($bdata['d_data'], $match.$matches[2][$key]);
    			array_push($bdata['d_data'], $matches[3][$key]);
    			array_push($bdata['d_data'], trim($matches[5][$key]));
    			array_push($bdata['d_data'], trim($matches[4][$key]));
    			$full_score = str_replace('-', ':', trim($matches[6][$key]));
    			$full_score = $this->CI->lib_comm->score_filter($full_score);
    			array_push($bdata['d_data'], $full_score);
    			array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertJclqScore($fields, $bdata);
    				$bdata['s_data'] = array();
    				$bdata['d_data'] = array();
    				$count = 0;
    			}
	        }
	        if(!empty($bdata['s_data']))
	        {
    			$this->CI->data_model->insertJclqScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
	        }
    	}
    }
}