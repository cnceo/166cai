<?php

class Awards extends MY_Controller {
	
	const STATE_HISTORY = 201; //期号状态，已开奖
	
    public function __construct() 
    {
        parent::__construct();
        $this->load->library('tools');
        $this->load->model('award_model', 'Award');
        $this->load->model('lottery_model', 'Lottery');
    }

    /**
     * 开奖公告
     */
    public function index()
    {
    	$awards = array();
    	$awardData = $this->Award->getLastByDcenter();
        if(!empty($awardData))
        {
            foreach ($awardData as $award) 
            {
                $awards[$award['seLotid']] = $award;
            }
        } 

    	$this->load->view('award/index', array('title' => '历史开奖', 'awards' => $awards));
    }
    
    /**
     * 数字彩、老足彩历史开奖
     */
    public function number($lotteryId, $pageNumber = 1)
    {
    	$awards = array();
    	$lotteryId = intval($lotteryId);
    	$pageNumber = intval($pageNumber);
    	$pageNumber = max(1, $pageNumber);
		if(in_array( $lotteryId, $this->getNumberType()))
		{
            $num = (in_array($lotteryId, array('53', '54', '57'))) ? 25 : 10;
			$awards = $this->Award->getNumberByDcenter($lotteryId, $pageNumber, $num, self::STATE_HISTORY);
		}

        // 快三历史开奖形态
		if(!empty($awards) && in_array($lotteryId, array('53', '57')))
        {
            foreach ($awards as $key => $items) 
            {
                $awards[$key]['mark'] = $this->getKsMark($items['awardNumber']);
            }
        }

    	if($this->is_ajax)
    	{
    	    if(in_array($lotteryId, array('53', '57')))
            {
                echo $this->load->view('award/ksAjaxList', array(
                    'title' => '历史开奖',
                    'awards' => $awards,
                    'lotteryId' => $lotteryId,
                    'cnName' => $this->Lottery->getCnName($lotteryId),
                    'enName' => $this->Lottery->getEnName($lotteryId),
                    'pageNumber' => $pageNumber,
                ), true);
            }
            else
            {
                echo $this->load->view('award/number', array(
                    'title' => '历史开奖',
                    'awards' => $awards,
                    'lotteryId' => $lotteryId,
                    'cnName' => $this->Lottery->getCnName($lotteryId),
                    'enName' => $this->Lottery->getEnName($lotteryId),
                    'pageNumber' => $pageNumber,
                ), true);
            }
    	}
    	else
    	{
    		if(in_array($lotteryId, array('53', '57')))
            {
                $this->load->view('award/ksList', array(
                    'title' => '历史开奖',
                    'awards' => $awards,
                    'lotteryId' => $lotteryId,
                    'cnName' => $this->Lottery->getCnName($lotteryId),
                    'enName' => $this->Lottery->getEnName($lotteryId),
                    'pageNumber' => $pageNumber
                ));
            }
            else
            {
                $this->load->view('award/number', array(
                    'title' => '历史开奖',
                    'awards' => $awards,
                    'lotteryId' => $lotteryId,
                    'cnName' => $this->Lottery->getCnName($lotteryId),
                    'enName' => $this->Lottery->getEnName($lotteryId),
                    'pageNumber' => $pageNumber
                ));
            }
    	}
    }
    
    /**
     * 竞彩足球历史开奖
     */
    public function jczq($date='')
    {
        if(empty($date))
        {
            $date = $this->Award->getLastJczqDate();
        }

    	$matches = $this->Award->getJczqByDcenter(Lottery_Model::JCZQ, $date, self::STATE_HISTORY);

    	$this->load->view('award/jczq', array('title' => '开奖详情', 'matches' => $matches));
    }
    
    /**
     * 竞彩篮球开奖记录
     */
    public function jclq($date='') 
    {
    	if(empty($date))
        {
            $date = $this->Award->getLastJclqDate();
        }

    	$matches = $this->Award->getJclqByDcenter(Lottery_Model::JCLQ, $date, self::STATE_HISTORY);
    	
    	$this->load->view('award/jclq', array('title' => '开奖详情', 'matches' => $matches));
    	
    }
    
