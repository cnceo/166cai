<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技有限公司.
 * 摘    要: 用于运营管理的对阵管理
 * 作    者: 刁寿钧
 * 修改日期: 2015/5/27
 * 修改时间: 13:40
 */
class Model_Managematch extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function decideMatchTable($type)
    {
        $tableMap = array(
            'bjdc'   => 'cp_bjdc_paiqi',
            'bdsfgg' => 'cp_sfgg_paiqi',
            'tczq'   => 'cp_tczq_paiqi',
            'jczq'   => 'cp_jczq_paiqi',
            'jclq'   => 'cp_jclq_paiqi',
        );
        $table = array_key_exists($type, $tableMap) ? $tableMap[$type] : '';

        return $table;
    }

    /**
     * 参    数：type
     *           conditions
     *           limit
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function fetchMatchIds($type, $conditions, $limit)
    {
        //北单胜负过关取mid是从北京单场排期表中取的
        if ($type == 'bdsfgg')
        {
            $type = 'bjdc';
        }
        $table = $this->decideMatchTable($type);
        if (empty($table))
        {
            return array();
        }

        $this->slaveCfg1->select('mid');
        $this->slaveCfg1->from($table);
        if (! empty($conditions))
        {
            foreach ($conditions as $key => $value)
            {
                $this->slaveCfg1->where($key, $value);
            }
        }
        $this->slaveCfg1->group_by('mid');
        $this->slaveCfg1->order_by('mid', 'desc');
        $this->slaveCfg1->limit($limit);
        $result = $this->slaveCfg1->get()->result_array();
        $matchIds = array();
        if ( ! empty($result[0]))
        {
            foreach ($result[0] as $item)
            {
                array_push($matchIds, $item['mid']);
            }
        }

        return $matchIds;
    }

    /**
     * 参    数：ctype = 1
     *           limit = 10
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function get_tczq_mids($ctype = 1, $limit = 10)
    {
        $sql = "SELECT mid FROM cp_tczq_paiqi WHERE ctype=? GROUP BY mid ORDER BY mid DESC LIMIT ?";

        return $this->slaveCfg1->query($sql, array($ctype, $limit))->getCol();
    }

    /**
     * 参    数：searchData
     *           page
     *           pageCount
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function list_bjdc($searchData, $page, $pageCount)
    {
        $where = " WHERE 1";
        $where .= $this->condition("mid", $searchData['mid']);
        $sql1 = "SELECT * FROM cp_bjdc_paiqi {$where} ORDER BY mname ASC LIMIT ". ($page - 1) * $pageCount . "," . $pageCount;
        $list = $this->slaveCfg1->query($sql1)->getAll();

        $sql2 = "SELECT count(*) FROM cp_bjdc_paiqi {$where}";
        $count = $this->slaveCfg1->query($sql2)->getCol();

        return array($list,$count[0]);
    }

    //注意：xxOld方法性能有问题，发现改成拼接SQL语句就没问题，暂时绕过这个坑，根本原因等后面调查

    /**
     * 参    数：type
     *           mid
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
//    public function fetchMatchesByMidOld($type, $mid)
//    {
//        $table = $this->decideMatchTable($type);
//        if (empty($table))
//        {
//            return array();
//        }
//
//        $this->cfgDB->select('*');
//        $this->cfgDB->from($table);
//        $this->cfgDB->where('mid', $mid);
//        $this->cfgDB->order_by('mname');
//        $resultAry = $this->cfgDB->get()->result_array();
//        if (empty($resultAry[0]))
//        {
//            return array();
//        }
//        $matches = $resultAry[0];
//        foreach ($matches as & $match)
//        {
//            $match['showEndTime'] = $this->matchShowEndTime($type, $match);
//            $match['statusInfo'] = $this->matchStatusInfo($match);
//        }
//
//        return $matches;
//    }

    /**
     * 参    数：type
     *           conditions
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
//    public function fetchMatchesOld($type, $conditions)
//    {
//        $table = $this->decideMatchTable($type);
//        if (empty($table))
//        {
//            return array();
//        }
//
//        $this->cfgDB->select('*');
//        $this->cfgDB->from($table);
//        foreach ($conditions as $key => $value)
//        {
//            $this->cfgDB->where($key, $value);
//        }
//        //ensure using index?
//        $this->cfgDB->order_by('mid');
//        $this->cfgDB->order_by('mname');
//        $resultAry = $this->cfgDB->get()->result_array();
//        if (empty($resultAry[0]))
//        {
//            return array();
//        }
//        $matches = $resultAry[0];
//        foreach ($matches as & $match)
//        {
//            $match['showEndTime'] = $this->matchShowEndTime($type, $match);
//            $match['statusInfo'] = $this->matchStatusInfo($match);
//        }
//
//        return $matches;
//    }

    /**
     * 参    数：type
     *           mid
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function fetchMatchesByMid($type, $mid)
    {
        $table = $this->decideMatchTable($type);
        if (empty($table))
        {
            return array();
        }

        $sql = "SELECT * FROM $table WHERE mid = ? ORDER BY mname";
        $matches = $this->slaveCfg1->query($sql, $mid)->getAll();
        if (empty($matches))
        {
            return array();
        }

        foreach ($matches as & $match)
        {
            $match['showEndTime'] = $this->matchShowEndTime($type, $match);
            $match['statusInfo'] = $this->matchStatusInfo($match);
        }

        return $matches;
    }

    /**
     * 参    数：type
     *           conditions
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function fetchMatches($type, $conditions)
    {
        $table = $this->decideMatchTable($type);
        if (empty($table))
        {
            return array();
        }

        $sql = "SELECT * FROM $table";
        $conditionAry = array();
        $params = array();
        if (empty($conditions))
        {
            $conditionStr = '';
        }
        else
        {
            foreach ($conditions as $key => $value)
            {
                array_push($conditionAry, "$key = ?");
                array_push($params, $value);
            }
            $conditionStr = implode(' AND ', $conditionAry);
            $sql .= " WHERE " . $conditionStr;
        }

        $sql .= " ORDER BY mid, mname";
        if ($conditionStr)
        {
            $matches = $this->slaveCfg1->query($sql, $params)->getAll();
        }
        else
        {
            $matches = $this->slaveCfg1->query($sql)->getAll();
        }
        if (empty($matches))
        {
            return array();
        }

        foreach ($matches as & $match)
        {
            $match['showEndTime'] = $this->matchShowEndTime($type, $match);
            $match['statusInfo'] = $this->matchStatusInfo($match);
        }

        return $matches;
    }

    /**
     * 参    数：type
     *           match
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function matchShowEndTime($type, $match)
    {
        $endTimeField = $this->decideOriginalEndTimeField($type);

        return ($match['show_end_time'] > '0000-00-00 00:00:00')
            ? $match['show_end_time']
            : $match[$endTimeField];
    }

    /**
     * 参    数：type
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    private function decideOriginalEndTimeField($type)
    {
        $fieldMap = array(
            'bjdc'   => 'begin_time',
            'bdsfgg' => 'begin_time',
            'tczq'   => 'begin_date',
            'jczq'   => 'end_sale_time',
            'jclq'   => 'begin_time',
        );

        return $fieldMap[$type];
    }

    /**
     * 参    数：match
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function matchStatusInfo($match)
    {
        if ($match[showEndTime] > date('Y-m-d H:i:s'))
        {
            $statusInfo = '在售';
        }
        elseif ($match['status'] >= 60)
        {
            $statusInfo = '结期';
        }
        else
        {
            $statusInfo = '截止';
        }

        return $statusInfo;
    }

    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function list_jczq($searchData)
    {
        $where = " WHERE 1";
        $where .= $this->condition(" m_date", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $sql = "SELECT * FROM cp_jczq_paiqi {$where} ORDER BY mid,mname ASC";

        $matches = $this->slaveCfg1->query($sql)->getAll();
        foreach ($matches as & $match)
        {
            $match['showEndTime'] = $this->matchShowEndTime('jczq', $match);
            $match['statusInfo'] = $this->matchStatusInfo($match);
        }

        return $matches;
    }

    /**
     * 参    数：searchData
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function list_jclq($searchData)
    {
        $where = " WHERE 1";
        $where .= $this->condition(" m_date", array(
            $searchData['start_time'],
            $searchData['end_time']
        ), "time");
        $sql = "SELECT * FROM cp_jclq_paiqi {$where} ORDER BY mid ASC";

        $matches = $this->slaveCfg1->query($sql)->getAll();
        foreach ($matches as & $match)
        {
            $match['showEndTime'] = $this->matchShowEndTime('jclq', $match);
            $match['statusInfo'] = $this->matchStatusInfo($match);
        }

        return $matches;
    }

    /**
     * 参    数：id
     *           data = array()
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function bjdc_update($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_bjdc_paiqi', $data);

        return $this->cfgDB->affected_rows();
    }

    /**
     * 参    数：id
     *           data = array()
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function sfgg_update($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_sfgg_paiqi', $data);

        return $this->cfgDB->affected_rows();
    }

    /**
     * 参    数：id
     *           data = array()
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function tczq_update($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_tczq_paiqi', $data);

        return $this->cfgDB->affected_rows();
    }

    /**
     * 参    数：id
     *           data = array()
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function jczq_update($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_jczq_paiqi', $data);

        return $this->cfgDB->affected_rows();
    }

    /**
     * 参    数：id
     *           data = array()
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function jclq_update($id, $data = array())
    {
        $this->cfgDB->where('id', $id);
        $this->cfgDB->update('cp_jclq_paiqi', $data);

        return $this->cfgDB->affected_rows();
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function get_bjdc($id)
    {
        return $this->slaveCfg1->query("select * from cp_bjdc_paiqi where id=?", array($id))->getRow();
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function get_sfgg($id)
    {
        return $this->slaveCfg1->query("select * from cp_sfgg_paiqi where id=?", array($id))->getRow();
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function get_jczq($id)
    {
        return $this->slaveCfg1->query("select * from cp_jczq_paiqi where id=?", array($id))->getRow();
    }

    /**
     * 参    数：id
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function get_jclq($id)
    {
        return $this->slaveCfg1->query("select * from cp_jclq_paiqi where id=?", array($id))->getRow();
    }

    /**
     * 参    数：type
     *           mid
     *           mname
     *           status
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function modifySaleStatus($type, $mid, $mname, $status)
    {
        if ($type == 'bdsfgg')
        {
            $table = 'cp_sfgg_paiqi';
        }
        else
        {
            $table = "cp_{$type}_paiqi";
        }

        $sql = "UPDATE $table SET sale_status = ? WHERE mid = ? AND mname = ?";
        return $this->cfgDB->query($sql, array($status, $mid, $mname));
    }

    /**
     * 参    数：type
     *           id
     *           status
     * 作    者：刁寿钧
     * 功    能：
     * 修改日期：2015-06-25
     */
    public function modifySaleStatusById($type, $id, $status)
    {
        if ($type == 'bdsfgg')
        {
            $table = 'cp_sfgg_paiqi';
        }
        else
        {
            $table = "cp_{$type}_paiqi";
        }
        $sql = "UPDATE $table SET sale_status = ? WHERE id = ?";
        return $this->cfgDB->query($sql, array($status, $id));
    }

    /**
     * 参    数：type
     *           id
     *           status
     * 作    者：liuz
     * 功    能：
     * 修改日期：2015-11-05
     */
    public function modifyHotStatus($type, $searchData)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("id", intval($searchData['id']));
        $query = $this->cfgDB->update_string('cp_'.$type.'_paiqi', $searchData , $querywhere );
        $this->cfgDB->query( $query);
        return $this->cfgDB->affected_rows();
    }
    /**
     * 参    数：id
     * 作    者：liuz
     * 功    能：
     * 修改日期：2015-11-20
     */
    public function selcetData($type, $searchData)
    {
        $where = " 1";
        $querywhere = $where.$this->condition("id", intval($searchData['id']));
        $sql = "select * from cp_".$type."_paiqi where ".$querywhere;
        $res = $this->slaveCfg1->query($sql)->row_array();
        return $res;
    }
}