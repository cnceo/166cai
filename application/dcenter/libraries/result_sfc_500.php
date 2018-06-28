<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【胜负任选九】赛果抓取 -- 来源：500万
 * @author:shigaoxing
 * @date:2015-03-23
 */

class Result_sfc_500
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
    	$issues = $data['issue'] ? $data['issue'] : array();
    	if(empty($issues))
    	{
    		return ;
    	}
    	$num = $data['num'] ? $data['num'] : 1;
    	$i = 1;
    	foreach ($issues as $issue)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		 
    		$url = "http://kaijiang.500.com/shtml/sfc/{$issue}.shtml";
    		$content = $this->CI->tools->get_content($url, __CLASS__);
    		$this->get_datas($param, $content);
    		$i++;
    	}
    }

    private function get_datas($param, $content)
    {
        //获取期号
        $rule  = '<a.*?class=[\'"]iSelect[\'"].*?id=[\'"]change_date[\'"].*?>(.*?)<\/a>';
        preg_match("/$rule/is", $content, $issues);
        if(!isset($issues[1]))
        {
            return ;
        }
		
        $issue = substr(date('Y'), 0, 2) . $issues[1];
        unset($issues);
        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?class.*?>.*?<span.*?>.*?<\/span>.*?<span.*?class.*?>开奖日期：(.*?)\s+.*?<\/span>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<td><span.*?>(.*?)<\/span><\/td>.*?<\/tr>.*?';
        $rule .= '<tr>.*?<td.*?colspan=[\'"]14[\'"].*?>.*?<span.*?class.*?>(.*?)元<\/span>.*?<span.*?class.*?>(.*?)元<\/span>.*?<span.*?>(.*?)元<\/span>.*?<\/td>.*?<\/tr>.*?<\/table>';
        preg_match("/$rule/is", $content, $balls);
        if(!isset($balls[1]))
        {
        	return ;
        }
        
        $data['lid'] = 'sfc';
        $data['mid'] = $this->CI->lib_comm->format_issue($issue, 2);
        for($i = 2; $i < 16; $i++)
        {
        	$balls[$i] = trim($balls[$i]);
        	if(!in_array($balls[$i], array('3','1','0','*')))
        	{
        		$balls[$i] = '*';
        	}
        }
        $data['result'] = $balls[2] . ',' . $balls[3] . ',' . $balls[4] .',' . $balls[5] . ',' . $balls[6]. ',' . $balls[7] . ',' . $balls[8] .',' . $balls[9] . ',' . $balls[10]. ',' . $balls[11] . ',' . $balls[12] .',' . $balls[13] . ',' . $balls[14] . ',' . $balls[15];
        
        $data['sfc_sale'] = $this->CI->lib_comm->format_num($balls[16]);
        $data['rj_sale'] = $this->CI->lib_comm->format_num($balls[17]);
        $data['award'] = $this->CI->lib_comm->format_num($balls[18]);
        $data['status'] = $this->CI->lib_comm->getStatus($data['result'], array('lid' => 'tczq', 'issue' => $data['mid'], 'ctype' => 1));
        $data['source'] = $param['source'];
        unset($balls[0]);
        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?>.*?开奖详情.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>.*?';
        $rule .= '<tr>.*?<\/tr>.*?<\/table>';
        preg_match("/$rule/is", $content, $detail);
        if(!isset($detail[1]))
        {
        	return ;
        }
        unset($detail[0]);
        $bonusDetail['1dj']['zs'] = $this->CI->lib_comm->format_num($detail[2]);
        $bonusDetail['1dj']['dzjj'] = $this->CI->lib_comm->format_num($detail[3]);
        $bonusDetail['2dj']['zs'] = $this->CI->lib_comm->format_num($detail[5]);
        $bonusDetail['2dj']['dzjj'] = $this->CI->lib_comm->format_num($detail[6]);
        $bonusDetail['rj']['zs'] = $this->CI->lib_comm->format_num($detail[8]);
        $bonusDetail['rj']['dzjj'] = $this->CI->lib_comm->format_num($detail[9]);
        $data['award_detail'] = json_encode($bonusDetail);
        // $data['rstatus'] = $data['sfc_sale'] > 0 ? 1 : 0;
        $data['rstatus'] = $bonusDetail['rj']['dzjj'] > 0 ? 1 : 0;
        //胜负彩入库操作
        $res = $this->CI->data_model->insertSfcAwards($data);
        if(!$res)
        {
        	log_message('error', '写入数据库失败');
        }
        unset($data['lid'], $data['award'], $data['award_detail'], $res);
    }
}