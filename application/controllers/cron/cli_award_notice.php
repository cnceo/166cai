<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * 统计中奖信息
 * @date:2016-06-28
 */
class Cli_Award_Notice extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('award_notice_model');
    }

    public function index()
    {
        $info = $this->award_notice_model->getOrderWin();

//         if(!empty($info['orderInfo']))
//         {
            $this->award_notice_model->saveOrderWin($info);
//         }
    }
}