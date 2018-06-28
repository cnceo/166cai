<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要:
 * 作    者: 刁寿钧
 * 修改日期: 2016/3/21
 * 修改时间: 13:21
 */
class Model_Info extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }

    /**
     * 参    数： $page 页码
     *                 $pageCount 单页条数
     *                 $$searchData 搜索条件
     * 作    者：diaosj
     * 功    能：获取公告列表
     * 修改日期：2014.11.11
     */
    public function list_notice($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        $where .= $this->condition("title", $searchData['title'], 'like');
        $where .= $this->condition("category_id", $searchData['category']);
        $where .= $this->condition("source_id", $searchData['source']);
        $where .= $this->condition("created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $where .= $this->condition("submitter", $searchData['submitter']);
        $where .= $this->condition("is_show", $searchData['isshow']);
        $select = "SELECT * FROM cp_info {$where}
                        ORDER BY created DESC  
                        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->row_array();
        $count = $this->BcdDb->query("SELECT COUNT(*) as count FROM cp_info {$where}")->row();
        return array(
            $result,
            $count->count
        );
    }

    /**
     * 参    数：$id 公告ID
     * 作    者：diaosj
     * 功    能：根据ID获取公告
     * 修改日期：2014.11.11
     */
    public function get_notice_by_id($id)
    {
        $select = "SELECT * FROM cp_info where id = {$id} limit 1";
        $result = $this->BcdDb->query($select)->row_array();
        return $result[0];
    }

    /**
     * 参    数：$addData 添加数据
     * 作    者：diaosj
     * 功    能：添加公告
     * 修改日期：2014.11.11
     */
    public function add($addData)
    {
        $keyStr = '';
        $valueStr = '';
        foreach ($addData as $key => $value)
        {
            $keyStr .= "`{$key}`,";
            $valueStr .= "'{$value}',";
        }
        $keyStr .= "`created`";
        $valueStr .= "NOW()";
        $select = "INSERT INTO cp_info({$keyStr}) VALUES({$valueStr})";
        $this->master->query($select);
        return $this->master->insert_id();
    }

    /**
     * 参    数：$updateData 更新数据
     *                 $id 广告ID
     * 作    者：diaosj
     * 功    能：更新公告
     * 修改日期：2014.11.11
     */
    public function update($updateData, $id)
    {
        $this->master->where('id', $id);
        $this->master->update('cp_info', $updateData);
        return $this->master->affected_rows();
    }

    public function delete($id)
    {
        $this->master->query("DELETE FROM cp_info WHERE id = $id");
        return $this->master->affected_rows();
    }

    public function getCategoryList()
    {
        $categoryList = $this->BcdDb->query("SELECT id, `name`
            FROM cp_info_category")
            ->getAll();
        $categoryHash = array();
        if (!empty($categoryList)) {
            foreach ($categoryList as $category){
                $categoryHash[$category['id']] = $category['name'];
            }
        }

        return $categoryHash;
    }
    
    public function getSourceList()
    {
        $categoryList = $this->BcdDb->query("SELECT id, `name`
            FROM cp_info_source")
            ->getAll();

        $categoryHash = array();
        if (!empty($categoryList)) {
            foreach ($categoryList as $category){
                $categoryHash[$category['id']] = $category['name'];
            }
        }

        return $categoryHash;
    }
    
    /**
     * 		 $data,二维数组
     * 作    者：shigx
     * 功    能：将新闻数据更新到数据库
     * 修改日期：2016-04-25
     */
    public function saveData($data)
    {
    	if(empty($data))
    	{
    		return;
    	}
    	$insertVal = "";
    	$vData = array();
    	foreach($data as $value)
    	{
    		$flag = $insertVal == null ? "" : ",";
    		$fields = array_keys($value);
    		$v = array_values($value);
    		$vData[] = implode('|||', $v);
    		$insertVal .= $flag."(".implode(',', array_map(array($this, 'maps'), $fields)).")";
    	}
    	$strArr = array();
    	foreach ($fields as $field) {
    	    array_push($strArr, "`$field` = VALUES(`$field`)");
    	}
    
    	$sql = "INSERT INTO `cp_info` (".implode(',', $fields).")
    	VALUES ".$insertVal." on duplicate key update ".implode(',', $strArr);
    
    	$vData = implode('|||', $vData);
    	return $this->master->query($sql, explode('|||', $vData));
    }
    
    /**
     * 查询需要抓取的配置信息
     */
    public function getInfoCrawl($category)
    {
    	$sql = "select category_id, category, source, url from cp_info_crawl where is_open=1
    	 and category_id in ?";
    	return $this->BcdDb->query($sql, array($category))->getAll();
    }
    
    public function getIndexByCategory($category, $index = false) {
    	if ($index) {
    		$field = "id, title";
    		$limit = 8;
    		$sql0 = 'AND modified > DATE_SUB(NOW(),INTERVAL 10 DAY)';
    	}else {
    		$field = "id, title, created";
    		$limit = 5;
    		$sql0 = 'AND created > DATE_SUB(NOW(),INTERVAL 10 DAY)';
    	}
    	$sql = "select {$field} from cp_info
    			where category_id = ? and platform & 1 and is_show = 1 {$sql0}
    			order by ";
    	if ($index) {
    		$sql .= "is_top desc, if(weight=0, 1, 0) ,weight, ";
    	}
    	$sql .= "created desc limit {$limit}";
    	return $this->BcdDb->query($sql, array($category))->getAll();
    }
    
    public function getRecommed() {
    	$sql = "SELECT id, title, content, submitter, submitter_id, category_id FROM cp_info
    			WHERE category_id in (9, 10) and platform & 1 and is_show = 1 AND modified > DATE_SUB(NOW(),INTERVAL 10 DAY)
    			ORDER BY if(weight=0, 1, 0), weight, created DESC
    			LIMIT 2";
    	return $this->BcdDb->query($sql)->getAll();
    }
}