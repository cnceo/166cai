<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：系统日志管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.05.28
 */
class Model_retrybonus extends MY_Model
{
    private $CI;

	public function __construct()
	{
		parent::__construct();
		$this->order_status = $this->orderConfig('orders');
        $this->CI = & get_instance();
	}

    public function calculateAward($type, $pIssue)
    {
        $this->CI->load->library('issue');
        if (in_array($type, array('bjdc', 'sfgg', 'bqc', 'jqc')))
        {
            return true;
        }
        if (in_array($type, array('sfc', 'rj')))
        {
            $sIssue = $this->CI->issue->getSIssueByPIssue($type, $pIssue);
            $retryMethod = 'retry' . ucfirst($type);
            return $this->$retryMethod($pIssue, $sIssue);
        }
        $sIssue = $this->CI->issue->getSIssueByPIssue($type, $pIssue);
        $lid = $this->getLotteryId($type);
        
        return $this->retryNumber($type, $lid, $pIssue, $sIssue);
    }

    private function getLotteryId($type)
    {
        $lotteryToId = array_flip($this->config->item('cfg_lidmap'));
        if ($type == 'fc3d')
        {
            $lid = $lotteryToId['fcsd'];
        }
        elseif ($type == 'pl3')
        {
            $lid = $lotteryToId['pls'];
        }
        elseif ($type == 'pl5')
        {
            $lid = $lotteryToId['plw'];
        }
        else
        {
            $lid = $lotteryToId[$type];
        }

        return $lid;
    }

    public function calculateMatchAward($type, $mid)
    {
        if ( ! in_array($type, array('jczq', 'jclq')))
        {
            return true;
        }

        $lid = $this->getLotteryId($type);
        $success = $this->retryJJC($type, $lid, $mid);

        return $success;
    }
	
    /**
     * 参    数：$lname 彩种名，用于确定查询哪个彩种的排期表
     *       $lid 彩种id
     *       $pIssue 彩种排期表期号
     *       $sIssue 彩种拆单详情表期号
     * 作    者：shigx
     * 功    能：重置数字彩奖金
     * 修改日期：2015.05.28
     */
    public function retryNumber($lname, $lid, $pIssue, $sIssue)
    {
    	//先检查该期次是否是排期表已计奖状态，如果不是直接return false
        $sql = "SELECT * FROM cp_{$lname}_paiqi WHERE issue=? AND (status={$this->order_status['paiqi_jjsucc']} or rstatus = {$this->order_status['paiqi_jjsucc']})";
        $res = $this->cfgDB->query($sql, array($pIssue))->getRow();
        $this->dc = $this->load->database('dc', TRUE);
        $re = $this->dc->query("UPDATE cp_{$lname}_paiqi SET d_synflag=0 WHERE issue=?", array($pIssue));
        if(!$res || !$re)
        {
        	return false;
        }
        //启动同步号码任务
        $this->updateTicketStop(1, $lid, 0);
        
        $this->cfgDB->trans_start();
        $sql1 = "UPDATE cp_{$lname}_paiqi SET status=0, rstatus=0, synflag=0, cd_bonus = 0 WHERE issue=?";
        $res1 = $this->cfgDB->query($sql1, array($pIssue));
        $sql2 = "UPDATE cp_orders_split 
        SET status={$this->order_status['draw']},bonus=0,margin=0,cpstate=0, 
        bonus_t = 0, margin_t = 0, otherBonus=0
        WHERE modified > date_sub(now(), interval 7 day) and lid=? AND issue=? 
        AND status IN ('{$this->order_status['split_ggwin']}','{$this->order_status['notwin']}','{$this->order_status['win']}')";
        $res2 = $this->cfgDB->query($sql2, array($lid, $sIssue));
        $res3 = $this->cfgDB->query("update cp_check_distribution set unmatched=0 where lottery_id=? and issue=?", array($lid, $sIssue));
        //$res4 = $this->cfgDB->query("update cp_orders_inconsistent set distributed=0 where lid=? and issue=?", array($lid, $sIssue));
        $sql4 = "UPDATE cp_orders_ori SET status={$this->order_status['draw']},bonus=0,margin=0 WHERE modified > date_sub(now(), interval 7 day) and lid=? AND issue=? AND status IN ('{$this->order_status['notwin']}','{$this->order_status['win']}')";
        $res4 = $this->cfgDB->query($sql4, array($lid, $sIssue));
        if($res1 && $res2 && $res3 && $res4)
        {
        	$this->cfgDB->trans_complete();
        	return true;
        }
        else
        {
        	$this->cfgDB->trans_rollback();
        	return false;
        }
    }
    
