<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：系统日志管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_syslog extends MY_Model
{
    public function __construct()
    {
    	parent::__construct();
        $this->get_db();
    }
    
    /**
     * 参    数：$searchData 搜索条件
     *                 $page 页码
     *                 $pageCount 单页条数
     * 作    者：wangl
     * 功    能：获取日志列表
     * 修改日期：2014.11.11
     */
    public function list_logs($searchData, $page, $pageCount)
    {
        $where = " where 1";
        $where .= $this->condition("{$this->logs}.userName", $searchData['name']);
        $where .= $this->condition(" {$this->logs}.addTime", array(
            strtotime($searchData['start_time']),
            strtotime($searchData['end_time'])
        ), "time");
        $where .= $this->condition("{$this->logs}.mark", $searchData['mark'], 'like');
        $where .= $this->condition("{$this->logs}.lmod", $searchData['lmod']);
        $select = "SELECT * FROM {$this->logs}
                        {$where}
                        ORDER BY addTime DESC 
                        LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
                        $count = $this->BcdDb->query("SELECT count(*) as count FROM {$this->logs} {$where}")->row();
                        $result = $this->BcdDb->query($select)->row_array();
        unset($where);
        unset($select);
        unset($searchData);
        return array(
            $result,
            $count->count
        );
    }
    
    /**
     * 参    数：$mod 模块
     *                 $mark 内容
     *                 $username 操作员
     * 作    者：wangl
     * 功    能：获取订单列表
     * 修改日期：2014.11.11
     */
   public function add_syslog($mod, $mark, $username)
   {
       $addData = array(
           "lmod" => intval($mod),
           "mark" => $mark,
           "userName" => $username,
           "addTime" => time()
       );

       $this->master->insert($this->logs, $addData);
       return $this->master->insert_id();
   }

//     public function add_syslog($mod, $mark)
//     {
//         $addData = array(
//             "lmod" => intval($mod),
//             "mark" => $mark,
// //            "userName" => $username,
//             "addTime" => time()
//         );

//         $this->master->insert($this->logs, $addData);
//         return $this->master->insert_id();
//     }
    
}
