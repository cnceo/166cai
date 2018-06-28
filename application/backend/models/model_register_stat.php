<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 注册相关统计模型
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/20
 * 修改时间: 13:09
 */
class Model_Register_Stat extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }

    public function fetchRecordsByPlatform($platform, $channelId = NULL, $version = NULL, $period)
    {
        $sql = "SELECT cdate date, SUM(register_num) registerUser, SUM(valid_user) validUser,
            SUM(complete_user) completeUser
            FROM cp_register_stat
            WHERE cdate >= SUBDATE(CURDATE(), INTERVAL ($period + 1) DAY) AND cdate < CURDATE() AND platform = $platform ";
        if ( ! empty($channelId))
        {
            $sql .= " AND channel_id = $channelId ";
        }
        if ( ! empty($version))
        {
            $sql .= " AND version = '$version' ";
        }
        $sql .= " GROUP BY date ORDER BY date DESC";
        $records = $this->BcdDb->query($sql)->getAll();

        return $records;
    }
}