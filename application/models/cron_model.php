<?php

class Cron_Model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->base_path = APPPATH;
    }
    
    public function get_crons()
    {
    	return $this->get_comm_list(__METHOD__);
    }

    public function mget_crons()
    {
        return $this->cfgDB->query('SELECT * FROM cp_cron_list WHERE state = 0 and stop = 0')->getAll();
    }
    
    public function get_run_list()
    {
    	return $this->get_comm_list(__METHOD__);
    }
    
    private function get_comm_list($lname)
    {
    	$lnames = explode('::', $lname);
    	$cfiles =  $this->base_path . "/logs/cronrdb/{$lnames[1]}";
    	$croname = "cron/clii_cfg_clear_pid index/{$lnames[1]}";
		system("{$this->php_path} {$this->cmd_path} $croname", $status);
		$crons = file_get_contents($cfiles . '.log');
		return unserialize($crons);
    }

    public function mget_run_list()
    {
        $rows = $this->cfgDB->query('SELECT * FROM cp_cron_list WHERE pid > 0')->getAll();
        $runList = array();
        foreach ($rows as $row)
        {
            $runList[$row['id']] = $row['pid'];
        }
        return $runList;
    }

    public function set_value($id, $fname, $fvalue)
    {
        return $this->cfgDB->query("UPDATE cp_cron_list SET $fname = ? WHERE id = ?", array($fvalue, $id));
    }

    public function set_alarm($ukeys, $ctype, $content)
    {
        $sql = "insert cp_alarm_list(cdate, ukeys, ctype, content) values(date(now()), ?, ?, ?) " .
            $this->onduplicate(array('content'), array('content'));

        return $this->cfgDB->query($sql, array($ukeys, $ctype, $content));
    }
    
    public function set_values($id, $pid = 0)
    {
    	$croname = "cron/clii_cfg_clear_pid index/set_pid/{$id}/$pid";
		system("{$this->php_path} {$this->cmd_path} $croname", $status);
    }
    
	public function mset_pid($id, $pid)
    {
    	$setetime = '';
    	$state = 0;
    	if($pid > 0)
    	{
    		$setetime = ", end_time = now()";
    		$state = 1;
    	}
    	return $this->cfgDB->query("update cp_cron_list set state = ?, pid = ?
    	$setetime where id = ?", array($state, $pid, $id));
    }

}
