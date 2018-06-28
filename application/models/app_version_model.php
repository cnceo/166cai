<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: App version
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/23
 * 修改时间: 16:43
 */

class App_Version_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：
     * 作    者：刁寿钧
     * 功    能：构造App version下拉列表
     * 修改日期：2015-07-23
     */
    public function getVersionList()
    {
        //我也想存取都用versionId啊……
        $sql = "SELECT version FROM cp_app_version";
        $results = $this->db->query($sql)->getCol();

        return $results;
    }

    /**
     * 参    数：
     * 作    者：刘祯
     * 功    能：根据移动武林榜中所有版本补足app_version表
     * 创建日期：2015-08-11
     */
    public function fillVersions()
    {
        $sqlAll = "SELECT DISTINCT version FROM cp_50bang_app";
        $allVersions  = $this->db->query($sqlAll)->getCol();
        $sql = "SELECT version FROM cp_app_version";
        $appVersions = $this->db->query($sql)->getCol();
        $addVersions = array_diff($allVersions, $appVersions);
        if (empty($addVersions))
        {
            return;
        }

        $insertSql = "";
        $values = array();
        foreach($addVersions as $version)
        {
            array_push($values, "('$version', NOW())");
        }
        $valueStr = implode(',', $values);
        $insertSql .= "INSERT cp_app_version (version, created) VALUES $valueStr";
        $this->db->query($insertSql);
    }
}
