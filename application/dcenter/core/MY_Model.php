<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	const CODE_SUCCESS = 0;
    public function __construct() {
        parent::__construct();
        
        $this->load->config('scheme');
        $this->db_config = $this->config->item('db_config');
    }
	
	protected function maps($v)
	{
		return '?';
	}
	
	protected function onduplicate($fields, $upd, $apd=array(), $_ = '')
	{
		$tail = array();
		if(empty($apd['add'])) $apd['add'] = array();
		if(empty($apd['con'])) $apd['con'] = array();
		foreach ($fields as $field)
		{
			if(in_array($field, $upd))
			{
				if(in_array($field, $apd['add']))
				{
					array_push($tail, "$field = $_$field + values($field)");
				}
				elseif(in_array($field, $apd['con']))
				{
					array_push($tail, "$field = if($_$field < values($field), values($field), $_$field)");
				}
				elseif ($field == 'aduitflag')
				{
				    array_push($tail, "$field = if($_$field = 0, values($field), $_$field)");
				}
				else 
				{
					array_push($tail, "$field = values($field)");
				}
			}
		}
		if(!empty($tail))
			return " on duplicate key update " . implode(', ', $tail);
	}
	
	public function getFields($table)
	{
		$sql = "SELECT column_name FROM information_schema.COLUMNS
		WHERE table_schema = '{$this->db_config['cp']}'
		AND table_name = ?";
		$fields = $this->db->query($sql, array($table))->getCol();
		if(in_array('id', $fields))
			$fields = array_slice($fields, 1);
		return $fields;
	}
	
	protected function getDB($db)
    {
    	$dbmap = array('cfgDB' => 'cfg', 'dc' => 'dc', 'db' => 'default');
    	$this->$db = $this->load->database($dbmap[$db], true);	
    }
}
