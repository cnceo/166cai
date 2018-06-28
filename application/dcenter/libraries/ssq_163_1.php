<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【双色球】赛果抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-16
 */

class Ssq_163_1
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
                $url = 'http://caipiao.163.com/award/ssq/'.$issue.'.html';
                $this->get_datas($url, $param, $issue);
                if(++$count >= $data['num']) break;
            }
        }
    }


    private function get_datas($url, $param, $issue)
    {
        //获取页面
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));
        $rule = '/<a\s+href="\/award\/ssq\/'.$issue.'.html"\s+time="(.*?)".*?sale="(.*?)".*?pool="(.*?)".*?matchBall="(.*?)".*?bonus="(.*?)".*?>'.$issue.'<\/a>/i';
        preg_match($rule, $content, $matchs);
        unset($matchs[0]);
        if(empty($matchs))
        {
        	return ;
        }
        //幸运号码
        $ballOrange = '<span.*?ssqExtraNum.*?>(.*?)<i.*?\/span>';
        preg_match("/$ballOrange/is", $content, $orangeBall);
        unset($orangeBall[0]);

        $allBalls = explode(' ', $matchs[4]);
        $blueBall = array_pop($allBalls);
        //开奖号码
        $award_ball = implode(',', $allBalls).'|'.$blueBall;

        if(!empty($orangeBall[1]))
        {
            $award_ball .= "({$orangeBall[1]})";
        }

        $awardDetail = array();
        $awardDetail['lid'] = 'ssq';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $matchs[1];
        $awardDetail['sale'] = $this->CI->lib_comm->format_num($matchs[2]);
        $awardDetail['pool'] = $this->CI->lib_comm->format_num($matchs[3]);
        $dfields = array();
        $pool = array();
        $bounsDetail = explode('|', $matchs[5]);
        foreach ($bounsDetail as $key => $value)
        {
        	$pool[$key] = explode(',', $value);
        }
        $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($pool[0][1]);
        $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[0][2]);
        $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($pool[1][1]);
        $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[1][2]);
        $dfields['3dj']['zs'] = $this->CI->lib_comm->format_num($pool[2][1]);
        $dfields['3dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[2][2]);
        $dfields['4dj']['zs'] = $this->CI->lib_comm->format_num($pool[3][1]);
        $dfields['4dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[3][2]);
        $dfields['5dj']['zs'] = $this->CI->lib_comm->format_num($pool[4][1]);
        $dfields['5dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[4][2]);
        $dfields['6dj']['zs'] = $this->CI->lib_comm->format_num($pool[5][1]);
        $dfields['6dj']['dzjj'] = $this->CI->lib_comm->format_num($pool[5][2]);
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields);
        $this->CI->data_model->insertNumberAwards($awardDetail, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));
    }

}