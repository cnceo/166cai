<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:遗漏数据服务类
 */
class Model_missed extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 删除期次
     * @param unknown_type $lid
     * @param unknown_type $issue
     */
    public function deleteIssue($lid, $issue)
    {
        $timeSql = "select created from cp_missed_counter where lid={$lid} and issue>={$issue} order by created limit 1";
        $issueTime = $this->cfgDB->query($timeSql)->getRow();
        if ((intval((time() - strtotime($issueTime['created'])) / 3600 / 24)) > 30)
        {
            return FALSE;
        }
        $sql = "delete from cp_missed_counter where lid={$lid} and issue>={$issue}";
    	$this->cfgDB->query($sql);
        return true;
    }
    
}