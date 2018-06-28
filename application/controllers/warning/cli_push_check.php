<?php
/**
 * 推送监控报警，十分钟一启动
 * @author mwxu
 *
 */
class Cli_Push_Check extends MY_Controller
{
	
	public function __construct() {
		parent::__construct();
		$this->load->model('warning_model', 'wanning');
	}
	
	public function index() {
		//推送监控报警
		$this->wanning->pushCheck();
	}
}