    /**
     * 数字彩、老足彩开奖详情
     * @param unknown_type $lotteryId
     * @param unknown_type $issue
     */
    public function getAwardDetail($lotteryId, $issue = 0)
    {
    	$awards = array();
    	$lotteryId = intval($lotteryId);
    	$pageNumber = intval($issue);

        if(empty($issue))
        {
            // 接口获取最新期次
            $awardsInfo = array();
            $awardData = $this->Award->getLastByDcenter();
            if(!empty($awardData))
            {
                foreach ($awardData as $award) 
                {
                    $awardsInfo[$award['seLotid']] = $award;
                }
            }    
            $issue = $awardsInfo[$lotteryId]['seExpect'];
        }

		if(in_array( $lotteryId, $this->getNumberType()))
		{
			$awards = $this->Award->getAwardDetailByDcenter($lotteryId, $issue);
		}

    	$this->load->view('award/detail', array(
                'title' => '开奖详情',
    			'awards' => $awards,
    			'lotteryId' => $lotteryId,
    			'cnName' => $this->Lottery->getCnName($lotteryId),
    			'enName' => $this->Lottery->getEnName($lotteryId),
    			'issue' => $issue
    	));
    }
    
    //定义数字彩类型数组
    private function getNumberType()
    {
    	return array(
    			Lottery_Model::DLT,
    			Lottery_Model::SSQ,
    			Lottery_Model::SYYDJ,
                Lottery_Model::JXSYXW,
                Lottery_Model::HBSYXW,
    			Lottery_Model::FCSD,
    			Lottery_Model::PLS,
    			Lottery_Model::PLW,
    			Lottery_Model::QXC,
    			Lottery_Model::QLC,
    			Lottery_Model::SFC,
    			Lottery_Model::RJ,
                Lottery_Model::KS,
                Lottery_Model::CQSSC,
    	        Lottery_Model::JXKS,
    	        Lottery_Model::GDSYXW,
    	);
    }

    // 快三形态
    public function getKsMark($awardNumber)
    {
        $mark = '';
        if(!empty($awardNumber))
        {
            $awards = explode(',', $awardNumber);

            if($awards[0] == $awards[1] && $awards[1] == $awards[2])
            {
                $mark = '三同号';
            }
            elseif($awards[0] == $awards[1] || $awards[1] == $awards[2] || $awards[2] == $awards[0])
            {
                $mark = '二同号';
            }
            else
            {
                asort($awards);
                $max = end($awards);
                if($max * 3 - array_sum($awards) > 3)
                {
                    $mark = '三不同号';
                }
                else
                {
                    $mark = '三连号';
                }
            }
        }
        return $mark;
    }

