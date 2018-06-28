<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：订单管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.12.24
 */
class Model_chase extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
        $this->cfgDB = $this->load->database('cfg', TRUE);
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：shigx
     * 功    能：获取追号管理订单列表
     * 修改日期：2015.12.24
     */
    function listChase($searchData, $page, $pageCount)
    {
        $where = ' where 1 ';
        if($this->emp($searchData['uid']))
        {
            $where .= " and m.uid = '{$searchData['uid']}'";
        }
        if ($this->emp($searchData['name']))
        {
            $where .= " and (u.uname = '{$searchData['name']}' or m.chaseId ='{$searchData['name']}')";
        }
        $where .= $this->condition("m.lid", $searchData['lid']);
        $where .= $this->condition(" m.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $where .= $this->condition(" m.money", array(
            $searchData['start_money'],
            $searchData['end_money']
        ), "during", "m");
        if ($this->emp($searchData['status']))
        {
            // 已付款状态
            if($searchData['status'] == '1000')
            {
                $where .= " and m.status > '20'";
            }
            else
            {
                $where .= $this->condition("status", $searchData['status']); 
            } 
        }
        if ($this->emp($searchData['setStatus']))
        {
        	$where .= " and m.setStatus = '{$searchData['setStatus']}'";
        }
        if ($this->emp($searchData['buyPlatform']))
        {
        	$where .= " and m.buyPlatform = '{$searchData['buyPlatform']}'";
        }
        if($this->emp($searchData['chaseType']))
        {
        	if($searchData['chaseType'] == 'all')
        	{
        		$where .= " and m.chaseType in(1,2,3)";
        	}
        	else
        	{
        		$where .= $this->condition("m.chaseType", $searchData['chaseType']);
        	}
        }
        if($this->emp($searchData['notBonus']))
        {
        	$where .= $this->condition("m.bonus", 0);
        }
        $where1 = $where;
        if ($this->emp($searchData['registerChannel'])) 
        {
        	$where1 = $where." and u.channel = '{$searchData['registerChannel']}'";
        }
        if ($searchData['reg_type'] !== FALSE && $searchData['reg_type'] > 0)
        {
            if($searchData['reg_type'] == '1')
            {
                $where1 .= " and u.reg_type in ('0', '2')";
            }
            else
            {
                $where1 .= " and u.reg_type = ".$searchData['reg_type'];
            } 
        }
        $count = $this->BcdDb->query("SELECT COUNT(*) FROM cp_chase_manage as m inner join cp_user as u on m.uid=u.uid {$where1}")->getOne();
        $select = "select m.*, u.channel, u.reg_type, u.uname 
        from cp_chase_manage as m inner join cp_user as u on m.uid=u.uid 
        {$where1} ORDER BY m.created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->getAll();
        $total = "select count(distinct(m.uid)) as uid,sum(m.money) as amoney,sum(m.chaseMoney) as cmoney,sum(m.bonus) as bmoney,sum(m.margin) as mmoney 
        from cp_chase_manage as m inner join cp_user as u on m.uid=u.uid {$where1}";
        $sum = $this->BcdDb->query($total)->getRow();
             
        return array(
            $result,
            $count,
            array(
                $sum['amoney'],
                $sum['cmoney'],
            	$sum['bmoney'],
            	$sum['mmoney'],
            	$sum['uid']
            )
        );
    }
    
    /**
     * 参    数：$chaseId 追号订单id
     * 作    者：shigx
     * 功    能：获取追号管理记录
     * 修改日期：2015.12.24
     */
    public function getChaseOrder($chaseId)
    {
    	$sql = "SELECT a.*,min(b.issue) minIssue,max(b.issue) maxIssue FROM cp_chase_manage a INNER JOIN cp_chase_orders b ON a.chaseId = b.chaseId WHERE a.chaseId=?";
    	$result = $this->BcdDb->query($sql, array($chaseId))->getRow();
    	if($result)
    	{
    	    $uname = $this->BcdDb->query("select uname from cp_user where uid = ?", array($result['uid']))->getOne();
    		$result['userName'] = $uname ? $uname : $result['userName'];
    	}
    	
    	return $result;
    }
    
    /**
     * 参    数：$chaseId 追号订单id
     * 作    者：shigx
     * 功    能：获取追号子订单记录
     * 修改日期：2015.12.24
     */
    public function getSubOrder($chaseId)
    {
    	$sql = "SELECT id,sequence,orderId,lid,issue,money,multi,`status`,my_status,bonus,margin, award_time,bet_flag FROM cp_chase_orders WHERE chaseId=? ORDER BY sequence asc";
    	$subOrders = $this->BcdDb->query($sql, array($chaseId))->getAll();
    	$issues = array();
    	$lid = '';
    	foreach ($subOrders as $order)
    	{
    		$lid = $order['lid'];
    		if($order['award_time'] > date('Y-m-d H:i:s'))
    		{
    			continue;
    		}
    		$issue[] = in_array($order['lid'], array(23529, 10022, 33, 35)) ? substr($order['issue'], 2) : $order['issue'];
    	}
    	
    	if($issue)
    	{
    		$lidMap = array(
    			'51' => array('table' => 'cp_ssq_paiqi'),
    			'52' => array('table' => 'cp_fc3d_paiqi'),
    			'33' => array('table' => 'cp_pl3_paiqi'),
    			'35' => array('table' => 'cp_pl5_paiqi'),
    			'10022' => array('table' => 'cp_qxc_paiqi'),
    			'23528' => array('table' => 'cp_qlc_paiqi'),
    			'23529' => array('table' => 'cp_dlt_paiqi'),
    			'21406' => array('table' => 'cp_syxw_paiqi'),
    			'21407' => array('table' => 'cp_jxsyxw_paiqi'),
    			'53' => array('table' => 'cp_ks_paiqi'),
                '56' => array('table' => 'cp_jlks_paiqi'),
    		    '57' => array('table' => 'cp_jxks_paiqi'),
    			'21408' => array('table' => 'cp_hbsyxw_paiqi'),
                '54' => array('table' => 'cp_klpk_paiqi'),
                '55' => array('table' => 'cp_cqssc_paiqi'),
    		    '21421' => array('table' => 'cp_gdsyxw_paiqi'),
    		);
    		$sql = "select issue, awardNum from {$lidMap[$lid]['table']} where issue in (".implode(',', $issue).")";
    		$result = $this->slaveCfg1->query ($sql)->getAll();
    		$awards = array();
    		foreach ($result as $val)
    		{
    			$key = in_array($lid, array(23529, 10022, 33, 35)) ? '20' . $val['issue'] : $val['issue'];
    			$awards[$key] = $val['awardNum'];
    		}
    	}
    	return array('subOrders' => $subOrders, 'awards' => $awards);
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *       $page 页码
     *       $pageCount 单页条数
     * 作    者：shigx
     * 功    能：获取追号子订单列表
     * 修改日期：2016.01.08
     */
    public function listChaseOrder($searchData, $page, $pageCount)
    {
    	$where = ' where 1 ';
    	if ($this->emp($searchData['name']))
    	{
    		$where .= " and (b.userName = '{$searchData['name']}' or a.chaseId ='{$searchData['name']}')";
    	}
    	$where .= $this->condition("a.lid", $searchData['lid']);
    	$where .= $this->condition("a.issue", $searchData['issue']);
    	if($this->emp($searchData['status']))
    	{
    		$where .= " and a.status = '{$searchData['status']}'";
    	}
    	else
    	{
    		$where .= " and a.status in('0', '601', '602', '603')";
    	}
    	$where .= " and b.status >= '240'";
    	
    	$countSql = "SELECT
						COUNT(1) count,
						SUM(IF(a.`status` = '0', 1, 0)) dCount,
						SUM(IF(a.`status` = '601', 1, 0)) uCount,
						SUM(IF(a.`status` = '602', 1, 0)) sCount,
						SUM(IF(a.`status` = '603', 1, 0)) aCount
					FROM
						cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId = b.chaseId
					{$where}";
					$count = $this->BcdDb->query($countSql)->getRow();
    	$select = "SELECT
					a.id, a.chaseId, b.uid, b.userName, a.lid, b.playType, a.issue, a.multi, a.money, 
					a.`status`, a.cancel_flag
				FROM
					cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId = b.chaseId
				{$where} 
				 ORDER BY a.status ASC,a.created LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
				$result = $this->BcdDb->query($select)->getAll();
    	return array('data' => $result, 'total' => $count);
    }
    
    /**
     * 参    数：$lid 彩种id
     * 作    者：shigx
     * 功    能：查询期次号
     * 修改日期：2016.01.08
     */
    public function getIssueByLid($lid)
    {
    	if(in_array($lid, array('21406', '21407', '53', '56', '57', '21408', '54', '55', '21421')))
    	{
    		$limit = " LIMIT 780";
    	}
    	else
    	{
    		$limit = " LIMIT 150";
    	}
    	$sql = "select DISTINCT issue from cp_chase_orders where lid=? and status in('0', '601', '602', '603') order by `status` asc {$limit}";
    	return $this->BcdDb->query($sql, array($lid))->getCol();
    }
    
    /**
     * 参    数：$lid 彩种id
     * 		 $issue 期次
     * 作    者：shigx
     * 功    能：期次系统撤单
     * 修改日期：2016.01.08
     */
    public function cancelByIssue($lid, $issue)
    {
    	$sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId SET a.`status`='602' WHERE b.lid=? AND a.issue=? AND b.`status`='240' AND a.`status`='0' and a.bet_flag='0'";
    	return $this->master->query($sql, array($lid, $issue));
    }
    
    /**
     * 检查期次是否有订单可撤
     * @param unknown_type $lid
     * @param unknown_type $issue
     */
    public function checkIssueOrder($lid, $issue)
    {
    	$sql = "select count(1) from cp_chase_orders a inner join cp_chase_manage b on a.chaseId=b.chaseId where b.lid=? and a.issue=? and b.status='240' and a.status='0' and a.bet_flag='0'";
    	$count = $this->BcdDb->query($sql, array($lid, $issue))->getOne();
    	if($count > 0)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    /**
    * 参    数：$ids id
    * 作    者：shigx
    * 功    能：期次系统撤单
    * 修改日期：2016.01.08
    */
    public function cancelById($ids)
    {
    	$sql = "select count(1) from cp_chase_orders where id in('{$ids}') and (`status` !='0' OR bet_flag != '0')";
    	$count = $this->BcdDb->query($sql)->getOne();
    	if($count > 0)
    	{
    		return false;
    	}
    	else
    	{
    		$sql = "UPDATE cp_chase_orders a INNER JOIN cp_chase_manage b ON a.chaseId=b.chaseId SET a.`status`='602' WHERE a.id in('{$ids}') AND b.`status`='240' AND a.`status`='0'";
    		return $this->master->query($sql);
    	}
    }
    
    /**
     * 返回用户名
     * @param unknown_type $ids
     */
    public function getUserNameById($ids)
    {
    	$sql = "select b.userName from cp_chase_orders a inner join cp_chase_manage b on a.chaseId=b.chaseId where a.id in('{$ids}')";
    	return $this->BcdDb->query($sql)->getCol();
    }
}
