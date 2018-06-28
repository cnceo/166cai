<?php

/**
 * Copyright (c) 2014,上海二三四五网络科技股份有限公司
 * 摘    要：后台首页
 * 作    者：wangl@2345.com
 * 修改日期：2014.11.06
 */
defined('BASEPATH') OR die('No direct script access allowed');

class Login extends MY_Controller
{
    
    /**
     * 参    数：无
     * 作    者：wangl
     * 功    能：后台首页
     * 修改日期：2014.11.05
     */
    public function index()
    {
    	if ($this->uid) $this->redirect ( '/backend/' );
    	
    	$this->load->library(array('session'));
    	$uinfo = $_SESSION['oa_auth_userinfo'];
    	$data = array();
    	if ($uinfo['username'] && $this->Model_capacity->getUserByName($uinfo['username'])) {
    		set_cookie(array(
    		'name'    => 'cp_uname',
    		'value'   => $uinfo['username'],
    		'expire'  => 0,
    		'httponly'=> true
    		));
    		$data['username'] = $uinfo['username'];
    	}
    	$wrongpass = $this->session->userdata('wrongpass');
    	if ($wrongpass) $data['wrongpass'] = $wrongpass;
    	$this->load->view("login", $data);
    }
    
    public function dologin()
    {
    	$pass = $this->input->post ( 'pass' );
    	$name = $this->input->post ( 'name' );
    	$this->cre_pubkey ();
    	if (empty($name)) $name = get_cookie('cp_uname');
    	if ($name && $pass)
    	{
    		$this->load->library(array('session'));
    		$this->decrypt($pass);
    		//验证用户
    		$res = $this->Model_capacity->checkUser ( $name, $pass );
    		if ($res)
    		{
    			$this->session->set_userdata('wrongpass', '0');
    			set_cookie(array(
    			'name'    => 'cp_uname',
    			'value'   => $name,
    			'expire'  => 0,
    			'httponly'=> true
    			));
    			set_cookie(array(
    			'name'    => 'cp_uid',
    			'value'   => $res ['id'],
    			'expire'  => 0,
    			'httponly'=> true
    			));
    			$this->uid = get_cookie ( 'cp_uid' );
    			$this->uname = $name;
    			$msg = "登录成功，用户名：{$name}";
    			$this->syslog(9, $msg);
    			$this->redirect ( '/backend/' );
    		} else
    		{
    			$this->session->set_userdata('wrongpass', '1');
    			$this->redirect ( '/backend/login' );
    		}
    	}
    }
    
    public function out()
    {
    	$this->uname = $this->uid = null;
    	delete_cookie('cp_uid');
    	delete_cookie('cp_uname');
    	$this->redirect ( '/backend/login' );
    }
    
    //解密
    private function decrypt (&$pass)
    {
    	$decrypt = '';
    	$passArr = explode ( ' ', $pass );
    	foreach ( $passArr as $ps )
    	{
    		$decrypt .= trim ( $this->tools->rsa_decrypt ( $ps, true ) );
    	}
    	if (! empty ( $decrypt ))
    	{
    		$decrypts = explode ( '<PSALT>', $decrypt );
    		$pass = $decrypts [0];
    	}
    }
    
}
