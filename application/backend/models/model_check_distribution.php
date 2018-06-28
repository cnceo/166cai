<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/5/28
 * 修改时间: 11:08
 */
class Model_Check_Distribution extends MY_Model
{
    private $tbName = 'cp_check_distribution';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：conAry
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：获取对比不一致的数据
     * 修改日期：2015-06-25
     */
    public function getUnmatchedItems($conAry, $page, $pageCount)
    {
        //派奖核对不需要展示竞彩足球和竞彩篮球的数据
        $sql = "SELECT lottery_id lotteryId, issue, total_sales totalSales, total_ticket totalTicket, bonus, margin,
            bonus_t ticketBonus, margin_t ticketMargin, distributed
            FROM {$this->tbName}
            WHERE lottery_id NOT IN (42, 43)";
        $countSql = "SELECT COUNT(*) FROM {$this->tbName} WHERE lottery_id NOT IN (42, 43)";

        $conditions = array();
        $params = array();
        if ($conAry)
        {
            foreach ($conAry as $key => $value)
            {
                $conditions[] = " $key = ? ";
                $params[] = $value;
            }
            $condition =' AND ' . implode(' AND ', $conditions);
            $sql .= " $condition ";
            $countSql .= " $condition ";
        }
        $pageStart = ($page - 1) * $pageCount;
        $sql .= " LIMIT $pageStart, $pageCount";

        if ($conditions)
        {
            $items = $this->slaveCfg1->query($sql, $params)->getAll();
            $count = $this->slaveCfg1->query($countSql, $params)->getOne();
        }
        else
        {
            $items = $this->slaveCfg1->query($sql)->getAll();
            $count = $this->slaveCfg1->query($countSql)->getOne();
        }

        return array($items, $count);
    }

    /**
     * 参    数：lotteryId
     *           issue
     * 作    者：刁寿钧
     * 功    能：对某一彩种某一期次执行派奖
     * 修改日期：2015-06-25
     */
    public function forceDistribution($lotteryId, $issue)
    {
    	$tables = $this->getSplitTable($lotteryId);
        $this->cfgDB->trans_start();
        $setDistributedSql = "UPDATE {$this->tbName} SET unmatched = 0, distributed = 1
            WHERE lottery_id = ? AND issue = ?";
        $this->cfgDB->query($setDistributedSql, array($lotteryId, $issue));
        $orderIdSql = "SELECT orderId FROM cp_orders_inconsistent WHERE lid = ? AND issue = ?";
        $orderIds = $this->cfgDB->query($orderIdSql, array($lotteryId, $issue))->getCol();
        if ($orderIds)
        {
            $orderIdStr = implode("', '", $orderIds);
            $setDispatchSql = "UPDATE cp_orders_inconsistent SET distributed = 0, dispatch = 1
                WHERE orderId IN ('$orderIdStr')";
            $this->cfgDB->query($setDispatchSql);
            $setStateSql = "UPDATE {$tables['split_table']} SET cpstate = 1 WHERE orderId IN ('$orderIdStr')";
            $this->cfgDB->query($setStateSql);
        }
        $this->cfgDB->trans_complete();
        $querySql = "SELECT distributed FROM {$this->tbName} WHERE lottery_id = ? AND issue = ?";
        $success = $this->cfgDB->query($querySql, array($lotteryId, $issue))->getOne();

        return $success;
    }
    /**
     * 参    数：lotteryId
     *           issue
     * 作    者：胡小明
     * 功    能：对某一彩种某一期次执行派奖
     * 修改日期：2017-08-21
     */
    public function forceDispatch($issue, $lotteryId, $ts)
    {
    	$cpbonus = array('caidou' => 1, 'qihui' => 2, 'shancai' => 4, 'huayang' => 8, 'hengju' => 16);
        $this->lidmap = $this->orderConfig('lidmap');
        $mylidmap = array('52' => 'fc3d', '33' => 'pl3', '35' => 'pl5');
    	$piqitable = empty($mylidmap[$lotteryId]) ? $this->lidmap[$lotteryId] : $mylidmap[$lotteryId]; 
    	$tables = $this->getSplitTable($lotteryId);
    	$setStateSql = "UPDATE {$tables['split_table']} SET cpstate = 1, bonus_t = bonus,
    	margin_t = margin WHERE lid = ? and issue = ? and ticket_seller = ?
    	and status in(1000, 2000) and cpstate = 0";
    	$this->cfgDB->trans_start();
    	$sql = "update cp_{$piqitable}_paiqi set cd_bonus = (cd_bonus | {$cpbonus[$ts]}) where issue= ?";
        $re = $this->cfgDB->query($setStateSql, array($lotteryId, $issue, $ts));
        $re = $re && ($this->cfgDB->query($sql, array($issue)));
        if($re)
        {
        	$this->cfgDB->trans_complete();
        }
        else 
        {
        	$this->cfgDB->trans_rollback();
        }
        return $re;
    }
    

