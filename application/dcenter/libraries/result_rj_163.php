<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【胜负彩】开奖详情抓取 -- 来源：网易
 * @author:liuli
 * @date:2015-03-23
 */

class Result_rj_163
{
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
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
    		$url = 'http://caipiao.163.com/award/rx9/'.$issue.'.html';
    		$this->get_datas($url, $param);
    		$i++;
    	}
    }

    private function get_datas($url, $param)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__, 0, array('ENCODING' => 'gzip'));

        //获取当前最新期号
        $ruleDetail = '<option.*?value.*?sale="(.*?)".*?matchball="(.*?)".*?bonus1=".*?,(.*?)".*?>(.*?)<\/option>';
        preg_match("/$ruleDetail/is",$content,$detail);

        if(!empty($detail))
        {
            $awardDetail = array();
            $awardDetail['lid'] = 'rj';
            $awardDetail['mid'] = substr(date('Y'), 0, 2) . $detail[4];
            $awardDetail['result'] = $detail[2]?str_replace(' ', ',', trim($detail[2])):'';
            $awardDetail['sale'] = isset($detail[1])?$detail[1]:'';
            $awardDetail['award'] = '';

            $ydj = explode(',', $detail[3]);
            $dfields = array();
            $dfields['1dj']['zs'] = $ydj[0];
            $dfields['1dj']['dzjj'] = $ydj[1];

            $awardDetail['award_detail'] = json_encode($dfields);
            $awardDetail['source'] = $param['source'];
            $awardDetail['status'] = $detail[2]?'1':'0';

            $this->CI->data_model->insertSfcAwards($awardDetail);
        }
    }

}