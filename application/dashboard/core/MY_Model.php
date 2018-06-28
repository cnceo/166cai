<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：投注站外部系统后台model
 * 作    者：xumw@2345.com
 * 修改日期：2016.1.26
 */
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class MY_Model extends CI_Model
{
	protected $_table;

	public function __construct()
	{
		$this->load->database ();
	}

	/**
	 * 对单张表的新增、根据ID修改操作
	 * 
	 * @param
	 *        	array 需要修改的字段、值 $data
	 * @param int $id        	
	 */
	public function save($data, $where = null)
	{
		if ($where)
		{
			$this->db->update ( $this->_table, $data, $where );
		} else
		{
			$data ['created'] = date ( 'Y-m-d H:i:s' );
			$this->db->insert ( $this->_table, $data );
			return $this->db->insert_id ();
		}
	}

	/**
	 * 常规根据ID删除操作
	 * 
	 * @param int $id        	
	 */
	public function delById($id)
	{
		$this->db->delete ( $this->_table, array (
				'id' => $id 
		) );
	}
}
