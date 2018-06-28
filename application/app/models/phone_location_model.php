<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phone_location_Model extends MY_Model
{
	//查看手机所属地
	public function getArea($key)
	{
		$sql = "select province from cp_phone_location_map where phone = ?";
		return $this->slave->query($sql, array($key))->getOne();
	}
}
