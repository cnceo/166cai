<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 比赛信息 接口
 * @version:V1.2
 * @date:2016-04-11
 */
class Match extends MY_Controller
{
	// 赛事状态
	public $lqMatchState = array(
		'0' => '未开赛',
		'1' => '第一节',
		'2' => '第一节完',
		'3' => '第二节',
		'4' => '第二节完',
		'5' => '第三节',
		'6' => '第三节完',
		'7' => '第四节',
		'8' => '第四节完',
		'9' => '完场',
		'10' => '加时',
		'11' => '完场(加)',
		'12' => '中断',
		'13' => '取消',
		'14' => '延期',
		'15' => '腰斩',
		'16' => '待定',
	);
	public $zqMatchState = array(
		'0' => '未开赛',
		'1' => '上半场',
		'2' => '中场',
		'3' => '下半场',
		'4' => '完场',
		'5' => '中断',
		'6' => '取消',
		'7' => '加时',
		'8' => '完场(加)',
		'9' => '点球',
		'10' => '延期',
		'11' => '腰斩',
		'12' => '待定',
		'13' => '金球',
	);
	
	/**
	 * 静态资源服务器地址数组
	 * @var unknown
	 */
	private $static_url;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('api_zhisheng_match2_model','Match');
		$this->load->driver('cache', array('adapter' => 'redis'));
        $this->config->load('jcMatch');
        $this->redis = $this->config->item('redisList');
        $this->static_url = $this->config->item('img_url');
	}

	public function index()
    {
        $result = array(
			'status' => '1',
			'msg' => '通讯成功',
			'data' => $this->getRequestHeaders()
		);
		echo json_encode($result);
    }
    
    /*
     * 赛事列表
    * @date:2016-04-11
    */
    public function matchLqList()
    {
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
    
    	$leagueInfo = $this->Match->getLeague(2);
    
    	if(!empty($leagueInfo))
    	{
    		foreach ($leagueInfo as $key => $items)
    		{
    			$leagueInfo[$key]['logo'] = $protocol . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/' . $items['logo'];
    		}
    	}
    
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $leagueInfo
    	);
    
    	$this->ajaxResult($result);
    }
    
    /*
     * 赛事列表 足球
    * @date:2016-04-11
    */
    public function matchZqList()
    {
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https:" : "http:";
    
    	$leagueInfo = $this->Match->getLeague(1);
    
    	if(!empty($leagueInfo))
    	{
    	    $header = $this->getRequestHeaders();
    		foreach ($leagueInfo as $key => $items)
    		{
    		    if(empty($header['platform']) && ($items['lid'] == 149)) {
    		        unset($leagueInfo[$key]);
    		        continue;
    		    }
    			$leagueInfo[$key]['logo'] = $protocol . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/' . $items['logo'];
    		}
    	}
    
    	$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    	        'data' => array_values($leagueInfo)
    	);
    
    	$this->ajaxResult($result);
    }
    
    /**
     * 查询赛程赛果信息
     */
    public function getLqMatchSchedule()
    {
    	$lid = $this->input->get("lid", true);
    	$sid = $this->input->get("sid", true);
    	$oname = $this->input->get("oname", true);
    	$mtime = $this->input->get("mtime", true);
    	$pageFlag = $this->input->get("pageFlag", true);
    	if(empty($lid) || empty($sid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$onames = $this->Match->getMatchOnames($lid, $sid);
    	if(empty($oname) || empty($mtime))
    	{
    		$date = date('Y-m-d');
    		if(!$oname)
    		{
    			$currentOname = $this->Match->getLqMatchOname($lid, $sid, $date);
    			$oname = $currentOname['oname'];
    		}
    		$list = $this->Match->getLqMatchStartSchedule($lid, $sid, $oname, $date);
    	}
    	else
    	{
    		$list = $this->Match->getLqMatchSchedule($lid, $sid, $oname, $mtime, $pageFlag);
    	}
    	$details = array();
    	foreach ($list as $value)
    	{
    		$value['stateMsg'] = $this->lqMatchState[$value['state']];
    		$details[] = $value;
    	}

    	$result = array(
			'status' => '1',
			'msg' => 'succ',
			'data' => array(
				'oname' => $oname,
				'onames' => $onames,
				'detail' => $details,
			)
		);
		
    	$this->ajaxResult($result);
    }
    
    /**
     * 查询赛程赛果信息
     */
    public function getZqMatchSchedule()
    {
    	$lid = $this->input->get("lid", true);
    	$sid = $this->input->get("sid", true);
    	$oname = $this->input->get("oname", true);
    	if(empty($lid) || empty($sid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$onames = $this->Match->getZqMatchOnames($lid, $sid);
    	if(empty($oname))
    	{
    		$date = date('Y-m-d');
    		$currentOname = $this->Match->getZqMatchOname($lid, $sid, $date);
    		$oname = $currentOname['oname'];
    		$list = $this->Match->getZqMatchStartSchedule($lid, $sid, $oname);
    	}
    	else
    	{
    		$list = $this->Match->getZqMatchSchedule($lid, $sid, $oname);
    	}
    	$details = array();
    	foreach ($list as $value)
    	{
    		$value['stateMsg'] = $this->zqMatchState[$value['state']];
    		//如果是2018世界杯赛程 主客队logo替换
    		if($lid == '149' && $sid == '7574') {
    		    shuffle($this->static_url);
    		    if($value['htid'] > 0) {
    		        $value['homelogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $this->static_url[0] . 'cpiaoimg/zqlogo/sjb/' . $value['htid'] . '.png';
    		    }
    		    if($value['atid'] > 0) {
    		        $value['awaylogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $this->static_url[0] . 'cpiaoimg/zqlogo/sjb/' . $value['atid'] . '.png';
    		    }
    		}
    		$details[] = $value;
    	}
    
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'oname' => $oname,
    			'onames' => $onames,
    			'detail' => $details,
    		)
    	);
		
    	$this->ajaxResult($result);
    }
    
    /*
     * 获取 篮球联赛 - 积分榜
    * @date:2016-04-12
    */
    public function getLqScoreRank()
    {
    	$sid = $this->input->get("sid", true);
    	if(empty($sid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少赛季编号',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	// 获取联赛赛程赛果
    	$data = $this->Match->getLqScoreRank($sid);
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $data
    	);

    	$this->ajaxResult($result);
    }
    
    /*
     * 获取 足球联赛 - 积分榜
    * @date:2016-04-12
    */
    public function getZqScoreRank()
    {
    	$sid = $this->input->get("sid", true);
    	if(empty($sid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少赛季编号',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	// 获取联赛赛程赛果
    	$data = $this->Match->getZqScoreRank($sid);
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $data
    	);
		
    	$this->ajaxResult($result);
    }
    
    /*
     * 获取 联赛 - 射手榜
    * @date:2016-04-12
    */
    public function getZqShotRank()
    {
    	$sid = $this->input->get("sid", true);
    	if(empty($sid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少赛季编号',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	// 获取联赛赛程赛果
    	$data = $this->Match->getZqShotRank($sid);
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $data
    	);
    	 
    	$this->ajaxResult($result);
    }
    
    /**
     * 篮球详情页面
     */
    public function lqMatchDetail()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	
    	$match = $this->Match->getLqMatchDetail($mid);
    	$matchData = '';
    	if($match)
    	{
    		$matchData['mid'] = $match['mid'];
    		$matchData['home'] = $match['home'];
    		$matchData['away'] = $match['away'];
    		$matchData['htid'] = $match['htid'];
    		$matchData['atid'] = $match['atid'];
    		$matchData['mtime'] = $match['mtime'];
    		$matchData['stime'] = $match['stime'];
    		$matchData['lid'] = $match['lid'];
    		$matchData['ln'] = $match['ln'];
    		$matchData['homelogo'] = $match['homelogo'] ? $match['homelogo'] : $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/lq_homelogo.png';
    		$matchData['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/lq_awaylogo.png';
    		$matchData['hqt'] = $match['hqt'];
    		$matchData['aqt'] = $match['aqt'];
    		$matchData['state'] = $match['state'];
    		$matchData['stateMsg'] = $this->lqMatchState[$match['state']];
    		$matchData['sid'] = $match['sid'];
    		$matchData['type'] = $match['type'];
    		$matchData['hpm'] = $match['hpm'];
    		$matchData['apm'] = $match['apm'];
    		$matchData['ctime'] = date('Y-m-d H:i:s');
    	}
    	
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $matchData
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 篮球详情页面
     */
    public function zqMatchDetail()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	 
    	$match = $this->Match->getZqMatchDetail($mid);
    	$matchData = '';
    	if($match)
    	{
    		$matchData['mid'] = $match['mid'];
    		$matchData['home'] = $match['home'];
    		$matchData['away'] = $match['away'];
    		$matchData['htid'] = $match['htid'];
    		$matchData['atid'] = $match['atid'];
    		$matchData['mtime'] = $match['mtime'];
    		$matchData['stime'] = $match['stime'];
    		$matchData['lid'] = $match['lid'];
    		$matchData['ln'] = $match['ln'];
    		$matchData['homelogo'] = $match['homelogo'] ? $match['homelogo'] : $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/zq_homelogo.png';
    		$matchData['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : $this->config->item('protocol') . $this->config->item('pages_url') . 'caipiaoimg/static/images/match/zq_awaylogo.png';
    		//如果是2018世界杯赛程 主客队logo替换
    		if($match['lid'] == '149' && $match['sid'] == '7574') {
    		    shuffle($this->static_url);
    		    if($match['htid'] > 0) {
    		        $matchData['homelogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $this->static_url[0] . 'cpiaoimg/zqlogo/sjb/' . $match['htid'] . '.png';
    		    }
    		    if($match['atid'] > 0) {
    		        $matchData['awaylogo'] = (ENVIRONMENT === 'production' ? 'https:' : 'http:') . $this->static_url[0] . 'cpiaoimg/zqlogo/sjb/' . $match['atid'] . '.png';
    		    }
    		}
    		$matchData['bc'] = $match['bc'];
    		$matchData['hqt'] = $match['hqt'];
    		$matchData['aqt'] = $match['aqt'];
    		$matchData['state'] = $match['state'];
    		$matchData['stateMsg'] = $this->zqMatchState[$match['state']];
    		$matchData['oname'] = $match['oname'];
    		$matchData['coname'] = $match['coname'];
    		$matchData['sid'] = $match['sid'];
    		$matchData['type'] = $match['type'];
    		$matchData['hpm'] = $match['hpm'];
    		$matchData['apm'] = $match['apm'];
    		$matchData['ctime'] = date('Y-m-d H:i:s');
    	}
    	 
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $matchData
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 足球预测数据
     */
    public function zqPrediction()
    {
        $mid = $this->input->get("mid", true);
        if(empty($mid))
        {
            $result = array(
                'status' => '0',
                'msg' => '参数错误',
                'data' => ''
            );
            $this->ajaxResult($result);
        }
        
        $match = $this->Match->getZqPrediction($mid);
        $matchData = '';
        if($match)
        {
            $matchData['hrank'] = $match['hrank'] ? $match['hrank'] : '0,0,0,0,0';
            $matchData['arank'] = $match['arank'] ? $match['arank'] : '0,0,0,0,0';
            $matchData['hrecent'] = $match['hrecent'] ? $match['hrecent'] : '0,0,0';
            $matchData['arecent'] = $match['arecent'] ? $match['arecent'] : '0,0,0';
            $matchData['hhistorical'] = $match['hhistorical'] ? $match['hhistorical'] : '0,0,0';
            $matchData['ahistorical'] = $match['ahistorical'] ? $match['ahistorical'] : '0,0,0';
            $matchData['odds'] = $match['odds'] ? $match['odds'] : '0,0,0';
            if($match['bet'])
            {
                $bet = explode(',', $match['bet']);
                $matchData['bet'] = $bet[1] . ',' . $bet[2];
            }
            else
            {
                $matchData['bet'] = '';
            }
            if ($match['transaction']) {
                $transaction = explode(',', $match['transaction']);
                $matchData['transaction'] = '$' . number_format($transaction[0]) . '|$' . number_format($transaction[1]) . '|$' . number_format($transaction[2]);
            } else {
                $matchData['transaction'] = '$0|$0|$0';
            }
            $matchData['prediction'] = $match['prediction'] ? $match['prediction'] : '0,0,0';
            $matchData['recommend'] = $match['recommend'];
            $matchData['upset'] = $match['upset'];
            $match['aidata'] = $match['aidata'] ? json_decode($match['aidata'], true) : array(
                'home' => array('strength' => 0, 'recentState' => 0, 'historyBattle' => 0, 'oddRecommend' => 0, 'betOptimistic' => 0, 'tradingHeat' => 0),
                'away' => array('strength' => 0, 'recentState' => 0, 'historyBattle' => 0, 'oddRecommend' => 0, 'betOptimistic' => 0, 'tradingHeat' => 0)
            );
            $matchData['aihome'] = $match['aidata']['home'];
            $matchData['aiaway'] = $match['aidata']['away'];
        }
        
        $result = array(
            'status' => '1',
            'msg' => 'succ',
            'data' => $matchData
        );
        
        $this->ajaxResult($result);
    }
    
    /**
     * 篮球比赛详情直播页面
     */
    public function getLqMatchLive()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	
    	//基本信息
    	$match = $this->Match->getLqMatchDetail($mid);
    	if(empty($match))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$liveData = array();
    	$liveData['match']['ln'] = $match['ln'];
    	$liveData['match']['home'] = $match['home'];
    	$liveData['match']['away'] = $match['away'];
    	$liveData['match']['mtime'] = $match['mtime'];
    	$liveData['match']['stime'] = $match['stime'];
    	$liveData['match']['ctime'] = date('Y-m-d H:i:s');
    	$liveData['match']['week'] = $this->getWeekName(date('w', strtotime($liveData['match']['mtime'])));
    	$liveData['match']['state'] = $match['state'];
    	$liveData['match']['stateMsg'] = $this->lqMatchState[$match['state']];
    	$liveData['match']['hs1'] = $match['hs1'];
    	$liveData['match']['hs2'] = $match['hs2'];
    	$liveData['match']['hs3'] = $match['hs3'];
    	$liveData['match']['hs4'] = $match['hs4'];
    	$liveData['match']['hot'] = $match['hot'];
    	$liveData['match']['as1'] = $match['as1'];
    	$liveData['match']['as2'] = $match['as2'];
    	$liveData['match']['as3'] = $match['as3'];
    	$liveData['match']['as4'] = $match['as4'];
    	$liveData['match']['aot'] = $match['aot'];
    	$liveData['match']['hqt'] = $match['hqt'];
    	$liveData['match']['aqt'] = $match['aqt'];
    	
    	//技术统计
    	$statistics = $this->Match->getLqStatistics($mid);
    	if($statistics)
    	{
    		foreach ($statistics as $val)
    		{
    			if($val['type'] == 1)
    			{
    				$liveData['total']['home'] = $val;
    			}
    			if($val['type'] == 2)
    			{
    				$liveData['total']['away'] = $val;
    			}
    		}
    	}
    	//预计阵容
    	$players = $this->Match->getLqPlayer($mid);
    	$liveData['player']['home'] = array();
    	$liveData['player']['away'] = array();
    	if($players)
    	{
    		foreach ($players as $val)
    		{
    			if($val['type'] == 1)
    			{
    				$liveData['player']['home'][] = $val;
    			}
    			if($val['type'] == 2)
    			{
    				$liveData['player']['away'][] = $val;
    			}
    		}
    	}
    	
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $liveData
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 足球比赛详情直播页面
     */
    public function getZqMatchLive()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}

    	//基本信息
    	$match = $this->Match->getZqMatchDetail($mid);
    	if(empty($match))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$liveData = array();
    	$liveData['match']['ln'] = $match['ln'];
    	$liveData['match']['home'] = $match['home'];
    	$liveData['match']['away'] = $match['away'];
    	$liveData['match']['mtime'] = $match['mtime'];
    	$liveData['match']['stime'] = $match['stime'];
    	$liveData['match']['ctime'] = date('Y-m-d H:i:s');
    	$liveData['match']['week'] = $this->getWeekName(date('w', strtotime($liveData['match']['mtime'])));
    	$liveData['match']['state'] = $match['state'];
    	$liveData['match']['stateMsg'] = $this->zqMatchState[$match['state']];
    	$liveData['match']['bc'] = $match['bc'];
    	$liveData['match']['hqt'] = $match['hqt'];
    	$liveData['match']['aqt'] = $match['aqt'];
    	 
    	//技术统计
    	$statistics = $this->Match->getZqStatistics($mid);
    	if($statistics)
    	{
    		$liveData['event'] = json_decode($statistics['event'], true);
    		$liveData['total'] = json_decode($statistics['total'], true);
    		if($match['exflag'] > 0)
    		{
    		    //标识大于0  需要主客队颠倒
    		    $event = array();
    		    foreach ($liveData['event'] as $value)
    		    {
    		        $data = array();
    		        foreach ($value as $key => $val)
    		        {
    		            $newKey = $key;
    		            if(strpos($key, 'la') !== false)
    		            {
    		                $newKey = str_replace('la', 'lb', $key);
    		                
    		            }
    		            if (strpos($key, 'lb') !== false)
    		            {
    		                $newKey = str_replace('lb', 'la', $key);
    		            }
    		            
    		            $data[$newKey] = $val;
    		            
    		        }
    		        $event[] = $data;
    		    }
    		    $liveData['event'] = $event;
    		    foreach ($liveData['total'] as $key => $val)
    		    {
    		        $va = explode(',', $val);
    		        $liveData['total'][$key] = $va['1'] . ',' . $va['0'];
    		    }
    		}
    	}
    	else
    	{
    		$liveData['event'] = array();
    		$liveData['total'] = array(
    			"smcu" 		=> ",",
                "szqmcs"	=> ",",
            	"fgcs"		=> ",",
            	"jqcs"		=> ",",
            	"ywcs"		=> ",",
            	"hps"		=> ",",
            	"rcard"		=> ",",
            	"kqsj"		=> ",",
            	"jq"		=>","
    		);
    	}
    	
    	//预计阵容
    	$players = $this->Match->getZqPlayer($mid);
    	$liveData['player']['home'] = array();
    	$liveData['player']['away'] = array();
    	if($players)
    	{
    		foreach ($players as $val)
    		{
    			if($val['type'] == 1)
    			{
    				$liveData['player']['home'][] = $val;
    			}
    			if($val['type'] == 2)
    			{
    				$liveData['player']['away'][] = $val;
    			}
    		}
    		if($match['exflag'] > 0)
    		{
    		    //标识大于0  需要主客队颠倒
    		    $home = $liveData['player']['away'];
    		    $liveData['player']['away'] = $liveData['player']['home'];
    		    $liveData['player']['home'] = $home;
    		}
    	}
    	 
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $liveData
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 对阵详情 - 分析
     */
    public function getLqMatchAnaly()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    		
    		$this->ajaxResult($result);
    	}
    	
    	// 获取比赛详情
    	$matchInfo = $this->Match->getLqMatchDetail($mid);
    	
    	if(empty($matchInfo))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	
    	// 比赛信息
    	$matchDetail = array(
    		'mid' => $matchInfo['mid'],
    		'home' => $matchInfo['home'],
    		'away' => $matchInfo['away'],
    		'htid' => $matchInfo['htid'],
    		'atid' => $matchInfo['atid'],
    		'lid' => $matchInfo['lid'],
    		'ln' => $matchInfo['ln'],
    		'state' => $matchInfo['state'],
    	);
    	
    	// 历史交锋
    	$historyData = $this->Match->getLqHistoryMatch($matchInfo);
    	// 获取联赛赛程赛果
    	$rankData = $this->Match->getLqScoreRank($matchInfo['sid']);
    	$rankScore = array(
    		'detail' => array(),
    	);
    	foreach ($rankData as $val)
    	{
    		if(in_array($val['tid'], array($matchInfo['htid'], $matchInfo['atid'])))
    		{
    			$rankScore['detail'][] = $val;
    		}
    	}
    	// 近期战绩
    	$recentData = $this->Match->getLqLastMatch($matchInfo);
    	
    	// 未来比赛
    	$futureData = $this->Match->getLqFutureMatch($matchInfo);
    	foreach ($futureData as $key => $value)
    	{
    		foreach ($value as $k => $val)
    		{
    			$futureData[$key][$k]['timeMsg'] = $this->getFutureTime($val['mtime'], $matchInfo['mtime']);
    		}
    	}
    	
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'matchInfo' => $matchDetail,
    			'historyMatch' => $historyData,
    			'rankScore' => $rankScore,
    			'recentMatch' => $recentData,
    			'futureMatch' => $futureData
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 足球对阵详情 - 分析
     */
    public function getZqMatchAnaly()
    {
    	$mid = $this->input->get("mid", true);
    	if(empty($mid))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '参数错误',
    			'data' => ''
    		);
    
    		$this->ajaxResult($result);
    	}
    	 
    	// 获取比赛详情
    	$matchInfo = $this->Match->getZqMatchDetail($mid);
    	 
    	if(empty($matchInfo))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	 
    	// 比赛信息
    	$matchDetail = array(
    		'mid' => $matchInfo['mid'],
    		'home' => $matchInfo['home'],
    		'away' => $matchInfo['away'],
    		'htid' => $matchInfo['htid'],
    		'atid' => $matchInfo['atid'],
    		'lid' => $matchInfo['lid'],
    		'ln' => $matchInfo['ln'],
    		'state' => $matchInfo['state'],
    	);
    	 
    	// 历史交锋
    	$historyData = $this->Match->getZqHistoryMatch($matchInfo);
    	// 获取联赛赛积分榜
    	$rankData = $this->manageScoreRank($matchInfo);
    	// 近期战绩
    	$recentData = $this->Match->getZqLastMatch($matchInfo);
    	 
    	// 未来比赛
    	$futureData = $this->Match->getZqFutureMatch($matchInfo);
    	foreach ($futureData as $key => $value)
    	{
    		foreach ($value as $k => $val)
    		{
    			$futureData[$key][$k]['timeMsg'] = $this->getFutureTime($val['mtime'], $matchInfo['mtime']);
    		}
    	}
    	 
    	$result = array(
    			'status' => '1',
    			'msg' => 'succ',
    			'data' => array(
    				'matchInfo' => $matchDetail,
    				'historyMatch' => $historyData,
    				'rankScore' => $rankData,
    				'recentMatch' => $recentData,
    				'futureMatch' => $futureData
    			)
    	);

    	$this->ajaxResult($result);
    }
    
    /*
     * 赛事 - 分析 - 积分
    * @date:2016-04-18
    */
    public function manageScoreRank($matchInfo)
    {
    	$rankData = array(
    		'statistic' => '',
    		'detail' => array()
    	);
    
    	$data = $this->Match->getZqScoreRank($matchInfo['sid']);
    	if($data)
    	{
    		$type = $data['0']['type'];
    		$rankData = array(
    			'statistic' => array(
    				'lid' => $matchInfo['lid'],
    				'sid' => $matchInfo['sid'],
    				'ln' => $matchInfo['ln'],
    				'type' => $type
    			),
    			'detail' => array()
    		);
    		// 联赛展示当前两场 杯赛展示小组赛
    		if($type == 'league')
    		{
    			foreach ($data as $key => $items)
    			{
    				if(in_array($items['tid'], array($matchInfo['htid'], $matchInfo['atid'])))
    				{
    					$items['rank'] = $key + 1;
    					$rankData['detail'][] = $items;
    					$rankGroup[$items['tid']] = $items;
    				}
    			}
    		}
    		if($type == 'cup')
    		{
    			$rankArray = array();
    			$rankGroup = array();
    			foreach ($data as $key => $items)
    			{
    				$items['rank'] = $key + 1;
    				$rankArray[$items['grouping']][] = $items;
    				$rankGroup[$items['tid']] = $items;
    			}
    		
    			if($rankGroup[$matchInfo['htid']]['grouping'] == $rankGroup[$matchInfo['atid']]['grouping'])
    			{
    			    $rankData['detail'] = $rankArray[$rankGroup[$matchInfo['htid']]['grouping']] ? $rankArray[$rankGroup[$matchInfo['htid']]['grouping']] : array();
    				if(!empty($rankData['detail']))
    				{
    					foreach ($rankData['detail'] as $key => $items)
    					{
    						$items['rank'] = (string)($key + 1);
    						$rankData['detail'][$key] = $items;
    					}
    				}
    				$rankData['statistic']['ln'] = $matchInfo['ln'] . $rankGroup[$matchInfo['htid']]['grouping'] . '组';
    			}
    		}
    	}

    	return $rankData;
    }
    
    /**
     * 篮球欧亚赔列表
     */
    public function getLqOddList()
    {
    	$mid = $this->input->get("mid", true);
    	$type = $this->input->get("type", true);
    	if(empty($mid) || (!in_array($type, array('o', 'y'))))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => '参数错误',
    			'data' => array()
    		);
    		$this->ajaxResult($result);
    	}
    	$redisKey = "{$this->redis['ODD_LIST']}_getLqOddList_{$mid}_{$type}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		$oddData = $redisData;
    	}
    	else
    	{
    		// 开赔公司赔率列表
    		$oddArray = $this->Match->getOddList($type, $mid, 43);
    		// 主流开赔公司
    		$arteryOddArray = $this->Match->getArteryList(43);
            // 新增非主流开赔公司
            $arteryOddArray = $this->mergeArteryOdd($arteryOddArray, $type, 43);

    		$oddData = array();
    		if($oddArray)
    		{
    			$companies = array();
    			foreach ($oddArray as $val)
    			{
    				$companies[$val['cid']] = $val;
    			}
    		
    			foreach ($arteryOddArray as $value)
    			{
    				if($companies[$value['cid']])
    				{
    					if($type == 'o')
    					{
    						$data = array();
    						$data['cid'] = $value['cid'];
    						$data['cname'] = $value['cname'];
    						$data['olite'] = $companies[$value['cid']]['ioa'] . ',' . $companies[$value['cid']]['ioh'];
    						$data['clite'] = $companies[$value['cid']]['oa'] . ',' . $companies[$value['cid']]['oh'];
    						$oddData[] = $data;
    					}
    					else
    					{
    						$data = array();
    						$data['cid'] = $value['cid'];
    						$data['cname'] = $value['cname'];
    						$data['olite'] = $companies[$value['cid']]['ibe'] . ',' . $companies[$value['cid']]['ibets'] . ',' . $companies[$value['cid']]['iab'];
    						$data['clite'] = $companies[$value['cid']]['be']  . ',' . $companies[$value['cid']]['bets'] . ',' . $companies[$value['cid']]['ab'];
    						$oddData[] = $data;
    					}
    				}
    			}
    		}
    		$this->setRedisData($redisKey, $oddData, 10);
    	}
    	
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $oddData
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 足球欧亚赔列表
     */
    public function getZqOddList()
    {
    	$mid = $this->input->get("mid", true);
    	$type = $this->input->get("type", true);
    	if(empty($mid) || (!in_array($type, array('o', 'y'))))
    	{
    		$result = array(
    			'status' => '1',
    			'msg' => '参数错误',
    			'data' => array()
    		);
    		$this->ajaxResult($result);
    	}
    	$redisKey = "{$this->redis['ODD_LIST']}_getZqOddList_{$mid}_{$type}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		$oddData = $redisData;
    	}
    	else
    	{
    		//开赔公司赔率列表
    		$oddArray = $this->Match->getOddList($type, $mid, 42);
    		//主流开赔公司
    		$arteryOddArray = $this->Match->getArteryList(42);
            // 新增非主流开赔公司
            $arteryOddArray = $this->mergeArteryOdd($arteryOddArray, $type, 42);

    		$oddData = array();
    		if($oddArray)
    		{
    		    $match = $this->Match->getZqMatchDetail($mid);
    			$companies = array();
    			foreach ($oddArray as $val)
    			{
    				$companies[$val['cid']] = $val['lite'];
    			}
    			$k = 0;
    			foreach ($arteryOddArray as $value)
    			{
    				if($companies[$value['cid']])
    				{
    					if($type == 'o')
    					{
    						$oddData[$k]['cid'] = $value['cid'];
    						$oddData[$k]['cname'] = $value['cname'];
    						$lite = $this->getLite($companies[$value['cid']]);
    						if($lite[0])
    						{
    							$olite = explode(',', $lite[0]);
    							$oddData[$k]['olite'] = $olite[0] . ',' . $olite[1] . ',' . $olite[2];
    							if($value['cid'] != '10000' && $match['exflag'] > 0)
    							{
    							    //主客队赔率反转
    							    $oddData[$k]['olite'] = $olite[2] . ',' . $olite[1] . ',' . $olite[0];
    							}
    						}
    						else
    						{
    							$oddData[$k]['olite'] = '';
    						}
    						if($lite[1])
    						{
    							$clite = explode(',', $lite[1]);
    							$oddData[$k]['clite'] = $clite[0] . ',' . $clite[1] . ',' . $clite[2];
    							if($value['cid'] != '10000' && $match['exflag'] > 0)
    							{
    							    //主客队赔率反转
    							    $oddData[$k]['clite'] = $clite[2] . ',' . $clite[1] . ',' . $clite[0];
    							}
    						}
    						else
    						{
    							$oddData[$k]['clite'] = '';
    						}
    					}
    					else
    					{
    						$oddData[$k]['cid'] = $value['cid'];
    						$oddData[$k]['cname'] = $value['cname'];
    						$lite = $this->getLite($companies[$value['cid']]);
    						if($lite[0])
    						{
    							$olite = explode(',', $lite[0]);
    							$oddData[$k]['olite'] = $olite[0] . ',' . $olite[1] . ',' . $olite[2];
    							if($match['exflag'] > 0)
    							{
    							    $olite[1] = (strpos($olite[1], '受') !== false) ? str_replace('受', '', $olite[1]) : ($olite[1] == '平手' ? $olite[1] : '受' . $olite[1]);
    							    $oddData[$k]['olite'] = $olite[2] . ',' . $olite[1] . ',' . $olite[0];
    							}
    						}
    						else
    						{
    							$oddData[$k]['olite'] = '';
    						}
    						if($lite[1])
    						{
    							$clite = explode(',', $lite[1]);
    							$oddData[$k]['clite'] = $clite[0] . ',' . $clite[1] . ',' . $clite[2];
    							if($match['exflag'] > 0)
    							{
    							    $clite[1] = (strpos($clite[1], '受') !== false) ? str_replace('受', '', $clite[1]) : ($clite[1] == '平手' ? $clite[1] : '受' . $clite[1]);
    							    $oddData[$k]['clite'] = $clite[2] . ',' . $clite[1] . ',' . $clite[0];
    							}
    						}
    						else
    						{
    							$oddData[$k]['clite'] = '';
    						}
    					}
    					$k++;
    				}
    			}
    		}
    		$this->setRedisData($redisKey, $oddData, 10);
    	}

    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $oddData
    	);

    	$this->ajaxResult($result);
    }
    
    /*
     * 赛事 - 赔率处理
    * @date:2016-04-18
    */
    private function getLite($lite)
    {
    	$liteData = array();
    	if(!empty($lite))
    	{
    		$liteData = explode('|', $lite);
    	}
    	return $liteData;
    }
    
    /*
     * 赛事 - 欧赔亚赔 - 详情
    * @date:2016-04-18
    */
    public function getLqOddDetail()
    {
    	$mid = $this->input->get("mid", true);
    	$type = $this->input->get("type", true);
    	$cid = $this->input->get("cid", true);
    
    	if(empty($mid) || empty($cid) || !in_array($type, array('o', 'y')))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少赛事标识',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$redisKey = "{$this->redis['ODD_DETAIL']}_getLqOddDetail_{$mid}_{$type}_{$cid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		$oddData = $redisData;
    	}
    	else
    	{
    		$oddArray = $this->Match->getOddDetail($type, $cid, $mid, 43);
    		$oddData = array();
    		if(!empty($oddArray))
    		{
    			$match = $this->Match->getLqMatchDetail($mid);
    			if($type == 'o')
    			{
    				foreach ($oddArray as $value)
    				{
    					$data = array();
    					$data['oh'] = $value['oh'];
    					$data['oa'] = $value['oa'];
    					if($value['tp'])
    					{
    						$data['timeMsg'] = $this->calCurTime(strtotime($match['mtime']), $value['time']);
    					}
    					else
    					{
    						$data['timeMsg'] = '初盘';
    					}
    					$oddData[] = $data;
    				}
    			}
    			else
    			{
    				foreach ($oddArray as $value)
    				{
    					$data = array();
    					$data['oh'] = $value['ab'];
    					$data['bets'] = $value['bets'];
    					$data['oa'] = $value['be'];
    					if($value['tp'])
    					{
    						$data['timeMsg'] = $this->calCurTime(strtotime($match['mtime']), $value['time']);
    					}
    					else
    					{
    						$data['timeMsg'] = '初盘';
    					}
    					$oddData[] = $data;
    				}
    			}
    		
    		}
    		$this->setRedisData($redisKey, $oddData, 10);
    	}
    
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $oddData
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /*
     * 赛事 - 欧赔亚赔 - 详情
    * @date:2016-04-18
    */
    public function getZqOddDetail()
    {
    	$mid = $this->input->get("mid", true);
    	$type = $this->input->get("type", true);
    	$cid = $this->input->get("cid", true);
    
    	if(empty($mid) || empty($cid) || !in_array($type, array('o', 'y')))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少赛事标识',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$redisKey = "{$this->redis['ODD_DETAIL']}_getZqOddDetail_{$mid}_{$type}_{$cid}";
    	$redisData = $this->getRedisData($redisKey);
    	if($redisData)
    	{
    		$oddData = $redisData;
    	}
    	else
    	{
    		$oddArray = $this->Match->getOddDetail($type, $cid, $mid, 42);
    		$oddData = array();
    		if(!empty($oddArray))
    		{
    			$match = $this->Match->getZqMatchDetail($mid);
    			$count = count($oddArray);
    			if($type == 'o')
    			{
    				foreach ($oddArray as $key => $value)
    				{
    					$data = array();
    					$data['oh'] = $value['oh'];
    					$data['od'] = $value['od'];
    					$data['oa'] = $value['oa'];
    					if($match['exflag'] > 0 && $cid != '10000')
    					{
    					    $data['oh'] = $value['oa'];
    					    $data['oa'] = $value['oh'];
    					}
    					if($count == $key + 1)
    					{
    						$data['timeMsg'] = '初盘';
    					}
    					else
    					{
    						$data['timeMsg'] = $this->calCurTime(strtotime($match['mtime']), $value['time']);
    					}
    					$oddData[] = $data;
    				}
    			}
    			else
    			{
    				foreach ($oddArray as $key => $value)
    				{
    					$data = array();
    					$data['oh'] = $value['ab'];
    					$data['bets'] = $value['bet'];
    					$data['oa'] = $value['be'];
    					if($match['exflag'] > 0)
    					{
    					    $data['oh'] = $value['be'];
    					    $data['oa'] = $value['ab'];
    					    $value['bet'] = (strpos($value['bet'], '受') !== false) ? str_replace('受', '', $value['bet']) : ($value['bet'] == '平手' ? $value['bet'] : '受' . $value['bet']);
    					    $data['bets'] = $value['bet'];
    					}
    					if($count == $key + 1)
    					{
    						$data['timeMsg'] = '初盘';
    					}
    					else
    					{
    						$data['timeMsg'] = $this->calCurTime(strtotime($match['mtime']), $value['time']);
    					}
    					$oddData[] = $data;
    				}
    			}
    		
    		}
    		$this->setRedisData($redisKey, $oddData, 10);
    	}
    
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => $oddData
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 篮球直播即时列表
     */
    public function lqLiveList()
    {
    	$matchs = $this->Match->getLqMatchList();
    	$matchInfo = array();
    	foreach ($matchs as $match)
    	{
    		$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    		$match['stateMsg'] = $this->lqMatchState[$match['state']];
    		$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
            $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
            $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    		$matchInfo[] = $match;
    	}
    	$msgId = $this->Match->getLqNewMessageId();
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $msgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 足球直播即时列表
     */
    public function zqLiveList()
    {
    	$matchs = $this->Match->getZqMatchList();
    	$matchInfo = array();
    	foreach ($matchs as $match)
    	{
    		$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    		$match['stateMsg'] = $this->zqMatchState[$match['state']];
    		$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
    		$match['nameType'] = $this->getNameType($match['lid']);
            $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
            $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    		$matchInfo[] = $match;
    	}
    	$msgId = $this->Match->getZqNewMessageId();
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $msgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 篮球增量刷新接口
     */
    public function lqLiveScore()
    {
    	$msgId = $this->input->get("msgId", true);	// 消息编号
    	if(empty($msgId))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少必要标识',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$newMsgId = $this->Match->getLqNewMessageId();
    	// 检查消息间隔最大值
    	if($newMsgId - $msgId > 20)
    	{
    		$result = array(
    			'status' => '2',
    			'msg' => '消息列表id超过最大值',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$result = $this->Match->getLqLiveScore($newMsgId, $msgId);
    	$liveData = array();
    	foreach ($result as $mid => $value)
    	{
    		$data = array();
    		$data['mid'] = $value['mid'];
    		$data['state'] = $value['state'];
    		$data['stateMsg'] = $this->lqMatchState[$value['state']];
    		$data['hqt'] = $value['hqt'];
    		$data['aqt'] = $value['aqt'];
    		$data['stime'] = $value['stime'];
    		$liveData[] = $data;
    	}
    	
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $newMsgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'liveData' => $liveData
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 足球增量刷新接口
     */
    public function zqLiveScore()
    {
    	$msgId = $this->input->get("msgId", true);	// 消息编号
    	if(empty($msgId))
    	{
    		$result = array(
    			'status' => '0',
    			'msg' => '缺少必要标识',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$newMsgId = $this->Match->getZqNewMessageId();
    	// 检查消息间隔最大值
    	if($newMsgId - $msgId > 20)
    	{
    		$result = array(
    			'status' => '2',
    			'msg' => '消息列表id超过最大值',
    			'data' => ''
    		);
    		$this->ajaxResult($result);
    	}
    	$result = $this->Match->getZqLiveScore($newMsgId, $msgId);
    	$liveData = array();
    	foreach ($result as $mid => $value)
    	{
    		$data = array();
    		$data['mid'] = $value['mid'];
    		$data['state'] = $value['state'];
    		$data['stateMsg'] = $this->zqMatchState[$value['state']];
    		$data['hqt'] = $value['hqt'];
    		$data['aqt'] = $value['aqt'];
    		$data['stime'] = $value['stime'];
    		$liveData[] = $data;
    	}
    	 
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $newMsgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'liveData' => $liveData
    		)
    	);

    	$this->ajaxResult($result);
    }
    
    /**
     * 完结列表  篮球
     */
    public function lqEndList()
    {
        $headerInfo = $this->getRequestHeaders();
    	$matchs = $this->Match->getLqEndList();
    	$matchInfo = array();
    	foreach ($matchs as $match)
    	{
    		$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    		$match['stateMsg'] = $this->lqMatchState[$match['state']];
    		$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
            $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
            $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    		// 针对安卓3.8版本主客队字段取错处理
            if($headerInfo['appVersionCode'] == 30800)
            {
                $hqt = $match['hqt'] ? $match['hqt'] : 0;
                $match['hqt'] = $match['aqt'];
                $match['aqt'] = $hqt;
            }
            $matchInfo[] = $match;   
    	}
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 完结列表  足球
     */
    public function zqEndList()
    {
    	
    	$matchs = $this->Match->getZqEndList();
    	$matchInfo = array();
    	foreach ($matchs as $match)
    	{
    		$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    		$match['stateMsg'] = $this->zqMatchState[$match['state']];
    		$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
    		$match['nameType'] = $this->getNameType($match['lid']);
            $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
            $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    		$matchInfo[] = $match;
    	}
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);

    	$this->ajaxResult($result);
    }
    
    public function getLqFollow()
    {
    	$mids = $this->input->get("mids", true);
    	$matchInfo = array();
    	$mids = explode(',', $mids);
    	if($mids)
    	{
    		$matchs = $this->Match->getLqFollow($mids);
    		foreach ($matchs as $match)
    		{
    			$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    			$match['stateMsg'] = $this->lqMatchState[$match['state']];
    			$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
                $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
                $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    			$matchInfo[] = $match;
    		}
    	}
    	$msgId = $this->Match->getLqNewMessageId();
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $msgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 足球关注列表
     */
    public function getZqFollow()
    {
    	$mids = $this->input->get("mids", true);
    	$matchInfo = array();
    	$mids = explode(',', $mids);
    	if($mids)
    	{
    		$matchs = $this->Match->getZqFollow($mids);
    		foreach ($matchs as $match)
    		{
    			$match['issue'] = date('Y-m-d', strtotime('20' . substr($match['xid'], 0, -3)));
    			$match['stateMsg'] = $this->zqMatchState[$match['state']];
    			$match['week'] = $this->getWeekName(date('w', strtotime($match['issue'])));
    			$match['nameType'] = $this->getNameType($match['lid']);
                $match['homelogo'] = $match['homelogo'] ? $match['homelogo'] : '';
                $match['awaylogo'] = $match['awaylogo'] ? $match['awaylogo'] : '';
    			$matchInfo[] = $match;
    		}
    	}
    	$msgId = $this->Match->getZqNewMessageId();
    	$result = array(
    		'status' => '1',
    		'msg' => 'succ',
    		'data' => array(
    			'msgId' => $msgId,
    			'ctime' => date('Y-m-d H:i:s'),
    			'matchInfo' => $matchInfo
    		)
    	);
    	
    	$this->ajaxResult($result);
    }
    
    /**
     * 打印json数据，并终止程序
     * @param array $result
     */
    private function ajaxResult($result)
    {
    	header('Content-type: application/json');
    	die(json_encode($result));
    }
    
    /**
     * 返回日期的中文信息
     * @param unknown_type $week
     */
    private function getWeekName($week)
    {
    	$weekDays = array('周日', '周一', '周二', '周三', '周四', '周五', '周六');
    	return $weekDays[$week];
    }
    
    /*
     * 赛事 - 分析 - 未来比赛 相隔处理
    * @date:2016-04-18
    */
    public function getFutureTime($mtime, $ctime)
    {
    	// 开赛时间
    	$time = strtotime($mtime);
    	// 当前时间
    	$now = strtotime($ctime);
    
    	$timeDif = $time - $now;
    
    	$msg = '';
    	if($timeDif > 0)
    	{
    		$remain = $timeDif%86400;
    		$difH = intval($remain/3600);
    		$difM = intval(($remain%3600)/60);
    
    		$day = $timeDif/86400;
    		if(intval($day) > 0)
    		{
    			$msg .= intval($day) . '天后';
    		}
    		else
    		{
    			if($difH > 0)
    			{
    				$msg .= $difH . "小时后";
    			}
    		}
    	}
    	else
    	{
    		$msg .= '1小时内';
    	}
    	return $msg;
    }
    
    /*
     * 赛事 - 欧赔亚赔 - 距离时间
    * @date:2016-04-18
    */
    public function calCurTime($mtime, $time)
    {
    	if(empty($time))
    	{
    		return '赛前1分钟';
    	}
    
    	$timeDif = $mtime - $time;
    
    	$msg = '赛前';
    	if($timeDif > 0)
    	{
    		$remain = $timeDif%86400;
    		$difH = intval($remain/3600);
    		$difM = intval(($remain%3600)/60);
    
    		$day = $timeDif/86400;
    		if(intval($day) > 0)
    		{
    			$msg .= intval($day) . '天';
    			if($difH > 0)
    			{
    				$msg .= $difH . "小时";
    			}
    		}
    		else
    		{
    			if($difH > 0)
    			{
    				$msg .= $difH . "小时";
    			}
    
    			if($difM > 0)
    			{
    				$msg .= $difM . "分钟";
    			}
    		}
    	}
    	else
    	{
    		$msg .= '1分钟';
    	}
    	
    	return $msg;
    }
    
    /*
     * 获取 五大联赛标识
    * @date:2016-04-22
    */
    private function getNameType($lid)
    {
    	$type = '0';
    	if(in_array($lid, array('34', '92', '85', '39', '93')))
    	{
    		$type = '1';
    	}
    	return $type;
    }
    
    /**
     * 获取缓存数据
     */
    private function getRedisData($redisKey)
    {
    	$this->load->driver('cache', array('adapter' => 'redis', 'dbname' => 'slave'), 'cacheSlave');
    	$resData = $this->cacheSlave->get($redisKey);
    	return json_decode($resData, true);
    }
    
    /**
     * 保存缓存数据
     */
    private function setRedisData($redisKey, $value = array(), $lifeTime)
    {
    	$this->cache->save($redisKey, json_encode($value), $lifeTime);
    }

    // 新增欧亚赔
    public function mergeArteryOdd($arteryOddArray = array(), $type = 'o', $lid = 0)
    {
        $extra = array(
            '42'    =>  array(
                'o' =>  array(
                    0   =>  array(
                        'cid'   =>  '10160',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'SNAI.it',
                    ),
                    1   =>  array(
                        'cid'   =>  '10151',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Bet-52',
                    ),
                    2   =>  array(
                        'cid'   =>  '10084',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Bet-at-home.uk',
                    ),
                    3   =>  array(
                        'cid'   =>  '10047',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'CBCX',
                    ),
                    4   =>  array(
                        'cid'   =>  '10045',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Sportium',
                    ),
                    5   =>  array(
                        'cid'   =>  '10042',
                        'reg'   =>  '菲律宾',
                        'zflag' =>  '0',   
                        'cname' =>  'UEDBET',
                    ),
                    6   =>  array(
                        'cid'   =>  '10036',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'BetVictor',
                    ),
                    7   =>  array(
                        'cid'   =>  '10008',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Mobibet',
                    ),
                    8   =>  array(
                        'cid'   =>  '10009',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Netbet.fr',
                    ),
                    9   =>  array(
                        'cid'   =>  '10013',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Betfair SB',
                    ),
                ),
                'y' =>  array(
                    0   =>  array(
                        'cid'   =>  '893',
                        'reg'   =>  '直布罗陀',
                        'zflag' =>  '0',   
                        'cname' =>  '伟德',
                    ),
                    1   =>  array(
                        'cid'   =>  '583',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'as3388',
                    ),
                    2   =>  array(
                        'cid'   =>  '566',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  '盈禾',
                    ),
                    3   =>  array(
                        'cid'   =>  '10164',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'ManbetX',
                    ),
                    4   =>  array(
                        'cid'   =>  '10001',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  '18Bet',
                    ),
                    5   =>  array(
                        'cid'   =>  '204',
                        'reg'   =>  '菲律宾',
                        'zflag' =>  '0',   
                        'cname' =>  '12BET',
                    ),
                    6   =>  array(
                        'cid'   =>  '2',
                        'reg'   =>  '英国',
                        'zflag' =>  '0',   
                        'cname' =>  '10BET',
                    ),
                    7   =>  array(
                        'cid'   =>  '450',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  '明陞',
                    ),
                    8   =>  array(
                        'cid'   =>  '310',
                        'reg'   =>  '菲律宾',
                        'zflag' =>  '0',   
                        'cname' =>  '利记',
                    ),
                ),
            ),
            '43'    =>  array(
                'o' =>  array(
                    0   =>  array(
                        'cid'   =>  '724',
                        'reg'   =>  '菲律宾',
                        'zflag' =>  '0',   
                        'cname' =>  'Dafabet',
                    ),
                    1   =>  array(
                        'cid'   =>  '726',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Marathon',
                    ),
                    2   =>  array(
                        'cid'   =>  '706',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  '1Bet',
                    ),
                    3   =>  array(
                        'cid'   =>  '893',
                        'reg'   =>  '直布罗陀',
                        'zflag' =>  '0',   
                        'cname' =>  '伟德',
                    ),
                    4   =>  array(
                        'cid'   =>  '757',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'ComeOn',
                    ),
                    5   =>  array(
                        'cid'   =>  '770',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Smarkets',
                    ),
                    6   =>  array(
                        'cid'   =>  '543',
                        'reg'   =>  '英属维尔京群岛',
                        'zflag' =>  '0',   
                        'cname' =>  'Titanbet',
                    ),
                    7   =>  array(
                        'cid'   =>  '675',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'Island Casino',
                    ),
                    8   =>  array(
                        'cid'   =>  '681',
                        'reg'   =>  '',
                        'zflag' =>  '0',   
                        'cname' =>  'youwin',
                    ),
                ),
                'y' =>  array(
                    0   =>  array(
                        'cid'   =>  '204',
                        'reg'   =>  '菲律宾',
                        'zflag' =>  '0',   
                        'cname' =>  '12BET',
                    ),
                    1   =>  array(
                        'cid'   =>  '2',
                        'reg'   =>  '英国',
                        'zflag' =>  '0',   
                        'cname' =>  '10BET',
                    ),
                ),
            ),
        );
        if(!empty($arteryOddArray) && !empty($extra[$lid][$type]))
        {
            $OddArray = array();
            foreach ($arteryOddArray as $items) 
            {
                $OddArray[$items['cid']] = $items;
            }

            foreach ($extra[$lid][$type] as $items) 
            {
                if(empty($OddArray[$items['cid']]))
                {
                    array_push($arteryOddArray, $items);
                }
            }
        }
        return $arteryOddArray;
    }
}
