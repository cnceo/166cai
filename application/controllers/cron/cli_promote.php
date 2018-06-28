<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cli_Promote extends MY_Controller
{
	public function index($date = null) {
		$date = empty($date) ? date('Y-m-d', strtotime("-1 day")) : $date;
		$this->load->model('order_model');
		$this->order_model->savePromote($date);
	}
}