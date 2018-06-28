<?php

class Model_pushconfig extends MY_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}
	
	// 查询指定彩种推送配置
	public function getPushConfig($lid, $ctype = 1)
	{
		$sql = "SELECT id, ctype, lid, week, title, content, send_time, status FROM cp_push_config WHERE lid = ? AND ctype = ? ORDER BY id ASC";
		$info = $this->BcdDb->query($sql, array($lid, $ctype))->getAll();
    	return $info;
	}

	// 删除
	public function delPushConfig($lid)
	{
		return $this->master->delete('cp_push_config', array('lid' => $lid));
	}

	// 更新配置
	public function updatePushConfig($fields, $bdata)
	{
		if(!empty($bdata['s_data']))
        {
            $sql = "insert cp_push_config(" . implode(', ', $fields) . ") values" . 
            implode(', ', $bdata['s_data']);
            $this->master->query($sql, $bdata['d_data']);
        }
	}
}