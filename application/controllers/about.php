<?php
class About extends MY_Controller {
	
	public function index()
	{
		$this->display('about/about', array(), 'v1.1');
	}
	
	// public function safe()
	// {
	// 	$this->display('about/safe', array(), 'v1.1');
	// }
	
	public function contact()
	{
		$this->display('about/contact', array(), 'v1.1');
	}

	public function recruit()
	{
		$this->display('about/recruit', array(), 'v1.1');
	}
}