<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【胜负彩】赛果抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-16
 */

class Jqc_163
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
        $url = 'http://live.caipiao.163.com/zcbf/?date='.$issue.'&gameType=f4cjq';
        $content = $this->CI->tools->get_content($url, __CLASS__ . $issue, 0, array('ENCODING' => 'gzip'));

        //正则  
        $reg = '<dd.*?score.*?=.*?[\'"](.*?)[\'"].*?';
        $reg .= 'halfScore.*?=.*?[\'"](.*?)[\'"].*?';
        $reg .= 'bidScore.*?=.*?[\'"](.*?)[\'"].*?';
        $reg .= '<em.*?>.*?';
        $reg .= '<span.*?">(.*?)<\/span>.*?';
        $reg .= '<\/em>.*?';
        $reg .= '<em.*?\/em>.*?';
        $reg .= '<em.*?\/em>.*?';
        $reg .= '<\/dd>';
        preg_match_all("/$reg/is",$content,$matches);
        unset($matches[0]);
        $this->updateJqcScore($matches, $param['source'], $issue, $mname);
    }


    private function updateJqcScore($matches, $source, $issue, $mname)
    {
        if(!empty($matches)){
            $fields = array('mid', 'mname', 'half_score', 'full_score', 'status', 'source', 'created');
            $bdata['s_data'] = array();
            $bdata['d_data'] = array();
            $count = 0;
            foreach ($matches[1] as $key => $match)
            {
                if(!in_array($matches[4][$key], $mname))
                {
                    continue;
                }
                array_push($bdata['s_data'], "(?, ?, ?, ?, ?, $source, now())");
                array_push($bdata['d_data'], $this->CI->lib_comm->format_issue($issue, 0));
                array_push($bdata['d_data'], trim($matches[4][$key]));
                array_push($bdata['d_data'], $this->CI->lib_comm->score_filter($matches[2][$key]));
                $full_score = $this->CI->lib_comm->score_filter($matches[1][$key]);
                array_push($bdata['d_data'], $full_score);
                array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));

                if(++$count >= 500)
                {
                    $this->CI->data_model->insertJqcScore($fields, $bdata);
                    $bdata['s_data'] = array();
                    $bdata['d_data'] = array();
                    $count = 0;
                }                
            }
            if(!empty($bdata['s_data']))
            {
                $this->CI->data_model->insertJqcScore($fields, $bdata);
                $bdata['s_data'] = array();
                $bdata['d_data'] = array();
                $count = 0;
            }
        }
    }
  }