<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
class Model_injury extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
    	$this->get_db();
    }
    
    public function getDataByTeam($team) {
    	$sql = "select * from cp_info_injury
    			where team='{$team}' ORDER BY id";
    	return $this->BcdDb->query($sql)->getAll();
    }
    
    public function delByTeam($team) {
    	return $this->master->delete('cp_info_injury', array('team' => $team));
    }
    
    public function insertAllData($datas)
    {
    	if (!empty($datas))
    	{
    		foreach ($datas as $data)
    		{
    			if (empty($field)) {
    				$field = '';
    				$fields = array_keys($data);
    				foreach ($fields as $value)
    				{
    					$field .= $value.", ";
    				}
    				$field = substr($field, 0, -2);
    				$sql = "insert into cp_info_injury ({$field}) values ";
    			}
    			$sql .= "(";
    			foreach ($fields as $value)
    			{
    				$sql .= "'{$data[$value]}', ";
    			}
    			$sql = substr($sql, 0, -2)."), ";
    		}
    		$sql = substr($sql, 0, -2);
    		return $this->master->query($sql);
    	}
    }
}