    // 竞彩足球开奖详情
    public function jczqDetail()
    {
        $mid = $this->input->get("mid", true);

        $result = array(
            'status' => '0',
            'msg' => '通讯成功',
            'data' => ''
        );

        if(!empty($mid))
        {
            $detail = $this->Award->getJczqAwardDetail($mid);
            if(!empty($detail))
            {
                $tpl = '';
                $tpl .= '<div class="overflow-y">';
                $tpl .= '<table class="table-bet">';
                $tpl .= '<caption>胜平负过关固定奖金</caption>';
                $tpl .= '<thead>';    
                $tpl .= '<tr>'; 
                $tpl .= '<th width="34%">发布时间</th>';         
                $tpl .= '<th width="22%">主胜</th>'; 
                $tpl .= '<th width="22%">主平</th>'; 
                $tpl .= '<th width="22%">主负</th>';             
                $tpl .= '</tr>';             
                $tpl .= '</thead>';             
                $tpl .= '<tbody>'; 
                // 赛果
                $info = json_decode($detail[0]['detail'], true);
                // 胜平负     
                if($detail[1]['ctype'] == '1')
                {
                    $spfDetail = $detail[1]['detail'];
                    $spfDetail = json_decode($spfDetail, true);
                    if(!empty($spfDetail) && !empty($info['spf']))
                    {
                        foreach ($spfDetail as $key => $spf) 
                        {
                            $tpl .= '<tr>';
                            $tpl .= '<td>' . date('m-d H:i:s', strtotime($spf['t'])) .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['spf'] == '胜')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $spf['s'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['spf'] == '平')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $spf['p'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['spf'] == '负')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $spf['f'] .'</td>';
                            $tpl .= '</tr>';
                        }
                    }
                }    
                $tpl .= '</tbody>'; 
                $tpl .= '</table>';

                $tpl .= '<table class="table-bet">';   
                $tpl .= '<caption>让球胜平负过关固定奖金</caption>';                 
                $tpl .= '<thead>';         
                $tpl .= '<tr>';             
                $tpl .= '<th width="32%">发布时间</th>';
                $tpl .= '<th width="16%">让球</th>';
                $tpl .= '<th width="16%">主胜</th>';            
                $tpl .= '<th width="16%">主平</th>';
                $tpl .= '<th width="16%">主负</th>';
                $tpl .= '</tr>';
                $tpl .= '</thead>';
                $tpl .= '<tbody>';
                // 让球胜平负   
                if($detail[2]['ctype'] == '2')
                {
                    $rspfDetail = $detail[2]['detail'];
                    $rspfDetail = json_decode($rspfDetail, true);
                    $info['rqspf'] = preg_replace('/\([+-]?\d+\)/is', '', $info['rqspf']);
                    if(!empty($rspfDetail) && !empty($info['rqspf']))
                    {
                        foreach ($rspfDetail as $key => $rspf) 
                        {
                            $tpl .= '<tr>';
                            $tpl .= '<td>' . date('m-d H:i:s', strtotime($rspf['t'])) .'</td>';
                            $tpl .= '<td>' . $rspf['rq'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['rqspf'] == '胜')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $rspf['s'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['rqspf'] == '平')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $rspf['p'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($info['rqspf'] == '负')?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $rspf['f'] .'</td>';
                            $tpl .= '</tr>';
                        }
                    }
                }
                $tpl .= '</tbody>'; 
                $tpl .= '</table>';
                $tpl .= '</div>';
                
                $result = array(
                    'status' => '1',
                    'msg' => '通讯成功',
                    'data' => $tpl
                );
            }
        }
        echo json_encode($result);
    }

