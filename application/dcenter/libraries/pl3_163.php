<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【排列3】赛果抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-23
 */

class Pl3_163
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
                $url = 'http://caipiao.163.com/award/pl3/'.$issue.'.html';
                $this->get_datas($url, $param);
                if(++$count >= $data['num']) break;
            }
        }

        // $issue = '';        //格式：15027
        // if(empty($issue))
        // {
        //     $url = 'http://caipiao.163.com/award/pl3/';
        // }else{
        //     $url = 'http://caipiao.163.com/award/pl3/'.$issue.'.html';
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
        /* $rulesBall = '/<p.*?zj_area.*?>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<span.*?red_ball.*?>(\d+)<\/span>[\s\S]*?<\/p>/i';
            */
        $rulesBall = '/<p.*?zj_area.*?>[\s\S]*?<span.*?red_ball.*?>(.*?)<\/span>[\s\S]*?<span.*?red_ball.*?>(.*?)<\/span>[\s\S]*?<span.*?red_ball.*?>(.*?)<\/span>[\s\S]*?<\/p>/i';
        preg_match($rulesBall, $content, $balls);

        $award_ball = '';
        if(!empty($balls))
        {
            unset($balls[0]);
            for ($i=1; $i <= 3; $i++) 
            { 
                if(trim($balls[$i])=='')
                {
                    return;
                }else{
                    $award_ball .= $balls[$i];
                    if($i < 3){
                        $award_ball .= ',';
                    }else{

                    }                    
                }

            }
        }

        //开奖销量，滚存
        $ruleDetail = '/<p.*?>.*?<span.*?time.*?>(\d+-\d+-\d+ \d+:\d+)<\/span>[\s\S]*?<span.*?sale.*?>(.*?)<\/span>[\s\S]*?<\/p>/i';

        preg_match($ruleDetail, $content, $details);

        //奖池信息
        $rulePools1 = '/<div.*?search_zj_right.*?>.*?<table.*?table2.*?>(.*?)<\/table>.*?<\/div>/is';
        preg_match($rulePools1,$content,$pools);

        $rulePools2 = '/<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<span.*?award1.*?>(.*?)<\/span>.*?<\/td>.*?<td.*?>.*?<span.*?awardvalue1.*?>(.*?)<\/span>.*?<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<span.*?award2.*?>(.*?)<\/span>.*?<\/td>.*?<td.*?>.*?<span.*?awardvalue2.*?>(.*?)<\/span>.*?<\/td>.*?<\/tr>.*?<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<span.*?award3.*?>(.*?)<\/span>.*?<\/td>.*?<td.*?>.*?<span.*?awardvalue3.*?>(.*?)<\/span>.*?<\/td>.*?<\/tr>/is';
        preg_match($rulePools2,$pools[1],$pool);

        $dfields = array();
        $dfields['zx']['zs'] = $this->CI->lib_comm->format_num($pool[1]);
        $dfields['zx']['dzjj'] = '1040';
        $dfields['z3']['zs'] = $this->CI->lib_comm->format_num($pool[3]);
        $dfields['z3']['dzjj'] = '346';
        $dfields['z6']['zs'] = $this->CI->lib_comm->format_num($pool[5]);
        $dfields['z6']['dzjj'] = '173';
        
        $awardDetail = array();
        $awardDetail['lid'] = 'pl3';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $details[1]?$details[1]:'';
        $awardDetail['sale'] = '0';
        $awardDetail['pool'] = '0';
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}