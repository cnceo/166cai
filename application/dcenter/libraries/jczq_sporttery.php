<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩足球】赛果抓取 -- 来源：中国竞彩网
 * @author:liuli
 * @date:2015-03-16
 */

class Jczq_sporttery 
{

    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('lib_comm');
        $this->CI->load->library('tools');
        $this->CI->load->model('data_model');
    }

    private function get_dinfo(&$content)
    {
        $rule  = '<input.*?start_date.*?value=[\'"](.*?)[\'"].*?\/>.*?';
        $rule .= '<input.*?end_date.*?value=[\'"](.*?)[\'"].*?\/>.*?';
        preg_match("/$rule/is", $content, $dinfos);
        $info['fdate'] = $dinfos['1'];
        $info['edate'] = $dinfos['2'];
        preg_match("/\/a>.*?<a.*?match_result.php\?page=(\d+)[^<>]+>尾页.*?<\/a>/is", $content, $dinfos);
        unset($dinfos[0]);
        $info['tpage'] = isset($dinfos[1]) ? $dinfos[1] : 0;
        return $info;
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
    		 
    		$this->get_content($date, $param, $mids);
    		$i++;
    	}
    }
    
    private function get_content($date, $param, $mids)
    {
    	$fdate = $date;
    	$edate = date('Y-m-d', strtotime('2 day', strtotime($date)));
    	$page = 1;
    	$url = "http://info.sporttery.cn/football/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
    	$content = $this->CI->tools->getCurlUrl($url);
    	$dinfos = $this->get_dinfo($content);
    	
    	$this->get_datas($content, $param, $mids);
    	
    	if($dinfos['tpage'] > 1)
    	{
    		for ($sp = 2; $sp <= $dinfos['tpage']; $sp++)
    		{
    			$page = $sp;
    			$url = "http://info.sporttery.cn/football/match_result.php?page=$page&search_league=&start_date=$fdate&end_date=$edate";
    			$content = $this->CI->tools->getCurlUrl($url);
    			$this->get_datas($content, $param, $mids);
    		}
    	}
    	
    	//抓取遗漏数据
    	//To do..
    }

    /*
     * 【竞彩足球】处理分页数据
     * @author:liuli
     * @date:2015-03-20
     */
    private function get_datas($content, $param, $mids)
    {
        //赛果抓取正则
        $reg  = '<tr.*?>.*?';
        $reg .= '<td.*?>(.*?)<\/td>.*?';
        $reg .= '<td.*?>(.*?)(\d+)<\/td>.*?';
        $reg .= '<td.*?>(.*?)<\/td>.*?';
        $reg .= '<td.*?<span.*?>(.*?)<\/span>.*?<span.*?<span.*?>(.*?)<\/span>.*?<\/td>.*?';
        $reg .= '<td.*?><span.*?>(.*?)<\/span><\/td>.*?';
        $reg .= '<td.*?><span.*?>(.*?)<\/span><\/td>.*?';
        $reg .= '<\/tr>';
        preg_match_all("/$reg/is", $content, $matches);
        unset($matches[0]);
        if(!empty($matches[1]))
        {   

            $fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'status', 'source', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;
            foreach ($matches[2] as $key => $match) 
            {
                $weeks = $this->CI->tools->getWeekArrayByDate($matches[1][$key]);
                $mid = $weeks[$match].trim($matches[3][$key]);
                if(!in_array($mid, $mids))
                {
                	continue;
                }
                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, {$param['source']}, now())");
                array_push($bdata['d_data'], $mid);
                array_push($bdata['d_data'], $match.$matches[3][$key]);
                array_push($bdata['d_data'], $matches[4][$key]);
                array_push($bdata['d_data'], $matches[5][$key]);
                array_push($bdata['d_data'], $matches[6][$key]);
                $half_score = $this->CI->lib_comm->score_filter($matches[7][$key]);
                array_push($bdata['d_data'], $half_score);
                $full_score = $this->CI->lib_comm->score_filter($matches[8][$key]);
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