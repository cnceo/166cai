<?php
class Cp_Partner_Shop_File extends MY_Model
{
	protected $_table = 'cp_partner_shop_file';

	public function getDataByShopId($shopId)
	{
		$sql = "select id, filename, filepath from " . $this->_table . " where shopId = ? and delete_flag <> 1";
		return $this->db->query ( $sql, array (
				$shopId 
		) )
			->getAll ();
	}

	public function saveAll($files, $data)
	{
		$sql = "insert into " . $this->_table . " (`partnerId`, `shopId`, `filename`, `filepath`, `created`) 
				values";
		foreach ( $files as $file )
		{
			$sql .= "('{$data['partnerId']}', '{$data['shopId']}', '{$file['filename']}', '{$file['filepath']}', '" . date ( 'Y-m-d H:i:s' ) . "'),";
		}
		$sql = substr ( $sql, 0, - 1 );
		return $this->db->query ( $sql );
	}

	public function getFileByName($shopId, $name)
	{
		$sql = "select id, filename, filepath from " . $this->_table . " where shopId = {$shopId} and filename = '{$name}' and delete_flag <> 1";
		return $this->db->query ( $sql )
			->getRow ();
	}
}