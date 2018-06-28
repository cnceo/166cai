<?php
class Cp_Partner_Shop extends MY_Model
{
	protected $_table = 'cp_partner_shop';

	public function __construct()
	{
		parent::__construct ();
	}

	public function getListData($start, $offset, $partner, $shopNum = null)
	{
		$sql = "from cp_partner_shop where partnerId = ?";
		$data = array (
				$partner 
		);
		if (! is_null ( $shopNum ))
		{
			$sql .= " and shopNum like ?";
			$data [] = "%" . $shopNum . "%";
		}
		$column = "id, shopNum, cname, phone, qq, webchat, address, status";
		$res ['total'] = $this->db->query ( "select count(*) as num " . $sql, $data )
			->getRow ();
		$res ['datas'] = $this->db->query ( "select {$column} " . $sql . " order by shopNum limit {$start}, {$offset}", $data )
			->getAll ();
		return $res;
	}

	public function getDataById($id)
	{
		$sql = "select id, partnerId, shopNum, cname, phone, qq, webchat, other_contact, lottery_type, address, created, status from cp_partner_shop where id = ?";
		return $this->db->query ( $sql, array (
				$id 
		) )
			->getRow ();
	}

	public function checkShopnum($pid, $shopNum, $id = null)
	{
		$sql = "select id from cp_partner_shop where partnerId = ? and shopNum = ?";
		$data = array (
				$pid,
				$shopNum 
		);
		if ($id)
		{
			$sql .= " and id <> ?";
			$data [] = $id;
		}
		return $this->db->query ( $sql, $data )
			->getRow ();
	}
}