<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/21
 * 修改时间: 5:32
 */
class Model_Info_Crawl extends MY_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}

    public function getCategoryList()
    {
        $categoryList = $this->BcdDb->query("SELECT id, name 
            FROM cp_info_category")
            ->getAll();

        return $categoryList;
    }

    public function queryCategoryConfig($categoryId)
    {
        $configList = $this->BcdDb->query("SELECT c.id, c.category, c.url, s.name, c.source, c.is_open FROM cp_info_crawl as c inner join cp_info_source as s on c.source=s.id
            WHERE category_id = ?", array($categoryId))
            ->getAll();
        return $configList;
    }
    
    public function getSource() {
    	$sql = "select id, name from cp_info_source";
    	return $this->BcdDb->query($sql)->getAll();
    }
    
    public function update($id, $data = array()){
    	$this->master->where('id', $id);
    	$this->master->update('cp_info_crawl', $data);
    	return $this->master->affected_rows();
    }
}