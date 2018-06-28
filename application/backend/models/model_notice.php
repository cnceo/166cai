<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：公告管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_notice extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    
    /**
     * 参    数： $page 页码
     *                 $pageCount 单页条数
     *                 $$searchData 搜索条件
     * 作    者：wangl
     * 功    能：获取公告列表
     * 修改日期：2014.11.11
     */
    public function list_notice($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        $where .= $this->condition("title", $searchData['title']);
        $where .= $this->condition("category", $searchData['category']);
        $where .= $this->condition("addTime", array(
            strtotime($searchData['start_time']),
            strtotime($searchData['end_time'])
        ), "time");
        $where .= $this->condition("username", $searchData['name']);
        $where .= $this->condition("status", array(
            $searchData['isshow'],
            $searchData['ishide']
        ), "choose", array(
            0,
            1
        ));
        if ($this->emp($searchData['source']))
        {
            if ($searchData['source'] == 1)
            {
                $where .= " and username !='抓取'";
            }
            else
            {
                $where .= " and username ='抓取'";
            }
        }
        $select = "SELECT * FROM {$this->cp_notice} {$where}
                        ORDER BY addTime DESC  
                        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $result = $this->BcdDb->query($select)->row_array();
        $count = $this->BcdDb->query("SELECT COUNT(*) as count FROM {$this->cp_notice} {$where}")->row();
        return array(
            $result,
            $count->count
        );
    }
    
    /**
     * 参    数：$id 公告ID
     * 作    者：wangl
     * 功    能：根据ID获取公告
     * 修改日期：2014.11.11
     */
    public function get_notice_by_id($id)
    {
        $select = "SELECT * FROM {$this->cp_notice} where id = {$id} limit 1";
        $result = $this->BcdDb->query($select)->row_array();
        return $result[0];
    }
    
    /**
     * 参    数：$addData 添加数据
     * 作    者：wangl
     * 功    能：添加公告
     * 修改日期：2014.11.11
     */
    public function add($addData)
    {
        foreach ($addData as $key => $value)
        {
            $keyStr .= "`{$key}`,";
            $valueStr .= "'{$value}',";
        }
        $keyStr = trim($keyStr, ",");
        $valueStr = trim($valueStr, ",");
        $select = "INSERT INTO {$this->cp_notice}({$keyStr}) VALUES({$valueStr})";
        $this->master->query($select);
        return $this->master->insert_id();
    }
    
    /**
     * 参    数：$updateData 更新数据
     *                 $id 广告ID
     * 作    者：wangl
     * 功    能：更新公告
     * 修改日期：2014.11.11
     */
    public function update($updateData, $id)
    {
        $this->master->where('id', $id);
        $this->master->update($this->cp_notice, $updateData);
        return $this->master->affected_rows();
    }
    
}
