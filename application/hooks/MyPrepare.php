<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MyPrepare extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('session');
	}
	public function CheckLogged()
	{
		$con = $this->router->class;
		$method = $this->router->method;
		if($con=='main' && $method=='index')
			return true;
		$logged_in = $this->session->userdata('logged_in');
		if($logged_in)
		{
			return true;
		}
		else 
		{
			$this->session->set_userdata('reffer', $_SERVER['REQUEST_URI']);
			header("location:/admin/");
		}
	}
}