<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cache_Model extends MY_Model {

	private $lidMap = array(
			'51' => array('method' => 'refreshByLid', 'table' => 'cp_ssq_paiqi', 'cache' => 'SSQ_ISSUE', 'issuePrefix' => '', 'name' => '双色球'),
			'52' => array('method' => 'refreshByLid', 'table' => 'cp_fc3d_paiqi', 'cache' => 'FC3D_ISSUE', 'issuePrefix' => '', 'name' => '福彩3D'),
			'33' => array('method' => 'refreshByLid', 'table' => 'cp_pl3_paiqi', 'cache' => 'PLS_ISSUE', 'issuePrefix' => '20', 'name' => '排列三'),
			'35' => array('method' => 'refreshByLid', 'table' => 'cp_pl5_paiqi', 'cache' => 'PLW_ISSUE', 'issuePrefix' => '20', 'name' => '排列五'),
			'10022' => array('method' => 'refreshByLid', 'table' => 'cp_qxc_paiqi', 'cache' => 'QXC_ISSUE', 'issuePrefix' => '20', 'name' => '七星彩'),
			'23528' => array('method' => 'refreshByLid', 'table' => 'cp_qlc_paiqi', 'cache' => 'QLC_ISSUE', 'issuePrefix' => '', 'name' => '七乐彩'),
			'23529' => array('method' => 'refreshByLid', 'table' => 'cp_dlt_paiqi', 'cache' => 'DLT_ISSUE', 'issuePrefix' => '20', 'name' => '大乐透'),
			'11' => array('method' => '', 'table' => 'cp_tczq_paiqi', 'cache' => 'SFC_ISSUE', 'cacheNew' => 'SFC_ISSUE_NEW', 'issuePrefix' => '20', 'name' => '胜负彩'),
			'19' => array('method' => '', 'table' => 'cp_tczq_paiqi', 'cache' => 'RJ_ISSUE', 'cacheNew' => 'RJ_ISSUE_NEW', 'issuePrefix' => '20', 'name' => '任九'),
			'21406' => array('method' => 'refreshFastLottery', 'table' => 'cp_syxw_paiqi', 'cache' => 'SYXW_ISSUE_TZ', 'issuePrefix' => '', 'name' => '老11选5'), //老11选5
			'21407' => array('method' => 'refreshFastLottery', 'table' => 'cp_jxsyxw_paiqi', 'cache' => 'JXSYXW_ISSUE_TZ', 'issuePrefix' => '', 'name' => '新11选5'),
			'21408' => array('method' => 'refreshFastLottery', 'table' => 'cp_hbsyxw_paiqi', 'cache' => 'HBSYXW_ISSUE_TZ', 'issuePrefix' => '', 'name' => '惊喜11选5'),
			'53' => array('method' => 'refreshFastLottery', 'table' => 'cp_ks_paiqi', 'cache' => 'KS_ISSUE_TZ', 'issuePrefix' => '', 'name' => '上海快三'),
            '56' => array('method' => 'refreshFastLottery', 'table' => 'cp_jlks_paiqi', 'cache' => 'JLKS_ISSUE_TZ', 'issuePrefix' => '', 'name' => '吉林快三'),
	        '57' => array('method' => 'refreshFastLottery', 'table' => 'cp_jxks_paiqi', 'cache' => 'JXKS_ISSUE_TZ', 'issuePrefix' => '', 'name' => '江西快三'),
			'54' => array('method' => 'refreshFastLottery', 'table' => 'cp_klpk_paiqi', 'cache' => 'KLPK_ISSUE_TZ', 'issuePrefix' => '', 'name' => '快乐扑克'),
            '55' => array('method' => 'refreshFastLottery', 'table' => 'cp_cqssc_paiqi', 'cache' => 'CQSSC_ISSUE_TZ', 'issuePrefix' => '', 'name' => '老时时彩'),
	       '21421' => array('method' => 'refreshFastLottery', 'table' => 'cp_gdsyxw_paiqi', 'cache' => 'GDSYXW_ISSUE_TZ', 'issuePrefix' => '', 'name' => '乐11选5'),
	);
	
    public function __construct() 
    {
        parent::__construct();
    }

    //刷新竞彩足球对阵缓存
    public function refreshJczqMatch()
    {
        $sql = "select p.mid,p.hot,p.hotid,p.end_sale_time,p.sale_status,p.show_end_time,z.zhisheng from cp_jczq_paiqi p left join cp_jczq_zhisheng z on p.mid=z.mid where p.show_end_time >= now()";
        $res = $this->cfgDB->query($sql)->getAll();
        $mids = array();
        $matchs = array();
        foreach ($res as $val)
        {
        	$matchs[$val['mid']] = $val;
        	$mids[] = $val['mid'];
        }
        if($mids)
        {
            $matchinfo = $this->getMatchInfo($mids, $matchs);
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['JCZQ_MATCH'], json_encode($matchinfo['data']), 0);
            if(!empty($matchinfo['rqdata'])){
                $this->saveRqs($matchinfo['rqdata']);
            }
        }
        else
        {
            $this->load->driver('cache', array('adapter' => 'redis'));
            $REDIS = $this->config->item('REDIS');
            $this->cache->save($REDIS['JCZQ_MATCH'], json_encode($mids), 0);
        }
    }

    /**
     * 更新dc排期表部分对阵rq数为0问题(华阳票商出票不提供让球数，故从我们自己的dc库去取)
     * @param unknown $rqdata
     */
    private function saveRqs($rqdata){
        $sql = 'insert cp_jczq_paiqi(mid, rq)values';
        $sdata = array();
        $bdata = array();
        foreach ($rqdata as $mid => $rq){
            array_push($sdata, '(?, ?)');
            array_push($bdata, $mid);
            array_push($bdata, $rq);
        }
        $sql .= implode(',', $sdata) . 'on duplicate key update rq = values(rq)';
        $result = $this->dc->query($sql, $bdata);
        //不需要触发同步任务 比分出来后会触发
        //$res = $this->cfgDB->query('update cp_task_manage set stop= 0 where task_type= 1 and lid= 42');
    }
    
    private function getMatchInfo($mids,$matchs)
    {
        $data = array();
        $rqdata = array();
        $midStr = implode("','", $mids);
        $sql1 = "select * from cp_jczq_match where mid in ('{$midStr}') order by end_sale_date, end_sale_time, mid";
        $result = $this->dc->query($sql1)->getAll();
        $typeMatchs = array();
        foreach ($result as $value)
        {
                $typeMatchs[$value['ctype']][$value['mid']] = $value['codes'];
                if($data[$value['mid']]) continue;

                $data[$value['mid']]['mid'] = $value['mid'];
                $data[$value['mid']]['weekId'] = $value['mname'];
                $data[$value['mid']]['name'] = $value['league'];
                $data[$value['mid']]['nameSname'] = $value['league_abbr'];
                $data[$value['mid']]['home'] = $value['home'];
                $data[$value['mid']]['homeSname'] = $value['home_abbr'];
                $data[$value['mid']]['awary'] = $value['away'];
                $data[$value['mid']]['awarySname'] = $value['away_abbr'];
                $data[$value['mid']]['dt'] = strtotime($matchs[$value['mid']]['end_sale_time'])*1000;
                $data[$value['mid']]['jzdt'] = strtotime($matchs[$value['mid']]['show_end_time'])*1000;
                $data[$value['mid']]['cl'] = '#' . $value['l_background_color'];
                $data[$value['mid']]['issue'] = $value['m_date'];
                $data[$value['mid']]['hot'] = $matchs[$value['mid']]['hot']; //添加是否热门
                $data[$value['mid']]['hotid'] = $matchs[$value['mid']]['hotid'];
                $data[$value['mid']]['zhisheng'] = $matchs[$value['mid']]['zhisheng'];
        }
        foreach ($matchs as $mid => $val)
        {
                //胜平负
                if($val['sale_status'] & 1)
                {
                        $codes = @unserialize($typeMatchs[1][$mid]);
                        $data[$mid]['spfSp3'] = $codes['h'];
                        $data[$mid]['spfSp1'] = $codes['d'];
                        $data[$mid]['spfSp0'] = $codes['a'];
                        $data[$mid]['spfGd'] = $codes ? 1 : 0;
                        $data[$mid]['spfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
                }
                else
                {
                        $data[$mid]['spfSp3'] = '';
                        $data[$mid]['spfSp1'] = '';
                        $data[$mid]['spfSp0'] = '';
                        $data[$mid]['spfGd'] = 0;
                        $data[$mid]['spfFu'] = 0;
                }
                //让球胜平负
                if($val['sale_status'] & 2)
                {
                        $codes = @unserialize($typeMatchs[2][$mid]);
                        $data[$mid]['let'] = $codes['fixedodds'];
                        //添加让球数的同步
                        $rqdata[$mid] = $codes['fixedodds'];
                        $data[$mid]['rqspfSp3'] = $codes['h'];
                        $data[$mid]['rqspfSp1'] = $codes['d'];
                        $data[$mid]['rqspfSp0'] = $codes['a'];
                        $data[$mid]['rqspfGd'] = $codes ? 1 : 0;
                        $data[$mid]['rqspfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
                }
                else
                {
                        $data[$mid]['let'] = '';
                        $data[$mid]['rqspfSp3'] = '';
                        $data[$mid]['rqspfSp1'] = '';
                        $data[$mid]['rqspfSp0'] = '';
                        $data[$mid]['rqspfGd'] = 0;
                        $data[$mid]['rqspfFu'] = 0;
                }
                //半全场
                if($val['sale_status'] & 4)
                {
                        $codes = @unserialize($typeMatchs[3][$mid]);
                        $data[$mid]['bqcSp00'] = $codes['aa'];
                        $data[$mid]['bqcSp01'] = $codes['ad'];
                        $data[$mid]['bqcSp03'] = $codes['ah'];
                        $data[$mid]['bqcSp10'] = $codes['da'];
                        $data[$mid]['bqcSp11'] = $codes['dd'];
                        $data[$mid]['bqcSp13'] = $codes['dh'];
                        $data[$mid]['bqcSp30'] = $codes['ha'];
                        $data[$mid]['bqcSp31'] = $codes['hd'];
                        $data[$mid]['bqcSp33'] = $codes['hh'];
                        $data[$mid]['bqcGd'] = $codes ? 1 : 0;
                        $data[$mid]['bqcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
                }
                else
                {
                        $codes = @unserialize($typeMatchs[3][$mid]);
                        $data[$mid]['bqcSp00'] = '';
                        $data[$mid]['bqcSp01'] = '';
                        $data[$mid]['bqcSp03'] = '';
                        $data[$mid]['bqcSp10'] = '';
                        $data[$mid]['bqcSp11'] = '';
                        $data[$mid]['bqcSp13'] = '';
                        $data[$mid]['bqcSp30'] = '';
                        $data[$mid]['bqcSp31'] = '';
                        $data[$mid]['bqcSp33'] = '';
                        $data[$mid]['bqcGd'] = 0;
                        $data[$mid]['bqcFu'] = 0;
                }
                //进球数
                if($val['sale_status'] & 8)
                {
                        $codes = @unserialize($typeMatchs[4][$mid]);
                        $data[$mid]['jqsSp0'] = $codes['s0'];
                        $data[$mid]['jqsSp1'] = $codes['s1'];
                        $data[$mid]['jqsSp2'] = $codes['s2'];
                        $data[$mid]['jqsSp3'] = $codes['s3'];
                        $data[$mid]['jqsSp4'] = $codes['s4'];
                        $data[$mid]['jqsSp5'] = $codes['s5'];
                        $data[$mid]['jqsSp6'] = $codes['s6'];
                        $data[$mid]['jqsSp7'] = $codes['s7'];
                        $data[$mid]['jqsGd'] = $codes ? 1 : 0;
                        $data[$mid]['jqsFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
                }
                else
                {
                        $data[$mid]['jqsSp0'] = '';
                        $data[$mid]['jqsSp1'] = '';
                        $data[$mid]['jqsSp2'] = '';
                        $data[$mid]['jqsSp3'] = '';
                        $data[$mid]['jqsSp4'] = '';
                        $data[$mid]['jqsSp5'] = '';
                        $data[$mid]['jqsSp6'] = '';
                        $data[$mid]['jqsSp7'] = '';
                        $data[$mid]['jqsGd'] = 0;
                        $data[$mid]['jqsFu'] = 0;
                }
                //比分
                if($val['sale_status'] & 16)
                {
                        $codes = @unserialize($typeMatchs[5][$mid]);
                        $data[$mid]['bfSp00'] = $codes['0:0'];
                        $data[$mid]['bfSp01'] = $codes['0:1'];
                        $data[$mid]['bfSp02'] = $codes['0:2'];
                        $data[$mid]['bfSp03'] = $codes['0:3'];
                        $data[$mid]['bfSp04'] = $codes['0:4'];
                        $data[$mid]['bfSp05'] = $codes['0:5'];
                        $data[$mid]['bfSp10'] = $codes['1:0'];
                        $data[$mid]['bfSp11'] = $codes['1:1'];
                        $data[$mid]['bfSp12'] = $codes['1:2'];
                        $data[$mid]['bfSp13'] = $codes['1:3'];
                        $data[$mid]['bfSp14'] = $codes['1:4'];
                        $data[$mid]['bfSp15'] = $codes['1:5'];
                        $data[$mid]['bfSp20'] = $codes['2:0'];
                        $data[$mid]['bfSp21'] = $codes['2:1'];
                        $data[$mid]['bfSp22'] = $codes['2:2'];
                        $data[$mid]['bfSp23'] = $codes['2:3'];
                        $data[$mid]['bfSp24'] = $codes['2:4'];
                        $data[$mid]['bfSp25'] = $codes['2:5'];
                        $data[$mid]['bfSp30'] = $codes['3:0'];
                        $data[$mid]['bfSp31'] = $codes['3:1'];
                        $data[$mid]['bfSp32'] = $codes['3:2'];
                        $data[$mid]['bfSp33'] = $codes['3:3'];
                        $data[$mid]['bfSp40'] = $codes['4:0'];
                        $data[$mid]['bfSp41'] = $codes['4:1'];
                        $data[$mid]['bfSp42'] = $codes['4:2'];
                        $data[$mid]['bfSp50'] = $codes['5:0'];
                        $data[$mid]['bfSp51'] = $codes['5:1'];
                        $data[$mid]['bfSp52'] = $codes['5:2'];
                        $data[$mid]['bfSp90'] = $codes['a_o'];
                        $data[$mid]['bfSp91'] = $codes['d_o'];
                        $data[$mid]['bfSp93'] = $codes['h_o'];
                        $data[$mid]['bfGd'] = $codes ? 1 : 0;
                        $data[$mid]['bfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
                }
                else
                {
                        $data[$mid]['bfSp00'] = '';
                        $data[$mid]['bfSp01'] = '';
                        $data[$mid]['bfSp02'] = '';
                        $data[$mid]['bfSp03'] = '';
                        $data[$mid]['bfSp04'] = '';
                        $data[$mid]['bfSp05'] = '';
                        $data[$mid]['bfSp10'] = '';
                        $data[$mid]['bfSp11'] = '';
                        $data[$mid]['bfSp12'] = '';
                        $data[$mid]['bfSp13'] = '';
                        $data[$mid]['bfSp14'] = '';
                        $data[$mid]['bfSp15'] = '';
                        $data[$mid]['bfSp20'] = '';
                        $data[$mid]['bfSp21'] = '';
                        $data[$mid]['bfSp22'] = '';
                        $data[$mid]['bfSp23'] = '';
                        $data[$mid]['bfSp24'] = '';
                        $data[$mid]['bfSp25'] = '';
                        $data[$mid]['bfSp30'] = '';
                        $data[$mid]['bfSp31'] = '';
                        $data[$mid]['bfSp32'] = '';
                        $data[$mid]['bfSp33'] = '';
                        $data[$mid]['bfSp40'] = '';
                        $data[$mid]['bfSp41'] = '';
                        $data[$mid]['bfSp42'] = '';
                        $data[$mid]['bfSp50'] = '';
                        $data[$mid]['bfSp51'] = '';
                        $data[$mid]['bfSp52'] = '';
                        $data[$mid]['bfSp90'] = '';
                        $data[$mid]['bfSp91'] = '';
                        $data[$mid]['bfSp93'] = '';
                        $data[$mid]['bfGd'] = 0;
                        $data[$mid]['bfFu'] = 0;
                }
        }

        return array('data' => $data, 'rqdata' => $rqdata);
    }

        //刷新竞彩篮球对阵缓存
    public function refreshJclqMatch()
    {
    	$data = array();
    	$sql = "select p.mid,p.hot,p.hotid,p.begin_time,p.sale_status,p.show_end_time,z.zhisheng from cp_jclq_paiqi p left join cp_jclq_zhisheng z on p.mid=z.mid where p.show_end_time >= now()";
    	$res = $this->cfgDB->query($sql)->getAll();
    	$mids = array();
    	$matchs = array();
    	foreach ($res as $val)
    	{
    		$matchs[$val['mid']] = $val;
    		$mids[] = $val['mid'];
    	}
    	if($mids)
    	{
    		$midStr = implode("','", $mids);
    		$sql1 = "select * from cp_jclq_match where mid in ('{$midStr}') order by begin_time, mid";
    		$result = $this->dc->query($sql1)->getAll();
    		$typeMatchs = array();
    		foreach ($result as $value)
    		{
    			$typeMatchs[$value['ctype']][$value['mid']] = $value['codes'];
    			if($data[$value['mid']]) continue;
    		
    			$data[$value['mid']]['mid'] = $value['mid'];
    			$data[$value['mid']]['weekId'] = $value['mname'];
    			$data[$value['mid']]['name'] = $value['league'];
    			$data[$value['mid']]['nameSname'] = $value['league_abbr'];
    			$data[$value['mid']]['home'] = $value['home'];
    			$data[$value['mid']]['homeSname'] = $value['home_abbr'];
    			$data[$value['mid']]['awary'] = $value['away'];
    			$data[$value['mid']]['awarySname'] = $value['away_abbr'];
    			$data[$value['mid']]['dt'] = strtotime($matchs[$value['mid']]['begin_time'])*1000;
    			$data[$value['mid']]['jzdt'] = strtotime($matchs[$value['mid']]['show_end_time'])*1000;
    			$data[$value['mid']]['cl'] = '#' . $value['l_background_color'];
    			$data[$value['mid']]['issue'] = $value['m_date'];
    			$data[$value['mid']]['hot'] = $matchs[$value['mid']]['hot']; //添加是否热门
    			$data[$value['mid']]['hotid'] = $matchs[$value['mid']]['hotid']; 
                        $data[$value['mid']]['zhisheng'] = $matchs[$value['mid']]['zhisheng'];
    		}
    		
    		foreach ($matchs as $mid => $val)
    		{
    			//胜负
    			if($val['sale_status'] & 1)
    			{
    				$codes = @unserialize($typeMatchs[1][$mid]);
    				$data[$mid]['sfHs'] = $codes['h'];
    				$data[$mid]['sfHf'] = $codes['a'];
    				$data[$mid]['sfGd'] = $codes ? 1 : 0;
    				$data[$mid]['sfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
    			}
    			else
    			{
    				$data[$mid]['sfHs'] = '';
    				$data[$mid]['sfHf'] = '';
    				$data[$mid]['sfGd'] = 0;
    				$data[$mid]['sfFu'] = 0;
    			}
    			//让分胜负
    			if($val['sale_status'] & 2)
    			{
    				$codes = @unserialize($typeMatchs[2][$mid]);
    				$data[$mid]['let'] = $codes['fixedodds'];
    				$data[$mid]['rfsfHs'] = $codes['h'];
    				$data[$mid]['rfsfHf'] = $codes['a'];
    				$data[$mid]['rfsfGd'] = $codes ? 1 : 0;
    				$data[$mid]['rfsfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
    			}
    			else
    			{
    				$data[$mid]['let'] = '';
    				$data[$mid]['rfsfHs'] = '';
    				$data[$mid]['rfsfHf'] = '';
    				$data[$mid]['rfsfGd'] = 0;
    				$data[$mid]['rfsfFu'] = 0;
    			}
    			//胜分差
    			if($val['sale_status'] & 4)
    			{
    				$codes = @unserialize($typeMatchs[3][$mid]);
    				$data[$mid]['sfcHs15'] = $codes['h_1-5'];
    				$data[$mid]['sfcHs610'] = $codes['h_6-10'];
    				$data[$mid]['sfcHs1115'] = $codes['h_11-15'];
    				$data[$mid]['sfcHs1620'] = $codes['h_16-20'];
    				$data[$mid]['sfcHs2125'] = $codes['h_21-25'];
    				$data[$mid]['sfcHs26'] = $codes['h_26+'];
    				$data[$mid]['sfcAs15'] = $codes['a_1-5'];
    				$data[$mid]['sfcAs610'] = $codes['a_6-10'];
    				$data[$mid]['sfcAs1115'] = $codes['a_11-15'];
    				$data[$mid]['sfcAs1620'] = $codes['a_16-20'];
    				$data[$mid]['sfcAs2125'] = $codes['a_21-25'];
    				$data[$mid]['sfcAs26'] = $codes['a_26+'];
    				$data[$mid]['sfcGd'] = $codes ? 1 : 0;
    				$data[$mid]['sfcFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
    			}
    			else
    			{
    				$data[$mid]['sfcHs15'] = '';
    				$data[$mid]['sfcHs610'] = '';
    				$data[$mid]['sfcHs1115'] = '';
    				$data[$mid]['sfcHs1620'] = '';
    				$data[$mid]['sfcHs2125'] = '';
    				$data[$mid]['sfcHs26'] = '';
    				$data[$mid]['sfcAs15'] = '';
    				$data[$mid]['sfcAs610'] = '';
    				$data[$mid]['sfcAs1115'] = '';
    				$data[$mid]['sfcAs1620'] = '';
    				$data[$mid]['sfcAs2125'] = '';
    				$data[$mid]['sfcAs26'] = '';
    				$data[$mid]['sfcGd'] = 0;
    				$data[$mid]['sfcFu'] = 0;
    			}
    			if($val['sale_status'] & 8)
    			{
    				$codes = @unserialize($typeMatchs[4][$mid]);
    				$data[$mid]['preScore'] = $codes['score'];
    				$data[$mid]['dxfBig'] = $codes['b_s'];
    				$data[$mid]['dxfSmall'] = $codes['m_s'];
    				$data[$mid]['dxfGd'] = $codes ? 1 : 0;
    				$data[$mid]['dxfFu'] = isset($codes['single']) && $codes['single'] > 0 ? 1 : 0;
    			}
    			else
    			{
    				$data[$mid]['preScore'] = '';
    				$data[$mid]['dxfBig'] = '';
    				$data[$mid]['dxfSmall'] = '';
    				$data[$mid]['dxfGd'] = 0;
    				$data[$mid]['dxfFu'] = 0;
    			}
    		}
    	}
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['JCLQ_MATCH'], json_encode($data), 0);
    }
    
        //数字彩刷新缓存
    public function refreshByLid($lid)
    {
    	if(empty($this->lidMap[$lid]))
    	{
    		if($lid == 42) 
    		{
    			//刷新竞彩足球缓存
    			$this->cache_model->refreshJczqMatch();
    		}elseif($lid == 43){
    			//刷新竞彩篮球缓存
        	 	$this->cache_model->refreshJclqMatch();
    		}elseif($lid == 10){
        		//刷新老足彩的缓存
		        $this->cache_model->refreshSfcMatch();
		        $this->cache_model->refreshSfcMatchInfo();
		
		        $this->cache_model->refreshSfc(11);
		        $this->cache_model->refreshSfc(19);
		
		        $this->cache_model->refreshSfcNew(11);
		        $this->cache_model->refreshSfcNew(19);
        	}
    		return ;
    	}
    	
    	$data = array(
    		'lIssue' => array(),
    		'cIssue' => array(),
    		'nIssue' => array(),
            'aIssue' => array(),    //正在开奖期次
    	);
    	$start = date('Y-m-d', strtotime("-5 day"));
    	$end = date('Y-m-d', strtotime("5 day"));
    	$sql = "select * from {$this->lidMap[$lid]['table']} where 1 and end_time between '{$start}' and '{$end}' and is_open = 1 and delect_flag = 0 order by issue asc";
    	$res = $this->cfgDB->query($sql)->getAll();
    	if($res)
    	{
    		$now = time();
    		$cKey = 0;
    		foreach ($res as $key => $val)
    		{
    			if($val['awardNum'])
    			{
    				$data['lIssue'] = $val;
    			}
    			if($now <= strtotime($val['show_end_time']))
    			{
    				$cKey = $key;
    				$data['cIssue'] = $val;
    				break;
    			}
                // 正在开奖期次
                if(strtotime($val['show_end_time']) <= $now && $val['status'] < 50 && $val['delect_flag'] == 0)
                {
                    $data['aIssue'] = $val;
                }
    		}
    		$data['nIssue'] = ($cKey != 0 &&isset($res[$cKey+1])) ? $res[$cKey+1] : $data['nIssue'];
    	}
    	//取不到时特殊处理
    	if(empty($data['lIssue']) || empty($data['cIssue']) || empty($data['nIssue']))
    	{
    		//当前期
    		$sql = "SELECT * FROM {$this->lidMap[$lid]['table']} WHERE show_end_time > NOW() and is_open = 1 and delect_flag = 0 ORDER BY issue ASC LIMIT 1";
    		$data['cIssue'] = $this->cfgDB->query($sql)->getRow();
    		if($data['cIssue'])
    		{
    			//上一期
    			$data['lIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue < '{$data['cIssue']['issue']}' and awardNum is not null  and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1")->getRow();
    			//下一期
    			$data['nIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue > '{$data['cIssue']['issue']}' and is_open = 1 and delect_flag = 0 ORDER BY issue ASC LIMIT 1")->getRow();
                //正在开奖
                $data['aIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue < '{$data['cIssue']['issue']}' and awardNum is null and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1")->getRow();
    		}
    	}
    	foreach ($data as $key => $value)
    	{
    		if(empty($value)) continue;    			
    		$data[$key] = array();
    		$data[$key]['seExpect'] = $this->lidMap[$lid]['issuePrefix'] . $value['issue'];
    		$data[$key]['awardNumber'] = str_replace(array('|','(', ')'), array(':', ':', ''), $value['awardNum']);
    		$data[$key]['seLotid'] = $lid;
    		$data[$key]['seFsendtime'] = strtotime($value['show_end_time'])*1000;
    		$data[$key]['seEndtime'] = strtotime($value['end_time'])*1000;
    		$data[$key]['awardTime'] = strtotime($value['award_time'])*1000;
    		$data[$key]['sale_time'] = strtotime($value['sale_time'])*1000;
    		$data[$key]['seDsendtime'] = strtotime($value['end_time'])*1000;
            $data[$key]['awardPool'] = $value['pool']?$value['pool']:0;
            $data[$key]['bonusDetail'] = $value['bonusDetail'];
    		$data[$key]['seAllowbuy'] = $value['is_open'];
    		$data[$key]['rStatus'] = $value['rstatus'];
    	}
    	
    	if(empty($data['lIssue']) || empty($data['cIssue']) || empty($data['nIssue']))
    	{
    		$msg = "彩种id:$lid " . (empty($data['lIssue']) ? '上一期期次为空,' : '') . (empty($data['cIssue']) ? '当前期次为空,' : '') . (empty($data['nIssue']) ? '下一期期次为空' : '') . "，可能影响投注区投注，请及时处理。";
                $sql = "INSERT INTO cp_alert_log
                (ctype,title,content,status,created) VALUES ('1', '".$this->lidMap[$lid]['name']."投注缓存报警', '".$msg."', '0', NOW())";
    		$this->db->query($sql);
    	}
    	
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS[$this->lidMap[$lid]['cache']], json_encode($data), 0);
    }
    
    /**
     * 快频彩投注缓存
     * @param unknown_type $lid
     */
    public function refreshFastLottery($lid)
    {
    	$data = array(
    		'lIssue' => array(),
    		'cIssue' => array(),
    		'nIssue' => array(),
    		'aIssue' => array(),
    		'nlIssue' => array(),
    	);
    	$sql = "(SELECT issue, sale_time, award_time, show_end_time, end_time, awardNum, status, is_open
				FROM {$this->lidMap[$lid]['table']}
				WHERE 1
				AND `award_time` >= date_sub( now( ) , INTERVAL 10 MINUTE )
				AND `award_time` < date_add( now( ) , INTERVAL 10 DAY )
				AND show_end_time > now()
				AND is_open =1
				AND delect_flag =0
				ORDER BY issue
				LIMIT 5 )
				union
				(SELECT issue, sale_time, award_time, show_end_time, end_time, awardNum, status, is_open
				FROM {$this->lidMap[$lid]['table']}
				WHERE 1
				AND `award_time` >= date_sub( now( ) , INTERVAL 10 DAY )
				AND `award_time` < date_add( now( ) , INTERVAL 10 MINUTE )
				AND show_end_time <= now()
				AND is_open =1
				AND delect_flag =0
				ORDER BY issue desc
				LIMIT 5 )";
    	$res = $this->cfgDB->query($sql)->getAll();
    	if($res)
    	{
    		foreach ($res as $key=>$value)
    		{
    			$issues[$key] = $value['issue'];
    		}
    		array_multisort($issues, SORT_NUMERIC, SORT_ASC, $res);
    		$now = time();
    		$cKey = 0;
    		foreach ($res as $key => $val)
    		{
    			if($val['awardNum'])
    			{
    				$data['lIssue'] = $val;
    			}
    			
    			if($now <= strtotime($val['show_end_time']))
    			{
    				$cKey = $key;
    				$data['cIssue'] = $val;
    				break;
    			}
    			// 正在开奖期次
    			if(strtotime($val['show_end_time']) <= $now && $val['status'] < 50)
    			{
    				$data['aIssue'] = $val;
    			}
    			// 上一期期次
    			if(strtotime($val['show_end_time']) <= $now)
    			{
    				$data['nlIssue'] = $val;
    			}
    		}
    		$data['nIssue'] = ($cKey != 0 && isset($res[$cKey+1])) ? $res[$cKey+1] : $data['nIssue'];
    	}
    	//取不到时特殊处理
    	if(empty($data['lIssue']) || empty($data['cIssue']) || empty($data['nIssue']))
    	{
    		//当前期
    		$sql = "SELECT * FROM {$this->lidMap[$lid]['table']} WHERE show_end_time > NOW() and is_open = 1 and delect_flag = 0 ORDER BY issue ASC LIMIT 1";
    		$data['cIssue'] = $this->cfgDB->query($sql)->getRow();
    		if($data['cIssue'])
    		{
    			//上一期已开奖期次
    			$data['lIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue < '{$data['cIssue']['issue']}' and awardNum is not null  and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1")->getRow();
    			//下一期
    			$data['nIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue > '{$data['cIssue']['issue']}' and is_open = 1 and delect_flag = 0 ORDER BY issue ASC LIMIT 1")->getRow();
    			//正在开奖
    			$data['aIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue < '{$data['cIssue']['issue']}' and awardNum is null and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1")->getRow();
    			//上一期
    			$data['nlIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE issue < '{$data['cIssue']['issue']}' and is_open = 1 and delect_flag = 0 ORDER BY issue DESC LIMIT 1")->getRow();
    		}
    	}
    	
    	foreach ($data as $key => $value)
    	{
    		if(empty($value)) continue;
    		$data[$key] = array();
    		$data[$key]['seExpect'] = $value['issue'];
    		$data[$key]['awardNumber'] = $value['awardNum'];
    		$data[$key]['seLotid'] = $lid;
    		$data[$key]['seFsendtime'] = strtotime($value['show_end_time'])*1000;
    		$data[$key]['seEndtime'] = strtotime($value['end_time'])*1000;
    		$data[$key]['awardTime'] = strtotime($value['award_time'])*1000;
    		$data[$key]['sale_time'] = strtotime($value['sale_time'])*1000;
    		$data[$key]['seDsendtime'] = strtotime($value['end_time'])*1000;
    		$data[$key]['seAllowbuy'] = $value['is_open'];
    	}
    	
    	if(empty($data['lIssue']) || empty($data['cIssue']) || empty($data['nlIssue']))
    	{
    		$msg = "彩种id:$lid " . (empty($data['lIssue']) ? '上一期已开奖期次为空,' : '') . (empty($data['cIssue']) ? '当前期次为空,' : '') . (empty($data['nIssue']) ? '下一期期次为空，' : ''). (empty($data['nlIssue']) ? '上一期次为空' : '') . "，可能影响投注区投注，请及时处理。";
                $sql = "INSERT INTO cp_alert_log
                (ctype,title,content,status,created) VALUES ('1', '".$this->lidMap[$lid]['name']."投注缓存报警', '".$msg."', '0', NOW())";
    		$this->db->query($sql);
    	}
    	
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS[$this->lidMap[$lid]['cache']], json_encode($data), 0);
    }
    
    //胜负彩当前期对阵
    public function refreshSfcMatch()
    {
    	$data = array();
    	$sql = "select mid,is_open,rj_open,show_end_time from cp_tczq_paiqi where ctype=1 and show_end_time >= now() order by mid asc limit 1";
    	$res = $this->cfgDB->query($sql)->getRow();
    	if($res)
    	{
    		$result = $this->dc->query("select * from cp_tczq_paiqi where mid='{$res['mid']}' and ctype=1")->getAll();
    		foreach ($result as $value)
    		{
    			$match = array();
    			$match['orderId'] = $value['mname'];
    			$match['gameName'] = $value['league'];
    			$match['issueId'] = $this->lidMap[11]['issuePrefix'] . $value['mid'];
    			$match['teamName1'] = $value['home'];
    			$match['teamName2'] = $value['away'];
    			$match['gameTime'] = strtotime($value['begin_date'])*1000;
    			$match['teamResult1'] = $value['result1'];
    			$match['teamResult2'] = $value['result2'];
    			$match['isOpen'] = $res['is_open'];
    			$match['rjOpen'] = $res['rj_open'];
    			$match['odds1'] = $value['eur_odd_win'];
    			$match['odds2'] = $value['eur_odd_deuce'];
    			$match['odds3'] = $value['eur_odd_loss'];
    			$data[] = $match;
    		}
    	}
    	
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['SFC_MATCH'], json_encode($data), 0);
    }

	public function refreshSfcMatchInfo()
    {
        $currentIssueId = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
            WHERE ctype = 1 AND show_end_time >= NOW() ORDER BY mid LIMIT 1")
            ->getOne();
		
        $futureThree = array();
        if (!empty($currentIssueId)) {
        	$lastOne = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 AND mid < '$currentIssueId' ORDER BY mid DESC LIMIT 1")
        			->getOne();
        	$futureThree = $this->cfgDB->query("SELECT DISTINCT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 AND mid > '$currentIssueId' ORDER BY mid LIMIT 3")
        			->getCol();
        }else {
        	$lastOne = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 ORDER BY mid DESC LIMIT 1")->getOne();
        }
        
        $issueIds = array_merge(array($lastOne, $currentIssueId), $futureThree);
        $issueStr = implode("','", $issueIds);
        $cfgMatches = $this->cfgDB->query("SELECT mid, is_open, rj_open FROM cp_tczq_paiqi
            WHERE mid IN ('{$issueStr}') AND ctype = 1")
            ->getAll();
        $issueToInfo = array();
        foreach ($cfgMatches as $cfgMatch) {
            $issueId = $this->lidMap[11]['issuePrefix'] . $cfgMatch['mid'];
            if (array_key_exists($issueId, $issueToInfo)) {
                continue;
            }
            $issueInfo = array();
            $issueInfo['isOpen'] = $cfgMatch['is_open'];
            $issueInfo['rjOpen'] = $cfgMatch['rj_open'];
            $issueToInfo[$issueId] = $issueInfo;
        }

        $rows = $this->dc->query("SELECT * FROM cp_tczq_paiqi
            WHERE mid IN ('{$issueStr}') AND ctype = 1 ORDER BY mid, mname")->getAll();
        $issueToMatches = array();
        foreach ($rows as $row) {
            $issueId = $this->lidMap[11]['issuePrefix'] . $row['mid'];
            if (!array_key_exists($issueId, $issueToMatches)) {
                $issueToMatches[$issueId] = array();
            }
            $match = array();
            $match['orderId'] = $row['mname'];
            $match['gameName'] = $row['league'];
            $match['issueId'] = $issueId;
            $match['teamName1'] = $row['home'];
            $match['teamName2'] = $row['away'];
            $match['gameTime'] = strtotime($row['begin_date']) * 1000;
            $match['score'] = $row['full_score'];
            $match['teamResult1'] = $row['result1'];
            $match['teamResult2'] = $row['result2'];
            $match['isOpen'] = $issueToInfo[$issueId]['isOpen'];
            $match['rjOpen'] = $issueToInfo[$issueId]['rjOpen'];
            $match['odds1'] = $row['eur_odd_win'];
            $match['odds2'] = $row['eur_odd_deuce'];
            $match['odds3'] = $row['eur_odd_loss'];
            $issueToMatches[$issueId][] = $match;
        }

        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->cache->save($REDIS['SFC_MATCH_NEW'], json_encode($issueToMatches), 0);
    }
    
    public function refreshSfc($lid)
    {
    	if(empty($this->lidMap[$lid]))
    	{
    		return ;
    	}   	 
    	$data = array(
    			'lIssue' => array(),
    			'cIssue' => array(),
    			'nIssue' => array(),
    	);
    	//当前期
    	$sql = "select * from {$this->lidMap[$lid]['table']} where ctype=1 and show_end_time >= now() order by mid asc,begin_date desc limit 1";
    	$data['cIssue'] = $this->cfgDB->query($sql)->getRow();
    	if($data['cIssue'])
    	{
    		//上一期
    		$lResult = $this->cfgDB->query("select mid,result from cp_rsfc_paiqi where mid<'{$data['cIssue']['mid']}' and status>='50' ORDER BY mid DESC limit 1")->getRow();
    		$data['lIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE ctype=1 and mid = '{$lResult['mid']}' ORDER BY begin_date DESC LIMIT 1")->getRow();
    		$data['lIssue']['result'] = $lResult['result'];
    		//下一期
    		$data['nIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE mid > '{$data['cIssue']['mid']}' ORDER BY mid ASC,begin_date DESC LIMIT 1")->getRow();
    		$data['nIssue']['result'] = '';
    	}else {
    		//上一期
    		$lResult = $this->cfgDB->query("select mid,result from cp_rsfc_paiqi where status>='50' ORDER BY mid DESC limit 1")->getRow();
    		$data['lIssue'] = $this->cfgDB->query("SELECT * FROM {$this->lidMap[$lid]['table']} WHERE ctype=1 and mid = '{$lResult['mid']}' ORDER BY begin_date DESC LIMIT 1")->getRow();
    		$data['lIssue']['result'] = $lResult['result'];
    	}
    	$data['cIssue']['result'] = '';
    	 
    	foreach ($data as $key => $value)
    	{
    		if(empty($value)) continue;
    		$data[$key] = array();
    		$data[$key]['seExpect'] = $this->lidMap[$lid]['issuePrefix'] . $value['mid'];
    		$data[$key]['awardNumber'] = $value['result'];
    		$data[$key]['seLotid'] = $lid;
    		$data[$key]['seFsendtime'] = strtotime($value['show_end_time'])*1000;
    		$data[$key]['seEndtime'] = strtotime($value['end_sale_time'])*1000;
    		$data[$key]['sale_time'] = strtotime($value['start_sale_time'])*1000;
    		$data[$key]['seDsendtime'] = strtotime($value['end_sale_time'])*1000;
    		$data[$key]['awardTime'] = strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($value['begin_date'])))) * 1000;
    		$data[$key]['seAllowbuy'] = $lid == 11 ? $value['is_open'] : $value['rj_open'];
    	}
    	
    	if(empty($data['lIssue']) || empty($data['cIssue']) || empty($data['nIssue']))
    	{
    		$sql = "INSERT INTO cp_alert_log
    		(ctype,title,content,status,created) VALUES ('1','".$this->lidMap[$lid]['name']."投注缓存报警', '老足彩期次读取有空值，将影响投注区投注，请及时处理。', '0', NOW())";
    		$this->db->query($sql);
    	}
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS[$this->lidMap[$lid]['cache']], json_encode($data), 0);
    }

    public function refreshSfcNew($lid)
    {
        if (empty($this->lidMap[$lid])) {
            return;
        }

        $currentIssueId = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
            WHERE ctype = 1 AND show_end_time >= NOW() ORDER BY mid LIMIT 1")
            ->getOne();
        
        $futureThree = array();
        if (!empty($currentIssueId)) {
        	$lastOne = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 AND mid < '$currentIssueId' ORDER BY mid DESC LIMIT 1")
        			->getOne();
        	$futureThree = $this->cfgDB->query("SELECT DISTINCT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 AND mid > '$currentIssueId' ORDER BY mid LIMIT 3")
        			->getCol();
        }else {
        	$lastOne = $this->cfgDB->query("SELECT mid FROM cp_tczq_paiqi
        			WHERE ctype = 1 ORDER BY mid DESC LIMIT 1")
        			->getOne();
        }
        
        $issueIds = array_merge(array($lastOne, $currentIssueId), $futureThree);

        $awardIssueId = $this->cfgDB->query("SELECT mid FROM cp_rsfc_paiqi
            WHERE status >= 50 ORDER BY mid DESC LIMIT 1")
            ->getOne();
        if (!in_array($awardIssueId, $issueIds)) {
            array_push($issueIds, $awardIssueId);
        }
        $issueStr = implode("','", $issueIds);
        $results = $this->cfgDB->query("SELECT mid, result, rj_sale, sfc_sale, award, award_detail,
			rstatus sfcRStatus, rjrstatus rjRStatus
            FROM cp_rsfc_paiqi WHERE mid IN ('{$issueStr}')")
            ->getAll();
        $resultToInfo = array();
		foreach ($results as $result) {
            $issueId = $this->lidMap[$lid]['issuePrefix'] . $result['mid'];
            $resultInfo = array();
            $resultInfo['result'] = $result['result'];
            $resultInfo['rjSale'] = $result['rj_sale'];
            $resultInfo['sfcSale'] = $result['sfc_sale'];
            $resultInfo['award'] = $result['award'];
            $resultInfo['awardDetail'] = $result['award_detail'];
            $resultInfo['sfcRStatus'] = $result['sfcRStatus'];
            $resultInfo['rjRStatus'] = $result['rjRStatus'];
            $resultToInfo[$issueId] = $resultInfo;
        }

        $issues = $this->cfgDB->query("SELECT * FROM cp_tczq_paiqi
            WHERE mid IN ('{$issueStr}') AND ctype = 1 GROUP BY mid")
            ->getAll();
        foreach ($issues as &$issueLn) {
            $issueId = $this->lidMap[$lid]['issuePrefix'] . $issueLn['mid'];
            $issueResult = $resultToInfo[$issueId];
            $issueLn['seExpect'] = $issueId;
            $issueLn['awardNumber'] = $issueResult['result'];
            $issueLn['result'] = $issueResult['result'];
            $issueLn['rjSale'] = $issueResult['rjRStatus'] > 50 ? $issueResult['rjSale'] : '';
            $issueLn['sfcSale'] = $issueResult['sfcRStatus'] > 50 ? $issueResult['sfcSale'] : '';
			$issueLn['sfcRStatus'] = $issueResult['sfcRStatus'];
			$issueLn['rjRStatus'] = $issueResult['rjRStatus'];
            $issueLn['award'] = $issueResult['award'];
            $issueLn['awardDetail'] = $issueResult['awardDetail'];
            $issueLn['seLotid'] = $lid;
            $issueLn['seFsendtime'] = strtotime($issueLn['show_end_time'])*1000;
            $issueLn['seEndtime'] = strtotime($issueLn['end_sale_time'])*1000;
            $issueLn['sale_time'] = strtotime($issueLn['start_sale_time'])*1000;
            $issueLn['seDsendtime'] = strtotime($issueLn['end_sale_time'])*1000;
            $issueLn['awardTime'] = strtotime(date('Y-m-d 12:00:00', strtotime('1 day', strtotime($issueLn['begin_date'])))) * 1000;
            $issueLn['seAllowbuy'] = $lid == 11 ? $issueLn['is_open'] : $issueLn['rj_open'];
        }

        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->cache->save($REDIS[$this->lidMap[$lid]['cacheNew']], json_encode($issues), 0);
    }

    /**
     * 彩种管理缓存
     */
    public function refreshLotteryConfig()
    {
    	$data = array();
    	$result = $this->cfgDB->query("select * from cp_lottery_config where 1")->getAll();
    	foreach ($result as $value)
    	{
    		$data[$value['lottery_id']] = $value;
    	}
    	
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['LOTTERY_CONFIG'], json_encode($data), 0);
    }
    
    //刷新脚本缓存
    public function refreshCrontabConfig()
    {
    	$data = array();
    	$result = $this->cfgDB->query("select * from cp_crontab_config where 1 and delflag = 0")->getAll();
    	foreach ($result as $value)
    	{
    		$data[$value['ctype']][] = $value['cname'];
    	}
    	$this->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->config->item('REDIS');
    	$this->cache->save($REDIS['CRONTAB_CONFIG'], json_encode($data), 0);
    }
    
    public function getCheckCacheType()
    {
    	return $this->lidMap;
    }
}
