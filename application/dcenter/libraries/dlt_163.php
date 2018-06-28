<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【大乐透】赛果抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-17
 */

class Dlt_163
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
                $url = 'http://caipiao.163.com/award/dlt/'.$issue.'.html';
                $this->get_datas($url, $param);
                if(++$count >= $data['num']) break;
            }
        }

        // $issue = '';    //15031
        // if(empty($issue))
        // {
        //     $url = 'http://caipiao.163.com/award/dlt/';
        // }else{
        //     $url = 'http://caipiao.163.com/award/dlt/'.$issue.'.html';
        // }

        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        //获取当前最新期号
        $ruleIssue = '/<a.*?iSelect.*?change_date.*?>(\d+)<\/a>/i';
        preg_match_all($ruleIssue,$content,$issues);

        //期号信息
        if(empty($issues[1][0]))
        {
            return false;
        }

        $issue = $issues[1][0];

        //开奖球号
        $rulesBall = '/<p.*?zj_area.*?>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?blue_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?blue_ball.*?>(\d+)<\/span>[\s\S]*?<\/p>/i';

        preg_match($rulesBall, $content, $balls);

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

        //开奖销量，滚存
        $ruleDetail = '/<p.*?>.*?<span.*?time.*?>(\d+-\d+-\d+ \d+:\d+)<\/span>[\s\S]*?<span.*?sale.*?>(.*?)<\/span>[\s\S]*?<span.*?pool.*?>(.*?)<\/span>[\s\S]*?<\/p>/i';

        preg_match($ruleDetail, $content, $details);

        //奖池信息
        $rulePools1 = '/<div.*?search_zj_right.*?>.*?<table.*?sport_table.*?>(.*?)<\/table>.*?<\/div>/is';
        preg_match($rulePools1,$content,$pools);

        $rulePools2 = '/<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>/is';
        preg_match($rulePools2,$pools[1],$pool);

        $awardDetail = array();
        $awardDetail['lid'] = 'dlt';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $details[1]?$details[1]:'';
        $awardDetail['sale'] = $this->CI->lib_comm->format_num($details[2]);
        $awardDetail['pool'] = $this->CI->lib_comm->format_num($details[3]);
        $dfields = array();
        $dfields['1dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[1]);
        $dfields['1dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[2]);
        $dfields['1dj']['zj']['zs'] = $this->CI->lib_comm->format_num($pool[3]);
        $dfields['1dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($pool[4]);
        $dfields['2dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[5]);
        $dfields['2dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[6]);
        $dfields['2dj']['zj']['zs'] = $this->CI->lib_comm->format_num($pool[7]);
        $dfields['2dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($pool[8]);
        $dfields['3dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[9]);
        $dfields['3dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[10]);
        $dfields['3dj']['zj']['zs'] = $this->CI->lib_comm->format_num($pool[11]);
        $dfields['3dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($pool[12]);
        $dfields['4dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[13]);
        $dfields['4dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[14]);
        $dfields['4dj']['zj']['zs'] = $this->CI->lib_comm->format_num($pool[15]);
        $dfields['4dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($pool[16]);
        $dfields['5dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[17]);
        $dfields['5dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[18]);
        $dfields['5dj']['zj']['zs'] = $this->CI->lib_comm->format_num($pool[19]);
        $dfields['5dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($pool[20]);
        $dfields['6dj']['jb']['zs'] = $this->CI->lib_comm->format_num($pool[21]);
        $dfields['6dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($pool[22]);
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => 'dlt'));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}