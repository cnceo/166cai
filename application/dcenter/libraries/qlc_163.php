<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【七乐彩】赛果抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-23
 */

class Qlc_163
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
                $url = 'http://caipiao.163.com/award/qlc/'.$issue.'.html';
                $this->get_datas($url, $param, $issue);
                if(++$count >= $data['num']) break;
            }
        }
        
        // $issue = '';        //格式：2015027
        // if(empty($issue))
        // {
        //     $url = 'http://caipiao.163.com/award/qlc/';
        // }else{
        //     $url = 'http://caipiao.163.com/award/qlc/'.$issue.'.html';
        // }

        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param, $issue)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        //开奖球号
        $rulesBall = '/<p.*?zj_area.*?>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?blue_ball.*?>(\d+)<\/span>[\s\S]*?<\/p>/i';

        preg_match($rulesBall, $content, $balls);

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

        //开奖销量，滚存
        $ruleDetail = '/<p.*?>.*?<span.*?time.*?>(\d+-\d+-\d+ \d+:\d+)<\/span>[\s\S]*?<span.*?sale.*?>(.*?)<\/span>[\s\S]*?<span.*?pool.*?>(.*?)<\/span>[\s\S]*?<\/p>/i';

        preg_match($ruleDetail, $content, $details);

        //奖池信息
        $rulePools1 = '/<div.*?search_zj_right.*?>.*?<table.*?bonus.*?>(.*?)<\/table>.*?<\/div>/is';
        preg_match($rulePools1,$content,$pools);

        $rulePools2 = '/<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<\/tr>.*?/is';
        preg_match($rulePools2,$pools[1],$pool);

        $dfields = array();
        $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($pool[1]);
        $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[2]);
        $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($pool[3]);
        $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[4]);
        $dfields['3dj']['zs'] = $this->CI->lib_comm->format_num($pool[5]);
        $dfields['3dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[6]);
        $dfields['4dj']['zs'] = $this->CI->lib_comm->format_num($pool[7]);
        $dfields['4dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[8]);
        $dfields['5dj']['zs'] = $this->CI->lib_comm->format_num($pool[9]);
        $dfields['5dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[10]);
        $dfields['6dj']['zs'] = $this->CI->lib_comm->format_num($pool[11]);
        $dfields['6dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[12]);
        $dfields['7dj']['zs'] = $this->CI->lib_comm->format_num($pool[13]);
        $dfields['7dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[14]);

        $awardDetail = array();
        $awardDetail['lid'] = 'qlc';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $details[1]?$details[1]:'';
        $awardDetail['sale'] = $this->CI->lib_comm->format_num($details[2]);
        $awardDetail['pool'] = $this->CI->lib_comm->format_num($details[3]);
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}