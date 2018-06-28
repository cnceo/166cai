<?php
class Model_Countclick extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getClicks($order=null)
    {
        if($order)
        {
            $sql = "SELECT clickTimes 
            FROM cp_clickcount 
            WHERE aid = {$order}";
            $times = $this->db->query($sql)->getRow();
            return $times;
        }
        else 
        {
            $sql = "SELECT clickTimes
            FROM cp_clickcount
            ORDER BY aid";
            $times = $this->db->query($sql)->getAll();
            return $times;
        }
    }
    
    public function writeToDB($id,$clickTimes)
    {
        $sql = "UPDATE cp_clickcount SET clickTimes = {$clickTimes} WHERE aid = {$id}";
        return $this->db->query($sql);
    }
    
    public function reWriteRedis($clickTimes)
    {
        $this->load->driver('cache', array('adapter' => 'redis'));
        $REDIS = $this->config->item('REDIS');
        $this->cache->save($REDIS["CLICK_NUM"], $clickTimes, 0);
    }
}
?>