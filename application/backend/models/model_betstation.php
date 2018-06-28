<?php

/**
 * Copyright (c) 2016,上海二三四五网络科技股份有限公司
 * 摘    要：投注站管理模型
 * 作    者：liuz
 * 修改日期：2016.01.20
 */
class Model_betstation extends MY_Model
{
    public function __construct()
    {
        $this->get_db();
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：获取投注站列表
     * 修改日期：2016.01.20
     */
    public function getList($searchData, $page, $pageCount)
{
    $where = " where 1";
    if ($this->emp($searchData['partnerId']))
    {
        $where .= " and ({$this->cp_p_s}.partnerId = '{$searchData['partnerId']}')";
    }
    if ($this->emp($searchData['shopId']))
    {
        $where .= " and ({$this->cp_p_s}.shopNum  LIKE '%{$searchData['shopId']}%')";
    }
    
    if ($this->emp($searchData['lid']))
    {
    	$where .= " and ({$this->cp_p_s}.lid = '{$searchData['lid']}')";
    }

    $count = $this->BcdDb->query("SELECT COUNT(*) FROM {$this->cp_p_s} {$where}")->getOne();
    $select = "select * from {$this->cp_p_s} {$where} ORDER BY partner_name  LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
    $result = $this->BcdDb->query($select)->getAll();
    return array(
        $result,
        $count,
    );
}

    public function getOne($searchData , $type)
    {
        $where = " 1";
        if(!empty($searchData['id']))
        {
            $where .= ($type == 'cp_partner_shop'? ' and id = ? ' : ' and shopId = ?');
        }
        if($type == 'cp_partner_shop_file')
        {
            $where .= ' and delete_flag != 1';
        }
        $sql = "SELECT * FROM {$type} WHERE {$where}";
        $result = $this->BcdDb->query($sql, array($searchData['id']))->row_array();
        return $type == 'cp_partner_shop' ? $result[0] : $result;
    }

    public function getNum($searchData , $type)
    {
        $where = " 1";
        if(!empty($searchData['id']))
        {
            $where .= ($type == 'cp_partner_shop'? ' and id = ? ' : ' and shopId = ?');
        }
        if($type == 'cp_partner_shop_file')
        {
            $where .= ' and delete_flag != 1';
        }
        $sql = "SELECT count(*) num FROM {$type} WHERE {$where}";
        $result = $this->BcdDb->query($sql, array($searchData['id']))->row_array();
        return $type == 'cp_partner_shop' ? $result[0] : $result;
    }

    public function download($searchData , $type)
    {
        $where = " id = ?";
        $sql = "SELECT * FROM {$type} WHERE {$where}";
        $result = $this->BcdDb->query($sql, array($searchData['id']))->row_array();
        return  $result;
    }

    public function deleteFile($searchData , $type)
    {
        $where = ' id = ?';
        $sql = "update {$type} set delete_flag = 1 WHERE {$where}";
        $result = $this->master->query($sql, array($searchData['id']));
        return $result;
    }
    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：更新投注站信息
     * 修改日期：2016.01.20
     */
    public function updateData($searchData)
    {
        $result = $this->master->update ( $this->cp_p_s, $searchData, array('id' => $searchData['id']) );
        return $result;
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：获取合作商
     * 修改日期：2016.01.20
     */
    public function getPartner($type)
    {
        $sql = "SELECT  id, partnerId, shopNum  FROM {$type}";;
        $result = $this->BcdDb->query($sql)->row_array();
        return $result;
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：获取合作商Id
     * 修改日期：2016.01.20
     */
    public function getPartnerId($type)
    {
        $sql = "SELECT  id, name  FROM {$type}";;
        $result = $this->BcdDb->query($sql)->row_array();
        return $result;
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：上传文件
     * 修改日期：2016.01.20
     */
    public function upload($searchData, $files)
    {

        $sql = "insert into {$this->cp_file} (partnerId, shopId, filename, filepath, created) VALUES ";
         foreach ( $files as $file )
         {
              $sql .= "('{$searchData['partnerId']}', '{$searchData['id']}', '{$file['filename']}', '{$file['filepath']}', '" . date ( 'Y-m-d H:i:s' ) . "'),";
         }
        $sql = rtrim($sql, ',');
        $result = $this->master->query($sql);
        return $result;
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：更新投注站信息
     * 修改日期：2016.01.20
     */
    public function updateStatus($searchData)
    {
        $where = ' id = ?';
        $sql = "update {$this->cp_p_s} set status = ?  WHERE {$where}";
        $result = $this->master->query($sql, array($searchData['status'],$searchData['id']));
        return $result;
    }

    /**
     * 参    数：$uname 用户名
     *                $id 用户ID
     * 作    者：liuz
     * 功    能：更新投注站信息
     * 修改日期：2016.01.20
     */
    public function unPass($searchData)
    {
        $where = 'id = ?';
        if(empty($searchData['fail_reason']))
        {
            $sql = "update {$this->cp_p_s} set status = ?, off_reason = ?  WHERE {$where}";
            $result = $this->master->query($sql, array($searchData['status'],$searchData['off_reason'], $searchData['id']));
        }
        else
        {
            $sql = "update {$this->cp_p_s} set status = ?, fail_reason = ?  WHERE {$where}";
            $result = $this->master->query($sql, array($searchData['status'],$searchData['fail_reason'],$searchData['id']));
        }

        return $result;
    }

}