    /**
     * 参    数：lotteryId
     *           issue
     * 作    者：刁寿钧
     * 功    能：对某一彩种某一期次确认派奖
     * 修改日期：2015-06-25
     */
    public function fakeDistribution($lotteryId, $issue)
    {
        $setDistributedSql = "UPDATE {$this->tbName} SET distributed = 1 WHERE lottery_id = ? AND issue = ?";
        $this->cfgDB->query($setDistributedSql, array($lotteryId, $issue));
        $querySql = "SELECT distributed FROM {$this->tbName} WHERE lottery_id = ? AND issue = ?";
        $success = $this->cfgDB->query($querySql, array($lotteryId, $issue))->getOne();

        return $success;
    }

//    /**
//     * 参    数：lotteryId
//     *           issue
//     * 作    者：刁寿钧
//     * 功    能：是否可以执行派奖
//     * 修改日期：2015-06-25
//     */
//    public function canDistributeOld($lotteryId, $issue)
//    {
//        $this->cfgDB->from('cp_orders_split');
//        $this->cfgDB->where('lid', $lotteryId);
//        $this->cfgDB->where('issue', $issue);
//        $this->cfgDB->where('cpstate <', 2);
//        $count = $this->cfgDB->count_all_results();
//
//        return ! $count;
//    }

    /**
     * 参    数：lotteryId
     *           issue
     * 作    者：刁寿钧
     * 功    能：是否可以执行派奖
     * 修改日期：2015-06-29
     */
    public function canDistribute($lotteryId, $issue)
    {
    	$tables = $this->getSplitTable($lotteryId);
        $sql = "SELECT COUNT(*) FROM {$tables['split_table']} WHERE lid = ? AND issue = ? AND cpstate < 2 and status <> 600";
        $count = $this->slaveCfg1->query($sql, array($lotteryId, $issue))->getOne();

        return ! $count;
    }

    /**
     * 参    数：lotteryId
     *           issueId
     *           page = NULL
     *           pageSize = NULL
     * 作    者：刁寿钧
     * 功    能：获取某一彩种某一期次所有不一致的订单
     * 修改日期：2015-06-25
     */
    public function getUnmatchedOrders($lotteryId, $issueId, $page = NULL, $pageSize = NULL)
    {
        $this->slaveCfg1->from('cp_orders_inconsistent');
        $this->slaveCfg1->where('lid', $lotteryId);
        $this->slaveCfg1->where('issue', $issueId);
        $this->slaveCfg1->where('distributed', 1);
        $this->slaveCfg1->where('dispatch', 0);
        $count = $this->slaveCfg1->count_all_results();

        $this->slaveCfg1->select('orderId, bonus, bonus_t ticketBonus, margin, margin_t ticketMargin');
        $this->slaveCfg1->where('lid', $lotteryId);
        $this->slaveCfg1->where('issue', $issueId);
        $this->slaveCfg1->where('distributed', 1);
        $this->slaveCfg1->where('dispatch', 0);
        if ($page && $pageSize)
        {
            $this->slaveCfg1->limit($pageSize, ($page - 1) * $pageSize);
        }
        $resultAry = $this->slaveCfg1->get('cp_orders_inconsistent')->result_array();

        return array($resultAry[0], $count);
    }

}