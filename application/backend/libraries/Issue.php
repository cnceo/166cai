<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 期次相关函数
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/4
 * 修改时间: 9:54
 */
class Issue
{

    /**
     * 参    数：issue
     *           flag = 1
     *           len = 2
     * 作    者：刁寿钧
     * 功    能：格式化彩种期号
     * 修改日期：2015-06-29
     */
    public function formatIssue($issue, $flag = 1, $len = 2)
    {
        if ($len <= 0)
        {
            return $issue;
        }
        if ($flag)
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
     * 参    数：type
     *           pIssue
     * 作    者：刁寿钧
     * 功    能：由排期表期号得到订单表期号
     * 修改日期：2015-06-29
     */
    public function getSIssueByPIssue($type, $pIssue)
    {
        return in_array($type, array('sfc', 'rj', 'dlt', 'qxc', 'pl3', 'pl5'))
            ? $this->formatIssue($pIssue)
            : $pIssue;
    }

    /**
     * 参    数：type
     *           sIssue
     * 作    者：刁寿钧
     * 功    能：由订单表期号得到排期表期号
     * 修改日期：2015-06-29
     */
    public function getPIssueBySIssue($type, $sIssue)
    {
        return in_array($type, array('sfc', 'rj', 'dlt', 'qxc', 'pl3', 'pl5'))
            ? $this->formatIssue($sIssue, 0)
            : $sIssue;
    }
}