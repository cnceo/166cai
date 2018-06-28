<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩足球】赛果抓取 -- 来源：澳客网
 * @author:shigx
 * @date:2017-06-07
 */

class Jczq_Okooo
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
    	$url = "http://www.okooo.com/jingcai/kaijiang/?LotteryType=SportteryWDL&StartDate={$date}&EndDate={$endDate}";
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
        $rule .= '<td.*?>.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
        $rule .= '<td.*?>.*?<\/td>.*?';
        $rule .= '<td.*?>(.*?)<\/td>.*?';
        $rule .= '<td.*?class=.*?>(.*?)<\/td>.*?';
        $rule .= '<\/tr>';
        preg_match_all("/$rule/is", $content, $matches);
        unset($matches[0]);
        if(!isset($matches[1]) || empty($matches[1]))
        {
        	//TODO error记录
        	return ;
        }
        $weekes = $this->CI->tools->getWeekArrayByDate($date);
        $this->updateJczqScore($matches, $param['source'], $weekes, $mids);
    }
    
    /**
     * 
     * @param unknown_type $datas
     * @param unknown_type $source
     * @param unknown_type $weekes
     * @param unknown_type $mids
     */
    private function updateJczqScore($datas, $source, $weekes=null, $mids)
    {

    	if(!empty($datas))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
    		foreach ($datas[1] as $in => $val)
    		{
    			$mid = $weekes[$datas[1][$in]] . trim($datas[2][$in]);
    			if(!in_array($mid, $mids))
    			{
    				continue;
    			}
    			array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, $source, now())");
    			array_push($bdata['d_data'], $mid);
    			array_push($bdata['d_data'], $datas[1][$in] . $datas[2][$in]);
    			array_push($bdata['d_data'], $datas[3][$in]);
    			array_push($bdata['d_data'], $datas[4][$in]);
    			array_push($bdata['d_data'], $datas[5][$in]);
    			$half_score = str_replace('-', ':', trim($datas[6][$in]));
    			$half_score = $this->CI->lib_comm->score_filter($half_score);
    			array_push($bdata['d_data'], $half_score);
    			$full_score = str_replace('-', ':', trim($datas[7][$in]));
    			$full_score = $this->CI->lib_comm->score_filter($full_score);
    			array_push($bdata['d_data'], $full_score);
    			array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertJczqScore($fields, $bdata);
    				$bdata['s_data'] = array();
    				$bdata['d_data'] = array();
    				$count = 0;
    			}
    		}
    		if(!empty($bdata['s_data']))
    		{
    			$this->CI->data_model->insertJczqScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
    		}
    	}
    }
}