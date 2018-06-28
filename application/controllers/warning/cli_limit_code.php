<?php
class Cli_Limit_Code extends MY_Controller
{
	public function index()
	{
		$this->load->model('limit_code_model');
		$this->limit_code_model->checkcode();
	}
}