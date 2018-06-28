<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【胜负彩】开奖详情抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-23
 */

class Result_sfc_163
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
    	$url = 'http://caipiao.163.com/award/sfc/'.$issue.'.html';
    	$rj_url = 'http://caipiao.163.com/award/rx9/'.$issue.'.html';
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        $rj_content = $this->CI->tools->get_content($rj_url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        //获取当前最新期号
        $rj_ruleDetail = '<option.*?value="'.$issue.'".*?sale="(.*?)".*?matchball="(.*?)".*?bonus1=".*?,(.*?)".*?>(.*?)<\/option>';
        preg_match("/$rj_ruleDetail/is",$rj_content,$rj_detail);

        if(!empty($rj_detail))
        {
            $rj = explode(',', $rj_detail[3]);
        }

        //获取当前最新期号
        $ruleDetail = '<select.*?change_date.*?>.*?<option.*?value="'.$issue.'".*?sale.*?(\d+).*?matchball="(.*?)".*?bonus1.*?一等奖,(.*?)".*?bonus2="二等奖,(.*?)".*?>(\d+)<\/option>';
        preg_match("/$ruleDetail/is",$content,$detail);

        if(!empty($detail))
        {
            $awardDetail = array();
            $awardDetail['lid'] = 'sfc';
            $awardDetail['mid'] = $this->CI->lib_comm->format_issue($detail[5], 0);
            $awardDetail['result'] = $detail[2]?str_replace(' ', ',', trim($detail[2])):'';
            $awardDetail['sfc_sale'] = $this->CI->lib_comm->format_num($detail[1]);
            $awardDetail['rj_sale'] = $this->CI->lib_comm->format_num($rj_detail[1]);
            $awardDetail['award'] = '0';
            
            $ydj = explode(',', $detail[3]);
            $edj = explode(',', $detail[4]);
            $dfields = array();
            $dfields['1dj']['zs'] = $this->CI->lib_comm->format_num($ydj[0]);
            $dfields['1dj']['dzjj'] = $this->CI->lib_comm->format_num($ydj[1]);
            $dfields['2dj']['zs'] = $this->CI->lib_comm->format_num($edj[0]);
            $dfields['2dj']['dzjj'] = $this->CI->lib_comm->format_num($edj[1]);
            $dfields['rj']['zs'] = $this->CI->lib_comm->format_num($rj[0]);
            $dfields['rj']['dzjj'] = $this->CI->lib_comm->format_num($rj[1]);

            $awardDetail['award_detail'] = json_encode($dfields);
            $awardDetail['source'] = $param['source'];
            $awardDetail['status'] = $this->CI->lib_comm->getStatus($awardDetail['result'], array('lid' => 'tczq', 'issue' => $awardDetail['mid'], 'ctype' => 1));
			$awardDetail['rstatus'] = $awardDetail['sfc_sale'] > 0 ? 1 : 0;
			
            $this->CI->data_model->insertSfcAwards($awardDetail);
        }

        
    }

}