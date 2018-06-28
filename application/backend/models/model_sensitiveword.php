<?php

class Model_sensitiveword extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->get_db();
    }
    
    public function getAllWord($word, $pageNum, $num)
    {
        $where = "where status=0 ";
        if ($word) {
            $where.="and word like '%{$word}%' ";
        }
        $sql = "select id,word from cp_sensitive_words " . $where . "LIMIT " . ($pageNum - 1) * $num . "," . $num;
        $words = $this->BcdDb->query($sql)->getAll();
        $sql = "select count(id) as count from cp_sensitive_words " . $where;
        $count = $this->BcdDb->query($sql)->getRow();
        return array('words' => $words, 'count' => $count['count']);
    }
    
    public function delWord($ids)
    {
        if (!empty($ids['id'])) {
            $id = implode(',', $ids['id']);
            $sql = "update cp_sensitive_words set status=1 where id in({$id})";
            $this->master->query($sql);
        }
    }
    
    public function insertWord($txt)
    {
        $sql = "delete from cp_sensitive_words where 1";
        $this->master->query($sql);
        $words = explode(',', $txt);
        $insert = array();
        foreach ($words as $word)
        {
            $insert[] = array('word' => trim($word), 'created' => date('Y-m-d H:i:s', time()));
        }
        if(!empty($insert))
        {
            $this->db->insert_batch('cp_sensitive_words', $insert);
        }
    }
}
