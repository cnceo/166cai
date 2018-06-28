<?php
/**
 * 竞彩不中包赔统计  每天6点跑
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Jcbzbp_Cal extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->model("activity_model");
        $this->activity_model->calJcbpData();
    }
    
}