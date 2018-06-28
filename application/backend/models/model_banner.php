<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：shigx@2345.com
 * 修改日期：2015.03.25
 */
class Model_banner extends MY_Model
{
public function __construct()
    {
    	parent::__construct();
    	$this->get_db();
    }
    
    public function getDataByPosition($position)
    {
    	$sql = "select id, title, path, url, priority, location from {$this->cp_bn} where position = ? order by priority";
    	$res = $this->master->query($sql, array($position))->getAll();
    	foreach ($res as $value)
    	{
    		$location = explode('|', $value['location']);
    		foreach (explode('|', $value['location']) as $val) {
    			$data[$val] = array(
	    			'title' => $value['title'],
	    			'path' => $value['path'],
	    			'url' => $value['url'],
	    		);
    		}
    	}
    	return $data;
    }
    
    public function getListByPosition($position)
    {
    	$sql = "select id, title, path, url, priority, location from {$this->cp_bn} where position = ? order by priority";
    	$res = $this->BcdDb->query($sql, array($position))->getAll();
    	return $res;
    }
    
    public function delByPosition($position)
    {
    	$sql = "delete from {$this->cp_bn} where position = ?";
    	return $this->master->query($sql, array($position));
    }
    
    public function insertAllData($datas)
    {
    	if (!empty($datas))
    	{
    		$field = '';
    		$fields = array_keys($datas[0]);
    		foreach ($fields as $value)
    		{
    			$field .= $value.", ";
    		}
    		$field = substr($field, 0, -2);
    		$sql = "insert into {$this->cp_bn} ({$field}) values ";
    		foreach ($datas as $data)
    		{
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
    
    public function updateData($data, $id)
    {
    	$this->master->update ( $this->cp_bn, $data, array('id' => $id) );
    }
    
    public function getAlldata()
    {
    	$sql = "select * from {$this->cp_bn} order by position";
    	return $this->BcdDb->query($sql)->getAll();
    }
    
}
