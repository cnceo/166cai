<?php
/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：运营管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_operation extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    /**
     * 参    数：$searchData 搜索条件
     *          $page 页码
     *          $pageCount 单页条数
     * 作    者：wangl
     * 功    能：获取订单列表
     * 修改日期：2014.11.11
     */
    public function list_operation($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        $where .= $this->condition("{$this->cp_op_user}.name", $searchData['name']);
        $where .= $this->condition("{$this->cp_op_user}.created", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $where .= $this->condition("{$this->cp_op_server}.created", array(
            $searchData['reply_s_time'],
            $searchData['reply_e_time']
        ), "time");
        $where .= $this->condition("{$this->cp_op_user}.type", $searchData['type']);
        $where .= $this->condition("{$this->cp_op_server}.name", $searchData['reply_name']);
        $where .= $this->condition("{$this->cp_op_user}.content", $searchData['content'], 'like');
        if ($this->emp($searchData['platform']) && $searchData['platform'] != -1)
        {
            $where .= " and {$this->cp_op_user}.platform = ".$searchData['platform'];
        }
        $left = " LEFT JOIN (SELECT s.* FROM {$this->cp_op_server} s JOIN (SELECT {$this->cp_op_server}.reply_id, max({$this->cp_op_server}.id) id FROM {$this->cp_op_server} WHERE delect_flag = 0 GROUP BY {$this->cp_op_server}.reply_id) t ON s.id = t.id) r
        ON {$this->cp_op_user}.id = r.reply_id";
        $select = "SELECT {$this->cp_op_user}.*, r.created as rcreated,r.name as rname FROM {$this->cp_op_user}
                    {$left}
                    {$where}
                    ORDER BY {$this->cp_op_user}.created DESC
                    LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
                    $count = $this->BcdDb->query("SELECT count(*) as count FROM {$this->cp_op_user} {$left} {$where} ")->row();
                    $result = $this->BcdDb->query($select)->row_array();
        return array(
            $result,
            $count->count
        );
    }
    /**
     * 参    数：id ID
     * 作    者：wangl
     * 功    能：获取反馈信息
     * 修改日期：2014.12.02
     */
    public function get_operation_by_id($id)
    {
        $select = "SELECT * FROM {$this->cp_op_user} WHERE id = {$id} LIMIT 1";
        $result = $this->BcdDb->query($select)->row_array();
        return $result[0];
    }
    /**
     * 参    数：id ID
     * 作    者：wangl
     * 功    能：获取回复信息
     * 修改日期：2014.12.02
     */
    public function get_reply_by_id($id)
    {
        $select = "SELECT id,content,name,created FROM {$this->cp_op_server} WHERE reply_id = {$id} AND delect_flag = 0 ORDER BY id asc";
        $result = $this->BcdDb->query($select)->row_array();
        return $result;
    }
    /**
     * 参    数：$id ID
     *          $content 回复内容
     *          $reply_name 回复人
     * 作    者：wangl
     * 功    能：回复
     * 修改日期：2014.12.02
     */
    public function reply($id, $content, $reply_name)
    {
        $time = date("Y-m-d H:i:s");
        $this->master->trans_start();
        $this->master->where("id", $id);
        $this->master->update($this->cp_op_user, array(
            "if_reply" => 1
        ));
        $this->master->insert($this->cp_op_server, array(
            "name" => $reply_name,
            "reply_id" => $id,
            "content" => $content,
            "created" => $time
        ));
        $this->master->trans_complete();
        if ($this->master->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }
    /**
     * 参    数：$id ID
     * 作    者：wangl
     * 功    能：删除回复
     * 修改日期：2014.12.02
     */
    public function delete($id)
    {
        $this->master->where("id", $id);
        $this->master->update($this->cp_op_server, array(
            "delect_flag" => 1
        ));
        return $this->master->affected_rows();
        
    }
    /**
     * 参    数：$id ID
     *          $content 内容
     * 作    者：wangl
     * 功    能：修改回复
     * 修改日期：2014.11.05
     */
    public function edit($id, $content)
    {
        $this->master->where("id", $id);
        $this->master->update($this->cp_op_user, array(
            "content" => $content
        ));
        return $this->master->affected_rows();
    }
}