<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【竞彩足球】赛果抓取 -- 来源：500.com
 * @author:huxm
 * @date:2015-03-17
 */

class Jczq_500
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
    		
    		$url = "http://zx.500.com/jczq/kaijiang.php?d={$date}";
    		$this->get_datas($url, $param, $mids);
    		$i++;
    	}
    }
    
    private function get_datas($url, $param, $mids)
    {
    	$content = $this->CI->tools->get_content($url, __CLASS__);
        $rule  = '.*?<input.*?id=["\']date["\'].*?name=["\']date["\'].*?value=["\'](.*?)["\'].*?\/>.*?';
        $rule .= '<table.*?class=["\']ld_table["\']>.*?<tr>.*?<\/table>.*?<\/table>.*?<\/table>.*?<\/table>.*?<\/tr>(.*?)<\/table>.*?';
        preg_match("/$rule/is", $content, $matches);
        $cdate = $matches[1];
        $content =  $matches[2];
        $rule  = '<tr>.*?<td>(.*?)(\d+)<\/td>.*?';
        $rule .= '<td>.*?<a.*?class=[\'"]league[\'"].*?>(.*?)<\/a><\/td>.*?';
        $rule .= '<td.*?class=[\'"]eng[\'"]>.*?<\/td>.*?';
        $rule .= '<td.*?class=[\'"]text_r[\'"]><a.*?>(.*?)<\/a><\/td>.*?';
        $rule .= '<td.*?class=[\'"]eng[\'"]>.*?<\/td>.*?';
        $rule .= '<td.*?class=[\'"]text_l[\'"]><a.*?>(.*?)<\/a><\/td>.*?';
        $rule .= '<td.*?class=[\'"]eng[\'"]>(.*?)<\/td>.*?';
        $rule .= '<\/tr>';
        preg_match_all("/$rule/is", $content, $matches);
        $weekes = $this->CI->tools->getWeekArrayByDate($cdate);
        unset($matches[0]);
        $this->updateJczqScore($matches, $param['source'], $weekes, $mids);
    }
    
    /*
     * 【竞彩足球】更新赛果
     * @author:liuli
     * @date:2015-03-12
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
    			preg_match('/\((.*?)\)(.*)/', $datas[6][$in], $scores);
    			array_push($bdata['d_data'], $this->CI->lib_comm->score_filter($scores[1]));
    			$full_score = $this->CI->lib_comm->score_filter($scores[2]);
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