    /**
     * 参    数：$pIssue 彩种排期表期号
     *       $sIssue 彩种拆单详情表期号
     * 作    者：shigx
     * 功    能：胜负彩重算奖金
     * 修改日期：2015.05.28
     */
    public function retrySfc($pIssue, $sIssue)
    {
    	$sql = "SELECT * FROM cp_rsfc_paiqi WHERE mid=? AND (status={$this->order_status['paiqi_jjsucc']} or rstatus={$this->order_status['paiqi_jjsucc']})";
    	$res = $this->cfgDB->query($sql, array($pIssue))->getRow();
    	$this->dc = $this->load->database('dc', TRUE);
    	$re = $this->dc->query("UPDATE cp_rsfc_paiqi SET d_synflag=0 WHERE mid=?", array($pIssue));
    	if(!$res || !$re)
    	{
    		return false;
    	}
    	
    	//启动同步号码任务
    	$this->updateTicketStop(1, 11, 0);
    	
    	$this->cfgDB->trans_start();
    	$sql1 = "UPDATE cp_rsfc_paiqi SET status=0, rstatus=0, cd_bonus=0, synflag=0 WHERE mid=?";
    	$res1 = $this->cfgDB->query($sql1, array($pIssue));
    	$sql2 = "UPDATE cp_orders_split 
    	SET status={$this->order_status['draw']},bonus=0,margin=0, cpstate=0, 
        bonus_t = 0, margin_t = 0
    	WHERE modified > date_sub(now(), interval 7 day) 
    	and lid IN('11') AND issue=? AND 
    	status IN ('{$this->order_status['split_ggwin']}','{$this->order_status['notwin']}','{$this->order_status['win']}')";
    	$res2 = $this->cfgDB->query($sql2, array($sIssue));
    	$res3 = $this->cfgDB->query("update cp_check_distribution set unmatched=0 where lottery_id=11 and issue=?", array($sIssue));
    	//$res4 = $this->cfgDB->query("update cp_orders_inconsistent set distributed=0 where lid=11 and issue=?", array($sIssue));
    	$sql4 = "UPDATE cp_orders_ori SET status={$this->order_status['draw']},bonus=0,margin=0 WHERE modified > date_sub(now(), interval 7 day) and lid IN('11') AND issue=? AND status IN ('{$this->order_status['notwin']}','{$this->order_status['win']}')";
    	$res4 = $this->cfgDB->query($sql4, array($sIssue));
    	if($res1 && $res2 && $res3 && $res4)
    	{
    		$this->cfgDB->trans_complete();
    		return true;
    	}
    	else
    	{
    		$this->cfgDB->trans_rollback();
    		return false;
    	}
    }
    
