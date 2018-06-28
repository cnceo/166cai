<?php
class Cp_Partner extends MY_Model
{
	protected $_table = 'cp_partner';

	public function __construct()
	{
		parent::__construct ();
	}

	public function checkUser($name, $pass)
	{
		$sql = "select id, name from cp_partner where name = ? and pass = ?";
		$res = $this->db->query ( $sql, array (
				$name,
				md5($pass) 
		) )
			->getRow ();
		return $res;
	}
}