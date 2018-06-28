<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cli_Refresh_Cache_File extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
		if (ENVIRONMENT === 'production')
        {
        	$this->filepath = '/opt/case/www.166cai.com/source/cache';
        }
        elseif (ENVIRONMENT === 'checkout')
        {
        	$this->filepath = '/opt/case/www.166cai.com/source/cache';
        }
        else 
        {
        	$this->filepath = 'E:\wamp\www\166cai\source\cache';
        }
	}
	
	public function index()
	{
		while(true)
		{
			if(file_exists("{$this->filepath}/cache.start"))
        	{
        		if(file_exists("{$this->filepath}/cache.stop"))
        			unlink("{$this->filepath}/cache.stop");
        		if(file_exists("{$this->filepath}/cache.start"))
        			unlink("{$this->filepath}/cache.start");
        	}
			if(file_exists("{$this->filepath}/cache.stop"))
        	{
        		break;
        	}
        	//便于测试环境的调试
        	if(ENVIRONMENT==='development')
        	{
        		$this->runCron();
        	}else{
        		$croname = "cron/cli_refresh_cache_file runCron";
	    		system("{$this->php_path} {$this->cmd_path} $croname",  $status);
        	}
        	if (ENVIRONMENT === 'production')
        	{
        		system("/bin/bash /opt/shell/rsync_cache.sh", $status);
        	}
        	usleep(100000);
		}
	}
	
	public function runCron()
	{
		$funcs = array('syxw' => 'syxw', 'jczq' => 'jc', 'jclq' => 'jc', 'ssq' => 'fc', 'dlt' => 'fc');
        foreach ($funcs as $param => $func)
        {
        	$method = "cache$func";
        	if(method_exists($this, $method))
        	{
        		$jsons = $this->$method($param);
        		file_put_contents("{$this->filepath}/$param.html", $jsons);
        	}
        }
        
		//ks
        $kisufuncs = array('ks' => 'cacheKIssue', 'jlks' => 'cacheKIssue', 'jxks' => 'cacheKIssue', 'klpk' => 'cacheKIssue', 'cqssc' => 'cacheKIssue');
        foreach ($kisufuncs as $param => $func) {
        	$jsons = $this->$func($param);
        	file_put_contents("{$this->filepath}/issue_{$param}.html", $jsons);
        	$jsons = $this->$func($param, true, false);
        	file_put_contents("{$this->filepath}/issuefollow_{$param}.html", $jsons);
        	$jsons = $this->$func($param, false, true);
        	file_put_contents("{$this->filepath}/issuehstyopen_{$param}.html", $jsons);
        }
        //syxw
        $syxwisufuncs = array('syxw' => 'SyxwIssue', 'jxsyxw' => 'SyxwIssue', 'hbsyxw' => 'SyxwIssue', 'gdsyxw' => 'SyxwIssue');
		foreach ($syxwisufuncs as $param => $func)
        {
        	$method = "cache$func";
        	if(method_exists($this, $method))
        	{
        		$jsons = $this->$method($param);
        		file_put_contents("{$this->filepath}/issue_$param.html", $jsons);
        		$jsons = $this->$method($param, true);
        		file_put_contents("{$this->filepath}/issuefollow_$param.html", $jsons);
        	}
        }
        
        //慢频彩
        $issuefuncs = array('ssq' => 'Issue', 'dlt' => 'Issue', 'fcsd' => 'Issue', 'pls' => 'Issue', 'plw' => 'Issue', 'qlc' => 'Issue', 'qxc' => 'Issue');
        foreach ($issuefuncs as $param => $func)
        {
        	$method = "cache$func";
        	if(method_exists($this, $method))
        	{
        		$jsons = $this->$method($param);
        		file_put_contents("{$this->filepath}/issue_$param.html", $jsons);
        	}
        }
	}
	
	private function cachesyxw($params)
	{
		$current = json_decode($this->cache->get($this->REDIS['SYXW_ISSUE_TZ']), TRUE);
		$res = array(
        			'issue'   => $current['cIssue']['seExpect'],
        			'restTime'=> $current['cIssue']['seFsendtime']/1000-time()
        	);
        return json_encode($res);
	}
	
	private function cachefc($enName)
	{
		$current = json_decode($this->cache->get($this->REDIS[strtoupper($enName) . '_ISSUE']), TRUE);
        $res = array(
        		'seFsendtime' => $current['cIssue']['seFsendtime'],
        		'issue'       => $current['cIssue']['seExpect'],
        		'restTime'    => $current['cIssue']['seFsendtime']/1000-time()
        );
        $res['jrkj'] = 0;
        if ( date('d', $current['cIssue']['seFsendtime']/1000) == date('d'))
        {
        	$res['jrkj'] = 1;
        }
        return json_encode($res);
	}
	
	private function cachejc($lottery)
	{
		$$lottery = $this->cache->redis->get($this->REDIS[strtoupper($lottery).'_MATCH']);
    	$lotteryArr = json_decode($$lottery, true);
        $hot = array();
        if (!empty($lotteryArr)) 
        {
        	foreach ($lotteryArr as $mid => $val)
        	{
        		if (empty($val['spfSp3']) && empty($val['rfsfHf'])) 
        		{
        			unset($lotteryArr[$mid]);
        		}else 
        		{
        			$hot[$val['hotid']][(($val['jzdt']/1000)-time()).$mid] = $val['mid'];
        		}
        	}
        	krsort($hot);
        	foreach ($hot as $k => $value) 
        	{
        		ksort($value);
        		$hot[$k] = $value;
        		$hot[$k] = array_values($hot[$k]);
        	}
        	$lArr = array();
        	for ($i = 1; $i <= 10; $i++) {
        		if ($i == 10) {
        			$j = 0;
        		}else {
        			$j = $i;
        		}
        		if (!empty($hot[$j])) {
        			foreach ($hot[$j] as $k => $hv) {
        				if (count($lArr) < 3) {
        					$lArr[$hv] = $lotteryArr[$hv];
        				}else {
        					unset($hot[$j][$k]);
        				}
        			}
        		}
        	}
        }
    	return json_encode(array($lArr, $hot));
	}
	
	public function cacheSyxwIssue($enName, $follow = false) 
	{
		$dateStr = str_replace('20', '', date('Ymd'));
		$flag = false;
		$followLength = array('syxw' => 87, 'jxsyxw' => '84', 'hbsyxw' => '81', 'gdsyxw' => '84');
		$current = json_decode($this->cache->get($this->REDIS[strtoupper($enName).'_ISSUE_TZ']), TRUE);
		$awardArr = unserialize($this->cache->get($this->REDIS[strtoupper($enName).'_AWARD']));
		$miss = unserialize($this->cache->get($this->REDIS[strtoupper($enName).'_MISS']));
		$res['prev'] = empty($current['aIssue']) ? $current['lIssue']['seExpect'] : $current['aIssue']['seExpect'];
		//计算已售、剩余期数
		$count = substr($current ['cIssue'] ['seExpect'], -2)-1;
		$rest = $followLength[$enName]-$count;
		$history = array_slice($awardArr, 0, 10);
		$miss = $this->getMissNumber($miss, $history);
		
		foreach ($history as $k => $h)
		{
			$kjCal = $this->calculate($h['awardNum']);
			$history[$k]['dx'] = $kjCal[1].":".(5-$kjCal[1]);
			$history[$k]['jo'] = $kjCal[0].":".(5-$kjCal[0]);
			$history[$k]['he'] = array_sum(explode(',', $h['awardNum']));
			if ($history[$k]['issue'] == $res['prev'])
			{
				$flag = true;
			}
		}
		if (!$flag)
		{
			$arr = array('issue' => $res['prev']);
			$res['awardNumber'] = "'正', '在', '开', '奖', '中'";
			array_unshift($history, $arr);
			array_pop($history);
		}
		else
		{
			$res['awardNumber'] = $history[0]['awardNum'];
		}
		krsort($history);
		$history = array_values($history);
		
		if ($follow) 
		{
			$followIssues = json_decode($this->cache->hGet($this->REDIS['ISSUE_COMING'], strtoupper($enName)), true);
			//生成追号期次
			if (strtotime($followIssues[0]['show_end_time']) <= time()) 
			{//假如缓存来不及更新，判断第一期是否已经过期
				unset($followIssues[0]);
			}
			$followIssues = array_slice($followIssues, 0, $followLength[$enName]);
			$multi = 1;
			foreach ($followIssues as $i => $issue) 
			{
				$chases[$issue['issue']] = array(
						'award_time' => $issue['award_time'],
						'show_end_time' => $issue['show_end_time'],
						'multi' => $multi,
						'money' => 0
				);
			}
			$res['chases'] = $chases;
		}
		$res['seFsendtime'] = $current['cIssue']['seFsendtime'];
		$res['restTime'] = $current['cIssue']['seFsendtime']/1000-time();
		$res['rest'] = $rest;
		$res['awardresttime'] = empty($current['aIssue']) ? 9999999 : ($current['aIssue']['awardTime']/1000-time());
		$res['count'] = $count;
		$res['issue'] = $current['cIssue']['seExpect'];
		$res['history'] = $history;
		$res['miss'] = $miss;
		return json_encode($res);
	}

	public function cacheKIssue($enName, $follow = false, $hstyopen = false) {
		$numArr = array('ks' => 82, 'jlks' => 87, 'jxks' => 84, 'klpk' => 88, 'cqssc' => 120);
		$current = json_decode($this->cache->get($this->REDIS[strtoupper($enName).'_ISSUE_TZ']), TRUE);
		$count = substr($current ['cIssue'] ['seExpect'], -2)-1;
		$rest = $numArr[$enName]-$count;
		if ($follow) {
			$followIssues = json_decode($this->cache->hGet($this->REDIS['ISSUE_COMING'], strtoupper($enName)), true);
	
			//生成追号期次
			if (strtotime($followIssues[0]['show_end_time']) <= time()) {//假如缓存来不及更新，判断第一期是否已经过期
				unset($followIssues[0]);
			}
			$followIssues = array_slice($followIssues, 0, $numArr[$enName]);
			$multi = 1;
			foreach ($followIssues as $i => $issue) {
				$chases[$issue['issue']] = array(
						'award_time' => $issue['award_time'],
						'show_end_time' => $issue['show_end_time'],
						'multi' => $multi,
						'money' => 0
				);
			}
			$res['chases'] = $chases;
		}
		$res['prev'] = $current['nlIssue']['seExpect'];
		$res['awardNumber'] = "正, 在, 开, 奖, 中";
		if ($current['nlIssue']['awardTime']/1000 < time()) {
			$awardArr = unserialize($this->cache->get ( $this->REDIS [strtoupper($enName).'_AWARD'] ));
			if ($current['nlIssue']['seExpect'] == $awardArr[0]['issue']) $res['awardNumber'] = $awardArr[0]['awardNum'];
		}
		if ($hstyopen || $follow) {
			if ($enName == 'cqssc') {
				$miss = unserialize($this->cache->get ( $this->REDIS ['CQSSC_MISS'] ));
			}else {
				$miss = json_decode($this->cache->get($this->REDIS[strtoupper($enName).'_MISS']), true);
			}
			if (empty($awardArr)) {
				$awardArr = unserialize($this->cache->get ( $this->REDIS [strtoupper($enName).'_AWARD'] ));
			}
			$history = array_slice ( $awardArr, 0, 10 );
			$hsty = array();
	
			foreach ( $history as $h ) {
				unset($h['pool']);
	    		unset($h['sale']);
	    		if (in_array($enName, array('ks', 'jlks', 'jxks'))) {
	    			$award =  explode ( ',', $h ['awardNum'] );
	    			$acount = count(array_unique(array_values($award)));
	    			$h['he'] = array_sum ($award);
	    			if ($acount == 1) {
	    				$h['type'][] = 0;
	    				$h['type'][] = 3;
	    			}elseif ($acount == 2) {
	    				$h['type'][] = 4;
	    				$h['type'][] = 3;
	    				$h['type'][] = 5;
	    			}else {
	    				$h['type'][] = 1;
	    				$h['type'][] = 5;
	    			}
	    			if (in_array($h ['awardNum'], array('1,2,3', '2,3,4', '3,4,5', '4,5,6'))) {
	    				$h['type'][] = 2;
	    			}
	    			$h['kd'] = max($award) - min($award);
	    		}elseif ($enName === 'klpk') {
	    			$award = explode('|', $h ['awardNum']);
	    			$awArr = array(explode(',', $award[0]), explode(',', $award[1]));
	    			sort($awArr[0]);
	    			$c0 = count(array_unique(array_values($awArr[0])));
	    			$c1 = count(array_unique(array_values($awArr[1])));
	    			if ($c1 == 1) {
	    				$h['type'] = '同花';
	    			}
	    			if ($c0 == 1) {
	    				$h['type'] = '豹子';
	    			}elseif ($c0 == 2) {
	    				$h['type'] = '对子';
	    			}elseif ((($awArr[0][1] == $awArr[0][0] + 1) && ($awArr[0][2] == $awArr[0][1] + 1)) || implode(',', $awArr[0]) === '01,12,13') {
	    				if ($h['type'] === '同花'){
	    					$h['type'] = '同花顺';
	    				}else {
	    					$h['type'] = '顺子';
	    				}
	    			}
	    			if (empty($h['type'])) {
	    				$h['type'] = '散牌';
	    			}
	    		}
				$hsty[$h['issue']] = $h;
			}
	
			ksort ( $hsty );
			if (!array_key_exists($current['nlIssue']['seExpect'], $hsty)) {
				$hsty[$current['nlIssue']['seExpect']] = array('issue' => $current['nlIssue']['seExpect'], 'prev' => 1);
				unset ( $hsty[min(array_keys($hsty))] );
			}
			$res['history'] = $hsty;
			$res['miss'] = $miss;
		}
		$res['seFsendtime'] = $current['cIssue']['seFsendtime'];
		$res['restTime'] = $current['cIssue']['seFsendtime']/1000-time();
		$res['rest'] = $rest;
		$res['awardresttime'] = empty($current['aIssue']) ? 9999999 : ($current['aIssue']['awardTime']/1000-time());
		$res['count'] = $count;
		$res['issue'] = $current['cIssue']['seExpect'];
		return json_encode($res);
	}
	
	public function cacheIssue($enName) {
		if ($enName === 'fcsd') {
			$current = json_decode($this->cache->get($this->REDIS['FC3D_ISSUE']), TRUE);
		}else {
			$current = json_decode($this->cache->get($this->REDIS[strtoupper($enName) . '_ISSUE']), TRUE);
		}
		$temp = array(
		    'sfc'   => '11',
		    'rj'    => '19',
		    'pls'   => '33',
		    'plw'   => '35',
		    'jczq'  => '42',
		    'jclq'  => '43',
		    'ssq'   => '51',
		    'fcsd' => '52',
		    'qxc' => '10022',
		    'syxw' => '21406',
		    'qlc' => '23528',
		    'dlt' => '23529',
			'ks'  => '53',
			'jxsyxw' => '21407',
		    'hbsyxw' => '21408',
			'klpk' => '54',
			'jlks'  => '56',
		    'jxks'  => '57',
		    'gdsyxw' => '21421',
		);
		//合买提前截止时间
		$this->load->model('lottery_model');
		$lotteryConfig = $this->lottery_model->getLotteryConfig($temp[$enName], 'united_ahead,ahead');
		$res = array(
				'seFsendtime' => $current['cIssue']['seFsendtime'],
				'issue'       => $current['cIssue']['seExpect'],
				'restTime'    => $current['cIssue']['seFsendtime']/1000-time(),
				'hmendTime'   => $current['cIssue']['seFsendtime']/1000 - $lotteryConfig['united_ahead'] * 60,
				'realendTime' => date('Y-m-d H:i:s', $current['cIssue']['seEndtime']/1000)
				);

		$followIssues = json_decode($this->cache->hGet($this->REDIS['ISSUE_COMING'], strtoupper($enName)), true);
		//生成追号期次
		if (strtotime($followIssues[0]['show_end_time']) <= time()) {//假如缓存来不及更新，判断第一期是否已经过期
			unset($followIssues[0]);
		}
		$followIssues = array_slice($followIssues, 0, 50);
		$multi = 1;
		foreach ($followIssues as $i => $issue)
		{
			$key = in_array($enName, array('dlt', 'pls', 'plw', 'qxc')) ? "20".$issue['issue'] : $issue['issue'];
			$chases[$key] = array(
					'award_time' => $issue['award_time'],
					'show_end_time' => $issue['show_end_time'],
					'multi' => $multi,
					'money' => 0
			);
		}
		$res['chases'] = $chases;
		return json_encode($res);
	}
	
	public function getMissNumber($miss, $history)
	{
		$missIssue = array_keys($miss);
		if($missIssue[0] < $history[0]['issue'])
		{
			// 计算最新开奖其次的遗漏
			if(!empty($history[0]['awardNum']) && !empty($miss[$history[1]['issue']]))
			{
	
				$ballAmount = $this->getBallAmount();
				$count = count($ballAmount);
	
				// 上期遗漏数据
				foreach ($miss[$history[1]['issue']] as $playType => $countStr)
				{
					$tmpAry = explode(',', $countStr);
					$c = count($tmpAry);
					for ($i = 0; $i < $c; $i ++)
					{
					$missedCounterAry[$playType][$i + 1] = intval($tmpAry[$i]);
					}
					}
	
					$awardNumber = $history[0]['awardNum'];
					$numberAry = explode(',', $awardNumber);
					// 初始化数据源格式
					$matches = array(
					1 => $awardNumber,
					2 => $numberAry[0],
					3 => $numberAry[1],
					4 => $numberAry[2],
					5 => implode(',', array($numberAry[0], $numberAry[1])),
					6 => implode(',', array($numberAry[0], $numberAry[1], $numberAry[2]))
					);
	
					for ($i = 0; $i < $count; $i ++)
					{
					for ($j = 1; $j <= $ballAmount[$i]; $j ++)
					{
					if ($j < 10)
					{
					$needle = '0' . $j;
					}
					else
					{
					$needle = '' . $j;
					}
						if (strstr($matches[$i + 1], $needle))
							{
								$missedCounterAry[$i][$j] = 0;
					}
					else
					{
					$missedCounterAry[$i][$j] += 1;
					}
					}
					}
	
					foreach ($missedCounterAry as $playType => $countStr)
					{
					$missNum[$playType] = implode(',', $countStr);
					}
					array_pop($miss);
					$miss[$history[0]['issue']] = $missNum;
					krsort($miss);
					}
					}
					return $miss;
					}
	
					// 遗漏种类统计
					private function getBallAmount()
					{
					$ballAmountConfig = array(
							0 => 11, //11个任选n
							1 => 11, //11个前n直选第一位
							2 => 11, //11个前n直选第二位
							3 => 11, //11个前n直选第三位
							4 => 11, //11个前n组选前二位
							5 => 11, //11个前n组选前三位
									);
									return $ballAmountConfig;
	}
	
	function calculate($str)
	{
		$arr = explode(',', $str);
		$ji = 0;
		$da = 0;
		foreach ($arr as $v)
		{
			if ($v%2 > 0)
			{
				$ji++;
			}
			if ($v >= 6)
			{
				$da++;
			}
		}
		return array($ji, $da);
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */