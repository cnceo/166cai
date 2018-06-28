<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cli_Checkusers extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }
    
    public function checkRealname() {
    	$this->user_model->checkusersbyrealname();
    	$this->user_model->freezeByRealName();
    }
    
    public function checkIp() {
    	$this->user_model->checkusersByIp();
    }
}