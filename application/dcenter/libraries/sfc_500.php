<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sfc_500
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
    	$url = 'http://live.500.com/zucai.php?e=' . $issue;
    	$content = $this->CI->tools->get_content($url, __CLASS__ . $issue);
    	preg_match('/<table.*?table_match.*?>.*?<tbody>(.*?)<\/tbody>/is', $content, $matches);
    	unset($matches[0]);

        $url2 = "http://live.500.com/static/info/bifen/xml/livedata/sfc/{$issue}Full.txt?_=" . time();

        // $content2 = $this->CI->tools->get_content($url2, __CLASS__ . $issue);
        //获取比分
        // $scores = $this->CI->lib_comm->getTczqScore($content2);

    	$rule  = '<tr.*?fid="(.*?)".*?>.*?<td.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?<a.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?<a.*?clt1.*?>(.*?)<\/a><a.*?clt3.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?><a.*?>(.*?)<\/a>.*?<\/td>.*?';
    	$rule .= '<td.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?<\/td>.*?';
    	$rule .= '<td.*?>.*?<strong.*?>(.*?)<\/strong>.*?<\/td>.*?';
    	$rule .= '<\/tr>';
    	preg_match_all("/$rule/is", $matches[1], $content);
    	unset($content[0]);

    	$this->updateSfcScore($content, $param['source'], $issue, $mname);
    }
    
	private function updateSfcScore($matches, $source, $issue, $mname)
    {
    	if(!empty($matches))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'result', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
            foreach ($matches[2] as $key => $match)
            {
                if(!in_array($match, $mname))
                {
                    continue;
                }
            	array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, ?, ?, $source, now())");
            	array_push($bdata['d_data'], $this->CI->lib_comm->format_issue($issue, 0));
    			array_push($bdata['d_data'], $match);
    			array_push($bdata['d_data'], trim($matches[3][$key]));
    			array_push($bdata['d_data'], trim($matches[5][$key]));
    			array_push($bdata['d_data'], trim($matches[8][$key]));
                // 半场比分
                preg_match('/(\d+).*?(\d+)/is', trim($matches[9][$key]), $halfMatch);
                $half_score = (isset($halfMatch[1]) && isset($halfMatch[2]))?$halfMatch[1] . ':' . $halfMatch[2]:'';
                array_push($bdata['d_data'], $this->CI->lib_comm->score_filter($half_score));
                // 全场比分
                $full_score = (is_numeric(trim($matches[6][$key])) && is_numeric(trim($matches[7][$key])))?trim($matches[6][$key]) . ':' . trim($matches[7][$key]):'';
                array_push($bdata['d_data'], $this->CI->lib_comm->score_filter($full_score));
    			array_push($bdata['d_data'], $matches[10][$key]);
    			array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertSfcScore($fields, $bdata);
	    			$bdata['s_data'] = array();
	    			$bdata['d_data'] = array();
	    			$count = 0;
    			}
            }
    		if(!empty($bdata['s_data']))
	        {
    			$this->CI->data_model->insertSfcScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
	        }
        }
    }
    
    

    /*
     * 【竞彩足球/篮球】赛果抓取 获取赛果状态
     * @author:liuli
     * @date:2015-03-12
     */
    private function getStatus($status)
    {
        if( $status == '完')
        {
            $status = 1;
        }else
        {
            $status = 0;
        }
        return $status;
    }

}