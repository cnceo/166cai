<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技股份有限公司
 * 摘    要：配置管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.04.09
 */
class Model_configure extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
    	$this->dbDc = $this->load->database('dc', true);
    }
    
    /**
     * 参    数：$ctype 字符串型 配置类型
     * 作    者：shigx
     * 功    能：返还指定类型配置
     * 修改日期：2015.04.09
     */
    public function getByCtype($ctype)
    {
        return $this->slaveDc->query("select * from cp_cron_score where ctype=?", array($ctype))->getAll();
    }
    
    /**
     * 参    数：$id 整形 主键id
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新配置记录
     * 修改日期：2015.04.09
     */
    public function update($id, $data = array()){
    	$this->dbDc->where('id', $id);
    	$this->dbDc->update('cp_cron_score', $data);
    	return $this->dbDc->affected_rows();
    }
    /**
     * [getStartNum 获取抓紧开始的数量 至少大于2家]
     * @author LiKangJian 2017-09-21
     * @param  [type] $ctype [description]
     * @return [type]        [description]
     */
    public function getStartNum($ctype)
    {
        $sql = "select count(*) as c from cp_cron_score where ctype= ? and start = ?";
        $res = $this->slaveDc->query($sql,array($ctype,1))->getRow();
        return $res['c'] ;
    }

    // 重置抓取信息
    public function removeConfigure($ctype)
    {
        return $this->dbDc->query("update cp_cron_score set start = 0, compare = 0 where ctype = ?", array($ctype));
    }

    // 查询拉取开奖号码脚本配置表
    public function getCrontabs()
    {
        return $this->slaveCfg1->query("select id, ctype, cname, delflag from cp_crontab_config where 1 and cname like 'kjResult%'")->getAll();
    }

    // 设置cp_crontab_config
    public function updateCrontab($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_crontab_config', $data);
        return $this->cfgDB->affected_rows();
    }
    
    public function getJlksIssue($ctype)
    {
    	return $this->dbDc->query("select * from cp_cron_score where ctype = ?", array($ctype))->getAll();
    }

    public function updateJlksIssue($param, $ctype)
    {
    	$sql = "select count(*) from cp_cron_score where ctype = ? and cname = ?";
        $res = $this->dbDc->query($sql, array($ctype, $param['cname']))->getRow();
        if (empty($res)) {
            return array('status' => 300, 'msg' => '不存在的票商');
        }
        if($param['start'] == 1){
            $this->dbDc->query("update cp_cron_score set start = 1 where id = ? and ctype = ?", array($param['updateId'], $ctype));
            $this->dbDc->query("update cp_cron_score set start = 0 where cname <> ? and ctype = ?", array($param['cname'], $ctype));
            return array('status' => 200, 'msg' => '更新成功');
        }else{
            $this->dbDc->query("update cp_cron_score set start = 0 where id = ? and ctype = ?", array($param['updateId'], $ctype));
            $this->dbDc->query("update cp_cron_score set start = 1 where cname <> ? and ctype = ?", array($param['cname'], $ctype));
            return array('status' => 200, 'msg' => '更新成功');
        }
    }       
}
