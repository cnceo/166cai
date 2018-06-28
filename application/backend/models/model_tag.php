<?php
class Model_tag extends MY_Model
{
    
    public function __construct() {
        parent::__construct();
        $this->get_db();
    }
    
    public function get_list($searchData, $start, $limit) {
        if ($searchData['name']) $where .= " and tn.tag_name like '%{$searchData['name']}%'";
        if ($searchData['start_time']) $where .= " and tn.created > '{$searchData['start_time']}'";
        if ($searchData['end_time']) $where .= " and tn.created < '{$searchData['end_time']}'";
        if ($searchData['scope']) $where .= " and scope & {$searchData['scope']}";
        $count = $this->data->query("select count(*) from cp_tag_name as tn where 1 and tn.delete_flag = 0$where")->getOne();
        $data = $this->data->query("select tn.id, tn.tag_name, tn.scope, tn.conditions, tn.created, tn.rundate, COUNT(u.id) as ucount
                                from cp_tag_name as tn
                                LEFT JOIN cp_tag_user as u ON tn.rundate = u.date and tn.id = u.tag_id
                                where 1 and tn.delete_flag = 0$where
                                group by tn.id
                                order by tn.created desc
                                limit $start, $limit")->getAll();
        return array('count' => $count, 'data' => $data);
    }
    
    public function get_cluster_list($searchData, $start, $limit) {
        if ($searchData['name']) $where .= " and cluster_name like '%{$searchData['name']}%'";
        if ($searchData['ctype']) $where .= " and ctype = '".($searchData['ctype'] - 1)."'";
        if ($searchData['start_time']) $where .= " and created > '{$searchData['start_time']}'";
        if ($searchData['end_time']) $where .= " and created < '{$searchData['end_time']}'";
        if ($searchData['scope']) $where .= " and scope & {$searchData['scope']}";
        $count = $this->data->query("select count(*) from cp_tag_cluster where 1 and delete_flag = 0$where")->getOne();
        $data = $this->data->query("select id, cluster_name, cluster_desc, ctype, tag_ids, update_logic, update_date, created
            from cp_tag_cluster
            where 1 and delete_flag = 0$where
            order by created desc
            limit $start, $limit")->getAll();
        return array('count' => $count, 'data' => $data);
    }
    
    public function get_data($id) {
        return $this->data->query('select tag_name, tag_desc, base_type, sub_type, `conditions` from cp_tag_name where id = ?', array($id))->getRow();
    }
    
    public function get_cluster($id) {
        $data = $this->data->query('select cluster_name, cluster_desc, ctype, tag_ids, conditions, update_logic, update_date, created from cp_tag_cluster where id = ?', array($id))->getRow();
        $where = array();
        foreach (json_decode($data['conditions'], true) as $condition) {
            array_push($where, "(tag_id = '".$condition['tag_id']."' and date = '".$condition['date']."')");
        }
        $data['ucount'] = 0;
        if (!empty($data['tag_ids'])) $data['ucount'] = $this->data->query("select COUNT(DISTINCT uid) from cp_tag_user where ".implode(' or ', $where))->getOne();
        return $data;
    }
    
    public function save_data($data) {
        $filedArr = $dataArr = array();
        foreach ($data as $key => $val) {
            array_push($filedArr, "`$key`");
            array_push($dataArr, '?');
        }
        $this->data->query("insert into cp_tag_name (".implode(',', $filedArr).", created) values (".implode(',', $dataArr).", NOW())", $data);
    }
    
    public function save_cluster($data) {
        $filedArr = $dataArr = $tail = $datamore = array();
        foreach ($data as $key => $val) {
            array_push($filedArr, "`$key`");
            array_push($dataArr, '?');
            if ($key != 'id') array_push($tail, "`$key`= ? ");
            array_push($datamore, $val);
        }
        if ($data['tag_ids']) {
            $scopeRes = $this->data->query("select scope from cp_tag_name where id in (".$data['tag_ids'].")")->getCol();
            $scope = PHP_INT_MAX;
            foreach ($scopeRes as $scp) {
                $scope &= $scp;
            }
            $conditionArr = $this->data->query("SELECT tag_id, MAX(date) as date FROM cp_tag_user WHERE tag_id in (".$data['tag_ids'].") GROUP BY tag_id")->getAll();
            array_push($filedArr, "`scope`", "`conditions`");
            array_push($dataArr, $scope, "'".json_encode($conditionArr)."'");
            array_push($tail, "`scope`= ? ", "`conditions`= ? ");
            array_push($datamore, $scope, json_encode($conditionArr));
        }
        $this->data->query("insert into cp_tag_cluster (".implode(',', $filedArr).", created) values (".implode(',', $dataArr).", NOW())
            on duplicate key update " . implode(', ', $tail), array_merge($data, $datamore));
    }
    
    public function del_data($id) {
        $this->data->query('update cp_tag_name set delete_flag = 1 where id = ?', array($id));
        return $this->data->affected_rows();
    }
    
    public function del_cluster($id) {
        $this->data->query('update cp_tag_cluster set delete_flag = 1 where id = ?', array($id));
        return $this->data->affected_rows();
    }
    
    public function getUidByTag($id, $start, $end) {
        $maxuDate = $this->data->query('select max(date) from cp_tag_user')->getOne();
        return $this->data->query("select distinct uid from cp_tag_user where tag_id in (?) and date = '$maxuDate' limit $start, $end", array($id))->getCol();
    }
    
    public function getTagInfo($id) {
        return $this->data->query('select tag_name, runtime from cp_tag_name where id = ?', array($id))->getRow();
    }
    
    public function getClusterInfo($id) {
        return $this->data->query('select cluster_name, tag_ids, `conditions`, update_date from cp_tag_cluster where id = ?', array($id))->getRow();
    }
    
    public function getTagsInfo($id) {
        $tagids = $this->data->query('SELECT tag_ids FROM cp_tag_cluster WHERE id = ?', array($id))->getOne();
        return $this->data->query("SELECT id, tag_name FROM cp_tag_name WHERE id in ($tagids)", array($id))->getAll();
    }
    
    public function caculate_uids($tagids) {
        $ucount0 = $this->data->query("select count(distinct uid) from cp_tag_user", array($tagids))->getOne();
        if ($ucount0 > 0) {
            $ucount1 = $this->data->query("select count(distinct uid) from cp_tag_user where tag_id in ?", array($tagids))->getOne();
            return array('count' => $ucount1, 'percentage' => number_format($ucount1/$ucount0, 2, '.', ''));
        }
        return array('count' => '0', 'percentage' => '0.00');        
    }
    
    public function get_top_tag_ids() {
        $tagids = $this->data->query('SELECT group_concat(tag_ids) FROM cp_tag_cluster WHERE created > DATE_SUB(NOW(),INTERVAL 30 DAY)')->getOne();
        $tagArr = array_diff(explode(',', $tagids), array(''));
        $res = array_count_values($tagArr);
        arsort($res);
        $ids = implode(',', array_keys($res));
        $data = $this->data->query("select id, tag_name from cp_tag_name where delete_flag = 0 and rundate > 0 and id in (".$ids.") limit 8")->getAll();
        $count = count($data);
        if ($count < 8) {
            $other = $this->data->query("select id, tag_name from cp_tag_name where delete_flag = 0 and rundate > 0 and id not in (".$ids.") order by created desc limit ".(8 - $count))->getAll();
            $data = array_merge($data, $other);
        }
        return $data;
    }
    
    public function get_tag_ids($scope, $start, $offset) {
        $total = $this->data->query("select count(*) from cp_tag_name where delete_flag = 0 and rundate > 0 and scope & $scope")->getOne();
        $data = $this->data->query("select id, tag_name from cp_tag_name where delete_flag = 0 and rundate > 0 and scope & $scope order by created desc limit $start, $offset")->getAll();
        return compact('total', 'data');
    }
}