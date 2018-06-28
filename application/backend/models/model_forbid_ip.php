<?php
class Model_forbid_ip extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->get_db();
	}
	
	public function list_data($searchData, $page, $pageCount)
	{
		$count = $this->BcdDb->query("select count(*) from cp_forbid_ip")->getCol();
		$sql = "SELECT id, ip, address, operator, num, status, delete_time, created 
				FROM cp_forbid_ip 
				order by created desc 
				LIMIT " . ($page - 1) * $pageCount . "," . $pageCount;
		$result = $this->BcdDb->query($sql)->row_array();
		return array('count' => $count[0], 'data' => $result);
	}
	
	public function add($addData)
    {
    	if (!$this->master->query("select 1 from cp_forbid_ip where ip = ? and status = 0", array($addData['ip']))->getRow()) {
    		$keyStr = '';
    		$valueStr = '';
    		foreach ($addData as $key => $value)
    		{
    			$keyStr .= "`{$key}`,";
    			$valueStr .= "'{$value}',";
    		}
    		$keyStr .= "`created`";
    		$valueStr .= "NOW()";
    		$select = "INSERT INTO cp_forbid_ip({$keyStr}) VALUES({$valueStr})";
    		$this->master->query($select);
    		return $this->master->insert_id();
    	}
       	return false;
    }
    
    public function del($id) {
    	$this->master->query("update cp_forbid_ip set `status` = 1, delete_time = NOW() where id = {$id}");
    	return $this->master->query("select ip, address, operator from cp_forbid_ip where id = ?", array($id))->getRow();
    }

}
