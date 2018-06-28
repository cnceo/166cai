<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tmp_Code_Model extends MY_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * [writeCodes 写入投注串]
	 * @author LiKangJian 2017-08-08
	 * @param  [type] $codes [description]
	 * @param  [type] $lid   [description]
	 * @return [type]        [description]
	 */
	public function writeCodes($codes,$lid,$endTime)
	{
		$upload_no = date('Ymdhis').'_'.$lid.rand(1000000,9999999);
		$sql = "insert into cp_codes_temp(lid,upload_no,codes,endtime,created) VALUES(?,?,?,?,NOW()); ";
		$tag = $this->db->query($sql,array($lid,$upload_no,$codes,$endTime));
		if($tag) return $upload_no;
		return false;
	}
	/**
	 * [getCode 得到投注串]
	 * @author LiKangJian 2017-08-08
	 * @param  [type] $upload_no [description]
	 * @return [type]            [description]
	 */
	public function getCode($upload_no)
	{
		$sql = "select codes from cp_codes_temp where upload_no = ?";
		$tag = $this->db->query($sql,array($upload_no))->getCol();
		if(isset($tag[0])) return $tag[0];
		return '';
	}
	/**
	 * [updateOrderId 更新订单]
	 * @author LiKangJian 2017-08-08
	 * @param  [type] $orderId   [description]
	 * @param  [type] $upload_no [description]
	 * @return [type]            [description]
	 */
	public function updateOrderId($orderId,$upload_no)
	{
		$sql =  "update cp_codes_temp set orderId = ? where  upload_no = ?";
		return  $this->db->query($sql,array($orderId,$upload_no));
	}
	/**
	 * [getCodeByOrderId 通过orderId]
	 * @author LiKangJian 2017-08-08
	 * @param  [type] $orderId [description]
	 * @return [type]          [description]
	 */
	public function getCodeByOrderId($orderId)
	{
		$sql = "select codes,lid,endTime from cp_codes_temp where orderId = ?";
		$tag = $this->db->query($sql,array($orderId))->getRow();
		return $tag;
	}
}
