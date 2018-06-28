<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 对比不一致的订单
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/11
 * 修改时间: 15:31
 */
class Model_Order_Inconsistent extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：orderId
     * 作    者：刁寿钧
     * 功    能：判定某一订单是否对比一致
     * 修改日期：2015-06-25
     */
    public function isConsistent($orderId)
    {
        $sql = "SELECT 1 FROM cp_orders_inconsistent WHERE orderId = ? AND distributed = 1 AND dispatch = 0";
        $isInContent = $this->slaveCfg1->query($sql, $orderId)->getOne();

        return ! $isInContent;
    }
}