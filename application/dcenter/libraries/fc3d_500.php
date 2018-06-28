<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【福彩3D】赛果抓取 -- 来源：500万
 * @author:liuli
 * @date:2015-03-23
 */

class Fc3d_500
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
                $url = 'http://kaijiang.500.com/shtml/sd/'.$issue.'.shtml';
                $this->get_datas($url, $param);
                if(++$count >= $data['num']) break;
            }
        }
        
        // $issue = '';        //格式：2015031
        // if(empty($issue))
        // {
        //     $url = 'http://kaijiang.500.com/shtml/sd/';
        // }else{
        //     $url = 'http://kaijiang.500.com/shtml/sd/'.$issue.'.shtml';
        // }

        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);
        //获取期号
        $ruleIssue  = '<a.*?iSelect.*?>(\d+)<\/a>';
        preg_match("/$ruleIssue/is", $content, $issues);

        if(empty($issues[1]))
        {
            return false;
        }

        $issue = $issues[1];

        $rulesBall = '<div.*?ball_box01.*?>.*?<ul.*?>.*?<li.*?ball_orange.*?>(\d+)<\/li>.*?<li.*?ball_orange.*?>(\d+)<\/li>.*?<li.*?ball_orange.*?>(\d+)<\/li>.*?<\/ul>.*?<\/div>';

        preg_match("/$rulesBall/is", $content, $balls);

        $award_ball = '';
        if(!empty($balls))
        {
            unset($balls[0]);
            for ($i=1; $i <= 3; $i++) 
            { 
                $award_ball .= $balls[$i];
                if($i < 3){
                    $award_ball .= ',';
                }else{

                }
            }
        }

        //获取开奖时间
        $rulesTime = '<span.*?span_right.*?>.*?(\d+.*?\d+.*?\d+.*?) .*?<\/span>';
        preg_match("/$rulesTime/is", $content, $times);

        //获取销量，滚存
        $rulesPool = '<td>.*?本期销量.*?<span.*?cfont1.*?>(.*?)元<\/span>.*?<\/td>';
        preg_match("/$rulesPool/is", $content, $pools);

        //开奖详情
        $rulesDetail .= '<tr.*?开奖详情.*?\/tr>.*?';
        $rulesDetail .= '<tr.*?\/tr>.*?';
        $rulesDetail .= '<tr.*?>.*?<td.*?\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?\/tr>.*?';
        $rulesDetail .= '<tr.*?>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>.*?\/tr>.*?';
        $rulesDetail .= '<\/table>';
        preg_match("/$rulesDetail/is", $content, $details);

		$kname = array('单选' => 'zx', '组三' => 'z3', '组六' => 'z6');
		$dfields = array();
        foreach ($kname as $key)
        {
        	$dfields[$key]['zs'] = '0';
        }

        if(!empty($details))
        {
            $dfields['zx']['zs'] = $this->CI->lib_comm->format_num($details[1]);
            $dfields[$kname[trim($details[3])]]['zs'] = $this->CI->lib_comm->format_num($details[4]);
        }
        //奖金详情
        $dfields['zx']['dzjj'] = '1040';
        $dfields['z3']['dzjj'] = '346';
        $dfields['z6']['dzjj'] = '173';
        
        $awardDetail = array();
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['lid'] = 'fc3d';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $times[1]?$times[1]:'';
        $awardDetail['sale'] = '0'; //$this->CI->lib_comm->format_num($pools[1]);
        $awardDetail['pool'] = '0'; //$this->CI->lib_comm->format_num($pools[2]);

        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}