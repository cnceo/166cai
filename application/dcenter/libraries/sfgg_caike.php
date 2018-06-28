<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【北京单场】赛果抓取 -- 来源：310win.com
 * @author:shigx
 * @date:2015-03-18
 */

class Sfgg_caike
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
    		$this->get_datas($issue, $param, $mnames);
    		$i++;
    	}
    }
    
    private function get_datas($issue, $param, $mnames)
    {
    	$url = "http://www.310win.com/others/kaijiang_dc_11_{$issue}.html";
    	$content = $this->CI->tools->get_content($url, __CLASS__);
    	$rule  = '<tr.*?id.*?class.*?onmouseover.*?onMouseOut.*?style.*?>.*?';
    	$rule .= '<td>(.*?)<\/td>.*?';
    	$rule .= '<td.*?\/td>.*?';
    	$rule .= '<td.*?style.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?\/td>.*?';
    	$rule .= '<td.*?right.*?>(.*?)<\/td>.*?';
    	$rule .= '<td><span.*?style.*?>(.*?)<\/span>.*?<\/td>.*?';
    	$rule .= '<td.*?align.*?>(.*?)<\/td>.*?';
    	$rule .= '<td.*?\/td>.*?';
    	$rule .= '<td.*?\/td>.*?';
    	$rule .= '<td.*?\/td>.*?';
    	$rule .= '<\/tr>';
    	preg_match_all("/$rule/is", $content, $matches);
    	unset($matches[0]);
    	if(!isset($matches[1]) || empty($matches[1]))
    	{
    		//TODO error记录
    		return ;
    	}
        $this->updateSfggScore($matches, $param['source'], $issue, $mnames);
    }
    
    /*
     * 【北京单场】更新赛果
     * @author:shigx
     * @date:2015-03-18
     */
    private function updateSfggScore($datas, $source, $mid = '', $mnames)
    {
    	if(!empty($datas))
    	{
    		$fields = array('mid', 'mname', 'league', 'home', 'away', 'full_score', 'status', 'source', 'created');
    		$bdata['s_data'] = array();
    		$bdata['d_data'] = array();
    		$count = 0;
    		foreach ($datas[1] as $in => $val)
    		{
    			if(!in_array(trim($datas[1][$in]), $mnames))
    			{
    				continue;
    			}
    			array_push($bdata['s_data'], "(?, ?, ?, ?, ?, ?, ?, $source, now())");
    			array_push($bdata['d_data'], $this->CI->lib_comm->format_issue($mid, 0));
    			array_push($bdata['d_data'], trim($datas[1][$in]));
    			array_push($bdata['d_data'], trim($datas[2][$in]));
    			array_push($bdata['d_data'], trim($datas[3][$in]));
    			array_push($bdata['d_data'], strip_tags($datas[5][$in]));
    			$full_score = $this->CI->lib_comm->score_filter(str_replace('-', ':', $datas[4][$in]));
    			array_push($bdata['d_data'], $full_score);
    			array_push($bdata['d_data'], $this->CI->lib_comm->getStatus($full_score));
    			if(++$count >= 500)
    			{
    				$this->CI->data_model->insertSfggScore($fields, $bdata);
    				$bdata['s_data'] = array();
    				$bdata['d_data'] = array();
    				$count = 0;
    			}
    		}
    		if(!empty($bdata['s_data']))
    		{
    			$this->CI->data_model->insertSfggScore($fields, $bdata);
    			$bdata['s_data'] = array();
    			$bdata['d_data'] = array();
    			$count = 0;
    		}
    	}
    }
}