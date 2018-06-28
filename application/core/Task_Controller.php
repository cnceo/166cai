<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Task_Controller extends CI_Controller 
{
    public $con;
    public $act;
	
	public $cmd_path;
	public $php_path;
	public $app_path;
	public $log_path;
	
    public function __construct() 
    {
        parent::__construct();
        $this->load->library("Tlog");
        $this->load->helper('string');
        $this->load->driver('cache', array('adapter' => 'redis'));
        $this->REDIS = $this->config->item('REDIS');
        $this->con = $this->router->class;
        $this->act = $this->router->method;
        $this->cmd_path = $this->config->item('cmd_path');
        $this->php_path = $this->config->item('php_path');
        $this->app_path = $this->config->item('app_path');
        $this->log_path = $this->app_path . 'logs';
        $this->load->config('scheme');
        $this->db_config = $this->config->item('db_config'); 
        if(preg_match('/^cli_/i', $this->con))
        {
         	if ($this->input->is_cli_request()) 
         	{
	            $this->controlRun($this->con);
	            $this->load->library('processlock');
	            $param = $this->con . '-' . $this->act.(isset($this->router->uri->segments[4]) ? '-' . $this->router->uri->segments[4] : '');
 	            if (!$this->processlock->getLock($param))
 	            {
 	                log_message('LOG', "This file({$param}) is running!", 'LOCK');
 	                die("This file({$this->con}) is running!");
 	            }
        	}
        }
    }

    protected static function controlRun($fname) 
    {
        $fpath = APPPATH . '/logs/plock';
        if (file_exists("$fpath/$fname.start")) 
        {
            unlink("$fpath/$fname.stop");
            unlink("$fpath/$fname.start");
        } 
        else if (file_exists("$fpath/$fname.stop")) 
        {
            die($fname . date('Y-m-d H:i:s') . ":被手动停止\n");
        }
    }
    
    /**
     * 当前任务回收工作
     * @param int $taskId	任务id
     * @param boolean $stopFlag 关闭状态  true 回收并关闭任务  false 只回收不关闭任务
     */
    public function stopCurrentTask($taskId, $stopFlag = true)
    {
    	$this->load->model('task_model');
    	$task = $this->task_model->getTaskById($taskId);
    	$stop = ($stopFlag && ($task['stop'] == 2)) ? 1 : 0;
    	return $this->task_model->updateTask($taskId, array('state' => 0, 'pid' => 0, 'stop' => $stop, 'end_time' => date('Y-m-d H:i:s')));
    }
    
    /**
     * 获取同步配置
     * @return Ambigous <number, unknown>
     */
    public function webInstance()
    {
    	$webInstance = $this->config->item('web_instance');
    	return empty($webInstance) ? 1 : $webInstance;
    }
}