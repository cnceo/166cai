<?php

class Model_Auto_Push_Config extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function getPushList()
    {
        $time = date("Y-m-d",strtotime("-1 days"));
        $sql = "select c.id,c.topic,c.config,c.ptype,c.rid,c.rname,c.title,c.content,c.status,c.action,c.url,l.totalNum from cp_auto_push_config c left join (SELECT ctype,totalNum from cp_auto_push_list WHERE pdate=? and cid=0) l on c.id=l.ctype";
        $info = $this->data->query($sql, array($time))->getAll();
        return $info;
    }

    public function getRedPacks()
    {
        $sql = "select id,use_desc,c_name,use_params from cp_redpack where aid=7 order by created DESC";
        $info = $this->BcdDb->query($sql)->getAll();
        return $info;
    }
    
    public function updateConfig($params)
    {
        $id = $params['id'];
        unset($params['id']);
        $field = array();
        foreach ($params as $key => $param) {
            $field[] = $key . "='" . $param . "'";
        }
        $sql = "update cp_auto_push_config set " . (implode(',', $field)) . " where id=".$id;
        $this->data->query($sql);
    }
    
    public function getEffectList($search, $page, $pageCount)
    {
        $where = " where cid=0";
        if ($search['topic'] > 0) {
            $where .= ' and ctype=' . $search['topic'];
        }
        if ($search['type'] !== FALSE && $search['type'] >= 0) {
            $where .= ' and ptype=' . $search['type'];
        }
        if ($search['redpack'] != FALSE && $search['redpack'] > 0) {
            if ($search['redpack'] == 1) {
                $where .= ' and rid>0';
            } else {
                $where .= ' and rid=0';
            }
        }
        if ($search['start_time']) {
            $where .= ' and pdate>="' . $search['start_time'] . '"';
        }
        if ($search['end_time']) {
            $where .= ' and pdate<="' . $search['end_time'] . '"';
        }
        $sql = "select id,pdate,ptype,topic,config,rid,rname,totalNum,regNum,authNum,recNum,content,action,url from cp_auto_push_list" . $where . " ORDER BY created DESC LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
        $countsql = "select count(*) as num from cp_auto_push_list" . $where;
        $sumsql = "select sum(totalNum) as totalNum,sum(regNum) as regNum,sum(authNum) as authNum,sum(recNum) as recNum from cp_auto_push_list" . $where;
        $lists = $this->data->query($sql)->getAll();
        $count = $this->data->query($countsql)->getRow();
        $sum = $this->data->query($sumsql)->getRow();
        return array($lists, $count, $sum);
    }
    
    public function getEffectDetail($id)
    {
        $sql = "select pdate,ctype,ptype from cp_auto_push_list where id=?";
        $list = $this->data->query($sql, $id)->getRow();
        $sql = "select topic,pdate,config,totalNum,regNum,authNum,recNum from cp_auto_push_list where cid!=0 and pdate=? and ctype=? and ptype=?  order by created DESC";
        return $this->data->query($sql, array($list['pdate'], $list['ctype'], $list['ptype']))->getAll();
    }
    
    public function getPushListById($id)
    {
        $sql = "select pdate,ctype,topic,config from cp_auto_push_list where id=?";
        return $this->data->query($sql, $id)->getRow();
    }
    
    public function getPushDetails($list)
    {
        $sql = "select uid,phone from cp_auto_push_detail where ctype=? and pdate=? group by uid,phone";
        return $this->data->query($sql, array($list['ctype'], $list['pdate']))->getAll();
    }
}    