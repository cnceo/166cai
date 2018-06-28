<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩足球】赛果抓取 -- 来源：310win.com
 * @author:shigx
 * @date:2015-03-19
 */

class Jczq_caike
{

	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('lib_comm');
		$this->CI->load->library('tools');
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
    	$url = 'http://www.310win.com/jingcaizuqiu/rangqiushengpingfu/kaijiang_jc_all.html';
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	$rule  = '<input.*?name=[\'"]__VIEWSTATE[\'"].*?value=[\'"](.*?)[\'"].*?';
    	$rule .= '<input.*?name=[\'"]__EVENTVALIDATION[\'"].*?value=[\'"](.*?)[\'"].*?';
    	preg_match("/$rule/is", $content, $matches);
    	$post_data['__VIEWSTATE'] = $matches[1];
    	$post_data['__EVENTVALIDATION'] = $matches[2];
    	$post_data['txtEndDate'] = date('Y-m-d', strtotime('+1 day', strtotime($date)));
    	$post_data['txtStartDate'] = $date;
    	$post_data['Button1'] = '';
    	$content = $this->CI->tools->request($url, $post_data);
    	
    	$date_arr = $this->getAllDates($post_data['txtStartDate'], $post_data['txtEndDate']);
    	unset($rule, $matches);
        $rule  = '<tr\s+id.*?class.*?onmouseover.*?onMouseOut.*?style.*?>.*?';
        $rule .= '<td>(.*?)(\d+)<br>(.*?) .*?<\/td>.*?';
        $rule .= '<td\s+style.*?>(.*?)<\/td>.*?';
        $rule .= '<td\s+style.*?>(.*?)(?:<b>.*?<\/b>)?<\/td>.*?';
        $rule .= '<td\s+style.*?>(.*?)<\/td>.*?';
        $rule .= '<td\s+style.*?>(.*?)<\/td>.*?';
        $rule .= '<td>(.*?)<\/td>.*?';
        $rule .= '<\/tr>';
        preg_match_all("/$rule/is", $content, $matches);
        unset($matches[0]);
        if(!isset($matches[1]) || empty($matches[1]))
        {
        	//TODO error记录
        	return ;
        }
        
        unset($matches[0]);
        $this->updateJczqScore($matches, $param['source'], $date_arr, $mids);
    }
    
    /*
     * 【竞彩足球】更新赛果
     * @author:shigx
     * @date:2015-03-18
     */
    private function updateJczqScore($datas, $source, $date_arr, $mids)
    {

    	if(!empty($datas))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
    		foreach ($datas[1] as $in => $val)
    		{
    			$date = $date_arr[trim($datas[3][$in])];
    			$weekes = $this->CI->tools->getWeekArrayByDate($date);
    			$mid = $weekes[$datas[1][$in]] . trim($datas[2][$in]);
    			if(!in_array($mid, $mids))
    			{
    				continue;
    			}
    			array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, $source, now())");
    			array_push($bdata['d_data'], $mid);
    			array_push($bdata['d_data'], $datas[1][$in] . $datas[2][$in]);
    			array_push($bdata['d_data'], trim($datas[4][$in]));
    			array_push($bdata['d_data'], trim($datas[5][$in]));
    			array_push($bdata['d_data'], $datas[7][$in]);
    			$half_score = str_replace('-', ':', trim($datas[8][$in]));
    			$half_score = $this->CI->lib_comm->score_filter($half_score);
    			array_push($bdata['d_data'], $half_score);
    			$full_score = str_replace('-', ':', trim($datas[6][$in]));
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
    
    /**
     * 处理日期数组
     * @param string $s	开始日期
     * @param string $e	结束日期
     * @return array()
     */
    private function getAllDates($s, $e)
    {
    	if (empty($s) || empty($e) || (strtotime($s) > strtotime($e)))
    	{
    		return array();
    	}
    
    	$datetime1 = new DateTime($s);
    	$datetime2 = new DateTime($e);
    	$interval  = $datetime1->diff($datetime2);
    	$days = $interval->format('%a');
    	for ($j = 0; $j <= $days; $j++)
    	{
	    	$time = strtotime("+$j days", strtotime($s));
	    	$key = date("m-d", $time);
	    	$val = date("Y-m-d", $time);
	    	$res[$key] = $val;
    	}
    	return $res;
    }
}