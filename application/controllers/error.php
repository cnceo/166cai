<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Error extends MY_Controller 
{

    /**
     * 彩票后台入口控制器
     */
    public function index() 
    {
    	
    	$this->load->view('v1.1/elements/common/header');
    	$this->load->view('v1.1/error/error_404');
    	$this->load->view('v1.1/elements/common/footer_short');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */