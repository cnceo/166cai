<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩篮球】赛果抓取 -- 来源：中国竞彩网
 * @author:liuli
 * @date:2015-03-16
 */

class Jclq_500
{
	private $CI;
	private $mids = array();
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
    	
    	foreach ($data as $val)
    	{
    		$this->mids = array_merge($this->mids, $val);
    	}
    	$i = 1;
    	foreach ($data as $date => $mids)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    	
    		$this->get_datas($date, $param);
    		$date1 = date('Y-m-d', strtotime('1 day', strtotime($date))); //解决跨天比赛抓取
    		$this->get_datas($date1, $param);
    		$i++;
    	}
    }
    
	private function get_datas($date, $param)
    {
    	$url = "http://zx.500.com/jclq/kaijiang.php?d={$date}";
        $content = $this->CI->tools->get_content($url, __CLASS__);
        $rule = '<input.*?id=["\']date["\'].*?value=["\'](.*?)["\'].*?\/>.*?<table.*?id=[\'"]dg1[\'"].*?>.*?<tr>.*?<\/table>.*?<\/th>[^\/th]*<\/tr>(.*?)<\/table>';
        preg_match("/$rule/is", $content, $matches);
        unset($matches[0]);
        $rule  = '<tr>.*?<td>(.*?)(\d+)<\/td>.*?';
        $rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
        $rule .= '<td.*?<\/td>.*?';
        $rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
        $rule .= '<td.*?<\/td>.*?';
        $rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
        $rule .= '<td.*?>(.*?)<\/td>.*?';
        $rule .= '<\/tr>';
        preg_match_all("/$rule/is", $matches[2], $content);
        unset($matches[0]);
        $weeks = $this->CI->tools->getWeekArrayByDate($date);
        $this->updateJclqScore($content, $param['source'], $weeks);
    }

    private function updateJclqScore($matches, $source, $weeks)
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
	       		if(!in_array($mid, $this->mids))
	       		{
	       			continue;
	       		}
	       		array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, $source, now())");
    			array_push($bdata['d_data'], $mid);
    			array_push($bdata['d_data'], $match.$matches[2][$key]);
    			array_push($bdata['d_data'], $matches[3][$key]);
    			array_push($bdata['d_data'], trim($matches[5][$key]));
    			array_push($bdata['d_data'], trim($matches[4][$key]));
    			$full_score = $this->CI->lib_comm->score_filter($matches[6][$key]);
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