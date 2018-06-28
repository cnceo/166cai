<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Tlog.php
 * 任务执行日志类
 */

class Tlog
{
	/*
	 * 框架变量*/
	//private $CI;
	public function __construct()
	{
		//$this->CI = & get_instance();
	}
	
	/**
	 * 错误日志记录
	 * @param int $errno		错误类型
	 * @param string $errstr	错误描述
	 * @param string $errfile	错误文件描述
	 * @param string $errline	错误行
	 * @param string $file		日志文件名
	 */
	public function errorHandler($errno,$errstr,$errfile,$errline, $file = '')
	{
		$filepath = APPPATH . 'logs/taskLog/log-'.date('Y-m-d') . '-' . $file . '-error' . '.php';
		$arr = array(
			'['.date('Y-m-d H:i:s').']',
			$errno,
			'|',
			$errstr,
			$errfile,
			'line:'.$errline,
		);
		$message = implode(' ',$arr) . "\r\n";
		//写入错误日志
		error_log($message, 3, $filepath);
	}
	
	/**
	 * 致命错误记录
	 * @param unknown_type $file
	 */
	public function fatalErrorHandler($file = '')
	{
		$e = error_get_last();
		switch($e['type'])
		{
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				$this->errorHandler($e['type'], $e['message'], $e['file'], $e['line'], $file);
				break;
		}
	}
	
	/**
	 * 成功日志记录
	 * @param int $_start_time	微秒数 
	 * @param string $message	信息描述
	 * @param string $file		文件名
	 */
	public function infoHandler($_start_time, $message, $file = '')
	{
		$filepath = APPPATH . 'logs/taskLog/log-'.date('Y-m-d') . '-' . $file . '.php';
		$message = '['.date('Y-m-d H:i:s').']' . (microtime(true) - $_start_time) . ' | ' . $message . "\r\n";
		//写入错误日志
		error_log($message, 3, $filepath);
	}
}