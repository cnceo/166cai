<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【北京单场】赛果抓取 -- 来源：500万
 * @author:liuli
 * @date:2015-03-16
 */

class Bjdc_500 
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
    	foreach ($data as $issue => $mnames)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		$issue = substr(date('Y-m-d'), 2, 1).$issue;
    		$this->get_datas($issue, $param, $mnames);
    		$i++;
    	}
    }

	private function get_datas($issue, $param, $mnames)
    {
    	$url = "http://zx.500.com/zqdc/kaijiang.php?playid=0&expect={$issue}";
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	 //抓取赛事信息
        $reg = '/<tr.*?>[\s\S]*?<td.*?>(\d+)<\/td>[\s\S]*?<td.*?>.*?<a.*?>(.*?)<\/a>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<a.*?>(.*?)<\/a>.*?<\/td>[\s\S]*?<td.*?>(.*?)<\/span><\/td>[\s\S]*?<td.*?>.*?<a.*?>(.*?)<\/a>.*?<\/td>[\s\S]*?<td.*?>(.*?)<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<td.*?>.*?<\/td>[\s\S]*?<\/tr>/i';
        preg_match_all($reg, $content, $matches);
        unset($matches[0]);
        $this->updateBjdcScore($matches, $issue, $param['source'], $mnames);
    	
    }

    private function updateBjdcScore($matches, $issue, $source, $mnames)
    {
    	if(!empty($matches))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'half_score', 'full_score', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
    		foreach ($matches[1] as $key => $match)
    		{
    			if(!in_array($match, $mnames))
    			{
    				continue;
    			}
    			$score = $this->getScore($matches[6][$key]);
    			array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, " . $score['status'] . 
    			", $source, now())");
    			array_push($bdata['d_data'], $this->CI->lib_comm->format_issue($issue, 1));
    			array_push($bdata['d_data'], $match);
    			array_push($bdata['d_data'], $matches[2][$key]);
    			array_push($bdata['d_data'], trim($matches[3][$key]));
    			array_push($bdata['d_data'], trim($matches[5][$key]));
    			array_push($bdata['d_data'], $score['half_score']);
    			array_push($bdata['d_data'], $score['full_score']);
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertBjdcScore($fields, $bdata);
    				$bdata['s_data'] = array();
    				$bdata['d_data'] = array();
    				$count = 0;
    			}
    		}
    		if(!empty($bdata['s_data']))
    		{
    			$this->CI->data_model->insertBjdcScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
    		}
    	}
    }
    /*
     * 【北京单场】赛果抓取 获取赛果状态
     * @author:liuli
     * @date:2015-03-12
     */
    private function getScore($score)
    {
        $scores = array(
            'half_score' => '',
            'full_score' => '',
            'status' => '0'
        );
        if(!empty($score))
        {
            preg_match_all('/\((\d+:\d+)\).*?(\d+:\d+)/i', $score, $matches);
            $matches[1][0] = isset($matches[1][0]) ? $matches[1][0] : '';
            $matches[2][0] = isset($matches[2][0]) ? $matches[2][0] : '';
            $scores['half_score'] = $this->CI->lib_comm->score_filter($matches[1][0]);
            $scores['full_score'] = $this->CI->lib_comm->score_filter($matches[2][0]);
            $scores['status'] = $this->CI->lib_comm->getStatus($scores['full_score']);
        }
        return $scores;
    }

}