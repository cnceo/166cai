<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：历史开奖
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_winning extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    /**
     * 参    数：$searchData 搜索信息
     * 作    者：wangl
     * 功    能：获取开奖详细信息
     * 修改日期：2014.11.11
     */
    public function get_winning($searchData)
    {
        $result = $this->BcdDb->get_where($this->cp_winning, $searchData)->row_array();
        return $result[0];
    }
    
}