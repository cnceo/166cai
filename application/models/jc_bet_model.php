<?php

/*
 * 竞彩（竞足/竞篮/胜负彩）投注比例占比 模型层
 * @date:2017-05-25
 */

class Jc_Bet_Model extends MY_Model 
{

    public function __construct() 
    {
        parent::__construct();
    }

    // 配置信息
    private $lidMap = array(
        '42'    =>  array(
            'matchCache'    =>  'JCZQ_MATCH',
            'countCache'    =>  'JCZQ_BET_COUNT',
            'playType'      =>  array('SPF', 'RQSPF'),
            'table'         =>  'cp_jczq_bet',
            'cal'           =>  array('s' => 0, 'p' => 0, 'f' => 0)
        ),
        '43'    =>  array(
            'matchCache'    =>  'JCLQ_MATCH',
            'countCache'    =>  'JCLQ_BET_COUNT',
            'playType'      =>  array('SF', 'RFSF'),
            'table'         =>  'cp_jclq_bet',
            'cal'           =>  array('s' => 0, 'f' => 0)
        ),
        '11'    =>  array(
            'matchCache'    =>  'SFC_ISSUE',
            'countCache'    =>  'SFC_BET_COUNT',
            'playType'      =>  array(),
            'table'         =>  'cp_sfc_bet',
            'cal'           =>  array('s' => 0, 'f' => 0)
        ),
    );

    // 获取配置信息
    public function getLidMap()
    {
        return $this->lidMap;
    }

    // 竞彩足球 - 查询统计详情
    public function getJczqBet($mid)
    {
        $sql = "SELECT id, ptype, mid, s, p, f, last_id FROM cp_jczq_bet WHERE mid = ?";
        return $this->slaveCfg->query($sql, array($mid))->getAll();
    }

    // 竞彩篮球 - 查询统计详情
    public function getJclqBet($mid)
    {
        $sql = "SELECT id, ptype, mid, s, f, last_id FROM cp_jclq_bet WHERE mid = ?";
        return $this->slaveCfg->query($sql, array($mid))->getAll();
    }

    public function getSfcBet($mid)
    {
        $sql = "SELECT id, mid, mname, s, p, f, last_id FROM cp_sfc_bet WHERE mid = ?";
        return $this->slaveCfg->query($sql, array($mid))->getAll();
    }

    // 统计待查询cp_orders数据总数
    public function getRelationCount($lid, $mid, $last_id)
    {
        $con = '';
        if($last_id)
        {
            $con = " AND n.orderId > '{$last_id}'";
        }

        $sql = "SELECT COUNT(DISTINCT n.orderId, m.ptype) 
        FROM bn_cpiao_cfg.cp_orders_relation m
        JOIN bn_cpiao_cfg.cp_orders_split n ON m.sub_order_id = n.sub_order_id 
        WHERE m.mid = ? and m.lid = ? AND m.ptype IN ('" . implode("','", $this->lidMap[$lid]['playType']) . "') AND m.status > 0 AND m.status != 600{$con}
        ORDER BY n.orderId ";
        return $this->slaveCfg->query($sql, array($mid, $lid))->getOne();
    }

    // 查询累计查询relation数据
    public function getMatchRelation($lid, $mid, $last_id = 0, $page = 1, $limit = 2000)
    {
        $con = '';
        if($last_id)
        {
            $con = " AND n.orderId > '{$last_id}'";
        }

        $sql = "SELECT m.id, m.ptype, m.mid, m.pscores, n.orderId, 
        sum(if(m.pscores REGEXP '3(\\\{.{0,}\\\})?\\\(', 1, 0)) as s,
        sum(if(m.pscores REGEXP '1(\\\{.{0,}\\\})?\\\(', 1, 0)) as p,
        sum(if(m.pscores REGEXP '0(\\\{.{0,}\\\})?\\\(', 1, 0)) as f
        FROM bn_cpiao_cfg.cp_orders_relation m
        JOIN bn_cpiao_cfg.cp_orders_split n ON m.sub_order_id = n.sub_order_id 
        WHERE m.mid = '{$mid}' and m.lid = '{$lid}' AND m.ptype IN ('" . implode("','", $this->lidMap[$lid]['playType']) . "') AND m.status > 0 AND m.status != 600{$con}
        GROUP BY n.orderId, m.ptype ORDER BY n.orderId ASC LIMIT " . ($page - 1) * $limit . ", $limit";
        return $this->slaveCfg->query($sql)->getAll();
    }

    public function getSfcOrderCount($mid, $sale_time, $last_id)
    {
        $con = '';
        if($last_id)
        {
            $con = "orderId > '{$last_id}' AND ";
        }
        $sql = "SELECT COUNT(*) FROM cp_orders WHERE {$con}created >= ? AND lid IN(11, 19) AND status in(500,510,1000,2000) AND issue = ? ";
        return $this->slave->query($sql, array($sale_time, $mid))->getOne();
    }

    public function getSfcBetOrder($mid, $sale_time, $last_id = 0, $page = 1, $limit = 2000)
    {
        $con = '';
        if($last_id)
        {
            $con = "orderId > '{$last_id}' AND ";
        }

        $sql = "SELECT orderId, lid, codes, issue FROM cp_orders WHERE {$con} created >= ? AND lid IN(11, 19) AND status in(500,510,1000,2000) AND issue = ? ORDER BY orderId ASC LIMIT " . ($page - 1) * $limit . ", $limit";
        return $this->slave->query($sql, array($sale_time, $mid))->getAll();
    }

    public function insertMatchDetail($lid, $info)
    {
        $table = $this->lidMap[$lid]['table'];
        if(in_array($lid, array(43)))
        {
            $upd = array('s', 'f', 'spv', 'fpv', 'last_id');
        }
        else
        {
            $upd = array('s', 'p', 'f', 'spv', 'ppv', 'fpv', 'last_id');
        }
        
        $fields = array_keys($info);
        $sql = "insert {$table}(" . implode(',', $fields) . ", created)values(" . 
        implode(',', array_map(array($this, 'maps'), $fields)) .  ", now())" . $this->onduplicate($fields, $upd);
        return $this->cfgDB->query($sql, $info);
    }
}
