<?php

class Kaijiang extends MY_Controller {

	public static $JCLQ_TYPE_MAP = array(
			'hh'  => array(
					'cnName' => '混合过关',
			),
			'sfc' => array(
					'cnName' => '胜分差',
			),
	);
	
	public static $JCZQ_TYPE_MAP = array(
			'hh'    => array(
					'cnName' => '混合过关',
			),
			'dg'    => array(
					'cnName' => '单关',
			),
			'spf'   => array(
					'cnName' => '胜平负',
			),
			'rqspf' => array(
					'cnName' => '让球胜平负',
			),
			'cbf'   => array(
					'cnName' => '比分',
			),
//			'jqs'   => array(
//					'cnName' => '总进球',
//			),
//			'bqc'   => array(
//					'cnName' => '半全场',
//			),
	);
    public function __construct() {
        parent::__construct();
       $this->load->model('award_model', 'Award');
       $this->load->model('lottery_model', 'Lottery');
    }

    public function index() {
        $awardData = $this->Award->getCurrentAward();
        $awards = array();
        foreach ($awardData as $award) 
        {
            $awards[$award['seLotid']] = $award;
        }
        $this->displayMore('kaijiang/index', 
            array(
                'htype'=>1,
                'awards' => $awards
            ),"v1.1");
    }

