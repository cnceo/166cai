<?php
class Model_Countclick_Daily extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function writeToDB($type ,$clickTimes)
    {
        return $this->db->query("insert into cp_clickcount_daily (type, count, date) values ('{$type}', {$clickTimes}, curdate())
         ON DUPLICATE KEY UPDATE type = values(type), count = count + values(count)");
    }

}
?>