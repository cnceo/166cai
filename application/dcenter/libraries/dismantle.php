<?php
/**
 * Copyright (c) 2013,上海瑞创网络科技股份有限公司
 * 文件名称：ProcessLock.php
 * 摘    要：进程锁功能
 * 作    者：胡小明
 * 修改日期：2013.12.03
 */
class Dismantle
{
	private $rballs = 6;
	public function __construct()
	{
		
	}
	
	public function dismantle_qxc()
	{
		$orderId = '20150408163808009635';
		$codestr = '01234,01234,56789,13579,56789,01234,134579:1:1';
		$codearr = explode(':', $codestr);
		$codestrs = explode(',', $codearr[0]);
		$codearrs = array();
		$sortarr = array();
		//$betnums = 1;
		foreach ($codestrs as $in => $codestr)
		{
			$codearrs[$in] = str_split($codestr);
		}
		
		$allCodes = array();
		$this->dismantle_recursive_qxc($codearrs, $allCodes);
		$betnums = 0;
		foreach ($allCodes as $allCode)
		{
			$num = 1;
			foreach ($allCode as $bets)
			{
				$num *= count($bets); 
			}
			$betnums += $num;
		}
		//print_r($allCodes);
		//echo $betnums;
		$arr = array();
		foreach ($allCodes as $value)
		{
			$tmp = array();
			foreach ($value as $val)
			{
				$tmp[] = implode('', $val);
			}
			$arr[] = implode(',', $tmp);
		}
		echo '<pre>';
		print_r($arr);
	}
	
	private function dismantle_recursive_qxc($codearrs, &$allCodes)
	{
		$betnums = 1;
		foreach ($codearrs as $in => $codearr)
		{
			$nums = count($codearr);
			$sortarr[$nums] = $in;
			$betnums *= $nums;	
		}
		if($betnums > 10000)
		{
			ksort($sortarr);
			$in = array_pop($sortarr);
			$splinum = $betnums / 10000;
			$betnums = $betnums / count($codearrs[$in]);
			$arrsize = floor(count($codearrs[$in]) / $splinum);
			$arrsize = $arrsize > 0 ? $arrsize : 1;
			$splitarrs = array_chunk($codearrs[$in], $arrsize);
			foreach ($splitarrs as $splitarr)
			{
				$codearrs[$in] = $splitarr;
				$betnums *= count($splitarr);
				if($betnums <= 10000)
				{
					array_push($allCodes, $codearrs);
				}
				else 
				{
					$this->dismantle_recursive_qxc($codearrs, $allCodes);
				}
			}
		}
		else
		{
			array_push($allCodes, $codearrs);
		}
	}
	
	public function do_dismantle()
	{
		$rballs = $this->rballs;
		$balls = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33);
		//$balls = array('01', '03', '07', '09', '10', '11', '14', '15', '16', '17', '19', '20', '22', '24', '27', '28', '29', '31', '32');
		$balls = array(1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33);
		$blu_balls = array('03', '12', '11');
		$betnum =  $this->combine(count($balls), $rballs);
		$OneBets = array();
		$AllBets = array();
		$OneBets['saltball'] = array();
		$OneBets['otherball'] = $balls;
		$OneBets['betnum'] = $betnum;
		array_push($AllBets, $OneBets);
		$reAllBets = array();
		$this->_dismantle_ball($reAllBets, $OneBets);
		$blu_bets = $this->combineList($blu_balls, 1);
		$blu_nums = count($blu_bets);
		
