<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2015/6/1
 * 修改时间: 15:05
 */
class Model_Issue_Cfg extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //获取对应期号数据
    /**
     * 参    数：issue
     *           type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getSelectIssue($issue, $type)
    {
        $sql = "SELECT issue, sale_time, end_time, award_time
            FROM cp_{$type}_paiqi
            WHERE issue = ? and delect_flag = 0;";
        $issueInfo = $this->slaveCfg1->query($sql, array($issue))->getAll();

        return $issueInfo;
    }

    //查询彩种期次信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_number($searchData, $page, $pageCount)
    {
        if (empty($searchData['type'])) {
            $searchData['type'] = 'ssq';
        }

        $where = " 1";
        if (in_array($searchData['type'], array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw'))) {
            $where .= " AND sale_time >= '{$searchData['start_time']}' AND sale_time <= '{$searchData['end_time']}' AND delect_flag = 0";
        }

        $sql1 = "SELECT id, issue, sale_time, end_time, award_time, awardNum, sale, pool, bonusDetail, status, rstatus,
            is_open, IF (show_end_time = '0000-00-00 00:00:00', end_time, show_end_time) showEndTime
            FROM cp_{$searchData['type']}_paiqi
            WHERE {$where} AND delect_flag = 0
            ORDER BY issue DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) FROM cp_{$searchData['type']}_paiqi WHERE {$where} AND delect_flag = 0";
        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }

    //北京单场 彩果信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_bjdc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT mid issue, MIN(begin_time) sale_time, MAX(begin_time) end_time,
            sum(if(status < 50, 1, 0)) status, sum(if(state = 1, 0, 1)) rstatus, is_open,
            IF (MAX(show_end_time) = '0000-00-00 00:00:00', MAX(begin_time), MAX(show_end_time)) showEndTime
            FROM cp_{$searchData['type']}_paiqi
            GROUP BY mid ORDER BY mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi GROUP BY mid) a;";

        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }

    //北京单场 胜负过关
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_sfgg($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT mid issue, MIN(begin_time) sale_time, MAX(begin_time) end_time,
            sum(if(status < 50, 1, 0)) status, sum(if(state = 1, 0, 1)) rstatus, is_open,
            IF (MAX(show_end_time) = '0000-00-00 00:00:00', MAX(begin_time), MAX(show_end_time)) showEndTime
            FROM cp_{$searchData['type']}_paiqi
            GROUP BY mid ORDER BY mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi GROUP BY mid) a;";

        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }

    //胜负彩  彩果信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_sfc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid issue, t.start_sale_time sale_time, t.end_sale_time end_time, r.result awardNum,
            r.rj_sale, r.sfc_sale, r.award pool, r.award_detail bonusDetail, r.status, r.rstatus, t.is_open,
            IF (t.show_end_time = '0000-00-00 00:00:00', t.end_sale_time, t.show_end_time) showEndTime
            FROM cp_tczq_paiqi AS t
            LEFT JOIN cp_rsfc_paiqi AS r ON t.mid = r.mid
            WHERE t.ctype = 1 GROUP BY t.mid ORDER BY t.mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 GROUP BY mid) a;";

        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }
    
    //任选九 彩果信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_rj($searchData, $page, $pageCount)
    {
    	$sql1 = "SELECT t.mid issue, t.start_sale_time sale_time, t.end_sale_time end_time, r.result awardNum,
    	    r.rj_sale, r.sfc_sale, r.award pool, r.award_detail bonusDetail,
    	    r.rjstatus status, r.rjrstatus rstatus, t.rj_open is_open,
    	    IF (t.show_end_time = '0000-00-00 00:00:00', t.end_sale_time, t.show_end_time) showEndTime
    	    FROM cp_tczq_paiqi AS t
    	    LEFT JOIN cp_rsfc_paiqi AS r ON t.mid = r.mid
    	    WHERE t.ctype = 1 GROUP BY t.mid ORDER BY t.mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    
    	$list = $this->slaveCfg1->query($sql1)->getAll();
    
    	$sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 GROUP BY mid) a;";
    
    	$count = $this->slaveCfg1->query($sql2)->getCol();
    
    	return array($list, $count[0]);
    }

    //半全场 彩果信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_bqc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid issue, t.start_sale_time sale_time, t.end_sale_time end_time, r.result awardNum,
            r.sale, r.award pool, r.award_detail bonusDetail, r.status, r.rstatus, t.is_open,
            IF (t.show_end_time = '0000-00-00 00:00:00', t.end_sale_time, t.show_end_time) showEndTime
            FROM cp_tczq_paiqi t
            LEFT JOIN cp_rbqc_paiqi AS r ON t.mid = r.mid
            WHERE t.ctype = 2 GROUP BY t.mid ORDER BY t.mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 2 GROUP BY mid) a;";

        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }

    //进球彩 彩果信息
    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getIssueList_jqc($searchData, $page, $pageCount)
    {
        $sql1 = "SELECT t.mid issue, t.start_sale_time sale_time, t.end_sale_time end_time, r.result awardNum,
            r.sale, r.award pool, r.award_detail bonusDetail, r.status, r.rstatus, t.is_open,
            IF (t.show_end_time = '0000-00-00 00:00:00', t.end_sale_time, t.show_end_time) showEndTime
            FROM cp_tczq_paiqi t
            LEFT JOIN cp_rjqc_paiqi AS r ON t.mid = r.mid
            WHERE t.ctype = 3 GROUP BY t.mid ORDER BY t.mid DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;

        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 3 GROUP BY mid) a;";

        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list, $count[0]);
    }

    //查询开奖详情
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getDetailList($data)
    {
        $list = array();
        if ( ! empty($data)) {
            $sql = "SELECT id, issue, sale_time, end_time, award_time, awardNum, sale, pool, bonusDetail, status
                FROM cp_{$data['lid']}_paiqi WHERE issue = {$data['issue']} AND delect_flag = 0;";
            $list = $this->slaveCfg1->query($sql)->getRow();
        }

        return $list;
    }

    //查询开奖详情 胜负彩
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getDetailList_sfc($data)
    {
        $list = array();
        if ( ! empty($data)) {
            $sql = "SELECT mid as issue, result as awardNum, rj_sale, sfc_sale, award as pool, award_detail as bonusDetail FROM cp_rsfc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveCfg1->query($sql)->getRow();
        }

        return $list;
    }

    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getDetailList_rj($data)
    {
        $list = array();
        if ( ! empty($data)) {
            $sql = "SELECT mid issue, result awardNum, rj_sale, sfc_sale, award pool, award_detail bonusDetail
                FROM cp_rsfc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveCfg1->query($sql)->getRow();
        }

        return $list;
    }

    //更新开奖详情 胜负彩
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function updateDetailList_sfc($data)
    {
        $sql = "UPDATE cp_rsfc_paiqi SET result = ?, rj_sale = ?, sfc_sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
        $res = $this->cfgDB->query($sql, array(
            $data['awardNum'],
            $data['rj_sale'],
            $data['sfc_sale'],
            $data['pool'],
            $data['bonusDetail'],
            $data['issue']
        ));

        return $res;
    }

    //查询开奖详情 半全场
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getDetailList_bqc($data)
    {
        $list = array();
        if ( ! empty($data)) {
            $sql = "SELECT mid as issue, result as awardNum, sale, award as pool, award_detail as bonusDetail FROM cp_rbqc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveCfg1->query($sql)->getRow();
        }

        return $list;
    }

    //更新开奖详情 半全场
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function updateDetailList_bqc($data)
    {
        $sql = "UPDATE cp_rbqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
        $res = $this->cfgDB->query($sql,
            array($data['awardNum'], $data['sale'], $data['pool'], $data['bonusDetail'], $data['issue']));

        return $res;
    }

    //查询开奖详情 进球彩
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getDetailList_jqc($data)
    {
        $list = array();
        if ( ! empty($data)) {
            $sql = "SELECT mid as issue, result as awardNum, sale, award as pool, award_detail as bonusDetail FROM cp_rjqc_paiqi WHERE mid = {$data['issue']};";
            $list = $this->slaveCfg1->query($sql)->getRow();
        }

        return $list;
    }

    //更新开奖详情 进球彩
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function updateDetailList_jqc($data)
    {
        $sql = "UPDATE cp_rjqc_paiqi SET result = ?, sale = ?, award = ?, award_detail = ?, state = 1, status = 50, rstatus = 50 WHERE mid = ?;";
        $res = $this->cfgDB->query($sql,
            array($data['awardNum'], $data['sale'], $data['pool'], $data['bonusDetail'], $data['issue']));

        return $res;
    }

    /**
     * 参    数：issues
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function saleStart($issues)
    {
        $sql = "update cp_syxw_paiqi set status = 1 where status < 2 and issue in ('" . implode("','", $issues) . "')";

        return $this->cfgDB->query($sql);
    }

    /**
     * 参    数：issues
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function deleteIssue($issues, $type='syxw')
    {
        $sql = "update cp_{$type}_paiqi set delect_flag = 1 where delect_flag = 0 and status < 60 and rstatus < 80 and issue in ('" . implode("','",
                $issues) . "')";
        return $this->cfgDB->query($sql);
    }

    /**
     * 参    数：issue
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function bjdcStart($issue)
    {
        $sql = "update cp_bjdc_paiqi set status = 1 where status < 1 and mid = ?";

        return $this->cfgDB->query($sql, array($issue));
    }

    /**
     * 参    数：issue
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function sfcStart($issue)
    {
        $sql = "update cp_tczq_paiqi set status = 1 where status < 1 and mid = ? and ctype = 1";

        return $this->cfgDB->query($sql, array($issue));
    }

    //获取对比异常的数据
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getCompareDetail($data)
    {
        $sql = "SELECT a.lid, a.issue, a.awardNum, a.time, a.sale, a.pool, a.bonusDetail, a.status, a.state, a.source, s.lname
            FROM cp_number_award as a
            LEFT JOIN cp_cron_score as s ON a.lid = s.ctype AND a.source = s.source
            WHERE a.lid = ? AND a.issue = ?;";
        $detail = $this->slaveCfg1->query($sql, array($data['type'], $data['issue']))->getAll();

        return $detail;
    }

    //获取对比异常的数据 体彩足球
    /**
     * 参    数：data
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getCompareDetailByTczq($data)
    {
        $dbName = array(
            'sfc' => 'rsfc',
            'bqc' => 'rbqc',
            'jqc' => 'rjqc',
        );
        $db = $dbName[$data['type']];
        $where = "";
        $add = "";
        if ($data['type'] == 'sfc') {
            $where = " p.lid = s.ctype AND";
            $add = " p.lid = '{$data['type']}' AND";
        }
        $sql = "SELECT p.result as awardNum, s.lname FROM cp_{$db}_score as p
            LEFT JOIN cp_cron_score as s ON{$where} p.source = s.source
            WHERE{$add} p.mid = ? GROUP BY p.source;";
        $detail = $this->slaveCfg1->query($sql, array($data['issue']))->getAll();

        return $detail;
    }

    //获取期次管理的开始页面 数字彩
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod($searchData)
    {
        $sql = "SELECT count(*) FROM cp_{$searchData['type']}_paiqi WHERE award_time >= now() AND delect_flag = 0";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    //期次管理的开始页面 北京单场
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_bjdc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi WHERE begin_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    //期次管理的开始页面 胜负过关
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_sfgg($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid, MIN(begin_time), MAX(begin_time) FROM cp_{$searchData['type']}_paiqi WHERE begin_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    //期次管理的开始页面 胜负彩
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_sfc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_rj($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 1 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    //期次管理的开始页面 半全场
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_bqc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 2 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    //期次管理的开始页面 进球彩
    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function pageMethod_jqc($searchData)
    {
        $sql = "SELECT count(*) from ( SELECT mid FROM cp_tczq_paiqi WHERE ctype = 3 AND start_sale_time >= NOW() GROUP BY mid) a;";
        $count = $this->slaveCfg1->query($sql)->getCol();

        return $count;
    }

    /**
     * 参    数：type
     *           issue
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function openIssue($type, $issue)
    {
        $issueField = $this->decideIssueField($type);
        $table = $this->decideIssueTable($type);
        $sql = "UPDATE $table SET is_open = 1 WHERE $issueField = ?";

        return $this->cfgDB->query($sql, $issue);
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function decideIssueField($type)
    {
        $field = in_array($type, array('bjdc', 'sfgg', 'sfc', 'bqc', 'jqc','rj')) ? 'mid' : 'issue';

        return $field;
    }

    /**
     * 参    数：type
     *           issueId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function closeIssue($type, $issueId)
    {
        $issueField = $this->decideIssueField($type);
        $table = $this->decideIssueTable($type);
        $cfgOrders = $this->config->item('cfg_orders');
        $statusField = 'rstatus';
        $sql = "UPDATE $table SET $statusField = ? WHERE $issueField = ?";

        return $this->cfgDB->query($sql, array($cfgOrders['paiqi_awarded'], $issueId));
    }

    /**
     * 参    数：type
     *           issueId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function canClose($type, $issueId)
    {
        $issueField = $this->decideIssueField($type);
        $table = $this->decideIssueTable($type);
        $cfgOrders = $this->config->item('cfg_orders');
        $sql = "SELECT 1 FROM $table WHERE $issueField = ? AND rstatus = ?";
        return $this->cfgDB->query($sql, array($issueId, $cfgOrders['paiqi_awarding']))->getOne();
    }

    /**
     * 参    数：type
     *           issue
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function showIssueStatus($type, $issue)
    {
        $statusSet = $this->config->item('cfg_orders');

        if ( ! in_array($type, array('bjdc', 'sfgg', 'tczq', 'jczq', 'jclq')))
        {
            //只针对期次管理中的页面
            if ( ! $issue['is_open'])
            {
                return "未开启";
            }
        }

		if (in_array($type, array('bjdc', 'sfgg')))
        {
            if (strtotime($issue['showEndTime']) > time())
            {
                $status = "开启";
            }
            else
            {
                if ($issue['status'] == 0 && $issue['rstatus'] == 0)
                {
                    $status = '结期';
                }
                else
                {
                    $status = "截止";
                }
            }
        }
        else
        {
            if(strtotime($issue['sale_time']) <= time() && strtotime($issue['showEndTime']) >= time())
            {
            	$status = "在售";
            }
            else
            {
            	if (strtotime($issue['showEndTime']) > time())
            	{
            		$status = "开启";
            	}
            	else
            	{
            		if ($issue['status'] == $statusSet['paiqi_ggsucc'])
            		{
            			$status = "已过关";
            			if ($issue['rstatus'] == $statusSet['paiqi_jjsucc'])
            			{
            				$status = "已计奖";
            			}
            			elseif ($issue['rstatus'] == $statusSet['paiqi_awarding'] || $issue['rstatus'] == $statusSet['chase_awarding'])
            			{
            				$status = "派奖中";
            			}
            			elseif ($issue['rstatus'] == $statusSet['paiqi_awarded'])
            			{
            				$status = "结期";
            			}
            		}
            		else
            		{
            			$status = "截止";
            		}
            	}
            }   
        }

        return $status;
    }

    /**
     * 参    数：type
     *           issueId
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function getAwardNum($type, $issueId)
    {
        if ( ! in_array($type, array('dlt', 'fc3d', 'pl3', 'pl5', 'qlc', 'qxc', 'ssq', 'syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')))
        {
            return '';
        }

        $this->cfgDB->select('awardNum');
        $issueField = $this->decideIssueField($type);
        $this->cfgDB->where($issueField, $issueId);
        $issueTable = $this->decideIssueTable($type);
        $resultAry = $this->cfgDB->get($issueTable)->row_array();
        return $this->getAwardFormat($type, $resultAry[0]['awardNum']);
    }

    // 快乐扑克开奖号码
    public function getAwardFormat($type, $awardNum = '')
    {
        switch ($type) 
        {
            case 'klpk':
                $formatArr = array(
                    '01' => 'A', 
                    '02' => '2',
                    '03' => '3',
                    '04' => '4',
                    '05' => '5',
                    '06' => '6',
                    '07' => '7',
                    '08' => '8',
                    '09' => '9',
                    '10' => '10',
                    '11' => 'J', 
                    '12' => 'Q', 
                    '13' => 'K', 
                    'S' => '黑桃', 
                    'H' => '红桃', 
                    'C' => '梅花', 
                    'D' => '方块'
                );
                $number = '';
                $awardArr = explode('|', $awardNum);
                $numArr = array_map('trim', explode(',', $awardArr[0]));
                $typeArr = array_map('trim', explode(',', $awardArr[1]));
                for ($i=0; $i < 3; $i++) 
                { 
                    $number .= $formatArr[$typeArr[$i]] . $formatArr[$numArr[$i]] . ' ';
                }
                break;         
            default:
                $number = $awardNum;
                break;
        }
        return $number;
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function decideIssueTable($type)
    {
        $table = $this->isSoccerLottery($type) ? "cp_tczq_paiqi" : "cp_{$type}_paiqi";

        return $table;
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function isSoccerLottery($type)
    {
        return in_array($type, array('sfc', 'rj', 'bqc', 'jqc'));
    }
}