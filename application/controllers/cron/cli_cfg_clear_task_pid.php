<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 客户端APP - 数字彩开奖 - 小米推送
 * @date:2016-02-18
 */
class Cli_Cfg_Clear_Task_Pid extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('task_model');
		$this->base_path = APPPATH;
	}
	
	/*
	 * 客户端APP - 数字彩开奖小米推送主进程
	 * @date:2016-02-19
	 */
	public function index($func='', $id=0, $pid=0)
	{
		$this->$func($id, $pid);
	}
	
	private function set_pid($id, $pid)
	{
		$lnames = explode('::', __METHOD__);
		$method = "m" . $lnames[1];
		$this->task_model->$method($id, $pid);
	}
	
	private function get_task_crons($id, $pid)
	{
		$this->get_comm_list(__METHOD__, $id, $pid);
	}
	
	private function get_task_run_list($id, $pid)
	{
		$this->get_comm_list(__METHOD__, $id, $pid);
	}
	
	private function get_comm_list($func, $id, $pid)
	{
		$lnames = explode('::', $func);
		$cfiles =  $this->base_path . "/logs/cronrdb/{$lnames[1]}.log";
		$method = "m{$lnames[1]}";
		$crons = $this->task_model->$method();
		echo json_encode($crons);
	}
	
	
}