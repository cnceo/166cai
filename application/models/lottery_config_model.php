<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/8
 * 修改时间: 17:44
 */
class Lottery_Config_model extends MY_Model
{
    private $soccerCTypeAry = array(
        'sfc' => 1,
        'rj'  => 1,
        'bqc' => 2,
        'jqc' => 3,
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function fetchConfigItems($lid = 0)
    {
    	if(!empty($lid))
    	{
	        $sql = "SELECT lottery_id AS lotteryId, `status`, window, ahead FROM cp_lottery_config where lottery_id = ?";
	        $items = $this->cfgDB->query($sql, array($lid))->getAll();
    	}
    	else 
    	{
	    	$sql = "SELECT lottery_id AS lotteryId, `status`, window, ahead FROM cp_lottery_config";
	        $items = $this->cfgDB->query($sql)->getAll();
    	}

        return $items;
    }
    /**
     * [getLimitByLid 获取出票限制]
     * @author LiKangJian 2017-07-28
     * @param  [type] $lid [description]
     * @return [type]      [description]
     */
    public function getLimitByLid($lid)
    {
        $sql = "SELECT order_limit FROM cp_lottery_config where lottery_id = ?";
        return  $this->cfgDB->query($sql, array($lid))->getCol();
    }
    public function getEndTime($lid)
    {
        $sql = "SELECT ahead FROM cp_lottery_config where lottery_id = ?";
        return  $this->cfgDB->query($sql, array($lid))->getCol();
    }
    public function deliveryConfigItems($configItems = array(), $lid = 0)
    {
    	if(empty($configItems))
    	{
    		$maplid = array('10' => 11);
    		$lid = empty($maplid[$lid]) ? $lid : $maplid[$lid];
    		$configItems = $this->fetchConfigItems($lid);
    	}
        foreach ($configItems as $item)
        {
            $type = $this->getLotteryType($item['lotteryId']);
            if ( ! $type)
            {
                continue;
            }

            if (in_array($type, array('jczq', 'jclq')))
            {
                $this->deliveryJJC($type, $item);
                continue;
            }

            if (empty($item['window']))
            {
                continue;
            }

            $currentIssueId = $this->fetchCurrentIssueId($type);
            if ( ! $currentIssueId)
            {
                continue;
            }

            $updateIssueIds = $this->fetchUpdateIssueIds($type, $currentIssueId, $item['window']);
            if (empty($updateIssueIds))
            {
                continue;
            }

            $table = $this->decideIssueTable($type);
            $issueField = $this->decideIssueField($type);
            $endTimeField = $this->decideEndTimeField($type);
            $idStr = '(' . implode(',', $updateIssueIds) . ')';
            $openField = $this->decideOpenField($type);
            if (in_array($type, array('sfc', 'rj')))
            {
                $closeSql = "UPDATE $table
                    SET $openField = 0, rj_open = 0, show_end_time = $endTimeField
                    WHERE $issueField > $currentIssueId";
                $this->cfgDB->query($closeSql);
                $sql = "UPDATE $table SET $openField = 1, rj_open = 1,
                    show_end_time = TIMESTAMPADD(MINUTE, -{$item['ahead']}, $endTimeField)
                    WHERE $issueField IN $idStr";
            }
            else
            {
                $closeSql = "UPDATE $table SET $openField = 0, show_end_time = $endTimeField
                    WHERE $issueField > $currentIssueId";
                $this->cfgDB->query($closeSql);
                $sql = "UPDATE $table SET $openField = 1,
                    show_end_time = TIMESTAMPADD(MINUTE, -{$item['ahead']}, $endTimeField)
                    WHERE $issueField IN $idStr";
            }
            $this->cfgDB->query($sql);
        }
    }

    public function openHistoryIssues($lotteryIds)
    {
        foreach ($lotteryIds as $lotteryId)
        {
            $type = $this->getLotteryType($lotteryId);
            if ( ! $type)
            {
                continue;
            }

            if (in_array($type, array('jczq', 'jclq')))
            {
                continue;
            }

            $currentIssueId = $this->fetchCurrentIssueId($type);
            if ( ! $currentIssueId)
            {
                continue;
            }

            $updateIssueIds = $this->fetchHistoryIssueIds($type, $currentIssueId);
            if (empty($updateIssueIds))
            {
                continue;
            }

            $table = $this->decideIssueTable($type);
            $issueField = $this->decideIssueField($type);
            $idStr = '(' . implode(',', $updateIssueIds) . ')';
            $openField = $this->decideOpenField($type);
            if (in_array($type, array('sfc', 'rj')))
            {
                $closeSql = "UPDATE $table SET $openField = 0, rj_open = 0
                    WHERE $issueField > $currentIssueId";
                $this->cfgDB->query($closeSql);
                $sql = "UPDATE $table SET $openField = 1, rj_open = 1
                    WHERE $issueField IN $idStr";
            }
            else
            {
                $closeSql = "UPDATE $table SET $openField = 0
                    WHERE $issueField > $currentIssueId";
                $this->cfgDB->query($closeSql);
                $sql = "UPDATE $table SET $openField = 1 WHERE $issueField IN $idStr";
            }
            $this->cfgDB->query($sql);
        }
    }

    private function deliveryJJC($type, $item)
    {
        $table = $this->decideIssueTable($type);
        $endTimeField = $this->decideEndTimeField($type);
        $sql = "UPDATE $table SET show_end_time =
            TIMESTAMPADD(MINUTE, -{$item['ahead']}, $endTimeField)
            where $endTimeField > now()";
        $this->cfgDB->query($sql);

        //竞足修正
        if ($type == 'jczq')
        {
            // 世界杯调整
            $dates_01 = array('2018-06-16', '2018-06-18', '2018-06-19', '2018-06-20', '2018-06-21', '2018-06-22', '2018-06-23', '2018-06-24', '2018-06-25', '2018-06-26', '2018-06-27', '2018-06-28', '2018-06-29', '2018-06-30', '2018-07-01', '2018-07-02', '2018-07-03', '2018-07-04', '2018-07-07', '2018-07-08', '2018-07-11', '2018-07-12');
            $dates_02 = array('2018-07-05', '2018-07-06', '2018-07-09', '2018-07-10', '2018-07-13', '2018-07-14', '2018-07-15', '2018-07-16');
            $dates_03 = array('2018-06-17');

            //周二至周六，从00：00往前推；周一与周日，从01：00往前推
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']}, DATE($endTimeField))
                    WHERE $endTimeField >= NOW() AND WEEKDAY($endTimeField) IN (1, 2, 3, 4, 5)
                    AND TIME($endTimeField) < '09:30:00'
                    AND DATE($endTimeField) NOT IN ('" . implode("', '", array_merge($dates_01, $dates_02, $dates_03)) . "')";
            $this->cfgDB->query($patchSql);

            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']},
                    CONCAT(DATE($endTimeField), ' 01:00:00'))
                    WHERE $endTimeField >= NOW() AND WEEKDAY($endTimeField) IN (0, 6)
                    AND $endTimeField > CONCAT(DATE($endTimeField), ' 01:00:00')
                    AND TIME($endTimeField) < '09:30:00'
                    AND DATE($endTimeField) NOT IN ('" . implode("', '", array_merge($dates_01, $dates_02, $dates_03)) . "')";
            $this->cfgDB->query($patchSql);
            
            /*
             * 去掉欧洲杯时间处理逻辑
             * */
            //欧洲杯特殊时间处理逻辑
            // $dates_03 = array('2016-06-11', '2016-06-12', '2016-06-13', '2016-06-14', '2016-06-15', 
            // '2016-06-16', '2016-06-17', '2016-06-18', '2016-06-19', '2016-06-20', '2016-06-21', 
            // '2016-06-22', '2016-06-23', '2016-06-26', '2016-06-27', '2016-06-28', '2016-07-01',
            // '2016-07-02', '2016-07-03', '2016-07-04', '2016-07-07', '2016-07-08', '2016-07-11');
            
            //$dates_03 = array('2016-06-03', '2016-06-04', '2016-06-05');
            
            // $dates_00 = array('2016-06-24', '2016-06-25', '2016-06-29', '2016-06-30', '2016-07-05', 
            // '2016-07-06', '2016-07-09', '2016-07-10');
            
            //$dates_00 = array('2016-06-14', '2016-06-12', '2016-06-13');
            
            // $patchSql = "UPDATE $table SET show_end_time =
            //         TIMESTAMPADD(MINUTE, -{$item['ahead']}, $endTimeField)
            //         WHERE $endTimeField >= NOW() 
            //         AND date($endTimeField) IN ('" . implode("', '", $dates_03) . "')
            //         AND $endTimeField >= DATE($endTimeField)
            //         AND TIME($endTimeField) < '10:00:00'";
            //$this->cfgDB->query($patchSql);
            
            
            // $timetail = "if(DATE($endTimeField) in('" . implode("', '", $dates_03) . "'), '03:00:00', 
            //  if(DATE($endTimeField) in('" . implode("', '", $dates_00) . "'), '00:00:00', ''))";
            
            // $patchSql = "UPDATE $table SET show_end_time =
            //         TIMESTAMPADD(MINUTE, -{$item['ahead']},
            //         CONCAT(DATE($endTimeField), ' ', $timetail))
            //         WHERE $endTimeField >= NOW() AND DATE($endTimeField) 
            //         IN ('" . implode("', '", array_merge($dates_03, $dates_00)) . "')
            //         AND $endTimeField >= CONCAT(DATE($endTimeField), ' ', $timetail)
            //         AND TIME($endTimeField) < '10:00:00'";
            //$this->cfgDB->query($patchSql);

            // 世界杯调整
            // $dates_01 = array('2018-06-16', '2018-06-19', '2018-06-20', '2018-06-21', '2018-06-22', '2018-06-23', '2018-06-24', '2018-06-26', '2018-06-27', '2018-06-28', '2018-06-29', '2018-06-30', '2018-07-01', '2018-07-03', '2018-07-04', '2018-07-07', '2018-07-08', '2018-07-11', '2018-07-12');
            // $dates_02 = array('2018-07-05', '2018-07-06', '2018-07-10', '2018-07-13', '2018-07-14', '2018-07-15');
            // $dates_03 = array('2018-06-17');

            $timetail = "if(DATE($endTimeField) in('" . implode("', '", $dates_01) . "'), '02:00:00', 
            if(DATE($endTimeField) in('" . implode("', '", $dates_02) . "'), '00:00:00', 
            if(DATE($endTimeField) in('" . implode("', '", $dates_03) . "'), '03:00:00', '')))";
            
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']},
                    CONCAT(DATE($endTimeField), ' ', $timetail))
                    WHERE $endTimeField >= NOW() AND DATE($endTimeField) 
                    IN ('" . implode("', '", array_merge($dates_01, $dates_02, $dates_03)) . "') 
                    AND $endTimeField >= CONCAT(DATE($endTimeField), ' ', $timetail)
                    AND TIME($endTimeField) < '09:30:00'";
            $this->cfgDB->query($patchSql);
            
        }

        //竞篮修正
        if ($type == 'jclq')
        {
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']}, DATE($endTimeField))
                    WHERE $endTimeField >= NOW()
                    AND (WEEKDAY($endTimeField) IN (1, 4, 5)
                    AND TIME($endTimeField) < '09:30:00')";
            $this->cfgDB->query($patchSql);
            
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']}, DATE($endTimeField))
                    WHERE $endTimeField >= NOW()
                    AND (WEEKDAY($endTimeField) IN (2, 3)
                    AND TIME($endTimeField) < '08:00:00')";
            $this->cfgDB->query($patchSql);
            
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']},
                    CONCAT(DATE($endTimeField), ' 01:00:00'))
                    WHERE $endTimeField >= NOW() AND WEEKDAY($endTimeField) IN (0, 6)
                    AND TIME($endTimeField) > '01:00:00'
                    AND TIME($endTimeField) < '09:30:00'";
            $this->cfgDB->query($patchSql);

            // 世界杯调整 6月14日-7月15日期间销售时间为每天早上9点到晚上24点截止
            $patchSql = "UPDATE $table SET show_end_time =
                    TIMESTAMPADD(MINUTE, -{$item['ahead']}, DATE($endTimeField))
                    WHERE $endTimeField >= NOW()
                    AND DATE($endTimeField) >= '2018-06-14'
                    AND DATE($endTimeField) <= '2018-07-16'
                    AND TIME($endTimeField) < '09:30:00'";
            $this->cfgDB->query($patchSql);
        }
    }

    private function decideOpenField($type)
    {
        //之前任九要与胜负彩分离，通用的is_open针对任九改为rj_open，
        //现在不区分，再改回来，任九与胜负彩将一直是一致的配置
        return 'is_open';
    }

    private function getLotteryType($lotteryId)
    {
        $lotteryMap = $this->orderConfig('lidmap');
        $type = $lotteryMap[$lotteryId];

        $typeTransform = array(
            'pls'  => 'pl3',
            'plw'  => 'pl5',
            'fcsd' => 'fc3d',
        );
        if (array_key_exists($type, $typeTransform))
        {
            $type = $typeTransform[$type];
        }

        return $type;
    }

    private function decideIssueTable($type)
    {
        $table = $this->isSoccerLottery($type) ? "cp_tczq_paiqi" : "cp_{$type}_paiqi";

        return $table;
    }

    private function decideIssueField($type)
    {
        $field = in_array($type, array('bjdc', 'sfgg', 'sfc', 'rj', 'bqc', 'jqc')) ? 'mid' : 'issue';

        return $field;
    }

    private function fetchCurrentIssueId($type)
    {
        $table = $this->decideIssueTable($type);
        $issueField = $this->decideIssueField($type);
        $endTimeField = $this->decideEndTimeField($type);
        $sql = "SELECT $issueField FROM $table WHERE $endTimeField > NOW()";
        if ($this->isSoccerLottery($type))
        {
            $sql .= " AND ctype = " . $this->soccerCTypeAry[$type];
        }
        $sql .= " ORDER BY $endTimeField LIMIT 1";
        $currentIssueId = $this->cfgDB->query($sql)->getOne();

        return $currentIssueId;
    }

    private function isNumericalLottery($type)
    {
        return in_array($type, array(
            'dlt',
            'fc3d',
            'pl3',
            'pl5',
            'qlc',
            'qxc',
            'ssq',
            'syxw',
        	'jxsyxw',
        	'ks',
            'jlks',
            'jxks',
        	'hbsyxw',
            'klpk',
            'cqssc',
            'gdsyxw',
        ));
    }

    private function fetchUpdateIssueIds($type, $currentId, $window)
    {
        $table = $this->decideIssueTable($type);
        $issueField = $this->decideIssueField($type);
        if ($this->isNumericalLottery($type))
        {
            $sql = "SELECT DISTINCT $issueField FROM $table
                WHERE $issueField >= $currentId AND delect_flag = 0
                ORDER BY $issueField LIMIT $window";
        }
        else
        {
            $sql = "SELECT DISTINCT $issueField FROM $table
                WHERE $issueField >= $currentId
                ORDER BY $issueField LIMIT $window";
        }
        $ids = $this->cfgDB->query($sql)->getCol();

        return $ids;
    }

    private function fetchHistoryIssueIds($type, $currentId)
    {
        $table = $this->decideIssueTable($type);
        $issueField = $this->decideIssueField($type);
        $sql = "SELECT DISTINCT $issueField FROM $table WHERE $issueField < $currentId
            ORDER BY $issueField";
        $ids = $this->cfgDB->query($sql)->getCol();

        return $ids;
    }

    private function decideEndTimeField($type)
    {
        if ($this->isSoccerLottery($type))
        {
            $field = 'end_sale_time';
        }
        elseif (in_array($type, array('bjdc', 'jclq')))
        {
            $field = 'begin_time';
        }
        elseif (in_array($type, array('jczq')))
        {
            $field = 'end_sale_time';
        }
        elseif (in_array($type, array('dlt', 'fc3d', 'pl3', 'pl5', 'qlc', 'qxc', 'ssq', 'syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            $field = 'end_time';
        }

        return $field;
    }

    private function isSoccerLottery($type)
    {
        return array_key_exists($type, $this->soccerCTypeAry);
    }

    /**
     * 返回现有预排期次数量
     *
     * @param unknown_type $table
     */
    public function getCountNumberIssue($table)
    {
        return $this->cfgDB->query("SELECT COUNT(*) FROM {$table} WHERE award_time>NOW() AND status='0' and delect_flag='0'")->getOne();
    }

    public function saveAlert($ctype, $content = '')
    {
        $sql = "INSERT INTO cp_alert_log(ctype,title,content,status,created) VALUES (?,'期次预排报警',?, '0', NOW())";
        $this->db->query($sql, array($ctype, $content));
    }

    public function getCountTczqIssue($table)
    {
        return $this->cfgDB->query("SELECT COUNT(DISTINCT mid) FROM {$table} WHERE end_sale_time>NOW() AND status<'50'")->getOne();
    }
}