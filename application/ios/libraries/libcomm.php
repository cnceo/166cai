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

    /**
     * 期次规则格式化处理
     * @param int $issue    期号
     */
    public function getIssueFormat($lotteryId, $issue = null)
    {
        if(!empty($issue))
        {
            switch ($lotteryId) 
            {
                case '23529':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                case '35':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                case '11':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                case '19':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                case '33':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                case '10022':
                    $issue = strlen($issue) >= 7 ? substr($issue, -5) : $issue;
                    break;
                default:
                    $issue = $issue;
                    break;
            }
        }
        return $issue;
    } 

    public function getTypeName($lid, $playType)
    {
        $ballAmountConfig = array(
            '51' => array(
                0 => 'hongqiu', //33个红球
                1 => 'lanqiu', //16个蓝球
            ),
            '23529' => array(
                0 => 'hongqiu', //35个红球
                1 => 'lanqiu', //12个蓝球
            ),
            '21406' => array(
                0 => 'renxuan', //11个任选n
                1 => 'zhixuan1', //11个前n直选第一位
                2 => 'zhixuan2', //11个前n直选第二位
                3 => 'zhixuan3', //11个前n直选第三位
                4 => 'zuxuan2', //11个前n组选前二位
                5 => 'zuxuan3', //11个前n组选前三位
            ),
            '21407' => array(
                0 => 'renxuan', //11个任选n
                1 => 'zhixuan1', //11个前n直选第一位
                2 => 'zhixuan2', //11个前n直选第二位
                3 => 'zhixuan3', //11个前n直选第三位
                4 => 'zuxuan2', //11个前n组选前二位
                5 => 'zuxuan3', //11个前n组选前三位
            ),
            '21408' => array(
                0 => 'renxuan', //11个任选n
                1 => 'zhixuan1', //11个前n直选第一位
                2 => 'zhixuan2', //11个前n直选第二位
                3 => 'zhixuan3', //11个前n直选第三位
                4 => 'zuxuan2', //11个前n组选前二位
                5 => 'zuxuan3', //11个前n组选前三位
            ),
            '21421' => array(
                0 => 'renxuan', //11个任选n
                1 => 'zhixuan1', //11个前n直选第一位
                2 => 'zhixuan2', //11个前n直选第二位
                3 => 'zhixuan3', //11个前n直选第三位
                4 => 'zuxuan2', //11个前n组选前二位
                5 => 'zuxuan3', //11个前n组选前三位
            ),
            '23528' => array(
                0 => 'putong', //30个球
            ),
            '10022' => array(
                0 => '1', //第一位遗漏
                1 => '2', //第二位遗漏
                2 => '3', //第三位遗漏
                3 => '4', //第四位遗漏
                4 => '5', //第五位遗漏
                5 => '6', //第六位遗漏
                6 => '7', //第七位遗漏
            ),
            '33' => array(
                0 => 'zhixuanbaiwei', //直选百位遗漏
                1 => 'zhixuanshiwei', //直选十位遗漏
                2 => 'zhixuangewei', //直选个位遗漏
                3 => 'zuxuan', //组选遗漏
            ),
            '35' => array(
                0 => 'wanwei', //万位遗漏
                1 => 'qianwei', //千位遗漏
                2 => 'baiwei', //百位遗漏
                3 => 'shiwei', //十位遗漏
                4 => 'gewei', //个位遗漏
            ),
            '52' => array(
                0 => 'zhixuanbaiwei', //直选百位遗漏
                1 => 'zhixuanshiwei', //直选十位遗漏
                2 => 'zhixuangewei', //直选个位遗漏
                3 => 'zuxuan', //组选遗漏
            ),
        	'53' => array(
        		0 => 'jiben', 
        		1 => 'hz', 
        		2 => 'sthtx', 
        		3 => 'sthdx', 
        		4 => 'sbth', 
        		5 => 'slhtx', 
        		6 => 'ethfx', 
        		7 => 'ethdx', 
        		8 => 'ebth', 
        		9 => 'hz_xingtai', 
        		10 => 'hm_xingtai', 
        		11 => 'kuadu',
        	),
            '57' => array(
                0 => 'jiben',
                1 => 'hz',
                2 => 'sthtx',
                3 => 'sthdx',
                4 => 'sbth',
                5 => 'slhtx',
                6 => 'ethfx',
                7 => 'ethdx',
                8 => 'ebth',
                9 => 'hz_xingtai',
                10 => 'hm_xingtai',
                11 => 'kuadu',
            ),
        );

        return $ballAmountConfig[$lid][$playType];
    }


}