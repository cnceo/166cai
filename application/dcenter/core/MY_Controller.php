<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Controller extends CI_Controller 
{

	public $cmd_path;
	public $php_path;
	public $app_path;
	public $log_path;
    public function __construct() 
    {
        parent::__construct();
        $this->con = $this->router->class;
        $this->act = $this->router->method;
        $this->cmd_path = $this->config->item('cmd_path');
        $this->php_path = $this->config->item('php_path');
        $this->app_path = $this->config->item('app_path');
        $this->log_path = $this->app_path . 'logs';
        define('UCIP', $this->get_client_ip());
        if(preg_match('/^cli_/i', $this->con))
        {
         	if ($this->input->is_cli_request()) 
         	{
	            $this->controlRun("{$this->con}-{$this->act}");
	            $this->load->library('processlock');
	            if (!$this->processlock->getLock("{$this->con}-{$this->act}")) 
	            {
	                log_message('LOG', "This file({$this->con}) is running!", 'LOCK');
	                die("This file({$this->con}) is running!");
	            }
        	}
        	else 
        	{
        		$this->redirect('/error');
        		die('不能从浏览器访问！');
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
        } else if (file_exists("$fpath/$fname.stop")) 
        {
            die($fname . date('Y-m-d H:i:s') . ":被手动停止\n");
        }
    }
    
	public function get_client_ip()
	{
	    //代理IP白名单
	    $allowProxys = array(
	        '42.62.31.40',
	        '172.16.0.40',
	    );
	    $onlineip = $_SERVER['REMOTE_ADDR'];
	    if (in_array($onlineip, $allowProxys))
	    {
	        $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
	        if ($ips)
	        {
	            $ips = explode(",", $ips);
	            $curIP = array_pop($ips);
	            $onlineip = trim($curIP);
	        }
	    }
	    if (filter_var($onlineip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
	    {
	        return $onlineip;
	    }
	    else
	    {
	        return '0.0.0.0';
	    }
	}
}