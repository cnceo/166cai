<?php
/**
 * 追号不中包赔
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Activity_Chase extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('activity_model');
    }

    public function index()
    {
        $this->activity_model->chaseActivity();
    }
}