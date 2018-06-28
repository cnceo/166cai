<?php

/**
 * 刷新银行缓存
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Refresh_Bank extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    private $limits = 2000;

    public function index()
    {
        for ($uid = 1; $uid <= 856042; $uid++) 
        { 
            $this->user_model->freshBankInfo($uid);
        }
    }
}