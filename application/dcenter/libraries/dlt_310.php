<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【大乐透】赛果抓取 -- 来源：彩客
 * @author:liuli
 * @date:2015-03-17
 */

class Dlt_310
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
            foreach ($data['issues'] as $issues) 
            {
                //$issues = '';    //15031
                $url = 'http://www.310win.com/daletou/kaijiang_sz_20.html';
                $content = $this->CI->tools->get_content($url, __CLASS__);
                $reg = '<select.*?id=[\'"]dropIssueNum[\'"].*?>(.*?)<\/select>';
                preg_match("/$reg/is", $content, $selects);
                unset($selects[0]);
                $result = preg_match_all('/<option(?:.*?selected=[\'"](.*?)[\'"])?.*?value=[\'"](.*?)[\'"].*?>(.*?)<\/option>/is', $selects[1], $matches);

                if($result)
                {
                    unset($matches[0]);
                    foreach ($matches[1] as $in => $issue)
                    {
                        if(!empty($issues))
                        {
                            if($matches[3][$in] == $issues)
                            {
                               $this->get_datas($matches[2][$in],$issues,$param); 
                               break;
                            }
                        }
                        elseif(!empty($issue))
                        {
                            $this->get_datas($matches[2][$in],$matches[3][$in],$param);
                            break;
                        }
                    }
                }
                if(++$count >= $data['num']) break;
            }
        }

        
    }

    private function get_datas($issueID,$issue,$param)
    {
        //开奖详情请求接口
    	$request_url = "http://www.310win.com/Info/Result/Numeric.aspx?load=ajax&typeID=20&IssueID={$issueID}&randomT-_-=" . time();

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
                if($k == 4)
                {
                    $award_ball .= '|';
                }elseif($k < 6){
                    $award_ball .= ',';
                }
            }
        }

        $awardDetail = array();
        $awardDetail['lid'] = 'dlt';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $award_ball;
        $awardDetail['time'] = $awardResponse['AwardTime']?$awardResponse['AwardTime']:'';
        $awardDetail['sale'] = $awardResponse['SaleMoney']?preg_replace('/[^\d\.]/is', '', $awardResponse['SaleMoney']):'';
        $awardDetail['pool'] = $awardResponse['PoolMoney']?preg_replace('/[^\d\.]/is', '', $awardResponse['PoolMoney']):'';
        $dfields = array();
        $dfields['1dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['BasicStakes']);
        $dfields['1dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['BasicBonus']);
        $dfields['1dj']['zj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['PursueStakes']);
        $dfields['1dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][0]['PursueBonus']);
        $dfields['2dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['BasicStakes']);
        $dfields['2dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['BasicBonus']);
        $dfields['2dj']['zj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['PursueStakes']);
        $dfields['2dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][1]['PursueBonus']);
        $dfields['3dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['BasicStakes']);
        $dfields['3dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['BasicBonus']);
        $dfields['3dj']['zj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['PursueStakes']);
        $dfields['3dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][2]['PursueBonus']);
        $dfields['4dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['BasicStakes']);
        $dfields['4dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['BasicBonus']);
        $dfields['4dj']['zj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['PursueStakes']);
        $dfields['4dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][3]['PursueBonus']);
        $dfields['5dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['BasicStakes']);
        $dfields['5dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['BasicBonus']);
        $dfields['5dj']['zj']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['PursueStakes']);
        $dfields['5dj']['zj']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][4]['PursueBonus']);
        $dfields['6dj']['jb']['zs'] = $this->CI->lib_comm->format_num($awardResponse['Table'][5]['BasicStakes']);
        $dfields['6dj']['jb']['dzjj'] = $this->CI->lib_comm->format_num($awardResponse['Table'][5]['BasicBonus']);
        //$dfields['6dj']['zj']['zs'] = preg_replace('/[^\d\.]/is', '', $awardResponse['Table'][5]['PursueStakes']);
        //$dfields['6dj']['zj']['dzjj'] = $awardResponse['Table'][5]['PursueStakes']?preg_replace('/[^\d\.]/is', '', $awardResponse['Table'][5]['PursueBonus']):'0';
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($award_ball);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => 'dlt'));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}