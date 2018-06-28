<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 点击量相关统计模型
 * 作    者: 刁寿钧
 * 修改日期: 2015/7/20
 * 修改时间: 13:09
 */
class Model_Click extends MY_Model
{

    private $platformWeb = 1;
    private $platformApp = 2;

    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }

    private function fetchRecordsWeb($channelId = NULL, $period)
    {
        if (empty($channelId))
        {
            $sql = "SELECT date, browse_uv AS uv, browse_pv AS pv, click_uv AS clickUser, click_pv AS clickAmount
                FROM cp_50bang_web_all
                WHERE date >= SUBDATE(CURDATE(), INTERVAL ($period + 1) DAY) AND date < CURDATE()
                ORDER BY date DESC";
        }
        else
        {
            $sql = "SELECT date, SUM(browse_uv) uv, SUM(browse_pv) pv, SUM(click_uv) clickUser, SUM(click_pv) clickAmount
                FROM cp_50bang_web
                WHERE date >= SUBDATE(CURDATE(), INTERVAL ($period + 1) DAY) AND date < CURDATE()
                AND channel_id = $channelId
                GROUP BY date ORDER BY date DESC";
        }
        $records = $this->BcdDb->query($sql)->getAll();

        return $records;
    }

    private function fetchRecordsApp($channelId = NULL, $version = NULL, $period)
    {
        $sql = "SELECT date, SUM(new_num) newUser, SUM(history_active_num) activeUser,
            SUM(history_actionuv_num) clickUser, SUM(history_actionpv_num) clickAmount
            FROM cp_50bang_app
            WHERE date >= SUBDATE(CURDATE(), INTERVAL ($period + 1) DAY) AND date < CURDATE()";
        if ( ! empty($channelId))
        {
            $sql .= " AND channel_id = $channelId ";
        }
        if ( ! empty($version))
        {
            $sql .= " AND version = '$version'";
        }
        $sql .= " GROUP BY date ORDER BY date DESC";
        $records = $this->BcdDb->query($sql)->getAll();

        return $records;
    }

    public function fetchRecordsByPlatform($platform, $channelId = NULL, $version = NULL, $days)
    {
        if ($platform == $this->platformApp)
        {
            $records = $this->fetchRecordsApp($channelId, $version, $days);
        }
        elseif ($platform == $this->platformWeb)
        {
            $records = $this->fetchRecordsWeb($channelId, $days);
        }
        else
        {
            $records = array();
        }

        return $records;
    }

    public function getDateToUV($platform, $channelId, $version, $period)
    {
        $dateToUV = array();
        $clickRecords = $this->fetchRecordsByPlatform($platform, $channelId, $version, $period);
        foreach ($clickRecords as $record)
        {
            if ($platform == 1)
            {
                $dateToUV[$record['date']] = $record['uv'];
            }
            elseif ($platform == 2)
            {
                $dateToUV[$record['date']] = $record['activeUser'];
            }
        }

        return $dateToUV;
    }
    
    public function getCount($type) {
        return $this->BcdDb->select('sum(count) as count')->where_in('type', $type)->get('cp_clickcount_daily')->getCol();
    }
}