    public function ssq($issue = null)
    {
        $data = $this->Lottery->getDetail(51, $issue);
        $issueList = $this->Lottery->getAllIssue(51, '2015001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $arr = explode('|', $data['awardNum']);
        $data['award'] = array('red' => explode(',', $arr[0]), 'blue' => $arr[1]);
        $data['sale'] = $this->jine_format($data['sale']);
        $data['pool'] = $this->jine_format($data['pool']);
        $this->display('kaijiang/ssq', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function dlt($issue = null)
    {
        $data = $this->Lottery->getDetail(23529, $issue);
        $issueList = $this->Lottery->getAllIssue(23529, '15001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $arr = explode('|', $data['awardNum']);
        $data['award'] = array('red' => explode(',', $arr[0]), 'blue' => explode(',', $arr[1]));
        $data['sale'] = $this->jine_format($data['sale']);
        $data['pool'] = $this->jine_format($data['pool']);
        $this->display('kaijiang/dlt', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function qlc($issue = null)
    {
        $data = $this->Lottery->getDetail(23528, $issue);
        $issueList = $this->Lottery->getAllIssue(23528, '2008001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $arr = explode('(', $data['awardNum']);
        $data['award'] = array('red' => explode(',', $arr[0]), 'blue' => str_replace(')', '', $arr[1]));
        $data['sale'] = $this->jine_format($data['sale']);
        $data['pool'] = $this->jine_format($data['pool']);
        $data['htype'] = 1;
        $this->display('kaijiang/qlc', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function qxc($issue = null)
    {
        $data = $this->Lottery->getDetail(10022, $issue);
        $issueList = $this->Lottery->getAllIssue(10022, '08001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $data['sale'] = $this->jine_format($data['sale']);
        $data['pool'] = $this->jine_format($data['pool']);
        $data['htype'] = 1;
        $this->display('kaijiang/qxc', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function pl3($issue = null)
    {
        $data = $this->Lottery->getDetail(33, $issue);
        $issueList = $this->Lottery->getAllIssue(33, '15001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $data['sale'] = $this->jine_format($data['sale']);
        $data['htype'] = 1;
        $this->display('kaijiang/pl3', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function pl5($issue = null)
    {
        $data = $this->Lottery->getDetail(35, $issue);
        $issueList = $this->Lottery->getAllIssue(35, '15001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $data['sale'] = $this->jine_format($data['sale']);
        $data['htype'] = 1;
        $this->display('kaijiang/pl5', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function fc3d($issue = null)
    {
        $data = $this->Lottery->getDetail(52, $issue);
        $issueList = $this->Lottery->getAllIssue(52, '2015001');
        $issue = $issue ? $issue : $issueList[0]['issue'];
        $data['sale'] = $this->jine_format($data['sale']);
        $data['htype'] = 1;
        $this->display('kaijiang/fc3d', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'htype' => 1), 'v1.1');
    }
    
    public function syxw($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['SYXW_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['SYXW_ISSUE_TZ']), true);
        foreach ($award as $aw) {
        	if (strpos($aw['issue'], date('ymd')) !== false) {
        		$cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        	}
        }
        if($date && $date !== date('Y-m-d')) {
        	$cdate['data'] = array();
        	$data = $this->Lottery->getDetail(SYXW, $date);
        	foreach ($data as $val) {
        		$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
        	}
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/syxw', $cdate, 'v1.1');
    }
    
    //新11选5
    public function jxsyxw($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['JXSYXW_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['JXSYXW_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
        	if (strpos($aw['issue'], date('ymd')) !== false) {
        		$cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        	}
        }
        if($date && $date !== date('Y-m-d')) {
        	$cdate['data'] = array();
        	$data = $this->Lottery->getDetail(JXSYXW, $date);
        	foreach ($data as $val) {
        		$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
        	}
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/jxsyxw', $cdate, 'v1.1');
    }
    
    public function hbsyxw($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['HBSYXW_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['HBSYXW_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
        	if (strpos($aw['issue'], date('ymd')) !== false) {
        		$cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        	}
        }
        if($date && $date !== date('Y-m-d')) {
        	$cdate['data'] = array();
        	$data = $this->Lottery->getDetail(HBSYXW, $date);
        	foreach ($data as $val) {
        		$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
        	}
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/hbsyxw', $cdate, 'v1.1');
    }
    
    //乐11选5
    public function gdsyxw($date = null)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['GDSYXW_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['GDSYXW_ISSUE_TZ']), true);
        foreach ($award as $aw) {
            if (strpos($aw['issue'], date('ymd')) !== false) {
                $cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
            }
        }
        if($date && $date !== date('Y-m-d')) {
            $cdate['data'] = array();
            $data = $this->Lottery->getDetail(GDSYXW, $date);
            foreach ($data as $val) {
                $cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
            }
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
        $cdate['info'] = $issue;
        $cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/gdsyxw', $cdate, 'v1.1');
    }
    
    public function rj($issue = null)
    {
        $data = $this->Lottery->getDetail(19, $issue);
        $issueList = $this->Lottery->getAllIssue(19, '15051');
        $issue = $issue ? $issue : $data['mid'];
        $team = $this->Lottery->getTeamByIssue($issue);
        $data['rj_sale'] = $this->jine_format($data['rj_sale']);
        $data['award'] = $this->jine_format($data['award']);
        $this->display('kaijiang/rj', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'team' => $team, 'htype' => 1), 'v1.1');
    }
    
    public function sfc($issue = null)
    {
        $data = $this->Lottery->getDetail(11, $issue);
        $issueList = $this->Lottery->getAllIssue(11, '15051');
        $issue = $issue ? $issue : $data['mid'];
        $team = $this->Lottery->getTeamByIssue($issue);
        $data['rj_sale'] = $this->jine_format($data['rj_sale']);
        $data['award'] = $this->jine_format($data['award']);
        $this->display('kaijiang/sfc', array('data' => $data, 'issueList' => $issueList, 'issue' => $issue, 'team' => $team, 'htype' => 1), 'v1.1');
    }
    
	public function ks($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['KS_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['KS_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
        	if (strpos($aw['issue'], date('ymd')) !== false) $cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        }
        if($date && $date !== date('Y-m-d')) {
        	$cdate['data'] = array();
        	$data = $this->Lottery->getDetail(KS, $date);
        	foreach ($data as $val) {
        		$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
        	}
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/ks', $cdate, 'v1.1');
    }
    
    public function jlks($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$award = unserialize($this->cache->get ( $REDIS ['JLKS_AWARD'] ));
    	$issue = json_decode($this->cache->get($REDIS['JLKS_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
    		if (strpos($aw['issue'], date('ymd')) !== false) $cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
    	}
    	if($date && $date !== date('Y-m-d')) {
    		$cdate['data'] = array();
    		$data = $this->Lottery->getDetail(JLKS, $date);
    		foreach ($data as $val) {
    			$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
    		}
    	}
    	$cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
    	$cdate['htype'] = 1;
    	$this->display('kaijiang/jlks', $cdate, 'v1.1');
    }
    
    public function jxks($date = null)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $award = unserialize($this->cache->get ( $REDIS ['JXKS_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['JXKS_ISSUE_TZ']), true);
        foreach ($award as $aw) {
            if (strpos($aw['issue'], date('ymd')) !== false) $cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        }
        if($date && $date !== date('Y-m-d')) {
            $cdate['data'] = array();
            $data = $this->Lottery->getDetail(JXKS, $date);
            foreach ($data as $val) {
                $cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
            }
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
        $cdate['info'] = $issue;
        $cdate['issue'] = $issue['cIssue']['seExpect'];
        $cdate['htype'] = 1;
        $this->display('kaijiang/jxks', $cdate, 'v1.1');
    }
    
    public function klpk($date = null)
    {
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$award = unserialize($this->cache->get ( $REDIS ['KLPK_AWARD'] ));
        $issue = json_decode($this->cache->get($REDIS['KLPK_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
        	if (strpos($aw['issue'], date('ymd')) !== false) {
        		$cdate['data'][substr($aw['issue'],  -2)] = array('awardNum' => $aw['awardNum']);
        	}
        }
        if($date && $date !== date('Y-m-d')) {
        	$cdate['data'] = array();
        	$data = $this->Lottery->getDetail(KLPK, $date);
        	foreach ($data as $val) {
        		$cdate['data'][substr($val['issue'],  -2)] = array('awardNum' => $val['awardNum']);
        	}
        }
        $cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
    	$cdate['htype'] = 1;
    	$this->display('kaijiang/klpk', $cdate, 'v1.1');
    }
    
    public function cqssc($date = null)
    {
        //时时彩下架
        $this->redirect('/error');
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->load->library('handlenum/HandleCqssc');
    	$award = unserialize($this->cache->get ( $REDIS ['CQSSC_AWARD'] ));
    	$issue = json_decode($this->cache->get($REDIS['CQSSC_ISSUE_TZ']), true);
    	foreach ($award as $aw) {
    		if (strpos($aw['issue'], date('ymd')) !== false) {
    			$cdate['data'][substr($aw['issue'],  -3)] = array('awardNum' => $aw['awardNum']);
    		}
    	}
    	if($date && $date !== date('Y-m-d')) {
    		$cdate['data'] = array();
    		$data = $this->Lottery->getDetail(CQSSC, $date);
    		foreach ($data as $val) {
    			$cdate['data'][substr($val['issue'],  -3)] = array('awardNum' => $val['awardNum']);
    		}
    	}
    	$cdate['date'] = $date ? $date : date('Y-m-d');
    	$cdate['info'] = $issue;
    	$cdate['issue'] = $issue['cIssue']['seExpect'];
    	$cdate['htype'] = 1;
    	$this->display('kaijiang/cqssc', $cdate, 'v1.1');
    }
    
    function jine_format($str)
    {
        $num = strlen($str) % 3;
        $sl = substr($str, 0, $num);
        $sl = empty($sl) ? $sl : $sl.",";
        $sr = substr($str, $num);
        $arr = str_split($sr, 3);
        return $sl.implode(',', $arr);
    }
    
    public function jczq($date = '')
    {
    	if (empty($date))
    	{
    		$date = $this->Award->getDefaultDate(Lottery_Model::JCZQ);
    	}
    
    	$matches = array();
    	$awards = $this->Award->getJCNew(Lottery_Model::JCZQ, $date);
    	$this->load->config('wenan');
    	$wenan = $this->config->item('wenan');
    	foreach ($awards as $items) {
    		$match = array();
    		$match['issue'] = $items['m_date'];
    		$match['score'] = $items['full_score'];
    		$match['mid'] = $items['mid'];
    		$match['homeSname'] = $items['home'];
    		$match['awary'] = $items['away'];
    		$match['awarySname'] = $items['away'];
    		$match['scoreHalf'] = $items['half_score'];
    		$match['home'] = $items['home'];
    		$match['name'] = $items['league'];
    		$match['let'] = $items['rq'];
                $match['m_status'] = $items['m_status'];
    		$match['end_sale_time'] = $items['end_sale_time'];
    		list($homeScore, $awayScore) = explode(':', $items['full_score']);
    		list($homeHalfScore, $awayHalfScore) = explode(':', $items['half_score']);
    		// 胜平负
    		$q = $homeScore > $awayScore ? '3' : ($homeScore == $awayScore ? 1 : 0);
    		$r = $homeScore + $items['rq'] > $awayScore ? '3' : ($homeScore + $items['rq'] == $awayScore ? 1 : 0);
    		$b = $homeHalfScore > $awayHalfScore ? '3' : ($homeHalfScore == $awayHalfScore ? 1 : 0);
    		$match['spf'] = $wenan['jzspf'][$q];
    		$match['rqspf'] = $wenan['jzspf']["r".$r];
    		$match['bqc'] = $wenan['jzspf']["o".$b]."-".$wenan['jzspf']["o".$q];
    		// 总进球
    		$match['jqs'] = $homeScore + $awayScore;
    		// 拆分获取 日期 + 赛事编号
    		$weekarray = array("日", "一", "二", "三", "四", "五", "六");
    		$matchDate = "周" . $weekarray[date("w", strtotime($match['issue']))];
    		$match['matchId'] = $matchDate . substr($match['mid'], 8);
            // 开奖详情
            $match['showDetail'] = $items['showDetail'] ? 1 : 0;
    		$matches[] = $match;
    	}
    
    	$dates = array(
    			date('Y.m.d')                      => date("Ymd"),
    			date("Y.m.d", strtotime("-1 day")) => date("Ymd", strtotime("-1 day")),
    			date("Y.m.d", strtotime("-2 day")) => date("Ymd", strtotime("-2 day")),
    			date("Y.m.d", strtotime("-3 day")) => date("Ymd", strtotime("-3 day")),
    			date("Y.m.d", strtotime("-4 day")) => date("Ymd", strtotime("-4 day")),
    			date("Y.m.d", strtotime("-5 day")) => date("Ymd", strtotime("-5 day")),
    			date("Y.m.d", strtotime("-6 day")) => date("Ymd", strtotime("-6 day")),
    			date("Y.m.d", strtotime("-7 day")) => date("Ymd", strtotime("-7 day")),
    			date("Y.m.d", strtotime("-8 day")) => date("Ymd", strtotime("-8 day")),
    			date("Y.m.d", strtotime("-9 day")) => date("Ymd", strtotime("-9 day")),
    	);
    	$this->display('awards/jczq', array(
    			'matches'   => $matches,
    			'dates'     => $dates,
    			'date'      => $date,
    			'issue'     => $date,
    			'lotteryId' => JCZQ,
    			'enName'    => 'jczq',
    			'typeMAP'   => self::$JCZQ_TYPE_MAP,
    			'topBanner' => 'awards',
    	), 'v1.1');
    }
    
    public function jclq($date = '')
    {
    	if (empty($date))
    	{
    		$date = $this->Award->getDefaultDate(Lottery_Model::JCLQ);
    	}
    	$matches = array();
    	$awards = $this->Award->getJCNew(Lottery_Model::JCLQ, $date);
    	$this->load->config('wenan');
    	$wenan = $this->config->item('wenan');
    	foreach ($awards as $in => $items) {
    		$match = array();
    		$match['issue'] = $items['m_date'];
    		$match['score'] = $items['full_score'];
    		$match['mid'] = $items['mid'];
    		$match['home'] = $items['home'];
    		$match['awary'] = $items['away'];
    		$match['name'] = $items['league'];
                $match['m_status'] = $items['m_status'];
    		$match['let'] = $items['rq'];
    		$match['begin_time'] = $items['begin_time'];
    
    		list($awayScore, $homeScore) = explode(':', $match['score']);
    		$preScore = $items['preScore'];
    		$q = $homeScore > $awayScore ? 3 : 0;
    		$r = $homeScore + $match['let'] > $awayScore ? 3 : 0;
    		$sfc = $match['sf'] = $wenan['jlsf'][$q];
    		$match['rfsf'] = $wenan['jlsf']["r".$r];
    		if ($homeScore >= $awayScore)
    		{
    			$gap = $homeScore - $awayScore;
    		}
    		else
    		{
    			$gap = $awayScore - $homeScore;
    		}
    		if ($gap >= 1 && $gap <= 5)
    		{
    			$sfc .= '1-5';
    		}
    		else if ($gap >= 6 && $gap <= 10)
    		{
    			$sfc .= '6-10';
    		}
    		else if ($gap >= 11 && $gap <= 15)
    		{
    			$sfc .= '11-15';
    		}
    		else if ($gap >= 16 && $gap <= 20)
    		{
    			$sfc .= '16-20';
    		}
    		else if ($gap >= 21 && $gap <= 25)
    		{
    			$sfc .= '21-25';
    		}
    		else if ($gap >= 26)
    		{
    			$sfc .= '26+';
    		}
    		$match['sfc'] = $sfc;
    		$match['dxf'] = ($homeScore + $awayScore > $preScore) ? '大分' : '小分';
    
    		//拆分获取 日期 + 赛事编号
    		$weekarray = array("日", "一", "二", "三", "四", "五", "六");
    		$matchDate = "周" . $weekarray[date("w", strtotime($match['issue']))];
    		$match['matchId'] = $matchDate . substr($match['mid'], 8);
            // 开奖详情
            $match['showDetail'] = $items['showDetail'] ? 1 : 0;
    		$matches[] = $match;
    	}
    	$dates = array(
    			date('Y.m.d')                      => date("Ymd"),
    			date("Y.m.d", strtotime("-1 day")) => date("Ymd", strtotime("-1 day")),
    			date("Y.m.d", strtotime("-2 day")) => date("Ymd", strtotime("-2 day")),
    			date("Y.m.d", strtotime("-3 day")) => date("Ymd", strtotime("-3 day")),
    			date("Y.m.d", strtotime("-4 day")) => date("Ymd", strtotime("-4 day")),
    			date("Y.m.d", strtotime("-5 day")) => date("Ymd", strtotime("-5 day")),
    			date("Y.m.d", strtotime("-6 day")) => date("Ymd", strtotime("-6 day")),
    			date("Y.m.d", strtotime("-7 day")) => date("Ymd", strtotime("-7 day")),
    			date("Y.m.d", strtotime("-8 day")) => date("Ymd", strtotime("-8 day")),
    			date("Y.m.d", strtotime("-9 day")) => date("Ymd", strtotime("-9 day")),
    	);
    	$this->display('awards/jclq', array(
    			'matches'   => $matches,
    			'dates'     => $dates,
    			'date'      => $date,
    			'lotteryId' => JCLQ,
    			'enName'    => 'jclq',
    			'typeMAP'   => self::$JCLQ_TYPE_MAP,
    			'topBanner' => 'awards',
    	), 'v1.1');
    }

    // 竞彩篮球开奖详情
    public function jclqDetail($mid = 0)
    {
        $awards = array();
        $detail = $this->Award->getJcDetail(Lottery_Model::JCLQ, $mid);
        if(!empty($detail))
        {
            $ctypeMap = array(
                '1' =>  'sf',
                '2' =>  'rfsf',
                '3' =>  'sfc',
                '4' =>  'dxf',
            );
            foreach ($detail as $key => $items) 
            {
                $scoreArr = ($items['full_score']) ? explode(':', trim($items['full_score'])) : array();
                $awards['info'] = array(
                    'm_date'    =>  $items['m_date'],
                    'mname'     =>  $items['mname'],
                    'league'    =>  $items['league'],
                    'home'      =>  $items['home'],
                    'away'      =>  $items['away'],
                    'full_score'    =>  $items['full_score'],
                    'hscore'    =>  $scoreArr[1] ? $scoreArr[1] : 0,
                    'ascore'    =>  $scoreArr[0] ? $scoreArr[0] : 0,
                    'fscore'    =>  array_sum($scoreArr),
                );
                $awards[$ctypeMap[$items['ctype']]] = json_decode($items['detail'], true);
                $awards['issue'] = $awards['info']['m_date'];
            }
        }
        $this->display('awards/jclqDetail', $awards, 'v1.1');
    }

    // 竞彩足球开奖详情
    public function jczqDetail($mid = 0)
    {
        $awards = array();
        $detail = $this->Award->getJcDetail(Lottery_Model::JCZQ, $mid);
        
        if(!empty($detail))
        {
            $ctypeMap = array(
                '0' =>  'sg',
                '1' =>  'spf',
                '2' =>  'rqspf',
                '3' =>  'bqc',
                '4' =>  'jqs',
                '5' =>  'cbf',
            );
            foreach ($detail as $key => $items) 
            {
                $awards['info'] = array(
                    'm_date'    =>  $items['m_date'],
                    'mname'     =>  $items['mname'],
                    'league'    =>  $items['league'],
                    'home'      =>  $items['home'],
                    'away'      =>  $items['away'],
                    'full_score'    =>  $items['full_score'],
                );
                $awards[$ctypeMap[$items['ctype']]] = json_decode($items['detail'], true);
            }

            $awards['info']['spf'] = $awards['sg']['spf'];
            $awards['info']['rqspf'] = preg_replace('/\([+-]?\d+\)/is', '', $awards['sg']['rqspf']);
            $awards['info']['jqs'] = $awards['sg']['jqs'];
            $awards['info']['bqc'] = str_replace(array('胜', '平', '负'), array('s', 'p', 'f'), $awards['sg']['bqc']);
            $awards['info']['cbf'] = $this->getCbf($awards['info']['full_score']);
            $awards['issue'] = $awards['info']['m_date'];
        }
        $this->display('awards/jczqDetail', $awards, 'v1.1');
    }

    private function getCbf($score)
    {
        $cbf = '';
        $tag = array(
            '1:0'   =>  's1',
            '2:0'   =>  's2',
            '2:1'   =>  's3',
            '3:0'   =>  's4',
            '3:1'   =>  's5',
            '3:2'   =>  's6',
            '4:0'   =>  's7',
            '4:1'   =>  's8',
            '4:2'   =>  's9',
            '5:0'   =>  's10',
            '5:1'   =>  's11',
            '5:2'   =>  's12',
            '0:0'   =>  's14',
            '1:1'   =>  's15',
            '2:2'   =>  's16',
            '3:3'   =>  's17',
            '0:1'   =>  's19',
            '0:2'   =>  's20',
            '1:2'   =>  's21',
            '0:3'   =>  's22',
            '1:3'   =>  's23',
            '2:3'   =>  's24',
            '0:4'   =>  's25',
            '1:4'   =>  's26',
            '2:4'   =>  's27',
            '0:5'   =>  's28',
            '1:5'   =>  's29',
            '2:5'   =>  's30',
        );
        if(!empty($score))
        {
            $scoreArr = explode(':', $score);
            if($scoreArr[0] > $scoreArr[1])
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's13';
            }
            elseif($scoreArr[0] == $scoreArr[1])
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's18';
            }
            else
            {
                $cbf = (!empty($tag[$score])) ? $tag[$score] : 's31';
            }
        }
        return $cbf;
    }
}
