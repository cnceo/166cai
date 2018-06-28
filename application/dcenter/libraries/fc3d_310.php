<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【福彩3D】赛果抓取 -- 来源：彩客
 * @author:liuli
 * @date:2015-03-23
 */

class Fc3d_310
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
                //$issues = '';    //2015073
                $url = 'http://www.310win.com/3d/kaijiang_sz_44.html';
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
        $request_url = "http://www.310win.com/Info/Result/Numeric.aspx?load=ajax&typeID=25&IssueID={$issueID}&randomT-_-=" . time();

        $awardResponse = $this->CI->tools->request($request_url);

        if(!empty($awardResponse))
        {
            $awardResponse = json_decode($awardResponse,true);           
        }

        $awardDetail = array();
        $awardDetail['lid'] = 'fc3d';
        $awardDetail['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $awardDetail['awardNum'] = $awardResponse['Results']?$awardResponse['Results']:'';
        $awardDetail['time'] = $awardResponse['AwardTime']?$awardResponse['AwardTime']:'';
        $awardDetail['sale'] = '0'; //$this->CI->lib_comm->format_num($awardResponse['SaleMoney']);
        $awardDetail['pool'] = '0'; //$this->CI->lib_comm->format_num($awardResponse['PoolMoney']);
        $dfields = array();
    	$kname = array('单选' => 'zx', '组三' => 'z3', '组六' => 'z6');
        foreach ($kname as $key)
        {
        	$dfields[$key]['zs'] = '0';
        }
    	foreach ($awardResponse['Table'] as $level)
        {
        	$dfields[$kname[$level['Grade']]]['zs'] = $this->CI->lib_comm->format_num($level['BasicStakes']);
        }
        //奖金详情
        $dfields['zx']['dzjj'] = '1040';
        $dfields['z3']['dzjj'] = '346';
        $dfields['z6']['dzjj'] = '173';
        $awardDetail['bonusDetail'] = json_encode($dfields);
        $awardDetail['source'] = $param['source'];
        $awardDetail['status'] = $this->CI->lib_comm->getStatus($awardResponse['Results']);
        $awardDetail['rstatus'] = $this->CI->lib_comm->getRStatus($dfields, array('issue' => $awardDetail['issue'], 'ctype' => $awardDetail['lid']));

        $this->CI->data_model->insertNumberAwards($awardDetail);
    }

}