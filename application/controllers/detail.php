<?php

class Detail extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('lottery_model', 'Lottery');
    }
    
    public function ssq($issue = null)
    {
    	$data = $this->Lottery->getDetail(51, $issue);
    	$issueList = $this->Lottery->getAllIssue(51);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$arr = explode('|', $data['awardNum']);
    	$data['award'] = array('red' => explode(',', $arr[0]), 'blue' => $arr[1]);
    	$data['sale'] = $this->jine_format($data['sale']);
    	$data['pool'] = $this->jine_format($data['pool']);
    	$this->display('detail/ssq', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function dlt($issue = null)
    {
    	$data = $this->Lottery->getDetail(23529, $issue);
    	$issueList = $this->Lottery->getAllIssue(23529);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$arr = explode('|', $data['awardNum']);
    	$data['award'] = array('red' => explode(',', $arr[0]), 'blue' => explode(',', $arr[1]));
    	$data['sale'] = $this->jine_format($data['sale']);
    	$data['pool'] = $this->jine_format($data['pool']);
    	$this->display('detail/dlt', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function qlc($issue = null)
    {
    	$data = $this->Lottery->getDetail(23528, $issue);
    	$issueList = $this->Lottery->getAllIssue(23528);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$arr = explode('(', $data['awardNum']);
    	$data['award'] = array('red' => explode(',', $arr[0]), 'blue' => str_replace(')', '', $arr[1]));
    	$data['sale'] = $this->jine_format($data['sale']);
    	$data['pool'] = $this->jine_format($data['pool']);
    	$this->display('detail/qlc', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function qxc($issue = null)
    {
    	$data = $this->Lottery->getDetail(10022, $issue);
    	$issueList = $this->Lottery->getAllIssue(10022);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$data['sale'] = $this->jine_format($data['sale']);
    	$data['pool'] = $this->jine_format($data['pool']);
    	$this->display('detail/qxc', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function pl3($issue = null)
    {
    	$data = $this->Lottery->getDetail(33, $issue);
    	$issueList = $this->Lottery->getAllIssue(33);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$data['sale'] = $this->jine_format($data['sale']);
    	$this->display('detail/pl3', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function pl5($issue = null)
    {
    	$data = $this->Lottery->getDetail(35, $issue);
    	$issueList = $this->Lottery->getAllIssue(35);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$data['sale'] = $this->jine_format($data['sale']);
    	$this->display('detail/pl5', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
    public function fc3d($issue = null)
    {
    	$data = $this->Lottery->getDetail(52, $issue);
    	$issueList = $this->Lottery->getAllIssue(52);
    	$issue = $issue ? $issue : $issueList[0]['issue'];
    	$data['sale'] = $this->jine_format($data['sale']);
    	$this->display('detail/fc3d', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue));
    }
    
	public function syxw($date = null)
    {
    	$dateList = $this->Lottery->getAllAwarddate(21406);
    	$date = $date ? $date : $dateList[0]['date'];
    	$data = $this->Lottery->getDetail(21406, $date);
    	$current = $this->Lottery->getCurrentIssue(21406);
    	$rest = strtotime($current['end_time'])-strtotime("now");
    	$min = floor($rest/60);
    	$second = $rest%60;
    	$this->display('detail/syxw', array('data' => $data, 'dateList' => $dateList, 'date' => $date, 'min' => $min, 'second' => $second, 'current' => $current));
    }
    
    function jine_format($str){
    	$num = strlen($str) % 3;
    	$sl = substr($str, 0, $num);
    	$sl = empty($sl) ? $sl : $sl.",";
    	$sr = substr($str, $num);
    	$arr = str_split($sr, 3);
    	return $sl.implode(',', $arr);
    }

}
