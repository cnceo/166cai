<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * 客户端APP - 数字彩开奖 - 小米推送
 * @date:2016-02-18
 */
class Clii_Cfg_Clear_Pid extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('cron_model');
		$this->base_path = APPPATH;
	}
	
	public function index($func='', $id=0, $pid=0)
	{
		$this->$func($id, $pid);
	}
	/*
	 * 通过执行脚本获得数据
	 * $params 存放模型和方法名称
	 * @date:2016-11-23
	 */
	public function getDatas($model, $func, $param = '')
	{
		$this->load->model($model);
		$redatas = $this->$model->$func($param);
		if(!empty($redatas))
		{
			echo json_encode($redatas);
		}
	}
	
	private function set_pid($id, $pid)
	{
		$lnames = explode('::', __METHOD__);
		$method = "m" . $lnames[1];
		$this->cron_model->$method($id, $pid);
	}
	
	private function get_crons($id, $pid)
	{
		$this->get_comm_list(__METHOD__, $id, $pid);
	}
	
	private function get_run_list($id, $pid)
	{
		$this->get_comm_list(__METHOD__, $id, $pid);
	}
	
	private function get_comm_list($func, $id, $pid)
	{
		$lnames = explode('::', $func);
		$cfiles =  $this->base_path . "/logs/cronrdb/{$lnames[1]}.log";
		$method = "m{$lnames[1]}";
		$crons = $this->cron_model->$method();
		file_put_contents($cfiles, serialize($crons));
	}
	
	
}