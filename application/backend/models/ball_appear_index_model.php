<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/8/5
 * 修改时间: 10:14
 */
class Ball_Appear_Index_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：指定彩种的排期表
     * 修改日期：2015-08-07
     */
    private function decideIssueTable($type)
    {
        $table = "cp_{$type}_paiqi";

        return $table;
    }

    public function cleanTable()
    {
        $this->cfgDB->query("DELETE FROM cp_missed_counter");
        $this->cfgDB->query("ALTER TABLE cp_missed_counter AUTO_INCREMENT = 1");
    }

    //性能考虑，初始化时只取1000条，耗时约5分钟
    private function fetchIssueRecords($type, $startIssue)
    {
        $table = $this->decideIssueTable($type);
        $sql = "SELECT issue, awardNum FROM $table
            WHERE issue > '$startIssue' AND awardNum IS NOT NULL AND status >= 50
            ORDER BY issue
            LIMIT 500";
        $records = $this->slaveCfg1->query($sql)->getAll();

        return $records;
    }

    private function decideLotteryId($type)
    {
        $lotteryIdMap = array(
            'ssq' => 51,
            'dlt' => 23529,
            'syxw' => 21406,
        	'jxsyxw' => 21407,
        	'hbsyxw' => 21408,
            'qlc' => 23528,
            'qxc' => 10022,
            'pl3' => 33,
            'pl5' => 35,
            'fc3d' => 52,
            'gdsyxw' => 21421,
        );

        return $lotteryIdMap[$type];
    }

    private function getBallAmount($type)
    {
        $ballAmountConfig = array(
            'ssq' => array(
                0 => 33, //33个红球
                1 => 16, //16个蓝球
            ),
            'dlt' => array(
                0 => 35, //35个红球
                1 => 12, //12个蓝球
            ),
            'syxw' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
        	'jxsyxw' => array(
        		0 => 11, //11个任选n
        		1 => 11, //11个前n直选第一位
        		2 => 11, //11个前n直选第二位
        		3 => 11, //11个前n直选第三位
        		4 => 11, //11个前n组选前二位
        		5 => 11, //11个前n组选前三位
        	),
        	'hbsyxw' => array(
        		0 => 11, //11个任选n
        		1 => 11, //11个前n直选第一位
        		2 => 11, //11个前n直选第二位
        		3 => 11, //11个前n直选第三位
        		4 => 11, //11个前n组选前二位
        		5 => 11, //11个前n组选前三位
        	),
            'gdsyxw' => array(
                0 => 11, //11个任选n
                1 => 11, //11个前n直选第一位
                2 => 11, //11个前n直选第二位
                3 => 11, //11个前n直选第三位
                4 => 11, //11个前n组选前二位
                5 => 11, //11个前n组选前三位
            ),
            'qlc' => array(
                0 => 30, //30个球
            ),
            'qxc' => array(
                0 => 10, //第一位遗漏
                1 => 10, //第二位遗漏
                2 => 10, //第三位遗漏
                3 => 10, //第四位遗漏
                4 => 10, //第五位遗漏
                5 => 10, //第六位遗漏
                6 => 10, //第七位遗漏
            ),
            'pl3' => array(
                0 => 10, //直选百位遗漏
                1 => 10, //直选十位遗漏
                2 => 10, //直选个位遗漏
                3 => 10, //组选遗漏
            ),
            'pl5' => array(
                0 => 10, //万位遗漏
                1 => 10, //千位遗漏
                2 => 10, //百位遗漏
                3 => 10, //十位遗漏
                4 => 10, //个位遗漏
            ),
            'fc3d' => array(
                0 => 10, //直选百位遗漏
                1 => 10, //直选十位遗漏
                2 => 10, //直选个位遗漏
                3 => 10, //组选遗漏
            ),
        );

        return $ballAmountConfig[$type];
    }

    private function insertStatistics($type, $issueToCounters)
    {
        //双色球大乐透拼接字符串，一期一条
        //十一选五拼接字符串，一期六条
        $lotteryId = $this->decideLotteryId($type);
        $values = array();
        foreach($issueToCounters as $issue => $counters)
        {
            if (in_array($type, array('syxw', 'qlc', 'qxc', 'pl3', 'pl5', 'fc3d', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
            {
                foreach ($counters as $key => $counter)
                {
                    $detail = implode(',', $counter);
                    array_push($values, "($lotteryId, '$issue', $key, '$detail', NOW())");
                }
            }
            elseif (in_array($type, array('ssq', 'dlt')))
            {
                $detailParts = array();
                foreach ($counters as $counter)
                {
                    array_push($detailParts, implode(',', $counter));
                }
                $detail = implode('|', $detailParts);
                array_push($values, "($lotteryId, '$issue', 0, '$detail', NOW())");
            }
        }
        $valueStr = implode(',', $values);
        $sql = "INSERT cp_missed_counter (lid, issue, play_type, detail, created) VALUES $valueStr";
        $this->cfgDB->query($sql);
    }

    public function initialLottery($type)
    {
        $startIssue = 0;
        $records = $this->fetchIssueRecords($type, $startIssue);
        list($dealIssues, $issueToAward) = $this->formatIssues($records);

        $ballAmount = $this->getBallAmount($type);
        $count = count($ballAmount);
        $issueToCounters = array();
        foreach ($dealIssues as $dealIssue)
        {
            $missedCounterAry = $this->createEmptyCounterAry($type, $ballAmount);
            $appearedBalls = array();
            for ($i = 0; $i < $count; $i ++)
            {
                $appearedBalls[$i] = array();
            }

            //对于每一期，都要从该期往前推，挨个检查
            //TODO 如果appearedBalls中满了，就不必再循环了
            foreach ($dealIssues as $checkIssue)
            {
                if ($checkIssue > $dealIssue)
                {
                    continue;
                }

                if (in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
                {
                    $matches[1] = $issueToAward[$checkIssue];
                    $numberAry = explode(',', $issueToAward[$checkIssue]);
                    $matches[2] = $numberAry[0];
                    $matches[3] = $numberAry[1];
                    $matches[4] = $numberAry[2];
                    $matches[5] = implode(',', array($numberAry[0], $numberAry[1]));
                    $matches[6] = implode(',', array($numberAry[0], $numberAry[1], $numberAry[2]));
                }
                elseif(in_array($type, array('ssq', 'dlt')))
                {
                    preg_match('/([\d,]+?)\|([\d,]*)/', $issueToAward[$checkIssue], $matches);
                }
                elseif(in_array($type, array('qlc')))
                {
                    // 04,08,10,15,20,22,27(28) 幸运号
                    $matches[1] = str_replace(array('(', ')'), array(',', ''), $issueToAward[$checkIssue]);
                }
                elseif ( in_array($type, array('qxc', 'pl5')) ) 
                {
                    $numberAry = explode(',', $issueToAward[$checkIssue]);
                    $matches = array_chunk($numberAry, 1);
                }
                elseif ( in_array($type, array('pl3', 'fc3d')) ) 
                {
                    $numberAry = explode(',', $issueToAward[$checkIssue]);
                    $matches = array_chunk($numberAry, 1);
                    $matches[3] = $numberAry;
                }
                elseif($type == 'pl5')
                {
                    $numberAry = explode(',', $issueToAward[$checkIssue]);
                    $matches = array_chunk($numberAry, 1);
                }

                if( in_array($type, array('qxc', 'pl5')) )
                {
                    // 针对球号从 0 开始的彩种
                    for ($i = 0; $i < $count; $i ++)    // 玩法或红篮球
                    {
                        for ($j = 0; $j < $ballAmount[$i]; $j ++)  // 球号
                        {
                            $needle = $j;
                            if ( ! in_array($j, $appearedBalls[$i]))
                            {
                                if (in_array($needle, $matches[$i]))
                                {
                                    array_push($appearedBalls[$i], $j);
                                }
                                else
                                {                                    
                                    $missedCounterAry[$i][$j] += 1;
                                }
                            }
                        }
                    }
                }
                elseif( in_array($type, array('pl3', 'fc3d')) )
                {
                    // 针对球号从 0 开始的彩种
                    for ($i = 0; $i < $count; $i ++)    // 玩法或红篮球
                    {
                        for ($j = 0; $j < $ballAmount[$i]; $j ++)  // 球号
                        {
                            $needle = $j;
                            if ( ! in_array($j, $appearedBalls[$i]))
                            {
                                if (in_array($needle, $matches[$i]))
                                {
                                    // 排列三、福彩3D 组选遗漏统计 顺子豹子 0 -2 -3
                                    $countArray = array(
                                        1 => 0,
                                        2 => -2,
                                        3 => -3
                                    ); 
                                    $countIndex = array_count_values($matches[$i]);
                                    $missedCounterAry[$i][$j] = $countArray[$countIndex[$needle]];
                                    array_push($appearedBalls[$i], $j);
                                    
                                }
                                else
                                {   
                                    if($missedCounterAry[$i][$j] >= 0)
                                    {
                                        $missedCounterAry[$i][$j] += 1;
                                    }
                                    else
                                    {
                                        $missedCounterAry[$i][$j] = 1;
                                    }                             
                                }
                            }
                        }
                    }
                }
                else
                {
                    // 针对球号从 01 开始的彩种
                    for ($i = 0; $i < $count; $i ++)    // 玩法或红篮球
                    {
                        for ($j = 1; $j <= $ballAmount[$i]; $j ++)  // 球号
                        {
                            if ($j < 10)
                            {
                                $needle = '0' . $j;
                            }
                            else
                            {
                                $needle = '' . $j;
                            }
                            if ( ! in_array($j, $appearedBalls[$i]))
                            {
                                if (strstr($matches[$i + 1], $needle))
                                {
                                    array_push($appearedBalls[$i], $j);
                                }
                                else
                                {
                                    $missedCounterAry[$i][$j] += 1;
                                }
                            }
                        }
                    }
                }
                
            }
            $issueToCounters[$dealIssue] = $missedCounterAry;
        }

        $this->insertStatistics($type, $issueToCounters);
        $this->writeRedis($type);
    }

    private function createEmptyCounterAry($type, $ballAmount)
    {
        $missedCounterAry = array();
        $count = count($ballAmount);
        if(in_array($type, array('qxc', 'pl3', 'pl5', 'fc3d')))
        {
            for ($i = 0; $i < $count; $i ++)
            {
                for ($j = 0; $j < $ballAmount[$i]; $j ++)
                {
                    $missedCounterAry[$i][$j] = 0;
                }
            }
        }
        else
        {
            for ($i = 0; $i < $count; $i ++)
            {
                for ($j = 1; $j <= $ballAmount[$i]; $j ++)
                {
                    $missedCounterAry[$i][$j] = 0;
                }
            }
        }

        return $missedCounterAry;
    }

    private function fetchLastIssue($type)
    {
        $lotteryId = $this->decideLotteryId($type);
        $sql = "SELECT MAX(issue) FROM cp_missed_counter WHERE lid = $lotteryId";
        $lastIssue = $this->slaveCfg1->query($sql)->getOne();

        return $lastIssue;
    }

    private function fetchCurrentCounter($type, $lastIssue)
    {
        $lotteryId = $this->decideLotteryId($type);
        $sql = "SELECT detail FROM cp_missed_counter WHERE lid = $lotteryId AND issue = $lastIssue";
        $details = $this->slaveCfg1->query($sql)->getCol();
        if (in_array($type, array('syxw', 'qlc', 'qxc', 'pl3', 'pl5', 'fc3d', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
        {
            $counter = $details;
        }
        elseif (in_array($type, array('ssq', 'dlt')))
        {
            $counter = explode('|', $details[0]);
        }

        return $counter;
    }

    private function formatIssues($records)
    {
        //要处理的期次，后面要从后往前统计
        $dealIssues = array();
        //期号到开奖号码的哈希
        $issueToAward = array();
        foreach ($records as $record)
        {
            array_unshift($dealIssues, $record['issue']);
            $issueToAward[$record['issue']] = $record['awardNum'];
        }

        return array($dealIssues, $issueToAward);
    }

    //注意，initialLottery是从后往前，updateLottery是从前往后
    public function updateLottery($type)
    {
        $lastIssue = $this->fetchLastIssue($type);
        $records = $this->fetchIssueRecords($type, $lastIssue);
        if (empty($records))
        {
            return;
        }

        $ballAmount = $this->getBallAmount($type);
        $missedCounterAry = $this->createEmptyCounterAry($type, $ballAmount);
        $currentCounter = $this->fetchCurrentCounter($type, $lastIssue);

        if(in_array($type, array('qxc', 'pl3', 'pl5', 'fc3d')))
        {
            // 针对球号从 0 开始的彩种
            foreach ($currentCounter as $playType => $countStr)
            {
                $tmpAry = explode(',', $countStr);
                $c = count($tmpAry);
                for ($i = 0; $i < $c; $i ++)
                {
                    $missedCounterAry[$playType][$i] = intval($tmpAry[$i]);
                }
            }
        }
        else
        {
            // 针对球号从 01 开始的彩种
            foreach ($currentCounter as $playType => $countStr)
            {
                $tmpAry = explode(',', $countStr);
                $c = count($tmpAry);
                for ($i = 0; $i < $c; $i ++)
                {
                    $missedCounterAry[$playType][$i + 1] = intval($tmpAry[$i]);
                }
            }
        }
        
        $count = count($ballAmount);
        $issueToCounters = array();

        list(, $issueToAward) = $this->formatIssues($records);
        $dealIssues = array_keys($issueToAward);
        foreach ($dealIssues as $dealIssue)
        {
            if (in_array($type, array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw')))
            {
                $matches[1] = $issueToAward[$dealIssue];
                $numberAry = explode(',', $issueToAward[$dealIssue]);
                $matches[2] = $numberAry[0];
                $matches[3] = $numberAry[1];
                $matches[4] = $numberAry[2];
                $matches[5] = implode(',', array($numberAry[0], $numberAry[1]));
                $matches[6] = implode(',', array($numberAry[0], $numberAry[1], $numberAry[2]));
            }
            elseif(in_array($type, array('ssq', 'dlt')))
            {
                preg_match('/([\d,]+?)\|([\d,]*)/', $issueToAward[$dealIssue], $matches);
            }
            elseif(in_array($type, array('qlc')))
            {
                // 04,08,10,15,20,22,27(28) 幸运号
                $matches[1] = str_replace(array('(', ')'), array(',', ''), $issueToAward[$dealIssue]);
            }
            elseif ( in_array($type, array('qxc', 'pl5')) ) 
            {
                $numberAry = explode(',', $issueToAward[$dealIssue]);
                $matches = array_chunk($numberAry, 1);
            }
            elseif (in_array($type, array('pl3', 'fc3d')))
            {
                $numberAry = explode(',', $issueToAward[$dealIssue]);
                $matches = array_chunk($numberAry, 1);
                $matches[3] = $numberAry;
            }

            if( in_array($type, array('qxc', 'pl5')) )
            {
                for ($i = 0; $i < $count; $i ++)
                {
                    for ($j = 0; $j < $ballAmount[$i]; $j ++)
                    {
                        $needle = $j;
                        if (in_array($needle, $matches[$i]))
                        {
                            $missedCounterAry[$i][$j] = 0;
                        }
                        else
                        {
                            $missedCounterAry[$i][$j] += 1;
                        }
                    }
                }
            }
            elseif( in_array($type, array('pl3', 'fc3d')) ) 
            {
                for ($i = 0; $i < $count; $i ++)
                {
                    for ($j = 0; $j < $ballAmount[$i]; $j ++)
                    {
                        $needle = $j;
                        if (in_array($needle, $matches[$i]))
                        {
                            // 排列三、福彩3D 组选遗漏统计 顺子豹子 0 -2 -3
                            $countArray = array(
                                1 => 0,
                                2 => -2,
                                3 => -3
                            ); 
                            $countIndex = array_count_values($matches[$i]);
                            $missedCounterAry[$i][$j] = $countArray[$countIndex[$needle]];
                        }
                        else
                        {
                            if($missedCounterAry[$i][$j] >= 0)
                            {
                                $missedCounterAry[$i][$j] += 1;
                            }
                            else
                            {
                                $missedCounterAry[$i][$j] = 1;
                            }
                            
                        }
                    }
                }
            }
            else
            {
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
            }
            $issueToCounters[$dealIssue] = $missedCounterAry;
        }

        $this->insertStatistics($type, $issueToCounters);
        $this->writeRedis($type);
        $this->writeRedisMore($type);
    }

    private function writeRedis($type)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $lotteryId = $this->decideLotteryId($type);
        $limit = $this->getLotteryLimit($type);
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = $lotteryId ORDER BY issue DESC LIMIT $limit";
        $records = $this->slaveCfg1->query($sql)->getAll();
        $missCounter = array();
        if ( in_array($type, array('ssq', 'dlt', 'qlc')) )
        {
            foreach ($records as $rc)
            {
                $missCounter[$rc['issue']] = $rc['detail'];
            }
        }
        else
        {
            foreach ($records as $rc)
            {
                if (empty($missCounter[$rc['issue']]))
                {
                    $missCounter[$rc['issue']] = array();
                }
                $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
            }
        }
        $this->cache->save($redis[strtoupper($type) . '_MISS'], serialize($missCounter), 0);
    }

    private function writeRedisMore($type)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $redis = $this->config->item('REDIS');
        $lotteryId = $this->decideLotteryId($type);
        $limit = $this->getLotteryLimit200($type);
        $sql = "SELECT * FROM cp_missed_counter WHERE lid = $lotteryId ORDER BY issue DESC LIMIT $limit";
        $records = $this->slaveCfg1->query($sql)->getAll();
        $missCounter = array();
        if ( in_array($type, array('ssq', 'dlt', 'qlc')) )
        {
            foreach ($records as $rc)
            {
                $missCounter[$rc['issue']] = $rc['detail'];
            }
        }
        else
        {
            foreach ($records as $rc)
            {
                if (empty($missCounter[$rc['issue']]))
                {
                    $missCounter[$rc['issue']] = array();
                }
                $missCounter[$rc['issue']][$rc['play_type']] = $rc['detail'];
            }
        }
        $this->cache->save($redis[strtoupper($type) . '_MISS_MORE'], serialize($missCounter), 0);
    }

    private function getLotteryLimit($type)
    {
        $limits = array(
            'ssq' => 10,
            'dlt' => 10,
            'syxw' => 60,
        	'jxsyxw' => 60,
        	'hbsyxw' => 60,
            'qlc' => 10,
            'qxc' => 70,
            'pl3' => 40,
            'pl5' => 50,
            'fc3d' => 40,
            'gdsyxw' => 60,
        );

        return $limits[$type];
    }

    private function getLotteryLimit200($type)
    {
        $limits = array(
            'ssq' => 200,
            'dlt' => 200,
            'syxw' => 1200,
        	'jxsyxw' => 1200,
        	'hbsyxw' => 1200,
            'qlc' => 200,
            'qxc' => 1400,
            'pl3' => 800,
            'pl5' => 1000,
            'fc3d' => 800,
            'gdsyxw' => 1200,
        );

        return $limits[$type];
    }

}