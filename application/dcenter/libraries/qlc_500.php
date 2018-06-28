<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【七乐彩】赛果抓取 -- 来源：500万
 * @author:liuli
 * @date:2015-03-23
 */

class Qlc_500
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
    public function capture($param,$data)
    {

        if(!empty($data['issues']))
        {
            $count = 0;
            foreach ($data['issues'] as $issue) 
            {
                $format_issue = substr($issue, 2);
                $url = 'http://kaijiang.500.com/shtml/qlc/'.$format_issue.'.shtml';
                $this->get_datas($url, $param);
                if(++$count >= $data['num']) break;
            }
        }
        
        // $issue = '';        //格式：15030
        // if(empty($issue))
        // {
        //     $url = 'http://kaijiang.500.com/shtml/qlc/';
        // }else{
        //     $url = 'http://kaijiang.500.com/shtml/qlc/'.$issue.'.shtml';
        // }

        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);

        //获取当前期号
        $ruleIssue = '<a.*?iSelect.*?>(\d+)<\/a>';

        preg_match("/$ruleIssue/is", $content, $issues);

        //期号信息
        if(empty($issues[1]))
        {
            return false;
        }

        $issue = '20'.$issues[1];

        //开奖球号
        $rulesBall = '<div.*?ball_box01.*?>.*?<ul.*?>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_blue.*?>(\d+)<\/li>.*?<\/ul>.*?<\/div>';

        preg_match("/$rulesBall/is", $content, $balls);

        $award_ball = '';
        if(!empty($balls))
        {
            unset($balls[0]);
            for ($i=1; $i <= 7; $i++) 
            { 
                $award_ball .= $balls[$i] . ','; 
            }
            $award_ball = preg_replace('/,$/is', '', $award_ball);
            if(!empty($balls[8]))
            {
                $award_ball .= "({$balls[8]})";
            }
        }

        //获取开奖时间
        $rulesTime = '<span.*?span_right.*?>.*?(\d+.*?\d+.*?\d+.*?) .*?<\/span>';
        preg_match("/$rulesTime/is", $content, $times);

        //开奖销量，滚存
        $rulesPool = '<td>.*?本期销量.*?<span.*?cfont1.*?>(.*?)元<\/span>.*?<span.*?cfont1.*?>(.*?)元<\/span>.*?<\/td>';
        preg_match("/$rulesPool/is", $content, $pools);

        //开奖详情
        $rulesDetail = '.*?<td.*?td_title02.*?>.*?<\/td>.*?<td.*?td_title02.*?>.*?<\/td>.*?<td.*?td_title02.*?>.*?<\/td>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>';

        preg_match("/$rulesDetail/is", $content, $details);

        $awardDetail = array();
        $awardDetail['lid'] = 'qlc';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $times[1]?$times[1]:'';
        $awardDetail['sale'] = $this->CI->lib_comm->format_num($pools[1]);
        $awardDetail['pool'] = $this->CI->lib_comm->format_num($pools[2]);

        $dfields = array();
        $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($details[1]);
        $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($details[2]);
        $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($details[3]);
        $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($details[4]);
        $dfields['3dj']['zs'] = $this->CI->lib_comm->format_num($details[5]);
        $dfields['3dj']['dzjj'] = $this->CI->lib_comm->format_num($details[6]);
        $dfields['4dj']['zs'] = $this->CI->lib_comm->format_num($details[7]);
        $dfields['4dj']['dzjj'] = $this->CI->lib_comm->format_num($details[8]);
        $dfields['5dj']['zs'] = $this->CI->lib_comm->format_num($details[9]);
        $dfields['5dj']['dzjj'] = $this->CI->lib_comm->format_num($details[10]);
        $dfields['6dj']['zs'] = $this->CI->lib_comm->format_num($details[11]);
        $dfields['6dj']['dzjj'] = $this->CI->lib_comm->format_num($details[12]);
        $dfields['7dj']['zs'] = $this->CI->lib_comm->format_num($details[13]);
        $dfields['7dj']['dzjj'] = $this->CI->lib_comm->format_num($details[14]);

        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
		$awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));
		
        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}