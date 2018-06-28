<?php
class Partner extends MY_Controller {

	public function index()
	{
		$this->display('partner/index', array(), 'v1.1');
	}

}