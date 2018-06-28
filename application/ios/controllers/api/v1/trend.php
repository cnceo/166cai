<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * 数字彩走势图
 * @date:2016-01-18
 */

class Trend extends MY_Controller 
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model('trend_model','Trend');
	}

	private function getBallAmount($lid)
    {
        $ballAmountConfig = array(
            51 => array(
                'playType' => array(
                    0 => 33, //33个红球
                    1 => 16, //16个蓝球
                ),
                'limit' => 1
            ),
            23529 => array(
                'playType' => array(
                    0 => 35, //35个红球
                    1 => 12, //12个蓝球
                ),
                'limit' => 1
            ),
            21406 => array(
                'playType' => array(
                    0 => 11, //11个任选n
                    1 => 11, //11个前n直选第一位
                    2 => 11, //11个前n直选第二位
                    3 => 11, //11个前n直选第三位
                    4 => 11, //11个前n组选前二位
                    5 => 11, //11个前n组选前三位
                ),
                'limit' => 6
            ),
            21407 => array(
                'playType' => array(
                    0 => 11, //11个任选n
                    1 => 11, //11个前n直选第一位
                    2 => 11, //11个前n直选第二位
                    3 => 11, //11个前n直选第三位
                    4 => 11, //11个前n组选前二位
                    5 => 11, //11个前n组选前三位
                ),
                'limit' => 6
            ),
            21408 => array(
                'playType' => array(
                    0 => 11, //11个任选n
                    1 => 11, //11个前n直选第一位
                    2 => 11, //11个前n直选第二位
                    3 => 11, //11个前n直选第三位
                    4 => 11, //11个前n组选前二位
                    5 => 11, //11个前n组选前三位
                ),
                'limit' => 6
            ),
            21421 => array(
                'playType' => array(
                    0 => 11, //11个任选n
                    1 => 11, //11个前n直选第一位
                    2 => 11, //11个前n直选第二位
                    3 => 11, //11个前n直选第三位
                    4 => 11, //11个前n组选前二位
                    5 => 11, //11个前n组选前三位
                ),
                'limit' => 6
            ),
            23528 => array(
                'playType' => array(
                    0 => 30, //30个球
                ),
                'limit' => 1
            ),
            10022 => array(
                'playType' => array(
                    0 => 10, //第一位遗漏
                    1 => 10, //第二位遗漏
                    2 => 10, //第三位遗漏
                    3 => 10, //第四位遗漏
                    4 => 10, //第五位遗漏
                    5 => 10, //第六位遗漏
                    6 => 10, //第七位遗漏
                ),
                'limit' => 7
            ),
            33 => array(
                'playType' => array(
                    0 => 10, //直选百位遗漏
                    1 => 10, //直选十位遗漏
                    2 => 10, //直选个位遗漏
                    3 => 10, //组选遗漏
                ),
                'limit' => 4
            ),
            35 => array(
                'playType' => array(
                    0 => 10, //万位遗漏
                    1 => 10, //千位遗漏
                    2 => 10, //百位遗漏
                    3 => 10, //十位遗漏
                    4 => 10, //个位遗漏
                ),
                'limit' => 5
            ),
            52 => array(
                'playType' => array(
                    0 => 10, //直选百位遗漏
                    1 => 10, //直选十位遗漏
                    2 => 10, //直选个位遗漏
                    3 => 10, //组选遗漏
                ),
                'limit' => 4
            ),
        	53 => array(
        		'playType' => array(
        			0 => 6,
        			1 => 16,
        			2 => 1,
        			3 => 6,
        			4 => 20,
        			5 => 1,
        			6 => 6,
        			7 => 30,
        			8 => 15,
        			9 => 4,
        			10 => 6,
        			11 => 6
        		),
        		'limit' => 12
        	),
        	54 => array(
        		'playType' => array(
        			0 => 13,
        			1 => 6,
        		),
        		'limit' => 2
        	),
            57 => array(
                'playType' => array(
                    0 => 6,
                    1 => 16,
                    2 => 1,
                    3 => 6,
                    4 => 20,
                    5 => 1,
                    6 => 6,
                    7 => 30,
                    8 => 15,
                    9 => 4,
                    10 => 6,
                    11 => 6
                ),
                'limit' => 12
            ),
        );

        return $ballAmountConfig[$lid];
    }

    public function index()
    {
        var_dump($this->config->item('base_url'));die;
        $result = array(
            'status' => '1',
            'msg' => '通讯成功',
            'data' => $this->getRequestHeaders()
        );
        echo json_encode($result);  
    }

	public function getData()
	{
		$data = $this->input->get(NULL, TRUE);

		// $data = array(
		// 	'lid' => '23528',	//彩种ID
		// 	'rows' => '30',	//显示期数
		// );

        $ballInfo = $this->getBallAmount($data['lid']);

        if(empty($ballInfo['limit']) || empty($data['rows']))
        {
            $result = array(
                'status' => '0',
                'msg' => 'parmas error',
                'data' => array()
            );
            echo json_encode($result);die;
        }

        $showLimit = array('30', '50', '100', '200');
        if(!in_array($data['rows'], $showLimit))
        {
            $data['rows'] = 200;
        }
        
		$limits = $ballInfo['limit'] * $data['rows'];

		// 200期遗漏数据
        $missData = $this->Trend->getTrendCache($data['lid'], $limits);
        // 获取最新期次开奖信息
        $awardInfo = $this->Trend->getLastByLid($data['lid']);
		// 处理遗漏格式及计算数据
		$trendData = array();

		if(!empty($missData))
		{
			$trendData = $this->dealTrendData($data['lid'], $missData, $data['rows'], $awardInfo);
		}

        $result = array(
            'status' => '1',
            'msg' => 'success',
            'data' => $trendData
        );

		echo json_encode($result);die;
	}

	/*
	 * 处理遗漏格式及计算数据
	 * @date:2016-01-18
	 */
	public function dealTrendData($lid, $missData, $rows, $awardInfo)
	{
        // 截取需要的期次
        $missData = $this->getRequiredRowsData($missData, $rows);
        if($lid == '54')
        {
        	foreach ($missData as $issue => $items)
        	{
        		$missData[$issue] = array($items[0], $items[6]);
        	}
        }
        $missNum = array();
        $missBall = array();
		$issues = array();
        foreach ($missData as $issue => $items) 
        {
            $missNum[$issue] = $this->ballFormat($items, $lid, 1);
            $issues[] = $issue;
        }

		ksort($missNum);
		$countRows = count($missNum);

		// 统计走势数据
		$missType = $this->calTrendInfo($lid, $missNum, $countRows);
		$awardNums = array();
		if(in_array($lid, array('53', '57')))
		{
			$tmpData = array();
			foreach ($missType['missInfo'] as $key => $val)
			{
				foreach ($val as $kk => $vv)
				{
					if(in_array($kk, array(2, 3, 4, 5, 6, 7, 8, 9, 11)))
					{
						continue;
					}
					$tmpData[$key][] = $vv;
				}
			}
			$missType['missInfo'] = $tmpData;
			
		}
		if(in_array($lid, array('53', '54', '57')))
		{
			$this->load->model('award_model', 'Award');
			$awardNums = $this->Award->getAwardsByIssue($lid, $issues);
		}
        foreach ($missData as $issue => $items) 
        {
            $data['issue'] = $issue;
            $data['detail'] = $this->ballFormat($items, $lid, 0);
            if(in_array($lid, array('33', '52')))
            {
                $data['plusCount'] = $missType['plusCount'][$issue];
            }
            if(in_array($lid, array('53', '57')))
            {
            	$data['award'] = $awardNums[$issue];
            	$data['plusCount'] = isset($awardNums[$issue]) ? array_sum(explode(',', $awardNums[$issue])) : 0;
            	$data['detail']['jiben'] = $this->ksJbFormat($data['detail']['jiben'], $data['award']);
            }
            if(in_array($lid, array('54')))
            {
            	$data['award'] = $awardNums[$issue];
            }
            array_push($missBall, $data);
            // $missBall[$issue] = $this->ballFormat($items, $lid, 0);
        }

        sort($missBall);
        // 检查等待开奖
        $awardStatus = $this->checkAwards($lid, $missBall);
		$trendData = array(
			'missNum' => $missBall,
			'trendInfo' => $missType['missInfo'],
            'awardInfo' => $awardStatus
		);

		return $trendData;
	}
	
	private function ksJbFormat($jibenValue, $award)
	{
		$award = explode(',', $award);
		if(in_array(count(array_unique($award)), array(1, 2)))
		{
			$miss = explode(',', $jibenValue);
			$countArr = array_count_values($award);
			foreach ($countArr as $number => $val)
			{
				if($val > 1)
				{
					$miss[$number - 1] = -$val;
				}
			}
			
			$jibenValue = implode(',', $miss);
		}
		
		return $jibenValue;
	}

    /*
     * 截取指定期数的遗漏信息
     * @date:2016-01-18
     */
    private function getRequiredRowsData($missData, $rows)
    {
        $count = 0;
        $data = array();
        foreach ($missData as $issue => $items) 
        {
            $data[$issue] = $items;
            $count ++;
            if($count == $rows)
            {
                break;
            }
        }
        return $data;
    }

	/*
	 * 双色球大乐透 红篮球处理 |
	 * @date:2016-01-18
	 */
	public function ballFormat($detail, $lid, $type = 1)
	{
        $ballInfo = array();

        if(in_array($lid, array('51', '23529')))
        {
            $ballInfo = explode('|', $detail);
        }
        elseif (in_array($lid, array('53')))
        {
        	$ballInfo = explode('|', $detail[0]);
        }
        else
        {
            $ballInfo = $detail;
        }

		if($type)
        {
            if($lid == '23528')
            {
                $missData[] = explode(',', $ballInfo);
            }
            elseif (in_array($lid, array('53')))
            {
            	foreach ($ballInfo as $playType => $items)
            	{
            		if(!in_array($playType, array('0', '1', '10', '11')))
            		{
            			continue;
            		}
            		$missData[$playType] = explode(',', $items);
            	}
            }
            else
            {
                foreach ($ballInfo as $playType => $items) 
                {
                    $missData[$playType] = explode(',', $items);
                }
            }
        }
        else
        {
            foreach ($ballInfo as $playType => $items) 
            {
                $typeName = $this->getTypeName($lid, $playType);
                if(in_array($lid, array('53', '57')) && (!in_array($typeName, array('jiben', 'hz', 'hm_xingtai', 'kuadu'))))
                {
                	continue;
                }
                $missData[$typeName] = $items;
            }
            // $missData = $ballInfo;
        }
			
		return $missData;
	}

	/*
	 * 统计走势数据
	 * @date:2016-01-18
	 */
	public function calTrendInfo($lid, $missNum, $rows)
	{
		// 出现次数：此号码在当前统计期数中累计出现的次数
		// 平均遗漏：统计期数内遗漏期数总和除以出现次数（四舍五入）
		// 最大遗漏：统计期数内所有遗漏值的最大值
		// 最大连出：统计期数内最多连续出现的期数
        // 和值：统计期数内中奖号码总和

		// $data['issue']['playType']['ballIndex'] 比较

        $ballInfo = $this->getBallAmount($lid);
		$ballAmount = $ballInfo['playType'];
        $count = count($ballAmount);	// 统计玩法、红篮球

		// 出现次数
		$appearCountAry = $this->createEmptyCounterAry($ballAmount);
		// 最大遗漏
		$maxMissCountAry = $this->createEmptyCounterAry($ballAmount);
		// 最大连出
		$maxKeepCountAry = $this->createEmptyCounterAry($ballAmount);
		$tempAry = $this->createEmptyCounterAry($ballAmount, 0);
		$issueAry = array_keys($missNum);

		foreach ($missNum as $issue => $missDetail) 
		{
            // 和值统计
            $plusCount[$issue] = 0;
			for($i = 0; $i < $count; $i ++) 
			{ 
				for ($j = 0; $j < $ballAmount[$i]; $j ++)
                {
                	// 出现次数 该玩法该球位遗漏出现0的统计
                	if( $missDetail[$i][$j] <= 0 )
                	{
                		$appearCountAry[$i][$j] += 1;
                		// 最大连出统计
                		array_push($tempAry[$i][$j], $issue);
                	}

                	// 最大遗漏
                	if( $maxMissCountAry[$i][$j] < $missDetail[$i][$j] )
                	{
                		$maxMissCountAry[$i][$j] = $missDetail[$i][$j];
                	}

                    // 和值统计
                    if( $i != 3 && in_array($lid, array('33', '52')) )
                    {
                        if($missDetail[$i][$j] <= 0)
                        {
                            $plusCount[$issue] += $j;
                        }
                    }
                }

			}
		}
        
		// 平均遗漏 平均遗漏＝统计期内的总遗漏数/(出现次数+1)
		foreach($appearCountAry as $playType => $missItem) 
		{
			foreach ($missItem as $ballNum => $deatils) 
			{
				$avgMissCountAry[$playType][$ballNum] = intval(($rows - $deatils)/($deatils + 1));
			}
		}

		// 最大连出
		$compareAry = array_flip($issueAry);

		for($i = 0; $i < $count; $i ++) 
		{ 
			for ($j = 0; $j < $ballAmount[$i]; $j ++)
            {
            	if(!empty($tempAry[$i][$j]))
            	{
            		$maxCount = 1;
            		$tempMax = 1;
            		foreach ($tempAry[$i][$j] as $in => $issue) 
            		{
            			$index = $compareAry[$issue];
            			$nextIn = $in + 1;
            			$nextIndex = $compareAry[$tempAry[$i][$j][$nextIn]];
            			if( isset($nextIndex) && ($index + 1) == $nextIndex )
            			{
            				$tempMax ++;
            				if($tempMax > $maxCount)
            				{
            					$maxCount = $tempMax;    
            				}       				  				
            			}
            			else
            			{
            				// 初始化当前最大连续
            				$tempMax = 1;
            			}
            		}
            		$maxKeepCountAry[$i][$j] = $maxCount;
            	}
            	else
            	{
            		$maxKeepCountAry[$i][$j] = 0;
            	}
            }
        }

        $missType = array(
        	0 => $this->countFormat($appearCountAry),
        	1 => $this->countFormat($avgMissCountAry),
        	2 => $this->countFormat($maxMissCountAry),
        	3 => $this->countFormat($maxKeepCountAry),
        );

        return array('missInfo' => $missType, 'plusCount' => $plusCount);
	}

	/*
	 * 初始化统计空数组
	 * @date:2016-01-18
	 */
	private function createEmptyCounterAry($ballAmount, $type = 1)
    {
        $missedCounterAry = array();
        $count = count($ballAmount);

        $input =  $type?0:array();

        for($i = 0; $i < $count; $i ++)
        {
            for ($j = 0; $j < $ballAmount[$i]; $j ++)
            {
                $missedCounterAry[$i][$j] = $input;
            }
        }
        return $missedCounterAry;
    }

    /*
	 * 初始化统计空数组
	 * @date:2016-01-18
	 */
    private function countFormat($countData)
    {
    	$data = array();
    	foreach ($countData as $playType => $items)
    	{
    		$data[$playType] = implode(',', $items);
    	}
    	return $data;
    }

    /*
     * 初始化统计空数组
     * @version:V1.3
     * @date:2015-09-28
     */
    private function getTypeName($lid, $playType)
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
        	'54' => array(
        		0 => 'jiben',
        		1 => 'xingtai',
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

    // 检查等待开奖信息
    public function checkAwards($lotteryId, $missBall)
    {
        $awardStatus = array(
            'issue' => '',
            'status' => 0
        );

        // 获取开奖中信息缓存
        $this->load->model('cache_model','Cache');
        $issueInfo = $this->Cache->getIssueInfo($lotteryId);

        if(!empty($missBall) && !empty($issueInfo['aIssue']['seExpect']))
        {
            $missInfo = $missBall;
            $lastMissInfo = end($missInfo);

            $this->load->library('libcomm');
            $cIssue = $this->libcomm->getIssueFormat($lotteryId, $issueInfo['aIssue']['seExpect']);

            if($lastMissInfo['issue'] < $cIssue)
            {
                $awardStatus = array(
                    'issue' => $cIssue,
                    'status' => 1
                );
            }       
        }
        return $awardStatus;
    }

}