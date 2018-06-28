<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 【排列五】赛果抓取 -- 来源：500万
 * @author:shigaoxing
 * @date:2015-03-23
 */

class Pl5_500
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
                $url = 'http://kaijiang.500.com/shtml/plw/'.$issue.'.shtml';
                $this->get_datas($url, $param, $issue);
                if(++$count >= $data['num']) break;
            }
        }

        // $url = 'http://kaijiang.500.com/static/info/kaijiang/shtml/plw/';
        // $this->get_datas($url, $param);
    }

    private function get_datas($url, $param, $issue)
    {
        $content = $this->CI->tools->get_content($url, __CLASS__);

        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?class.*?>.*?<span.*?>.*?<\/span>.*?<span.*?class.*?>开奖日期：(.*?)\s+.*?<\/span>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td.*?>.*?<table.*?>.*?<ul>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<li.*?class.*?>(\d+)<\/li>.*?<\/table>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr>.*?<td>.*?<span.*?class.*?>(.*?)元<\/span>.*?<\/td>.*?<\/tr>.*?<\/table>';
        preg_match("/$rule/is", $content, $balls);
        if(!isset($balls[1]))
        {
        	return ;
        }
        
        $data['lid'] = 'pl5';
        $data['issue'] = $this->CI->lib_comm->format_issue($issue, 0);
        $data['awardNum'] = $balls[2] . ',' . $balls[3] . ',' . $balls[4] .',' . $balls[5] . ',' . $balls[6];
        //$data['time'] = $balls[1];
        $data['sale'] = '0';
        $data['pool'] = '0';
        $data['status'] = $this->CI->lib_comm->getStatus($data['awardNum']);
        $data['source'] = $param['source'];
        unset($balls);
        $rule  = '<table.*?class=[\'"]kj_tablelist02[\'"].*?>.*?';
        $rule .= '<tr>.*?<td.*?>.*?开奖详情.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<td.*?>.*?<\/td>.*?<\/tr>.*?';
        $rule .= '<tr.*?>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<td>(.*?)<\/td>.*?<\/tr>.*?';
        $rule .= '<tr>.*?<\/tr>.*?<\/table>';
        preg_match("/$rule/is", $content, $detail);
        if(!isset($detail[1]))
        {
        	return ;
        }
        unset($detail[0]);
        $bonusDetail['zx']['zs'] = $this->CI->lib_comm->format_num($detail[2]);
        $bonusDetail['zx']['dzjj'] = $this->CI->lib_comm->format_num($detail[3]);
        $data['bonusDetail'] = json_encode($bonusDetail);
        $data['rstatus'] = $this->CI->lib_comm->getRStatus($bonusDetail, array('issue' => $data['issue'], 'ctype' => $data['lid']));
        $res = $this->CI->data_model->insertNumberAwards($data);
        if(!$res)
        {
        	log_message('error', '写入数据库失败');
        }
    }
}