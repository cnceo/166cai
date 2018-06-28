<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【大乐透】赛果抓取 -- 来源：500万
 * @author:liuli
 * @date:2015-03-17
 */

class Dlt_500
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
                $url = 'http://kaijiang.500.com/shtml/dlt/'.$issue.'.shtml';
                $this->get_datas($url, $param);
                if(++$count >= $data['num']) break;
            }
        }

        // $issue = '';        //格式：15031
        // if(empty($issue))
        // {
        //     $url = 'http://kaijiang.500.com/shtml/dlt/';
        // }else{
        //     $url = 'http://kaijiang.500.com/shtml/dlt/'.$issue.'.shtml';
        // }

        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);
        //获取期号
        $ruleIssue_bak  = '<a.*?iSelect.*?>(\d+)<\/a>';
        $ruleIssue = '<font.*?cfont2.*?>.*?<strong.*?>(\d+)<\/strong>.*?<\/font>';
        preg_match("/$ruleIssue/is", $content, $issues);

        if(empty($issues[1]))
        {
            return false;
        }

        $issue = '20'.$issues[1];

        $rulesBall = '<div.*?ball_box01.*?>.*?<ul.*?>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_red.*?>(\d+)<\/li>.*?<li.*?ball_blue.*?>(\d+)<\/li>.*?<li.*?ball_blue.*?>(\d+)<\/li>.*?<\/ul>.*?<\/div>';

        preg_match("/$rulesBall/is", $content, $balls);

        $award_ball = '';
        if(!empty($balls))
        {
            unset($balls[0]);
            for ($i=1; $i <= 7; $i++) 
            { 
                $award_ball .= $balls[$i];
                if($i == 5)
                {
                    $award_ball .= '|';
                }elseif($i < 7){
                    $award_ball .= ',';
                }else{

                }
            }
        }

        //获取开奖时间
        $rulesTime = '<span.*?span_right.*?>.*?(\d+.*?\d+.*?\d+.*?) .*?<\/span>';
        preg_match("/$rulesTime/is", $content, $times);

        //获取销量，滚存
        $rulesPool = '<td>.*?本期销量.*?<span.*?cfont1.*?>(.*?)元<\/span>.*?<span.*?cfont1.*?>(.*?)元<\/span>.*?<\/td>';
        preg_match("/$rulesPool/is", $content, $pools);

        //开奖详情
        $rulesDetail = '.*?<td.*?td_title02.*?>.*?<\/td>.*?<td.*?td_title02.*?>.*?<\/td>.*?<td.*?td_title02.*?>.*?<\/td>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?center.*?>.*?<td.*?>..*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?';

        preg_match("/$rulesDetail/is", $content, $details);

        $dfields = array();
        $dfields['1dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[1]);
        $dfields['1dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[2]);
        $dfields['1dj']['zj']['zs'] = $this->CI->lib_comm->format_num($details[3]);
        $dfields['1dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($details[4]);
        $dfields['2dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[5]);
        $dfields['2dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[6]);
        $dfields['2dj']['zj']['zs'] = $this->CI->lib_comm->format_num($details[7]);
        $dfields['2dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($details[8]);
        $dfields['3dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[9]);
        $dfields['3dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[10]);
        $dfields['3dj']['zj']['zs'] = $this->CI->lib_comm->format_num($details[11]);
        $dfields['3dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($details[12]);
        $dfields['4dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[13]);
        $dfields['4dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[14]);
        $dfields['4dj']['zj']['zs'] = $this->CI->lib_comm->format_num($details[15]);
        $dfields['4dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($details[16]);
        $dfields['5dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[17]);
        $dfields['5dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[18]);
        $dfields['5dj']['zj']['zs'] = $this->CI->lib_comm->format_num($details[19]);
        $dfields['5dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($details[20]);
        $dfields['6dj']['jb']['zs'] = $this->CI->lib_comm->format_num($details[21]);
        $dfields['6dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($details[22]);

        $awardDetail = array();
        $awardDetail['lid'] = 'dlt';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 2);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $times[1]?$times[1]:'';
        $awardDetail['sale'] = $this->CI->lib_comm->format_num($pools[1]);
        $awardDetail['pool'] = $this->CI->lib_comm->format_num($pools[2]);
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => 'dlt'));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}