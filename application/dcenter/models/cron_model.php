<?php

class Cron_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_crons()
    {
    	$this->getDB('dc');
        return $this->dc->query('SELECT * FROM cp_cron_list WHERE state = 0')->getAll();
    }

    public function get_run_crons()
    {
    	$this->getDB('dc');
        $rows = $this->dc->query('SELECT * FROM cp_cron_list WHERE pid > 0')->getAll();
        $runList = array();
        foreach ($rows as $row)
        {
            $runList[$row['id']] = $row['pid'];
        }
        unset($runList[0]);

        return $runList;
    }

    public function set_value($id, $fname, $fvalue)
    {
    	$this->getDB('dc');
        return $this->dc->query("UPDATE cp_cron_list SET $fname = ? WHERE id = ?", array($fvalue, $id));
    }

    public function set_alarm($ukeys, $ctype, $content)
    {

    	$this->getDB('dc');
        $sql = "insert cp_alarm_list(cdate, ukeys, ctype, content) values(date(now()), ?, ?, ?) " .
            $this->onduplicate(array('content'), array('content'));

        return $this->dc->query($sql, array($ukeys, $ctype, $content));
    }
    
    public function set_values($id, $fields)
    {
    	$this->getDB('dc');
    	$setval = array();
    	foreach ($fields as $field => $value)
    	{
    		array_push($setval, "$field = '$value'");
    	}
    	$sql = "update cp_cron_list set " . implode(', ', $setval) . " where id = ?";
    	return $this->dc->query($sql, array($id));
    }

}
