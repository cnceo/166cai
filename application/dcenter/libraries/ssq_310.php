<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【双色球】赛果抓取 -- 来源：彩客
 * @author:liuli
 * @date:2015-03-18
 */

class Ssq_310
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
                $url = 'http://www.310win.com/shuangseqiu/kaijiang_sz_10.html';
                $this->get_datas($url, $param, $issue);
                if(++$count >= $data['num']) break;
            }
        }

        // $issue = '';        
        // $url = 'http://www.310win.com/shuangseqiu/kaijiang_sz_10.html';

        // $this->get_datas($url, $param, $issue);

    }

    private function get_datas($url, $param, $issue)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);

        if(empty($issue))
        {
            //获取当前期号信息
            $ruleIssue  = '<select.*?dropIssueNum.*?>.*?<option.*?selected.*?value=.*?(\d+).*?>(.*?)<\/option>.*?<\/select>';
            preg_match_all("/$ruleIssue/is",$content,$issues);
            //期号信息
            if(empty($issues[1][0]) || empty($issues[2][0]))
            {
                return false;
            }

            $issueID = $issues[1][0];
            $issue = $issues[2][0];

        }else{
            //获取下拉框期号信息
            $ruleIssue  = '<select.*?dropIssueNum.*?>((?:.*?<option.*?value="\d+".*?>.*?<\/option>.*?)+)<\/select>';
            preg_match("/$ruleIssue/is",$content,$cMatch);

            if(!empty($cMatch[1]))
            {
                $regIssue = '<option.*?value=.*?(\d+).*?>(\d+)<\/option>';
                preg_match_all("/$regIssue/is",$cMatch[1],$matches);

                if($matches)
                {
                    $issueData = array();
                    foreach ($matches[2] as $key => $match) {
                        $issueData[$match] = $matches[1][$key];
                    }
                }
            }else{
                return false;
            }

            $issueID = $issueData[$issue];
            $issue = $issue;

        }   

        if(!empty($issueID))
        {
            //开奖详情请求接口
            $request_url = 'http://www.310win.com/Info/Result/Numeric.aspx?load=ajax&typeID=10&IssueID='.$issueID.'&randomT-_-='.time();

            $awardResponse = $this->CI->tools->request($request_url);

            if(!empty($awardResponse))
            {
                $awardResponse = json_decode($awardResponse,true);           
            }

            //解析开奖号码
            $award_ball = '';
            if(!empty($awardResponse['Results']))
            {
                $balls = explode(',', $awardResponse['Results']);

                foreach ($balls as $k => $ball) {
                    $award_ball .= $ball;
                    if($k == 5)
                    {
                        $award_ball .= '|';
                    }elseif($k < 6){
                        $award_ball .= ',';
                    }
                }
            }

            if(!empty($awardResponse['EspecialNum']))
            {
                $EspecialNum = str_pad($awardResponse['EspecialNum'],2,"0",STR_PAD_LEFT);              
                $award_ball .= "({$EspecialNum})";
            }

            $awardDetail = array();
            $awardDetail['lid'] = 'ssq';
            $awardDetail['issue'] = $this->CI->lib_comm->format_issue($awardResponse['IssueNum'], 0);
            $awardDetail['awardNum'] = $award_ball;
            $awardDetail['time'] = $awardResponse['AwardTime'];
            $awardDetail['sale'] = $this->CI->lib_comm->format_num($awardResponse['SaleMoney']);
            $awardDetail['pool'] = $this->CI->lib_comm->format_num($awardResponse['PoolMoney']);
            $dfields = array();
            $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['BasicStakes']);
            $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['BasicBonus']);
            $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['BasicStakes']);
            $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['BasicBonus']);
            $dfields['3dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['BasicStakes']);
            $dfields['3dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['BasicBonus']);
            $dfields['4dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['BasicStakes']);
            $dfields['4dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['BasicBonus']);
            $dfields['5dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['BasicStakes']);
            $dfields['5dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['BasicBonus']);
            $dfields['6dj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][5]['BasicStakes']);
            $dfields['6dj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][5]['BasicBonus']);
            $awardDetail['bonusDetail'] = json_encode($dfields);
            $awardDetail['source'] = $param['source'];
            $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
            $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));

            $this->CI->data_model->insertNumberAwards($awardDetail);
        }
    }
}