<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * M版 错误页
 * @date:2018-08-31
 */

class Error extends MY_Controller 
{

    public function __construct() 
    {
        parent::__construct();
    }

    /*
     * M版 错误页
     * @date:2018-08-31
     */
    public function index() 
    {
        $this->load->view('/error/error_404');
    }

}