    /**
     * 参    数：$pIssue 彩种排期表期号
     *       $sIssue 彩种拆单详情表期号
     * 作    者：shigx
     * 功    能：胜负彩重算奖金
     * 修改日期：2015.05.28
     */
    public function retryRj($pIssue, $sIssue)
    {
    	$sql = "SELECT * FROM cp_rsfc_paiqi WHERE mid=? AND (rjstatus={$this->order_status['paiqi_jjsucc']} or rjrstatus={$this->order_status['paiqi_jjsucc']})";
    	$res = $this->cfgDB->query($sql, array($pIssue))->getRow();
    	$this->dc = $this->load->database('dc', TRUE);
    	$re = $this->dc->query("UPDATE cp_rsfc_paiqi SET d_synflag=0 WHERE mid=?", array($pIssue));
    	if(!$res || !$re)
    	{
    		return false;
    	}
    	//启动同步号码任务
    	$this->updateTicketStop(1, 11, 0);
    	
    	$this->cfgDB->trans_start();
    	$sql1 = "UPDATE cp_rsfc_paiqi SET rjstatus=0, rjrstatus=0, cd_rjbonus=0, synflag=0 WHERE mid=?";
    	$res1 = $this->cfgDB->query($sql1, array($pIssue));
    	$sql2 = "UPDATE cp_orders_split 
    	SET status={$this->order_status['draw']},bonus=0,margin=0,cpstate=0, 
        bonus_t = 0, margin_t = 0 
    	WHERE modified > date_sub(now(), interval 7 day) and lid IN('19') AND issue=? AND status IN ('{$this->order_status['split_ggwin']}','{$this->order_status['notwin']}','{$this->order_status['win']}')";
    	$res2 = $this->cfgDB->query($sql2, array($sIssue));
    	$res3 = $this->cfgDB->query("update cp_check_distribution set unmatched=0 where lottery_id=19 and issue=?", array($sIssue));
    	//$res4 = $this->cfgDB->query("update cp_orders_inconsistent set distributed=0 where lid=19 and issue=?", array($sIssue));
    	$sql4 = "UPDATE cp_orders_ori SET status={$this->order_status['draw']},bonus=0,margin=0 WHERE modified > date_sub(now(), interval 7 day) and lid IN('19') AND issue=? AND status IN ('{$this->order_status['notwin']}','{$this->order_status['win']}')";
    	$res4 = $this->cfgDB->query($sql4, array($sIssue));
    	if($res1 && $res2 && $res3 && $res4)
    	{
    		$this->cfgDB->trans_complete();
    		return true;
    	}
    	else
    	{
    		$this->cfgDB->trans_rollback();
    		return false;
    	}
    }
    
    /**
     * 参    数：$lname 彩种名，用于确定查询哪个彩种的排期表
     *       $lid 彩种id
     *       $mid 期号
     * 作    者：shigx
     * 功    能：胜负彩重算奖金
     * 修改日期：2015.05.28
     */
    public function retryJJC($lname, $lid, $mid)
    {
    	$sql = "SELECT * FROM cp_{$lname}_paiqi WHERE mid=? AND status={$this->order_status['paiqi_jjsucc']}";
    	$res = $this->cfgDB->query($sql, array($mid))->getRow();
    	$this->dc = $this->load->database('dc', TRUE);
    	$re = $this->dc->query("UPDATE cp_{$lname}_paiqi SET d_synflag=0 WHERE mid=?", array($mid));
    	if(!$res || !$re)
    	{
    		return false;
    	}
    	//启动同步号码任务
    	$this->updateTicketStop(1, $lid, 0);
    	
    	$this->cfgDB->trans_start();
    	$sql1 = "UPDATE cp_{$lname}_paiqi SET status=0 WHERE mid=?";
    	$res1 = $this->cfgDB->query($sql1, array($mid));
    	$sql2 = "UPDATE cp_orders_relation m join cp_orders_split n 
    	on m.sub_order_id = n.sub_order_id
    	SET m.status={$this->order_status['draw']}, n.status={$this->order_status['draw']},
    	n.bonus = 0, n.margin = 0, n.cpstate = 0, bonus_t = 0, margin_t = 0
    	WHERE m.lid=? AND m.mid=? and m.status != 600";
    	$res2 = $this->cfgDB->query($sql2, array($lid, $mid));
    	if($res1 && $res2)
    	{
    		$this->cfgDB->trans_complete();
    		return true;
    	}
    	else
    	{
    		$this->cfgDB->trans_rollback();
    		return false;
    	}
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param int $type
     * @param int $lid
     * @param int $stop
     */
    public function updateTicketStop($type, $lid, $stop)
    {
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
}