    // 竞彩足球开奖详情
    public function jclqDetail()
    {
        $mid = $this->input->get("mid", true);

        $result = array(
            'status' => '0',
            'msg' => '通讯成功',
            'data' => ''
        );

        if(!empty($mid))
        {
            $detail = $this->Award->getJclqAwardDetail($mid);
            if(!empty($detail))
            {
                $tpl = '';
                $tpl .= '<div class="overflow-y">';
                $tpl .= '<table class="table-bet">';
                $tpl .= '<caption>胜负固定奖金</caption>';
                $tpl .= '<thead>';    
                $tpl .= '<tr>'; 
                $tpl .= '<th width="34%">发布时间</th>';         
                $tpl .= '<th width="33%">主负</th>'; 
                $tpl .= '<th width="33%">主胜</th>';            
                $tpl .= '</tr>';             
                $tpl .= '</thead>';             
                $tpl .= '<tbody>'; 
                // 胜负     
                if($detail[0]['ctype'] == '1')
                {
                    $sfDetail = $detail[0]['detail'];
                    $scoreArr = ($detail[0]['full_score']) ? explode(':', trim($detail[0]['full_score'])) : array();
                    $sfDetail = json_decode($sfDetail, true);
                    if(!empty($sfDetail))
                    {
                        foreach ($sfDetail as $key => $sf) 
                        {
                            $tpl .= '<tr>';
                            $tpl .= '<td>' . date('m-d H:i:s', strtotime($sf['t'])) .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($scoreArr[1] < $scoreArr[0])?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $sf['zf'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($scoreArr[1] > $scoreArr[0])?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $sf['zs'] .'</td>';
                            $tpl .= '</tr>';
                        }
                    }
                }    
                $tpl .= '</tbody>'; 
                $tpl .= '</table>';

                $tpl .= '<table class="table-bet">';   
                $tpl .= '<caption>让分胜负固定奖金</caption>';                 
                $tpl .= '<thead>';         
                $tpl .= '<tr>';             
                $tpl .= '<th width="34%">发布时间</th>';         
                $tpl .= '<th width="22%">主负</th>'; 
                $tpl .= '<th width="22%">让分</th>'; 
                $tpl .= '<th width="22%">主胜</th>';  
                $tpl .= '</tr>';
                $tpl .= '</thead>';
                $tpl .= '<tbody>';
                // 让分胜负   
                if($detail[1]['ctype'] == '2')
                {
                    $rfsfDetail = $detail[1]['detail'];
                    $scoreArr = ($detail[1]['full_score']) ? explode(':', trim($detail[1]['full_score'])) : array();
                    $rfsfDetail = json_decode($rfsfDetail, true);
                    if(!empty($rfsfDetail))
                    {
                        foreach ($rfsfDetail as $key => $rfsf) 
                        {
                            $tpl .= '<tr>';
                            $tpl .= '<td>' . date('m-d H:i:s', strtotime($rfsf['t'])) .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($scoreArr[1] + $rfsf['rf'] < $scoreArr[0])?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $rfsf['rfzf'] .'</td>';
                            $tpl .= '<td>' . $rfsf['rf'] .'</td>';
                            $tpl .= '<td class="';
                            $tpl .= ($scoreArr[1] + $rfsf['rf'] > $scoreArr[0])?'special-color':'';
                            $tpl .= '">';
                            $tpl .= $rfsf['rfzs'] .'</td>';
                            $tpl .= '</tr>';
                        }
                    }
                }
                $tpl .= '</tbody>'; 
                $tpl .= '</table>';
                $tpl .= '</div>';
                
                $result = array(
                    'status' => '1',
                    'msg' => '通讯成功',
                    'data' => $tpl
                );
            }
        }
        echo json_encode($result);
    }

    // 中奖墙
    public function wall()
    {
        $winData = $this->Award->getIndexWin();
        $info = array();
        if(!empty($winData))
        {
            foreach ($winData as $items) 
            {
                preg_match('/(\d+)$/is', trim($items['url']), $match);
                $newsId = $match[1] ? $match[1] : 0;
                $info[] = array(
                    'bonus' =>  $items['content'],
                    'lot'   =>  $items['lname'],
                    'text'  =>  $items['title'],
                    'url'   =>  $this->config->item('pages_url') . 'ios/info/detail/' . $newsId,
                    'top'   =>  $items['is_top']
                );
            }
        }
        // 投注期次
        $lotteryData = $this->getDefaultByLid();
        $winnews = $this->Award->getWins();
        $margin = $winnews['count']['margin'] + 4000000000;
        $b = floor($margin / 10000000000);
        $m = floor(($margin - $b * 10000000000) / 1000000);
        $str = '';
        if ($b > 0) {
            $str.=$b . "亿";
        }
        if ($m > 0) {
            $str.=$m . "万";
        }
        if(!empty($lotteryData))
        {
            $this->load->library('BetCnName');
            $weekarray=array("日","一","二","三","四","五","六");
            $lottery = array(
                'title'     =>  BetCnName::$BetCnName[$lotteryData['seLotid']],
                'slogan' => '周' . $weekarray[date("w", substr($lotteryData['seFsendtime'], 0, 10))] . '晚' . date("H:i", substr($lotteryData['awardTime'], 0, 10)) . '开奖',
                'lid'       =>  $lotteryData['seLotid'],
                'issue'     =>  $lotteryData['seExpect'],
                'endTime'   =>  date('Y-m-d H:i:s',substr($lotteryData['seFsendtime'], 0, 10)),
                'award'     => $str
            );
        }
        $this->load->view('award/wall', array('info' => $info, 'lottery' => $lottery));
    }

    // 获取当前投注彩种
    public function getDefaultByLid()
    {
        $this->load->model('cache_model','Cache');
        $ssq = $this->Cache->getCurrentByLid(51);
        $dlt = $this->Cache->getCurrentByLid(23529);

        if($dlt[0]['seFsendtime'] > 0 && $dlt[0]['seFsendtime'] < $ssq[0]['seFsendtime'])
        {
            $info = $dlt[0];
        }
        elseif($ssq[0]['seFsendtime'] > 0 && $ssq[0]['seFsendtime'] < $dlt[0]['seFsendtime'])
        {
            $info = $ssq[0];
        }
        else
        {
            $info = array();
        }
        return $info;
    }
}