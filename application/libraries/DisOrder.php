<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class DisOrder
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->library('libcomm');
	}
	
	public function dismantle_ball($codes, $rballs, $bballs, $check)
    {
    	$codes_arr = explode(';', $codes);
    	$rebetnum = 0;
    	$resultBets = array();
    	if(!empty($codes_arr))
    	{
    		foreach ($codes_arr as $betcode)
    		{
    			$betcodes = explode('|', $betcode);
    			$endstrs = explode(':', $betcodes[1]);
    			$preSalt = array();
    			$posSalt = array();
    			if((strpos($betcodes[0], '$') != false) || (strpos($endstrs[0], '$') != false))
    			{
    				$rTmp = explode('$', $betcodes[0]);
    				$preSalt = isset($rTmp[1]) ? explode(',', $rTmp[0]) : array();
    				$pre_balls = isset($rTmp[1]) ? explode(',', $rTmp[1]) : explode(',', $rTmp[0]);
    				$pre_cbt =  $this->CI->libcomm->combine(count($pre_balls), $rballs - count($preSalt));
    				$bTmp = explode('$', $endstrs[0]);
    				$posSalt = isset($bTmp[1]) ? explode(',', $bTmp[0]) : array();
    				$pos_balls = isset($bTmp[1]) ? explode(',', $bTmp[1]) : explode(',', $bTmp[0]);
    				$pos_cbt =  $this->CI->libcomm->combine(count($pos_balls), $bballs - count($posSalt));
    			}
    			else
    			{
    				$pre_balls = explode(',', $betcodes[0]);
    				$pre_cbt =  $this->CI->libcomm->combine(count($pre_balls), $rballs);
    				$pos_balls = explode(',', $endstrs[0]);
    				$pos_cbt =  $this->CI->libcomm->combine(count($pos_balls), $bballs);
    			}
    			$betnum = $pre_cbt * $pos_cbt;
    			$rebetnum += $betnum;
    			if(!$check)
    			{
	    			if($betnum > 10000)
	    			{
	    				$OneBets = array();
						$reAllBets = array();
						$OneBets['saltball'] = $preSalt;
						$OneBets['otherball'] = $pre_balls;
						$OneBets['betnum'] = $pre_cbt;
						$OneBets['playtype'] = $endstrs[1];
						$this->_dismantle_ball($reAllBets, $OneBets, $rballs);
						$pos_cbt_lists = $this->CI->libcomm->combineList($pos_balls, $bballs - count($posSalt));
						
						foreach ($reAllBets as $reAllBet)
						{
							$_betnum = $reAllBet['betnum'];
							if($_betnum * $pos_cbt > 10000)
							{
								if($bballs == 1)
								{//对一个两球彩种的特殊处理
									for($bnum = 1; $bnum <= count($pos_balls); )
									{
										if($reAllBet['betnum'] * $bnum >= 10000)
										{
											break;
										}
										$bnum++;
									}
									$pos_cbt_lists = array_chunk($pos_balls, --$bnum);
								}
								foreach ($pos_cbt_lists as $pos_cbt_list)
								{
									$reAllBet['bsaltball'] = $posSalt;
									$reAllBet['bluball'] = $pos_cbt_list;
									if($bballs == 1)
									{
										$reAllBet['betnum'] = $_betnum * count($pos_cbt_list);
									}
									array_push($resultBets, $reAllBet);
								}
							}
							else 
							{
								$reAllBet['bsaltball'] = $posSalt;
								$reAllBet['bluball'] = $pos_balls;
								$reAllBet['betnum'] =  $_betnum * $pos_cbt;
								array_push($resultBets, $reAllBet);
							}
						}
	    			}
	    			else 
	    			{
	    				$reAllBet['saltball'] = $preSalt;
	    				$reAllBet['otherball'] = $pre_balls;
	    				$reAllBet['bsaltball'] = $posSalt;
	    				$reAllBet['bluball'] = $pos_balls;
						$reAllBet['betnum'] = $betnum;
						$reAllBet['playtype'] = $endstrs[1];
						array_push($resultBets, $reAllBet);
	    			}
    			}
    		}
    	}
    	return array('betnum' => $rebetnum, 'betcbt' => $resultBets);
    }
    
	private function _dismantle_ball(&$reAllBets, $AllBet, $rballs)
	{
		$AllBets = array();
		$mballs = array();
		$rball = $rballs - count($AllBet['saltball']); //去掉胆的个数
		$betnum = $AllBet['betnum'];
		$balls = $AllBet['otherball'];
		$playtype = $AllBet['playtype'];
		if($betnum > 10000)
		{
			while($betnum > 10000)
			{
				array_push($mballs, array_shift($balls));
				$betnum =  $this->CI->libcomm->combine(count($balls), -- $rball);
			}
			$OneBets['saltball'] = $this->CI->libcomm->marge_arr($AllBet['saltball'], $mballs);
			$OneBets['otherball'] = $balls;
			$OneBets['betnum'] = $betnum;
			$OneBets['playtype'] = $playtype;
			array_push($reAllBets, $OneBets);
			$mballnum = count($mballs);
			if($mballnum > 1)
			{
				$zhnum = 1;
				while($zhnum < $mballnum)
				{
					$clists = $this->CI->libcomm->combineList($mballs, $zhnum);
					foreach ($clists as $clist)
					{
						$OneBets['saltball'] = $this->CI->libcomm->marge_arr($AllBet['saltball'], $clist);
						$OneBets['betnum'] = $this->CI->libcomm->combine(count($balls), $rballs - $zhnum);
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
			$OneBets['saltball'] = $this->CI->libcomm->marge_arr($AllBet['saltball'],  array());
			$OneBets['betnum'] = $this->CI->libcomm->combine(count($balls), $rballs - count($AllBet['saltball']));
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
					$this->_dismantle_ball($reAllBets, $AllBet, $rballs);
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
	
	/**
	 * 七乐彩拆票
	 * @param unknown_type $codes
	 * @param unknown_type $rballs
	 * @param unknown_type $check
	 */
	public function dismantle_qlc($codes, $rballs, $check)
	{
		$codes_arr = explode(';', $codes);
		$rebetnum = 0;
		$resultBets = array();
		if(!empty($codes_arr))
		{
			foreach ($codes_arr as $betcode)
			{
				$endstrs = explode(':', $betcode);
				$balls = explode(',', $endstrs[0]);
				$betnum =  $this->CI->libcomm->combine(count($balls), $rballs);
				$rebetnum += $betnum;
				if(!$check)
				{
					if($betnum > 10000)
					{
						$OneBets = array();
						$reAllBets = array();
						$OneBets['saltball'] = array();
						$OneBets['otherball'] = $balls;
						$OneBets['betnum'] = $betnum;
						$OneBets['playtype'] = $endstrs[1];
						$this->_dismantle_ball($reAllBets, $OneBets, $rballs);
	
						foreach ($reAllBets as $reAllBet)
						{
							array_push($resultBets, $reAllBet);
						}
					}
					else
					{
						$reAllBet['saltball'] = array();
						$reAllBet['otherball'] = $balls;
						$reAllBet['betnum'] = $betnum;
						$reAllBet['playtype'] = $endstrs[1];
						array_push($resultBets, $reAllBet);
					}
				}
			}
		}
		return array('betnum' => $rebetnum, 'betcbt' => $resultBets);
	}
	
	/**
	 * 竞彩单关拆票逻辑
	 * @param unknown_type $betstr
	 * @return multitype:number multitype: Ambigous <multitype:, multitype:string >
	 */
	public function _dismantle_single_match($betstr)
	{
		$betarr = explode('|', $betstr);
		$matcharr = explode(',', $betarr[1]);
		$rebetnum = 0;
		if(!empty($matcharr))
		{
			$matches = array();
			$matchnums = array();
			foreach ($matcharr as $match)
			{
				if(preg_match('/(.*?)>(.*?)=(.*)/i', $match, $map))
				{
					$odds = explode('/', $map[3]);
					foreach ($odds as $key => $odd)
					{
						$tmpArr = explode('@', $odd);
						$mKey = $map[2].','.$map[1];
						$matches[$mKey][$key]['val'] = $map[2] . ',' . $map[1] . ',' . $tmpArr[0] .',1';
						$matches[$mKey][$key]['BS'] = $tmpArr[1];
						$rebetnum++;
						$matchnums[] = $map[2];
					}
				}
			}
			$matchnums = array_unique($matchnums);
			$matchcom = array();
			foreach ($matches as $match)
			{
				foreach ($match as $bet)
				{
					if($bet['BS'] > 99)
					{
						$multis = $this->calMulti($bet['BS'], 99);
						foreach ($multis as $multi)
						{
							$matchcom[] = $bet['val'] . "|ZS=1,BS={$multi},JE=" . ($multi * 2) . '|' . $betarr[2];
						}
					}
					else
					{
						$matchcom[] = $bet['val'] . "|ZS=1,BS={$bet['BS']},JE=" . ($bet['BS'] * 2) . '|' . $betarr[2];
					}
				}
			}
			
			return array('betnum' => $rebetnum, 'betcbt' => $matchcom, 'matchnums' => $matchnums);
		}
	}
	
	/**
	 * 冠亚军拆票
	 * @param unknown_type $betstr
	 * @param unknown_type $check
	 */
	public function _dismantle_gyj($betstr, $multi, $check)
	{
		$betarr = explode('|', $betstr);
		$rebetnum = 0;
		$money = 0;
		$betcbt = array();
		if(preg_match('/(.*?)=(.*)/i', $betarr[1], $map))
		{
			$bet = explode('/', $map[2]);
			//判断解析与选择是否一致
			if(count($bet) == $betarr[2])
			{
				$rebetnum = $betarr[2];
				$money = $rebetnum * 2 * $multi;
				if(!$check)
				{
					if($multi > 99)
					{
						$multis = $this->calMulti($multi, 99);
						foreach ($multis as $val)
						{
							$betcbt[] = $betarr[1] . "|ZS={$rebetnum},BS={$val},JE=" . ($rebetnum * $val * 2);
						}
					}
					else
					{
						$betcbt[] = $betarr[1] . "|ZS={$rebetnum},BS={$multi},JE=" . ($rebetnum * $multi * 2);
					}
				}
			}
		}
		
		return array('betnum' => $rebetnum, 'money' => $money, 'playType' => $betarr[0], 'issue' => $map[1], 'betcbt' => ($check ? array() : $betcbt));
	}
	
	/**
	 * 竞彩奖金优化拆票逻辑
	 * @param unknown_type $betstr
	 */
	public function _dismantle_optimization($betstr)
	{
		$betstrs = explode(';', $betstr);
		$rebetnum = 0;
		$matches = array();
		$matchnums = array();
		foreach ($betstrs as $key => $code)
		{
			$codes = explode('|', $code);
			$matcharr = explode(',', $codes['1']);
			if(!empty($matcharr))
			{
				$valStr = array();
				foreach ($matcharr as $match)
				{
					if(preg_match('/(.*?)>(.*?)=(.*)/i', $match, $map))
					{
						$valStr[] = $map['2'] . ',' . $map['1'] . ',' . $map['3'] . ',1';
						$matchnums[] = $map['2'];
					}
				}
				$matches[$key]['val'] = implode('*', $valStr);
				$matches[$key]['BS'] = $codes[2];
				$matches[$key]['playType'] = $codes[3];
				$matches[$key]['subCodeId'] = $key + 1;
				$rebetnum ++;
			}
		}
		$matchnums = array_unique($matchnums);
		$matchcom = array();
		foreach ($matches as $match)
		{
			if($match['BS'] > 99)
			{
				$multis = $this->calMulti($match['BS'], 99);
				foreach ($multis as $val)
				{
					$matchcom[] = $match['val'] . "|ZS=1,BS={$val},JE=" . ($val * 2) . '|' . $match['playType'] . '|' . $match['subCodeId'];
				}
			}
			else
			{
				$matchcom[] = $match['val'] . "|ZS=1,BS={$match['BS']},JE=" . ($match['BS'] * 2) . '|' . $match['playType'] . '|' . $match['subCodeId'];
			}
		}
		return array('betnum' => $rebetnum, 'betcbt' => $matchcom, 'matchnums' => $matchnums);
	}
	
	/**
	 * 拆倍数
	 * @param unknown_type $multi
	 * @param unknown_type $omulti
	 * @return Ambigous <multitype:, multitype:number >
	 */
	private function calMulti($multi, $omulti)
	{
		$tmpmultis = array();
		//拆倍数
		$tmpmulti = intval($multi / $omulti);
		if($tmpmulti > 0)
		{
			$tmpmultis = array_fill(0, $tmpmulti, $omulti);
		}
		$remainder = $multi % $omulti;
		if($remainder)
		{
			$tmpmultis[] = $remainder;
		}
		return $tmpmultis;
	}
    
	/**
	 * 竞彩混合玩法拆票逻辑
	 * @param unknown_type $betstr
	 * @param unknown_type $multi
	 * @return multitype:unknown
	 */
	public function _dismantle_match($betstr, $multi, $betTnum)
    {
    	$betarr = explode('|', $betstr);
    	$ggtypes = explode(',', $betarr[2]);
    	$matcharr = explode(',', $betarr[1]);
    	$betnum = 0;
    	$betcbt = array();
    	$matchnums = array();
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
            /*
             * 自由过关判断
             * 需要排除的情况是容错、单关、1万注以上、>=8场以上、一场比赛选择两个以上玩法
             * */
            if($betTnum <= 10000){
                $redatas = $this->free_match($matches, $ggtypes, count($matchnums));
                if(!empty($redatas)){
                    $betmoney = $betTnum * $multi * 2;
                    if(($multi > 99) || ($betmoney > 20000)){
                        //倍数或金额超标
                        $multis = $this->free_dismulti($betmoney, $multi);
                        foreach ($multis as $mult){
                            $matchcom[] =  $redatas['codes'] . "|ZS={$betTnum},BS={$mult},JE=" . $betTnum * $mult * 2 . "|{$redatas['ptint']}";
                        }
                    }else{
                        $matchcom[] =  $redatas['codes'] . "|ZS={$betTnum},BS={$multi},JE=" . $betmoney . "|{$redatas['ptint']}";
                    }
                    return array('betnum' => $betTnum, 'betcbt' => $matchcom, 'matchnums' => $matchnums, 'ggtype' => '9*9');
                }
            }
            $detailMatch = array();
    		if(!empty($ggtypes))
    		{
    			foreach ($ggtypes as $ggtype)
    			{
    				$ggnum = preg_replace('/\*\d+$/', '', $ggtype);
    				$matchcoms = $this->CI->libcomm->combineList($matchnums, intval($ggnum)); //先场次组合
    				foreach ($matchcoms as $key => $matchone)
    				{
    					$at = array_values($matchone);
    					$ah = array_shift($at);
    					$xtypes = $this->recursive($matches, $ah, $at); //玩法组合
    					foreach ($xtypes as $xtype)
    					{
    						$detailMatch[$ggtype][] = $xtype;
    					}
    				}
    			}
    		}
    		//释放处理变量
    		unset($matcharr, $matches, $ggtypes);
    		$matchcom = array();
    		foreach ($detailMatch as $ggtype => $xtypes)
    		{
    			foreach ($xtypes as $key => $xtype)
    			{
    				$matchs = explode('*', $xtype);
    				$sortarr = array();
    				$matchSelected = array();
    				foreach ($matchs  as $xin => $type)
    				{
    					preg_match('/,(\d+)$/i', $type, $maps);
    					$sortarr[intval($maps[1])] = $xin;
    					$matchSelected[] = intval($maps[1]);
    				}
    				$codeBetNum = $this->CI->libcomm->calBetNum($ggtype, $matchSelected);
    				$betnum += $codeBetNum;
    				if($codeBetNum > 10000 || $multi > 99 || ($codeBetNum * $multi * 2 > 20000))
    				{
    					$splits = $this->splitRongCuoOrder($detailMatch[$ggtype][$key], $codeBetNum, $multi, $sortarr, $ggtype);
    					foreach ($splits as $split)
    					{
    						$matchcom[] = $split;
    					}
    				}
    				else
    				{
    					$matchcom[] = $detailMatch[$ggtype][$key] .= "|ZS={$codeBetNum},BS={$multi},JE=" . ($codeBetNum * $multi * 2) . "|" . $ggtype;
    				}
    			}
    		}
    	}
    	return array('betnum' => $betnum, 'betcbt' => $matchcom, 'matchnums' => $matchnums);
    }

    private function free_dismulti($betmoney, $multi){
        $nmulti = $this->split_multi($betmoney, $multi);
        $nsplitnum = ceil($multi / $nmulti);
        $data = array();
        for($nu = 1; $nu <= $nsplitnum; ++$nu)
        {
            if(($multi - $nmulti * $nu) < 0)
            {
                $nmulti = $multi % $nmulti;
            }
            $data[] = $nmulti;
        }
        return $data;
    }

    /*
     * $matches[matchnum][playtypenum] = 复选数组和赔率;
     * */
    private function free_match($matches, $ggtypes, $mnum){
    	// 竞彩足球 - 福牛牛暂无自由过关玩法处理
    	return false;
        //判断过关方式是否符合
        $ggmaps = array(
            '2*1' => 2, '3*1' => 3, '4*1' => 4, '5*1' => 5, '6*1' => 6, '7*1' => 7, '8*1' => 8
        );
        $min = 10;
        $ptint = 0;
        foreach ($ggtypes as $ggtype){
            if(empty($ggmaps[$ggtype])){
                return false;
            }else{
                $ptint ^= (1 << ($ggmaps[$ggtype]-2));
                if($ggmaps[$ggtype] < $min){
                    $min = $ggmaps[$ggtype];
                }
            }
        }
        //判断场次是否大于最小串关
        if($mnum <= $min || $mnum > 8){
            return false;
        }
        //判断单场玩法是否符合
        $codes = '';
        foreach ($matches as $match => $ptye){
            if(count($ptye) > 1) {
                return false;
            }
            $codes .= $match;
            foreach ($ptye as $pt => $opts){
                $codes .= ",$pt," . implode('/', $opts) . "," . count($opts) . "*";
            }
        }
        return array('codes' => preg_replace('/\*$/is', '', $codes), 'ptint' => $ptint);
    }
	/**
     * 包含容错过关订单拆票
     * @param unknown_type $betstr
     * @param unknown_type $betnum
     * @param unknown_type $multi
     * @param unknown_type $sortarr
     * @param unknown_type $ggtype
     */
    private function splitRongCuoOrder($betstr, $betnum, $multi, $sortarr, $ggtype)
    {
    	$data = array();
    	$betmoney = $betnum * $multi * 2 ;
    	if($betnum > 10000)
    	{
    		ksort($sortarr);
    		$splitnum = ceil($betnum / 10000);
    		$splitin =  array_pop($sortarr);
    		$splitstr = explode('*', $betstr);
    		$betstrs = explode(',', $splitstr[$splitin]);
    		$betarr = explode('/', $betstrs[2]);
    		$splitarr = array_chunk($betarr, floor(count($betarr)/$splitnum));
    		$matchSelected = array();
    		foreach ($splitstr  as $key => $type)
    		{
    			if($key == $splitin)
    			{
    				continue;
    			}
    			preg_match('/,(\d+)$/i', $type, $maps);
    			$matchSelected[] = intval($maps[1]);
    		}
    		foreach ($splitarr as $split)
    		{
    			$selectValue = $matchSelected;
    			$selectValue[] = count($split);
    			$nbetnum = $this->CI->libcomm->calBetNum($ggtype, $selectValue);
    			$betstrs[2] = implode('/', $split);
    			$betstrs[3] = count($split);
    			$splitstr[$splitin] = implode(',', $betstrs);
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
    					$data[] = $nbetstr . ',BS=' . $nmulti . ',JE=' . ($nbetnum * $nmulti * 2). "|" . $ggtype;
    				}
    			}
    			else
    			{
    				$data[] = $nbetstr . ',BS=' . $multi . ',JE=' . $betmoney. "|" . $ggtype;
    			}
    		}
    	}
    	elseif($betmoney > 20000 || $multi > 99)
    	{
    		$betstr .= "|ZS={$betnum}";
    		$nmulti = $this->split_multi($betmoney, $multi);
    		$nsplitnum = ceil($multi / $nmulti);
    		for($nu = 1; $nu <= $nsplitnum; ++$nu)
    		{
    			if(($multi - $nmulti * $nu) < 0)
    			{
    				$nmulti = $multi % $nmulti;
    			}
    			$data[] = $betstr . ',BS=' . $nmulti . ',JE=' . ($betnum * $nmulti * 2). "|" . $ggtype;
    		}
    	}
    	
    	return $data;
    }
    
    /**
     * 竞彩玩法组合
     * @param unknown_type $matches
     * @param unknown_type $ah
     * @param unknown_type $at
     * @return multitype:
     */
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
	
	public function _dismantle_rj($codestr, $check)
	{
		$codes_arr = explode(';', $codestr);
		$rebetnum = 0;
		$resultBets = array();
		foreach ($codes_arr as $code)
		{
			$codearr = explode(':', $code);
			$codestrs = explode(',', $codearr[0]);
			$codearrs = array();
			$mcode = array();
			foreach ($codestrs as $in => $codestr)
			{
				$codearrs[$in+1] = str_split($codestr);
				if($codestr == '#')
				{
					continue;
				}
				$mcode[] = $in + 1;
			}
			$combineList = $this->CI->libcomm->combineList($mcode, 9);	//场次组合
			$allCodes = array();
			$this->dismantle_recursive_rj($codearrs, $combineList, $allCodes, $check);
			$betnums = 0;
			foreach ($allCodes as $allCode)
			{
				$num = 1;
				foreach ($allCode as $bets)
				{
					$num *= count($bets);
				}
				$betnums += $num;
				array_push($resultBets, $allCode);
			}
			$rebetnum += $betnums;
		}
		
		return array('betnum' => $rebetnum, 'betcbt' => ($check ? array() : $resultBets));
	}
	
	private function dismantle_recursive_rj($codearrs, $combineList, &$allCodes, $check)
	{
		foreach ($combineList as $mcodearr)
		{
			$newcodearrs = array();
			for($i = 1; $i <= 14; ++$i)
			{
				$newcodearrs[$i] = array(4); //初始化比赛场次为未选中
			}
			$onebetnums = 1;
			foreach ($mcodearr as $mcode)
			{
				$num = count($codearrs[$mcode]);
				$onebetnums *= $num;
				$sortarr[$num] = $mcode;
				$newcodearrs[$mcode] = $codearrs[$mcode];
			}
			if(!$check)
			{
				if($onebetnums > 10000)
				{
					ksort($sortarr);
					$in = array_pop($sortarr);
					$splinum = $onebetnums / 10000;
					$onebetnums = $onebetnums / count($newcodearrs[$in]);
					$arrsize = floor(count($newcodearrs[$in]) / $splinum);
					$arrsize = $arrsize > 0 ? $arrsize : 1;
					$splitarrs = array_chunk($newcodearrs[$in], $arrsize);
					foreach ($splitarrs as $splitarr)
					{
						$newcodearrs[$in] = $splitarr;
						$onebetnums *= count($splitarr);
						if($onebetnums <= 10000)
						{
							array_push($allCodes, $newcodearrs);
						}
						else
						{
							$this->dismantle_recursive_rj($newcodearrs, $combineList, $allCodes, $check);
						}
					}
				}
				else
				{
					array_push($allCodes, $newcodearrs);
				}
			}
			else
			{
				array_push($allCodes, $newcodearrs);
			}
		}
	}
	
	/**
	 * 3D和排列三拆票公用方法
	 * @param unknown_type $codestr
	 * @param unknown_type $check
	 */
	public function dismantle_3dAndpls($codestr, $check)
	{
		$codes_arr = explode(';', $codestr);
		$rebetnum = 0;
		$playtype = 0;
		$resultBets = array();
		foreach ($codes_arr as $codes)
		{
			$strs = explode(':', $codes);
			$balls = explode(',', $strs[0]);
			$playtype = $strs[1];
			if($playtype == 1)
			{
				$results = $this->_dismantle_number($codes, $check);
				$rebetnum += $results['betnum'];
				array_push($resultBets, $results);
			}
			elseif($playtype == 2)
			{
				if($strs[2] == 1)
				{
					$betnum =  $this->CI->libcomm->combine(count($balls), 3);
				}
				else
				{
					$betnum =  $this->CI->libcomm->combine(count($balls), 2);
					$betnum *= 2;
				}
				$rebetnum += $betnum;
				if(!$check)
				{
					$all = array(
							'playtype' => $playtype,
							'betnum' => $betnum,
							'betcbt' => $balls
					);
					array_push($resultBets, $all);
				}
			}
			elseif ($playtype == 3)
			{
				$betnum =  $this->CI->libcomm->combine(count($balls), 3);
				$rebetnum += $betnum;
				if(!$check)
				{
					$all = array(
							'playtype' => $playtype,
							'betnum' => $betnum,
							'betcbt' => $balls
					);
					array_push($resultBets, $all);
				}
			}
		}
		return array('betnum' => $rebetnum, 'betcbt' => $resultBets);
	}
	
	/**
	 * 十一选五
	 * @param unknown_type $codestr
	 * @param unknown_type $check
	 * @param int $lid   彩种lid
	 */
	public function dismantle_syxw($codestr, $check, $lid = '')
	{
		$codes_arr = explode(';', $codestr);
		$rebetnum = 0;
		$totalMoney = 0;
		$resultBets = array();
		foreach ($codes_arr as $code)
		{
			$ballArr = array();
			$codes = explode(':', $code);
			$isSalts = intval($codes['2']) == '5' ? true : false;
			$ballArr['playtype'] = intval($codes['1']);
			$ballArr['isSalts'] = $isSalts;
			$ballArr['salts'] = array();
			$ballArr['balls'] = array();
			if($isSalts)
			{
				$ball = explode('$', $codes[0]);
				$ballArr['salts'] = explode(',', $ball[0]);
				$ballArr['balls'] = explode(',', $ball[1]);
				$betNum = $this->getSyxwBetNum($ballArr, $isSalts);
				$rebetnum += $betNum;
				$ballArr['betnum'] = $betNum;
			}
			else
			{
				if(in_array($ballArr['playtype'], array(9,10)))
				{
					$balls = explode('|', $codes[0]);
					foreach ($balls as $ballstr)
					{
						$ball = explode(',', $ballstr);
						array_push($ballArr['balls'], $ball);
					}
				}
				else if($ballArr['playtype'] == 13)//乐3按'|'分割,乐4、乐5分割','分隔
				{
					$ballArr['balls'] = explode('|', $codes[0]);
				}
				else
				{
					$ballArr['balls'] = explode(',', $codes[0]);
				}
				//乐3 乐4 乐5 都是单式 每一个code 只有1注 13，14,15分别对应乐3 乐4 乐5玩法
				if(in_array($ballArr['playtype'], array(13,14,15)))
				{
					$betNum = 1;
				}else
				{
					$betNum = $this->getSyxwBetNum($ballArr, $isSalts);	
				}
				//$totalMoney += $this->getMoney($ballArr['playtype']) * $betNum;
				$rebetnum += $betNum;
				$ballArr['betnum'] = $betNum;
			}
			
			//任选八复试拆成单式
			if((!$check) && ($ballArr['playtype'] == 8))
			{
				if($ballArr['betnum'] > 1)
				{
					$bList = $this->CI->libcomm->combineList($ballArr['balls'], 8);
					foreach ($bList as $val)
					{
						$ballArr['balls'] = $val;
						$ballArr['betnum'] = 1;
						array_push($resultBets, $ballArr);
					}
				}
				else
				{
					array_push($resultBets, $ballArr);
				}
			}
			elseif(in_array($ballArr['playtype'], array(9,10)) && $lid = '21407')
			{
			    if($ballArr['betnum'] > 1)
			    {
			        $bList = $this->getSyxwZxDismantle($ballArr['balls'], $ballArr['playtype']);
			        foreach ($bList as $val)
			        {
			            $ballArr['balls'] = $val;
			            $ballArr['betnum'] = 1;
			            array_push($resultBets, $ballArr);
			        }
			    }
			    else
			    {
			        array_push($resultBets, $ballArr);
			    }
			}
			else
			{
				array_push($resultBets, $ballArr);
			}
		}
		
		return array('betnum' => $rebetnum, 'betcbt' => ($check ? array() : $resultBets));
	}
	
	/**
	 * 十一选五直选玩法拆成单式
	 * @param unknown $balls
	 * @param unknown $playtype
	 */
	private function getSyxwZxDismantle($balls, $playtype)
	{
	    $datas = array();
	    if($playtype == '9')
	    {
	        foreach ($balls[0] as $val0)
	        {
	            foreach ($balls[1] as $val1)
	            {
	                $data = array();
	                $data['0'] = array($val0);
	                $data['1'] = array($val1);
	                $datas[] = $data;
	            }
	        }
	    }
	    elseif($playtype == '10')
	    {
	        foreach ($balls[0] as $val0)
	        {
	            foreach ($balls[1] as $val1)
	            {
	                foreach ($balls[2] as $val2)
	                {
	                    $data = array();
	                    $data['0'] = array($val0);
	                    $data['1'] = array($val1);
	                    $data['2'] = array($val2);
	                    $datas[] = $data;
	                }
	            }
	        }
	    }
	    
	    return $datas;
	}
	
	/**
	 * 计算十一选五注数
	 * @param unknown_type $ballArr
	 * @param unknown_type $isSalts
	 */
	private function getSyxwBetNum($ballArr, $isSalts)
	{
		$betNum = 0;
		switch ($ballArr['playtype'])
		{
			case 1:
				$betNum = count($ballArr['balls']);
				break;
			case 9:
				$betNum = count($ballArr['balls']['0']) * count($ballArr['balls']['1']);
				break;
			case 10:
				$betNum = count($ballArr['balls']['0']) * count($ballArr['balls']['1']) * count($ballArr['balls']['2']);
				break;
			default:
				$cm = $ballArr['playtype'] == 11 ? 2 : ($ballArr['playtype'] == 12 ? 3 : $ballArr['playtype']);
				if(!$isSalts)
				{
					$betNum = $this->CI->libcomm->combine(count($ballArr['balls']), $cm);
				}
				else
				{
					$betNum = $this->CI->libcomm->combine(count($ballArr['balls']), $cm - count($ballArr['salts']));
				}
				break;
		}
		return $betNum;
	}

	/**
	 * 计算快乐扑克注数
	 * @param unknown_type $ballArr
	 * @param unknown_type $isSalts
	 */
	private function getKlpkBetNum($ballArr)
	{
		$betNum = 0;

		if(in_array($ballArr['playtype'], array('1', '2', '21', '3', '31', '4', '41', '5', '51', '6', '61')))
		{
			// 任选一及任选X单复试玩法 C(N,X)
			$codesArry = explode(',', $ballArr['codes']);
			$cm = substr($ballArr['playtype'], 0, 1);
			$betNum = $this->CI->libcomm->combine(count($codesArry), $cm);
		}
		elseif(in_array($ballArr['playtype'], array('22', '32', '42', '52', '62')))
		{
			// 任选胆拖处理 
			$ball = explode('$', $ballArr['codes']);
			$danArry = explode(',', $ball[0]);
			$tuoArry = explode(',', $ball[1]);

			if($ballArr['playtype'] == '22')
			{
				// 任二胆拖
				$betNum = $this->CI->libcomm->combine(count($tuoArry), 1);
			}
			else
			{
				$cm = substr($ballArr['playtype'], 0, 1);
				$cm = $cm - count($danArry);
				$betNum = $this->CI->libcomm->combine(count($tuoArry), $cm);
			}
		}
		return $betNum;
	}

	/**
	 * 计算老时时彩注数
	 * @param unknown_type $ballArr
	 * @param unknown_type $isSalts
	 */
	private function getCqsscBetNum($ballArr, $check)
	{
		$betNum = 0;
		switch ($ballArr['playtype'])
		{
			case 10:
			case 43:
			case 31:
			case 41:
				$betNum = 1;
				$codesArr = explode(',', $ballArr['codes']);
				if(!empty($codesArr))
				{
					foreach ($codesArr as $code) 
					{
						// 拆分最小单位
						$betNum = $betNum * strlen($code);
					}
				}
				break;
			case 27:
				// 二星组选复式
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 2);
				break;
			case 37:
				// 三星组三复式
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 2);
				$betNum *= 2;
				break;
			case 38:
				$betNum = $this->CI->libcomm->combine(count(explode(',', $ballArr['codes'])), 3);
				break;
			default:
				$results = $this->_dismantle_number($ballArr['codes'], $check);
				$betNum = $results['betnum'];
				break;
		}
		return $betNum;
	}
	
	/**
	 * 排列彩种玩法公用方法
	 * @param unknown_type $codestr
	 * @param unknown_type $check
	 */
	public function dismantle_plcomm($codestr, $check)
	{
		$codes_arr = explode(';', $codestr);
		$rebetnum = 0;
		$playtype = 0;
		$resultBets = array();
		foreach ($codes_arr as $codes)
		{
			$results = $this->_dismantle_number($codes, $check);
			$rebetnum += $results['betnum'];
			array_push($resultBets, $results);
		}
		
		return array('betnum' => $rebetnum, 'betcbt' => $resultBets);
	}
	
	public function _dismantle_number($codestr, $check)
	{
		$codestrmulti = explode(';', $codestr);
		$allCodes = array();
		foreach ($codestrmulti as $codearrone)
		{
			$codearr = explode(':', $codearrone);
			$codestrs = explode(',', $codearr[0]);
			$codearrs = array();
			$sortarr = array();
			foreach ($codestrs as $in => $codestr)
			{
				$codearrs[$in] = str_split($codestr);
			}
			$this->dismantle_recursive_number($codearrs, $allCodes, $check);
		}
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
		return array('playtype' => $codearr[1],'betnum' => $betnums, 'betcbt' => ($check ? array() : $allCodes));
	}
	
	private function dismantle_recursive_number($codearrs, &$allCodes, $check)
	{
		$betnums = 1;
		foreach ($codearrs as $in => $codearr)
		{
			$nums = count($codearr);
			$sortarr[$nums] = $in;
			$betnums *= $nums;
		}
		if(!$check)
		{
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
						$this->dismantle_recursive_number($codearrs, $allCodes, $check);
					}
				}
			}
			else
			{
				array_push($allCodes, $codearrs);
			}
		}
		else
		{
			array_push($allCodes, $codearrs);
		}
	}
	/**
	 * [dismantle_ks 易快3 经典快3 拆票]
	 * @author LiKangJian 2017-11-14
	 * @param  [type]  $codestr [description]
	 * @param  boolean $check   [description]
	 * @return [type]           [description]
	 */
	public function dismantle_ks($codestr, $check = false)
    {
    	//3,4,5:1:1
    	$codes = explode(';', $codestr);
    	if(!empty($codes))
    	{
    		$allcode = array();
    		$betnum = 0;
    		foreach ($codes as $code)
    		{
    			if(empty($code)) break;
    			$codearr = explode(':', $code);
    			switch ($codearr[1]) 
    			{
    				case '1':
    					$nums = explode(',', $codearr[0]);
    					foreach ($nums as $in => $num)
    					{
    						$betnum = $betnum + 1 * $codearr[2];
    						$allcode[] = array('codes' => "$num", 'playtype' => $codearr[1], 'betnum' => 1, 'multi' => $codearr[2]);
    					}
    					break;
    				case '2':
    				case '3':
    				case '4':
    				case '5':
    				case '6':
    				case '7':
    				case '8':
    					$allcode[] = array('codes' => "{$codearr[0]}", 'playtype' => $codearr[1], 'betnum' => 1, 'multi' => $codearr[2]);
    					$betnum = $betnum + 1 * $codearr[2];
    					break;
    				default:
    					break;
    			}
    		}
    		return array('betcbt' => $allcode, 'betnum' => $betnum);
    	}
    }

    // 快乐扑克
    public function dismantle_klpk($codestr, $check = false)
    {
    	$codes = explode(';', $codestr);
    	if(!empty($codes))
    	{
    		$allcode = array();
    		$betnum = 0;
    		foreach ($codes as $code)
    		{
    			if(empty($code)) break;
    			$codearr = explode(':', $code);
    			
    			// 花色玩法拆分处理
    			if(in_array($codearr[1], array('7', '8', '9', '10', '11', '12')))
    			{
    				$codeArr = explode(',', $codearr[0]);
    				$bx = '00';
    				if(in_array($bx, $codeArr))
    				{
    					$allcode[] = array(
							'codes' => $bx, 
							'playtype' => $codearr[1], 
							'betnum' => 1, 
							'multi' => $codearr[2]
						);  				
						$betnum += 1 * $codearr[2];
    				}

    				$codeTmp = array_merge(array_diff($codeArr, array($bx)));
    				if(!empty($codeTmp))
    				{
    					$betNums = count($codeTmp);
	    				$allcode[] = array(
							'codes' => implode(',', $codeTmp), 
							'playtype' => $codearr[1], 
							'betnum' => $betNums, 
							'multi' => $codearr[2]
						);  				
						$betnum += $betNums;
    				}				
    			}
    			else
    			{
    				$ballArr = array(
	    				'codes' => $codearr[0],
	    				'playtype' => $codearr[1]
	    			);
	    			// 根据号码玩法计算注数
					$betNums = $this->getKlpkBetNum($ballArr);

					// 胆拖投注串替换
					$betcodes = str_replace('$', '#', $codearr[0]);

					$allcode[] = array(
						'codes' => $betcodes, 
						'playtype' => $codearr[1], 
						'betnum' => $betNums, 
						'multi' => $codearr[2]
					);  				
					$betnum += $betNums;
    			}
    		}
    		return array('betcbt' => $allcode, 'betnum' => $betnum);
    	}
    }

    // 老时时彩
    public function dismantle_cqssc($codestr, $check = false)
    {
    	$codes = explode(';', $codestr);
    	if(!empty($codes))
    	{
    		$allcode = array();
    		$betnum = 0;
    		foreach ($codes as $code)
    		{
    			if(empty($code)) break;
    			$codearr = explode(':', $code);

    			// 一星直选 五星通选 仅支持单式
    			if(in_array($codearr[1], array('10', '43')))
    			{
    				// 拆单式方法
    				$codeArr = $this->dismantle_sigle_codes($codearr[0]);
    				foreach ($codeArr as $num) 
    				{
    					$ballArr = array(
		    				'codes' => implode(',', $num),
		    				'playtype' => $codearr[1]
		    			);

		    			// 根据号码玩法计算注数
						$betNums = $this->getCqsscBetNum($ballArr, $check);

						$allcode[] = array(
							'codes' => implode(',', $num), 
							'playtype' => $codearr[1], 
							'betnum' => $betNums, 
							'multi' => $codearr[2]
						);  				
						$betnum += $betNums;
    				}
    			}
                        //二星组选仅支持单式
                        elseif($codearr[1] == 27)
                        {
                            $codesData = array();
                            $codesArr = explode(',', $codearr[0]);
                            for ($i = 0; $i < count($codesArr); $i++) 
                            {
                                for ($j = $i; $j < count($codesArr); $j++) 
                                {
                                    if($codesArr[$i]!=$codesArr[$j]){
                                        $codesData[]=array($codesArr[$i],$codesArr[$j]);
                                    }
                                }
                            }
    				foreach ($codesData as $num) 
    				{
    					$ballArr = array(
		    				'codes' => implode(',', $num),
		    				'playtype' => 23
		    			);

		    			// 根据号码玩法计算注数
						$betNums = $this->getCqsscBetNum($ballArr, $check);

						$allcode[] = array(
							'codes' => implode(',', $num), 
							'playtype' => 23, 
							'betnum' => $betNums, 
							'multi' => $codearr[2]
						);  				
						$betnum += $betNums;
    				}                            
                        }
    			elseif(in_array($codearr[1], array('40', '41')))
    			{
    				// 五星直选 注释可能超过一万注
    				$results = $this->_dismantle_number($codearr[0], $check);
    				if($results['betcbt'])
    				{
    					foreach ($results['betcbt'] as $betcodes) 
    					{
    						$betCodeArr = array();
    						$oneBetNum = 1;
    						foreach ($betcodes as $betcode) 
    						{
    							$oneBetNum *= count($betcode);
    							array_push($betCodeArr, implode('', $betcode));
    						}

	    					$allcode[] = array(
								'codes' => implode(',', $betCodeArr), 
								'playtype' => $codearr[1], 
								'betnum' => $oneBetNum, 
								'multi' => $codearr[2]
							);  
	    				}
    				}
    				
    				$betnum += $results['betnum'];
    			}
    			else
    			{
    				$ballArr = array(
	    				'codes' => $codearr[0],
	    				'playtype' => $codearr[1]
	    			);

    				// 根据号码玩法计算注数
					$betNums = $this->getCqsscBetNum($ballArr, $check);

					$allcode[] = array(
						'codes' => $codearr[0],
						'playtype' => $codearr[1], 
						'betnum' => $betNums, 
						'multi' => $codearr[2]
					);  				
					$betnum += $betNums;
    			}
    		}
    		return array('betcbt' => $allcode, 'betnum' => $betnum);
    	}
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
    
	private function split_multi($betmoney, $multi)
    {
    	$splitnum = $betmoney / 20000;
    	$nmulti = floor($multi / $splitnum);
    	return min(array($nmulti, 99));
    }

    private function dismantle_sigle_codes($codes)
    {
    	$codesData = array();
    	$codesArr = explode(',', $codes);
    	for ($i = 0; $i < count($codesArr); $i++) 
    	{ 
    		$splitArr = str_split($codesArr[$i], 1);
    		for ($j = 0; $j < count($splitArr); $j++) 
    		{ 
    			$codesData[$i][$j] = $splitArr[$j];
    		}
    	}
    	return $this->CI->libcomm->dismantleSigleCodes($codesData);
    }
}