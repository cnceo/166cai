<?php

class Model_shouye_img extends MY_Model
{
		
    public function __construct()
    {
    	parent::__construct();
    	$this->get_db();
    }
    
    public function getDataByPosition($position)
    {
    	$sql = "select id, title, bgcolor, path, url, priority, created from {$this->cp_sy_img} where position = ? order by priority desc";
    	$res = $this->master->query($sql, array($position))->getAll();
    	foreach ($res as $value)
    	{
    		$data[$value['priority']] = array(
    			'id' => $value['id'],
    			'title' => $value['title'],
    			'path' => $value['path'],
    			'bgcolor' => $value['bgcolor'],
    			'url' => $value['url'],
    			'created' => $value['created']
    		);
    	}
    	return $data;
    }
    
    public function getListByPosition($position)
    {
    	$sql = "select id, title, bgcolor, path, url, priority, created from {$this->cp_sy_img} where position = ? order by priority desc";
    	$res = $this->BcdDb->query($sql, array($position))->getAll();
    	return $res;
    }
    
    public function getBannerList()
    {
        $sql = "select id, title, bgcolor, path, url, priority, start_time, end_time, start_time > NOW() isorder, created
        from {$this->cp_sy_img}
        where position = 'banner' and end_time > NOW()
        order by priority desc, start_time";
        $res = $this->BcdDb->query($sql)->getAll();
        return $res;
    }
    
    public function delByPosition($position)
    {
    	$sql = "delete from {$this->cp_sy_img} where position = ?";
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
    		$sql = "insert into {$this->cp_sy_img} ({$field}) values ";
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
    	$this->master->update ( $this->cp_sy_img, $data, array('id' => $id) );
    }
    
    public function getAlldata()
    {
    	$sql = "select * from {$this->cp_sy_img} order by position";
    	return $this->BcdDb->query($sql)->getAll();
    }
    
}
