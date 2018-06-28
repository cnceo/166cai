<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

	const CODE_SUCCESS = 0;
    public function __construct() {
        parent::__construct();
    }
	
	protected function maps($v)
	{
		return '?';
	}
	
	protected function onduplicate($fields, $upd, $apd=array(), $_ = '')
	{
		$tail = array();
		foreach ($fields as $field)
		{
			if(in_array($field, $upd))
			{
				if(in_array($field, $apd))
				{
					array_push($tail, "$field = $_$field + values($field)");
				}else
				{
					if(in_array($field, array('status')))
					{
						array_push($tail, "$field = if($field < values($field), values($field), $field)");
					}
					else
					{
						array_push($tail, "$field = values($field)");
					}
				}
			}
		}
		if(!empty($tail))
			return " on duplicate key update " . implode(', ', $tail);
	}
	
	protected function getSplitTable($lid)
	{
		$tables = array();
		if(in_array($lid, array(53, 21406, 21407, 21408, 54, 55, 56, 57, 21421)))
		{
			$this->load->config('order');
			$lidmap = $this->config->item("cfg_lidmap");
			$tables['split_table'] = "cp_orders_split_{$lidmap[$lid]}";
			$tables['relation_table'] = "cp_orders_relation_{$lidmap[$lid]}";
		}
		else
		{
			$tables['split_table'] = "cp_orders_split";
			$tables['relation_table'] = "cp_orders_relation";
		}
		return $tables;
	}
}
