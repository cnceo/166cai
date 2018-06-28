<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：报警服务类
 * 作    者：shigx@2345.com
 * 修改日期：2014.11.06
 */
class Model_warning extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    
    /**
     * 返回所有报警类型信息
     */
    public function getWarningConfig()
    {
        return $this->BcdDb->query("select ctype, name, phone, email, sendType, stop, otherCondition from cp_alert_config where 1")->getAll();
    }
    
    /**
     * 参    数：$id 整形 报警类型
     * 		 $data 数组  需要修改的值
     * 作    者：shigx
     * 功    能：更新报警配置
     * 修改日期：2016.03.14
     */
    public function update($id, $data = array())
    {
    	$this->master->where('ctype', $id);
    	$this->master->update('cp_alert_config', $data);
    	return $this->master->affected_rows();
    }
    /**
     * [getAlertName 获取报警类型]
     * @author LiKangJian 2017-07-24
     * @param  [type] $ctype [description]
     * @return [type]        [description]
     */
    public function getAlertName($ctype)
    {
        $sql = "select name from bn_cpiao.cp_alert_config where ctype = ?";
        $res = $this->BcdDb->query($sql,array($ctype))->getCol();
        return $res[0];
    }
}