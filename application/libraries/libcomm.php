<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要: 银行卡验证
 * 作    者: lizq
 * 来    源: www.yinhangkahao.com/bank_luhm.html
 * 修改日期: 2014-11-25 
 */

class LibComm
{
	private $CI;
	public function __construct()
	{
		$this->CI = &get_instance();
        $this->CI->load->library('tools');
	}
	
	public function combine($cn, $cm)
	{
		 if($cn >= $cm && $cn >= 0 && $cm >= 0)
		 {
			 $dividend = $this->factorial($cn, $cn - $cm + 1);
	         $divisor  = $this->factorial($cm);
	         return $dividend / $divisor;
		 }
		 else
		 {
		 	return 0;
		 }
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
	
	public function marge_arr($darr, $sarr)
	{
		foreach ($sarr as $val)
		{
			array_push($darr, $val);
		}
		return $darr;
	}
	
	public function combineList($arr, $ns) 
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
	
    /**
     * 格式化彩种期号
     * @param int $issue	期号
     * @param int $flag		1加  0减
     * @param int $len		加或减长度
     * @return string
     */
	public function format_issue($issue, $flag = 1, $len = 2)
	{
		if($len <= 0 || empty($issue))
			return $issue;
		if($flag)
		{
			$issue = substr(date('Y'), 0, $len) . $issue;
		}
		else
		{
			$issue = substr($issue, $len);
		}
		
		return $issue;
	}
	
	public function refreshCache($ctype)
	{
		$CI = & get_instance();
		$ctypemap = array(
			'jxsyxw' => 'refreshJxSyxwAwards',
			'syxw'   => 'refreshSyxwAwards',
		);
		if(isset($ctypemap[$ctype]))
		{
			$cmd_path = $CI->config->item('cmd_path');
			$php_path = $CI->config->item('php_path');
			system("{$php_path} {$cmd_path} cron/cli_cfg_syncr_data cfg_{$ctype}_score", $status);
			$CI->load->model('lottery_model', 'Lottery');
			if(method_exists($CI->Lottery, $ctypemap[$ctype]))
			{
				$CI->Lottery->$ctypemap[$ctype]();
			}
		}
		elseif($ctype == 'ks') 
		{
			$lidmap = array('ks' => '53');
			$CI->load->model('ticket_model');
			$CI->ticket_model->updateStop(1, $lidmap[$ctype], 0);
		}
	}
	
	/**
     * 将投注数组转换成字符串
     * @param unknown_type $betcbts
     * @return multitype:
     */
    public function betsToStr($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	foreach ($betcbts as $betcbt)
    	{
    		$onebet = array();
    		$betnum = 1;
    		$codes_arr = array();
    		foreach ($betcbt as $bet)
    		{
    			$betnum *= count($bet);
    			$codes_arr[] = implode(',', $bet);
    		}
    		$codes = implode('*', $codes_arr);
    		$onebet['codes'] = $codes;
    		$onebet['betnum'] = $betnum;
    		array_push($betArrs, $onebet);
    	}
    	return $betArrs;
    }
    
	/**
     * 将单注彩票合并到一张票
     * @param unknown_type $betcbts
     * @return multitype:
     */
    public function oneBetsToStr($betcbts)
    {
    	$betArrs = array();
    	$onebets = array();
    	foreach ($betcbts as $betcbt)
    	{
    		$onebet = array();
    		$playtype = isset($betcbt['playtype']) ? $betcbt['playtype'] : 1;
    		$onebet['codes'] = $betcbt['codes'];
    		$onebet['betnum'] = $betcbt['betnum'];
    		$onebet['playtype'] = $playtype;
    		if($betcbt['betnum'] > 1)
    		{
    			array_push($betArrs, $onebet);
    		}
    		else
    		{
    			$onebets[$playtype][] = $onebet;
    		}
    	}
    	//相同类型的单式票每5注合并到一张票
    	foreach ($onebets as $onebet)
    	{
    		$counts = 0;
    		$tmp = array();
    		foreach ($onebet as $v)
    		{
    			$tmp['codes'] .= $v['codes'] . '^';
    			$tmp['betnum'] += $v['betnum'];
    			$tmp['playtype'] = $v['playtype'];
    			if(++$counts >= 5)
    			{
    				array_push($betArrs, $tmp);
    				$tmp = array();
    				$counts = 0;
    			}
    		}
    		
    		if($counts > 0)
    		{
    			array_push($betArrs, $tmp);
    			$onebets = array();
    			$counts = 0;
    		}
    	}
    	
    	return $betArrs;
    }
	/**
     * 拆倍数公共方法
     * @param unknown_type $multi
     * @param unknown_type $omulti
     */
    public function calBets($multi, $omulti)
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
     * 拆倍数
     * @param int $betnum	注数
     * @param int $multi	倍数
     */
    public function disMulti($betnum, $multi, $lid = 0, $nmulti = 0)
    {
    	$mymulti = 99;
    	if(in_array($lid, array(51, 52, 23528)))
    	{
    		$mymulti = 50;
    	}
    	$mymulti = $nmulti > 0 ? $nmulti : $mymulti;
    	$multis = array();
    	$tmpmultis = $this->calBets($multi, $mymulti);
    	foreach ($tmpmultis as $multi)
    	{
    		//拆总注数
    		$obetnum = $betnum * $multi;
    		if($obetnum > 10000)
    		{
    			$disnum = $obetnum / 10000;
    			$omulti = floor($multi / $disnum);
    			$multit = $this->calBets($multi, $omulti);
    			$multis = $this->marge_arr($multis, $multit);
    		}
    		else
    		{
    			array_push($multis, $multi);
    		}
    	}
    	return $multis;
    }
	/**
     * 用于生成新的订单编号
     * @param unknown_type $betcbts
     */
    public function createOrderId($lid)
    {
    	$this->CI->load->driver('cache', array('adapter' => 'redis'));
    	$REDIS = $this->CI->config->item('REDIS');
    	$datas = json_decode($this->CI->cache->get($REDIS['LOTTERY_CONFIG']), true);
    	$lidmap = array();
    	foreach ($datas as $mlid => $lottery)
    	{
    		$lidmap[$mlid] = $lottery['oid'];
    	}
   		if(!empty($lidmap[$lid]))
   		{
	   		$nlid = $this->CI->tools->getIncNum('UNIQUE_KEY') . $lidmap[$lid];
	   		return $nlid;
   		}
    }
    
    /**
     * 竞彩玩法组合
     * @param unknown_type $matches
     * @param unknown_type $ah
     * @param unknown_type $at
     * @return multitype:
     */
    public function jjcRecursive($matches, $ah, $at)
    {
    	$matchhs = array_keys($matches[$ah]);
    	$matchstr = array();
    	foreach ($matchhs as $matchh)
    	{
    		if(count($at) > 1)
    		{
    			$atn = $at;
    			$ahn = array_shift($atn);
    			$substrs = $this->jjcRecursive($matches, $ahn, $atn);
    			foreach ($substrs as $substr)
    			{
    				array_push($matchstr, $matches[$ah][$matchh].",$substr");
    			}
    		}
    		else
    		{
    			if(empty($at[0]))
    			{
    				array_push($matchstr, $matches[$ah][$matchh]);
    			}
    			else
    			{
    				$matchts = array_keys($matches[$at[0]]);
    				foreach ($matchts as $matcht)
    				{
    					array_push($matchstr, $matches[$ah][$matchh]."," . $matches[$at[0]][$matcht]);
    				}
    
    			}
    		}
    	}
    	
    	return $matchstr;
    }
    
	/**
     * 竞技彩计算彩注数逻辑
     * @param string $ggtype 过关方式
     * @param array $sortarr 选中的玩法数组
     */
    public function calBetNum($ggtype, $sortarr)
    {
    	switch ($ggtype)
    	{
    		case '1*1':
    		case '2*1':
    		case '3*1':
    		case '4*1':
    		case '5*1':
    		case '6*1':
    		case '7*1':
    		case '8*1':
    		case '9*1':
    		case '10*1':
    		case '11*1':
    		case '12*1':
    		case '13*1':
    		case '14*1':
    		case '15*1':
    			$betNum = 1;
    			foreach ($sortarr as $vaule)
    			{
    				$betNum *= $vaule;
    			}
    			break;
    		case '3*3':
    		case '4*6':
    		case '5*10':
    		case '6*15':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			break;
    		case '3*4':
    		case '5*20':
    		case '6*35':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			break;
    		case '4*4':
    		case '6*20':
    			$betNum = $this->getCalZhuShu($sortarr, 3);
    			break;
    		case '4*5':
    			$betNum = $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			break;
    		case '4*11':
    		case '6*50':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			break;
    		case '5*5':
    		case '7*35':
    		case '8*70':
    			$betNum = $this->getCalZhuShu($sortarr, 4);
    			break;
    		case '5*6':
    			$betNum = $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			break;
    		case '5*16':
    			$betNum = $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			break;
    		case '5*26':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			break;
    		case '6*6':
    		case '7*21':
    		case '8*56':
    			$betNum = $this->getCalZhuShu($sortarr, 5);
    			break;
    		case '6*7':
    			$betNum = $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			break;
    		case '7*7':
    		case '8*28':
    			$betNum = $this->getCalZhuShu($sortarr, 6);
    			break;
    		case '7*8':
    			$betNum = $this->getCalZhuShu($sortarr, 6);
    			$betNum += $this->getCalZhuShu($sortarr, 7);
    			break;
    		case '6*22':
    			$betNum = $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			break;
    		case '6*42':
    			$betNum = $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			break;
    		case '6*57':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			break;
    		case '7*120':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			$betNum += $this->getCalZhuShu($sortarr, 7);
    			break;
    		case '8*8':
    			$betNum = $this->getCalZhuShu($sortarr, 7);
    			break;
    		case '8*9':
    			$betNum = $this->getCalZhuShu($sortarr, 7);
    			$betNum += $this->getCalZhuShu($sortarr, 8);
    			break;
    		case '8*247':
    			$betNum = $this->getCalZhuShu($sortarr, 2);
    			$betNum += $this->getCalZhuShu($sortarr, 3);
    			$betNum += $this->getCalZhuShu($sortarr, 4);
    			$betNum += $this->getCalZhuShu($sortarr, 5);
    			$betNum += $this->getCalZhuShu($sortarr, 6);
    			$betNum += $this->getCalZhuShu($sortarr, 7);
    			$betNum += $this->getCalZhuShu($sortarr, 8);
    			break;
    		default:
    			$betNum = 0;
    	}
    	 
    	return $betNum;
    }
    
    /**
     * 返回不同组合下注数
     * @param unknown_type $arr
     * @param unknown_type $combine
     */
    private function getCalZhuShu($arr, $combine)
    {
    	$betNum = 0;
    	$combine = $this->combineList($arr, $combine);
    	foreach ($combine as $value)
    	{
    		$bet = 1;
    		foreach ($value as $val)
    		{
    			$bet *= $val;
    		}
    		$betNum += $bet;
    	}
    	 
	return $betNum;
    }

    public function dismantleSigleCodes($arrs, $_current_index = -1)
    {
        // 总数组
        static $_total_arr;
        // 总数组下标计数
        static $_total_arr_index;
        // 输入的数组长度
        static $_total_count;
        // 临时拼凑数组
        static $_temp_arr;
        
        // 进入输入数组的第一层，清空静态数组，并初始化输入数组长度
        if($_current_index < 0)
        {
            $_total_arr = array();
            $_total_arr_index = 0;
            $_temp_arr = array();
            $_total_count = count($arrs) - 1;
            $this->dismantleSigleCodes($arrs, 0);
        }
        else
        {
            // 循环第$_current_index层数组
            foreach($arrs[$_current_index] as $v)
            {
                // 如果当前的循环的数组少于输入数组长度
                if($_current_index < $_total_count)
                {
                    // 将当前数组循环出的值放入临时数组
                    $_temp_arr[$_current_index] = $v;
                    // 继续循环下一个数组
                    $this->dismantleSigleCodes($arrs,$_current_index + 1);
                    
                }
                // 如果当前的循环的数组等于输入数组长度(这个数组就是最后的数组)
                else if($_current_index == $_total_count)
                {
                    // 将当前数组循环出的值放入临时数组
                    $_temp_arr[$_current_index] = $v;
                    // 将临时数组加入总数组
                    $_total_arr[$_total_arr_index] = $_temp_arr;
                    // 总数组下标计数+1
                    $_total_arr_index ++;
                }

            }
        }
        
        return $_total_arr;
    }
    
    /**
     * 根据票商比例随机分配一个票商返回
     * @param unknown $rateArr
     * @return string|unknown
     */
    public function getTicketSeller($rateArr) 
    {
        $result = '';
        //概率数组的总概率精度
        $proSum = array_sum($rateArr);
        //概率数组循环
        foreach ($rateArr as $key => $proCur) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $proCur) {
                $result = $key;
                break;
            } else {
                $proSum -= $proCur;
            }
        }
        unset ($rateArr);
        
        return $result;
    }
}
