<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：基准后台controller
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
defined('BASEPATH') OR die('No direct script access allowed');

class MY_Controller extends CI_Controller
{
//     public $uname; //用户名
    public $capacity; //权限

	protected $uid;
	protected $uname;
	protected $con;
	protected $act;
    //此参数仅用于本地登录网站，提交代码时需关闭此按钮
    public $isTest = FALSE;
    
    const NUM_PER_PAGE = 20; //分页条数
    
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array(
        	'fn_curl',
            'fn_common',
        	'cookie',
        	'string'
        ));

        $url_prefix = $this->config->item('url_prefix');
        $this->url_prefix = isset($url_prefix[$this->config->item('base_url')]) ? $url_prefix[$this->config->item('base_url')] : 'http';
        $this->load->model('Model_capacity');
        $this->con = $this->router->class;
        $this->act = $this->router->method;
        $this->check_logined();
//        $this->isTest OR $this->check_logged();
    }
    
    private function check_logined()
    {
    	$this->uid = get_cookie ( 'cp_uid' );
    	$this->uname = get_cookie ( 'cp_uname' );
//     	if (!in_array($_SERVER['REMOTE_ADDR'], array('222.66.88.98', '127.0.0.1', '180.169.86.54')) && !in_array($this->con, array('main'))) {
//     		$_SESSION['currenturl'] = $_SERVER['REQUEST_URI'];
//     		$this->load->library('oacheck');
//     		$this->oacheck->index();
//     	}
    	
    	$capacity_info = $this->Model_capacity->get_capacity($this->uname, 0, true);
    	$username = isset($capacity_info['name']) ? $capacity_info['name'] : '';
    	if (! in_array ( $this->con, array ('login') ) && (empty ( $this->uid ) || (!$username || $username != $this->uname)) && $this->act !== 'callback')
    	{
    		echo "<script type='text/javascript'>parent.location.href='/backend/login';</script>";
    		exit();
    	}
    	
    	$capacity = isset($capacity_info['capacity']) ? $capacity_info['capacity'] : '';
    	$this->capacity = explode(",", $capacity);
    }
    
    /**
     * 参    数：空
     * 作    者：wangl
     * 功    能：判断是否登录
     * 修改日期：2014.11.05
     */
    private function check_logged()
    {
        $this->load->library(array(
            'session',
            'encrypt'
        ));
        $logged_in = $this->session->userdata('cp_session_man');
        $username = $this->session->userdata('cp_session_user');
        $this->load->model('Model_capacity');
        $capacity_info = $this->Model_capacity->get_capacity($username, 0, true);
        $username = isset($capacity_info['name']) ? $capacity_info['name'] : '';
        $this->uname = $this->encrypt->decode($logged_in, MANAGER_ENCODE_KEY);
        if (!$logged_in || !$username || $username != $this->uname)
        {
            exit("Access denied");
        }

        $capacity = isset($capacity_info['capacity']) ? $capacity_info['capacity'] : '';
        $this->capacity = explode(",", $capacity);
    }
    
    /**
     * 参    数：$capacity 权限
     * 作    者：wangl
     * 功    能：判断用户是否拥有该权限
     * 修改日期：2014.11.10
     */
    public function check_capacity($capacity, $isAjax = false)
    {
    	
       if ($this->isTest OR in_array($capacity, $this->capacity) || $this->capacity[0] == 'all')
       {
           return true;
       }
       $errorMsg = "呐，这么做最重要的是要有权限啦！";
       $isAjax && $this->ajaxReturn('n', $errorMsg);
       die($errorMsg);
    }
    public function get_all_capacity()
    {
        return $this->capacity;
    }
    /**
     * 参    数：$status 状态
     *                $message 消息
     *                $data 返回附加数据
     *                $type 类型
     * 作    者：wangl
     * 功    能：ajax返回函数
     * 修改日期：2014.11.05
     */
    public function ajaxReturn($status, $message = '', $data = array(), $type = 'json')
    {
        $return_data = array(
            'status' => $status,
            'message' => $message,
            'info' => $data
        );
        //        $return_data = array_my_icov($return_data);
        $type = strtolower($type);
        if ($type == 'json')
        {
            exit(json_encode($return_data));
        }
        elseif ($type == 'xml')
        {
            $xml = '<?xml version="1.0" encoding="utf-8"?>';
            $xml .= '<return>';
            $xml .= '<status>' . $status . '</status>';
            $xml .= '<message>' . $message . '</message>';
            $xml .= '<data>' . serialize($data) . '</data>';
            $xml .= '</return>';
            exit($xml);
        }
        elseif ($type == 'eval')
        {
            exit($return_data);
        }
        else
        {
        }
    }
    /**
     * 参    数：$mod 模块
     *                $mark 内容
     * 作    者：wangl
     * 功    能：记录系统日志
     * 修改日期：2014.11.10
     */
    public function syslog($mod, $mark)
    {
        $this->load->model('Model_syslog');
        $this->Model_syslog->add_syslog($mod, $mark, $this->uname);
//         $this->Model_syslog->add_syslog($mod, $mark);
    }
    
    /**
     * 参    数：$time1 时间1
     *          $time2 时间2
     * 作    者：wangl
     * 功    能：过滤
     * 修改日期：2014.11.10
     */
     public function filterTime(&$time1, &$time2)
     {
         if (!empty($time1) || !empty($time2))
         {
             if (empty($time1))
             {
                 $time1 = date("Y-m-d 00:00:00", strtotime('-3 months', strtotime($time2)));
             }
             elseif (empty($time2))
             {
                 $time2 = date("Y-m-d 23:59:59", strtotime('+ 3 months', strtotime($time1)));
             }
             else
             {
                 if (strtotime($time1) > strtotime($time2))
                 {
                     echo "时间非法";
                     exit;
                 }
                 
                 if (strtotime("-3 months", strtotime($time2)) > strtotime($time1))
                 {
                     $time2 = date("Y-m-d 23:59:59", strtotime("+3 months", strtotime($time1)));
                 }
             }
         }
         else
         {
             $time2 = date("Y-m-d 23:59:59");
             $time1 = date("Y-m-d 00:00:00", strtotime('-1 month'));
         }
     }
     
     protected function cre_pubkey()
     {
     	$this->load->library('tools');
     	$sec = substr(time(), -5);
     	$this->pub_salt = $this->tools->rsa_encrypt($sec);
     }
     
     protected function redirect($uri = '', $method = 'location', $http_response_code = 302)
     {
     	switch ($method)
     	{
     		case 'refresh' :
     			header ( "Refresh:0;url=" . $uri );
     			break;
     		default :
     			header ( "Location: " . $uri, TRUE, $http_response_code );
     			break;
     	}
     	exit ();
     }
     
     protected function checkenv($env, $isAjax = false) {
     	if ($env !== ENVIRONMENT) {
     		if ($isAjax) $this->ajaxReturn(false, '环境不对');
     		die('环境不对');
     	}
     }
    
}
