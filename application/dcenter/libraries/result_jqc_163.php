<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【进球彩】开奖详情抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-23
 */

class Result_jqc_163
{
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('lib_comm');
        $this->CI->load->library('tools');
        $this->CI->load->model('data_model');
    }

	//主函数
    public function capture($param, $data)
    {
    	$issues = $data['issue'] ? $data['issue'] : array();
    	if(empty($issues))
    	{
    		return ;
    	}
    	$num = $data['num'] ? $data['num'] : 1;
    	$i = 1;
    	foreach ($issues as $issue)
    	{
    		if($i > $num)
    		{
    			break;
    		}
    		$this->get_datas($issue, $param);
    		$i++;
    	}
    }

    private function get_datas($issue, $param)
    {
    	$url = 'http://caipiao.163.com/award/f4cjq/'.$issue.'.html';
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        //获取当前最新期号
        $ruleDetail = '<option.*?value="'.$issue.'".*?sale="(.*?)".*?matchball="(.*?)".*?bonus1=".*?,(.*?)".*?>(.*?)<\/option>';
        preg_match("/$ruleDetail/is",$content,$detail);

        if(!empty($detail))
        {
            $awardDetail = array();
            $awardDetail['mid'] = $this->CI->lib_comm->format_issue($detail[4], 0);
            $awardDetail['result'] = $detail[2]?str_replace(' ', ',', trim($detail[2])):'';
            $awardDetail['sale'] = $this->CI->lib_comm->format_num($detail[1]);
            $awardDetail['award'] = '0';
            $dfields = array();
            $dStr = array();
            if($detail[3])
            {
                $dStr = explode(',', $detail[3]);
            }
            $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($dStr[0]);
            $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($dStr[1]);
            $awardDetail['award_detail'] = json_encode($dfields);
            $awardDetail['status'] = $this->CI->lib_comm->getStatus($detail[2], array('lid' => 'tczq', 'issue' => $awardDetail['mid'], 'ctype' => 3));
			$awardDetail['rstatus'] = $awardDetail['sale'] > 0 ? 1 : 0;
			
            $s_data = array();
            array_push($s_data, "(?, ?, ?, ?, ?, ?, ?, {$param['source']}, now())");
            $this->updatejqcScore($s_data, $awardDetail);
        }

    }

    private function updatejqcScore($s_data, $d_data)
    {
        $bdata['s_data'] = $s_data;
        $bdata['d_data'] = $d_data;
        $this->CI->data_model->insertJqcResult($bdata);
    }

}