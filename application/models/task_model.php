<?php
/**
 * 任务管理数据类
 * @author shigx
 *
 */
class Task_Model extends MY_Model {

    public function __construct()
    {
        parent::__construct();
        $this->base_path = APPPATH;
    }
    
    /**
     * 获取触发任务列表
     * @return mixed
     */
    public function get_task_crons()
    {
    	return $this->get_comm_list(__METHOD__);
    }
    
    /**
     * 获取触发任务表记录
     */
    public function mget_task_crons()
    {
    	return $this->cfgDB->query('SELECT * FROM cp_task_manage WHERE state = 0 and stop = 0')->getAll();
    }
    
    /**
     * 触发任务表中 正在运行中的任务
     */
    public function get_task_run_list()
    {
    	return $this->get_comm_list(__METHOD__);
    }
    
    private function get_comm_list($lname)
    {
    	$lnames = explode('::', $lname);
    	$cfiles =  $this->base_path . "/logs/cronrdb/{$lnames[1]}";
    	$croname = "cron/cli_cfg_clear_task_pid index/{$lnames[1]}";
    	exec("{$this->php_path} {$this->cmd_path} $croname", $outPut, $status);
    	if(count($outPut) == 1)
    	{
    		return json_decode($outPut[0], true);
    	}
    	
    	return array();
    }
    
    /**
     * 触发任务表中 正在运行中的任务
     */
    public function mget_task_run_list()
    {
    	$rows = $this->cfgDB->query('SELECT * FROM cp_task_manage WHERE pid > 0')->getAll();
    	$runList = array();
    	foreach ($rows as $row)
    	{
    		$runList[$row['id']] = $row['pid'];
    	}
    	return $runList;
    }
    
    public function set_task_values($id, $pid = 0)
    {
    	$croname = "cron/cli_cfg_clear_task_pid index/set_pid/{$id}/$pid";
    	system("{$this->php_path} {$this->cmd_path} $croname", $status);
    }
    
    /**
     * 任务触发表更新记录
     * @param unknown_type $id
     * @param unknown_type $pid
     */
    public function mset_pid($id, $pid)
    {
    	$setetime = '';
    	$state = 0;
    	if($pid > 0)
    	{
    		$setetime = ", end_time = now(), stop = 2";
    		$state = 1;
    	}
    	else
    	{
    		$setetime = ", end_time = now(), stop = 0";
    	}
    	return $this->cfgDB->query("update cp_task_manage set state = ?, pid = ?
    			$setetime where id = ?", array($state, $pid, $id));
    }
    
    /**
     * 修改任务记录信息
     * @param unknown_type $taskId
     * @param unknown_type $data
     */
    public function updateTask($taskId, $data = array())
    {
    	$this->cfgDB->where('id', $taskId);
    	$this->cfgDB->update('cp_task_manage', $data);
    	return $this->cfgDB->affected_rows();
    }
    
    /**
     * 根据类型和彩种id更新任务状态
     * @param unknown_type $type
     * @param unknown_type $lid
     * @param unknown_type $stop
     */
    public function updateStop($type, $lid, $stop)
    {
    	$this->cfgDB->query("update cp_task_manage set stop= ? where task_type= ? and lid= ?", array($stop, $type, $lid));
    	return $this->cfgDB->affected_rows();
    }
    
    /**
     * 根据任务id查询任务记录
     * @param unknown_type $taskId
     */
    public function getTaskById($taskId)
    {
    	return $this->cfgDB->query("select * from cp_task_manage where id=?", array($taskId))->getRow();
    }

    // 更新启停状态
    public function updateRestart($fname, $status)
    {
        $this->cfgDB->query("update cp_task_restart set status = ? where fname = ?", array($status, $fname));
        return $this->cfgDB->affected_rows();
    }
    
    public function getJLKSTaskStatus($lid = 56)
    {
        $tableArr = array(56 => 'cp_jlks_paiqi', 57 => 'cp_jxks_paiqi');
        $issueTime = $this->cfgDB->query("select award_time from {$tableArr[$lid]} where show_end_time<now() and award_time>now() limit 1")->getRow();
        return array($issueTime);
    }
    
    public function getCurJLKS($lid = 56)
    {
        $tableArr = array(56 => 'cp_jlks_paiqi', 57 => 'cp_jxks_paiqi');
        $issueTime = $this->cfgDB->query("select issue from {$tableArr[$lid]} where sale_time<now() and award_time>now() limit 1")->getRow();
        return array($issueTime);
    }    

    /**
     * 查询开具号码需要抓取的数据源
     * @param string $ctype 彩种类型
     * @return unknown
     */
    public function getCronScore($ctype)
    {
        $sql = "select lname, ctype from cp_cron_score where ctype = ? and start = 1 limit 1";
        return $this->dc->query($sql, array($ctype))->getRow();
    }

   public function getJLKSIssueStatus($ctype = 'jlks_issue')
   {
       $sql = "select lname from cp_cron_score where ctype = ? and start = 1 limit 1";
       return $this->dc->query($sql, array($ctype))->getRow();
   }
}
