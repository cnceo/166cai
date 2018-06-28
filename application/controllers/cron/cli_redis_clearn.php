<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * redis缓存定期清理脚本
 * @author shigx
 *
 */
class Cli_Redis_Clearn extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * 清除缓存主方法
	 * @param int $isSpan 是否分段清除 1 是   0 否 清除全部
	 */
	public function index($isSpan = 1)
	{
		$this->clearnUser($isSpan);
	}
	
	/**
	 * 清除用户缓存信息
	 * @param boolean $isSpan 是否分段清除 true 是   false 否 清除全部
	 */
	private function clearnUser($isSpan)
	{
		$this->load->model('user_model');
		$this->user_model->clearnUserRedis($isSpan);
	}
}