		$resultBets = array();
		foreach ($reAllBets as $in => $reAllBet)
		{
			$num = $reAllBet['betnum'] * $blu_nums;
			if($num > 10000)
			{
				foreach ($blu_bets as $blu_bet)
				{
					$reAllBet['bluball'] = $blu_bet;
					array_push($resultBets, $reAllBet);
				}
			}
			else 
			{
				$reAllBet['bluball'] = $blu_balls;
				$reAllBet['betnum'] = $num;
				array_push($resultBets, $reAllBet);
			}
		}
		echo '<pre>';
		print_r($resultBets);
	}
	
	private function _dismantle_ball(&$reAllBets, $AllBet)
	{
		$rballs = $this->rballs;
		$AllBets = array();
		$mballs = array();
		$rball = $rballs - count($AllBet['saltball']); //去掉胆的个数
		$betnum = $AllBet['betnum'];
		$balls = $AllBet['otherball'];
		if($betnum > 10000)
		{
			while($betnum > 10000)
			{
				array_push($mballs, array_shift($balls));
				$betnum =  $this->combine(count($balls), -- $rball);
			}
			$OneBets['saltball'] = $this->marge_arr($AllBet['saltball'], $mballs);
			$OneBets['otherball'] = $balls;
			$OneBets['betnum'] = $betnum;
			array_push($reAllBets, $OneBets);
			$mballnum = count($mballs);
			if($mballnum > 1)
			{
				$zhnum = 1;
				while($zhnum < $mballnum)
				{
					$clists = $this->combineList($mballs, $zhnum);
					foreach ($clists as $clist)
					{
						$OneBets['saltball'] = $this->marge_arr($AllBet['saltball'], $clist);
						$OneBets['betnum'] = $this->combine(count($balls), $rballs - $zhnum);
						if($OneBets['betnum'] > 10000)
						{
							array_push($AllBets, $OneBets);
						}
						else 
						{
							array_push($reAllBets, $OneBets);
						}
					}
					$zhnum ++ ;
				}
			}
			$OneBets['saltball'] = $this->marge_arr($AllBet['saltball'],  array());
			$OneBets['betnum'] = $this->combine(count($balls), $rballs - count($AllBet['saltball']));
			if($OneBets['betnum'] > 10000)
			{
				array_push($AllBets, $OneBets);
			}
			else 
			{
				array_push($reAllBets, $OneBets);
			}
			if(count($AllBets) > 0)
			{
				foreach ($AllBets as $AllBet)
				{
					$this->_dismantle_ball($reAllBets, $AllBet);
				}
			}
			else
			{
				return ;
			}
		}
		else 
		{
			array_push($reAllBets, $AllBet);
		}
	}
	
	public function _dismantle_match($betstr='') 
    {
    	//单关测试
    	$betstr = 'HH|CBF>20150227001=1:0(9)/1:1(6.5)/1:2(7.5)/1:3(16)/1:4(40)/1:5(100)/2:0(13)/2:1(8.5)/2:2(12)/2:3(21)/2:4(60)/2:5(150)/3:0(29)/3:1(19)/3:2(23)/3:3(50)/4:0(80)/4:1(50)/4:2(70)/5:0(300)/5:1(150)/5:2(250)/0:9(60)/9:9(300)/9:0(70)/0:0(12)/0:1(8.5)/0:2(11)/0:3(21)/0:4(60)/0:5(150),CBF>20150227002=1:0(5.3)/1:1(6.75)/1:2(15)/1:3(50)/1:4(250)/1:5(700)/2:0(6.5)/2:1(7.25)/2:2(18)/2:3(60)/2:4(250)/2:5(700)/3:0(10)/3:1(12)/3:2(28)/3:3(80)/4:0(22)/4:1(28)/4:2(50)/5:0(60)/5:1(60)/5:2(150)/0:9(300)/9:9(600)/9:0(40)/0:0(9)/0:2(27)/0:1(11)/0:3(80)/0:4(300)/0:5(900),CBF>20150227003=1:0(5.3)/1:1(6)/1:2(11)/1:3(40)/1:4(120)/1:5(500)/2:0(7.5)/2:1(8)/2:2(17)/2:3(50)/2:4(200)/2:5(600)/3:0(16)/3:1(17)/3:2(33)/3:3(80)/4:0(40)/4:1(50)/4:2(80)/5:0(120)/5:1(150)/5:2(300)/0:9(250)/9:9(600)/9:0(100)/0:0(6.75)/0:3(50)/0:2(16)/0:1(7.5)/0:4(150)/0:5(600),CBF>20150227004=1:0(7)/1:1(6.75)/1:2(10.5)/1:3(28)/1:4(80)/1:5(250)/2:0(8.5)/2:1(7.5)/2:2(13)/2:3(30)/2:4(100)/2:5(300)/3:0(15)/3:1(13)/3:2(21)/3:3(50)/4:0(35)/4:1(30)/4:2(50)/5:0(80)/5:1(80)/5:2(120)/0:9(100)/9:9(300)/9:0(50)/0:0(11)/0:1(10)/0:2(18)/0:3(40)/0:4(120)/0:5(400),CBF>20150227005=1:0(4.9)/2:0(7)/2:1(7.5)/3:0(13)/3:1(17)/3:2(35)/4:0(35)/4:1(40)/4:2(80)/5:0(100)/5:1(120)/5:2(250)/9:0(100)|3*1';
    	//混合投注测试
    	//$betstr = 'HH|RQSPF>20150227001=0{1}(4.7)/1{1}(4)/3{1}(1.5),JQS>20150227001=1(4.7)/2(3.35)/3(3.55)/5(8.5)/6(15)/7(24),CBF>20150227001=1:3(16)/2:3(21)/2:4(60)/3:0(29)/3:3(50)/4:0(80)/0:4(60),SPF>20150227002=0(5.7),RQSPF>20150227002=0{-1}(2.22)/3{-1}(2.7),SPF>20150227003=1(2.95),RQSPF>20150227003=0{-1}(1.65)/3{-1}(4.2),SPF>20150227004=1(3.35),RQSPF>20150227004=0{-1}(1.73)/3{-1}(3.6),SPF>20150227005=1(2.78),RQSPF>20150227005=1{-1}(3.35),RQSPF>20150227006=3{-1}(3.9),SPF>20150227008=1(2.85),RQSPF>20150227008=3{-1}(4.1),SPF>20150227009=1(2.75),RQSPF>20150227009=1{-1}(3.95),SPF>20150227010=0(4.1)/1(2.8)/3(1.91),RQSPF>20150227010=3{-1}(4.1),RQSPF>20150227011=1{1}(4),RQSPF>20150227012=1{-1}(3.9),RQSPF>20150227013=1{1}(3.9),RQSPF>20150227016=1{-1}(3.65),RQSPF>20150227015=1{-1}(4.2),CBF>20150227015=1:2(8.5)/2:1(7.75)/2:3(20)/2:5(175)/3:2(18)/4:1(30)/0:4(60),RQSPF>20150227014=3{-1}(2.65)|2*1,4*1';
    	$betarr = explode('|', $betstr);
    	$ggtypes = explode(',', $betarr[2]);
    	$matcharr = explode(',', $betarr[1]);
    	$multi = 1;
    	if(!empty($matcharr))
    	{
    		$matches = array();
    		foreach ($matcharr as $match)
    		{
    			if(preg_match('/(.*?)>(.*?)=(.*)/i', $match, $map))
    			{
    				$matches[$map[2]][$map[1]] = explode('/', $map[3]);
    			}
    		}
    		$matchnums = array_keys($matches);
    		if(!empty($ggtypes))
    		{
    			foreach ($ggtypes as $ggtype)
    			{
    				$ggnum = preg_replace('/\*\d+$/', '', $ggtype);
    				$matchcom[intval($ggnum)] = $this->combineList($matchnums, intval($ggnum)); //先场次组合
    			}
    			$num = 0;
    			foreach ($matchcom as $ggtype => $matchcoms)
    			{
    				foreach ($matchcoms as $key => $matchone)
    				{
    					$at = array_values($matchone);
    					$ah = array_shift($at);
    					$matchcom[$ggtype][$key]['xtypes'] = $this->recursive($matches, $ah, $at);//玩法组合
    					foreach ($matchcom[$ggtype][$key]['xtypes'] as $in => $xtype)
    					{
    						$xtypes = explode('*', $xtype);
    						$betnum = 1;
    						$sortarr = array();
    						foreach ($xtypes  as $xin => $type)
    						{
    							if(preg_match('/,(\d+)$/i', $type, $maps));
    							$betnum *= intval($maps[1]);
    							$sortarr[intval($maps[1])] = $xin;
    						}
    						if($betnum > 10000 || $multi > 99 || ($betnum * $multi * 2 > 20000))
    						{
    							$this->split_order($matchcom[$ggtype][$key]['xtypes'], $in, $betnum, $multi, $sortarr);
    						}
    						else 
    						{
    							$matchcom[$ggtype][$key]['xtypes'][$in] .= "|ZS={$betnum},BS={$multi},JE=" . ($betnum * $multi * 2);
    						}
    					}
    				}
    			}
    			//调试信息
    			$this->debug($matchcom);
    		}
    	}
    }
    
    private function split_order(&$betstr, $xin, $betnum, $multi, $sortarr)
    {
    	$betmoney = $betnum * $multi * 2 ;
    	if($betnum > 10000)
    	{
    		ksort($sortarr);
    		$splitnum = ceil($betnum / 10000);
    		$splitin =  array_pop($sortarr);
    		$splitstr = explode('*', $betstr[$xin]);
    		$betstrs = explode(',', $splitstr[$splitin]);
    		$betarr = explode('/', $betstrs[2]);
            $nbetnum_ = $betnum / count($betarr);
    		$splitarr = array_chunk($betarr, floor(count($betarr)/$splitnum));
    		foreach ($splitarr as $split)
    		{
    			$betstrs[3] = count($split);
    			$betstrs[2] = implode('/', $split);
    			$splitstr[$splitin] = implode(',', $betstrs);
    			$nbetnum = $nbetnum_ * $betstrs[3];
    			$betmoney = $nbetnum * $multi * 2 ;
    			$nbetstr = implode('*', $splitstr) . "|ZS={$nbetnum}";
    			if($betmoney > 20000 || $multi > 99)
    			{
    				$nmulti = $this->split_multi($betmoney, $multi);
    				$nsplitnum = ceil($multi / $nmulti);
    				for($nu = 1; $nu <= $nsplitnum; ++$nu)
    				{
    					if(($multi - $nmulti * $nu) < 0)
    					{
    						$nmulti = $multi % $nmulti;
    					}
    					$betstr[] = $nbetstr . ',BS=' . $nmulti . ',JE=' . ($nbetnum * $nmulti * 2);
    				}
    			}
    			else
    			{
    				$betstr[] = $nbetstr . ',BS=' . $multi . ',JE=' . $betmoney;
    			}
    		}
    	}
    	elseif($betmoney > 20000 || $multi > 99)
    	{
    		$betstr[$xin] .= "|ZS={$betnum}";
    		$nmulti = $this->split_multi($betmoney, $multi);
    		$nsplitnum = ceil($multi / $nmulti);
    		for($nu = 1; $nu <= $nsplitnum; ++$nu)
    		{
    			if(($multi - $nmulti * $nu) < 0)
    			{
    				$nmulti = $multi % $nmulti;
    			}
    			$betstr[] = $betstr[$xin] . ',BS=' . $nmulti . ',JE=' . ($betnum * $nmulti * 2);
    		}
    	}
    	unset($betstr[$xin]);
    }
    
    private function split_multi($betmoney, $multi)
    {
    	$splitnum = ceil($betmoney / 20000);
    	$nmulti = floor($multi / $splitnum);
    	return min(array($nmulti, 99));
    }
    
    private function recursive($matches, $ah, $at)
    {
    	$matchhs = array_keys($matches[$ah]);
    	$matchstr = array();
    	foreach ($matchhs as $matchh) 
    	{
    		if(count($at) > 1)
    		{
    			$atn = $at;
    			$ahn = array_shift($atn);
    			$substrs = $this->recursive($matches, $ahn, $atn);
    			foreach ($substrs as $substr) 
    			{
    				array_push($matchstr, "$ah,$matchh,".implode('/', $matches[$ah][$matchh]).",".count($matches[$ah][$matchh])."*$substr");
    			}
    		}
    		else 
    		{
    			if(empty($at[0]))
    			{
	    			array_push($matchstr, "$ah,$matchh,".implode('/', $matches[$ah][$matchh]).",".count($matches[$ah][$matchh]));
    			}
    			else 
    			{
    				$matchts = array_keys($matches[$at[0]]);
	    			foreach ($matchts as $matcht)
	    			{
	    				array_push($matchstr, "$ah,$matchh,".implode('/', $matches[$ah][$matchh]).",".count($matches[$ah][$matchh]).
	    				"*{$at[0]},$matcht,".implode('/', $matches[$at[0]][$matcht]).",".count($matches[$at[0]][$matcht]));
	    			}
	    			
    			}
    		}
    	}
    	return $matchstr;
    }
    	
	private function marge_arr($darr, $sarr)
	{
		foreach ($sarr as $val)
		{
			array_push($darr, $val);
		}
		return $darr;
	}
	
	public function combine($cn, $cm)
	{
		 $dividend = $this->factorial($cn, $cn - $cm + 1);
         $divisor  = $this->factorial($cm);
         return $dividend / $divisor;
	}
	
	private function factorial($cn, $cs = 0) 
	{
         if ($cn == 0) 
         {
             return 1;
         }
         $product = 1;
         ($cs > 0) || ($cs = 1);
         for ($i = $cs; $i <= $cn; ++$i) 
         {
             $product *= $i;
         }
         return $product;
	}
	
	private function combineList($arr, $ns) 
    {
            $len = count($arr);
            $pow = pow(2, $len);
            $result = array();
            for ($i = 0; $i < $pow; ++$i) 
            {
                    $tmp = array();
                    if ($this->bitCount($i) == $ns) 
                    {
                        for ($j = 0; $j < $len; ++$j) 
                        {
                            if (($i & (1 << $j)) != 0) 
                            {
                                array_push($tmp, $arr[$j]);
                            }
                        }
                    }
                    if(!empty($tmp))
                    	array_push($result, $tmp);
            }
            return $result;
    }
        
    private function bitCount($i)
    {
    	$count = 0;
    	while($i)
    	{
    		$count += 1;
    		$i &= ($i - 1);
    	}
    	return $count;
    } 
    
 	private function debug($matchcom)
    {
    	$je = 0;
    	foreach ($matchcom as $m1)
    	{
    		foreach ($m1 as $m2)
    		{
    			foreach ($m2['xtypes'] as $m3)
    			{
    				if(preg_match('/JE=(\d+)$/', $m3, $mp))
    				{
    					$je += intval($mp[1]);
    				}
    			}
    		}
    	}
    	log_message('LOG', $je);
    	log_message('LOG', print_r($matchcom, true));
    }
}
?>