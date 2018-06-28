<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Safe_Model extends MY_Model
{
	public $tbname;

	public function __construct()
	{
		parent::__construct();
		$this->load->library('tools');
	}

    // 获取安全中心用户摘要
	public function getSafeBrief()
	{
		return array(
          'bind_pay_password' => true,  // 支付密码
          'bind_phone' => true,  // 手机号码绑定
          'bind_id_card' => true,  // 真实身份
          'bind_bank_card' => true,  // 银行卡
          'bind_email' => true,  // 邮箱绑定
        );
	}

	public function getProvince()
    {
		$sql = "select DISTINCT province from cp_city";
		return $this->db->query( $sql )->row_array();
    }

    public function getCityListByProvince( $province )
    {
		$sql = "select DISTINCT city from cp_city WHERE province = ?";
		return $this->db->query( $sql, array( $province ) )->row_array();
    }
}
