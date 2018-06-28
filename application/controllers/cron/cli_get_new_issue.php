<?php
/**
 * 吉林快三获取期次比对更新
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Get_New_Issue extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($taskId = 9, $lid = 56)
    {
        $this->load->library('ticket_hengju');
        $this->ticket_hengju->met_getIssue($lid);
    }
}