<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: App version
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/23
 * 修改时间: 16:43
 */

class Model_App_Version extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
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
        $results = $this->BcdDb->query($sql)->getCol();

        return $results;
    }
}
