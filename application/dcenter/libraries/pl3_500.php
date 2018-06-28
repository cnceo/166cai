<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【排列三】赛果抓取 -- 来源：500万
 * @author:shigaoxing
 * @date:2015-03-23
 */

class Pl3_500
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
                $url = 'http://kaijiang.500.com/shtml/pls/'.$issue.'.shtml';
                $this->get_datas($url, $param, $issue);
                if(++$count >= $data['num']) break;
            }
        }
    }

    private function get_datas($url, $param, $issue)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);

        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?class.*?>.*?<span.*?>.*?<\/span>.*?<span.*?class.*?>开奖日期：(.*?)\s+.*?<\/span>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td.*?>.*?<table.*?>.*?<ul>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<\/table>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr>.*?<td>.*?<span.*?class.*?>(.*?)元<\/span>.*?<\/td>.*?<\/tr>.*?<\/table>';
        preg_match("/$rule/is", $content, $balls);
        if(!isset($balls[1]))
        {
        	return ;
        }
        
        $data['lid'] = 'pl3';
        $data['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $data['awardNum'] = $balls[2] . ',' . $balls[3] . ',' . $balls[4];
        //$data['time'] = $balls[1];
        $data['sale'] = '0';
        $data['pool'] = '0';
        $data['status'] = $this->CI->lib_comm->getStatus($data['awardNum']);
        $data['source'] = $param['source'];
        unset($balls);
        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?>.*?开奖详情.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?<\/tr>';
        $rule .= '(.*?)';
        $rule .= '<\/table>';
        preg_match("/$rule/is", $content, $detail);
        unset($detail[0]);
        preg_match_all('/<tr.*?align=[\'"]center[\'"].*?>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>/is', $detail[1], $detail);
        unset($detail[0]);
    	$kname = array('直选' => 'zx', '排列三直选' => 'zx', '排列3直选' => 'zx', '组三' => 'z3', '排列三组三' => 'z3', '排列3组三' => 'z3', '组六' => 'z6', '排列三组六' => 'z6', '排列3组六' => 'z6');
        foreach ($kname as $key)
        {
        	$bonusDetail[$key]['zs'] = '0';
        }
        foreach ($detail[1] as $in => $level)
        {
        	$keyName = $kname[trim($level)];
        	if($keyName)
        	{
        		$bonusDetail[$keyName]['zs'] = $this->CI->lib_comm->format_num($detail[2][$in]);
        	}
        }
        //奖金详情
        $bonusDetail['zx']['dzjj'] = '1040';
        $bonusDetail['z3']['dzjj'] = '346';
        $bonusDetail['z6']['dzjj'] = '173';

        $data['bonusDetail'] = json_encode($bonusDetail);
        $data['rstatus'] = $this->CI->lib_comm->getRStatus($bonusDetail, array('issue' => $data['issue'], 'ctype' => $data['lid']));
    	$res = $this->CI->data_model->insertNumberAwards($data);
        if(!$res)
        {
        	log_message('error', '写入数据库失败');
        }
        
    }
}