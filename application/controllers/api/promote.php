<?php
/*
 * 订单信息处理 创建、付款通用接口
* @date:2015-05-22
*/
require_once APPPATH . '/core/CommonController.php';
class Promote extends CommonController
{

	public function __construct()
	{
		parent::__construct();
		if(!in_array($this->get_client_ip(), $this->config->item('own_ip'))) {
			$response = array('code' => 1, 'msg'  => '查询失败', 'data' => array());
			echo json_encode($response);
			die();
		}
	}

	public function getData($cdate = null) {
		$cdate = empty($cdate) ? date('Y-m-d', strtotime("-1 day")) : $cdate;
		$this->load->model('order_model');
		$res = $this->order_model->getPromote($cdate);
		exit(json_encode($res));
	}
}