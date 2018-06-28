<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：权限管理模型
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
class Model_capacity extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }
    
    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：wangl
     * 功    能：获取用户权限
     * 修改日期：2014.11.05
     */
    public function get_capacity($uname, $id = 0, $status = false)
    {
        $where = $id > 0 ? 'id = ?' : 'name = ?';
        if ($status)
            $where .= " and status = 1";
        $sql = "SELECT * FROM {$this->user_capacity} WHERE {$where}  limit 1";
        $result = $this->BcdDb->query($sql, array(
            $id > 0 ? $id : $uname
        ))->row_array();
        return $result[0];
    }
    
    /**
     * 参    数：$uname 用户名
     * 作    者：wangl
     * 功    能：获取用户权限
     * 修改日期：2014.11.05
     */
    public function list_capacity()
    {
        $sql = "SELECT * FROM {$this->user_capacity}";
        $result = $this->BcdDb->query($sql)->row_array();
        return $result;
    }
    
    /**
     * 参    数：$id  用户ID
     *               $name 用户姓名
     *                $updateData 更新数据
     * 作    者：wangl
     * 功    能：更新数据
     * 修改日期：2014.11.05
     */
    public function update_capacity($id, $updateData, $name = '')
    {
        if ($id > 0)
        {
            $this->master->where('id', $id);
        }
        else
        {
            $this->master->where('name', $name);
        }
        return $this->master->update($this->user_capacity, $updateData);
        //return $this->master->affected_rows();
    }
    
    /**
     * 参    数：$addData 添加数据
     * 作    者：wangl
     * 功    能：添加帐号
     * 修改日期：2014.11.10
     */
    public function add_account($addData)
    {
        $this->master->insert($this->user_capacity, $addData);
        return $this->master->insert_id();
    }
    
    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：wangl
     * 功    能：获取用户权限
     * 修改日期：2014.11.05
     */
    public function checkUser($name, $pass)
    {
    	$sql = "select id, name from ".$this->user_capacity." where name = ? and pass = ? and status = '1'";
    	$res = $this->BcdDb->query ( $sql, array (
    			$name,
    			md5($pass)
    	) )
    	->getRow ();
    	return $res;
    }
    
    public function getUserByName($name) {
    	$sql = "select id from ".$this->user_capacity." where name = ? and status= '1'";
    	return $this->BcdDb->query($sql, array($name))->getCol();
    }
    
}
