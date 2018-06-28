<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Last_Tenday extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->model('tenday_model');
        $this->tenday_model->calResult(42);
        $this->tenday_model->calResult(43);
        $this->tenday_model->writeEuropeOdds();
    }
    
}   

